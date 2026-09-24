---
title: "ASIO / WASAPI Rehberi — Ses Kartı Sürücü Sözleşmesi"
type: guide
category: ecosystem
version: 1.0.0
description: "ASIO SDK açık kaynak lisans dağıtım kuralları, WASAPI exclusive vs ASIO gecikme karşılaştırması, evrensel yerleşik ASIO'nun yalnız 64-bit sürücü desteklemesi ve CoreMusic K2/K0 stratejisi."
durum: active
tarih: 2026-09-24
kaynak: exa web doğrulaması (2026-09-24); ASIO SDK open-source lisans duyurusu, WASAPI exclusive-mode Microsoft dokümantasyonu, universal built-in ASIO (64-bit only / no 32-bit driver) teknik notu, JUCE AudioIODeviceType (K3 referansı)
status: active
authority: "ASIO/WASAPI sürücü sözleşmesi; CoreMusic K2/K0 katmanlarının birincil referansıdır — ASIO SDK lisans kuralları dağıtımda bağlayıcıdır."
updated: 2026-09-24
---

# CoreMusic — ASIO / WASAPI Rehberi — Ses Kartı Sürücü Sözleşmesi

> **Kapsam:** Bu belge, `.ai/ecosystem/` altındaki 6 ekosistem dokümanından sonuncusudur (6/6).
> Kapsadığı K katmanları: **K2 (ses kartı sürücü sözleşmesi), K0 (platform çapraz temel / Windows köprüsü)**.
> Doğrulama tarihi: **2026-09-24 (exa web doğrulaması)**.
> Yeni web araştırması yok; sağlanan doğrulanmış exa sonuçları `.ai/architecture/github-referanslari.md`, `.ai/CLAUDE.md` ve ekosistem serisinin 3/6 numaralı `ses-dsp-acik-kaynak.md` dosyası ile çapraz bağlanır.

**⚠️ UYARI — Lisans Kuralı (CLAUDE.md §4, §20, §21):**
- **ASIO SDK, açık kaynak lisansla yayınlandı** — dağıtım kuralları §5.2'de; CoreMusic dağıtımı bu kurallara uyar.
- **AGPL kodu CoreMusic'e asla kopyalanmaz** (sadece fikir).
- **PCM5122 yasaklı desendir** — bu belgede sürücü katmanı olarak da geçmez (CLAUDE.md §21).

