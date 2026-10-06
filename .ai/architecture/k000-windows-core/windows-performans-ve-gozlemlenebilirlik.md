---
title: "Windows Performans ve Gözlemlenebilirlik — Ölçüm Kapısı, QPC, Event Log ve K12 Sınırı"
type: architecture
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "K000 Windows Core — SSOT: .ai/architecture/k000-windows-core/"
updated: 2026-10-06
---

# Windows Performans ve Gözlemlenebilirlik — Ölçüm Kapısı, QPC, Event Log ve K12 Sınırı

> **K numarası:** K000 — Windows Core. Bu belge, CoreMusic'in **Windows tarafı
> performans ölçüm ve gözlemlenebilirlik** iddialarını tek yerde toplar: hangi
> hedef vardır, hangi ölçüm **yoktur**, ölçüm kapısı neredir ve K12 (İzleme)
> ile sınır neredir. Depoda `*.cpp` / `*.h` sayısı **0** olduğundan tüm kod
> blokları **kavramsal iskelettir**. **Ölçülmüş latency / throughput / süre
> rakamı bu belgede üretilmez** — sayısal satırlar yalnız kaynak belgeden alınır
> ve etiketlenir.

---

## §1 Kapsam ve Bağlam

### §1.1 Dosya İlişkileri

| Dosya | İlişki |
|---|---|
| [[index]] | K000 klasör indeksi |
| [[windows-api-yuzeyi]] | Win32 API yüzeyi — ölçüm çağrılarının yüzeyi (`QueryPerformanceCounter`, `ReportEvent`) |
| [[windows-core-mimari]] | § "Performans Metrikleri" tablosunun sahibi (L424-432) — bu belge o tablonun **kanıt ve ölçüm** katmanıdır |
| [[win32-olay-dongusu-ve-mesaj-kuyrugu]] | Olay döngüsü gecikmesi (`Event loop latency` hedefi §7 tablosunda) |
| [[wasapi-ses-yolu-cekirdek]] | Ses yolu ölçüm yollarını bu belgeye yönlendirir (L424: "ETW, QPC, xrun metrik yolları") |
| [[asio-cekirdek-entegrasyonu]] | xrun sayacı ve ölçüm kapısı (L512 aynı yönlendirme) |
| [[windows-guvenlik-ve-olcullu-kisitlar]] | Audit / Event Log yazımı ile kesişim (L33, L373) |

### §1.2 Kapsam Sınırı

| Kapsar | Kapsamaz |
|---|---|
| Windows performans **hedef** tablolarının kanıt toplanması | **K12 İzleme**'nin toplama/dashboard/pipeline uygulaması → `[KAPSAM DIŞI]` |
| Ölçüm kapısı tanımı (kavramsal: QPC zaman damgası, Event Log yazımı, xrun sayacı) | **K13 CI/CD** metrik/izleme entegrasyonu → `[KAPSAM DIŞI]` |
| RED/USE metrik **sözlüğü** (yalnız tanım) | Prometheus/Grafana/ELK kurulum ve sürüm detayları → `[KAPSAM DIŞI]` (kaynak: K12 belgesi) |
| K0 → K2 → K3 ölçüm sorumluluğu sınırı (matris kanıtlı) | **K5 Veri / K7 Middleware / K8 Servis** metrikleri → `[KAPSAM DIŞI]` |
| K12 ile **sınır** ve yasakların gösterimi | Ölçülmüş p50/p95/p99, xrun sayısı, CPU kullanımı → `[NOT PROVIDED]` (ölçüm yok) |

### §1.3 Etiket Sözlüğü

| Etiket | Anlamı |
|---|---|
| **MEVCUT PROJE GERÇEĞİ** | Dosya yolu + satır kanıtı olan ifade |
| **ARAŞTIRMA REFERANSI** | Depo dışı/genel bilgi — bu oturumda web araştırması **yapılmadı**, rakam eklenmez |
| **ESKİ BELGE İDDİASI** | Kaynak belgede var ama ölçümle doğrulanmamış hedef/rakam |
| `[NOT PROVIDED]` | Depoda verisi yok |
| `⚠️ VERIFICATION REQUIRED` | Doğrulanamayan iddia |
| `[KAPSAM DIŞI]` | Bu belgenin konusu değil |
| **Belirlenecek** | Kaynak belgede "Mevcut" sütunu böyle yazıyor — hedef var, değer yok |

---

## §2 Gömülü Kaynaklar

Aşağıdaki dosyalar bu belge yazılmadan **okunmuştur** (hepsi disk kanıtıdır):

