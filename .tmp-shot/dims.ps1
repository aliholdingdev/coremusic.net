Add-Type -AssemblyName System.Drawing
$files = @(
  'C:\www\coremusic.net\assets.coremusic.net\Image\background\welcome-popup-girl.png',
  'C:\www\coremusic.net\assets.coremusic.net\Image\background\bkimage1.png'
)
foreach ($f in $files) {
  $i = [System.Drawing.Image]::FromFile($f)
  Write-Host ("{0}x{1}  {2}" -f $i.Width, $i.Height, $f)
  $i.Dispose()
}
