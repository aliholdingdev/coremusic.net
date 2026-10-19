---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/is-parcacigi-planlamasi
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-plan
children:
  - is-parcacigi-planlamasi-anayasa.md
  - is-parcacigi-planlamasi-surucu.md
  - is-parcacigi-planlamasi-ornekler.md
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-runtime
evidence:
  - "shared/src/Api/Registry/ServiceHealth.php (L24, L26) — 'parallel' yorumu tek süreç içinde"
  - "shared/tests/Repository/ApiKeyCreateCommandTest.php (L253) — proc_open ile alt süreç"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_atfork.html"
validation:
  - "2026-10-13: index 40–100 · 3 içerik dosyası ≥500 satır · envanter kesişimi 0"
open_questions:
  - "P-1: hedef mimaride pthread/parçacık kullanımı var mı? Repo'da thread oluşturucu kod yok. [UNKNOWN]"
  - "Plan1: parçacık planlaması içeriği kernel/runtime'da mı, burada mı? [UNKNOWN]"
group: mimari
---

# is-parcacigi-planlamasi — Deep-Dal İndeksi (Kademe-4)

## §0 — ELI15: bu alt-dal tek cümlede

> **Bir otobüs düşün:** otobüs = CPU, yolcular = işler. "Süreç" (process) ayrı koltuklu yolcudur
> (bellek uzayı korunur), "iş parçacığı" (thread) aynı koltuğu paylaşır. Bu alt-dal şu soruyu
> yanıtlar: **bu yolcular koltuğu nasıl paylaşır, birbirini nasıl bekler, kilitlenince ne olur?**

| Kavram | Otobüs karşılığı | OS karşılığı |
|---|---|---|
| Ayrı koltuk | kendi bellek uzayı | process (adres uzayı) |
| Aynı koltuk paylaşımı | ortak bellek uzayı | thread (aynı process içinde) |
| Kilitlenme | ikisi de "önce sen" deyip beklemek | deadlock |
| Koltuk devri | muavinin öncelik vermesi | priority inheritance |

## §1 — Amaç

| # | Amaç | Bu alt-daldaki karşılığı | Kapanış ölçütü |
|---|---|---|---|
| A-1 | süreç ile iş parçacığını ayırmak | bellek uzayı, dayanıklılık, fork semantiği | `-anayasa §2` |
| A-2 | kilitlenmeyi (deadlock) koşul ve önlemiyle tanımlamak | dört gerekli koşul, önleme/tespit/kurtarma | `-anayasa §3` · `-surucu §7.1` |
| A-3 | senkronizasyon araçlarını çerçeve düzeyinde tutmak | mutex · condition variable · semaphore · spinlock | `-anayasa §6` |
| A-4 | kaynak kilitlerini sıraya bağlamak | lock ordering, tek kaynaklı sıralama | `-anayasa §3.4` |
| A-5 | öncelik devrini (priority inheritance) işlemek | priority inversion ve devir kuralı | `-surucu §1.2` |
| A-6 | uygulama katmanı ile OS katmanını ayırmak | repo'da parçacık API'si yokluğu dürüstlük defteri | `-ornekler §5` |

## §2 — Kademe ve rol

| Alan | Değer |
|---|---|
| Kademe | Kademe-3 klasör · dosyalar Kademe-4 |
| Ebeveyn | `K000-isletim-sistemi/isletim-sistemi-plan` (Kademe-3) |
| Eksen sorusu | iş birimi nasıl kurulur, çekirdeğe nasıl oturtulur? |
| Komşu sorumluluk | mekanizma → `isletim-sistemi-kernel` · yürütme → `isletim-sistemi-runtime` |
| Kapsam dışı | uygulama yol haritası (`isletim-sistemi-plan-kapsam.md §A.2 B1`) |

## §3 — Dosya envanteri (3 dosya, Kademe-4)

| # | Dosya | Bölüm dizisi | Ne verir |
|---|---|---|---|
| 1 | [`is-parcacigi-planlamasi-anayasa.md`](is-parcacigi-planlamasi-anayasa.md) | §0–§9 | ELI15, süreç vs parçacık ayrımı, kilitlenme koşulları, senkronizasyon çerçevesi, ölçüm/eşik, komşu sınırı, terimler, kapsam dışı, açık sorular, kaynakça |
| 2 | [`is-parcacigi-planlamasi-surucu.md`](is-parcacigi-planlamasi-surucu.md) | §0–§8 | öncelik yüzeyi, uygunluk kümesi, RLIMIT, fork guard, havuz seçimi, hata modları, test, kaynakça |
| 3 | [`is-parcacigi-planlamasi-ornekler.md`](is-parcacigi-planlamasi-ornekler.md) | §0–§6 | çalışan yapılandırmalar, gerçek repo kodu, anti-pattern kataloğu, eşik/kabul kriteri, dürüstlük defteri ve referanslar |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `is-parcacigi-planlamasi-anayasa.md` | kavram ve sınır kuralları | Kademe-4 |
| mimari | `is-parcacigi-planlamasi-surucu.md` | uygulama akışı, hata modu ve ölçüm | Kademe-4 |
| kod | `is-parcacigi-planlamasi-ornekler.md` | gerçek repo kanıtı, anti-pattern ve eşik tabloları | Kademe-4 |

## §5 — Komşu sınırı

| Komşu | Bu alt-dalın ona bıraktığı | Almadığı | Ayırıcı çizgi |
|---|---|---|---|
| `isletim-sistemi-core` | süreç/parçacık kavram iskeleti | süreç tablosu ve yaşam döngüsü mekanizması | kavram (bu) vs tablo (core) |
| `isletim-sistemi-kernel` | kilit ve kuyruk **politikası** | mutex/rb-tree/wakeup iç mekanizması | ne (bu) vs nasıl (kernel) |
| `isletim-sistemi-runtime` | parçacık API'sinin çağrılmayacağı sınır | `pthread_*`/`epoll` çağrı sözleşmesi | çerçeve (bu) vs yürütme (runtime) |
| `zamanlama-scheduler` | öncelik devrinin zamanlama politikasına bağı | sıralama algoritması | kilit (bu) vs sıra (plan) |
| `isletim-sistemi-security` | parçacık yetkisi ihtiyacının tanımı | capability/rol modeli | ihtiyaç (bu) vs model (security) |
| `isletim-sistemi-observability` | deadlock/livelock ölçüm adlarının listesi | metrik boru hattı | okuma (bu) vs toplama (observability) |
| `isletim-sistemi-testing` | kilit senaryosu ve eşikleri | test kodu, CI kapısı | senaryo (bu) vs kod (testing) |
| `isletim-sistemi-audio` | RT parçacığında kilit kullanmama kuralının ihtiyacı | ring buffer/ASIO tasarımı | kural (bu) vs zincir (audio) |

## §7 — Açık sorular

| # | Soru | Durum | Kapanış koşulu |
|---|---|---|---|
| P-1 | Hedef mimaride `pthreads`/parçacık kullanımı var mı? Repo'da thread oluşturucu kod yok. | **[UNKNOWN]** | hedef mimari kararının yazılması — `-anayasa §8` |
| P-2 | Kilitlenme tespiti üretime açık mı (watchdog / timeout)? | **[UNKNOWN]** | `-surucu §7.1` hata modu kararının üst otoritece onayı |
| Plan1 | Parçacık planlaması içeriği `kernel`/`runtime` içinde mi, burada mı? | **[UNKNOWN]** | komşu dosyaların okunup sınırın teyidi |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
