Add-Type -AssemblyName System.Drawing
$dir='C:\www\coremusic.net\assets.coremusic.net\Image\res-pink'
$names=@('music.png','playlist1.png','playlsit-2.png','cd-case.png','cd-ico.png','users.png','mic-1.png','girl-1.png','album-goksel.png','default-album.png','star.png','favori.png','actions\favori.png','actions\like.png','kesfet.png','music-2.png')
$cell=64; $cols=8; $rows=[math]::Ceiling($names.Count/$cols)
$out=New-Object System.Drawing.Bitmap(($cols*$cell),($rows*$cell))
$g=[System.Drawing.Graphics]::FromImage($out)
$g.Clear([System.Drawing.Color]::FromArgb(40,20,30))
$font=New-Object System.Drawing.Font('Arial',8)
$i=0
foreach($n in $names){
  $p=Join-Path $dir $n
  if(Test-Path $p){
    $img=[System.Drawing.Image]::FromFile($p)
    $x=($i%$cols)*$cell; $y=[math]::Floor($i/$cols)*$cell
    $g.DrawImage($img,$x,$y,$cell-14,$cell-14)
    $g.DrawString(($n -replace '.*\\',''),$font,[System.Drawing.Brushes]::White,$x,($y+$cell-13))
    $img.Dispose()
  } else { Write-Output ("MISSING " + $n) }
  $i++
}
$out.Save('C:\www\coremusic.net\.tmp-shot\mock\icons-sheet.png',[System.Drawing.Imaging.ImageFormat]::Png)
$g.Dispose(); $out.Dispose()
Write-Output "done"
