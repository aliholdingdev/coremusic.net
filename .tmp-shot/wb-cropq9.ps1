Add-Type -AssemblyName System.Drawing
$src = 'C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png'
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$bx=552; $by=99
function Crop([int]$x,[int]$y,[int]$w,[int]$h,[int]$z,[string]$name){
  $out = New-Object System.Drawing.Bitmap(($w*$z),($h*$z))
  $g=[System.Drawing.Graphics]::FromImage($out)
  $g.InterpolationMode='NearestNeighbor'; $g.PixelOffsetMode='Half'
  $g.DrawImage($bmp, [System.Drawing.Rectangle]::new(0,0,($w*$z),($h*$z)), [System.Drawing.Rectangle]::new($bx+$x,$by+$y,$w,$h), [System.Drawing.GraphicsUnit]::Pixel)
  $g.Dispose(); $out.Save($name,[System.Drawing.Imaging.ImageFormat]::Png); $out.Dispose()
  Write-Output ("saved {0} ({1}x{2})" -f $name,($w*$z),($h*$z))
}
Crop 260 138 110 44 8 'C:\www\coremusic.net\.tmp-shot\mock\q9-plus-8x.png'
Crop 20 140 250 40 5 'C:\www\coremusic.net\.tmp-shot\mock\q9-chips-5x.png'
Crop 95 110 110 36 8 'C:\www\coremusic.net\.tmp-shot\mock\q9-cta-8x.png'
$bmp.Dispose()
