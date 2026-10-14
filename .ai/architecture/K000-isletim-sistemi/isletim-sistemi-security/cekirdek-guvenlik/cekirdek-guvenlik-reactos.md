---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-reactos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
evidence:
  - https://reactos.org/
  - https://reactos.org/architecture/
  - https://web.archive.org/web/20260928150412/https://reactos.org/wiki/ReactOS
  - https://web.archive.org/web/20250912212042/https://reactos.org/wiki/Ntoskrnl.exe
  - https://github.com/reactos/reactos/blob/master/README.md
  - shared/src/Session/SessionConfig.php:20
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yoklugu 2026-10-14 taramasinda 0 isabet."
  - "2026-10-14: reactos.org/wiki/* canli adresleri Anubis 1.19.1 challenge dondurdu (HTTP 200, 4188 bayt); wiki metni Wayback arsip kopyasindan okundu, satir satir §5'te."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Cekirdek-Guvenlik — ReactOS OS Kartı

## §0 ELI15

Bu kart **çekirdek-güvenlik** alt-dalının ReactOS sürümüdür: ReactOS'ta
çekirdek neyi içeriyor, önyükleme/güvenlik durumu nedir ve olgunluk sınırı
nerededir — hepsi **2026-10-14'te okuduğum resmi ReactOS kaynaklarına**
bağlıdır. Kaynakta olmayan hiçbir özellik burada "var" diye yazılmaz;
okunamayan her satır `UNKNOWN` kalır.

> **Erişim dürüstlüğü (önce oku):** `reactos.org/wiki/*` canlı adresleri
> 2026-10-14'te **Anubis 1.19.1 bot-challenge** sayfası döndürüyor (HTTP 200,
> içerik challenge metni). Wiki içeriği bu nedenle **Wayback Machine arşiv
> kopyasından** okunmuştur; hangi iddianın hangi URL'den geldiği §5'te yazılıdır.

> **Öncelikli gerçek:** ReactOS bir **NT uyumluluk** projesidir; README
> kendisini **"Alpha quality operating system"** olarak tanımlar ve veri
> kaybı uyarısı yapar. Bu kart "güvenli" iddiasını **kaynağın söylediği
> kadar** taşır: wiki "designed for high security" der, kaynak olgunluk
> sınırını **alpha** olarak koyar — beta değildir.

## §1 ReactOS güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| NT çekirdek (`ntoskrnl`) | "complete implementations of the Object manager, IO manager, Executive, NT thread scheduler" | çekirdek uyumluluk iskeleti | Yüksek (architecture/ tam metin) |
| HAL | çekirdek ile donanım arasını ayırır; wiki: "porting ReactOS to other architectures only involves porting the hardware abstraction layer" | platform soyutlama | Yüksek (wiki/ReactOS arşiv) |
| Kendi NT bootloader'ı | "our own NT bootloader which is able to boot both the ReactOS and Windows kernels" | önyükleme durumu | Yüksek (architecture/ tam metin) |
| Hedef NT sürümü | "The ReactOS kernel currently targets NT 5.2 and supports both x86 and x64 platforms" | uyumluluk hedefi | Yüksek (architecture/ tam metin) |
| Security Reference Monitor | "controls the access of resources or objects by the means of access control lists" | çekirdek erişim denetimi | Yüksek (wiki/Ntoskrnl.exe arşiv) |
| Object Manager | "all other executive sub-systems must go through in order to have access to ReactOS resources" | nesne/yönetim geçidi | Yüksek (wiki/Ntoskrnl.exe arşiv) |
| Executive + Token | "Tokens (managed by the Security Subsystem)" | yetki bağlamının çekirdekte tutulması | Orta (Exa indeks — canlı sayfa challenge) |
| NT hizmet süreçleri | winlogon · lsass · smss · SCM; "run with higher privileges than regular applications" | çekirdek-üstü güvenlik yüzeyi | Yüksek (architecture/ tam metin) |
| Güvenlik varsayılanları | "ReactOS will incorporate proper default security settings" | tasarım iddiası (hedef) | Orta (niyet cümlesi, uygulama kanıtı yok) |
| Secure Boot / bootloader imzası | — | önyükleme güvenliği | **UNKNOWN** (kaynakta geçmiyor) |
| KASLR · SMEP/SMAP · modül imzası | — | bellek/çalışma-zamanı koruması | **UNKNOWN** (kaynakta geçmiyor) |
| Olgunluk sınırı | README: "Alpha quality" · wiki Versions: 0.4 serisi alpha'nın sonu, 0.5 serisi beta hedefi | güvenlik olgunluğu | Yüksek (README tam metin) + Orta (Versions: indeks) |
| Sürüm durumu | ana sayfa: "Introducing ReactOS 0.4.16" (29 Ağustos 2026) | bulunulan nokta | Yüksek (reactos.org/ tam metin) |

