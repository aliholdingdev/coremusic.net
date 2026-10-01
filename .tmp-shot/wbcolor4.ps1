Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
# find warm-gold pixels (R-B large, high luminance) vs near-white in name/eyebrow bands
function Warm($name, $x0, $y0, $x1, $y1) {
  $gold=0; $white=0; $n=0; $goldAvg=@(0,0,0)
  for ($y=$y0;$y -lt $y1;$y++){ for($x=$x0;$x -lt $x1;$x++){
    $p=$bmp.GetPixel($x,$y)
    if (($p.R + $p.G + $p.B) -lt 560) { continue }
    $n++
    if (($p.R - $p.B) -gt 40) { $gold++; $goldAvg[0]+=$p.R; $goldAvg[1]+=$p.G; $goldAvg[2]+=$p.B }
    else { $white++ }
  }}
  $s = if ($gold -gt 0) { ("#{0:X2}{1:X2}{2:X2}" -f [int]($goldAvg[0]/$gold), [int]($goldAvg[1]/$gold), [int]($goldAvg[2]/$gold)) } else { "-" }
  Write-Output ("{0}: n={1} gold={2} white={3} goldAvg={4}" -f $name, $n, $gold, $white, $s)
}
Warm "eyebrow" 638 103 835 115
Warm "name"    638 116 835 140
Warm "quote"   638 142 952 152
Warm "brand"   638 152 852 172
Warm "cta"     670 217 742 237
Warm "chips"   585 254 805 267
$bmp.Dispose()
