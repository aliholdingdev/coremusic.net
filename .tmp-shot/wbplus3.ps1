Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# dump colors in plus zone to learn its palette
$buckets=@{}
for ($ry=145; $ry -lt 178; $ry++){
  for ($rx=295; $rx -lt 345; $rx++){
    $p=$bmp.GetPixel($bx+$rx,$by+$ry)
    $key="{0},{1},{2}" -f ([int]($p.R/32)*32),([int]($p.G/32)*32),([int]($p.B/32)*32)
    if($buckets.ContainsKey($key)){$buckets[$key]++}else{$buckets[$key]=1}
  }
}
$buckets.GetEnumerator() | Sort-Object Value -Descending | Select-Object -First 10 | ForEach-Object { Write-Output ("{0} x{1}" -f $_.Key,$_.Value) }
# ring = pixel where R-B > 60
$minx=999;$maxx=-1;$miny=999;$maxy=-1;$n=0
for ($ry=145; $ry -lt 178; $ry++){
  for ($rx=295; $rx -lt 345; $rx++){
    $p=$bmp.GetPixel($bx+$rx,$by+$ry)
    if (($p.R - $p.B) -ge 60 -and $p.R -ge 180) {
      $n++
      if($rx -lt $minx){$minx=$rx}; if($rx -gt $maxx){$maxx=$rx}
      if($ry -lt $miny){$miny=$ry}; if($ry -gt $maxy){$maxy=$ry}
    }
  }
}
Write-Output ("warm ring: x{0}..{1} y{2}..{3} n={4}" -f $minx,$maxx,$miny,$maxy,$n)
$bmp.Dispose()
