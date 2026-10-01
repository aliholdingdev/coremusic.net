Add-Type -AssemblyName System.Drawing
$img=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
# focused dump: ring box + inside
Write-Output "== focused x272..292 y151..168 =="
for($y=151;$y -le 168;$y++){
  $line=""
  for($x=272;$x -le 292;$x++){
    $c=$img.GetPixel($bx+$x,$by+$y); $R=[int]$c.R;$G=[int]$c.G;$Bl=[int]$c.B
    $mn=[math]::Min($R,[math]::Min($G,$Bl))
    if($mn -ge 210){ $line+="#" }          # white
    elseif($R -ge 205 -and $G -ge 120 -and ($R-$Bl) -ge 60){ $line+="O" } # orange
    elseif($R -ge 120 -and $G -ge 70 -and ($R-$Bl) -ge 30){ $line+="o" }  # dim orange
    elseif($mn -ge 60){ $line+="." }        # bg-ish
    else { $line+=" " }
  }
  Write-Output ("{0,3} |{1}" -f $y,$line)
}
# ring stroke color sample
$c=$img.GetPixel($bx+275,$by+158); Write-Output ("stroke(275,158) #{0:X2}{1:X2}{2:X2}" -f $c.R,$c.G,$c.B)
$c=$img.GetPixel($bx+286,$by+159); Write-Output ("stroke(286,159) #{0:X2}{1:X2}{2:X2}" -f $c.R,$c.G,$c.B)
$c=$img.GetPixel($bx+280,$by+154); Write-Output ("top(280,154) #{0:X2}{1:X2}{2:X2}" -f $c.R,$c.G,$c.B)
# white "+" bbox strictly inside ring
$wminx=9999;$wmaxx=-1;$wminy=9999;$wmaxy=-1;$wn=0
for($y=152;$y -le 167;$y++){ for($x=273;$x -le 289;$x++){
  $c=$img.GetPixel($bx+$x,$by+$y); $mn=[math]::Min([int]$c.R,[math]::Min([int]$c.G,[int]$c.B))
  if($mn -ge 210){ $wn++; if($x -lt $wminx){$wminx=$x}; if($x -gt $wmaxx){$wmaxx=$x}; if($y -lt $wminy){$wminy=$y}; if($y -gt $wmaxy){$wmaxy=$y} }
} }
Write-Output ("white inside ring n={0} x{1}..{2} y{3}..{4}" -f $wn,$wminx,$wmaxx,$wminy,$wmaxy)
$img.Dispose()
