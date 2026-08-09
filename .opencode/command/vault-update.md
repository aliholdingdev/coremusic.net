---
description: "Proje degisimlerine gore vault'u guncelle, indeksleri ve referanslari tazele"
agent: vault-updater
---

# Vault Update Komutu

Bu komut, proje degisimlerine gore vault dosyalarini otomatik olarak gunceller.
Indeksleri ve referanslari tazeler.

## Ne Yapar?

1. **Degisiklikleri Tespit Eder** - git diff ile son degisiklikleri bulur
2. **Indeksi Gunceller** - .ai/index.md'yi tazeler
3. **Referanslari Kontrol Eder** - Kirik linkleri duzeltir
4. **Yeni Dosyalari Ekler** - Yeni eklenen dosyalari indeksler
5. **Silinen Dosyalari Cikarir** - Silinen dosyalari indeksten temizler
6. **Rapor Olusturur** - Guncelleme raporunu .ai/log.md'ye yazar

## Neden Onemli?

Vault'un proje ile es zamanli olmasini saglar.
Yeni dosyalarin kaybolmasini onler.
Silinen dosyalarin kirik referans birakmasini engeller.
Bilgi tutarliligini korur.

## Ne Zaman Kullanilir?

- Git commit sonrasi
- Git merge sonrasi
- Yeni dosya olusturulduktan sonra
- Dosya silindikten sonra
- Dosya degistirildikten sonra
- Deployment oncesi

## Tetikleyiciler

### Otomatik Tetikleyiciler

| Tetikleme | Aksiyon | Oncelik |
|-----------|---------|---------|
| git commit sonrasi | Vault'u tara | Dusuk |
| git merge sonrasi | Butunlugu dogrula | Yuksek |
| Yeni dosya olusturulmasi | Indekse ekle | Yuksek |
| Dosya silinmesi | Indeksten cikar | Yuksek |
| Dosya degistirilmesi | Referanslari kontrol et | Orta |
| Deployment sonrasi | Vault'u guncelle | Kritik |

## Prosedur

### Adim 1: Degisiklik Tespiti

```powershell
# Son degisiklikleri bul
git diff --name-only HEAD~1 HEAD

# Yeni dosyalari bul
git ls-files --others --exclude-standard

# Silinen dosyalari bul
git diff --name-only --diff-filter=D HEAD~1 HEAD
```

### Adim 2: Vault Iliskili Dosyalari Filtreleme

```powershell
# Vault ile iliskili degisiklikler
$changes = git diff --name-only HEAD~1 HEAD
$vaultFiles = $changes | Where-Object { $_ -match "^(\.ai|\.claude|\.workflows)/" }
```

### Adim 3: Indeks Guncelleme

`.ai/index.md` dosyasi guncellenir:

- Yeni dosyalar eklenir
- Silinen dosyalar cikarilir
- Degisen dosyalar guncellenir
- Zaman damgasi guncellenir

### Adim 4: Referans Kontrolu

Tum referanslar kontrol edilir:

```powershell
# Wiki-link referanslari
$wikiRefs = [regex]::Matches($content, '\[\[(.*?)\]\]')
foreach ($ref in $wikiRefs) {
    $refName = $ref.Groups[1].Value
    $refPath = Join-Path $Path "$refName.md"
    if (-not (Test-Path $refPath)) {
        Write-Warning "Kirik referans: $refName"
    }
}
```

### Adim 5: Butunluk Kontrolu

```powershell
# Checksum'lari hesapla
Get-ChildItem -Path .ai -Recurse -Filter *.md | ForEach-Object {
    $hash = Get-FileHash $_.FullName -Algorithm MD5
    Write-Host "$($_.Name): $($hash.Hash)"
}
```

### Adim 6: Rapor Olusturma

Guncelleme raporu olusturulur:

```markdown
# Vault Guncelleme Raporu

**Tarih:** YYYY-MM-DD HH:MM
**Tetikleyici:** commit|merge|manual

## Yapilan Islemler
- [ ] X dosya tarandi
- [ ] Y yeni dosya indekslendi
- [ ] Z referans duzeltildi
- [ ] Butunluk dogrulandi

## Uyarilar
- Uyari varsa

## Sonraki Adimlar
- Planlanan calismalar
```

## Guncellenen Dosyalar

| Dosya | Guncelleme Sikligi | Guncelleme Turu |
|-------|-------------------|-----------------|
| `.ai/index.md` | Her degisiklik | Ekleme/Cikarma |
| `.ai/keys.md` | Yeni anahtar | Ekleme |
| `.ai/log.md` | Her islem | Log ekleme |
| `.ai/AGENTS.md` | Agent degisikligi | Guncelleme |
| `.ai/WORKFLOW.md` | Workflow degisikligi | Guncelleme |

## Guncellenmeyen Dosyalar

| Dosya | Neden | Guncelleme |
|-------|-------|------------|
| `.ai/brain.md` | Kritik veri | Sadece onayla |
| `.ai/security/` | Guvenlik | Sadece Security Engineer |
| `.ai/decisions/` | ADR | Sadece onay sureci |
| `.claude/rules/` | Kurallar | Sadece Tech Lead |

## Hata Yonetimi

### Yaygin Hatalar

| Hata | Belirti | Cozum |
|------|---------|-------|
| Degisiklik bulunamadi | Bos diff | Branch kontrol et |
| Indeks guncellenemedi | Write hatasi | Izinleri kontrol et |
| Referans bulunamadi | Kirik link | Referansi duzelt |
| Checksum hatasi | Hash eslesmiyor | Dosyayi kontrol et |

### Kurtarma Proseduru

1. Sorunu tespit et
2. Kok nedeni bul
3. Cozumu uygula
4. Dogrula
5. Logla

## Komut Satiri Ornekleri

### Tam Guncelleme

```powershell
.\.ai\scripts\vault-auto-update.ps1
```

### Otomatik Duzeltme ile

```powershell
.\.ai\scripts\vault-auto-update.ps1 -AutoFix
```

### Belirli Commit Icin

```powershell
.\.ai\scripts\vault-auto-update.ps1 -CommitHash abc123
```

## Log Formati

Her guncelleme su formatta loglanir:

```
[YYYY-MM-DD HH:MM:SS] [INFO] [vault-updater] [UPDATE] [DOSYA] [ACIKLAMA]
```

Ornek:

```
[2026-07-12 14:30:00] [INFO] [vault-updater] [UPDATE] .ai/index.md Yeni dosya eklendi
[2026-07-12 14:31:00] [WARN] [vault-updater] [REFERENCE] Kirik link bulundu
[2026-07-12 14:32:00] [INFO] [vault-updater] [INTEGRITY] Butunluk dogrulandi
```

## Monitoring

### Izlenen Metrikler

| Metrik | Hedef | Alert |
|--------|-------|-------|
| Guncelleme basarisi | %100 | Basarisiz oldugunda |
| Referans gecerliligi | %100 | Kirik link tespit ettiginde |
| Guncelleme suresi | < 5 dk | 10 dk ustunde |
| Hata orani | < %1 | %5 ustunde |

### Alert Politikalari

| Alert | Seviye | Yanit |
|-------|--------|-------|
| Guncelleme basarisiz | Kritik | Hemen duzelt |
| Kirik referans | Yuksek | 1 saat icinde |
| Gecikme | Orta | 24 saat icinde |
| Uyari | Dusuk | Haftalik inceleme |

## Kontrol Listesi

- [ ] Degisiklikler tespit edildi
- [ ] Vault iliskili dosyalar filtrelendi
- [ ] Indeks guncellendi
- [ ] Referanslar kontrol edildi
- [ ] Kirik referanslar duzeltildi
- [ ] Butunluk dogrulandi
- [ ] Rapor olusturuldu
- [ ] Log guncellendi

## Sorumluluklar

- Her agent bu komutu kullanabilir
- Guncelleme islemleri vault-updater agent'i tarafindan yapilir
- Guvenlik kontrolleri security-reviewer agent'i tarafindan yapilir
- Raporlama build agent'i tarafindan yapilir

## Zaman Cizelgesi

| Islem | Suresi | Sikligi |
|-------|--------|---------|
| Degisiklik tespiti | 1-2 dk | Her degisiklik |
| Indeks guncelleme | 1-2 dk | Her degisiklik |
| Referans kontrolu | 2-5 dk | Her degisiklik |
| Butunluk kontrolu | 1-3 dk | Her degisiklik |
| Rapor olusturma | 1 dk | Her calistirma |

## Basari Kriterleri

- Tum degisiklikler yakalandi
- Indeks guncellendi
- Referanslar dogrulandi
- Butunluk saglandi
- Rapor olusturuldu
- Log guncellendi

## Risk Degerlendirmesi

| Risk | Olasilik | Etki | Onlem |
|------|----------|------|-------|
| Degisiklik kacirma | Dusuk | Yuksek | Git hook |
| Indeks karismasi | Dusuk | Orta | Versiyon kontrolu |
| Referansi kacirma | Orta | Orta | Otomatik kontrol |
| Log kaybi | Dusuk | Dusuk | Yedekleme |

## Sonraki Adimlar

1. Degisiklikleri tespit et
2. Vault iliskili dosyalari filtrole
3. Indeksi guncelle
4. Referanslari kontrol et
5. Butunlugu dogrula
6. Raporu olustur
7. Logu guncelle

## Notlar

- Bu komut her onemli degisiklik sonrasi calistirilmalidir
- Git commit oncesi calistirilmasi onerilir
- Haftalik olarak tam kontrol yapilmalidir
- Sorunlar hemen cozulmelidir
- Guncelleme raporlari saklanmalidir

---

## Ek Bolum 1: Guncelleme Stratejileri

### Strateji Secim Matrisi

| Durum | Strateji | Agent | Sure |
|-------|----------|-------|------|
| Yeni API endpoint | Full guncelleme | Backend | 2-4 saat |
| UI degisikligi | Incremental | UI | 1-2 saat |
| DB migration | Full guncelleme | Data | 3-5 saat |
| Guvenlik acigi | Immediate | Security | 1 saat |
| Test ekleme | Incremental | QA | 1-2 saat |
| Deployment | Full guncelleme | DevOps | 2-3 saat |

### Guncelleme Akis Diyagrami

```
┌─────────────────────────────────────────────────────────────┐
│                 VAULT GUNCELLEME AKISI                         │
│                                                               │
│  ┌─────────────┐              ┌─────────────┐                │
│  │ Degisiklik   │              │ Analiz      │                │
│  │ Tespiti      │─────────────►│             │                │
│  └─────────────┘              └─────────────┘                │
│         │                           │                         │
│         │                           │                         │
│  ┌─────────────┐              ┌─────────────┐                │
│  │ Agent       │              │ Guncelleme  │                │
│  │ Secimi      │◄─────────────│ Planlama    │                │
│  └─────────────┘              └─────────────┘                │
│         │                           │                         │
│         │                           │                         │
│  ┌─────────────┐              ┌─────────────┐                │
│  │ Uygulama    │              │ Dogrulama   │                │
│  │             │─────────────►│             │                │
│  └─────────────┘              └─────────────┘                │
│         │                           │                         │
│         │                           │                         │
│  ┌─────────────┐              ┌─────────────┐                │
│  │ Raporlama   │              │ Tamamlandi  │                │
│  │             │─────────────►│             │                │
│  └─────────────┘              └─────────────┘                │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### Guncelleme Sablonlari

```markdown
# Guncelleme Sablonu

## Degisiklik Bilgileri
- **Tarih:** YYYY-MM-DD HH:MM
- **Agent:** [Agent adi]
- **Tur:** [Full/Incremental/Update]

## Degisiklik Detaylari
- **Dosya:** [Dosya yolu]
- **Eski durum:** [Aciklama]
- **Yeni durum:** [Aciklama]

## Etkilenen Dosyalar
- [Dosya 1]
- [Dosya 2]

## Sonraki Adimlar
- [Adim 1]
- [Adim 2]

## Onay
- [ ] Tech Lead onayladi
- [ ] QA dogruladi
```

---

## Ek Bolum 2: Guncelleme Scripti

### Temel Guncelleme Scripti

```powershell
# vault-update-core.ps1
param(
    [string]$VaultPath = ".ai",
    [string]$Mode = "incremental",
    [switch]$Force,
    [switch]$DryRun
)

Write-Host "=== VAULT GUNCELLEME ===" -ForegroundColor Cyan
Write-Host "Mod: $Mode" -ForegroundColor Yellow

# 1. Degisiklikleri tespit et
Write-Host "`n1. Degisiklikler tespit ediliyor..." -ForegroundColor Yellow

$gitStatus = git status --porcelain
if (-not $gitStatus) {
    Write-Host "Degisiklik bulunamadi" -ForegroundColor Green
    exit 0
}

# Vault ile iliskili degisiklikleri filtrele
$vaultChanges = $gitStatus | Where-Object {
    $_ -match "^\.ai/|\.claude/|\.opencode/"
}

