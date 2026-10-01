Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# Chip vertical borders: average luminance over rows 152..170 per column; border = local max vs +/-4
$w=340
$v = New-Object int[] $w
for ($i=0;$i -lt $w;$i++){
  $s=0
  for ($ry=152; $ry -lt 170; $ry++){ $p=$bmp.GetPixel($bx+$i,$by+$ry); $s+=([int]$p.R+[int]$p.G+[int]$p.B) }
  $v[$i]=[int]($s/18)
}
# smooth
$sm = New-Object int[] $w
for($i=0;$i -lt $w;$i++){
  $a=[Math]::Max(0,$i-2); $b=[Math]::Min($w-1,$i+2); $s=0;$n=0
  for($k=$a;$k -le $b;$k++){ $s+=$v[$k]; $n++ }
  $sm[$i]=[int]($s/$n)
}
# find peaks where sm[i] > sm[i-5]+12 and sm[i] > sm[i+5]+12
Write-Output "--- vertical border peaks ---"
for($i=5;$i -lt ($w-5);$i++){
  if (($sm[$i] - $sm[$i-5]) -ge 10 -and ($sm[$i] - $sm[$i+5]) -ge 10) { Write-Output ("x={0} v={1}" -f $i, $sm[$i]) }
}
Write-Output "--- profile every 5px ---"
for($i=0;$i -lt $w;$i+=5){ Write-Output ("{0},{1}" -f $i, $sm[$i]) }
$bmp.Dispose()
