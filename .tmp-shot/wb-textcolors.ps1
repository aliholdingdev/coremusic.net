Add-Type -AssemblyName System.Drawing
$img=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
$accR=0;$accG=0;$accB=0;$accN=0
function Reset-Acc(){ $script:accR=0; $script:accG=0; $script:accB=0; $script:accN=0 }
function Add-Pix($c){ $script:accR+=[int]$c.R; $script:accG+=[int]$c.G; $script:accB+=[int]$c.B; $script:accN++ }
function Res([string]$label){
  if($script:accN -gt 0){ return ("{0} n={1} #{2:X2}{3:X2}{4:X2}" -f $label,$script:accN,[int]($script:accR/$script:accN),[int]($script:accG/$script:accN),[int]($script:accB/$script:accN)) }
  return ($label + " n=0")
}
function ScanBright([string]$label,[int]$x0,[int]$x1,[int]$y0,[int]$y1,[int]$thr){
  Reset-Acc
  for($yy=$y0;$yy -le $y1;$yy++){ for($xx=$x0;$xx -le $x1;$xx++){
    $c=$script:img.GetPixel($script:bx+$xx,$script:by+$yy)
    $mn=[math]::Min([int]$c.R,[math]::Min([int]$c.G,[int]$c.B))
    if($mn -ge $thr){ Add-Pix $c }
  } }
  Write-Output (Res $label)
}
function ScanAvg([string]$label,[int]$x0,[int]$x1,[int]$y0,[int]$y1){
  Reset-Acc
  for($yy=$y0;$yy -le $y1;$yy++){ for($xx=$x0;$xx -le $x1;$xx++){
    Add-Pix ($script:img.GetPixel($script:bx+$xx,$script:by+$yy))
  } }
  Write-Output (Res $label)
}
ScanBright 'eyebrow text'   85 213 31 41 190
ScanBright 'name text'      45 237 50 72 200
ScanBright 'quote text'     63 235 86 91 170
ScanBright 'coremusic text' 127 174 100 107 170
ScanBright 'cta text'       111 175 124 131 170
ScanAvg    'cta fill mid'   130 160 128 133
ScanAvg    'cta fill top'   130 160 119 121
ScanAvg    'cta border top' 110 190 118 118
ScanAvg    'cta border L'   105 106 122 133
ScanAvg    'chip bg'        40 76 150 170
ScanAvg    'chip topbord'   40 76 147 147
ScanAvg    'chip leftbord'  28 29 152 167
ScanAvg    'ring stroke L'  275 276 156 161
ScanAvg    'ring stroke top' 278 283 154 154
ScanAvg    'row147 band'    30 260 147 148
ScanBright 'stat num text'  44 74 154 160 170
ScanBright 'stat lbl text'  44 74 162 166 140
Write-Output "-- name gradient --"
ScanBright 'name y52-55'    60 220 52 55 210
ScanBright 'name y59-62'    60 220 59 62 210
ScanBright 'name y68-71'    60 220 68 71 210
$img.Dispose()
