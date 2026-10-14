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
validation: "draft — 2026-10-13 yazıldı · ≥500 ölçümü yazım sonrası · 2026-10-14 · K000 kaynaklı taşıma eklendi (bkz §T-K000)"
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

---

## §T-K000 — K000 kaynaklı taşıma (guc-ve-koruma · 2026-10-14)

> **Taşıma kaydı.** Bu bölüm `K000-isletim-sistemi/electroncis/guc-ve-koruma/`
> altındaki üç dosyadan alınan **benzersiz** içeriği taşır (kaynaklar salt-okunur
> kaldı; K000'e bu taşımadan sonra dokunulmaz). Kaynak §/satır atıfları her
> blokta verilmiştir. Sayısal eşiklerin çoğu kaynakta `⚠️ VERIFICATION REQUIRED`
> / `UNKNOWN` işaretliydi ve **aynen korunmuştur** — bu ek ölçülmüş değer üretmez.

### §T-K000.1 — AC→DC ön uç, verimlilik ve koruma mimarisi

Kaynak: `guc-ve-koruma-anayasa.md` §1/§1b/§1c/§1e (L29–63, L178–200), §2/§2b (L64–84), §4/§4b (L109–126).

**AC→DC→Regulator katmanları**

| Katman | İş | Tipik eleman | Örnek |
|---|---|---|---|
| AC giriş | Şebekeden ham AC | priz, sigorta, EMI filtresi | 220 V AC |
| EMC filtresi | Giriş emisyonu bastırma | X/Y kapasitör, common-mode choke | — |
| Transformatör / izolasyon | İndirme + izolasyon | toroidal / PCB trafosu | 230→24 V AC |
| Rectifier | AC→DC | köprü diyot (schottky/FAST) | — |
| Bulk kapasitör | Ripple deposu | elektrolitik | C_bulk |
| Ön regulator (ham DC) | Yüksek→düşük | anahtarlama (buck/boost) | 24→12 V |
| Lineer regulator (temiz DC) | Son regülasyon | LDO / lineer | 12→5 V, 3.3 V |
| Dağıtım | Rail'ler | PCB iz, star-ground | +V, -V, AVDD, DVDD |
| Koruma rölesi | DC offset çıkış kesme | röle + kontrol | >0.5 V kesme |

**AC→DC geçiş gerekçeleri**

| Gerekçe | Açıklama |
|---|---|
| İzolasyon | kullanıcı emniyeti (Class II) |
| Seviye indirme | 220→güvenli DC |
| EMC | giriş emisyonu sınırlama |
| Ripple azaltma | bulk + regüle |
| Rail ayrımı | analog/dijital ayrımı gürültü için |

**AC giriş / EMC ayrıntıları**

| Bileşen | İş | Not |
|---|---|---|
| Sigorta | Aşırı akım | hızlı eriyen |
| X kapasitör | diferansiyel gürültü | L-N |
| Y kapasitör | common-mode gürültü | L/N-GND |
| Common-mode choke | CM bastırma | — |
| MOV | Surge | arrester |
| Köprü diyot | Rectifier | FAST/Schottky |
| Bulk kapasitör | Ripple deposu | elektrolitik |
| Bleeder direnç | Boşaltma | emniyet |

**Rail mimarisi (ses cihazı — örnek, ölülmemiş)**

| Rail | Görev | Tipik regülatör | Hassasiyet |
|---|---|---|---|
| AVDD (analog +) | DAC/amp analog | LDO (temiz) | yüksek gürültü |
| AVSS (analog −) | negatif analog | inverting / −V | yüksek |
| DVDD (dijital) | MCU/logic | anahtarlama | orta |
| VREF | referans | izole / LDO | kritik |
| HP_OUT | kulaklık güç | lineer | yüksek |
| Relay_V | röle bobin | ayrı | orta |

**Rail ayrımı — gürültü mantığı**

| Ayrım | Neden |
|---|---|
| Analog / dijital ayrı rail | dijital gürültü izolasyonu |
| LDO son katman | ripple/EMI azaltma |
| Yıldız (star) toprak | ortak-impedans gürültüsü önleme |
| Ayrı güç izi | yüksek akım ayrımı |
| VREF temiz LDO | referans gürültüsü → SNR |

**Koruma katmanları (özet)**

| Katman | Tehdit | Koruma | Hız |
|---|---|---|---|
| Giriş sigorta | Aşırı akım / kısa devre | sigorta / PTC | anlık |
| EMI filtresi | Gürültü / emisyon | LC / common-mode | sürekli |
| Akım sınırı | Kısa devre / yük | eFuse / limit | hızlı |
| Gerilim sınırı | Aşırı gerilim / tepe | TVS / AKŞ (crowbar) | hızlı |
| Termal koruma | Aşırı ısınma | termal kapanma (OTP) | orta |
| ESD koruma | ESD deşarj | TVS diodo / ESD array | çok hızlı |
| DC offset rölesi | Hoparlör hasarı | DC algılama + röle | yavaş (ms) |
| Yumuşak başlat (soft-start) | İnrush akımı | rampa kontrol | açılış |

**Verimlilik — topoloji karşılaştırması**

| Topoloji | Tipik verim | Gürültü | Kullanım |
|---|---|---|---|
| Lineer (LDO) | düşük-orta (%30–70) | çok düşük | temiz rail |
| Buck | yüksek (%80–95) | orta | ön regulator |
| Boost | yüksek | orta | yüksek rail |
| Flyback | orta-yüksek | yüksek | izole |
| Charge-pump | orta | düşük-orta | küçük yük |

**Verimlilik–gürültü trade-off**

| Seçim | Kazanım | Kayıp |
|---|---|---|
| Anahtarlama ön-regülatör | verim | ripple/EMI |
| LDO son-regülatör | temiz | ısınma |
| Daha yüksek anahtarlama frekansı | küçük L/C | EMI |
| Daha düşük anahtarlama frekansı | düşük EMI | büyük L/C |

### §T-K000.2 — Soft-start (inrush) sırası A–G

Kaynak: `guc-ve-koruma-anayasa.md` §3/§3b (L86–107), §3c (L234–242), §3d (L307–315), §3e (L428–435).

| Adım | Olay | Eşik/durum | Sonuç |
|---|---|---|---|
| A | Güç gelir | VIN hazır | inrush sınır |
| B | Bulk doldu | bulk ≥ eşik | DC rail hazır |
| C | Enable | soft-start rampa | rail yukarı |
| D | Rail stabil | DC within tol | — |
| E | DC algılama hazır | offset <0.5 V | röle KAPALI |
| F | Röle kapat | hoparlör bağlı | ses çıkışı |
| G | Anormallik | offset/akım/ısı | röle AÇIK |

**Sıralama (inrush / soft-start)**

| Sıralama | Amaç | Yöntem |
|---|---|---|
| 1 | Girişi sınırla | inrush NTC / pre-charge |
| 2 | Bulk'ı yavaş doldur | soft-start / pre-charge direnç |
| 3 | Rail'leri kademeli aç | sıralı enable |
| 4 | Koruma eşiklerini hazırlama | comparator ready |
| 5 | Röleyi geç bağla | DC stabil → röle |
| 6 | Yük aç | rampa / step |

**İnrush / soft-start mantığı**

| Kavram | Açıklama | Eşik |
|---|---|---|
| İnrush | Açılış ani akımı | sınırlı |
| Pre-charge | Bulk'ı yavaş doldur | direnç/NTC |
| Soft-start rampa | Rail yukarı yavaş | süre |
| Kademeli enable | Rail sırası | sıralı |
| Röle gecikme | Sonra bağla | stabil sonrası |

**Soft-start süre / akım mantığı**

| Parametre | Etki | Not |
|---|---|---|
| Soft-start süresi | İnrush ↓ | yavaş açılış |
| Pre-charge direnç | Bulk yavaş | güç kaybı |
| NTC soğuk direnç | yüksek soğuk | ısınca ↓ |
| Enable gecikme | Sıra | rail seq |
| Röle gecikme | DC stabil sonra | güvenli |

**Sıralama-senaryo tablosu**

| Senaryo | Sıra sonucu |
|---|---|
| Normal açılış | soft-start → röle |
| Ani güç kesme | röle aç → boşaltma |
| Yeniden başlat | yeniden soft-start |
| Anomali | koruma → röle aç |

### §T-K000.3 — Duty / ripple formülleri

Kaynak: `guc-kaynaklari-ve-regulatorler.md` §2b/§2c/§2d (L59–87), §3/§3b (L89–106), §3c (L198–205), §2f (L271–278), §3e (L297–304), §3f (L383–390), §3g (L435–441).

**Buck topolojisi**

| Öğe | Değer/Not | Durum |
|---|---|---|
| Anahtar MOSFET | Rds(on) | `⚠️ VERIFICATION REQUIRED` |
| İndüktör L | DCR, satürasyon | `⚠️ VERIFICATION REQUIRED` |
| Çıkış kapasitör | ESR, ripple | `⚠️ VERIFICATION REQUIRED` |
| Frekans fs | PWM | `⚠️ VERIFICATION REQUIRED` |
| Duty D | VOUT/VIN | hesap |
| Ripple ΔIL | (VIN−VOUT)·D/(L·fs) | hesap |

**Boost topolojisi**

| Öğe | Not | Durum |
|---|---|---|
| İndüktör | akım satürasyon | `⚠️ VERIFICATION REQUIRED` |
| Diyot | Schottky | `⚠️ VERIFICATION REQUIRED` |
| Duty | VOUT/(VIN·η) | hesap |
| Giriş akımı | yüksek (yükseltme) | — |
| Ripple | çıkış kapasitör | `⚠️ VERIFICATION REQUIRED` |

**Buck-boost topolojisi**

| Öğe | Not | Durum |
|---|---|---|
| Kapama | 4-anahtar / inverting | `⚠️ VERIFICATION REQUIRED` |
| Duty | VOUT/(VIN+VOUT) | hesap |
| Negatif çıkış | inverting | — |
| Kullanım | rail esnekliği | — |

**Ripple**

| Kaynak | Belirti | Azaltma |
|---|---|---|
| Anahtarlama | fs frekansı | LC filtre |
| İndüktör ΔIL | üçgen | L↑ |
| Kapasitör ESR | Vripple=ΔI·ESR | düşük ESR |
| LDO | PSRR | LDO son katman |

**Ripple-azaltma yöntemleri**

| Yöntem | Etki | Eksi |
|---|---|---|
| LC filtre | fs bastırma | alan, maliyet |
| Düşük-ESR kapasitör | ripple ↓ | maliyet |
| LDO son katman | ripple ↓↓ | ısı |
| Daha yüksek fs | L/C ↓ | EMI |
| Post-regülasyon | temiz | verim |

**Ripple-hesap mantığı**

| Büyüklük | İlişki | Etki |
|---|---|---|
| ΔIL | (VIN−VOUT)·D/(L·fs) | L↑ → ΔIL↓ |
| Vripple | ΔI·ESR | ESR↓ → ripple↓ |
| Vripple(C) | ΔI/(8·fs·C) | C↑ → ripple↓ |
| Toplam ripple | ikisi | min |

**LC-filter ripple-azaltma**

| Parametre | Etki |
|---|---|
| L (ön) | fs bastırma ↑ |
| C (ön) | fs bastırma ↑ |
| fs | L/C ↓ |
| Sönüm | ρ kontrol |

**Buck-duty mantığı**

| Büyüklük | İlişki |
|---|---|
| D | VOUT/(VIN·η) |
| 1−D | kesme oranı |
| Minimum D | yüksek VIN |
| Satürasyon | min off-time |

**Kapasitör-tipi ripple ilişkisi**

| Tip | ESR | Kullanım |
|---|---|---|
| Elektrolitik | yüksek | bulk |
| Seramik (MLCC) | çok düşük | ripple |
| Tantal | düşük-orta | bulk-temiz |
| Polimer | düşük | çıkış |

**Ripple-sönüm ilişkisi**

| Faktör | Etki |
|---|---|
| ρ (ESR/L) | sönüm |
| ρ<0.5 | under-damped |
| ρ>0.5 | over-damped |
| ρ≈0.5 | critical |

**Ripple-toplam not**

| Bileşen | Toplam |
|---|---|
| Kapasitif | ripple(C) |
| ESR | ripple(ESR) |
| LDO-red | PSRR |

### §T-K000.4 — LDO vs Buck seçimi ve parametreleri

Kaynak: `guc-kaynaklari-ve-regulatorler.md` §1/§1b (L27–46), §1c (L168–176), §1d (L178–185), §2e (L187–196), §1f (L361–369), §1g (L425–433), §2h (L372–381), §2i (L416–423).

**Lineer (LDO) regülatör**

| Özellik | Artı | Eksi |
|---|---|---|
| Gürültü | çok düşük | — |
| PSRR | yüksek (düşük fs) | yüksek fs'de düşer |
| Karmaşıklık | basit | — |
| Verim | — | (VOUT/VIN) |
| Isı | — | (VIN−VOUT)·I |
| Yanıt | hızlı | — |

**LDO seçim kriterleri**

| Kriter | Not | Durum |
|---|---|---|
| Giriş-çıkış farkı (dropout) | VIN−VOUT ≥ dropout | `⚠️ VERIFICATION REQUIRED` |
| Çıkış gürültüsü | µVrms | `⚠️ VERIFICATION REQUIRED` |
| PSRR | dB @ fs | `⚠️ VERIFICATION REQUIRED` |
| Akım kapasitesi | yük | `⚠️ VERIFICATION REQUIRED` |
| Termal (θJA) | °C/W | `⚠️ VERIFICATION REQUIRED` |

**LDO vs Buck karar matrisi**

| Durum | Seçim | Gerekçe |
|---|---|---|
| VIN≈VOUT, düşük akım | LDO | verim sorun değil, temiz |
| VIN≫VOUT, yüksek akım | Buck | LDO çok ısı |
| Temiz analog rail | Buck ön + LDO son | verim + temiz |
| Çok düşük gürültü | LDO | gürültü |
| Yüksek verim zorunlu | Buck | verim |

**LDO PSRR–frekans ilişkisi**

| Frekans bölgesi | PSRR | Not |
|---|---|---|
| DC / düşük | yüksek | — |
| fs (anahtarlama) | düşer | LDO son katman |
| Yüksek fs | düşük | LC ön filtre |
| Çok yüksek | — | ölçüm gerekli |

**Topoloji karşılaştırma tablosu**

| Topoloji | Verim | Gürültü | Karmaşıklık | İzolasyon |
|---|---|---|---|---|
| LDO | düşük | çok düşük | basit | yok |
| Buck | yüksek | orta | orta | yok |
| Boost | yüksek | orta | orta | yok |
| Buck-boost | yüksek | orta | orta | yok |
| Flyback | orta | yüksek | karmaşık | var |
| Charge-pump | orta | düşük-orta | basit | yok |

**LDO parametre özet**

| Parametre | Not |
|---|---|
| VIN max | limit |
| VOUT | ayar |
| Iout max | yük |
| Dropout | VIN−VOUT |
| Gürültü | µVrms |
| PSRR | dB |

**LDO bağlantı notu**

| Pin | Bağlantı |
|---|---|
| IN | ham DC |
| OUT | temiz rail |
| GND | star-ground |
| EN (varsa) | sequencing |
| NR (varsa) | gürültü ↓ |

**Buck parametre özet**

| Parametre | Not |
|---|---|
| VIN range | giriş |
| VOUT | çıkış |
| Iout | yük |
| fs | frekans |
| L | indüktör |
| Cout | kapasitör |

**Topoloji-başına kondansatör/indüktör notu**

| Topoloji | L | C |
|---|---|---|
| Buck | giriş-çıkış | çıkış |
| Boost | giriş | çıkış |
| Buck-boost | — | çıkış |
| Flyback | transformer | çıkış |

**Flyback / SEPIC — kısa not**

| Topoloji | İzolasyon | Kullanım | Zorluk |
|---|---|---|---|
| Flyback | var | izole güç | tasarımı karmaşık |
| SEPIC | yok | VIN esnek | kapasitör |
| Ćuk | yok | negatif | karmaşık |

### §T-K000.5 — EMI, ölçüm ve termal derating

Kaynak: `guc-kaynaklari-ve-regulatorler.md` §4/§4b (L108–126), §4c (L216–223), §4d (L306–312), §5/§5b (L128–145), §5c (L225–233), §5d (L235–242), §5e (L314–321), §5f (L400–406), §5g (L450–456), §1e (L288–295), §6/§6b (L147–166), §6c (L244–251), §6d (L253–260), §6e (L262–269), §6f (L323–331), §6g (L343–349), §6h (L351–359), §6i (L408–414), §6j (L466–472).

**EMI (anahtarlama kaynaklı)**

| EMI kaynağı | Azaltma |
|---|---|
| Yüksek dV/dt | snubber, gate |
| Yüksek dI/dt | indüktör, layout |
| Anahtarlama düğüm | min alan |
| Loop alanı | küçük akım döngüsü |
| Filtre | LC / common-mode |

**EMI-önleme PCB/layout**

| Kural | Neden |
|---|---|
| Küçük güç döngüsü | EMI ↓ |
| Toprak düzlemi | return yolu |
| Anahtarlama-node min | emisyon |
| Giriş kapasitörü yakın | ripple |
| Separasyon analog/dijital | gürültü |

**EMI-snubber / gate**

| Yöntem | Etki | Eksi |
|---|---|---|
| RC snubber | dV/dt ↓ | kayıp |
| Gate direnç | geçiş yavaş | kayıp |
| Soft-switch | EMI ↓ | karmaşık |

**Termal (buck/LDO kayıp → sıcaklık)**

| Konu | Hesap | Not |
|---|---|---|
| LDO kaybı | (VIN−VOUT)·I | ısı |
| Buck kaybı | (1−η)·P | η |
| θJA | °C/W | paket |
| ΔT | P·θJA | sıcaklık |
| Maks P | (Tjmax−Ta)/θJA | limit |

**Termal-derating**

| Faktör | Etki |
|---|---|
| Yüksek Ta | ΔT başlangıç ↓ |
| θJA (hava) | soğutma |
| Soğutucu | θJA ↓ |
| Akım derating | sıcakta akım ↓ |

**Buck termal-hesap mantığı**

| Büyüklük | İlişki |
|---|---|
| Kayıp | (1−η)·Pout |
| ΔT | P·θJA |
| Tj | Ta+ΔT |
| Maks P | (Tjmax−Ta)/θJA |
| Derating | sıcakta ↓ |

**θJA faktörleri**

| Faktör | θJA etkisi |
|---|---|
| Paket tipi | θJA |
| PCB bakır alan | θJA ↓ |
| Soğutucu | θJA ↓↓ |
| Hava akımı | θJA ↓ |

**Termal-rölanti (Buck vs LDO)**

| VIN-VOUT | Akım | Seçim |
|---|---|---|
| küçük | düşük | LDO |
| küçük | yüksek | LDO (kontrol) |
| büyük | düşük | LDO |
| büyük | yüksek | Buck |

**LDO termal-örnek mantığı**

| Büyüklük | İlişki |
|---|---|
| P (LDO) | (VIN−VOUT)·Iout |
| ΔT | P·θJA |
| Tj | Ta+ΔT |
| Limit | Tjmax |

**Termal-ölçüm (gerçek)**

| Alet | Ölçüm |
|---|---|
| Termokupl | Tj yaklaşma |
| Termal kamera | yüzey |
| DMM (temp) | — |

**Termal-ölçüm kabul**

| Parametre | Kabul |
|---|---|
| Tj | ≤ Tjmax |
| ΔT | ≤ limit |
| Derating | uygulanır |

**Ölçüm (regülatör)**

| Ölçüm | Alet | Kabul |
|---|---|---|
| Çıkış DC | DMM | ±%5 |
| Ripple | Osc AC | ≤ hedef |
| Verim | VIN·IIN vs VOUT·IOUT | η |
| PSRR | ripple inj + DMM | dB |
| Termal | TC/termal kamera | ΔT |
| Yük geçişi | E-load + Osc | overshoot |

**Ölçüm-öncesi hazırlık**

| Adım | İş |
|---|---|
| 1 | Bilinen yük bağla |
| 2 | VIN ayarla |
| 3 | Ripple AC ölç |
| 4 | Verim hesapla |
| 5 | Termal izle |

**Ripple-ölçüm yöntemi**

| Adım | Alet | Not |
|---|---|---|
| 1 | Osc AC-koplu | ripple |
| 2 | Probe yakın | gerçek |
| 3 | BW limit | gürültü |
| 4 | Ripple ölç | mV |

**PSRR-ölçüm yöntemi**

| Adım | İş |
|---|---|
| 1 | Girişe ripple enjekte |
| 2 | Çıkış ripple ölç |
| 3 | PSRR = 20log(Vin_ac/Vout_ac) |
| 4 | fs'de tekrarla |

**Yük-geçişi ölçüm**

| Adım | Alet | Kabul |
|---|---|---|
| 1 | E-load step | — |
| 2 | Osc tekilleme | overshoot |
| 3 | Settle süre | — |
| 4 | Karşılaştırma | sim |

**Verim-ölçüm tablosu**

| Girdi | Ölçüm |
|---|---|
| VIN | DMM |
| IIN | DMM (seri) |
| VOUT | DMM |
| IOUT | DMM / yük |
| η | POUT/PIN |

**Ripple-hedef (ölçülmemiş)**

| Rail | Ripple hedef | Durum |
|---|---|---|
| AVDD | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| DVDD | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| HP_OUT | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |

**Ölçüm-kabul matrisi (regülatör)**

| Parametre | Kabul | Durum |
|---|---|---|
| Çıkış DC | ±%5 | `⚠️ VERIFICATION REQUIRED` |
| Ripple | ≤ hedef | `⚠️ VERIFICATION REQUIRED` |
| Verim η | hedef | `⚠️ VERIFICATION REQUIRED` |
| PSRR @fs | hedef | `⚠️ VERIFICATION REQUIRED` |
| ΔT | ≤ limit | `⚠️ VERIFICATION REQUIRED` |

**Verim-ölçüm-sınırı**

| Sınır | Durum |
|---|---|
| Verim hedefi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Ölçüm tekrarı | — |
| η-ölçüm kalibrasyon | — |

**Ölçüm-sıra özeti**

| Sıra | Ölçüm |
|---|---|
| 1 | DC |
| 2 | Ripple |
| 3 | Verim |

### §T-K000.6 — Aşırı akım koruma: eFuse / sigorta / PTC / I²t

Kaynak: `koruma-devreleri-ve-esikler.md` §1/§1b (L27–45), §1c (L129–136), §1d (L138–146), §1e (L148–156), §1f (L249–257), §1g (L276–283), §1h (L285–291), §1i (L369–374), §1j (L410–418), §1k (L468–473).

**Aşırı akım koruma**

| Yöntem | Çalışma | Hız | Eksi |
|---|---|---|---|
| Sigorta (eriyen) | akım → erir | orta | tek-use |
| PTC (resettable) | ısın → direnç ↑ | yavaş | soğuma |
| eFuse | elektronik limit | hızlı | maliyet |
| Akım-sınırlı regülatör | limit | hızlı | ısı |
| Hot-swap kontrolörü | sıralı takma | hızlı | karmaşık |

**EFuse / akım-limit parametreleri**

| Parametre | Not | Durum |
|---|---|---|
| Akım limit | kesme eşiği | `⚠️ VERIFICATION REQUIRED` |
| Vblank (UVLO) | düşük-gerilim kapanma | `⚠️ VERIFICATION REQUIRED` |
| Slew (soft-start) | rampa | `⚠️ VERIFICATION REQUIRED` |
| Fault bayrağı | hata çıktısı | `⚠️ VERIFICATION REQUIRED` |
| Termal | dahili OTP | `⚠️ VERIFICATION REQUIRED` |

**Aşırı-akım senaryo matrisi**

| Senaryo | Algılama | Aksiyon | Hız |
|---|---|---|---|
| Çıkış kısa devre | akım ↑ | eFuse/sigorta | hızlı |
| Yük aşımı | akım > limit | limit | hızlı |
| İç arıza | akım ↑ | kesme | hızlı |
| Yumuşak kısa (ısınma) | ısınma | PTC/OTP | yavaş |

**EFuse vs sigorta karşılaştırma**

| Kriter | eFuse | Sigorta |
|---|---|---|
| Tekrar | evet | hayır (eriyen) |
| Hız | hızlı | orta |
| Precision | yüksek | düşük |
| Maliyet | yüksek | düşük |
| Fault sinyali | var | yok |

**Hot-swap sırası**

| Adım | Olay |
|---|---|
| 1 | Takma (hot) |
| 2 | Pre-charge (sınır) |
| 3 | Rail stabil |
| 4 | Tam bağlantı |
| 5 | Fault izleme |

**Eşik-tipi koruma özeti**

| Koruma | Eşik | Hız |
|---|---|---|
| Akım | limit | hızlı |
| Gerilim | Vrwm | hızlı |
| Isı | OTP | orta |
| ESD | ±kV | çok hızlı |
| DC offset | 0.5 V | ms |

**Sigorta / PTC parametre**

| Parametre | Sigorta | PTC |
|---|---|---|
| Akım (Ih) | erime | tutma |
| Vmax | limit | limit |
| Reset | hayır | evet |
| Soğuma | — | zaman |

**Akım-limit hesap mantığı**

| Büyüklük | İlişki | Durum |
|---|---|---|
| Limit | Iload×margin | `⚠️ VERIFICATION REQUIRED` |
| Trip süre | akım²×t | `⚠️ VERIFICATION REQUIRED` |
| let-through | I²t | `⚠️ VERIFICATION REQUIRED` |

**Eşik-akım-güvenilirlik**

| Faktör | Etki |
|---|---|
| let-through (I²t) | enerji |
| Trip süre | koruma |
| Soğuma | yeniden |

**Koruma-hız-zinciri**

| Koruma | Hız |
|---|---|
| ESD | çok hızlı |
| Akım | hızlı |
| Gerilim | hızlı |
| Isı | orta |
| DC röle | ms |

**Akım-eşik-özeti**

| Eşik | Durum |
|---|---|
| Limit | `⚠️ VERIFICATION REQUIRED` |
| let-through | `⚠️ VERIFICATION REQUIRED` |

### §T-K000.7 — Aşırı gerilim: TVS / crowbar (AKŞ)

Kaynak: `koruma-devreleri-ve-esikler.md` §2/§2b (L47–64), §2c (L158–166), §2d (L168–175), §2e (L177–184), §2f (L259–266), §2g (L377–383), §2h (L428–434).

**Aşırı gerilim / AKŞ (crowbar)**

| Eleman | İş | Not |
|---|---|---|
| TVS | tepe-bastırma | hızlı, tekrarlı |
| Zener | eşik | hassasiyet |
| SCR (crowbar) | kısa-devre kesme | AKŞ |
| MOV | surge enerji | arrester |
| Sensör (comparator) | eşik algılama | hız |

**AKŞ (crowbar) çalışma sırası**

| Adım | Olay | Sonuç |
|---|---|---|
| 1 | Gerilim > eşik | sensör tetik |
| 2 | SCR tetik | kısa-devre |
| 3 | Sigorta akım | sigorta erir |
| 4 | Güç kesil | yük korunur |

**TVS-parametre matrisi**

| Parametre | Not | Durum |
|---|---|---|
| Vrwm | çalışma | `⚠️ VERIFICATION REQUIRED` |
| Vbr | kırılma | `⚠️ VERIFICATION REQUIRED` |
| Vc | klamlama | `⚠️ VERIFICATION REQUIRED` |
| Ipp | tepe akım | `⚠️ VERIFICATION REQUIRED` |
| Kapasitör | sinyal | `⚠️ VERIFICATION REQUIRED` |

**Crowbar vs TVS karşılaştırma**

| Kriter | TVS | Crowbar (AKŞ) |
|---|---|---|
| Enerji | düşük-tepe | yüksek |
| Tekrar | evet | sigorta |
| Hız | çok hızlı | hızlı |
| Karmaşıklık | basit | orta |

**Eşik-sapma (comparator) matrisi**

| Parametre | Not | Durum |
|---|---|---|
| Offset | sapma | `⚠️ VERIFICATION REQUIRED` |
| Hysteresis | yanlış tetik | `⚠️ VERIFICATION REQUIRED` |
| Hız | prop. gecikme | `⚠️ VERIFICATION REQUIRED` |
| Referans | eşik | `⚠️ VERIFICATION REQUIRED` |

**AKŞ-gerilim-eşik mantığı**

| Büyüklük | İlişki |
|---|---|
| Eşik (V) | rail + margin |
| TVS Vrwm | ≥ rail |
| Vc | ≤ yük limit |
| Tolerans | uç |

**Gerilim-eşik-güvenilirlik**

| Faktör | Etki |
|---|---|
| Tolerans | sapma |
| Sıcaklık | drift |
| Vc (klamlama) | koruma |

**AKŞ-çalışma-parametre**

| Parametre | Not |
|---|---|
| SCR akım | Ift |
| Trigger | eşik |
| Sigorta | erime |

### §T-K000.8 — Tolerans bütçesi ve worst-case

Kaynak: `koruma-devreleri-ve-esikler.md` §5/§5b (L101–119), §5c (L121–127), §5d (L222–229), §5e (L231–239), §5f (L241–247), §5g (L330–337), §5h (L339–346), §5i (L348–357), §5j (L401–408), §5k (L444–450), §5l (L460–466).

**Eşik / tolerans hesabı**

| Eşik | Formül / Mantık | Durum |
|---|---|---|
| DC offset röle | \|DC\| > 0.5 V | kural |
| Akım limit | limit = yük×margin | `⚠️ VERIFICATION REQUIRED` |
| Gerilim sınır | Vrwm ≥ rail | `⚠️ VERIFICATION REQUIRED` |
| OTP | Tjmax − margin | `⚠️ VERIFICATION REQUIRED` |
| Tolerans | ±tol × nominal | `⚠️ VERIFICATION REQUIRED` |

**Tolerans / worst-case**

| Kalem | Tolerans | Worst-case |
|---|---|---|
| Direnç | ±%1–5 | uç |
| Kapasitör | ±%10–20 | uç |
| Referans | ±%1 | uç |
| Eşik sapması | offset | uç |
| Sıcaklık | drift | uç |

**Eşik-güvenilirlik (hysteresis / debounce)**

| Teknik | Neden |
|---|---|
| Hysteresis | yanlış tetikleme önleme |
| Debounce (zaman) | gürültü-bloklama |
| Yeniden-açılma gecikme | kararlılık |

**Eşik-hesap örnek matrisi (kavramsal, ölülmemiş)**

| Eşik | Nominal | Margin | Worst-case |
|---|---|---|---|
| DC offset | 0.5 V | — | kural |
| Akım | UNKNOWN | ×1.5 | `⚠️ VERIFICATION REQUIRED` |
| Gerilim | rail | +margin | `⚠️ VERIFICATION REQUIRED` |
| OTP | Tjmax | −margin | `⚠️ VERIFICATION REQUIRED` |

**Tolerans-zincir (worst-case toplam)**

| Kalem | Sapma | Toplam |
|---|---|---|
| Direnç | ±%x | — |
| Kapasitör | ±%y | — |
| Referans | ±%z | — |
| Sıcaklık | drift | — |
| Toplam | — | worst-case |

**Eşik-güvenilirlik — hysteresis örnek**

| Durum | Tetik | Sonuç |
|---|---|---|
| Yükselen | > üst | kes |
| Düşen | < alt | geri |
| Arada | — | bekle |

**Tolerans-kabul matrisi**

| Kalem | Kabul | Durum |
|---|---|---|
| Eşik sapması | ±tol | `⚠️ VERIFICATION REQUIRED` |
| Sıcaklık drift | ±ppm/°C | `⚠️ VERIFICATION REQUIRED` |
| Zaman-drift | — | `⚠️ VERIFICATION REQUIRED` |
| Worst-case | toplam | `⚠️ VERIFICATION REQUIRED` |

**DC-offset eşik-güvenilirlik**

| Faktör | Etki | Not |
|---|---|---|
| Ölçüm penceresi | tespit | blok/ornek |
| Eşik (0.5 V) | kural | — |
| Hysteresis | yanlış alarm | — |
| Gecikme | hoparlör koruma | ms |

**Eşik-gereksinim matrisi (ölçülmemiş)**

| Koruma | Eşik | Durum |
|---|---|---|
| Akım | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| Gerilim | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| Isı | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| ESD | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| Surge | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| DC offset | 0.5 V | kural |

**Tolerans-worst-case-özet**

| Kalem | Uç-değer |
|---|---|
| Direnç | ±%x |
| Kapasitör | ±%y |
| Sıcaklık | drift |
| Toplam | worst |

**Eşik-tolerans-özet**

| Eşik | Tolerans |
|---|---|
| Akım | `⚠️ VERIFICATION REQUIRED` |
| Gerilim | `⚠️ VERIFICATION REQUIRED` |
| Isı | `⚠️ VERIFICATION REQUIRED` |

**Eşik-domain özeti**

| Domain | Eşik | Durum |
|---|---|---|
| Akım | limit | `⚠️ VERIFICATION REQUIRED` |
| Gerilim | Vrwm | `⚠️ VERIFICATION REQUIRED` |
| Isı | Tjmax | `⚠️ VERIFICATION REQUIRED` |

### §T-K000.9 — OTP, ESD, surge/EFT ve IEEE/IEC atıf matrisleri

Kaynak: `koruma-devreleri-ve-esikler.md` §3/§3b (L66–82), §3c (L186–193), §3d (L195–202), §3e (L293–300), §3f (L302–310), §3g (L385–391), §3h (L436–442), §4/§4b (L84–99), §4c (L204–211), §4d (L213–220), §4e (L312–319), §4f (L321–328), §4g (L393–399), §7b (L268–274); `guc-ve-koruma-anayasa.md` §2b (L77–84), §2f (L286–294), §2g (L297–305).

**Termal kapanma (OTP)**

| Yöntem | Sensör | Eşik |
|---|---|---|
| Dahili OTP | die sıcaklık | Tjmax |
| Termistör (NTC/PTC) | dış sıcaklık | ayar |
| Termal röle | mekanik | — |
| Firmware OTP | K005 | yazılım |

**Termal-eşik mantığı**

| Büyüklük | İlişki | Durum |
|---|---|---|
| Tjmax | paket limit | `⚠️ VERIFICATION REQUIRED` |
| OTP eşiği | Tjmax − margin | `⚠️ VERIFICATION REQUIRED` |
| Hysteresis | yeniden-açılma | `⚠️ VERIFICATION REQUIRED` |
| θJA | °C/W | `⚠️ VERIFICATION REQUIRED` |

**Termal-eşik-karşılaştırma**

| Yöntem | Tolerans | Hız | Durum |
|---|---|---|---|
| Dahili OTP | orta | orta | `⚠️ VERIFICATION REQUIRED` |
| NTC + comparator | ayarlanır | yavaş-orta | `⚠️ VERIFICATION REQUIRED` |
| Termal röle | düşük | yavaş | — |
| Firmware OTP | — | yavaş | K005 |

**Termistör-eşik mantığı**

| Büyüklük | İlişki |
|---|---|
| R(T) | Steinhart-Hart |
| β | sabit |
| Eşik R | karşılık sıcaklık |
| Hysteresis | yeniden-açılma |

**Termal-monitor (TMP461 benzeri) mantığı**

| Parametre | Not | Durum |
|---|---|---|
| Uzak diode | die/NTC | `⚠️ VERIFICATION REQUIRED` |
| Alert | uyarı | `⚠️ VERIFICATION REQUIRED` |
| T_HYST | histeresiz | `⚠️ VERIFICATION REQUIRED` |
| Ölçüm hassasiyet | ±°C | `⚠️ VERIFICATION REQUIRED` |

**Termal-eşik-sıra**

| Adım | Olay |
|---|---|
| 1 | Sıcaklık ölç |
| 2 | > OTP eşiği kontrol |
| 3 | Hysteresis bekle |
| 4 | Kapanma |
| 5 | Soğuma → reopen |

**Termal-güvenilirlik**

| Faktör | Etki |
|---|---|
| Hysteresis | kararlılık |
| θJA | ΔT |
| Yerleşim | ölçüm |

**OTP-kabul**

| Parametre | Kabul |
|---|---|
| Tj | ≤ Tjmax |
| Hysteresis | uygulanır |
| Alert | var |

**ESD koruma**

| Seviye (HBM/kategori) | Deşarj | Koruma |
|---|---|---|
| İnsan-modeli | ±kV | TVS/array |
| Machines | ±V | TVS |
| IEC 61000-4-2 | ±kV | ESD array |

**ESD-parametre matrisi**

| Parametre | Not | Durum |
|---|---|---|
| Eşik (±kV) | test seviyesi | `⚠️ VERIFICATION REQUIRED` |
| TVS kapasitör | düşük-kap (sinyal) | `⚠️ VERIFICATION REQUIRED` |
| Düzlem (layout) | akım yolu | `⚠️ VERIFICATION REQUIRED` |
| Topraklama | kısa yol | `⚠️ VERIFICATION REQUIRED` |

**ESD-test-kategori matrisi**

| Kategori | Seviye | Durum |
|---|---|---|
| HBM (insan) | ±kV | `⚠️ VERIFICATION REQUIRED` |
| Machine model | ±V | `⚠️ VERIFICATION REQUIRED` |
| IEC 61000-4-2 | ±kV | atıf `⚠️ VERIFICATION REQUIRED` |
| Charged-device | ±V | `⚠️ VERIFICATION REQUIRED` |

**ESD-koruma-yerleşimi**

| Kural | Neden |
|---|---|
| Giriş yakını | akım yolu kısa |
| Düzlem-entegre | akım toprağa |
| Düşük-kap TVS | sinyal bozulmaz |
| Paralel bağlama | deşarj yolu |

**ESD-akım-yolu matrisi**

| Yol | Etki |
|---|---|
| Giriş → TVS → GND | deşarj |
| Düzlem akım | kısa |
| Toprak düzlemi | dağılma |
| İzole (opto) | ek |

**ESD-koruma-eleman karşılaştırma**

| Eleman | Kapasitör | Hız | Kullanım |
|---|---|---|---|
| TVS array | düşük | çok hızlı | sinyal |
| Tek TVS | orta | hızlı | güç |
| Varistor (MOV) | yüksek | yavaş | surge |
| Gas deşarj | yüksek | yavaş | yüksek-enerji |

**ESD-güvenilirlik**

| Faktör | Etki |
|---|---|
| Kapasitör | sinyal |
| Akım yolu | deşarj |
| Toprak | dağılma |

**ESD/surge — giriş-arayüz ayrıntı matrisi (kaynak: guc-ve-koruma-anayasa §2b/§2f/§2g)**

| Olay | Standart atfı | Koruma elemanı | Durum |
|---|---|---|---|
| ESD deşarj | IEEE 61000-4-2 | TVS / ESD array | atıf `⚠️ VERIFICATION REQUIRED` |
| EFT / surge | IEC (yayın 2724) | MOV / TVS / filtre | atıf `⚠️ VERIFICATION REQUIRED` |
| Dalgıç (surge) | IEEE 1547 (ilgili) | arrester / TVS | atıf `⚠️ VERIFICATION REQUIRED` |
| EMI / iletken gürültü | — | LC filtre | — |

**ESD/TVS koruma — ayrıntı matrisi**

| Konu | Değer/Not | Durum |
|---|---|---|
| ESD kategorisi | insan/model (HBM) / machines | `⚠️ VERIFICATION REQUIRED` |
| TVS çalışma gerilimi | Vrwm rail'e uygun | tasarım |
| TVS kapasitör | düşük-kap (sinyal) | tasarım |
| Düzlem / sıralı yerleşim | ESD akım yolu | PCB |
| Topraklama | kısa ESD yolu | PCB |
| Test standardı | IEEE 61000-4-2 | atıf `⚠️ VERIFICATION REQUIRED` |

**Surge/EFT — filtre ayrıntı**

| Konu | Not | Durum |
|---|---|---|
| Surge enerjisi | MOV boyutu | `⚠️ VERIFICATION REQUIRED` |
| Common-mode | Y-kap + choke | tasarım |
| Diferansiyel | X-kap | tasarım |
| Filtre sonrası | TVS son katman | tasarım |
| Test | IEC (yayın 2724) | atıf `⚠️ VERIFICATION REQUIRED` |

**IEEE/IEC-atıf matrisi (devam)**

| Standart | Konu | Durum |
|---|---|---|
| IEEE 61000-4-2 | ESD | atıf `⚠️ VERIFICATION REQUIRED` |
| IEEE 1547 | dağıtım-arayüz | atıf `⚠️ VERIFICATION REQUIRED` |
| IEC 2724 | (yayın) | atıf `⚠️ VERIFICATION REQUIRED` |

### §T-K000.10 — Koruma karar matrisleri (hız · senaryo · eşik)

Kaynak: `guc-ve-koruma-anayasa.md` §2c (L201–211), §2d (L213–222), §2e (L224–232), §2h (L359–368), §2i (L390–398), §2j (L446–454), §4c (L244–252), §4d (L254–262), §4e (L317–325), §4f (L419–426), §1g (L349–357), §1h (L370–379), §1i (L381–388), §1j (L456–463).

**Koruma elemanı seçim matrisi**

| Koruma | Eleman | Hız | Eşik | Durum |
|---|---|---|---|---|
| Kısa devre | eFuse / sigorta | hızlı | akım limit | — |
| Aşırı gerilim | TVS | hızlı | Vrwm | — |
| Crowbar (AKŞ) | SCR + TVS | hızlı | aşırı V | — |
| Aşırı ısınma | termistör + OTP | orta | sıcaklık | — |
| ESD | ESD array / TVS | çok hızlı | ±kV | IEEE 61000-4-2 |
| İnrush | NTC / pre-charge | yavaş | akım | soft-start |
| DC offset | algılama + röle | ms | 0.5 V | kural |

**Koruma senaryoları (ne zaman kesen)**

| Senaryo | Algılama | Aksiyon | Hız |
|---|---|---|---|
| Çıkış kısa devre | akım > limit | eFuse kesme | hızlı |
| Hoparlör DC offset | DC >0.5 V | röle aç | ms |
| Aşırı ısınma | sıcaklık > OTP | kapanma | orta |
| ESD deşarj | tepe | TVS toprağa | çok hızlı |
| Giriş surge | tepe | MOV/TVS | hızlı |
| Açılış inrush | akım | soft-start | açılış |

**DC-offset / röle koruma mantığı**

| Adım | İş | Eşik |
|---|---|---|
| 1 | Çıkış DC ölç | her örnek/blok |
| 2 | \|DC\| hesap | >0.5 V kontrol |
| 3 | Eşik aşımı | röle komut |
| 4 | Röle açma | hoparlör ayır |
| 5 | Normale dön | gecikme sonra kapat |

**Koruma — karar özet matrisi**

| Tehdit | Birincil koruma | İkincil |
|---|---|---|
| Kısa devre | eFuse | sigorta |
| Aşırı gerilim | TVS | crowbar |
| Aşırı ısınma | OTP | termal röle |
| ESD | ESD array | TVS |
| İnrush | NTC/soft-start | pre-charge |
| DC offset | röle | algılama |

**Eşik matrisi (örnek — hepsi `⚠️ VERIFICATION REQUIRED`)**

| Koruma | Eşik | Not |
|---|---|---|
| DC offset | 0.5 V | kural |
| Akım limit | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| Gerilim sınır | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| OTP | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| ESD | IEEE 61000-4-2 | atıf `⚠️ VERIFICATION REQUIRED` |

**Koruma hız sıralaması**

| Koruma | Hız | Öncelik |
|---|---|---|
| ESD/TVS | çok hızlı | ilk |
| Akım (eFuse) | hızlı | ikinci |
| Crowbar | hızlı | ikinci |
| Termal (OTP) | orta | üçüncü |
| DC röle | yavaş (ms) | son |

**Verim / kayıp mantığı**

| Kayıp | Kaynak | Azaltma |
|---|---|---|
| Anahtarlama kaybı | MOSFET geçiş | doğru boyut |
| İletim kaybı | Rds(on) | düşük Rds |
| İndüktör kaybı | DCR + çekirdek | kaliteli L |
| Kapasitör kaybı | ESR | düşük ESR |
| Lineer kayıp | (VIN−VOUT)·I | LDO gürültü-öncelik |

**Verim-ölçüm mantığı**

| Ölçüm | Yöntem |
|---|---|
| Giriş gücü | VIN·IIN |
| Çıkış gücü | VOUT·IOUT |
| Verim | POUT/PIN |
| Isı kaybı | (PIN−POUT) |
| Termal | ΔT ölçümü |

**Verim vs gürültü — karar matrisi**

| Öncelik | Seçim | Gerekçe |
|---|---|---|
| Gürültü kritik | LDO (son) | temiz rail |
| Verim kritik | Buck/boost | verimli |
| İkisi | Buck ön + LDO son | dengeli |
| Düşük EMI | Düşük fs / LC | EMI |
| İzole | Flyback | güvenlik |

**Verimlilik-ölçüm kabul matrisi**

| Parametre | Kabul | Durum |
|---|---|---|
| Buck verim | hedef (UNKNOWN) | `⚠️ VERIFICATION REQUIRED` |
| LDO dropout | datasheet | `⚠️ VERIFICATION REQUIRED` |
| Isı kaybı | termal ΔT | `⚠️ VERIFICATION REQUIRED` |
| Quiescent | düşük | `⚠️ VERIFICATION REQUIRED` |

**Güç topoloji seçim kararı (başlangıç)**

| Girdi→Çıktı | Topoloji | Gerekçe |
|---|---|---|
| Yüksek→düşük, verimli | Buck | verim |
| Düşük→yüksek | Boost | yükseltme |
| İki yöne | Buck-boost | esnek |
| İzole | Flyback | güvenlik |
| Temiz son | LDO | gürültü |

**Rail sıralama (power sequencing) tablosu**

| Sıra | Rail | Beklenen | Durum |
|---|---|---|---|
| 1 | Ham DC | VIN stabil | — |
| 2 | DVDD | logic hazır | — |
| 3 | AVDD/AVSS | analog hazır | — |
| 4 | VREF | referans stabil | — |
| 5 | HP_OUT | çıkış hazır | — |
| 6 | Röle | DC algılamadan sonra | kural |

**Gürültü-bütçe rail ilişkisi**

| Rail | Gürültü etkisi | SNR katkısı |
|---|---|---|
| AVDD | doğrudan | yüksek |
| VREF | doğrudan | kritik |
| DVDD | sınırlı (izole) | orta |
| HP_OUT | hoparlör | yüksek |

**Rail-güç tablosu (örnek)**

| Rail | Tipik güç | Öncelik |
|---|---|---|
| AVDD | düşük | yüksek |
| DVDD | düşük | orta |
| HP_OUT | orta-pik | yüksek |
| Relay_V | pik | orta |

### §T-K000.11 — Terim ekleri ve ölçü-birim tablosu (K000 kaynaklı)

Kaynak: `guc-ve-koruma-anayasa.md` §6 (L146–163), §6b (L274–284), §6c (L337–347), §6d (L437–444), §6c-ölçü (L337–347).

**Terimler (power/protection)**

| Terim | Anlam |
|---|---|
| LDO | Lineer düşük-damlama regülatör |
| Buck | Düşürücü anahtarlama topolojisi |
| Boost | Yükseltici topoloji |
| Buck-boost | Her iki yöne |
| Flyback | İzole anahtarlama |
| eFuse | Elektronik sigorta (akım limiti) |
| AKŞ (crowbar) | Aşırı-gerilim kısa-devre kesici |
| TVS | Tepe-bastırma diyodu |
| Inrush | Açılış ani akımı |
| Soft-start | Yumuşak açılış rampası |
| Ripple | DC üzerinde AC dalgalanma |
| PSRR | Güç kaynak redd oranı |
| OTP | Aşırı-ısı kapanması |
| Hot-swap | Sıcak takma koruması |

**Terimler — devam**

| Terim | Anlam |
|---|---|
| PSRR | Güç reddi (dB) |
| Quiescent | Boşta akım |
| DCR | Direnç (indüktör) |
| ESR | Eşdeğer seri direnç (kapasitör) |
| Creepage / clearance | İzolasyon boşluğu |
| Class II | Çift izolasyon |
| Röle contact | Kontak direnci |

**Terim — ek**

| Terim | Anlam |
|---|---|
| Pre-charge | Ön şarj |
| Sequencing | Rail sıralama |
| Brown-out | Düşük gerilim |
| Derating | Güç düşürme |

**Ölçü / birim tablosu**

| Büyüklük | Birim | Not |
|---|---|---|
| Gerilim | V | rail |
| Akım | A | yük / limit |
| Güç | W | bütçe |
| Sıcaklık | °C | termal |
| Frekans | kHz | fs |
| Ripple | mV | temizlik |
| PSRR | dB | redd |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
