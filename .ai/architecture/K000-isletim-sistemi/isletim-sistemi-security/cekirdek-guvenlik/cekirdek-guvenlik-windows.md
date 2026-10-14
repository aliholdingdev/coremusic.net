---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-windows
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
evidence:
  - https://learn.microsoft.com/en-us/windows-hardware/design/device-experiences/oem-secure-boot
  - https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business
  - https://learn.microsoft.com/en-us/windows/security/threat-protection/device-guard/device-guard-deployment-guide
  - https://learn.microsoft.com/en-us/windows/security/hardware-security/tpm/trusted-platform-module-overview
  - https://learn.microsoft.com/en-us/windows/security/threat-protection/windows-defender-application-guard/wd-app-guard-overview
  - https://learn.microsoft.com/en-us/windows-hardware/drivers/develop/
  - https://learn.microsoft.com/en-us/powershell/module/secureboot/confirm-securebootuefi
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligi 2026-10-14 taramasinda 0 isabet."
  - "2026-10-14: 7 kaynak HEAD 200 + webfetch icerik okundu; surucu/WDAC glob (sys/inf/cat/vcxproj/efi/cip/p7b/wim) 0 isabet; PowerShell guvenlik komutu cagrisi 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# cekirdek-guvenlik — Windows OS Kartı

## §0 ELI15

Bu kart, Windows'ta **çalışan işletim sisteminden önce** başlayan güvenlik
halkasını taşır: Secure Boot ile firmware'in imza doğrulaması, TPM ile
donanım köklü ölçüm/anahtar saklama, App Control (eski adıyla Device Guard /
WDAC) ile hangi kodun çalışabileceği ve WDK ile sürücü paketi imzalama zinciri.
Kart, bu mekanizmaların **CoreMusic repoda karşılığının olmadığını** kanıtlayan
negatif kanıt dosyasıdır; kod bloklarının tamamı `kaynak: upstream docs` ile
imzalıdır ve hedef mimari kararı S1'e (`[UNKNOWN]`) bağlıdır.

