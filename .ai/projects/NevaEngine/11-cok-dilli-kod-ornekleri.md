---
title: "Neva Engine — Çok Dilli Kod Örnekleri (10 Dil, Index)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer / UI Designer inceleme bekliyor"
updated: 2026-10-15
---

# Neva Engine — Çok Dilli Kod Örnekleri (Index)

> **Durum:** v1.0.0 · örnekler derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu
> **İstek:** "Bütün yazılım dilleri için kod örnekleri ekle" (2026-10-15, kullanıcı onayı: 10 dil · geniş
> örnek seti · hem yeni doc 11 hem doc 03 bağlantısı hem dil-başına dosya · Neva K1-K6 + CoreMusic karma).

## §1 Kapsam

`11-examples/` altında **10 dil dosyası**; her dosya aynı 6-örnek şemasını kendi idiomuyla uygular.
Kod blokları vault referansıdır — **derlenmemiş, çalıştırılmamış**; canlı kod üretiminde ayrıca
build + test (Faz 0) gerekir (doc 01/03 geliştirme planı).

- Kaynak bağlamı: doc 02 (DSP zincir) · doc 03 (OS API örnekleri — C++ odağı) · doc 05 (web mixer
  PCM planı) · doc 10 (K1-K6 I/O format kuralları — bağlayıcı).
- Zero-hallucination: yalnız bilinen standart API; doğrulanamayan satır `⚠️ VERIFICATION REQUIRED`.

## §2 Bağlayıcı K1-K6 özeti (doc 10'dan)

| Kural | İçerik |
|---|---|
| K1 | Input **32-bit float** |
| K2 | Internal processing **32-bit float** |
| K3 | Output **32-bit float** (24-bit int'e düşüşte **TPDF dither**) |
| K4 | SR **44.1–192 kHz** (varsayılan **48 kHz**) |
| K5 | Cihaz 192 sunmuyorsa → **en yüksek yerel SR** |
| K6 | Bit-perfect = **SR eşit + float32** |

## §3 Dil × Dosya matrisi

| # | Dil | Dosya | Neva/CoreMusic bağlamı | Örnek sayısı |
|---|---|---|---|---|
| 1 | C11 | [`11-examples/01-c11.md`](11-examples/01-c11.md) | Taşınabilir çekirdek / sürücü seviyesi | 6 |
| 2 | C++20 | [`11-examples/02-cpp20.md`](11-examples/02-cpp20.md) | **Ana motor** (JUCE, doc 00/03) | 6 |
| 3 | C# | [`11-examples/03-csharp.md`](11-examples/03-csharp.md) | R12 masaüstü kontrol | 6 |
| 4 | PHP 8.x | [`11-examples/04-php8.md`](11-examples/04-php8.md) | **R11 yerel PHP sunucu** (CoreMusic) | 6 |
| 5 | JavaScript | [`11-examples/05-javascript.md`](11-examples/05-javascript.md) | Web mixer (AudioWorklet, doc 05) | 6 |
| 6 | TypeScript | [`11-examples/06-typescript.md`](11-examples/06-typescript.md) | Web mixer tipli katman | 6 |
| 7 | Python | [`11-examples/07-python.md`](11-examples/07-python.md) | Offline analiz/araç zinciri | 6 |
| 8 | Go | [`11-examples/08-go.md`](11-examples/08-go.md) | Yerel servis / MCP köprü (§11 doc 04 dersi) | 6 |
| 9 | Rust | [`11-examples/09-rust.md`](11-examples/09-rust.md) | Sistem güvenliği alternatifi | 6 |
| 10 | HTML/CSS | [`11-examples/10-html-css.md`](11-examples/10-html-css.md) | Mixer UI (DSP yok — görselleştirme örnekleri) | 6 |

## §4 Ortak örnek şeması (1-9: DSP dilleri; 10: UI)

| Örnek | Ad | Kural/R | Ne yapıyor |
|---|---|---|---|
| 1 | Biquad EQ | R16 · K2 | RBJ peaking koef. + Direct Form I (+ çift form notu) |
| 2 | 32f buffer döngüsü | K2·K4·K5 | 48k varsayılan, SR fallback, interleaved float32 |
| 3 | SPSC ring buffer | R10 | Lock-free kuyruk (atomik head/tail; PHP/Python dürüst sapmalar) |
| 4 | WAV 32f I/O | K3 · K1 | RIFF + format 3 (IEEE_FLOAT), 44.1-192k |
| 5 | Compressor+limiter | R10 · K3 | envelope + 4:1 + 0dBFS clamp + TPDF dither notu |
| 6 | OS/cihaz enum | R18 | Otomatik varsayılan cihaz seçimi |

HTML/CSS dosyası aynı sıra numaralarını UI örneklerine uyarlar (strip · rozet/SR · meter ·
worklet yükleyici · gain-reduction bar · OS rozeti).

## §5 Paket yapısı

```
11-cok-dilli-kod-ornekleri.md   ← bu index
11-examples/
  01-c11.md   02-cpp20.md   03-csharp.md   04-php8.md   05-javascript.md
  06-typescript.md  07-python.md  08-go.md  09-rust.md  10-html-css.md
```

## §6 Bağlam eşlemesi (kullanıcı kararı: "3 ve 1" = karma + Neva özel)

- **Sistem dilleri (C/C++/Rust/Go/C#):** Neva Engine'e özel, K1-K6 etiketli.
- **PHP 8.x:** CoreMusic R11 yerel sunucu — offline WAV işleme; **gerçek zamanlı PHP yok** (dürüst sınır).
- **JS/TS/HTML/CSS:** Neva web mixer (doc 05) — AudioWorklet 128-frame, K5 otomatik (AudioContext
  sampleRate = cihazın max SR'si).
- **Python:** offline analiz — **real-time değil** (GIL notu dosyada).

## §7 Uyarlama & guardrails

1. Her dosya üstünde **"derlenmedi/test edilmedi"** notu — üretim kodu doc 01/03 planından geçer.
2. Dil standartları (global AGENTS.md): C++ RAII/noexcept · C# nullable+ConfigureAwait · PHP
   strict_types · JS `var`/`any` yok · SQL parametreli (bu doc'da SQL yok) · Rust ownership.
3. HTML/CSS: yalnız `--cm-*` token okunur; ham hex/px yalnızca "token dosyasına eklenecek" yorumu.
4. Gerçek UI işi → **mockup gate** (`.ai/ui-design/` görseli) geçerli kalır.
5. Clean-room (R1): örnekler standart desenlerdir; tescilli ürün kodu kopyalanmaz (doc 04 RE
   bulguları yalnızca mimari ders düzeyinde — §12).

## §8 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-15 | İlk sürüm: 10 dil dosyası + 6 örnek şeması + K1-K6 eşlemesi |

**Authority:** Master Orchestrator — Embedded Engineer (C/Rust) + UI Designer (HTML/CSS) inceleme
bekliyor · Örnekler derlenmedi · doc 03 §8 ile çapraz bağlı · commit'siz (orkestratöre ait)
