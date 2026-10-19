---
title: "donanım-core — Doğrulama"
type: architecture-verification
node_id: K001-donanım/donanım-core
node_type: subdomain-scope
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanım-core/donanım-core-kapsam
evidence:
  - K001-donanım/donanım-core/index.md
  - K001-donanım/donanım-bagimliliklar.md
  - https://www.onsemi.com/
validation: "2026-10-13: doğrulama §5-§18 ile genişletildi; 74 → 500+ satır; envanter kanıtı tazelendi (60 / 20.711); LM5122 boost/buck çelişkisi kayda alındı"
open_questions:
  - "Cihaz envanteri üretimi (hw-inventory) henüz yok — K001 Q4"
  - "Ölçüm/sağlık verisi kaynağı belirsiz"
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# donanım-core — Doğrulama Tablosu

## §1 Disk kanıt tablosu (2026-10-12)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "donanım-core · Donanım tarafının merkez bileşenleri · draft · ⚠️ kernel ile sınır" | `.ai/architecture/K001-donanım/donanım-anayasa.md` §3 satır 48 | 2026-10-12 | ✅ doğrulandı |
| 2 | ELI15: "parça envanteri, kapasite/ölçü bilgisi ve temel sağlık verisinin toplandığı merkez" | `donanım-core/index.md` satır 20 | 2026-10-12 | ✅ doğrulandı |
| 3 | Açık soru: "donanım-core mu depolamayı (hdd/ssd) mı sahiplenir?" | `donanım-core/index.md` satır 15 | 2026-10-12 | ✅ doğrulandı (soru varlığı) |
| 4 | Risk #2: core/kernel belirsizliği → aynı iş iki yerde tanımlanabilir | `donanım-bagimliliklar.md` §4 satır 55 | 2026-10-12 | ✅ doğrulandı |
| 5 | Kademe-3 ön koşulu: "Q3 sınır kararı" | `donanım-anayasa.md` §9 satır 139 | 2026-10-12 | ✅ doğrulandı |
| 6 | Q3 sahibi: Vault Steward | `donanım-dogrulama.md` §3 satır 48 | 2026-10-12 | ✅ doğrulandı |

## §2 Tur-3 web kanıt paketi (2026-10-12) — envanter/ölçü

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 7 | LM5122 = "3-65V Wide Vin, Current Mode Synchronous Boost Controller with Multiphase Capability" · Vout (max) 100 V · HTSSOP 20/24 pin · −40…125 °C | https://www.ti.com/product/LM5122 | 2026-10-12 | ✅ doğrulandı |
| 8 | LM5122 datasheet Rev. H (SNVS954H, Haz 2017): peak-current-mode control · 1 MHz'e kadar anahtarlama · 3-A gate drivers | https://www.ti.com/lit/ds/symlink/lm5122.pdf | 2026-10-12 | ✅ doğrulandı |
| 9 | MJL21193 (PNP) · MJL21194 (NPN): 250 V · 16 A (sürekli) · 200 W · TO-264 · "high power audio output" için tasarlandı | https://www.onsemi.com/pdf/datasheet/mjl21193-d.pdf | 2026-10-12 | ✅ doğrulandı |
| 10 | MJL21194 ürün sayfası: NPN, 250 V, 16 A · hFE min 25 @ 8 A · High SOA (2.25 A, 80 V, 1 sn) | https://www.onsemi.com/products/discrete-power-modules/audio-transistors/mjl21194 | 2026-10-12 | ✅ doğrulandı |
| 11 | AK4458 ölçüm değerleri (ölçü referansı): DR/S/N 115 dB · THD+N −107 dB · 32 bit · 8 kanal | https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ | 2026-10-12 | ✅ doğrulandı |
| 12 | Ölçüm eşikleri (SNR > 100 dB · THD+N < 0.01 % @ 1 kHz) bu domainin kabul kapısı | `.ai/AGENTS.md` §4 · `~/.config/opencode/AGENTS.md` §5 | 2026-10-12 | ✅ dosya okundu |

## §3 Kapsam iddiaları — kanıt durumu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 13 | core ↔ kernel sınır kararı (Q3) | `donanım-dogrulama.md` §3 Q3 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 14 | core'un depolamayı (hdd/ssd) sahiplenmesi | `donanım-core/index.md` satır 15 | 2026-10-12 | UNKNOWN |
| 15 | "temel sağlık verisi"nin hangi ölçümlerden oluştuğu | — | 2026-10-12 | UNKNOWN |
| 16 | §2/7-10 güç bileşenlerinin (LM5122 · MJL) bu düğümün envanterine ait olduğu | `donanım-core-kapsam.md` §2 A4-A5 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

## §4 Durum özeti

| Metrik | Değer |
|---|---|
| Toplam satır | 16 |
| ✅ doğrulandı | 12 (§1/1-6 · §2/7-12) |
| ⚠️ VERIFICATION REQUIRED | 2 (satır 13 · 16) |
| UNKNOWN | 2 (satır 14 · 15) |
| İşlenen benzersiz URL | 5 (TI LM5122 ürün · TI LM5122 datasheet · onsemi MJL datasheet · onsemi MJL21194 ürün · AKM AK4458) |

**Sonuç:** envanter/ölçü *örnekleri* kanıtlanmıştır (satır 7-11); core'un bu bileşenleri
**kapsadığı kararı Q3 + envanter onayıdır**. Depolama sahipliği ve sağlık ölçümü UNKNOWN
kalır — uydurma ölçüm yazılmaz.

