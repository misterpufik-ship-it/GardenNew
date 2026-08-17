param(
    [string]$TargetDir = "$env:USERPROFILE\Projects\GardenNew",
    [string]$RepoUrl = "https://github.com/misterpufik-ship-it/GardenNew.git"
)

$ErrorActionPreference = "Stop"

function Test-CommandExists {
    param([string]$Name)
    return [bool](Get-Command $Name -ErrorAction SilentlyContinue)
}

Write-Host "== Cursor local setup: GardenNew ==" -ForegroundColor Cyan

if (!(Test-CommandExists "git")) {
    throw "Git is not installed. Install from https://git-scm.com/download/win and rerun this script."
}

$parent = Split-Path -Parent $TargetDir
if (!(Test-Path $parent)) {
    New-Item -ItemType Directory -Path $parent | Out-Null
    Write-Host "Created folder: $parent"
}

if (Test-Path (Join-Path $TargetDir ".git")) {
    Write-Host "Repository already exists. Pulling latest changes..."
    Push-Location $TargetDir
    try {
        git pull --ff-only
    }
    finally {
        Pop-Location
    }
}
else {
    Write-Host "Cloning repository to $TargetDir ..."
    git clone $RepoUrl $TargetDir
}

if (Test-CommandExists "cursor") {
    Write-Host "Opening project in Cursor..."
    & cursor $TargetDir
}
else {
    Write-Host "Cursor CLI not found in PATH." -ForegroundColor Yellow
    Write-Host "Open Cursor manually: File -> Open Folder -> $TargetDir"
}

Write-Host ""
Write-Host "Done. Project path: $TargetDir" -ForegroundColor Green
Write-Host "If Cloud Agents are not connected yet, open:" -ForegroundColor Yellow
Write-Host "https://cursor.com/dashboard?tab=integrations"
