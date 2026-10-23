---
title: "CoreMusic — admin.coremusic.net PRD (Yönetim Paneli Gereksinim Belgesi)"
type: prd
category: admin
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · bağ: [[CONTEXT.md]] (envanter) · [[AGENTS.md]] (routing)"
updated: 2026-10-23
---

# admin.coremusic.net — PRD

**docType:** prd · **Klasör:** `admin.coremusic.net/` · **Yüzey:** (aile indeksi §5 — bu revizyonda eklenecek) · **Sorumlu:** Backend Architect (+ Security denetimi)

**Zorunlu Bağlantılar:** [[../PRD.md]] · [[CONTEXT.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[WORKFLOW.md]]

**Bağlı platform gereksinimleri (tekrar edilmez, yalnız bağlanır):** PRD-PLT-004 · PRD-PLT-005 · PRD-PLT-006

---

## 1. Amaç

Bu PRD, `admin.coremusic.net/` yüzeyinin **server-rendered yönetim paneli** gereksinimlerini tanımlar: 6 admin sayfasının davranışı, auth callback sözleşmesi, CSRF/escaping kapıları ve test kabulü. Amaç, panelin **auth'dan izole tek kimlik akışıyla** (ADR-043) ve **kanıtlanabilir sözleşme testleriyle** (ADR-058) yönetilmesidir.

> **eli10 (basit):** Bu dosya, admin panelinin neye izin verdiğini ve neye izin vermediğini söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü panel yüzeyi auth'dan farklı hızda değişir; gereksinimi yoksa her sayfa kendi kuralını icat eder. Okunması, yeni sayfa eklerken sözleşme kontrolünü verir. Yazılması, Backend ile Security arasındaki yetki sınırını görünür kılar.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `pages/**` (6 sayfa + layout), `config/routes.php`, `config/bootstrap.php` | `../auth.coremusic.net/` auth formları + validate-key (PRD'si orada; ADR-043) |
| `include/Auth/AdminAuthBridge.php` + `include/Routing/**` | `../shared/src/**` ortak katman (bağımlı olunan) |
| `tests/**` + `phpunit.xml` kapıları | `../assets.coremusic.net/` CSS/JS/ görsel varlıklar |
| `.htaccess` / `web.config` erişim kapatma + başlıklar | DB şeması → `.ai/.sql/**` (Data) |

- **Ölçüm (disk kanıtı, 2026-10-23):** kök **8 dosya + 5 alt dizin**; vendor hariç toplam **28 dosya** (21 `.php`) — sayı `[[CONTEXT.md]]` §3.1'dedir.
- **Ön koşul:** `shared/` paketi Composer path-repo (junction) ile bağlı (PRD-PLT-005); auth SSOT erişilebilir (`auth.coremusic.net/validate-key`).
- **Dokunulmaz:** `shared/config/routes.php` (dosya yorumu: DEĞİŞTİRİLMEZ); frozen ADR metinleri; `vendor/` junction.

> **eli10 (basit):** Kapsam bu klasörün panel, kapı ve test dosyaları; auth formları ve ortak katman başka PRD'de.
> **eli15 (detay):** Sınır, iki PRD'nin aynı kuralı farklı yazmasını (SSOT ihlali) engeller. Sınır yoksa auth akışı iki yerde tanımlanır ve sürüm dağılır.

---

## 3. Gereksinimler (PRD-ADM-001..007)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-ADM-001 | Panel erişimi yalnız authed session ile mümkün | `requiresAuth:true` işaretli 6 rota; auth'suz istek auth login'e düşer (`AdminRootRedirector` testi yeşil) |
| PRD-ADM-002 | Auth formları bu yüzeyde üretilmez (ADR-043) | `/login`, `/register`, `/admin-login`, `/admin-register` → 302 `auth.coremusic.net/admin/*`; `AdminLoginAliasRedirectTest` yeşil |
| PRD-ADM-003 | `/auth/callback` doğrulaması yalnız `AdminAuthBridge` üzerinden | auth_key → cURL POST `/validate-key` (retry 2, timeout 5 sn) → `session_regenerate_id(true)` → 302 `/admin-dashboard`; `AdminAuthBridgeTest` yeşil |
| PRD-ADM-004 | Her state-changing form CSRF token taşır | `$_SESSION['csrf_token']` gizli input deseni (settings, song-add, playlist sayfaları); token olmayan form reddedilir |
| PRD-ADM-005 | Tüm çıktı escaping ile geçer | `$h` (htmlspecialchars ENT_QUOTES\|ENT_SUBSTITUTE, UTF-8) tek sarmalayıcı; `AdminMarkupContractTest` yeşil (ADR-058: auth markup sözleşmesi de korunur) |
| PRD-ADM-006 | Yüzey sunucu düzeyinde kapatılır | `config|include|pages` + non-index `.php` + `.env/.md/.log/...` → 403; güvenlik başlıkları (X-Frame-Options DENY, nosniff) korunur |
| PRD-ADM-007 | `/dev-login` yalnız üretim-dışı ortamda anlamlı | DEBUG_MODE koşulu korunur; production'da devre dışı olduğu Security doğrulamasıyla teyit edilir (kanıt yoksa `⚠️ VERIFICATION REQUIRED`) |

