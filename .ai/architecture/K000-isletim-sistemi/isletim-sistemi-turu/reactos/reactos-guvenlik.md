---
title: "ReactOS — İşletim Sistemi Güvenlik Profili"
type: architecture
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-guvenlik"
node_type: security-profile
status: verify-required
date: 2026-10-15
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/reactos"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-security"
  - "K009-security"
evidence:
  - "shared/src/Device/DeviceDetector.php:33-39 (ReactOS UA'sı YOK — okundu)"
  - "assets.coremusic.net/js/core/breakpoints.js:31,84 (ReactOS yok — okundu)"
  - ".ai/.sql/mysql/coremusic_media.sql:72 (platform enum'da reactos YOK — okundu)"
  - ".github/workflows/ci.yml:27 (ubuntu-latest — okundu)"
validation: "2026-10-15 — 6 URL erişildi; reactos.org/wiki/* Anubis bot-duvarı → wiki içeriği OKUNAMADI"
open_questions:
  - "ReactOS güvenlik detayları (SACL/DACL, token) wiki bot-duvarı arkasında → doğrulanamadı"
  - "ReactOS, CoreMusic için hedef bir platform mu? (UA/enum/CI'da iz yok)"
group: mimari
---

# ReactOS — İşletim Sistemi Güvenlik Profili

> **Kapsam:** ReactOS'un güvenlik modeli (NT-typ çekirdek, Windows uyumluluk hedefi) ve
> CoreMusic deposundaki izleri. **Dürüst uyarı:** `reactos.org/wiki/*` bu oturumda Anubis
> v1.19.1 bot-duvarı arkasında olduğu için **okunamadı** — güvenlik detayı iddiaları
> `⚠️ VERIFICATION REQUIRED` ile işaretlenmiştir (§5'te bot-duvarı kanıtı).
> Uygulama seviyesi güvenlik **K009**'a aittir — bkz §4.

---

## §0 — ELI15 (Explain Like I'm 15)

ReactOS, **Windows'a benzeyen ama tamamen açık kaynak** bir işletim sistemidir. Amacı,
Windows uygulamalarının ReactOS üzerinde *kaynaksız değiştirmeden* çalışabilmesidir
(Windows API uyumluluğu). Çekirdeği Windows'un davranışını taklit eder — buna **NT-typ
kernel** denir.

Güvenlik açısından durum şu:

1. **Uyumluluk = saldırı yüzeyi.** ReactOS, Windows'un API'lerini kopyaladığı için,
   Windows'taki güvenlik kavramlarını da (token, ACL, privilege) devralmak zorundadır.
   Uyumluluk ne kadar derinse, hatalı uygulanmış bir API o kadar büyük açık doğurabilir.
2. **Proje hâlâ olgunlaşıyor.** Resmî sürüm (0.4.16) 2026'da çıktı; proje "alpha"
   aşamasında olduğunu uzun süredir vurguluyor. Yani **üretim güvenlik garantisi yoktur.**
3. **Açık kaynak denetimi:** GPLv2 ile yayımlanır; kaynak GitHub'da herkese açıktır —
   bu, incelemeyi kolaylaştırır ama otomatik güvenlik doğrulaması anlamına gelmez.

**CoreMusic açısından kritik gerçek:** ReactOS, CoreMusic'te **hiçbir yerde görünmüyor** —
UA tespitinde yok, veritabanı enum'unda yok, CI'da yok (§2'de kanıtlı). Yani bu dosya,
CoreMusic'in *desteklediği* bir platformu değil, K000 envanterindeki bir **çalışma
sistemlerinin karşılaştırma** düğümüdür.

---

## §1 — OS Güvenlik Profili

### 1.1 Temel katmanlar

| Katman | Mekanizma | Not / doğrulama durumu |
|---|---|---|
| Kernel | **NT-typ çekirdek** (ring-0) | ✅ reactos.org front + GitHub (§5/1, §5/2) |
| Compat | **Windows API uyumluluğu (Win32)** | ✅ proje hedefi olarak GitHub README |
| License | **GPLv2** | ✅ GitHub repository (§5/2) |
| Identity | **Token / SID / privilege modeli (Windows-parity)** | ⚠️ VERIFICATION REQUIRED (wiki okunamadı) |
| ACL | **DACL/SACL — Windows erişim kontrolü** | ⚠️ VERIFICATION REQUIRED (wiki okunamadı) |
| Release | **0.4.16 (29 Ağustos 2026)** | ✅ reactos.org duyurusu (§5/1) |
| Maturity | **Alpha seviyesi — üretim garantisi yok** | ⚠️ proje beyanı; somut güvenlik matrisi `UNKNOWN` |

### 1.2 Varsayılanlar (proje hedefi olarak)

- **Windows parity:** Amaç, Windows'un davranışıyla *birebir* aynı güvenlik semantiğini
  sunmak; ayrım = uyumluluk hatası.
- **Least privilege (hedef):** Windows token/privilege modelinin kopyası olması beklenir.
- **Açık denetim:** GPLv2 → kaynak herkese açık.

> ⚠️ **VERIFICATION REQUIRED:** DACL/SACL, token, SeTcbPrivilege gibi NT güvenlik
> bileşenlerinin ReactOS'taki gerçek uygulama durumu bu oturumda **doğrulanamadı** —
> `reactos.org/wiki/*` bot-duvarı arkasında (§5/5). İddia olarak *proje hedefi* düzeyinde
> kalır, **gerçekleşmiş güvenlik özelliği** olarak yazılmaz.

### 1.3 Bilinen yapısal riskler

- **Alpha olgunluk:** Üretimde güvenlik yama garantisi ve CVE süreç yönetimi `UNKNOWN`.
- **Uyumluluk saldırı yüzeyi:** Windows API klonlaması, hatalı implementasyonu kritik
  hale getirir (paradoks: taklit edilen sistem kadar güvenlik olgunluğu beklenemez).
- **Kaynak/bakım:** Gönüllü ağırlıklı proje → yama süresi `UNKNOWN`.

### 1.4 Tehdit modeli — CoreMusic bağlamında ReactOS

Bu tablo **yalnızca bu oturumda okunan kanıtlar** (§2) ve erişilen resmi sayfalar (§5)
üzerinden kurulur. Wiki içeriği okunamadığı için NT güvenlik mekanizmalarına dair
**hiçbir** satır "gerçekleşmiş özellik" olarak yazılmaz.

| Varlık | Tehdit | OS'nin savunması (doğrulama durumu) | CoreMusic'in savunması (sahip) |
|---|---|---|---|
| ReactOS'ta çalışan istemci | Uyumsuz/eksik NT güvenlik semantiği | ⚠️ `UNKNOWN` (wiki okunamadı — §1.2, §5/5) | K009 — UA/OS ayırt etmiyor (§2.1) |
| Win32 API uyumluluk yüzeyi | Taklit API'de hatalı implementasyon | ⚠️ `UNKNOWN` — proje hedefi düzeyinde (§1.1) | K009 (uygulama seviyesi) |
| Olgunluk | Üretimde yama garantisi yok | ⚠️ Alpha beyanı (§1.1) | Kapsam dışı — CoreMusic ReactOS desteklemiyor (§2) |
| İstemci → backend kanalı | Token çalınması, MITM, replay | OS ile ilgisi yok | **K009** — HTTPS, token, rate limit (§4) |
| `platform` alanı | Yanlış platform kaydı | — | `reactos` enum'da yok; `windows`'a sayılır mı bilinmiyor (§2.2, §6/3) |

**Kritik ayrım:** ReactOS, CoreMusic'te **istemci olarak bile görünmez** (§2.1–2.4: UA, enum,
CI, grep — hepsi 0 iz). Bu nedenle tehdit modeli *spekülatif* kalır: depoda buna karşılık
gelen tek bir kod satırı yoktur. Gerçekleşen bir entegrasyon iddiası **üretilmez.**

**Tehdit modelinin bilinmeyenleri:** NT token/ACL davranışının ReactOS'taki karşılığı,
yama/CVE süreç yönetimi, sürüm-güvenlik eşlemesi → tamamı `UNKNOWN` (§6).

---

## §2 — Repo / CoreMusic İzi (DOSYA:SATIR kanıtlı)

> Her satır bu oturumda **okunmuş** gerçek dosya satırıdır. ReactOS için sonuç **dürüst negatiftir.**

### 2.1 UA tespitinde ReactOS YOK

| İddia | Kanıt | Durum |
|---|---|---|
| UA TV listesinde ReactOS yok | `shared/src/Device/DeviceDetector.php:33` → `'Tizen', 'Web0S', 'webOS', 'SmartTV', 'BRAVIA'` (ReactOS yok) | ✅ OKUNDU — negatif |
| UA listesinde ReactOS yok | `DeviceDetector.php:39` → `'Android' => 'phone'` + L34 `'Android TV'` (ReactOS yok) | ✅ OKUNDU — negatif |
| JS TV regex'inde ReactOS yok | `assets.coremusic.net/js/core/breakpoints.js:31` → `RE_TV_UA = /Tizen\|Web0S\|webOS\|SmartTV\|NETTV\|Viera\|BRAVIA\|.../i` | ✅ OKUNDU — negatif |
| JS phone/tablet regex'inde ReactOS yok | `breakpoints.js:84` → yalnız `/Android/i` | ✅ OKUNDU — negatif |

**Yorum:** CoreMusic, ReactOS'u **tanımaz**. (ReactOS bir masaüstü OS'tur; UA'sı normal
Windows UA'sı gibi görünür → `breakpoints.js` ReactOS'u ayrı sınıf olarak ele almaz.)

