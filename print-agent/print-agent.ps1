$ApiKey = "diegospizza_print_2024"
$ServerUrl = "https://diegospizzabq.click"
$ConfigFile = "$env:USERPROFILE\.diegospizza-print-id.txt"
$lastId = 0

if (Test-Path $ConfigFile) { $lastId = [int](Get-Content $ConfigFile) }

Write-Host "Diego's Pizza - Agente de Impresion"
Write-Host "Imprime tickets con formato termico via Edge"
Write-Host "Ultimo ID: $lastId"
Write-Host ""

function Print-Ticket($orderId, $orderNum) {
    $url = "$ServerUrl/api/agent/ticket/$orderId?key=$ApiKey"
    Write-Host "  Imprimiendo Pedido #$orderNum..."
    $p = Start-Process "msedge.exe" -ArgumentList "--kiosk-printing --new-window `"$url`"" -PassThru
    Start-Sleep -Seconds 8
    if (-not $p.HasExited) { $p.Kill() }
    Write-Host "OK - Pedido #$orderNum impreso"
}

while ($true) {
    Start-Sleep -Seconds 4
    try {
        $resp = Invoke-RestMethod -Uri "$ServerUrl/api/agent/pendientes?key=$ApiKey&after_id=$lastId" -UseBasicParsing -TimeoutSec 10
        if ($resp.ok -and $resp.orders -and $resp.orders.Count -gt 0) {
            foreach ($order in $resp.orders) {
                Print-Ticket $order.id $order.numero_pedido
                if ($order.id -gt $lastId) { $lastId = $order.id }
            }
            [System.IO.File]::WriteAllText($ConfigFile, $lastId)
        }
    } catch { }
}
