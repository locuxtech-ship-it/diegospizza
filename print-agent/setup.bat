@echo off
chcp 65001 >nul
title Diego's Pizza - Instalacion Agente de Impresion

echo =============================================
echo  Diego's Pizza - Agente de Impresion
echo  Instalacion en Windows
echo =============================================
echo.

:: Check Node.js
where node >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Node.js no esta instalado.
    echo Descargalo de: https://nodejs.org/ (LTS recomendada)
    echo.
    pause
    exit /b 1
)

set "SCRIPT_DIR=%~dp0"
set "CONFIG_FILE=%USERPROFILE%\.diegospizza-print-agent.json"

:: Get server URL
set /p SERVER_URL="URL del servidor [http://192.168.80.20]: "
if "%SERVER_URL%"=="" set "SERVER_URL=http://192.168.80.20"

:: Get API key
set /p API_KEY="API Key (se la proporciona el administrador): "

:: Get printer name (optional)
echo.
echo Impresoras disponibles:
node "%SCRIPT_DIR%agent.js" --list-printers 2>nul || powershell -Command "Get-Printer | Format-Table Name,DriverName -AutoSize" 2>nul
echo.
set /p PRINTER="Nombre de la impresora (dejar vacio para usar la predeterminada): "

:: Save config
if exist "%CONFIG_FILE%" (
    for /f "usebackq delims=" %%a in ("%CONFIG_FILE%") do set OLD_CONFIG=%%a
) else (
    set OLD_CONFIG={}
)

echo {> "%CONFIG_FILE%"
echo   "server_url": "%SERVER_URL%",>> "%CONFIG_FILE%"
echo   "api_key": "%API_KEY%",>> "%CONFIG_FILE%"
echo   "printer": "%PRINTER%",>> "%CONFIG_FILE%"
echo   "last_printed_id": 0>> "%CONFIG_FILE%"
echo }>> "%CONFIG_FILE%"

echo [OK] Configuracion guardada.

:: Create scheduled task
set "TASK_NAME=DiegosPizzaPrintAgent"
schtasks /query /tn "%TASK_NAME%" >nul 2>&1
if %ERRORLEVEL% equ 0 (
    echo Eliminando tarea existente...
    schtasks /delete /tn "%TASK_NAME%" /f >nul
)

echo Creando tarea programada...
schtasks /create /tn "%TASK_NAME%" /tr "wscript.exe \"%SCRIPT_DIR%start.vbs\"" /sc onlogon /rl limited /f >nul

if %ERRORLEVEL% neq 0 (
    echo [ERROR] No se pudo crear la tarea programada.
    echo Intenta ejecutar como Administrador.
    pause
    exit /b 1
)

echo [OK] Tarea creada: %TASK_NAME%
echo.

:: Start now
echo Iniciando agente...
wscript.exe "%SCRIPT_DIR%start.vbs"

echo [OK] Agente iniciado.
echo.
echo El agente imprimira automaticamente los pedidos nuevos.
echo Para verificar: http://localhost:8192/ping
echo.
pause
