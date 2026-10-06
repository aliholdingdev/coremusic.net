---
title: "Windows Güvenlik ve Ölçüllü Kısıtlar — Token, UAC, Job Objects ve K6 Sınırı"
type: architecture
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "K000 Windows Core — SSOT: .ai/architecture/k000-windows-core/"
updated: 2026-10-06
---

# Windows Güvenlik ve Ölçüllü Kısıtlar — Token, UAC, Job Objects ve K6 Sınırı

> **K numarası:** K000 — Windows Core. Bu belge **platform yetki mekanizmalarını**
> (token, UAC, Job Object, bellek kilidi ayrıcalığı) anlatır ve **K6 (Güvenlik) /
> K7** ile sınırını çizer. Depoda `*.cpp` / `*.h` sayısı **0** olduğundan tüm kod
> blokları **kavramsal iskelettir**. Güvenlik açığı iddiası, sürüm iddiası ve
> ayrıcalık **başarısı** bu belgede **üretilmez** — yalnızca kaynak belgeden alınır
> ve etiketlenir.

---

## §1 Kapsam ve Bağlam

### §1.1 Dosya İlişkileri

| Dosya | İlişki |
|---|---|
| [[index]] | K000 klasör indeksi |
| [[windows-api-yuzeyi]] | Win32 API yüzeyi — `OpenProcessToken`, `ShellExecuteEx`, `CreateJobObject` |
| [[win32-olay-dongusu-ve-mesaj-kuyrugu]] | Olay döngüsü — güvenlik dialogları (`runas`) ile kesişim |
| [[wasapi-ses-yolu-cekirdek]] | Ses oturumu — ayrıcalık gerektirmeyen normal yol |
| [[asio-cekirdek-entegrasyonu]] | RT öncelik (`SetThreadPriority`) — izin boyutu bu belgede |
| [[windows-performans-ve-gozlemlenebilirlik]] | Audit / Event Log yazımı |

### §1.2 Kapsam Sınırı

| Kapsar | Kapsamaz |
|---|---|
| Process token + privilege modeli (`AdjustTokenPrivileges`) | **K6 Uygulama Güvenliği** (auth, CSRF, CSP, Argon2id) → `[KAPSAM DIŞI]` |
| UAC elevation (`ShellExecuteEx runas`) | **K7** web güvenliği başlıkları → `[KAPSAM DIŞI]` |
| Job Object kaynak sınırları | **K1 Donanım / K2 Sürücü** güvenlik donanım anahtarı → `[KAPSAM DIŞI]` |
| Bellek kilidi ayrıcalığı (`SeLockMemoryPrivilege`) | Uygulama-sınıfı şifreleme (AES-256-GCM, Argon2id) → K6 |
| K0 ↔ K6 bağımlılık **yasak** yorumu (matris) | Windows sürüm/yama düzeyi iddiası → `[NOT PROVIDED]` |

---

## §2 Gömülü Kaynaklar

| # | Dosya | Bu belgede kullanımı |
|---|---|---|
| 1 | `_backup\...\k0-isletim-sistemi\windows-core.md` | Token (L55–L66, L320–L330) · Job Objects (L282–L304) · UAC (L306–L330) · Güvenlik Notları (L400–L406) |
| 2 | `_backup\...\k0-isletim-sistemi\process-isolation.md` | Sandbox / chroot / namespaces / seccomp-bpf (L13, L37–L107, L543–L547, L610) |
| 3 | `_backup\...\k0-isletim-sistemi\macos-core.md` | `com.apple.security.app-sandbox` (L396) — karşılaştırma |
| 4 | `_backup\...\k0-isletim-sistemi\index.md` | Modül envanteri (L51) · "Durum: Planlandı" |
| 5 | `_backup\...\k0-isletim-sistemi\README.md` | K0.1.3.18/19 kayıt satırları (L474–L475) |
| 6 | `_backup\...\architecture\k6-guvenlik\index.md` (ve 15 dosya) | K6 kapsamının gerçekte ne olduğu |
| 7 | `_backup\...\architecture\katman-baglilik-matrisi.md` | **K6 → K0 yasak (KRİTİK)** · K7 konumu |
| 8 | `.ai\AGENTS.md` | §5 A1 = K6-K7 · §24.3 Security okuma listesi · §18 uyarılar |
| 9 | `.ai\CLAUDE.md` | Katman tanımı K6 · güvenlik terimleri |
| 10 | `.ai\.decisions\accepted\ADR-010-*` (dizinde referans) | K6 karar kapısı — dosya varlığı §6.3 |

---

## §3 K0 ↔ K6 ↔ K7 Konumu

### §3.1 A1 Etiketi

`.ai/AGENTS.md` §5 (MEVCUT PROJE GERÇEĞİ):

| Alan | K katmanları | Kapsam |
|---|---|---|
| **A1** | **K6-K7** | Güvenlik |

### §3.2 Yasak Okuması (Matris)

`katman-baglilik-matrisi.md` (MEVCUT PROJE GERÇEĞİ):

```text
K6 → K0 bağımlılığı = YASAK (KRİTİK)
K12 → K0 bağımlılığı = YASAK (ORTA)
K0 = kök katman
```

**Doğru okuma:** K6 (uygulama güvenliği) **doğrudan** K0 Win32 API'sini çağıramaz;
güvenlik *politikası* K6'da yaşar, güvenlik *primitive*'ı (token/ACL/Job) K0'dadır.
K6 ile K0 arasındaki tek yasal bağlantı, **ara katmanlar üzerinden** kurulur.

| İddia | Etiket |
|---|---|
| K6 → K0 bağımlılığı KRİTİK yasak | **MEVCUT PROJE GERÇEĞİ** (matris) |
| A1 = K6-K7 = Güvenlik | **MEVCUT PROJE GERÇEĞİ** (`.ai/AGENTS.md` §5) |
| K6'nın gerçek dosyaları auth/CSRF/CSP/encryption başlıklarını taşır | **MEVCUT PROJE GERÇEĞİ** (`_backup\...\k6-guvenlik\` 16 dosya) |
| Bu belge, K6'nın **değil** K0'ın güvenlik yüzeyidir | bu belgenin tanımı |

---

## §4 Token ve Ayrıcalık (Privilege) Modeli

### §4.1 Large Page Ayrıcalığı (Kaynak: `windows-core.md` L55–L66 — birebir)

```c
// KAVRAMSAL İSKELET — kaynak: _backup/.../windows-core.md L55-L66
// Depoda derlenebilir C dosyası YOKTUR.
HANDLE hToken;
OpenProcessToken(GetCurrentProcess(), TOKEN_ADJUST_PRIVILEGES, &hToken);
// SeLockMemoryPrivilege'ı etkinleştir

LPVOID pBuffer = VirtualAlloc(
    NULL,
    LARGE_PAGE_SIZE,  // 2MB large page
    MEM_COMMIT | MEM_RESERVE | MEM_LARGE_PAGES,
    PAGE_READWRITE | PAGE_WRITECOMBINE
);
```

### §4.2 `SE_DEBUG_NAME` Ayrıcalığı (Kaynak: L320–L330 — birebir)

```c
// KAVRAMSAL İSKELET — kaynak: windows-core.md L320-L330
HANDLE hToken;
OpenProcessToken(GetCurrentProcess(),
                 TOKEN_ADJUST_PRIVILEGES | TOKEN_QUERY, &hToken);

TOKEN_PRIVILEGES tp;
LookupPrivilegeValue(NULL, SE_DEBUG_NAME, &tp.Privileges[0].Luid);
tp.PrivilegeCount = 1;
tp.Privileges[0].Attributes = SE_PRIVILEGE_ENABLED;

AdjustTokenPrivileges(hToken, FALSE, &tp, sizeof(tp), NULL, NULL);
```

### §4.3 Ayrım Tablosu

| Ayrıcalık | Amaç | Kapsam | Etiket |
|---|---|---|---|
| `SeLockMemoryPrivilege` | Large page bellek kilidi | K0 (bellek) | **MEVCUT PROJE GERÇEĞİ** (`windows-core.md` L59) |
| `SE_DEBUG_NAME` | Süreç hedefleme (hata ayıklama) | K0 (süreç) | **MEVCUT PROJE GERÇEĞİ** (`windows-core.md` L325) |
| `TOKEN_QUERY` / `TOKEN_ADJUST_PRIVILEGES` | Token okuma/ayar | K0 | **MEVCUT PROJE GERÇEĞİ** |

