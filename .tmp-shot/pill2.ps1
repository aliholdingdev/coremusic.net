Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx = 552; $by = 99
function P($x,$y){ $c=$src.GetPixel($bx+$x,$by+$y); 0.299*$c.R+0.587*$c.G+0.114*$c.B }

Write-Host "=== pill column x=150 (y110..150) ==="
for ($y=110;$y -le 150;$y++){
  $a=P 150 $y; $u=P 150 ($y-3); $d=P 150 ($y+3)
  Write-Host ("y={0} L={1} dUp={2} dDn={3}" -f $y,[math]::Round($a,1),[math]::Round($a-$u,1),[math]::Round($a-$d,1)) }

Write-Host ""
Write-Host "=== pill row y=122 (above text) x=95..215 ==="
for ($x=95;$x -le 215;$x++){
  $a=P $x 122; $l=P ($x-3) 122; $r=P ($x+3) 122
  $dL=[math]::Round($a-$l,1); $dR=[math]::Round($a-$r,1)
  if ([math]::Abs($dL) -gt 8 -or [math]::Abs($dR) -gt 8) { Write-Host ("x={0} L={1} dL={2} dR={3}" -f $x,[math]::Round($a,1),$dL,$dR) } }

Write-Host ""
Write-Host "=== chip row y=160 (inside chips) x=10..300 : dL/dR > 8 ==="
for ($x=10;$x -le 300;$x++){
  $a=P $x 160; $l=P ($x-3) 160; $r=P ($x+3) 160
  $dL=[math]::Round($a-$l,1); $dR=[math]::Round($a-$r,1)
  if ([math]::Abs($dL) -gt 9 -or [math]::Abs($dR) -gt 9) { Write-Host ("x={0} L={1} dL={2} dR={3}" -f $x,[math]::Round($a,1),$dL,$dR) } }

Write-Host ""
Write-Host "=== chip col x=25 (inside chip1 left pad) y140..184 ==="
for ($y=140;$y -le 184;$y++){
  $a=P 25 $y; $u=P 25 ($y-3); $d=P 25 ($y+3)
  Write-Host ("y={0} L={1} dUp={2} dDn={3}" -f $y,[math]::Round($a,1),[math]::Round($a-$u,1),[math]::Round($a-$d,1)) }
$src.Dispose()
