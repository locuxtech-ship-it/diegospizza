param($Port=9200)

# Matar procesos anteriores en este puerto
try {
    $prev = Get-NetTCPConnection -LocalPort $Port -ErrorAction SilentlyContinue
    if ($prev -and $prev.OwningProcess -ne 4) { Stop-Process -Id $prev.OwningProcess -Force -ErrorAction SilentlyContinue; Start-Sleep 1 }
} catch {}

$listener = New-Object System.Net.HttpListener
$listener.Prefixes.Add("http://127.0.0.1:$Port/")
$listener.Prefixes.Add("http://localhost:$Port/")
$listener.Start()

Write-Host ""
Write-Host "=== Diego's Pizza - Agente de Impresion Directa ==="
Write-Host "Impresion ESC/POS sin ventanas"
Write-Host ""
Write-Host "Puerto: $Port (presiona Ctrl+C para detener)"
Write-Host ""

while ($listener.IsListening) {
    try {
        $ctx = $listener.GetContext()
        $req = $ctx.Request
        $res = $ctx.Response

        $res.Headers.Add("Access-Control-Allow-Origin", "*")
        $res.Headers.Add("Access-Control-Allow-Methods", "POST, GET, OPTIONS")
        $res.Headers.Add("Access-Control-Allow-Headers", "Content-Type")

        if ($req.HttpMethod -eq "OPTIONS") { $res.StatusCode = 204; $res.Close(); continue }

        if ($req.HttpMethod -eq "GET" -and $req.Url.AbsolutePath -eq "/ping") {
            $json = '{"ok":true,"printer":"(configurada desde el panel)"}'
            $bytes = [Text.Encoding]::UTF8.GetBytes($json)
            $res.ContentType = "application/json"
            $res.OutputStream.Write($bytes, 0, $bytes.Length)
            $res.Close()
            continue
        }

        if ($req.HttpMethod -eq "POST" -and $req.Url.AbsolutePath -eq "/print") {
            $reader = New-Object System.IO.StreamReader($req.InputStream)
            $body = $reader.ReadToEnd()
            $reader.Close()
            $data = $body | ConvertFrom-Json
            $pedidoId = $data.pedido_id
            $serverUrl = $data.server_url

            if (-not $pedidoId -or -not $serverUrl) {
                $json = '{"error":"Faltan pedido_id o server_url"}'
                $bytes = [Text.Encoding]::UTF8.GetBytes($json)
                $res.StatusCode = 400; $res.ContentType = "application/json"
                $res.OutputStream.Write($bytes, 0, $bytes.Length); $res.Close()
                continue
            }

            $configUrl = $serverUrl.TrimEnd('/') + "/admin/print-config?t=" + (Get-Date -Format "yyyyMMddHHmmss")
            $rawUrl = $serverUrl.TrimEnd('/') + "/admin/ticket/" + $pedidoId + "/raw?t=" + (Get-Date -Format "yyyyMMddHHmmss")
            Write-Host "Imprimiendo ticket #$pedidoId"
            Write-Host "  URL: $rawUrl"

            # Obtener config de impresion
            $printerName = ""
            try {
                $cfg = Invoke-RestMethod -Uri $configUrl -UseBasicParsing -TimeoutSec 5
                $printerName = $cfg.printer
                Write-Host "  Impresora configurada: '$printerName'"
            } catch {
                Write-Host "  No se pudo obtener config, usando impresora por defecto"
            }

            # Obtener texto del ticket
            try {
                $text = Invoke-RestMethod -Uri $rawUrl -UseBasicParsing -TimeoutSec 10
                Write-Host "  Ticket obtenido ($($text.Length) caracteres)"
            } catch {
                Write-Host "  ERROR obteniendo ticket: $_"
                $json = '{"error":"No se pudo obtener el ticket: ' + $_.Exception.Message.Replace('\','\\').Replace('"','\"') + '"}'
                $bytes = [Text.Encoding]::UTF8.GetBytes($json)
                $res.StatusCode = 500; $res.ContentType = "application/json"
                $res.OutputStream.Write($bytes, 0, $bytes.Length); $res.Close()
                continue
            }

            # Construir ESC/POS
            $esc = [char]27; $gs = [char]29
            $escBytes = New-Object System.Collections.Generic.List[byte]
            $escBytes.AddRange(@([byte]$esc, 0x40))
            $escBytes.AddRange(@([byte]$esc, 0x61, 0x01))
            $escBytes.AddRange(@([byte]$esc, 0x45, 0x01))
            $escBytes.AddRange([System.Text.Encoding]::GetEncoding(28591).GetBytes($text))
            $escBytes.AddRange(@([byte]$esc, 0x61, 0x00))
            $escBytes.AddRange(@([byte]$esc, 0x45, 0x00))
            $escBytes.AddRange(@([byte]10, [byte]10))
            $escBytes.AddRange(@([byte]$gs, 0x56, 0x41))
            $dataBytes = $escBytes.ToArray()
            Write-Host "  Datos ESC/POS: $($dataBytes.Length) bytes"

            # Determinar nombre de impresora
            if (-not $printerName) {
                try {
                    $printerName = (Get-CimInstance Win32_Printer -Filter "Default=$true" -ErrorAction Stop).Name
                } catch {
                    try { $printerName = (Get-WmiObject Win32_Printer -Filter "Default=$true").Name } catch {}
                }
                if (-not $printerName) { $printerName = "POS-58" }
                Write-Host "  (fallback a impresora por defecto del sistema)"
            }
            Write-Host "  Usando impresora: '$printerName'"

            # Enviar a impresora
            $ok = $false
            $errMsg = ""

            # Metodo 1: File.Write directo al puerto
            $printerPath = "\\localhost\$printerName"
            try {
                $stream = [System.IO.File]::Open($printerPath, [System.IO.FileMode]::Open, [System.IO.FileAccess]::Write, [System.IO.FileShare]::Read)
                $stream.Write($dataBytes, 0, $dataBytes.Length)
                $stream.Flush(); $stream.Close()
                $ok = $true
                Write-Host "  OK - File.Write a $printerPath"
            } catch {
                $errMsg = "File.Write fallo: $_"
                Write-Host "  $errMsg"
            }

            # Metodo 2: Write-Printer
            if (-not $ok) {
                try {
                    Import-Module PrintManagement -ErrorAction Stop
                    Write-Printer -Name $printerName -Data $dataBytes
                    $ok = $true
                    Write-Host "  OK - Write-Printer"
                } catch {
                    $errMsg = "Write-Printer fallo: $_"
                    Write-Host "  $errMsg"
                }
            }

            if ($ok) {
                $json = '{"ok":true}'
                Write-Host "  Listo!"
            } else {
                $json = '{"error":"' + $errMsg.Replace('\','\\').Replace('"','\"') + '"}'
                Write-Host "  ERROR: No se pudo imprimir"
            }

            $bytes = [Text.Encoding]::UTF8.GetBytes($json)
            $res.ContentType = "application/json"
            $res.OutputStream.Write($bytes, 0, $bytes.Length)
            $res.Close()
            continue
        }

        $res.StatusCode = 404; $res.Close()
    } catch {
        try {
            $json = '{"error":"' + $_.Exception.Message.Replace('\','\\').Replace('"','\"') + '"}'
            $bytes = [Text.Encoding]::UTF8.GetBytes($json)
            $res.StatusCode = 500; $res.ContentType = "application/json"
            $res.OutputStream.Write($bytes, 0, $bytes.Length)
            $res.Close()
        } catch {}
    }
}
