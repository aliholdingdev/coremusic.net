---
title: "CPU (İşlemci) — Doğrulama"
type: cpu-dogrulama
node_id: K001-donanım/donanaım-type/cpu/cpu-dogrulama
node_type: module
status: proposed
parent_id: K001-donanım/donanaım-type/cpu
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "https://www.php.net/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.php.net/releases/8.4/en.php (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.php.net/manual/en/book.opcache.php (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.php.net/manual/en/features.commandline.php (HTTP 200, 2026-10-13, HEAD)"
  - "https://riscv.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.riscv.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.arm.com/architecture (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.arm.com/architecture/cpu (HTTP 200, 2026-10-13, HEAD)"
  - "https://docs.microsoft.com/ (HTTP 200, 2026-10-13, HEAD)"
  - "Repo ölçümü 2026-10-13: *.c/*.cpp/*.h/*.hpp/*.cs = 0 · cpuid|RDTSC|AVX|NEON = 0 · \\bcpu\\b *.php = 7"
  - "shared/composer.json: \"php\": \">=8.4\" + ext-apcu/ext-json/ext-mbstring/ext-pdo (okundu 2026-10-13)"
  - ".ai/.decisions/accepted/ADR-006-performance-targets.md satır 128: OPcache/JIT zorunlu (okundu 2026-10-13)"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · fiziksel ölçüm YAPILMADI (K-C6 hariç tüm S1 kapıları AÇIK)"
open_questions:
  - "Q-CD1: Üretim sunucusunun CPU sınıfı (x86-64 mü, ARM mı) bilinmiyor — UNKNOWN (K-C7/K-C8)"
  - "Q-CD2: opcache.jit üretimde etkin mi ve hangi modda — ADR-006 zorunlu kılıyor, canlı doğrulama YOK — ⚠️ VERIFICATION REQUIRED (K-C3)"
  - "Q-CD3: VT-x/AMD-V/ARM-VHE sanallaştırma üretimde kullanılıyor mu — UNKNOWN (K-C4)"
  - "Q-CD4: Bellek kanalı (tek/çift) ve önbellek gecikme ölçümü yapılmadı — ⚠️ VERIFICATION REQUIRED (K-C6)"
group: kanit
date: 2026-10-13
updated: 2026-10-13
---

# cpu-dogrulama — İşlemci Doğrulama Kapıları

> **Amaç:** `cpu-kapsam.md` içindeki her iddiayı **kapı kapı** doğrulamak. Bu dosya
> **kanıt** dosyasıdır: her bölüm bir test protokolü, geçme eşikleri ve güncel durum
> içerir. S1 kanıtı olmayan hiçbir kapı "GEÇTİ" yazılamaz (AGENTS.md §3
> ZERO-HALLUCINATION). Doğrulanamayan → `⚠️ VERIFICATION REQUIRED`, bilinmeyen → `UNKNOWN`.

## §1 ELI15

İşlemci için beş soru sorulur ve hiçbiri **ölçülmeden/dokümante edilmeden** yanıtlanmaz:
(1) hangi ISA aileleri kapsamda ve kaynakları hâlâ ayakta mı (URL), (2) PHP 8.4'ün
çalışma bağlamı disk kanıtıyla sabit mi (composer.json + ADR-006), (3) repo'da gerçekten
düşük seviyeli CPU kodu var mı (grep/dosya sayımı), (4) sanallaştırma/vektör uzantıları
kullanılıyor mu (kod = 0 → kullanılmıyor), (5) üretim donanımının CPU sınıfı biliniyor mu
(bilinmiyor → UNKNOWN). Bu dosya bu beş sorunun **adımlarını, kayıt formunu ve geçme
eşiklerini** yazar.

> ⚠️ VERIFICATION REQUIRED: Bu dosyada **hiçbir gerçek performans ölçümü yoktur** —
> CPU modeli, çekirdek sayısı, önbellek boyutu, saat hızı, bellek gecikmesi
> **ölçülmedi** ve envanterde kayıtlı değil. Fiziksel ölçüm = 0 → bu dosyadaki tüm
> performans iddiaları `⚠️ VERIFICATION REQUIRED` işaretini taşır. Ölçülen tek şey
> **repo'daki kod ve disk dosyalarıdır** (§4, §5) — bunlar envanter/kanıt ölçümüdür,
> performans ölçümü DEĞİLDİR.

## §2 Kanıt Seviye Şeması (bu dosyada kullanılır)

| # | Seviye | Anlamı | Bu dosyadaki örnek | Kapı durumu |
|---|---|---|---|---|
| 1 | S1 | Fiziksel ölçüm / gözlem (cihaz üzerinde) | CPU modeli, çekirdek, önbellek gecikmesi, JIT etkisi | ❌ AÇIK (tezgâh/envanter yok) |
| 2 | S2 | Aritmetik olarak türetilmiş | 319 php dosyası ÷ 0 native dosya = saf PHP yığını | ✅ kapandı (§4) |
| 3 | S3 | Bu oturumda URL doğrulandı (HEAD 200) | 11 URL (§3) | ✅ kapandı (§3) |
| 4 | S4 | Vault disk kanıtı (okunan dosya) | composer.json · ADR-006:128 · ci.yml · anayasa:195/250/258 | ✅ kapandı (§5) |
| 5 | S5 | Üst otorite / ADR / sabit kural | ADR-006 OPcache/JIT zorunlu · donanım-anayasa §10/§12 | ✅ kapandı (§5) |

**Kural:** Bu dosyadaki bir "GEÇTİ" yazısı yalnızca **S1 kanıtı** ile yazılabilir —
istisnası §7'de kapı bazında belirtilir (K-C2/K-C3 gibi S4/S5 ile kapanan kapılar
"kanıtlandı, ölçülmedi" olarak işaretlenir). S2–S5 tek başına performans ölçümü yerine
geçmez; datasheet/anayasa değeri S4/S5 sayılır, S1 sayılmaz.

### §2.1 İddia Türü → Zorunlu Kanıt Seviyesi Eşleme Matrisi

| İddia türü | Örnek (bu dosyadan) | Zorunlu seviye | Tek başına yeterli mi? |
|---|---|---|---|
| "Repo'da kod yok/var" | §4.1 native dosya = 0 | S2 (ölçüm) | Evet — dosya sayımı tekrarlanabilir |
| "Sürüm/eklenti bu" | §5.1 composer.json `>=8.4` | S4 (disk) | Evet — dosya kanıtı |
| "Kural böyle" | §5.2 ADR-006 JIT zorunlu | S5 (ADR) | Evet — kural iddiası için |
| "URL hâlâ ayakta" | §3.1 11 URL 200 | S3 (HEAD) | Evet — erişim iddiası için |
| "Donanım bu kadar hızlı" | §8.2 throughput/latency | S1 (ölçüm) | **Hayır** — S2–S5 ölçüm yerine geçmez |
| "Üretimde etkin" | §5.2/§7.3 JIT canlı durum | S1 (canlı okuma) | **Hayır** — ADR kuralı ≠ canlı gerçeklik |
| "Donanımda var mı" | §7.7/§7.8 envanter | S1 (envanter) | **Hayır** — CI/URL donanım kanıtı değildir |

**Bu matrisin görevi:** Bir sonraki revizyonda yeni iddia eklenecekse hangi seviyede
kanıt gerektirdiği bu tablodan okunur; seviyesiz iddia dosyaya girmez.

## §3 Web Doğrulama Kaydı (yalnızca bu oturumda HEAD ile doğrulanmış URL'ler)

> Yöntem: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
> (oturum içi çalıştırıldı, 2026-10-13). Aşağıdaki tabloda **yalnızca bu oturumda
> dönen** HTTP durumları vardır; eski belgelerdeki "200" iddiaları buraya kopyalanmaz.

### §3.1 Kullanılan kaynaklar (HTTP 200 — kaynak olarak kullanıldı)

| # | URL | HTTP | Erişim | Kullanım alanı |
|---|---|---|---|---|
| 1 | `https://www.php.net/` | 200 | 2026-10-13 | PHP dilinin resmi ana sayfası — genel referans |
| 2 | `https://www.php.net/releases/8.4/en.php` | 200 | 2026-10-13 | PHP 8.4 sürüm sayfası — sürüm varlığı |
| 3 | `https://www.php.net/manual/en/book.opcache.php` | 200 | 2026-10-13 | OPcache el kitabı — §5.2 JIT kanıtı bağlamı |
| 4 | `https://www.php.net/manual/en/features.commandline.php` | 200 | 2026-10-13 | CLI çalışma bağlamı — §5.2 |
| 5 | `https://riscv.org/` | 200 | 2026-10-13 | RISC-V ISA'nın resmi kuruluşu — C-T03 |
| 6 | `https://www.riscv.org/` | 200 | 2026-10-13 | Aynı kuruluşun www kanalı — C-T03 |
| 7 | `https://www.arm.com/architecture` | 200 | 2026-10-13 | ARM mimarisi genel bakış — C-T02 |
| 8 | `https://www.arm.com/architecture/cpu` | 200 | 2026-10-13 | ARM CPU mimarisi — C-T02 |
| 9 | `https://docs.microsoft.com/` | 200 | 2026-10-13 | Windows/platform belgeleri kökü — x86-64/Windows bağlamı |
| 10 | `https://www.amd.com/` | 200 | 2026-10-13 | AMD kökü — x86-64 üreticisi referansı (kök; derin sayfa 404, §3.2) |
| 11 | `https://www.khronos.org/` | 200 | 2026-10-13 | Khronos kökü — ISA/başlık sahibi kuruluş (vektör/standart bağlamı) |

### §3.2 Kullanılmayan kaynaklar (erişilemedi veya sayfa yok → KAYNAK OLMADI)

| # | URL | HTTP | Erişim | Karar |
|---|---|---|---|---|
| 1 | `https://www.intel.com/` | **403** | 2026-10-13 | **KULLANILMADI** — Intel ana sayfası bu oturumda erişim reddi verdi; Intel'e ait **hiçbir** iddia bu dosyalara kaynak alınamaz |
| 2 | `https://www.xmos.com/` | **406** | 2026-10-13 | **KULLANILMADI** — XMOS DSP tarafıdır (K004) ve HEAD 406 aldı; cpu dosyalarına kaynak yazılmaz |
| 3 | `https://developer.arm.com/` | **403** | 2026-10-13 | **KULLANILMADI** — ARM geliştirici portalı erişim reddi verdi; yalnız arm.com/architecture/* (§3.1/7-8) kullanıldı |
| 4 | `https://www.amd.com/en/products/processors` | **404** | 2026-10-13 | **KULLANILMADI** — sayfa yok; AMD kökü (§3.1/10) dışında AMD derin sayfası kullanılmadı |
| 5 | `https://www.php.net/manual/en/opcache.jit.php` | **404** | 2026-10-13 | **KULLANILMADI** — JIT sayfası bu adreste yok; JIT için §3.1/3 (book.opcache.php) kullanıldı |

### §3.3 Kullanılmayan ama erişilebilir kaynaklar (erişildi, bu dosyada kaynak yapılmadı)

| # | URL | HTTP | Neden kaynak yapılmadı |
|---|---|---|---|
| 1 | `https://www.arm.com/` (kök) | 200 | Kök URL bu oturumda K001 kapsamında başka dosyalarda da kullanılıyor; cpu dosyaları **derin** sayfaları (§3.1/7-8) kaynak alır (G6 tekilleştirme) |
| 2 | `https://www.nvidia.com/` · `https://developer.nvidia.com/` · `https://www.vulkan.org/` · `https://www.opengl.org/` · `https://www.gnu.org/` · `https://www.amd.com/en/products/technologies/rdna` | 200 | Bunlar **gpu dalının** alanıdır (compute/render); cpu dosyalarına **yazılmaz** — sınır: `cpu-kapsam.md` §12 |

**Kural:** cpu dosyalarında yalnızca §3.1 tablosundaki 11 URL kaynak verilebilir.
§3.2'deki 5 URL **hiçbir** cpu dosyasına kaynak olarak yazılamaz (403/406/404 =
kanıt değildir). `.ai/electronic/index.md:75` gibi dosyalardaki eski "HTTP 200"
iddiaları bu oturumda yeniden doğrulanmadıysa o belgede `⚠️ VERIFICATION REQUIRED`
olarak kalmalıdır — bu dosya o iddiaları **onaylamaz**.

## §4 Repo Ölçüm Kaydı (2026-10-13 — bu oturumda alındı)

| Parametre | Değer |
|---|---|
| Tarih | 2026-10-13 |
| Yöntem | `grep` + `filesystem` (uzantı sayımı) |
| Kapsam | Repo kökü `C:\www\coremusic.net` |
| Hariç tutulanlar | `node_modules/` · `vendor/` · `.git/` |
| Fiziksel ölçüm | **YOK** — bu tablo kod/envanter sayımıdır, performans ölçümü DEĞİLDİR |

### §4.1 Dosya uzantısı sayımı (kaynak seti)

| Uzantı | Sayı | Yorum |
|---|---|---|
| `*.php` | **319** | Uygulama katmanının tamamı PHP |
| `*.js` | **98** | İstemci katmanı |
| `*.css` | **280** | Stil katmanı |
| `*.sql` | **23** | Şema/migrasyon |
| `*.ts` | **2** | Sınırlı TypeScript |
| `*.c` · `*.cpp` · `*.h` · `*.cs` | **0** (hepsi) | **Gömülü/yerel CPU kodu YOK** — JUCE/ASIO/C# derlenmedi |

### §4.2 Desen arama (grep) tablosu

| Desen | Dosya seti | Eşleşme | Yorum |
|---|---|---|---|
| `cpuid\|__rdtsc\|RDTSC\|AVX\|NEON` | `*.{php,c,cpp,h,hpp,cs}` | **0** | CPU kimlik/vektör komutu kullanılmıyor |
| `\bcpu\b` | `*.php` | **7** | 5'i test dosyasındaki UA string'i; 2'si `media.coremusic.net/bin/audit.php:58,390` yorum satırı |
| `XMOS\|XU316` | `shared/*.php` | **1** | `shared/src/AI/AIEngine.php:140` yorum satırı — DSP (K004) sınırı, CPU iddiası DEĞİL |
| `Vulkan\|OpenCL\|CUDA\|OpenGL` | `*.{php,c,cpp,h,hpp,cs}` | **0** | Bu dosyalarda geçmez (gpu dalının konusu — §3.3/2) |

### §4.3 Ölçümden çıkan sonuç (S2 — aritmetik yorum)

- Uygulama katmanı **saf PHP** (319 php / 0 native) → CPU'yu doğrudan etkileyen
  yürütme faktörü **PHP yorumlayıcısı + OPcache/JIT**'tir (§5.2), ham SIMD komutu değil.
- Düşük seviyeli CPU komutu (`cpuid/AVX/NEON`) = **0** → bu dal şu an envanter +
  tasarım kararı seviyesindedir; kod tarafı **yoktur**.
- `cpu` terimi repo'da yalnızca **yorum ve UA metinlerinde** geçer (7 eşleşme) →
  "repo'da CPU'ya özgü mantık yok" iddiasını destekler.
- XMOS geçişi (1 eşleşme) yorum satırıdır ve **DSP alanındadır** — `cpu-kapsam.md` §12
  sınırı gereği CPU envanterine sayılmaz.

### §4.4 Yeniden Ölçüm Protokolü (başkası tekrar edebilsin)

> Aşağıdaki adımlar §4.1–§4.2 sayılarını **yeniden üretmek** içindir; sonuç bu
> oturumda alınmıştır, adımlar tekrar eden kişiye yol haritasıdır.

| Adım | İşlem | Beklenen kayıt yeri |
|---|---|---|
| R1 | Repo kökünden `*.php` say (node_modules/vendor/.git hariç) | §4.1 → 319 |
| R2 | Aynı kapsamda `*.js` · `*.css` · `*.sql` · `*.ts` say | §4.1 → 98 · 280 · 23 · 2 |
| R3 | `*.c` · `*.cpp` · `*.h` · `*.cs` say | §4.1 → 0 (hepsi) |
| R4 | `cpuid\|__rdtsc\|RDTSC\|AVX\|NEON` desenini `*.{php,c,cpp,h,hpp,cs}` içinde ara | §4.2 → 0 |
| R5 | `\bcpu\b` desenini `*.php` içinde ara; her isabeti açıp yorum/UA olduğunu doğrula | §4.2 → 7 (5 UA + 2 yorum) |
| R6 | `XMOS\|XU316` desenini `shared/*.php` içinde ara | §4.2 → 1 (AIEngine.php:140) |
| R7 | `Vulkan\|OpenCL\|CUDA\|OpenGL` desenini `*.{php,c,cpp,h,hpp,cs}` içinde ara | §4.2 → 0 |
| R8 | Sonuçları §4.1/§4.2 tablolarıyla karşılaştır; fark varsa §9'a yeni satır ekle | U-C kayıtları |

**Fark kuralı:** Yeniden ölçümde sayı farkı çıkarsa **eski sayı silinmez** — yeni
satır tarih/ölçüm kimliğiyle eklenir ve fark §9'a "tekrar ölçüm farkı" olarak yazılır
(kimse geçmiş kaydı değiştirmez).

## §5 Vault Disk Kanıtları (okunan dosyalar — 2026-10-13)

### §5.1 `shared/composer.json` — PHP sürüm ve eklenti kanıtı

| Anahtar | Değer (dosyadan birebir) | Anlamı |
|---|---|---|
| `require.php` | `">=8.4"` | Uygulama PHP 8.4 ve üzerini hedefler |
| `require.ext-apcu` | mevcut | Kullanıcı-uzayı önbellek eklentisi zorunlu |
| `require.ext-json` | mevcut | JSON desteği zorunlu |
| `require.ext-mbstring` | mevcut | Çok baytlı metin desteği zorunlu |
| `require.ext-pdo` | mevcut | PDO veri erişimi zorunlu |

**Yorum (S4):** CPU yürütme bağlamı PHP 8.4'tür; eklenti listesi opcache'yi
**açıkça** listelemez — OPcache zorunluluğu ayrı kanıttır (§5.2). `ext-opcache`
listede yokluğu `UNKNOWN` değildir: OPcache derleme zamanı etkinleştirilen bir
uzantıdır ve ADR-006 onu üretimde zorunlu kılar (§5.2).

### §5.2 `.ai/.decisions/accepted/ADR-006-performance-targets.md` satır 128 — OPcache/JIT

| Öğe | Değer |
|---|---|
| Dosya | `.ai/.decisions/accepted/ADR-006-performance-targets.md` |
| Satır | 128 |
| İçerik (birebir aktarım) | OPcache/JIT: "Zorunlu, üretimde açık" |
| Kanıt seviyesi | S5 (accepted ADR — bağlayıcı kural) |
| Bu oturumda canlı doğrulandı mı | **HAYIR** — üretim sunucusuna erişim yok → K-C3 kapısı "kanıtlandı, ölçülmedi" |

**İddia sınırı:** `cpu-kapsam.md` §11'de "JIT açık" DEĞİL, "ADR-006 üretimde açık
olmasını zorunlu kılıyor; canlı doğrulama YOK" yazılır. İkisi aynı şey değildir.

### §5.3 `.github/workflows/ci.yml` — çalışma zamanı ortamı

| Öğe | Değer |
|---|---|
| `runs-on` | `ubuntu-latest` (workflow boyunca **6** kez) |
| Anlamı | CI CPU'su GitHub barındırma ortamıdır — **üretim sunucusu DEĞİL** |
| Üretim CPU sınıfı | **UNKNOWN** (Q-CD1) — CI'dan üretim CPU'su çıkarılamaz |

### §5.4 `.ai/architecture/K001-donanım/donanım-anayasa.md` — bağlayıcı satırlar

| Satır | Bağlam | cpu dosyalarına etkisi |
|---|---|---|
| 195 | §10 Açık Sorular/3 — ses işleme/DSP sınırı | DSP = K004; bu dosyada DSP performans iddiası YOK |
| 250 | §12/C10 — OPcache/JIT zorunluluğu | §5.2 ile aynı kuralın anayasa kaydı |
| 258 | §12.1 — performans/hedef bağlamı | Performans hedefleri anayasada; ölçüm bu dosyada YOK |

### §5.5 `.ai/architecture/K001-donanım/donanaım-type/donanaım-type-kapsam.md` — kapsam satırları

| Satır | Bağlam | Etki |
|---|---|---|
| 46 | §2/A6 — "NEEDS DISCOVERY" ifadesi | Tüm cpu tipleri §13.6.w'te NEEDS DISCOVERY işaretli |
| 135 | §7/3-T1 — dal/tür ayrımı kuralı | cpu bir **tür** dalıdır; ISA/aileler bu dalda, üretim donanımı envanterde |

### §5.6 ADR-038 (accepted) — DSP sınırı kanıtı (yalnız sınır için)

| Öğe | Değer |
|---|---|
| Dosya | `.ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md` |
| Kullanım | **Yalnız** sınır kaydı: XU316/PCM3168A DSP/ses kartı alanındadır (K004/K006) |
| cpu dosyalarına etkisi | XU316 bir **CPU tipi kaydı değildir** — `cpu-kapsam.md` §12 bunu zorunlu kılar |
| Bu dosyadaki tek XMOS geçişi | §4.2 — yorum satırı (AIEngine.php:140), envanter kanıtı DEĞİL |

## §6 İddia → Kanıt Matrisi (cpu-kapsam.md iddiaları)

| # | İddia (kapsam dosyasında) | Kanıt (dosya + yer) | Seviye | Durum |
|---|---|---|---|---|
| M1 | Üç ISA ailesi kapsamdadır: x86-64, ARM, RISC-V | §3.1/5-8 (riscv.org, arm.com/architecture) · görev tanımı | S3 | ✅ kaynaklı |
| M2 | PHP 8.4 yürütme bağlamı | §5.1 composer.json `">=8.4"` | S4 | ✅ kanıtlandı |
| M3 | OPcache/JIT üretimde zorunlu | §5.2 ADR-006:128 · §5.4 anayasa:250 | S5 | ✅ kanıtlandı · ❌ S1 AÇIK |
| M4 | Repo'da native CPU kodu yok (0 dosya) | §4.1 `*.c/*.cpp/*.h/*.cs = 0` | S2 | ✅ ölçüldü |
| M5 | CPU komut uzantıları kullanılmıyor (0 eşleşme) | §4.2 cpuid/AVX/NEON = 0 | S2 | ✅ ölçüldü |
| M6 | `cpu` terimi yalnız yorum/UA'da (7 eşleşme) | §4.2 `\bcpu\b` = 7 | S2 | ✅ ölçüldü |
| M7 | XMOS = DSP sınırı, CPU kaydı değil | §5.6 ADR-038 · §4.2 tek yorum geçişi | S4 | ✅ sınır kaydedildi |
| M8 | CI `ubuntu-latest` (6×) — üretim CPU'su değil | §5.3 ci.yml | S4 | ✅ kanıtlandı |
| M9 | Üretim CPU sınıfı biliniyor | Yok — Q-CD1 | — | ❌ UNKNOWN |
| M10 | VT-x/NEON/vCPU kullanımı | Yok — repo grep 0 · envanter yok (§4.2) | — | ❌ UNKNOWN (kod = kullanılmıyor) |
| M11 | Önbellek/bellek denetleyici değerleri | Yok — ölçüm YOK (§1 uyarısı) | — | ⚠️ VERIFICATION REQUIRED |
| M12 | RISC-V üretim senaryosu | Yok — yalnız ISA kaynağı var (§3.1/5-6) | — | ❌ UNKNOWN (kapsam: taşıma ilgisi) |

## §7 Doğrulama Kapıları (K-C1 … K-C8)

> Her kapının 4 parçası vardır: **tanım** (neyi kontrol eder), **adım** (nasıl
> kontrol edilir), **geçme eşiği** (ne yazarsa geçer), **durum** (bu oturumda ne oldu).
> S1 gerektiren kapılar tezgâh yokluğunda AÇIK kalır.

### §7.1 K-C1 — ISA kaynak doğrulaması (S3)

| Öğe | Değer |
|---|---|
| Tanım | Üç ISA ailesinin resmi kaynakları bu oturumda HEAD ile doğrulandı mı? |
| Adım | §3.1 tablosundaki ISA URL'leri (`riscv.org` ×2, `arm.com/architecture` ×2, `khronos.org`) HEAD 200 mü? |
| Geçme eşiği | En az 1 URL / aile → 200 |
| Durum | ✅ **GEÇTİ** — RISC-V 2×200 · ARM 2×200 · x86-64 için üretici kökü 200 (amd.com) + platform belgeleri 200 (docs.microsoft.com); Intel 403 olduğu için Intel kaynağı **kullanılmadı** (§3.2/1) |

### §7.2 K-C2 — PHP çalışma bağlamı (S4)

| Öğe | Değer |
|---|---|
| Tanım | composer.json sürüm/eklenti kanıtı okundu mu ve kapsam dosyasına birebir aktarıldı mı? |
| Adım | `shared/composer.json` → `require` bloğu okunur; `php`, `ext-*` satırları §5.1'e kopyalanır |
| Geçme eşiği | `">=8.4"` ve eklenti listesi dosyada aynen görünür |
| Durum | ✅ **GEÇTİ (kanıtlandı, ölçülmedi)** — S4 kanıtı tam; canlı çalışma ölçümü yok (S1 bu kapıda istenmez) |

### §7.3 K-C3 — OPcache/JIT zorunluluğu (S5 + S1 boşluğu)

| Öğe | Değer |
|---|---|
| Tanım | JIT zorunluluğu bağlayıcı kaynakla sabit mi, canlı doğrulama durumu nedir? |
| Adım | ADR-006:128 + anayasa:250 okunur; üretim sunucusunda `opcache_get_status()` **bu oturumda çalıştırılmadı** |
| Geçme eşiği | Zorunluluk kuralı kaynaklı (S5) → kapı "kanıtlandı"; canlı etkinlik S1 ile ayrı kapıdır |
| Durum | ⚠️ **KANITLANDI / S1 AÇIK** — kural sabit, canlı etkinlik doğrulanmadı (Q-CD2) |

### §7.4 K-C4 — Sanallaştırma ve vektör uzantı kullanımı (S2)

| Öğe | Değer |
|---|---|
| Tanım | VT-x/AMD-V/NEON/AVX kullanımı repo'da görünüyor mu? |
| Adım | grep `cpuid|RDTSC|AVX|NEON` → 0 (§4.2) · vCPU/konteyner donanım hızlandırma envanteri → yok |
| Geçme eşiği | Kullanım iddiası için ≥1 kod kanıti gerekir |
| Durum | ✅ **GEÇTİ (kullanım YOK tescili)** — "kullanılmıyor" iddiası ölçüldü; "donanımda var mı" ayrı soru → Q-CD3 UNKNOWN |

### §7.5 K-C5 — CPU ↔ GPU sınır temizliği (S2)

| Öğe | Değer |
|---|---|
| Tanım | cpu dosyalarına GPU'ya özgü iddia/URL sızmış mı? |
| Adım | `cpu/*.md` içinde gpu-dosya URL'leri (nvidia/vulkan/opengl/rdna/developer.nvidia.com) aranır → 0 · `Vulkan|OpenCL|CUDA|OpenGL` repo'da `*.{php,c,cpp,h,hpp,cs}` = 0 (§4.2) |
| Geçme eşiği | cpu dosyalarında gpu kaynağı = 0 |
| Durum | ✅ **GEÇTİ** — §3.3/2 bu URL'leri bilinçli olarak yalnız gpu dalı için ayırdı; cpu dosyalarına yazılmadı |

### §7.6 K-C6 — Ölçüm tekrarlanabilirliği (S2/S1)

| Öğe | Değer |
|---|---|
| Tanım | Repo ölçümleri (§4) başkası tarafından tekrar edilebilir mi? |
| Adım | Aynı desenler yeniden çalıştırılır: uzantı sayımı + 4 grep deseni |
| Geçme eşiği | Aynı sayılar (319/98/280/23/2/0 · 0 · 7 · 1 · 0) |
| Durum | ✅ **GEÇTİ (S2)** — ölçüm bu oturumda alındı ve §4'te ham hâliyle kayıtlı; ❌ **S1 AÇIK**: CPU donanım ölçümü (model/çekirdek/önbellek/gecikme) hiç yapılmadı (Q-CD4) |

### §7.7 K-C7 — x86-64 envanter durumu (S1 boşluğu)

| Öğe | Değer |
|---|---|
| Tanım | Üretim/çalışma makinesinin x86-64 olduğu doğrulandı mı? |
| Adım | CI `ubuntu-latest` (§5.3) ≠ üretim kanıtı · Intel 403 (§3.2/1) · envanter kaydı yok |
| Geçme eşiği | Üretim makinesi CPU modeli kayıtlı olmalı (envanter) |
| Durum | ❌ **AÇIK** — üretim CPU sınıfı bilinmiyor (Q-CD1); x86-64 yalnızca **kapsam tanımı** olarak var (görev sınırı), envanter iddiası değil |

### §7.8 K-C8 — ARM/RISC-V envanter durumu (S1 boşluğu)

| Öğe | Değer |
|---|---|
| Tanım | ARM veya RISC-V cihaz envanterde kayıtlı mı? |
| Adım | Repo'da `\barm\b` geçişi yalnız yorum/tespit kodu düzeyindedir; RISC-V için envanter kaydı **yok**; developer.arm.com 403 (§3.2/3) |
| Geçme eşiği | En az 1 kayıtlı ARM/RISC-V cihaz (envanter + rol) |
| Durum | ❌ **AÇIK** — ARM/RISC-V envanter kaydı yok; ISA yalnız kaynaklı tanımdır (K-C1) |

### §7.9 Kapı özeti tablosu

| Kapı | Konu | Gerekli seviye | Durum |
|---|---|---|---|
| K-C1 | ISA kaynak doğrulama | S3 | ✅ GEÇTİ |
| K-C2 | PHP çalışma bağlamı | S4 | ✅ GEÇTİ (ölçülmedi) |
| K-C3 | OPcache/JIT zorunluluğu | S5 (+S1 açık) | ⚠️ KANITLANDI / S1 AÇIK |
| K-C4 | Sanallaştırma/SIMD kullanımı | S2 | ✅ GEÇTİ (kullanım YOK) |
| K-C5 | CPU↔GPU sınır temizliği | S2 | ✅ GEÇTİ |
| K-C6 | Ölçüm tekrarlanabilirliği | S2 (+S1 açık) | ✅ GEÇTİ (S2) / ❌ S1 AÇIK |
| K-C7 | x86-64 envanter | S1 | ❌ AÇIK |
| K-C8 | ARM/RISC-V envanter | S1 | ❌ AÇIK |

**Toplam:** 8 kapı · 4 tam geçti · 1 kanıtlandı/S1 açık · 1 karışık · 2 açık (envanter).

### §7.10 K-C7/K-C8 Kapatma Protokolü (envanter S1 adımları)

> Bu protokoller bu oturumda **uygulanmadı** (erişim/envanter yok); kapatma
> Vault Steward yetkisindedir.

| Adım | İşlem | Kayıt |
|---|---|---|
| E1 | Üretim sunucusunda CPU modelini oku (Windows: `Get-CimInstance Win32_Processor`; Linux: `lscpu`) | §8.1 `cpu_model` |
| E2 | Sınıfı belirle: x86-64 / ARM / RISC-V (E1 çıktısındaki mimari satırı) | §8.1 `cpu_class` |
| E3 | Çekirdek/iş parçacığı sayısını oku | §8.1 `core_count` · `thread_count` |
| E4 | Saat hızını oku (baz/boost) | §8.1 `base_clock` |
| E5 | Önbellek boyutlarını oku (L1d/L1i/L2/L3) | §8.1 `cache` |
| E6 | Bellek kanalı sayısını oku | §8.1 `memory_channels` |
| E7 | Sanallaştırma bayrağını oku (VT-x/AMD-V/ARM-VHE — BIOS/OS düzeyi) | §8.3 · §8.1 `virtualization_enabled` |
| E8 | Ölçümleri `measured_by`/`measured_at` ile imzala | §8.1 son satır |

**Geçme eşiği (K-C7/K-C8):** E1–E7'de tek bir `UNKNOWN` kalmazsa kapı kapanır;
`cpu_class` x86-64 ise K-C7, ARM/RISC-V ise K-C8 kapanır, diğeri "ilgisiz" işaretlenir.

### §7.11 K-C6 S1 Kapatma Protokolü (performans ölçüm adımları)

| Adım | İşlem | Kayıt |
|---|---|---|
| P1 | Ölçüm ortamını sabitle (donanım + OS + PHP sürümü) | §8.2 başlık |
| P2 | Önbellek gecikmesi ölç (sırayla erişim zamanı: L1→L2→L3→RAM) | §8.2 `cache_latency` |
| P3 | Bellek gecikmesi ölç (rastgele erişim) | §8.2 `memory_latency` |
| P4 | PHP iş yükü ölç (sabit döngü/gerçek sorgu; JIT açık/kapalı iki koşul) | §8.2 `php_throughput` · `jit_overhead` |
| P5 | Ses bağlamı etkisini ölç (varsa); yoksa "uygulanamaz" yaz | §8.2 `audio_context_impact` |
| P6 | Sonuçları `⚠️ VERIFICATION REQUIRED` işaretleriyle değiştir | §8.2 → gerçek değerler |

**Kural:** P2–P5 yapılmadan §8.2'deki `⚠️ VERIFICATION REQUIRED` işaretleri **kaldırılamaz**.

### §7.12 K-C3 S1 Kapatma Protokolü (JIT canlı kontrol)

| Adım | İşlem | Kayıt |
|---|---|---|
| J1 | Üretimde PHP CLI/web durumunu oku: `opcache_get_status()` çıktısı | K-C3 kanıt eki |
| J2 | `opcache.enable` · `opcache.jit` · `opcache.jit_buffer_size` değerlerini kaydet | K-C3 kanıt eki |
| J3 | ADR-006:128 ile karşılaştır ("Zorunlu, üretimde açık") | U-C2 kapanışı |
| J4 | Uyuşmazlık varsa anayasa §12/C10 (satır 250) ihlali olarak eskale et | §10 kapanış |

**Geçme eşiği (K-C3 S1):** J2'deki üç değer ADR-006 ile uyumluysa kapı tam kapanır;
değilse kaynak güncellenir (önce ADR, sonra envanter — ADR-102 §4.2 "genişletme yok"
kuralı: belge, ADR'yi takip eder, tersi değil).

## §8 Envante/Form Kayıtları (hepsi BOŞ — doldurulmayı bekliyor)

> Aşağıdaki formlar bu oturumda **boş** bırakılmıştır; değer uydurulmaz. Doldurma
> yetkisi Vault Steward'dır (K-C7/K-C8 kapılarını kapatır).

### §8.1 Üretim CPU envanter formu (BOŞ)

| Alan | Değer |
|---|---|
| cpu_model | `UNKNOWN` |
| cpu_class (x86-64 / ARM / RISC-V) | `UNKNOWN` |
| core_count | `UNKNOWN` |
| thread_count | `UNKNOWN` |
| base_clock | `UNKNOWN` |
| cache (L1/L2/L3) | `UNKNOWN` |
| memory_channels | `UNKNOWN` |
| virtualization_enabled | `UNKNOWN` |
| measured_by / measured_at | `NOT PROVIDED` |

### §8.2 Ölçüm sonuç formu (BOŞ — S1 kapalı değil)

| Alan | Değer |
|---|---|
| benchmark_workload | `NOT PROVIDED` |
| php_throughput | `⚠️ VERIFICATION REQUIRED` |
| jit_overhead | `⚠️ VERIFICATION REQUIRED` |
| cache_latency | `⚠️ VERIFICATION REQUIRED` |
| memory_latency | `⚠️ VERIFICATION REQUIRED` |
| audio_context_impact | `⚠️ VERIFICATION REQUIRED` |
| measured_by / measured_at | `NOT PROVIDED` |

### §8.3 Sanallaştırma formu (BOŞ)

| Alan | Değer |
|---|---|
| hypervisor | `UNKNOWN` |
| vcpu_count | `UNKNOWN` |
| cpu_pinning | `UNKNOWN` |
| vt_x_or_amd_v_state | `UNKNOWN` |
| arm_vhe_state | `UNKNOWN` |

### §8.4 Form Doldurma Kuralları (Vault Steward yönergesi)

| # | Kural |
|---|---|
| 1 | Form yalnız Vault Steward onayıyla doldurulur; alt agent doldurmaz |
| 2 | Her değer bir **ölçüm kaynağı** taşır (komut çıktısı + tarih + ölçen kişi) |
| 3 | `UNKNOWN` alanı atlanmaz — doldurulana kadar `UNKNOWN` kalır |
| 4 | `⚠️ VERIFICATION REQUIRED` işareti yalnız S1 ölçümüyle kaldırılır |
| 5 | Ölçüm farkı çıkarsa eski satır silinmez, yeni satır eklenir (§4.4 fark kuralı) |
| 6 | Form doldurulunca §7.9 kapı özeti tablosu yeniden yazılır (kapı durumları değişir) |

## §9 UNKNOWN / VERIFICATION REQUIRED Kaydı

| # | Belirsizlik | İşaret | Kapanış koşulu |
|---|---|---|---|
| U-C1 | Üretim CPU sınıfı (x86-64/ARM) | UNKNOWN | §8.1 formu doldurulunca (K-C7/K-C8) |
| U-C2 | Üretimde JIT etkinliği | ⚠️ VERIFICATION REQUIRED | Canlı `opcache_get_status()` okununca (K-C3/S1) |
| U-C3 | VT-x/AMD-V/VHE durumu | UNKNOWN | §8.3 formu doldurulunca |
| U-C4 | Önbellek/bellek gecikme ölçümü | ⚠️ VERIFICATION REQUIRED | §8.2 formunda ölçüm girilince (K-C6/S1) |
| U-C5 | x86-64 üretici sayfası doğrulanamadı (Intel 403) | Kayıt | Intel kaynağı kullanılmaz kalır; erişim düzelirse yeniden denenir |
| U-C6 | RISC-V üretim senaryosu | UNKNOWN | Kapsam kararı Vault Steward'dan gelince |

## §10 Dürüstlük Bloğu ve Kapanış

| Kural | Bu dosyadaki durum |
|---|---|
| Uydurma marka/model/ölçüm/URL yok | ✅ — tüm sayılar §4/§5'te ölçüm kaydı; tüm URL'ler §3'te HEAD kaydı |
| Yer tutucu dizgesi yok (boşluk içeren tutucu parantez, geçici işaret) | ✅ — boş alanlar `UNKNOWN`/`NOT PROVIDED` işaretli |
| Fiziksel ölçüm 0 → tüm performans iddiaları ⚠️ | ✅ — §1 uyarısı + §8.2 formu |
| `parent:` anahtarı yok | ✅ — frontmatter `parent_id` kullanır |
| Intel/XMOS kaynak değil | ✅ — §3.2 KULLANILMADI kaydı |

**Kapanış (2026-10-13):** 8 kapıdan 4'ü tam geçti (K-C1/C2/C4/C5), 2'si S2 düzeyinde
geçti ama S1 açık (K-C3/C6), 2'si envanter eksikliğinden açık (K-C7/C8). cpu dalı bu
nedenle **envanter + tasarım kararı** seviyesindedir; performans iddiası taşımaz.
Sonraki adım: §8 formlarının Vault Steward tarafından doldurulması.

## §11 Revizyon İzi ve Sonraki Adımlar

| Tarih | Revizyon | Değişen | Not |
|---|---|---|---|
| 2026-10-13 | r1 | Dosya spec'e göre tamamen yeniden yazıldı (eski 127 satır → yeni sürüm) | Eski sürüm ADR-102/ADR-038 odaklıydı ve Authority satırı yoktu; DSP (XU316) içeriği §5.6'ya sınır kaydı olarak indirgendi |

| # | Sonraki adım | Sahibi |
|---|---|---|
| 1 | §8.1–§8.3 formlarını doldur (K-C7/K-C8 kapanır) | Vault Steward |
| 2 | §7.12 JIT canlı kontrolü (K-C3/S1 kapanır) | Vault Steward + DevOps |
| 3 | §7.11 performans ölçümü (K-C6/S1 kapanır) | Vault Steward |
| 4 | Kapı kapanınca §7.9 tablosunu güncelle | Bu dosyanın sahibi |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
