Add-Type -AssemblyName System.Drawing
$b=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$z=3; $w=491; $h=44
$o=New-Object System.Drawing.Bitmap(($w*$z),($h*$z))
$g=[System.Drawing.Graphics]::FromImage($o)
$g.InterpolationMode='NearestNeighbor'; $g.PixelOffsetMode='Half'
$g.DrawImage($b,[System.Drawing.Rectangle]::new(0,0,($w*$z),($h*$z)),[System.Drawing.Rectangle]::new(552,99+139,$w,$h),[System.Drawing.GraphicsUnit]::Pixel)
$g.Dispose()
$o.Save('C:\www\coremusic.net\.tmp-shot\mock\q9-fullstats-3x.png',[System.Drawing.Imaging.ImageFormat]::Png)
$o.Dispose(); $b.Dispose()
Write-Output 'ok 1473x132 origin banner y=139'
