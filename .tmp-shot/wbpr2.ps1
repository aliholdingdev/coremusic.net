Add-Type -AssemblyName System.Drawing
$src=[System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552;$by=99
$x0=290;$w=70;$y0=143;$h=34;$s=8
$rect=[System.Drawing.Rectangle]::new(($bx+$x0),($by+$y0),$w,$h)
$crop=$src.Clone($rect,$src.PixelFormat)
$out=New-Object System.Drawing.Bitmap(($w*$s),($h*$s+30))
$g=[System.Drawing.Graphics]::FromImage($out)
$g.InterpolationMode=[System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
$g.DrawImage($crop,0,0,($w*$s),($h*$s))
$font=New-Object System.Drawing.Font("Arial",12)
for($v=290;$v -le 360;$v+=5){
  $px=($v-$x0)*$s
  if($v%10 -eq 0){ $g.DrawLine([System.Drawing.Pens]::Yellow,$px,0,$px,($h*$s)); $g.DrawString("$v",$font,[System.Drawing.Brushes]::Yellow,($px+2),($h*$s+4)) }
  else { $g.DrawLine([System.Drawing.Pens]::Red,$px,($h*$s-8),$px,($h*$s)) }
}
# horizontal rulers every 5 rows from y143
for($u=145;$u -le 175;$u+=5){
  $py=($u-$y0)*$s
  $g.DrawLine([System.Drawing.Pens]::Lime,0,$py,10,$py)
  $g.DrawString("$u",$font,[System.Drawing.Brushes]::Lime,12,($py-8))
}
$out.Save("C:\www\coremusic.net\.tmp-shot\mock\zwb-plus2.png")
$g.Dispose();$out.Dispose();$crop.Dispose();$src.Dispose()
Write-Output "ok"
