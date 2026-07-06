# 🚀 Guía de Deploy — HungerClick

## 📦 Archivos que ya tengo listos en tu PC

| Archivo | Descripción |
|---|---|
| `env.production` | .env listo para producción (dominio, db, app_key) |
| `Dockerfile` | Multi-stage: Node build + PHP 8.3 + Supervisor |
| `docker-compose.yml` | app + nginx + mariadb |
| `nginx/default.conf` | Servidor nginx optimizado |
| `docker-entrypoint.sh` | Inicia todo automáticamente |
| `supervisord.conf` | php-fpm + queue worker |
| `.dockerignore` | Excluye lo innecesario del build |
| `server-setup.sh` | Script de instalación para el servidor |

---

## 🔷 PARTE 1 — En tu PC (ahora)

### Paso 1: Subir el proyecto a GitHub

```powershell
# Abre PowerShell en D:\diegospizzaApp\diegospizza

# Inicializar git
git init
git add .
git commit -m "Produccion v1"

# Crear repo en github.com y conectar
git remote add origin https://github.com/TU_USUARIO/diegospizza.git
git push -u origin main
```

> Si no tienes GitHub, puedes usar `git bundle` o comprimir la carpeta y subirla por SCP.

---

## 🔷 PARTE 2 — En el Servidor Ubuntu

### Paso 1: Conectarte por SSH

```bash
# Desde tu PC (PowerShell o CMD):
ssh usuario@192.168.80.20
```

### Paso 2: Instalar Docker

```bash
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER
```

**Cierra sesión y vuelve a entrar:**
```bash
exit
# Espera
ssh usuario@192.168.80.20
```

Verifica:
```bash
docker --version
docker compose version
```

### Paso 3: Bajar el proyecto

```bash
# Opción A — Con git:
sudo mkdir -p /opt/diegospizza
sudo chown $USER:$USER /opt/diegospizza
cd /opt/diegospizza
git clone https://github.com/TU_USUARIO/diegospizza.git .

# Opción B — Sin git (sube el ZIP con SCP desde tu PC):
# En tu PC:
#   cd D:\diegospizzaApp
#   Compress-Archive -Path diegospizza -DestinationPath diegospizza.zip
#   scp diegospizza.zip usuario@192.168.80.20:/opt/
# En el servidor:
#   cd /opt
#   unzip diegospizza.zip
#   mv diegospizza/* diegospizza/.* . 2>/dev/null; rmdir diegospizza
```

### Paso 4: Preparar .env

```bash
cd /opt/diegospizza
cp env.production .env
```

### Paso 5: Construir e iniciar

```bash
# Build de las imágenes (tarda 2-5 min la primera vez)
docker compose build

# Iniciar base de datos primero
docker compose up -d db

# Esperar a que MariaDB esté lista (15 segundos)
sleep 15

# Iniciar todo
docker compose up -d

# Verificar que todo corre
docker compose ps
```

Debes ver:
```
NAME                 STATUS
diegospizza-app      Up (healthy)
diegospizza-db       Up (healthy)
diegospizza-nginx    Up
```

### Paso 6: Crear usuario admin

```bash
docker exec -it diegospizza-app php artisan make:filament-user
```

Responde:
```
Name: admin
Email: locuxtech@gmail.com
Password: admin123
```

### Paso 7: Probar localmente

```bash
curl -I http://localhost
# → Debe responder: HTTP/1.1 200 OK
```

Si ves `200 OK`, la aplicación funciona.

### Paso 8: Instalar Cloudflared

```bash
# Descargar
curl -L https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 -o /usr/local/bin/cloudflared
chmod +x /usr/local/bin/cloudflared

# Autenticar
cloudflared tunnel login
# → Se abre un enlace en la terminal
# → Cópialo, pégalo en tu navegador
# → Inicia sesión en Cloudflare
# → Elige tu dominio diegospizzabq.click
# → Vuelve al servidor

# Crear tunnel
cloudflared tunnel create diegospizza
# → Te muestra un ID, guárdalo
```

### Paso 9: Configurar el tunnel

```bash
# Obtén el ID del tunnel (cópialo del paso anterior)
# Crea el archivo de configuración:
cat > ~/.cloudflared/config.yml << 'EOF'
tunnel: COLOCA_AQUI_TU_TUNNEL_ID
credentials-file: /root/.cloudflared/COLOCA_AQUI_TU_TUNNEL_ID.json

ingress:
  - hostname: diegospizzabq.click
    service: http://localhost:80
  - hostname: www.diegospizzabq.click
    service: http://localhost:80
  - service: http_status:404
EOF
```

Reemplaza `COLOCA_AQUI_TU_TUNNEL_ID` con el ID que te mostró en el paso anterior.

### Paso 10: Crear DNS y probar

```bash
# Crear registros DNS
cloudflared tunnel route dns diegospizza diegospizzabq.click
cloudflared tunnel route dns diegospizza www.diegospizzabq.click

# Probar el tunnel (Ctrl+C para detener)
cloudflared tunnel run diegospizza
```

Si ves `Request accepted` o `Connection registered`, funciona.

### Paso 11: Instalar como servicio

```bash
# Para que corra siempre al iniciar el servidor
cloudflared service install
```

---

## ✅ Verificación final

Abre tu navegador y visita:
```
https://diegospizzabq.click/admin
```

Inicia sesión con:
- **Email:** locuxtech@gmail.com
- **Password:** admin123

---

## 📋 Checklist post-deploy

- [ ] Entrar a Configuración → activar métodos de pago
- [ ] Entrar a Fidelidad → configurar puntos y recompensas
- [ ] Crear algunos productos de prueba
- [ ] Hacer un pedido de prueba desde `/checkout`
- [ ] Verificar que llegue al PDV y suene la alarma
- [ ] Probar impresión de ticket

---

## 🔄 Actualizar código después del deploy

```bash
cd /opt/diegospizza
git pull
docker compose up -d --build
```
