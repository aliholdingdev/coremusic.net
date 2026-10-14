---
title: "donanaım-type/gpu — Örnekler (Render/Hesaplama Ayrım Örnekleri, Çalışma Sayfaları, Şablonlar)"
type: kod
node_id: K001-donanım/donanaım-type/gpu/ornekler
node_type: donanaım-type-ornekler
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - index.md
  - gpu-kapsam.md
  - gpu-dogrulama.md
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Repo kod ölçümü 2026-10-13: Vulkan|OpenCL|CUDA|OpenGL → *.{php,c,cpp,h,hpp,cs} = 0 · WebGL|WebGPU → *.js = 0 · php=319 · js=98 · native=0 → GPU API GERÇEK KODU YOK"
  - "Bu dosya yalnızca ŞABLON ve ÇALIŞMA SAYFASI içerir — gerçek üretim kodu değil, doldurulacak formlar; PLANNED bloklar çalıştırılmadı"
validation: "proposed — 2026-10-13 yazıldı; anayasa revizyonu YAPILMADI (gpu-kapsam.md §0); 0 gerçek GPU API kodu; 0 fiziksel ölçüm"
open_questions:
  - "Q-G7: GPU envanter scripti (OS enumerasyonu + VRAM okuma) ne zaman yazılacak? — bu turda YOK"
  - "Q-G8: VRAM bant/genişlik ve GPU yükü ölçümü ne zaman yapılacak? — kapı K-G3/S1"
group: kod
date: 2026-10-13
updated: 2026-10-13
---

# gpu — Örnekler (K001-donanım · donanaım-type · gpu)

> Bu dosya `gpu/` sınıfının **örnek/şablon** kaydıdır. **Önemli (Truth Mode):**
> bu klasörde gerçek GPU API **kod dosyası YOKTUR** (repo ölçümü 2026-10-13:
> `Vulkan|OpenCL|CUDA|OpenGL` → `*.{php,c,cpp,h,hpp,cs}` = **0**;
> `WebGL|WebGPU` → `*.js` = **0**); bu dosyadaki her şey **doldurulacak
> şablon / çalışma sayfası**dır — üretim kodu değil, uydurma kod değil.
> Kapsam/çelişki: `gpu-kapsam.md` §0 · kapılar: `gpu-dogrulama.md` §7 (K-G1…K-G6).

---

## §1 Gerçek Repo Kodu — GPU API YOK (Kanıtla)

### §1.1 API desen araması (ölçüm: 2026-10-13)

| # | Desen | Dosya seti | Eşleşme | Yorum |
|---|---|---|---|---|
| 1 | `Vulkan\|OpenCL\|CUDA\|OpenGL` | `*.{php,c,cpp,h,hpp,cs}` | **0** | sunucu/uygulama tarafında GPU API'si yok |
| 2 | `WebGL\|WebGPU` | `*.js` | **0** | istemci tarafında GPU API'si yok |

### §1.2 Dosya uzantısı sayımı (aynı ölçüm seti)

| # | Dosya tipi | Repo sayısı | Yorum |
|---|---|---|---|
| 1 | `*.php` | 319 | GPU API = 0 (§1.1) — web uygulaması |
| 2 | `*.js` | 98 | WebGL/WebGPU = 0 (§1.1) — frontend |
| 3 | `*.c` | 0 | GPU derleme kodu yok |
| 4 | `*.cpp` · `*.h` · `*.hpp` · `*.cs` | 0 | — |

**Sonuç:** `gpu/` sınıfının kod yüzeyi **boştur**; bu dosya yalnızca
gelecekteki kod/ölçüm oturumları için **şablon** sağlar. **Önemli sınır:**
bu ölçüm **donanım envanteri DEĞİLDİR** — sistemde GPU olabilir (UNKNOWN ·
K-G5 · `gpu-dogrulama.md` §4.3).

---

## §2 Render vs Hesaplama Ayrım Örneği (Doğru/Yanlış Kayıt)

