@echo off
echo Iniciando agente (ventana visible para depuracion)...
echo Cerrar esta ventana detiene el agente.
echo.
powershell.exe -ExecutionPolicy Bypass -File "%~dp0print-agent.ps1"
pause
