---
title: "CPU (İşlemci) — Örnekler"
type: cpu-ornekler
node_id: K001-donanım/donanaım-type/cpu/cpu-ornekler
node_type: module
status: proposed
parent_id: K001-donanım/donanaım-type/cpu
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Repo ölçümü 2026-10-13: *.php=319 · *.js=98 · *.css=280 · *.sql=23 · *.ts=2 · native(c/cpp/h/cs)=0"
  - "Repo grep 2026-10-13: cpuid|RDTSC|AVX|NEON=0 · \\bcpu\\b=7 (yorum/UA) · XMOS|XU316=1 (yorum)"
  - "shared/composer.json: php \">=8.4\" · ext-apcu · ext-json · ext-mbstring · ext-pdo (okundu 2026-10-13)"
  - ".ai/.decisions/accepted/ADR-006-performance-targets.md satır 128: OPcache/JIT zorunlu"
  - ".github/workflows/ci.yml: runs-on ubuntu-latest (6×)"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · uydurma kod/sürüm/çıktı/ölçüm YOK (yalnız şablon ve adım tabloları)"
open_questions:
  - "Q-CO1: Kod tarafı henüz yok — native dosya 0; örnek olarak yalnız şablon/prosedür sunulur"
  - "Q-CO2: cpu envanter kaydı (marka/model/çekirdek) bilinmiyor — UNKNOWN (cpu-dogrulama.md §8.1)"
  - "Q-CO3: Üretim CPU sınıfı (x86-64/ARM) bilinmiyor — UNKNOWN (K-C7/K-C8)"
group: kod
date: 2026-10-13
updated: 2026-10-13
---

# cpu-ornekler — İşlemci Örnekleri ve Şablonları

> **Amaç:** cpu dalının "örnek" katmanı. Bu dosya iki şey sunar: (1) repo'da
> **gerçekten var olan** kanıt satırları (2026-10-13 ölçümü), (2) kod/envanter
> başladığında doldurulacak **şablon ve adım tabloları**. Uydurma kaynak dosya,
> sürüm, kod bloğu, pin, ölçüm veya çıktı **üretilmez** (AGENTS.md §3
> ZERO-HALLUCINATION). Boşluklar `NOT PROVIDED`/`UNKNOWN` işaretidir, doldurulmaz.

## §1 ELI15

Bu dosya "cpu dalından örnek görebilir miyiz?" sorusunun dürüst cevabıdır. Bugün
için cevap: **kod örneği yok** — repo'da `.c/.cpp/.h/.cs` dosyası 0, CPU komut
deseni (`cpuid/AVX/NEON`) 0 eşleşme. Elinizde göreceğiniz iki şey var: (a) gerçek
repo'dan ölçülmüş birkaç **referans satırı** (yorum/UA metinleri) ve (b) ileride
kod/envanter başladığında **birebir doldurulacak** şablonlar. Şablonlar bilgi
işaretidir; içleri uydurma bilgiyle doldurulmaz — doldurma yetkisi Vault Steward'ın
ölçüm/karar girmesiyle açılır.

> ⚠️ VERIFICATION REQUIRED: Bu dosyada **hiçbir performans ölçümü, benchmark
> çıktısı veya donanım değeri yoktur**. Fiziksel ölçüm = 0 (cpu-dogrulama.md §1).
> Şablonlardaki performans alanları `⚠️ VERIFICATION REQUIRED` işaretini taşır.

## §2 Gerçek Repo Kanıtları (2026-10-13 ölçümü — bu dosyadaki TEK "örnek"ler)

### §2.1 Dosya uzantısı envanteri (gerçek sayılar)

| Uzantı | Sayı | cpu dalına etkisi |
|---|---|---|
| `*.php` | 319 | Yürütme bağlamı PHP 8.4 (composer.json) |
| `*.js` | 98 | İstemci — CPU'yu dolaylı etkiler |
| `*.css` | 280 | CPU ile ilgisiz (stil) |
| `*.sql` | 23 | Sunucu tarafı sorgu — CPU yükü üzerinde dolaylı |
| `*.ts` | 2 | Sınırlı |
| `*.c` · `*.cpp` · `*.h` · `*.cs` | 0 (hepsi) | **Gömülü/native CPU kodu YOK** — kod örneği üretilemez |

### §2.2 Ölçülmüş gerçek satırlar (repo'dan — yorum/UA düzeyi)

> Aşağıdaki bloklar **gerçektir** (grep 2026-10-13); kod mantığı DEĞİLDİR,
> belge/tespit niteliğindedir. Bunlar bu dalın repo'daki tek CPU izleridir.

```php
// shared/src/AI/AIEngine.php:140 — yorum satırı (gerçek, ölçülmüş)
// PCM3168A, XMOS XU316, AK4458 için analiz
```

```php
// shared/src/Device/DeviceDetector.php:74 ve :192 — cihaz/Platform tespiti (gerçek, ölçülmüş)
// ARM Linux (Raspberry Pi 5) tespiti — UA/Platform üzerinden
```

```php
// media.coremusic.net/bin/audit.php:58 ve :390 — yorum satırı (gerçek, ölçülmüş)
// "cpu" terimi UA/tarayıcı analizinde geçiyor — gerçek işlemci kodu değil
```

### §2.3 Desen dürüstlük tablosu (grep sonuçları — 2026-10-13)

| Desen | Dosya seti | Eşleşme | Yorum |
|---|---|---|---|
| `cpuid\|__rdtsc\|RDTSC\|AVX\|NEON` | `*.{php,c,cpp,h,hpp,cs}` | **0** | Düşük seviyeli CPU komutu yok |
| `\bcpu\b` | `*.php` | **7** | 5 UA + 2 yorum (§2.2) |
| `XMOS\|XU316` | `shared/*.php` | **1** | AIEngine.php:140 yorum — DSP sınırı (K004) |
| `Vulkan\|OpenCL\|CUDA\|OpenGL` | `*.{php,c,cpp,h,hpp,cs}` | **0** | gpu dalı konusu; cpu dosyalarına sızmaz |

**Sonuç:** cpu dalının "örnek" katmanı bugün **kanıt + şablon** katmanıdır; kod
katmanı **yoktur** (Q-CO1). Bu, eksiklik değil ölçülmüş durumdur.

### §2.4 Kategori Bazlı CPU Etkisi (ne ölçüldü, ne etkiler)

| Kategori | Dosya sayısı | CPU etkisi türü | cpu dalına kayıt |
|---|---|---|---|
| PHP (uygulama) | 319 | Yürütme yükü — OPcache/JIT ile doğrudan | §4.2 (bağlam şablonu) |
| JS (istemci) | 98 | İstemci yürütme — sunucu CPU'sunu etkilemez (tarayıcı tarafı) | Kayıt yok (ilgisiz) |
| CSS | 280 | İşlem yok (tarayıcı ayrıştırma istemcide) | Kayıt yok (ilgisiz) |
| SQL | 23 | Sorgu yükü **sunucu** CPU'sunda — MySQL barındırma CPU'su ayrı envanter | Kapsam dışı (veritabanı sunucusu) |
| TS | 2 | Derlenmiş JS | JS ile aynı |
| native (c/cpp/h/cs) | 0 | YOK — bu dalın kod yüzeyi boş | §3 kaydı |

**Not:** SQL'in CPU etkisi **MySQL sunucusunun** CPU'sundadır; bu, uygulama
sunucusunun CPU envanteri değildir (farklı makine olabilir) — cpu dalı yalnız
uygulama/barındırma CPU'sunu kapsar, DB sunucusu ayrı envanter alanıdır.

### §2.5 Ölçüm Ortamı Kaydı (§2 sayılarının alındığı ortam)

| Alan | Değer |
|---|---|
| Tarih | 2026-10-13 |
| Araçlar | `grep` + `filesystem` (uzantı sayımı) |
| Kapsam | Repo kökü `C:\www\coremusic.net` |
| Hariç | `node_modules/` · `vendor/` · `.git/` |
| Ölçen | Orkestratör (subagent — Vault Steward onaylı dal yazımı) |
| Tekrarlanabilirlik | cpu-dogrulama.md §4.4 R1–R8 protokolü |

### §2.6 Ölçüm Yöntemi → Güvenilirlik Matrisi

| Ölçüm | Yöntem | Güvenilirlik | Tekrar koşulu |
|---|---|---|---|
| Uzantı sayımı | filesystem | Yüksek (deterministik) | Her revizyonda |
| Grep desenleri | regex arama | Orta (desen kapsamasına bağlı) | Her revizyonda |
| Composer sürümü | dosya okuma | Yüksek (birebir metin) | composer.json değişince |
| ADR satırı | dosya okuma | Yüksek (birebir aktarım) | ADR değişince |
| Canlı JIT durumu | opcache_get_status | Yüksek (canlı okuma) | Dağıtım sonrası |
| Performans ölçümü | benchmark | Düşük→yüksek (ortama bağlı) | Ortam değişince |

### §2.7 Ölçüm Güvenilirlik Sınırları (bu dosya neyi KANITLAMAZ)

| Sınır | Açıklama |
|---|---|
| Sayılar yalnız repo'yu kapsar | Üretim sunucusu HARIÇ — repo ≠ çalışan sistem |
| CI ≠ üretim | `ubuntu-latest` GitHub ortamıdır (cpu-dogrulama.md §5.3) |
| Grep 0 = kod yok | Farklı desenler yine 0 verebilir; desen listesi §2.3'te sınırlı |
| Yorum satırı ≠ envanter | AIEngine.php:140 bir yorumdur; XU316 envanter kanıtı DEĞİLDİR |

## §3 Kod Yok Kaydı (neden gerçek kod bloğu yok)

| # | Neden | Kanıt | Kapanış koşulu |
|---|---|---|---|
| 1 | Native kaynak dosyası 0 | §2.1 `*.c/*.cpp/*.h/*.cs = 0` | İlk native dosya yazılınca |
| 2 | CPU komut deseni 0 | §2.2/§2.3 grep = 0 | İlk cpuid/AVX/NEON eşleşmesi gelince |
| 3 | CPU envanteri bilinmiyor | Q-CO2/Q-CO3 — envanter formu boş | cpu-dogrulama.md §8.1 doldurulunca |
| 4 | DSP çipi firmware'i bu dalda değil | DSP = K004 (cpu-kapsam.md §12) | İlgili dal yazılmışsa oraya bakılır |
| 5 | Üretim CPU sınıfı bilinmiyor | cpu-dogrulama.md K-C7/K-C8 açık | Envanter girilince |

**Kural:** Bu 5 madde kapanmadan bu dosyaya "örnek kod" bloğu **eklenemez**;
eklenecek olursa Vault Steward ölçüm/karar girişi yapar, alt agent uydurmaz.

## §4 Örnek Şablonları (kod/envanter başladığında doldurulacak)

> **UYARI:** Aşağıdaki bloklar **şablondur**, mevcut kod/veri değildir. Her hücre
> `NOT PROVIDED`/`UNKNOWN` işaretlidir; doldurma yalnız Vault Steward yetkisindedir
> (cpu-dogrulama.md §8.4). Kod başlayınca bu bloklar gerçek içerikle DEĞİŞTİRİLİR.

### §4.1 CPU envanter kayıt şablonu (boş)

| Alan | Değer | Kaynak (doldurunca) |
|---|---|---|
| cpu_model | `NOT PROVIDED` | `lscpu` / `Get-CimInstance Win32_Processor` çıktısı |
| cpu_class | `NOT PROVIDED` | x86-64 · ARM · RISC-V (birini seç) |
| core_count | `NOT PROVIDED` | OS çıktısı |
| thread_count | `NOT PROVIDED` | OS çıktısı |
| base_clock | `NOT PROVIDED` | OS çıktısı |
| cache_l1d / l1i / l2 / l3 | `NOT PROVIDED` | OS çıktısı |
| memory_channels | `NOT PROVIDED` | OS/BIOS çıktısı |
| virtualization | `NOT PROVIDED` | VT-x · AMD-V · VHE · yok (birini seç) |
| measured_by / measured_at | `NOT PROVIDED` | Ölçen kişi + tarih |

### §4.2 PHP yürütme bağlamı şablonu (yarı dolu — disk kanıtlı olanlar dolduruldu)

| Alan | Değer | Kanıt durumu |
|---|---|---|
| php_version | `>=8.4` | ✅ composer.json (gerçek) |
| ext-apcu · ext-json · ext-mbstring · ext-pdo | `require` listesinde | ✅ composer.json (gerçek) |
| opcache_enable | `UNKNOWN` | ❌ canlı okuma yok (K-C3) |
| opcache_jit_mode | `UNKNOWN` | ❌ canlı okuma yok (K-C3) |
| opcache_jit_buffer_size | `UNKNOWN` | ❌ canlı okuma yok (K-C3) |
| measured_by / measured_at | `NOT PROVIDED` | — |

### §4.3 Önbellek/bellek ölçüm şablonu (boş — S1 kapısı)

| Ölçüm | Değer | Birim |
|---|---|---|
| cache_latency_l1 | `⚠️ VERIFICATION REQUIRED` | ns |
| cache_latency_l2 | `⚠️ VERIFICATION REQUIRED` | ns |
| cache_latency_l3 | `⚠️ VERIFICATION REQUIRED` | ns |
| memory_latency | `⚠️ VERIFICATION REQUIRED` | ns |
| memory_bandwidth | `⚠️ VERIFICATION REQUIRED` | GB/s |
| measured_by / measured_at | `NOT PROVIDED` | — |

### §4.4 Sanallaştırma şablonu (boş)

| Alan | Değer |
|---|---|
| hypervisor | `UNKNOWN` |
| vcpu_count | `UNKNOWN` |
| cpu_pinning | `UNKNOWN` |
| vt_x_amd_v_state | `UNKNOWN` |
| arm_vhe_state | `UNKNOWN` |
| measured_by / measured_at | `NOT PROVIDED` |

### §4.5 Yeni ISA/aile örneği şablonu (boş — yeni tip eklenince)

| Alan | Değer |
|---|---|
| isa_family | `NOT PROVIDED` |
| official_source_url | `NOT PROVIDED` (yalnız bu oturumda HEAD 200 olanlar — cpu-dogrulama.md §3.1) |
| relevance_to_coremusic | `NOT PROVIDED` |
| status | `NOT PROVIDED` (PROPOSED · NEEDS DISCOVERY) |

### §4.6 JIT etki ölçüm şablonu (boş — S1 kapısı)

| Koşul | throughput | Not |
|---|---|---|
| JIT kapalı | `⚠️ VERIFICATION REQUIRED` | Ölçüm yok |
| JIT açık (tracing) | `⚠️ VERIFICATION REQUIRED` | Ölçüm yok |
| fark | `⚠️ VERIFICATION REQUIRED` | Aritmetik türetme S2 ile later |

### §4.7 Sistem İzleme Şablonu (boş — canlı izleme kaydı)

| Alan | Değer | Not |
|---|---|---|
| cpu_usage_peak | `⚠️ VERIFICATION REQUIRED` | % |
| cpu_usage_avg_24h | `⚠️ VERIFICATION REQUIRED` | % |
| load_per_core | `⚠️ VERIFICATION REQUIRED` | çekirdek başına |
| steal_time (VM'de) | `UNKNOWN` | Sanallaştırma varsa |
| throttle_event | `UNKNOWN` | Konteyner limiti varsa |
| measured_by / measured_at | `NOT PROVIDED` | — |

### §4.8 PHP İstek Yaşam Döngüsü Şablonu (boş — kayıt adımları)

| Aşama | CPU etkisi | Değer |
|---|---|---|
| HTTP kabul (web sunucusu) | hafif | `NOT PROVIDED` |
| PHP bootstrap (autoload) | ılımlı — JIT etkisi burada | `NOT PROVIDED` |
| Middleware zinciri (11 PSR-15) | ılımlı | `NOT PROVIDED` |
| Sorgu çalıştırma (PDO) | bekleme ağırlıklı — DB sunucusu CPU'su | `NOT PROVIDED` |
| Yanıt gönderimi | hafif | `NOT PROVIDED` |
| Toplam | — | `⚠️ VERIFICATION REQUIRED` |

**Kural:** Bu şablon **kod okunarak** (Controller/Middleware akışı) ve **ölçülerek**
doldurulur; aşama süreleri uydurulmaz. Kod referansı: `shared/src/PageRouter/` +
`shared/src/Middleware/` (PSR-15 ×11, vault kanıtı).

### §4.9 K000 Bağımlılık Şablonu (boş — OS katmanı kaydı)

| Alan | Değer | Not |
|---|---|---|
| os_family | `UNKNOWN` | Windows · Linux ·两者 (üretim) |
| os_version | `UNKNOWN` | — |
| scheduler | `UNKNOWN` | K000 alanı — cpu dalı yalnız işaretler |
| irq_affinity | `UNKNOWN` | Düşük gecikme senaryosu varsa |
| governor | `UNKNOWN` | performance · powersave · UNKNOWN |

**Sınır notu:** Bu şablonun doldurulması **K000-isletim-sistemi** dalının işidir;
cpu-ornekler.md yalnız hangi alanın hangi dalda olduğunu gösterir (çifte kayıt yasağı).

### §4.10 Dal Denetim Listesi (her revizyonda işaretlenir)

| # | Kontrol | İşaret |
|---|---|---|
| 1 | `parent:` anahtarı frontmatter'da yok | ☐ |
| 2 | Son satır Authority satırı | ☐ |
| 3 | Uydurma kod/ölçüm/URL eklenmedi | ☐ |
| 4 | `UNKNOWN`/`NOT PROVIDED`/`⚠️` işaretleri korundu | ☐ |
| 5 | Ölçüm yenilendiyse eski satır silinmedi (fark kuralı) | ☐ |
| 6 | Sınır dışı içerik eklenmedi (§6/§6.1/§6.2/§6.3) | ☐ |
| 7 | Kapı tablosu güncellendi mi kontrol edildi (cpu-dogrulama.md §7.9) | ☐ |
| 8 | Commit ATILMADI (orkestratöre bırakıldı) | ☐ |

## §5 Adım Adım Prosedürler (şablon doldurma rehberi)

### §5.1 Yeni CPU tipi örneği ekleme (5 adım)

| Adım | İşlem | Yasak |
|---|---|---|
| 1 | Önce ADR/karar: yeni tip kararı Vault Steward'dan | Mevcut ADR'leri değiştirmek |
| 2 | `cpu-kapsam.md` §13'e 26 alanlı kayıt ekle | 26 alan dışına çıkmak · `parent:` kullanmak |
| 3 | `cpu-dogrulama.md` §6'ya iddia→kanıt satırı ekle | Kanıtsız satır yazmak |
| 4 | `cpu-ornekler.md` §4.5 şablonunu doldur | `NOT PROVIDED` hücreleri uydurma değerle doldurmak |
| 5 | Repo ölçümünü yenile (cpu-dogrulama.md §4.4 R1–R8) | Eski sayıyı silmek (fark kuralı) |

### §5.2 CPU envanter örneği girme (3 adım)

| Adım | İşlem | Kayıt |
|---|---|---|
| E1 | Üretim sunucusunda §4.1 formundaki alanları oku | §4.1 satırları |
| E2 | Ölçümleri `measured_by/at` ile imzala | §4.1 son satır |
| E3 | `cpu-dogrulama.md` §7.9 kapı tablosunu güncelle (K-C7/K-C8) | Kapı kapanışı |

### §5.3 Ölçüm şablonu doldurma (3 adım)

| Adım | İşlem | Kural |
|---|---|---|
| M1 | §4.3/§4.6 formlarını bir ölçüm oturumunda doldur | `⚠️ VERIFICATION REQUIRED` yalnız S1 ile kalkar |
| M2 | Ölçüm ortamını kaydet (donanım+OS+PHP) | Ortamsız ölçüm sayılmaz |
| M3 | Sonuçları `cpu-dogrulama.md` §8.2'ye taşı | Form tek noktada yaşar; kopya çoğaltma |

### §5.4 Kod bloğu ekleme önkoşulu (2 adım)

| Adım | İşlem | Kapı |
|---|---|---|
| K1 | §3'teki 5 maddeyi kapat | cpu-dogrulama.md K-C6/S1 |
| K2 | Gerçek dosya yolu + ölçüm satırıyla blok ekle | Uydurma blok yasak |

### §5.5 Periyodik Yeniden Ölçüm Prosedürü (§2 sayıları için)

| Adım | İşlem | Zamanlama |
|---|---|---|
| P1 | cpu-dogrulama.md §4.4 R1–R8'i çalıştır | Dal revizyonunda |
| P2 | Yeni sayıları eski tabloyla karşılaştır | Aynı oturumda |
| P3 | Fark varsa yeni satır ekle (eskiyi silme) | Aynı oturumda |
| P4 | Fark varsa §9'a "tekrar ölçüm farkı" satırı yaz | Aynı oturumda |
| P5 | Değişiklik commit'ini orkestratöre bildir | Kapanışta |

### §5.6 Kapı Kapatma Prosedürü (cpu-dogrulama.md kapıları)

| Kapı | Kapatma adımı | Kanıt eki |
|---|---|---|
| K-C7 | §4.1 formunu doldur (E1–E8) → `cpu_class` gir | OS çıktı kaydı |
| K-C8 | ARM/RISC-V envanteri gir veya "ilgisiz" işaretle | Envanter kaydı |
| K-C3/S1 | §7.12 JIT canlı kontrol (J1–J4) | `opcache_get_status` çıktısı |
| K-C6/S1 | §7.11 performans ölçüm (P1–P6) | Ölçüm oturum kaydı |

### §5.7 Handover Prosedürü (dal dışı içerik gelirse)

| Durum | Aksiyon |
|---|---|
| GPU konulu içerik | `gpu/` dalına ilet — bu dosyaya YAZMAZ |
| DSP/FIR/EQ konulu içerik | K004 dalına ilet |
| OS zamanlayıcı/irq konulu içerik | K000 dalına ilet |
| Display/panel gecikmesi | `display/` dalına ilet |
| CPU envanter/ölçüm | Bu dosyada §4 şablonlarına işlenir |

### §5.8 Bilinen Hata Kayıtları (bu dalın yazım tarihçesinden)

| # | Hata | Sonuç | Düzeltme |
|---|---|---|---|
| H1 | Eski sürümde `depends_on` ADR-102/ADR-038 idi | Dal bağımlılığı ADR'ye bağlandı; anayasa bağımlılığı kayboldu | `K001-donanım` + `K000-isletim-sistemi` yazıldı (spec) |
| H2 | Eski sürümde Authority satırı yoktu | Otorite zinciri kopuk | Son satır Authority yapıldı |
| H3 | Eski sürüm XU316 içeriğini "örnek" sanıyordu | DSP içeriği cpu dalına sızmıştı | Sınır kaydına indirgendi (§6) |
| H4 | `node_id` düz çizgi formatındaydı | Şema ihlali | `K001-donanım/donanaım-type/cpu/cpu-ornekler` yazıldı |
| H5 | Boş şablonlarda parantezli tutucular vardı | Yer tutucu riski | `NOT PROVIDED`/`UNKNOWN` işaretlerine çevrildi |

**Hata kuralı:** Bu tablo **append-only**'dir — yeni hata satır olarak eklenir,
eski satır silinmez (kayıt bütünlüğü).

## §6 Kapsam Sınırı Örneği (ne bu dosyada, ne bu dalda)

| Konu | cpu'da? | Neden | Gideceği yer |
|---|---|---|---|
| CPU envanter kaydı (model/çekirdek) | ✅ İÇİ | İşlemcinin kendisi | §4.1 + cpu-kapsam.md §13 |
| OPcache/JIT bağlamı | ✅ İÇİ | PHP yürütme bağlamı | §4.2 + cpu-kapsam.md §11 |
| AVX/NEON vektör uzantıları | ✅ İÇİ | Komut seti genişletmesi | cpu-kapsam.md §9 |
| VT-x/AMD-V/VHE sanallaştırma | ✅ İÇİ | CPU uzantısı | §4.4 + cpu-kapsam.md §10 |
| FIR/EQ/FFT algoritması | ❌ DIŞI | Algoritma ≠ işlemci | K004 Ses İşleme |
| DSP çipi (XU316) firmware kodu | ❌ DIŞI | DSP = K004 sınırı | K004 / K005 |
| GPU hesaplama (CUDA/OpenCL) | ❌ DIŞI | compute+render = gpu dalı | `gpu/` dalı |
| Ekran/panel gecikmesi | ❌ DIŞI | display dalı | `display/` dalı |
| Bellek/önbellek donanım ölçümü | ✅ İÇİ (form) | CPU besleme yolu | §4.3 (boş form) |
| UA string'inde "cpu" geçen satır | ⚠️ Not | Belge niteliğinde | §2.2 (gerçek, ölçülmüş) |

### §6.1 Kardeş Dal Sınır Matrisi (donanaım-type altındaki diğer dallar)

| Dal | Kapsadığı | cpu ile sınır |
|---|---|---|
| `cpu/` (bu dal) | İşlemci ISA/çekirdek/önbellek/bellek denetleyici/SIMD/sanallaştırma | — |
| `gpu/` | Hesaplama + render (CUDA/OpenCL/Vulkan, video kod çözme) | Vektör işleme: CPU SIMD ≠ GPU compute — ayrım §4.5 |
| `display/` | Panel + UI çıktı gecikmesi | Display ≠ render — render gpu'da, panel display'de |
| `memory/` | RAM/fiş fiziksel katmanı | Bellek denetleyici CPU içindedir; RAM modülü memory dalında — sınır: cpu-kapsam.md §8 |
| `power/` | Besleme/akım/termal | CPU TDP termali power dalında; CPU işlevi burada |
| `cables/` | Fiziksel bağlantı | CPU ile ilgisi yok (bu dal dışında) |
| `network/` | Ağ | CPU ile ilgisi yok (bu dal dışında) |
| `audio/` | DAC/ADC/ses kartı | Ses kartı ≠ CPU — DSP çipi K004 |
| `hdd/` · `ssd/` · `cipler/` | Depolama · çip envanteri | I/O beklemesi CPU'yu etkiler ama depolama dalında |

**Çifte kayıt yasağı:** Bir bilgi iki dalda birden yazılmaz; hangi dalda olduğu bu
tablodan bulunur. Sınır uyuşmazlığı Vault Steward'a sorulur (kendi başına yorumlanmaz).

### §6.2 Senaryo Bazlı Karar Örnekleri (hangi soru hangi dosyaya)

| Soru | Doğru dosya | Yanlış dosya |
|---|---|---|
| "Üretimde kaç çekirdek kullanılıyor?" | cpu-dogrulama.md §8.1 (envanter formu) | cpu-kapsam.md (kapsam dosyası form taşımaz) |
| "JIT açık mı?" | cpu-dogrulama.md §7.12 (JIT protokolü) | cpu-ornekler.md (örnek dosyası canlı durum taşımaz) |
| "AVX kullanabilir miyiz?" | cpu-kapsam.md §9 (SIMD kapsamı) + §4.6 (etki şablonu) | gpu/ (GPU compute ≠ CPU SIMD) |
| "CUDA ile FIR hızlandırılır mı?" | gpu/ dalı (compute) | cpu/ (bu dal) |
| "Panel gecikmesi ne kadar?" | display/ dalı | cpu/ (bu dal) |
| "RAM kaç kanal?" | cpu-kapsam.md §8 (denetleyici) + memory/ (RAM modülü) | Tek dosyaya iki kez yazmak (çifte kayıt) |
| "XU316 kodu nerede?" | K004 (DSP/firmware sınırı) | cpu/ (yalnız sınır notu §6) |
| "OS zamanlayıcı nasıl?" | K000-isletim-sistemi | cpu/ (§4.9 yalnız işaret eder) |

### §6.3 Karar Ağacı (tek soru — tek yol)

| Soru | Evet | Hayır |
|---|---|---|
| İşlemcinin kendisiyle ilgili mi (ISA/çekirdek/önbellek/SIMD/sanallaştırma)? | → bu dal (§4) | ↓ |
| Hesaplama + render ile ilgili mi? | → gpu/ dalı | ↓ |
| Panel/UI çıktı ile ilgili mi? | → display/ dalı | ↓ |
| DSP/ses işleme ile ilgili mi? | → K004 | ↓ |
| OS katmanı ile ilgili mi? | → K000 | ↓ |
| Fiziksel besleme/termal mi? | → power/ dalı | ↓ |
| Hâlâ emin değil | → Vault Steward'a sor (kendi başına yorumlama) | — |

## §7 Yasak Üretim Listesi (bu dosyada ASLA)

| # | Yasak | Sonuç |
|---|---|---|
| 1 | Uydurma kod bloğu (çekirdek/başlatma/ölçüm kodu) | Hallüsinasyon — dosya reddedilir |
| 2 | Uydurma benchmark/çıktı/ölçüm değeri | `⚠️ VERIFICATION REQUIRED` yerine sahte kanıt |
| 3 | Doğrulanmamış URL kaynak olarak | cpu-dogrulama.md §3.2 (403/406/404) kullanımı yasak |
| 4 | `parent:` anahtarı (frontmatter) | Şema ihlali — `parent_id` kullanılır |
| 5 | Yer tutucu dizgesi (süslü parantezli tutucu, geçici işaretleme) | `UNKNOWN`/`NOT PROVIDED` kullanılır |
| 6 | DSP/GPU içeriği buraya taşımak | Sınır ihlali (§6) — ilgili dala |
| 7 | Mevcut ADR'leri düzenlemek | ADR'ler salt-okunur; karar yeni ADR ile |
| 8 | Eski ölçüm satırlarını silmek | cpu-dogrulama.md §4.4 fark kuralı |

### §7.1 Yanlış / Doğru Aksiyon Örnekleri (karar anı rehberi)

| Durum | Yanlış aksiyon | Doğru aksiyon |
|---|---|---|
| "Örnek kod eklemem gerekiyor" ama native dosya 0 | Uydurma çekirdek kodu yazmak | §3 kaydını göstermek + §4.6 şablonunu boş bırakmak |
| JIT açık mı soruldu | "Evet açık" demek (ADR-006'dan çıkarım) | "ADR-006 zorunlu kılıyor; canlı doğrulama YOK — K-C3/S1 açık" demek |
| Intel kaynağı erişilemedi (403) | Başka Intel sayfası denemeden "200" yazmak | §3.2'ye KULLANILMADI kaydı eklemek; Intel'siz kaynak zinciri kurmak |
| ARM cihaz tespit kodu bulundu | "Sistem ARM'de çalışıyor" demek | "UA/Platform tespiti var; üretim sınıfı UNKNOWN (K-C8)" demek |
| Ölçüm farkı çıktı | Eski sayıyı silip yenisini yazmak | Yeni satır eklemek + §9'a fark satırı yazmak |
| GPU konusu geldi | Bu dosyaya eklemek | gpu/ dalına iletmek (§5.7) |
| Form doldurulacak | Alt agent doldurmak | Vault Steward'a iletmek (§4.x yetki notu) |
| Yeni tip eklenecek | cpu-kapsam.md'ye doğrudan yazmak | §5.1 sırasını izlemek (ADR → kapsam → doğrulama → örnek → ölçüm) |

## §8 Dürüstlük Bloğu ve Revizyon

| Kural | Bu dosyadaki durum |
|---|---|
| §2'deki tüm satırlar ölçülmüş gerçek repo verisi | ✅ (2026-10-13, grep/filesystem) |
| §4 şablonları yalnız yapı/alan adı taşır; değer `NOT PROVIDED`/`UNKNOWN` | ✅ |
| Performans alanları `⚠️ VERIFICATION REQUIRED` | ✅ (§1, §4.3, §4.6) |
| Uydurma kod/sürüm/çıktı/URL yok | ✅ (§7 yasak listesi) |
| DSP/GPU içeriği yok (sınır korundu) | ✅ (§6) |

### §8.1 Terim Sözlüğü (bu dosyada geçen terimler)

| Terim | Anlamı |
|---|---|
| ISA | Instruction Set Architecture — işlemci komut seti ailesi (x86-64 · ARM · RISC-V) |
| SIMD | Single Instruction Multiple Data — tek komutla çoklu veri işleme (AVX/NEON) |
| Önbellek hiyerarşisi | L1/L2/L3 katmanları — çekirdeğe veri besleme yolu |
| Bellek denetleyici | RAM erişimini sağlayan CPU içi blok |
| OPcache/JIT | PHP derleme-öncesi önbelleği / çalışma zamanı derleyici |
| Sanallaştırma | VT-x · AMD-V · ARM-VHE — donanım destekli VM yürütme uzantısı |
| Envanter | Fiziksel/varlık kaydı — marka/model/adet; ölçümden farklıdır |
| Ölçüm (S1) | Fiziksel gözlem — bu dosyada YOK |
| Şablon | Doldurulmayı bekleyen boş form — içeriği uydurulmaz |
| NEEDS DISCOVERY | Keşif bekliyor (donanaım-type-kapsam.md:46) — tüm tipler bu işaretli |
| PROPOSED | Önerildi — Vault Steward onayı bekliyor |

### §8.2 Çapraz Referanslar

| Konu | Dosya |
|---|---|
| Tip kayıtları (6 tip, 26 alan) | `cpu/cpu-kapsam.md` §13 |
| Kapılar (K-C1…K-C8) + formlar | `cpu/cpu-dogrulama.md` §7–§8 |
| Dal indeksi | `cpu/index.md` |
| Kapsam/çelişki kaydı | `cpu/cpu-kapsam.md` §0 |
| Ölçüm tekrar protokolü | `cpu/cpu-dogrulama.md` §4.4 |
| Dal üstü anayasa | `donanım-anayasa.md` §10/§12 |
| Tür üstü kapsam | `donanaım-type-kapsam.md` §2/§7 |

### §8.3 Dosya Bütünlük Özeti (bu dosyanın kendi ölçümü)

| Öğe | Değer |
|---|---|
| Dosya | `cpu/cpu-ornekler.md` |
| Satır hedefi | ≥500 (spec) |
| Ölçüm tarihi | 2026-10-13 (yazım sonrası) |
| Authority satırı | Son satırda mevcut |
| Frontmatter anahtarları | `parent_id` (yalnız) · `group: kod` · `status: proposed` |
| Uydurma içerik | 0 (§7 yasak listesi denetlendi) |
| `UNKNOWN`/`NOT PROVIDED`/`⚠️` işaretleri | Mevcut (§4 formları) |
| Kod bloğu | Yalnız §2.2 (gerçek, ölçülmüş repo satırları) |
| Commit | ATILMADI (orkestratöre ait) |

| Tarih | Revizyon | Not |
|---|---|---|
| 2026-10-13 | r1 | Dosya spec'e göre yeniden yazıldı (eski 152 satır → yeni sürüm); eski sürüm ADR-038/XU316 odaklıydı, DSP içeriği sınır kaydına indirgendi; Authority satırı eklendi |

## §9 Açık Soru İzleme (Q-CO1 … Q-CO3)

| # | Açık soru | İşaret | Kapanış koşulu | Kapı |
|---|---|---|---|---|
| Q-CO1 | Kod tarafı yok — örnek olarak yalnız şablon sunuluyor | `NOT PROVIDED` | İlk native dosya yazılınca (§3/§5.4) | K-C6/S1 |
| Q-CO2 | cpu envanter kaydı (marka/model/çekirdek) bilinmiyor | `UNKNOWN` | cpu-dogrulama.md §8.1 doldurulunca | K-C7/K-C8 |
| Q-CO3 | Üretim CPU sınıfı (x86-64/ARM) bilinmiyor | `UNKNOWN` | §4.1 formunda `cpu_class` girilince | K-C7/K-C8 |

**İzleme kuralı:** Bu üç soru kapanmadan bu dosyaya gerçek kod bloğu veya ölçüm
değeri **eklenmez** (§7 yasak 1-2); kapanışta §8.1–§8.3 işaretleri güncellenir ve
`cpu-dogrulama.md` §7.9 kapı tablosu yeniden yazılır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
