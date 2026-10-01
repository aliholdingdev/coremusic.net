Add-Type -AssemblyName System.Drawing
$img=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
Write-Output "== raw colors x274..290 y152..166 =="
for($y=152;$y -le 166;$y++){
  $row=""
  for($x=274;$x -le 290;$x++){
    $c=$img.GetPixel($bx+$x,$by+$y)
    $row += ("{0},{1},{2};" -f [int]$c.R,[int]$c.G,[int]$c.B)
  }
  Write-Output ("y{0}: {1}" -f $y,$row)
}
Write-Output "== wider map x265..345 y143..181 =="
for($y=143;$y -le 181;$y++){
  $line=""
  for($x=265;$x -le 345;$x++){
    $c=$img.GetPixel($bx+$x,$by+$y); $R=[int]$c.R;$G=[int]$c.G;$Bl=[int]$c.B
    $mn=[math]::Min($R,[math]::Min($G,$Bl))
    if($mn -ge 210){ $line+="#" }
    elseif($R -ge 205 -and $G -ge 120 -and ($R-$Bl) -ge 60){ $line+="O" }
    elseif($R -ge 140 -and ($R-$Bl) -ge 40){ $line+="o" }
    elseif($mn -ge 60){ $line+="." }
    else { $line+=" " }
  }
  Write-Output ("{0,3} |{1}" -f $y,$line)
}
$img.Dispose()
