Add-Type -AssemblyName System.Drawing
$src = 'C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png'
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$bx=552; $by=99; $W=491; $H=184
$z=3
$out = New-Object System.Drawing.Bitmap(($W*$z),($H*$z))
$g=[System.Drawing.Graphics]::FromImage($out)
$g.InterpolationMode='NearestNeighbor'
$g.PixelOffsetMode='Half'
$g.DrawImage($bmp, [System.Drawing.Rectangle]::new(0,0,($W*$z),($H*$z)), [System.Drawing.Rectangle]::new($bx,$by,$W,$H), [System.Drawing.GraphicsUnit]::Pixel)
$g.Dispose()
$out.Save('C:\www\coremusic.net\.tmp-shot\mock\q9-banner-3x.png',[System.Drawing.Imaging.ImageFormat]::Png)
$out.Dispose(); $bmp.Dispose()
Write-Output 'saved q9-banner-3x.png  (1473x552)'
