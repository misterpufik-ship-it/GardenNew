param(
    [switch]$AllowDirty,
    [switch]$SkipPush,
    [switch]$DryRun
)

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$RepoRoot = Resolve-Path (Join-Path $ScriptDir "..")
$ConfigPath = Join-Path $ScriptDir "deploy.env.ps1"
$ExamplePath = Join-Path $ScriptDir "deploy.env.example.ps1"

if (!(Test-Path $ConfigPath)) {
    Copy-Item $ExamplePath $ConfigPath
    throw "Created scripts/deploy.env.ps1. Fill in SSH hosting values, then run deploy again."
}

. $ConfigPath

if (!$DeployHost -or !$DeployUser -or !$RemotePath) {
    throw "DeployHost, DeployUser, and RemotePath are required in scripts/deploy.env.ps1."
}

if (!$DeployPort) { $DeployPort = 22 }
if (!$LocalPath) { $LocalPath = "public_html" }
if (!$KeepBackups) { $KeepBackups = 10 }
if (!$ExcludePaths) { $ExcludePaths = @() }

function Invoke-RepoGit {
    param([Parameter(ValueFromRemainingArguments = $true)][string[]]$GitArgs)
    Push-Location $RepoRoot
    try {
        & git @GitArgs
        if ($LASTEXITCODE -ne 0) { throw "git $($GitArgs -join ' ') failed." }
    }
    finally {
        Pop-Location
    }
}

function Test-ExcludedPath {
    param([string]$Path)
    $normalized = $Path.Replace("\", "/")
    foreach ($pattern in $ExcludePaths) {
        if ($normalized -like $pattern) { return $true }
    }
    return $false
}

Push-Location $RepoRoot
try {
    & git rev-parse --is-inside-work-tree | Out-Null
    if ($LASTEXITCODE -ne 0) { throw "This folder is not a Git repository." }

    if (!$AllowDirty) {
        $status = & git status --porcelain
        if ($status) {
            throw "Working tree has uncommitted changes. Commit/stash them first, or rerun with -AllowDirty."
        }
    }

    $commit = (& git rev-parse --short HEAD).Trim()
    $branch = (& git branch --show-current).Trim()

    if (!$SkipPush) {
        Invoke-RepoGit fetch --quiet
        $upstream = (& git rev-parse --abbrev-ref --symbolic-full-name "@{u}" 2>$null)
        if ($LASTEXITCODE -ne 0 -or !$upstream) {
            throw "Current branch has no upstream. Push it once with: git push -u origin $branch"
        }

        Invoke-RepoGit push
    }

    $stamp = Get-Date -Format "yyyyMMdd-HHmmss"
    $workDir = Join-Path ([System.IO.Path]::GetTempPath()) "garden-deploy-$stamp"
    $packageDir = Join-Path $workDir "package"
    $zipPath = Join-Path $workDir "garden-$commit.zip"

    New-Item -ItemType Directory -Force -Path $packageDir | Out-Null

    $trackedFiles = & git ls-files -- $LocalPath
    foreach ($file in $trackedFiles) {
        if (Test-ExcludedPath $file) { continue }

        $relativeInsideLocal = $file.Substring($LocalPath.Length).TrimStart("/", "\")
        if (!$relativeInsideLocal) { continue }

        $source = Join-Path $RepoRoot $file
        $target = Join-Path $packageDir $relativeInsideLocal
        $targetDir = Split-Path -Parent $target
        New-Item -ItemType Directory -Force -Path $targetDir | Out-Null
        Copy-Item -LiteralPath $source -Destination $target -Force
    }

    Compress-Archive -Path (Join-Path $packageDir "*") -DestinationPath $zipPath -Force

    $remoteZip = "/tmp/garden-$commit-$stamp.zip"
    $sshTarget = "$DeployUser@$DeployHost"

    if ($DryRun) {
        Write-Host "Dry run OK."
        Write-Host "Branch: $branch"
        Write-Host "Commit: $commit"
        Write-Host "Package: $zipPath"
        Write-Host "Target: ${sshTarget}:$RemotePath"
        exit 0
    }

    & scp -P $DeployPort $zipPath "${sshTarget}:$remoteZip"
    if ($LASTEXITCODE -ne 0) { throw "Upload over SSH failed." }

    $remoteScript = @"
set -e
REMOTE_PATH='$RemotePath'
REMOTE_ZIP='$remoteZip'
KEEP_BACKUPS='$KeepBackups'
COMMIT='$commit'
BACKUP_BASE=`$(dirname "`$REMOTE_PATH")/_deploy_backups
STAMP=`$(date +%Y%m%d-%H%M%S)

mkdir -p "`$BACKUP_BASE"

if [ -d "`$REMOTE_PATH" ]; then
  tar -czf "`$BACKUP_BASE/public_html-`$STAMP.tgz" -C "`$(dirname "`$REMOTE_PATH")" "`$(basename "`$REMOTE_PATH")"
fi

mkdir -p "`$REMOTE_PATH"
unzip -oq "`$REMOTE_ZIP" -d "`$REMOTE_PATH"
rm -f "`$REMOTE_ZIP"

printf '%s commit=%s path=%s\n' "`$(date -Is)" "`$COMMIT" "`$REMOTE_PATH" >> "`$BACKUP_BASE/deployments.log"
ls -1t "`$BACKUP_BASE"/public_html-*.tgz 2>/dev/null | tail -n +`$((KEEP_BACKUPS + 1)) | xargs -r rm -f
"@

    & ssh -p $DeployPort $sshTarget $remoteScript
    if ($LASTEXITCODE -ne 0) { throw "Remote deploy failed. A backup may have been created on the server." }

    Write-Host "Deploy complete: $commit -> ${sshTarget}:$RemotePath"
}
finally {
    Pop-Location
}
