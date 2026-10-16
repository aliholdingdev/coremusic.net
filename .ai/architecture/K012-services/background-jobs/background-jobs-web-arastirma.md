---
node_id: "K012-services/background-jobs/background-jobs-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K012-services/background-jobs"
children: []
depends_on:
  - "services-dogrulama.md"
  - "index.md"
evidence:
  - "10 URL — HTTP 200 (2026-10-16)"
  - "kesişim testi: url-envanter.txt — çakışma 0"
  - "kesişim testi: url-inventory.txt — çakışma 0"
  - "K008 playback-web-arastirma (10 URL) ile eşleşme 0 · K012 services-dogrulama §5 (15 URL) ile eşleşme 0 · kardeş playback-service-web-arastirma ile eşleşme 0"
validation:
  - "2026-10-16 — 10 benzersiz URL; her biri HTTP HEAD ile 200 doğrulandı (2026-10-16)"
  - "Kesişim taraması iki envanter dosyasında da yapıldı; isabet 0 (envanter çakışması 9 URL §3'te gerekçelendirildi)"
open_questions:
  - "Q5 (services-dogrulama §4 / services-master §7/6) bu dosyada KAPANMAZ — yalnız seçenek sunulur; motor seçimi ADR gerektirir"
  - "Outbox pattern için resmi/incelenebilir birincil kaynak bulunamadı (en.wikipedia.org/wiki/Outbox_pattern → 404) → ⚠️ VERIFICATION REQUIRED"
  - "Idempotency için https://en.wikipedia.org/wiki/Idempotence (HTTP 200, kesişim 0) doğrulandı ama 10-URL kontenjanına sığmadı — Q5 kapanırken eklenebilir"
group: kanit
---

# background-jobs — Web Araştırması (K012)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-16
> 10 URL **yalnız bu dosyada** (K012 içi + kardeş dosyalar çakışması 0).
> Doğrulama: **2026-10-16 (HTTP HEAD, hepsi 200)** · Kesişim: url-envanter.txt (2701 satır) + url-inventory.txt (2761 satır) → **0**.
> Konu: **Q5 — PHP'de arka plan/zamanlanmış iş motoru seçenekleri** (DB kuyruk/outbox · Redis · AMQP · cron/systemd).

## §1 KAYNAK TABLOSU

| W | URL | Ne için (bu dosyanın iddiası) | HTTP 200 |
|---|-----|-------------------------------|----------|
| W-1 | https://www.php.net/manual/en/features.commandline.php | PHP CLI çalıştırma yüzeyi — cron/daemon tarafından çağrılan uzun ömürlü iş süreci temeli | 200 |
| W-2 | https://man7.org/linux/man-pages/man5/crontab.5.html | crontab biçim/dosya tanımı — zamanlanmış iş (scheduler) seçeneğinin resmi tanımı | 200 |
| W-3 | https://symfony.com/doc/current/messenger.html | Symfony Messenger: transport (AMQP/Redis/DB), retry ve failed-job kavramları — framework iş motoru referansı | 200 |
| W-4 | https://laravel.com/docs/12.x/queues | Laravel Queues: kuyruk connection/driver, deneme (retry), başarısız iş saklama — framework iş motoru referansı | 200 |
| W-5 | https://redis.io/docs/latest/develop/data-types/lists/ | Redis list — BLPOP tabanlı basit FIFO kuyruk (seçenek B taşıyıcısı) | 200 |
| W-6 | https://redis.io/docs/latest/develop/data-types/streams/ | Redis Stream — consumer group, at-least-once işleme ve yeniden tüketim (seçenek B ileri düzey) | 200 |
| W-7 | https://www.rabbitmq.com/tutorials | RabbitMQ resmi tutorial seti — AMQP kuyruk/exchange/topoloji temelleri (seçenek C) | 200 |
| W-8 | https://www.rabbitmq.com/docs/dlx | Dead-letter exchange — hatalı iş yönlendirme, retry/ölü-kuyruk davranışı (retry-dead-letter pratiği) | 200 |
| W-9 | https://12factor.net/backingservices | Backing services — kuyruğun (DB, broker) süreçten bağımsız, değiştirilebilir kaynak olarak ele alınması | 200 |
| W-10 | https://en.wikipedia.org/wiki/Message_queue | Kuyruk genel kavramı — iş motoru ile veritabanı tabanlı kuyruk arasındaki ayrımın çerçevesi (seçenek A) | 200 |

## §2 Q5 SEÇENEK MATRİSİ

services-dogrulama §4 Q5 · services-master §7/6 (background-jobs motoru UNKNOWN).

| Seçenek | Kaynak (W-x) | Artı | Eksi | Etkilenen dosya |
|---|---|---|---|---|
| A — **DB tabanlı kuyruk / outbox**: MySQL tablo kuyruğu + CLI worker (aynı PDO/şema üzerinde) | W-10, W-9, W-1 | Yeni altyapı yok; transaction ile atomik iş kaydı; K012 mevcut MySQL kanıtına (services-dogrulama §6) oturur | Tablo tarama/lock yükü; ölçek ve sıralama/garanti için ek index şart; outbox deseni için birincil kaynak bulunamadı (§3) | background-jobs-anayasa · background-jobs-surucu · K007-data şema · services-master §7/6 |
| B — **Redis kuyruk**: list (BLPOP) veya stream (consumer group) | W-5, W-6, W-9 | Düşük gecikme; stream ile at-least-once + yeniden tüketim; broker bağımsız kaynak (W-9) | Ek servis bağımlılığı; kalıcılık/ölçüm ayrı ele alınmalı; envanterdeki diğer Redis sayfaları kullanılamaz (§3) | background-jobs-surucu · background-jobs-ornekler · services-bagimliliklar |
| C — **AMQP (RabbitMQ)**: exchange/topoloji + dead-letter ile retry | W-7, W-8, W-9 | Endüstri standardı kuyruk; DLX ile ölü-kuyruk/tekrar davranışı hazır (W-8) | Yeni broker işletimi; PHP istemcisi (kütüphane) seçimi ayrıca doğrulanmalı → ⚠️ | background-jobs-anayasa · service-contracts/* · services-master §7/6, §7/9 |
| D — **cron / systemd timer + PHP CLI worker**: zamanlanmış süreç, kuyruk yok | W-2, W-1 | En basit; yeni bağımlılık yok; PHP CLI (W-1) ile doğrudan uygulanır | Kuyruk/öncelik/tekrar yok; çakışan koşum (overlapping run) riski kullanıcıda | background-jobs-surucu · devops deployment/yaml |
| E — **Framework iş kütüphanesi** (Symfony Messenger / Laravel Queue tarzı transport+retry katmanı) | W-3, W-4 | Transport, retry ve failed-job kavramları hazır ve belgelenmiş | CoreMusic framework kullanmaz (AGENTS §18/7 — bağımlılık artışı uyarısı); Vanilla PHP'ye taşıma maliyeti | service-contracts/* · background-jobs-anayasa · kök AGENTS §18 |

**Öneri-seviyesi (tek yorum — KARAR DEĞİL):** Kanıt yoğunluğu sırası **A > D > B > C > E**; A ve D mevcut PHP/MySQL/cron kanıt tabanına (W-1, W-2, W-10) doğrudan oturur, B/C ek servis, E ise vault kuralıyla (framework yasağı) gerilim içindedir. Bu bir karar değildir — **karar ADR'ye aittir** (services-master §7/6 → ⚠️ UNKNOWN korunur).

## §3 REDDEDİLEN ADAYLAR

| Aday | Neden (RED) | Tarih |
|---|---|---|
| https://en.wikipedia.org/wiki/Outbox_pattern | HTTP **404** — outbox için birincil kaynak bulunamadı → ⚠️ VERIFICATION REQUIRED | 2026-10-16 |
| https://12factor.net/backingservices | HTTP **404** — slug yazım hatası; doğru adres W-9 (backing-services) | 2026-10-16 |
| https://redis.io/docs/latest/develop/pubsub/ | **Envanter çakışması** — url-envanter.txt 1886 · url-inventory.txt 1936 | 2026-10-16 |
| https://redis.io/docs/latest/develop/data-types/strings/ | **Envanter çakışması** — url-envanter.txt 1885 · url-inventory.txt 1935 | 2026-10-16 |
| https://redis.io/docs/latest/develop/caching-patterns/ | **Envanter çakışması** — url-envanter.txt 1884 · url-inventory.txt 1934 | 2026-10-16 |
| https://redis.io/docs/latest/ | **Envanter çakışması** — url-envanter.txt 1883 · url-inventory.txt 1933 | 2026-10-16 |
| https://redis.io/docs/latest/develop/use/pipelining/ | **Envanter çakışması** — url-envanter.txt 1890 · url-inventory.txt 1940 | 2026-10-16 |
| https://redis.io/docs/latest/develop/ttl/ | **Envanter çakışması** — url-envanter.txt 1889 · url-inventory.txt 1939 | 2026-10-16 |
| https://systemd.io/ ve https://systemd.io/CGROUP_DELEGATION/ | **Envanter çakışması** — url-envanter.txt 1993-1994 · url-inventory.txt 2040-2041 | 2026-10-16 |
| https://www.freedesktop.org/software/systemd/man/latest/journalctl.html | **Envanter çakışması** — url-envanter.txt 2137 · url-inventory.txt 2181 | 2026-10-16 |
| https://www.freedesktop.org/software/systemd/man/latest/systemd.timer.html | HTTP 200 ve kesişim 0 — **kontenjan dışı** (10 URL sınırı; cron tarafı W-2 ile karşılanıyor) | 2026-10-16 |
| https://man7.org/linux/man-pages/man1/crontab.1.html | HTTP 200 ve kesişim 0 — **kontenjan dışı** (W-2 man5 biçim tanımı tercih edildi) | 2026-10-16 |
| https://www.rabbitmq.com/docs/confirms | HTTP 200 ve kesişim 0 — **kontenjan dışı** (W-7 + W-8 AMQP kapsamını karşılıyor) | 2026-10-16 |
| https://www.rabbitmq.com/tutorials/tutorial-one-python | HTTP 200 — **kontenjan dışı**; W-7 (tutorial kökü) Python'a özgü tekil tutorial yerine tercih edildi | 2026-10-16 |
| https://en.wikipedia.org/wiki/Idempotence | HTTP 200 ve kesişim 0 — **kontenjan dışı**; Q5 kapanışında eklenebilir (frontmatter open_questions) | 2026-10-16 |

## §4 KAPSAM DIŞI + KARDEŞ POINTERLAR

| Konu | Nereye gitti |
|---|---|
| Servisler arası çağrının gerçek taşıyıcısı (HTTP/CLI/queue) | services-dogrulama §4/Q9 — bu dosyanın değil |
| Retry/timeout/kırma politikası (servis seviyesi) | [[services-master]] §14 — iş motoru D seçeneğiyle ayrışır |
| Uygulama içi çok adımlı akış | K011/workflows (background-jobs/index §Kapsam) |
| "Ne çalıyor / sıra / konum" sahipliği (Q2) | [[playback-service/playback-service-web-arastirma]] — URL'leri o dosyada |
| K008 playback oynatma/streaming URL'leri | K008-domain/playback/playback-web-arastirma — tekrarlanmadı |

**Kardeş dosyalar:** [[background-jobs/index]] · [[background-jobs/background-jobs-anayasa]] · [[background-jobs/background-jobs-surucu]] · [[background-jobs/background-jobs-ornekler]]

---

## §5 DEĞİŞİKLİK KAYDI

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-16 | İlk yazım — 10 URL (HEAD 200), kesişim 0, K012 §5 / K008 / kardeş dosya çakışma 0 | Researcher subagent (git commit YASAK — commit orkestratöre aittir) |
| 2026-10-16 | 16 aday / 15 satır §3'te gerekçesiyle elendi (envanter 9 · 404 2 · kontenjan 5) | Researcher subagent |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode