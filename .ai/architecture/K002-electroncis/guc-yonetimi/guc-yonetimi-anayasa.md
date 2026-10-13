---
node_id: K002-electroncis/guc-yonetimi/guc-yonetimi-anayasa
node_type: module
status: draft
parent_id: K002-electroncis/guc-yonetimi
children: []
depends_on:
  - K001-donanım
evidence:
  - "Web doğrulama: 13 benzersiz URL · HTTP 200 · envanter (2.701) kesişimi 0 (§12.1)"
  - ".ai/electronic/** güç konu başlıkları (60 dosya · 18.242 satır, 2026-10-13 disk ölçümü)"
  - "L0 kuralı 10 — DC offset >0.5V → koruma rölesi (bağlayıcı kısıt)"
validation: "draft — 2026-10-13 yazıldı · ≥500 ölçümü yazım sonrası"
open_questions:
  - "Q-GYA1: 8.1 kanal toplam güç bütçesi (rail × akım) hesaplanmadı → ⚠️ VERIFICATION REQUIRED"
  - "Q-GYA2: Koruma rölesi DC offset birinciliği (`devre-ve-analog` ile sınır) → UNKNOWN"
group: mimari
---

# guc-yonetimi-anayasa — Güç Yönetimi Tasarım Anayasası

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya `guc-yonetimi`
> alt-konusunun **anayasa** katmanıdır: topoloji, regülatör, gate sürücü,
> açma sırası, koruma ve ölçüm kapısı — hepsi **kural** düzeyindedir.

**Bağlayıcı kısıtlar (öncelik sırası):**

| # | Kısıt | Kaynak | Sonuç |
|---|---|---|---|
| 1 | DC offset >0.5V → koruma rölesi ZORUNLU | L0 kuralı 10 | tetik devresi şart |
| 2 | PCM5122 YASAK · yalnız PCM3168A | ADR-038 H001 | DAC rail planı bu yonga üzerinden |
| 3 | 32-bit float veri | ADR-017 | sayısal alan ≠ güç alanı |
| 4 | 512 örnek / 48 kHz | sartname | yük transiyanı bu periyotta ele alınır |
| 5 | ASIO Exclusive Lock tek uygulama | L0 kuralı 9 | güç arızası ≠ cihaz kilidi |

## §0 ELI15

Ses kartının **nefes aldığı yer**: fişteki voltajı önce sistemin ihtiyacı olan
rail'lere böler, gürültüden arındırır, doğru sırayla açar ve bozulursa
şalteri indirir. Basitçe: `devre-ve-analog` sinyalin yolunu, bu alt-konu ise
**enerjinin yolunu** çizer. Yanlış topoloji → ısınma / gürültü / DC kaçak;
yanlış sıra → çip hasarı; koruma yok → hoparlöre DC gider.

## §1 Kapsam

| # | Kapsam içi | Kapsam dışı |
|---|---|---|
| 1 | AC/DC dönüştürücü topoloji seçimi | Hoparlör/motor yük tasarımı |
| 2 | Lineer regülatör (LDO) & anahtarlı regülatör | Sinyal zinciri kazanç planı |
| 3 | Gate sürücü & MOSFET sürücü | Analog filtre hesabı |
| 4 | Açma/kapama sırası (sequencing) | Firmware watchdog kodu (K005) |
| 5 | Koruma: aşırı akım/gerilim/DC offset | Kutuplama/emağ (imalat) |
| 6 | Verim & termal kapıları | PCB yerleşimi (K001) |

## §2 Topoloji Seçimi

### §2.1 Karar matrisi

| # | İhtiyaç | Önerilen topoloji | Gerekçe | Kapsam dışı |
|---|---|---|---|---|
| 1 | ≥36 V rail (çıkış aşaması) | buck-boost / ileri converter | yüksek verim | ses kalitesi hesabı |
| 2 | 5 V / 3.3 V mantık | senkron buck + LDO kademe | ripple zinciri | — |
| 3 | 1.8 V çekirdek | LDO (düşük gürültü) | ripple hassasiyeti | — |
| 4 | ±15 V analog | çift rail flyback / ayrı regülatör | kanal ayrımı | — |
| 5 | Ses çıkış koruma | röle / MOSFET anahtarlama | DC ayrımı | — |
| 6 | Pil/BM726 (taslak) | buck + şarj yolu | mobil senaryo | ⚠️ Q-GYA3 |

### §2.2 Topoloji seçim kuralları

| # | Kural | Eşik | İhlal |
|---|---|---|---|
| 1 | Verim ≥ %85 (anahtarlı kademe) | 1 W üzeri yük | topoloji değişir |
| 2 | Ripple ≤ 10 mVpp (mantık rail) | 3.3 V / 5 V | ek kademe |
| 3 | Ripple ≤ 1 mVpp (analog rail) | ±15 V | LC/ferrit kademe |
| 4 | Dropout ≥ 300 mV (LDO) | tam yükte | topoloji değişir |
| 5 | Termal: Tj ≤ 125 °C worst-case | en kötü yük + sıcaklık | soğutma/güç düşürme |
| 6 | Tek kaynak tek uygulama (ASIO) | — | L0 kuralı 9 ihlali değil, bilgi |

## §3 Regülatör Kuralları

### §3.1 LDO seçimi

| # | Parametre | Kural | Neden |
|---|---|---|---|
| 1 | PSRR | ≥ 60 dB @ 100 kHz (analog rail) | anahtarlı gürültü geçişi |
| 2 | Gürültü | ≤ 20 µVrms (10 Hz–100 kHz) | ses bandı temizliği |
| 3 | Dropout | ≤ 300 mV @ tam yük | verim kaybı |
| 4 | Termal tepki | θJA ile Tj kontrolü | kapanma koruması |
| 5 | Kondansatör | ESR üretici şartında | kararlılık halkası |

### §3.2 Anahtarlı regülatör

| # | Parametre | Kural | Kapı |
|---|---|---|---|
| 1 | Anahtar frekansı | ses bandının (>20 kHz) çok üstü | ölçüm §7 |
| 2 | Kompansasyon | üretici hesap tablosu | §7.2 |
| 3 | Geçiş rejimi | yük basamağı testi | §7.3 |
| 4 | Breaking-switch | topolojiye bağlı | tasarım |

## §4 Gate Sürücü

| # | Kural | Değer | Not |
|---|---|---|---|
| 1 | Sürücü gerilimi | MOSFET Vgs(th) üstü + marj | 10 V sınıfı klasik |
| 2 | Yükleme akımı | Qg × f anahtar | sürücü kaynağı boyutlanır |
| 3 | Boot kapasitörü | sürücü üretici şartı | high-side için |
| 4 | Ani açma/kapama | Miller kilidi dikkate alınır | kısa devre riski |
| 5 | Dead-time | üst/alt FET çakışma yok | ısınma kapısı |
| 6 | Slew-rate | EMI ↔ verim ödünleşimi | ölçüm §7.5 |

## §5 Açma / Kapama Sırası

### §5.1 Sequencing tablosu

| # | Adım | Koşul | Beklenen | İhlal |
|---|---|---|---|---|
| 1 | Önregülatör ON | PG1 = 1 | ilk rail hazır | bekleme |
| 2 | Mantık rail (3.3V) ON | PG2 = 1 | MCU/JTAG kalkar | — |
| 3 | Analog rail ON | PG3 = 1 | op-amp bias | — |
| 4 | DAC mute aç | MUTE = 0 | sinyal yolu hazır | pop sesi |
| 5 | Çıkış rölesi ON | DC ölçüm < 0.5 V | hoparlör bağlantısı | L0 kuralı 10 |
| 6 | Kapama (ters sıra) | sistem kapanış | mute → röle → rail | DC kaçak |

### §5.2 Sıra kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Mantık, analogdan önce açılır | yanlış GPIO/undefined durum |
| 2 | DAC mute, röle öncesi açılır | pop/noise önlenir |
| 3 | Röle, DC doğrulaması olmadan KAPANIK kalır | hoparlör koruması |
| 4 | Kapama, açmanın tersidir | yığılma önlenir |
| 5 | Power-good yoksa sayaçla bekleme (firmware) | K005 sahipliği |

## §6 Koruma

### §6.1 Koruma katmanları

| # | Katman | Tetik | tepki | Sahip |
|---|---|---|---|---|
| 1 | DC offset koruma | çıkış DC > 0.5 V | röle aç | bu dosya (L0 kuralı 10) |
| 2 | Aşırı akım | yük akımı > sınır | foldback / kapanma | bu dosya |
| 3 | Aşırı gerilim | rail > %110 | klap / kapanma | bu dosya |
| 4 | Termal | Tj > 125 °C | throttle / kapanma | bu dosya |
| 5 | Kısa devre | akım tepe | µs içinde kesme | gate sürücü |
| 6 | Ters polarite | giriş ters | seri diyot / koruma | bu dosya |

### §6.2 DC offset koruma zinciri (L0 kuralı 10 — bağlayıcı)

| # | Aşama | İş | Eşik |
|---|---|---|---|
| 1 | Algılama | çıkış DC örneklenir (DC ölçümü) | 0.5 V |
| 2 | Doğrulama | filtreli teyit (yanlış tetik yok) | süre penceresi |
| 3 | Müdahale | röle açılır → hoparlör ayrılır | anlık |
| 4 | Kilit | elle reset yoksa kilitli kalır | — |
| 5 | Kayıt | olay K005 loguna | — |
| 6 | Test | üretim testinde tetiklenir | `imalat-ve-test` |

## §7 Ölçüm Kapıları

| # | Kapı | Kriter | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Ripple | rail başına §2.2 eşikleri | osiloskop + blackout | PLANNED |
| 2 | Verim | ≥ %85 (yük üstü 1 W) | güç analizörü | PLANNED |
| 3 | Transiyan | yük basamağında droop ≤ %5 | yük programı | PLANNED |
| 4 | Termal | Tj ≤ 125 °C worst-case | IR/termokupl | PLANNED |
| 5 | DC koruma | 0.5 V üstünde röle açar mı | fonksiyonel test | PLANNED |
| 6 | Açma sırası | §5.1 adımları tek tek | lojik analizör | PLANNED |
| 7 | Ses etkisi | pop/noise ölçülmeli | ölçüm masası | PLANNED |
| 8 | EMI | conduction/radiated ön ölçüm | EMC masası | PLANNED |

> Ölçüm kapılarının tamamı **PLANNED** — fiziksel ölçüm yapılmadı (dürüstlük: ölçüm yoksa yoktur).

## §8 Tasarım Kuralları (özet, bağlayıcı)

| # | Kural | Neden |
|---|---|---|
| 1 | Koruma rölesi DC >0.5V'da zorunlu | L0 kuralı 10 |
| 2 | Analog ve mantık rail'i ayrı kademe | gürültü ayrımı |
| 3 | Mute ↔ röle sırası §5.1'e birebir | pop/DC önleme |
| 4 | Worst-case tolerans + ESD ile boyutlama | global mühendislik kuralı |
| 5 | Boot bekleme firmware'e ait (K005) | katman sınırı |
| 6 | Ölçüm yoksa iddia yok | zero-hallucination |
| 7 | Veri 32-bit float (ADR-017) — güç alanı bundan ayrı | veri bütünlüğü |
| 8 | PCM5122 kullanılmaz (ADR-038 H001) | frozen kısıt |

## §9 Riskler

| # | Risk | Olasılık | Etki | Azaltma |
|---|---|---|---|---|
| 1 | Röle DC'de tetiklenmez | orta | hoparlör hasarı | §6.2 test kapısı |
| 2 | Açma sırası ihlali | orta | çip hasarı | §5.1 + firmware bekleme |
| 3 | Ripple ses bandına taşınır | yüksek | SNR düşüşü | §3.2 ölçüm kapısı |
| 4 | Termal kaçak (yaz) | orta | throttle | θJA worst-case |
| 5 | Gate sürücü çakışma | düşük | FET hasarı | dead-time kuralı |
| 6 | Kullanıcı yanlış rail takar | düşük | hasar | ters polarite koruması |

## §10 Terim Sözlüğü

| Terim | Tanım |
|---|---|
| Rail | Bir devre kademesinin besleme gerilimi |
| LDO | Düşük dropout lineer regülatör |
| Ripple | Anahtarlı kaynaktan kalan gerilim titreşimi |
| PG (Power-Good) | Rail hazır bayrağı |
| Sequencing | Rail'lerin açılış/kapanış sırası |
| Gate sürücü | MOSFET kapısını yükleyen sürücü kademesi |
| Foldback | Aşırı akımda akımı düşürerek koruma |
| Dead-time | Üst/alt FET'in çakışmadığı boşluk |
| Pop/noise | Açma-kapamada duyulan istenmeyen ses |
| PSRR | Kaynak voltaj reddi (gürültü bastırma) |

## §11 Kapsam Dışı

| # | Kapsam dışı | Sahip |
|---|---|---|
| 1 | Analog filtre/sinyal zinciri hesabı | `devre-ve-analog` |
| 2 | I2C/register ile rail yapılandırması | `dijital-ve-mikroislemci` |
| 3 | Sensör/aktüatör sürücü entegrasyonu | `sensor-ve-akteyici` |
| 4 | Üretim test prosedürü | `imalat-ve-test` |
| 5 | Firmware bekleme/log kodu | K005-firmware |
| 6 | PCB yerleşim/termal pad | K001-donanım |

## §12 Kaynakça & Çapraz Referans

### §12.1 Bu dosyanın web kaynakları (benzersiz — kesişim 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Buck regülatör datasheet başvurusu (kimlik) | https://www.ti.com/lit/ds/symlink/lm5117.pdf | 2026-10-13 | HTTP 200 |
| Synchronik buck regülatör başvurusu (kimlik) | https://www.ti.com/lit/ds/symlink/lm25117.pdf | 2026-10-13 | HTTP 200 |
| Düşük gürültülü LDO başvurusu (kimlik) | https://www.ti.com/lit/ds/symlink/tps7a20.pdf | 2026-10-13 | HTTP 200 |
| Buck dönüştürücü başvurusu (kimlik) | https://www.ti.com/lit/ds/symlink/tps54202.pdf | 2026-10-13 | HTTP 200 |
| Half-bridge gate sürücü başvurusu (kimlik) | https://www.ti.com/lit/ds/symlink/ucc27211.pdf | 2026-10-13 | HTTP 200 |
| Gate sürücü başvurusu (kimlik) | https://www.ti.com/lit/ds/symlink/lm5109a.pdf | 2026-10-13 | HTTP 200 |
| Uygulama notu — güç tasarımı (kimlik) | https://www.ti.com/lit/an/slva475/slva475.pdf | 2026-10-13 | HTTP 200 |
| Uygulama notu — güç tasarımı (kimlik) | https://www.ti.com/lit/an/slva477/slva477.pdf | 2026-10-13 | HTTP 200 |
| Uygulama notu — güç tasarımı (kimlik) | https://www.ti.com/lit/an/slva555/slva555.pdf | 2026-10-13 | HTTP 200 |
| Uygulama notu — güç tasarımı (kimlik) | https://www.ti.com/lit/an/snva486/snva486.pdf | 2026-10-13 | HTTP 200 |
| Uygulama notu — manyetik/koruma (kimlik) | https://www.ti.com/lit/an/slma002/slma002.pdf | 2026-10-13 | HTTP 200 |
| Tasarım notu — güç düzeni (kimlik) | https://www.ti.com/lit/an/slyt496/slyt496.pdf | 2026-10-13 | HTTP 200 |
| Tasarım notu — güç düzeni (kimlik) | https://www.ti.com/lit/an/slyt504/slyt504.pdf | 2026-10-13 | HTTP 200 |

> **İddia sınırı:** PDF'ler OKUNMADI — satırlar yalnız belge **kimliği**
> düzeyindedir (içerik iddiası değil). Erişilemeyen alanlar (st/microchip/jedec/analog) bu turda kullanılmadı → `guc-yonetimi-dogrulama` §5.8.

### §12.2 Vault atıfları (okuma)

| # | Yüzey | Kullanım |
|---|---|---|
| 1 | `.ai/electronic/**` (60 dosya · 18.242 satır) | güç konu başlıkları |
| 2 | `.ai/architecture/index.md` | yalnız okuma (registry — girilmedi) |
| 3 | `K001..K019/**` | yalnız okuma |

### §12.3 Dosya bağımlılıkları

