@echo off
echo Deteniendo agente de impresion...
powershell -Command "Get-Process | Where-Object { $_.ProcessName -eq 'powershell' -and $_.CommandLine -like '*print-agent*' } | Stop-Process -Force"
echo [OK] Agente detenido.
pause
