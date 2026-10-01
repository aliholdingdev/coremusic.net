Add-Type -AssemblyName System.Drawing
$b=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
function Px([int]$gx,[int]$gy){ ,($b.GetPixel($gx,$gy)) }
Write-Output '=== ASCII map: min-channel brightness, x255..355 y142..180  (B=200+, +=180+, .=160+, space<160) ==='
for($y=142;$y -le 180;$y++){
  $line=''
  for($x=255;$x -le 355;$x++){
    $c=$b.GetPixel($bx+$x,$by+$y)
    $mn=[math]::Min([int]$c.R,[math]::Min([int]$c.G,[int]$c.B))
    if($mn -ge 200){$line+='B'} elseif($mn -ge 180){$line+='+'} elseif($mn -ge 160){$line+='.'} else {$line+=' '}
  }
  Write-Output ("{0,3} |{1}|" -f $y,$line)
}
Write-Output 'ruler: x255 at col0; tick every 10'
$r=''; for($x=255;$x -le 355;$x++){ if(($x-255)%10 -eq 0){$r+='|'} else {$r+=' '} }
Write-Output ("    |{0}|" -f $r)
Write-Output ''
Write-Output '=== same but x290..360 y150..175 with lower thr (min>=140 = h, >=120 = o) ==='
for($y=150;$y -le 175;$y++){
  $line=''
  for($x=290;$x -le 360;$x++){
    $c=$b.GetPixel($bx+$x,$by+$y)
    $mn=[math]::Min([int]$c.R,[math]::Min([int]$c.G,[int]$c.B))
    if($mn -ge 140){$line+='h'} elseif($mn -ge 120){$line+='o'} elseif($mn -ge 100){$line+='.'} else {$line+=' '}
  }
  Write-Output ("{0,3} |{1}|" -f $y,$line)
}
$r=''; for($x=290;$x -le 360;$x++){ if(($x-290)%10 -eq 0){$r+='|'} else {$r+=' '} }
Write-Output ("    |{0}|" -f $r)
$b.Dispose()
