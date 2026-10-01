Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
# banner origin (552,99), 491x184 — crop mid band y40..118 rel, full width, 2x scale
$x=552; $y=99+38; $w=491; $h=82
$rect = New-Object System.Drawing.Rectangle($x,$y,$w,$h)
$crop = $src.Clone($rect, $src.PixelFormat)
$out = New-Object System.Drawing.Bitmap(($w*2), ($h*2))
$g = [System.Drawing.Graphics]::FromImage($out)
$g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
$g.DrawImage($crop, 0, 0, ($w*2), ($h*2))
$out.Save("C:\www\coremusic.net\.tmp-shot\mock\wb-mid.png")
$g.Dispose(); $out.Dispose(); $crop.Dispose(); $src.Dispose()
Write-Output "ok"
