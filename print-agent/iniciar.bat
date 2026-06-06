@echo off
title Diego's Pizza - Agente de Impresion
cd /d "%~dp0"

echo ========================================
echo  Diego's Pizza - Agente de Impresion
echo ========================================
echo.

:: Matar procesos que ocupen el puerto 9200 (excepto System PID 4)
for /f "tokens=5" %%p in ('netstat -ano ^| findstr ":9200"') do (
    if not "%%p"=="0" if not "%%p"=="4" (
        taskkill /f /pid %%p 2>nul
    )
)
timeout /t 1 /nobreak >nul

:: Matar instancias anteriores del agente PowerShell
taskkill /f /fi "WINDOWTITLE eq Agente*" /t 2>nul
taskkill /f /fi "WINDOWTITLE eq *Impresion*" /t 2>nul

powershell -ExecutionPolicy Bypass -File "%~dp0agent.ps1"
pause
