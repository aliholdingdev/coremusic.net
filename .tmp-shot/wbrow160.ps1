Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# Row y=160 raw luminance every 1px, x0..340
$v = New-Object int[] 341
for ($i=0;$i -le 340;$i++){ $p=$bmp.GetPixel($bx+$i,$by+160); $v[$i]=[int]$p.R+[int]$p.G+[int]$p.B }
Write-Output "--- y160 profile ---"
$sb = New-Object System.Text.StringBuilder
for($i=0;$i -le 340;$i++){
  [void]$sb.Append(("{0}:{1} " -f $i, $v[$i]))
  if (($i+1) % 10 -eq 0) { [void]$sb.Append("`n") }
}
Write-Output $sb.ToString()
$bmp.Dispose()
