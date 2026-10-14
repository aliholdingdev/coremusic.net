---
node_id: K000-isletim-sistemi/isletim-sistemi-cross
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: [platform-yuzeyleri, derleme-ve-paketleme, tasinabilirlik-siniri]
depends_on: []
evidence: ["https://pubs.opengroup.org/onlinepubs/9799919799/", "https://www.gnu.org/software/libc/manual/html_node/System-Calls.html", ".ai/architecture/K000-isletim-sistemi/os-master.md §5"]
validation: "2026-10-14 · ornekler §22-§28 OS gruplaması (Grup L/W/M + OS×yüzey matrisi) · dogrulama §H kaydı · 7 yeni URL HEAD 200 + 6 regresyon + 3 elenen · repo satır yeniden okuma 10/10 · kök 4 dosya + 3 alt-dal 12 dosya disk kanıtlı"
open_questions: ["Hedef platform seti bilinmiyor → ⚠️ VERIFICATION REQUIRED (Q4)", "Cross-platform kanıtı yalnız test/CI + web sunucusu düzeyinde; üretim OS doğrulanmadı"]
group: mimari
---

# isletim-sistemi-cross — Kademe-2 Kayıt

**ELI15:** İşletim sistemleri birbirine **benzemez** — yollar, ayracılar, API'ler farklıdır. Bu dal, CoreMusic'in o farkları **nasıl yuttuğunu** (veya yutmadığını) tarif eder: "aynı kod her yerde çalışsın" problemi.

| Öğe | Değer |
|---|---|
| parent_id | K000-isletim-sistemi |
| Kademe | 3 · subdomain |
| Master referansı | `os-master.md` §5 (platform bağımlılığı) |
| Alt kademe | `children: 3 alt-dal (bu turda AÇILDI — disk kanıtlı)` |
| Durum | `draft` (Q4 açık — hedef platform seti) |

## §1 Platform bağımlılığının 4 yüzeyi

| # | Yüzey | Fark | CoreMusic'te çözüm | Kanıt | Alt-dal |
|---|---|---|---|---|---|
| 1 | Yol ayracı | `/` vs `\` | `DIRECTORY_SEPARATOR` | `TemplateEngine.php:73` | `platform-yuzeyleri` |
| 2 | Platform adı | Linux/Windows/Darwin | `php_uname('s')` | `DesktopBff.php:35` | `platform-yuzeyleri` |
| 3 | Web sunucusu | Apache vs IIS | `.htaccess` **+** `web.config` | 4 alt-alanda ikisi de diskte | `platform-yuzeyleri` |
| 4 | Çalışma ortağı | Linux vs Windows | CI `ubuntu-latest` | `ci.yml:27` | `tasinabilirlik-siniri` |

> **Dağılım notu (bu tur):** Yüzey 1-3 tek alt-dala toplandı → `platform-yuzeyleri/`. Yüzey 4 → `tasinabilirlik-siniri/`. Derleme-paketleme yüzeyi kökte tablolaştırılmamıştı (boşluk) → `derleme-ve-paketleme/` açıldı.

## §2 Bilinen kanıt + bilinmeyen

| # | İddia | Durum |
|---|---|---|
| 1 | POSIX taşınabilirlik standardı var | ✅ POSIX.1-2024 (HTTP 200) |
| 2 | CoreMusic testleri Linux üzerinde | ✅ `.github/workflows/ci.yml:27` |
| 3 | CoreMusic Apache **ve** IIS yapılandırması taşıyor | ✅ 4 alt-alanda `.htaccess` + `web.config` |
| 4 | CoreMusic **üretimde** hangi OS'ta? | ⚠️ **UNKNOWN** (Q4) |
| 5 | macOS üzerinde çalışma kanıtı | ⚠️ **UNKNOWN** |
| 6 | Windows üzerinde çalışma kanıtı | ⚠️ **UNKNOWN** |

> **Dürüst sınır:** `home.coremusic.net/AGENTS.md` §1 "Raspberry Pi 5 üzerinde çalışan ana medya paneli" der — bu **Linux/ARM** hedefine işaret eder; ancak tek başına **üretim platformu teyidi değildir**. Q4 kapalı değildir.

## §3 Sınır komşuları

| Dışında olan | Nereye gider |
|---|---|
| OS ailesinin **karşılaştırılması** (ne farklı) | `isletim-sistemi-turu` |
| Platform API'sinin kendisi | `isletim-sistemi-api` |
| OS güvenlik mekanizmaları | `isletim-sistemi-security` |
| CI/CD'nin kendisi | K002-altyapi · `devops` |
| Ses platform API'leri | `isletim-sistemi-audio` |

## §4 Doğrulanmış kaynaklar (2026-10-12 · 4 URL, hepsi ✅ HTTP 200)

POSIX.1-2024 → `https://pubs.opengroup.org/onlinepubs/9799919799/` · glibc System-Calls → `https://www.gnu.org/software/libc/manual/html_node/System-Calls.html` · Linux syscall → `https://man7.org/linux/man-pages/man2/syscalls.2.html` · Windows bellek yüzeyi → `https://learn.microsoft.com/en-us/windows/win32/memory/memory-management`

## §5 Repo'daki platform bağımsızlık kanıtları

| # | Kanıt | Dosya |
|---|---|---|
| P1 | `runs-on: ubuntu-latest` + `ini-values: apcu.enable_cli=1` | `ci.yml:27` · `ci.yml:38` |
| P2 | Apache yapılandırması (×4 alt-alan) | `home.coremusic.net/.htaccess` |
| P3 | IIS yapılandırması (×4 alt-alan) | `home.coremusic.net/web.config` |
| P4 | `DIRECTORY_SEPARATOR` kullanımı | `TemplateEngine.php:73` · `MusicStreamHandler.php:199` |
| P5 | `php_uname('s')` runtime tespiti | `DesktopBff.php:35` |
| P6 | Yerel hedef: Raspberry Pi 5 (Linux/ARM) | `home.coremusic.net/AGENTS.md` §1 |
| P7 | Cache anahtarı `${{ runner.os }}` (platforma bağlı cache) | `ci.yml:44` · `ci.yml:96` |

## §6 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-X1 | Hedef platform seti tek noktada tanımlı mı? (Q4) | ⚠️ VERIFICATION REQUIRED |
| Q-X2 | `cross` ↔ `turu` sınırı — bağımsızlık vs karşılaştırma farkı onaylandı mı? | ⚠️ VERIFICATION REQUIRED |
| Q-X3 | macOS/Windows üzerinde çalıştırma testi var mı? | UNKNOWN |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**. İlişkili: kök `*-kapsam.md` · `*-dogrulama.md` · `*-ornekler.md` · alt-dal 3 `index.md` · `../os-master.md` §5 · `../os-bagimliliklar.md` §3

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `isletim-sistemi-cross-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kod | `isletim-sistemi-cross-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `isletim-sistemi-cross-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
| mimari | `platform-yuzeyleri/index.md` | Alt-dal kataloğu: 4 platform yüzeyi | Kademe-3 |
| mimari | `derleme-ve-paketleme/index.md` | Alt-dal kataloğu: derleme + dağıtım | Kademe-3 |
| mimari | `tasinabilirlik-siniri/index.md` | Alt-dal kataloğu: taşınabilirlik + CI matrisi | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
