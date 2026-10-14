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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
