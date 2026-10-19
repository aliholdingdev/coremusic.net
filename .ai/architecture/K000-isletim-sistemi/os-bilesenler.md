---
node_id: K000-isletim-sistemi/os-bilesenler.md
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on:
  - K001-donanım
  - K003-audio
  - K005-firmware
evidence:
  - "index.md §4 — 13 kademe-2 düğüm envanteri (353 md / 152.346 satır, 2026-10-15 sayımı) — okundu 2026-10-18"
  - "index.md §1 — alt alan → OS hizmeti → repo karşılığı tablosu — okundu 2026-10-18"
  - "os-master.md §C.1–§C.8 — 8 çekirdek kavram + repo dosya satır kanıtları — okundu 2026-10-18"
  - "index.md §GRUPLAR — kök/kademe-2 grup matrisi — okundu 2026-10-18"
validation:
  - "2026-10-18 — §2 bileşen satırları index §1 + §4 ile birebir (13 düğüm)"
  - "2026-10-18 — §3 repo satır kanıtları os-master §C'den alınmıştır (uydurma yok)"
  - "2026-10-18 — dalga/kanıt etiketi her satırda mevcut"
open_questions:
  - "`isletim-sistemi-plan` bileşen sınırı Q3 nedeniyle belirsiz (⚠️ VERIFICATION REQUIRED)"
  - "`electroncis` alt dalı ile K002-electroncis kökü arasındaki sahiplik paylaşımı yazılı değil (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# os-bilesenler — K000 Bileşen Ağacı

> Dayanak: `index.md` §1/§4/§GRUPLAR · `os-master.md` §C. Bu dosya K000'in
> bileşenlerini, sahipliklerini ve repo'daki somut karşılıklarını tek yerde
> toplar. Yeni bileşen ekleme yetkisi Vault Steward'a aittir.

## §1 ELI15

> **Bir orkestradaki çalgılar gibi: her bileşen OS'in tek bir işini yapar,
> birlikte çalışınca sistem çalar.**

Bileşen listesi şunları söyler: hangi parça var (bileşen), o parça hangi işin
sahibi (sorumluluk), repo'da karşılığı ne (kanıt), kimden bağımlı (bağ). Bileşen
**adı** diskteki dizin adıdır — tahmin edilmez, yeniden adlandırılmaz.

## §2 Kök bileşenler (4 dosya — dokunulmaz)

| # | Bileşen | Görev | Grup | Etiket |
|---|---|---|---|---|
| R1 | `index.md` | Klasör künyesi, envanter, çocuk kataloğu, §BAĞLANTILAR | mimari | [KANIT] |
| R2 | `os-master.md` | Ana anayasa: ELI15, 8 kavram, katman haritası | mimari | [KANIT] |
| R3 | `os-bagimliliklar.md` | 12×12 bağımlılık matrisi + döngü denetimi | mimari | [KANIT] |
| R4 | `os-dogrulama.md` | İddia · URL · tarih doğrulama matrisi | kanit | [KANIT] |

> Bu üretim `index.md` hariç dört kök dosyadan **bağımsızdır** — `index.md`
> yazımı başka agent'ın tekelindedir (brief yasakları).

## §3 Kademe-2 bileşenler (13 düğüm) + repo karşılığı

