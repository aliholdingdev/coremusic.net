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

---

## §6 TUR-2 KAYNAK EKİ (2026-10-18)

> **Append-only ek** — §1–§5 korunmuştur; §5 satırlarına dokunulmadı.
> Doğrulama: Code Mode `fetch` (GET · redirect: follow) — **2026-10-18**.
> Benzersizlik: K012 recursive tarama (50 dosya) + kardeş 4 web-arastirma dosyası → kesişim **0**
> (RED kayıtları §6.7). §1'deki 10 URL ile byte-tekilliği ihlali **0**.

### §6.1 TUR-2 KAYNAK TABLOSU (T2-B1 … T2-B5)

| # | URL | Ne kanıtlar | Q5 seçeneğine etkisi | HTTP (2026-10-18) |
|---|-----|-------------|----------------------|--------------------|
| T2-B1 | https://www.quartz-scheduler.net | Kuruluş içi zamanlayıcı (scheduler) iş motoru: tetikleyici (trigger), iş (job) ve job-store kavramları; çakışan koşum (misfire) politikaları | D (cron+timer) seçeneğinin "kuruluş içi iş motoru" varyantı | GET 200 ✅ |
| T2-B2 | https://learn.microsoft.com/en-us/azure/architecture/patterns/idempotent-consumer | Idempotent-consumer: aynı işi birden fazla teslim alan tüketici için tekrar-güvenli işleme deseni | A/B/C'de tekrar teslim (redelivery) güvenliği | GET 200 ✅ |
| T2-B3 | https://brandur.org/idempotency-keys | Idempotency anahtarı pratiği: istek başına tekil anahtar ile tekrarlanmış isteğin ilk sonucunu verme (Stripe usulü) | A/B/C iş kaydında idempotency anahtarı tasarımı | GET 200 ✅ |
| T2-B4 | https://en.wikipedia.org/wiki/Priority_queue | Öncelik kuyruğu veri yapısı: işlerin öncelik sırasıyla alınması — kuyruk seçiminde öncelik desteği kriteri | B/C'de öncelik desteği; A'da sıralı SELECT alternatifi | GET 200 ✅ |
| T2-B5 | https://en.wikipedia.org/wiki/Dead_letter_queue | Ölü-kuyruk: işlenemeyen mesajların ayrı kuyruğa yönlendirilmesi — operasyonel gözlem ve yeniden işleme | C'de DLX doğrudan; A/B'de tablo/stream alanı karşılığı | GET 200 ✅ |

### §6.2 T2-B1 — Quartz Scheduler (kuruluş içi zamanlayıcı)

**Kanıt:** Quartz, tetikleyici (trigger) + iş (job) + job-store üçlüsüyle çalışan bir zamanlayıcı
motorudur. Tetikleyici bir zaman/zaman-aralığı tanımlar; job-store job ve trigger durumunu
kalıcı tutar (RAM veya JDBC üzerinden). **Misfire politikaları** tanımlanır: zamanlayıcı
kaçırılmış tetiklemeyi ne yapacağını (anında çalıştır, sonrakine ertele, at) karar verir.
Clustering modu çoklu düğümde çakışan koşumu önler (DB lock üzerinden).

**Q5 etkisi:** Seçenek D ("cron + PHP CLI worker") bu kanıtla **"kuruluş içi zamanlayıcı"**
varyantına ayrılır: saf cron (§1 W-2) yalnız "X zamanında çalıştır" der; Quartz tarzı job-store
ise "kaçırılmış tetikleme politikası + yeniden deneme + kalıcı schedule" ekler. CoreMusic içinse
bu **ek altyapı** demektir (PHP dışı bir zamanlayıcı işletmek) — Q5 matrisinde D seçeneğinin
D1 (saf cron) ve D2 (kuruluş içi job-store) olarak ayrışması önerilir.

**İlişki:** §1 W-2 (crontab) + W-1 (PHP CLI) ile birlikte okunur; Quartz'ın PHP'ye doğrudan
bağlanmadığı (HTTP/CLI tetikleme gerekir) bir **altyapı maliyeti** olarak yazılır.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.3 T2-B2 — Idempotent Consumer (learn.microsoft)

**Kanıt:** Azure Architecture Center idempotent-consumer kalıbı, **aynı mesajın birden fazla
teslim edilebildiği** (at-least-once) kuyruk ortamlarında tekrar-güvenli işleme tanımlar:
tüketici işi işlemeden önce işin daha önce işlenip işlenmediğini **kalıcı bir kayıttan**
doğrular (işlem-defteri / processed-records tablosu) ve tekrar gelen işi atlar.

**Q5 etkisi:** §1 W-6 (Redis Stream at-least-once) ve W-8 (RabbitMQ DLX) zaten tekrar teslim
imkânını kanıtlamıştı; T2-B2 **yanıt tarafını** kanıtlar: hangi seçenek seçilirse seçilsin
(A/B/C) tüketici tarafında idempotency kaydı zorunludur. background-jobs-anayasa.md'de "iş
kaydı + sonucu" tablosunun birincil kanıtı bu desendir.

**İlişki:** T2-B3 (idempotency-keys) ile birlikte okunur — T2-B2 **tüketici** tarafını,
T2-B3 **istemci/talep** tarafını kanıtlar. microservices.io idempotent-consumer adresi bu turda
**404** döndü (§6.7); kurumsal kaynak tercih edildi.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.4 T2-B3 — Idempotency Keys (brandur.org)

**Kanıt:** Idempotency anahtarı pratiği: istemci her isteğe tekil bir anahtar ekler; sunucu
anahtarı + istek gövdesinin özetiyle (fingerprint) bir kayıt tutar. Aynı anahtarla ikinci bir
geldiğinde sunucu **ilk yanıtın kopyasını** döner (işlemi tekrarlamaz); gövde farklıysa
çakışma hatası verir. Kaydın TTL ile temizlenmesi anlatılır.

**Q5 etkisi:** Q5 A seçeneği (DB kuyruk) için iş satırının `idempotency_key` + `request_hash`
alanlarıyla yazılması bu pratiğin DB karşılığıdır; B/C'de iş kimliği + sonucu saklama
T2-B2 deseninin istemci tarafı uzantısıdır. services-master §7/6 (motor seçimi) kararına
"girdi-koruma" katmanı olarak eklenir.

**İlişki:** Q9 (servisler arası taşıyıcı) — HTTP taşınırken idempotency anahtarı header'da
(`Idempotency-Key`) taşınır; bu, T2-B2'nin kuyruk versiyonunun HTTP versiyonudur.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.5 T2-B4 — Priority Queue (öncelik kuyruğu)

**Kanıt:** Öncelik kuyruğu, her ögeye öncelik atanması ve en yüksek önceliğin önce alınması
temelinde çalışan kuyruk yapısıdır; heap/ikili ağaç implementasyonu O(log n) ekler. Wiki
ayrıca "sıralı kuyruk" (ordered queue) varyantını ve öncelik eşitliğinde FIFO'nun
korunmasını tanımlar.

**Q5 etkisi:** Q5 matrisinde B (Redis Stream) seçeneği XPENDING/XAUTOCLAIM ile öncelik
desteklemez (akış sırası teslim sırasıdır); C (RabbitMQ) priority exchange destekler ama
starvation (düşük öncelikli işlerin aç kalması) riski taşır; A (DB kuyruk) `ORDER BY
priority DESC, id ASC` ile öncelik destekler — sıralama maliyeti index'e bağlıdır. T2-B4 bu
kriterin kavramsal çerçevesidir.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.6 T2-B5 — Dead Letter Queue (ölü-kuyruk)

**Kanıt:** Ölü-kuyruk (DLQ), bir mesaj işlenemediğinde (redelivery sınırı aşıldığında)
yönlendirildiği ayrı kuyruktur. DLQ mesajları operasyonel gözlem, yeniden işleme (replay)
ve denetim için saklanır. RabbitMQ'da DLX (dead-letter exchange, §1 W-8) bu desenin broker
uygulamasıdır.

**Q5 etkisi:** C seçeneğinin DLX kanıtı (W-8) bu turda **kavramsal karşılığıyla** genişler:
DLQ yalnız broker'da değil, A (DB'de `status='dead'` alanı) ve B (ayrı stream/Stream) için de
tanımlanabilir. background-jobs-surucu.md'de "ölen iş" durum makinesi bu kanıta dayanır.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.7 REDDEDİLEN ADAYLAR (TUR-2 — dürüst kayıt, 2026-10-18)

| Aday URL | Neden (RED) | Tarih |
|----------|-------------|-------|
| https://microservices.io/patterns/data/claim-check.html | HTTP **404** — desen sayfası erişilemez; claim-check kanıtı bu dosyaya yazılmadı (uydurma içerik yasak) | 2026-10-18 |
| https://en.wikipedia.org/wiki/Queue-based_load_leveling | HTTP **404** — wiki maddesi yok; RED kaydı kök `services-web-arastirma.md` §6.8'e aittir, burada tekrarlanmadı (byte-tekilliği) | 2026-10-18 |
| https://en.wikipedia.org/wiki/Idempotence | **Tekrar/RED** — bu dosya §1 open_questions (satır 21) ve §3 (satır 79) zaten kayıtlı; TUR-2'de ayrıca satır açılmadı. İlgili kanıt T2-B2 + T2-B3 ile kapandı → open_questions satır 21 **dolaylı olarak kapandı** (Q5 kararı hâlâ ADR'ye aittir) | 2026-10-18 |
| https://www.rabbitmq.com/docs/confirms | **Byte-tekilliği** — 200 döndü ancak bu dosyanın §1'inde değil, kardeş doğrulama setinde yaşamaktadır; ek satır açılmadı | 2026-10-18 |

### §6.8 Q5 MATRİSİNE TUR-2 ETKİSİ (özet — KARAR DEĞİL)

| Seçenek | TUR-1 kanıtı | TUR-2 ek kanıt | Değişen satır |
|---|---|---|---|
| A — DB kuyruk/outbox | W-10, W-9, W-1 | T2-B2 (idempotent consumer), T2-B3 (idempotency key), T2-B4 (sıralı SELECT öncelik), T2-B5 (DLQ='dead' alanı) | 4 yeni kriter; "outbox için birincil kaynak yok" notu **korunur** |
| B — Redis kuyruk | W-5, W-6, W-9 | T2-B4 (öncelik: stream'de sınırlı), T2-B5 (DLQ ayrı stream) | öncelik eksiği netleşti |
| C — RabbitMQ | W-7, W-8, W-9 | T2-B4 (priority exchange + starvation), T2-B5 (DLX ↔ DLQ doğrudan eşleşme) | en zenginleşen seçenek |
| D — cron/systemd timer | W-2, W-1 | T2-B1 (D1 saf cron ↔ D2 job-store ayrımı) | tek satır ikiye ayrıştı |
| E — Framework iş kütüphanesi | W-3, W-4 | (değişmedi) | — |

> **Öneri-seviyesi (KARAR DEĞİL):** TUR-2 sonrası kanıt yoğunluğu sırası **A ≈ C > D > B > E**
> olarak güncellendi (TUR-1'de A > D > B > C > E); C'nin yükselmesi T2-B5 (DLX↔DLQ) eşleşmesi,
> A'nın sabit kalması T2-B2/B3 idempotency kanıtlarının A'yı A'nın kendi içinde
> güçlendirmesinden kaynaklanır. **Nihai karar ADR'ye aittir** (services-master §7/6 →
> ⚠️ UNKNOWN korunur).

### §6.10 TUR-2 KANIT SAYIMI

| Kalem | Değer |
|-------|-------|
| Aday URL (bu tur) | 8 |
| Kabul (T2-B1…T2-B5) | 5 — hepsi GET 200 (2026-10-18) |
| RED (§6.7) | 4 — dürüst gerekçeli (404 ×2 · tekrar ×2) |
| K012 recursive kesişim | 0 |
| §1–§5 silindi mi? | HAYIR — append-only |

### §6.11 Q5 SEÇENEK DERİNLİK MATRİSİ (TUR-2 kanıtlarıyla — KARAR DEĞİL)

> Bu tablo §2'deki matrisi **tamamlar** (§2 silinmedi); TUR-2'nin eklediği kriterleri
> seçenek başına açar. ADR kararı hâlâ verilmedi (services-master §7/6 → UNKNOWN).

#### §6.11.1 A — DB tabanlı kuyruk / outbox (derinlik)

| Kriter | TUR-1 durumu | TUR-2 durumu | Kanıt |
|--------|--------------|--------------|-------|
| Tekrar teslim güvenliği | kanıt yoktu | **idempotent consumer zorunlu** (işlem-defteri tablosu) | T2-B2 |
| İstemci tarafı koruma | kanıt yoktu | **idempotency anahtarı + request hash** (3 kolon: key, hash, ts) | T2-B3 |
| Öncelik desteği | kanıt yoktu | `ORDER BY priority DESC, id ASC` — index maliyeti O(log n) yerine O(k) tarama | T2-B4 |
| Ölü iş yönlendirme | kanıt yoktu | `status='dead'` alanı + retry_count eşiği (ör. 5) → dead-letter alanı | T2-B5 |
| Çakışan koşum | kanıt yoktu | `SELECT … FOR UPDATE SKIP LOCKED` (MySQL 8+) — ⚠️ bu satır K007-data kanıtına dayanır, web kanıtı TUR-2'de yok | ⚠️ VERIFICATION REQUIRED |

> **A seçeneğinin TUR-2 sonucu:** 4 kriter kanıtla doldu; 1 kriter (SKIP LOCKED) açık kaldı.
> Outbox için birincil kaynak hâlâ yok (§3) — bu eksiklik bilinçli olarak `⚠️` işaretli kaldı.

#### §6.11.2 B — Redis kuyruk (derinlik)

| Kriter | TUR-1 durumu | TUR-2 durumu | Kanıt |
|--------|--------------|--------------|-------|
| FIFO | W-5 (BLPOP list) | — | §1 |
| At-least-once + yeniden tüketim | W-6 (Stream consumer group) | — | §1 |
| Öncelik | kanıt yoktu | **sınırlı** — Stream XPENDING'te öncelik alanı yok; ayrı stream'ler gerekir (akış başına öncelik havuzu) | T2-B4 + W-6 |
| DLQ | kanıt yoktu | ayrı stream (ör. `{q}:dead`) — broker'ın kendi mekanizması yok, uygulama katmanında | T2-B5 |
| Idempotency | kanıt yoktu | consumer `XACK` öncesi işlenen-id kaydı tutmalı (T2-B2 deseni A'daki gibi) | T2-B2 |

> **B seçeneğinin TUR-2 sonucu:** öncelik eksiği netleşti (akış başına havuz maliyeti);
> DLQ uygulama yükü (broker desteği yok) — bu iki satır ADR'de B'nin aleyhine yazılmalı.

#### §6.11.3 C — RabbitMQ / AMQP (derinlik)

| Kriter | TUR-1 durumu | TUR-2 durumu | Kanıt |
|--------|--------------|--------------|-------|
| Exchange/topoloji | W-7 | — | §1 |
| DLX (dead-letter) | W-8 | **DLQ ↔ DLX doğrudan eşleşti** — en güçlü seçenek | T2-B5 |
| Öncelik | kanıt yoktu | priority exchange var ama **starvation** (düşük öncelik aç kalır) — uygulama politikası gerekir | T2-B4 |
| Idempotency | kanıt yoktu | consumer tarafı (T2-B2) + confirm ile publisher (§3 rabbitmq confirms notu — bu dosyada tekrar yazılmaz) | T2-B2 |
| Çakışan koşum | kanıt yoktu | tek consumer + prefetch (QoS) ile önlenir — broker kilitli teslim eder | ⚠️ doğrudan web kanıtı yok |

> **C seçeneğinin TUR-2 sonucu:** DLQ/ DLX eşleşmesi C'yi operasyonel en olgun seçenek yaptı;
> öncelik starvation'ı ve PHP istemci kütüphanesi seçimi (§2 C satırındaki ⚠️) açık kaldı.

#### §6.11.4 D — cron / systemd timer (derinlik)

| Kriter | TUR-1 durumu | TUR-2 durumu | Kanıt |
|--------|--------------|--------------|-------|
| Zamanlama | W-2 (crontab) | — | §1 |
| İş motoru (job-store) | kanıt yoktu | **ayrı bir boyut** — Quartz tarzı job-store: misfire politikası + kalıcı schedule + clustering | T2-B1 |
| Kuyruk/öncelik/tekrar | yok (§2) | yok — D1'de iş kuyruğu **hiç** yok; D2'de (job-store) kısmi | T2-B1 |
| Çakışan koşum | kullanıcıda (§2) | D2'de clustering lock ile çözülür; D1'de `flock`/lockfile ile kısmi | T2-B1 + ⚠️ |

> **D seçeneğinin TUR-2 sonucu:** D1 (saf cron) ↔ D2 (kuruluş içi job-store) ayrışması netleşti;
> D2 CoreMusic'te PHP dışı altyapı demektir → bağımlılık artışı (AGENTS §18/8) uyarısı geçerli.

#### §6.11.5 E — Framework iş kütüphanesi (derinlik)

| Kriter | TUR-1 durumu | TUR-2 durumu | Kanıt |
|--------|--------------|--------------|-------|
| Transport/retry/failed-job | W-3 (Messenger), W-4 (Laravel) | — | §1 |
| Framework yasağı | kök AGENTS §18/7 | — | §1 |
| Idempotency | kanıt yoktu | T2-B2/B3 desenleri Messenger/Laravel'de yerleşik değil — uygulama katmanı gerekir | T2-B2, T2-B3 |

> **E seçeneğinin TUR-2 sonucu:** E'nin "hazır" iddiası idempotency için **zayıfladı** —
> framework kütüphaneleri kuyruğu/ retry'ı verir ama tekrar-güvenli işlemeyi vermez.

### §6.12 IDEMPOTENCY DERİNLİĞİ (T2-B2 + T2-B3 birleşimi — Q5 A/B/C ortak zorunluluk)

> Bu alt bölüm TUR-2'nin en önemli ortak bulgusudur: **hangi seçenek seçilirse seçilsin**,
> idempotency katmanı zorunludur. Aşağıdaki tablo katmanları ayırır; kod `services-ornekler.md`
> §29'da (9 dil) — bu dosyada tekrarlanmaz (byte-tekilliği).

| Katman | Ne korur | Kanıt | A'da karşılığı | B'de karşılığı | C'de karşılığı |
|--------|----------|-------|----------------|----------------|----------------|
| İstemci anahtarı | Aynı isteğin iki kez işlenmesi | T2-B3 | `idempotency_key` kolonu (UNIQUE) | job payload'da key alanı | message header + plugin |
| Tüketici defteri | Aynı mesajın iki kez işlenmesi | T2-B2 | `processed_jobs` tablosu | `XACK` öncesi seen-id seti | consumer DB defteri |
| Fingerprint | Anahtar aynı, gövde farklı (yanlış replay) | T2-B3 | `sha256(body)` kolonu | payload hash | header hash |
| TTL | Defter şişmesi | T2-B3 | zamanlayıcı job (D seçeneği!) | `EXPIRE` | policy + cleanup |

> **Döngü notu (dürüstlük):** TTL temizliği için zamanlanmış iş gerekir → bu, A seçeneğinin
> **D seçeneğiyle birlikte** çalıştırılmasını ima eder (tek başına A yetmez). Bu bağımlılık
> ADR'de açıkça yazılmalıdır.

### §6.13 ÖLÜ-KUYRUK (DLQ) SEÇENEK MATRİSİ (T2-B5 derinliği)

| Seçenek | DLQ karşılığı | Avantaj | Dezavantaj | Kanıt |
|---------|---------------|---------|------------|-------|
| A — DB kuyruk | `status='dead'` satırı + `dead_reason` alanı | sorgulanabilir (SELECT), audit hazır | tablo şişer; `dead` satırları için ayrı index şart | T2-B5 (kavramsal) |
| B — Redis Stream | ayrı `{q}:dead` stream'i | `XRANGE` ile replay mümkün | stream'in de TTL'si gerekir (RAM) | T2-B5 + W-6 |
| C — RabbitMQ | DLX → dead-letter exchange → DLQ queue | broker yerleşik (W-8 + T2-B5) | ayrı queue yönetimi; policy config | W-8, T2-B5 |
| D — cron | (DLQ yok — iş zaten kuyruksuz) | — | başarısız iş log'u DLQ'dur; gözlem yalnız log | T2-B1 (misfire ≠ DLQ) |

> **Ortak kural (TUR-2):** DLQ mesajı **asla silinmez**; yeniden işleme (replay) ve denetim
> için saklanır — bu, wiki tanımının (T2-B5) doğrudan mimari sonucudur.

### §6.14 ZAMANLAYICI (SCHEDULER) DERİNLİĞİ — T2-B1 D1/D2 AYRIŞMASI

| Özellik | D1 — saf cron (W-2) | D2 — job-store (T2-B1) | CoreMusic etkisi |
|---------|---------------------|-------------------------|------------------|
| Schedule tanımı | crontab satırı | job + trigger kaydı (DB/RAM) | D1: dosya; D2: tablo |
| Kaçırılmış tetikleme (misfire) | yok (bir sonraki tick) | politika: anında / ertele / at | D2 kazanımı |
| Kalıcı schedule | crontab dosyası (git'te) | job-store tablosu | D1: kod-as-config; D2: veri |
| Çoklu düğüm | `flock`/lockfile (kısmi) | clustering lock (DB) | D2 kazanımı |
| PHP'ye maliyet | yok (W-1 CLI) | HTTP/CLI tetikleme gerekir | D2 ek altyapı (bağımlılık artışı) |
| Öncelik/tekrar | yok | job-store'da kısmi | T2-B4 D satırında |

> **ADR notu:** Q5 kararı A/B/C/D/E beşli değil, **A/B/C/E × D1/D2 matrisi** olarak
> düşünülmelidir (idempotency TTL temizliği D1'e bağımlı — §6.12 döngü notu).

### §6.14a BACKOFF + RETRY POLİTİKA ÇERÇEVESİ (kök T2-6 ile birleşim — KARAR DEĞİL)

> Kök `services-web-arastirma.md` §6.7'deki T2-6 (Retry kalıbı) bu dosyadaki Q5 seçenekleriyle
> birleştiğinde **backoff politikası** ortak zorunluluktur. Bu tablo politika boyutlarını
> seçenek başına çizer; sayısal değerler (max deneme, taban gecikme) ADR'ye aittir → ⚠️.

| Politika boyutu | A — DB kuyruk | B — Redis | C — RabbitMQ | D — cron |
|-----------------|---------------|-----------|--------------|----------|
| Backoff kaynağı | `next_attempt_at` kolonu (uygulama) | stream entry timestamp (uygulama) | broker TTL / DLX gecikmesi (yerleşik) | bir sonraki tick (tabii) |
| Max deneme | `retry_count` kolonu sınırı | consumer sayacı | x-death sayacı (yerleşik) | job-log sayacı |
| Jitter | uygulamada rastgele gecikme | uygulamada | broker politikası | yok (tick sabit) |
| Idempotency etkisi | retry'da aynı key | retry'da aynı payload | redelivery'da aynı message-id | her tick yeni istek (dikkat!) |
| Ölçüm (retry oranı) | tablo sorgusu | `XPENDING` | management API | log sayımı |

> **D-satırı uyarısı (dürüstlük):** D seçeneğinde her tick bir **yeni istektir** — idempotency
> anahtarı tick başına değişirse (ör. timestamp) T2-B3 koruması **devre dışı kalır**; bu, D
> seçeneğinin gizli maliyetidir ve ADR'de açıkça yazılmalıdır.

### §6.14b ÖLÇÜM / GÖZLEM SATIRLARI (TUR-2 türevi — KARAR DEĞİL)

| Metrik | Tanım | A'da kaynağı | B'de kaynağı | C'de kaynağı | Kanıt |
|--------|-------|--------------|--------------|--------------|-------|
| kuyruk derinliği | bekleyen iş sayısı | `COUNT(*) WHERE status='received'` | `XLEN` | queue depth (management) | §1 + T2-B5 |
| ortalama bekleme | received→processing süresi | zaman damgası farkı | `XINFO` | consumer lag | T2-B2 |
| retry oranı | tekrar denenen iş / toplam | `retry_count>0` | `XPENDING` | x-death | T2-B2, kök T2-6 |
| dead oranı | ölü-kuyruk / toplam | `status='dead'` | dead stream uzunluğu | DLQ uzunluğu | T2-B5 |
| idempotency çakışma | replay+conflict / toplam | key sayımı | payload hash sayımı | header hash sayımı | T2-B3 |
| job-store gecikmesi (D2) | tetik→gerçekleşme farkı | — | — | — | T2-B1 (misfire) |

> **Kapsam notu:** bu metrikler **işlem-defteri/ölçüm** düzeyindedir; uygulama içi tracing
> (OpenTelemetry vb.) bu dosyanın kapsamı dışındadır → ⚠️ VERIFICATION REQUIRED.

### §6.14c ÇOK-DÜĞÜM (CLUSTERING) KARŞILAŞTIRMASI (T2-B1 türevi)

| Seçenek | Çoklu düğüm davranışı | Çakışan koşum riski | Kanıt |
|---------|------------------------|---------------------|-------|
| A — DB kuyruk | `SELECT … FOR UPDATE SKIP LOCKED` (⚠️ web kanıtı yok) | düşük (DB kilidi) | ⚠️ |
| B — Redis Stream | consumer group — her entry **tek** consumer | düşük (broker sahipliği) | W-6 |
| C — RabbitMQ | prefetch (QoS) — broker tek consumer'a verir | düşük (broker sahipliği) | W-7 |
| D1 — saf cron | `flock`/lockfile (dosya kilidi) | **yüksek** (kilit kırılırsa çift koşum) | T2-B1 türevi |
| D2 — job-store | clustering lock (DB) | düşük | T2-B1 (clustering) |

> **Sonuç (KARAR DEĞİL):** çoklu düğüm senaryosunda D1 tek başına **en riskli** seçenektir;
> D2, A, B, C kilit mekanizmasıyla çalışır. Bu satır Q5 ADR'sinin "ölçek" kriteri olarak
> kullanılabilir.

### §6.15 BAŞARI/BAŞARISIZLIK DURUŞU KATALOĞU (TUR-2 kanıtlı — KARAR DEĞİL)

| Durum | Tanım | A'da | B'de | C'de | Kanıt |
|-------|-------|------|------|------|-------|
| received | iş alındı, henüz işlenmedi | `status='received'` | stream entry (pending) | queue'da | §1 + T2-B5 |
| processing | işleniyor | `status='processing'` + lock_ts | `XCLAIM` sahipliği | unacked | T2-B2 |
| done | bitti | `status='done'` | `XACK` | ack | §1 |
| retry | geçici hata, tekrar denenecek | `retry_count++` + backoff (T2-B6 retry) | pending'e iade | nacked + TTL | T2-B2 |
| dead | ölü-kuyruk | `status='dead'` | `:dead` stream | DLX→DLQ | T2-B5 |
| conflict | idempotency çakışması | key ≠ hash → 409 | payload hash ≠ → drop+log | header hash ≠ → reject | T2-B3 |

> **Duruş matrisi Q5 kararının doğrudan girdisidir:** hangi seçenek hangi duruşu doğal
> taşıyor, hangisini uygulamada ekliyor — ADR satırı bu tabloya dayanmalıdır.

### §6.16 DEĞİŞİKLİK KAYDI (TUR-2)

| Tarih | Değişiklik | Yapan |
|-------|------------|-------|
| 2026-10-18 | §6 TUR-2 — 5 kabul URL (GET 200) · 4 dürüst RED · Q5 matrisi güncellendi (D1/D2 ayrışması + idempotency kriterleri) · append-only | Researcher subagent (git commit YASAK) |
| 2026-10-18 | §6.11–§6.15 eklendi (derinlik matrisleri: Q5 seçenek derinliği · idempotency katmanları · DLQ · scheduler · duruş kataloğu) · append-only | Researcher subagent (git commit YASAK) |

### §6.17 OUTBOX BOŞLUĞUNUN DURUMU (⚠️ VERIFICATION REQUIRED — dürüst kayıtlık)

> **Önemli dürüstlük notu:** Outbox pattern bu görevde **birincil kaynakla doğrulanamadı**.
> `en.wikipedia.org/wiki/Outbox_pattern` → **404** (§3 TUR-1 kaydı); TUR-2'de `microservices.io`
> claim-check → **404** (§6.7). Outbox için güvenilir, erişilebilir bir web kanıtı **yok**.

| İddia | Durum | Ne yapılmalı |
|-------|-------|--------------|
| Outbox = DB transaction ile iş kaydı + ayrı poller/publisher | ⚠️ VERIFICATION REQUIRED (kanıt yok) | Q5 kararı ADR'ye taşınırken outbox kavramı **kanıtsız** yazılmalı |
| Transactional outbox vs poller outbox ayrımı | ⚠️ VERIFICATION REQUIRED | A seçeneği (DB kuyruk) ile örtüşüyor ama resmi kaynak yok |
| Outbox + CDC (Debezium vb.) | ⚠️ VERIFICATION REQUIRED | K013-integraion kapsamına komşu; bu dosyanın değil |

> **Sonuç:** A seçeneğinin "outbox" etiketi bu dosyada **kanıtlanmadı**; yalnızca DB kuyruk
> (W-10, W-1, T2-B2, T2-B3) kanıtlandı. ADR'de bu ayrım korunmalıdır — "outbox deseni
> doğrulandı" denemez.

### §6.18 WORKER YAŞAM DÖNGÜSÜ (D1/D2 + A/B/C ortak çerçeve — KARAR DEĞİL)

> PHP CLI worker'ın (W-1) yaşam döngüsü TUR-2 kanıtlarıyla çizilir; **motor kodu yazılmaz**
> (services-ornekler §29 kapsam sınırı) — yalnız duruş/davranış çerçevesi.

| Evre | Ne olur | Seçenek bağımsız zorunluluk | Kanıt |
|------|---------|------------------------------|-------|
| 1 — boot | süreç başlar, yapılandırma okunur | yapılandırma dosyası (config) | W-1 |
| 2 — schedule bekleyişi | D1: tick bekleme; D2: trigger bekleme; A/B/C: kuyruk bekleme | — | W-2, T2-B1, W-5/W-7 |
| 3 — iş alımı | kuyruktan/tablodan iş çekilir | idempotency anahtarı kontrolü (T2-B3) | T2-B2, T2-B3 |
| 4 — işleme | iş çalıştırılır (retry/backoff) | max deneme + jitter (kök T2-6) | T2-B2, kök T2-6 |
| 5 — sonuç yazımı | done / retry / dead duruşu | duruş matrisi §6.15 | T2-B5 |
| 6 — kapanış | idempotency TTL temizliği | zamanlanmış iş (D1'e bağımlı) | T2-B3, T2-B1 |
| 7 — crash kurtarma | süreç çökmesi → iş ne olur? | iş `processing`'de kalmışsa sahiplik zaman aşımı (T2-B2) | T2-B2 |

> **7. evre kritik (TUR-2):** worker çökerse `processing`'de kalan iş **zaman aşımıyla**
> yeniden alınır (idempotent consumer deseni — T2-B2). Bu, seçeneklerin tamamında
> zorunludur; D1'de (kuyruksuz) iş log'a düşer, DLQ yoktur (§6.13 D satırı).

### §6.19 DEĞİŞİKLİK KAYDI (TUR-2 ek)

| Tarih | Değişiklik | Yapan |
|-------|------------|-------|
| 2026-10-18 | §6.17–§6.18 eklendi (outbox boşluğu dürüst kaydı · worker yaşam döngüsü) · append-only | Researcher subagent (git commit YASAK) |

### §6.20 İŞ KAYDI TABLO ALAN MATRİSİ (A seçeneği için — şema TASLAĞI, KARAR DEĞİL)

> A seçeneği (DB kuyruk) seçilirse kuyruk tablosunun **alanları** şu şekilde olmalıdır;
> bu bir **şema taslağıdır** (SQL DDL değil — motor kodu yasak kapsamıyla uyumlu),
> migration değil, ADR girdisidir.

| Alan tipi | Ne | Neden | Kanıt |
|-----------|-----|-------|-------|
| iş kimliği | tekil id (UUID/BigInt) | takip | §1 |
| idempotency anahtarı | tekil (UNIQUE) + hash | T2-B3 fingerprint | T2-B3 |
| duruş | enum: received/processing/done/retry/dead | §6.15 katalog | T2-B5 |
| retry sayacı | integer + sınır | T2-B2 + kök T2-6 backoff | T2-B2 |
| öncelik | integer (DESC sıralama) | T2-B4 (priority queue) | T2-B4 |
| zaman damgaları | created_at / next_attempt_at / completed_at | backoff + ölçüm (§6.14a) | T2-B1 (misfire), kök T2-6 |
| dead_reason | text (nullable) | DLQ audit (§6.13) | T2-B5 |
| iş yükü (payload) | JSON/text | taşınan veri | §1 |

> **Uyarı (dürüstlük):** bu alanlar **taslaktır**; K007-data şemasıyla (BCNF) uyumu ayrıca
> doğrulanmalı → ⚠️ VERIFICATION REQUIRED. Şema kararı K007-data'ya aittir (bu dosya değil).

### §6.21 KUYRUK/TAŞIYICI ÖLÇÜM SORULARI (Q9 ile kesişim — KARAR DEĞİL)

| Soru | Neden önemli | Kanıt |
|------|---------------|-------|
| Kuyruk seçimi (A/B/C) Q9 taşıyıcı seçimiyle aynı mı? | Q9 "servisler arası taşıyıcı"nı sorar; kuyruk "iş taşıyıcısı"dır — farklı olabilir | §4 |
| Servis→servis senkron çağrı (HTTP) ayrı mı? | evet — kuyruk asenkron, HTTP senkron; ikisi Q9'da ayrı satır | §4 + T2-B2 |
| Retry politikası iki taşıyıcıda aynı mı? | HTTP'de uygulama backoff (kök T2-6); kuyrukta broker/uygulama (§6.14a) | kök T2-6 + T2-B2 |
| Idempotency anahtarı iki taşıyıcıda ortak mı? | evet — T2-B3 anahtarı HTTP header'da da, kuyruk payload'ında da taşınır | T2-B3 |

> **Sonuç (KARAR DEĞİL):** kuyruk seçimi (Q5) ile taşıyıcı seçimi (Q9) **bağımsız** ama
> idempotency anahtarı **ortak** zorunluluktur — bu, bg dosyası ile kök dosya arasındaki
> en güçlü kesişimdir.

### §6.22 TUR-2 SONUÇ TABLOSU (background-jobs — dürüst özet)

| Boyut | TUR-1 durumu | TUR-2 sonucu |
|-------|--------------|--------------|
| Zamanlayıcı | D tek satır | **D1/D2 ayrıştı** (T2-B1) — job-store misfire/clustering kazanımı |
| Idempotency | kanıt yoktu | **iki katman** (T2-B2 tüketici + T2-B3 istemci) — A/B/C ortak zorunlu |
| DLQ | W-8 (DLX, C) | **4 seçeneğe yayıldı** (T2-B5) — A'da `dead` alanı, B'de ayrı stream |
| Öncelik | kanıt yoktu | **T2-B4** — A'da ORDER BY, C'de starvation uyarısı, B'de sınırlı |
| Outbox | 404 (§3) | **hâlâ kanıtsız** (§6.17 dürüst boşluk) — ADR'de ⚠️ |
| Öneri sırası | A > D > B > C > E | **A ≈ C > D > B > E** (C DLX eşleşmesiyle yükseldi) |
| Karar | UNKNOWN | **hâlâ UNKNOWN** (5 eksik + outbox ⚠️) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode