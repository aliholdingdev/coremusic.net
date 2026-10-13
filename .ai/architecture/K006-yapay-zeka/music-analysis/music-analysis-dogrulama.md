---
title: "music-analysis — Doğrulama"
type: architecture-verification
node_id: K006-yapay-zeka/music-analysis
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# music-analysis — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa ile bulunan **resmi URL'ler** · (b) bu oturumda okunan **gerçek disk yolları**.
> Yasak: URL, sürüm, kütüphane uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk adı "music-analysis" v2 §2.6 satır 263'te birebir geçer | `.ai/architecture/K006-yapay-zeka/music-analysis/index.md` (evidence) | 2026-10-12 | ✅ dosya okundu |
| 2 | Rol tanımı v2'de yok → `⚠️ VERIFICATION REQUIRED` | `music-analysis/index.md` (open_questions) | 2026-10-12 | ✅ dosya okundu |
| 3 | `analyzeAudio()` BPM/key/energy/mood sözleşmesi | `shared/src/Contracts/AI/AIEngineInterface.php` satır 31–36 | 2026-10-12 | ✅ dosya okundu |
| 4 | Metadata çıkarma ffprobe komut satırı ile yapılır | `shared/src/AI/AIEngine.php` satır 288–310 | 2026-10-12 | ✅ dosya okundu |
| 5 | Feature çıkarma placeholder — gerçek analiz yok | `shared/src/AI/AIEngine.php` satır 315–327 | 2026-10-12 | ✅ dosya okundu |
| 6 | ffprobe `-show_format` / `-show_streams` / `-output_format json` resmi seçenektir | https://www.ffmpeg.org/ffprobe.html | 2026-10-12 | ✅ doğrulandı |
| 7 | librosa `librosa.feature.tempo` ile BPM tahmini resmi API'dedir | https://librosa.org/doc/latest/api/feature.html | 2026-10-12 | ✅ doğrulandı |
| 8 | librosa ritim analizi (onset → tempo → beat) resmi tutorial'da anlatılır | https://librosa.org/doc/main/auto_tutorials/01-intro/06-rhythm.html | 2026-10-12 | ✅ doğrulandı |
| 9 | Essentia spektral/tonal/yüksek seviye müzik tanımlayıcıları içerir | https://essentia.upf.edu/ | 2026-10-12 | ✅ doğrulandı |
| 10 | CoreMusic'in librosa/Essentia/ffprobe-kütüphane seçimi | `ai-master.md` §4 | 2026-10-12 | **UNKNOWN** |
| 11 | music-analysis ↔ audio-intelligence sınırı | `ai-master.md` §3 satır 263–264 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 12 | `tracks` tablosuna yazma yetkisinin sahibi (K006 ↔ K007) | `shared/src/AI/AIWorkflow.php` satır 117–127 · bagimliliklar §4/2 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 13 | CoreMusic'in ffprobe kullandığı (repo kodunda görüldü) | `shared/src/AI/AIEngine.php` satır 291 | 2026-10-12 | ✅ dosya okundu |
| 14 | BPM/key çıkarımının doğruluk kabul kriteri (ground truth) | — | 2026-10-12 | **UNKNOWN** |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 14 |
| İşlenen benzersiz URL | 4 (6 · 7 · 8 · 9) |
| Gerçek dosya yolu satırı | 7 (1 · 2 · 3 · 4 · 5 · 12 · 13) |
| ✅ doğrulandı | 10 (1–10, 13) |
| ⚠️ VERIFICATION REQUIRED | 2 (11 · 12) |
| UNKNOWN | 2 (10 · 14) |

**Geçiş kuralı:** satır 11 ADR-097 K4 ile, 12 sahiplik kararıyla; satır 10 ve 14 teknoloji/kabul kriteri
kaydıyla (`yapay-zeka-dogrulama.md` §4) kapanır — kanıt gelene kadar UNKNOWN kalır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
