@echo off
title Monitor Diego's Pizza
echo Abriendo monitor de impresion...
echo.
echo Si no se abre Edge, edita este .bat
echo y cambia la ruta de msedge.exe
echo.
start "" "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe" --kiosk-printing "https://diegospizzabq.click/page/print-monitor?key=diegospizza_print_2024"
exit
