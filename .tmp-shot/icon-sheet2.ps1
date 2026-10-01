Add-Type -AssemblyName System.Drawing
$dir='C:\www\coremusic.net\assets.coremusic.net\Image\res-pink'
$names=@('music.png','playlist1.png','playlsit-2.png','cd-case.png','cd-ico.png','users.png','mic-1.png','erkek-gender-select.png','k?z-gender-select.png','notur-gender-select.png','star.png','star-filled.png','actions\favori.png','actions\like.png','kesfet.png','album-goksel.png','default-album.png','radio.png')
$cell=110; $cols=6; $rows=[math]::Ceiling($names.Count/$cols)
$out=New-Object System.Drawing.Bitmap(($cols*$cell),($rows*($cell+16)))
$g=[System.Drawing.Graphics]::FromImage($out)
$g.Clear([System.Drawing.Color]::FromArgb(90,50,70))
$g.InterpolationMode=[System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$font=New-Object System.Drawing.Font('Arial',9)
$i=0
foreach($n in $names){
  $p=Join-Path $dir $n
  if(Test-Path -LiteralPath $p){
    $img=[System.Drawing.Image]::FromFile($p)
    $x=($i%$cols)*$cell; $y=[math]::Floor($i/$cols)*($cell+16)
    $g.DrawImage($img,($x+8),($y+4),($cell-16),($cell-16))
    $g.DrawString(($n -replace '.*\\',''),$font,[System.Drawing.Brushes]::White,$x,($y+$cell-8))
    $img.Dispose()
  } else { Write-Output ("MISSING " + $n) }
  $i++
}
$out.Save('C:\www\coremusic.net\.tmp-shot\mock\icons-sheet2.png',[System.Drawing.Imaging.ImageFormat]::Png)
$g.Dispose(); $out.Dispose()
Write-Output "done"
