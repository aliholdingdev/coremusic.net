---
title: "audio-intelligence — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/audio-intelligence
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# audio-intelligence — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`ai-master.md` §1, v2 §2.6 rol cümlesini birebir taşır: **"Şarkı öneren, sesi analiz eden yapay zeka parçaları."**
`ai-master.md` §1 sorumluluk tablosu (satır 37–42) bu adı **"Sesi ve metriği analiz etmek"** sorumluluğuna bağlar —
ama master bu tabloyu açıkça *tasarım yorumu* olarak işaretler; **resmi çocuk kapsamı v2'de yoktur.**

Basitçe: ses dosyasından sayı üreten taraf — tempo (BPM), ton (key), enerji, mood.
`audio-intelligence` ile `music-analysis` arasındaki ayrım v2'de **tanımlı değildir**; bu kayıt ikisini birleştirmez,
ayrımı §3/R1'e sorar.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Ses dosyasından öznitelik (feature) çıkarımının planı | Özniteliklerin veritabanında saklanması | K007-data (bagimliliklar §2/1) |
| 2 | Ses zekâsı modelinin K006 kapsamında çalıştırılması | DSP zinciri ve sinyal üretimi | K004-dsp (ai-master §2.1) |
| 3 | Çıktının (BPM/key/energy/mood) öneri akışına verilmesi | Öneri sıralamasının kendisi | recommendation (ai-master §3) |
| 4 | Ses zekâsı için K006 ↔ K016 tek ML çağrı noktası | Ajan çalışma zamanı, prompt yönetimi | K016-AI (ai-master §2.1) |
| 5 | Metrik **sözleşmesi** (alan adları, tipler) | "çal/beğen" iş kuralının yorumu | K008-domain (ai-master §2.1) |
| 6 | Model/format seçimi için kanıt toplama | Firmware seviyesinde ses işleme | K005-firmware (ai-master §2.1) |

**Sınır kuralı (v2 §2.6 · registry §6):** bu dal yalnız **K006 = ML ürün tarafını** kapsar; K016-AI ajan altyapısı
bu kapsama **taşınamaz**.

**Yasak:** yukarıdaki tablo resmi sınır değil, **kademe-2 taslak sınır önerisidir** — v2 §2.6 bu çocuğa rol vermez;
kilit ADR-097 K4 (kademe-3)tedir.

### Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | audio-intelligence ↔ music-analysis (ikisi de "ses analizi") | **UNKNOWN** — ayrım v2'de yok | ai-master §3 satır 263–264 |
| A2 | Feature çıkarma katmanı: PHP mi, C++ DSP mi | ⚠️ taslak — repo kodu C++'a işaret eder | `AIEngine.php` satır 317 yorumu |
| A3 | Ses zekâsı ↔ K004-dsp sinyal yolu | ⚠️ üst karar bekliyor | ai-master §2.1 |
| A4 | Metrik sözleşmesinin sahibi (K006 mı, K008 mi) | ⚠️ VERIFY REQUIRED | bagimliliklar §2/2 |

## §3 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk adı v2 §2.6 satır 264'te birebir geçer | `.ai/architecture/K006-yapay-zeka/audio-intelligence/index.md` (evidence) | ✅ dosya okundu |
| 2 | Rol tanımı yok → `⚠️ VERIFICATION REQUIRED` | `audio-intelligence/index.md` (open_questions satır 13) | ✅ dosya okundu |
| 3 | Motor 5 modülden oluşur, 2'si ses tarafı (RecommendationEngine · AudioAnalyzer) | `shared/src/AI/AIEngine.php` satır 21–23 | ✅ dosya okundu |
| 4 | `analyzeAudio()` BPM/key/energy/mood döndürür | `shared/src/Contracts/AI/AIEngineInterface.php` satır 30–36 | ✅ dosya okundu |
| 5 | Özellik çıkarma henüz **placeholder** (bpm=120.0, key=C sabit) | `shared/src/AI/AIEngine.php` satır 315–327 | ✅ dosya okundu |
| 6 | "Gerçek implementasyonda C++ DSP engine ile yapılacak" | `shared/src/AI/AIEngine.php` satır 317 | ✅ dosya okundu |
| 7 | Ses analizi workflow'u sonuçları `tracks` tablosuna prepared statement ile yazar | `shared/src/AI/AIWorkflow.php` satır 117–127 | ✅ dosya okundu |
| 8 | Metadata çıkarma ffprobe ile yapılır | `shared/src/AI/AIEngine.php` satır 291 | ✅ dosya okundu |
| 9 | K006'nın resmi model/kütüphane seçimi | `ai-master.md` §4 | **UNKNOWN** |

### Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | audio-intelligence ↔ music-analysis ayrımı hangi ölçütlerle? | ⚠️ VERIFICATION REQUIRED |
| R2 | Feature çıkarma hangi katmanda (PHP / C++ DSP / Python)? | ⚠️ ai-master §7/4 — **UNKNOWN** |
| R3 | Hangi ses analizi kütüphanesi/modeli seçilecek? (adaylar da doğrulanmadı) | ⚠️ **UNKNOWN** — seçim yok |
| R4 | Ses zekâsı ↔ K004-dsp sınırı ne zaman kilitlenecek? | ⚠️ ADR-097 K4 |

**Kapanış:** status: draft kalır. R1–R3 kapanmadan bu dosyada **teknoloji ya da resmi sınır iddiası** yazılmaz
(ai-master §4.1 · yapay-zeka-dogrulama §5).

## §4 İlişkili dosyalar

- [[audio-intelligence-dogrulama]]
- [[audio-intelligence-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
