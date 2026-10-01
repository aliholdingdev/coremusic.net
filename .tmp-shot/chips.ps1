Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx = 552; $by = 99
function P($x,$y){ $c=$src.GetPixel($bx+$x,$by+$y); 0.299*$c.R+0.587*$c.G+0.114*$c.B }

foreach ($yy in @(149, 151, 169, 171)) {
  Write-Host "=== row y=$yy : luminance peaks (local max vs +-4) ==="
  for ($x=8;$x -le 340;$x++){
    $a=P $x $yy; $l=0; $r=0
    for($k=1;$k -le 4;$k++){ $l += P ($x-$k) $yy; $r += P ($x+$k) $yy }
    $l/=4; $r/=4
    if (($a-$l) -gt 10 -and ($a-$r) -gt 10) { Write-Host ("  x={0} L={1}" -f $x,[math]::Round($a,1)) }
  }
}

Write-Host ""
Write-Host "=== row y=160 : find gaps (dark troughs) ==="
$vals = @()
for ($x=8;$x -le 340;$x++){ $vals += [pscustomobject]@{X=$x; L=[math]::Round((P $x 160 + P $x 161 + P $x 162)/3,1)} }
$vals | Where-Object { $_.L -lt 60 } | ForEach-Object { Write-Host ("  trough x={0} L={1}" -f $_.X, $_.L) }
$src.Dispose()