| # | Dosya (okunan) | Bu belgede kullanımı |
|---|---|---|
| 1 | `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-core.md` | Performans tablosu L390-398 · Event Log `ReportEvent` L162-176 · WMI L182 |
| 2 | `_backup\...\architecture\k0-isletim-sistemi\ipc-mekanizmalari.md` | § Performans Metrikleri L626-634 (Named Pipe **< 15μs** L631) |
| 3 | `_backup\...\architecture\k0-isletim-sistemi\threading-model.md` | Performans tablosu L711-720 |
| 4 | `_backup\...\architecture\k0-isletim-sistemi\system-calls.md` | Performans tablosu L673-676 (POSIX mekanizmaları) |
| 5 | `_backup\...\architecture\k0-isletim-sistemi\process-isolation.md` | Performans tablosu L605 |
| 6 | `_backup\...\architecture\k0-isletim-sistemi\cross-platform-api.md` | Event loop latency L640 |
| 7 | `_backup\...\architecture\k0-isletim-sistemi\README.md` | K0-09 Time Service L65 · QPC L115 · "~100ns" L354 · modül indeksi L466-483 |
| 8 | `_backup\...\architecture\k12-izleme\index.md` | Entegrasyon noktaları L48-81 · RED/USE L85-98 · saklama L102-110 · bileşenler L135-143 · bağımlılık iddiası L150 |
| 9 | `_backup\...\architecture\katman-baglilik-matrisi.md` | K2 satırı L91 · K12 satırı L101 · yasak L292 · fallback L268 · derinlik L359 |
| 10 | `.ai\ecosystem\asio-wasapi-rehber.md` | xrun dersi L161 · metrik alanları L386 · §6.4 ölçüm bekleyen L240 · p95 hedef yok L271 |
| 11 | `.ai\architecture\k000-windows-core\windows-core-mimari.md` | Performans tablosu L424-432 · sorumluluk L690 · ölçüm uyarıları L698-699 |
| 12 | `.ai\architecture\k000-windows-core\wasapi-ses-yolu-cekirdek.md` · `asio-cekirdek-entegrasyonu.md` · `windows-guvenlik-ve-olcullu-kisitlar.md` | Kardeş yönlendirmeler (bkz. §1.1) |

> Bu oturumda **web araştırması yapılmamıştır**; hiçbir sürüm, donanım, latency
> veya throughput rakamı yeni üretilmemiştir.

---

## §3 Ölçüm Durumu: Hedef Var, Ölçüm Yok

Bu belgenin ana bulgusu tek cümledir: **vault'ta Windows performansı için hedef
tablolar var, ölçülmüş değer yoktur.**

1. **Hedef tablolar "Belirlenecek" ile bitiyor (MEVCUT PROJE GERÇEĞİ):**
   `windows-core.md` L394-398 tablosunda her satırın "Mevcut" sütunu
   `Belirlenecek` yazıyor. Aynı tablo K000'e taşınmış:
   `windows-core-mimari.md` L426-432 (`| Named Pipe latency | < 10μs | Belirlenecek |`).
2. **Ölçüm adımı açıkça bekleyen iş olarak tanımlanmış (MEVCUT PROJE GERÇEĞİ):**
   `asio-wasapi-rehber.md` L240 sıralamasında "(5) §6.4 gerçek donanımda
   gecikme ölçümü" son adımdadır; L271 "CoreMusic K2 için p95 gecikme hedefi (ms)
   henüz yok — ürün kararı" der.
3. **K000 kardeş belgeler de aynı durumu yazıyor (MEVCUT PROJE GERÇEĞİ):**
   `windows-core-mimari.md` L698 `⚠️ VERIFICATION REQUIRED` — "bu vault'ta ölçüm
   sonucu yok"; L699 aynı damgayla Windows sürüm bazlı large-page izin matrisine
   ölçüm gerektiğini söyler.
4. **Kod kanıtı yok:** depoda `*.cpp` / `*.h` = 0 → hiçbir metrik üretici,
   sayacı veya telemetry kodu fiilen çalışmıyor. Ölçüm kapısı bu nedenle
   **tasarım düzeyindedir**.

> **Kural:** Bu belgede bir sayı gördüğünüzde üç soru sorun: (1) hangi dosyadan
> geldi? (2) hedef mi ölçüm mü? (3) etiketi ne? Cevapsız satır işaretsizdir.

---

## §4 Zaman Kaynağı — QueryPerformanceCounter

### §4.1 Vault Kanıtı

| İddia | Kaynak | Etiket |
|---|---|---|
| `QueryPerformanceCounter` "High-resolution timer" olarak K0 araç setinde | `k0-isletim-sistemi\README.md` L115 | MEVCUT PROJE GERÇEĞİ |
| K0-09 Time Service modülü "QueryPerformanceCounter" kullanır | `README.md` L65 | MEVCUT PROJE GERÇEĞİ |
| `QueryPerformanceCounter ≈ 100ns` | `README.md` L354 (tablo satırı) | **ESKİ BELGE İDDİASI** — ölçüm yok, `⚠️ VERIFICATION REQUIRED` |
| Ölçüm çağrısı için yüksek çözünürlüklü sayaç gereği | `windows-api-yuzeyi` (API yüzeyi) · bu belge §13 | MEVCUT PROJE GERÇEĞİ (kavramsal akış) |

### §4.2 Sınır ve Dürüstlük Notu

1. **~100ns rakamı üretilmez, tekrarlanmaz:** README L354'te geçiyor diye bu
   belge "QPC ~100ns'dir" demez; der ki: *"eski belge ~100ns iddia ediyor,
   bu depoda doğrulama ölçümü yok."* Kumaş ölçümü (`QueryPerformanceFrequency`)
   ve iki okuma farkı **kavramsal** olarak §13'tedir.
