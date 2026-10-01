Add-Type -AssemblyName System.Drawing
$items=@(
 @('C:\www\coremusic.net\assets.coremusic.net\Image\profiles\default-avatar.png','default-avatar'),
 @('C:\www\coremusic.net\assets.coremusic.net\Image\res-pink\users.png','users'),
 @('C:\www\coremusic.net\assets.coremusic.net\Image\res-pink\mic-1.png','mic-1'),
 @('C:\www\coremusic.net\assets.coremusic.net\Image\res-pink\erkek-gender-select.png','erkek'),
 @('C:\www\coremusic.net\assets.coremusic.net\Image\res-pink\music.png','music'),
 @('C:\www\coremusic.net\assets.coremusic.net\Image\res-pink\playlist1.png','playlist1'),
 @('C:\www\coremusic.net\assets.coremusic.net\Image\res-pink\cd-case.png','cd-case'),
 @('C:\www\coremusic.net\assets.coremusic.net\Image\res-pink\cd-ico.png','cd-ico')
)
$cell=120; $cols=8
$out=New-Object System.Drawing.Bitmap(($cols*$cell),($cell+18))
$g=[System.Drawing.Graphics]::FromImage($out)
$g.Clear([System.Drawing.Color]::FromArgb(70,40,55))
$g.InterpolationMode=[System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$font=New-Object System.Drawing.Font('Arial',10)
$i=0
foreach($it in $items){
  if(Test-Path -LiteralPath $it[0]){
    $img=[System.Drawing.Image]::FromFile($it[0])
    $x=$i*$cell
    $g.DrawImage($img,($x+10),6,($cell-20),($cell-20))
    $g.DrawString($it[1],$font,[System.Drawing.Brushes]::White,$x,($cell-6))
    $img.Dispose()
  } else { Write-Output ("MISSING " + $it[0]) }
  $i++
}
$out.Save('C:\www\coremusic.net\.tmp-shot\mock\icons-final.png',[System.Drawing.Imaging.ImageFormat]::Png)
$g.Dispose(); $out.Dispose()
Write-Output "done"
