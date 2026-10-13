---
node_id: K011-application/usecase-akislari/index.md
node_type: file
status: draft
parent_id: K011-application
children:
  - usecase-akislari-kapsam.md
  - usecase-akislari-dogrulama.md
  - usecase-akislari-web-arastirma.md
  - usecase-akislari-ornekler.md
  - usecase-akislari-bilesenler.md
depends_on:
  - K008-domain
  - K009-security
  - K012-services
evidence:
  - "shared/src/PageRouter/RouteRegistry.php — resolve() akış kanıtı (83 satır toplam, ölçüm 2026-10-12)"
  - "shared/src/Middleware/MiddlewarePipeline.php — pipe/run akış kanıtı (54 satır toplam, ölçüm 2026-10-12)"
  - "https://en.wikipedia.org/wiki/Use_case — kullanım senaryosu tanımı (erişim 2026-10-12)"
validation:
  - "Satır aralığı 40–100 — (Get-Content).Count ile doğrulandı, 2026-10-12"
  - "Tüm Dosya/Satır iddiaları disk ölçümü, 2026-10-12"
open_questions:
  - "repo'da *UseCase sınıfı yok (0 adet) — akışlar PageRouter/Middleware üzerinden mi modellemeli? → ⚠️ VERIFICATION REQUIRED"
  - "kullanıcı senaryosu adlarının K008-domain ile eşlemesi → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K011-application/usecase-akislari — Kullanım Senaryosu Akışları

## §1 — ELI15

Bu çocuk, uygulamanın "kullanıcı bir şey yapar → sistem karşılık verir" zincirlerini
yazır. Gerçek repo'da `*UseCase` sınıfı **yok**; akışlar iki gerçek omurga üzerinden
modellenir: `shared/src/PageRouter/` (istek → rota → yanıt, 14 dosya) ve
`shared/src/Middleware/` (istek → boru hattı → çekirdek, 11 dosya).

## §2 — GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | index.md | Giriş · katalog · grup özeti | bu dosya |
| mimari | usecase-akislari-kapsam.md | Akış kapsamı, sınır soruları | kademe-2 |
| kanit | usecase-akislari-dogrulama.md | Doğrulama tablosu (≥10 satır) | kademe-2 |
| kanit | usecase-akislari-web-arastirma.md | Web araştırma (≥8 URL, P3 havuzu) | kademe-2 |
| kod | usecase-akislari-ornekler.md | Gerçek kod blokları (Dosya/Satır) | kademe-2 |
| kod | usecase-akislari-bilesenler.md | Akış bileşen envanteri | kademe-2 |

## §3 — Akış omurgası (disk kanıtı)

| # | Omurga | Dosya | Kanıt |
|---|---|---|---|
| 1 | Rota çözümü | `shared/src/PageRouter/RouteRegistry.php` | `resolve(string $uri): ?SpaRoute` — satır 35 |
| 2 | Boru hattı | `shared/src/Middleware/MiddlewarePipeline.php` | `pipe()` / `run()` / `buildPipeline()` — satır 17 / 23 / 29 |
| 3 | Çekirdek yürütme | `shared/src/PageRouter/PageRouterKernel.php` | istek yaşam döngüsü (324 satır toplam) |
| 4 | Kimlik kontrolü | `shared/src/PageRouter/AuthGuard.php` | korumalı rota denetimi (80 satır toplam) |
| 5 | Yanıt üretimi | `shared/src/PageRouter/ResponseEmitter.php` | HTTP yanıt yayımı (163 satır toplam) |

## §4 — Kapsam dışı

İş kuralı kararları → K008-domain · arka plan işi → K012-services · dış API →
K013-integraion. Bu çocuk yalnız *akış şemasını* tutar; kural üretmez.

## §5 — Açık sorular

1. `*UseCase` sınıfları yok → akış modeli PageRouter mı, ayrı soyutlama mı? → ⚠️ VERIFICATION REQUIRED
2. `shared/src/Session/SessionInitializer.php` ile `shared/src/PageRouter/SessionInitializer.php` iki ayrı sınıf — akışta hangisi? → ⚠️ VERIFICATION REQUIRED

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `usecase-akislari-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kanit | `usecase-akislari-web-arastirma.md` | Sistemli resmi kaynak tarama kaydı | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-12