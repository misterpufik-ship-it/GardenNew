param(
    [switch]$List,
    [string]$Backup
)

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ConfigPath = Join-Path $ScriptDir "deploy.env.ps1"

if (!(Test-Path $ConfigPath)) {
    throw "Missing scripts/deploy.env.ps1. Create it from scripts/deploy.env.example.ps1 first."
}

. $ConfigPath

if (!$DeployHost -or !$DeployUser -or !$RemotePath) {
    throw "DeployHost, DeployUser, and RemotePath are required in scripts/deploy.env.ps1."
}

if (!$DeployPort) { $DeployPort = 22 }

$sshTarget = "$DeployUser@$DeployHost"

if ($List) {
    $remoteList = "BACKUP_BASE=`$(dirname '$RemotePath')/_deploy_backups; ls -1t `"`$BACKUP_BASE`"/public_html-*.tgz 2>/dev/null || true"
    & ssh -p $DeployPort $sshTarget $remoteList
    exit $LASTEXITCODE
}

$backupLine = ""
if ($Backup) {
    $backupLine = "BACKUP='$Backup'"
}
else {
    $backupLine = "BACKUP=`$(ls -1t `"`$BACKUP_BASE`"/public_html-*.tgz 2>/dev/null | head -n 1)"
}

$remoteScript = @"
set -e
REMOTE_PATH='$RemotePath'
BACKUP_BASE=`$(dirname "`$REMOTE_PATH")/_deploy_backups
$backupLine

if [ -z "`$BACKUP" ] || [ ! -f "`$BACKUP" ]; then
  echo "No backup found. Use scripts/rollback.ps1 -List to inspect available backups." >&2
  exit 1
fi

STAMP=`$(date +%Y%m%d-%H%M%S)
if [ -d "`$REMOTE_PATH" ]; then
  mv "`$REMOTE_PATH" "`$REMOTE_PATH.before-rollback-`$STAMP"
fi

tar -xzf "`$BACKUP" -C "`$(dirname "`$REMOTE_PATH")"
printf '%s rollback=%s path=%s\n' "`$(date -Is)" "`$BACKUP" "`$REMOTE_PATH" >> "`$BACKUP_BASE/deployments.log"
echo "Rolled back to `$BACKUP"
"@

& ssh -p $DeployPort $sshTarget $remoteScript
if ($LASTEXITCODE -ne 0) { throw "Rollback failed." }
