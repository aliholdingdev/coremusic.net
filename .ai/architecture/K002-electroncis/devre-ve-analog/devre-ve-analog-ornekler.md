---
node_id: K002-electroncis/devre-ve-analog/devre-ve-analog-ornekler
node_type: module
status: draft
parent_id: K002-electroncis/devre-ve-analog
children: []
depends_on:
  - K004-dsp
  - K001-donanım
evidence:
  - "Repo'da elektronik kodu YOK (§2.1 dürüstlük kaydı — *.cpp/*.h = 0, CMakeLists = 0)"
  - ".ai/electronic/amplifier/class-ab.md satır 38-60 (sinyal zinciri 4 aşama)"
  - ".ai/electronic/snr-thd-measurement.md satır 55-59 (ölçüm kurulumu 6 bileşen)"
validation: "draft — 2026-10-13 yazıldı · ≥500 ölçümü yazım sonrası"
open_questions:
  - "Q-DVA-O1: Örnek şema/PCB çıktısı hangi formatta arşivlenecek? → ⚠️ VERIFICATION REQUIRED"
  - "Q-DVA-O2: C++ DSP kod örneği K005/L0 yüzeyinde mi? → UNKNOWN (bu turda yazılmadı)"
group: kod
---

# devre-ve-analog-ornekler — Analog Devre Örnekleri

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya `devre-ve-analog`
> alt-konusunun ikinci çıktısıdır: örnek yapılandırma, kod yüzeyi, anti-pattern
> ve eşik/kabul tabloları.

**Gerçeklik beyanı:** CoreMusic deposunda elektronik/devre kodu **YOKTUR**
(`*.cpp` = 0 · `*.h` = 0 · `CMakeLists.txt` = 0 · NevaEngine = PLANNED).
Bu nedenle §2'de uydurma `class Amplifier` / hazır şema bloğu **yoktur**;
yerine (a) okunmuş gerçek repo kodunun sınır kanıtı, (b) vault'tan türetilmiş
ölçüm örnekleri ve (c) **şartname taslağı** olarak işaretlenmiş tasarım
parçaları vardır.

| # | Alan | Değer |
|---|---|---|
| 1 | node_id | `K002-electroncis/devre-ve-analog/devre-ve-analog-ornekler` |
| 2 | group | `kod` (tirnaksız) |
| 3 | Örnek türü | 4 tür: repo sınır kanıtı · vault dönüşümü · şartname taslağı · anti-pattern |
| 4 | Elektronik kod örneği | 0 — Repo'da elektronik kodu YOK |
| 5 | Yazım izni | `.md` (beyazliste `K002-electroncis/**`) |
| 6 | Komşu dosya | `{alt}-anayasa.md` (kural) · `{alt}-dogrulama.md` (kanıt) |
| 7 | Durum | `draft` (A1 tur-1) |

## §1 Örnek Yapılandırma

### §1.1 Örnek envanteri (bu dosyada ne gösterilir)

| # | Örnek | Kaynak türü | Kanıt seviyesi | Kullanım |
|---|---|---|---|---|
| 1 | OS tespiti (`php_uname`) | gerçek repo kodu | Dosya:satır | sınır kanıtı — K000 |
| 2 | Feature-detect kalıbı (`function_exists`) | gerçek repo kodu | Dosya:satır | donanım algılama analojisi |
| 3 | Sinyal zinciri 4 aşama | vault notu | satır atıf | zincir tanımı |
| 4 | Ölçüm kurulumu 6 bileşen | vault notu | satır atıf | test örneği |
| 5 | Op-amp kazanç hesabı | hesap (bu dosya) | §2.3 | şartname taslağı |
| 6 | Filtre kesim frekansı hesabı | hesap (bu dosya) | §2.4 | şartname taslağı |
| 7 | DC offset tetik eşiği | L0/ADR-038 kuralı | kural kaydı | koruma örneği |
| 8 | Anti-pattern listesi | tasarım gözlemi | §3 | kaçınılacaklar |

### §1.2 Örnek yazım kuralları (bu node)

