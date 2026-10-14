---
title: "OS Donanım Tespiti — Doğrulama Kapıları"
type: architecture-module
node_id: os-donanim-tespiti-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/os-donanim-tespiti
children: []
depends_on:
  - os-donanim-tespiti-kapsam
  - K000-isletim-sistemi
  - K001-donanım
evidence:
  - "Repo ölçümü 2026-10-13: shared/src/Device/*.php grep sysfs|SetupDi|udev|devnode|PnP|ioctl = 0 isabet"
  - "Repo ölçümü 2026-10-13: *.c|*.cpp|*.h|*.hpp = 0 · *.cs = 0 · *.inf = 0 · *.rs = 0"
  - "Repo ölçümü 2026-10-13: DeviceDetector.php = 272 satır · DeviceManager.php = 588 satır · DeviceCssMap.php = 49 satır"
  - "https://learn.microsoft.com/en-us/windows/win32/api/setupapi/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://docs.kernel.org/driver-api/driver-model/index.html (HTTP 200, 2026-10-13, HEAD)"
  - "https://devicetree.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.usb.org/ (HTTP 200, 2026-10-13, HEAD)"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · OS gözlem kapıları TAMAMI AÇIK (çalışan Windows/Linux makine yok) · fiziksel ölçüm YOK"
open_questions:
  - "Q-DD1: Platform gözlem kapıları (Windows SetupAPI / Linux sysfs listesi) için çalışan makine envanteri yok → tüm gözlem kapıları AÇIK"
  - "Q-DD2: Gerçek donanım VID/PID envanteri bilinmiyor → UNKNOWN (K001 donanım-devices sahipliği)"
  - "Q-DD3: PnP/uevent olay kaydı (log) tutulmuyor → olay kapısı AÇIK"
date: "2026-10-13"
updated: "2026-10-13"
group: kanit
---

# os-donanim-tespiti-dogrulama — OS Donanım Tespiti Doğrulama Kapıları

> **Amaç:** `os-donanim-tespiti-kapsam.md` içindeki iddiaları **kapı kapı**
> doğrulamak. Bu dosya **kanıt** dosyasıdır: her bölüm bir doğrulama
> protokolü ve geçme koşulu içerir. Doğrulanmamış hiçbir sonuç "geçti"
> sayılmaz.

## §1 ELI15

Donanım tespiti için beş soru sorulur ve beşi de **gözlemlenmeden/dokümante
edilmeden** yanıtlanmaz: (1) platform keşif yüzeyi gerçekten tarif edildiği
gibi mi (Windows SetupAPI / Linux sysfs gözlemi), (2) repo'daki tespit kodu
gerçekten bu dalın sınırında mı (grep + satır ölçümü), (3) hotplug olayı
gerçekten yakalanıyor mu (olay kaydı), (4) web kaynakları hâlâ ayakta mı
(HEAD), (5) etiketler doğru mu (sahte kanıt temizliği). Bu dosya bu beş
sorunun **adımlarını, kayıt formunu ve geçme eşiklerini** yazar.

> ⚠️ VERIFICATION REQUIRED: Bu dosyada **hiçbir gerçek platform gözlemi
> yoktur** — çalışan bir Windows veya Linux makinesinde SetupAPI/sysfs
> listesi bu oturumda **çekilmemiştir** (Q-DD1). Tüm gözlem kapıları
> **AÇIK**tır; buradaki protokoller uygulanacak iş listesidir, sonuç raporu
> değildir. "DOCUMENTED" etiketi doküman-varlık iddiasıdır, gözlem **değildir**.

## §2 Kanıt Seviye Şeması (bu dosyada kullanılır)

| # | Seviye | Anlamı | Örnek | Kapı durumu |
|---|---|---|---|---|
| 1 | S1 | Canlı platform gözlemi (çalışan makine) | SetupAPI listesi · sysfs ağacı · uevent kaydı | ❌ AÇIK (makine yok, Q-DD1) |
| 2 | S2 | Aritmetik/ölçüm olarak türetilmiş | satır sayıları (§3) | ✅ kapandı (§3) |
| 3 | S3 | Bu oturumda URL doğrulandı (HEAD 200) | 8 web kaynağı (§7) | ✅ kapandı |
| 4 | S4 | Vault disk kanıtı (okunan dosya/ölçüm) | 3 PHP dosyası · ADR-064 · grep 0 | ✅ kapandı (§3, §4) |
| 5 | S5 | Üst otorite / ADR / anayasa kuralı | `.ai/CLAUDE.md` §2 (ring-0 → K000) | ✅ kapandı |

**Kural:** Bu dosyadaki bir "GEÇTİ" yazısı yalnızca **S1 kanıtı** ile
platform-gözlemi kapılarında yazılabilir. S2–S5 tek başına gözlem yerine
geçmez. Doküman-varlık (S4) gözlem (S1) sayılmaz.

## §3 Repo Ölçüm Kapıları (bu oturum — S2/S4)

### §3.1 PHP tespit kodu ölçümü

| # | Ölçüm | Sonuç | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `DeviceDetector.php` satır sayısı | 272 | `[System.IO.File]::ReadAllLines` 2026-10-13 | ✅ S2 |
| 2 | `DeviceDetector` class satırı | 19 | dosya satır 19 | ✅ S4 |
| 3 | `detect()` giriş metodu | satır 57 | dosya satır 57 | ✅ S4 |
| 4 | `HTTP_X_DEVICE_TYPE` başlığı kontrolü | satır 63 | dosya satır 63 | ✅ S4 |
| 5 | TV agent listesi | satır 32–36 | dosya satır 32–36 | ✅ S4 |
| 6 | Embedded agent listesi | satır 28–30 | dosya satır 28–30 | ✅ S4 |
| 7 | `DeviceManager.php` satır sayısı | 588 | ReadAllLines 2026-10-13 | ✅ S2 |
| 8 | 7 cihaz sabiti | satır 29–40 | dosya satır 29–40 | ✅ S4 |
| 9 | `DeviceCssMap.php` satır sayısı | 49 | ReadAllLines 2026-10-13 | ✅ S2 |

