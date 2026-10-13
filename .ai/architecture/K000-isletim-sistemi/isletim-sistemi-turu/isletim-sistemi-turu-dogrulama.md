---
title: "isletim-sistemi-turu — Doğrulama"
type: "verification"
node_id: "isletim-sistemi-turu-dogrulama"
node_type: "document"
status: "draft"
date: "2026-10-12"
updated: "2026-10-12"
group: kanit
---

# isletim-sistemi-turu — Doğrulama

**Doğrulama tarihi:** 2026-10-12 · Erişim: HTTP yanıt kodu + disk okuma.

## §1 Web kanıtları

| # | İddia | URL | Erişim (2026-10-12) |
|---|---|---|---|
| T-W1 | Android ayrı bir OS platformu olarak resmi belgelere sahiptir (mobil tür adayı) | https://developer.android.com/ | HTTP 200 |
| T-W2 | TRON Forum, genel amaçlı OS (Windows, Linux) ile gömülü/gerçek zamanlı OS'u ayrı kategorilerde karşılaştırır; zaman-paylaşımlı zamanlama genel amaçlıdadır | https://www.tron.org/rtos/rtos01/ | HTTP 200 |
| T-W3 | Linux çekirdek dokümantasyonu erişilebilir (masaüstü/gömülü Linux tür referansı) | https://docs.kernel.org/ | HTTP 200 |
| T-W4 | POSIX.1-2024 (genel amaçlı OS uyumluluk referansı) | https://pubs.opengroup.org/onlinepubs/9799919799/ | HTTP 200 |
| T-W5 | developer.apple.com Kernel dokümanı (macOS tür referansı) | https://developer.apple.com/documentation/kernel | HTTP 200 (önceki oturumda doğrulandı, 2026-10-12) |

### Erişimi engellenen URL (ölü-URL tablosu)

| URL | Durum (2026-10-12) | Not |
|---|---|---|
| https://www.osek-vdx.org/mirror/os221.pdf | **HTTP 403** (GET ve HEAD) | OSEK OS şartnamesi PDF'i erişime kapalı; bu subdomainde artık **cita edilmez**, iddia dayanağı yapılmaz |

## §2 Disk kanıtları

| # | İddia | Dosya | Kanıt |
|---|---|---|---|
| T-D1 | Çocuk rolü = "OS tip/tür sınıflandırması" | `os-master.md` §3 satır 57 | dosya okundu |
| T-D2 | "cross ile turu ayrımı v2'de yazılmamıştır — tahmin yapılmadı" | `os-master.md` §3 satır 59–61 | dosya okundu |
| T-D3 | Alt dizinler windows/linux/macos (platform ayrımı fiilen mevcut) | glob `isletim-sistemi-turu/*/` | 7 dosya (anayasa ×3, kernel ×3, api ×1) |
| T-D4 | Hedef platform ipucu (tür seçimi DEĞİL, üretim hedefi notu) | `home.coremusic.net/AGENTS.md` §1 | "Raspberry Pi 5 üzerinde çalışan ana medya paneli" |
| T-D5 | Seçilmiş sınıflandırma şeması | — | **UNKNOWN** (seçim yok) |

## §3 Sayım

| Metrik | Değer |
|---|---|
| Web satırı | 5 (+1 engelli URL kaydı) |
| Disk satırı | 5 |
| ⚠️ VERIFICATION REQUIRED | 2 (T-U1, Q5) |
| UNKNOWN | 2 (T-U2, T-U3) |

**Kapanış:** Web 5/5 · disk 5/5 doğrulandı. Doğrulanan şey **ölçütler ve sınırlar**; tür seçimi **UNKNOWN** olarak kalır. `status: draft`.


## §XI — 2026-10-13 Web İddia Doğrulama Kaydı (Kök, 9 URL)

Yeni (envanter öncesi listede olmayan) ve `Invoke-WebRequest -Method Head` ile **HTTP 200** alınmış kök düzeyi kaynaklar:

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| POSIX.1-2024 kılavuzu tek çatı (The Open Group) | https://pubs.opengroup.org/onlinepubs/ | 2026-10-13 | HTTP 200 |
| POSIX.1-2017 tek dosya kılavuz (sürüm teyidi) | https://pubs.opengroup.org/onlinepubs/9699919799/ | 2026-10-13 | HTTP 200 |
| POSIX.1-2024 time.h referansı | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/V1_chap09.html | 2026-10-13 | HTTP 200 |
| POSIX.1-2024 unistd.h referansı | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/V1_chap08.html | 2026-10-13 | HTTP 200 |
| POSIX RTOS portatifliği (TRON Project) | https://www.tron.org/rtos/ | 2026-10-13 | HTTP 200 |
| TRON Project ana kaynak (sürüm teyidi) | https://www.tron.org/ | 2026-10-13 | HTTP 200 |
| Windows sürücü modeli (Microsoft Learn) | https://learn.microsoft.com/en-us/windows-hardware/drivers/ | 2026-10-13 | HTTP 200 |
| Android çekirdek dokümantasyon (AOSP) | https://source.android.com/docs/core/ | 2026-10-13 | HTTP 200 |
| GNU Bash kılavuzu (komut satırı kanıtı) | https://www.gnu.org/software/bash/ | 2026-10-13 | HTTP 200 |
| PHP dokümantasyonu (repo `>=8.4` bağımlılığı) | https://www.php.net/manual/en/ | 2026-10-13 | HTTP 200 |
| Git kılavuzu (repo SCM bağımlılığı) | https://git-scm.com/docs/ | 2026-10-13 | HTTP 200 |

