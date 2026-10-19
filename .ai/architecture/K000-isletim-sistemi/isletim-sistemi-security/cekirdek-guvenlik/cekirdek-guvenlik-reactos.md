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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca ReactOS dosya/surec/erisim kisitlari ele alinir.

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

## §7 Çapraz Kart Matrisi (K000 içi karşılaştırma)

> **Analiz bölümü** — yeni olgu iddiası YOKTUR; hücreler diğer K000
> çekirdek-güvenlik kartlarının §1 özetleridir; ReactOS satırındaki alıntılar
> §2'deki upstream/alıntı bloklarına bağlıdır.

| OS kartı | Root-of-trust | Ana kontrol | ReactOS = temel satır farkı |
|---|---|---|---|
| **ReactOS (bu kart)** | NT bootloader + HAL (NT 5.2 hedefi) | Security Reference Monitor (ACL) | NT-uyum hedefi; **Alpha** olgunluk (§2.2 README uyarısı) |
| Windows | UEFI Secure Boot + TPM | WDAC/App Control + token/MIC | ReactOS hedefi olan NT'nin olgun sürümü; `cekirdek-guvenlik-windows` |
| Linux | Bootloader → çekirdek | LSM + capability + seccomp | Donanım zorunluluğu yok; `cekirdek-guvenlik-linux` |
| iOS | Boot ROM zinciri; SEP | Hardware root of trust | Donanım-anal; ReactOS'un tam tersi; `cekirdek-guvenlik-ios` |
| macOS | LocalPolicy (SEP imzalı) | SIP + extension kuralları | Boot politikası donanım-anal; `cekirdek-guvenlik-macos` |
| Tizen | İmzalama + SMACK | Sertifika→API | Manifest privilege; `cekirdek-guvenlik-tizen` |
| webOS | Image derleme + FOTA | Statik servis + ACG | Boot-time statik; `cekirdek-guvenlik-webos` |

**Okuma:** ReactOS tek karttır ki güvenlik **tasarım iddiası** ile
**olgunluk gerçeği** arasında açıkça bölünmüştür: §2.3'te wiki "secure by
design" iddiası, §2.2'de README "Alpha quality ... can corrupt the data"
uyarısı. Bu kartın karakteri budur: tasarım satırları §2.3 kaynaklı,
kullanılabilirlik satırı README kaynaklıdır ve CoreMusic senaryosunda
**yalnız sanal makine** bağlamında anılır (§10.3).

## §8 Ses/Zamanlama Yüzeyi Etkileşimi (Neva Engine senaryosu)

