---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/trace/trace-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability/trace
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - https://docs.kernel.org/trace/ftrace-design.html
  - https://docs.kernel.org/trace/ftrace.html
  - https://docs.kernel.org/trace/tracepoints.html
  - https://docs.kernel.org/trace/kprobes.html
  - https://docs.kernel.org/trace/uprobetracer.html
  - https://docs.kernel.org/trace/events.html
  - https://docs.kernel.org/trace/histogram.html
  - https://docs.kernel.org/trace/osnoise-tracer.html
  - https://man7.org/linux/man-pages/man2/bpf.2.html
  - https://docs.kernel.org/bpf/
  - https://docs.kernel.org/bpf/verifier.html
  - https://docs.kernel.org/bpf/helpers.html
  - https://docs.kernel.org/bpf/ringbuf.html
  - https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal
  - https://learn.microsoft.com/en-us/windows/win32/api/evntrace/
  - https://learn.microsoft.com/en-us/windows/win32/api/evntcons/
  - https://docs.kernel.org/core-api/symbol-namespaces.html
validation: "2026-10-14 — §6.1 ETW toplama araçları eklendi (T-W16 logman, T-W17 tracerpt, T-W18 portal); §6 karar 4 + §13 karar 5 PDH notu güncellendi (404 → M-W28–32 ile metrik dalında kapandı); §8 platform tablosu macOS sütunu dolduruldu (T-W19–T-W22 ayna dipnotlu + T-W23 Apple portal; hedef sürüm UNKNOWN) — 8 satıra genişletildi; §11.4 güncellendi; §12 envanter +8 satır (T-W16–23); §12 reddedilen adaylar 2. tur durumuyla güncellendi; §15.7 düzeltildi; §16.1 eklendi | 2026-10-13 — T-W1…T-W13 kök -dogrulama.md §2'de HTTP 200 doğrulanmış; 3 yeni URL (bpf/verifier.html webfetch içerik doğrulandı, bpf/helpers.html ve bpf/ringbuf.html HEAD 200) bu turda doğrulandı (envanter kesişimi 0); ftrace.html / events.html / etw portal os-master §C.6 ✅ envanterinden yeniden kullanıldı; sayısal overhead/taşma değeri yazılmadı"
open_questions:
  - "O1: OS-level trace toplama (ftrace, ETW oturumu, eBPF) hedef mimaride var mı, yoksa yalnız uygulama traceId'si mi? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? [UNKNOWN]"
group: mimari
---

# trace-anayasa — OS Trace Anayasası

## §0 ELI15

Trace, bir olayın **zaman damgasıyla, sırası korunarak** kaydedilmesidir: "hangi
süreç, hangi çekirdek yolu, hangi sırayla geçti?" metrik ve log'un veremediği
bağlantıyı verir. Linux bunu **ftrace/tracefs** üzerinde **tracepoint** (derleme
zamanı nokta) ve **kprobe/uprobe** (çalışma anı prob) ile; dinamik mantığı
**eBPF** ile yapar. Windows aynı işi **ETW oturumu** ile yapar. Bu anayasa OS
düzeyi trace **tanımını** bağlar. Repo'daki `traceId` bir istek korelasyon
kimliğidir, OS trace değildir; OpenTelemetry span/dağıtık trace `K017-observability`
dalındadır.

Değişmez üç madde:

1. Ölçülmemiş sayı yazılmaz — overhead, buffer boyutu, kayıp olay sayısı
   envanterde/mevcut ölçümdе yoksa `UNKNOWN` yazılır.
2. Repo'da OS trace aracı yoktur (grep 0 isabet); `traceId` bu boşluğu
   doldurmaz — ikisi aynı katmanda değildir.
3. Doğrulanmayan URL envantere girmez; bu dosyadaki her URL ya kök
   `-dogrulama.md` §2'de (T-W1…T-W13) HTTP 200 doğrulanmıştır ya da bu turda
   doğrulanmıştır (§12, tarih 2026-10-13).

## §1 Trace türleri — aynı kelime, üç katman

| # | Tür | Ne kaydeder | Üreten | Bu dalın kapsamı |
|---|---|---|---|---|
| 1 | OS olay izi | çekirdek/uygulama olaylarını sırayla (pid, tid, cpu, zaman damgası) | ftrace, tracepoint, kprobe, eBPF, ETW | **evet** |
| 2 | Uygulama traceId | tek isteğin log satırlarını birleştiren kimlik | uygulama kodu (`PageRouterKernel.php:87`) | yalnız ayrım (`-ornekler` §10) |
| 3 | Dağıtık span (OTel) | servisler arası çağrı ağacı | OpenTelemetry / APM | hayır — `K017-observability` |

| Kriter | OS olay izi | Uygulama traceId | Dağıtık span |
|---|---|---|---|
| Birim | olay (event) | log satırı etiketi | span (başlangıç+bitiş) |
| Zaman | çekirdek zaman damgası | uygulama saat/monotonik | servis saat senkronu |
| Sıra garantisi | oturum içi tam sıra | dosyaya ekleme sırası | yayım sırası |
| Üretici | OS çekirdeği | PHP kodu | instrumentasyon kütüphanesi |
| Kaldırılabilir mi | evet (trace noktaları kapalı) | evet (kod kararı) | evet (instrumentasyon) |

