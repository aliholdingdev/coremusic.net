Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
# banner origin (552,99) size 491x184
function Sample($name, $x0, $y0, $x1, $y1) {
  $buckets = @{}
  $maxL = -1; $maxC = $null
  for ($y = $y0; $y -lt $y1; $y++) {
    for ($x = $x0; $x -lt $x1; $x++) {
      if ($x -lt 0 -or $y -lt 0 -or $x -ge $bmp.Width -or $y -ge $bmp.Height) { continue }
      $p = $bmp.GetPixel($x, $y)
      $key = "{0},{1},{2}" -f ([int]($p.R/16)*16), ([int]($p.G/16)*16), ([int]($p.B/16)*16)
      if ($buckets.ContainsKey($key)) { $buckets[$key]++ } else { $buckets[$key] = 1 }
      $l = [int]$p.R + [int]$p.G + [int]$p.B
      if ($l -gt $maxL) { $maxL = $l; $maxC = $p }
    }
  }
  $top = $buckets.GetEnumerator() | Sort-Object Value -Descending | Select-Object -First 3
  Write-Output "== $name (x$x0..$x1 y$y0..$y1)"
  Write-Output ("   brightest: #{0:X2}{1:X2}{2:X2}" -f $maxC.R, $maxC.G, $maxC.B)
  foreach ($t in $top) { Write-Output ("   bucket {0} x{1}" -f $t.Key, $t.Value) }
}
# abs coords = banner origin (552,99) + relative
Sample "eyebrow"  (552+86)  (99+4)  (552+283) (99+16)
Sample "name"     (552+86)  (99+17) (552+283) (99+41)
Sample "quote"    (552+86)  (99+43) (552+400) (99+53)
Sample "brand"    (552+86)  (99+53) (552+300) (99+73)
Sample "cta-pill" (552+118) (99+118)(552+190)(99+138)
Sample "chip-txt" (552+33)  (99+155)(552+253)(99+168)
Sample "plus"     (552+320) (99+148)(552+340)(99+174)
$bmp.Dispose()
