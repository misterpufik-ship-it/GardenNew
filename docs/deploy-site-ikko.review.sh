#!/usr/bin/env bash
set -euo pipefail

PROJECT="${1:-}"
if [[ -z "$PROJECT" ]]; then
  echo "Usage: deploy-site <project>"
  exit 1
fi

CONFIG="/srv/deploy/projects/$PROJECT/deploy.env"
if [[ ! -f "$CONFIG" ]]; then
  echo "Config not found: $CONFIG"
  exit 1
fi

set -a
source "$CONFIG"
set +a

: "${REPO_URL:?REPO_URL is required}"
: "${HOSTING_USER:?HOSTING_USER is required}"
: "${HOSTING_HOST:?HOSTING_HOST is required}"
: "${HOSTING_PATH:?HOSTING_PATH is required}"

BRANCH="${BRANCH:-main}"
KEEP_BACKUPS="${KEEP_BACKUPS:-5}"
BACKUP_EXCLUDES="${BACKUP_EXCLUDES:-}"
PROJECT_DIR="/srv/deploy/projects/$PROJECT"
REPO_DIR="$PROJECT_DIR/repo"
BACKUP_PATH="${BACKUP_PATH:-~/deploy_backups/$PROJECT}"
SOURCE_DIR="${SOURCE_DIR:-.}"
TIMESTAMP="$(date +%Y-%m-%d_%H-%M-%S)"

DEFAULT_EXCLUDES=(
  "--exclude=.git/"
  "--exclude=.env"
  "--exclude=config.php"
  "--exclude=uploads/"
  "--exclude=storage/"
  "--exclude=cache/"
  "--exclude=tmp/"
  "--exclude=node_modules/"
)

# Match the root entry, including a symlink or file named ikko.
# A normal exclude both hides it from transfer and protects it from --delete.
if [[ "$PROJECT" == "garden-lounge.pro" ]]; then
  DEFAULT_EXCLUDES+=("--exclude=/ikko")
fi

REMOTE="$HOSTING_USER@$HOSTING_HOST"
export RSYNC_RSH="ssh -o BatchMode=yes -o StrictHostKeyChecking=yes"
PUBLISH_ARGS=(-az "${DEFAULT_EXCLUDES[@]}")
if [[ "${DELETE_REMOTE:-false}" == "true" ]]; then
  PUBLISH_ARGS+=(--delete)
fi
PUBLISH_PATHS=("$REPO_DIR/$SOURCE_DIR/" "$REMOTE:$HOSTING_PATH/")
if [[ "${2:-}" == "--dry-run" ]]; then
  # Preview the current checkout without updating Git, building or backing up.
  rsync "${PUBLISH_ARGS[@]}" -ni "${PUBLISH_PATHS[@]}"
  exit
fi

if [[ ! -d "$REPO_DIR/.git" ]]; then
  git clone --branch "$BRANCH" "$REPO_URL" "$REPO_DIR"
else
  git -C "$REPO_DIR" fetch origin "$BRANCH"
  git -C "$REPO_DIR" reset --hard "origin/$BRANCH"
fi

if [[ -n "${BUILD_COMMAND:-}" ]]; then
  (cd "$REPO_DIR" && bash -lc "$BUILD_COMMAND")
fi

export GIT_TERMINAL_PROMPT=0
ssh -o BatchMode=yes "$REMOTE" "test -d '$HOSTING_PATH'"
ssh "$REMOTE" "mkdir -p '$BACKUP_PATH/$TIMESTAMP'"
ssh "$REMOTE" \
  "HOSTING_PATH='$HOSTING_PATH' BACKUP_PATH='$BACKUP_PATH' TIMESTAMP='$TIMESTAMP' BACKUP_EXCLUDES='$BACKUP_EXCLUDES' bash -s" <<'REMOTE_BACKUP'
set -euo pipefail
target="$BACKUP_PATH/$TIMESTAMP"
if [[ ! -d "$HOSTING_PATH" ]]; then
  exit 0
fi
if [[ -n "$BACKUP_EXCLUDES" ]]; then
  mapfile -d, -t excludes <<< "$BACKUP_EXCLUDES,"
  args=(-a)
  for item in "${excludes[@]}"; do
    item="${item//[$'\t\r\n ']}"
    [[ -n "$item" ]] && args+=(--exclude="$item")
  done
  rsync "${args[@]}" "$HOSTING_PATH/" "$target/"
else
  cp -a "$HOSTING_PATH/." "$target/"
fi
REMOTE_BACKUP

rsync "${PUBLISH_ARGS[@]}" "${PUBLISH_PATHS[@]}"

ssh "$REMOTE" "ls -1dt '$BACKUP_PATH'/*/ 2>/dev/null | tail -n +$((KEEP_BACKUPS + 1)) | xargs -r rm -rf"

echo "Deploy finished: $PROJECT"
echo "Backup created: $BACKUP_PATH/$TIMESTAMP"
echo "Backups kept: $KEEP_BACKUPS"

