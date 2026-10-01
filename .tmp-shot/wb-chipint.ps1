Add-Type -AssemblyName System.Drawing
$img=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
function XRun([int]$x0,[int]$x1,[int]$y0,[int]$y1,[int]$thr){
  $min=9999;$max=-1;$n=0
  for($yy=$y0;$yy -le $y1;$yy++){ for($xx=$x0;$xx -le $x1;$xx++){
    $c=$script:img.GetPixel($script:bx+$xx,$script:by+$yy)
    $mn=[math]::Min([int]$c.R,[math]::Min([int]$c.G,[int]$c.B))
    if($mn -ge $thr){ $n++; if($xx -lt $min){$min=$xx}; if($xx -gt $max){$max=$xx} }
  } }
  return ("x{0}..{1} w={2} n={3}" -f $min,$max,($max-$min+1),$n)
}
# chips: icon zone, number zone, label zone per chip box
Write-Output ("c1 icon  " + (XRun 29 51 150 170 150))
Write-Output ("c1 num   " + (XRun 52 81 152 161 170))
Write-Output ("c1 label " + (XRun 29 81 162 167 130))
Write-Output ("c2 icon  " + (XRun 86 107 150 170 150))
Write-Output ("c2 num   " + (XRun 108 123 152 161 170))
Write-Output ("c2 label " + (XRun 86 124 162 167 130))
Write-Output ("c3 icon  " + (XRun 131 151 150 170 150))
Write-Output ("c3 num   " + (XRun 152 168 152 161 170))
Write-Output ("c3 label " + (XRun 131 169 162 167 130))
Write-Output ("c4 icon  " + (XRun 173 193 150 170 150))
Write-Output ("c4 num   " + (XRun 194 213 152 161 170))
Write-Output ("c4 label " + (XRun 173 214 162 167 130))
Write-Output ("c5 icon  " + (XRun 217 237 150 170 150))
Write-Output ("c5 num   " + (XRun 238 266 152 161 170))
Write-Output ("c5 label " + (XRun 217 267 162 167 130))
# vertical ink extents per text
function YRun([int]$x0,[int]$x1,[int]$y0,[int]$y1,[int]$thr){
  $min=9999;$max=-1;$n=0
  for($yy=$y0;$yy -le $y1;$yy++){ for($xx=$x0;$xx -le $x1;$xx++){
    $c=$script:img.GetPixel($script:bx+$xx,$script:by+$yy)
    $mn=[math]::Min([int]$c.R,[math]::Min([int]$c.G,[int]$c.B))
    if($mn -ge $thr){ $n++; if($yy -lt $min){$min=$yy}; if($yy -gt $max){$max=$yy} }
  } }
  return ("y{0}..{1} h={2} n={3}" -f $min,$max,($max-$min+1),$n)
}
Write-Output ("c1 num y   " + (YRun 52 81 150 163 170))
Write-Output ("c1 label y " + (XRun 29 81 161 169 130))
Write-Output ("c1 label yrun " + (YRun 52 81 161 170 130))
Write-Output ("c2 label yrun " + (YRun 100 124 161 170 130))
$img.Dispose()