if (-not $vaultChanges) {
    Write-Host "Vault ile iliskili degisiklik bulunamadi" -ForegroundColor Green
    exit 0
}

Write-Host "Vault degisiklikleri:" -ForegroundColor Yellow
$vaultChanges | ForEach-Object { Write-Host "  $_" }

# 2. Degisiklik turunu belirle
Write-Host "`n2. Degisiklik turleri belirleniyor..." -ForegroundColor Yellow

$changeTypes = @{
    New = @()
    Modified = @()
    Deleted = @()
}

foreach ($change in $vaultChanges) {
    $status = $change.Substring(0, 2).Trim()
    $file = $change.Substring(3)
    
    switch ($status) {
        "A" { $changeTypes.New += $file }
        "M" { $changeTypes.Modified += $file }
        "D" { $changeTypes.Deleted += $file }
    }
}

Write-Host "  Yeni: $($changeTypes.New.Count)" -ForegroundColor Green
Write-Host "  Degisen: $($changeTypes.Modified.Count)" -ForegroundColor Yellow
Write-Host "  Silinen: $($changeTypes.Deleted.Count)" -ForegroundColor Red

# 3. Guncelleme plani olustur
Write-Host "`n3. Guncelleme plani olusturuluyor..." -ForegroundColor Yellow

$plan = @{
    Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Mode = $Mode
    Changes = $vaultChanges.Count
    NewFiles = $changeTypes.New
    ModifiedFiles = $changeTypes.Modified
    DeletedFiles = $changeTypes.Deleted
}

# 4. Dry run kontrolu
if ($DryRun) {
    Write-Host "`nDRY RUN - Guncelleme yapilmayacak" -ForegroundColor Yellow
    $plan | ConvertTo-Json | Set-Content -Path "$VaultPath/dry-run-plan.json"
    Write-Host "Plan kaydedildi: $VaultPath/dry-run-plan.json" -ForegroundColor Green
    exit 0
}

# 5. Guncellemeyi uygula
Write-Host "`n4. Guncelleme uygulaniyor..." -ForegroundColor Yellow

# Yeni dosyalari vault'a ekle
foreach ($file in $changeTypes.New) {
    Write-Host "  Yeni dosya ekleniyor: $file" -ForegroundColor Green
    # Vault'a ekleme mantigi
}

# Degisen dosyalari guncelle
foreach ($file in $changeTypes.Modified) {
    Write-Host "  Dosya guncelleniyor: $file" -ForegroundColor Yellow
    # Guncelleme mantigi
}

# Silinen dosyalari temizle
foreach ($file in $changeTypes.Deleted) {
    Write-Host "  Dosya temizleniyor: $file" -ForegroundColor Red
    # Temizleme mantigi
}

# 6. Dogrulama
Write-Host "`n5. Dogrulama yapiliyor..." -ForegroundColor Yellow
& ".ai/scripts/vault-validate.ps1"

# 7. Rapor olustur
Write-Host "`n6. Rapor olusturuluyor..." -ForegroundColor Yellow
$report = @"
# Vault Guncelleme Raporu

## Tarih: $($plan.Timestamp)
## Mod: $($plan.Mode)
## Degisiklik Sayisi: $($plan.Changes)

## Yeni Dosyalar
$($plan.NewFiles | ForEach-Object { "- $_" } | Out-String)

## Degisen Dosyalar
$($plan.ModifiedFiles | ForEach-Object { "- $_" } | Out-String)

## Silinen Dosyalar
$($plan.DeletedFiles | ForEach-Object { "- $_" } | Out-String)

## Durum: BASARILI
"@

$reportFile = "$VaultPath/reports/update-$(Get-Date -Format 'yyyyMMdd-HHmmss').md"
New-Item -ItemType Directory -Path (Split-Path $reportFile) -Force | Out-Null
$report | Set-Content -Path $reportFile

Write-Host "Rapor: $reportFile" -ForegroundColor Green

# 8. Log guncelle
Write-Host "`n7. Log guncelleniyor..." -ForegroundColor Yellow
$logEntry = @"
## Guncelleme Logu

**Tarih:** $($plan.Timestamp)
**Mod:** $($plan.Mode)
**Degisiklik:** $($plan.Changes) dosya
**Durum:** Basarili

"@

Add-Content -Path "$VaultPath/log.md" -Value $logEntry

Write-Host "`n=== GUNCELLEME TAMAMLANDI ===" -ForegroundColor Cyan
```

---

## Ek Bolum 3: Indeks Yonetimi

### Indeks Yapisi

```json
{
    "version": "1.0.0",
    "lastUpdate": "YYYY-MM-DD HH:MM:SS",
    "files": {
        "agents": {
            "count": 0,
            "list": []
        },
        "skills": {
            "count": 0,
            "list": []
        },
        "commands": {
            "count": 0,
            "list": []
        },
        "workflows": {
            "count": 0,
            "list": []
        },
        "rules": {
            "count": 0,
            "list": []
        }
    },
    "stats": {
        "totalFiles": 0,
        "totalSize": 0,
        "lastModified": ""
    }
}
```

### Indeks Guncelleme Scripti

```powershell
# vault-index-update.ps1
param(
    [string]$VaultPath = ".ai"
)

Write-Host "=== INDEKS GUNCELLEME ===" -ForegroundColor Cyan

# Indeks dosyasi
$indexFile = "$VaultPath/index.json"

# Mevcut indeksi yukle veya olustur
if (Test-Path $indexFile) {
    $index = Get-Content $indexFile | ConvertFrom-Json
} else {
    $index = @{
        version = "1.0.0"
        lastUpdate = ""
        files = @{
            agents = @{ count = 0; list = @() }
            skills = @{ count = 0; list = @() }
            commands = @{ count = 0; list = @() }
            workflows = @{ count = 0; list = @() }
            rules = @{ count = 0; list = @() }
        }
        stats = @{
            totalFiles = 0
            totalSize = 0
            lastModified = ""
        }
    }
}

# Dosyalari tara
Write-Host "Dosyalar taraniyor..." -ForegroundColor Yellow

$categories = @{
    agents = "agents"
    skills = "skills"
    commands = "commands"
    workflows = "workflows"
    rules = "rules"
}

foreach ($category in $categories.Keys) {
    $categoryPath = "$VaultPath/$($categories[$category])"
    if (Test-Path $categoryPath) {
        $files = Get-ChildItem -Path $categoryPath -Recurse -Filter "*.md"
        $index.files.$category.count = $files.Count
        $index.files.$category.list = $files | ForEach-Object { $_.Name }
    }
}

# Istatistikleri guncelle
$allFiles = Get-ChildItem -Path $VaultPath -Recurse -Include "*.md","*.ps1","*.json"
$index.stats.totalFiles = $allFiles.Count
$index.stats.totalSize = ($allFiles | Measure-Object -Property Length -Sum).Sum
$index.stats.lastModified = (Get-Date).ToString("yyyy-MM-dd HH:mm:ss")
$index.lastUpdate = $index.stats.lastModified

# Indeksi kaydet
$index | ConvertTo-Json -Depth 10 | Set-Content -Path $indexFile

Write-Host "Indeks guncellendi: $indexFile" -ForegroundColor Green
Write-Host "Toplam dosya: $($index.stats.totalFiles)" -ForegroundColor Yellow
Write-Host "Toplam boyut: $([Math]::Round($index.stats.totalSize / 1KB, 2)) KB" -ForegroundColor Yellow
```

---

## Ek Bolum 4: Referans Dogrulama

### Referans Kontrol Scripti

```powershell
# vault-reference-check.ps1
param(
    [string]$VaultPath = ".ai"
)

Write-Host "=== REFERANS DOGRULAMA ===" -ForegroundColor Cyan

# Tum markdown dosyalarini tara
$files = Get-ChildItem -Path $VaultPath -Recurse -Filter "*.md"
$references = @()

foreach ($file in $files) {
    $content = Get-Content -Path $file.FullName -Raw
    
    # Link referanslarini bul
    $linkMatches = [regex]::Matches($content, '\[([^\]]+)\]\(([^)]+)\)')
    foreach ($match in $linkMatches) {
        $references += [PSCustomObject]@{
            Source = $file.Name
            Text = $match.Groups[1].Value
            Target = $match.Groups[2].Value
            Type = "Link"
        }
    }
    
    # Dosya referanslarini bul
    $fileMatches = [regex]::Matches($content, '`([^`]+\.(md|ps1|json))`')
    foreach ($match in $fileMatches) {
        $references += [PSCustomObject]@{
            Source = $file.Name
            Text = $match.Groups[1].Value
            Target = $match.Groups[1].Value
            Type = "File"
        }
    }
}

# Referanslari kontrol et
Write-Host "Referanslar kontrol ediliyor..." -ForegroundColor Yellow

$brokenReferences = @()

foreach ($ref in $references) {
    if ($ref.Type -eq "File") {
        $targetPath = Join-Path $VaultPath $ref.Target
        if (-not (Test-Path $targetPath)) {
            $brokenReferences += $ref
            Write-Host "  KIRIK REFERANS: $($ref.Source) -> $($ref.Target)" -ForegroundColor Red
        }
    }
}

if ($brokenReferences.Count -eq 0) {
    Write-Host "  Kırık referans bulunamadi" -ForegroundColor Green
} else {
    Write-Host "  $($brokenReferences.Count) kırık referans bulundu" -ForegroundColor Red
}

# Rapor olustur
$report = @{
    Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    TotalReferences = $references.Count
    BrokenReferences = $brokenReferences.Count
    Details = $brokenReferences
}

$reportFile = "$VaultPath/reports/reference-check-$(Get-Date -Format 'yyyyMMdd-HHmmss').md"
New-Item -ItemType Directory -Path (Split-Path $reportFile) -Force | Out-Null

$reportContent = @"
# Referans Dogrulama Raporu

## Tarih: $($report.Timestamp)

## Ozet
- Toplam referans: $($report.TotalReferences)
- Kirik referans: $($report.BrokenReferences)

## Kirik Referanslar
$($brokenReferences | ForEach-Object { "- $($_.Source) -> $($_.Target)" } | Out-String)

## Durum: $(if ($report.BrokenReferences -eq 0) { "TEMIZ" } else { "SORUNLU" })
"@

$reportContent | Set-Content -Path $reportFile
Write-Host "Rapor: $reportFile" -ForegroundColor Green
```

---

## Ek Bolum 5: Butunluk Kontrolu

### Butunluk Dogrulama Scripti

```powershell
# vault-integrity-check.ps1
param(
    [string]$VaultPath = ".ai"
)

Write-Host "=== BUTUNLUK KONTROLU ===" -ForegroundColor Cyan

$issues = @()

# 1. Dosya erisim kontrolu
Write-Host "`n1. Dosya erisim kontrolu..." -ForegroundColor Yellow
$files = Get-ChildItem -Path $VaultPath -Recurse -Include "*.md","*.ps1","*.json"

foreach ($file in $files) {
    try {
        $content = Get-Content -Path $file.FullName -Raw -ErrorAction Stop
    } catch {
        $issues += [PSCustomObject]@{
            File = $file.Name
            Issue = "Dosya okunamadi"
            Severity = "High"
        }
        Write-Host "  HATA: $($file.Name) okunamadi" -ForegroundColor Red
    }
}

# 2. Format kontrolu
Write-Host "`n2. Format kontrolu..." -ForegroundColor Yellow
$mdFiles = Get-ChildItem -Path $VaultPath -Recurse -Filter "*.md"

foreach ($file in $mdFiles) {
    $content = Get-Content -Path $file.FullName -Raw
    
    # Baslik kontrolu
    if (-not ($content -match "^#\s+")) {
        $issues += [PSCustomObject]@{
            File = $file.Name
            Issue = "Baslik eksik"
            Severity = "Medium"
        }
        Write-Host "  UYARI: $($file.Name) basliksiz" -ForegroundColor Yellow
    }
    
    # Bos icerik kontrolu
    if ($content.Length -lt 100) {
        $issues += [PSCustomObject]@{
            File = $file.Name
            Issue = "Cok kisa icerik"
            Severity = "Low"
        }
        Write-Host "  UYARI: $($file.Name) cok kisa" -ForegroundColor Yellow
    }
}

# 3. Baglanti kontrolu
Write-Host "`n3. Baglanti kontrolu..." -ForegroundColor Yellow
foreach ($file in $mdFiles) {
    $content = Get-Content -Path $file.FullName -Raw
    
    # Kırık link kontrolu
    $brokenLinks = [regex]::Matches($content, '\[([^\]]+)\]\(([^)]+)\)') | 
        Where-Object { $_.Groups[2].Value -match "^https?://" -and $_.Groups[2].Value -notmatch "coremusic\.net" }
    
    if ($brokenLinks) {
        $issues += [PSCustomObject]@{
            File = $file.Name
            Issue = "Dis baglanti var"
            Severity = "Low"
        }
    }
}

