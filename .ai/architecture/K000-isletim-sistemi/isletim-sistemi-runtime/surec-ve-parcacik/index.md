---
node_id: "K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik"
node_type: "subdomain"
status: "draft"
parent_id: "K000-isletim-sistemi/isletim-sistemi-runtime"
children:
  - "K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik/anayasa"
  - "K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik/surucu"
  - "K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik/ornekler"
depends_on:
  - "isletim-sistemi-kernel"
  - "isletim-sistemi-core"
evidence:
  - "pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_join.html yanıt kodu 2026-10-13 (HTTP 200)"
  - "pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_detach.html yanıt kodu 2026-10-13 (HTTP 200)"
  - "man7.org/linux/man-pages/man3/exit.3.html yanıt kodu 2026-10-13 (HTTP 200)"
  - "shared/src/Cache/ApcuAdapter.php (APCu per-process bellek)"
  - "shared/composer.json (ext-apcu zorunlu bağımlılık)"
validation: "web: pthread_join · pthread_detach · exit(3) POSIX/man sayfaları HEAD ile 2026-10-13 doğrulandı (HTTP 200). disk: ApcuAdapter.php ve composer.json içeriği okundu."
open_questions:
  - "R2: Çok-iş-parçacıklı çalışma (pthreads) hedef mimaride var mı? Uygulama katmanında thread oluşturucu kod yok. [UNKNOWN]"
  - "R9: APCu per-process bellek modeli — çok-worker topolojide cache tutarlılığı nasıl sağlanıyor? Repo'da invalidation stratejisi görünmüyor. [⚠️ VERIFICATION REQUIRED]"
  - "R10: Süreç kuyruğu (background job / queue) hangi süreç modelinde çalışıyor? Repo'da ayrı worker process'i tanımlı değil. [UNKNOWN]"
group: mimari
---

# surec-ve-parcacik

> **Kapsam (tek cümle):** CoreMusic'in OS düzeyinde **süreç ve iş-parçacığı** ile olan
> sınırı — PHP'nin per-process bellek modeli (APCu), POSIX süreç başlatma/çıkış
> semantiği (fork/exec/exit) ve hedef mimaride pthread varlığı.

Bu alt-dal Kademe-3'tür; kök `isletim-sistemi-runtime` altındadır. Üç içerik dosyası
sınırsal (anayasa), yürütücü (surucu) ve kanıt (ornekler) katmanını taşır.

## İçerik Dosyaları

| Dosya | Grup | Ne içerir |
|---|---|---|
| [`anayasa.md`](anayasa.md) | mimari | Süreç/iş-parçacığı sınır kuralları, POSIX semantiği, kapsam dışı |
| [`surucu.md`](surucu.md) | mimari | APCu per-process modeli, composer bağımlılık kanıtı, çalıştırma akışı |
| [`ornekler.md`](ornekler.md) | kod | Gerçek repo kodu: `ApcuAdapter`, `composer.json`, `RuntimeBootstrap` boot |

## Katman Konumu

| Katman | Repo'da | OS düzeyi |
|---|---|---|
| Süreç modeli | ⚠️ dolaylı (Apache worker / IIS app-pool) | `fork(2)` / `CreateProcess` |
| İş-parçacığı | ❌ yok (R2 UNKNOWN) | `pthreads(7)` aday hedef |
| Per-process bellek | ✅ APCu (`ext-apcu`) | Paylaşımsız process belleği |

## Açık Sorular

R2 pthread varlığı · R9 APCu cache tutarlılığı · R10 arka-plan kuyruk süreci —
üçü de [UNKNOWN] / [⚠️ VERIFICATION REQUIRED]; ayrıntı `anayasa.md` §9.

## Komşu Sınır Matrisi

| Komşu | Paylaşılan sınır | Bu alt-dalın sahibi | Komşunun sahibi |
|---|---|---|---|
| `isletim-sistemi-core` | Süreç/parçacık yaşam döngüsü çağrısı | Runtime boot tetikleyicisi (`RuntimeBootstrap`) | `core` süreç tablosu / fork yönetimi |
| `isletim-sistemi-plan` | Zamanlayıcı / kuyruk sırası | Süreç kuyruğu boşaltma (drain) sinyali | `plan` zamanlayıcı / cron |
| `isletim-sistemi-api` | HTTP giriş kapısı (process başına istek) | Worker process döngüsü | `api` route/dispatch |
| `isletim-sistemi-security` | Süreç kimliği / yetki | Süreç çalışma kullanıcısı (least-privilege) | `security` auth / capability |
| `isletim-sistemi-observability` | Süreç log/ölçüm | Process stderr / error_log çıkışı | `observability` metrik toplayıcı |
| `isletim-sistemi-testing` | Test süreci sağlama | Test boot ortamı (process env) | `testing` PHPUnit harness |
| `K011-application` | Uygulama boot bağımlılığı | `RuntimeBootstrap` çağrısı | `K011` uygulama mantığı |
| `K018-devops` | Süreç modeli dağıtım hedefi | Worker/app-pool kapasitesi | `K18` deploy / süreç yöneticisi |

## Web Research

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| POSIX `pthread_join`, bir iş-parçacığının bitişini bekler ve kaynaklarını serbest bırakır; bu semantic, hedef mimaride pthread kullanılırsa geçerli olur (R2 iddiası). | `https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_join.html` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |
| POSIX `pthread_detach`, iş-parçağını beklemeden kaynaklarını ayırır; per-process bellek modeliyle (APCu) çakışan detach davranışı dikkat ister (R2/R9 iddiası). | `https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_detach.html` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |
| `exit(3)`, atexit temizleyicilerini çalıştırıp süreci sonlandırır; PHP `register_shutdown_function` bu semantiğin uygulama katmanı karşılığıdır (R10 iddiası). | `https://man7.org/linux/man-pages/man3/exit.3.html` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
