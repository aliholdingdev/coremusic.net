Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx = 552; $by = 99
function P($x,$y){ $c=$src.GetPixel($bx+$x,$by+$y); 0.299*$c.R+0.587*$c.G+0.114*$c.B }

Write-Host "=== CTA vertical profile at x=116 (left inner) ==="
for ($y=110;$y -le 148;$y++){ $a=P 116 $y; $b=P 116 ($y-5); $c=P 116 ($y+5)
  Write-Host ("y={0} L={1} up5={2} dn5={3} dUp={4} dDn={5}" -f $y,[math]::Round($a,1),[math]::Round($b,1),[math]::Round($c,1),[math]::Round($a-$b,1),[math]::Round($a-$c,1)) }

Write-Host ""
Write-Host "=== CTA horizontal profile at y=131 ==="
for ($x=95;$x -le 215;$x++){ $a=P $x 131; $l=P ($x-5) 131; $r=P ($x+5) 131
  Write-Host ("x={0} L={1} lf5={2} rt5={3} dL={4} dR={5}" -f $x,[math]::Round($a,1),[math]::Round($l,1),[math]::Round($r,1),[math]::Round($a-$l,1),[math]::Round($a-$r,1)) }

Write-Host ""
Write-Host "=== CHIP1 vertical profile at x=45 ==="
for ($y=140;$y -le 184;$y++){ $a=P 45 $y; $b=P 45 ($y-5); $c=P 45 ($y+5)
  Write-Host ("y={0} L={1} dUp={2} dDn={3}" -f $y,[math]::Round($a,1),[math]::Round($a-$b,1),[math]::Round($a-$c,1)) }
$src.Dispose()
