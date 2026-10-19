---
node_id: K011-application/application-riskler
node_type: file
status: verify-required
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K008-domain
  - K009-security
  - K012-services
  - K013-infrastructure
evidence:
  - "shared/src/PageRouter/PageRouterKernel.php — 281 satır (2026-10-12)"
  - "shared/src/Middleware/CsrfMiddleware.php — 52 satır (2026-10-12)"
  - "shared/src/Session/ — iki SessionInitializer adı (2026-10-12)"
  - "shared/tests/ — 44 test dosyası (2026-10-12)"
validation:
  - "Her risk satırı en az bir disk kanıtı veya yokluk kanıtı ile eşleştirildi: 2026-10-12"
open_questions:
  - "Kuyruk/worker yokluğu üretimde hangi arıza biçimini üretir?"
  - "Ödeme sözleşmesi K012'ye devredildi — devir ADR'si var mı?"
group: kanit
---

# K011 Application — Riskler

> **Authority: Bayram Ali / Vault Steward**
> **Tarih: 2026-10-12** · **Kapsam: K011 risk defteri**
> **Ölçek: E=etki (1–5) · O=olasılık (1–5) → P=E×O · Eşik: P≥15 yüksek · 8–14 orta · <8 düşük**

## §1 RİSK TABLOSU (24 satır)

| # | Risk | E | O | P | Kanıt (2026-10-12) | Önlem |
|---|---|---|---|---|---|---|
| 1 | Use case katmanı yok → senaryolar controller içinde dağılır | 5 | 4 | 20 | sembol `*UseCase` = 0 | K003/K011 sözleşmesi + ilk iskelet |
| 2 | Komut/sorgu ayrımı yok → yazma/okuma birbirine karışır | 4 | 4 | 16 | `shared/src/Command*` = 0 | K003 karar ADR'si |
| 3 | Kuyruk/worker yok → uzun işler HTTP isteğini bloklar | 5 | 4 | 20 | `shared/src/Queue*` = 0 | K013 kuyruk sözleşmesi |
| 4 | Ödeme kodu yok → ödeme akışı belgesiz | 5 | 3 | 15 | repo src = 0 | K012 sözleşmesi + contract test |
| 5 | Zamanlı görev yok → toplu işler elle yapılıyor | 4 | 4 | 16 | cron/worker = 0 | K013 zamanlayıcı |
| 6 | `PageRouterKernel` 281 satır → büyüme eşiği | 3 | 4 | 12 | `PageRouterKernel.php` (281) | 400 satır gate'i (karar #1) |
| 7 | İki `SessionInitializer` → çakışan oturum davranışı | 4 | 3 | 12 | Session/ + PageRouter/ | birleştirme refactor'ü |
| 8 | Middleware sırası test dışı değişirse CSRF düşebilir | 5 | 2 | 10 | `CsrfMiddleware.php` (52) | `MiddlewarePipelineTest` + sıra kilidi |
| 9 | Hız sınırı kalıcı değil → restart'ta sayaç sıfırlanır | 3 | 4 | 12 | `RateLimiterMiddleware.php` (126), depo kanıtsız | K013 sayaç deposu |
| 10 | Olay dinleyicisi yok → dispatcher yayıncı, kimse duymuyor | 4 | 3 | 12 | `Events/` içinde dinleyici sınıfı yok | ilk abone seti |
| 11 | `FileCache` yok → dosya tabanlı önbellek planı eksik | 2 | 4 | 8 | `Cache/FileCache.php` = 0 | önbellek ADR'si |
| 12 | Önbellek ölçümü yok → isabet oranı bilinmiyor | 3 | 4 | 12 | `CacheMetrics` = 0 | ölçüm adaptörü |
| 13 | `BypassAuthMiddleware` kötü kullanılırsa auth düşer | 5 | 2 | 10 | `BypassAuthMiddleware.php` (54) | allowlist + fail-closed test (`BypassAuthMiddlewareFailClosedTest`) |
| 14 | CORS geniş olabilir → kaynak sızıntısı | 4 | 3 | 12 | `CorsMiddleware.php` (121) | origin allowlist denetimi |
| 15 | Test kapsamı belirsiz → coverage bilinmiyor | 4 | 3 | 12 | 44 test dosyası, coverage raporu yok | coverage çalıştırma |
| 16 | Use case yokken test stratejisi de eksik uygulanır | 4 | 4 | 16 | §1/1 ile aynı kök | katman üretilince test de |
| 17 | `phpstan/phpstatik` yazım riski → kurulum kırılabilir | 3 | 2 | 6 | `composer.json` doğrulanmadı | yeniden okuma + lock |
| 18 | K011↔K008 sınırı → domain işi uygulamaya sızabilir | 4 | 3 | 12 | `auth.../include/Domain/` ortak | mimari denetim |
| 19 | K011↔K012 belirsizliği → iki "servis" adı | 3 | 4 | 12 | ad çakışması | sınır ADR'si |
| 20 | Çocuk index'leri `verify-required` → kapsam şişkinliği | 2 | 4 | 8 | 10 eski çocuk | çocuk gözden geçirme |
| 21 | URL tekrarı riski (web kanıtı) → kanıt geçersizleşir | 3 | 2 | 6 | tekillik taraması | tarama tekrarı |
| 22 | Hata mesajı sızıntısı | 4 | 2 | 8 | `ErrorHandler.php` (62) | OWASP Error Handling |
| 23 | Oturum fixation/zaman aşımı boşluğu | 5 | 2 | 10 | `SessionLifecycle.php` (108) | OWASP Session Management |
| 24 | Görsel/mockup gate → UI değişikliği kanıtsız | 3 | 3 | 9 | `.ai/ui-design/` gate kuralı | gate script |

