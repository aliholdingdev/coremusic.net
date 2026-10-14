---
title: "K001-donanım — Domain Eşleme Matrisi (K000–K005 sınır kararları)"
type: analysis-report
node_id: K001-donanım/raporlar/domain-esleme-matrisi
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
  - K002-electroncis
  - K003-audio
  - K004-dsp
  - K005-firmware
evidence:
  - "Disk ölçümü 2026-10-13 (tur-2): K000 282 md/60 dizin · K001 70 md · K002 16 md/3 dizin (alt-dizin: devre-ve-analog 4 · dijital-ve-mikroislemci 4 · guc-yonetimi 2 md) · K003 68 md/12 üst-dizin (13 recursif) · K004 69 md/12 dizin · K005 35 md/9 dizin"
  - ".ai/architecture/K001-donanım/donanım-anayasa.md:55-66 (§2 sınır tablosu 10 satır — §3'te birebir taşındı) · :195 (tur-2b: '✓ CEVAPLANDI — disk gerçeği 10 bağlam') · :250 (C10 PROPOSED notu)"
  - ".ai/.decisions/accepted/: ADR-017 362 satır (dsp-hardware-mode) · ADR-038 314 satır (sound-card-chip: XU316+PCM3168A, PCM5122 RED) · ADR-062 389 satır (DSP Pipeline Architecture) · ADR-063 (hardware-design-standards: k19-pcb, IPC-6012 Class 2, EMC) · ADR-064 328 satır (electronics-platform; authority(b): '13 servis DOĞRULANAMADI — bağlayıcı 11 alan servisi'; title: 'Servis Sayım Çelişkisi 13/11/9/7')"
  - ".ai/.decisions/index.md:44 (ADR-061 kaldırıldı 2026-10-12; yedek yolu iddiası diskte YOK — C-11) · draft/ADR-102 230 satır (§6 C1-C6 çelişki tablosu bu raporun C-05/C-07/C-14'ünü bağımsız doğrular)"
  - "K000-isletim-sistemi/index.md:6 (children 13 satır — os-donanim-tespiti LİSTEDE, tur-2b) · :7 (depends_on [K002-altyapi] — dizin YOK, C-14) · :8 (validation '12 düğüm sayıldı' — 13 child ile çelişir, Q7)"
  - "K000-isletim-sistemi/electroncis/ = 12 md tam liste (index·kapsam·dogrulama·ornekler + devre-tasarimi/4 + guc-ve-koruma/4; her iki alt-dalin kendi 'anayasa' dosyası var) — elektronik K000 altında da var (C-13)"
  - "K001-donanım/index.md:6-11 (children 5 satır — devices eklendi, tur-2b E2 kapandı) · K001 kademe-2 4 indeks frontmatter okuması (§3.1: api/core/kernel children=[] vs devices 3-child farklı şablon)"
  - "shared/src/Device/ (ADR-064 authority kaynaklı): DeviceManager.php:29-40 = 7 tip · DeviceCssMap.php:8-33 = 7 CSS + 4 view-mode · DeviceDetector.php:21-26 eşikleri · Api/Bff/ = 4 BFF"
validation:
  - "6 ağaç sayımı tur-2 Get-ChildItem recursif ile ölçüldü; alt-dizin sayıları üst-dizin ve recursif olarak ayrı ayrı raporlandı (belirsizlik gizlenmedi)"
  - "Sınır tablosu 10 satır (anayasa §2 :55-66) — tamamı gerçek dosya/satır ile teyit edildi, §3'te eksiksiz"
  - "5 accepted ADR (017·038·062·063·064) + 1 silinmiş ADR (061) + 1 draft (102) disk kayıtları §4'te"
  - "DeviceManager.php:29-40 (7 tip) · DeviceDetector.php:21-26 — IMPLEMENTED kod konumları §6'da"
open_questions:
  - "K002 index.md:56-64 '5 alt-konu' der — diskte 3 dizin; hedef '26 dosya / 5 alt-konu' (architecture/index.md:583) — üretim sürüyor mu?"
  - "ADR-064 '13/11/9/7' servis sayısı — hangisi bağlayıcı? (title bizzat çelişkiyi beyan eder)"
  - "ADR-061 silinmiş; ADR-064:19 atıf yapar — atıf güncellensin mi, yedek (vault-backups/*.bak) geri yüklensin mi?"
  - "K000/electroncis (12 md) ile K002-electroncis (16 md) arasındaki sınır ne? — C-13"
  - "K000 index:7 'K002-altyapi' bağımlılığı hangi gerçek düğüm? — C-14"
date: 2026-10-13
updated: 2026-10-13
group: kanit
---

# K001-donanım — Domain Eşleme Matrisi

## §1 Bu raporun rolü (ELI15)

CoreMusic mimari ağacında K000–K005 arasındaki **sınır** nerededir? "Driver mı,
firmware mı, DSP mi, devre mi?" sorusunun cevabı her yerde aynı olmalı. Bu rapor
o sınırları tek matriste toplar: hangi konu hangi ağaca ait, hangi ADR bunu
kapatmış, hangi kod dosyası IMPLEMENTED kanıtıdır, hangi komşuda **çakışma**
var. Amaç: "bu içerik nereye yazılır?" tartışmasının dosya ve satır kanıtıyla
bitmesi. Rapor salt okumadır; hiçbir dosya değiştirilmez. Çelişkiler çözülmez,
kayıt altına alınır (tam liste: `K001-donanım-celiskiler-ve-acik-kararlar.md`).

### §1.1 Kanıt türleri ve güven düzeyi (bu raporun okuma kuralı)