| # | Bileşen (dizin) | Dosya | Sahip olduğu OS hizmeti | Repo karşılığı (kanıt) | Etiket |
|---|---|---|---|---|---|
| B1 | `electroncis` | 12 | OS↔donanım yazılım yüzeyi | ⚠️ Q1 KAPANDI — iç kanıt `K000-isletim-sistemi/electroncis/**` | [TASARIM] |
| B2 | `isletim-sistemi-api` | 20 | kullanıcı-kernel sınırı, libc, FFI | `shell_exec` · `proc_open` (os-master §C.1) | [KANIT] |
| B3 | `isletim-sistemi-audio` | 42 | WASAPI · ALSA · Core Audio erişimi | ffmpeg + `shell_exec` (`shared/src/AI/AIEngine.php:288-310`) | [KANIT] |
| B4 | `isletim-sistemi-core` | 21 | bellek · süreç · dosya kavramları | `sys_get_temp_dir()` (`shared/src/Component/TemplateEngine.php:73`) | [KANIT] |
| B5 | `isletim-sistemi-cross` | 18 | platform bağımsızlık (PATH, sep) | `DIRECTORY_SEPARATOR` (index §1) | [KANIT] |
| B6 | `isletim-sistemi-kernel` | 17 | süreç · syscall · zaman | `proc_open` test (`shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269`) | [KANIT] |
| B7 | `isletim-sistemi-observability` | 17 | ftrace · ETW · perf · log hattı | `error_log()` → `shared/src/PageRouter/StructuredLogger.php:72` | [KANIT] |
| B8 | `isletim-sistemi-plan` | 17 | scheduling/timer — **kapsam belirsiz** | — (Q3 ⚠️) | [BİLİNMEYEN] |
| B9 | `isletim-sistemi-runtime` | 17 | PHP SAPI + web sunucusu | `home.coremusic.net/.htaccess` + `web.config` | [KANIT] |
| B10 | `isletim-sistemi-security` | 56 | izin · capability · sandbox | `realpath()` sınır denetimi (`MusicStreamHandler.php:185-208`) | [KANIT] |
| B11 | `isletim-sistemi-testing` | 23 | kselftest · KUnit · CI platformu | `shared/phpunit.xml` · CI `apcu.enable_cli=1` | [KANIT] |
| B12 | `isletim-sistemi-turu` | 88 | 8 platform karşılaştırması | `.github/workflows/ci.yml:1` → `ubuntu-latest` | [KANIT] |
| B13 | `os-donanim-tespiti` | 5 | PnP · enumerasyon · sürücü bağlama | `shared/src/Device/DeviceManager.php:24` (K001 anayasa evidence'i) | [KANIT] |

**Toplam:** 13 bileşen · dosya sayıları `index.md §4` (2026-10-15 sayımı).

## §4 Bileşen ↔ kademe-3 dal ilişkileri

| # | Üst bileşen | Kademe-3 dalları | Not | Etiket |
|---|---|---|---|---|
| D1 | isletim-sistemi-turu | windows · linux · macos · android · ios · reactos · tizen · webos | 8 platform (index §4 satır 12) | [KANIT] |
| D2 | isletim-sistemi-audio | `wasapi/` · (ALSA/CoreAudio karşılıkları `isletim-sistemi-audio/index.md` §2.5) | Neva Engine bağlantılı | [KANIT] |
| D3 | isletim-sistemi-security | 40 alt-dal (2026-10-14 kod-seti turu) + 8 `*-guvenlik.md` (2026-10-15) | K009 sınır köprüsü | [KANIT] |
| D4 | os-donanim-tespiti | tek kademe (5 md) | en küçük düğüm | [KANIT] |
| D5 | isletim-sistemi-plan | kapsam belirsiz (Q3) | ⚠️ | [BİLİNMEYEN] |

## §5 Kod-seti bileşenleri (18 adet — 13-dil turu 2026-10-15)

| # | Bileşen | Dosya | Satır | Fence | Etiket |
|---|---|---|---|---|---|
| K1 | core | `isletim-sistemi-core/isletim-sistemi-core-kod-seti.md` | 1088 | 27 | [KANIT] |
| K2 | kernel | `isletim-sistemi-kernel/isletim-sistemi-kernel-kod-seti.md` | 894 | 17 | [KANIT] |
| K3 | api | `isletim-sistemi-api/isletim-sistemi-api-kod-seti.md` | 846 | 20 | [KANIT] |
| K4 | runtime | `isletim-sistemi-runtime/isletim-sistemi-runtime-kod-seti.md` | 724 | 20 | [KANIT] |
| K5 | security | `isletim-sistemi-security/isletim-sistemi-security-kod-seti.md` | 804 | 20 | [KANIT] |
| K6 | observability | `isletim-sistemi-observability/isletim-sistemi-observability-kod-seti.md` | 752 | 22 | [KANIT] |
| K7 | plan | `isletim-sistemi-plan/isletim-sistemi-plan-kod-seti.md` | 1027 | 29 | [KANIT] |
| K8 | testing | `isletim-sistemi-testing/isletim-sistemi-testing-kod-seti.md` | 756 | 19 | [KANIT] |
| K9 | donanim-tespiti | `os-donanim-tespiti/os-donanim-tespiti-kod-seti.md` | 1155 | 26 | [KANIT] |
| K10 | turu (ana) | `isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md` | 1038 | 40 | [KANIT] |

> Kademe-3 8 platform kod-seti (615–847 satır arası) bu tabloya **dahil değildir**
> — sayım `index.md §GRUPLAR`'daki 18 satırıyla sınırlıdır (index validation,
> 2026-10-15: "18 kod-seti · fence toplamı 355").

## §6 Bileşen bağımlılık yönleri (kim kime güvenir)