> Bu bölüm `gpu-kapsam.md` §5/§9 (hesaplama API'leri) ve §13.5/§13.6
> (G-T05/G-T06) ayrımının **uygulama örneğidir**.

### §2.1 Yanlış (rol karıştırıldı — RED)

```text
YANLIŞ KAYIT:
  type     : "CUDA render motoru"
  neden    : CUDA bir HESAPLAMA API'sidir (G-T05) — render pipeline (G-T06)
             ile tek tipte birleşti; rol ayrımı yok.
  sonuç    : RED — rol/katman karışımı.
```

### §2.2 Doğru (rol ayrımı korundu)

```text
DOĞRU KAYITLAR (ikisi ayrı):
  [a] G-T05 hesaplama API'si (yazılım yüzeyi — donanım DEĞİL):
        physical_component_or_device : YOK — API, donanım değildir
        owner_domain                 : K000 / uygulama
        vendor_source                : developer.nvidia.com (HEAD 200 · 2026-10-13)
        status                       : proposed (API kaydı)
  [b] GPU donanımı (fiziksel — envanter bekliyor):
        physical_component_or_device : GPU (marka/model: UNKNOWN — K-G5)
        owner_domain                 : K001 envanter · sürücü K000
        status                       : proposed
```

### §2.3 Fiziksellik/rol etiketleme şablonu (her kayıt için)

```text
KAYIT ŞABLONU (26 alan — doldurulacak):
  [ 1] type_id                      : G-T__
  [ 2] name_tr                      : 
  [ 3] name_en                      : 
  [ 4] category                     : gpu
  [ 5] subcategory                  : uretici-aile | hesaplama-api | render-api | video-kodlama
  [ 6] description                  : 
  [ 7] physical_component_or_device :  (API/sınır kaydında: YOK)
  [ 8] primary_function             :  (render | compute | video | envanter)
  [ 9] interfaces                   :  (PCIe · display çıkışı · video çıkışı ⚠️)
  [10] protocols                    :  (yalnız etiket)
  [11] power_requirements           : ⚠️ VERIFICATION REQUIRED
  [12] operating_environment        : 
  [13] os_dependencies              :  (K000)
  [14] driver_requirements          :  (K000 — §4.4)
  [15] firmware_dependency          :  (K000)
  [16] hardware_abstraction_requirement : 
  [17] audio_relevance              :  (GPU ile ses ilişkisi YOK varsayılır — K004 sınırı)
  [18] coremusic_relevance          : 
  [19] parent_domain                : K001-donanım
  [20] owner_domain                 :  (K001 envanter · K000 sürücü/API)
  [21] existing_document            :  (gpu-kapsam.md §13.x)
  [22] evidence                     :  (HEAD 200 URL · repo ölçümü)
  [23] status                       : proposed
  [24] confidence                   : low | medium | high
  [25] open_questions               : 
  [26] proposed_path                : donanaım-type/gpu/
```

---

## §3 Sınır Ayrımı Kasası (bu dalda ne YOK)

### §3.1 Kapsam DIŞINA devredilenler (`gpu-kapsam.md` §3 ile aynı eksen)

| # | Dışarıda olan | Nereye | Gerekçe |
|---|---|---|---|
| 1 | Panel/gecikme/refresh | `display/` dalı | ekran donanımı |
| 2 | AVX/NEON (CPU SIMD) | `cpu/` dalı | CPU talimat seti |
| 3 | DSP zinciri (FIR/EQ offload tasarımı) | K004 | ses işleme |
| 4 | GPU **sürücüsü**/API kullanımı kodu | K000 | yazılım |
| 5 | GPU **sanallaştırma** politikası | K000 | OS katmanı |
| 6 | Video **oynatma** yazılımı | uygulama/K000 | donanım hızlandırıcı ≠ oynatıcı |
| 7 | Bellek **yönetimi** (VRAM tahsisi) | K000 | algoritma |
| 8 | GPU güç/PCB/sinyal tasarımı | K002-electroncis | ADR-102 §4.2 |

### §3.2 3 test uygulaması (fiziksel / yalnız kod / sürüm)

| # | Öğe | T1 fiziksel? | T2 yalnız kod? | Karar |
|---|---|---|---|---|
| 1 | GPU kartı / iGPU | ✅ | — | **bu dal** |
| 2 | VRAM çipi/modülü | ✅ | — | **bu dal** |
| 3 | `CUDA` çağrısı | ❌ | ✅ | **K000** |
| 4 | `WebGL` çağrısı | ❌ | ✅ | **K000** (tarayıcı) |
| 5 | GPU sürücü sürümü | ❌ | sürüm | **K000** |
| 6 | Video kod donanımı (çip üstü blok) | ✅ | — | **bu dal** (G-T07) |
| 7 | Kodlama **çözümü** (FFmpeg benzeri) | ❌ | ✅ | **K000** |

---

## §4 Boş Çalışma Sayfaları (doldurulacak)

### §4.1 GPU envanter sayfası (K-G5 — `gpu-dogrulama.md` §8.1 ile eşleşir)

| # | Alan | Değer | Kanıt | Tarih |
|---|---|---|---|---|
| 1 | GPU marka/model | `UNKNOWN` | — | — |
| 2 | Rol (bütünleşik/ayrı) | `UNKNOWN` | — | — |
| 3 | VRAM kapasitesi | `UNKNOWN` | — | — |
| 4 | Bellek tipi | `UNKNOWN` | — | — |
| 5 | Video kod donanımı (var mı?) | `UNKNOWN` | — | — |
| 6 | Hesaplama API'si (donanım destekli) | `UNKNOWN` | — | — |
| 7 | Sanallaştırma (var mı?) | `UNKNOWN` | — | — |

### §4.2 OS enumerasyonu sayfası (PLANNED — çalıştırılmadı)

| # | Kayıt | Yön | Değer | Kaynak | Tarih |
|---|---|---|---|---|---|
| 1 | OS listelediği GPU sayısı | K000 → bu dal | `MEASURE` | — | `YYYY-MM-DD` |
| 2 | Görüntüleyici (display) adı | K000 → bu dal | `MEASURE` | — | `YYYY-MM-DD` |
| 3 | Sürücü sürümü | K000 | `MEASURE` | — | `YYYY-MM-DD` |
| 4 | VRAM okunan değer | K000 → bu dal | `MEASURE` | — | `YYYY-MM-DD` |

> **Sınır**: enumerasyon **yöntemi** (araç/komut) K000'de (`os-donanim-tespiti`
> sahipliği); **bulunan donanım gerçeği** bu dalın envanterine işlenir.

