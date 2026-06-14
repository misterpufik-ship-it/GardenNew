# Copy this file to scripts/deploy.env.ps1 and fill in your hosting values.
# deploy.env.ps1 is ignored by Git and must never be committed.

$DeployHost = "example.com"
$DeployUser = "ssh_user"
$DeployPort = 22

# Absolute path to the live site folder on hosting.
# Common examples:
#   /home/username/garden-lounge.pro/public_html
#   /var/www/garden-lounge.pro/public_html
$RemotePath = "/home/username/site/public_html"

# Local folder to publish.
$LocalPath = "public_html"

# Number of server backups to keep.
$KeepBackups = 10

# Paths are matched against repository-relative paths with / separators.
$ExcludePaths = @(
    "public_html/admiralteyskaya/couch/config.php",
    "public_html/admiralteyskaya/couch/config copy*.php",
    "public_html/admiralteyskaya/couch/confi41g.php",
    "public_html/admiralteyskaya/couch/cache/*",
    "public_html/admiralteyskaya/couch/cache copy/*"
)
