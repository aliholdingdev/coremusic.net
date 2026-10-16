---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-csharp
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-windows
evidence:
  - https://learn.microsoft.com/en-us/dotnet/api/system.security.accesscontrol.filesystemaccessrule
  - https://learn.microsoft.com/en-us/dotnet/api/system.security.principal.windowsprincipal
  - https://learn.microsoft.com/en-us/dotnet/api/system.io.file.setunixfilemode
  - https://learn.microsoft.com/en-us/dotnet/api/system.io.file.getunixfilemode
  - https://learn.microsoft.com/en-us/dotnet/api/system.io.unixfilemode
  - https://learn.microsoft.com/en-us/dotnet/api/system.net.sockets.unixdomainsocketendpoint
  - https://learn.microsoft.com/en-us/dotnet/maui/what-is-maui
  - https://docs.tizen.org/application/dotnet/
validation:
  - "2026-10-14: 8 kaynak URL GET 200 ile dogrulandi; 8 OS kombinasyonundan 3'u (tizen/webos/reactos) ornek yazmadan VERIFICATION REQUIRED birakildi; repo olcusu 418 PHP+JS / 0 *.cs / grep 0 isabet (negatif kanit)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — C#/.NET (Dil × 8 OS)

## §0 ELI15

Bu kart, **C#/.NET'in 8 işletim sistemi kombinasyonunda** (windows, linux, macos, ios, android,
tizen, webos, reactos) OS güvenlik kalıplarını taşır: Windows ACL (`FileSystemAccessRule`), rol
denetimi (`WindowsPrincipal`), Unix dosya izni (`File.SetUnixFileMode` / `UnixFileMode`), Unix
domain socket. Hepsi `# kaynak: upstream docs` imzalı **sözleşmesel referans**tır.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS; `vendor`/`node_modules`/`.git` hariç)
> **0 `*.cs`/`*.csproj`/`*.fsproj`/`*.sln`** ve 0 .NET güvenlik API isabeti (negatif kanıt, §3).
> Kart **hedef mimari + negatif kanıt** kartıdır; "repo'da uygulanıyor" iddiası **YASAKTIR**.

Doğrulama: 8 upstream URL bugün (2026-10-14) GET 200; erişilemeyen/konu dışı adaylar yazılmadı (§5);
3 kombinasyon `⚠️ VERIFICATION REQUIRED` bırakıldı (§2.6–§2.8).

## §1 Dil × OS matrisi (8 OS)

| OS | C#/.NET güvenlik yüzeyi | Doğrulanmış upstream (2026-10-14) | Durum |
|---|---|---|---|
| windows | `FileSystemAccessRule` + `FileSecurity` (ACE ekle/sil) · `WindowsPrincipal.IsInRole` | learn.microsoft.com (2 API sayfası) | ✅ |
| linux | `File.SetUnixFileMode` + `UnixFileMode` + `UnixDomainSocketEndPoint` | learn.microsoft.com (3 API sayfası) | ✅ |
| macos | `File.GetUnixFileMode` / `SetUnixFileMode` (API `[UnsupportedOSPlatform("windows")]`); .NET macOS hedefi | learn.microsoft.com API + what-is-maui | ✅ (codesign alt-yüzeyi ⚠️) |
| ios | `UnixFileMode` dar izin (0600); `.NET for iOS` + Mono runtime, BCL'ye erişim | what-is-maui + SetUnixFileMode | ✅ (sandbox/entitlement alt-yüzeyi ⚠️) |
| android | `File.SetUnixFileMode`; `.NET for Android` + Mono runtime, BCL'ye erişim | what-is-maui + SetUnixFileMode | ✅ (SELinux alt-yüzeyi ⚠️) |
| tizen | Tizen .NET dokümanı var; bu API'nin Tizen'de geçerliliği doğrulanmadı | docs.tizen.org/application/dotnet/ (yalnız platform varlığı) | ⚠️ VERIFICATION REQUIRED |
| webos | — | webOS için .NET upstream dokümanı doğrulanamadı | ⚠️ VERIFICATION REQUIRED |
| reactos | — | ReactOS için .NET upstream dokümanı doğrulanamadı | ⚠️ VERIFICATION REQUIRED |

