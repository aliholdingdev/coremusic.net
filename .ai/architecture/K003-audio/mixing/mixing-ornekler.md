---
id: mixing-ornekler
title: "Karışım — Kullanım Örnekleri"
node_id: K003-EXP-MIXING
parent_id: K003-audio
type: architecture
group: kod
status: reference
tags: [karistirma, summing, limiter, denge, ornekler]
created: 2026-10-13
updated: 2026-10-13
children: []
---

# Karışım — Kullanım Örnekleri

> Bu dosya K003 kapsamında REFERANS/ÖRNEK niteliğindedir: kod, akış ağaçları, negatif
> örnekler ve eşlemeler içerir. Gerçeğe yakın DISK KAYDI değildir (bkz. §9 Dürüstlük).
> Kapsam (sorumluluk/gereksinim/kısıt/kapı) tarafı için: `mixing-kapsam.md`.

**Kapsam dosyası:** `mixing-kapsam.md` (id: mixing-kapsam · parent: K003-audio)

## 1. SORUMLULUK ÖZETİ

- Düz toplama (summing), ses/dezibel ölçümü, korelasyon, seviye dengeleme, limiter
  gözetimi, DC tanılama (gerçekleşim beklentisi doğrulanmadı).
- Girdeler: `input-kapsam.md` tür/shm modelleri · sürücü: `driver-kapsam.md` yazma
  kontrolü · ölçüm: `diagnostics-kapsam.md`.

## 2. REFERANS AKIŞ AĞAÇLARI (salt-okunur taslak — DERLENMEZ)

### 2.1 Düz toplama akışı (kavramsal — ` ```text ` bloğu, kod değildir)

```text
MixBus(out[N]) <- sum over sources S[k]:
  contribution = S[k].gain * S[k].pcm[ch]
  out[ch] += contribution
clamp/limiter per ADR-004 (0 dBTP, lookahead — bkz. §6)
```

### 2.2 Ölçüm akışı (kavramsal)

```text
segment (RMS) -> level (LUFS-approx) -> min/max -> correlation (pair)
DC estimate (block mean) -> expose measurement-only
```

> Bu ağaçlar tasarım referansıdır; derlenebilir kod değildir, hiçbir yerde
> `main`/derleme iddiası taşımaz.

## 3. DOĞRULAMA DURUMLARI — ÖRNEK DURUMLAR

| Durum | Gözlenen | Beklenen | Kayıt |
|---|---|---|---|
| Düz toplama doğruluğu | SUM = S1+S2+S3 | idempotent | ölçüm AÇIK |
| Taşma/limit | — | 0 dBTP (ADR-004) | ölçüm AÇIK |
| Denge (LUFS) | — | yayın standardı (ADR-004) | ölçüm AÇIK |
| Korelasyon | — | mono uyumu | ölçüm AÇIK |
| DC tanılama | — | eşiği gör (ADR-038) | ölçüm AÇIK |

## 4. ÖLÇÜM BEKLENTİLERİ (sayı uydurmadan)

- Düz toplama: doğrusal süperpozisyon (tutarlılık beklenir) → ölçüm AÇIK.
- Limiter: 0 dBTP aşım gözlenmemeli (ADR-004) → ölçüm AÇIK.
- DC: blok ortalama |DC| eşiği → eşiği KAPALI/ADR-038 → ölçüm AÇIK.

## 5. KULLANIM SINIFLARI (tasarım düzeyi — işlev adları gerçektir)

| Sınıf | Amaç | Doğrulanmış işlev/altyapı |
|---|---|---|
| Summing | çok kaynaklı düz toplama | sayısal döngü (gerçek işlev adı yok — VERIFICATION REQUIRED) |
| Level | segment RMS / dosya tepesi / integrated | `juce::Decibels::` · `juce::AudioBuffer<float>::getRMSLevel` (JUCE API kılavuzu) |
| Denge | LUFS aprox ölçüm | VERIFICATION REQUIRED (kütüphane/dahili yöntem bilinmiyor) |
| Limiter | ADR-004 kapıları | tasarım §mixing-kapsam §5 — uygulama KAPALI |
| Correlation | kanal çifti korelasyonu | sayısal döngü (standart yöntem) |
| DC tanılama | blok ortalama | sayısal döngü + `std::atomic` yayın (ADR-038 eşiği) |

`juce::Decibels` alt kırılımı (açık). Dosya sonu okuma: `juce::AudioFormatManager`
ile `juce::AudioFormatReader::read` (muhtemel; kesin eşleme ⚠️ VERIFICATION REQUIRED).

## 6. KÖPRÜ — ADR-004 4 DURUM (taşıyıcı — bkz. `mixing-kapsam.md` §5)

| Durum | Kapı |
|---|---|
| D1: hedefe göre — | KAPALI |
| D2: eşiğin altı — | KAPALI |
| D3: eşiğe eşit — | KAPALI |
| D4: eşiğin üstü — | KAPALI |

## 7. AKIŞ ŞABLONLARI (salt-okunur referans — kod DEĞİL)

### 7.1 Düz toplama eşlemesi (durum özeti)

```text
IN:  src1..n, gains -> SUM -> limiter -> out
POST: level/corr/DC report (measurement-only)
GATE: 0 dBTP (ADR-004) | ölçüm AÇIK
```

### 7.2 Denge akışı (özet)

```text
per-source gain -> LUFS aprox (integrated) -> target (ADR-004) -> gate KAPALI
```

### 7.3 Limiter akışı (özet)

```text
pre-gain -> lookahead -> ceiling -> release -> POST 0 dBTP ölçüm
```

> Bu şablonlar kavramsaldir; işlev adı/derleme iddiası taşımaz.

## 8. NEGATİF ÖRNEKLER (bu dosya YAZMAZ — yasak kalıplar)

| # | Yasak | Neden |
|---|---|---|
| N1 | `'21.0 dB'` gibi uydurma ölçüm | ölçüm yok |
| N2 | `LimA.dll`/`dynamA` adı | doğrulanmadı |
| N3 | `dasLimit`/`dasSum` işlev adı | doğrulanmadı (§5 sütun notu) |
| N4 | `0.86 LU` hedef | doğrulanmadı |
| N5 | derlenebilir `main` iddiası | yalnız ` ```text ` serbest |

## 9. DÜRÜSTLÜK — TAŞMA DENETİMİ VE ATIF

**Veri kaydı:** Bu dosya kapsam/ölçüm/tasarım referansı içerir. Gözlemsel ölçüm
İÇERMEZ (ölçüm kapıları AÇIK). Uydurma değer yok; işlev adları (§5) ya JUCE API
klavuzu tabanlı ya da VERIFICATION REQUIRED işaretlidir.

**Komşu eşleme:** Sorumluluk → `mixing-kapsam.md` §1 · ADR-004 kapıları → §5/§6 ·
girdi → `input-kapsam.md` (t/shm/eşleme) · sürücü → `driver-kapsam.md` (yazma) ·
ölçüm → `diagnostics-kapsam.md` (tayf/RMS).

**Açık soru bağlama (yeni soru açılmadı):** R1–R6 + M6 (mixing-kapsam §9 — KAPALI);
ölçüm kapıları 8 (AÇIK).

**Atıf:** `audio-master.md` §2.2 (durumlar) · §3 (girdi) · §4 (karışım/loudness) ·
§5 (kazanç) · §8 (ölçüm) — sayfa içeriği referans değildir (K003 aynı vault türü).

## 10. KAPI LİSTESİ (kapsam dosyasından — durum KAPALI/AÇIK)

| Kapı | Durum |
|---|---|
| R1 (uzantı) | KAPALI |
| R2 (sayfa) | KAPALI |
| R3 (girdi) | KAPALI |
| R4 (toplam) | KAPALI |
| R5 (rapor) | KAPALI |
| R6 (psd) | KAPALI |
| M6 (kaynak) | KAPALI |
| Ölçüm 8 kapısı | AÇIK |

Tek dosya etkileşimi: 7 KAPALI + 1 AÇIK.

## 11. DÜRÜSTLÜK

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