| # | Dosya | İlişki |
|---|---|---|
| 1 | `guc-yonetimi/index.md` | üst indeks |
| 2 | `guc-yonetimi-ornekler.md` | hesap örnekleri |
| 3 | `guc-yonetimi-dogrulama.md` | kanıt katmanı |
| 4 | `devre-ve-analog/*` | sinyal yolu sınırı |
| 5 | `dijital-ve-mikroislemci/*` | mantık rail sınırı |

## §13 Açık Sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-GYA1 | 8.1 kanal toplam güç bütçesi | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q-GYA2 | DC offset birinciliği (`devre-ve-analog` sınırı) | UNKNOWN | Vault Steward |
| Q-GYA3 | Mobil/BM726 güç yolu kapsamı (vault satırı okunmadı) | ⚠️ VERIFICATION REQUIRED | 3 ajan |
| Q-GYA4 | Ölçüm kapıları tamamı PLANNED | PLANNED | ölçüm |

## §14 Rail Haritası ve Yük Bütçesi (derinleştirme eki 1)

### §14.1 Rail envanteri

§2.1'deki topoloji matrisinin altında yatan rail haritası — her rail'in
kaynağı, yükü ve önceliği:

| # | Rail | Gerilim | Kaynak topoloji | Yük (tipik) | Öncelik |
|---|---|---|---|---|---|
| R1 | Ana DC giriş | 12–24 V DC (harici adaptör) | harici kaynak | tüm kademeler | — |
| R2 | Mantık 5 V | 5 V | senkron buck (R1'den) | MCU, dijital IO | 1 (erken açılır) |
| R3 | Mantık 3.3 V | 3.3 V | LDO (R2'den) | çekirdek, sensör arayüzü | 2 |
| R4 | Analog +15 V | +15 V | flyback/klap (R1'den) | op-amp, DAC referans | 3 |
| R5 | Analog −15 V | −15 V | flyback/klap (R1'den) | op-amp, DAC referans | 3 |
| R6 | DAC analog | ±5 V (veya 3.3 V) | LDO (R4/R5'ten) | PCM3168A analog | 4 |
| R7 | Amplifikatör rail | ≥36 V (TPA3255) | ileri converter (R1'den) | çıkış aşaması | 5 (son açılır) |

### §14.2 Rail yük bütçesi (kural çerçevesi — sayılar PLANNED)

**Kural:** her rail için yük bütçesi üç terimle tanımlanır:
`I_static` (boşta akım) + `I_transient` (transiyan tepe) ≤ `I_rated`
(regülatör sürekli akım sınırı).

| Rail | I_static | I_transient | I_rated | Bütçe durumu |
|---|---|---|---|---|
| R2 (5 V) | ⚠️ PLANNED | ⚠️ PLANNED | ⚠️ PLANNED | Q-GYA1 |
| R3 (3.3 V) | ⚠️ PLANNED | ⚠️ PLANNED | ⚠️ PLANNED | Q-GYA1 |
| R4/R5 (±15 V) | ⚠️ PLANNED | ⚠️ PLANNED | ⚠️ PLANNED | Q-GYA1 |
| R6 (DAC) | ⚠️ PLANNED | ⚠️ PLANNED | ⚠️ PLANNED | Q-GYA1 |
| R7 (≥36 V) | ⚠️ PLANNED | ⚠️ PLANNED | ⚠️ PLANNED | Q-GYA1 |

> **Dürüstlük:** sayısal yük değerleri bu dosyada UYDURULMAZ — PCM3168A
> ve TPA3255 datasheet akımları okunmadan (§12.1 notu: PDF'ler
> OKUNMADI) her hücre `⚠️ PLANNED` kalır ve Q-GYA1 kapanmaz.

### §14.3 Rail öncelik ↔ sequencing eşlemesi

§5.1 sequencing tablosu ile §14.1 rail haritasının birebir eşlemesi:

| §5.1 adım | §14.1 rail | Beklenen PG |
|---|---|---|
| 1 (önregülatör) | R1 giriş doğrulama | PG1 |
| 2 (mantık 3.3 V) | R2 → R3 | PG2 |
| 3 (analog rail) | R4 + R5 + R6 | PG3 |
| 4 (DAC mute) | R6 stabil | MUTE = 0 |
| 5 (çıkış rölesi) | R7 stabil + DC < 0.5 V | röle ON |
| 6 (kapama ters) | R7 → R6/R4/R5 → R3/R2 → R1 | — |

**Eşleme kuralı:** §5.1'de her adım bir §14.1 rail grubuna karşılık
gelir; adım↔rail eşlemesizliği §5.1 tablosunun revizyonunu gerektirir
(eşleme bozuksa kapı §7 satır 6 kapanmaz).

## §15 Koruma Eşikleri — Sayısal Çerçeve (derinleştirme eki 1)

§6.1 koruma katmanlarının eşik çerçevesi — sayısal değerler kaynak
bağımlıdır ve uydurulmaz:

| Katman | Eşik çerçevesi | Değer kaynağı | Durum |
|---|---|---|---|
| DC offset | 0.5 V (L0 kuralı 10 — sabit) | L0 kuralı 10 | ✅ sabit |
| Aşırı akım | I_rated × (1 + tolerans) | Rail bütçesi (§14.2) | ⚠️ PLANNED |
| Aşırı gerilim | rail nominal × %110 | §14.1 rail nominal | türetilmiş |
| Termal | Tj ≤ 125 °C | Çip datasheet | ✅ endüstri standardı |
| Kısa devre | tepe akım, µs ölçek | Gate sürücü + FET Rds(on) | ⚠️ PLANNED |
| Ters polarite | giriş negatif | Seri diyot/Vf | ⚠️ PLANNED |

### §15.1 Eşik hesap kuralları

| # | Kural | Formül | Not |
|---|---|---|---|
| 1 | Aşırı akım eşiği | I_trip = I_rated × (1 + T_max) | T_max = bileşen toleransı (worst-case) |
| 2 | Aşırı gerilim eşiği | V_ov = V_nom × 1.10 | klap/kapanma |
| 3 | Foldback başlangıcı | I_fb = I_trip × 0.8 | akım önce %80'e düşer, sonra kapanma |
| 4 | DC offset teyit penceresi | ≥ 1 tampon periyodu (512/48k = 10.67 ms) | yanlış tetik önleme |
| 5 | Termal throttling başlangıcı | T_j ≥ 110 °C (kapanma 125 °C) | ara koruma |

**Hesap kuralı:** her formüldeki bilinmeyenler §14.2'deki `⚠️ PLANNED`
hücrelere bağlıdır; bilinmeyen dolmadan eşik SAYISAL olarak yazılmaz
(zero-hallucination — ölçüm/kaynak yoksa yoktur).

## §16 EMI / EMC Kapıları (derinleştirme eki 1)

### §16.1 EMI kaynak ↔ azaltma matrisi

| # | Kaynak | Etkilenen rail | Azaltma | Kapı |
|---|---|---|---|---|
| 1 | Buck anahtar harmoniği | R2/R3 (mantık) | LC filtre + ferrit | §7 ölçüm 1 |
| 2 | Flyback/klap geçişi | R4/R5 (analog) | ayrı kademe + shield | §7 ölçüm 1 |
| 3 | Amplifikatör anahtarlama | R7 (≥36 V) |布局 + LC | §7 ölçüm 8 |
| 4 | Gate sürücü slew | R7 | slew-rate ödünleşimi (§4 satır 6) | §7 ölçüm 8 |
| 5 | Röle bobin tepe | R2 (mantık) | flyback diyodu + RC snubber | §7 ölçüm 6 |

### §16.2 EMC doğrulama kapıları

| Kapı | Kriter | Yöntem | Durum |
|---|---|---|---|
| Conducted emisyon | CISPR/FCC sınıf sınırı altında | EMC masası + LISN | PLANNED |
| Radiated emisyon | sınıf sınırı altında | EMC odası | PLANNED |
| Immunity | temel seviye Dayanıklılık | EMC masası | PLANNED |
| Ses etkisi | pop/noise eşiği altında | ölçüm masası | PLANNED |

> **Dürüstlük:** EMC sınıfları (CISPR B vb.) bu dosyada SEÇİLMEDİ —
> sınıf seçimi proje gereksinimiyle (kullanım ortamı) belirlenir ve
> Q-GYA4'e bağlı PLANNED'dır.

## §17 Termal Kapılar (derinleştirme eki 1)

### §17.1 Termal kaynak ↔ soğutma matrisi

| # | Kaynak | Tipik ısınma | Soğutma yöntemi | Kapı |
|---|---|---|---|---|
| 1 | Buck regülatör | orta | PCB pad + hava akışı | §7 ölçüm 4 |
| 2 | LDO (analog) | düşük | pad yeterli | §7 ölçüm 4 |
| 3 | Gate sürücü | düşük-orta | pad | §7 ölçüm 4 |
| 4 | Güç FET (R7) | YÜKSEK | heatsink + fan | §7 ölçüm 4 |
| 5 | Amplifikatör (TPA3255) | YÜKSEK | heatsink (üretici şartı) | §7 ölçüm 4 |
| 6 | Koruma rölesi | düşük | pad | — |

### §17.2 θJA / θJC hesap kuralları

| # | Kural | Formül | Not |
|---|---|---|---|
| 1 | Cüzda sıcaklık | T_j = T_ambient + P × θJA | worst-case ambient |
| 2 | heatsink'li | T_j = T_ambient + P × (θJC + θCS + θSA) | θCS = termal macun |
| 3 | Throttling | T_j ≥ 110 °C → akım düşürme | §15.1 satır 5 |
| 4 | Kapanma | T_j ≥ 125 °C → rail kapanır | §6.1 katman 4 |

**Hesap kuralı:** θJA/θJC/θSA değerleri çip datasheet'inden okunur
(okunmadıysa `⚠️ PLANNED`); PCB pad tasarımı K001-donanım'ın (§11
kapsam dışı 6) sorumluluğundadır — bu dosya yalnız termal EŞİKLERİ
tarif eder, pad tasarlamaz.

## §18 Açma Sırası — Hata Davranışları (derinleştirme eki 1)

### §18.1 Sequencing hata senaryoları

| # | Senaryo | Algılama | tepki | Sonuç |
|---|---|---|---|---|
| 1 | PG1 gelmez (önregülatör) | sayaç aşımı (K005) | bekleme durdurulur | boot başarısız |
| 2 | PG2 gelmez (mantık) | sayaç aşımı | R3 açılmaz | boot başarısız |
| 3 | PG3 gelmez (analog) | sayaç aşımı | MUTE açılmaz | ses yok |
| 4 | MUTE açılmaz | sayaç aşımı | röle açılmaz | hoparlör bağlantısız |
| 5 | DC > 0.5 V röle öncesi | DC ölçüm | röle açılmaz | L0 kuralı 10 koruması |
| 6 | R7 açılmaz (≥36 V) | PG5 yok | ses yok, koruma sağlam | amplifier koruması |

### §18.2 Hata senaryo ↔ koruma katmanı eşlemesi

| Senaryo | Korunan katman (§6.1) | Firmware sahibi (K005) |
|---|---|---|
| 1–3 | açma sırası (§5) | boot-anayasa §7 bekleme |
| 4 | pop/noise önleme (§5.2 satır 2) | audio-control mute |
| 5 | DC offset koruma (§6.2) | K005 log kaydı |
| 6 | amplifikatör koruması (§6.1 katman 2) | device-control |

**Eşleme kuralı:** her hata senaryosu bir §6.1 koruma katmanına ve bir
K005 firmware sahibine bağlanır; sahipsiz senaryo taslak sayılır ve
§7 ölçüm kapısına eklenir.

## §19 Doğrulama Kapıları Özeti (derinleştirme eki 1)

### §19.1 Kapı ↔ bölüm eşlemesi

| Kapı | Doğrulanan bölüm | Ölçüm | Durum |
|---|---|---|---|
| Kapı 1 (ripple) | §3.2 + §14.1 | osiloskop | PLANNED |
| Kapı 2 (verim) | §2.2 kural 1 | güç analizörü | PLANNED |
| Kapı 3 (transiyan) | §14.2 I_transient | yük programı | PLANNED |
| Kapı 4 (termal) | §17 θJA | IR/termokupl | PLANNED |
| Kapı 5 (DC koruma) | §6.2 + §15 | fonksiyonel test | PLANNED |
| Kapı 6 (açma sırası) | §5.1 + §18.1 | lojik analizör | PLANNED |
| Kapı 7 (ses etkisi) | §5.2 + §18 | ölçüm masası | PLANNED |
| Kapı 8 (EMI) | §16 | EMC masası | PLANNED |

### §19.2 Kapı kapanış koşulları

| Kapı | Kapanış koşulu | Sahip |
|---|---|---|
| 1–3 | gerçek ölçüm + sonuç kaydı | audio-hardware-engineer |
| 4 | termal ölçüm (worst-case ambient) | audio-hardware-engineer |
| 5 | üretim testinde DC tetikleme | imalat-ve-test |
| 6 | lojik analizör kaydı | audio-hardware-engineer |
| 7 | pop/noise ölçümü | audio-hardware-engineer |
| 8 | EMC test raporu | EMC laboratuvarı |

**Kapanış kuralı:** sekiz kapı da PLANNED'dır ve bu ekle kapanmaz —
kapıların kapanışı fiziksel ölçüm gerektirir; ölçüm sonucu bu dosyaya
değil `guc-yonetimi-dogrulama.md`'ye yazılır (iki-dosya kuralı: tarif
burada, ölçüm orada).

## §20 Dürüstlük bloğu (derinleştirme eki 1)

Bu ek (§14-§19) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 ölçüm sonucu, 0 sürüm sayısı, 0 çip akım değeri
eklenmiştir; yalnız rail haritası (§14), koruma eşik çerçevesi (§15),
EMI kapıları (§16), termal kapılar (§17), açma sırası hata senaryoları
(§18) ve doğrulama kapıları özeti (§19) eklenmiştir. §1-§13'teki
mevcut tablolar, kural çerçeveleri ve açık sorular aynen korunur.
§14.2'deki yük bütçesi hücrelerinin tamamı `⚠️ PLANNED` kalır —
PCM3168A ve TPA3255 datasheet akımları okunmadan sayı yazılmaz
(§12.1 notu: PDF'ler OKUNMADI; zero-hallucination). §15.1'deki
formüller kural çerçevesidir, sonuç sayı değildir — bilinmeyenler
dolmadan sonuç üretilmez. §16.2'deki EMC sınıfı SEÇİLMEDİ ve
Q-GYA4'e bağlıdır. Q-GYA1 bu ekle KAPANMAZ. §12.1'deki 13 URL ve
§12.2'deki vault atıfları aynen korunur; bu ek yeni URL eklemez.

### §20.1 Ek ↔ kapı eşlemesi

Bu ek eklediği her bölüme bir §7 ölçüm kapısı bağlar:

| Ek bölüm | Kapı | Kapı durumu |
|---|---|---|
| §14.2 yük bütçesi | Kapı 3 (transiyan) | PLANNED |
| §15 koruma eşikleri | Kapı 5 (DC koruma) | PLANNED |
| §16 EMI/EMC | Kapı 8 (EMI) | PLANNED |
| §17 termal | Kapı 4 (termal) | PLANNED |
| §18 hata senaryoları | Kapı 6 + 7 (sıra + ses) | PLANNED |
| §19 kapı özeti | tümü | PLANNED |

### §20.2 Ek ↔ dış dosya referansları

| Ek bölüm | Dış dosya | İlişki |
|---|---|---|
| §14.1 rail R6 | `K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` | DAC rail hedefi |
| §14.1 rail R7 | `K001-donanım/donanaım-type/audio/cipler/tpa3255-amplifier.md` | Amplifikatör rail hedefi |
| §15 DC offset | `K002-electroncis/devre-ve-analog/*` | sinyal yolu sınırı (Q-GYA2) |
| §17 termal pad | `K001-donanım` (kapsam dışı 6) | PCB yerleşimi sahibi |
| §18 firmware bekleme | `K005-firmware/boot/boot-anayasa.md` §7 | boot bekleme sahibi |

**Referans kuralı:** beş dış referans da yalnız okuma ilişkisidir —
bu dosya dış dosyaya içerik dayatmaz; çift yönlü tutarlılık dış
dosyanın kendi derinleştirme turunda sağlanır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
