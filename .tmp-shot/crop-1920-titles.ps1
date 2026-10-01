# 1920 — sol kolon baslik bolgesi crop (x40..520, y330..510) 2x
Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$r = [System.Drawing.Rectangle]::new(40, 330, 480, 180)
$crop = $src.Clone($r, $src.PixelFormat)
$out = New-Object System.Drawing.Bitmap(($crop.Width * 2), ($crop.Height * 2))
$g = [System.Drawing.Graphics]::FromImage($out)
$g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
$g.DrawImage($crop, 0, 0, $out.Width, $out.Height)
$g.Dispose()
$out.Save('C:\www\coremusic.net\.tmp-shot\crop-1920-titles.png', [System.Drawing.Imaging.ImageFormat]::Png)
$out.Dispose(); $crop.Dispose(); $src.Dispose()
Write-Host 'DONE'
