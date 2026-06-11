$ApiKey = "diegospizza_print_2024"
$ServerUrl = "https://diegospizzabq.click"
$ConfigFile = "$env:USERPROFILE\.diegospizza-print-id.txt"

$lastId = 0
if (Test-Path $ConfigFile) { $lastId = [int](Get-Content $ConfigFile) }

$printerName = (Get-WmiObject -Class Win32_Printer | Where-Object { $_.Default -eq $true }).Name
if (-not $printerName) {
    Write-Host "[ERROR] No hay impresora predeterminada."
    pause
    exit 1
}

Write-Host "Diego's Pizza - Agente de Impresion"
Write-Host "Impresora: $printerName"
Write-Host "Ultimo ID: $lastId"
Write-Host ""

function Print-Ticket($text, $orderNum) {
    $tmp = [System.IO.Path]::GetTempFileName() + ".txt"
    [System.IO.File]::WriteAllText($tmp, $text, [System.Text.Encoding]::UTF8)

    try {
        Write-Printer -Name $printerName -Path $tmp -ErrorAction Stop
        Write-Host "OK - Pedido #$orderNum impreso"
        Remove-Item $tmp -ErrorAction SilentlyContinue
        return
    } catch { }

    try {
        cmd /c "copy /b `"$tmp`" `"\\localhost\$printerName`"" > $null 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "OK - Pedido #$orderNum impreso"
            Remove-Item $tmp -ErrorAction SilentlyContinue
            return
        }
    } catch { }

    try {
        Get-Content $tmp | Out-Printer -Name $printerName -ErrorAction Stop
        Write-Host "OK - Pedido #$orderNum impreso"
    } catch {
        Write-Host "[ERROR] No se pudo imprimir Pedido #$orderNum"
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