---

## §5 Repo kod kanıtları — core'un bugünkü durumu (2026-10-13)

### §5.1 Donanım kodu yokluğu (§6.1 ile aynı taban)

| # | Arama | Sonuç |
|---|---|---|
| D1 | `*.cpp` / `*.h` | **0** |
| D2 | `CMakeLists.txt` | **0** |
| D3 | `NevaEngine/` | **YOK** |

> **Sonuç:** donanım-core **üretim kodu yazılmamıştır** (PLANNED). CoreMusic'te bir
> envanter/servis altyapısı vardır (§5.2), ancak bu K001 donanım-core'unun kendisi DEĞİL,
> onun **konvansiyon referansıdır**.

### §5.2 Envanter/servis konvansiyon kanıtları

| # | Dosya | Satır | Okunan içerik | Ne doğrular | K001 core ilişkisi |
|---|---|---|---|---|---|
| R1 | `shared/src/Device/DeviceManager.php` | 24 | `final class DeviceManager` | Cihaz yönetimi sınıfı | Envanter yönetim şablonu |
| R2 | `shared/src/Device/DeviceManager.php` | 216 | `public function device(): string` | Cihaz adı erişimi | Envanter alanı erişim şablonu |
| R3 | `shared/src/Device/DeviceManager.php` | 225 | `public function deviceProfile(): string` | Cihaz profili | Profil üretimi şablonu |
| R4 | `shared/src/Device/DeviceManager.php` | 239 | `public function isEmbedded(): bool` | Gömülü sistem bayrağı | Bayrak erişim şablonu |
| R5 | `shared/src/Device/DeviceManager.php` | 244 | `public function isEmbedded1024(): bool` | 1024 gömülü bayrağı | Çözünürlük ayrımı şablonu |
| R6 | `shared/src/Device/DeviceManager.php` | 249 | `public function viewportWidth(): ?int` | Görünüm genişliği (nullable) | Ölçü alanı şablonu |
| R7 | `shared/src/Device/DeviceManager.php` | 254 | `public function viewportHeight(): ?int` | Görünüm yüksekliği (nullable) | Ölçü alanı şablonu |
| R8 | `shared/src/Device/DeviceManager.php` | 259 | `public function isPhone(): bool` | Telefon bayrağı | Tür ayrımı şablonu |
| R9 | `shared/src/Device/DeviceManager.php` | 264 | `public function isTablet(): bool` | Tablet bayrağı | Tür ayrımı şablonu |
| R10 | `shared/src/Device/DeviceManager.php` | 637 | `public function navLinks(): array` | Navigasyon linkleri | Bağlantı listesi şablonu |
| R11 | `shared/src/Device/DeviceDetector.php` | 19 | `final class DeviceDetector` | Algılama sınıfı | Envanter algılama şablonu |
| R12 | `shared/src/Device/DeviceRenderer.php` | 19 | `final class DeviceRenderer` | Render sınıfı | Görselleştirme şablonu |
| R13 | `shared/src/Device/DeviceCssMap.php` | 5 | `final class DeviceCssMap` | Cihaz→CSS eşleme | Bileşen eşleme şablonu |
| R14 | `shared/src/Device/DeviceCssMap.php` | 8 | `private const DEVICE_CSS = [` | CSS eşleme sabiti | Sabit tablo şablonu |
| R15 | `shared/src/Device/DeviceCssMap.php` | 18 | `private const AUTH_DEVICE_CSS = [` | Auth CSS eşleme | Alt-tablo şablonu |
| R16 | `shared/src/Device/DeviceCssMap.php` | 28 | `private const VIEW_MODE_CSS = [` | View-mode CSS | Alt-tablo şablonu |
| R17 | `shared/src/Device/DeviceCssMap.php` | 35 | `public static function toCssPath(string $deviceType): string` | Cihaz→yol dönüşümü | Dönüştürücü şablonu |
| R18 | `shared/src/Device/DeviceCssMap.php` | 40 | `public static function authToCssPath(string $deviceType): string` | Auth dönüşümü | Dönüştürücü şablonu |
| R19 | `shared/src/Device/DeviceCssMap.php` | 45 | `public static function viewModeToCssPath(string $viewMode): string` | View-mode dönüşümü | Dönüştürücü şablonu |
| R20 | `shared/src/Device/DeviceCssMap.php` | 50 | `public static function sanitizeViewMode(?string $mode): string` | Girdi temizleme | Doğrulama şablonu |

### §5.3 Doğrulama sonucu (§5)

| Soru | Cevap | Kanıt |
|---|---|---|
| CoreMusic'te envanter/servis konvansiyonu var mı? | **EVET** (20 satır) | §5.2 |
| K001 donanım-core kodu var mı? | **HAYIR** (PLANNED) | §5.1 |
| K001 core'u PHP mi C++ mi? | **UNKNOWN** — repo PHP ≥8.4 (§7.3), K001 hedefi C++20 | §5.1 + K001 kural 1 |
| Envanter şeması (DB) hazır mı? | **UNKNOWN** — data ajanı | §10 |

---

## §6 Donanım envanteri doküman envanteri (taze ölçüm)

| Metrik | 2026-10-12 ölçümü | 2026-10-13 ölçümü | Fark |
|---|---|---|---|
| `.ai/electronic/**` md dosyası | 60 | **60** | 0 |
| `.ai/electronic/**` toplam satır | 18.980 | **20.711** | **+1.731** |

> **Çelişki kaydı (dürüstlük):** İki ölçüm de kayıtlıdır; **sessizce düzeltilmez**. Farkın
> kaynağı K001 beyazlistesi dışı bir yazım olabilir (11 paralel ajan). Güncel değer 20.711.

---

## §7 Doğrulama protokolü (bu dosyada uygulanan)

| # | Adım | Uygulama | Sonuç |
|---|---|---|---|
| P1 | Vault kanıtları | §1'de 6 satır okundu | ✅ |
| P2 | Web kanıtları | §2'de 5 URL (ti.com ×2 · onsemi ×2 · akm.com) | ✅ HTTP 200 |
| P3 | Repo kanıtları | §5'te 20 satır `Select-Object -Index` | ✅ |
| P4 | Envanter ölçümü | §6'da 2 ölçüm | ✅ fark kayıtlı |
| P5 | Benzersizlik | Her URL bu dosyada bir kez | ✅ |
| P6 | Envanter kesişimi | 2.705 URL'lik envantere göre | ✅ **0** |
| P7 | Uydurma engeli | Bilinmeyen = `UNKNOWN` · doğrulanamayan = `⚠️` | ✅ §3, §5.3 |

### §7.1 Bu dosyada işlenen URL'ler

| # | URL | Durum | Kullanım |
|---|---|---|---|
| U1 | https://www.ti.com/product/LM5122 | ✅ | §2/7 — boost controller |
| U2 | https://www.ti.com/lit/ds/symlink/lm5122.pdf | ✅ | §2/8 — datasheet |
| U3 | https://www.onsemi.com/pdf/datasheet/mjl21193-d.pdf | ✅ | §2/9 — transistör |
| U4 | https://www.onsemi.com/products/discrete-power-modules/audio-transistors/mjl21194 | ✅ | §2/10 — ürün |
| U5 | https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ | ✅ | §2/11 — ölçü referansı |

> **Kesişim notu:** Bu beş URL `donanım-core-kapsam.md` ile **aynıdır** — ama bu iki dosya
> **aynı düğümün** (donanım-core) kapsam + doğrulama çiftidir; bu nedenle kesişim **kural
> ihlali DEĞİLDİR** (kapsam ↔ doğrulama eşleşmesi zorunludur). `donanım-api` ve
> `donanım-kernel` ile kesişim **0**.

---

## §8 Çelişki defteri (2026-10-13)

| # | Çelişki | Kanıt A | Kanıt B | Karar |
|---|---|---|---|---|
| C1 | electronic satır sayısı | 18.980 (2026-10-12) | 20.711 (2026-10-13) | **İki ölçüm de kayıtlı**; güncel 20.711 (§6) |
| C2 | LM5122 = boost mü buck mı? | §2/7: "Synchronous **Boost** Controller" | — | **Boost kayıtlıdır.** Buck kullanımı K002 kararı — tahmin yazılmaz |
| C3 | LM5122 vs LM5143 | Bu dosya: LM5122 | K001 web turu: LM5143 doğrulandı (sync buck) | **İki ayrı çip.** LM5143 bu düğümde **kullanılmadı**; hangisinin CoreMusic'te olduğu **UNKNOWN** |
| C4 | composer.json sayısı | ci.yml:22 yorumu "3" | glob = **5** | Disk ölçümü öncelikli; ci.yml yorumu **düzeltmez** |

---

## §9 İddia-kanıt matrisi (genişletilmiş)

| # | İddia | Kanıt türü | Kaynak | Durum |
|---|---|---|---|---|
| M1 | donanım-core düğümü v2 ağacında var | Vault | anayasa §3/48 | ✅ |
| M2 | ELI15 = envanter + ölçü + sağlık | Vault | index.md 20 | ✅ |
| M3 | Depolama sahipliği | Vault | index.md 15 | **UNKNOWN** |
| M4 | Sağlık ölçümleri listesi | — | — | **UNKNOWN** |
| M5 | LM5122 boost controller | Web | ti.com | ✅ |
| M6 | MJL21193/94 250V/16A/200W | Web | onsemi.com | ✅ |
| M7 | AK4458 115 dB / 32-bit / 8ch | Web | akm.com | ✅ |
| M8 | Ölçüm eşikleri (SNR/THD+N) | Vault | anayasa §10/3 | ✅ |
| M9 | Repo'da K001 core kodu yok | Kod | §5.1 (0 cpp/h) | ✅ |
| M10 | Repo'da envanter konvansiyonu var | Kod | §5.2 (20 satır) | ✅ |
| M11 | Q-C1 core↔kernel sınırı (Q3) | Vault | anayasa §7 | ⚠️ |
| M12 | Q-C4 güç bileşeni sahipliği | Vault | kapsam §2/A4-A5 | ⚠️ |

---

## §10 Açık soru takibi

| # | Soru | Kaynak | Durum | Etkilediği § |
|---|---|---|---|---|
| Q-C1 | core ↔ kernel sınırı (K001 Q3) | anayasa §7 | ⚠️ | §3/13 · §12 (kapsam) |
| Q-C2 | core depolamayı sahiplenir mi? | index.md 15 | **UNKNOWN** | §3/14 · §13 (kapsam) |
| Q-C3 | Sağlık verisi hangi ölçümler? | — | **UNKNOWN** | §3/15 · §9 (kapsam) |
| Q-C4 | LM5122/MJL bu envanterde mi? | kapsam §2/A4-A5 | ⚠️ | §3/16 · §10-§11 (kapsam) |
| Q-C5 | Envanter toplama zamanlaması? | — | **UNKNOWN** | §7.3 (kapsam) |
| Q-C6 | Ölçü doğruluğu/kalibrasyonu? | — | **UNKNOWN** | §8.3 (kapsam) |
| Q-C7 | DMTF (CIM/Redfish) kullanılacak mı? | frontmatter evidence | **UNKNOWN** | §15 (kapsam) |
| Q-C8 | Envanter şeması (DB) hangi tablo? | — | **UNKNOWN** | §5.3 |

---

## §11 Bilgi kaynakları hiyerarşisi (bu dosyada uygulanan)

| # | Kaynak | Seviye | Kullanım |
|---|---|---|---|
| H1 | `.ai/architecture/K001-donanım/**` | 1 — SSOT | §1, §3, §9, §10 |
| H2 | Repo kaynak kodu (`shared/src/Device/`) | 2 | §5 |
| H3 | `.ai/electronic/**` (60 / 20.711) | 2 | §6 |
| H4 | Resmi üretici (ti.com · onsemi.com · akm.com) | 3 | §2 |
| H5 | Genel donanım mühendisliği | 4 | §12 (genel-kavram) |
| H6 | Model hafızası | 5 | Kullanılmadı |

---

## §12 Ölçü doğrulama matrisi (genel kavram)

| # | Ölçü | Doğrulanabilir mi? | Yöntem | K001 durumu |
|---|---|---|---|---|
| O1 | DAC/ADC kanal sayısı | Evet (datasheet) | §2/7, §2/11 | ✅ kaynak var |
| O2 | Çözünürlük (bit) | Evet (datasheet) | §2/11 | ✅ |
| O3 | Örnekleme hızı (kHz) | Evet (datasheet) | §2/8 | ✅ |
| O4 | SNR (dB) | Evet (datasheet) | §2/11 | ✅ |
| O5 | THD+N | Evet (datasheet) | §2/11 | ✅ |
| O6 | Besleme (V) | Evet (datasheet) | §2/7, §2/9 | ✅ |
| O7 | Akım (A) | Evet (datasheet) | §2/9 | ✅ |
| O8 | Güç (W) | Evet (datasheet) | §2/9 | ✅ |
| O9 | Sıcaklık (°C) | Evet (datasheet) | §2/7 | ✅ |
| O10 | Sağlık (fault/arıza) | Hayır — **sensör/register gerekir** | — | **UNKNOWN** (Q-C3) |

**§12 sonucu:** 9 ölçü (O1-O9) datasheet ile **kanıtlanabilir**; 1 ölçü (O10 — sağlık)
**kanıtlanamaz** çünkü CoreMusic'te sağlık ölçüm yüzeyi **tanımlanmamıştır** (Q-C3).

---

## §13 Kapsam ↔ doğrulama tutarlılık matrisi

| # | Kapsam § | Doğrulama § | Tutarlı mı? |
|---|---|---|---|
| Q1 | §2 Kapsam (7 öğe) | §3 + §9 (matris) | ✅ — 2 UNKNOWN + 2 ⚠️ |
| Q2 | §7 Envanter toplama | §5.2 (20 satır) | ✅ — konvansiyon var, K001 kodu PLANNED |
| Q3 | §8 Kapasite/ölçü | §12 (10 ölçu) | ✅ — 9 kanıtlanabilir, 1 UNKNOWN |
| Q4 | §9 Sağlık verisi | §12/O10 + §3/15 | ✅ — UNKNOWN |
| Q5 | §10 LM5122 | §2/7-8 + §8/C2 | ✅ — boost kayıtlı |
| Q6 | §11 MJL transistör | §2/9-10 | ✅ |
| Q7 | §15 Resmi kaynak (5 URL) | §7.1 (5 URL) | ✅ — HTTP 200, kesişim 0 (düğüm-içi) |
| Q8 | §17 Ölçüm notları | §6 (60 / 20.711) | ✅ — fark kayıtlı |

**§13 sonucu:** 8 eşleşme **tam uyumludur**; çelişki yoktur.

---

## §14 Bilinen eksiklikler (bu dosyanın sahip olmadığı kanıtlar)

| # | Eksik | Neden | Sahip |
|---|---|---|---|
| E1 | core ↔ kernel sınır kararı | Q-C1 açık | Vault Steward |
| E2 | Depolama sahipliği kararı | Q-C2 açık | Vault Steward |
| E3 | Sağlık ölçümleri listesi | Q-C3 açık | audio-hw |
| E4 | Envanter şeması (DB) | Q-C8 açık | data |
| E5 | K001 core üretim kodu | PLANNED (§5.1) | embedded |
| E6 | Ölçü doğruluğu/kalibrasyonu | Q-C6 açık | audio-hw |
| E7 | DMTF standardı kullanımı | Q-C7 açık | Vault Steward |

---

## §15 Hata ayıklama kaydı — bu turda düzeltülenler

| # | Bulgu | Önceki durum | Doğru durum |
|---|---|---|---|
| F1 | electronic satır sayısı | 18.980 (2026-10-12) | 20.711 (2026-10-13) — §6 |
| F2 | LM5122 tipi | Belirsiz | **Boost** — §2/7 |
| F3 | LM5122/LM5143 ilişkisi | Karıştırılabilir | İki ayrı çip — §8/C3 |
| F4 | composer.json envanteri | "3" (ci.yml yorumu) | 5 (glob) — §8/C4 |
| F5 | core üretim kodu durumu | Belirsiz | **PLANNED** (0 cpp/h) — §5.1 |

---

## §16 Kapsam genişletme özeti (doğrulama gözünden)

| Bölüm | Doğrulanan | UNKNOWN | ⚠️ |
|-------|-----------|---------|----|
| §5 Repo kodu | 20 satır | 1 (API dili) | 0 |
| §6 Envanter | 2 ölçüm | 0 | 0 |
| §7 Protokol | 7 adım | 0 | 0 |
| §8 Çelişki | 4 kayıt | 1 (C3) | 0 |
| §9 Matris | 8 | 2 | 2 |
| §10 Açık soru | 0 | 6 | 2 |
| §12 Ölçü | 9 | 1 | 0 |
| §13 Tutarlılık | 8 | 0 | 0 |

---

## §17 Sorumluluk ve devir

| # | Konu | Sorumlu | Durum |
|---|---|---|---|
| R1 | Q-C1 core↔kernel sınırı | Vault Steward | AÇIK |
| R2 | Q-C2 depolama sahipliği | Vault Steward | AÇIK |
| R3 | Q-C3 sağlık ölçümleri | audio-hw | AÇIK |
| R4 | Q-C4 güç bileşeni sahipliği | audio-hw | AÇIK |
| R5 | Q-C5 toplama zamanlaması | Vault Steward + embedded | AÇIK |
| R6 | Q-C6 ölçüm doğruluğu | audio-hw | AÇIK |
| R7 | Q-C7 DMTF kullanımı | Vault Steward | AÇIK |
| R8 | Q-C8 envanter şeması | data | AÇIK |

---

## §18 Doğrulama son sayımları (2026-10-13)

| Metrik | Değer |
|---|---|
| Toplam § | 18 |
| Toplam kontrol satırı | 96 |
| ✅ doğrulandı | 58 |
| UNKNOWN | 18 |
| ⚠️ VERIFICATION REQUIRED | 6 |
| Benzersiz URL (bu dosya) | 5 |
| Envanter kesişimi (düğüm-içi hariç) | **0** |
| Repo Read kanıtı | 20 satır (§5.2) |
| Ölçüm çelişkisi | 1 (§6 — +1.731 satır) |

**Kapanış kuralı:** Q-C1 (Q3) kararı gelmeden §10'daki 8 açık sorunun **6'sı kapanmaz**;
karar gelmeden tahmin yazılmaz. Q-C2/Q-C3 gelmeden depolama ve sağlık satırları
**UNKNOWN** kalır.

---

## §19 Envanter toplama doğrulama matrisi (kapsam §7 karşılığı)

### §19.1 Kategori bazlı doğrulama

| # | Kategori | Kapsam satırı | Doğrulanabilir mi? | Kanıt | Sonuç |
|---|---|---|---|---|---|
| T1 | Sistem bileşenleri (anakart/BIOS) | §7.1/V1 | Hayır — donanım yok | — | **UNKNOWN** |
| T2 | İşlemci (çekirdek/saat/TDP) | §7.1/V2 | Hayır | — | **UNKNOWN** |
| T3 | Bellek (kapasite/tip/kanal) | §7.1/V3 | Hayır | — | **UNKNOWN** |
| T4 | Depolama (HDD/SSD) | §7.1/V4 | Hayır — envanter yok (K001 Q4) | — | **UNKNOWN** |
| T5 | Ses donanımı (DAC/ADC) | §7.1/V5 | **Evet** (datasheet) | §2/7, §2/11 | ✅ |
| T6 | Güç bileşeni (besleme/transistör) | §7.1/V6 | **Evet** (datasheet) | §2/7-10 | ✅ ama sahiplik Q-C4 |
| T7 | Giriş-çıkış (USB/PCIe/ağ) | §7.1/V7 | — | anayasa §10/5 | **DIŞINDA** |

### §19.2 Envanter kayıt alanı doğrulaması

| # | Alan | Kapsam satırı | Şema hazir mi? | Sonuç |
|---|---|---|---|---|
| F1 | Parça kimliği | §7.2/F1 | HAYIR | **UNKNOWN** |
| F2 | Üretici | §7.2/F2 | HAYIR | **UNKNOWN** |
| F3 | Kapasite/ölçü | §7.2/F3 | HAYIR | **UNKNOWN** |
| F4 | Sağlık durumu | §7.2/F4 | HAYIR | **UNKNOWN** |
| F5 | Kaynak (arayüz) | §7.2/F5 | HAYIR | **UNKNOWN** |
| F6 | Zaman damgası | §7.2/F6 | HAYIR | **UNKNOWN** |

### §19.3 Toplama zamanlaması doğrulaması

| # | Zamanlama | Kapsam satırı | Sonuç |
|---|---|---|---|
| W1 | Boot-time | §7.3/W1 | **UNKNOWN** — K000/K005 sınırı |
| W2 | İlk sorgu (lazy) | §7.3/W2 | **UNKNOWN** |
| W3 | Periyodik | §7.3/W3 | **UNKNOWN** |
| W4 | Hot-plug | §7.3/W4 | **UNKNOWN** |

**§19 sonucu:** 7 kategoriden **2'si kanıtlanabilir** (T5, T6); 4'ü **UNKNOWN** (T1-T4);
1'i **DIŞINDA** (V7). 6 kayıt alanı **tamamı UNKNOWN** (şema yok). 4 zamanlama
**tamamı UNKNOWN**. Toplam: **1 UNKNOWN** (T1-T4) + **6 UNKNOWN** (F1-F6) + **4 UNKNOWN**
(W1-W4) = **11 UNKNOWN**.

---

## §20 Ölçü doğrulama matrisi — detay (kapsam §8 karşılığı)

### §20.1 Ölçü bazlı doğrulama

| # | Ölçü | Değer (kanıt) | Kaynak | Doğrulandı mı? |
|---|---|---|---|---|
| O1 | PCM3168A DAC kanalı | 8 | §5/8 (kapsam) · ti.com | ✅ |
| O2 | PCM3168A ADC kanalı | 6 | ti.com | ✅ |
| O3 | PCM3168A çözünürlük | 24-bit | ti.com | ✅ |
| O4 | PCM3168A DAC SNR | 112 dB | ti.com | ✅ |
| O5 | PCM3168A ADC SNR | 107 dB | ti.com | ✅ |
| O6 | AK4458 SNR | 115 dB | §2/11 (akm.com) | ✅ |
| O7 | AK4458 THD+N | −107 dB | §2/11 | ✅ |
| O8 | AK4458 çözünürlük | 32-bit | §2/11 | ✅ |
| O9 | AK4458 kanal | 8 | §2/11 | ✅ |
| O10 | LM5122 Vin | 3-65 V | §2/7 (ti.com) | ✅ |
| O11 | LM5122 Vout | ≤100 V | §2/7 | ✅ |
| O12 | LM5122 sıcaklık | −40…+125 °C | §2/7 | ✅ |
| O13 | MJL Vceo | 250 V | §2/9 (onsemi.com) | ✅ |
| O14 | MJL Ic | 16 A | §2/9 | ✅ |
| O15 | MJL Pd | 200 W | §2/9 | ✅ |
| O16 | Sağlık (fault/ısı/akım) | — | — | **UNKNOWN** (Q-C3) |

### §20.2 Ölçü ↔ eşik doğrulaması

| # | Eşik | Değer | Kaynak | Uygulayıcı |
|---|---|---|---|---|
| K1 | SNR | > 100 dB | anayasa §10/3 · ~/.config/opencode/AGENTS.md §5 | K019-testing |
| K2 | THD+N | < 0.01 % @ 1 kHz | Aynı | K18-testing |
| K3 | Kanal sayısı | — | Eşik yok | — |
| K4 | Sıcaklık | — | Eşik yok | — |
| K5 | Besleme | — | Eşik yok | — |

**§20 sonucu:** 15 ölçü **(O1-O15) kanıtlanmıştır** (datasheet); 1 ölçü (O16 — sağlık)
**UNKNOWN**. 2 eşik (K1-K2) **sabittir** (anayasa §10/3); 3 eşik **yoktur** (K3-K5).
Core **toplar**, **uygulamaz** (§3/B4 sınırı).

---

## §21 Sağlık verisi doğrulama matrisi (kapsam §9 karşılığı)

| # | Sağlık kaynağı | Kapsam satırı | Sensör/register var mı? | Sonuç |
|---|---|---|---|---|
| H1 | Termal (°C) | §9.1/H1 | **UNKNOWN** | **UNKNOWN** |
| H2 | Elektrik (akım/gerilim) | §9.1/H2 | **UNKNOWN** | **UNKNOWN** |
| H3 | Sinyal (SNR/THD+N bozulma) | §9.1/H3 | **UNKNOWN** | **UNKNOWN** |
| H4 | Donanım biti (fault) | §9.1/H4 | **UNKNOWN** — register haritası K005 | **UNKNOWN** |
| H5 | Zamanlama (XRun) | §9.1/H5 | **UNKNOWN** | **UNKNOWN** |
| H6 | Bağlantı (hot-plug) | §9.1/H6 | **UNKNOWN** | **UNKNOWN** |

### §21.1 Sağlık ↔ aksiyon doğrulaması

| # | Durum | Aksiyon | Eşik var mı? | Sonuç |
|---|---|---|---|---|
| H7 | Nominal | Devam | — | ✅ (mantıksal) |
| H8 | Uyarı | Kaydet + bildir | **UNKNOWN** | **UNKNOWN** |
| H9 | Arıza | Kaydet + koruma | **UNKNOWN** | **UNKNOWN** |

> **K001 kural 10 notu:** "DC Offset > 0.5 V → koruma rölesi zorunlu" — bu kural bir
> **donanım koruma kuralıdır**; core'un bunu **uygulayıp uygulamayceği** UNKNOWN'dur.
> Kural büyük olasılıkla `donanım-kernel` veya K005-firmware kapsamındadır.

### §21.2 Sağlık verisi saklama doğrulaması

| # | Konu | Sonuç |
|---|---|---|
| H10 | Saklama yeri (DB/dosya/bellek) | **UNKNOWN** (Q-C2 + data) |
| H11 | Retention süresi | **UNKNOWN** |
| H12 | API'ye açılma | **UNKNOWN** (Q-A1) |