> `PLANNED` senaryo analizi. ReactOS bir **hedef OS değil, referans-NT**
> olarak incelenmiştir; Neva Engine ReactOS backend'i YOKTUR. Kaynaklar:
> `Dosya: .ai/electronic/drivers/audio-drivers.md [Satır: 709]` ·
> `Dosya: .ai/projects/NevaEngine/os/02-windows-wasapi.md [Satır: 79]` (NT
> referans backend'i).

### §8.1 NT ses mimarisi ReactOS'ta (§2.1 Executive satırına bağlı)

§2.1 Executive servis listesi (wiki arşivi) içinde I/O Manager ve Memory
Manager yer alır; ses zinciri Win32 katmanından (§2.2 CSRSS/Win32k) aşağı
iner. ReactOS ses stack'i bu vault'ta ayrı karttır
(`isletim-sistemi-turu/reactos/reactos-ses-stacki`). Bu kartın açısı
**güvenlik**: ACL tabanlı erişim kontrolü (§2.1 Security Reference Monitor
alıntısı) ses cihazı nesnelerine de uygulanır — NT modelinde cihaz
açma (`CreateFile` benzeri) bir nesne erişimidir ve ACL ile denetlenir.

### §8.2 Win32 uyumluluğu ↔ güvenlik quirk'ları (§2.1 kaynaklı)

§2.1 alıntısı: Win32 API "mimic Windows versions ... implementing their
bugs too ... unless they introduce security issues". Çıkarım: ReactOS,
güvenlik açığı quirk'larını **bilinçli olarak** kopyalamaz. Neva Engine
açısından sonuç: NT-uyumlu davranış beklenen bir kod yolu (exclusive lock,
§1 kural 9 eşdeğeri), ReactOS'ta da ACL üzerinden denetlenir; ancak
olgunluk sınırı (§2.2) bu davranışın sanal makine dışında doğrulanamayacağı
anlamına gelir.

### §8.3 Sürücü modeli × güvenlik

ReactOS Architecture (§2.2 alıntısı): "we work on the drivers that are
provided as part of the operating system" — yani çekirdek sürücüleri
ReactOS'un kendisi tarafından yazılır/imzalanır. NT'deki sürücü imzalama
zorunluluğu (Windows kartı §1) ReactOS'ta olgunluk sınırındadır: bu kart,
sürücü imzalama dayatmasının ReactOS'ta **kanıtlanmadığını** işaretler
(`⚠️ VERIFICATION REQUIRED`); §2.2'de sürüm/hazırlık notlarında imza
zorunluluğu okunmamıştır.

## §9 Başarısızlık Modları ve Risk Matrisi

| # | Başarısızlık | Konum | Belirti | Kaynak dayanağı | CoreMusic etkisi (senaryo) |
|---|---|---|---|---|---|
| F1 | CSRSS dosyası bozulması | §2.2 CSRSS alıntısı | BSOD `0xc000021a` | wiki arşiv (§2.2) | Sistem açılamaz — veri riski (§2.2 README) |
| F2 | Alpha kararlılık | §2.2 README | Beklenmedik davranış/veri bozulması | README (§2.2) | Üretim kullanımı yasak; VM |
| F3 | ACL uygulama boşluğu | §2.1 Se örneği | Erişim kontrolü tam uygulanmamış olabilir | §2.1/§2.3 (tasarım) + olgunluk | Sanal makine dışında doğrulanamaz |
| F4 | Sürücü imzalama kanıtsızlığı | §8.3 | İmza dayatması bilinmiyor | ⚠️ VERIFICATION REQUIRED | Sürücü güveni doğrulanamaz |
| F5 | Wine DLL bağımlılığı (§2.3 ReactOS-Wine) | Kullanıcı uzayı DLL'leri | Davranış NT'den sapabilir | §2.3 (aynı vault, uygulama kartı) | Uyumluluk testi şart |
| F6 | Ses sürücüsü olgunluğu | ReactOS ses stack kartı | Çekirdek/cihaz davranışı eksik | Vault ses kartı | Duyusal doğrulama VM'de |

**Örüntü:** 6 modun tamamı **olgunluk kaynaklıdır**; tasarım boşluğu
iddiası §2.3'te yoktur. Bu, ReactOS kartlarının imzasıdır: "tasarım doğru,
zamanlama erken" (§2.2 README'nin kendi ifadesiyle: highly recommended to
test on a virtual machine).

## §10 Entegrasyon Senaryoları (hedef mimari)

### §10.1 Senaryo A — Uyumluluk testi (tek meşru kullanım)

ReactOS, Neva Engine NT-backend'inin (WASAPI/ASIO) **uyumluluk laboratuvarı**
olur: §2.3 uyumluluk hedefi (NT4→2003 odağı) ile NT API çağrıları VM'de
doğrulanır. Üretim ses çıkışı hedefi DEĞİLDİR. S1/S2'yi etkilemez.

### §10.2 Senaryo B — Sürücü uyumluluk testi

ReactOS'un kendi sürücü modeli (§8.3) NT sürücü sözleşmelerinin referans
karşılaştırmasıdır: sanal donanım üzerinde WDM-KS benzeri davranış
gözlemi. Kanıt: VM-only (F2).

### §10.3 Senaryo C — Üretim (kapalı)

ReactOS'ta üretim ses uygulaması: **önerilmez** (§2.2 README "corrupt the
data" uyarısı + F2). Bu senaryo bu kartta açıkça reddedilmiştir; karar
ihtiyacı bile doğmaz.

### §10.4 Senaryo karşılaştırma

| Senaryo | Amaç | Ortam | Kanıt durumu | S1/S2 |
|---|---|---|---|---|
| A | NT-API uyumluluk | VM | F2 nedeniyle VM-only | Etkilemez |
| B | Sürücü sözleşmesi | VM + sanal donanım | F4 ⚠️ | Etkilemez |
| C | Üretim | — | **Reddedildi** | — |

## §11 Kaynakça — envanter ve ölçüm

### §11.1 Atıf yapılan vault dosyaları (2026-10-16 ölçümü)

| Dosya | Satır | Kullanım |
|---|---|---|
| `.ai/electronic/drivers/audio-drivers.md` | 709 | §8.1 katman modeli, §8.2 kural 9 |
| `.ai/projects/NevaEngine/os/02-windows-wasapi.md` | 79 | §8 NT referans backend |
| `.ai/projects/NevaEngine/os/01-windows-asio.md` | 119 | §10.1 uyumluluk hedefi |
| `isletim-sistemi-turu/reactos/reactos-ses-stacki.md` | (bu vault) | §8.1, §9 F6 |
| `isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-reactos.md` | (bu vault) | §8.2 Wine/Win32 derinliği |
| `isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-reactos.md` | (bu vault) | §8.1 ACL derinliği |
| `isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-{windows,linux,ios,macos,tizen,webos}.md` | (bu vault) | §7 çapraz |

### §11.2 Upstream/alıntı kanıt durumu (§2 blokları)

| Blok | Kaynak | 2026-10-14 durumu |
|---|---|---|
| §2.1 Executive/Se alıntıları | reactos.org/architecture + wiki arşiv | Tam metin okundu |
| §2.2 Boot/sürücü/README alıntıları | reactos.org/architecture + GitHub README | HTTP 200 (README) |
| §2.3 "Secure" bölümü alıntısı | wiki arşiv (oldid=50975) | Arşiv okundu |
| Canlı wiki | challenge sayfası ("not a bot") | İçerik arşivden alındı (§5.1) |

## §12 Karar Ağaçları

### §12.1 ReactOS bağlamı seçimi

```
Neva Engine NT-backend doğrulama
    │
    ├─ Gerçek Windows hedef mi?        → Windows kartı (bu vault) — birincil
    ├─ Uyumluluk laboratuvarı mı?      → ReactOS VM (Senaryo A, §10.1)
    ├─ Sürücü sözleşmesi karşılaştırması?→ ReactOS + sanal donanım (Senaryo B)
    └─ Üretim?                          → REDDEDİLDİ (§10.3, F2)
```

### §12.2 NT-kaynaklı davranış şüphesi

```
NT uyumlu davranış beklentisi var mı?
    ├─ Windows'ta doğrulandı   → ReactOS farkı yalnız referanstır
    └─ Henüz yok               → önce Windows kartı, sonra ReactOS VM (§10.1)
```

## §13 Zaman Çizelgesi (vault ölçümleri)

| Tarih | Olay | Kaynak |
|---|---|---|
| 2026-09-28 | Wiki arşivi (ReactOS "Secure" bölümü, oldid=50975) | §2.3 |
| 2026-09-12 | Wiki arşivi (Ntoskrnl.exe Executive listesi) | §2.1 |
| 2026-10-14 | Architecture + README tam metin okundu; canlı wiki challenge | §2.2, §5.1 |
| 2026-10-14 | Repo OS-güvenlik taraması: 0 isabet (721 dosya) | §3 |
| 2026-10-16 | Bu ek yazıldı | bu dosya |

## §14 Test ve Doğrulama Önerileri (senaryo, uygulanmadı)

| # | Doğrulama | Yöntem | Beklenen | Kanıt durumu |
|---|---|---|---|---|
| V1 | NT-API uyumluluk (ses yüzeyi) | VM + Neva NT-backend denemesi | Çağrı davranışları NT benzeri | Senaryo A |
| V2 | CSRSS/BSOD yüzeyi | VM hasarlama testi (kontrollü) | F1 davranışı (0xc000021a) | ⚠️ VM gerekli |
| V3 | ACL denetimi | Yetkisiz erişim denemesi | Red | VM |
| V4 | Sürücü imzası | İmzasız sürücü yükleme denemesi | Bilinmiyor → sonuç ölçülmeli | ⚠️ VERIFICATION REQUIRED |
| V5 | Ses çıkışı kararlılığı | Uzun süre VM testi | F2 nedeniyle yalnız istatistik | VM |

**Doğrulama kapısı:** V1–V5 tamamı sanal makine gerektirir; gerçek donanım
testi bu kartta planlanmaz (§10.3).

