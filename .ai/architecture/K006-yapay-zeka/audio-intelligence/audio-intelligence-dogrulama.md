---
title: "audio-intelligence — Doğrulama"
type: architecture-verification
node_id: K006-yapay-zeka/audio-intelligence
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# audio-intelligence — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa ile bulunan **resmi URL'ler** · (b) bu oturumda okunan **gerçek disk yolları**.
> Yasak: URL, sürüm, sınıf, kütüphane uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk adı "audio-intelligence" v2 §2.6 satır 264'te birebir geçer | `.ai/architecture/K006-yapay-zeka/audio-intelligence/index.md` (evidence) | 2026-10-12 | ✅ dosya okundu |
| 2 | Rol tanımı v2'de yok → `⚠️ VERIFICATION REQUIRED` | `audio-intelligence/index.md` (open_questions) | 2026-10-12 | ✅ dosya okundu |
| 3 | K006 = ML ürün yetenekleri · K016 = ajan altyapısı (kapsam sınırı) | `.ai/architecture/K006-yapay-zeka/ai-master.md` §2.2 | 2026-10-12 | ✅ dosya okundu |
| 4 | Repo'da ses analizi sözleşmesi: `analyzeAudio()` → BPM/key/energy/mood | `shared/src/Contracts/AI/AIEngineInterface.php` satır 30–36 | 2026-10-12 | ✅ dosya okundu |
| 5 | Özellik çıkarımı henüz placeholder (sabit değerler) | `shared/src/AI/AIEngine.php` satır 315–327 | 2026-10-12 | ✅ dosya okundu |
| 6 | Metadata çıkarma ffprobe komutu ile yapılır | `shared/src/AI/AIEngine.php` satır 291 | 2026-10-12 | ✅ dosya okundu |
| 7 | ffprobe `-show_format` / `-show_streams` ve `-output_format json` destekler | https://www.ffmpeg.org/ffprobe.html | 2026-10-12 | ✅ doğrulandı |
| 8 | librosa resmi feature extraction API'si (chroma · mfcc · tempo) yayınlar | https://librosa.org/doc/latest/api/feature.html | 2026-10-12 | ✅ doğrulandı |
| 9 | Essentia: ses analizi ve music information retrieval için C++ kütüphanesi (Python/JS binding) | https://essentia.upf.edu/documentation.html | 2026-10-12 | ✅ doğrulandı |
| 10 | librosa `beat_track` ile BPM tahmini resmi tutorial'da gösteriliyor | https://librosa.org/doc/latest/tutorial.html | 2026-10-12 | ✅ doğrulandı |
| 11 | CoreMusic'in librosa kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 12 | CoreMusic'in Essentia kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 13 | Feature çıkarma katmanı kararı (PHP ↔ C++ DSP) | `ai-master.md` §7/4 · `AIEngine.php` satır 317 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 14 | audio-intelligence ↔ music-analysis sınırı | `ai-master.md` §3 satır 263–264 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 15 | K006 model/runtime/kütüphane ekosistemi | `ai-master.md` §4 | 2026-10-12 | **UNKNOWN** |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 15 |
| İşlenen benzersiz URL | 4 (7 · 8 · 9 · 10) |
| Gerçek dosya yolu satırı | 6 (1 · 2 · 3 · 4 · 5 · 6) |
| ✅ doğrulandı | 10 (1–10) |
| ⚠️ VERIFICATION REQUIRED | 2 (13 · 14) |
| UNKNOWN | 3 (11 · 12 · 15) |

**Geçiş kuralı:** satır 13 katman kararıyla, satır 14 ADR-097 K4 ile; satır 11–12 ve 15 teknoloji seçim
kayıdıyla (`yapay-zeka-dogrulama.md` §4) kapanır — kanıt gelene kadar UNKNOWN kalır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