## §2 ftrace ve tracefs — çekirdek iz altyapısı

| # | Kavram | Tanım | Kaynak |
|---|---|---|---|
| 1 | ftrace | çekirdek içi izleme altyapısı; olayları ve tracers'ları besler | T-W1 `ftrace-design.html` · `ftrace.html` (os-master §C.6 ✅) |
| 2 | tracefs | ftrace'in bağlandığı sanal dosya sistemi (iz noktaları, kontrol dosyaları) | T-W1 |
| 3 | trace events | tracepoint'lerin tekilleştirilmiş olay arayüzü | `events.html` (os-master §C.6 ✅) |
| 4 | tracers | hazır iz türleri (fonksiyon, irq, osnoise…) | T-W6 osnoise |

Bağlayıcı ilkeler:

1. ftrace **olay üretmez** — olayları **üreten** tracepoint/kprobe'dır; ftrace onları
   kayıt ve iletme altyapısına bağlar (T-W1, T-W2).
2. İz **açmak/kapamak** çalışma anında yapılır; kapalı noktanın maliyeti
   (açıklanmayan sayı → `UNKNOWN`) açıktan farklıdır — sayı uydurulmaz.
3. ftrace hattı **tek-yazıcı halka tamponu** üzerinden ilerler; taşma durumunda
   kayıp olaylar işaretlenir (mekanizma `-surucu` §5'te; sayısal değer yok → `UNKNOWN`).

## §3 Tracepoint — statik olay noktaları

| # | Özellik | Değer | Kaynak |
|---|---|---|---|
| 1 | Tanım anı | derleme/kurulum zamanı, kaynak koda gömülü | T-W2 `tracepoints.html` |
| 2 | Tür | statik (kodda yer ayırtılmış) | T-W2 |
| 3 | Maliyet | kapalıyken kodda boş yer tutma; açıkken kayıt yolu | T-W2 (sayısal değer yok → `UNKNOWN`) |
| 4 | Katalog | `events.html` altındaki kategoriler (sched, irq, syscalls…) | `events.html` ✅ |
| 5 | Üretici | çekirdek modülü / çekirdek kaynağı + tanımlayıcı makro | T-W2 |

| # | Tracepoint kategorisi | Örnek olay ailesi (katalogdan — sayısal id uydurulmaz) | İlgili dal |
|---|---|---|---|
| 1 | zamanlama (sched) | `events/sched/` kataloğu | `isletim-sistemi-plan` |
| 2 | kesme/softirq | `events/irq/` kataloğu | `isletim-sistemi-kernel` |
| 3 | sistem çağrıları | `events/syscalls/` kataloğu | `isletim-sistemi-api` |
| 4 | bellek | `events/kmem/` kataloğu | `isletim-sistemi-core` |

> Katalog **adları** `events.html` envanterinden (✅) alınmıştır; tek tek olay
> adları ve id'leri bu dosyaya **kopyalanmaz** (yanılma riski — doğrulanmadı →
> `⚠️ VERIFICATION REQUIRED`, katalog bağlantısı esas alınır).

## §4 kprobe ve uprobe — çalışma anı prob

| # | Özellik | kprobe | uprobe | Kaynak |
|---|---|---|---|---|
| 1 | Hedef | çekirdek fonksiyonu | kullanıcı uzayı (uygulama) ikili dosyası | T-W3 / T-W5 |
| 2 | Ekleme anı | çalışma anı (dinamik) | çalışma anı (dinamik) | T-W3 `kprobes.html` · T-W5 `uprobetracer.html` |
| 3 | Dayanak | mevcut makine koduna kırma (breakpoint benzeri) | sembol konumuna prob | T-W3, T-W5 |
| 4 | Sınırlama | çekirdek içi geçersiz/kısıtlı bölgelerde eklenemez | yürürlükteki ikili dosyada sembol bulunmalı | T-W3, T-W5 |
| 5 | Kullanım | geçici teşhis — üretimde süreklilik için tracepoint/eBPF tercih edilir | fonksiyon giriş/çıkış izleme | T-W3, T-W5, T-W7 |

| # | Prob yolu | Ne zaman seçilir | Karşılığı |
|---|---|---|---|
| 1 | tracepoint | nokta zaten varsa | kalıcı, düşük maliyet |
| 2 | kprobe/uprobe | nokta yoksa ve geçiciyse | esnek, teşhis amaçlı |
| 3 | eBPF | mantık/koşullu toplama gerekirse | programlanabilir (§5) |

| # | kprobe sınırlaması (T-W3) | Belirti | Dürüst tepki |
|---|---|---|---|
| 1 | prob noktası yok/kısmi sembol | prob eklenemez | sembol adı doğrulanmadan yazılmaz |
| 2 | çekirdek içi geçersiz bölge | prob reddedilir | alternatif nokta aranır |
| 3 | örtüşen prob | ikinci prob reddedilir | tek prob yeterli |

| # | uprobe sınırlaması (T-W5) | Belirti | Dürüst tepki |
|---|---|---|---|
| 1 | ikili dosyada sembol yok | prob eklenemez | dosya/simbol doğrulanır |
| 2 | yürürlükteki süreç değişimi | prob bağlamı kayar | pid filtresi |

> kprobe/uprobe **teşhis amaçlı geçici** araçtır; kalıcı iz için tracepoint,
> koşullu toplama için eBPF tercih edilir (§4.5).

## §4.5 Prob seçimi karar akışı

| # | Soru | Evet → | Hayır → |
|---|---|---|---|
| 1 | İzlenecek nokta çekirdekte zaten tanımlı mı? | tracepoint (§3) | §2 |
| 2 | Nokta kullanıcı uzayında mı? | uprobe (§4) | §3 |
| 3 | Nokta çekirdek içinde ve dinamik mi? | kprobe (§4) | §3 |
| 4 | Olaylar üzerinde koşul/filtre/Toplama gerekiyor mu? | eBPF (§5) | doğrudan kayıt (§2) |
| 5 | Sonuç yalnız sayaç mı? | `metrik` dalı (perf_event) | trace hattı (bu dal) |

| # | Seçim sonrası zorunlu kayıt | Neden |
|---|---|---|
| 1 | hangi noktanın/prob'un açıldığı | kapalıyken kanıt yoktur (§9.4) |
| 2 | periyot/filtre ayarları | tekrarlanabilirlik |
| 3 | kapatma anı | kaynak temizliği (§6 ETW oturum sonlandırma · `-surucu` §6) |

## §5 eBPF — programlanabilir çekirdek mantığı

| # | Parça | Rol | Kaynak |
|---|---|---|---|
| 1 | `bpf(2)` | eBPF syscall arayüzü (program yükleme, harita, sorgu) | T-W7 `bpf.2.html` |
| 2 | BPF çekirdek dokümantasyonu | resmi ana kaynak | T-W8 `docs.kernel.org/bpf/` |
| 3 | verifier | yüklenen programın güvenli olduğunu **kanıtlayan** doğrulayıcı | **yeni** `bpf/verifier.html` (2026-10-13, webfetch HTTP 200) |
| 4 | helpers | çekirdekten çağrılabilen yardım fonksiyonları (olay yazma vb.) | **yeni** `bpf/helpers.html` (2026-10-13, HEAD 200) |
| 5 | ring buffer | düşük maliyetli olay çıkış kanalı (uygulamaya aktarım) | **yeni** `bpf/ringbuf.html` (2026-10-13, HEAD 200) |

| # | Yükleme kapısı (verifier) | Ne doğrular | Sonuç |
|---|---|---|---|
| 1 | program biçimi | geçerli BPF komut dizisi | geçersizse yüklenmez |
| 2 | bellek erişimi | dizi/kayıt sınırları | sınır dışı erişim reddedilir |
| 3 | döngü/atlama | sonlu yürütme | kanıtlanamayan sonluluk reddedilir |
| 4 | helper çağrısı | izinli yardımcılar | izinsiz çağrı reddedilir |

> Verifier'ın **reddetme davranışı fail-safe'dir**: doğrulanamayan program
> çalıştırılmaz — bu, `-surucu` §7'deki "kapı" kuralının çekirdek karşılığıdır.
> Verifier'ın ayrıntılı kuralları `bpf/verifier.html` metnindedir; bu dosyaya
> **kopyalanmaz** (yanılma riski — kaynak esas).

Değişmez üç madde (eBPF):

1. Program **her yüklemede verifier'dan** geçmek zorundadır; geçemeyse yüklenmez
   (fail-safe — kapı `verifier.html`'de tanımlıdır).
2. eBPF, tracepoint/kprobe **üzerinde** çalışır; ikisini **yerine geçirmez** —
   yakalama noktası ayrı, mantık ayrı katmandır.
3. BPF haritası ve ring buffer, olayların **toplanması/aktarım** mekanizmasıdır;
   bunlar metrik deposu **değildir** (sayaç → `metrik` dalı; yalnız aktarım
   yüzeyi burada).

## §6 ETW (Windows) — oturum, sağlayıcı, tüketici

| # | Parça | Rol | Kaynak |
|---|---|---|---|
| 1 | ETW portal | Windows olay izleme genel portalı | os-master §C.6 ✅ `etw/event-tracing-portal` |
| 2 | `evntrace` | trace **oturumu** başlatma/surma/yönetme Win32 API'si | T-W12 `api/evntrace/` |
| 3 | `evntcons` | olay **tüketici** API'si (oturumdan olay okuma) | T-W13 `api/evntcons/` |
| 4 | `WNODE_HEADER` | olay/oturum düğüm başlığı yapı taşı | os-master §C.6 ✅ `etw/wnode-header` |

| # | ETW rol | Windows karşılığı | Linux karşılığı | Fark |
|---|---|---|---|---|
| 1 | oturum yönetimi | `evntrace` | tracefs kontrol dosyaları | Windows oturum merkezli; Linux dosya arayüzli |
| 2 | olay üretici | sağlayıcı (provider) | tracepoint/kprobe/eBPF | ikisi de statik+dinamik karışımı |
| 3 | olay okuma | `evntcons` | ftrace okuma / perf / ringbuf | Windows API, Linux dosya + syscall |
| 4 | kalıcılık | canlı oturum + log dosyası | hattan halka tamponu (kalıcı değil) | ikisi de varsayılan olarak canlı-izleme |

| # | ETW kararları (bağlayıcı) | Gerekçe |
|---|---|---|
| 1 | oturum **kapatılır** — açık oturum kaynak bırakmaz | T-W12 yaşam döngüsü |
| 2 | sağlayıcı adı doğrulanmadan yazılmaz | sağlayıcı kataloğu bu envanterde yok → `UNKNOWN` |
| 3 | tüketici (`evntcons`) olmadan oturum yalnız toplama yapar | T-W13 ayrı API'dir |
| 4 | performans sayacı/PDH karıştırılmaz | o `metrik` dalıdır; 2. turda PDH doğrulandı (M-W28–32, metrik-anayasa §10) — bu dalın konusu değildir |

> ETW'de **performans sayacı toplama/PDH** bu dalın **kapsamı dışındadır** (o
> metrik dalıdır). 1. turdaki 404 kaydı (kök `-dogrulama.md` §4 X6) **korunur**;
> 2. turda doğru PDH yolları metrik dalında doğrulandı (M-W28–M-W32).

### §6.1 ETW toplama araçları — 2. turda doğrulandı (2026-10-14)

| # | Araç | Rol | Kanıt |
|---|---|---|---|
| 1 | `logman` | ETW trace/toplayıcı oturumu yönetir (başlat/sdur/listele) | T-W16 |
| 2 | `tracerpt` | ETL ham verisini rapor/format'a çevirir (CSV/HTML/XML) | T-W17 |
| 3 | ETW portal | Windows olay izleme teknoloji turu (genel portal) | T-W18 |

`logman` (T-W16) hem ETW oturumu hem performans toplayıcısı oluşturabilir —
bu dosyada **yalnız ETW oturumu** tarafı geçer; sayaç tarafı metrik dalıdır
(M-W26, `logman create counter`). `tracerpt` (T-W17) ETL dosyasını
insan-okunur formata çevirir; bu, Linux'taki `perf script` (T-W11) ile
aynı sınıftır (ham → yorumlanabilir).

## §7 Olay veri modeli — her iki platformda ortak alanlar

| # | Alan | Linux izi | Windows ETW | Not |
|---|---|---|---|---|
| 1 | zaman damgası | çekirdek zamanı | olay zaman damgası | saat kaynağı platforma ait |
| 2 | süreç/sistem (pid) | evet | evet | uygulama katmanı traceId ile **karıştırılmaz** |
| 3 | iş parçacığı (tid) | evet | evet | sıra bu düzeyde okunur |
| 4 | cpu | evet | evet | çok-çekirdek sırası için gerekli |
| 5 | olay adı/türü | `events/` kataloğu | sağlayıcı+olay kimliği | kimlik sistemi farklı |
| 6 | yük (payload) | tracepoint argümanları | etkinlik verisi | alan modeli olaya özel |

| # | Bu modelin taşımadığı alan | Nerede |
|---|---|---|
| 1 | uygulama JSON alanları (seviye, istek yolu) | `log` alt-dalı |
| 2 | sayaç değeri (CPU %, sayaç birikimi) | `metrik` alt-dalı |
| 3 | servis adı, span kimliği, korelasyon (OTel) | `K017-observability` |

## §7a Olay veri yolu — halkadan uygulamaya (tarif)

| # | Adım | Linux (ftrace/eBPF hattı) | Windows (ETW) |
|---|---|---|---|
| 1 | olay kaynağı | tracepoint/kprobe/uprobe tetiklenir | sağlayıcı etkinliği üretilir |
| 2 | damgalama | pid/tid/cpu + çekirdek zaman damgası | alanlar + olay zaman damgası |
| 3 | aktarım | ftrace hattı veya BPF ring buffer | oturum tamponu |
| 4 | tüketim | kullanıcı uzayı okur (dosya/syscall) | `evntcons` (T-W13) |
| 5 | yorumlama | `perf script`/histogram (T-W11, T-W4) | tüketici uygulaması |

| # | Bu yolun **olmadığı** yer | Kanıt |
|---|---|---|
| 1 | PHP kodundaki `error_log` yolu | `StructuredLogger.php:72` — ayrı katman |
| 2 | HTTP `X-Trace-Id` başlığı | `ResponseEmitter.php:97` — uygulama etiketi |
| 3 | CI pipeline | `.github/workflows/ci.yml` — trace aracı yok (§-ornekler §8) |

## §8 Platform karşılaştırması — tek tablo (2. tur güncellendi — 2026-10-14)

| Özellik | Linux | Windows | macOS |
|---|---|---|---|
| Birincil mekanizma | ftrace/tracefs · kprobe/uprobe · eBPF | ETW (`evntrace`/`evntcons`) · `logman`/`tracerpt` (T-W16–17) | DTrace (`dtrace.1`, T-W19, ayna) · kernel portal (T-W23) |
| Arayüz biçimi | sanal dosya sistemi + syscall (`bpf(2)`) | Win32 API + `logman` komut satırı | DTrace komut satırı + `fs_usage`/`sample` sarmalayıcı |
| Statik nokta | tracepoint (`tracepoints.html` ✅) | sağlayıcı etkinliği | DTrace prob tipleri (`dtrace.1`, T-W19 — ayrıntı `UNKNOWN`) |
| Dinamik nokta | kprobe/uprobe | — (⚠️ eşleniği doğrulanmadı) | DTrace dinamik prob (`dtrace.1`, T-W19, ayna) |
| Programlanabilir mantık | eBPF (verifier + helpers + ringbuf ✅) | ⚠️ (OEM/ETW genişletmesi doğrulanmadı) | DTrace d-script (`dtrace.1`, T-W19, ayna) — sürüm kullanılabilirliği `UNKNOWN` |
| Dosya/sistem izleme | `fs` tracepoint + eBPF | ⚠️ (ETW dosya sağlayıcısı doğrulanmadı) | `fs_usage(1)` (T-W20, ayna) |
| CPU örnekleme profili | `perf record` (T-W10) | ⚠️ (xperf/ETW örnekleme doğrulanmadı) | `sample(1)` (T-W21, ayna) |
| Sembol/ikili inceleme | `perf script` + sembol ad uzayları (T-W9, T-W11) | ⚠️ (dumpbin doğrulanmadı) | `otool(1)` Mach-O (T-W22, ayna) |
| Kaynak envanteri | T-W1…T-W11 + 3 yeni + 2 os-master ✅ | T-W12, T-W13 + portal/wnode ✅ + T-W16–18 | T-W19–T-W22 (ayna, dipnotlu) + T-W23 (Apple portal) |

> **macOS durumu (2. tur):** 1. turda macOS sütunu **tamamen `⚠️`** idi
> (kök `-dogrulama.md` §15.1). 2. turda dört Xcode man sayfası üçüncü taraf
> aynasından (keith.github.io) HTTP 200 ile doğrulanmıştır (T-W19–T-W22)
> ve Apple'ın kendi kernel portalı T-W23 ile doğrulanmıştır. **Kalan
> `⚠️`:** hedef macOS sürümünde DTrace/ktrace kullanılabilirliği `UNKNOWN`
> (ayna sayfası sürüm bağlamı taşımaz) — bu nedenle macOS sütunu artık
> **tam `⚠️` değil, ayna-kanıtlı + sürüm-bilinmeyen** olarak işaretlenir.
> **Ayna dipnotu (zorunlu):** `dtrace.1`, `fs_usage.1`, `sample.1`,
> `otool.1` keith.github.io üzerinden doğrulanmıştır; bu site Apple'ın
> resmi sitesi değildir — üçüncü taraf aynasıdır (kök `-dogrulama.md` §1.1).

## §9 Ayırma ilkeleri — bu anayasanın bağlayıcı kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | OS trace, log **değildir** — trace hattı satır dosyasına yazmaz | iki farklı taşıma (halka tamponu vs. dosya I/O) |
| 2 | OS trace, metrik **değildir** — olay akışı sayım değildir | akım vs. birikim |
| 3 | uygulama `traceId` ≠ OS trace — biri kimlik, diğeri olay akısı | katman farkı (`-ornekler` §10) |
| 4 | Trace noktası açmak bir **karar**dır; kapalıyken kanıt "yoktur", "0 olay" **değildir** | dürüstlük |
| 5 | Sayısal overhead/taşma değeri yalnız kaynaklı yazılır | zero-hallucination |

## §10 K017 sınırı ve komşu matrisi

| Konu | Sınır | Gittiği dal |
|---|---|---|
| OTel span üretimi, korelasyon servisi, APM paneli | uygulama/dağıtık | `K017-observability` |
| alarm kuralı ("trace gecikmesi > eşik") | iş kuralı | `K017-observability` |
| tracepoint tanımı/kaynağı | çekirdek | `isletim-sistemi-kernel` |
| event üretimi ve sırası | planlama | `isletim-sistemi-plan` |
| süreç/bellek bağlamı (pid, addr) | core | `isletim-sistemi-core` |
| PMU/örnekleme sayaçları | sayım | `metrik` alt-dalı |
| softlockup/hung task **log** çıkışı | günlük | `log` alt-dalı |

| # | Sınır cümlesi (bağlayıcı) | Kısa |
|---|---|---|
| 1 | Bu dal OS trace'i **tanımlar ve tarif eder**; uygulama/dağıtık trace'e **girmez** | OS ≠ OTel |
| 2 | traceId ile trace'i **karıştırmak yasaktır** — biri kimlik, diğeri olay akısı | §1, `-ornekler` §10 |
| 3 | metrik dalıyla ortak yüzey yalnız **perf_event** arayüzüdür; sayım `metrik`'te kalır | `-surucu` §3 |
| 4 | log dalıyla ortak yüzey yalnız **eşik/olay kesişimidir**; yazım `log`'dadır | `log-surucu` §7 |

## §10.5 Senaryo — "bu istek neden yavaştı?" sorusunun OS trace ile cevabı

| # | Adım | Araç/kavram | Kaynak |
|---|---|---|---|
| 1 | olay noktası seç | tracepoint/katalog | T-W2, `events.html` ✅ |
| 2 | nokta yoksa ekle | kprobe/uprobe | T-W3, T-W5 |
| 3 | koşul/filtre | eBPF (verifier kapısından geçer) | yeni bpf URL'leri §12 |
| 4 | kayıt | ftrace hattı / ring buffer | T-W1, ringbuf |
| 5 | çözümleme | `perf script` / histogram | T-W11, T-W4 |
| 6 | sembol | ad uzayları | T-W9 |
| 7 | yorum + alarm | uygulama katmanı | `K017-observability` |

> Bu senaryo **tariftir** — repo'da bu yolu çağıran kod yoktur (grep 0 isabet,
> `-ornekler` §9). Senaryonun 1–6. adımları OS dalı, 7. adımı K017 dalıdır.

## §11 Ölçüm ve dürüstlük kuralları

| # | Kural | Uygulama |
|---|---|---|
| 1 | İz maliyeti (kapalı/açık) sayısal olarak yazılmaz | yalnız mekanizma tarifi |
| 2 | Taşma/kayıp sayısı ölçülmeden yazılmaz | `-surucu` §5'te mekanizma, sayı `UNKNOWN` |
| 3 | Olay adları katalogdan okunur, hafızadan yazılmaz | §3 notu |
| 4 | macOS iddiaları ayna-dipnotlu ve sürüm-bilinmeyen kalır | §8 (2. tur: T-W19–T-W22 ayna + T-W23 portal; hedef sürüm `UNKNOWN`) |
| 5 | Repo iddiası yalnız Read/grep ile okunan satırla yazılır | `-ornekler` |

| # | Dürüstlük senaryosu | Doğru davranış | Yanlış davranış |
|---|---|---|---|
| 1 | trace kapalıyken sistem sorunu var mı? | "iz yok" der, "olay yok" **demez** | kapalıyken temiz rapor |
| 2 | taşma sayacı arttıysa | "iz eksik" der | "kayıp önemsiz" uydurması |
| 3 | sembol yoksa | ham adres verir | ad/satır **uydurması** |
| 4 | ölçüm yapılmadıysa | `UNKNOWN` yazar | tahmini sayı yazar |
| 5 | platform doğrulanmadıysa | `⚠️` etiketi | genelleme ("macOS'ta da böyledir") |

| # | Ölçüm sorumluluğu | Bu dal | Karşılığı |
|---|---|---|---|
| 1 | OS olay akışı ölçümü | tarif (`-surucu` §7) | repo ölçümü yok |
| 2 | uygulama süresi ölçümü | yalnız ayrım (`-ornekler` §5) | `metrik` dalı da değil — uygulama içi |
| 3 | alarm eşiği | yok | `K017-observability` |

## §12 Resmi yapı taşları — URL envanteri (2026-10-13)

| # | ID | Kaynak | URL | Doğrulama |
|---|---|---|---|---|
| 1 | T-W1 | ftrace tasarım | https://docs.kernel.org/trace/ftrace-design.html | HTTP 200 (§2) |
| 2 | — | ftrace ana | https://docs.kernel.org/trace/ftrace.html | os-master §C.6 ✅ yeniden kullanım |
| 3 | T-W2 | tracepoint | https://docs.kernel.org/trace/tracepoints.html | HTTP 200 (§2) |
| 4 | — | olay kataloğu | https://docs.kernel.org/trace/events.html | os-master §C.6 ✅ yeniden kullanım |
| 5 | T-W3 | kprobe | https://docs.kernel.org/trace/kprobes.html | HTTP 200 (§2) |
| 6 | T-W5 | uprobe | https://docs.kernel.org/trace/uprobetracer.html | HTTP 200 (§2) |
| 7 | T-W4 | histogram trigger | https://docs.kernel.org/trace/histogram.html | HTTP 200 (§2) |
| 8 | T-W6 | osnoise tracer | https://docs.kernel.org/trace/osnoise-tracer.html | HTTP 200 (§2) |
| 9 | T-W7 | bpf(2) | https://man7.org/linux/man-pages/man2/bpf.2.html | HTTP 200 (§2) |
| 10 | T-W8 | BPF dokümanları | https://docs.kernel.org/bpf/ | HTTP 200 (§2) |
| 11 | — | verifier | https://docs.kernel.org/bpf/verifier.html | **yeni**, webfetch HTTP 200 |
| 12 | — | helpers | https://docs.kernel.org/bpf/helpers.html | **yeni**, HEAD 200 |
| 13 | — | ring buffer | https://docs.kernel.org/bpf/ringbuf.html | **yeni**, HEAD 200 |
| 14 | T-W9 | sembol ad uzayları | https://docs.kernel.org/core-api/symbol-namespaces.html | HTTP 200 (§2) |
| 15 | T-W10 | perf record | https://man7.org/linux/man-pages/man1/perf-record.1.html | HTTP 200 (§2) |
| 16 | T-W11 | perf script | https://man7.org/linux/man-pages/man1/perf-script.1.html | HTTP 200 (§2) |
| 17 | T-W12 | ETW oturum API | https://learn.microsoft.com/en-us/windows/win32/api/evntrace/ | HTTP 200 (§2) |
| 18 | T-W13 | ETW tüketici API | https://learn.microsoft.com/en-us/windows/win32/api/evntcons/ | HTTP 200 (§2) |
| 19 | — | ETW portal | https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal | os-master §C.6 ✅ yeniden kullanım |
| 20 | — | WNODE_HEADER | https://learn.microsoft.com/en-us/windows/win32/etw/wnode-header | os-master §C.6 ✅ yeniden kullanım |

**2. tur genişletme (2026-10-14)** — Windows ETW toplama + macOS trace:

| # | ID | Kaynak | URL | Doğrulama |
|---|---|---|---|---|
| 21 | T-W16 | `logman` ETW oturumu yönetir | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/logman | HTTP 200 (§2.1) |
| 22 | T-W17 | `tracerpt` ETL → rapor formatı | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/tracerpt | HTTP 200 (§2.1) |
| 23 | T-W18 | ETW olay izleme portalı (teknoloji turu) | https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal | HTTP 200 (§2.1) |
| 24 | T-W19 | `dtrace(1)` dinamik izleme (ayna) | https://keith.github.io/xcode-man-pages/dtrace.1.html | HTTP 200 (§3.1) — ayna, dipnotlu |
| 25 | T-W20 | `fs_usage(1)` dosya sistemi izleme (ayna) | https://keith.github.io/xcode-man-pages/fs_usage.1.html | HTTP 200 (§3.1) — ayna, dipnotlu |
| 26 | T-W21 | `sample(1)` süreç CPU profili (ayna) | https://keith.github.io/xcode-man-pages/sample.1.html | HTTP 200 (§3.1) — ayna, dipnotlu |
| 27 | T-W22 | `otool(1)` Mach-O inceleme (ayna) | https://keith.github.io/xcode-man-pages/otool.1.html | HTTP 200 (§3.1) — ayna, dipnotlu |
| 28 | T-W23 | Apple kernel/çekirdek portalı | https://developer.apple.com/documentation/kernel | HTTP 200 (§3.1) |

Reddedilen adaylar:

| # | Aday | Sonuç | Karar |
|---|---|---|---|
| 1 | `docs.kernel.org/trace/trace-cmd.html` | bu turda doğrulanamadı | kullanılmadı |
| 2 | macOS DTrace/ktrace resmi dokümanı (Apple) | 1. turda resmi URL doğrulanmadı → `⚠️`; **2. turda ayna doğrulandı** (T-W19–T-W22, dipnotlu) — Apple'ın kendi sayfası hâlâ doğrulanmadı, hedef sürüm `UNKNOWN` | kısmen kapandı |
| 3 | Windows PDH toplama sayfası | 1. turda HTTP 404 (kök §4 X6); **2. turda doğru PDH yolları bulundu** (M-W28–M-W32, metrik dalı) | bu dalın dışı, kapandı (metrik dalında) |

> Bu üç aday **hiçbir bölümde kanıt olarak kullanılmaz**; yalnız
> "doğrulanmadı" kaydı taşır. Doğrulanırlarsa kök `-dogrulama.md`'ye eklenir,
> bu dosyaya sonradan uydurma URL ile girilmez.

## §13 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| O1 | OS trace toplama (ftrace, ETW oturumu, eBPF) hedef mimaride var mı, yoksa yalnız uygulama traceId'si mi? | UNKNOWN |
| O2 | Gözlemlenebilirlik üçlüsü K002-altyapi içinde nasıl konumlanacak? | UNKNOWN |
| O3 | Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? | UNKNOWN |

## §13 evntprov — ETW sağlayıcı arayüzü (bu tur doğrulandı)

**Kanıt:** `https://learn.microsoft.com/en-us/windows/win32/api/evntprov/` (2026-10-13 webfetch HTTP 200) — evntprov.h.

| # | Sembol | Rol | Katman |
|---|---|---|---|
| 1 | `EventRegister` | sağlayıcı kayıt oturumu | sağlayıcı |
| 2 | `EventUnregister` | oturumu kapatır | sağlayıcı |
| 3 | `EventWrite` | olay yazar | sağlayıcı |
| 4 | `EventWriteEx` | gelişmiş yazım (seçenekli) | sağlayıcı |
| 5 | `EventWriteTransfer` | hedef oturuma/aktiviteye transfer | sağlayıcı |
| 6 | `EVENT_DESCRIPTOR` | olay kimliği/yapısı taşıyıcısı | veri modeli |
| 7 | `PENABLECALLBACK` | enable/disable geri çağrısı | dinamik aç/kapa |

| # | Kavram | Karşılığı | Fark |
|---|---|---|---|
| 1 | `EVENT_DESCRIPTOR` | Linux `events.html` olay kimliği | kimlik sistemi farklı (§7 kural 5) |
| 2 | `PENABLECALLBACK` | trace nokta aç/kapa | Windows geri çağrı, Linux dosya arayüzü |
| 3 | `EventWrite*` | tracepoint tetiklenmesi | Windows API, Linux halka tamponu |
| 4 | sağlayıcı (provider) | tracepoint/kprobe üreteci | statik+dinamik karışımı (§6) |

| # | Bağlayıcı karar | Gerekçe |
|---|---|---|
| 1 | evntprov **üretici** API'sidir; oturum `evntrace`, tüketim `evntcons`'tur | üçü ayrı ailelerdir |
| 2 | `EventRegister` başarısızsa "olay yok" **denmez** | hata ≠ temizlik (§9 kural 4) |
| 3 | `EventUnregister` akışın zorunlu adımıdır | açık oturum kaynak bırakmaz |
| 4 | sağlayıcı adı doğrulanmadan yazılmaz | katalog envanterde yok → `UNKNOWN` |
| 5 | PDH ile karıştırılmaz | PDH metrik dalıdır; 2. turda M-W28–32 ile kapandı (§6, metrik-anayasa §10) |
| 6 | repo'da `EventWrite`/`EventRegister` 0 isabet | `-ornekler` §9 |

## §14 docs.kernel.org/trace/index.html — rehber çerçevesi (bu tur doğrulandı)

**Kanıt:** `https://docs.kernel.org/trace/index.html` (2026-10-13 webfetch HTTP 200) — Linux Tracing Technologies.

| # | Rehber konusu | Bu anayasadaki karşılığı |
|---|---|---|
| 1 | ftrace | §2 |
| 2 | kprobes | §4 |
| 3 | fprobe | §4 (prob ailesi) |
| 4 | eprobes | §4–§5 sınırı |
| 5 | lockless ring buffer | §2 kural 3 |
| 6 | event tracing | §3 |
| 7 | histograms | `-surucu` §2 |
| 8 | osnoise | §0, `-surucu` §1 |
| 9 | timerlat | `-surucu` §1 (aynı aile) |
| 10 | user_events | §4.5 (kullanıcı uzayı olayı) |
| 11 | uprobes | §4 |
| 12 | boot-time tracing | kapsam dışı — açılma senaryosu |
| 13 | tracefs ring-buffer memory mapping | §2 kural 3 |

| # | Kural | Neden |
|---|---|---|
| 1 | rehber **katalogdur**; komut/satır kopyalanmaz | yanılma riski |
| 2 | konu adları kaynakla aynı yazılır | uydurma ad yok |
| 3 | sayısal değer (buffer, gecikme) alınmaz | okunmadı → `UNKNOWN` |
| 4 | 404 aday (irqsoff-tracer, perf-sched) bu tabloya **alınmaz** | red kaydı §12 |

## §15 Sık karıştırılan cümleler — düzeltme matrisi

| # | Yanlış cümle | Doğru cümle | Kanıt |
|---|---|---|---|
| 1 | "traceId bir trace'dir" | korelasyon kimliğidir; olay akısı yok | §1 |
| 2 | "ETW tek API'dir" | evntprov / evntrace / evntcons üç ayrı ailedir | §6, §13 |
| 3 | "PDH ETW'dür" | PDH sayacıdır → metrik dalı | §6 |
| 4 | "eBPF tracepoint'i ekleme yöntemidir" | üzerinde çalışır; yakalama ayrı katmandır | §5 kural 2 |
| 5 | "trace rehberi komut listesidir" | katalogdur | §14 kural 1 |
| 6 | "kapalı tracer = 0 olay" | kapalı = kanıt yok | §9 kural 4 |
| 7 | "macOS trace doğrulandı" | yalnız ayna-kanıtlı (T-W19–22, dipnotlu); Apple'ın kendi sayfası + hedef sürüm `UNKNOWN` → §8 |
| 8 | "sembol yoksa ad tahmin edilir" | ham adres yazılır | §11 |
| 9 | "sağlayıcı adı yazılabilir" | doğrulanmadan yazılmaz | §13 karar 4 |
| 10 | "kprobe üretim kalıcı izdir" | teşhis amaçlı geçicidir | §4 |

## §16 Bu tur red/kayıp kayıtları (2026-10-13)

| # | Aday | Sonuç | Karar |
|---|---|---|---|
| 1 | `docs.kernel.org/trace/irqsoff-tracer.html` | HTTP 404 | kullanılmadı — §12 reddedilenlere eklendi |
| 2 | `man7.org/.../man8/perf-sched.8.html` | HTTP 404 | kullanılmadı |
| 3 | `man7.org/.../man3/openlog.3.html` | HTTP 200 ama içerik **syslog(3)** sayfası | mükerrer — yeni ID verilmedi (log dalında L-W1) |
| 4 | `man7.org/.../man8/logger.8.html` | HTTP 404 | log dalına alınmadı (L-W17 alternatifi logger.1) |

| # | Doğrulanan (bu tur) | URL |
|---|---|---|
| 1 | evntprov (ETW sağlayıcı) | https://learn.microsoft.com/en-us/windows/win32/api/evntprov/ |
| 2 | Linux Tracing Technologies rehberi | https://docs.kernel.org/trace/index.html |

> Bu iki URL envanter kesişimi 0'dır (T-W1…T-W13 ile örtüşmez); kök
> `-dogrulama.md` §2'deki T-W serisi bu dosya §12'de taşınır.

### §16.1 2. tur kayıtları (2026-10-14)

| # | Kategori | Kanıt | Sonuç |
|---|---|---|---|
| 1 | Windows ETW toplama | T-W16 (`logman`), T-W17 (`tracerpt`), T-W18 (ETW portal) | 3 × HTTP 200 (kök §2.1) |
| 2 | macOS trace (ayna) | T-W19 (`dtrace.1`), T-W20 (`fs_usage.1`), T-W21 (`sample.1`), T-W22 (`otool.1`) | 4 × HTTP 200 (kök §3.1) — **ayna dipnotlu** |
| 3 | Apple kernel portalı | T-W23 (`developer.apple.com/documentation/kernel`) | HTTP 200 (kök §3.1) |
| 4 | macOS sürüm kullanılabilirliği | — | `UNKNOWN` — ayna sayfası sürüm bağlamı taşımaz |

> 1. turdaki üç reddedilen aday §12'de korunur (silinmez); ikisi 2. turda
> **doğru yollarla** kapandı (macOS ayna ile, PDH metrik dalında).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
