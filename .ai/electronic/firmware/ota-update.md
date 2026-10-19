---
type: electronic
category: ota-update
title: "CoreMusic — OTA Firmware Update"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — OTA Firmware Update

**See also:** [[electronic/firmware/index]] · [[architecture/07-security/driver-signing]]

---

## 1. Amaç

OTA Firmware Update, CoreMusic ELECTRONICS platformunun kablosuz firmware güncelleme mekanizmasını tanımlar.

---

## 2. OTA Akışı

```
Download Firmware (HTTPS)
    ↓
Verify Checksum (SHA-256)
    ↓
Verify Signature (RSA-2048)
    ↓
Backup Current Firmware
    ↓
Write New Firmware
    ↓
Reboot
    ↓
Verify New Firmware
    ↓
Or: Rollback to Backup
```

---

## 3. Güvenlik

| Özellik | Değer |
|---------|-------|
| İmza | RSA-2048 |
| Şifreleme | AES-256-GCM |
| Checksum | SHA-256 |
| Rollback | Mevcut firmware yedeği |
| Fail-safe | Dual-bank flash |

---

## 4. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-022-database-hardened-security]] | Şifreleme |
| [[ADR-034-credential-vault-normalization]] | Credential vault |

---

## 5. Güncelleme İmzalama Zinciri (Root of Trust)

OTA güvenilirliği, donanımda yerleşik bir güven köküne (root of trust) dayanır. İmza doğrulama zinciri, bootloader'ın her aşamasında bir sonraki aşamanın imzasını doğrulamasıyla kurulur.

| Aşama | Sorumlu | Doğrulanan | Hata Davranışı |
|-------|---------|------------|----------------|
| Boot ROM | Donanım (immutable) | Bootloader imzası | Boot reddi / safe-mode |
| Bootloader | ROM tarafından doğrulandı | Kernel/firmware imzası | Rollback / recovery |
| Kernel/firmware | Bootloader tarafından | Uygulama imzası (isteğe bağlı) | Uygulama devre dışı |
| OTA agent | Firmware | Güncelleme paketi imzası | Güncelleme reddi |

### 5.1 İmza Algoritması Seçimi

| Algoritma | Güç | Anahtar | CoreMusic Kullanımı |
|-----------|-----|---------|---------------------|
| RSA-2048 | Yüksek | Uzun anahtar, yavaş imzalama | Mevcut tasarım (Bölüm 3) |
| ECDSA P-256 | Yüksek | Kısa anahtar, hızlı | Alternatif (daha verimli) |
| Ed25519 | Yüksek | Çok kısa, hızlı | Modern alternatif |
| SHA-256 | — | — | Checksum (Bölüm 3) |
| AES-256-GCM | — | Simetrik | Paket şifreleme (Bölüm 3) |

### 5.2 İmzalama Zinciri Prensibi

İmza doğrulama zinciri, "güvenilen her aşama bir sonrakini doğrular" prensibine dayanır; en altta donanımda salt-okunur (immutable) bir root of trust bulunur. Bootloader yalnızca imzası doğrulanmış bir sonraki aşamayı yükler. Zincir herhangi bir halkasında koparsa sistem güvenli moda (recovery) düşer — sessizce çalışmaya devam etmez.

> ⚠️ VERIFICATION REQUIRED: CoreMusic donanım root-of-trust (secure boot ROM / HSM) implementasyonu henüz belgelenmemiştir; bu tablo tasarım niyetidir, ölçülmüş/kodlanmış gerçek değildir.

---

## 6. Dual-Bank (A/B) Güncelleme Mekanizması

Kesintisiz ve güvenli güncelleme, iki firmware yuvasının (A/B) dönüşümlü kullanımıyla sağlanır; bu, Bölüm 2'deki "dual-bank flash" maddesinin detayıdır.

