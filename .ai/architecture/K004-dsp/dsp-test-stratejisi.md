---
node_id: K004-dsp/test-stratejisi
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
  - ".ai/AGENTS.md §5 (SNR >100dB, THD+N <0.01%@1kHz) — boot 2026-10-18"
  - ".ai/architecture/K004-dsp/dsp-master.md §B (tier-3 planı, T8 bütçesi UNKNOWN) — okundu 2026-10-18"
  - ".ai/architecture/K004-dsp/dsp-riskler.md (R1-R10 risk matrisi) — okundu 2026-10-18"
  - "dsp-kod-ornekleri.md §3.7 (Python sweep/THD-N betiği, bu oturumda üretildi)"
validation: "Test kapıları AGENTS.md §5 eşikleri + EBU R128 ile hizalı; T8 bütçesi ⚠️ UNKNOWN"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — T8 saatlik bütçesi dsp-master'da UNKNOWN; kapatılmadan
  tier-3 planı revize edilecek. Ölçüm altyapısı (analizör/rig) repo'da yok.
group: kod
---

# K004-dsp — Test Stratejisi

## §1 — ELI15: Bu nedir?

DSP algoritmasının "sesi gerçekten doğru işliyor mu?" kanıtı. En güçlü test
**null test**: aynı sinyali işlenmiş/hal işlenmemiş olarak karşılaştır;
fark sıfırsa algoritma birebir (kaybısız) demektir. Sonra **sweep**
(frekans tarama) EQ'nun gerçekten frekansları düzelttiğini, **THD+N**
bozulmayı, **LUFS** ise insan kulağına göre ses seviyesini ölçer.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | Test piramidi, senaryolar (null/sweep/THD-N/LUFS), kabul kriterleri |
| Kapsam dışı | Backend donanım testleri (K003-audio test-stratejisi) · firmware test (K005) |

## §3 — Test piramidi

| Katman | Ne test edilir | Araç | Pay |
|---|---|---|---|
| Birim | Biquad katsayı doğrulama, kompresör envelope, parametre sınırları (§3.5/§3.12) | PHPUnit 12 · Vitest (TS) | %60 |
| Sinyal (entegrasyon) | Null test, sweep, THD+N, LUFS — offline WAV işleme | Python (numpy) betikleri ⚠️ | %30 |
| Donanım | Gerçek zamanlı zincir, K003 backend üzerinden round-trip | K00305 rig ⚠️ | %10 |

## §4 — Senaryolar

### 4.1 Birim

| # | Senaryo | Beklenen | Kanıt |
|---|---|---|---|
| T-D1 | Nyquist üstü frekans girişi | Reddedilir (Validate hata) | §3.5/§3.12 sözleşmesi |
| T-D2 | Q < 0.1 veya > 10 | Reddedilir | §3.5 |
| T-D3 | Biquad bypass ≠ null | Bypass'ta sinyal birebir geçer | §3.9 (DspChain.bypass) |
| T-D4 | Limiter: genlik > ceiling | Asla aşılmaz (yapısal garanti) | §3.4 |
| T-D5 | Preset migration v1→v2 | Açık hata veya dönüşüm; sessiz bozulma YOK | C-prof-1 |

### 4.2 Sinyal (çevrimdışı ölçüm)

| # | Senaryo | Beklenen | Araç |
|---|---|---|---|
| T-D6 | **Null test**: preset OFF vs ON (boş EQ) | Fark = tam sıfır (±1 LSB) | Python §3.7 |
| T-D7 | Log sweep 20 Hz-20 kHz, biquad peaking +6 dB @1 kHz | Frekans yanıtında 1 kHz tepesi ±0.1 dB | sweep betiği ⚠️ |
| T-D8 | THD+N @ 1 kHz sine | < %0.01 (zincir sonrası) | `.ai/AGENTS.md §5` · §3.7 |
| T-D9 | EBU R128 normalizasyon | Program -23 LUFS ±0.5; true peak ≤ -1 dBTP | §3.6 · §3.3 |
| T-D10 | Kompresör: -12 dBFS sine, threshold -18, ratio 4 | Çıkış -21 dBFS ±0.5 (grafik hesabı) | §3.2 |

### 4.3 Donanım (K003 backend zinciri)

| # | Senaryo | Beklenen | Araç |
|---|---|---|---|
| T-D11 | Zincir round-trip (EQ+comp+limiter) | SNR > 100 dB (zincir eklenince bozulmaz) | K003 rig ⚠️ |
| T-D12 | 20 dk gerçek zamanlı | xrun = 0; CPU bütçesi aşılmaz | K016 event |
| T-D13 | T8 bütçesi | ⚠️ UNKNOWN — kapatılacak | dsp-master §B |

## §5 — Kabul kriterleri

| Kriter | Eşik | Kapı |
|---|---|---|
| Null test farkı | ±1 LSB (bit-exact) | T-D6 — BLOCK |
| THD+N @1 kHz | < %0.01 | `.ai/AGENTS.md §5` — BLOCK |
| SNR (zincir sonrası) | > 100 dB | `.ai/AGENTS.md §5` — BLOCK |
| LUFS hedefi | -23.0 ±0.5 · true peak ≤ -1 dBTP | EBU R128 — BLOCK |
| Sweep düzlemi | ±0.1 dB | T-D7 — BLOCK |
| Birim kapsamı | K004 çekirdek %90+ | release gate |
| T8 saatlik bütçe | ⚠️ UNKNOWN (kapatılacak) | dsp-master §B |

## §6 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| SNR/THD+N eşikleri | `.ai/AGENTS.md §5` | ✓ Boot 2026-10-18 |
| R1-R10 riskleri testlere bağlandı | `dsp-riskler.md` | ✓ Okundu 2026-10-18 |
| Ölçüm betiği | `dsp-kod-ornekleri.md` §3.7 | ✓ Bu oturumda üretildi |
| T8 bütçesi | `dsp-master.md` | ⚠️ UNKNOWN — kapatılmadan tier-3 kapanmaz |
| Ölçüm altyapısı (analizör/rig) | repo'da yok | ⚠️ VERIFICATION REQUIRED |

## §7 — Açık sorular

| # | Soru | Sahip |
|---|---|---|
| 1 | T8 saatlik bütçe ne? (dsp-master UNKNOWN) | K004-dsp |
| 2 | Null/sweep betikleri CI'ye mi girer (Python+numpy bağımlılığı)? | K007-sandbox |
| 3 | Ölçüm WAV referans dosyaları nerede saklanacak (repo mu, vault mu)? | K006-codebase |

## §8 — Ölçüm raporu şablonu

Her sinyal testi (T-D6..T-D10) sonrası bu şablon doldurulur:

```yaml
test_id: T-D8
preset: "<preset adı + schema_version>"
sample_rate: 48000
signal: "1 kHz sine, -20 dBFS"
olcumler:
  thd_n_pct: 0.0     # hedef < 0.01
  null_diff_lsb: 0   # T-D6: hedef ±1
  lufs: 0.0          # T-D9: hedef -23.0 ±0.5
sonuc: gecti | gecmedi
tarih: YYYY-MM-DD
not: "⚠️ araç sürümü teyitsizse yaz"
```

### Kapı karar matrisi

| Ölçüm | Eşik | Aşarsa/ne olur |
|---|---|---|
| Null test farkı | ±1 LSB | BLOCK — birebirlik bozulmuş |
| THD+N | < %0.01 | BLOCK — bozulma kapısı |
| SNR | > 100 dB | BLOCK |
| LUFS | -23.0 ±0.5 | BLOCK — R128 ihlali |
| Sweep düzlemi | ±0.1 dB | BLOCK — EQ yanıtı hatalı |

### Bağımlılık notu (CI)

| Bağımlılık | Kullanım | Durum |
|---|---|---|
| Python 3 + numpy | sweep/THD-N/LUFS betikleri (§3.7) | ⚠️ repo'da yok |
| WAV referansları | null test + sweep girdisi | ⚠️ üretim öncesi üretilecek |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
