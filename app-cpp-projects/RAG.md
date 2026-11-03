---
title: "app-cpp-projects — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: app-cpp-projects/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# app-cpp-projects — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `app-cpp-projects` alanında çalışan AI agent'larının veya geliştiricilerin C++20 Neva ses motoruna, JUCE 9 / ASIO / WASAPI ses sürücülerine, Class AB amfi telemetrisine ve DSP filtrelerine nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/app-cpp-projects/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **C++ Alanı Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/app-cpp-projects/CONTEXT.md) | `CMakeLists.txt` |
| **Neva Ses Motoru Çekirdeği (Audio Core)** | `.ai/projects/NevaEngine/` | `include/` |
| **ASIO & WASAPI Düşük Gecikmeli Ses I/O** | `src/AudioIO/` | `ADR-038` |
| **Discrete Class AB Donanım Amfi Arayüzü** | `.ai/electronic/amplifier/` | `ADR-089` |
| **DSP Filtreleri (31-Bant EQ, Kompresör)** | `.ai/projects/NevaEngine/04-reverse-voice-fxsound.md` | `src/DSP/` |
| **C ABI & FFI Dışa Aktarımı (neva_c.h)** | `.ai/projects/NevaPlayer/` | `ADR-0105` |

---

## 🎯 Keyword & Arama Etiketleri

`cpp`, `neva-engine`, `juce9`, `asio`, `wasapi`, `dsp`, `class-ab`, `discrete-amplifier`, `snr-105db`, `ak4458`, `pcm3168a`, `c-abi`, `ffi`, `bit-perfect`

---

## ⚠️ Kritik Alan Kısıtları

1. **CLASS AB AMPLİFİKATÖR MİMARİSİ (`HL-02`, `ADR-038`):** Donanım ve DSP amfi topolojisi kesinlikle discrete Class AB'dir; Class D amfi veya PWM varsayımı yasaktır.
2. **GERÇEK ZAMANLI AUDIO (LOCK-FREE):** RT audio callback hattında bellek ayırma (malloc/new), kilit (mutex) ve sistem çağrısı yapılamaz; lock-free ring buffer zorunludur.
3. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi kullanıcıya / orkestratöre aittir.

---

*app-cpp-projects RAG Router v1.0.0 — SSOT: [`app-cpp-projects/CONTEXT.md`](file:///c:/www/coremusic.net/app-cpp-projects/CONTEXT.md)*
