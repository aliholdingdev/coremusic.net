---
type: architecture
category: layer
title: "K000 — İşletim Sistemi"
date: 2026-10-10
status: active
version: 2.1.0
authority: SSOT
---

# K000 — İşletim Sistemi

> **Dizin (index):** bu dosya katman özetini + alt klasör dizinini taşır.
> Makro özet (K0 = K000+K001): `[[os-master]]` · Bağlantı matrisi: `[[../katman-baglilik-matrisi.md]]`

## §1 Kimlik
- Katman: K000 · Alan: **A0** (K0-K5 — Altyapı/Donanım, `.ai/AGENTS.md` §5).
- Kapsam: web platformunun çalışma-zemini: subdomain dizin yerleşimi, PHP 8.4 runtime,
  composer autoload, `shared/` tek PSR-4 package, PDO bağlantı zemini (plan §1, §3).

## §2 Sorumluluk
1. Subdomain barındırma ve giriş noktaları (`index.php`, `autoload.php`, `.htaccess`/`web.config`).
2. `shared/` package'ının sağlanması: `Middleware`, `PageRouter`, `Database`, `Security`, `Session`.
3. Ortam/yapılandırma okuma stratejisi (ADR-015 env parser).
4. Veritabanı zemini: `shared/src/Database/` (DatabaseManager · DatabaseRegistry).
5. İşletim sistemi türü bazında platform ses yollarının dokümante edilmesi (`isletim-sistemi-turu/`).

## §3 Bağlantılar (Dependency Rule)
- **Alt katman (bağımlı):** yok — K000 en alt zemin katmanıdır (plan §2-1).
  İstisna mikro kenar: `K001 → K000` (matris §2).
- **Üst katman (çağıran):** K001–K020 tamamı bu zemine oturur.
- Kural: her katman yalnız alt katmana bağımlı; ihlal → revert + log ERROR (plan §4).

## §4 Alt Klasör Dizini (2026-10-10 disk ölçümü)

| # | Alt klasör | Dosya | Durum |
|---|---|---:|---|
| 1 | `electroncis/` | index.md | ⚠️ yerleşim notu §6-4 — ad değişmez |
| 2 | `isletim-sistemi-api/` | index.md | IMPLEMENTED (zemin) |
| 3 | `isletim-sistemi-core/` | index.md | IMPLEMENTED (zemin) |
| 4 | `isletim-sistemi-cross/` | index.md | IMPLEMENTED (shared/ zemini) |
| 5 | `isletim-sistemi-kernel/` | index.md | IMPLEMENTED (kavram + `PageRouterKernel`) |
| 6 | `isletim-sistemi-plan/` | index.md | PLANNED kayıtları |
| 7 | `isletim-sistemi-security/` | index.md | IMPLEMENTED (bazı) — politika K006'da |
| 8 | `isletim-sistemi-turu/` | 8 md | 6/8 web-doğrulanmış platform + web |

**Okuma sırası:** `os-master` → `isletim-sistemi-core` → `isletim-sistemi-api` → `isletim-sistemi-kernel`
→ `isletim-sistemi-cross` → `isletim-sistemi-security` → `isletim-sistemi-turu/*` → `isletim-sistemi-plan`.

## §5 ADR Bağlantıları
| ADR | Başlık | Durum |
|---|---|---|
| ADR-015 | Env Parser Strategy (Infrastructure) | accepted — `.ai/.decisions/accepted/ADR-015-env-parser-strategy.md` |
| ADR-082 | Dev/Staging Environment Architecture | accepted — dosya VAR |
| ADR-085 | Shared Library Hybrid (tek shared/ + PSR-4 namespace) | accepted — dosya VAR |
| ADR-019 | Per-OS Neva Player (Windows/macOS/Linux — IAudioBackend) | accepted — `turu/` ile ilgili |

## §6 Risk / Not
1. ADR-015 uygulama kanıtı: `shared/src/Config/EnvParser.php` **VAR**; çağıranlar
   `api/auth/home.coremusic.net/config/constants.php` (grep 2026-10-10) — bu, eski "UNKNOWN"
   kaydını **kısmen kapatır**; ADR metni ile birebir satır eşleşmesi okunmadı → ⚠️.
2. `assets.coremusic.net/` altında `composer.json` yok — kasıt **UNKNOWN**.
3. `isletim-sistemi-turu/andorid.md` dosya adında yazım hatası var; dosya boşken
   adlandırıldı — referans (wiki-link) 0 olduğu için **ad değiştirilmedi**, rapor edildi.
4. `electroncis/` alt klasörü K000 (OS) altında; elektronik içeriği K001'e aittir
   (ADR-061 L6) — **taşınmadı** (onaysız taşıma yasak), sınır referansı olarak yazıldı.
