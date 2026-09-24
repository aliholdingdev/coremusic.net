# ============================================================
# katman-sayim.ps1 — CoreMusic katman dugum sayim betigi
# ADR-026: sayim birimi = DUGUM · Kabul: kapasite >= 5000
# Kaynak: katman-sayim-rehberi.md §4 (baglayici tablo)
# ============================================================
param(
    [string]$AiRoot = "",
    [int]$HedefMinimum = 5000
)

# --- 0) Yol cozumu (betigin kendi koku; rehber §9 taslagindan bagimsiz) ---
# Betik: .ai\architecture\scripts\katman-sayim.ps1
#   .ai\architecture = Split-Path -Parent $PSScriptRoot
#   .ai (AiRoot)     = bir uste (Split-Path -Parent iki kez)
# Once $PSScriptRoot tabanli aday, bulunamazsa CWD tabanli fallback.
$Adaylar = @()
if (-not [string]::IsNullOrWhiteSpace($AiRoot)) { $Adaylar += $AiRoot }
if (-not [string]::IsNullOrWhiteSpace($PSScriptRoot)) {
    $Adaylar += (Split-Path -Parent (Split-Path -Parent $PSScriptRoot))  # PSScriptRoot tabanli: .ai
    $Adaylar += (Split-Path -Parent $PSScriptRoot)                       # aday .ai\architecture kendisi ise
}
$Cwd = (Get-Location).Path                                               # CWD fallback
$Adaylar += $Cwd
$Adaylar += (Join-Path $Cwd ".ai")
$Adaylar += (Split-Path -Parent $Cwd)

$KokBulundu = $null
foreach ($aday in $Adaylar) {
    if ([string]::IsNullOrWhiteSpace($aday)) { continue }
    # .ai root mu? (architecture\ altinda sayim rehberi var mi)
    if (Test-Path (Join-Path $aday "architecture\katman-sayim-rehberi.md")) { $KokBulundu = $aday; break }
    # aday .ai\architecture kendisi mi? (rehber dogrudan icinde)
    if (Test-Path (Join-Path $aday "katman-sayim-rehberi.md")) { $KokBulundu = (Split-Path -Parent $aday); break }
}
if ($null -eq $KokBulundu) {
    Write-Host "HATA: .ai root bulunamadi (katman-sayim-rehberi.md yok) - PSScriptRoot ve CWD adaylari denendi."
    exit 1
}
$AiRoot = $KokBulundu
$ArchRoot = Join-Path $AiRoot "architecture"

# --- §4 baglayici tablo (a, b, tavan, baglayici satir toplami) ---
$Katmanlar = @(
    [pscustomobject]@{ K="K0";  Dizin="k0-isletim-sistemi";  Alt=10; Orta=7; Tavan=210; Baglayici=280 },
    [pscustomobject]@{ K="K1";  Dizin="k1-donanim";          Alt=12; Orta=7; Tavan=360; Baglayici=427 },
    [pscustomobject]@{ K="K2";  Dizin="k2-surucu";           Alt=8;  Orta=6; Tavan=170; Baglayici=218 },
    [pscustomobject]@{ K="K3";  Dizin="k3-ses-motoru";       Alt=9;  Orta=6; Tavan=200; Baglayici=254 },
    [pscustomobject]@{ K="K4";  Dizin="k4-yapay-zeka";       Alt=8;  Orta=5; Tavan=130; Baglayici=170 },
    [pscustomobject]@{ K="K5";  Dizin="k5-veri-yonetimi";    Alt=10; Orta=7; Tavan=300; Baglayici=370 },
    [pscustomobject]@{ K="K6";  Dizin="k6-guvenlik";         Alt=9;  Orta=5; Tavan=150; Baglayici=195 },
    [pscustomobject]@{ K="K7";  Dizin="k7-middleware";       Alt=10; Orta=5; Tavan=140; Baglayici=190 },
    [pscustomobject]@{ K="K8";  Dizin="k8-servis";           Alt=10; Orta=5; Tavan=150; Baglayici=200 },
    [pscustomobject]@{ K="K9";  Dizin="k9-api-routing";      Alt=9;  Orta=4; Tavan=90;  Baglayici=126 },
    [pscustomobject]@{ K="K10"; Dizin="k10-uygulama";        Alt=10; Orta=6; Tavan=200; Baglayici=260 },
    [pscustomobject]@{ K="K11"; Dizin="k11-ux";              Alt=11; Orta=7; Tavan=330; Baglayici=407 },
    [pscustomobject]@{ K="K12"; Dizin="k12-izleme";          Alt=8;  Orta=5; Tavan=120; Baglayici=160 },
    [pscustomobject]@{ K="K13"; Dizin="k13-cicd";            Alt=7;  Orta=5; Tavan=90;  Baglayici=95  },
    [pscustomobject]@{ K="K14"; Dizin="k14-ag";              Alt=9;  Orta=6; Tavan=170; Baglayici=224 },
    [pscustomobject]@{ K="K15"; Dizin="k15-medya-streaming"; Alt=9;  Orta=6; Tavan=170; Baglayici=224 },
    [pscustomobject]@{ K="K16"; Dizin="k16-class-ab";        Alt=11; Orta=7; Tavan=280; Baglayici=357 },
    [pscustomobject]@{ K="K17"; Dizin="k17-guc-kaynagi";     Alt=9;  Orta=6; Tavan=190; Baglayici=244 },
    [pscustomobject]@{ K="K18"; Dizin="k18-termal";          Alt=8;  Orta=5; Tavan=130; Baglayici=170 },
    [pscustomobject]@{ K="K19"; Dizin="k19-pcb";             Alt=8;  Orta=5; Tavan=120; Baglayici=160 },
    [pscustomobject]@{ K="K20"; Dizin="k20-bom";             Alt=8;  Orta=5; Tavan=120; Baglayici=160 }
)

# --- 1) Sutun toplamlari (§5 denetimi) ---
$BaglayiciToplam = ($Katmanlar | Measure-Object -Property Baglayici -Sum).Sum   # 4891
$FormulToplam = 0
foreach ($satir in $Katmanlar) {
    $FormulToplam += ($satir.Alt * $satir.Orta) + $satir.Tavan                  # 4938
}
$IkinciSeviye = ($Katmanlar | Measure-Object -Property Alt -Sum).Sum           # 193
$Kok = $Katmanlar.Count                                                         # 21

$KapasiteBaglayici = $BaglayiciToplam + $Kok + $IkinciSeviye                    # 5105
$KapasiteFormul = $FormulToplam + $Kok + $IkinciSeviye                          # 5152

# --- 2) Disk kaniti (tur i): k* klasorlerindeki .md dosyalari ---
# Rehber §7 yontemi: "glob (k*/*.md), yalniz .ai/architecture/ altindaki birinci
# seviye klasorler" → recursive DEGIL; klasor kokundeki .md dosyalari sayilir.
$DiskToplam = 0
$Satirlar = @()
foreach ($satir in $Katmanlar) {
    $yol = Join-Path $ArchRoot $satir.Dizin
    $adet = 0
    if (Test-Path $yol) {
        $adet = @(Get-ChildItem -Path $yol -Filter *.md -File -ErrorAction SilentlyContinue).Count
    }
    $DiskToplam += $adet
    $Satirlar += [pscustomobject]@{
        K = $satir.K; Dizin = $satir.Dizin; DiskMd = $adet
        KapasiteBaglayici = $satir.Baglayici
        KapasiteFormul = ($satir.Alt * $satir.Orta) + $satir.Tavan
    }
}

# --- 3) ADR-024 kontrol noktalari (firmware = 8, k-surucu = 0/yok) ---
$firmwareYol = Join-Path $ArchRoot "firmware"
$firmwareAdet = 0
if (Test-Path $firmwareYol) {
    $firmwareAdet = @(Get-ChildItem -Path $firmwareYol -Filter *.md -File -ErrorAction SilentlyContinue).Count
}
$kSurucuYol = Join-Path $ArchRoot "k-surucu"
$kSurucuVar = Test-Path $kSurucuYol
$kSurucuAdet = 0
if ($kSurucuVar) {
    $kSurucuAdet = @(Get-ChildItem -Path $kSurucuYol -Recurse -File -ErrorAction SilentlyContinue).Count
}

# --- 4) Kanit turu taramasi (§9): K desenli dugum adaylari ---
$desen = "K\d{1,2}(\.\d{1,2}){1,3}"
$tumMd = @(Get-ChildItem -Path $ArchRoot -Recurse -Filter *.md -File -ErrorAction SilentlyContinue)
$dugumKesisim = 0
foreach ($dosya in $tumMd) {
    $eslesen = Select-String -Path $dosya.FullName -Pattern $desen -AllMatches -ErrorAction SilentlyContinue
    if ($null -ne $eslesen) {
        foreach ($e in @($eslesen)) { $dugumKesisim += $e.Matches.Count }
    }
}

# --- 5) Rapor ---
$Satirlar | Format-Table -AutoSize
Write-Host ("Kok katman sayisi            : " + $Kok)
Write-Host ("2. seviye (a toplami)         : " + $IkinciSeviye)
Write-Host ("3.-4. seviye (baglayici)      : " + $BaglayiciToplam)
Write-Host ("3.-4. seviye (formul)         : " + $FormulToplam)
Write-Host ("Kapasite (baglayici tablo)    : " + $KapasiteBaglayici)
Write-Host ("Kapasite (formul)             : " + $KapasiteFormul)
Write-Host ("Senaryolar                    : alt 5152 / orta 5400 / ust 6200")
Write-Host ("Disk .md (K0-K20)             : " + $DiskToplam)
Write-Host ("Disk .md firmware (beklenen 8): " + $firmwareAdet)
if ($kSurucuVar) {
    Write-Host ("Disk .md k-surucu (beklenen 0): " + $kSurucuAdet)
} else {
    # klasor yok = dogru durum (ADR-024); [char] ile UTF-8 mojibake riskine karsi
    Write-Host ("Disk .md k-surucu (beklenen 0): yok (ADR-024 ile k2-surucu'ya tasindi)")
}
Write-Host ("K desenli eslesme (kanit taramasi, bilgi): " + $dugumKesisim)

# --- 6) Kabul kriteri: iki kapasitenin MINKIMUMU >= 5000 ---
$enDusuk = [Math]::Min($KapasiteBaglayici, $KapasiteFormul)
if ($enDusuk -ge $HedefMinimum) {
    Write-Host ("KABUL: kapasite " + $enDusuk + " >= " + $HedefMinimum)
    if ($firmwareAdet -ne 8) { Write-Host "UYARI: firmware sayisi 8 degil (ADR-024)" }
    if ($kSurucuAdet -ne 0) { Write-Host "UYARI: k-surucu bos degil (ADR-024)" }
    exit 0
} else {
    Write-Host ("RED: kapasite " + $enDusuk + " < " + $HedefMinimum)
    exit 1
}
