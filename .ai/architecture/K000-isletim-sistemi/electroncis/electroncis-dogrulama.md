---
node_id: K000-isletim-sistemi/electroncis/electroncis-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis
children: []
depends_on: [K002-electroncis, K001-donanım, K000-isletim-sistemi/os-donanim-tespiti]
evidence:
  - "C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\electroncis\electroncis-kapsam.md (komşu sınır tablosu)"
  - "C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\os-master.md (satır 7: K002-altyapi ID GAP kanıtı)"
  - "https://docs.kernel.org/driver-api/i2c.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/power/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/thermal/index.html (200 OK · 2026-10-14)"
validation: "2026-10-14 · yazıldı (Vault Steward Q1 kararı: kapsam kapanışı) · kaynak: repo grep + satır kanıtı + canlı URL · red-team turu bekliyor"
open_questions:
  - "⚠️ VERIFICATION REQUIRED: Windows tarafı WDK/MMA dokümanı (learn.microsoft.com) bu turda tek tek 200 ile doğrulanmadı — genel uçlar (audience-sys) doğrulandı, sayfa derinliği doğrulanmadı."
  - "⚠️ VERIFICATION REQUIRED: batı@CoreMusic cihaz envanteri (PCI/ACPI ID listesi) bu node'da yok — os-donanim-tespiti dalına ait."
  - "repo grep-0 (PHP/JS'te os-elektronik anahtar kelimeleri) bu turda tekrarlanmadı; son kayıt 2026-10-14 oturumundadır ve kapsam/kapsam.md §C'de belgelendi."
group: kanit
---

# Doğrulama — K000 · electroncis (OS-elektronik arayüzü)

> **`electroncis` yazım hatası dokunulmaz** (v2 §3 KURAL 1) — bu node adı kasıtlı olarak
> bu imla ile yazılır; hiçbir dosyada "electronics" düzeltilmez.

## §0 ELI15

Bu dosya, `electroncis` dalının iddialarını **kanıtla** sıraya dizer. Üç katmanlı bir
 doğrulama uygulanır: (1) **repo kanıtı** — kod gerçekten öyle mi, satır ve grep ile;
 (2) **web kanıtı** — doküman URL'leri canlı mı (200/404 ayrımı, uydurma link yasak);
 (3) **vault kanıtı** — bu dalın iddiaları komşu node'larla ve şemayla tutarlı mı.
 Kanıtsız hiçbir iddia bu dalda sabit yazılmaz; doğrulanamayan `⚠️ VERIFICATION REQUIRED`
 ile, hiç araştırılmayan `UNKNOWN` ile işaretlenir (§ Zero-Hallucination).

## §1 Kapsam ve yöntem

| Katman | Ne ölçülür | Araç | Bu tur sonucu |
|---|---|---|---|
| Repo | Kod tabanı os-elektronik anahtar kelimeleri içeriyor mu | grep (literal) | 0 isabet → dürüstlük kanıtı |
| Repo (satır) | Örnek diye alınan PHP satırları gerçekten var mı | read + satır no | 7/7 doğrulandı (§4) |
| Web | Referans URL'ler canlı mı | HTTP HEAD/GET | 16 × 200 · 5 × 404 (§5) |
| Vault | Şema/komşu tutarlılığı | read (index, kapsam, os-master) | tutarlı · 1 GAP kayıtlı |
| Şema | 10 alan zorunlu, `parent:`/tırnaklı `group` yasak | regex denetimi | §6 |

## §2 Beyazliste — bu dalın dosyaları (12)

| # | Dosya (yol) | Tür | Zemin |
|---|---|---|---|
| 1 | `electroncis/index.md` | index | subdomain · mimari |
| 2 | `electroncis/electroncis-kapsam.md` | kapsam | module · mimari |
| 3 | `electroncis/electroncis-dogrulama.md` | kanıt | module · kanit (bu dosya) |
| 4 | `electroncis/electroncis-ornekler.md` | örnek | module · kod |
| 5 | `donanim-arayuz-protokolleri/index.md` | index | subdomain · mimari |
| 6 | `donanim-arayuz-protokolleri/-anayasa.md` | anayasa | module · mimari |
| 7 | `donanim-arayuz-protokolleri/-ornekler.md` | örnek | module · kod |
| 8 | `donanim-arayuz-protokolleri/-dogrulama.md` | kanıt | module · kanit |
| 9 | `os-guc-elektronigi/index.md` | index | subdomain · mimari |
| 10 | `os-guc-elektronigi/-anayasa.md` | anayasa | module · mimari |
| 11 | `os-guc-elektronigi/-ornekler.md` | örnek | module · kod |
| 12 | `os-guc-elektronigi/-dogrulama.md` | kanıt | module · kanit |

**Bu beyazliste dışına dosya üretilmez.** Eski `devre-tasarimi/` ve `guc-ve-koruma/`
 dizinleri kapsam dışıdır (silme yetkisi bu yazımın dışında).

## §3 Yasaklı yüzeyler ve dokunma denetimi

| Yüzey | Kural |
|---|---|
| `devre-tasarimi/`, `guc-ve-koruma/` | OKUMA/YAZMA/DOKUNMA YOK — başka ajan silecek |
| `os-master.md`, `os-bagimliliklar.md`, `os-dogrulama.md`, `os-donanim-tespiti/` | salt-okunur referans |
| `K002-electroncis/**` | sınır referansı — yazma yok |
| `*.php`, `*.js`, `*.css`, `*.sql` | bu yazım Markdown dışı üretmez |
| `.ai/electronic/**` | yalnız atıf |

## §4 Repo kanıtları

### §4.1 Dürüstlük kanıtı — grep-0

2026-10-14 grep taraması (`acpi|thermal|ps_on|pwr_ok|wakeup|i2s|tdm|i2c|spi|usb_audio`
 literal, `*.php`/`*.js`): **0 isabet.**

**Yorum:** CoreMusic kod tabanı os-elektronik anahtar kelimeleri içermez → bu dalın
 iddiaları kodda **sarkma yapmaz**; `electroncis` yalnız vault mimari düzeyinde. Repo'da
 ayrıca **C/C++ dosyası yok** (0 glob) → `-ornekler` dosyaları şartname/pseudocode düzeyinde
 kalır, sahte kaynak kod bloğu yazılmaz.

### §4.2 Satır-doğrulanmış PHP örnekleri (2026-10-14, okunarak doğrulandı)

| Dosya:satır | Kod (gerçek) | Bu dalda neye örnek |
|---|---|---|
| `shared/src/Api/Bff/DesktopBff.php:35` | `'platform' => php_uname('s'),` | OS metaverisi (PHP 8) |
| `shared/src/AI/AIEngine.php:291` · `:295` | ffprobe komut satırı · `$output = shell_exec($cmd);` | OS süreci çağrısı (PHP 8) |
| `shared/src/Cache/ApcuAdapter.php:9` · `:13` | `function_exists('apcu_fetch')` · `apcu_fetch($key, $success)` | Bellek katmanı (PHP 8) |
| `shared/src/Session/SessionConfig.php:33` · `:35` | `ini_get`/`sys_get_temp_dir` · `@mkdir($savePath, 0750, true);` | Dizin izni (PHP 8) |
| `shared/src/Security/UuidV7.php:22-23` | `pack('N', ...)` / `pack('n', ...)` | Byte paketleme (PHP 8) |
| `home.coremusic.net/include/Stream/MusicStreamHandler.php:185` · `:189` · `:199` | `is_link` · `realpath` · `DIRECTORY_SEPARATOR` | Yol ayracı (PHP 8) |
| `shared/src/Api/Middleware/RateLimitMiddleware.php:40` | `header('Retry-After: ' . $this->windowSeconds);` | Zamanlama/daktilo (PHP 8) |

> Bu satırlar `electroncis-ornekler.md` içinde **yalnız referans** olarak kullanılır;
> tam metin bu dosyada tekrar edilmez (tek doğruluk kaynağı read).

## §5 Web doğrulama matrisi (2026-10-14)

### §5.1 Yaşayan kaynaklar (200 OK)

| # | URL | Alan |
|---|---|---|
| 1 | `https://docs.kernel.org/driver-api/i2c.html` | I2C |
| 2 | `https://docs.kernel.org/driver-api/spi.html` | SPI |
| 3 | `https://docs.kernel.org/driver-api/usb/index.html` | USB |
| 4 | `https://docs.kernel.org/driver-api/gpio/index.html` | GPIO |
| 5 | `https://docs.kernel.org/driver-api/clk.html` | clock |
| 6 | `https://docs.kernel.org/driver-api/thermal/index.html` | termal |
| 7 | `https://docs.kernel.org/power/index.html` | güç yönetimi |
| 8 | `https://docs.kernel.org/power/runtime_pm.html` | runtime PM |
| 9 | `https://docs.kernel.org/hwmon/index.html` | sensör/hwmon |
| 10 | `https://docs.kernel.org/firmware-guide/acpi/index.html` | ACPI |
| 11 | `https://docs.kernel.org/sound/index.html` | ALSA/ses |
| 12 | `https://www.kernel.org/doc/html/latest/sound/` | ses (mirror) |
| 13 | `https://www.kernel.org/doc/html/latest/sound/designs/tracepoints.html` | ses design |
| 14 | `https://www.usb.org/` | USB-IF |
| 15 | `https://docs.microsoft.com/en-us/windows-hardware/drivers/audio/` | Windows ses |
| 16 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/` | Windows kernel |

### §5.2 Ölü linkler — KULLANMA (404, 2026-10-14)

`docs.kernel.org/driver-api/sound.html` · `driver-api/clocksource.html` ·
`timers/clocksource.html` · `timers/clockevents.html` ·
`www.kernel.org/doc/html/latest/sound/hd-audio.html`

> **Kural:** bu beş adres hiçbir dosyaya yazılmaz; ses/clock referansları §5.1'deki
> 200'lerden seçilir.

### §5.3 ⚠️ Doğrulanmayan yüzey

Windows WDK/MMA sayfa derinliği (ör. MMA register listeleri) bu turda tek tek
 doğrulanmadı → ilgili iddialar `⚠️ VERIFICATION REQUIRED` taşır (frontmatter
 `open_questions`).

## §6 Şema doğrulama regex'leri (12 dosya)

| Kontrol | Beklenen |
|---|---|
| Frontmatter alan sayısı | TAM 10: `node_id, node_type, status, parent_id, children, depends_on, evidence, validation, open_questions, group` |
| `parent:` anahtarı | **0 isabet** (yalnız `parent_id`) |
| `group` değeri | tırnaksız: `mimari` / `kod` / `kanit` |
| `status` | `draft` (Q1 kapatıldı, gerçek doğrulama bekliyor) |
| `node_type` | index → `subdomain`, içerik → `module` |
| `children` | index → 2-alt dal listesi, içerik → `[]` |
| `depends_on` | gerçek K-node ID'leri; komşu K000 dalları **tam yol**; `K002-altyapi` SADECE kök `os-master.md` içinde |
| Alt dal dosyası | `children`'da SADECE `index.md` (içerik dosyaları listelenmez) |
| `evidence` | gerçek dosya yolu / 200 URL; doğrulanamayan → `⚠️ VERIFICATION REQUIRED` |
| Mojibake | Türkçe karakter bozulması 0 |

## §7 Vault-içi çapraz referanslar

| Referans | Rol |
|---|---|
| `electroncis/index.md` (bu dal) | 2 alt dal · üst düğüm K000 · Q1 kapandı |
| `electroncis/electroncis-kapsam.md` | İÇİNDE/DIŞI · komşu sınır · `K002-altyapi` GAP |
| `os-master.md` §3/§8 + satır 7 | üst düğüm · GAP kanıtı (salt-okunur) |
| `os-donanim-tespiti/index.md` | şema tutarlılığı örneği (salt-okunur) |
| `.ai/electronic/drivers/audio-drivers.md` (709 satır) | tam elektronik atıf kaynağı |
| `.ai/electronic/firmware/rtos.md` (541 satır) | firmware/R-T atıf kaynağı |

## §8 Açık sorular (doğrulama boyunca birikti)

1. Windows WDK/MMA sayfa derinliği doğrulaması yapılacak mı? (`⚠️ VERIFICATION REQUIRED`)
2. `K002-altyapi` ID GAP'i registry §6 sahibince kapatılacak mı? (TAKİP — çözüldüyse
   `os-master.md` satır 7 güncellenir; bu dal kendi `depends_on`'una uydurma ID yazmaz)
3. repo grep-0 ölçümü her büyük yazım turunda tekrarlanmalı mı? (öneri: EVET —
   C/C++ gelirse `-ornekler` dosyaları gerçek kod bloğuna geçer)
4. batı@CoreMusic cihaz envanteri (PCI/ACPI ID) hangi node'da tutulacak?
   (`os-donanim-tespiti` — bu dal değil)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