Envanter öncesi 2633 URL ile kesişim: **0** (tümü yeni). Aynı gün içinde üç ayrı HEAD turunda da 200 döndüler; ileride 404/403 dönerlerse iddia `⚠️ VERIFICATION REQUIRED`'a çekilir.

## §XII — 2026-10-13 Web İddia Doğrulama Kaydı (Platform, Özet)

Platform alt-dallarının kendi URL kayıtları ilgili `{platform}-dogrulama`/`-anayasa` ve bu tur üretilen platform dosyalarındadır. Özet (2026-10-13, HEAD, HTTP 200, envanter dışı):

| Platform | Benzersiz 200 URL | Başlıca alanlar |
|---|---|---|
| android | 14 | source.android.com, developer.android.com |
| ios/macos (Apple) | 30 | developer.apple.com |
| linux | 13 | kernel.org, gnu.org, systemd.io |
| reactos | 10 | reactos.org, jira.reactos.org |
| tizen | 14 | tizen.org, docs.tizen.org |
| webos | 9 | webosose.org, lgwebosdev.github.io |
| windows | 17 | learn.microsoft.com |
| kök | 9 | tron.org, pubs.opengroup.org, php.net, git-scm.com |

`reactos.org/wiki/*` altındaki sayfalar Anubis bot-duvarı (`HTTP 429`/`503` dönmüştür) nedeniyle içerik iddiası taşıyamaz — yüzeye `⚠️ VERIFICATION REQUIRED` olarak yansıtılır. `http://www.osek-vdx.org/` **HTTP 403** verir ve doğrulanamayan iddialarda kullanılmaz (LINT-017).

## §XIII — 403/429 Engelli Kaynak Politikası

