param(
    [switch]$SkipPush,
    [switch]$DryRun,
    [switch]$SyncMenuVisual,
    [switch]$AllowDirty,
    [string]$Project = "garden-lounge.pro",
    [string]$VpsHost = "159.194.232.89",
    [string]$VpsUser = "root",
    [string]$IdentityFile = "$env:USERPROFILE\.ssh\codex_beget_vps_ed25519"
)

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$RepoRoot = Resolve-Path (Join-Path $ScriptDir "..")

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

Push-Location $RepoRoot
try {
    & git rev-parse --is-inside-work-tree | Out-Null
    if ($LASTEXITCODE -ne 0) { throw "This folder is not a Git repository." }

    if (!$AllowDirty -and !$DryRun) {
        $status = & git status --porcelain
        if ($status) {
            throw "Working tree has uncommitted changes. Commit them first, or rerun with -AllowDirty for checks only."
        }
    }

    if (!(Test-Path $IdentityFile)) {
        throw "Missing SSH key: $IdentityFile"
    }

    $target = "$VpsUser@$VpsHost"
    if ($Project -notmatch '^[a-zA-Z0-9][a-zA-Z0-9.-]*$') {
        throw "Invalid VPS project name."
    }
    if ($DryRun -or $Project -eq "garden-lounge.pro") {
        # Refuse older server scripts, whose dry-run mutates the checkout.
        $guard = "grep -Fq -- '--exclude=/ikko' /srv/deploy/bin/deploy-site && grep -Fq -- 'Preview the current checkout without updating Git' /srv/deploy/bin/deploy-site"
        & ssh -i $IdentityFile -o BatchMode=yes -o ConnectTimeout=15 $target $guard
        if ($LASTEXITCODE -ne 0) { throw "Install and verify the VPS deploy safety patch first." }
    }

    if (!$SkipPush -and !$DryRun) {
        Invoke-RepoGit fetch --quiet
        Invoke-RepoGit push
    }

    $preview = if ($DryRun) { " --dry-run" } else { "" }
    $remoteCommand = "sudo -u deploy /srv/deploy/bin/deploy-site $Project$preview"

    & ssh -i $IdentityFile -o BatchMode=yes -o ConnectTimeout=15 $target $remoteCommand
    if ($LASTEXITCODE -ne 0) { throw "Remote VPS deploy failed." }

    # The main deploy leaves CMS uploads alone. Opt in to adding menu assets.
    if ($SyncMenuVisual -and $Project -eq "garden-lounge.pro") {
        $menuFlags = if ($DryRun) { " -ni" } else { "" }
        $menuVisualSync = @(
            "sudo -u deploy rsync -az --ignore-existing --omit-dir-times$menuFlags",
            "/srv/deploy/projects/garden-lounge.pro/repo/public_html/admiralteyskaya/couch/uploads/image/menu-visual/",
            "mrpuffch@mrpuffch.beget.tech:/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/uploads/image/menu-visual/"
        ) -join " "

        & ssh -i $IdentityFile -o BatchMode=yes -o ConnectTimeout=15 $target $menuVisualSync
        if ($LASTEXITCODE -ne 0) { throw "Visual menu image sync failed." }
    }
}
finally {
    Pop-Location
}
