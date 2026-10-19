---
node_id: K18-testing-web-arastirma
node_type: subdomain
status: draft
parent_id: K18-testing
children: []
depends_on: [K17-devops]
evidence:
  - "5 URL bu oturumda fetch edildi (HTTP 200 — 2026-10-18): martinfowler practical-test-pyramid · martinfowler TestPyramid bliki · martinfowler test-shapes · testrail testing-pyramid · smashingmagazine long-live-test-pyramid"
  - ".ai/architecture/K18-testing/testing-dogrulama.md §4 (2026-10-17 turu: 5 web kanıt kaydı — PHPUnit --testsuite · PHPStan 2.x · Playwright 1.63 · 1 pending)"
validation: "Her URL oturumda fetch edilerek doğrulandı (erişim tarihi + HTTP durumu); erişilemeyen kaynak ⚠️/404/403 olarak işaretlenir; içerik iddiası yalnız okunandan yazılır."
open_questions:
  - "⚠️ testing-dogrulama.md §4/5'teki pending iddia (soft-gate best practice — GitHub Actions resmi docs) bu turda da OKUNMADI, hâlâ S4"
group: kanit
---

# Testing — Web Araştırması

> K18-testing iddialarının dış kaynak doğrulamaları. Her URL erişim tarihi +
> HTTP durumuyla kayıtlıdır; içeriğe dair iddialar yalnız okunandan yazılır
> (zero-hallucination).

## §1. ELI15 — Web doğrulaması neden yapılıyor?

Test pyramid'i gibi kavramlar "herkes öyle biliyor" diye dolaşır; ama "herkes
öyle biliyor" bir kanıt değildir. Bu dosya, K18-testing'in dayandığı kavramları
**gerçek kaynakta okuyarak** doğrular: kaynak bugün duruyor mu, ne diyor, ne
deminiyor. Erişilemeyen kaynak da kayıt altına alınır — "404" da bir bilgidir.

## §2. Bu Oturumda Doğrulanan URL'ler (2026-10-18 — fetch edildi)

| # | URL | Erişim | Ne doğrulandı (okunan içerikten) |
|---|---|---|---|
| 1 | https://martinfowler.com/articles/practical-test-pyramid.html | ✅ HTTP 200 (2026-10-18) | Vocke, 2018: pyramid katları (unit/integration/contract/UI); unit = fast, isolated, self-verifying; integration = gerçek bağımlılık; Arrange-Act-Assert akışı; contract testing (Pact/CDC) tanımı; UI testlerinin yavaş/kırılgan oluşu |
| 2 | https://martinfowler.com/bliki/TestPyramid.html | ✅ HTTP 200 (2026-10-18) | Mike Cohn'un pyramid etimolojisi (2009); "second line of defense" (CI öncesi hızlı geri bildirim); bulgu = unit test'e çevrilmeli |
| 3 | https://martinfowler.com/articles/2021-test-shapes.html | ✅ HTTP 200 (2026-10-18) | Fowler, 2021: pyramid/honeycomb/trophy tartışması; "unit test" tanımı belirsizdir (sociable vs solitary — Jay Fields); Justin Searls alıntısı: oran tartışması dikkat dağıtır, EXPRESSIVE testler asıl mesele |
| 4 | https://www.testrail.com/blog/testing-pyramid | ✅ HTTP 200 (2026-10-18) | Pyramid'in 3 katı + statik analiz varyantı; üst kata çıktıkça feedback süresi artar/yavaşlar |
| 5 | https://www.smashingmagazine.com/2023/09/long-live-test-pyramid | ✅ HTTP 200 (2026-10-18) | Pyramid'e alternatifler: diamond, ice-cream-cone, testing trophy (Kent C. Dodds); "Write tests. Not too many. Mostly integration." (Guillermo Rauch); trophy = integration ağırlıklı |

## §3. Devralınan Web Kayıtları (2026-10-17 turu — `testing-dogrulama.md` §4)

| # | URL | Tarih | Durum | Not |
|---|---|---|---|---|
| 1 | https://docs.phpunit.de/en/12.5/textui.html | 2026-10-17 | ✅ verified | `--testsuite` XML'deki suite'leri çalıştırır (ci.yml php-test job temeli doğru) |
| 2 | https://core.trac.wordpress.org/ticket/62004 | 2026-10-17 | ✅ verified (ikincil) | PHPUnit 11+: sınıf artık birden fazla suite'e ait olamaz; ⚠️ resmi migration sayfası 404 verdi (docs.phpunit.de/en/11.5/migration11.html) — resmi URL sonraki tur |
| 3 | https://phpstan.org/blog/phpstan-2-0-released-level-10-elephpants | 2026-10-17 | ✅ verified | PHPStan 2.0 (2024-11-11) yayınlandı; repo'daki ^1.10 iki major geride |
| 4 | https://phpstan.org/blog/upgrading-from-phpstan-1-to-2 | 2026-10-17 | ✅ verified | 1→2 yükseltme rehberi mevcut |
| 5 | https://playwright.dev/docs/release-notes | 2026-10-17 | ✅ verified | 1.63 güncel kararlı sürüm (test locks; Chrome for Testing indirmesi); repo ^1.63.0 güncel |

## §4. Erişilemeyen / Elenen Kaynaklar (bu domainde — kayıtlı)

| # | Kaynak | Durum | Sonuç |
|---|---|---|---|
| 1 | docs.phpunit.de/en/11.5/migration11.html | 404 (2026-10-17) | yerine ikincil kaynak (WordPress Trac) kullanıldı — resmi URL açık |
| 2 | GitHub Actions resmi docs (soft-gate best practice iddiası) | ⏳ OKUNMADI (2026-10-17 ve 2026-10-18 turlarında) | iddia hâlâ S4 = karar girdisi yapılamaz (`testing-dogrulama.md` §4/5) |

## §5. Araştırma → Strateji Etkisi

| Bulgu | K18-testing'e etkisi |
|---|---|
| Pyramid = oran rehberi, dogma değil (Fowler 2021; trophy/diamond alternatifleri) | `testing-test-stratejisi.md` §2 yorumu "diskten sayıldı" ile hizalı — oran dogmalaştırılmadı |
| "Mostly integration" akımı (Rauch/Dodds) | mevcut dağılım (unit ağırlıklı 55 PHP + integration ~21) ile tutarlılık sorgusu → açık soru değil, bilinçli pyramid yorumu |
| PHPUnit 11 suite kuralı | `--testsuite` job'u (ci.yml L66) sürüm yükseltmesinde kırılabilir → R-kayıt (phpstan ^1.10 geride — testing-riskler.md R9 ile aynı sınıf) |
| PHPStan 2.x / Playwright 1.63 | envanter sürüm tazeliği doğrulandı (`testing-master.md` §4) |

## §6. Kaynaklar

- Bu oturum fetch kayıtları (5 URL × HTTP 200 — 2026-10-18)
- `testing-dogrulama.md` §4 (2026-10-17 turu 5 kayıt) — 2026-10-18 okundu

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18 (Stage-2-F)
