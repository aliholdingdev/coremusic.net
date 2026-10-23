<#
.SYNOPSIS
    Adim 11 - Dropdown menu (rol bazli, DB baglantili) canli curl kaniti (server PC).

.DESCRIPTION
    Sunucu PC'de `git pull` sonrasi calistirilir. Kapsam: anonim menu bos |
    login sonrasi rol matrisi (user->2, admin->3 - rol sunucudan okunur) |
    route'lar gercek disk yuzeyiyle eslesir | yetkisiz ogede admin id'si yok.

.NOTES
    Test hesabi: CM_TEST_EMAIL / CM_TEST_PASSWORD ortam degiskenleri.
    Beklenen menu sayisi hesaplanirken rol yanit govdesinden okunur
    (Hard Rule 2: sunucu filtresi - istemciye yetkisiz oge gitmez).
#>
[CmdletBinding()]
param(
    [string]$BaseUrl   = 'https://api.coremusic.net',
    [string]$Email     = $env:CM_TEST_EMAIL,
    [string]$Password  = $env:CM_TEST_PASSWORD
)

$ErrorActionPreference = 'Stop'
$cookie = Join-Path $env:TEMP ('cm-step11-' + [guid]::NewGuid().ToString('N') + '.cookie')
$anon   = Join-Path $env:TEMP ('cm-step11-anon-' + [guid]::NewGuid().ToString('N') + '.cookie')
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
        $script:fail++
    }
}

function Assert-Ok {
    param([string]$Label, [bool]$Cond, [string]$Detail = '')
    if ($Cond) {
        Write-Host ('  [OK]   {0}{1}' -f $Label, $(if ($Detail) { ' -> ' + $Detail } else { '' })) -ForegroundColor Green
        $script:pass++
    } else {
        Write-Host ('  [FAIL] {0}{1}' -f $Label, $(if ($Detail) { ' -> ' + $Detail } else { '' })) -ForegroundColor Red
        $script:fail++
    }
}

if (-not $Email -or -not $Password) {
    Write-Error 'CM_TEST_EMAIL / CM_TEST_PASSWORD ortam degiskenleri gerekli (test hesabi).'
    exit 2
}

Write-Host ('Adim 11 curl kaniti - ' + $BaseUrl) -ForegroundColor Cyan
Write-Host ''

# 1) Anonim: menu bos (hata degil - oturumsuz dropdown'da menu ogesi yok)
$hAnon = Invoke-Cm -Method GET -Path '/api/v1/user/header-data' -CookieFile $anon
Assert-Code 'header-data (oturum yok)' $hAnon @(200)
$anonMenu = ($hAnon.Json | ConvertFrom-Json).data.menu
Assert-Ok 'anonim menu bos []' (@($anonMenu).Count -eq 0) ('adet=' + @($anonMenu).Count)

# 2) Login
$login = Invoke-Cm -Method POST -Path '/api/v1/auth/login' `
    -Body (@{ email = $Email; password = $Password } | ConvertTo-Json -Compress)
Assert-Code 'login' $login @(200)

# 3) Oturumlu menu + rol matrisi (rol yanittan okunur - Hard Rule 2 kaniti)
$h1 = Invoke-Cm -Method GET -Path '/api/v1/user/header-data'
Assert-Code 'header-data (oturumlu)' $h1 @(200)
$obj = $h1.Json | ConvertFrom-Json
$role = [string]$obj.data.user.role
$menu = @($obj.data.menu)
$expectCount = if ($role -eq 'admin') { 3 } else { 2 }
$ids = @($menu | ForEach-Object { [string]$_.id })

Assert-Ok ("rol okundu: " + $role) ($role -in @('admin', 'user')) $role
Assert-Ok ("menu sayisi = " + $expectCount + " (rol bazli)") ($menu.Count -eq $expectCount) ('gercek=' + $menu.Count)
Assert-Ok 'menu.id: settings + history herkeste' (($ids -contains 'settings') -and ($ids -contains 'history')) ($ids -join ',')

if ($role -eq 'admin') {
    Assert-Ok 'admin rolunde admin ogesi VAR' ($ids -contains 'admin') ($ids -join ',')
} else {
    Assert-Ok 'user rolunde admin ogesi YANITTA YOK (sunucu filtresi)' (-not ($ids -contains 'admin')) ($ids -join ',')
}

# 4) Route'lar gercek disk yuzeyiyle eslesir (header.php dropdown + admin alt domain)
$routes = @{}
foreach ($m in $menu) { $routes[[string]$m.id] = [string]$m.route }
Assert-Ok "route settings = /ayarlar" ($routes['settings'] -eq '/ayarlar') $routes['settings']
Assert-Ok "route history  = /history"  ($routes['history'] -eq '/history') $routes['history']
if ($routes.ContainsKey('admin')) {
    Assert-Ok 'route admin = https://admin.coremusic.net/' ($routes['admin'] -eq 'https://admin.coremusic.net/') $routes['admin']
}

# 5) Sema alanlari (id/label/route/roles) her ogede tam
$schemaOk = $true
foreach ($m in $menu) {
    if (-not $m.id -or -not $m.label -or -not $m.route -or ($null -eq $m.roles)) { $schemaOk = $false }
}
Assert-Ok 'sema: id/label/route/roles her ogede tam' $schemaOk

# 6) Auth'siz baska uc -> 401
$unauth = Invoke-Cm -Method GET -Path '/api/v1/music/recent?limit=5' -CookieFile $anon
Assert-Code 'GET recent (auth yok -> 401)' $unauth @(401)

Remove-Item $cookie -ErrorAction SilentlyContinue
Remove-Item $anon  -ErrorAction SilentlyContinue

$color = if ($script:fail -eq 0) { 'Green' } else { 'Red' }
Write-Host ''
Write-Host ('Sonuc: ' + $script:pass + ' gecti, ' + $script:fail + ' basarisiz') -ForegroundColor $color
exit $(if ($script:fail -eq 0) { 0 } else { 1 })
