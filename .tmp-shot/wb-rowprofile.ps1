Add-Type -AssemblyName System.Drawing
$src = 'C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png'
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$bx=552; $by=99; $W=491; $H=184
function D([int]$x,[int]$y){ $c=$bmp.GetPixel($x,$y); $r=[int]$c.R; $g=[int]$c.G; $b=[int]$c.B
  # bg approx dark green/blue; measure deviation from pixel at (0,0) of banner
  $bg=$bmp.GetPixel($bx,$by); $m=0
  $d=[math]::Abs($r-[int]$bg.R); if($d -gt $m){$m=$d}
  $d=[math]::Abs($g-[int]$bg.G); if($d -gt $m){$m=$d}
  $d=[math]::Abs($b-[int]$bg.B); if($d -gt $m){$m=$d}
  $m }
$bgc=$bmp.GetPixel($bx,$by)
Write-Output ("banner origin color: {0},{1},{2}" -f $bgc.R,$bgc.G,$bgc.B)
Write-Output 'row  minx maxx count   (thr>14)'
for($y=0;$y -lt $H;$y++){
  $mn=-1;$mx=-1;$n=0
  for($x=0;$x -lt $W;$x++){ if((D ($bx+$x) ($by+$y)) -gt 14){ if($mn -lt 0){$mn=$x}; $mx=$x; $n++ } }
  if($n -gt 0){ Write-Output ("{0,3}  {1,3} {2,3} {3,3}" -f $y,$mn,$mx,$n) }
}
$bmp.Dispose()
