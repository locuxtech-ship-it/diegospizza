param($Url, $PrinterName = $null)

# Obtener nombre de impresora predeterminada
Add-Type -AssemblyName System.Drawing.Printing
if (-not $PrinterName) {
    $ps = New-Object System.Drawing.Printing.PrinterSettings
    $PrinterName = $ps.PrinterName
}
Write-Host "Impresora: $PrinterName"

# Obtener texto del ticket
try {
    Write-Host "Obteniendo ticket de: $Url"
    $text = Invoke-RestMethod -Uri $Url -UseBasicParsing -TimeoutSec 10
    Write-Host "Ticket obtenido ($($text.Length) caracteres)"
} catch {
    Write-Host "Error al obtener ticket: $_"
    return
}

# Construir comando ESC/POS
$esc = [char]27
$gs = [char]29
$bytes = New-Object System.Collections.Generic.List[byte]

# Inicializar impresora
$bytes.AddRange(@([byte]$esc, 0x40))
# Centrar
$bytes.AddRange(@([byte]$esc, 0x61, 0x01))
# Negritas on
$bytes.AddRange(@([byte]$esc, 0x45, 0x01))

# Texto del ticket (ISO-8859-1 para caracteres españoles)
$textBytes = [System.Text.Encoding]::GetEncoding(28591).GetBytes($text)
$bytes.AddRange($textBytes)

# Reset alineacion izquierda, negritas off, feeds, corte
$bytes.AddRange(@([byte]$esc, 0x61, 0x00))
$bytes.AddRange(@([byte]$esc, 0x45, 0x00))
$bytes.AddRange(@([byte]10, [byte]10))
$bytes.AddRange(@([byte]$gs, 0x56, 0x41))

$data = $bytes.ToArray()
Write-Host "Datos ESC/POS: $($data.Length) bytes"

# Método 1: Escribir directo al puerto de la impresora (mas confiable)
$ok = $false
$printerPath = "\\localhost\$PrinterName"
try {
    $stream = [System.IO.File]::Open($printerPath, [System.IO.FileMode]::Open, [System.IO.FileAccess]::Write, [System.IO.FileShare]::Read)
    $stream.Write($data, 0, $data.Length)
    $stream.Flush()
    $stream.Close()
    $ok = $true
    Write-Host "OK - Enviado via File.Write a $printerPath"
} catch {
    Write-Host "File.Write fallo: $_"
}

# Método 2: Write-Printer cmdlet
if (-not $ok) {
    try {
        Import-Module PrintManagement -ErrorAction Stop
        Write-Printer -Name $PrinterName -Data $data
        $ok = $true
        Write-Host "OK - Enviado via Write-Printer"
    } catch {
        Write-Host "Write-Printer fallo: $_"
    }
}

if (-not $ok) {
    Write-Host "ERROR: No se pudo enviar a la impresora '$PrinterName'"
}
