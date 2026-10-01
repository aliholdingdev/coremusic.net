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

Crop 'z-banner' 552 99 491 184 2
Crop 'z-playerinfo' 53 99 469 184 2
Crop 'z-widget' 1073 99 752 185 1.6
Crop 'z-ensons' 53 350 1180 60 1
Crop 'z-playlist' 53 480 1140 60 1
$src.Dispose()
