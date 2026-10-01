# 1920 — y440..500 arasi beyaz piksel x-kumelerini bul (baslik mi baska mu?)
Add-Type -AssemblyName System.Drawing
$img = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')

function Get-Clusters($img, $y0, $y1, $x0, $x1, $gapMax) {
    $hits = @()
    for ($x = $x0; $x -lt $x1; $x++) {
        $hit = $false
        for ($y = $y0; $y -le $y1; $y++) {
            $p = $img.GetPixel($x, $y)
            if ($p.R -ge 215 -and $p.G -ge 215 -and $p.B -ge 215) { $hit = $true; break }
        }
        if ($hit) { $hits += $x }
    }
    $clusters = @()
    $start = $null; $prev = $null
    foreach ($x in $hits) {
        if ($null -eq $start) { $start = $x; $prev = $x; continue }
        if ($x - $prev -gt $gapMax) {
            $clusters += [pscustomobject]@{ x0 = $start; x1 = $prev; w = ($prev - $start + 1) }
            $start = $x
        }
        $prev = $x
    }
    if ($null -ne $start) { $clusters += [pscustomobject]@{ x0 = $start; x1 = $prev; w = ($prev - $start + 1) } }
    return $clusters
}

foreach ($band in @(@(343,349), @(458,466), @(474,490), @(500,512))) {
    Write-Host ("=== y{0}..{1} ===" -f $band[0], $band[1])
    foreach ($c in (Get-Clusters $img $band[0] $band[1] 40 1900 25)) {
        Write-Host ("  x={0}..{1} w={2}" -f $c.x0, $c.x1, $c.w)
    }
}
$img.Dispose()
Write-Host 'DONE'
