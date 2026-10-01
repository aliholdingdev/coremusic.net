Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$out = 'C:\www\coremusic.net\.tmp-shot\mock'
New-Item -ItemType Directory -Force -Path $out | Out-Null

function Crop($n, $x, $y, $w, $h, $s) {
  $r = New-Object System.Drawing.Rectangle $x, $y, $w, $h
  $c = $src.Clone($r, $src.PixelFormat)
  if ($s -ne 1) {
    $nw = [int]($w * $s); $nh = [int]($h * $s)
    $b = New-Object System.Drawing.Bitmap $nw, $nh
    $g = [System.Drawing.Graphics]::FromImage($b)
    $g.InterpolationMode = 'NearestNeighbor'
    $g.DrawImage($c, 0, 0, $nw, $nh)
    $g.Dispose(); $c.Dispose(); $c = $b
  }
  $c.Save("$out\$n.png", [System.Drawing.Imaging.ImageFormat]::Png)
  $c.Dispose()
  Write-Host "$n ${w}x${h} scale=$s"
}

# banner origin = (552,99)
Crop 'b-left'   552 99 300 184 3
Crop 'b-stats'  552 240 340 44 4
Crop 'b-cta'    552 225 200 40 4
$src.Dispose()