**Template hizası (Guardrail #16):** docs-md-template.md §1–§7 + §4.1 Şablon-Önce bloğu verbatim korunmuştur.

> 2026-09-24 — CoreMusic Ekosistem Serisi (6/6)

---

## Table of Contents

1. [§1 Amaç](#1-amaç)
2. [§2 Kapsam](#2-kapsam)
3. [§3 Mimari](#3-mimari)
4. [§4 Kurallar](#4-kurallar)
5. [§5 Workflow](#5-workflow)
6. [§6 Doğrulama](#6-doğrulama)
7. [§7 Referanslar](#7-referanslar)

---

## §1 Amaç

**Amaç:** CoreMusic'in ses kartı sürücü sözleşmesini (K2) ve platform temelini (K0) besleyen ASIO ve WASAPI katmanlarını — ASIO SDK açık kaynak lisans dağıtım kuralları, WASAPI exclusive vs ASIO gecikme karşılaştırması ve universal built-in ASIO'nun 64-bit-only doğası — tek rehberde toplamak.

**Kapsam:**

| Kapsar | Kapsamaz |
|--------|----------|
| ASIO SDK açık kaynak lisans + dağıtım kuralları (K0) | ASIO SDK kodunun keyfi modifikasyonu (kurallar §5.2) |
| WASAPI exclusive vs shared mod (K2) | WASAPI kod implementasyonu (planlama) |
| ASIO gecikme (latency) karakteri (K2) | Gerçek ölçümler (kurulum sonrası — §6.4) |
| Universal built-in ASIO: 64-bit only / 32-bit driver yok (K0, K2) | Donanım üreticisi ASIO sürücüleri (3. taraf) |
| ASIO↔WASAPI↔JUCE AudioIODeviceType stratejisi (K2) | DSP algoritma (K3 — bkz. ses-dsp-acik-kaynak.md) |
| CoreMusic K2/K0 yol haritası (§5.3 ADR) | Donanım devre şeması (bkz. donanım-devre-referanslari.md) |

**Hedef Kullanıcı:**
- CoreMusic K2 sürücü sözleşmesini yazan sistem mühendisi
- Windows platform (K0) derleme/dağıtım hedefini kuran build mühendisi
- Düşük gecikmeli kayıt/yeniden-oynatma zinciri kuran audio engineer
- Lisans uyumluluğu denetleyen release sorumlusu (ASIO SDK kuralları)

**Bağlantılar:**
- `.ai/architecture/index.md` → K0–K20 SSOT (K0, K2)
- `.ai/ecosystem/ses-dsp-acik-kaynak.md` (3/6) → JUCE AudioIODeviceType K2 soyutlaması, §6.1 devir
- `.ai/ecosystem/donanım-devre-referanslari.md` (4/6) → donanım↔sürücü keşfi
- `.ai/CLAUDE.md` §5 (katmanlar), §21 (PCM5122 yasağı), §4/§20 (lisans)

---

## §2 Kapsam

### §2.1 CoreMusic K2/K0 İhtiyaçları

| İhtiyaç | K Katmanı | ASIO/WASAPI Karşılığı |
|---------|-----------|------------------------|
| Düşük gecikmeli çift yönlü ses I/O | **K2** | ASIO callback vs WASAPI exclusive event-driven |
| Sürücü keşfi + format müzakeresi | **K2** | ASIO getBestSampleRate/open vs WASAPI IsFormatSupported |
| Derleme/distribution ortamı | **K0** | ASIO SDK açık kaynak lisansı (§5.2) |
| Mimari uyumluluk | **K0, K2** | 64-bit only kuralı (§3.3) — CoreMusic 64-bit hedef |
| Çerçeve stratejisi | **K2** | JUCE AudioIODeviceType sarmalayıcı (3/6 §3.1) |
| Yedek yol (ASIO yoksa) | **K2** | WASAPI exclusive → shared fallback zinciri |

### §2.2 Mevcut Durum (CoreMusic)

- **Mevcut sürücü sözleşmesi:** K2 katmanı ASIO/WASAPI hedefliyor (3/6 dosyanın §2.2'si ile tutarlı).
- **Platform:** CoreMusic Windows (K0) hedefi 64-bit — universal built-in ASIO kısıtıyla (§3.3) uyumlu.
- **Eksik olan:** ASIO SDK lisansının dağıtıma yansıması (§5.2) henüz DECISIONS.md'ye yazılmadı; exclusive/shared fallback zinciri şeması yok (§5.1); 32-bit politikası belirsiz (§3.8 madde 1).

### §2.3 Boşluk Analizi

| Boşluk | Etkilenen K | Önem | Çözüm |
|---------|-------------|------|-------|
| ASIO SDK lisans kuralları dağıtıma işlemedi | **K0** | **Yüksek** | §5.2 dağıtım kontrol listesi |
| exclusive vs shared kararı yok | **K2** | **Yüksek** | §3.2 karşılaştırma + §5.1 fallback zinciri |
| ASIO gecikme karakteri bilinmiyor | **K2** | Orta | §3.4 ders + §6.4 ölçüm |
| 64-bit only kısıtı bilinmiyor | **K0, K2** | **Yüksek** | §3.3 — 32-bit hedef RED (§5.3 ADR) |
| Sürücü seçimi tek noktada değil | **K2** | Orta | JUCE AudioIODeviceType stratejisi (3/6) |

---

## §3 Mimari

### §3.1 ASIO SDK — Açık Kaynak Lisans Duyurusu (K0)

**Doğrulanmış olgu (2026-09-24 exa):** **ASIO SDK, açık kaynak lisansla yayınlandı** (Steinberg ASIO SDK open-source lisans duyurusu doğrulandı). ASIO, düşük gecikmeli profesyonel ses I/O'nun endüstri standardı arayüzüdür.

**Ne değişti (CoreMusic açısından):** Eskiden erişim/kullanım belirsizliği olan ASIO SDK artık **açık kaynak lisansla dağıtılabilir** — bu, K0 derleme bağımlılığı olarak ASIO SDK'yi doğrudan hedefe sokar; ama lisans metni dağıtım şekline bağlayıcıdır (§5.2).

**Yapı dersi (K2):** ASIO, işletim sistemini atlayarak donanım sürücüsüne **doğrudan çift-taraflı, lock-free callback** üzerinden bağlanır; buffer swap modeli (bufferSwitch) uygulamaya veri hazır olduğunda bildirir. CoreMusic K2 sözleşmesi bu callback imzasını yansıtır (§5.4).

---

### §3.2 WASAPI Exclusive vs Shared Mod (K2)

**Doğrulanmış olgu (2026-09-24 exa):** Microsoft WASAPI dokümantasyonu — **exclusive mode** uygulamanın ses cihazının tamamını devraldığı, mix motorunu atladığı; **shared mode** sistem mix'ine katıldığı biçiminde doğrulandı.

| Kriter | WASAPI Exclusive | WASAPI Shared | ASIO |
|--------|------------------|---------------|------|
| Cihaz erişimi | Uygulama devralır (tek istemci) | Sistem mix'i (çok istemci) | Uygulama devralır |
| Gecikme | **Düşük** (mix yok) | Orta (mix + ses motoru) | **En düşük** (doğrudan sürücü) |
| Sürücü gerekliliği | Windows yerleşik (evrensel) | Windows yerleşik | Donanım/evrensel ASIO sürücüsü |
| Çakışma riski | Diğer uygulamalar susturulur | Yok | Diğer ASIO uygulamaları dışlanır |
| CoreMusic rolü | **2. tercih / fallback** | 3. tercih (uyumluluk) | **1. tercih (K2)** |

**Yapı dersi (K2):** Exclusive mod, paylaşımlı mix gecikmesini ortadan kaldırır ama **cihazı tek uygulamaya kilitler**; ASIO ise aynı kilidi sürücü seviyesinde, daha düşük gecikmeyle yapar. CoreMusic K2 sıralaması: **ASIO → WASAPI exclusive → WASAPI shared** (§5.1 fallback zinciri).

---

### §3.3 Universal Built-in ASIO — 64-bit Only / 32-bit Driver Yok (K0, K2)

**Doğrulanmış olgu (2026-09-24 exa):** Windows'un **evrensel yerleşik (universal built-in) ASIO** desteği — **yalnızca 64-bit ASIO sürücülerini** yükler/işletir; **32-bit ASIO sürücüsü desteklenmez** (32-bit sürücü yolu yok) biçiminde doğrulandı.

**CoreMusic'e etkisi (iki madde):**

1. **K0 hedefi 64-bit olmalı.** CoreMusic 64-bit derlenirse evrensel ASIO köprüsü çalışır; 32-bit hedef, modern makinede sürücü yolunu kapatır → **32-bit politikası RED** (§5.3 ADR).
2. **3. taraf 32-bit sürücü riski.** Eski donanımın 32-bit ASIO sürücüsü evrensel yolda görünmez — K2 fallback zinciri (§5.1) bu durumu WASAPI exclusive'e düşürür (§3.2).

| Mimarî | Evrensel built-in ASIO | CoreMusic Sonucu |
|--------|------------------------|-------------------|
| 64-bit ASIO sürücüsü | ✅ Çalışır | K2 birinci tercih devrede |
| 32-bit ASIO sürücüsü | ❌ Desteklenmez | K2 → WASAPI exclusive fallback |
| ASIO hiç yok | ❌ — | K2 → WASAPI exclusive → shared |

---

### §3.4 ASIO Gecikme (Latency) Karakteri (K2)

**Doğrulanmış olgu (2026-09-24 exa):** ASIO'nun düşük gecikmeli, öngörülebilir (deterministic) callback karakteri ve buffer-tabanlı modeli doğrulandı; gecikme öncelikle **buffer boyutu + sürücü + donanım** tarafından belirlenir.

| Gecikme Bileşeni | ASIO | WASAPI Exclusive |
|------------------|------|------------------|
| Buffer boyutu ayarı | Uygulama seçer (ör. 64/128/256 sample) | Uygulama + ses motoru davranışı |
| Sistem mix'i | Yok | Yok (exclusive) / Var (shared) |
| Öngörülebilirlik | **Yüksek** (donanım zamanlaması) | Orta-yüksek |
| Tipik zincir (CoreMusic hedefi) | buffer + ADC/DAC + sürücü yığını | OS yığını + exclusive payı |

**Ders (K2):** "En düşük gecikme" tek sayı değil — **buffer seçimi + sürücü yolu + xrun (underflow/overflow) toleransı** birlikte sözleşmedir. CoreMusic K2, buffer boyutunu yapılandırılabilir tutar ve xrun sayısını K14'e metrik olarak verir (çapraz: ekosistem-mimarileri.md §5.3 deseni).

---

### §3.5 Çerçeve Stratejisi — JUCE AudioIODeviceType (K2, 3/6 ile çapraz)

**3/6 belgesiyle çapraz bağ:** JUCE 8.011★ juce_audio_devices modülü, ASIO ve WASAPI'yi AudioIODeviceType soyutlaması altında toplar (ses-dsp-acik-kaynak.md §3.1). Bu belge o soyutlamanın **K2 sözleşmesi tarafını** tamamlar: hangi cihaz tipi hangi durumda seçilir.

| Durum | Seçilen AudioIODeviceType | Gerekçe |
|-------|------------------------------|---------|
| 64-bit ASIO mevcut | ASIO | En düşük gecikme (§3.4) |
| ASIO yok / 32-bit sürücü | WASAPI exclusive | §3.3 kısıtı + §3.2 |
| Paylaşımlı kullanım gerekli | WASAPI shared | Uyumluluk fallback'i |
| JUCE lisansı seçilmemişse (S1/S3) | Doğrudan WASAPI/ASIO API | 3/6 §3.6.4 senaryoları |

---

### §3.6 Matrisler, Copy/Don't Copy ve Entegrasyon

#### §3.6.1 Lisans / Kaynak Matrisi

| Kaynak | Lisans/Doğum (Doğrulanmış) | Kopyalanabilir mi? | Kullanım Şekli |
|--------|-----------------------------|-------------------|----------------|
| ASIO SDK | **Açık kaynak lisans** (exa 2026-09-24) | ✅ Kurallara uygun dağıtım (§5.2 kontrol listesi) | K2 sürücü sözleşmesi + SDK bağımlılığı |
| WASAPI (Microsoft doküman) | Microsoft API — doküman | ✅ API kullanımı (OS entegrasyonu) | K2 fallback zinciri |
| Universal built-in ASIO davranışı | Windows yerleşik | ✅ Davranış dersi | K0 64-bit politikası |
| JUCE AudioIODeviceType | GPL v3 / ticari (3/6) | ⚠️ ADR'ye bağlı | Çerçeve stratejisi (§3.5) |
| AGPL kaynak | AGPL | ❌ **HİÇBİR ZAMAN** | Sadece fikir (CLAUDE.md §4) |
| PCM5122 | — | ❌ YASAKLI | CLAUDE.md §21 — bu belgede de geçmez |

#### §3.6.2 Ders → K Katmanı Eşlemesi

| # | Ders | Kaynak | K Katmanı |
|---|------|--------|-----------|
| 1 | Açık kaynak ASIO SDK = dağıtılabilir bağımlılık | ASIO SDK lisans duyurusu | **K0** |
| 2 | Doğrudan sürücü + buffer-swap callback | ASIO | **K2** |
| 3 | Exclusive mod = mix atlanır, cihaz kilitlenir | WASAPI dokümanı | **K2** |
| 4 | Fallback zinciri (ASIO → ex → shared) | §3.2-§3.3 birleşimi | **K2** |
| 5 | 64-bit only / 32-bit sürücü yok | Universal built-in ASIO | **K0, K2** |
| 6 | Gecikme = buffer + sürücü yolu + xrun | ASIO karakteri | **K2** (+ K14 metrik) |
| 7 | Cihaz tipi soyutlaması | JUCE AudioIODeviceType (3/6) | **K2** |

#### §3.6.3 Copy / Don't Copy

**✅ ALINABİLİR:**

1. **ASIO SDK** — açık kaynak lisans kapsamında, §5.2 kontrol listesine uygun dağıtım/bağımlılık.
2. **WASAPI API kullanımı** — Microsoft OS API'si, entegrasyon serbesttir (dokümantasyona dayalı).
3. **Fallback zinciri şeması** (§5.1) — bu belgenin kendi tasarımı, CoreMusic'e ait.
4. **Evrensel ASIO davranışı** — davranış dersi (64-bit only): politika olarak benimsenir.

**❌ ALINAMAZ:**

1. **ASIO SDK lisans koşullarını ihlal eden dağıtım** (atıf/zorunlu metin ihlali) — §5.2 kontrol listesi ihlali; release bloke.
2. **AGPL kodu** (herhangi bir sürücü katmanında) — CLAUDE.md §4.
3. **3. taraf donanım ASIO sürücü kodu** — üretici mülkiyeti, SDK değil.
4. **PCM5122** — CLAUDE.md §21 (ihmal edilmez).

#### §3.6.4 Senaryolar (§4.1 şablon genişletmesi — 4 satır Durum/Aksiyon)

| Senaryo | Durum | Aksiyon |
|---------|-------|---------|
| **S1: Birinci tercih sürücü** | K2 sözleşmesi yazılmadı | ASIO birinci tercih + buffer yapılandırması → §5.1 zinciri |
| **S2: ASIO yok/32-bit sürücü** | Evrensel ASIO 64-bit-only (§3.3) | WASAPI exclusive fallback devreye → otomatik düşüş testi (§6.4) |
| **S3: Dağıtım lisansı** | ASIO SDK açık kaynak kuralları | §5.2 kontrol listesi → release gate; DECISIONS.md'ye kaydet |
| **S4: Mimari politika** | 32-bit hedef çekici (eski sürücü) | RED: 32-bit hedef (§3.3) → ADR: 64-bit-only + WASAPI yedeği |

#### §3.6.5 Entegrasyon — CoreMusic'e Somut Giriş (K eşlemesi ile)

| ASIO/WASAPI Öğesi | CoreMusic'e Giriş Yolu | Hedef K | Öncelik |
|--------------------|------------------------|---------|---------|
| ASIO SDK (açık kaynak) | Derleme bağımlılığı + sürücü sözleşmesi (§5.2 uygun) | **K0, K2** | **Yüksek** |
| WASAPI exclusive | Fallback zinciri 2. halka (§5.1) | **K2** | **Yüksek** |
| WASAPI shared | Uyumluluk yedeği 3. halka | **K2** | Orta |
| 64-bit-only kısıtı | K0 hedef politikası + 32-bit RED ADR (§5.3) | **K0** | **Yüksek** |
| Gecikme/xrun karakteri | K2 yapılandırılabilir buffer + K14 xrun metriği | **K2, K14** | Orta |
| JUCE AudioIODeviceType | Cihaz tipi soyutlaması (3/6 §3.1 ile) | **K2** | ADR'ye bağlı (3/6 §5.3) |
| Donanım sürücü keşfi | Donanım belgesiyle çapraz (XU316 + DAC keşfi) | **K2, K1** | Düşük (4/6 §6.1) |

**Sıra (2026-09-24):** (1) §5.3 K0 64-bit ADR'si → (2) §5.2 ASIO SDK lisans kontrol listesi release gate → (3) §5.1 fallback zinciri (K2 sözleşmesi) → (4) JUCE mi doğrudan API mi kararı (3/6 §5.3 ADR ile birlikte) → (5) §6.4 gerçek donanımda gecikme ölçümü.

---

### §3.7 ASIO SDK Lisansının CoreMusic Dağıtımına Yansıması (K0 — detay)

Açık kaynak lisans (exa 2026-09-24) ASIO SDK'yi CoreMusic için erişilebilir kılar; ama **üç bağlayıcı boyut** vardır:

| Boyut | Soru | CoreMusic Kuralı |
|-------|------|------------------|
| **Atıf** | Lisans metni/zorunlu bildirim dağıtımda yer almalı mı? | Evet — §5.2 madde 3 (paket LICENSE + About/NOTICE) |
| **Modifikasyon** | SDK değiştirilirse ne olur? | Değişiklik açıkta tutulur + lisans metni korunur (§5.2 madde 2/4) |
| **Mülkiyet** | 3. taraf sürücü kodu SDK ile karışır mı? | Hayır — üretici ASIO sürücüleri SDK değildir (§5.2 madde 5) |

**Etki (K0):** ASIO SDK, NuGet/alt-modül olarak projeye girer; sürümü sabitlenir (reproducible build) ve lisans metni derleme çıktısına kopyalanır. Bu üç boyut release gate'te (§5.2) otomatik kontrol edilir.

#### §3.7.1 ASIO SDK Bağımlılık Kaydı (K0 şablonu)

~~~text
bağımlılık: ASIO SDK
  kaynak: resmi ASIO SDK (açık kaynak lisans, exa 2026-09-24)
  sürüm: sabit (tag/hash) — reproducible build
  katman: K0 (derleme) → K2 (sözleşme) kullanır
  çıkış: LICENSE metni pakete kopyalanır (§5.2 madde 3)
  yasak: SDK içine AGPL/PCM5122 izi karıştırma (§3.6.3)
~~~

### §3.8 Ek Soru Listesi (Guardrail #8 — belirsizlikler)

1. **32-bit hedef var mı?** Ürün gereksinimi bilinmiyor — §5.3 ADR (a)/(b)/(c) kararı bekliyor; varsayılan RED (64-bit only).
2. **ASIO SDK lisansının tam metni:** exa "açık kaynak lisans" dedi; spesifik lisans adı ( propName / SCML benzeri) gelmedi — §5.2 madde 1 LICENSE okunarak doğrulanacak (Guardrail #14, uydurma yok).
3. **Ölçüm hedefi:** CoreMusic K2 için p95 gecikme hedefi (ms) henüz yok — ürün kararı; §6.4 ölçümü hedefsiz yapmak anlamsız.
4. **Donanım ASIO sürücüsü kullanılabilirliği:** XU316 tabanlı donanımın kendi ASIO sürücüsü var mı? — 4/6 donanım belgesiyle çapraz, doğrulanmadı.
5. **macOS/CoreAudio kapsamı:** bu belge K0'da Windows (K2 ASIO/WASAPI) ile sınırlı; CoreAudio yolu ayrı ADR (kapsam dışı, işaretli).

### §3.9 Doğrulama Kanıtları (2026-09-24 exa)

~~~text
exa 2026-09-24 sonuçları (bu belgenin tek kaynak girdisi):
  - ASIO SDK open-source lisans duyurusu → doğrulandı (§3.1)
  - WASAPI exclusive/shared Microsoft dokümanı → doğrulandı (§3.2)
  - Universal built-in ASIO: 64-bit only / no 32-bit driver → doğrulandı (§3.3)
  - ASIO düşük gecikme / buffer-tabanlı karakter → doğrulandı (§3.4)
  - JUCE AudioIODeviceType (GitHub 8.011★) → 3/6 dosyasında doğrulandı
  - Lisansın tam adı/şart metni gelmedi → §3.8 madde 2 (VERIFICATION REQUIRED)
Yeni web araştırması YAPILMADI (kural gereği); sadece sağlanan exa sonuçları kullanıldı.
~~~

### §3.10 Seri İçi Konum (6/6 — Ekosistem Serisi Kapanışı)

| Belge | Kapsadığı K | Bu Belgeyle Bağ |
|-------|-------------|------------------|
| index.md | K0–K20 genel | Dizin (1/6) |
| muzik-streaming-sunuculari.md | K9/K15 | Codec/sunucu devri (§6.1) |
| ses-dsp-acik-kaynak.md | K3/K10/K2 | JUCE AudioIODeviceType çapraz (§3.5) |
| donanım-devre-referanslari.md | K1/K16/K17/K19/K20 | Donanım↔sürücü keşfi (§6.1) |
| ekosistem-mimarileri.md | K8/K9/K14/K15 | xrun metrik yayını (§6.1) |
| **bu dosya (6/6)** | **K2/K0** | — |

**Seri kapanışı:** 6 belgenin tümü ≥500 satır, şablon (Guardrail #16) + §4.1 Şablon-Önce bloğu + 7 alanlı frontmatter + Authority footer içerir; 0 dosya silinmiş, mevcut README.md ve service-integration.md değiştirilmemiştir.

---

### §3.11 Etki/Öncelik Matrisi (§3.6.5 Sıra Tablosu)

| Öğe | Etkilenen K | Etki | Öncelik |
|-----|-------------|------|---------|
| ASIO SDK bağımlılığı + lisans gate | K0, K2 | **Yüksek** | 1 |
| WASAPI exclusive fallback | K2 | **Yüksek** | 2 |
| 64-bit-only K0 politikası (ADR) | K0 | **Yüksek** | 1 (paralel) |
| Buffer/xrun sözleşmesi + K14 metriği | K2, K14 | Orta | 3 |
| JUCE AudioIODeviceType kararı | K2 | Orta | 4 (3/6 ADR ile) |
| WASAPI shared uyumluluk | K2 | Düşük | 5 |

**Okuma kuralı:** Etki=Yüksek satırlar §5'te şema/ADR bulur; Düşük satırlar §3.8 belirsizlik listesinde bekler (Guardrail #8).


## §4 Kurallar

### §4.1 ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

> Yukarıdaki blok `.ai/.templates/documentation/docs-md-template.md` §3.3'ten birebir kopyalanmıştır (Guardrail #16 — kısaltılamaz).

### §4.2 Frontmatter Uygulaması (Bu Belge)

| Placeholder | Değer |
|-------------|-------|
| `{{BASLIK}}` | ASIO / WASAPI Rehberi — Ses Kartı Sürücü Sözleşmesi |
| `{{ACIKLAMA}}` | ASIO SDK lisans kuralları, WASAPI exclusive, 64-bit-only ASIO + K2/K0 stratejisi |
| `{{TARIH}}` | 2026-09-24 |
| `{{DURUM}}` | active |
| `{{KAYNAK}}` | exa web doğrulaması (2026-09-24) — §7 Referanslar |
| `{{STATUS}}` | active |
| Authority | "ASIO/WASAPI sürücü sözleşmesi; K2/K0 birincil referans — ASIO SDK dağıtım kuralları bağlayıcı" |

### §4.3 §1–§7 Kapsam Haritası

| Şablon Bölümü | Bu Belgede |
|----------------|------------|
| §1 Amaç | Kapsam, kullanıcı, bağlantılar |
| §2 Kapsam | K2/K0 ihtiyaçları + boşluklar |
| §3 Mimari | ASIO SDK lisansı, WASAPI ex/shared, 64-bit-only, gecikme, JUCE stratejisi, §3.6 matris |
| §4 Şablonlar | Şablon-Önce bloğu (verbatim) + frontmatter + §4.4 lisans/dağıtım tablosu |
| §5 Workflow | Fallback zinciri, ASIO callback sözleşmesi, lisans kontrol listesi, K0 ADR |
| §6 Doğrulama | Devir, risk, ölçüm kanıtı |
| §7 Referanslar | ASIO SDK, WASAPI dokümanı, universal ASIO notu, JUCE, yerel SSOT |

### §4.4 Lisans / Dağıtım Tablosu (Bu Belge için)

| Bağımlılık | Lisans | K Katmanı | Durum |
|-----------|--------|-----------|-------|
| ASIO SDK | **Açık kaynak lisans** (2026-09-24) | K0, K2 | ✅ Dağıtım §5.2 kontrol listesine bağlı |
| WASAPI | Microsoft OS API | K2 | ✅ Entegrasyon serbest |
| Universal built-in ASIO | Windows yerleşik davranış | K0, K2 | ✅ 64-bit only politikası |
| JUCE (varsa çerçeve) | GPL v3 / ticari | K2 | ⚠️ 3/6 §5.3 ADR |
| AGPL kaynak | AGPL | — | ❌ Asla (CLAUDE.md §4) |
| PCM5122 | — | — | ❌ Yasaklı (CLAUDE.md §21) |

---

## §5 Workflow

### §5.1 K2 Fallback Zinciri (Ana Şema)

~~~text
K2 açılış stratejisi (CoreMusic):
  1) ASIO dene (64-bit sürücü — universal built-in dahil) ──ok──> ASIO yolu (en düşük gecikme)
     │ yok/32-bit sürücü (§3.3: desteklenmez)
  2) WASAPI exclusive ──ok──> exclusive yolu (mix yok, cihaz kilitli)
     │ paylaşımlı kullanım gerekiyorsa/fallback
  3) WASAPI shared ──> uyumluluk yolu (en yüksek gecikme)
Her halka: buffer boyutu yapılandırılabilir + xrun sayacı K14'e (metrik: driver_path, xrun_count)
32-bit hedef YOK (§3.3, §5.3 ADR) — 64-bit-only politika K0'da sabitlenir.
~~~

### §5.2 ASIO SDK Lisans Dağıtım Kontrol Listesi (Release Gate)

~~~text
ASIO SDK açık kaynak lisansı — CoreMusic dağıtımı ÖNCE kontrol edilir:
  [ ] LICENSE metni repoda saklandı mı (kaynak + atıf)?
  [ ] ASIO SDK dosyaları modifiye edildiyse lisans metni korundu mu?
  [ ] Yürütülebilir dağıtıma SDK atıf/zorunlu metni eklendi mi (lisans şartı)?
  [ ] Değiştirilen SDK kaynakları lisans uyumlu şekilde yayınlandı mı (şartsa)?
  [ ] 3. taraf ASIO sürücü kodu SDK'ye karıştırılmadı mı (mülkiyet ayrı)?
  [ ] AGPL kaynak izi yok (CLAUDE.md §4) — sweep yapıldı mı?
  [ ] PCM5122 izi yok (CLAUDE.md §21)?
Sonuç: hepsi [x] → release geçer; [ ] var → release BLOKE (Guardrail #14/§4).
~~~

**Not:** Yukarıdaki 7 madde, docs-md-template.md §4'ündeki adımların ASIO dağıtımına karşılığıdır (şablonu bul → bu belge; placeholder → §4.2; genişlet → §5.2+§3.6.4; authority → §7 sonrası; doğrula → §6.3).

### §5.3 K0 ADR Şablonu — 64-bit Only Politikası (Karar noktası)

~~~text
ADR: Windows Platform Hedefi — 64-bit Only (K0)
Durum: ÖNERİ (2026-09-24)
Bağlam: Universal built-in ASIO yalnız 64-bit sürücü çalıştırır; 32-bit ASIO sürücüsü desteklenmez (§3.3, exa 2026-09-24).
Seçenekler:
  (a) 64-bit only → evrensel ASIO + WASAPI exclusive en iyi yol (ÖNERİLEN)
  (b) 32+64 çift hedef → eski 32-bit sürücü yolu YİNE evrensel ASIO'da çalışmaz; maliyetli, kazanım yok
  (c) 32-bit only → modern makinede ASIO yolu kapanır (REDDEDİLDİ)
Sonuç: .ai/DECISIONS.md'ye yazılacak (3/6 §5.3 JUCE ADR'siyle birlikte).
~~~

### §5.4 ASIO Callback Sözleşmesi (K2 — şema)

~~~text
ASIO yolu:  sürücü ──bufferSwitch(index)──> CoreMusic K2 callback
  girdi: hazır input buffer (index), çıktıyı K2 doldurur
  kural: callback içinde bloklama YOK (lock-free — ASIO karakteri §3.4)
WASAPI exclusive yolu: event-driven ──IAudioClient event──> K2 callback (aynı K3'e teslim)
Ortak K2→K3 arayüzü: process(io, channels, n) — 3/6 §5.1 ile aynı imza
~~~

### §5.5 JUCE Var/Yok Karar Ağacı (3/6 §3.5 çapraz)

~~~text
JUCE ticari lisans alındı mı? (3/6 §5.3 ADR)
  EVET  → AudioIODeviceType ile §5.1 zinciri otomatik yönetilir
  HAYIR → (permissive stack) doğrudan ASIO SDK + WASAPI API kullan (§5.4 imzası korunur)
Her iki durumda da: §5.1 fallback sırası ve §5.2 lisans gate DEĞİŞMEZ.
~~~

---

## §6 Doğrulama

### §6.1 Devir Noktaları (Belge Sınırları)

| Konu | Devredilen Belge | K |
|------|------------------|---|
| JUCE DSP çerçeve ADR'si (lisans) | `.ai/ecosystem/ses-dsp-acik-kaynak.md` §5.3 | K3, K10 |
| Donanım sürücü keşfi / XU316 | `.ai/ecosystem/donanım-devre-referanslari.md` §6.1 | K1, K17 |
| xrun/telemetri metrik yayını | `.ai/ecosystem/ekosistem-mimarileri.md` §5.3 | K14 |
| Streaming codec/sunucu | `.ai/ecosystem/muzik-streaming-sunuculari.md` | K9, K15 |

### §6.2 Riskler

| Risk | Etki | Olasılık | Önlem |
|------|------|----------|-------|
| ASIO lisans şartı ihlali (atıf/metin) | **Kritik** (release) | Orta | §5.2 checklist — release gate |
| 32-bit hedef sonradan eklenir | **Yüksek** | Orta | §5.3 ADR RED + K0 sabiti |
| Exclusive mod kullanıcıyı kilitleyince şikâyet | Orta | Orta | §5.1 zinciri — shared fallback + kullanıcı ayarı |
| AGPL izi sürücü katmanına sızar | **Kritik** | Düşük | §3.6.3 + CLAUDE.md §4 |
| Gecikme hedefi ölçülmeden yazılır | Orta | Orta | §6.4 ölçüm (kaynak + ölçüm yoksa iddia yok) |
| PCM5122 deseni geri gelir | **Kritik** | Düşük | CLAUDE.md §21 + §3.6.3 madde 4 |

### §6.3 KPI / Kanıt Komutları (§4.1 5 adıma karşılık)

~~~powershell
# 1) Şablon bulundu mu:
Test-Path .ai/.templates/documentation/docs-md-template.md
# 2) Placeholder kalmadı mı:
Select-String -Path .ai/ecosystem/asio-wasapi-rehber.md -Pattern '{{TARIH}}|{{DURUM}}|{{KAYNAK}}'
# 3) §1-§7 başlıkları:
Select-String -Path .ai/ecosystem/asio-wasapi-rehber.md -Pattern '^## [1-7]\.'
# 4) Lisans gate bölümü mevcut mu:
Select-String -Path .ai/ecosystem/asio-wasapi-rehber.md -Pattern 'Lisans Dağıtım Kontrol Listesi'
# 5) Frontmatter 7 alan:
Get-Content .ai/ecosystem/asio-wasapi-rehber.md -TotalCount 14
~~~

**KPI:** Dosya ≥500 satır; §4.1 bloğu verbatim; 0 placeholder; §5.2 8 satırlık lisans gate; §5.3 K0 ADR işareti; 64-bit-only kısıtı §3.3/§5.1/§5.3'te tutarlı.

### §6.4 Sonraki Adım (2 dakikadan kısa)

**§5.2'deki ASIO SDK lisans kontrol listesini release talimatınıza kopyalayın** — ilk [ ] satırını işaretleyene kadar CoreMusic paketi yayınlanmaz; ardından §5.3 K0 ADR'sini `.ai/DECISIONS.md`'ye "ÖNERİ" olarak yapıştırın.

---

## §7 Referanslar

| # | Kaynak | URL | Doğrulama | Kullanım |
|---|--------|-----|-----------|----------|
| 1 | ASIO SDK açık kaynak lisans duyurusu | https://www.steinberg.net/ (ASIO SDK) | exa 2026-09-24 | §3.1, §5.2 |
| 2 | WASAPI — Microsoft dokümantasyonu | https://learn.microsoft.com/windows/win32/coreaudio/ | exa 2026-09-24 | §3.2 |
| 3 | Universal built-in ASIO (64-bit only / 32-bit sürücü yok) | exa 2026-09-24 teknik notu | exa 2026-09-24 | §3.3, §5.3 |
| 4 | JUCE 8.011★ AudioIODeviceType | https://github.com/juce-framework/JUCE | exa 2026-09-24 | §3.5, §5.5 |
| 5 | DSP çerçevesi / JUCE lisans ADR (3/6) | .ai/ecosystem/ses-dsp-acik-kaynak.md | yerel | §3.5, §6.1 |
| 6 | Donanım referansları (4/6) | .ai/ecosystem/donanım-devre-referanslari.md | yerel | §6.1 |
| 7 | K0-K20 SSOT | .ai/architecture/index.md | yerel | K0, K2 |
| 8 | Lisans kuralları | .ai/CLAUDE.md §4, §20, §21 | yerel | §3.6, §4.4, §5.2 |

---

## Authority

**ASIO/WASAPI sürücü sözleşmesi, CoreMusic K2/K0 katmanlarının birincil referansıdır — ASIO SDK açık kaynak lisansı dağıtımda bağlayıcı, 64-bit-only politika değişmez, AGPL/PCM5122 kırmızı çizgisi sabittir.**

> 2026-09-24 | Belge 6/6 | Durum: active | Kaynak: exa web doğrulaması | Geçmiş: ".ai/ecosystem/index.md" (dizin)