| Yuva | Durum | Açıklama |
|------|-------|----------|
| A (active) | Çalışan | Şu an boot edilen firmware |
| B (inactive) | Boş / eski | Yeni firmware buraya yazılır |
| Scratch / recovery | Koruma | Kurtarma image (isteğe bağlı) |

### 6.1 A/B Güncelleme Adımları

```
1. Yeni firmware indir (TLS, Bölüm 8) + imza doğrula (Bölüm 5)
2. Inactive yuvaya yaz (B yuvası)
3. Yuvayı "bootable" işaretle, aktif yuvayı "pending" yap
4. Reboot → bootloader B yuvasından dener
5. Sağlık kontrolü (Bölüm 9) → başarılıysa B'yi "good" işaretle
6. Başarısızsa → watchdog/recovery A yuvasına geri döner
```

### 6.2 Neden A/B (Atomic Update)

Tek-yuva güncellemede, yazma sırasında güç kesilirse firmware bozulur ve cihaz "brick" olur. A/B modelinde yeni firmware inactive yuvaya tamamlanana kadar aktif yuva değişmez; güncelleme atomiktir — ya yeni sürüm tamamen devreye girer ya da eski sürüm aynen kalır. Bu, Bölüm 3'teki "Fail-safe: Dual-bank flash" maddesinin garanti mekanizmasıdır.

---

## 7. Rollback ve Sağlamlık (Robustness)

Rollback, hatalı bir güncellemenin geri alınmasıdır. İki tür rollback vardır: otomatik (sağlık kontrolü tetikli) ve anti-rollback (sürüm monotonisi).

| Rollback Türü | Tetikleyici | Amaç |
|---------------|-------------|------|
| Otomatik rollback | Post-update sağlık kontrolü başarısız | Bozuk sürümü reddet |
| Anti-rollback | Sürüm numarası monoton artmalı | Eski/vulnerable sürümün yüklenmesini engelle |
| Kademeli rollback | Kademeli dağıtım geri alma | Yaygın hatalı sürümü çekme |

### 7.1 Anti-Rollback (Version Monotonicity)

Sürüm numarası (monotonic counter) donanımda (OTP/secure counter) tutulur; bootloader, depolanan sürümdeki paketleri reddeder. Bu, bilinen bir güvenlik açığı içeren eski firmware'in geri yüklenmesini engeller. Düşük seviyeli sayaç salt-okunur donanımda tutulmadığında anti-rollback zayıflar → root-of-trust (Bölüm 5) ile birlikte düşünülmelidir.

### 7.2 Rollback Durum Tablosu

| Durum | Aktif Yuva | Sonraki Boot | Sonuç |
|-------|-------------|--------------|-------|
| Güncelleme öncesi | A (good) | A | Normal çalışma |
| B'ye yazıldı, reboot öncesi | A (pending) | A→B dener | B denenir |
| B sağlık kontrolü geçti | B (good) | B | Yeni sürüm aktif |
| B sağlık kontrolü başarısız | A (good) | A | Otomatik rollback |

---

## 8. Güvenli Transport ve Manifest (TLS 1.3)

Firmware paketi, Bölüm 2'deki HTTPS adımı üzerinden güvenli bir kanaldan taşınır. Modern transport, TLS 1.3'tür.

| Katman | Protokol | Amaç |
|--------|----------|------|
| Transport | TLS 1.3 | Gizlilik + bütünlük + kimlik doğrulama |
| Paket şifreleme | AES-256-GCM | Uçtan uca paket şifreleme |
| İmza | RSA-2048 / ECDSA | Firmware bütünlüğü (Bölüm 5) |
| Manifest | JSON/COSE | Sürüm, hedef donanım, bağımlılıklar |

### 8.1 Güncelleme Manifesti

Her firmware paketi, bir manifest ile birlikte taşınır. Manifest, paketin ne olduğunu ve kime uygun olduğunu tanımlar.

