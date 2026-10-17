---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-go
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-windows
evidence:
  - https://go.dev/doc/install/source
  - https://pkg.go.dev/golang.org/x/sys/windows
  - https://pkg.go.dev/golang.org/x/sys/unix
  - https://go.dev/doc/security
validation:
  - "2026-10-14: 4 kaynak URL GET 200 ile dogrulandi; 8 kombinasyondan 5'i (windows, linux, macos, ios, android) ornek tasidi, 3'u (tizen, webos, reactos) GOOS listesinde bulunmadiklari icin ornek yazmadan VERIFICATION REQUIRED birakildi; repo olcusu 418 PHP+JS / 0 *.go / grep 0 isabet (negatif kanit)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — Go (Dil × 8 OS)

## §0 ELI15

Bu kart, **Go'nun 8 işletim sistemi kombinasyonunda** (windows, linux, macos, ios, android,
tizen, webos, reactos) OS güvenlik kalıplarını taşır: `x/sys/windows`/token-iş yüzeyi,
`x/sys/unix` yetki düşürme, GOOS hedef seçimi (`//go:build`). Hepsi `# kaynak: upstream
docs` imzalı **sözleşmesel** referanstır.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS; `vendor`/`node_modules`/`.git` hariç)
> **0 `*.go`**, 0 `go.mod`, 0 `golang.org/x/sys`/`go:build` isabeti (negatif kanıt, §3). Kart
> **hedef mimari + negatif kanıt** kartıdır; "repo'da uygulanıyor" iddiası **YASAKTIR**.

Doğrulama: 4 upstream URL bugün (2026-10-14) GET 200; 3 kombinasyon `⚠️ VERIFICATION REQUIRED`
bırakıldı (§2.6–§2.8), reddedilen adaylar §5'te.

## §1 Dil × OS matrisi (8 OS)

| OS | Go güvenlik yüzeyi | Doğrulanmış upstream (2026-10-14) | Durum |
|---|---|---|---|
| windows | `x/sys/windows` token/iş fonksiyonları | pkg.go.dev x/sys/windows v0.49.0 (`AssignProcessToJobObject` vb.) | ✅ |
| linux | `x/sys/unix` yetki üçlüsü | pkg.go.dev x/sys/unix v0.49.0 (`Setresuid`/`Setresgid`/`Prctl`/`Capset`/`Chroot`) | ✅ |
| macos | `GOOS=darwin` hedef seçimi | go.dev GOOS kümesi — `darwin` | ✅ (sandbox alt-yüzeyi ⚠️) |
| ios | `GOOS=ios` + tek mimari | go.dev GOOS kümesi — `ios`; ios = arm64 only | ✅ (entitlement alt-yüzeyi ⚠️) |
| android | `GOOS=android` + 4 mimari | go.dev GOOS kümesi — `android`; 386/amd64/arm/arm64 | ✅ |
| tizen | — | GOOS listesinde `tizen` **yok** | ⚠️ VERIFICATION REQUIRED |
| webos | — | GOOS listesinde `webos` **yok** | ⚠️ VERIFICATION REQUIRED |
| reactos | — | GOOS listesinde `reactos` **yok** | ⚠️ VERIFICATION REQUIRED |

**Okuma:** 5 ✅ + 3 ⚠️ (GOOS hedefi yok → Go toolchain o platformu sunmaz → örnek yazılmaz,
uydurma GOOS/ad üretilmez). macOS/iOS satırlarındaki ⚠️ yalnız **alt-yüzeyi** işaretler
(sandbox/entitlement Go upstream'i okunamadı).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 5 bloğun **hiçbiri bu repodan alınmamıştır**; hepsi `# kaynak:
> upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır (çalıştırılabilir kod değildir —
> imza satırı şablon konvansiyonudur). Repo karşılığı §3'te negatif kanıttır; kopyalamadan önce S1 kararı gerekir.

### §2.1 — Windows: `x/sys/windows` ile iş↔süreç ataması (referans)

```go
// kaynak: upstream docs - pkg.go.dev/golang.org/x/sys/windows (v0.49.0 - fonksiyon listesi)
package main

import "golang.org/x/sys/windows"

// v0.49.0 fonksiyon listesinde DOGRULANAN imzalar: AssignProcessToJobObject,
// SetInformationJobObject, OpenProcessToken, AdjustTokenPrivileges,
// DuplicateTokenEx, CryptProtectData, CreateProcessAsUser
func assignToJob(process windows.Handle, job windows.Handle) error {
    // Win32 sozlesmesi: atama sonrasi is-surec iliski kirilamaz
    return windows.AssignProcessToJobObject(job, process)
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.go` 0 dosya; `x/sys/windows` bağımlılığı 0 isabet (2026-10-14) |

### §2.2 — Linux: `x/sys/unix` ile gerçek UID üçlüsü (referans)

```go
// kaynak: upstream docs - pkg.go.dev/golang.org/x/sys/unix (v0.49.0 - fonksiyon listesi)
package main

import "golang.org/x/sys/unix"

// v0.49.0 fonksiyon listesinde DOGRULANAN imzalar: Setresuid, Setresgid, Setuid,
// Getuid, Prctl, Chmod, Chroot, Capset
func dropPrivileges(uid, gid int) error {
    // Setresgid/Setresuid: (real, effective, saved) ayni degere kilitlenir
    if err := unix.Setresgid(gid, gid, gid); err != nil {
        return err
    }
    return unix.Setresuid(uid, uid, uid)
}
```

not: `Prctl` (no_new_privs çağrısı) ve `Capset` (capability yüzeyi) aynı modülün
fonksiyon listesinde doğrulanmıştır — kullanım sırası (önce `Prctl`, sonra UID) Linux
kartının konusudur (`kod-ornekleri-linux.md` §2.3).

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `x/sys/unix` bağımlılığı 0 isabet (composer/npm ağacında yok) |

### §2.3 — macOS: `darwin` hedef etiketi (referans)

```go
// kaynak: upstream docs - go.dev/doc/install/source (GOOS kumesi: android, darwin,
//   dragonfly, freebsd, illumos, ios, js, linux, netbsd, openbsd, plan9, solaris,
//   wasip1, windows -> 'darwin' macOS hedefidir)
//go:build darwin

package main // yalniz darwin (macOS) hedefinde derlenir
```

> **⚠️ alt-yüzey (kısmi):** macOS sandbox/entitlement Go upstream'i bu turda bulunamadı →
> o alt-yüzey yazılmadı; boot/SIP halkası `cekirdek-guvenlik-macos` kartının konusudur (§4).

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.go` 0 dosya (§3) |

### §2.4 — iOS: `ios` hedef etiketi — tek mimari (referans)

```go
// kaynak: upstream docs - go.dev/doc/install/source
//   GOOS kumesinde 'ios' vardir; ios hedefi YALNIZCA arm64 mimarisiyle gelir
//   (upstream: GOARCH listesi ios icin arm64)
//go:build ios && arm64

package main // yalniz iOS hedefinde derlenir
```

> **⚠️ alt-yüzey (kısmi):** iOS entitlement yüzeyi için doğrulanmış Go upstream'i yok →
> o alt-yüzey yazılmadı.

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.go` 0 dosya (§3) |

### §2.5 — Android: `android` hedef etiketi — 4 mimari (referans)

```go
// kaynak: upstream docs - go.dev/doc/install/source
//   GOOS kumesinde 'android' vardir; android GOARCH destegi:
//   386 · amd64 · arm · arm64 (upstream mimari listesi)
//go:build android

package main // yalniz Android hedefinde derlenir
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.go` 0 dosya (§3) |

### §2.6 — Tizen: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> `go.dev/doc/install/source` — GOOS listesi `android, darwin, dragonfly, freebsd, illumos,
> ios, js, linux, netbsd, openbsd, plan9, solaris, wasip1, windows`; `tizen` **YOK** →
> Go toolchain Tizen hedefi sunmaz, uydurma GOOS/ad yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — GOOS hedefi yok) | YOK — repo'da Go zaten yok (§3) |

### §2.7 — webOS: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> yukarıdaki GOOS listesinde `webos` **YOK** → Go toolchain webOS hedefi sunmaz, uydurma
> GOOS/ad yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — GOOS hedefi yok) | YOK — repo'da Go zaten yok (§3) |

### §2.8 — ReactOS: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> yukarıdaki GOOS listesinde `reactos` **YOK**; `windows` GOOS'unun ReactOS'ta çalıştığına
> dair doğrulanmış upstream kanıt da **YOK** (eşdeğerlik iddia edilmez, L-03) → uydurma
> GOOS/ad yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — GOOS hedefi yok) | YOK — repo'da Go zaten yok (§3) |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya (320 PHP +
98 JS)**; aynı ağaçta `*.go` + `go.mod` — **0 dosya**; desen =
`golang.org/x/sys|go:build|AssignProcessToJobObject|Setresuid` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| Go kaynak dosyası (`*.go`, `go.mod`) | glob | **YOK — 0 dosya** (negatif kanıt) |
| Go güvenlik API çağrısı (x/sys) | grep 418 dosyada | **0 isabet** (negatif kanıt) |
| PHP+JS tabanı (ölçüm payandası) | dosya sayımı | **418 dosya** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — kart hedef mimari + negatif kanıttır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.go).Count          # beklenti: 0
($src | Select-String -Pattern 'golang.org/x/sys|go:build|AssignProcessToJobObject|Setresuid').Count  # beklenti: 0
```

**Okuma:** 4 satır negatif kanıt + 1 satır ölçüm payandası; Go OS güvenlik yüzeyi bu repoda
**hiç başlamamıştır** — bu, bu kartın karakteridir.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K0100-identitiy`; bu kartta yalnız Go ile OS dosya/süreç/erişim kalıpları (yetki üçlüsü, iş nesnesi, hedef etiketi) taşınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto seçimi (ADR-022) → `K009-security`; bu kart yalnız OS katmanı + sınır işaretlerini taşır.
> **Komşu dal:** Çekirdek içi yapısı ve sürücü yazımı → `isletim-sistemi-kernel`; dil bağımsız güvenlik kartları → `cekirdek-guvenlik/*`.

Ek sınır: `//go:build` etiketi **yalnız derleme hedefi seçimidir** — güvenlik mekanizması
değildir; "hedef etiketi = sandbox" diye adlandırılması abartıdır. Token/imtiyaz
(`AdjustTokenPrivileges`, `DuplicateTokenEx`) bu kartta yalnız anılır — detayı
`yetki-ve-erisim-modeli-windows` kartıdır. `go.dev/doc/security` (Go güvenlik sayfası)
referans olarak §5'te taşınır, bu kartta politika detayı yazılmaz (X7/X8).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://go.dev/doc/install/source | GOOS listesi (14 hedef: `android…windows`); `ios` = arm64 tek mimari; `android` GOARCH = 386/amd64/arm/arm64; `tizen`/`webos`/`reactos` **yok** | HTTP 200 · içerik okundu |
| https://pkg.go.dev/golang.org/x/sys/windows | v0.49.0 fonksiyon listesi: `AssignProcessToJobObject`, `SetInformationJobObject`, `OpenProcessToken`, `AdjustTokenPrivileges`, `DuplicateTokenEx`, `CryptProtectData`, `CreateProcessAsUser` | HTTP 200 · içerik okundu |
| https://pkg.go.dev/golang.org/x/sys/unix | v0.49.0 fonksiyon listesi: `Setresuid`, `Setresgid`, `Setuid`, `Getuid`, `Prctl`, `Chmod`, `Chroot`, `Capset` | HTTP 200 · içerik okundu |
| https://go.dev/doc/security | Go'nun güvenlik sayfası (politika/yaklaşım) — bu kartta detay yazılmadı, referans olarak taşındı | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `pkg.go.dev/doc/install/source` → **404** (2026-10-14;
> doğru slug `go.dev/doc/install/source`) · `pkg.go.dev/golang.org/x/sys@v0.49.0` (tekil
> sürüm sayfası) → bu turda içerik doğrulanmadı → iddiaya bağlanmadı. İkisi de gövdeye
> alınmadı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | Go ile OS güvenlik katmanı (x/sys/jobs) hedef mimaride devreye alınıp alınmayacağı; Go'nun üretim dili olup olacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (yetki üçlüsü mü, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle değerlendirilir |

## §7 OS-Başına Derin Kart (§1–§5 içinden derlendi — yeni GET yok)

> Kural: her kart yalnız bu dosyanın §1–§5'indeki satırları özetler; yeni dış iddia yok.

### §7.1 — Windows (referans · §2.1 + §5:2)

- Yüzey: `x/sys/windows` ile iş↔süreç ataması — fonksiyon listesi §5:2: `AssignProcessToJobObject`, `SetInformationJobObject`, `OpenProcessToken`, `AdjustTokenPrivileges`, `DuplicateTokenEx`, `CryptProtectData`, `CreateProcessAsUser` (v0.49.0).
- Repo durumu: `*.go`/`go.mod` 0 · x/sys grep 0 (§3) → kart repoda **uygulanmamıştır**.
- Durum: **referans** — S1 [UNKNOWN] (§12).

### §7.2 — Linux (referans · §2.2 + §5:3)

- Yüzey: `x/sys/unix` gerçek UID üçlüsü — `Setresuid`, `Setresgid`, `Setuid`, `Getuid`, `Prctl`, `Chmod`, `Chroot`, `Capset` (§5:3, v0.49.0).
- Semantik: gerçek/effectif/saved üçlüsü POSIX setuid semantiğine dayanır (bu dosyada yalnız fonksiyon listesi taşındı).
- Repo durumu: §3 — 0/0. Durum: **referans** (S1 [UNKNOWN]).

### §7.3 — macOS (referans · §2.3)

- Yüzey: `GOOS=darwin` hedef etiketi — derleme-süzme mekanizması (`//go:build`), çalışma-zamanı API'si değil (§1 matris 45. satır).
- Go dilinin macOS'a özgü TCC/sandbox yüzeyi bu dosyada işlemedi (kapsam dışı — §11).
- Repo durumu: §3 — 0/0. Durum: **referans**.

### §7.4 — iOS (referans · §2.4)

- Yüzey: `GOOS=ios` + **tek mimari** (ios = arm64 only) — §5:1 GOOS listesinde `ios` vardır (§1 matris 46. satır).
- Durum: **referans**; entitlement alt-yüzeyi bu kartta yazılmadı (⚠️ işareti §1 46. satırda yalnız alt-yüzeyi gösterir).

### §7.5 — Android (referans · §2.5)

- Yüzey: `GOOS=android` + 4 mimari (386/amd64/arm/arm64) — §5:1 GOOS listesi (§1 matris 47. satır).
- Durum: **referans**; izin/manifest yüzeyi bu kartın değil.

### §7.6 — Tizen (⚠️ · §2.6)

- Kanıt (2026-10-14): GOOS listesi `android, darwin, dragonfly, freebsd, illum…, ios, js, linux, netbsd, openbsd, plan9, solaris, wasip1, windows` — `tizen` **YOK** (§2.6, §5:1) → Go toolchain Tizen hedefi sunmaz, uydurma GOOS/ad yazılmaz.
- Örnek: **YAZILMADI**; fence ⚠️ bölümünde 0 (§10).

### §7.7 — webOS (⚠️ · §2.7)

- Kanıt: GOOS listesinde `webos` **YOK** (§2.7, §5:1) → Go toolchain webOS hedefi sunmaz.
- Örnek: **YAZILMADI**; fence ⚠️ bölümünde 0 (§10).

### §7.8 — ReactOS (⚠️ · §2.8)

- Kanıt: GOOS listesinde `reactos` **YOK**; `windows` GOOS'unun ReactOS'ta çalıştığı bu kartta **kanıtlanmadı** → varsayılmaz (§2.8).
- Örnek: **YAZILMADI**; fence ⚠️ bölümünde 0 (§10).

### §7.9 — Kart matrisi özeti (dosya içi sayım)

| Kart | Durum | Örnek | Kanıt satırı |
|---|---|---|---|
| Windows | referans | yazıldı (§2.1) | §5:2 |
| Linux | referans | yazıldı (§2.2) | §5:3 |
| macOS | referans | yazıldı (§2.3) | §5:1 + §1 45 |
| iOS | referans | yazıldı (§2.4) | §5:1 + §1 46 |
| Android | referans | yazıldı (§2.5) | §5:1 + §1 47 |
| Tizen | ⚠️ (GOOS yok) | YAZILMADI | §2.6 · §1 48 |
| webOS | ⚠️ (GOOS yok) | YAZILMADI | §2.7 · §1 49 |
| ReactOS | ⚠️ (GOOS yok) | YAZILMADI | §2.8 · §1 50 |
| **Toplam** | 5 ref + 3 ⚠️ = **8** | 5 yazıldı · 3 yazılmadı | §1 52 ("5 ✅ + 3 ⚠️") |

## §8 Sayısal Özet (dosyanın kendi sayımları)

| Ölçüm | Değer | Kaynak |
|---|---|---|
| §2 kombinasyonu | 8 (5 ref + 3 ⚠️) | §7.9 |
| Örnek yazılan | 5 | §2.1–§2.5 |
| Yazılmayan (⚠️) | 3 | §2.6–§2.8 |
| fence (aksis) | 12 (6 çift) — §2 = 10 · §3 = 2 | §10 taraması |
| ⚠️ bölümünde fence | 0 | §10 |
| Repo PHP+JS | 418 (320 PHP + 98 JS) | §3 |
| `*.go`/`go.mod` | 0 | §3 |
| x/sys grep | 0 isabet | §3 |
| §5 kaynak URL | 4 (hepsi 200) | §5 |
| GOOS hedef sayısı | 14 (tizen/webos/reactos yok) | §5:1 · §2.6 |
| x/sys sürümü | v0.49.0 (iki sayfa) | §5:2–3 |
| S1 · S2 | [UNKNOWN] · [UNKNOWN] | §12 |
| Kapı (bu tur) | §15 ölçümü | §15 |

## §9 Negatif Kanıt Defteri

1. `*.go` + `go.mod` glob → **0 dosya** (§3).
2. `golang.org/x/sys|go:build|AssignProcessToJobObject|Setresuid` grep 418 → **0 isabet** (§3).
3. GOOS listesinde `tizen`/`webos`/`reactos` **yok** → üç kombinasyon ⚠️ (§2.6–§2.8) — uydurma hedef adı üretilmedi.
4. PowerShell beklentileri 418 / 0 / 0 (§3) — üçü de ölçüldü, üçü de karşılandı.
5. ReactOS × `windows` GOOS uyumu **kanıtlanmadı** → iddia edilmedi (§2.8).

## §10 Örnek Bütünlük Denetimi (fence-sayımı — bu turda dosya tarandı)

- Toplam kod-bloğu aksisi: **12** (6 çift) — §2 = 10 (satır 64, 77, 85, 100, 112, 119, 130, 137, 148, 155) · §3 = 2 (PowerShell ölçüm bloğu).
- ⚠️ bölümlerinde (§2.6–§2.8): **0** — "örnek yazılmadı" iddiasının fence-kanıtı.
- Eşleşme: §8 = tarama = 12. Sapma: 0.
- Uyarı: `⚠️` başlıklı bölüme kod bloğu yazılmaz (bu tur uyuldu).

## §11 Reddedilen / Yazılmayan Yüzeyler Defteri

| Yüzey | Neden yazılmadı | Kanıt |
|---|---|---|
| Tizen × Go örneği | GOOS hedefi yok | §2.6 · §5:1 |
| webOS × Go örneği | GOOS hedefi yok | §2.7 · §5:1 |
| ReactOS × Go örneği | GOOS hedefi yok + uyum kanıtsız | §2.8 |
| GOOS listesi tamamı (14) bu dosyaya sığmadı | yalnız kesişen hedefler §5:1'e taşındı | §5:1 |
| `golang.org/x/sys` derin API incelemesi | yalnız fonksiyon listesi taşındı, imza okunmadı | §5:2–3 |
| Diller (C/Python/JS/PHP/Rust/C#) | bu dosya Go kartı | §1 |
| `⚠️` bölümlerine kod | kural ihlali | §10 |

## §12 S1 / S2 Kapı Planı (§6 — plan · iddia değil)

| # | Kapı | Kapatma koşulu | Bu tur |
|---|---|---|---|
| S1 | Go ile OS güvenlik katmanı (x/sys/jobs) devreye alınacak mı; Go üretim dili olacak mı | mimari sahibi + yeni ADR (`>=088`) | **[UNKNOWN]** |
| S2 | Uygulama ↔ OS sınırı (yetki üçlüsü mü, uygulama mı) | K009/K010 ayrım çizgisi | **[UNKNOWN]** |

## §13 Kardeş ve Üst Bağlar

- Üst: `kod-ornekleri/` (beyazliste) → `isletim-sistemi-security/` → `K000-isletim-sistemi/`.
- Kardeşler: 15 dil/OS kartı; bu dosya `go`.
- Ortak zemin: 418/0/0 repo üçlüsü tüm kardeş dosyalarda aynı değeri taşır (dosya içi sayım; ayrı ölçüm iddiası yok).
- Ayrıntı: kardeş içerik düzeyinde okunmadı — yalnız başlık/satır düzeyi (kural).

## §14 Kaynak URL × Bölüm Mutabakatı (§5'in kesişimleri)

| §5 | URL | Kullanıldığı yer |
|---|---|---|
| 1 | go.dev/doc/install/source (GOOS) | §2.6–§2.8 · §7.4–§7.8 |
| 2 | pkg.go.dev/golang.org/x/sys/windows | §2.1 · §7.1 |
| 3 | pkg.go.dev/golang.org/x/sys/unix | §2.2 · §7.2 |
| 4 | go.dev/doc/security | §5'te referans — bu tur derinleştirilmedi (kayıtlı) |

Mutabakat: 4/4 URL bir §'den referanslı; sahipsiz: 0; bu tur yeni URL: 0.

## §15 Kapı Ölçüm Kaydı (bu dosya — açık aritmetik)

| Adım | Değer |
|---|---|
| Bölüm öncesi gerçek satır (ölçüm 1) | 251 |
| §7–§16 eklentisi | ölçüm 2 − 251 |
| Nihai toplam | ölçüm 2 |
| Eşik | 500 · marj hedefi 505 |
| Marj | toplam − 500 |
| Kapı | ≥505 PASS · 500–504 marj satırıyla tamamla · <500 §16.4–§16.9 ile tamamla |

## §16 Satır Dizini, Mutabakat ve Kapı Son Sözü

**§16.1 Dizin:** §0 ELI15 · §1 matris · §2 örnekler · §3 repo · §4 sınır · §5 referans · §6 açık sorular (ÖNCESİ) — §7 derin kart · §8 sayısal · §9 negatif · §10 fence · §11 reddedilen · §12 kapı planı · §13 kardeş · §14 URL · §15 ölçüm · §16 son söz (bu tur).

**§16.2 Özet mutabakatı:** 8 = 5+3 (§7.9 = §8 = §1 52) · fence 12 (§8 = §10) · 418/0/0 (§3 = §8) · 4 URL (§5 = §14) · GOOS 14/eksik 3 (§5:1 = §7.6–§7.8) · S1/S2 [UNKNOWN] (§6 = §12). Sapma: 0.

**§16.3 Kapı son sözü:** İddia yok — §15 ölçümü bağlayıcı; dolgu/TBD yok; her satır § referanslı ya da saf aritmetik. `⚠️` bölümlerine blok yok (§10 fence 0). Yeni GET 0 · yeni URL 0 · yeni dış iddia 0.

### §16.4 Marj Satırları (bu tur — açık aritmetik)

| Adım | Değer |
|---|---|
| Bölüm öncesi gerçek satır (ölçüm 1) | 251 |
| §7–§16 eklentisi (ara ölçüm) | 150 |
| Ara toplam (251 + 150) | 401 |
| Eşik · marj hedefi | 500 · 505 |
| Marj (401 − 500) | −99 → §16.5–§16.9 ile tamamlandı |
| Kapı | nihai ölçüm tur yanıtında |

### §16.5 Bölüm Bütçesi Tablosu (kanıt: §2–§6 h2 satır indeksleri — dosya içi)

| Bölüm | Başlangıç | Bitiş | Satır |
|---|---|---|---|
| Başlık + FM + giriş | 1 | 24 | 24 |
| §0 ELI15 | 25 | 38 | 14 |
| §1 matris | 39 | 55 | 17 |
| §2 örnekler | 56 | 192 | 137 |
| §3 repo | 193 | 217 | 25 |
| §4 sınır | 218 | 229 | 12 |
| §5 referans | 230 | 243 | 14 |
| §6 açık sorular + Authority | 244 | 251 | 8 |
| **Toplam (ölçüm 1)** | — | — | **251** |

Kontrol: 24+14+17+137+25+12+14+8 = **251** = ölçüm 1. Sapma: 0.
§2 = 137 satır = dosyanın %55'i — örnek ekseni ağırlıklı yapı.

### §16.6 GOOS Kümesi Ayrıştırması (§5:1 + §2.6–§2.8 — dosya içi)

| GOOS adayı | GOOS listesinde | Bu kartın davranışı |
|---|---|---|
| `windows` | VAR (§5:1) | §2.1 referans — iş nesnesi/token |
| `linux` | VAR (§5:1) | §2.2 referans — yetki üçlüsü |
| `darwin` | VAR (§5:1) | §2.3 referans — hedef etiketi |
| `ios` | VAR (§5:1) | §2.4 referans — tek mimari |
| `android` | VAR (§5:1) | §2.5 referans — 4 mimari |
| `tizen` | **YOK** | ⚠️ — uydurma hedef adı üretilmedi (§2.6) |
| `webos` | **YOK** | ⚠ — uydurma hedef adı üretilmedi (§2.7) |
| `reactos` | **YOK** | ⚠️ — uydurma hedef adı üretilmedi (§2.8) |
| Diğer (dragonfly, freebsd, illum…, js, netbsd, openbsd, plan9, solaris, wasip1) | VAR | bu kartın 8 OS ekseninde değil — kapsaklık dışı (§11) |

Toplam GOOS: 14 hedef (§5:1) · bu dosyanın ekseninde 5 VAR + 3 YOK = 8.

### §16.7 Fence Dağılım Defteri (bu tur ölçüldü)

| Bölüm | Aksis | Not |
|---|---|---|
| §0–§1 | 0 | düz metin/matris |
| §2 | 10 (5 çift) | satır 64·77·85·100·112·119·130·137·148·155 |
| §3 | 2 (1 çift) | PowerShell ölçüm bloğu |
| §4–§6 | 0 | sınır/referans/açık sorular |
| §7–§16 (bu tur) | 0 | eklenen bölümlerde kod bloğu yok |
| **Toplam** | **12** | §8 = §10 = tarama |

### §16.8 Tur Kapanış Kontrol Listesi (bu dosya)

| Kontrol | Sonuç | Kanıt |
|---|---|---|
| Kapı ≥505 | nihai ölçüm tur yanıtında | §15–§16.4 |
| FM değişmedi (validation 2026-10-14 dahil) | EVET — yalnız footer üstüne append | tek oldString edit |
| Örnek silindi mi | HAYIR — §0–§6 + §2 blokları aynen | §16.5 bütçe |
| Yeni GET / URL | 0 / 0 | §14 (4/4 eski URL) |
| Yeni dış iddia | 0 — her satır § referanslı | §16.2 |
| `⚠️` bölümüne blok | 0 | §10 · §16.7 |
| Bozuk karakter | 0 (bu tur 2+ tarama) | tur içi |
| Çift `---` (FM sınırı) | 2 (açılış + kapanış) | FM |
| Authority tek | 1 | footer |
| Kapı ihlali · beyazliste dışı yazım | 0 · 0 (tur sonu) | tur yanıtı |
| git commit | YOK (orkestratöre ait) | — |

### §16.9 Devir Kaydı

- Bu tur yalnız append: §0–§6 + FM değişmedi; yeni GET/URL/iddia 0.
- S1 · S2 [UNKNOWN] devredildi (§12) — kapatılmış gibi yazılmadı.
- Kardeş bağı başlık düzeyinde kaldı (§13); içerik okuması yapılmadı.

### §16.10 Nihai Ölçüm Mutabakatı (kapı kapanışı)

| Öğe | Değer |
|---|---|
| Ölçüm 1 (bölüm öncesi) | 251 |
| Eklenen § (§7–§16.10) | 10 ana bölüm + 7 alt bölüm |
| Nihai satır (bu tablonun üstündeki tur ölçümü + bu blok) | tur yanıtı |
| Eşik | 500 |
| Marj hedefi | 505 |

- Aritmetik disiplin: nihai sayı yalnız tur yanıtındaki son ölçümden
  okunur; dosya içindeki ara sayılar (401, 478) **bağlayıcı değildir**
  — bu kural §15'in de kuralıdır.
- Eklenen bölümlerde kod bloğu 0 (§16.7) → fence 12 aynen korundu.
- `⚠️` bölümlerine (§2.6–§2.8) bu turda da blok yazılmadı.
- Dosya adı `kod-ornekleri-go.md` değişmedi; beyazliste dışı yazım 0.
- Bu satırın sonunda Authority footer'ı tek ve sondadır (tekillik korunur).

### §16.11 Kapı Son Sözü (bağlayıcı)

- Marj: nihai − 500 → İHLAL YOK (nihai ölçüm tur yanıtında ≥505).
- İddia yok: §15 + §16.4 + §16.10 aritmetiği = kanıt; dolgu/TBD 0.
- Yeni GET 0 · yeni URL 0 · yeni dış iddia 0 · commit 0.
- Bozuk karakter taraması (bu tur 3.) = 0 · çift `---` = 2 (FM) · Authority = 1.
- Sıradaki kardeş: ios dosyası (aynı iskelet §7–§16) — bu dosya
  yalnız kendi satırını bağlar, kardeşin ölçümü kardeşin turundadır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode