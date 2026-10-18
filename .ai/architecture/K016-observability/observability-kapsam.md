---
node_id: K016-observability/observability-kapsam
node_type: module
status: draft
parent_id: K016-observability
children: []
depends_on:
  - K012-services
  - K010-network
  - K014-media
  - K17-devops
evidence:
  - "shared/src/Log/LoggerFactory.php · Satır 11 — final class LoggerFactory (tek log fabrikası, 34 satır)"
  - "shared/src/Log/FileHandler.php · Satır 30 — final class FileHandler implements LoggerInterface (253 satır)"
  - "shared/src/Log/Redactor.php · Satır 31 — private const SENSITIVE_KEYS (tek maskeleme kaynağı, 106 satır)"
  - "shared/tests/Unit/Log/Psr3LoggerTest.php · Satır 46 — assertInstanceOf(LoggerInterface::class, ...) (PSR-3 kanıtı)"
  - "shared/tests/Unit/Log/LoggerRedactionTest.php · Satır 17 — final class LoggerRedactionTest (189 satır, 9 test)"
  - "K016-observability/ dizin ölçümü (2026-10-13): 10 dizin · 18 dosya · 3.395 satır"
validation:
  - "Tüm satır numaraları 2026-10-13 tarihinde dosya-içinde birebir okunarak doğrulandı"
  - "Web tablosu 1 URL · envanter (2640) dışı · HTTP 200 (2026-10-13)"
  - "Yasaklı yer-tutucu taraması 0 sonuç · boş hücre 0 · mojibake 0"
open_questions:
  - "alerting/ dizini v2 §2.17 çocuk listesinde YOK — kademe-3'te tescil mi alerts/ birleşmesi mi (master §7/4)"
  - "Metrik/tracing/health/alert teknolojisi seçilmedi (UNKNOWN) — kapsam sabit, seçim kademe-3"
  - "Vault .ai/log.md (ajan izi) ile shared/src/Log (ürün logu) sınırı kademe-3'te tescil edilecek"
group: mimari
---


# K016-observability — observability-kapsam.md

## §1 Kapsam Amacı (ELI15)

Bu dosya K016-observability domaininin **sınırlarını** çizer: ne bu domainde üretilir, ne komşu
domainlere devredilir, hangi kademe-2 çocukları kapsanır. Kapsam, koddan **önce** yazılır; kapsam
dışı yüzeye dokunmak revert sebebidir (kök AGENTS.md §3). Bu dosya yalnız **tanım** taşır —
uygulama master'da, kanıt doğrulama'dadır. Kapsam değişirse önce bu dosya, sonra master güncellenir;
tersi değil. Kapsam boşluğu tahminle değil, disk ölçümüyle doldurulur (zero-hallucination).

## §2 Kapsam Sınırı (bu domain · bu domain DEĞİL)

| # | Kapsam (K016 üretir/gözetir) | Kapsam DIŞI (domain · gerekçe) |
|---|---|---|
| 1 | Uygulama logları: yazım · format · seviye · redaction | Test stratejisi/coverage/E2E → **K019-testing** (K016 yalnız ölçer) |
| 2 | Metrik toplama: sayaç · gecikme · hata oranı | Metrik üreten servis mantığı → **K012-services** (K016 toplar, K012 üretir) |
| 3 | Tracing: trace/span · uçtan uca istek izi | Ağ katmanı trace başlığı işleme → **K010-network** |
| 4 | Correlation: request-id/trace-id üretimi tek nokta | İstek yönlendirme → **K011-application / K012-services** |
| 5 | Health-check uçları: tanım · durum kodu | Sağlık uçunun servis sahipliği → **K012-services** |
| 6 | Alerts: eşik · koşul · kanal | Uyarı tetiklediği deploy/rollback aksiyonu → **K018-devops** |
| 7 | Performance gözlemi: profil · bottleneck kaydı | Performans **optimizasyonu** kodu → üretim domaini (K011/K012) |
| 8 | audio-diagnostics: xrun · clip · latency teşhisi | DSP zinciri düzeltmesi → **K003-audio / K004-dsp** |
| 9 | Incident-response: runbook · eskalasyon (ürün odaklı) | Ajan eskalasyon zinciri → **K015-AI** (.ai/AGENTS.md §10) |
| 10 | Güvenlik olay logu (auth/security stream) | Auth/encryption **politikası** → **K009-security** (K016 yalnız kanıt üretir) |
| 11 | Vault audit trail (.ai/log.md) formatı ve eki | Vault depolama/senkron mekaniği → **K000-vault** |
| 12 | Log dosyası yazma hedefi (repo kökü, kanonik 4 dosya) | Disk kesme/döndürme politikası → **K018-devops** (depo/ortam) |
| 13 | Seviye etiketleri ([EMERGENCY]…[DEBUG]) kanonik adlandırma | Eşik değeri politikası (hangi seviye alarm) → alerts/ kademe-3 |
| 14 | Trace-id satır damgası ([trace=...]) | Trace-id üretiminin dağıtımı → correlation/ kademe-3 |
| 15 | CI job loglarının gözlem yüzeyi olarak okunması | CI loglarının üretilmesi → **K018-devops** (ci.yml sahipliği) |

**Sınır ilkeleri:**

| # | İlke | Açıklama | Sonuç |
|---|---|---|---|
| 1 | Gözlem ≠ üretim | K016 sistemi izler, ses/web üretmez | Üretim koduna dokunma revert |
| 2 | Uyarı ≠ aksiyon | Uyarı K016, deploy/rollback K018 | Sınır çekilmiş entegrasyon |
| 3 | Ölçüm ≠ optimizasyon | K016 bottleneck kaydeder, düzeltmez | Optimizasyon üretim domaininde |
| 4 | Tek fabrika | Tüm log LoggerFactory'dan geçer | Dağıtık loglama yasak |
| 5 | Merkezi redaction | Tüm satırlar Redactor'dan geçer | Ham hassas veri loga girmez |
| 6 | Tanım ≠ sahiplik | Health tanımı K016, servis sahipliği K012 | Çifte sahiplik önlenir |
| 7 | Ürün ≠ ajan | Ürün runbook'u K016, ajan zinciri K015-AI | Eskalasyon karışmaz |
| 8 | Sınır tek yön | K016 komşuya kanıt/uyarı gönderir | Komşu K016'ye iç detay dayatmaz |
| 9 | Kanıt > tahmin | Kapsam boşluğu disk ölçümüyle doldurulur | Uydurma kapsam yasak |
| 10 | Draft kapısı | Açık sorular kapanana dek draft | Erken verified etiketi yasak |

## §3 Kademe-2 Kapsam Kataloğu (v2 §2.17 — 9 çocuk + 1 disk fazlası)