| Alan | Açıklama |
|------|----------|
| version | Semantic sürüm + monotonic counter |
| hardware_id | Hedef donanım revizyonu |
| image_size | Firmware boyutu |
| sha256 | Paket checksum'ı (Bölüm 3) |
| signature | İmza (Bölüm 5) |
| min_version | Anti-rollback alt sınırı (Bölüm 7.1) |
| rollout_policy | Kademeli dağıtım oranı (Bölüm 10) |

### 8.2 Transport Güvenliği Notu

TLS 1.3, önceki sürümlere göre daha az handshake round-trip'ı ve zorunlu modern şifreleme sağlar. Ancak TLS yalnız taşıma kanalını korur; firmware'in kendi imzası (Bölüm 5) ayrıca doğrulanmalıdır — transport güvenliği firmware bütünlüğünün yerine geçmez (defense in depth).

---

## 9. Sağlık Kontrolü ve Doğrulama (Post-Update)

Güncelleme sonrası cihaz, yeni firmware'in çalışır durumda olduğunu doğrulamalıdır; aksi halde rollback (Bölüm 7) tetiklenir.

| Kontrol | Yöntem | Başarısızlık |
|---------|--------|--------------|
| Boot kontrolü | Watchdog kick (bkz. [[rtos]] §21) | Rollback |
| Temel servis kontrolü | Kritik servis alive ping | Rollback |
| Ses yolu kontrolü | I2S/DMA heartbeat | Uyarı / safe-mode |
| Bellek bütünlüğü | CRC / test | Rollback |
| Sürüm doğrulama | Boot edilen sürüm = manifest | Rollback |

### 9.1 Sağlık Kontrolü Zamanlaması

Sağlık kontrolü, firmware boot olduktan sonra belirli bir "canary penceresi" içinde yapılır. Pencere içinde beklenmedik yeniden başlatma (reset) veya watchdog tetiklemesi, güncellemeyi başarısız sayar ve eski yuvaya rollback yapar.

---

## 10. Kademeli Dağıtım (Staged Rollout / Canary)

Hatalı firmware'in tüm filoya aynı anda yayılması riskini azaltmak için kademeli dağıtım kullanılır.

| Aşama | Oran | Amaç |
|-------|------|------|
| Canary | %1–5 | Erken sinyal toplama |
| Early | %10–25 | Genişletilmiş doğrulama |
| Broad | %50–90 | Ana yayılım |
| Full | %100 | Tamamlanma |

### 10.1 Kademeli Dağıtım Karar Kapıları

Her aşamada, hata oranı ve cihaz sağlık metrikleri bir eşiğin altındaysa sonraki aşamaya geçilir; eşik aşılırsa dağıtım durdurulur ve geri alınır (rollback, Bölüm 7). Karar kapıları, cihaz telemetrisine (Bölüm 11 Monitoring) dayanır.

### 10.2 Sürüm Ayrıştırma (Phased by Cohort)

Kademeli dağıtım, cihaz kohortlarına göre yapılabilir (örn. donanım revizyonu, bölge, beta-programı). Böylece belirli bir kohorttaki sorun, genel filoyu etkilemeden izole edilir.

---

## 11. Monitoring ve Telemetri

Güncelleme sürecinin sağlığı, telemetri ile izlenir.

| Metrik | Açıklama | Kullanım |
|--------|----------|----------|
| Güncelleme başarı oranı | Başarılı/Toplam | Kademeli dağıtım kapısı |
| Rollback oranı | Geri alınan güncelleme | Hatalı sürüm sinyali |
| Güncelleme süresi | İndirme + yazma + reboot | UX / SLA |
| Boot başarısı | İlk boot'ta başarı | Sağlık kontrolü |
| Hata kodları | Aşama bazlı hata | Teşhis |

### 11.1 Telemetri Gizliliği

