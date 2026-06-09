@echo off
title Diego's Pizza - Detener Agente

:: Find and kill the node process running agent.js
echo Buscando agente de impresion...
wmic process where "commandline like '%%agent.js%%' and name='node.exe'" get ProcessId 2>nul | findstr /r "[0-9]"
if %ERRORLEVEL% equ 0 (
    for /f "skip=1" %%i in ('wmic process where "commandline like '%%agent.js%%' and name='node.exe'" get ProcessId 2^>nul') do (
        if not "%%i"=="" (
            taskkill /f /pid %%i >nul 2>&1
            echo [OK] Agente detenido (PID: %%i)
        )
    )
) else (
    echo El agente no esta ejecutandose.
)
echo.
pause
