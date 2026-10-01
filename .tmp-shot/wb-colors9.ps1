Add-Type -AssemblyName System.Drawing
$b=[System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx=552; $by=99
function Hex([int]$lx,[int]$ly){ $c=$b.GetPixel($bx+$lx,$by+$ly); ("#{0:X2}{1:X2}{2:X2}" -f $c.R,$c.G,$c.B) }
function Top([int]$lx,[int]$ly,[int]$n){
  $best=@{}; for($y=$ly;$y -lt ($ly+$n);$y++){ for($x=$lx;$x -lt ($lx+$n);$x++){
    $c=$b.GetPixel($bx+$x,$by+$y); $k=("#"+("{0:X2}{1:X2}{2:X2}" -f [int]($c.R/16*16),[int]($c.G/16*16),[int]($c.B/16*16)))
    if($best.ContainsKey($k)){$best[$k]++}else{$best[$k]=1} } }
  ($best.GetEnumerator() | Sort-Object Value -Descending | Select-Object -First 4 | ForEach-Object { "{0}x{1}" -f $_.Value,$_.Key }) -join '  ' }
Write-Output '--- spot samples (hex) ---'
foreach($p in @(
  @(64,62,'name stroke'),
  @(70,55,'name stroke2'),
  @(150,37,'eyebrow'),
  @(120,89,'quote'),
  @(140,104,'coremusic'),
  @(140,130,'cta fill mid'),
  @(112,130,'cta fill left'),
  @(185,127,'cta icon'),
  @(45,155,'chip1 num'),
  @(50,164,'chip1 label'),
  @(60,150,'chip1 bg'),
  @(14,160,'chip outside L'),
  @(288,163,'plus center'),
  @(281,163,'plus ring left'),
  @(288,156,'plus ring top')
)){ Write-Output ("{0,-16} ({1},{2}) = {3}" -f $p[2],$p[0],$p[1],(Hex $p[0] $p[1])) }
Write-Output ''
Write-Output '--- region dominant (4x4 buckets) ---'
foreach($p in @(
  @(60,52,'name region'),
  @(105,30,'eyebrow region'),
  @(60,85,'quote region'),
  @(125,98,'coremusic region'),
  @(110,120,'cta region'),
  @(30,148,'chip1 region'),
  @(278,155,'plus region'),
  @(0,0,'banner TL'),
  @(470,0,'banner TR'),
  @(0,175,'banner BL'),
  @(470,175,'banner BR')
)){ Write-Output ("{0,-16} = {1}" -f $p[2],(Top $p[0] $p[1] 8)) }
$b.Dispose()
