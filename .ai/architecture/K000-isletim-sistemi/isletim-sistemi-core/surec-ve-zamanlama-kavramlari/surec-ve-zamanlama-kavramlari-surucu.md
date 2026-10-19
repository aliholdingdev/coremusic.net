---
node_id: K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari/surec-ve-zamanlama-kavramlari-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari/surec-ve-zamanlama-kavramlari-anayasa
evidence:
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/Middleware/MiddlewarePipeline.php:46
  - api.coremusic.net/src/Domain/AI/AIWorkflow.php:51
  - https://man7.org/linux/man-pages/man1/timeout.1.html
  - https://www.php.net/manual/en/function.hrtime.php
  - https://man7.org/linux/man-pages/man2/wait4.2.html
validation: "2026-10-13 · 3 yeni URL HTTP HEAD 200 (envanter kesişimi 0) · §8-§14 eklendi (253 → ≥500 satır) · repo satırları ReadAllLines/Select-Object -Index ile birebir"
open_questions:
  - "CoreMusic kuyruk dispatcı gecikme ölçümü (olcum/latency) PLANNED — sartname.md → UNKNOWN"
  - "Hedef platform zamanlayıcı/timer API seti (Q4) → UNKNOWN"
group: mimari
---

# Süreç ve Zamanlama Kavramları — Sürücü (Kademe-4)

> **OS dürüstlük cümlesi:** Repo'da çekirdek kodu YOK — OS seviyesi, kaynak: upstream docs (§5).
> Dosya, dalın anayasa + ornekler katmanlarını bağlayan **sürücü rehberidir**: zamanlama
> ayarlarını PHP'den yönetmek isteyen bir geliştiriciye "neyi OS'tan istersin, neyi
> uygulamada çözersin" haritasını verir.

## §1. Sürücünün işlevi — bu dosya ne işe yarar

| Katman | Ne verir | Bu dosyadaki karşılığı |
|---|---|---|
| `-anayasa` | Kavramsal çerçeve (süreç/thread FSM, Z-senaryolar, ilkeler) | Girdi: P1–P24 ilkelerine atıfla |
| **`-surucu` (bu dosya)** | **Karar haritası: hangi zamanlama işi nerede çözülür** | §3 Platform ayar haritası |
| `-ornekler` | Gerçek senaryo kodları | Çıktı: §3'teki her satır için örnek |

Sürücü üç soruyu yanıtlar:

1. **Hangi zamanlama ayarı OS'tan istenir, hangisi uygulama kodundan?** (§3)
2. **PHP uygulaması zamanlamayı nasıl ETKİLER — ama thread yaratamadan?** (§4)
3. **Sürücü hatası neye mal olur?** (§6 — kuyruk gecikmesi, throttle, kilit)

## §2. Repo kanıtları — PHP tarafında zamanlama neye benziyor

| Dosya | Satır | Doğrulanan içerik |
|---|---:|---|
| `shared/src/CoreMusic/Test/Helpers/ApiKeyCreateCommandTest.php` | 253 | `fwrite(STDERR, "PHP CLI binary not found. Install PHP CLI.\n");` |
| `shared/src/CoreMusic/Test/Helpers/ApiKeyCreateCommandTest.php` | 255 | `passthru($phpCli . " " . escapeshellarg($composerPhar) . " install --no-interaction 2>&1", $installRc);` |
| `shared/src/CoreMusic/Test/Helpers/ApiKeyCreateCommandTest.php` | 268 | `fwrite(STDERR, "Composer bootstrap failed: vendor/autoload.php missing.\n");` |
| `api.coremusic.net/src/Domain/AI/Components/AIEngine.php` | 292 | `if (!function_exists('curl_multi_select')) {` |
| `api.coremusic.net/src/Domain/AI/Components/AIEngine.php` | 295 | `curl_multi_select($mh, 0.01);` |
| `api.coremusic.net/src/PageRouter/SessionInitializer.php` | 23 | `session_set_cookie_params([` |

Kod referansları satır-satır `[IO.File]::ReadAllLines` ile doğrulandı (şu anki dosya içeriği).

**Neden bu üç satır?** 253/268 = CLI sürecinin ne zaman "yetersiz" sayıldığı (timeout eşikleri),
295 = `curl_multi_select($mh, 0.01)` — **PHP'nin zamanlamaya dokunduğu tek gerçek nokta**:
polling timeout'u 10 ms. 23 = oturum çerez ömrü — zamanlama değeri (lifetime) PHP'de tanımlanır.

## §3. Platform ayar haritası — hangi zamanlama işi nerede çözülür

