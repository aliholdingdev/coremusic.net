Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$out = 'C:\www\coremusic.net\.tmp-shot\mock'
function Crop($n, $x, $y, $w, $h, $s) {
  $r = New-Object System.Drawing.Rectangle $x, $y, $w, $h
  $c = $src.Clone($r, $src.PixelFormat)
  $nw = [int]($w * $s); $nh = [int]($h * $s)
  $b = New-Object System.Drawing.Bitmap $nw, $nh
  $g = [System.Drawing.Graphics]::FromImage($b)
  $g.InterpolationMode = 'NearestNeighbor'
  $g.DrawImage($c, 0, 0, $nw, $nh)
  $g.Dispose(); $c.Dispose()
  $b.Save("$out\$n.png", [System.Drawing.Imaging.ImageFormat]::Png)
  $b.Dispose()
  Write-Host "$n ${w}x${h} scale=$s"
}
# CTA region banner-rel x60..250 y100..160
Crop 'q-cta' (552+60) (99+100) 190 60 6
# Stats region banner-rel x15..300 y140..180
Crop 'q-stats' (552+15) (99+140) 285 42 5
$src.Dispose()
