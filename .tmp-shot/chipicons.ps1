Add-Type -AssemblyName System.Drawing
$src='C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png'
$img=[System.Drawing.Bitmap]::FromFile($src)
$bx=552; $by=99
# chip icon zones (relative): c1 x36..49, c2 x93..106, c3 x138..151, c4 x180..193, c5 x224..237 ; y152..167
$zones=@(@(34,150,18,20),@(91,150,18,20),@(136,150,18,20),@(178,150,18,20),@(222,150,18,20))
$z=8
$w=18*$z; $h=20*$z
$out=New-Object System.Drawing.Bitmap(($w*5+40),($h))
$g=[System.Drawing.Graphics]::FromImage($out)
$g.InterpolationMode=[System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
$g.PixelOffsetMode=[System.Drawing.Drawing2D.PixelOffsetMode]::Half
for($i=0;$i -lt 5;$i++){
  $zx=$bx+$zones[$i][0]; $zy=$by+$zones[$i][1]
  $rect=[System.Drawing.Rectangle]::new($zx,$zy,18,20)
  $dst=[System.Drawing.Rectangle]::new(($i*($w+10)),0,$w,$h)
  $g.DrawImage($img,$dst,$rect,[System.Drawing.GraphicsUnit]::Pixel)
}
$out.Save('C:\www\coremusic.net\.tmp-shot\mock\q9-chipicons-8x.png',[System.Drawing.Imaging.ImageFormat]::Png)
$g.Dispose(); $out.Dispose(); $img.Dispose()
Write-Output "done"
