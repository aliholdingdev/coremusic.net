---
node_id: K003-audio/riskler
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on:
  - K005-firmware
  - K006-codebase
  - K007-sandbox
  - K008-superapp
  - K009-api
  - K010-network
  - K011-ui-kit
evidence:
  - ".ai/architecture/K003-audio/audio-master.md §8 (Q1-Q7 açık sorular) — okundu 2026-10-18"
  - ".ai/architecture/K003-audio/audio-master.md §29.2 (ASIO lisans kararı kapandı) — okundu 2026-10-18"
  - ".ai/architecture/K003-audio/audio-bagimliliklar.md §3 (ölü K001/K002 referansları) — okundu 2026-10-18"
  - "github.com/steinbergmedia/asiosdk-open (GPLv3, HTTP 200) — erişim 2026-10-18"
validation: "ASIO lisans riski 2026-10-14 §29.2 ile kapandı; sürüm teyidi ⚠️ kaldı"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — ASIO SDK sürümü/tag üretimde sabitlenmedi (KA-A2).
  ⚠️ VERIFICATION REQUIRED — audio-bagimliliklar.md'deki K001-infrastructure +
  K002-data ölü referansları düzeltme beyazliste dışı; silinmedi, ⚠️ işaretlendi.
group: kanit
---

# K003-audio — Riskler

## §1 — ELI15: Bu nedir?

"Bu iş nerede patlayabilir?" listesi. Her satır bir risk: olasılık (nasıl
sık?), etki (patlarsa ne olur?), azaltma (ne yapıyoruz?) ve saip (kim
çözüyor?). ASIO'nun lisans bulutu vardı — o risk 2026-10-14'te kapandı
(Steinberg deposu GPLv3'e açıldı), ama kartondaki sürüm teyidi duruyor.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | K003-audio kök riskleri: lisans, donanım bağımlılığı, latency, xrun, sıcak ekleme |
| Kapsam dışı | DSP algoritma riskleri (K004-dsp/riskler) · firmware riskleri (K005-firmware) |

## §3 — Risk matrisi (Olasılık × Etki × Azaltma × Sahip)

| # | Risk | Olasılık | Etki | Azaltma | Sahip | Kanıt |
|---|---|---|---|---|---|---|
| RA-1 | ASIO SDK lisansı kısıtlı (ticari kullanım) | **Kapandı** (2026-10-14) | — | Steinberg `asiosdk-open` GPLv3 (2025-10-15); §29.2 kararı | K003-audio | `audio-master.md` §29.2 · github.com/steinbergmedia/asiosdk-open (200, 2026-10-18) |
| RA-2 | ASIO SDK sürümü/tag üretimde sabitlenmedi | Yüksek | Orta | Tag kilidi + `composer`/submodule pin; üretim öncesi teyit | K003-audio | ⚠️ VERIFICATION REQUIRED (KA-A2) |
| RA-3 | Exclusive mode cihaz meşgul → açılamaz (DAW/Chrome kapmış) | Yüksek | Yüksek | KA-A5: shared fallback (bit-exact garanti yok, kullanıcıya bildir) | K003-audio | `audio-master.md` §3.4 Q5 · ADR-025 |
| RA-4 | Callback overrun (xrun/click) — allocate/lock/log callback'te | Orta | Yüksek | KA-A6: 5-20 ms bütçe, lock-free, ön-alloke; xrun sayacı K016'ya event | K003-audio | `audio-master.md` §3.4 Q7 · ADR-019 |
| RA-5 | Hot-plug: cihaz tak-çıkar → device-lost, stream ölür | Orta | Orta | C-dev-1: kalıcı ID eşlemesi + RECOVERING (3 deneme) → STOPPED | K003-audio | `audio-master.md` §3.4 |
| RA-6 | Bit-exact yolda beklenen resample (44.1→48) bozulma | Düşük | Yüksek | KA-A4: yalnız zorunlu zincirde dönüşüm; bit-exact yollar dönüşümsüz | K003-audio | `audio-master.md` §3.4 Q3 |
| RA-7 | Plugin crash (VST3/AAX/AU) ses yolunu öldürür | Orta | Yüksek | KA-A8: ayrı process sandbox (K007-sandbox); crash ≠ motor ölümü | K003-audio | `audio-master.md` §2 · `audio-bagimliliklar.md` §1 |
| RA-8 | 384 kHz/32f olmayan donanıma yanlış format dayatma | Düşük | Orta | KA-A3: 384k yalnız donanım bildirirse; capability sorgusu önce | K003-audio | `audio-master.md` §3.4 Q2 |
| RA-9 | MIDI 2.0 MIDI-CI uyumsuz eski cihaz | Orta | Düşük | KA-A7: MIDI 1.0 backward-compat köprü | K003-audio | `audio-master.md` §2 (K00302) |
| RA-10 | Bağımlılık dosyasındaki ölü K referansları (K001/K002) kafa karıştırır | Gerçekleşti | Düşük | ⚠️ işaretlendi; düzeltme beyazliste dışı — sonraki turda registry adıyla eşle | Vault Steward | `audio-bagimliliklar.md` §3 (K001-infrastructure · K002-data) |

## §4 — ASIO lisans riski: kapanış kaydı

| Tarih | Olay | Kaynak |
|---|---|---|
| 2026-10-14 | `audio-master.md` §29.2 — ASIO lisans riski **kapandı** (Steinberg açık kaynak geçişi) | okundu 2026-10-18 |
| 2025-10-15 | `steinbergmedia/asiosdk-open` — GPLv3 deposu (son commit etiketi doğrulanacak) | github.com/steinbergmedia/asiosdk-open · HTTP 200, 2026-10-18 |
| 2026-10-18 | `audiosdk/asio` (ASIO SDK 2.3 source) canlı | github.com/audiosdk/asio · HTTP 200, 2026-10-18 |

> **Kalan ⚠️:** üretimde hangi sürüm/tag sabitlenecek — KA-A2 kilitlenmedi.

## §5 — Açık sorular (audio-master §8 ile hizalı)

| # | Soru | Etkilediği risk | Durum |
|---|---|---|---|
| Q2 | Bit derinliği/örnekleme matrisi (384k?) | RA-8 | Karar verildi (KA-A3) ⚠️ donanım teyidi |
| Q3 | Bit-exact yolda resample politikası | RA-6 | Karar verildi (KA-A4) |
| Q5 | Exclusive/shared fallback | RA-3 | Kapandı (ADR-017 + KA-A5) |
| Q7 | Callback bütçesi | RA-4 | Karar verildi (KA-A6) |

## §6 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| 10 risk kaydı (RA-1..RA-10) | bu dosya §3 | ✓ 2026-10-18 |
| ASIO lisans kapandı | `audio-master.md` §29.2 + github 200 | ✓ 2026-10-18 |
| ASIO sürümü | — | ⚠️ VERIFICATION REQUIRED |
| Ölü K001/K002 referansları | `audio-bagimliliklar.md` §3 | ✓ Tespit, ⚠️ işaretli (silinmedi) |

## §7 — Risk kaydı şablonu (sonraki RA- kayıtları için)

```yaml
kod: RA-11                 # RA- önek + K003 sıra
risk: "tek cümlelik risk"
olasilik: yüksek | orta | düşük | kapandı
etki: yüksek | orta | düşük
azaltma: "somut, ölçülebilir önlem"
sahip: K003-audio | K004-dsp | ...
kanit: "<dosya> §<bölüm>" | "⚠️ VERIFICATION REQUIRED"
durum: açık | kapandı | izleniyor
```

### Azaltma türü sınıflandırması

| Tür | Açıklama | Bu dosyadaki örnek |
|---|---|---|
| Önleyici (prevent) | Risk hiç gerçekleşmesin | KA-A6 lock-free (RA-4) |
| Tespit (detect) | Gerçekleşirse hemen gör | xrun sayacı → K016 (RA-4) |
| Kurtarma (recover) | Gerçekleşse de toparlan | RECOVERING 3 deneme (RA-5) |
| Devir (transfer) | Başkasına aktar | K007 sandbox (RA-7) |

### Durum dağılımı (bu sürüm)

| Durum | Adet | Kayıtlar |
|---|---|---|
| Kapandı | 1 | RA-1 (ASIO lisans, 2026-10-14) |
| Açık (azaltılmış) | 8 | RA-2..RA-9 |
| Gerçekleşti (düşük etki) | 1 | RA-10 (ölü referans) ⚠️ |

### İzleme periyodu

| Risk sınıfı | Periyot | Kanal |
|---|---|---|
| Lisans/sürüm (RA-1/RA-2) | Sürüm değişiminde | Karar kaydı (audio-kararlar §4) |
| Runtime (RA-3/RA-4/RA-5) | Sürüm başına | K016 metrikleri ⚠️ (henüz yok) |
| Dokümantasyon (RA-10) | Vault senkronunda | Vault Steward |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
