---
description: "Vault dosyalarini senkronize et, butunlugu dogrula, referanslari kontrol et"
agent: build
---

# Vault Sync Komutu

Bu komut, tum vault dosyalarini senkronize eder.
Butunlugu dogrular ve referanslari kontrol eder.

## Ne Yapar?

1. **Tarama** - Tum .ai/ ve .claude/rules/ dosyalarini tarar
2. **Indeksleme** - Yeni dosyalari .ai/index.md'ye ekler
3. **Referans Dogrulama** - Tum [[wiki-link]] referanslarini kontrol eder
4. **Butunluk** - Dosya checksum'larini dogrular
5. **Rapor** - Senkronizasyon raporu olusturur

## Neden Onemli?

Vault'un guncel ve tutarli olmasini saglar.
Kirik referanslari tespit eder.
Veri butunlugunu korur.
Bilgi daginikligini onler.

## Ne Zaman Kullanilir?

- Yeni dosya olusturulduktan sonra
- Dosya degistirildikten sonra
- Git commit oncesi
- Git merge sonrasi
- Deployment oncesi
- Haftalik duzenli kontrolde

## Prosedur

### Adim 1: Tarama

Once tum vault dosyalari taranir:

```powershell
# .ai/ dizinindeki tum .md dosyalari
Get-ChildItem -Path .ai -Recurse -Filter *.md

# .claude/rules/ dizinindeki tum dosyalar
Get-ChildItem -Path .claude/rules -Filter *.md

# .workflows/ dizinindeki tum dosyalar
Get-ChildItem -Path .workflows -Filter *.md
```

### Adim 2: Satir Sayimi

Her dosya icin satir sayimi yapilir:

```powershell
Get-ChildItem -Path .ai -Recurse -Filter *.md | ForEach-Object {
    $content = Get-Content $_.FullName
    $lines = $content.Count
    Write-Host "$($_.Name): $lines satir"
}
```

### Adim 3: Indeks Guncelleme

Yeni dosyalar indekse eklenir:

```markdown
## [Dosya Adi]
- **Konum:** .ai/dosya-yolu.md
- **Tur:** Agent|Workflow|Decision|Security|Test
- **Satir:** satir_sayisi
- **Son guncelleme:** tarih
```

### Adim 4: Referans Kontrolu

Tum referanslar kontrol edilir:

```powershell
# Wiki-link referanslari
Select-String -Path "*.md" -Pattern "\[\[.*\]\]"

# Markdown link referanslari
Select-String -Path "*.md" -Pattern "\[.*\]\(.*\)"
```

### Adim 5: Checksum Hesaplama

Dosya butunlugu icin checksum hesaplanir:

```powershell
Get-ChildItem -Path .ai -Recurse -Filter *.md | ForEach-Object {
    $hash = Get-FileHash $_.FullName -Algorithm MD5
    Write-Host "$($_.Name): $($hash.Hash)"
}
```

## Hata Yonetimi

### Yaygin Hatalar

| Hata | Belirti | Cozum |
|------|---------|-------|
| Dosya okunamiyor | Read hatasi | Izinleri kontrol et |
| Referans bulunamadi | Kirik link | Referansi duzelt veya kaldir |
| Butunluk hatasi | Checksum eslesmiyor | Yedekten geri yukle |
| Guncelleme basarisiz | Write hatasi | Izinleri kontrol et |

### Kurtarma Proseduru

1. Sorunu tespit et
2. Kok nedeni bul
3. Cozumu uygula
4. Dogrula
5. Logla

## Rapor Formati