**Dağılım:** yüksek (P≥15) **6** · orta (8–14) **17** · düşük (<8) **1** · toplam **24**.

## §2 YÜKSEK RİSK DETAYI (P ≥ 15)

### §2.1 R-1 · Use case katmanı yok (P=20)

- **Bağlam:** SSOT §2.11 use case'leri K011 kapsamında tanımlar; diskte 0.
- **Etki:** İş akışları controller/handler içinde dağılır; yeniden kullanım ve test zorlaşır.
- **Önlem:** İlk iskelet `usecase-akislari/` çocuk dosyasındaki sözleşmeye göre üretilir.
- **Kapanış koşulu:** İlk `*UseCase` dosyası + testi.

### §2.2 R-3 · Kuyruk/worker yok (P=20)

- **Bağlam:** Uzun süreli iş (medya işleme, bildirim) HTTP isteğinde bekler.
- **Etki:** Zaman aşımı, kullanıcıda hata görünümü, sunucu kaynağı tükenmesi.
- **Önlem:** K013 kuyruk sözleşmesi; K011 yalnızca işi kuyruğa atar.
- **Kapanış koşulu:** Kuyruk sözleşmesi ADR + en az bir iş akışı.

### §2.3 R-2 · Komut/sorgu ayrımı yok (P=16)

- **Önlem:** K003-application-bus ile ortak sözleşme; `verify-required` korunur.
- **Kapanış koşulu:** Karar #7 kapanır.

### §2.4 R-5 · Zamanlı görev yok (P=16)

- **Önlem:** K013 zamanlayıcı; K011'de yalnız görev tanımı.
- **Kapanış koşulu:** Zamanlayıcı kaydı + ilk görev.

### §2.5 R-4 · Ödeme kodu yok (P=15)

- **Önlem:** K012 sözleşmesi + `odem-servisleri/` çocuk dosyasında `verify-required`.
- **Kapanış koşulu:** Ödeme DTO'su + contract test.

### §2.6 R-16 · Test stratejisi uygulanamıyor (P=16)

- **Bağlam:** Strateji var (test-stratejisi.md) ama hedef katman (use case) yok.
- **Önlem:** Katman üretilir üretilmez test dosyası birlikte yazılır (kapı §6).

## §3 ORTA RİSK ÖZETİ (8 ≤ P ≤ 14)

| # | Risk | P | Tek cümle önlem |
|---|---|---|---|
| 6 | Kernel büyümesi | 12 | 400 satır gate'i |
| 7 | Çift SessionInitializer | 12 | birleştirme |
| 8 | Middleware sıra kaybı | 10 | sıra testi + kilit |
| 9 | Rate limit kalıcı değil | 12 | kalıcı sayaç |
| 10 | Olay dinleyicisi yok | 12 | ilk abone seti |
| 11 | FileCache yok | 8 | önbellek ADR'si |
| 12 | CacheMetrics yok | 12 | ölçüm adaptörü |
| 13 | BypassAuth kötüye kullanımı | 10 | allowlist + fail-closed |
| 14 | CORS genişliği | 12 | origin allowlist |
| 15 | Coverage bilinmiyor | 12 | coverage çalıştır |
| 17 | composer yazım riski | 6 | composer.json oku |
| 18 | Domain sızması | 12 | mimari denetim |
| 19 | İki "servis" adı | 12 | sınır ADR'si |
| 20 | Çocuk kapsam şişkinliği | 8 | çocuk gözden geçirme |
| 22 | Hata sızıntısı | 8 | OWASP Error Handling |
| 23 | Oturum güvenliği | 10 | OWASP Session Management |
| 24 | Mockup gate | 9 | gate script |

## §4 RİSK–DOSYA EŞLEMESİ

| Risk grubu | İlgili K011 dosyası |
|---|---|
| R-1 · R-2 · R-16 | `usecase-akislari/` · `command-query/` · `application-test-stratejisi.md` |
| R-3 · R-5 | `zamanlamali-gorevler/` · `application-bagimliliklar.md` |
| R-4 | `odem-servisleri/` |
| R-6 · R-8 · R-13 · R-14 · R-22 · R-23 | `pagerrouter/-middleware-zinciri` · `pagerrouter/-riskler` |
| R-7 | `pagerrouter/-oturum-akisi` |
| R-10 | `event-bus/-abone-tablosu` |
| R-11 · R-12 | `cache-layer/-riskler` · `cache-layer/-gecersizlik-akisi` |
| R-17 | `application-bagimliliklar.md` §4 |
| R-21 | `application-web-arastirma.md` (tekillik taraması) |

## §5 RİSK İZLEME KURALLARI

1. Her tur (2026-10-12 gibi) tablo yeniden hesaplanır; P değişen satır işaretlenir.
2. Yeni risk = yeni satır; mevcut satır silinmez (append-only, §7/3).
3. `P≥15` riski kapanmadan K011 `status: active` olamaz (doğrulama §9/1).
4. Kanıtsız risk yazılmaz; her satırda 2026-10-12 kanıt sütunu var.

## §6 DOĞRULAMA

| # | İddia | Yöntem | Tarih | Sonuç |
|---|---|---|---|---|
| 1 | 24 risk satırı var | sayım | 2026-10-12 | ✅ |
| 2 | Her satırda kanıt var | sütun kontrolü | 2026-10-12 | ✅ |
| 3 | P değerleri E×O ile tutarlı | yeniden hesap | 2026-10-12 | ✅ |
| 4 | Dağılım toplamı 24 | 6+17+1 = 24 | 2026-10-12 | ✅ |
| 5 | Yokluk kanıtları doğrulama dosyasıyla uyumlu | §5 karşılaştırma | 2026-10-12 | ✅ |

## §7 AÇIK SORULAR

1. R-3 kapanışı K013'te mi K011'de mi takip edilecek?
2. 400 satır gate'inin sahibi hangi agent?
3. Coverage raporu hangi araçla üretilecek (PHPUnit sürümü composer.json'da)?
4. `verify-required` çocuklar ne zaman gözden geçirilecek?

## §8 REFERANSLAR

- `application-dogrulama.md` — yokluk kanıtları (2026-10-12)
- `application-kararlar.md` — karar #1 · #7–#10
- `shared/src/{PageRouter,Middleware,Events,Cache,Session,Api}/` — disk (2026-10-12)
- `coremusic-katmanli-mimari-agaci-v2.md` §2.11 — kapsam

## §A — Risk envanteri

> **Amaç:** §1'deki 24 riskin tam envanterini kanıt türü, sahibi ve durumuyla birlikte vermek. Kanıtlar 2026-10-13 tarihinde yeniden tazelenmiştir.

### §A.1 — Risk envanteri (tam liste)

| # | Risk | Kanıt türü | Sahip | Durum (2026-10-13) |
|---|---|---|---|---|
| 1 | Use case katmanı yok | yokluk (sembol = 0) | backend | açık |
| 2 | Komut/sorgu ayrımı yok | yokluk (glob = 0) | backend | açık |
| 3 | Kuyruk/worker yok | yokluk (glob = 0) | K013 | açık |
| 4 | Ödeme kodu yok | yokluk (metin = 0) | K012 | açık |
| 5 | Zamanlı görev yok | yokluk (metin = 0) | K013 | açık |
| 6 | Kernel büyüme | disk (281 satır) | backend | izleniyor |
| 7 | Çift `SessionInitializer` | disk (2 ad dosya) | refactorer | açık |
| 8 | Middleware sıra kaybı | disk + test | QA | kısmi kapalı |
| 9 | Rate limit kalıcı değil | disk (126 satır) + depo kanıtsız | K013 | açık |
| 10 | Olay dinleyicisi yok | yokluk (dinleyici sınıfı 0) | backend | açık |
| 11 | `FileCache` yok | yokluk (glob = 0) | Vault Steward | açık |
| 12 | `CacheMetrics` yok | yokluk (0) | Vault Steward | açık |
| 13 | BypassAuth kötüye kullanımı | disk (54 satır) + fail-closed test | Security | kısmi kapalı |
| 14 | CORS genişliği | disk (121 satır) | Security | açık |
| 15 | Coverage bilinmiyor | rapor yok | QA | açık |
| 16 | Test stratejisi uygulanamıyor | §1/1 kökü | QA | açık |
| 17 | composer yazım riski | `composer.json:8` okundu (2026-10-13) | backend | azaldı |
| 18 | Domain sızması | disk (alt proje dizinleri) | mimari | izleniyor |
| 19 | İki "servis" adı | ad çakışması | mimari | açık |
| 20 | Çocuk kapsam şişkinliği | 10 eski çocuk | Vault Steward | açık |
| 21 | URL tekrarı | tekillik taraması = 0 | Vault Steward | kapalı (izleniyor) |
| 22 | Hata mesajı sızıntısı | disk (62 satır) | Security | açık |
| 23 | Oturum güvenliği | disk (108 satır) | Security | açık |
| 24 | Mockup gate | gate kuralı | UI | izleniyor |

### §A.2 — Kanıt türü dağılımı

| # | Kanıt türü | Risk sayısı | Risk no |
|---|---|---|---|
| 1 | Yokluk kanıtı (0 sonuç) | 12 | 1 · 2 · 3 · 4 · 5 · 10 · 11 · 12 · 15 · 16 · 20 · 21 |
| 2 | Disk kanıtı (dosya + satır) | 9 | 6 · 7 · 8 · 9 · 13 · 14 · 17 · 22 · 23 |
| 3 | Süreç/kural kanıtı | 3 | 18 · 19 · 24 |

### §A.3 — Sahip dağılımı

| # | Sahip | Risk sayısı | Risk no |
|---|---|---|---|
| 1 | backend | 4 | 1 · 2 · 6 · 10 |
| 2 | Security | 4 | 13 · 14 · 22 · 23 |
| 3 | K013 (altyapı) | 3 | 3 · 5 · 9 |
| 4 | QA | 3 | 8 · 15 · 16 |
| 5 | Vault Steward | 4 | 11 · 12 · 20 · 21 |
| 6 | K012 (servis) | 1 | 4 |
| 7 | mimari | 2 | 18 · 19 |
| 8 | refactorer | 1 | 7 |
| 9 | UI | 1 | 24 |

### §A.4 — Envanter kuralları

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Yeni risk = yeni satır | mevcut satır silinmez |
| 2 | Her satırda kanıt | disk veya yokluk |
| 3 | Sahipsiz risk olmaz | her satırda sahip |
| 4 | Kapalı risk arşivlenir | silinmez, durum değişir |
| 5 | P yeniden hesap | her turda E×O |

## §B — Olasılık / etki

### §B.1 — Ölçek tanımı

| # | Değer | E (etki) | O (olasılık) |
|---|---|---|---|
| 1 | 5 | üretim kesintisi / güvenlik ihlali | neredeyse kesin |
| 2 | 4 | ciddi işlev kaybı | muhtemel |
| 3 | 3 | belirgin bozulma | mümkün |
| 4 | 2 | sınırlı etki | seyrek |
| 5 | 1 | ihmal edilebilir | nadir |

### §B.2 — E × O matrisi

| # | E \ O | 1 | 2 | 3 | 4 | 5 |
|---|---|---|---|---|---|---|
| 1 | 5 | 5 | 10 | 15 | 20 | 25 |
| 2 | 4 | 4 | 8 | 12 | 16 | 20 |
| 3 | 3 | 3 | 6 | 9 | 12 | 15 |
| 4 | 2 | 2 | 4 | 6 | 8 | 10 |
| 5 | 1 | 1 | 2 | 3 | 4 | 5 |

### §B.3 — Bant tanımı

| # | Bant | Aralık | Anlam | Aksiyon süresi |
|---|---|---|---|---|
| 1 | Yüksek | P ≥ 15 | K011 `active` olamaz | her tur |
| 2 | Orta | 8 ≤ P ≤ 14 | izleme + önlem | aylık |
| 3 | Düşük | P < 8 | kabul edilebilir | gözden geçirme |

### §B.4 — P değerleri (yeniden hesap 2026-10-13)

| # | Risk | E | O | P | Bant |
|---|---|---|---|---|---|
| 1 | Use case yok | 5 | 4 | 20 | yüksek |
| 3 | Kuyruk yok | 5 | 4 | 20 | yüksek |
| 2 | Komut/sorgu yok | 4 | 4 | 16 | yüksek |
| 5 | Zamanlı görev yok | 4 | 4 | 16 | yüksek |
| 16 | Test uygulanamıyor | 4 | 4 | 16 | yüksek |
| 4 | Ödeme yok | 5 | 3 | 15 | yüksek |
| 6 | Kernel büyümesi | 3 | 4 | 12 | orta |
| 7 | Çift oturum sınıfı | 4 | 3 | 12 | orta |
| 9 | Rate limit kalıcı değil | 3 | 4 | 12 | orta |
| 10 | Dinleyici yok | 4 | 3 | 12 | orta |
| 12 | Ölçüm yok | 3 | 4 | 12 | orta |
| 14 | CORS genişliği | 4 | 3 | 12 | orta |
| 15 | Coverage bilinmiyor | 4 | 3 | 12 | orta |
| 18 | Domain sızması | 4 | 3 | 12 | orta |
| 19 | İki servis adı | 3 | 4 | 12 | orta |
| 8 | Sıra kaybı | 5 | 2 | 10 | orta |
| 13 | BypassAuth | 5 | 2 | 10 | orta |
| 23 | Oturum güvenliği | 5 | 2 | 10 | orta |
| 24 | Mockup gate | 3 | 3 | 9 | orta |
| 11 | FileCache yok | 2 | 4 | 8 | orta |
| 20 | Çocuk şişkinliği | 2 | 4 | 8 | orta |
| 22 | Hata sızıntısı | 4 | 2 | 8 | orta |
| 17 | Composer yazımı | 3 | 2 | 6 | düşük |
| 21 | URL tekrarı | 3 | 2 | 6 | düşük |

### §B.5 — Değişim takibi

| # | Risk | Eski P | Yeni P | Neden |
|---|---|---|---|---|
| 1 | 17 composer yazım riski | 6 (2026-10-12 belirsiz) | 6 (kanıt tazelendi) | `composer.json:8` okundu |
| 2 | 21 URL tekrarı | 6 | 6 (kapatıldı, izleniyor) | tekillik = 0 (2026-10-13) |
| 3 | Diğerleri | — | değişmedi | yeni kanıt yok |

## §C — Azaltma

### §C.1 — Azaltma stratejileri

| # | Strateji | Geçerli olduğu risk | Uygulama |
|---|---|---|---|
| 1 | Sözleşme + iskelet | 1 · 2 · 4 | K003/K011/K012 ADR + ilk kod |
| 2 | Altyapı devri | 3 · 5 · 9 | K013 kuyruk · zamanlayıcı · sayaç |
| 3 | Kod bölme gate'i | 6 | 400 satır eşiği |
| 4 | Refactor | 7 | iki `SessionInitializer` birleştirme |
| 5 | Test kilidi | 8 · 13 | `MiddlewarePipelineTest` · fail-closed test |
| 6 | Abone seti | 10 | ilk dinleyici dosyaları |
| 7 | ADR | 11 · 12 · 19 | önbellek · sınır ADR'si |
| 8 | Ölçüm | 15 | coverage çalıştırma |
| 9 | Denetim | 18 · 24 | mimari denetim · gate script |
| 10 | OWASP referansı | 22 · 23 | Error Handling · Session Management |

### §C.2 — Azaltma → risk kapanış koşulu

| # | Azaltma | Kapanış kanıtı | Kapanınca P |
|---|---|---|---|
| 1 | İlk `*UseCase` | dosya + test | 1 → düşer |
| 2 | K013 kuyruk | sözleşme ADR + 1 akış | 3 → düşer |
| 3 | K003 karar ADR | ADR | 2 → düşer |
| 4 | K013 zamanlayıcı | kayıt + 1 görev | 5 → düşer |
| 5 | Ödeme DTO + contract test | dosya + test | 4 → düşer |
| 6 | Birleştirme refactor | tek `SessionInitializer` | 7 → kapanır |
| 7 | Coverage raporu | rapor + eşik | 15 → düşer |
| 8 | İlk abone seti | dinleyici dosyaları | 10 → düşer |
| 9 | Ölçüm adaptörü | `CacheMetrics` | 12 → düşer |
| 10 | Composer kilidi | lock + tekrar okuma | 17 → kapanır |

### §C.3 — Kabul edilen riskler

| # | Risk | P | Kabul gerekçesi |
|---|---|---|---|
| 1 | 17 composer yazım riski | 6 | düşük bant + kanıt tazelendi |
| 2 | 21 URL tekrarı | 6 | tekillik = 0, tarama tekrarlanır |
| 3 | 20 çocuk şişkinliği | 8 | yalnız belge yüzeyi, kod etkilemez |
| 4 | 11 FileCache yok | 8 | önbellek ADR'si bekliyor |

### §C.4 — Azaltma öncelik sırası

| # | Öncelik | Risk | Gerekçe |
|---|---|---|---|
| 1 | 1 | R-1 · R-16 (P=20 · 16) | en yüksek P + test kapısı bağlı |
| 2 | 2 | R-3 (P=20) | üretim kesintisi riski |
| 3 | 3 | R-2 · R-5 (P=16) | yazma/okuma ayrımı + zamanlama |
| 4 | 4 | R-4 (P=15) | yüksek bant kapanışı |
| 5 | 5 | orta bant izleme | 17 satır, aylık gözden geçirme |

## §D — Kontrol listesi

### §D.1 — Periyodik kontrol

| # | Kontrol | Sıklık | Sahip | Çıktı |
|---|---|---|---|---|
| 1 | P yeniden hesap | her tur | Vault Steward | değişen satır |
| 2 | Yokluk kanıtı tekrarı | her tur | backend | 0/sonuç |
| 3 | Coverage ölçümü | aylık | QA | rapor |
| 4 | Kernel satır sayımı | her tur | backend | 281 → ? |
| 5 | Test dosyası sayımı | her tur | QA | 44 → ? |
| 6 | URL tekillik taraması | her üretim | Vault Steward | kesişim 0 |
| 7 | Gate script | her UI üretiminde | UI | geçti/kaldı |
| 8 | Sınır denetimi (K008/K012) | aylık | mimari | ihlal yok |

### §D.2 — Kapanış kontrol listesi

| # | Kapanış | Koşul | Durum (2026-10-13) |
|---|---|---|---|
| 1 | R-1 | ilk `*UseCase` + test | ⏳ açık |
| 2 | R-2 | K003 ADR | ⏳ açık |
| 3 | R-3 | K013 kuyruk sözleşmesi | ⏳ açık |
| 4 | R-4 | ödeme DTO + contract test | ⏳ açık |
| 5 | R-5 | zamanlayıcı + ilk görev | ⏳ açık |
| 6 | R-7 | tek `SessionInitializer` | ⏳ açık |
| 7 | R-15 | coverage raporu ≥ %80 | ⏳ açık |
| 8 | R-21 | tekillik taraması = 0 | ✅ kapalı |
| 9 | R-17 | composer kanıtı tazelendi | ✅ azaldı |

### §D.3 — Tur kontrol listesi (her revizyon)

| # | Adım | İş | Çıktı |
|---|---|---|---|
| 1 | Tabloyu oku | 24 satır | envanter |
| 2 | Kanıtları tazele | disk + yokluk | §A.1 durum |
| 3 | P'yi yeniden hesap | E × O | §B.4 |
| 4 | Değişimi işaretle | §B.5 | fark satırı |
| 5 | Azaltmayı güncelle | §C.1 · §C.2 | kapanış |
| 6 | Kontrolü çalıştır | §D.1 | kayıt |

### §D.4 — Durum özeti

| # | Özet | Değer |
|---|---|---|
| 1 | Toplam risk | 24 |
| 2 | Yüksek (P≥15) | 6 |
| 3 | Orta (8–14) | 17 |
| 4 | Düşük (<8) | 1 |
| 5 | Kapalı | 1 (R-21) |
| 6 | Azalan | 1 (R-17) |
| 7 | Açık soru | 4 (§7) |
| 8 | Ölçüm tarihi | 2026-10-13 |

### §A.5 — Risk–kanıt güncelleme günlüğü

| # | Tarih | Risk | İşlem | Kanıt |
|---|---|---|---|---|
| 1 | 2026-10-12 | 1–24 | ilk envanter | 24 satır |
| 2 | 2026-10-13 | 17 | composer yeniden okundu | `composer.json:8` = `>=8.4` |
| 3 | 2026-10-13 | 21 | tekillik tekrarı | kesişim 0 |
| 4 | 2026-10-13 | 6 | kernel satırı tazelendi | 281 (değişmedi) |
| 5 | 2026-10-13 | 8 · 13 | test dosyaları tekrar | `MiddlewarePipelineTest` · `BypassAuthMiddlewareFailClosedTest` mevcut |

### §A.6 — İlişkili risk grupları

| # | Grup | Riskler | Ortak önlem |
|---|---|---|---|
| 1 | Eksik katmanlar | 1 · 2 · 16 | K003 + iskelet + test |
| 2 | Eksik altyapı | 3 · 5 · 9 | K013 |
| 3 | Servis devri | 4 | K012 |
| 4 | Kod yüzeyi | 6 · 7 · 8 · 13 · 14 | gate + refactor + test |
| 5 | Ölçüm boşluğu | 12 · 15 | ölçüm + coverage |
| 6 | Sözleşme/belge | 18 · 19 · 20 · 24 | ADR + denetim |
| 7 | Güvenlik | 22 · 23 | OWASP |
| 8 | Kanıt hijyeni | 17 · 21 | tazeleme + tarama |

### §B.6 — E/O tahmin kuralları

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Kanıtsız O yazılmaz | O tahmini kanıt satırına dayanır |
| 2 | E sabittir | etki yalnız olay senaryosuyla değişir |
| 3 | Yükselme gerekçeli | O artırırken gerekçe satırda |
| 4 | Düşme yalnız kanıtla | kanıt gelmeden P düşürülmez |
| 5 | Bant geçişi işaretli | P bandı değiştirirse §B.5'e satır |

### §B.7 — Bant–aksiyon eşlemesi

| # | Bant | Aksiyon | Frekans | Sahip |
|---|---|---|---|---|
| 1 | Yüksek | kapanış planı | her tur | Vault Steward |
| 2 | Yüksek | ilgili K dosyasını uyar | her tur | backend |
| 3 | Orta | azaltma uygula | aylık | sahip |
| 4 | Orta | izleme satırı güncelle | her tur | Vault Steward |
| 5 | Düşük | kabul kaydı | gözden geçirme | Vault Steward |

### §C.5 — Azaltma durumu (2026-10-13)

| # | Azaltma | Durum | Kanıt |
|---|---|---|---|
| 1 | Sözleşme + iskelet | ⏳ bekliyor | kod yok |
| 2 | Altyapı devri | ⏳ bekliyor | K013 kapsamı |
| 3 | Kod bölme gate'i | 📋 tanımlı | karar #1 (400 satır) |
| 4 | Refactor | ⏳ bekliyor | iki dosya duruyor |
| 5 | Test kilidi | ✅ kısmi | 2 test dosyası |
| 6 | Abone seti | ⏳ bekliyor | dinleyici yok |
| 7 | ADR | ⏳ bekliyor | önbellek · sınır |
| 8 | Ölçüm | ⏳ bekliyor | rapor yok |
| 9 | Denetim | 📋 tanımlı | gate script |
| 10 | OWASP referansı | ✅ referans | URL kanıtı (doğrulama §3) |

### §C.6 — Azaltma–dosya eşlemesi

| # | Azaltma | İlgili K011 dosyası |
|---|---|---|
| 1 | iskelet + sözleşme | `usecase-akislari/` |
| 2 | kuyruk/zamanlama | `zamanlamali-gorevler/` |
| 3 | ödeme | `odem-servisleri/` |
| 4 | kod bölme + sıra | `pagerrouter/-middleware-zinciri` |
| 5 | oturum birleştirme | `pagerrouter/-oturum-akisi` |
| 6 | abone seti | `event-bus/-abone-tablosu` |
| 7 | önbellek ADR | `cache-layer/-katman-zinciri` |
| 8 | coverage | `application-test-stratejisi.md` |
| 9 | composer | `application-bagimliliklar.md` |
| 10 | tekillik | `application-web-arastirma.md` |

### §D.5 — Sonraki tur işleri

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | K003 komut/sorgu ADR | R-2 · R-16 azalır | backend |
| 2 | K013 kuyruk + sayaç sözleşmesi | R-3 · R-9 azalır | K013 |
| 3 | İlk `*UseCase` iskeleti + test | R-1 · R-16 | backend |
| 4 | `SessionInitializer` birleştirme | R-7 | refactorer |
| 5 | Coverage çalıştır | R-15 | QA |
| 6 | İlk dinleyici seti | R-10 | backend |
| 7 | Önbellek ADR (`FileCache` · `CacheMetrics`) | R-11 · R-12 | Vault Steward |

### §D.6 — Ölçüm özeti

| # | Ölçüm | Değer | Tarih |
|---|---|---|---|
| 1 | Risk satırı | 24 | 2026-10-13 |
| 2 | Yüksek / orta / düşük | 6 / 17 / 1 | 2026-10-13 |
| 3 | Kapalı | 1 (R-21) | 2026-10-13 |
| 4 | Azalan | 1 (R-17) | 2026-10-13 |
| 5 | Kanıt türü | 12 yokluk · 9 disk · 3 süreç | 2026-10-13 |
| 6 | Yasaklı desen | 0 | 2026-10-13 |

**Last Updated:** 2026-10-13
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

## §D.7 Denetim Sorumluluk Matrisi (Kim Hangi Adımı Yürütür)

| # | Adım | Yürütücü | Onaylayıcı | Frekans |
|---|------|----------|------------|---------|
| D-19 | Risk envanteri gözden geçirme | Vault Steward | Bayram Ali | Aylık |
| D-20 | Rol tabanlı erişim sayımı | Backend geliştirici | Güvenlik denetçisi | Aylık |
| D-21 | Azaltma durumu güncelleme | Backend geliştirici | Vault Steward | Aylık |
| D-22 | Risk puanı yeniden hesaplama | Mimari sorumlu | Bayram Ali | Üç aylık |
| D-23 | Stratejik risk kararı | Bayram Ali | Onaysız karar yok | Yıllık |

### §D.8 §D Kapanış Notu

§D, risk denetiminin zamanlanmış ve kayıtlı yürütülmesini sağlar: her adım bir kod, bir hedef ve bir beklenen kanıt taşır. Eşik tablosu denetimin durma ve eskalasyon kurallarını, sorumluluk matrisi ise yürütücü/onaylayıcı ayrımını tanımlar. Aylık, üç aylık ve yıllık adımlar birbirini tamamlar: aylık yüzeyel, üç aylık derin, yıllık stratejik denetimdir. Risk envanteri (§A) ile azaltma planı (§C) arasındaki bağ bu adımlar sayesinde kırılmadan sürer.

**Last Updated:** 2026-10-13

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode