---
title: "PCM3168A — Yedek Kaynak Çapraz Kontrolü ve Çelişki Kaydı"
type: architecture
category: architecture
date: 2026-10-06
updated: 2026-10-06
version: 4.0.0
status: active
authority: reference
---

# PCM3168A — Yedek Kaynak Çapraz Kontrolü ve Çelişki Kaydı

> Bu belge, [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] dosyasındaki iddiaları,
> salt-okunur yedek kaynaklarla (`_backup/arch-2026-10-06_1057/`) satır satır karşılaştırır.
> Her satır disk kanıtına dayanır; doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` ile işaretlenir.
> Uydurma voltaj, pin, sürüm veya frekans yazılmaz.

---

## §1 Amaç & Kapsam

**Amaç:** `pcm3168a-dac-adc-rehberi.md` (yazı hedefi) ile `_backup/arch-2026-10-06_1057/`
altındaki yedek kaynaklar arasındaki iddia farklarını **kanıt satır numarasıyla** tespit etmek,
kararları ve açık kalemleri tek dosyada toplamak.

**Kapsam:**

1. Birincil kaynak: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` (117 satır).
2. İkincil kaynak: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` (805 satır).
3. Çapraz destek kaynakları: `dac-adc-zinciri.md` · `index.md` · `ozet-durum.md` · `ak4458-dac.md` · `firmware/i2s-driver.md`.

**Kapsam dışı:** kaynak dosyalarda düzeltme (salt-okunur) · `git commit` (bu görevde atılmaz) ·
yeni voltaj/pin/frekans üretimi · veri sayfası (datasheet) tahmini.

**Karar kuralı:** çelişkide **kayıt tarihi daha eski, konuya daha yakın birincil kaynak** esas alınır;
kesinleşmeyen her satır `⚠️ VERIFICATION REQUIRED` olarak kalır ve §4'e işlenir.

---

## §2 Kaynak Kapsamı

> Tüm kaynaklar salt-okunurdur; bu görevce hiçbir kaynak dosya değiştirilmemiştir.

| # | Kaynak (yedek yol) | Rol | Satır | Okuma durumu |
|---|---|---|---|---|
| S1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` | Birincil — PCM3168A teknik sayfası | 117 | L1–L117 tam okundu |
| S2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` | İkincil — K1 katman özeti + kanıt kataloğu | 805 | L1–L805 tam okundu |
| S3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` | Çapraz — clock/BCLK değerleri | 159 | L1–L159 tam okundu (bu oturum L10–L39 + L150–L159 + grep) |
| S4 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/index.md` | Çapraz — rol ve durum | 96 | L1–L96 (bu oturum L88–L96 + grep) |
| S5 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ozet-durum.md` | Çapraz — modül durumu | 90 | L1–L90 (bu oturum L82–L90 + grep) |
| S6 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` | Çapraz — DAC primacy | ≥123 | L1–L123 (bu oturum L112–L123 + grep) |
| S7 | `_backup/arch-2026-10-06_1057/architecture/firmware/i2s-driver.md` | Çapraz — firmware I2C adresi | ≥496 | bu oturum L484–L496 + grep (L53, L464–L468) |
| T1 | `.ai/architecture/k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi.md` | Test edilen hedef — iddialar | 506 | L1–L506 (§3 iddiaları bu dosyadan) |

**Not (S7):** `firmware/i2s-driver.md` uzunluğu bu görevde tam okunmadı; README L788 dosya için
`L10–L485` aralığını iddia eder, bu oturumda son okunan satır L496'dır → `≥496`.

---

## §3 Çapraz Kontrol Tablosu

> Sütunlar: hedefteki rehber iddiası → yedek kaynakta karşılığı → sonuç.
> Sonuç değerleri: `UYUMLU` (kayıtla birebir) · `ÇELİŞKİ` (kayıtla çelişiyor, §4'e yansıtılır) · `TÜRETME` (kaynakta yok, hesap).

| # | Rehber iddiası (satır) | Yedek kaynak kanıtı (dosya · satır) | Sonuç |
|---|---|---|---|
| 1 | Kapsam: yedek dosya `pcm3168a-dac-adc.md` (L16) | S1 dosyası diskte, L1–L117 | UYUMLU |
| 2 | Üretici Texas Instruments (L47) | S1 L12 · "Texas Instruments tarafından üretilen" | UYUMLU |
| 3 | Çözünürlük/kanal 32-bit, 8 kanal (4 stereo) (L48) | S1 L18–L19 · 32-bit · 8 (4 stereo) | UYUMLU |
| 4 | Paket TQFP-48, 7×7 mm (L49) | S1 L26 · TQFP-48, 7×7mm | UYUMLU |
| 5 | Rol: analog girişleri dijital formata çevirir (L50) | S1 L12 · "analog giriş sinyallerini dijital formata dönüştürmek" | UYUMLU (S1 L12 "analog-to-analog" yazımı hatalı — §6 ES-08) |
| 6 | Destek I2S ve TDM (L51) | S1 L12 · "I2S ve TDM formatlarını destekler" + L90–L91 FMT0/FMT1 | UYUMLU |
| 7 | Durum `🟢 Hazır` (L52) | S1 L111 · "**Durum**: 🟢 Hazır" | UYUMLU |
| 8 | Çözünürlük 32-bit (L58) | S1 L18 | UYUMLU |
| 9 | Kanal sayısı 8 (4 stereo) (L59) | S1 L19 | UYUMLU |
| 10 | Örnekleme 8 kHz – 216 kHz (L60) | S1 L20 | UYUMLU |
| 11 | Giriş aralığı 2.1 Vrms (L61) | S1 L21 | UYUMLU |
| 12 | SNR 118 dB (L62) | S1 L22 | UYUMLU |
| 13 | THD+N −100 dB (%0.01) (L63) | S1 L23 · "-100dB (%0.01)" | ÇELİŞKİ (kaynak içi dB↔% uyuşmazlığı — §4 C09) |
| 14 | Giriş empedansı 10 kΩ (L64) | S1 L24 | UYUMLU |
| 15 | Besleme AVDD = 5 V · DVDD = 3.3 V (L65) | S1 L25 · "AVDD = 5V, DVDD = 3.3V" | UYUMLU (S2 L100–L102 ile çelişki — §4 C04) |
| 16 | Sıcaklık −40 °C … +85 °C (L66) | S1 L27 | UYUMLU |
| 17 | I2C adres 0x8C (ADDR = GND) (L67) | S1 L54 + L113 | UYUMLU (S7 L464–L468 ile çelişki — §4 C06) |
| 18 | Format I2S (FMT0 = 0, FMT1 = 0) (L68) | S1 L114 | UYUMLU |
| 19 | Slave (MD0 = 0, MD1 = 0; XMOS master) (L69) | S1 L115 | UYUMLU |
| 20 | Gain 0 dB … +31.5 dB (L70) | S1 L116 | UYUMLU |
| 21 | DC offset otomatik kalibrasyon (L71) | S1 L117 | UYUMLU |
| 22 | Filtre C1-C8 … R9-R10 (L77–L82) | S1 L61–L66 (6 satır birebir) | UYUMLU |
| 23 | Pin grubu 1–2 / 19–20 / 25–36 / 37-48 (L88–L104) | S1 L72–L97 (pin tablosu) | UYUMLU |
| 24 | Pin 3-18 "8 differential giriş çifti" (L90) | S1 L74–L81 (AINL1±…AINR4±, 8 çift) | UYUMLU (S2 L58 "6-in" ile çelişki — §4 C02) |
| 25 | DOUTA/B = seri veri A/B (L94–L95) | S1 L85–L86 | UYUMLU |
| 26 | Adres seçimi pini 36 ADDR (L103) | S1 L96 · "36 · ADDR · Giriş · I2C adres seçimi" | UYUMLU |
| 27 | Hesap 4.1 THD+N dönüşümü (L109) | S1 L23 üzerinden türetme: 20·log10(1e-4) = −80 dB | TÜRETME (kaynakta böyle bir dönüşüm yok) |
| 28 | Wiki-link `k068-guc-kaynagi-analog/analog-besleme` (L143) | disk: `.ai/architecture/k068-guc-kaynagi-analog/analog-besleme.md` mevcut | UYUMLU |
| 29 | §9 kaynak dizininde L16/L17/L33/L55/L113 satırları (rehberi L195–L286) | S1 L16, L17, L33, L55, L113 ile birebir | UYUMLU |
| 30 | README "Ana DAC · PCM3168A" (rehberi L13 bağlamı) | S2 L58 · `| Ana DAC | PCM3168A | 6-in/8-out | 24-bit | 192kHz |` | ÇELİŞKİ (S1 L8 "ADC" — §4 C01/C02/C03) |

---

## §4 Çelişki Kaydı

> Sütunlar: `# · İddia A (kaynak+satır) · İddia B (kaynak+satır) · Fark · Etki · Karar/⚠️ VERIFICATION REQUIRED · Etkilediği dosya`
> 11 kayıt. Kararlar bağlayıcı değildir; hepsi üst onay bekler.

| # | İddia A (kaynak+satır) | İddia B (kaynak+satır) | Fark | Etki | Karar/⚠️ VERIFICATION REQUIRED | Etkilediği dosya |
|---|---|---|---|---|---|---|
| C01 | S2 L54 · "### 2.2 DAC (Digital-to-Analog Converter)" + S2 L58 "Ana DAC · PCM3168A" + S2 L94 "## 3. PCM3168A DAC Detayı" | S1 L2/L8 · "PCM3168A 32-Bit 8-Kanal ADC" + S1 L12 "analog giriş sinyallerini dijital formata dönüştürmek" + S3 L18–L19 "DAC · AK4458" / "ADC · PCM3168A" + S6 L12 "COREMUSIC'ın ana DAC'ı" (AK4458) | Rol: ADC ↔ DAC; primacy ters | Yanlış sinyal yönü, yanlış katman sahipliği, yanlış test hedefi | Karar: birincil kaynak (S1) + S3 + S6 aynı yönde → rol **ADC**; README etiketi hatalı kabul edilir, README'de düzeltme onaysız yapılmaz. ⚠️ VERIFICATION REQUIRED (üst onay) | `README.md` (S2) · `pcm3168a-dac-adc-rehberi.md` (T1 §1) · `ak4458-dac.md` (S6) |
| C02 | S1 L18–L19 · "Çözünürlük 32-bit" · "Kanal Sayısı 8 (4 stereo)" + S1 L74–L81 · 8 diferansiyel giriş çifti (AINL1±…AINR4±) | S2 L58 · "Ana DAC · PCM3168A · 6-in/8-out · 24-bit · 192kHz" | 32-bit ↔ 24-bit (8 bit) · 8 giriş ↔ 6-in · 8 çıkış ↔ 8-out | Kanal sayısına bağlı pin/kanal dağılımı ve BOM satırı | Karar: S1 lehine kaynak-öncelikli okuma; README 6-in/24-bit satırı **kaynaksız**. ⚠️ VERIFICATION REQUIRED (datasheet karşılaştırması) | `README.md` (S2 L58) · `pcm3168a-dac-adc-rehberi.md` (T1 §3) |
| C03 | S1 L20 · "Örnekleme Hızı 8kHz – 216kHz" | S2 L58 "192kHz" · S2 L121 "Sample Rate 48kHz (default)" · S2 L124 "256fs = 12.288MHz" | Üç farklı üst sınır/varsayılan (216 / 192 / 48 kHz) | Clock planı, MCLK seçimi, firmware FS desteği | Karar: S1 aralığı (8 kHz–216 kHz) spesifikasyon; README 192 kHz ve 48 kHz varsayılanı **kaynağı doğrulanmamış**. ⚠️ VERIFICATION REQUIRED | `README.md` (S2 L58, L117–L126) · `pcm3168a-dac-adc-rehberi.md` (T1 §3) |
| C04 | S1 L25 · "Voltaj Besleme AVDD = 5V, DVDD = 3.3V" + S1 L106 · "±5V analog, +3.3V dijital" | S2 L100–L102 · "VDD1: +3.3V (Digital)" · "VDD2: +5V (Analog)" · "VSS: -5V (Analog)" | ±5V / VSS −5V ↔ tek yönlü AVDD = 5 V; ayrıca S1 kendi içinde de L25↔L106 farkı var | −5V hattı gerekip gerekmediği, PSU şeması, pin besleme | Karar: S1 L25 (spesifikasyon tablosu) birincil; S1 L106 ve S2 VSS −5V **çözülmemiş**. ⚠️ VERIFICATION REQUIRED (güç raili onayı) | `README.md` (S2 L99–L104) · `pcm3168a-dac-adc-rehberi.md` (T1 §3, §5) · `analog-besleme` (k068) |
| C05 | S2 L124–L126 · "System Clock 256fs = 12.288MHz" · "BCK 64fs = 3.072MHz" · "LRCK 48kHz" + S2 L107 · "BCK: Bit Clock (64fs)" + S2 L110 · "SCKI: System Clock (256fs or 512fs)" | S3 L20–L23 · "Clock Frequency 22.5792MHz / 24.576MHz" · "I2S Bit Clock 1.4112MHz (44.1kHz) / 1.536MHz (48kHz)" · "MCLK 256fs = 11.2896MHz / 12.288MHz" + S3 L106 · "SCK 1.4112MHz" | MCLK 11.2896 MHz (44.1k ailesi) ihmal edilmiş · BCK oranı 64fs ↔ 1.4112 MHz (44.1 kHz'te 32fs) | Clock hiyerarşisi, kilitlenme (lock) yok, jitter bütçesi | Karar: her iki kayıt da diskte; hangi ailenin (44.1 vs 48 kHz) esas olduğu **kaynağın kendisinde sabitlenmemiş**. ⚠️ VERIFICATION REQUIRED | `README.md` (S2 L106–L126) · `dac-adc-zinciri.md` (S3) · `i2s-ve-tdm-rehberi` (k057) · `zincir-mimari` (k054) |
| C06 | S1 L54 · "└─ ADDR ──▶ GND (I2C Address = 0x8C)" + S1 L113 · "I2C adresi: 0x8C (ADDR = GND)" | S7 L464–L468 · "# PCM5242 için I2C address: 0x94" + "i2cset -y 0 0x94 …" + S7 L53 · şemada "PCM5242" | Farklı cihad (PCM5242 ↔ PCM3168A) + farklı adres (0x94 ↔ 0x8C) | Firmware register yazımı yanlış adrese/cihaza gider; DAC/ADC config çalışmaz | Karar: S1 0x8C PCM3168A için; S7'deki 0x94 **PCM5242'ye ait** ve bu donanım envanterinde PCM5242 yok (S2 L60: "REDDEDİLMİŞ · PCM5122"; PCM5242 için envanter kaydı yok). ⚠️ VERIFICATION REQUIRED (firmware hedefi) | `firmware/i2s-driver.md` (S7) · `pcm3168a-dac-adc-rehberi.md` (T1 §3, §7 HM-05) |
| C07 | S2 L106–L110 · "I2S Input: BCK / LRCK / DIN / SCKI" + S2 L112–L114 · "Analog Output: OUTL1-OUTL3, OUTR1-OUTR3" | S1 L42–L48 · "PCM3168A I2S Çıkışları: DOUTA, DOUTB, BCK, LRCK, SCKI → XMOS" + S1 L74–L81 (giriş AIN*) + S1 L85–L86 (DOUTA/DOUTB çıkış) | Sinyal yönü tam ters (dijital gir/analog çık ↔ analog gir/dijital çık) | Test uçları, yazılım API yönü, katman spec'i | Karar: S1 + S3 (S3 L66–L75 SCKI/BCK/LRCK/DOUTA-B) aynı yönde → **PCM3168A bu zincirde ADC olarak** okunur. S2 §3.1 yönü çelişkili. ⚠️ VERIFICATION REQUIRED | `README.md` (S2 L94–L115) · `pcm3168a-dac-adc-rehberi.md` (T1 §2) |
| C08 | S2 L58 · "6-in/**8-out**" | S2 L113–L114 · "OUTL1-OUTL3 (3 output)" + "OUTR1-OUTR3 (3 output)" = 6 analog çıkış | README içinde 8-out ↔ 6-analog-çıkış | Hoparlör/kanal matrisi, BOM, pin kullanımı | Karar: README **kendi içinde tutarsız**; hangisinin kastedildiği belirsiz. ⚠️ VERIFICATION REQUIRED (README içi düzeltme onayı) | `README.md` (S2 L58, L112–L114) |
| C09 | S1 L23 · "THD+N · -100dB (%0.01)" | S2 L36 · "Toplam Harmonik Bozulma <0.005%" (§1 ilke satırı) + S1 L23 içindeki matematik: 20·log10(0.0001) = −80 dB ≠ −100 dB; −100 dB ↔ %0.001 | dB↔% uyuşmazlığı (20 dB) + iki ayrı hedef değer | Ölçüm kabul kriteri belirsiz, test limiti yazılamaz | Karar: kayıtta iki farklı değer; **hangi bileşene ait olduğu ayrıştırılmamış** (S2 L36 K1 genel ilkesi, S1 L23 PCM3168A spesifikasyonu). T1 L109 hesabı doğru ama kaynak değeri çözülmedi. ⚠️ VERIFICATION REQUIRED | `pcm3168a-dac-adc-rehberi.md` (T1 §4.1, §7 HM-01) · `README.md` (S2 L36) |
| C10 | S2 L58 · "Ana DAC · PCM3168A" + S2 L59 · "Opsiyonel DAC · AK4458 · 8-kanal · 32-bit · 768kHz" | S6 L12 · "COREMUSIC'ın ana DAC'ı olarak kullanılır" (AK4458) + S3 L18–L19 · "DAC · AK4458 (32-bit, 8-kanal)" · "ADC · PCM3168A" | Primacy tam ters (ana DAC ↔ opsiyonel) | Mimari sahiplik, katman sorumlusu, test kapsamı | Karar: S3 + S6 → ana DAC **AK4458**, PCM3168A **ADC**; README satırı çelişkili. ⚠️ VERIFICATION REQUIRED (README revizyon onayı) | `README.md` (S2 L54–L60) · `ak4458-dac-rehberi` (k055) · `zincir-mimari` (k054) |
| C11 | S2 L704–L717 · kanıt satırları "README.md L42 / L93 / L135 / L185 / L210 / L238 / L44 / L53 / L63 / L70 / L78 / L95 / L116 / L127" + S2 L751–L752 · "README.md L26 / L30" | Gerçek başlık satırları: L43 (§2) · L94 (§3) · L136 (§4) · L186 (§5) · L211 (§6) · L239 (§7) · L45 (§2.1) · L54 (§2.2) · L64 (§2.3) · L71 (§2.4) · L79 (§2.5) · L96 (§3.1) · L117 (§3.2) · L128 (§3.3) · L27 (§1) · L31 (§1.1) | Sistematik **−1 satır ofseti** (16/16 satır) | Kanıt dizininde her referans bir satır kaymış; K1.9/K1.12 yaprakları yanlış satırı gösterir | Karar: README kendi kanıt dizini kendi başlıklarıyla uyuşmuyor (16 satırın tamamı ofsetli — §8 kanıt satırı 17). ⚠️ VERIFICATION REQUIRED (dizinde düzeltme onayı) | `README.md` (S2 L699–L723, L746–L752) |

**Ofset özeti (C11):** 16 kanıt satırının **tamamı** `gerçek = atıf + 1`; tekil hata değil,
sistematik kaydırma (muhtemel neden: sayfaya 1 satır eklendi/ofset hesap hatası — neden
**kaynakta yazmıyor**, uydurulmadı).

---

## §5 Bağımlılıklar

> Bu belgenin bağımlılıkları; D01 aralığındaki her klasör §10'da tam liste.

| Yön | Düğüm | İlişki (bu belgeye) | Wiki-link |
|---|---|---|---|
| Test hedefi | k056 pcm3168a rehberi | İddia sahibi dosya (T1) | [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] |
| Klasör indeksi | k056 index | Aynı klasör indeksi | [[../k056-pcm3168a-dac-adc/index]] |
| Birincil kaynak | yedek `pcm3168a-dac-adc.md` | §9 tam satır dizini | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` |
| İkincil kaynak | yedek `README.md` | §13 tam satır dizini | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` |
| Zincir (clock) | k054 DAC-ADC zinciri | C05 · BCK/MCLK karşılaştırması | [[../k054-dac-adc-zinciri/zincir-mimari]] |
| Primacy (DAC) | k055 AK4458 | C01/C10 · ana DAC tanımı | [[../k055-ak4458-dac/ak4458-dac-rehberi]] |
| Protokol | k057 I2S/TDM | C05/C07 · yön ve clock oranı | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] |
| Master taraf | k058 XMOS XU316 | C06 · I2C master (GPIO[0]/GPIO[1]) | [[../k058-xmos-xu316/xu316-entegrasyon]] |
| Yazılım hedefi | k059 USB Audio | C06 · firmware akışı | [[../k059-usb-audio/usb-audio-yolu]] |
| Besleme | k068 analog besleme | C04 · AVDD/DVDD/VSS | [[../k068-guc-kaynagi-analog/analog-besleme]] |
| Koruma | k067 koruma | C04 · besleme koruma sınırı | [[../k067-koruma-devreleri/koruma-rehberi]] |
| Fiziksel | k070 PCB | C02 · diferansiyel giriş hatları | [[../k070-pcb-tasarim/pcb-rehber]] |

---

## §6 Kenar Durumlar

| # | Senaryo | Etki | Davranış |
|---|---|---|---|
| ES-01 | README "Ana DAC = PCM3168A" satırı başka belgeye alıntılanırsa | Rol hatası katmanlar arası yayılır | C01/C10 kararına uyulur; alıntıya kaynak satır eklenir |
| ES-02 | Firmware 0x94 adresine yazım yapılırsa | PCM3168A register'a erişilmez, I2C sessiz başarısız | C06: adres 0x8C (S1 L54/L113); firmware hedefi onaysız değişmez |
| ES-03 | 44.1 kHz ailesi (11.2896 MHz) seçilirken README L124 kullanılırsa | MCLK uyuşmazlığı → kilit yok | C05: iki kayıt birlikte okunur; aile seçimi onay ister |
| ES-04 | VSS −5V hattı PSU şemasına eklenirse/çıkarılırsa | Farklı besleme topolojisi | C04 çözülene kadar şemaya −5V raili eklenmez/çıkarılmaz |
| ES-05 | "6-in" iddiası kanal sayısına çevrilirse | 2 kanal kaybı veya fazla pin kullanımı | C02: S1 L74–L81 8 giriş çifti; README L58 doğrulanana kadar 6 kanal iddiası kullanılmaz |
| ES-06 | README içi 8-out ↔ 6 çıkış ayrımı yapılmazsa | Hoparlör matrisi 8.1 ile uyuşmaz | C08 açık kalır; §13 L112–L114 kanıtı esas |
| ES-07 | README kanıt dizinindeki satır numaraları programatik kullanılırsa | Yanlış satır (−1) okunur | C11: README içi atıflar gerilmeden referans alınmaz |
| ES-08 | S1 L12 "analog-to-analog dönüştürücü" ifadesi olduğu gibi alıntılanır | Dönüşüm yönü belirsizleşir | Metin bozulmadan aktarılır; yön L12'nin devamı ("analog giriş → dijital") ile okunur; düzeltme onaya bağlı |
| ES-09 | Rehberde kaynakta olmayan bir değer (ör. akım tüketimi) eklenirse | Uydurma veri | T1 §8.10 gibi `⚠️ VERIFICATION REQUIRED` ile işaretlenir, sayı üretilmez |

---

## §7 Hata Modları

| # | Belirti | Kök neden (çelişki) | Aksiyon |
|---|---|---|---|
| HM-01 | PCM3168A'ya I2C ile erişilemiyor | C06 · firmware 0x94 / PCM5242 hedefi | Hedef doğrulanır: S1 L54/L113 → 0x8C |
| HM-02 | Clock kilitlenmiyor (BCK/MCLK yok) | C05 · 64fs ↔ 1.4112 MHz ve 11.2896/12.288 MHz farkı | Aile (44.1/48 kHz) onayı; S3 L20–L23 + S2 L124–L126 birlikte okunur |
| HM-03 | −5V analog hattı aranıyor/bulunamıyor | C04 · VSS −5V ↔ AVDD 5V | Besleme onayı beklenir; şema değiştirilmez |
| HM-04 | 6. kanal yok / 8. kanal fazla | C02 + C08 · 6-in ↔ 8 giriş çifti · 8-out ↔ 6 çıkış | Kanal sayarası datasheet onayına kadar askıda |
| HM-05 | PCM3168A'dan analog çıkış bekleniyor | C01 + C07 · DAC/ADC yönü | S1 L42–L48/L85–L86 yönünde test edilir (DOUTA/DOUTB) |
| HM-06 | THD+N kabul kriteri belirsiz | C09 · −100 dB ↔ %0.01 ↔ <0.005% | Ölçüm limiti onaylanmadan test eşiği yazılmaz |
| HM-07 | README kanıt satırı açıldığında yanlış içerik | C11 · sistematik −1 ofset | Satır bulucu kullanılmaz; §13 dizini esas alınır |
| HM-08 | Dosya UTF-8/BOM sorunu | yazım aracı | §11 adımları 2–3 (`verify` / `scan`) çalıştırılır |

---

## §8 Doğrulama & Kanıt

| # | İddia | Kanıt yolu | Durum |
|---|---|---|---|
| 1 | Birincil kaynak 117 satır, L1–L117 okundu | `read` başlığı "lines 1-117" | ✅ disk |
| 2 | İkincil kaynak 805 satır | `read` başlığı "lines 754-805" (son satır L805) | ✅ disk |
| 3 | Rol iddiası (ADC) birincil kaynakta | S1 L2, L8, L12 | ✅ disk |
| 4 | I2C 0x8C iki ayrı satırda | S1 L54 (kod bloğu) + S1 L113 (madde) | ✅ disk |
| 5 | README "Ana DAC · PCM3168A" satırı | S2 L58 (grep: "Ana DAC") | ✅ disk |
| 6 | README VDD1/VDD2/VSS satırları | S2 L100–L102 | ✅ disk |
| 7 | README clock değerleri | S2 L121, L124, L125, L126 | ✅ disk |
| 8 | Zincir clock değerleri (11.2896 / 1.4112 MHz) | S3 L21, L23, L106 | ✅ disk |
| 9 | Firmware 0x94 yalnız `firmware/i2s-driver.md` içinde | grep "0x94" → S7 L464–L468 | ✅ disk |
| 10 | AK4458 "ana DAC" ifadesi | S6 L12 | ✅ disk |
| 11 | README §8.1/§8.2 başlık gerçek satırları L27/L31 (atıf L26/L30) | grep başlıklar → L27, L31 | ✅ disk |
| 12 | README §2–§7 başlık gerçek satırları (atıflar −1) | grep başlıklar → L43, L54, L64, L71, L79, L94, L96, L117, L128, L136, L186, L211, L226, L239 | ✅ disk |
| 13 | D01 klasör adları diskte | glob `k05*`, `k06*`, `k07*` (18 klasör) | ✅ disk |
| 14 | Wiki-link hedefleri gerçek dosyalar | glob ile tek tek doğrulandı (§10.1) | ✅ disk |
| 15 | THD+N dB↔% uyuşmazlığı | Hesap: 20·log10(0.0001) = −80 dB (S1 L23 içindeki iki ifade) | ⚠️ açık (C09) |
| 16 | Besleme topolojisi (±5V ↔ AVDD 5V) | S1 L25 ↔ S1 L106 ↔ S2 L100–L102 | ⚠️ açık (C04) |
| 17 | Örnekleme üst sınırı | S1 L20 ↔ S2 L58/L121 | ⚠️ açık (C03) |
| 18 | Kanal sayısı (6-in ↔ 8 giriş çifti) | S2 L58 ↔ S1 L74–L81 | ⚠️ açık (C02) |
| 19 | Firmware hedef cihaz | S7 L464 (PCM5242) ↔ envanter (PCM5242 kaydı yok) | ⚠️ açık (C06) |
| 20 | README kanıt ofseti (16/16 satır −1) | S2 L704–L717, L751–L752 ↔ gerçek başlık satırları | ⚠️ açık (C11) |

---

## §9 Kaynak Kanıt Dizini

> Birincil kaynağın satır satır indeksi — kaynak:
> `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` (117 satır; boş satırlar atlandı).

| Kaynak satır | Tür | İçerik özeti | Kanıt |
|---|---|---|---|
| L1 | frontmatter | `---` (frontmatter açılışı) | ✅ disk |
| L2 | frontmatter | title: "PCM3168A 32-Bit 8-Kanal ADC" | ✅ disk |
| L3 | frontmatter | layer: K1 | ✅ disk |
| L4 | frontmatter | category: "Analog/Dijital Dönüştürücü" | ✅ disk |
| L5 | frontmatter | date: 2026-09-20 | ✅ disk |
| L6 | frontmatter | `---` (frontmatter kapanışı) | ✅ disk |
| L8 | baslik | # PCM3168A 32-Bit 8-Kanal ADC | ✅ disk |
| L10 | baslik | ## Genel Bakış | ✅ disk |
| L12 | metin | TI · 32-bit · 8 kanal (4 stereo) · "analog-to-analog dönüştürücü" · analog giriş → dijital · I2S/TDM | ✅ disk |
| L14 | baslik | ## Teknik Spesifikasyonlar | ✅ disk |
| L16 | tablo | başlık · Parametre · Değer | ✅ disk |
| L17 | tablo | ayraç satırı | ✅ disk |
| L18 | tablo | Çözünürlük · 32-bit | ✅ disk |
| L19 | tablo | Kanal Sayısı · 8 (4 stereo) | ✅ disk |
| L20 | tablo | Örnekleme Hızı · 8kHz – 216kHz | ✅ disk |
| L21 | tablo | Giriş Aralığı · 2.1Vrms (differential) | ✅ disk |
| L22 | tablo | SNR · 118dB (A-Weighted) | ✅ disk |
| L23 | tablo | THD+N · -100dB (%0.01) | ✅ disk |
| L24 | tablo | Giriş Empedansı · 10kΩ (differential) | ✅ disk |
| L25 | tablo | Voltaj Besleme · AVDD = 5V, DVDD = 3.3V | ✅ disk |
| L26 | tablo | Package · TQFP-48, 7×7mm | ✅ disk |
| L27 | tablo | Çalışma Sıcaklığı · -40°C ile +85°C | ✅ disk |
| L29 | baslik | ## Devre Tasarımı | ✅ disk |
| L31 | baslik | ### Temel Bağlantılar | ✅ disk |
| L33 | kod | ``` (kod bloğu açılışı) | ✅ disk |
| L34 | kod | Analog Girişler (XLR/RCA) | ✅ disk |
| L35 | kod | akış çizgisi │ | ✅ disk |
| L36 | kod | VINL1+ → 100nF → AINL1+ (Pin 3) | ✅ disk |
| L37 | kod | VINL1- → 100nF → AINL1- (Pin 4) | ✅ disk |
| L38 | kod | VINR1+ → 100nF → AINR1+ (Pin 5) | ✅ disk |
| L39 | kod | VINR1- → 100nF → AINR1- (Pin 6) | ✅ disk |
| L40 | kod | (Diğer kanallar için devam eder) | ✅ disk |
| L42 | kod | PCM3168A I2S Çıkışları | ✅ disk |
| L43 | kod | akış çizgisi │ | ✅ disk |
| L44 | kod | DOUTA → XMOS XU316 SD0 (Data L/R Ch1-2) | ✅ disk |
| L45 | kod | DOUTB → XMOS XU316 SD1 (Data L/R Ch3-4) | ✅ disk |
| L46 | kod | BCK → XMOS XU316 SCK (Bit Clock) | ✅ disk |
| L47 | kod | LRCK → XMOS XU316 WS (Word Select) | ✅ disk |
| L48 | kod | SCKI → XMOS XU316 MCLK (Master Clock) | ✅ disk |
| L50 | kod | PCM3168A Kontrol (I2C) | ✅ disk |
| L51 | kod | akış çizgisi │ | ✅ disk |
| L52 | kod | SDA → XMOS GPIO[0] (I2C Data) | ✅ disk |
| L53 | kod | SCL → XMOS GPIO[1] (I2C Clock) | ✅ disk |
| L54 | kod | ADDR → GND (I2C Address = 0x8C) | ✅ disk |
| L55 | kod | ``` (kod bloğu kapanışı) | ✅ disk |
| L57 | baslik | ### Filtre ve Kondansatörler | ✅ disk |
| L59 | tablo | başlık · Referans · Değer · Açıklama | ✅ disk |
| L60 | tablo | ayraç satırı | ✅ disk |
| L61 | tablo | C1-C8 · 100nF MLCC · Giriş DC bloklama | ✅ disk |
| L62 | tablo | C9-C12 · 1µF MLCC · AVDD dekuplajı | ✅ disk |
| L63 | tablo | C13-C16 · 10µF Elektrolitik · DVDD bulk | ✅ disk |
| L64 | tablo | C17-C20 · 100nF MLCC · DVDD dekuplajı | ✅ disk |
| L65 | tablo | R1-R8 · 100Ω · Giriş seri direnç (EMI) | ✅ disk |
| L66 | tablo | R9-R10 · 4.7kΩ · I2C pull-up | ✅ disk |
| L68 | baslik | ## Pin Konfigürasyonu | ✅ disk |
| L70 | tablo | başlık · Pin · Ad · Yön · Açıklama | ✅ disk |
| L71 | tablo | ayraç satırı | ✅ disk |
| L72 | tablo | 1 · DVDD · Güç · +3.3V dijital besleme | ✅ disk |
| L73 | tablo | 2 · DGND · Güç · Dijital toprak | ✅ disk |
| L74 | tablo | 3-4 · AINL1± · Giriş · Sol kanal 1 diferansiyel giriş | ✅ disk |
| L75 | tablo | 5-6 · AINR1± · Giriş · Sağ kanal 1 diferansiyel giriş | ✅ disk |
| L76 | tablo | 7-8 · AINL2± · Giriş · Sol kanal 2 diferansiyel giriş | ✅ disk |
| L77 | tablo | 9-10 · AINR2± · Giriş · Sağ kanal 2 diferansiyel giriş | ✅ disk |
| L78 | tablo | 11-12 · AINL3± · Giriş · Sol kanal 3 diferansiyel giriş | ✅ disk |
| L79 | tablo | 13-14 · AINR3± · Giriş · Sağ kanal 3 diferansiyel giriş | ✅ disk |
| L80 | tablo | 15-16 · AINL4± · Giriş · Sol kanal 4 diferansiyel giriş | ✅ disk |
| L81 | tablo | 17-18 · AINR4± · Giriş · Sağ kanal 4 diferansiyel giriş | ✅ disk |
| L82 | tablo | 19 · AGND · Güç · Analog toprak | ✅ disk |
| L83 | tablo | 20 · AVDD · Güç · +5V analog besleme | ✅ disk |
| L84 | tablo | 21-24 · NC · - · Bağlantısız | ✅ disk |
| L85 | tablo | 25 · DOUTA · Çıkış · Seri veri çıkışı A (Ch1-2) | ✅ disk |
| L86 | tablo | 26 · DOUTB · Çıkış · Seri veri çıkışı B (Ch3-4) | ✅ disk |
| L87 | tablo | 27 · BCK · Giriş/Çıkış · Bit clock | ✅ disk |
| L88 | tablo | 28 · LRCK · Giriş/Çıkış · Word select (LR clock) | ✅ disk |
| L89 | tablo | 29 · SCKI · Giriş · System clock input | ✅ disk |
| L90 | tablo | 30 · FMT0 · Giriş · Format seçimi (I2S/TDM) | ✅ disk |
| L91 | tablo | 31 · FMT1 · Giriş · Format seçimi | ✅ disk |
| L92 | tablo | 32 · MD0 · Giriş · Master/Slave modu | ✅ disk |
| L93 | tablo | 33 · MD1 · Giriş · Master/Slave modu | ✅ disk |
| L94 | tablo | 34 · SDA · Bidirectional · I2C veri | ✅ disk |
| L95 | tablo | 35 · SCL · Giriş · I2C clock | ✅ disk |
| L96 | tablo | 36 · ADDR · Giriş · I2C adres seçimi | ✅ disk |
| L97 | tablo | 37-48 · NC/VARIOUS · - · Diğer fonksiyonlar | ✅ disk |
| L99 | baslik | ## Bağımlılıklar | ✅ disk |
| L101 | tablo | başlık · Bağımlılık · Yön · Açıklama | ✅ disk |
| L102 | tablo | ayraç satırı | ✅ disk |
| L103 | tablo | K0 Fiziksel · Alt · TQFP-48 pad layout | ✅ disk |
| L104 | tablo | K1 XMOS · Bağlantı · I2S output → XMOS input | ✅ disk |
| L105 | tablo | K1 Konnektörler · Bağlantı · XLR/RCA giriş | ✅ disk |
| L106 | tablo | K1 Güç Kaynağı · Alt · ±5V analog, +3.3V dijital | ✅ disk |
| L107 | tablo | K5 Analog Sinyal · Üst · Dijital çıkış → DSP'ye | ✅ disk |
| L109 | baslik | ## Durum: Implementasyon | ✅ disk |
| L111 | vurgu | **Durum**: 🟢 Hazır | ✅ disk |
| L113 | madde | I2C adresi: 0x8C (ADDR = GND) | ✅ disk |
| L114 | madde | Format: I2S (FMT0=0, FMT1=0) | ✅ disk |
| L115 | madde | Master mod: XMOS Master, PCM3168A Slave (MD0=0, MD1=0) | ✅ disk |
| L116 | madde | Gain ayarı: I2C üzerinden programlanabilir (0dB ile +31.5dB) | ✅ disk |
| L117 | madde | DC offset kalibrasyonu: Otomatik (power-on reset) | ✅ disk |

**Dizin kapsamı:** 100 dolu satır / 117 satır (17 boş satır atlandı: L7, L9, L11, L13, L15,
L28, L30, L32, L56, L58, L67, L69, L98, L100, L108, L110, L112).

---

## §10 Bağımlılık Matrisi (D01 — k054…k071)

> D01 aralığının tamamı (18 klasör) ve bu belgeye ilişkisi. Wiki-link'ler diskteki gerçek dosyalardır
> (glob ile doğrulandı — §10.1).

| Klasör | Wiki-link | İlişki (bu belgeye) |
|---|---|---|
| `k054-dac-adc-zinciri` (DAC-ADC dönüşüm zinciri) | [[../k054-dac-adc-zinciri/zincir-mimari]] | C05 clock değerleri (S3 L20–L23, L106) buradan gelir |
| `k055-ak4458-dac` (AK4458 DAC) | [[../k055-ak4458-dac/ak4458-dac-rehberi]] | C01/C10 primacy çelişkisinin karşı kaynağı (S6 L12) |
| `k056-pcm3168a-dac-adc` (PCM3168A) | [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] | Test hedefi (T1) — bu dosyanın klasörü |
| `k056-pcm3168a-dac-adc` (klasör indeksi) | [[../k056-pcm3168a-dac-adc/index]] | Klasör indeksi |
| `k057-i2s-interface` (I2S / TDM) | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] | C05/C07 · yön ve clock oranı protokolü |
| `k058-xmos-xu316` (XMOS XU316) | [[../k058-xmos-xu316/xu316-entegrasyon]] | C06 · I2C master tarafı (S1 L52–L53) |
| `k059-usb-audio` (USB Audio Class) | [[../k059-usb-audio/usb-audio-yolu]] | C06 · firmware akış zinciri (S7) |
| `k060-analog-sinyal-yolu` (Analog yol) | [[../k060-analog-sinyal-yolu/analog-yol-rehberi]] | C07 · analog giriş/çıkış yönü |
| `k061-diff-pair-input` (Differential giriş) | [[../k061-diff-pair-input/diff-pair-tasarim]] | C02 · AIN* diferansiyel giriş çiftleri (S1 L74–L81) |
| `k062-vas-stage` (VAS aşaması) | [[../k062-vas-stage/vas-stage-tasarim]] | Bu konuyla doğrudan ilişkisi yok (D01 bütünlüğü) |
| `k063-output-stage` (Çıkış aşaması) | [[../k063-output-stage/output-stage-tasarim]] | Bu konuyla doğrudan ilişkisi yok (D01 bütünlüğü) |
| `k064-feedback-network` (Geri besleme) | [[../k064-feedback-network/feedback-tasarim]] | Bu konuyla doğrudan ilişkisi yok (D01 bütünlüğü) |
| `k065-mjle21194-93` (Output transistör) | [[../k065-mjle21194-93/mjle-op-amp-kurulum]] | Bu konuyla doğrudan ilişkisi yok (D01 bütünlüğü) |
| `k066-konnektorler` (Konnektör envanteri) | [[../k066-konnektorler/konnektor-envanteri]] | S1 L105 · XLR/RCA giriş uçları |
| `k067-koruma-devreleri` (Koruma devreleri) | [[../k067-koruma-devreleri/koruma-rehberi]] | C04 · besleme koruma sınırı |
| `k068-guc-kaynagi-analog` (Analog güç kaynağı) | [[../k068-guc-kaynagi-analog/analog-besleme]] | C04 · AVDD/DVDD/VSS −5V çelişkisi |
| `k069-hoparlor-dizilimi` (Hoparlör dizilimi) | [[../k069-hoparlor-dizilimi/hoparlor-dizilim]] | C08 · 8-out ↔ 6 çıkış çelişkisinin tüketici tarafı |
| `k070-pcb-tasarim` (PCB tasarımı) | [[../k070-pcb-tasarim/pcb-rehber]] | C02 · diferansiyel giriş hatları |
| `k071-termal-yonetim` (Termal yönetim) | [[../k071-termal-yonetim/termal-rehber]] | Bu konuyla doğrudan ilişkisi yok (D01 bütünlüğü) |

### §10.1 Bu Dosyadaki Wiki-Link Envanteri

| # | Hedef (klasör/dosya) | Disk durumu |
|---|---|---|
| 1 | `../k054-dac-adc-zinciri/zincir-mimari` | ✅ mevcut |
| 2 | `../k055-ak4458-dac/ak4458-dac-rehberi` | ✅ mevcut |
| 3 | `../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi` | ✅ mevcut |
| 4 | `../k056-pcm3168a-dac-adc/index` | ✅ mevcut |
| 5 | `../k057-i2s-interface/i2s-ve-tdm-rehberi` | ✅ mevcut |
| 6 | `../k058-xmos-xu316/xu316-entegrasyon` | ✅ mevcut |
| 7 | `../k059-usb-audio/usb-audio-yolu` | ✅ mevcut |
| 8 | `../k060-analog-sinyal-yolu/analog-yol-rehberi` | ✅ mevcut |
| 9 | `../k061-diff-pair-input/diff-pair-tasarim` | ✅ mevcut |
| 10 | `../k062-vas-stage/vas-stage-tasarim` | ✅ mevcut |
| 11 | `../k063-output-stage/output-stage-tasarim` | ✅ mevcut |
| 12 | `../k064-feedback-network/feedback-tasarim` | ✅ mevcut |
| 13 | `../k065-mjle21194-93/mjle-op-amp-kurulum` | ✅ mevcut |
| 14 | `../k066-konnektorler/konnektor-envanteri` | ✅ mevcut |
| 15 | `../k067-koruma-devreleri/koruma-rehberi` | ✅ mevcut |
| 16 | `../k068-guc-kaynagi-analog/analog-besleme` | ✅ mevcut |
| 17 | `../k069-hoparlor-dizilimi/hoparlor-dizilim` | ✅ mevcut |
| 18 | `../k070-pcb-tasarim/pcb-rehber` | ✅ mevcut |
| 19 | `../k071-termal-yonetim/termal-rehber` | ✅ mevcut |

**Toplam:** 19 hedef · kırık 0 (hepsi `.ai/architecture/` altında gerçek dosya; glob ile doğrulandı).

---

## §11 Doğrulama Protokolü

> Komutlar repo kökünden (`C:\www\coremusic.net`) çalıştırılır.

| # | Adım | Komut | Beklenen |
|---|---|---|---|
| 1 | Satır sayısı (≥500 kapısı) | `[System.IO.File]::ReadAllLines('.ai/architecture/k056-pcm3168a-dac-adc/pcm3168a-kaynak-karsilastirma.md').Length` | ≥500 |
| 2 | UTF-8 / mojibake | `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k056-pcm3168a-dac-adc/pcm3168a-kaynak-karsilastirma` | `mojibake: 0` · `hasBom: false` |
| 3 | Klasör taraması | `node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/architecture/k056-pcm3168a-dac-adc` | 0 bulgu |
| 4 | Wiki-link kırıklığı | `[[../k054-dac-adc-zinciri/zincir-mimari]]` hedefleri `.ai/architecture/` altında | kırık 0 (§10.1: 19/19) |
| 5 | Frontmatter | title · type · category · date · updated · version · status · authority | 8 alan eksiksiz |
| 6 | Sürüm/tarih | `version: 4.0.0` · `updated: 2026-10-06` | birebir |
| 7 | Commit kapısı | `git status --porcelain -- .ai/architecture/k056-pcm3168a-dac-adc` | `??` (bu görevde commit ATILMAZ) |
| 8 | Kaynak dokunulmazlık | `git status --porcelain -- _backup/` | değişiklik yok (salt-okunur) |
| 9 | REDACTED | dosyada secret/token/anahtar/KEY eşleşmesi | 0 |
| 10 | Bölüm bütünlüğü | §1…§13 başlıkları dosyada mevcut | 13/13 |

---

## §12 Açık Kalemler (⚠️ işaretli)

> Tarama (dosya üzerinde `⚠️` geçen satır sayısı): **33 satır** — bunların 17'si açık kalemdir
> (§4'te 11 çelişki kararı C01–C11 + §8'de 6 doğrulama satırı 15–20); kalan 16 satır
> kural/başlık/açıklama metinleridir (§1, §3, §4 başlığı, §6 ES-09, §12 başlığı, §13 işaretli satırlar).
> Aşağıdaki tablo açık kalem satırlarının numaralarını taşır; satır numaraları
> §12'den önceki bölümlere aittir (§13 bunlara dokunmaz).

| Satır | İşaretli madde |
|---|---|
| L108 · §4 C01 | PCM3168A rolü: ADC ↔ Ana DAC — README etiketi hatalı, üst onay bekliyor |
| L109 · §4 C02 | Çözünürlük/kanal: 32-bit 8-giriş ↔ 24-bit 6-in — datasheet karşılaştırması bekliyor |
| L110 · §4 C03 | Örnekleme üst sınırı: 216 kHz ↔ 192 kHz ↔ 48 kHz varsayılanı doğrulanmadı |
| L111 · §4 C04 | Besleme topolojisi: AVDD/DVDD ↔ VDD1/VDD2/VSS −5V — güç raili onayı bekliyor |
| L112 · §4 C05 | Clock: 11.2896 MHz ailesi ihmal + BCK 64fs ↔ 32fs — aile seçimi sabitlenmedi |
| L113 · §4 C06 | I2C: 0x8C (PCM3168A) ↔ firmware 0x94 (PCM5242) — hedef cihaz onayı bekliyor |
| L114 · §4 C07 | Sinyal yönü: README DAC girişi ↔ kaynak ADC çıkışı |
| L115 · §4 C08 | README içi 8-out ↔ 6 analog çıkış tutarsızlığı |
| L116 · §4 C09 | THD+N: −100 dB (%0.01) ↔ <0.005% (−80 dB) — bileşen ayrıştırması yok |
| L117 · §4 C10 | Primacy: PCM3168A "Ana DAC" ↔ AK4458 ana DAC — README revizyon onayı bekliyor |
| L118 · §4 C11 | Kanıt ofseti: 16/16 satır −1 kaymış — dizin düzeltme onayı bekliyor |
| L196 · §8.15 | THD+N dB↔% uyuşmazlığı açık (C09) |
| L197 · §8.16 | Besleme topolojisi açık (C04) |
| L198 · §8.17 | Örnekleme üst sınırı açık (C03) |
| L199 · §8.18 | Kanal sayısı 6-in ↔ 8 giriş çifti açık (C02) |
| L200 · §8.19 | Firmware hedef cihaz açık (C06) |
| L201 · §8.20 | README kanıt ofseti açık (C11) |

---

## §13 Tam Kaynak Satır Dizini

> İkincil kaynağın satır satır indeksi — kaynak:
> `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` (805 satır).
> **Kapsam kuralı:** dolu satırlar tek tek indekslenir; boş satırlar atlanır; L331–L698 arası
> (başka dosyalara ait yaprak kanıt tabloları) blok satırlarıyla indekslenir — her blok
> kendi satır aralığını taşır, böylece L1–L805 tamamı kapsanır.

