Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
$minx=999;$maxx=-1;$miny=999;$maxy=-1;$n=0
for ($ry=140; $ry -lt 180; $ry++){
  for ($rx=290; $rx -lt 350; $rx++){
    $p=$bmp.GetPixel($bx+$rx,$by+$ry)
    if ($p.R -ge 230 -and ($p.R - $p.B) -ge 110 -and $p.G -ge 110 -and $p.G -le 190) {
      $n++
      if($rx -lt $minx){$minx=$rx}; if($rx -gt $maxx){$maxx=$rx}
      if($ry -lt $miny){$miny=$ry}; if($ry -gt $maxy){$maxy=$ry}
    }
  }
}
Write-Output ("plus ring: x{0}..{1} y{2}..{3} n={4}" -f $minx,$maxx,$miny,$maxy,$n)
$bmp.Dispose()
