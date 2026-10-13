---
title: "music-analysis — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/music-analysis
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# music-analysis — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`ai-master.md` §1, v2 §2.6 rol cümlesini birebir taşır: **"Şarkı öneren, sesi analiz eden yapay zeka parçaları."**
`ai-master.md` §1 sorumluluk tablosu (satır 37–42) bu adı **"Sesi ve metriği analiz etmek"** sorumluluğuna bağlar
(`music-analysis · audio-intelligence` birlikte) — tablo *tasarım yorumudur*.

Basitçe: şarkıya ait **müzik metriklerini** (BPM · ton/key · enerji · mood · danceability) çıkarma ve
akışa sunma tarafı. Repo'da karşılığı **vardır** (`AIEngine::analyzeAudio`) ama özellik çıkarımı
**placeholder**dır (sabit değerler) — gerçek analiz henüz IMPLEMENTED **değildir**.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Şarkı metriklerinin (BPM/key/energy/mood) çıkarım planı | Metriklerin veritabanında saklanması | K007-data (bagimliliklar §2/1) |
| 2 | Metrik sözleşmesi (alan adları · tipler) | Metriğin iş kuralı yorumu ("tempo 120 iyi") | K008-domain (bagimliliklar §2/2) |
| 3 | Dosya metadata (süre · sample rate · bit depth · kanal) okuması | Dosyanın DSP ile işlenmesi/sinyal üretimi | K004-dsp (ai-master §2.1) |
| 4 | Analiz çıktısının öneri akışına girdi olması | Öneri skorlaması | recommendation (ai-master §3) |
| 5 | Analiz modelinin K006 kapsamında çalıştırılması | Ajan çalışma zamanı | K016-AI (ai-master §2.1) |
| 6 | Analiz görevinin yürütülmesi için süreç sözleşmesi | Sürecin kendisi | K012-services (ai-master §2.1) |

**Sınır kuralı (v2 §2.6 · registry §6):** bu dal yalnız **K006 = ML ürün tarafını** kapsar; K016-AI ajan
altyapısına **taşınmaz**.

**Yasak:** tablo **kademe-2 taslak sınır önerisidir** — v2 §2.6 bu çocuğa rol vermez; kilit ADR-097 K4'tedir.
`music-analysis` ↔ `audio-intelligence` ayrımı tanımlı değildir (§3/R1).

### Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | music-analysis ↔ audio-intelligence | **UNKNOWN** — v2 ayrım vermez | ai-master §3 satır 263–264 |
| A2 | Metrik çıkarma kütüphanesi/modeli seçimi | **UNKNOWN** — repo'da placeholder | `AIEngine.php` satır 315–327 |
| A3 | Dosya metadata (ffprobe) K006'da mı, K007'de mi? | ⚠️ VERIFY REQUIRED | bagimliliklar §4/2 |
| A4 | Analiz çıktısının yazıldığı `tracks` tablosu sahibi | ⚠️ K007 ile sınır | `AIWorkflow.php` satır 117–127 |

## §3 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk adı v2 §2.6 satır 263'te birebir geçer | `.ai/architecture/K006-yapay-zeka/music-analysis/index.md` (evidence) | ✅ dosya okundu |
| 2 | Rol tanımı yok → `⚠️ VERIFICATION REQUIRED` | `music-analysis/index.md` (open_questions satır 13) | ✅ dosya okundu |
| 3 | `analyzeAudio()` imzası BPM/key/energy/mood/danceability/acousticness döndürür | `shared/src/Contracts/AI/AIEngineInterface.php` satır 31–36 | ✅ dosya okundu |
| 4 | Implementasyon metadata + feature adımlarından oluşur | `shared/src/AI/AIEngine.php` satır 82–106 | ✅ dosya okundu |
| 5 | Metadata ffprobe `-show_format -show_streams` ile çıkarılır | `shared/src/AI/AIEngine.php` satır 288–310 | ✅ dosya okundu |
| 6 | Feature çıkarma sabit değerler döndürür (placeholder) | `shared/src/AI/AIEngine.php` satır 315–327 | ✅ dosya okundu |
| 7 | Analiz workflow'u bpm/key/energy'yi `tracks` tablosuna prepared statement ile yazar | `shared/src/AI/AIWorkflow.php` satır 117–127 | ✅ dosya okundu |
| 8 | `analysis` görevi orchestrator'da `embedded` ajanına düşer (priority high) | `shared/src/AI/AIOrchestrator.php` satır 42 | ✅ dosya okundu |
| 9 | Seçilen müzik analizi kütüphanesi/modeli | `ai-master.md` §4 | **UNKNOWN** |

### Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | music-analysis ↔ audio-intelligence ayrımı hangi ölçütlerle? | ⚠️ VERIFICATION REQUIRED |
| R2 | BPM/key çıkarma hangi araçla (repo: placeholder)? | ⚠️ **UNKNOWN** |
| R3 | `tracks` tablosuna yazma yetkisi K006'da mı, K007'de mi? | ⚠️ bagimliliklar §4 |
| R4 | Model/format/runtime seçimi? | ⚠️ ai-master §4 — **UNKNOWN** |

**Kapanış:** status: draft kalır. R1–R2 kapanmadan bu dosyada **çalışan analiz** ya da araç seçimi
iddiası yazılmaz (ai-master §4.1 · yapay-zeka-dogrulama §5).

## §4 İlişkili dosyalar

- [[music-analysis-dogrulama]]
- [[music-analysis-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
