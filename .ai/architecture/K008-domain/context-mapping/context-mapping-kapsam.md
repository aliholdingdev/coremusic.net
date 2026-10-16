---
node_id: "K008-context-mapping-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master"
evidence:
  - "shared/config/domain.php satır 1–19 — 7 subdomain haritası + portlar"
  - "shared/src/Config/DomainConfig.php satır 1–132 — final class (çözümleyici)"
  - "shared/config/routes.php satır 10–16 — 'Hardcoded domain KULLANILMAZ' + kurulum"
  - "domain-master.md §3.1 satır 96 (ContextMap adayı) · §3.4 satır 127 (event YOK)"
validation: "2026-10-15 — domain.php tam (19 satır) + DomainConfig tam (132 satır, iki parça okuma) + routes.php 1–30 + grep 27 isabet"
open_questions:
  - "ContextMap aggregate → ADR-097 K4 kapsamında kilitlenmedi → tasarım adayı"
  - "DomainConfig fallback listesi (5 subdomain) domain.php (7) ile uyuşmuyor → DRIFT raporlandı, kod değişikliği yetkisi bu görevde yok"
group: mimari
---

# context-mapping — Kapsam (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> `context-mapping`, gelen isteğin **bağlamını** (scheme/host/port) domain
> konfigürasyonuna eşler: harita `domain.php`'dedir, çözümleyici
> `DomainConfig`'tir. Stratejik katmandaki `ContextMap` (ilişki kaydı) adayı
> **diskte yoktur** — grep 0 isabet; master §3.1 satır 96'da tasarım adayı
> olarak bekler.

## §1 ROL (ELI15)

Bir istek geldiğinde sunucunun ilk sorusu "bu hangi domain, http mi https mi,
hangi portta?"dur. `shared/config/domain.php` bu sorunun **cevap
tablosunu** tutar: 7 subdomain (`auth`, `home`, `assets`, `music`, `admin`,
`media`, `api`) → tam host adları (satır 7–15), `home` için ayrı port 81
(satır 16–18). `DomainConfig` sınıfı bu tabloyu okur, `setOverrides()` ile
çalışma anındaki scheme/host/port'u üstüne bindirir ve `getUrl('music')`
gibi çağrılarla **bilinmeyen subdomain'i asla exception'a çevirmez** —
primary URL'e düşer (fail-safe, satır 89–96). URL üreteçleri
(`AuthRouteConfig::getAuthUrl/getHomeUrl`) ve `routes.php` bu sınıfa
bağımlıdır; hardcoded domain yazmak dosyanın kendisinde yasaktır
(routes.php satır 11). Stratejik "hangi bağlam hangi bağlamla ilişkili"
kaydı (`ContextMap`) ise henüz **tasarım adayıdır** — diskte sınıf, şema ve
event yoktur.

## §2 KAPSAM SINIRI

| # | Bu bağlamın İÇİNDE | Bu bağlamın DIŞINDA | Sınır komşusu |
|---|---|---|---|
| 1 | subdomain → host eşlemesi (`getSubdomainHost`/`getUrl`) | Route matching, sayfa yönlendirme | `PageRouter` (tüketicidir) |
| 2 | Domain haritası verisi (7 subdomain + portlar) | SpaRoute liste tanımı (routes.php'nin geri kalanı) | `PageRouter` |
| 3 | Runtime bağlam override (`setOverrides`) + scheme çözümü | Cihaz bağlamı (`X-Device-Type` başlığı) | `device-management` |
| 4 | Fail-safe davranış (bilinmeyen subdomain → primary URL) | Auth session bağlamı (kullanıcı kimliği) | auth / `user` |
| 5 | Stratejik context ilişkileri (**ContextMap adayı — diskte YOK**) | K006/K020 sahiplik kararı | `domain-master.md` §6 C2 (orkestratör) |

## §3 DDD MODEL NOTU (taslak — ADR-097 K4)

| # | Kalem | Ad | Kanıt |
|---|---|---|---|
| 1 | Bounded Context (aday) | Bağlam Eşleme (`context-mapping`) | ✅ domain-master §3.1 satır 96 |
| 2 | Aggregate (aday) | ContextMap (ilişki kaydı) | ⚠️ TASARIM İDDİSİ — ADR-097 K4 |
| 3 | Entity / kayıt | — (diskte ContextMap sınıfı ve şeması **YOK**) | ✅ grep `.ai/.sql/mysql` + `shared/src` = 0 isabet (2026-10-15) |
| 4 | Value Object (aday) | SubdomainName (7 değer) · Scheme · Port | ✅ domain.php satır 7–18 |
| 5 | Ubiquitous language | bağlam = scheme+host+port üçlüsü · harita = `domain.php` · eşleme = `DomainConfig::getUrl` | ✅ kod |
| 6 | Domain event | — | ❌ YOK — master §3.4 satır 127 (`⚠️ VERIFICATION REQUIRED`) |
| 7 | Invariant | bilinmeyen subdomain → primary URL (exception YOK) | ✅ KOD KANITLI — DomainConfig satır 91–93 |
| 8 | Invariant | hardcoded domain yazımı yasak | ✅ KOD KANITLI — routes.php satır 11 |

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `shared/config/domain.php` | 1–19 | 7 subdomain haritası, primary/primary_port/subdomain_port, `home => 81` |
| `shared/src/Config/DomainConfig.php` | 1–132 | final class — constructor fallback (28–45), setOverrides (47–52), getUrl (89–96), buildUrl (124–131) |
| `shared/config/routes.php` | 10–16 | "Tüm URL'ler DomainConfig'den dinamik üretilir" + kurulum |
| `shared/src/Config/AuthRouteConfig.php` | 33 · 39 · 52 | `getAuthUrl`/`getHomeUrl` — DomainConfig tüketicisi (grep) |
| `shared/src/PageRouter/{PageRouter,PageRouterKernel,HtmlShellRenderer,AuthUrlBuilder}.php` | 6–41 | 4 constructor parametresi (grep) |
| `.ai/architecture/K008-domain/domain-master.md` | 96 · 127 | ContextMap adayı · event boşluğu |

**Kardeş dosyalar:** [[context-mapping-dogrulama]] · [[context-mapping-web-arastirma]] · [[context-mapping-ornekler]] · [[context-mapping-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
