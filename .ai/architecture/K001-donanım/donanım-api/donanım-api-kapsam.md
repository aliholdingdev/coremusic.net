---
title: "donanım-api — Kapsam"
type: architecture-scope
node_id: K001-donanım/donanım-api
node_type: subdomain-scope
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanım-api/index
evidence:
  - K001-donanım/donanım-anayasa.md
  - K001-donanım/donanım-api/index.md
  - https://www.intel.com/
validation: "2026-10-13: kapsam §A-§D ile genişletildi; 85 → 500+ satır; API protokolü sorusu açık bırakıldı"
open_questions:
  - "API yüzeyi hangi protokole oturuyor (sürücü ioctl / sysfs benzeri)? — Q-A1"
  - "K001 Q3: kernel-core-api sınır kararı bekleniyor"
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# donanım-api — Kapsam

## §1 Rol (ELI15)

Donanımın yazılıma açılan arayüzü: üst katmanların (K000 sürücü, K003 ses motoru) parça
bilgisine sormasına izin veren çağrı seti. Donanımın kendisi değil, onun dışa kapalı olmayan
yüzü. (`donanım-api/index.md` ELI15 — birebir.)

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.2'deki **adından** türetilmiştir; v2 çocukları
> içerik olarak tanımlamaz (index.md satır 22-24).

## §2 Kapsam — İÇİNDE NE var

| # | Öğe | Durum |
|---|---|---|
| A1 | Donanıma erişim çağrı seti (üst katmanların parça bilgisini sorması) | ✅ ad kökeni — `donanım-anayasa.md` §3 satır 49 |
| A2 | Karar girdisi: ASIO SDK = **GPLv3 açık kaynak** arayüz-standardı (K003/K005 lisans notu) | ✅ §5/6 (Steinberg sayfası) — K001'in lisans iddiası DEĞİL |
| A3 | Karar girdisi: PCM3168A kontrol arayüzü — 4-wire SPI · 2-wire I2C · hardware control | ✅ §5/4 (TI datasheet) |
| A4 | Karar girdisi: PCM3168A dijital ses arayüzü — I2S · L/RJ · DSP · TDM | ✅ §5/3 (TI ürün sayfası) |
| A5 | Karar girdisi: XU316 USB Audio Class 2.0 · USB 2.0 high-speed device arayüzü | ✅ §5/5 (XMOS sayfası) |
| A6 | API yüzeyinin oturduğu protokol (ioctl / sysfs benzeri) | **UNKNOWN** — index.md açık sorusu |
| A7 | Alt kademe | `children: []` (bu turda AÇILMADI) — disk kanıtlı |

## §3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Donanıma en yakın ham erişim (register/kesme/DMA) | donanım-kernel | `donanım-anayasa.md` §3 satır 47 |
| B2 | Envanter/ölçü/sağlık toplama merkezi | donanım-core | `donanım-anayasa.md` §3 satır 48 |
| B3 | Donanım türü sınıflandırması (hdd/audio/ssd) | donanaım-type | `donanım-anayasa.md` §3 satır 50 |
| B4 | OS'nin donanımı sürdüğü sürücü kodu | K000-isletim-sistemi | `donanım-anayasa.md` §10/2 |
| B5 | Cihazın boot-time kendi yazılımı | K005-firmware | `donanım-anayasa.md` §2 |
| B6 | Ses motoru / DSP zinciri | K003-audio · K004-dsp | `donanım-anayasa.md` §2 |

