$ApiKey = "diegospizza_print_2024"
$ServerUrl = "https://diegospizzabq.click"
$ConfigFile = "$env:USERPROFILE\.diegospizza-print-id.txt"

$lastId = 0
if (Test-Path $ConfigFile) { $lastId = [int](Get-Content $ConfigFile) }

Write-Host "Diego's Pizza - Agente de Impresion"
Write-Host "Servidor: $ServerUrl"
Write-Host "Ultimo ID: $lastId"
Write-Host ""

while ($true) {
    try {
        $url = "$ServerUrl/api/agent/pendientes?key=$ApiKey&after_id=$lastId"
        $resp = Invoke-RestMethod -Uri $url -UseBasicParsing -TimeoutSec 10

        if ($resp.ok -and $resp.orders -and $resp.orders.Count -gt 0) {
            foreach ($order in $resp.orders) {
                Write-Host "Imprimiendo Pedido #$($order.numero_pedido)..."
                $tmp = [System.IO.Path]::GetTempFileName() + ".txt"
                [System.IO.File]::WriteAllText($tmp, $order.raw_text, [System.Text.Encoding]::UTF8)
                Get-Content $tmp | Out-Printer
                Remove-Item $tmp
                Write-Host "OK - Pedido #$($order.numero_pedido) impreso"
                if ($order.id -gt $lastId) { $lastId = $order.id }
            }
            [System.IO.File]::WriteAllText($ConfigFile, $lastId)
        }
    } catch {
        # Silencio - solo reintenta en el proximo ciclo
    }
    Start-Sleep -Seconds 4
}
