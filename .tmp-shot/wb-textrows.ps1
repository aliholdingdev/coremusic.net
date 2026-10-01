Add-Type -AssemblyName System.Drawing
$b=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
function Lum([int]$gx,[int]$gy){ $c=$b.GetPixel($gx,$gy); ([int]$c.R*299+[int]$c.G*587+[int]$c.B*114)/1000 }
Write-Output '=== per-row count of bright px (lum>150), x=45..255, y=20..145 ==='
for($y=20;$y -le 145;$y++){
  $n=0; $mn=-1; $mx=-1
  for($x=45;$x -le 255;$x++){ if((Lum ($bx+$x) ($by+$y)) -gt 150){ $n++; if($mn -lt 0){$mn=$x}; $mx=$x } }
  if($n -gt 0){ Write-Output ("{0,3} n={1,3} x={2}..{3}" -f $y,$n,$mn,$mx) } else { Write-Output ("{0,3} -" -f $y) }
}
$b.Dispose()
