---
title: "K000 İşletim Sistemi — Dil Kapsama Matrisi Raporu"
type: report
category: reports
version: 1.1.0
status: active
authority: "SSOT: .ai/architecture/K000-isletim-sistemi/ — tarama 2026-10-14 · §2/§4 taze 2026-10-15"
updated: 2026-10-15
tier: 2
domain: architecture
ssot: false
risk: medium
owner: "Vault Steward"
depends-on: [".ai/architecture/K000-isletim-sistemi/index.md"]
---

# K000 — 13 Dil Kapsama Matrisi (salt-okunur tarama)

> **Amaç:** Kullanıcı kapsama kararı (2026-10-14): **13 dil yüzeyi** — C · C++ · Rust ·
> Python · Go · C# · PHP · JavaScript/Node · Zig · Swift · Java/Kotlin · Bash ·
> PowerShell. Bu rapor mevcut durumu **kod bloğu sayısı** ile ölçer, boşlukları listeler.
> **Yöntem:** K000 ağacı altındaki tüm `*kod-seti.md` + `*ornekler.md` dosyalarındaki
> açılış-fence (`^```dil`) sayaçları — tahmin yok, disk ölçümü.

## §1 Kapsam

| Öğe | Değer |
|---|---|
| Taranan dizin | `.ai/architecture/K000-isletim-sistemi/` (17 yol: 13 alt düğüm + 4 kök) |
| Taranan dosya | 69 md (`*kod-seti.md` ×18 · `*ornekler.md` ×51) |
| Ölçüm birimi | Kod bloğu sayısı (fence sayımı · baseline 2026-10-14 · §2/§4 taze 2026-10-15) |
| Ekleme hedefi (karar ②) | 18 kod-seti dosyasının tamamı (kullanıcı onayı 2026-10-14) · **P1–P7 TAMAMLANDI 2026-10-15** |

## §2 ANA MATRİS — 13 dil × 10 ana kod-seti (blok sayısı · **taze 2026-10-15**)

> **P1–P7 ekleme turları tamamlandı** — bu tablo ekleme sonrası disk ölçümüdür
> (2026-10-15, fence taraması). Eski "0 blok" tablosu §3/§5'te tarihsel baseline
> olarak korunmuştur.

| # | Dosya | Satır | C | C++ | Rust | Py | Go | C# | PHP | JS | Zig | Swift | J/K | Bash | PS | Dil |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | `turu-kod-seti` | 1038 | 2 | 1 | 4 | 6 | 6 | 4 | 6 | 4 | 1 | 1 | 2 | 1 | 1 | **13** |
| 2 | `core-kod-seti` | 1088 | 4 | 1 | 3 | 3 | 3 | 3 | 2 | 2 | 1 | 1 | 2 | 1 | 1 | **13** |
| 3 | `kernel-kod-seti` | 894 | 4 | 2 | 3 | 2 | 1 | 2 | 0 | 0 | 1 | 0 | 2 | 0 | 0 | 8 |
| 4 | `api-kod-seti` | 846 | 3 | 1 | 2 | 1 | 1 | 1 | 3 | 1 | 1 | 1 | 2 | 2 | 1 | **13** |
| 5 | `runtime-kod-seti` | 724 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 2 | 2 | 1 | **13** |
| 6 | `security-kod-seti` | 804 | 2 | 1 | 1 | 1 | 2 | 2 | 1 | 1 | 1 | 1 | 2 | 2 | 2 | **13** |
| 7 | `observability-kod-seti` | 752 | 3 | 1 | 1 | 2 | 2 | 3 | 3 | 2 | 1 | 1 | 2 | 1 | 0 | 12 |
| 8 | `plan-kod-seti` | 1027 | 4 | 2 | 2 | 3 | 3 | 2 | 4 | 1 | 1 | 1 | 2 | 2 | 2 | **13** |
| 9 | `testing-kod-seti` | 756 | 1 | 1 | 2 | 2 | 2 | 1 | 2 | 2 | 1 | 1 | 2 | 1 | 1 | **13** |
| 10 | `os-donanim-tespiti-kod-seti` | 1155 | 6 | 3 | 1 | 3 | 2 | 2 | 1 | 0 | 1 | 1 | 2 | 2 | 2 | 12 |
| — | **TOPLAM (blok)** | 9.084 | 30 | 14 | 20 | 24 | 23 | 21 | 23 | 14 | 10 | 9 | 20 | 14 | 11 | — |

> J/K = Java/Kotlin (2 fence = 1 Java + 1 Kotlin). `kernel` (8/13): PHP/JS/Bash/PS/Swift
> boşlukları dosyanın semantik filtresiyle N/A kayıtlıdır (bkz kernel kod-seti §3).
> `observability` PS=0 ve `donanim` JS=0 da N/A kayıtlıdır.

## §3 TEMEL BULGU (zero-hallucination — disk kanıtlı · **2026-10-14 baseline — P1–P7 sonrası GEÇERSİZ**, taze durum §2/§4)

> ⚠️ Bu bölümdeki G1–G6 bulguları **ekleme öncesi** taramanın kaydıdır ve tarihsel
> değer olarak korunur; güncelleme talep edilmez. Güncel durum: Zig 16 blok /
> 18 dosyanın 16'sında, Swift 18 blok / 13 dosyada, J/K 32 blok / 15 dosyada (§4).

| # | Bulgu | Kanıt |
|---|---|---|
| **G1** | **Zig = 0 blok / 69 dosyanın tamamında** | fence taraması: ` ```zig ` = 0 |
| **G2** | **Swift = 0 blok / 10 ana kod-setinin tamamında** (yalnız turu altı `ios`=5, `macos`=2 ve birkaç `*-ornekler.md`'de var: coreaudio 9, observability/log 1-1, plan 3, bootstrap 1) | §2 + §4 |
| **G3** | **Java/Kotlin = 0 blok / 10 ana kod-setide** (Kotlin yalnız `turu/android-kod-seti`=4; Java yalnız `kernel-ornekler`=1) | §2 + §4 |
| **G4** | 10 dosyanın hiçbiri 13 dilin tamamını kapsamıyor; en yüksek: turu/core/plan = 10/13 | §2 "Dil" sütunu |
| **G5** | `kernel` (4/13) ve `runtime` (5/13) en dar dosyalar — mevcut 10 dil setinde bile eksik | §2 satır 3, 5 |
| **G6** | Mevcut 10 dosya **10 dil setiyle** yazıldı (plan sözleşmesi) — Zig/Swift/Java/Kotlin sonradan eklenen istek, sözleşme dışı | `K000-kod-seti-expansion-plan.md` §1 |

## §4 TAM KIRILIM — 18 kod-seti dosyası (turu altı platformlar dahil · **taze 2026-10-15**)

> P1–P7 sonrası disk ölçümü (fence taraması 2026-10-15). 0 olan hücrelerin tamamı
> dosya-içi `N/A`/çapraz-referans kayıtlıdır (R4 sahiplik kuralı) — blok yokluğu
> boşluk değil, semantik karardır.

| Dosya | Satır | C | C++ | Rust | Py | Go | C# | PHP | JS | Zig | Swift | J/K | Bash | PS | Dil |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| turu (kök) | 1038 | 2 | 1 | 4 | 6 | 6 | 4 | 6 | 4 | 1 | 1 | 2 | 1 | 1 | 13 |
| turu/android | 615 | 4 | 1 | 1 | 0 | 1 | 1 | 0 | 1 | 1 | 0 | 4 | 1 | 0 | 9 |
| turu/ios | 591 | 3 | 1 | 1 | 0 | 1 | 0 | 0 | 0 | 1 | 5 | 0 | 1 | 1 | 8 |
| turu/linux | 746 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 2 | 1 | 1 | 13 |
| turu/macos | 665 | 4 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 2 | 2 | 2 | 1 | 13 |
| turu/reactos | 847 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 2 | 1 | 1 | 13 |
| turu/tizen | 777 | 1 | 1 | 1 | 1 | 1 | 1 | 0 | 1 | 0 | 0 | 0 | 1 | 0 | 8 |
| turu/webos | 755 | 1 | 1 | 1 | 1 | 1 | 0 | 0 | 1 | 0 | 0 | 0 | 1 | 0 | 7 |
| turu/windows | 691 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 0 | 2 | 1 | 1 | 12 |
| core | 1088 | 4 | 1 | 3 | 3 | 3 | 3 | 2 | 2 | 1 | 1 | 2 | 1 | 1 | 13 |
| kernel | 894 | 4 | 2 | 3 | 2 | 1 | 2 | 0 | 0 | 1 | 0 | 2 | 0 | 0 | 8 |
| api | 846 | 3 | 1 | 2 | 1 | 1 | 1 | 3 | 1 | 1 | 1 | 2 | 2 | 1 | 13 |
| runtime | 724 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 1 | 2 | 2 | 1 | 13 |
| security | 804 | 2 | 1 | 1 | 1 | 2 | 2 | 1 | 1 | 1 | 1 | 2 | 2 | 2 | 13 |
| observability | 752 | 3 | 1 | 1 | 2 | 2 | 3 | 3 | 2 | 1 | 1 | 2 | 1 | 0 | 12 |
| plan | 1027 | 4 | 2 | 2 | 3 | 3 | 2 | 4 | 1 | 1 | 1 | 2 | 2 | 2 | 13 |
| testing | 756 | 1 | 1 | 2 | 2 | 2 | 1 | 2 | 2 | 1 | 1 | 2 | 1 | 1 | 13 |
| os-donanim-tespiti | 1155 | 6 | 3 | 1 | 3 | 2 | 2 | 1 | 0 | 1 | 1 | 2 | 2 | 2 | 12 |
| **TOPLAM (blok)** | 14.771 | 46 | 22 | 28 | 30 | 31 | 27 | 27 | 21 | 16 | 18 | 32 | 23 | 16 | — |

> **18/18 dosya ≥7 dil** · **10/18 dosya 13/13 tam** (turu · core · api · runtime ·
> security · plan · testing · linux · macos · reactos) · kalan 8 dosyanın boşlukları
> N/A kayıtlı (webos 7 · kernel/android/tizen/ios 8–9 · windows/obs/donanim 12).

**`-ornekler.md` dosyaları (51 adet):** ağırlıklı **PHP repo-kanıtı** taşır (kod-seti
dışıdır; ör. `security-ornekler` PHP=30, `plan-ornekler` PHP=34). Kapsama kararının
hedefi **kod-seti dosyalarıdır**, ornekler değiştirilmez.

## §5 Boşluk listesi → ekleme işi (karar ② girdisi · **TAMAMLANDI 2026-10-15 — P1–P7**)

> ✅ Bu tablo ekleme öncesi boşluk listesidir; tüm maddeler P1–P7 turlarında işlendi
> (Zig 16 · Swift 18 · J/K 32 blok — §4). Ekleme sözleşmesi maddeleri uygulanmıştır.

| Dil | 10 ana kod-setide durum | Ekleme mantığı |
|---|---|---|
| **Zig** | **0/10 dosya · 0 blok** | Her dosyaya 1–2 blok (systems-kavram gösterimi; `std.fs` ↔ `open`, `std.posix`); Zig 0.14+ resmi doküman URL zorunlu |
| **Swift** | **0/10 dosya** | Yalnız macOS/iOS ilgili gruplara mantıksal blok (`os_log`, `Dispatch`, `mach_absolute_time` karşılığı); ilgisiz dosyaya **zorla eklenmez** — hücre "N/A (kapsam dışı)" notu |
| **Java/Kotlin** | **0/10 dosya** (Kotlin yalnız `turu/android`) | JVM yüzeyi: `java.nio`, `java.util.concurrent`, `ProcessHandle` / Kotlin `kotlin.io`, `coroutines`; yine yalnız anlamlı gruplara |
| Mevcut 13 dil | 4–10/10 dosya arası | `kernel` 4/13 · `runtime` 5/13 — bu iki dosyada **mevcut set eksikleri** de (Py/Go/PHP/JS vb.) tespit edildi; ekleme kararına dahil mi? → açık soru O1 |

**Ekleme sözleşmesi (mevcut plan §4 ile aynı):** her blok `[ORİJİNAL ÖRNEK · derlenmedi]`
+ HTTP 200 doğrulanmış resmi URL · yer tutucu yasak · sessiz fallback yasak ·
`git commit` orkestratöre ait · 0 silme.

## §6 Açık sorular

| # | Soru | Sahip |
|---|---|---|
| ~~O1~~ | ~~`kernel`/`runtime` dosyalarındaki mevcut dil eksikleri ekleme kapsamına giriyor mu?~~ **KAPANDI (kullanıcı onayı 2026-10-14):** kapsam = tüm boşluklar (10 dil seti içindeki eksikler dahil) | Vault Steward ✅ |
| ~~O2~~ | ~~Ekleme kapsamı: 10 ana kod-seti mi, 18 mi?~~ **KAPANDI (kullanıcı onayı 2026-10-14):** 18 dosyanın tamamı (turu + 9 ana + 8 platform) | Vault Steward ✅ |
| ~~O3~~ | ~~Dil×OS matrisi / frontmatter `validation` / parent `index.md` envanter sayıları ekleme sonrası yeniden sayılacak~~ **KAPANDI (2026-10-15, P7):** 18/18 FM `validation` diskten tazelendi · `index.md` §4 + katalog + FM validation tazelendi (345 md / 150.210 satır) · §2/§4 bu dosyada taze · `log.md` append + batch raporu yapıldı | orchestrator ✅ |

## §7 İlişkili dosyalar

- Kapsam dizini: [`../architecture/K000-isletim-sistemi/index.md`](../architecture/K000-isletim-sistemi/index.md)
- Üretim planı (önceki): `C:\Users\MARHAN\.opencode\plan\K000-kod-seti-expansion-plan.md`
- Kod seti örneği: [`../architecture/K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md`](../architecture/K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
