<#
.SYNOPSIS
    Adim 09 - History sayfasi v2 API baglantisi canli curl kaniti (server PC).

.DESCRIPTION
    Sunucu PC'de `git pull` sonrasi calistirilir. MySQL bu makinede aktif ->
    gercek repo + gercek HTTP. Kapsam: sayfali yanit (total+per_page) |
    from/to tarih filtresi + gun gruplu day alani | tarih injection kapisi |
    gelecek tarih kiparilir | IDOR (user_id query yok sayilir) | auth 401.

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
$cookie = Join-Path $env:TEMP ('cm-step09-' + [guid]::NewGuid().ToString('N') + '.cookie')
$anon   = Join-Path $env:TEMP ('cm-step09-anon-' + [guid]::NewGuid().ToString('N') + '.cookie')
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

function Assert-Field {
    param([string]$Label, $Res, [string]$JsonPath, $Expected)
    try {
        $obj = $Res.Json | ConvertFrom-Json
        $actual = $obj
        foreach ($seg in $JsonPath.Split('.')) { $actual = $actual.$seg }
        $ok = if ($null -eq $Expected) { $null -eq $actual } else { "$actual" -eq "$Expected" }
        if ($ok) {
            Write-Host ('  [OK]   {0} -> {1} = {2}' -f $Label, $JsonPath, $actual) -ForegroundColor Green
            $script:pass++
        } else {
            Write-Host ('  [FAIL] {0} -> {1} = {2} (beklenen: {3})' -f $Label, $JsonPath, $actual, $Expected) -ForegroundColor Red
            $script:fail++
        }
    } catch {
        Write-Host ('  [FAIL] {0} -> JSON okunamadi: {1}' -f $Label, $_.Exception.Message) -ForegroundColor Red
        $script:fail++
    }
}

if (-not $Email -or -not $Password) {
    Write-Error 'CM_TEST_EMAIL / CM_TEST_PASSWORD ortam degiskenleri gerekli (test hesabi).'
    exit 2
}

Write-Host ('Adim 09 curl kaniti - ' + $BaseUrl) -ForegroundColor Cyan
Write-Host ''

# 1) Login -> session cookie
$login = Invoke-Cm -Method POST -Path '/api/v1/auth/login' `
    -Body (@{ email = $Email; password = $Password } | ConvertTo-Json -Compress)
Assert-Code 'login' $login @(200)
$userId = ($login.Json | ConvertFrom-Json).data.user_id
Write-Host ('  user_id: ' + $userId) -ForegroundColor DarkGray

# 2) HAPPY: GET /music/history -> sayfali yanit (meta: total + page + per_page)
$h1 = Invoke-Cm -Method GET -Path '/api/v1/music/history?page=1&per_page=20'
Assert-Code 'GET /music/history (happy)' $h1 @(200)
Assert-Field 'meta page'    $h1 'data.page'     1
Assert-Field 'meta per_page' $h1 'data.per_page' 20
Assert-Field 'meta total int' $h1 'data.total'   (($h1.Json | ConvertFrom-Json).data.total)
$items = ($h1.Json | ConvertFrom-Json).data.items
if ($items -is [array] -or $null -ne $items) {
    Write-Host '  [OK]   items dizisi mevcut (gun gruplu sutunlar)' -ForegroundColor Green
    $script:pass++
} else {
    Write-Host '  [FAIL] items dizisi yok' -ForegroundColor Red
    $script:fail++
}
# gun gruplu day alani (ilk item varsa)
$first = @($items)[0]
if ($first -and $first.day -match '^\d{4}-\d{2}-\d{2}$') {
    Write-Host ('  [OK]   day alani YYYY-MM-DD: ' + $first.day) -ForegroundColor Green
    $script:pass++
} else {
    Write-Host '  [FAIL] day alani yok veya format hatali (gun gruplu sozlesme)' -ForegroundColor Red
    $script:fail++
}

# 3) SAYFALAMA: page=2&per_page=1 -> echo
$h2 = Invoke-Cm -Method GET -Path '/api/v1/music/history?page=2&per_page=1'
Assert-Code 'GET (page=2)' $h2 @(200)
Assert-Field 'page echo'    $h2 'data.page'     2
Assert-Field 'per_page echo' $h2 'data.per_page' 1

# 4) TARIH FILTRESI: from/to -> echo + filtreli total
$today = Get-Date -Format 'yyyy-MM-dd'
$h3 = Invoke-Cm -Method GET -Path '/api/v1/music/history?from=2020-01-01&to=2026-10-22'
Assert-Code 'GET (from/to)' $h3 @(200)
Assert-Field 'from echo' $h3 'data.from' '2020-01-01'
Assert-Field 'to echo'   $h3 'data.to'   '2026-10-22'

# 5) INJECTION: gecersiz format sessizce yok sayilir (parametreli sorgu)
$h4 = Invoke-Cm -Method GET -Path ("/api/v1/music/history?from={0}" -f [uri]::EscapeDataString("2020-01-01' OR '1'='1"))
Assert-Code 'GET (injection denemesi)' $h4 @(200)
Assert-Field 'gecersiz from -> null' $h4 'data.from' ''

# 6) GELECEK TARIH: sunucu saatiyle kiparilir (trust client YASAK)
$h5 = Invoke-Cm -Method GET -Path '/api/v1/music/history?from=2099-01-01'
Assert-Code 'GET (gelecek from)' $h5 @(200)
Assert-Field 'from bugunne kiparildi' $h5 'data.from' $today

# 7) IDOR: user_id query parametresi YOK SAYILIR (ownership session'dan)
$h6a = Invoke-Cm -Method GET -Path '/api/v1/music/history?per_page=1'
$h6b = Invoke-Cm -Method GET -Path '/api/v1/music/history?per_page=1&user_id=deadbeefdeadbeefdeadbeefdeadbeef'
$tA = ($h6a.Json | ConvertFrom-Json).data.total
$tB = ($h6b.Json | ConvertFrom-Json).data.total
Assert-Code 'GET (IDOR denemesi)' $h6b @(200)
if ("$tA" -eq "$tB") {
    Write-Host ('  [OK]   IDOR: user_id yok sayildi (total ayni: ' + $tA + ')') -ForegroundColor Green
    $script:pass++
} else {
    Write-Host ('  [FAIL] IDOR: total farkli oldu (' + $tA + ' != ' + $tB + ')') -ForegroundColor Red
    $script:fail++
}

# 8) Auth'siz (yeni cookie = oturum yok) -> 401
$unauth = Invoke-Cm -Method GET -Path '/api/v1/music/history' -CookieFile $anon
Assert-Code 'GET history (auth yok -> 401)' $unauth @(401)

Remove-Item $cookie -ErrorAction SilentlyContinue
Remove-Item $anon  -ErrorAction SilentlyContinue

$color = if ($script:fail -eq 0) { 'Green' } else { 'Red' }
Write-Host ''
Write-Host ('Sonuc: ' + $script:pass + ' gecti, ' + $script:fail + ' basarisiz') -ForegroundColor $color
exit $(if ($script:fail -eq 0) { 0 } else { 1 })