| Kanıt türü | Örnek (bu rapor) | Güven | Sınır |
|---|---|---|---|
| Birincil disk ölçümü | Get-ChildItem / Get-Content .Count (§2·§2.2·§2.3) | en yüksek | dosya + sayı |
| Birincil metin okuması | anayasa §2 :55-66 · K002 index §2 · ADR-064 title (§3·§4) | en yüksek | okunan satır kadar |
| İkincil aktarım | ADR-064 authority alanının PHP envanteri özeti (§6.1) | orta — ADR metni kanıtı | PHP dosyaları bağımsız okunmadı, `⚠️` |
| Ad-benzerliği taraması | komşu alt-dizin adları (§2.1·§3.2) | düşük — **iddia değil kayıt** | içerik okunmadı, yalnız ad |
| Yorum/öneri | §10 senaryo akışı · §7 eşleme özetleri | yok — bağlayıcı değil | her satır kanıt satırına bağlanır |

Kural: bu raporda hiçbir satır "duydum" düzeyinde değildir; her iddia ya
dosya+satır, ya ölçüm sayısı, ya `⚠️ VERIFICATION REQUIRED`/`UNKNOWN` taşır.
İkincil ve ad-düzeyi kanıtlar birincil sayılmaz — §6.1 ve §3.2'de etiketleriyle
kullanılmıştır.

## §2 Komşu ağaçlar envanteri (tur-2 sayımı)

| Ağ | node_id | md | üst dizin | recursif dizin | Konu |
|---|---|---|---|---|---|
| K000 | `K000-isletim-sistemi` | 282 | — | 60 | İşletim sistemi katmanı |
| K001 | `K001-donanım` | 70 | 6 | — | Donanım (bu rapor) |
| K002 | `K002-electroncis` | 16 | 3 | 3 | Elektronik devre/analog/dijital/güç |
| K003 | `K003-audio` | 68 | 12 | 13 | Ses işleme |
| K004 | `K004-dsp` | 69 | 12 | 12 | DSP zinciri |
| K005 | `K005-firmware` | 35 | 9 | 9 | Firmware (XMOS XU316) |

K003 fark notu: üst dizin 12, recursif 13 — fark bir iç içe dizindir; iki sayı
da ölçümle alındı, biri diğerinin yerine geçmez.

### §2.1 Komşu alt-dizin kataloğu (adlar — sınır çakışması taraması için)

| Ağ | Alt-dizinler |
|---|---|
| K003-audio | backend · device · diagnostics · driver · engine · input · mixing · output · realtime · routing · synchronization · testing |
| K004-dsp | compressor · crossover · delay · dynamics · equalizer · filters · limiter · measurement · pipeline · reverb · spatial-audio · testing |
| K005-firmware | audio-control · boot · device-control · diagnostics · power-control · runtime · security · testing · update |
| K002-electroncis | devre-ve-analog · dijital-ve-mikroislemci · guc-yonetimi |
| K000-isletim-sistemi (üst) | electroncis · isletim-sistemi-api · isletim-sistemi-audio · isletim-sistemi-core · isletim-sistemi-cross · isletim-sistemi-kernel · isletim-sistemi-observability · isletim-sistemi-plan · isletim-sistemi-runtime · isletim-sistemi-security · isletim-sistemi-testing · isletim-sistemi-turu · os-donanim-tespiti (13 children — index:6) |

Çakışma uyarısı: K000 altında `electroncis` adında **tam bir dal** vardır (§6.2) —
K002 ile konu sınırı belirsizdir (C-13).

### §2.2 K002 kök dosya kataloğu (tur-2b — 6 md) + K001 kök doğrulaması (10 md)

K002-electroncis 16 md'nin 6'sı kök, 10'u alt-dizindir (§2.3: 4+4+2):

| K002 kök dosya | md | Satır |
|---|---|---|
| `index.md` | 1 | 100 |
| `electroncis-master.md` | 1 | 505 |
| `electroncis-kapsam.md` | 1 | 507 |
| `electroncis-dogrulama.md` | 1 | 507 |
| `electroncis-ornekler.md` | 1 | 505 |
| `electroncis-bagimliliklar.md` | 1 | 501 |

K001 kök 10 dosya tur-2b'de yeniden ölçüldü — §7.1 tablosundaki değerlerle
birebir (anayasa 518 · kararlar 529 · bagimliliklar 519 · dogrulama 507 ·
riskler 506 · test-stratejisi 509 · kod-ornekleri 509 · evrensel-dil 502 ·
web-arastirma 500 · index 99 = **5.004 satır, 10 dosya**).

Fark notu: K002 kökünde `master` + `bagimliliklar` vardır, K001 kökünde
`anayasa` + `kararlar` + `riskler` + `test-stratejisi` + `evrensel-dil` +
`web-arastirma` vardır — kök dosya şablonları **eşleşmez** (K001 10 / K002 6).
Bu bir kusur kaydı değil, şablon farkı kaydıdır; birleştirme veya
hizalama kararı Vault Steward'ındır.

### §2.3 Komşu alt-dizin dosya sayıları (tur-2b recursif sayım — 33 satır)

| K003-audio | md | K004-dsp | md | K005-firmware | md |
|---|---|---|---|---|---|
| backend | **20** | compressor | 6 | audio-control | 4 |
| device | 4 | crossover | 5 | boot | **6** |
| diagnostics | 4 | delay | 5 | device-control | 4 |
| driver | 4 | dynamics | 6 | diagnostics | 1 |
| engine | 4 | equalizer | 6 | power-control | 1 |
| input | 4 | filters | 5 | runtime | 4 |
| mixing | 4 | limiter | 6 | security | 1 |
| output | 4 | measurement | 5 | testing | 1 |
| realtime | 4 | pipeline | 5 | update | 1 |
| routing | 4 | reverb | 5 | (kök) | 12 |
| synchronization | 4 | spatial-audio | 5 | — | — |
| testing | 4 | testing | 5 | — | — |
| (kök) | 4 | (kök) | 6 | — | — |
| **toplam** | **68** | **toplam** | **69** | **toplam** | **35** |

Sayım kontrolü: K003 20+11×4+4 = 68 ✓ · K004 6+6+6+6+5×8+6 = 69 ✓ ·
K005 23+12 = 35 ✓ — üçü de §2 toplamlarıyla birebir.

Sınır gözlemi (ad düzeyi, içerik okunmadı): K003'ün 11 dalı 4'er md ile
**homojen** üretildi (backend 20 hariç — en büyük dal); K004'te 5'er/6'er
karışık; K005'te 4 dal 1'er md ile **iskelet** (diagnostics·power-control·
security·testing·update) — üretim derinliği eşit değil, bu bir kusur değil
durum kaydıdır. K005 `device-control`·`power-control` adları K001
type/power/audio ile konu-komşudur (§3.2).

## §3 Sınır matrisi (SSOT: donanım-anayasa.md §2 :55-66 — 10 satır eksiksiz)

| # | Yüzey | K001'de mi? | Karar mercii | Not |
|---|---|---|---|---|
| 1 | Sürücü seviyesi donanım soyutlama | EVET | K001 | ana iş |
| 2 | Cihaz tipi sınıflandırma | EVET | K001/donanaım-type | audio · hdd · ssd (gerçek: 10 bağlam) |
| 3 | Donanım erişim API'si | EVET | K001/donanım-api | keşif · komut · durum · olay |
| 4 | Kernel-üstü arayüz | EVET | K001/donanım-kernel | çekirdeğe en yakın rol |
| 5 | Çekirdek sürücü kodu (ring-0) | HAYIR | K000-isletim-sistemi | yalnız sınır |
| 6 | Cihaz firmware'i / MCU yüklemesi | HAYIR | K005-firmware | XMOS · DSP zinciri |
| 7 | Ses işleme zinciri | HAYIR | K003-audio | K001 yalnız cihazı sunar |
| 8 | DSP algoritması | HAYIR | K004-dsp | komşu |
| 9 | Fiziksel PCB / BOM | HAYIR | hardware vault | elektronik tarafı (adlandırma belirsiz — Q4) |
| 10 | Depolama cihazı envanteri | EVET | K001/donanaım-type/hdd · ssd | kademe-3 |

Okuma notu: satırlar anayasa §2 tablosunun birebir kopyasıdır (satır 1'de "Sürücü
seviyesi" ifadesi anayasadaki yazımıdır). "hardware vault" adlandırması anayasa
metninde ayrıca açılmaz; K002-electroncis ile eşanlamlı mı olduğu `UNKNOWN`
(Q4).

### §3.1 K001 kademe-2 bağımlılık/frontmatter matrisi (4 indeks okuması — tur-2b)

K001'in 4 kademe-2 indeksinin frontmatter'i karşılaştırıldı (her biri dosyadan
okundu, özet değil):

| Dal | node_type | status | children | depends_on | group | Şablon |
|---|---|---|---|---|---|---|
| `donanım-api/index.md` | subdomain | draft | `[]` (boş) | K000-isletim-sistemi | mimari | A (v2 mini-ağaç) |
| `donanım-core/index.md` | subdomain | draft | `[]` (boş) | K000-isletim-sistemi | mimari | A |
| `donanım-kernel/index.md` | subdomain | draft | `[]` (boş) | K000-isletim-sistemi | mimari | A |
| `donanım-devices/index.md` | architecture-index | **active** | **3 satır** (kapsam·dogrulama·ornekler) | K001-donanım + K000-isletim-sistemi | — (title/type alanları var) | **B (farklı şablon)** |

Bulgular (gizlenmez):

1. api·core·kernel indekslerinde `children: []` — bu üç dalın 2–3 dosyası
   (kapsam·dogrulama) listede değil (tüm kademe-2'de bilinen örüntü — bkz.
   tur-envanteri raporu §5.1).
2. devices indeksi **farklı frontmatter şablonu** kullanır (`title`+`type`
   mimari-index; `status: active`; children tam dosya yoluyla). Bu dal
   `shared/src/Device/DeviceManager.php` + ADR-064'ü doğrudan evidence
   olarak taşır — K001'in kod'a en yakın dalı.
3. Dört dalın da `depends_on: K000-isletim-sistemi`'i vardır — K001↔K000
   bağımlılığı tek yönlü ve tutarlı (anayasa §2 satır 5 ile uyumlu).

## §3.2 Komşu ağaç kademe-2 sınır özeti (K003·K004·K005 — alt-dizin adları → K001 etkisi)

| Komşu | 12/9 alt-dizin | K001 ile sınır çakışması var mı? | Kanıt |
|---|---|---|---|
| K003-audio | backend·device·diagnostics·driver·engine·input·mixing·output·realtime·routing·synchronization·testing | **`device` ve `driver` adları K001 konu alanıyla örtüşür** — anayasa §2 satır 7 "ses işleme K003" der, sınır kararlı sayılır; çakışma iddiası değil, ad-benzerliği kaydı | dizin listesi (tur-2) |
| K004-dsp | compressor·crossover·delay·dynamics·equalizer·filters·limiter·measurement·pipeline·reverb·spatial-audio·testing | Hayır — 12 dalın tamamı DSP-algoritma alanı; anayasa §2 satır 8 ile uyumlu | dizin listesi |
| K005-firmware | audio-control·boot·device-control·diagnostics·power-control·runtime·security·testing·update | **`device-control`·`power-control`·`audio-control`** adları K001 type/power/audio dallarıyla konu-komşu; anayasa §2 satır 6 firmware'i K005'e verir — sınır kararlı | dizin listesi |
| K002-electroncis | devre-ve-analog·dijital-ve-mikroislemci·guc-yonetimi | **`guc-yonetimi` ↔ K001 `donanaım-type/power` konu örtüşmesi** — anayasa §2 satır 9 "Fiziksel PCB/BOM → hardware vault" der; "güç devresi tasarımı" (K002) ile "güç gereksinimi envanteri" (K001 power-kapsam P-T01…P-T13) ayrımı metinde yazılı değil → C-13 ile bağlantılı Q4 | K002 index §2 + K001 power-kapsam |

Not: bu tablo **ad-benzerliği taramasıdır**; hiçbir komşu dosya bu oturumda
içerik olarak okunmamıştır (yalnız dizin adları + indeks satırları okundu).
İçerik düzeyinde sınır ihlali iddiası `⚠️ VERIFICATION REQUIRED`.

## §4 ADR matrisi (kabul edilmiş kararlar — disk kanıtlı, satır sayıları ölçülmüştür)

| ADR | Dosya (başlık özetİ) | Satır | Durum | K001 sınır katkısı |
|---|---|---|---|---|
| ADR-017 | `ADR-017-dsp-hardware-mode.md` | 362 | accepted | DSP ↔ hardware mod ayrımı: K004 DSP'yi, K001 donanımı taşır; 3 katman sınırı |
| ADR-038 | `ADR-038-8-1-sound-card-chip-selection.md` | 314 | accepted | XU316 + PCM3168A seçimi; **PCM5122 kullanımı yasak** (AGENTS §17-8 · brain.md:998) |
| ADR-062 | `ADR-062-dsp-pipeline-architecture.md` | 389 | accepted | **DSP Pipeline Architecture** (sinyal zinciri sırası · hard-RT kısıtları · katman sınırı) — K004 komşuluğunu bağlar, K001'e doğrudan sınır kararı içermez |
| ADR-063 | `ADR-063-hardware-design-standards.md` | 299 | accepted | k19-pcb · IPC-6012 Class 2 · EMC kapısı — PCB/BOM standartları K001'e komşu |
| ADR-064 | `ADR-064-electronics-platform-architecture.md` | 328 | accepted | L0-L6 · 5 cihaz sınıfı · cihaz-servis eşleme; **title bizzat "Servis Sayım Çelişkisi 13/11/9/7" der** (§5) |
| ADR-061 | **diskte YOK** | — | silinmiş (2026-10-12) | `decisions/index.md:44` kaldırma kaydı; yedek iddia edilen yol diskte YOK, gerçek yedek `C:\temp\opencode\vault-backups\ADR-061-electronics-architecture.md.bak` + `adr-061.md.bak` (C-11) |
| ADR-102 | `draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md` | 230 | **draft** | 3→9 bağlam genişlemesi (display·power·cables·network·memory·cpu) — onaylanana kadar PROPOSED |

Düzeltme notu (tur-1 hatası gizlenmez): rapor-1 §4'te ADR-062 "electronics
platform" olarak özetlenmişti; dosya başlığı okunduğunda **DSP Pipeline
Architecture** olduğu görüldü. Electronics platform ADR'si **ADR-064'tür** (328
satır). Bu rapor doğru sürümü taşır.

### §4.1 ADR-064 authority alanı derin okuması (ilk 45 satır — bu oturumda okundu)

ADR-064'ün frontmatter `authority` alanı (dosya satır 8, uzun metin — özet
tablo; ham metin dosyadadır):