```markdown
# Vault Senkronizasyon Raporu

**Tarih:** YYYY-MM-DD HH:MM
**Sorumlu:** [agent/kullanici]

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

## Monitoring

### Izlenen Metrikler

| Metrik | Hedef | Alert |
|--------|-------|-------|
| Dosya butunlugu | %100 | Bozulma tespit edildiginde |
| Referans gecerliligi | %100 | Kirik link tespit ettiginde |
| Guncelleme sikligi | Haftalik | Guncellenmediginde |
| Hata orani | < %1 | %5 ustunde |

### Alert Politikalari

| Alert | Seviye | Yanit |
|-------|--------|-------|
| Dosya bozuk | Kritik | Hemen duzelt |
| Kirik referans | Yuksek | 1 saat icinde |
| Guncelleme gecikmesi | Orta | 24 saat icinde |
| Boyut asimi | Dusuk | Haftalik inceleme |

## Komut Satiri Ornekleri

### Tam Senkronizasyon

```powershell
.\.ai\scripts\vault-sync.ps1 -Mode full
```

### Sadece Kontrol

```powershell
.\.ai\scripts\vault-sync.ps1 -Mode check
```

### Otomatik Guncelleme

```powershell
.\.ai\scripts\vault-auto-update.ps1 -AutoFix
```

### Butunluk Kontrolu

```powershell
.\.ai\scripts\vault-integrity-check.ps1 -Detailed
```

## Referanslar

### Internal

- `.ai/index.md` - Ana dizin katalogu
- `.ai/keys.md` - Navigasyon rehberi
- `.ai/engine.md` - Orchestration motoru
- `.ai/AGENTS.md` - Agent tanimlari
- `.ai/WORKFLOW.md` - Workflow kurallari

### External

- Git Documentation: https://git-scm.com/doc
- Markdown Specification: https://spec.commonmark.org/

## Kontrol Listesi

- [ ] Tum .ai/ dosyalari tarandi
- [ ] Tum .claude/rules/ dosyalari tarandi
- [ ] Tum .workflows/ dosyalari tarandi
- [ ] Yeni dosyalar indekslendi
- [ ] Silinen dosyalar cikarildi
- [ ] Referanslar kontrol edildi
- [ ] Checksum'lar hesaplandi
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
| Tam tarama | 5-10 dk | Haftalik |
| Referans kontrolu | 2-5 dk | Her degisiklik |
| Checksum hesaplama | 1-3 dk | Her degisiklik |
| Rapor olusturma | 1 dk | Her calistirma |
| Indeks guncelleme | 1-2 dk | Her degisiklik |

## Basari Kriterleri

- Tum dosyalar guncel
- Kirik referans yok
- Butunluk dogrulandi
- Indeks guncellendi
- Rapor olusturuldu
- Log guncellendi

## Risk Degerlendirmesi

| Risk | Olasilik | Etki | Onlem |
|------|----------|------|-------|
| Dosya kaybi | Dusuk | Yuksek | Yedekleme |
| Veri bozulma | Dusuk | Kritik | Checksum |
| Kirik referans | Orta | Orta | Otomatik kontrol |
| Guncelleme hatasi | Dusuk | Dusuk | Loglama |

## Sonraki Adimlar

1. Senkronizasyonu calistir
2. Sonuclari kontrol et
3. Varsa sorunlari duzelt
4. Raporu olustur
5. Logu guncelle

## Notlar

- Bu komut duzenli olarak calistirilmalidir
- Her onemli degisiklik sonrasi calistirilmasi onerilir
- Haftalik olarak tam kontrol yapilmalidir
- Sorunlar hemen cozulmelidir

---

## Ek Bolum 1: Komut Calistirma Kılavuzu

### Temel Kullanim

```bash
# Senkronizasyonu baslat
vault-sync

# Parametrelerle
vault-sync --mode=full --verbose

# Dry run
vault-sync --dry-run
```

### Parametreler

| Parametre | Aciklama | Varsayilan |
|-----------|----------|------------|
| --mode | Calisma modu | incremental |
| --target | Hedef dosyalar | all |
| --force | Zorla calistir | false |
| --dry-run | Test modu | false |
| --verbose | Detayli cikti | false |
| --report | Rapor olustur | false |

### Cikti Formatlari

| Format | Aciklama | Kullanim |
|--------|----------|----------|
| text | Insan okunabilir | Terminal |
| json | Makine okunabilir | CI/CD |
| html | Rapor | Dashboard |

---

## Ek Bolum 2: Senkronizasyon Stratejisi

### Strateji Secimi

| Durum | Strateji | Avantaj | Dezavantaj |
|-------|----------|---------|------------|
| Tam senkron | Full | Kapsamli | Yavas |
| Kismi senkron | Incremental | Hizli | Eksik kalabilir |
| Sadece dogrulama | Validate | Guvenli | Guncelleme yok |
| Raporlama | Report | Bilgilendirici | Aksiyon yok |

### Senkronizasyon Akisi

```
┌─────────────────────────────────────────────────────────────┐
│                 SENKRONIZASYON AKISI                           │
│                                                               │
│  ┌─────────────┐              ┌─────────────┐                │
│  │ Baslangic   │              │ Kontrol     │                │
│  │             │─────────────►│             │                │
│  └─────────────┘              └─────────────┘                │
│         │                           │                         │
│         │                           │                         │
│  ┌─────────────┐              ┌─────────────┐                │
│  │ Analiz      │              │ Planlama    │                │
│  │             │◄─────────────│             │                │
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

---

## Ek Bolum 3: Hata Yonetimi

### Hata Turleri

| Hata | Aciklama | Cozum |
|------|----------|-------|
| Dosya yok | Dosya bulunamadi | Olustur |
| Izin yok | Erisim engellendi | Izin ver |
| Format hatasi | Yanlis format | Duzelt |
| Baglanti hatasi | Network sorunu | Kontrol et |
| Timeout | Islem zaman asimi | Tekrar dene |

### Hata Yonetim Scripti

```powershell
# vault-sync-error-handling.ps1
param(
    [string]$Action = "sync"
)

try {
    Write-Host "Senkronizasyon baslatiliyor..." -ForegroundColor Cyan
    
    # Ana islem
    $result = & ".ai/scripts/vault-sync.ps1" -Action $Action
    
    if ($LASTEXITCODE -ne 0) {
        throw "Senkronizasyon hatasi: $LASTEXITCODE"
    }
    
    Write-Host "Senkronizasyon basarili" -ForegroundColor Green
    
} catch {
    Write-Host "HATA: $($_.Exception.Message)" -ForegroundColor Red
    
    # Hata logu
    $errorLog = @"
## Hata Logu

**Tarih:** $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
**Hata:** $($_.Exception.Message)
**Stack Trace:** $($_.ScriptStackTrace)
**Aksiyon:** Tekrar dene veya manual kontrol

"@
    
    Add-Content -Path ".ai/log.md" -Value $errorLog
    
    # Hata bildirimi
    Write-Host "Hata loglandi ve bildirim gonderildi" -ForegroundColor Yellow
    
    exit 1
}
```

---

## Ek Bolum 4: Performans Optimizasyonu

### Optimizasyon Teknikleri

| Teknik | Aciklama | Etki |
|--------|----------|------|
| Paralel islem | Eş zamanlı calisma | Hiz |
| Cache | Tekrarlanan islemleri azalt | Hiz |
| Incremental | Sadece degisenleri isle | Hiz |
| Lazy loading | Gerektiginde yukle | Bellek |
| Batch islem | Toplu isleme | Hiz |

### Performans Scripti

```powershell
# vault-sync-performance.ps1
param(
    [string]$VaultPath = ".ai",
    [int]$BatchSize = 10
)

$stopwatch = [System.Diagnostics.Stopwatch]::StartNew()

Write-Host "=== PERFORMANS ANALIZI ===" -ForegroundColor Cyan

# Dosyalari topla
$files = Get-ChildItem -Path $VaultPath -Recurse -Include "*.md"
$totalFiles = $files.Count

Write-Host "Toplam dosya: $totalFiles" -ForegroundColor Yellow

# Batch isleme
$batches = [Math]::Ceiling($totalFiles / $BatchSize)
$processedFiles = 0

for ($i = 0; $i -lt $batches; $i++) {
    $batch = $files | Select-Object -First $BatchSize -Skip ($i * $BatchSize)
    
    foreach ($file in $batch) {
        # Islem yap
        $content = Get-Content $file.FullName -Raw
        $lineCount = ($content -split "`n").Count
        
        $processedFiles++
        
        if ($processedFiles % 10 -eq 0) {
            Write-Host "Islenen: $processedFiles / $totalFiles" -ForegroundColor Green
        }
    }
}