> **⚠️ ÖLÇÜLLÜ KISIT — `SE_DEBUG_NAME`:** Bu ayrıcalık **en yüksek yetki sınıfından**
> biridir; kaynağındaki örnek bunu **açıkça etkinleştirir** ve **kapatma adımı
> yazmaz**. Belgenin kendi güvenlik notu ise tersini söyler (§4.4 madde 2):
> "Gereksiz privilege'ları devre dışı bırakın". İkisi arasındaki **gerilim bu
> belgede raporlanır, kod düzeltilmez** (kaynak salt-okunur).

### §4.4 Güvenlik Notları (Kaynak: `windows-core.md` L400–L406 — birebir)

1. **UAC**: Yönetici yetkisi gerektiren işlemler için UAC elevation kullanın
2. **Token Privileges**: Gereksiz privilege'ları devre dışı bırakın
3. **Job Objects**: Kaynak sınırlamaları ile process koruması sağlayın
4. **Registry Erişimi**: minimum yetki ile erişim sağlayın
5. **Named Pipes**: Güvenli pipe security descriptor kullanın

| Not | Uygulanabilirlik (bu depoda) |
|---|---|
| 1 — UAC | kod örneği var (§5) |
| 2 — Token Privileges | kod örneği var ama **kapatma yok** (§4.3 uyarısı) |
| 3 — Job Objects | kod örneği var (§6) |
| 4 — Registry minimum yetki | `ACL`/`DACL` ayrıntısı **verilmemiş** → `[NOT PROVIDED]` |
| 5 — Named Pipe security descriptor | oluşturucu API **verilmemiş** → `[NOT PROVIDED]` |

---

## §5 UAC — Yetki Yükseltme

### §5.1 `ShellExecuteEx` (Kaynak: L310–L318 — birebir)

```c
// KAVRAMSAL İSKELET — kaynak: windows-core.md L310-L318
SHELLEXECUTEINFO sei = { sizeof(sei) };
sei.lpVerb = L"runas";
sei.lpFile = L"coremusic_admin.exe";
sei.lpParameters = L"--install-driver";
sei.nShow = SW_SHOWNORMAL;

ShellExecuteEx(&sei);
```

| Alan | Değer | Anlam |
|---|---|---|
| `lpVerb` | `runas` | UAC yükselme isteği tetikler |
| `lpParameters` | `--install-driver` | Sürücü kurulumu — tipik yönetici gerektiren iş |
| Dönüş | `BOOL` | Kullanıcı red/iptal ederse başarısız |

### §5.2 ÖLÇÜLLÜ Kısıtlar

| Kısıt | Değer | Etiket |
|---|---|---|
| Kullanıcı redsine karşı hata yolu | — | `[NOT PROVIDED]` — belgede yok |
| Installer/driver kurulumu ne zaman `runas` kullanır | — | `⚠️ VERIFICATION REQUIRED` |
| Manifest asRequested vs requireAdministrator | — | `⚠️ VERIFICATION REQUIRED` — manifest dosyası depoda yok |

> **Kapsam notu:** `--install-driver` parametresi **K2 sürücü kurulumu** ile ilişkilidir;
> sürücü kurulumunun kendisi `[KAPSAM DIŞI]` (K2/K1 sınırı).

---

## §6 Job Objects — Ölçüllü Kaynak Kısıtı

### §6.1 Kod (Kaynak: `windows-core.md` L286–L304 — birebir)

```c
// KAVRAMSAL İSKELET — kaynak: windows-core.md L286-L304
HANDLE hJob = CreateJobObject(NULL, L"CoreMusicJob");

// CPU sınırlaması ekleme
JOBOBJECT_CPU_RATE_CONTROL cpuRate = { 0 };
cpuRate.CpuRate = 50;  // %50 CPU kullanımı
SetInformationJobObject(hJob, JobObjectCpuRateControlInformation,
    &cpuRate, sizeof(cpuRate));

// Memory sınırlaması ekleme
JOBOBJECT_EXTENDED_LIMIT_INFORMATION limitInfo = { 0 };
limitInfo.ProcessMemoryLimit = 1024 * 1024 * 512;  // 512MB
SetInformationJobObject(hJob, JobObjectExtendedLimitInformation,
    &limitInfo, sizeof(limitInfo));

// Process'i job'a atama
AssignProcessToJobObject(hJob, pi.hProcess);
```

### §6.2 Sınırlar Tablosu

