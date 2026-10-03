---
title: "CoreMusic — auth.coremusic.net İş Akışı"
type: docs
category: domain
docType: workflow
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# auth.coremusic.net — WORKFLOW.md

**docType:** workflow · **Klasör:** `auth.coremusic.net/` · **Sorumlu:** MO (workflow)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[CONTEXT.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `auth.coremusic.net/` üzerinde bir değişikliğin **adım adım akışını** (adım → çıktı → neden → atlarsan ne olur) ve **kapılarını** tanımlar. Auth, tüm subdomainlerin tek kimlik kapısı olduğu için hata güvenlik olayıdır; kapılar (keşif → ADR → uygulama → test → güvenlik onayı → rapor) zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Test kapısı phpunit ^10.5; `composer.json` `scripts` bloğu **YOK** | `auth.coremusic.net/composer.json` doğrudan okundu |
| Test yapılandırması `phpunit.xml` | dosya diskte var |
| Mevcut test kapsamı `tests/Unit/Domain/*` (5 test) | `tests/` dosya listesi |
| Güvenlik kararları ADR'de: ADR-010 (CSRF), ADR-011 (session), ADR-013 (rate limit), ADR-043 (konsolidasyon) | `.ai/.decisions/accepted/ADR-043|ADR-011|ADR-010` VAR |
| Yeni PHP dosyası `php-template.md`'den türetilir | [[../.ai/.templates/backend/php-template]] (Guardrail #16) |
| Commit subagent'a ait değil | Kök [[../AGENTS.md]] §7/§8 — "commit (subagent ATMAYACAK)" |

> VERIFICATION REQUIRED: `auth.coremusic.net/composer.json` içinde `scripts` bloğu yoktur (doğrudan okundu) — test komutu phpunit üzerinden (`phpunit.xml`) tanımlıdır. `.ai/.rules/error-recovery.md` **diskte YOK** — hata kurtarma adımı kök `AGENTS.md` §7 metnine dayanır. `auth/CLAUDE.md` §3'teki dosya adları (`AuthPost.php`, `Pipeline.php` vb.) diskteki `*Handler.php` / `*Middleware.php` adlarıyla uyuşmaz (CONTEXT §3.5) — bu akışta **disk adları** kullanılır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `auth.coremusic.net/` kod/config/route/sayfa/test değişiklik akışı | `vendor/` içeriği (composer yönetir) |
| Kapılar: keşif → ADR → uygulama → phpunit → güvenlik onayı → rapor | Deploy/CI → DevOps |
| Güvenlik yüzeyi onayı (CORS, middleware sırası, hash, rate limit) | DB şema içeriği → Data Engineer (`.ai/.sql/mysql/coremusic_auth.sql`) |
| Commit kuralı (subagent atmaz) | Vault `.ai/` içi doküman akışı |

- **Kullananlar:** Backend Architect (birincil), Security Engineer (güvenlik yüzeyi), QA Engineer (`tests/`), MO (kapı + rapor).
- **Ön koşul:** [[CONTEXT.md]] (envanter) + [[CLAUDE.md]] (kural) okunmuş; hedef dosya diskte mevcut.
- **Gizli veri:** `config/.env` içeriği ve session/cookie değerleri okunmaz, log'a/vault'a yazılmaz.

---

## 3. Mimari

### 3.1 Adım Tablosu (adım → çıktı → neden → atlarsan ne olur)

| # | Adım | Çıktı | Neden | Atlarsan ne olur | eli10 | eli15 |
|---|------|-------|-------|------------------|-------|-------|
| 1 | Görev tanımını netleştir (prompt-maker / soru kapısı) | Onaylı görev özeti | Yanlış iş yapmayı önler | İstenmeyen değişiklik, revert | Önce ne istendiğini anlamak; yanlış anlarsan tüm iş boşa gider. | Kapı (kök `AGENTS.md` §1) onaysız başlamaz. Tanım okunmadan başlarsan beklenti ile kod ayrışır. Çıktı, ölçülebilir özet olur. Atlarsan yanlış iş üretimi ve zaman kaybı. |
| 2 | Klasör kurallarını oku: [[CLAUDE.md]] + [[AGENTS.md]] + [[CONTEXT.md]] | Kural + envanter + §3.5 çelişki listesi | Klasör-specific yasaklar orada | Yasak ihlali (`.env`, superglobal, hash) | Bu klasörün özel kurallarını öğrenmek. | Kurallar klasör dosyalarında tutulur çünkü genel kural her detayı taşıyamaz. Çelişki listesi okununca eski dosya adlarıyla arama yapmazsın. Envanter, katman sınırını baştan gösterir. Atlarsan güvenlik ve katman hataları. |
| 3 | İlgili ADR'yi oku (karar yüzeyi ise): `.ai/.decisions/accepted/ADR-*` | Karar metni | Sıra/kurallar ADR ile sabit | ADR ihlali, güvenlik açığı | Kararın neden alındığını öğrenmek. | CSRF (ADR-010), oturum (ADR-011), hız sınırı (ADR-013) ve konsolidasyon (ADR-043) diskte VAR. Karar okunmadan değişim, mevcut güvenliği çiğner. ADR metni kopyalanmaz, yalnız okunur. Atlarsan güvenlik kararı yanlış değişir. |
| 4 | Hedef dosyayı 1. kez oku + disk kanıtı topla | Okunmuş dosya, envanter | Anti-overthink: ilk okumadan KARAR VER | Aynı dosya 2. kez okunur, token israfı | Dosyayı bir kez okuyup karar vermek. | Anti-waste kuralı (kök `AGENTS.md` §5) ikinci okumayı yasaklar. İlk okuma yeterli kanıtı verir; ikinci okuma "emin olmak" bahanesidir. Karar ilk okumadan verilir. Atlarsan tekrarlı okuma ve gecikme. |
| 5 | Değişikliği yap — yalnız hedef dizin: `include/` · `pages/` · `routes/` · `config/` · `tests/` | Kod diff'i | Hexagonal katman sınırı | Katman ihlali, kapsam genişlemesi | İstenen yeri değiştirmek, başka yere dokunmamak. | Mantık `Service/`, veri `Repository/`, kural `Domain/`, görünüm `pages/`, kapı `Middleware/` içindedir (CONTEXT §3.2). Kapsam genişlerse başka oturumun çalışması bozulur. Kilitli diff revert'i kolaylaştırır. Atlarsan katman ihlali + revert. |
| 6 | Yeni PHP dosyası → `php-template.md`'den türetilir | Şablondan üretilmiş dosya | Guardrail #16 — şablonsuz dosya üretilmez | Standartsız dosya, vault reddi | Yeni dosyayı hazır kalıba göre açmak. | Şablon zorunluluğu kök `AGENTS.md` §4 + Guardrail #16'dır. Şablon okunmadan üretilen dosya standart dışı kalır. Şablon, kural ve yapıyı hazır getirir. Atlarsan reddedilen dosya + ikinci iş. |
| 7 | Yeni middleware ise: `MiddlewareInterface` + `MiddlewarePipeline` kaydı | Kayıtlı kapı | Kapı sistemi tek yerde | Kapı atlanır, kontrol çalışmaz | Kapıyı listeye eklemek. | auth `AGENTS.md` §4 Zorunlu 5 bu adımı şart koşar. Kayıt yoksa middleware çağrılmaz ve güvenlik kontrolü sessizce devre dışı kalır. Değişiklik ADR ile birlikte düşünülür. Atlarsan sessiz güvenlik boşluğu. |
| 8 | Test ekle/koru: `tests/Unit/` hiyerarşisi + `phpunit.xml` | Yeni/güncel test dosyası | Regresyon kapısı | Sessiz kırılma, production'da arıza | Kodun doğru kaldığını kanıtlayan soru kâğıdı. | Mevcut desen `tests/Unit/Domain/{DTO,Entity,ValueObject}` (5 test — disk). Yeni sınıf = yeni test; `Unit/{Service,Handler,Repository}` klasörleri henüz YOK (CONTEXT §3.4). Eklenmezse gelecekteki kırık yakalanmaz. |
| 9 | Doğrulama: phpunit (`phpunit.xml` üzerinden) | Yeşil test | Regresyon kapısı | Hatalı değişiklik git'e girer | Sınavı çalıştırıp geçmek. | `composer.json` `scripts` yok (doğrudan okundu) → koşu `phpunit.xml` üzerindendir. Kapıdan geçmeyen iş bitmiş sayılmaz. Kırmızıda kök `AGENTS.md` §7: kurala dön → yeniden yaz → tekrar kontrol. |
| 10 | Güvenlik yüzeyi değiştiyse Security onayı (CORS/header/rate limit/hash/sıra) | Onay kaydı | A1 sınırı + ADR | Yetkisiz güvenlik değişikliği | Güvenlik işini güvenlikçiye bırakmak. | auth `AGENTS.md` §3 Security Engineer sorumluluğu; kök `AGENTS.md` §6 "auth, session, rate limit → Security Engineer". Onaysız değişim denetimden kaçar. Redde handover/ADR'ye gider. Atlarsan güvenlik açığı + log ERROR. |
| 11 | UI etkisi varsa browser testi (gerçek sayfa) | Ekran doğrulaması | Yerleşim kırığını yakala | Bozuk giriş formu kullanıcıya ulaşır | Değişiklikten sonra sayfayı tarayıcıda kontrol etmek. | Kök `AGENTS.md` §7 #7 zorunlu kılar; form/yerleşim iddiası kod ile kanıtlanamaz. Mockup (`auth` ekranları) ile karşılaştırma burada yapılır. Atlarsan gizli yerleşim hatası. |
| 12 | Rapor yaz; **commit ATMAZ** | Rapor | Yetki sınırı | Yetkisiz tarih, revert riski | İş bitince haber vermek; commit'i başkasının atması. | Subagent commit atmaz (kök `AGENTS.md` §7/§8); commit yetkisi orkestratördedir. Tarih tek elden yazılır. Rapor, kapıyı orkestratöre teslim eder. Atlarsan iş görünmez kalır ya da düzensiz commit oluşur. |

**eli10 / eli15 blokları (§3.1'deki 12 adımın karşılığı):**

**Adım 1 — Görev tanımı**
> **eli10 (basit):** Önce ne istendiğini anlamak.
> **eli15 (detay):** Kapı onaysız başlamaz. Tanım okunmadan kod yazılırsa beklenti ile sistem ayrışır. Çıktı ölçülebilir özettir. Atlarsan yanlış iş üretirsin.

**Adım 2 — Klasör kuralları**
> **eli10 (basit):** Bu klasörün kendi kurallarını ve bilinen farklarını öğrenmek.
> **eli15 (detay):** Kurallar ayrı dosyadır; genel kural detay taşımaz. Çelişki listesi (CONTEXT §3.5) okununca eski adlarla arama yapılmaz. `.env` yasağı burada bellidir. Atlarsan güvenlik ve katman hatası.

**Adım 3 — ADR okuma**
> **eli10 (basit):** Karar yüzeyini değiştirmeden önce kararın kendisini okumak.
> **eli15 (detay):** CSRF, oturum, hız sınırı ve konsolidasyon kararları diskte. ADR metni kopyalanmaz, yalnız okunur. Karar okunmadan değişim mevcut güvenliği çiğner. Atlarsan güvenlik kararı yanlış değişir.

**Adım 4 — 1. kez okuma**
> **eli10 (basit):** Dosyayı bir kez okuyup karar vermek.
> **eli15 (detay):** Anti-waste kuralı ikinci okumayı yasaklar. İlk okuma kanıtı verir. Karar ilk okumadan verilince akış hızlanır. Atlarsan token israfı ve gecikme.

**Adım 5 — Uygulama**
> **eli10 (basit):** Yalnız izinli dizinlerde, doğru katmanda değişiklik.
> **eli15 (detay):** Hexagonal sınır güvenlik denetimini mümkün kılar. Mantık görünümün içine girmez. Kapsam genişlerse çakışma doğar. Atlarsan katman ihlali + revert.

**Adım 6 — Şablon**
> **eli10 (basit):** Yeni dosyayı hazır kalıba göre açmak.
> **eli15 (detay):** Guardrail #16 şablonsuz dosyayı reddeder. Şablon standartları hazır getirir. Okunmadan üretilen dosya revizyonda elenir. Atlarsan standartsız dosya + ikinci iş.

**Adım 7 — Middleware kaydı**
> **eli10 (basit):** Yeni kapıyı listeye eklemek.
> **eli15 (detay):** Kayıt yoksa kapı çağrılmaz ve kontrol sessizce devre dışı kalır. `MiddlewareInterface` zorunludur. Değişiklik ADR ile düşünülür. Atlarsan sessiz güvenlik boşluğu.

**Adım 8 — Test**
> **eli10 (basit):** Kodun doğru kaldığını kanıtlayan sorular yazmak.
> **eli15 (detay):** Mevcut desen `tests/Unit/Domain/*` (5 test). Yeni sınıf = yeni test. Eksik klasörler (`Unit/Service` vb.) yok olarak bilinir, uydurulmaz. Atlarsan sessiz kırılma.

**Adım 9 — phpunit**
> **eli10 (basit):** Sınavı çalıştırıp geçmek.
> **eli15 (detay):** `composer.json` `scripts` bloğu yok; koşu `phpunit.xml` üzerindendir. Kapıdan geçmeyen iş bitmez. Kırmızıda kök `AGENTS.md` §7 işletilir. Atlarsan kırık değişiklik git'e girer.

**Adım 10 — Güvenlik onayı**
> **eli10 (basit):** Güvenlik kapısına dokunmadan güvenlikçinin onayını almak.
> **eli15 (detay):** CORS, başlık, hız sınırı, hash ve kapı sırası A1 yüzeyidir. Onaysız değişim denetimden kaçar. Redde handover veya ADR'ye gidilir. Atlarsan güvenlik açığı + log ERROR.

**Adım 11 — Browser testi**
> **eli10 (basit):** Değişikliği gerçek sayfada görmek.
> **eli15 (detay):** Form ve yerleşim iddiası kod ile kanıtlanamaz. Mockup ile karşılaştırma yapılır. Kök `AGENTS.md` §7 #7 zorunludur. Atlarsan gizli yerleşim hatası.

**Adım 12 — Rapor**
> **eli10 (basit):** İş bitince haber vermek; commit'i başkasına bırakmak.
> **eli15 (detay):** Commit yetkisi orkestratördedir; tarih tek elden yazılır. Rapor kapıyı teslim eder. Dağınık commit iz sürmeyi bozar. Atlarsan iş görünmez kalır.

### 3.2 Kapılar (Gate)

```text
[K1 KEŞİF: kural + envanter + ADR] ─onay─▶ [K2 UYGULAMA: include|pages|routes|config|tests]
    ─ yeni kapı: MiddlewarePipeline kaydı ─▶ [K3 TEST: phpunit yeşil]
    ─ güvenlik yüzeyi ise K4 SECURITY ONAYI ─▶ (UI ise K5 BROWSER)
    ─▶ [K6 RAPOR → ORKESTRATÖR → COMMIT (MO)]
```

| Kapı | Koşul | Red durumunda |
|------|-------|---------------|
| K1 Keşif | CLAUDE + AGENTS + CONTEXT okundu; karar yüzeyi ise ADR okundu; 1. okumadan karar | Görev uygulamaya alınmaz |
| K2 Uygulama | Diff yalnız `include/`, `pages/`, `routes/`, `config/`, `tests/`; yeni dosya `php-template.md`'den; middleware ise pipeline kaydı | Kapsam daraltılır / dosya şablondan yeniden üretilir / kayıt eklenir |
| K3 Test | phpunit (`phpunit.xml`) yeşil | Kök `AGENTS.md` §7 → kurala dön → yeniden yaz → tekrar kontrol (3 başarısız → DUR + 1 soru) |
| K4 Security | CORS/header/rate limit/hash/sıra değişikliği Security onaylı | Handover / ADR'ye gider (kök `AGENTS.md` §9.3) |
| K5 Browser | Gerçek sayfa + mockup karşılaştırması geçti | Düzelt, tekrar test |
| K6 Rapor | Rapor tam; **commit ORKESTRATÖRDE** | Subagent commit atmaz |

**eli10 / eli15 blokları (§3.2 — 6 kapı):**

**K1 Keşif**
> **eli10 (basit):** Kural, envanter ve karar okunmadan başlamamak.
> **eli15 (detay):** Kapı, yanlış dosyayı ve kararı çiğnemeyi engeller. Mockup/ADR gerekliliği burada belli olur. Redde görev başlamaz. Eksik okuma güvenlik hatası doğurur.

**K2 Uygulama**
> **eli10 (basit):** Yalnız izinli dizinlerde, doğru katmanda değişiklik.
> **eli15 (detay):** Kapsam disiplini çakışmayı önler. Yeni dosya şablondan türetilir. Yeni kapı kayda girer. Redde genişletilmiş kapsam geri alınır.

**K3 Test**
> **eli10 (basit):** Sınav yeşil olmadan iş bitmez.
> **eli15 (detay):** Kapı regresyonu yakalar. Kırmızıda kurala dönülür, kör deneme yapılmaz. 3 başarısız düzeltmede durulur ve tek soru sorulur. Atlarsan hata git geçmişine girer.

**K4 Security**
> **eli10 (basit):** Güvenlik kapısına dokununca güvenlikçiden onay.
> **eli15 (detay):** A1 yüzeyi ayrı yetkidir. Onaysız değişiklik denetimden kaçar. Redde handover veya ADR açılır. Atlarsan güvenlik açığı doğar.

**K5 Browser**
> **eli10 (basit):** Değişikliğin gerçek ekranda doğru olduğunu görmek.
> **eli15 (detay):** Form/yerleşim kod ile kanıtlanamaz. Mockup karşılaştırması zorunludur. Redde düzeltip tekrar test edilir. Atlarsan kullanıcıya bozuk form gider.

**K6 Rapor**
> **eli10 (basit):** İşi haber vermek; commit'i orkestratöre bırakmak.
> **eli15 (detay):** Yetki sınırı burada kapanır. Tarih tek elden yazılır. Rapor olmadan iş görünmezdir. Subagent commit atmaz.

### 3.3 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük, güvenli | Her şey değişir, inceleme kör olur |
| 2 | **Tek sorumluluk** | Adımın tek sahibi | Sahipsiz adım |
| 3 | **Token tek kaynak** | Ortak değer tek yerde | Tutarsızlık, tarama |
| 4 | **Cihaz izolasyonu** | Cihaz farkı ayrı | Yayılır, drift |
| 5 | **Vendor karantinası** | `vendor/` ayrı | Güncelleme kodu bozar |

> **eli10 (basit):** Adımlar ve kapılar ayrı kutularda; bir kutu değişince diğeri bozulmasın.
> **eli15 (detay):** Süreç ayrı dosyadır çünkü kapılar (ADR, güvenlik onayı, test, rapor) koddan farklı hızda değişir; kod içine gömülse kapı unutulur. Okunması, işe sırayla başlamayı sağlar. Yazması, tekrarlanabilir ve denetlenebilir akış bırakır. Bölünmezse "hangi kapı kaldı" sorusu cevapsız kalır — güvenlik onayı kaybolur.

### 3.4 Değişiklik Tipi → Zorunlu Adım → Kapı Eşlemesi

| # | Değişiklik tipi | Zorunlu adımlar | Geçilecek kapılar | eli10 | eli15 |
|---|-----------------|-----------------|-------------------|-------|-------|
| 1 | `pages/*.php` görünüm değişikliği | Adım 1-2 + Adım 4-5 + Adım 11 | K1, K2, K5, K6 | Giriş formunun görünümü değişince tarayıcıda görülür. | Form/yerleşim kod ile kanıtlanamaz; mockup ile karşılaştırılır. Mantık buraya taşınırsa SRP ihlali olur. Kapılar sırayla geçilir. |
| 2 | `include/Service/*` iş mantığı değişikliği | Adım 1-3 + Adım 4-5 + Adım 8-9 | K1, K2, K3, K6 | İş kuralı değişince sınavı da değişir. | Login/register/logout mantığı `Service`'tedir; görünüm veya veri katmanına yazılmaz. Yeni davranış = yeni test (`Unit/Service` henüz YOK — CONTEXT §3.4). Kapı test olmadan geçilmez. |
| 3 | `include/Domain/*` kural/VO değişikliği | Adım 1-3 + Adım 4-5 + Adım 8-9 | K1, K2, K3, K6 | Kural nesnesi değişince sınavı da değişir. | `Email`, `Password`, `Gender`, `UserId`, `User` değer nesneleri testlidir. Değişiklik ADR ile düşünülür (hash kuralı: yalnız `Password`). Kapı, davranış sapmasını yakalar. |
| 4 | `include/Middleware/*` değişikliği | Adım 1-3 (ADR) + Adım 4-5 + **Adım 7 (pipeline kaydı)** + Adım 10 | K1, YENİ KAPI kaydı, K3, **K4 Security** | Güvenlik kapısına dokunulur — onay şart. | Kapı (CORS, hız, başlık, oturum) A1 yüzeyindedir; sırası ADR ile sabittir. Kayıt yoksa kapı sessizce devre dışı kalır. Onaysız değişim revert + log ERROR üretir. |
| 5 | `config/cors.php` değişikliği | Adım 1-3 + Adım 4-5 + Adım 10 | K1, K2, **K4 Security** | İzin listesi güvenlikçinin kararındadır. | CORS izni genişlerse yetkisiz origin veri okur. Değişiklik tek başına yapılmaz; Security onayı ve gerekçe zorunludur. Redde ADR'ye gider. |
| 6 | `routes/oauth.php` + `handler/OAuthPostHandler.php` değişikliği | Adım 1-2 + Adım 4-5 + Adım 8-9 | K1, K2, K3, K6 | OAuth adresi ve POST akışı birlikte değişir. | Uç kaydı ile işleyici ayrı dosyalardadır; biri değişince diğeri güncellenmezse 404/sessiz hata doğar. Sağlayıcı listesi `shared/config/oauth-platforms.php` tarafındadır (SSOT orası). |
| 7 | Yeni PHP dosyası | Adım 1-2 + **Adım 6 (şablon)** + Adım 8-9 | K1, K2, K3 | Yeni dosya her zaman hazır kalıpla açılır. | Guardrail #16 şablonsuz dosyayı reddeder; `php-template.md` okunmadan üretim yapılmaz. Doğru katman (`Domain` mi `Service` mi) şablonla birlikte seçilir. Atlarsan reddedilen dosya + ikinci iş. |
| 8 | `tests/` değişikliği | Adım 1-2 + Adım 4 + Adım 8-9 | K1, K2, K3 | Test dosyası kendi kapısından geçer. | Test üretim koduna dokunmaz; yalnız doğrular. Hiyerarşi `Unit/{DTO,Entity,ValueObject}` korunur; yeni klasör `phpunit.xml` ile birlikte eklenir. Kapı yeşil kalır. |
| 9 | `vendor/` veya `.env` değişikliği | — (RED) | K1 reddi + Security bilgilendirme | Kurulan kutu ve gizli dosya elde değişmez. | `vendor/` yalnız composer ile yönetilir. `.env` değeri hiçbir dokümana/log'a kopyalanmaz; talep sunucu yöneticisine/Security'e götürülür. Kapı, revert yerine baştan engeller. |

**eli10 / eli15 blokları (§3.4 — 9 değişiklik tipi):**

**1 · `pages/` görünüm**
> **eli10 (basit):** Form görünümü değişince tarayıcıda görülür.
> **eli15 (detay):** Mockup karşılaştırması zorunludur. Mantık buraya taşınırsa sayfa okunmaz hâle gelir. Kapılar sırayla geçilir. Atlarsan bozuk form kullanıcıya gider.

**2 · Service (iş mantığı)**
> **eli10 (basit):** İş kuralı değişince sınavı da değişir.
> **eli15 (detay):** Mantık `Service`'tedir; görünüm/veriye yazılmaz. Yeni davranış = yeni test. Kapı test olmadan geçilmez. Atlarsan sessiz davranış değişikliği.

**3 · Domain (kural)**
> **eli10 (basit):** Değer nesnesi değişince testiyle birlikte değişir.
> **eli15 (detay):** VO'lar testlidir. Hash kuralı yalnız `Password`'dedir. Değişiklik ADR ile düşünülür. Kapı davranış sapmasını yakalar.

**4 · Middleware**
> **eli10 (basit):** Güvenlik kapısına dokunulur — onay şart.
> **eli15 (detay):** Kapı sırası ADR ile sabittir. Kayıt yoksa kapı devre dışı kalır. Onaysız değişim revert + log ERROR üretir. Kapı, güvenlik onayıdır.

**5 · CORS**
> **eli10 (basit):** İzin listesi güvenlikçinin kararındadır.
> **eli15 (detay):** Geniş izin yetkisiz origin veri okur. Tek başına yapılmaz; onay ve gerekçe zorunludur. Redde ADR'ye gidilir. Atlarsan veri sızıntısı.

**6 · OAuth**
> **eli10 (basit):** Adres ile işleyici birlikte değişir.
> **eli15 (detay):** Kayıt ve işleyici ayrı dosyalardadır; biri değişirse diğeri güncellenmezse 404 doğar. Sağlayıcı listesi shared'dedir. Kapı test ile uç da sınanır.

**7 · Yeni dosya**
> **eli10 (basit):** Yeni dosya her zaman hazır kalıpla açılır.
> **eli15 (detay):** Şablon okunmadan üretim yapılmaz. Doğru katman şablonla seçilir. Şablonsuz dosya revizyonda elenir. Atlarsan ikinci iş.

**8 · Test**
> **eli10 (basit):** Test dosyası kendi kapısından geçer.
> **eli15 (detay):** Test koduna dokunmaz, yalnız doğrular. Hiyerarşi korunur. Yeni klasör `phpunit.xml` ile eklenir. Kapı yeşil kalır.

**9 · `vendor/` · `.env`**
> **eli10 (basit):** Kutuya ve gizli dosyaya elle girilmez — talep reddedilir.
> **eli15 (detay):** `vendor/` yalnız composer yönetir. `.env` değeri hiçbir yere kopyalanmaz. Talep Security'e/sunucu yöneticisine gider. Kapı baştan engeller.

### 3.5 Sık Yapılan Hatalar

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | Dosya adlarını `CLAUDE.md` §3'teki gibi eski adlarla aramak | Disk adları: `*Handler.php` / `*Middleware.php` (CONTEXT §3.5) |
| 2 | Yeni middleware'i pipeline'a kaydetmemek | Adım 7 → `MiddlewareInterface` + `MiddlewarePipeline` |
| 3 | Mantığı `pages/` içine yazmak | Mantık `Service/`; `pages/` yalnız görünüm |
| 4 | Testi atlayıp "geçiyordur" demek | Adım 9 → `phpunit.xml` koşusu zorunlu |
| 5 | `.env`/session değerini rapora yapıştırmak | Değer asla kopyalanmaz; talep reddedilir |
| 6 | Aynı dosyayı 2. kez okumak ("emin olmak") | Adım 4 → ilk okumadan karar (kök `AGENTS.md` §5) |
| 7 | Subagent'ın commit atması | Adım 12 → rapor; commit orkestratörde |

**eli10 / eli15 (§3.5 bloğu):**

> **eli10 (basit):** En sık yapılan yedi hata ve doğrusu — hepsi yukarıdaki adımlara çıkar.
> **eli15 (detay):** Bu tablo kapıların neden var olduğunu örneklerle gösterir. İçine yalnız tekrar eden gerçek hatalar girer. Okunması, işe başlarken aynı tuzağa düşmeyi engeller. Yeni hata türü görülünce tek satır eklenir; eski satırlar silinmez. Hata 3 başarısız düzeltmeye ulaşırsa §4 #2 gereği durulur ve tek soru sorulur.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Faz 1 keşif onaysız kod yok | Yanlış dosyaya yazımı engeller (kök `AGENTS.md` §4) |
| 2 | Aynı dosya görevde 2. kez okunmaz · 3 başarısız → DUR + 1 soru | Anti-overthink (kök `AGENTS.md` §5) |
| 3 | ORM yasak — yalnız PDO prepared statement (ADR-002) | Injection yüzeyi (auth `AGENTS.md` §4) |
| 4 | Argon2id yalnız `Password` ValueObject içinde · `.env`/session log'a yazılmaz | Secret + gizlilik (auth `CLAUDE.md` §8) |
| 5 | Yeni middleware → `MiddlewareInterface` + pipeline kaydı | Kapı sessizce devre dışı kalmasın (auth `AGENTS.md` §4) |
| 6 | Güvenlik yüzeyi → Security onayı (ADR-010/011/013) | A1 sınırı (kök `AGENTS.md` §6) |
| 7 | Hata → kök `AGENTS.md` §7 kurala dön → yeniden yaz → tekrar kontrol | Kör düzeltme döngüsü durur (`.ai/.rules/error-recovery.md` diskte YOK — §1 notu) |
| 8 | UI etkisi → browser testi (gerçek sayfa) | Görsel kanıt zorunlu (kök `AGENTS.md` §7) |
| 9 | **Commit subagent ATMAZ** | Tarih/entegrasyon orkestratörde (kök `AGENTS.md` §7/§8) |

> **eli10 (basit):** Bu kurallar şifre ve kapıların sağlam kalmasını, işin onayla ve sırayla yürümesini, commit'in tek elden atılmasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile kod farklı hızda değişir; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazmaları, denetimin tekrarlanmasını garantiler. Kural değişince (ADR/kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → [K1: CLAUDE+AGENTS+CONTEXT (+ADR)] → [K2: include|pages|routes|config|tests diff]
  → (yeni kapı: pipeline kaydı · yeni dosya: php-template) → [K3: phpunit yeşil]
  → (güvenlikse [K4: SECURITY onayı])] → (UI ise [K5: BROWSER])
  → [K6: RAPOR — commit yok] → ORKESTRATÖR → COMMIT (MO)
```

Adım detayı §3.1 · kapı detayı §3.2.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: workflow` |
| 2 | Bölüm sırası | §1–§7 sabit |
| 3 | Adım tablosu | Her adımda "Çıktı" + "Neden" + "Atlarsan ne olur" dolu (12 satır) |
| 4 | Commit kuralı | §3.1 #12 + §4 #9 "ATMAZ" ifadesi mevcut |
| 5 | Kapılar | K1–K6 tablosu + zincirle uyumlu |
| 6 | Placeholder | Dosyada doldurulmamış şablon değişkeni kalmadı (arama deseni: iki parantez + harf) |
| 7 | Wiki-link | Wiki-link biçimi (çift köşeli parantez) kullanıldı; hedefler diskte var (olmayanlar §1 notunda) |
| 8 | eli10 + eli15 | §3.1 (12) + §3.2 (6) + §3.3 + §4 bloklarında etiketli blok |
| 9 | Disk kanıtı | `composer.json` `scripts` YOK + `phpunit.xml` VAR + ADR dosyaları VAR — doğrulandı |
| 10 | Dokunulmaz | Mevcut `CLAUDE.md` / `AGENTS.md` + `include/**/CLAUDE.md` değiştirilmedi |
| 11 | Emoji | Dekoratif emoji yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Uygulama kuralları (MEVCUT) |
| Klasör rolleri | [[AGENTS.md]] | Routing + sorumluluklar (MEVCUT) |
| Klasör context | [[CONTEXT.md]] | Envanter + §3.5 çelişki kaydı |
| Kök master kurallar | [[../AGENTS.md]] | §1 kapı · §4 keşif · §6 routing · §7 loop |
| ADR arşivi | `../.ai/.decisions/accepted/` | ADR-010 / ADR-011 / ADR-043 kararları |
| DB şema | `../.ai/.sql/mysql/coremusic_auth.sql` | `coremusic_auth` (Data Engineer) |
| PHP şablonu | [[../.ai/.templates/backend/php-template]] | Yeni dosya (Guardrail #16) |
| Context şablonu | [[../.ai/.templates/frontend/context-template]] | İskelet (Guardrail #16) |
| Test yapılandırması | `auth.coremusic.net/phpunit.xml` | K3 kapısı kanıtı |
| Paket tanımı | `auth.coremusic.net/composer.json` | phpunit ^10.5 · `scripts` YOK kanıtı |
| Home süreç | [[../home.coremusic.net/WORKFLOW.md]] | Köprüyü kuran tarafın akışı |
| api süreç | [[../api.coremusic.net/WORKFLOW.md]] | autoload hedefini kullanan tarafın akışı |
| shared süreç | [[../shared/WORKFLOW.md]] | Ortak katman akışı |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** workflow