> **eli10 (basit):** Yedi kural: kapalı kapı, form auth'da, doğru köprü, token zorunlu, temiz çıkış, kapalı sunucu, kapalı debug.
> **eli15 (detay):** Kimlikler ayrı çünkü panel sözleşmesi ihlali doğrudan güvenlik olayıdır ve denetlenebilir olmalıdır. Kimliksiz gereksinim "iyi olur"ya döner; test kapısı onu yakalayamaz.

---

## 4. Kabul Kriteri (bu yüzeyin kapıları)

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Sözleşme | PRD-ADM-001/002 — requiresAuth + alias 302 |
| 2 | Auth | PRD-ADM-003 — bridge + regenerate (Security PASS) |
| 3 | CSRF/escaping | PRD-ADM-004/005 — markup + token testleri |
| 4 | Sunucu | PRD-ADM-006 — 403 kuralları + başlıklar |
| 5 | Test | `vendor/bin/phpunit` yeşil (`phpunit.xml`, kapsam `include/`) |
| 6 | Kanıt | Ölçüm/iddia disk ölçümüne dayanır (PRD-PLT-011); `UNKNOWN` → `⚠️ VERIFICATION REQUIRED` |

> **eli10 (basit):** Altı kapıdan geçmeyen panel değişikliği "bitti" sayılmaz.
> **eli15 (detay):** Kapılar gereksinimlerden ayrıdır; gereksinim "ne"yi, kapı "nasıl kanıtlanır"ı söyler. Kapı yoksa yeşil test iddiası kanıtsız olur.

---

## 5. ADR Bağları

| Karar | ADR | Yüzeye etkisi |
|-------|-----|---------------|
| Auth formları auth subdomain'de | ADR-043 (frozen) | PRD-ADM-002 |
| Login/register markup testleri auth klasöründe | ADR-058 | PRD-ADM-005 |
| ORM yasak, DB erişimi shared üzerinden | ADR-002 / ADR-003 | PRD-PLT-005 (bağ) |
| SSO/RBAC bu yüzeyde icat edilmez | PRD-PLT-004 → auth PRD | PRD-ADM-001/003 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** | Auth/panel sözleşmesi değişikliğinde |

> **eli10 (basit):** Büyük kararlar ADR'dedir; bu PRD yalnız bağ verir, metni kopyalamaz.
> **eli15 (detay):** Bağ, ADR metninin frozen olduğunu korur; kopya iki sürüm doğurur ve biri eskir.

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | `/dev-login`'in production'a sızması | Yetkisiz panel erişimi (CRITICAL) | PRD-ADM-007 + Security teyidi; koşul satırı ADR'siz değişmez |
| 2 | `requiresAuth:false` işaretinin yanlış rota'ya konması | Açık sayfa | PRD-ADM-001 + RouteResolutionTest |
| 3 | CSRF token'ın yeni formda unutulması | CSRF açığı | PRD-ADM-004 + markup testi genişletmesi (QA) |
| 4 | `coremusic_auth` şemasına dağınık sorgu büyümesi | BCNF/SSOT ihlali | PRD-ADM-003 handover zinciri → Data (`../shared`) |
| 5 | Markup testinin auth formlarıyla senkron kayması | Sahte yeşil | ADR-058 koruması; test kırmızıysa auth klasörüne handover |

> **eli10 (basit):** En büyük tehlike: açık kapı (debug/auth işareti) ve unutulan token.
> **eli15 (detay):** Riskler ayrı çünkü her birinin azaltması farklı sorumluya aittir. Okunması, kapanış sırasında hangi kontrolün ihmal edilmeyeceğini gösterir.

---

## 7. Sonraki Adım

1. Aile indeksi güncellemesi: bu PRD `[[../PRD.md]]` §5 tablosuna satır olarak eklenir (admin yüzeyi #29 — bu revizyonda yapıldı; indeks sürümü 1.2.0).
2. Açık maddelerin kapanışı: `/dev-login` production teyidi (Security) · rate-limit uygulama kanıtı (⚠️) · `.phpunit.result.cache` gitignore kararı (DevOps).
3. Commit: **orkestratörde** (kullanıcı onayı ile) — bu revizyonun dosyaları commit edilmedi.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
