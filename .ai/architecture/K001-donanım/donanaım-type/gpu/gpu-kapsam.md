---
title: "GPU (Ekran Kartı / Hesaplama Birimi) — Kapsam"
type: gpu-kapsam
node_id: K001-donanım/donanaım-type/gpu/gpu-kapsam
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
  - "Repo ölçümü 2026-10-13: Vulkan|OpenCL|CUDA|OpenGL = 0 · WebGL|WebGPU = 0 · native(c/cpp/h/cs)=0"
  - ".ai/architecture/K001-donanım/donanım-anayasa.md §10 satır 195 · §12 satır 250 · §12.1 satır 258"
  - ".ai/architecture/K001-donanım/donanaım-type/donanaım-type-kapsam.md §2/A6 satır 46 · §7/3-T1 satır 135"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · fiziksel ölçüm YAPILMADI (tüm performans iddiaları ⚠️)"
open_questions:
  - "Q-GK1: ADR-102 (draft) 5 dal sayar, gpu YOK — dal sayısı 9 olur; ADR-102/metin revizyonu APPROVAL REQUIRED"
  - "Q-GK2: CoreMusic GPU envanteri (var mı, hangi rolde) — UNKNOWN"
  - "Q-GK3: GPU FIR/EQ offload senaryosu — PLANNED, ölçüm YOK"
  - "Q-GK4: Video kod çözme donanımı kullanımı — UNKNOWN (repo'da GPU API kodu = 0)"
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

# gpu-kapsam — GPU Hesaplama/Render Kapsamı

> **Amaç:** `donanaım-type/gpu/` dalının kapsam dosyası. GPU = **hesaplama +
> render** katmanı. Panel/UI çıktı gecikmesi **display** dalının, DSP çipi **K004**'ün,
> OS yükleme/sürücü katmanı **K000**'indir. Fiziksel ölçüm bu dosyada YOKTUR.

## §0 SINIR ÇELİŞKİSİ KAYDI

> Bu bölüm dalın **girişindedir** ve her okuyucunun ilk gördüğü kayıttır. Dal,
> mevcut anayasa/kapsam metinleriyle **kısmen çelişkili** bir genişlemedir; çelişki
> gizlenmez, açıkça kaydedilir.

### §0.1 Çelişki Kayıt Tablosu

| # | Kaynak (dosya + bölüm/satır) | Ne diyor | gpu dalına etkisi | Durum |
|---|---|---|---|---|
| 1 | `donanım-anayasa.md` §10 Açık Sorular/3 (**satır 195**) | Ses işleme/DSP sınırı açık soru | GPU ≠ DSP; FIR/EQ offload = PLANNED | PROPOSED |
| 2 | `donanım-anayasa.md` §12/C10 (**satır 250**) | OPcache/JIT zorunlu (PHP yürütme) | GPU PHP yürütmesine dokunmaz — bağlam sınırı | PROPOSED |
| 3 | `donanım-anayasa.md` §12.1 (**satır 258**) | Performans/hedef bağlamı | GPU performans iddiası = ölçüm yok → ⚠️ | PROPOSED |
| 4 | `donanaım-type-kapsam.md` §2/A6 (**satır 46**) | Yeni türler "NEEDS DISCOVERY" | gpu tipi NEEDS DISCOVERY işaretli | PROPOSED |
| 5 | `donanaım-type-kapsam.md` §7/3-T1 (**satır 135**) | Tür ayrım kuralı (dal ≠ tür) | gpu bir **tür** dalıdır; hesaplama burada | PROPOSED |
| 6 | Yeni ihtiyaç prompt'u §5 (A-sınıfı) | GPU tipi kapsamda (NVIDIA/AMD/Intel/Mali/Adreno) | Kapsam tanımının kendisi | PROPOSED |
| 7 | Vault Steward 2026-10-13 | **Klasör açma onayı ✓** | 4 dosyalık dal açıldı | ✅ ONAYLI |
| 8 | ADR-102 (draft, `.ai/.decisions/draft/`) §4 | 5 dal sayar: display·cables·network·memory·cpu — **gpu YOK** | ⚠️ **Bilinen boşluk:** gpu eklenirse dal sayısı 9 olur (ADR metni "3 → 8" der) | ⚠️ ÇELİŞKİ — ADR-102 revizyonu APPROVAL REQUIRED |
| 9 | Anayasa/metin revizyonu | — | Bu dosya **metin revizyonu YAPMAZ**; yalnız kaydeder | APPROVAL REQUIRED |

### §0.2 PROPOSED — APPROVAL REQUIRED Etiketi

