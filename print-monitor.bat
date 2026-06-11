@echo off
title Monitor Diego's Pizza
echo.
echo ============================================
echo  Monitor de Impresion - Diego's Pizza
echo ============================================
echo.
echo Cerrando Edge si esta abierto...
taskkill /f /im msedge.exe >nul 2>&1
timeout /t 1 /nobreak >nul
echo.
echo Abriendo monitor en modo kiosk...
echo.
start "" msedge.exe --user-data-dir="%TEMP%\edge-monitor" --no-first-run --kiosk --kiosk-printing "https://diegospizzabq.click/page/print-monitor?key=diegospizza_print_2024"
exit
