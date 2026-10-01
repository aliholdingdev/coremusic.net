Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# looser border detection: b147 avg vs (a143 + c151)/2 delta >= 18
$hit = New-Object bool[] 361
for ($i=0;$i -le 360;$i++){
  $b=0; for($ry=146;$ry -lt 149;$ry++){ $p=$bmp.GetPixel($bx+$i,$by+$ry); $b+=([int]$p.R+[int]$p.G+[int]$p.B) }
  $b=[int]($b/3)
  $a=0; for($ry=142;$ry -lt 145;$ry++){ $p=$bmp.GetPixel($bx+$i,$by+$ry); $a+=([int]$p.R+[int]$p.G+[int]$p.B) }
  $a=[int]($a/3)
  $c=0; for($ry=150;$ry -lt 153;$ry++){ $p=$bmp.GetPixel($bx+$i,$by+$ry); $c+=([int]$p.R+[int]$p.G+[int]$p.B) }
  $c=[int]($c/3)
  $hit[$i] = (($b - [Math]::Min($a,$c)) -ge 18)
}
$run=-1
for($i=0;$i -le 360;$i++){
  if($hit[$i]){ if($run -lt 0){$run=$i} }
  else { if($run -ge 0){ if(($i-$run) -ge 6){ Write-Output ("border {0}..{1} w={2}" -f $run,($i-1),($i-$run)) }; $run=-1 } }
}
if($run -ge 0){ Write-Output ("border {0}..360" -f $run) }
$bmp.Dispose()
