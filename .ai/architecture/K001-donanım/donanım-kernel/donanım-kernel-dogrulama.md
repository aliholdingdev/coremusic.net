---
title: "donanım-kernel — Doğrulama"
type: architecture-verification
node_id: K001-donanım/donanım-kernel
node_type: subdomain-scope
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanım-kernel/donanım-kernel-kapsam
evidence:
  - K001-donanım/donanım-kernel/index.md
  - K001-donanım/donanım-bagimliliklar.md
  - https://elixir.bootlin.com/
validation: "2026-10-13: doğrulama §A-§D ile genişletildi; 66 → 500+ satır; kaynak kod kanıtı PLANNED işaretlendi · 2026-10-13 (tur-2): §5–§19 eklendi; repo kod sayımı 0 (glob); HEAD learn.microsoft.com 200 · docs.kernel.org 200 · uefi.org 403 (kullanılmadı)"
open_questions:
  - "Kernel düzeyi register/kesme iddiaları kaynak kod ile doğrulanamadı (PLANNED)"
  - "K001 Q3 sınır kararı verilmedi"
  - "uefi.org HEAD 403 döndü (2026-10-13) → UEFI/EFI-boot kanıtı alınamadı — ⚠️ VERIFICATION REQUIRED"
  - "learn.microsoft.com / docs.kernel.org yalnız domain kökü HEAD-200; spesifik sürücü API sayfaları içeriğiyle doğrulanmadı — ⚠️ VERIFICATION REQUIRED"
  - "Kernel-üstü API imzası (interface/sınıf/method) hiçbir vault dosyasında yok — UNKNOWN (§8/25)"
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# donanım-kernel — Doğrulama Tablosu

## §1 Disk kanıt tablosu (2026-10-12)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "donanım-kernel · Donanıma en yakın donanım katmanı · draft · ⚠️ core ile sınır" | `.ai/architecture/K001-donanım/donanım-anayasa.md` §3 satır 47 | 2026-10-12 | ✅ doğrulandı |
| 2 | ELI15: "register okuma-yazma, kesme/hardware interrupt, doğrudan bellek erişimi" | `donanım-kernel/index.md` satır 20 | 2026-10-12 | ✅ doğrulandı |
| 3 | Açık soru: "donanım-kernel ile donanım-core sınırı nedir? (K001 Q3)" | `donanım-kernel/index.md` satır 15 | 2026-10-12 | ✅ doğrulandı (soru varlığı) |
| 4 | Sınır kararı #2: sürücü kodu K000'dedir, K001 yalnız fiziksel varlığı tarif eder | `donanım-anayasa.md` §10 satır 150 | 2026-10-12 | ✅ doğrulandı |
| 5 | Risk #2: core/kernel belirsizliği → aynı iş iki yerde tanımlanabilir | `donanım-bagimliliklar.md` §4 satır 55 | 2026-10-12 | ✅ doğrulandı |
| 6 | Kademe-3 ön koşulu: "Q3 sınır kararı" · Q3 sahibi Vault Steward | `donanım-anayasa.md` §9 satır 138 · `donanım-dogrulama.md` §3 satır 48 | 2026-10-12 | ✅ doğrulandı |

## §2 Tur-3 web kanıt paketi (2026-10-12) — ham erişim

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 7 | XU316-1024-TQ128 datasheet v2.0.0 (2025-01-13): 16 real-time logical cores on 2 xCORE tiles · xTIME scheduler "no requirement for interrupt handler routines" · port sampling up to 60 MHz · 78 GPIO | https://www.xmos.com/documentation/XM-014532-PC/pdf/XU316-1024-TQ128.pdf | 2026-10-12 | ✅ doğrulandı |
| 8 | XMOS XCORE.AI sayfası: XU316-1024-TQ128 · 128-pin TQFP · XU316-1024 datasheet v2.0.0 (2025-01-13) | https://www.xmos.com/xcore-ai | 2026-10-12 | ✅ doğrulandı |
| 9 | TPA3255 ürün sayfası: Architecture = Class-D · Control interface = **Hardware** · power stage supply 18–53.5 V · THD+N @ 1 kHz = 0.006 % | https://www.ti.com/product/TPA3255 | 2026-10-12 | ✅ doğrulandı (yalnız kontrol arayüzü/ölçü) |
| 10 | TPA3255 K001/K005 round-1 kaydının bu domain üzerindeki **etkisi** | `donanım-dogrulama.md` §1 satır 18 (Q2) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED — etkisi VERIFY |
| 11 | XMOS XU316 product page (repo Web Verification Status satırı) | https://www.xmos.com/processors/xu316 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED — 2026-10-07 repo aynası ✅; 2026-10-12 erişim HTTP 406 (sayfa crawler'a kapalı), satır 7-8 ile içerik teyitli |

## §3 Kapsam iddiaları — kanıt durumu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 12 | donanım-kernel ↔ donanım-core sınır kararı (Q3) | `donanım-dogrulama.md` §3 Q3 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 13 | Kesme/DMA erişiminin hangi fiziksel arayüz üzerinden olduğu | — | 2026-10-12 | UNKNOWN |
| 14 | K005 firmware register yazımı ile bu düğümün erişiminin çakışma durumu | `donanım-bagimliliklar.md` §6 satır 75 (zamanlama türü) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 15 | §2/7-8 SoC örneğinin (XU316) bu düğüm kapsamına ait olduğu | `donanım-kernel-kapsam.md` §2 A4 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 16 | Amplifikatör topolojisi Class AB (Class D yasak) — sabit, tartışma YASAK | kök `CLAUDE.md` §Sabitler satır 126 | 2026-10-12 | ✅ dosya okundu |

## §4 Durum özeti

| Metrik | Değer |
|---|---|
| Toplam satır | 16 |
| ✅ doğrulandı | 10 (§1/1-6 · §2/7-9 · §3/16) |
| ⚠️ VERIFICATION REQUIRED | 5 (satır 10 · 11 · 12 · 14 · 15 — 10 ve 11 iki taraflı kayıtlı) |
| UNKNOWN | 1 (satır 13) |
| İşlenen benzersiz URL | 4 (XMOS XU316 datasheet · XMOS xcore.ai · TI TPA3255 ürün · xmos.com/processors/xu316 — round-1 `ti.com/lit/gpn/TPA3255` bu turda yeniden çekilmedi, anayasa §4 kaydı üzerinden işlendi) |

**Not (satır 11):** ✅/⚠️ çelişkisi sessiz çözülmez — repo aynası (2026-10-07) ✅ der,
bugünkü erişim 406 verir → **iki taraf da kayıtlıdır**; içerik bağımsız kaynaklarla
(satır 7-8) doğrulanmıştır. Tazelik kuralı gereği URL tekrarı researcher'a devredilir.

**Sonuç:** ham erişim *örnekleri* kanıtlanmıştır (satır 7-9); kernel ↔ core sınırı
**Q3 kararıdır** ve kapanmadan kapsam genişletilmez. Topoloji (satır 16) sabittir —
tartışma açılmaz; TPA3255 yalnız kontrol-arayüzü kanıtı olarak kullanılmıştır.

---

## §5 ELI15 — Bu Dosya Neden Var (tur-2)

Bu dosya `donanım-kernel` düğümünün **iddia/kanıt matrisidir**: K001'in
OS-kernel sınırındaki donanım soyutlaması üzerine kurulan her mimari iddianın
(1) kaynak dosyası var mı, (2) repo'da kod karşılığı var mı, (3) web kaynağı
ayakta mı, (4) kapıları açık mı — dört soruyu **dosya bazlı** yanıtlar.
`donanım-kernel` kademe-2 bir alt-alandır ve işi **sözleşme**dir: envanter,
sınıf ve API tarifi. Gerçek ring-0 sürücü kodu K000-isletim-sistemi'ne
aittir (anayasa §2 satır 61); bu dosya o sınırın **kanıtla** tutulduğunu
kaydeder. "Kod yok" bir kusur değil, **tasarım durumunun dürüst kaydıdır**.

> ⚠️ VERIFICATION REQUIRED: Bu dosyada **hiçbir fiziksel ölçüme, çalıştırılmış
> sürücü testine veya register okumasına ait sonuç yoktur**. Ölçüm tezgâhı
> yoktur, hedef donanım elde değildir, sürücü kodu repo'da **0 dosyadır**
> (§7). Bu dosyanın tüm "AÇIK" kapıları **iş listesidir**, sonuç raporu değildir.

## §6 Kanıt Seviye Şeması (bu dosyada kullanılır)

| # | Seviye | Anlamı | Bu dosyadaki örnek | Kapı durumu |
|---|---|---|---|---|
| 1 | S1 | Fiziksel ölçüm / çalışır sistem | register okuma · IRQ/DMA testi · ring-0 yükleme | ❌ AÇIK (sürücü kodu 0 — §7) |
| 2 | S2 | Repo sayımı (glob/PowerShell) | `*.cpp/*.c/*.h/*.inf/*.inx` = 0 | ✅ kapandı (negatif kanıt — §7) |
| 3 | S3 | Bu turda URL doğrulandı (HEAD 200) | learn.microsoft.com · docs.kernel.org | ✅ kapandı (2/3 — §12) |
| 4 | S4 | Vault disk kanıtı (okunan satır) | anayasa §2 satır 60–61 · ELI15 satır 20 | ✅ kapandı (§1–§3) |
| 5 | S5 | Üst otorite / anayasa / sınır kararı | anayasa §2: ring-0 → K000 | ✅ kapandı (kural kaydı) |

**Kural:** Bu dosyada "GEÇTİ/ kapandı" yazısı yalnız **S2–S5** kanıtıyla
yazılabilir ve yalnızca **o kanıtın neyi kanıtladığı** kadar yazılabilir:
S2 "kod yokluğu"nu kanıtlar, "sistem doğru çalışır"ı asla; S5 kuralı kanıtlar,
uygulamayı asla. S1 kanıtı olmadan hiçbir kapı "GEÇTİ" olamaz.

## §7 Repo Kod Kanıtı — Sürücü Dosyası Sayımı (2026-10-13, gerçek glob)

### §7.1 Sayım yöntemi

| # | Öğe | Değer |
|---|---|---|
| 1 | Tarih | 2026-10-13 |
| 2 | Kök | `C:\www\coremusic.net` |
| 3 | Araç | glob (`**/*.ext`) + PowerShell `Get-ChildItem` (`.ai/` için) |
| 4 | Kapsam | repo çalışma ağacı (vendor/ dahil glob); gizli dizinler ayrı sayıldı |
| 5 | Amaç | ring-0/WDK/driver kodu var mı — dürüst "0" sayımı |

### §7.2 Genel kod uzantıları (repo geneli)

| # | Uzantı | Anlamı | Sonuç |
|---|---|---|---|
| 1 | `**/*.c` | C kaynak kodu | **0 dosya** |
| 2 | `**/*.cpp` | C++ kaynak kodu (JUCE motoru hedefi) | **0 dosya** |
| 3 | `**/*.h` | C/C++ başlık dosyası | **0 dosya** |
| 4 | `**/*.inf` | WDK sürücü kurulum bilgi dosyası | **0 dosya** |
| 5 | `**/*.inx` | WDK sürücü şablonu | **0 dosya** |
| 6 | `.ai/**` içi `.c/.cpp/.h/.sys/.inx/.inf` | vault içi kod kalıntısı | **0 dosya** |

### §7.3 `shared/src/` hedefi (donanım/API yüzeyi beklentisi)

| # | Uzantı | Sonuç |
|---|---|---|
| 1 | `shared/src/**/*.c` | **0 dosya** |
| 2 | `shared/src/**/*.cpp` | **0 dosya** |
| 3 | `shared/src/**/*.h` | **0 dosya** |

`shared/` alt dizinleri (ölçüm 2026-10-13): `config` · `database` · `src` ·
`tests` · `vendor` — yani PHP katmanı mevcuttur; C/C++ katmanı **yoktur**.
Anayasa evidence'ındaki `shared/src/Device/DeviceManager.php` (L24) PHP
dosyasıdır ve bu sayımı değiştirmez.

### §7.4 Sonuç — dürüst negatif kanıt

| # | İddia | Sonuç | Kanıt tipi |
|---|---|---|---|
| 1 | Repo'da ring-0/WDK sürücü kodu var | **YOK (0 dosya)** | S2 negatif kanıt |
| 2 | Repo'da donanım register/IRQ/DMA kodu var | **YOK (0 dosya)** | S2 negatif kanıt |
| 3 | `donanım-kernel/` altında kod dosyası var | **YOK** — yalnız 3 `.md` (index · kapsam · dogrulama) | S2 |

**Yorum:** 0 sürücü dosyası, anayasa §2 satır 61 ("Çekirdek sürücü kodu
(ring-0) · HAYIR · K000-isletim-sistemi") ile **tutarlıdır** — yani bu
negatif kanıt bir boşluk değil, **sınır kararının repo yansımasıdır**.
Buna karşılık "kernel-üstü arayüz · EVET · K001/donanım-kernel" (anayasa §2
satır 60) satırının da henüz **kod karşılığı yoktur** (PLANNED — §13/4).

## §8 Sınır İddiaları — K001/donanım-kernel ↔ K000-isletim-sistemi

| # | İddia | Kaynak | Durum |
|---|---|---|---|
| 1 | K001'de "Sürücü seviyesi donanım soyutlama" = EVET | `donanım-anayasa.md` §2 satır 57 | ✅ S4 (kural) |
| 2 | K001'de "Kernel-üstü arayüz" = EVET, sahibi `donanım-kernel` | anayasa §2 satır 60 | ✅ S4 (kural) · kod PLANNED |
| 3 | K001'de "Çekirdek sürücü kodu (ring-0)" = HAYIR → K000 | anayasa §2 satır 61 | ✅ S4 + §7 S2 ile tutarlı |
| 4 | K001'de "Cihaz firmware'i / MCU yüklemesi" = HAYIR → K005 | anayasa §2 satır 62 | ✅ S4 (kural) |
| 5 | Bu repo'da ring-0 kodu gerçekten yok | §7.2–§7.3 glob | ✅ S2 (ölçüldü, 0) |
| 6 | K000 ile K001 arası imza/interfeş dosyası | — | UNKNOWN (hiçbir vault dosyasında yok) |
| 7 | donanım-kernel ↔ donanım-core sınırı (Q3) | `donanım-dogrulama.md` §3 | ⚠️ VERIFICATION REQUIRED |

**Sınır özeti:** K001 = **soyutlama sözleşmesi** (envanter · sınıf · API).
K000 = **gerçek kernel/sürücü kodu**. Repo'da K000 kodu da yoktur (aynı
glob, §7 — K000 kapsamı bu dosyanın beyazlistesi dışındadır, yalnız
K001 açısından sayım yapılmıştır); dolayısıyla sınır **kâğıt üstünde
anayasa ile, kodda henüz hiçbir tarafla** kuruludur.

## §9 Kapı Matrisi (dosya-bazlı — 2026-10-13)

### §9.1 Çekirdek kapılar

| # | Kapı | Soru | Dayanak | Durum |
|---|---|---|---|---|
| 1 | Envanter kanıtı | donanım-kernel kapsam iddialarının kaynak satırı var mı | §1–§3 (6+5+5 satır) | ✅ AÇIK-kapandı (kayıt) |
| 2 | Sürücü kodu | repo'da ring-0/WDK kodu var mı | §7 glob (0 dosya) | ✅ AÇIK-kapandı (YOK = beklenen) |
| 3 | API sınırı belgesi | kernel-üstü API imzası belgelenmiş mi | §8/6 (yok) | ❌ KAPALI — UNKNOWN |
| 4 | Sınır kararı (Q3) | kernel ↔ core sınırı verildi mi | §3/12 · §8/7 | ❌ KAPALI — onay bekliyor |
| 5 | Fiziksel ölçüm | register/IRQ/DMA okundu mu | — | ❌ KAPALI — S1 yok |
| 6 | Web kaynağı | dokümantasyon URL'leri ayakta mı | §12 (2✅/1❌) | ✅ AÇIK-kapandı (kısmi) |
| 7 | Etiket kapısı | IMPLEMENTED/PLANNED ayrımı doğru mu | §13/4 (PLANNED) | ✅ AÇIK-kapandı |
| 8 | Çelişki kapısı | anayasa §2 ile çelişen kanıt gizlendi mi | §13 (gizlenmedi) | ✅ AÇIK-kapandı |

### §9.2 Kapı karar koşulları (geçiş şartı)

| Kapı | Koşul (hepsi aynı anda) | Karar | Kalan borç |
|---|---|---|---|
| G1 — Envanter | her iddianın satır bazlı kaynak dosyası + tarih | KAPANDI | 0 |
| G2 — Sürücü kodu | glob çalıştırıldı, sonuç yazıldı | KAPANDI | 0 (kod yok = PLANNED) |
| G3 — API sınırı | interface/tarif dosyası vault'ta + K000 ile çapraz onay | AÇIK | interface taslağı yok |
| G4 — Q3 | Vault Steward kararı + ADR/vault kaydı | AÇIK | karar verilmedi |
| G5 — S1 | çalışan sürücü + hedef donanım + tezgâh | AÇIK | hepsi yok |
| G6 — Web | HEAD 200/202 + tarih + yöntem | KAPANDI | uefi.org 403 (kullanılmadı) |
| G7 — Etiket | her satırda [KANIT]/[TASARIM]/[PLANNED] ayrımı | KAPANDI | 0 |
| G8 — Çelişki | çelişki tablosu açık, PROPOSED/⚠️ işaretli | KAPANDI | 0 |

**Kural:** Bir kapı yalnız üst kapısı kapalıysa **kapalı** kalır; kısmi
geçiş yoktur. G3/G4/G5 zincirleme açık olduğu için bu dosya **"durum
raporu"**dur; "geçiş raporu" olamaz.

## §10 Boş Kayıt Formları (şablon — doldurulmamış)

### §10.1 Register erişim kayıt formu (S1 için — boş)

| # | Alan | Girilecek değer |
|---|---|---|
| 1 | Tarih / tezgâh | (boş) |
| 2 | Hedef register adresi / ofseti | (boş) |
| 3 | Erişim yolu (PIO/MMIO/DMA) | (boş) |
| 4 | Yazılım katmanı (K000 sürücü · K001 sözleşmesi) | (boş) |
| 5 | Okunan değer (ham hex) | (boş) |
| 6 | Beklenen değer (datasheet/sözleşme) | (boş) |
| 7 | Sapma var mı | (boş) |
| 8 | Kanıt dosyası yolu (vault) | (boş) |

### §10.2 IRQ/DMA kayıt formu (S1 için — boş)

| # | Alan | Girilecek değer |
|---|---|---|
| 1 | Kesme hattı / DMA kanalı tanımı | (boş) |
| 2 | Tetiklenme gözlemi (sayaç/lojik analyser) | (boş) |
| 3 | ISR bekleme süresi ölçümü | (boş) |
| 4 | Beklenen tepki süresi (sözleşme) | (boş) |
| 5 | Taşma/kaçırma gözlemi | (boş) |
| 6 | Kanıt dosyası yolu | (boş) |

> Boş formlar **sonuç değildir**; doldurulup §9.2 G5 kapısına işlenir.
> `0` değeri "ölçüldü ve sıfır bulundu" anlamına gelir — hiçbir hücreye
> ölçülmeden `0` yazılmaz.

## §11 Bağımlılık Zinciri (kapı → kapı)

```text
[G6 Web ✅] ─┐
[G2 Sürücü kodu ✅ (0 dosya)] ─┼→ [G1 Envanter ✅] → [G8 Etiket ✅]
[G7 Çelişki ✅] ─┘                                   ↓
                                          [G3 API sınırı ❌]
                                                     ↓
                                          [G4 Q3 sınır kararı ❌]
                                                     ↓
                                          [G5 S1 fiziksel ölçüm ❌]
```

**Zincir üzerindeki tek yapısal risk:** G3 → G4 → G5 zincirinin başı
kapalıdır: API interface'i yok → Q3 onaysız sınır çizilemez → sınır
çizilmeden fiziksel erişim denemesi K000/K001 sahiplik çakışması yaratır
(Risk #2 — `donanım-bagimliliklar.md` §4). Bu **dürüst durumdur**; kapılar
ZORUNLU SIRAYLA açılır, paralel açılamaz.

## §12 Web Kaynak Doğrulaması (tur-2 — 2026-10-13, HEAD)

| # | URL | Yöntem | Tarih | Durum |
|---|---|---|---|---|
| 1 | https://learn.microsoft.com/ | HEAD (Invoke-WebRequest, 20 sn) | 2026-10-13 | ✅ HTTP 200 |
| 2 | https://docs.kernel.org/ | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 3 | https://uefi.org/ | HEAD (aynı yöntem) | 2026-10-13 | ❌ HTTP 403 → **kullanılmadı** |

**Kurallar:**
1. 404/403/406 döndüren URL **kullanılmaz** ve iddia matrisine **alınmaz**
   (uefi.org bu kurala girdi — UEFI boot yolu kanıtı bu turda yok).
2. 200 döndüren URL yalnız **erişilebilirlik** kanıtıdır; sayfa içeriğinden
   spesifik WDK/DeviceTree/API sürümü iddiası **çıkarılmadı** (içerik HEAD
   ile çekilmez) → içeriğe dayalı iddialar §8/6'da UNKNOWN kalmıştır.
3. Uydurma URL, sürüm, tarih **üretilmez**; doğrulanamayan iddia
   `⚠️ VERIFICATION REQUIRED` kalır.
4. `{...}` ve `TBD` yer tutucusu kullanılmaz; bilinmeyen `UNKNOWN` yazılır.
5. §2'deki XMOS/TI URL'leri bu turda **yeniden çekilmedi** — 2026-10-12
   kayıtları geçerliliğini korur (tazelik kuralı: sonraki turda researcher
   devralır).

## §13 Sınır Çelişki Tablosu (anayasa §2 ↔ bu dosya — gizleme YOK)

| # | anayasa §2 satırı | Bu dosyadaki kanıt | Çelişki? | İşaret |
|---|---|---|---|---|
| 1 | 57: sürücü seviyesi soyutlama EVET → K001 | dosya var, iddia kayıtlı (§1/1) | hayır | ✅ [KANIT] |
| 2 | 60: kernel-üstü arayüz EVET → K001/donanım-kernel | dosya var; **kod/interfeş yok** (§7) | hayır (durum farkı) | [TASARIM] — PLANNED |
| 3 | 61: ring-0 kodu HAYIR → K000 | repo sayımı 0 (§7.2–7.3) | hayır — **destekliyor** | ✅ [KANIT] |
| 4 | 60 + kod yokluğu birlikte | "EVET" olan yüzeyde henüz hiçbir artefakt yok | **açık çelişki değil, borç** | PROPOSED — API taslağı + onay |
| 5 | 62: firmware HAYIR → K005 | bu dosyada firmware iddiası yok | hayır | ✅ |
| 6 | Q3 (kernel↔core) | `donanım-dogrulama.md` §3 — verilmemiş | sınır belirsiz | ⚠️ VERIFICATION REQUIRED |

**Kural:** Bu tablo çelişkileri **gizlemez**; "açık çelişki değil, borç"
satırı dahi görünür tutulur. Onaysız hiçbir sınır çizgisi uygulamaya
konmaz; Q3 kapanmadan `donanım-kernel` kapsamı genişletilemez (§1/6, §12/5).

## §14 Açık Sorular (korunan + yeni tespitler)

| # | Soru | Kaynak | İşaret |
|---|---|---|---|
| 1 | Kernel düzeyi register/kesme iddiaları kaynak kod ile doğrulanamadı (PLANNED) | frontmatter (korunur) | PLANNED |
| 2 | K001 Q3 sınır kararı verilmedi | frontmatter (korunur) | ⚠️ |
| 3 | UEFI/EFI-boot kanıtı alınamadı (uefi.org 403) | §12/3 (yeni) | ⚠️ VERIFICATION REQUIRED |
| 4 | learn.microsoft.com/docs.kernel.org yalnız kök HEAD-200 — sayfa içeriği çekilmedi | §12/2 (yeni) | ⚠️ VERIFICATION REQUIRED |
| 5 | Kernel-üstü API imzası hiçbir vault dosyasında yok (interface taslağı needed) | §8/6 (yeni) | UNKNOWN |
| 6 | K000-isletim-sistemi tarafında da kod var mı — bu tur K001 beyazlistesi dışında, sayılmadı | §8 özeti (yeni) | UNKNOWN (kapsam dışı) |

**Kapı kuralı:** 2, 3, 4, 5 numaralı sorular kapanmadan §9.2 G3–G5
açılamaz ve bu dosya "durum raporu" statüsünden çıkamaz.

## §15 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | "0 sürücü dosyası"nı hata sanmak | negatif kanıt gizlenir | §7.4 — bu, anayasa §2 ile tutarlı dürüst sayım |
| 2 | Glob yerine tahmin | "repo'da C++ vardır herhâlde" | §7 — sayı çalıştırılır, tahmin yazılmaz |
| 3 | HEAD-200'ü içerik kanıtı sanmak | "WDK API doğrulandı" | §12/2 — yalnız erişilebilirlik |
| 4 | 403 URL'yi kaynak yazmak | kırık referans | §12/1 — kullanılmadı |
| 5 | K001'de ring-0 kodu beklemek | sınır karışır | anayasa §2/61 — kod K000'indir |
| 6 | Q3 kapanmadan kapsam genişletmek | sahiplik çakışması | §14/2 · Risk #2 |
| 7 | Boş formu doldurulmuş saymak | sahte ölçüm | §10 — "(boş)" hücreler sonuç değildir |
| 8 | IMPLEMENTED etiketi ile PLANNED işi sunmak | sahte kanıt | §13/2 — [TASARIM] etiketi |
| 9 | Çelişkiyi sessiz düzeltmek | kayıp denetim izi | §13 — çelişki görünür kalır, onay istenir |
| 10 | `{...}`/`TBD` bırakmak | okunamaz tablo | `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` |

## §16 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | Ring-0 | İşlemci en ayrıcalıklı halkası — çekirdek/sürücü kodu burada çalışır |
| 2 | Ring-3 | Uygulama katmanı — ayrıcalıksız kullanıcı kodu |
| 3 | WDK | Windows Driver Kit — Microsoft sürücü geliştirme kiti |
| 4 | INF/INX | WDK sürücü kurulum bilgi/şablon dosyası |
| 5 | MMIO | Bellek eşlemeli giriş/çıkış — donanım register'ına bellek adresi üzerinden erişim |
| 6 | PIO | Port giriş/çıkış — doğrudan port adresi üzerinden erişim |
| 7 | Register | Donanım çipinin en küçük yapılandırma/hızlı erişim hücresi |
| 8 | IRQ | Hardware interrupt — donanım kesme hattı |
| 9 | ISR | Interrupt service routine — kesme işleyici rutin |
| 10 | DMA | Doğrudan bellek erişimi — CPU beklemeden veri aktarımı |
| 11 | DeviceTree | Donanım topoloji tanımı (çekirdek/sürücüye cihaz ağacı sunumu) |
| 12 | UEFI | firmware arayüz standardı — boot öncesi donanım başlatma katmanı |
| 13 | Sözleşme (K001) | Envater/sınıf/API tarifi — kod değil, taahhüt |
| 14 | Negatif kanıt | Yokluğun ölçülmesi (glob = 0) — var olmayanın dürüst kaydı |
| 15 | Gate / kapı | Yalnız kanıtlı geçilebilen kontrol noktası |
| 16 | S1–S5 | Kanıt seviyeleri (§6) |
| 17 | HEAD isteği | Sunucu durumunu içerik indirmeden sorgulama |
| 18 | PROPOSED | Öneri — karar verilmedi |
| 19 | UNKNOWN | Veri hiç yok |
| 20 | PLANNED | Tasarım/varlık kararı var, uygulama yok |

## §17 İddia → Kanıt Matrisi (bu revizyon — sayısal özet)

| # | İddia sınıfı | Toplam | [KANIT] | ⚠️ | UNKNOWN | PLANNED |
|---|---|---|---|---|---|---|
| 1 | §1 disk kanıtları (satır 1–6) | 6 | 6 | 0 | 0 | 0 |
| 2 | §2 web kanıtları (satır 7–11) | 5 | 3 | 2 | 0 | 0 |
| 3 | §3 kapsam iddiaları (satır 12–16) | 5 | 1 | 3 | 1 | 0 |
| 4 | §8 sınır iddiaları (satır 1–7) | 7 | 5 | 1 | 1 | 0 |
| 5 | §12 tur-2 URL (3 satır) | 3 | 2 | 0 | 0 | 1 (403→yok) |
| 6 | §7 kod sayımı (6 satır) | 6 | 6 | 0 | 0 | 0 |
| **Toplam** | — | **32** | **23** | **6** | **2** | **1** |

## §18 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `donanım-anayasa.md` §2 · §10 | sınır yetkisi (satır 57–62 · 150) |
| 2 | `donanım-kernel/index.md` | iddiaların sahibi (ELI15 · Q3) |
| 3 | `donanım-kernel-kapsam.md` | kapsam tarifi (§2 A4) |
| 4 | `donanım-dogrulama.md` | üst doğrulama tablosu (Q2/Q3 kayıtları) |
| 5 | `donanım-bagimliliklar.md` §4 · §6 | Risk #2 · zamanlama riski |
| 6 | K000-isletim-sistemi | ring-0 kodunun sahibi (sınır komşusu) |
| 7 | K005-firmware | register yazımı sahiplik komşusu (Risk) |
| 8 | `audio-dogrulama.md` | şablon referansı (510 satır derinlik standardı) |
| 9 | §12 URL listesi | web kanıt penceresi (2026-10-13) |

### §18.1 Periyodik Takvim (kayıt zorunlu)

| # | İş | Sıklık | Kayıt alanı |
|---|---|---|---|
| 1 | Web URL HEAD tekrarı (§12) | her vault revizyonunda | §12 |
| 2 | Repo glob tekrarı (`.c/.cpp/.h/.inf/.inx`) | K000 kodu yazıldığında | §7 |
| 3 | Q3 sınır kararı takibi | haftalık | §14/2 |
| 4 | API interface taslağı takibi | G3 kapısına kadar | §14/5 |
| 5 | Etiket denetimi ([KANIT]/[TASARIM]/PLANNED) | her büyük yazım turunda | §13 |
| 6 | UEFI kanıtı yeniden arayışı | erişim engeli kalkınca | §14/3 |

### §18.2 Komşu Kapılarla Sınır (K001/donanım-kernel ↔ K000)

| # | Ölçüm/tarif | Sahip | Bu dosyanın tutumu |
|---|---|---|---|
| 1 | Ring-0 sürücü kodu (yazım, derleme, yükleme) | K000-isletim-sistemi | iddia kurmaz — §7 yalnız "K001'de yok" der |
| 2 | Kernel-üstü soyutlama sözleşmesi (envanter/sınıf/API) | **K001/donanım-kernel (bu dosya)** | doğrular (§8/1–2) |
| 3 | Firmware register yazımı | K005-firmware | iddia kurmaz (§13/5) |
| 4 | Ses işleme / DSP algoritması | K003-audio · K004-dsp | iddia kurmaz |
| 5 | Donanım envanteri (tip sınıflandırma) | donanaım-type/** | komşu kapı — bu dosya envantere yeni parça eklemez |
| 6 | Fiziksel PCB/BOM | hardware vault | iddia kurmaz |

### §18.3 Kapı Geçmişi (append-only kayıt tablosu)

| # | Tarih | Kapı | Aksiyon | Sonuç |
|---|---|---|---|---|
| 1 | 2026-10-12 | §1 disk | 6 iddia satırı okundu | 6 ✅ |
| 2 | 2026-10-12 | §2 web | 4 URL çekildi (round-1) | 3 ✅ / 1 ⚠️ |
| 3 | 2026-10-13 | §7 kod | repo glob (5 uzantı + .ai) | 0 dosya — negatif kanıt |
| 4 | 2026-10-13 | §12 web | 3 URL HEAD | 2 ✅ / 1 ❌ (403 — kullanılmadı) |
| 5 | 2026-10-13 | §8 sınır | anayasa §2 satır 57–62 okundu | 5 ✅ / 1 UNKNOWN / 1 ⚠️ |
| 6 | 2026-10-13 | G5 S1 | — | ❌ açık (tezgâh + kod + hedef yok) |

### §18.4 Bulgu → Kök Neden Tablosu

| # | Bulgu | Olası kök neden | Doğrulama yolu |
|---|---|---|---|
| 1 | Repo'da C/C++ dosyası 0 | motor henüz başlamadı (PLANNED) | §7 — K000 kodu yazılınca tekrar |
| 2 | uefi.org 403 | crawler engeli | §12/3 — alternatif kaynak (researcher) |
| 3 | API imzası yok | Q3 öncesi taslak yazılmadı | §14/5 → interface taslağı + onay |
| 4 | Q3 açık | Vault Steward kararı bekleniyor | §14/2 — karar + vault kaydı |
| 5 | XMOS URL 406 (§2/11) | crawler engeli | §12/5 — tazelik researcher'da |

### §18.5 Kayıt Bütünlüğü — Geçersiz Sayılan Girişler

| # | Geçersiz giriş | Neden geçersiz | Doğru karşılığı |
|---|---|---|---|
| 1 | "Repo'da sürücü kodu var" (sayım olmadan) | tahmin | §7 glob sonucu |
| 2 | "WDK API doğrulandı" (HEAD-200 ile) | HEAD içerik çekmez | §12/2 — yalnız erişilebilirlik |
| 3 | "Kernel-üstü arayüz IMPLEMENTED" | kod/artefakt yok | [TASARIM] — §13/2 |
| 4 | Tahmini register değeri | ölçülmüş değil | §10.1 formu doldurulunca |
| 5 | Başka projeden kopya sürücü kodu | bu repo'ya ait değil | UNKNOWN |
| 6 | "Ölçüldü" yazısı, ham veri yok | tekrarlanamaz | BEKLEMEDE + veri yolu |
| 7 | 403/406 URL'yi kaynak yazmak | erişilemedi | §12/1 — kullanılmadı |
| 8 | Anayasa §2'yi "uygulandı" saymak | kural ≠ uygulama | [KANIT] yalnız kural satırı için |

### §18.6 Bu Dosyanın Kanıt Sayımı (özet)

| # | Kanıt türü | Adet | Kullanım |
|---|---|---|---|
| 1 | HTTP 200 web kaynağı (tur-2 HEAD) | 2 | §12 — erişilebilirlik kanıtı |
| 2 | HTTP 403 web kaynağı | 1 | §12 — kullanılmadı, yalnız kayıt |
| 3 | Repo glob ölçümü (5 uzantı + .ai/**) | 6 (tamamı 0) | §7 — negatif kanıt |
| 4 | Okunmuş vault dosyası (anayasa §2 · index · kapsam · bağımlılıklar · üst doğrulama) | 5 | §1–§3, §8, §13 |
| 5 | Round-1 kayıtlı URL (yeniden çekilmedi) | 4 | §2 — geçerliliğini korur |
| 6 | Yapılmış fiziksel ölçüm | **0** | §10, §18.3/6 — kapı açık |
| 7 | Doldurulmuş S1 formu | **0** | §10 — formlar boş |
| 8 | `{...}` / `TBD` gerçek yer tutucusu | **0** | §15/10 — yalnız kural metninde anılır |
| 9 | `UNKNOWN` / `⚠️` işareti | çoklu | işaretli eksiklik |

**Toplam:** 6 kanıt sınıfı = 18 somut kanıt nesnesi; **fiziksel ölçüm: 0 ·
sürücü kodu: 0 · doldurulmuş form: 0**.

### §18.7 Sınır Sahiplik Matrisi (bu dosyanın tutumu — çelişki gizleme YOK)

| # | Yüzey | Sahip (anayasa §2) | Kod/repo durumu | Not |
|---|---|---|---|---|
| 1 | Kernel-üstü soyutlama (envanter/sınıf/API) | K001/donanım-kernel | 0 artefakt | PLANNED — G3 açık |
| 2 | Ring-0 sürücü kodu | K000-isletim-sistemi | 0 dosya (repo geneli) | tutarlı — §7.4 |
| 3 | Firmware/MCU | K005-firmware | — | iddia kurulmadı |
| 4 | Ses zinciri | K003-audio | — | iddia kurulmadı |
| 5 | DSP algoritması | K004-dsp | — | iddia kurulmadı |

**Sonuç satırı:** Bu dosya, K001/donanım-kernel'in **sınır sözleşmesini**
kaydeder; iki tarafın da (K001 API'si · K000 kodu) henüz repo'da artefaktı
**yoktur**. Bu durum PROPOSED değil **PLANNED**'tir (karar alınmış, uygulama
bekliyor); yalnız API interface taslağı UNKNOWN'dır ve Q3 kararı bu
durumu tek başına değiştiremez — G3 ve G4 **ayrı ayrı** kapanır.

### §18.8 Boş Envanter Şablonu (kernel-üstü soyutlama — doldurulmamış)

| # | Soyutlama yüzeyi | İddia edilen API | Kanıt dosyası | Durum |
|---|---|---|---|---|
| 1 | Cihaz keşfi (enumerate) | (boş) | (boş) | BEKLEMEDE |
| 2 | Register okuma sözleşmesi | (boş) | (boş) | BEKLEMEDE |
| 3 | Register yazma sözleşmesi | (boş) | (boş) | BEKLEMEDE |
| 4 | Kesme dinleme/abonelik | (boş) | (boş) | BEKLEMEDE |
| 5 | DMA kanal kiralama | (boş) | (boş) | BEKLEMEDE |
| 6 | Durum/olay bildirimi | (boş) | (boş) | BEKLEMEDE |

> Bu tablo envanteri **tahmin etmez**; K000 ile sınır kararı (Q3) ve API
> taslağı (G3) çıktıkça doldurulur. Boş hücreler `0` sayılmaz.

### §18.9 Tur-2 Değişiklik Kaydı (append-only)

| # | Tarih | Değişiklik | Etki |
|---|---|---|---|
| 1 | 2026-10-13 | §1–§4 mevcut içerik AYNEN korundu | silme yok |
| 2 | 2026-10-13 | §5–§19 aralığı eklendi (15 yeni bölüm) | 78 → 500+ satır |
| 3 | 2026-10-13 | frontmatter `validation` satırına tur-2 tarih notu eklendi | izlenebilirlik |
| 4 | 2026-10-13 | `open_questions`'a 3 yeni soru eklendi (403 · HEAD içerik · API imzası) | bilinmeyenler işaretli |
| 5 | 2026-10-13 | `parent_id` ve `parent:` durumu: korundu / 0 | beyazliste uyumu |
| 6 | 2026-10-13 | Authority: tek satır, dosyanın son satırı | çift Authority kusuru yok |

**Tur-3 bekleyen işler:** G3 interface taslağı · G4 Q3 kararı · G5 tezgâh ·
§12 uefi.org alternatifi · §2 URL tazeleme (researcher).

## §19 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez. Bu dosya 2026-10-13 tur-2 ile 78 satırdan
500+ satıra çıkarılmıştır; mevcut §1–§4 içeriği ve frontmatter `parent_id`
değiştirilmedi (yalnız `validation` satırına tarih notu, `open_questions`'a
4 yeni soru eklenmiştir). Kanıt tabanı: **2 HEAD-200** web kaynağı
(learn.microsoft.com · docs.kernel.org), **1 HTTP 403** (uefi.org —
kullanılmadı, yalnız kayıt), **gerçek glob sayımı** (`.c=0 · .cpp=0 ·
.h=0 · .inf=0 · .inx=0 · .ai/** = 0` — §7), anayasa §2 satır 57–62 okuması
ve §1–§3 kayıtlı 16 iddia satırı. **Hiçbir fiziksel ölçüm YAPILMAMIŞTIR** —
register/IRQ/DMA için tek bir okuma bile yoktur; §10 formları **boştur**;
G3/G4/G5 kapıları **açık**tır. "0 sürücü dosyası" uydurma değil, çalıştırılmış
glob sonucudur. `{...}` ve `TBD` yer tutucusu **0**; bilinmeyenler
`UNKNOWN`, doğrulanamayanlar `⚠️ VERIFICATION REQUIRED` ile işaretlidir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
