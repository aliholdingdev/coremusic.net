Add-Type -AssemblyName System.Drawing
$b=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$z=10; $w=44; $h=36
$o=New-Object System.Drawing.Bitmap(($w*$z),($h*$z))
$g=[System.Drawing.Graphics]::FromImage($o)
$g.InterpolationMode='NearestNeighbor'; $g.PixelOffsetMode='Half'
$g.DrawImage($b,[System.Drawing.Rectangle]::new(0,0,($w*$z),($h*$z)),[System.Drawing.Rectangle]::new(552+288,99+144,$w,$h),[System.Drawing.GraphicsUnit]::Pixel)
$g.Dispose()
$o.Save('C:\www\coremusic.net\.tmp-shot\mock\q9-ring-10x.png',[System.Drawing.Imaging.ImageFormat]::Png)
$o.Dispose(); $b.Dispose()
Write-Output 'ok 440x360 origin banner x=288 y=144'
