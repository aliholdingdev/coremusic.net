---
node_id: K000-isletim-sistemi/index
node_type: domain
status: draft
parent_id: K000-isletim-sistemi
children: [electroncis, isletim-sistemi-api, isletim-sistemi-audio, isletim-sistemi-core, isletim-sistemi-cross, isletim-sistemi-kernel, isletim-sistemi-observability, isletim-sistemi-plan, isletim-sistemi-runtime, isletim-sistemi-security, isletim-sistemi-testing, isletim-sistemi-turu, os-donanim-tespiti]
depends_on: [K002-altyapi]
evidence: [https://docs.kernel.org/core-api/index.html, https://man7.org/linux/man-pages/man2/syscalls.2.html, https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis, https://pubs.opengroup.org/onlinepubs/9799919799/]
validation: "2026-10-13 · 12 düğüm disk enumerasyonu ile sayıldı (124 md / 39.099 satır) · kök 4 dosya derinleştirildi · URL'ler HTTP HEAD ile doğrulandı"
open_questions: ["Q1 electroncis kapsamı", "Q2 core↔kernel sınırı", "Q3 plan kapsamı", "Q4 hedef platform seti", "Q5 turu↔altyapı gap"]
group: mimari
---

# K000 — İşletim Sistemi (Operating System) Domain

> **Tek cümle:** CoreMusic'in PHP uygulaması, disk dosyaları, süreçleri ve ses donanımını **bir işletim sistemi üzerine kurar** — bu domain o yüzeyin (syscall · kernel · sürücü · zaman paylaşımı · dosya sistemi · izin · ses yığını) mimari tarifidir.

## 1. Ne işe yarar

Bu domain **kod üretmez**, kodun **hangi OS hizmetlerine güvendiğini** sabitler:

| Alt alan | OS'un CoreMusic'e verdiği şey | Repo'da somut karşılığı |
|---|---|---|
| `isletim-sistemi-kernel` | süreç, syscall, dosya açma, izin | `shell_exec` · `proc_open` · `realpath` |
| `isletim-sistemi-api` | kullanıcı-kernel sınırı, libc | `php_uname('s')` → platform kararı |
| `isletim-sistemi-audio` | ses aygıtına erişim (WASAPI/ALSA/CoreAudio) | web'de WAV üretimi (ffmpeg) |
| `isletim-sistemi-core` | çekirdek kavramları: bellek, süreç, dosya | `sys_get_temp_dir()` · `ini_get` |
| `isletim-sistemi-runtime` | yığın: PHP SAPI + Apache/nginx | `home.coremusic.net/.htaccess` + `web.config` |
| `isletim-sistemi-turu` | OS ailesi karşılaştırması (Windows/Linux/macOS) | `.github/workflows/ci.yml:1` → `ubuntu-latest` |
| `isletim-sistemi-security` | izin, capability, sandbox, kural uygulayıcı | `.htaccess` güvenlik başlıkları · `realpath` denetimi |
| `isletim-sistemi-observability` | ftrace/ETW/perf, log yönlendirme | `error_log()` → `StructuredLogger.php:72` |
| `isletim-sistemi-testing` | kselftest/KUnit, süreç izolasyonu | `shared/phpunit.xml` · CI `apcu.enable_cli=1` |
| `isletim-sistemi-cross` | platform bağımsızlık: PATH sep, çekirdek/repl | `DIRECTORY_SEPARATOR` |
| `isletim-sistemi-plan` | [BİLİNMEYEN — Q3] scheduling mi, doküman mı | — |
| `electroncis` | [BİLİNMEYEN — Q1] donanım mı, devre mi | — |

## 2. Bu domainin sınırları

**İçeride:** OS'nin CoreMusic'in üstüne kurulduğu **sözleşme** — neyi hangi syscall/SAPI ile
çağırdığı, platform bağımlılığının nerede başladığı, ses donanımına nasıl erişildiği.

**Dışarıda:** (a) PHP uygulamasının iş mantığı → `K001`; (b) sunucu/altyapı kurulumu → `K002-altyapi`; (c) `electroncis` altındaki donanım devresi ayrı bir disiplin (Q1).

## 3. Kanıt durumu

- **[KANIT]** → diskte gerçek dosya/satır **veya** 2026-10-13'te HTTP 200 ile açılmış resmi URL.
- **[BİLİNMEYEN]** → veri yok, uydurulmadı (Q1–Q5 açık).
- **[TASARIM]** → CoreMusic'in kendi tercihi, OS zorunluluğu değil.

## 4. Düğüm envanteri (13 kademe-2 düğüm · disk ölçüm 2026-10-13 · kök 4 dosya hariç — onlar `index.md` 100 · `os-master/bagimliliklar/dogrulama` ≥500)

| # | Dizin | Dosya | Satır | ≥500 | <500 | Durum | Grup |
|---|---|---|---|---|---|---|---|
| 1 | `electroncis` | 4 | 428 | 0 | 4 | derinleştirme gerekli | mimari |
| 2 | `isletim-sistemi-api` | 6 | 1478 | 2 | 4 | kısmi hazır | mimari |
| 3 | `isletim-sistemi-audio` | 17 | 6187 | 7 | 10 | derin + geniş | kod |
| 4 | `isletim-sistemi-core` | 4 | 432 | 0 | 4 | derinleştirme gerekli | mimari |
| 5 | `isletim-sistemi-cross` | 4 | 423 | 0 | 4 | derinleştirme gerekli | mimari |
| 6 | `isletim-sistemi-kernel` | 4 | 384 | 0 | 4 | derinleştirme gerekli | mimari |
| 7 | `isletim-sistemi-observability` | 4 | 191 | 0 | 4 | derinleştirme gerekli | kanit |
| 8 | `isletim-sistemi-plan` | 4 | 206 | 0 | 4 | derinleştirme gerekli (Q3) | mimari |
| 9 | `isletim-sistemi-runtime` | 4 | 217 | 0 | 4 | derinleştirme gerekli | mimari |
| 10 | `isletim-sistemi-security` | 4 | 206 | 0 | 4 | derinleştirme gerekli | kanit |
| 11 | `isletim-sistemi-testing` | 4 | 202 | 0 | 4 | derinleştirme gerekli | kod |
| 12 | `isletim-sistemi-turu` | 65 | 28745 | 54 | 11 | en derin düğüm (8 platform) | mimari |
| 13 | `os-donanim-tespiti` | 4 | 1649 | 3 | 1 | 2026-10-13 açıldı (Vault Steward onayı) — PnP/enumeration/sürücü bağlama | mimari |
| — | **TOPLAM 13 düğüm** | **128** | **40.748** | **66** | **62** | kök 4 dosya hariç | — |

## 5. Okuma sırası (öneri)
`os-master.md` (ELI15 + 8 kavram + 12 düğüm) → `os-bagimliliklar.md` (12×12 matris + döngü) → `os-dogrulama.md` (URL + disk kanıtı) → ilgili child `index.md` → `-kapsam` → `-ornekler` → `-dogrulama`.

## 6. Doğrulama bağlantıları
Kaynak kanıtları: [`os-dogrulama.md`](os-dogrulama.md) · Bağımlılık: [`os-bagimliliklar.md`](os-bagimliliklar.md) · Ana anlatım: [`os-master.md`](os-master.md) · Registry: [`../index.md`](../index.md)

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.
| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi, envanter, çocuk kataloğu | Kademe-1 |
| mimari | `os-master.md` | Ana anayasa: ELI15, 8 kavram, katman haritası | Kademe-1 |
| mimari | `os-bagimliliklar.md` | 12×12 bağımlılık matrisi + döngü denetimi | Kademe-1 |
| kanit | `os-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `electroncis/index.md` | Donanım/elektronik devre kök künyesi (Q1) | Kademe-2 |
| mimari | `isletim-sistemi-api/index.md` | Kullanıcı-kernel sınırı, libc/API kökü | Kademe-2 |
| kod | `isletim-sistemi-audio/index.md` | Ses yığını kökü (WASAPI/ALSA/CoreAudio) | Kademe-2 |
| mimari | `isletim-sistemi-core/index.md` | Çekirdek kavramlar kökü (bellek/süreç/dosya) | Kademe-2 |
| mimari | `isletim-sistemi-cross/index.md` | Platform bağımsızlık kökü | Kademe-2 |
| mimari | `isletim-sistemi-kernel/index.md` | Kernel/süreç/syscall kökü | Kademe-2 |
| kanit | `isletim-sistemi-observability/index.md` | ftrace/ETW/perf kökü | Kademe-2 |
| mimari | `isletim-sistemi-plan/index.md` | Scheduling/plan kökü (Q3) | Kademe-2 |
| mimari | `isletim-sistemi-runtime/index.md` | PHP SAPI + web sunucusu kökü | Kademe-2 |
| kanit | `isletim-sistemi-security/index.md` | İzin/capability/sandbox kökü | Kademe-2 |
| kod | `isletim-sistemi-testing/index.md` | OS düzeyi test kökü (kselftest/KUnit) | Kademe-2 |
| mimari | `isletim-sistemi-turu/index.md` | OS ailesi karşılaştırma kökü (8 platform) | Kademe-2 |
| mimari | `os-donanim-tespiti/index.md` | Donanım tespiti kökü — PnP/enumeration/sürücü bağlama (2026-10-13, Vault Steward onayı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