$stopwatch.Stop()

# Rapor
$report = @"
# Performans Raporu

## Tarih: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")

## Sonuclar
- Toplam dosya: $totalFiles
- Islenen dosya: $processedFiles
- Toplam sure: $($stopwatch.Elapsed.TotalSeconds) saniye
- Ortalama sure: $($stopwatch.Elapsed.TotalSeconds / $totalFiles) saniye/dosya

## Hizmet
- Dosya/saniye: $([Math]::Round($totalFiles / $stopwatch.Elapsed.TotalSeconds, 2))

## Oneriler
- Batch boyutunu ayarlayarak hizlanabilir
- Cache kullanimi ile iyilestirilebilir
"@

Write-Host "`n$report" -ForegroundColor Cyan
```

---

## Ek Bolum 5: monitoring ve Alarm

### Monitoring Kurulumu

```powershell
# vault-sync-monitoring.ps1
param(
    [string]$VaultPath = ".ai",
    [string]$AlertEmail = "admin@coremusic.net"
)

# Monitoring durumunu kontrol et
Write-Host "=== VAULT MONITORING ===" -ForegroundColor Cyan

# Son guncelleme zamanini kontrol et
$lastUpdate = (Get-ChildItem -Path $VaultPath -Recurse -File |
    Sort-Object LastWriteTime -Descending | Select-Object -First 1).LastWriteTime

$timeSinceUpdate = (Get-Date) - $lastUpdate

if ($timeSinceUpdate.TotalDays -gt 7) {
    Write-Host "UYARI: Vault 7 günden fazla guncellenmemis" -ForegroundColor Yellow
    
    # Bildirim gonder
    $subject = "Vault Guncelleme Uyarisi"
    $body = "Vault son olarak $($timeSinceUpdate.TotalDays) gun once guncellenmis."
    
    # Email gonderimi (ornek)
    # Send-MailMessage -To $AlertEmail -Subject $subject -Body $body
}

# Dosya sayisi degisimini kontrol et
$currentFileCount = (Get-ChildItem -Path $VaultPath -Recurse -Filter "*.md").Count
$countFile = Join-Path $VaultPath "metrics/file-count.txt"

if (Test-Path $countFile) {
    $previousCount = Get-Content $countFile -Raw
    $change = $currentFileCount - [int]$previousCount
    
    if ([Math]::Abs($change) -gt 5) {
        Write-Host "UYARI: Dosya sayisinda buyuk degisim: $change" -ForegroundColor Yellow
    }
}

# Guncel sayiyi kaydet
$currentFileCount | Set-Content -Path $countFile

Write-Host "Monitoring tamamlandi" -ForegroundColor Green
```

---

## Ek Bolum 6: Raporlama Sistemi

### Rapor Sablonu

```markdown
# Vault Senkronizasyon Raporu

## Tarih
YYYY-MM-DD HH:MM

## Ozet
- **Mod:** [Full/Incremental/Validate]
- **Durum:** [Basarili/Basarisiz]
- **Sure:** X saniye
- **Dosya sayisi:** X

## Detaylar

### Islenen Dosyalar
| Dosya | Tur | Durum |
|-------|-----|-------|
| AGENTS.md | Agent | Guncellendi |
| WORKFLOW.md | Workflow | Degisiklik yok |

### Bulunan Sorunlar
| Sorun | Onem | Cozum |
|-------|------|-------|
| Eski versiyon | Orta | Guncelleme gerekli |

### Performans
- Ortalama isleme suresi: X ms
- Toplam bellek kullanimi: X MB
- Hata orani: %X

## Oneriler
1. [Oneri 1]
2. [Oneri 2]

## Sonraki Adimlar
1. [Adim 1]
2. [Adim 2]
```

---

## Ek Bolum 7: Guvenlik Kontrolleri

### Guvenlik Proseduru

| Adim | Aciklama | Sorumlu |
|------|----------|---------|
| 1 | Erisim kontrolu | DevOps |
| 2 | Secret taramasi | Security |
| 3 | Izin dogrulama | DevOps |
| 4 | Log tutma | DevOps |
| 5 | Audit trail | Security |

### Guvenlik Scripti

```powershell
# vault-sync-security.ps1
param(
    [string]$VaultPath = ".ai"
)

Write-Host "=== VAULT GUVENLIK KONTROLU ===" -ForegroundColor Cyan

# 1. Erisim kontrolu
Write-Host "`n1. Erisim kontrolu yapiliyor..." -ForegroundColor Yellow
$acl = Get-Acl $VaultPath
$accessRules = $acl.Access | ForEach-Object {
    [PSCustomObject]@{
        Identity = $_.IdentityReference
        Rights = $_.FileSystemRights
        Type = $_.AccessControlType
    }
}

$accessRules | Format-Table -AutoSize

# 2. Secret taramasi
Write-Host "`n2. Secret taramasi yapiliyor..." -ForegroundColor Yellow
$secretPatterns = @(
    'password\s*=\s*["\x27]',
    'secret\s*=\s*["\x27]',
    'api_key\s*=\s*["\x27]'
)

$files = Get-ChildItem -Path $VaultPath -Recurse -Filter "*.md"
$secretsFound = $false

foreach ($file in $files) {
    $content = Get-Content -Path $file.FullName -Raw
    foreach ($pattern in $secretPatterns) {
        if ($content -match $pattern) {
            Write-Host "  POTANSIEL SECRET: $($file.Name)" -ForegroundColor Red
            $secretsFound = $true
        }
    }
}

if (-not $secretsFound) {
    Write-Host "  Secret bulunamadi" -ForegroundColor Green
}

# 3. Dosya boyutu kontrolu
Write-Host "`n3. Dosya boyutu kontrolu yapiliyor..." -ForegroundColor Yellow
$largeFiles = $files | Where-Object { $_.Length -gt 1MB }

if ($largeFiles) {
    Write-Host "  Buyuk dosyalar:" -ForegroundColor Yellow
    $largeFiles | ForEach-Object {
        Write-Host "    - $($_.Name): $([Math]::Round($_.Length / 1MB, 2)) MB"
    }
} else {
    Write-Host "  Buyuk dosya bulunamadi" -ForegroundColor Green
}

Write-Host "`n=== KONTROL TAMAMLANDI ===" -ForegroundColor Cyan
```

---

## Ek Bolum 8: Otomasyon Entegrasyonu

### CI/CD Entegrasyonu

```yaml
# .github/workflows/vault-sync.yml
name: Vault Sync

on:
  push:
    paths:
      - '.ai/**'
      - '.claude/**'
      - '.opencode/**'
  pull_request:
    paths:
      - '.ai/**'
      - '.claude/**'
      - '.opencode/**'

jobs:
  vault-sync:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PowerShell
        uses: azure/powershell@v1
      
      - name: Vault Sync
        run: |
          ./ai/scripts/vault-sync.ps1 -Action sync
      
      - name: Vault Validate
        run: |
          ./ai/scripts/vault-validate.ps1
      
      - name: Upload Report
        uses: actions/upload-artifact@v3
        with:
          name: vault-report
          path: .ai/reports/
```

### Git Hook Entegrasyonu

```bash
#!/bin/bash
# .git/hooks/pre-commit

echo "Vault kontrol ediliyor..."

# Vault dosyalarinda degisiklik var mi?
vault_changes=$(git diff --cached --name-only | grep -E "^\.ai/|^\.claude/|^\.opencode/" | wc -l)

if [ $vault_changes -gt 0 ]; then
    echo "Vault dosyalarinda degisiklik var, senkronizasyon yapiliyor..."
    
    # Vault sync
    pwsh -File ".ai/scripts/vault-sync.ps1" -Action validate
    
    if [ $? -ne 0 ]; then
        echo "Vault senkronizasyonu basarisiz!"
        exit 1
    fi
fi

echo "Vault kontrolu tamamlandi"
```

---

## Ek Bolum 9: Geri Alma Proseduru

### Geri Alma Adimlari

```markdown
# Vault Geri Alma Proseduru

## 1. Durum Degerlendirmesi
- Hangi dosya etkilendi?
- Ne kadar degisiklik var?
- Geri almak mumkun mu?

## 2. Yedek Kontrolu
- Son yedek hangi tarihte?
- Yedek tam mi?
- Geri alma mumkun mu?

## 3. Geri Alma Islemi
- Dosyalari geri yukle
- Dogrulama yap
- Test et

## 4. Dogrulama
- Dosyalar dogru mu?
- Icerik tam mi?
- Baglantilar calisiyor mu?

## 5. Bildirim
- Ekip bilgilendirme
- Rapor olusturma
- Dokumantasyon guncelleme
```

### Geri Alma Scripti

```powershell
# vault-rollback.ps1
param(
    [string]$BackupPath,
    [string]$VaultPath = ".ai"
)

Write-Host "=== VAULT GERI ALMA ===" -ForegroundColor Cyan

if (-not $BackupPath) {
    # Son yedegi bul
    $backupDir = "backups"
    $BackupPath = Get-ChildItem -Path $backupDir -Directory |
        Where-Object { $_.Name -like "vault-*" } |
        Sort-Object Name -Descending |
        Select-Object -First 1
    
    if (-not $BackupPath) {
        Write-Host "Yedek bulunamadi" -ForegroundColor Red
        exit 1
    }
    
    $BackupPath = $BackupPath.FullName
}

Write-Host "Yedek: $BackupPath" -ForegroundColor Yellow

# Onay al
$confirm = Read-Host "Geri almak istediginize emin misiniz? (E/H)"
if ($confirm -ne "E") {
    Write-Host "Islem iptal edildi" -ForegroundColor Yellow
    exit 0
}

# Geri al
Write-Host "Geri aliniyor..." -ForegroundColor Yellow

# Mevcut durumu yedekle
$currentBackup = "backups/vault-pre-rollback-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
Copy-Item -Path $VaultPath -Destination $currentBackup -Recurse
Write-Host "Mevcut durum yedeklendi: $currentBackup" -ForegroundColor Green

# Geri al
Copy-Item -Path "$BackupPath/*" -Destination $VaultPath -Recurse -Force
Write-Host "Geri alma tamamlandi" -ForegroundColor Green

# Dogrulama
Write-Host "Dogrulama yapiliyor..." -ForegroundColor Yellow
& ".ai/scripts/vault-validate.ps1"

Write-Host "=== GERI ALMA TAMAMLANDI ===" -ForegroundColor Cyan
```

---

## Ek Bolum 10: En Iyi Uygulamalar

### Kullanim Kurallari

1. **Duzenli senkronizasyon:** Her buyuk degisiklik sonra
2. **Dry run once:** Once test edin, sonra uygulayin
3. **Yedek alin:** Geri alma icin yedek tutun
4. **Log tutun:** Her islemi kaydedin
5. **Dogrulama:** Her guncelleme sonrasi kontrol edin

### Yaygin Hatalar

| Hata | Sonuc | Onleme |
|------|-------|--------|
| Dry run yapmama | Yanlis guncelleme | Her zaman dry run |
| Yedek almama | Geri alama | Duzenli yedek |
| Log tutmama | Takip edememe | Her islemi logla |
| Dogrulama yapmama | Hatali durum | Her guncelleme sonrasi |

### Kontrol Listesi

```markdown
# Vault Sync Kontrol Listesi

## On Hazirlik
- [ ] Yedek alindi
- [ ] Dry run yapildi
- [ ] Ekip bilgilendirildi

## Senkronizasyon
- [ ] Dogru mod secildi
- [ ] Hedef dosyalar belirlendi
- [ ] Baslatildi

## Sonrasinda
- [ ] Dogrulama yapildi
- [ ] Rapor olusturuldu
- [ ] Ekip bilgilendirildi
```

---

*Vault Sync Komutu v1.0.0 - 2026-07-12*
*CoreMusic Orchestration System*
