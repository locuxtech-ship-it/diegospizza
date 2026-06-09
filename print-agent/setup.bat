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
set "TASK_NAME=DiegosPizzaPrintAgent"

:: Remove existing task if any
schtasks /query /tn "%TASK_NAME%" >nul 2>&1
if %ERRORLEVEL% equ 0 (
    echo Eliminando tarea existente...
    schtasks /delete /tn "%TASK_NAME%" /f >nul
)

:: Create scheduled task (hidden via VBS wrapper)
echo Creando tarea programada...
schtasks /create /tn "%TASK_NAME%" /tr "wscript.exe \"%SCRIPT_DIR%start.vbs\"" /sc onlogon /rl limited /f >nul

if %ERRORLEVEL% neq 0 (
    echo [ERROR] No se pudo crear la tarea programada.
    echo Intenta ejecutar como Administrador (clic derecho ^> Ejecutar como admnistrador).
    pause
    exit /b 1
)

echo [OK] Tarea creada: %TASK_NAME%
echo.

:: Start now (hidden)
echo Iniciando agente...
wscript.exe "%SCRIPT_DIR%start.vbs"

echo [OK] Agente iniciado en http://localhost:8192
echo.
echo El agente se iniciara automaticamente al iniciar sesion en Windows.
echo - Para verificar: abrir http://localhost:8192/ping en el navegador
echo - Para detener: schtasks /end /tn "%TASK_NAME%"
echo - Para desinstalar: schtasks /delete /tn "%TASK_NAME%" /f
echo.
pause