| Sınır | Kaynaktaki değer | Doğrulama durumu |
|---|---|---|
| CPU oranı | `CpuRate = 50` (yorum: `%50`) | **ESKİ BELGE İDDİASI** — birim/yorum `⚠️ VERIFICATION REQUIRED` |
| Bellek sınırı | `512 MB` | **ESKİ BELGE İDDİASI** — ürün gereksinimi değil |
| Job adı | `CoreMusicJob` | **MEVCUT PROJE GERÇEĞİ** (kod örneği) |

> **Neden ölçülü:** RT ses yolu için CPU sınırı **xrun üretme riski** taşır
> (bkz. [[asio-cekirdek-entegrasyonu]] §11). Sınır ile gerçek zamanlı öncelik
> arasındaki çatışma **bu depoda çözülmemiştir** → `⚠️ VERIFICATION REQUIRED`.

---

## §7 Bellek Kilidi ve Paylaşım (Ölçüllü Ayrıcalık)

| API | Amaç | Ayrıcalık | Etiket |
|---|---|---|---|
| `VirtualAlloc(..., MEM_LARGE_PAGES, ...)` | Large page | `SeLockMemoryPrivilege` | **MEVCUT PROJE GERÇEĞİ** (`windows-core.md` L59) |
| `CreateFileMapping` + `MapViewOfFile` | Süreçler arası paylaşılan bellek | — | **MEVCUT PROJE GERÇEĞİ** (L68–L79) |
| `SEC_COMMIT` + ad `L"CoreMusicSharedMemory"` | Adlandırılmış nesne | Güvenlik descriptor **verilmemiş** | `[NOT PROVIDED]` |

> **Adlandırılmış nesne riski (kavramsal):** Aynı adı başka süreç oluşturabilir mi,
> sorusu belgede **yanıtlanmamıştır** → `⚠️ VERIFICATION REQUIRED`. Named Pipe için
> ise belge yalnız "güvenli pipe security descriptor kullanın" der (L406) — API'si
> yazılmamış.

---

## §8 Öncelik Sınıfları ve Yetki Kesişimi

Kaynak: `windows-core.md` L45–L49 (MEVCUT PROJE GERÇEĞİ).

| Sınıf | Belgedeki kullanımı |
|---|---|
| `REALTIME_PRIORITY_CLASS` | Ses işleme için |
| `HIGH_PRIORITY_CLASS` | Kritik işlemler için |
| `ABOVE_NORMAL_PRIORITY_CLASS` | Normal çoklu ortam için |
| `NORMAL_PRIORITY_CLASS` | Arka plan işlemleri için |

| İlişki | Durum |
|---|---|
| `REALTIME_PRIORITY_CLASS` + `SetThreadPriority(TIME_CRITICAL)` birlikte | kod örnekleri ayrı dosyalarda (`windows-core.md` L46, `windows-api.md` L166) → **birlikte kullanımı test edilmemiş** `⚠️ VERIFICATION REQUIRED` |
| Yönetici olmayan sürecin bu sınıfa geçebilmesi | `[NOT PROVIDED]` — belgede yok |
| Windows Software Engineer (`win-sw`) sahipliği | `.ai/AGENTS.md` §15 — `WASAPI, COM, WinRT, WDK` **MEVCUT PROJE GERÇEĞİ** |

---

## §9 İzolasyon (Process Isolation) — Ölçüllü Boşluk

### §9.1 Belgenin Tanımı (POSIX)

`process-isolation.md` L13 (MEVCUT PROJE GERÇEĞİ — birebir):

> Process sandboxing, capability dropping, **chroot, namespaces ve seccomp-bpf**
> ile uygulama ve process'leri birbirinden izole eder.

| Mekanizma | Platform |
|---|---|
| `chroot` (`/tmp/coremusic-sandbox-XXXXXX`) | POSIX |
| namespaces / capability dropping | POSIX (Linux) |
| seccomp-bpf | POSIX (Linux) |
| `com.apple.security.app-sandbox` | macOS (`macos-core.md` L396) |
| **Windows karşılığı** | **YOK** → `[NOT PROVIDED]` |

### §9.2 Sanal İşlev Arayüzü

`process-isolation.md` L543–L547 (MEVCUT PROJE GERÇEĞİ):

```c
CM_Status CM_Sandbox_Create(const char *root_dir, void **sandbox);
CM_Status CM_Sandbox_Enter(void *sandbox);
CM_Status CM_Sandbox_AddAllowedPath(void *sandbox, const char *path);
CM_Status CM_Sandbox_AddAllowedDevice(void *sandbox, const char *device);
CM_Status CM_Sandbox_Destroy(void *sandbox);
```

