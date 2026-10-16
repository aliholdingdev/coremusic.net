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

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K010-identitiy`; bu kartta yalnız Go ile OS dosya/süreç/erişim kalıpları (yetki üçlüsü, iş nesnesi, hedef etiketi) taşınır.
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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode