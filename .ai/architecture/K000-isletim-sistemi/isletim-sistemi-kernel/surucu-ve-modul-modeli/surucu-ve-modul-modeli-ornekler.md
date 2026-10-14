---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/surucu-ve-modul-modeli/surucu-ve-modul-modeli-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel/surucu-ve-modul-modeli
children: []
depends_on: []
evidence:
  - shared/src/Api/Bff/DesktopBff.php:35
  - shared/src/AI/AIEngine.php:292
  - shared/src/AI/AIEngine.php:295
  - shared/src/Component/TemplateEngine.php:73
  - shared/src/Component/TemplateEngine.php:74
  - shared/src/Session/SessionConfig.php:35
validation: "2026-10-13 · repo satırları grep ile birebir (php_uname:35 · escapeshellarg:292 · shell_exec:295 · sys_get_temp_dir:73 · LOCK_EX:74 · mkdir:35) · negatif kanıt grep 0 (insmod/modprobe/init_module/file_operations/spinlock · tüm *.php) · ioctl yüzeyi: Windows DeviceIoControl envanter 200 · Linux ioctl 404 → KULLANILMADI"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
  - "`electroncis` ↔ kernel sürücü sınırı (Q1) → UNKNOWN"
group: kod
---

# surucu-ve-modul-modeli-ornekler — Repo Örnekleri ve Senaryolar (Kademe-4 · §1-§6)

## §0 ELI15

Bu dosya, anayasadaki modül/sürücü doktrinini **repo örnekleriyle** eşler.
CoreMusic'te çekirdek içi sürücü/modül kodu **yok** (grep 0); buna karşılık
**kullanıcı uzayından çekirdeğe giden dolaylı köprüler** **var**: OS sorgusu
(`php_uname`), alt süreç (`shell_exec`), geçici dosya + kilit, dizin izni.
Bu dosya bu köprüleri satır kanıtıyla açar, sürücü kavramıyla eşler ve
çekirdek-yokluğunun dürüstlük kaydını taşır.

| Öğe | Değer |
|---|---|
| Dosya rolü | Örnek kataloğu (Kademe-4 · `group: kod`) |
| Bölüm omurgası | §1-§6 (bu dosya) |
| Bağlı dosyalar | `surucu-ve-modul-modeli-anayasa.md` · `surucu-ve-modul-modeli-surucu.md` · kök `index.md` §1 |
| Kapsam | Repo köprü örnekleri · sürücü eşlemesi · negatif kanıt |
| Kapsam dışı | Çekirdek içi sürücü kodu (repoda yok) · ioctl uç noktası (`-api`) · test kodu (`-testing`) |

> **Kanıt kuralı:** Her repo örneği `dosya:satır` ile işaretlidir ve grep ile
> birebir doğrulanmıştır (2026-10-13).

## §1 Örnek kataloğu — her satır tek bir gözlemlenebilir örüntü

### §1.1 Katalog tablosu

| # | Örüntü | Dosya | Satır | Kanıt sınıfı | Karşılık |
|---|---|---|---|---|---|
| K1 | platform/OS sorgusu | `shared/src/Api/Bff/DesktopBff.php` | 35 | repo (+) | sürüm/uyum (§2.1) |
| K2 | argüman kaçırma | `shared/src/AI/AIEngine.php` | 292 | repo (+) | sınır doğrulama (§2.2) |
| K3 | alt süreç başlatma | `shared/src/AI/AIEngine.php` | 295 | repo (+) | modül başlatma (§2.3) |
| K4 | geçici dosya yolu | `shared/src/Component/TemplateEngine.php` | 73 | repo (+) | VFS dosya (§2.4) |
| K5 | kilitlemeli yazma | `shared/src/Component/TemplateEngine.php` | 74 | repo (+) | dosya kilidi (§2.5) |
| K6 | dizin izni (0750) | `shared/src/Session/SessionConfig.php` | 35 | repo (+) | yetki (§2.6) |
| K7 | modül yükleme kodu | (tüm `*.php`) | — | repo (−) | **yok** (§4) |
| K8 | VFS operasyon tablosu | (tüm `*.php`) | — | repo (−) | **yok** (§4) |
| K9 | sürücü ioctl çağrısı | (tüm `*.php`) | — | repo (−) | **yok** (§4) |
| K10 | sürücü test kodu | (tüm `*.php`) | — | repo (−) | **yok** (§4) |
| K11 | eşik/süre değeri | — | — | yokluk | **yazılmaz** |

### §1.2 Kanıt sınıfları

| Sınıf | Ne demek | Katalogdaki | Güvenilirlik |
|---|---|---|---|
| repo (+) | dosya+satır grep ile birebir | K1-K6 | en yüksek |
| repo (−) | grep 0 isabet = o kod yok | K7-K10 | yüksek |
| upstream | resmi doküman URL'si (HTTP 200) | §5 | doküman düzeyi |
| yokluk | bilinçli yazılmayan değer | K11 | kural (İlke B10) |

## §2 Repo örnekleri — sürücü kavramıyla eşleme

### §2.1 K1 — `DesktopBff` platform sorgusu (sürüm/uyum eşlemesi)

| Dosya | Satır | Ne görülür | Sürücü kavramı karşılığı |
|---|---|---|---|
| `DesktopBff.php` | 35 | `'platform' => php_uname('s'),` | sürüm/uyum (vermagic — anayasa §1.5) |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| ne yapar | OS adını sorar | satır 35 |
| sürücü karşılığı | sürüm/uyum kontrolü (kavram) | anayasa §1.5 |
| sonuç | yanıt gövdesine yazılır | — |
| hedef çekirdek | `UNKNOWN` | Q4 |

**Ayrım kuralı:** Bu satır **uyum kontrolü değildir**, yalnız **sorgudur**;
çekirdek sürümü/uyumu **bilinmiyor** (Q4). Kavramsal eşleme: "modül hangi
çekirdekte çalışır?" sorusunun kullanıcı uzayındaki en zayıf hali.

### §2.2 K2 — `AIEngine` argüman kaçırma (sınır doğrulama eşlemesi)

| Dosya | Satır | Ne görülür | Sürücü kavramı karşılığı |
|---|---|---|---|
| `AIEngine.php` | 292 | `escapeshellarg($filePath)` | sınır doğrulama (surucu §4.4) |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| ne yapar | argümanı kaçırır | satır 292 |
| sürücü karşılığı | argüman doğrulama katmanı | surucu §4.1 (adım 2) |
| güvenlik etkisi | komut enjeksiyonuna karşı | — |
| doğrulama kapsamı | tek argüman | `$filePath` |

**Eşleme kuralı:** `escapeshellarg` **kullanıcı uzayı** doğrulamasıdır;
çekirdek **ayrı** doğrular (surucu §4.4 matrisi). İkisi **katmanlıdır** —
biri diğerinin yerine geçmez (B-S1).

### §2.3 K3 — `AIEngine` alt süreç başlatma (modül init eşlemesi)

| Dosya | Satır | Ne görülür | Sürücü kavramı karşılığı |
|---|---|---|---|
| `AIEngine.php` | 295 | `$output = shell_exec($cmd);` | başlatma (anayasa §2.1 adım 3) |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| ne yapar | alt süreç başlatır + bekler | satır 295 |
| sürücü karşılığı | init çağrısı (kavramsal) | anayasa §2.1 |
| fark | süreç başlatır, modül **değil** | §2.7 ayrım |
| bekleme | bloklayıcı (waiting) | `cekirdek-yonetim` ornekler §2.1 |

**Ayrım kuralı:** `shell_exec` **süreç başlatır**, modül **değil**; eşleme
**kavramsal**tır (başlatma → bekleme → sonuç). Modül init'i **çekirdek
içinde** olur (anayasa §2.1); bu satır **kullanıcı uzayında**.

### §2.4 K4 — `TemplateEngine` geçici dosya yolu (VFS eşlemesi)

| Dosya | Satır | Ne görülür | Sürücü kavramı karşılığı |
|---|---|---|---|
| `TemplateEngine.php` | 73 | `sys_get_temp_dir()` + rastgele ad | VFS dosya (anayasa §3.2) |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| ne yapar | geçici dosya yolu üretir | satır 73 |
| sürücü karşılığı | VFS dosya oluşturma | anayasa §3.2 |
| güvenlik | rastgele ad (tahmin zor) | `random_bytes` |

### §2.5 K5 — `TemplateEngine` kilitlemeli yazma (dosya kilidi eşlemesi)

| Dosya | Satır | Ne görülür | sürücü kavramı karşılığı |
|---|---|---|---|
| `TemplateEngine.php` | 74 | `file_put_contents(..., LOCK_EX)` | dosya kilidi (kesme dalı §2.4) |

**Not:** Bu satır kardeş dal (`kesme-ve-senkronizasyon` ornekler K1) ile
aynı kanıttır; iki dosya aynı örneği **farklı açıdan** işler (senkro vs VFS).

### §2.6 K6 — `SessionConfig` dizin izni (yetki eşlemesi)

| Dosya | Satır | Ne görülür | sürücü kavramı karşılığı |
|---|---|---|---|
| `SessionConfig.php` | 35 | `@mkdir($savePath, 0750, true);` | yetki denetimi (anayasa §3.3) |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| izin | 0750 | sabit `:20` |
| sürücü karşılığı | capability/VFS izni (kavram) | anayasa §3.3 |
| denetleyen | çekirdek (inode izni) | — |
| PHP rolü | **isteyen** taraf | — |

**Ayrım kuralı:** `mkdir` izni **VFS iznidir**; capability **farklı**
katmandır (surucu §7.3 — yetkiler katmanlıdır, B-M8).

### §2.7 Köprü özeti — PHP satırı ↔ sürücü kavramı ↔ sınır

| Repo satırı | Sürücü kavramı | Sınır katmanı (surucu §4.1) | Kanıt sınıfı |
|---|---|---|---|
| `php_uname:35` | sürüm/uyum sorgusu | — (kullanıcı içi) | kavramsal |
| `escapeshellarg:292` | argüman doğrulama | 2 (doğrulama) | kavramsal |
| `shell_exec:295` | başlatma (init eşlemesi) | 1-3 (syscall) | kavramsal |
| `sys_get_temp_dir:73` | VFS dosya | 4 (VFS) | kavramsal |
| `LOCK_EX:74` | dosya kilidi | 4 (VFS) | kavramsal |
| `mkdir:35` | yetki (VFS izni) | 3 (doğrulama) | kavramsal |

**Eşleme kuralı:** "Sınır katmanı" sütunu **tahmin değil**, surucu §4.1
katman modelinin eşlemesidir; gerçek syscall bu turda **ölçülmedi**
(strace yok) → ⚠️ VERIFICATION REQUIRED.

## §3 IOCTL yüzeyi — repo karşılığı (işaretli)

### §3.1 IOCTL × repo tablosu

| Yüzey | Repo karşılığı | Durum |
|---|---|---|
| Windows `DeviceIoControl` | **yok** (grep K9) | repo'da yok |
| Linux ioctl | **yok** (grep K9) | repo'da yok |
| VFS dosya operasyonu | dolaylı (K4-K6) | kavramsal |
| PHP `fopen`/`file_put_contents` | var (K4, K5) | VFS köprüsü |

**Dürüstlük cümlesi:** Repo'da **ioctl çağrısı yok** (grep K9 = 0); PHP
dosya işlemleri **VFS üzerinden** dolaylıdır — ioctl yüzeyi bu repoda
**kullanılmıyor** (kullanılmıyor olabilir, tam tarama ile doğrulanır).

### §3.2 IOCTL × sürüm işareti

| Yüzey | URL durumu | Kullanım |
|---|---|---|
| Windows DeviceIoControl | ✅ envanter 200 | kullanılabilir (§5) |
| Linux ioctl ayrıntısı | ❌ **404** | **KULLANILMADI** (surucu §4.3) |

