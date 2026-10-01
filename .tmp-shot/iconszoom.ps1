Add-Type -AssemblyName System.Drawing
$img=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
# chip4 icon x180..193, chip5 icon x224..237, y152..167 -> crop both at 12x
$zones=@(@(176,150,20,20),@(220,150,20,20),@(34,150,20,20),@(90,150,20,20),@(134,150,20,20))
$z=12
$cw=20*$z; $ch=20*$z
$out=New-Object System.Drawing.Bitmap(($cw*5+40),$ch)
$g=[System.Drawing.Graphics]::FromImage($out)
$g.InterpolationMode=[System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
$g.PixelOffsetMode=[System.Drawing.Drawing2D.PixelOffsetMode]::Half
for($i=0;$i -lt 5;$i++){
  $rect=[System.Drawing.Rectangle]::new(($bx+$zones[$i][0]),($by+$zones[$i][1]),20,20)
  $dst=[System.Drawing.Rectangle]::new(($i*($cw+10)),0,$cw,$ch)
  $g.DrawImage($img,$dst,$rect,[System.Drawing.GraphicsUnit]::Pixel)
}
$out.Save('C:\www\coremusic.net\.tmp-shot\mock\q9-icons-zoom-12x.png',[System.Drawing.Imaging.ImageFormat]::Png)
$g.Dispose(); $out.Dispose(); $img.Dispose()
Write-Output "done"
