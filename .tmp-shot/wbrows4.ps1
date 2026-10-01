Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# Robust: for each row in x+20..x+360, count pixels brighter than row-local background by 90
for ($ry=35; $ry -lt 184; $ry++) {
  $vals = New-Object int[] 340
  for ($i=0; $i -lt 340; $i++) {
    $p=$bmp.GetPixel($bx+20+$i, $by+$ry)
    $vals[$i] = [int]$p.R+[int]$p.G+[int]$p.B
  }
  $sorted = $vals | Sort-Object
  $med = $sorted[170]
  $thr = $med + 135
  $cnt=0; $x0=-1; $x1=-1
  for ($i=0; $i -lt 340; $i++) {
    if ($vals[$i] -ge $thr -and $vals[$i] -ge 560) { $cnt++; if ($x0 -lt 0) { $x0=20+$i }; $x1=20+$i }
  }
  if ($cnt -ge 6) { Write-Output ("y={0} n={1} x{2}..{3}" -f $ry, $cnt, $x0, $x1) }
}
$bmp.Dispose()