### §3.2 OS-seviyesi tespit kodu ölçümü (negatif kanıt)

| # | Ölçüm | Sonuç | Yorum | Durum |
|---|---|---|---|---|
| 1 | `shared/src/Device/*.php` içinde `sysfs|SetupDi|udev|devnode|PnP|ioctl` | **0 isabet** | OS yüzeyi kullanımı YOK | ✅ S4 (negatif) |
| 2 | Repo geneli `*.c|*.cpp|*.h|*.hpp` | **0** | ring-0 sürücü kodu YOK | ✅ S4 (negatif) |
| 3 | Repo geneli `*.cs` | **0** | .NET/sürücü kodu YOK | ✅ S4 (negatif) |
| 4 | Repo geneli `*.inf` | **0** | Windows sürücü tarifi YOK | ✅ S4 (negatif) |
| 5 | Repo geneli `*.rs` | **0** | Rust çekirdek kodu YOK | ✅ S4 (negatif) |

**Yorum (bağlayıcı):** 3.1 ve 3.2 birlikte tek sonucu kanıtlar: CoreMusic
bugün donanımı **OS seviyesinde keşfetmiyor**; UA/viewport ile **UI cihaz
tipi tahmin** ediyor (IMPLEMENTED) ve OS keşif yüzeyini **hiç kullanmıyor**
(PLANNED/UNKNOWN). Bu ayrım `-kapsam.md` §13 sınır yorumuyla birebir aynıdır.

## §4 Vault Disk Kanıtı Kapıları (S4)

| # | İddia | Kaynak | Tarih | Durum |
|---|---|---|---|---|
| 1 | ADR-017 dosyası var | glob `.ai/.decisions/accepted/ADR-017*.md` → 1 dosya | 2026-10-13 | ✅ yalnız varlık |
| 2 | ADR-038 dosyası var | glob ADR-038*.md → 1 dosya | 2026-10-13 | ✅ yalnız varlık |
| 3 | ADR-064 dosyası var + §1/§1.1 okundu | `ADR-064-electronics-platform-architecture.md` satır 1–60 | 2026-10-13 | ✅ okundu |
| 4 | ADR-064'te `DeviceManager.php:29-40` 7 tip kaydı | ADR-064 kaynak satırı (frontmatter `kaynak` alanı) | 2026-10-13 | ✅ çapraz teyit |
| 5 | `cihaz-erisimi` 4 dosya | glob `isletim-sistemi-api/cihaz-erisimi/*.md` → 4 dosya | 2026-10-13 | ✅ sınır komşusu |
| 6 | `wasapi/` 13 md dosya | glob 2026-10-13 | 2026-10-13 | ✅ sınır komşusu |
| 7 | K000 kademe-2 = 12 dizin | dizin listesi 2026-10-13 | 2026-10-13 | ✅ çelişki kaydı girdisi |
| 8 | `donanaım-type/` = 5 alt konu + 3 dosya | dizin listesi 2026-10-13 | 2026-10-13 | ✅ sınır komşusu |
| 9 | `donanım-devices/` = 4 dosya | dizin listesi 2026-10-13 | 2026-10-13 | ✅ sınır komşusu |
| 10 | `donanım-api/` = 3 dosya | dizin listesi 2026-10-13 | 2026-10-13 | ✅ sınır komşusu |
| 11 | K005-firmware = 9 alt dizin + 11 dosya | dizin listesi 2026-10-13 | 2026-10-13 | ✅ sınır komşusu |

**Yorum:** ADR-017 ve ADR-038 hakkında **yalnız varlık iddiası** kurulmuştur
(içerikleri okunmamıştır; `-kapsam.md` §14 ile aynı). ADR-064 §1/§1.1
**okunmuştur** ve `DeviceManager` 7 tip kaydı bu dosyadaki §3.1/8 ölçümüyle
**çapraz teyitlidir** (iki bağımsız kaynak aynı satır aralığını gösterir).

## §5 Platform Gözlem Kapıları (S1 — TAMAMI AÇIK)

> Bu bölümün hiçbiri bu oturumda **uygulanmamıştır**; formlar **boştur**.
> Her kapı için (a) alet/ortam, (b) adım, (c) geçme koşulu tanımlıdır.

### §5.1 Windows gözlem kapıları

| # | Kapı | Ne gözlemlenir | Geçme koşulu | Durum |
|---|---|---|---|---|
| W1 | Cihaz listesi okuma | Çalışan Windows'ta cihaz listesi çıktısı | Liste en az 1 bilinen cihaz içerir + §4.1 şablonu doldu | ❌ AÇIK |
| W2 | Sınıf/ID okuma | Seçili cihazın sınıf + ID bilgisi | ID biçimi §8.1/#4'e uyar (uydurma yok) | ❌ AÇIK |
| W3 | Hotplug olayı | Tak-çıkar sırasında olay bildirimi | Olay alındı + zaman damgası kaydedildi | ❌ AÇIK |
| W4 | Sürücü bağlama durumu | Seçili cihazın sürücü durumu | Durum okundu (bağlı/bağlı değil) | ❌ AÇIK |
| W5 | Başarısız keşif | Sürücüsü olmayan sanal cihaz (varsa) | Hata sınıfı §8.2 ile eşleşti | ❌ AÇIK |