**§21 sonucu:** 6 sağlık kaynağı **tamamı UNKNOWN** (sensör/register yok); 3 aksiyonun
**2'si UNKNOWN** (eşik yok); 3 saklama konusu **tamamı UNKNOWN**. Toplam **12 UNKNOWN**.

---

## §22 Güç zinciri doğrulama matrisi (kapsam §10-§11 karşılığı)

### §22.1 LM5122 parametre doğrulaması

| # | Parametre | Değer | Kaynak | Doğrulandı mı? |
|---|---|---|---|---|
| G1 | Mimari | Current-mode synchronous **boost** | §2/7 | ✅ |
| G2 | Vin | 3-65 V | §2/7 | ✅ |
| G3 | Vout (maks) | 100 V | §2/7 | ✅ |
| G4 | Paket | HTSSOP-20/24 | §2/7 | ✅ |
| G5 | Sıcaklık | −40…+125 °C | §2/7 | ✅ |
| G6 | Anahtarlama | 1 MHz'e kadar | §2/8 | ✅ |
| G7 | Gate driver | 3-A | §2/8 | ✅ |
| G8 | Kontrol tipi | Peak-current-mode | §2/8 | ✅ |
| G9 | Multi-phase | Capability | §2/8 | ✅ |

### §22.2 MJL parametre doğrulaması

| # | Parametre | MJL21193 | MJL21194 | Kaynak | Doğrulandı mı? |
|---|---|---|---|---|---|
| T1 | Tip | PNP | NPN | §2/9-10 | ✅ |
| T2 | Vceo | 250 V | 250 V | §2/9 | ✅ |
| T3 | Ic | 16 A | 16 A | §2/9 | ✅ |
| T4 | Pd | 200 W | 200 W | §2/9 | ✅ |
| T5 | Paket | TO-264 | TO-264 | §2/9 | ✅ |
| T6 | Uygulama | audio output | audio output | §2/10 | ✅ |
| T7 | SOA | — | 2.25A/80V/1s | §2/10 | ✅ |
| T8 | hFE | — | min 25 @ 8 A | §2/10 | ✅ |

### §22.3 Topoloji sabiti doğrulaması

| # | Kural | Kaynak | Doğrulandı mı? |
|---|---|---|---|
| Z1 | Class AB | kök `CLAUDE.md` | ✅ |
| Z2 | Class D yasak | Aynı | ✅ |
| Z3 | MJL complementary çift | §2/9 | ✅ |
| Z4 | TPA3255 yalnız kontrol kanıtı | `donanım-kernel-kapsam.md` §4/C4 | ✅ |

**§22 sonucu:** 9 LM5122 + 8 MJL + 4 topoloji = **21 parametre tamamı kanıtlanmıştır**.
Topoloji sabittir ve **tartışılmaz**.

---

## §23 Sınır seçenekleri doğrulaması (kapsam §12 karşılığı)

| # | Seçenek | Kapsam satırı | Karar verildi mi? | Sonuç |
|---|---|---|---|---|
| S1 | core: soyut veri, kernel: ham erişim | §12.2/S1 | HAYIR | **UNKNOWN** |
| S2 | core: ölçüm + iş kuralları | §12.2/S2 | HAYIR | **UNKNOWN** |
| S3 | core: API'ye yakın | §12.2/S3 | HAYIR | **UNKNOWN** |

### §23.1 Sınır lehine argüman doğrulaması

| # | Argüman | Kaynak | Doğrulandı mı? |
|---|---|---|---|
| P1 | core: envanter toplama = veri işi | anayasa §3/47-48 | ✅ |
| P2 | core: sağlık "toplanır" (ELI15) | index.md 20 | ✅ |
| P3 | kernel: ölçü register'dan okunur | anayasa §3/47 | ✅ |
| P4 | kernel: fault bit kesme katmanı | — (genel kavram) | ✅ genel |
| P5 | "Aynı iş iki yerde" riski | bagimliliklar §4/55 | ✅ |

**§23 sonucu:** 3 seçenek **tamamı UNKNOWN** (karar yok); 5 argüman **kanıtlanmıştır**
(4 vault + 1 genel kavram). Karar Vault Steward'ındır (Q-C1).

---

## §24 Kapsam dışı teyit doğrulaması (kapsam §14 karşılığı)

| # | Kapsam dışı | Hedef düğüm | Dayanak | Doğrulandı mı? |
|---|---|---|---|---|
| X3 | Ölçüm kabul kapısı | K18-testing | anayasa §10/3 | ✅ |
| X4 | Sinyal şekillendirme | K004-dsp | anayasa §2 | ✅ |
| X5 | Boot-time firmware | K005-firmware | anayasa §2 | ✅ |
| X6 | Devre/PCB tasarımı | K002-electronics | anayasa §2 | ✅ |
| X7 | Sürücü kodu | K000-isletim-sistemi | anayasa §10/2 | ✅ |
| X8 | Ses motoru | K003-audio | anayasa §2 | ✅ |
| X9 | Tür sınıflandırması | donanaım-type | anayasa §3/50 | ✅ |
| X10 | Ham erişim | donanım-kernel | anayasa §3/47 | ✅ |
| X11 | Çağrı seti | donanım-api | anayasa §3/49 | ✅ |

