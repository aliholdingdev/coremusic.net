Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx = 552; $by = 99; $bh = 184
$X0 = 40; $X1 = 300

$stats = @()
for ($y = 0; $y -lt $bh; $y++) {
  $v = New-Object System.Collections.ArrayList
  for ($x = $X0; $x -le $X1; $x++) {
    $c = $src.GetPixel($bx+$x, $by+$y)
    [void]$v.Add(0.299*$c.R + 0.587*$c.G + 0.114*$c.B)
  }
  $m = ($v | Measure-Object -Average).Average
  $sd = [math]::Sqrt((($v | ForEach-Object { [math]::Pow($_ - $m,2) } | Measure-Object -Average).Average))
  $stats += [pscustomobject]@{ Y=$y; M=[math]::Round($m,1); SD=[math]::Round($sd,1) }
}

Write-Host "=== row stddev (x40..300) ==="
foreach ($s in $stats) { Write-Host ("y={0} sd={1} mean={2}" -f $s.Y, $s.SD, $s.M) }
$src.Dispose()