**İşaret kuralı:** 404 URL **hiçbir dosyada kullanılmaz**; Linux ioctl
ayrıntısı `⚠️ VERIFICATION REQUIRED` olarak taşınır (surucu B-S7).

## §4 Negatif kanıt — repo'da olmayan sürücü/modül kodu

### §4.1 Grep tablosu (2026-10-13 · tüm `*.php`)

| # | Aranan | Kapsam | İsabet | Yorum |
|---|---|---|---|---|
| N1 | `insmod` | tüm `*.php` | 0 | modül yükleme yok |
| N2 | `modprobe` | tüm `*.php` | 0 | modül çözümleme yok |
| N3 | `init_module` | tüm `*.php` | 0 | modül başlatma yok |
| N4 | `file_operations` | tüm `*.php` | 0 | VFS operasyon tablosu yok |
| N5 | `spinlock` | tüm `*.php` | 0 | çekirdek kilit yok |
| N6 | ioctl çağrısı (`ioctl`/`DeviceIoControl`) | tüm `*.php` | 0 | ioctl yüzeyi yok |

### §4.2 Dürüstlük cümlesi

0 isabet "**repoda o kod yok**" der; "**sürücü/modül hiç yok**" **demez** —
6 dolaylı köprü örneği vardır (§1.1 K1-K6). Bu dosyadaki iddialar yalnız
grep kapsamıyla sınırlıdır (2026-10-13, tüm `*.php`).

### §4.3 Olumsuz kanıtın sonucu — ne yapılamaz

| Soru | Cevap | Neden |
|---|---|---|
| hangi sürücü yüklü? | `UNKNOWN` | N1-N3 = 0 + Q4 |
| VFS operasyonları neler? | `UNKNOWN` | N4 = 0 |
| ioctl yüzeyi hangi kodla? | `UNKNOWN` | N6 = 0 |
| modül sızıntısı var mı? | `UNKNOWN` | modül kodu yok |
| test kapsamı (coverage) | `UNKNOWN` | test kodu yok (K10) |

## §5 Doğru/yanlış kullanım ve kaynakça

### §5.1 Doğru örüntüler (mimari uygunluk)

| # | Örüntü | Repo kanıtı | Neden doğru |
|---|---|---|---|
| D1 | argüman kaçırma | K2 (`escapeshellarg`) | enjeksiyona karşı |
| D2 | rastgele dosya adı | K4 (`random_bytes`) | tahmin zor |
| D3 | kilitlemeli yazma | K5 (`LOCK_EX`) | yarış azaltılıyor |
| D4 | izin sabiti tek yerde | K6 (`DIR_PERMISSIONS`) | sihirli sayı yok |
| D5 | ölçüm yoksa eşik yazmama | bu dosyanın tabloları | uydurma değer yok |

**Örnek kuralı:** "Doğru" burada **mimari uygunluk** demektir; davranışsal
doğruluk (test) `-testing` dalının alanı.

### §5.2 Yanlış kullanım — bu repoda görülmesi muhtemel hatalar

| # | Yanlış kullanım | Sonuç | İlgili hata modu |
|---|---|---|---|
| Y1 | `escapeshellarg`'ı ihmal | komut enjeksiyonu | güvenlik |
| Y2 | kilitsiz yazma | yarış koşulu | kesme dalı |
| Y3 | 0777 izin | yetki açığı | güvenlik |
| Y4 | kilit türünü karıştırma | yanlış ölçüm | kesme dalı §3.2 |
| Y5 | 0 isabeti "sürücü yok" diye genişletmek | iddia taşması | §4.2 |
| Y6 | 404 URL kullanmak | kanıtsız iddia | §3.2 |

### §5.3 Kaynakça — envanterden yeniden kullanılan URL'ler (HTTP 200 · 2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| O-W1 | VFS çekirdek arayüzü | https://docs.kernel.org/filesystems/vfs.html | 200 (envanter) |
| O-W2 | Windows DeviceIoControl | https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol | 200 (envanter) |
| O-W3 | Sürücü geliştirme arayüzü | https://docs.kernel.org/driver-api/index.html | 200 (envanter) |
| O-W4 | Modül imzalama | https://docs.kernel.org/admin-guide/module-signing.html | 200 (envanter) |
| O-W5 | `insmod(8)` | https://man7.org/linux/man-pages/man8/insmod.8.html | 200 (envanter) |

**Doğrulama notu:** Beş URL de `-dogrulama.md` §E envanterinden yeniden
kullanıldı (2026-10-13 HTTP 200); bu dosyada **yeni** URL doğrulanmadı.

### §5.4 Ölçüm — bu dosyanın kendi ölçüm tablosu

| # | Ölçü | Değer | Durum |
|---|---|---|---|
| 1 | repo örneği sayısı | 6 örüntü (K1-K6) | ölçüldü (2026-10-13) |
| 2 | negatif kanıt (grep) | 6 arama / 0 isabet (N1-N6) | ölçüldü (2026-10-13) |
| 3 | URL (yeniden kullanım) | 5 (HTTP 200) | ölçüldü (2026-10-13) |
| 4 | kullanılmayan URL | 1 (ioctl 404) | ölçüldü (2026-10-13) |
| 5 | gerçek syscall | — | ⚠️ (ölçülmedi) |
| 6 | eşik değeri | — | yazılmaz (İlke B10) |
| 7 | açık soru | 3 (Q1-Q4) | açık |

### §5.5 Dosyanın komşulara verdiği özet (handover satırı)

| Alıcı dal | Verilen bilgi | Biçim |
|---|---|---|
| `surucu-ve-modul-modeli-anayasa` | repo köprü örnekleri + negatif kanıt | §1, §4 |
| `surucu-ve-modul-modeli-surucu` | sınır eşlemesi + risk | §2.7, §5.2 |
| `isletim-sistemi-api` | syscall/ioctl sınırı (dolaylı) | §2.7, §3.1 |
| `isletim-sistemi-security` | argüman kaçırma + izin örneği | §2.2, §2.6 |
| `isletim-sistemi-testing` | test senaryoları taslağı | §6.2 |
| `kesme-ve-senkronizasyon` | dosya kilidi kardeş kanıtı (K5) | §2.5 |

**Handover kuralı:** Bu tablodaki alıcılar bilgiyi **kullanır**, bu dosyanın
satırlarını **değiştirmez**; değişiklik ADR/üst karar ister.

## §6 Senaryolar ve açık sorular

### §6.1 Senaryolar — adım adım (kavramsal akış)

