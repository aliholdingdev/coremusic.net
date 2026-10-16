---
node_id: K009-security/audit/audit-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/audit
  - K009-security/authorization
  - K017-observability
evidence: "Redactor.php·28-31/40/65-93 · FileHandler.php·207-235/248-252 · StructuredLogger.php·61-72 · UserLoggedInEvent.php·26-31/55-60 · SecurityHelper.php·42-55"
validation: "2026-10-15 — 9 repo kanıtı diskten okundu (Dosya·Satır); saklama süresi/imza kanıtı YOK → ⚠️"
open_questions: "saklama süresi + KVKK silme ilkesi ⚠️ VERIFICATION REQUIRED · kayıt bütünlüğü (imza/zoetluk) ⚠️ VERIFICATION REQUIRED · K017 tüketim akışı ⚠️"
group: mimari
---

# K009 · audit · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · parent: K009-security/audit · rol: denetim izi bağılayıcı kurallar.

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K009-security/audit/audit-anayasa |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | ne kaydedilir/ne kaydedilmez · append-only · redaction · tek kayıt · zaman damgası · saklama+KVKK · bütünlük |
| Kapsam dışı | sürüş akışı (audit-surucu) · gerçek kod (audit-ornekler) · sınırlar (audit-kapsam) |
| Sürüş | `audit-surucu.md` · Kod örnekleri: `audit-ornekler.md` · Kanıt: `audit-dogrulama.md` |

> **Temel ilke (index §1'den):** kim, ne zaman, neye erişti, sonuç ne oldu — **sır asla kaydedilmez**.

## §2 Ne Kaydedilir (Kayıt Zorunlu)

| # | Olay | Zorunlu alanlar | Kanıt |
|---|---|---|---|
| 1 | Giriş (login) | userId, ip, userAgent, timestamp | `UserLoggedInEvent.php·26-31/55-60` |
| 2 | Çıkış (logout) | userId, sessionId, timestamp | `UserLoggedOutEvent.php·26-30/54-58` |
| 3 | Başarısız giriş | event=login_failed, reason, ip | `FileHandler.php·155-160` |
| 4 | Yetki reddi (authz denied) | event=authz_denied, uri, role, code | `FileHandler.php·172-181` |
| 5 | CSRF reddi | event=csrf_invalid | `FileHandler.php·179` |
| 6 | Origin reddi | event=origin_blocked | `FileHandler.php·179` |
| 7 | Rate limit tetiklenmesi | event=rate_limited | `FileHandler.php·157/179` |
| 8 | Test bypass aktive | context, file, line | `SecurityHelper.php·42-55` |
| 9 | Sırra erişim olayı | — | ⚠️ KANIT YOK (bkz §12 U-3) |
| 10 | Kayıt/şifre sıfırlama hatası | event=register_error | `FileHandler.php·158` |

## §3 Ne Kaydedilmez (Sır Asla)

| # | Yasaklı veri | Neden | Savunma |
|---|---|---|---|
| 1 | Ham parola / parola hash'i | kimlik sırrı | Redactor `password|passwd` anahtarı — `Redactor.php·31` |
| 2 | Token / CSRF jetonu | taşıyıcı sırrı | Redactor `token|csrf_token` — `Redactor.php·31` |
| 3 | API anahtarı | servis sırrı | Redactor `api_key` — `Redactor.php·31` |
| 4 | Session çerezi / session_id (ham) | oturum sırrı | Redactor `session|session_id|cookie` — `Redactor.php·31` |
| 5 | Authorization başlığı (Bearer/Basic) | kimlik kanıtı | Redactor çok-kelime kuralı — `Redactor.php·18/40` |
| 6 | 64-char hex/base64 hash | anahtar uzunluğu sızıntısı | Redactor hash maskesi — `Redactor.php·49/83` |
| 7 | E-posta düz metin | PII | e-posta maskesi `***@alan` — `Redactor.php·56/86-90` |
| 8 | APP_PEPPER / private.pem içeriği | kripto sırrı | dosyadan hiç okunmaz (secrets) |
| 9 | Sır değeri (secret) | erişim nesnesi | Redactor `secret` — `Redactor.php·31` |

> **Kural:** ham parola hiçbir log yoluna girmez (authentication §7 notu, Password VO dokümanı). Kısmi sızıntıda bile kırmızı: `AGENTS.md §17 Edge Case 3 → [REDACTED]`.

## §4 Append-Only İlkesi

| # | Kural | Kanıt | Durum |
|---|---|---|---|
| 1 | Log yazımı yalnız ekleme (append) | `FileHandler.php·251` → `FILE_APPEND \| LOCK_EX` | ✅ KANITLI |
| 2 | Kilitli yazma (eşzamanlı bozulma önlenir) | `FileHandler.php·251` `LOCK_EX` | ✅ KANITLI |
| 3 | Ayrı audit yazıcısı da append | `SecurityHelper.php·54` → `FILE_APPEND \| LOCK_EX` | ✅ KANITLI |
| 4 | Geçmiş satır düzenleme/dsilme yeteneği kodda YOK | dosyalarda truncate/overwrite yok (okuma 2026-10-15) | ✅ negatif kanıt |
| 5 | Redaction yalnız YAZILACAK satırı etkiler | `Redactor.php·22` (geçmiş .log dosyalarına dokunulmaz) | ✅ KANITLI |
| 6 | Yetkisiz yazma engeli (dosya izinleri/OS) | — | ⚠️ KANIT YOK (K000 kapsam dışı) |

## §5 Redaction (Tek Maskeleme Noktası)

| # | Kural | Kanıt | Durum |
|---|---|---|---|
| 1 | Tüm log yazma yolları Redactor'dan geçer | `Redactor.php·10-11` (FileHandler, StructuredLogger, PageRouterKernel fatal) | ✅ KANITLI |
| 2 | Hassas anahtar-adı eşleşmesi → `[REDACTED]` | `Redactor.php·31/40` | ✅ KANITLI |
| 3 | Mask değeri sabit | `Redactor.php·28` → `MASK = '[REDACTED]'` | ✅ KANITLI |
| 4 | 64-char hash maskesi | `Redactor.php·49` | ✅ KANITLI |
| 5 | E-posta maskesi (alan korunur) | `Redactor.php·56/88` | ✅ KANITLI |
| 6 | UUID korunur (adli iz için) | `Redactor.php·20/82` | ✅ KANITLI |
| 7 | Hata durumunda ham satır döner | `Redactor.php·78-80` (preg null → orijinal) | ✅ KANITLI |
| 8 | camelCase `sessionId` anahtarı key-pattern'e GIRMEZ | `UserLoggedOutEvent.php·56` + `Redactor.php·40` (`\b session \b` sınırı) | ⚠️ VERIFICATION REQUIRED (U-4) |

## §6 Tek Kayıt İlkesi (Her Kritik Olayda Tek Kayıt)

| # | Olay | Kayıt noktası | Çift kayıt riski |
|---|---|---|---|
| 1 | login/logout | domain event + `authEvent` ayrı akışlar | iki farklı hedef (event ↔ log) — çakışma tanımı yok ⚠️ (U-5) |
| 2 | authz/csrf/origin/rate-limit | `securityEvent` → errors.log + security.log AYNI satır | bilinçli ikiz yazım — satır birebir aynı (`FileHandler.php·220/228/234`) |
| 3 | test bypass | `logTestBypass` ayrı dosya (`coremusic_php_errors.log`) + varsa securityEvent | ⚠️ ikiz kayıt senaryosu okunmadı (U-5) |
| 4 | yapısal garanti | bir `logAt` çağrısı = tek satır üretimi | ✅ `FileHandler.php·188-200` |

> **İlke:** olay başına EN AZ BİR kayıt (kayıp yasak) ve mantıksal olarak TEK olay kaydı (ani­moz­luk). İkiz yazım (errors.log + security.log) aynı satırın kopyasıdır — olay sayımını BOZMAZ.

## §7 Zaman Damgası

| # | Katman | Alan | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Domain event | `occurredOn` (DateTimeImmutable, constructor'da) | `UserLoggedInEvent.php·24/31` | ✅ KANITLI |
| 2 | Domain event payload | `timestamp` ISO-8601 (`format('c')`) | `UserLoggedInEvent.php·59` | ✅ KANITLI |
| 3 | StructuredLogger | `timestamp` ATOM formatı | `StructuredLogger.php·62` | ✅ KANITLI |
| 4 | FileHandler satır başı | `[Y-m-d H:i:s]` (yerel saat) | `FileHandler.php·209/220` | ✅ KANITLI |
| 5 | Test bypass kaydı | `date('d-M-Y H:i:s e')` (tz adı dâhil) | `SecurityHelper.php·49` | ✅ KANITLI |
| 6 | Saat senkronu / timezone politikası | — | — | ⚠️ K000 kapsam dışı (index §4) |

## §8 Saklama Süresi ve KVKK Silme İlkesi

| # | Soru | Repo Kanıtı | Durum |
|---|---|---|---|
| 1 | Log saklama süresi tanımlı mı? | log rotation/retention kodu OKUNMADI ve TARAMADA GÖRÜLMEDİ | ⚠️ VERIFICATION REQUIRED |
| 2 | KVKK (m.7 silme/anzıklaştırma) log'a uygulanıyor mu? | log silme rotini YOK | ⚠️ VERIFICATION REQUIRED |
| 3 | PII minimize ediliyor mu? | e-posta maskesi ✅ (`Redactor.php·56`) — kısmi | ✅ kısmi |
| 4 | Silme talebi redacted log'a nasıl yansır? | redaction geçmişe dokunmaz (`Redactor.php·22`) → silme yalnız fiziksel rotinle | ⚠️ (U-2) |
| 5 | Süre dolan kayıt temizliği | kod kanıtı YOK | ⚠️ VERIFICATION REQUIRED |

> **İlke (KVKK):** denetim kaydı hukuki dayanağı olmadan SÜRESİZ saklanamaz; süre dolduğunda silme/anonimleştirme gerekir. CoreMusic'te bu rotin **belgelenmemiştir** → `⚠️ VERIFICATION REQUIRED` (bkz `audit-dogrulama.md` AD-12/AD-13).

## §9 Kayıt Bütünlüğü (İmza / Zoetluk)

| # | Mekanizma | Repo Kanıtı | Durum |
|---|---|---|---|
| 1 | Kriptografik imza (HMAC/sign) ile kayıt doğrulama | imza/verify kodu TARAMADA YOK | ⚠️ VERIFICATION REQUIRED |
| 2 | Write-once depolama (zoetluk) | dosya tabanlı append; OS düzeyi bilinmiyor | ⚠️ VERIFICATION REQUIRED |
| 3 | Zincirleme hash (önceki satır hash'i) | YOK | ⚠️ KANIT YOK |
| 4 | Ayrı security stream (errors.log kalabalığından ayrışır) | `FileHandler.php·230-235` → `coremusic_php_security.log` | ✅ KANITLI (korumanın başlangıcı) |
| 5 | LOCK_EX ile eşzamanlı bozulma önlenir | `FileHandler.php·251` | ✅ KANITLI (bütünlüğün bir parçası) |
| 6 | SIEM/uzak kopya | akış bilinmiyor | UNKNOWN (K017) |

## §10 Bağlayıcı Kurallar (Anayasa Maddeleri)

| Madde | Kural | Yaptırım |
|---|---|---|
| A-01 | Kritik olay (login/logout/yetki reddi/sır erişimi) **kayıtsız** kalamaz | RED |
| A-02 | Sır/parola/token değerleri log'a **ham** girmez | RED — `Redactor.php·31` |
| A-03 | Log yazımı append-only'dir; mevcut kayıt düzenlenemez/silinemez | RED — `FileHandler.php·251` |
| A-04 | Tüm log satırları tek maskeleme noktasından (Redactor) geçer | RED — `Redactor.php·10-11` |
| A-05 | Her kayıt zaman damgalıdır | RED — §7 |
| A-06 | Saklama süresi tanımlanmadan KVKK uyumu iddia EDİLMEZ | ⚠️ işareti zorunlu |
| A-07 | Bütünlük (imza/zoetluk) kanıtı olmadan "değiştirilemez kayıt" iddia edilmez | ⚠️ işareti zorunlu |
| A-08 | Audit satırında kullanıcıya dönen hata ile aynı sızıntı düzeyi bulunmaz | RED |
| A-09 | Olay kaydı olay anında üretilir (toplu/sonradan yazım yasak) | RED |
| A-10 | Kayıt alanı genişletmesi (yeni alan) redaction listesini yeniden denetler | BLOCK |

## §11 Yasaklar (Forbidden)

| # | Yasak | Sınıf |
|---|---|---|
| Y-01 | Log'a ham parola/hash yazmak | sızıntı |
| Y-02 | Token/cookie/API anahtarını ham loglamak | sızıntı |
| Y-03 | Mevcut log satırını düzenleme/silme (append-only ihlali) | bütünlük |
| Y-04 | Redactor'ü atlayan log yazıcısı açmak | maskeleme boşluğu |
| Y-05 | E-postayı düz metin loglamak | PII |
| Y-06 | Yetki reddi/erişim olayını "gereksiz gürültü" diye susturmak | denetim boşluğu |
| Y-07 | Kayıt bütünlüğü olmadan uyumluluk sertifikasyonu iddiası | hukuki |
| Y-08 | Kullanıcı silme talebinde KVKK'yı "log hariç" diye reddedilen gerekçesiz bırakma | hukuki |

## §12 Kritik Olay Envanteri (Bağlayıcı)

| # | Olay | Üretici | Kanıt | Durum |
|---|---|---|---|---|
| 1 | user.logged_in | domain event | `UserLoggedInEvent.php·39` | ✅ |
| 2 | user.logged_out | domain event | `UserLoggedOutEvent.php·38` | ✅ |
| 3 | login_failed / rate_limited | `authEvent` seviye eşlemesi | `FileHandler.php·157` | ✅ |
| 4 | authz_denied | `securityEvent` | `FileHandler.php·179` | ✅ |
| 5 | csrf_invalid | `securityEvent` | `FileHandler.php·179` | ✅ |
| 6 | origin_blocked | `securityEvent` | `FileHandler.php·179` | ✅ |
| 7 | bypass_activated | `securityEvent` | `FileHandler.php·179` | ✅ (çağrıran kod okunmadı ⚠️) |
| 8 | TEST_BYPASS (dosya kaydı) | `SecurityHelper::logTestBypass` | `SecurityHelper.php·42-55` | ✅ |
| 9 | secret_access (sırra erişim) | — | — | ⚠️ KANIT YOK (U-3) |
| 10 | password_reset_requested | event dosyası mevcut | `PasswordResetRequestedEvent.php` (dosya var, satır okunmadı) | ⚠️ kısmi |

## §13 Hata Yolu Zorunlulukları

| # | Kural | Neden |
|---|---|---|
| H-01 | Redaction `preg` null dönerse satır YOK SAYILMAZ, ham döner | kayıp yasak (`Redactor.php·78-80`) |
| H-02 | Yazma hatası (`@file_put_contents`) sessizdir → sessizlik fark edilmeli | `FileHandler.php·251` `@` suppress ⚠️ (U-6) |
| H-03 | Log hata mesajı güvenlik olayını **durdurmaz** (fail-safe log) | denetim sürekliliği |
| H-04 | securityEvent seviyesi 'warning' (düşme yok) | `FileHandler.php·178-181` |
| H-05 | authEvent seviyesi olaya göre info/warning/error | `FileHandler.php·155-160` |

## §14 Doğrulama Bağlantısı

| Anayasa Maddesi | Doğrulama Satırı |
|---|---|
| A-01 (kritik olay kaydı) | AD-01, AD-06 |
| A-02 (redaction) | AD-02, AD-03, AD-04 |
| A-03 (append-only) | AD-05 |
| §8 (saklama/KVKK) | AD-12, AD-13 |
| §9 (bütünlük) | AD-10, AD-11 |
| §7 (zaman damgası) | AD-07, AD-08, AD-09 |
| §6 (tek kayıt) | AD-14 |

## §15 Bilinmeyenler Defteri

| # | Soru | Etiket |
|---|---|---|
| U-1 | Saklama süresi / retention politikası | ⚠️ VERIFICATION REQUIRED |
| U-2 | KVKK silme/anonimleştirme rotini | ⚠️ VERIFICATION REQUIRED |
| U-3 | Sırra erişim (secret_access) olay tanımı | ⚠️ VERIFICATION REQUIRED |
| U-4 | camelCase `sessionId` payload alanının maskelenmesi | UNKNOWN |
| U-5 | İkiz kayıt (event + log) sayım kuralı | UNKNOWN |
| U-6 | `@file_put_contents` sessiz yazma hatası | UNKNOWN |
| U-7 | Kayıt bütünlüğü (imza/zoetluk) mekanizması | ⚠️ VERIFICATION REQUIRED |

## §16 Bağlantılar

| Bağ | Yön | Not |
|---|---|---|
| [[index]] | üst | denetim izi künyesi |
| [[audit-surucu]] | kardeş | anayasa maddelerinin sürüşü |
| [[audit-ornekler]] | kardeş | madde → gerçek kod |
| [[audit-dogrulama]] | kardeş | madde → AD-xx kanıtı |
| [[authorization-ornekler]] | öncesi | yetki reddi olayını üreten katman |
| [[secrets-kapsam]] | öncesi | sırra erişim olaylarının kaynağı (⚠️ U-3) |
| [[security-master]] | üst | K009 ana düğüm |
| K017-observability | sonrası | tüketici sistem (index §5) |

## §17 Kayıt Alanları Sözleşmesi (Audit Kaydı)

| Alan | Zorunlu mu | Kaynak | Kanıt |
|---|---|---|---|
| timestamp | Evet | DateTimeImmutable | `StructuredLogger.php·62` |
| level | Evet | sabit seviye kümesi | `StructuredLogger.php·9` |
| module | Evet | çağıran modül | `StructuredLogger.php·63` |
| event | Evet | olay adı | `StructuredLogger.php·64` |
| traceId | İsteğe bağlı | istek korelasyonu | `StructuredLogger.php·66` |
| ip | Olaya bağlı | `$_SERVER` | `FileHandler.php·212` |
| uri / method | Olaya bağlı | `$_SERVER` | `FileHandler.php·210-211` |
| userId / role | Olaya bağlı | `_auth` / event | `UserLoggedInEvent.php·56` |
| reason / code | Olaya bağlı | olay bağlamı | `FileHandler.php·172-175` |
| ham sır değeri | ASLA | — | Redactor maskeler (`Redactor.php·31`) |

## §18 Loglama Seviye Politikası (Olay Sınıfı)

| Olay sınıfı | Seviye | Kanıt |
|---|---|---|
| login_success / register_success / logout | info | `FileHandler.php·156` |
| login_failed / register_failed / rate_limited | warning | `FileHandler.php·157` |
| login_error / register_error | error | `FileHandler.php·158` |
| authz_denied / csrf_invalid / origin_blocked / bypass_activated | warning | `FileHandler.php·179` |
| debug (yalnız debug mode) | debug | `FileHandler.php·190-193` |
| bilinmeyen seviye | error'a düşer (fail-safe) | `FileHandler.php·127-130` |

## §19 Akış Hedefleri (Nereye Yazılır)

| Hedef dosya | Koşul | Kanıt |
|---|---|---|
| coremusic_php_errors.log | tüm seviyeler, her zaman | `FileHandler.php·228` |
| coremusic_php_security.log | securityStream=true (audit tail'i) | `FileHandler.php·233-235` |
| coremusic_php_warnings.log | debug mode + warning ailesi | `FileHandler.php·238-241` |
| coremusic_php_info.log | debug mode + info/debug | `FileHandler.php·242-244` |
| coremusic_php_errors.log (TEST_BYPASS) | bypass aktifse ayrı yazıcı | `SecurityHelper.php·46` |

## §20 Olay Üretici Katman Matrisi

| Katman | Ürettiği olay | Kanıt |
|---|---|---|
| Domain (Events/Domain) | user.logged_in / user.logged_out | `UserLoggedInEvent.php·39`, `UserLoggedOutEvent.php·38` |
| Middleware | authz_denied / csrf_invalid / origin_blocked / rate_limited | `FileHandler.php·179` (çağrıran kod ⚠️ okunmadı) |
| Security (bypass) | TEST_BYPASS | `SecurityHelper.php·42-55` |
| Controller (auth) | login_success / login_failed | `FileHandler.php·155-160` |
| PageRouter (fatal) | kernel log ayrı yazıcı | `FileHandler.php·23` (doküman kanıtı) |

## §21 Kayıp ve Gürültü Dengesi

| Durum | Karar | Gerekçe |
|---|---|---|
| Olay üretildi ama loglanamadı | kabul edilemez (A-01) | denetim boşluğu |
| Aynı olay 2 stream'e yazıldı | kabul edilir (aynı satır) | §6 notu — sayım bozulmaz |
| Yüksek hacimli debug | yalnız debug mode | `FileHandler.php·60` |
| Üretimde info akışı | errors.log'a yazar (mevcut davranış) | `FileHandler.php·226-228` |
| Kayıt alanı bilinmiyor | alan yazılmaz, uydurulmaz | Zero-Hallucination |

## §22 Uyum Matrisi (Standartlarla)

| Kural | Kaynak | Uyum |
|---|---|---|
| Hassas veri log'da → [REDACTED] | `AGENTS.md §17 Edge Case 3` | ✅ `Redactor.php·31` |
| Sır asla loga (Zero-Hallucination/gizlilik) | `.ai/AGENTS.md` | ✅ §3 + §5 |
| Audit trail append-only | `.ai/AGENTS.md §5` (log.md kuralı) | ✅ `FileHandler.php·251` |
| Yetersiz loglama (CWE-778) | https://cwe.mitre.org/data/definitions/778.html | ⚠️ §8/§9 boşlukları |
| Hassas bilgi log sızıntısı (CWE-532) | https://cwe.mitre.org/data/definitions/532.html | ✅ redaction (§5) |
| KVKK silme/anonimleştirme | mevzuat | ⚠️ KANIT YOK (U-2) |

## §23 Senaryo × Sonuç Matrisi

| # | Senaryo | Beklenen Sonuç | Kural |
|---|---|---|---|
| S-1 | Başarılı login | user.logged_in kaydı + authEvent | A-01 |
| S-2 | Yanlış parola | login_failed (warning) — parola ham GİRMEZ | A-02, Y-01 |
| S-3 | Yetkisiz rota denemesi | authz_denied security akışı | A-01 |
| S-4 | Log'a `token=abc123` sızarsa | `[REDACTED]` | §5-2 |
| S-5 | Log'a 64-hex sızarsa | `[REDACTED]` | §5-4 |
| S-6 | E-posta loglanır | `***@alan` | §5-5 |
| S-7 | Log yazma başarısız (disk) | sessiz ⚠️ (U-6) — fail-safe değil | H-02 |
| S-8 | Kullanıcı veri silme ister | KVKK rotini yok → ⚠️ | U-2 |
| S-9 | Log dosyası elle düzenlenir | imza/zoetluk yok → tespit edilemez ⚠️ | U-7 |
| S-10 | Eşzamanlı 2 yazıcı | LOCK_EX ile sıralı | §4-1 |

## §24 Anti-Pattern (Sık Yapılan Hatalar)

| # | Anti-Pattern | Doğrusu |
|---|---|---|
| AP-1 | `var_dump`/`print_r` ile debug logu | StructuredLogger + redaction |
| AP-2 | Exception mesajını ham loglamak | redacted satır (A-04) |
| AP-3 | Audit kaydını "performans" için atlamak | A-01 — kritik olay atlanamaz |
| AP-4 | Log dosyasını döndürmeden (rotation) büyütmek | retention ⚠️ U-1 |
| AP-5 | `sessionId` gibi alanları maskeleme listesine EKLEMEDEN PII saymak | U-4 — key listesi genişletilmeli |

## §25 Roller ve Sorumluluklar (RACI)

| Görev | Sorumlu | Onay |
|---|---|---|
| Anayasa maddeleri | Vault Steward | Security Engineer |
| Redaction listesi genişletme | Security Engineer | Architect |
| Saklama süresi kararı (KVKK) | İnsan (Bayram Ali) | Legal ⚠️ |
| Bütünlük mekanizması (imza) ADR | Architect | Vault Steward |
| Doğrulama satırları (AD-xx) | Vault Steward (subagent) | QA Engineer |

## §26 Karar Ağacı (Olay Kaydı)

```text
Kritik olay üretildi mi?
  ├─ HAYIR → kayıt yok (kural ihlali değil)
  └─ EVET  → Sır/parola/token içeriyor mu?
        ├─ EVET  → Redactor ile maskele (A-02)
        └─ HAYIR → ham bırak
     → Zaman damgası ekle (A-05)
     → Append ile yaz (A-03)
     → security akışı mı? → ikiz stream'e de yaz (§6)
     → K017-observability tüketir (index §5)
```

## §27 Sözlük (TR ↔ EN ↔ Kod)

| Türkçe | English | Kod Karşılığı |
|---|---|---|
| Denetim izi | Audit trail | `coremusic_php_security.log` |
| Maskeleme | Redaction | `Redactor::redact()` |
| Olay kaydı | Audit record | `StructuredLogger::log()` |
| Zaman damgası | Timestamp | `DateTimeImmutable` (`occurredOn`) |
| Yalnız-ekleme | Append-only | `FILE_APPEND` |
| Kayıt bütünlüğü | Log integrity | ⚠️ (imza yok — U-7) |
| Saklama süresi | Retention | ⚠️ (U-1) |
| Silme/anonimleştirme | Erasure / anonymization | ⚠️ KVKK (U-2) |

## §28 Revizyon ve Sürüm

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 9 repo kanıtı diskten okundu; 7 bilinmeyen ⚠️ | Vault Steward (subagent) |

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 (ilk yazım) |
| Tarih | 2026-10-15 |
| Durum | draft (U-1..U-7 açık) |
| Erişim | `.ai/architecture/K009-security/audit/audit-anayasa.md` |
| git commit | ATILMADI (subagent yasağı) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
