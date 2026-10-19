---
title: "driver — Kapsam"
type: architecture-scope
node_id: K003-audio/driver
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# driver — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 3 bu dalı **"Sürücü yüzeyiyle tek noktadan
etkileşim"** olarak yazar. Basitçe: işletim sisteminin sürücü katmanına giden
**tek kapı** — motor ne yapmak isterse (aç/kapa/yaz/ok) bu kapıdan geçer.
Farklı kapılar (Windows'ta ASIO ve WASAPI, Linux'ta ALSA ve PipeWire) varsa
aradaki çevrimi bu düğüm sahiplenir; motor her kapı için ayrı kod yazmaz.

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (v2 §3 KURAL 3); içerik iddiası değildir. Hangi sürücü kapılarının kapsama
> alınacağı (ASIO mu WASAPI mi) `driver/index.md` open_questions'da açıktır.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Sürücü yüzeyiyle tek noktadan etkileşim (aç/kapa/ yaz/ok komutlarının sürücüye iletilmesi) | Sürücünün kendisi (kernel modülü) ve register yazımı | K000-isletim-sistemi (master §2.2) · K005-firmware |
| 2 | Platform sürücü API'lerinin tek arayüzde toplanması (ASIO · WASAPI · ALSA · PipeWire) | Bu API'lerin özellik katalogu ve detay belgeleri (onlar backend/asio alt-alanında) | backend (master §3 satır 4) |
| 3 | Sürücü hata/kesinti kodlarının yorumlanması ve üst katmana tercümesi | Hata loglarının toplanması ve sınıflandırılması | diagnostics (master §3 satır 11) |
| 4 | Cihaz kaybı (device-loss) durumunda sürücüden güvenli biçimde çekilme niyeti | Hangi cihazın seçileceği kararı | device (master §3 satır 2) |
| 5 | ASIO → WASAPI fallback zincirinin **sürücü tarafı** yürütmesi (`.ai/AGENTS.md` §17 #6) | Fallback tetikleyen politika/karar mantığı | backend (Q5 ile bağlantılı) |
| 6 | *(dışında)* buffer/kuyruk ve çalışma zamanı altyapısı | Bellek yönetimi | backend (master §3 satır 4) |
| 7 | *(dışında)* sürücü gecikmesinin ölçümü ve raporlanması | Ölçüm metodu ve kabul kriteri | diagnostics · testing (master §3 satır 11-12) |
| 8 | *(dışında)* sürücünün zamanlama/lock-free kuralları | Gerçek zamanlı çalışma disiplini | realtime (master §3 satır 9) |

**Yasak:** satır 4–5'teki ayrım Q6 (input/output ↔ device/driver) kapanana kadar
**sınır önerisi** değildir; driver ile device aynı kararı iki yerde verirse
`audio-bagimliliklar.md` §2 çakışma riski gerçekleşir.

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| D1 | driver ↔ backend | sürücü komutu (niyet) ↔ altyapı (buffer/kuyruk) | ⚠️ master §8 Q5 ile kilitli — VERIFICATION REQUIRED |
| D2 | driver ↔ device | "konuşma kanalı" ↔ "hangi cihaz" kararı | ⚠️ master §8 Q6 — VERIFICATION REQUIRED |
| D3 | driver ↔ realtime | komutun iletilmesi ↔ döngünün kuralları | ⚠️ ad-türetme (master §3 satır 3 ve 9) |
| D4 | driver ↔ diagnostics | sürücü hatasının üretilmesi ↔ sınıflandırılması | ⚠️ üst karar bekliyor |
| D5 | driver ↔ K005-firmware | OS sürücüsü ↔ donanım içi firmware | ✅ master §2.2 sınır tablosu (boot/register K005) |
| D6 | driver ↔ K000-isletim-sistemi | çağrı yüzeyi ↔ sürücü yükleme/kaynak yönetimi | ✅ master §2.2 + §6 (zemin katman) |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "driver · Sürücü yüzeyiyle tek noktadan etkileşim · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 64 | ✅ dosya okundu |
| 2 | Açık soru: "hangi sürücü kapıları kapsama alınacak? ASIO mu WASAPI mi?" | `driver/index.md` open_questions (satır 15) | ✅ dosya okundu |
| 3 | ASIO SDK açık kaynak ilan edilmiş (GPLv3) | `audio-master.md` §5 T1 → https://www.steinberg.net/developers/asiosdk-open | ✅ dosya okundu (URL oturumda fetch edildi) |
| 4 | WASAPI exclusive mode streams belgesi mevcut | `audio-master.md` §5 T7 → learn.microsoft.com exclusive-mode-streams | ✅ dosya okundu (URL fetch) |
| 5 | ALSA hw_params/sw_params ayrımı + beş access modu | `audio-master.md` §5 T13 → alsa-project.org pcm.html | ✅ dosya okundu (URL fetch) |
| 6 | ALSA plug plugin format/channels/rate otomatik dönüşümü | `audio-master.md` §5 T14 → alsa-project.org pcm_plugins.html | ✅ dosya okundu (URL fetch) |
| 7 | ASIO cihaz kaybında WASAPI fallback kuralı bağlayıcıdır | `.ai/AGENTS.md` §17 #6 (register satırı `audio-master.md` §5 T8) | ✅ dosya okundu |
| 8 | ASIO alt-alanı 8 dosyalık katalog olarak mevcut (index + kapsam + 6 deep-dive) | `.ai/architecture/K003-audio/backend/asio/` glob sayımı 2026-10-12 | ✅ glob ölçümü |
| 9 | Repo'da K003 sürücü kodu (C/C++) yok | kök `*.cpp`/`*.h` glob 0 isabet (2026-10-12) | ✅ shell ölçümü |
| 10 | ASIO SDK 2.3.4 sürüm numarasının bağımsız web teyidi | — | ⚠️ VERIFICATION REQUIRED (master §5 T11) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | Hangi sürücü kapıları kapsama alınacak — ASIO mu WASAPI mı, ikisi birden mi? | ⚠️ VERIFICATION REQUIRED | embedded-engineer |
| R2 | ASIO SDK GPLv3 lisansı CoreMusic'e nasıl etki eder? (master §8 Q1) | ⚠️ VERIFY REQUIRED | legal-advisor |
| R3 | ASIO SDK 2.3.4 hedefi güncel sürümlerle uyumlu mu? (master §8 Q4) | ⚠️ VERIFY REQUIRED | embedded-engineer |
| R4 | driver ↔ backend sınırında buffer sahipliği kime ait? (master §8 Q5) | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R5 | Sürücü gecikmesi hangi birimle raporlanır (ms? periyot sayısı?) | **UNKNOWN** — ölçüm metodu yok | diagnostics |
| R6 | NAudio + ASIO (2026-09-28) hangi thread? (master §8 Q2) | ⚠️ VERIFY REQUIRED | researcher |

**Kapanış:** status: draft kalır. R1 kapanmadan "driver yalnız ASIO konuşur"
iddiası yazılmaz; R5 için sayısal gecikme değeri uydurulmaz → UNKNOWN.

---

## §6 Sürücü yaşam döngüsü şablonu

> Bu bölüm sürücünün **aşama dizisini** tanımlar; kapı seçimi (ASIO/WASAPI) §5 R1
> ile açıktır ve burada **kararlaştırılmaz**. Aşama dizisi doğrulanmış ALSA
> kanıtına dayanır (`driver-ornekler.md` §2 satır 2: `hw_params` → `sw_params`).

### §6.1 Yaşam döngüsü aşamaları

| # | Aşama | Ne olur | Bu dalın payı | Komşu payı | Durum |
|---|---|---|---|---|---|
| L1 | Platform seçimi | hangi kapı kullanılacağı | niyet taşır | R1 kararı | ⚠️ R1 AÇIK |
| L2 | Kapı açılması | sürücü yüzeyine bağlanma | ✅ tek kapı ilkesi (§1) | — | şablon |
| L3 | Cihaz enumerasyonu | sunulan cihaz listesi | ✅ taşıma | device kararı (§2 satır 4) | SINIR AÇIK (D2) |
| L4 | Format pazarlığı | desteklenen küme üzerinden | ✅ yürütme | device niyeti | SINIR AÇIK (D2) |
| L5 | Buffer/periyot bağlama | 512/48 hedefi (sartname; ölçüm PLANNED) | ✅ yürütme | backend sahipliği (§5 R4) | ⚠️ R4 |
| L6 | Kip kararı | exclusive/shared | ✅ yürütme | R1 | ⚠️ R1 AÇIK |
| L7 | Akışın başlatılması | yazma/okuma başlar | ✅ | engine niyeti | şablon |
| L8 | Çalışma gözetimi | hata/kesinti bildirimi | ✅ bildirir | diagnostics sınıflandırır | §3 D4 |
| L9 | Cihaz kaybı tepkisi | güvenli çekilme niyeti | ✅ (§2 satır 4) | zincir sırası R4 (device) | ⚠️ device R4 |
| L10 | Kapatma | akışın serbest bırakılması | ✅ | — | şablon |
| L11 | Yeniden açma | hata/koşul sonrası | ✅ | device kararı | şablon |
| L12 | Platform değişimi | fallback kapısına geçiş | ✅ yürütme (§2 satır 5) | politika backend/device | ⚠️ R1 |

### §6.2 Durum şeması (aday — kapı bağımsız)

| # | Durum | Tanım | Giriş | Çıkış | Etiket |
|---|---|---|---|---|---|
| D1 | KAPALI | kapı yok | açılış | L2 | şablon |
| D2 | ACILIYOR | L2 sürüyor | L2 | L3/L4 | şablon |
| D3 | HAZIR | pazarlık tamam, akış yok | L4–L6 | L7 | şablon |
| D4 | CALISIYOR | yazma/okuma aktif | L7 | L8/L9/L10 | şablon |
| D5 | HATA | sürücü hatası bildirildi | L8 | L11 veya L12 | §3 D4 |
| D6 | CIHAZ_KAYBI | güvenli çekilme | L9 | L12 | ⚠️ zincir (device R4) |
| D7 | KAPATILIYOR | L10 | D4 | D1 | şablon |
| D8 | YENIDEN_ACILIYOR | L11 | D5 | D3/D4 | şablon |
| D9 | FALLBACK | L12 — başka kapı | D6/D5 | D2 (yeni kapı) | ⚠️ R1 |
| D10 | DURDU | tüm kapılar tükendi | D6 | manuel | ⚠️ zincir |

### §6.3 Geçiş tablosu

| # | Durum | Koşul | Hedef | Not |
|---|---|---|---|---|
| 1 | D1 | kapı açılma isteği | D2 | — |
| 2 | D2 | pazarlık başarılı | D3 | — |
| 3 | D2 | pazarlık başarısız | D5 | hata kodu asıl olarak saklanır (§9) |
| 4 | D3 | akış başlatma | D4 | engine niyeti |
| 5 | D4 | sürücü hatası | D5 | diagnostics olayı |
| 6 | D4 | cihaz kaybı | D6 | — |
| 7 | D6 | zincirde sonraki kapı | D9 | sıra device R4 ⚠️ |
| 8 | D6 | kapı yok | D10 | rapor |
| 9 | D9 | yeni kapı açıldı | D2 | — |
| 10 | D5 | yeniden dene | D2 veya D8 | — |
| 11 | D4 | kapatma isteği | D7 | — |
| 12 | D7 | serbest bırakma tamam | D1 | — |
| 13 | D10 | kullanıcı yeniden dener | D2 | — |
| 14 | D8 | başarı | D3/D4 | — |

## §7 IRP/akış şablonları

> ⚠️ **VERIFICATION REQUIRED:** "IRP" (I/O Request Packet) ve Windows sürücüsü
> çağrı akışı model hafızasıdır; resmi WDK/MS dokümanı bu oturumda fetch
> **edilmemiştir**. Şablonlar kavramsal akıştır; major function kodu gibi
> ayrıntılar **yazılmaz/teyit kapıdadır**. Doğrulanmış aşamalı akış kanıtı
> ALSA tarafındadır (§6.1 notu).

### §7.1 Kavramsal sürücü istek akışı (platform bağımsız)

| # | Adım | İstek yönü | Yanıt | Hata yolunun sahibi |
|---|---|---|---|---|
| 1 | kapı aç | uygulama → sürücü | tutamaç / başarısız | driver |
| 2 | cihaz bilgisi iste | uygulama → sürücü | nitelikler (enum) | driver |
| 3 | format yapılandır | uygulama → sürücü | kabul / ret | driver |
| 4 | buffer yapılandır | uygulama → sürücü | kabul / ret | driver + backend (R4) |
| 5 | kip yapılandır | uygulama → sürücü | kabul / ret | driver |
| 6 | akışı başlat | uygulama → sürücü | akış | driver |
| 7 | veri gönder/al | sürücü ↔ uygulama | tampon | realtime (kurallar) |
| 8 | durum bildirimi | sürücü → uygulama | olay | diagnostics |
| 9 | hata bildirimi | sürücü → uygulama | kod | diagnostics (§3 D4) |
| 10 | akışı durdur | uygulama → sürücü | onay | driver |
| 11 | yapılandırmayı serbest bırak | uygulama → sürücü | onay | driver |
| 12 | kapıyı kapat | uygulama → sürücü | onay | driver |

### §7.2 Aşamalı akış — doğrulanmış kanıtla kilitli

| # | Aşama | Doğrulanmış kaynak | Kural | Durum |
|---|---|---|---|---|
| 1 | donanım params (format/rate/channels/buffer) | ALSA hw_params (§2 satır 2) | önce pazarlık | ✅ fetch edilmiş URL |
| 2 | yazılım params (avail_min/period_event) | ALSA sw_params (§2 satır 2) | sonra akış ayarı | ✅ fetch edilmiş URL |
| 3 | beş erişim kipi (MMAP/RW, interleaved/non) | ALSA access (§3 ornekler §2 satır 3) | erişim kipi seçim | ✅ fetch edilmiş URL |
| 4 | plug dönüşümü (format/channels/rate) | ALSA plug (§2 satır 4) | uyuşmazlık sürücüde çözülür | ✅ fetch edilmiş URL |
| 5 | exclusive/shared akış kurulumu | MS exclusive-mode-streams (§2 satır 4) | kip seçimi | ✅ fetch edilmiş URL |
| 6 | sürücü node zamanlaması | PipeWire scheduling (driver-ornekler §2 satır 5) | döngü sürücü tarafında | ✅ fetch edilmiş URL |
| 7 | Windows IRP major kodları | — | — | ⚠️ teyit kapıda |
| 8 | ASIO aç/kapa/yaz sırası | ASIO SDK kataloğu (backend/asio) | komşu derin-dal | ⚠️ bu dosyada tekrarlanmaz |

### §7.3 Yazma/okuma akış şablonu (akış açıldıktan sonra)

| # | Adım | Playback | Capture | Kural |
|---|---|---|---|---|
| 1 | tampon talebi | sürücü ister | sürücü verir | pull kanıtı (§4 komşular) |
| 2 | doldurma | engine zinciri | input kaynağı | sahiplik Q5 açık |
| 3 | teslim | yazılır | okunur | realtime kuralları |
| 4 | tampon bitişi | bir sonraki tik | bir sonraki tik | monoton tik (§4 engine A4) |
| 5 | eksik/taşma | xrun bildirir | xrun bildirir | diagnostics |
| 6 | kapatma | sustur → bırak | bırak | click/DC niyeti |

### §7.4 Hata yanıt şablonu

| # | Hata durumu | Sürücü yanıtı | Üst katmana iletim | Sahip |
|---|---|---|---|---|
| 1 | yapılandırma ret | ret + asıl kod | çeviri (§2 satır 3) | driver |
| 2 | cihaz bulunamadı | durum kodu | envanter (device) | driver → device |
| 3 | kip reddi | durum kodu | kip kararı (R1) | driver |
| 4 | çalışma anı hata | bildirim | sınıf atama (diagnostics) | driver → diagnostics |
| 5 | cihaz kaybı | güvenli bildirim | çekme niyeti (§2 satır 4) | driver → device |
| 6 | zaman aşımı | bildirim | kapı/teşhis | driver |
| 7 | bilinmeyen kod | kod aynen korunur | "bilinmeyen" sınıfı | diagnostics |
| 8 | tekrarlayan hata | sayaç | teşhis (§8 hata modları) | diagnostics |

## §8 Sürücü ↔ OS ve sürücü ↔ uygulama sınır tabloları

### §8.1 Sürücü ↔ OS (platform) sınırı

| # | Konu | OS/WDK'nın işi | Bu dalın işi | Sınır notu |
|---|---|---|---|---|
| 1 | sürücü yüklenmesi | ✅ | ❌ | K000 (§3 D6) |
| 2 | cihaz kimliği veren | ✅ platform | okur ve taşır | K000 |
| 3 | sürücü çağrısı | ✅ API sunar | çağırır | bu dal |
| 4 | hata kodu üretimi | ✅ | yorumlar (§2 satır 3) | bu dal |
| 5 | kaynak/izin yönetimi | ✅ | ❌ | K000 |
| 6 | zamanlama (RT öncelik) | ✅ OS politikası | niyet | realtime komşu |
| 7 | güç yönetimi | ✅ | bildirim alır | — |
| 8 | sürücü sürümü | ✅ | envanter bilgisi (device) | komşu |
| 9 | register/firmware yazımı | ❌ (kullanım modu) | ❌ | K005 (§3 D5) |
| 10 | kernel modülü kodu | ❌ bu dalda değil | ❌ | K000 · K005 |

### §8.2 Sürücü ↔ uygulama (motor) sınırı

| # | Konu | Motor (engine) | driver | Kilit |
|---|---|---|---|---|
| 1 | ne çalıştırılacağı | ✅ niyet | yürütür | §3 D3 · master §3 1/3 |
| 2 | ne zaman başlatılacağı | ✅ karar | uygular | — |
| 3 | buffer sahipliği | ⚠️ soru | ortak sınır | §5 R4 (Q5) |
| 4 | periyot boyutu | hedef (512/48) | pazarlık yürütür | R4/Q5 |
| 5 | hata kodunun yorumu | okur | üretir | §2 satır 3 |
| 6 | fallback politikası | ⚠️ backend/device | yürütür (§2 satır 5) | R1 |
| 7 | kip seçimi | niyet | uygular | R1 |
| 8 | format dönüşümü | istemez | yapabilir (plug) | §7.2 satır 4 |
| 9 | gecikme ölçümü | ❌ | ❌ | diagnostics · testing |
| 10 | log yazımı | ❌ | ❌ | diagnostics |

### §8.3 driver ↔ device ayrım tablosu (Q6 girdisi)

| # | Soru | driver cevabı | device cevabı | Etiket |
|---|---|---|---|---|
| 1 | hangi cihaz? | hayır (envanter değil) | evet | §3 D2 |
| 2 | cihazla nasıl konuşulur? | evet | hayır | §3 D2 |
| 3 | cihaz düştüğünde ne olur (kod)? | bildirir | karar verir (zincir) | ⚠️ R4 device |
| 4 | hangi kapı (ASIO/WASAPI)? | evet (R1) | hayır | ⚠️ R1 |
| 5 | envanter alanları? | niteliği sağlar | listeler (R2) | SINIR AÇIK |
| 6 | geri düşme zinciri sırası? | yürütür | değerlendirir | ⚠️ iki taraf da açık |
| 7 | kip kimde? | uygulama | envanter bilgisi | ⚠️ R1 |

**Bu tablo Q6'ya CEVAP DEĞİLDİR** — yalnız sınırın iki yakasını gösterir;
karar Vault Steward (§5 R1) üzerindedir.

## §9 Yaygın hata modları (failure modes)

| # | Hata modu | Belirti | Olası kök neden (aday) | İlk tepki | Sınıf (diagnostics) |
|---|---|---|---|---|---|
| 1 | kapı açılamıyor | akış başlamıyor | cihaz yok / kip reddi / sürücü eksik | kodu sakla + bildir | H2/H3 |
| 2 | format ret ediliyor | pazarlık başarısız | küme uyuşmazlığı | dönüşüm yolu | H4 |
| 3 | anlık cihaz kaybı | akış kesildi | donanım/USB/sürücü | güvenli çekme + zincir | H1 |
| 4 | tekrarlayan dropout | ses kesik | buffer/periyot uyumsuzluğu | ölç | H6 |
| 5 | xrun seli | taşma | RT öncelik/ CPU | ölç + R2 girdisi | H5 |
| 6 | exclusive kilitlenmesi | diğer uygulama açamıyor | kip sahipliği | kip kararı (R1) | H3 |
| 7 | sürücü çökmesi | mavi/kesinti | sürücü hatası | rapor + geri düşme | H10 |
| 8 | yanlış kanal çıkışı | hoparlör yanlış | maske eşleşmedi | routing kararı | — (routing) |
| 9 | gecikme artışı | bütçe aşımı | buffer büyüdü | ölç | H8 |
| 10 | clock kayması | klik/drift | çift clock | synchronization | H11 |
| 11 | fallback döngüsü | kapılar dönüyor | zincir tasarımı yok | zincir R4 ⚠️ | H1/H9 |
| 12 | bilinmeyen kod | teşhis edilemiyor | yeni sürücü/OS | "bilinmeyen" sınıfı | — |
| 13 | açılış yavaştır | L2/L4 uzun sürüyor | enumerasyon yavaş | ölç (device R6) | H14 |
| 14 | kapanışta ses patlaması | click/DC | susturma yok | kapatma şablonu (§7.3 satır 6) | H12 komşu |
| 15 | sürüm değişince bozulma | L3 sonrası | envanter bayatladı | yeniden tarama (T6) | H4 |
| 16 | çift uygulama yarışı | T7 çakışması | sahiplik kontrolsüz | F17 + R1 | H3 |

### §9.1 Hata modu → kapı eşlemesi

| # | Hata modu | Hangi soruya girdi | Kapı |
|---|---|---|---|
| 1–2 | açma/format | driver R1/R3 | KAPALI |
| 3–5, 9 | kayıp/dropout/xrun/gecikme | diagnostics R2 · device R4 | KAPALI |
| 6, 16 | kip/çift uygulama | driver R1 | KAPALI |
| 7 | sürücü çökmesi | K000/K005 komşu | — |
| 8 | kanal eşleşmesi | routing komşu | — |
| 10 | clock | synchronization komşu | — |
| 11 | fallback döngüsü | device R4 | KAPALI |
| 12 | bilinmeyen kod | diagnostics R3 | KAPALI |
| 13 | açılış süresi | device R6 | KAPALI |
| 14 | kapatma patlaması | mixing R6 · realtime | KAPALI |
| 15 | sürüm değişimi | device R6 | KAPALI |

## §10 Platform kapı karşılaştırması (R1'in girdisi)

> Kaynaklar bu dosya §4'teki doğrulanmış satırlardır (ASIO SDK, WASAPI
> exclusive-mode-streams, ALSA hw/plug, PipeWire scheduling). Tablo **karar
> değil, karşılaştırma**dır.