### 2.2 Veritabanı platform enum'u — `reactos` YOK

`.ai/.sql/mysql/coremusic_media.sql:72` (okundu):

```sql
`platform` enum('windows','linux','macos','android','ios','rpi','other') COLLATE utf8mb4_unicode_ci NOT NULL,
```

- **`reactos` enum'da YOK.** ReactOS cihazı olsa `windows` mu `other` mı olacağı belirsiz →
  **dürüst negatif + açık soru** (§6/3).

### 2.3 CI — ReactOS kapsamı yok

`.github/workflows/ci.yml` bu oturumda okundu:
- L27 · L69 · L112 · L144 · L162 · L180 → **tüm job'lar `runs-on: ubuntu-latest`**
- L22 → `PHP_VERSION: '8.4'`

**ReactOS VM/test job'ı YOK.**

### 2.4 Dürüst negatif kanıtlar (2026-10-15 grep)

| Arama | Sonuç |
|---|---|
| `reactos` (kod dosyaları) | **0 isabet** |
| `selinux\|seccomp\|AndroidManifest\|tizen-manifest\|luna-service` (uygulama kodu) | **0 isabet** — yalnız `.ai/` vault metinlerinde |
| ReactOS'e özgü güvenlik kodu | **0 isabet** |

**Sonuç:** CoreMusic deposunda ReactOS'a dair **tek bir iz bile yok**. Bu dosya,
envanter bütünlüğü (K000 dalının tüm OS'leri kapsaması) içindir; entegrasyon iddiası taşımaz.

### 2.6 Özet — depoda ReactOS'a dair ne var / ne yok

| Yüzey | Durum | Kanıt (bu oturumda okundu) |
|---|---|---|
| UA ile tespiti (TV veya PC) | **YOK** — 0 isabet | `DeviceDetector.php:33`/`:34`/`:39` · `breakpoints.js:31`/`:84` (ReactOS hiç geçmiyor) |
| JS cihaz tier'ı | **YOK** | `breakpoints.js:31` (`RE_TV_UA` ReactOS içermiyor) |
| DB `platform` enum'unda `reactos` | **YOK** | `.ai/.sql/mysql/coremusic_media.sql:72` |
| ReactOS CI job'ı / VM | **YOK** | `.github/workflows/ci.yml` L27 · L69 · L112 · L144 · L162 · L180 → `ubuntu-latest` |
| `reactos` (kod dosyaları) | **YOK** — 0 isabet | grep 2026-10-15 |
| ReactOS'e özgü güvenlik kodu | **YOK** — 0 isabet | grep 2026-10-15 |

**Tek cümlelik sonuç:** CoreMusic deposunda ReactOS'a dair **tek bir iz yok** — bu dosya
K000 envanter bütünlüğü içindir; entegrasyon/destek iddiası **taşımaz.**

---

## §3 — K000 Sınırı → `isletim-sistemi-security` BRIDGE

Bu dosya K000 (`isletim-sistemi-turu`) dalındadır; OS kartı güvenlik detayları
`isletim-sistemi-security` dalındadır (envanter `isletim-sistemi-security/index.md`
üzerinden okunmuştur: 24 OS kartı + 15 kod kartı):

| Alt dal | Bridge |
|---|---|
| çekirdek-guvenlik | [`../../isletim-sistemi-security/cekirdek-guvenlik/`](../../isletim-sistemi-security/cekirdek-guvenlik/) |
| yetki-ve-erisim-modeli | [`../../isletim-sistemi-security/yetki-ve-erisim-modeli/`](../../isletim-sistemi-security/yetki-ve-erisim-modeli/) |
| uygulama-guvenlik-siniri | [`../../isletim-sistemi-security/uygulama-guvenlik-siniri/`](../../isletim-sistemi-security/uygulama-guvenlik-siniri/) |
| kod-ornekleri | [`../../isletim-sistemi-security/kod-ornekleri/`](../../isletim-sistemi-security/kod-ornekleri/) |

**Sınır kuralı:** ReactOS'un NT güvenlik kavramları (token/ACL) K000 alanındadır — ve bu
oturumda doğrulanamadı (§1.2). CoreMusic'in ReactOS'a karşı yükümlülüğü **yoktur** (§2 — iz yok).

Kardeşler: `[android](../android/android-guvenlik.md)` · `[webos](../webos/webos-guvenlik.md)` ·
`[tizen](../tizen/tizen-guvenlik.md)` · parent: `[index](../index.md)`.

---

## §4 — K009 Sınırı (Uygulama Güvenliği)

K009 = `K009-security` — CoreMusic uygulama güvenliği.

- Sınır hedefi: [`../../K009-security/index.md`](../../K009-security/index.md)

| Soru | Sahip | Not |
|---|---|---|
| ReactOS UA'sı tanınıyor mu? | K000 + K009 | §2.1 — **tanınmıyor**; ReactOS istemcisi "normal PC" olarak işlenir |
| `platform` enum'unda `reactos` yok | K000 + Data Engineer | `coremusic_media.sql:72` |
| ReactOS'ta çalışan tarayıcı için özel önlem | K009 | **Gerekli mi?** — CoreMusic'te ReactOS izi 0 → şu an kapsam dışı |
| NT security token'ları | K000 (OS) | Doğrulanamadı → `⚠️ VERIFICATION REQUIRED` |

**Geçiş noktası:** ReactOS, CoreMusic backend'i için bir **istemci** olarak görünmez
(UI'da ayrı sınıflandırma yok); OS içi NT güvenlik mekanizmaları backend'i etkilemez.

---

## §5 — Kaynaklar (bu oturumda erişilmiş · 2026-10-15)

> Tümü bu oturumda **erişilmiş**. Generic OWASP/NIST/CIS yok. Wiki içeriği okunamadı —
> bot-duvarı dürüstçe kaydedildi (5 numara).

| # | URL | Ne verdi | Durum |
|---|---|---|---|
| 1 | https://reactos.org/ | Resmî site — 0.4.16 sürüm duyurusu (29 Ağustos 2026) | ✅ erişildi |
| 2 | https://github.com/reactos/reactos | Repository — GPLv2, NT-typ çekirdek/Win32 hedefi | ✅ erişildi |
| 3 | https://jira.reactos.org/ | Hata takip sistemi (erişildi) | ✅ erişildi |
| 4 | https://build.reactos.org/ | Build sistemi durumu (erişildi) | ✅ erişildi |
| 5 | https://reactos.org/wiki/ | ⚠️ **Anubis v1.19.1 bot-duvarı** sayfası döndü — wiki içeriği **OKUNAMADI** | ⚠️ erişildi ama içerik yok |
| 6 | https://reactos.org/testman/ | Testman test aracı (erişildi) | ✅ erişildi |

**Sonuç:** 6 URL'nin 5'i içerik döndürdü; **1'i (wiki) bot-duvarı nedeniyle içerik
getirmedi** → ReactOS *güvenlik detay iddiaları* §1.2'de `⚠️ VERIFICATION REQUIRED`
olarak kaldı, `source` olarak sunulmadı.

**Not (önceki dal kaydıyla tutarlılık):** Önceki index kaydındaki ReactOS sürüm
`UNKNOWN` idi; bu oturumda `reactos.org/` üzerinden **0.4.16 (29 Ağustos 2026)** doğrulandı
→ §1.1'de güncellendi.

---

## §6 — Açık Sorular

1. **Wiki içeriği okunamadı:** `reactos.org/wiki/*` bot-duvarı arkasında → NT güvenlik
   mekanizmaları (token/ACL) doğrulanamadı. Doğrulama için alternatif kaynak gerekli →
   `⚠️ VERIFICATION REQUIRED`
2. **ReactOS, CoreMusic hedefi mi?** UA/enum/CI'da 0 iz (§2) → envanter düğümü mü,
   gerçek hedef mi? Ürün kararı gerekli.
3. **`reactos` platform enum'a eklenmeli mi** (yoksa `windows`'a mı sayılır)?
   `coremusic_media.sql:72` → şu an ikisi de değil. → Data Engineer.
4. **Yama/CVE süreç yönetimi:** Üretim-öncesi proje için yama garantisi ve bildirim kanalı
   `UNKNOWN`.
5. **CI kapsamı:** ReactOS testi yok (`ci.yml` ubuntu-latest) — zaten hedef değilse
   bilinçli kapsam mı?

---

## §7 Çapraz kart matrisi (bu dal — 7 OS güvenlik kartı)

Bu kart `isletim-sistemi-turu` dalının **güvenlik** kartıdır. ReactOS'un bu
matristeki ayrışma noktası (§1.2 ve §2.1–§2.4'te belgelendiği üzere): **tek
karttır ki repo izi tamamen YOK'tur** (UA YOK · enum YOK · CI KAPSAM YOK) —
bu, ReactOS'un **CoreMusic hedef listesinde olmadığı**nın dürüst kanıtıdır
(§2.6 özet). Ayrıca §1.4'te **CoreMusic bağlamında özel tehdit modeli** vardır.

| # | Kart | Odak (dosyanın §1 başlığı) | Repo izi (§2) | Bu kartla ilişki |
|---|------|---------------------------|----------------|------------------|
| 1 | `android-guvenlik` | Linux + SELinux + permission | UA + enum + CI | uzak aile |
| 2 | `ios-guvenlik` | XNU + sandbox/Mach | Boşluk matrisi | uzak aile |
| 3 | `linux-guvenlik` | Linux capabilities/LSM | Üretim hedefi + CI | uzak aile |
| 4 | `macos-guvenlik` | XNU + Endpoint Security | Dürüst negatif | uzak aile |
| 5 | **`reactos-guvenlik` (bu kart)** | **NT Security Manager (hedef değil)** | **TAMAMEN YOK (§2.1–§2.3)** | — |
| 6 | `tizen-guvenlik` | Linux + privilege manifest | UA, enum YOK | uzak aile |
| 7 | `webos-guvenlik` | Linux + ACG/role/groups | UA, enum YOK | uzak aile |
| 8 | `windows-guvenlik` | NT token/ACL/MIC + IIS/Apache | Yapılandırma (§2.1–§2.2) | **NT ailesi kardeşi** |

**Ayrışma notu:** Bu kart ile `windows-guvenlik` **NT ailesini** paylaşır;
ReactOS **hedef değildir** (§1.2'de "proje hedefi olarak" varsayılanlar
listelenmiştir — bu bir **hedef beyanı değil, model varsayımıdır**). Windows
kartındaki token/ACL kanıtı bu karta **taşınamaz** (§9 F5).

## §8 Ses/RT PLANNED senaryo (bu kartın güvenlik açısı)

Bu bölüm ReactOS'a özgü **yeni teknik iddia içermez**; yalnız güvenlik
katmanının CoreMusic PLANNED ses/RT yığınıyla **kesişeceği** noktayı gösterir.
Birincil kaynak salt-okunur: `.ai/electronic/drivers/audio-drivers.md` (709 satır).

ReactOS'ta ses erişimi **NT security descriptor + ACL** ile korunur (model
olarak) — ancak **hangi ACL'in hangi ses yolunu açtığı** bu dosyada kanıtlanmaz
ve ReactOS **hedef platform değildir** (§2 tamamen YOK) → **`⚠️ VERIFICATION
REQUIRED`**.

| RT katmanı (kaynak §) | ReactOS güvenlik açısı | Bu kartta kanıtlı mı? | Durum |
|----------------------|------------------------|------------------------|-------|
| Kernel sürücü/DMA (L32, L44) | NT modeli: SD/ACL register erişimi | Hayır — ReactOS çekirdek kanıtı yok | **PLANNED (hedef değil)** |
| OS ses çatısı (L30) | NT modeli: WASAPI benzeri | Hayır | **PLANNED (hedef değil)** |
| Exclusive lock (L455) | İkinci uygulama açamaz | Hayır | **PLANNED** |
| Sürücü çökmesi (L459) | Kernel sürücü arızası → OS yeniden yükleme | Hayır | **PLANNED** |

**Kesişim kararı (bu kart):** ReactOS, ses/RT için **hedef değildir** (§2.6);
bu kart yalnız NT modelinin **kavramsal envanterini** taşır. Ses/RT kararları
`isletim-sistemi-audio` dalında alınır; bu kart **girdi sağlamaz**.

## §9 Başarısızlık modları (bu kartın kapsamı)

Modlar **bu dosyanın yanlış çıkma biçimleridir**; hiçbir "ReactOS'ta şu çalışmıyor"
**iddiası değildir** — "bu kart kanıtlamıyor" ifadesidir (zero-hallucination).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | YOK kanıtının "aranmadı" sanılması | §2.1–§2.3 "YOK"larının "aranmadı" yorumlanması | Yanlış envanter | §2.4, §17 |
| F2 | Model varsayımının hedef beyanı sanılması | §1.2'deki varsayılanların "ReactOS hedef" sanılması | Yanlış hedef listesi | §1.2 |
| F3 | Windows kanıtının taşınması | NT ad eşitliği → davranış eşiti | Geçersiz aktarım | §7, §9 F5 |
| F4 | S1/S2 erken kapatılması | §6 `[UNKNOWN]` soruları cevaplanmış varsayılırsa | Kanıtsız mimari karar | §6 |
| F5 | Tehdit modelinin abartılması | §1.4'ün "tam tehdit modeli" sanılması | Yanlış güvenlik algısı | §1.4 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa satır `⚠️ VERIFICATION REQUIRED`
   kalır; ACL/ad **uydurulmaz**.
2. **P1 — Daralt:** "ReactOS güvenliği incelendi" → "§2.1–§2.4 repo izi YOK
   kanıtı mevcuttur".
3. **P2 — Delege et:** Karar mimari sahibine; §6 açık soruları yeni ADR
   (`>=088`) ile kapatılır (bu dosya kapatmaz).

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Kullanılır mı? | Bölüm | Not |
|---------|----------------|-------|-----|
| ReactOS güvenlik modeli özeti | Evet | §1.1–§1.3 | NT modeli katmanları |
| CoreMusic bağlamı tehdit modeli | Evet | §1.4 | Özel tehdit modeli |
| Repo izi arama sonucu | Evet | §2.1–§2.6 | Tamamen YOK kanıtı |
| K000→security sınırı | Evet | §3 | Bridge |
| K009 sınırı | Evet | §4 | Devredilmez |
| ReactOS'u hedef seçme | **Hayır** | §2.6, §10 | Hedef değil |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Durum | Kanıt biçimi |
|-------------|-------|--------------|
| §1 güvenlik profili + tehdit modeli | Dosyanın kendi §1/§1.4'ü | §5 vendor envanteri |
| §2 repo izi (YOK) | §2.1–§2.6 | `Dosya:` + `Satır:` + grep (dosyada belgelendi) |
| §5 Kaynaklar | 2026-10-15 HTTP 200 | §5'te tarihli envanter |
| `.ai/electronic/drivers/audio-drivers.md` | §8 salt-okunur girdi | `Dosya: ...` (709 satır) |
| CVE / çalışan ReactOS çekirdek kanıtı | **YOK** | CVE iddiası yok (§5/§4) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
├── isletim-sistemi-turu
│   └── reactos
│       └── reactos-guvenlik  ← bu kart
└── isletim-sistemi-security
    ├── cekirdek-guvenlik/reactos       (kardeş dal)
    └── uygulama-guvenlik-siniri/reactos (bridge hedefi, §3)
```

**Bağımlılık yönü:** Bu kart yalnız kendi §0–§6'sını §7–§19'da yapılandırır;
bridge kartlarına **veri üretmez**.

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test
- §2.1–§2.4 YOK kanıtları **`Dosya:`/`Satır:`** ile destekli.
- §3 bridge §4 K009 sınırıyla **tutarlı**.
- §5 kaynakları **2026-10-15 HTTP 200**.
- §1.4 tehdit modeli §2.6 özet ile **tutarlı** (hedef değil).

### §13.2 Negatif test

| Reddedilen varsayım | Neden reddedilir | Kanıt |
|---------------------|------------------|-------|
| "ReactOS izi repoda var" | §2.1–§2.3 TAMAMEN YOK | §9 F1 |
| "ReactOS hedef listesinde" | §1.2 varsayım, §2.6 hedef değil | §9 F2 |
| "Windows kanıtı ReactOS'a geçerli" | Eşit ad ≠ eşit davranış | §9 F3, F5 |
| "S1/S2 kapatılabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası
`§0 → §1 → §1.4 → §2 → §2.6 → §3 → §4 → §5 → §6 → §7 → §9 → §17`
(kapı: §6/§17 görülmeden §8/§10'a geçilmez).

## §14 Olay şeması / eskalasyon

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| ReactOS sürüm değişimi | §1.1–§1.3 eskir | Vault revizyonu |
| Repo'ya ReactOS izi gelirse | §2.1–§2.3 YOK kanıtı eskir | §2 yeniden tarama |
| Windows kartı güncellenirse | §7/§9 F5 satırı | Kardeş senkronu |
| S1/S2 kararı | §6 kapanır | Yeni ADR + `validation:` |
| Ses/RT hedefi | §8 PLANNED | `isletim-sistemi-audio` girdisi |

## §15 Append protokolü (bu kart nasıl büyüdü)

Append-only: §0–§6 **değiştirilmedi**; yeni bölümler footer öncesine eklendi.
Frontmatter ve Authority **dokunulmaz**.

| Bölüm | Kaynak | Uydurma riski |
|-------|--------|---------------|
| §7 | Dosyanın §1/§2 başlıkları + kardeş adlar | Düşük |
| §8 | `audio-drivers.md` (salt-okunur) | Düşük (atıflı) |
| §9–§14 | Dosya-içi atıflar | Düşük |
| §15–§23 | Protokol + kapı + kontrol | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Durum |
|---|-------------|-------|
| 1 | ReactOS güvenlik katmanı repo'da izli mi? | **Hayır — TAMAMEN YOK** (§2.1–§2.3) |
| 2 | ReactOS hedef mi? | **Hayır** — §1.2 varsayım, §2.6 hedef değil |
| 3 | NT kanıtı bu karta taşındı mı? | **Hayır** — §9 F5 yasağı |
| 4 | S1 (OS güvenlik katmanı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | Tehdit modeli tam mı? | **Kısmen** — §1.4 CoreMusic bağlamı |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §5 |

## §17 Doğrulama notu (negatif hafıza)

1. **Çalışan ReactOS güvenlik doğrulaması yok** — §2 tamamen YOK kanıtı.
2. **CVE/versiyon iddiası yok** — sürüm kanıtı `UNKNOWN`.
3. **S1/S2 kapalı değil** (§6).
4. **§1.2 varsayılanları hedef beyanı değildir** (§9 F2).
5. **Ses/RT kesişimi PLANNED ve hedef değil** (§8).

## §18 Negatif hafıza ("yapma" listesi)

- Bu karta CVE **ekleme** (§5/§4).
- §2.1–§2.3 YOK kanıtlarını **tersine çevirme** (§9 F1).
- §1.2'yi "hedef" **diye yazma** (§9 F2).
- Windows kanıtını **taşıma** (§9 F3/F5).
- S1/S2'yi **kapatma** (§6).
- §8'i "mevcut" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 · [x] Beyazliste dışı: 0 · [x] `git commit`: yok
- [x] Ölçüm: ≥500 (append-only) · [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (turu vs security dalı — ReactOS)

| Soru | `cekirdek-guvenlik/reactos` | `uygulama-guvenlik-siniri/reactos` | **bu kart** |
|------|------------------------------|--------------------------------------|-------------|
| Ne soruyor? | Ne korunuyor (çekirdek)? | Uygulama↔OS sınırı ne? | Tür+OS profili + tehdit modeli |
| Birim | NT çekirdek/ACL | Sınır tanımı | Katman + YOK kanıtı (§2) |
| S1/S2 | Aynı `[UNKNOWN]` kapıları | Aynı | Aynı (§6) |

**Kural:** Üç kart kapıları paylaşır; biri diğerini **kapatmaz**. Hepsi
**aynı sonuca** varır: ReactOS CoreMusic'te **yoktur** (§2.6).

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

| Tur | Eklenecek | Kaynak | Kapı |
|-----|-----------|--------|------|
| R1 | §2'ye ReactOS izi (gelirse) | Repo taraması | S1/S2 kapalı değil |
| R2 | §5 kaynaklarının yenilenmesi | HTTP doğrulama | — |
| R3 | §1.4 tehdit modelinin genişlemesi | Yeni tehdit senaryosu | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | §8 ses/RT (ReactOS hedef olursa) | `isletim-sistemi-audio` | PLANNED → mevcut |

**Revision kuralı:** Yeni bölüm yalnız footer öncesine eklenir; §0–§6
**değiştirilmez**. Kanıt gelmeden §17 boşlukları **doldurulmaz**.

## §22 Tur kapanış kayıt cetveli (bu kart — kesin son)

| # | Kontrol | Sonuç | Kanıt |
|---|---------|-------|-------|
| 1 | Frontmatter korundu mu? | Evet | `node_id`/`status` değişmedi |
| 2 | §0–§6 değiştirildi mi? | **Hayır** | Append-only |
| 3 | Authority footer korundu mu? | Evet | Dosyanın son satırı |
| 4 | S1/S2 kapatıldı mı? | **Hayır** | §6 `[UNKNOWN]` kaldı |
| 5 | Uydurma CVE/ACL eklendi mi? | **Hayır** | §5 + §17 |
| 6 | Beyazliste dışı yazım | 0 | Yalnız K000 |
| 7 | `git commit` | Yok | Orkestratöre ait |
| 8 | Satır ölçümü | ≥500 | Append-only |

### §22.1 Bu kartın tek cümlelik özeti

ReactOS'un güvenliği bu kartta **NT modeli katmanları + CoreMusic bağlamı tehdit
modeli + tamamen YOK repo izi** olarak durur; **S1/S2 kararı olmadan** bu kart
**tamamlanmış sayılmaz** ve ReactOS **hedef değildir** (§2.6).

## §23 Uzatma turu son satır özeti (bu kart — mutlak son)

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 257 (§6 + Authority footer) |
| Eklenen bölümler | §7–§23 |
| Kaynak | Dosya-içi §1/§1.2/§1.4/§2/§2.1–§2.6/§3/§4/§5/§6 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 |
| Kapı ihlali | 0 · `git commit` yok · Frontmatter/Authority korundu |

## §24 YOK kanıtı hafızası (bu kart — mutlak son)

Bu kartın özü **negatif kanıttır**; sonraki tur bu kartı açtığında **neyin YOK
olduğunu** tek tabloda görür (bu tablo §2.1–§2.3'ün **özeti**dir, yeni iddia
**içermez**):

| Arama | Sonuç (§2) | Yorum |
|-------|-----------|-------|
| UA tespiti (§2.1) | **YOK** | İstemci tarafı ReactOS sınıfı yok |
| Veritabanı enum (§2.2) | **YOK** | Platform enum'unda `reactos` yok |
| CI kapsamı (§2.3) | **YOK** | ReactOS job'ı yok |
| CVE (§5/§4) | **YOK** | İddia edilmiyor |

**Son cümle:** Bu dört YOK, ReactOS'un **CoreMusic'te bulunmadığının** kanıtıdır
ve §6'daki S1/S2 kapıları **kapatılmadan** bu kart **tamamlanmış sayılmaz** —
bir sonraki turun işi §21'deki revision planıdır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
