Add-Type -AssemblyName System.Drawing
$img=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
# 1) all near-white pixels in x260..360 y140..182
$wh=@()
for($y=140;$y -le 182;$y++){ for($x=260;$x -le 360;$x++){
  $c=$img.GetPixel($bx+$x,$by+$y); $mn=[math]::Min([int]$c.R,[math]::Min([int]$c.G,[int]$c.B))
  if($mn -ge 205){ $wh += ("{0},{1}" -f $x,$y) }
} }
Write-Output ("white px n={0}" -f $wh.Count)
$wh | ForEach-Object { Write-Output ("  "+$_) }
# 2) orange pixels clusters in same window
$or=@()
for($y=140;$y -le 182;$y++){ for($x=260;$x -le 360;$x++){
  $c=$img.GetPixel($bx+$x,$by+$y); $R=[int]$c.R;$G=[int]$c.G;$Bl=[int]$c.B
  if($R -ge 205 -and $G -ge 120 -and ($R-$Bl) -ge 60 -and ($G-$Bl) -ge 25){ $or += ("{0},{1}" -f $x,$y) }
} }
Write-Output ("orange px n={0}" -f $or.Count)
# cluster by x gap > 6
$pts = $or | ForEach-Object { $p=$_.Split(','); [pscustomobject]@{x=[int]$p[0];y=[int]$p[1]} }
$grp=@(); $cur=@()
foreach($p in ($pts | Sort-Object x,y)){
  if($cur.Count -eq 0 -or ($p.x - (($cur | Measure-Object x -Maximum).Maximum)) -le 6){ $cur+=$p } else { $grp+=,$cur; $cur=@($p) }
}
if($cur.Count){ $grp+=,$cur }
$i=0
foreach($g in $grp){
  $i++
  $mx=($g|Measure-Object x -Minimum -Maximum); $my=($g|Measure-Object y -Minimum -Maximum)
  Write-Output ("cluster{0}: n={1} x{2}..{3} y{4}..{5}" -f $i,$g.Count,$mx.Minimum,$mx.Maximum,$my.Minimum,$my.Maximum)
}
$img.Dispose()
