---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon/kesme-ve-senkronizasyon-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon
children: []
depends_on: []
evidence:
  - shared/src/Component/TemplateEngine.php:74
  - media.coremusic.net/src/Media/CatalogWriter.php:113
  - shared/src/Security/SecurityHelper.php:54
  - shared/src/PageRouter/PageRouterKernel.php:133
  - shared/src/PageRouter/PageRouterKernel.php:134
  - shared/src/Log/FileHandler.php:251
  - home.coremusic.net/include/Auth/HomeAuthBridge.php:25
  - home.coremusic.net/include/Auth/HomeAuthBridge.php:117
validation: "2026-10-13 · LOCK_EX grep 6 isabet / 5 dosya (birebir) · flock()/pcntl_/sem_/WaitForSingleObject/CreateMutex grep 0 isabet (tüm *.php) · spinlock/insmod/modprobe/init_module/request_irq/file_operations grep 0 (kardeş dal kanıtı) · URL: envanterden yeniden kullanım (HTTP 200)"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
  - "LOCK_EX kilidi ne kadar bekletiyor? → PLANNED (ölçülmedi)"
group: kod
---

# kesme-ve-senkronizasyon-ornekler — Repo Örnekleri ve Senaryolar (Kademe-4 · §1-§6)

## §0 ELI15

Bu dosya, anayasadaki kesme/kilit doktrinini **repo örnekleriyle** eşler.
CoreMusic'te çekirdek içi kilit/spinlock kodu **yok** (grep 0); buna karşılık
**kullanıcı uzayı** senkronizasyonu **var**: 6 `LOCK_EX` (dosya kilidi) ve
`usleep` (bekleme). Bu dosya bu örneği satır kanıtıyla açar, doğru
kullanım/anti-pattern ayırır ve çekirdek-yokluğunun dürüstlük kaydını taşır.

| Öğe | Değer |
|---|---|
| Dosya rolü | Örnek kataloğu (Kademe-4 · `group: kod`) |
| Bölüm omurgası | §1-§6 (bu dosya) |
| Bağlı dosyalar | `kesme-ve-senkronizasyon-anayasa.md` · `kesme-ve-senkronizasyon-surucu.md` · kök `index.md` §1 |
| Kapsam | Repo senkronizasyon örnekleri · senaryolar · negatif kanıt |
| Kapsam dışı | Çekirdek içi kilit kodu (repoda yok) · test kodu (`-testing`) · gözlem (`-observability`) |

> **Kanıt kuralı:** Her repo örneği `dosya:satır` ile işaretlidir ve grep ile
> birebir doğrulanmıştır (2026-10-13).

## §1 Örnek kataloğu — her satır tek bir gözlemlenebilir örüntü

### §1.1 Katalog tablosu

| # | Örüntü | Dosya | Satır | Kanıt sınıfı | Karşılık |
|---|---|---|---|---|---|
| K1 | şablon önbelleği kilitleme | `shared/src/Component/TemplateEngine.php` | 74 | repo (+) | dosya kilidi (§2.1) |
| K2 | katalog yazımı kilitleme | `media.coremusic.net/src/Media/CatalogWriter.php` | 113 | repo (+) | dosya kilidi (§2.2) |
| K3 | güvenlik logu kilitleme | `shared/src/Security/SecurityHelper.php` | 54 | repo (+) | ekleme-kilidi (§2.3) |
| K4 | hata logu kilitleme (×2) | `shared/src/PageRouter/PageRouterKernel.php` | 133, 134 | repo (+) | ekleme-kilidi (§2.4) |
| K5 | dosya logu kilitleme | `shared/src/Log/FileHandler.php` | 251 | repo (+) | ekleme-kilidi (§2.5) |
| K6 | bekleme (uyku) | `home.coremusic.net/include/Auth/HomeAuthBridge.php` | 25, 117 | repo (+) | sleep (§2.6) |
| K7 | çekirdek kilit kodu | (tüm `*.php`) | — | repo (−) | **yok** (§4) |
| K8 | süreç kilidi / `flock()` | (tüm `*.php`) | — | repo (−) | **yok** (§4) |
| K9 | `pcntl_` / `sem_` | (tüm `*.php`) | — | repo (−) | **yok** (§4) |
| K10 | Windows senkron nesnesi | (tüm `*.php`) | — | repo (−) | **yok** (§4) |
| K11 | eşik/süre değeri | — | — | yokluk | **yazılmaz** |

### §1.2 Kanıt sınıfları — dört sınıf aynı tabloda birleştirilmez

| Sınıf | Ne demek | Katalogdaki | Güvenilirlik |
|---|---|---|---|
| repo (+) | dosya+satır grep ile birebir | K1-K6 | en yüksek (gözlemlenmiş) |
| repo (−) | grep 0 isabet = o kod yok | K7-K10 | yüksek (eksiksiz tarama) |
| upstream | resmi doküman URL'si (HTTP 200) | §5 | doküman düzeyi |
| yokluk | bilinçli yazılmayan değer | K11 | kural (İlke B10) |

## §2 Repo örnekleri — dosya kilidi ve bekleme

### §2.1 K1 — `TemplateEngine` şablon önbelleği kilidi

| Dosya | Satır | Ne görülür | Yorum |
|---|---|---|---|
| `TemplateEngine.php` | 73 | `sys_get_temp_dir()` + rastgele dosya adı | geçici dosya yolu |
| `TemplateEngine.php` | 74 | `file_put_contents($cachePath, $template, LOCK_EX)` | kilitlemeli yazma |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| kilit türü | dosya kilidi (LOCK_EX) | satır 74 |
| kapsam | tek dosya | `$cachePath` |
| bekleyen | kilidi bekleyen süreç uyur | PHP kilit modeli |
| çekirdek karşılığı | fcntl/flock (kullanıcı uzayı) | kavramsal (§3) |
| ölçüm | **yok** | süre kodu grep 0 |

**Doğru kullanım:** Çok süreçli (php-fpm) ortamda önbellek yazımının
çakışmasını önlemek. **Yanlış kullanım:** Uzun okuma-yazma-I/O sırasında
kilidi tutmak (bekleyen her süreç uyur — §4 A2).

### §2.2 K2 — `CatalogWriter` katalog yazımı kilidi

| Dosya | Satır | Ne görülür | Yorum |
|---|---|---|---|
| `CatalogWriter.php` | 113 | `@file_put_contents($this->yol, $metin, LOCK_EX)` | kilitlemeli yazma |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| hedef | katalog dosyası | `$this->yol` |
| kilit türü | LOCK_EX | satır 113 |
| çakışma riski | eşzamanlı yazan iki süreç | PHP-FPM modeli |
| ölçüm | **yok** | — |

### §2.3 K3 — `SecurityHelper` güvenlik logu kilidi

| Dosya | Satır | Ne görülür | Yorum |
|---|---|---|---|
| `SecurityHelper.php` | 54 | `FILE_APPEND \| LOCK_EX` | ekleme + kilitleme |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| mod | ekleme (append) | `FILE_APPEND` |
| kilit | LOCK_EX | satır 54 |
| güvenlik etkisi | log kaybı önlenir | ekleme-kilit kombinasyonu |

### §2.4 K4 — `PageRouterKernel` hata logu kilidi (×2 satır)

| Dosya | Satır | Ne görülür | Yorum |
|---|---|---|---|
| `PageRouterKernel.php` | 133 | `coremusic_php_errors.log` · `FILE_APPEND \| LOCK_EX` | hata logu |
| `PageRouterKernel.php` | 134 | `coremusic_php_kernel_debug.log` · `FILE_APPEND \| LOCK_EX` | debug logu |

**Not:** İki satır **aynı desenin** iki farklı dosyaya uygulanmasıdır;
aynı kural (§2.3) geçerlidir.

### §2.5 K5 — `FileHandler` dosya logu kilidi

| Dosya | Satır | Ne görülür | Yorum |
|---|---|---|---|
| `FileHandler.php` | 251 | `FILE_APPEND \| LOCK_EX` | ekleme-kilidi |

**Not:** Bu satır kardeş dal (`bellek-kavramlari` ornekler K7) ile aynı
kanıttır; iki dosya aynı örneği **farklı açıdan** işler (bellek vs senkro).

### §2.6 K6 — `HomeAuthBridge` bekleme (uyku)

| Dosya | Satır | Ne görülür | Yorum |
|---|---|---|---|
| `HomeAuthBridge.php` | 25 | `RETRY_DELAY_US = 200_000` (200 ms) | bekleme sabiti |
| `HomeAuthBridge.php` | 117 | `usleep(self::RETRY_DELAY_US);` | bekleme çağrısı |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| tür | uyku (sleep) | `usleep` |
| süre | 200 ms (sabit) | sabit 25 |
| backoff | üstel? | ⚠️ VERIFICATION REQUIRED (tam dosya okunmadı) |
| kilit mi? | hayır | bekleme ≠ kilit |

