---
node_id: audio-ornekler
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/audio
children: []
depends_on:
  - audio-kapsam
  - audio-dogrulama
evidence:
  - "Repo ölçümü 2026-10-13: *.cpp = 0 · *.h = 0 · *.xc = 0 · *.ino = 0"
  - "https://www.ti.com/product/PCM3168A (HTTP 200, 2026-10-13)"
  - "https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ (HTTP 200, 2026-10-13)"
  - "K001-donanım/donanaım-type/audio/cipler/{ak4458-dac,pcm3168a-codec,tpa3255-amplifier}.md (okundu)"
  - "ADR-038 (accepted, 2026-09-26) §2.2-d M1–M6 eşikleri"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · repo kodu YOK (0 C++ dosyası)"
open_questions:
  - "Q-AO1: PCM3168A / AK4458 fiziksel pin atamaları (PCB şeması) — vault'ta şema YOK → UNKNOWN"
  - "Q-AO2: XMOS XU316 firmware başlatma sırası — repo'da *.xc = 0 → şablon olarak kalır"
  - "Q-AO3: Ölçüm aleti envanteri boş (audio-dogrulama §13.11) → M1–M6 kayıtları doldurulamaz"
group: kod
---

# audio-ornekler — Ses Donanımı Uygulama Örnekleri

> **Kural:** §1 repo kodu birebir aranır (yol + sayı); ilgizse **— yok** yazılır.
> §2–§7 **adım şablonlarıdır** — gerçek çalışan kod, gerçek pin ataması veya
> gerçek ölçüm sonucu olarak sunulmaz. Uydurma pin numarası, uydurma seri no,
> uydurma ölçüm çıktısı **üretilmez**; bilinen değer `UNKNOWN` ile işaretlenir.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

Ölçüm (2026-10-13, repo kökü `C:\www\coremusic.net`, `.git`/gizli klasör ayrımı
olmaksızın tam tarama):

| # | Aranan | Sonuç |
|---|---|---|
| 1 | `*.cpp` dosyası | **0** |
| 2 | `*.h` dosyası | **0** |
| 3 | `*.xc` dosyası (XMOS X-CORE) | **0** |
| 4 | `*.ino` dosyası (Arduino/MCU) | **0** |
| 5 | I2S/TDM pin planı dosyası (kod veya devre listesi) | — yok |
| 6 | Kodek başlatma (init) kodu | — yok |
| 7 | Ölçüm kayıt betiği (M1–M6) | — yok |
| 8 | Kanal eşleme (channel map) yapılandırması | — yok |

**Yorum:** ADR-038 **kabul edilmiş** bir karardır (2026-09-26, TEYİT); ancak
kabul edilmiş karar ile **repo'daki kod** farklı şeylerdir. Repo kodu 0 olduğu
için bu dosyadaki her örnek **tasarım şablonu** olarak yazılır. Bu görev kod
üretmez, yalnız `.md` üretir; repo'ya dosya eklenmez.

