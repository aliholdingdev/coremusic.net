---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross
children:
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-anayasa"
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-surucu"
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-ornekler"
depends_on: []
evidence:
  - "shared/composer.json:8"
  - "package.json:10"
  - "https://gcc.gnu.org/onlinedocs/gccint/"
  - "https://ninja-build.org/"
validation: "2026-10-13 · 4 URL HEAD 200 · envanter kesişimi 0 · repo satırları Select-Object -Index (N-1) ile okundu"
open_questions:
  - "Q6 — hedef paket formatları (MSI/deb/AppImage) seti UNKNOWN"
group: mimari
---

# derleme-ve-paketleme — Kademe-3 Alt-Dal İndeksi

> **Kapsam:** Bu alt-dal, işletim sistemi sınırında **derleme araç zinciri**, **bağımlılık
> çözümleme**, **paket formatları** ve **tekrar üretilebilir (reproducible) derleme**
> konularını kapsar. Repo gerçeği: bugün derlenen tek yüzey PHP/Composer + npm'tir;
> C++ toolchain (Neva Engine) PLANNED'dir.

## §GRUPLAR (Kademe-3)

| Grup | Dosya | Ne taşır |
|---|---|---|
| mimari | `derleme-ve-paketleme-anayasa.md` | toolchain matrisi, paket formatları, bağımlılık modeli, reproducible kuralları |
| mimari | `derleme-ve-paketleme-surucu.md` | paket formatı sürücü matrisi (MSI/deb/rpm/AppImage), kurulum/denetim yüzeyi |
| kod | `derleme-ve-paketleme-ornekler.md` | gerçek `composer.json`/`package.json`/`ci.yml` kanıtları + PLANNED derleme kalıpları |

## Komşu sınır matrisi

| Komşu | Sınır | Taşınmayan |
|---|---|---|
| `platform-yuzeyleri` | OS API/ABI farkları | paket/dağıtım bu dalda |
| `tasinabilirlik-siniri` | `#ifdef` guard, ortak payda | derleme bayrakları bu dalda |
| `isletim-sistemi-kernel` | çekirdek/AB | toolchain bu dalda |
| `isletim-sistemi-testing` | test koşumu | paket doğrulama bu dalda |
| `K018-devops` | CI pipeline | `ci.yml` yalnız kanıt |
| `K004-dsp` | DSP algoritması | derleme hedefi sınır |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode