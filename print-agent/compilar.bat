@echo off
title Diego's Pizza - Compilar Agente (Opcional)
cd /d "%~dp0"

echo ========================================
echo  Compilar Agente C# (Opcional)
echo ========================================
echo.
echo  El agente PowerShell incluido (agent.ps1) funciona
echo  SIN compilacion en Windows 10/11.
echo.
echo  Esta compilacion es solo si prefieres el .exe.
echo.

if exist "%~dp0agente.exe" (
    echo [OK] agente.exe ya existe! No es necesario compilar.
    echo.
    pause
    exit /b
)

echo Compilando con PowerShell Add-Type...
powershell -ExecutionPolicy Bypass -Command "Add-Type -OutputAssembly '%~dp0agente.exe' -OutputType ConsoleApplication -ReferencedAssemblies 'System.Windows.Forms.dll','System.Drawing.dll' -Path '%~dp0agente.cs'; if ($?) { exit 0 } else { exit 1 }"

if %errorlevel% equ 0 (
    echo.
    echo [OK] Compilacion exitosa!
    echo   Archivo: agente.exe
) else (
    echo.
    echo [INFO] No se pudo compilar (es normal si falta el SDK).
    echo El agente PowerShell funciona igual de bien.
    echo Simplemente ejecuta "iniciar.bat".
)

pause
