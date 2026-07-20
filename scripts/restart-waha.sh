#!/bin/bash
# Reinicia la sesión de WAHA (WhatsApp HTTP API) al arrancar el servidor
# Se ejecuta via systemd o manualmente después de un reinicio

set -e

COMPOSE_DIR="/home/oliver/diegospizza"
API_KEY=$(grep -oP 'WAHA_API_KEY=\K.*' "$COMPOSE_DIR/.env" 2>/dev/null || echo "")
WAHA_URL="http://localhost:3000"
SESSION="default"
MAX_RETRIES=30
RETRY_INTERVAL=2

log() { echo "[$(date '+%H:%M:%S')] $1"; }

log "Esperando a que el contenedor waha esté activo..."
for i in $(seq 1 $MAX_RETRIES); do
    if docker inspect diegospizza-waha --format '{{.State.Status}}' 2>/dev/null | grep -q running; then
        log "Contenedor listo"
        break
    fi
    if [ "$i" -eq "$MAX_RETRIES" ]; then
        log "ERROR: El contenedor no inició después de ${MAX_RETRIES}s"
        exit 1
    fi
    sleep 1
done

log "Esperando a que la API de WAHA responda..."
for i in $(seq 1 $MAX_RETRIES); do
    HTTP_CODE=$(curl -s -o /dev/null -w '%{http_code}' -H "X-Api-Key: $API_KEY" "$WAHA_URL/api/sessions" 2>/dev/null || echo "000")
    if [ "$HTTP_CODE" != "000" ]; then
        log "API respondió (HTTP $HTTP_CODE)"
        break
    fi
    if [ "$i" -eq "$MAX_RETRIES" ]; then
        log "ERROR: API no respondió después de ${MAX_RETRIES}s"
        exit 1
    fi
    sleep 1
done

SESSION_STATUS=$(curl -s -H "X-Api-Key: $API_KEY" "$WAHA_URL/api/sessions/$SESSION" 2>/dev/null)
CURRENT_STATUS=$(echo "$SESSION_STATUS" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('status','UNKNOWN'))" 2>/dev/null || echo "UNKNOWN")

if [ "$CURRENT_STATUS" = "WORKING" ] || [ "$CURRENT_STATUS" = "CONNECTED" ]; then
    log "Sesión '$SESSION' ya está activa ($CURRENT_STATUS)"
    exit 0
fi

log "Estado actual: $CURRENT_STATUS. Iniciando sesión '$SESSION'..."

START_RESULT=$(curl -s -X POST -H "X-Api-Key: $API_KEY" "$WAHA_URL/api/sessions/$SESSION/start" 2>/dev/null)
if echo "$START_RESULT" | python3 -c "import sys,json; d=json.load(sys.stdin); sys.exit(0 if d.get('status') in ('WORKING','CONNECTED') else 1)" 2>/dev/null; then
    log "Sesión iniciada correctamente"
    exit 0
fi

log "No se pudo iniciar. Recreando sesión..."
curl -s -X DELETE -H "X-Api-Key: $API_KEY" "$WAHA_URL/api/sessions/$SESSION" > /dev/null 2>&1
sleep 2
CREATE_RESULT=$(curl -s -X POST -H "X-Api-Key: $API_KEY" -H "Content-Type: application/json" -d "{\"name\":\"$SESSION\"}" "$WAHA_URL/api/sessions" 2>/dev/null)
sleep 2
START_RESULT=$(curl -s -X POST -H "X-Api-Key: $API_KEY" "$WAHA_URL/api/sessions/$SESSION/start" 2>/dev/null)

if echo "$START_RESULT" | python3 -c "import sys,json; d=json.load(sys.stdin); sys.exit(0 if d.get('status') in ('WORKING','CONNECTED') else 1)" 2>/dev/null; then
    log "Sesión recreada e iniciada correctamente"
    log "IMPORTANTE: Escanea el QR en /admin/chat-bot para conectar WhatsApp"
else
    log "ERROR: No se pudo iniciar la sesión después de recrearla"
    log "Ve a /admin/chat-bot e inicia la sesión manualmente"
    exit 1
fi
