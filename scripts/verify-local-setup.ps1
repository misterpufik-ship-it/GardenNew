$ErrorActionPreference = "Stop"

$RepoRoot = Split-Path -Parent $PSScriptRoot
$KeyPath = Join-Path $env:USERPROFILE ".ssh\codex_beget_vps_ed25519"
$VpsHost = "82.202.129.7"
$VpsUser = "root"

Write-Host "== GardenNew local setup check ==" -ForegroundColor Cyan

function Pass([string]$Message) { Write-Host "[OK] $Message" -ForegroundColor Green }
function Warn([string]$Message) { Write-Host "[!!] $Message" -ForegroundColor Yellow }
function Fail([string]$Message) { Write-Host "[XX] $Message" -ForegroundColor Red }

if (Test-Path (Join-Path $RepoRoot ".git")) {
    Pass "Git repository found at $RepoRoot"
}
else {
    Fail "This folder is not a git repository: $RepoRoot"
}

if (Get-Command git -ErrorAction SilentlyContinue) {
    Push-Location $RepoRoot
    try {
        $branch = git branch --show-current
        $remote = git remote get-url origin
        Pass "Git branch: $branch"
        Pass "Git remote: $remote"
    }
    finally {
        Pop-Location
    }
}
else {
    Fail "Git is not installed"
}

if (Get-Command cursor -ErrorAction SilentlyContinue) {
    Pass "Cursor CLI is available"
}
else {
    Warn "Cursor CLI not in PATH. Open project via File -> Open Folder"
}

if (Test-Path $KeyPath) {
    Pass "VPS SSH key found: $KeyPath"
}
else {
    Fail "Missing VPS SSH key: $KeyPath"
}

Write-Host ""
Write-Host "Testing VPS SSH ($VpsUser@$VpsHost)..." -ForegroundColor Cyan
if (Test-Path $KeyPath) {
    $sshTest = & ssh -i $KeyPath -o BatchMode=yes -o ConnectTimeout=10 -o StrictHostKeyChecking=accept-new "$VpsUser@$VpsHost" "echo connected && hostname && pwd" 2>&1
    if ($LASTEXITCODE -eq 0) {
        Pass "VPS SSH connection works"
        $sshTest | ForEach-Object { Write-Host "     $_" }
    }
    else {
        Fail "VPS SSH failed"
        $sshTest | ForEach-Object { Write-Host "     $_" }
    }
}

Write-Host ""
Write-Host "Done." -ForegroundColor Cyan
