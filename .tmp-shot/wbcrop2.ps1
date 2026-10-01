Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
function Crop($name, $ry0, $ry1) {
  $x=552; $y=99+$ry0; $w=491; $h=$ry1-$ry0
  $rect = New-Object System.Drawing.Rectangle($x,$y,$w,$h)
  $crop = $src.Clone($rect, $src.PixelFormat)
  $out = New-Object System.Drawing.Bitmap(($w*2), ($h*2))
  $g = [System.Drawing.Graphics]::FromImage($out)
  $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
  $g.DrawImage($crop, 0, 0, ($w*2), ($h*2))
  $out.Save("C:\www\coremusic.net\.tmp-shot\mock\$name.png")
  $g.Dispose(); $out.Dispose(); $crop.Dispose()
}
Crop "wba-y40-78" 40 78
Crop "wbb-y78-118" 78 118
Crop "wbc-y112-145" 112 145
$src.Dispose()
Write-Output "ok"
