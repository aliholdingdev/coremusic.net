---
node_id: K000-isletim-sistemi/index
node_type: domain
status: draft
parent_id: K000-isletim-sistemi
children: [electroncis, isletim-sistemi-api, isletim-sistemi-audio, isletim-sistemi-core, isletim-sistemi-cross, isletim-sistemi-kernel, isletim-sistemi-observability, isletim-sistemi-plan, isletim-sistemi-runtime, isletim-sistemi-security, isletim-sistemi-testing, isletim-sistemi-turu, os-donanim-tespiti]
depends_on: [K002-altyapi]
evidence: [https://docs.kernel.org/core-api/index.html, https://man7.org/linux/man-pages/man2/syscalls.2.html, https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis, https://pubs.opengroup.org/onlinepubs/9799919799/]
validation: "2026-10-13 · 12 düğüm disk enumerasyonu ile sayıldı (124 md / 39.099 satır) · kök 4 dosya derinleştirildi · URL'ler HTTP HEAD ile doğrulandı · 2026-10-14: `isletim-sistemi-turu/kod-seti` eklendi → turu satırı tazelendi (66 dosya / 31.258 satır) · 2026-10-15: 13-dil kapsama turu (P1–P7) sonrası 13 düğüm tam sayım → 345 md / 150.210 satır / 18 kod-seti (fence toplamı 355) — §4 ve §GRUPLAR katalog satırları diskten tazelendi; toplamlar satır toplamıdır, kök 4 dosya hariç. · 2026-10-15 (2): OS-guvenlik turu — `isletim-sistemi-turu/*/` altına 8× `*-guvenlik.md` (254–272 satır, K000↔K009 sınır köprüsü) + 8 index eki → **353 md / 152.346 satır** (§4 + §GRUPLAR tazelendi; `android/index.md` YOK bilinçli karar — parent L62/88/97)"
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

## 4. Düğüm envanteri (13 kademe-2 düğüm · disk ölçüm 2026-10-15 · kök 4 dosya hariç — onlar `index.md` 100+ · `os-master/bagimliliklar/dogrulama` ≥500)

| # | Dizin | Dosya | Satır | ≥500 | <500 | Durum | Grup |
|---|---|---|---|---|---|---|---|
| 1 | `electroncis` | 12 | 2968 | 0 | 12 | derinleştirme gerekli (0× ≥500) | mimari |
| 2 | `isletim-sistemi-api` | 20 | 10373 | 14 | 6 | kısmi hazır (14× ≥500) · 2026-10-14 kod-seti · 2026-10-15 13-dil turu (fence 20) | mimari |
| 3 | `isletim-sistemi-audio` | 42 | 16453 | 19 | 23 | derin + geniş (19× ≥500) | kod |
| 4 | `isletim-sistemi-core` | 21 | 11743 | 12 | 9 | kısmi hazır (12× ≥500) · 2026-10-14 kod-seti · 2026-10-15 13-dil turu (fence 27) | mimari |
| 5 | `isletim-sistemi-cross` | 18 | 8567 | 12 | 6 | kısmi hazır (12× ≥500) | mimari |
| 6 | `isletim-sistemi-kernel` | 17 | 8036 | 6 | 11 | kısmi hazır (6× ≥500) · 2026-10-14 kod-seti · 2026-10-15 13-dil turu (fence 17) | mimari |
| 7 | `isletim-sistemi-observability` | 17 | 7848 | 13 | 4 | kısmi hazır (13× ≥500) · 2026-10-14 kod-seti · 2026-10-15 13-dil turu (fence 22) | kanit |
| 8 | `isletim-sistemi-plan` | 17 | 8064 | 13 | 4 | kısmi hazır (13× ≥500, Q3) · 2026-10-14 kod-seti · 2026-10-15 13-dil turu (fence 29) | mimari |
| 9 | `isletim-sistemi-runtime` | 17 | 8304 | 13 | 4 | kısmi hazır (13× ≥500) · 2026-10-14 kod-seti · 2026-10-15 13-dil turu (fence 20) | mimari |
| 10 | `isletim-sistemi-security` | 56 | 16465 | 13 | 43 | derin + geniş (56 dosya) · 2026-10-14 40 alt-dal + kök kod-seti · 2026-10-15 13-dil turu (fence 20) | kanit |
| 11 | `isletim-sistemi-testing` | 23 | 10479 | 15 | 8 | kısmi hazır (15× ≥500) · 2026-10-14 kod-seti · 2026-10-15 13-dil turu (fence 19) | kod |
| 12 | `isletim-sistemi-turu` | 88 | 40240 | 66 | 22 | en derin düğüm (8 platform · 66× ≥500) · 2026-10-14 kod-seti + O6 fix · 2026-10-15 13-dil turu (turu 40 + 8 platform kod-seti) · 2026-10-15 OS-guvenlik turu (8× `*-guvenlik.md` + 8 index eki) | mimari |
| 13 | `os-donanim-tespiti` | 5 | 2806 | 4 | 1 | kısmi hazır (4× ≥500) · 2026-10-13 açıldı · 2026-10-14 kod-seti (Vault Steward onayı) · 2026-10-15 13-dil turu (fence 26) | mimari |
| — | **TOPLAM 13 düğüm** | **353** | **152.346** | **200** | **153** | tümü 2026-10-15 disk sayımı · kök 4 dosya hariç · 9+9 kod-seti (18 toplam) · 13 dil yüzeyi · 2026-10-15 OS-guvenlik +8 md | — |

## 5. Okuma sırası (öneri)
`os-master.md` (ELI15 + 8 kavram + 12 düğüm) → `os-bagimliliklar.md` (12×12 matris + döngü) → `os-dogrulama.md` (URL + disk kanıtı) → ilgili child `index.md` → `-kapsam` → `-ornekler` → `-dogrulama`.

## 6. Doğrulama bağlantıları
Kaynak kanıtları: [`os-dogrulama.md`](os-dogrulama.md) · Bağımlılık: [`os-bagimliliklar.md`](os-bagimliliklar.md) · Ana anlatım: [`os-master.md`](os-master.md) · Registry: [`../index.md`](../index.md)

**İlişkili vault projesi (2026-10-15):** **Neva Engine** — `.ai/projects/NevaEngine/` (11 md). Bu domainin `isletim-sistemi-audio` katmanını (ASIO/WASAPI/ALSA/CoreAudio) host-side C++20 ses motorunda kullanan planlama projesi; OS kod örnekleri `os/01-windows-asio` · `os/02-windows-wasapi` · `os/03-linux-alsa-pipewire` · `os/04-macos-coreaudio`. Detay kayıt: `isletim-sistemi-audio/index.md` §2.5.

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
| kod | `isletim-sistemi-core/isletim-sistemi-core-kod-seti.md` | Cross-platform bellek ölçümü / dosya meta / temp dizin kod seti (GRUP A/B/C · 1088 satır · fence 27) | Kademe-2 |
| kod | `isletim-sistemi-kernel/isletim-sistemi-kernel-kod-seti.md` | Cross-platform süreç/fork · mmap · ioctl kod seti (894 satır · fence 17) | Kademe-2 |
| kod | `isletim-sistemi-api/isletim-sistemi-api-kod-seti.md` | open/read köprüsü, hata kodu deseni, FFI sınırı (846 satır · fence 20) | Kademe-2 |
| kod | `isletim-sistemi-runtime/isletim-sistemi-runtime-kod-seti.md` | SAPI/runtime tespiti, sunucu yapılandırması, servis/daemon (724 satır · fence 20) | Kademe-2 |
| kod | `isletim-sistemi-security/isletim-sistemi-security-kod-seti.md` | İzin/sandbox/kaynak limiti kod seti (804 satır · fence 20) | Kademe-2 |
| kod | `isletim-sistemi-observability/isletim-sistemi-observability-kod-seti.md` | Log hattı / monotonic saat / ölçümler kod seti (752 satır · fence 22) | Kademe-2 |
| kod | `isletim-sistemi-plan/isletim-sistemi-plan-kod-seti.md` | Öncelik/timer/kuyruk kod seti (1027 satır · fence 29) | Kademe-2 |
| kod | `isletim-sistemi-testing/isletim-sistemi-testing-kod-seti.md` | Test izolasyonu / platform matrisi / CI gate kod seti (756 satır · fence 19) | Kademe-2 |
| kod | `os-donanim-tespiti/os-donanim-tespiti-kod-seti.md` | CPU/bellek/ses/arayüz enumerasyon kod seti (1155 satır · fence 26) | Kademe-2 |
| kod | `isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md` | OS ailesi karşılaştırma ana kod seti (1038 satır · fence 40) | Kademe-2 |
| kod | `isletim-sistemi-turu/linux/linux-kod-seti.md` | Linux platform kod seti (746 satır · fence 15) | Kademe-3 |
| kod | `isletim-sistemi-turu/windows/windows-kod-seti.md` | Windows platform kod seti (691 satır · fence 14) | Kademe-3 |
| kod | `isletim-sistemi-turu/macos/macos-kod-seti.md` | macOS platform kod seti (665 satır · fence 21) | Kademe-3 |
| kod | `isletim-sistemi-turu/android/android-kod-seti.md` | Android platform kod seti (615 satır · fence 16) | Kademe-3 |
| kod | `isletim-sistemi-turu/ios/ios-kod-seti.md` | iOS platform kod seti (591 satır · fence 15) | Kademe-3 |
| kod | `isletim-sistemi-turu/reactos/reactos-kod-seti.md` | ReactOS platform kod seti (847 satır · fence 15) | Kademe-3 |
| kod | `isletim-sistemi-turu/tizen/tizen-kod-seti.md` | Tizen platform kod seti (777 satır · fence 10) | Kademe-3 |
| kod | `isletim-sistemi-turu/webos/webos-kod-seti.md` | webOS platform kod seti (755 satır · fence 9) | Kademe-3 |
| mimari | `isletim-sistemi-turu/windows/windows-guvenlik.md` | OS güvenlik profili — K000↔K009 sınırı (269 satır · 6 URL · 2026-10-15) | Kademe-3 |
| mimari | `isletim-sistemi-turu/linux/linux-guvenlik.md` | OS güvenlik profili — K000↔K009 sınırı (272 satır · 7 URL · 2026-10-15) | Kademe-3 |
| mimari | `isletim-sistemi-turu/macos/macos-guvenlik.md` | OS güvenlik profili — K000↔K009 sınırı (272 satır · 6 URL · 2026-10-15) | Kademe-3 |
| mimari | `isletim-sistemi-turu/ios/ios-guvenlik.md` | OS güvenlik profili — K000↔K009 sınırı (259 satır · 6 URL · 2026-10-15) | Kademe-3 |
| mimari | `isletim-sistemi-turu/android/android-guvenlik.md` | OS güvenlik profili — K000↔K009 sınırı (264 satır · 7 URL · 2026-10-15) | Kademe-3 |
| mimari | `isletim-sistemi-turu/webos/webos-guvenlik.md` | OS güvenlik profili — K000↔K009 sınırı (260 satır · 7 URL · 2026-10-15) | Kademe-3 |
| mimari | `isletim-sistemi-turu/tizen/tizen-guvenlik.md` | OS güvenlik profili — K000↔K009 sınırı (254 satır · 6 URL · 2026-10-15) | Kademe-3 |
| mimari | `isletim-sistemi-turu/reactos/reactos-guvenlik.md` | OS güvenlik profili — K000↔K009 sınırı (257 satır · 6 URL · 2026-10-15) | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
