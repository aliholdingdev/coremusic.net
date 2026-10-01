Add-Type -AssemblyName System.Drawing
$src = 'C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png'
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$bx=552; $by=99; $W=491; $H=184
function Lum([int]$gx,[int]$gy){ $c=$bmp.GetPixel($gx,$gy); ([int]$c.R*299+[int]$c.G*587+[int]$c.B*114)/1000 }
# sample bg luminance grid
Write-Output '--- bg luminance samples (x,y):lum ---'
$s=''
for($y=2;$y -lt $H;$y+=10){ $row="{0,3}: " -f $y
  for($x=2;$x -lt $W;$x+=40){ $row += ("{0} " -f [int](Lum ($bx+$x) ($by+$y))) }
  Write-Output $row }
# histogram of luminance over banner
$hist=@{}
for($y=0;$y -lt $H;$y++){ for($x=0;$x -lt $W;$x++){ $l=[int](Lum ($bx+$x) ($by+$y)); $b=[int]([math]::Floor($l/10))*10; if($hist.ContainsKey($b)){$hist[$b]++}else{$hist[$b]=1} } }
Write-Output '--- luminance histogram (bucket:start -> count) ---'
foreach($k in ($hist.Keys | Sort-Object {[int]$_})){ Write-Output ("{0,3} -> {1}" -f $k,$hist[$k]) }
$bmp.Dispose()
