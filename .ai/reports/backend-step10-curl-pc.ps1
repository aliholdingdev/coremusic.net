<#
.SYNOPSIS
    Adim 10 - Header DB baglantisi (hibrit) canli curl kaniti (server PC).

.DESCRIPTION
    Sunucu PC'de `git pull` sonrasi calistirilir. MySQL bu makinede aktif ->
    gercek repo + gercek HTTP. Kapsam: anonim header ozeti (200, bos) |
    login sonrasi uc veri blogu (profil+rol, playlist, bildirim) |
    CSRF'siz POST -> 403 kaniti | auth'siz 401.

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
$cookie = Join-Path $env:TEMP ('cm-step10-' + [guid]::NewGuid().ToString('N') + '.cookie')
$anon   = Join-Path $env:TEMP ('cm-step10-anon-' + [guid]::NewGuid().ToString('N') + '.cookie')
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

function Assert-NotNull {
    param([string]$Label, $Res, [string]$JsonPath)
    try {
        $obj = $Res.Json | ConvertFrom-Json
        $actual = $obj
        foreach ($seg in $JsonPath.Split('.')) { $actual = $actual.$seg }
        if ($null -ne $actual -and "$actual" -ne '') {
            Write-Host ('  [OK]   {0} -> {1} = {2}' -f $Label, $JsonPath, $actual) -ForegroundColor Green
            $script:pass++
        } else {
            Write-Host ('  [FAIL] {0} -> {1} bos/null' -f $Label, $JsonPath) -ForegroundColor Red
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

Write-Host ('Adim 10 curl kaniti - ' + $BaseUrl) -ForegroundColor Cyan
Write-Host ''

# 1) LOGIN/LOGOUT SENARYOSU A - oturumsuz header (anonim ozet, hata degil)
$hAnon = Invoke-Cm -Method GET -Path '/api/v1/user/header-data' -CookieFile $anon
Assert-Code 'header-data (oturum yok -> 200 anonim ozet)' $hAnon @(200)
Assert-Field 'anonim is_authenticated' $hAnon 'data.is_authenticated' 'False'
Assert-Field 'anonim user null'        $hAnon 'data.user'             ''
Assert-Field 'anonim playlist bos'     $hAnon 'data.playlist_shortcuts.Count' 0
Assert-Field 'anonim bildirim 0'       $hAnon 'data.unread_notifications' 0

# 2) Login -> session cookie
$login = Invoke-Cm -Method POST -Path '/api/v1/auth/login' `
    -Body (@{ email = $Email; password = $Password } | ConvertTo-Json -Compress)
Assert-Code 'login' $login @(200)

# 3) LOGIN/LOGOUT SENARYOSU B - oturumlu header: UC VERI BLOGU
$h1 = Invoke-Cm -Method GET -Path '/api/v1/user/header-data'
Assert-Code 'header-data (oturumlu)' $h1 @(200)
Assert-Field 'is_authenticated'  $h1 'data.is_authenticated' 'True'
Assert-NotNull 'blog 1: profil adi' $h1 'data.user.username'
Assert-NotNull 'blog 1: rol'        $h1 'data.user.role'
$pls = ($h1.Json | ConvertFrom-Json).data.playlist_shortcuts
if ($null -ne $pls) {
    Write-Host ('  [OK]   blog 2: playlist_shortcuts dizisi (' + @($pls).Count + ' kayit)') -ForegroundColor Green
    $script:pass++
} else {
    Write-Host '  [FAIL] blog 2: playlist_shortcuts yok' -ForegroundColor Red
    $script:fail++
}
$unread = ($h1.Json | ConvertFrom-Json).data.unread_notifications
if ($null -ne $unread -and ("$unread" -match '^\d+$')) {
    Write-Host ('  [OK]   blog 3: unread_notifications int = ' + $unread) -ForegroundColor Green
    $script:pass++
} else {
    Write-Host '  [FAIL] blog 3: unread_notifications yok/tip hatali' -ForegroundColor Red
    $script:fail++
}

# 4) CSRF AKISI (Hard Rule 2 / 4): csrf'siz state-changing POST -> 403
$noCsrf = Invoke-Cm -Method POST -Path '/api/v1/music/history' `
    -Body (@{ music_id = '018f3a5b6c7d8e9f0123456789abcdef'; duration_sec = 10 } | ConvertTo-Json -Compress)
Assert-Code "POST (csrf'siz -> 403)" $noCsrf @(403)

# 5) Auth'siz baska uc -> 401 (session kontrolu)
$unauth = Invoke-Cm -Method GET -Path '/api/v1/music/recent?limit=5' -CookieFile $anon
Assert-Code 'GET recent (auth yok -> 401)' $unauth @(401)

Remove-Item $cookie -ErrorAction SilentlyContinue
Remove-Item $anon  -ErrorAction SilentlyContinue

$color = if ($script:fail -eq 0) { 'Green' } else { 'Red' }
Write-Host ''
Write-Host ('Sonuc: ' + $script:pass + ' gecti, ' + $script:fail + ' basarisiz') -ForegroundColor $color
exit $(if ($script:fail -eq 0) { 0 } else { 1 })
