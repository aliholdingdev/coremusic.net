Add-Type -AssemblyName System.Drawing
$dir='C:\www\coremusic.net\assets.coremusic.net\Image\res-pink'
$names=@('erkek-gender-select.png','notur-gender-select.png','session-logout.png','settings-white.png','users.png','mic-1.png','star-filled.png','star.png')
$cell=110; $cols=8
$out=New-Object System.Drawing.Bitmap(($cols*$cell),($cell+16))
$g=[System.Drawing.Graphics]::FromImage($out)
$g.Clear([System.Drawing.Color]::FromArgb(90,50,70))
$g.InterpolationMode=[System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$font=New-Object System.Drawing.Font('Arial',9)
$i=0
foreach($n in $names){
  $p=Join-Path $dir $n
  if(Test-Path -LiteralPath $p){
    $img=[System.Drawing.Image]::FromFile($p)
    $x=($i%$cols)*$cell
    $g.DrawImage($img,($x+8),4,($cell-16),($cell-16))
    $g.DrawString($n,$font,[System.Drawing.Brushes]::White,$x,($cell-8))
    $img.Dispose()
  } else { Write-Output ("MISSING " + $n) }
  $i++
}
$out.Save('C:\www\coremusic.net\.tmp-shot\mock\icons-person.png',[System.Drawing.Imaging.ImageFormat]::Png)
$g.Dispose(); $out.Dispose()
Write-Output "done"