**Okuma:** 13 satırın 9'u doğrudan okunmuş metne, 1'i indekse, 3'ü
`UNKNOWN`'a bağlıdır. **`UNKNOWN` satırları bu kartın bilinmeyenleridir;
doldurulmaz.**

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` ile imzalı ReactOS/NT kaynak **alıntısı veya
> özetidir**; repo'da karşılığı yoktur (§3 negatif kanıt).

### §2.1 — NT çekirdek ve Executive yapı taşları (alıntı)

```text
# kaynak: upstream docs - https://reactos.org/architecture/ + https://web.archive.org/web/20250912212042/https://reactos.org/wiki/Ntoskrnl.exe
# NT Kernel (ReactOS Architecture sayfasi, 2026-10-14 tam metin):
#   "The ReactOS kernel currently targets NT 5.2 and supports both x86 and x64
#    platforms. It contains all of the components you've come to expect from an
#    NT kernel, including complete implementations of the Object manager, IO
#    manager, Executive, NT thread scheduler, and many more."
# Executive servisleri (wiki/Ntoskrnl.exe, arsip 2025-09-12):
#   Object Manager · Configuration Manager · Cache Controller · LPC
#   Memory Manager · I/O Manager · Power Manager · PnP Manager
#   Process Structure · Security Reference Monitor
# Security Reference Monitor (wiki/Ntoskrnl.exe):
#   "It controls the access of resources or objects by the means of access
#    control lists."
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream docs) | YOK (negatif kanıt, 2026-10-14) |
| Kaynak | reactos.org/architecture/ · 2026-10-14 HTTP 200 (tam metin) |

### §2.2 — Önyükleme ve olgunluk durumu (alıntı)

```text
# kaynak: upstream docs - https://reactos.org/architecture/ + https://github.com/reactos/reactos/blob/master/README.md
# Onyukleme (ReactOS Architecture, 2026-10-14 tam metin):
#   "We also package various HALs for a number of platforms and our own NT
#    bootloader which is able to boot both the ReactOS and Windows kernels."
# Surucu kodlama kurali (ReactOS Architecture):
#   "we work on the drivers that are provided as part of the operating system"
# Olgunluk (README, Product quality warning, 2026-10-14 HTTP 200):
#   "ReactOS is currently an Alpha quality operating system ... Different things
#    may not work well and it can corrupt the data present on your hard disk.
#    It is HIGHLY recommended to test ReactOS on a virtual machine or on a
#    computer with no sensitive or critical data!"
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream docs) | YOK (negatif kanıt, 2026-10-14) |
| Kaynak | reactos.org/architecture/ · github README · 2026-10-14 HTTP 200 |

### §2.3 — Güvenlik tasarım iddiası (alıntı, wiki arşiv kopyası)

```text
# kaynak: upstream docs - https://web.archive.org/web/20260928150412/https://reactos.org/wiki/ReactOS
# Bolum: "Secure" (wiki ReactOS sayfasi, arsip 2026-09-28, oldid=50975):
#   "Despite statements to the contrary, Windows NT is secure by design. It was
#    the first mainstream operating system with a proper implementation of a
#    very flexible security model based on access control lists."
#   "ReactOS will incorporate proper default security settings."
#   "ReactOS has been designed for high security; it doesn't share some of the
#    common security flaws with other operating systems."
# Not: bu cumleler TASARIM iddiasidir; olgunluk sinirini README alpha uyari belirler.
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream docs, arşiv kopya) | YOK (negatif kanıt, 2026-10-14) |
| Kaynak | reactos.org/wiki/ReactOS (Wayback 20260928150412) · 2026-10-14 HTTP 200 |

## §3 CoreMusic repo durumu

| Yüzey | Beklenen kanıt | Repo durumu | Ölçüm (2026-10-14) | Sonuç |
|---|---|---|---|---|
| ReactOS kaynak kodu / NT çekirdek kodu | `ntoskrnl` · `se/` · `SepAccessCheck` | YOK | kod taraması: 0 isabet | **YOK (negatif kanıt)** |
| bootloader yapılandırması | `freeldr.ini` · `*.iso` kaynak | YOK | glob: 0 | **YOK (negatif kanıt)** |
| çekirdek konfigürasyonu | `config-*` · `*.sys` kaynağı | YOK | glob: 0 | **YOK (negatif kanıt)** |
| OS güvenlik aracı (seccomp/LSM/setcap/prctl) | çağrı veya politika | YOK | 721 kod dosyası (`*.php/js/css/sql`, vendor·node_modules·.git hariç) → **0 isabet** | **YOK (negatif kanıt)** |
| imzalama anahtarı | `*.asc` / `*.pem` (release) | YOK | `.gitignore` + secret yasağı | gizlilik kuralı |
| NT erişim denetim API çağrısı | `SeAccessCheck` · `RtlCreateSecurityDescriptor` | YOK | kod taraması: 0 isabet | **YOK (negatif kanıt)** |
| session dizin izni | PHP `mkdir` modu | **VAR** | `shared/src/Session/SessionConfig.php:20` → `DIR_PERMISSIONS = 0750` | **POZİTİF — uygulama katmanı (DAC), çekirdek değil** |

**Okuma:** 7 satırın 5'i negatif, 1'i gizlilik kuralı, 1'i uygulama katmanı
pozitifidir. **Bu alt-dalda çekirdek-güvenlik seviyesinde tek bir satır
ReactOS/OS kanıtı yoktur.**

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca ReactOS dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Cekirdek ici yapisi -> `isletim-sistemi-kernel`; ReactOS platform geneli -> `isletim-sistemi-turu/reactos`.

- Bu kart **"ReactOS güvenlidir"** demez; yalnızca kaynağın yazdığı iddiayı
  (`designed for high security`) ve olgunluk sınırını (`Alpha quality`) birlikte taşır.
- `0750` session izni ReactOS ile ilişkili değildir; bu repoda **uygulama
  katmanı DAC** kanıtıdır ve §3'te bu sıfatla geçer.
- Secure Boot, modül imzası, KASLR, SMEP/SMAP, LSM hakkında **kaynakta
  okunan satır yoktur** → `UNKNOWN`; "yok/eksik" diye de yazılmaz.
- Wiki içeriği arşiv kopyasından okundu; canlı sayfa erişimi `Anubis`
  challenge nedeniyle başarısızdır (§5 durum sütunu).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://reactos.org/ | 0.4.16 sürümü (29 Ağustos 2026) duyurusu; "open-source environment you can trust" | HTTP 200 · tam metin okundu (19.323 bayt) |
| https://reactos.org/architecture/ | NT çekirdek bileşenleri · NT 5.2 hedefi · kendi bootloader'ı · yüksek yetkili sistem süreçleri · test milyonlarca test | HTTP 200 · tam metin okundu (22.474 bayt) |
| https://reactos.org/wiki/ | wiki ana sayfası | HTTP 200 ama içerik **Anubis 1.19.1 challenge** (4.188 bayt) → içerik **OKUNAMADI** |
| https://reactos.org/wiki/ReactOS_Architecture | (havuz URL) | HTTP 200 challenge + **Wayback CDX 0 isabet** → içerik OKUNAMADI · sayfa varlığı **UNKNOWN** |
| https://reactos.org/wiki/ReactOS_Security | (havuz URL) | HTTP 200 challenge + **Wayback CDX 0 isabet** → içerik OKUNAMADI · sayfa varlığı **UNKNOWN** |
| https://web.archive.org/web/20260928150412/https://reactos.org/wiki/ReactOS | "Secure" bölümü: NT ACL modeli · "proper default security settings" · "designed for high security" | HTTP 200 · tam metin okundu (8.114 karakter) |
| https://web.archive.org/web/20250912212042/https://reactos.org/wiki/Ntoskrnl.exe | Executive servisleri · Security Reference Monitor ACL ile denetim | HTTP 200 · tam metin okundu (75.258 karakter) |
| https://github.com/reactos/reactos/blob/master/README.md | "Alpha quality operating system" + disk veri kaybı uyarısı | HTTP 200 · başlık doğrulandı |
| https://github.com/reactos/reactos | README tam metin: NT4–7 uyumluluğu · GPL-2.0 · FAT16/FAT32 kurulum | HTTP 200 · tam metin okundu |
| https://reactos.org/wiki/Versions | 0.4 serisi alpha'nın sonu · 0.5 beta hedefi · 1.0 günlük kullanım | canlı: challenge · **Exa indeks** üzerinden okundu (indirect) |
| https://reactos.org/wiki/Installing_ReactOS | "still in alpha stage ... not stable or feature-complete" | canlı: challenge · **Exa indeks** üzerinden okundu (indirect) |

> **Dürüstlük notu:** 11 satırın 7'si doğrudan tam metin okuma, 2'si challenge
> nedeniyle okunamayan havuz URL'si, 2'si indeks üzerinden **indirect** okumadır.
> İndirect satırlar "tam metin doğrulandı" diye kullanılmaz.

### §5.1 Yeniden üretilebilir ölçüm

```powershell
# 1) havuz URL durumlari (2026-10-14)
(Invoke-WebRequest 'https://reactos.org/' -UseBasicParsing).StatusCode
# wiki = 200 ama baslik "Making sure you're not a bot!" -> challenge
(Get-Content '<repo>\shared\src\Session\SessionConfig.php' -Encoding UTF8)[19]  # L20 0750
(Get-Content '<repo>\shared\src\Session\SessionConfig.php' -Encoding UTF8)[34]  # L35 mkdir 0750

# 2) repo OS-guvenlik araci taramasi (negatif kanit)
Get-ChildItem '<repo>' -Recurse -File -Include *.php,*.js,*.css,*.sql |
  Where-Object { $_.FullName -notmatch '\\(vendor|node_modules|\.git)\\' } |
  Select-String -Pattern 'seccomp|apparmor|selinux|setcap|ptrace_scope|prctl\(|CLONE_NEWNS' -CaseSensitive:$false
# beklenti: 0 isabet (721 dosya)
```

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? | **[UNKNOWN]** — bu dosya kapatmaz; mimari sahibi kararı + yeni ADR (`>=088`) gerekir |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | **[UNKNOWN]** — bu dosya kapatmaz; `uygulama-guvenlik-siniri` dalı ile birlikte karar verilir |

**KAPATMA kuralı:** S1/S2 bu kartta **kapatılamaz**; kapatma yetkisi mimari
sahibine aittir ve `validation:` alanına işlenir. Erken kapatma = karar gasbı.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
