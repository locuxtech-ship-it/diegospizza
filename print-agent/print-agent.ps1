$ApiKey = "diegospizza_print_2024"
$ServerUrl = "https://diegospizzabq.click"
$ConfigFile = "$env:USERPROFILE\.diegospizza-print-id.txt"
$lastId = 0

if (Test-Path $ConfigFile) { $lastId = [int](Get-Content $ConfigFile) }

$printer = Get-WmiObject -Class Win32_Printer | Where-Object { $_.Default -eq $true }
if (-not $printer) { Write-Host "ERROR: No hay impresora predeterminada."; pause; exit 1 }

Write-Host "Diego's Pizza - Agente"
Write-Host "Impresora: $($printer.Name)"
Write-Host "Ultimo ID: $lastId"
Write-Host ""

function Print-Ticket($text, $orderNum) {
    $tmp = "$env:TEMP\pedido_$($orderNum).txt"
    [System.IO.File]::WriteAllText($tmp, $text, [System.Text.Encoding]::Default)
    Start-Process notepad.exe -ArgumentList "/P `"$tmp`"" -WindowStyle Hidden
    Write-Host "OK - Pedido #$orderNum enviado a $($printer.Name)"
    Remove-Item $tmp -ErrorAction SilentlyContinue
}

while ($true) {
    Start-Sleep -Seconds 4
    try {
        $resp = Invoke-RestMethod -Uri "$ServerUrl/api/agent/pendientes?key=$ApiKey&after_id=$lastId" -UseBasicParsing -TimeoutSec 10
        if ($resp.ok -and $resp.orders -and $resp.orders.Count -gt 0) {
            foreach ($order in $resp.orders) {
                Write-Host "Imprimiendo Pedido #$($order.numero_pedido)..."
                Print-Ticket $order.raw_text $order.numero_pedido
                if ($order.id -gt $lastId) { $lastId = $order.id }
            }
            [System.IO.File]::WriteAllText($ConfigFile, $lastId)
        }
    } catch { }
}
