@echo off
title Diego's Pizza - Instalacion del Agente
echo ========================================
echo  Diego's Pizza - Instalacion del Agente
echo ========================================
echo.
echo El agente NO requiere instalacion de software externo.
echo Solo necesitas Windows 10/11 y una impresora termica instalada.
echo.
echo === PASO 1: Configurar impresora predeterminada ===
echo   Ve a Configuracion ^> Bluetooth y dispositivos ^> Impresoras
echo   Selecciona tu POS58 y haz clic en "Establecer como predeterminada"
echo.
echo === PASO 2: Iniciar el agente ===
echo   Ejecuta "iniciar.bat" (doble clic)
echo   Manten la ventana abierta mientras trabajes
echo.
echo === COMO FUNCIONA ===
echo   El agente usa WebBrowser.Print() de .NET Framework
echo   - Imprime SIN dialogo, SIN popups, SIN ventanas
echo   - Cuando llega un pedido, el ticket sale directo a la POS58
echo.
echo === ACTIVAR EN EL ADMIN ===
echo   Admin ^> Configuracion: activa "Imprimir comandas automaticamente"
echo.
echo === SOLUCION DE PROBLEMAS ===
echo   - "No imprime" ^> Verifica que la POS58 sea la impresora predeterminada
echo   - "Puerto ocupado" ^> Cierra otros programas en el puerto 9199
echo   - "Error de ejecucion" ^> Intenta ejecutar como Administrador
echo.
pause
