---
title: "GPU (Ekran Kartı / Hesaplama Birimi) — Dal İndeksi"
type: gpu-index
node_id: K001-donanım/donanaım-type/gpu
node_type: context
status: proposed
parent_id: K001-donanım/donanaım-type
children:
  - K001-donanım/donanaım-type/gpu/gpu-kapsam
  - K001-donanım/donanaım-type/gpu/gpu-dogrulama
  - K001-donanım/donanaım-type/gpu/gpu-ornekler
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - ".ai/architecture/K001-donanım/donanım-anayasa.md §10 (satır 195) · §12 (satır 250) · §12.1 (satır 258) — okundu 2026-10-13"
  - ".ai/architecture/K001-donanım/donanaım-type/donanaım-type-kapsam.md §2/A6 (satır 46) · §7/3-T1 (satır 135) — okundu 2026-10-13"
  - "https://www.vulkan.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.opengl.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://developer.nvidia.com/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.khronos.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "Repo ölçümü 2026-10-13: Vulkan|OpenCL|CUDA|OpenGL (*.{php,c,cpp,h,hpp,cs}) = 0 · WebGL|WebGPU (*.js) = 0"
validation: "draft — 2026-10-13 yazıldı · Vault Steward dal açma onayı 2026-10-13 (klasör açma ✓) · anayasa/metin revizyonu hâlâ APPROVAL REQUIRED"
open_questions:
  - "Q-GI1: ADR-102 (draft) 5 dali sayar; gpu bu listede YOK — dal sayımı 3→8 yerine 3→9 olur; ADR-102 revizyonu APPROVAL REQUIRED"
  - "Q-GI2: CoreMusic'te GPU kullanımı envanterde yok — hesaplama/render/video rolü PLANNED/UNKNOWN"
  - "Q-GI3: Ses işlemede GPU (FIR/EQ offload) senaryosu PLANNED — fiziksel ölçüm YOK"
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

# gpu — Ekran Kartı / Hesaplama Birimi Dal İndeksi

> **Amaç:** `donanaım-type/gpu/` dalının indeksi. Bu dal **hesaplama + render**
> katmanını (compute/render) kapsar; **panel + UI çıktı gecikmesi display dalının**
> konusudur (sınır: `gpu-kapsam.md` §0/§12). DSP konusu K004'tedir.

## §1 Dal Kimliği

| Alan | Değer |
|---|---|
| Dal | `donanaım-type/gpu/` |
| node_id | `K001-donanım/donanaım-type/gpu` |
| Üst | `K001-donanım/donanaım-type` |
| Durum | `proposed` (Vault Steward klasör açma onayı 2026-10-13 ✓; anayasa/metin revizyonu APPROVAL REQUIRED) |
| Bağımlılık | `K001-donanım` · `K000-isletim-sistemi` |
| Grup | `mimari` (indeks · kapsam) · `kanit` (doğrulama) · `kod` (örnekler) |

## §2 Dosyalar

| Dosya | Grup | İçerik |
|---|---|---|
| `index.md` | mimari | Bu indeks — dal kimliği, çelişki özeti, dosya listesi |
| `gpu-kapsam.md` | mimari | Kapsam içi/dışı · tip envanteri (26 alan) · sınırlar · çelişki kaydı |
| `gpu-dogrulama.md` | kanit | İddia→kanıt matrisi · kapılar · HEAD URL tablosu · repo ölçümü |
| `gpu-ornekler.md` | kod | Gerçek repo kanıtları + şablon/adım tabloları (uydurma kod YOK) |

## §3 SINIR ÇELİŞKİSİ ÖZETİ (tam kayıt: `gpu-kapsam.md` §0)

| # | Kaynak | Ne diyor | gpu durumu |
|---|---|---|---|
| 1 | `donanım-anayasa.md` §12/C10 (satır 250) | OPcache/JIT zorunlu (PHP yürütme) | İlgili — GPU≠PHP yürütme |
| 2 | `donanaım-type-kapsam.md` §2/A6 (satır 46) | Yeni türler NEEDS DISCOVERY | gpu = NEEDS DISCOVERY |
| 3 | `donanaım-type-kapsam.md` §7/3-T1 (satır 135) | Tür ayrım kuralı | gpu bir tür dalı |
| 4 | Yeni ihtiyaç prompt'u §5 (A-sınıfı) | GPU tipi kapsamda | Kapsam tanımı |
| 5 | Vault Steward 2026-10-13 | Klasör açma onayı ✓ | Açıldı |
| 6 | ADR-102 (draft) §4 | 5 dal sayar — **gpu YOK** | ⚠️ Çelişki: dal sayısı 9 olur (Q-GI1) |

## §4 Tip Envanteri Özeti (tam kayıt: `gpu-kapsam.md` §13)

| Tip | Konu | Durum |
|---|---|---|
| G-T01 | NVIDIA (CUDA ekosistemi) | PROPOSED · envanter UNKNOWN |
| G-T02 | AMD (RDNA/Radeon) | PROPOSED · envanter UNKNOWN |
| G-T03 | Intel entegre grafik | PROPOSED · envanter UNKNOWN |
| G-T04 | ARM Mali / Adreno (mobil/SoC) | PROPOSED · envanter UNKNOWN |
| G-T05 | Hesaplama API'leri (CUDA/OpenCL/Vulkan compute) | PROPOSED · repo = 0 |
| G-T06 | Render pipeline (Vulkan/OpenGL) | PROPOSED · repo = 0 |
| G-T07 | Video kod çözme/kodlama | PROPOSED · envanter UNKNOWN |

## §5 Sınır Hatları (özet — tam kayıt: `gpu-kapsam.md` §12)

| Sınır | Kural |
|---|---|
| gpu ↔ display | gpu = hesaplama+render; display = panel+UI çıktı gecikmesi |
| gpu ↔ cpu | CPU SIMD ≠ GPU compute; vektör uzantıları cpu dalında |
| gpu ↔ DSP | FIR/EQ GPU offload = PLANNED/UNKNOWN; DSP çipi K004 |
| gpu ↔ K000 | Sürücü/OS yükleme katmanı K000'de |

## §6 Açık Sorular (özet)

| # | Soru | Durum |
|---|---|---|
| Q-GI1 | ADR-102 gpu'yu saymıyor — revizyon onayı | APPROVAL REQUIRED |
| Q-GI2 | CoreMusic'te GPU envanteri | UNKNOWN |
| Q-GI3 | GPU FIR/EQ offload senaryosu | PLANNED · ölçüm YOK |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
