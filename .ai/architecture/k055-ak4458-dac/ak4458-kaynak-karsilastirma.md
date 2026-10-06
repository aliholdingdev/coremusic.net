---
title: "AK4458 DAC — Yedek Kaynak Çapraz Kontrolü ve Çelişki Kaydı"
type: architecture
category: architecture
date: 2026-10-06
updated: 2026-10-06
version: 4.0.0
status: active
authority: reference
---

# AK4458 DAC — Yedek Kaynak Çapraz Kontrolü ve Çelişki Kaydı

## §1 Amaç & Kapsam

Bu belge, `k055-ak4458-dac` klasörünün konu dosyalarındaki iddiaları **salt-okunur yedek kaynaklara** (`_backup/arch-2026-10-06_1057/architecture/…`) satır satır karşılaştırır; eşleşenleri ve çelişenleri ayrı tablolarda kaydeder. Her satır referansı diskteki gerçek içeriğe dayanır; doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED` işaretlenir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `ak4458-dac.md` iddialarının çapraz kontrolü (§3) | Yorum/öneri üretmek → yalnız kayıt tutulur (§4) |
| Kaynaklar arası çelişki kaydı + karar (§4) | Çelişkinin kaynağı düzeltmek → `_backup/**` salt-okunur, yasak |
| Birincil + ikincil kaynak satır indeksleri (§9, §13) | I2S/TDM protokol derinliği → [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] |
| D01 bağımlılık matrisi + wiki-link envanteri (§10) | Teknik hesap/derleme → [[../k055-ak4458-dac/ak4458-dac-rehberi]] |
| Açık kalemler ve doğrulama protokolü (§11, §12) | PCM3168A teknik derinliği → [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] |

**Birincil kaynak:** `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` (satır indeksi §9'da)
**İkincil kaynak:** `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` (satır indeksi §13'te)

## §2 Kaynak Kapsamı

> Kural: bu bölümdeki 7 dosyanın hiçbiri bu görevce **değiştirilmez** (`_backup/**` salt-okunur). İddialar yalnız okunur ve satır numarasıyla kaydedilir.

| # | Kaynak dosya (salt-okunur) | Rol | Bu belgedeki kullanımı |
|---|---------------------------|-----|------------------------|
| 1 | `k1-donanim/ak4458-dac.md` | birincil | §3 iddia tabanı · §9 satır indeksi |
| 2 | `k1-donanim/README.md` | ikincil (bağlam) | §3 çapraz satırlar · §13 satır indeksi |
| 3 | `k1-donanim/dac-adc-zinciri.md` | destek | Clock/rol çapraz kontrolü (C-05, C-06) |
| 4 | `k1-donanim/index.md` | destek | DAC rolü + besleme rayı çapraz kontrolü (C-01, C-07) |
| 5 | `k1-donanim/ozet-durum.md` | destek | Durum rozeti kontrolü (§6) |
| 6 | `k1-donanim/pcm3168a-dac-adc.md` | destek | PCM3168A rol/spec çapraz kontrolü (C-02) |
| 7 | `architecture/firmware/i2s-driver.md` | destek | Firmware bileşen + I2C/MCLK kontrolü (C-03, C-04) |

**Stil kaynağı (yazım öncesi okundu):** `k055-ak4458-dac/ak4458-dac-rehberi.md` · `k055-ak4458-dac/index.md` — § numaralandırması, tablo stili ve wiki-link envanteri bu iki dosyadan klonlandı.

**Kapsam dışı kayıt:** PCM5122/ADR-038 karar satırları (README L60, L62, L274) AK4458 konusuyla doğrudan ilgili değil; §6 kenar durum olarak taşınır.

## §3 Çapraz Kontrol Tablosu

> Sonuç sütunu: `✅` = iki bağımsız kaynakta eşleşme · `⚠️` = tek kaynak veya doğrulanamayan türetme · `❌` = çelişki (§4'teki C-numarası ile).

| # | İddia (özet) | Birincil kaynak + satır | Çapraz kaynak + satır | Sonuç |
|---|--------------|-------------------------|------------------------|-------|
| 1 | AK4458 = ana DAC | `ak4458-dac.md L12` "COREMUSIC'ın ana DAC'ı olarak kullanılır" | `README.md L58` Ana DAC = PCM3168A · `L59` Opsiyonel DAC = AK4458 | ❌ C-01 |
| 2 | AK4458 Opsiyonel DAC · 32-bit · 768kHz | `ak4458-dac.md L20` "Up to 768kHz PCM, DSD256" | `README.md L59` "Opsiyonel DAC \| AK4458 \| 8-kanal \| 32-bit \| 768kHz" | ✅ |
| 3 | AK4458 = 32-bit, 8 kanal | `ak4458-dac.md L12` "premium 32-bit 8 kanallı" | `dac-adc-zinciri.md L18` "AK4458 (32-bit, 8-kanal)" | ✅ |
| 4 | SNR 125 dB (A-weighted) | `ak4458-dac.md L22` | ikinci bağımsız kaynak yok | ⚠️ tek kaynak |
| 5 | THD+N −112 dB (%0.00025) | `ak4458-dac.md L23` | ikinci bağımsız kaynak yok | ⚠️ tek kaynak |
| 6 | Çıkış 2.1 Vrms (differential) | `ak4458-dac.md L21` | ikinci bağımsız kaynak yok | ⚠️ tek kaynak |
| 7 | Çıkış empedansı 25 Ω | `ak4458-dac.md L24` | ikinci bağımsız kaynak yok | ⚠️ tek kaynak |
| 8 | Paket LQFP-64, 10×10 mm | `ak4458-dac.md L26` | `ak4458-dac.md L108` "K0 Fiziksel \| LQFP-64 pad layout" (aynı dosya) | ⚠️ tek kaynak |
| 9 | Besleme AVDD 5V · DVDD 3.3V · CVDD 1.2V | `ak4458-dac.md L25` · `L84` · `L86` · `L88` | `ak4458-dac.md L111` "±5V, +3.3V, +1.2V" (kendi içinde) | ❌ C-07 |
| 10 | DSD64/128/256 saatleri | `ak4458-dac.md L33`-`L35` | ikinci bağımsız kaynak yok | ⚠️ tek kaynak |
| 11 | Format TDM · Slave | `ak4458-dac.md L118` · `L119` | `dac-adc-zinciri.md L86` "AK4458 (Slave)" | ✅ |
| 12 | I2C adres 0x10 (CSN = LOW) | `ak4458-dac.md L121` | `i2s-driver.md L470` "AK4493 için I2C address: 0x10" | ❌ C-04 |
| 13 | Firmware DAC listesi | `ak4458-dac.md L12` (AK4458) | `i2s-driver.md L53` "PCM5242 · AK4493 · CS5368" | ❌ C-03 |
| 14 | MCLK = 256fs (11.2896/12.288 MHz) | `ak4458-dac.md` (oran yok — bkz. §6 E-02) | `dac-adc-zinciri.md L23` · `i2s-driver.md L117`-`L118` (256x) | ❌ C-05 |
| 15 | MCLK = 22.5792 MHz (44.1k ailesi) | `ak4458-dac.md` (kaynakta yok) | `dac-adc-zinciri.md L81` · `L105` · `README.md L51` | ✅ (kaynaklar arası eşleşme; 512fs okuması türetme → C-05) |
| 16 | TDMCLK (pin 18) = MCLK girişi | `ak4458-dac.md L46` · `L90` | `dac-adc-zinciri.md L65` (SCKI ayrı) · `L68` (SCK → TDMCLK "Bit Clock") | ❌ C-06 |
| 17 | PCM3168A = 32-bit / 8kHz–216kHz ADC | `pcm3168a-dac-adc.md L18` · `L20` · `L12` (ADC) | `README.md L58` (24-bit / 192kHz / Ana DAC) · `L122` (24-bit) | ❌ C-02 |
| 18 | PCM3168A I2C 0x8C | `pcm3168a-dac-adc.md L54` · `L113` | README'de I2C adresi yok | ⚠️ tek kaynak (dosya içi tutarlı) |
| 19 | Ana DAC rolü (dizin) | `ak4458-dac.md L12` (AK4458) | `k1-donanim/index.md L56` "DAC \| AK4458" · `L55` "ADC \| PCM3168A" | ✅ (README'e göre — C-01) |
| 20 | Sistem beslemesi ±35V analog · +5V/+3.3V dijital | `ak4458-dac.md L111` (±5V satırı) | `k1-donanim/index.md L77` · `README.md L75` (LM5122 → ±35V) | ❌ C-07 (dönüşüm aşaması kaynakta yok) |
| 21 | AK4458 durum rozeti 🟢 | `ak4458-dac.md L116` "🟢 Hazır" | `ozet-durum.md L21` "🟢 Hazır" · `k1-donanim/index.md L91` "🟢 Hazır" | ✅ (özne = dosya; katman 🟡 → §6 E-05) |
| 22 | README dosya adı = `ak4458-dac.md` | disk: `_backup/.../ak4458-dac.md` | `README.md L287` · `L305` · `L763` (doğru) · `L261` "ak4458.md" (yanlış) | ❌ C-08 |
| 23 | PCM5122 reddedilmiş (ADR-038) | `ak4458-dac.md` PCM5122'den söz etmez | `README.md L60` · `L62` · `L274` | ✅ (çelişki değil — §6 E-01) |
| 24 | Clock 24.576 MHz (48k ailesi) | `ak4458-dac.md` (kaynakta yok) | `dac-adc-zinciri.md L20` · `README.md L52` | ✅ (çapraz kaynaklar arası) |

**Sayım:** 24 madde · **6 çelişki** (C-01…C-08 aralığında 8 kayıt; §4'te tamamı) · **9 tek-kaynak/türetme** ⚠️ · **9 eşleşme** ✅.

## §4 Çelişki Kaydı

> Kolonlar: `#` · `İddia A (kaynak+satır)` · `İddia B (kaynak+satır)` · `Fark` · `Etki` · `Karar` · `Etkilediği dosya`. Karar sütunundaki `⚠️ VERIFICATION REQUIRED`, çelişkinin bu görevce **çözülmediğini** (kaynak salt-okunur) gösterir.

| # | İddia A (kaynak + satır) | İddia B (kaynak + satır) | Fark | Etki | Karar | Etkilediği dosya |
|---|--------------------------|--------------------------|------|------|-------|------------------|
| C-01 | `k1-donanim/ak4458-dac.md L12` — "COREMUSIC'ın ana DAC'ı olarak kullanılır" | `k1-donanim/README.md L58` — "Ana DAC \| PCM3168A" ve `L59` — "Opsiyonel DAC \| AK4458" | Ana DAC kimliği: AK4458 vs PCM3168A | k055/k056 kapsam tanımı, firmware öncelik sırası, BOM satırı (`README.md L244` "DAC (PCM3168A)") | ⚠️ VERIFICATION REQUIRED — hangi dosyanın güncel olduğu diskten anlaşılmıyor; iki iddia da satırıyla taşınır | `ak4458-dac-rehberi.md` §1 · `pcm3168a-dac-adc-rehberi.md` · `k055/ak4458-kaynak-karsilastirma.md` (bu dosya) |
| C-02 | `k1-donanim/pcm3168a-dac-adc.md L12` — "analog giriş … dijital formata dönüştürücüdür" (ADC), `L18` — "32-bit", `L20` — "8kHz – 216kHz" | `k1-donanim/README.md L58` — "Ana DAC \| PCM3168A \| 6-in/8-out \| 24-bit \| 192kHz", `L122` — "Bit Depth \| 24-bit" | Rol (ADC vs DAC) + bit (32 vs 24) + örnekleme (216k vs 192k) | k056 rehberi spec tablosu, README §3 (L94) I2S konfigürasyonu | ⚠️ VERIFICATION REQUIRED — iki kaynak aynı çip için üç farklı nitelik yazıyor | `pcm3168a-dac-adc-rehberi.md` · `README.md` §3 |
| C-03 | `k1-donanim/ak4458-dac.md L12` — AK4458 ana DAC; `pcm3168a-dac-adc.md L8` — PCM3168A ADC | `firmware/i2s-driver.md L53` — "PCM5242 · AK4493 · CS5368"; `L464` — "PCM5242 için I2C address: 0x94" | Firmware bileşen listesi donanım dokümanıyla uyuşmuyor (AK4493/PCM5242/CS5368 ↔ AK4458/PCM3168A) | I2C register yazımı (`i2s-driver.md L471`-`L473`), sürücü doğrulaması, k058 entegrasyonu | ⚠️ VERIFICATION REQUIRED — hangi çipin fiziksel olarak lehimlendiği bu dosyalardan anlaşılmıyor | `firmware/i2s-driver.md` · `ak4458-dac-rehberi.md` §3 (I2C) |
| C-04 | `k1-donanim/ak4458-dac.md L121` — "I2C adres: 0x10 (CSN = LOW)" | `firmware/i2s-driver.md L470` — "# AK4493 için I2C address: 0x10" (örnek: `L471`-`L473`) | Aynı adres iki farklı çip için kullanılıyor; register haritası çip-özel olduğu için adres eşitliği register doğrulaması sağlamaz | Hatalı register yazımı cihazı sessize/bozabilir | ⚠️ VERIFICATION REQUIRED — 0x10'un AK4458 register haritasına uyumu bağımsız doğrulanmalı | `ak4458-dac-rehberi.md` §3 · `firmware/i2s-driver.md` §I2C |
| C-05 | `k1-donanim/dac-adc-zinciri.md L23` — "MCLK \| 256fs = 11.2896MHz / 12.288MHz"; `firmware/i2s-driver.md L117`-`L118` — "MCLK Ratio 256x" | `k1-donanim/dac-adc-zinciri.md L81` — "MCLK (22.5792MHz)", `L105` — "MCLK \| 22.5792MHz \| ±50ppm" | 256fs (11.2896 MHz) ↔ 22.5792 MHz; 22.5792 MHz = 512fs @ 44.1 kHz olduğu **türetmedir**, hiçbir kaynakta AK4458 için "512fs" yazmıyor (`README.md L110` "256fs or 512fs" satırı §3 PCM3168A pin listesine ait) | Clock hiyerarşisi, k054 şeması, k057 klon oranı | ⚠️ VERIFICATION REQUIRED — birincil `ak4458-dac.md` dosyasında fs oranı hiç yok (§6 E-02) | `zincir-mimari` (k054) · `i2s-ve-tdm-rehberi` (k057) · `ak4458-dac-rehberi.md` §4 |
| C-06 | `k1-donanim/ak4458-dac.md L90` — "18 \| TDMCLK \| Giriş \| TDM clock (MCLK)"; `L46` — "SCK ──▶ AK4458 TDMCLK (Pin 18)" | `k1-donanim/dac-adc-zinciri.md L68` — "SCK Output ──▶ AK4458 TDMCLK (Bit Clock)"; `L65` — "MCLK Output ──▶ AK4458 SCKI" | TDMCLK'e gelen sinyal MCLK mı (A) bit clock mı (B); B'de ayrı SCKI girişi var, A'da SCKI pin olarak geçmiyor | Pin eşlemesi (k057), k055 §2 bağlantı şeması | ⚠️ VERIFICATION REQUIRED — iki şema birbirinin yerine kullanılırsa clock yanlış pine gider | `ak4458-dac-rehberi.md` §2 · `zincir-mimari` (k054) |
| C-07 | `k1-donanim/ak4458-dac.md L25` — "AVDD = 5V, DVDD = 3.3V, CVDD = 1.2V"; `L84` — "+5V analog besleme" | `k1-donanim/ak4458-dac.md L111` — "±5V, +3.3V, +1.2V" (bağımlılık tablosu) | Birincil kaynak **kendi içinde**: tekli +5V ↔ çift ±5V; ayrıca sistem rayı `index.md L77` "±35V analog" ve `README.md L75` "LM5122 → ±35V" iken AK4458'in 5V rayını üreten aşamada hiçbir kaynakta yok | k068 besleme tasarımı, k055 §3 besleme satırı | ⚠️ VERIFICATION REQUIRED — ±5V/5V dönüşüm yolu ve AK4458 rayının kaynağı kanıtsız (±50ppm/±35V değerleri bu belgede tekrar üretilmez) | `analog-besleme` (k068) · `ak4458-dac-rehberi.md` §3 · `k055/index.md` |
| C-08 | Disk + `README.md L287` — "ak4458-dac.md, pcm3168a-dac-adc.md, dac-adc-zinciri.md"; `L305` — "ak4458-dac.md — 7 yaprak"; `L763` — "ak4458-dac.md" | `README.md L261` — "`architecture/k1-donanim/ak4458.md`"; `L260` — "`…/pcm3168a.md`" | README §8 "İlgili Dosyalar" tablosu diskte olmayan dosya adları veriyor (README kendi §5/§10 tablolarıyla da çelişiyor) | Otomatik wiki-link taraması, referans kırıkları | ⚠️ VERIFICATION REQUIRED — README salt-okunur, bu görevce düzeltilmez; doğru adlar L287/L305/L763'ten taşınır | `README.md` §8 · bu dosya §2 |
| N-09 (not) | `k1-donanim/ak4458-dac.md` — PCM5122'ye dair tek satır yok | `k1-donanim/README.md L60` · `L62` · `L274` — PCM5122 REDDEDİLMİŞ (ADR-038) | Kapsam farkı: karar PCM5122'yi ilgilendiriyor, AK4458 dosyasını değil | Yok (bilgi) | Çelişki değil — kapsam notu olarak §6'ya taşındı | `ak4458-dac-rehberi.md` (dokunulmaz) |
| N-10 (not) | `k1-donanim/ak4458-dac.md L116` — "🟢 Hazır" (dosya) · `ozet-durum.md L21` — "🟢 Hazır" | `k1-donanim/index.md L85` — "K1 Katman Durumu: 🟡 Tasarım Aşamasında" · `dac-adc-zinciri.md L153` — "🟡 Simülasyon Aşamasında" | Özne farkı: dosya bazlı rozet 🟢, katman/zincir bazlı 🟡 | Durum raporu okuması | Çelişki değil — özne ayrımı §6 E-05'te kayıtlı | `k055/index.md` §4 · `ozet-durum` referansı |

**Toplam:** **8 çelişki** (C-01…C-08) + **2 kapsam/özne notu** (N-09, N-10).

## §5 Bağımlılıklar

| Yön | Hedef | Gerekçe / kanıt |
|-----|-------|-----------------|
| Üst kapsayıcı | [[../k054-dac-adc-zinciri/zincir-mimari]] | Clock/rol çelişkilerinin (C-05, C-06) düğüm noktası |
| Kardeş rehber | [[../k055-ak4458-dac/ak4458-dac-rehberi]] | Bu belgenin karşılaştırdığı hedef dosya (iddia sahibi) |
| Kardeş index | `k055-ak4458-dac/index.md` | Klasör envanteri; §10.1 link envanteriyle hizalı |
| ADC kardeşi | [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] | C-01/C-02 çelişkilerinin ikinci tarafı |
| Protokol | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] | C-05 (fs oranı) ve C-06 (pin rolü) doğrulama sorumlusu |
| Kaynak üretici | [[../k058-xmos-xu316/xu316-entegrasyon]] | MCLK/SCK/WS sinyalleri XMOS'tan gelir (`ak4458-dac.md L46`-`L65`) |
| Besleme | [[../k068-guc-kaynagi-analog/analog-besleme]] | C-07 (±5V/+5V ↔ ±35V dönüşüm yolu) |
| Çıkış yükü | [[../k063-output-stage/output-stage-tasarim]] | AK4458 differential çıkışının hedefi (`ak4458-dac.md L55`-`L59`) |
| Analog devam | [[../k060-analog-sinyal-yolu/analog-yol-rehberi]] | Çıkış filtresi sonrası yol |

## §6 Kenar Durumlar

| # | Kenar durum | Kaynak kanıtı | Davranış |
|---|-------------|---------------|----------|
| E-01 | PCM5122/ADR-038 kapsam dışı | `README.md L60` · `L62` · `L274` | AK4458 çelişkisi sayılmaz; yalnız kayıt (N-09) |
| E-02 | Birincil kaynakta fs oranı yok | `ak4458-dac.md L80`-`L102` (pin tablosu) ve `L114`-`L123` (durum) arasında oran/çarpan satırı bulunmuyor | "256fs/512fs" iddiası bu dosyaya atfedilemez → `⚠️ VERIFICATION REQUIRED` |
| E-03 | 22.5792 MHz → 512fs okuması | `dac-adc-zinciri.md L81`, `L105` değeri veriyor; "512fs" kelimesi geçmiyor | Türetme işaretli kullanılır; değer olduğu gibi, oran yorumu `⚠️` |
| E-04 | README L110 "256fs or 512fs" | `README.md L94` §3 başlığı altında (PCM3168A pin listesi) | AK4458 için kanıt olarak **kullanılmaz** |
| E-05 | Durum rozeti özne farkı | `ak4458-dac.md L116` 🟢 (dosya) vs `index.md L85` 🟡 (katman) vs `dac-adc-zinciri.md L153` 🟡 (zincir) | Çelişki değil; özne adıyla kaydedilir (N-10) |
| E-06 | Firmware "Planlandı" statüsü | `i2s-driver.md L489`-`L496` (I2S Master/Slave, TDM, Clock, SRC, DAC/ADC Config = Planlandı) | Donanım 🟢 rozetini **etkilemez**; farklı katman, ayrı satır |
| E-07 | Aynı I2C adresi iki çip | `ak4458-dac.md L121` · `i2s-driver.md L470` | Adres eşitliği ≠ register eşitliği; doğrulama gerekiyor (C-04) |
| E-08 | ±5V vs +5V yazım farkı | `ak4458-dac.md L84` "+5V" vs `L111` "±5V" | Aynı kaynak içinde iki yazım; ikisi de olduğu gibi aktarılır, normalize edilmez |

## §7 Hata Modları

| # | Hata modu | Belirti (kaynak + satır) | Etki | Önlem / işaret |
|---|-----------|--------------------------|------|----------------|
| H-01 | Yanlış ana DAC varsayımı | `README.md L58` vs `ak4458-dac.md L12` (C-01) | Firmware yanlış çipe öncelik verir, kanal planı bozulur | Çifte kayıt korunur; karar üst dokümanda `⚠️` |
| H-02 | Çip-özel register'ın başka çipe yazılması | `i2s-driver.md L471`-`L473` (C-03/C-04) | Ses çıkışı sessiz/bozuk; register haritası uyuşmazlığı | `i2cset` öncesi çip kimliği teyidi zorunlu → `⚠️` |
| H-03 | Clock oranının yanlış okunması | `dac-adc-zinciri.md L23` vs `L81` (C-05) | AK4458 kilitlenmez (slave, `L119`), TDM çerçevesi kayar | fs oranı birincil kaynakta yok → uydurulmaz, `⚠️` |
| H-04 | MCLK'ın bit clock pine bağlanması | `ak4458-dac.md L90` vs `dac-adc-zinciri.md L68` (C-06) | Hiçbir kanal çalışmaz veya bozuk çalışır | Pin-eşleme k057'de teyit edilmeden şema üretilmez → `⚠️` |
| H-05 | Besleme rayının yanlış türetilmesi | `ak4458-dac.md L25` vs `L111` (C-07) | AVDD'e ±5V uygulanması çipi hasara uğratma riski | ±5V/±35V dönüşüm yolu kanıtlanana kadar değer yazılmaz → `⚠️` |
| H-06 | Kırık dosya referansı | `README.md L260`-`L261` (C-08) | Otomatik link taraması sahte kırık üretir | Referanslar L287/L305/L763'ten alınır; README düzeltilmez |
| H-07 | Tek kaynaklı spec'in doğruluk sanılması | `ak4458-dac.md L22`-`L24` (SNR/THD/Zout) | Doğrulanmamış ölçüm gibi kullanılır | §3'te "⚠️ tek kaynak" olarak işaretli kalır |
| H-08 | Durum rozetinin yanlış özne | `index.md L85` 🟡 vs `ak4458-dac.md L116` 🟢 (N-10) | "Hazır" sanılan katman aslında tasarımda | Özne (dosya/katman/zincir) her rozette yazılır |

## §8 Doğrulama & Kanıt

| # | İddia kümesi | Kanıt yöntemi | Durum |
|---|--------------|---------------|-------|
| 1 | §9/§13 satır indeksleri | Kaynak dosya programatik okuma (satır satır) + tür sınıflandırma | ✅ disk |
| 2 | §3/§4 satır referansları | Her `L<n>` değeri kaynak dosyadan grep/read ile teyit edildi | ✅ disk |
| 3 | Wiki-link hedefleri | `.ai/architecture/**` glob ile varlık kontrolü (18/18) | ✅ disk |
| 4 | Frontmatter 7 alan + version 4.0.0 / updated 2026-10-06 | Doğrulama protokolü §11 adım 5-6 | ⏳ (§11) |
| 5 | UTF-8 (mojibake 0 · BOM yok) | `node .ai/scripts/vault-utf8-writer.mjs verify` | ⏳ (§11) |
| 6 | Satır kapısı ≥500 | `[System.IO.File]::ReadAllLines(...)` | ⏳ (§11) |
| 7 | Türetme/tek-kaynak iddialar | Uydurma yok; yalnız `⚠️ VERIFICATION REQUIRED` | ✅ disk |

## §9 Birincil Kaynak Satır İndeksi (`ak4458-dac.md`)

> Salt-okunur birincil kaynağın satır-satır indeksi: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md`. Boş satırlar atlandı; `|` karakteri `\|` ile kaçırıldı; özet 100 karakterle sınırlı.

| Kaynak satır | Tür | İçerik özeti | Kanıt |
|---|---|---|---|
| L1 | metin | --- | ✅ disk |
| L2 | metin | title: "AK4458 32-Bit 8-Kanal DAC" | ✅ disk |
| L3 | metin | layer: K1 | ✅ disk |
| L4 | metin | category: "Dijital/Analog Dönüştürücü" | ✅ disk |
| L5 | metin | date: 2026-09-20 | ✅ disk |
| L6 | metin | --- | ✅ disk |
| L8 | baslik | # AK4458 32-Bit 8-Kanal DAC | ✅ disk |
| L10 | baslik | ## Genel Bakış | ✅ disk |
| L12 | metin | AK4458, Asahi Kasei Microdevices (AKM) tarafından üretilen premium 32-bit 8 kanallı (4 stereo) digi… | ✅ disk |
| L14 | baslik | ## Teknik Spesifikasyonlar | ✅ disk |
| L16 | tablo | \| Parametre \| Değer \| | ✅ disk |
| L17 | tablo | \|-----------\|-------\| | ✅ disk |
| L18 | tablo | \| Çözünürlük \| 32-bit \| | ✅ disk |
| L19 | tablo | \| Kanal Sayısı \| 8 (4 stereo) \| | ✅ disk |
| L20 | tablo | \| Örnekleme Hızı \| Up to 768kHz PCM, DSD256 \| | ✅ disk |
| L21 | tablo | \| Çıkış Voltajı \| 2.1Vrms (differential) \| | ✅ disk |
| L22 | tablo | \| SNR \| 125dB (A-Weighted) \| | ✅ disk |
| L23 | tablo | \| THD+N \| -112dB (%0.00025) \| | ✅ disk |
| L24 | tablo | \| Çıkış Empedansı \| 25Ω (differential) \| | ✅ disk |
| L25 | tablo | \| Voltaj Besleme \| AVDD = 5V, DVDD = 3.3V, CVDD = 1.2V \| | ✅ disk |
| L26 | tablo | \| Package \| LQFP-64, 10×10mm \| | ✅ disk |
| L27 | tablo | \| Çalışma Sıcaklığı \| -40°C ile +85°C \| | ✅ disk |
| L29 | baslik | ## DSD Modu Destekleri | ✅ disk |
| L31 | tablo | \| Mod \| Örnekleme Hızı \| Bit Derinliği \| | ✅ disk |
| L32 | tablo | \|-----\|----------------\|---------------\| | ✅ disk |
| L33 | tablo | \| DSD64 \| 2.8224 MHz \| 1-bit \| | ✅ disk |
| L34 | tablo | \| DSD128 \| 5.6448 MHz \| 1-bit \| | ✅ disk |
| L35 | tablo | \| DSD256 \| 11.2896 MHz \| 1-bit \| | ✅ disk |
| L36 | tablo | \| DoP128 \| 5.6448 MHz \| 1-bit (PCM wrap) \| | ✅ disk |
| L37 | tablo | \| DoP256 \| 11.2896 MHz \| 1-bit (PCM wrap) \| | ✅ disk |
| L39 | baslik | ## Devre Tasarımı | ✅ disk |
| L41 | baslik | ### Temel Bağlantılar | ✅ disk |
| L43 | kod | ``` | ✅ disk |
| L44 | kod | XMOS XU316 I2S Output | ✅ disk |
| L45 | kod | │ | ✅ disk |
| L46 | kod | ├─ SCK ──────────────────▶ AK4458 TDMCLK (Pin 18) | ✅ disk |
| L47 | kod | ├─ WS ───────────────────▶ AK4458 TDMFS (Pin 19) | ✅ disk |
| L48 | kod | ├─ SD0 (Ch1-2 Data) ────▶ AK4458 TDMD0 (Pin 20) | ✅ disk |
| L49 | kod | ├─ SD1 (Ch3-4 Data) ────▶ AK4458 TDMD1 (Pin 21) | ✅ disk |
| L50 | kod | ├─ SD2 (Ch5-6 Data) ────▶ AK4458 TDMD2 (Pin 22) | ✅ disk |
| L51 | kod | └─ SD3 (Ch7-8 Data) ────▶ AK4458 TDMD3 (Pin 23) | ✅ disk |
| L53 | kod | AK4458 Analog Çıkışlar | ✅ disk |
| L54 | kod | │ | ✅ disk |
| L55 | kod | ├─ OUTL1+ ──▶ Class AB Amp Input (Kanal 1 Sol) | ✅ disk |
| L56 | kod | ├─ OUTL1- ──▶ Class AB Amp Input (Kanal 1 Sol Negatif) | ✅ disk |
| L57 | kod | ├─ OUTR1+ ──▶ Class AB Amp Input (Kanal 1 Sağ) | ✅ disk |
| L58 | kod | ├─ OUTR1- ──▶ Class AB Amp Input (Kanal 1 Sağ Negatif) | ✅ disk |
| L59 | kod | └─ (Diğer kanallar için devam eder, toplam 8 çift differential) | ✅ disk |
| L61 | kod | AK4458 Kontrol (I2C) | ✅ disk |
| L62 | kod | │ | ✅ disk |
| L63 | kod | ├─ SDA ──▶ XMOS GPIO[2] | ✅ disk |
| L64 | kod | ├─ SCL ──▶ XMOS GPIO[3] | ✅ disk |
| L65 | kod | └─ CSN ──▶ GND (SPI = I2C modu) | ✅ disk |
| L66 | kod | ``` | ✅ disk |
| L68 | baslik | ### Kondansatör ve Direnç Değerleri | ✅ disk |
| L70 | tablo | \| Referans \| Değer \| Açıklama \| | ✅ disk |
| L71 | tablo | \|----------\|-------\|----------\| | ✅ disk |
| L72 | tablo | \| C1-C4 \| 100nF MLCC \| AVDD dekuplajı \| | ✅ disk |
| L73 | tablo | \| C5-C8 \| 10µF MLCC \| AVDD bulk \| | ✅ disk |
| L74 | tablo | \| C9-C12 \| 100nF MLCC \| DVDD dekuplajı \| | ✅ disk |
| L75 | tablo | \| C13-C16 \| 1µF MLCC \| CVDD dekuplajı \| | ✅ disk |
| L76 | tablo | \| C17-C24 \| 22pF \| Çıkış filtre kondansatörü \| | ✅ disk |
| L77 | tablo | \| R1-R8 \| 100Ω \| Çıkış seri direnç \| | ✅ disk |
| L78 | tablo | \| R9-R10 \| 4.7kΩ \| I2C pull-up \| | ✅ disk |
| L80 | baslik | ## Pin Konfigürasyonu (Önemli Pinler) | ✅ disk |
| L82 | tablo | \| Pin \| Ad \| Yön \| Açıklama \| | ✅ disk |
| L83 | tablo | \|-----\|-----\|------\|----------\| | ✅ disk |
| L84 | tablo | \| 1-4 \| AVDD \| Güç \| +5V analog besleme \| | ✅ disk |
| L85 | tablo | \| 5-8 \| AGND \| Güç \| Analog toprak \| | ✅ disk |
| L86 | tablo | \| 9-12 \| DVDD \| Güç \| +3.3V dijital besleme \| | ✅ disk |
| L87 | tablo | \| 13-14 \| DGND \| Güç \| Dijital toprak \| | ✅ disk |
| L88 | tablo | \| 15-16 \| CVDD \| Güç \| +1.2V çekirdek besleme \| | ✅ disk |
| L89 | tablo | \| 17 \| CPGND \| Güç \| Charge pump toprak \| | ✅ disk |
| L90 | tablo | \| 18 \| TDMCLK \| Giriş \| TDM clock (MCLK) \| | ✅ disk |
| L91 | tablo | \| 19 \| TDMFS \| Giriş \| TDM frame sync (LRCK) \| | ✅ disk |
| L92 | tablo | \| 20-23 \| TDMD[0:3] \| Giriş \| TDM data inputs \| | ✅ disk |
| L93 | tablo | \| 24 \| DIF0 \| Giriş \| Format seçimi \| | ✅ disk |
| L94 | tablo | \| 25 \| DIF1 \| Giriş \| Format seçimi \| | ✅ disk |
| L95 | tablo | \| 26 \| DIF2 \| Giriş \| Format seçimi \| | ✅ disk |
| L96 | tablo | \| 27-34 \| OUTL1±, OUTR1± \| Çıkış \| Analog çıkış çift 1 \| | ✅ disk |
| L97 | tablo | \| 35-42 \| OUTL2±, OUTR2± \| Çıkış \| Analog çıkış çift 2 \| | ✅ disk |
| L98 | tablo | \| 43-50 \| OUTL3±, OUTR3± \| Çıkış \| Analog çıkış çift 3 \| | ✅ disk |
| L99 | tablo | \| 51-58 \| OUTL4±, OUTR4± \| Çıkış \| Analog çıkış çift 4 \| | ✅ disk |
| L100 | tablo | \| 59 \| SDA \| Bidirectional \| I2C veri \| | ✅ disk |
| L101 | tablo | \| 60 \| SCL \| Giriş \| I2C clock \| | ✅ disk |
| L102 | tablo | \| 61-64 \| NC/VARIOUS \| - \| Diğer \| | ✅ disk |
| L104 | baslik | ## Bağımlılıklar | ✅ disk |
| L106 | tablo | \| Bağımlılık \| Yön \| Açıklama \| | ✅ disk |
| L107 | tablo | \|------------\|-----\|----------\| | ✅ disk |
| L108 | tablo | \| K0 Fiziksel \| Alt \| LQFP-64 pad layout \| | ✅ disk |
| L109 | tablo | \| K1 XMOS \| Bağlantı \| I2S/TDM input \| | ✅ disk |
| L110 | tablo | \| K1 Class AB Amp \| Çıkış \| Differential analog output → amplifikatör \| | ✅ disk |
| L111 | tablo | \| K1 Güç Kaynağı \| Alt \| ±5V, +3.3V, +1.2V \| | ✅ disk |
| L112 | tablo | \| K1 Termal \| Bağlantı \| Sıcaklık izleme için NTC \| | ✅ disk |
| L114 | baslik | ## Durum: Implementasyon | ✅ disk |
| L116 | vurgu | **Durum**: 🟢 Hazır | ✅ disk |
| L118 | madde | - Format: TDM (DIF2:1:0 = 1:0:0) | ✅ disk |
| L119 | madde | - Master/Slave: Slave (AK4458 clock XMOS'tan gelir) | ✅ disk |
| L120 | madde | - DSD modu: Auto-detect (DSD_DATA pin HIGH) | ✅ disk |
| L121 | madde | - I2C adres: 0x10 (CSN = LOW) | ✅ disk |
| L122 | madde | - Volume: Dijital potansiyometre ile programlanabilir (0dB ile -∞) | ✅ disk |
| L123 | madde | - Soft mute: I2C register MUTE bit ile | ✅ disk |

## §10 Bağımlılık Matrisi (D01 — k054…k071)

> Tüm hedefler `.ai/architecture/` altında **glob ile doğrulandı** (2026-10-06): 18/18 klasör, 18/18 dosya mevcut.

| Klasör | Wiki-link | İlişki (bu dosyaya) |
|---|---|---|
| `k054-dac-adc-zinciri` (DAC-ADC dönüşüm zinciri) | [[../k054-dac-adc-zinciri/zincir-mimari]] | C-05 clock oranı + C-06 pin rolü çelişkilerinin sahibi |
| `k055-ak4458-dac` (AK4458 ana DAC) | [[../k055-ak4458-dac/ak4458-dac-rehberi]] | Bu dosyanın karşılaştırdığı hedef (iddia sahibi) |
| `k056-pcm3168a-dac-adc` (PCM3168A DAC+ADC) | [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] | C-01/C-02 çelişkilerinin ikinci tarafı |
| `k057-i2s-interface` (I2S/TDM haberleşme) | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] | fs oranı ve pin eşlemesi doğrulama sorumlusu |
| `k058-xmos-xu316` (XMOS XU316) | [[../k058-xmos-xu316/xu316-entegrasyon]] | Clock/I2S master kaynağı (`ak4458-dac.md L46`-`L65`) |
| `k059-usb-audio` (USB Audio Class yolu) | [[../k059-usb-audio/usb-audio-yolu]] | Üst veri yolu; bu belgede doğrudan iddia taşımaz |
| `k060-analog-sinyal-yolu` (Analog sinyal yolu) | [[../k060-analog-sinyal-yolu/analog-yol-rehberi]] | AK4458 çıkışının devamı (`ak4458-dac.md L55`-`L59`) |
| `k061-diff-pair-input` (Differential pair giriş) | [[../k061-diff-pair-input/diff-pair-tasarim]] | Differential çift eşleşmesi için referans |
| `k062-vas-stage` (VAS aşaması) | [[../k062-vas-stage/vas-stage-tasarim]] | Zincir devamı; bu belgede iddia taşımaz |
| `k063-output-stage` (Çıkış aşaması) | [[../k063-output-stage/output-stage-tasarim]] | Differential çıkışın yükü (`ak4458-dac.md L110`) |
| `k064-feedback-network` (Geri besleme ağı) | [[../k064-feedback-network/feedback-tasarim]] | Çıkış aşaması kazancı; kapsam dışı |
| `k065-mjle21194-93` (MJL21194/93 güç çıkışı) | [[../k065-mjle21194-93/mjle-op-amp-kurulum]] | Sınıf AB zinciri sonu; kapsam dışı |
| `k066-konnektorler` (Konnektör envanteri) | [[../k066-konnektorler/konnektor-envanteri]] | Fiziksel arayüz; bu belgede iddia taşımaz |
| `k067-koruma-devreleri` (Koruma devreleri) | [[../k067-koruma-devreleri/koruma-rehberi]] | Besleme/çıkış koruması; C-07 ile ilgili |
| `k068-guc-kaynagi-analog` (Analog güç kaynağı) | [[../k068-guc-kaynagi-analog/analog-besleme]] | C-07 ±5V/±35V dönüşüm yolu sorumlusu |
| `k069-hoparlor-dizilimi` (Hoparlör dizilimi) | [[../k069-hoparlor-dizilimi/hoparlor-dizilim]] | Çıkış yükü nihai hedefi; kapsam dışı |
| `k070-pcb-tasarim` (PCB tasarımı) | [[../k070-pcb-tasarim/pcb-rehber]] | Pin/rota fiziksel taşıyıcısı; kapsam dışı |
| `k071-termal-yonetim` (Termal yönetim) | [[../k071-termal-yonetim/termal-rehber]] | Sıcaklık izleme (`ak4458-dac.md L112`); kapsam dışı |

### §10.1 Bu Dosyadaki Wiki-Link Envanteri

> Her hedef, `.ai/architecture/**` glob çıktısıyla karşılaştırıldı; **kırık link 0**.

| Hedef | Durum |
|---|---|
| `../k054-dac-adc-zinciri/zincir-mimari` | ✅ disk |
| `../k055-ak4458-dac/ak4458-dac-rehberi` | ✅ disk |
| `../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi` | ✅ disk |
| `../k057-i2s-interface/i2s-ve-tdm-rehberi` | ✅ disk |
| `../k058-xmos-xu316/xu316-entegrasyon` | ✅ disk |
| `../k059-usb-audio/usb-audio-yolu` | ✅ disk |
| `../k060-analog-sinyal-yolu/analog-yol-rehberi` | ✅ disk |
| `../k061-diff-pair-input/diff-pair-tasarim` | ✅ disk |
| `../k062-vas-stage/vas-stage-tasarim` | ✅ disk |
| `../k063-output-stage/output-stage-tasarim` | ✅ disk |
| `../k064-feedback-network/feedback-tasarim` | ✅ disk |
| `../k065-mjle21194-93/mjle-op-amp-kurulum` | ✅ disk |
| `../k066-konnektorler/konnektor-envanteri` | ✅ disk |
| `../k067-koruma-devreleri/koruma-rehberi` | ✅ disk |
| `../k068-guc-kaynagi-analog/analog-besleme` | ✅ disk |
| `../k069-hoparlor-dizilimi/hoparlor-dizilim` | ✅ disk |
| `../k070-pcb-tasarim/pcb-rehber` | ✅ disk |
| `../k071-termal-yonetim/termal-rehber` | ✅ disk |

## §11 Doğrulama Protokolü

| # | Adım | Komut (repo kökünden) | Beklenen |
|---|------|------------------------|----------|
| 1 | Satır sayısı (≥500 kapısı) | `[System.IO.File]::ReadAllLines('.ai/architecture/k055-ak4458-dac/ak4458-kaynak-karsilastirma.md').Length` | ≥500 (boş satır dahil) |
| 2 | UTF-8 / mojibake | `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k055-ak4458-dac/ak4458-kaynak-karsilastirma` | `mojibake: 0`, `hasBom: false` |
| 3 | Klasör taraması | `node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/architecture/k055-ak4458-dac` | 0 bulgu |
| 4 | Wiki-link kırıklığı | `../<klasör>/<dosya>` biçimindeki tüm hedefler `.ai/architecture/` altında (§10.1 envanteri) | kırık 0 |
| 5 | Frontmatter | 7 zorunlu alan: title · type · category · date · updated · version · status · authority | eksik yok |
| 6 | Sürüm/tarih | `version: 4.0.0` · `updated: 2026-10-06` | birebir |
| 7 | Kaynak dokunulmazlık | `git status --porcelain -- _backup/` | değişiklik yok (salt-okunur) |
| 8 | Commit kapısı | `git status --porcelain -- .ai/architecture/k055-ak4458-dac` | `??` — bu görevde **commit ATILMAZ** |
| 9 | REDACTED | dosyada secret/token/anahtar bulunmaması | 0 eşleşme |
| 10 | Çelişki/uyarı sayımı | §4 (8 çelişki) + §12 (`⚠️` satır sayısı) | sayılar tutarlı |

## §12 Açık Kalemler (⚠️ işaretli)

> Tarama: bu dosyanın yazar gövdesinde `⚠️` içeren satır sayısı = **29** (otomatik sayım; §12 başlığı/kendi tablosu ve §9·§13 kaynak indeks satırları hariç — numaralar §9/§13 eklendikten sonra ölçüldü; bu tabloya satır eklemek yukarıdaki numaraları değiştirmez).

| Satır | İşaretli madde |
|---|---|
| 16 | Bu belge, `k055-ak4458-dac` klasörünün konu dosyalarındaki iddiaları **salt-okunur yedek kaynaklara** (`_backup/arch-2026-10-06_1057/archit… |
| 49 | > Sonuç sütunu: `✅` = iki bağımsız kaynakta eşleşme · `⚠️` = tek kaynak veya doğrulanamayan türetme · `❌` = çelişki (§4'teki C-numarası ile… |
| 56 | \| 4 \| SNR 125 dB (A-weighted) \| `ak4458-dac.md L22` \| ikinci bağımsız kaynak yok \| ⚠️ tek kaynak \| |
| 57 | \| 5 \| THD+N −112 dB (%0.00025) \| `ak4458-dac.md L23` \| ikinci bağımsız kaynak yok \| ⚠️ tek kaynak \| |
| 58 | \| 6 \| Çıkış 2.1 Vrms (differential) \| `ak4458-dac.md L21` \| ikinci bağımsız kaynak yok \| ⚠️ tek kaynak \| |
| 59 | \| 7 \| Çıkış empedansı 25 Ω \| `ak4458-dac.md L24` \| ikinci bağımsız kaynak yok \| ⚠️ tek kaynak \| |
| 60 | \| 8 \| Paket LQFP-64, 10×10 mm \| `ak4458-dac.md L26` \| `ak4458-dac.md L108` "K0 Fiziksel \\| LQFP-64 pad layout" (aynı dosya) \| ⚠️ tek … |
| 62 | \| 10 \| DSD64/128/256 saatleri \| `ak4458-dac.md L33`-`L35` \| ikinci bağımsız kaynak yok \| ⚠️ tek kaynak \| |
| 70 | \| 18 \| PCM3168A I2C 0x8C \| `pcm3168a-dac-adc.md L54` · `L113` \| README'de I2C adresi yok \| ⚠️ tek kaynak (dosya içi tutarlı) \| |
| 78 | **Sayım:** 24 madde · **6 çelişki** (C-01…C-08 aralığında 8 kayıt; §4'te tamamı) · **9 tek-kaynak/türetme** ⚠️ · **9 eşleşme** ✅. |
| 82 | > Kolonlar: `#` · `İddia A (kaynak+satır)` · `İddia B (kaynak+satır)` · `Fark` · `Etki` · `Karar` · `Etkilediği dosya`. Karar sütunundaki `… |
| 86 | \| C-01 \| `k1-donanim/ak4458-dac.md L12` — "COREMUSIC'ın ana DAC'ı olarak kullanılır" \| `k1-donanim/README.md L58` — "Ana DAC \\| PCM3168… |
| 87 | \| C-02 \| `k1-donanim/pcm3168a-dac-adc.md L12` — "analog giriş … dijital formata dönüştürücüdür" (ADC), `L18` — "32-bit", `L20` — "8kHz – … |
| 88 | \| C-03 \| `k1-donanim/ak4458-dac.md L12` — AK4458 ana DAC; `pcm3168a-dac-adc.md L8` — PCM3168A ADC \| `firmware/i2s-driver.md L53` — "PCM5… |
| 89 | \| C-04 \| `k1-donanim/ak4458-dac.md L121` — "I2C adres: 0x10 (CSN = LOW)" \| `firmware/i2s-driver.md L470` — "# AK4493 için I2C address: 0… |
| 90 | \| C-05 \| `k1-donanim/dac-adc-zinciri.md L23` — "MCLK \\| 256fs = 11.2896MHz / 12.288MHz"; `firmware/i2s-driver.md L117`-`L118` — "MCLK Ra… |
| 91 | \| C-06 \| `k1-donanim/ak4458-dac.md L90` — "18 \\| TDMCLK \\| Giriş \\| TDM clock (MCLK)"; `L46` — "SCK ──▶ AK4458 TDMCLK (Pin 18)" \| `k1… |
| 92 | \| C-07 \| `k1-donanim/ak4458-dac.md L25` — "AVDD = 5V, DVDD = 3.3V, CVDD = 1.2V"; `L84` — "+5V analog besleme" \| `k1-donanim/ak4458-dac.m… |
| 93 | \| C-08 \| Disk + `README.md L287` — "ak4458-dac.md, pcm3168a-dac-adc.md, dac-adc-zinciri.md"; `L305` — "ak4458-dac.md — 7 yaprak"; `L763` … |
| 118 | \| E-02 \| Birincil kaynakta fs oranı yok \| `ak4458-dac.md L80`-`L102` (pin tablosu) ve `L114`-`L123` (durum) arasında oran/çarpan satırı … |
| 119 | \| E-03 \| 22.5792 MHz → 512fs okuması \| `dac-adc-zinciri.md L81`, `L105` değeri veriyor; "512fs" kelimesi geçmiyor \| Türetme işaretli ku… |
| 130 | \| H-01 \| Yanlış ana DAC varsayımı \| `README.md L58` vs `ak4458-dac.md L12` (C-01) \| Firmware yanlış çipe öncelik verir, kanal planı boz… |
| 131 | \| H-02 \| Çip-özel register'ın başka çipe yazılması \| `i2s-driver.md L471`-`L473` (C-03/C-04) \| Ses çıkışı sessiz/bozuk; register harita… |
| 132 | \| H-03 \| Clock oranının yanlış okunması \| `dac-adc-zinciri.md L23` vs `L81` (C-05) \| AK4458 kilitlenmez (slave, `L119`), TDM çerçevesi … |
| 133 | \| H-04 \| MCLK'ın bit clock pine bağlanması \| `ak4458-dac.md L90` vs `dac-adc-zinciri.md L68` (C-06) \| Hiçbir kanal çalışmaz veya bozuk … |
| 134 | \| H-05 \| Besleme rayının yanlış türetilmesi \| `ak4458-dac.md L25` vs `L111` (C-07) \| AVDD'e ±5V uygulanması çipi hasara uğratma riski \… |
| 136 | \| H-07 \| Tek kaynaklı spec'in doğruluk sanılması \| `ak4458-dac.md L22`-`L24` (SNR/THD/Zout) \| Doğrulanmamış ölçüm gibi kullanılır \| §3… |
| 149 | \| 7 \| Türetme/tek-kaynak iddialar \| Uydurma yok; yalnız `⚠️ VERIFICATION REQUIRED` \| ✅ disk \| |
| 323 | \| 10 \| Çelişki/uyarı sayımı \| §4 (8 çelişki) + §12 (`⚠️` satır sayısı) \| sayılar tutarlı \| |

## §13 İkincil Kaynak Satır İndeksi (`README.md`)

> İkincil bağlam kaynağının satır-satır indeksi: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` (805 satır). Boş satırlar atlandı; `|` karakteri `\|` ile kaçırıldı; özet 100 karakterle sınırlı.

| Kaynak satır | Tür | İçerik özeti | Kanıt |
|---|---|---|---|
| L1 | metin | --- | ✅ disk |
| L2 | metin | reference_doc: Freelancer Technical Documentation v1.0 | ✅ disk |
| L3 | metin | title: "CoreMusic — K1 Donanım Layer" | ✅ disk |
| L4 | metin | type: architecture-layer | ✅ disk |
| L5 | metin | category: architecture | ✅ disk |
| L6 | metin | date: 2026-09-20 | ✅ disk |
| L7 | metin | updated: 2026-09-29 | ✅ disk |
| L8 | metin | last_update_note: "3 turlu agent tartışması" | ✅ disk |
| L9 | metin | status: active | ✅ disk |
| L10 | metin | version: 1.0.1 | ✅ disk |
| L11 | metin | authority: Single Source of Truth (SSOT) | ✅ disk |
| L12 | metin | governance: Red Team · Human Mode · Truth Mode | ✅ disk |
| L13 | metin | reference: | ✅ disk |
| L14 | metin | authority: ".ai/CLAUDE.md" | ✅ disk |
| L15 | metin | source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md" | ✅ disk |
| L16 | metin | --- | ✅ disk |
| L18 | baslik | # K1: Donanım Layer | ✅ disk |
| L20 | vurgu | **Katman:** K1 (Donanım Altyapısı) | ✅ disk |
| L21 | vurgu | **Kapsam:** XMOS, DAC, Amplifikatör, Hoparlör, Güç kaynağı | ✅ disk |
| L22 | vurgu | **Sorumlu Agent:** Audio Hardware Engineer | ✅ disk |
| L23 | vurgu | **Bileşen Sayısı:** 120 | ✅ disk |
| L25 | metin | --- | ✅ disk |
| L27 | baslik | ## 1. Genel Bakış | ✅ disk |
| L29 | metin | K1 katmanı, CoreMusic'in fiziksel donanım bileşenlerini içerir. Bu katman, dijital sinyali analog s… | ✅ disk |
| L31 | baslik | ### 1.1 Temel İlkeler | ✅ disk |
| L33 | tablo | \| İlke \| Açıklama \| | ✅ disk |
| L34 | tablo | \|------\|----------\| | ✅ disk |
| L35 | tablo | \| **Bit-Perfect** \| Sinyal zincirinde kayıp yok \| | ✅ disk |
| L36 | tablo | \| **Low THD** \| Toplam Harmonik Bozulma <0.005% \| | ✅ disk |
| L37 | tablo | \| **High SNR** \| Sinyal-Gürültü Oranı >100dB \| | ✅ disk |
| L38 | tablo | \| **8.1 Surround** \| 8 kanal + 1 LFE \| | ✅ disk |
| L39 | tablo | \| **DC-Only** \| Güç kaynağı DC Only \| | ✅ disk |
| L41 | metin | --- | ✅ disk |
| L43 | baslik | ## 2. Bileşen Haritası | ✅ disk |
| L45 | baslik | ### 2.1 USB Audio Interface | ✅ disk |
| L47 | tablo | \| Bileşen \| Model \| Özellik \| | ✅ disk |
| L48 | tablo | \|---------\|-------\|---------\| | ✅ disk |
| L49 | tablo | \| USB Audio \| XMOS XU316 \| USB Audio Class 2.0, 32-bit \| | ✅ disk |
| L50 | tablo | \| USB Interface \| USB-C \| 24-pin, USB 2.0/3.0 \| | ✅ disk |
| L51 | tablo | \| Clock \| 22.5792 MHz \| 44.1kHz family \| | ✅ disk |
| L52 | tablo | \| Clock \| 24.576 MHz \| 48kHz family \| | ✅ disk |
| L54 | baslik | ### 2.2 DAC (Digital-to-Analog Converter) | ✅ disk |
| L56 | tablo | \| Bileşen \| Model \| Kanal \| Bit \| Sample Rate \| | ✅ disk |
| L57 | tablo | \|---------\|-------\|-------\|-----\|-------------\| | ✅ disk |
| L58 | tablo | \| Ana DAC \| PCM3168A \| 6-in/8-out \| 24-bit \| 192kHz \| | ✅ disk |
| L59 | tablo | \| Opsiyonel DAC \| AK4458 \| 8-kanal \| 32-bit \| 768kHz \| | ✅ disk |
| L60 | tablo | \| REDDEDİLMİŞ \| PCM5122 \| 2-kanal \| 32-bit \| — \| | ✅ disk |
| L62 | vurgu | **⚠️ Uyarı:** PCM5122 8.1 surround için yetersizdir (ADR-038). Sadece 2 kanal destekler. | ✅ disk |
| L64 | baslik | ### 2.3 Amplifikatör | ✅ disk |
| L66 | tablo | \| Bileşen \| Model \| Topoloji \| Güç \| THD \| | ✅ disk |
| L67 | tablo | \|---------\|-------\|----------\|-----\|-----\| | ✅ disk |
| L68 | tablo | \| NPN Output \| MJL21194 \| Class AB Darlington \| 50W/kanal \| <0.005% \| | ✅ disk |
| L69 | tablo | \| PNP Output \| MJL21193 \| Class AB Darlington \| 50W/kanal \| <0.005% \| | ✅ disk |
| L71 | baslik | ### 2.4 Güç Kaynağı | ✅ disk |
| L73 | tablo | \| Bileşen \| Model \| Giriş \| Çıkış \| Verim \| | ✅ disk |
| L74 | tablo | \|---------\|-------\|-------\|-------\|-------\| | ✅ disk |
| L75 | tablo | \| Boost Converter \| LM5122 \| 22.2V (6S LiPo) \| ±35V \| %96 \| | ✅ disk |
| L76 | tablo | \| Batarya \| 6S LiPo \| 22.2V nominal \| — \| — \| | ✅ disk |
| L77 | tablo | \| DC Adapter \| 19-24V \| AC/DC \| — \| — \| | ✅ disk |
| L79 | baslik | ### 2.5 Hoparlör Matrisi (8.1 Surround) | ✅ disk |
| L81 | tablo | \| Kanal \| Hoparlör \| Frekans \| Konum \| | ✅ disk |
| L82 | tablo | \|-------\|----------\|---------\|-------\| | ✅ disk |
| L83 | tablo | \| CH1 \| Front Left \| 20Hz-20kHz \| Ön sol \| | ✅ disk |
| L84 | tablo | \| CH2 \| Front Right \| 20Hz-20kHz \| Ön sağ \| | ✅ disk |
| L85 | tablo | \| CH3 \| Center \| 100Hz-8kHz \| Merkez \| | ✅ disk |
| L86 | tablo | \| CH4 \| LFE (Sub) \| 20Hz-120Hz \| Subwoofer \| | ✅ disk |
| L87 | tablo | \| CH5 \| Surround Left \| 100Hz-16kHz \| Arka sol \| | ✅ disk |
| L88 | tablo | \| CH6 \| Surround Right \| 100Hz-16kHz \| Arka sağ \| | ✅ disk |
| L89 | tablo | \| CH7 \| Rear Left \| 100Hz-16kHz \| Arka sol \| | ✅ disk |
| L90 | tablo | \| CH8 \| Rear Right \| 100Hz-16kHz \| Arka sağ \| | ✅ disk |
| L92 | metin | --- | ✅ disk |
| L94 | baslik | ## 3. PCM3168A DAC Detayı | ✅ disk |
| L96 | baslik | ### 3.1 Pin Out | ✅ disk |
| L98 | kod | ``` | ✅ disk |
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
| L115 | kod | ``` | ✅ disk |
| L117 | baslik | ### 3.2 I2S Konfigürasyonu | ✅ disk |
| L119 | tablo | \| Parametre \| Değer \| | ✅ disk |
| L120 | tablo | \|-----------\|-------\| | ✅ disk |
| L121 | tablo | \| Sample Rate \| 48kHz (default) \| | ✅ disk |
| L122 | tablo | \| Bit Depth \| 24-bit \| | ✅ disk |
| L123 | tablo | \| I2S Mode \| Standard I2S \| | ✅ disk |
| L124 | tablo | \| System Clock \| 256fs = 12.288MHz \| | ✅ disk |
| L125 | tablo | \| BCK \| 64fs = 3.072MHz \| | ✅ disk |
| L126 | tablo | \| LRCK \| 48kHz \| | ✅ disk |
| L128 | baslik | ### 3.3 Analogy Output Devresi | ✅ disk |
| L130 | kod | ``` | ✅ disk |
| L131 | kod | PCM3168A OUTL1 → I/V Resistor (1kΩ) → Low-Pass Filter (20kHz) → Differential Driver → Amplifier Inp… | ✅ disk |
| L132 | kod | ``` | ✅ disk |
| L134 | metin | --- | ✅ disk |
| L136 | baslik | ## 4. Class AB Amplifikatör Detayı | ✅ disk |
| L138 | baslik | ### 4.1 Tek Kanal Devre Şeması | ✅ disk |
| L140 | kod | ``` | ✅ disk |
| L141 | kod | +35V (PVDD) | ✅ disk |
| L142 | kod | │ | ✅ disk |
| L143 | kod | ┌────┴────┐ | ✅ disk |
| L144 | kod | │ Q15 │ MJL21194 (NPN Output) | ✅ disk |
| L145 | kod | │ NPN │ | ✅ disk |
| L146 | kod | Input ─────┤ Q16 ├──── Output → Hoparlör | ✅ disk |
| L147 | kod | (Diff) │ BD139 │ | ✅ disk |
| L148 | kod | │ VAS │ | ✅ disk |
| L149 | kod | │ Q17 │ MJL21193 (PNP Output) | ✅ disk |
| L150 | kod | │ PNP │ | ✅ disk |
| L151 | kod | └────┬────┘ | ✅ disk |
| L152 | kod | │ | ✅ disk |
| L153 | kod | -35V (PVSS) | ✅ disk |
| L155 | kod | Bias Network: | ✅ disk |
| L156 | kod | Q1 (BC546B): Diferansiyel çift giriş | ✅ disk |
| L157 | kod | Q2 (BC546B): Diferansiyel çift giriş | ✅ disk |
| L158 | kod | Q5 (BC556B): Akım havuzu | ✅ disk |
| L159 | kod | Q9 (KSC3503): VAS (Voltage Amplifier Stage) | ✅ disk |
| L160 | kod | Q10 (BD139): Vbe çarpımı (bias spreader) | ✅ disk |
| L161 | kod | ``` | ✅ disk |
| L163 | baslik | ### 4.2 Bias Ayar Prosedürü | ✅ disk |
| L165 | tablo | \| Adım \| İşlem \| Değer \| | ✅ disk |
| L166 | tablo | \|------\|-------\|-------\| | ✅ disk |
| L167 | tablo | \| 1 \| Güç kaynağı ayarla \| ±35V DC \| | ✅ disk |
| L168 | tablo | \| 2 \| Multimetre çıkışa bağla \| DC offset ölç \| | ✅ disk |
| L169 | tablo | \| 3 \| Bias potansiyometresi ayarla \| 0V DC offset hedefle \| | ✅ disk |
| L170 | tablo | \| 4 \| Sıcaklık stabilizasyonu \| 5-10 dk bekle \| | ✅ disk |
| L171 | tablo | \| 5 \| Son kontrol \| <0.5V DC offset \| | ✅ disk |
| L173 | baslik | ### 4.3 Termal Hesaplama | ✅ disk |
| L175 | tablo | \| Parametre \| Değer \| | ✅ disk |
| L176 | tablo | \|-----------\|-------\| | ✅ disk |
| L177 | tablo | \| Güç (kanal başına) \| 50W @ 8Ω \| | ✅ disk |
| L178 | tablo | \| Verimlilik \| ~%65 (Class AB) \| | ✅ disk |
| L179 | tablo | \| Isı (kanal başına) \| ~17.5W \| | ✅ disk |
| L180 | tablo | \| Toplam ısı (8 kanal) \| ~140W \| | ✅ disk |
| L181 | tablo | \| Heatsink gereksinimi \| >140W/C° thermal resistance \| | ✅ disk |
| L182 | tablo | \| Fan gereksinimi \| 80mm PWM, >50 CFM \| | ✅ disk |
| L184 | metin | --- | ✅ disk |
| L186 | baslik | ## 5. XMOS XU316 Detayı | ✅ disk |
| L188 | baslik | ### 5.1 Blok Diyagramı | ✅ disk |
| L190 | kod | ``` | ✅ disk |
| L191 | kod | USB 2.0 ──→ XMOS XU316 ──→ I2S ──→ PCM3168A | ✅ disk |
| L192 | kod | │ | ✅ disk |
| L193 | kod | ├→ Clock Generator | ✅ disk |
| L194 | kod | ├→ USB Audio Class 2.0 | ✅ disk |
| L195 | kod | ├→ DSP Processing | ✅ disk |
| L196 | kod | └→ Control Interface | ✅ disk |
| L197 | kod | ``` | ✅ disk |
| L199 | baslik | ### 5.2 XMOS Kaynak Kullanımı | ✅ disk |
| L201 | tablo | \| Kaynak \| Kullanım \| | ✅ disk |
| L202 | tablo | \|--------\|----------\| | ✅ disk |
| L203 | tablo | \| Logical Cores \| 8 (4 x 2 tile) \| | ✅ disk |
| L204 | tablo | \| MIPS \| ~2000 (toplam) \| | ✅ disk |
| L205 | tablo | \| RAM \| 512KB (tile 0+1) \| | ✅ disk |
| L206 | tablo | \| Flash \| 16MB (external) \| | ✅ disk |
| L207 | tablo | \| USB PHY \| High-speed 480Mbps \| | ✅ disk |
| L209 | metin | --- | ✅ disk |
| L211 | baslik | ## 6. PCB Tasarım Kuralları | ✅ disk |
| L213 | tablo | \| Parametre \| Değer \| | ✅ disk |
| L214 | tablo | \|-----------\|-------\| | ✅ disk |
| L215 | tablo | \| Layer \| 6-layer stackup \| | ✅ disk |
| L216 | tablo | \| Copper (top/bottom) \| 2oz \| | ✅ disk |
| L217 | tablo | \| Copper (inner) \| 1oz \| | ✅ disk |
| L218 | tablo | \| Finish \| ENIG \| | ✅ disk |
| L219 | tablo | \| Min trace \| 4mil \| | ✅ disk |
| L220 | tablo | \| Min via \| 8mil drill, 16mil pad \| | ✅ disk |
| L221 | tablo | \| USB Impedans \| 90Ω differential \| | ✅ disk |
| L222 | tablo | \| I2S Impedans \| 50Ω single-ended \| | ✅ disk |
| L223 | tablo | \| Ground \| Star ground topology \| | ✅ disk |
| L224 | tablo | \| Thermal \| Thermal vias under power components \| | ✅ disk |
| L226 | baslik | ### 6.1 Stackup | ✅ disk |
| L228 | kod | ``` | ✅ disk |
| L229 | kod | Layer 1: Signal (top) — Components, traces | ✅ disk |
| L230 | kod | Layer 2: Ground — Continuous ground plane | ✅ disk |
| L231 | kod | Layer 3: Signal — I2S, control signals | ✅ disk |
| L232 | kod | Layer 4: Power — +35V, -35V, +3.3V, +5V | ✅ disk |
| L233 | kod | Layer 5: Ground — Continuous ground plane | ✅ disk |
| L234 | kod | Layer 6: Signal (bottom) — Components, traces | ✅ disk |
| L235 | kod | ``` | ✅ disk |
| L237 | metin | --- | ✅ disk |
| L239 | baslik | ## 7. BOM Maliyet Analizi | ✅ disk |
| L241 | tablo | \| Kategori \| Bileşen Sayısı \| Toplam Maliyet \| | ✅ disk |
| L242 | tablo | \|----------\|---------------\|---------------\| | ✅ disk |
| L243 | tablo | \| USB Audio (XMOS) \| 3 \| $15.00 \| | ✅ disk |
| L244 | tablo | \| DAC (PCM3168A) \| 15 \| $25.00 \| | ✅ disk |
| L245 | tablo | \| Amplifikatör (8 kanal) \| 120 \| $85.00 \| | ✅ disk |
| L246 | tablo | \| Güç Kaynağı \| 35 \| $45.00 \| | ✅ disk |
| L247 | tablo | \| Pasif Bileşenler \| 800+ \| $180.00 \| | ✅ disk |
| L248 | tablo | \| Konnektörler \| 25 \| $30.00 \| | ✅ disk |
| L249 | tablo | \| PCB (6-layer) \| 1 \| $50.00 \| | ✅ disk |
| L250 | tablo | \| **TOPLAM** \| **~1,000** \| **~$430** \| | ✅ disk |
| L252 | metin | --- | ✅ disk |
| L254 | baslik | ## 8. İlgili Dosyalar | ✅ disk |
| L256 | tablo | \| Dosya \| Amaç \| | ✅ disk |
| L257 | tablo | \|-------\|------\| | ✅ disk |
| L258 | tablo | \| `architecture/k1-donanim/README.md` \| Bu dosya \| | ✅ disk |
| L259 | tablo | \| `architecture/k1-donanim/xmos-xu316.md` \| XMOS detayı \| | ✅ disk |
| L260 | tablo | \| `architecture/k1-donanim/pcm3168a.md` \| DAC detayı \| | ✅ disk |
| L261 | tablo | \| `architecture/k1-donanim/ak4458.md` \| High-end DAC \| | ✅ disk |
| L262 | tablo | \| `architecture/k1-donanim/class-ab-amplifier.md` \| Amplifikatör devresi \| | ✅ disk |
| L263 | tablo | \| `architecture/k1-donanim/speaker-matrix.md` \| Hoparlör konfigürasyonu \| | ✅ disk |
| L264 | tablo | \| `architecture/k1-donanim/bom-cost.md` \| BOM maliyet \| | ✅ disk |
| L265 | tablo | \| `architecture/k16-class-ab/README.md` \| K16 Class AB \| | ✅ disk |
| L266 | tablo | \| `architecture/k17-guc-kaynagi/README.md` \| K17 Güç kaynağı \| | ✅ disk |
| L268 | metin | --- | ✅ disk |
| L270 | baslik | ## 9. İlgili ADR'ler | ✅ disk |
| L272 | tablo | \| ADR \| Konu \| | ✅ disk |
| L273 | tablo | \|-----\|------\| | ✅ disk |
| L274 | tablo | \| ADR-038 \| PCM3168A (PCM5122 REDDEDİLMİŞ) \| | ✅ disk |
| L275 | tablo | \| ADR-089 \| Class AB Amplifikatör + 6S LiPo + ±35V Boost \| | ✅ disk |
| L277 | metin | --- | ✅ disk |
| L279 | baslik | ## Alt Katman Şeması (K1.a.b.c) | ✅ disk |
| L281 | vurgu | > **Şema kuralları (2026-09-24 · 3 turlu agent tartışması):** `K1` → `K1.a` (2. katman, 12 düğüm) →… | ✅ disk |
| L283 | baslik | ### K1 Şema Özeti | ✅ disk |
| L285 | tablo | \| 2. Katman \| Ad \| 3. Katman \| 4. Kanıtlı Yaprak \| Birincil Kanıt \| | ✅ disk |
| L286 | tablo | \|-----------\|----\|-----------\|-------------------\|----------------\| | ✅ disk |
| L287 | tablo | \| K1.1 \| DAC/ADC Zinciri \| 3 \| 23 \| ak4458-dac.md, pcm3168a-dac-adc.md, dac-adc-zinciri.md \| | ✅ disk |
| L288 | tablo | \| K1.2 \| Amplifikatör Aşamaları \| 6 \| 66 \| diff-pair/vas/class-ab/output/feedback/mjle md \| | ✅ disk |
| L289 | tablo | \| K1.3 \| Analog Sinyal Yolu \| 1 \| 17 \| analog-sinyal-yolu.md \| | ✅ disk |
| L290 | tablo | \| K1.4 \| Güç Kaynağı & Koruma \| 2 \| 23 \| guc-kaynagi-analog.md, koruma-devreleri.md \| | ✅ disk |
| L291 | tablo | \| K1.5 \| Dijital Arayüzler \| 3 \| 32 \| i2s-interface.md, usb-audio.md, xmos-xu316.md \| | ✅ disk |
| L292 | tablo | \| K1.6 \| PCB & Termal \| 2 \| 21 \| pcb-tasarim.md, termal-yonetim.md \| | ✅ disk |
| L293 | tablo | \| K1.7 \| Konnektör & Hoparlör \| 2 \| 24 \| konnektorler.md, hoparlor-dizilimi.md \| | ✅ disk |
| L294 | tablo | \| K1.8 \| Firmware (K1.f alt katmanı) \| 8 \| 123 \| firmware/ — 8 MD \| | ✅ disk |
| L295 | tablo | \| K1.9 \| Bileşen Haritası & BOM \| 1 \| 20 \| README.md §2-§7 \| | ✅ disk |
| L296 | tablo | \| K1.10 \| index.md \| 1 \| 5 \| index.md \| | ✅ disk |
| L297 | tablo | \| K1.11 \| CLAUDE.md Guardrails \| 1 \| 4 \| CLAUDE.md \| | ✅ disk |
| L298 | tablo | \| K1.12 \| README Genel Bakış \| 1 \| 2 \| README.md §1 \| | ✅ disk |
| L299 | tablo | \| **TOPLAM** \| \| **31** \| **360** \| \| | ✅ disk |
| L301 | baslik | ### K1.1 — DAC/ADC Zinciri | ✅ disk |
| L303 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L304 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L305 | tablo | \| **K1.1.1** \| AK4458 DAC (3. katman) \| ak4458-dac.md — 7 yaprak \| | ✅ disk |
| L306 | tablo | \| K1.1.1.1 \| Genel Bakış \| ak4458-dac.md L10 \| | ✅ disk |
| L307 | tablo | \| K1.1.1.2 \| Teknik Spesifikasyonlar \| ak4458-dac.md L14 \| | ✅ disk |
| L308 | tablo | \| K1.1.1.3 \| DSD Modu Destekleri \| ak4458-dac.md L29 \| | ✅ disk |
| L309 | tablo | \| K1.1.1.4 \| Devre Tasarımı \| ak4458-dac.md L39 \| | ✅ disk |
| L310 | tablo | \| K1.1.1.5 \| Pin Konfigürasyonu (Önemli Pinler) \| ak4458-dac.md L80 \| | ✅ disk |
| L311 | tablo | \| K1.1.1.6 \| Temel Bağlantılar \| ak4458-dac.md L41 \| | ✅ disk |
| L312 | tablo | \| K1.1.1.7 \| Kondansatör ve Direnç Değerleri \| ak4458-dac.md L68 \| | ✅ disk |
| L313 | tablo | \| **K1.1.2** \| PCM3168A DAC/ADC (3. katman) \| pcm3168a-dac-adc.md — 6 yaprak \| | ✅ disk |
| L314 | tablo | \| K1.1.2.1 \| Genel Bakış \| pcm3168a-dac-adc.md L10 \| | ✅ disk |
| L315 | tablo | \| K1.1.2.2 \| Teknik Spesifikasyonlar \| pcm3168a-dac-adc.md L14 \| | ✅ disk |
| L316 | tablo | \| K1.1.2.3 \| Devre Tasarımı \| pcm3168a-dac-adc.md L29 \| | ✅ disk |
| L317 | tablo | \| K1.1.2.4 \| Pin Konfigürasyonu \| pcm3168a-dac-adc.md L68 \| | ✅ disk |
| L318 | tablo | \| K1.1.2.5 \| Temel Bağlantılar \| pcm3168a-dac-adc.md L31 \| | ✅ disk |
| L319 | tablo | \| K1.1.2.6 \| Filtre ve Kondansatörler \| pcm3168a-dac-adc.md L57 \| | ✅ disk |
| L320 | tablo | \| **K1.1.3** \| DAC-ADC Zinciri (3. katman) \| dac-adc-zinciri.md — 10 yaprak \| | ✅ disk |
| L321 | tablo | \| K1.1.3.1 \| Genel Bakış \| dac-adc-zinciri.md L10 \| | ✅ disk |
| L322 | tablo | \| K1.1.3.2 \| Teknik Spesifikasyonlar \| dac-adc-zinciri.md L14 \| | ✅ disk |
| L323 | tablo | \| K1.1.3.3 \| Sinyal Yolu Diyagramı \| dac-adc-zinciri.md L27 \| | ✅ disk |
| L324 | tablo | \| K1.1.3.4 \| Clock Synchronization \| dac-adc-zinciri.md L56 \| | ✅ disk |
| L325 | tablo | \| K1.1.3.5 \| EMI Filtreleme \| dac-adc-zinciri.md L109 \| | ✅ disk |
| L326 | tablo | \| K1.1.3.6 \| Empedans Eşleşme \| dac-adc-zinciri.md L123 \| | ✅ disk |
| L327 | tablo | \| K1.1.3.7 \| Bileşen Değerleri \| dac-adc-zinciri.md L131 \| | ✅ disk |
| L328 | tablo | \| K1.1.3.8 \| Master/Slave Konfigürasyonu \| dac-adc-zinciri.md L58 \| | ✅ disk |
| L329 | tablo | \| K1.1.3.9 \| Clock Accuracy \| dac-adc-zinciri.md L101 \| | ✅ disk |
| L330 | tablo | \| K1.1.3.10 \| I2S Hat Filtresi \| dac-adc-zinciri.md L111 \| | ✅ disk |
| L332 | baslik | ### K1.2 — Amplifikatör Aşamaları | ✅ disk |
| L334 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L335 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L336 | tablo | \| **K1.2.1** \| Diff Pair Input (3. katman) \| diff-pair-input.md — 11 yaprak \| | ✅ disk |
| L337 | tablo | \| K1.2.1.1 \| Genel Bakış \| diff-pair-input.md L10 \| | ✅ disk |
| L338 | tablo | \| K1.2.1.2 \| Teknik Spesifikasyonlar \| diff-pair-input.md L14 \| | ✅ disk |
| L339 | tablo | \| K1.2.1.3 \| Devre Şeması \| diff-pair-input.md L28 \| | ✅ disk |
| L340 | tablo | \| K1.2.1.4 \| Bias Current Hesaplaması \| diff-pair-input.md L59 \| | ✅ disk |
| L341 | tablo | \| K1.2.1.5 \| CMRR Analizi \| diff-pair-input.md L80 \| | ✅ disk |
| L342 | tablo | \| K1.2.1.6 \| Gürültü Analizi \| diff-pair-input.md L102 \| | ✅ disk |
| L343 | tablo | \| K1.2.1.7 \| Tail Current \| diff-pair-input.md L61 \| | ✅ disk |
| L344 | tablo | \| K1.2.1.8 \| Operating Point \| diff-pair-input.md L69 \| | ✅ disk |
| L345 | tablo | \| K1.2.1.9 \| CMRR Formülü \| diff-pair-input.md L82 \| | ✅ disk |
| L346 | tablo | \| K1.2.1.10 \| hesaplama \| diff-pair-input.md L93 \| | ✅ disk |
| L347 | tablo | \| K1.2.1.11 \| Giriş Gürültüsü Kaynakları \| diff-pair-input.md L104 \| | ✅ disk |
| L348 | tablo | \| **K1.2.2** \| VAS Stage (3. katman) \| vas-stage.md — 11 yaprak \| | ✅ disk |
| L349 | tablo | \| K1.2.2.1 \| Genel Bakış \| vas-stage.md L10 \| | ✅ disk |
| L350 | tablo | \| K1.2.2.2 \| Teknik Spesifikasyonlar \| vas-stage.md L14 \| | ✅ disk |
| L351 | tablo | \| K1.2.2.3 \| Devre Şeması \| vas-stage.md L28 \| | ✅ disk |
| L352 | tablo | \| K1.2.2.4 \| Miller Compensation Analizi \| vas-stage.md L62 \| | ✅ disk |
| L353 | tablo | \| K1.2.2.5 \| Bileşen Değerleri \| vas-stage.md L104 \| | ✅ disk |
| L354 | tablo | \| K1.2.2.6 \| Frekans Tepkisi \| vas-stage.md L114 \| | ✅ disk |
| L355 | tablo | \| K1.2.2.7 \| Stabilite Analizi \| vas-stage.md L136 \| | ✅ disk |
| L356 | tablo | \| K1.2.2.8 \| Neden Miller Compensation? \| vas-stage.md L64 \| | ✅ disk |
| L357 | tablo | \| K1.2.2.9 \| Miller Etkisi Formülü \| vas-stage.md L77 \| | ✅ disk |
| L358 | tablo | \| K1.2.2.10 \| Dominant Pole \| vas-stage.md L94 \| | ✅ disk |
| L359 | tablo | \| K1.2.2.11 \| Phase Margin Hesabı \| vas-stage.md L138 \| | ✅ disk |
| L360 | tablo | \| **K1.2.3** \| Class-AB Amplifikatör (3. katman) \| class-ab-amplifikator.md — 8 yaprak \| | ✅ disk |
| L361 | tablo | \| K1.2.3.1 \| Genel Bakış \| class-ab-amplifikator.md L10 \| | ✅ disk |
| L362 | tablo | \| K1.2.3.2 \| Teknik Spesifikasyonlar \| class-ab-amplifikator.md L14 \| | ✅ disk |
| L363 | tablo | \| K1.2.3.3 \| Devre Şeması — Genel Görünüm \| class-ab-amplifikator.md L28 \| | ✅ disk |
| L364 | tablo | \| K1.2.3.4 \| Devre Tasarımı — Detaylı \| class-ab-amplifikator.md L50 \| | ✅ disk |
| L365 | tablo | \| K1.2.3.5 \| Bileşen Listesi \| class-ab-amplifikator.md L129 \| | ✅ disk |
| L366 | tablo | \| K1.2.3.6 \| Differential Pair Input Stage \| class-ab-amplifikator.md L52 \| | ✅ disk |
| L367 | tablo | \| K1.2.3.7 \| Voltage Amplification Stage (VAS) \| class-ab-amplifikator.md L78 \| | ✅ disk |
| L368 | tablo | \| K1.2.3.8 \| Push-Pull Output Stage \| class-ab-amplifikator.md L101 \| | ✅ disk |
| L369 | tablo | \| **K1.2.4** \| Output Stage (3. katman) \| output-stage.md — 15 yaprak \| | ✅ disk |
| L370 | tablo | \| K1.2.4.1 \| Genel Bakış \| output-stage.md L10 \| | ✅ disk |
| L371 | tablo | \| K1.2.4.2 \| Teknik Spesifikasyonlar \| output-stage.md L14 \| | ✅ disk |
| L372 | tablo | \| K1.2.4.3 \| Push-Pull Konfigürasyon \| output-stage.md L26 \| | ✅ disk |
| L373 | tablo | \| K1.2.4.4 \| Darlington Configuration \| output-stage.md L65 \| | ✅ disk |
| L374 | tablo | \| K1.2.4.5 \| Thermal Tracking \| output-stage.md L108 \| | ✅ disk |
| L375 | tablo | \| K1.2.4.6 \| Bileşen Değerleri \| output-stage.md L146 \| | ✅ disk |
| L376 | tablo | \| K1.2.4.7 \| Akım Yolu Analizi \| output-stage.md L159 \| | ✅ disk |
| L377 | tablo | \| K1.2.4.8 \| Tek transistor (Single-ended output) \| output-stage.md L28 \| | ✅ disk |
| L378 | tablo | \| K1.2.4.9 \| Push-Pull Output \| output-stage.md L35 \| | ✅ disk |
| L379 | tablo | \| K1.2.4.10 \| Neden Darlington? \| output-stage.md L67 \| | ✅ disk |
| L380 | tablo | \| K1.2.4.11 \| Darlington Emitters Follower \| output-stage.md L82 \| | ✅ disk |
| L381 | tablo | \| K1.2.4.12 \| Crossover Distortion Sorunu \| output-stage.md L110 \| | ✅ disk |
| L382 | tablo | \| K1.2.4.13 \| Çözüm: Thermal Tracking \| output-stage.md L120 \| | ✅ disk |
| L383 | tablo | \| K1.2.4.14 \| Pozitif Yarım Döngü (MJL21194 Active) \| output-stage.md L161 \| | ✅ disk |
| L384 | tablo | \| K1.2.4.15 \| Negatif Yarım Döngü (MJL21193 Active) \| output-stage.md L170 \| | ✅ disk |
| L385 | tablo | \| **K1.2.5** \| Feedback Network (3. katman) \| feedback-network.md — 11 yaprak \| | ✅ disk |
| L386 | tablo | \| K1.2.5.1 \| Genel Bakış \| feedback-network.md L10 \| | ✅ disk |
| L387 | tablo | \| K1.2.5.2 \| Teknik Spesifikasyonlar \| feedback-network.md L14 \| | ✅ disk |
| L388 | tablo | \| K1.2.5.3 \| Devre Şeması \| feedback-network.md L26 \| | ✅ disk |
| L389 | tablo | \| K1.2.5.4 \| Kazanç Hesaplaması \| feedback-network.md L58 \| | ✅ disk |
| L390 | tablo | \| K1.2.5.5 \| Frequency Compensation \| feedback-network.md L82 \| | ✅ disk |
| L391 | tablo | \| K1.2.5.6 \| Bileşen Değerleri \| feedback-network.md L117 \| | ✅ disk |
| L392 | tablo | \| K1.2.5.7 \| Empedans Eşleşme \| feedback-network.md L126 \| | ✅ disk |
| L393 | tablo | \| K1.2.5.8 \| Kapalı Devre Kazancı \| feedback-network.md L60 \| | ✅ disk |
| L394 | tablo | \| K1.2.5.9 \| Distorsiyon Azaltma \| feedback-network.md L73 \| | ✅ disk |
| L395 | tablo | \| K1.2.5.10 \| Bode Plot \| feedback-network.md L84 \| | ✅ disk |
| L396 | tablo | \| K1.2.5.11 \| Stabilite Kriterleri \| feedback-network.md L108 \| | ✅ disk |
| L397 | tablo | \| **K1.2.6** \| MJL21194/93 Output Transistör (3. katman) \| mjle21194-93.md — 10 yaprak \| | ✅ disk |
| L398 | tablo | \| K1.2.6.1 \| Genel Bakış \| mjle21194-93.md L10 \| | ✅ disk |
| L399 | tablo | \| K1.2.6.2 \| Teknik Spesifikasyonlar \| mjle21194-93.md L14 \| | ✅ disk |
| L400 | tablo | \| K1.2.6.3 \| DC Karakteristikleri (Tipik @ 25°C) \| mjle21194-93.md L32 \| | ✅ disk |
| L401 | tablo | \| K1.2.6.4 \| AC Karakteristikleri (Tipik @ 25°C) \| mjle21194-93.md L42 \| | ✅ disk |
| L402 | tablo | \| K1.2.6.5 \| Devre Bağlantıları \| mjle21194-93.md L51 \| | ✅ disk |
| L403 | tablo | \| K1.2.6.6 \| Termal Hesaplamalar \| mjle21194-93.md L115 \| | ✅ disk |
| L404 | tablo | \| K1.2.6.7 \| Push-Pull Konfigürasyon \| mjle21194-93.md L53 \| | ✅ disk |
| L405 | tablo | \| K1.2.6.8 \| Thermal Tracking (Bias Transistörleri) \| mjle21194-93.md L93 \| | ✅ disk |
| L406 | tablo | \| K1.2.6.9 \| Güç Tüketimi (Tipik Usage) \| mjle21194-93.md L117 \| | ✅ disk |
| L407 | tablo | \| K1.2.6.10 \| Soğutucu Gereksinimi \| mjle21194-93.md L127 \| | ✅ disk |
| L409 | baslik | ### K1.3 — Analog Sinyal Yolu | ✅ disk |
| L411 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L412 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L413 | tablo | \| **K1.3.1** \| Analog Sinyal Yolu (3. katman) \| analog-sinyal-yolu.md — 17 yaprak \| | ✅ disk |
| L414 | tablo | \| K1.3.1.1 \| Genel Bakış \| analog-sinyal-yolu.md L10 \| | ✅ disk |
| L415 | tablo | \| K1.3.1.2 \| Teknik Spesifikasyonlar \| analog-sinyal-yolu.md L14 \| | ✅ disk |
| L416 | tablo | \| K1.3.1.3 \| Sinyal Yolu Diyagramı \| analog-sinyal-yolu.md L28 \| | ✅ disk |
| L417 | tablo | \| K1.3.1.4 \| Aşama 1: Giriş Filtresi \| analog-sinyal-yolu.md L52 \| | ✅ disk |
| L418 | tablo | \| K1.3.1.5 \| Aşama 2: Differential Pair \| analog-sinyal-yolu.md L79 \| | ✅ disk |
| L419 | tablo | \| K1.3.1.6 \| Aşama 3: VAS Stage \| analog-sinyal-yolu.md L91 \| | ✅ disk |
| L420 | tablo | \| K1.3.1.7 \| Aşama 4: Output Stage \| analog-sinyal-yolu.md L103 \| | ✅ disk |
| L421 | tablo | \| K1.3.1.8 \| Aşama 5: Feedback Network \| analog-sinyal-yolu.md L112 \| | ✅ disk |
| L422 | tablo | \| K1.3.1.9 \| Impedance Matching \| analog-sinyal-yolu.md L121 \| | ✅ disk |
| L423 | tablo | \| K1.3.1.10 \| EMI Filtering \| analog-sinyal-yolu.md L162 \| | ✅ disk |
| L424 | tablo | \| K1.3.1.11 \| Bileşen Değerleri \| analog-sinyal-yolu.md L182 \| | ✅ disk |
| L425 | tablo | \| K1.3.1.12 \| Differential Input Filter \| analog-sinyal-yolu.md L54 \| | ✅ disk |
| L426 | tablo | \| K1.3.1.13 \| Input Impedance \| analog-sinyal-yolu.md L123 \| | ✅ disk |
| L427 | tablo | \| K1.3.1.14 \| Inter-stage Impedance \| analog-sinyal-yolu.md L133 \| | ✅ disk |
| L428 | tablo | \| K1.3.1.15 \| Output Impedance \| analog-sinyal-yolu.md L147 \| | ✅ disk |
| L429 | tablo | \| K1.3.1.16 \| Input EMI \| analog-sinyal-yolu.md L164 \| | ✅ disk |
| L430 | tablo | \| K1.3.1.17 \| Output EMI \| analog-sinyal-yolu.md L173 \| | ✅ disk |
| L432 | baslik | ### K1.4 — Güç Kaynağı & Koruma | ✅ disk |
| L434 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L435 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L436 | tablo | \| **K1.4.1** \| Analog Güç Kaynağı (3. katman) \| guc-kaynagi-analog.md — 9 yaprak \| | ✅ disk |
| L437 | tablo | \| K1.4.1.1 \| Genel Bakış \| guc-kaynagi-analog.md L10 \| | ✅ disk |
| L438 | tablo | \| K1.4.1.2 \| Teknik Spesifikasyonlar \| guc-kaynagi-analog.md L14 \| | ✅ disk |
| L439 | tablo | \| K1.4.1.3 \| Güç Topolojisi \| guc-kaynagi-analog.md L28 \| | ✅ disk |
| L440 | tablo | \| K1.4.1.4 \| Devre Tasarımı \| guc-kaynagi-analog.md L71 \| | ✅ disk |
| L441 | tablo | \| K1.4.1.5 \| Bileşen Listesi \| guc-kaynagi-analog.md L110 \| | ✅ disk |
| L442 | tablo | \| K1.4.1.6 \| Ripple Analizi \| guc-kaynagi-analog.md L123 \| | ✅ disk |
| L443 | tablo | \| K1.4.1.7 \| Koruma Devreleri \| guc-kaynagi-analog.md L138 \| | ✅ disk |
| L444 | tablo | \| K1.4.1.8 \| LM5122 Dual Boost Converter \| guc-kaynagi-analog.md L73 \| | ✅ disk |
| L445 | tablo | \| K1.4.1.9 \| Voltaj Ayarı \| guc-kaynagi-analog.md L95 \| | ✅ disk |
| L446 | tablo | \| **K1.4.2** \| Koruma Devreleri (3. katman) \| koruma-devreleri.md — 14 yaprak \| | ✅ disk |
| L447 | tablo | \| K1.4.2.1 \| Genel Bakış \| koruma-devreleri.md L10 \| | ✅ disk |
| L448 | tablo | \| K1.4.2.2 \| Teknik Spesifikasyonlar \| koruma-devreleri.md L14 \| | ✅ disk |
| L449 | tablo | \| K1.4.2.3 \| DC Offset Koruması \| koruma-devreleri.md L25 \| | ✅ disk |
| L450 | tablo | \| K1.4.2.4 \| Overcurrent Koruması \| koruma-devreleri.md L69 \| | ✅ disk |
| L451 | tablo | \| K1.4.2.5 \| Thermal Shutdown \| koruma-devreleri.md L107 \| | ✅ disk |
| L452 | tablo | \| K1.4.2.6 \| Short Circuit Koruması \| koruma-devreleri.md L141 \| | ✅ disk |
| L453 | tablo | \| K1.4.2.7 \| Bileşen Değerleri \| koruma-devreleri.md L157 \| | ✅ disk |
| L454 | tablo | \| K1.4.2.8 \| Devre Şeması (DC Offset) \| koruma-devreleri.md L27 \| | ✅ disk |
| L455 | tablo | \| K1.4.2.9 \| Çalışma Prensibi \| koruma-devreleri.md L58 \| | ✅ disk |
| L456 | tablo | \| K1.4.2.10 \| Devre Şeması (Overcurrent) \| koruma-devreleri.md L71 \| | ✅ disk |
| L457 | tablo | \| K1.4.2.11 \| Current Limiting Profile \| koruma-devreleri.md L98 \| | ✅ disk |
| L458 | tablo | \| K1.4.2.12 \| Devre Şeması (Thermal) \| koruma-devreleri.md L109 \| | ✅ disk |
| L459 | tablo | \| K1.4.2.13 \| Thermal Profile \| koruma-devreleri.md L131 \| | ✅ disk |
| L460 | tablo | \| K1.4.2.14 \| Çift Koruma \| koruma-devreleri.md L143 \| | ✅ disk |
| L462 | baslik | ### K1.5 — Dijital Arayüzler | ✅ disk |
| L464 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L465 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L466 | tablo | \| **K1.5.1** \| I2S Interface (3. katman) \| i2s-interface.md — 13 yaprak \| | ✅ disk |
| L467 | tablo | \| K1.5.1.1 \| Genel Bakış \| i2s-interface.md L10 \| | ✅ disk |
| L468 | tablo | \| K1.5.1.2 \| Teknik Spesifikasyonlar \| i2s-interface.md L14 \| | ✅ disk |
| L469 | tablo | \| K1.5.1.3 \| I2S Sinyalleri \| i2s-interface.md L28 \| | ✅ disk |
| L470 | tablo | \| K1.5.1.4 \| I2S Timing Diagram \| i2s-interface.md L49 \| | ✅ disk |
| L471 | tablo | \| K1.5.1.5 \| Master/Slave Mode \| i2s-interface.md L70 \| | ✅ disk |
| L472 | tablo | \| K1.5.1.6 \| Multi-Channel Configuration \| i2s-interface.md L105 \| | ✅ disk |
| L473 | tablo | \| K1.5.1.7 \| Impedans ve Drive \| i2s-interface.md L135 \| | ✅ disk |
| L474 | tablo | \| K1.5.1.8 \| Bileşen Değerleri \| i2s-interface.md L159 \| | ✅ disk |
| L475 | tablo | \| K1.5.1.9 \| XMOS as Master \| i2s-interface.md L72 \| | ✅ disk |
| L476 | tablo | \| K1.5.1.10 \| DAC/ADC as Slave \| i2s-interface.md L87 \| | ✅ disk |
| L477 | tablo | \| K1.5.1.11 \| 8-Channel TDM (Time Division Multiplexing) \| i2s-interface.md L107 \| | ✅ disk |
| L478 | tablo | \| K1.5.1.12 \| Source Impedans \| i2s-interface.md L137 \| | ✅ disk |
| L479 | tablo | \| K1.5.1.13 \| Load Impedans \| i2s-interface.md L150 \| | ✅ disk |
| L480 | tablo | \| **K1.5.2** \| USB Audio (3. katman) \| usb-audio.md — 13 yaprak \| | ✅ disk |
| L481 | tablo | \| K1.5.2.1 \| Genel Bakış \| usb-audio.md L10 \| | ✅ disk |
| L482 | tablo | \| K1.5.2.2 \| Teknik Spesifikasyonlar \| usb-audio.md L14 \| | ✅ disk |
| L483 | tablo | \| K1.5.2.3 \| USB Descriptor Hierarchy \| usb-audio.md L29 \| | ✅ disk |
| L484 | tablo | \| K1.5.2.4 \| Isochronous Transfer \| usb-audio.md L63 \| | ✅ disk |
| L485 | tablo | \| K1.5.2.5 \| Sample Rate Support \| usb-audio.md L90 \| | ✅ disk |
| L486 | tablo | \| K1.5.2.6 \| DSD (DoP) Support \| usb-audio.md L103 \| | ✅ disk |
| L487 | tablo | \| K1.5.2.7 \| USB-C Connection \| usb-audio.md L129 \| | ✅ disk |
| L488 | tablo | \| K1.5.2.8 \| Driver Status \| usb-audio.md L150 \| | ✅ disk |
| L489 | tablo | \| K1.5.2.9 \| Bileşen Değerleri \| usb-audio.md L160 \| | ✅ disk |
| L490 | tablo | \| K1.5.2.10 \| Neden Isochronous? \| usb-audio.md L65 \| | ✅ disk |
| L491 | tablo | \| K1.5.2.11 \| Transfer Parameters \| usb-audio.md L79 \| | ✅ disk |
| L492 | tablo | \| K1.5.2.12 \| DoP (DSD over PCM) \| usb-audio.md L105 \| | ✅ disk |
| L493 | tablo | \| K1.5.2.13 \| Pin Mapping \| usb-audio.md L131 \| | ✅ disk |
| L494 | tablo | \| **K1.5.3** \| XMOS XU316 (3. katman) \| xmos-xu316.md — 6 yaprak \| | ✅ disk |
| L495 | tablo | \| K1.5.3.1 \| Genel Bakış \| xmos-xu316.md L10 \| | ✅ disk |
| L496 | tablo | \| K1.5.3.2 \| Teknik Spesifikasyonlar \| xmos-xu316.md L14 \| | ✅ disk |
| L497 | tablo | \| K1.5.3.3 \| Devre Tasarımı \| xmos-xu316.md L29 \| | ✅ disk |
| L498 | tablo | \| K1.5.3.4 \| Pin Konfigürasyonu \| xmos-xu316.md L64 \| | ✅ disk |
| L499 | tablo | \| K1.5.3.5 \| Temel Bağlantılar \| xmos-xu316.md L31 \| | ✅ disk |
| L500 | tablo | \| K1.5.3.6 \| Kondansatör Değerleri \| xmos-xu316.md L55 \| | ✅ disk |
| L502 | baslik | ### K1.6 — PCB & Termal | ✅ disk |
| L504 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L505 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L506 | tablo | \| **K1.6.1** \| PCB Tasarım (3. katman) \| pcb-tasarim.md — 8 yaprak \| | ✅ disk |
| L507 | tablo | \| K1.6.1.1 \| Genel Bakış \| pcb-tasarim.md L10 \| | ✅ disk |
| L508 | tablo | \| K1.6.1.2 \| Teknik Spesifikasyonlar \| pcb-tasarim.md L14 \| | ✅ disk |
| L509 | tablo | \| K1.6.1.3 \| Katman Stackup \| pcb-tasarim.md L30 \| | ✅ disk |
| L510 | tablo | \| K1.6.1.4 \| Star Grounding Sistemi \| pcb-tasarim.md L76 \| | ✅ disk |
| L511 | tablo | \| K1.6.1.5 \| Controlled Impedance \| pcb-tasarim.md L101 \| | ✅ disk |
| L512 | tablo | \| K1.6.1.6 \| Bileşen Yerleşimi \| pcb-tasarim.md L142 \| | ✅ disk |
| L513 | tablo | \| K1.6.1.7 \| Impedans Hesaplaması \| pcb-tasarim.md L103 \| | ✅ disk |
| L514 | tablo | \| K1.6.1.8 \| Differential Pair (I2S, USB) \| pcb-tasarim.md L123 \| | ✅ disk |
| L515 | tablo | \| **K1.6.2** \| Termal Yönetim (3. katman) \| termal-yonetim.md — 13 yaprak \| | ✅ disk |
| L516 | tablo | \| K1.6.2.1 \| Genel Bakış \| termal-yonetim.md L10 \| | ✅ disk |
| L517 | tablo | \| K1.6.2.2 \| Teknik Spesifikasyonlar \| termal-yonetim.md L14 \| | ✅ disk |
| L518 | tablo | \| K1.6.2.3 \| Isı Dağılım Analizi \| termal-yonetim.md L26 \| | ✅ disk |
| L519 | tablo | \| K1.6.2.4 \| Soğutucu Seçimi \| termal-yonetim.md L46 \| | ✅ disk |
| L520 | tablo | \| K1.6.2.5 \| Sıcaklık İzleme \| termal-yonetim.md L71 \| | ✅ disk |
| L521 | tablo | \| K1.6.2.6 \| Fan Kontrolü \| termal-yonetim.md L102 \| | ✅ disk |
| L522 | tablo | \| K1.6.2.7 \| Termal Pad Uygulaması \| termal-yonetim.md L127 \| | ✅ disk |
| L523 | tablo | \| K1.6.2.8 \| Toplam Isı (8 Kanal) \| termal-yonetim.md L38 \| | ✅ disk |
| L524 | tablo | \| K1.6.2.9 \| Alüminyum Ekstrüzyon Soğutucu \| termal-yonetim.md L48 \| | ✅ disk |
| L525 | tablo | \| K1.6.2.10 \| Termal Ped \| termal-yonetim.md L61 \| | ✅ disk |
| L526 | tablo | \| K1.6.2.11 \| NTC Sensör Devresi \| termal-yonetim.md L73 \| | ✅ disk |
| L527 | tablo | \| K1.6.2.12 \| Sıcaklık-Hassasiyet Tablosu \| termal-yonetim.md L92 \| | ✅ disk |
| L528 | tablo | \| K1.6.2.13 \| PWM Fan Driver \| termal-yonetim.md L104 \| | ✅ disk |
| L530 | baslik | ### K1.7 — Konnektör & Hoparlör | ✅ disk |
| L532 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L533 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L534 | tablo | \| **K1.7.1** \| Konnektörler (3. katman) \| konnektorler.md — 16 yaprak \| | ✅ disk |
| L535 | tablo | \| K1.7.1.1 \| Genel Bakış \| konnektorler.md L10 \| | ✅ disk |
| L536 | tablo | \| K1.7.1.2 \| Teknik Spesifikasyonlar \| konnektorler.md L14 \| | ✅ disk |
| L537 | tablo | \| K1.7.1.3 \| XLR Konnektörü \| konnektorler.md L25 \| | ✅ disk |
| L538 | tablo | \| K1.7.1.4 \| RCA Konnektörü \| konnektorler.md L61 \| | ✅ disk |
| L539 | tablo | \| K1.7.1.5 \| USB-C Konnektörü \| konnektorler.md L80 \| | ✅ disk |
| L540 | tablo | \| K1.7.1.6 \| Optical (Toslink) \| konnektorler.md L97 \| | ✅ disk |
| L541 | tablo | \| K1.7.1.7 \| HDMI ARC \| konnektorler.md L111 \| | ✅ disk |
| L542 | tablo | \| K1.7.1.8 \| Binding Posts (Hoparlör Çıkışları) \| konnektorler.md L136 \| | ✅ disk |
| L543 | tablo | \| K1.7.1.9 \| Panel Düzeni \| konnektorler.md L151 \| | ✅ disk |
| L544 | tablo | \| K1.7.1.10 \| Pin Konfigürasyonu (XLR) \| konnektorler.md L27 \| | ✅ disk |
| L545 | tablo | \| K1.7.1.11 \| XLR Devre Bağlantısı \| konnektorler.md L45 \| | ✅ disk |
| L546 | tablo | \| K1.7.1.12 \| Pin Konfigürasyonu (RCA) \| konnektorler.md L63 \| | ✅ disk |
| L547 | tablo | \| K1.7.1.13 \| Pin Konfigürasyonu (USB-C) \| konnektorler.md L82 \| | ✅ disk |
| L548 | tablo | \| K1.7.1.14 \| Pin Konfigürasyonu (Optical) \| konnektorler.md L99 \| | ✅ disk |
| L549 | tablo | \| K1.7.1.15 \| Pin Konfigürasyonu (HDMI ARC) \| konnektorler.md L113 \| | ✅ disk |
| L550 | tablo | \| K1.7.1.16 \| Pin Konfigürasyonu (Binding Posts) \| konnektorler.md L138 \| | ✅ disk |
| L551 | tablo | \| **K1.7.2** \| Hoparlör Dizilimi (3. katman) \| hoparlor-dizilimi.md — 8 yaprak \| | ✅ disk |
| L552 | tablo | \| K1.7.2.1 \| Genel Bakış \| hoparlor-dizilimi.md L10 \| | ✅ disk |
| L553 | tablo | \| K1.7.2.2 \| Teknik Spesifikasyonlar \| hoparlor-dizilimi.md L14 \| | ✅ disk |
| L554 | tablo | \| K1.7.2.3 \| Hoparlör Konumları \| hoparlor-dizilimi.md L27 \| | ✅ disk |
| L555 | tablo | \| K1.7.2.4 \| Kanal Haritası \| hoparlor-dizilimi.md L68 \| | ✅ disk |
| L556 | tablo | \| K1.7.2.5 \| Hoparlör Özellikleri \| hoparlor-dizilimi.md L81 \| | ✅ disk |
| L557 | tablo | \| K1.7.2.6 \| Kablo ve Bağlantılar \| hoparlor-dizilimi.md L106 \| | ✅ disk |
| L558 | tablo | \| K1.7.2.7 \| Full-Range Hoparlörler (FL, C, FR, SL, SR, SBL, SBR) \| hoparlor-dizilimi.md L83 \| | ✅ disk |
| L559 | tablo | \| K1.7.2.8 \| Subwoofer (LFE) \| hoparlor-dizilimi.md L95 \| | ✅ disk |
| L561 | baslik | ### K1.8 — Firmware (K1.f Alt Katmanı) | ✅ disk |
| L563 | vurgu | > **Kural:** `firmware/` klasörü K1'in alt katmanıdır (**K1.f**); 8 MD'nin tamamı K1.f.1..K1.f.8 ol… | ✅ disk |
| L565 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L566 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L567 | tablo | \| **K1.f.1** \| Bootloader (3. katman) \| firmware/bootloader.md — 16 yaprak \| | ✅ disk |
| L568 | tablo | \| K1.f.1.1 \| Genel Bakış \| firmware/bootloader.md L10 \| | ✅ disk |
| L569 | tablo | \| K1.f.1.2 \| Firmware Mimarisi \| firmware/bootloader.md L14 \| | ✅ disk |
| L570 | tablo | \| K1.f.1.3 \| Kaynak Kod Yapısı \| firmware/bootloader.md L58 \| | ✅ disk |
| L571 | tablo | \| K1.f.1.4 \| Teknik Detaylar \| firmware/bootloader.md L85 \| | ✅ disk |
| L572 | tablo | \| K1.f.1.5 \| Derleme & Yükleme \| firmware/bootloader.md L395 \| | ✅ disk |
| L573 | tablo | \| K1.f.1.6 \| Boot Sequence \| firmware/bootloader.md L87 \| | ✅ disk |
| L574 | tablo | \| K1.f.1.7 \| Dual-Bank Firmware Update \| firmware/bootloader.md L102 \| | ✅ disk |
| L575 | tablo | \| K1.f.1.8 \| DFU Protocol \| firmware/bootloader.md L130 \| | ✅ disk |
| L576 | tablo | \| K1.f.1.9 \| XMOS Bootloader Implementasyonu \| firmware/bootloader.md L150 \| | ✅ disk |
| L577 | tablo | \| K1.f.1.10 \| Flash Manager \| firmware/bootloader.md L211 \| | ✅ disk |
| L578 | tablo | \| K1.f.1.11 \| USB DFU Handler \| firmware/bootloader.md L274 \| | ✅ disk |
| L579 | tablo | \| K1.f.1.12 \| Reboot & Recovery \| firmware/bootloader.md L356 \| | ✅ disk |
| L580 | tablo | \| K1.f.1.13 \| XMOS Bootloader Derleme \| firmware/bootloader.md L397 \| | ✅ disk |
| L581 | tablo | \| K1.f.1.14 \| STM32 Bootloader Derleme \| firmware/bootloader.md L410 \| | ✅ disk |
| L582 | tablo | \| K1.f.1.15 \| DFU ile Firmware Güncelleme \| firmware/bootloader.md L422 \| | ✅ disk |
| L583 | tablo | \| K1.f.1.16 \| XMOS DFU \| firmware/bootloader.md L441 \| | ✅ disk |
| L584 | tablo | \| **K1.f.2** \| XMOS Firmware (3. katman) \| firmware/xmos-firmware.md — 18 yaprak \| | ✅ disk |
| L585 | tablo | \| K1.f.2.1 \| Genel Bakış \| firmware/xmos-firmware.md L10 \| | ✅ disk |
| L586 | tablo | \| K1.f.2.2 \| Firmware Mimarisi \| firmware/xmos-firmware.md L14 \| | ✅ disk |
| L587 | tablo | \| K1.f.2.3 \| Kaynak Kod Yapısı \| firmware/xmos-firmware.md L41 \| | ✅ disk |
| L588 | tablo | \| K1.f.2.4 \| Teknik Detaylar \| firmware/xmos-firmware.md L75 \| | ✅ disk |
| L589 | tablo | \| K1.f.2.5 \| Derleme & Yükleme \| firmware/xmos-firmware.md L262 \| | ✅ disk |
| L590 | tablo | \| K1.f.2.6 \| XC Dil Özellikleri \| firmware/xmos-firmware.md L77 \| | ✅ disk |
| L591 | tablo | \| K1.f.2.7 \| XMOS XU316 Özellikleri \| firmware/xmos-firmware.md L96 \| | ✅ disk |
| L592 | tablo | \| K1.f.2.8 \| Thread Zamanlama \| firmware/xmos-firmware.md L110 \| | ✅ disk |
| L593 | tablo | \| K1.f.2.9 \| Memory Map \| firmware/xmos-firmware.md L121 \| | ✅ disk |
| L594 | tablo | \| K1.f.2.10 \| USB Audio Pipeline \| firmware/xmos-firmware.md L132 \| | ✅ disk |
| L595 | tablo | \| K1.f.2.11 \| Clock Recovery \| firmware/xmos-firmware.md L142 \| | ✅ disk |
| L596 | tablo | \| K1.f.2.12 \| DSP Processing Chain \| firmware/xmos-firmware.md L171 \| | ✅ disk |
| L597 | tablo | \| K1.f.2.13 \| USB Audio Class 2.0 Entegrasyonu \| firmware/xmos-firmware.md L201 \| | ✅ disk |
| L598 | tablo | \| K1.f.2.14 \| Error Handling & Recovery \| firmware/xmos-firmware.md L231 \| | ✅ disk |
| L599 | tablo | \| K1.f.2.15 \| Ortam Kurulumu \| firmware/xmos-firmware.md L264 \| | ✅ disk |
| L600 | tablo | \| K1.f.2.16 \| Firmware Derleme \| firmware/xmos-firmware.md L276 \| | ✅ disk |
| L601 | tablo | \| K1.f.2.17 \| Firmware Yükleme \| firmware/xmos-firmware.md L294 \| | ✅ disk |
| L602 | tablo | \| K1.f.2.18 \| Debug & Trace \| firmware/xmos-firmware.md L307 \| | ✅ disk |
| L603 | tablo | \| **K1.f.3** \| USB Audio Firmware (3. katman) \| firmware/usb-audio-firmware.md — 15 yaprak \| | ✅ disk |
| L604 | tablo | \| K1.f.3.1 \| Genel Bakış \| firmware/usb-audio-firmware.md L10 \| | ✅ disk |
| L605 | tablo | \| K1.f.3.2 \| Firmware Mimarisi \| firmware/usb-audio-firmware.md L14 \| | ✅ disk |
| L606 | tablo | \| K1.f.3.3 \| Kaynak Kod Yapısı \| firmware/usb-audio-firmware.md L62 \| | ✅ disk |
| L607 | tablo | \| K1.f.3.4 \| Teknik Detaylar \| firmware/usb-audio-firmware.md L89 \| | ✅ disk |
| L608 | tablo | \| K1.f.3.5 \| Derleme & Yükleme \| firmware/usb-audio-firmware.md L432 \| | ✅ disk |
| L609 | tablo | \| K1.f.3.6 \| USB Audio Class 2.0 Descriptor Hiyerarşisi \| firmware/usb-audio-firmware.md L91 \| | ✅ disk |
| L610 | tablo | \| K1.f.3.7 \| Isochronous Transfer Mekanizması \| firmware/usb-audio-firmware.md L129 \| | ✅ disk |
| L611 | tablo | \| K1.f.3.8 \| Clock Recovery & Sync \| firmware/usb-audio-firmware.md L187 \| | ✅ disk |
| L612 | tablo | \| K1.f.3.9 \| Ring Buffer Implementasyonu \| firmware/usb-audio-firmware.md L265 \| | ✅ disk |
| L613 | tablo | \| K1.f.3.10 \| Audio Format Conversion \| firmware/usb-audio-firmware.md L319 \| | ✅ disk |
| L614 | tablo | \| K1.f.3.11 \| USB Audio Control Requests \| firmware/usb-audio-firmware.md L354 \| | ✅ disk |
| L615 | tablo | \| K1.f.3.12 \| Latency Optimization \| firmware/usb-audio-firmware.md L408 \| | ✅ disk |
| L616 | tablo | \| K1.f.3.13 \| USB Audio Firmware Derleme \| firmware/usb-audio-firmware.md L434 \| | ✅ disk |
| L617 | tablo | \| K1.f.3.14 \| USB Descriptor Doğrulama \| firmware/usb-audio-firmware.md L453 \| | ✅ disk |
| L618 | tablo | \| K1.f.3.15 \| USB Audio Test \| firmware/usb-audio-firmware.md L467 \| | ✅ disk |
| L619 | tablo | \| **K1.f.4** \| MCU Support (3. katman) \| firmware/mcu-support.md — 18 yaprak \| | ✅ disk |
| L620 | tablo | \| K1.f.4.1 \| Genel Bakış \| firmware/mcu-support.md L10 \| | ✅ disk |
| L621 | tablo | \| K1.f.4.2 \| Firmware Mimarisi \| firmware/mcu-support.md L14 \| | ✅ disk |
| L622 | tablo | \| K1.f.4.3 \| Kaynak Kod Yapısı \| firmware/mcu-support.md L56 \| | ✅ disk |
| L623 | tablo | \| K1.f.4.4 \| Teknik Detaylar \| firmware/mcu-support.md L112 \| | ✅ disk |
| L624 | tablo | \| K1.f.4.5 \| Derleme & Yükleme \| firmware/mcu-support.md L877 \| | ✅ disk |
| L625 | tablo | \| K1.f.4.6 \| STM32F4 Pin Configuration \| firmware/mcu-support.md L114 \| | ✅ disk |
| L626 | tablo | \| K1.f.4.7 \| SPI Communication Protocol \| firmware/mcu-support.md L143 \| | ✅ disk |
| L627 | tablo | \| K1.f.4.8 \| System Health Monitoring \| firmware/mcu-support.md L252 \| | ✅ disk |
| L628 | tablo | \| K1.f.4.9 \| Firmware Update Orchestration \| firmware/mcu-support.md L352 \| | ✅ disk |
| L629 | tablo | \| K1.f.4.10 \| Raspberry Pi Control Interface \| firmware/mcu-support.md L470 \| | ✅ disk |
| L630 | tablo | \| K1.f.4.11 \| Network Manager \| firmware/mcu-support.md L619 \| | ✅ disk |
| L631 | tablo | \| K1.f.4.12 \| Configuration Management \| firmware/mcu-support.md L684 \| | ✅ disk |
| L632 | tablo | \| K1.f.4.13 \| UART Debug Console \| firmware/mcu-support.md L783 \| | ✅ disk |
| L633 | tablo | \| K1.f.4.14 \| STM32 Firmware Derleme \| firmware/mcu-support.md L879 \| | ✅ disk |
| L634 | tablo | \| K1.f.4.15 \| STM32 Firmware Yükleme \| firmware/mcu-support.md L897 \| | ✅ disk |
| L635 | tablo | \| K1.f.4.16 \| Raspberry Pi Firmware Derleme \| firmware/mcu-support.md L911 \| | ✅ disk |
| L636 | tablo | \| K1.f.4.17 \| Raspberry Pi Kurulum \| firmware/mcu-support.md L931 \| | ✅ disk |
| L637 | tablo | \| K1.f.4.18 \| Entegrasyon Testi \| firmware/mcu-support.md L953 \| | ✅ disk |
| L638 | tablo | \| **K1.f.5** \| DSP Firmware (3. katman) \| firmware/dsp-firmware.md — 15 yaprak \| | ✅ disk |
| L639 | tablo | \| K1.f.5.1 \| Genel Bakış \| firmware/dsp-firmware.md L10 \| | ✅ disk |
| L640 | tablo | \| K1.f.5.2 \| Firmware Mimarisi \| firmware/dsp-firmware.md L14 \| | ✅ disk |
| L641 | tablo | \| K1.f.5.3 \| Kaynak Kod Yapısı \| firmware/dsp-firmware.md L72 \| | ✅ disk |
| L642 | tablo | \| K1.f.5.4 \| Teknik Detaylar \| firmware/dsp-firmware.md L103 \| | ✅ disk |
| L643 | tablo | \| K1.f.5.5 \| Derleme & Yükleme \| firmware/dsp-firmware.md L547 \| | ✅ disk |
| L644 | tablo | \| K1.f.5.6 \| DSP Processing Block Structure \| firmware/dsp-firmware.md L105 \| | ✅ disk |
| L645 | tablo | \| K1.f.5.7 \| IIR Biquad Filter \| firmware/dsp-firmware.md L146 \| | ✅ disk |
| L646 | tablo | \| K1.f.5.8 \| Parametric EQ \| firmware/dsp-firmware.md L216 \| | ✅ disk |
| L647 | tablo | \| K1.f.5.9 \| Dynamics Compressor \| firmware/dsp-firmware.md L282 \| | ✅ disk |
| L648 | tablo | \| K1.f.5.10 \| Peak Limiter \| firmware/dsp-firmware.md L345 \| | ✅ disk |
| L649 | tablo | \| K1.f.5.11 \| Noise Gate \| firmware/dsp-firmware.md L402 \| | ✅ disk |
| L650 | tablo | \| K1.f.5.12 \| Stereo Crossfeed (Headphone) \| firmware/dsp-firmware.md L457 \| | ✅ disk |
| L651 | tablo | \| K1.f.5.13 \| DSP Performance Optimization \| firmware/dsp-firmware.md L509 \| | ✅ disk |
| L652 | tablo | \| K1.f.5.14 \| DSP Firmware Derleme \| firmware/dsp-firmware.md L549 \| | ✅ disk |
| L653 | tablo | \| K1.f.5.15 \| DSP Test \| firmware/dsp-firmware.md L568 \| | ✅ disk |
| L654 | tablo | \| **K1.f.6** \| GPIO Control (3. katman) \| firmware/gpio-control.md — 14 yaprak \| | ✅ disk |
| L655 | tablo | \| K1.f.6.1 \| Genel Bakış \| firmware/gpio-control.md L10 \| | ✅ disk |
| L656 | tablo | \| K1.f.6.2 \| Firmware Mimarisi \| firmware/gpio-control.md L14 \| | ✅ disk |
| L657 | tablo | \| K1.f.6.3 \| Kaynak Kod Yapısı \| firmware/gpio-control.md L61 \| | ✅ disk |
| L658 | tablo | \| K1.f.6.4 \| Teknik Detaylar \| firmware/gpio-control.md L90 \| | ✅ disk |
| L659 | tablo | \| K1.f.6.5 \| Derleme & Yükleme \| firmware/gpio-control.md L716 \| | ✅ disk |
| L660 | tablo | \| K1.f.6.6 \| GPIO Pin Haritası (XMOS XU316) \| firmware/gpio-control.md L92 \| | ✅ disk |
| L661 | tablo | \| K1.f.6.7 \| Button Handler \| firmware/gpio-control.md L119 \| | ✅ disk |
| L662 | tablo | \| K1.f.6.8 \| LED Controller \| firmware/gpio-control.md L233 \| | ✅ disk |
| L663 | tablo | \| K1.f.6.9 \| Rotary Encoder \| firmware/gpio-control.md L399 \| | ✅ disk |
| L664 | tablo | \| K1.f.6.10 \| Display Driver (OLED SSD1306) \| firmware/gpio-control.md L493 \| | ✅ disk |
| L665 | tablo | \| K1.f.6.11 \| Event Queue \| firmware/gpio-control.md L607 \| | ✅ disk |
| L666 | tablo | \| K1.f.6.12 \| GPIO Interrupt Handler \| firmware/gpio-control.md L671 \| | ✅ disk |
| L667 | tablo | \| K1.f.6.13 \| GPIO Firmware Derleme \| firmware/gpio-control.md L718 \| | ✅ disk |
| L668 | tablo | \| K1.f.6.14 \| GPIO Test \| firmware/gpio-control.md L734 \| | ✅ disk |
| L669 | tablo | \| **K1.f.7** \| I2S Driver (3. katman) \| firmware/i2s-driver.md — 15 yaprak \| | ✅ disk |
| L670 | tablo | \| K1.f.7.1 \| Genel Bakış \| firmware/i2s-driver.md L10 \| | ✅ disk |
| L671 | tablo | \| K1.f.7.2 \| Firmware Mimarisi \| firmware/i2s-driver.md L14 \| | ✅ disk |
| L672 | tablo | \| K1.f.7.3 \| Kaynak Kod Yapısı \| firmware/i2s-driver.md L63 \| | ✅ disk |
| L673 | tablo | \| K1.f.7.4 \| Teknik Detaylar \| firmware/i2s-driver.md L84 \| | ✅ disk |
| L674 | tablo | \| K1.f.7.5 \| Derleme & Yükleme \| firmware/i2s-driver.md L426 \| | ✅ disk |
| L675 | tablo | \| K1.f.7.6 \| I2S Protocol Overview \| firmware/i2s-driver.md L86 \| | ✅ disk |
| L676 | tablo | \| K1.f.7.7 \| Clock Configuration \| firmware/i2s-driver.md L109 \| | ✅ disk |
| L677 | tablo | \| K1.f.7.8 \| I2S Master Driver \| firmware/i2s-driver.md L161 \| | ✅ disk |
| L678 | tablo | \| K1.f.7.9 \| Multi-Channel I2S \| firmware/i2s-driver.md L286 \| | ✅ disk |
| L679 | tablo | \| K1.f.7.10 \| Sample Rate Conversion \| firmware/i2s-driver.md L326 \| | ✅ disk |
| L680 | tablo | \| K1.f.7.11 \| Clock Recovery (Slave Mode) \| firmware/i2s-driver.md L367 \| | ✅ disk |
| L681 | tablo | \| K1.f.7.12 \| Audio Quality Metrics \| firmware/i2s-driver.md L407 \| | ✅ disk |
| L682 | tablo | \| K1.f.7.13 \| I2S Driver Derleme \| firmware/i2s-driver.md L428 \| | ✅ disk |
| L683 | tablo | \| K1.f.7.14 \| I2S Test \| firmware/i2s-driver.md L447 \| | ✅ disk |
| L684 | tablo | \| K1.f.7.15 \| DAC/ADC Konfigürasyonu \| firmware/i2s-driver.md L460 \| | ✅ disk |
| L685 | tablo | \| **K1.f.8** \| Firmware index (3. katman) \| firmware/index.md — 12 yaprak \| | ✅ disk |
| L686 | tablo | \| K1.f.8.1 \| Genel Bakış \| firmware/index.md L10 \| | ✅ disk |
| L687 | tablo | \| K1.f.8.2 \| Firmware Mimarisi \| firmware/index.md L14 \| | ✅ disk |
| L688 | tablo | \| K1.f.8.3 \| Kaynak Kod Yapısı \| firmware/index.md L49 \| | ✅ disk |
| L689 | tablo | \| K1.f.8.4 \| Teknik Detaylar \| firmware/index.md L88 \| | ✅ disk |
| L690 | tablo | \| K1.f.8.5 \| Derleme & Yükleme \| firmware/index.md L134 \| | ✅ disk |
| L691 | tablo | \| K1.f.8.6 \| XMOS XU316 Seçim Gerekçesi \| firmware/index.md L90 \| | ✅ disk |
| L692 | tablo | \| K1.f.8.7 \| Firmware Katmanları \| firmware/index.md L99 \| | ✅ disk |
| L693 | tablo | \| K1.f.8.8 \| Real-Time Gereksinimleri \| firmware/index.md L110 \| | ✅ disk |
| L694 | tablo | \| K1.f.8.9 \| Thread Kullanımı (XMOS) \| firmware/index.md L121 \| | ✅ disk |
| L695 | tablo | \| K1.f.8.10 \| XMOS Firmware Derleme \| firmware/index.md L136 \| | ✅ disk |
| L696 | tablo | \| K1.f.8.11 \| STM32 Firmware Derleme \| firmware/index.md L151 \| | ✅ disk |
| L697 | tablo | \| K1.f.8.12 \| DFU ile Güncelleme \| firmware/index.md L164 \| | ✅ disk |
| L699 | baslik | ### K1.9 — Bileşen Haritası & BOM | ✅ disk |
| L701 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L702 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L703 | tablo | \| **K1.9.1** \| README §2-§7 (3. katman) \| README.md — 20 yaprak \| | ✅ disk |
| L704 | tablo | \| K1.9.1.1 \| 2. Bileşen Haritası \| README.md L42 \| | ✅ disk |
| L705 | tablo | \| K1.9.1.2 \| 3. PCM3168A DAC Detayı \| README.md L93 \| | ✅ disk |
| L706 | tablo | \| K1.9.1.3 \| 4. Class AB Amplifikatör Detayı \| README.md L135 \| | ✅ disk |
| L707 | tablo | \| K1.9.1.4 \| 5. XMOS XU316 Detayı \| README.md L185 \| | ✅ disk |
| L708 | tablo | \| K1.9.1.5 \| 6. PCB Tasarım Kuralları \| README.md L210 \| | ✅ disk |
| L709 | tablo | \| K1.9.1.6 \| 7. BOM Maliyet Analizi \| README.md L238 \| | ✅ disk |
| L710 | tablo | \| K1.9.1.7 \| 2.1 USB Audio Interface \| README.md L44 \| | ✅ disk |
| L711 | tablo | \| K1.9.1.8 \| 2.2 DAC (Digital-to-Analog Converter) \| README.md L53 \| | ✅ disk |
| L712 | tablo | \| K1.9.1.9 \| 2.3 Amplifikatör \| README.md L63 \| | ✅ disk |
| L713 | tablo | \| K1.9.1.10 \| 2.4 Güç Kaynağı \| README.md L70 \| | ✅ disk |
| L714 | tablo | \| K1.9.1.11 \| 2.5 Hoparlör Matrisi (8.1 Surround) \| README.md L78 \| | ✅ disk |
| L715 | tablo | \| K1.9.1.12 \| 3.1 Pin Out \| README.md L95 \| | ✅ disk |
| L716 | tablo | \| K1.9.1.13 \| 3.2 I2S Konfigürasyonu \| README.md L116 \| | ✅ disk |
| L717 | tablo | \| K1.9.1.14 \| 3.3 Analogy Output Devresi \| README.md L127 \| | ✅ disk |
| L718 | tablo | \| K1.9.1.15 \| 4.1 Tek Kanal Devre Şeması \| README.md L137 \| | ✅ disk |
| L719 | tablo | \| K1.9.1.16 \| 4.2 Bias Ayar Prosedürü \| README.md L162 \| | ✅ disk |
| L720 | tablo | \| K1.9.1.17 \| 4.3 Termal Hesaplama \| README.md L172 \| | ✅ disk |
| L721 | tablo | \| K1.9.1.18 \| 5.1 Blok Diyagramı \| README.md L187 \| | ✅ disk |
| L722 | tablo | \| K1.9.1.19 \| 5.2 XMOS Kaynak Kullanımı \| README.md L198 \| | ✅ disk |
| L723 | tablo | \| K1.9.1.20 \| 6.1 Stackup \| README.md L225 \| | ✅ disk |
| L725 | baslik | ### K1.10 — index.md | ✅ disk |
| L727 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L728 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L729 | tablo | \| **K1.10.1** \| K1 İndeksi (3. katman) \| index.md — 5 yaprak \| | ✅ disk |
| L730 | tablo | \| K1.10.1.1 \| Genel Bakış \| index.md L10 \| | ✅ disk |
| L731 | tablo | \| K1.10.1.2 \| Blok Diyagramı \| index.md L14 \| | ✅ disk |
| L732 | tablo | \| K1.10.1.3 \| Bileşen Listesi \| index.md L50 \| | ✅ disk |
| L733 | tablo | \| K1.10.1.4 \| Katman Bağımlılıkları \| index.md L65 \| | ✅ disk |
| L734 | tablo | \| K1.10.1.5 \| Teknik Özet \| index.md L73 \| | ✅ disk |
| L736 | baslik | ### K1.11 — CLAUDE.md Guardrails | ✅ disk |
| L738 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L739 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L740 | tablo | \| **K1.11.1** \| K1 Guardrails (3. katman) \| CLAUDE.md — 4 yaprak \| | ✅ disk |
| L741 | tablo | \| K1.11.1.1 \| Hard Guardrails \| CLAUDE.md L16 \| | ✅ disk |
| L742 | tablo | \| K1.11.1.2 \| Bileşen Seçim Kısıtları \| CLAUDE.md L26 \| | ✅ disk |
| L743 | tablo | \| K1.11.1.3 \| Yasaklı Bileşenler \| CLAUDE.md L36 \| | ✅ disk |
| L744 | tablo | \| K1.11.1.4 \| İlgili ADR'ler \| CLAUDE.md L44 \| | ✅ disk |
| L746 | baslik | ### K1.12 — README Genel Bakış | ✅ disk |
| L748 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L749 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L750 | tablo | \| **K1.12.1** \| README §1 (3. katman) \| README.md — 2 yaprak \| | ✅ disk |
| L751 | tablo | \| K1.12.1.1 \| 1. Genel Bakış \| README.md L26 \| | ✅ disk |
| L752 | tablo | \| K1.12.1.2 \| 1.1 Temel İlkeler \| README.md L30 \| | ✅ disk |
| L754 | baslik | ## Kanıt Kataloğu (K1) | ✅ disk |
| L756 | vurgu | > Bu dizin, K1 şemasındaki 360 yaprağın dosya bazlı kaynağını verir. "Kapsanan" = şemaya giren yapr… | ✅ disk |
| L758 | tablo | \| # \| Dosya \| Rol \| H2 \| H3 \| Kapsanan \| Satır Aralığı \| | ✅ disk |
| L759 | tablo | \|---\|-------\|-----\|----\|----\|----------\|---------------\| | ✅ disk |
| L760 | tablo | \| 1 \| README.md \| Katman özeti, bileşen haritası, BOM \| 9 \| 15 \| 22 \| L26–L269 \| | ✅ disk |
| L761 | tablo | \| 2 \| CLAUDE.md \| Hard guardrails \| 4 \| 0 \| 4 \| L16–L44 \| | ✅ disk |
| L762 | tablo | \| 3 \| index.md \| Mimari indeks \| 6 \| 0 \| 5 \| L10–L83 \| | ✅ disk |
| L763 | tablo | \| 4 \| ak4458-dac.md \| AK4458 DAC \| 7 \| 2 \| 7 \| L10–L114 \| | ✅ disk |
| L764 | tablo | \| 5 \| analog-sinyal-yolu.md \| 5 aşamalı analog yol \| 13 \| 6 \| 17 \| L10–L209 \| | ✅ disk |
| L765 | tablo | \| 6 \| class-ab-amplifikator.md \| Class-AB devresi \| 7 \| 3 \| 8 \| L10–L153 \| | ✅ disk |
| L766 | tablo | \| 7 \| dac-adc-zinciri.md \| DAC-ADC zinciri \| 9 \| 3 \| 10 \| L10–L151 \| | ✅ disk |
| L767 | tablo | \| 8 \| diff-pair-input.md \| Differential giriş \| 8 \| 5 \| 11 \| L10–L122 \| | ✅ disk |
| L768 | tablo | \| 9 \| feedback-network.md \| Geri besleme ağı \| 9 \| 4 \| 11 \| L10–L145 \| | ✅ disk |
| L769 | tablo | \| 10 \| guc-kaynagi-analog.md \| LM5122 boost PSU \| 9 \| 2 \| 9 \| L10–L156 \| | ✅ disk |
| L770 | tablo | \| 11 \| hoparlor-dizilimi.md \| 8.1 hoparlör matrisi \| 8 \| 2 \| 8 \| L10–L125 \| | ✅ disk |
| L771 | tablo | \| 12 \| i2s-interface.md \| I2S/TDM arayüzü \| 10 \| 5 \| 13 \| L10–L177 \| | ✅ disk |
| L772 | tablo | \| 13 \| koruma-devreleri.md \| DC/OC/thermal/short \| 9 \| 7 \| 14 \| L10–L180 \| | ✅ disk |
| L773 | tablo | \| 14 \| konnektorler.md \| XLR/RCA/USB-C/HDMI \| 11 \| 7 \| 16 \| L10–L194 \| | ✅ disk |
| L774 | tablo | \| 15 \| mjle21194-93.md \| Output transistörler \| 8 \| 4 \| 10 \| L10–L148 \| | ✅ disk |
| L775 | tablo | \| 16 \| output-stage.md \| Darlington output \| 9 \| 8 \| 15 \| L10–L190 \| | ✅ disk |
| L776 | tablo | \| 17 \| pcm3168a-dac-adc.md \| Ana DAC/ADC \| 6 \| 2 \| 6 \| L10–L109 \| | ✅ disk |
| L777 | tablo | \| 18 \| pcb-tasarim.md \| 6-layer PCB \| 8 \| 2 \| 8 \| L10–L177 \| | ✅ disk |
| L778 | tablo | \| 19 \| termal-yonetim.md \| Soğutma/fan/NTC \| 9 \| 6 \| 13 \| L10–L160 \| | ✅ disk |
| L779 | tablo | \| 20 \| usb-audio.md \| USB Audio Class \| 11 \| 4 \| 13 \| L10–L181 \| | ✅ disk |
| L780 | tablo | \| 21 \| vas-stage.md \| VAS + Miller \| 9 \| 4 \| 11 \| L10–L160 \| | ✅ disk |
| L781 | tablo | \| 22 \| xmos-xu316.md \| XMOS XU316 devresi \| 6 \| 2 \| 6 \| L10–L92 \| | ✅ disk |
| L782 | tablo | \| 23 \| firmware/bootloader.md \| K1.f — bootloader/DFU \| 7 \| 11 \| 16 \| L10–L465 \| | ✅ disk |
| L783 | tablo | \| 24 \| firmware/xmos-firmware.md \| K1.f — XMOS XC \| 7 \| 13 \| 18 \| L10–L336 \| | ✅ disk |
| L784 | tablo | \| 25 \| firmware/usb-audio-firmware.md \| K1.f — UAC2 firmware \| 7 \| 10 \| 15 \| L10–L494 \| | ✅ disk |
| L785 | tablo | \| 26 \| firmware/mcu-support.md \| K1.f — STM32/RPi \| 7 \| 13 \| 18 \| L10–L980 \| | ✅ disk |
| L786 | tablo | \| 27 \| firmware/dsp-firmware.md \| K1.f — DSP blokları \| 7 \| 10 \| 15 \| L10–L592 \| | ✅ disk |
| L787 | tablo | \| 28 \| firmware/gpio-control.md \| K1.f — GPIO/LED/display \| 7 \| 9 \| 14 \| L10–L758 \| | ✅ disk |
| L788 | tablo | \| 29 \| firmware/i2s-driver.md \| K1.f — I2S master \| 7 \| 10 \| 15 \| L10–L485 \| | ✅ disk |
| L789 | tablo | \| 30 \| firmware/index.md \| K1.f — indeks \| 7 \| 7 \| 12 \| L10–L189 \| | ✅ disk |
| L790 | tablo | \| \| **TOPLAM** \| 30 dosya \| **241** \| **176** \| **360** \| \| | ✅ disk |
| L792 | vurgu | **Katalog notları:** | ✅ disk |
| L794 | madde | 1. **Bilinçli çıkarım (57 başlık):** 27 "Bağımlılıklar" + 28 "Durum: Implementasyon" + README §8 İl… | ✅ disk |
| L795 | madde | 2. **Satır bazlı ek kanıt (şemaya sayılmadı):** README §2 bileşen tabloları 20 satır (L48-89), §7 B… | ✅ disk |
| L796 | madde | 3. **K1.f:** firmware/ 8 MD'nin tamamı K1.f.1..K1.f.8 olarak 3. katmanda; 139 firmware yaprağı K1.f… | ✅ disk |
| L797 | madde | 4. **Kapsam:** 19 içerik MD + README + CLAUDE + index = 22 k1-donanim dosyası + 8 firmware dosyası … | ✅ disk |
| L798 | madde | 5. **Adlandırma:** klasör/dosya adları lowercase-hyphen (k1-donanim, firmware, mjle21194-93, …), be… | ✅ disk |
| L800 | metin | --- | ✅ disk |
| L802 | metin | *K1 Donanım Layer v1.0.0 — CoreMusic Architecture* | ✅ disk |
| L803 | metin | *Authority: Bayram Ali / Vault Steward* | ✅ disk |
| L804 | metin | *Last Updated: 2026-09-29 — genişletme: 3 turlu agent tartışması* | ✅ disk |
| L805 | metin | *Mode: Red Team · Human Mode · Truth Mode* | ✅ disk |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