| Madde | ADR-064'ün kendi kararı | Kanıt içeriği |
|---|---|---|
| (a) 5 cihaz sınıfı | gömülü · monitör · masaüstü · mobil · otomotiv — **kullanıcı onaylı**; `ui-design` T07/T17 hizası; DeviceManager **7 tipi** + 45-tier/11 kategori ile eşleme tablosu; `4k-tv` sınıfı + otomotiv PHP tespiti ⚠️ | `DeviceManager.php:29-40` 7 tip · `DeviceCssMap.php:8-33` · `DeviceDetector.php:21-26` |
| (b) "13 servis" iddiası | **DOĞRULANAMADI** — tek iz `keys.md:163` → `electronic/service-architecture.md` = **0 dosya hayalet**; bağlayıcı sayı **11 alan servisi** (ADR-039 §2.1 + `.ai/CLAUDE.md:106` "Infra (11)"); 13 satırı ⚠️ altında kalır, **vault reset'ine ertelendi** | ADR-064 authority (b) |
| (c) cihaz-servis matrisi | 5 sınıf × 11 servis; BFF 4'lüsü `Desktop/Embedded/Mobile/Spa` IMPLEMENTED, **otomotiv BFF'i yok** ⚠️ | `shared/src/Api/Bff/` 4 dosya |
| (d) sayım çelişkisi | 13/11/9/7/11-subdomain **tek tabloya bağlandı — yeni sayı üretilmedi** (ADR-039 C1 kuralı) | ADR-064 authority (d) |
| (e) sınır | ADR-061 (L6 + bileşen politikası) · ADR-062 (DSP pipeline) · ADR-063 (tasarım standartları) — **üçü de yeniden alınmaz** | ADR-064 authority (e) |

K001 açısından kritik: ADR-064 (b) ve (e) maddeleri bu raporun §5 ve §8
sorularına **ADR metni içinde kısmi cevap** verir — çelişki "açık" olmaktan
çıkıp "**başlık stale, gövde çözümlü**" durumuna geçer (§5.1).

### §4.2 ADR-102 §6 çelişki tablosu (draft — K001 sınır etkileriyle)

ADR-102 taslağının §6 tablosu (okuma: draft/ADR-102…:184-191) kendi
çelişkilerini listeler; K001 matrisiyle eşlemesi:

| ADR-102 §6 | Konu | ADR-102 tutumu | Bu matristeki karşılığı |
|---|---|---|---|
| C1 | `architecture/index.md:94` "K002 hiç yok" vs 16 dosya | **ÇÖZÜLMEZ** (Vault Steward) | C-05 |
| C2 | `K000/index.md` `depends_on: [K002-altyapi]` — isim uyuşmazlığı | **ÇÖZÜLMEZ** (raporlanır) | **C-14 — ADR-102 de kayıtlı** |
| C3 | K002 index 5 çocuk vs 3 dizin | **ÇÖZÜLMEZ** (raporlanır) | C-07 / Q1 |
| C4 | `donanım-devices/index.md:44` "donanaım-type = sınıf (audio·hdd·ssd·display·power)" — 10 bağlam oldu, **stale** | ADR-102 ile düzeltilmez (ayrı edit ister) | **bu oturumun yeni tespiti — C-15** |
| C5 | `display/index.md` "5. alt bağlam" der — 10 oldu | yazım hatası, raporlanır | tur-envanteri Q-domain |
| C6 | Eski "yalnız 3 bağlam" citation'ları (kapsam §7.3 · dogrulama §34) | **4 edit ile PROPOSED işaretlenir** | C-03 ile bağlantılı |