## §1 Windows güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Secure Boot (UEFI) | Firmware, açılışta her parçanın imzasını doğrular: UEFI sürücüleri (Option ROM), EFI applications, işletim sistemi; imzalar geçerliyse boot devam eder | Boot zincirinin **kökü** — ilk güven | Yüksek (firmware) |
| İmza veritabanları `db` / `dbx` / `KEK` / `PK` | `db` onaylı imzaları, `dbx` iptal edilenleri listeler; bir imza ikisinde de varsa **`dbx` kazanır**; `KEK` imza veritabanını güncelleme anahtarlarını taşır | Hangi kod boot edebilir | Yüksek (firmware) |
| Firmware signing şartı | Tüm firmware bileşenleri en az **RSA-2048 + SHA-256** ile imzalanmalı; rollback (eski sürüme düşme) koruması zorunlu | İmza gücü eşiği | Yüksek (donanım şartı) |
| Boot zinciri doğrulama | Güç açıkken firmware, sıradaki tüm görüntülerin imzasını algoritma politikasına göre doğrular — Windows Boot Manager'a kadar | Zincirin kesintisizliği | Yüksek |
| TPM | Boot kodunu ölçüp TPM'e kaydeder; anahtar üretir/saklar/limitler; TPM-dışı export edilemeyen anahtar; çok yanlış denemede dictionary attack logic devreye girer | Donanım köklü kanıt + anahtar koruması | Yüksek (donanım) — Windows 10/11 TPM'i otomatik başlatır |
| App Control for Business (WDAC) | Hangi imzalı/kural uyan kodun çalışacağını politika ile belirler; **audit mode → enforcement** sırası; imzalı politika ile değişiklik koruması | Kullanıcı-uzayı kod denetimi (boot'tan sonra 2. halka) | Yüksek (politika) |
| Device Guard dağıtım kılavuzu | Politika XML'ini `ConvertFrom-CIPolicy` ile binary (`.cip` / `SiPolicy.p7b`) çevirir; deployment ring'leri; önce audit, sonra enforce | Politika **dağıtım** prosedürü | Orta (süreç) |
| Microsoft Defender Application Guard | Güvenilmeyen site/dosyayı Hyper-V kapsayıcısında izole eder | Geçici izolasyon — **kullanım dışı** | Kullanım dışı (deprecated; Windows 11 24H2'ten itibaren yok) |
| WDK sürücü paketi zinciri | WDK 8'den beri Visual Studio ile entegre; MSBuild ile kodlama/paketleme/dağıtım; build süreci sürücü paketini **otomatik oluşturur ve imzalar** | Kernel modül/sürücü imzalama **aracı** zinciri | Orta (araç) — politika kararı `UNKNOWN` |

**Okuma:** 9 satırın 8'i "yüksek/orta" güvenle upstream davranışı tarif eder;
1'i (MDAG) **kullanım dışıdır** ve kartta pozitif öneri olarak taşınmaz.
Sürücü imzalama satırında yalnızca **araç zinciri** doğrulanmıştır — hangi
imza politikasının hedef mimaride geçerli olduğu `UNKNOWN` (S1).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki blokların **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` imzalı referans kalıplarıdır; repo'da
> karşılığı §3'te negatif kanıt olarak ölçülür. Kopyalamadan önce S1 kararı gerekir.

### §2.1 — Secure Boot durumu okuma kalıbı (referans, PowerShell)

```powershell
# kaynak: upstream docs - learn.microsoft.com/powershell/module/secureboot/confirm-securebootuefi
# Secure Boot durumunu okur; PowerShell yonetici modunda calismalidir
Confirm-SecureBootUEFI
# ornek cikti: True
# desteklenmeyen platform (BIOS/non-UEFI): "Cmdlet not supported on this platform."
# yonetici olmayan oturum: "Unable to set proper privileges. Access was denied."
```

| Sınıf | R (referans — upstream docs) |
|---|---|
| Repo karşılığı | YOK (`Confirm-SecureBootUEFI` çağrısı 721 kaynak dosyada 0 isabet) |
| İddia sınırı | Bu blok yalnızca **durum okur**; politika değiştirmez |
| Yorum kuralı | Çıkış `True` değilse sebebi `UNKNOWN` sayılır, uydurulmaz |
| Bağımlılık | UEFI donanımı + yönetici modu |

### §2.2 — App Control politikasını binary'ye çevirme kalıbı (referans, PowerShell)

```powershell
# kaynak: upstream docs - device-guard-deployment-guide
# XML politikayi binary formata cevirir - politika onayi S1'e baglidir
$AppControlPolicyXMLFile = $env:USERPROFILE + "\Desktop\MyAppControlPolicy.xml"
[xml]$AppControlPolicy = Get-Content -Path $AppControlPolicyXMLFile
if (($AppControlPolicy.SiPolicy.PolicyID) -ne $null)
{
    $PolicyID = $AppControlPolicy.SiPolicy.PolicyID
    $PolicyBinary = $PolicyID+".cip"
}
else
{
    $PolicyBinary = "SiPolicy.p7b"
}
ConvertFrom-CIPolicy -XmlFilePath $AppControlPolicyXMLFile -BinaryFilePath $env:USERPROFILE\Desktop\$PolicyBinary
```

| Sınıf | R (referans — upstream docs) |
|---|---|
| Repo karşılığı | YOK (`.cip` / `SiPolicy.p7b` / `ConvertFrom-CIPolicy` repoda 0 isabet) |
| Dağıtım sırası | Upstream: **önce audit mode**, sonra enforce (kural ihlali beklenmeden enforce yasak) |
| İmza | Politika değişiklikleri imzalı politika ile korunabilir (upstream başlığı) |
| Kapı | Bu repo'nun hiçbir CI/adımında bu komut **koşmaz** |

## §3 CoreMusic repo durumu

| Yüzey | Repo karşılığı | Not |
|---|---|---|
| Secure Boot policy / boot anahtarı | → YOK (negatif kanıt, 2026-10-14) | `.efi`/boot policy glob 0; firmware katmanı bu repoda taşınmaz |
| WDAC / App Control politikası (`.cip`, `SiPolicy.p7b`, politika XML'i) | → YOK (negatif kanıt, 2026-10-14) | `*.sys,*.inf,*.cat,*.vcxproj,*.efi,*.cip,*.p7b,*.wim` glob = **0** (vendor/node_modules hariç) |
| TPM kullanımı (ölçüm/anahtar) | → YOK (negatif kanıt, 2026-10-14) | TPM çağrısı/TPM politikası içeren kaynak dosya 0 |
| Kernel modül / sürücü imzalama (WDK) | → YOK (negatif kanıt, 2026-10-14) | Sürücü projesi (`.vcxproj`/`.inf`) glob = 0; WDK aracı zinciri repoda başlamamış |
| PowerShell güvenlik komutu çağrısı (`Confirm-SecureBootUEFI`, `ConvertFrom-CIPolicy`, `Get-Tpm`, `icacls`, `Set-Acl`) | → YOK (negatif kanıt, 2026-10-14) | 721 PHP/JS/CSS/SQL dosyasında **0 isabet** |
| Boot zinciri kanıtı (`*.ko`/`*.efi`/kernel config eşdeğeri) | → YOK (negatif kanıt, 2026-10-14) | K000 Linux kartları için de aynı durum; bu kart Linux'a taşımaz |
| Session dizin izni (OS-yakın tek uygulama kanıtı) | `shared/src/Session/SessionConfig.php:20,35` → `0750` | **P sınıfı** — ama bu **POSIX DAC** ayarıdır; Secure Boot/WDAC halkası **değildir**, §4 sınırı |
| Uygulama güvenlik başlıkları (`.htaccess`) | `home.coremusic.net/.htaccess:10` → `X-Frame-Options "DENY"` | **P sınıfı** — uygulama/tarayıcı katmanı (K009), OS boot güvenliği değil |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js,*.css,*.sql |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }
($src | Select-String -Pattern 'Confirm-SecureBootUEFI|ConvertFrom-CIPolicy|Get-Tpm|SecureBoot').Count   # beklenti: 0

Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.sys,*.inf,*.cat,*.vcxproj,*.efi,*.cip,*.p7b,*.wim |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }                          # beklenti: 0 sonuc
```

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Windows'un dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; Windows platform geneli -> `isletim-sistemi-turu/windows`.

- **Boot ≠ uygulama:** Bu kart Secure Boot/TPM/WDAC zincirini taşır; uygulama
  katmanının `0750` / `X-Frame-Options` kanıtları bu halkanın **yerine geçmez**.
- **Araç ≠ politika:** WDK'nın sürücüyü imzalaması bir **araç yeteneğidir**;
  hangi imza politikasının geçerli olduğu S1'e kadar `UNKNOWN` sayılır.
- **Deprecated taşınmaz:** MDAG (Application Guard) upstream'te kaldırılmıştır;
  bu kartta "öneri" olarak sunulmaz, yalnız durum satırı olarak kalır.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://learn.microsoft.com/en-us/windows-hardware/design/device-experiences/oem-secure-boot | Firmware, boot yazılımının imzasını doğrular; `db`/`dbx`/`KEK` veritabanları; RSA-2048 + SHA-256; rollback koruması | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business | App Control politika tasarımı, audit/enforcement mode, imzalı politika, kod imzasıyla politika basitleştirme | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/security/threat-protection/device-guard/device-guard-deployment-guide | `ConvertFrom-CIPolicy` XML→binary; deployment ring; önce audit, sonra enforce | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/security/hardware-security/tpm/trusted-platform-module-overview | TPM kripto-işlemcisi; boot ölçümleri; export edilemeyen anahtar; dictionary attack logic; Win10/11 otomatik başlatma | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/security/threat-protection/windows-defender-application-guard/wd-app-guard-overview | Hyper-V kapsayıcı izolasyonu; **deprecated** — Windows 11 24H2'ten itibaren yok | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows-hardware/drivers/develop/ | WDK 8'den beri Visual Studio entegrasyonu; MSBuild; build süreci sürücü paketini otomatik oluşturur ve imzalar | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/powershell/module/secureboot/confirm-securebootuefi | `Confirm-SecureBootUEFI` Boolean döndürür; yönetici modu zorunlu; desteklenmeyen platform mesajı | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `powershell/module/tpm/get-tpm` → 404
> (2026-10-14); herhangi bir TPM PowerShell bloğu bu yüzden **üretilmedi**.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (Secure Boot/TPM/WDAC) hedef mimaride devreye alınıp alınmayacağı; sürücü imzalama politikası seçimi | **[UNKNOWN]** — kapatılmaz; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (boot zinciri mi, uygulama mı) | **[UNKNOWN]** — kapatılmaz; K009/K010 ayrım çizgisiyle birlikte değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
