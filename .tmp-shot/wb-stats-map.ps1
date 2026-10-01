Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.tmp-shot\mock\zwb491x184.bmp')
function P([int]$x,[int]$y){ $c=$bmp.GetPixel($x,$y); ,@($c.R,$c.G,$c.B) }
function D([int]$x,[int]$y){ $b=[byte[]](P $x $y); $bg=[byte[]](P 0 0); $m=0; for($i=0;$i -lt 3;$i++){ $d=[math]::Abs($b[$i]-$bg[$i]); if($d -gt $m){$m=$d} }; $m }
Write-Output 'ASCII map  x0..359  y144..175   . bg   # non-bg'
for($y=144;$y -lt 176;$y++){
  $line=''
  for($x=0;$x -lt 360;$x++){ if((D $x $y) -gt 12){ $line+='#' } else { $line+='.' } }
  Write-Output ("{0,3} {1}" -f $y,$line)
}
$bmp.Dispose()