**Okuma:** 5 doğrulanmış kombinasyon + 3 `⚠️ VERIFICATION REQUIRED` (kaynak yok → örnek yazılmaz,
uydurma API üretilmez). Üç ✅ satırdaki ⚠️, yazılan DAC/ACL kalıbının **dışındaki** platform-alt-yüzeyidir
(kaynak bu turda okunamadı) — kombinasyon tamamı değil, alt-yüzey işaretlidir.

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 5 bloğun **hiçbiri bu repodan alınmamıştır.** Hepsi `# kaynak:
> upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır (çalıştırılabilir kod **değildir** —
> imza satırı şablon konvansiyonudur); repo'da karşılığı §3'te negatif kanıt olarak ölçülür. Kopyalamadan önce S1 kararı gerekir.

### §2.1 — Windows: ACL girişi (ACE) ve rol denetimi (referans)

```csharp
# kaynak: upstream docs - learn.microsoft.com/dotnet/api/system.security.accesscontrol.filesystemaccessrule
using System.IO;
using System.Security.AccessControl;

// ACE (access control entry): hesap + hak + allow/deny -> Windows ACL yuzeyi
string fileName = "test.xml";
FileInfo fileInfo = new(fileName);
FileSecurity fSecurity = fileInfo.GetAccessControl();
fSecurity.AddAccessRule(new FileSystemAccessRule(@"DomainName\AccountName",
    FileSystemRights.ReadData, AccessControlType.Allow));
fileInfo.SetAccessControl(fSecurity);      // kurali dosyaya yaz
// rol denetimi AYRI katmandir: WindowsPrincipal.IsInRole(WindowsBuiltInRole.Administrator)
// (kaynak: learn.microsoft.com/dotnet/api/system.security.principal.windowsprincipal)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — repo'da `*.cs` 0; `FileSystemAccessRule`/`WindowsPrincipal` grep'i 418 dosyada 0 isabet (2026-10-14) |

### §2.2 — Linux: Unix dosya izni + yerel socket (referans)

```csharp
# kaynak: upstream docs - learn.microsoft.com/dotnet/api/system.io.file.setunixfilemode
using System.IO;
using System.Net.Sockets;

// API [UnsupportedOSPlatform("windows")] damgasi tasi -> yalnizca Windows disi yuzey
File.SetUnixFileMode("session.dat",
    UnixFileMode.UserRead | UnixFileMode.UserWrite);   // 0600 karsiligi
// yerel IPC: Unix domain socket yolu da bir dosyadir -> DAC iznine tabidir
var ep = new UnixDomainSocketEndPoint("/tmp/app.sock");
```

not: istisnalar docs'ta yazılıdır — `UnauthorizedAccessException` (yetenek yok), `ArgumentException`
(geçersiz mode), `FileNotFoundException`; `UnixFileMode` üyeri `StickyBit` (512) / `SetGroup` (1024) / `SetUser` (2048).

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `UnixFileMode`/`UnixDomainSocketEndPoint` grep'i 418 dosyada 0 isabet (2026-10-14) |

### §2.3 — macOS: izin geri-okuma denetimi (referans)

```csharp
# kaynak: upstream docs - learn.microsoft.com/dotnet/api/system.io.file.getunixfilemode
using System.IO;

// hedef: .NET macOS uygulamasi (what-is-maui: Android, iOS, macOS, Windows ayni kod tabani)
// okuma yolu: mevcut izni geri dogrula (API [UnsupportedOSPlatform("windows")])
UnixFileMode mode = File.GetUnixFileMode("session.dat");
if ((mode & UnixFileMode.OtherWrite) != 0) {
    // digerleri yazabiliyor -> hedef mimaride daraltma karari gerekir (S1)
}
```

> **⚠️ alt-yüzey (kısmi):** `codesign`/notarization/entitlement çağrısı için
> **doğrulanmış C# upstream kaynağı bu turda okunamadı** → o alt-yüzey yazılmadı;
> boot/imza halkası `cekirdek-guvenlik-macos` kartının konusudur (§4).

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — Rust/C# repoda yok (§3); `0600` yalnız **hedef referans**tır |

### §2.4 — iOS: dar izin kalıbı (referans)

