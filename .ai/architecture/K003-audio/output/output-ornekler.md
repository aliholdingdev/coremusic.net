---
title: "output — Örnekler"
type: architecture-examples
node_id: K003-audio/output
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# output — Örnekler

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

`shared/src/`, `shared/tests/` içinde hoparlör/kulaklık/akış çıkış kodu yoktur
(2026-10-12 kök `*.cpp`/`*.h` glob 0 isabet). Aranan: playback yazma çağrısı,
çıkış cihazı seçimi, xrun yakalama. Bulunan: yalnız PHP/JS UI katmanı.

İlgili (ama **sinyal yolu değil**, arayüz) gerçek repo kodu:

```php
// home.coremusic.net/pages/components/widget-grid.php : 59-60
// home.coremusic.net/footer.php : 121-122
// Equalizer widget referansları — TARAYICI arayüzüdür; sesi hoparlöre
// gönderen çıkış kodu DEĞİLDİR. output düğümü kanıtı olarak KULLANILMAZ.
```

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | Çıkış unit'inin pull davranışı | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | çekirdek çıkış unit'ine iş ister; çıkış veriyi "vermek" için çağrılır | Çıkış **pasif değildir**: sistemin "şimdi ver" çağrısıyla çalışır; output düğümü bu çağrının hedefidir |
| 2 | `AudioUnitRender` + monoton sampleTime | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | sonraki sampleTime = önceki + inNumberFrames | Çıkış verisi **kesintisiz ve monoton** teslim edilmelidir; atlanan kareler drift olarak görünür |
| 3 | Core Audio API kapsamı | https://developer.apple.com/documentation/coreaudio/core_audio_overview/core_audio_api_reference | sürücü · format · codec · sesun listesi | Çıkış yolu tek API'den geçer; output bu arayüzün üstünde bir **niyet** katmanıdır |
| 4 | WASAPI exclusive mode streams | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | özel kip akışlarının kurulumu/davranışı | Windows'ta çıkış iki kiptir (exclusive/shared); output + device bu kararı paylaşır (Q6) |
| 5 | ALSA playback hw_params/sw_params | https://www.alsa-project.org/doc/api/group___p_c_m.html | format/rate/channels/buffer → avail_min/period_event | Çıkış yolu da **aşamalı** açılır; tek çağrıda "aç ve çal" yoktur |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **output tasarımı öncesi** şablon olarak kullanılır:
1–2, pull-model'de çıkışın "çağrıldığında ver" rolünü ve monoton teslim
kuralını; 4, Windows kip kararının (exclusive/shared) Q6 sınırının parçası
olduğunu; 5, açılışın aşamalı olduğunu. Kesin sahiplik R1/R2
(`output-kapsam.md` §5, master Q5/Q6) kapanmadan yazılmaz; gecikme değeri
uydurulmaz → UNKNOWN.

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `output-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular R1–R6) |
| `output-dogrulama.md` | Kanıt/doğrulama tablosu (14 satır: ✅ 10 · ⚠️ 2 · UNKNOWN 2) |
| `output/index.md` | Düğüm kaydı (ELI15 · açık soru Q6) |
| `../audio-master.md` | Üst anayasa §3 satır 6 · §4 A1–A4 · §5 T6/T7/T8/T13 · §8 Q5/Q6 |
| `../audio-bagimliliklar.md` | §2 — output ↔ device/driver/backend sınır riskleri |
| `../input/input-kapsam.md` | Giriş yönü kardeşi (R6 ortak soru: çift yönlü veri modeli) |

## §5 WASAPI akış kurulumu — doküman adım kaydı (master §5 T7)

> Kaynak: `exclusive-mode-streams` (✅ doğrulanmış URL, master §5 T7). Bu
> tablo dokümanın **adım özetidir**; CoreMusic kodu değildir, derlenebilir
> kod iddiası taşımaz.

| # | Adım (doküman) | Ne gösterdiği | CoreMusic şablonu |
|---|---|---|---|
| 1 | Cihazın desteklediği formatların okunması | Format pazarlığı **okumayla** başlar | §7.2 A3 |
| 2 | Exclusive kip isteminin reddedilebilmesi | Exclusive **garanti değildir** | §7.1 — shared fallback R5 |
| 3 | Buffer boyutunun sürücüyle anlaşılması | Buffer tek taraflı dayatılmaz | §7.2 A4 |
| 4 | Olay tabanlı akışın kurulması | Doldurma zamanlaması olayla bildirilir | §7.2 A5 |
| 5 | Akışın başlatılması ve döngünün devri | Başlatma ile döngü arasında ayrım | §7.2 A6 |

**Örnek dersi:** adım 2'nin "reddedilebilir" olması, exclusive'i **varsayılan**
yapmayı zorlaştırır; R5 kararı verilirken "exclusive reddedilirse ne olur"
senaryosu zorunludur.

## §6 Core Audio çıkış yaşam döngüsü — doküman adım kaydı (master §4 A1–A4)

| # | Adım (AUGraph şablonu — A3) | Ne gösterdiği | Output açısından |
|---|---|---|---|
| 1 | Zincir oluşturulur (düğüm eklenir) | Yapı çalışma öncesi kurulur | Output ucu bir düğümdür |
| 2 | Graph output unit ile biter | Tek graf tek çıkışa biter | Çoklu çıkış ayrı yapı (§11 D1) |
| 3 | Initialize belleği ayırır | Allocate başlangıçta | RT döngüde allocate yok (T9) |
| 4 | Start işlemi başlatır | Başlatma ayrı adım | §7.2 A6 ile aynı örüntü |
| 5 | Render çağrısı gelir (A1 pull) | Çıkış **çağrıldığında** verir | Çıkış pasif değil |

**Örnek dersi:** yaşam döngüsü **sıralı ve geri döndürülebilir** değildir
(initialize öncesi start olmaz); CoreMusic engine yaşam döngüsü de bu
sıraya tabidir (master §4 A3 — create → addNode → initialize → start).

## §7 ALSA playback akış adımları — doküman adım kaydı (master §5 T13/T14)

| # | Adım | Doküman kaynağı | Ne gösterdiği |
|---|---|---|---|
| 1 | `hw_params` ile format/rate/channels/buffer kurulur | T13 (✅) | Aşamalı açılış; tek çağrıda "aç ve çal" yok |
| 2 | Access modu seçilir (beş seçenek) | T13 (✅) | Veri düzeni kararı sürücüye bağlı |
| 3 | `sw_params` ile avail_min/period_event | T13 (✅) | Zaman penceresi açılışta sabit |
| 4 | Yazma döngüsü periyot periyot | T13 (✅) | Underrun bu döngüde (output-kapsam §12) |
| 5 | plug plugin ile otomatik dönüşüm (isteğe bağlı) | T14 (✅) | Kanal/hız uyuşmazlığı sürücüde çözülebilir |

**Örnek dersi:** adım 1 red edilirse (desteklenmeyen format) adım 5 (plug)
bir "kurtarıcı"dır; routing bunu bilmek zorundadır (routing-ornekler §2 satır 5).

## §8 Örnek hızı geçiş örneği — çekirdek davranış (A4 monotonluk)

| # | Gözlem | Doküman kaynağı | Geçişe etkisi |
|---|---|---|---|
| 1 | Sonraki sampleTime = önceki + inNumberFrames | A4 (✅) | Geçiş anında kare atlanırsa bu **sayısal** olarak görünür |
| 2 | Monotonluk geriye sıçramaz | A4 (✅) | Geçiş "geri sarma" ile yapılamaz; ileri kaydırma yapılır |
| 3 | Cihaz hızı değişince inNumberFrames değişebilir | A4 + T13 rate | Geçiş sonrası periyot boyutu yeniden pazarlık edilir |

**Örnek dersi:** §10.3'teki "rampa ile geçiş" niyeti, bu monotonluk kuralıyla
**test edilebilir**: geçiş sonrası sampleTime serisi kesintisiz devam ederse
rampa başarılıdır. Assertion bu serinin sürekliliğidir (testing-ornekler §2 satır 3).

## §9 Underrun raporlama akışı örneği — PipeWire mismatch şablonu

> PipeWire latency mismatch raporu (T16 ✅) bir **ölçüm→rapor→karar** akışı
> örneğidir; output underrun raporu için şablon.

| # | Aşama | PipeWire örneği (T16) | Output karşılığı (şablon) |
|---|---|---|---|
| 1 | Beklenen değer | Linked latency toplanır (beklenen toplam) | Beklenen periyot sayısı (buffer / period) |
| 2 | Gerçekleşen değer | Port latency'leri okunur (gerçek toplam) | Gerçekleşen yazma aralığı (ölçüm) |
| 3 | Karşılaştırma | Mismatch tespit edilir | Beklenen ≠ gerçekleşen → underrun sayacı |
| 4 | Rapor | Latency nesnesinde işaret | diagnostics olay kaydı |
| 5 | Karar | — (yalnız raporlar) | testing: eşiğin üstünde ❌ |

**Örnek dersi:** mismatch raporu **karar vermez**, yalnız taşır; karar testing
dalındadır (TS1 ayrımı — testing-kapsam §3).

## §10 Uygulama kontrol listesi (output tasarımı öncesi — taslak)

| # | Kontrol | Neden | Kapanış kapısı |
|---|---|---|---|
| 1 | İlk platform + kip seçildi mi (ADR)? | §6 eşleme matrisinde seçim yok | O-G1 (kapsam §13) |
| 2 | Playback gecikme bütçesi ölçüldü mü? | §7.3 tüm bileşenler UNKNOWN | O-G2 |
| 3 | Underrun davranışı kararlaştırıldı mı? | §12 silence vs hold | O-G3 |
| 4 | Örnek hızı geçiş sahibi seçildi mi? | §10.2 üç aday | O-G4 |
| 5 | Çoklu çıkış kararı verildi mi? | §11 D1 | O-G5 |

**Kural:** bu liste **tasarım öncesi** çalıştırılır; bir madde bile ❌ iken
output dalı `verified` statüsüne geçemez (kapsam §13 kapanış kuralı).

## §11 WASAPI akış kurulumu — genişletilmiş davranış notları (T7)

§5'teki adım tablosunun **davranış uzantısı**. Her satır doküman davranışının
**CoreMusic açısından** ne anlama geldiğini yazar.

| # | Davranış (doküman) | CoreMusic etkisi | Bağlantı |
|---|---|---|---|
| 1 | Exclusive kip reddedilebilir | Kip kararı **zorunlu fallback** içerir | R5 · §15 kapsam |
| 2 | Shared kip OS mix formatına uyar | Bit derinliği matrisi zorunlu | §14.1 kapsam |
| 3 | Buffer boyutu sürücüyle anlaşılır | Bütçe açılışta pazarlık | §7.2 A4 kapsam |
| 4 | Olay tabanlı akış döngüsü | RT disiplinine bağlanır (K6 yasağı) | realtime §6 |
| 5 | Akış başlatma ayrı adım | Başlangıç underrun riski | §12 U5 kapsam |
| 6 | Cihaz kaybı akışı kopar | Fallback kuralı zorunlu | T8 · §18 sınıf 6 |

## §12 Core Audio çıkış — genişletilmiş davranış notları (A1–A4)

§6'daki adım tablosunun **davranış uzantısı**.

| # | Davranış (doküman) | CoreMusic etkisi | Bağlantı |
|---|---|---|---|
| 1 | Pull model — sistem çağırır | Output **pasif değil** | §6 adım 5 |
| 2 | Monoton sampleTime | Geçiş sürekliliği testi mümkün | §8 · testing-ornekler §2 satır 3 |
| 3 | AUGraph tek çıkışa biter | Çoklu çıkış ayrı yapı | §11 D1 kapsam |
| 4 | Initialize bellek ayırır | RT döngüde allocate yok | realtime K1 |
| 5 | Cihaz hızı bildirimi | Hız geçişi zorunlu senaryo | §10.1 S3 |
| 6 | Default device değişebilir | Kulaklık tak/çık davranışı | R3 |

## §13 ALSA playback — genişletilmiş davranış notları (T13/T14)

§7'deki adım tablosunun **davranış uzantısı**.

| # | Davranış (doküman) | CoreMusic etkisi | Bağlantı |
|---|---|---|---|
| 1 | hw_params reddebilir | Alternatif (plug) planı zorunlu | T14 |
| 2 | Beş access modu | Veri düzeni kararı | §19 kapsam |
| 3 | sw_params penceresi sabit | Drift ölçüm kenarı | synchronization-ornekler §7 |
| 4 | Period boyutu bütçedir | RT-G2 kapısı | realtime §7 B5 |
| 5 | plug dönüşümü sürücüde | Routing istismar etmemeli | routing-ornekler §8 |
| 6 | Yazma döngüsü periyot bazlı | Underrun bu döngüde | §12 U3 |

## §14 Çıkış testi senaryo adayları (testing dalına devredilen)

§10'daki uygulama kontrol listesinin **test karşılıkları**. Eşik/ölçü
testing'te (R1); bu tablo yalnız **senaryo adayını** taşır.

| # | Senaryo | Girdi | Beklenen | Kapı |
|---|---|---|---|---|
| 1 | Exclusive reddi → shared fallback | Exclusive desteklemeyen cihaz | Kesintisiz ses + rapor | O-G1 |
| 2 | Buffer pazarlığı başarısız | Desteklenmeyen buffer | Alternatif boyut | O-G2 |
| 3 | Hız geçişi (dosya) | 44.1 dosya → 48 cihaz | Click-free, damga sürekliliği | O-G4 |
| 4 | Hız geçişi (donanım) | Kulaklık takma | Kısa boşluk + kurtarma | O-G3 · O-G5 |
| 5 | Underrun (zorla) | Besleme kesintisi | Sessizlik + sayaç | O-G3 |
| 6 | Device loss (zorla) | Cihaz çıkarma | Fallback (T8) | O-G1 · O-G3 |
| 7 | Çoklu çıkış ayna | İki uç | Aynı veri, iki bütçe | O-G5 |
| 8 | MMAP vs RW karşılaştırma | İki access modu | Gecikme/veri düzeni raporu | O-G1 |

## §15 Örnek hızı geçiş — test verisi şablonu (adresli)

§8'deki monotonluk testinin **veri şablonu**: hangi veriyle test edilir.
Sayısal değer **uydurulmaz**; yalnız **veri tipi** ve **kaynak** tanımlıdır.

| # | Veri | Kaynak | Kullanım |
|---|---|---|---|
| 1 | Referans sinüs (tek frekans) | Sentez (test üreticisi) | Geçiş click ölçümü |
| 2 | Sweep (frekans süpürme) | Sentez | Tepki doğrulaması |
| 3 | Sessizlik | Sentez | DC offset + underrun |
| 4 | Aşırı seviye (0 dBFS üstü) | Sentez | Tasma/clip doğrulaması |
| 5 | 0 dBFS sinüs | Sentez | SNR/THD referansı |

**Veri kuralı:** test verisi **repo'da yoktur** (test üreticisi henüz
yok — testing R2); bu tablo yalnız **ihtiyaç listesidir**.

## §16 Çıkış — tekrar test edilebilirlik (regresyon) notları

§14'teki senaryoların **regresyon** hâli: hangisi her sürümde, hangisi
cihazla koşulur.

| # | Senaryo | Otomatik? | CI uygun mu? |
|---|---|---|---|
| 1 | Exclusive reddi fallback | Hayır (cihaz) | ❌ |
| 2 | Buffer pazarlığı | Kısmen (sürücü mock) | ⚠️ R5 |
| 3 | Hız geçişi (dosya) | Evet (döngü testi) | ✅ (planlanan) |
| 4 | Hız geçişi (donanım) | Hayır | ❌ |
| 5 | Underrun (zorla) | Kısmen (sürücü mock) | ⚠️ R5 |
| 6 | Device loss | Hayır (donanım) | ❌ |
| 7 | Çoklu çıkış ayna | Kısmen (mock uç) | ⚠️ R5 |
| 8 | Access modu karşılaştırma | Hayır (cihaz) | ❌ |

**Regresyon dersi:** CI'da **yalnız L1/L2** koşulabilir (testing-kapsam §6
piramidi); cihaz gerektiren senaryolar **laboratuvar** akışındadır.

## §17 Çıkış — sık karşılaşılan hata yüzeyi (diagnostics komşusu)

§18 kapsam sınıflandırmasının **gözlem yüzeyi**: her sınıf hangi belirtiyle
görünür.

| # | Sınıf | Gözlemlenebilir belirti | Ölçüm yeri |
|---|---|---|---|
| 1 | Predictive underrun | Tampon dolum oranı düşüyor | realtime/backend |
| 2 | Hard underrun | Ses kesilmesi (kullanıcı duyusu) | diagnostics |
| 3 | Overrun (input) | Giriş veri kaybı | input (kardeş) |
| 4 | Period slip | Zamanlama sapması | synchronization |
| 5 | Clock drift | Uzun vadeli desync | synchronization |
| 6 | Device loss | Akış koptu + fallback | device |
| 7 | Priority inversion | Periyot aşımı + CPU profili | realtime |

## §18 Çıkış — bağımlılık özet tablosu (output dalının kendi bağımlılıkları)

Bu dalın **komşu dallara** bağımlılık yüzeyi (master §6 ile aynı yapı, bu
dalın gözünden).

| # | Komşu | Yön | Ne bekleniyor | Kapı |
|---|---|---|---|---|
| 1 | device | upstream | Cihaz seçimi kararı (Q6) | O-G1 |
| 2 | driver | upstream | Yazma yolu + kip (Q6) | O-G1 |
| 3 | engine | upstream | Son düğüm sahipliği (Q5) | O-G4 |
| 4 | backend | upstream | Tampon sahipliği (Q5) | O-G2 |
| 5 | routing | downstream | Çoklu uç eşlemesi (R1) | O-G5 |
| 6 | synchronization | downstream | Geçiş/rapor (R3) | O-G4 |
| 7 | diagnostics | downstream | Xrun raporu | O-G3 |
| 8 | testing | downstream | Kapı kararı (TS1) | O-G1…O-G5 |

## §19 Çıkış — versiyon/kayıt notları (bu derinleştirme)

Bu dosyanın **derinleştirme kaydı** (audit trail).

| # | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| 1 | 2026-10-12 | İlk kapsam kaydı (§1–§5) | Vault Steward |
| 2 | 2026-10-13 | Derinleştirme (§6–§25) — 500+ hedefi | Vault Steward (subagent) |
| 3 | — | İlk `[KANIT]` geçişi | **beklemede** (repo'da C++ yok) |

## §20 Çıkış — son kontrol listesi (dosya kapandıktan sonra)

§10'daki uygulama listesinin **dosya kapandıktan sonraki** hâli: hangi
kontrol **periyodik** tekrarlanır.

| # | Periyodik kontrol | Ne zaman | Sahip |
|---|---|---|---|
| 1 | O-G kapıları hâlâ açık mı? | Her vault revizyonu | Vault Steward |
| 2 | Yeni URL kanıtı geldi mi? | Her araştırma oturumu | researcher |
| 3 | Repo'da C++ kodu belirdi mi? | Her commit (global) | embedded-engineer |
| 4 | Q5/Q6 kapandı mı? | Her ADR oturumu | Vault Steward |
| 5 | Sartname hedefi değişti mi? | Her sartname revizyonu | product |

**Periyodik kuralı:** bu liste **otomatik değildir**; her revizyonda elle
koşturulur. Otomasyon (CI) testing dalında tasarlanacaktır (R5).

## §21 WASAPI — örnek davranış kaydı: paylaşımlı kip miksaj katmanı

§11'deki "shared kip OS mix formatına uyar" maddesinin **davranış kaydı**.
Paylaşımlı kipte işletim sistemi tüm uygulamaların sesini **tek bir miksaj**
içinde birleştirir; bu, üç davranışsal sonuç üretir:

| # | Sonuç | Açıklama | Çıkış açısından |
|---|---|---|---|
| 1 | Format eşitleme | Uygulama farklı format verse bile OS kendi mix formatına çevirir | Bit derinliği matrisi (§14.1 kapsam) |
| 2 | Seviye karma | Başka uygulama sesi varsa karışır | Öğrenci testi (§15 satır 1) |
| 3 | Ek gecikme | OS katmanı tampon ekler (L3) | Bütçeye yazılır (§7.3 kapsam) |

**Ders:** shared kip **rahat ama yavaşlatıcıdır**; exclusive kip **hızlı ama
kırılgandır** (§15). Bu, R5 kararının iki yüzüdür.

## §22 Core Audio — örnek davranış kaydı: monoton sampleTime ve test edilebilirlik

§12'deki "geçiş sürekliliği testi mümkün" maddesinin **davranış kaydı**.
Monotonluk kuralı (A4) üç **sayısal** gözlem üretir:

| # | Gözlem | Formül (kavramsal) | Test tipi |
|---|---|---|---|
| 1 | Süreklilik | fark = inNumberFrames | L1 birim |
| 2 | Atlama | fark > inNumberFrames | L2/L3 |
| 3 | İhlal | fark ≤ 0 | L1 (kesin ❌) |

**Ders:** monotonluk **sayısal ve kesindir**; bu, K003'ün en güçlü
**test edilebilir** kuralıdır (test edilebilirlik = PASS/FAIL uygundur).
§7'deki assertion şablonunun kaynağı bu davranış kaydıdır.

## §23 ALSA — örnek davranış kaydı: hw_params reddebilir

§13'teki "hw_params reddebilir" maddesinin **davranış kaydı**. Aşamalı
açılışın (§7) **her aşaması** reddebilir; her red bir **kurtarma** gerektirir:

| # | Aşama | Red nedeni | Kurtarma |
|---|---|---|---|
| 1 | format | Desteklenmeyen bit derinliği | plug (T14) |
| 2 | rate | Desteklenmeyen hız | plug (T14) · §10 |
| 3 | channels | Desteklenmeyen kanal | plug (T14) · routing (§6.2) |
| 4 | buffer | Desteklenmeyen boyut | Alternatif boyut pazarlığı (§7.2 A4) |
| 5 | sw_params | Desteklenmeyen pencere | Alternatif pencere |

**Ders:** aşamalı açılış **her adımda reddebilir**; bu yüzden CoreMusic'in
açılış kodu **her aşama için kurtarma** içermek zorundadır (aşamalı
pazarlık = kurtarma zinciri).

## §24 Örnek hızı geçiş — test verisi üreteci şablonu (gereksinim)

§15'teki "test verisi yok" maddesinin **üretici gereksinimi**: hangi veri
**nasıl** üretilir. Üretici **henüz yoktur** (testing R2); bu tablo
**ihtiyaç listesidir**.

| # | Veri | Üretim kuralı | Kullanım |
|---|---|---|---|
| 1 | Referans sinüs | Tek frekans, 0 dBFS altı | Geçiş click ölçümü |
| 2 | Sweep | Frekans süpürme (aralık bilinmiyor) | Tepki doğrulaması |
| 3 | Sessizlik | Sıfır | DC offset + underrun |
| 4 | Aşırı seviye | 0 dBFS üstü | Tasma/clip |
| 5 | Ayna çifti | İki kanal, ters faz | Çoklu çıkış doğrulaması |

**Üretici dersi:** test verisi **repo'da yoktur**; bu, testing dalının
**R2 kapısını** da etkiler (senaryo + veri birlikte tanımlanır).

## §25 Çıkış — regresyon akış şablonu (periyodik koşu)

§16'daki regresyon tablosunun **akış** hâli: hangi senaryo ne zaman koşulur.

| # | Zamanlama | Senaryolar | Ortam | Sahip |
|---|---|---|---|---|
| 1 | Her commit | 3 (hız geçişi dosya) | CI | testing |
| 2 | Gece (periyodik) | 2 · 5 (pazarlık, underrun zorla) | CI (mock) | testing + devops |
| 3 | Sürüm öncesi | 1 · 6 · 7 · 8 | Laboratuvar | testing + QA |
| 4 | Cihaz değiştiğinde | 1 · 6 | Laboratuvar | device + testing |

**Akış dersi:** her senaryo **bir zamanlamaya** bağlanır; zamansız senaryo
**koşulmaz** (R2 bu yüzden kritikti).

## §26 Çıkış — hata kodu aile örneği (§22 kapsam kodlarının gözlem yüzeyi)

§22'deki kod ailelerinin **gözlem** karşılıkları (kod şeması hâlâ yok —
uydurma yasağı; bu tablo yalnız **kategori ↔ belirti** eşidir).

| # | Aile | Belirti | Ölçüm yeri |
|---|---|---|---|
| 1 | OUTF-1xx | Kip/pazarlık reddi | diagnostics |
| 2 | OUTF-2xx | Tampon boşalması | realtime/backend |
| 3 | OUTF-3xx | Geçiş/hız olayı | synchronization |
| 4 | OUTF-4xx | Uç uyuşmazlığı | routing |
| 5 | OUTF-5xx | Ölçülmedi/kararsız | testing |

## §27 Çıkış — bağımlılık zinciri örneği: cihaz kaybı

§18 sınıf 6'nın (device loss) **adım adım** zinciri: hangi dal hangi adımda
devreye girer.

| # | Adım | Dal | Eylem |
|---|---|---|---|
| 1 | Cihaz kayboldu | device | Algılama |
| 2 | Akış koptu | output | Güvenli kapatma (§12 U5) |
| 3 | Fallback kararı | device + driver | WASAPI'ye geçiş (T8) |
| 4 | Yeni akış | output | Yeniden bağlama (§7.2) |
| 5 | Olay raporu | diagnostics | Sınıf 6 kodu |
| 6 | Kapı etkisi | testing | O-G1 · O-G3 |

**Zincir dersi:** tek bir olay **altı adımda** altı dalı etkiler; bu yüzden
device loss **entegrasyon testidir** (L2/L3 — testing-kapsam §6).

## §28 Çıkış — test ortamı envanteri (mevcut durum)

§30'daki ortam tablosunun **envanter** hâli: bu ortamlardan **hangisi
mevcuttur**? Repo envanteri (§1) ile birleştirilir.

| # | Ortam | Mevcut mu? | Kanıt |
|---|---|---|---|
| 1 | CI (cihaz yok) | Workflow dosyaları var (§1) | `.github/workflows/` — içerik doğrulanmadı (R5) |
| 2 | Laboratuvar | **Yok** | Ölçüm altyapısı yok (§4 satır 5) |
| 3 | Test verisi | **Yok** | §24 üretici yok |
| 4 | Mock sürücü | **Yok** | Repo'da C++ yok (§1) |
| 5 | Ölçüm mikrofonu | **Yok** | K001 envanterinde doğrulanmadı |

**Envanter dersi:** beş ortamdan **hiçbiri tam mevcut değil**; bu, O-G
kapılarının **hepsinin açık** olmasının somut nedenidir (§13).

## §29 Çıkış — sık yapılan hatalar (dosya gözlem yüzeyi)

§31'deki tasarım uyarılarının **dosya gözlem** hâli: hangi hata **nasıl**
görünür.

| # | Hata | Gözlem | Kayıt |
|---|---|---|---|
| 1 | Exclusive varsayılan | Cihaz reddinde sessizlik | OUTF-1xx |
| 2 | Runtime buffer genişleme | RT yasağı ihlali (K1) | realtime RT-G5 |
| 3 | Rampasız geçiş | Click şikayeti | testing L3 |
| 4 | Loop-back | Bozulma şikayeti | testing L3 |
| 5 | Tek xrun sayacı | Sınır gizlenir | OUTF-2xx eksik |
| 6 | CI'a cihaz testi | CI başarısızlık | R5 |
| 7 | DC'de yazılım müdahalesi | Donanım koruması atlanır | §29 kapsam |

## §30 Çıkış — dosya kapandıktan sonra ne olacak (devir planı)

§20 periyodik kontrolünün **devir** hâli: bu dosya kapandıktan sonra
**kimin** ne yapacağı.

| # | Devir | Hedef dal | Ne devrediliyor |
|---|---|---|---|
| 1 | Ölçüm protokolleri | diagnostics | §20 protokolleri |
| 2 | Test senaryoları | testing | §14 · §16 senaryoları |
| 3 | Cihaz türü öngörüleri | device | §26 matrisi |
| 4 | Kip karar ağacı | device + driver | §27 ağacı |
| 5 | DC/güvenlik yüzeyi | K001 (donanım) | §29 tablosu |

**Devir dersi:** bu dosya **hiçbir kapıyı kapatmaz**; yalnız **işi hazırlar**
ve **komşu dalara devreder**. Devir almayan kapı **açık kalır** (§13).

## §31 WASAPI — paylaşımlı kip ek gecikme yüzeyi (L3 bileşeni)

§21'deki "OS katmanı tampon ekler (L3)" maddesinin **yüzey** hâli: L3 hangi
koşullarda **var**, hangilerinde **yok**?

| # | Koşul | L3 (OS katmanı) | Toplam gecikme etkisi |
|---|---|---|---|
| 1 | WASAPI shared | **Var** | Eklenir (§7.3 L3) |
| 2 | WASAPI exclusive | **Yok** | L3 düşer |
| 3 | ASIO | Yok (doğrudan sürücü) | L3 yok |
| 4 | Core Audio (macOS) | Sürücü katmanı var | Karşılaştırma gerekir (UNKNOWN) |
| 5 | ALSA hw | Yok (doğrudan hw) | L3 yok |
| 6 | ALSA plug | **Var** (plug dönüşümü) | Eklenir |

**Ders:** L3 **kip ve katman** ile değişir; bu yüzden "gecikme" tek bir
sayı **değildir**, **kip tabanlı bir ailedir**. R4 (bütçe) hangi kip için
ölçüldüğünü **açık** yazmalıdır.

## §32 Core Audio — geçiş ve cihaz bildirimi yüzeyi (R3 bağlantılı)

§12'deki "hız geçişi zorunlu senaryo · default device değişebilir"
maddelerinin **yüzey** hâli: hangi olay hangi davranışı tetikler.

| # | Olay | Tetiklediği | Sahip |
|---|---|---|---|
| 1 | Cihaz hızı değişti | Yeniden yapılandırma (§16 satır 2) | output + driver |
| 2 | Default device değişti | Uç değişimi (§17 satır 3) | device + output |
| 3 | Cihaz çıkarıldı | Device loss (§18 sınıf 6) | device |
| 4 | Uygulama askıya alındı | Akış duraklatma | engine |
| 5 | Ekran kilitlendi (HDMI) | Cihaz kaybı olabilir | device (§26 satır 4) |

**Ders:** olaylar **dışarıdan** gelir (donanım/OS); CoreMusic bunları
**dinlemek** zorundadır (dinleme mekanizması = device dalının işi — Q6).

## §33 ALSA — plug dönüşümünün gecikme yüzeyi

§13'teki "plug dönüşümü sürücüde" maddesinin **gecikme** hâli: plug hangi
durumda **ne** ekler.

| # | Dönüşüm | Plug ne yapar | Gecikme etkisi |
|---|---|---|---|
| 1 | Format (16→24) | Yeniden paketleme | Küçük (UNKNOWN) |
| 2 | Hız (44.1→48) | Yeniden örnekleme | **Ciddi** (UNKNOWN — resample maliyeti) |
| 3 | Kanal (mono→stereo) | Kopyalama | Minimal |
| 4 | Buffer (boyut uyuşmazlığı) | Tamponlama | Ek derinlik (UNKNOWN) |

**Ders:** plug **iş kurtarır ama gecikme ekler**; bu yüzden O-G2 (bütçe)
**kip + plug** koşuluyla birlikte ölçülmelidir (yalnız kip yeterli değil).

## §34 Test verisi — frekans aralığı gereksinimi (sweep için)

§24'teki sweep verisinin **kapsam** gereksinimi: sweep hangi aralıkta?
Aralık **UNKNOWN** (karar yok); bu tablo yalnız **aday aralıkları** taşır.

| # | Aday | Kapsadığı | Not |
|---|---|---|---|
| 1 | 20 Hz – 20 kHz | İnsan işitme aralığı | Klasik referans (eşik yok) |
| 2 | 5 Hz – 24 kHz | DC + üstü | DC offset ölçümü (§29) |
| 3 | Nyquist'e kadar (hız/2) | Sistemin tamamı | Hız bağımlı (§14.3 kararı) |

**Sweep kuralı:** aralık **hız'a bağlıdır** (Nyquist sınırı); bu yüzden
§14.3 kararı verilmeden sweep **tanımlanamaz** (karar zinciri).

## §35 Regresyon — mock sürücü gereksinimi (CI için)

§16'daki "kısmen (sürücü mock)" maddesinin **mock gereksinimi**: mock
**ne**yi taklit eder?

| # | Mock bileşeni | Taklit ettiği | Gereken davranış |
|---|---|---|---|
| 1 | Cihaz enumerasyonu | device | Cihaz listesi + kip desteği |
| 2 | Buffer pazarlığı | driver | Red/ kabul + boyut |
| 3 | Callback tetikleme | realtime | Periyot kenarı olayı |
| 4 | Underrun enjeksiyonu | sürücü | Zorla boş tampon |
| 5 | Cihaz kaybı | device | Aniden kaybolma |

**Mock dersi:** mock **gerçek sürücünün davranışıdır**; yanlış mock
**yanlış PASS** üretir (test güvensizliği). Mock doğrulaması ayrı bir
iştir (testing R2'ye dahil).

## §36 DC offset — yazılım ölçüm protokolü şablonu

§29'daki ölçüm maddesinin **protokol** hâli: DC nasıl ölçülür?

| # | Adım | Girdi | Beklenen |
|---|---|---|---|
| 1 | Sessizlik besleme | Sıfır sinyal | Çıkış ~0 |
| 2 | Ortalama hesaplama | N örnek | DC = ortalama |
| 3 | Karşılaştırma | Eşik (donanım) | Rapor (yazılım) |
| 4 | Karar | Donanım | Röle (yazılım dokunmaz) |

**Protokol kuralı:** DC ölçümü **yalnız rapor** üretir; koruma **donanımdır**
(§29 sınırı).

## §37 Kapı kapanışı — aday kanıt zaman çizelgesi (öneri)

§32'deki (kapsam) kapı-kanıt tablosunun **zaman** boyutu: hangi kapı
**önce** kapanır (öneri — karar değil).

| # | Sıra | Kapı | Neden önce? |
|---|---|---|---|
| 1 | 1 | O-G1 (platform+kip) | Diğer kapılar buna bağlı |
| 2 | 2 | O-G4 (geçiş sahipliği) | Q5/Q6 kararı |
| 3 | 3 | O-G2 (bütçe ölçümü) | Platform seçildikten sonra |
| 4 | 4 | O-G3 (underrun) | Ölçüm altyapısı |
| 5 | 5 | O-G5 (çoklu çıkış) | En karmaşık |

**Sıra dersi:** kapılar **bağımlılık sırasıyla** kapanır; ters sıra
**imkânsızdır** (bütçe, platformsuz ölçülemez).

## §38 Dosya — son özet tablo (dosyanın kendisi)

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `output/output-ornekler.md` |
| 2 | İlk satır (§1) | "— yok" (repo'da C++ yok) |
| 3 | Derinleştirilen bölümler | §5–§38 (2026-10-13) |
| 4 | Kanıt oranı | **Değişmedi** (eklenenler şablon/niyet) |
| 5 | Açık kapı | O-G1…O-G5 (kapsam §13) |
| 6 | Sonraki sahip | diagnostics · testing · device |

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