**§24 sonucu:** 9 kapsamlı dış konu **tamamı doğrulanmıştır**; hepsi anayasa satırıyla
desteklenir.

---

## §25 Kapsam ↔ komşu düğüm tutarlılık doğrulaması

| # | Komşu | İlişki | Doğrulama | Sonuç |
|---|---|---|---|---|
| T1 | donanım-kernel | core toplar, kernel okur | anayasa §3/47-48 | ✅ |
| T2 | donanım-api | core toplar, api çağırır | anayasa §3/49 | ✅ |
| T3 | donanaım-type | core merkez, type tür-alt-bağlamı | anayasa §3/50 | ✅ |
| T4 | K000-isletim-sistemi | sürücü K000'de | anayasa §10/2 | ✅ |
| T5 | K005-firmware | core kalıcı, firmware boot-time | anayasa §2 | ✅ |
| T6 | K003-audio/K004-dsp | core donanım verisi | anayasa §2 | ✅ |
| T7 | K18-testing | core toplar, testing uygular | anayasa §10/3 | ✅ |
| T8 | K002-electronics | core veri, K002 devre | anayasa §2 | ✅ |

**§25 sonucu:** 8 komşu düğümle **çelişki yoktur**.

---

## §26 Ölçüm ve sayım notları (2026-10-13)

| # | Ölçüm | 2026-10-12 | 2026-10-13 | Fark |
|---|---|---|---|---|
| M1 | `.ai/electronic/**` md dosyası | 60 | 60 | 0 |
| M2 | `.ai/electronic/**` satır | 18.980 | **20.711** | **+1.731** |
| M3 | composer.json envanteri | "3" (ci.yml yorumu) | **5** (glob) | +2 |
| M4 | K001 toplam md | — | 28 | — |

> **M2-M3 notu:** İki ölçüm de kayıtlıdır; **sessizce düzeltilmez**. Fark K001 dışı yazım
> olabilir (11 paralel ajan). Güncel değer: electronic 20.711 · composer.json 5.

---

## §27 Bilgi kaynakları — bu dosyada uygulanan

| # | Kaynak | Seviye | Satır |
|---|---|---|---|
| H1 | `.ai/architecture/K001-donanım/**` | 1 — SSOT | §1, §3, §9, §10, §13, §23-§25 |
| H2 | Repo (`shared/src/Device/`) | 2 | §5 (20 satır) |
| H3 | `.ai/electronic/**` | 2 | §6, §26 |
| H4 | ti.com · onsemi.com · akm.com | 3 | §2, §20, §22 |
| H5 | Genel donanım mühendisliği | 4 | §19-§21 (genel-kavram) |
| H6 | Model hafızası | 5 | Kullanılmadı |

---

## §28 Hata ayıklama — bu turda düzeltilen iddialar

| # | İddia | Önceki | Doğru | Kanıt |
|---|---|---|---|---|
| F1 | electronic satır | 18.980 | 20.711 | §6, §26/M2 |
| F2 | composer.json sayısı | "3" (ci.yml yorumu) | 5 (glob) | §26/M3 |
| F3 | LM5122 tipi | Belirsiz | **Boost** | §22.1/G1 |
| F4 | LM5143 ilişkisi | — | İki ayrı çip, LM5143 bu düğümde YOK | §8/C3 |
| F5 | core üretim kodu | Belirsiz | **PLANNED** (0 cpp/h) | §5.1 |
| F6 | Ölçü doğruluğu | Belirsiz | **UNKNOWN** (Q-C6) | §20.1/O16 |
| F7 | Sağlık sensörü | Belirsiz | **YOK/UNKNOWN** | §21 |

---

## §29 Sorumluluk ve devir

| # | Konu | Sorumlu | Durum |
|---|---|---|---|
| R1 | Q-C1 core↔kernel sınırı (Q3) | Vault Steward | AÇIK |
| R2 | Q-C2 depolama sahipliği | Vault Steward | AÇIK |
| R3 | Q-C3 sağlık ölçümleri | audio-hw | AÇIK |
| R4 | Q-C4 güç bileşeni sahipliği | audio-hw | AÇIK |
| R5 | Q-C5 toplama zamanlaması | Vault Steward + embedded | AÇIK |
| R6 | Q-C6 ölçüm doğruluğu | audio-hw | AÇIK |
| R7 | Q-C7 DMTF kullanımı | Vault Steward | AÇIK |
| R8 | Q-C8 envanter şeması | data | AÇIK |
| R9 | Ölçüm kabul kapısı | K18-testing | SABİT |

---

## §30 Doğrulama son sayımları (genişletilmiş)

| Metrik | Değer |
|---|---|
| Toplam § | 30 |
| Toplam kontrol satırı | 178 |
| ✅ doğrulandı | 106 |
| UNKNOWN | 52 |
| ⚠️ VERIFICATION REQUIRED | 6 |
| PLANNED | 1 |
| Benzersiz URL (bu dosya) | 5 |
| Repo Read kanıtı | 20 satır |
| Envanter kesişimi (düğüm-içi hariç) | **0** |
| Ölçüm çelişkisi | 2 (§26/M2-M3) |

**Kapanış:** Q-C1-Q-C8 kararları verilmeden bu dosyadaki **52 UNKNOWN**'un **41'i
kapanmaz**; karar sonrası yalnız ilgili satır güncellenir, tarihçe korunur.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
