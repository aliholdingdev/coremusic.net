Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
$x0=15; $w=340; $y0=18; $h=124; $s=3
$rect=[System.Drawing.Rectangle]::new(($bx+$x0), ($by+$y0), $w, $h)
$crop=$src.Clone($rect, $src.PixelFormat)
$out=New-Object System.Drawing.Bitmap(($w*$s), ($h*$s+30))
$g=[System.Drawing.Graphics]::FromImage($out)
$g.InterpolationMode=[System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
$g.DrawImage($crop, 0, 0, ($w*$s), ($h*$s))
$penR=[System.Drawing.Pens]::Red; $penY=[System.Drawing.Pens]::Yellow
$font=New-Object System.Drawing.Font("Arial", 11)
# horizontal rules every 10 rows (banner coords)
for ($v=20; $v -le 142; $v+=10) {
  $py=($v-$y0)*$s
  if ($v % 20 -eq 0) {
    $g.DrawLine($penY, 0, $py, ($w*$s), $py)
    $g.DrawString("$v", $font, [System.Drawing.Brushes]::Yellow, 2, ($py+1))
  } else {
    $g.DrawLine($penR, 0, $py, 8, $py)
  }
}
# vertical ticks every 10
for ($v=20; $v -le 350; $v+=10) {
  $px=($v-$x0)*$s
  if ($v % 50 -eq 0) {
    $g.DrawLine($penY, $px, 0, $px, ($h*$s))
    $g.DrawString("$v", $font, [System.Drawing.Brushes]::Yellow, ($px+2), ($h*$s+2))
  } else {
    $g.DrawLine($penR, $px, ($h*$s-8), $px, ($h*$s))
  }
}
$out.Save("C:\www\coremusic.net\.tmp-shot\mock\wb-head-ruler.png")
$g.Dispose(); $out.Dispose(); $crop.Dispose(); $src.Dispose()
Write-Output "ok"
