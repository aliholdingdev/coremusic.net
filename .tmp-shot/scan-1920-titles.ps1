# 1920 mockup — y280..560 bandinda beyaz metin satirlarini tara (En Son basligi var mi?)
Add-Type -AssemblyName System.Drawing
$png = 'C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png'
$img = [System.Drawing.Bitmap]::FromFile($png)
Write-Host ("SIZE {0}x{1}" -f $img.Width, $img.Height)

$x0 = 40; $x1 = 1900
$y0 = 280; $y1 = 560
$bands = @()
$cur = $null
for ($y = $y0; $y -lt $y1; $y++) {
    $cnt = 0
    for ($x = $x0; $x -lt $x1; $x += 2) {
        $p = $img.GetPixel($x, $y)
        if ($p.R -ge 215 -and $p.G -ge 215 -and $p.B -ge 215) { $cnt++ }
    }
    if ($cnt -ge 15) {
        if ($null -eq $cur) { $cur = @{ y0 = $y; y1 = $y; max = $cnt } }
        else { $cur.y1 = $y; if ($cnt -gt $cur.max) { $cur.max = $cnt } }
    } else {
        if ($null -ne $cur) { $bands += $cur; $cur = $null }
    }
}
if ($null -ne $cur) { $bands += $cur }
foreach ($b in $bands) {
    # band x-arisini de bul
    $xmin = 99999; $xmax = -1
    $ymid = [int](($b.y0 + $b.y1) / 2)
    for ($x = $x0; $x -lt $x1; $x++) {
        $hit = $false
        for ($yy = $b.y0; $yy -le $b.y1; $yy++) {
            $p = $img.GetPixel($x, $yy)
            if ($p.R -ge 215 -and $p.G -ge 215 -and $p.B -ge 215) { $hit = $true; break }
        }
        if ($hit) { if ($x -lt $xmin) { $xmin = $x }; if ($x -gt $xmax) { $xmax = $x } }
    }
    Write-Host ("BAND y={0}..{1} h={2} x={3}..{4} w={5} maxcnt={6}" -f $b.y0, $b.y1, ($b.y1-$b.y0+1), $xmin, $xmax, ($xmax-$xmin+1), $b.max)
}
$img.Dispose()
Write-Host 'DONE'
