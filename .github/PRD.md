---
title: "CoreMusic — .github PRD (CI/CD & Depo Altyapısı Gereksinim Belgesi)"
type: prd
category: infrastructure
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · bağ: [[CONTEXT.md]] · [[AGENTS.md]]"
updated: 2026-10-23
---

# .github — PRD

**docType:** prd · **Klasör:** `.github/` · **Yüzey:** #8 (aile indeksi §5) · **Sorumlu:** DevOps Engineer (+ Security/QA denetimi)

**Zorunlu Bağlantılar:** [[../PRD.md]] · [[CONTEXT.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[WORKFLOW.md]]

**Bağlı platform gereksinimleri (tekrar edilmez, yalnız bağlanır):** PRD-PLT-006 · PRD-PLT-011

---

## 1. Amaç

Bu PRD, `.github/` klasörünün **CI/CD ve depo altyapısı** gereksinimlerini tanımlar: `ci.yml` (test/lint kapıları), `secret-scan.yml` (GitLeaks) ve `ISSUE_TEMPLATE/` (hata raporu formu). Bu yüzeyin dosyaları depo davranışını değiştirdiğinden yetki sınırı koddan dardır; her `.yml` değişikliği onay kapısından geçer.

> **eli10 (basit):** Bu dosya, otomatik kapıların (test, tarama, form) ne yapması gerektiğini söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü CI dosyaları ilk push'ta sürpriz çalışır; gereksinimi yoksa
> onaysız kapı açılır/kapanır. Okunması, yml değişikliği öncesi onay kuralını verir. Yazılması,
> QA/Security denetimini görünür kılar. Olmazsa sızıntı ve test kapısı kör çalışır.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `workflows/ci.yml` (test/lint job'ları) | GitHub arayüz ayarları (org/repo seviyesi — diskte değil) |
| `workflows/secret-scan.yml` (GitLeaks) | PHP/JS/CSS kodu (ilgili katman PRD'leri) |
| `ISSUE_TEMPLATE/01-bug-report.md` | `.ai/` vault registry (SSOT → `.ai/AGENTS.md`) |
| `.gitleaks.toml` (gelecek config — Security onayı) | Deployment onay akışı → `.workflows/deployment.md` |

- **Ölçüm (disk kanıtı):** `.github/` = `workflows/` + `ISSUE_TEMPLATE/` + `AGENTS.md` · `CLAUDE.md` · `CONTEXT.md` · `WORKFLOW.md`; workflow dosyaları **2** (`ci.yml` · `secret-scan.yml`).
- **Ön koşul:** Yeni workflow taslağı şablondan türetilir (Guardrail #16) → kullanıcı onayı → uygulama.
- **Dokunulmaz:** `CLAUDE.md` (kök + `ISSUE_TEMPLATE/`) — yalnız okunur · job silme/ekleme onaysız yasak · secret değeri `.md/.log/.json`'a yazılmaz (`[REDACTED]`).

> **eli10 (basit):** Kapsam iki workflow ve hata formu; GitHub ayarları ve uygulama kodu dışarıda.
> **eli15 (detay):** Sınır, kural dosyalarının elle ezilmesini engeller. Okunması, hangi dosyanın kimin
> olduğunu gösterir. Sınır yoksa CI sessizce kırılır ve kimsesiz kalır.

---

## 3. Gereksinimler (PRD-GIT-001..005)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-GIT-001 | CI test/lint kapıları yeşil olmadan merge yok | `ci.yml` job'ları PASS; kapsam değişince QA onayı |
| PRD-GIT-002 | Secret tarama her push/PR'da çalışır | GitLeaks job aktif; `.gitleaks.toml` değişikliği Security onayı ister |
| PRD-GIT-003 | Yeni workflow onay kapısından geçer | Taslak → kullanıcı onayı → uygulama; onaysız job yok |
| PRD-GIT-004 | Hata raporu formu standart bilgiyi toplar | `01-bug-report.md` alan değişikliği kullanıcı onaylı |
| PRD-GIT-005 | CI'daki coverage/lint hedefi QA standardıyla uyumlu | PRD-PLT-006 kapıları CI'ya yansır; iddiasız kapatma yok |

> **eli10 (basit):** Beş kural: yeşil kapı, tarama açık, onaysız workflow yok, standart form, uyumlu hedef.
> **eli15 (detay):** Kimlikler ayrı çünkü CI değişikliği tüm ekibi etkiler; denetlenebilir olmalıdır.
> Okunması, yml düzenlerken kontrol verir. Kimliksiz kural "tercih"e döner, kapı sürprizi doğar.

---

## 4. Kabul Kriteri (bu yüzeyin kapıları)

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | CI | PRD-GIT-001 — `ci.yml` PASS |
| 2 | Güvenlik | PRD-GIT-002 — GitLeaks temiz; config onayı Security'de |
| 3 | Onay | PRD-GIT-003/004 — kullanıcı onayı kayıtlı |
| 4 | Tutarlılık | PRD-GIT-005 — QA standardı ile hizalı |
| 5 | Kanıt | CI durumu/log kanıtı yoksa "geçti" iddiası yazılmaz (`⚠️ VERIFICATION REQUIRED` — PRD-PLT-011) |

> **eli10 (basit):** Beş kapı: CI, güvenlik, onay, tutarlılık, kanıt.
> **eli15 (detay):** Kapılar üretimden ayrıdır. Okunması, kapanış raporu checklist'ini verir. Kapı yoksa
> "CI yeşil" iddiası kanıtsız olur.

---

## 5. ADR Bağları

| Karar | ADR | Yüzeye etkisi |
|-------|-----|---------------|
| OWASP/secret kapıları CI'ya yansır | PRD-PLT-006 (bağ) | PRD-GIT-002/005 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** | Yeni workflow/policy gerektiğinde |
| Deployment onay akışı | `.workflows/deployment.md` (süreç) | CI kapısını tamamlar |

> **eli10 (basit):** Güvenlik/deploy kararları ADR ve süreçtedir; bu PRD yalnız bağ verir.
> **eli15 (detay):** Bağ, frozen metnin tek sürüm kalmasını sağlar.

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Onaysız `.yml` değişikliği | Sürpriz CI davranışı (kapı açılır/kapanır) | PRD-GIT-003 + §3.3 yasak 4 |
| 2 | GitLeaks config'inin körleşmesi | Secret sızıntısı | PRD-GIT-002 + Security onayı |
| 3 | `CLAUDE.md`'nin elle düzenlenmesi | SSOT bozulması | Dokunulmazlık — vault-sync ile |
| 4 | Test job'ının kapsam dışı kalması | Kör merge | PRD-GIT-001/005 + QA |

> **eli10 (basit):** En büyük tehlike: izinsiz yml değişikliği ve taramanın körleşmesi.
> **eli15 (detay):** Her riskin azaltması bir kapıya bağlıdır; kapı atlanırsa risk doğrudan depoya girer.

---

## 7. Sonraki Adım

1. CI coverage/lint durumu `[[../PRD.md]]` §8 riskine geri beslenir (PRD-PLT-011).
2. Yeni kapı ihtiyacı (deploy, tarama genişlemesi) → ADR (next: 111) + taslak onayı.
3. `.workflows/deployment.md` ile CI↔deploy akışı çapraz kontrol edilir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