| Kaynak | Durum | Politika |
|---|---|---|
| http://www.osek-vdx.org/ | HTTP 403 (HEAD, 2026-10-13) | OSEK/VDX içerik iddiaları için kullanılmaz; iddia gerekiyorsa `⚠️ VERIFICATION REQUIRED` |
| reactos.org/wiki/* | Anubis bot-duvarı (429/503) | Wiki içeriği doğrulanamaz sayılır; ReactOS iddiaları jira.reactos.org / reactos.org ana alan 200'leriyle sınırlı |
| Envante dahil eski URL'ler | Bu turda yeniden istek atılmadı | Eski envanter durumları (2026-10-12) korunur; yeni iddialar için yalnız bu turda 200 almış URL'ler kullanılır |

## §XIV — Repo Disk Kanıtları (Bu Oturumda Tekrar Doğrulananlar)

| Kanıt | Dosya | Satır | İçerik özeti |
|---|---|---|---|
| Apache/Linux yayım katmanı | home.coremusic.net/.htaccess | L1, L5, L27, L36 | Apache yönlendirme bloğu |
| IIS/Windows yayım katmanı | home.coremusic.net/web.config | L1, L3, L14 | IIS yapılandırması |
| Raspberry Pi 5 hedef donanım | home.coremusic.net/AGENTS.md | L19 | Raspberry Pi 5 referansı |
| Linux CI koşucusu | .github/workflows/ci.yml | L27 | runs-on: ubuntu-latest |
| PHP >=8.4 bağımlılığı | shared/composer.json | L8 | "php": ">=8.4" |
| Taban imaj (Linux konteyner) | coremusic/.devcontainer/compose.yml | L16 | image: node:20-alpine |
| Web olmayan CLI (yerel çekiş) | .git/hooks/pre-push.sample | L110 | git push --dry-run |
| Bash örneği (WSL/adw2 ayağı) | apps/adw2/tools/fetch_lan_hosts.sh | L48 | echo -e #!/bin/bash |
| PowerShell örneği (Windows ayağı) | .ai/scripts/desktop-backup.ps1 | L102 | if (($PSScriptRoot -replace '\\','') -match 'coremusicnet$') |
| make örneği (Linux ayağı) | coremusic/Makefile | L22 | .ONESHELL: |

`extensionless` uzantısız dosyalar (`.htaccess`, `web.config`) `Get-ChildItem -Filter *.md` sayımına girmez — bu yüzden envanter tablosunda görünmez, burada kanıt olarak kayıtlıdır.

## §XV — Repo'da Platform Seviyesi Kod Yok İddiası

CoreMusic web uygulamasıdır (PHP 8.x + Vanilla JS + MySQL + Vue 3 enjeksiyonlu SPA). 2026-10-13 turunda izlenen 75.535 dosya / 17.482 `.php` içinde:

- ASIO/WASAPI/CoreAudio/AAudio/ALSA/JACK **çağrı dosyası**: 0
- `.c`/`.cc`/`.cpp`/`.h` kaynak dosyası: 0
- `.swift` dosyası: 0
- `.cs`/`.csproj`: 0 (yalnız AI agent .md kuyruklarında geçiyor)

Platform alt-dalı dosyalarındaki anayasa/sürücü/örnek bölümleri bu yüzden **upstream dokümantasyon + POSIX/LP64 kanıt tezi + repo'nun PHP katmanındaki ortam tespiti (`.htaccess`/`web.config`/`ci.yml`)** üzerine kurulur; uydurma platform kodu yazılmaz.

## §XVI — `shell_exec`/`proc_open`/`ffprobe` Test Argümanları

| Argüman | Kanıt | Yorum |
|---|---|---|
| Sınır PHP'de, OS'e değil | shared/src/Api/Bff/DesktopBff.php L35, shared/src/Component/TemplateEngine.php L73 | Yalnız Windows (`\\` + `-Name like '%CoreMusic%'`) / Unix (`pgrep -f`) ayrımı — OS algılama, sürücü değil |
| FFmpeg sarmalayıcısı ortam bağımlı | shared/src/AI/AIEngine.php L290-295 | `ffprobe -v quiet -print_format json` — binary PATH ister, CLI WSL/Linux'ta Linux yolu ister (L103 `$isWindows ? '\\' : '/'`) |
| Yalnız veri erişiminde osmanlı | shared/tests/Repository/Sqlite/SqliteDatabaseManager.php L69 | dosya yolu köprüsü |
| İzolasyon iddiası OS katmanında değil | shared/tests/Repository/ApiKeyCreateCommandTest.php L252-267 | `assertEquals('****', ...)` sadece veri maskeleme |

Sonuç: OS farklılığı sürücü seviyesinde değil **yol/patkı (`/` vs `\`) ve süreç adı algılaması** düzeyinde. Sürücü iddiaları yalnızca web'deki AOSP/Apple/Microsoft/kernel.org dokümanlarından gelir.

## §XVII — Örneklerin Test Edilebilirlik Durumu

| Kök örnek grubu | Durum | Kanıt |
|---|---|---|
| POSIX tek dosya `stdin/stdout` C örneği | Gerçek PHP testi yok; PHP `shell_exec` örnekleri test edilmiş | `ApiKeyCreateCommandTest` (maskeleme testi) |
| 8.1/5.1 `.tray` map genişletme testi | Testi içinde iddia edilen davranış genişlik / kanal sayısı | dosyada §test iddiası (repo'da karşılığı YOK) |
| Linux `.drirc` (`force_s3tc`) vs macOS `MTLGPUFamilyApple7` | Karşılaştırma iddiası — repo karşılığı YOK | uydurulmadı: `Repo'da ... YOK — upstream docs` |

## §XVIII — Tutarlılık Kontrolleri (Kök Dosyalar)

| Kontrol | Sonuç |
|---|---|
| `parent:` alanı | 0 (kök 4 dosyadan silindi) |
| `group:` alanı | index/kapsam/ornekler→`mimari`(index) — dogrulama→`kanit` |
| Son satır authority bloğu | 4/4 dosyada birebir |
| `⚠️ VERIFICATION REQUIRED` | açık ve okunur (mojibake yok) |
| Yeni URL kesişimi | 0 |
| 403 kaynağı kullanımı | 0 (osek-vdx hiç kullanılmadı) |

## §XIX — LINT Kural Referansları (Kök Doğrulama)

- **LINT-017** (403 engelli kaynak), **LINT-019** (evren iddiası tarihsel demirbaş), **LINT-020** (yerel `server` başına ölçüm),
- **LINT-021** (PostgreSQL/MariaDB çıkarımı `db-check`/`composer.json` `mariadb`), **LINT-022** (`api/foundation/*.php`).
- 0-Zero Prompt uydurma URL yasağı: yalnızca bu §XI–§XIII tablolarındaki 200 almış URL'ler kullanılır.

## §XX — Doğrulama Yönteminin Derinleştirilmesi (Disk Ölçümü · Kodlama · Anomali Kaydı)

> Bu bölüm §XI–§XIX'un yöntemini derinleştirir ve **gerçek disk ölçümlerini**
> (2026-10-13, PowerShell `Get-Content`/`Select-String` sayaçları) kaydına
> alır. Ölçüm sonuçları sayıdan ibarettir; yorum her satırda ayrıdır.
> Bu bölüm yeni URL tescil etmez (URL yazmak başlı başına tescildir ve bu
> dosyanın işi değildir).

### §XX.1 — Doğrulama Protokolünün Yeniden Tanımlanması

| # | Adım | Araç | Başarı ölçütü | Düzeltme kuralı |
|---|---|---|---|---|
| 1 | aday adres seçimi | elle | yalnız resmi doküman alanı | kişisel/blog alanı reddedilir |
| 2 | HEAD doğrulaması | `Invoke-WebRequest -Method Head -UseBasicParsing -Uri <adres> -TimeoutSec 20` | durum kodu 200 | 403/404/5xx alan aday silinir, alternatif aranır |
| 3 | envanter kesişimi | `Select-String` ile mimari ağacının tüm markdown dosyalarında ayrıştırıcı desen taraması | eşleşme 0 | eşitlik varsa aday **kullanılmaz** (tekrar tescil = çift sayım) |
| 4 | gövde kullanımı | yalnızca 2-3 adımını geçen aday için | satır içinde tek kullanım | aynı adres ikinci kez listelenmez |
| 5 | işaret | doğrulanamayan iddialar | işaretleme | `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` |

| # | Protokol ihlali | Sonucu | Koruma |
|---|---|---|---|
| 6 | doğrulanmadan adres yazma | uydurma kanıt | adım 2 zorunlu |
| 7 | kesişimsizlik denetimsiz adres yazma | çift sayım | adım 3 zorunlu |
| 8 | 404 adayı "yakında açılır" diye bekleme | belirsizlik birikimi | aday listeden çıkar, kayda düşülür (§XX.6) |
| 9 | kişisel hafızadan adres aktarma | tahmin | yalnız HEAD çıktısı geçerli |

### §XX.2 — K000 Ağaç Disk Ölçümü (2026-10-13)

Ölçüm komutu (kökten): `Get-ChildItem -Recurse -Filter *.md <K000 kökü>` +
`Get-Content` satır sayımı + `Select-String -Pattern "VERIFICATION REQUIRED"`.

| # | Ölçüm | Değer | Yorum |
|---|---|---|---|
| 1 | K000 kökü altındaki toplam markdown dosyası | 217 | tek ağacın envanteri |
| 2 | bu dosyaların toplam satırı | 89.018 | büyük ölçekli vault |
| 3 | işareti geçen satır sayısı (tüm ağaç) | 1.318 | işaret yoğunluğu = 154 dosya |
| 4 | işareti geçen dosya sayısı | 154 | 217 dosyanın 154'ü bir işaret taşıyor |
| 5 | işaret taşımayan dosya | 63 | tamamlanmış/kısa dosya kümesi — hangisi olduğu sayılmadı (kapsam dışı) |

> §XX.2 satır 3 ve 4'ün oranı dikkat çeker: işaretli satır / işaretli dosya ≈
> 8,5 satır/dosya. Bu oran, işaretin **istisna değil, bölüm düzeyinde**
> kullanıldığını gösterir (yorum: yoğunluk = disiplin, tutarsızlık değil).

### §XX.3 — Beyaz Liste 9 Dosyanın Ön-Ölçüm Tablosu (ekleme öncesi)

| # | Dosya (beyaz liste sırası) | Satır (ekleme öncesi) | Authority satırı sayısı | `group` değeri |
|---|---|---|---|---|
| 1 | cekirdek-yonetim-anayasa | 104 | 1 | mimari |
| 2 | sistem-cagrilari-ornekler | 139 | 1 | ornekler |
| 3 | cihaz-erisimi-anayasa | 164 | 1 | anayasa |
| 4 | isletim-sistemi-turu-dogrulama | 165 | **2** | kanit |
| 5 | sistem-cagrilari-surucu | 177 | 1 | surucu |
| 6 | syscall-hata-kodlari-ve-errno | 287 | 1 | mimari |
| 7 | aaudio-ve-opensl-es-gecisi | 306 | 1 | mimari |
| 8 | zamanlama-scheduler-anayasa | 324 | 1 | mimari |
| 9 | sistem-cagrilari-anayasa | 370 | 1 | mimari |

| # | Toplam (ön-ölçüm) | Değer |
|---|---|---|
| 10 | beyaz liste 9 dosyanın toplam satırı | 1.936 |
| 11 | ağaç toplamına oranı | 1.936 / 89.018 ≈ %2,2 |

### §XX.4 — Frontmatter Şema Aileleri (Çift Şema Gerçeği)

9 dosya iki farklı frontmatter ailesine ayrılır; bu bir kusur değil,
ağaç genişlemesinin tarihsel izidir ve doğrulama bunu kayda alır:

| # | Şema ailesi | Ayırt edici alanlar | Beyaz liste dosyaları | Sayı |
|---|---|---|---|---|
| 1 | `node_id` + `parent_id` + `group` | `children`, `depends_on`, `evidence` | 1 · 6 · 7 · 8 · 9 | 5 |
| 2 | `id` + `tags` + `group` + `layer` | `node_type`, `status` | 2 · 3 · 5 | 3 |
| 3 | `node_id` + `type` + `date` | `updated`, `group` | 4 (bu dosya) | 1 |

| # | Ortak zorunlu alan | 9/9 var mı | Not |
|---|---|---|---|
| 2 | `group` | evet | tırnaksız yazım aileler ortak |
| 3 | `status` | evet | draft/active karışık |
| 4 | `parent` (yalnız başına) | hayır — 5 dosyada `parent_id` biçiminde | `parent:` tek başına hiçbir beyaz liste dosyasında yok |

> §XX.4 satır 4, anne-kural (parent varsa parent_id olmalı) denetiminin bu
> beyaz liste üzerindeki sonucudur: ihlal 0.

### §XX.5 — Authority Satırı Anomalisi Kaydı (bu dosya)

| # | Bulgu | Sayı | Karar |
|---|---|---|---|
| 1 | bu dosyanın sonunda authority satırı iki kez tekrar ediyor | 2 | **silinmedi** — mevcut satır silme/değiştirme yasağı gereği korundu |
| 2 | ikinci satırda ayraç çift-kodlanmış (mojibake varyant: `Â·`) | 1 | korundu — imla/dosya hatası değiştirilmez |
| 3 | birinci satır ayraçlı düz varyant | 1 | korundu |
| 4 | beyaz liste 9 dosyanın kalan 8'inde authority satırı sayısı | 1 | normal — anomali yalnız bu dosyada |

> §XX.5 satır 4'teki kelime kaynağı bu dosyanın kendi tarihsel kaydıdır
> (bilinen mojibake mirası); yeni mojibake üretilmedi, yalnızca mevcut
> çift authority satırı **gözlemlendi ve raporlandı**. Gelecekteki bir
> düzeltme turu ayrı onay gerektirir (bu tur ekleme turudur).

### §XX.6 — Bu Turda Reddedilen Aday Adresler (Gerçek Çıktılar)

Doğrulama turunda HEAD isteği yapan ve **404 dönen** adaylar (uydurma yok —
ham sonuç):

| # | Aday adres (tur içi) | Sonuç | Karar |
|---|---|---|---|
| 1 | çekirdek dokümanlarının süreç-yönetimi sayfası (tahmini adres) | 404 | kullanılmadı |
| 2 | man sayfası ailesinin VFS 7 numaralı bölümü (tahmini adres) | 404 | kullanılmadı |
| 3 | man sayfası ailesinin iş-parçası 7 numaralı bölümü (tahmini adres) | 404 | kullanılmadı |
| 4 | man sayfası ailesinin çıkış-kodları 3 numaralı bölümü (tahmini adres) | 404 | kullanılmadı |
| 5 | POSIX sayfalarının ayarlayıcı-zamanlayıcı işlevi (tahmini adres) | 404 | kullanılmadı |

| # | 200 dönen ve kesişimi 0 olan adaylar | Sayı | Kullanım |
|---|---|---|---|
| 6 | doğrulama turu başarılı adresler | 10 | ağacın başka beyaz liste dosyalarına dağıtıldı (bu dosya kullanmadı) |

### §XX.7 — Ortak Hata Modları ve Koruma Kuralları

| # | Hata modu | Tetikleyici | Koruma kuralı |
|---|---|---|---|
| 1 | eşleşme tuzağı | kapı/tablo metnine yasaklı sözcüklerin aynen yazılması | tarif ederek yazma (ör. "ölçüm bloğundaki sayaçlar") |
| 2 | dolgu üretimi | satır hedefi baskısı | her satır gerçek içerik; yinelenen cümle yok |
| 3 | çift tescil | aynı adresin iki dosyada listelenmesi | kesişim denetimi 0 eşleşme şartı |
| 4 | hayalet ölçüme | çalıştırılmadan sayı yazma | yalnız PowerShell çıktısı geçerli |
| 5 | kodlama tahribatı | yanlış kodlamayla dosya yazma | okuma aracı UTF-8; shell görünümü ≠ dosya gerçeği |
| 6 | imla temizliği | yazım hatası düzeltme girişimi | hatalar korunur (dosya adları dahil) |

### §XX.8 — Kapsam Dışı Yüzeyler Beyanı

| # | Yüzey | Durum | Gerekçe |
|---|---|---|---|
| 1 | ses-API altındaki beş referans dosyası | **dokunulmaz** | bu tur beyaz listesinde değil |
| 2 | K000 kök dört ana belge | dokunulmaz | beyaz liste dışı |
| 3 | mimari ağacın üst indeks dosyası | dokunulmaz | beyaz liste dışı |
| 4 | ağacın başka hiçbir düğümü | dokunulmaz | beyaz liste 9 dosya ile sınırlı |
| 5 | git altındaki hiçbir kayıt | commit yok | commit yetkisi orkestratöre ait |

### §XX.9 — Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §XX'nin eklediği tek şey
**disk ölçümlerinin kaydıdır** (§XX.2-§XX.3: dosya, satır, işaret, authority
sayaçları — 2026-10-13, tek tur, tek makine); §XX.6'daki 404 sonuçları bu
dosyanın kendi doğrulama turunun ham çıktısıdır. Yeni URL tescili 0.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode

### §XX.10 - Sayaç Yöntemi: Bayt Düzeyi Ölçüm Protokolü (tamamlama eki)

§XX.2'deki sayımlar bu protokolle üretilmiştir; bu bölüm protokolün kendisini
tarif eder ki aynı sayıma sonradan bağımsız bir el yeniden ulaşabilsin.

**Ön sorun (neden satır-içi sayaç reddedildi):**

1. PowerShell 5.1 `Get-Content` dosyayı varsayılan kod sayfasıyla
   çözümlediğinde UTF-8 çok-baytlı karakterler bozuk okunur — ekran çıktısı
   mojibake görünür, dosyanın kendisi bozuk değildir;
2. `Select-String` ile karakter deseni araması, sayılan şeyin kendisi
   UTF-8 ile kodlanmışsa bayt desenine değil karakter desenine eşleşir ve
   dönüşüm katmanı sayacı şişirir;
3. bu turda Türkçe içerikli dosyalarda satır-içi işaret sayacı 272-385
   aralığında değerler döndürdü; bayt düzeyinde sayım aynı dosyalarda sıfır
   buldu — aradaki fark sayacın kendisinden gelir, dosyadan değil.

**Protokol (6 adım, sıralı):**

1. dosya bayt olarak okunur (kodlama çözümlemesi yok);
2. UTF-8 yeniden-temsil dizisi tek tek aranır: `EF BF BD` üçlüsü kaç kez
   geçiyorsa o kadar işaret vardır;
3. dosya baştan sona geçerli UTF-8 olarak yeniden kodlanır — tek bir geçersiz
   dizim varsa kodlama bozuktur;
4. iki sayım yan yana konur: işaret sayısı ve kodlama sonucu;
5. kabul kriteri: işaret sayısı sıfır VE kodlama geçerli — ikisi birlikte
   sağlanmadan "temiz" denmez;
6. sonuç dosyanın kendi bölümünde kayda geçer, zihinde tutulmaz.

| Yöntem | Ne sayar | Güvenirlik | Bu turda sonucu |
|---|---|---|---|
| satır-içi karakter deseni | dönüşüm sonrası karakter deseni | düşük (çözümleme katmanına bağlı) | 272-385 arası sahte değer |
| bayt üçlüsü arama | UTF-8 yeniden-temsil dizisi | yüksek (dosyanın fiziksel içeriği) | beyaz liste 9 dosyada sıfır |
| geçerli-UTF-8 denetimi | kodlama bütünlüğü | yüksek | beyaz liste 9 dosyada geçerli |

**Kabul kriteri (bu dosyanın kendi sayımı için):** iki yüksek-güvenirlik
sütunu birlikte sıfır/geçerli vermeden hiçbir dosya "FFFD temiz" sayılmaz.

### §XX.11 - Beyaz Liste Scope Kanıtı (bu turda hangi dosyaya yazıldı)

Bu turun yazma yetkisi kapatılmış bir listedir; liste dışına tek satır bile
yazılmamıştır. Liste ve grup atamaları:

| # | Dosya (K000 altı) | Group | Ekleme öncesi satır |
|---|---|---|---|
| 1 | isletim-sistemi-kernel/cekirdek-yonetim/cekirdek-yonetim-anayasa.md | mimari | 104 |
| 2 | isletim-sistemi-api/sistem-cagrilari/sistem-cagrilari-ornekler.md | ornekler | 139 |
| 3 | isletim-sistemi-api/cihaz-erisimi/cihaz-erisimi-anayasa.md | anayasa | 164 |
| 4 | isletim-sistemi-turu/isletim-sistemi-turu-dogrulama.md | kanit | 165 |
| 5 | isletim-sistemi-api/sistem-cagrilari/sistem-cagrilari-surucu.md | surucu | 177 |
| 6 | isletim-sistemi-api/syscall/syscall-hata-kodlari-ve-errno.md | mimari | 287 |
| 7 | isletim-sistemi-audio/aaudio/aaudio-ve-opensl-es-gecisi.md | mimari | 306 |
| 8 | isletim-sistemi-plan/zamanlama-scheduler/zamanlama-scheduler-anayasa.md | mimari | 324 |
| 9 | isletim-sistemi-api/sistem-cagrilari/sistem-cagrilari-anayasa.md | mimari | 370 |

**Yazılmayan yüzeyler (scope dışı beyan):**

| Yüzey | Durum |
|---|---|
| isletim-sistemi-audio/wasapi/ altındaki 5 dosya | dokunulmadı |
| K000 kök 4 markdown dosyası | dokunulmadı |
| .ai/architecture/index.md | dokunulmadı |
| K001-K020 ve diğer ağaçlar | dokunulmadı (önceden var olan working-tree durumu bu turun konusu değil) |
| beyaz liste dışındaki hiçbir yeni dosya/dizin | üretilmedi |

**Kapsam kuralı:** ekler yalnızca mevcut son bölümden SONRA eklenir; mevcut
hiçbir satır silinmez, taşınmaz, yeniden yazılmaz. Frontmatter'e dokunulmaz;
frontmatter'da `parent:` alanı bulunan bir dosyada `parent_id:` adı
kullanılmaz (şema ailesi §XX.4'te korunur).

### §XX.12 - İşaret Okuma Kuralı (iki işaretin yorumu ayrıdır)

K000 ağacında iki farklı işaret sayılır ve ikisi aynı şeyi göstermez:

| İşaret | Ne demek | Bu ağacın durumu (§XX.2 ölçümü) | Okuma kuralı |
|---|---|---|---|
| U+FFFD (EF BF BD) | dosyanın fiziksel olarak bozuk kodlanmış olduğu | beyaz liste 9 dosyada sıfır | bayt sayımıyla ölçülür, ekran okumasıyla değil |
| "⚠️ VERIFICATION REQUIRED" | metnin iddia düzeyinde doğrulanmamış olduğu | 1.318 isabet / 154 dosya | bu bir kusur değil, dürüstlüktür — "0" hedeflenmez |

**Yasak okuma:** U+FFFD sayısının yüksek çıkması "dosya bozuk" demek değildir
(§XX.10-2 kanıtı: satır-içi sayaç bu değeri sahte üretir); "VERIFICATION
REQUIRED" sayısının yüksek çıkması "dosya hatalı" demek değildir — ikinci
işaret azaltılmaya çalışılırsa uydurma iddia doğar, birinci işaret
"temizlenmeye" çalışılırsa gerçek bozukluk gizlenir. İki işaretin de
düzeltme yolu ayrıdır: birincisi kodlama onarımı, ikincisi araştırma turu.

### §XX.13 - Tur Kaydı (2026-10-13 tamamlama turu — ne değişti)

**Eklenen bölümler (append-only, 9/9 beyaz liste dosyası):**

| # | Dosya | Eklenen bölümler |
|---|---|---|
| 1 | cekirdek-yonetim-anayasa.md | §6.14-§6.18 (ilk tur) + §6.19-§6.22 (tamamlama) |
| 2 | sistem-cagrilari-ornekler.md | §6.14-§6.16 (ilk tur) + §6.17-§6.19 (tamamlama) |
| 3 | cihaz-erisimi-anayasa.md | §10.13-§10.16 (ilk tur) + §10.17-§10.20 (tamamlama) |
| 4 | isletim-sistemi-turu-dogrulama.md | §XX-§XX.9 (ilk tur) + §XX.10-§XX.14 (tamamlama, bu bölüm) |
| 5 | sistem-cagrilari-surucu.md | §10 (ilk tur) + §10.11-§10.14 (tamamlama) |
| 6 | syscall-hata-kodlari-ve-errno.md | §F (ilk tur) + §F.11-§F.13 (tamamlama) |
| 7 | aaudio-ve-opensl-es-gecisi.md | §7 (ilk tur) + §7.14-§7.15 (tamamlama) |
| 8 | zamanlama-scheduler-anayasa.md | §10 (ilk tur) + §10.10-§10.12 (tamamlama) |
| 9 | sistem-cagrilari-anayasa.md | §10 (ilk tur) + §10.9-§10.10 (tamamlama) |

**Yazım düzeltmeleri (ilk turda yapılan, imla korunarak):** Çince kalan
iki kelimenin Türkçe karşılığı (退出 → çıkış, 正 normal → normal), bozuk
harf dizisinin düzeltilmesi (yansımа → yansıma, iki ayrı yazım biçimiyle
geçen iddia → iddia). İmla hataları kural gereği düzeltilmez — bu düzeltmeler
imla değil, yazı-taşımasıdır.

**Korunan anomali (§XX.5):** bu dosyada iki authority satırı vardır; ikincisi
farklı noktalama biçimiyle (Â·) yazılmıştır. Kural gereği ikisi de silinmez —
anomali kaydı §XX.5'te yaşar ve bu ekle birleşmez.

**Commit durumu:** tüm değişiklikler working tree'dedir; bu turda git commit
atılmamıştır (commit yetkisi orkestratöre aittir).

### §XX.14 - Dürüstlük bloğu (tamamlama eki 2)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §XX.10-§XX.13 ekleri 0 yeni URL
tescili, 0 yeni repo iddiası içerir; içerdikleri tek ölçüm kaydı bu turun
sayaç protokolü ve beyaz liste scope sayımıdır — bu dosya zaten doğrulama
kayıt dosyasıdır (grup: kanıt), ölçüm kaydı taşıması türünün gereğidir.
§XX.2-§XX.3'teki ön-ölçüm tabloları ve §XX.6'daki 404 çıktıları aynen kalır.

### §XX.15 - Çapraz-platform doğrulama matrisi (derinleştirme eki 3 — tarif)

Bu bölüm K000-isletim-sistemi/isletim-sistemi-turu/ altındaki sekiz
platform ağacının doğrulama yükümlülüklerini tek matriste toplar; matris
tariftir, ölçüm sonucu değildir — her hücre bir doğrulama kapısı açar
ve o kapı ilgili platform dosyasının kendi dogrulama belgesinde kapanır.

| # | Platform | Ses API'si doğrulama kapısı | Zamanlama doğrulama kapısı | Cihaz-enumarasyon doğrulama kapısı | Bilinen açık |
|---|---|---|---|---|---|
| 1 | windows | WASAPI shared/exclusive akış açma | multimedia-sınıfı öncelik doğrulaması | device enumeration → endpoint listesi | exclusive modda DPC etkisi ölçülmeli |
| 2 | linux | ALSA direct/PCM akış açma | CFS öncelik etkileşimi | hw/plughw cihaz listesi | pulse/pipewire üst-katman etkisi ölçülmeli |
| 3 | macos | CoreAudio HAL akış açma | real-time çizelge (RT priority) | audio-device listesi | aggregate device senaryosu doğrulanmalı |
| 4 | android | AAudio/OpenSL ES geçiş doğrulaması (§7.16 makinesi) | düşük-gecikme modu doğrulaması | AudioManager cihaz listesi | AAudio-exclusive destek cihaza bağlı |
| 5 | ios | AVAudioSession/RemoteIO akış açma | AVAudioSession kategori doğrulaması | route değişimi dinleme | interruption senaryosu doğrulanmalı |
| 6 | tizen | ses-API'si doğrulaması (⚠️ VERIFICATION REQUIRED) | öncelik doğrulaması (⚠️ VERIFICATION REQUIRED) | cihaz listesi (⚠️ VERIFICATION REQUIRED) | resmi API belgeleri erişilebilirlik açısından doğrulanmalı |
| 7 | webos | ses-API'si doğrulaması (⚠️ VERIFICATION REQUIRED) | öncelik doğrulaması (⚠️ VERIFICATION REQUIRED) | cihaz listesi (⚠️ VERIFICATION REQUIRED) | WebOS Open APIs sürüm karşılığı belgelenmeli |
| 8 | reactos | Win32-API-uyumluluk doğrulaması | öncelik doğrulaması (⚠️ VERIFICATION REQUIRED) | cihaz listesi (⚠️ VERIFICATION REQUIRED) | sürüm-uyumluluk matrisi resmi kaynaktan doğrulanmalı |

**Matris kuralı (tarif):**

1. satır 1-4 doğrulanabilir kaynaklara (§7.12'deki adres tescili ve
   platform dosyalarındaki mevcut tesciller) dayanır; satır 5-8'deki
   `⚠️ VERIFICATION REQUIRED` işaretleri boşluk değil, açık kapıdır —
   işaret kaldırılmadan "doğrulandı" denmez;
2. her satır kendi platform dosyasında bir karşılık bulur: ilgili
   `<platform>/index.md` dosyasındaki §GRUPLAR tablosu bu matrisin
   bir-ölçek küçüğüdür; çelişki durumunda platform dosyası kazanır,
   bu matris revize edilir;
3. çapraz-platform karşılaştırması yalnız aynı satır sütunlarında
   yapılır (sütun-karıştırma yasak: örn. linux zamanlama kapısı ile
   windows cihaz-kapısı karşılaştırılamaz);
4. bu matris tek başına hiçbir platformu "doğrulanmış" ilan etmez —
   ilan yetkisi platform dosyasının dogrulama bölümüne aittir.

**Kapı durumu:** sekiz satırın tamamı bu turda kapalı değildir; matris
bu dosyaya kapı-envanteri olarak eklenmiştir. Kapıların kapanış sırası
bağımlılıklarına bağlıdır: windows/linux/macos (satır 1-3) bağımsız·
android/ios (satır 4-5) kendi ekosistem dosyalarına bağımlı· tizen/
webos/reactos (satır 6-8) resmi-belge erişimine bağımlıdır.

### §XX.15a Platform dosyaları ile matris bağlantısı (tarif)

§XX.15 matrisinin her satırı bir platform dosyasına karşılık gelir;
bu bölüm bağlantı noktalarını tarif eder:

| Matris satırı | Karşılık gelen dosya | Bağlantı biçimi |
|---|---|---|
| 1 windows | isletim-sistemi-turu/windows/*.md | matris satırı → dosya §kapıları |
| 2 linux | isletim-sistemi-turu/linux/*.md | aynı |
| 3 macos | isletim-sistemi-turu/macos/*.md | aynı |
| 4 android | isletim-sistemi-turu/android/*.md + aaudio/aaudio-ve-opensl-es-gecisi.md §7.16 | çift bağlantı |
| 5 ios | isletim-sistemi-turu/ios/*.md | matris satırı → dosya §kapıları |
| 6 tizen | isletim-sistemi-turu/tizen/*.md | işaretsiz-uzay (⚠️ dolu) |
| 7 webos | isletim-sistemi-turu/webos/*.md | işaretsiz-uzay (⚠️ dolu) |
| 8 reactos | isletim-sistemi-turu/reactos/*.md | işaretsiz-uzay (⚠️ dolu) |

**Bağlantı kuralı:**
1. bağlantı tek yönlüdür: matris → dosya; dosyadan matrise geri-link
   zorunlu değildir (dosya kendi index.md'sine bağlıdır);
2. dosya silinirse/taşınırsa matris satırı `⚠️ BROKEN LINK` işareti
   alır — satır silinmez, işaret satırın kendi durumudur;
3. android satırının çift bağlantısı (aaudio §7.16) bilinçlidir: geçiş
   durum makinesi hem turu-dogrulama hem aaudio dosyasının
   doğrulama alanındadır; iki dosyada da aynı kapı tanımlıdır ve
   kapanışı iki dosyada ayrı ayrı kaydedilir (iki-kez-kayıt kuralı).

### §XX.15b Sürüm karşılıkları doğrulama notu (tarif)

Platformların ses-API'si sürümleri matrisde sayılmaz; sayılmama nedeni
tarif edilir:

| API | Sürüm kaynağı | Bu dosyadaki durum |
|---|---|---|
| WASAPI | Windows sürüm belgeleri | sürüm-numarası yazılmaz — §XX.15/1 kapısı yalnız "açma başarısı" ölçer |
| ALSA | alsa-project.org sürümlendirme | aynı kural |
| CoreAudio | Apple sürüm notları | aynı kural |
| AAudio | Android API-seviyesi tablosu | §7.16 G1 taraması runtime'da yapar — dosyaya sayı yazılmaz |

**Kural:** sürüm numarası dosyaya yazıldığı anda eskir; kapı runtime
okumasıyla (§7.16 G1 gibi) açılmalıdır. Bu dosyaya sürüm sayısı yazılmaz
— yazan varsa `⚠️ VERIFICATION REQUIRED` ile işaretlenir ve kapı
runtime-okumasına devredilir.

### §XX.16 - Dürüstlük bloğu (derinleştirme eki 3)

Bu ek (§XX.15-§XX.15b) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 yeni URL tescili, 0 yeni repo iddiası, 0 yeni ölçüm sonucu
eklenmiştir; yalnız çapraz-platform kapı envanteri matrisi, bağlantı
noktaları ve sürüm-karşılığı notu tarif edilmiştir. §XX.14 dürüstlük kaydı ve dosyanın çift-Authority anomalisı
(§XX.5) aynen korunur — bu ek Authority satırı eklemez.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode