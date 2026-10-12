---
node_id: K004-dsp
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - pipeline
  - equalizer
  - filters
  - dynamics
  - compressor
  - limiter
  - reverb
  - delay
  - crossover
  - spatial-audio
  - measurement
  - testing
depends_on:
  - K003-audio
  - K000-isletim-sistemi
evidence:
  - "v2 §2.4 mini-ağaç — 12 çocuk adı (2026-10-12 okundu)"
  - "dsp-master.md §4 — web kanıtları (Patina, Signalsmith basics/dsp, DSPark ⚠️)"
validation: "draft — A1 tur-1; D-kriterleri dsp-dogrulama.md §2"
open_questions:
  - "DSPark v1 iddiası (90+) ile ölçüm (2★) çelişiyor; depo eşleşmedi (⚠️ VERIFY REQUIRED)"
---

# K004-dsp — DSP Alanı

## §1 ELI15

**Sesin şekillendirildiği yer — bass, tiz, ekolayzır, oda hissi.**

Bir şarkının 'daha sıcak' ya da 'daha net' duymak istediğiniz hali burada
üretilir: ses önce ölçülür, sonra bantlar ayrılır, seviyeler tırpılanır,
gerçek odayı taklit eden yankı eklenir. ELI15 ile: K003 sesi **taşır**,
K004 ise taşıdığı suyun **tadını değiştirir** — müzik stüdyosundaki miksaj
masası gibi, hoparlörün kendisi değil.

Ne DEĞİL: ham sesin taşınması ve cihaza bağlanması (→ K003-audio) ·
amplifikatör/DAC devresinin tasarımı (→ K001-donanım) · cihaz içindeki
kontrol yazılımı (→ K005-firmware).

## §2 Kademe-2 çocuk katalogu (v2 §2.4 — 12 çocuk)

| # | Çocuk | Rol (1 satır) | Kanıt durumu |
|---|---|---|---|
| 1 | pipeline | İşlem basamaklarının sırası ve akış grafiği | ⚠️ adından türetildi |
| 2 | equalizer | Frekans bantlarına göre seviye ayarı (bass/tiz) | ⚠️ adından türetildi |
| 3 | filters | Alçak/yüksek/geçiren bant filtreleri | ⚠️ adından türetildi |
| 4 | dynamics | Seviye davranışının tümü (sıkıştırma ailesi) | ⚠️ adından türetildi |
| 5 | compressor | Sesin yüksek kısmını tırpayan dinamik işlem | ⚠️ adından türetildi |
| 6 | limiter | Aşırı seviyeyi kesen güvenlik katmanı | ⚠️ adından türetildi |
| 7 | reverb | Oda hissi — yansıma ve yankı taklidi | ⚠️ adından türetildi |
| 8 | delay | Belirli gecikmeli kopya ekleme | ⚠️ adından türetildi |
| 9 | crossover | Sesi frekansa göre hoparlör türlerine ayırma | ⚠️ adından türetildi |
| 10 | spatial-audio | Konum/hissiyat — saran ses katmanı | ⚠️ adından türetildi |
| 11 | measurement | Frekans/zaman tepkisi ölçümü ve raporu | ⚠️ adından türetildi |
| 12 | testing | DSP testleri ve bozulma (distortion) kontrolü | ⚠️ adından türetildi |

> ⚠️ VERIFICATION REQUIRED: v2 §2.4 çocukları yalnızca ad olarak tanımlar;
> roller adından türetilmiştir (v2 §3 KURAL 3) — uydurma parametre/API yazılmaz.

## §3 Bağımlılık özeti (tasarım iddiası)

| Yön | Bağımlılık | Etiket |
|---|---|---|
| K004 → K003-audio | DSP, motorun akışına eklenir; bağımsız kaynak yoktur | [TASARIM] |
| K004 → K000-isletim-sistemi | CPU/Zaman kaynağı ve thread zemini | [TASARIM] |
| K004 → K001-donanım | Çıktı nihayet amplifikatöre/hoparlöre iner | [TASARIM] |
| K005-firmware | Bazı DSP hedefleri donanım içinde çalışabilir | [TASARIM] — ⚠️ kapsam belirsiz |

## §4 Doğrulama durumu

| Kriter | Durum |
|---|---|
| D1 v2 §2.4 birebirliği (12 çocuk) | ✅ 2026-10-12 |
| D2 12 çocuk klasöründe index.md | ✅ 2026-10-12 |
| D3 master + bagimliliklar + dogrulama üçlüsü | ✅ 2026-10-12 |
| D4 açık ⚠️ / UNKNOWN kalmadı | ❌ DSPark iddiası ⚠️ + doğrulanamayanlar |
| D5 açık sorular kapandı | ❌ 5 açık |
| D6 en az bir [TASARIM] → [KANIT] geçişi | ❌ 0 |
| D7 status: verified | ❌ D1–D7 tamamlanınca |

**Status: draft** — verified yalnız D1–D7 tamamı ✅ olduğunda yazılır.

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
