---
title: "app-cpp-projects — Agent Operating Rules"
type: rules
category: agent-registry
version: 1.0.0
status: active
authority: "SSOT: .ai/AGENTS.md — Skill Registry @CLAUDE.md §Skill Registry"
updated: 2026-10-23
---
# app-cpp-projects — Agent Operating Rules

> **Amaç:** C++/sürekli-zaman ses motoru yüzeyinde çalışan agent'lar bu
> kurallara uyar. Bu yüzeyin tek istisnası: **kod konumu açık karar maddesidir**
> (`app-cpp-projects/` boş → kod `--/neva-engine/`'de, `.ai/PLAN.md:330` TODO açık).

## 1. Roller ve Sahiplik

| Rol | Ne yapar | Ne YAPMAZ |
|---|---|---|
| **Embedded Engineer** (ana, Expert #4) | Motor çekirdeği, DSP zinciri, WASAPI backend, firmware şartnameleri | PCB/PHP'ye dokunma |
| **performance-engineer** (Senior #9) | Latency/bellek ölçümü, profil | ölçümü uydurma |
| **cpp-pro** | modern C++20/23, RAII, template, bellek yönetimi | platform sözleşmesini kırma |
| **architect** (Expert #1) | Katman sınırı, ADR bağları | ADR'yi frozen iken değiştirme |

Tek ana sahip: **Embedded Engineer**.

## 2. Workflow (bu yüzeyde)

1. **Brief** — önce `.ai/PLAN.md:330` TODO durumunu ve mimari doc §19 onay
   durumunu OKU (açık ise üretim yalnızca taslak/şartname düzeyinde kalır).
2. **Approval** — mimari/donanım kararı **açık onay** ister (K-6 kapısı).
3. **Implement** — kod `--/neva-engine/` içinde; `app-cpp-projects/` yalnız
   doküman yüzeyidir (boş klasör silinmez, koda geri taşınmaz — onaysız).
4. **Track** — `.ai/log.md` append.
5. **Test** — `cmake --build` + `ctest` (§3).
6. **Review** — MISRA disiplini, const-correctness, lock-free RT.
7. **Close** — vault sync; **commit YOK** (orkestratöre ait).

## 3. Kurallar

- **Dil:** C++20 · RAII · smart pointer · `nullptr` · const-correctness ·
  MISRA C:2025 disiplini · `#pragma once`/include guard tutarlılığı.
- **RT:** audio callback **lock-free**; buffer'lar baştan allocate;
  callback içinde `new`/`lock`/`throw` yasak.
- **Platform:** yalnız WASAPI (tek backend); yeni backend = ADR + onay.
- **Ölçüm:** SNR/THD+N/latency iddiası yalnız ölçüm raporuyla yazılır.
- **Kod konumu:** kod `--/neva-engine/` altındadır; `app-cpp-projects/`e kod
  taşımak/üretmek yalnız açık onayla (`.ai/PLAN.md:330` kapanmadan YOK).
- **Yasak yüzeyler:** `git commit` · silme · `*.php/js/css/sql` · frozen ADR ·
  secret'ı `.md`'ye yazma.

## 4. Agent Listesi (bu yüzeyde çalışan)

| Agent | Tetikleyici |
|---|---|
| `embedded-engineer` | motor çekirdeği, OS backend soyutlama, buffer/DMA planı |
| `cpp-pro` | modern C++ inceleme, bellek/performans |
| `performance-engineer` | latency, bellek, callback profili |
| `architect` / `architect-reviewer` | katman sınırı, ADR |
| `dsp-firmware-engineer` | DSP algoritması firmware şartnamesi (donanım topolojisine dokunmaz) |
| `hardware-electronics` | Class AB/güç/termal doküman (yalnız belge) |

## 5. Skill Bağlantısı

- **`audio-engine-cpp`** — bu yüzeyin birincil skill'i (Neva Engine, JUCE 9,
  ASIO, DSP chain, realtime C++). Tetikleyici eşleşmesinde **ilk işlem** bu
  skill'i Skill tool ile yüklemektir (Skill Usage Mandate §2).
- İkincil: `hardware-electronics` (yalnız belge), `embedded-systems`.

## 6. Doğrulama (her işten önce)

```bash
cmake -S . -B build      # --/neva-engine altında configure = 0 hata
cmake --build build      # = 0 hata
ctest --test-dir build   # = 0 fail
```

Ek kapı: `.ai/PLAN.md:330` TODO **açıkken** "yüzey tamamlandı" sayılmaz.

## 7. Escalation

3 başarız düzeltme → dur · şüpheli varsayımı yaz · üst karar iste
(`@.ai/WORKFLOW.md` §6). Donanım/görsel üretim öncesi **açık onay** zorunlu.