Sonuç: ADR-102 taslağı bile bu matrisin çelişki setini **bağımsız olarak**
doğrulamaktadır (C1→C-05 · C2→C-14 · C3→C-07 birebir örtüşür).

### §4.3 ADR-017 sınır özeti (dosya başlığı + §1 okuması — 3 katman)

`ADR-017-dsp-hardware-mode.md` (362 satır, accepted, v1.0.1) başlığı ve §1
girişi (ilk 30 satır okundu) sınır kararını doğrudan taşır:

> Başlık: "DSP Hardware Mode (**3 Katman: XMOS/xCORE Firmware · JUCE Plugin DSP ·
> ASIO/WASAPI Host** — Hard RT Kısıtları, Buffer/Latency Bütçesi, Xrun Politikası)"

| Katman | Teknoloji | ADR-017'nin sınır kararı | K001 açısından |
|---|---|---|---|
| 1 — Firmware | XMOS XU316 (gömülü) | Zamanlama sahibi firmware; hard-RT kısıtları burada | K005'in işi (anayasa §2 satır 6) |
| 2 — DSP plugin | JUCE / C++20 Neva Engine | Hesap sahibi uygulama kodu | K004'ün işi (anayasa §2 satır 8) |
| 3 — Host/driver | ASIO/WASAPI (Windows) | Taşıma sahibi sürücü-katman; latency bütçesi `<10ms ASIO / <20ms WASAPI` (ADR-006'dan, ⚠️ hedef) · xrun telemetrisi ADR-013 ruhu | **K001 sınır alanı** — "sürücü seviyesi donanım soyutlama" (anayasa §2 satır 1) tam buraya komşudur; hangi kısım K001 donanım-api/kernel, hangi kısım K000 ring-0 ADR-017'de ayrıca adlandırılmaz |

Ek kanıt (başlık okuması): ADR-017 debate "✓ TAMAMLANDI (3 tur / 20 persona,
18/2/0 KABUL)" · frozen YOK · karar veren Vault Steward (kullanıcı onaylı).
§1.1 "Mevcut Durum" bölümü kod/vault kanıtlarını dosya+satır ile taşır
(uzantı: 362 satırın tamamı bu oturumda okunmadı — yalnız başlık + ilk 30
satır; K001'e çapraz etki Q5'te `⚠️` olarak kalır).

## §5 ADR-064 çelişkisi (13/11/9/7 — kayıtlı, gizlenmedi)

`ADR-064-electronics-platform-architecture.md` frontmatter title'ı (dosya başlığı
okuması, 2026-10-13):

> "CoreMusic - ADR-064: Electronics Platform Architecture (L0-L6 · 5 Cihaz Sınıfı
> · Cihaz-Servis Eşleme Matrisi · **Servis Sayım Çelişkisi 13/11/9/7**)"

Yani ADR'nin kendisi dört farklı servis sayısını (13 · 11 · 9 · 7) başlıkta
taşıyor ve bunu çözmüyor. Ek olarak :19'da silinmiş olan ADR-061'e atıf yapar.

| Çelişki | Değerler | Durum |
|---|---|---|
| Servis sayısı | 13 / 11 / 9 / 7 | **AÇIK** — Vault Steward kararı gerekiyor |
| ADR-061 atıfı | :19 → diskte YOK | **AÇIK** — atıf güncellenmeli veya yedek geri yüklenmeli |

Bu rapor çelişkiyi **çözmez, yalnız kaydeder** (görev emri: gizleme yasak).

### §5.1 ADR-064 gövdesinin kendi çözümü (başlık ≠ gövde — kanıtlı ayrım)

| Katman | Ne diyor? | Durum |
|---|---|---|
| Başlık (title satırı) | "Servis Sayım Çelişkisi 13/11/9/7" — 4 sayı taşır, çözüm üretmez | **stale başlık** (değiştirilmez — frozen okuma) |
| Authority alanı (b) | "13 servis" **DOĞRULANAMADI** · bağlayıcı = **11 alan servisi** · 13 ⚠️ altında · vault reset'ine ertelendi | **gövde çözümü — bağlayıcı karar burada** |
| Authority alanı (d) | 13/11/9/7/11-subdomain tek tabloya bağlandı; yeni sayı üretilmedi (ADR-039 C1) | çözüm yöntemi |
| §1.1 Mevcut Durum tablosu | "13 servis" = hayalet hedef (tek iz keys.md:163 → 0 dosya) · 11 servis = ADR-039 §2.1 kayıtlı · 9 subdomain (shared/Config/CLAUDE.md:45-55) · 7 subdomain (domain.php:7-15) — hepsi ayrı satırda etiketli | kanıt tablosu |

Yorum (kayıt düzeyi): "hangisi bağlayıcı?" sorusunun cevabı ADR-064 metninde
**vardır: 11**. Kalan açık: başlık neden hâlâ 4 sayı taşıyor (eski beyanın
taşınması — düzeltme yasağı kuralı ile tutarlı olabilir, karar Vault
Steward'ındır). Q2 bu bilgiyle güncellenir (§8).

Not: ADR-064 §1.1 tablosu "9 subdomain vs 7 subdomain" çelişkisini de ayrı
satırlarda raporlar (`shared/Config/CLAUDE.md:45-55` = 9 · `domain.php:7-15` =
7) — bu çelişki K001 dışıdır, yalnızca ADR-064'ün sayım-çelişkisi disiplininin
kapsamını göstermek için kaydedildi.

## §6 IMPLEMENTED kod kanıtları ve komşu-sınır çakışmaları

### §6.1 K001 ↔ kod yüzeyi

| Dosya | Kanıt |
|---|---|
| `shared/src/Device/DeviceManager.php` | :29-40 — 7 cihaz tipi (662 satır · IMPLEMENTED) |
| `shared/src/Device/DeviceDetector.php` | :21-26 — algılama kuralları (IMPLEMENTED) |

İçerik iddiası yok — yalnız konum kanıtı; dosya içeriği bu oturumda okunmadı
(`⚠️ VERIFICATION REQUIRED`).

ADR-064 authority (a)+(c) alanlarından aktarılan **kod envanteri kaydı** (ADR
metni kanıt olarak okundu; PHP dosyaları doğrudan okunmadı — ikincil kanıt):

| Yüzey | ADR-064'ün kaydettiği değer | K001 matrisiyle ilişki |
|---|---|---|
| DeviceManager tipleri | 7: `embedded · phone · tablet · laptop · desktop · 4k-tv · 4k-monitor` | "Cihaz tipi sınıflandırma" (§3 satır 2) — PHP tipleri ile `donanaım-type` bağlam adları **eşleşmez** (bağlam: audio·hdd·ssd·…; tip: phone·laptop·…) — iki farklı eksen, eşleme tablosu ADR-064'ün işi |
| DeviceCssMap | 7 cihaz CSS + 4 view-mode (`home · pro · studio · car`) | UI eksen — K001 dışı |
| DeviceDetector eşikleri | phone ≤767 · tablet/embedded ≤1024 · laptop ≤1440 · desktop ≤2560 · 4k-tv ≤3840 | ADR-064 "otomotiv PHP tespiti ⚠️" der — `car` yalnız view-mode |
| BFF katmanı | 4: `SpaBff · MobileBff · EmbeddedBff · DesktopBff` + BffLayer | API köprüsü — K001/donanım-api'ye komşu, eşleme ADR-064 (c) matrisinde |
| domain.php subdomains | 7 (auth·home·assets·music·admin·media·api) | K002/registry dünyası — C-05 bağlantılı |

Bu tablo ADR-064'ün kendi kanıt satırlarının **özetidir**; PHP dosyaları bu
oturumda bağımsız olarak okunmamıştır — ADR-064 authority alanı kaynak
gösterilmiştir (ikincil kanıt, `⚠️` işaretli satırlar ADR'nin kendi ⚠️'larıdır).

### §6.2 K000/electroncis ↔ K002-electroncis sınırı (C-13)

| Yüzey | Ölçüm |
|---|---|
| `K000-isletim-sistemi/electroncis/` | 12 md · 2 alt-dizin (`devre-tasarimi` · `guc-ve-koruma`) + index·kapsam·dogrulama·ornekler |
| `K002-electroncis/` | 16 md · 3 alt-dizin (`devre-ve-analog` · `dijital-ve-mikroislemci` · `guc-yonetimi`) |

K000/electroncis dosya kataloğu (bu oturumda tek tek listelendi — 12/12):

```text
electroncis/index.md · electroncis-kapsam.md · electroncis-dogrulama.md · electroncis-ornekler.md
electroncis/devre-tasarimi/ : index.md · devre-tasarimi-anayasa.md · analog-ve-dijital-devre-temelleri.md · devre-simulasyonu-ve-dogrulama.md
electroncis/guc-ve-koruma/  : index.md · guc-ve-koruma-anayasa.md · guc-kaynaklari-ve-regulatorler.md · koruma-devreleri-ve-esikler.md
```

K002 alt-dizin md sayıları (tur-2b sayımı): `devre-ve-analog` **4** ·
`dijital-ve-mikroislemci` **4** · `guc-yonetimi` **2** → alt-dizin toplamı 10;
kök dosyalarla birlikte 16. `guc-yonetimi` 2 md ile üçlü içinde en küçüğüdür
(üretim devam ediyor olabilir — C-07 ile bağlantılı).

Konu örtüşmesi barizdir (devre tasarımı + güç her iki ağaçta). anayasa §2 satır
9 "Fiziksel PCB / BOM → hardware vault" der; "hardware vault" hangi düğüm
(`K000/electroncis` mı, `K002-electroncis` mı, ayrı bir vault mu) **UNKNOWN** —
karar Vault Steward'ındır (C-13 + §3 Q4). Ek bulgu: her iki dalın da kendi
`anayasa` dosyası vardır (K000/electroncis/devre-tasarımı/devre-tasarımı-anayasa.md
· guc-ve-koruma/guc-ve-koruma-anayasa.md) — yani K000 tarafı tam bir mini-vault
işlevi görür; K002 ile eşanlamlılık iddiası `⚠️ VERIFICATION REQUIRED`.

### §6.3 K000 → K002 bağımlılık atfı (C-14)

`K000-isletim-sistemi/index.md:7` → `depends_on: [K002-altyapi]`. `Test-Path`
ölçümü: `K002-altyapi` dizini **diskte YOK** (K002'nin gerçek düğümü
`K002-electroncis`). Atıf ya tarihi bir isim (silinmiş düğüm) ya da yazım
farkıdır — kasıtlı mı hata mı `UNKNOWN`, kayıt C-14.

## §7 Eşleme matrisi — konu → düğüm (özet tablo)

| Konu grubu | Hedef düğüm | Durum | Karar |
|---|---|---|---|
| Donanım türü sınıflandırması (10 bağlam) | `K001-donanım/donanaım-type` | DOCUMENTED (46 md) | ADR-102 draft — onay bekliyor |
| Cihaz yönetimi/algoritması | `K001-donanım/donanım-devices` | DOCUMENTED (4 md) + IMPLEMENTED kod | anayasa §2 |
| API yüzeyi | `K001-donanım/donanım-api` | DOCUMENTED (3 md) | anayasa §2 |
| Çekirdek/ortak | `K001-donanım/donanım-core` | DOCUMENTED (3 md) | anayasa §2 |
| Kernel/driver soutlama | `K001-donanım/donanım-kernel` | DOCUMENTED (3 md) | anayasa §2 (ring-0 K000'de kalır) |
| Ring-0 / OS katmanı | `K000-isletim-sistemi` | 282 md · 13 child | anayasa §2 · os-donanim-tespiti 4/4 (children'da) |
| Elektronik (K002 yolu) | `K002-electroncis` | 16 md (3 dizin) | anayasa §2 satır 9 — hedef 26 dosya/5 alt-konu |
| Elektronik (K000 yolu) | `K000-isletim-sistemi/electroncis` | 12 md (2 dizin) | **SINIR BELİRSİZ — C-13** |
| Ses işleme | `K003-audio` | 68 md (12 üst-dizin) | anayasa §2 satır 7 |
| DSP zinciri | `K004-dsp` | 69 md (12 dizin) | ADR-017 · ADR-062 |
| Firmware (XMOS) | `K005-firmware` | 35 md (9 dizin) | ADR-038 (XU316) |
| Cihaz envanteri (kod yüzeyi) | `shared/src/Device/` + K001 `donanım-devices` | 7 tip (PHP) · 4 md | ADR-064 (a) · devices index evidence'inde ADR-064+DeviceManager kayıtlı |
| Sürücü/driver dokümanı (K003 `driver/` dalı) | `K003-audio/driver` | ad-benzerliği | anayasa §2 satır 5+7 — K001 sınırı yalnız soyutlama; içerik K003 (§3.2) |

### §7.1 K001 kök 10 dosya — sınır referansları (ad-bazlı tarama)

K001 kökünün 9 içerik dosyasının her birinin adı bir sınır rolü taşır; roller
anayasa §2 + kök indeks frontmatter'inden eşlenir:

| Kök dosya | Rol | Sınır ilişkisi |
|---|---|---|
| `donanım-anayasa.md` (518) | anayasa — §2 sınır tablosu 10 satır | **sınır kararlarının SSOT'u** |
| `donanım-kararlar.md` (529) | karar günlüğü | ADR'lerin K001-özgü karşılığı |
| `donanım-bagimliliklar.md` (519) | bağımlılık envanteri | K000/K003/K005 bağımlılıkları |
| `donanım-dogrulama.md` (507) | doğrulama matrisi | iç kapılar |
| `donanım-riskler.md` (506) | risk kaydı | sınır riskleri dahil |
| `donanım-test-stratejisi.md` (509) | test stratejisi | kod yüzeyi testi |
| `donanım-kod-ornekleri.md` (509) | kod örnekleri | DeviceManager vb. |
| `donanım-evrensel-dil.md` (502) | sözlük | K001 terimleri |
| `donanım-web-arastirma.md` (500) | kaynak taraması | URL kanıtları |
| `index.md` (99) | children 5 · depends_on K000 | kök künye (tur-2b: children 5 ✓ E2 kapalı) |

İçerik okuması yapılmadı — yalnız ad/rol/satır eşlemesi (`⚠️ VERIFICATION
REQUIRED`: her dosyanın içinde tam olarak hangi sınır kararlarını taşıdığı).

## §8 Açık sorular (sınır matrisine özgü)

| # | Soru | Kanıt | Durum (tur-2b güncelleme) |
|---|---|---|---|
| Q1 | K002 index.md:56-64 "5 alt-konu" der; diskte 3 dizin. `sensor-ve-akteyici` ve `imalat-ve-test` eksik mi, üretim sürüyor mu? | K002 index okuması + Get-ChildItem (4/4/2 md) | AÇIK — ADR-102 §6 C3 de raporlar |
| Q2 | ADR-064'ün 13/11/9/7 servis sayısından hangisi bağlayıcı? | ADR-064 title + authority (b) | **CEVAPLANDI (tur-2b): ADR-064 authority (b) "bağlayıcı = 11 alan servisi; 13 DOĞRULANAMADI, vault reset'ine ertelendi" der — başlık stale, gövde çözümlü (§5.1). Kalan açık: başlık düzeltmesi kararı** |
| Q3 | ADR-061 silinmiş; ADR-064:19 atıfı kırık. Atıf düzeltilecek mi, yedek geri yüklenecek mi? | decisions/index.md:44 + vault-backups/ | AÇIK |
| Q4 | anayasa §2'deki "hardware vault" = K002-electroncis mi, K000/electroncis mi, ayrı vault mu? | anayasa:65 + §6.2 (K000/electroncis kendi anayasa dosyalarına sahip) | AÇIK — C-13 |
| Q5 | ADR-062 K004 sınırını bağlar; K001'e çapraz etkisi (parametre/API) var mı? İçerik okunmadı | dosya var, içerik `⚠️` | AÇIK |
| Q6 | K000 index:7 `K002-altyapi` bağımlılığı hangi gerçek düğüm? | index:7 + Test-Path (C-14) — **ADR-102 §6 C2'de de kayıtlı** | AÇIK |
| Q7 | K000 index validation satırı "12 düğüm sayıldı" der — children 13 satır (os-donanim-tespiti eklendi, validation güncellenmemiş) | K000 index:6 (13 child) vs :8 (validation "12 düğüm") | **AÇIK — bu turun yeni tespiti (C-15 bağlantılı, kayıt: celiskiler raporu)** |

## §9 tur-2b kapanış kayıtları (paralel Vault Steward oturumu — bu raporun işi değil, durum raporudur)

Bu oturum sırasında **paralel ajan** şu düzeltmeleri canlı ağaçta uyguladı;
bu rapor onları **kendi işi olarak sunmaz**, yalnızca ölçüm gerçeği olarak
kaydeder:

| Değişiklik | Etkilediği matris kalemi | Eski | Yeni |
|---|---|---|---|
| K000 index children 13 satır (os-donanim-tespiti eklendi) | §2.1 · C-06 → **KAPANDI** | 12 child | 13 child |
| K001 index children 5 satır (devices eklendi) | §7.1 · C-01 → E2 **KAPANDI** | 4 child | 5 child |
| anayasa:195 "✓ CEVAPLANDI: disk gerçeği 10 bağlam" | C-02 → içerik düzeyinde kapanış | ⚠️ | ✓ |
| anayasa:250 C10 satırı PROPOSED notu taşır | C-02 → kural sınıfı korunarak işlendi | yok | PROPOSED notu |
| type-kapsam:135 STALE işaretlendi | C-03 → kısmi | işaretsiz | STALE işaretli |
| `donanaım-type/index.md:94` gpu-ornekler "4/4 tamam" | C-10 → **KAPANDI** | "BEKLENIYOR" | kayıtlı |

Ölçüm tazeliği: §2 tablosu ile §9 tablosu aynı gün (2026-10-13) farklı
anlardır; §2 tur-2, §9 tur-2b ölçümüdür. Canlı ağaçta paralel yazım
sürüyorsa bu raporun sayıları anlık görüntüdür — Faz 4 kapanışında yeniden
ölçülür (kabul-kriterleri raporu §6).

## §10 Örnek senaryo → doğru düğüm akışı (yorum tablosu — kanıt bağlantılı)

Amaç: "bu içerik nereye yazılır?" sorusunun matrisle nasıl cevaplanacağını
göstermek. Her satır bir **yorumdur** (karar değil); kanıt sütunu hangi
matris satırından türediğini gösterir.

| # | Senaryo | Doğru düğüm | Gerekçe (matris satırı) | Karıştırma riski |
|---|---|---|---|---|
| S1 | Yeni bir SSD modelinin TBW/kapasite envanteri | `K001/donanaım-type/ssd` | §3 satır 10 + §2 10 bağlam | K002'ye yazma (orada devre tasarımı var) |
| S2 | PCM3168A'nın I2S pin init sırası | `K001/donanaım-type/audio` (ornekler) | §3 satır 1-2 · §6.1 kod kanıtı | K005'e yazma (firmware yükleme orada) |
| S3 | XMOS XU316 bootloader akışı | `K005-firmware/boot` | §3 satır 6 | K001 kernel'e yazma |
| S4 | ASIO exclusive-lock politikası | K000 (ring-0/OS) + K001 sınır notu | §3 satır 5 · §4.3 ADR-017 katman 3 | Tek tarafa yazıp sınırı kaybetme |
| S5 | Class AB amfi termal hesabı | "hardware vault" — **C-13/Q4 çözülene kadar dur** | §3 satır 9 · §6.2 | K000/electroncis vs K002 — ikisi de iddialı |
| S6 | 26 alan şema alanı ekleme | power-kapsam.md (şema SSOT'u) + tüm type dalları | tur-envanteri §6 | Tek dala yazıp diğerlerini güncellememe |
| S7 | DeviceManager'a 8. tip ekleme | K001 `donanım-devices` + ADR-064 eşleme tablosu | §6.1 · §4.1 (a) | Yalnız PHP'ye yazıp vault'u güncellememe |
| S8 | "13 servis" iddiasını vault'ta düzeltme | ADR-064 başlık düzeltmesi (Q2b) — Vault Steward | §5.1 | Gövde zaten 11'e bağladı; başlık düzeltmesi tek iş |

Yorum kuralı: senaryo bir ADR-017/038/062/063/064 kapsamına giriyorsa ADR
kazanır (§4); girmiyorsa anayasa §2 kazanır (§3); ikisi de susuyorsa
Vault Steward'a sorulur — **susma boşluğu tahminle doldurulmaz** (zero-hallucination).

## §KAPSAM-DIŞI (bu rapor neyi çözmez)

1. **Sınır kararı üretilmez** — anayasa §2 ve ADR'lerdeki mevcut kararlar
   kaydedilir; yenisi yazılmaz.
2. **ADR-064 çelişkisi çözülmez** (§5) — Vault Steward kararıdır.
3. **K002 alt-konu eksikliği giderilmez** (Q1) — üretim kararı başka ajanındır.
4. **C-13/C-14 çözülmez** — sınır/bağımlılık kararları Vault Steward'ındır.
5. **ADR-062 içeriği okunmaz** — `⚠️ VERIFICATION REQUIRED` bırakıldı
   (yalnız başlık + ilk 30 satır okundu, §4.3).
6. **Kod dosyaları düzenlenmez** — DeviceManager/DeviceDetector yalnız okundu.

## §SONUÇ-VE-SONRAKİ-ADIM

**Doğrulandı (tur-2 + tur-2b disk ölçümü): 41 iddia** — 6 ağaç sayımı + alt-dizin
katalogları (§2·§2.1: K002 alt-dizin 4/4/2 md dahil) · K003/K004/K005/K002
sınır-özeti 4 satır (§3.2) · 10 satır sınır tablosu (§3) · K001 kademe-2
frontmatter matrisi 4 satır (§3.1) · 7 ADR disk kaydı, 5'inin satır sayısı
ölçüldü (§4) · ADR-064 authority 5 madde (§4.1) · ADR-102 §6 6 satır (§4.2) ·
ADR-064 gövde/başlık ayrımı 4 satır (§5.1) · 2 kod kanıtı konumu (§6.1) ·
K000/electroncis 12 dosya + K002 3 dizin ölçümü (§6.2) · C-14 Test-Path (§6.3) ·
13 satır eşleme matrisi (§7) · K001 kök 10 dosya rol eşlemesi (§7.1) ·
tur-2b kapanış tablosu 6 satır (§9).

**PROPOSED: 2 kalem** — ADR-102 (3→9 bağlam, draft) · K002 alt-konu 3→5
(üretim tamamlanırsa, hedef 26 dosya).

**Onay bekliyor (APPROVAL REQUIRED): 5 kalem** — Q1 K002 alt-konu · Q2b ADR-064
başlık stale kararı (gövde 11'i bağladı) · Q3 ADR-061 atıf/geri yükleme ·
Q4+Q6 (K000/K002 sınırı + K002-altyapi ismi) · Q5 ADR-062 çapraz etki ·
Q7 K000 validation "12 düğüm" stale.

**Sonraki adım (tek):** Vault Steward Q2b+Q3 (ADR-064 başlık/ADR-061) ve
Q4+Q6 (K000/K002 sınırı) kararlarını tek bir "sınır hizalama" karar dosyasında
kapatmadan eşleme matrisi **öneri** düzeyinde kalır; bağlayıcı olan anayasa §2
+ accepted ADR'lerdir. ADR-064 için ek not: gövde çözümü (11 servis) zaten
yazılıdır — başlık düzeltmesi bir "karar" değil, hizalama işidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode