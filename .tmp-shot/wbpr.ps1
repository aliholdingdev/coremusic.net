Add-Type -AssemblyName System.Drawing
$src=[System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552;$by=99
$x0=270;$w=90;$y0=140;$h=40;$s=6
$rect=[System.Drawing.Rectangle]::new(($bx+$x0),($by+$y0),$w,$h)
$crop=$src.Clone($rect,$src.PixelFormat)
$out=New-Object System.Drawing.Bitmap(($w*$s),($h*$s+26))
$g=[System.Drawing.Graphics]::FromImage($out)
$g.InterpolationMode=[System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
$g.DrawImage($crop,0,0,($w*$s),($h*$s))
$font=New-Object System.Drawing.Font("Arial",11)
for($v=270;$v -le 360;$v+=10){
  $px=($v-$x0)*$s
  if($v%20 -eq 0){ $g.DrawLine([System.Drawing.Pens]::Yellow,$px,0,$px,($h*$s)); $g.DrawString("$v",$font,[System.Drawing.Brushes]::Yellow,($px+2),($h*$s+2)) }
  else { $g.DrawLine([System.Drawing.Pens]::Red,$px,($h*$s-7),$px,($h*$s)) }
}
$out.Save("C:\www\coremusic.net\.tmp-shot\mock\wb-plus-ruler.png")
$g.Dispose();$out.Dispose();$crop.Dispose();$src.Dispose()
Write-Output "ok"
