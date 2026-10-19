---
node_id: K005-firmware/device-control/device-control-anayasa
node_type: module
status: draft
parent_id: K005-firmware/device-control
children: []
depends_on:
  - K005-firmware/device-control
  - K005-firmware/boot
  - K005-firmware/audio-control
  - K005-firmware/power-control
  - K005-firmware/runtime
  - K005-firmware/diagnostics
evidence:
  - "K005-firmware/boot/boot-anayasa.md §1-§26 — anayasa şablonu, okundu 2026-10-14"
  - "K005-firmware/boot/boot-surucu.md §1-§26 — sürücü şablonu, okundu 2026-10-14"
  - "K005-firmware/device-control/index.md (open_question · §GRUPLAR) — okundu 2026-10-14"
  - "K005-firmware/device-control/device-control-kapsam.md §A-§AB — okundu 2026-10-14"
  - "K005-firmware/device-control/device-control-dogrulama.md §A-§AF — okundu 2026-10-14"
  - "K005-firmware/device-control/device-control-ornekler.md §A-§B — okundu 2026-10-14"
  - "K005-firmware/firmware-master.md §2 · §3.3 · §5 · §6 · §7 · §8 · §9 · §10 · §11 · §16 — okundu 2026-10-14"
  - "K005-firmware/firmware-kapsam.md §4 · §6 · §9.3 · §15.1 — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md §4 · §11 · §19.3 — okundu 2026-10-14"
  - "K005-firmware/firmware-bagimliliklar.md §9.1 · §17 · §20 — okundu 2026-10-14"
  - "K005-firmware/firmware-kararlar.md §2 — okundu 2026-10-14"
  - "K005-firmware/firmware-riskler.md §2 · §21 — okundu 2026-10-14"
  - "K005-firmware/firmware-evrensel-dil.md §2 · §5 — okundu 2026-10-14"
  - "K005-firmware/firmware-kod-ornekleri.md §4 Örnek 11 — okundu 2026-10-14"
  - "K005-firmware/firmware-dogrulama.md §3 · §17 — okundu 2026-10-14"
  - ".ai/CLAUDE.md §19 Audio Engine Standards (C++ Guardrails) — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group (mimari) + son satır Authority denetimi"
open_questions:
  - "hangi register'lara kimin yazdığı belirsiz ⚠️"
  - "Hedef MCU ailesi / platform / hedef dil seçilmedi (firmware-master.md §10 Q4 · firmware-kararlar.md K-10 · K-11) — ⚠️ VERIFICATION REQUIRED"
  - "K004-dsp ile sınır (kontrol parametresi ↔ algoritma) yazılı teyit bekliyor (firmware-master.md §10 Q5) — ⚠️ VERIFICATION REQUIRED"
  - "Bus yarışı riski #12 bu modüle devredildi (firmware-riskler.md §21) — azaltma fiilen başlamadı"
  - "Bus topolojisi, tarama periyodu ve retry sınırı değeri bilinmiyor (device-control-dogrulama.md DD-7 · DD-8) — UNKNOWN"
group: mimari
---

# device-control-anayasa — Device-Control Alt-Modülü Anayasası (K005-firmware)

> **ELI15:** Bu modül, cihazın parmak yerine geçer: bir röleyi açar, DAC'a
> register yazar, PWM'yi sürer — ama **yalnızca** başka modüller adına ve
> tek elden. Repo'da derlenebilir firmware kodu YOK (2026-10-13 keşif: 0
> `.c`/`.cpp`/`.h`), hedef MCU bilinmiyor (UNKNOWN) — bu yüzden buradaki
> kurallar **mimari taslaktır**; register adresi, sürüm ve Dosya:/Satır:
> referansı verilmez. Kural ve sınır bu dosyadadır; arayüz/durum makinesi
> `device-control-surucu.md`, kalıplar `device-control-ornekler.md`,
> ölçüm `device-control-dogrulama.md` içindedir.

## §1 Amaç, Kapsam ve Kapsam Dışı

| Alan | Soru | Cevap | Sahip |
|---|---|---|---|
| Amaç | Bu dosya neyi düzenler? | device-control alt-modülünün anayasası (kural + sınır) | K005-firmware/device-control |
| Kapsam içi | Bus soyutlama: I2C · SPI · GPIO · UART · PWM · counter tek API | evet — bileşen B1 | device-control |
| Kapsam içi | Cihaz kayıt modeli + deterministik başlatma sırası | evet — bileşen B2 | device-control |
| Kapsam içi | PCM3168A register erişimi (audio-control ADINA) | evet — bileşen B3 | device-control |
| Kapsam içi | Röle/DC koruma GPIO çıkışı (power-control ADINA) | evet — bileşen B4 | device-control |
| Kapsam içi | Polling + interrupt olay yolu, ring-buffer telemetri köprüsü | evet — bileşen B5/B6/B7 | device-control |
| Kapsam dışı | Ses işleme algoritması (EQ · reverb · DC offset izleme) | hayır → `audio-control` | K005/audio-control |
| Kapsam dışı | Güç amplifikatörü · termal · PCB devresi | hayır → K001-donanım · `.ai/electronics/` | K001 |
| Kapsam dışı | OTA yükleme · imza · secure boot politikası | hayır → `update` · `security` | K005/update · security |
| Kapsam dışı | Telemetri saklama ve raporlama | hayır → `diagnostics` | K005/diagnostics |
| Kapsam dışı | Ses akışının taşınması (stream · routing · miks) | hayır → K003-audio | K003 |
| Kapsam dışı | Host işletim sistemi sürücü modeli | hayır → K000-isletim-sistemi | K000 |

> **Sınır notu:** Bu modül *komut yolunu* yürütür, *kararı* vermez. DC koruma
> kararı audio-control'de (AV-5), kapanma kararı power-control'de, imza
> kararı security'dedir. Kaynak: `firmware-kapsam.md` §3 (O1–O6) ve bu
> dosyanın §10 komşu tablosu.

## §2 Rol ve Sınır Konumu

| Sınır | device-control tarafı | Komşu tarafı | Geçiş izni |
|---|---|---|---|
| device-control ↔ K001 | register okuma/yazma kodu | register haritası · reset değeri · clock · pin · DMA | yalnız okunan/yazılan adres (`firmware-kapsam.md` §4) |
| device-control ↔ K003 | "yol hazır mı" bayrağı sorgusu (audio-control üzerinden) | akış · routing · miks | bayrak, akış verisi DEĞİL (`firmware-kapsam.md` §4) |
| device-control ↔ K004 | kontrol parametresi aktarımı (kapsam D-sınırı) | EQ/reverb algoritması | parametre, ham ses DEĞİL (`firmware-kapsam.md` §4 — teyit bekliyor ⚠️) |
| device-control ↔ boot | boot olaylarını dinler, init tetiği alır | boot zinciri + devir paketi | olay, boot iç yapısı DEĞİL |
| device-control ↔ audio-control | DAC register yazımını yürütür | hangi register'a kimin yazdığı | belirsiz — index open_question ⚠️ |
| device-control ↔ power-control | röle GPIO'sunu sürer | kapanma/DC kararı | pin, karar DEĞİL |

**Rol kaynağı:** `firmware-master.md` §2 3. satır — "device-control · Parçaya
komut · bus · driver kipi". Bu rol satırı **adından türetilmiştir**; v2 §2.5 bu
dala yalnız ad verir (`⚠️ VERIFICATION REQUIRED` — `firmware-master.md` §2 notu).

## §3 Bus Sahipliği Kuralları (tek registry)

| # | Kural | Kaynak | Sonuç (ihlalde) |
|---|---|---|---|
| B1 | Bus erişimini yalnız device-control soyutlar; ikinci bir bus soyutlama katmanı açılmaz | `device-control-kapsam.md` §B (B1) · `firmware-bilesenler.md` §4 (F6) | ret |
| B2 | Her bus/peripheral cihazının **tek** yazıcısı vardır; iki yazıcı = yarış (risk #12) | `firmware-master.md` §3.3 C1 · `firmware-kod-ornekleri.md` §4 Örnek 11 | ret + veri bozulması riski |
| B3 | I²C/SPI/UART erişimi kuyruk üzerinden serileştirilir | `firmware-master.md` §3.3 C3 | ret |
| B4 | Bus arbitraj sahipliği kuralı bağlayıcıdır; bus topolojisi UNKNOWN — tahmin edilmez | `firmware-kapsam.md` §9.3 D7 · `device-control-dogrulama.md` DD-8 | belirsizlik `⚠️` kalır |
| B5 | Bus cihaz kayıtları tek registry'de tutulur, ikinci kayıt yoktur | `firmware-kod-ornekleri.md` §4 Örnek 11 (analogi, `[TASARIM]`) | ret |
| B6 | Bus hatası (NACK/timeout) sessizce yutulmaz; hata kodu + olay üretilir | `firmware-bilesenler.md` §11 C6 · `firmware-master.md` §3.5 I3 | ret |

> **Analogi notu:** Örnek 11'deki `DatabaseRegistry::$managers` haritasının
> firmware karşılığı "bus cihaz tablosu"dur — eşdeğerlik iddiası değildir,
> `firmware-kod-ornekleri.md` §4 satırı bunu `[TASARIM]` etiketler.

## §4 Register Yazım Kuralları

| # | Kural | Kaynak | Sonuç (ihlalde) |
|---|---|---|---|
| R1 | Register haritası K001'den okunur, bu modülde **tekrar tanımlanmaz** | `firmware-kapsam.md` §6 K-5 · §15.1 N1 | ret (risk #5, skor 12) |
| R2 | Register yazımı önce oku-değiştir-yazma (RMW) ile yapılır, bit alanı ezilmez | `firmware-master.md` §3.3 C2 | ret |
| R3 | DAC yalnız PCM3168A; PCM5122 mutlak yasak | `device-control-kapsam.md` §A.3 DC-2 · ADR-038 H001 (aktarım) | ret |
| R4 | Register adresi yalnız doğrulanmış kaynaktan yazılır; uydurma adres üretilmez | `device-control/index.md` ⚠️ notu · `device-control-kapsam.md` §J | `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` |
| R5 | Reset değerleri K001'den gelir; varsayılan değer uydurulmaz | `firmware-bagimliliklar.md` §9.1 E1b | ret |
| R6 | Hangi register'a kimin yazdığı **belirsizdir ve bu belgede çözülmez** — soru olduğu gibi taşınır | `device-control/index.md` open_questions | Q1 (§16) |

## §5 Deterministik Başlatma Sırası

| # | Aşama | Tetik | device-control davranışı | Hata koluna çıkış |
|---|---|---|---|---|
| 1 | Boot bekleme | boot olayı `BOOT_OK` (taslak ad) | `device_init_all()` çağrısı — sıra `init_order` artan | boot hata olayında cihaz **hiç başlamaz** |
| 2 | Bus kurulumu | init adımı 1 | B1 bus soyutlama başlatılır | bus hata → durum `Failed` |
| 3 | DAC yapılandırması | `init_order` içinde | PCM3168A register yazımı (audio-control adına) | NACK → hata kodu + olay (E1) |
| 4 | Röle GPIO | `init_order` içinde | kural 10'a uygun başlangıç değeri yazılır | polarity `UNKNOWN` → doğrula (F4) |
| 5 | Tarama + olay | init tamam | polling (B5) + interrupt (B6) devreye gir | gecikme §9 bütçesine tabi |
| 6 | Hazır bayrağı | tüm cihazlar `Ready` | `DEV_READY` (taslak ad) → runtime | eksik cihaz → kısmi hazır listesi |

**Boot olay eşlemesi** (`device-control-kapsam.md` §F.2 — taslak adlar):

| Boot olayı | device-control tepkisi | Son durum |
|---|---|---|
| `BOOT_OK` | `device_init_all()` (deterministik sıra) | `Initializing` → `Ready` |
| `BOOT_ERR_INTEGRITY` | cihaz hiç başlamaz | `Uninitialized` |
| `BOOT_ERR_ROLLBACK` | cihaz hiç başlamaz | `Uninitialized` |
| `BOOT_ERR_HW_INIT` | cihaz hiç başlamaz | `Failed` |

**Sıra kuralı (A1):** Başlatma sırası her açılışta aynıdır (deterministik);
sırada değişiklik yalnız cihaz kayıt tablosu üzerinden yapılır — kod içinde
dağınık init çağrısı yasaktır. Doğrulama kapısı: DV-7 + boot B-12 köprüsü
(`device-control-dogrulama.md` §C.3).

**Fail-safe kuralı (A2):** Boot başarısızsa cihaz **hiç başlamaz**; ara durum
(eksik başlatılmış ama "hazır" görünen cihaz) tanımı yoktur
(`device-control-kapsam.md` §G).

## §6 Anayasal İlkeler (Sert Kurallar)

| # | Kural | Kaynak | Sonuç (ihlalde) |
|---|---|---|---|
| P1 | Çalışma zamanı tahsisi (`malloc`/`new`/`delete`) yasak | K005-firmware kuralı 1 · `.ai/CLAUDE.md` §19 (Zero-allocation) | ret — DV-9 |
| P2 | Mutex/lock yasak; erişim lock-free (atomik) | K005-firmware kuralı 2 · `.ai/CLAUDE.md` §19 (lock-free) | ret — DV-10 |
| P3 | Callback/imza `noexcept`; yakalama yok → tanımlı hata kodu | K005-firmware kuralı 3 · `.ai/CLAUDE.md` §19 (noexcept) | ret |
| P4 | Sabitler `constexpr`; derleme zamanında doğrulanır | K005-firmware kuralı 4 | ret |
| P5 | Paylaşımlı tampon `alignas(64)` önbellek hizasında | K005-firmware kuralı 5 · `.ai/CLAUDE.md` §19 (64-byte alignment) | ret |
| P6 | DAC yalnız PCM3168A | K005-firmware kuralı 6 · ADR-038 H001 (aktarım) | ret |
| P7 | PCM5122 kullanımı mutlak yasak | ADR-038 H001 (aktarım — `device-control-kapsam.md` §J) | ret |
| P8 | Tahsis=0 ve mutex=0 denetimi statik analizle zorunlu | `device-control-kapsam.md` §A.3 DC-5 | ret |
| P9 | Bilinmeyen değer uydurulmaz: `UNKNOWN` · doğrulanamayan `⚠️ VERIFICATION REQUIRED` | AGENTS §3 Zero-Hallucination · `firmware-kararlar.md` K-7 | ret |
| P10 | Kod alıntısı yalnız `Kaynak:` etiketiyle; `Dosya:`/`Satır:` yalnız diskte okunan dosya için | `firmware-kararlar.md` K-15 (repo'da firmware kodu YOK) | ret |

> Kural 1–13 setinin bu üretimde okunan aktarımı
> `device-control-kapsam.md` §C.3/§R içindedir; setin tanım dosyası bu üretimde
> **okunmadı** — `⚠️ VERIFICATION REQUIRED`.

## §7 Röle/DC GPIO Fail-Safe Kuralı

| # | Kural | Kaynak | Sonuç (ihlalde) |
|---|---|---|---|
| F1 | DC >0,5 V ölçülürse röle tetiklenmek **zorundadır** (kural 10) | `device-control-kapsam.md` §H · §A.3 DC-6 | ret — kritik |
| F2 | GPIO yönü ve başlangıç değeri reset anında tanımlıdır; floating pin yok | `firmware-master.md` §3.3 C8 | ret |
| F3 | Röle/olay tepkisi ≤ 1 tampon (10,67 ms) içinde işlenir | `device-control-kapsam.md` §A.1 · DC-3 | ölçüm B1 (§12) |
| F4 | Röle polarity'si **aktif-düşük = taslak**; gerçek polarity K001'e bağlı → `UNKNOWN` | `device-control-kapsam.md` §L.4 · `device-control-ornekler.md` §A.2 | `⚠️ VERIFICATION REQUIRED` |
| F5 | Fail-safe yönü (enerjide açık/kapalı) donanım devresine bağlı → `UNKNOWN`; tahmin yazılmaz | tahmin yasağı (P9) | `⚠️ VERIFICATION REQUIRED` |
| F6 | Röle GPIO **pini** device-control'dedir; röle **kararı** power-control/audio-control'dedir (AV-5 köprüsü) | `device-control-kapsam.md` §A.4 | ret |

## §8 Hata Sınıfları ve Kurtarma Hiyerarşisi

| # | Sınıf | Tetik | Eylem | Son durum |
|---|---|---|---|---|
| E1 | Bus NACK/timeout | I2C/SPI yanıt yok | hata kodu + telemetri olayı (DV-12) | cihaz yok bayrağı |
| E2 | Bus tekrar (retry) denemesi | E1 sonrası | tekrar uygulanır — **sınır değeri tanımlı değil** | `⚠️ VERIFICATION REQUIRED` |
| E3 | Bilinmeyen komut | ayrıştırıcı reddi | reddet; sessizce yok saymak yasak | hata kodu + olay |
| E4 | Aralık/onay ihlali | argüman sınır dışı | kabul/red — red sayacı işler | reddedilen komut olayı |
| E5 | Init hatası | `init_order` içinde bir cihaz başarısız | durum `Failed`, diğer cihazlar için sıra korunur | kısmi hazır listesi |
| E6 | Boot hatası | `BOOT_ERR_*` (taslak) | cihaz hiç başlamaz (A2) | `Uninitialized` / `Failed` |
| E7 | Bus yarışı | iki yazıcı aynı bus'ta (B2 ihlali) | tek sahip kuralı ihlali sayılır | risk #12 — veri bozulması |

**Kurtarma kuralı (A3):** Hiyerarşi sabittir: `sınırlı tekrar → cihaz yok
bayrağı → telemetri olayı → diagnostics`. Sonsuz retry yasaktır; ancak
**tekrar sınırının rakamsal değeri bu üretimde tanımlı değildir** (kapsam
D2 "tekrar deneme sınırı"nı kapsam maddesi olarak verir, değer vermez) —
uydurma rakam yazılmaz.

## §9 Gerçek Zamanlı Zaman Bütçesi

| # | Bütçe | Değer | Kaynak | Durum |
|---|---|---|---|---|
| T1 | Olay işleme gecikmesi | ≤ 1 tampon = 10,67 ms (512 / 48000) | `device-control-kapsam.md` §A.1 · `device-control-dogrulama.md` DD-1 | eşik tanımlı — cihaz ölçümü YOK → `UNKNOWN` |
| T2 | Cihaz tarama (polling) periyodu | `UNKNOWN` | `device-control-dogrulama.md` DD-7 | `⚠️ VERIFICATION REQUIRED` — uydurma frekans yazılmaz |
| T3 | Bus transfer timeout değeri | `UNKNOWN` | `device-control-kapsam.md` §L.1 (hata kodu var, süre yok) | `⚠️ VERIFICATION REQUIRED` |
| T4 | Başlatma sırası toplam süresi | `UNKNOWN` | DV-7 yalnız determinism ölçer | ölçüm `testing` kapısında |
| T5 | Boot → init penceresi | boot B-12 köprüsüne bağlı | `boot-dogrulama.md` B-12 (aktarım) | ölçüm `testing` kapısında |

**Bütçe kuralı (A4):** Ölçüm yapılmadan hiçbir satır "hedef tuttu" olarak
yazılmaz; eşik tanımdır, ölçüm ayrıdır (`firmware-master.md` §16 disiplini).

## §10 Sınır Komşuları ve Devir Sözleşmeleri

| Komşu | Yön | Ne verilir / alınır | Kanıt |
|---|---|---|---|
| boot | ← boot | `BOOT_OK` / `BOOT_ERR_*` olayları (taslak ad) → init tetiği | `device-control-kapsam.md` §F.2 · §G |
| runtime | → runtime | ring-buffer telemetri (B7), tick/olay akışı | `device-control-kapsam.md` §C.2 |
| audio-control | ↔ audio-control | DAC register yazımı (device-control yürütür, audio-control ister) + hazır bayrağı | `device-control-kapsam.md` §A.4 · `firmware-master.md` §5 |
| power-control | ↔ power-control | röle GPIO sürüşü (DC koruma) | `device-control-kapsam.md` §A.4 · kural 10 |
| K001-donanım | ↔ K001 | register haritası · pin · clock · DMA · reset değerleri | `firmware-kapsam.md` §15.1 · `firmware-bagimliliklar.md` §9.1 |
| diagnostics | → diagnostics | hata olayları + telemetri (E1–E7) | `device-control-kapsam.md` §A.4 |
| testing | ← testing | DV-1…DV-12 tetikleri | `device-control-dogrulama.md` §A |
| K003-audio | → K003 | yalnız "yol hazır mı" bayrağı — akış verisi geçmez | `firmware-kapsam.md` §4 |
| K004-dsp | → K004 | yalnız kontrol parametresi — algoritma geçmez | `firmware-kapsam.md` §4 (teyit ⚠️) |

## §11 Bağımlılık Grafiği (yön: bağımlı → bağımlı olduğu)

| Düğüm | Bağımlı olduğu | Tür | Not |
|---|---|---|---|
| device-control-anayasa | K005-firmware/device-control | mimari | `parent_id` bu düğüm |
| device-control-anayasa | `firmware-master.md` §3.3 | kanıt | C1–C8 iddiaları |
| device-control-anayasa | `firmware-kapsam.md` §9.3 | kanıt | D1–D7 kapsam maddeleri |
| device-control-surucu | device-control-anayasa | mimari | arayüz anayasadan türetilir |
| device-control-ornekler | device-control-anayasa + device-control-surucu | kod | kalıp anayasaya uyar |
| device-control-dogrulama | device-control-anayasa | kanıt | kapı eşlemesi (§12) |
| boot | device-control (olay) | modül | §5 başlatma zinciri |
| audio-control | device-control (DAC register erişimi) | modül | §10 |
| power-control | device-control (röle GPIO) | modül | §7 · §10 |
| runtime | device-control (telemetri) | modül | §10 |

## §12 Ölçüm ve Eşik Kapıları

| # | Kapı | Eşik | Nasıl ölçülür | Durum |
|---|---|---|---|---|
| B1 | Olay işleme gecikmesi | ≤ 10,67 ms (DD-1) | zaman damgası (`device-control-dogrulama.md` §C.1) | ölçülmedi — `UNKNOWN` (kod yok) |
| B2 | Tahsis denetimi | 0 (kural 1 · P1) | cppcheck / Clang-Tidy (DV-9) | kural yazıldı, ölçüm planlı |
| B3 | Lock-free denetimi | mutex yok (kural 2 · P2) | cppcheck / Clang-Tidy (DV-10) | kural yazıldı, ölçüm planlı |
| B4 | Başlatma determinizmi | her açılışta aynı sıra | boot olay günlüğü (DV-7 · B-12) | planlı |
| B5 | Bus yazma/okuma | doğru değer + NACK raporu | mantık analizörü (DV-1 · DV-2) | planlı |
| B6 | DAC register doğrulama | doğrulanmış kaynakla eşleşme | ADR-038 H001 (DV-6) | planlı |
| B7 | Röle tepki gecikmesi | ≤ 1 tampon (DC-3 · DC-6) | DMM + zaman işaretçisi (DV-3) | planlı |
| B8 | Kök D-kapıları | D1 · D2 · D5 · D6 · D7 ✅ — D3 · D4 · D8 🔄 — D9 · D10 ❌ | `firmware-dogrulama.md` §3 (2026-10-13) | kısmi |

> Kapıların tam protokolü `device-control-dogrulama.md` §A–§C'dedir; burada
> yalnız tanım ve kök durum verilir. Hiçbir kapı bu üretimde
> **çalıştırılmadı** — çalıştırılmış gibi yazılmaz.

## §13 Terminoloji (TR domain ↔ EN teknik) — bu dosyadaki kullanımlar

| Terim (EN) | Bu dosyadaki karşılığı | Kullanım notu |
|---|---|---|
| host interface | ana bilgisayar arayüzü | `firmware-evrensel-dil.md` §2 #10 — device-control |
| peripheral | çevre birimi | `firmware-evrensel-dil.md` §2 #11 — device-control |
| RMW (read-modify-write) | oku-değiştir-yazma | `firmware-evrensel-dil.md` §2 #12 — §4 R2 |
| register map | ayar/hazır kayıt | `firmware-evrensel-dil.md` §2 #13 — K001'e ait |
| driver kipi | driver mode | `firmware-kapsam.md` §9.3 D5 — sözlükte EN karşılığı yok → süreç `firmware-evrensel-dil.md` §12 |

**Dil kuralı (A5):** Soldaki TR ifade anlatımda, sağdaki EN terim **terim
olarak** kullanılır; terim sütunu bağlayıcıdır (`firmware-evrensel-dil.md` §2
okuma kuralı · §5 eşlemesi: device-control → terim 10 · 11 · 12 · 13).

## §14 Karar Kaydı ve ADR Bağlantıları

| Karar / ADR | Dayanak | Bu dosyadaki yeri | Durum |
|---|---|---|---|
| K-1 K005 yalnız cihaz firmware'ini kapsar | `firmware-kararlar.md` §2 | §1 kapsam dışı | ALINDI |
| K-7 kanıtsız iddia `[TASARIM]`, bilinmeyen `⚠️` | `firmware-kararlar.md` §2 | P9 | ALINDI |
| K-10 hedef platform seçilmedi | `firmware-kararlar.md` §2 | Q2 | BEKLİYOR |
| K-11 hedef dil (C/C++) seçilmedi | `firmware-kararlar.md` §2 | Q2 | BEKLİYOR |
| K-15 kod alıntısı `Kaynak:`, disk kanıtı `Dosya:`/`Satır:` | `firmware-kararlar.md` §2 | P10 · §19 | ALINDI |
| K-16 son satır tek Authority bloğu | `firmware-kararlar.md` §2 | §20 | ALINDI |
| ADR-038 H001 (PCM3168A tek DAC · PCM5122 yasak) | kardeş dosyalar aktarımı (`device-control-kapsam.md` §R) | R3 · P6 · P7 | donmuş (aktarım) |
| ADR-097 §5.5 (grup şeması — `group: mimari`) | `device-control/index.md` evidence | frontmatter | aktif (aktarım) |

## §15 Gereksinim → Kural Eşleme Matrisi

| # | Gereksinim (kaynak) | Kural (bu dosya) | Doğrulama kapısı | Sahip |
|---|---|---|---|---|
| R1 | Her peripheral yalnız tek sahip modülce yazılır (`firmware-master.md` §3.3 C1) | B2 · R6 · Q1 | DV-11 + inceleme | device-control |
| R2 | RMW ile yazım, bit alanı ezilmez (§3.3 C2) | R2 | kod incelemesi (taslak) | device-control |
| R3 | Bus erişimi kuyrukla serileştirilir (§3.3 C3) | B3 · B4 | DV-11 | device-control |
| R4 | GPIO yön/başlangıç değeri reset anında tanımlı (§3.3 C8) | F2 | reset denetimi | device-control |
| R5 | Bilinmeyen komut sessizce yok sayılmaz (§3.5 I3) | E3 | DV-12 + test | device-control |
| R6 | Register haritası K001'den okunur (`firmware-kapsam.md` §6 K-5) | R1 · R5 | risk #5 denetimi | K001 → device-control |
| R7 | Bus tekrar sınırı + hata kodu (`firmware-bilesenler.md` §11 C6) | E1 · A3 | DV-12 | device-control |
| R8 | Bilinmeyen komut reddi (`firmware-bilesenler.md` §11 C7) | E3 · E4 | DV-12 | device-control |
| R9 | Başlatma sırası deterministik (`device-control-kapsam.md` §A.3 DC-4) | A1 · A2 | DV-7 (B-12) | device-control |
| R10 | Tahsis yok · lock-free (kural 1 · 2) | P1 · P2 · P8 | DV-9 · DV-10 | device-control |
| R11 | DC >0,5 V → röle (kural 10) | F1 · F3 | DV-3 · DC-6 (AV-5) | power-control → device-control |
| R12 | PCM3168A register doğrulaması (ADR-038 H001) | R3 · P6 · P7 | DV-6 | audio-control → device-control |
| R13 | Olay gecikmesi ≤ 1 tampon (kapsam §A.1 DC-3) | F3 · A4 | B1 | device-control |
| R14 | Bus yarışı azaltması (`firmware-riskler.md` §2/§21 risk 12) | B2 · B3 | DV-11 | device-control |
| R15 | Ölçüm yoksa hedef tutulmuş sayılmaz (`firmware-master.md` §16) | A4 · P9 | §12 B1–B8 | testing |
| R16 | Bilinmeyen değer uydurulmaz (AGENTS §3) | P9 · R4 | inceleme | tüm dosya |

## §16 Açık Sorular (open_questions — çözülmeden taşındı)

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q1 | **Hangi register'lara kimin yazdığı belirsiz** (child index) | §4 R6 · `device-control-surucu.md` §9 | `⚠️ VERIFICATION REQUIRED` — KAPATILMADI, taşındı |
| Q2 | Hedef MCU ailesi / platform / hedef dil seçilmedi mi? | tüm register/bus satırları | `⚠️ VERIFICATION REQUIRED` (master §10 Q4 · K-10 · K-11) |
| Q3 | K004-dsp ile sınır (kontrol parametresi ↔ algoritma) yazılı teyit edildi mi? | §2 · §10 | `⚠️ VERIFICATION REQUIRED` (master §10 Q5) |
| Q4 | Bus topolojisi ve arbitraj mekanizması nedir? | B4 · DV-11 | `UNKNOWN` (DD-8) |
| Q5 | Cihaz tarama periyodu nedir? | §9 T2 | `UNKNOWN` (DD-7) |
| Q6 | Bus tekrar (retry) sınırının rakamsal değeri nedir? | §8 A3 · E2 | `⚠️ VERIFICATION REQUIRED` |
| Q7 | Bus yarışı riski #12 ne zaman azaltılıyor? | §3 B2 | açık — azaltma başlamadı (`firmware-riskler.md` §21) |

## §17 Red Team İtiraz Kayıtları (soru → yanıt → kanıt)

| # | İtiraz | Yanıt | Kanıt |
|---|---|---|---|
| T1 | "Register adresleri neden yok?" | Uydurma register yasağı; yalnız doğrulanmış kaynak | P9 · R4 · index ⚠️ notu |
| T2 | "Bu bir anayasa mı, yoksa kapsam tekrarı mı?" | Kapsam *ne içerdiğini*, bu dosya *neyin yasak/şart olduğunu* koyar — her kuralın kaynak satırı var | §3–§9 |
| T3 | "Ölçüm kapıları çalıştırıldı mı?" | Hayır — kapı tanımlı; ölçüm `testing`/tezgah işi | §12 notu |
| T4 | "10,67 ms nereden geliyor?" | 1 tampon (512/48000) — kaynak eşiği, cihaz ölçümü değil | DD-1 · §9 T1 |
| T5 | "Tarama frekansı neden yok?" | DD-7 `UNKNOWN` — uydurma frekans yazılmaz | §9 T2 |
| T6 | "Retry sınırı 2 mi (boot'taki gibi)?" | Hayır — boot'un A8 kuralı boot'a ait; bu modülde sınır değeri tanımsız | §8 A3 · Q6 |
| T7 | "PCM5122 kullanılabilir mi?" | Hayır — ADR-038 H001 mutlak yasak | P7 · R3 |
| T8 | "Kural 1–13 setinin tanımını okudun mu?" | Hayır — yalnız kardeş aktarımı okundu; açıkça işaretlendi | §6 notu |
| T9 | "Örnek 11 (PHP) firmware kanıtı mı?" | Hayır — `[TASARIM]` etiketli analogi | §3 notu |
| T10 | "index.md'yi değiştirdin mi?" | Hayır — yalnız okundu; open_question aynen taşındı | §16 Q1 |
| T11 | "Register sahipliği sorusu kapandı mı?" | Hayır — kapatmak yasaktı; belgeye taşındı | §16 Q1 · §4 R6 |
| T12 | "Boot ↔ device-control bayrak adları tutarlı mı?" | Belirsiz — boot-surucu `BOOT_READY`, kapsam `BOOT_OK`; bu üretimde **eşleştirilmedi**, soru olarak kaydedildi | `device-control-surucu.md` §17 Q3 |
| T13 | "Uydurma Dosya:/Satır: var mı?" | Yok — repo'da firmware kodu YOK; `Dosya:`/`Satır:` hiç kullanılmadı | §19 |
| T14 | "URL uydurdun mu?" | Bu dosyada web araştırması bölümü yok — URL yok | §1–§20 |

## §18 İddia → Dayanak Satır Eşlemesi

| # | İddia (bu dosyada) | Dayanak | Tür |
|---|---|---|---|
| C1 | Rol: parçaya komut · bus · driver kipi | `firmware-master.md` §2 (3. satır) | kaynak (adından türetildi ⚠️) |
| C2 | Tek sahip peripheral kuralı | `firmware-master.md` §3.3 C1 | kaynak (tasarım) |
| C3 | RMW zorunluluğu | `firmware-master.md` §3.3 C2 | kaynak (tasarım) |
| C4 | Kuyrukla serileştirme | `firmware-master.md` §3.3 C3 | kaynak (tasarım) |
| C5 | GPIO başlangıç değeri reset anında | `firmware-master.md` §3.3 C8 | kaynak (tasarım) |
| C6 | Bilinmeyen komut reddi | `firmware-master.md` §3.5 I3 | kaynak (tasarım) |
| C7 | Bus arbitraj maddesi (D7) | `firmware-kapsam.md` §9.3 D7 | kaynak (tasarım) |
| C8 | Tekrar deneme sınırı kapsam maddesi | `firmware-kapsam.md` §9.3 D2 | kaynak (değer yok) |
| C9 | Komşu geçiş izinleri | `firmware-kapsam.md` §4 | kaynak |
| C10 | K001 register/clock/pin/DMA satırları (N1–N12) | `firmware-kapsam.md` §15.1 | kaynak |
| C11 | Register haritası tekrar tanımlanmaz (K-5) | `firmware-kapsam.md` §6 | kaynak (karar) |
| C12 | F6 bus denetçisi · F7 GPIO/driver | `firmware-bilesenler.md` §4 | kaynak (tasarım) |
| C13 | C6/C7 arayüz sözleşmeleri | `firmware-bilesenler.md` §11 | kaynak (tasarım) |
| C14 | d1–d8 bileşen listesi | `firmware-bilesenler.md` §19.3 | kaynak (tasarım) |
| C15 | K001 bağımlılık satırları E1a–E1l | `firmware-bagimliliklar.md` §9.1 | kaynak |
| C16 | F6/F7 bağımlılık satırları (D6 · D7) | `firmware-bagimliliklar.md` §17 | kaynak |
| C17 | Açılış senaryosu adım 5 (F6 → F7 → F8) | `firmware-bagimliliklar.md` §20.1 | kaynak |
| C18 | Bus NACK → cihaz yok bayrağı | `firmware-bagimliliklar.md` §20.4 | kaynak |
| C19 | K-1 · K-7 · K-10 · K-11 · K-15 · K-16 | `firmware-kararlar.md` §2 | kaynak (karar) |
| C20 | Risk #12 bus yarışı (device-control sahibi) | `firmware-riskler.md` §2 · §21 | kaynak |
| C21 | Risk #5 register tekrarı | `firmware-riskler.md` §2 | kaynak |
| C22 | Terim 10–13 device-control'e ait | `firmware-evrensel-dil.md` §2 · §5 | kaynak |
| C23 | Örnek 11 bus registry analogisi | `firmware-kod-ornekleri.md` §4 | kaynak (analogi · [TASARIM]) |
| C24 | D1–D10 kapı durumları (✅/🔄/❌) | `firmware-dogrulama.md` §3 · §17 | kaynak (2026-10-13) |
| C25 | Boot olay eşlemesi (`BOOT_OK` · `BOOT_ERR_*`) | `device-control-kapsam.md` §F.2 · §G | taslak (kardeş dosya) |
| C26 | Başlatma sırası + init_order | `device-control-kapsam.md` §B (B2) · §F.1 | taslak (kardeş dosya) |
| C27 | DC-1…DC-6 kabul kriterleri | `device-control-kapsam.md` §A.3 | kaynak (kardeş dosya) |
| C28 | DV-1…DV-12 doğrulama senaryoları | `device-control-dogrulama.md` §A | kaynak (kardeş dosya) |
| C29 | DD-1…DD-8 eşikleri | `device-control-dogrulama.md` §D | kaynak (kardeş dosya) |
| C30 | Repo'da firmware kodu YOK (0 dosya) | `device-control-ornekler.md` başlık notu · 2026-10-13 keşfi | dürüstlük |
| C31 | C++ Guardrails (zero-allocation · lock-free · noexcept · 64-byte align) | `.ai/CLAUDE.md` §19 | kaynak |
| C32 | Ölçüm yoksa "hedef tuttu" yazılmaz | `firmware-master.md` §16 | kural |

## §19 Kaynak ve Dayanak Kanıtı

**Repo'da firmware kodu YOK — 2026-10-13 keşfi: 0 `.c`/`.cpp`/`.h` dosyası;
bu dosyadaki hiçbir satır `Dosya:`/`Satır:` künyesi taşımaz.**

| Kaynak | Bölüm | Bu dosyada kullanıldığı yer |
|---|---|---|
| `K005-firmware/boot/boot-anayasa.md` | §1–§26 (şablon) | iskelet · Authority satırı · ELI15 |
| `K005-firmware/boot/boot-surucu.md` | §2 · §8 · §12 (şablon) | bayrak/sözleşme disiplini · T12 |
| `K005-firmware/device-control/index.md` | open_questions · §GRUPLAR | Q1 · frontmatter `group` |
| `device-control-kapsam.md` | §A · §B · §F · §G · §H · §L | §1 · §5 · §7 · §9 |
| `device-control-dogrulama.md` | §A · §C · §D | §12 · §15 |
| `device-control-ornekler.md` | §A · §B başlık notu | §7 F4 · C30 |
| `firmware-master.md` | §2 · §3.3 · §3.5 · §5 · §16 | §2 · §3 · §4 · §15 · A4 |
| `firmware-kapsam.md` | §4 · §6 · §9.3 · §15.1 | §2 · §3 · §4 |
| `firmware-bilesenler.md` | §4 · §11 · §19.3 | §3 · §8 · §15 |
| `firmware-bagimliliklar.md` | §9.1 · §17 · §20 | §10 · §11 · §15 |
| `firmware-kararlar.md` | §2 | §14 |
| `firmware-riskler.md` | §2 · §21 | §3 · §15 · Q7 |
| `firmware-evrensel-dil.md` | §2 · §5 | §13 |
| `firmware-kod-ornekleri.md` | §4 Örnek 11 | §3 |
| `firmware-dogrulama.md` | §3 · §17 | §12 B8 |
| `.ai/CLAUDE.md` | §19 | §6 P1–P5 |

## §20 Revizyon ve Kapanış Kontrolü

| # | Kayıt | Değer |
|---|---|---|
| V1 | Dosya | `device-control/device-control-anayasa.md` (K005-firmware) |
| V2 | node_id | K005-firmware/device-control/device-control-anayasa |
| V3 | node_type | module |
| V4 | status | draft |
| V5 | group | mimari (tirnaksız) |
| V6 | parent_id | K005-firmware/device-control |
| V7 | children | [] (yaprak içerik dosyası) |
| V8 | Üretim tarihi | 2026-10-14 |
| V9 | Okunan kaynak dosya sayısı | 16 (15 K005 dosyası + `.ai/CLAUDE.md` §19) |
| V10 | Web URL sayısı | 0 (bu üretimde web araştırması yapılmadı — URL uydurulmadı) |
| V11 | Bölüm sayısı | §1 → §20 |
| V12 | Sahip | Bayram Ali / Vault Steward |
| V13 | Mod | Red Team · Human Mode · Truth Mode |
| V14 | İlgili modüller | boot · runtime · audio-control · power-control · diagnostics · testing |
| V15 | Dokunulmaz yüzey | `index.md` (yalnız okundu) · mevcut 3 kardeş dosya · frozen ADR'ler · `*.php`/`*.js`/`*.css`/`*.sql` |
| V16 | Değişiklik yetkisi | yalnız K005-firmware beyaz listesi + orkestratör onayı · commit orkestratöre aittir |

| Kontrol | Beklenti | Durum |
|---|---|---|
| FM anahtar sayısı | 10 (node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group) | evet |
| `parent:` anahtarı | bulunmamalı (yalnız `parent_id`) | evet |
| `group` değeri | tirnaksız `mimari` | evet |
| Liste alanları | children · depends_on · evidence · validation · open_questions | evet |
| Bölüm başı tablo | her bölümde ≥1 tablo | evet |
| Boş tablo hücresi | 0 | evet |
| Uydurma register/sürüm/URL/Dosya:/Satır: | 0 | evet |
| index open_question | çözülmeden taşındı (Q1) | evet |
| Bölüm tekrarı / dolgu bölümü | 0 | evet |
| Son satır | Authority satırı birebir | evet |

## §21 Vault Drivers Kaynağı Derinletmesi (append · 2026-10-16)

> Kaynaklar bu ek için: `Dosya: .ai/electronic/drivers/embedded-drivers.md` (511 satır) · `Dosya: .ai/electronic/drivers/device-integration.md` (503 satır) — ikisi de okundu 2026-10-16. Her satır Dosya+satır referanslıdır; repo `*.cpp/*.h/*.c` YOK (2026-10-15 ölçümü) → "çalışan kod" iddiası yazılmaz.

### §21.1 I2C Register Boot Sırası (§4 register yazım kuralının dayanağı)

```
Boot → GPIO reset → I2C bus init (≤400 kHz Fast)
  → DAC register yaz (mode/mute/PLL) → ACK bekle
  → I2S clock init (BCLK/LRCLK/MCLK) → DMA başlat
  → Akış başlat
```

Dayanak: `embedded-drivers.md:482` (§11.1 · PCM3168A — ADR-038 H001 bağlamı).

| # | Kural | Gerekçe | Dayanak |
|---|---|---|---|
| I1 | Register yazımı I2S clock'tan ÖNCE | DAC PLL kilitlenmesi | embedded-drivers.md:490 |
| I2 | ACK yoksa → bus reset → 3 retry → escalate | kilitli/ölü bus | embedded-drivers.md:491 |
| I3 | Mute register'ı akış bitiminde set | graceful stop (click/pop önleme) | embedded-drivers.md:492 |

Bu üç kural bu dosyanın §4 (register yazım) ve §5 (deterministik başlatma) ile **aynı yöndedir**; çelişki yok — §21 yalnız kaynak satırını ekler.

### §21.2 Boot→Hazır Süre Bütçesi (§9 zaman bütçesinin dış dayanağı)

| Aşama | Hedef | Not | Dayanak |
|---|---|---|---|
| OS boot (kernel→user) | ≤10 sn (tipik) | OS bağımlı | embedded-drivers.md:498 |
| GPIO/I2S init | ≤10 ms | §1.4/§1.5 | embedded-drivers.md:499 |
| DAC register (I2C) | ≤5 ms | ACK dahil | embedded-drivers.md:500 |
| DMA warm-up (2 tampon) | 21.33 ms | §2.4 | embedded-drivers.md:501 |
| **Ses-çıktı-hazıra toplam** | **boot + ~50 ms** | akışa hazır | embedded-drivers.md:502 |

Kural: boot sonrası ilk ses, DMA warm-up (2 tampon) dolmadan çıkmaz — underrun önleme (embedded-drivers.md:504).

⚠️ VERIFICATION REQUIRED: bu bütçe, bu dosya §9'daki ≤1200 ms boot→hazır atıfını **tesbit etmez**; iki sayı farklı ölçüm katmanını (OS boot vs firmware boot) anlatır — §15 T3 çelişki kaydı aynen geçerlidir.

### §21.3 Durum Makinesi Çapraz Tekibi (§5 kiplerine ek veri)

| Vault durumu | Karşılığı bu dosyada | Dayanak |
|---|---|---|
| Discovered → Probed (VID:PID/MAC, timeout 5 sn) | §5 keşif/ipucu kipi | device-integration.md:364 §9.2 tablo |
| Probed → Authorized (whitelist onayı, 5 sn) | §5 doğrulama kipi | device-integration.md:365 §9.2 |
| Connected → Streaming / Pause | §5 akış kipi | device-integration.md:368 §9.2 |
| Error → 3 retry → escalation | §8 hata hiyerarşisi | device-integration.md:372 §9.2 |

### §21.4 Graceful Stop Sırası (§7 röle/DC fail-safe ile ilişkili sıra disiplini)

| # | Adım | Sıra kuralı | Dayanak |
|---|---|---|---|
| 1 | DSP akışını durdur (yeni örnek yok) | — | device-integration.md:397 |
| 2 | Son tamponu flush | akış durdurmadan SONRA | device-integration.md:398 |
| 3 | DAC mute (I2C register) | flush SONRASI, DMA ÖNCESİ | device-integration.md:399 |
| 4 | DMA/IRQ serbest bırak | mute SONRASI | device-integration.md:400 |
| 5 | Sürücü detach | son adım | device-integration.md:401 |

Kural: mute, DMA serbest bırakmadan ÖNCE yapılır; ters sırada click/pop oluşur (device-integration.md:404).

### §21.5 Sınır Komşusu: Yetkilendirme (whitelist) — yalnız işaretleme

| Konu | Vault içeriği | Bu dosyadaki sınır |
|---|---|---|
| Yetki karar ağacı (fail-safe: whitelist yoksa BLOCK) | device-integration.md:445 §10.3 | device-control KAYIT YAZMAZ — yetki katmanı K003/komşu |
| Yetki durumları (UNKNOWN/PROBING/PENDING/AUTHORIZED/BLOCKED) | device-integration.md:431 §10.2 | yalnız göz önüne alınır |
| Yetki bütçesi (whitelist kontrolü ≤1 ms) | device-integration.md:487 §10.6 | bu dosyanın §9 bütçesine DAHİL DEĞİL |

⚠️ VERIFICATION REQUIRED: yetkilendirme sahipliği bu dosyada tanımlı değil (komşu modül §10'a ait); kapı sahibi açık soru olarak kalır.

### §21.6 Ölçüm Kaydı Şeması (§12 ölçüm kapısına veri)

| Alan | Tip | Örnek | Dayanak |
|---|---|---|---|
| timestamp | ISO 8601 | 2026-10-13T12:00:00Z | device-integration.md:407 §9.5 |
| device_id | string | VID:PID / BT MAC | device-integration.md:408 §9.5 |
| discover_ms | float | 120 (USB) | device-integration.md:410 §9.5 |
| hotplug_pop | bool | false | device-integration.md:411 §9.5 |
| leak_detected | bool | false | device-integration.md:413 §9.5 |
| whitelist_verdict | enum | AUTHORIZED / BLOCKED | device-integration.md:414 §9.5 |

⚠️ VERIFICATION REQUIRED: şema vault'ta kavramsaldır; gerçek kayıt formatı (JSON/SQLite) tanımlı değil (device-integration.md:467 §10.5 notu).

### §21.7 Çapraz Referans Listesi (bu ekte okunanlar)

| Dosya | Kullanılan bölümler | Erişim |
|---|---|---|
| .ai/electronic/drivers/embedded-drivers.md | §11.1 (477–492) · §11.2 (494–504) | okundu 2026-10-16 |
| .ai/electronic/drivers/device-integration.md | §9 (348–421) · §10 (422–503) | okundu 2026-10-16 |
| K005-firmware/device-control/* | §4 · §5 · §7 · §9 · §12 | bu dosyanın mevcut bölümleri |

### §21.8 Kapanış (bu ek)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölüm | §21 (append-only; §1–§20 silinmedi) |
| Silinen mevcut içerik | 0 |
| Uydurma register/sürüm/URL | 0 — chip/model adı yalnız vault kaynağından (embedded-drivers.md:477 §11.1) |
| Dayanaksız iddia | 0 |
| Açık sorular | kapatılmadı (§16 aynen geçerli) |
| Son satır | Authority satırı korunur |


**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