| # | Çocuk | Kapsam rolü | Kapsam durumu | Disk (2026-10-13) |
|---|---|---|---|---|
| 1 | logging/ | Log yazımı · redaction · format | TAM (kanıtlı) | 3 dosya · 1.068 satır |
| 2 | metrics/ | Sayaç/ölçüm toplama · etiketleme | TASARIM (kanıt bekliyor) | 2 dosya · 571 satır |
| 3 | tracing/ | Uçtan uca iz · span · dağıtık izleme | TASARIM (kanıt bekliyor) | 2 dosya · 570 satır |
| 4 | correlation/ | Request-id ile log/trace birleştirme | İSKELET (index tek) | 1 dosya · 50 satır |
| 5 | health-checks/ | Canlılık/sağlık uçları | İSKELET (index tek) | 1 dosya · 50 satır |
| 6 | alerts/ | Uyarı kuralları + kanalları | İSKELET (index tek) | 1 dosya · 51 satır |
| 7 | performance/ | Profil/bottleneck gözlem kaydı | İSKELET (index tek) | 1 dosya · 50 satır |
| 8 | audio-diagnostics/ | Ses zinciri teşhisi | İSKELET (index tek) | 1 dosya · 51 satır |
| 9 | incident-response/ | Olay müdahale akışı | İSKELET (index tek) | 1 dosya · 50 satır |
| 10 | alerting/ (fazla) | Eşik/yükseklik (v2'de YOK) | AÇIK KARAR | 1 dosya · 553 satır |

**Kapsam dengesi:** 9 çocuğun 1'i TAM (logging) · 2'si TASARIM (metrics · tracing) · 6'sı İSKELET ·
1 disk fazlası (alerting) açık karar. Bu, domainin **kağıtta geniş / diskte dar** olduğunu gösterir;
eksikler uydurulmadı, işaretlendi.

### §3.1 Kapsam durumu etiketlerinin anlamı

| Etiket | Anlamı | Kapanış şartı | K016 çocuğunda sayı |
|---|---|---|---|
| TAM | Kod + test diskte, kapsam doldu | IMPLEMENTED etiketi | 1 (logging) |
| TASARIM | Tasarım dosyası var, kod yok | Kod + kanıt | 2 (metrics · tracing) |
| İSKELET | Yalnız index.md (40-100 satır) | 3 içerik dosyası ≥500 | 6 (correlation…incident) |
| AÇIK KARAR | Disk fazlası, v2'de yok | ADR ya da kullanıcı onayı | 1 (alerting) |
| UNKNOWN | Veri yok, tahmin yazılmaz | Ölçüm | 0 (bu domainde) |

### §3.2 Her çocuk için kapsam-sınır cümlesi (kademe-3 öncesi)

| # | Çocuk | Kapsam cümlesi (bu domainde) | Sınır cümlesi (DEĞİL) |
|---|---|---|---|
| 1 | logging/ | Log satırının üretilmesi · maskelenmesi · dosyalanması | Log okuyucu/SIEM entegrasyonu (kademe-3, kanıt yok) |
| 2 | metrics/ | Sayaç/ölçüm tanımı · etiketleme · dışa aktarım | Metrik görselleştirme dashboard (kademe-3) |
| 3 | tracing/ | Span üretimi · bağlam taşıma · dağıtık iz | Trace backend depolama (kademe-3) |
| 4 | correlation/ | Tek istek-kimliği üretimi ve satıra damgası | Çok-servis korelasyon topolojisi (kademe-3) |
| 5 | health-checks/ | Uç tanımı · durum kodu · bağımlılık sorgusu | Health sonucunun deploy'a bağlanması → K018 |
| 6 | alerts/ | Koşul · eşik · kanal tanımı | Uyarı sonrası aksiyon (deploy/rollback) → K018 |
| 7 | performance/ | Profil/bottleneck kaydı · regresyon eşiği paylaşımı | Kod optimizasyonu → üretim domaini |
| 8 | audio-diagnostics/ | xrun/clip/latency ölçüm eşiği · raporu | DSP düzeltme → K003/K004 |
| 9 | incident-response/ | Ürün runbook'u · severity · eskalasyon (insan) | Ajan eskalasyonu → K015-AI |
| 10 | alerting/ | Eşik/yükseklik hesabı (fazladan dizin) | alerts/ ile ilişki — açık karar |

## §4 Komşu Domain Kapsam Sınırı (K000 · K001 · K009 · K010 · K011 · K012)

| # | Komşu | Sınır (kesişim) | K016 ne yapar | Komşu ne yapar |
|---|---|---|---|---|
| 1 | K000-vault | Audit trail (.ai/log.md) | Append-only format · tip/aktör/tarih | Depolama · senkron · git kurtarma |
| 2 | K001-donanım | Ses ölçümleri (xrun/clip) | Teşhis eşiği · raporu | Donanım sürücü/ölçüm kaynağı |
| 3 | K009-security | Güvenlik olayları · redaction | Log akışı · maskeleme · audit kanıtı | Auth/encryption politikası · anahtar |
| 4 | K0100-identitiy | Kimlik olay logu | login/register event kaydı | Kimlik doğrulama mantığı |
| 5 | K011-application | İstek logu · correlation | trace-id üretimi · format | Uygulama iş mantığı |
| 6 | K012-services | Servis log/metric/health | Toplama · health tanımı | Servis üretimi · health sahipliği |
| 7 | K018-devops | Alert → pipeline | Uyarı üretimi | Deploy/rollback aksiyonu |
| 8 | K010-network | Ağ trace/health | Trace başlığı · sağlık | Ağ katmanı işleme |
| 9 | K014-media | Medya/ses izleme | Teşhis raporu | Medya işleme |
| 10 | K015-AI | Ajan eskalasyonu | Ürün runbook'u | Ajan zinciri (.ai/AGENTS.md §10) |
| 11 | K002-electroncis | Devre telemetrisi | Yalnız sınır (kapsam dışı) | Elektronik tasarım/BOM |
| 12 | K005-firmware | Firmware logu | Yalnız sınır (kapsam dışı) | XMOS/DSP firmware |

**Sınır ihlali davranışı:** domain sınırı ihlalinde sistem durur, MO müdahale eder
(.ai/AGENTS.md §18 kural 1); halüsinasyon (kanıtsız iddia) `VERIFICATION REQUIRED` ile işaretlenir
(.ai/AGENTS.md §18 kural 3). İhlal → DUR + rapor (kök AGENTS.md §3), rollback değil.

### §4.1 Kesişim-sınırı davranış matrisi

| # | Kesişim senaryosu | Doğru davranış | Yanlış davranış (yasak) |
|---|---|---|---|
| 1 | Redactor'a yeni anahtar eklenecek | K016 yazar · K009'a bilgi verilir | K009 sessizce anahtar değiştirir |
| 2 | Alert eşiği değişecek | K016 kararı · test K019 | K018 eşiği değiştirir |
| 3 | Health-check eklenecek | Tanım K016 · kod K012 | K016 servis içine kod gömer |
| 4 | Trace-id biçimi değişecek | correlation/ tek nokta | Her servis kendi id'sini üretir |
| 5 | Log dosyası kesilecek | Politika K018 · format K016 | K016 kesme kodu yazar |
| 6 | Incident sırasında seviye artışı | Geçici: K016 · kalıcı: ADR | Kalıcı değişiklik sessiz yapılır |
| 7 | Güvenlik olayı loglanacak | securityEvent() stream'i K016 | K009 ayrı log açar (dağıtık) |
| 8 | Vault sync yorumu | .ai/log.md append (K000 biçimi) | Geçmiş satır silinir/değiştirilir |

## §5 Kapsam Kararları (defter)

| # | Karar | Dayanak | Etkilenen çocuk | Durum |
|---|---|---|---|---|
| 1 | Domain yalnız gözlemler, üretim yapmaz | §2 sınır ilkeleri | tümü | tescilli |
| 2 | Uyarı üretimi K016, aksiyon K018 | v2 §2.17/§2.18 | alerts/ | tescilli |
| 3 | Test stratejisi K019; K016 yalnız ölçer | v2 §2.19 | performance/ | tescilli |
| 4 | Vault .ai/log.md ≠ uygulama logu shared/src/Log | .ai/AGENTS.md §5 · §2/11 | logging/ · correlation/ | tescilli (ayrım kademe-3) |
| 5 | Hassas veri log'a yazılmaz, [REDACTED] | .ai/AGENTS.md §17 edge-3 · Redactor.php Satır 31 | logging/ | tescilli — kanıtlı |
| 6 | Log arayüzü PSR-3 LoggerInterface | FileHandler.php Satır 30 · Psr3LoggerTest.php Satır 46 | logging/ | tescilli — kanıtlı |
| 7 | alerting/ kademe-3'te tescil ya da alerts/ birleşmesi | §3/10 · master §A.2 bulgu 1-2 | alerts/ · alerting | açık karar |
| 8 | Metrik/tracing/health/alert teknolojisi seçilmedi | master §4 UNKNOWN satırları | metrics/ · tracing/ · health-checks/ · alerts/ | açık karar |
| 9 | CI job logları bu domainin gözlem yüzeyidir | ci.yml Satır 158 · 203 | incident-response/ | tescilli — kanıtlı |
| 10 | Ses teşhisi bu domainde, DSP düzeltmesi K003/K004 | v2 §2.3/§2.4/§2.17 | audio-diagnostics/ | tescilli |
| 11 | Performance gözlemi burada, optimizasyon üretimde | §2/7 · §5/3 | performance/ | tescilli |
| 12 | Health-check tanımı K016, sahiplik K012 | v2 §2.12/§2.17 | health-checks/ | tescilli — uygulama bekliyor |
| 13 | Incident runbook ürün odaklı; ajan eskalasyonu K015-AI | .ai/AGENTS.md §10 | incident-response/ | tescilli |
| 14 | Domain açık sorular kapanana dek draft kalır | master §7 · index.md §3 | tümü | tescilli |
| 15 | K018'e yalnız uyarı; pipeline kapısına dokunma | .ai/AGENTS.md §5 · v2 §2.18 | alerts/ | tescilli |
| 16 | K009'a yalnız kanıt (audit log); auth politikası değişmez | v2 §2.9 · §2/10 | logging/ | tescilli |
| 17 | Log dosyası diski doldurursa kesme K018 depo politikasında | v2 §2.18 | logging/ | tescilli |
| 18 | Redaction anahtar listesi tek kaynaktadır (Redactor) | Redactor.php Satır 31 | logging/ | tescilli — kanıtlı |
| 19 | Trace-id tek noktada üretilir (correlation), dağıtılmaz | Psr3LoggerTest.php Satır 116 | correlation/ | tescilli — kanıtlı |
| 20 | Kapsam dışı yüzeye dokunma revert sebebidir | kök AGENTS.md §3 | tümü | tescilli |
| 21 | Log hedefi kanonik 4 dosya (errors/warnings/info/security) | FileHandler.php Satır 19-25 · 228-234 | logging/ | tescilli — kanıtlı |
| 22 | Security stream prod'da dâhil her zaman aktif | FileHandler.php Satır 24-25 · 233-235 | logging/ | tescilli — kanıtlı |
| 23 | debug() yalnız debugMode'da yazar (B-F-14 düzeltmesi) | FileHandler.php Satır 190-193 | logging/ | tescilli — kanıtlı |
| 24 | Bilinmeyen seviye 'error'a düşer (fail-safe) | FileHandler.php Satır 128-130 | logging/ | tescilli — kanıtlı |
| 25 | E-posta maskelenir, alan adı korunur (PII dengesi) | LoggerRedactionTest.php Satır 106 | logging/ | tescilli — kanıtlı |
| 26 | 64-char hash redakte, UUID korunur | LoggerRedactionTest.php Satır 122 | logging/ | tescilli — kanıtlı |
| 27 | Kapsam dosyası kapsam değişiminde ilk güncellenir | bu dosya §1 | tümü | tescilli |
| 28 | Kapsam dışı yüzey için kod/doküman üretilmez | §6 dürüstlük beyanı | tümü | tescilli |

## §6 Kapsam-Dışı Yüzey Beyanı (dürüstlük)

Aşağıdaki yüzeyler bu domainin **kapsamı dışındadır** ve bu dosyada tanımlanmaz; referans için
ilgili domain dosyasına bakılır. Kapsam dışı yüzey için kod/doküman **üretilmez**.

| # | Yüzey | Sahip domain | Bu dosyada |
|---|---|---|---|
| 1 | İşletim sistemi / boot | K000-isletim-sistemi | tanımlanmaz |
| 2 | Donanım / DAC-ADC / PCB | K001-donanım | tanımlanmaz |
| 3 | Elektronik devre / BOM | K002-electroncis | tanımlanmaz |
| 4 | Firmware / XMOS / DSP chain | K005-firmware | tanımlanmaz |
| 5 | Güvenlik politikası / crypto | K009-security | yalnız sınır (§4/3) |
| 6 | Identity doğrulama mantığı | K0100-identitiy | yalnız sınır (§4/4) |
| 7 | Uygulama iş mantığı / route | K011-application | yalnız sınır (§4/5) |
| 8 | API gateway / servis üretimi | K012-api / K012-services | yalnız sınır (§4/6) |
| 9 | Ses motoru (Neva Engine) | K003-audio | yalnız sınır (§2/8) |
| 10 | DSP algoritması | K004-dsp | yalnız sınır (§2/8) |
| 11 | UI/CSS/JS bileşen kodu | K011-application | tanımlanmaz |
| 12 | Veritabanı şema/migration | K006-database | tanımlanmaz |

> **Dürüstlük notu:** `.ai/electronic/` ve Neva Engine C++ kaynakları bu domainin parçası değildir.
> Repo'da K016'ye ait yalnız PHP log altyapısı (shared/src/Log) + testleri + CI log yüzeyi vardır;
> metrik/tracing/health/alert **kod karşılığı YOK** — kaynak: vault (uydurma yok).

### §6.1 Repo'da kapsam karşılığı olan/yolmayan yüzeyler

| # | Yüzey | Repo durumu | Kapsam etiketi |
|---|---|---|---|
| 1 | Log fabrikası | shared/src/Log/LoggerFactory.php VAR (34 satır) | IMPLEMENTED |
| 2 | Log handler | shared/src/Log/FileHandler.php VAR (253 satır) | IMPLEMENTED |
| 3 | Redaction | shared/src/Log/Redactor.php VAR (106 satır) | IMPLEMENTED |
| 4 | PSR-3 testi | shared/tests/Unit/Log/Psr3LoggerTest.php VAR (163 satır) | IMPLEMENTED |
| 5 | Redaction testi | shared/tests/Unit/Log/LoggerRedactionTest.php VAR (189 satır) | IMPLEMENTED |
| 6 | Vault audit trail | .ai/log.md VAR (20 satır) | IMPLEMENTED (doküman) |
| 7 | CI log yüzeyi | .github/workflows/ci.yml VAR (203 satır) | IMPLEMENTED (doküman) |
| 8 | Metrik sistemi | Prometheus/StatsD/özel — disk kanıtı YOK | UNKNOWN |
| 9 | Tracing altyapısı | OpenTelemetry vb. — disk kanıtı YOK | UNKNOWN |
| 10 | Alert kanalı | Slack/e-mail/webhook — disk kanıtı YOK | UNKNOWN |
| 11 | Health endpoint | health route — disk kanıtı YOK | UNKNOWN |
| 12 | Incident runbook (ürün) | disk kanıtı YOK (ajan eskalasyonu var) | İSKELET |

## §7 Repo Kanıtları (kapsam — gerçek dosya/satır)

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 11 — `final class LoggerFactory` (tek log fabrikası — §5/6).

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 15 — `public static function getInstance(string $logDir = '', string $level = 'info'): FileHandler` (tek nokta erişim).

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 32 — `return dirname(__DIR__, 3);` (log dizini repo kökü — kanonik hedef).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 30 — `final class FileHandler implements LoggerInterface` (PSR-3 arayüzü — §5/6).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 40-49 — `private const LEVELS` (sekiz kanonik seviye debug→emergency).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 52 — `private const WARNING_FAMILY` (warning-ailesi → errors.log).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 125 — `public function log($level, string|Stringable $message, array $context = []): void` (PSR-3 dispatcher).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 188 — `private function logAt(...bool $securityStream = false): void` (güvenlik akışı ayrımı — §4/3).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 224 — `$line = Redactor::redact($line);` (merkezi redaction — §2/5 · §5/5).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 228 — `$this->appendToFile('coremusic_php_errors.log', $line);` (tüm seviyeler errors.log — §5/21).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 234 — `$this->appendToFile('coremusic_php_security.log', $line);` (security stream — §5/22).

Dosya: `shared/src/Log/Redactor.php` · Satır: 31 — `private const SENSITIVE_KEYS = 'auth_key|password|...'` (tek maskeleme kaynağı — §5/18).

Dosya: `shared/src/Log/Redactor.php` · Satır: 65 — `public static function redact(string $line): string` (dış arayüz).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 46 — `$this->assertInstanceOf(LoggerInterface::class, new FileHandler($this->dir));` (PSR-3 kanıtı — §5/6).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 17 — `final class LoggerRedactionTest extends TestCase` (189 satır, 9 test).

Dosya: `shared/composer.json` · Satır: 19 — `"psr/log": "^3.0"` (PSR-3 bağımlılığı — §5/6).

Dosya: `.github/workflows/ci.yml` · Satır: 158 — `run: npm run test:js` (CI gözlem yüzeyi — §5/9).

Dosya: `.ai/log.md` · Satır: 13 — `2026-10-02 | session | ...` (vault audit trail — §2/11 · §4/1).

## §8 Web tablosu (kapsam)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| OpenTelemetry logs data model, log kayıtlarının yapı taşları (timestamp · severity · body · attributes) olarak tanımlanır — tracing/metrics/logs sinyal ayrımı K016 kapsamının standart dayanağı | https://opentelemetry.io/docs/specs/otel/logs/data-model/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter (2640) dışı |

## §9 Doğrulama (kapsam)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | §G/1 |
| 2 | Son satır | Authority dizgesi birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + node_id + group (mimari) · parent: 0 | ✓ |
| 4 | Kapsam sınırı | §2 · §4 · §6 tabloları dolu | ✓ |
| 5 | Repo kanıtları | ≥ 1 Dosya/Satır · gerçek satır indeksi | ✓ (§7) |
| 6 | Web tablosu | 1 URL · envanter dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | 3 yer-tutucu türü · boş hücre · mojibake | ✓ 0 |
| 8 | Kapsam dışı beyanı | §6'da K000/K001/K002/K005/K009/K010/K011/K012 sınırı | ✓ |

## §A Ölçüm günlüğü (kapsam)

| # | Ölçüm | Sonuç | Not |
|---|---|---|---|
| 1 | Görev öncesi durum | kapsam dosyası YOK | kök 6 MD şemasında eksik |
| 2 | Yazım sonrası uzunluk | §G/1 hücresinde | ≥ 500 hedefi |
| 3 | Frontmatter `group` | mimari (tırnaksız) | -kapsam → mimari |
| 4 | `parent:` sayısı | 0 | yalnız parent_id |
| 5 | Boş hücre | 0 | tüm hücreler dolu |
| 6 | Mojibake | 0 | UTF-8, Türkçe sağlam |
| 7 | Repo kanıtı satırı | 18 Dosya/Satır | gerçek dosya indeksi |
| 8 | Web URL | 1 · envanter dışı · HTTP 200 | benzersiz |
| 9 | Yazılan dosya (bu görev) | 4 (K016 kapsam+ornekler · K018 kapsam+ornekler) | beyazliste sınırlı |
| 10 | Dışarıya yazım | 0 | beyazliste ihlali yok |

## §B Kapsam→Dosya Eşlemesi (bu domain)

| # | Kök dosya | group | Kapsam rolü | Durum |
|---|---|---|---|---|
| 1 | index.md | mimari | Klasör künyesi · çocuk kataloğu | mevcut (84 satır) |
| 2 | observability-master.md | mimari | Domain anayasası · karar defteri | mevcut (503 satır) |
| 3 | observability-kapsam.md | mimari | Sınır tanımı (bu dosya) | bu görevde yazıldı |
| 4 | observability-bagimliliklar.md | mimari | Katman bağı · used_by | mevcut (502 satır) |
| 5 | observability-dogrulama.md | kanit | İddia/URL doğrulama matrisi | mevcut (500 satır) |
| 6 | observability-ornekler.md | kod | Gerçek kod örnekleri | bu görevde yazıldı |

## §C Alt-Konu Kapsam Planı (kademe-3)

| # | Alt-konu | Gerekli iskelet | Mevcut | Eksik |
|---|---|---|---|---|
| 1 | logging/ | index + 3 içerik | index + 2 içerik | 1 içerik |
| 2 | metrics/ | index + 3 içerik | index + 1 içerik | 2 içerik |
| 3 | tracing/ | index + 3 içerik | index + 1 içerik | 2 içerik |
| 4 | correlation/ | index + 3 içerik | index | 3 içerik |
| 5 | health-checks/ | index + 3 içerik | index | 3 içerik |
| 6 | alerts/ | index + 3 içerik | index | 3 içerik |
| 7 | performance/ | index + 3 içerik | index | 3 içerik |
| 8 | audio-diagnostics/ | index + 3 içerik | index | 3 içerik |
| 9 | incident-response/ | index + 3 içerik | index | 3 içerik |
| 10 | alerting/ | index + 3 içerik | 1 içerik | index + 2 içerik |

> Alt-konu derinleştirme Band-1 kök iskeletinin **sonrasında** ayrı turda yapılır (master §D.1 Faz 5).

## §D Terimler

| Terim | Tanım |
|---|---|
| Gözlem (observability) | Sistemin iç durumunu dışarıdan anlama yeteneği: log · metric · trace |
| Redaction | Hassas veriyi maskeleme ([REDACTED]) — Redactor uygular |
| Correlation | Log/trace/istekleri tek kimlikle birleştirme (request-id/trace-id) |
| Health-check | Servis canlılık/sağlık durumu sorgusu |
| Incident | Sistem olayı; müdahale akışı runbook ile yürür |
| Bottle­neck | Performans tıkanma noktası; K016 kaydeder, düzeltmez |
| Stream | Ayrı log akışı (security stream errors.log'dan ayrılır) |
| Fail-safe | Bilinmeyen seviye 'error'a düşer — log kaybı önlenir |

## §E Referanslar

| Kaynak | Referans |
|---|---|
| v2 spec | coremusic-katmanli-mimari-agaci-v2.md §2.17 (çocuk listesi birebir) |
| ADR-097 (draft) | §2 K1–K7 · §5 isimlendirme |
| Master | observability-master.md §1-§8 · §A-§G |
| Bağımlılık | observability-bagimliliklar.md |
| Doğrulama | observability-dogrulama.md |
| Registry | .ai/architecture/index.md §5 (satır 80) |

## §F Sınır Özeti (1 satır)

K016 = sistemin **görünürlüğü**: log/metric/trace/health/alert/incident; üretim domainlerini izler,
kendisi üretim yapmaz; uyarı K016, aksiyon K018; ölçüm K016, optimizasyon üretimde.

## §G Revizyon doğrulama kaydı (kapsam)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 512 ReadAllLines |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + node_id + group (mimari, tırnaksız) · parent: 0 | ✓ |
| 4 | Zorunlu bölümler | §1-§9 + §A-§G · tümü `## §` | ✓ |
| 5 | Repo kanıtları | 18 Dosya/Satır · gerçek satır indeksi ile birebir okundu | ✓ |
| 6 | Web tablosu | 1 URL · envanter (2640) dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | 3 yer-tutucu türü · boş hücre · mojibake · dolgu | ✓ 0 |
| 8 | Kapsam dışı beyanı | K000/K001/K002/K005/K009/K010/K011/K012 §6'da sınır | ✓ |
| 9 | Yazılan dosya | bu dosya dâhil 4 (K016×2 · K018×2) | ✓ |
| 10 | Dışarıya yazım | 0 (beyazliste K016*/K018* *.md) | ✓ |

## §H Çocuk-Kapsam Ayrıntı Annexi (kademe-2 her çocuk)

| # | Çocuk | Kapsam girdisi (ne ile başlar) | Kapsam çıktısı (ne bırakır) | Kapsam sonu (dur) |
|---|---|---|---|---|
| 1 | logging/ | LoggerFactory · FileHandler · Redactor (3 dosya) | PSR-3 log satırı · [REDACTED] · security stream | Log okuyucu/SIEM yok |
| 2 | metrics/ | Sayaç/ölçüm tanımı (tasarım) | Etiketli metrik · dışa aktarım | Dashboard/render yok |
| 3 | tracing/ | Span · bağlam (tasarım) | Trace · span · trace-id | Trace backend yok |
| 4 | correlation/ | Request-id üretimi (iskelet) | Satır damgası [trace=...] | Çok-servis topoloji yok |
| 5 | health-checks/ | Uç tanımı (iskelet) | Durum kodu · bağımlılık sorgusu | Deploy bağlantısı → K018 |
| 6 | alerts/ | Koşul/eşik (iskelet) | Uyarı · kanal çağrısı | Aksiyon → K018 |
| 7 | performance/ | Profil/bottleneck (iskelet) | Kayıt · regresyon eşiği paylaşımı | Optimizasyon → üretim |
| 8 | audio-diagnostics/ | xrun/clip/latency (iskelet) | Teşhis eşiği · raporu | DSP düzeltme → K003/K004 |
| 9 | incident-response/ | Runbook/severity (iskelet) | Olay akışı · insan eskalasyonu | Ajan zinciri → K015-AI |
| 10 | alerting/ | Eşik/yükseklik (fazladan, 553 satır) | Yükseklik hesabı | alerts/ ilişkisi — açık karar |

### §H.1 Kapsam giriş-çıkış bağı (logging örneği — kanıtlı çocuk)

| # | Adım | Girdi | İşlem | Çıktı | Kanıt |
|---|---|---|---|---|---|
| 1 | Fabrika | logDir · level | getInstance singleton | FileHandler | LoggerFactory.php Satır 15 |
| 2 | Çağrı | message · context | logAt seviye kontrolü | fileLevel seçimi | FileHandler.php Satır 188 |
| 3 | Bileşim | timestamp · uri · ip · trace | write satır kurma | tek satır | FileHandler.php Satır 220 |
| 4 | Maskeleme | ham satır | Redactor::redact | [REDACTED] satırı | FileHandler.php Satır 224 |
| 5 | Dosyalama | maskeli satır | errors.log append | dosya satırı | FileHandler.php Satır 228 |
| 6 | Güvenlik | securityStream | security.log append | audit satırı | FileHandler.php Satır 234 |
| 7 | Debug split | debugMode | warnings/info split | ek dosya | FileHandler.php Satır 238-244 |

## §I Kapsam Kabul Kriterleri (domain-level)

| # | Kriter | Ölçüt | Durum (2026-10-13) |
|---|---|---|---|
| 1 | Kök 6 MD şeması eksiksiz | index · master · kapsam · bagimliliklar · dogrulama · ornekler | bu görevle tamamlandı |
| 2 | Her kök dosya §4.2 FM | 8 alan + node_id + group · parent: 0 | ✓ |
| 3 | Kapsam sınırı yazılı | §2 · §4 · §6 tabloları dolu | ✓ |
| 4 | Kapsam dışı beyanı | K000/K001/K002/K005/K009/K010/K011/K012 sınırı | ✓ |
| 5 | Repo kanıtı ≥1/dosya | Dosya/Satır · gerçek indeks | ✓ (18 satır) |
| 6 | Web kanıtı | envanter dışı · HTTP 200 | ✓ (1 URL) |
| 7 | Alt-konu planı | §C'de 10 çocuk eksik tablosu | ✓ |
| 8 | Açık sorular | FM open_questions ≥1 | ✓ (3) |
| 9 | Draft kapısı | Açık sorular kapanana dek draft | ✓ (status: draft) |
| 10 | Uydurma yok | Yer-tutucu/dolgu/mojibake 0 | ✓ |

## §J Kapsam Değişim Kontrolü (change control)

| # | Değişim türü | Onay | Etkilenen | Zorunlu güncelleme |
|---|---|---|---|---|
| 1 | Yeni çocuk ekleme | ADR + kullanıcı | index.md children · master §3 | kapsam §3 · registry |
| 2 | Çocuk çıkarma | ADR + kullanıcı | master §3 · disk | kapsam §3 · silme log |
| 3 | Sınır genişleme | Komşu domain mutabakatı | §2 · §4 | master §B · bagimliliklar |
| 4 | Sınır daraltma | ADR | §2 · §6 | master · dogrulama |
| 5 | alerting kararı | Kullanıcı onayı | §3/10 · §5/7 | master §D Faz 4 |
| 6 | Teknoloji seçimi | ADR | §3.1 TASARIM→TAM | master §4 · IMPLEMENTED |
| 7 | Sınır ihlali tespiti | DUR + rapor | §4.1 | master §C.2 |
| 8 | Kapsam dosyası revizyonu | Bu dosya ilk | §1-§J | Authority + FM |

## §K Sınır-İhlali Senaryoları (örnek)

| # | Senaryo | Tetikleyici | Doğru tepki | Yetkili |
|---|---|---|---|---|
| 1 | Bir middleware'e log satırı elle eklendi | LoggerFactory atlandı | DUR · revert · tek fabrikaya dön | Backend + MO |
| 2 | Redactor'suz echo loglandı | hassas veri sızdı | DUR · [REDACTED] · K009 bilgi | Security |
| 3 | K018 deploy'u alert eşiğiyle oynadı | sınır ihlali | DUR · revert · L1→L2 | DevOps + MO |
| 4 | Her servis kendi trace-id'sini üretti | dağıtım | correlation/ tek noktaya taşı | Backend |
| 5 | Health-check servise gömüldü | sahiplik | DUR · tanım K016'ye | Backend + MO |
| 6 | DSP metriği K016'de düzeltildi | domain ihlali | DUR · revert · K003/K004 | Embedded + MO |
| 7 | Vault log satırı silindi | append-only ihlali | git checkout kurtarma | MO (.ai/AGENTS.md §17) |
| 8 | Metrik için uydurma endpoint yazıldı | kanıtsız iddia | VERIFICATION REQUIRED etiketi | her ajan |

## §L Kapsam→Gereksinim İzlenebilirliği (traceability)

| # | Domain gereksinimi | Kapsam maddesi | Çocuk | Kanıt durumu |
|---|---|---|---|---|
| R1 | Tüm loglar PSR-3 olmalı | §5/6 | logging/ | kanıtlı (FileHandler.php Satır 30) |
| R2 | Hassas veri maskelenmeli | §5/5 · §5/18 | logging/ | kanıtlı (Redactor.php Satır 31) |
| R3 | Tek log fabrikası | §2/4 · §5/18 | logging/ | kanıtlı (LoggerFactory.php Satır 11) |
| R4 | Güvenlik olayı ayrı akışta | §5/22 | logging/ | kanıtlı (FileHandler.php Satır 234) |
| R5 | Trace-id satıra damgalanmalı | §5/19 | correlation/ | kanıtlı (Psr3LoggerTest.php Satır 116) |
| R6 | E-posta PII maskelenmeli | §5/25 | logging/ | kanıtlı (LoggerRedactionTest.php Satır 106) |
| R7 | Uyarı→aksiyon sınırı | §5/2 · §4/7 | alerts/ | tasarım |
| R8 | Health tanımı K016'de | §5/12 | health-checks/ | iskelet |
| R9 | Ses teşhisi K016'de | §5/10 | audio-diagnostics/ | iskelet |
| R10 | Metrik toplama tanımlı olmalı | §3/2 | metrics/ | tasarım (kanıt yok) |
| R11 | Tracing tanımlı olmalı | §3/3 | tracing/ | tasarım (kanıt yok) |
| R12 | Incident runbook ürün odaklı | §5/13 | incident-response/ | iskelet |

## §M Kapsam-Dışı Red Listesi (bu domainde ASLA)

| # | Red madde | Neden yasak | Sahip |
|---|---|---|---|
| 1 | PCM5122 kullanımı | ADR-038 H001 — yalnız PCM3168A | K001/K002 |
| 2 | Uygulama loguna secret yazımı | [REDACTED] kuralı (.ai/AGENTS.md §17) | K009 |
| 3 | Deploy/rollback kodu | Aksiyon K018'de | K018 |
| 4 | Test yazımı/koşumu | Test K019'da | K019 |
| 5 | Auth politikası değişikliği | Politika K009'da | K009 |
| 6 | DSP zinciri düzeltmesi | DSP K003/K004'te | K003/K004 |
| 7 | Ajan eskalasyon zinciri | Ajan K015-AI'da | K016 |
| 8 | Optimizasyon kodu | Üretim domaininde | K011/K012 |
| 9 | Kapsam dışı domain dosyası yazımı | Beyazliste ihlali | orkestratör |
| 10 | Uydurma metrik/trace/health endpoint | Zero-hallucination | — |

## §N Sınır-Karar Hızlı Kartı (1 sayfa özet)

| Alan | K016 yapar | K016 yapmaz | Komşu |
|---|---|---|---|
| Log | Yazım · format · redaction | Okuyucu/SIEM · politika | K009 (politika) |
| Metric | Toplama · etiketleme | Render/dashboard | K012 (üretim) |
| Trace | Span · trace-id · iz | Backend depolama | K014 (ağ) |
| Health | Uç tanımı · kod | Servis sahipliği | K012 (sahiplik) |
| Alert | Koşul · eşik · kanal | Deploy/rollback | K018 (aksiyon) |
| Perf | Bottleneck kaydı | Optimizasyon | K011/K012 (kod) |
| Audio | xrun/clip teşhisi | DSP düzeltme | K003/K004 |
| Incident | Ürün runbook'u | Ajan eskalasyonu | K015-AI |
| Vault | Audit trail biçimi | Depolama/senkron | K000 |

**Tek cümle:** K016 sistemi **izler**, üretmez; ölçümler, düzeltmez; uyarır, aksiyon yapmaz.

### §N.1 Hızlı karar akışı (sınır belirsizliğinde)

```text
Soru: "Bu iş K016'de mi?"
  → Üretim/optimizasyon mu? → HAYIR (üretim domaini)
  → Test/coverage mu? → HAYIR (K019)
  → Deploy/rollback aksiyonu mu? → HAYIR (K018)
  → Auth/crypto politikası mı? → HAYIR (K009)
  → Sistemi izleme/gözlem mi? → EVET (K016)
  → Kanıt yoksa? → ⚠️ VERIFICATION REQUIRED + üst karar
```

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode