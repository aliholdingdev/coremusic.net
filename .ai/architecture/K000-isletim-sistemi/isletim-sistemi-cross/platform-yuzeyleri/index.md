---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross
children: [platform-yuzeyleri-anayasa, platform-yuzeyleri-surucu, platform-yuzeyleri-ornekler]
depends_on: []
evidence:
  - "shared/src/Component/TemplateEngine.php:73"
  - "shared/src/Api/Bff/DesktopBff.php:35"
  - "home.coremusic.net/.htaccess:27"
  - "home.coremusic.net/web.config:14"
validation: "2026-10-13 · 3 içerik dosyası ≥500 ham satır · 3 URL HEAD 200 · envanter kesişimi 0 · repo satırları Select-Object -Index ile okundu"
open_questions:
  - "Q4 — hedef platform seti tek noktada tanımlı değil → UNKNOWN"
  - "Q-X3 — windows-latest / macos-latest runner kanıtı yok → UNKNOWN"
group: mimari
---

# platform-yuzeyleri — Kademe-3 Alt-dal Kataloğu

**ELI15:** Bir program OS'e "dosya aç" der ama her OS bunu **farklı isimle**, **farklı
bayrakla** ve **farklı hatayla** karşılar. Bu alt-dal, Win32 ↔ POSIX ↔ Apple/GLib
yüzeyinin farklarını, ABI kurallarını (sonluluk · struct packing) ve cihaz erişim
katmanını tek yerde toplar.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-cross` |
| Kademe | 3 · subdomain |
| İçerik dosyası | 3 (anayasa · surucu · ornekler) — her biri ≥500 ham satır |
| Master referansı | `../isletim-sistemi-cross-kapsam.md` §B · `../../os-master.md` §5 |
| Durum | `draft` (Q4 açık — hedef platform seti) |

## §1 İçerik dosyaları

| # | Dosya | group | Ne taşır | Eşik |
|---|---|---|---|---|
| 1 | `platform-yuzeyleri-anayasa.md` | mimari | §0 ELI15 · §1 yüzey karşılaştırma matrisi · §2 ABI/sonluluk · §3 sistem çağrı farkları · §4 cihaz/API katmanı · §5 ölçüm · §6 repo kanıtı · §7 riskler · §8 kapsam dışı · §9 kaynakça | ≥500 |
| 2 | `platform-yuzeyleri-surucu.md` | mimari | Sürücü/cihaz erişim yüzeyi: servis yaşam döngüsü, IOCTL yüzeyi, PE başlığı, ABI paketleme kuralları, ölçüm ve repo kanıtı | ≥500 |
| 3 | `platform-yuzeyleri-ornekler.md` | kod | Gerçek repo örnekleri + POSIX fonksiyon yüzeyi örnekleri, hata yüzeyi, ölçüm, risk | ≥500 |

## §2 Komşu sınırı matrisi (cross → dışarı)

| # | Konu | Komşu dal | Sınır kuralı |
|---|---|---|---|
| 1 | OS API'sinin kendisi (tanım, imza, sürüm) | `isletim-sistemi-api` | Bu dal yalnız **fark** ve **karşılaştırma** üretir; API tanımı orada kalır |
| 2 | Çekirdek kavramları (süreç, bellek, dosya) | `isletim-sistemi-kernel` | İçerik: syscall/AB'nin anatomisi değil, **yüzey farkı** |
| 3 | Platform türü karşılaştırması (Windows/Linux/macOS/Android…) | `isletim-sistemi-turu` | Aile **içinde** derinlik orada; burada **aileler arası fark** |
| 4 | Test stratejisi ve portability matrix | `isletim-sistemi-testing` | Burada ölçüm tanımlanır, test koşumu orada |
| 5 | CI/CD pipeline, runner matrisi | `K17-devops` | `ci.yml` yalnız **kanıt** olarak okunur; pipeline tasarımı orada |
| 6 | DSP/donanım taşınabilirliği (örn. ARM64, ses kartı) | `K004-dsp` · `K001-donanım` | Donanım katmanı bu dalın **kapsam dışı**dır |

## §3 Web doğrulama (2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Microsoft sistem bilgisi API'si OS/ekipman bilgisini runtime'da verir | https://learn.microsoft.com/en-us/windows/win32/sysinfo/system-information | 2026-10-13 | ✅ HTTP 200 |
| GLib belgeleri GNOME tarafında taşınabilir platform yüzeyini tanımlar | https://gnome.pages.gitlab.gnome.org/glib/ | 2026-10-13 | ✅ HTTP 200 |
| POSIX `mmap()` bellek eşlemeyi ortak yüzey olarak tanımlar | https://pubs.opengroup.org/onlinepubs/9699919799/functions/mmap.html | 2026-10-13 | ✅ HTTP 200 |

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-X1 | Hedef platform seti (Windows/Linux/macOS/ARM) tek SSOT'ta tanımlı mı? | ⚠️ VERIFICATION REQUIRED |
| Q-X19 | Bu alt-dalın ölçüm eşikleri `isletim-sistemi-testing` ile senkron mu? | UNKNOWN |
| Q-X3 | macOS yüzeyi (CoreFoundation) üretimde kullanılıyor mu? | UNKNOWN |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
