---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/isletim-sistemi-plan-kapsam
node_type: module
status: verify-required
parent_id: K000-isletim-sistemi/isletim-sistemi-plan
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-runtime
evidence:
  - "https://docs.kernel.org/scheduler/index.html"
  - "https://man7.org/linux/man-pages/man7/pthreads.7.html"
  - "https://man7.org/linux/man-pages/man7/capabilities.7.html"
  - "G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md §2.1"
  - "home.coremusic.net/AGENTS.md §1 (Raspberry Pi 5 hedefi)"
validation:
  - "2026-10-13 kapsam karar kaydı: bu tur scheduling ekseninde dallandırıldı; Q3 açık"
  - "2026-10-13 alt-dal dosya adları disk enumerasyonuyla hizalandı (anayasa/surucu/ornekler)"
open_questions:
  - "Q3: kapsam = OS planlama mı, uygulama yol haritası mı? [UNKNOWN]"
  - "Plan1: scheduling içeriği kernel/runtime'da mı, burada mı? [UNKNOWN]"
group: mimari
---

# isletim-sistemi-plan — Kapsam

> **Tek cümle:** Bu dosya `isletim-sistemi-plan` subdomaininin **sınırını** çizer — neyi içerir, neyi içermez, hangi alt-dallara bölündü, hangi düğümlere bağımlıdır ve hangi sorular **kapatılmadan** bırakılmıştır. Sınır, içerik değildir; içerik alt-dallarda durur.

## §A — Kapsam

### §A.1 Kapsam içi (bu subdomainin taşıdığı)

| # | Kapsam maddesi | Açıklama | Kanıt durumu |
|---|---|---|---|
| A1 | OS planlama (scheduling) çerçevesi | hangi işlemin, hangi öncelikle, ne zaman CPU aldığına dair **politika çerçevesi** | aday kaynaklarla destekli (bkz. §A.3) |
| A2 | İş parçası planlaması | eşzamanlı birimin seçimi, çekirdek atama, bloklayan/non-bloklayan ayrımı | aday kaynaklarla destekli |
| A3 | Kaynak dağılımı | CPU/bellek/IO kotaları, rezervasyon, üst sınır ve aşım davranışı | aday kaynaklarla destekli |
| A4 | Alt-dal bağımlılık ve sınır kaydı | üç alt-dalın birbirine ve `kernel`/`runtime` komşularına çizdiği çizgi | disk kanıtı (bu dosya §B, §D, §E) |
| A5 | Açık soru defteri | Q3 ve Plan1'in kapanış koşulları | disk kanıtı (§F) |
| A6 | Doğrulama ve örnek köprüsü | iddia → URL → tarih → HTTP durumu zinciri | `isletim-sistemi-plan-dogrulama.md` · `isletim-sistemi-plan-ornekler.md` |

### §A.2 Kapsam DIŞI (bu subdomaine yazılmaz)

| # | Kapsam dışı madde | Nereye gider | Gerekçe |
|---|---|---|---|
| B1 | **Uygulama yol haritası / roadmap** (faz, sprint, milestone, release planı) | Vault'taki proje planı dosyaları — `.ai/PROJECTS.md` · `.ai/WORKFLOW.md` | Q3 henüz `[UNKNOWN]`; "plan" kelimesinin roadmap yorumu **kapsam dışıdır** ve bu turda bilinçli olarak seçilmedi |
| B2 | Çekirdek yürütme mekanizması (zamanlayıcı sınıfı çekirdeği, kuyruk yapısı, wakeup yolu) | `isletim-sistemi-kernel` | `depends_on` yönü: plan → kernel; mekanizma değil, çerçeve burada |
| B3 | Çalışma zamanı (süreç/iş parçası ömrü, kilit, çağrı sözleşmesi, geri çağırma) | `isletim-sistemi-runtime` | yürütme ayrı bir subdomain; burada yalnızca planlama etkisi anlatılır |
| B4 | Yetki modeli tanımı (capability seti, rol, ACL) | `isletim-sistemi-core` · `isletim-sistemi-security` | kaynak dağılımı yetkiyi **kullanır**, tanımlamaz |
| B5 | Donanım topolojisi (çekirdek sayısı, big.LITTLE, bellek denetleyicisi) | `K001-donanım` (vault) | hedef cihaz ipucu alınır, donanım tasarımı alınmaz |
| B6 | Ses/DSP gerçek zamanlı zinciri (ASIO, ring buffer, callback) | `isletim-sistemi-audio` | bu subdomain yalnızca RT **zamanlama sınıfının anlamı** ile ilgilenir |
| B7 | Gözlemlenebilirlik (metrik toplama, log şeması, alarm eşikleri) | `isletim-sistemi-observability` | §C'deki ölçüm maddeleri yalnızca **doğrulama amaçlı** okumalardır |
| B8 | Test altyapısı (koşu ortamı, fixture, CI kapısı) | `isletim-sistemi-testing` | `isletim-sistemi-plan-dogrulama.md §B` test **stratejisi** taşır, test kodu değil |
| B9 | OS dağıtım/kurulum (systemd unit, init script, bootloader) | `isletim-sistemi-turu` | burada yalnız zamanlama **politikası** anlatılır; kurulum yolu alınmaz |

### §A.3 Kapsam içi maddelerin aday dayanakları

| Madde | Dayanak | Kaynak | Durum |
|---|---|---|---|
| A1 | Linux çekirdek zamanlama dokümantasyonu | https://docs.kernel.org/scheduler/index.html | HTTP 200 (2026-10-12) |
| A2 | İş parçası arayüzü tanımı | https://man7.org/linux/man-pages/man7/pthreads.7.html | HTTP 200 (2026-10-12) |
| A3 | Yetki ile öncelik sınırı | https://man7.org/linux/man-pages/man7/capabilities.7.html | HTTP 200 (2026-10-12) |
| A4 | Ağaç konumu | v2 `coremusic-katmanli-mimari-agaci-v2.md §2.1` | disk okuma |
| A5 | Hedef donanım | `home.coremusic.net/AGENTS.md §1` | disk okuma — "Raspberry Pi 5 üzerinde çalışan ana medya paneli" |

### §A.4 Kapsam karar kuralları (bu dosya geçerlilik mantığı)

| Kural | Koşul | Sonuç |
|---|---|---|
| K1 | Bir madde `B1..B9` listesinden birine geliyorsa | bu subdomaine yazılmaz, sahibi subdomain'e yönlendirilir |
| K2 | Bir madde `A1..A6` ile çakışıyorsa | alt-dal dosyasına yazılır; kök dosyalara yalnızca **sınır** yazılır |
| K3 | Q3 "roadmap" sonucuna varırsa | `A1..A3` kapsam dışı ilan edilir, bu dosya yeniden yazılır, `B1` kaldırılır |
| K4 | Q3 "OS planlama" sonucuna varırsa | `A1..A3` kesinleşir, `status: verify-required` → `draft`/`verified` |
| K5 | Q3 cevabı gelmeden bir dosya `B1..B9` içeriyorsa | ihlal sayılır; dosya `⚠️ VERIFICATION REQUIRED` ile düzeltilir |
| K6 | Alt-dal ile kök çelişirse | kök `index.md` ve bu dosya kazanır; alt-dal revize edilir |
| K7 | Alt-dal dosya adı disk enumerasyonuyla uyuşmaz | kök `index.md` `children` alanı güncellenir, alt-dal beklemez |

### §A.5 Sınır testi (tek satır soru)

| Soru | Evet ise | Hayır ise |
|---|---|---|
| Bu cümle OS'de çalışan bir işlemin CPU hakkını anlatıyor mu? | kapsam içi (A1–A3) | kapsam dışı |
| Bu cümle bir sprint/faz/tarih veriyor mu? | kapsam dışı (B1) | kapsam içi muhtemel |
| Bu cümle çekirdeğin iç veri yapısını tarif ediyor mu? | kapsam dışı (B2) | kapsam içi çerçeve olabilir |
| Bu cümle bir capability/tokens/ACL tanımı içeriyor mu? | kapsam dışı (B4) | devam |
| Bu cümle systemd unit / init script yazıyor mu? | kapsam dışı (B9) | devam |

### §A.6 Kapsam dışı maddelerin sinyal kelimeleri

| Madde | Sinyal kelime / desen | Karşı örnek (kapsam içi) |
|---|---|---|
| B1 roadmap | "faz", "sprint", "milestone", "tarih planı", "yol haritası", "v1/v2 planı" | "politika çerçevesi", "eşik" |
| B2 çekirdek yürütme | "wakeup yolu", "kuyruk düğümü", "zamanlayıcı sınıfı iç yapısı" | "politika seçimi" |
| B3 çalışma zamanı | "çözümleme", "locks API", "çagri sözleşmesi" | "b bloklayan/non-bloklayan ayrımı" |
| B4 yetki modeli | "capability seti", "rol matrisi", "ACL girişi" | "yetki ile sınırlandırma ihtiyacı" |
| B5 donanım | "çekirdek sayısı çizimi", "bellek denetleyicisi", "PCB" | "hedef cihaz ipucu" |
| B6 ses zinciri | "ring buffer", "ASIO callback", "buffer boyutu" | "RT zamanlama sınıfının anlamı" |
| B7 gözlemlenebilirlik | "metrik adı", "log şeması", "alarm kanalı" | "ölçüm adımı" |
| B8 test altyapısı | "fixture", "test kodu", "CI kapısı" | "doğrulama senaryosu" |
| B9 kurulum | "systemd unit", "init script", "bootloader" | "politika parametresi" |

### §A.7 Kapsam cümlesi (birebir kullanılacak)

> "`isletim-sistemi-plan`, OS planlama (scheduling) ekseninde, `kernel` ve `runtime` komşularına bağımlı, **uygulama yol haritası kapsam dışı** bir alt-dır; üç alt-dalı (`zamanlama-scheduler`, `is-parcacigi-planlamasi`, `kaynak-dagilimi`) çerçeveler, mekanizmayı değil."

## §B — 3 alt-dal envanteri

### §B.1 Alt-dal kökleri

| Alt-dal | Kademe | Dosya sayısı | Eksen sorusu | `group` |
|---|---|---|---|---|
| `zamanlama-scheduler/` | Kademe-3 klasör | 1 index + 3 içerik = 4 | hangi iş, hangi sırayla CPU alır? | mimari (index + anayasa + surucu) · kod (ornekler) |
| `is-parcacigi-planlamasi/` | Kademe-3 klasör | 1 index + 3 içerik = 4 | iş birimi nasıl kurulur, çekirdeğe nasıl oturtulur? | mimari (index + anayasa + surucu) · kod (ornekler) |
| `kaynak-dagilimi/` | Kademe-3 klasör | 1 index + 3 içerik = 4 | kaynak kime, ne kadar, ne zaman verilir? | mimari (index + anayasa + surucu) · kod (ornekler) |

### §B.2 `zamanlama-scheduler/` dosya envanteri

| Dosya | Bölüm dizisi | Ne verir | Komşuya bıraktığı |
|---|---|---|---|
| `index.md` | künye + çocuk kataloğu + komşu sınır matrisi | alt-dalın 3 dosyası ve bağımlılıkları | — |
| `zamanlama-scheduler-anayasa.md` | §0–§9 | ELI15, zamanlamanın amacı, politika ailesi özeti, komşu sınırı, terimler, açık sorular | çekirdek mekanizması → `-kernel`, yürütme → `-runtime` |
| `zamanlama-scheduler-surucu.md` | §0–§10 | round-robin · CFS · priority · EDF, preemptif/işbirlikçi, quantum/tick, SCHED_FIFO/RR/DEADLINE, starvation/fairness, ölçüm, riskler | politika çerçevesi |
| `zamanlama-scheduler-ornekler.md` | §0–§6 | gerçek repo kodu, ölçüm örnekleri, kötü uygulama, politika karşılaştırması | uygulama örneği → `-kernel`/`-runtime` |

### §B.3 `is-parcacigi-planlamasi/` dosya envanteri

| Dosya | Bölüm dizisi | Ne verir | Komşuya bıraktığı |
|---|---|---|---|
| `index.md` | künye + çocuk kataloğu + komşu sınır matrisi | alt-dalın 3 dosyası ve bağımlılıkları | — |
| `is-parcacigi-planlamasi-anayasa.md` | §0–§9 | ELI15, thread vs process vs coroutine, durum diyagramı, kullanıcı/kernel parçacığı, affinity, fiber/async çalışma zamanı | süreç/thread mekanizması → `-runtime`, çekirdek → `-kernel` |
| `is-parcacigi-planlamasi-surucu.md` | §0–§8 | durum makinesi ayrıntısı, bağışıklama (affinity) çağrıları, fiber/async çalışma zamanı sınırları, ölçüm | çağrı sözleşmesi → `-runtime` |
| `is-parcacigi-planlamasi-ornekler.md` | §0–§6 | PHP-FPM worker / event loop gerçek kanıtı, `proc_open` test kanıtı, affinity ölçüm örneği | uygulama örneği → `-runtime` |

### §B.4 `kaynak-dagilimi/` dosya envanteri

| Dosya | Bölüm dizisi | Ne verir | Komşuya bıraktığı |
|---|---|---|---|
| `index.md` | künye + çocuk kataloğu + komşu sınır matrisi | alt-dalın 3 dosyası ve bağımlılıkları | — |
| `kaynak-dagilimi-anayasa.md` | §0–§9 | ELI15, CPU çekirdeği/paylaşım, bellek atribütleri (NUMA · cgroup), G/Ç kuyrukları, priority inheritance, ölçüm | izin → `-core`/`-security`, cihaz → `K001-donanım` |
| `kaynak-dagilimi-surucu.md` | §0–§7 | cgroup/NUMA IO kuyruğu ayrıntısı, priority inheritance zinciri, utilization/run queue ölçümü | kuyruk disiplini uygulaması → `-kernel` |
| `kaynak-dagilimi-ornekler.md` | §0–§6 | gerçek repo kodu (cache TTL, rate limit window, retryAfter), kota örneği, kötü uygulama | uygulama örneği → `-kernel` |

### §B.5 Envanter sayımı

| Sayım | Değer | Ölçüm yöntemi |
|---|---|---|
| Kök dosya (index + 3 belge) | 4 | disk enumerasyonu, 2026-10-13 |
| Alt-dal klasörü | 3 | disk enumerasyonu |
| Alt-dal index dosyası | 3 | `index.md` × 3 klasör |
| Alt-dal içerik dosyası | 9 | 3 × 3 |
| Toplam bu tur üretilen | 16 (4 kök revizyon + 12 yeni) | 4 kök: 1 daraltma + 3 büyütme |
| ≥500 satır hedefi | 12 dosya (3 kök içerik + 9 alt-dal içerik) | `Get-Content ... .Count` |

### §B.6 Alt-dal bölüm indeksi (9 içerik dosyasının tam § dizisi)

| # | Dosya | § | Bölüm |
|---|---|---|---|
| 1 | `zamanlama-scheduler-anayasa.md` | §0 | ELI15 — zamanlama tek cümlede |
| 2 | `zamanlama-scheduler-anayasa.md` | §1 | Kimlik ve konum (node_id, parent, group) |
| 3 | `zamanlama-scheduler-anayasa.md` | §2 | Zamanlamanın amacı (adalet, gecikme, verim, öngörülebilirlik) |
| 4 | `zamanlama-scheduler-anayasa.md` | §3 | Politika ailesi özeti (RR · CFS · priority · EDF) |
| 5 | `zamanlama-scheduler-anayasa.md` | §4 | Komşu sınırı: çekirdek → `-kernel`, yürütme → `-runtime` |
| 6 | `zamanlama-scheduler-anayasa.md` | §5 | Terim sözlüğü |
| 7 | `zamanlama-scheduler-anayasa.md` | §6 | Kapsam dışı |
| 8 | `zamanlama-scheduler-anayasa.md` | §7 | Açık sorular (Plan1) |
| 9 | `zamanlama-scheduler-anayasa.md` | §8 | Raspberry Pi 5 hedefi notu |
| 10 | `zamanlama-scheduler-anayasa.md` | §9 | Kaynakça |
| 11 | `zamanlama-scheduler-surucu.md` | §0 | ELI15 |
| 12 | `zamanlama-scheduler-surucu.md` | §1 | Zamanlama algoritmaları (round-robin · CFS · priority · EDF) |
| 13 | `zamanlama-scheduler-surucu.md` | §2 | Preemptif vs işbirlikçi |
| 14 | `zamanlama-scheduler-surucu.md` | §3 | Quantum / tick |
| 15 | `zamanlama-scheduler-surucu.md` | §4 | Gerçek zamanlı (SCHED_FIFO · RR · DEADLINE) |
| 16 | `zamanlama-scheduler-surucu.md` | §5 | Starvation / fairness |
| 17 | `zamanlama-scheduler-surucu.md` | §6 | Ölçüm (context switch latency · jitter) |
| 18 | `zamanlama-scheduler-surucu.md` | §7 | Repo disk kanıtları |
| 19 | `zamanlama-scheduler-surucu.md` | §8 | Riskler |
| 20 | `zamanlama-scheduler-surucu.md` | §9 | Kapsam dışı |
| 21 | `zamanlama-scheduler-surucu.md` | §10 | Kaynakça |
| 22 | `zamanlama-scheduler-ornekler.md` | §0 | ELI15 |
| 23 | `zamanlama-scheduler-ornekler.md` | §1 | Gerçek repo kodu |
| 24 | `zamanlama-scheduler-ornekler.md` | §2 | Ölçüm örnekleri |
| 25 | `zamanlama-scheduler-ornekler.md` | §3 | Kötü uygulama kataloğu |
| 26 | `zamanlama-scheduler-ornekler.md` | §4 | Politika karşılaştırma matrisi |
| 27 | `zamanlama-scheduler-ornekler.md` | §5 | Repo'da olmayan (dürüstlük) |
| 28 | `zamanlama-scheduler-ornekler.md` | §6 | Kaynakça |
| 29 | `is-parcacigi-planlamasi-anayasa.md` | §0 | ELI15 |
| 30 | `is-parcacigi-planlamasi-anayasa.md` | §1 | Kimlik ve konum |
| 31 | `is-parcacigi-planlamasi-anayasa.md` | §2 | Thread vs process vs coroutine |
| 32 | `is-parcacigi-planlamasi-anayasa.md` | §3 | Parçacık durum diyagramı |
| 33 | `is-parcacigi-planlamasi-anayasa.md` | §4 | Kullanıcı / kernel parçacığı |
| 34 | `is-parcacigi-planlamasi-anayasa.md` | §5 | CPU bağışıklama (affinity) |
| 35 | `is-parcacigi-planlamasi-anayasa.md` | §6 | Fiber / async çalışma zamanı |
| 36 | `is-parcacigi-planlamasi-anayasa.md` | §7 | Komşu sınırı |
| 37 | `is-parcacigi-planlamasi-anayasa.md` | §8 | Açık sorular |
| 38 | `is-parcacigi-planlamasi-anayasa.md` | §9 | Kaynakça |
| 39 | `is-parcacigi-planlamasi-surucu.md` | §0 | ELI15 |
| 40 | `is-parcacigi-planlamasi-surucu.md` | §1 | Durum makinesi ayrıntısı |
| 41 | `is-parcacigi-planlamasi-surucu.md` | §2 | Kullanıcı/kernel sınır geçişi |
| 42 | `is-parcacigi-planlamasi-surucu.md` | §3 | Affinity çağrıları (Linux · POSIX · Windows) |
| 43 | `is-parcacigi-planlamasi-surucu.md` | §4 | Fiber / async çalışma zamanı sınırları |
| 44 | `is-parcacigi-planlamasi-surucu.md` | §5 | Ölçüm |
| 45 | `is-parcacigi-planlamasi-surucu.md` | §6 | Repo disk kanıtları |
| 46 | `is-parcacigi-planlamasi-surucu.md` | §7 | Riskler |
| 47 | `is-parcacigi-planlamasi-surucu.md` | §8 | Kaynakça |
| 48 | `is-parcacigi-planlamasi-ornekler.md` | §0 | ELI15 |
| 49 | `is-parcacigi-planlamasi-ornekler.md` | §1 | PHP-FPM worker / event loop gerçek kanıtı |
| 50 | `is-parcacigi-planlamasi-ornekler.md` | §2 | `proc_open` ile alt süreç kanıtı |
| 51 | `is-parcacigi-planlamasi-ornekler.md` | §3 | Affinity ölçüm örneği |
| 52 | `is-parcacigi-planlamasi-ornekler.md` | §4 | Kötü uygulama kataloğu |
| 53 | `is-parcacigi-planlamasi-ornekler.md` | §5 | Repo'da olmayan (dürüstlük) |
| 54 | `is-parcacigi-planlamasi-ornekler.md` | §6 | Kaynakça |
| 55 | `kaynak-dagilimi-anayasa.md` | §0 | ELI15 |
| 56 | `kaynak-dagilimi-anayasa.md` | §1 | Kimlik ve konum |
| 57 | `kaynak-dagilimi-anayasa.md` | §2 | CPU çekirdeği / paylaşım |
| 58 | `kaynak-dagilimi-anayasa.md` | §3 | Bellek atribütleri (NUMA · cgroup) |
| 59 | `kaynak-dagilimi-anayasa.md` | §4 | G/Ç kuyrukları |
| 60 | `kaynak-dagilimi-anayasa.md` | §5 | Öncelik devralma (priority inheritance) |
| 61 | `kaynak-dagilimi-anayasa.md` | §6 | Ölçüm (utilization · run queue) |
| 62 | `kaynak-dagilimi-anayasa.md` | §7 | Komşu sınırı |
| 63 | `kaynak-dagilimi-anayasa.md` | §8 | Açık sorular |
| 64 | `kaynak-dagilimi-anayasa.md` | §9 | Kaynakça |
| 65 | `kaynak-dagilimi-surucu.md` | §0 | ELI15 |
| 66 | `kaynak-dagilimi-surucu.md` | §1 | cgroup kota ayrıntısı |
| 67 | `kaynak-dagilimi-surucu.md` | §2 | NUMA atribütleri ve yerleşim |
| 68 | `kaynak-dagilimi-surucu.md` | §3 | IO kuyruğu disiplinleri |
| 69 | `kaynak-dagilimi-surucu.md` | §4 | Priority inheritance zinciri |
| 70 | `kaynak-dagilimi-surucu.md` | §5 | Ölçüm (utilization · run queue) |
| 71 | `kaynak-dagilimi-surucu.md` | §6 | Repo disk kanıtları |
| 72 | `kaynak-dagilimi-surucu.md` | §7 | Kaynakça |
| 73 | `kaynak-dagilimi-ornekler.md` | §0 | ELI15 |
| 74 | `kaynak-dagilimi-ornekler.md` | §1 | Gerçek repo kodu (cache TTL · rate limit · retryAfter) |
| 75 | `kaynak-dagilimi-ornekler.md` | §2 | Kota örneği |
| 76 | `kaynak-dagilimi-ornekler.md` | §3 | Kötü uygulama kataloğu |
| 77 | `kaynak-dagilimi-ornekler.md` | §4 | Aşım davranışı matrisi |
| 78 | `kaynak-dagilimi-ornekler.md` | §5 | Repo'da olmayan (dürüstlük) |
| 79 | `kaynak-dagilimi-ornekler.md` | §6 | Kaynakça |

### §B.7 Alt-dal çapraz referans matrisi

| Kaynak dosya | `zamanlama-scheduler`e atıf | `is-parcacigi-planlamasi`ye atıf | `kaynak-dagilimi`ye atıf |
|---|---|---|---|
| `zamanlama-scheduler-anayasa.md` | — (kendisi) | iş birimi seçilir, politika uygulanır | öncelik sınıfı kota ile birlikte okunur |
| `zamanlama-scheduler-surucu.md` | — (kendisi) | Round Robin kuantum süresi iş parçası sayısına bağlı | `nice` seviyeleri politika girdisidir |
| `zamanlama-scheduler-ornekler.md` | — (kendisi) | thread/alt süreç testi ayrıntılı iş parçası kanıtı verir | rate-limit penceresi kuyruk disiplini örneğidir |
| `is-parcacigi-planlamasi-anayasa.md` | politika, birim üzerinden uygulanır | — (kendisi) | eşzamanlı birim sayısı kota kullanır |
| `is-parcacigi-planlamasi-surucu.md` | durum geçişi zamanlama etkisidir | — (kendisi) | blok bekleme kaynak tüketir |
| `is-parcacigi-planlamasi-ornekler.md` | worker örneği politika girdisi verir | — (kendisi) | worker başına bellek kotası |
| `kaynak-dagilimi-anayasa.md` | kota, politika girdisidir | kota, iş birimi başına tanımlanır | — (kendisi) |
| `kaynak-dagilimi-surucu.md` | run queue ölçümü politika ölçümüdür | kuyrukta bekleyen birim sayısı | — (kendisi) |
| `kaynak-dagilimi-ornekler.md` | rate-limit penceresi kuyruk disiplinidir | worker TTL = birim ömrü | — (kendisi) |

## §C — Kapsam karar kaydı (Q3 neden scheduling eksenine alındı)

| # | Dayanak | Ne söylediği | Karara katkısı |
|---|---|---|---|
| C1 | `index.md` `depends_on` alanı | `[isletim-sistemi-kernel, isletim-sistemi-runtime]` | iki komşu da "yürütme" subdomaini; kök zaten yürütme çerçevesine bakıyor |
| C2 | `evidence[0]` | `docs.kernel.org/scheduler/index.html` | kanıt birinci sırada **zamanlayıcı** dokümanı |
| C3 | `evidence[1]` | `man7.org/.../pthreads.7.html` | kanıt **iş parçası** arayüzü |
| C4 | `evidence[2]` | `man7.org/.../capabilities.7.html` | kanıt **yetki ile öncelik sınırı** |
| C5 | `evidence[4]` | `home.coremusic.net/AGENTS.md §1` | hedef cihaz Raspberry Pi 5 — gerçek zamanlı/örtük çekirdek konularını pratik yapar |
| C6 | v2 §2.1 | yalnız düğüm adı, tanım yok | kapsam kesinleştirilemez → dallanma karar değil, **yer tutma** olur |

### §C.1 Seçenek matrisi

| Seçenek | Ne anlama gelir | Bu turda | Gerekçe |
|---|---|---|---|
| Y1 — OS planlama (scheduling) | CPU zaman paylaşımı, öncelik, kuyruk | **SEÇİLDİ (yer tutucu)** | C1–C5 kanıtların %100'ü bu eksende |
| Y2 — Uygulama yol haritası (roadmap) | faz, sprint, tarih planı | SEÇİLMEDİ — kapsam dışı | kanıt listesinde tek bir tarih/faz kanıtı yok |
| Y3 — İkisi birden | hem politika hem yol haritası | REDDEDİLDİ | Q3 tek cevap ister; iki ekseni aynı düğümde tutmak sınırı belirsizleştirir |
| Y4 — Beklemek (hiç dallanmamak) | dosya 60/52/47/47 satırda kalır | REDDEDİLDİ | görev 16 dosya ve ≥500 satır öngörüyor; içerik yine de Y1 kanıtı üzerine kuruldu |

### §C.2 Kararın neyi **kapatmadığı**

| Soru | Bu kararla kapanır mı? | Neden |
|---|---|---|
| Q3 kapsam tanımı | **HAYIR** | Y1, kanıtla **desteklenmiş en olası yorumdur**; kesin tanım üst otoritedir |
| Plan1 içerik yeri | **HAYIR** | `kernel`/`runtime` içeriği bu turda okunmadı |
| `status: verify-required` | **HAYIR** | Q3 açık kaldığı için `verify-required` korunur |
| Alt-dalların varlığı | **EVET** | 3 klasör disktedir, sayılabilir |

### §C.3 Karar metni (birebir kullanılacak cümle)

> "Bu tur OS planlama (scheduling) ekseninde dallandırıldı; dayanak `depends_on: [kernel, runtime]` + kanıt üçlüsü `scheduler · pthreads · capabilities`. **Q3 kapsam sorusu açık kalır**; uygulama yol haritası kapsam dışıdır."

### §C.4 Q3 sonuçlarına göre eylem tablosu

| Q3 sonucu | Kapsam dosyası | Alt-dal dosyaları | `status` |
|---|---|---|---|
| Y1 (OS planlama) | §A.1 kesinleşir, §A.2 `B1` satırı kalır | üç alt-dal aynen geçerli | `verify-required` → `draft` |
| Y2 (roadmap) | §A.1 tamamen yeniden yazılır | üç alt-dal kapsam dışı ilan edilir, silinmez işaretlenir | `verify-required` kalır |
| Cevap yok | mevcut hali geçerli | mevcut hali geçerli (yer tutucu) | `verify-required` kalır |

### §C.5 Bu tur okunan ve okunmayan kaynaklar

| Kaynak | Durum | Not |
|---|---|---|
| `isletim-sistemi-plan/index.md` | OKUNDU | `depends_on` + `evidence` kanıt dayanağı |
| `isletim-sistemi-plan-kapsam.md` (önceki hali) | OKUNDU | 459 satır, Q3/Plan1 `[UNKNOWN]` |
| `isletim-sistemi-plan-dogrulama.md` (önceki hali) | OKUNDU | 47 satır, web 5/5 + disk 3/3 |
| `isletim-sistemi-plan-ornekler.md` (önceki hali) | OKUNDU | 47 satır, 3 örnek — 2 adedi disk'te doğrulanamadı (bkz. §G.3) |
| `home.coremusic.net/AGENTS.md §1` | OKUNDU | Raspberry Pi 5 hedefi |
| `shared/src/Bootstrap/RuntimeBootstrap.php` | OKUNDU | çalışma zamanı boot kanıtı |
| `shared/src/Api/Registry/ServiceHealth.php` · `ServiceDefinition.php` | OKUNDU | "parallel health check" + timeout kanıtı |
| `shared/src/AI/AIOrchestrator.php` | OKUNDU | öncelik kuyruğu (priority queue) kanıtı |
| `shared/src/Exception/RateLimitException.php` | OKUNDU | `retryAfter` kanıtı |
| `home.coremusic.net/config/bootstrap.php` | OKUNDU | boot sırası + `/health` erken çıkış kanıtı |
| `shared/src/Middleware/MiddlewarePipeline.php` | OKUNDU | `hrtime` ile süre ölçümü kanıtı |
| `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-kernel/**` | OKUNMADI | Plan1 kapanış kaynağı — sonraki tur |
| `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-runtime/**` | OKUNMADI | Plan1 kapanış kaynağı — sonraki tur |
| `K001` donanım vault'u | OKUNMADI | B5 kapsam dışı — bu tur gerekmedi |
| v2 ağacının tam metni | KISMEN | yalnız §2.1 adı referans edildi |

## §D — Bağımlılık grafiği (plan → kernel · runtime)

### §D.1 Bildirilen bağımlılıklar

| # | Hedef | Alan | Yön | Ne alıyoruz |
|---|---|---|---|---|
| D1 | `isletim-sistemi-kernel` | `depends_on` | plan → kernel | zamanlayıcı mekanizması, kuyruk/wakeup çerçevesi |
| D2 | `isletim-sistemi-runtime` | `depends_on` | plan → runtime | süreç/iş parçası ömrü, kilit, çağrı bağlamı |

### §D.2 Alt-dal → bağımlılık matrisi

| Alt-dal | `kernel`e bağımlı | `runtime`a bağımlı | Ek bağımlılık |
|---|---|---|---|
| `zamanlama-scheduler` | EVET — politika mekanizması çekirdekte | EVET — yürütme bağlamı çalışma zamanında | — |
| `is-parcacigi-planlamasi` | EVET — affinity/çekirdek sayısı çekirdekte | EVET — `pthread` çağrıları çalışma zamanında | — |
| `kaynak-dagilimi` | EVET — kota ve kuyruk mekanizması çekirdekte | KISMEN — üst sınır süreç ömründe | `isletim-sistemi-core` · `isletim-sistemi-security` (yetki çerçevesi) · `K001-donanım` (cihaz) |

### §D.3 Ters yön (geriye dönük referans) denetimi

| Senaryo | Beklenen | İhlal durumunda |
|---|---|---|
| `kernel`, `plan`e `depends_on` yazar | olmaz — yön tek yönlü | `⚠️ VERIFICATION REQUIRED` + kök `index.md`'ye kaydet |
| `runtime`, `plan`in içeriğini kopyalar | olmaz — tekrar = dolgu | kopya satırlar silinir, köke link verilir |
| Alt-dal, kökte olmayan bir bağımlılık ekler | kök `index.md` güncellenmeden yazılmaz | kök güncellenir, alt-dal bekler |
| Kök, alt-dalda olmayan bir dosyaya link verer | olmaz | link kaldırılır |

### §D.4 Bağımlılık grafiği (metin)

```text
K000-isletim-sistemi
└── isletim-sistemi-plan            (bu subdomain — Kademe-3)
    ├── isletim-sistemi-plan-kapsam.md        (sınır)
    ├── isletim-sistemi-plan-dogrulama.md     (kanıt)
    ├── isletim-sistemi-plan-ornekler.md      (kod)
    ├── zamanlama-scheduler/                  (Kademe-3 → Kademe-4 ×3)
    │   ├── index.md
    │   ├── zamanlama-scheduler-anayasa.md
    │   ├── zamanlama-scheduler-surucu.md
    │   └── zamanlama-scheduler-ornekler.md
    ├── is-parcacigi-planlamasi/              (Kademe-3 → Kademe-4 ×3)
    │   ├── index.md
    │   ├── is-parcacigi-planlamasi-anayasa.md
    │   ├── is-parcacigi-planlamasi-surucu.md
    │   └── is-parcacigi-planlamasi-ornekler.md
    └── kaynak-dagilimi/                      (Kademe-3 → Kademe-4 ×3)
        ├── index.md
        ├── kaynak-dagilimi-anayasa.md
        ├── kaynak-dagilimi-surucu.md
        └── kaynak-dagilimi-ornekler.md
Bağımlılar: isletim-sistemi-kernel · isletim-sistemi-runtime
Yönlü: isletim-sistemi-plan → kernel · isletim-sistemi-plan → runtime (tek yön)
```

### §D.5 Bağımlılık ihlali katalogu

| İhlal | Belirti | Düzeltme |
|---|---|---|
| D-I1 | kök `depends_on` listesinde `plan` görünür | liste `kernel`/`runtime` dışına çıkar |
| D-I2 | bir alt-dal `roadmap` tarihi içerir | madde `§A.2 B1` ile işaretlenip taşınır |
| D-I3 | çekirdek veri yapısı tanımı alt-dalda | tanım `-kernel` dosyasına taşınır, burada çerçeve kalır |
| D-I4 | aynı tablo iki dosyada birebir | tekrar silinir, tek dosya sahiplenir |
| D-I5 | `children` listesi disk ile uyuşmaz | `index.md` `children` alanı disk enumerasyonuyla hizalanır |
| D-I6 | alt-dal dosya adı `anayasa`/`surucu`/`ornekler` dışındaysa | ad değiştirilmez; kapsam bu dosyada düzeltilir (bkz. §B.2–§B.4) |

### §D.6 Bağımlılık sözlüğü (alan → anlam)

| Alan | Tip | Anlamı bu subdomainde |
|---|---|---|
| `depends_on` | liste | hangi subdomainin içeriği olmadan bu subdomain yazılamaz |
| `children` | liste | diskteki çocuk dosya/dizin kimlikleri (yol, uzantısız) |
| `evidence` | liste | iddiaları taşıyan fiziksel kaynak (URL, dosya, bölüm) |
| `validation` | liste | neyin, ne zaman, nasıl doğrulandığı |
| `open_questions` | liste | kapatılmamış soru; `[UNKNOWN]` işaretli kalır |
| `group` | skaler | iki-eksen gruplama etiketi (mimari · kod · kanit) |
| `status` | skaler | `draft` · `verify-required` · `verified` |
| `node_type` | skaler | `subdomain` (klasör indeksi) · `module` (içerik dosyası) |

## §E — Sınır komşuları

| # | Komşu | Ne verdiğimiz | Ne almadığımız | Ortak konu | Ayırıcı çizgi |
|---|---|---|---|---|---|
| E1 | `isletim-sistemi-kernel` | politika çerçevesi ve ölçütler | kuyruk/wakeup iç mekanizması | zamanlayıcı | ne **nasıl** çalışır (kernel) vs hangi **kural** (plan) |
| E2 | `isletim-sistemi-runtime` | öncelik/paralelilik kararlarının yürütme etkisi | süreç/iş parçası API sözleşmesi | eşzamanlılık | ne **çağrılır** (runtime) vs ne **planlanır** (plan) |
| E3 | `isletim-sistemi-core` | kaynak çerçevesinin ihtiyaç duyduğu çekirdek kavramları | çekirdek mimari tanımı | temel tipler | ortak tip kimde tanımlı (core) |
| E4 | `isletim-sistemi-security` | yetki ile sınırlandırma **ihtiyacının** tanımı | capability/rol/ACL modeli | yetki | çerçeve (plan) vs model (security) |
| E5 | `isletim-sistemi-observability` | ölçüm adımlarının listesi | metrik boru hattı, log şeması | ölçüm | okuma (plan) vs toplama (observability) |
| E6 | `isletim-sistemi-testing` | doğrulama senaryosu ve eşikler | test kodu, CI kapısı | doğrulama | strateji (plan) vs uygulama (testing) |
| E7 | `isletim-sistemi-audio` | RT zamanlama sınıfının ses için anlamı | zincir/ASIO/ring buffer tasarımı | gerçek zamanlı | sınıf (plan) vs zincir (audio) |
| E8 | `isletim-sistemi-api` | uygulama katmanı kuyruk/öncelik örneklerinin OS olmadığı notu | endpoint/sözleşme tasarımı | öncelik | örnek (plan) vs API (api) |
| E9 | `isletim-sistemi-observability` | context switch latency ve jitter ölçümünün **adının** burada geçmesi | metrik şeması, toplama hattı | gecikme | ölçüm tanımı (plan) vs toplama (observability) |
| E10 | `K001-donanım` (vault) | hedef cihaz ipucu (Raspberry Pi 5) | çekirdek/bellek topolojisi çizimi | donanım | ipucu (plan) vs tasarım (donanım) |
| E11 | `electroncis` (K000 altı, ad yazımı korunmuştur) | — | devre/BOM içeriği | — | hiç kesişmez |
| E12 | `isletim-sistemi-turu` | OS zamanlayıcı farklarının **varlığının** işaret edilmesi | kurulum yolu, dağıtım yapılandırması | OS seçimi | politika (plan) vs kurulum (turu) |

### §E.1 Kesişim senaryoları ve çözümü

| Senaryo | Çözüm | Kayıt yeri |
|---|---|---|
| İki subdomain aynı kavramı yazıyor | kök `index.md` sahibini belirler | `isletim-sistemi-plan/index.md` |
| Alt-dal sınırı aşıyor | alt-dal `§7 Komşu sınırı` bölümüne geri çekilir | ilgili `-anayasa.md §7` |
| Komşu subdomain bu subdomaini `depends_on` olarak ekler | yön yeniden değerlendirilir; çoğunlukla yanlış tarafta | `isletim-sistemi-plan/index.md` bağımlılık grafiği |
| Q3 kapsam dışı bir içerik geliyor | `§A.2` sahibine taşınır | bu dosya §A.2 |
| Bir URL envanterde zaten varsa | o URL kullanılmaz; yeni benzersiz URL aranır | `isletim-sistemi-plan-dogrulama.md §E` |

### §E.2 Kapsam dışı komşulara yapılan açık atıflar

| Atıf | Amaç |
|---|---|
| `isletim-sistemi-plan-kapsam.md §A.2 B1` | uygulama yol haritası kapsam dışıdır |
| `isletim-sistemi-plan-kapsam.md §A.2 B2` | çekirdek yürütme `-kernel`e aittir |
| `isletim-sistemi-plan-kapsam.md §A.2 B3` | çalışma zamanı `-runtime`a aittir |
| `isletim-sistemi-plan-kapsam.md §A.2 B9` | OS kurulum yolu `-turu`'a aittir |
| `isletim-sistemi-plan-dogrulama.md §E` | 2026-10-13 HTTP 200 doğrulama matrisi |
| `isletim-sistemi-plan-ornekler.md §B` | gerçek repo kodu (`Dosya:`/`Satır:`) |

### §E.3 Komşu sınır kontrol listesi (alt-dal yazarı için)

| # | Kontrol | Evet | Hayır |
|---|---|---|---|
| 1 | Cümle bu subdomainin ekseninde mi? | yaz | `-kernel`/`-runtime`e bırak |
| 2 | İçerik uygulama yol haritası içeriyor mu? | `§A.2 B1` ihlali — sil | devam |
| 3 | Çekirdek iç veri yapısı tarif ediliyor mu? | `§A.2 B2` ihlali — çerçeveyle değiştir | devam |
| 4 | Yetki modeli mi tanımlanıyor? | `§A.2 B4` ihlali — `security`e bırak | devam |
| 5 | Cümle `[UNKNOWN]`/`⚠️` gerektiriyor mu? | işaretle | işaretleme |
| 6 | Aynı tablo başka dosyada var mı? | sahibine bırak, link ver | yaz |
| 7 | Son satır Authority satırı mı? | koru | ekle |
| 8 | Frontmatter `parent:` içeriyor mu? | `parent_id` ile değiştir | devam |
| 9 | `group` değeri tırnaklı mı? | tırnağı kaldır | devam |

## §F — Açık sorular (Q3 · Plan1)

### §F.1 Q3 — kapsam nedir?

| Alan | Değer |
|---|---|
| Soru | `plan` = OS planlama (scheduling) mı, uygulama planlama/roadmap mı? |
| Kaynak | v2 `coremusic-katmanli-mimari-agaci-v2.md §2.1` — ad geçer, içerik tanımlanmaz |
| Durum | **[UNKNOWN] — KAPATILMAZ** |
| Bu turda yapılan | Y1 (scheduling) ekseninde yer tutucu dallanma; karar kaydı §C |
| Kapanış koşulu | Üst otoritenin tek cümlelik kararı + `isletim-sistemi-plan-kapsam.md §A`nin yeniden yazımı |
| Kapanmadığında ne olur | `status: verify-required` korunur, alt-dallar "aday" statüsünde kalır |

### §F.2 Plan1 — scheduling içeriği ayrı yerde mi?

| Alan | Değer |
|---|---|
| Soru | Zamanlama içeriği `isletim-sistemi-kernel` / `isletim-sistemi-runtime` içinde mi, burada mı? |
| Durum | **[UNKNOWN] — KAPATILMAZ** |
| Bu turda yapılan | Sınır çizildi: mekanizma → `-kernel`, yürütme → `-runtime`, politika çerçevesi → burası (`-anayasa §7`) |
| Kapanış koşulu | `isletim-sistemi-kernel` ve `isletim-sistemi-runtime` dosyalarının okunup sınırın teyidi |
| Kapanmadığında ne olur | üç alt-dal `draft` kalır; çakışma tespit edilirse `§D.5` kataloğu işletilir |

### §F.3 Açık soru defteri kuralları

| Kural | Metin |
|---|---|
| F1 | Açık soru, kanıtla cevaplanamıyorsa `[UNKNOWN]` etiketiyle listede kalır |
| F2 | Cevap uydurulmaz; `⚠️ VERIFICATION REQUIRED` yalnız doğrulanamayan iddia için kullanılır |
| F3 | Soru kapanırsa hem kök `index.md` hem bu dosya hem ilgili `-anayasa §8` eşzamanlı güncellenir |
| F4 | Kapatma kararı subagent tarafından verilmez — orkestratöre/üst otoriteye aittir |
| F5 | Bu tur kapanıştır: 16 dosya yazıldı, sorular **açık bırakıldı** |

### §F.4 Soru → kanıt ihtiyacı

| Soru | Mevcut kanıt | Eksik kanıt | Alınacak yer | Sorumluluk |
|---|---|---|---|---|
| Q3 | `depends_on` + 3 URL + v2 adı + donanım ipucu | v2 §2.1'de kapsam tanımı | v2 ağacının yazımı / üst otorite | Vault Steward |
| Plan1 | sınır çizgisi (anayasa §7) | `kernel`/`runtime` dosyalarının içeriği | `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-kernel/` · `.../isletim-sistemi-runtime/` | sonraki tur |
| Q-ek | — | hedef mimaride `pthread` varlığı | `home.coremusic.net/AGENTS.md` + çalışma zamanı tercihi | sonraki tur |

### §F.5 Açık soru durum geçmişi

| Tarih | Olay | Q3 | Plan1 |
|---|---|---|---|
| 2026-10-12 | kök 4 dosya üretildi (60/52/47/47 satır) | [UNKNOWN] açıldı | [UNKNOWN] açıldı |
| 2026-10-13 | envanter ölçüldü (≥500: 0 · alt-dal klasörü yok) | [UNKNOWN] | [UNKNOWN] |
| 2026-10-13 | 3 alt-dal üretildi (scheduling ekseninde; adlar anayasa/surucu/ornekler) | **AÇIK KALDI** | **AÇIK KALDI** |
| 2026-10-13 | `ornekler.md` içindeki 2 aday `Dosya:` referansı disk'te doğrulanamadı, `⚠️ VERIFICATION REQUIRED` ile işaretlendi | **AÇIK KALDI** | **AÇIK KALDI** |
| (gelecek) | `kernel`/`runtime` okunacak | — | kapatılabilir |
| (gelecek) | üst otorite kararı | kapatılabilir | — |

## §G — Dürüstlük kaydı (bu tur tespit edilen doğrulanamayan iddialar)

### §G.1 Doğrulanan iddialar

| # | İddia | Doğrulama yöntemi | Sonuç |
|---|---|---|---|
| G-1 | `shared/src/Bootstrap/RuntimeBootstrap.php:9` boot satırı | `Select-Object -Index 8` | `date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');` — DOĞRULANDI |
| G-2 | `shared/src/Api/Registry/ServiceHealth.php:26` "parallel health check" | `Select-Object -Index 25` | `public function checkAll(): array` — DOĞRULANDI |
| G-3 | `shared/src/Api/Registry/ServiceDefinition.php:23` timeout | `Select-Object -Index 22` | `private readonly int $timeout = 5,` — DOĞRULANDI |
| G-4 | `shared/src/AI/AIOrchestrator.php:20` "priority queue" | `Select-Object -Index 19` | ` * - TaskQueue (priority queue)` — DOĞRULANDI |
| G-5 | `shared/src/AI/AIOrchestrator.php:45` öncelik seviyesi | `Select-Object -Index 44` | `'fault' => ['agent' => 'dsp-fw', 'priority' => 'critical'],` — DOĞRULANDI |
| G-6 | `shared/src/Exception/RateLimitException.php:7` `retryAfter` | `Select-Object -Index 6` | `private int $retryAfter;` — DOĞRULANDI |
| G-7 | `home.coremusic.net/config/bootstrap.php:13` istek URI ayrıştırma | `Select-Object -Index 12` | `$requestUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');` — DOĞRULANDI |
| G-8 | `shared/src/Middleware/MiddlewarePipeline.php:42` süre ölçümü | `Select-Object -Index 41` | `$start = hrtime(true);` — DOĞRULANDI |
| G-9 | `shared/src/Api/Middleware/RateLimitMiddleware.php:39` pencere kontrolü | `Select-Object -Index 38` | `if ($this->rateLimiter->isLimited($key, $this->maxRequests, $this->windowSeconds)) {` — DOĞRULANDI |
| G-10 | `shared/src/Cache/PageCacheAdapter.php:11` TTL | `Select-Object -Index 10` | `private int $ttlPage;` (yapılandırıcı 600) — DOĞRULANDI |
| G-11 | `shared/tests/Repository/ApiKeyCreateCommandTest.php:253` alt süreç | `Select-Object -Index 252` | `$handle = proc_open(` — DOĞRULANDI |

### §G.2 Doğrulanamayan iddialar (⚠️ işaretli, kapatılmaz)

| # | İddia | Neden doğrulanamadı | Durum |
|---|---|---|---|
| G-N1 | `home.coremusic.net/src/Service/JobService.php:40,60` içinde `available_after` / `max_attempts` | dosya disk'te YOK (`Get-ChildItem -Recurse -Filter JobService.php` → 0 sonuç · grep `available_after|max_attempts` → 0 eşleşme) | ⚠️ VERIFICATION REQUIRED |
| G-N2 | `home.coremusic.net/config/bootstrap.php:3-13` içinde `require_once ... autoload.php` bloğu | dosya diskte VAR ama L3 = `use CoreMusic\Home\Container\HomeContainer;`, L13 = `$requestUri = ...` — iddia edilen `require_once` bloğu bulunamadı | ⚠️ VERIFICATION REQUIRED |

### §G.3 Dürüstlük kuralı (bu dosyanın geçerliliği)

| Kural | Metin |
|---|---|
| G-K1 | Doğrulanamayan iddia silinmez; `⚠️ VERIFICATION REQUIRED` ile işaretlenir ve §G.2 tablosuna kaydedilir |
| G-K2 | Doğrulanamayan iddia **kanıt olarak kullanılamaz**; kapsam kararı G-1..G-11 üzerine kurulur |
| G-K3 | Bu dosyadaki her `Dosya:`/`Satır:` çifti §G.1 tablosunda `Select-Object -Index` ile teyit edilmiştir |
| G-K4 | Q3/Plan1 `[UNKNOWN]` olarak kalır; G-N1/G-N2 kapatılmaz |

## §H — Bu tur üretim kaydı

| # | Dosya | İşlem | Hedef |
|---|---|---|---|
| 1 | `isletim-sistemi-plan-kapsam.md` | yeniden yazım + büyütme (459 → ≥500) | ≥500 satır · `group: mimari` |
| 2 | `isletim-sistemi-plan-dogrulama.md` | yeniden yazım + büyütme (47 → ≥500) | ≥500 satır · `group: kanit` |
| 3 | `isletim-sistemi-plan-ornekler.md` | yeniden yazım + büyütme (47 → ≥500) | ≥500 satır · `group: kod` |
| 4 | `index.md` | daraltma (102 → 40–100) | 40–100 satır · `group: mimari` |
| 5 | `zamanlama-scheduler/index.md` | yeni | 40–100 satır · `group: mimari` |
| 6 | `zamanlama-scheduler/zamanlama-scheduler-anayasa.md` | yeni | ≥500 satır · `group: mimari` |
| 7 | `zamanlama-scheduler/zamanlama-scheduler-surucu.md` | yeni | ≥500 satır · `group: mimari` |
| 8 | `zamanlama-scheduler/zamanlama-scheduler-ornekler.md` | yeni | ≥500 satır · `group: kod` |
| 9 | `is-parcacigi-planlamasi/index.md` | yeni | 40–100 satır · `group: mimari` |
| 10 | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-anayasa.md` | yeni | ≥500 satır · `group: mimari` |
| 11 | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-surucu.md` | yeni | ≥500 satır · `group: mimari` |
| 12 | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-ornekler.md` | yeni | ≥500 satır · `group: kod` |
| 13 | `kaynak-dagilimi/index.md` | yeni | 40–100 satır · `group: mimari` |
| 14 | `kaynak-dagilimi/kaynak-dagilimi-anayasa.md` | yeni | ≥500 satır · `group: mimari` |
| 15 | `kaynak-dagilimi/kaynak-dagilimi-surucu.md` | yeni | ≥500 satır · `group: mimari` |
| 16 | `kaynak-dagilimi/kaynak-dagilimi-ornekler.md` | yeni | ≥500 satır · `group: kod` |

### §H.1 Commit kuralı

| Kural | Durum |
|---|---|
| `git commit` subagent tarafından atılmaz | BU TURDA COMMIT YOK — orkestratöre aittir |
| Yazım yüzeyi | yalnız `isletim-sistemi-plan/**` beyazlistesi |
| Komşu yasağı | `isletim-sistemi-{core,cross,kernel,observability,runtime,security,testing}` · `isletim-sistemi-{api,audio,turu}` · `electroncis` · `K001..K019` · `*.php`/`*.js`/`*.css`/`*.sql` — **yazılmadı, yalnız okundu** |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode