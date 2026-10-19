#Requires -Version 5.1
<#
  figma-extract.ps1 — CoreMusic Figma tam çekim betiği (15 sayfa + 12 node + PNG)
  Yeniden çalıştırılabilir. Token yalnız .ai/.env.figma içinden okunur.
  Çıktılar: .ai/ui-design/reference/figma/{raw, png, extracted-*.md}
#>
param(
    [switch]$PagesOnly,     # sadece 15 sayfa ham JSON
    [switch]$NodesOnly,     # sadece 12 verilen node
    [switch]$ImagesOnly,    # sadece PNG export
    [int]$Scale = 2
)
$ErrorActionPreference = 'Stop'
[Console]::OutputEncoding = [Text.Encoding]::UTF8

$ROOT   = 'C:\www\coremusic.net\.ai'
$FIG    = "$ROOT\ui-design\reference\figma"
$RAW    = "$FIG\raw"
$PNG    = "$FIG\png"
$ENVF   = "$ROOT\.env.figma"
$LOGF   = "$FIG\_extraction-notes.md"

New-Item -ItemType Directory -Force -Path $RAW, $PNG | Out-Null

# --- token + file key (SSOT: yalniz .ai/.env.figma; betik icinde sabit anahtar YOK) ---
if (-not (Test-Path $ENVF)) { throw "ENV YOK: $ENVF" }
$ENVRAW = Get-Content -Raw $ENVF -Encoding UTF8
$T  = ([regex]::Match($ENVRAW, 'FIGMA_TOKEN=(\S+)')).Groups[1].Value
$KEY = ([regex]::Match($ENVRAW, 'FIGMA_FILE_KEY=(\S+)')).Groups[1].Value
if (-not $T)   { throw 'FIGMA_TOKEN bos' }
if (-not $KEY) { throw 'FIGMA_FILE_KEY bos' }
$H = @{ 'X-Figma-Token' = $T }

# --- 15 sayfa (dogrulanmis, 2026-09-29) ---
$PAGES = @(
    @{ id='1991:12056'; name='Plan';                   bp='-'      },
    @{ id='1047:15802'; name='Linux-Pi';               bp='1024'   },
    @{ id='462:5874';   name='Linux-1920';             bp='1920'   },
    @{ id='2161:12438'; name='Music-Admin';            bp='1920'   },
    @{ id='2135:19832'; name='Linux-Pi-Sonn-Kullanici';bp='1024'   },
    @{ id='2003:24752'; name='Mobil-Sonn-Kullanici';   bp='mobile' },
    @{ id='1988:14156'; name='Kurumsal';               bp='1920'   },
    @{ id='319:2789';   name='Web-Laptop-1920';        bp='1920'   },
    @{ id='326:3386';   name='Web-Monitor-3840';       bp='3840'   },
    @{ id='608:11052';  name='Web-Sonn-Kullanici-1920';bp='1920'   },
    @{ id='16:106';     name='Tizen-OS-Samsung';       bp='tv'     },
    @{ id='1801:12472'; name='Windows-CPP-App';        bp='1920'   },
    @{ id='1801:12473'; name='Windows-CPP-Fullscreen'; bp='1920'   },
    @{ id='15:403';     name='Web-Design-Eski';        bp='1920'   },
    @{ id='18:2907';    name='Design-System-XD';       bp='system' }
)

# --- 12 kullanici node + design system ---
$NODES = @(
    '1639:10160','1646:17727','1639:9775','1639:9773','1639:9904','1639:9892','1639:9910',
    '2831:10267','2831:13747','2849:21489','2850:21494','18:2907'
)

$stamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
$notes = @()

function Get-Json($url) {
    $r = Invoke-WebRequest -Uri $url -Headers $H -UseBasicParsing -TimeoutSec 600
    return $r.Content
}
function Save-Utf8($path, $text) {
    $enc = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($path, $text, $enc)
}
function Slug($s) { ($s -replace '[^A-Za-z0-9\-]+','-').Trim('-') }

function Count-Nodes($n) {
    $c = 0
    if ($n.children) { foreach ($ch in $n.children) { $c += 1 + (Count-Nodes $ch) } }
    return $c
}

# =========================================================
# 1) SAYFALAR — ham JSON
# =========================================================
if (-not $NodesOnly -and -not $ImagesOnly) {
    foreach ($p in $PAGES) {
        $out = "$RAW\page-$($p.id -replace ':','-').json"
        try {
            $c = Get-Json ("https://api.figma.com/v1/files/$KEY/nodes?ids=" + $p.id)
            Save-Utf8 $out $c
            $j = $c | ConvertFrom-Json
            $doc = $j.nodes.($p.id).document
            $top = ($doc.children | Measure-Object).Count
            $total = Count-Nodes $doc
            $bytes = (Get-Item $out).Length
            $status = if ($top -eq 0) { 'BOS (tasarim yok)' } else { 'OK' }
            $line = "| ``$($p.id)`` | $($p.name) | bp=$($p.bp) | top=$top | toplam node=$total | $([math]::Round($bytes/1KB)) KB | $status |"
            $notes += $line
            Write-Host $line
        } catch {
            $notes += "| ``$($p.id)`` | $($p.name) | HATA: $($_.Exception.Message) |"
            Write-Host "HATA $($p.id): $($_.Exception.Message)" -ForegroundColor Red
        }
    }
}