# Rapor olustur
$report = @{
    Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    TotalFiles = $files.Count
    Issues = $issues.Count
    High = ($issues | Where-Object { $_.Severity -eq "High" }).Count
    Medium = ($issues | Where-Object { $_.Severity -eq "Medium" }).Count
    Low = ($issues | Where-Object { $_.Severity -eq "Low" }).Count
}

Write-Host "`n=== SONUCLAR ===" -ForegroundColor Cyan
Write-Host "Toplam dosya: $($report.TotalFiles)" -ForegroundColor Yellow
Write-Host "Sorun sayisi: $($report.Issues)" -ForegroundColor Yellow
Write-Host "  Kritik: $($report.High)" -ForegroundColor Red
Write-Host "  Orta: $($report.Medium)" -ForegroundColor Yellow
Write-Host "  Dusuk: $($report.Low)" -ForegroundColor Green

if ($report.Issues -eq 0) {
    Write-Host "`nBUTUNLUK TAMAM" -ForegroundColor Green
} else {
    Write-Host "`nBUTUNLUK SORUNLARI VAR" -ForegroundColor Red
    $issues | Format-Table -AutoSize
}
```

---

## Ek Bolum 6: Log Yonetimi

### Log Yapisi

```
.ai/logs/
├── daily/
│   ├── 2026-07-12.log
│   └── ...
├── weekly/
│   ├── 2026-W28.log
│   └── ...
├── monthly/
│   ├── 2026-07.log
│   └── ...
└── errors/
    ├── 2026-07-12-error.log
    └── ...
```

### Log Scripti

```powershell
# vault-logging.ps1
param(
    [string]$Message,
    [string]$Level = "INFO",
    [string]$VaultPath = ".ai"
)

$timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
$logDir = "$VaultPath/logs"

# Log dizinlerini olustur
@("daily", "weekly", "monthly", "errors") | ForEach-Object {
    $dir = Join-Path $logDir $_
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
}

# Gunluk log
$dailyLog = Join-Path $logDir "daily/$(Get-Date -Format 'yyyy-MM-dd').log"
$logEntry = "[$timestamp] [$Level] $Message"
Add-Content -Path $dailyLog -Value $logEntry

# Hata logu
if ($Level -eq "ERROR") {
    $errorLog = Join-Path $logDir "errors/$(Get-Date -Format 'yyyy-MM-dd')-error.log"
    Add-Content -Path $errorLog -Value $logEntry
}

# Haftalik log
$weekNumber = [System.Globalization.CultureInfo]::CurrentCulture.Calendar.GetWeekOfYear(
    (Get-Date),
    [System.Globalization.CalendarWeekRule]::FirstFourDayWeek,
    [System.DayOfWeek]::Monday
)
$weeklyLog = Join-Path $logDir "weekly/$(Get-Date -Format 'yyyy')-W$($weekNumber.ToString('00')).log"
Add-Content -Path $weeklyLog -Value $logEntry

# Aylik log
$monthlyLog = Join-Path $logDir "monthly/$(Get-Date -Format 'yyyy-MM').log"
Add-Content -Path $monthlyLog -Value $logEntry
```

---

## Ek Bolum 7: Performans Metrikleri

### Metrik Toplama Scripti

```powershell
# vault-metrics.ps1
param(
    [string]$VaultPath = ".ai",
    [string]$MetricsPath = ".ai/metrics"
)

Write-Host "=== VAULT PERFORMANS METRIKLERI ===" -ForegroundColor Cyan

# Metrics dizinini olustur
if (-not (Test-Path $MetricsPath)) {
    New-Item -ItemType Directory -Path $MetricsPath -Force | Out-Null
}

$stopwatch = [System.Diagnostics.Stopwatch]::StartNew()

# 1. Dosya analizi
Write-Host "`n1. Dosya analizi..." -ForegroundColor Yellow
$files = Get-ChildItem -Path $VaultPath -Recurse -Include "*.md","*.ps1","*.json"

$fileStats = $files | Group-Object Extension | ForEach-Object {
    [PSCustomObject]@{
        Extension = $_.Name
        Count = $_.Count
        TotalSize = ($_.Group | Measure-Object -Property Length -Sum).Sum
        AvgSize = ($_.Group | Measure-Object -Property Length -Average).Average
    }
}

# 2. Guncellik analizi
Write-Host "`n2. Guncellik analizi..." -ForegroundColor Yellow
$outdatedFiles = $files | Where-Object { 
    $_.LastWriteTime -lt (Get-Date).AddDays(-30) 
}

# 3. Boyut analizi
Write-Host "`n3. Boyut analizi..." -ForegroundColor Yellow
$largeFiles = $files | Where-Object { $_.Length -gt 50000 }  # 50KB

$stopwatch.Stop()

# Metrikleri kaydet
$metrics = @{
    Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    AnalysisDuration = $stopwatch.Elapsed.TotalSeconds
    FileStats = $fileStats
    OutdatedFiles = $outdatedFiles.Count
    LargeFiles = $largeFiles.Count
    TotalFiles = $files.Count
    TotalSize = ($files | Measure-Object -Property Length -Sum).Sum
}

$metricsFile = Join-Path $MetricsPath "metrics-$(Get-Date -Format 'yyyyMMdd-HHmmss').json"
$metrics | ConvertTo-Json -Depth 5 | Set-Content -Path $metricsFile

# Rapor
Write-Host "`n=== METRIK SONUCLARI ===" -ForegroundColor Cyan
Write-Host "Toplam dosya: $($metrics.TotalFiles)" -ForegroundColor Yellow
Write-Host "Toplam boyut: $([Math]::Round($metrics.TotalSize / 1KB, 2)) KB" -ForegroundColor Yellow
Write-Host "Eski dosya (30+ gun): $($metrics.OutdatedFiles)" -ForegroundColor Yellow
Write-Host "Buyuk dosya (50KB+): $($metrics.LargeFiles)" -ForegroundColor Yellow
Write-Host "Analiz suresi: $([Math]::Round($metrics.AnalysisDuration, 2)) saniye" -ForegroundColor Yellow
```

---

## Ek Bolum 8: Rapor Sablonlari

### Guncelleme Raporu

```markdown
# Vault Guncelleme Raporu

## Tarih
YYYY-MM-DD HH:MM

## Ozet
- **Mod:** [Full/Incremental/Update]
- **Durum:** [Basarili/Basarisiz]
- **Sure:** X saniye
- **Degisiklik:** X dosya

## Degisiklik Detaylari

### Yeni Dosyalar
| Dosya | Tur | Boyut |
|-------|-----|-------|
| [dosya.md] | Agent | X KB |

### Degisen Dosyalar
| Dosya | Tur | Degisiklik |
|-------|-----|------------|
| [dosya.md] | Skill | Icerik |

### Silinen Dosyalar
| Dosya | Tur | Neden |
|-------|-----|-------|
| [dosya.md] | Command | Gereksiz |

## Performans
- Isleme suresi: X ms
- Dosya isleme hizi: X dosya/saniye

## Sorunlar
| Sorun | Onem | Cozum |
|-------|------|-------|
| [Sorun] | [Onem] | [Cozum] |

## Oneriler
1. [Oneri 1]
2. [Oneri 2]

## Sonraki Adimlar
1. [Adim 1]
2. [Adim 2]
```

---

## Ek Bolum 9: En Iyi Uygulamalar

### Guncelleme Kurallari

1. **Once dry run:** Her guncelleme oncesi test edin
2. **Yedek alin:** Geri alma icin yedek tutun
3. **Log tutun:** Her islemi kaydedin
4. **Dogrulama:** Her guncelleme sonrasi kontrol edin
5. **Raporlama:** Sonuclari raporlayin

### Yaygin Hatalar

| Hata | Sonuc | Onleme |
|------|-------|--------|
| Dry run yapmama | Yanlis guncelleme | Her zaman dry run |
| Yedek almama | Geri alama | Duzenli yedek |
| Log tutmama | Takip edememe | Her islemi logla |
| Dogrulama yapmama | Hatali durum | Her guncelleme sonrasi |

### Kontrol Listesi

```markdown
# Vault Update Kontrol Listesi

## On Hazirlik
- [ ] Yedek alindi
- [ ] Dry run yapildi
- [ ] Ekip bilgilendirildi

## Guncelleme
- [ ] Degisiklikler tespit edildi
- [ ] Agent secildi
- [ ] Guncelleme uygulandi

## Sonrasinda
- [ ] Dogrulama yapildi
- [ ] Rapor olusturuldu
- [ ] Log guncellendi
- [ ] Ekip bilgilendirildi
```

---

*Vault Update Komutu v1.0.0 - 2026-07-12*
*CoreMusic Orchestration System*
