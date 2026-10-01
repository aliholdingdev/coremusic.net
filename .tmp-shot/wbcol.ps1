Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
function ColHist($tag, $x0, $x1, $y0, $y1, $bin) {
  Write-Output "--- $tag ---"
  $cols = @{}
  for ($rx=$x0; $rx -lt $x1; $rx++) {
    $n=0
    for ($ry=$y0; $ry -lt $y1; $ry++) {
      $p=$bmp.GetPixel($bx+$rx, $by+$ry)
      $L=[int]$p.R+[int]$p.G+[int]$p.B
      if ($L -ge 600 -and ($p.R - $p.B) -ge 40) { $n++ }
    }
    $cols[$rx] = $n
  }
  # print runs where n>=2
  $run=-1
  for ($rx=$x0; $rx -lt $x1; $rx++) {
    if ($cols[$rx] -ge 2) { if ($run -lt 0) { $run=$rx } }
    else { if ($run -ge 0) { if (($rx-$run) -ge 3) { Write-Output ("  run {0}..{1}" -f $run, ($rx-1)) }; $run=-1 } }
  }
  if ($run -ge 0) { Write-Output ("  run {0}..{1}" -f $run, ($x1-1)) }
}
ColHist "eyebrow y26-44" 40 340 26 44
ColHist "name y48-74" 20 340 48 74
ColHist "quote y84-92" 20 400 84 92
ColHist "core y98-110" 60 320 98 110
$bmp.Dispose()
