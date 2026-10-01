Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# Edge-density text detector: count strong horizontal-gradient pairs per row (text strokes), x+15..x+350
Write-Output "--- edge density rows (|dL|>=60 between adjacent px) ---"
$prev = $null
for ($ry=0; $ry -lt 184; $ry++) {
  $cur = New-Object int[] 336
  for ($i=0; $i -lt 336; $i++) {
    $p=$bmp.GetPixel($bx+15+$i, $by+$ry)
    $cur[$i] = [int]$p.R+[int]$p.G+[int]$p.B
  }
  $e=0
  for ($i=1; $i -lt 336; $i++) { $d=[Math]::Abs($cur[$i]-$cur[$i-1]); if ($d -ge 60) { $e++ } }
  Write-Output ("{0},{1}" -f $ry, $e)
}
$bmp.Dispose()