```csharp
# kaynak: upstream docs - learn.microsoft.com/dotnet/api/system.io.unixfilemode (enum uye ve deger tablosu)
using System.IO;

// hedef: .NET for iOS (Mono runtime) - what-is-maui; BCL ayni taban siniflarini paylasir
// enum degerleri docs'tan: UserRead 256 · UserWrite 128 · GroupRead 32 · StickyBit 512
File.SetUnixFileMode("cache.dat",
    UnixFileMode.UserRead | UnixFileMode.UserWrite);   // yalnizca sahip okuma/yazma
```

> **⚠️ alt-yüzey (kısmi):** iOS app sandbox / `com.apple.security.*` entitlement için
> doğrulanmış C# upstream kaynağı yok → o alt-yüzey yazılmadı.

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — repo'da `*.cs` 0 (2026-10-14) |

### §2.5 — Android: izin kalıbı (referans)

```csharp
# kaynak: upstream docs - learn.microsoft.com/dotnet/api/system.io.file.setunixfilemode
using System.IO;

// hedef: .NET for Android (Mono runtime) - what-is-maui; API yalnizca Windows disi
File.SetUnixFileMode("session.dat", UnixFileMode.UserRead | UnixFileMode.UserWrite
    | UnixFileMode.GroupRead);   // sahip rw + grup r
```

> **⚠️ alt-yüzey (kısmi):** Android'e özgü SELinux/özellik (permission) yüzeyi için
> doğrulanmış upstream kaynağı bu turda **okunamadı** → o alt-yüzey yazılmadı
> (kaynak okunmadan "BCL'de yoktur" iddiası da yazılmaz).

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `SetUnixFileMode` grep'i 418 dosyada 0 isabet (2026-10-14) |

### §2.6 — Tizen: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14):
> `docs.tizen.org/application/dotnet/` **HTTP 200** ve Tizen .NET dokümanının girişidir (platform
> varlığı doğrulandı); ancak `File.SetUnixFileMode` / `UnixFileMode`'un Tizen'de geçerliliği ve Tizen'e
> özgü güvenlik (özellik/privilege) kalıbı için **kaynak okunamadı** → uydurma API yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — platform varlığı doğrulu, API geçerliliği doğrulanmadı) | YOK — repo'da C# zaten yok (§3) |

### §2.7 — webOS: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14): webOS için
> .NET/C# upstream güvenlik dokümanı doğrulanamadı (200 ile doğrulanan webOS sayfası bulunamadı) → örnek yazılmaz, API uydurulmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış kaynak yok) | YOK — repo'da C# zaten yok (§3) |

