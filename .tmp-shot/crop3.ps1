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
  Write-Host "$n ${w}x${h} s=$s"
}
Crop 'v9-stats' (552+8) (99+140) 300 44 5
Crop 'v9-cta'   (552+95) (99+110) 110 34 7
Crop 'v9-head'  (552+40) (99+15) 260 80 3
$src.Dispose()
