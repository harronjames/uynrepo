#!/usr/bin/env bash
set -euo pipefail

SERVER_USER="root"
SERVER_HOST="212.132.65.191"

IMAGE_NAME="umzugland-client"
IMAGE_TAG="latest"
COMPOSE_SERVICE_APP="app"
COMPOSE_SERVICE_SCHEDULER="scheduler"
REMOTE_DIR="/root/umzugland"
REMOTE_COMPOSE="${REMOTE_DIR}/docker-compose.yml"
REMOTE_ENV="${REMOTE_DIR}/.env"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

LOCAL_ENV="${SCRIPT_DIR}/deploy.env"
LOCAL_COMPOSE="${SCRIPT_DIR}/docker-compose.server.yml"

if [[ -z "$SERVER_HOST" ]]; then
  echo "Bitte SERVER_HOST in deploy.sh setzen." >&2
  exit 1
fi

if [[ ! -f "$LOCAL_ENV" ]]; then
  echo "Fehlt: deploy.env (deploy.env.example kopyala ve doldur)" >&2
  exit 1
fi

if [[ ! -f "$LOCAL_COMPOSE" ]]; then
  echo "Fehlt: docker-compose.server.yml" >&2
  exit 1
fi

FULL_IMAGE="${IMAGE_NAME}:${IMAGE_TAG}"
SSH_TARGET="${SERVER_USER}@${SERVER_HOST}"
# Windows/Git Bash: kein SSH-Multiplexing (mux_client_request_session / Connection reset)
SSH_OPTS=(-o ServerAliveInterval=30)

echo "==> Docker Build: ${FULL_IMAGE}"
docker build -t "${FULL_IMAGE}" .

echo "==> Dateien hochladen (SSH-Passwort kann mehrfach kommen — Windows/OpenSSH)"
ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" "mkdir -p ${REMOTE_DIR}"
scp "${SSH_OPTS[@]}" "$LOCAL_COMPOSE" "${SSH_TARGET}:${REMOTE_COMPOSE}"
scp "${SSH_OPTS[@]}" "$LOCAL_ENV" "${SSH_TARGET}:${REMOTE_ENV}"
ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" "chmod 600 ${REMOTE_ENV}"

echo "==> Image upload (dauert je nach Leitung)"
docker save "${FULL_IMAGE}" | gzip -1 | ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" "gunzip | docker load"

echo "==> Container starten (alter Name / Waisen werden bereinigt)"
ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" bash -s <<EOF
set -euo pipefail
docker rm -f umzugland umzugland-scheduler umzugland-db 2>/dev/null || true
docker compose -f ${REMOTE_COMPOSE} up -d --remove-orphans db
docker compose -f ${REMOTE_COMPOSE} up -d --force-recreate --no-deps --remove-orphans ${COMPOSE_SERVICE_APP} ${COMPOSE_SERVICE_SCHEDULER}
rm -f ${REMOTE_ENV}
docker ps --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}' | grep -E 'NAMES|umzugland' || true
EOF

echo "==> Fertig. Site: Port 8000 (nginx/host proxy). Secrets: ${REMOTE_ENV} gelöscht."
echo "==> Optional auf Server: docker image prune -f"