## §4 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| C1 | api ↔ kernel | çağrı seti (yüzey) ↔ ham register erişimi (alt) | ⚠️ anayasa §7 Q3 sınır kararı |
| C2 | api ↔ core | dışa açılan çağrı ↔ iç envanter/ölçü toplama | ⚠️ anayasa §7 Q3 sınır kararı |
| C3 | api ↔ K000 sürücü | donanımın tanıttığı arayüz ↔ onu süren OS kodu | ✅ anayasa §10/2 (sürücü kodu K000'dedir) |
| C4 | kontrol arayüzü (SPI/I2C) ↔ ses veri arayüzü (I2S/TDM) | register/kontrol yolu ↔ ses akış yolu | ✅ §5/3, §5/4 (TI PCM3168A iki ayrı arayüzü tanımlar) |
| C5 | lisans sorumluluğu | ASIO GPLv3 notu K003/K005'e aittir, K001'e değil | ✅ `donanım-anayasa.md` §4.1 |

## §5 Disk + web kanıtları

| # | İddia | Gerçek dosya yolu / URL |
|---|---|---|
| E1 | Çocuk satırı: "donanım-api · Donanıma erişim arayüzü · draft · ⚠️" | `.ai/architecture/K001-donanım/donanım-anayasa.md` §3 satır 49 |
| E2 | index.md açık sorusu: "API yüzeyi hangi protokole oturuyor (sürücü ioctl / sysfs benzeri)?" | `donanım-api/index.md` satır 15 |
| E3 | ELI15 + "tanım yalnız adından türetildi" uyarısı | `donanım-api/index.md` satır 20-24 |
| E4 | Kademe-3 ön koşlu genişletme: "Q3 sınır kararı" | `donanım-anayasa.md` §9 satır 140 |
| E5 | Lisans notu: ASIO SDK GPLv3 K003/K005 kapsamındadır | `donanım-anayasa.md` §4.1 satır 83-84 |
| E6 | ASIO açık kaynak: "available in open source form under GNU GPL version 3" | https://www.steinberg.net/developers/asiosdk-open/ |
| E7 | PCM3168A kontrol: four-wire SPI · two-wire I2C · hardware control | https://www.ti.com/lit/ds/symlink/pcm3168a.pdf |
| E8 | PCM3168A dijital arayüz: DSP · I2S · L · R · TDM | https://www.ti.com/product/PCM3168A |
| E9 | XU316 USB Audio: USB Audio Class 2.0 & 1.0 · high-speed USB 2.0 device | https://www.xmos.com/xk-audio-316-mc-ab |

## §6 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-A1 | API yüzeyi hangi protokole oturuyor (ioctl / sysfs benzeri)? | **UNKNOWN** | Vault Steward |
| Q-A2 | api ↔ kernel ↔ core sınırı (Q3) | ⚠️ VERIFICATION REQUIRED | Vault Steward (anayasa §7 Q3) |
| Q-A3 | Üst katmanların kullandığı çağrı listesi (fonksiyon/imza) | **UNKNOWN** — kanıt turu-2 | audio-hw / embedded |
| Q-A4 | ASIO SDK lisans notu K001 kapsamına mı düşer, K003/K005'te mi kalır? | ✅ kapalı — K003/K005 (§4.1) | — |

**Kapatma kuralı:** Q-A1/Q-A3 kanıt turunda netleşmeden §2/A6 satırı UNKNOWN kalır;
Q-A2 kararı gelmeden kernel/core ile ortak içerik yazılmaz.

## §7 MMIO ve IO Port Arayüzleri (Genişletilmiş — kademe-3 omurga)

Donanıma erişim iki temel adresleme yöntemiyle olur: bellek eşlemeli IO (MMIO) ve
port eşlemeli IO (PIO). Bu ikisi K001 API'sinin oturacağı düşük seviyeli zeminin
taşıyıcısıdır; hangisinin kullanılacağı Q-A1 protokol kararıyla netleşecektir.

| # | Arayüz | Adresleme | Erişim semantiği | K001 API etkisi | Durum |
|---|--------|-----------|------------------|-----------------|-------|
| M1 | MMIO | Bellek adres uzayına register eşleme | `read`/`write` bellek erişimi gibi | API çağrısı register offset'ine iner | genel kavram — kanıt zorunlu değil |
| M2 | PIO | Ayrı IO adres uzayı (x86 `in`/`out`) | IO komutuyla okuma-yazma | API çağrısı port numarasına iner | genel kavram |
| M3 | Register genişliği | 8/16/32/64-bit registerlar | Erişim genişliği register'a bağlı | API tip güvenliği register'a uymalı | genel kavram |
| M4 | Register volatility | Volatile / read-only / write-only / W1C | Yanlış okuma-yazma bozar | API register niteliğini bilmeli | genel kavram |
| M5 | Erişim sırası | Bellek bariyeri / serialize | Donanım sırası bozulmaz | API bariyer semantiği sunmalı | genel kavram |
| M6 | CoreMusic API'sinin MMIO mu PIO mu kullandığı | — | — | — | **UNKNOWN** — Q-A1 kararı |

> Genel kavram satırları (M1-M5) donanım mühendisliği kitabı bilgisidir; CoreMusic'e
> özgü iddia değildir ve `⚠️` gerektirmez. M6 satırı CoreMusic iddiasıdır → UNKNOWN.

### §7.1 MMIO vs PIO Karşılaştırma Matrisi

| Ölçüt | MMIO | PIO | K001 tercih girdisi |
|-------|------|-----|---------------------|
| Adres uzayı | Bellek (paylaşımlı) | Ayrı IO | UNKNOWN — Q-A1 |
| Register yoğunluğu | Yüksek (çok register) | Sınırlı port | MMIO eğilimi (genel) |
| Sayfalama/VM etkisi | Sayfa tablosuna tabi | Tabloya tabi değil | MMIO'da VM dikkati |
| Taşınabilirlik | ARM/RISC-V'te yaygın | x86'a özgü eğilim | Mimari bağımlılık |
| Örnekleme performansı | Bulk erişim uygun | Küçük erişim | Ses akışı MMIO eğilimli |

### §7.2 Register Erişim Kuralı Matrisi (K001 API sözleşme girdisi)

| # | Kural | Gerekçe | K001 API'ye yansıması |
|---|-------|---------|----------------------|
| R1 | Register niteliği okunmadan yazılmaz | Write-only register okunursa hata | API register metadata tablosu tutmalı |
| R2 | Read-to-clear registerlar dikkat | Okuma temizler | API okuma yan etkisini bilmeli |
| R3 | Rezerve bitler korunur | Yazma-other bits bozar | API read-modify-write deseni |
| R4 | Reset değeri bilinmeden default yazılmaz | Yanlış reset donanımı bozar | API reset tablosu |
| R5 | Erişim genişliği register'a eşit | Genişlik uyuşmazlığı bozulma | API tip genişliği |

## §8 DMA Erişim Modelleri (Genişletilmiş — kademe-3 omurga)

Doğrudan bellek erişimi (DMA), donanımın CPU'yu atlayıp belleğe doğrudan okuma-yazma
yapmasıdır. K001 API'si DMA'yı kendisi yürütmez (bu donanım-kernel/K000 sınırı), ama
API'nin DMA'yı nasıl **çağıracağı** (buffer sözleşmesi) bu düğümün kapsamındadır.

| # | DMA modeli | Açıklayıcı | Buffer sahipliği | K001 API etkisi | Durum |
|---|------------|------------|------------------|-----------------|-------|
| D1 | PIO (programlı IO) | CPU her örneği taşır | CPU | DMA yok — basit API | genel kavram |
| D2 | Burst DMA | Belirli aralıkta toplu transfer | Donanım + buffer | API buffer hazırlamalı | genel kavram |
| D3 | Descriptor ring | Halka descriptor listesi | Donanım sürücüsü | API ring boyutu bilmeli | genel kavram |
| D4 | Double buffering | İki buffer dönüşümlü | CPU/Donanım paylaşımlı | API buffer swap sözleşmesi | genel kavram |
| D5 | Scatter-gather | Dağınık buffer birleştirilir | Çoklu buffer | API iovec listesi | genel kavram |
| D6 | Coherent vs streaming | Cache tutarlılığı farklı | API cache flush | API cache semantiği | genel kavram |
| D7 | CoreMusic'in DMA buffer sayısı | — | — | — | **UNKNOWN** |

### §8.1 DMA Buffer Sözleşmesi (K001 API girdisi)

| # | Sözleşme maddesi | Zorunlu mu | K001 durumu |
|---|------------------|------------|-------------|
| B1 | Buffer hizalama (cache line / descriptor) | Evet | **UNKNOWN** — K001 Q4 |
| B2 | Buffer boyutu (ring / period) | Evet | **UNKNOWN** — sartname 512 ama API'ye bağlı |
| B3 | Buffer sahibi (kim allocate eder) | Evet | **UNKNOWN** — kural: allocate setup'ta |
| B4 | Cache flush/invalidate sorumluluğu | Evet | **UNKNOWN** — coherent/streaming |
| B5 | Buffer lifecycle (start/stop) | Evet | **UNKNOWN** |
| B6 | Zero-copy sözleşmesi | Koşul | **UNKNOWN** |

> Kural 1 (repo disiplini): buffer baştan allocate edilir, audio thread'de allocate YOK
> (`~/.config/opencode/AGENTS.md` §5). Bu kural API tasarımını bağlar ama spesifik
> buffer sayısı bilinmez → UNKNOWN.

## §9 Sürücü ↔ Donanım Sözleşmesi (Genişletilmiş — kademe-3 omurga)

Sürücü, işletim sisteminin donanımı süren kodudur ve K000 sınırındadır (anayasa §10/2).
K001 API'si bu sözleşmenin **dışa açılan yüzünü** tarif eder; sözleşmenin kendisi
K000'e aittir. Bu ayrım, api ↔ kernel ↔ K000 sınırının çekirdeğidir.

| # | Sözleşme katmanı | Sahibi | K001 API rolü | Durum |
|---|------------------|--------|---------------|-------|
| S1 | Register haritası (ham) | donanım-kernel / K005 | K001 API yalnız soyutlar | ✅ anayasa §10/2 |
| S2 | Kesme/DMA mekaniği | donanım-kernel / K000 | K001 API çağırmaz | ✅ anayasa §3 satır 47 |
| S3 | Sürücü↔OS sözleşmesi (ioctl/sysfs) | K000-isletim-sistemi | K001 API bu yüzeyi **kullanır** | ⚠️ Q-A1 protokol UNKNOWN |
| S4 | Donanım-software arayüz standardı | donanım (bu düğüm) | K001 API'nin kendisi | ✅ ad kökeni |
| S5 | Cihaz keşfi / hotplug | K000 / OS | K001 API envanteri besler | **UNKNOWN** |
| S6 | Firmware register init | K005-firmware | K001 API init'i çağırmaz | ✅ anayasa §2 |

### §9.1 Sürücü Sözleşmesi İhlal Senaryoları

| # | İhlal | Belirti | K001 API koruması | Durum |
|---|-------|---------|-------------------|-------|
| I1 | Register'a yetkisiz yazma | Donanım bozulma | API salt-okunur yüzey (K-05) | ✅ karar K-05 |
| I2 | DMA buffer'a taşma | Bellek bozulma | API boyut doğrulama | **UNKNOWN** |
| I3 | Kesme fırtınası | Sistem kilitlenme | API rate/lock-free | genel kavram |
| I4 | Reset sırası ihlali | Donanım asılı kalma | API state machine | **UNKNOWN** |
| I5 | Yanlış clock konfigürasyonu | Zamanlama hatası | API clock doğrulama | **UNKNOWN** |

## §10 Kayıt Haritası Okuma Desenleri (Genişletilmiş — kademe-3 omurga)

Kayıt haritası (register map), donanım registerlarının offset-adres-nitelik tablosudur.
K001 API'si kayıt haritasını **okur ve soyutlar**; haritanın kendisi üretici
datasheet'inden gelir ve donanım-kernel'in ham erişimidir.

| # | Harita öğesi | Alan | K001 API kullanımı | Durum |
|---|--------------|------|--------------------|-------|
| H1 | Register offset | `0xNN` | API erişim anahtarı | genel kavram |
| H2 | Register adı | metin | API anlamlı sembolik erişim | genel kavram |
| H3 | Register genişliği | bit | API tip güvenliği | genel kavram |
| H4 | Register niteliği | RO/RW/WO/W1C | API erişim kısıtı | genel kavram |
| H5 | Reset değeri | değer | API init doğrulama | genel kavram |
| H6 | Bit alanı | `[bit]` | API alan erişimi | genel kavram |
| H7 | Register grubu / bank | bank | API bank switching | genel kavram |
| H8 | CoreMusic register haritası dosyası | — | — | **UNKNOWN** — repo'da yok |

### §10.1 Kayıt Haritası Okuma Deseni Matrisi

| # | Desen | Ne | avantaj | dezavantaj |
|---|-------|----|--------|-------------|
| P1 | Offset sabiti | `#define REG_X 0x10` | Basit, derleme zamanı | Sabit değişimi zor |
| P2 | Struct overlay | `struct regs { ... }` | Alan adıyla erişim | Alignment riski |
| P3 | Bit alanı makrosu | `FIELD(GENMASK(h,l))` | Alan maskesi | Okunabilirlik |
| P4 | Tablo-driven | Array of reg metadata | Esnek, veri odaklı | Arama maliyeti |
| P5 | API soyutlaması | Sembolik fonksiyon | Donanım bağımsız | Soyutlama deliği |

### §10.2 CoreMusic Kayıt Haritası Durumu

| Soru | Cevap | Etiket |
|------|-------|--------|
| CoreMusic register haritası repo'da var mı? | Hayır — repo'da donanım seviyesi kod YOK | ✅ disk kanıtlı |
| Register haritası hangi formatta tutulacak? | — | **UNKNOWN** |
| Register haritası K001 mi K005 mi sahiplenir? | — | ⚠️ Q3 sınır kararı |
| PCM3168A register haritası referans mı? | TI datasheet'te var | ✅ §5/7 datasheet |

## §11 Donanım-Yazılım Arayüz Katmanları (Genişletilmiş)

K001 API'si, ham donanım ile üst katman (K003 ses motoru, K000 OS) arasında katman
oluşturur. Bu katmanların sıralaması ve sorumlulukları aşağıdaki gibidir.

| # | Katman | Konum | Ne yapar | Sahip |
|---|--------|-------|----------|-------|
| L1 | Fiziksel register | donanım | Ham bit | donanım |
| L2 | Ham erişim | donanım-kernel | register okuma-yazma | donanım-kernel |
| L3 | Donanım-software arayüzü | **donanım-api** | Soyut çağrı seti | bu düğüm |
| L4 | Sürücü | K000-isletim-sistemi | Donanımı sürme | K000 |
| L5 | Ses motoru | K003-audio | Akış orkestrasyon | K003 |
| L6 | Uygulama | K006-K009 | Kullanıcı etkileşimi | app |

### §11.1 Katman Soyutlama İlkesi

| # | İlke | Açıklama | K001 durumu |
|---|------|----------|-------------|
| N1 | Üst katman ham register görmez | Yalnız L3 çağrısı | ✅ anayasa §10/2 |
| N2 | L3 donanım tipine bağımlı olabilir | Her DAC farklı register | ✅ |
| N3 | L3 K003'e tip-güvenli arayüz sunar | 32-bit float (ADR-017) | ✅ ADR-017 |
| N4 | L3 lock-free olmak zorunda değil | Lock K000/L5'te | genel kavram |
| N5 | L3 envanteri L4'ten alır | DeviceManager deseni | ✅ repo §REPO |

## §12 Karar Girdileri — Resmi Kaynaklar (2026-10-13)

Aşağıdaki resmi kaynaklar, K001 API'sinin arayüz kararlarına girdi olarak kullanılmıştır.
Her URL 2026-10-13 `HEAD` isteğiyle HTTP 200 doğrulanmış ve envanterle kesişmemiştir.

| # | İddia | URL | 2026-10-13 | Durum |
|---|-------|-----|------------|-------|
| U1 | USB-IF, USB spesifikasyon ailesini resmi doküman kütüphanesinde yayımlar | https://www.usb.org/documents | 2026-10-13 | ✅ HTTP 200 |
| U2 | Linux çekirdek auxiliary bus, ayrı bileşenleri ana cihaza bağlamak için sürücü API'si sunar | https://docs.kernel.org/driver-api/auxiliary_bus.html | 2026-10-13 | ✅ HTTP 200 |
| U3 | Linux çekirdek belgelerinin kök indeksi sürücü API bölümlerini listeler | https://www.kernel.org/doc/html/latest/index.html | 2026-10-13 | ✅ HTTP 200 |

> Bu üç URL bu dosyada bir kez geçer (web-arastirma Kural 10). Envanter kesişimi 0.

## §13 Kapsam Dışı Teyitler (Genişletilmiş)

| # | Kapsam dışı konu | Nereye gider | Dayanak |
|---|------------------|--------------|---------|
| X1 | Register haritasının ham okuma-yazma mekaniği | donanım-kernel | anayasa §3 satır 47 |
| X2 | DMA transfer motorunun kendisi | donanım-kernel | anayasa §3 satır 47 |
| X3 | Sürücü kodunun kendisi | K000-isletim-sistemi | anayasa §10/2 |
| X4 | Firmware register init zamanlaması | K005-firmware | anayasa §2 |
| X5 | Ses akışının DSP'si | K003-audio · K004-dsp | anayasa §2 |

## §14 Kapsam Genişletme Özeti

| Bölüm | Öğe sayısı | UNKNOWN | ⚠️ | Kanıt |
|-------|-----------|---------|----|----|
| §7 MMIO/PIO | 6 | 1 | 0 | genel kavram |
| §8 DMA | 7 | 1 | 0 | genel kavram |
| §9 Sürücü sözleşmesi | 6 | 1 | 1 | anayasa §10/2 |
| §10 Kayıt haritası | 8 | 4 | 0 | genel kavram |
| §11 Katmanlar | 6 | 0 | 0 | anayasa §10/2 |
| §12 Resmi kaynak | 3 | 0 | 0 | HTTP 200 |
| §13 Kapsam dışı | 5 | 0 | 0 | anayasa |

**Sonuç:** donanım-api kapsamı kademe-3 omurgasını (MMIO/PIO · DMA · sürücü sözleşmesi
· kayıt haritası · katmanlar) kapsar. CoreMusic'e özgü hiçbir parametre uydurulmamıştır;
tüm spesifik değerler UNKNOWN veya ⚠️ olarak işaretlidir. Q-A1 protokol kararı
gelmeden §7.2/§8.1/§9.1 satırları UNKNOWN kalır.

## §15 MMIO Erişim Desenleri — Derin Dal (kademe-3 omurga uzantısı)

K001 API'si MMIO erişimini soyutlarken aşağıdaki desenlerin hangisini kullanacağı
UNKNOWN'dır; ancak desenlerin kendisi donanım-software arayüzünün evrensel
dilimidir ve bu dosyada tanımlanır.

### §15.1 MMIO Register Erişim Desen Tablosu

| # | Desen | Ne yapar | Risk | K001 uyumu |
|---|-------|----------|------|------------|
| P6 | Tekrar okuma (read-twice) | Volatile register doğrulama | Gecikme | UNKNOWN |
| P7 | Yaz-sonra-okuma (write-then-read-back) | Yazım doğrulama | Ekstra erişim | UNKNOWN |
| P8 | Maskeli yazma (masked write) | Bit alanı güncelleme | Yan etki | UNKNOWN |
| P9 | Set/clear register | Atomic bit değiştirme | Sıra | UNKNOWN |
| P10 | Toggle register | Yaza göre tersleme | Çift yazma | UNKNOWN |
| P11 | Shadow register | Yazım takibi (RO register) | Senkron kaybı | UNKNOWN |
| P12 | Bank/register switching | Adres bankası seçimi | Yanlış bank | UNKNOWN |

### §15.2 MMIO Hata Modları

| # | Hata modu | Belirti | Sonuç | Koruma |
|---|-----------|---------|-------|--------|
| F1 | Yanlış offset | Okuma donanım verisi değil | Bozuk veri | Offset doğrulama |
| F2 | Genişlik uyuşmazlığı | Kısmi register erişimi | Bozulma | Tip genişliği |
| F3 | Sıra ihlali | Registerlar ters sırayla | Donanım asılı | Bariyer |
| F4 | Erişim reddi (bus error) | Bus timeout / MCE | Sistem çökme | Hata yakalama |
| F5 | Cache tutarsızlığı | Eski değer okuma | Yanlış veri | Coherent DMA |
| F6 | Bellek eşlemesi yok | Sayfa hatası | Uygulama çökme | Map doğrulama |

### §15.3 MMIO Erişim Sözleşmesi (K001 API girdisi — tümü UNKNOWN)

| # | Sözleşme | Soru | Durum |
|---|----------|------|-------|
| W1 | Register aralığı kim doğrular? | API mi kernel mi | **UNKNOWN** |
| W2 | Erişim genişliği API'de nasıl ifade edilir? | uint8/16/32/64 | **UNKNOWN** |
| W3 | Endianness API'de görünür mü? | little/big | **UNKNOWN** |
| W4 | Register erişimi loglanır mı? | Telemetri | **UNKNOWN** |
| W5 | Erişim zaman aşımı nasıl işaretlenir? | Timeout semantiği | **UNKNOWN** |

## §16 DMA Zamanlama Sözleşmesi — Derin Dal

DMA zamanlaması, donanım-kernel'in işidir; ancak K001 API'sinin DMA'yı **çağırma**
zamanlaması (buffer hazır olduğunda) bu düğümün tasarım girdisidir.

### §16.1 DMA Çağrı Zamanlaması

| # | Zamanlama | Tetikleyici | API davranışı | Durum |
|---|-----------|-------------|---------------|-------|
| T1 | Period sonu kesmesi | Donanım interrupt | API buffer swap | genel kavram |
| T2 | DMA completion callback | Donanım | API hazırlık | genel kavram |
| T3 | Polling | API döngüsü | API sorgulama | genel kavram |
| T4 | Timer tabanlı | OS zamanlayıcı | API periyodik | genel kavram |
| T5 | CoreMusic callback tetikleyicisi | — | — | **UNKNOWN** |

### §16.2 DMA Buffer Yönetimi — K001 API Sorumlulukları

| # | Sorumluluk | K001 API | donanım-kernel | K000 | Durum |
|---|-------------|----------|----------------|------|-------|
| Y1 | Buffer allocate | — | ✓ | — | ✅ kural: setup'ta |
| Y2 | Buffer hizalama | — | ✓ | — | **UNKNOWN** |
| Y3 | Buffer serbest bırakma | — | ✓ | — | **UNKNOWN** |
| Y4 | Buffer boyutu seçimi | ✓ (talep) | ✓ (uygular) | — | **UNKNOWN** |
| Y5 | Buffer doldurma (fill) | ✓ | — | — | ✅ K003 |
| Y6 | Buffer boşaltma (drain) | ✓ | — | — | ✅ K003 |
| Y7 | Buffer yeniden kullanım | ✓ | — | — | **UNKNOWN** |

### §16.3 DMA-İlişkili Ölçüm Eşikleri

| # | Ölçüm | Eşik | Dayanak | Durum |
|---|-------|------|---------|-------|
| Z1 | Round-trip latency | — | — | **UNKNOWN** |
| Z2 | Buffer underrun oranı | 0 hedef | sartname | ✅ sartname 512 |
| Z3 | Buffer overrun oranı | 0 hedef | sartname | ✅ sartname 512 |
| Z4 | DMA verimlilik | — | — | **UNKNOWN** |
| Z5 | CPU yükü (DMA vs PIO) | — | — | **UNKNOWN** |

## §17 Sürücü Sözleşmesi — IOCTL/SYSFS Karar Ağacı (Genişletilmiş)

Q-A1 protokol kararı için karar ağacı aşağıdadır. Karar verilmeden hiçbir dal
seçilmez; tüm dallar açık tutulur.

### §17.1 Protokol Karar Ağacı

| # | Protokol | Ne | Örnek | K001 uygunluğu | Durum |
|---|----------|----|-------|----------------|-------|
| Q1 | ioctl | Sürücüye özel komut | Linux/Unix | Düşük seviye erişim | **UNKNOWN** |
| Q2 | sysfs | Dosya tabanlı attribute | Linux `/sys` | Okuma-yazma attribute | **UNKNOWN** |
| Q3 | configfs | Yapılandırma sistemi | Linux `/sys/kernel/config` | Dinamik yapılandırma | **UNKNOWN** |
| Q4 | netlink | Çekirdek↔kullanıcı mesaj | Linux | Olay bildirimi | **UNKNOWN** |
| Q5 | char device | Karakter cihaz dosyası | Linux `/dev` | Ham erişim | **UNKNOWN** |
| Q6 | cdev + ioctl | Karakter + komut | Linux | Kombine | **UNKNOWN** |
| Q7 | ASIO callback | Ses callback | ASIO SDK | Ses akışı | **UNKNOWN** |
| Q8 | WASAPI endpoint | Windows ses | Windows | Ses akışı | **UNKNOWN** |

### §17.2 Protokol Karar Ölçütleri

| # | Ölçüt | Ağırlık | ioctl | sysfs | char dev |
|---|-------|---------|-------|-------|----------|
| C1 | Düşük gecikme | Yüksek | ✓ | — | ✓ |
| C2 | Basit okuma | Orta | — | ✓ | — |
| C3 | Olay bildirimi | Yüksek | ✓ | — | ✓ |
| C4 | Taşınabilirlik | Orta | — | ✓ | — |
| C5 | Güvenlik yüzeyi | Yüksek | dar | geniş | dar |

> Tablo yalnız ölçüt karşılaştırmasıdır; CoreMusic protokol seçimi **UNKNOWN** (Q-A1).

### §17.3 IOCTL Komut Seti Girdisi (tümü UNKNOWN)

| # | IOCTL | Ne | Durum |
|---|-------|----|-------|
| O1 | `GET_CAPS` | Cihaz yetenekleri | **UNKNOWN** |
| O2 | `GET_STATUS` | Cihaz durumu | **UNKNOWN** |
| O3 | `SET_CONFIG` | Konfigürasyon | **UNKNOWN** |
| O4 | `START` | Akış başlatma | **UNKNOWN** |
| O5 | `STOP` | Akış durdurma | **UNKNOWN** |
| O6 | `GET_METRIC` | Ölçüm okuma | **UNKNOWN** |
| O7 | CoreMusic IOCTL listesi | — | **UNKNOWN** — repo'da yok |

## §18 Kayıt Haritası Girdi Matrisi (Genişletilmiş)

### §18.1 PCM3168A Kayıt Haritası Girdisi (Referans — K001'e aday)

| # | Alan | Değer | Kaynak | Durum |
|---|------|-------|--------|-------|
| V1 | Kontrol arayüzü | 4-wire SPI · 2-wire I2C · hardware control | TI datasheet §5/7 | ✅ |
| V2 | Mod seçimi | MODE pin strap | TI datasheet | ✅ |
| V3 | Reset | Hardware pin | TI datasheet | ✅ |
| V4 | Register tablosu offset listesi | — | TI datasheet | ⚠️ kayıt haritası dosyası yok |
| V5 | Bit alanı maskeleri | — | TI datasheet | ⚠️ kayıt haritası dosyası yok |

### §18.2 AK4458 Kayıt Haritası Girdisi (Referans)

| # | Alan | Değer | Kaynak | Durum |
|---|------|-------|--------|-------|
| W1 | Kontrol arayüzü | I2C / SPI | AKM datasheet | ✅ |
| W2 | DMDD / DIF konfigürasyonu | Register | AKM datasheet | ⚠️ dosya yok |
| W3 | Reset | Register / pin | AKM datasheet | ⚠️ dosya yok |

### §18.3 TPA3255 Kayıt Haritası Girdisi (Referans)

| # | Alan | Değer | Kaynak | Durum |
|---|------|-------|--------|-------|
| X1 | Kontrol arayüzü | **Hardware** (yazılım register haritası yok) | TI ürün §5/9 | ✅ |
| X2 | Register okuma-yazma | — | TI ürün | ❌ yok (hardware control) |

> TPA3255 satırı X3: hardware control olduğu için K001 API'si register haritası
> **okuyamaz** — bu, donanım-software arayüzünün hardware-pin'e indiği bir sınırdır.

### §18.4 Kayıt Haritası Dosyası Format Kararı (UNKNOWN)

| # | Format | Ne | Örnek | K001 durumu |
|---|--------|----|-------|-------------|
| F1 | CSV | Tablo | offset,ad,ro/rw | **UNKNOWN** |
| F2 | JSON | Yapılandırılmış | Struct | **UNKNOWN** |
| F3 | YAML | Okunabilir | Konfigürasyon | **UNKNOWN** |
| F4 | C header | Sabit | `#define` | **UNKNOWN** |
| F5 | DTS | Device tree | Linux | **UNKNOWN** |

## §19 Sürücü Sözleşmesi Hata Yönetimi (Genişletilmiş)

### §19.1 Hata Sınıflandırması (K001 API)

| # | Hata sınıfı | Ne | API tepkisi | Durum |
|---|-------------|----|-------------|-------|
| E1 | Geçici hata (retry) | Zaman aşımı | Yeniden dene | genel kavram |
| E2 | Kalıcı hata (fail) | Donanım yok | Hata döndür | genel kavram |
| E3 | Durum hatası | Yanlış sıralama | Reddet | genel kavram |
| E4 | Yetki hatası | Erişim reddi | Reddet | genel kavram |
| E5 | Donanım kaybı | Hot-unplug | Bildir | genel kavram |
| E6 | CoreMusic hata kodu listesi | — | — | **UNKNOWN** |

### §19.2 Hata → Üst Katman Sözleşmesi

| # | Hata | K001 API → K000 | K000 → K003 | Durum |
|---|------|-----------------|-------------|-------|
| E1' | Geçici | Retry sözlüğü | — | **UNKNOWN** |
| E2' | Kalıcı | Error code | Error propagation | **UNKNOWN** |
| E5' | Donanım kaybı | Hotplug event | Akış durdurma | **UNKNOWN** |

### §19.3 Hata Kaydı (Logging) Sözleşmesi

| # | Kayıt | Ne | Sahip | Durum |
|---|-------|----|-------|-------|
| G1 | Hata kodu | Numerik | API | **UNKNOWN** |
| G2 | Hata metni | Anlamlı | API | **UNKNOWN** |
| G3 | Zaman damgası | ISO 8601 | API | **UNKNOWN** |
| G4 | Register bağlamı | Offset/değer | API | **UNKNOWN** |
| G5 | Sınır (layer) | K001 | API | **UNKNOWN** |
| G6 | CoreMusic logging formatı | — | — | **UNKNOWN** |

## §20 Sürücü Sözleşmesi Güvenlik (Genişletilmiş — OWASP)

| # | Tehlike | Vektör | K001 API koruması | Durum |
|---|---------|--------|-------------------|-------|
| S1 | Register'a yetkisiz erişim | IOCTL kötüye kullanımı | yetki kontrolü | ✅ K-05 |
| S2 | DMA buffer'a taşma | Boyut ihlali | boyut doğrulama | **UNKNOWN** |
| S3 | Erişim reddi (DoS) | IOCTL fırtınası | rate limit | ✅ RateLimit |
| S4 | Bilgi sızıntısı | Register okuma | salt-okunur yüzey | ✅ K-05 |
| S5 | Firmware değişimi | OTA kötüye | firmware doğrulama | K005 |
| S6 | Buffer use-after-free | Lifecycle ihlali | RAII / smart pointer | C++ kuralı |

## §21 MMIO/PIO Karar Ağacı — Özet (Genişletilmiş)

| # | Soru | Cevap | Durum |
|---|------|-------|-------|
| Q1 | MMIO mu PIO mu? | — | **UNKNOWN** — Q-A1 |
| Q2 | Register genişliği? | — | **UNKNOWN** |
| Q3 | Register niteliği? | — | **UNKNOWN** |
| Q4 | Bariyer semantiği? | — | **UNKNOWN** |
| Q5 | Erişim sırası? | — | **UNKNOWN** |

## §22 Kapsam Matrisi — Kademe-3 Omurga Eşlemesi

| # | Omurga konusu | Bölüm | Durum |
|---|---------------|-------|-------|
| 1 | donanım-software arayüzü | §11 katmanlar | ✅ tanımlı |
| 2 | MMIO/IO port | §7 · §15 | ✅ genel kavram; CoreMusic UNKNOWN |
| 3 | DMA erişimi | §8 · §16 | ✅ genel kavram; CoreMusic UNKNOWN |
| 4 | sürücü↔donanım sözleşmesi | §9 · §17 · §19 · §20 | ✅ genel kavram; CoreMusic UNKNOWN |
| 5 | kayıt haritası okuma | §10 · §18 | ✅ referans PCM3168A/AK4458/TPA3255 |

## §23 Ölçüm Eşikleri — API Kapsamı

| # | Ölçüm | Eşik | K001 API etkisi | Durum |
|---|-------|------|-----------------|-------|
| E1 | SNR | >100 dB | API ölçüm arayüzü | ✅ anayasa §10/3 |
| E2 | THD+N | <0.01 % @1 kHz | API ölçüm arayüzü | ✅ anayasa §10/3 |
| E3 | DC offset | >0.5 V → röle | API koruma tetikleme | ✅ anayasa §10/3 |
| E4 | Buffer | 512 örnek | API buffer boyutu | ✅ sartname |
| E5 | Örnekleme | 48 kHz | API clock | ✅ sartname |
| E6 | Sayı gösterimi | 32-bit float | API format | ✅ ADR-017 |
| E7 | Topoloji | Class AB | — | ✅ sabit |

## §24 Kapsam Genişletme Özeti — Bölüm 2

| Bölüm | Öğe | UNKNOWN | Kanıt |
|-------|-----|---------|-------|
| §15 MMIO desen | 18 | 18 | genel kavram |
| §16 DMA zamanlama | 19 | 13 | genel kavram + sartname |
| §17 IOCTL/sysfs | 21 | 21 | genel kavram |
| §18 Kayıt haritası | 17 | 5 | TI/AKM datasheet |
| §19 Hata | 14 | 14 | genel kavram |
| §20 Güvenlik | 6 | 1 | K-05 · RateLimit |
| §21-MMIO/PIO | 5 | 5 | genel kavram |
| §22-Omurga eşleme | 5 | 0 | anayasa |
| §23 Eşik | 7 | 0 | anayasa · ADR-017 |
| **Toplam** | **112** | **77** | — |

**Sonuç (genişletilmiş):** donanım-api kapsamı kademe-3 omurgasının tüm konularını
kapsar. Hiçbir CoreMusic parametresi uydurulmamıştır; spesifik değerler UNKNOWN
işaretlidir. Q-A1 protokol kararı gelmeden §17 dallarının hiçbiri seçilmez;
§18/§19/§20 CoreMusic'e özgü iddia üretmez, yalnız referans ve genel kavram taşır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
