Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx = 552; $by = 99
function Lum($x, $y) { $c = $src.GetPixel($x, $y); 0.299*$c.R + 0.587*$c.G + 0.114*$c.B }

Write-Host "=== CTA: row means (inside x100..185 vs bg x210..270) ==="
for ($y = 105; $y -le 150; $y++) {
  $si=0.0; $sb=0.0
  for ($x=100;$x -le 185;$x++){ $si += Lum ($bx+$x) ($by+$y) }
  for ($x=210;$x -le 270;$x++){ $sb += Lum ($bx+$x) ($by+$y) }
  $mi=[math]::Round($si/86,1); $mb=[math]::Round($sb/61,1)
  Write-Host ("y={0}  inside={1}  bg={2}  delta={3}" -f $y,$mi,$mb,[math]::Round($mi-$mb,1))
}
Write-Host ""
Write-Host "=== CTA: column means (inside y124..140 vs bg y150..160) ==="
for ($x = 80; $x -le 215; $x++) {
  $si=0.0; $sb=0.0
  for ($y=124;$y -le 140;$y++){ $si += Lum ($bx+$x) ($by+$y) }
  for ($y=150;$y -le 160;$y++){ $sb += Lum ($bx+$x) ($by+$y) }
  $mi=[math]::Round($si/17,1); $mb=[math]::Round($sb/11,1)
  if ($x % 1 -eq 0) { Write-Host ("x={0}  inside={1}  below={2}  delta={3}" -f $x,$mi,$mb,[math]::Round($mi-$mb,1)) }
}
$src.Dispose()
