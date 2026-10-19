---
title: "output — Kapsam"
type: architecture-scope
node_id: K003-audio/output
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# output — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 6 bu dalı **"Çıkış tarafı (hoparlör, kulaklık, akış)"**
olarak yazar. Basitçe: işlenmiş sesin **nereye gittiğini** son kez yönlendiren
düğüm — hoparlöre, kulaklığa ya da bir ağa giden akışa. Su boru hattı
benzetmesindeki **çesme**: su (işlenmiş ses) buradan dışarı çıkar; çeşmenin
içindeki mekanizma ayrı bir düğümdür (device/driver).

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (v2 §3 KURAL 3); içerik iddiası değildir. Çıkış kipleri (exclusive/shared),
> tamponlama ve çoklu çıkış davranışı kanıt turunda netleşecektir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Çıkış uçlarının tanımlanması (hoparlör · kulaklık · akış) — adından türetilmiş | Çıkış cihazının envanteri ve seçimi | device (master §3 satır 2) |
| 2 | Zincirin sonundan dışarı **veri verme** işleminin sahipliği (pull-model'de son düğüm) | Zinciri yürüten ana döngü | engine (master §3 satır 1) |
| 3 | Çıkış gecikmesi (playback latency) niyetinin taşınması | Ölçüm metodu ve rapor | diagnostics (master §3 satır 11) |
| 4 | Çoklu çıkış (aynı anda hoparlör + kulaklık) niyetinin tanımı | Kanal yönlendirme kuralları | routing (master §3 satır 7) |
| 5 | Çıkış kesintisi (underrun/xrun) durumunda güvenli davranış niyeti | Xrun'un sınıflandırılması ve kabul kriteri | diagnostics · testing |
| 6 | *(dışında)* cihazla sürücü üzerinden konuşma (yazma yolu) | Sürücü çevirimi | driver (master §3 satır 3) |
| 7 | *(dışında)* buffer/kuyruk doldurma altyapısı | Bellek ve kuyruk yönetimi | backend (master §3 satır 4) |
| 8 | *(dışında)* sesin şekillendirilmesi (EQ, dinamik, reverb) | DSP zinciri | K004-dsp (master §2.2) |

**Yasak:** satır 1'deki ayrım Q6 kapanana kadar **sınır önerisi** değildir;
output ile device aynı kararı iki yerde verirse `audio-bagimliliklar.md` §2
çakışma riski gerçekleşir.

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| O1 | output ↔ device | "nereye ver" (çıkış ucu) ↔ "hangi cihaz" (envanter/seçim) | ⚠️ master §8 Q6 — VERIFICATION REQUIRED |
| O2 | output ↔ driver | veri verme niyeti ↔ sürücü çevirimi (playback) | ⚠️ ad-türetme (master §3 satır 6 ve 3) |
| O3 | output ↔ engine | son düğüm olarak veri üretme ↔ döngüyü adımlama | ⚠️ Q5 ile kilitli (pull-model sahipliği) |
| O4 | output ↔ backend | verinin teslimi ↔ teslim altyapısı (buffer/kuyruk) | ⚠️ ad-türetme (master §3 satır 6 ve 4) |
| O5 | output ↔ routing | tek çıkış ucu ↔ çoklu akış eşlemesi | ⚠️ Q6 ile bağlantılı — üst karar |
| O6 | output ↔ mixing | teslim ↔ birleştirme | ✅ master §3 satır 8 (ayrı çocuklar) |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "output · Çıkış tarafı (hoparlör, kulaklık, akış) · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 67 | ✅ dosya okundu |
| 2 | Çıkış unit'leri veriye isteğe bağlı **pull** eder | `audio-master.md` §4 A1–A2 (Apple: AudioUnitProgrammingGuide) | ✅ dosya okundu |
| 3 | `AudioUnitRender` her render'da `inTimeStamp.sampleTime` monoton artar | `audio-master.md` §4 A4 (Apple: AudioUnitRender) | ✅ dosya okundu |
| 4 | Açık soru Q6: input/output ↔ device/driver sorumluluk ayrımı | `audio-master.md` §8 Q6 | ✅ dosya okundu |
| 5 | WASAPI exclusive ↔ shared kipi seçeneği (çıkış kipi kararı adayı) | `audio-master.md` §5 T6/T7 → learn.microsoft.com WASAPI | ✅ dosya okundu (URL fetch) |
| 6 | ALSA playback yolu: hw_params/sw_params + beş access modu | `audio-master.md` §5 T13 → alsa-project.org pcm.html | ✅ dosya okundu (URL fetch) |
| 7 | ASIO → WASAPI fallback kuralı (cihaz kaybında çıkışın sürdürülmesi) | `.ai/AGENTS.md` §17 #6 · `audio-master.md` §5 T8 | ✅ dosya okundu |
| 8 | Repo'da çıkış (playback) kodu yok | kök `*.cpp`/`*.h` glob 0 isabet (2026-10-12) | ✅ shell ölçümü |
| 9 | UI'da equalizer widget referansı vardır (ses çıktısı DEĞİL, arayüz) | `home.coremusic.net/pages/components/widget-grid.php` satır 59–60 | ✅ dosya okundu |
| 10 | Çıkış gecikme bütçesi (ms) ve çoklu çıkış davranışı | — | **UNKNOWN** (ölçüm/tasarım yok) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | output ↔ device/driver sorumluluk ayrımı? (master §8 Q6) | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R2 | Pull-model'de son düğümün (çıktının) veri sahipliği kime ait? (master §8 Q5) | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R3 | Çoklu çıkış (hoparlör + kulaklık aynı anda) desteklenecek mi? | ⚠️ VERIFICATION REQUIRED | embedded-engineer |
| R4 | Playback gecikme bütçesi kaç ms? | **UNKNOWN** — ölçüm yok | performance-engineer |
| R5 | exclusive/shared kipi varsayılanı nedir (Windows)? | ⚠️ VERIFICATION REQUIRED (T6/T7 kanıtlı, karar yok) | embedded-engineer |
| R6 | Underrun/xrun olduğunda çıkış nasıl davranır (sessiz mi, tekrar mı)? | **UNKNOWN** — tasarım yok | diagnostics |

**Kapanış:** status: draft kalır. R1/R2 kapanmadan "output cihazı seçmez" ya da
"sahip engine'dir" iddiası yazılmaz; R4/R6 için sayı/parametre uydurulmaz →
UNKNOWN.

## §6 Çıkış uçları ↔ platform eşleme matrisi (şablon — karar yok)

§2 satır 1'deki "çıkış uçlarının tanımlanması" maddesinin platform bazlı
şablonu. Bu tablo **eşleme taslağıdır**; hangi platformun hedef olduğu
(CoreMusic ilk platformu) kararlaştırılmamıştır → sütunlar **aday** olarak
okunur, seçim iddiası değildir.

| # | Platform | Çıkış API'si | Kip seçeneği | Çıkış ucu karşılığı | Kanıt durumu |
|---|---|---|---|---|---|
| M1 | Windows | WASAPI | shared · exclusive | `IAudioClient` ile başlatılan render akışı | ✅ T6/T7 (master §5) — API var, CoreMusic eşlemesi **karar yok** |
| M2 | Windows | ASIO | exclusive (tek uygulama) | sürücü callback'inin beslediği çıktı tamponu | ✅ T1 (master §5) — API var; ASIO→WASAPI fallback kuralı iç kanıt (T8) |
| M3 | macOS | Core Audio | tek default output device | `AudioDeviceIOProc` / render notify akışı | ✅ §4 A1–A4 (pull model) — CoreMusic eşlemesi **karar yok** |
| M4 | Linux | ALSA | hw · plug | `snd_pcm` playback handle'ı | ✅ T13/T14 (master §5) — access modları beş adet |
| M5 | Linux | PipeWire | graph sink | graph'a bağlı output node'u | ✅ T15–T17 (master §5) — node/port/link modeli |
| M6 | Linux | JACK | process callback | graf ucu (patchbay) | ⚠️ yalnız ad — master §5.1'de URL'siz geçiyor; erişim YOK |

**Eşleme kuralı (şablon):** her çıkış ucu (hoparlör · kulaklık · akış) en az
**bir** platform karşılığına bağlanır; platform karşılığı olmayan uç
**çalışmaz** (desteklenmiyor etiketi alır). Birden fazla platform karşılığı
varsa (ör. Windows'ta hem WASAPI hem ASIO) bu bir **kip kararıdır** → R5.

> ⚠️ VERIFICATION REQUIRED: tablodaki satırlar API varlığını gösterir;
> CoreMusic'in hangi satırı seçtiği **karar olarak kaydedilmemiştir**. İlk
> platform seçimi ayrı bir karar (ADR) gerektirir.

## §7 WASAPI çıkış şablonu (Windows — ayrıntı kaydı)

Kaynak: master §5 T6 (wasapi ana sayfa) ve T7 (exclusive mode streams) —
her ikisi de ✅ doğrulanmış URL. Bu bölüm T6/T7'nin K003-output açısından
**okunmuş notudur**, yeni iddia üretmez.

### §7.1 Kip karşılaştırması (çıkış açısından)

| Özellik | Shared kip | Exclusive kip | Çıkışa etkisi (K003) |
|---|---|---|---|
| Kim paylaşır | Windows ses motoru (mix motoru) tüm uygulamalarla paylaşırlar | Tek uygulama cihazı doğrudan kullanır | shared'de ek bir OS miksajı katmanı vardır → gecikme bileşeni ekler |
| Format kontrolü | OS formatı dayatır (genelde shared mix formatı) | Uygulama formatı sürücüye yazılır | exclusive'de format uyuşmazlığı doğrudan hatadır; shared'de OS çevirir |
| Öncelik/kilit | Paylaşımlı | Exclusive lock (kural #9: tek uygulama) | ASIO zaten exclusive'tür; WASAPI exclusive ile çakışma riski ayrı bir kapı |
| Cihaz değişimi | OS yönetir (default device değişimi) | Uygulama kendi cihaz tutar | default device değişikliği shared'de otomatik akabilir; exclusive'de yeniden bağlama gerekir (R3) |
| Uygulama karışımı | Diğer uygulamaların sesi karışır | Yalnız bu uygulama ses verir | stüdyo kullanımı exclusive'i, masaüstü paylaşımı shared'i önerir — **karar R5** |

### §7.2 Akış açılış aşamaları (şablon — sıralı)

T7 belgesindeki exclusive-mode stream kurulum akışının K003-output
açısından aşama kaydı. Aşama adları dokümandaki kavramlara dayanır;
CoreMusic çağrısı **yoktur** (repo'da C++ yok — §4 satır 8).

| # | Aşama | Ne olur | Olası hata modu | K003 sahibi |
|---|---|---|---|---|
| A1 | Cihaz seçimi | Çıkış ucu bir cihaza bağlanır | cihaz yok/kayboldu (→ ASIO fallback kuralı T8) | device (Q6) |
| A2 | Akış istemi (activate) | `IAudioClient` benzeri arayüz üzerinden akış hazırlanır | kip desteklenmiyor (exclusive bazı cihazlarda kapalıdır) | driver |
| A3 | Format pazarlığı | Cihaz destekleyeceği format aralığını bildirir | desteklenmeyen örnekleme hızı/kanal sayısı → R10 §10 | driver + output |
| A4 | Buffer boyutu | Ortak buffer (shared) veya exclusive buffer boyutu belirlenir | alt sınırın altı (driver reddi) veya çok büyük (gecikme patlaması) | backend |
| A5 | Olay/uyarı mekanizması | Akışın ne zaman doldurulacağı bildirilir (event tabanlı akış T7 şablonu) | olay kaybı → underrun (§12) | realtime |
| A6 | Başlatma | Akış döner; callback tetiklenir | başlangıç underrun'u (doldurulmadan başlatma) | engine + output |

### §7.3 Gecikme bileşenleri (çıkış yolu — değerler UNKNOWN)

Aşağıdaki tablo yalnızca **bileşen adlarını ve kaynaklarını** listeler;
hiçbir değere sahip değildir (R4 — playback gecikme bütçesi UNKNOWN).
Sayı uydurmak yasaktır.

| # | Bileşen | Kaynağı | Değeri | Ölçüm yeri |
|---|---|---|---|---|
| L1 | Süreç içi tampon (CoreMusic backend buffer) | backend (Q5) | **UNKNOWN** | diagnostics |
| L2 | Driver tamponu (period size) | driver pazarlığı (A4) | **UNKNOWN** | diagnostics |
| L3 | OS/miksaj katmanı (shared kip varsa) | işletim sistemi | **UNKNOWN** | diagnostics |
| L4 | DAC dönüşüm gecikmesi (donanım) | K001-donanım | **UNKNOWN** | ölçüm (K001) |
| L5 | Toplam playback latency = L1+L2+L3+L4 | — | **UNKNOWN** | testing (R4) |

**Kural:** §7.3 tablosuna hiçbir hücreye tahmini sayı yazılmaz; ölçüm
(`diagnostics`) veya karar (`R4`) gelene kadar UNKNOWN kalır.

## §8 Core Audio çıkış şablonu (macOS — ayrıntı kaydı)

Kaynak: master §4 A1–A4 (pull model · AudioUnitRender monoton sampleTime ·
AUGraph yaşam döngüsü) — ✅ doğrulanmış dokümanlar.

| # | Şablon maddesi | K003 çıktısına etkisi | Durum |
|---|---|---|---|
| C1 | Çıkış pasiftir: sistem render çağırır (A1) | output düğümü "şimdi ver" çağrısının hedefidir; kendi kendine göndermez | ✅ doküman kanıtlı |
| C2 | `AudioUnitRender` sonraki sampleTime = önceki + inNumberFrames (A4) | atlanan kareler drift olarak görünür → synchronization'a sinyal | ✅ doküman kanıtlı |
| C3 | Zincir output unit ile biter (A3) | tek graf tek çıkışa biter; çoklu çıkış ayrı yapı kurar (Q6/R3) | ✅ doküman kanıtlı |
| C4 | Default output device değişebilir | kulaklık tak/çık olayında çıkış ucu değişir → yeniden bağlama | ⚠️ davranış CoreMusic'te tanımlanmadı (R3) |
| C5 | Cihaz örnekleme hızı bildirimi | cihaz 44.1↔48 geçişinde çıkış yeniden yapılandırılır → §10 | ⚠️ karar yok |
| C6 | Bellek allocate initialize'da yapılır (A3) | realtime döngüde allocate yok (T9 global kural) ile uyumlu | ✅ doküman + kural |

## §9 ALSA playback çıkış şablonu (Linux — ayrıntı kaydı)

Kaynak: master §5 T13 (hw_params/sw_params + beş access modu) ve T14 (plug
plugin) — ✅ doğrulanmış.

| # | Aşama | Alanlar | Çıkış açısından not |
|---|---|---|---|
| P1 | `hw_params` | format · rate · channels · buffer_size · period_size | Çıkış ucu burada **pazarlık** edilir; red olursa alternatif dene (plug — T14) |
| P2 | access modu seçimi | MMAP_INTERLEAVED · MMAP_NONINTERLEAVED · MMAP_COMPLEX · RW_INTERLEAVED · RW_NONINTERLEAVED | CoreMusic veri düzeni (interleaved mi noninterleaved mi) bu seçime bağlanır — ⚠️ karar yok |
| P3 | `sw_params` | avail_min · period_event | Akış olayının penceresi burada sabitlenir (synchronization-ornekler §2 satır 4) |
| P4 | Yazma döngüsü | period period yazma | Underrun bu döngüde oluşur → §12 |
| P5 | plug plugin (isteğe bağlı) | format/channels/rate slave dönüşümü | Kanal sayısı uyuşmazlığı sürücüde çözülebilir (T14); routing bunu bilmeli (routing-ornekler §2 satır 5) |

**Period/buffer ilişkisi (kavramsal — sayı yok):** buffer_size period_size'ın
kaç katıdır; her periyot bir "iş penceresi"dir. Küçük period → düşük gecikme
ama underrun riski yüksek; büyük period → yüksek gecikme ama dayanıklı.
**Optimum nokta ölçülecektir (R4)**, uydurulmaz.

## §10 Örnek hızı (sample rate) geçişleri

### §10.1 Geçiş senaryoları matrisi

| # | Senaryo | Tetikleyici | Çıkış tepkisi (aday) | Karar durumu |
|---|---|---|---|---|
| S1 | Kaynak 44.1 kHz ↔ cihaz 48 kHz | Dosya oynatma | Kaynağı cihaz hızına yeniden örnekle (resample) | ⚠️ resample **nerede** (input mu output mu) — Q5/Q6 bağlantılı |
| S2 | Aynı hız, kanal sayısı farklı | Stereo dosya → çoklu çıkış | Kanal eşleme/kopyalama (routing) | routing dalı (R1) |
| S3 | Cihaz hızı değişir (kulaklık takıldı) | Donanım olayı | Akışı kapat → yeniden pazarlık → aç (§7.2 A2–A6) | ⚠️ davranış tasarlanmadı (R3) |
| S4 | Aynı anda iki cihaz, farklı hızlar | Çoklu çıkış (hoparlör+kulaklık) | İki ayrı akış; kaynak iki kez yeniden örneklenebilir | ⚠️ R3 — çoklu çıkış kararı yok |
| S5 | Çoklu kaynak, farklı hızlar | Mixing (kardeş dal) | Mixing öncesi hız eşitleme | K003 mixing sınırı — ⚠️ sahiplik (Q5) |

### §10.2 Yeniden örnekleme sahipliği sorusu (açık)

Resampling kimin işi? Üç aday:

| Aday | Sav | Aleyhine | Durum |
|---|---|---|---|
| input (kaynak okuma) | Kaynak okunduğu anda standarda çekilir; zincir tek hızda çalışır | Aynı dosya iki farklı hızlı cihaza giderse iki kez iş | ⚠️ |
| output (teslim) | Her çıkış ucu kendi cihaz hızına uyar; kaynak korunur | Aynı dönüşüm iki uçta tekrarlanır | ⚠️ |
| backend (orta katman) | Tek noktada dönüşüm; zincir ve uçlar sabit kalır | Q5'teki pull-model sahipliğiyle çakışabilir | ⚠️ |

> ⚠️ VERIFICATION REQUIRED: bu üç adaydan **hiçbiri seçilmemiştir**; seçim
> Q5/Q6 kapanınca yapılacaktır. Sayısal örnek hızı hedefi (48 kHz mi,
> 96 kHz mi — sartname "48kHz · 512 buffer" diyor) için: sartname referansı
> `.ai/` vault'undadır, ancak K003'te bağlayıcı karar **ADR olarak
> kaydedilmemiştir** → ⚠️ VERIFICATION REQUIRED.

### §10.3 Geçiş anı davranışı (click-free kuralı — niyet)

| Kural | Açıklama | Bağlantı |
|---|---|---|
| Geçişte kesinti olmaz (niyet) | Yeniden yapılandırma blok kenarında, rampa ile yapılır | realtime (R3 routing) |
| Boşluk doldurma | Geçiş penceresinde veri yoksa sessizlik (silence) üretilir, yinelenen veri değil | backend |
| Eski akış kapatılır | Yeni akış açılmadan eskisi kapatılmaz (önce yeni, sonra eski) | engine yaşam döngüsü |

## §11 Çoklu çıkış senaryoları (R3 — karar yok, şablon var)

| # | Senaryo | Ne gerekir | Bileşenler | Karar durumu |
|---|---|---|---|---|
| D1 | Hoparlör + kulaklık aynı anda | İki ayrı çıkış akışı, aynı kaynak | output ×2 · device ×2 · routing (ayna) | ⚠️ R3 — desteklenip desteklenmediği yok |
| D2 | Yalnız hoparlör | Tek akış | output ×1 | varsayılan aday |
| D3 | Yalnız kulaklık | Tek akış (cihaz değişimi tetikli) | device değişimi + output yeniden bağlama | ⚠️ R3 |
| D4 | Akışa çıkış (ağ) | Ağ tabanlı çıktı ucu | kapsam dışı olabilir (ad-türetme §2 satır 1) | ⚠️ R3 + synchronization R5 |

**Mirror (ayna) vs bağımsız akış farkı (şablon):**

| | Mirror | Bağımsız |
|---|---|---|
| Veri | Aynı tampon iki uca yazılır | Her uç kendi tamponunu besler |
| Gecikme | Uçlar arası fark sürücüye bağlı | Her uç kendi gecikmesini taşır |
| Hız farkı | Farklı hızlı cihazlarda mirror **çalışmaz** (yeniden örnekleme gerekir) | Uç başına resample mümkün |
| K003 sahibi | routing (ayna kuralı) + output (iki uç) | output (iki uç) + mixing (iki akış) |

## §12 Çıkış kesintisi (underrun/xrun) davranış şablonu

§2 satır 5'teki "güvenli davranış niyeti" maddesinin durum şablonu.
Sınıflandırma ve kabul kriteri **diagnostics/testing**tedir (bilgi için);
bu tablo yalnız **davranış niyetini** taşır.

| # | Durum | Tetikleyici | Güvenli davranış (niyet) | Sahip |
|---|---|---|---|---|
| U1 | Önsezi (predictive) | Zaman yaklaşıyor ama tampon dolu | Devam — ek işlem yok | realtime |
| U2 | Underrun eşiği | Tampon boşalıyor, veri gelmedi | Son geçerli veriyi **tutma** (hold) yerine sessizlik (silence) doldur | output + backend |
| U3 | Underrun gerçekleşti | Sürücü boş tampon okudu | Sessizlik yaz; sayacı artır (diagnostics) | output |
| U4 | Tekrarlı underrun (kurtulma) | N periyot üst üste | Durum raporu üret; **asla eski veriyi döngüye geri sarma** (loop-back yasak) | diagnostics |
| U5 | Kalıcı kesinti (device loss) | Cihaz kayboldu | Akışı kapat; fallback kuralı (T8: ASIO→WASAPI) uygulanır | device + driver |

**Yasak (şablon):** underrun'da **eski tampon tekrar oynatılmaz** (click ve
desync üretir); **tasanlanan ses (hold-last-sample) yalnızca kararlı
tasarımda** serbesttir — CoreMusic'te hangisinin geçerli olduğu **UNKNOWN**
(R6). Değer/parametre uydurulmaz.

## §13 Ölçüm ve kapanış kapıları (açık)

| Kapı | Koşul | Şu an |
|---|---|---|
| O-G1 | İlk platform + kip kararı (ADR) | ❌ açık (R5) |
| O-G2 | Playback gecikme bütçesi (ms) ölçümü | ❌ UNKNOWN (R4) |
| O-G3 | Underrun davranış kararı (silence vs hold) | ❌ açık (R6) |
| O-G4 | Örnek hızı geçiş sahipliği kararı (§10.2) | ❌ açık (Q5/Q6) |
| O-G5 | Çoklu çıkış destek kararı | ❌ açık (R3) |

**Kapanış kuralı:** bu kapıların **hiçbiri bu dosyada açılmaz**; ölçüm
`diagnostics`/`testing` dalında, karar ADR'de yapılır. §6–§12 şablonları
karar verilince `[KANIT]` etiketi alır — o zamana kadar taslak şablon
statüsündedir.

## §14 Format matrisleri (bit derinliği × kanal × hız)

§10.1'deki geçiş senaryolarının **girdi matrisi**: hangi format kombinasyonu
desteklenmeli, hangi kombinasyon dönüşüm gerektirir. Hücreler **kural**
değil, **destek matrisi taslağı**dır — hangi hücrenin zorunlu olduğu R5'te
kararlaştırılacaktır.

### §14.1 Bit derinliği matrisi

| Bit derinliği \ Durum | Çıkış ucu (WASAPI shared) | Çıkış ucu (WASAPI exclusive) | ALSA hw | Core Audio |
|---|---|---|---|---|
| 16-bit PCM | destek (OS çevirir) | cihaza bağlı | destek | destek |
| 24-bit PCM | destek (OS karıştırır) | cihaza bağlı | destek | destek |
| 32-bit float | destek (OS mix formatı olabilir) | cihaza bağlı | destek (format kaydı) | destek |
| 32-bit PCM | destek (OS çevirir) | cihaza bağlı | destek | destek |

### §14.2 Kanal sayısı matrisi

| Kanal \ Durum | Stereo çıkış | Çoklu çıkış | Not |
|---|---|---|---|
| 1 (mono) | tek kanal → iki uç kopyalanır | — | routing §6.1 |
| 2 (stereo) | doğrudan | — | varsayılan aday |
| 6 (5.1) | — | ayrı yapı gerekir | master §4 A3: tek graf tek çıkışa |
| 8 (7.1) | — | ayrı yapı gerekir | ⚠️ CoreMusic kararı yok (R3) |

### §14.3 Örnekleme hızı matrisi

| Hız \ Durum | Destek (nihai) | Dönüşüm gerekir | Not |
|---|---|---|---|
| 44.1 kHz | evet (dosya kaynağı) | cihaz 48 kHz ise evet | S1 senaryosu (§10.1) |
| 48 kHz | evet (sartname hedefi — ⚠️ karar yok) | kaynak 44.1 ise evet | sartname "48kHz" — VERIFICATION REQUIRED |
| 88.2 / 96 kHz | bilinmiyor | — | **UNKNOWN** (karar yok) |
| 176.4 / 192 kHz | bilinmiyor | — | **UNKNOWN** (karar yok) |

**Matris kuralı:** destek matrisi **boş bırakılamaz**; "destek" hücresi
ölçümle (`[KANIT]`), "bilinmiyor" hücresi UNKNOWN ile işaretlenir. §14.3'teki
sartname hedefi **bir iddia değil, bir hedeftir** — sürücü pazarlığı
(output-kapsam §9 P1) onu değiştirebilir.

## §15 WASAPI kip davranışları — derinlemesine karşılaştırma

§7.1'deki özetin **davranış derinliği**: her satır bir **davranış**
taşır, API adı taşımaz (uydurma yasağı).

| # | Davranış | Shared kip | Exclusive kip | Test edilebilir mi? |
|---|---|---|---|---|
| 1 | Diğer uygulamaların sesi | karışır (OS miksajı) | duyulmaz (tek uygulama) | ✅ evet — iki uygulama testi |
| 2 | Cihaz format dayatması | OS dayatır | uygulama dayatır (cihaz onaylar) | ✅ pazarlık sonucu okunur |
| 3 | Gecikme bileşeni | OS katmanı ekler (L3) | L3 yok (doğrudan) | ✅ ölçüm (diagnostics) |
| 4 | Exclusive reddi | — | cihaz desteklemeyebilir (§7.2 A2) | ✅ red kodu okunur |
| 5 | Default device değişimi | otomatik akış | yeniden bağlama | ⚠️ davranış tasarlanmadı (R3) |
| 6 | Uygulama askıya alma | OS devralır | cihaz boşalır → underrun | ⚠️ davranış tasarlanmadı |
| 7 | Eşzamanlı iki uygulama | ikisi de çalışır | ikincisi reddedilir (kural #9) | ✅ lock testi |

**Derin ders:** exclusive kip **daha az katman** taşır (L3 yok → daha düşük
gecikme) ama **daha kırılgandır** (reddedilebilir, cihaz değişimi zorlaşır).
R5 kararı yalnız "hangisi daha hızlı" değil, "hangisi daha **dayanıklı**"
sorusunu da içerir.

### §15.1 Exclusive reddi senaryoları (güvenli davranış şablonu)

| # | Red nedeni | Davranış (niyet) | Sahip |
|---|---|---|---|
| 1 | Cihaz exclusive desteklemiyor | shared'e düş (fallback) | output + device |
| 2 | Başka uygulama exclusive kullanıyor | shared'e düş + kullanıcıya bildir | output + diagnostics |
| 3 | Format reddedildi | Format pazarlığını tekrar dene; olmazsa shared | driver + output |
| 4 | Cihaz aniden kayboldu | Akışı kapat; T8 fallback kuralı | device (T8) |

## §16 Örnek hızı geçiş — davranış derinlemesi

§10'daki senaryoların **davranış tablosu**. Her satır "geçiş anında ne
olur"u tanımlar; süre/ölçü **UNKNOWN** (R4).

| # | Geçiş anı | Beklenen | Risk | Önleme (niyet) |
|---|---|---|---|---|
| 1 | Kaynak hızı değişti (dosya) | Yeni hız okunur; dönüşüm uygulanır | Click (anlık kesinti) | Rampa (§10.3) |
| 2 | Cihaz hızı değişti (donanım olayı) | Akış kapat → yeniden pazarlık → aç | Uzun boşluk (desync) | Sessizlik + rapor (§12 U5) |
| 3 | İki uç farklı hızda (çoklu çıkış) | Uç başına dönüşüm | İki kez iş (çift maliyet) | §10.2 sahiplik kararı |
| 4 | Aynı hız, farklı periyot | Periyot boyutu değişir | Bütçe aşımı | Bütçe yeniden pazarlığı (§7.2 A4) |
| 5 | Geçiş sırasında underrun | Sessizlik + sayaç | Bozulma (loop-back) | §12 U4 yasağı |

**Geçiş kuralı (niyet):** geçiş **blok kenarında** ve **tek seferde**
yapılır; yarım geçiş (eski hızla yazma + yeni hızla okuma) **yasaktır**
(kesin desync üretir). Bu kural routing R3 (blok kenarı) ile aynı karardır.

## §17 Çoklu çıkış — senaryo derinlemesi

§11'deki dört senaryonun **davranış tablosu**. R3 kararı yok; bu tablo
yalnız **senaryo betimlerini** taşır.

| # | Senaryo | Veri akışı | Gecikme farkı | Sahip |
|---|---|---|---|---|
| 1 | Hoparlör + kulaklık (aynı hız) | Ayna (mirror) | Sürücüye bağlı (UNKNOWN) | routing + output |
| 2 | Hoparlör + kulaklık (farklı hız) | İki akış + dönüşüm | İki ayrı bütçe | output + synchronization |
| 3 | Yalnız hoparlör (kulaklık takıldı) | Yeni uç devreye girer | Cihaz değişim bedeli | device + output |
| 4 | Ağ çıkışı (stream) | Ağ jitter'ı ekler | Jitter buffer gerekir | synchronization §7 |
| 5 | Kayıt (loopback) | Çıkış → geri besleme | Ek gecikme | K003 kapsamı **dışında** olabilir (R3) |

**Senaryo dersi:** senaryo 2, §10.2'deki "çift dönüşüm" maliyetini üretir;
senaryo 5 ise **kapsam kararı** gerektirir (kayıt K003'te midir, ayrı bir
dalda mı — bu dosyada çözülmez, işaretlenir).

## §18 Xrun sınıflandırma tablosu (diagnostics komşusu — şablon)

§12'deki durum tablosunun **sınıflandırma uzantısı**. Bu tablo yalnız
**sınıf adlarını** taşır; eşik/ölçü testing dalındadır (TS1).

| # | Sınıf | Tanım | Belirti | Sahip |
|---|---|---|---|---|
| 1 | Predictive underrun | Tampon azalıyor, veri henüz gelmedi | Sayaç uyarısı | realtime |
| 2 | Hard underrun | Sürücü boş tampon okudu | Ses kesilmesi | output |
| 3 | Overrun (input tarafı) | Giriş tamponu taştı | Veri kaybı | input (kardeş dal) |
| 4 | Period slip | Periyot kenarı kaçtı | Zamanlama sapması | synchronization |
| 5 | Clock drift | Uzun vadeli kayma | Desync | synchronization §9 |
| 6 | Device loss | Cihaz kayboldu | Akış koptu | device (T8 fallback) |
| 7 | Priority inversion | RT bekledi | Periyot aşımı | realtime §9 |

**Sınıflandırma dersi:** her sınıfın **farklı bir sahibi** vardır; bu yüzden
tek bir "xrun" sayacı **yetersizdir** — sınıflandırma diagnostics'te
(yeni dal), karar testing'te (R1) yapılır.

## §19 Çıkış veri düzeni (interleaved / noninterleaved)

§9 P2'deki access modu seçiminin **veri düzeni etkisi**: ALSA beş access
modu (T13) doğrudan veri düzenini belirler; WASAPI/Core Audio tarafında
düzen sürücü/API ile anlaşılır. Bu tablo yalnız **kavramsal düzen** taşır.

| Düzen | Tanım | Avantaj | Dezavantaj | K003 etkisi |
|---|---|---|---|---|
| Interleaved | Kanallar ardışık (L R L R) | Tek tampon, basit yazma | Kanal başına iş bölünür | Varsayılan aday (⚠️ karar yok) |
| Noninterleaved | Kanal başına ayrı tampon | Kanal bazlı işlem kolay | Çok tampon, cache baskısı | ⚠️ karar yok |
| MMAP (ALSA) | Bellek paylaşımı | Kopya yok | Karmaşık eşleme | ⚠️ erişim kararı (P2) |

**Düzen kuralı:** düzen **motorun iç sözleşmesidir**; DSP (K004) hangi
düzeni beklediğini sözleşmede **açık** tanımlar (master §4 A5 — Process
sözleşmesi). Düzen değişimi bir **breaking change**tir (K004 sınırı).

## §20 Ölçüm protokolleri (çıkış yolu — şablon)

§13'teki kapıların **nasıl** kapatılacağının protokol şablonu. Ölçüm
`diagnostics` dalında, karar testing dalında (TS1).

| # | Protokol | Girdi | Çıktı | Kapı |
|---|---|---|---|---|
| 1 | Playback latency ölçümü | Referans sinyal + loopback | Toplam ms (L1+L2+L3+L4) | O-G2 |
| 2 | Underrun sayımı | Zorla besleme kesintisi | Sınıf + sayaç (§18) | O-G3 |
| 3 | Geçiş sürekliliği | Hız değişimi senaryosu | Damga sürekliliği (§5 testing-ornekler) | O-G4 |
| 4 | Çoklu çıkış doğrulama | İki uç testi | Ayna + gecikme farkı | O-G5 |
| 5 | Kip karşılaştırma | Shared vs exclusive | İki ölçüm seti | O-G1 |

**Protokol kuralı:** her protokol **bir kapıya** bağlanır; kapılar bu
dosyada **açık** kalır (§13). Ölçüm değerleri bu dosyaya **yazılmaz** —
`diagnostics` dosyasına düşer, testing orada karar verir.

## §21 Karar kaydı (decision log şablonu)

§13 ve §10.2'deki açık kararların **izlenebilirliği** için şablon. Bu tablo
**boş**dur — kararlar verilince doldurulacaktır (uydurma yasağı).

| # | Karar | Tarih | Sahip | Sonuç |
|---|---|---|---|---|
| D1 | İlk platform + kip (R5) | — | — | **beklemede** |
| D2 | Playback gecikme bütçesi (R4) | — | — | **beklemede** |
| D3 | Underrun davranışı (R6) | — | — | **beklemede** |
| D4 | Örnek hızı geçiş sahipliği (§10.2) | — | — | **beklemede** |
| D5 | Çoklu çıkış (R3) | — | — | **beklemede** |
| D6 | Veri düzeni (§19) | — | — | **beklemede** |

## §22 Çıkış hata kodu şablonu (diagnostics yüzeyi)

§18 sınıflandırmasının **kod yüzeyi**: her sınıf bir hata kodu ailesi alır.
Kodlar **taslaktır** (UYARI: gerçek kod şeması tanımlanmamıştır — bu tablo
yalnız **yapı**yı gösterir).

| # | Aile | Kapsam | Örnek alt kod | Sınıf (§18) |
|---|---|---|---|---|
| 1 | OUTF | Çıkış hattı geneli | OUTF-000 | — |
| 2 | OUTF-1xx | Kip/pazarlık | OUTF-101 red · OUTF-102 format | 6 (device loss) |
| 3 | OUTF-2xx | Buffer/tampon | OUTF-201 boş | 1 · 2 (underrun) |
| 4 | OUTF-3xx | Geçiş/hız | OUTF-301 hız değişimi | 4 · 5 (drift) |
| 5 | OUTF-4xx | Çoklu çıkış | OUTF-401 uç uyuşmazlığı | 3 (period slip) |
| 6 | OUTF-5xx | Ölçüm/kapı | OUTF-501 ölçülmedi | INCONCLUSIVE (testing §8) |

**Kod kuralı:** kod **kullanılmadan önce** diagnostics dalında kaydedilir;
bu tablo yalnız **kategori iskeletidir** (uydurma kod yazılmaz — gerçek
kod şeması tanımlanmamıştır).

## §23 Sık karşılaşılan tasarım soruları (FAQ — cevapsız)

§5'teki R1–R6 sorularının **tasarım sırasında** çıkacak yüzeyi. Bu bölüm
**cevap üretmez**, soruları **genişletir** (zero-hallucination).

| # | Soru | Neden sık | Bağlantı |
|---|---|---|---|
| 1 | Exclusive kip varsayılan mı olsun? | Hız + paylaşımsızlık avantajı | R5 · §15 |
| 2 | Kulaklık takılınca otomatik geçiş? | Kullanıcı deneyimi | R3 · §17 |
| 3 | Ağ çıkışı K003'te mi? | Kapsam sınırı | §17 satır 5 |
| 4 | Resample hangi katmanda? | Çift dönüşüm maliyeti | §10.2 |
| 5 | Underrun'da hold mı silence mı? | Click riski vs sessizlik | R6 · §12 |
| 6 | 8.1 topolojisi onaylı mı? | Domain hedefi var, karar yok | §8 satır 3 |
| 7 | Loopback/kayıt kapsam mı? | Yeni dal gerekebilir | §17 satır 5 |

**FAQ kuralı:** bu sorular **kapatılmadan** output dalı `verified` olamaz
(§13 O-G kapıları).

## §24 Komşu dallarla sınır özeti (cross-reference tablosu)

§2'deki sınır tablosunun **karar bazlı** hâli: her satır bir sınır kararını
**tek satırda** özetler.

| Sınır | Karar | Sahibi | Kapı |
|---|---|---|---|
| output ↔ device | Cihaz seçimi kimde? | Q6 (master) | O-G1 · BK-2 |
| output ↔ driver | Yazma yolu kimde? | Q6 (master) | O-G1 · BK-2 |
| output ↔ engine | Son düğüm sahipliği | Q5 (master) | O-G4 · BK-1 |
| output ↔ backend | Tampon sahipliği | Q5 (master) | O-G2 · BK-1 |
| output ↔ routing | Çoklu uç eşlemesi | R1 (routing) | O-G5 · RO-G1 |
| output ↔ synchronization | Geçiş/rapor | R3 (routing) | O-G4 · SY-G2 |
| output ↔ diagnostics | Xrun raporu | TS1 (testing) | O-G3 · TS-G1 |

**Özet ders:** output dalının **her sınırı bir üst soruya** (Q5/Q6) bağlıdır;
output **kendi başına** hiçbir sınır kararını kapatamaz.

## §25 Ölçüm/kanıt özet tablosu (dosya durumu)

Bu dosyanın **kanıt durumunun** özeti: hangi bölüm kanıta, hangisi niyete
dayanır.

| Bölüm | Dayanak | Kanıt durumu |
|---|---|---|
| §1–§5 (mevcut) | v2 §2.3 ad-türetme + disk kanıtları | ✅ dosya okundu · ⚠️/UNKNOWN işaretli |
| §6–§9 (şablon) | master §5 T6/T7/T13/T14 · §4 A1–A4 | ✅ doküman çerçevesi — CoreMusic kararı **yok** |
| §10–§12 (niyet) | Q5/Q6/R1–R6 ile kilitli | ⚠️ karar yok → UNKNOWN |
| §13 (kapılar) | — | ❌ hepsi açık |
| §14–§21 (şablon/protokol) | §6–§13 üzerine kurulu | ⚠️ şablon — `[KANIT]` almaz |
| §22–§25 (yüzey/FAQ/özet) | — | ⚠️ yapı iskeleti — kod şeması yok |

**Kapanış:** bu dosya **kanıt oranını değiştirmez**; yalnız mevcut kanıtı
**işler ve yapılandırır**. `status: draft` kalır; ilk `[KANIT]` geçişi
yalnız **kod + ölçüm** ile olur (repo'da C++ yok — master §7 D6).

## §26 Çıkış cihazı türleri — ayrıntılı davranış matrisi

§6 eşleme matrisinin **cihaz türü** boyutu: hangi tür cihaz hangi davranışları
zorunlu kılar. Karar yok; yalnız **davranış öngörüsü** var.

| # | Cihaz türü | Tipik gecikme eğilimi | Kip tercih eğilimi | Özel davranış |
|---|---|---|---|---|
| 1 | USB DAC | Orta (USB toplama) | Exclusive mümkün | Cihaz kaybı sık (T8 fallback tetikleyicisi) |
| 2 | PCIe/entegre ses kartı | Düşük | Exclusive/ASIO mümkün | Donanım clock (synchronization R1 adayı) |
| 3 | Bluetooth kulaklık | **Yüksek** (kodek + tampon) | Shared (OS kodeği) | Kodek gecikmesi ölçülebilir değil → UNKNOWN |
| 4 | HDMI/DisplayPort çıkışı | Yüksek (ekran senkronu) | Shared | Ekran kapanınca cihaz kaybolabilir |
| 5 | Ağ/stream çıkışı | Jitter'lı | OS yönetir | Jitter buffer gerekir (synchronization §7) |
| 6 | Dahili hoparlör | Orta | Shared | Ölçüm zor (mikrofon geri besleme gerekir) |

**Matris dersi:** cihaz türü **kip ve gecikme öngörüsü** üretir; bu yüzden
device seçimi (Q6) yalnız "hangi cihaz" değil, "hangi tür davranış" sorusudur.
Bluetooth satırı **özellikle dikkat** gerektirir: yüksek gecikme + OS
kodeği, RT bütçesini (realtime §7) doğrudan etkiler.

## §27 Çıkış — kip kararı ağaç şablonu (R5 kararı öncesi)

R5 kapanmadan önce **karar ağacının** kurulması: hangi durumda hangi kipe
düşülür. Ağaç **niyet**tir; eşik yok, sayı yok.

| # | Düğüm (soru) | Evet | Hayır |
|---|---|---|---|
| 1 | Exclusive cihazda destekleniyor mu? | 2'ye git | Shared (4'e git) |
| 2 | Başka uygulama exclusive istiyor mu? | Shared (4'e git) | Exclusive dene (3'e git) |
| 3 | Exclusive pazarlığı başarılı mı? | Exclusive kullan | Shared (4'e git) |
| 4 | Shared'de OS mix formatı uygun mu? | Shared kullan | Format pazarlığı → sonra Shared |
| 5 | Cihaz kaybı olursa | T8 fallback (WASAPI'ye) | — |

**Ağaç dersi:** bu ağaç **her zaman bir güvenli sonuca** varır (shared);
exclusive **yalnızca üç koşul birden** sağlanınca seçilir. Bu, "önce
dayanıklılık, sonra hız" ilkesinin ağaçtaki hâlidir.

## §28 Çıkış — veri boyutu ve bellek şablonu (backend bağlantılı)

§19'daki veri düzeninin **bellek** boyutu: bir tick'te kaç bayt taşınır.
Sayılar **formülle** verilir; somut değer **UNKNOWN** (bütçe yok — R4).

| # | Bileşen | Formül (kavramsal) | Değeri |
|---|---|---|---|
| 1 | Kanal başına bayt | bit_derinliği / 8 | UNKNOWN (§14.1 kararı) |
| 2 | Tick başına kanal | kanal sayısı (§14.2 kararı) | UNKNOWN |
| 3 | Tick başına toplam bayt | (1) × (2) × periyot boyutu | UNKNOWN |
| 4 | Tampon derinliği | tick başına × N tick (§7.1 J1) | UNKNOWN |
| 5 | Çoklu çıkış toplamı | (3) × uç sayısı (§17) | UNKNOWN |

**Bellek kuralı:** bu boyutlar **baştan allocate** edilir (realtime K1);
çalışma anında genişleme yok. Boyut bilinmediği için bu dosyada bellek
iddiası **yazılmaz** → UNKNOWN.

## §29 Çıkış — güvenlik ve koruma (DC offset, kural #10)

Kural #10 (`.ai/AGENTS.md` — DC Offset >0.5V → koruma rölesi, donanım
kuralıdır) bu dalın **ölçüm yüzeyiyle** kesişir: yazılım tarafı DC'yi
**ölçebilir**, röleyi **tetikleyemez** (donanım işi).

| # | Yüzey | Sahibi | Yazılım rolü |
|---|---|---|---|
| 1 | DC offset ölçümü | output + diagnostics | Sessizlik/DC sinyali oku, ortalama |
| 2 | Eşik (0.5V) | Donanım (K001) | Eşik donanımdadır; yazılım yalnız raporlar |
| 3 | Röle tetikleme | K001/K005 (donanım) | Yazılım **dokunmaz** |
| 4 | Yazılım tasma (clip) | output | 0 dBFS üstünü kes (niyet — karar yok) |

**Güvenlik dersi:** yazılım **ölçer ve raporlar**, donanım **korur**; bu
sınır aşılmaz (donanım katmanına kod yazmak K001'e ihlaldir).

## §30 Çıkış — test ortamı gereksinimleri (testing dalına devir)

§14'teki senaryoların **ortam** gereksinimi: hangi ortamda test edilir.

| # | Ortam | Gereken | Senaryolar |
|---|---|---|---|
| 1 | CI (cihaz yok) | Mock sürücü + döngü testi | 3 (hız geçişi dosya) · 5 (zorla underrun) |
| 2 | Laboratuvar (gerçek cihaz) | USB DAC + mikrofon | 1 · 2 · 4 · 6 · 7 · 8 |
| 3 | Kullanıcı ortamı | Gerçek sistem | 4 (donanım olayı) · 6 (device loss) |
| 4 | Ölçüm (SNR/THD) | Kalibre mikrofon + oda | testing-kapsam §7 protokolü |

**Ortam dersi:** CI **yalnız L1/L2**'yi kapsar (testing-kapsam §6); cihaz
testleri **laboratuvar** akışındadır ve bu dosyanın kapılarını **kapatabilir**
(ölçüm oradan gelir).

## §31 Çıkış — sık yapılan hatalar (tasarım uyarısı)

§27–§30'un **tersi** hâli: hangi hata **sık** yapılır ve **nasıl** önlenir.
Uyarı yüzeyidir; ölçüm/değer yok.

| # | Hata | Sonuç | Önlem |
|---|---|---|---|
| 1 | Exclusive'i varsayılan yapmak | Cihaz reddinde sessizlik | §27 ağacı — önce dayanıklılık |
| 2 | Buffer'ı çalışma anında büyütmek | RT yasağı ihlali (K1) | §28 — baştan allocate |
| 3 | Geçişte rampa yok | Click | §16 — blok kenarı + rampa |
| 4 | Underrun'da loop-back | Bozulma + desync | §12 U4 yasağı |
| 5 | Tek "xrun" sayacı | Sınıf gizlenir | §18 — sınıflandırma |
| 6 | Ölçümı CI'a taşımak | CI başarısız (cihaz yok) | §30 — ortam ayrımı |
| 7 | DC eşiğini yazılımda uygulamak | Donanım koruması atlanır | §29 — sınır |

## §32 Çıkış — kapıyı kapatan kanıt listesi (hangi kapı hangi kanıtla kapanır)

§13 kapılarının **kanıt karşılıkları**: her kapı yalnız aşağıdaki kanıtla
kapanır; başka kanıt **kabul edilmez**.

| Kapı | Gerekli kanıt | Kanıt nereden gelir | Kabul edilmeyen |
|---|---|---|---|
| O-G1 | İlk platform + kip kararı (ADR) | `.ai/.decisions/` | Dosyada yazılmış tercih |
| O-G2 | Ölçülmüş toplam playback latency (L1+L2+L3+L4) | diagnostics ölçümü | Tahmini süre, doküman değeri |
| O-G3 | Underrun davranışı kararı + test sonucu | testing kararı + ölçüm | Niyet cümlesi |
| O-G4 | Geçiş sahipliği kararı + süreklilik testi | ADR + testing (§8 assertion) | Şablon tablo |
| O-G5 | Çoklu çıkış kararı + ayna doğrulaması | ADR + laboratuvar ölçümü | Senaryo betimi |

**Kapanış dersi:** beş kapının **hiçbiri** bu dosyadaki şablonlarla
kapatılamaz; kanıt **dışarıdan** gelir (ADR + diagnostics + testing).
Bu dosya yalnız **kapıların yerini ve şeklini** tanımlar — kapanışı
**bekler**.

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
