---
title: "GPU — Doğrulama"
type: gpu-dogrulama
node_id: K001-donanım/donanaım-type/gpu/gpu-dogrulama
node_type: module
status: proposed
parent_id: K001-donanım/donanaım-type/gpu
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "https://www.vulkan.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.opengl.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://developer.nvidia.com/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.khronos.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.amd.com/en/products/technologies/rdna (HTTP 200, 2026-10-13, HEAD)"
  - "Repo ölçümü 2026-10-13: Vulkan|OpenCL|CUDA|OpenGL (*.{php,c,cpp,h,hpp,cs}) = 0 · WebGL|WebGPU (*.js) = 0"
  - ".ai/architecture/K001-donanım/donanım-anayasa.md §10 satır 195 · §12 satır 250 · §12.1 satır 258"
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md §4 (5 dal — gpu YOK)"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · fiziksel ölçüm YAPILMADI (S1 kapıları açık)"
open_questions:
  - "Q-GD1: GPU envanteri (var mı, hangi rolde) — UNKNOWN (K-G5)"
  - "Q-GD2: FIR/EQ GPU offload senaryosu — PLANNED, ölçüm YOK"
  - "Q-GD3: Video donanım hızlandırma kullanımı — UNKNOWN"
  - "Q-GD4: ADR-102 revizyonu (gpu dahil) — APPROVAL REQUIRED"
group: kanit
date: 2026-10-13
updated: 2026-10-13
---

# gpu-dogrulama — GPU Doğrulama Kapıları

> **Amaç:** `gpu-kapsam.md` iddialarını kapı kapı doğrulamak. S1 kanıtı olmadan
> hiçbir kapı "GEÇTİ" yazılamaz (AGENTS.md §3 ZERO-HALLUCINATION); do
> ğrulanamayan → `⚠️ VERIFICATION REQUIRED`, bilinmeyen → `UNKNOWN`.

## §1 ELI15

GPU için beş soru sorulur: (1) hangi API/üretici kaynakları hâlâ ayakta mı (URL),
(2) repo'da gerçekten GPU API'si kullanımı var mı (grep), (3) donanım envanteri
biliniyor mu (bilinmiyor → UNKNOWN), (4) dalın açılışı onaylı mı ve çelişki
kaydı tam mı (ADR-102 boşluğu dâhil), (5) tüm tipler 26 alan taşıyor mu (bütünlük).
Bu dosya bu soruların adım/eşik/durum tablolarını yazar.

> ⚠️ VERIFICATION REQUIRED: Bu dosyada **hiçbir GPU performans ölçümü yoktur** —
> model, bellek, saat, TFLOPS, kod çözme performansı **ölçülmedi**. Fiziksel ölçüm
> = 0 → tüm performans iddiaları bu işareti taşır. Ölçülen tek şey **repo kodu ve
> disk dosyalarıdır** (§4, §5) — bunlar envanter/kanıt ölçümüdür.

## §2 Kanıt Seviye Şeması

| # | Seviye | Anlamı | gpu örneği | Durum |
|---|---|---|---|---|
| 1 | S1 | Fiziksel ölçüm / canlı okuma | GPU modeli, VRAM, bant genişliği | ❌ AÇIK (envanter yok) |
| 2 | S2 | Aritmetik/ölçüm türetmesi | GPU API grep = 0 → repo'da kullanım yok | ✅ kapandı (§4) |
| 3 | S3 | Bu oturumda URL HEAD 200 | 5 URL (§3.1) | ✅ kapandı (§3) |
| 4 | S4 | Vault disk kanıtı | anayasa:195/250/258 · kapsam:46/135 · ADR-102:§4 | ✅ kapandı (§5) |
| 5 | S5 | Üst otorite/ADR/sabit kural | Vault Steward klasör açma onayı (2026-10-13) | ✅ kapandı (§5) |

**Kural:** "GEÇTİ" yalnız S1 kanıtıyla yazılır — istisnalar kapı bazında §7'de
belirtilir (S2/S3/S4/S5 ile kapanan kapılar "ölçülmedi, kanıtlandı" işaretlenir).

### §2.1 İddia Türü → Zorunlu Kanıt Seviyesi Eşleme Matrisi

| İddia türü | gpu örneği | Zorunlu seviye | Tek başına yeterli mi? |
|---|---|---|---|
| "API var/şu üreticinin" | §3.1 URL 200 | S3 | Evet — erişim iddiası |
| "Repo'da kullanılmıyor" | §4.1 grep=0 | S2 | Evet — tekrarlanabilir |
| "Kural/onay böyle" | §5.4 Vault Steward onayı | S5 | Evet — karar iddiası |
| "Donanımda var mı" | §8.1 envanter | S1 | **Hayır** — URL/repo donanım kanıtı değil |
| "X kat hızlı" | §8.3 offload hızı | S1 (ölçüm) | **Hayır** — kuramsal yorum bile olsa ölçüm gerekir |
| "Sürücü şu sürümde" | §8.1 driver_version | S1 (canlı okuma) | **Hayır** |
| "Video hızlandırma aktif" | §8.2 video_decode_note | S1 | **Hayır** |

## §3 Web Doğrulama Kaydı (HEAD — 2026-10-13)

> Yöntem: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.

### §3.1 Kullanılan kaynaklar (HTTP 200)

| # | URL | HTTP | Erişim | Kullanım |
|---|---|---|---|---|
| 1 | `https://www.vulkan.org/` | 200 | 2026-10-13 | Vulkan — G-T05/G-T06 |
| 2 | `https://www.opengl.org/` | 200 | 2026-10-13 | OpenGL — G-T06 |
| 3 | `https://developer.nvidia.com/` | 200 | 2026-10-13 | NVIDIA geliştirici — G-T01 |
| 4 | `https://www.khronos.org/` | 200 | 2026-10-13 | Khronos — API sahibi |
| 5 | `https://www.amd.com/en/products/technologies/rdna` | 200 | 2026-10-13 | AMD RDNA — G-T02 |

### §3.2 Kullanılmayan kaynaklar (erişilemedi → KAYNAK OLMADI)

| # | URL | HTTP | Karar |
|---|---|---|---|
| 1 | `https://www.intel.com/` | **403** | **KULLANILMADI** — G-T03 Intel iddiası yalnız repo ölçümüne dayanır |
| 2 | `https://www.amd.com/en/products/graphics` | **404** | **KULLANILMADI** — derin sayfa yok; rdna sayfası kullanıldı |
| 3 | `https://developer.arm.com/` | **403** | **KULLANILMADI** — Mali/Adreno derin sayfası doğrulanmadı |

### §3.3 cpu dalından ayrılan kaynaklar

| URL | Neden gpu'da |
|---|---|
| vulkan.org · opengl.org · khronos.org | Hesaplama/render API — gpu dalının konusu (cpu-kapsam.md §12 sınırı) |
| developer.nvidia.com · rdna | GPU üretici kaynakları |

**Kural:** gpu dosyalarında yalnız §3.1'deki 5 URL kaynak verilebilir; §3.2'deki
3 URL hiçbir gpu dosyasına kaynak yazılamaz.

### §3.4 URL Tekilleştirme Notu (G6 — K001 geneli)

| URL durumu | Kural |
|---|---|
| khronos.org kökü | K001'de başka dosyalar da kullanabilir — çakışma varsa ilk yazan korunur, son yazan not düşer |
| vulkan.org/opengl.org | gpu dalına özgü (cpu dosyalarına yazılmaz — cpu-kapsam.md §12) |
| rdna derin sayfası | gpu dalına özgü |
| developer.nvidia.com | gpu dalına özgü |
| 403/404 URL'ler | Hiçbir dosyaya KAYNAK yazılmaz; yalnız "KULLANILMADI" kaydı |

### §3.5 Doğrulama Zaman Damgası ve Tekrar Kuralı

| Kural | Değer |
|---|---|
| Doğrulama tarihi | 2026-10-13 (HEAD, oturum içi) |
| Geçerlilik | URL'ler **o an** doğrulandı; kalıcılık garantisi yok |
| Tekrar | Dal revizyonunda §3.1 tablosu yeniden HEAD'lenir |
| 403/406 değişirse | Yeni satır eklenir (eski silinmez) — fark kuralı §4.4 |
| Eski belge "200" iddiası | Bu dosya onaylamaz; o belge kendi işaretiyle kalır |

## §4 Repo Ölçüm Kaydı (2026-10-13)

| Parametre | Değer |
|---|---|
| Tarih | 2026-10-13 |
| Yöntem | `grep` + `filesystem` |
| Kapsam | Repo kökü; `node_modules/` · `vendor/` · `.git/` hariç |
| Fiziksel ölçüm | **YOK** |

### §4.1 Desen arama tablosu

| Desen | Dosya seti | Eşleşme | Yorum |
|---|---|---|---|
| `Vulkan\|OpenCL\|CUDA\|OpenGL` | `*.{php,c,cpp,h,hpp,cs}` | **0** | Sunucu/uygulama tarafında GPU API'si yok |
| `WebGL\|WebGPU` | `*.js` | **0** | İstemci tarafında GPU API'si yok |

### §4.2 Dosya uzantısı sayımı (cpu-dogrulama.md §4.1 ile aynı ölçüm seti)

| Uzantı | Sayı | gpu etkisi |
|---|---|---|
| `*.php` | 319 | GPU API = 0 (§4.1) |
| `*.js` | 98 | WebGL/WebGPU = 0 (§4.1) |
| `*.c` · `*.cpp` · `*.h` · `*.cs` | 0 (hepsi) | GPU derleme kodu yok |

### §4.3 Yorum (ölçümden çıkan sonuç — S2)

- Repo'da GPU API'si kullanımı **0** → "repo'da GPU kullanılmıyor" iddiası ölçüldü.
- Bu **donanım envanteri** iddiası DEĞİLDİR: sistemde GPU olabilir (UNKNOWN, K-G5).
- WebGL/WebGPU = 0 → UI render'ı GPU API'siyle programlanmıyor (tarayıcı işi).

### §4.4 Yeniden Ölçüm Protokolü

| Adım | İşlem | Beklenen |
|---|---|---|
| R1 | `Vulkan\|OpenCL\|CUDA\|OpenGL` → `*.{php,c,cpp,h,hpp,cs}` | 0 |
| R2 | `WebGL\|WebGPU` → `*.js` | 0 |
| R3 | Uzantı sayımı (php/js/native) | 319/98/0 |
| R4 | Fark varsa yeni satır ekle (eskiyi silme) + §9'a fark kaydı | Kayıt bütünlüğü |

### §4.5 Desen Kapsama Sınırları (bu ölçüm neyi KAPSAMAZ)

| Sınır | Açıklama |
|---|---|
| Vendor-specific desenler | `nvidia|amd|radeon|geforce` desenleri BU oturumda Ölçülmedi — ölçülseydi yorum satırları çıkabilirdi; kapsam dışı bırakıldı |
| Build/CI dosyaları | `.github/workflows/` içinde GPU runner aranmadı |
| Config dosyaları | `.json/.yaml` GPU yapılandırması aranmadı |
| Sonuç | "GPU API = 0" iddiası yalnız §4.1'deki iki desen ve iki dosya setiyle sınırlıdır |

### §4.6 Ölçüm Geçmişi Tablosu (bu dal)

| Tarih | Ölçümcü | Desen | Sonuç | Not |
|---|---|---|---|---|
| 2026-10-13 | Orkestratör | Vulkan\|OpenCL\|CUDA\|OpenGL → php/c/cpp/h/hpp/cs | 0 | İlk ölçüm |
| 2026-10-13 | Orkestratör | WebGL\|WebGPU → js | 0 | İlk ölçüm |
| 2026-10-13 | Orkestratör | uzantı sayımı | php 319 · js 98 · native 0 | cpu-dogrulama §4.1 ile paylaşımlı |
| — | — | Yeniden ölçüm | BEKLEMEDE | §4.4 protokolü uygulanınca |

## §5 Vault Disk Kanıtları (2026-10-13 okundu)

### §5.1 `donanım-anayasa.md` bağlayıcı satırlar

| Satır | Bağlam | gpu etkisi |
|---|---|---|
| 195 | §10 Açık Sorular/3 — ses işleme/DSP sınırı | GPU≠DSP; FIR/EQ offload PLANNED |
| 250 | §12/C10 — OPcache/JIT zorunlu | GPU PHP yürütmesine dokunmaz |
| 258 | §12.1 — performans/hedef bağlamı | GPU performans iddiası = ölçüm yok ⚠️ |

### §5.2 `donanaım-type-kapsam.md` bağlayıcı satırlar

| Satır | Bağlam | gpu etkisi |
|---|---|---|
| 46 | §2/A6 — NEEDS DISCOVERY | gpu tipi NEEDS DISCOVERY |
| 135 | §7/3-T1 — tür ayrım kuralı | gpu = tür dalı (hesaplama) |

### §5.3 ADR-102 (draft) — bilinen boşluk

| Öğe | Değer |
|---|---|
| Dosya | `.ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md` |
| İçerik | §4 5 dal sayar: display·cables·network·memory·cpu — **gpu YOK** |
| Sonuç | gpu eklenirse dal sayısı 9 olur; ADR metni "3 → 8" der |
| İşlem | **Değiştirilmedi** (salt-okunur); Q-GD4 — Vault Steward onayı REQUIRED |

### §5.4 Vault Steward onayı (S5)

| Öğe | Değer |
|---|---|
| Onay | Klasör açma ✓ (2026-10-13) |
| Kapsamı | 4 dosyalık gpu/ dalının üretimi |
| Kapsamı DIŞI | Anayasa/ADR/metin revizyonu — APPROVAL REQUIRED (gpu-kapsam.md §0.2) |

### §5.5 Ek Disk Kanıtları (gpu ile ilişki — cpu-dogrulama ile paylaşımlı ölçüm)

| Dosya | Kanıt | gpu etkisi |
|---|---|---|
| `shared/composer.json` | `"php": ">=8.4"` + ext-* | PHP yürütme bağlamı GPU içermez — §4.1 ile uyumlu |
| `.github/workflows/ci.yml` | `runs-on: ubuntu-latest` (6×) | CI'da GPU runner yok — GPU CI ölçümü de YOK |
| `.ai/.decisions/accepted/ADR-006-performance-targets.md:128` | OPcache/JIT zorunlu | PHP hedefi — GPU ile ilgisiz (sınır §5.1/250) |

### §5.6 DSP/K004 Sınır Referansı (gpu ↔ ses işleme)

| Öğe | Değer |
|---|---|
| Anayasa satırı | 195 (§10/3) — ses işleme/DSP sınırı açık soru |
| gpu tutumu | FIR/EQ offload **PLANNED** — hız iddiası YOK (§6/M9) |
| DSP çipi sahibi | K004 (XU316 vb. — gpu dosyalarına yazılmaz) |
| Ölçüm durumu | Offload ölçümü YOK → §8.3 formu boş |

## §6 İddia → Kanıt Matrisi (gpu-kapsam.md iddiaları)

| # | İddia | Kanıt | Seviye | Durum |
|---|---|---|---|---|
| M1 | 4 üretici ailesi kapsamda (NVIDIA/AMD/Intel/Mali-Adreno) | Görev tanımı · §3.1 (nvidia/rdna 200) | S3 | ✅ kaynaklı |
| M2 | 3 hesaplama API'si + 2 render API'si | §3.1 vulkan/opengl/khronos 200 | S3 | ✅ kaynaklı |
| M3 | Repo'da GPU API kullanımı 0 | §4.1 (2 desen, 0) | S2 | ✅ ölçüldü |
| M4 | Native GPU kodu yok | §4.2 (native=0) | S2 | ✅ ölçüldü |
| M5 | WebGL/WebGPU 0 | §4.1 | S2 | ✅ ölçüldü |
| M6 | ADR-102 gpu'yu saymıyor | §5.3 (dosya okundu) | S4 | ✅ kaydedildi · ⚠️ APPROVAL REQUIRED |
| M7 | Klasör açma onaylı | §5.4 (Vault Steward) | S5 | ✅ |
| M8 | GPU envanteri | Yok | — | ❌ UNKNOWN (K-G5) |
| M9 | FIR/EQ offload hızı | Yok — ölçüm YOK | — | ⚠️ VERIFICATION REQUIRED |
| M10 | Video donanım hızlandırma kullanımı | Yok | — | ❌ UNKNOWN |
| M11 | VRAM/bant genişliği değerleri | Yok | — | ⚠️ VERIFICATION REQUIRED |
| M12 | GPU sanallaştırma (passthrough) | Yok | — | ❌ UNKNOWN |

### §6.2 İddia → Kapı Eşleme (hangi iddia hangi kapıda kapanır)

| İddia | Kapı | Kapanış koşulu |
|---|---|---|
| M1 (üretici aileleri) | K-G1 | §3.1 URL'leri korunur |
| M2 (API'ler) | K-G2 | §3.1 API URL'leri korunur |
| M3/M4/M5 (repo 0) | K-G3 | §4.4 yeniden ölçüm aynı sonucu verir |
| M6 (ADR-102 boşluğu) | K-G6 | Vault Steward kararı verilir |
| M7 (onay) | K-G5 öncesi | Kalıcı (S5) |
| M8–M12 (envanter/ölçüm) | K-G5 · K-G3/S1 | §8 formları doldurulur |

## §7 Doğrulama Kapıları (K-G1 … K-G6)

### §7.1 K-G1 — Üretici kaynak doğrulaması (S3)

| Öğe | Değer |
|---|---|
| Tanım | Üretici/teknoloji kaynakları HEAD 200 mü? |
| Adım | §3.1 tablosu: nvidia (developer.nvidia.com) · amd (rdna) · vulkan · opengl · khronos |
| Geçme eşiği | Her aile için ≥1 URL 200 |
| Durum | ✅ **GEÇTİ** — NVIDIA 200 · AMD 200 · Vulkan/OpenGL/Khronos 200; Intel 403 → kaynak yok (§3.2/1), Intel iddiası repo ölçümüne dayalı |

### §7.2 K-G2 — API kaynak doğrulaması (S3)

| Öğe | Değer |
|---|---|
| Tanım | Hesaplama/render API kaynakları doğrulandı mı? |
| Adım | vulkan.org 200 · opengl.org 200 · khronos.org 200 (§3.1) |
| Geçme eşiği | ≥2 API kaynağı 200 |
| Durum | ✅ **GEÇTİ** — 3/3 |

### §7.3 K-G3 — Repo ölçüm tekrarlanabilirliği (S2)

| Öğe | Değer |
|---|---|
| Tanım | GPU grep ölçümleri tekrar edilebilir mi? |
| Adım | §4.4 R1–R3 |
| Geçme eşiği | Aynı sayılar (0 · 0 · 319/98/0) |
| Durum | ✅ **GEÇTİ (S2)** — bu oturumda alındı; ❌ **S1 AÇIK**: GPU donanım ölçümü hiç yapılmadı (Q-GD1) |

### §7.4 K-G4 — 26 alan × 7 tip bütünlüğü (S4)

| Öğe | Değer |
|---|---|
| Tanım | Her tip kaydı 26 alanın tamamını taşıyor mu? |
| Adım | gpu-kapsam.md §13.1–§13.7 sayımı: 7 × 26 = 182 satır |
| Geçme eşiği | Atlanan/silinmiş alan yok; boş alanlar `UNKNOWN`/`NOT PROVIDED` işaretli |
| Durum | ✅ **GEÇTİ** — 7/7 kayıt tam (§13.8 eşleme dâhil) |

### §7.5 K-G5 — Envanter/ölçüm (S1 — AÇIK)

| Öğe | Değer |
|---|---|
| Tanım | Sistemde GPU var mı, hangi rolde? |
| Adım | Envanter formu §8.1 doldurulmalı — bu oturumda doldurulmadı |
| Geçme eşiği | `gpu_present` + `gpu_role` + `api_support` girilmiş olmalı |
| Durum | ❌ **AÇIK** — envanter yok (Q-GD1) |

### §7.6 K-G6 — ADR-102 çelişki süreci (S5 — ONAY BEKLİYOR)

| Öğe | Değer |
|---|---|
| Tanım | ADR-102 boşluğu (gpu YOK) Vault Steward'a iletildi mi? |
| Adım | gpu-kapsam.md §0.1/8 + §5.3 kaydı yapıldı → revizyon kararı Vault Steward'da |
| Geçme eşiği | Vault Steward ADR-102 revizyonunu onaylar/reddeder |
| Durum | ⚠️ **BEKLEMEDE** — kayıt tam, karar verilmedi (Q-GD4) |

### §7.7 Kapı özeti tablosu

| Kapı | Konu | Seviye | Durum |
|---|---|---|---|
| K-G1 | Üretici kaynak doğrulama | S3 | ✅ GEÇTİ |
| K-G2 | API kaynak doğrulama | S3 | ✅ GEÇTİ |
| K-G3 | Repo ölçüm tekrarlanabilirliği | S2 (+S1 açık) | ✅ GEÇTİ (S2) / ❌ S1 AÇIK |
| K-G4 | 26 alan × 7 tip bütünlüğü | S4 | ✅ GEÇTİ |
| K-G5 | Envanter/ölçüm | S1 | ❌ AÇIK |
| K-G6 | ADR-102 çelişki süreci | S5 | ⚠️ BEKLEMEDE |

**Toplam:** 6 kapı · 3 tam geçti · 1 karışık (S2 geçti/S1 açık) · 1 açık · 1 bekleyede.

### §7.8 K-G5 Kapatma Protokolü (envanter S1 adımları)

| Adım | İşlem | Kayıt |
|---|---|---|
| E1 | GPU var mı belirle (Windows: `Get-CimInstance Win32_VideoController`; Linux: `lspci`) | §8.1 `gpu_present` |
| E2 | Üretici/model/sürücü kaydet | §8.1 satırları |
| E3 | Desteklenen API'leri kaydet (CUDA/OpenCL/Vulkan — gerçek çıktıdan) | §8.1 `api_support` |
| E4 | VRAM/bilgi oku | §8.1 |
| E5 | `measured_by`/`measured_at` ile imzala | §8.1 son satır |
| E6 | gpu-dogrulama §7.7 tablosunu güncelle | K-G5 kapanışı |

**Geçme eşiği:** E1–E4'te tek `UNKNOWN` kalmazsa K-G5 kapanır.

### §7.9 K-G3 S1 Kapatma Protokolü (performans ölçümü)

| Adım | İşlem | Kayıt |
|---|---|---|
| P1 | Ölçüm ortamını sabitle (GPU+API+sürücü) | §8.2 başlık |
| P2 | Hesaplama ölçümü (varsa FIR iş yükü; yoksa genel döngü) | §8.2 `compute_benchmark` |
| P3 | Render ölçümü (frame zamanı — varsa UI senaryosu) | §8.2 `render_frame_time` |
| P4 | CPU tekilleme ölçümüyle karşılaştır (varsa) | §8.2 `cpu_vs_gpu_note` |
| P5 | Sonuçları ⚠️ işaretleriyle değiştir | §8.2 → gerçek değerler |

**Kural:** P2–P4 yapılmadan §8.2'deki `⚠️ VERIFICATION REQUIRED` işaretleri **kaldırılamaz**.

### §7.10 K-G6 Karar Seçenekleri (ADR-102 revizyonu — Vault Steward masası)

| Seçenek | Sonuç | Etkilenen dosyalar |
|---|---|---|
| Onay: gpu ADR-102'ye eklenir | Dal sayısı 9 olur; "3 → 8" ifadesi "3 → 9" olur | ADR-102 (draft) + donanaım-type-kapsam §7 + anayasa (gerekirse) |
| Red: gpu dalı kapatılır | 4 dosya arşivlenir; sayım 8'de kalır | gpu/ klasörü |
| Erteleme: draft bekletilir | gpu/ "PROPOSED" statüsünde kalır (mevcut durum) | — |
| Böl: gpu cpu'ya birleştirilir | cpu dalı genişler; gpu/ kapatılır | cpu-kapsam.md + cpu/ dosyaları |

**Bu dosyanın tutumu:** Seçenek **seçilmedi** — bu dosya yalnız seçenekleri ve
etkilerini kaydeder; karar Vault Steward'ındır (Q-GD4).

## §8 Envante/Form Kayıtları (hepsi BOŞ — doldurulmayı bekliyor)

### §8.1 GPU envanter formu (BOŞ)

| Alan | Değer |
|---|---|
| gpu_present | `UNKNOWN` (var/yok) |
| gpu_vendor | `UNKNOWN` (NVIDIA · AMD · Intel · ARM · yok) |
| gpu_model | `UNKNOWN` |
| vram | `UNKNOWN` |
| api_support | `UNKNOWN` (CUDA · OpenCL · Vulkan) |
| driver_version | `UNKNOWN` |
| role | `UNKNOWN` (render · hesap · video · yok) |
| measured_by / measured_at | `NOT PROVIDED` |

### §8.2 Performans ölçüm formu (BOŞ — S1)

| Alan | Değer |
|---|---|
| compute_benchmark | `⚠️ VERIFICATION REQUIRED` |
| render_frame_time | `⚠️ VERIFICATION REQUIRED` |
| memory_bandwidth | `⚠️ VERIFICATION REQUIRED` |
| video_decode_note | `⚠️ VERIFICATION REQUIRED` |
| cpu_vs_gpu_note | `NOT PROVIDED` |
| measured_by / measured_at | `NOT PROVIDED` |

### §8.3 FIR/EQ offload senaryo formu (BOŞ — PLANNED)

| Alan | Değer |
|---|---|
| scenario | `NOT PROVIDED` (hangi iş yükü) |
| api_choice | `UNKNOWN` (CUDA · OpenCL · Vulkan) |
| speedup_vs_cpu | `⚠️ VERIFICATION REQUIRED` |
| latency_impact | `⚠️ VERIFICATION REQUIRED` |
| decision | `PLANNED` (karar yok) |

### §8.4 Form Doldurma Kuralları

| # | Kural |
|---|---|
| 1 | Form yalnız Vault Steward onayıyla doldurulur |
| 2 | Her değer ölçüm kaynağı taşır (komut çıktısı + tarih + kişi) |
| 3 | `UNKNOWN` atlanmaz — doldurulana kadar kalır |
| 4 | ⚠️ işareti yalnız S1 ölçümüyle kaldırılır |
| 5 | Ölçüm farkında eski satır silinmez (§4.4 fark kuralı) |
| 6 | Doldurunca §7.7 kapı tablosu yeniden yazılır |

### §8.5 Form Gönderim Formatı (Vault Steward girişi)

| Alan | Format | Biçim örneği (gerçek değer DEĞİL) |
|---|---|---|
| gpu_present | evet · hayır · UNKNOWN | UNKNOWN (mevcut durum) |
| gpu_vendor | NVIDIA · AMD · Intel · ARM · UNKNOWN | UNKNOWN |
| vram | sayı + birim | NOT PROVIDED |
| api_support | virgülle liste | NOT PROVIDED |
| measured_at | YYYY-MM-DD | NOT PROVIDED |

**Biçim kuralı:** Yukarıdaki sütun yalnız **biçimi** gösterir; gerçek değer
girilmemiştir (hepsi UNKNOWN/NOT PROVIDED). Değer girilince hücre gerçek veriyle
değişir ve ölçümlü olur.

## §9 UNKNOWN / VERIFICATION REQUIRED Kaydı

| # | Belirsizlik | İşaret | Kapanış koşulu |
|---|---|---|---|
| U-G1 | GPU envanteri | UNKNOWN | §8.1 doldurulunca (K-G5) |
| U-G2 | FIR/EQ offload hızı | ⚠️ VERIFICATION REQUIRED | §8.3 + ölçüm (K-G3/S1) |
| U-G3 | Video donanım hızlandırma | UNKNOWN | §8.1 `role` girilince |
| U-G4 | VRAM/bant genişliği | ⚠️ VERIFICATION REQUIRED | §8.2 ölçüm (K-G3/S1) |
| U-G5 | ADR-102 revizyonu | APPROVAL REQUIRED | Vault Steward kararı (K-G6) |
| U-G6 | GPU sanallaştırma | UNKNOWN | Envanter girişi (K-G5) |

### §9.1 Açık Soru İzleme Adımları

| Adım | İşlem |
|---|---|
| 1 | §8 formlarını Vault Steward doldurur |
| 2 | Kapanan soruları bu tabloda işaretle (silme, işaretleme) |
| 3 | §7.7 kapı tablosunu güncelle |
| 4 | gpu-kapsam.md §13 ilgili satırlarını `UNKNOWN` → değer çevir |
| 5 | Revizyonu Authority satırıyla kapat |

## §10 Dürüstlük Bloğu ve Kapanış

| Kural | Durum |
|---|---|
| Uydurma model/ölçüm/URL yok | ✅ (§3/§4 ölçüm kaydı; §6 hız iddiası yasağı) |
| Yer tutucu dizgesi yok | ✅ (boş alanlar UNKNOWN/NOT PROVIDED) |
| Fiziksel ölçüm 0 → ⚠️ | ✅ (§1 + §8.2/§8.3) |
| `parent:` anahtarı yok | ✅ (frontmatter `parent_id`) |
| Intel/ARM portalı kaynak değil | ✅ (§3.2 KULLANILMADI) |
| ADR-102 salt-okunur kaldı | ✅ (§5.3) |

**Kapanış (2026-10-13):** 6 kapıdan 3'ü tam geçti (K-G1/G2/G4), 1'i S2'de geçti/S1
açık (K-G3), 1'i envanter eksikliğiyle açık (K-G5), 1'i onay bekliyor (K-G6). gpu
dalı bu nedenle **tanım + envanter bekleyen** seviyededir; performans iddiası
taşımaz. Sonraki adım: §8 formları + K-G6 kararı.

### §10.1 Sonraki Adımlar

| # | Adım | Sahibi | Kapı |
|---|---|---|---|
| 1 | §8.1 envanter formunu doldur | Vault Steward | K-G5 |
| 2 | §7.9 performans protokolünü çalıştır | Vault Steward | K-G3/S1 |
| 3 | K-G6 kararını ver (§7.10 seçenekleri) | Vault Steward | K-G6 |
| 4 | Karar sonrası §7.7 tablosunu güncelle | Dosya sahibi | — |
| 5 | Karar sonrası gpu-kapsam.md §0.2'yi güncelle | Dosya sahibi | — |

### §10.2 Kayıp/Karışıklık Riskleri (bu dalın özgü)

| # | Risk | Belirti | Önlem |
|---|---|---|---|
| R1 | "GPU var" sanrısı | Envanter olmadan §13'e değer yazma | UNKNOWN/⚠️ işaretleri (§8.1 boş) |
| R2 | display/ ile sınır karışması | Panel gecikmesi bu dosyaya sızmak | §5.6 + gpu-kapsam §12.1 |
| R3 | CPU SIMD ile karıştırılma | AVX/NEON gpu dosyasına girmek | cpu-kapsam §12 sınırı |
| R4 | Ölçümsüz hız iddiası | "X kat hızlı" cümlesi | §6/M9 + §9 hız yasağı |
| R5 | ADR-102 sessiz değişikliği | Draft'ın okunmadan güncellenmesi | §5.3 + Q-GD4 (onay zorunlu) |

## §11 Revizyon İzi

| Tarih | Revizyon | Not |
|---|---|---|
| 2026-10-13 | r1 | İlk yazım (gpu/ dalı — Vault Steward klasör açma onayıyla) |

### §11.1 Revizyon Tetikleyicileri

| Tetikleyici | Etkilenen bölüm |
|---|---|
| §8.1 envanter doldurulunca | §7.7 (K-G5) · §9 (U-G1/U-G3/U-G6) · gpu-kapsam §13 satırları |
| §8.2/§8.3 ölçüm girilince | §7.7 (K-G3/S1) · §9 (U-G2/U-G4) · gpu-kapsam §1 (⚠️ kaldırma) |
| K-G6 kararı verilince | §5.3 · §7.7 · gpu-kapsam §0.1/§0.2 · index.md §3 |
| URL'lerden biri 404/403 olursa | §3.1/§3.2 (yeni satır) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
