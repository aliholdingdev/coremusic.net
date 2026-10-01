Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx = 552; $by = 99
function P($x,$y){ $c=$src.GetPixel($bx+$x,$by+$y); 0.299*$c.R+0.587*$c.G+0.114*$c.B }

# Top border of chips is a bright line at y=147..149. Average y=147,148,149 minus average y=143,144,145
Write-Host "=== chip top-border detection: mean(y147..149) - mean(y142..144) ==="
$prof = @()
for ($x=5;$x -le 340;$x++) {
  $t = (P $x 147 + P $x 148 + P $x 149)/3
  $b = (P $x 142 + P $x 143 + P $x 144)/3
  $prof += [pscustomobject]@{X=$x; D=[math]::Round($t-$b,1)}
}
# report contiguous runs where D > 8
$in=$false;$s=0
foreach($p in $prof){
  if($p.D -gt 8 -and -not $in){$in=$true;$s=$p.X}
  elseif($p.D -le 8 -and $in){$in=$false; Write-Host ("  run x={0}..{1} (w={2})" -f $s,($p.X-1),($p.X-$s))}
}
if($in){Write-Host ("  run x={0}..340" -f $s)}

Write-Host ""
Write-Host "=== chip bottom-border: mean(y172..174) - mean(y177..179) ==="
$prof2 = @()
for ($x=5;$x -le 340;$x++) {
  $t = (P $x 172 + P $x 173 + P $x 174)/3
  $b = (P $x 177 + P $x 178 + P $x 179)/3
  $prof2 += [pscustomobject]@{X=$x; D=[math]::Round($t-$b,1)}
}
$in=$false;$s=0
foreach($p in $prof2){
  if($p.D -gt 6 -and -not $in){$in=$true;$s=$p.X}
  elseif($p.D -le 6 -and $in){$in=$false; Write-Host ("  run x={0}..{1} (w={2})" -f $s,($p.X-1),($p.X-$s))}
}
if($in){Write-Host ("  run x={0}..340" -f $s)}

Write-Host ""
Write-Host "=== '+' circle: scan x=290..340, y=155..170 for bright peak ==="
for ($x=285;$x -le 345;$x++){
  $m=0
  for($y=155;$y -le 170;$y++){ $v=P $x $y; if($v -gt $m){$m=$v} }
  Write-Host ("  x={0} max={1}" -f $x,[math]::Round($m,1))
}
$src.Dispose()
