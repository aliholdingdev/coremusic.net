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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
