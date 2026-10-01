Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# Column edge-density within each text band (text strokes = high vertical-local variance)
function ColEdge($tag, $x0, $x1, $y0, $y1) {
  $counts = @{}
  for ($rx=$x0; $rx -lt $x1; $rx++) {
    $e=0
    for ($ry=$y0; $ry -lt $y1; $ry++) {
      $p1=$bmp.GetPixel($bx+$rx, $by+$ry)
      $p2=$bmp.GetPixel($bx+$rx+1, $by+$ry)
      $L1=[int]$p1.R+[int]$p1.G+[int]$p1.B; $L2=[int]$p2.R+[int]$p2.G+[int]$p2.B
      if ([Math]::Abs($L1-$L2) -ge 70) { $e++ }
    }
    $counts[$rx]=$e
  }
  # find runs where e >= 40% of max
  $max=0; foreach ($k in $counts.Keys) { if ($counts[$k] -gt $max) { $max=$counts[$k] } }
  $thr=[Math]::Max(3, [int]($max*0.35))
  $run=-1; $runs=@()
  for ($rx=$x0; $rx -lt $x1; $rx++) {
    if ($counts[$rx] -ge $thr) { if ($run -lt 0) { $run=$rx } }
    else { if ($run -ge 0) { $runs += ("{0}..{1}" -f $run, ($rx-1)); $run=-1 } }
  }
  if ($run -ge 0) { $runs += ("{0}..{1}" -f $run, ($x1-1)) }
  Write-Output ("{0}: max={1} thr={2} runs: {3}" -f $tag, $max, $thr, ($runs -join " | "))
}
ColEdge "eyebrow" 40 340 28 42
ColEdge "name" 20 340 50 73
ColEdge "quote" 20 400 85 91
ColEdge "core" 60 330 99 108
$bmp.Dispose()
