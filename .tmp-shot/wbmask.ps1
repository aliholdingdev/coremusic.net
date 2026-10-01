Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
function Mask($tag, $x0, $x1, $y0, $y1) {
  $h=$y1-$y0; $w=$x1-$x0
  $L = New-Object 'int[,]' $w,$h
  for ($i=0;$i -lt $w;$i++){ for($j=0;$j -lt $h;$j++){
    $p=$bmp.GetPixel($bx+$x0+$i, $by+$y0+$j); $L[$i,$j]=[int]$p.R+[int]$p.G+[int]$p.B } }
  # per-column background = median across rows
  $bg = New-Object int[] $w
  for ($i=0;$i -lt $w;$i++){
    $arr = New-Object int[] $h
    for($j=0;$j -lt $h;$j++){ $arr[$j]=$L[$i,$j] }
    $s=$arr | Sort-Object; $bg[$i]=$s[[int]($h/2)]
  }
  $cols = New-Object int[] $w
  for ($i=0;$i -lt $w;$i++){ for($j=0;$j -lt $h;$j++){
    if (($L[$i,$j] - $bg[$i]) -ge 70 -and $L[$i,$j] -ge 500) { $cols[$i]++ } } }
  $max=0; foreach($c in $cols){ if($c -gt $max){$max=$c} }
  $thr=[Math]::Max(2,[int]($max*0.25))
  $run=-1; $first=-1; $last=-1; $runs=@()
  for($i=0;$i -lt $w;$i++){
    if($cols[$i] -ge $thr){ if($run -lt 0){$run=$i}; $last=$i }
    else { if($run -ge 0){ $runs += ("{0}..{1}" -f ($x0+$run), ($x0+$i-1)); $run=-1 } }
  }
  if($run -ge 0){ $runs += ("{0}..{1}" -f ($x0+$run), ($x0+$w-1)) }
  $first=$x0+0; # compute overall extent
  $f=-1;$l=-1
  for($i=0;$i -lt $w;$i++){ if($cols[$i] -ge $thr){ if($f -lt 0){$f=$i}; $l=$i } }
  Write-Output ("{0}: extent x{1}..{2} (w={3}) maxCol={4} thr={5}" -f $tag, ($x0+$f), ($x0+$l), ($l-$f), $max, $thr)
  Write-Output ("   runs: {0}" -f ($runs -join " "))
}
Mask "eyebrow" 60 340 26 46
Mask "name" 30 340 47 76
Mask "quote" 30 340 82 95
Mask "core" 60 340 96 112
$bmp.Dispose()
