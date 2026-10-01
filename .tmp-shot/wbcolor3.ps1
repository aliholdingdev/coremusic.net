Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
# CTA pill fill: sample interior pixels away from text (pill x670..742 y217..237 abs)
function Avg($name, $x0, $y0, $x1, $y1) {
  $r=0;$g=0;$b=0;$n=0
  for ($y=$y0;$y -lt $y1;$y++){ for($x=$x0;$x -lt $x1;$x++){
    $p=$bmp.GetPixel($x,$y); $r+=$p.R; $g+=$p.G; $b+=$p.B; $n++ } }
  Write-Output ("{0}: #{1:X2}{2:X2}{3:X2} (n={4})" -f $name, [int]($r/$n), [int]($g/$n), [int]($b/$n), $n)
}
# pill top strip (above text) and bottom strip
Avg "pill-top"    674 219 738 223
Avg "pill-bottom" 674 232 738 236
# chip area background between chips (chip row y246..272 abs, chips x585..805)
Avg "chip-gap"    660 250 664 270
# banner bg right side (empty area)
Avg "bg-right"    960 110 1030 180
Avg "bg-left"     556 105 580 140
# stats chip top border line y=246
Avg "border-y246" 590 245 700 248
$bmp.Dispose()