Telemetri, cihaz kimliğini deşifre etmeden (pseudonymous) ve minimum düzeyde toplanır. Hassas veri (kullanıcı içeriği, anahtar) telemetriye asla dahil edilmez; bu, çekirdek `AGENTS.md` §5 Security kuralıyla (secret'lar log'a yazılmaz) uyumludur.

---

## 12. Güç-Kesintisi Güvenliği (Power-Fail Safety)

Yazma sırasında güç kesilmesi, güncellemenin en kritik riskidir.

| Tehdit | Önlem |
|--------|-------|
| Yazma yarıda kesilir | A/B — aktif yuva değişmez (Bölüm 6.2) |
| Yarım yazılmış image | İmza + checksum doğrulaması (Bölüm 5) |
| Bootable bayrağı bozuk | Atonom bayrak güncelleme (commit-after-write) |
| Boot döngüsü | Boot contador + recovery (Bölüm 7) |

### 12.1 Atonom Commit

Yeni firmware yazıldıktan sonra "bootable" bayrağı, firmware'in tam yazıldığından emin olunduktan sonra (flush + verify) atılır; bu "commit-after-write" deseni, yarım yazmanın boot edilmesini engeller.

---

## 13. Hata Senaryoları ve Kurtarma

| Senaryo | Olasılık | Sonuç | Kurtarma |
|---------|----------|-------|----------|
| İndirme kesilir | Orta | Güncelleme iptal | Yeniden dene |
| İmza doğrulama başarısız | Düşük | Güncelleme reddi | Eski sürüm kalır |
| Yazma sırasında güç kesilir | Düşük | Yuva değişmez | Aktif yuva devam |
| Yeni firmware boot olmaz | Orta | Boot döngüsü | Watchdog → rollback |
| Sağlık kontrolü başarısız | Orta | Rollback | Eski yuva |
| Anti-rollback ihlali | Düşük | Güncelleme reddi | Eski sürüm kalır |

### 13.1 Kurtarma Hiyerarşisi

```
Normal güncelleme
  → Boot başarısız (watchdog)
    → Recovery boot (scratch image)
      → Rollback (eski yuva)
        → Kullanıcı müdahalesi (safe-mode / USB recovery)
```

---

## 14. Repo Kanıtı

Repo'da OTA/firmware (C++/C) kodu YOK — 2026-10-13 ölçümü: `0` `.cpp/.h/.c` dosyası, `0` `CMakeLists.txt`, `NevaEngine` yok. Bu dosya salt spesifikasyon; kaynak `.ai/electronic/**`. Uydurma `Dosya:/Satır:` referansı yazılmaz.

---

## 15. Web Araştırması

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| MCUboot, güvenli boot loader ve firmware image upgrade (A/B, imza doğrulama) sağlar | https://docs.mcuboot.com/ | 2026-10-13 | HTTP 200 doğrulandı |
| TLS 1.3, güvenli transport için modern handshake ve zorunlu şifreleme tanımlar | https://www.ietf.org/rfc/rfc8446.txt | 2026-10-13 | HTTP 200 doğrulandı |

---

## 16. Kaynakça

1. MCUboot — secure boot loader documentation: https://docs.mcuboot.com/
2. IETF RFC 8446 — The Transport Layer Security (TLS) Protocol Version 1.3: https://www.ietf.org/rfc/rfc8446.txt
3. CoreMusic Vault — `.ai/electronic/firmware/index.md`, `.ai/architecture/07-security/` (driver-signing)
4. İlgili: [[boot-sequence]] · [[rtos]] · [[dsp-firmware]] · [[electronic/firmware/index]]

---

## 17. Terimler ve Kısaltmalar

| Terim | Açıklama |
|-------|----------|
| OTA | Over-The-Air (kablosuz güncelleme) |
| A/B update | Dual-bank, iki yuvalı atomik güncelleme |
| Root of trust | Donanımda yerleşik güven kökü |
| Anti-rollback | Sürüm monotonisi / eski sürüm engelleme |
| Manifest | Firmware paketini tanımlayan meta-veri |
| Canary | Kademeli dağıtımın ilk küçük aşaması |
| Secure boot | İmza zinciriyle güvenli açılış |
| Recovery | Kurtarma image / mod |

