# 1920 — x840..1280 / y440..500 bolgesini 3x buyultup kaydet (ne o bolgede?)
Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$r = [System.Drawing.Rectangle]::new(840, 440, 440, 60)
$crop = $src.Clone($r, $src.PixelFormat)
$out = New-Object System.Drawing.Bitmap(($crop.Width * 3), ($crop.Height * 3))
$g = [System.Drawing.Graphics]::FromImage($out)
$g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
$g.DrawImage($crop, 0, 0, $out.Width, $out.Height)
$g.Dispose()
$out.Save('C:\www\coremusic.net\.tmp-shot\crop-1920-mid.png', [System.Drawing.Imaging.ImageFormat]::Png)
$out.Dispose(); $crop.Dispose(); $src.Dispose()
Write-Host 'DONE'