**Etiket özeti:** ADR-038 = `DOCUMENTED` (kabul kaydı vault'ta) · firmware =
`PLANNED` (repo 0) · fiziksel donanım = `UNKNOWN` (envanter boş) ·
`IMPLEMENTED` = **hiçbir kalem için yazılmadı**.

## §2 I2S / TDM Pin Planı Şablonu

### §2.1 Sinyal listesi (arayüz seviyesi, cip bağımsız)

| # | Sinyal | Yön (host↔cip) | Zorunlu | Not |
|---|---|---|---|---|
| 1 | `BCLK` (bit clock) | host → cip | evet | Frekans = Fs × bit × kanal |
| 2 | `LRCLK` / `FSYNC` (frame sync) | host → cip | evet | TDM'de slot çerçevesi |
| 3 | `SDATA_OUT` (DAC verisi) | host → cip | evet (DAC) | Cipe doğru seri veri |
| 4 | `SDATA_IN` (ADC verisi) | cip → host | evet (ADC) | PCM3168A → host yönü |
| 5 | `MCLK` (master clock) | host → cip | cipe göre | Bazı ciplerde iç osc. ile de çalışır → **UNKNOWN** |
| 6 | `SCK` / `SYSTEM CLOCK` | host → cip | cipe göre | Ayrık sistem saati |
| 7 | `RESET_N` / `RST` | host → cip | evet | Aktif-düşük çoğunlukla |
| 8 | `PDN` / `POWER-DOWN` | host → cip | cipe göre | Güç modu |
| 9 | `SDA` / `SCL` (kontrol) | çift yönlü | evet | I2C adresi **UNKNOWN** |
| 10 | `ADDR` / `ADSEL` (adres seçimi) | sabit seviye | cipe göre | Lehim pin'i olabilir → **UNKNOWN** |
| 11 | `GND` | — | evet | Analog / dijital / çekirdek ayrımı |
| 12 | `DVDD` / `AVDD` / `PVDD` | besleme | evet | Gerilim aralıkları cip dosyasından → §6 |

### §2.2 Pin planı doldurma şablonu (BOŞ — PCB şeması yok)

| # | Sinyal | Cip bacağı (pin) | MCU/XMOS bacağı | Yön | Besleme alanı | Rev. | Durum |
|---|---|---|---|---|---|---|---|
| 1 | BCLK | UNKNOWN | UNKNOWN | → | dijital I/O | — | BEKLEMEDE |
| 2 | LRCLK | UNKNOWN | UNKNOWN | → | dijital I/O | — | BEKLEMEDE |
| 3 | SDATA_OUT | UNKNOWN | UNKNOWN | → | dijital I/O | — | BEKLEMEDE |
| 4 | SDATA_IN | UNKNOWN | UNKNOWN | ← | dijital I/O | — | BEKLEMEDE |
| 5 | MCLK | UNKNOWN | UNKNOWN | → | dijital I/O | — | BEKLEMEDE |
| 6 | RESET_N | UNKNOWN | UNKNOWN | → | dijital I/O | — | BEKLEMEDE |
| 7 | SDA | UNKNOWN | UNKNOWN | ↔ | I2C alanı | — | BEKLEMEDE |
| 8 | SCL | UNKNOWN | UNKNOWN | → | I2C alanı | — | BEKLEMEDE |
| 9 | GND | cip GND bacağı | — | — | ortak | — | BEKLEMEDE |
| 10 | DVDD | cip DVDD bacağı | — | — | dijital besleme | — | BEKLEMEDE |
| 11 | AVDD | cip AVDD bacağı | — | — | analog besleme | — | BEKLEMEDE |
| 12 | PVDD | cip PVDD bacağı | — | — | güç çıkışı | — | BEKLEMEDE |

**Yasak:** Bu tabloya pin numarası **uydurularak** yazılamaz. Pin numarası yalnız
(a) cip dosyasındaki datasheet kaydı, (b) PCB şeması, (c) eval kart dokümanı —
üçünden biriyle doldurulur; hiçbiri vault'ta yoksa `UNKNOWN` kalır.

### §2.3 Pin planı kararları (kural seviyesi)

| # | Karar | Seçenekler | Bu konudaki tutum |
|---|---|---|---|
| 1 | Saat kaynağı | host master / cip master / harici osilatör | **UNKNOWN** — ADR-038'te belirtilmedi, uydurulmaz |
| 2 | Format | I2S / TDM (slot) / left-justified | ADR-038 **TDM** bağlamı geçer (XMOS + PCM3168A sound-card core) → `DOCUMENTED` |
| 3 | Kanal başına bit | 16 / 24 / 32 | **UNKNOWN** — ölçüm/tespit bekliyor |
| 4 | Örnekleme frekansı | 44.1k / 48k / 96k / 192k | **UNKNOWN** — hedef Fs kararı üst karar |
| 5 | Çift cipleme (DAC + kodek) | ortak BCLK-LRCLK hattı / ayrı | Ortak hat önerilir (tek zamanlama alanı); uygulanmadı → `PLANNED` |
| 6 | Analog/dijital GND | tek nokta köprü / ayrı düzlem | **UNKNOWN** — PCB tasarımına bağlı |
| 7 | MCLK gereksinimi | var / yok | Cip dosyasına bakılmadan karar verilmez → **UNKNOWN** |

### §2.4 Pin planı kontrol listesi

| # | Kontrol | Geçme koşulu | Durum |
|---|---|---|---|
| 1 | Her sinyal için çift taraflı pin ataması var mı? | 12/12 dolu | ❌ 0/12 |
| 2 | Besleme alanları ayrıldı mı? | DVDD/AVDD/PVDD ayrı satır | ❌ dolu değil |
| 3 | Gerilim seviyeleri cip dosyasıyla uyumlu mu? | çapraz kontrol 0 çelişki | ❌ yapılmadı |
| 4 | Reset/PDN sırası yazılı mı? | §3 adım tablosu dolu | ❌ şablon |
| 5 | I2C adresi çakışması var mı? | iki cip için farklı adres | ❌ **UNKNOWN** |
| 6 | Uzunluk / empedans notu var mı? | BCLK hattı notu | ❌ yok |

**Sonuç:** 0/6 geçti → §2 tamamlandı **sayılmaz**.

## §3 Kodek Başlatma (Init) Sırası Şablonu

### §3.1 Genel sıralama (güç → saat → kontrol → akış)

| # | Adım | Amaç | Beklenen gözlem | Hata hâli |
|---|---|---|---|---|
| 1 | Besleme sırası | Yükler aktif değilken çıkış | Ölçülen gerilimler cip aralığında | Aralık dışındaysa DUR |
| 2 | Reset'i tut (düşük) | Cip bilinmeyen durumda kalmasın | Reset seviyesi aktif | — |
| 3 | Saati başlat | Kilitlenebilir saat yoksa veri anlamsız | BCLK/LRCLK frekansı = hedef | Kilit yoksa DUR |
| 4 | Saat kilit bekleme | PLL/kilit bayrağı | Kilit bayrağı set | Zaman aşımı → hata |
| 5 | Reset'i bırak | Cip init'e hazır | Cip hazır bayrağı | — |
| 6 | Format ayarı | I2S/TDM, bit uzunluğu, slot | Okunan değer yazılan değerle aynı | Okuma-yazma farkı → hata |
| 7 | Kanal sayımı / slot eşleme | TDM slot planı | Slot–kanal birebir | Kayma varsa hata |
| 8 | Kazanç / mute ayarı | Açılışta mute ile başla | Mute = ON | Mutesiz açılış = pop riski |
| 9 | Demux / jumper (PCM3168A tarafı) | Kanal yönlendirme | Hedef pin seviyesi | Yanlış yön → kanal kayması |
| 10 | Mute'i kaldır | Sinyal yolu açık | Çıkışta beklenen sinyal | Yok → §4 ölçümüne git |
| 11 | Kayıt oku-back | Ayarların gerçekten oturduğu | Tüm register'lar beklenen değer | Fark → rollback + log |
| 12 | Durum kaydı | Teşkilat için kanıt | Init log dosyası | Log yoksa tekrarlanamaz |

### §3.2 PCM3168A'ye özgü adım notları (kayıt düzeyinde)

| # | Not | Kaynak | Etiket |
|---|---|---|---|
| 1 | 8 kanal codec — TDM ile çoklu kanal taşır | `cipler/pcm3168a-codec.md` | `DOCUMENTED` |
| 2 | ADC + DAC tek pakette | `cipler/pcm3168a-codec.md` | `DOCUMENTED` |
| 3 | Demux/jumper ile kanal yönlendirme seçeneği | `cipler/pcm3168a-codec.md` | `DOCUMENTED` |
| 4 | Register haritası sırası (hangi reg. önce) | Vault'ta ayrı kayıt **aranmadı** | **UNKNOWN** |
| 5 | I2C/ SPI arayüz tercihi | Vault'ta karar **aranmadı** | **UNKNOWN** |

### §3.3 AK4458'ye özgü adım notları (kayıt düzeyinde)

| # | Not | Kaynak | Etiket |
|---|---|---|---|
| 1 | 8 kanal DAC | `cipler/ak4458-dac.md` | `DOCUMENTED` |
| 2 | PCM3168A ile birlikte ADR-038 çekirdeğinde | ADR-038 (accepted) | `DOCUMENTED` |
| 3 | Attenüatör / mod ayarları register sırası | Vault'ta ayrı kayıt **aranmadı** | **UNKNOWN** |
| 4 | Filtre modu seçimi | Vault'ta karar **aranmadı** | **UNKNOWN** |
| 5 | Sıcaklık/entegrasyon notları (PCB) | PCB şeması yok | **UNKNOWN** |

### §3.4 Init hata senaryoları

| # | Senaryo | Belirti | Müdahale |
|---|---|---|---|
| 1 | Saat hiç gelmiyor | BCLK 0 Hz | Host saat yapılandırmasını kontrol → değilse DUR |
| 2 | Saat var, kilit yok | PLL lock = 0 | Frekans/çarpan hatası → yeniden başlat |
| 3 | Format yazıldı, okuma farklı | Register okuma farkı | Bus seviyesi / adres / write-protection |
| 4 | Akış var, çıkış yok | Mute hâlâ açık | §3.1 adım 10 |
| 5 | Kanallar kaymış | Sol/sağ yer değiştirilmiş | §5 kanal eşleme + TDM slot planı |
| 6 | Pop/noise açılışta | Mekanik tıklama benzeri | Besleme ve mute sırası (adım 1/8/10) |
| 7 | I2C adres çakışması | İkinci cip yanıt vermiyor | ADDR/ADSEL lehim pin'i → §2.1 #10 |

## §4 M1–M6 Ölçüm Kayıt Şablonu

> Eşikler ADR-038 §2.2-d'den alınmıştır (mevcut kabul edilmiş karar).
> **Hiçbir hücre doldurulmamıştır** — ölçümler `audio-dogrulama.md` §13.10'da
> `BEKLEMEDE`dir.

### §4.1 Ölçüm adımları (her ölçüm için)

| # | Adım | Amaç | Çıkış | Hata hâli |
|---|---|---|---|---|
| 1 | Aleti seç ve envantere yaz | Kalibrasyon izlenebilirliği | Alet + seri no | Envanterde yoksa DUR |
| 2 | Kalibrasyon tarihi kontrol | Geçerlilik | Tarih | Süresi dolmuşsa DUR |
| 3 | Ortam kaydını al | Gürültü tabanı | Arka plan kaydı | Taban eşiğe yakınsa DUR |
| 4 | Bağlantıyı kur | Sinyal yolu | Bağlantı şeması | — |
| 5 | Sinyali uygula | Test koşulu | Uygulanan sinyal | — |
| 6 | Ham veriyi kaydet | Tekrarlanabilirlik | Ham dosya yolu | Dosya yoksa kayıt geçersiz |
| 7 | Sonucu hesapla | Eşik karşılaştırması | Ölçülen değer | — |
| 8 | Eşikle karşılaştır | GEÇTİ/KALDI | Karar | — |
| 9 | Kaydı yaz | Denetim izi | §4.2 tablosu | — |

### §4.2 Ölçüm kayıt formu (BOŞ)

| # | Ölçüm | Eşik (ADR-038) | Alet | Kalibr. tarihi | Ham dosya | Ölçülen | Karar |
|---|---|---|---|---|---|---|---|
| M1 | SNR | ≥90 dB | UNKNOWN | — | — | — | BEKLEMEDE |
| M2 | Dinamik aralık (DIN ADYN) | ≥105 dB | UNKNOWN | — | — | — | BEKLEMEDE |
| M3 | THD+N | ≥95 dB (eşdeğer) | UNKNOWN | — | — | — | BEKLEMEDE |
| M4 | Kanal eşleniği / faz | FADGI matrisi | UNKNOWN | — | — | — | BEKLEMEDE |
| M5 | Jitter | ≤100 ps | UNKNOWN | — | — | — | BEKLEMEDE |
| M6 | Kanal arası kazanç farkı | ≤1 dB | UNKNOWN | — | — | — | BEKLEMEDE |

### §4.3 M4 — FADGI matrisi için boş ızgara

| # | Kanal / test | Ölçüt | Sonuç |
|---|---|---|---|
| 1 | Kanal 1 – kanal 2 eşitliği | ±dB | BEKLEMEDE |
| 2 | Kanal 1 – kanal 2 faz | derece | BEKLEMEDE |
| 3 | Frekans düzgünlüğü | ±dB (band) | BEKLEMEDE |
| 4 | Crosstalk | dB | BEKLEMEDE |
| 5 | Gölge / clipping | var/yok | BEKLEMEDE |
| 6 | DC ofset | mV | BEKLEMEDE |

### §4.4 Ölçüm hata senaryoları

| # | Senaryo | Sonuç | Doğru tepki |
|---|---|---|---|
| 1 | Değer eşiğin altında | KALDI | `KALDI` yaz, kapatma; nedeni ara |
| 2 | Alet kalibrasyonu süresi dolmuş | Geçersiz | Kaydı silme, `BEKLEMEDE` yap |
| 3 | Ham dosya kaybolmuş | Tekrarlanamaz | Ölçümü tekrarla |
| 4 | Ortam gürültüsü yüksek | Sahte düşük sonuç | Ortam kaydını ekle, tekrarla |
| 5 | Datasheet değerini kopyalama isteği | Sahte geçiş | Yasak — §13.12 (doğrulama dosyası) |
| 6 | Tek ölçümle "GEÇTİ" | Tekrarlanamaz | En az tekrar gerekir → tekrar sayısı **UNKNOWN** |

## §5 Kanal Eşleme (Channel Map) Çalışma Sayfası

### §5.1 TDM slot ↔ kanal şablonu

| # | Slot | Kaynak | Hedef (DAC çıkışı) | Fark | Durum |
|---|---|---|---|---|---|
| 1 | Slot 0 | UNKNOWN | UNKNOWN | — | BEKLEMEDE |
| 2 | Slot 1 | UNKNOWN | UNKNOWN | — | BEKLEMEDE |
| 3 | Slot 2 | UNKNOWN | UNKNOWN | — | BEKLEMEDE |
| 4 | Slot 3 | UNKNOWN | UNKNOWN | — | BEKLEMEDE |
| 5 | Slot 4 | UNKNOWN | UNKNOWN | — | BEKLEMEDE |
| 6 | Slot 5 | UNKNOWN | UNKNOWN | — | BEKLEMEDE |
| 7 | Slot 6 | UNKNOWN | UNKNOWN | — | BEKLEMEDE |
| 8 | Slot 7 | UNKNOWN | UNKNOWN | — | BEKLEMEDE |

### §5.2 Kanal sayısı aritmetiği

| # | İfade | Hesap | Sonuç |
|---|---|---|---|
| 1 | Kodek kanalı (PCM3168A) | cip dosyası: 8 kanal | 8 (`DOCUMENTED`) |
| 2 | DAC kanalı (AK4458) | cip dosyası: 8 kanal | 8 (`DOCUMENTED`) |
| 3 | Bir TDM çerçevesinde taşınan kanal | slot sayısı | **UNKNOWN** (Fs/bit kararı yok) |
| 4 | Gerekli BCLK | Fs × bit × slot | Hesaplanamaz (Fs ve slot **UNKNOWN**) |
| 5 | Çift cip senkronu | ortak BCLK/LRCLK | Karar verilmedi → `PLANNED` |

### §5.3 Eşleme kontrol listesi

| # | Kontrol | Geçme koşulu | Durum |
|---|---|---|---|
| 1 | Her slot bir kez atanmış mı? | 0 çakışma | ❌ atanmadı |
| 2 | Sol/sağ tersliği test edildi mi? | M4 kapsamında | ❌ ölçülmadi |
| 3 | Faz tersliği var mı? | M4 kapsamında | ❌ ölçülmadi |
| 4 | Crosstalk ölçümü var mı? | M4 satırı | ❌ BEKLEMEDE |
| 5 | Atlas-topolojisi (varsa) diye not | Fiziksel kablo/PCB | ❌ **UNKNOWN** |

## §6 Jitter Bütçesi Çalışma Sayfası

### §6.1 Bütçe dağılımı şablonu (BOŞ)

| # | Kaynak | Pay (ps) | Ölçüldü mü? | Not |
|---|---|---|---|---|
| 1 | Saat jeneratörü (kaynak) | UNKNOWN | hayır | Alet yok |
| 2 | PLL / kilit devresi | UNKNOWN | hayır | — |
| 3 | PCB dağıtımı (hat jitter) | UNKNOWN | hayır | Pin planı yok |
| 4 | Cip içi jitter (DAC) | UNKNOWN | hayır | Datasheet değeri ölçüm sayılmaz |
| 5 | Besleme gürültüsü kaynaklı | UNKNOWN | hayır | — |
| 6 | **Toplam (karekök toplam)** | **UNKNOWN** | hayır | Eşik ≤100 ps |

### §6.2 Jitter hesabı (formül — değer yok)

| # | İfade | Formül | Değer |
|---|---|---|---|
| 1 | Toplam jitter | √(Σ pay²) | Hesaplanamaz |
| 2 | Hedef | ≤100 ps (ADR-038 M5) | 100 ps |
| 3 | Marj | hedef − toplam | Hesaplanamaz |
| 4 | Çıktı etkisi (kabaca) | SINAD etkisi Fs'e bağlı | **UNKNOWN** — Fs yok |

### §6.3 Jitter ölçüm adımları

| # | Adım | Çıkış | Hata hâli |
|---|---|---|---|
| 1 | Alet (≥1 GHz osiloskop / jitter paketi) envantere gir | Alet kaydı | Yoksa DUR |
| 2 | Referans bağlantı noktası seç | Probe noktası | — |
| 3 | Örnekleme sayısı belirle | İstatistiksel anlamlılık | Sayı **UNKNOWN** (üst karar) |
| 4 | Ölç | RMS ve PP değer | — |
| 5 | Kaydet (ham + özet) | Dosya yolu | Dosya yoksa geçersiz |
| 6 | Eşikle karşılaştır (≤100 ps) | Karar | — |

## §7 Sınıf (Class) Kaydı — A-durumunun Belge İşi

> **Bu bölüm bir karar değil, kayıt bölümüdür.** Var olan durum olduğu gibi
> yazılır; kurala otomatik onay **verilmez**.

| # | Öğe | Vault'taki durum | Bu bölümün tutumu |
|---|---|---|---|
| 1 | Class AB kuralı | Vault kuralı olarak mevcut (§ Kapsam / ADR bağlamı) | Kural **kırılmaz**, ihlal edilmez |
| 2 | TPA3255 | Class D amplifikatör sınıfındadır — `cipler/tpa3255-amplifier.md` | Durum **olduğu gibi** yazılır |
| 3 | TPA3255'in Class AB kuralıyla ilişkisi | Otomatik uygunluk **onaylanmamıştır** | Onay yok → `UNKNOWN` / üst karar |
| 4 | Kuralın bu donanımda istisnası var mı? | Vault'ta istisna kaydı **aranmadı** | **UNKNOWN** |
| 5 | Çözüm yolu | — | Üst karar (Vault Steward) — bu dosya karar üretmez |

**Yasak:** Bu tabloya "Class AB uygun" / "kural ihlali yok" gibi bir sonuç
**yazılamaz**; yalnızca mevcut sınıf bilgisi ve onaysızlık kaydedilir.

## §8 Sık Yapılan Hatalar ve SSS

### §8.1 Sık yapılan hatalar

| # | Hata | Sonuç | Önleme |
|---|---|---|---|
| 1 | Pin numarasını ezberden yazmak | Yanlış PCB | `UNKNOWN` + şema bekle |
| 2 | Datasheet tipik değeri ölçüm sanmak | Sahte geçiş | `DOC` etiketi |
| 3 | Mute olmadan açılış | Pop/noise | §3.1 adım 8 |
| 4 | Saat kilitlenmeden veri göndermek | Anlamsız akış | §3.1 adım 4 |
| 5 | İki cipe aynı I2C adresi | İkincisi yanıtsız | §2.1 #10 |
| 6 | Slot sayısını kanal sayısıyla karıştırmak | Kanal kayması | §5.2 |
| 7 | PCM5122 önermek | Kural ihlali (§17 Edge #8) | PCM3168A / AK4458 öner |
| 8 | Class AB kuralını kendiliğinden onaylamak | Yetkisiz karar | §7 |
| 9 | ADR-038'i yeni seçim gibi sunmak | Karar tarihi çarpıtılır | TEYİT (2026-09-26) der |
| 10 | ADR-017/063 içeriği için iddia kurmak | Hallüsinasyon | Yalnız varlık iddiası |

### §8.2 Sık sorulanlar

| # | Soru | Cevap |
|---|---|---|
| 1 | Repo'da hangi ses kodu var? | **Yok** — §1 ölçümü: cpp/h/xc/ino = 0 |
| 2 | PCM3168A pin atamaları belli mi? | Hayır → **UNKNOWN** (Q-AO1) |
| 3 | M1–M6 ölçüldü mü? | Hayır → `BEKLEMEDE` (§4.2) |
| 4 | ADR-038 yeni mi? | Hayır — kabul tarihi 2026-09-26, **TEYİT** |
| 5 | TPA3255 Class AB kuralına uyar mı? | **UNKNOWN** — otomatik onay yok (§7) |
| 6 | K003 ile örtüşme var mı? | Hayır — sınır: K001 donanım TYPE, K003 işlenme |
| 7 | PCM5122 kullanılabilir mi? | **Önerilmez** (§17 Edge #8) |
| 8 | Jitter bütçesi dolu mu? | Hayır — tüm paylar **UNKNOWN** (§6.1) |

## §9 Uygulama Sırası (Checklist) ve Kapı Eşlemesi

### §9.1 Sıra kontrol listesi

| # | Adım | Bağımlılık | Durum |
|---|---|---|---|
| 1 | Envanter doldur (alet + cip) | — | ❌ 10/10 UNKNOWN |
| 2 | PCB şeması / pin planı (§2.2) | 1 | ❌ yok |
| 3 | Besleme + saat tasarımı | 2 | ❌ yok |
| 4 | Init sırası (§3.1) | 3 | ❌ şablon |
| 5 | Kanal eşleme (§5.1) | 2, 4 | ❌ şablon |
| 6 | Jitter bütçesi (§6.1) | 3 | ❌ şablon |
| 7 | M1–M6 ölçüm (§4.2) | 1–6 | ❌ BEKLEMEDE |
| 8 | FADGI matrisi (§4.3) | 7 | ❌ BEKLEMEDE |
| 9 | Sınıf kaydı üst karar (§7) | — | ❌ UNKNOWN |
| 10 | Etiket güncelleme (IMPLEMENTED vb.) | 7, 8 | ❌ yazılamaz |

### §9.2 Adım ↔ kapı eşlemesi

| Adım | İlgili kapı (doğrulama dosyası) | Kapı durumu |
|---|---|---|
| 1 | K1 — Envanter | kapalı |
| 2–3 | K2 — Tezgâh | kapalı |
| 4–6 | K2 / K3 bağımlılığı | kapalı |
| 7–8 | K3–K4 — S1 ham veri + eşik | kapalı |
| 9 | K6 — S2 envanter köprüsü | kapalı |
| 10 | K7 — S3 doküman | kapalı |

## §10 Çapraz Referanslar

| # | Dosya | İlişki |
|---|---|---|
| 1 | `audio-kapsam.md` | Kapsam ve sınır (mimari grup) |
| 2 | `audio-dogrulama.md` | Kapılar, ölçüm formu, kanıt sayımı (kanıt grup) |
| 3 | `cipler/pcm3168a-codec.md` | Kodek kaydı — §3.2, §5.2 |
| 4 | `cipler/ak4458-dac.md` | DAC kaydı — §3.3 |
| 5 | `cipler/tpa3255-amplifier.md` | Amplifikatör kaydı — §7 (referans) |
| 6 | ADR-038 (accepted) | M1–M6 eşikleri, TDM çekirdeği — TEYİT |
| 7 | ADR-017 · ADR-063 | Yalnız **varlık** iddiası |
| 8 | `hdd/hdd-ornekler.md` | İskelet kaynağı (grup: kod) |
| 9 | `K003-audio/**` | Sınır: donanım TYPE ≠ ses işlenme |

## §11 Uygulama Defteri (boş formlar + senaryo tabloları)

### §11.1 Donanım kayıt formu (BOŞ — envanter girişi)

| # | Alan | Değer | Kanıt |
|---|---|---|---|
| 1 | Cip adı (ör. PCM3168A) | — | — |
| 2 | Üretici | — | — |
| 3 | Sürüm / revizyon (datasheet) | — | — |
| 4 | Parti / lot numarası | — | — |
| 5 | Kaynak (satın alma yolu) | — | — |
| 6 | Fiziksel elde mi? (evet/hayır) | — | — |
| 7 | PCB üzerindeki pozisyon | — | — |
| 8 | Besleme gerilimi (ölçülen) | — | — |
| 9 | Sıcaklık (ölçülen) | — | — |
| 10 | Not | — | — |

### §11.2 Açılış (power-up) gözlem günlüğü

| # | Zaman | Olay | Gözlem | Beklenen | Sapma |
|---|---|---|---|---|---|
| 1 | — | Besleme 1 | — | cip aralığında | — |
| 2 | — | Besleme 2 | — | cip aralığında | — |
| 3 | — | Reset tut | — | aktif seviye | — |
| 4 | — | Saat başlangıcı | — | hedef Fs | — |
| 5 | — | Kilit (PLL lock) | — | set | — |
| 6 | — | Reset bırak | — | hazır | — |
| 7 | — | Format yazımı | — | okuma = yazma | — |
| 8 | — | Slot eşleme | — | birebir | — |
| 9 | — | Mute açık | — | pop yok | — |
| 10 | — | İlk sinyal | — | beklenen çıktı | — |

### §11.3 Sinyal yolu doğrulama adımları

| # | Adım | Yöntem | Sonuç | Durum |
|---|---|---|---|---|
| 1 | BCLK varlığı | osiloskop | — | BEKLEMEDE |
| 2 | LRCLK/FSYNC frekansı | sayıcı | — | BEKLEMEDE |
| 3 | SDATA_OUT akışı | dekoder | — | BEKLEMEDE |
| 4 | SDATA_IN akışı (ADC) | dekoder | — | BEKLEMEDE |
| 5 | Mute bayrağı | register oku | — | BEKLEMEDE |
| 6 | Kanal–kanal ayrışma | test sinyali | — | BEKLEMEDE |
| 7 | Analog çıkış DC ofseti | multimetre | — | BEKLEMEDE |
| 8 | Yük altında gerilim düşümü | yüklü ölçüm | — | BEKLEMEDE |

### §11.4 Frekans / hesap defteri (formül + boş satır)

| # | İfade | Formül | Değer | Not |
|---|---|---|---|---|
| 1 | BCLK | Fs × bit × slot | — | Fs, bit, slot **UNKNOWN** |
| 2 | TDM çerçevesi | 1 / Fs | — | Fs **UNKNOWN** |
| 3 | Slot süresi | 1 / (Fs × slot) | — | — |
| 4 | Bit süresi | 1 / BCLK | — | — |
| 5 | Örnekleme periyodu | 1 / Fs | — | — |
| 6 | Toplam jitter | √(Σpay²) | — | §6.1 payları boş |
| 7 | SNR marjı | ölçülen − 90 dB | — | ölçülen yok |
| 8 | Kanal farkı marjı | 1 dB − ölçülen | — | ölçülen yok |

### §11.5 FADGI ölçüm oturumu formu (BOŞ)

| # | Alan | Değer |
|---|---|---|
| 1 | Oturum tarihi | — |
| 2 | Ölçüm ortamı (oda/noise) | — |
| 3 | Alet + seri no | — |
| 4 | Kalibrasyon tarihi | — |
| 5 | Sistem altrevizyonu (donanım) | — |
| 6 | Test sinyali tipi | — |
| 7 | Referans seviyesi | — |
| 8 | Ham dosya yolları | — |
| 9 | Matris sonucu (§4.3) | — |
| 10 | Karar (GEÇTİ/KALDI) | — |
| 11 | İmza | — |

### §11.6 Hata ayıklama akışı

| # | Belirti | Olası neden | Sonraki adım |
|---|---|---|---|
| 1 | Hiç akış yok | Saat / reset / format | §3.4 #1–#3 |
| 2 | Akış var, ses yok | Mute / kanal eşleme | §3.1 #10, §5.1 |
| 3 | Ses var, gürültü yüksek | Besleme / GND / jitter | §6.1, §11.1 #8 |
| 4 | Kanallar yer değiştirmiş | Slot planı | §5.1 |
| 5 | Tek kanal geliyor | Demux/jumper yönlendirme | §3.2 #3 |
| 6 | Klip / kesilme | Kazanç zinciri | §11.1 #8, §4.1 |
| 7 | Açılışta pop | Besleme/mute sırası | §3.1 #1, #8, #10 |
| 8 | Ölçüm eşiği geçemiyor | Alet / ortam / tasarım | §4.4 #1–#4 |

### §11.7 Sürüm / revizyon kayıt şablonu

| # | Tarih | Rev. | Değişen | Neden | Doğrulanan ölçüm |
|---|---|---|---|---|---|
| 1 | — | — | — | — | — |
| 2 | — | — | — | — | — |
| 3 | — | — | — | — | — |

### §11.8 Kapı ↔ dosya eşlemesi (bu üçlü)

| # | Kapı | Sahip dosya | Durum |
|---|---|---|---|
| 1 | S1 (fiziksel ölçüm) | `audio-dogrulama.md` §5 | kapalı |
| 2 | S2 (envanter köprüsü) | `audio-dogrulama.md` §6 | kapalı |
| 3 | S3 (doküman) | `audio-dogrulama.md` §12 | kapalı |
| 4 | Uygulama şablonu | `audio-ornekler.md` (bu dosya) | şablon |
| 5 | Kapsam sınırı | `audio-kapsam.md` | DOCUMENTED |

### §11.9 Adım ↔ sorumlu (yetki sınırları — iddia değil, kural seviyesi)

| # | Adım | İlgili kaynak | Not |
|---|---|---|---|
| 1 | Pin planı / PCB | üst karar + şema | bu dosya yetki vermez |
| 2 | Init sırası kodu | firmware (repo 0) | şablon olarak kalır |
| 3 | Ölçüm aleti edinimi | envanter kararı | `audio-dogrulama.md` §13.11 |
| 4 | M1–M6 kaydı | ölçüm ekibi | form §4.2 |
| 5 | Sınıf kuralı onayı | Vault Steward | §7 — otomatik onay yok |
| 6 | Etiket güncelleme | kanıt geldikten sonra | IMPLEMENTED = ölçüm sonrası |

### §11.10 Kapanış kontrol listesi (bu dosyanın kendisi için)

| # | Kontrol | Durum |
|---|---|---|
| 1 | §1 repo kodu ölçümle yazıldı mı? | ✅ 0/0/0/0 |
| 2 | Şablonlar "kod" olarak sunuldu mu? | ✅ hayır |
| 3 | Uydurma pin/gerilim/Fs yazıldı mı? | ✅ hayır (`UNKNOWN`) |
| 4 | ADR-038 TEYİT olarak sunuldu mu? | ✅ evet |
| 5 | ADR-017/063 yalnız varlık iddiası mı? | ✅ evet |
| 6 | Class AB kuralı ihlal edildi mi? | ✅ hayır (§7 kayıt) |
| 7 | PCM5122 önerildi mi? | ✅ hayır |
| 8 | `{...}` / `TBD` var mı? | ✅ hayır |
| 9 | Satır sayısı ≥500 mü? | ölçüm sonrası |
| 10 | Tek Authority satırı son satır mı? | ✅ evet |

---

## §12 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez. §1 "kod yok" ölçümle kanıtlanmıştır
(`*.cpp = 0 · *.h = 0 · *.xc = 0 · *.ino = 0`); §2–§11 **şablondur ve çalışan
kod, gerçek pin ataması veya gerçek ölçüm sonucu olarak sunulmamıştır**.
Pin numarası, gerilim, Fs, slot sayısı ve jitter payları uydurulmamış;
`UNKNOWN` / `BEKLEMEDE` ile işaretlenmiştir. **Hiçbir M1–M6 ölçümü YAPILMAMIŞTIR.**
ADR-038 mevcut kabul edilmiş karar (TEYİT, 2026-09-26) olarak sunulmuş, yeni
seçim gibi gösterilmemiştir; ADR-017/063 hakkında yalnız varlık iddiası
kurulmuştur. TPA3255 sınıf durumu olduğu gibi yazılmış, Class AB kuralına
otomatik onay verilmemiştir. PCM5122 önerilmemiştir. `{...}` ve `TBD` yer
tutucusu kullanılmamıştır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
