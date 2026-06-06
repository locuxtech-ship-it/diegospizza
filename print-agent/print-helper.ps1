param($Url, $TimeoutSeconds = 25)

Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

$paperMm = 57
$formW = [int]($paperMm / 25.4 * 96)

# Guardar márgenes de IE y ponerlos a 0
$regPath = "HKCU:\Software\Microsoft\Internet Explorer\PageSetup"
$oldMargins = @{}
if (Test-Path $regPath) {
    foreach ($key in @("margin_left","margin_right","margin_top","margin_bottom")) {
        $oldMargins[$key] = (Get-ItemProperty -Path $regPath -Name $key -ErrorAction SilentlyContinue).$key
        Set-ItemProperty -Path $regPath -Name $key -Value "0"
    }
} else {
    New-Item -Path $regPath -Force | Out-Null
    foreach ($key in @("margin_left","margin_right","margin_top","margin_bottom")) {
        Set-ItemProperty -Path $regPath -Name $key -Value "0"
    }
}

$form = New-Object System.Windows.Forms.Form
$form.Text = "Diego's Pizza - Impresion"
$form.Width = $formW + 20
$form.Height = 800
$form.StartPosition = 'Manual'
$form.Location = New-Object System.Drawing.Point(-9999, -9999)
$form.ShowInTaskbar = $false
$form.FormBorderStyle = 'Sizable'

$wb = New-Object System.Windows.Forms.WebBrowser
$wb.ScriptErrorsSuppressed = $true
$wb.ScrollBarsEnabled = $false
$form.Controls.Add($wb)
$wb.Dock = 'Fill'

$done = $false
$timeout = New-Object System.Windows.Forms.Timer
$timeout.Interval = ($TimeoutSeconds * 1000)
$timeout.Add_Tick({
    $timeout.Stop()
    if (-not $done) { $done = $true; $form.Close() }
})
$timeout.Start()

$wb.Add_DocumentCompleted({
    if ($done) { return }
    $done = $true
    $timeout.Stop()
    Start-Sleep 2000
    try {
        $form.Show()
        $form.Location = New-Object Drawing.Point(-9999, -9999)
        $form.Opacity = 1
        [Windows.Forms.Application]::DoEvents()
        Start-Sleep 500

        $body = $wb.Document.Body
        if (-not $body) { $form.Close(); return }
        $bodyW = [Math]::Max(1, $body.ScrollRectangle.Width)
        $bodyH = [Math]::Max(100, $body.ScrollRectangle.Height)

        $form.Height = [Math]::Min($bodyH + 40, 12000)
        [Windows.Forms.Application]::DoEvents()
        Start-Sleep 300

        $bmp = New-Object Drawing.Bitmap($bodyW, $bodyH)
        $wb.DrawToBitmap($bmp, [Drawing.Rectangle]::new(0, 0, $bodyW, $bodyH))
        $form.Hide()

        $ancho100 = [int]($paperMm / 25.4 * 100)
        $alto100 = [int]($bodyH / $bodyW * $ancho100)

        $pd = New-Object Drawing.Printing.PrintDocument
        try {
            $ps = New-Object Drawing.Printing.PaperSize("Ticket", [Math]::Max(100, $ancho100), [Math]::Max(100, $alto100))
            $pd.DefaultPageSettings.PaperSize = $ps
        } catch {}
        $pd.PrintPage = {
            $_.Graphics.InterpolationMode = [Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
            $w = $_.PageBounds.Width
            $h = [int]($bodyH * $w / $bodyW)
            $_.Graphics.DrawImage($bmp, 0, 0, $w, $h)
            $_.HasMorePages = $false
        }
        $pd.Print()
        $bmp.Dispose()
    } catch {
        try { $wb.Print() } catch {}
    }
    $form.Close()
})

$wb.Navigate($Url)
[Windows.Forms.Application]::Run($form)

if ($oldMargins.Count -gt 0) {
    foreach ($key in $oldMargins.Keys) {
        if ($oldMargins[$key]) { try { Set-ItemProperty -Path $regPath -Name $key -Value $oldMargins[$key] } catch {} }
    }
}