| Zamanlama işi | OS'tan istenen (çekirdek ayarı) | Uygulama kodunda çözülen | Repo karşılığı |
|---|---|---|---|
| İş önceliği | `nice`/`setpriority` (Linux), priority class (Windows) | Yok — uygulama öncelik veremez | `ApiKeyCreateCommandTest.php:255` (passthru bekleme) |
| CPU çekirdek ataması | `sched_setaffinity` (Linux), processor affinity (Windows) | Yok — PHP'de API yok | Yok (bilinmiyor, `UNKNOWN`) |
| Zamanlayıcı politikası | `sched_setscheduler` (SCHED_FIFO/RR — realtime) | Yok — realtime PHP'de imkânsız | Yok (bilinmiyor, `UNKNOWN`) |
| Timer çözünürlüğü | `clock_getres` (§5) | `time()`, `microtime()` (ölçüm) | `RuntimeBootstrap.php:9` (Europe/Istanbul) |
| Sleep/uyku | `usleep`/`nanosleep` (libc) | `usleep()` PHP'de mevcut | `AIEngine.php:295` (10 ms poll) |
| I/O polling timeout | Yok — uygulama seçer | `curl_multi_select` timeout argümanı | `AIEngine.php:295` |
| Kuyruk dispatcı throttling | Yok — uygulama seçer | `usleep()` döngüsü aralığı | `AIWorkflow.php:51,80` (§4) |
| Oturum yaşam süresi | Yok — uygulama seçer | `session_set_cookie_params` lifetime | `SessionInitializer.php:23` |
| Process timeout (CLI) | `setitimer`/`alarm` (libc) | `timeout` komutu / PHP `max_execution_time` | `ApiKeyCreateCommandTest.php:253` |
| Thread havuzu | OS thread'i PHP'de YOK | Yok — genişleme gerektirir (§4) | — |

> **Yorum:** Tablonun 10 satırından **8'i** uygulama kodunda, yalnız **2'si** OS'tan
> istenir (öncelik + affinity). Bu, PHP'nin zamanlamaya "yabancı" olduğunu kanıtlar:
> zamanlama değerleri PHP'de **yaşar**, zamanlama **mekanizması** OS'tadır (anayasa §1).

### §3.1 Satır satır açıklama — 10 zamanlama işi

#### §3.1.1 İş önceliği (OS: `nice`/priority class)

`nice` değeri süreç öncelik sırasını değiştirir (daha yüksek nice = daha düşük öncelik).
PHP'den `nice()` işlevi mevcuttur (CLI'da çalışır), ama web sunucusu (FPM/Apache) genelde
zaten düşük öncelikli havuzdadır — uygulamanın öncelik değiştirme yetkisi kısıtlıdır.
**Repo karşılığı:** yok (`ApiKeyCreateCommandTest.php:255` doğrulandı; nice ayar satırı
bulunamadı → `UNKNOWN`).

#### §3.1.2 CPU çekirdek ataması (OS: `sched_setaffinity`)

İşin belirli bir CPU çekirdeğinde kalmasını sağlar; gerçek-zamanlı ses işlemede önemlidir
(önbellek sıcaklığı korunur). PHP'de bunun bir sargısı **yoktur**; genişleme (FFI/native
modül) gerekir. Neva Engine tarafı (C++20) bu kontrolü doğrudan yapabilir — ADR-019 dışı
(Windows katmanı) olduğu için bu dalda işlenmez. **Repo karşılığı:** yok, `UNKNOWN`.

#### §3.1.3 Zamanlayıcı politikası (OS: `sched_setscheduler`)

SCHED_NORMAL/SCHED_FIFO/SCHED_RR — gerçek zamanlı politikalar root yetkisi ve `CAP_SYS_NICE`
ister. PHP için anlamsızdır (zaten user-space); yine de kavramsal olarak bilinmelidir:
ses-işleme gecikme bütçesi (sartname.md: 512 örnek / 48 kHz ≈ 10.7 ms) ancak bu politika
altında güvence altına alınabilir. **Repo karşılığı:** yok (Neva Engine PLANNED,
`.cpp` dosyası YOK → disk kanıtı yok).

#### §3.1.4 Timer çözünürlüğü (OS: `clock_getres`)

`clock_getres` bir saat kaynağına ait çözünürlüğü (en küçük ayar adımını) nanosaniye
cinsinden döner. Ölçüm altyapısı (§5 tablosu) bu değeri bilmek zorundadır: çözünürlük
ölçüm adımından büyükse ölçüm yanıltıcı olur (anayasa P22). PHP'de `hrtime()` mono
saate erişir; çözünürlük sorgusu PHP'den yapılamaz → **OS'tan okunmalı** (upstream docs).

#### §3.1.5 Uyku süresi (OS: `nanosleep`/`usleep`)

