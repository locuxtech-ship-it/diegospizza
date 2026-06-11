$ApiKey = "diegospizza_print_2024"
$ServerUrl = "https://diegospizzabq.click"
$ConfigFile = "$env:USERPROFILE\.diegospizza-print-id.txt"

$lastId = 0
if (Test-Path $ConfigFile) { $lastId = [int](Get-Content $ConfigFile) }

Write-Host "================================="
Write-Host "Diego's Pizza - Agente de Impresion"
Write-Host "================================="
Write-Host "Servidor: $ServerUrl"
Write-Host "Ultimo ID impreso: $lastId"
Write-Host ""

# List available printers
Write-Host "Impresoras disponibles:"
Get-Printer | ForEach-Object { Write-Host "  - $($_.Name)" }
$default = Get-WmiObject -Class Win32_Printer | Where-Object { $_.Default -eq $true }
if ($default) { Write-Host "Predeterminada: $($default.Name)" }
Write-Host ""

function Print-Ticket($text, $orderNum) {
    $tmp = [System.IO.Path]::GetTempFileName() + ".txt"
    [System.IO.File]::WriteAllText($tmp, $text, [System.Text.Encoding]::UTF8)
    try {
        # Method 1: Out-Printer (default)
        Get-Content $tmp | Out-Printer
        Write-Host "OK - Pedido #$orderNum enviado a impresora predeterminada"
        return $true
    } catch {
        try {
            # Method 2: Copy to printer share
            $printerName = (Get-WmiObject -Class Win32_Printer | Where-Object { $_.Default -eq $true }).Name
            if ($printerName) {
                cmd /c "copy /b `"$tmp`" `"\\localhost\$printerName`"" | Out-Null
                Write-Host "OK - Pedido #$orderNum enviado a $printerName"
                return $true
            }
        } catch {
            Write-Host "[ERROR] No se pudo imprimir Pedido #$orderNum"
        }
    }
    Remove-Item $tmp -ErrorAction SilentlyContinue
    return $false
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
    } catch {
        # Silencio
    }
    Start-Sleep -Seconds 4
}
