---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security
children: [cekirdek-guvenlik-anayasa, cekirdek-guvenlik-surucu, cekirdek-guvenlik-ornekler]
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-kernel
evidence:
  - https://docs.kernel.org/security/self-protection.html
  - https://docs.kernel.org/admin-guide/LSM/index.html
  - https://github.com/seccomp/libseccomp
  - shared/src/Session/SessionConfig.php:35
  - home.coremusic.net/.htaccess:2
validation:
  - "2026-10-13: 3 web URL HEAD ile HTTP 200; ucu da 2097'lik katalog envanterinde YOK -> kesisim 0."
  - "2026-10-13: 2 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - "S1: OS guvenlik katmani (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? [UNKNOWN]"
group: mimari
---

# cekirdek-guvenlik

> **Kapsam (tek cumle):** Isgucu-cekirdek koruma halkalari — guvenli onyukleme,
> mitigasyonlar (SMEP/SMAP/KASLR), LSM catisi, seccomp filtreleri ve **surucu
> siniri**; repo'da bu yuzeyin kacindan gercek karsiligi oldugu ayri ayri yazili.

> **Oncelikli gercek:** Cekirdek yuzeyine ait `*.c` / `*.cpp` / `*.h` dosyasi
> sayisi **0** (2026-10-13, vendor/node_modules hariç); dolayisiyla bu dal
> **hedef-mimari + negatif kanit** dalidir, calisan kod raporu degildir.

## Dosya indeksi (Kademe-4 — bu klasorun 4 dosyasi)

| Dosya | Ne icerir | group |
|---|---|---|
| [`cekirdek-guvenlik-anayasa.md`](cekirdek-guvenlik-anayasa.md) | Koruma halkalari, onyukleme zinciri, mitigasyon envanteri, LSM securitem | mimari |
| [`cekirdek-guvenlik-surucu.md`](cekirdek-guvenlik-surucu.md) | IOCTL/yukleme mekanizmasi, baslik-sorumluluklari, surucu review kapisi | mimari |
| [`cekirdek-guvenlik-ornekler.md`](cekirdek-guvenlik-ornekler.md) | Repo'da cekirdek-temasli gercek kod (headers, yol dogrulama, kisitlar) | kod |
| `index.md` | Bu dosya — alt-dal kunyesi, 3 cocuk, komsu sinirlari | mimari |

## Alt-dal sorusu

Bu dal tek bir soruyu yanitlar: **"Isgucu-cekirdek katmanindan uygulamaya kadar
ne korur, bu korumanin repo'daki karsiligi nedir?"** Cevap uclusunde bolunur:
anayasa (ne korur) · surucu (nasil zorlanir) · ornekler (repo'da gercekten ne var).

## §GRUPLAR

| Dosya | Kademe | group |
|---|---|---|
| `cekirdek-guvenlik-anayasa` | Kademe-4 | mimari |
| `cekirdek-guvenlik-surucu` | Kademe-4 | mimari |
| `cekirdek-guvenlik-ornekler` | Kademe-4 | kod |

**Toplam:** 1 index (mimari) + 3 icerik (mimari x2 · kod x1) = 4 dosya.

## Komsu sinir matrisi

| Komsu dal | Ne devralir | Devretmez (sinir cumlesi) |
|---|---|---|
| `isletim-sistemi-kernel` | Cekirdek mekanizmasinin **kullanim modeli** | Cekirdek ici yapisi, surucu kodu yazimi, kernel patch politikasi |
| `yetki-ve-erisim-modeli` | Koruma halkasinin **yetki etkisi** (kim izin alir) | Izin modelinin tanimi (DAC/MAC/RBAC/BEL), ACL/umask mekanizmasi |
| `uygulama-guvenlik-siniri` | Sandbox'in **cekecegi syscall yuzeyi** | Uygulama OWASP denetimi, CSRF tasarimi, rate limit politikasi |
| `K009-security` | Enjeksiyon sinirlama etkisi (seccomp/capability) | OWASP denetimi, kripto algoritma secimi (ADR-022) |

> **K009 ayrimi (baglayici):** OWASP Top 10 denetimi, auth/CSRF/CSP tasarimi,
> kripto secimi ve rate limit politikasi -> `K009-security` + ADR-010/013/022.

## Acik soru (kapatilmaz)

| # | Soru | Durum | Kapanis kriteri |
|---|---|---|---|
| S1 | OS guvenlik katmani (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? | `[UNKNOWN]` | Mimari sahibinin yazili karari + ADR (yeni ise `>=088`) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
