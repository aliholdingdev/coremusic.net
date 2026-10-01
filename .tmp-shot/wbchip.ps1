Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# Chip borders: scan row y=148 (top border) for luminance bumps across x
Write-Output "--- top border row scan: avg of y147..149 per column ---"
$vals=@()
for ($rx=0; $rx -lt 360; $rx++) {
  $s=0
  for ($ry=147; $ry -lt 150; $ry++) { $p=$bmp.GetPixel($bx+$rx,$by+$ry); $s+=([int]$p.R+[int]$p.G+[int]$p.B) }
  $vals += [int]($s/3)
}
# print local peaks vs neighbors
for ($rx=1; $rx -lt 359; $rx++) {
  $avg=($vals[$rx-1]+$vals[$rx]+$vals[$rx+1])/3
  # border = brighter than 6px-away neighborhood
  $nb=0; $c=0
  foreach ($d in @(-7,-6,-5,5,6,7)) { $i=$rx+$d; if ($i -ge 0 -and $i -lt 360) { $nb+=$vals[$i]; $c++ } }
  $nbAvg=$nb/$c
  if (($vals[$rx]-$nbAvg) -ge 18) { Write-Output ("x={0} v={1} nb={2}" -f $rx, $vals[$rx], [int]$nbAvg) }
}
Write-Output "--- chip bottom row: avg y172..174 ---"
for ($rx=1; $rx -lt 359; $rx++) {
  $s=0; for ($ry=172; $ry -lt 175; $ry++) { $p=$bmp.GetPixel($bx+$rx,$by+$ry); $s+=([int]$p.R+[int]$p.G+[int]$p.B) }
  $v=[int]($s/3)
  $nb=0; $c=0
  foreach ($d in @(-7,-6,-5,5,6,7)) { $i=$rx+$d; if ($i -ge 0 -and $i -lt 360) { $s2=0; for ($ry=172; $ry -lt 175; $ry++) { $p=$bmp.GetPixel($bx+$i,$by+$ry); $s2+=([int]$p.R+[int]$p.G+[int]$p.B) }; $nb+=[int]($s2/3); $c++ } }
  $nbAvg=$nb/$c
  if (($v-$nbAvg) -ge 18) { Write-Output ("x={0} v={1} nb={2}" -f $rx, $v, [int]$nbAvg) }
}
$bmp.Dispose()
