Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
# Core text pixels: L>=700 (near-white core) — check warmth
function Core($name, $x0, $y0, $x1, $y1) {
  $r=0;$g=0;$b=0;$n=0; $min=[int]::MaxValue
  for ($y=$y0;$y -lt $y1;$y++){ for($x=$x0;$x -lt $x1;$x++){
    $p=$bmp.GetPixel($x,$y); $L=[int]$p.R+[int]$p.G+[int]$p.B
    if ($L -lt 700) { continue }
    $r+=$p.R;$g+=$p.G;$b+=$p.B;$n++
    if ($p.B -lt $min) { $min=$p.B }
  }}
  if ($n -gt 0) { Write-Output ("{0}: n={1} avg=#{2:X2}{3:X2}{4:X2}" -f $name,$n,[int]($r/$n),[int]($g/$n),[int]($b/$n)) }
  else { Write-Output ("{0}: no core pixels" -f $name) }
}
Core "eyebrow" 638 103 835 115
Core "name"    638 116 835 140
Core "quote"   638 142 952 152
Core "brand"   638 152 852 172
Core "cta"     670 217 742 237
Core "chips"   585 254 805 267
Core "plus"    872 247 892 273
$bmp.Dispose()
