---
title: "CoreMusic — api.coremusic.net PRD (API Gateway Gereksinim Belgesi)"
type: prd
category: api
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · bağ: [[CONTEXT.md]] (envanter) · [[AGENTS.md]] (routing)"
updated: 2026-10-23
---

# api.coremusic.net — PRD

**docType:** prd · **Klasör:** `api.coremusic.net/` · **Yüzey:** #2 (aile indeksi §5) · **Sorumlu:** Backend Architect (+ Security denetimi)

**Zorunlu Bağlantılar:** [[../PRD.md]] · [[CONTEXT.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[WORKFLOW.md]]

**Bağlı platform gereksinimleri (tekrar edilmez, yalnız bağlanır):** PRD-PLT-004 · PRD-PLT-005 · PRD-PLT-006 · PRD-PLT-008

---

## 1. Amaç

Bu PRD, `api.coremusic.net/` yüzeyinin **JSON-only API ağ geçidi** gereksinimlerini tanımlar: `/health` canlılık ucunun ve `/api/v1/*` uçlarının davranışı, route/middleware sözleşmesi, hata şekli ve güvenlik kapıları. Amaç, istemciye (SPA, mobil, sağlık izleyici) **tek ve öngörülebilir bir JSON sözleşmesi** vermektir.

> **eli10 (basit):** Bu dosya, API'nin neye izin verdiğini ve neye izin vermediğini söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü ağ geçidi tüm subdomain'lerin veri kapısıdır; gereksinimi yoksa
> her uç kendi kuralını icat eder. Okunması, yeni uç eklerken sözleşme kontrolünü verir. Yazılması,
> Backend ile Security arasındaki yetki sınırını görünür kılar. Olmazsa test ve güvenlik denetimi
> hedefsiz kalır.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `index.php` (middleware sırası, hata sözleşmesi) | `shared/src/**` ortak katman (PRD'si #7) |
| `include/**` Controller/Container, `config/routes.php` | `auth.coremusic.net/include/` (auth yüzeyi, #22) |
| `config/cors.php` (Security sahipliğinde) | DB şeması → `.ai/.sql/**` (#25) |
| `tests/**` + `phpunit.xml` kapıları | Vault doküman üretimi (`.ai/**`) |

- **Ölçüm (disk kanıtı, 2026-10-12):** kök **13 dosya + 4 alt dizin**; yineleme **2856 dosya / 358 dizin / 3214 girdi** (`vendor/` dahil) — sayı `[[CONTEXT.md]]` §1'dedir, burada tekrarlanmaz.
- **Ön koşul:** `shared/` paketi Composer path-repo ile bağlı (PRD-PLT-005); route tablosu ADR-020 ile sabit sıra taşır.
- **Dokunulmaz:** `index.php` içindeki middleware sırası (ADR-020) ADR'siz değiştirilemez; frozen ADR metinleri.

> **eli10 (basit):** Kapsam bu klasördeki kapı ve kural dosyaları; veritabanı ve auth başka PRD'de.
> **eli15 (detay):** Sınır, iki PRD'nin aynı kuralı farklı yazmasını (SSOT ihlali) engeller. Okunması,
> hangi işin buraya ait olduğunu gösterir. Sınır yoksa ortak katman kopyalanır ve sürüm dağılır.

---

## 3. Gereksinimler (PRD-API-001..006)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-API-001 | Tüm uçlar JSON döndürür; HTML/SPA router kullanılmaz | Yanıt `Content-Type: application/json`; template çıktısı 0 |
| PRD-API-002 | `/health` canlılık ucu ayrık ve ucuza mal olur | DB'ye dokunmadan 200 JSON; süre ölçümü log'da (`⚠️` eşiği: ölçülmediyse iddia yazılmaz) |
| PRD-API-003 | Middleware sırası ADR-020'ye sadık kalınır | Sıra değişikliği yalnız ADR ile; `RouteConfigTest` yeşil |
| PRD-API-004 | Hata yanıtı sızdırmaz (stack/SQL görünmez) | 5xx gövdesi yalnız `{code, message}`; Security denetimi PASS |
| PRD-API-005 | CORS yalnız `config/cors.php`'den yönetilir | Endpoint içinde header set edilmez; Security onayı yoksa değişiklik yok |
| PRD-API-006 | Her yeni uç route + controller + test üçlüsüyle gelir | `composer test` yeşil (`SmokeTest` · `RouteConfigTest` · `AuthControllerTest` korunur) |

> **eli10 (basit):** Altı kural: hep JSON, ucuz sağlık ucu, sabit sıra, sızıntısız hata, tek CORS kapısı, testli yeni uç.
> **eli15 (detay):** Kimlikler ayrı çünkü API sözleşmesi ihlali müşteri tarafında kırılır ve denetlenebilir
> olmalıdır. Okunması, yeni uç eklerken kontrol listesi verir. Kimliksiz gereksinim "iyi olur"ya döner,
> `composer test` kapısı onu yakalayamaz.

---

## 4. Kabul Kriteri (bu yüzeyin kapıları)

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Sözleşme | PRD-API-001/002 — JSON-only + `/health` |
| 2 | Sıra | PRD-API-003 — ADR-020 |
| 3 | Güvenlik | PRD-API-004/005 — `security-engineer` PASS (PRD-PLT-006) |
| 4 | Test | PRD-API-006 — `composer test` yeşil |
| 5 | Kanıt | Ölçüm/iddia disk ölçümüne dayanır (PRD-PLT-011); `UNKNOWN` → `⚠️ VERIFICATION REQUIRED` |

> **eli10 (basit):** Beş kapıdan geçmeyen API değişikliği "bitti" sayılmaz.
> **eli15 (detay):** Kapılar gereksinimlerden ayrıdır; gereksinim "ne"yi, kapı "nasıl kanıtlanır"ı söyler.
> Okunması, kapanış raporuna checklist verir. Kapı yoksa yeşil test iddiası kanıtsız olur.

---

## 5. ADR Bağları

| Karar | ADR | Yüzeye etkisi |
|-------|-----|---------------|
| Middleware sırası / route sözleşmesi | ADR-020 | PRD-API-003 |
| ORM yasak, DB erişimi shared üzerinden | ADR-002 / ADR-003 | PRD-PLT-005 (bağ) |
| SSO/RBAC akışı bu yüzeyde icat edilmez | PRD-PLT-004 (bağ) → auth PRD (#4/#22) | PRD-API-006 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** | Sıra/CORS değişikliğinde |

> **eli10 (basit):** Büyük kararlar ADR'dedir; bu PRD yalnız bağ verir, metni kopyalamaz.
> **eli15 (detay):** Bağ, ADR metninin frozen olduğunu korur; kopya iki sürüm doğurur ve biri eskir.

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Middleware sırasının ADR'siz değişmesi | Güvenlik/zincir kırılması | PRD-API-003 + `RouteConfigTest` |
| 2 | Uçta gömülü CORS/header | Çifte politika, sızıntı | PRD-API-005 (`config/cors.php` tek kapı) |
| 3 | Hata gövdesinde stack/SQL | Bilgi sızıntısı (OWASP) | PRD-API-004 + Security denetimi |
| 4 | Test'siz yeni uç | Sessiz sözleşme kırılması | PRD-API-006 üçlüsü |

> **eli10 (basit):** En büyük tehlike: sırayı ve hata cevabını bozup bunu testle yakalanmamak.
> **eli15 (detay):** Her riskin azaltması bir kapıya bağlıdır; kapı devre dışı kalırsa risk geri döner.

---

## 7. Sonraki Adım

1. #7 (shared) PRD'si ile ADR-020 route sözleşmesi çapraz kontrol edilir.
2. Uç genişlemesi: route + controller + test üçlüsü (PRD-API-006) — QA kapısı `composer test`.
3. Ölçüm/iddia gerekliyse `⚠️ VERIFICATION REQUIRED` ile üst PRD'ye (`[[../PRD.md]]`) bildirilir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