| # | Bileşen | Yukarıya (üst K) | Aşağıya (alt bileşen) | Etiket |
|---|---|---|---|---|
| A1 | isletim-sistemi-runtime | K011-application (SAPI'yi PHP kullanır) | isletim-sistemi-kernel | [TASARIM] |
| A2 | isletim-sistemi-audio | K003-audio (cihaz erişimi) | isletim-sistemi-kernel | [TASARIM] |
| A3 | isletim-sistemi-security | K009-security (OS yetkisi uygulama yetkisini besler) | isletim-sistemi-core | [TASARIM] |
| A4 | os-donanim-tespiti | K001-donanım (envanter) | isletim-sistemi-kernel | [TASARIM] |
| A5 | electroncis (alt dal) | K002-electroncis | os-donanim-tespiti | [TASARIM] ⚠️ Q1 |

> Ters yön (alt bileşenin üst K'yi **import** etmesi) katman ihlalidir; bkz.
> `os-bagimliliklar.md` döngü denetimi.

## §7 Bileşen şema özetleri (dosya türü dağılımı)

| # | Türe | Sayı | Grup dağılımı | Etiket |
|---|---|---|---|---|
| T1 | Kök MD | 4 (index + master + bagimliliklar + dogrulama) | mimari 3 · kanit 1 | [KANIT] |
| T2 | Bu üretim (Stage-2) | +8 (`os-kapsam` … `os-evrensel-dil`) | mimari 5 · kod 1 · kanit 2 | [TASARIM] |
| T3 | Kademe-2 MD (toplam) | 349 (353 − kök 4) | 13 düğüm | [KANIT] |
| T4 | Toplam MD | 353 (2026-10-15) | 152.346 satır | [KANIT] |

## §8 Sahiplik matrisi (kim neyi tek yazar)

| # | Yüzey | Tek yazıcı | Okuyucu | İhlal sonucu |
|---|---|---|---|---|
| O1 | `index.md` | Stage-1 subagent'ı (bu üretim YAZMAZ) | tümü | brief yasağı |
| O2 | `os-master.md` | Vault Steward | tümü | çelişkide master kazanır |
| O3 | Kod-seti dosyaları | ilgili kademe-2 sahibi | tümü | — |
| O4 | URL doğrulama | `os-dogrulama.md` | tümü | doğrulanmayan URL yazılmaz |
| O5 | Bağımlılık satırı | `os-bagimliliklar.md` | tümü | tek yazıcı kuralı |

## §9 Doğrulama kriterleri

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| C1 | §3 satır sayısı = 13 (index §4) | sayım | ✅ 2026-10-18 |
| C2 | Her bileşenin repo kanıtı var mı | os-master §C çaprazı | ⚠️ B1 (Q1), B8 (Q3) hariç |
| C3 | Kod-seti sayısı 10 (kök/turu) + 8 platform = 18 | index §GRUPLAR | ✅ 2026-10-18 |
| C4 | Uydurma dosya adı yok (diskte var olan adlar) | list_directory 2026-10-18 | ✅ |

## §10 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | `electroncis` alt dalının sahibi K000 mi K002 mi? | `⚠️ VERIFICATION REQUIRED` |
| Q3 | `isletim-sistemi-plan` bileşeni tam olarak neyi kapsar? | `⚠️ VERIFICATION REQUIRED` |
| Q4 | Hedef platform seti (Windows/Linux/macOS) tek bileşende tanımlı mı? | `⚠️ VERIFICATION REQUIRED` |

## §11 Yeni bileşen ekleme protokolü

| Adım | Aksiyon | Zorunlu kayıt |
|---|---|---|
| 1 | Dizin diskte var mı? (list_directory) | var/yok |
| 2 | Hangi kademe? (2/3) | kademe |
| 3 | Grup ataması (mimari/kod/kanit) | §GRUPLAR satırı |
| 4 | Repo kanıtı var mı? | dosya:satır veya `UNKNOWN` |
| 5 | Bu dosya §3'e satır olarak eklenir | yeni satır no |

## §12 Anti-pattern

| # | Hatalı | Doğru | Dayanak |
|---|---|---|---|
| X1 | Bileşen adını "iyileştirme" | Ad immutable (KURAL 1) | brief yasakları |
| X2 | Kanıtsız repo satırı yazmak | `UNKNOWN` / `⚠️` | zero-hallucination |
| X3 | `index.md`'yi düzenlemek | YASAK — başka agent'ın tekelinde | brief yasakları |
| X4 | 13 sayısını elle değiştirmek | index §4 sayımı esastır | 2026-10-15 ölçümü |
| X5 | Kademe-3'ü kademe-2 saymak | §4/§5 ayrı tutulur | §7 notu |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18