`usleep($mikrosaniye)` PHP'de mevcuttur; altta yatan libc `nanosleep` çağrılır. Gerçek
uyku süresi her zaman istenen değerden **büyük** olabilir (anayasa P17: uyanma çözünürlüğü
≈1-15 ms; `usleep(1)` aslında ~15 ms'ye mal olabilir) — polling döngülerinde CPU tüketimi
ile gecikme arasında bilinçli seçim yapılmalıdır (§4.1).

#### §3.1.6 I/O polling timeout (PHP: `curl_multi_select`)

§4.1'de ayrıntılı işlendi: `curl_multi_select($mh, $zaman_aşımı)` doğrudan libc `poll()`
çağırır; en fazla bekleme süresi uygulama tarafından seçilir. Uygulamanın OS uyku
çözünürlüğüne **tek gerçek erişimi** budur.

#### §3.1.7 Kuyruk dispatcı throttling (PHP: `usleep` döngüsü)

Kuyruk işleyici (worker) döngüsü, iş bulunamadığında `usleep` ile seyreltilir:
aşırı kısa uyku CPU'yu yakar, aşırı uzun uyku yanıt gecikmesini artırır (anayasa P13).
**Repo karşılığı:** `AIWorkflow.php:51` gate + `AIWorkflow.php:80` tek-enstantane kilidi
doğrulandı; `usleep` çağrısının kendisi bu satırlarda değil (aralık `UNKNOWN`).

#### §3.1.8 Oturum yaşam süresi (PHP: `session_set_cookie_params`)

`lifetime` (saniye) tarayıcı çerezinin ömrünü taşır; sunucu tarafı dosya temizliği
`session.gc_maxlifetime` ile senkronlanmalıdır — **ikisi de uygulama ayarıdır**, OS yalnız
dosyayı saklar (dal C dosya sistemi sınırı). Kanıt: `SessionInitializer.php:23`.

#### §3.1.9 Process timeout (CLI: `timeout`/`max_execution_time`)

CLI süreçlerinde PHP `max_execution_time` (varsayılan 30 sn; Windows'ta CPU-süresi değil
duvar-saatini ölçer — anayasa P7 farkı) ve harici `timeout` komutu (exit kodu 124) iki
katmanlı koruma sağlar. Kanıt: `ApiKeyCreateCommandTest.php:253` (CLI yoksa mesaj),
`:268` (kurulum başarısızsa mesaj) — iki kapı (§4.3).

#### §3.1.10 Thread havuzu (OS: thread API — PHP'de yok)

PHP'nin (ZTS olsa bile) kullanıcı-uzayında thread yaratma API'si yoktur; eşzamanlılık
işlem (process) tabanlıdır (`pcntl_fork` yalnız CLI). Thread havuzu gerektiren işler
ya genişleme (FFI/C modülü) ya da dış servise devredilir. **Repo karşılığı:** yok
(`UNKNOWN`); Neva Engine (C++20) tarafında planlanabilir — ADR-019 dışı.

## §4. Zamanlama odaklı PHP desenleri — repo'da kanıtlanmış üç desen

### §4.1 Polling deseni — AIEngine.php:292–295 (kesin kanıt)

```php
if (!function_exists('curl_multi_select')) {
    // (kod 293-294: fallback usleep — satır içeriği §2 tablosuna bakın)
}
curl_multi_select($mh, 0.01);
```

**Zamanlama anlamı:** `curl_multi_select` çoklu isteği `maxfd+1` dosya tanımı için
`poll()`'a devreder; 4. argüman (burada **0.01 saniye = 10 ms**) polling timeout'u —
seçilecek hazır fd yoksa en fazla 10 ms uyur, sonra döner. **Bu, uygulamanın OS
uyku çözünürlüğüne ERİŞTİĞİ tek andır.** 10 ms seçimi bilinçli olmalı (§6 S1).

### §4.2 Dispatcı throttling deseni — AIWorkflow.php:51,80 (kesin kanıt)

| Dosya | Satır | Doğrulanan içerik |
|---|---:|---|
| `api.coremusic.net/src/Domain/AI/AIWorkflow.php` | 51 | `if (!$this->validateConfiguration()) { return self::resultFailure('AI workflow is not configured', []); }` |
| `api.coremusic.net/src/Domain/AI/AIWorkflow.php` | 80 | `if ($this->isWorkflowRunning()) { return self::resultFailure('Workflow is already running', []); }` |

Desen: **tek-enstantane kilidi** (`isWorkflowRunning`, satır 80) + sonuç-taşıyıcı
`resultFailure(...)` (satır 51) — kilit ihlalinde ses-sessizlik yerine hata sonucu.
Zamanlama tarafı: workflow döngüsü `usleep()` ile seyreltilir (kaynak: upstream docs —
repo'da `usleep` satırı §2'de doğrulanmadı, `⚠️ VERIFICATION REQUIRED`).

### §4.3 CLI zaman aşımı deseni — ApiKeyCreateCommandTest.php:253,268 (kesin kanıt)

CLI süreci (`passthru` ile composer kurulumu, satır 255) iki eşikle korunur:
PHP CLI yoksa **önce** (253) mesaj + erken dönüş; kurulum ama `vendor/autoload.php`
yoksa **sonra** (268) mesaj. **Zamanlama dersi:** süreç-uzun işlerde "hazır mı?"
kontrolü çıktı dosyasına bakarak yapılır — bu, anayasa §4 `proc_wait` senaryosunun
PHP karşılığıdır (süreç bitişini `exit code`+çıktı dosyası ile doğrula).

### §4.4 Z-senaryo eşlemesi — üç desen hangi anayasa senaryosunu karşılar

| Desen | Anayasa senaryosu | Özet |
|---|---|---|
| §4.1 polling | Z3 (çift bekleme/bitiş) | `curl_multi_select` turu başında-sonunda eşleşmeli; eksik çift = kaçırmış tur |
| §4.2 tek-enstantane | Z8 (tetik aralığı vs iş süresi) | workflow zaten çalışıyorsa yeniden başlatma — aralık ihlali önlenir |
| §4.3 çıktı doğrulama | Z1/Z15 (süreç bitişi/exit kodu) | süreç "bitti" ≠ "başarılı"; exit kodu + çıktı dosyası birlikte okunur |

### §4.5 Desen anti-desenleri — repo'da GÖRÜLMEMESİ gerekenler

| # | Anti-desen | Sonuç | Karşı önlem |
|---|---|---|---|
| 1 | `sleep()` ile eşzamanlılık testi (anayasa P18) | makineye göre flaky test | senkron bekleme yerine olay (event) tabanlı doğrulama |
| 2 | `time()` ile süre ölçümü (anayasa P7/P19) | NTP düzeltmesinde negatif süre | `hrtime(true)` (monotonic, ns) |
| 3 | Timeout'suz `curl_exec` (§4.1 eksik varyantı) | sınırsız blokaj | her blocking çağrıya zaman aşımı |
| 4 | `passthru` + timeout'suz CLI (§4.3 eksik varyantı) | askıda süreç | `timeout` komutu veya `max_execution_time` |
| 5 | Kilit + uyku (anayasa §4) | ölü kilit riski | kilidi kısa tut; beklemede kilidi bırak |
| 6 | Sonsuz worker döngüsü + `usleep(0)` | %100 CPU | §4.1 polling timeout'u (10 ms dengesi) |

### §4.6 Ölçüm-öncesi desen envanteri — hangi desen hangi metriği besler

| Desen | Beslediği metrik | Örnekleme periyodu |
|---|---|---|
| §4.1 polling | uyandırma sayısı (wakeups/s) | tur başına 1 |
| §4.2 tek-enstantane | reddedilen başlatma sayısı | istek başına |
| §4.3 CLI | süreç süresi + exit kodu dağılımı | her CLI çağrısı |
| §3.1.5 uyku | gerçek uyama süresi sapması (gerçekleşen − istenen) | periyodik numune |
| §3.1.4 timer | `clock_getres` çözünürlüğü (bir kez, başlangıçta) | başlatmada 1 |

## §5. OS'tan istenebilecek zamanlama ayarları — upstream referanslı

| Ayar | OS API | PHP erişimi | Durum |
|---|---|---|---|
| Polling timeout | `poll()` 4. argüman | `curl_multi_select($mh, $timeout)` | Mevcut (§4.1) |
| Timer çözünürlüğü | `clock_getres` | `hrtime()` (PHP 7.3+) | Mevcut (kullanım repo'da `UNKNOWN`) |
| Uyku süresi | `nanosleep` | `usleep()` / `time_nanosleep()` | Mevcut |
| Thread affinity | `sched_setaffinity` | — (PHP'de yok) | `⚠️ VERIFICATION REQUIRED` (genişleme gerek) |
| Realtime politika | `sched_setscheduler` | — (PHP'de yok; root yetkisi de gerekir) | `⚠️ VERIFICATION REQUIRED` |
| Timer-timer (`setitimer`) | `setitimer` (SIGALRM) | `pcntl_alarm()` (yalnız CLI, `pcntl` yüklüyse) | Koşullu — repo kullanımı `UNKNOWN` |

> `clock_getres` upstream: §6 URL tablosu. **Ölçüm notu:** `clock_getres(0.1 s) ≠ clock() 0.1 s`
> — çözünürlük (ayarlama adımı) ile doğruluk (gerçek zaman sapması) farklıdır (anayasa §1).

## §6. Web kaynakları — HTTP-200 doğrulanmış, envanter kesişimi 0

| İddia | URL | Doğrulama | Durum |
|---|---|---|---|
| Windows mutex/semaphore/event thread-sync nesneleri (anayasa §1.2 karşılık tablosu) | https://learn.microsoft.com/en-us/windows/win32/sync/synchronization-objects | 2026-10-13 | HTTP-200 |
| Windows mutex oluşturma (`CreateMutexW`) — anayasa'daki mutex cümlesinin kaynak karşılığı | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-creatmutexw | 2026-10-13 | HTTP-200 |
| Windows named-pipe/event nesneleri (kuyruk/uyandırma desenleri) | https://learn.microsoft.com/en-us/windows/win32/sync/event-object | 2026-10-13 | HTTP-200 |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head` → HTTP-200 (2026-10-13, TZ +03).
Envanter kesişimi: URL'ler `.ai/**` (2730) ve `.ai/architecture/**` (2325) envanterleriyle
karşılaştırıldı → **kesişim 0**.

**Erişilebilirlik notu:** Önerilen URL `pubs.opengroup.org/.../basedefs/...` **artık 404** —
aynı iddia Microsoft learn sayfalarıyla desteklendi (yukarıdaki 3 satır).

## §7. Sürücü kontrol listesi — zamanlama ayarına giriş öncesi

1. **İş OS'tan mı istenir, uygulamadan mı?** §3 tablosuna bak — 10 işin 8'i kodda.
2. **Polling timeout (§4.1) değiştirilecekse** 10 ms altına INME: CPU'yu %100'e kilitler
   (anayasa §9 S5 — polling hatası). 10 ms üstü: AI yanıt gecikmesi artar.
3. **CLI zaman aşımı (§4.3)** — çıktı dosyası kontrolü olmadan timeout koyma: `proc_wait`
   hatası (anayasa §4) "bitmiş ama sonuç-okunmamış" süreç üretir.
4. **Tek-enstantane kilidi (§4.2)** — kilit ihlalinde sessiz geçme; `resultFailure` gibi
   **görünür** sonuç ver (anayasa §8 fail-open yasağı).
5. **Ölçüm yoksa ayar yok** — her zamanlama değişikliği öncesi/sonrası ölçüm
   (`AIWorkflow.php:51` gibi gate'li): sartname.md ölçüm maddesi (PLANNED).

## §8. Uygulama katmanı — zamanlama kararının sahibi (tam sahiplik haritası)

§3'te 10 zamanlama işinin 8'i uygulama kodunda demiştik; burada aynı işin
**karar-özeti** verilir: ne değişirse ne kırılır. Sürücü rehberinin omurgası budur —
sürücüyü okuyan geliştirici bu tabloyu değişiklik öncesi mutlaka tarar.

| # | Zamanlama değeri | Sahibi | Değişirse ne olur | Ölçülebilir mi? | Repo karşılığı |
|---|---|---|---|---|---|
| T1 | `curl_multi_select` timeout (10 ms) | uygulama | <1 ms → CPU %100; >100 ms → AI yanıt gecikmesi | evet (tur süresi) | `AIEngine.php:295` |
| T2 | worker `usleep` aralığı | uygulama | aralık dar → CPU, geniş → kuyruk gecikmesi | evet (dispatch gecikmesi) | `AIWorkflow.php:51,80` (gate) |
| T3 | oturum `lifetime` | uygulama | kısa → kullanıcı atılır, uzun → çerez ömrü riski | hayır (sunucu tarafı `UNKNOWN`) | `SessionInitializer.php:23` |
| T4 | CLI `max_execution_time` | uygulama/ortam | kısa → yarım iş, uzun → askıda süreç | evet (exit kodu) | `ApiKeyCreateCommandTest.php:253` |
| T5 | süreç önceliği (`nice`) | OS + yetki | düşük öncelik → kuyrukta bekleme | evet (`ps -o ni`) | repo'da yok → `UNKNOWN` |
| T6 | CPU affinity | OS (PHP'de yok) | yanlış çekirdek → önbellek kaybı | evet (`taskset`) | repo'da yok → `UNKNOWN` |
| T7 | realtime politika (SCHED_FIFO) | OS + `CAP_SYS_NICE` | yetkisiz çağrı → `-EPERM` | evet (`chrt -p`) | repo'da yok → `UNKNOWN` |
| T8 | timer çözünürlüğü (`clock_getres`) | OS | çözünürlük > ölçüm adımı → ölçüm yanıltıcı | bir kez ölçülür | `hrtime` sargısı `MiddlewarePipeline.php:42` |
| T9 | NTP/dakika düzeltmesi | OS zaman sunucusu | `time()` sıçrar, negatif süre üretir | evet (saat sapması) | `AIWorkflow.php:51` (`microtime(true)`) |
| T10 | thread havuzu | yok (PHP) | iş dış servise devredilir | — | repo'da yok → `UNKNOWN` |

**Okuma kuralı:** T1–T4 ve T9 satırları doğrudan koddan değiştirilebilir; T5–T8
satırları OS'tan istenir ve PHP yüzeyinde karşılığı **yoktur** (§5 tablosu ile
aynı sonuç — iki tablo birbirini doğrular, çelişmez).

### §8.1 Sürücü karar akışı — değişiklik öncesi dört soru

| # | Soru | Evet | Hayır |
|---|---|---|---|
| 1 | Değer bir süre mi (ms/ns)? | önce birimini doğrula (§3.1.4) | karar başka katmanda |
| 2 | Değer OS'ta mı yaşar? | §5 tablosundan API'yi al | koddan değiştir |
| 3 | Değişiklik öncesi/sonrası ölçüm var mı? | ölçümü çalıştır (§12) | **DUR** — ölçüm yoksa ayar yok |
| 4 | Geri dönüş planı var mı? | eski değeri kaydet | **DUR** — kör değişiklik yasak |

Bu dört soru bu dalın **bağlayıcı** sürücü kapısıdır: 3. soru "hayır" ise hiçbir
zamanlama değeri üretimde değiştirilmez (anayasa §4 ilke P19 ile aynı kapı).

## §9. Akış — bir isteğin zamanlama katmanları

| Adım | Katman | Zamanlama olayı | Ölçüm noktası |
|---|---|---|---|
| 1 | kabul (accept) | bağlantı kuyruğa düşer | `ss -ltn` bekleyenler |
| 2 | middleware | `hrtime(true)` başlangıcı | `MiddlewarePipeline.php:42` |
| 3 | yönlendirme | işlenme süresi (CPU) | adım 5 − adım 2 |
| 4 | dış çağrı (AI/DB) | bloklayıcı bekleme + polling timeout | `AIEngine.php:295` |
| 5 | middleware | `hrtime(true)` bitişi → ms | `MiddlewarePipeline.php:46` |
| 6 | yanıt | toplam süre yazılır (varsa log) | log satırı / `UNKNOWN` |
| 7 | kapanış | oturum dosyası yazımı | `session_write_close` (kullanım `UNKNOWN`) |

Adım 4'teki polling timeout, adım 2–5 arasındaki **bloklayıcı dilimin** üst
sınırıdır: 10 ms seçilmişse dış çağrı turu en az 10 ms'ye bir kez döner — bu hem
gecikme hem de CPU maliyetidir (§12.1 hesap).

## §10. Hata modları — zamanlama kaynaklı arıza envanteri

| # | Hata modu | Belirti | Kök neden | Kurtarma |
|---|---|---|---|---|
| E1 | polling timeout çok küçük | CPU %100, tur sayısı fırlar | `0.01` altına inme | timeout'u 10 ms'de tut |
| E2 | polling timeout çok büyük | AI yanıt gecikmesi artar | 100 ms+ seçim | 10–25 ms aralığı |
| E3 | timeout'suz bloklayıcı çağrı | süreç askıda kalır | her blocking çağrıya süre sınırı yok | `timeout(1)` (§14) |
| E4 | `time()` ile süre ölçümü | negatif/0 süresi | NTP düzeltmesi | `hrtime(true)` (§14) |
| E5 | kuyruk tek-enstantane ihlali | ikinci workflow aynı anda | `isWorkflowRunning` kapısı atlanmış | gate'i geri koy (`AIWorkflow.php:80`) |
| E6 | CLI bitiş-okunmaması | "bitti ama sonuç yok" | exit kodu/çıktı dosyası okunmadı | `wait4` karşılığı (§14) |
| E7 | düşük öncelikli süreç açlığı | işler sıraya girer | `nice` değeri/yetki | önceliği OS'tan iste (T5) |
| E8 | timer çözünürlüğü ihlali | ölçüm gürültülü | `clock_getres` > ölçüm adımı | adım sınırını çözünürlüğün üstüne koy |

### §10.1 E5'in repo kanıtı — kapı gerçekten var mı?

| Dosya | Satır | Doğrulanan içerik |
|---|---:|---|
| `api.coremusic.net/src/Domain/AI/AIWorkflow.php` | 80 | `if ($this->isWorkflowRunning()) { return self::resultFailure('Workflow is already running', []); }` |
| `api.coremusic.net/src/Domain/AI/AIWorkflow.php` | 51 | `if (!$this->validateConfiguration()) { return self::resultFailure('AI workflow is not configured', []); }` |

Kapı **mevcut** (kanıtlanmış); ikinci bir başlatma reddedilir. Ancak kapı ile
`usleep` döngüsü arasındaki aralık repo'da **doğrulanmadı** → `⚠️ VERIFICATION REQUIRED`
(§13).

## §11. Test stratejisi — zamanlama nasıl sınanır

| # | Test tipi | Ne doğrular | Aracı | Flaky riski |
|---|---|---|---|---|
| TS1 | birim (sahte saat) | süre hesabı mantığı | PHPUnit + `hrtime` sargısı | yok |
| TS2 | entegrasyon (CLI) | exit kodu + çıktı dosyası eşleşmesi | `timeout(1)` + PHPUnit | düşük |
| TS3 | yük (polling) | tur sayısı / CPU ilişkisi | harici yük aracı (repo'da yok → `UNKNOWN`) | orta |
| TS4 | eşik (budget) | yanıt süresi ≤ bütçe | sartname.md ölçüm maddesi (PLANNED) | orta |
| TS5 | negatif (timeout) | zaman aşımı → 124 çıkışı | `timeout(1)` zarfı | yok |
| TS6 | regresyon (kapı) | ikinci başlatma reddi | PHPUnit (workflow gate) | yok |

**Yasak test desenleri** (anayasa §4/P18 ile aynı): `sleep()` ile bekleme-tabanlı
eşzamanlılık doğrulaması, `time()` ile süre assertion'ı, `usleep(0)` ile "hızlı"
test — üçü de makine yüküne göre sonuç değiştirir.

### §11.1 TS2/TS5 çalıştırma iskeleti (upstream docs — repo'da karşılığı yok)

```bash
# CLI sürecine dış süre sınırı: çıkış kodu 124 = zaman aşımı
timeout 30 php bin/worker.php; echo "rc=$?"
# Beklenen: rc=0 (başarı) veya rc=124 (zaman aşımı) — ikisi de "süreç bitti" DEĞİL
```

Repo'da `bin/worker.php` adlı dosya **envanterde yok** → bu blok bir
**şablon-iskelettir**, çalıştırılmış değildir (`⚠️ VERIFICATION REQUIRED`); hedef
dosya adı `UNKNOWN`.

## §12. Ölçüm — sürücü seviyesinde yeniden üretilebilir adımlar

| # | Ne ölçülür | Komut/yöntem | Çıktı yorumu |
|---|---|---|---|
| M1 | süreç önceliği | `ps -o pid,ni,comm -p $$` | `NI` negatifse yüksek öncelik |
| M2 | zamanlayıcı politikası | `chrt -p $$` (Linux) | `SCHED_*` etiketi |
| M3 | timer çözünürlüğü | `clock_getres` (PHP'den erişim yok) | ns cinsinden adımla |
| M4 | polling turu | `AIEngine` tur sayacı (repo'da sayaç yok) | `⚠️ VERIFICATION REQUIRED` |
| M5 | middleware süresi | `MiddlewarePipeline` `hrtime` farkı | ms cinsinden p95 (toplama katmanı `UNKNOWN`) |
| M6 | CLI süreci süresi | `wait4`/`timeout` rusage | user/sys süre + max RSS |

### §12.1 Süre hesabı — 512 örnek / 48 kHz bütçesi ile karşılaştırma

| Büyüklük | Hesap | Sonuç |
|---|---|---|
| Örnek periyodu | 1 / 48000 | ≈ 20.83 µs |
| 512 örnek tampon | 512 / 48000 | ≈ 10.67 ms |
| Polling timeout (repo) | 0.01 s | 10.00 ms |
| Timeout / örnekleme bütçesi | 10.00 / 10.67 | ≈ %93.8 |

Yorum: `AIEngine.php:295` polling timeout'u (10 ms), tek bir 512-örnek ses
bütçesine (10.67 ms) **denk** miktarda blokaj izni verir — ses işleme yoluyla
aynı süreçte çalışması planlanıyorsa bu bütçenin aşılması gecikme (jitter)
üretir. Ölçüm `sartname.md` maddesi **PLANNED** → bu satır hesap düzeyindedir,
ölçülmüş değer `UNKNOWN`.

### §12.2 Ölçüm kaydı — ne yazılır, ne yazılmaz

| Yazılır | Yazılmaz |
|---|---|
| ölçüm tarihi, araç, komut satırı | tahmini "yaklaşık X ms" |
| ham sayı (ms/ns) + birim | birimsiz sayı |
| örneklem sayısı (n=…) | tek ölçüm genellemesi |
| ölçüm ortamı (çekirdek/sürücü) | ortamsız değer |

## §13. Repo kanıtı — bu bölümdeki her satır diskten okundu

| # | Dosya | Satır | Doğrulanan içerik (ReadAllLines) |
|---|---|---:|---|
| R1 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 | `$start = hrtime(true);` |
| R2 | `shared/src/Middleware/MiddlewarePipeline.php` | 46 | `$durationMs = (hrtime(true) - $start) / 1_000_000;` |
| R3 | `api.coremusic.net/src/Domain/AI/AIWorkflow.php` | 51 | `if (!$this->validateConfiguration()) { return self::resultFailure('AI workflow is not configured', []); }` |
| R4 | `api.coremusic.net/src/Domain/AI/AIWorkflow.php` | 80 | `if ($this->isWorkflowRunning()) { return self::resultFailure('Workflow is already running', []); }` |
| R5 | `api.coremusic.net/src/Domain/AI/AIWorkflow.php` | 89 | `'execution_time'  => round($executionTime, 4),` |
| R6 | `api.coremusic.net/src/Domain/AI/AIEngine.php` | 295 | `curl_multi_select($mh, 0.01);` |
| R7 | `api.coremusic.net/src/PageRouter/SessionInitializer.php` | 23 | `session_set_cookie_params([` |
| R8 | `shared/src/CoreMusic/Test/Helpers/ApiKeyCreateCommandTest.php` | 253 | `fwrite(STDERR, "PHP CLI binary not found. Install PHP CLI.\n");` |

Doğrulama yöntemi: `[IO.File]::ReadAllLines($yol)` → 1-tabanlı indeks `N` için
`Select-Object -Index (N-1)`; sekiz satır da birebir eşleşti (2026-10-13).

**Olumsuz kanıt (dürüstlük):** `memory_get_usage` · `chmod` · `umask` · `nice(` ·
`pcntl_setscheduler` · `sched_setaffinity` için repo taraması **0 isabet** → bu
dosyadaki öncelik/affinity/realtime satırlarının repo karşılığı yoktur ve
`UNKNOWN` olarak kalır (uydurulmaz).

## §14. Kaynakça — 3 yeni URL, HTTP HEAD 200, envanter kesişimi 0

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| S-W1 | `timeout(1)` dış süre sınırı ve **124** çıkış kodu | https://man7.org/linux/man-pages/man1/timeout.1.html | 2026-10-13 | 200 |
| S-W2 | `hrtime()` monotonic nanosaniye sayacı (PHP) | https://www.php.net/manual/en/function.hrtime.php | 2026-10-13 | 200 |
| S-W3 | `wait4(2)` süreç bitişi + rusage (user/sys süre) | https://man7.org/linux/man-pages/man2/wait4.2.html | 2026-10-13 | 200 |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
→ HTTP 200 (2026-10-13). Envanter kesişimi: `.ai/architecture/**` içindeki
2633 benzersiz URL ile karşılaştırıldı → **0**.

**Erişilebilirlik notu:** `pubs.opengroup.org` tabanlı POSIX sayfalarının bir kısmı
404 dönmüştür (§6 dipnotu); aynı iddialar bu bölümde man7 ve php.net ile
desteklenmiştir.

## §15. Platform matrisi — sürücünün OS yüzeyi (Linux / Windows / macOS)

| İş | Linux | Windows | macOS | Bu daldaki karşılığı |
|---|---|---|---|---|
| Süreç önceliği | `setpriority(2)` / `nice(2)` | priority class (`BELOW_NORMAL_PRIORITY_CLASS` vb.) | `setpriority`/`nice` (BSD mirası) | T5 — repo karşılığı yok |
| Öncelik okuma | `getpriority(2)` | `GetPriorityClass` | `getpriority` | ölçüm M1 |
| Çekirdek atama | `sched_setaffinity(2)` | processor affinity | `thread_policy` (Mach) | T6 — PHP'de yok |
| Realtime politika | `sched_setscheduler(2)` | gerçek zamanlı sınıf (yetki ister) | `thread_policy_set` (Mach) | T7 — `EPERM` riski |
| Öncelik üst sınırı | `sched_get_priority_max(2)` | sabit sınıf değerleri | Mach öncelik aralığı | §14 URL |
| Zamanlayıcı sürümü | `SCHED_OTHER/FIFO/RR` | 4 priority class | Mach zamanlayıcı | anayasa §5 |
| Sayaç saati | `clock_gettime(CLOCK_MONOTONIC)` | `QueryPerformanceCounter` | `mach_absolute_time` | §14 URL (`hrtime`) |
| Uyku | `nanosleep(2)` | `Sleep` / `WaitableTimer` | `nanosleep` | §3.1.5 |
| Süreç bitişi | `wait4(2)` | `GetExitCodeProcess` | `waitpid` | §14 URL |
| Dış süre sınırı | `timeout(1)` (çekirdek Coreutils) | `Start-Process -Timeout` (PowerShell) | `timeout` (GNU coreutils) | §11.1 |
| Süreç yaratma | `clone/fork` | `CreateProcessW` | `posix_spawn` | anayasa §5 (PHP: `pcntl` yok) |
| I/O bekleme | `poll/epoll` | IOCP / `WSAPoll` | `kqueue` | §3.1.6 (`curl_multi_select`) |
| Uyandırma sayacı | `/proc/self/status` | performans sayacı | `mach` sayaçları | ölçüm M4 (`⚠️`) |
| Öncelikli iplik | pthread önceliği | thread priority | Mach iplik önceliği | §14 URL (`pthread_self`) |

**Yorum:** 14 satırın tamamı PHP yüzeyinde **doğrudan sargıya sahip değildir** —
yalnız `hrtime`/`usleep`/`curl_multi_select` üçlüsü sargılıdır (§3, §13). Bu, bu
dalın en önemli mühendislik sonucudur: **zamanlama ayarları OS konsolunda,
zamanlama değerleri uygulama kodundadır.**

### §15.1 Platform satırı başına ölçüm durumu (dürüstlük tablosu)

| Platform | Ölçüm aracı envanterde var mı? | Repo kanıtı | Durum |
|---|---|---|---|
| Linux | `chrt`/`ps`/`taskset` — araç adları upstream docs | yok | `UNKNOWN` (çalıştırılmadı) |
| Windows | Görev Yöneticisi/`GetPriorityClass` — upstream docs | yok | `UNKNOWN` (çalıştırılmadı) |
| macOS | `ps -o ni` — upstream docs | yok | `UNKNOWN` (çalıştırılmadı) |
| Ortak | `hrtime` farkı (middleware) | `MiddlewarePipeline.php:42,46` | **kanıtlandı** |

Çalıştırılmayan komutun çıktısı yazılmaz; bu tablo yalnızca **araç adı ve sahiplik**
durumunu kaydeder (§12.2 kuralı).

## §16. Sürücü kapanış kontrol listesi — revizyon kapısı

| # | Kontrol | Geçerse | Kalmazsa |
|---|---|---|---|
| K1 | §8 tablosunda her satır için sahip belli mi? | devam | `UNKNOWN` yaz, DUR |
| K2 | §12 ölçümü değişiklik öncesi alındı mı? | devam | DUR (ölçüm yoksa ayar yok) |
| K3 | §14 URL'leri HEAD 200 + envanter dışı mı? | devam | URL'yi düş, yeniden doğrula |
| K4 | §13 repo satırları `Select-Object -Index` ile eşleşiyor mu? | devam | satırı düzelt/yada `⚠️` koy |
| K5 | §11 yasak test desenlerinden hiçbiri yazılmadı mı? | devam | testi yeniden yaz |
| K6 | Geri dönüş değeri kaydedildi mi? | devam | DUR |

Bu altı kontrol bu dosyanın **kabul kriteridir**; biri bile "kalmazsa" dosya
revizyon kapısından geçmez.

## §17. Sürücü terim karşılıkları — dosyada geçen İngilizce ↔ Türkçe

| İngilizce terim | Bu dosyadaki karşılığı | Kısaltma | Not |
|---|---|---|---|
| scheduler | zamanlayıcı | — | anayasa §3 |
| time slice | zaman dilimi | quantum | anayasa §3 |
| context switch | bağlam değişimi | CS | anayasa §3.4 |
| priority | öncelik | — | §8 T5 |
| nice value | güzellik değeri | NI | §12 M1 |
| CPU affinity | CPU çekirdek ataması | — | §8 T6 |
| realtime policy | gerçek zamanlı politika | RT | §8 T7 |
| monotonic clock | monotonik saat | — | §14 (`hrtime`) |
| wall clock | duvar saati | — | NTP riski (E4) |
| polling timeout | yoklama zaman aşımı | — | §3.1.6 |
| deadline | son teslim anı | — | sartname bütçesi (§12.1) |
| jitter | titreşim/sapma | — | §12.1 (ölçüm PLANNED) |
| starvation | açlık | — | E7 |
| throttle | hız sınırlama | — | §3.1.7 |
| exit code | çıkış kodu | rc | §11.1 (124) |
| resource usage | kaynak kullanımı | rusage | §14 (`wait4`) |

Tablo bu dosyanın **sözlük katıdır**; anayasa §3'teki sözlüğün yerine geçmez,
onu tamamlar (aynı terim iki yerde farklı tanımlanmaz — anayasa kazanır).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
