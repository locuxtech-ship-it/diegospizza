#!/bin/bash
set -e

echo "========================================"
echo " Diego's Pizza - Server Setup"
echo "========================================"

cd /opt/diegospizza

echo ""
echo "[1/8] Creando .env..."
cp env.production .env

echo ""
echo "[2/8] Construyendo imágenes Docker..."
docker compose build

echo ""
echo "[3/8] Iniciando servicios..."
docker compose up -d db
sleep 10

echo ""
echo "[4/8] Iniciando app y nginx..."
docker compose up -d

echo ""
echo "[5/8] Verificando servicios..."
docker compose ps

echo ""
echo "[6/8] Ejecutando migraciones..."
docker compose exec -T app php artisan migrate --force

echo ""
echo "[7/8] Creando usuario admin..."
docker compose exec app php artisan make:filament-user --name=admin --email=locuxtech@gmail.com --password=admin123

echo ""
echo "========================================"
echo " SETUP COMPLETADO"
echo "========================================"
echo ""
echo " Admin URL: https://diegospizzabq.click/admin"
echo " Email:     locuxtech@gmail.com"
echo " Password:  admin123"
echo ""
echo "IMPORTANTE: Cambia la contraseña después del primer inicio de sesión"
echo "========================================"
