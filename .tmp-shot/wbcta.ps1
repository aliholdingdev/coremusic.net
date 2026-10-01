Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# CTA pill: brighter-than-bg rounded rect around y112..142. Detect by column luminance profile vs neighbors
Write-Output "--- row luminance avg x110..200 (CTA zone) ---"
for ($ry=110; $ry -lt 145; $ry++) {
  $s=0
  for ($rx=110; $rx -lt 200; $rx++) { $p=$bmp.GetPixel($bx+$rx,$by+$ry); $s+=([int]$p.R+[int]$p.G+[int]$p.B) }
  Write-Output ("y={0} avg={1}" -f $ry, [int]($s/90))
}
Write-Output "--- col luminance avg y120..135 (pill x extent) ---"
for ($rx=95; $rx -lt 215; $rx++) {
  $s=0
  for ($ry=120; $ry -lt 135; $ry++) { $p=$bmp.GetPixel($bx+$rx,$by+$ry); $s+=([int]$p.R+[int]$p.G+[int]$p.B) }
  Write-Output ("x={0} avg={1}" -f $rx, [int]($s/15))
}
$bmp.Dispose()
