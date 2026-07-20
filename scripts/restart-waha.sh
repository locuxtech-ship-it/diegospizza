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

call_api() {
    local method="$1" path="$2" body="$3"
    if [ -n "$body" ]; then
        curl -s -X "$method" -H "X-Api-Key: $API_KEY" -H "Content-Type: application/json" -d "$body" "$WAHA_URL$path"
    else
        curl -s -X "$method" -H "X-Api-Key: $API_KEY" "$WAHA_URL$path"
    fi
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

# Obtener estado de la sesion (si existe)
SESSION_DATA=$(call_api "GET" "/api/sessions/$SESSION" 2>/dev/null)
CURRENT_STATUS=$(echo "$SESSION_DATA" | grep -o '"status":"[^"]*"' | head -1 | cut -d'"' -f4 || echo "NOT_FOUND")
HTTP_CODE=$(echo "$SESSION_DATA" | head -1 | grep -c 'NotFound' >/dev/null 2>&1 && echo "404" || echo "200")

if [ "$CURRENT_STATUS" = "WORKING" ] || [ "$CURRENT_STATUS" = "CONNECTED" ]; then
    log "Sesion '$SESSION' ya esta activa ($CURRENT_STATUS)"
    exit 0
fi

# Si la sesion existe pero no esta activa, intentar iniciar
if [ "$CURRENT_STATUS" != "NOT_FOUND" ] && [ -n "$CURRENT_STATUS" ]; then
    log "Sesion existe (estado: $CURRENT_STATUS). Iniciando..."
    call_api "POST" "/api/sessions/$SESSION/start" '{}'
    sleep 5
    # Verificar si inicio
    STATUS=$(call_api "GET" "/api/sessions/$SESSION" 2>/dev/null | grep -o '"status":"[^"]*"' | head -1 | cut -d'"' -f4)
    if [ "$STATUS" = "WORKING" ] || [ "$STATUS" = "CONNECTED" ]; then
        log "Sesion '$SESSION' iniciada correctamente"
        exit 0
    fi
    log "No se pudo iniciar (estado: $STATUS). Reintentando con restart..."
    call_api "POST" "/api/sessions/$SESSION/restart" '{}'
    sleep 5
    STATUS=$(call_api "GET" "/api/sessions/$SESSION" 2>/dev/null | grep -o '"status":"[^"]*"' | head -1 | cut -d'"' -f4)
    if [ "$STATUS" = "WORKING" ] || [ "$STATUS" = "CONNECTED" ]; then
        log "Sesion '$SESSION' reiniciada correctamente"
        exit 0
    fi
    log "ERROR: No se pudo reiniciar la sesion (estado: $STATUS)"
    log "Ve a /admin/chat-bot e inicia la sesion manualmente"
    exit 1
fi

# La sesion no existe, crearla
log "Sesion no existe. Creando sesion '$SESSION'..."
CREATE_RESP=$(call_api "POST" "/api/sessions" "{\"name\":\"$SESSION\"}" 2>/dev/null)
HTTP_CODE=$(echo "$CREATE_RESP" | grep -c '"statusCode":40' >/dev/null 2>&1 && echo "400" || echo "200")

if [ "$HTTP_CODE" = "400" ]; then
    log "Error al crear sesion: $CREATE_RESP"
    log "Ve a /admin/chat-bot e inicia la sesion manualmente"
    exit 1
fi
log "Sesion creada"

sleep 2

# Iniciar la sesion creada
log "Iniciando sesion..."
call_api "POST" "/api/sessions/$SESSION/start" '{}'
sleep 5

STATUS=$(call_api "GET" "/api/sessions/$SESSION" 2>/dev/null | grep -o '"status":"[^"]*"' | head -1 | cut -d'"' -f4)
if [ "$STATUS" = "WORKING" ] || [ "$STATUS" = "CONNECTED" ]; then
    log "Sesion '$SESSION' creada e iniciada correctamente"
    exit 0
fi

log "ERROR: No se pudo iniciar la sesion (estado: $STATUS)"
log "Ve a /admin/chat-bot e inicia la sesion manualmente"
exit 1
