Add-Type -AssemblyName System.Drawing
$src = 'C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png'
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$bx=552; $by=99
function Lum([int]$gx,[int]$gy){ $c=$bmp.GetPixel($gx,$gy); ([int]$c.R*299+[int]$c.G*587+[int]$c.B*114)/1000 }
function Runs([int]$ry,[double]$thr){
  $out=@(); $start=-1
  for($x=10;$x -le 340;$x++){
    $a1=Lum ($bx+$x) ($by+$ry); $a2=Lum ($bx+$x+1) ($by+$ry); $a3=Lum ($bx+$x-1) ($by+$ry)
    $v=($a1+$a2+$a3)/3
    if($v -ge $thr){ if($start -lt 0){$start=$x} } else { if($start -ge 0){ if(($x-$start) -ge 3){ $out += ("{0}..{1} (w={2})" -f $start,($x-1),($x-$start)) }; $start=-1 } }
  }
  if($start -ge 0){ $out += ("{0}..END (w={1})" -f $start,(341-$start)) }
  return $out
}
foreach($ry in @(147,172)){
  foreach($t in @(96,104,112)){
    Write-Output ("=== row y={0} thr={1} ===" -f $ry,$t)
    (Runs $ry $t) | ForEach-Object { Write-Output ("   " + $_) }
  }
}
$bmp.Dispose()
