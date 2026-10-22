<#
.SYNOPSIS
    Adim 07 - Playlist CRUD v2 canli curl kaniti (server PC).

.DESCRIPTION
    Sunucu PC'de `git pull` sonrasi calistirilir. MySQL bu makinede aktif ->
    gercek repo + gercek HTTP. Kapsam: 200/201/204/403(CSRF)/404(IDOR)/422/405
    + kalici siralama (reorder idempotent + detail'de ayni sira).

.NOTES
    Test hesabi: CM_TEST_EMAIL / CM_TEST_PASSWORD ortam degiskenleri.
#>
[CmdletBinding()]
param(
    [string]$BaseUrl   = 'https://api.coremusic.net',
    [string]$Email     = $env:CM_TEST_EMAIL,
    [string]$Password  = $env:CM_TEST_PASSWORD
)

$ErrorActionPreference = 'Stop'
$cookie = Join-Path $env:TEMP ('cm-step07-' + [guid]::NewGuid().ToString('N') + '.cookie')
$script:pass = 0
$script:fail = 0

function Invoke-Cm {
    param([string]$Method, [string]$Path, [hashtable]$Headers = @{}, [string]$Body)
    $a = @('-s', '-X', $Method, '-b', $cookie, '-c', $cookie, '-w', "`n%{http_code}")
    foreach ($k in $Headers.Keys) { $a += @('-H', ('{0}: {1}' -f $k, $Headers[$k])) }
    if ($Body) { $a += @('-H', 'Content-Type: application/json', '-d', $Body) }
    $a += ($BaseUrl + $Path)
    $raw = & curl.exe @a
    $code = [int]($raw[-1])
    $json = ($raw[0..($raw.Count - 2)] -join "`n")
    [pscustomobject]@{ Code = $code; Json = $json }
}

function Assert-Code {
    param([string]$Label, $Res, [int[]]$Expect)
    if ($Res.Code -in $Expect) {
        Write-Host ('  [OK]   {0} -> {1}' -f $Label, $Res.Code) -ForegroundColor Green
        $script:pass++
    } else {
        Write-Host ('  [FAIL] {0} -> {1} (beklenen: {2})' -f $Label, $Res.Code, ($Expect -join '/')) -ForegroundColor Red
        $snippet = if ($Res.Json) { $Res.Json.Substring(0, [Math]::Min(220, $Res.Json.Length)) } else { '<bos>' }
        Write-Host ('         govde: {0}' -f $snippet) -ForegroundColor DarkYellow
        $script:fail++
    }
}

if (-not $Email -or -not $Password) {
    Write-Error 'CM_TEST_EMAIL / CM_TEST_PASSWORD ortam degiskenleri gerekli (test hesabi).'
    exit 2
}

Write-Host ('Adim 07 curl kaniti - ' + $BaseUrl) -ForegroundColor Cyan
Write-Host ''

# 1) Login -> session cookie + csrf token
$login = Invoke-Cm -Method POST -Path '/api/v1/auth/login' `
    -Body (@{ email = $Email; password = $Password } | ConvertTo-Json -Compress)
Assert-Code 'login' $login @(200)
$csrf = ($login.Json | ConvertFrom-Json).data.csrf_token
if (-not $csrf) { Write-Error ('csrf_token alinamadi: ' + $login.Json); exit 2 }
Write-Host ('  csrf: ' + $csrf.Substring(0, 8) + '...') -ForegroundColor DarkGray

# 2) GET list -> 200
$list = Invoke-Cm -Method GET -Path '/api/v1/playlist'
Assert-Code 'GET /playlist' $list @(200)

# 3) POST create -> 201
$created = Invoke-Cm -Method POST -Path '/api/v1/playlist' `
    -Headers @{ 'x-csrf-token' = $csrf } -Body (@{ name = 'Adim07 Curl Test' } | ConvertTo-Json -Compress)
Assert-Code 'POST /playlist (201)' $created @(201)
$plid = ($created.Json | ConvertFrom-Json).data.playlist_id
Write-Host ('  playlist_id: ' + $plid) -ForegroundColor DarkGray

# 4) GET detail -> 200
$detail = Invoke-Cm -Method GET -Path ('/api/v1/playlist/detail?id=' + $plid)
Assert-Code 'GET /playlist/detail' $detail @(200)

# 5) PUT update -> 200
$update = Invoke-Cm -Method PUT -Path '/api/v1/playlist' `
    -Headers @{ 'x-csrf-token' = $csrf } `
    -Body (@{ id = $plid; name = 'Adim07 Guncel'; visibility = 'private' } | ConvertTo-Json -Compress)
Assert-Code 'PUT /playlist (update)' $update @(200)

# 6) POST addTrack -> 200 (gercek music_id, tracks ucundan; alan: music_hex)
$tracks = Invoke-Cm -Method GET -Path '/api/v1/music/tracks?limit=1'
$musicId = ($tracks.Json | ConvertFrom-Json).data.items[0].music_hex
if (-not $musicId) { Write-Error ('music_id alinamadi (tracks): ' + $tracks.Json); exit 2 }
$add = Invoke-Cm -Method POST -Path '/api/v1/playlist/tracks' `
    -Headers @{ 'x-csrf-token' = $csrf } `
    -Body (@{ playlist_id = $plid; track_id = $musicId } | ConvertTo-Json -Compress)
Assert-Code 'POST /playlist/tracks (addTrack)' $add @(200)
$position = ($add.Json | ConvertFrom-Json).data.position
Write-Host ('  position: ' + $position) -ForegroundColor DarkGray

# 7) PUT order -> 200 (sadece eklenen)
$order1 = Invoke-Cm -Method PUT -Path '/api/v1/playlist/order' `
    -Headers @{ 'x-csrf-token' = $csrf } `
    -Body (@{ playlist_id = $plid; track_ids = @($musicId) } | ConvertTo-Json -Compress)
Assert-Code 'PUT /playlist/order #1' $order1 @(200)
$seq1 = ((($order1.Json | ConvertFrom-Json).data.order) -join ',')

# 8) PUT order tekrar -> idempotent (ayni sira yazilmali)
$order2 = Invoke-Cm -Method PUT -Path '/api/v1/playlist/order' `
    -Headers @{ 'x-csrf-token' = $csrf } `
    -Body (@{ playlist_id = $plid; track_ids = @($musicId) } | ConvertTo-Json -Compress)
$seq2 = ((($order2.Json | ConvertFrom-Json).data.order) -join ',')
if ($seq1 -eq $seq2) {
    Write-Host '  [OK]   reorder idempotent (ayni sira)' -ForegroundColor Green
    $script:pass++
} else {
    Write-Host ('  [FAIL] reorder idempotent degil: ' + $seq1 + ' vs ' + $seq2) -ForegroundColor Red
    $script:fail++
}

# 9) GET detail -> kalici sira (ayni sira donmeli)
$detail2 = Invoke-Cm -Method GET -Path ('/api/v1/playlist/detail?id=' + $plid)
$seqDetail = (((($detail2.Json | ConvertFrom-Json).data.playlist.tracks) | ForEach-Object { $_.music_hex }) -join ',')
if ($seqDetail -eq $seq1) {
    Write-Host '  [OK]   sira kalici (detail ayni sira)' -ForegroundColor Green
    $script:pass++
} else {
    Write-Host ('  [FAIL] sira kalici degil: ' + $seqDetail + ' vs ' + $seq1) -ForegroundColor Red
    $script:fail++
}

# 10) CSRF'siz PUT -> 403
$noCsrf = Invoke-Cm -Method PUT -Path '/api/v1/playlist' `
    -Body (@{ id = $plid; name = 'csrf yok' } | ConvertTo-Json -Compress)
Assert-Code 'PUT (csrf yok -> 403)' $noCsrf @(403)

# 11) bilinmeyen id -> 404 (IDOR/kayip)
$missing = Invoke-Cm -Method GET -Path '/api/v1/playlist/detail?id=99999999999999999999999999999999'
Assert-Code 'GET detail (bilinmeyen -> 404)' $missing @(404)

# 12) create name eksik -> 422
$invalid = Invoke-Cm -Method POST -Path '/api/v1/playlist' `
    -Headers @{ 'x-csrf-token' = $csrf } -Body (@{} | ConvertTo-Json -Compress)
Assert-Code 'POST (name eksik -> 422)' $invalid @(422)

# 13) yanlis method -> 405
$badMethod = Invoke-Cm -Method GET -Path '/api/v1/playlist/order'
Assert-Code 'GET /playlist/order (405)' $badMethod @(405)

# 14) DELETE removeTrack -> 200
$remove = Invoke-Cm -Method DELETE `
    -Path ('/api/v1/playlist/tracks?playlist_id=' + $plid + '&track_id=' + $musicId) `
    -Headers @{ 'x-csrf-token' = $csrf }
Assert-Code 'DELETE /playlist/tracks (removeTrack)' $remove @(200)

# 15) DELETE playlist -> 204
$del = Invoke-Cm -Method DELETE -Path ('/api/v1/playlist?id=' + $plid) `
    -Headers @{ 'x-csrf-token' = $csrf }
Assert-Code 'DELETE /playlist (204)' $del @(204)

# 16) silinen detail -> 404 (soft-delete gorunurlugu)
$gone = Invoke-Cm -Method GET -Path ('/api/v1/playlist/detail?id=' + $plid)
Assert-Code 'GET detail (silinen -> 404)' $gone @(404)

Remove-Item $cookie -ErrorAction SilentlyContinue

$color = if ($script:fail -eq 0) { 'Green' } else { 'Red' }
Write-Host ''
Write-Host ('Sonuc: ' + $script:pass + ' gecti, ' + $script:fail + ' basarisiz') -ForegroundColor $color
exit $(if ($script:fail -eq 0) { 0 } else { 1 })
