---
title: "memory — Bellek Donanımı Dalı (INDEX)"
type: bounded-context
node_id: K001-donanım/donanaım-type/memory
node_type: bounded-context
status: proposed
parent_id: K001-donanım/donanaım-type
children:
  - memory-kapsam.md
  - memory-dogrulama.md
  - memory-ornekler.md
depends_on:
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md"
evidence: proposed
validation: draft-validation
open_questions: 3
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

> **PROPOSED (ADR-102 taslak — APPROVAL REQUIRED)**: anayasa/üst kapsam
> revize **edilmedi**; bu dosya bir **taslak**tır ve yalnız ADR-102 §4 kapsamıyla
> sınırlıdır. Dal yetkisi: "memory = bellek donanımı (RAM DDR/SO-DIMM,
> EEPROM/flash, önbellek depolama donanımı); **bellek yönetim algoritması +
> sanal bellek → K000-isletim-sistemi**". Yazım notu: `donanaım-type` v2
> kaynağına sadık — düzeltme **yasak**.

## 1. Durum

| Öğe | Değer |
|---|---|
| Dal | `memory` (yeni — ADR-102 §4 ile açıldı) · üst: `K001-donanım/donanaım-type/` |
| Yetki | ADR-102 **taslak** → `status: proposed` |
| Dosya | `index` (bu) · `kapsam` (mimari) · `dogrulama` (kanıt) · `ornekler` (kod) |
| Envanter | **UNKNOWN** (Q-M1 — ölçüm: donanım kanıtı 0) |
| Ölçüm | 2026-10-13 · 1338 dosya / 25.752.595 bayt · yöntem: directory_tree + read_multiple_files |
| Kapılar | **M1 ✅ · M2 ❌ · M3 ❌ · M4 ✅ · M5 ⚠️ · M6 ⚠️** (M5 yeniden değerlendirme: JEDEC 403 — `memory-dogrulama.md` §6.4/§12.2) |

---

## 2. Gruplar

| Grup | Dosya | İş |
|---|---|---|
| **mimari** | [`index.md`](index.md) | Bu indeks — dal kimliği, durum/kapı özeti, çapraz referanslar (eksikti, 2026-10-14 tamamlandı) |
| **mimari** | [`memory-kapsam.md`](memory-kapsam.md) | Kapsam İÇİN/DIŞINDA · komşu sınırlar (K000/K002) · derinlik · DDR/ECC/M.2/self-refresh eksenleri (§10–§13) |
| **kanıt** | [`memory-dogrulama.md`](memory-dogrulama.md) | S1–S5 · repo ölçümü (RAM/DDR/EEPROM/cache) · **kapılar M1–M6** · URL defteri + JEDEC 403 çelişki kaydı (§6.4) |
| **kod** | [`memory-ornekler.md`](memory-ornekler.md) | `shared/src/Cache/` gerçek kanıtı (6 sınıf — **yazılım sınırı**) + envanter/ölçüm/ECC/M.2 **şablonları** (§8–§12) |

## 3. Kapsam sınırı (özet — tam metin `kapsam` §2/§3)

**İÇİNDE**: RAM (DDR4/DDR5 · DIMM/SO-DIMM · ECC donanımı) · EEPROM/NVRAM ·
gömülü flash (SPI NOR/NAND) · depolama tabanlı önbellek **donanımı** ·
modül/mikrodenetleyici bellek donanımı.

**DIŞINDA → K000-isletim-sistemi**: bellek yönetim algoritması · sanal
bellek/swap · sayfa tablosu/TLB · OS önbellek **yazılımı** (PHP
`CoreMusic\Cache\` dahil) · tahsisat · **os-donanim-tespiti yöntemi**.
**DIŞINDA → K002-electroncis**: bellek güç/PCB/sinyal (ADR-102 §4.2).

> **Kritik ölçüm uyarısı**: 857 `cache` eşleşmesi **yazılım cache'idir**
> (PHP 146/30 · `shared/src/Cache/` 6 sınıf) — donanım önbellek **DEĞİL**;
> `swap` 64'ün 57'si **`font-display: swap`** CSS. Ayrıntı: `memory-dogrulama.md` §5.

## 4. Çapraz referanslar

| Yön | Hedef | Ne |
|---|---|---|
| Yetki | ADR-102 §4 (+ §4.2 kısıtları) | Dal kapsamı — ADR-038/PCM5122/ADR-089 **dokunulmaz** |
| ↓ K000 | `K000-isletim-sistemi` | Bellek yönetimi · sanal bellek/swap · OS önbellek yazılımı · os-donanim-tespiti |
| → K002 | `K002-electroncis` | Bellek güç/PCB tasarımı · sinyal bütünlüğü |
| → kardeşler | `cpu/` (L2/L3) · `ssd/` · `hdd/` · `display/` · `power/` · `cables/` · `audio/` · `gpu/` | Sınır kayıtları `kapsam` §2.2–§2.3 |
| → network | `network/` | ADR-102 dalgası kardeşi — ortak ölçüm defteri `network-dogrulama.md` §5 |

## 5. Standart kaynakları (HEAD — 2026-10-13)

| Kaynak | Ne verdi | Erişim |
|---|---|---|
| `https://www.jedec.org/` | JEDEC — standart kuruluşu (kavramsal) | **⚠️ yeniden HEAD: HTTP 403** → sayısal JEDEC iddiaları **VERIFICATION REQUIRED** (`memory-dogrulama.md` §6.4.1; eski "✅" kaydı tarihsel olarak korunur) |
| `https://docs.kernel.org/` · `https://kernel.org/doc/html/latest/` | K000 sınırı (sürücü/çekirdek dokümanı) | 200 ✅ (S5 eklendi) |
| ADR-102 §4 | Dal yetkisi + kısıtlar | yerel ✅ |
| ⚠️ Elenen | `jedec.org/.../main-memory-ddr-sdram-hbm` → 404 · `standards.ieee.org/ieee/802.11/7033/` → yanlış sayfa · `https://uefi.org/` → 403 | **kullanılmadı** |

## 6. Açık sorular (3)

| # | Soru | Kapı |
|---|---|---|
| **Q-M1** | Proje envanterinde RAM/EEPROM/flash **modelleri** neler? (ölçüm: `ddr` 0 · `dimm` 0 · donanım `flash` 0) | M2 |
| **Q-M2** | Önbellek **donanımı** var mı (cache diski / L2-L3 envanteri)? (ölçüm: 857 `cache` = yazılım) | M3 |
| **Q-M3** | Ölçülmüş bellek gecikmesi/bant genişliği? (ölçüm yok) | M6 |

> **Dürüstlük**: bu dosyada **uydurma envanter/sürüm/çıktı yok**; her iddia
> `memory-dogrulama.md` S1–S5 zincirine bağlı. Revizyon kuralı: kapı
> **kapalıyken** dal `proposed` kalır; kapı açılırsa **satır silinmez, hücre güncellenir**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