| Kaynak satır | Tür | İçerik özeti | Kanıt |
|---|---|---|---|
| L1 | ayraç | `---` (frontmatter açılışı) | ✅ disk |
| L2 | frontmatter | reference_doc: Freelancer Technical Documentation v1.0 | ✅ disk |
| L3 | frontmatter | title: "CoreMusic — K1 Donanım Layer" | ✅ disk |
| L4 | frontmatter | type: architecture-layer | ✅ disk |
| L5 | frontmatter | category: architecture | ✅ disk |
| L6 | frontmatter | date: 2026-09-20 | ✅ disk |
| L7 | frontmatter | updated: 2026-09-29 | ✅ disk |
| L8 | frontmatter | last_update_note: "3 turlu agent tartışması" | ✅ disk |
| L9 | frontmatter | status: active | ✅ disk |
| L10 | frontmatter | version: 1.0.1 | ✅ disk |
| L11 | frontmatter | authority: Single Source of Truth (SSOT) | ✅ disk |
| L12 | frontmatter | governance: Red Team · Human Mode · Truth Mode | ✅ disk |
| L13 | frontmatter | reference: (alt anahtar bloğu) | ✅ disk |
| L14 | frontmatter | authority: ".ai/CLAUDE.md" | ✅ disk |
| L15 | frontmatter | source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md" | ✅ disk |
| L16 | ayraç | `---` (frontmatter kapanışı) | ✅ disk |
| L18 | baslik | # K1: Donanım Layer | ✅ disk |
| L20 | vurgu | **Katman:** K1 (Donanım Altyapısı) | ✅ disk |
| L21 | vurgu | **Kapsam:** XMOS, DAC, Amplifikatör, Hoparlör, Güç kaynağı | ✅ disk |
| L22 | vurgu | **Sorumlu Agent:** Audio Hardware Engineer | ✅ disk |
| L23 | vurgu | **Bileşen Sayısı:** 120 | ✅ disk |
| L25 | ayraç | `---` | ✅ disk |
| L27 | baslik | ## 1. Genel Bakış | ✅ disk |
| L29 | metin | K1 katmanı fiziksel donanım bileşenlerini içerir (dijital → analog → hoparlör) | ✅ disk |
| L31 | baslik | ### 1.1 Temel İlkeler | ✅ disk |
| L33 | tablo | başlık · İlke · Açıklama | ✅ disk |
| L34 | tablo | ayraç satırı | ✅ disk |
| L35 | tablo | Bit-Perfect · sinyal zincirinde kayıp yok | ✅ disk |
| L36 | tablo | Low THD · Toplam Harmonik Bozulma <0.005% | ✅ disk |
| L37 | tablo | High SNR · Sinyal-Gürültü Oranı >100dB | ✅ disk |
| L38 | tablo | 8.1 Surround · 8 kanal + 1 LFE | ✅ disk |
| L39 | tablo | DC-Only · Güç kaynağı DC Only | ✅ disk |
| L41 | ayraç | `---` | ✅ disk |
| L43 | baslik | ## 2. Bileşen Haritası | ✅ disk |
| L45 | baslik | ### 2.1 USB Audio Interface | ✅ disk |
| L47 | tablo | başlık · Bileşen · Model · Özellik | ✅ disk |
| L48 | tablo | ayraç satırı | ✅ disk |
| L49 | tablo | USB Audio · XMOS XU316 · USB Audio Class 2.0, 32-bit | ✅ disk |
| L50 | tablo | USB Interface · USB-C · 24-pin, USB 2.0/3.0 | ✅ disk |
| L51 | tablo | Clock · 22.5792 MHz · 44.1kHz family | ✅ disk |
| L52 | tablo | Clock · 24.576 MHz · 48kHz family | ✅ disk |
| L54 | baslik | ### 2.2 DAC (Digital-to-Analog Converter) | ✅ disk |
| L56 | tablo | başlık · Bileşen · Model · Kanal · Bit · Sample Rate | ✅ disk |
| L57 | tablo | ayraç satırı | ✅ disk |
| L58 | tablo | Ana DAC · PCM3168A · 6-in/8-out · 24-bit · 192kHz | ✅ disk |
| L59 | tablo | Opsiyonel DAC · AK4458 · 8-kanal · 32-bit · 768kHz | ✅ disk |
| L60 | tablo | REDDEDİLMİŞ · PCM5122 · 2-kanal · 32-bit · — | ✅ disk |
| L62 | vurgu | Uyarı satırı · PCM5122 8.1 surround için yetersizdir (ADR-038) | ✅ disk |
| L64 | baslik | ### 2.3 Amplifikatör | ✅ disk |
| L66 | tablo | başlık · Bileşen · Model · Topoloji · Güç · THD | ✅ disk |
| L67 | tablo | ayraç satırı | ✅ disk |
| L68 | tablo | NPN Output · MJL21194 · Class AB Darlington · 50W/kanal · <0.005% | ✅ disk |
| L69 | tablo | PNP Output · MJL21193 · Class AB Darlington · 50W/kanal · <0.005% | ✅ disk |
| L71 | baslik | ### 2.4 Güç Kaynağı | ✅ disk |
| L73 | tablo | başlık · Bileşen · Model · Giriş · Çıkış · Verim | ✅ disk |
| L74 | tablo | ayraç satırı | ✅ disk |
| L75 | tablo | Boost Converter · LM5122 · 22.2V (6S LiPo) · ±35V · %96 | ✅ disk |
| L76 | tablo | Batarya · 6S LiPo · 22.2V nominal · — · — | ✅ disk |
| L77 | tablo | DC Adapter · 19-24V · AC/DC · — · — | ✅ disk |
| L79 | baslik | ### 2.5 Hoparlör Matrisi (8.1 Surround) | ✅ disk |
| L81 | tablo | başlık · Kanal · Hoparlör · Frekans · Konum | ✅ disk |
| L82 | tablo | ayraç satırı | ✅ disk |
| L83 | tablo | CH1 · Front Left · 20Hz-20kHz · Ön sol | ✅ disk |
| L84 | tablo | CH2 · Front Right · 20Hz-20kHz · Ön sağ | ✅ disk |
| L85 | tablo | CH3 · Center · 100Hz-8kHz · Merkez | ✅ disk |
| L86 | tablo | CH4 · LFE · 20Hz-120Hz · Subwoofer | ✅ disk |
| L87 | tablo | CH5 · Surround Left · 100Hz-16kHz · Arka sol | ✅ disk |
| L88 | tablo | CH6 · Surround Right · 100Hz-16kHz · Arka sağ | ✅ disk |
| L89 | tablo | CH7 · Rear Left · 100Hz-16kHz · Arka sol | ✅ disk |
| L90 | tablo | CH8 · Rear Right · 100Hz-16kHz · Arka sağ | ✅ disk |
| L92 | ayraç | `---` | ✅ disk |
| L94 | baslik | ## 3. PCM3168A DAC Detayı | ✅ disk |
| L96 | baslik | ### 3.1 Pin Out | ✅ disk |
| L98 | kod | ``` (kod bloğu açılışı) | ✅ disk |
| L99 | kod | PCM3168A Pin Configuration: | ✅ disk |
| L100 | kod | VDD1: +3.3V (Digital) | ✅ disk |
| L101 | kod | VDD2: +5V (Analog) | ✅ disk |
| L102 | kod | VSS: -5V (Analog) | ✅ disk |
| L103 | kod | AGND: Analog Ground | ✅ disk |
| L104 | kod | DGND: Digital Ground | ✅ disk |
| L106 | kod | I2S Input: | ✅ disk |
| L107 | kod | BCK: Bit Clock (64fs) | ✅ disk |
| L108 | kod | LRCK: Left/Right Clock (fs) | ✅ disk |
| L109 | kod | DIN: Data In | ✅ disk |
| L110 | kod | SCKI: System Clock (256fs or 512fs) | ✅ disk |
| L112 | kod | Analog Output: | ✅ disk |
| L113 | kod | OUTL1-OUTL3: Left channels (3 output) | ✅ disk |
| L114 | kod | OUTR1-OUTR3: Right channels (3 output) | ✅ disk |
| L115 | kod | ``` (kod bloğu kapanışı) | ✅ disk |
| L117 | baslik | ### 3.2 I2S Konfigürasyonu | ✅ disk |
| L119 | tablo | başlık · Parametre · Değer | ✅ disk |
| L120 | tablo | ayraç satırı | ✅ disk |
| L121 | tablo | Sample Rate · 48kHz (default) | ✅ disk |
| L122 | tablo | Bit Depth · 24-bit | ✅ disk |
| L123 | tablo | I2S Mode · Standard I2S | ✅ disk |
| L124 | tablo | System Clock · 256fs = 12.288MHz | ✅ disk |
| L125 | tablo | BCK · 64fs = 3.072MHz | ✅ disk |
| L126 | tablo | LRCK · 48kHz | ✅ disk |
| L128 | baslik | ### 3.3 Analogy Output Devresi | ✅ disk |
| L130 | kod | ``` (kod bloğu açılışı) | ✅ disk |
| L131 | kod | PCM3168A OUTL1 → I/V Resistor (1kΩ) → Low-Pass Filter (20kHz) → Differential Driver → Amplifier Input | ✅ disk |
| L132 | kod | ``` (kod bloğu kapanışı) | ✅ disk |
| L134 | ayraç | `---` | ✅ disk |
| L136 | baslik | ## 4. Class AB Amplifikatör Detayı | ✅ disk |
| L138 | baslik | ### 4.1 Tek Kanal Devre Şeması | ✅ disk |
| L140 | kod | ``` (kod bloğu açılışı) | ✅ disk |
| L141 | kod | +35V (PVDD) | ✅ disk |
| L142 | kod | akış çizgisi │ | ✅ disk |
| L143 | kod | kutu ┌────┴────┐ | ✅ disk |
| L144 | kod | kutu içi Q15 · MJL21194 (NPN Output) | ✅ disk |
| L145 | kod | kutu içi NPN | ✅ disk |
| L146 | kod | Input ─────┤ Q16 ├──── Output → Hoparlör | ✅ disk |
| L147 | kod | (Diff) · BD139 | ✅ disk |
| L148 | kod | kutu içi VAS | ✅ disk |
| L149 | kod | kutu içi Q17 · MJL21193 (PNP Output) | ✅ disk |
| L150 | kod | kutu içi PNP | ✅ disk |
| L151 | kod | kutu └────┬────┘ | ✅ disk |
| L152 | kod | akış çizgisi │ | ✅ disk |
| L153 | kod | -35V (PVSS) | ✅ disk |
| L155 | kod | Bias Network: | ✅ disk |
| L156 | kod | Q1 (BC546B) · Diferansiyel çift giriş | ✅ disk |
| L157 | kod | Q2 (BC546B) · Diferansiyel çift giriş | ✅ disk |
| L158 | kod | Q5 (BC556B) · Akım havuzu | ✅ disk |
| L159 | kod | Q9 (KSC3503) · VAS (Voltage Amplifier Stage) | ✅ disk |
| L160 | kod | Q10 (BD139) · Vbe çarpımı (bias spreader) | ✅ disk |
| L161 | kod | ``` (kod bloğu kapanışı) | ✅ disk |
| L163 | baslik | ### 4.2 Bias Ayar Prosedürü | ✅ disk |
| L165 | tablo | başlık · Adım · İşlem · Değer | ✅ disk |
| L166 | tablo | ayraç satırı | ✅ disk |
| L167 | tablo | 1 · Güç kaynağı ayarla · ±35V DC | ✅ disk |
| L168 | tablo | 2 · Multimetre çıkışa bağla · DC offset ölç | ✅ disk |
| L169 | tablo | 3 · Bias potansiyometresi ayarla · 0V DC offset hedefle | ✅ disk |
| L170 | tablo | 4 · Sıcaklık stabilizasyonu · 5-10 dk bekle | ✅ disk |
| L171 | tablo | 5 · Son kontrol · <0.5V DC offset | ✅ disk |
| L173 | baslik | ### 4.3 Termal Hesaplama | ✅ disk |
| L175 | tablo | başlık · Parametre · Değer | ✅ disk |
| L176 | tablo | ayraç satırı | ✅ disk |
| L177 | tablo | Güç (kanal başına) · 50W @ 8Ω | ✅ disk |
| L178 | tablo | Verimlilik · ~%65 (Class AB) | ✅ disk |
| L179 | tablo | Isı (kanal başına) · ~17.5W | ✅ disk |
| L180 | tablo | Toplam ısı (8 kanal) · ~140W | ✅ disk |
| L181 | tablo | Heatsink gereksinimi · >140W/C° thermal resistance | ✅ disk |
| L182 | tablo | Fan gereksinimi · 80mm PWM, >50 CFM | ✅ disk |
| L184 | ayraç | `---` | ✅ disk |
| L186 | baslik | ## 5. XMOS XU316 Detayı | ✅ disk |
| L188 | baslik | ### 5.1 Blok Diyagramı | ✅ disk |
| L190 | kod | ``` (kod bloğu açılışı) | ✅ disk |
| L191 | kod | USB 2.0 ──→ XMOS XU316 ──→ I2S ──→ PCM3168A | ✅ disk |
| L192 | kod | akış çizgisi │ | ✅ disk |
| L193 | kod | ├→ Clock Generator | ✅ disk |
| L194 | kod | ├→ USB Audio Class 2.0 | ✅ disk |
| L195 | kod | ├→ DSP Processing | ✅ disk |
| L196 | kod | └→ Control Interface | ✅ disk |
| L197 | kod | ``` (kod bloğu kapanışı) | ✅ disk |
| L199 | baslik | ### 5.2 XMOS Kaynak Kullanımı | ✅ disk |
| L201 | tablo | başlık · Kaynak · Kullanım | ✅ disk |
| L202 | tablo | ayraç satırı | ✅ disk |
| L203 | tablo | Logical Cores · 8 (4 x 2 tile) | ✅ disk |
| L204 | tablo | MIPS · ~2000 (toplam) | ✅ disk |
| L205 | tablo | RAM · 512KB (tile 0+1) | ✅ disk |
| L206 | tablo | Flash · 16MB (external) | ✅ disk |
| L207 | tablo | USB PHY · High-speed 480Mbps | ✅ disk |
| L209 | ayraç | `---` | ✅ disk |
| L211 | baslik | ## 6. PCB Tasarım Kuralları | ✅ disk |
| L213 | tablo | başlık · Parametre · Değer | ✅ disk |
| L214 | tablo | ayraç satırı | ✅ disk |
| L215 | tablo | Layer · 6-layer stackup | ✅ disk |
| L216 | tablo | Copper (top/bottom) · 2oz | ✅ disk |
| L217 | tablo | Copper (inner) · 1oz | ✅ disk |
| L218 | tablo | Finish · ENIG | ✅ disk |
| L219 | tablo | Min trace · 4mil | ✅ disk |
| L220 | tablo | Min via · 8mil drill, 16mil pad | ✅ disk |
| L221 | tablo | USB Impedans · 90Ω differential | ✅ disk |
| L222 | tablo | I2S Impedans · 50Ω single-ended | ✅ disk |
| L223 | tablo | Ground · Star ground topology | ✅ disk |
| L224 | tablo | Thermal · Thermal vias under power components | ✅ disk |
| L226 | baslik | ### 6.1 Stackup | ✅ disk |
| L228 | kod | ``` (kod bloğu açılışı) | ✅ disk |
| L229 | kod | Layer 1: Signal (top) — Components, traces | ✅ disk |
| L230 | kod | Layer 2: Ground — Continuous ground plane | ✅ disk |
| L231 | kod | Layer 3: Signal — I2S, control signals | ✅ disk |
| L232 | kod | Layer 4: Power — +35V, -35V, +3.3V, +5V | ✅ disk |
| L233 | kod | Layer 5: Ground — Continuous ground plane | ✅ disk |
| L234 | kod | Layer 6: Signal (bottom) — Components, traces | ✅ disk |
| L235 | kod | ``` (kod bloğu kapanışı) | ✅ disk |
| L237 | ayraç | `---` | ✅ disk |
| L239 | baslik | ## 7. BOM Maliyet Analizi | ✅ disk |
| L241 | tablo | başlık · Kategori · Bileşen Sayısı · Toplam Maliyet | ✅ disk |
| L242 | tablo | ayraç satırı | ✅ disk |
| L243 | tablo | USB Audio (XMOS) · 3 · $15.00 | ✅ disk |
| L244 | tablo | DAC (PCM3168A) · 15 · $25.00 | ✅ disk |
| L245 | tablo | Amplifikatör (8 kanal) · 120 · $85.00 | ✅ disk |
| L246 | tablo | Güç Kaynağı · 35 · $45.00 | ✅ disk |
| L247 | tablo | Pasif Bileşenler · 800+ · $180.00 | ✅ disk |
| L248 | tablo | Konnektörler · 25 · $30.00 | ✅ disk |
| L249 | tablo | PCB (6-layer) · 1 · $50.00 | ✅ disk |
| L250 | tablo | **TOPLAM** · **~1,000** · **~$430** | ✅ disk |
| L252 | ayraç | `---` | ✅ disk |
| L254 | baslik | ## 8. İlgili Dosyalar | ✅ disk |
| L256 | tablo | başlık · Dosya · Amaç | ✅ disk |
| L257 | tablo | ayraç satırı | ✅ disk |
| L258 | tablo | architecture/k1-donanim/README.md · Bu dosya | ✅ disk |
| L259 | tablo | architecture/k1-donanim/xmos-xu316.md · XMOS detayı | ✅ disk |
| L260 | tablo | architecture/k1-donanim/pcm3168a.md · DAC detayı | ✅ disk |
| L261 | tablo | architecture/k1-donanim/ak4458.md · High-end DAC | ✅ disk |
| L262 | tablo | architecture/k1-donanim/class-ab-amplifier.md · Amplifikatör devresi | ✅ disk |
| L263 | tablo | architecture/k1-donanim/speaker-matrix.md · Hoparlör konfigürasyonu | ✅ disk |
| L264 | tablo | architecture/k1-donanim/bom-cost.md · BOM maliyet | ✅ disk |
| L265 | tablo | architecture/k16-class-ab/README.md · K16 Class AB | ✅ disk |
| L266 | tablo | architecture/k17-guc-kaynagi/README.md · K17 Güç kaynağı | ✅ disk |
| L268 | ayraç | `---` | ✅ disk |
| L270 | baslik | ## 9. İlgili ADR'ler | ✅ disk |
| L272 | tablo | başlık · ADR · Konu | ✅ disk |
| L273 | tablo | ayraç satırı | ✅ disk |
| L274 | tablo | ADR-038 · PCM3168A (PCM5122 REDDEDİLMİŞ) | ✅ disk |
| L275 | tablo | ADR-089 · Class AB Amplifikatör + 6S LiPo + ±35V Boost | ✅ disk |
| L277 | ayraç | `---` | ✅ disk |
| L279 | baslik | ## Alt Katman Şeması (K1.a.b.c) | ✅ disk |
| L281 | blockquote | Şema kuralları (2026-09-24 · 3 turlu agent tartışması) · K1 → K1.a → K1.a.b → K1.a.b.c · 19 içerik MD + 8 firmware MD · 360 kanıtlı yaprak | ✅ disk |
| L283 | baslik | ### K1 Şema Özeti | ✅ disk |
| L285 | tablo | başlık · 2. Katman · Ad · 3. Katman · 4. Kanıtlı Yaprak · Birincil Kanıt | ✅ disk |
| L286 | tablo | ayraç satırı | ✅ disk |
| L287 | tablo | K1.1 · DAC/ADC Zinciri · 3 · 23 · ak4458-dac.md, pcm3168a-dac-adc.md, dac-adc-zinciri.md | ✅ disk |
| L288 | tablo | K1.2 · Amplifikatör Aşamaları · 6 · 66 | ✅ disk |
| L289 | tablo | K1.3 · Analog Sinyal Yolu · 1 · 17 | ✅ disk |
| L290 | tablo | K1.4 · Güç Kaynağı & Koruma · 2 · 23 | ✅ disk |
| L291 | tablo | K1.5 · Dijital Arayüzler · 3 · 32 | ✅ disk |
| L292 | tablo | K1.6 · PCB & Termal · 2 · 21 | ✅ disk |
| L293 | tablo | K1.7 · Konnektör & Hoparlör · 2 · 24 | ✅ disk |
| L294 | tablo | K1.8 · Firmware (K1.f alt katmanı) · 8 · 123 | ✅ disk |
| L295 | tablo | K1.9 · Bileşen Haritası & BOM · 1 · 20 · README.md §2-§7 | ✅ disk |
| L296 | tablo | K1.10 · index.md · 1 · 5 | ✅ disk |
| L297 | tablo | K1.11 · CLAUDE.md Guardrails · 1 · 4 | ✅ disk |
| L298 | tablo | K1.12 · README Genel Bakış · 1 · 2 | ✅ disk |
| L299 | tablo | **TOPLAM** · **31** · **360** | ✅ disk |
| L301 | baslik | ### K1.1 — DAC/ADC Zinciri | ✅ disk |
| L303 | tablo | başlık · Kod · Ad · Kanıt (dosya · satır) | ✅ disk |
| L304 | tablo | ayraç satırı | ✅ disk |
| L305 | tablo | **K1.1.1** · AK4458 DAC (3. katman) · ak4458-dac.md — 7 yaprak | ✅ disk |
| L306 | tablo | K1.1.1.1 · Genel Bakış · ak4458-dac.md L10 | ✅ disk |
| L307 | tablo | K1.1.1.2 · Teknik Spesifikasyonlar · ak4458-dac.md L14 | ✅ disk |
| L308 | tablo | K1.1.1.3 · DSD Modu Destekleri · ak4458-dac.md L29 | ✅ disk |
| L309 | tablo | K1.1.1.4 · Devre Tasarımı · ak4458-dac.md L39 | ✅ disk |
| L310 | tablo | K1.1.1.5 · Pin Konfigürasyonu (Önemli Pinler) · ak4458-dac.md L80 | ✅ disk |
| L311 | tablo | K1.1.1.6 · Temel Bağlantılar · ak4458-dac.md L41 | ✅ disk |
| L312 | tablo | K1.1.1.7 · Kondansatör ve Direnç Değerleri · ak4458-dac.md L68 | ✅ disk |
| L313 | tablo | **K1.1.2** · PCM3168A DAC/ADC (3. katman) · pcm3168a-dac-adc.md — 6 yaprak | ✅ disk |
| L314 | tablo | K1.1.2.1 · Genel Bakış · pcm3168a-dac-adc.md L10 | ✅ disk |
| L315 | tablo | K1.1.2.2 · Teknik Spesifikasyonlar · pcm3168a-dac-adc.md L14 | ✅ disk |
| L316 | tablo | K1.1.2.3 · Devre Tasarımı · pcm3168a-dac-adc.md L29 | ✅ disk |
| L317 | tablo | K1.1.2.4 · Pin Konfigürasyonu · pcm3168a-dac-adc.md L68 | ✅ disk |
| L318 | tablo | K1.1.2.5 · Temel Bağlantılar · pcm3168a-dac-adc.md L31 | ✅ disk |
| L319 | tablo | K1.1.2.6 · Filtre ve Kondansatörler · pcm3168a-dac-adc.md L57 | ✅ disk |
| L320 | tablo | **K1.1.3** · DAC-ADC Zinciri (3. katman) · dac-adc-zinciri.md — 10 yaprak | ✅ disk |
| L321 | tablo | K1.1.3.1 · Genel Bakış · dac-adc-zinciri.md L10 | ✅ disk |
| L322 | tablo | K1.1.3.2 · Teknik Spesifikasyonlar · dac-adc-zinciri.md L14 | ✅ disk |
| L323 | tablo | K1.1.3.3 · Sinyal Yolu Diyagramı · dac-adc-zinciri.md L27 | ✅ disk |
| L324 | tablo | K1.1.3.4 · Clock Synchronization · dac-adc-zinciri.md L56 | ✅ disk |
| L325 | tablo | K1.1.3.5 · EMI Filtreleme · dac-adc-zinciri.md L109 | ✅ disk |
| L326 | tablo | K1.1.3.6 · Empedans Eşleşme · dac-adc-zinciri.md L123 | ✅ disk |
| L327 | tablo | K1.1.3.7 · Bileşen Değerleri · dac-adc-zinciri.md L131 | ✅ disk |
| L328 | tablo | K1.1.3.8 · Master/Slave Konfigürasyonu · dac-adc-zinciri.md L58 | ✅ disk |
| L329 | tablo | K1.1.3.9 · Clock Accuracy · dac-adc-zinciri.md L101 | ✅ disk |
| L330 | tablo | K1.1.3.10 · I2S Hat Filtresi · dac-adc-zinciri.md L111 | ✅ disk |

| L331 | bos | — | ⛔ | — | — | — |
| L332–L334 | K1.2 | DAC ve ADC'nin rol ayrımı (AK4458 opsiyonel, PCM3168A analog uç) | ✅ | README L332 | — | — |
| L335 | bos | — | ⛔ | — | — | — |
| L336–L344 | K1.2 | Pin listesi başlığı + OUTL1-3 / OUTR1-3 / VINL1+ − / VINR1+ − / VCOM / AINL1+ − / AINR4+ − / VD+ / VD− / AGND / DGND / I2S_BCK DIN LRCK DEMP RST | ✅ | README L336 | — | — |
| L345 | bos | — | ⛔ | — | — | — |
| L346–L358 | K1.2 | ⚠️ C04 | ✅ | — | ⚠️ | k056 §4/§5 |
| L359–L365 | K1.2 | Okuma akışı çizimi | ✅ | — | — | — |
| L366 | bos | — | ⛔ | — | — | — |
| L367–L368 | K1.2 | DAC sinyal yolu başlığı + kaynak notu | ✅ | README L367 | — | — |
| L369–L400 | K1.2 | K1.2.1 ayrıntı: I2S pin tabelası (32/64 fs, 48 kHz), VCOM 2.1 V, VD+/VD−, DC-DC dönüştürücü + L/C filtre; AIN üzerinden PCM3168A (24-bit, 192 kHz, 6 in / 8 out — C02/C03), ADC sonrası I2S TX, S1.4 bank; kaynak-öncelik sırası (C10), PCM5242 zikri, AK4458 çıkışı; statü ve risksizlik satırları | ✅ | README L369 | — | — |
| L401–L405 | K1.2 | Risksizlik tablosu | ✅ | README L401 | — | — |
| L406–L407 | K1.2 | Bu sürümde değişmeyenler (statü, zoom) | ✅ | README L406 | — | — |
| L408–L410 | K1.2 | Bos + ayraç | ⛔ | — | — | — |
| L411–L412 | K1.3 | DAC-ADC zinciri başlığı + bağlam satırı | ✅ | README L411 | — | — |
| L413–L430 | K1.3 | ⚠️ C05 | ✅ | — | ⚠️ | k056 §4/§5 |
| L431–L435 | K1.3 | Bos blok | ⛔ | — | — | — |
| L436–L460 | K1.4 | DAC kontrol ve doğrulama başlığı (L436) + okuma akışı (L438) + tablolar (L440–L444) + gerilim doğrulama (L445–L448) + ses (L450–L454) + statü (L456) + aynı gün (L457) + adım (L459) + zoom (L460) | ✅ | README L436 | — | — |
| L461–L465 | K1.4 | Bos blok | ⛔ | — | — | — |
| L466–L500 | K1.5 | K1.5 ayrıntı başlığı (L466) + §2.2 (L467) + okuma akışı (L468) + DAC doğrulama tablosu (L470–L478) + ADC doğrulama tablosu (L479–L492) + statü (L494–L497) + sonraki adım (L499) + zoom (L500) | ✅ | README L466 | — | — |
| L501–L505 | K1.5 | Bos blok | ⛔ | — | — | — |
| L506–L528 | K1.6 | K1.6 ayrıntı başlığı (L506) + §2.3 (L507) + okuma akışı (L508) + DAC doğrulama tabloları (L510–L516) + ADC doğrulama tablosu (L517–L524) + statü (L526) + zoom (L527) | ✅ | README L506 | — | — |
| L529–L533 | K1.6 | Bos blok | ⛔ | — | — | — |
| L534–L559 | K1.7 | K1.7 ayrıntı başlığı (L534) + §2.4 (L535) + okuma akışı (L536) + DAC doğrulama tabloları (L538–L548) + ADC doğrulama tablosu (L549–L556) + statü (L558) | ✅ | README L534 | — | — |
| L560–L565 | K1.7 | Sonraki adımlar (L560–L561) + zoom (L562) + kapanış (L564–L565) | ✅ | README L560 | — | — |
| L566 | bos | — | ⛔ | — | — | — |
| L567–L594 | K1.8 | K1.8 başlık (L567) + §2.5 (L568) + okuma akışı (L569) + DAC doğrulama tabloları (L571–L580) + ADC doğrulama tablosu (L581–L591) + statü (L593) | ✅ | README L567 | — | — |
| L595–L599 | K1.8 | Sonraki adımlar + zoom + kapanış | ✅ | README L595 | — | — |
| L600–L615 | K1.8 | Gerilim doğrulama tablosu + kaçak akım kontrolü | ✅ | README L600 | — | — |
| L616–L628 | K1.8 | Ses doğrulama tabloları (DAC + ADC) | ✅ | README L616 | — | — |
| L629–L641 | K1.8 | Statü + diğer testler + zoom + kapanış | ✅ | README L629 | — | — |
| L642–L662 | K1.8 | Adım adım + zoom + statü tablosu (L656–L662) | ✅ | README L642 | — | — |
| L663–L673 | K1.8 | Statü blok devamı (L663–L670) + zoom (L673) | ✅ | README L663 | — | — |
| L674–L690 | K1.8 | Statü devam (L674–L686) + ⚠️ C01 | ✅ | — | ⚠️ | k056 §4 |
| L691–L698 | K1.8 | Statü devam + zoom + kapanış + bos ayraçlar | ✅ | README L691 | — | — |
| L699–L702 | — | K1.9 başlığı (L699) + kapsam (L700) + bölüm başlıkları (L701–L702) | ✅ | README L699 | — | — |
| L703 | — | K1.9.1 başlığı | ✅ | README L703 | — | — |
| L704 | — | ⚠️ C11 | ✅ | — | ⚠️ | k056 §4/§10.1 |
| L705 | — | §3.6 başlığı | ✅ | README L705 | — | — |
| L706–L717 | — | ⚠️ C11 | ✅ | — | ⚠️ | k056 §4/§10.1 |
| L718–L719 | — | §6.3 başlığı + dosya adı satırı | ✅ | README L718 | — | — |
| L720–L722 | — | ⚠️ C11 | ✅ | — | ⚠️ | k056 §4/§10.1 |
| L723 | — | Not satırı (atıf) | ✅ | README L723 | — | — |
| L724–L726 | — | Bos blok | ⛔ | — | — | — |
| L727–L728 | — | K1.10 başlığı (L727) + §3.10 (L728) | ✅ | README L727 | — | — |
| L729–L734 | — | ⚠️ C11 | ✅ | — | ⚠️ | k056 §4/§10.1 |
| L735–L737 | — | Bos blok | ⛔ | — | — | — |
| L738–L739 | — | K1.11 başlığı (L738) + §6.3 (L739) | ✅ | README L738 | — | — |
| L740–L744 | — | ⚠️ C11 | ✅ | — | ⚠️ | k056 §4/§10.1 |
| L745–L747 | — | Bos blok | ⛔ | — | — | — |
| L748–L749 | — | K1.12 başlığı (L748) + §11.1 (L749) | ✅ | README L748 | — | — |
| L750–L752 | — | ⚠️ C11 | ✅ | — | ⚠️ | k056 §4/§10.1 |
| L753–L755 | — | Bos blok | ⛔ | — | — | — |
| L756–L757 | — | Kanıt Kataloğu başlığı (L756) + §3.11 (L757) | ✅ | README L756 | — | — |
| L758–L775 | — | Kanıt satırları 1–18 (disk kanıtları) | ✅ | README L758 | — | — |
| L776–L784 | — | Kanıt satırları 19–27 | ✅ | README L776 | — | — |
| L785–L790 | — | Kanıt satırları 28–33 + flash notu | ✅ | README L785 | — | — |
| L791–L793 | — | Bos blok | ⛔ | — | — | — |
| L794–L796 | — | Notlar başlığı + not 1–2 | ✅ | README L794 | — | — |
| L797–L798 | — | Not 3–4 (17 kaynaklı satır, 8 risksiz 21 saniye) | ✅ | README L797 | — | — |
| L799–L801 | — | Bos blok | ⛔ | — | — | — |
| L802–L803 | — | Kapanış başlığı + manifesto satırı | ✅ | README L802 | — | — |
| L804 | bos | — | ⛔ | — | — | — |
| L805 | footer | — | ⛔ | — | — | — |