# =========================================================
# 2) 12 NODE — ham JSON
# =========================================================
if (-not $PagesOnly -and -not $ImagesOnly) {
    $out = "$RAW\nodes-user-12.json"
    try {
        $c = Get-Json ("https://api.figma.com/v1/files/$KEY/nodes?ids=" + ($NODES -join ','))
        Save-Utf8 $out $c
        $bytes = (Get-Item $out).Length
        $notes += "| ``12 node`` | kullanici linkleri | - | - | - | $([math]::Round($bytes/1KB)) KB | OK |"
        Write-Host "nodes-user-12.json: $([math]::Round($bytes/1KB)) KB"
    } catch { Write-Host "NODE HATA: $($_.Exception.Message)" -ForegroundColor Red }
}

# =========================================================
# 3) PNG EXPORT (top-level frame bazli, scale parametresi)
# =========================================================
if (-not $PagesOnly -and -not $NodesOnly) {
    # hedefler: kullanici 11 node (18:2907 CANVAS disinda) + sayfa top-frame'leri
    $targets = @()
    $targets += $NODES | Where-Object { $_ -ne '18:2907' }
    $j12 = (Get-Json ("https://api.figma.com/v1/files/$KEY/nodes?ids=" + ($targets -join ','))) | ConvertFrom-Json
    $idName = @{}
    foreach ($k in $j12.nodes.PSObject.Properties.Name) {
        $d = $j12.nodes.$k.document
        $idName[$k] = $d.name
    }
    # sayfa top-level frame'leri (bos sayfalar atlanir)
    foreach ($p in $PAGES) {
        $f = "$RAW\page-$($p.id -replace ':','-').json"
        if (-not (Test-Path $f)) { continue }
        $j = (Get-Content -Raw -Encoding UTF8 $f | ConvertFrom-Json)
        $doc = $j.nodes.($p.id).document
        foreach ($ch in $doc.children) {
            if ($ch.type -eq 'FRAME' -or $ch.type -eq 'COMPONENT' -or $ch.type -eq 'INSTANCE' -or $ch.type -eq 'GROUP') {
                if (-not $idName.ContainsKey($ch.id)) { $idName[$ch.id] = $ch.name }
                $targets += $ch.id
            }
        }
    }
    $targets = $targets | Select-Object -Unique
    Write-Host "PNG hedef sayisi: $($targets.Count)"

    # 20'lik parti halinde images istegi (URL 400 riskini onler)
    for ($i = 0; $i -lt $targets.Count; $i += 20) {
        $part = $targets[$i..([Math]::Min($i+19, $targets.Count-1))]
        $url = "https://api.figma.com/v1/images/$KEY" + "?ids=" + ($part -join ',') + "&format=png&scale=$Scale"
        try {
            $jr = (Get-Json $url) | ConvertFrom-Json
            if ($jr.err) { Write-Host "images err: $($jr.err)" -ForegroundColor Red; continue }
            foreach ($k in $jr.images.PSObject.Properties.Name) {
                $u = $jr.images.$k
                if (-not $u) { continue }
                $nm = Slug($idName[$k])
                if (-not $nm) { $nm = ($k -replace ':','-') }
                $out = "$PNG\$($k -replace ':','-')-$nm.png"
                if (Test-Path $out) { continue }
                try {
                    Invoke-WebRequest -Uri $u -OutFile $out -UseBasicParsing -TimeoutSec 300
                } catch { Write-Host "PNG inilemedi $k : $($_.Exception.Message)" -ForegroundColor Yellow }
            }
        } catch { Write-Host "images istek hatasi [$i]: $($_.Exception.Message)" -ForegroundColor Red }
        Start-Sleep -Milliseconds 400
    }
    $pngCount = (Get-ChildItem $PNG -Filter *.png -ErrorAction SilentlyContinue | Measure-Object).Count
    $notes += "| PNG | $($targets.Count) hedef | scale=$Scale | - | - | - | $pngCount dosya indirildi |"
    Write-Host "Toplam PNG: $pngCount"
}

# =========================================================
# 4) NOT EKLE
# =========================================================
if ($notes.Count -gt 0) {
    $block = "`n---`n`n## Extract: tam cekim ($stamp)`n`n| Node | Ad | Breakpoint | Top | Toplam | Boyut | Durum |`n|---|---|---|---|---|---|---|`n" + ($notes -join "`n") + "`n"
    $enc = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::AppendAllText($LOGF, $block, $enc)
    Write-Host "`n_notlara eklendi: $LOGF"
}
Write-Host "BITTI" -ForegroundColor Green
