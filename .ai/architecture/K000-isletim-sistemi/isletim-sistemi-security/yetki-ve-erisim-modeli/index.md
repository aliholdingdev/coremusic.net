---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security
children: [yetki-ve-erisim-modeli-anayasa, yetki-ve-erisim-modeli-surucu, yetki-ve-erisim-modeli-ornekler]
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security
  - K000-isletim-sistemi/isletim-sistemi-core
evidence:
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html
  - https://man7.org/linux/man-pages/man2/umask.2.html
  - https://man7.org/linux/man-pages/man1/setfacl.1.html
  - shared/src/Session/SessionInitializer.php:31
  - shared/src/Middleware/PermissionMiddleware.php:31
  - home.coremusic.net/.htaccess:18
validation:
  - "2026-10-13: 3 web URL HEAD ile HTTP 200; ucu da 2097'lik katalog envanterinde YOK -> kesisim 0."
  - "2026-10-13: 3 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - "S2: Oturum dizini izni neden tek degere (muhtemelen 0750) indirgenmiyor? [UNKNOWN]"
group: mimari
---

# yetki-ve-erisim-modeli

> **Kapsam (tek cumle):** "Kim ne yapabilir" sorusunun dort katmani (DAC, MAC,
> RBAC, BEL) ve bu katmanlarin bu repo'daki **gercek karsiliklari** — dizin
> izni, iki RBAC middleware'i, `.htaccess` erisim kapilari, production kilidi.

> **Oncelikli gercek:** RBAC katmani **tam calisiyor** (iki ayri uygulama,
> ikisi de strict + fail-closed); DAC **kismen calisiyor** (mkdir modu var,
> `umask` yok); MAC ve BEL **etkin degil** (0 kullanim). En acik bulgu:
> oturum dizini **iki farkli modla** olusturuluyor (`0777` vs `0750`).

## Dosya indeksi (Kademe-4 — bu klasorun 4 dosyasi)

| Dosya | Ne icerir | group |
|---|---|---|
| [`yetki-ve-erisim-modeli-anayasa.md`](yetki-ve-erisim-modeli-anayasa.md) | DAC/MAC/RBAC/BEL tanimlari, POSIX bit, ACL, capability, LSM | mimari |
| [`yetki-ve-erisim-modeli-surucu.md`](yetki-ve-erisim-modeli-surucu.md) | Denetim zinciri (syscall->VFS->DAC->ACL->MAC), umask, audit | mimari |
| [`yetki-ve-erisim-modeli-ornekler.md`](yetki-ve-erisim-modeli-ornekler.md) | 23 gercek kod ornegi (3 tuzak dahil), hepsi Select-Object ile | kod |
| `index.md` | Bu dosya — alt-dal kunyesi, 3 cocuk, komsu sinirlari | mimari |

## Alt-dal sorusu

Bu dal tek bir soruyu yanitlar: **"Erisim karari kim tarafindan, hangi sirayla,
hangi kanitla veriliyor?"** Cevap uclusunde bolunur: anayasa (dort katman
**ne**dir) · surucu (karar **nasil** verilir) · ornekler (bu repo'da **gercekte**
ne var).

## §GRUPLAR

| Dosya | Kademe | group |
|---|---|---|
| `yetki-ve-erisim-modeli-anayasa` | Kademe-4 | mimari |
| `yetki-ve-erisim-modeli-surucu` | Kademe-4 | mimari |
| `yetki-ve-erisim-modeli-ornekler` | Kademe-4 | kod |

**Toplam:** 1 index (mimari) + 3 icerik (mimari x2 · kod x1) = 4 dosya.

## Komsu sinir matrisi

| Komsu dal | Ne devralir | Devretmez (sinir cumlesi) |
|---|---|---|
| `isletim-sistemi-core` | Izin **denetim akisi** (DAC/MAC/ACL -> allow/deny) | Izin kavraminin tanimi, dosya modeli kararlari |
| `cekirdek-guvenlik` | Koruma halkasinin **yetki etkisi** (kim izin alir) | Koruma halkasinin kendisi (mitigasyon, LSM catisi, surucu) |
| `uygulama-guvenlik-siniri` | RBAC'in **syscall yuzeyine** indigi yer | Sandbox kurulumu, politika yazimi, syscall sinirlama |
| `K010-identitiy` | Oturum dosyasinin **OS kisiti** (dizin `0750`) | Oturum is mantigi, login, session fixation, MFA akisi |
| `K009-security` | Yetki modelinin OWASP'a etkisi | OWASP denetimi, auth/CSRF tasarimi (ADR-010) |

> **K010 ayrimi (baglayici):** kimlik dogrulama, oturum, sifre ve MFA ->
> `K010-identitiy`. Bu dal **yalnizca** "kimlik dogrulandi SONRA ne yapabilir"
> sorusunu tasir.
> **K009 ayrimi (baglayici):** OWASP Top 10 denetimi, auth/CSRF/CSP tasarimi,
> kripto secimi ve rate limit politikasi -> `K009-security` + ADR-010/013/022.

## Kanit baglantisi

| Bulgu | Kanit tipi | Nerede |
|---|---|---|
| `0777` vs `0750` kontrasti | disk (2 kod satiri) | `-ornekler.md` §1 |
| iki RBAC, ikisi de strict | disk (4 kod satiri) | `-ornekler.md` §2-§3 |
| `.htaccess` erisim kapilari | disk (5 kod satiri) | `-ornekler.md` §5 |
| ACL/capability/LSM 0 kullanim | negatif kanit | `-dogrulama.md` D-01..D-43 |
| 8 URL HTTP 200 + envanter disi | web (HEAD) | `-dogrulama.md` §1.2 U-havuzu |

## Acik soru (kapatilmaz)

| # | Soru | Durum | Kapanis kriteri |
|---|---|---|---|
| S2 | Oturum dizini izni neden tek degere (muhtemelen `0750`) indirgenmiyor? | `[UNKNOWN]` | Mimari sahibinin yazili karari; kod degisikligi bu vault'ta YAPILMAZ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
