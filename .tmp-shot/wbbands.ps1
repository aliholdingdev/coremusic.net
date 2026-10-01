Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99; $w=360
$bands = @(@(4,20),@(20,46),@(46,58),@(58,76),@(76,96),@(96,112),@(112,142),@(142,180))
$scale = 3
$padH = 24
$totals = 0; foreach ($b in $bands) { $totals += ($b[1]-$b[0])*$scale + $padH }
$out = New-Object System.Drawing.Bitmap(($w*$scale), $totals)
$g = [System.Drawing.Graphics]::FromImage($out)
$g.Clear([System.Drawing.Color]::FromArgb(255,20,20,30))
$g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
$font = New-Object System.Drawing.Font("Arial", 14)
$brush = [System.Drawing.Brushes]::Yellow
$y=0
foreach ($b in $bands) {
  $h = $b[1]-$b[0]
  $rect = [System.Drawing.Rectangle]::new($bx, ($by+$b[0]), $w, $h)
  $crop = $src.Clone($rect, $src.PixelFormat)
  $g.DrawImage($crop, 0, $y, ($w*$scale), ($h*$scale))
  $crop.Dispose()
  $y += $h*$scale
  $g.DrawString(("rows {0}-{1}" -f $b[0], $b[1]), $font, $brush, 4, ($y+3))
  $y += $padH
}
$out.Save("C:\www\coremusic.net\.tmp-shot\mock\wb-bands.png")
$g.Dispose(); $out.Dispose(); $src.Dispose()
Write-Output "ok"