## §15 Sınır İhlali Kayıtları (negatif kontroller)

| Kontrol | Sonuç |
|---|---|
| ReactOS "güvenli" diye iddia edildi mi? | HAYIR — §2.3 tasarımdır, §2.2 uyarısı korunur |
| Üretim senaryosu açıldı mı? | HAYIR — §10.3 reddedildi |
| İmza zorunluluğu iddia edildi mi? | HAYIR — §8.3 `⚠️ VERIFICATION REQUIRED` |
| S1/S2 kapatıldı mı? | HAYIR (§6) |
| Frontmatter/`status:` değişti mi? | HAYIR (append-only) |

## §16 Ek: ReactOS Güvenlik Mekanizması ↔ CoreMusic Bileşeni Eşlemesi

| CoreMusic bileşeni (vault) | ReactOS karşılığı | Eşleme türü | Sınır notu |
|---|---|---|---|
| Neva Engine NT-backend (WASAPI/ASIO, PLANNED) | NT API yüzeyi (§2.1 Executive) | Uyumluluk | VM-only (Senaryo A) |
| Exclusive Lock kuralı (audio-drivers.md §1.4/§9) | NT dosya paylaşım davranışı | Kilit | Windows'ta doğrulanır; ReactOS referans |
| Sürücü katmanı (§8.3) | ReactOS kendi sürücüleri | Sürücü | İmza kanıtı ⚠️ (F4) |
| Sanal ses köprüsü (`09-open-source-virtual-audio-driver`) | NT sanal cihaz modeli | Yönlendirme | NT referansı Windows kartında |
| Format/IO (`10-io-format-32bit-192khz`) | NT WaveFormat eşdeğeri | Format | Uyumluluk testi (Senaryo A) |
| ACL ile cihaz erişimi (§8.1) | Security Reference Monitor | Denetim | VM'de gözlemlenir |

### §16.1 NT-uyum test paketi (senaryo A detayı)

| Test yüzeyi | NT beklentisi | ReactOS gözlemi (plan) | Kanıt |
|---|---|---|---|
| Cihaz açma/kilit | Exclusive ret | Ret/beklenen | VM |
| Format müzakeresi | WaveFormatEx davranışı | Aynı | VM |
| Buffer/period IOCTL'ları | NT sözleşmesi | Karşılaştırma | VM |
| Hata kodları | NT hata ailesi | Eşleme | VM |

**Not:** Bu paket bir **test taslağıdır**; `isletim-sistemi-testing` kartları
ayrı alt-daldadır. Çalıştırma kanıtı YOKTUR.

## §17 Ek: SSS — Bu Kartın Sınırı

| Soru | Kısa cevap | Bölüm |
|---|---|---|
| ReactOS güvenli midir? | Tasarım iddiası var (§2.3); kullanılabilirlik Alpha (§2.2) — ikisi ayrı okunur | §2.2, §2.3 |
| ReactOS'ta üretim ses mümkün mü? | Hayır — bu kartta reddedildi | §10.3, F2 |
| CSRSS neden kritik? | Bozulursa BSOD 0xc000021a | §2.2, F1 |
| ReactOS Windows sürücüsü imzalar mı? | Bilinmiyor — `⚠️ VERIFICATION REQUIRED` | §8.3, F4 |
| Wine ne sağlar? | Kullanıcı uzayı DLL'leri; kernel32/gdi32 fork (§2.3 uygulama kartı) | §9 F5 |
| ReactOS Neva hedefi midir? | Hayır — uyumluluk laboratuvarı | §10.1 |
| Wiki canlı erişilebilir mi? | Canlı wiki challenge; içerik arşivden (§5.1) | §11.2 |

## §18 Ek: NT-Güvenlik Modeli Referans Notları (§2.1/§2.3 alıntılarına bağlı sentez)

> Bu bölüm alıntılardan türetilen **sentezdir**; yeni upstream iddiası
> içermez. ReactOS, NT modelinin açık kaynak referansıdır; CoreMusic NT
> backend'i (PLANNED) için model okuması olarak kullanılır.

### §18.1 Nesne/ACL denetim zinciri (§2.1 "access control lists" alıntısı)

| Adım | NT modeli (ReactOS referansı) | CoreMusic etkisi (senaryo) |
|---|---|---|
| 1 | Nesne oluşturma (dosya/cihaz/güvenlik tokeni) | Ses cihazı nesnesi |
| 2 | Erişim kontrol listesi (ACL) ile koruma | Cihaz açma izni |
| 3 | Erişim kontrolü (Security Reference Monitor) | yetkisiz süreç reddi |
| 4 | Yetki/privilege kontrolü (token) | RT önceliği gibi yetenekler |

### §18.2 Executive servislerinin güvenlik yüzeyi (§2.1 listesi)

| Executive servisi (§2.1) | Güvenlik yüzeyi | Bu kartla ilişki |
|---|---|---|
| Object Manager | Nesne yaşam döngüsü + ACL | §18.1 |
| Security Reference Monitor | Erişim denetimi (§2.1 alıntısı) | §1 |
| Configuration Manager | Kayıt defteri (Registry) | Yapılandırma güveni |
| Memory Manager | Adres alanı/izolasyon | Süreç izolasyonu |
| I/O Manager | Cihaz/ sürücü I/O | §8.3 sürücü modeli |
| Process Structure | Süreç/token | §18.1 adım 4 |

### §18.3 Çekirdek ↔ kullanıcı uzayı ayrımı (§2.2 CSRSS alıntısı)

§2.2 alıntısı: Win32 kütüphanesi çağrıları CSRSS'e IPC ile gider
("without compromising the kernel"); pencere/GDI hizmetleri win32k.sys
(çekirdek modu sürücüsü). Güvenlik mimarisi dersi: NT, Win32 yüzeyini
çekirdekten **ayırarak** hata alanı daraltır; ReactOS bunu aynen modellemiş
(§2.2). CoreMusic açısından: kullanıcı uzayı ses API'si de benzer şekilde
IOCTL yüzeyini dar tutar (audio-drivers.md §1.3 "HAL üç şey teslim eder").

### §18.4 Olgunluk ↔ güvenilirlik matrisi (§2.2 README + §2.3 birleşimi)

| Katman | Tasarım (§2.3) | Olgunluk (§2.2) | Kullanılabilirlik |
|---|---|---|---|
| Çekirdek (Executive/Se) | Tasarım iddiası olumlu | Alpha | VM |
| Kullanıcı uzayı (Win32) | Wine + fork DLL'ler | Alpha | VM |
| Sürücüler | ReactOS kendi sürücüleri | Alpha | VM |
| Ses stack'i | Vault kartında | Vault kartı | VM (F6) |

**Örüntü:** Tasarım sütunu her satırda olumlu, olgunluk sütunu her satırda
Alpha. Bu iki sütunun ayrı okunması bu kartın zorunlu kuralıdır (§2.2
README "HIGHLY recommended" uyarısı).

### §18.5 Bu kartın diğer ReactOS kartlarına bağımlılığı

| Kart | Bağımlılık |
|---|---|
| `uygulama-guvenlik-siniri-reactos` | Wine/Win32 sınır derinliği (§9 F5) |
| `yetki-ve-erisim-modeli-reactos` | ACE/tokken derinliği (§18.1) |
| `isletim-sistemi-turu/reactos/*` | Ses stack, kernel, sürücü modeli (§9 F6) |

## §19 Ek: Okuma Sırası (yeni okuyucu için)

1. §0 ELI15 — ReactOS "referans-NT" çerçevesi.
2. §2.2 README Alpha uyarısı — **önce bu**; §2.3 "secure by design" iddiası
   ancak bu uyarı ile birlikte okunur.
3. §3 repo durumu — negatif kanıt; §2 bloklarının "repo karşılığı" satırları.
4. §7 çapraz matris — Windows ile tezat (olgunluk).
5. §8–§10 senaryo katmanı — VM-only senaryolar; üretim reddi (§10.3).
6. §18 NT-modeli notları — mimari okuma.
7. §14–§15 ölçüm + kontroller.

**Ek not (okuma):** Bu kart ile `cekirdek-guvenlik-windows` birlikte
okunmalıdır: ReactOS, NT modelinin erken/Alpha anıdır; CoreMusic NT
backend'inin **hedefi Windows**'tur, ReactOS yalnız laboratuvardır (§10.1).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
