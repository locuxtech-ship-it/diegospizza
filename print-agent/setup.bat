@echo off
title Diego's Pizza - Agente de Impresion

echo =============================================
echo  Diego's Pizza - Agente de Impresion
echo  Instalacion en Windows
echo =============================================
echo.

set "SCRIPT_DIR=%~dp0"
set "TASK_NAME=DiegosPizzaPrintAgent"

:: Remove existing task if any
schtasks /query /tn "%TASK_NAME%" >nul 2>&1
if %ERRORLEVEL% equ 0 (
    echo Eliminando tarea existente...
    schtasks /delete /tn "%TASK_NAME%" /f >nul
)

:: Create scheduled task (hidden)
echo Creando tarea programada que inicia el agente al iniciar sesion...
schtasks /create /tn "%TASK_NAME%" /tr "powershell.exe -WindowStyle Hidden -ExecutionPolicy Bypass -File \"%SCRIPT_DIR%print-agent.ps1\"" /sc onlogon /rl limited /f >nul

if %ERRORLEVEL% neq 0 (
    echo [ERROR] No se pudo crear la tarea programada.
    echo Intenta ejecutar como Administrador.
    pause
    exit /b 1
)

echo [OK] Tarea creada: %TASK_NAME%
echo.

:: Start now (hidden)
echo Iniciando agente...
powershell.exe -WindowStyle Hidden -ExecutionPolicy Bypass -File "%SCRIPT_DIR%print-agent.ps1"

echo [OK] Agente iniciado.
echo.
echo El agente imprimira automaticamente los pedidos nuevos.
echo Para verificar: revisa el archivo %USERPROFILE%\.diegospizza-print-id.txt
echo.
pause
