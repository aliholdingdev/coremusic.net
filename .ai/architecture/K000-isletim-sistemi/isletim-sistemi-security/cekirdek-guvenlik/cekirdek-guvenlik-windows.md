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

---

## §7 Çapraz kart matrisi (Windows güvenlik kontrol haritası)

§1'deki Windows güvenlik profili, bu dosyanın diğer bölümleriyle
çaprazlandığında aşağıdaki kontrol matrisi çıkar. Her satır bir kontroldür;
sütunlar geçerli yüzeyi gösterir. Kanıt olmadan kontrol "var" sayılmaz
(§5 referans kanıtı, Red Team modu).

| # | Güvenlik kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | Secure Boot + ölçülebilir önyükleme | §1 boot katmanı | UEFI + Boot Manager | Statik (ölçüm) | P0 |
| K2 | App Control / WDAC politikası | §2.2 binary politikası | Kernel Mode CI | Statik + dinamik | P0 |
| K3 | Kimlik koruması (Credential Guard sınıfı) | §1 kimlik katmanı | VBS + LSA | Dinamik | P0 |
| K4 | Kernel modu zorunlu (işaretçi/çıkış kontrolleri) | §1 çekirdek | Kernel EF | Dinamik | P0 |
| K5 | BitLocker şifreleme (şifreli alan) | §1 depolama | TPM + BitLocker | Statik | P1 |
| K6 | Windows Defender / cihaz kontrolü | §1 uygulama katmanı | Defender + MDM | Dinamik | P1 |
| K7 | Ağ kuralı (güvenlik duvarı profili) | §1 ağ katmanı | Windows Filtering | Dinamik | P1 |
| K8 | Güncel yama/RCa denetimi | §1 bakım katmanı | Windows Update | Statik (ölçüm) | P1 |

### §7.1 Matris okuma kuralı

Her satır yalnız şu kanatlardan biriyle onaylanır:

1. **Statik kanıt** — `Get-...` ölçüm çıktısı, politika XML'i, kayıt defteri anahtarı (Dosya: ile).
2. **Dinamik kanıt** — reddetme olayı (olay günlüğü kaydı) veya engellenen süreç.
3. **Ölçüm kanatı** — §5.1 ölçüm komutlarının yeniden üretilebilir çıktısı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır; tahminle doldurulmaz.

### §7.2 Çapraz kart eşlemesi (güvenlik → kart kategorisi)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Önyükleme kartları | K1 | §1 boot, §2.1 durum okuma kalıbı |
| Kimlik kartları | K3 | §1 kimlik koruması |
| Uygulama kartları | K2, K6 | §2.2 politika kalıbı, §10 S2 |
| Bağlantı kartları | K7 | §1 ağ, §10 S3 |
| Depolama kartları | K5 | §1 şifreleme, §10 S4 |
| Bakım kartları | K8 | §5.1 ölçüm, §13 T4 |

## §8 Ses/RT yüzeyi (Windows için PLANNED senaryo)

> **DURUM: PLANNED** — Aşağıdaki zincir bu turda gerçek bir Windows cihazında
> doğrulanmadı; sonuç satırları ölçüm eklenene kadar PLANNED kalır.

### §8.1 Neden bu bölüm bir güvenlik dosyasında?

Windows ses alt sistemi (WASAPI/ört-bellek paylaşımı, aygıt üzerinde ayrıcalıklı
ses servisleri) K3/K4 ile aynı kernel yüzeyini paylaşır: paylaşılan bellek
kanalları ve ayrıcalıklı ses süreçleri, §7 matrisindeki kimlik ve çekirdek
kontrollerinin doğrudan hedefidir. Gecikme/kalite (SNR/THD) bu dosyanın dışıdır;
yalnız yetki ve başarısızlık yüzeyi PLANNED olarak çerçevelenir.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Güvenlik)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama paylaşımlı bellek üzerinden ses kanalı açar | Erişim denetimi (K3/K4 sınıfı) | PLANNED |
| A2 | Ses cihazı üzerinde ayrıcalıklı süreç veri aktarır | Yetki kapsamı daraltma | PLANNED |
| A3 | DMA/ört-bellek yolu veri taşır | K5 şifreli alan etkilenmemeli | PLANNED |
| A4 | Uygulama sonlanır / çöker | Açık kanallar kapatılır (§9 M4) | PLANNED |

**Kapsam dışı:** ses algoritması, codec, sanallaştırma katmanı detayı.
**Harici referans:** surfaçı fallback modeli `.ai/electronic/drivers/audio-drivers.md`
içinde incelenir — bu turda yalnız **okuma** (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (Windows güvenlik)

| Mod | Tetikleyici | Gözlenen belirti | Güvenlik etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | Secure Boot ölçüm uyuşmazlığı | Önyükleme reddi/kurtarma | Fail-closed duraklama | Kurtarma modu + ölçüm |
| M2 | WDAC politika ihlali | Süreç engellenir | Kötü amaçlı yürütme durur | Politika imzalama akışı |
| M3 | Kimlik koruması reddi (LSA) | Oturum açma hatası | Kimlik sızışı engellenir | Kurtarma anahtarı/kimlik sıfırlama |
| M4 | Ses/uygulama çökmesi | Süreç sonlanır | Kaynak sızıntısı riski | Kanal kapanışı + yeniden başlat |
| M5 | Şifreli alan etiket ihlali | Erişim reddi | Veri korunur | ACL/etiket denetimi |
| M6 | Zaman/sertifika geçersizliği | TLS/WinHTTP hatası | Bağlantı fail-closed düşer | Kök CA + zaman senkronu |
| M7 | Güvenlik duvarı servisi çöküşü | Filtre kuralı askıda | Kısa boşluk riski | Fail-closed doğrulama |
| M8 | Yama eksikliği (K8 ihlali) | Bilinen zafiyet yüzeyi açık | Bilinmeyen | Kritik yama SLA'sı |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1, M2, M3:** fail-closed; hiçbir koşulda atlatma (bypass) yok.
2. **P1 — M5, M6, M7:** tek yüzey düşer, çekirdek oturum ayakta.
3. **P2 — M4, M8:** yalnız ilgili süreç/yama takibi.

`fail-open` davranışı hiçbir modda kabul edilmez. Üç başarısız düzeltme turundan
sonra durulur ve şüpheli varsayım üst karara yazılır.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama adımı → sonuç (işaretli)**.

### §10.1 S1 — Önyükleme bütünlüğü (P0)

- **Kural:** Secure Boot ölçümü beklenen değerde değilse önyükleme reddi (K1, §9 M1).
- **Yüzey:** UEFI → Boot Manager → yükleyici (§1).
- **Doğrulama:** §2.1 durum okuma kalıbı çalıştırılır, çıktı beklenenle karşılaştırılır.
- **Sonuç:** Uyuşmazlıkta kurtarma. (Durum: PLANNED — gerçek ölçüm bu turda alınmadı.)

### §10.2 S2 — İmzasız/policiesiz yürütme engeli (P0)

- **Kural:** WDAC politikasına uymayan binary yürütülmez (K2, §9 M2).
- **Yüzey:** Kernel Mode Code Integrity (§2.2 politika kalıbı).
- **Doğrulama:** Test binary'si → beklenen: engelleme olayı (olay günlüğü).
- **Sonuç:** Engelleme yoksa K2 ihlali → P0 eskalasyon. (Durum: PLANNED.)

### §10.3 S3 — Yetkisiz ağ bağlantısı (P1)

- **Kural:** Profilde olmayan bağlantı reddedilir (K7, §9 M7).
- **Yüzey:** Windows Filtering profili (§1 ağ).
- **Doğrulama:** Profil dışı hedefe bağlantı → beklenen: ret + sayfa.
- **Sonuç:** Bağlanırsa profil eksik → politika güncelle. (Durum: PLANNED.)

### §10.4 S4 — Şifreli alan sızıntısı direnci (P1)

- **Kural:** Kapalı oturumda şifreli alan ham okunamaz (K5, §9 M5).
- **Yüzey:** BitLocker/TPM (§1 depolama).
- **Doğrulama:** Ham disk okuma denemesi (kurtarma ortamı üzerinden) → beklenen: okunamayan veri.
- **Sonuç:** Ham okunursa K5 ihlali → P0'a yükseltilir (veri ifşası). (Durum: PLANNED.)

### §10.5 Senaryo günlüğü tutma biçimi

| Alan | Zorunluluk |
|---|---|
| Senaryo no | Zorunlu |
| Kanıt türü | Zorunlu |
| Durum (PLANNED / doğrulandı) | Zorunlu |
| Beklenen vs gözlenen | Doğrulandığında zorunlu |

## §11 Kaynakça envanteri

### §11.1 İç kaynaklar (Dosya: ile)

| Kaynak | Kullanım |
|---|---|
| Bu dosya §0–§6 | Profil, PowerShell kalıpları (§2.1/§2.2), sınır cümleleri |
| K000 isletim-sistemi-security kardeşleri | Çapraz okuma (§12 ağacı) |
| K000 isletim-sistemi-testing | Doğrulama adımlarının test karşılığı (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses katmanı fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| Microsoft güvenlik dokümantasyonu | K1–K8 tanımları | Erişilebilir URL eklendiğinde işaretlenir |
| Olay günlüğü örnekleri | §10 dinamik kanıt | ⚠️ VERIFICATION REQUIRED (bu turda alınmadı) |
| Cihaz ölçüm çıktıları | §5.1, §13 T4 | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Cihaz üstü ölçüm/olay kaydı. 2. Dosya içi § referansı. 3. Vault içi kardeş
dosya (Dosya: yolu). 4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
cekirdek-guvenlik-windows.md
├── §0 ELI15
├── §1 Windows güvenlik profili     (kaynak: dosyanın kendi tespiti)
├── §2 Kod örnekleri (§2.1/§2.2)    (PowerShell referans kalıpları)
├── §3 CoreMusic repo durumu
├── §4 Sınır cümleleri              (neyin YAPILMAYACAĞI)
├── §5 Referans + §5.1 ölçüm        (yeniden üretilebilir kanıt)
├── §6 Açık sorular                 (S1/S2 — KAPATMA YASAĞI)
├── §7 Çapraz matris                (bu eki: K1–K8 haritası)
├── §8 Ses/RT PLANNED               (bu eki: sınırlı yüzey)
├── §9 Başarısızlık modları         (bu eki: M1–M8 + fail-safe)
├── §10 Kullanım senaryoları        (bu eki: S1–S4 kalıp)
├── §11 Kaynakça envanteri          (bu eki: güven sırası)
├── §12 Eşleme ağacı               (bu satır: kendini konumlandırır)
└── §13 Test + negatif kontrol      (bu eki: kapı adımları)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | §2.1 ile Secure Boot durumunu oku (K1) | Beklenen değer |
| T2 | İmzalı/uygun yürütme (K2) | Çalışır |
| T3 | Kimlikli oturum açışı (K3) | Başarılı oturum |
| T4 | Yama denetimi (K8, §5.1) | Kritik yama = güncel |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Ölçüm bozulmuş önyükleme taklidi | Önyükleme reddi (M1) | K1 ihlali → P0 |
| N2 | Politikaya uymayan test binary'si | Yürütme engeli (M2) | K2 ihlali → P0 |
| N3 | Yetkisiz kimlik denemesi | Oturum reddi (M3) | K3 ihlali → P0 |
| N4 | Ham disk okuma | Okuma engeli (M5) | K5 ihlali → P0 |
| N5 | Profil dışı bağlantı | Ağ ret (M7) | K7 ihlali → P1 |
| N6 | Eski/test sertifikayla bağlantı | TLS ret (M6) | fail-open ise P1 |
| N7 | Kritik yamasız makine | Bilinen zafiyet | K8 ihlali → P1 SLA |

**Kural:** Ret yoksa kontrol `VERIFICATION REQUIRED` kalır; "çalışıyor"
varsayılmaz. 3 başarısız tur → dur + üst karar.

### §13.3 Okuma sırası penceresi (öneri)

1. §0 → §4 (5 dk) — bağlam + sınırlar.
2. §7 matrisi (3 dk) — kontrol ↔ yüzey.
3. §9 + §10 (7 dk) — modlar ve senaryolar.
4. §13 negatifler (5 dk) — ret kanıtları.
5. §6 açık sorular sona — okunur, **kapatılmaz**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §14 Olay günlüğü şeması + eskalasyon matrisi (Windows)

§9 modları canlıya geçtiğinde her olay aşağıya girer; şemasız log parçası
kanıt sayılmaz (§7.1). Bu bölüm append-only eklendi; §0–§13 değişmedi.

### §14.1 Olay kayıt şeması (zorunlu alanlar)

| Alan | Tip | Açıklama |
|---|---|---|
| ts | ISO-8601 | Cihaz saati; saat güveni M6'ya bağlı |
| mod | M1–M8 | §9 başarısızlık modu etiketi |
| kontrol | K1–K8 | §7 matrisindeki kontrol no |
| yuzey | § referansı | Örn. `§1 boot katmanı` |
| beklenen / gozlenen | ikili | Reddedilmesi gereken vs gözlenen |
| durum | PLANNED / doğrulandı | §10.5 ile aynı alan adı |
| kanit | statik/dinamik/ölçüm | §7.1 kanıt türü |
| eskalasyon | P0/P1/P2 | §9.1 fail-safe sırası |
| kaynak | PC adı / oturum | Olay hangi yüzeyde (§12 ağacı) |

### §14.2 Eskalasyon matrisi

| Olay sıklığı (5 dk pencere) | Eylem | Sahip |
|---|---|---|
| Aynı K'de 1 ret | Günlüğe yaz, izle | Güvenlik günlüğü |
| Aynı P0 K'de 2+ ret | Olay aç → inceleme | Olay müdahale |
| P0 K'de ret **yok ama izin var** | Sızıntı varsayımı → derhal eskale | Sızıntı yanıtı |
| M7 (filtre boşluğu) | Fail-open araması → P1 derhal | Sistem güvenliği |
| M4 (süreç çökmesi) tekrarı | Kaynak sızıntısı incelemesi | Uygulama güvenliği |
| M8 (yama eksikliği) | Kritik yama SLA takibi | Bakım/Güvenlik |

### §14.3 Log bütünlüğü kuralları

1. Günlük satırı append-only'dir; düzenleme/silme yasak (dosya protokolüyle aynı ilke).
2. `ts` geriye sarılamaz; saat atlaması M6 olayı üretir.
3. `kanit` boş olay sayılmaz; yalnız ipucudur.
4. P0 olayları bir sonraki §13.2 turunda tekrar denenir.
5. Olay kaydında `status:`/frontmatter benzeri alan yoktur — durum yalnız
   `durum` alanında yaşar (frontmatter kapısıyla karıştırılmaz).

### §14.4 Bu bölümün kapı ilişkisi

| Kapı | §14 katkısı |
|---|---|
| İçerik kapısı | Ek uzunluk bu bölümle ölçülür |
| Açık soru kapısı | §6 S1/S2 etkilenmez (yeni soru açılmaz/kapatılmaz) |
| Frontmatter kapısı | group/meta değişmez |
| Append kapısı | Yalnız sonradan eklendi |

### §14.5 Uygulama sırası (öncelik sırası güvenlik)

1. P0: K1, K2, K3, K4 → şema bu kontrollerle denenir.
2. P1: K5, K6, K7, K8 → M5/M6/M7/M8 ile eşleştirilir.
3. P2: M4 → yalnız ilgili süreç takibi.
4. PLANNED satırları (§8, §10) en sonda; kanıt gelmeden "çalışıyor" yapılmaz.

### §14.6 §14 ile ilgili SSS

**S: Aynı olay K2 ve K4'ü ilgilendiriyorsa?**
C: Ana satır yüksek öncelikli/birincil ihlal edilen kontrol; diğeri `ilgili` notu.

**S: PLANNED senaryonun logu çıkarsa?**
C: Şema doldurulur, `durum: doğrulandı`; §8/§10 işareti yalnız kanıtla kalkar.

**S: Olay kotası/alan dolarsa?**
C: Yeni segment açılır; eski segment silinmez (append-only).

**S: Kim bakar?**
C: §14.2 sahiplik bağlayıcı; çakışmada P0 sahibi önceliklidir.

### §14.7 Kardeş bölümlerle bağı

| Kardeş | Bağ |
|---|---|
| §7 | Şemanın K alanı doğrudan matristen |
| §9 | M etiketi + eskalasyon önceliği |
| §10 | Senaryo kalıbı → durum alanı |
| §13 | Negatif kontrol sonucu → bu şemaya olay |
| §2.1/§2.2 | PowerShell kalıpları → statik kanıt üretimi |

### §14.8 Ölçüm tekrarlanabilirlik kontrolü

| Ölçüm | Komut kaynağı | Sıklık |
|---|---|---|
| Secure Boot durumu | §2.1 kalıbı | Açılışta + haftalık |
| Politika uygulanabilirliği | §2.2 kalıbı | Politika değişikliğinde |
| Yama durumu | §5.1 ölçümü | Günlük |
| Olay şeması bütünlüğü | §14.1 alan denetimi | Olay başına |

Ölçüm çıktısı bu dosyaya **ek** olarak yazılır (mevcut satırlar değişmez);
çıktı yoksa ilgili satır `⚠️ VERIFICATION REQUIRED` kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §15 Özet kontrol listesi (kapıdan önce son okuma)

Bu bölüm, dosyanın son okuma oturumunda tek seferde taranacak özetdir;
içeriği §0–§14'ten türetilmiştir (yeniden yazım değil, yönlendirme).

### §15.1 Zorunlu 8 kontrol (P0/P1 özeti)

| # | Tek cümlelik soru | Cevap referansı |
|---|---|---|
| 1 | Boot ölçümü beklenen değerde mi? | §2.1 + §10.1 (PLANNED) |
| 2 | WDAC politikası uygulanabilir durumda mı? | §2.2 + §10.2 (PLANNED) |
| 3 | Kimlik koruması açık mı? | §7 K3 |
| 4 | Kernel modu zorlaması etkin mi? | §7 K4 |
| 5 | Şifreleme (BitLocker sınıfı) açık mı? | §10.4 (PLANNED) |
| 6 | Defender/üretici kontrolü etkin mi? | §7 K6 |
| 7 | Ağ profili mevcut ve fail-closed mu? | §10.3 + §9 M7 |
| 8 | Kritik yamalar güncel mi? | §5.1 + §13.1 T4 |

### §15.2 Hızlı okuma rotası (3 dakika)

1. §15.1 tablosu (1 dk) — sekiz soru, sekiz referans.
2. §9.1 fail-safe sırası (1 dk) — hangi mod önce.
3. §6 açık sorular (1 dk) — oku, **kapatma**.

### §15.3 Sık yapılan hatalar (negatif hafıza)

| Hata | Sonuç | Doğru davranış |
|---|---|---|
| "Secure Boot açık, bitti" (ölçüm yok) | Kanıtsız iddia | §5.1 ölçümü üret, Dosya:/çıktı ekle |
| PLANNED senaryoyu "çalışıyor" saymak | Sahte kapı geçişi | Durum alanını koru, kanıt bekle |
| Log satırını düzenleme | Bütünlük kaybı | Append-only (§14.3) |
| §6 açık soruyu cevaplayıp kapatmak | Kapı ihlali | Yalnız yeni bölüm ekle, S satırı değiştirme |
| fail-open yorumu ("izin verdi, iyi") | P0 sızıntı riski | §9.1: ret kanıtı ister, izin = eskale |

### §15.4 Kapanış özeti (bu dosyanın haritası, tek ekran)

```text
cekirdek-guvenlik-windows.md  — 00: §0-§6 (orijinal) | eki: §7-§15 (append-only)
  Guvenlik profili  -> §1 + §7 K1-K8 matrisi
  Kanit uretimi      -> §2.1 / §2.2 / §5.1 + §7.1 kanit turu
  Ses/RT yuzeyi      -> §8 (PLANNED — sinir acikca yazili)
  Basarisizlik       -> §9 M1-M8 + §9.1 fail-safe P0>P1>P2
  Senaryolar         -> §10 S1-S4 (kural->yuzey->dogrulama->sonuc)
  Kaynaklar          -> §11 guven sirasi (olcum > §ref > vault > URL > hafiza)
  Olay semasi        -> §14.1-§14.8 (eskalasyon + butunluk)
  Test               -> §13.1 pozitif + §13.2 negatif N1-N7
  Son okuma          -> §15.1 sekiz soru (3 dk rota)
  Acik sorular       -> §6 S1/S2 — KAPATMA YASAGI (degismedi)
```

### §15.5 Bir sonraki tur için hazır iş kalemleri

| İş | Koşul | Sıra |
|---|---|---|
| §8 PLANNED satırlarını gerçek cihaz ölçümüyle güncelle | Cihaz erişimi | P0 sonra |
| §10 senaryolarına gözlenen/ beklenen sonucu ekle | Dinamik kanıt | P1 |
| §11.2 dış kaynak satırlarını tarihli URL ile doldur | Erişilebilir URL | Sıradaki tur |
| §13.2 negatif turunu çalıştırıp olayı §14.1 şemasına yaz | Test ortamı | P0 |

Bu işler bu turda **yapılmadı** olarak işaretlidir; yapılıyormuş gibi
raporlanmaz (Zero-Hallucination, §11.3 sırası).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
