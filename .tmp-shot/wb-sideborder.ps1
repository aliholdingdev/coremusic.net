Add-Type -AssemblyName System.Drawing
$src = 'C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png'
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$bx=552; $by=99
function Lum([int]$gx,[int]$gy){ $c=$bmp.GetPixel($gx,$gy); ([int]$c.R*299+[int]$c.G*587+[int]$c.B*114)/1000 }
function ColAvg([int]$lx,[int]$y0,[int]$y1){ $s=0.0;$n=0; for($y=$y0;$y -le $y1;$y++){ $s+=Lum ($bx+$lx) ($by+$y); $n++ }; [double]($s/$n) }
Write-Output '=== colavg y150..154 (inside chip, above text) x 25..300, printed as graph ==='
for($x=25;$x -le 300;$x++){
  $v=ColAvg $x 150 154
  $bar=[int]($v/3)
  $s='#'*$bar
  Write-Output ("{0,3} {1,3} {2}" -f $x,[int]$v,$s)
}
$bmp.Dispose()