> **Boşluk:** Bu arayüzün **Windows uygulaması** (AppContainer / Job Object tabanlı
> izolasyon) hiçbir belgede yoktur. Boşluğa bu belgede `[NOT PROVIDED]` denir;
> AppContainer/CFG/ASLR gibi adlar **depoda geçmediği için buraya yazılmaz**
> (ZERO-HALLUCINATION: API adı uydurulmaz).

### §9.3 Belgenin Kendi İddiası

`process-isolation.md` L624: "Tam sandbox implementasyonu" → **sonraki adım** listesinde.
`index.md` L51: Windows Core modülü durumu → **"Planlandı"**.
İki satır birlikte: **izolasyon modülü tasarlanmış, Windows uygulanmamıştır.**

---

## §10 K6 (Uygulama Güvenliği) Sınırı — `[KAPSAM DIŞI]`

`_backup\...\architecture\k6-guvenlik\` altındaki 16 dosya K6 kapsamını tanımlar:

| Dosya | Başlık | Bu belgeyle ilişki |
|---|---|---|
| `authentication-jwt.md` | Kimlik doğrulama | `[KAPSAM DIŞI]` |
| `csrf-protection.md` | CSRF | `[KAPSAM DIŞI]` |
| `csp-policy.md` | CSP | `[KAPSAM DIŞI]` |
| `encryption-aes256.md` | AES-256-GCM | `[KAPSAM DIŞI]` |
| `password-hashing.md` | Argon2id | `[KAPSAM DIŞI]` |
| `rate-limiting.md` | Rate limit | `[KAPSAM DIŞI]` |
| `audit-logging.md` | Audit log | çapraz: [[windows-performans-ve-gozlemlenebilirlik]] |
| `rbac-authorization.md` | Yetkilendirme | `[KAPSAM DIŞI]` |
| `input-validation.md` | Girdi doğrulama | `[KAPSAM DIŞI]` |
| `session-management.md` | Oturum | `[KAPSAM DIŞI]` |
| `security-headers.md` | Header'lar | `[KAPSAM DIŞI]` |
| `oauth2-integration.md` | OAuth2 | `[KAPSAM DIŞI]` |
| `vault-secrets.md` | Sır yönetimi | `[KAPSAM DIŞI]` |

**Kural:** Bu dosyaların **hiçbiri bu belgede yeniden anlatılmaz**; yalnızca K0↔K6
sınırı için adları geçer. Erişim yolu → `## ÇELİŞKİ / DOĞRULAMA` maddesi 1.

---

## §11 Tehdit → Kontrol Eşlemesi (Kapsam İçi)

| Tehdit (kavramsal) | K0 kontrolü | Kaynak | Etiket |
|---|---|---|---|
| Yetkisiz ayrıcalık artışı | UAC + "gereksiz privilege kapat" | `windows-core.md` L402–L403 | **MEVCUT PROJE GERÇEĞİ** |
| Bellek taşkını / kaynak tüketimi | Job Object CPU/bellek sınırı | L291–L300 | **MEVCUT PROJE GERÇEĞİ** |
| Bellek kilidi ayrıcalığı kötüye kullanımı | `SeLockMemoryPrivilege` yalnız large page | L59 | **MEVCUT PROJE GERÇEĞİ** |
| Paylaşılan nesne üzerine yazma | Güvenlik descriptor | L406 (yalnız not) | `[NOT PROVIDED]` |
| Sürücü kurulumunun yükseltilmesi | `runas` | L313 | **MEVCUT PROJE GERÇEĞİ** |
| Sessiz yetki artışı (debug ayrıcalığı) | — | kaynakta kapatma yok | `⚠️ VERIFICATION REQUIRED` (§4.3) |
| Uygulama-sınıfı saldırılar (XSS/CSRF/SQLi) | **K6** | `k6-guvenlik/*` | `[KAPSAM DIŞI]` |

---

## §12 Gömülü Doğrulanabilir İddialar Tablosu

| # | İddia | Kanıt | Etiket |
|---|---|---|---|
| 1 | `OpenProcessToken` + `TOKEN_ADJUST_PRIVILEGES` kullanılır | `windows-core.md` L58, L322 | **MEVCUT PROJE GERÇEĞİ** |
| 2 | `SeLockMemoryPrivilege` large page için etkinleştirilir | L59 | **MEVCUT PROJE GERÇEĞİ** |
| 3 | `SE_DEBUG_NAME` + `AdjustTokenPrivileges` örneği | L325–L329 | **MEVCUT PROJE GERÇEĞİ** |
| 4 | `ShellExecuteEx` + `runas` + `--install-driver` | L312–L318 | **MEVCUT PROJE GERÇEĞİ** |
| 5 | Job Object CPU `%50` / bellek `512MB` örnekleri | L292, L298 | **ESKİ BELGE İDDİASI** |
| 6 | 5 maddelik güvenlik notu | L400–L406 | **MEVCUT PROJE GERÇEĞİ** |
| 7 | İzolasyon mekanizmaları POSIX (chroot/ns/seccomp) | `process-isolation.md` L13 | **MEVCUT PROJE GERÇEĞİ** |
| 8 | Windows sandbox uygulaması var mı? | — | **YOK → `[NOT PROVIDED]`** |
| 9 | K6 → K0 bağımlılığı KRİTİK yasak | `katman-baglilik-matrisi.md` | **MEVCUT PROJE GERÇEĞİ** |
| 10 | Windows Core modülü durumu | `index.md` L51 = **"Planlandı"** | **MEVCUT PROJE GERÇEĞİ** |

---

## §13 Bağlantı Haritası

| Konu | Giden dosya | Kapsam |
|---|---|---|
| RT öncelik + affinity kodu | [[asio-cekirdek-entegrasyonu]] §9 | K000 |
| Event Log / audit yazımı | [[windows-performans-ve-gozlemlenebilirlik]] | K000 |
| COM/thread API yüzeyi | [[windows-api-yuzeyi]] | K000 |
| Token → bellek yolu (large page) | [[wasapi-ses-yolu-cekirdek]] | K000 |
| Auth/CSRF/CSP/şifreleme | `[KAPSAM DIŞI]` → K6 | K6 |
| Sürücü kurulumu / Donanım anahtarı | `[KAPSAM DIŞI]` → K1, K2 | K1/K2 |
| ADR-010 (auth bypass kararı) | `.ai/.decisions/` — bu belge **karar kaynağı değildir** | K6 |

---

## §14 Gerilim Kaydı (Kapsam İçi)

| # | Gerilim | Durum |
|---|---|---|
| 1 | `SE_DEBUG_NAME` örneği açıkken "gereksiz privilege kapat" notu | `⚠️ VERIFICATION REQUIRED` — kaynak salt-okunur |
| 2 | Job CPU sınırı (`%50`) ↔ RT ses yolu ihtiyacı | `⚠️ VERIFICATION REQUIRED` — birlikte test yok |
| 3 | Paylaşılan nesne (`CoreMusicSharedMemory`) güvenlik descriptor'ı | `[NOT PROVIDED]` |
| 4 | Named Pipe security descriptor API'si | `[NOT PROVIDED]` |
| 5 | AppContainer / CFG / ASLR adları | **depoda geçmiyor → yazılmadı** (ZERO-HALLUCINATION) |

---

## §15 Ölçüllü Kısıtlar Özeti (Karar Tablosu)

| # | Kısıt | Neden ölçülü | Aşılırsa | Bu depoda durum |
|---|---|---|---|---|
| 1 | `SE_DEBUG_NAME` etkinleştirme | En yüksek yetki sınıfı | Süreçler arası yetki gaspı | kod örneği var, kapatma yok → `⚠️ V.R.` |
| 2 | `SeLockMemoryPrivilege` | Bellek kilidi ayrıcalığı | Bellek presleri / DoS | kod örneği var → **MEVCUT PROJE GERÇEĞİ** |
| 3 | `runas` elevation | Yönetici yetkisi | Sessiz yönetici çalıştırma | kod örneği var, hata yolu yok → `[NOT PROVIDED]` |
| 4 | Job Object CPU/bellek sınırı | RT yolu xrun üretir | Ses kesilmesi | birlikte test yok → `⚠️ V.R.` |
| 5 | Paylaşılan bellek nesnesi | Başka süreç yazabilir | Veri bozulması | descriptor yok → `[NOT PROVIDED]` |
| 6 | Registry minimum yetki | ACL/DACL ayrıntısı yok | Yetki genişlemesi | `[NOT PROVIDED]` |
| 7 | Named Pipe security descriptor | API yazılmamış | Yerel erişim genişliği | `[NOT PROVIDED]` |

