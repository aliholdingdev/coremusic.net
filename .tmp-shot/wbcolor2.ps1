Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
function Bright($name, $x0, $y0, $x1, $y1, $minL) {
  $buckets = @{}
  for ($y = $y0; $y -lt $y1; $y++) {
    for ($x = $x0; $x -lt $x1; $x++) {
      if ($x -lt 0 -or $y -lt 0 -or $x -ge $bmp.Width -or $y -ge $bmp.Height) { continue }
      $p = $bmp.GetPixel($x, $y)
      $l = [int]$p.R + [int]$p.G + [int]$p.B
      if ($l -lt $minL) { continue }
      $key = "{0},{1},{2}" -f ([int]($p.R/24)*24), ([int]($p.G/24)*24), ([int]($p.B/24)*24)
      if ($buckets.ContainsKey($key)) { $buckets[$key]++ } else { $buckets[$key] = 1 }
    }
  }
  Write-Output "== $name (L>=$minL)"
  $buckets.GetEnumerator() | Sort-Object Value -Descending | Select-Object -First 5 | ForEach-Object { Write-Output ("   {0} x{1}" -f $_.Key, $_.Value) }
}
Bright "eyebrow"  638 103 835 115 500
Bright "name"     638 116 835 140 500
Bright "quote"    638 142 952 152 500
Bright "brand"    638 152 852 172 500
Bright "cta-text" 670 217 742 237 550
Bright "chip"     585 254 805 267 500
$bmp.Dispose()