---

## 18. Flash Bölümleme (Partition Layout)

A/B güncellemenin gerçekleştirilmesi, flash belleğin bölümlenmesine bağlıdır. Ö tipik bir yerleşim:

| Bölüm | Boyut (tipik) | İçerik | Yazma |
|-------|---------------|--------|-------|
| Boot ROM / loader | Sabit | Donanıma bağlı | Salt-okunur |
| Recovery image | 1× firmware | Kurtarma | Nadiren |
| Slot A | 1× firmware | Aktif/pasif firmware | Güncelleme |
| Slot B | 1× firmware | Aktif/pasif firmware | Güncelleme |
| NVS / config | Küçük | Kalıcı ayar, sayaç | Sık |
| Scratch | Değişken | Geçici tampon | Sık |

### 18.1 Bölümleme Prensibi

| Prensip | Açıklama |
|---------|----------|
| Ayrık yuvalar | A ve B aynı boyutta, birbirinden bağımsız |
| NVS ayrık | Kalıcı ayarlar firmware yuvalarından ayrı (güncelleme silmez) |
| Recovery korumalı | Recovery nadiren yazılır, imzalı |
| Anti-rollback counter | Donanımda/OTP (Bölüm 7.1) |
| Yedek alanı | Boşluk, gelecekteki genişleme için |

### 18.2 NVS (Non-Volatile Settings) Koruma

Güncelleme sırasında kalıcı kullanıcı/cihaz ayarları (NVS) korunmalıdır; firmware yuvası değişse de NVS aynı kalır. Sürüm yükseltmesinde NVS şeması değişirse, migrate edilir (backward-compatible schema). NVS, firmware'in bir parçası olarak paketlenmez.

---

## 19. Bootloader Entegrasyonu

OTA, bootloader ile sıkı entegredir; bootloader güncelleme kararını verir ve yuvayı seçer.

| Bootloader Görevi | İlgili Bölüm |
|-------------------|--------------|
| İmza doğrulama | Bölüm 5 |
| Yuva seçimi (A/B) | Bölüm 6 |
| Anti-rollback kontrolü | Bölüm 7.1 |
| Bootable bayrak okuma | Bölüm 12.1 |
| Boot contador / döngü engelleme | Bölüm 13 |
| Recovery yönlendirme | Bölüm 13.1 |

### 19.1 Boot Seçim Algoritması

```
bootloader başlangıcı
  → aktif yuva bayrağını oku (good / pending / bad)
  → pending ise → yeni yuvayı dene (boot contador++)
  → contador eşiği aşarsa → eski (good) yuvaya dön
  → recovery image yalnız son çare
```

İlgili: [[boot-sequence]] (boot aşamaları).

---

## 20. Kısmi İndirme ve Devam (Resume / Delta Update)

Güncelleme, kesintili ağ koşullarında dayanıklı olmalıdır.

| Teknik | Açıklama | Avantaj |
|--------|----------|---------|
| Chunked download | Paket parçalara bölünür | Kesintide kaldığı yerden devam |
| Resume token | İndirme ilerlemesi saklanır | Tekrar başlamaz |
| Delta (binary diff) | Yalnız değişen baytlar indirilir | Bant genişliği tasarrufu |
| Cache / local staging | İndirilen paket yerelde tutulur | Yeniden doğrulama |
| Retry + backoff | Ağ hatasında üstel geri çekilme | Dayanıklılık |

### 20.1 Delta Update Dikkat

Delta (binary diff) güncellemeleri, patch'in uygulandığı taban firmware'in tam olarak bilinmesini gerektirir; taban sürüm uyuşmazsa delta reddedilir ve tam paket indirilir. Delta, imza doğrulamasını (Bölüm 5) karmaşıklaştırır — imza TAM paket üzerinde doğrulanır, delta üzerinde değil.

---

## 21. Filo (Fleet) Güncelleme Yönetimi

Çok sayıda cihaz, merkezi bir güncelleme yönetimi gerektirir.

| Rol | Sorumluluk |
|-----|-------------|
| Update server | Firmware + manifest barındırır, imzalar |
| Enrollment / device registry | Cihaz envanteri, mevcut sürüm takibi |
| Rollout orchestrator | Kademeli dağıtım kararı (Bölüm 10) |
| Telemetry ingest | Cihaz sağlık metrikleri (Bölüm 11) |
| Rollback coordinator | Hatalı sürüm geri çekme (Bölüm 7) |

### 21.1 Cihaz Kaydı ve Sürüm Takibi

Sunucu, her cihazın mevcut sürümünü ve donanım kimliğini (hardware_id, Bölüm 8.1) bilir; böylece yalnız uygun cihazlara uygun sürümü dağıtır. Uyumsuz donanım revizyonuna yanlış firmware gönderilmez (manifest hardware_id kontrolü).

---

## 22. Güvenlik Tehdit Modeli (Özet)

| Tehdit | Vektör | Önlem | İlgili Bölüm |
|--------|--------|-------|--------------|
| Sahte firmware | Sahte sunucu / MITM | TLS + imza | Bölüm 5, 8 |
| Replay attack | Eski paket yeniden gönderme | Anti-rollback / nonce | Bölüm 7.1 |
| Firmware değiştirme | Paket manipülasyonu | İmza + checksum | Bölüm 3, 5 |
| Boot döngüsü / brick | Hatalı firmware | A/B + watchdog + rollback | Bölüm 6, 13 |
| Eski/zayıf sürüm geri yükleme | Anti-rollback ihlali | Donanımsal sayaç | Bölüm 7.1 |
| Telemetri sızıntısı | Fazla veri toplama | Minimizasyon | Bölüm 11.1 |

### 22.1 Savunma Derinliği (Defense in Depth)

OTA güvenliği tek katmanlı değildir: transport (TLS) → paket imzası → checksum → secure boot zinciri → anti-rollback → post-update sağlık kontrolü. Her katman bağımsızdır; biri ihlal edilse diğeri ayakta kalır. Bu, çekirdek `AGENTS.md` §5 Security (fail-safe, fail-open değil) ile uyumludur.

---

## 23. Uyum ve Referans Standartlar

| Alan | Referans | Not |
|------|----------|-----|
| Transport | TLS 1.3 (RFC 8446) | Bölüm 8 |
| Kripto-primitif | SHA-256, AES-256-GCM, RSA/ECDSA | Bölüm 3, 5 |
| Boot loader | MCUboot (açık kaynak referans) | Bölüm 19 |
| Firmware imzalama | 07-security driver-signing | [[architecture/07-security/driver-signing]] |

> ⚠️ VERIFICATION REQUIRED: CoreMusic OTA implementasyonunun hangi spesifik uyum çerçevesine (ör. sertifikasyon programı) girdiği belgelenmemiştir; bu tablo teknik referansları listeler, uyum beyanı değildir.

---

## 24. Cross-Referanslar ve Yol Haritası

| Konu | İlgili Dosya |
|------|--------------|
| Boot sırası | [[boot-sequence]] |
| RTOS / watchdog | [[rtos]] |
| DSP firmware | [[dsp-firmware]] |
| Firmware genel bakış | [[electronic/firmware/index]] |
| Güvenlik / imzalama | [[architecture/07-security/driver-signing]] |
| ADR-022 / ADR-034 | Bölüm 4 |

### 24.1 Yol Haritası

| Aşama | İçerik | Durum |
|-------|--------|-------|
| Faz 1 | OTA akışı + güvenlik tasarımı (bu dosya) | Tasarım |
| Faz 2 | A/B + anti-rollback implementasyonu | PLANNED (repo kodu YOK) |
| Faz 3 | Kademeli dağıtım + telemetri altyapısı | PLANNED |
| Faz 4 | Donanım üzerinde doğrulama (güç-kesinti testleri) | PLANNED |

---

## 25. Doğrulama ve Test Matrisi

OTA güvenliği, ancak test edilmişse iddia edilebilir. CoreMusic doğrulama matrisi:

| Test | Senaryo | Kabul Kriteri |
|------|---------|----------------|
| Normal güncelleme | İmzalı paket, sağlıklı ağ | Yeni sürüm aktif |
| İmza reddi | Bozuk/sahte imza | Güncelleme reddi, eski sürüm |
| Güç-kesintisi (yazma) | Yazma sırasında reset | Aktif yuva değişmez |
| Güç-kesintisi (commit) | Bayrak öncesi reset | Eski yuva boot |
| Boot döngüsü | Yeni firmware boot olmaz | Watchdog → rollback |
| Sağlık kontrolü başarısız | Canary penceresinde reset | Otomatik rollback |
| Anti-rollback | Eski sürüm paketi | Reddi |
| Ağ kesintisi (resume) | İndirme yarıda | Kal yerden devam |
| Uyumsuz donanım | Yanlış hardware_id | Reddi |

### 25.1 Rollback Test Senaryoları

| # | Senaryo | Beklenen |
|---|---------|----------|
| 1 | Yeni firmware boot olmaz (3 deneme) | Eski yuva boot |
| 2 | Canary penceresinde watchdog | Rollback + log |
| 3 | Sağlık kontrolü timeout | Rollback |
| 4 | NVS migrate hatası | Rollback (NVS korunur) |
| 5 | Rollback sonrası tekrar güncelleme | Başarılı (temiz) |

### 25.2 Test Ortamı

Doğrulama, QEMU/simüle flash + gerçek donanım kombinasyonuyla yapılır. Güç-kesinti testleri, programlanabilir güç rölesiyle yazma/commit anında kesilerek doğrulanır. ⚠️ VERIFICATION REQUIRED: Bu test altyapısı henüz repo'da otomatikleştirilmemiştir; tasarım seviyesindedir.

---

## 26. Enerji ve Ağ Bütçesi

| Kalem | Etki | Azaltma |
|-------|------|---------|
| İndirme enerjisi | Radyo/sayısal aktarım | Delta update (Bölüm 20) |
| Yazma enerjisi | Flash yazma | Minimum yazma |
| Reboot enerjisi | Yeniden açılış | Tek reboot (A/B) |
| Telemetri | Sürekli küçük paket | Toplu/greatest-common |
| Bekleme | Güncelleme penceresi | Düşük güç penceresi |

### 26.1 Güncelleme Zamanlaması

Kademeli dağıtım (Bölüm 10) ve güncelleme penceresi, cihazın düşük güç / şebeke-available olduğu zamanlara planlanır; böylece kullanıcı denetimi ve enerji bütçesi korunur.

---

## 27. Özet ve Anahtar Kararlar

| # | Karar | Dayanak |
|---|-------|---------|
| 1 | A/B dual-bank atomik güncelleme | Güç-kesintisi dayanıklılığı (Bölüm 6) |
| 2 | İmza zinciri + root of trust | Sahte firmware reddi (Bölüm 5) |
| 3 | Anti-rollback (donanımsal sayaç) | Eski sürüm engelleme (Bölüm 7.1) |
| 4 | TLS 1.3 transport | Güvenli kanal (Bölüm 8) |
| 5 | Post-update sağlık kontrolü | Otomatik rollback (Bölüm 9) |
| 6 | Kademeli dağıtım | Filo risk yönetimi (Bölüm 10) |
| 7 | Savunma derinliği | Çok katmanlı güvenlik (Bölüm 22.1) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