**Ayrım kuralı:** `usleep` **kilit değildir** — bekleyen süreç CPU'yu
bırakır (uyur); spinlock'ta süreç döner (CPU kullanır). İki kavram
karıştırılırsa yanlış ölçüm doğar (anayasa §2.1).

### §2.7 Örnek özeti — kilit deseni karşılaştırması

| Örnek | Desen | Kilit | Bekleyen | Ölçüm |
|---|---|---|---|---|
| K1 | yazma-kilitle | LOCK_EX | uyur | yok |
| K2 | yazma-kilitle | LOCK_EX | uyur | yok |
| K3-K5 | ekleme-kilitle | FILE_APPEND+LOCK_EX | uyur | yok |
| K6 | bekleme | — | uyur | yok |

**Özet kuralı:** Tüm repo örnekleri **kullanıcı uzayı** senkronizasyonudur;
çekirdek içi kilit (spinlock/mutex) **yok** (§4 dürüstlük kaydı).

## §3 Kilit köprüsü — repo örneği ↔ çekirdek kavramı

### §3.1 Dolaylı köprü tablosu (PHP → libc → syscall)

| Repo örneği | Muhtemel syscall (dolaylı) | Kilit türü (çekirdek) | Durum |
|---|---|---|---|
| K1-K5 (LOCK_EX) | fcntl/flock | dosya kilidi (VFS) | kavramsal (§3.2) |
| K6 (usleep) | nanosleep | — (uyku) | kavramsal (§2.6) |

### §3.2 Kilit türü eşlemesi — repo ↔ anayasa

| Repo deseni | Anayasa kilit türü | Bölüm | Ayrım |
|---|---|---|---|
| LOCK_EX | dosya kilidi (kullanıcı uzayı) | §2.4 | spinlock/mutex değil |
| usleep | sleep (kilit değil) | §2.1 | bekleyen CPU bırakır |
| (yok) | spinlock | §2.1 | çekirdek içi — repo'da yok |
| (yok) | mutex | §2.1 | çekirdek içi — repo'da yok |

**Eşleme kuralı:** "Muhtemel syscall" sütunu **tahmin değil**, POSIX/glibc
köprüsünün bilinen yoludur; hangi syscall'un gerçekten çağrıldığı bu turda
**ölçülmedi** (strace yok) → ⚠️ VERIFICATION REQUIRED.

## §4 Negatif kanıt — repo'da olmayan çekirdek/senkronizasyon kodu

### §4.1 Grep tablosu (2026-10-13 · tüm `*.php`)

| # | Aranan | Kapsam | İsabet | Yorum |
|---|---|---|---|---|
| N1 | `spinlock` | tüm `*.php` | 0 | çekirdek kilit kodu yok |
| N2 | `flock()` (fonksiyon) | tüm `*.php` | 0 | süreç kilidi yok |
| N3 | `pcntl_` | tüm `*.php` | 0 | süreç kontrolü yok |
| N4 | `sem_` | tüm `*.php` | 0 | semafor yok |
| N5 | `WaitForSingleObject` | tüm `*.php` | 0 | Windows nesne kilidi yok |
| N6 | `CreateMutex` | tüm `*.php` | 0 | Windows mutex yok |
| N7 | `insmod\|modprobe\|init_module` | tüm `*.php` | 0 | modül yükleme yok |
| N8 | `request_irq\|file_operations` | tüm `*.php` | 0 | kesme/VFS kodu yok |

**Ayrım notu:** N2'de `flock()` **fonksiyonu** arandı (0 isabet); `LOCK_EX`
**bayrağı** `file_put_contents` içinde kullanılır (6 isabet — §1.1 K1-K5).
İkisi farklıdır: bayrak var, ayrı `flock()` çağrısı yok.

### §4.2 Dürüstlük cümlesi

0 isabet "**repoda o kod yok**" der; "**senkronizasyon hiç yok**" **demez**
— 6 `LOCK_EX` örneği vardır (§1.1). Bu dosyadaki iddialar yalnız grep
kapsamıyla sınırlıdır (2026-10-13, tüm `*.php`).

### §4.3 Olumsuz kanıtın sonucu — ne yapılamaz

| Soru | Cevap | Neden |
|---|---|---|
| çekirdek hangi kilit türünü kullanıyor? | `UNKNOWN` | N1 = 0 + Q4 |
| hangi kesme vektörleri aktif? | `UNKNOWN` | N8 = 0 |
| Windows mutex kullanılıyor mu? | `UNKNOWN` | N6 = 0 (kullanılmıyor olabilir) |
| kilit çekişmesi kaç? | `UNKNOWN` | ölçüm yok (surucu §5) |
| LOCK_EX ne kadar bekletiyor? | `UNKNOWN` | süre kodu yok (PLANNED) |

## §5 Doğru ve yanlış kullanım — bu repoda görülen örüntüler

### §5.1 Doğru örüntüler (mimari uygunluk)

| # | Örüntü | Repo kanıtı | Neden doğru |
|---|---|---|---|
| D1 | ekleme + kilitleme | K3, K4, K5 (`FILE_APPEND \| LOCK_EX`) | log kaybı azaltılıyor |
| D2 | tek dosyayı kilitleme | K1, K2 | kapsam en küçük (yalnız hedef dosya) |
| D3 | beklemede sabit süre | K6 (`200_000`) | sonsuz döngü yok |
| D4 | ölçüm yoksa eşik yazmama | bu dosyanın tabloları (`UNKNOWN`) | uydurma değer yok |
| D5 | çekirdek kodu iddia etmeme | §4 dürüstlük | zero-hallucination |

**Örnek kuralı:** "Doğru" burada **mimari uygunluk** demektir; davranışsal
doğruluk (test) `-testing` dalının alanı, bu dosya yalnız örnek kataloğu üretir.

### §5.2 Yanlış kullanım — bu repoda görülmesi muhtemel hatalar

| # | Yanlış kullanım | Sonuç | İlgili hata modu |
|---|---|---|---|
| A1 | kilidi unutmak (`LOCK_UN` eksik) | kilitleme kaçar | dosya kilidi |
| A2 | uzun I/O sırasında kilidi tutmak | bekleyen her süreç uyur | anayasa §2.1 |
| A3 | kilit türünü karıştırma (LOCK_EX = spinlock sanma) | yanlış ölçüm | §3.2 |
| A4 | `usleep`'i kilit sanmak | bekleme/kilit karışır | §2.6 |
| A5 | 0 isabeti "senkronizasyon yok" diye genişletmek | iddia taşması | §4.2 |
| A6 | eşik/süre uydurmak | kanıtsız sayı | yasak (İlke B10) |

### §5.3 Anti-pattern → repo korelasyonu

| Anti-pattern | Bu repoda var mı? | Kanıt | Sonuç |
|---|---|---|---|
| A1 | `UNKNOWN` | `LOCK_UN` grep yapılmadı | ⚠️ (tarama PLANNED) |
| A2 | `UNKNOWN` | kilidin tutulduğu süre okunmadı | PLANNED |
| A3 | hayır (dosya kilidi doğru) | §2.7 özeti | uygun |
| A4 | hayır (`usleep` ayrı) | §2.6 ayrımı | uygun |
| A5 | hayır (bu dosyada) | `UNKNOWN`/`⚠️` işaretli | korunuyor |
| A6 | hayır (eşik yazılmadı) | K11 | korunuyor |

**Korelasyon kuralı:** "Var mı?" hücreleri **kanıtla** cevaplanır; kanıt
yoksa `UNKNOWN` yazılır, "yok" **yazılmaz**.

## §6 Ölçüm, senaryolar ve kaynakça

### §6.1 Ölçüm — bu dosyanın kendi ölçüm tablosu

| # | Ölçü | Değer | Durum |
|---|---|---|---|
| 1 | repo örneği sayısı | 6 örüntü (K1-K6) | ölçüldü (2026-10-13) |
| 2 | LOCK_EX isabeti | 6 (5 dosya) | ölçüldü (2026-10-13) |
| 3 | negatif kanıt (grep) | 8 arama / 0 isabet (N1-N8) | ölçüldü (2026-10-13) |
| 4 | URL (yeniden kullanım) | envanter (HTTP 200) | ölçüldü (2026-10-13) |
| 5 | kilit bekleme süresi | — | `UNKNOWN` (ölçülmedi) |
| 6 | eşik değeri | — | yazılmaz (İlke B10) |
| 7 | açık soru | 3 | açık (frontmatter) |

**Ölçüm dürüstlüğü:** Tablodaki 1-4 **yapısal ölçüm** (dosya/satır/grep);
5-6 **durum ölçümüdür** ve gerçek değer üretilmemiştir.

### §6.2 Senaryolar — bu dosyanın test dalına verdiği taslak

| # | Senaryo | Adım | Beklenen | Durum |
|---|---|---|---|---|
| TS1 | kilitlemeli yazma | K1-K5 | dosya kilidi alınır | taslak |
| TS2 | kilitleme kaçırma | A1 (LOCK_UN) | kilidi kontrol et | taslak (grep yapılmadı) |
| TS3 | bekleme | K6 | ~200 ms bekler | taslak |
| TS4 | negatif kanıt tekrarı | N1-N8 | 0 isabet tekrarlanır | **tekrarlanabilir** |
| TS5 | kilit türü doğrulama | §3.2 | LOCK_EX ≠ spinlock | **tekrarlanabilir** |

**Senaryo kuralı:** TS1-TS3 bu turda **çalıştırılmadı**; TS4-TS5 yapısal
olarak **tekrarlanabilirdir** (grep yeniden çalıştırılabilir). Test kodu bu
dosyaya **yazılmaz** (test dalının alanı).

### §6.3 Kaynakça — envanterden yeniden kullanılan URL'ler (HTTP 200 · 2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| K-W1 | Kilit türleri karşılaştırma | https://docs.kernel.org/locking/locktypes.html | 200 (envanter) |
| K-W2 | Mutex tasarım dokümanı | https://docs.kernel.org/locking/mutex-design.html | 200 (envanter) |
| K-W3 | Lockdep kilit tasarım dokümanı | https://docs.kernel.org/locking/lockdep-design.html | 200 (envanter) |
| K-W4 | Lockstat kilit istatistik altyapısı | https://docs.kernel.org/locking/lockstat.html | 200 (envanter) |
| K-W5 | Windows mutex nesneleri | https://learn.microsoft.com/en-us/windows/win32/sync/mutex-objects | 200 (envanter) |

**Doğrulama notu:** Beş URL de `-dogrulama.md` §E envanterinden yeniden
kullanıldı (2026-10-13 HTTP 200); bu dosyada **yeni** URL doğrulanmadı
(envanter yeterli).

### §6.4 Dosyanın komşulara verdiği özet (handover satırı)

| Alıcı dal | Verilen bilgi | Biçim |
|---|---|---|
| `kesme-ve-senkronizasyon-anayasa` | repo kilit örnekleri + negatif kanıt | §1, §4 |
| `kesme-ve-senkronizasyon-surucu` | kilit deseni karşılaştırması + risk | §2.7, §5.2 |
| `isletim-sistemi-security` | güvenlik logu kilidi örneği (K3) | §2.3 |
| `isletim-sistemi-observability` | log kilidi deseni (K4, K5) | §2.4, §2.5 |
| `isletim-sistemi-testing` | test senaryoları taslağı (TS1-TS5) | §6.2 |
| `cekirdek-yonetim` | dosya kilidi kardeş dal kanıtı (K5) | §2.5 |

**Handover kuralı:** Bu tablodaki alıcılar bilgiyi **kullanır**, bu dosyanın
satırlarını **değiştirmez**; değişiklik ADR/üst karar ister.

## §7 Derin örnekler — adım adım senaryolar (kavramsal akış)

### §7.1 Senaryo S1 — kilitlemeli yazma akışı (K1, K2)

```text
[PHP süreci A] --> [file_put_contents LOCK_EX :74/:113]
                        |
                        v
              [çekirdek: dosya kilidi al]
                        |
        [süreç B dener] --> [uyur — kilidi bekler]
                        |
        [süreç A yazar + bırakır]
                        |
        [süreç B uyanır] --> [kilidi alır, yazar]
```

| Adım | Olay | Bekleme etkisi | Ölçüm | Repo |
|---|---|---|---|---|
| 1 | süreç A kilidi alır | — | yok | `:74` / `:113` |
| 2 | süreç A yazar | — | yok | aynı satır |
| 3 | süreç B dener | **uyur** (sleep) | yok | — |
| 4 | süreç A bırakır | — | yok | — |
| 5 | süreç B uyanır | **runnable** | yok | — |

**Ayrım kuralı:** Adım 3'te süreç B **uyur** (CPU bırakır) — spinlock'ta
**döner** (CPU kullanır). Dosya kilidi her zaman "uyuyan" taraftır
(anayasa §2.1).

### §7.2 Senaryo S2 — ekleme-kilidi akışı (K3, K4, K5)

| Adım | Olay | Etki | Ölçüm | Repo |
|---|---|---|---|---|
| 1 | `FILE_APPEND \| LOCK_EX` | ekleme + kilitleme | yok | `:54` / `:133-134` / `:251` |
| 2 | dosya sonuna yaz | mevcut içerik korunur | yok | — |
| 3 | kilidi bırak | — | yok | — |

**Not:** `FILE_APPEND` POSIX'te atomik ekleme sağlar; `LOCK_EX` ise çok
süreçli ekleme yarışını önler — ikisi birlikte **log kaybı** riskini azaltır.

### §7.3 Senaryo S3 — bekleme akışı (K6)

| Adım | Olay | Durum | Ölçüm | Repo |
|---|---|---|---|---|
| 1 | sabit oku | — | yok | `:25` |
| 2 | `usleep` | **uyku** (CPU bırakır) | yok | `:117` |
| 3 | süre dolar | **runnable** | yok | — |
| 4 | yeniden dener | **running** | yok | — |

**Not:** Bu akış **kilit değildir** — bekleyen süreç kilit beklemez,
zaman bekleme yapar (§2.6 ayrımı).

## §8 Örnek × kilit türü matrisi (repo ↔ anayasa)

| Örnek ↓ / Kilit türü → | dosya kilidi | spinlock | mutex | RCU | kilit yok |
|---|---|---|---|---|---|
| K1 `TemplateEngine` | ✓ | — | — | — | — |
| K2 `CatalogWriter` | ✓ | — | — | — | — |
| K3 `SecurityHelper` | ✓ | — | — | — | — |
| K4 `PageRouterKernel` | ✓ | — | — | — | — |
| K5 `FileHandler` | ✓ | — | — | — | — |
| K6 `HomeAuthBridge` | — | — | — | — | ✓ (sleep) |
| (çekirdek içi) | — | **yok** (N1) | **yok** (N1) | **yok** | — |

**Matris kuralı:** `✓` = "o kilit türü o örnekte **gözlemlendi**";
`—` = "o tür o örnekte **tasarımsal olarak yok**". "yok (N1)" = grep 0
kayıtlıdır (§4.1). Bu matris **tahmin değil**, gözlem-haritasıdır.

### §8.1 Örnek × risk ilişkisi

| Örnek | Kilit kaçak riski (A1) | Uzun tutma riski (A2) | Ölçüm eksikliği |
|---|---|---|---|
| K1 | `UNKNOWN` (LOCK_UN okunmadı) | `UNKNOWN` | ✓ (süre yok) |
| K2 | `UNKNOWN` | `UNKNOWN` | ✓ |
| K3-K5 | `UNKNOWN` | `UNKNOWN` | ✓ |
| K6 | — (kilit değil) | — | ✓ (süre sabit, ölçülmedi) |

**Risk kuralı:** `UNKNOWN` hücreler **tarama eksikliğidir**, "risk yok"
**değildir**; kapanış için tam dosya okuması/ölçüm gerekir.

## §9 Sık yapılan hatalar — bu dalda görülmesi muhtemel 8 hata

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| E1 | "repoda kilit yok" demek | 6 LOCK_EX ile çelişir | "çekirdek kilit kodu yok, dosya kilidi var" |
| E2 | LOCK_EX'i spinlock sanmak | kilit türleri karışır | §3.2 eşleme |
| E3 | usleep'i kilit sanmak | bekleme/kilit karışır | §2.6 ayrımı |
| E4 | flock() ile LOCK_EX'i aynı sanmak | N2 vs K1-K5 | §4.1 ayrım notu |
| E5 | 0 isabeti "senkronizasyon yok" diye genişletmek | iddia taşması | §4.2 dürüstlük |
| E6 | kilit süresi tahmin etmek | kanıtsız süre | PLANNED (§6.1 #5) |
| E7 | LOCK_UN'ı yok saymak | kilitleme kaçak | §5.2 A1 |
| E8 | eşik uydurmak | kanıtsız sayı | yasak (İlke B10) |

**Hata kuralı:** E1, E5, E6 bu dosyanın **doğruluk** hatalarıdır; diğerleri
**yorum** hatalarıdır. Doğruluk hatası bir kez işlendiğinde dosya
`draft`'tan çıkmaz (yeniden denetim gerekir).

### §9.1 Hata × kapı ilişkisi

| Hata | Hangi kapıda yakalanır | Kapı durumu |
|---|---|---|
| E1/E5 | K4 (olumsuz kanıt) | ✅ geçti (2026-10-13) |
| E6/E8 | K3 (eşik) | ✅ geçti (eşik yazılmadı) |
| E2/E3/E4 | kavram denetimi | ✅ geçti (§2.6, §3.2, §4.1) |
| E7 | tam dosya taraması | ❌ açık (LOCK_UN grep yapılmadı) |

## §10 Açık sorular ve kapanış yolları

### §10.1 Açık sorular

| # | Soru | Etiket | Kapanış yolu |
|---|---|---|---|
| Q1 | core ↔ kernel sınırı (Q2) | ⚠️ VERIFICATION REQUIRED | üst karar |
| Q2 | hedef çekirdek (Q4) | UNKNOWN | ortam/prod okuması |
| Q3 | LOCK_EX ne kadar bekletiyor? | PLANNED | surucu §8 ölçüm protokolü |
| Q4 | LOCK_UN her yolda çağrılıyor mu? (E7) | ⚠️ VERIFICATION REQUIRED | tam dosya taraması |
| Q5 | gerçek syscall hangisi (§3.1)? | ⚠️ VERIFICATION REQUIRED | strace/ölçüm |
| Q6 | `usleep` backoff üstel mi? | ⚠️ VERIFICATION REQUIRED | tam dosya okuması |

**Kapanış kuralı:** Q1-Q2 üst karar/hedef-çekirdek ister; Q3 ölçüm ister;
Q4-Q6 kod taraması ister. Bu altı soru açık kaldığı sürece dosya `draft`
statüsünde kalır.

### §10.2 Değişiklik günlüğü (bu dosya)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0-§6) | eksik alt-dal derinleştirme görevi |
| 2026-10-13 | LOCK_EX grep 6 isabet kaydedildi | kanıt kapısı |
| 2026-10-13 | negatif kanıt kaydedildi (8 arama/0 isabet) | kanıt kapısı |
| 2026-10-13 | §7-§10 derinleştirme eklendi | dosya derinliği hedefi |

## §11 Komşu sınır matrisi — bu dosyanın bilinçli taşımadıkları

| Taşınmayan | Nereye ait | Neden taşınmadı |
|---|---|---|
| çekirdek içi kilit tasarımı | `kesme-ve-senkronizasyon-anayasa` | doktrin anayasada |
| kilit çekişme ölçümü | `kesme-ve-senkronizasyon-surucu` | ölçüm surucu'da |
| lockdep/locktorture kullanımı | `isletim-sistemi-testing` | test `-testing`'de |
| log kilidi telemetry | `isletim-sistemi-observability` | sayaç `-observability` |
| dosya kilidi güvenlik etkisi | `isletim-sistemi-security` | politika `-security`'de |
| test kodu (TS1-TS5) | `isletim-sistemi-testing` | kod `-testing`'de |

**Sınır kuralı:** Bu dosya yalnız **örnek kataloğu** üretir; ölçüm sonucu,
eşik ve test çıktısı **üretemez** (hepsi `UNKNOWN`/`PLANNED` işaretlidir).

### §11.1 Dosyanın izinli/yasaklı iddia türleri (tek tablo)

| İddia türü | Örnek | Durum |
|---|---|---|
| repo satırı (grep birebir) | `TemplateEngine.php:74` | ✅ izinli |
| olumsuz kanıt (grep 0) | N1-N8 | ✅ izinli |
| kavramsal köprü | LOCK_EX → fcntl/flock | ✅ izinli (kavramsal) |
| gerçek syscall değeri | "fcntl çağrılıyor" | ❌ ölçülmüş değil |
| kilit süresi | "X µs bekliyor" | ❌ ölçülmedi |
| eşik | "tolerans Y" | ❌ uydurma yasak |

**İzin kuralı:** Sağ sütundaki ❌ satırları **hiçbir zaman** yazılmaz;
yalnız gerçek ölçüm/strace ile ❌ → ✅ olur.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
