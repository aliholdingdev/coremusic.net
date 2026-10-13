---
title: "realtime — Kapsam"
type: architecture-scope
node_id: K003-audio/realtime
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# realtime — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 9 bu dalı **"Zaman baskısı ve lock-free çalışma
kuralları"** olarak yazar. Basitçe: ses döngüsünün **gecikme tahammülü olmayan**
disiplini — her tik zamanında bitecek; kilit (lock), bellek ayırma, log yazma
gibi "biraz bekleyebilir" işler burada **yasaktır**. Bir fabrika bandı gibi:
bant durursa ürün çöpe gider; bant hiç durmaz, duracak işler vardiyaya
bırakılır. Global standart: "RT audio callback lock-free, buffer'lar baştan
allocate" (`~/.config/opencode/AGENTS.md` §5 — master §5 T9).

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (v2 §3 KURAL 3); içerik iddiası değildir. Öncelik, kuyruk stratejisi ve
> bütçe değerleri kanıt turunda netleşecektir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Zaman baskısı altında çalışma **kuralları** (lock-free, ön-atama, yasak listesi) | Bu kuralları uygulayan ana döngü | engine (master §3 satır 1) |
| 2 | Realtime thread'in öncelik/süreç politikası tasarımı (ne zaman, hangi öncelik) | Somut öncelik değeri (ölçüm/karar yok → UNKNOWN) | K000-isletim-sistemi (master §6) |
| 3 | Callback içinde yasak işlerin listesi (malloc · lock · dosya/ağ I/O · log) | Yasak ihlalinin tespiti ve raporu | diagnostics (master §3 satır 11) |
| 4 | Realtime yolundaki buffer'ların baştan allocate edilmesi (taşma yok) | Buffer/kuyruk altyapısının kendisi | backend (master §3 satır 4) |
| 5 | Deadline aşımının (tik'in geçmesi) güvenli davranışının niyeti | Ölçüm, sınıflandırma, kabul kriteri | diagnostics · testing |
| 6 | *(dışında)* clock/epoch uyumu ve drift telafisi | Zaman referansının senkronizasyonu | synchronization (master §3 satır 10) |
| 7 | *(dışında)* DSP hesabının kendisi ve gerçek zamanlı DSP kodu | Algoritma | K004-dsp (master §2.2) |
| 8 | *(dışında)* sürücü/OS realtime mekanizması (thread önceliği, ASIO callback) | Platform mekanizması | driver · K000 (master §2.2) |

**Yasak:** satır 1'deki ayrım (kural ↔ uygulama) v2 §2.3'te engine ile realtime
**ayrı çocuklar** olduğu için bağlayıcıdır; kural ve döngü tek dosyada
birleştirilemez (KURAL 1).

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| RT1 | realtime ↔ engine | "hangi kuralla çalıştırılır" ↔ "ne çalıştırılır" | ✅ master §3 satır 1/9 (ayrı çocuklar) |
| RT2 | realtime ↔ synchronization | döngünün disiplini ↔ zaman referansının uyumu | ✅ master §3 satır 9/10 (ayrı çocuklar) |
| RT3 | realtime ↔ backend | kural (lock-free, ön-atama) ↔ altyapı (kuyruk) | ⚠️ Q5 ile kilitli — VERIFICATION REQUIRED |
| RT4 | realtime ↔ diagnostics | kural ihlalinin üretilmesi ↔ tespiti | ⚠️ üst karar bekliyor |
| RT5 | realtime ↔ K004-dsp | döngü disiplini ↔ hesabın kendisi | ✅ master §2.2 sınır kuralı |
| RT6 | realtime ↔ driver | thread disiplini ↔ platform callback mekanizması | ⚠️ ad-türetme (master §3 satır 9 ve 3) |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "realtime · Zaman baskısı ve lock-free çalışma kuralları · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 70 | ✅ dosya okundu |
| 2 | "RT audio callback lock-free, buffer'lar baştan allocate" — global mühendislik kuralı | `~/.config/opencode/AGENTS.md` §5 (Audio & Electronics) · `audio-master.md` §5 T9 | ✅ dosya okundu |
| 3 | `AudioUnitRender` sample-time monotonluğu (sonraki = önceki + inNumberFrames) — deadline referansı | `audio-master.md` §4 A4 (Apple: AudioUnitRender) | ✅ dosya okundu |
| 4 | PipeWire scheduling: driver node döngüyü başlatır; async node 1 quantum latency ekler | `audio-master.md` §5 T15 → docs.pipewire.org page_scheduling | ✅ dosya okundu (URL fetch) |
| 5 | Embedded kalite hedefi: zero-allocation, lock-free, noexcept (%100) | `.ai/AGENTS.md` §16 Embedded satırı | ✅ dosya okundu |
| 6 | Repo'da realtime callback/kuyruk kodu yok | kök `*.cpp`/`*.h` glob 0 isabet (2026-10-12) | ✅ shell ölçümü |
| 7 | Somut thread önceliği/süreç değeri (ör. yüksek öncelik sayısı) | — | **UNKNOWN** (ölçüm/karar yok) |
| 8 | Tick/buffer bütçesi (ms veya örnek sayısı) | — | **UNKNOWN** (bütçe tanımı yok) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | Realtime thread önceliği/süreci nedir (somut değer)? | **UNKNOWN** — kanıt yok | embedded-engineer |
| R2 | Tick bütçesi kaç örnek/ms ve aşımında davranış nedir? | **UNKNOWN** — bütçe yok | performance-engineer |
| R3 | Callback'te yasak işler hangi vardiyaya (kuyruğa) devredilir? | ⚠️ VERIFICATION REQUIRED | backend |
| R4 | realtime ↔ engine sınırında döngü kimin (Q5) | ⚠️ VERIFICATION REQUIRED (master Q5) | Vault Steward |
| R5 | ASIO/WASAPI callback'i ile CoreMusic döngüsü nasıl eşleşir? | ⚠️ VERIFICATION REQUIRED | driver |
| R6 | Yasak ihlali nasıl tespit edilir (debug-only kontrol)? | ⚠️ VERIFICATION REQUIRED | diagnostics |

**Kapanış:** status: draft kalır. R1/R2 kapanmadan öncelik veya bütçe sayısı
yazılmaz → UNKNOWN; "lock-free" iddiası ancak kod kanıtıyla `[KANIT]` olur
(repo'da C++ kodu yok — master §7 D6).

## §6 Gerçek zamanlı kısıtlar kataloğu (şablon — değer yok)

§2 satır 1'deki "zaman baskısı altında çalışma kuralları" maddesinin ayrıntı
kataloğu. Her satır bir **kısıt**tır; "ihlal belirtisi" sütunu tespit
yüzeyini, "alternatif" sütunu ise yasağın yerine konacak **kural**nı gösterir.
Değer/eşikler **UNKNOWN**'dur (R2).

| # | Kısıt | Neden (RT disiplini) | İhlal belirtisi | Alternatif (izin verilen yol) |
|---|---|---|---|---|
| K1 | Bellek ayırma (malloc/new) yasak | Tahminsiz gecikme + fragmentasyon | Heap ölçüm artışı, ilk çağrıda pik | Baştan allocate (T9 global kural) + sabit havuz |
| K2 | Kilitleme (mutex/lock) yasak | Bekleme belirsiz; priority inversion üretir | Deadlock, öncelik düşüşü | Lock-free atomik + SPSC kuyruk |
| K3 | Dosya/ağ I/O yasak | Disk/ağ gecikmesi ms'lerce sürer | Callback süresi pikleri | Non-RT vardiyaya devir (§8) |
| K4 | Log yazma yasak (dosyaya) | I/O + formatlama gecikmesi | Callback süresi artışı | Halka tampon (ring) + non-RT okuma |
| K5 | Exception yayılımı yasak | RT yolunda stack unwind belirsiz | noexcept ihlali derleme hatası | Döndürme kodu / durum bayrağı |
| K6 | Süreç içi senkron beklemek (sleep/wait) yasak | Deadline garanti edilmez | Callback gecikmesi | Beklemek yok; veri yoksa sessizlik |
| K7 | Koşullu dal (branch) mümkün olduğunca deterministik | Tahmin edilebilirlik | Dal maliyeti değişkenliği | Durum önceden hesapla, döngüde sabit yol |
| K8 | Görsel/UI çağrısı yasak | UI mesaj döngüsü RT değildir | Callback'te pencere mesajı | UI yalnız non-RT taraf |

**Kural:** bu katalog **niyet kataloğu**dur; somut uygulama kod kanıtıyla
`[KANIT]` olur. "K3 uygulandı" iddiası ölçüm/kod olmadan yazılmaz.

## §7 Latans bütçesi şablonu (bileşenler — değerler UNKNOWN)

§2 satır 2'deki "öncelik/süreç politikası" ile ilgili **bütçe iskeleti**.
Bütçe **tahsis edilmemiştir**; tablo yalnız bileşen adlarını ve sahiplerini
taşır. R2 kapanınca sayısal sütun doldurulacaktır.

| # | Bileşen | Sahibi | Bütçeye etkisi | Değeri |
|---|---|---|---|---|
| B1 | Callback giriş → ilk DSP işi | realtime | İşleme başlangıcı | **UNKNOWN** |
| B2 | DSP zinciri işleme süresi | K004-dsp | Toplam iş | **UNKNOWN** |
| B3 | Routing/mixing toplamı | routing · mixing | Toplam iş | **UNKNOWN** |
| B4 | Kuyruk yazma (RT→non-RT) | backend | Çıkış kuyruğu | **UNKNOWN** |
| B5 | Toplam tick bütçesi (periyot süresi) | realtime + driver | Sınır: toplam ≤ tick | **UNKNOWN** |
| B6 | Güvenlik payı (safety margin, %?) | realtime | Bütçe − iş ≥ pay | **UNKNOWN** |

**Bütçe formülü (kavramsal — sayı yok):** tick_süresi ≥ (B1+B2+B3+B4) +
güvenlik_payı. Tick süresi periyot boyutuna bağlıdır (ALSA/ASIO'da açılışta
pazarlık — output-ornekler §7). **Optimum periyot boyutu ölçülecektir**;
uydurulmaz.

## §8 Non-RT ↔ RT sınırı (vardiya şablonu — Q5 ile kilitli)

§2 satır 1'deki ayrımın **sınır mimarisi**. RT tarafı yalnız **hazır veriyi
işler**; üretimi, kararı, logu non-RT'ye devreder. Sınır iki yönlüdür ama
**asimetrik**dır: RT→non-RT kuyruk (veri taşır), non-RT→RT atomik bayrak
(komut taşır).

### §8.1 Sınırın yönleri

| # | Yön | Taşıyan | Bekleyen sonuç | RT tarafı davranışı |
|---|---|---|---|---|
| S1 | RT → non-RT | Ölçüm/olay verisi (ring tampon) | Non-RT okur | Yaz, bekleme; doluysa eskiyi bırak (drop-oldest şablonu) |
| S2 | RT → non-RT | Parametre değişikliği bildirimi | UI/log güncellenir | Atomik yaz, devam et |
| S3 | non-RT → RT | Parametre değişikliği (EQ gain vb.) | RT bir sonraki tick'te uygular | Atomik oku, **rampa** ile uygula (click-free) |
| S4 | non-RT → RT | Komut (duraklat/başlat) | RT durum makinesi geçişi | Atomik komut kuyruğu; lock yok |
| S5 | non-RT → RT | Yeni tampon/cihaz yapılandırması | RT yeniden bağlanır | **Blok kenarında** uygula; anlık geçiş yasak |

### §8.2 Kuyruk yapıları adayları (kütüphane iddiası yok)

| Yapı | Yön | Neden aday | Durum |
|---|---|---|---|
| SPSC ring buffer | Tek RT yazar, tek non-RT okur | Ölçüm/olay akışı | ⚠️ aday — implementasyon yok |
| MPSC ring buffer | Çok non-RT yazar, tek RT okur | Komut toplama | ⚠️ aday |
| Atomik double-buffer | İki tampon dönüşümlü | Parametre seti | ⚠️ aday |
| Sabit boyutlu atomik sayaç | Tek sayı | Durum/sayaç | ⚠️ aday |

> ⚠️ VERIFICATION REQUIRED: hangi yapının kullanılacağı **karar değildir**;
> yalnızca sınırın **asla lock kullanmadığı** kuralı bağlayıcıdır (K2). C++
> std kütüphanesi referansları (atomik, memory_order) ayrı bir karar
> dosyasında ele alınacaktır; bu dosyada API adı **yazılmaz** (uydurma yasağı).

## §9 Priority inversion ve çözüm şablonları

| # | Senaryo | Mekanizma | Çözüm şablonu | Durum |
|---|---|---|---|---|
| 1 | RT thread, non-RT thread'in kilidini bekler | Mutex kilidi | Kilit **RT yolunda hiç kullanılmaz** (K2) — en güçlü çözüm | ✅ kural var |
| 2 | Düşük öncelikli işlem yükseldi (OS zamanlayıcısı) | OS zamanlama | RT önceliği uygun seçilmeli (R1) + periyot bütçesi (R2) | ⚠️ R1/R2 |
| 3 | RT yolunda bekleyen sistem çağrısı (I/O) | Sürücü gecikmesi | K3 — I/O RT yolunda yasak | ✅ kural var |
| 4 | Uzun non-RT işin atomik yazması geç kalır | Yarış durumu | Tek yazar tek okuyucu (SPSC) kuralı | ⚠️ §8.2 |
| 5 | Gözlemci (watchdog) RT'yi kilitler | Gözlemci tasarımı | Gözlemci **sadece okur**, RT'yi durdurmaz; uyarı non-RT'ye | ⚠️ tasarımı yok (diagnostics) |

**Priority inversion özeti (kavramsal):** yüksek öncelikli bir thread'in
düşük önceliklinin kaynakmasını beklemesi → zincir etkisi. Ses yolunda bunun
**tek etkili çözümü** kilit kullanmamaktır (K2); öncelik mirası (priority
inheritance) kilit tabanlıdır ve bu yüzden RT yoluna taşınmaz. Bu bir
**mühendislik çerçevesidir** (kaynaklı değil, klasik disiplin); CoreMusic
uygulaması henüz **yoktur** (repo'da C++ yok — §4 satır 6).

## §10 Callback yasak listesi — genişletilmiş katalog

§6'daki kısıtların **kesin yasak listesi** hâli (output §7.2 A5 ile
bağlantılı: callback'i çağıran platform ne olursa olsun bu liste geçerlidir).

| # | Yasak iş | Yerine | İhlal tespiti (şablon) |
|---|---|---|---|
| Y1 | `new` / `delete` / `malloc` / `free` | Önceden allocate edilmiş havuz | Bellek ölçümü (debug build) |
| Y2 | `mutex` / `lock_guard` / `unique_lock` | Atomik + lock-free kuyruk | Derleme/denetim (kod incelemesi) |
| Y3 | Dosya/ağ yazma-okuma | non-RT vardiyaya devir | Callback süresi pikleri |
| Y4 | Konsol/log basma | Halka tampon + non-RT okuma | I/O ölçümü |
| Y5 | `throw` (yayma) | Döndürme kodu / bayrak | `noexcept` sözleşmesi |
| Y6 | `sleep` / `wait` / koşul değişkeni | Bekleme yok; veri yoksa sessizlik | Süre ölçümü |
| Y7 | Görsel/pencere mesajı | UI non-RT taraf | Kod incelemesi |
| Y8 | Dinamik std konteyner ekleme (push_back) | Sabit boyut, baştan allocate | Kapasite ölçümü |
| Y9 | Karmaşık biçimlendirme (string format) | Hazır format, sabit | Kod incelemesi |
| Y10 | Kütüphane başlangıç kodu (lazy init) | `main`/initialize'da yap | İlk çağrı pikleri |

## §11 Deadline aşımı davranış niyeti (Q5 ile kilitli — şablon)

§2 satır 5'teki "deadline aşımının güvenli davranışının niyeti" maddesi.

| # | Aşım düzeyi | Davranış (niyet) | Sahip |
|---|---|---|---|
| 1 | Sınır ihlali yok | Normal tick | realtime |
| 2 | Sınır aşımı ama tick tamamlandı | Olay sayacı + non-RT'ye rapor | diagnostics |
| 3 | Tick tamamlanamadı (kısmi) | Sonucu **sessizlik** ile tamamla; eski veriyi tekrarlama | output + realtime |
| 4 | Tekrarlı aşım (N tick) | Kalıcı uyarı; engine durum makinesi geçişi | engine + diagnostics |
| 5 | Cihaz/driver kesintisi | Fallback kuralı (T8) uygulanır | device + driver |

**Niyet kuralı:** aşım durumunda **asla "hızlıca yetişmek" için iş atlanmaz**
(atlanan iş ses bozulması üretir); aksine **sessizlik + rapor** tercih edilir.
N atlanan tick sayısı ve eşiği **UNKNOWN** (R2 — sayı uydurulmaz).

## §12 Ölçüm ve kapanış kapıları (açık)

| Kapı | Koşul | Şu an |
|---|---|---|
| RT-G1 | RT thread önceliği/süreci kararı (R1) | ❌ UNKNOWN |
| RT-G2 | Tick bütçesi + periyot boyutu ölçümü (R2) | ❌ UNKNOWN |
| RT-G3 | Vardiya sınırı tasarımı (kuyruk yapısı kararı — §8.2) | ❌ açık (Q5) |
| RT-G4 | Yasak ihlali tespit mekanizması (R6) | ❌ açık |
| RT-G5 | İlk `[KANIT]` geçişi: lock-free kod kanıtı | ❌ 0 (repo'da C++ yok — master §7 D6) |

## §13 RT bütçe bileşenleri — genişletilmiş katalog

§7'deki altı bileşenin (B1–B6) **genişletilmiş** hâli: her bütçe bileşeninin
**ne yaptığı**, **kimin ölçeceği** ve **kapıya** etkisi.

| # | Bileşen | Ne yapar | Ölçer kim? | Kapı |
|---|---|---|---|---|
| B1a | Callback giriş damgası | İlk işaretleme | realtime | RT-G2 |
| B1b | İlk iş kalemi başlangıcı | DSP zinciri girişi | K004 + testing | RT-G2 |
| B2a | EQ/işlem süresi | K004 çekirdeği | K004 (K004.testing) | RT-G2 |
| B2b | Parametre rampası | Click-free geçiş | mixing + realtime | RT-G2 |
| B3a | Routing toplamı | Matris uygulaması | routing | RT-G2 |
| B3b | Mixing toplamı | Gain/toplama | mixing | RT-G2 |
| B4a | Kuyruk yazma | RT→non-RT | backend | RT-G3 |
| B4b | Ölçüm/olay yazma | RT→diagnostics | diagnostics | RT-G3 |
| B5a | Periyot süresi (tick) | Sınır | driver pazarlığı | RT-G2 |
| B5b | Güvenlik payı | Bütçe rezervi | realtime | RT-G2 |
| B6a | Toplam iş | B1+B2+B3+B4 | testing | RT-G2 |
| B6b | Toplam ≤ tick kontrolü | Sınır ihlali | testing (TS1) | RT-G2 |

**Genişletilmiş ders:** bütçe **on iki bileşene** bölünür; her bileşenin
**tek bir sahibi** vardır (RT-G2 kapısında ölçülür). Sayılar hâlâ
**UNKNOWN** — bu tablo yalnız **sahiplik** matrisidir.

## §14 Vardiya modeli — veri yapısı derinlemesi (SPSC/MPSC)

§8.2'deki kuyruk adaylarının **davranış derinliği**. Yapı seçimi **karar
değildir** (Q5 kilitli); tablo yalnız **davranış farklarını** taşır.

| # | Yapı | Yazar/Okuyucu | Yarış güvenliği | Uygun akış |
|---|---|---|---|---|
| 1 | SPSC ring | 1 / 1 | Tek yazar — basit | Ölçüm (RT→non-RT) |
| 2 | MPSC ring | Çok / 1 | Dikkatli atomik gerekir | Komut toplama (non-RT→RT) |
| 3 | Double-buffer | 2 (dönüşümlü) | Bayrak ile | Parametre seti |
| 4 | Atomik sayaç | 1 / 1 | En basit | Durum/sayaç |
| 5 | Lock-free stack | Çok / çok | Karmaşık | **RT yolunda önerilmez** |

**Ders:** RT yolunda **tek yazar** (SPSC) **en güvenli**dir; MPSC/karmaşık
yapılar **dikkat** gerektirir (yarış riski). §8.2'deki "asla lock yok"
kuralı bu yapıların **hepsi için** geçerlidir (K2).

## §15 Priority inversion — senaryo derinlemesi (çözüm şablonları)

§9'daki beş senaryonun **çözüm derinliği**: her senaryo için **tam**
çözüm zinciri (önleme + tespit + kurtarma).

| # | Senaryo | Önleme | Tespit | Kurtarma |
|---|---|---|---|---|
| 1 | Mutex bekleme | K2 (yasağı) | Derleme/denetim | — (önlenir) |
| 2 | Düşük öncelikli yükseldi | R1 (öncelik) | CPU profili | Öncelik düzeltmesi |
| 3 | Bekleyen sistem çağrısı | K3 (yasağı) | Süre pikleri | — (önlenir) |
| 4 | Yavaş atomik yazma | SPSC (§14) | Parametre gecikmesi | Yapı değişimi |
| 5 | Gözlemci kilidi | Sadece okuma | Gözlemci profili | Gözlemci tasarımı |

**Çözüm dersi:** her senaryonun **tam üçlüsü** (önleme + tespit + kurtarma)
tanımlanır; eksik olan **kör nokta**dır. §9'da yalnız önleme vardı — bu
bölüm tespit ve kurtarmayı ekler.

## §16 Yasak listesi — tespit mekanizması derinlemesi (R6)

§10'daki on yasağın (Y1–Y10) **tespit** derinliği: her yasak **nasıl**
tespit edilir (R6 açık — bu tablo yalnız **yöntem** taşır).

| # | Yasak | Tespit yöntemi | Zamanlama | Otomatik mi? |
|---|---|---|---|---|
| Y1 | Bellek ayırma | Bellek ölçümü (debug) | Test | Kısmen |
| Y2 | Kilitleme | Kod incelemesi + statik denetim | Commit | Kısmen (araç yok) |
| Y3 | Dosya/ağ I/O | Süre pikleri | Test | Evet (ölçüm) |
| Y4 | Log basma | I/O ölçümü | Test | Evet |
| Y5 | Exception yayılım | Derleme (`noexcept`) | Compile | **Evet** |
| Y6 | Sleep/wait | Süre ölçümü | Test | Evet |
| Y7 | UI çağrısı | Kod incelemesi | Commit | Hayır |
| Y8 | Dinamik ekleme | Kapasite ölçümü | Test | Kısmen |
| Y9 | Biçimlendirme | Kod incelemesi | Commit | Hayır |
| Y10 | Lazy init | İlk çağrı pikleri | Test | Evet |

**Tespit dersi:** Y5 **derlemede** yakalanır (tek otomatik yasak); diğerleri
**ölçüm veya inceleme** ister. R6 kapanınca hangi yöntemin **zorunlu**
olduğu kararlaştırılacaktır (bu dosyada karar yok).

## §17 Deadline aşımı — sayım yüzeyi (N eşiği)

§11'deki "N tick" belirsizliğinin **yüzey** hâli: hangi sayaç **ne**yi sayar.
Sayı **UNKNOWN** (R2) — tablo yalnız **sayaç türlerini** taşır.

| # | Sayaç | Ne sayar | Sahip | Değeri |
|---|---|---|---|---|
| 1 | Periyot aşım sayacı | Kaç tick geç kaldı | realtime | **UNKNOWN** |
| 2 | Underrun sayacı | Kaç boş tampon | output | **UNKNOWN** |
| 3 | Drift sayacı | Kaç örnek kayma | synchronization | **UNKNOWN** |
| 4 | İhlal sayacı | Kaç K-yasağı ihlali | realtime | **UNKNOWN** |
| 5 | Fallback sayacı | Kaç device loss | device | **UNKNOWN** |

**Sayım dersi:** her sayacın **tek bir sahibi** vardır (diagnostics'te
toplanır); tek bir "genel sayaç" **sınıflandırmayı gizler** (output §18
dersi ile aynı).

## §18 RT disiplini — cross-reference tablosu (diğer dallarla sınır)

§6'daki kısıtların (K1–K8) **sınır** boyutu: her kısıt hangi dalın
**sınırında** durur.

| # | Kısıt | Sınır dalı | İlişki |
|---|---|---|---|
| K1 | Bellek ayırma | backend | Tampon sahipliği (Q5) |
| K2 | Kilitleme | backend · diagnostics | Lock-free sınırı |
| K3 | I/O | diagnostics | Vardiya sınırı (§8) |
| K4 | Log | diagnostics | Halka tampon |
| K5 | Exception | engine | `noexcept` sözleşmesi |
| K6 | Sleep/wait | engine · driver | Bekleme yok |
| K7 | Dal determinizmi | K004-dsp | DSP dalı |
| K8 | UI çağrısı | K000-frontend | UI non-RT |

**Sınır dersi:** her kısıtın **bir komşu dalı** vardır; bu yüzden RT
disiplini **yalnız realtime dalının** işi değildir — komşu dallar da
**sınırda** durur (K003 içi bağımlılık — audio-bagimliliklar §7).

## §19 RT — sık karşılaşılan tasarım soruları (FAQ — cevapsız)

§6–§18'in **tasarım sırasında** sorulacak yüzeyi. Cevap yok; yalnız
**soru genişletmesi** var (zero-hallucination).

| # | Soru | Neden | Bağlantı |
|---|---|---|---|
| 1 | Öncelik kaç olmalı? | OS'e bağlı | R1 · K000 |
| 2 | Periyot boyutu kaç örnek? | Sürücü pazarlığı | R2 · driver |
| 3 | Kuyruk derinliği? | Bütçe | Q5 · backend |
| 4 | Vardiya hangi yapı? | Yarış riski | §14 |
| 5 | İhlal nasıl tespit? | Otomasyon | R6 · §16 |
| 6 | Aşım eşiği N? | Ölçüm yok | R2 · §17 |
| 7 | Watchdog kim? | Gözlemci tasarımı | §15 satır 5 |

**FAQ kuralı:** bu sorular **kapatılmadan** realtime `verified` olamaz
(§12 RT-G kapıları).

## §20 RT — ölçüm protokolleri (kapıların kapanma yolu)

§12 kapılarının **protokol** hâli: her kapı **nasıl** ölçülür.

| # | Kapı | Protokol | Ortam |
|---|---|---|---|
| 1 | RT-G1 (öncelik) | OS öncelik okuma + profilleme | Test |
| 2 | RT-G2 (bütçe) | Periyot başına süre ölçümü (N tick) | Test + cihaz |
| 3 | RT-G3 (yapı) | Yarış stresi testi | Test |
| 4 | RT-G4 (ihlal) | Statik denetim + debug ölçümü | CI + test |
| 5 | RT-G5 ([KANIT]) | Lock-free kod kanıtı | Repo (C++ gerekli) |

**Protokol kuralı:** kapılar **ölçümle** kapanır; bu dosyada **hiçbiri
açılmaz** (§12 kapanış kuralı).

## §21 RT — bağımlılık zinciri örneği: periyot aşımı

§11 sınıf 4'ün (tekrarlı aşım) **adım adım** zinciri: hangi dal hangi
adımda devreye girer.

| # | Adım | Dal | Eylem |
|---|---|---|---|
| 1 | Periyot aşımı | realtime | Sayaç (§17 satır 1) |
| 2 | Kısmi tick | output | Sessizlik (output §12 U3) |
| 3 | Tekrarlı (N tick) | realtime | Kalıcı uyarı (§11 sınıf 4) |
| 4 | Engine geçişi | engine | Durum makinesi (Q7) |
| 5 | Rapor | diagnostics | Olay + sınıf |
| 6 | Kapı etkisi | testing | RT-G2 · TS-G1 |

**Zincir dersi:** periyot aşımı **altı adımda** altı dalı etkiler; bu
**entegrasyon testidir** (L2/L3 — testing-kapsam §6).

## §22 RT — sık yapılan hatalar (tasarım uyarısı)

§19 FAQ'ının **tersi**: hangi hata **sık** yapılır ve **nasıl** önlenir.

| # | Hata | Sonuç | Önlem |
|---|---|---|---|
| 1 | Mutex RT yolunda | Priority inversion | K2 (yasağı) |
| 2 | Runtime bellek ayırma | Tahminsiz gecikme | K1 · §28 (kapsam) |
| 3 | Lazy init | İlk çağrı pikleri | Y10 |
| 4 | Tek sayaç | Sınıf gizlenir | §17 |
| 5 | MPSC karmaşıklığı | Yarış riski | §14 — SPSC tercih |
| 6 | Gözlemci kilidi | RT bekler | §15 satır 5 |
| 7 | Aşım sessizliği | Kör nokta | §11 sınıf 4 |

## §23 RT — devir planı (dosya kapandıktan sonra)

§20 protokollerinin **devir** hâli: hangi iş **hedef dala** devredilir.

| # | Devir | Hedef dal | Ne devrediliyor |
|---|---|---|---|
| 1 | Bütçe ölçümü | diagnostics · testing | §20 protokol 2 |
| 2 | Kuyruk yapısı kararı | backend | §14 (Q5 kilitli) |
| 3 | İhlal tespiti | diagnostics | §16 yöntemleri |
| 4 | Engine durum makinesi | engine | §11 sınıf 4 (Q7) |
| 5 | CPU profili | performance-engineer | §15 senaryo 2 |

**Devir dersi:** bu dosya **hiçbir RT-G kapısını kapatmaz**; yalnız
**işleri hazırlar** ve **devreder**.

## §24 RT — dosya özet tablosu (durum)

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `realtime/realtime-kapsam.md` |
| 2 | Derinleştirilen bölümler | §6–§24 (2026-10-13) |
| 3 | Kanıt oranı | **Değişmedi** (eklenenler şablon/niyet) |
| 4 | Açık kapı | RT-G1…RT-G5 (§12) |
| 5 | İlk [KANIT] | ❌ 0 (repo'da C++ yok — D6) |

## §25 RT — platform callback karşılaştırma tablosu

§6 kısıtlarının **platform** boyutu: hangi platform callback'i hangi
davranışlarla gelir. Tüm satırlar **doküman çerçevesidir**; CoreMusic
kararı **yoktur** (R5 kilitli).

| # | Platform | Callback kaynağı | Davranış | RT açısından |
|---|---|---|---|---|
| 1 | Windows/ASIO | Sürücü callback (`bufferSwitch`) | Dışarıdan gelir | §21 (master §5 T8 · vault notu) |
| 2 | Windows/WASAPI | Olay tabanlı akış | Olayla tetiklenir | output §7.2 A5 |
| 3 | macOS/Core Audio | Render çağrısı (pull) | Sistem çağırır | §4 A1 |
| 4 | Linux/ALSA | Yazma döngüsü (periyot) | Uygulama yazar | §6 (output §13) |
| 5 | Linux/PipeWire | Driver node (sayaç) | Sayaçla tetiklenir | §5 (T15) |
| 6 | Linux/JACK | Process callback | Graf çağırır | ⚠️ erişim yok (ad — URL yok) |

**Karşılaştırma dersi:** tüm platformlarda callback **dışarıdan** gelir;
CoreMusic **çağıran değil, çağrılan** taraftır. Bu, §21 ve §22'deki
"host'a tabidir" dersinin **platform-geneli** hâlidir.

## §26 RT — kuyruk derinliği şablonu (Q5 bağlantılı)

§14'teki yapı seçiminin **derinlik** boyutu: derinlik **bütçeden** çıkar
(§7 B4). Değer UNKNOWN; tablo yalnız **formül ve sahiplik** taşır.

| # | Bileşen | Formül (kavramsal) | Sahibi |
|---|---|---|---|
| 1 | Yazma hızı | Bir tick'te N olay (UNKNOWN) | diagnostics |
| 2 | Okuma hızı | non-RT periyodu (UNKNOWN) | diagnostics |
| 3 | Derinlik | (1) × (2) × güvenlik payı | backend (Q5) |
| 4 | Taşma politikesi | drop-oldest vs block (RT'de block **yasak**) | realtime (K6) |

**Derinlik dersi:** derinlik **bütçeden** çıkar, uydurulmaz; taşma
politikesi RT'de **yalnız drop** olabilir (block = K6 ihlali).

## §27 RT — engine durum makinesi şablonu (Q7 bağlantılı)

§11 sınıf 4'teki "engine durum makinesi"nin **şablon** hâli: hangi durum
**hangi** RT davranışını belirler. Durum kümesi **UNKNOWN** (Q7 — AAudio
5 durum şablon alınır mı?); tablo yalnız **yapı** taşır.

| # | Durum (aday — Q7) | RT davranışı | Geçiş |
|---|---|---|---|
| 1 | Açık (open) | Döngü yok | Başlat |
| 2 | Çalışıyor (started) | Döngü aktif | Duraklat · Durdur |
| 3 | Duraklatılmış | Döngü beklemede (kilit yok) | Devam · Durdur |
| 4 | Durulmuş (stopped) | Döngü kapalı | Başlat |
| 5 | Temizlenmiş (flushed) | Tampon boşaltıldı | Açık |

**Durum dersi:** durum geçişleri **RT yolunda** yapılır (atomik komut
kuyruğu — §8.1 S4); geçişlerde **lock yok** (K2). Durum kümesi Q7 kapanınca
**onaylanacaktır** (bu tablo adaydır).

## §28 RT — cross-ölçüm yüzeyi (diagnostics komşusu)

§17 sayaçlarının **ölçüm** yüzeyi: hangi sayacın **hangi** protokolle
toplanacağı (TS1 — ölç ↔ karar).

| # | Sayaç | Toplama | Rapor |
|---|---|---|---|
| 1 | Periyot aşım | Periyot kenarı (§5 dersi) | diagnostics olay |
| 2 | Underrun | Periyot kenarı (output §13) | diagnostics olay |
| 3 | Drift | Ölçüm periyodu (R3) | diagnostics olay |
| 4 | İhlal | Debug/test | diagnostics olay |
| 5 | Fallback | Olay anı (device) | diagnostics olay |

**Ölçüm dersi:** sayaçlar **periyot kenarında** toplanır (ALSA örneği — §7);
kenar dışında toplama **RT yasağına** takılır (K4 log yazma yasağı).

## §29 RT — devir sonrası periyodik kontrol listesi

§23 devir planının **periyodik** hâli: bu dosya kapandıktan sonra **kim**
**ne**yi tekrar kontrol eder.

| # | Kontrol | Sahip | Ne zaman |
|---|---|---|---|
| 1 | RT-G kapıları hâlâ açık mı? | Vault Steward | Her revizyon |
| 2 | Yeni platform callback kanıtı geldi mi? | researcher | Araştırma |
| 3 | Repo'da C++ kodu belirdi mi? | embedded-engineer | Her commit |
| 4 | Q5/Q7 kapandı mı? | Vault Steward | Her ADR oturumu |
| 5 | Yeni yasak eklendi mi (K-kataloğu)? | realtime | Tasarım değişikliği |

## §30 RT — sık yapılan hatalar (dosya gözlem yüzeyi)

§22 uyarılarının **dosya gözlem** hâli: hangi hata **nasıl** görünür.

| # | Hata | Gözlem | Kayıt |
|---|---|---|---|
| 1 | Mutex RT yolunda | Periyot aşımı (RT-G2) | realtime |
| 2 | Runtime bellek | Bellek ölçüm artışı | realtime (Y1) |
| 3 | Lazy init | İlk çağrı pikleri | realtime (Y10) |
| 4 | Tek sayaç | Sınıf gizlenir | diagnostics (§17) |
| 5 | MPSC karmaşıklığı | Yarış stresi bulgusu | backend (§14) |
| 6 | Gözlemci kilidi | Gözlemci profili | diagnostics (§15) |
| 7 | Aşım sessizliği | Kör nokta | testing (§11) |

## §31 RT — kapı-kanıt zaman çizelgesi (öneri)

§20 protokollerinin **sıra** hâli: hangi kapı **önce** kapanır (öneri —
karar değil).

| # | Sıra | Kapı | Neden önce? |
|---|---|---|---|
| 1 | 1 | RT-G1 (öncelik) | OS kararı — diğer kapılar buna bağlı |
| 2 | 2 | RT-G2 (bütçe) | Periyot pazarlığı (RT-G1 sonrası) |
| 3 | 3 | RT-G4 (ihlal) | Tespit mekanizması |
| 4 | 4 | RT-G5 ([KANIT]) | Kod kanıtı (repo C++ gerekli) |
| 5 | 5 | RT-G3 (yapı) | En karmaşık (Q5 kilitli) |

**Sıra dersi:** kapılar **bağımlılık sırasıyla** kapanır; RT-G3 **en
son**dur (Q5 kararı + yapı testi).

## §32 RT — son özet tablo

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `realtime/realtime-kapsam.md` |
| 2 | İlk kısıt kataloğu | §6 (K1–K8) |
| 3 | Derinleştirilen bölümler | §6–§32 (2026-10-13) |
| 4 | Kanıt oranı | **Değişmedi** |
| 5 | Açık kapı | RT-G1…RT-G5 (§12) |
| 6 | Sonraki sahip | diagnostics · testing · backend · engine |

## §33 RT — callback giriş/çıkış sözleşmesi şablonu

§6 kısıtlarının **sözleşme** hâli: callback girdi/çıktılarının **sahipliği**.
Sözleşme **niyet**tir; Q5 kilitli.

| # | Alan | Yönü | Sahibi | Kural |
|---|---|---|---|---|
| 1 | Girdi tamponu (gelen ses) | driver → RT | driver (Q5) | RT yalnız **okur** |
| 2 | Çıktı tamponu (giden ses) | RT → driver | RT (Q5 kilitli) | RT yalnız **yazar** |
| 3 | Parametre seti | non-RT → RT | mixing · K004 | Atomik oku (§8.1 S3) |
| 4 | Ölçüm/olay | RT → non-RT | diagnostics | Atomik yaz (§8.1 S1) |
| 5 | Durum (engine) | RT → non-RT | engine | Atomik yaz |

**Sözleşme dersi:** her alanın **tek sahibi** vardır; sahipsiz alan
**yarış** üretir (K2'nin özü).

## §34 RT — kısıt ↔ platform uyum tablosu

§25 platform karşılaştırmasının **kısıt** boyutu: hangi kısıt **hangi**
platformda **daha kritik**.

| # | Kısıt | ASIO | WASAPI | Core Audio | ALSA | PipeWire |
|---|---|---|---|---|---|---|
| 1 | K1 (bellek) | kritik | kritik | kritik | kritik | kritik |
| 2 | K2 (kilit) | kritik | kritik | kritik | kritik | kritik |
| 3 | K3 (I/O) | yüksek | yüksek | yüksek | yüksek | yüksek |
| 4 | K6 (sleep) | kritik | kritik | kritik | kritik | kritik |
| 5 | K8 (UI) | orta | orta | orta | orta | orta |

**Uyum dersi:** K1 · K2 · K6 **tüm platformlarda kritiktir** (evrensel RT
yasağı); bu, "platform bağımsız RT çekirdek" tasarımının gerekçesidir.

## §35 RT — en son karar yüzeyi (kapı-kanıt özeti)

§20 ve §31'deki kapı-kanıt ilişkisinin **tek satırda** özeti.

| Kapı | Kanıt | Nereden | Bu dosyada? |
|---|---|---|---|
| RT-G1 | OS öncelik kararı | ADR + ölçüm | ❌ açık |
| RT-G2 | Periyot başına süre ölçümü | diagnostics + testing | ❌ açık |
| RT-G3 | Yarış stresi testi | testing | ❌ açık |
| RT-G4 | Statik denetim + debug ölçümü | CI + diagnostics | ❌ açık |
| RT-G5 | Lock-free kod kanıtı | Repo (C++ gerekli — D6) | ❌ açık |

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
