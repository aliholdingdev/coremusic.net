Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# Chip fill vs background: avg(y152..170) minus avg(y178..188) per column
$d = New-Object int[] 361
for ($i=0;$i -le 360;$i++){
  $a=0; for($ry=152;$ry -lt 170;$ry++){ $p=$bmp.GetPixel($bx+$i,$by+$ry); $a+=([int]$p.R+[int]$p.G+[int]$p.B) }
  $a=[int]($a/18)
  $b=0; for($ry=178;$ry -lt 188;$ry++){ $p=$bmp.GetPixel($bx+$i,$by+$ry); $b+=([int]$p.R+[int]$p.G+[int]$p.B) }
  $b=[int]($b/10)
  $d[$i]=$a-$b
}
# smooth 5
$sm=New-Object int[] 361
for($i=0;$i -le 360;$i++){ $s=0;$n=0; for($k=[Math]::Max(0,$i-2);$k -le [Math]::Min(360,$i+2);$k++){ $s+=$d[$k]; $n++ }; $sm[$i]=[int]($s/$n) }
# print runs where sm >= 15
$run=-1
for($i=0;$i -le 360;$i++){
  if ($sm[$i] -ge 12) { if($run -lt 0){$run=$i} }
  else { if($run -ge 0){ if(($i-$run) -ge 5){ Write-Output ("fill run {0}..{1}" -f $run,($i-1)) }; $run=-1 } }
}
if($run -ge 0){ Write-Output ("fill run {0}..360" -f $run) }
Write-Output "--- profile x0..360 step2 ---"
for($i=0;$i -le 360;$i+=2){ Write-Output ("{0},{1}" -f $i,$sm[$i]) }
$bmp.Dispose()