**Okuma kuralı:** `⚠️ V.R.` = doğrulanmadı · `[NOT PROVIDED]` = kaynakta hiç yok ·
**MEVCUT PROJE GERÇEĞİ** = dosya + satır kanıtlı.

---

## §16 Gerçekleştirme Kontrol Listesi (Kavramsal)

> Bu liste **tarif değildir**; her satır bir doğrulama kapısıdır.

| # | Kontrol | Beklenen durum | Kanıt |
|---|---|---|---|
| 1 | Yalnız gereken ayrıcalık açık mı? | `SeLockMemoryPrivilege` büyük sayfa için; `SE_DEBUG_NAME` **kapalı** | `⚠️ V.R.` (kaynakta kapatma yok) |
| 2 | UAC yalnız sürücü kurulumunda mı? | `runas` yalnız `--install-driver` | **MEVCUT PROJE GERÇEĞİ** |
| 3 | Job sınırı RT ile çelişmiyor mu? | CPU sınırı test edilmiş | `⚠️ V.R.` |
| 4 | Paylaşılan nesne descriptor'lu mu? | Güvenlik descriptor tanımlı | `[NOT PROVIDED]` |
| 5 | Registry/pipe minimum yetki mi? | ACL listesi yazılı | `[NOT PROVIDED]` |
| 6 | Sandbox yolu Windows'ta tanımlı mı? | İzolasyon mekanizması seçilmiş | `[NOT PROVIDED]` (§9) |
| 7 | K6 → K0 doğrudan çağrı yok mu? | Matris yasağı korunmuş | **MEVCUT PROJE GERÇEĞİ** (§3.2) |
| 8 | Audit yolu yazılı mı? | Event Log yazımı | [[windows-performans-ve-gozlemlenebilirlik]] |
| 9 | Öncelik sınıfı + thread önceliği birlikte test edildi mi? | `REALTIME` + `TIME_CRITICAL` | `⚠️ V.R.` (§8) |
| 10 | Gerçek ölçüm/varlık sayımı yapıldı mı? | — | `[NOT PROVIDED]` |

**KPI:** 10 maddenin 10'u kanıtla kapanmadan bu belge "uygulanmış sayılmaz".

---

## §17 İddia → Kanıt Eşiği

```text
Güvenlik iddiası yazılacak
  → dosya yolu + satır var mı?
      EVET → etiket: MEVCUT PROJE GERÇEĞİ
      HAYIR → dış kaynak (Microsoft dokümanı) ile doğrulandı mı?
                  EVET → ARAŞTIRMA REFERANSI (+ tarih)
                  HAYIR → [NOT PROVIDED] veya ⚠️ VERIFICATION REQUIRED
  → "açık/zaaf/aşım" gibi güvenlik sonucu iddiası mı?
      EVET → OWASP/açık tarayıcı kanıtı yoksa YAZMA (ZERO-HALLUCINATION §3)
```

**Bu belgede üretilen yeni güvenlik açığı / sürüm / ayrıcalık başarısı iddiası yoktur.**

---

## §14.1 Eksik Veri Kayıt Defteri

| # | Eksik veri | Neden eksik | Nereye sorulur | Durum |
|---|---|---|---|---|
| 1 | Windows sandbox mekanizması seçimi | hiçbir belgede yok | mimari karar (ADR) | `[NOT PROVIDED]` |
| 2 | Named Pipe / Registry ACL ayrıntısı | yalnız "not" var, API yok | K6 + K0 sahibi | `[NOT PROVIDED]` |
| 3 | Paylaşılan nesne güvenlik descriptor'ı | kod örneğinde yok | uygulama sahibi | `[NOT PROVIDED]` |
| 4 | `runas` hata/kırmızı yolu | örnek başarılı akış-only | uygulama sahibi | `[NOT PROVIDED]` |
| 5 | Job Object CPU sınırının RT etkisi | birlikte test yok | QA ölçümü | `⚠️ VERIFICATION REQUIRED` |
| 6 | `SE_DEBUG_NAME` kapatma adımı | kaynakta yok | güvenlik denetimi | `⚠️ VERIFICATION REQUIRED` |
| 7 | Uygulanan ayrıcalık envanteri (gerçek) | kod yok (`*.cpp` = 0) | uygulama yazıldıktan sonra | `[NOT PROVIDED]` |

> **Kural:** Bu defter, "bilinmeyen" ile "yanlış"ı ayırır. Defterdeki hiçbir satır
> tahminle **doldurulmaz**; doldurma işlemi kanıt (dosya + satır) ile yapılır.

### §14.2 Sonraki Adım (2 dakikadan kısa)

**Yedek `k6-guvenlik\index.md` dosyasını okuyup K6 başlıklarını bu belgenin §10
tablosuyla karşılaştırın** — hangi K6 başlığının canlı vault'ta karşılığı olduğunu
bir bakışta görürsünüz; ardından farkı `log.md`'ye tek satır olarak kaydedin.

---

## ÇELİŞKİ / DOĞRULAMA

> Bu belgede en fazla **5** madde; bu dosya **2** madde taşır (toplam 10 sınırı).
> Düzeltme yapılmaz — yalnız raporlanır.

**1 — K6 zorunlu okuma yolu canlı vault'ta yok**

| Kaynak | Yer | İddia |
|---|---|---|
| `.ai\AGENTS.md` | §24.3 Domain-Based Reading — Security satırı | `architecture/k6-guvenlik/*.md` **zorunlu okuma** |
| Canlı disk (bu görevde ölçülen) | `.ai\architecture\` | glob `*guvenlik*/*.md` → **0 sonuç** |
| Yedek disk | `_backup\arch-2026-10-06_1057\architecture\k6-guvenlik\` | **16 dosya** (`index.md`, `CLAUDE.md` + 14 başlık) |

> **Etki:** Security agent'ın zorunlu okuma listesindeki birinci madde kırık; K6
> kanıtı yalnız backup'ta duruyor.
> **Durum:** `⚠️ VERIFICATION REQUIRED` — klasör ya taşındı ya da yollar
> güncellenmedi; hangisi depoda çözülemedi.

**2 — İzolasyon modülü platform iddiası ile Windows karşılığı arasındaki boşluk**

| Kaynak | Satır | İddia |
|---|---|---|
| `_backup\...\k0-isletim-sistemi\process-isolation.md` | L13 | İzolasyon: `chroot, namespaces, seccomp-bpf` + capability dropping (POSIX) |
| `_backup\...\k0-isletim-sistemi\macos-core.md` | L396 | `com.apple.security.app-sandbox` (macOS sandbox) |
| `_backup\...\k0-isletim-sistemi\windows-core.md` | §11–§12 | Windows tarafında yalnız **Job Objects + UAC** — sandbox yok |
| `_backup\...\k0-isletim-sistemi\index.md` | L51 | Windows Core modülü durumu: **"Planlandı"** |

> **Etki:** Aynı modül üç platformu kapsama iddiasındayken **Windows sandbox
> karşılığı hiçbir belgede yok** → Windows izolasyonu `[NOT PROVIDED]`.
> **Durum:** `⚠️ VERIFICATION REQUIRED` — AppContainer vb. adlar depoda geçmediği
> için uydurulmamış, boşluk boşluk olarak taşınmıştır.

---

## Kaynaklar

Okunan gerçek dosya yolları (bu belgeye gömülü kanıt):

1. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-core.md`
2. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\process-isolation.md`
3. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\macos-core.md`
4. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\index.md`
5. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\README.md`
6. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k6-guvenlik\` (16 dosya — başlıklar)
7. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\katman-baglilik-matrisi.md`
8. `C:\www\coremusic.net\.ai\AGENTS.md`
9. `C:\www\coremusic.net\.ai\CLAUDE.md`
10. `C:\www\coremusic.net\.ai\architecture\k000-windows-core\index.md` (salt-okunur stil referansı)
11. `C:\www\coremusic.net\.ai\architecture\k000-windows-core\windows-api-yuzeyi.md` (salt-okunur stil referansı)

**Doğrulama durumu:** Bu belgede **yeni ayrıcalık/sürüm/açık iddiası üretilmemiştir**.
Depoda `*.cpp`/`*.h` = 0 olduğundan tüm kod blokları **kavramsal iskelettir**;
`⚠️ VERIFICATION REQUIRED` ve `[NOT PROVIDED]` işaretli satırlar gerçekleştirme
öncesi doğrulanmalıdır.