| Öğe | Durum |
|---|---|
| Klasör açma (dal üretimi) | **ONAYLI** (Vault Steward, 2026-10-13 ✓) |
| Dalın anayasaya eklenmesi (donanım-anayasa.md revizyonu) | **PROPOSED — APPROVAL REQUIRED** (bu dosya revize ETMEZ) |
| `donanaım-type-kapsam.md` §7 sayımının 9'a güncellenmesi | **PROPOSED — APPROVAL REQUIRED** |
| ADR-102 draft'ının gpu'yu içerecek şekilde revizyonu | **PROPOSED — APPROVAL REQUIRED** (ADR salt-okunur; orkestratör/Vault Steward kararı) |
| Tipler (G-T01…G-T07) | **PROPOSED** — envanter girişi Vault Steward yetkisinde |

**Çelişki işleme kuralı:** Bu dosyadaki hiçbir içerik anayasa/ADR/metin dosyalarını
**değiştirmez**; yalnızca dalın açılmasını gerektiren çelişkiyi kaydeder ve revizyon
onayını bekler. Revizyon onayı gelmeden bu dal "öneri" statüsündedir.

## §1 ELI15

GPU, bilgisayarın **paralel hesaplama ve resim çizme** birimidir. CoreMusic'te
olası rolleri üçtür: (1) **hesaplama** — çok sayıda küçük işlem (FIR/EQ gibi ses
işlemleri dâhil, henüz PLANNED), (2) **render** — arayüzün ekrana çizimi,
(3) **video kod çözme/kodlama**. Bu dal bu üç rolün **tanım ve envanter** katmanını
yazar; hangi GPU'nun sistemde olduğu **bilinmiyor** (UNKNOWN), repo'da GPU API'si
kullanımı **0** (ölçüm). Panelin kendisi (ekran gecikmesi) display dalındadır,
GPU değil.

> ⚠️ VERIFICATION REQUIRED: Bu dosyada **hiçbir GPU performans değeri, TFLOPS,
> bellek boyutu, saat hızı veya model numarası YOKTUR** — fiziksel ölçüm = 0.
> Tüm performans iddiaları bu işareti taşır. Marka adları (NVIDIA/AMD/Intel/Mali/
> Adreno) yalnızca **tip/kategori** etiketidir; "sistemde var" iddiası DEĞİLDİR.

## §2 Kapsam İçi

| # | Konu | cpu/ display/ ile sınır |
|---|---|---|
| 1 | GPU üretici aileleri: NVIDIA · AMD · Intel entegre · ARM Mali/Adreno | cpu ≠ GPU (SIMD ≠ compute) |
| 2 | Hesaplama API'leri: CUDA · OpenCL · Vulkan compute | — (gpu'nun konusu) |
| 3 | Render pipeline: Vulkan · OpenGL | display ≠ render (panel display'de) |
| 4 | Render pipeline bileşenleri (kavramsal): komut kuyruğu · shader · framebuffer | K004 ≠ GPU (DSP ayrı) |
| 5 | Video kod çözme/kodlama donanım hızlandırma | — |
| 6 | GPU bellek hiyerarşisi (VRAM + önbellek katmanları, kavramsal) | memory ≠ VRAM (RAM modülü memory dalında) |
| 7 | GPU sanallaştırma (passthrough/SR-IOV, kavramsal) | cpu sanallaştırma = VT-x (cpu dalı) |
| 8 | Ses işleme GPU offload'u (FIR/EQ) — **yalnız sınır/PLANNED** | DSP çipi K004 |
| 9 | PHP 8.4 repo bağlamında GPU kullanımı (**ölçüm: 0**) | PHP yürütme bağlamı cpu dalında |

## §3 Kapsam Dışı

| # | Konu | Nereye ait |
|---|---|---|
| 1 | Panel/ekran gecikmesi, çözünürlük, yenileme | `display/` dalı |
| 2 | DSP çipi (XU316) ve FIR/EQ sayısal doğruluğu | K004 Ses İşleme |
| 3 | CPU vektör uzantıları (AVX/NEON) | `cpu/` dalı |
| 4 | OS sürücü yükleme, kernel GPU yönetimi | K000-isletim-sistemi |
| 5 | RAM modülü/fiş fiziksel katmanı | `memory/` dalı |
| 6 | GPU besleme/termal (TDP, güç konektörü) | `power/` dalı |
| 7 | Fiziksel PCIe yolları/uyum | `cipler/` dalı |
| 8 | WebGPU/WebGL istemci kullanımı kodu | UI katmanı — repo ölçümü bu dosyada yalnız sayı olarak |
| 9 | Benchmark/oyun performans iddiaları | Ölçüm yok → bu dosyada YOK |

## §4 Tip Envanteri Özeti

| Tip | Aile | Alt konu |
|---|---|---|
| G-T01 | NVIDIA | CUDA ekosistemi + tüketici/veri merkezi ayrımı |
| G-T02 | AMD | RDNA (tüketici) — rdna sayfası HEAD 200 (§21) |
| G-T03 | Intel | Entegre grafik (iGPU) |
| G-T04 | ARM | Mali / Adreno (mobil/SoC) |
| G-T05 | API | Hesaplama: CUDA · OpenCL · Vulkan compute |
| G-T06 | API | Render: Vulkan · OpenGL |
| G-T07 | İşlev | Video kod çözme/kodlama |

## §5 Hesaplama API Karşılaştırması (kavramsal — ölçüm yok)

| API | Sahip/kuruluş | Kapsam | Repo kullanımı (2026-10-13) |
|---|---|---|---|
| CUDA | NVIDIA (vendor kilidi) | Hesaplama — yalnız NVIDIA donanımı | **0** (grep §11) |
| OpenCL | Khronos (çok üreticili) | Hesaplama — üretici bağımsız | **0** |
| Vulkan compute | Khronos | Hesaplama + render — modern çok üreticili | **0** |
| OpenGL | Khronos (eski) | Render — geniş uyumluluk | **0** |
| WebGPU/WebGL | W3C/浏览器 | İstemci tarafı render/hesap | **0** (JS, §11) |

**Kural:** Bu tablo **sürüm/özelliği** değil, yalnız **kategori ve sahiplik** taşır;
sürüm numarası/destek listesi uydurulmaz (ölcüm yok, sürüm kanıtı yok).

## §6 Mimari Nesiller (kavramsal — sayısal eşik YOK)

| Nesil | Kavram | gpu kaydı |
|---|---|---|
| Sabit işlev | Erken GPU — işlev önceden belirlenmiş | Kavramsal referans |
| Programlanabilir shader | Vertex/fragment/pixel birimleri | Kavramsal referans |
| Genel amaçlı hesaplama (GPGPU) | Hesaplama birimleri render dışı iş için | G-T05 kapsamı |
| Modern birleşik | Compute + render aynı API'de (Vulkan) | G-T05/G-T06 |

**Sayısal eşik yasağı:** "X nm", "Y TFLOPS", "Z CU" gibi **sayısal üretici
değerleri bu dosyaya YAZILMAZ** — kaynak/ölçüm yok; değer gerekirse §21'de HEAD
doğrulanmış üretici sayfasından Vault Steward aktarır.

## §7 GPU Bellek Hiyerarşisi (kavramsal)

| Katman | Kavram | Ölçüm |
|---|---|---|
| Kayıt/dosya (register) | En hızlı, çekirdek içi | ⚠️ YOK |
| Önbellek (L1/L2 benzeri) | Çekirdek/SM yakın | ⚠️ YOK |
| VRAM (global bellek) | Yüksek bant genişliği | ⚠️ YOK |
| CPU belleği (paylaşımlı) | Kopyalama maliyeti | ⚠️ YOK |

**Sınır:** VRAM **modülü/fiziksel katmanı** memory/ dalına, VRAM **denetleyici ve
bant genişliği kavramı** bu dalda (§8). Çifte kayıt yasağı: değer bir kez yazılır.

## §8 Bellek Denetleyici / Bant Genişliği (kavramsal)

| Kavram | gpu kaydı |
|---|---|
| Bant genişliği (GB/s) | ⚠️ VERIFICATION REQUIRED — ölçüm/model yok |
| Bellek veri yolu genişliği (bit) | UNKNOWN |
| ECC desteği | UNKNOWN |

## §9 Hesaplama Yetenekleri (kavramsal)

| Yetenek | Kavram | Ölçüm |
|---|---|---|
| Paralel iş parçacığı | Binlerce hafif iş parçacığı | ⚠️ YOK |
| Vektör/matrıs hızlandırma | Donanım matris birimleri | ⚠️ YOK |
| Ses işleme uygunluğu | FIR/EQ = filtre zinciri, paralelleşmeye uygun (kuramsal) | PLANNED (Q-GK3) |

**Kuramsal sınır iddiası yasağı:** "GPU FIR'da X kat hızlıdır" gibi **karşılaştırmalı
hız iddiası YAZILMAZ** — ölçüm yok; yalnız "uygun olduğu kuramsal olarak düşünülür,
ölçülmedi" denir.

## §10 GPU Sanallaştırma (kavramsal)

| Kavram | gpu kaydı |
|---|---|
| GPU passthrough (VM'ye fiziksel GPU) | UNKNOWN — envanter yok |
| SR-IOV (birden çok VM'ye bölme) | UNKNOWN |
| vGPU zaman paylaşımı | UNKNOWN |

**Sınır:** CPU sanallaştırma uzantıları (VT-x/AMD-V/VHE) **cpu dalında** (cpu-kapsam.md §10);
burada yalnız **GPU'nun kendisinin** paylaşımı.

## §11 PHP 8.4 Repo Bağlamı (disk kanıtlı — 2026-10-13 ölçümü)

| Ölçüm | Değer | Yorum |
|---|---|---|
| `Vulkan\|OpenCL\|CUDA\|OpenGL` | **0** (`*.{php,c,cpp,h,hpp,cs}`) | Uygulamada GPU API kullanımı yok |
| `WebGL\|WebGPU` | **0** (`*.js`) | İstemcide GPU API kullanımı yok |
| `*.php` sayısı | 319 | Saf PHP yığını (cpu-dogrulama.md §4.1 ile aynı ölçüm) |
| `*.js` sayısı | 98 | — |
| native (c/cpp/h/cs) | 0 | GPU kodu derlenmedi |

**Sonuç (S2):** GPU bu depoda **kullanılmıyor** — bu, "donanımda yoktur" iddiası
DEĞİLDİR (repo ≠ donanım envanteri, Q-GK2 UNKNOWN).

## §12 Sınır Tabloları

### §12.1 gpu ↔ display

| gpu | display |
|---|---|
| Hesaplama + render (çizim işi) | Panel + UI çıktı gecikmesi |
| API: CUDA/OpenCL/Vulkan/OpenGL | Panel arayüzü, çözünürlük, yenileme |
| "Çizim hesaplandı mı?" | "Panel çizimi ne zaman gösterdi?" |

### §12.2 gpu ↔ cpu

| gpu | cpu |
|---|---|
| Binlerce hafif iş parçacığı | Az sayıda güçlü çekirdek |
| CUDA/OpenCL/Vulkan compute | AVX/NEON SIMD (cpu-kapsam.md §9) |
| Render pipeline | PHP yürütme (OPcache/JIT) |

### §12.3 gpu ↔ DSP (K004)

| gpu | K004 DSP |
|---|---|
| FIR/EQ offload = PLANNED/UNKNOWN (Q-GK3) | DSP çipi = ses işleme uzmanı (XU316) |
| Genel amaçlı paralel hesap | Özel ses zamanlama (I2S/TDM) |
| Ölçüm YOK → ⚠️ | Ölçüm ADR-038 sürecinde |

### §12.4 gpu ↔ K000

| gpu | K000 |
|---|---|
| GPU hesap/render işi | OS sürücü yükleme/dağıtım katmanı |
| API çağrısı | Kernel GPU yönetimi |

## §13 Tip Kayıtları (26 alan × 7 tip)

> Her kayıt 26 alan taşır (type_id … proposed_path). Bilinmeyen alan `UNKNOWN` /
> `NOT PROVIDED` / `⚠️ VERIFICATION REQUIRED` yazılır, ATLANMAZ.

### §13.1 G-T01 — NVIDIA (tüketici/veri merkezi ailesi)

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | G-T01 |
| 2 | name_tr | NVIDIA ekran kartı ailesi |
| 3 | name_en | NVIDIA GPU family |
| 4 | category | gpu |
| 5 | subcategory | üretici ailesi |
| 6 | description | NVIDIA GPU'ları ve CUDA hesaplama ekosistemi |
| 7 | physical_component_or_device | PCIe ekran kartı / SoC içi (UNKNOWN) |
| 8 | primary_function | Hesaplama + render + video kod çözme |
| 9 | interfaces | PCIe (varsayım — envanter yok ⚠️) |
| 10 | protocols | CUDA · Vulkan · OpenGL (API kategorileri) |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET — sürücü K000 |
| 14 | driver_requirements | EVET — üretici sürücüsü (K000 katmanı) |
| 15 | firmware_dependency | GPU vBIOS — UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET — API katmanı |
| 17 | audio_relevance | DOLAYLI — FIR/EQ offload PLANNED (Q-GK3) |
| 18 | coremusic_relevance | Repo ölçümü: CUDA=0 → **kullanılmıyor** |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (gpu) · sürücü K000 |
| 21 | existing_document | `gpu/gpu-kapsam.md` §13.1 |
| 22 | evidence | Repo grep 2026-10-13 (CUDA=0) · developer.nvidia.com HEAD 200 |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-GK2 (envanter) · Q-GK3 (ses offload) |
| 26 | proposed_path | `gpu/gpu-kapsam.md` §13.1 |

### §13.2 G-T02 — AMD (RDNA/Radeon ailesi)

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | G-T02 |
| 2 | name_tr | AMD ekran kartı ailesi |
| 3 | name_en | AMD GPU family |
| 4 | category | gpu |
| 5 | subcategory | üretici ailesi |
| 6 | description | AMD GPU'ları (RDNA) ve çok üreticili API desteği |
| 7 | physical_component_or_device | PCIe ekran kartı / SoC içi (UNKNOWN) |
| 8 | primary_function | Hesaplama + render + video kod çözme |
| 9 | interfaces | PCIe (varsayım — envanter yok ⚠️) |
| 10 | protocols | OpenCL · Vulkan · OpenGL |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET — sürücü K000 |
| 14 | driver_requirements | EVET — üretici sürücüsü |
| 15 | firmware_dependency | GPU firmware — UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | DOLAYLI — PLANNED |
| 18 | coremusic_relevance | Repo ölçümü: OpenCL=0 → **kullanılmıyor** |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (gpu) · sürücü K000 |
| 21 | existing_document | `gpu/gpu-kapsam.md` §13.2 |
| 22 | evidence | amd.com/en/products/technologies/rdna HEAD 200 (§21) |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-GK2 |
| 26 | proposed_path | `gpu/gpu-kapsam.md` §13.2 |

### §13.3 G-T03 — Intel entegre grafik

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | G-T03 |
| 2 | name_tr | Intel entegre grafik birimi |
| 3 | name_en | Intel integrated graphics |
| 4 | category | gpu |
| 5 | subcategory | entegre GPU (CPU içinde) |
| 6 | description | Intel işlemci içindeki grafik birimi — ayrı kart DEĞİL |
| 7 | physical_component_or_device | CPU/SoC içi birim |
| 8 | primary_function | Render + video kod çözme (sınırlı hesaplama) |
| 9 | interfaces | Dahili (ayrı arayüz değil) |
| 10 | protocols | Vulkan · OpenGL (OpenCL — UNKNOWN) |
| 11 | power_requirements | UNKNOWN (CPU payı) |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET — sürücü K000 |
| 14 | driver_requirements | EVET |
| 15 | firmware_dependency | UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | DOLAYLI — PLANNED |
| 18 | coremusic_relevance | Repo ölçümü: OpenGL=0 → **kullanılmıyor** |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (gpu) |
| 21 | existing_document | `gpu/gpu-kapsam.md` §13.3 |
| 22 | evidence | Repo grep 2026-10-13 (OpenGL=0); **intel.com 403 → KULLANILMADI** (gpu-dogrulama §3.2) |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-GK2 |
| 26 | proposed_path | `gpu/gpu-kapsam.md` §13.3 |

### §13.4 G-T04 — ARM Mali / Adreno (mobil/SoC)

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | G-T04 |
| 2 | name_tr | ARM GPU aileleri (Mali / Adreno) |
| 3 | name_en | ARM GPU families (Mali / Adreno) |
| 4 | category | gpu |
| 5 | subcategory | SoC içi mobil GPU |
| 6 | description | Mobil/SoC entegre GPU'ları — düşük güç, paralel hesap |
| 7 | physical_component_or_device | SoC içi birim |
| 8 | primary_function | Render + mobil hesaplama |
| 9 | interfaces | SoC içi (UNKNOWN) |
| 10 | protocols | OpenGL ES · Vulkan |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN (mobil/SoC) |
| 13 | os_dependencies | EVET — K000 (Android/Linux) |
| 14 | driver_requirements | EVET (OS bütünleşik olabilir — UNKNOWN) |
| 15 | firmware_dependency | UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | DOLAYLI — PLANNED |
| 18 | coremusic_relevance | Repo ölçümü: GPU API=0 → **kullanılmıyor**; ARM CPU tespiti cpu dalının konusu |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (gpu) |
| 21 | existing_document | `gpu/gpu-kapsam.md` §13.4 |
| 22 | evidence | Repo grep 2026-10-13 (0); arm.com HEAD 200 (kök; derin GPU sayfası doğrulanmadı) |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-GK2 |
| 26 | proposed_path | `gpu/gpu-kapsam.md` §13.4 |

### §13.5 G-T05 — Hesaplama API'leri (CUDA/OpenCL/Vulkan compute)

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | G-T05 |
| 2 | name_tr | GPU hesaplama API'leri |
| 3 | name_en | GPU compute APIs |
| 4 | category | gpu |
| 5 | subcategory | yazılım arayüzü (API) |
| 6 | description | GPU'da genel amaçlı paralel hesap arayüzleri |
| 7 | physical_component_or_device | API katmanı (donanım değil) |
| 8 | primary_function | Paralel hesap yürütme sözleşmesi |
| 9 | interfaces | Donanım API arayüzü |
| 10 | protocols | CUDA (NVIDIA) · OpenCL (Khronos) · Vulkan compute (Khronos) |
| 11 | power_requirements | İlgisiz (API) |
| 12 | operating_environment | K000 + sürücü |
| 13 | os_dependencies | EVET |
| 14 | driver_requirements | EVET |
| 15 | firmware_dependency | İlgisiz (API) |
| 16 | hardware_abstraction_requirement | EVET — zaten soyutlama katmanı |
| 17 | audio_relevance | FIR/EQ offload = PLANNED (Q-GK3) |
| 18 | coremusic_relevance | Repo ölçümü: CUDA+OpenCL+Vulkan = **0** → kullanılmıyor |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (gpu) |
| 21 | existing_document | `gpu/gpu-kapsam.md` §13.5 |
| 22 | evidence | Repo grep 2026-10-13 (0) · vulkan.org HEAD 200 · khronos.org HEAD 200 |
| 23 | status | PROPOSED · kullanım YOK (repo) |
| 24 | confidence | LOW |
| 25 | open_questions | Q-GK3 (ses offload) |
| 26 | proposed_path | `gpu/gpu-kapsam.md` §13.5 |

### §13.6 G-T06 — Render pipeline (Vulkan/OpenGL)

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | G-T06 |
| 2 | name_tr | Render pipeline (çizim hattı) |
| 3 | name_en | Rendering pipeline |
| 4 | category | gpu |
| 5 | subcategory | render API + pipeline kavramları |
| 6 | description | Sahneyi/pixeli çizim işine çeviren hattı — komut kuyruğu, shader, framebuffer (kavramsal) |
| 7 | physical_component_or_device | API + GPU render birimleri |
| 8 | primary_function | Görüntü üretimi (display paneline HAZIRLIK) |
| 9 | interfaces | Display sunucusuna/OS'e |
| 10 | protocols | Vulkan · OpenGL |
| 11 | power_requirements | İlgisiz |
| 12 | operating_environment | K000 + sürücü |
| 13 | os_dependencies | EVET |
| 14 | driver_requirements | EVET |
| 15 | firmware_dependency | UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | YOK (görsel) |
| 18 | coremusic_relevance | Repo ölçümü: WebGL/WebGPU=0 · OpenGL=0 → kullanılmıyor; UI render'ı tarayıcıda |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (gpu) · panel display/ |
| 21 | existing_document | `gpu/gpu-kapsam.md` §13.6 |
| 22 | evidence | Repo grep 2026-10-13 (0) · vulkan.org/opengl.org HEAD 200 |
| 23 | status | PROPOSED · kullanım YOK |
| 24 | confidence | LOW |
| 25 | open_questions | Q-GK2 |
| 26 | proposed_path | `gpu/gpu-kapsam.md` §13.6 |

### §13.7 G-T07 — Video kod çözme/kodlama

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | G-T07 |
| 2 | name_tr | Video donanım kod çözme/kodlama |
| 3 | name_en | Video decode/encode acceleration |
| 4 | category | gpu |
| 5 | subcategory | donanım hızlandırma işlevi |
| 6 | description | Video akışlarını donanım biriminde çözme/kodlama |
| 7 | physical_component_or_device | GPU içinde birim (UNKNOWN ayrı/entegre) |
| 8 | primary_function | Video kod çözme + kodlama hızlandırma |
| 9 | interfaces | API üzerinden (UNKNOWN) |
| 10 | protocols | UNKNOWN (vendor API'leri — sürüm kanıtı yok) |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET — K000 |
| 14 | driver_requirements | EVET |
| 15 | firmware_dependency | UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | DOLAYLI (ses/video senkronu) — ölçüm YOK |
| 18 | coremusic_relevance | Repo'da video işleme GPU kodu = 0 |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (gpu) |
| 21 | existing_document | `gpu/gpu-kapsam.md` §13.7 |
| 22 | evidence | Repo grep 2026-10-13 (0); envanter kanıtı YOK |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-GK4 |
| 26 | proposed_path | `gpu/gpu-kapsam.md` §13.7 |

### §13.8 Tip-Bağlam Sınır Matrisi

| Tip \ Bağlam | Kapsam | Doğrulama | Örnekler |
|---|---|---|---|
| G-T01 NVIDIA | §5, §13.1 | K-G1, K-G5 | Ş-1 |
| G-T02 AMD | §5, §13.2 | K-G1, K-G5 | Ş-1 |
| G-T03 Intel iGPU | §13.3 | K-G1 | Ş-1 |
| G-T04 Mali/Adreno | §13.4 | K-G1 | Ş-1 |
| G-T05 Hesap API'leri | §5, §9, §13.5 | K-G2, K-G5 | Ş-2, Ş-3 |
| G-T06 Render | §6, §13.6 | K-G2 | Ş-2 |
| G-T07 Video | §13.7 | K-G2 | Ş-3 |

## §14 Etiket Disiplini

| Etiket | Anlam | Bu dosyadaki kullanım |
|---|---|---|
| PROPOSED | Önerildi — onay bekliyor | Tüm tipler + dal |
| APPROVAL REQUIRED | Revizyon onayı gerekli | §0.2 (anayasa/ADR/metin) |
| UNKNOWN | Değer bilinmiyor | Envanter alanları |
| NOT PROVIDED | Kaynakta verilmemiş | Ölçüm yokluğu |
| ⚠️ VERIFICATION REQUIRED | Ölçüm/kanıt gerekli | Tüm performans iddiaları |
| PLANNED | Hedef — henüz yok | Ses offload (Q-GK3) |
| NEEDS DISCOVERY | Keşif gerekli | donanaım-type-kapsam.md:46 |

## §15 Kapılar (özet — tam kayıt: gpu-dogrulama.md)

| Kapı | Konu | Durum |
|---|---|---|
| K-G1 | Üretici URL doğrulama | ✅ (§21 HEAD tablosu) |
| K-G2 | API URL doğrulama | ✅ |
| K-G3 | Repo ölçüm (GPU API = 0) | ✅ ölçüldü |
| K-G4 | 26 alan × 7 tip bütünlüğü | ✅ (§13 — atlanan yok) |
| K-G5 | Envanter/ölçüm | ❌ AÇIK (Q-GK2) |
| K-G6 | ADR-102 çelişkisi | ⚠️ APPROVAL REQUIRED (Q-GI1) |

## §16 Riskler

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| 1 | ADR-102 sayımı 9'a çıkar (gpu YOK) | Sayım/revizyon uyuşmazlığı | Q-GI1 — Vault Steward kararı |
| 2 | Envanter yokken "GPU var" denmesi | Hallüsinasyon | UNKNOWN/⚠️ işaretleri |
| 3 | Display/gpu sınırı karışması | Çifte kayıt | §12.1 matrisi |
| 4 | FIR/EQ offload hız iddiası | Ölçülmüş gibi görünme | §9 hız iddiası yasağı |

## §17 Kontrol Listesi

| # | Kontrol | Durum |
|---|---|---|
| 1 | §0 çelişki kaydı girişte | ✅ |
| 2 | 26 alan × 7 tip tam | ✅ (§13) |
| 3 | Fiziksel ölçüm yok → ⚠️ işaretli | ✅ |
| 4 | URL'ler HEAD doğrulanmış | ✅ (§21) |
| 5 | `parent:` anahtarı yok | ✅ |
| 6 | Authority satırı son satırda | ✅ |
| 7 | Commit ATILMADI | ✅ |

## §18 Hata Kayıtları

| # | Hata | Durum |
|---|---|---|
| H-G1 | ADR-102 draft'ı gpu'yu içermiyor | Bilinçli boşluk — §0.1/8, APPROVAL REQUIRED |
| H-G2 | intel.com erişilemedi (403) | KULLANILMADI — §21.2, Intel kaynağı yok |
| H-G3 | Repo'da GPU API kodu yok | Durum kaydı — §11 (eksiklik değil, ölçüm) |

## §19 Sözlük

| Terim | Anlam |
|---|---|
| GPGPU | Render dışı genel amaçlı GPU hesaplama |
| Shader | GPU'da çalışan görüntü/iş programı |
| VRAM | GPU'nun kendi belleği |
| Bant genişliği | Bellek veri akış hızı (GB/s) — ölçüm yok |
| Passthrough | VM'ye fiziksel GPU atama |
| Kod çözme (decode) | Sıkıştırılmış videoyu açma |

## §20 Kontrol Listesi Özeti

§17 yeterlidir — tekrarlanmaz (çifte kayıt yasağı).

## §21 Kaynaklar (bu oturumda HEAD doğrulanmış)

### §21.1 Kullanılan kaynaklar (HTTP 200)

| # | URL | HTTP | Erişim | Kullanım |
|---|---|---|---|---|
| 1 | `https://www.vulkan.org/` | 200 | 2026-10-13 | Vulkan — G-T05/G-T06 |
| 2 | `https://www.opengl.org/` | 200 | 2026-10-13 | OpenGL — G-T06 |
| 3 | `https://developer.nvidia.com/` | 200 | 2026-10-13 | NVIDIA geliştirici — G-T01 |
| 4 | `https://www.khronos.org/` | 200 | 2026-10-13 | Khronos — API sahibi kuruluş |
| 5 | `https://www.amd.com/en/products/technologies/rdna` | 200 | 2026-10-13 | AMD RDNA — G-T02 |

### §21.2 Kullanılmayan kaynaklar (erişilemedi → KAYNAK OLMADI)

| # | URL | HTTP | Karar |
|---|---|---|---|
| 1 | `https://www.intel.com/` | **403** | **KULLANILMADI** — Intel G-T03 için kaynak yok; iddia yalnız repo ölçümüne dayanır |
| 2 | `https://www.amd.com/en/products/graphics` | **404** | **KULLANILMADI** — sayfa yok |
| 3 | `https://developer.arm.com/` | **403** | **KULLANILMADI** — Mali/Adreno için derin sayfa doğrulanmadı |

## §22 Çapraz Referanslar

| Konu | Dosya |
|---|---|
| Dal indeksi | `gpu/index.md` |
| Kapılar/ölçüm | `gpu/gpu-dogrulama.md` |
| Şablonlar/adımlar | `gpu/gpu-ornekler.md` |
| CPU sınırı | `cpu/cpu-kapsam.md` §9/§12 |
| Display sınırı | `display/` dalı |
| DSP sınırı | K004 · `donanım-anayasa.md` §10 (satır 195) |
| Tür üstü kapsam | `donanaım-type-kapsam.md` §2/A6 (satır 46) · §7/3-T1 (satır 135) |

## §23 Dürüstlük Bloğu

| Kural | Durum |
|---|---|
| Fiziksel ölçüm 0 → tüm performans iddiaları ⚠️ | ✅ (§1, §7–§9) |
| Uydurma model/TFLOPS/bellek boyutu yok | ✅ (§6 sayısal eşik yasağı) |
| URL'ler yalnız HEAD 200 olanlar | ✅ (§21) |
| ADR-102 boşluğu kaydedildi, değiştirilmedi | ✅ (§0.1/8) |
| `parent:` · yasak yer tutucu yok | ✅ |

---

## §24 OS × DİL ERİŞİM MATRİSİ (grafik erişimi)

> **Kapsam:** GPU/Görüntü donanımı tespiti (ad, sürücü, PCIe konumu) hangi OS
> arayüzüyle hangi dilden erişilir? Fiziksel ölçüm YOK; bu bölüm yalnız erişim yollarını
> tanımlar (§6 sayısal eşlik yasağı ve §23 Dürüstlük Bloğu geçerlidir).

| Dil | Windows | macOS | Linux |
|---|---|---|---|
| JavaScript/Node | `wmic path win32_VideoController` / PowerShell child ⚠️ ayrıntı doğrulanmadı | `system_profiler SPDisplaysDataType` child ⚠️ | `lspci` child / sysfs `/sys/class/drm` okuma |
| PHP | Windows'ta yerel grafik API'si yok → çocuk süreç (PowerShell) ⚠️ | çocuk süreç `system_profiler` ⚠️ | `/sys/class/drm` okuma (fopen) |
| Python | `ctypes` + DXGI/SetupAPI ⚠️ URL'siz | `subprocess` → `system_profiler` ⚠️ | `/sys/class/drm` + `lspci` okuma |
| C / C++ | DXGI `EnumAdapters` / SetupAPI P/Invoke ⚠️ (üye doğrulaması K4) | IOKit Registry (IOGPU/PCI) ⚠️ URL'siz | DRM/KMS (`drmModeGetResources`) · sysfs |
| C# / PowerShell | DXGI P/Invoke (SetLastError) · WMI `Win32_VideoController` | `system_profiler SPDisplaysDataType` child ⚠️ | `/sys/class/drm` okuma |
| Rust | `windows` crate DXGI | — | sysfs `/sys/class/drm` okuma |
| Go | `golang.org/x/sys/windows` + WMI ⚠️ | — | `/sys/class/drm` + `lspci` |
| Bash | yok (çocuk süreç: PowerShell) | `system_profiler SPDisplaysDataType` ⚠️ | `lspci -nn \| grep -i vga` · `ls /sys/class/drm` |
| Java / Kotlin | JNA + JNI (dış GPU kütüphanesi) ⚠️ JNA/FFM URL'si yok (K3) | JNA `IOKit` ⚠️ | `Runtime` + JNI/`lspci` ⚠️ |
| Swift | yok | IOKit `IOServiceMatching("IOPCIDevice")` ⚠️ | yok |

**Öncelikli öneri (PHP/JS):** Belirleme Linux'ta PHP ile doğrudan `/sys/class/drm`
okunarak yapılır; Windows/macOS'ta PHP grafik API'si **yoktur** — PowerShell/Çİ (Bash)
çocuk süreçle `system_profiler`/WMI'ye devredilir.

**Risk notları:**
- macOS IOKit Registry bit alanları (BOOLEAN/prop) ve eşleştirme anahtarları bu belgede doğrulanamadı → ⚠️ VERIFICATION REQUIRED.
- Windows SetupAPI P/Invoke üye/alan doğrulaması ( GUID/SP_DEVINFO_DATA) yok → ⚠️ VERIFICATION REQUIRED.
- Sürücü/sürüm ve performans (FPS/TFLOPS) iddiası üretilmez; ölçüm yapılmadan sayı yazılmaz.

Kapsamlı şablonlar: `donanım-platformlararasi-kod-ornekleri.md` §G1–§G11 · tam matris: `raporlar/K001-donanım-os-dil-matrisi.md`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