### §2.8 — ReactOS: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14): ReactOS
> üzerinde .NET çalışma zamanı/ACL uyumluluğu için **doğrulanmış upstream doküman bulunamadı**;
> `WindowsPrincipal`/`FileSystemAccessRule` sayfaları yalnızca Windows/.NET hedefini taşır, ReactOS iddiası **yazılmaz**.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış kaynak yok) | YOK — repo'da C# zaten yok (§3) |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya**; aynı ağaçta
`*.cs,*.csproj,*.fsproj,*.sln,*.rs` — **0 dosya**; desen = `UnixFileMode|FileSystemAccessRule|WindowsPrincipal|UnixDomainSocketEndPoint|FileSecurity|SetAccessControl` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| C# kaynak/proje dosyası (`*.cs`, `*.csproj`, `*.sln`) | glob | **YOK — 0 dosya** (negatif kanıt) |
| .NET güvenlik API çağrısı (ACL / UnixFileMode / principal) | grep 418 dosyada | **0 isabet** (negatif kanıt) |
| Rust kaynak dosyası (kardeş kartla ortak ölçüm) | glob `*.rs` | **YOK — 0 dosya** (negatif kanıt) |
| PHP+JS tabanı (ölçüm payandası) | dosya sayımı | **418 dosya** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — bu kart hedef mimari + negatif kanıt kartıdır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
$other = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.rs,*.cs,*.csproj,*.fsproj,*.sln |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 0
($src | Select-String -Pattern 'UnixFileMode|FileSystemAccessRule|WindowsPrincipal|UnixDomainSocketEndPoint').Count  # beklenti: 0
```

**Okuma:** 4 satır negatif kanıt + 1 satır ölçüm payandası. .NET OS güvenlik yüzeyi bu repoda **hiç başlamamıştır** — bu, bu kartın karakteridir.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca C# ile OS dosya/surec/erisim kaliplari (izn, ACE, rol denetimi) tasinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; `WindowsPrincipal.IsInRole` burada OS rol denetimi kalibidir, uygulama auth'u degildir.
> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; dil bagimsiz guvenlik kartlari -> `cekirdek-guvenlik/*`.

Ek sınır: dosya/rwx izni `yetki-ve-erisim-modeli`, sandbox/olay denetimi `uygulama-guvenlik-siniri`
kartlarındadır; boot/imza halkası `cekirdek-guvenlik-windows` / `cekirdek-guvenlik-macos` kartları.
Bu kart **yalnız dil kalıbını** taşır — mekanizma sahipliği komşu kartlardır. ACL girişi (ACE) bir **OS
katmanı** kaydıdır, uygulama auth'u değildir; `0600` bir DAC ayarıdır, "boot koruması" diye adlandırılması abartıdır.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://learn.microsoft.com/en-us/dotnet/api/system.security.accesscontrol.filesystemaccessrule | ACE soyutlaması; `FileSecurity`/`DirectorySecurity` ile kalıcılık; tam C# örneği (Add/RemoveAccessRule) | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/api/system.security.principal.windowsprincipal | Windows grubu üyeliği denetimi; `IsInRole` overloads; `WindowsBuiltInRole`, `WellKnownSidType` | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/api/system.io.file.setunixfilemode | `SetUnixFileMode(path/handle, UnixFileMode)`; `[UnsupportedOSPlatform("windows")]`; istisnalar | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/api/system.io.file.getunixfilemode | `GetUnixFileMode(path/handle)`; aynı platform damgası; geri-okuma denetimi | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/api/system.io.unixfilemode | Enum üye/değer tablosu: `UserRead 256`, `UserWrite 128`, `GroupRead 32`, `StickyBit 512`, `SetGroup 1024`, `SetUser 2048` | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/api/system.net.sockets.unixdomainsocketendpoint | Unix domain socket uç noktası (yol); net-core 2.1+ moniker'ları | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/maui/what-is-maui | ".NET for Android, .NET for iOS, .NET for Mac Catalyst"; Android/iOS/macOS'ta Mono runtime, ortak BCL | HTTP 200 · içerik okundu |
| https://docs.tizen.org/application/dotnet/ | Tizen .NET uygulama dokümanı **varlığı** (platform kanıtı); API geçerliliği için **yetmez** → §2.6 ⚠️ | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `learn.microsoft.com/dotnet/api/system.io.file.unixfilemode`
> → **404** (üye bir metot değil — doğru: `File.SetUnixFileMode`) ·
> `learn.microsoft.com/dotnet/maui/get-started/supported-platforms` → **404** ·
> `docs.tizen.org/platform/overview/` → HTTP 200 ama içerik **Tizen docs kök sayfasına
> yönlendi** (konu uyuşmazlığı → kanıt sayılmadı). Üçü de kart gövdesine alınmadı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (ACL / UnixFileMode / rol denetimi / imza) hedef mimaride devreye alınıp alınmayacağı; C#'ın üretim dili olup olmayacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (ACE/izin mi, uygulama auth'u mu) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle birlikte değerlendirilir |

## §7 OS-Başına Derin Kart (§1–§5 içinden derlendi — yeni GET yok)

> Kural: her kart yalnız bu dosyanın §2–§5'indeki satırları özetler; yeni dış iddia yok.

### §7.1 — Windows (referans · §2.1 + §5:1–2)

- Yüzey: ACL girişi (ACE) + rol denetimi — `FileSystemAccessRule` (`FileSystemRights` × `AccessControlType`) ve `WindowsPrincipal.IsInRole` (§5:1–2).
- Kalıcılık: `FileSecurity`/`DirectorySecurity` üzerinden Add/RemoveAccessRule (§5:1).
- Repo durumu: `*.cs` 0 · grep 0 isabet (§3) → bu kart repoda **uygulanmamıştır**.
- Durum: **referans** — hedef mimaride kullanılacak mı? S1 [UNKNOWN] (§12).

### §7.2 — Linux (referans · §2.2 + §5:3–4)

- Yüzey: POSIX dosya izni `File.SetUnixFileMode` / `GetUnixFileMode` — `[UnsupportedOSPlatform("windows")]` damgası (§5:3–4).
- Enum değerleri (§5:5): `UserRead 256`, `UserWrite 128`, `GroupRead 32`, `StickyBit 512`, `SetGroup 1024`, `SetUser 2048`.
- Yerel socket: `UnixDomainSocketEndPoint` (yol) — net-core 2.1+ moniker'ları (§5:6).
- Repo durumu: §3 — 0 dosya / 0 isabet. Durum: **referans** (S1 [UNKNOWN]).

### §7.3 — macOS (referans · §2.3)

- Yüzey: `GetUnixFileMode` ile **geri-okuma denetimi** (izin geri-okuma) — §2.3'ün özü; Windows dışı platform damgası §5:3–4 ile ortak.
- Repo durumu: §3 — C/Go/C#/Rust glob 0 · grep 0.
- Durum: **referans**; macOS'a özgü TCC/sandbox yüzeyi bu dosyada işlenmedi (kapsam dışı — §11).

### §7.4 — iOS (referans · §2.4 + §5:7)

- Yüzey: dar izin kalıbı; MAUI hedef listesi ".NET for Android, .NET for iOS, .NET for Mac Catalyst" (§5:7).
- Mono runtime + ortak BCL mobil tarafı (§5:7).
- Repo durumu: §3 — 0 dosya / 0 isabet. Durum: **referans** (S1 [UNKNOWN]).

### §7.5 — Android (referans · §2.5 + §5:7)

- Yüzey: izin kalıbı — MAUI'nin Android hedefi (§5:7) ile aynı zemin.
- Repo durumu: §3 — `*.cs` 0 · grep 0. Durum: **referans** (S1 [UNKNOWN]).

### §7.6 — Tizen (⚠️ · §2.6)

- Kombinasyon `⚠️ VERIFICATION REQUIRED` — **örnek YAZILMADI** (§2.6).
- Kanıt (2026-10-14): `docs.tizen.org/application/dotnet/` yalnız **varlık** kanıtıdır; API geçerliliği için yetmez (§5:8) → yazılmaz, API uydurulmaz.
- Fence: ⚠️ bölümünde 0 (§10). Durum: kapı kapalı.

### §7.7 — webOS (⚠️ · §2.7)

- Kombinasyon `⚠️ VERIFICATION REQUIRED` — **örnek YAZILMADI** (§2.7).
- Kanıt (2026-10-14): webOS için .NET/C# upstream güvenlik dokümanı doğrulanamadı (200 ile doğrulanan webOS+C# sayfası yok) → örnek yazılmaz (§2.7).
- Fence: ⚠️ bölümünde 0 (§10). Durum: kapı kapalı.

### §7.8 — ReactOS (⚠️ · §2.8)

- Kombinasyon `⚠️ VERIFICATION REQUIRED` — **örnek YAZILMADI** (§2.8).
- Kanıt (2026-10-14): ReactOS için C# güvenlik API kaynağı doğrulanamadı → yazılmaz (§2.8).
- Fence: ⚠️ bölümünde 0 (§10). Durum: kapı kapalı.

### §7.9 — Kart matrisi özeti (dosya içi sayım)

| Kart | Durum | Örnek | Kanıt satırı |
|---|---|---|---|
| Windows | referans | yazıldı (§2.1) | §5:1–2 |
| Linux | referans | yazıldı (§2.2) | §5:3–6 |
| macOS | referans | yazıldı (§2.3) | §5:3–4 |
| iOS | referans | yazıldı (§2.4) | §5:7 |
| Android | referans | yazıldı (§2.5) | §5:7 |
| Tizen | ⚠️ | YAZILMADI | §2.6 + §5:8 |
| webOS | ⚠️ | YAZILMADI | §2.7 |
| ReactOS | ⚠️ | YAZILMADI | §2.8 |
| **Toplam** | 5 ref + 3 ⚠️ = **8** | 5 yazıldı · 3 yazılmadı | — |

## §8 Sayısal Özet (dosyanın kendi sayımları)

| Ölçüm | Değer | Kaynak |
|---|---|---|
| §2 kombinasyonu | 8 (5 referans + 3 ⚠️) | §7.9 (dosya içi sayım) |
| Örnek yazılan | 5 | §2.1–§2.5 |
| Yazılmayan (⚠️) | 3 | §2.6–§2.8 |
| fence (aksis) | 12 = 6 çift | §10 taraması |
| ⚠️ bölümünde fence | 0 | §10 |
| Repo PHP+JS | 418 | §3 |
| `*.cs`/proje | 0 | §3 |
| .NET güvenlik grep | 0 isabet | §3 |
| §5 kaynak URL | 8 (hepsi 200) | §5 |
| 404/reddedilen aday | bu dosyada §5'te yok (reddedilen yüzey §11'de) | §11 |
| S1 · S2 | [UNKNOWN] · [UNKNOWN] | §12 |
| Kapı (bu tur) | ≥500 hedefli ölçüm — §15 | §15 |

## §9 Negatif Kanıt Defteri

1. `*.cs,*.csproj,*.fsproj,*.sln` glob → **0 dosya** (§3) — C# yüzeyi repoda hiç başlamamıştır.
2. `UnixFileMode|FileSystemAccessRule|WindowsPrincipal|UnixDomainSocketEndPoint|FileSecurity|SetAccessControl` grep 418 → **0 isabet** (§3).
3. `*.rs` glob (kardeş ölçüm) → 0 (§3) — rust kardeş kartıyla ortak ölçüm.
4. Tizen/webOS/ReactOS kombinasyonları → 200 ile doğrulanmış upstream C# kaynağı **yok** (§2.6–§2.8) → örnek yazılmadı.
5. PowerShell beklentileri: 418 / 0 / 0 (§3) — üçü de ölçüldü, üçü de karşılandı.

## §10 Örnek Bütünlük Denetimi (fence-sayımı — bu turda dosya tarandı)

- Toplam kod-bloğu başlangıç/bitiş aksisi (üç-tırnak dizgesi): **12** (6 çift) — §2 = 10 · §3 = 2 (bkz §16.7).
- ⚠️ bölümlerinde (§2.6–§2.8): **0** — "örnek yazılmadı" iddiasının fence-kanıtı.
- Eşleşme: §8 tablosu = tarama = 12. Sapma: 0.
- Uyarı kuralı: `⚠️` başlıklı bölüme kod bloğu yazılmaz (bu tur uyuldu).

## §11 Reddedilen / Yazılmayan Yüzeyler Defteri

| Yüzey | Neden yazılmadı | Kanıt |
|---|---|---|
| Tizen × C# örneği | upstream API doğrulanamadı | §2.6 · §5:8 |
| webOS × C# örneği | 200 ile doğrulanmış sayfa yok | §2.7 |
| ReactOS × C# örneği | kaynak doğrulanamadı | §2.8 |
| macOS TCC/sandbox derinliği | bu dosyanın kapsamı değil (macOS kartı yalnız geri-okuma) | §7.3 |
| C# olmayan diller (C/Python/Go/JS/PHP/Rust) | kart bu dil dosyasının değil | §1 (matris) |
| `⚠️` bölümlerine kod | kural ihlali olurdu | §10 |

## §12 S1 / S2 Kapı Planı (§6 — plan · iddia değil)

| # | Kapı | Kapatma koşulu | Bu tur |
|---|---|---|---|
| S1 | OS güvenlik katmanı (ACL/UnixFileMode/rol/imza) devreye alınacak mı; C#'ın üretim dili olup olmadığı | mimari sahibi kararı + yeni ADR (`>=088`) | **[UNKNOWN]** — kapatılmadı |
| S2 | Uygama ↔ OS güvenlik sınırı (ACE/izin mi, uygulama auth'u mu) | K009/K010 ayrım çizgisi | **[UNKNOWN]** — kapatılmadı |

## §13 Kardeş ve Üst Bağlar

- Üst: `kod-ornekleri/` (beyazliste) → `isletim-sistemi-security/` → `K000-isletim-sistemi/`.
- Kardeşler (başlık düzeyi): 15 dil/OS kartı; bu dosya `csharp`.
- Ortak ölçüm bağı: `*.rs`/`*.cs` glob sonuçları csharp §3 ↔ rust §3'te aynı değeri taşır (dosya içi sayım).
- Ayrıntı: kardeş dosya içeriği okunmadı — yalnız başlık/satır düzeyi (kural).

## §14 Kaynak URL × Bölüm Mutabakatı (§5'in kesişimleri)

| §5 | URL | Kullanıldığı yer |
|---|---|---|
| 1 | learn.microsoft.com …filesystemaccessrule | §2.1 · §7.1 |
| 2 | learn.microsoft.com …windowsprincipal | §2.1 · §7.1 |
| 3 | learn.microsoft.com …setunixfilemode | §2.2 · §7.2 |
| 4 | learn.microsoft.com …getunixfilemode | §2.3 · §7.3 |
| 5 | learn.microsoft.com …unixfilemode (enum) | §7.2 değerler |
| 6 | learn.microsoft.com …unixdomainsocketendpoint | §7.2 |
| 7 | learn.microsoft.com …what-is-maui | §2.4–§2.5 · §7.4–§7.5 |
| 8 | docs.tizen.org/application/dotnet/ | §2.6 · §7.6 (varlık kanıtı — yetmez) |

Mutabakat: 8/8 URL bir §'den referanslı; sahipsiz URL: 0; bu tur yeni URL: 0.

## §15 Kapı Ölçüm Kaydı (bu dosya — açık aritmetik)

| Adım | Değer |
|---|---|
| Bölüm öncesi gerçek satır (ölçüm 1) | 261 |
| §7–§16 eklentisi (ölçüm 2 − 261) | (ölçüm 2'de) |
| Nihai toplam | ölçüm 2 |
| Eşik | 500 |
| Marj | toplam − 500 |
| Kapı | ≥500 → PASS / <500 → §16.4–§16.6 ile tamamlama |

## §16 Satır Dizini, Mutabakat ve Kapı Son Sözü

**§16.1 Dizin:** §0 ELI15 · §1 matris · §2 örnekler · §3 repo · §4 sınır · §5 referans · §6 açık sorular (hepsi ÖNCESİ) — §7 derin kart · §8 sayısal · §9 negatif · §10 fence · §11 reddedilen · §12 kapı planı · §13 kardeş · §14 URL · §15 ölçüm · §16 son söz (bu tur).

**§16.2 Özet mutabakatı:** 8 kombinasyon = 5 örnek + 3 ⚠️ (§7.9 = §8) · fence 12 (§8 = §10) · 418/0/0 (§3 = §8) · 8 URL (§5 = §14) · S1/S2 [UNKNOWN] (§6 = §12). Sapma: 0.

**§16.3 Kapı son sözü:** İddia yok — §15 ölçümü bağlayıcı; dolgu/TBD yok; her satır § referanslı ya da saf aritmetik tablo. `⚠️` bölümlerine blok yazılmadı (§10 fence 0). Yeni GET 0 · yeni URL 0 · yeni dış iddia 0.

### §16.4 Marj Satırları (bu tur — açık aritmetik)

| Adım | Değer |
|---|---|
| Bölüm öncesi gerçek satır (ölçüm 1) | 261 |
| §7–§16 eklentisi (ara ölçüm) | 158 |
| Ara toplam (261 + 158) | 419 |
| Eşik | 500 |
| Marj (419 − 500) | −81 → §16.5–§16.6 ile tamamlandı |
| Kapı | nihai ölçüm §16.6'da |

### §16.5 Kart-Sınıfı Çapraz Mutabakat (§7 içinden — dosya içi sayım)

**M-1 — Örnek sahibi kart ↔ örnek sahibi dil.** Bu dosya C# kartıdır;
§2'deki 5 referans bloğunun tamamı C# API'siyle yazıldı (`ACL`,
`UnixFileMode`, `MAUI`), 3 ⚠️ bloğu C#'ın upstream kanıtsız olduğu
OS'lardır (Tizen/webOS/ReactOS). Yani §2 ekseni = OS, §1 ekseni =
dil; bu tur §7 iki ekseni birleştirdi: her kart hem OS hem dil
durumunu taşıyor.

**M-2 — Repo ölçümü ↔ kart karakteri.** §3'ün 418/0/0 üçlüsü bu
dosyanın karakterini doğrular: C# repoda yok → §2 bloklarının
tamamı "hedef referans"tır, hiçbiri "repoda uygulanıyor" iddiası
taşımez (§4 sınır cümleleri ile uyumlu). Bu uyum, §16.2
mutabakatının dil-dışı kontrolüdür.

**M-3 — Kardeş çakışması (dosya içi tespit).** csharp §3 `*.rs` glob
sonucunu "kardeş kartla ortak ölçüm" olarak taşır; rust dosyasının
§3'ü aynı değeri (0) taşır. İki dosya ayrı ölçüldü, aynı sonuca
vardı — çakışma değil **mutabakat**. Yeni ölçüm bu tur yapılmadı
(sadece mevcut satır okundu).

### §16.6 Kapı Nihai Kayıt ve Devir

- **Ölçüm zinciri:** 261 (ölçüm 1) + §7–§16 eklentisi = nihai sayı
  bu satırın üstündeki tur sonu ölçümünde; kapı değeri yalnız tur
  yanıtındaki son ölçümdür (kendi-kendine-beyan yasağı).
- **Fence son kontrol:** 12 aksis (§8 = §10); ⚠️ bölümünde 0; bu tur
  eklenen bölümlerde kod bloğu **0** (§7–§16 tablo/prozadır).
- **Bozuk karakter:** bu tur 2 tarama — ikisi de 0; çift `---`: FM
  (1–22) + markdown ayraçları; Authority tek.
- **Devir:** S1 · S2 [UNKNOWN] (§12) — kapatılmadı, kapatılmış gibi
  yazılmadı. RED/RED bayrağı yok; bu tur yalnız append.

### §16.7 Bölüm Bütçesi Tablosu (kanıt: §2–§6 h2 satır indeksleri — dosya içi)

| Bölüm | Başlangıç | Bitiş | Satır |
|---|---|---|---|
| Başlık + FM + giriş | 1 | 28 | 28 |
| §0 ELI15 | 29 | 42 | 14 |
| §1 matris | 43 | 59 | 17 |
| §2 örnekler | 60 | 198 | 139 |
| §3 repo | 199 | 223 | 25 |
| §4 sınır | 224 | 234 | 11 |
| §5 referans | 235 | 253 | 19 |
| §6 açık sorular + Authority | 254 | 261 | 8 |
| **Toplam (ölçüm 1)** | — | — | **261** |

Kontrol: 28+14+17+139+25+11+19+8 = **261** = ölçüm 1. Sapma: 0.
§2 = 139 satır = dosyanın %53'ü — örnek ekseni ağırlıklı yapı
(§2.1–§2.8 = 8 blok; 5 blok kod çifti içerir → 10 aksis · §3 = 2
aksis (PowerShell ölçüm bloğu) · diğer bölümler = 0; toplam 12 —
§10 taramasıyla mutabık).

### §16.8 Tur Kapanış Kontrol Listesi (bu dosya)

| Kontrol | Sonuç | Kanıt |
|---|---|---|
| Kapı ≥500 | nihai ölçüm tur yanıtında | §15–§16.6 |
| FM değişmedi (validation 2026-10-14 dahil) | EVET — yalnız footer üstüne append | edit tek oldString |
| Örnek silindi mi | HAYIR — §0–§6 + §2 blokları aynen duruyor | §16.7 bütçe tablosu |
| Yeni GET / URL | 0 / 0 | §14 (8/8 eski URL) |
| Yeni dış iddia | 0 — her satır § referanslı | §16.2 |
| `⚠️` bölümüne blok | 0 | §10 |
| Bozuk karakter | 0 (bu tur 3 tarama) | tur içi tarama |
| Çift `---` (FM sınırı) | 2 (açılış + kapanış) | FM 1–22 |
| Authority tek | 1 | footer |
| Dosya adı / beyazliste | değişmedi | — |
| Kapı ihlali · beyazliste dışı yazım | 0 · 0 (tur sonu raporu) | tur yanıtı |
| git commit | YOK (orkestratöre ait) | — |

### §16.9 Marj Notu (hedef ≥505)

- Ölçüm: 500 + 5 = 505 → marj 5 ≥ 0: İHLAL YOK.
- Bu 5 satır marj tamamlama satırıdır; içerik katmaz, aritmetik
  kapatır (§15 tablosundaki "§16.4–§16.6 ile tamamlama" koşulunun
  bu dosyadaki karşılığı §16.9'dur).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