### §4.3 VRAM sayfası (BOŞ — ölçüm yok)

| # | Metrik | Değer | Alet | Tarih |
|---|---|---|---|---|
| 1 | Kapasite (GB) ⚠️ | — | — | — |
| 2 | Bant genişliği (GB/s) ⚠️ | — | — | — |
| 3 | Veri yolu genişliği (bit) ⚠️ | — | — | — |
| 4 | Hız (MT/s/eşdeğeri) ⚠️ | — | — | — |

### §4.4 Sürücü bağlama sayfası (K000 sınırı — boş)

| # | Senaryo | Bu dalın kaydı | Devredilen (K000) |
|---|---|---|---|
| 1 | GPU sürücü kurulumu | donanım varlığı | kurulum |
| 2 | Sürücü sürüm değişimi | — | sürüm |
| 3 | `bind`/`unbind` | yok | tamamen K000 |
| 4 | Düşük profilli/karmaşık (hybrid) geçiş | donanım **kabiliyeti** ⚠️ | politika K000 |
| 5 | Sürücü çökmesi / cihaz kaybı | yok | K000 |

---

## §5 Doldurulmuş Örnek Kayıt (şablon — G-T05 mantığı)

> Aşağıdaki kayıt **örnek/şablondur**; değerler `gpu-kapsam.md` §13.5'teki
> G-T05 kaydının **görsel provası**dır (uydurma değer yok).

| # | Alan | Değer |
|---|---|---|
| 1 | `type_id` | G-T05 |
| 2 | `name_tr` | Hesaplama API'leri (CUDA / OpenCL / Vulkan compute) |
| 3 | `name_en` | compute APIs |
| 4 | `category` | gpu |
| 5 | `subcategory` | `hesaplama-api` |
| 6 | `description` | GPU üzerinde genel amaçlı hesaplama arayüzleri — **API katmanı** |
| 7 | `physical_component_or_device` | **YOK** (API donanım değildir) |
| 8 | `primary_function` | paralel hesaplama arayüzü |
| 9 | `interfaces` | sürücü/API katmanı (donanıma bağımlı) |
| 10 | `protocols` | API adları — yalnız etiket |
| 11 | `power_requirements` | K001'de değil (API) |
| 12 | `operating_environment` | — |
| 13 | `os_dependencies` | K000 (sürücü yığını) |
| 14 | `driver_requirements` | K000 (§4.4) |
| 15 | `firmware_dependency` | K000 |
| 16 | `hardware_abstraction_requirement` | envanter tipi — soyutlama K000 |
| 17 | `audio_relevance` | OFFLOAD senaryosu PLANNED (`gpu-dogrulama.md` §8.3) |
| 18 | `coremusic_relevance` | envanter tipi |
| 19 | `parent_domain` | K001-donanım |
| 20 | `owner_domain` | API → K000 · donanım → K001 |
| 21 | `existing_document` | `gpu-kapsam.md` §13.5 |
| 22 | `evidence` | repo API=0 (§1.1) · developer.nvidia.com 200 (HEAD 2026-10-13) |
| 23 | `status` | proposed |
| 24 | `confidence` | low |
| 25 | `open_questions` | Q-G7 · Q-G8 |
| 26 | `proposed_path` | `donanaım-type/gpu/` |

