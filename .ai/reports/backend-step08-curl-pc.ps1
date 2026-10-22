<#
.SYNOPSIS
    Adim 08 - Son dinlenen sarkilar + home panel baglantisi canli curl kaniti (server PC).

.DESCRIPTION
    Sunucu PC'de `git pull` sonrasi calistirilir. MySQL bu makinede aktif ->
    gercek repo + gercek HTTP. Kapsam: POST history (csrf'li 200) -> GET recent
    kaliciligi -> csrf'siz 403 -> 422 -> auth'siz 401.

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
$cookie = Join-Path $env:TEMP ('cm-step08-' + [guid]::NewGuid().ToString('N') + '.cookie')
$anon   = Join-Path $env:TEMP ('cm-step08-anon-' + [guid]::NewGuid().ToString('N') + '.cookie')
$script:pass = 0
$script:fail = 0

function Invoke-Cm {
    param([string]$Method, [string]$Path, [hashtable]$Headers = @{}, [string]$Body, [string]$CookieFile)
    if (-not $CookieFile) { $CookieFile = $cookie }
    $a = @('-s', '-X', $Method, '-b', $CookieFile, '-c', $CookieFile, '-w', "`n%{http_code}")
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

function Assert-BodyContains {
    param([string]$Label, $Res, [string]$Needle)
    $data = ($Res.Json | ConvertFrom-Json).data
    $flat = $Res.Json
    if ($flat -like ('*' + $Needle + '*')) {
        Write-Host ('  [OK]   {0} -> icerigi barindiriyor' -f $Label) -ForegroundColor Green
        $script:pass++
    } else {
        Write-Host ('  [FAIL] {0} -> icerik yok (aranan: {1})' -f $Label, $Needle) -ForegroundColor Red
        $script:fail++
    }
}

if (-not $Email -or -not $Password) {
    Write-Error 'CM_TEST_EMAIL / CM_TEST_PASSWORD ortam degiskenleri gerekli (test hesabi).'
    exit 2
}

Write-Host ('Adim 08 curl kaniti - ' + $BaseUrl) -ForegroundColor Cyan
Write-Host ''

# 1) Login -> session cookie + csrf token
$login = Invoke-Cm -Method POST -Path '/api/v1/auth/login' `
    -Body (@{ email = $Email; password = $Password } | ConvertTo-Json -Compress)
Assert-Code 'login' $login @(200)
$csrf = ($login.Json | ConvertFrom-Json).data.csrf_token
if (-not $csrf) { Write-Error ('csrf_token alinamadi: ' + $login.Json); exit 2 }
Write-Host ('  csrf: ' + $csrf.Substring(0, 8) + '...') -ForegroundColor DarkGray

# 2) GET tracks -> gercek music_id
$tracks = Invoke-Cm -Method GET -Path '/api/v1/music/tracks?limit=1'
Assert-Code 'GET /music/tracks' $tracks @(200)
$musicId = ($tracks.Json | ConvertFrom-Json).data.items[0].music_hex
if (-not $musicId) { Write-Error ('music_id alinamadi (tracks): ' + $tracks.Json); exit 2 }
Write-Host ('  music_id: ' + $musicId) -ForegroundColor DarkGray

# 3) POST /music/history (csrf'li) -> 200 (DB satiri yazilir)
$rec = Invoke-Cm -Method POST -Path '/api/v1/music/history' `
    -Headers @{ 'x-csrf-token' = $csrf } `
    -Body (@{ music_id = $musicId; duration_sec = 180; device_type = 'desktop' } | ConvertTo-Json -Compress)
Assert-Code "POST /music/history (csrf'li)" $rec @(200)

# 4) GET /music/recent -> kalicilik: kayit listede gorunur
$recent = Invoke-Cm -Method GET -Path '/api/v1/music/recent?limit=10'
Assert-Code 'GET /music/recent' $recent @(200)
Assert-BodyContains 'recent listesinde music_id' $recent $musicId

# 5) GET /music/history -> satir gorunur
$hist = Invoke-Cm -Method GET -Path "/api/v1/music/history?page=1&per_page=20"
Assert-Code 'GET /music/history' $hist @(200)
Assert-BodyContains 'history listesinde music_id' $hist $musicId

# 6) POST csrf'siz -> 403
$noCsrf = Invoke-Cm -Method POST -Path '/api/v1/music/history' `
    -Body (@{ music_id = $musicId; duration_sec = 10 } | ConvertTo-Json -Compress)
Assert-Code 'POST (csrf yok -> 403)' $noCsrf @(403)

# 7) POST music_id eksik -> 422
$invalid = Invoke-Cm -Method POST -Path '/api/v1/music/history' `
    -Headers @{ 'x-csrf-token' = $csrf } `
    -Body (@{} | ConvertTo-Json -Compress)
Assert-Code 'POST (music_id eksik -> 422)' $invalid @(422)

# 8) GET recent auth'siz (yeni cookie = oturum yok) -> 401
$unauth = Invoke-Cm -Method GET -Path '/api/v1/music/recent?limit=10' -CookieFile $anon
Assert-Code 'GET recent (auth yok -> 401)' $unauth @(401)

Remove-Item $cookie -ErrorAction SilentlyContinue
Remove-Item $anon  -ErrorAction SilentlyContinue

$color = if ($script:fail -eq 0) { 'Green' } else { 'Red' }
Write-Host ''
Write-Host ('Sonuc: ' + $script:pass + ' gecti, ' + $script:fail + ' basarisiz') -ForegroundColor $color
exit $(if ($script:fail -eq 0) { 0 } else { 1 })
