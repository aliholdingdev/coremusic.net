Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# Orange plus ring: R>=200, R-B>=80, G between 100..200
$minx=999;$maxx=-1;$miny=999;$maxy=-1;$n=0
for ($ry=140; $ry -lt 180; $ry++){
  for ($rx=250; $rx -lt 360; $rx++){
    $p=$bmp.GetPixel($bx+$rx,$by+$ry)
    if ($p.R -ge 200 -and ($p.R - $p.B) -ge 80 -and $p.G -ge 90 -and $p.G -le 210) {
      $n++
      if($rx -lt $minx){$minx=$rx}; if($rx -gt $maxx){$maxx=$rx}
      if($ry -lt $miny){$miny=$ry}; if($ry -gt $maxy){$maxy=$ry}
    }
  }
}
Write-Output ("plus ring: x{0}..{1} y{2}..{3} n={4}" -f $minx,$maxx,$miny,$maxy,$n)
# chip5 right edge: check border row y147 x255..300
for ($i=255; $i -le 305; $i++){
  $b=0; for($ry=146;$ry -lt 149;$ry++){ $p=$bmp.GetPixel($bx+$i,$by+$ry); $b+=([int]$p.R+[int]$p.G+[int]$p.B) }
  $b=[int]($b/3)
  $c=0; for($ry=150;$ry -lt 153;$ry++){ $p=$mp=$bmp.GetPixel($bx+$i,$by+$ry); $c+=([int]$p.R+[int]$p.G+[int]$p.B) }
  $c=[int]($c/3)
  if (($b-$c) -ge 12) { Write-Output ("b5 x={0} d={1}" -f $i, ($b-$c)) }
}
$bmp.Dispose()
