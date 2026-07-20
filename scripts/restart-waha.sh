#!/bin/bash
# Reinicia la sesión de WAHA (WhatsApp HTTP API) al arrancar el servidor
# Se ejecuta via systemd o manualmente después de un reinicio

set -e

COMPOSE_DIR="/home/oliver/diegospizza"
API_KEY=$(grep -oP 'WAHA_API_KEY=\K.*' "$COMPOSE_DIR/.env" 2>/dev/null || echo "")
WAHA_URL="http://localhost:3000"
SESSION="default"
MAX_RETRIES=30

log() { echo "[$(date '+%H:%M:%S')] $1"; }

waha_get() {
    curl -s -H "X-Api-Key: $API_KEY" "$WAHA_URL$1"
}

waha_post() {
    curl -s -X POST -H "X-Api-Key: $API_KEY" -H "Content-Type: application/json" -d "$2" "$WAHA_URL$1"
}

waha_delete() {
    curl -s -X DELETE -H "X-Api-Key: $API_KEY" "$WAHA_URL$1"
}

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

SESSION_DATA=$(waha_get "/api/sessions/$SESSION" 2>/dev/null)
CURRENT_STATUS=$(echo "$SESSION_DATA" | python3 -c "
import sys, json
try:
    d = json.load(sys.stdin)
    print(d.get('status', 'UNKNOWN'))
except:
    print('UNKNOWN')
" 2>/dev/null || echo "NOT_FOUND")

if [ "$CURRENT_STATUS" = "WORKING" ] || [ "$CURRENT_STATUS" = "CONNECTED" ]; then
    log "Sesion '$SESSION' ya esta activa ($CURRENT_STATUS)"
    exit 0
fi

log "Estado: $CURRENT_STATUS. Recreando sesion '$SESSION'..."

waha_delete "/api/sessions/$SESSION" > /dev/null 2>&1
sleep 2

CREATE_RESP=$(python3 -c "
import urllib.request, json
data = json.dumps({'name': '$SESSION'}).encode('utf-8')
req = urllib.request.Request(
    '$WAHA_URL/api/sessions',
    data=data,
    headers={
        'X-Api-Key': '$API_KEY',
        'Content-Type': 'application/json'
    },
    method='POST'
)
try:
    resp = urllib.request.urlopen(req)
    print(resp.read().decode())
except urllib.error.HTTPError as e:
    print(f'HTTP_ERROR:{e.code}:{e.read().decode()}')
except Exception as e:
    print(f'ERROR:{e}')
" 2>&1)

if echo "$CREATE_RESP" | grep -q 'HTTP_ERROR\|ERROR'; then
    log "Error al crear sesion: $CREATE_RESP"
    log "Intenta iniciar sesion manualmente desde /admin/chat-bot"
    exit 1
fi
log "Sesion creada"

sleep 2

START_RESP=$(python3 -c "
import urllib.request, json
data = json.dumps({}).encode('utf-8')
req = urllib.request.Request(
    '$WAHA_URL/api/sessions/$SESSION/start',
    data=data,
    headers={
        'X-Api-Key': '$API_KEY',
        'Content-Type': 'application/json'
    },
    method='POST'
)
try:
    resp = urllib.request.urlopen(req)
    print(resp.read().decode())
except urllib.error.HTTPError as e:
    body = e.read().decode()
    print(f'HTTP_ERROR:{e.code}:{body}')
    if 'session not found' in body.lower() or 'not found' in body.lower():
        # reintentar crear + iniciar
        import time
        req2 = urllib.request.Request(
            '$WAHA_URL/api/sessions',
            data=json.dumps({'name': '$SESSION'}).encode('utf-8'),
            headers={
                'X-Api-Key': '$API_KEY',
                'Content-Type': 'application/json'
            },
            method='POST'
        )
        urllib.request.urlopen(req2)
        time.sleep(2)
        req3 = urllib.request.Request(
            '$WAHA_URL/api/sessions/$SESSION/start',
            data=json.dumps({}).encode('utf-8'),
            headers={
                'X-Api-Key': '$API_KEY',
                'Content-Type': 'application/json'
            },
            method='POST'
        )
        resp = urllib.request.urlopen(req3)
        print(resp.read().decode())
except Exception as e:
    print(f'ERROR:{e}')
" 2>&1)

if echo "$START_RESP" | grep -q 'HTTP_ERROR\|ERROR'; then
    log "Error al iniciar sesion: $START_RESP"
    log "Ve a /admin/chat-bot e inicia la sesion manualmente"
    exit 1
fi

log "Sesion '$SESSION' iniciada correctamente"
log "IMPORTANTE: Escanea el QR en /admin/chat-bot para conectar WhatsApp"
