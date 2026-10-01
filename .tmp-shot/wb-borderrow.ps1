Add-Type -AssemblyName System.Drawing
$src = 'C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png'
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$bx=552; $by=99
function Lum([int]$gx,[int]$gy){ $c=$bmp.GetPixel($gx,$gy); ([int]$c.R*299+[int]$c.G*587+[int]$c.B*114)/1000 }
foreach($ry in @(146,147,148,171,172,173)){
  Write-Output ("--- row y={0} ---" -f $ry)
  $line=''
  for($x=20;$x -le 300;$x++){
    $v=Lum ($bx+$x) ($by+$ry)
    # 3-sample avg horizontally for noise
    $v2=Lum ($bx+$x+1) ($by+$ry); $v3=Lum ($bx+$x-1) ($by+$ry)
    $a=($v+$v2+$v3)/3
    if($a -ge 115){$line+='#'} elseif($a -ge 100){$line+='+'} elseif($a -ge 88){$line+='.'} else {$line+=' '}
  }
  Write-Output ("    x20..300 (thr: >=115 '#', >=100 '+', >=88 '.')")
  Write-Output ("    {0}" -f $line)
}
Write-Output '--- ruler: x=20 at col0, each char = 1px ---'
$r=''; for($x=20;$x -le 300;$x++){ if(($x-20)%10 -eq 0){$r+='|'} else {$r+=' '} }
Write-Output ("    {0}" -f $r)
$bmp.Dispose()
