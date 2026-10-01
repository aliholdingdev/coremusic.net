Add-Type -AssemblyName System.Drawing
$src = 'C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png'
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$bx=552; $by=99
function Lum([int]$gx,[int]$gy){ $c=$bmp.GetPixel($gx,$gy); ([int]$c.R*299+[int]$c.G*587+[int]$c.B*114)/1000 }
function ColAvg([int]$lx,[int]$y0,[int]$y1){ $s=0;$n=0; for($y=$y0;$y -le $y1;$y++){ $s+=Lum ($bx+$lx) ($by+$y); $n++ }; $s/$n }
function RowAvg([int]$ly,[int]$x0,[int]$x1){ $s=0;$n=0; for($x=$x0;$x -le $x1;$x++){ $s+=Lum ($bx+$x) ($by+$ly); $n++ }; $s/$n }

Write-Output '=== CHIPS: vertical edges via colavg y146..173, x 0..340 ==='
$prev=$null
for($x=0;$x -le 340;$x++){
  $v=ColAvg $x 146 173
  if($prev -ne $null -and [math]::Abs($v-$prev) -ge 4.0){ Write-Output ("x={0} v={1} -> {2} (d={3})" -f $x,[int]$prev,[int]$v,[int]($v-$prev)) }
  $prev=$v
}
Write-Output '=== CHIP ROW: horizontal edges via rowavg x30..300, y 140..180 ==='
$prev=$null
for($y=140;$y -le 180;$y++){
  $v=RowAvg $y 30 300
  if($prev -ne $null -and [math]::Abs($v-$prev) -ge 2.0){ Write-Output ("y={0} v={1} -> {2} (d={3})" -f $y,[int]$prev,[int]$v,[int]($v-$prev)) }
  $prev=$v
}
Write-Output '=== PLUS RING: colavg y150..170, x 285..360 ==='
$prev=$null
for($x=285;$x -le 360;$x++){
  $v=ColAvg $x 150 170
  if($prev -ne $null -and [math]::Abs($v-$prev) -ge 4.0){ Write-Output ("x={0} v={1} -> {2} (d={3})" -f $x,[int]$prev,[int]$v,[int]($v-$prev)) }
  $prev=$v
}
Write-Output '=== PLUS RING: rowavg x295..345, y 145..180 ==='
$prev=$null
for($y=145;$y -le 180;$y++){
  $v=RowAvg $y 295 345
  if($prev -ne $null -and [math]::Abs($v-$prev) -ge 2.5){ Write-Output ("y={0} v={1} -> {2} (d={3})" -f $y,[int]$prev,[int]$v,[int]($v-$prev)) }
  $prev=$v
}
Write-Output '=== CTA PILL: colavg y120..134, x 95..200 ==='
$prev=$null
for($x=95;$x -le 200;$x++){
  $v=ColAvg $x 120 134
  if($prev -ne $null -and [math]::Abs($v-$prev) -ge 4.0){ Write-Output ("x={0} v={1} -> {2} (d={3})" -f $x,[int]$prev,[int]$v,[int]($v-$prev)) }
  $prev=$v
}
Write-Output '=== CTA PILL: rowavg x110..185, y 110..145 ==='
$prev=$null
for($y=110;$y -le 145;$y++){
  $v=RowAvg $y 110 185
  if($prev -ne $null -and [math]::Abs($v-$prev) -ge 2.0){ Write-Output ("y={0} v={1} -> {2} (d={3})" -f $y,[int]$prev,[int]$v,[int]($v-$prev)) }
  $prev=$v
}
$bmp.Dispose()