2. **K000 sahipliği:** zaman kaynağı okuması bu belgede; **zaman damgasının
   ses yoluyla ilişkilendirilmesi** (buffer dolumu vs callback süresi)
   `windows-core-mimari.md` L690'da `performance-engineer` rolüne atanmış
   ("callback bütçesi ve p95 ölçümü") — çakışma yok, sorumluluk bölünmüştür.
3. **QPC dışında alternatif aranmaz:** vault'ta `GetTickCount64`,
   `QueryUnbiasedInterruptTime` gibi ikinci bir sayaç tanımı **grep ile
   bulunmamıştır** (2026-10-06 ölçümü) → `[NOT PROVIDED]`.

---

## §5 Olay Günlüğü Yazımı — `ReportEvent` ve Yazma Bütçesi

### §5.1 Vault Kanıtı

| İddia | Kaynak | Etiket |
|---|---|---|
| Event Log modülü `ReportEvent(hEventLog, EVENTLOG_INFORMATION_TYPE, ...)` çağrısı içerir | `windows-core.md` L162-176 (modül L162; çağrı L176) | MEVCUT PROJE GERÇEĞİ |
| Modül indeksi: "K0.1.3.13 Event Log — windows-core.md L162" | `README.md` L469 | MEVCUT PROJE GERÇEĞİ |
| `Event Log write | < 5ms | Belirlenecek` | `windows-core.md` L398 · `windows-core-mimari.md` L432 | ESKİ BELGE İDDİASI (hedef) |
| Audit yazımı bu belgeye bağlanmış (çapraz referans) | `windows-guvenlik-ve-olcullu-kisitlar.md` L33, L324, L373, L424 | MEVCUT PROJE GERÇEĞİ |

### §5.2 Yorum (kanıtlı sınırlar içinde)

1. **5ms bütçesi bir heftir, ölçüm değildir:** `Event Log write < 5ms`
   satırının "Mevcut" sütunu `Belirlenecek` olduğundan, gerçek yazma süresi
   `[NOT PROVIDED]`'dir.
2. **Ses yoluna yazma yasağı/uyarısı bu belgede uydurulmaz:** Event Log yazımının
   RT callback içinde yapılıp yapılmayacağına dair bir kural kaynak belgede
   **yoktur** → `⚠️ VERIFICATION REQUIRED` (karar ADR/`performance-engineer`
   alanındadır; komşu belge `asio-cekirdek-entegrasyonu` callback bütçesini
   konu alır).
3. **Güvenlik tarafı örtüşmez:** audit-in *formatı/erişimi*
   `windows-guvenlik-ve-olcullu-kisitlar` içindir; burada yalnız *süre
   bütçesi ve ölçümü* kalır.

---

## §6 Named Pipe Gecikme İddiası — İki Rakam (giriş)

Aynı metrik, iki kaynak belgede **iki farklı hedefle** yazılmış:

| Kaynak | Satır | Değer | "Mevcut" |
|---|---|---|---|
| `k0-isletim-sistemi\windows-core.md` | L397 | `< 10μs` | Belirlenecek |
| `k0-isletim-sistemi\ipc-mekanizmalari.md` | L631 | `< 15μs` | Belirlenecek |
| `k000\windows-core-mimari.md` | L431 | `< 10μs` | Belirlenecek (kopya) |

1. K000'e taşınan değer **10μs**'dir; IPC modülünün kendi tablosu **15μs** der.
2. İki değer de **hedeftir** ve ikisi de `Belirlenecek`'tir — yani hangisinin
   doğru olduğu **ölçümle** belli olacaktır.
3. Ayrıntı, çözüm önerisi ve etiket için bkz. **§18 C1** (bu belgenin
   çelişki kaydı).

> `ipc-mekanizmalari.md` § Performans Metrikleri (L626-634) ayrıca
> `Unix Socket < 10μs` · `Shared Memory throughput > 10GB/s` ·
> `Message Queue < 50μs` · `gRPC < 1ms` satırları taşır — hepsi
> **ESKİ BELGE İDDİASI (hedef)** ve Windows ölçümü değildir.

---

## §7 Thread ve Sistem Çağrısı Gecikme Hedefleri

### §7.1 Windows ile ilgili hedefler

| Hedef | Kaynak | Etiket |
|---|---|---|
| `Thread creation | < 50μs` | `threading-model.md` L715 | ESKİ BELGE İDDİASI (hedef, `Belirlenecek`) |
| `Condition wait/signal | < 1μs` | `threading-model.md` L720 | ESKİ BELGE İDDİASI (hedef, `Belirlenecek`) |
| `Thread Oluşturma | < 0.5ms` | `windows-core.md` L396 · `windows-core-mimari.md` L430 | ESKİ BELGE İDDİASI (hedef) |
| `Process Oluşturma | < 1ms` | `windows-core.md` L394 · `k000 L428` | ESKİ BELGE İDDİASI (hedef) |
| `Bellek Ayırma (Large Page) | < 0.1ms` | `windows-core.md` L395 · `k000 L429` | ESKİ BELGE İDDİASI (hedef) |
| `Capability drop | < 1μs` | `process-isolation.md` L605 | ESKİ BELGE İDDİASI (hedef) |
| `Event loop latency | < 10μs` | `cross-platform-api.md` L640 | ESKİ BELGE İDDİASI (hedef) |

**⚠️ Birim çelişkisi değil, ölçek notu:** `threading-model.md` "Thread creation
< 50μs" derken `windows-core.md` "Thread Oluşturma < 0.5ms" der (500μs). İkisi
de `Belirlenecek` hedefidir ve aynı anda doğru kabul edilebilir (50μs < 500μs);
**hangisinin bağlayıcı olduğu belirsizdir** → `⚠️ VERIFICATION REQUIRED`
(çelişki bütçesi kullanılmadı; kayıt §18 notunda).

### §7.2 Windows dışı hedefler (ayrım zorunlu)

`system-calls.md` L673-676 tablosu `io_uring submit/completion < 1μs`,
`epoll wait < 10μs`, `kqueue wait < 10μs` satırlarını taşır. Bunlar sırasıyla
**Linux** ve **BSD/macOS** mekanizmalarıdır — **Windows performans hedefi
değillerdir**. Bu belgede Windows ölçümü olarak **kullanılmazlar**
(`[KAPSAM DIŞI]` — Windows satırı bu tabloda yoktur).

---

## §8 Metrik Sözlüğü — RED ve USE

K12 (İzleme) belgesi, izleme katmanının **sözlüğünü** tanımlar. Bu belge o
sözlüğü **yalnızca K000 ölçümünün hangi alan adlarını doldurması gerektiğini
göstermek** için alır; uygulama `[KAPSAM DIŞI]`'dir.

| Aile | Alanlar | Kaynak |
|---|---|---|
| **RED** | `Rate` (istek/saniye) · `Errors` (hata oranı/türü) · `Duration` (yanıt süreleri p50/p95/p99) | `k12-izleme\index.md` L85-88 |
| **USE** | `Utilization` · `Saturation` (kuyruk boyutu/doluluk) · `Errors` | `k12-izleme\index.md` L90-93 |
| İş metrikleri | aktif kullanıcı, başarı oranı, süreler | `k12-izleme\index.md` L95-98 |

1. **K000'e düşen alanlar:** `Duration` (callback/buffer gecikmesi —
   `performance-engineer`, `windows-core-mimari.md` L690) ve `Errors`
   (xrun, §9). `Rate`/iş metrikleri K000'ün konusu **değildir**
   (`[KAPSAM DIŞI]`).
2. **p50/p95/p99 ölçümü yok:** hedeflerde p-değeri **hiçbir kaynak belgede
   geçmiyor** — yalnız `asio-wasapi-rehber.md` L271 "p95 gecikme hedefi (ms)
   henüz yok" der → `[NOT PROVIDED]`.
3. **Saklama/çözünürlük politikası** (L102-110: 15 gün/10s … 1 yıl/1h)
   K12'nin konusudur → `[KAPSAM DIŞI]`.

---

## §9 Xrun Metriği ve Üst Katman Yayınlama Sınırı

### §9.1 Vault Kanıtı

| İddia | Kaynak | Etiket |
|---|---|---|
| "Gecikme tek sayı değil — buffer seçimi + sürücü yolu + **xrun** toleransı birlikte sözleşmedir; CoreMusic K2 buffer'ı yapılandırılabilir tutar ve **xrun sayısını metrik olarak verir**" | `asio-wasapi-rehber.md` L161 | MEVCUT PROJE GERÇEĞİ |
| Halka başına: "buffer boyutu yapılandırılabilir + xrun sayacı (metrik: **`driver_path`, `xrun_count`**)" | `asio-wasapi-rehber.md` L386 | MEVCUT PROJE GERÇEĞİ — alan adları kaynakla birebir |
| Gecikme = buffer + sürücü yolu + xrun → sahiplik "K2 (+ K14 metrik)" | `asio-wasapi-rehber.md` L200, L236 | MEVCUT PROJE GERÇEĞİ |
| xrun/telemetri metrik yayını çapraz referansı → `ekosistem-mimarileri.md` §5.3 | `asio-wasapi-rehber.md` L448 | MEVCUT PROJE GERÇEĞİ |
| Kardeş belge: "xrun metriği, ölçüm kapısı" bu belgeye bağlanmış | `asio-cekirdek-entegrasyonu.md` L32, L512 | MEVCUT PROJE GERÇEĞİ |

### §9.2 Sınır Uyarısı (matris karşılaştırması)

1. **Matris K2 satırı yalnız `K2 → K1` okunu taşır** (`katman-baglilik-matrisi.md`
   L91). Rehberin "xrun metriğini K14'e verir" modeli ile matrisin K2 satırı
   arasındaki ilişki **bu oturumda çözülmedi** → `⚠️ VERIFICATION REQUIRED`
   (rehber `ekosistem-mimarileri.md` §5.3'e dayanıyor; o dosya okunmadı).
   **Çelişki olarak sayılmadı** — iki belge farklı düzeyde konuşuyor olabilir
   (yayın yolu vs katman bağımlılığı).
2. **K000 sınırı:** `driver_path` / `xrun_count` alanlarının **üretilmesi**
   K2 (sürücü yolu) içindir; K000 yalnız **ölçüm kapısını** (zaman damgası +
   olay yazımı) ve bu belgenin hedef tablolarını taşır.
3. **xrun sayısı değeri `[NOT PROVIDED]`** — depoda sayaç kodu yok (`*.cpp` = 0).

---

## §10 ETW ve Sayma Yollarının Vault Durumu

| Konu | Vault durumu | Etiket |
|---|---|---|
| **ETW** (Event Tracing for Windows) | Kaynak K0/backup mimari belgelerinde **grep 0 eşleşme** (2026-10-06). Ad yalnız kardeş k000 belgelerinde **bu dosyaya referans** olarak geçer: `wasapi-ses-yolu-cekirdek.md` L424 · `asio-cekirdek-entegrasyonu.md` L512 | `[NOT PROVIDED]` — provider/kullanım tanımı yok |
| **`ReportEvent`** (Event Log) | `windows-core.md` L176 çağrı örneği | MEVCUT PROJE GERÇEĞİ |
| **`QueryPerformanceCounter`** | `README.md` L65, L115 | MEVCUT PROJE GERÇEĞİ |
| **WMI** (Windows Management Instrumentation) | Modül: `windows-core.md` L182 · indeks `README.md` L470 ("K0.1.3.14 WMI") | MEVCUT PROJE GERÇEĞİ (varlık) |
| **Sayaç/Performance Counter API** | Vault'ta tanım **grep ile bulunmadı** | `[NOT PROVIDED]` |
| **Gerçekleşen ölçümler** (CPU/RAM/xrun/latency) | Hiçbir kaynak belgede yok | `[NOT PROVIDED]` |

1. **ETW bu belgede anlatılmaz:** anlatmak uydurma olurdu; kapı, kardeş
   belgelerin yönlendirmesiyle **bu dosyada açıkça boş bırakılmıştır**
   (etiket: `[NOT PROVIDED]`).
2. **WMI yalnız varlık olarak kayıtlı:** `Win32_SoundDevice` gibi spesifik
   sınıf adları kaynak belgede **geçmiyor** → bu belgede yazılmaz.

---

## §11 K12 (İzleme) ile Sınır ve Entegrasyon Noktaları

### §11.1 K12'nin K000'e bakan yüzü (kaynak: `k12-izleme\index.md`)

| Nokta | K12'nin dediği | Satır |
|---|---|---|
| K0 entegrasyonu | "Sistem metrikleri (CPU, RAM, disk) · Process izleme · Ağ istatistikleri" | L48-51 |
| K3 entegrasyonu | "Ses işleme metrikleri · Buffer durumu · Latans bilgileri" | L63-66 |
| Metrik aileleri | RED / USE (§8) | L85-98 |
| Bileşenler | Prometheus 2.47+ · Grafana 10.0+ · Alertmanager 0.26+ · Jaeger 1.50+ · OpenTelemetry 1.0+ | L137-143 |
| **Bağımlılık iddiası** | "**Bağımlılıklar**: K0, K3, K5 katmanlarının metrik export etmesi gerekmektedir." | **L150** |

### §11.2 Matrisin K12'ye dediği (kaynak: `katman-baglilik-matrisi.md`)

| Kural | Satır |
|---|---|
| K12 satırındaki **tek ok → K8** ("K12 → K8 tek resmi bağımlılık") | L101, L115, L135, L217 |
| **K12 → K0 yasak** ("Doğrudan OS erişimi izinsiz değişiklik riski" — **ORTA**) | L292 · şema L327 |
| K12 derinliği 5: `K12 → K8 → … → K0` | L359 |
| K12 veri kesintisi fallback'i: "Buffered metrics (çevrimdışı toplama), < 60 saniye" | L268 |

### §11.3 Sonuç (bu belge açısından)

1. **İki belge aynı şeyi istemiyor:** K12, K0'dan (ve K3/K5'ten) **metrik
   export** isterken matris, K12 → K0 bağımlılığını **ORTA seviye yasak**
   sayıyor ve tek resmi bağımlılığı **K12 → K8** olarak tanımlıyor.
   Ayrıntı ve etiket için bkz. **§18 C2**.
2. **K000'ün bu çelişkideki tutumu:** ölçüm üretimi K000'ün **iç işi**
   olabilir (kendi iç testi), ancak metriklerin **K12'ye akışı** matristeki
   oklarla tanımlanana kadar bu belge **hiçbir K0 → K12 export akışı
   tasarlamaz** → `[KAPSAM DIŞI]` + `⚠️ VERIFICATION REQUIRED`.
3. **K3 bağlantısı:** "Buffer durumu / Latans bilgisi" (K12 L63-66) ile
   K000'in WASAPI/ASIO kardeş belgelerinin ölçüm alanları aynı kavramlardır;
   sahiplik çakışması yok: **üretim K2/K3, sınır K000, toplama K12.**

---

## §12 Konsolide Hedef Tablosu ("Belirlenecek" kümeleri)

| # | Hedef | Değer | Kaynak | Etiket |
|---|---|---|---|---|
| 1 | Process Oluşturma | < 1ms | `windows-core.md` L394 · `k000 L428` | ESKİ BELGE İDDİASI |
| 2 | Bellek Ayırma (Large Page) | < 0.1ms | `windows-core.md` L395 · `k000 L429` | ESKİ BELGE İDDİASI |
| 3 | Thread Oluşturma | < 0.5ms | `windows-core.md` L396 · `k000 L430` | ESKİ BELGE İDDİASI |
| 4 | Named Pipe latency | < 10μs (**≠ ipc L631: 15μs**) | `windows-core.md` L397 · `k000 L431` | ESKİ BELGE İDDİASI → **§18 C1** |
| 5 | Event Log write | < 5ms | `windows-core.md` L398 · `k000 L432` | ESKİ BELGE İDDİASI |
| 6 | Thread creation | < 50μs | `threading-model.md` L715 | ESKİ BELGE İDDİASI |
| 7 | Condition wait/signal | < 1μs | `threading-model.md` L720 | ESKİ BELGE İDDİASI |
| 8 | Event loop latency | < 10μs | `cross-platform-api.md` L640 | ESKİ BELGE İDDİASI |
| 9 | Capability drop | < 1μs | `process-isolation.md` L605 | ESKİ BELGE İDDİASI |
| 10 | io_uring / epoll / kqueue | 1μs / 10μs / 10μs | `system-calls.md` L673-676 | `[KAPSAM DIŞI]` (Windows dışı) |
| 11 | QPC çözünürlüğü | ~100ns | `README.md` L354 | ESKİ BELGE İDDİASI (`⚠️`) |
| 12 | p95 gecikme hedefi | **yok** | `asio-wasapi-rehber.md` L271 | `[NOT PROVIDED]` |
| 13 | xrun sayısı / ölçülmüş latency | **yok** | depoda `*.cpp` = 0 | `[NOT PROVIDED]` |

**Ortak sütun değeri:** satır 1-9'un "Mevcut" sütunu kaynak belgede
`Belirlenecek`'tir → **hepsi hedeftir, ölçüm değildir.**

---

## §13 Kavramsal Ölçüm Akışı (iskelet — DEĞİL ölçüm)

> ⚠️ Aşağıdaki blok **kavramsal iskelettir**; derlenebilirlik, API imzası ve
> sayısal doğruluk iddiası **taşımez**. Depoda `*.cpp` = 0.

```cpp
// KAVRAMSAL — kanıt: README.md L115 (QPC = high-resolution timer)
// Zaman damgası al ve iki okuma arasındaki farkı hesapla.
LARGE_INTEGER freq, t0, t1;
QueryPerformanceFrequency(&freq);   // kumaş — README.md L354 (~100ns iddiası: ESKİ BELGE)
QueryPerformanceCounter(&t0);
// ...ölçülen bölge (ör. bir Event Log yazımı veya callback süresi)...
QueryPerformanceCounter(&t1);
// delta_ns = (t1 - t0) * 1e9 / freq  →  SONUÇ: raporlanır, eşiğe YORULMAZ.
```

```text
KAVRAMSAL OLAY YAZIMI — kanıt: windows-core.md L176 (ReportEvent çağrısı)
ReportEvent(hEventLog, EVENTLOG_INFORMATION_TYPE, 0, 0, ...)
  → yazma süresi ölçülür; hedef < 5ms (windows-core.md L398 — Belirlenecek)
  → ölçüm sonucu bu depoda YOK: [NOT PROVIDED]
```

```text
KAVRAMSAL SAYAÇ ÇIKTISI — alan adları kanıt: asio-wasapi-rehber.md L386
{ driver_path: <yapılandırma>, xrun_count: <sayı> }
  → xrun_count değeri: [NOT PROVIDED] (sayaç kodu yok)
```

**Kural:** üç blok da yalnız **akışın şeklini** gösterir; içlerindeki hiçbir
yorum satırı ölçüm sonucu değildir.

---

## §14 Kardeş Belgelerle Sorumluluk Paylaşımı

| Konu | Sahip (K000) | Bu belgenin rolü |
|---|---|---|
| Performans tablosunun kendisi | `windows-core-mimari.md` L424-432 | Tablonun **kanıt/hedef/ölçüm** ayrımını taşır (§12) |
| Event Log yazımı (süre bütçesi) | bu belge (§5) | `windows-guvenlik-ve-olcullu-kisitlar` L373 ile çapraz bağlantı |
| QPC / ETW / xrun yolları | `wasapi-ses-yolu-cekirdek.md` L424 · `asio-cekirdek-entegrasyonu.md` L512 → **bu belgeye** yönlendirir | §4, §9, §10 |
| Callback bütçesi + p95 ölçümü | `performance-engineer` rolü (`windows-core-mimari.md` L690) | Ölçüm **kapısı** (§13) burada; **hedef kararı** orada |
| Olay döngüsü gecikmesi hedefi | `win32-olay-dongusu-ve-mesaj-kuyrugu` | §7/§12 satır 8 olarak kayıtlı |
| Audit formatı/erişimi | `windows-guvenlik-ve-olcullu-kisitlar` | yalnız süre ölçümü (§5) |
| Metrik toplama/dashboard | K12 → `[KAPSAM DIŞI]` | §11 sınırı |

---

## §15 Uygulama Kontrol Listesi

1. [ ] Ölçüm eklenirken `Belirlenecek` hedefi mi, ölçüm mü olduğu **etiketlendi mi**? (§12)
2. [ ] Zaman kaynağı `QueryPerformanceCounter` mı, kaynak `README.md` L115 ile aynı mı? (§4)
3. [ ] Event Log yazımı `windows-core.md` L176 deseniyle mi, süre bütçesi 5ms hedefine mi referans? (§5)
4. [ ] Named Pipe hedefi seçilirken **çelişki C1** (10μs/15μs) dikkate alındı mı, uydurulmuş üçüncü bir değer yok mu? (§6, §18)
5. [ ] Metrik alan adları `driver_path` / `xrun_count` ve RED/USE sözlüğüyle mi? (§8, §9)
6. [ ] K12'ye export akışı **matristeki oklarla** mı (K12 → K8), yoksa yeni ok **onaysız** mı eklendi? (§11)
7. [ ] ETW kullanılırsa önce `⚠️ VERIFICATION REQUIRED` kalktı mı? (§10)
8. [ ] Ölçüm sonucu eklendiğinde bu belgedeki `[NOT PROVIDED]` satırları güncellendi mi? (§3)

---

## §16 Yaygın Hata Senaryoları

| # | Hata | Sonuç | Düzeltme |
|---|---|---|---|
| 1 | Hedefi ölçüm sanmak (`< 10μs` okuyup "ölçtük" demek) | Yalan iddia | Etiket: ESKİ BELGE İDDİASI + `Belirlenecek` (§12) |
| 2 | QPC ~100ns'i sabit kabul etmek | Ölçüm yokken sayı üretmek | `README.md` L354'idir, `⚠️` damgalıdır (§4) |
| 3 | ETW hakkında detay yazmak | Kaynaksız anlatım | `[NOT PROVIDED]` bırak; kapı açık kalsın (§10) |
| 4 | K12'ye doğrudan metrik akışı tasarlamak | Matris ihlali (K12 → K0 ORTA) | §11 + §18 C2; akış yalnız matris oklarıyla |
| 5 | `system-calls.md` POSIX hedeflerini Windows ölçümü saymak | Yanlış platform | `[KAPSAM DIŞI]` (§7.2) |
| 6 | xrun/latency değeri uydurmak | Zero-Hallucination ihlali | `[NOT PROVIDED]` (§9, §12 satır 13) |

---

## §17 İddia → Kanıt Tablosu

| İddia | Kanıt (dosya + satır) | Durum |
|---|---|---|
| Windows hedef tablosu 5 satır, hepsi "Belirlenecek" | `windows-core.md` L394-398 | ✅ |
| Aynı tablo K000'de mevcut | `windows-core-mimari.md` L426-432 | ✅ |
| Named Pipe 10μs / 15μs iki değer | `windows-core.md` L397 · `ipc-mekanizmalari.md` L631 | ✅ → C1 |
| K12 "K0, K3, K5 metrik export" ister | `k12-izleme\index.md` L150 | ✅ |
| Matris K12 → K8 tek ok; K12 → K0 yasak ORTA | `katman-baglilik-matrisi.md` L101/L115/L135 · L292 | ✅ → C2 |
| QPC high-resolution timer | `README.md` L115, L65 | ✅ |
| `ReportEvent` örneği | `windows-core.md` L176 | ✅ |
| xrun metrik alan adları `driver_path`, `xrun_count` | `asio-wasapi-rehber.md` L386 | ✅ |
| p95 hedefi yok, ölçüm bekliyor | `asio-wasapi-rehber.md` L271, L240 | ✅ |
| ETW kaynak belgede yok | grep (backup mimari) 0 eşleşme · yalnız k000 yönlendirmeleri L424/L512 | ✅ (`[NOT PROVIDED]`) |
| Ölçülmüş performans değeri | — | ❌ yok → `[NOT PROVIDED]` |
| K2 → K14 metrik yolu | `asio-wasapi-rehber.md` L200/L236 vs matris K2 satırı L91 (yalnız → K1) | ⚠️ VERIFICATION REQUIRED (§9.2) |

---

## ÇELİŞKİ / DOĞRULAMA

> **Dosya çelişki sayısı: 2** (dosya limiti ≤ 5 · görev toplam limiti 10).

### C1 — Named Pipe latency hedefi: `< 10μs` vs `< 15μs`

| | |
|---|---|
| **Kaynak A** | `_backup\architecture\k0-isletim-sistemi\windows-core.md` **L397**: `| Named Pipe latency | < 10μs | Belirlenecek |` (K000'e taşınmış: `windows-core-mimari.md` **L431**, aynı değer) |
| **Kaynak B** | `_backup\architecture\k0-isletim-sistemi\ipc-mekanizmalari.md` **L631**: `| Named Pipe latency | < 15μs | Belirlenecek |` (§ Performans Metrikleri L626-634) |
| **Çelişki** | Aynı metrik, iki kaynak modülde **farklı hedef** (< 10μs ≠ < 15μs); ikisi de "Mevcut: Belirlenecek" |
| **Etki** | Orta — iki değer de **hedeftir**, ölçüm yok; hangisinin bağlayıcı olduğu belirsiz. Ölçüm yapılmadan "Named Pipe ≤ Xμs" cümlesi kurulamaz |
| **Etiket** | **ESKİ BELGE İDDİASI** (her iki taraf) · `⚠️ VERIFICATION REQUIRED` |
| **Çözüm önerisi** | Tek bir bağlayıcı hedef seçilip (`performance-engineer` + kullanıcı onayı) diğer dosyada çapraz not bırakılmalıdır; **bu belge düzeltmez** |

### C2 — K12 metrik export iddiası: matrisin K12 → K0 yasağı ile çelişiyor

| | |
|---|---|
| **Kaynak A** | `_backup\architecture\k12-izleme\index.md` **L150**: "**Bağımlılıklar**: K0, K3, K5 katmanlarının metrik export etmesi gerekmektedir." (aynı dosya L48-51 K0 entegrasyonunu da sayar) |
| **Kaynak B** | `_backup\architecture\katman-baglilik-matrisi.md` **L101/L115/L135/L217**: K12 satırındaki tek ok → **K8** ("K12 → K8 tek resmi bağımlılık") · **L292**: `| 10 | K12 → K0 | İzleme → OS | Doğrudan OS erişimi izinsiz değişiklik riski | ORTA |` · şema L327 |
| **Çelişki** | K12 belgesi, K0'dan (doğrudan) metrik export beklerken matris, K12 → K0'ı **yasaklıyor (ORTA)** ve K12'nin tek bağımlılığını **K8** gösteriyor; ayrıca `L56` "matriste olmayan izin yoktur" kuralını ekler |
| **Etki** | Yüksek — ölçüm/telemetri akışının **yönü** mimari denetimde (Layer Violation) tutarsız sonuç üretir |
| **Etiket** | `⚠️ VERIFICATION REQUIRED` — "export" kelimesinin matriste "gösterim verisi" gibi istisna olarak kaydedilip kaydedilmediği **araştırılmalı** (bkz. matris §3.3'teki K11 → K12 istisna örneği, L232-234) |
| **Çözüm önerisi** | Ya K12 belgesi "export"u **K8 üzerinden** (K2/K3 → K8 → K12 zinciri) tarif etmeli, ya matriste K0 → K12 **veri akışı** için açık istisna tanınmalıdır. Karar: mimari + kullanıcı onayı |

**Doğrulama notu:** İki çelişki de bu oturumda **disk kanıtıyla** (dosya + satır)
tespit edilmiştir; üçüncü bir çelişki **bulunmamıştır** (threading 50μs/0.5ms
ölçek farkı §7.1'de `⚠️` olarak kayıtlıdır, çelişki sayılmamıştır).

---

## Kaynaklar

Bu belge aşağıdaki dosyalar **okunarak** üretildi (tam yollar):

1. `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-core.md`
2. `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\ipc-mekanizmalari.md`
3. `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\threading-model.md`
4. `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\system-calls.md`
5. `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\process-isolation.md`
6. `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\cross-platform-api.md`
7. `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\README.md`
8. `_backup\arch-2026-10-06_1057\architecture\k12-izleme\index.md`
9. `_backup\arch-2026-10-06_1057\architecture\katman-baglilik-matrisi.md`
10. `.ai\ecosystem\asio-wasapi-rehber.md`
11. `.ai\architecture\k000-windows-core\windows-core-mimari.md`
12. `.ai\architecture\k000-windows-core\wasapi-ses-yolu-cekirdek.md` (yönlendirme satırları)
13. `.ai\architecture\k000-windows-core\asio-cekirdek-entegrasyonu.md` (yönlendirme satırları)
14. `.ai\architecture\k000-windows-core\windows-guvenlik-ve-olcullu-kisitlar.md` (çapraz referanslar)
15. `grep` ölçümleri: backup mimari ağacında `ETW` (0 eşleşme) · `μs` hedefleri · `Belirlenecek` (2026-10-06)

**Doğrulama durumu:** Bu belgede **yeni sürüm/ölçüm/latency rakamı
üretilmemiştir**; tüm sayısal satırlar 1-15'teki kaynaklardan alınmış ve
etiketlenmiştir. Ölçüm yapıldığında §3, §10 ve §12'deki `[NOT PROVIDED]`
satırları güncellenir.

**Çelişki sayısı: 2 (C1, C2)** · Mevcut K000 dosyalarına (index, windows-api-yuzeyi, windows-core-mimari) **dokunulmamıştır** · Commit: orkestratöre aittir.
