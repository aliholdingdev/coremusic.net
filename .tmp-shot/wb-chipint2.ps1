Add-Type -AssemblyName System.Drawing
$img=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
function Test-px($c){ return ([int]$c.R -ge 150 -and [int]$c.G -ge 125 -and [int]$c.B -ge 135) }
function RunX([string]$lbl,[int]$x0,[int]$x1,[int]$y0,[int]$y1){
  $min=9999;$max=-1;$n=0
  for($yy=$y0;$yy -le $y1;$yy++){ for($xx=$x0;$xx -le $x1;$xx++){
    if(Test-px ($script:img.GetPixel($script:bx+$xx,$script:by+$yy))){ $n++; if($xx -lt $min){$min=$xx}; if($xx -gt $max){$max=$xx} }
  } }
  Write-Output ("{0}: x{1}..{2} w={3} n={4}" -f $lbl,$min,$max,($max-$min+1),$n)
}
function RunY([string]$lbl,[int]$x0,[int]$x1,[int]$y0,[int]$y1){
  $min=9999;$max=-1;$n=0
  for($yy=$y0;$yy -le $y1;$yy++){ for($xx=$x0;$xx -le $x1;$xx++){
    if(Test-px ($script:img.GetPixel($script:bx+$xx,$script:by+$yy))){ $n++; if($yy -lt $min){$min=$yy}; if($yy -gt $max){$max=$yy} }
  } }
  Write-Output ("{0}: y{1}..{2} h={3} n={4}" -f $lbl,$min,$max,($max-$min+1),$n)
}
# numbers band y153..161, labels band y162..168
RunX 'c1 num'   50 81 153 161
RunX 'c1 label' 30 81 162 168
RunX 'c2 num'   106 123 153 161
RunX 'c2 label' 86 123 162 168
RunX 'c3 num'   152 168 153 161
RunX 'c3 label' 131 168 162 168
RunX 'c4 num'   194 213 153 161
RunX 'c4 label' 173 213 162 168
RunX 'c5 num'   238 266 153 161
RunX 'c5 label' 217 266 162 168
RunY 'c1 num y'   50 81 150 162
RunY 'c1 label y' 30 81 161 170
RunY 'c2 label y' 86 123 161 170
RunY 'c5 label y' 217 266 161 170
# icon boxes
RunX 'c1 icon x' 30 51 150 168
RunY 'c1 icon y' 36 50 148 172
RunX 'c2 icon x' 86 106 150 168
RunY 'c2 icon y' 92 105 148 172
$img.Dispose()
