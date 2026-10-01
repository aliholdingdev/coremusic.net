Add-Type -AssemblyName System.Drawing
$img=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
$minx=9999;$maxx=-1;$miny=9999;$maxy=-1;$n=0
$pts=@()
for($y=140;$y -le 182;$y++){ for($x=240;$x -le 420;$x++){
  $c=$img.GetPixel($bx+$x,$by+$y); $R=[int]$c.R;$G=[int]$c.G;$Bl=[int]$c.B
  # orange/amber ring: R high, G medium-high, B much lower
  if($R -ge 205 -and $G -ge 120 -and ($R-$Bl) -ge 60 -and ($G-$Bl) -ge 25){
    $n++; if($x -lt $minx){$minx=$x}; if($x -gt $maxx){$maxx=$x}; if($y -lt $miny){$miny=$y}; if($y -gt $maxy){$maxy=$y}
    $pts += ("{0},{1}" -f $x,$y)
  }
} }
Write-Output ("orange ring px n={0} bbox x{1}..{2} y{3}..{4}" -f $n,$minx,$maxx,$miny,$maxy)
if($n -gt 0 -and $n -lt 400){ $pts | ForEach-Object { Write-Output ("  " + $_) } }

# white "+" inside: very bright in that bbox
if($n -gt 0){
  $wminx=9999;$wmaxx=-1;$wminy=9999;$wmaxy=-1;$wn=0
  for($y=$miny;$y -le $maxy;$y++){ for($x=$minx;$x -le $maxx;$x++){
    $c=$img.GetPixel($bx+$x,$by+$y); $mn=[math]::Min([int]$c.R,[math]::Min([int]$c.G,[int]$c.B))
    if($mn -ge 210){ $wn++; if($x -lt $wminx){$wminx=$x}; if($x -gt $wmaxx){$wmaxx=$x}; if($y -lt $wminy){$wminy=$y}; if($y -gt $wmaxy){$wmaxy=$y} }
  } }
  Write-Output ("white + inside n={0} x{1}..{2} y{3}..{4}" -f $wn,$wminx,$wmaxx,$wminy,$wmaxy)
}
$img.Dispose()