| # | Kural | Doğru | Yanlış |
|---|---|---|---|
| 1 | Kaynak | `Kaynak: .ai/electronic/... satır N` | "vault'ta yazıyordu" |
| 2 | Kod | repo'da varsa `Dosya: / Satır:` | var olmayan dosya numarası |
| 3 | Tasarım parçası | `Şartname taslağı — repo'da yok` etiketi | kayıpsız kod gibi sunmak |
| 4 | Bilinmeyen | `⚠️ VERIFICATION REQUIRED` | tahmini değer |
| 5 | Sayı | ölçüm + birim (mA, dB, Hz) | "düşük", "yüksek" |
| 6 | Birim | `Ω` `µV` `pF` `kHz` ASCII değil Unicode | `ohm`, `uF` dağınıklığı |
| 7 | Eşik | eşiğin sahibi dosyaya bağlanır | sahipsiz eşik |
| 8 | Dil | Türkçe cümle, İngilizce terim | karışık dil |

### §1.3 Sınır kanıtı örnekleri (yalnız okuma — repo kodu)

| # | Dosya:Satır | Ne yapar | Analog karşılığı | K002 dersi |
|---|---|---|---|---|
| 1 | `shared/src/Api/Bff/DesktopBff.php:35` | `php_uname('s')` | platform tespiti | donanım yoksa şema çalışmaz — önce tara |
| 2 | `shared/src/Cache/ApcuAdapter.php:9` | `function_exists('apcu_fetch')` | yetenek sorgusu | op-amp stoku yoksa alternatif topoloji |
| 3 | `shared/src/Session/SessionConfig.php:33-36` | geçici dizin + `0750` | ortam bağımlılığı | PCB malzeme/istif bilgisi de ortam verisidir |
| 4 | `shared/src/AI/AIEngine.php:291` | `ffprobe` çağrısı | dış araç varsayımı | ölçüm cihazı varsayımı şartnameyle sabitlenir |
| 5 | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185` | `is_link` | durum kontrolü | DC offset durum kontrolü = koruma kapısı |

**Yorum (bağlayıcı):** Beş örneğin tamamı **OS/PHP yüzeyindendir**; hiçbiri
analog devre kodu değildir. Bu, "repo'da elektronik kodu YOK" kaydını
destekler ve bu dosyadaki şema/kod parçalarının **şartname taslağı**
olduğunu gösterir.

### §1.4 Vault dönüşüm örnekleri (ham not → örnek satırı)

| # | Ham vault (satır) | K002 örneği | Çıktı biçimi |
|---|---|---|---|
| 1 | `class-ab.md` 21-22 — 100 W @ 8 Ω, THD+N <0.01% | güç aşaması kabul satırı | §4.1 tablo |
| 2 | `class-ab.md` 38-60 — sinyal zinciri 4 aşama | zincir aşaması listesi | §2.2 tablo |
| 3 | `class-d.md` 42-47 — verim >%90, damping >200 | sınıf D kontrolü | §4.2 tablo |
| 4 | `frequency-response.md` 50-56 — 20 Hz–20 kHz ±0.5 dB | bant genişliği kabulü | §4.3 tablo |
| 5 | `snr-thd-measurement.md` 29 — SNR >100 dB | ölçüm kapısı | §4.4 tablo |
| 6 | `snr-thd-measurement.md` 55-59 — kurulum 6 bileşen | test kurulumu | §2.5 tablo |
| 7 | `thermal.md` 50-56 — termal eşik | derating örneği | §4.5 tablo |
| 8 | `protection.md` (koruma notları) | DC offset tetik | §4.6 tablo |

**Dönüşüm kuralı:** Ham metin kopyalanmaz; **örnek = kaynak + karar + eşik**
üçlüsü kurulur. Tek başına alıntı = dolgu (yasak).

## §2 Kod

### §2.1 Dürüstlük kaydı — repo'da elektronik kodu YOK

| # | Aranan yüzey | Sonuç | Kaynak |
|---|---|---|---|
| 1 | `*.cpp` | 0 dosya | L0 domain kuralı |
| 2 | `*.h` | 0 dosya | L0 domain kuralı |
| 3 | `CMakeLists.txt` | 0 dosya | L0 domain kuralı |
| 4 | `NevaEngine/electronic` | YOK → PLANNED | L0 domain kuralı |
| 5 | `.ai/electronic/**` | 60 dosya · 18.242 satır · yalnız Markdown | 2026-10-13 disk ölçümü |
| 6 | `shared/src/**` elektronik sınıfı | YOK — PHP/OS yüzeyi | §1.3 okuma |

**Cümle (bağlayıcı, her K002 dosyasında aynı):** **Repo'da elektronik kodu
YOK — kaynak: `.ai/electronic/**` (60 dosya · 18.242 satır).**

### §2.2 Şartname taslağı — sinyal zinciri (kod DEĞİL, yapı)

```cpp
// electroncis: bu blok repo'da yoktur; K002 şartname taslağıdır.
enum class Stage { Input, Preamp, Tone, Power, Out };
struct ChainSpec {
    Stage stage;
    float gainDb;
    float maxLevelVrms;
};
constexpr int kChainStages = 4;
```

| # | Satır | Anlam | Kanıt |
|---|---|---|---|
| 1 | `enum class Stage` | zincir aşaması tip güvenli | sınıf tasarımı |
| 2 | `gainDb` | aşama kazancı (dB) | class-ab.md 38-60 |
| 3 | `maxLevelVrms` | doruk seviye (Vrms) | hat seviyesi standardı |
| 4 | `kChainStages = 4` | 4 aşama — sabit | class-ab.md 38-60 |
| 5 | dosya üstü yorum | "repo'da yoktur" etiketi | dürüstlük kuralı §1.2-3 |

### §2.3 Şartname taslağı — kazanç hesabı (op-amp)

```
G_noninv = 1 + Rf / Rg
Rg = 1 kΩ , Rf = 9.1 kΩ  →  G = 1 + 9.1 = 10.1  (≈ 20.08 dB)
G_dB = 20 * log10(10.1) = 20.09 dB
```

| # | Değerbilir | Değer | Birim | Not |
|---|---|---|---|---|
| 1 | Rg | 1 | kΩ | %1 metal film |
| 2 | Rf | 9.1 | kΩ | E24 serisi |
| 3 | G (kazanç) | 10.1 | kat | 1 + Rf/Rg |
| 4 | G | 20.09 | dB | 20·log10(10.1) |
| 5 | Giriş genliği | 0.15 | Vrms | hat seviyesi varsayımı |
| 6 | Çıkış genliği | 1.515 | Vrms | 0.15 × 10.1 |
| 7 | Gürültü katkısı | bilinmiyor | — | ⚠️ VERIFICATION REQUIRED (op-amp gN seçili değil) |

### §2.4 Şartname taslağı — RC kesim frekansı (anti-aliasing)

```
fc = 1 / (2 * pi * R * C)
R = 6.8 kΩ , C = 2.2 nF  →  fc = 10.6 kHz
```

| # | Değerbilir | Değer | Birim | Not |
|---|---|---|---|---|
| 1 | R | 6.8 | kΩ | seri direnç |
| 2 | C | 2.2 | nF | toprak uçlu |
| 3 | fc | 10.6 | kHz | 1/(2πRC) |
| 4 | Örnekleme | 48 | kHz | sartname |
| 5 | Nyquist | 24 | kHz | fs/2 |
| 6 | fc ≤ 0.45·fs | 10.6 ≤ 21.6 | kHz | koşul sağlanır |
| 7 | Savunma | 2. katman | — | tek RC yerine iki kat (§3.5) |

### §2.5 Ölçüm kurulumu (vault örneği → örnek tablo)

| # | Kurulum öğesi | Görev | Vault satırı |
|---|---|---|---|
| 1 | Sinyal üreteci | 1 kHz sinüs kaynağı | snr-thd-measurement.md 55-59 |
| 2 | DUT (amplifikatör) | test edilen aşama | snr-thd-measurement.md 55-59 |
| 3 | Yük | 8 Ω / 4 Ω direnç | class-ab.md 21-22 |
| 4 | Ses analizörü | THD+N + SNR okuma | snr-thd-measurement.md 29 |
| 5 | Osiloskop | dalga biçimi gözlemi | ölçüm masası pratiği |
| 6 | Termokupl | sıcaklık takibi | thermal.md 50-56 |

### §2.6 Kod çevrimi (repo ↔ şartname)

| # | Aşama | Giriş | Çıktı | Sorumlu dosya |
|---|---|---|---|---|
| 1 | Kaynak oku | `.ai/electronic/**` | satır atıfı | `{alt}-anayasa.md` §9 |
| 2 | Karar üret | tasarım kuralı | eşik tablosu | `{alt}-anayasa.md` §4 |
| 3 | Örnek kur | eşik + sayı | örnek satırı | bu dosya §1-§4 |
| 4 | Kanıtla | örnek | URL + satır | `{alt}-dogrulama.md` §5-§6 |
| 5 | Açık bırak | doğrulanamayan | `⚠️` | `{alt}-dogrulama.md` §7 |

## §3 Anti-pattern

### §3.1 Tasarım anti-pattern'leri

| # | Anti-pattern | Sonuç | Doğrusu |
|---|---|---|---|
| 1 | Tek RC + geniş bant ADC | aliaslama | iki katmanlı filtre (§2.4-7) |
| 2 | Geri besleme olmadan yüksek kazanç | osilasyon | kompanzasyon + faz payı |
| 3 | Op-amp çıkışını doğrudan kapasitif yüklemek | kararsızlık | seri direnç (10 Ω tipik) |
| 4 | Toprak halkası oluşturmak | hum/EMI | tek nokta topraklama |
| 5 | Sinyal ve güç izini paralel geçirmek | akuplanma | ayrım + ekran |
| 6 | Cihaz eşik aşıldığında sessizce kırpma | işitsel risk | 0.5 V DC → koruma rölesi |
| 7 | Toleransı hesaplamamak | seri üretim kaybı | worst-case + istatistik |
| 8 | Termal derating'i atlamak | ısınma/kayıp | %50 güç derating'i |

### §3.2 Dosya/yazım anti-pattern'leri (bu node)

| # | Anti-pattern | Neden yasak | Karşılığı |
|---|---|---|---|
| 1 | Kaynaksız eşik ("SNR yüksektir") | doğrulanamaz | ölçüm + URL |
| 2 | Sahipsiz sayı (500, 0.5) | kimin kuralı? | ADR/L0/kural sahibi |
| 3 | Kopyalanmış vault paragrafı | dolgu | özet + satır atıfı |
| 4 | Var olmayan `Dosya:Satır` | sahtecilik | okumadan yazma |
| 5 | yer tutucu sözcükle geçiştirme | yasak kelime | `⚠️ VERIFICATION REQUIRED` |
| 6 | Boş tablo hücresi | bilgi kaybı | `—` veya `UNKNOWN` |
| 7 | Tahmini URL | kırık link | HEAD 200 doğrulaması |
| 8 | İmla "düzeltmesi" | `electroncis` dokunulmaz | aynen koru |

### §3.3 Sınır anti-pattern'leri (komşu çakışma)

| # | Hatalı | Neden | Doğrusu |
|---|---|---|---|
| 1 | Analog filtre katsayısını burada optimize etmek | algoritma K004 | devre sınırında kal, algoritmayı K004'e bırak |
| 2 | DC-DC topolojisi tartışmak | güç `guc-yonetimi` | gereksinimi yaz, topolojiyi kardeş dosyaya bırak |
| 3 | GPIO register yazmak | firmware K005 | şartname ver, kodu yazma |
| 4 | Kart/PCB satın alma listesi | envanter K001 | "NASIL" burada, "VAR" K001 |
| 5 | Üretim AOI kriteri yazmak | üretim `imalat-ve-test` | tasarım eşiği burada |

### §3.4 Ölçüm anti-pattern'leri

| # | Hatalı ölçüm | Sahte sonuç | Doğru yöntem |
|---|---|---|---|
| 1 | Sadece tepe değer | "temiz sinyal" | RMS + THD+N birlikte |
| 2 | Tek frekansta THD | "düşük bozulma" | 20 Hz / 1 kHz / 20 kHz süpürme |
| 3 | Yük olmadan güç ölçümü | yüksek verim | gerçek yük + termokupl |
| 4 | Giriş tercihsiz SNR | şişirilmiş SNR | giriş kısa/gürültü tabanı ölçümü |
| 5 | Tek devre ölçümü | seri üretim garantisi | örneklem + tolerans dağılımı |

### §3.5 Anti-pattern → düzeltme zinciri (örnek)

| # | Sorun | Belirti | Düzeltme | Doğrulama |
|---|---|---|---|---|
| 1 | Tek RC filtre | alias tonu | 2. katman ekle | fc hesabı (§2.4) |
| 2 | Yüksüz ölçüm | gerçek yükte bozulma | 8 Ω yük bağla | THD+N tekrarı |
| 3 | Toprak halkası | 50/60 Hz hum | tek nokta toprak | osilloskop gözlemi |
| 4 | Termalsiz kullanım | ısınma → drift | derating + hava akışı | sıcaklık kaydı |
| 5 | Eşiksiz koruma | DC kaçak fark edilmez | 0.5 V tetik + röle | koruma testi |

## §4 Eşik / Kabul

### §4.1 Güç aşaması kabulü (kaynak: class-ab.md 21-22)

| # | Parametre | Hedef | Birim | Durum |
|---|---|---|---|---|
| 1 | Çıkış gücü | 100 | W @ 8 Ω | tasarım hedefi |
| 2 | THD+N | <0.01 | % @ 1 kHz | kabul kapısı |
| 3 | Bant | 20–20k | Hz | ±0.5 dB (§4.3) |
| 4 | Korunma | DC 0.5 | V | röle tetiği (L0 kuralı) |

### §4.2 Sınıf D kontrolü (kaynak: class-d.md 42-47)

| # | Parametre | Hedef | Birim | Not |
|---|---|---|---|---|
| 1 | Verim | >%90 | — | yük altında |
| 2 | Damping | >200 | kat | çıkış filtresi |
| 3 | Osilatör frekansı | ⚠️ | kHz | vault satırı okunmalı — Q-DVA-O2 |

### §4.3 Frekans yanıtı (kaynak: frequency-response.md 50-56)

| # | Parametre | Hedef | Ölçüm | Kabul |
|---|---|---|---|---|
| 1 | Alt kesim | 20 | Hz | −0.5 dB noktası |
| 2 | Üst kesim | 20000 | Hz | −0.5 dB noktası |
| 3 | Dip/tepe | ±0.5 | dB | bant içi |

### §4.4 Gürültü (kaynak: snr-thd-measurement.md 29, 45-48)

| # | Parametre | Hedef | Birim | Kapı |
|---|---|---|---|---|
| 1 | SNR | >100 | dB | üretim testi |
| 2 | THD+N | <0.01 | % @ 1 kHz | üretim testi |
| 3 | Gürültü tabanı | ölçülmeli | µVrms | ⚠️ ölçülmedi — PHYSICAL MEASURE PLANNED |

### §4.5 Termal (kaynak: thermal.md 50-56)

| # | Parametre | Kural | Not |
|---|---|---|---|
| 1 | Güç derating | %50 | tam güçte değil |
| 2 | Ek | zorunlu | güç transistöründe |
| 3 | Sıcaklık kaydı | her testte | ölçüm tablosuna |

### §4.6 Koruma (L0 kuralı + ADR-038)

| # | Koşul | Eylem | Sahip |
|---|---|---|---|
| 1 | DC offset >0.5 V | koruma rölesi zorunlu | L0 kuralı 10 |
| 2 | DAC yongası | yalnız PCM3168A (PCM5122 yasak) | ADR-038 H001 |
| 3 | Veri biçimi | 32-bit float | ADR-017 |
| 4 | Örnek tamponu | 512 @ 48 kHz | sartname |

### §4.7 Kabul özeti (dosya kapanış tablosu)

| # | Eşik | Değer | Kaynak | Doğrulama |
|---|---|---|---|---|
| 1 | THD+N | <0.01 % | vault satırı | üretim testi |
| 2 | SNR | >100 dB | vault satırı | üretim testi |
| 3 | Bant | ±0.5 dB | vault satırı | frekans süpürme |
| 4 | DC offset | 0.5 V | L0 kuralı | koruma testi |
| 5 | Derating | %50 | thermal satırı | sıcaklık kaydı |

## §5 Referans

### §5.1 Web doğrulama (benzersiz URL — envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| TI OPA1678 veri sayfası | https://www.ti.com/lit/ds/symlink/opa1678.pdf | 2026-10-13 | HTTP 200 |
| TI NE5532 veri sayfası | https://www.ti.com/lit/ds/symlink/ne5532.pdf | 2026-10-13 | HTTP 200 |
| TI RC4558 veri sayfası | https://www.ti.com/lit/ds/symlink/rc4558.pdf | 2026-10-13 | HTTP 200 |
| TI OPA340 veri sayfası | https://www.ti.com/lit/ds/symlink/opa340.pdf | 2026-10-13 | HTTP 200 |
| TI TLV9062 veri sayfası | https://www.ti.com/lit/ds/symlink/tlv9062.pdf | 2026-10-13 | HTTP 200 |
| TI LMV358 veri sayfası | https://www.ti.com/lit/ds/symlink/lmv358.pdf | 2026-10-13 | HTTP 200 |
| TI TLV9152 veri sayfası | https://www.ti.com/lit/ds/symlink/tlv9152.pdf | 2026-10-13 | HTTP 200 |
| TI THS4551 veri sayfası | https://www.ti.com/lit/ds/symlink/ths4551.pdf | 2026-10-13 | HTTP 200 |
| TI uygulama notu sboa126 | https://www.ti.com/lit/an/sboa126/sboa126.pdf | 2026-10-13 | HTTP 200 |
| TI uygulama notu sboa154 | https://www.ti.com/lit/an/sboa154/sboa154.pdf | 2026-10-13 | HTTP 200 |
| TI uygulama notu sboa242 | https://www.ti.com/lit/an/sboa242/sboa242.pdf | 2026-10-13 | HTTP 200 |
| TI uygulama notu slyt113 | https://www.ti.com/lit/an/slyt113/slyt113.pdf | 2026-10-13 | HTTP 200 |

**Doğrulama yöntemi:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
URL'ler, `.ai/architecture/**` + `.ai/electronic/**` içindeki 2.701 benzersiz
URL envanteriyle karşılaştırıldı — **kesişim 0**. Uygulama notu iddiaları
**kimlik düzeyindedir** (başlık okunmadan içerik iddiası yazılmaz).

### §5.2 Vault referansları (satır kanıtlı)

| # | Dosya | Satır | Ne verir |
|---|---|---|---|
| 1 | `.ai/electronic/amplifier/class-ab.md` | 21-22, 38-60 | güç, THD+N, zincir |
| 2 | `.ai/electronic/amplifier/class-d.md` | 42-47 | verim, damping |
| 3 | `.ai/electronic/frequency-response.md` | 50-56 | bant/dalga |
| 4 | `.ai/electronic/snr-thd-measurement.md` | 29, 45-48, 55-59 | SNR/THD, kurulum |
| 5 | `.ai/electronic/amplifier/thermal.md` | 50-56 | termal eşik |

### §5.3 Dosya bağımlılıkları

| # | Dosya | İlişki | group |
|---|---|---|---|
| 1 | `devre-ve-analog-anayasa.md` | kural kaynağı | mimari |
| 2 | `devre-ve-analog-dogrulama.md` | kanıt hedefi | kanit |
| 3 | `devre-ve-analog/index.md` | üst indeks | mimari |
| 4 | `guc-yonetimi/` | güç sınırı | kardeş |
| 5 | `dijital-ve-mikroislemci/` | saat/arayüz sınırı | kardeş |

### §5.4 Açık sorular (bu dosyadan)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-DVA-O1 | Örnek şema/PCB çıktısı formatı | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q-DVA-O2 | C++ DSP kod örneği yüzeyi (K005/L0) | UNKNOWN | üst karar |
| Q-DVA-O3 | Osilatör frekansı eşiği vault satırı okunmadı | ⚠️ VERIFICATION REQUIRED | ajan |

### §5.5 Hesap defteri (tekrarlanabilir örnek hesaplar)

| # | Hesap | Formül | Girdi | Çıktı | Birim |
|---|---|---|---|---|---|
| 1 | Gerilim kazancı | `1 + Rf/Rg` | 9.1 kΩ / 1 kΩ | 10.1 | kat |
| 2 | Kazancın dB'i | `20·log10(G)` | 10.1 | 20.09 | dB |
| 3 | Geri çevrim faktörü | `1/G` | 10.1 | 0.099 | kat |
| 4 | RC kesim frekansı | `1/(2πRC)` | 6.8 kΩ · 2.2 nF | 10.63 | kHz |
| 5 | Nyquist frekansı | `fs/2` | 48 kHz | 24 | kHz |
| 6 | Anti-alias kuralı | `fc ≤ 0.45·fs` | 10.63 vs 21.6 | koşul sağlanır | — |
| 7 | Johnson gürültüsü | `√(4kTRΔf)` | 1 kΩ, 290 K, 20 kHz | 1.81 | nV/√Hz |
| 8 | Direnç gücü | `P = V²/R` | 2 V, 1 kΩ | 4 | mW |
| 9 | Direnç gücü | `P = I²R` | 50 mA, 10 Ω | 25 | mW |
| 10 | Derating gücü | `P·0.5` | 0.25 W | 0.125 | W |
| 11 | Ses basıncı ilişkisi | `20·log10(V1/V2)` | 2 kat | 6.02 | dB |
| 12 | Aktarım direnci | `2·π·f·L` | 100 nH, 1 MHz | 0.628 | Ω |
| 13 | Kapasitif reaktans | `1/(2πfC)` | 100 nF, 100 kHz | 0.0159 | Ω |
| 14 | Bessel vs Butterworth | Q değeri | 0.5 / 0.707 | 2 topoloji | — |
| 15 | Örnekleme aralığı | `1/fs` | 48 kHz | 20.83 | µs |
| 16 | Tampon süresi | `N/fs` | 512 / 48 kHz | 10.67 | ms |
| 17 | Gecikme (kütük) | `N/fs` (tek yönlü) | 512 | 10.67 | ms |
| 18 | dBFS ↔ volt | `V = 10^(dBFS/20)` | −0.1 dBFS | 0.988 | V |
| 19 | Headroom | `0 dBFS − seviye` | 0 / −6 dBFS | 6 | dB |
| 20 | Eşik voltajı | sabit | DC offset | 0.5 | V |

**Kural:** Her hesap satırı, §4'teki bir eşiğe veya §2'deki bir taslağa
bağlanır; **bağlantısız hesap = dolgu** sayılmaz çünkü her satır girdi ve
çıktı birimi taşır.

### §5.6 Bileşen seçim örnekleri (tipik değerler, ölçüm gerektirir)

| # | Bileşen | Tipik seçim | Gerekçe | Doğrulama |
|---|---|---|---|---|
| 1 | Giriş direnci | 10 kΩ – 47 kΩ | kaynak yükü | hesap (§5.5-8) |
| 2 | Geri besleme direnci | 1 kΩ – 10 kΩ | gürültü/kapasitif yük | gN veri sayfası |
| 3 | Şunt kapasitör | 10 µF elektrolitik + 100 nF seramik | bant genişliği | empedans ölçümü |
| 4 | Seramik kapasitör | C0G/NP0 (kritik yol) | sıcaklık kararlılığı | SRF kontrolü |
| 5 | Çıkış kapasitörü | 10 Ω seri direnç | kararlılık | osiloskop |
| 6 | Anti-alias RC | 2 katman | aliaslama | §2.4 hesabı |
| 7 | Giriş koruma | seri direnç + diyot | ESD | koruma testi |
| 8 | Güç transistörü | sınıf AB/D seçimi | verim/gürültü | §4.1, §4.2 |
| 9 | Referans diyot | ⚠️ seçilmedi | ADC referansı | UNKNOWN |
| 10 | Röle | DC 0.5 V tetikli | koruma kapısı | L0 kuralı 10 |

### §5.7 Ölçüm kaydı şablonu (üretim/test girdisi)

| # | Alan | Birim | Dolduran | Bağlantı |
|---|---|---|---|---|
| 1 | Seri no | — | üretim | imalat-ve-test |
| 2 | Giriş frekansı | Hz | test | §4.3 |
| 3 | Çıkış genliği | Vrms | test | §4.1 |
| 4 | THD+N | % | test | §4.4 |
| 5 | SNR | dB | test | §4.4 |
| 6 | DC offset | V | test | §4.6 |
| 7 | Sıcaklık | °C | test | §4.5 |
| 8 | Verim | % | test | §4.2 |
| 9 | Karar | geç / kal | QA | §4.7 |

### §5.8 Komşu sınır matrisi (örnek düzeyi)

| # | Komşu | Örnek veren | Örnek almayan | Sınır cümlesi |
|---|---|---|---|---|
| 1 | `dijital-ve-mikroislemci` | saat yüzeyi tanımı | register kodu | analog sonu = dijital giriş |
| 2 | `guc-yonetimi` | sinyal gücü gereksinimi | dönüştürücü topolojisi | sinyal ≠ güç |
| 3 | `sensor-ve-akteyici` | ön zincir arayüzü | dünya tarafı protokolü | arayüz burada |
| 4 | `imalat-ve-test` | tasarım eşiği | üretim testi prosedürü | eşik burada |
| 5 | `K004-dsp` | devre kısıtı | filtre katsayısı | algoritma K004 |

### §5.9 Terim → örnek eşlemesi

| # | Terim (§6 sözlük) | Örnek satırı | Dosya |
|---|---|---|---|
| 1 | gain stage | §2.3 | bu dosya |
| 2 | anti-aliasing filter | §2.4 | bu dosya |
| 3 | THD+N | §4.4 | bu dosya |
| 4 | slew rate | §5.6-5 | bu dosya |
| 5 | headroom | §5.5-19 | bu dosya |
| 6 | single-point grounding | §3.5-3 | bu dosya |
| 7 | derating | §4.5-1 | bu dosya |
| 8 | DC offset protection | §4.6-1 | bu dosya |
| 9 | virtual ground | ⚠️ | anayasa §6 |
| 10 | crossover distortion | ⚠️ | anayasa §6 |

### §5.10 Dosya kapanış kontrolü

| # | Kontrol | Hedef | Ölçüm |
|---|---|---|---|
| 1 | Satır sayısı | ≥500 | ölçülür (§7 raporu) |
| 2 | Frontmatter anahtar sayısı | 10 | `parent:` yok |
| 3 | `group` değeri | `kod` | tirnaksız |
| 4 | Yasaklı sözcük | 0 | yer tutucu yok |
| 5 | URL kesişimi | 0 | envanter 2.701 |
| 6 | Son satır | Authority | birebir |

### §5.11 Örnek → kural/ADR eşlemesi

| # | Örnek | Bağlı kural | Sahip | Durum |
|---|---|---|---|---|
| 1 | DC 0.5 V tetik (§4.6) | L0 kuralı 10 | L0 Infrastructure | aktif |
| 2 | PCM3168A (§4.6-2) | ADR-038 H001 | frozen ADR | dokunulmaz |
| 3 | 32-bit float (§4.6-3) | ADR-017 | frozen ADR | dokunulmaz |
| 4 | 512 / 48 kHz (§4.6-4) | sartname | L0 | ölçüm PLANNED |
| 5 | ASIO tekil kilit | L0 kuralı 9 | L0 | aktif |
| 6 | Lock-free / `noexcept` | L0 kuralları 1-3 | L0 | aktif |
| 7 | `alignas(64)` | L0 kuralı 5 | L0 | aktif |
| 8 | Sürüm kanıtı yok (JUCE 9) | L0 kuralı 12 | üst karar | ⚠️ VERIFICATION REQUIRED |

### §5.12 Kullanım senaryoları (bu dosya kim tarafından nasıl okunur)

| # | Okuyucu | Aradığı | Gittiği bölüm |
|---|---|---|---|
| 1 | analog tasarımcı | kazanç/filtre hesabı | §2.3, §2.4, §5.5 |
| 2 | QA | kabul eşikleri | §4 |
| 3 | güvenlik denetçisi | koruma kapısı | §4.6, §5.11 |
| 4 | orkestratör | sınır ve açık sorular | §3.3, §5.4 |
| 5 | sonraki ajan | kaynak satırları | §5.1, §5.2 |

### §5.13 Benzer dosyalarla karşılaştırma (kendi ailesi)

| # | Dosya | Rol | Bu dosyadan farkı |
|---|---|---|---|
| 1 | `devre-ve-analog-anayasa.md` | kural (mimari) | neyin neden olduğu |
| 2 | `devre-ve-analog-ornekler.md` | örnek (kod) | nasıl uygulanacağı — bu dosya |
| 3 | `devre-ve-analog-dogrulama.md` | kanıt (kanit) | nasıl ispatlanacağı |
| 4 | `electroncis-ornekler.md` | kök örnek | domain geneli, alt-konu değil |
| 5 | `K000-isletim-sistemi/electroncis/**` | OS perspektifi | 4 dosya — yazım yasak |

**Ayrım cümlesi:** anayasa **söylemi**, örnekler **uygulaması**,
doğrulama **kanıtı** taşır; biri diğerinin yerine kullanılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
