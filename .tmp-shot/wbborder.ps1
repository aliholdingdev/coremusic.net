Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# Top border line detection: find exact border row first (avg over x30..300)
for ($ry=140; $ry -lt 155; $ry++) {
  $s=0; for($rx=30;$rx -lt 300;$rx++){ $p=$bmp.GetPixel($bx+$rx,$by+$ry); $s+=([int]$p.R+[int]$p.G+[int]$p.B) }
  Write-Output ("row y={0} avg={1}" -f $ry, [int]($s/270))
}
Write-Output "--- bottom border rows ---"
for ($ry=168; $ry -lt 180; $ry++) {
  $s=0; for($rx=30;$rx -lt 300;$rx++){ $p=$bmp.GetPixel($bx+$rx,$by+$ry); $s+=([int]$p.R+[int]$p.G+[int]$p.B) }
  Write-Output ("row y={0} avg={1}" -f $ry, [int]($s/270))
}
$bmp.Dispose()