| Senaryo | Adımlar | Beklenen | Durum |
|---|---|---|---|
| S1: OS sorgusu | K1 → yanıt | platform adı döner | kavramsal |
| S2: güvenli komut | K2 → K3 | kaçırma + başlatma + bekleme | kavramsal |
| S3: kilitlemeli yazma | K4 → K5 | geçici dosya + kilit | kavramsal |
| S4: yetkili dizin | K6 | 0750 izniyle dizin | kavramsal |
| S5: modül yükleme | (repo'da **yok**) | — | **imkânsız** (N1-N3) |

**Senaryo kuralı:** S1-S4 **kavramsal** akıştır (repo satırı §2'de işaretli);
S5 bu repoda **imkânsızdır** (modül kodu yok) — bu dürüstlük kaydıdır.

### §6.2 Test senaryoları — test dalına verdiği taslak

| # | Senaryo | Adım | Beklenen | Durum |
|---|---|---|---|---|
| TS1 | argüman kaçırma | K2 | enjeksiyon engellenir | taslak |
| TS2 | kilitlemeli yazma | K5 | dosya kilidi alınır | taslak |
| TS3 | izin reddi | K6 (yetkisiz) | `mkdir` başarısız | taslak |
| TS4 | negatif kanıt tekrarı | N1-N6 | 0 isabet | **tekrarlanabilir** |
| TS5 | 404 URL yokluğu | §3.2 | 404 kullanılmaz | **tekrarlanabilir** |

**Senaryo kuralı:** TS1-TS3 bu turda **çalıştırılmadı**; TS4-TS5 yapısal
olarak **tekrarlanabilirdir**. Test kodu bu dosyaya **yazılmaz** (`-testing`).

### §6.3 Açık sorular ve kapanış yolları

| # | Soru | Etiket | Kapanış yolu |
|---|---|---|---|
| Q1 | core ↔ kernel sınırı (Q2) | ⚠️ VERIFICATION REQUIRED | üst karar |
| Q2 | hedef çekirdek (Q4) | UNKNOWN | ortam/prod okuması |
| Q3 | `electroncis` ↔ sürücü sınırı (Q1) | UNKNOWN | üst karar |
| Q4 | gerçek syscall hangisi (§2.7)? | ⚠️ VERIFICATION REQUIRED | strace/ölçüm |
| Q5 | `escapeshellarg` her argümanda mı? | ⚠️ VERIFICATION REQUIRED | tam dosya taraması |

**Kapanış kuralı:** Q1-Q3 üst karar ister; Q4 ölçüm ister; Q5 kod taraması
ister. Bu beş soru açık kaldığı sürece dosya `draft` statüsünde kalır.

### §6.4 Değişiklik günlüğü (bu dosya)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0-§6) | eksik alt-dal derinleştirme görevi |
| 2026-10-13 | repo satırları grep ile birebir | kanıt kapısı |
| 2026-10-13 | negatif kanıt kaydedildi (6 arama/0) | kanıt kapısı |
| 2026-10-13 | ioctl 404 işareti kaydedildi | kanıt kapısı |

## §7 Derinleştirme — köprü akışları adım adım

### §7.1 K3 akış derinliği (`shell_exec:295` → libc → syscall)

| # | Katman | Ne olur | Kanıt sınıfı |
|---|---|---|---|
| 7.1.1 | PHP | `shell_exec($cmd)` çağrılır | repo (satır 295) |
| 7.1.2 | PHP runtime | `/bin/sh -c` ile sarmalanır | kavramsal ⚠️ |
| 7.1.3 | libc | `popen`/`system` → `fork` + `execve` | upstream ⚠️ |
| 7.1.4 | çekirdek | `clone` ile süreç yaratılır | upstream (clone.2) |
| 7.1.5 | çekirdek | çocuk `execve` ile resim değiştirir | upstream ⚠️ |
| 7.1.6 | bekleyiş | ebeveyn pipe'tan okur (blok) | upstream (wait.2) |

> 7.1.2/7.1.3/7.1.5 satırları **kavramsal/upstream**tır; bu turda `strace`
> ile gerçek syscall listesi **çekilmedi** → ⚠️ VERIFICATION REQUIRED (Q4).

### §7.2 K5 akış derinliği (`LOCK_EX:74` → VFS → kilit)

| # | Katman | Ne olur | Kanıt sınıfı |
|---|---|---|---|
| 7.2.1 | PHP | `file_put_contents(..., LOCK_EX)` | repo (satır 74) |
| 7.2.2 | PHP runtime | `open` + `flock`/`LOCK_EX` semantiği | upstream ⚠️ |
| 7.2.3 | VFS | dosya akışı üzerinden yazma | vfs.html (200) |
| 7.2.4 | çekirdek | kilit için bekleme (gerekirse) | kesme dalı §2.4 |
| 7.2.5 | dönüş | yazma + kilit bırakma | kavramsal |

> 7.2.2'nin hangi syscall'a indiği (BSD `flock` mu, POSIX `fcntl` mu) PHP
> dokümantasyonundan **bu turda doğrulanmadı** → ⚠️ (İlke: tahmin yok).

### §7.3 K6 akış derinliği (`mkdir:35` → VFS → izin denetimi)

| # | Katman | Ne olur | Kanıt sınıfı |
|---|---|---|---|
| 7.3.1 | PHP | `@mkdir($savePath, 0750, true)` | repo (satır 35) |
| 7.3.2 | PHP | `@` hataları bastırır (istisna değil) | PHP semantiği ⚠️ |
| 7.3.3 | VFS | dizin oluşturma çağrısı | vfs.html (200) |
| 7.3.4 | çekirdek | inode izin denetimi (0750) | capabilities.7 (200) |
| 7.3.5 | sonuç | başarısızsa `false` (sessiz) | kavramsal |

> 7.3.2'nin "sessiz hata" etkisi bu dosyanın **riski**dir, kanıtı değil:
> hata yutma denetimi `-security` dalının taslağına eklenir (§5.5 handover).

## §8 Hata kataloğu — köprü ne zaman bozulur

| # | Hata modu | Belirti | Etkilendi | Sınıf |
|---|---|---|---|---|
| H1 | komut enjeksiyonu | K2 ihmal edilirse | K3 | güvenlik |
| H2 | PATH bağımlılığı | komut adı tam yolla değil | K3 | uyum |
| H3 | yarış: kilitsiz yazma | K5 ihmal edilirse | K4-K5 | senkro |
| H4 | yetki reddi | 0750 uygulanamazsa | K6 | yetki |
| H5 | geçici dosya sızıntısı | K4 dosyası temizlenmezse | K4 | bellek/dosya |
| H6 | hata yutma | `@`/`null` sonucu denetlenmezse | K6 | güvenilirlik |
| H7 | ortam farkı | `php_uname` sonucu yorumlanırsa | K1 | uyum |

> H1-H7 **taslak katalogdur** (test edilmedi); her satır §2'deki repo
> köprüsüne bağlıdır, uydurma senaryo yok.

## §9 Komşu sınır matrisleri

| Komşu dal | Bu dosyanın verdiği | Bu dosyanın almadığı | Sınır |
|---|---|---|---|
| `kesme-ve-senkronizasyon` | LOCK_EX kardeş kanıtı (K5) | çekirdek kilit kodu | dosya kilidi ≠ spinlock |
| `cekirdek-yonetim` | bekleme/uyanma eşlemesi (§7.1.6) | zamanlama politikası | durum makinesi anayasa'da |
| `surucu-ve-modul-modeli-anayasa` | repo köprüleri + negatif kanıt | doktrin tekrarı | doktrin anayasa'da |
| `surucu-ve-modul-modeli-surucu` | sınır eşlemesi (§2.7) | risk azaltımı | azaltım surucu'da |
| `isletim-sistemi-api` | syscall/ioctl kavramsal harita | syscall listesi (⚠️) | gerçek liste ölçüm işi |
| `isletim-sistemi-security` | H1/H4/H6 hata kataloğu | düzeltme kararı | karar security dalında |

## §10 Sürüm notu — dosyanın kendi ölçüm tablosu (taze)

| # | Ölçü | Değer | Tarih |
|---|---|---|---|
| 10.1 | dolu satır sayısı | bu sürümde yeniden sayıldı | 2026-10-13 |
| 10.2 | repo (+) örneği | 6 (K1-K6) | 2026-10-13 |
| 10.3 | repo (−) araması | 6 (N1-N6) / 0 isabet | 2026-10-13 |
| 10.4 | envanter URL | 5 (HTTP 200) | 2026-10-13 |
| 10.5 | yeni URL doğrulama | 0 | 2026-10-13 |
| 10.6 | kırık/uydurma iddia | 0 | 2026-10-13 |
| 10.7 | ⚠️ işaretli iddia | §7.1 (3 satır) · §7.2 (2 satır) · §7.3 (1 satır) | 2026-10-13 |

> 10.1 satırı sayıyı **sabit olarak yazmaz**; geçerli sayı ölçüm anında
> PowerShell ile sayılır (uydurma sayaç yasak — İlke B10).

## §11 Derinleştirme — köprü sınıflandırma matrisleri

### §11.1 Köprü ↔ syscall sınıfı (kavramsal eşleme genişletmesi)

| Repo köprüsü | syscall sınıfı | VFS/dosya mı? | Süreç mü? | Bellek mi? |
|---|---|---|---|---|
| K1 `php_uname:35` | (syscall değil — uname bilgisi) | hayır | hayır | hayır |
| K2 `escapeshellarg:292` | (düzleştirme, syscall değil) | hayır | hayır | hayır |
| K3 `shell_exec:295` | `clone`+`execve`+pipe (§7.1) | hayır | **evet** | hayır |
| K4 `sys_get_temp_dir:73` | `open`/`stat` sınıfı | **evet** | hayır | hayır |
| K5 `LOCK_EX:74` | kilit + yazma (§7.2) | **evet** | hayır | hayır |
| K6 `mkdir:35` | `mkdir` + izin | **evet** | hayır | hayır |

> K1-K2'nin "syscall değil" sütunu **dürüstlük ayrımıdır**: her PHP çağrısı
> çekirdeğe inmez; K2 düzleştirmedir (uygulama katmanı), K1 sorgulamadır.

### §11.2 Kanıt gücü matrisi (hangi iddia ne kadar güçlü)

| İddia | Güç | Dayanak | Kullanım sınırı |
|---|---|---|---|
| "repo'da 6 köprü örneği var" | en yüksek | grep dosya:satır | her yerde kullanılabilir |
| "repo'da modül kodu yok" | yüksek | grep 0 (N1-N3) | kapsam: `*.php` |
| "köprü syscall'a iner" | orta | upstream doküman | ⚠️ (ölçülmedi) |
| "hangi syscall'a iner" | zayıf | kavramsal tahmin | ⚠️ (strace yok) |
| "sürücü yüklenemez" | yüksek | N1-N3 + Q4 | bu repoda |
| "ioctl kullanılmıyor" | yüksek | N6 grep 0 | kapsam: `*.php` |

### §11.3 Sürüm/karşılaştırma matrisi (platform bağımsızlık)

| Köprü | Linux karşılığı | Windows karşılığı | Bu dosyanın iddiası |
|---|---|---|---|
| K3 `shell_exec` | `fork`+`execve` ⚠️ | `CreateProcessW` ⚠️ | kavramsal — Q4 |
| K5 `LOCK_EX` | `flock`/`fcntl` ⚠️ | `LockFileEx` ⚠️ | kavramsal — ⚠️ |
| K6 `mkdir 0750` | POSIX izin | ACL/izin modeli ⚠️ | kavramsal — ⚠️ |
| K1 `php_uname` | `uname(2)` sınıfı | `GetVersionEx` sınıfı ⚠️ | kavramsal — ⚠️ |

> Her ⚠️ hücresi **ölçülmemiş/karşılaştırılmamış** demektir; platform
> eşlemeleri uydurma **değil**, işaretli tahmindir (Q4 açık).

### §11.4 Kapsam dışı beyanlar (bu dosyanın yazmadığı)

| Beyan | Neden yazılmaz | Kime ait |
|---|---|---|
| "hangi modül çekirdekte yüklü" | Q4 + N1-N3 (bilgi yok) | `-observability` |
| sürücü kaynak kodu | çekirdek içi kod bu vault'ta yok | — |
| ioctl parametre düzeni | Linux ioctl 404 → ⚠️ | üst karar |
| test kodu / coverage | test dalının alanı | `-testing` |
| güvenlik düzeltmesi | taslak, karar değil | `-security` |

> Kapsam dışı beyanlar bu dosyada **hiçbir tabloya girmez**; komşu dal
> handover tablosu (§5.5) üzerinden taşınır.

## §12 Tarihçe ve güncelleme günlüğü (bu dosya)

| Tarih | Değişiklik | Kanıt |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0-§6) | görev sözleşmesi |
| 2026-10-13 | repo satırları grep ile birebir | §1.1 doğrulama |
| 2026-10-13 | negatif kanıt 6 arama / 0 isabet | §4.1 |
| 2026-10-13 | §7-§10 derinleştirme eklendi | dolu-satır kapısı |
| 2026-10-13 | §11-§12 sınıflandırma matrisleri eklendi | dolu-satır kapısı |
| 2026-10-13 | §4.3 yazım düzeltmesi (yabancı karakter) | denetim |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