| # | Boyut | ASIO | WASAPI (exclusive) | ALSA | PipeWire |
|---|---|---|---|---|---|
| 1 | düşük gecikme adayı | ✅ aday (§4 #3) | ✅ aday (§4 #4) | — | — |
| 2 | kip modeli | özel/tekil | exclusive/shared | — | — |
| 3 | Linux yüzeyi | ❌ | ❌ | ✅ ana | ✅ ana |
| 4 | Windows yüzeyi | ✅ | ✅ | ❌ | ⚠️ port (bilgi yok) |
| 5 | aşama sırası | SDK akışı (komşu) | kurulum akışı | hw→sw (✅ §4 #5) | node/link |
| 6 | dönüşüm imkânı | sınırlı | sistem düzeneği | plug (✅ §4 #6) | graph |
| 7 | zamanlama sahipliği | sürücü | OS | uygulama/OS | driver node (§4) |
| 8 | lisans riski | GPLv3 (§5 R2 ⚠️) | — | — | — |
| 9 | CoreMusic durumu | aday | aday | hedef (Linux) | hedef (Linux) |
| 10 | kapı kararı | — | — | — | R1 AÇIK |

## §11 Uygulama kapıları (hepsi KAPALI)

| # | Kapı | Soru | Sahip | Durum |
|---|---|---|---|---|
| 1 | R1 | ASIO mu WASAPI mi? | embedded-engineer | KAPALI |
| 2 | R2 | GPLv3 etkisi? | legal-advisor | KAPALI |
| 3 | R3 | SDK 2.3.4 uyumu? | embedded-engineer | KAPALI |
| 4 | R4 | buffer sahipliği? | Vault Steward | KAPALI |
| 5 | R5 | gecikme rapor birimi? | diagnostics | KAPALI |
| 6 | R6 | NAudio+ASIO thread? | researcher | KAPALI |
| 7 | IRP teyidi | WDK/MS dokümanı fetch | sonraki tur | KAPALI (§7 ⚠️) |
| 8 | IRP kodu | major function kod listesi | sonraki tur | KAPALI (yazılmadı) |
| 9 | ölçüm | sürücü gecikmesi ölçümü | diagnostics | KAPALI (ölçüm yok) |
| 10 | zincir | fallback kapı sırası | device R4 | KAPALI |

## §12 Kapsam dışı taşma denetimi

| # | Eklenen bölüm | Denetim | Sonuç |
|---|---|---|---|
| 1 | §6 | kapı seçimi kararlaştırıldı mı? | ✅ HAYIR — R1 açık |
| 2 | §6.1 L5 | buffer sahibi belirtildi mi? | ✅ HAYIR — R4 açık + sartname niyeti |
| 3 | §7.1 | IRP kodu yazıldı mı? | ✅ HAYIR — kavramsal 12 adım |
| 4 | §7.2 | doğrulanmayan URL kanıt yapıldı mı? | ✅ HAYIR — satır 7/8 ⚠️ |
| 5 | §7.3 | sahiplik kararı verildi mi? | ✅ HAYIR — Q5 açık |
| 6 | §8 | sınır değiştirildi mi? | ✅ HAYIR — mevcut D1–D6 |
| 7 | §9 | kök neden kesin yazıldı mı? | ✅ HAYIR — "aday" sütunu |
| 8 | §9 | sayısal eşik yazıldı mı? | ✅ HAYIR |
| 9 | §10 | kapı kararı verildi mi? | ✅ HAYIR — tablo son satır R1 |
| 10 | §11 | kapı "geçti" mi? | ✅ HAYIR — 10/10 KAPALI |
| 11 | genel | §1–§5 değişikliği | ✅ hayır |
| 12 | genel | yasaklı yer tutucu / derlenebilir kod | ✅ yok (yalnız §7.3/§10 akış dili) |

## §13 Atıf tablosu

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §6.1 | §1 (tek kapı) + ALSA aşama kanıtı | iç kanıt + URL |
| 2 | §6.2/§6.3 | §5 R1/R4/R5 + device §7 şeması | açık soru + komşu |
| 3 | §7.1 | — | ⚠️ kavramsal (teyit kapıda) |
| 4 | §7.2 satır 1–6 | §4 #4/#5/#6/#7 + ornekler §2 | ✅ fetch edilmiş URL |
| 5 | §7.2 satır 7 | — | ⚠️ VERIFICATION REQUIRED |
| 6 | §7.2 satır 8 | backend/asio kataloğu (§4 #8) | komşu derin-dal |
| 7 | §7.3 | pull kanıtı (engine §4 A1–A4) | ✅ dosya okundu |
| 8 | §8.1 satır 10 | §3 D5/D6 (K000/K005) | ✅ master §2.2 |
| 9 | §8.2 satır 3 | §5 R4 (Q5) | ⚠️ açık |
| 10 | §8.3 | §5 R1 + device §5 R1 | ⚠️ açık (Q6) |
| 11 | §9 | §3 D4 + diagnostics H-sınıfları | komşu şema |
| 12 | §10 | §4 #3/#4/#5/#6/#7 | ✅ iç kanıt |
| 13 | §11 | §5 R1–R6 | açık soru |
| 14 | §12/§13 | §1–§5 | iç kanıt |
| 15 | fallback zinciri | `.ai/AGENTS.md` §17 #6 | ✅ iç kanıt (tek kural) |
| 16 | repo sürücü kodu yok | §4 #9 (glob 0) | ölçüm |
| 17 | SDK 2.3.4 teyidi | §4 #10 | ⚠️ açık |

---

## §14 Kip (exclusive/shared) karar etkisi — R1 girdisi

> Bu bölüm **karar vermez**; R1 (ASIO mu WASAPI mi) açıktır. Tablo yalnızca
> kip seçiminin hangi alanları etkileyeceğini gösterir; "hangi kip" satırı
> hiçbir yerde doldurulmaz.

| # | Alan | Exclusive etkisi | Shared etkisi | R1 bağlantısı |
|---|---|---|---|---|
| 1 | başka uygulama erişimi | engellenir | paylaşılır | §9 satır 6/16 |
| 2 | format pazarlığı | doğrudan donanım | sistem düzeneği araya girer | §7.2 satır 4–5 |
| 3 | dönüşüm (plug) ihtiyacı | genelde yok | sistem üstlenir | §7.2 satır 4 |
| 4 | gecikme yolu | daha kısa aday | ara katman ekler | §9 satır 9 |
| 5 | cihaz kaybı tepkisi | aynı şablon (§6 L9) | aynı şablon | — |
| 6 | fallback davranışı | kapı değişir, kip yeniden sorulur | aynı | §6.3 satır 7/9 |
| 7 | hata yüzeyi | sürücü kodu doğrudan gelir | sistem çevirisinden geçebilir | §2 satır 3 |
| 8 | kilidi kim kırar | — | — | ⚠️ R1 AÇIK |

### §14.1 Kip değişimi akışı (yazılmaz — şablon)

```text
L6 kip kararı
  → R1 kapısı AÇIK
    → exclusive seçildi (örnek senaryo, KARAR DEĞİL)
      → paylaşımlı erişim reddedilirse beklenti: diğer uygulama açılamaz (§9 satır 6)
    → shared seçildi (örnek senaryo, KARAR DEĞİL)
      → ara katman beklentisi: dönüşüm sistemde (§7.2 satır 4)
  → her iki yol da L7'ye varır; kip kararı L2/L4'ü geri tetikleyebilir
    → geri tetikleme durumunda durum şeması: D3 → D2 (§6.3)
```

**Yasak:** bu akışta "exclusive tercih edilir" ya da "shared yeterlidir" gibi
**değer yargısı yazılmaz** — yargı R1 sahibine (embedded-engineer) aittir.

## §15 Buffer/periyot bağlama şeması (L5 — R4/Q5 açık)

> Sartname hedefi 512 örnek / 48 kHz'tir (`sartname`; ölçüm PLANNED). Burada
> yalnız **bağlama sırası** şablonlanır; **sahiplik** (backend mi driver mı)
> §5 R4 ile açıktır.

### §15.1 Bağlama sırası şablonu

| # | Adım | Aday veri | Sahip | Durum |
|---|---|---|---|---|
| 1 | hedef ilanı | 512 örnek / 48 kHz (sartname) | engine niyeti | hedef |
| 2 | destek kümesi okuma | sürücünün sunduğu küme | driver | §7.2 |
| 3 | kesişim | hedef ∩ destek | driver yürütür | şablon |
| 4 | uzlaşma | hedef desteklenmiyorsa | ⚠️ kim karar verir? | R4/Q5 AÇIK |
| 5 | bağlama | seçilen değer | driver | şablon |
| 6 | geri bildirim | bağlanan değer | engine'e iletilir | şablon |
| 7 | ölçüm | gerçek periyot süresi | diagnostics | KAPALI (ölçüm yok) |

### §15.2 Uzlaşma senaryoları (etiketli — sonuç yazılmaz)

| # | Senaryo | Olası yol (aday) | Karar sahibi | Durum |
|---|---|---|---|---|
| 1 | 512 destekleniyor | doğrudan bağla | — | en basit yol |
| 2 | 512 desteklenmiyor, 256 var | — | ⚠️ R4/Q5 | AÇIK |
| 3 | 48 kHz desteklenmiyor | dönüşüm mü, ret mi? | ⚠️ R4 + §7.2 satır 4 | AÇIK |
| 4 | exclusive altında dar küme | — | ⚠️ R1 + R4 | AÇIK |
| 5 | shared altında geniş küme | sistem düzeneği | §7.2 satır 5 | şablon |

**Kural:** hiçbir senaryoda sayısal sonuç (ör. "256'ya düşürülür") yazılmaz;
sayı yalnız sartname hedefi ve bilinen ALSA kesişim örneği olarak kalır.

## §16 Otomatik kurtarma (reconnect) şablonu

> §6'daki L10/L11/L12 yaşam döngüsüyle uyumludur; bu bölüm **tekrar deneme
> davranışının şablonudur**, politika/karar değil.

### §16.1 Kurtarma akışı

```text
D5/D6 (hata veya cihaz kaybı)
  → kod saklanır (§7.4 satır 1/7)
    → tekrar deneme sayacı: SINIR BELİRSİZ — sayı uydurulmaz (UNKNOWN)
      → D8 yeniden açma (§6.3 satır 10)
        → başarı → D3/D4
        → başarısız → D6'ya veya zincire (device R4 sırası)
          → kapı tükendi → D10 (manuel)
```

### §16.2 Kurtarma tablosu

| # | Aşama | Ne yapılır | Asla yapılmaz | Sahip |
|---|---|---|---|---|
| 1 | hata saklama | asıl kod korunur | kod yorumlanıp ezilmez | driver |
| 2 | kısa bekleme | — | süre yazılmaz (UNKNOWN) | realtime komşu |
| 3 | yeniden açma (L11) | L2'den tekrar | eski tutamaç kullanılmaz | driver |
| 4 | başarısızlık sayımı | sayaç tutulur | eşik sayısal yazılmaz | diagnostics |
| 5 | zincir adımı | sıradaki kapı | sıra uydurulmaz | device R4 ⚠️ |
| 6 | kullanıcıya dönüş | D10 raporu | sessizce kapanmaz | UI komşu |
| 7 | log | — | bu dalda yazmaz (§8.2 satır 10) | diagnostics |

## §17 Taşma denetimi eki (§14–§16)

| # | Eklenen bölüm | Denetim | Sonuç |
|---|---|---|---|
| 1 | §14 | kip kararı verildi mi? | ✅ HAYIR — R1 açık, yalnız etki tablosu |
| 2 | §14.1 | "exclusive tercih" yazıldı mı? | ✅ HAYIR — örnek senaryo etiketi |
| 3 | §15 | buffer sahibi atandı mı? | ✅ HAYIR — R4/Q5 açık |
| 4 | §15.2 | uzlaşma sonucu sayısal mı? | ✅ HAYIR — sonuç yazılmadı |
| 5 | §16 | tekrar denme eşiği/ süresi yazıldı mı? | ✅ HAYIR — UNKNOWN |
| 6 | §16 | fallback sırası uyduruldu mu? | ✅ HAYIR — device R4 ⚠️ |
| 7 | §14–§16 | §1–§5 değişikliği | ✅ hayır |
| 8 | genel | derlenebilir kod iddiası | ✅ yok (yalnız ```text akış) |
| 9 | genel | yasaklı yer tutucu kalıbı | ✅ kullanılmadı |

## §18 Atıf tablosu (§14–§16)

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §14 | §10 kip satırları + §9 satır 6/16 | iç kanıt |
| 2 | §14.1 | §6.3 durum geçişleri | iç kanıt |
| 3 | §15.1 | sartname hedefi (512/48) + §7.2 | hedef + iç kanıt |
| 4 | §15.2 | §5 R4/Q5 + §7.2 satır 4–5 | açık soru + URL kanıtı |
| 5 | §16.1 | §6.3 satır 10 + `.ai/AGENTS.md` §17 #6 | iç kanıt |
| 6 | §16.2 | §8.2 satır 10 (log bu dalda değil) | iç kanıt |
| 7 | §17 | §12 tekniğinin devamı | iç kanıt |

### §18.1 Kapı/kod teyit listesi (§14–§19 ekleri için)

| # | Teyit bekleyen | Bölüm | Bağlantı | Durum |
|---|---|---|---|---|
| 1 | WDK/MS IRP dokümanı fetch | §7 ⚠️ | §11 kapı 7/8 | KAPALI |
| 2 | ASIO SDK 2.3.4 bağımsız teyidi | §4 #10 | §5 R3 | KAPALI |
| 3 | GPLv3 lisans etkisi | §10 satır 8 | §5 R2 | KAPALI |
| 4 | buffer sahipliği kararı | §15.1 satır 4 | §5 R4 (Q5) | KAPALI |
| 5 | uzlaşma yolu seçimi | §15.2 | §5 R4 | KAPALI |
| 6 | tekrar denme eşiği/süresi | §16.1/§16.2 | — | KAPALI (UNKNOWN) |
| 7 | fallback kapı sırası | §16.2 satır 5 | device R4 | KAPALI |
| 8 | sürücü gecikme ölçümü | §15.1 satır 7 | §5 R5 · diagnostics | KAPALI |
| 9 | kip kararı (exclusive/shared) | §14 satır 8 | §5 R1 | KAPALI |
| 10 | PipeWire Windows port bilgisi | §10 satır 4 | — | KAPALI (bilgi yok) |

**Toplam kapı: 10/10 KAPALI** — bu ek bölümlerde hiçbir kapı geçmez; §14–§16
yalnız mevcut açık soruların **girdi tablolarını** genişletmiştir.

---

## §19 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
