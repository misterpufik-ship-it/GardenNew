$hash = [BitConverter]::ToString(
    [System.Security.Cryptography.MD5]::Create().ComputeHash(
        [Text.Encoding]::UTF8.GetBytes('loungegarden-menu-visual-2026')
    )
).Replace('-', '').Substring(0, 12).ToLower()
$token = "garden-visual-$hash"
$url = "https://garden-lounge.pro/admiralteyskaya/_maintenance/import-visual-taplink.php?token=$token"
Write-Host "Import URL ready"
curl.exe -sL $url
