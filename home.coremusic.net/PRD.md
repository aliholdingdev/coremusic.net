---
title: "CoreMusic — home.coremusic.net PRD (Ana Medya Paneli Gereksinim Belgesi)"
type: prd
category: domain
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · bağ: [[CONTEXT.md]] · [[AGENTS.md]]"
updated: 2026-10-23
---

# home.coremusic.net — PRD

**docType:** prd · **Klasör:** `home.coremusic.net/` · **Yüzey:** #5 (aile indeksi §5) · **Sorumlu:** UI Designer + Backend Architect (+ embedded iş birliği)

**Zorunlu Bağlantılar:** [[../PRD.md]] · [[CONTEXT.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[WORKFLOW.md]]

**Bağlı platform gereksinimleri (tekrar edilmez, yalnız bağlanır):** PRD-PLT-001 · PRD-PLT-010

---

## 1. Amaç

Bu PRD, `home.coremusic.net/` yüzeyinin **ana medya paneli** gereksinimlerini tanımlar: `index.php` üzerinden Shared SPA Router (PageRouterKernel) ile sayfa üretimi, `assets.coremusic.net`'ten statik varlık çağrısı, kimlik devri (`include/Auth/HomeAuthBridge.php`) ve `HomeSessionManager` ile oturum. Panel, Raspberry Pi 5 üzerinde çalışan CoreMusic ana ekranıdır.

> **eli10 (basit):** Bu dosya, ana ekranın ne yapması gerektiğini ve kimden ne ödünç aldığını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü panel hem frontend hem backend hem auth köprüsü taşıdığından
> sınır çakışması en çok burada olur. Okunması, hangi parçanın kimin olduğunu gösterir. Yazılması,
> mockup/oturum/router üçlüsünün sözleşmesini kilitler. Olmazsa panel kendi auth'unu ve router'ını
> icat eder, platform tekilliği kırılır.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `index.php` + `header.php` / `footer.php` (HTML kabuk) | Router çekirdeği → `shared/src/PageRouter` (#9) |
| `include/Auth/HomeAuthBridge.php` (auth devri sözleşmesi) | Auth akışı → auth PRD (#4) + auth/include (#22) |
| `include/Session/HomeSessionManager.php` | Statik varlık üretimi → assets PRD (#3) |
| `pages/**` (home, player, ayarlar, health, redirect, auth_callback) | Çalar DSP çekirdeği → coreplayer alt PRD (#20) |
| `config/**` bootstrap zinciri (app/bootstrap/config/constants) | `.env` içeriği (REDACTED) |

- **Ölçüm (disk kanıtı, 2026-10-12):** kök **20 girdi = 14 dosya + 6 dizin**; yineleme **2168 girdi = 1865 dosya + 303 dizin**; proje PHP **38** / vendor PHP **1591** — sayı `[[CONTEXT.md]]` §1'dedir, burada tekrarlanmaz.
- **Ön koşul:** Mockup gate — header/footer değişikliği `home-1024` PNG'leriyle karşılaştırılır (Header 60px y:0-60 · İçerik 450px · Footer 90px kanonik referans; 12 ekran).
- **Dokunulmaz:** `config/.env` (REDACTED) · `.ai/.png/**` (salt-okunur SSOT) · `auth_callback.php` içinde oturum mantığı yeniden icat edilmez · frozen ADR metinleri.

> **eli10 (basit):** Kapsam panel sayfaları, kabuk ve auth köprüsü; router/çalar/auth başka PRD'de.
> **eli15 (detay):** Sınır, panelin ortak katmanı kopyalamasını engeller. Okunması, ödünç alınanın
> nerede olduğunu gösterir. Sınır yoksa PageRouter kökü kopyalanır, sürüm dağılır.

---

## 3. Gereksinimler (PRD-HOM-001..006)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-HOM-001 | Sayfa üretimi tek router üzerinden (PageRouterKernel, PRD-PLT-008) | Kendi route/mechanism yaratılmaz; yeni sayfa `pages/` + bootstrap zincirine kayıt |
| PRD-HOM-002 | Kimlik devri yalnız `HomeAuthBridge` üzerinden yürür | Bridge dışına taşan auth/token mantığı yok |
| PRD-HOM-003 | Oturum işlemleri yalnız `HomeSessionManager` üzerinden | Doğrudan session/superglobal yazımı yok |
| PRD-HOM-004 | `auth_callback.php` yeniden icat etmez | Token/oturum kurulumu yalnız akış sözleşmesindeki çağrı sırası |
| PRD-HOM-005 | Header/footer mockup'a sadık (kanonik ölçüler) | 60/450/90px referansı bozulmaz; mockupta olmayan öge eklenmez (Guardrail #11) |
| PRD-HOM-006 | `health.php` davranışı korunur | Sağlık ucu yanıt şekli değişmez (QA koruma) |

> **eli10 (basit):** Altı kural: tek router, tek auth köprüsü, tek oturum yolu, icat yok, mockup ölçüsü, sağlık ucu korumalı.
> **eli15 (detay):** Kimlikler ayrı çünkü panel kritikleri denetlenebilir olmalıdır. Okunması, sayfa eklerken
> kontrol verir. Kimliksiz kural "stil tercihi"ne döner; bridge ihlali testle yakalanmaz.

---

## 4. Kabul Kriteri (bu yüzeyin kapıları)

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Mockup | PRD-HOM-005 — `home-1024` PNG'leri okundu, ölçüler korundu |
| 2 | Router | PRD-HOM-001 — PageRouterKernel tek mekanizma |
| 3 | Auth | PRD-HOM-002/004 — Bridge sözleşmesi; security PASS |
| 4 | Oturum | PRD-HOM-003 — `HomeSessionManager` tek kapı |
| 5 | Kanıt | Ölçülmeyen layout/ölçü iddiası `⚠️ VERIFICATION REQUIRED` (PRD-PLT-011) |

> **eli10 (basit):** Beş kapı: mockup, router, auth, oturum, kanıt.
> **eli15 (detay):** Kapılar üretimden ayrıdır. Okunması, kapanış raporu checklist'ini verir. Kapı yoksa
> "mockup'a uygun" iddiası kanıtsız olur.

---

## 5. ADR Bağları

| Karar | ADR | Yüzeye etkisi |
|-------|-----|---------------|
| Shared SPA Router deseni | ADR-021 / ADR-083 | PRD-HOM-001 |
| Servis yerleşimi / portlar | ADR-039 (bağ) | `health.php` + deploy |
| Auth konsolidasyonu (köprü gerekçesi) | ADR-043 | PRD-HOM-002/004 |
| Offline-First akış | PRD-PLT-001 (bağ) → coreplayer #20 | PRD-PLT-001 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** | Bridge/router sözleşmesi değişince |

> **eli10 (basit):** Mimari kararlar ADR'de; bu PRD yalnız bağ verir.
> **eli15 (detay):** Bağ, frozen ADR metninin tek sürüm kalmasını sağlar.

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | `auth_callback`'de oturum mantığının yeniden yazılması | Çifte akış, güvenlik açığı | PRD-HOM-004 + review |
| 2 | Panelin kendi router'ı/kopyası olması | Sürüm dağılması | PRD-HOM-001 + PRD-PLT-005 |
| 3 | Mockupsız header/footer | Tasarım sapması + layout shift | PRD-HOM-005 + Guardrail #11 DUR |
| 4 | `HomeSessionManager` yerine doğrudan session | Oturum tutarsızlığı | PRD-HOM-003 |

> **eli10 (basit):** En büyük tehlike: köprüyü ve oturumu bozup mockupı atlamak.
> **eli15 (detay):** Her riskin azaltması bir kapıya bağlıdır; kapı atlanırsa risk geri döner.

---

## 7. Sonraki Adım

1. #20 (coreplayer alt PRD) ile Offline-First oynatma akışı (PRD-PLT-001) çapraz kontrol edilir.
2. #10/#9 (Middleware/PageRouter) PRD'leriyle router/kernel sözleşmesi doğrulanır.
3. Faz 3 GUI spec (`media.coremusic.net/docs/faz3-gui-spec.md`) bu panelin genişlemesi ise kapıya eklenir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
