---
node_id: K000-isletim-sistemi/isletim-sistemi-security/index
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: [cekirdek-guvenlik, yetki-ve-erisim-modeli, uygulama-guvenlik-siniri]
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-kernel
  - K000-isletim-sistemi/isletim-sistemi-core
evidence:
  - https://man7.org/linux/man-pages/man2/capget.2.html
  - https://github.com/seccomp/libseccomp
  - https://docs.kernel.org/admin-guide/LSM/index.html
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
  - shared/src/Session/SessionConfig.php:35
  - shared/src/Middleware/PermissionMiddleware.php:31
validation:
  - "2026-10-13: 3 web URL HEAD ile HTTP 200; ucu da 2097'lik katalog envanterinde YOK -> kesisim 0."
  - "2026-10-13: 3 disk kaniti Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - "S1: OS guvenlik katmani (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri (kim hangini uygular)? [UNKNOWN]"
group: mimari
---

# isletim-sistemi-security

> **Kapsam (tek cumle):** CoreMusic'te **iki guvenlik katmaninin** ayrimi — OS duzeyi guvenlik (capabilities, seccomp, LSM) ile repo'da fiilen bulunan **uygulama katmani** guvenlik pratiigi (`realpath` yol dogrulama, `escapeshellarg`, `.htaccess` basliklari, `mkdir 0750`).

> **Oncelikli gercek:** OS guvenlik araclina dair repo'da **0 kullanim** (2026-10-13 negatif kanit, tekrar olcum); uygulama katmaninda somut kanit var. OS tarafi **hedef mimari referansidir**, mevcut durum degildir.

## Dosya indeksi (Kademe-2 — bu klasorun 4 dosyasi)

| Dosya | Ne icerir | group |
|---|---|---|
| [`isletim-sistemi-security-kapsam.md`](isletim-sistemi-security-kapsam.md) | Kapsam siniri, 12 saldiri sinifinda katman ayrim matrisi, S1/S2 | mimari |
| [`isletim-sistemi-security-dogrulama.md`](isletim-sistemi-security-dogrulama.md) | 68 URL (HTTP 200) + 43 disk kaniti + negatif kanit defteri | kanit |
| [`isletim-sistemi-security-ornekler.md`](isletim-sistemi-security-ornekler.md) | 18 gercek repo kod ornegi (her biri `Select-Object -Index` ile) | kod |
| `index.md` | Bu dosya — klasor kunyesi, 3 cocuk, komsu sinirlari | mimari |

## Alt-dal indeksi (Kademe-3 — 3 alt-dal x 12 dosya)

| Alt-dal | Cocuk dosyalar (Kademe-4) | Ana soru | group |
|---|---|---|---|
| [`cekirdek-guvenlik/`](cekirdek-guvenlik/index.md) | `-anayasa` · `-surucu` · `-ornekler` + 8 OS kartı (`-windows` · `-linux` · `-macos` · `-ios` · `-android` · `-tizen` · `-webos` · `-reactos`) | Koruma halkalari, guvenli onyukleme, mitigasyon, surucu siniri | mimari |
| [`yetki-ve-erisim-modeli/`](yetki-ve-erisim-modeli/index.md) | `-anayasa` · `-surucu` · `-ornekler` + 8 OS kartı (aynı `-os` deseni) | DAC/MAC/RBAC/BEL, ACL, capability, denetim akisi | mimari |
| [`uygulama-guvenlik-siniri/`](uygulama-guvenlik-siniri/index.md) | `-anayasa` · `-surucu` · `-ornekler` + 8 OS kartı (aynı `-os` deseni) | Syscall yuzeyi, sandbox turleri, konteyner kisitlari | mimari |

## §GRUPLAR

| Alt-dal | Kademe | Dosya sayisi | group dagilimi |
|---|---|---|---|
| `cekirdek-guvenlik` | Kademe-3 | 12 (1 index + 3 icerik + 8 OS kartı) | mimari x3 · kod x9 |
| `yetki-ve-erisim-modeli` | Kademe-3 | 12 (1 index + 3 icerik + 8 OS kartı) | mimari x3 · kod x9 |
| `uygulama-guvenlik-siniri` | Kademe-3 | 12 (1 index + 3 icerik + 8 OS kartı) | mimari x3 · kod x9 |

**Toplam:** 4 kok + 36 alt-dal = **40 dosya** (16 temel + 24 OS kartı, 2026-10-14) · icerik esigi >=500 satir · kart 150–260 satir · `index.md` 40–100 satir.

## OS kartlari (8 platform x 3 alt-dal = 24 kart · 2026-10-14)

| OS | Kart deseni (`<alt-dal>/<alt-dal>-<os>.md`) | group |
|---|---|---|
| Windows | `cekirdek-guvenlik-windows` · `yetki-ve-erisim-modeli-windows` · `uygulama-guvenlik-siniri-windows` | kod |
| Linux | `cekirdek-guvenlik-linux` · `yetki-ve-erisim-modeli-linux` · `uygulama-guvenlik-siniri-linux` | kod |
| macOS | `cekirdek-guvenlik-macos` · `yetki-ve-erisim-modeli-macos` · `uygulama-guvenlik-siniri-macos` | kod |
| iOS | `cekirdek-guvenlik-ios` · `yetki-ve-erisim-modeli-ios` · `uygulama-guvenlik-siniri-ios` | kod |
| Android | `cekirdek-guvenlik-android` · `yetki-ve-erisim-modeli-android` · `uygulama-guvenlik-siniri-android` | kod |
| Tizen | `cekirdek-guvenlik-tizen` · `yetki-ve-erisim-modeli-tizen` · `uygulama-guvenlik-siniri-tizen` | kod |
| webOS | `cekirdek-guvenlik-webos` · `yetki-ve-erisim-modeli-webos` · `uygulama-guvenlik-siniri-webos` | kod |
| ReactOS | `cekirdek-guvenlik-reactos` · `yetki-ve-erisim-modeli-reactos` · `uygulama-guvenlik-siniri-reactos` | kod |

> Her kart sabit sablonda: §0 ELI15 · §1 OS guvenlik profili · §2 kod ornegi
> (`kaynak: upstream docs` imzali; "repo'da uygulaniyor" yazmak YASAK) · §3 repo
> durumu (hedef referans) · §4 sinir cumleleri (K010/K009/komsu dal) · §5 referans ·
> §6 acik sorular (S1/S2 kapatilmaz). Tum OS kartlari `group: kod` — Kademe-4.

## Komsu sinir matrisi (her alt-dal icin zorunlu)

| Alt-dal | Komsu dal | Ne devralir | Devretmez (sinir cumlesi) |
|---|---|---|---|
| `cekirdek-guvenlik` | `isletim-sistemi-kernel` | Cekirdek mekanizmasinin **kullanim modeli** | Cekirdek ici yapisi, surucu kodu yazimi, kernel patch politikasi |
| `cekirdek-guvenlik` | `K009-security` | Enjeksiyon sinirlama etkisi (seccomp/capability) | OWASP denetimi, kripto algoritma secimi (ADR-022) |
| `yetki-ve-erisim-modeli` | `K010-identitiy` | Oturum dosyasinin **OS kisiti** (dizin `0750`, TTY sahipligi) | Oturum is mantigi, login, session fixation, MFA akisi |
| `yetki-ve-erisim-modeli` | `isletim-sistemi-core` | Izin **denetim akisi** (DAC/MAC/ACL -> allow/deny) | Izin kavraminin tanimi, dosya modeli kararlari |
| `uygulama-guvenlik-siniri` | `K009-security` | Uygulama <-> OS **sinir isaretleme** | Uygulama OWASP denetimi, CSRF tasarimi, rate limit politikasi |
| `uygulama-guvenlik-siniri` | `isletim-sistemi-runtime` | Sandbox **calisma zamani modeli** | Sandbox kurulumu, politika yazimi |

> **K010 ayrimi (baglayici):** Bu dal **OS duzeyi guvenlik + uygulama-OS sinirini** kapsar; kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`.
> **K009 ayrimi (baglayici):** OWASP Top 10 denetimi, auth/CSRF/CSP tasarimi, kripto secimi ve rate limit politikasi -> `K009-security` + ADR-010/013/022.

## Acik sorular (kapatilmaz)

| # | Soru | Durum | Kapanis kriteri |
|---|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | `[UNKNOWN]` | Mimari sahibinin yazili karari + ADR (yeni ise `>=088`) |
| S2 | Uygulama <-> OS sorumluluk siniri (kim hangini uygular)? | `[UNKNOWN]` | Sinir matrisi onayi (`isletim-sistemi-security-kapsam.md` §3.1) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