---

## §6 Kapı Formu (K-G5 / K-G3-S1 — BOŞ)

```text
KAPı FORMU (doldurulacak):
  oturum ID   : 
  tarih       : 
  alet/araç   : 
  tip         : G-T__
  ölçüm türü  : envanter | VRAM kapasite | VRAM bant | GPU yükü | enumerasyon
  hedef değer : (katalog/ölçüm — tahmin YASAK)
  ölçülen     : 
  sonuç       : GEÇ | GEÇMEDİ
  kaydeden    : 
```

**Doldurma kuralı**: form yalnız **gerçek çıktı** ile doldurulur;
`gpu-dogrulama.md` §8.5 (Vault Steward girişi) ile aynı formattadır.

---

## §7 PLANNED Kod Blokları (ŞABLON — çalıştırılmadı, üretim kodu DEĞİL)

> **Truth Mode**: aşağıdaki bloklar **iskele/şablondur**; repo'da karşılığı
> **yoktur** (§1.1 ölçümü = 0). **Çalıştırılmadı** · **derlenmedi** ·
> **doğrulanmadı**. `PLACEHOLDER`/`UNKNOWN` alanlar doldurulmadan **kopyalanıp
> üretime alınmaz**.

### §7.1 Render ve hesaplama API kontrol bloğu (PLANNED)

```text
[PLANNED — çalıştırılmadı]
  render  : GPU'nun render API'sine erişilebilirliği
    1) OS enumerasyonu (K000 aracı) → GPU listesi
    2) Render API'si kullanılabilir mi? (evet/hayır — kod YOK, yalnız sonuç)
    3) Sonuç → §4.2/§4.1 satırına yaz (kanıt + tarih)
    kural : API adı = etiket; "çalışıyor" iddiası = ölçüm kaydı (K-G3)
  compute: donanım destekli hesaplama API'si var mı?
    API yüzeyi (etiket): CUDA · OpenCL · Vulkan compute
    kaynak : developer.nvidia.com (HEAD 200 · 2026-10-13) · vulkan.org (200)
    işlem : envanter satırına "desteklenen API" olarak işlenir — sürüm YAZILMAZ
    yasak  : "X TFLOPS" / "Y kat hızlı" — ölçüm yok (K-G3/S1)
```

### §7.2 VRAM okuma bloğu (PLANNED)

```text
[PLANNED — çalıştırılmadı]
  amaç   : VRAM kapasitesi + bant genişliği okuma
  adım 1 : OS enumerasyonu → kapasite (GB) → §4.3/1
  adım 2 : bant genişliği ölçümü (araç gerekli) → §4.3/2
  adım 3 : katalog değeri ile karşılaştır → fark kaydı (ekle-silme)
  kural  : katalog değeri = kaynak gerekir; kaynak yoksa ⚠️ VERIFICATION REQUIRED
```

---

## §8 Tip-Bazlı Katman Provası (G-T01…G-T07 — doldurulacak)

### §8.1 G-T01 NVIDIA

```text
ROL        : üretici ailesi (donanım)
KAYNAK     : developer.nvidia.com — HEAD 200 · 2026-10-13
ENVANTER   : marka/model UNKNOWN (K-G5)
ÖLÇÜM      : — (K-G3/S1 açık)
```

### §8.2 G-T02 AMD (RDNA/Radeon)

```text
ROL        : üretici ailesi (donanım)
KAYNAK     : amd.com/en/products/technologies/rdna — HEAD 200 · 2026-10-13
ENVANTER   : UNKNOWN (K-G5)
UYARI      : amd.com/en/products/graphics → 404 — KULLANILMADI
```

### §8.3 G-T03 Intel entegre grafik · G-T04 ARM Mali / Adreno

```text
[G-T03 Intel entegre grafik]
  ROL        : entegre grafik (donanım)
  KAYNAK     : YOK — intel.com HEAD 403 → KULLANILMADI
  İDDİA DAYANA: yalnız repo ölçümü (S2); web kaynağı YOK → ⚠️ VERIFICATION REQUIRED
[G-T04 ARM Mali / Adreno]
  ROL        : mobil/SoC grafik (donanım)
  KAYNAK     : YOK — developer.arm.com HEAD 403 → KULLANILMADI
  İDDİA DAYANA: yok → ⚠️ VERIFICATION REQUIRED
```

### §8.4 G-T05 Hesaplama API'leri · G-T06 Render pipeline

```text
[G-T05 hesaplama API'si]
  ROL        : hesaplama API'si (yazılım yüzeyi — donanım DEĞİL)
  KAYNAK     : vulkan.org (200) · developer.nvidia.com (200)
  REPO ÖLÇÜM : CUDA|OpenCL|Vulkan = 0 (§1.1)
[G-T06 render pipeline]
  ROL        : render API'si (yazılım yüzeyi)
  KAYNAK     : vulkan.org (200) · opengl.org (200) · khronos.org (200)
  REPO ÖLÇÜM : OpenGL|Vulkan = 0 (§1.1) · WebGL|WebGPU = 0 (§1.1)
```

### §8.5 G-T07 Video kod çözme/kodlama

```text
ROL        : video hızlandırma donanımı (çip üstü blok)
ENVANTER   : UNKNOWN (K-G5 — `role` alanı boş)
UYARI      : "video hızlandırma VAR" cümlesi envanter gelmeden YAZILMAZ
```

---

## §9 Eksiklik ve Referans Matrisleri

### §9.1 Eksiklik matrisi (7 tip × eksik alan)

| Tip | ENVANTER eksik | Ölçüm eksik | Kaynak eksik |
|---|---|---|---|
| G-T01 | marka/model/rol | VRAM · yük | — (200 var) |
| G-T02 | marka/model/rol | VRAM · yük | — (200 var) |
| G-T03 | marka/model/rol | VRAM · yük | **TÜMÜ** (403) |
| G-T04 | marka/model/rol | VRAM · yük | **TÜMÜ** (403) |
| G-T05 | destekli API listesi | offload (§8.3 dogrulama) | — (200 var) |
| G-T06 | — | render ölçümü | — (200 var) |
| G-T07 | video donanımı var mı | kodlama ölçümü | — |

**Toplam**: 7 tip × (envanter + ölçüm) = **14 eksik alan** · 0/14 dolduruldu
(bu oturumda envanter/ölçüm YOK) → tüm tipler `proposed`.

### §9.2 Tip → Dosya referans matrisi

| # | Tip | Kapsam kaydı | Doğrulama kaydı | Örnek/şablon |
|---|---|---|---|---|
| 1 | G-T01 NVIDIA | `gpu-kapsam.md` §13.1 | `gpu-dogrulama.md` §6 | bu dosya §8.1 |
| 2 | G-T02 AMD | §13.2 | §6 | §8.2 |
| 3 | G-T03 Intel | §13.3 | §6 | §8.3 (G-T03) |
| 4 | G-T04 ARM | §13.4 | §6 | §8.3 (G-T04) |
| 5 | G-T05 hesaplama | §13.5 | §6 · §7.2 | §8.4 · §2 |
| 6 | G-T06 render | §13.6 | §6 · §7.2 | §8.4 · §2 |
| 7 | G-T07 video | §13.7 | §6 | §8.5 |

> **Tutarlılık**: her tipin üç dosyada karşılığı vardır (`gpu-kapsam.md` §22
> bu dosyayı referanslar); eksik karşılık = şema ihlali.

---

## §10 URL Kaynak Defteri (HEAD — 2026-10-13)

### §10.1 Kullanılan kaynaklar (HTTP 200 — bu dosyada atıf olabilir)

| # | URL | HTTP | Kullanım |
|---|---|---|---|
| 1 | `https://www.vulkan.org/` | 200 | Vulkan — G-T05/G-T06 (§7.1, §8.4) |
| 2 | `https://www.opengl.org/` | 200 | OpenGL — G-T06 (§8.4) |
| 3 | `https://developer.nvidia.com/` | 200 | NVIDIA — G-T01/G-T05 (§8.1, §2.2) |
| 4 | `https://www.khronos.org/` | 200 | Khronos — API sahibi kuruluş |
| 5 | `https://www.amd.com/en/products/technologies/rdna` | 200 | AMD RDNA — G-T02 (§8.2) |

### §10.2 Kullanılmayan kaynaklar (erişilemedi → KAYNAK OLMADI)

| # | URL | HTTP | Karar |
|---|---|---|---|
| 1 | `https://www.intel.com/` | **403** | **KULLANILMADI** — G-T03 kaynaksız (§8.3) |
| 2 | `https://www.amd.com/en/products/graphics` | **404** | **KULLANILMADI** |
| 3 | `https://www.khronos.org/` dışındaki derin sayfalar | ölçülmedi | bu oturumda **HEAD yapılmadı** → atıf yazılmaz |

**Kural** (`gpu-dogrulama.md` §3.3): bu dosyada **yalnız** §10.1'deki URL
kaynak yazılabilir; §10.2'deki URL **hiçbir satırda atıf olamaz**. Yöntem:
`Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` ·
erişim **2026-10-13** · kalıcılık garantisi YOK.

---

## §11 PLANNED Ölçüm — VRAM Bant Genişliği ve GPU Yükü [KANIT]

> **[KANIT] işareti**: bu bölümün **tüm** eşik adayları **kanıt bekliyor**
> (Vault Steward onayı + gerçek ölçüm). **Ölçüm = 0** — bu oturumda **hiçbir**
> GPU ölçümü yapılmadı. Eşikler **kaynak değil, adaydır** → ⚠️ VERIFICATION REQUIRED.

### §11.1 Aday eşik tablosu [KANIT]

| # | Metrik | Aday eşik (kaynak DEĞİL) | İşaret | Ölçüm |
|---|---|---|---|---|
| 1 | VRAM bant genişliği | donanım katalog değeri ile karşılaştırma ⚠️ | [KANIT] | **0** |
| 2 | GPU yükü (idle) | ⚠️ (eşik belirlenmedi) | [KANIT] | **0** |
| 3 | GPU yükü (render altında) | ⚠️ | [KANIT] | **0** |
| 4 | Sıcaklık/termal throttling | ⚠️ | [KANIT] | **0** |
| 5 | FIR/EQ offload hızı | ⚠️ (`gpu-dogrulama.md` §8.3 — U-G2) | [KANIT] | **0** |
| 6 | Video kodlama hızı | ⚠️ (envanter önce — G-T07) | [KANIT] | **0** |

### §11.2 Ölçüm kayıt formu (BOŞ — measurement = 0)

| # | Metrik | Değer | Alet | Ortam | Tarih |
|---|---|---|---|---|---|
| 1 | VRAM bant (GB/s) | `MEASURE` | — | — | `YYYY-MM-DD` |
| 2 | VRAM kapasite (GB) | `MEASURE` | — | — | `YYYY-MM-DD` |
| 3 | GPU yükü idle (%) | `MEASURE` | — | — | `YYYY-MM-DD` |
| 4 | GPU yükü altında (%) | `MEASURE` | — | — | `YYYY-MM-DD` |
| 5 | Sıcaklık (°C) | `MEASURE` | — | — | `YYYY-MM-DD` |

### §11.3 Ölçüm protokolü (20 adım — çalıştırılmadı)

```text
[1]  Oturum başlat: ID · tarih · makine/OS · araç adı+sürüm (gerçekse)
[2]  §4.1 envanter satırını tazele (K-G5 formu)
[3]  OS enumerasyonu (K000 aracı) → §4.2/1-4
[4]  VRAM kapasitesini kaydet → §11.2/2
[5]  Katalog bant genişliğini KAYNAKLA al (§10.1 URL) — tahmin YASAK
[6]  Ölçüm aracı kurulumunu doğrula (kurulu değilse DUR — araç adı yazılmaz)
[7]  VRAM bant ölçümü: 3× tekrar → medyan → §11.2/1
[8]  Idle GPU yükü: 60 s gözlem → medyan → §11.2/3
[9]  Render altında GPU yükü: PLANNED iş yükü → §11.2/4
[10] Sıcaklık okuması (varsa) → §11.2/5
[11] Sonuçları [KANIT] eşikleriyle karşılaştır (§11.1)
[12] Aşım varsa: OLAY kaydı — suçlu atfı YOK (driver/termal ayrımı ölçümle)
[13] FIR/EQ offload senaryosu (dogrulama §8.3 formu) — PLANNED
[14] Video kodlama ölçümü (G-T07 envanteri VARSA) — yoksa atla + not düş
[15] Ham çıktıları iliştir (S4); elle yazılmış sayı S3
[16] `gpu-dogrulama.md` §7.7 kapısını güncelle (satır silme)
[17] `gpu-kapsam.md` §1'deki ⚠️ işaretlerini yalnız ölçümle kaldır
[18] §9.1 eksiklik matrisinde doldurulan hücreleri işaretle
[19] Dürüstlük: ölçülmemen hücre MEASURE kalır (uydurma YASAK)
[20] 3 başarısız turda DUR + üst karar (Vault Steward)
```

---

## §12 İddia Kayıt Defteri ve Kapanış

### §12.1 İddia → durum matrisi (bu dosyanın iddiaları)

| # | İddia | Durum | Dayanak |
|---|---|---|---|
| G-OR01 | "Repo'da GPU API'si yok" | **ÖLÇÜLDÜ (S3)** | §1.1 (0 · 0) |
| G-OR02 | "Sistemde GPU var" | **UNKNOWN** | envanter yok (K-G5) |
| G-OR03 | "VRAM ölçümü yapıldı" | **REDDEDİLDİ** | §11: ölçüm = 0 |
| G-OR04 | "CUDA/OpenCL kullanılabilir" | **UNKNOWN** | repo=0 · envanter yok |
| G-OR05 | "Intel/ARM GPU kaynağı doğrulandı" | **REDDEDİLDİ** | 403 (§10.2) |
| G-OR06 | "Video donanımı var" | **UNKNOWN** | G-T07 `role` boş |
| G-OR07 | "Sürücü bağlama bu dalda" | **REDDEDİLDİ** | §3.1/4 → K000 |
| G-OR08 | "GPU ölçüm eşiği kesin" | **YASAK** | §11.1 [KANIT] |

### §12.2 Dürüstlük bloğu (QUANTIFIED)

- **Kod**: GPU API'si **0** (§1.1 · 2026-10-13) · fiziksel ölçüm **0**.
- **Ölçüm**: VRAM bant / GPU yükü / sıcaklık **0** — §11 formları boş.
- **URL**: **5 × HTTP 200** (§10.1) · **2 × kullanılmadı** (403 · 404) —
  erişim 2026-10-13; **bu dosyada başka URL atıfı YOK**.
- **Envanter**: 7 tip × envanter = **0 doldurulmuş** (§9.1) → `proposed`.
- **Kısıt**: ADR-038 · PCM5122 · ADR-089 **dokunulmaz** (ADR-102 §4.2);
  güç/PCB → K002 · ses offload tasarımı → K004.
- **Yer tutucu**: `parent:` yok · yer tutucu deseni yok · boş alanlar
  `UNKNOWN`/`MEASURE` işaretli.

### §12.3 Kullanım kuralı ve çapraz referanslar

| Kural | Ayrıntı |
|---|---|
| Bu dosya üretim kodu değildir | yalnız şablon/çalışma sayfası (§1.1: 0) |
| Uydurma ölçüm/URL/model yazmaz | boş hücre `—` · bilinmeyen `UNKNOWN` · iddia `⚠️ VERIFICATION REQUIRED` |
| Doldurma sahibi | Vault Steward ölçüm oturumu (K-G5 · K-G3/S1) |

| Konu | Dosya |
|---|---|
| Kapsam/tipler | `gpu-kapsam.md` (§13.1–§13.7 · §22) |
| Kapılar/formlar | `gpu-dogrulama.md` (§7 · §8.1–§8.5) |
| Dal indeksi | `gpu/index.md` |
| CPU sınırı | `cpu/cpu-kapsam.md` §9/§12 |
| Display sınırı | `display/` dalı |
| DSP/K004 sınırı | `donanım-anayasa.md` §10 |
| Kardeş şablon | `cables/cables-ornekler.md` · `network/network-ornekler.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