### §5.2 Linux gözlem kapıları

| # | Kapı | Ne gözlemlenir | Geçme koşulu | Durum |
|---|---|---|---|---|
| L1 | sysfs listesi | Çalışan Linux'ta cihaz ağacı çıktısı | Liste en az 1 bilinen cihaz içerir + §4.2 şablonu doldu | ❌ AÇIK |
| L2 | modalias/ID okuma | Seçili cihazın modalias değeri | Biçim §8.1/#2'ye uyar (uydurma yok) | ❌ AÇIK |
| L3 | uevent kaydı | Tak-çıkar sırasında uevent | Olay alındı + anahtar kaydedildi | ❌ AÇIK |
| L4 | udev → /dev düğümü | Olay sonrası /dev düğümü oluşumu | Düğüm oluştu + yetki kaydedildi | ❌ AÇIK |
| L5 | device-tree okuması (gömülü varsa) | compatible değeri | Dize okundu (uydurulmadı) | ❌ AÇIK |

**Kapı kuralı:** S1 kapısı yalnız (a) çalışan makine, (b) adım kaydı, (c) ham
çıktı dosyası — üçü birlikte varsa kapanır. "DOCUMENTED" etiketi bu kapıları
**kapatmaz**.

## §6 Hotplug Olay Kaydı Kapısı (S1 — AÇIK)

| # | Kapı | Ne kaydedilir | Geçme koşulu | Durum |
|---|---|---|---|---|
| 1 | Ekleme olayı kaydı | Olay + zaman + cihaz kimliği | §6.1 formu doldu | ❌ AÇIK (Q-DD3) |
| 2 | Kaldırma olayı kaydı | Olay + zaman + kimlik | §6.1 formu doldu | ❌ AÇIK |
| 3 | Sürücü bağlama kaydı | Bağlama denemesi + sonucu | Sonuç okundu | ❌ AÇIK |
| 4 | Olay kaybı testi | Uygulama olayı aldı mı | Uygulama logu olayı gösterir | ❌ AÇIK |

### §6.1 Olay kayıt formu (BOŞ — doldurulmamış)

| # | Tarih/saat | Platform | Olay sınıfı (§7.1 kapsam) | Kimlik (okundu) | Sürücü sonucu | Kaydeden |
|---|---|---|---|---|---|---|
| 1 | (boş) | (boş) | (boş) | (boş) | (boş) | (boş) |

> Boş form **sonuç değildir**; doldurulup §9.3'e işlenir.

## §7 Web Kaynak Doğrulaması (bu dosya — S3)

| # | URL | Yöntem | Tarih | Durum |
|---|---|---|---|---|
| 1 | https://learn.microsoft.com/ | HEAD (Invoke-WebRequest, 20 sn) | 2026-10-13 | ✅ HTTP 200 |
| 2 | https://learn.microsoft.com/en-us/windows/win32/api/setupapi/ | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 3 | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 4 | https://www.kernel.org/ | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 5 | https://docs.kernel.org/ | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 6 | https://docs.kernel.org/driver-api/index.html | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 7 | https://docs.kernel.org/driver-api/driver-model/index.html | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 8 | https://docs.kernel.org/driver-api/infrastructure.html | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 9 | https://docs.kernel.org/admin-guide/sysfs-rules.html | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 10 | https://docs.kernel.org/ABI/index.html | HEAD (aynı yöntem) | 2026-10-13 | ❌ HTTP 404 → **kullanılmadı** |
| 11 | https://devicetree.org/ | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 12 | https://uefi.org/ | HEAD (aynı yöntem) | 2026-10-13 | ❌ HTTP 403 → **kullanılmadı** |
| 13 | https://www.usb.org/ | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |

**Kurallar:**
1. 403/404 döndüren URL **kullanılmaz** ve kaynak tablosuna **alınmaz**
   (10–12. satırlar yalnız kayıt olarak durur).
2. Yeni envanter kesişimi **0** olmalıdır — bu dosya donanım envanterine
   fiziksel parça iddiası eklemez.
3. Uydurma URL, uydurma sürüm, uydurma tarih **üretilmez**; doğrulanamayan
   iddia `⚠️ VERIFICATION REQUIRED` kalır.
4. `{...}` ve `TBD` yer tutucusu kullanılmaz; bilinmeyen `UNKNOWN` yazılır.

## §8 İddia → Kanıt Matrisi

| # | İddia (kapsam dosyası) | Kanıt tipi | Kanıt yeri | Durum |
|---|---|---|---|---|
| 1 | Enumeration genel modeli (§4) | S5 (anayasa) + S4 (doküman-varlık) | `.ai/CLAUDE.md` §2 · §7 kökler | ✅ DOCUMENTED |
| 2 | Windows yüzeyi (§5) | S4 (HEAD-200 kök) | §7 satır 2–3 | ✅ DOCUMENTED · gözlem ❌ |
| 3 | Linux yüzeyi (§6) | S4 (HEAD-200 kök) | §7 satır 5–9, 11 | ✅ DOCUMENTED · gözlem ❌ |
| 4 | Hotplug modeli (§7) | S4 (doküman-varlık) | §5 kapıları | ✅ DOCUMENTED · gözlem ❌ |
| 5 | Sürücü bağlama + ID eşleme (§8) | S4 (doküman-varlık) | §5/§6 kapıları | ✅ DOCUMENTED · gözlem ❌ |
| 6 | Repo'da OS-seviyesi tespit YOK | S4 (grep 0) | §3.2 | ✅ (negatif kanıt) |
| 7 | DeviceDetector/DeviceManager IMPLEMENTED | S2+S4 (satır ölçümü) | §3.1 | ✅ |
| 8 | CoreMusic UI tahmini keşif değildir | S4 (kod okuması) | §3.1/#4, #5, #6 + `-kapsam.md` §13 | ✅ |
| 9 | ADR-017/038/064 varlıkları | S4 (glob) | §4 satır 1–3 | ✅ (ADR-064 ayrıca okundu) |
| 10 | 8 web kaynağı erişilebilir | S3 (HEAD 200) | §7 | ✅ |
| 11 | Gerçek platform gözlemi | **S1 gerekli** | §5–§6 | ❌ AÇIK (Q-DD1) |
| 12 | Gerçek VID/PID envanteri | insan + S1 | §4.2 kapsam | ❌ UNKNOWN (Q-DD2) |
| 13 | PnP/uevent olay kaydı | S1 | §6 | ❌ AÇIK (Q-DD3) |
| 14 | Bu dalın K000 kök kaydı onaylandı | Vault Steward onayı (klasör açımı) | `-kapsam.md` §10.1 | ⏳ PARTIAL — kök metin APPROVAL REQUIRED |

## §9 Kapı Özeti

| # | Kapı | Seviye | Durum | Engeli |
|---|---|---|---|---|
| 1 | Repo ölçüm kapısı | S2/S4 | ✅ kapandı (§3) | — |
| 2 | Vault disk kanıtı kapısı | S4 | ✅ kapandı (§4) | — |
| 3 | Web URL kapısı | S3 | ✅ kapandı (11/13 · 2 kullanılmadı) | — |
| 4 | Anayasa kapısı | S5 | ✅ kapandı | — |
| 5 | Windows gözlem kapıları (W1–W5) | S1 | ❌ açık | çalışan makine yok |
| 6 | Linux gözlem kapıları (L1–L5) | S1 | ❌ açık | çalışan makine yok |
| 7 | Hotplug olay kapısı | S1 | ❌ açık | olay kaydı yok (Q-DD3) |
| 8 | VID/PID envanter kapısı | insan+S1 | ❌ açık | UNKNOWN (Q-DD2) |
| 9 | Etiket kapısı (sahte kanıt) | S4 | ✅ bu dosyada uygulandı | — |
| 10 | Çelişki-kayıt kapısı | S4 | ✅ kayıtlı (`-kapsam.md` §10) | kök metin ⏳ |

## §10 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | "DOCUMENTED"ı gözlem sanmak | Kök sayfa 200 → "keşif doğrulandı" | §2 — S4 ≠ S1 |
| 2 | UI tahmini keşif saymak | DeviceDetector → "donanım keşfi" | §3.2 — grep 0 |
| 3 | 403/404 URL'yi kaynak yazmak | Kırık referans | §7/10–12 |
| 4 | Boş olay formunu sonuç saymak | "olay yakalandı" | §6.1 = şablon |
| 5 | Uydurma VID/PID ile gözlem "tamamlamak" | Sahte envanter | §5 kapı kuralı |
| 6 | IMPLEMENTED ile PLANNED işi sunmak | Sahte kanıt | `-kapsam.md` §12 |
| 7 | `cihaz-erisimi` kapısını burada kapatmak | Çifte sahiplik | §14 sınırı |
| 8 | Kök `index.md` onayını varsaymak | Kayıtsız mimari | §8/14 — APPROVAL REQUIRED |
| 9 | `{...}`/`TBD` bırakmak | Okunamaz tablo | `UNKNOWN` |
| 10 | Ölçüm olmadan "geçti" yazmak | Yanlış kabul | yalnız S1 ile GEÇTİ |

## §11 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | S1–S5 | Kanıt seviyeleri (§2) |
| 2 | Gözlem | Çalışan sistemde okunan gerçek çıktı |
| 3 | Doküman-varlık | Resmi dokümanın varlığının doğrulanması (HEAD 200) |
| 4 | HEAD isteği | Sunucu durumunu içerik indirmeden sorgulama |
| 5 | Negatif kanıt | "Yok" ölçümlenerek kanıtlanan iddia (grep 0) |
| 6 | Gate / kapı | Yalnız kanıtlı geçilebilen kontrol noktası |
| 7 | Enumeration | OS'nin bus taramasıyla cihaz listelemesi |
| 8 | Devnode | Windows çekirdek cihaz düğümü kaydı |
| 9 | sysfs | Linux çekirdek nesne ağacı yansıması |
| 10 | uevent / udev | Linux olay bildirimi / kural motoru |
| 11 | device-tree | Gömülü donanım ağacı |
| 12 | modalias | Linux modül alias eşleme dizesi |
| 13 | INF | Windows sürücü tarif dosyası |
| 14 | VID / PID | USB vendor / product identifier |
| 15 | IOCTL | Cihaza özgü kontrol çağrısı (erişim sınırı) |
| 16 | Gate geçmişi | Append-only kapı kapanış kaydı |
| 17 | IMPLEMENTED | Kod kanıtlı etiket |
| 18 | PLANNED | Spec var, kod yok etiketi |
| 19 | PROPOSED | Öneri — karar verilmedi |
| 20 | UNKNOWN | Veri hiç yok |

## §12 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `os-donanim-tespiti-kapsam.md` | Doğrulanan iddiaların sahibi |
| 2 | `os-donanim-tespiti-ornekler.md` | Repo kodu + uygulama şablonları |
| 3 | `index.md` | Künye |
| 4 | `../os-dogrulama.md` | K000 kök doğrulama (bu dal eklenmedi) |
| 5 | `../isletim-sistemi-api/cihaz-erisimi/index.md` | Sınır: API ≠ keşif (§10/#7) |
| 6 | ADR-017 · ADR-038 | Yalnız varlık iddiası (§4) |
| 7 | ADR-064 | Okundu + çapraz teyit (§4/#4) |

### §12.1 Periyodik Takvim (kayıt zorunlu)

| # | İş | Sıklık | Kayıt alanı |
|---|---|---|---|
| 1 | Web URL HEAD tekrarı | her vault revizyonunda | §7 |
| 2 | Repo ölçüm tekrarı (grep + satır) | repo değişince | §3 |
| 3 | Windows gözlem (W1–W5) | makine envanteri gelince | §5.1 |
| 4 | Linux gözlem (L1–L5) | makine envanteri gelince | §5.2 |
| 5 | Olay kaydı (§6.1) | hotplug senaryosu çalıştırılınca | §6 |
| 6 | Etiket denetimi | her büyük yazım turunda | `-kapsam.md` §12 |

### §12.2 Kapı Kapanış Akışı

| # | Adım | Şart | Sonuç |
|---|---|---|---|
| 1 | İddia listesi çıkar | `-kapsam.md` §2 + §9 | matris hazır |
| 2 | Kanıt tipini ata | S1–S5 | §2 |
| 3 | S1 gerekenler için makine envanteri kur | Q-DD1 | gözlem |
| 4 | Geçme koşulunu karşılaştır | §5–§6 | geç/kal |
| 5 | Sapmayı risk kaydına yaz | varsa | `-kapsam.md` §15 |
| 6 | Kapıyı kapat ve tarihle | S1 kanıtı | §12.4 |

### §12.3 Bulgu → Kök Neden Tablosu

| # | Bulgu | Olası kök neden | Doğrulama yolu |
|---|---|---|---|
| 1 | W1/L1 listesi boş | Yetki · sanal makine · filtre | §5 kapı kuralı |
| 2 | Olay alınmıyor (W3/L3) | Dinleme yüzeyi yanlış | §6 |
| 3 | URL 404 | Sayfa taşındı | §7 yeniden HEAD |
| 4 | Etiket tutarsız | Yazım hatası | `-kapsam.md` §12 |
| 5 | grep 0 sanıldı, kod aslında var | yanlış dizin | §3.2 ölçüm tekrarı |
| 6 | VID/PID uydurulmuş | Sahte envanter | §5/#5 + Q-DD2 |

### §12.4 Kapı Geçmişi (append-only kayıt tablosu)

| # | Tarih | Kapı | Aksiyon | Sonuç |
|---|---|---|---|---|
| 1 | 2026-10-13 | §7 web | 13 URL HEAD | 11 ✅ / 2 ❌ (403 · 404 — kullanılmadı) |
| 2 | 2026-10-13 | §3 repo | PHP + dil dosyası ölçümü | ✅ (negatif kanıt dahil) |
| 3 | 2026-10-13 | §4 vault | glob + ADR-064 okuma | ✅ (ADR-017/038 yalnız varlık) |
| 4 | 2026-10-13 | §5 S1 | — | ❌ açık (makine yok) |
| 5 | 2026-10-13 | §6 olay | — | ❌ açık (kayıt yok) |
| 6 | 2026-10-13 | §8/14 kök kayıt | — | ⏳ APPROVAL REQUIRED |

### §12.5 Kapı Karar Matrisi (geçiş koşulu)

| Kapı | Koşul (hepsi aynı anda) | Karar | Kalan borç |
|---|---|---|---|
| G1 — Repo | satır ölçümleri + grep kaydedildi | AÇILDI ✅ | 0 |
| G2 — Vault | glob + okuma kaydedildi | AÇILDI ✅ | 0 |
| G3 — URL | 11/11 kullanılan HEAD-200 | AÇILDI ✅ | 2 kullanılmadı (kayıtta) |
| G4 — Windows gözlem | W1–W5 ham çıktıları + form doldu | kapalı ❌ | Q-DD1 |
| G5 — Linux gözlem | L1–L5 ham çıktıları + form doldu | kapalı ❌ | Q-DD1 |
| G6 — Olay | §6.1 formu + zaman damgası | kapalı ❌ | Q-DD3 |
| G7 — Envanter | gerçek VID/PID kaydı | kapalı ❌ | Q-DD2 |
| G8 — Kök kayıt | K000 index/§GRUPLAR onayı | kapalı ⏳ | orkestratör |

**Kural:** Bir kapı yalnız bir üst kapı kapalıysa **kapalı** kalır; kısmi
geçiş yoktur. G4–G7 birbirinden **bağımsızdır** (birinin kapanması diğerini
açmaz); G8 üst otorite kapısıdır.

### §12.6 Bağımlılık Zinciri (kapı → kapı)

```text
[G1 Repo] → [G2 Vault] → [G3 URL]  … bu dosyanın自身 kapandı (S2–S5)
                    ↘
[G4 Windows gözlem] ← [Q-DD1 makine envanteri]
[G5 Linux gözlem]   ← [Q-DD1 makine envanteri]
[G6 Olay]           ← [Q-DD3 olay kaydı]
[G7 Envanter]       ← [Q-DD2 gerçek VID/PID]
[G8 Kök kayıt]      ← orkestratör onayı

G4/G5/G6/G7 kapalıyken bu dosya "durum raporu" olarak kalır;
"geçiş raporu" olamaz. Zincirin başı Q-DD1'dir — makine envanteri
doldurulmadan S1 kapıları açılmaz. Bu dürüst durumdur, kusur değildir.
```

### §12.7 Bu Dosyanın Kanıt Sayımı (özet)

| # | Kanıt türü | Adet | Kullanım |
|---|---|---|---|
| 1 | HTTP 200 web kaynağı | 11 | §7 — doğrulandı, atıf yapıldı |
| 2 | HTTP 403/404 web kaynağı | 2 | §7 — **kullanılmadı**, yalnız kayıt |
| 3 | PHP repo dosyası ölçümü | 3 (272/588/49 satır) | §3.1 — IMPLEMENTED kanıtı |
| 4 | Negatif repo ölçümü | 5 (grep 0 + 4 dil dosyası 0) | §3.2 — OS keşfi yok |
| 5 | ADR varlık iddiası | 2 (ADR-017, ADR-038) | §4 — içerik okunmadı |
| 6 | ADR okuma + çapraz teyit | 1 (ADR-064 §1/§1.1) | §4/#3–4 |
| 7 | Sınır komşusu sayımı | 5 (cihaz-erisimi 4 · wasapi 13 · type 8 · devices 4 · api 3 · firmware 20) | §4/#5–11 |
| 8 | Yapılmış platform gözlemi | **0** | §5–§6 — tüm kapılar kapalı |
| 9 | `{...}` / `TBD` yer tutucusu | **0** | — |
| 10 | `UNKNOWN` işareti | çoklu | bilinmeyenler işaretli |

**Toplam:** 10 kaynak sınıfı = 32 somut kanıt nesnesi; **platform gözlemi: 0**.

### §12.8 Ölçüm Metodolojisi Notu — toplam satır vs dolu satır (2026-10-13 ikinci tur)

> Bu bölüm §3.1 satır ölçümlerini **değiştirmez**; iki sayım biçimi arasındaki
> farkı kayda geçirir (gizleme yasak).

| # | Dosya | Toplam (ReadAllLines) | Boş satır | Dolu satır | §3.1 değeri | Yorum |
|---|---|---|---|---|---|---|
| 1 | `DeviceDetector.php` | 316 | 44 | **272** | 272 | §3.1 değeri = dolu satır |
| 2 | `DeviceManager.php` | 662 | 74 | **588** | 588 | §3.1 değeri = dolu satır |
| 3 | `DeviceCssMap.php` | 57 | 8 | **49** | 49 | §3.1 değeri = dolu satır |
| 4 | `DeviceRenderer.php` | 162 | 18 | **144** | — | yeni keşif (§12.9) |

**Yorum (bağlayıcı):** §3.1 satır ölçümleri **sayısal olarak doğrudur**; tek
fark, ölçümün **boş satır hariç** (dolu satır) biçiminde alınmış olmasıdır
(`Measure-Object -Line` davranışı). `[System.IO.File]::ReadAllLines` toplam
satırı döndürür (ör. 316); boş satırlar çıkarılınca §3.1'deki değer (272)
çıkar. **Çelişki yoktur, metodoloji farkıdır**; gelecek turlarda her iki sayı
da birlikte yazılır (toplam / dolu).

### §12.9 Yeni Dosya Keşfi — `DeviceRenderer.php` (2026-10-13 ikinci tur)

| # | Ölçüm | Sonuç | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `shared/src/Device/DeviceRenderer.php` dosyası var | evet (162 toplam / 144 dolu satır) | ReadAllLines 2026-10-13 | ✅ S2 |
| 2 | `final class DeviceRenderer` | satır 19 | dosya satır 19 | ✅ S4 |
| 3 | `__construct` cihaz doğrulama | satır 36–38 (`isValidDevice` → `InvalidArgumentException`) | dosya satır 36–38 | ✅ S4 |
| 4 | `tierOf()` DOM tier eşlemesi | satır 63–73 (phone / embedded / wide) | dosya satır 63–73 | ✅ S4 |
| 5 | OS-seviyesi tespit terimleri (`sysfs|SetupDi|udev|devnode|PnP|ioctl`) | **0 isabet** (4 dosyanın tamamı) | grep 2026-10-13 | ✅ S4 (negatif) |

**Yorum:** §3.1 listesi üç dosyayı ölçmüştür; `shared/src/Device/` altında
**dördüncü** bir PHP dosyası vardır (`DeviceRenderer.php` — uygulama-seviyesi
CSS/tier **render** sınıfı). Etiket: **IMPLEMENTED** (uygulama-seviyesi, 162/144
satır) — ancak bu da **keşif (enumeration) değildir**: cihaz bilgisini
`DeviceManager`'dan alır, DOM/tier çıktısı üretir. §3.2 negatif kanıtı bu
dosya **dahil** ölçülerek doğrulanmıştır (grep 0). `-kapsam.md` §13/1–3 üç
dosyalık ölçümünü tamamlar, **çürütmez** (envanter satırı ekleme yetkisi
beyazliste dışıdır → orkestratöre devredildi).

### §12.10 HEAD Tekrar Turu (2026-10-13, ikinci tur — `Invoke-WebRequest -Method Head -UseBasicParsing`, 20 sn)

| # | URL | §7 sonucu | İkinci tur | Sapma |
|---|---|---|---|---|
| 1 | https://learn.microsoft.com/ | ✅ 200 | ✅ 200 | 0 |
| 2 | https://learn.microsoft.com/en-us/windows/win32/api/setupapi/ | ✅ 200 | ✅ 200 | 0 |
| 3 | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | ✅ 200 | ✅ 200 | 0 |
| 4 | https://www.kernel.org/ | ✅ 200 | ✅ 200 | 0 |
| 5 | https://docs.kernel.org/ | ✅ 200 | ✅ 200 | 0 |
| 6 | https://docs.kernel.org/driver-api/index.html | ✅ 200 | ✅ 200 | 0 |
| 7 | https://docs.kernel.org/driver-api/driver-model/index.html | ✅ 200 | ✅ 200 | 0 |
| 8 | https://docs.kernel.org/driver-api/infrastructure.html | ✅ 200 | ✅ 200 | 0 |
| 9 | https://docs.kernel.org/admin-guide/sysfs-rules.html | ✅ 200 | ✅ 200 | 0 |
| 10 | https://docs.kernel.org/ABI/index.html | ❌ 404 | ❌ 404 | 0 (kullanılmadı) |
| 11 | https://devicetree.org/ | ✅ 200 | ✅ 200 | 0 |
| 12 | https://uefi.org/ | ❌ 403 | ❌ 403 | 0 (kullanılmadı) |
| 13 | https://www.usb.org/ | ✅ 200 | ✅ 200 | 0 |

**Sonuç:** 13 URL'nin 13'ü de §7 ile **aynı** sonucu döndürdü (11 ✅ 200 /
2 ❌). G3 URL kapısı için **sapma 0**; yeni kaynak alınmadı, kullanılmayan
2 URL kullanılmamaya devam ediyor.

### §12.11 Beyazliste / Dosya Bütünlüğü Kapısı (bu klasör — 2026-10-13)

| # | Dosya | Var mı | Authority = 1 (son satır) | `parent:` | `{...}`/`TBD` (gerçek yer tutucu) | Durum |
|---|---|---|---|---|---|---|
| 1 | `index.md` | ✅ | ✅ | 0 | 0 | ✅ |
| 2 | `os-donanim-tespiti-kapsam.md` | ✅ | ✅ | 0 | 0 (yalnız kural metni içinde anlatım) | ✅ |
| 3 | `os-donanim-tespiti-dogrulama.md` | ✅ | ✅ | 0 | 0 (yalnız kural metni içinde anlatım) | ✅ |
| 4 | `os-donanim-tespiti-ornekler.md` | ✅ (bu turda üretildi) | ✅ | 0 | 0 | ✅ |

**Kural:** "Yalnız kural metni içinde geçen `{...}`/`TBD` **anlatımdır**, yer
tutucu **değildir**" (ör. "`{...}` bırakılmaz" kuralı); gerçek yer tutucu =
doldurulmamış hücre/biçim. Sayım bu iki kategoriyi ayırır.

### §12.12 S1 Kapıları için Hazırlık — Windows gözlem adımları (şablon; UYGULANMADI)

| # | Kapı | Adım (araç adı düzeyinde — araç seçimi ⚠️ VERIFICATION REQUIRED) | Ham çıktı zorunluluğu | Durum |
|---|---|---|---|---|
| W1 | Cihaz listesi | OS'nin kendi cihaz listeleme yüzeyi (§5.2/#2–3) ile liste al | çıktı dosyası + tarih | ❌ AÇIK |
| W2 | Sınıf/ID okuma | Seçilen cihaz için sınıf + ID alanı oku | ham metin dosyası | ❌ AÇIK |
| W3 | Hotplug olayı | Olay dinleme yüzeyi (§5.2/#7) açıkken USB tak-çıkar | olay + zaman damgası | ❌ AÇIK |
| W4 | Sürücü bağlama | Seçilen cihazın sürücü durum alanı | durum satırı | ❌ AÇIK |
| W5 | Başarısız keşif | Sürücüsü olmayan cihaz (varsa) seç + hata sınıfı kaydet | hata çıktısı | ❌ AÇIK |

**Yasak:** Bu tabloya somut bir araç adı (PowerShell cmdlet'i, exe) veya IOCTL
numarası **uydurularak** yazılmaz; araç seçimi çalışan makinede seçilecek ve
`-ornekler.md` §2.4 formuna yazılacaktır.

### §12.13 S1 Kapıları için Hazırlık — Linux gözlem adımları (şablon; UYGULANMADI)

| # | Kapı | Adım (yüzey adı düzeyinde) | Ham çıktı zorunluluğu | Durum |
|---|---|---|---|---|
| L1 | sysfs listesi | §6.2/#1–2 cihaz ağacı yüzeyinden liste al | ağaç çıktısı + tarih | ❌ AÇIK |
| L2 | modalias/ID | Seçili cihazın modalias/ID alanı oku | ham metin | ❌ AÇIK |
| L3 | uevent kaydı | §6.2/#4–5 olay yüzeyi açıkken USB tak-çıkar | olay + anahtar | ❌ AÇIK |
| L4 | udev → `/dev` | Olay sonrası `/dev` düğümü + yetki | `ls`-tarzı ham çıktı | ❌ AÇIK |
| L5 | device-tree | §6.2/#6 yüzeyinden compatible değeri | ham dize | ❌ AÇIK |

**Yasak:** `/sys` alt yolu, uevent anahtarı veya modalias dizesi spesifik
değeri **uydurulmaz** — ham çıktıdan okunur.

### §12.14 Kod Kanıtı Derinleştirmesi — metod envanteri (2026-10-13 ikinci tur)

| # | Dosya | Sınıf/metin | Kanıt | Etiket |
|---|---|---|---|---|
| 1 | `DeviceDetector.php` | eşik sabitleri `PHONE_MAX=767` … `FOUR_K_TV_MAX=3840` (satır 21–26) | dosya satır 21–26 | IMPLEMENTED |
| 2 | `DeviceDetector.php` | `detect()` satır 57–92: başlık → TV → embedded → viewport → UA → varsayılan `desktop` | dosya satır 57–92 | IMPLEMENTED |
| 3 | `DeviceDetector.php` | `detectFromViewportAndContext()` satır 97–141 (1024×600→embedded, 1024×768→laptop ayrımı satır 110–117) | dosya satır 97–141 | IMPLEMENTED |
| 4 | `DeviceDetector.php` | `detectFromViewport()` satır 146–171 · `isTvAgent()` satır 176–189 · `isEmbeddedDevice()` satır 194–207 · `isDesktopOS()` satır 212–235 | dosya satırları | IMPLEMENTED |
| 5 | `DeviceManager.php` | `fromRequest()` satır 179–196 (viewport cookie `cm_viewport_w/h` satır 187–191) · `fromDevice()` satır 201–210 (geçersiz → `desktop` fallback satır 208) | dosya satırları | IMPLEMENTED |
| 6 | `DeviceManager.php` | 63 public metin envanteri (satır 145–658: `instance()` … `isValidDevice()`) | Select-String 2026-10-13 | IMPLEMENTED |
| 7 | `DeviceCssMap.php` | 3 sabit harita (DEVICE_CSS satır 8–16 · AUTH_DEVICE_CSS satır 18–26 · VIEW_MODE_CSS satır 28–33) + 3 getter (satır 35–48) | dosya satırları | IMPLEMENTED |
| 8 | `DeviceRenderer.php` | LINK_ID sabitleri satır 22–26 · `fromShell()` satır 45–54 (per-request singleton'a dokunmaz) · `tierOf()` satır 63–73 | dosya satırları | IMPLEMENTED |

**Sınır yorumu:** 8 satırın tamamı **uygulama-seviyesi** UI/tip tahmin ve
render kanıtıdır; tek satırı bile OS keşif yüzeyi (SetupAPI/sysfs/uevent/devnode)
kullanmaz — §3.2 grep 0 ile çapraz teyitli. Bu envanter `-kapsam.md` §13'ün
**derinleştirmesidir**, yeni iddia değildir.

### §12.15 Bu Turun Kanıt Sayımı Eki

| # | Kanıt türü | Adet | Kullanım |
|---|---|---|---|
| 1 | Yeni PHP dosyası ölçümü | 1 (`DeviceRenderer.php` 162/144) | §12.9 — envanter tamamlama |
| 2 | HEAD tekrar turu (aynı nesneler) | 13 | §12.10 — sapma 0 (yeniden doğrulama) |
| 3 | Metodoloji notu | 1 (toplam/dolu tablosu) | §12.8 |
| 4 | Beyazliste/bütünlük denetimi | 4 dosya | §12.11 |
| 5 | S1 hazırlık şablonu | 10 kapı (W1–W5 · L1–L5) | §12.12–§12.13 — uygulanmadı |
| 6 | Metod envanteri | 8 satır | §12.14 |

**Güncel toplam:** §12.7'nin 32 somut kanıt nesnesi + 2 **yeni** nesne
(`DeviceRenderer.php` ölçümü + beyazliste denetimi) = **34**; HEAD tekrar
turları mevcut nesneleri yeniden doğrular (yeni nesne sayılmaz).
**Platform gözlemi hâlâ: 0.**

---

## §13 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez. Bu dosya 11 adet HEAD-200 web URL'si (§7),
3 PHP repo dosyası ölçümü + 5 negatif repo ölçümü (§3), 3 ADR varlık/id
kaydı (§4) ve 5 sınır komşusu sayımı üzerine kuruludur; 403 ve 404 döndüren
2 URL kullanılmamıştır. **Hiçbir S1 platform gözlemi YAPILMAMIŞTIR** — tüm
gözlem kapıları (W1–W5 · L1–L5 · olay · envanter) açıktır ve "GEÇTİ" yazısı
yalnız S1 kanıtı ile yazılabilir. Doküman-varlık (HEAD 200) gözlem olarak
sunulmamıştır; repo'daki UI tahmin kodu keşif olarak sunulmamıştır (§3.2).
ADR-017 ve ADR-038 hakkında yalnız **varlık** iddiası kurulmuştur; ADR-064
okunmuş ve `DeviceManager` 7 tip kaydı çapraz teyitlenmiştir. Bu dalın K000
kök kaydı **APPROVAL REQUIRED** olarak işaretli kalmıştır (§8/14). `{...}` ve
`TBD` yer tutucusu kullanılmamış; bilinmeyenler `UNKNOWN` ile işaretlenmiştir.
Fiziksel/gözlemsel ölçüm **0**.

**İkinci tur eki (2026-10-13 — append, mevcut metin DEĞİŞTİRİLMEDİ):** §12.8–§12.15
bu dosyaya sonradan eklenmiştir. Ekleme sonrası tablo: PHP ölçüm dosyası
**4** oldu (`DeviceRenderer.php` 162/144 keşfedildi — §12.9); 8 adet HEAD-200
URL'sinin 13'lük kümesi **ikinci kez** HEAD ile doğrulandı ve **sapma 0**
çıktı (§12.10 — yeniden doğrulama, yeni nesne değil); somut kanıt nesnesi
**34**'e çıktı (§12.15). §12.8 metodoloji notu §3.1 satır sayılarının **dolu
satır** biçiminde ölçüldüğünü kayda geçirir — sayılar değişmemiştir. **S1
platform gözlemi yine 0'dır**; hiçbir gözlem kapısı kapanmamış, "GEÇTİ"
yazısı yazılmamıştır. Uydurma araç adı, IOCTL numarası veya `/sys` alt yolu
eklenmemiştir (§12.12–§12.13 yasak satırları).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode