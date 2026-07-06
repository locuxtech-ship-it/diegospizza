param(
    [string]$Server = "oliver@100.122.80.102",
    [string]$BackupDir = "$env:TEMP\diegospizza_sync",
    [switch]$KeepFiles
)

# ============================================
# HungerClick — Sincronizar produccion a dev
# ============================================
# 1. Descarga el backup mas reciente del servidor
# 2. Ejecuta php artisan db:import-prod para importar a SQLite
# ============================================

$ErrorActionPreference = "Stop"
$ProjectDir = Split-Path -Parent $PSScriptRoot

Write-Host "======================================" -ForegroundColor Cyan
Write-Host " HungerClick - Sync desde Produccion" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Paso 1: Buscar ultimo backup en el servidor
Write-Host "[1/3] Buscando ultimo backup en el servidor..." -ForegroundColor Yellow
$latestBackup = ssh $Server "ls -t ~/backups/diegospizza/diegospizza_*.tar.gz 2>/dev/null | head -1"
if (-not $latestBackup) {
    Write-Host "[1/3] Buscando en Google Drive..." -ForegroundColor Yellow
    $latestBackup = ssh $Server "~/.local/bin/rclone ls gdrive:diegospizza-backups 2>/dev/null | sort | tail -1 | awk '{print \$2}'"
    if ($latestBackup) {
        $latestBackup = ssh $Server "~/.local/bin/rclone copy gdrive:diegospizza-backups/$latestBackup /tmp/diegospizza_sync/ 2>&1 && ls -t /tmp/diegospizza_sync/diegospizza_*.tar.gz 2>/dev/null | head -1"
    }
}
if (-not $latestBackup) {
    Write-Host "ERROR: No se encontro ningun backup" -ForegroundColor Red
    exit 1
}
Write-Host "  Backup encontrado: $latestBackup" -ForegroundColor Green

# Paso 2: Descargar el backup
Write-Host "`n[2/3] Descargando backup..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
$localFile = Join-Path $BackupDir "diegospizza_prod.tar.gz"
Write-Host "  Copiando desde servidor..."
scp "${Server}:$latestBackup" $localFile 2>&1 | Out-Null
if (-not (Test-Path $localFile)) {
    Write-Host "ERROR: No se pudo descargar el backup" -ForegroundColor Red
    exit 1
}
Write-Host "  Descargado: $localFile" -ForegroundColor Green

# Paso 3: Importar a SQLite
Write-Host "`n[3/3] Importando datos a SQLite..." -ForegroundColor Yellow
Push-Location $ProjectDir
try {
    php artisan db:import-prod "$localFile" 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host "ERROR: Fallo la importacion" -ForegroundColor Red
        exit 1
    }
}
finally {
    Pop-Location
}

# Limpiar
if (-not $KeepFiles) {
    Remove-Item $BackupDir -Recurse -Force -ErrorAction SilentlyContinue
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host " Sincronizacion completada!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Ahora puedes trabajar con datos reales de produccion."
Write-Host "Para volver a sincronizar, ejecuta:"
Write-Host "  .\scripts\sync-prod-data.ps1" -ForegroundColor Yellow
