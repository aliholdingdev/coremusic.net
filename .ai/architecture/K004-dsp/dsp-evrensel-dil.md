---
node_id: K004-dsp/evrensel-dil
node_type: subdomain
status: draft
parent_id: K004-dsp
children: []
depends_on:
  - K002-electroncis
  - K003-audio
  - K005-firmware
  - K006-codebase
evidence:
  - ".ai/architecture/K004-dsp/dsp-master.md §A (12 çocuk, algoritma adları) — okundu 2026-10-18"
  - ".ai/architecture/K004-dsp/dsp-kararlar.md (KD- önek kuralı) — okundu 2026-10-18"
  - "Websearch 2026-10-18 — EBU R128 / ITU-R BS.1770 / RBJ Cookbook terim karşılıkları"
validation: "20 terim EN/TR; K003 evrensel-dil (backend terimleri) ile KAPSAM SINIRI NET"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — dsp-evrensel-dil.md beyazliste dışı mevcut dosya
  değil (üretildi); sonraki turda K003 sözlüğüyle çakışma taraması yapılacak.
group: mimari
---

# K004-dsp — Evrensel Dil (EN/TR Sözlük)

## §1 — ELI15: Bu nedir?

Ses işleme terimlerinin tek karşılık defteri. "Compressor mı, sıkıştırıcı mı?"
sorusuna buradan bakılır. K003 sözlüğü **backend/sürücü** terimlerini, bu
sözlük **algoritma/sinyal** terimlerini sabitler; ikisi farklı dosyadır,
çakışmaz.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | DSP algoritma terimleri: filtre, dinamik, loudness, ölçüm, sinyal kavramları |
| Kapsam dışı | Backend/sürücü terimleri (K003-audio/evrensel-dil) · ağ terimleri (K010-network) |

## §3 — Sözlük (EN ↔ TR, 20 terim)

| # | English | Türkçe | Kısa tanım (TR) | Kaynak |
|---|---|---|---|---|
| 1 | Biquad | İki kutuplu filtre | 2 katsayı + 2 durum elemanı; EQ'nun tuğlası | dsp-kod-ornekleri §3.1 |
| 2 | Impulse response (IR) | Düğüm yanıtı | Sistemin tek darbeyle açılan karakteri | dsp-master §A |
| 3 | Convolution | Evrişim | IR ile sinyali çarpma (reverb emülasyonu) | dsp-master §A |
| 4 | Compressor | Sıkıştırıcı | Yüksek seviyeyi oranı ile kısma (threshold/ratio) | dsp-kod-ornekleri §3.2 |
| 5 | Limiter | Sınırlayıcı | Örneği sert tavanda tutan brickwall | dsp-kod-ornekleri §3.4 |
| 6 | Threshold | Eşik | İşlemin başladığı seviye (dBFS) | §3.2 |
| 7 | Ratio | Oran | Eşik üstü aşımın kaçta bire kısıldığı | §3.2 |
| 8 | Attack / Release | Saldırı / Bırakma | Gain'in yükselme/alçalma zaman sabitleri | §3.2 |
| 9 | Envelope | Zarf | Zamanla değişen genlik izleyicisi | §3.2 |
| 10 | LUFS | LUFS (ses seviyesi birimi) | K-weighting'li loudness; EBU R128 hedefi -23 | §3.3 · §3.6 |
| 11 | True Peak (dBTP) | Gerçek tepe | Örnekleme-arası tepe; yayında -1 dBTP sınırı | §3.6 |
| 12 | K-weighting | K-ağırlıklandırma | İnsan kulağı frekans eğrisi (BS.1770) | §3.3 |
| 13 | THD+N | Toplam harmonik bozulma + gürültü | Temel dışındaki her şeyin oranı (%0.01 kapısı) | `.ai/AGENTS.md §5` |
| 14 | Null test | Sıfır testi | İşlenmiş vs referans farkı → bit-exact kanıtı | dsp-test-stratejisi T-D6 |
| 15 | Sweep | Frekans taraması | Üstel sine tarama (Farina) — yanıt düzlemi ölçümü | §3.7 |
| 16 | Oversampling | Aşırı örnekleme | Bozulma ürünlerini taşımak için örneklemeyi yükseltme | dsp-master §A |
| 17 | Aliasing | Örnekleme karışması | Nyquist üstü içeriğin yanlış frekansa dönmesi | §3.5 (Nyquist sınırı) |
| 18 | FIR / IIR | Sonlı / sonsuz geri beslemeli filtre | FIR = sabit gecikme, IIR = geri beslemeli (biquad) | §3.1 |
| 19 | Latency compensation | Gecikme telafisi | Zincir gecikmesini diğer yollarda eşitleme | K003 köprüsü (K00305) |
| 20 | Bypass | Atlatma | Bloğu sessizce devre dışı; sinyal birebir geçer | §3.9 |

## §4 — Yazım kuralları

| Kural | Örnek |
|---|---|
| Birimler Türkçe ek almaz | "-23 LUFS", "-1 dBTP", "4:1 oranı" |
| Standart adlar olduğu gibi kalır | "LUFS", "THD+N", "FIR/IIR" |
| Ondalık nokta (CI uyumu) | "0.01" — "0,01" DEĞİL |
| Eşik/oran her zaman dB/oran birimiyle | "-18 dBFS", "4:1" — çıplak sayı yok |

## §5 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| 20 terim EN/TR | bu dosya §3 | ✓ 2026-10-18 |
| Terim kaynakları | `dsp-master.md` §A · kod-ornekleri §3 (bu oturum) | ✓ 2026-10-18 |
| K003 sözlüğüyle sınır | K003 = backend, K004 = algoritma | ✓ 2026-10-18 |
| Mevcut dosya çakışması | beyazliste dışı dosya yoktu → bu dosya YENİ | ✓ 2026-10-18 |

## §5 — Terim üretimi kuralı (sonraki terimler için)

```yaml
en: "English term"
tr: "Türkçe karşılık"
tanim: "tek cümlelik kısa tanim (TR)"
kaynak: "<dosya> §<bölüm>" | "resmi doküman"
sinif: filtre | dinamik | loudness | olcum | sinyal
```

### Sınıf dağılımı (bu sürüm)

| Sınıf | Adet | Terimler (kısa) |
|---|---|---|
| Filtre | 4 | #1, #18, #17, #20 |
| Dinamik | 5 | #4, #5, #6, #7, #8 (+ #9) |
| Loudness | 3 | #10, #11, #12 |
| Ölçüm | 3 | #13, #14, #15 |
| Sinyal kavramı | 5 | #2, #3, #16, #17, #19 |

### Kapsam sınırı komşuları

| Komşu sözlük | Kapsamı | Ayrım çizgisi |
|---|---|---|
| K003-audio/evrensel-dil | Backend/donanım (ASIO, xrun, hot-plug) | "Ses donanıma nasıl ulaşır" |
| K010-network/evrensel-dil | Ağ (SSE, backoff, ETag) | "Ses nasıl taşınır" |
| Bu dosya (K004) | Algoritma (biquad, LUFS, THD+N) | "Ses nasıl işlenir" |

### Ölçüm birimleri hızlı tablosu

| Birim | Ne ölçer | Referans değer |
|---|---|---|
| dBFS | Örnekleme alanı genlik | 0 dBFS = tavan |
| dBTP | Örnekleme-arası gerçek tepe | yayın ≤ -1 dBTP |
| LUFS | K-weighting loudness | EBU R128: -23.0 |
| % (THD+N) | Bozulma + gürültü oranı | kapı < 0.01 |
| ms | Zaman (attack/release/latency) | KA-A6: 5-20 ms bütçe |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
