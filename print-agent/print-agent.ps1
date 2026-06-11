$ApiKey = "diegospizza_print_2024"
$ServerUrl = "https://diegospizzabq.click"
$ConfigFile = "$env:USERPROFILE\.diegospizza-print-id.txt"
$PrinterConfig = "$PSScriptRoot\printer.conf"

# ── Choose printer ──────────────────────────────────────────────────
$PrinterName = ""
if (Test-Path $PrinterConfig) {
    $PrinterName = (Get-Content $PrinterConfig).Trim()
}

if (-not $PrinterName) {
    # Auto-detect default printer
    $default = Get-WmiObject -Class Win32_Printer | Where-Object { $_.Default -eq $true }
    $allPrinters = @(Get-Printer | ForEach-Object { $_.Name })
    $listPrinters = @(Get-WmiObject -Class Win32_Printer | ForEach-Object { $_.Name })

    Write-Host "================================="
    Write-Host "CONFIGURACION INICIAL"
    Write-Host "================================="
    Write-Host "Impresoras detectadas:"
    $listPrinters | ForEach-Object { Write-Host "  - $_" }
    Write-Host ""

    if ($default) {
        $PrinterName = $default.Name
        Write-Host "Usando impresora predeterminada: $PrinterName"
    } elseif ($listPrinters.Count -gt 0) {
        $PrinterName = $listPrinters[0]
        Write-Host "Usando primera impresora disponible: $PrinterName"
    } else {
        Write-Host "[ERROR] No se detectaron impresoras."
        Write-Host "Crea el archivo printer.conf con el nombre de tu impresora."
        pause
        exit 1
    }

    # Save config
    [System.IO.File]::WriteAllText($PrinterConfig, $PrinterName)
    Write-Host "Configuracion guardada en: $PrinterConfig"
    Write-Host "Edita ese archivo si necesitas cambiar de impresora."
    Write-Host ""
}

# ── Start agent ────────────────────────────────────────────────────
$lastId = 0
if (Test-Path $ConfigFile) { $lastId = [int](Get-Content $ConfigFile) }

Write-Host "================================="
Write-Host "Diego's Pizza - Agente de Impresion"
Write-Host "================================="
Write-Host "Servidor: $ServerUrl"
Write-Host "Impresora: $PrinterName"
Write-Host "Ultimo ID impreso: $lastId"
Write-Host ""

function Print-Ticket($text, $orderNum) {
    $tmp = [System.IO.Path]::GetTempFileName() + ".txt"
    [System.IO.File]::WriteAllText($tmp, $text, [System.Text.Encoding]::UTF8)

    try {
        Write-Printer -Name $PrinterName -Path $tmp -ErrorAction Stop
        Write-Host "OK - Pedido #$orderNum impreso"
        Remove-Item $tmp -ErrorAction SilentlyContinue
        return
    } catch { }

    try {
        cmd /c "copy /b `"$tmp`" `"\\localhost\$PrinterName`"" > $null 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "OK - Pedido #$orderNum impreso"
            Remove-Item $tmp -ErrorAction SilentlyContinue
            return
        }
    } catch { }

    try {
        Get-Content $tmp | Out-Printer -Name $PrinterName -ErrorAction Stop
        Write-Host "OK - Pedido #$orderNum impreso"
    } catch {
        Write-Host "[ERROR] No se pudo imprimir Pedido #$orderNum en '$PrinterName'"
    }
    Remove-Item $tmp -ErrorAction SilentlyContinue
}

while ($true) {
    try {
        $url = "$ServerUrl/api/agent/pendientes?key=$ApiKey&after_id=$lastId"
        $resp = Invoke-RestMethod -Uri $url -UseBasicParsing -TimeoutSec 10

        if ($resp.ok -and $resp.orders -and $resp.orders.Count -gt 0) {
            foreach ($order in $resp.orders) {
                Write-Host "Imprimiendo Pedido #$($order.numero_pedido)..."
                Print-Ticket $order.raw_text $order.numero_pedido
                if ($order.id -gt $lastId) { $lastId = $order.id }
            }
            [System.IO.File]::WriteAllText($ConfigFile, $lastId)
        }
    } catch { }
    Start-Sleep -Seconds 4
}
