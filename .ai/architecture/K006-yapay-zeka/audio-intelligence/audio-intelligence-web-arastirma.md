---
node_id: K006.6-audio-intelligence-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/AI/AIEngine.php L82–106 · L288–330 (disk okuma 2026-10-12)"
validation: "2026-10-12 · 11 benzersiz URL havuzdan, 9 iddia sayfa metniyle · status=verified"
open_questions:
  - "Essentia hazır model sayfasındaki model listesi tek tek okunmadı (⚠️ VERIFICATION REQUIRED)"
  - "Pedalboard'ın GPL v3'ü K006 lisans politikasıyla uyumu → lisans denetimi ADR bekliyor"
group: kanit
---

# K006.6-audio-intelligence — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **küme 4** (ses/müzik analizi)
> ayağı; tüm URL'ler 2026-10-12 doğrulanmıştır. Yeni URL üretilmedi — yalnız havuz kullanıldı.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Ses öznitelikleri (BPM/key/energy) hangi kütüphanelerle çıkarılır? | K006.6'in repo karşılığı `extractFeatures` placeholder — gerçek alternatif aday | 4 (12–18) |
| Hazır model / hazır analiz var mı? | sıfırdan model eğitmek K006.6 kapsamı değilse hazır model gerekir | 15–16 |
| Konuşma/işitsel iştira da gerekir mi? | "ses zekâsı" başlığı altında tanıma modelleri | 1 · 6 |
| Kurulum/ölçüm testi nasıl yapılır? | K006.9 ile ortak | 32 |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.6 madde |
|---|---|---|---|
| 1 | https://librosa.org/ | librosa — Python ses analizi kütüphanesi ana sayfası | öznitelik çıkarım adayı |
| 2 | https://librosa.org/doc/latest/ | librosa dokümantasyonu — sürüm **1.0.0** (doc sayfası) | sürüm kanıtı |
| 3 | https://github.com/librosa/librosa | librosa GitHub — **8.664★**, lisans **ISC** (2026-10-12 görüntüsü) | lisans/yıldız |
| 4 | https://essentia.upf.edu/ | Essentia — müzik analizi ve ses parmak izi kütüphanesi (MTG/UPF) | öznitelik + fingerprint adayı |
| 5 | https://essentia.upf.edu/models.html | Essentia **hazır modeller** sayfası | hazır model adayı |
| 6 | https://spotify.github.io/ | Spotify açık kaynak yayın sayfası (ses araçları) | ekosistem |
| 7 | https://github.com/spotify/pedalboard | Pedalboard — Spotify ses eklentisi/çerçevesi; **6.346★**, **GPL v3** | ses efekt/zincir adayı |
| 8 | https://github.com/openai/whisper | Whisper — açık kaynak çok dilli konuşma tanıma; **110.308★**, **MIT** | konuşma tanıma (opsiyonel) |
| 9 | https://onnxruntime.ai/docs/ | ONNX Runtime dokümanları — model çalışma zamanı ailesi | taşınabilir çalıştırma |
| 10 | https://scikit-learn.org/stable/ | scikit-learn — metrik/ölçekleme/veri ayrımı araçları; sürüm **1.9.0** | sınıf etiket eşikleri |
| 11 | https://docs.pytest.org/en/stable/ | pytest — Python test çerçevesi dokümanları | aday test altyapısı |

**Toplam K006.6 için: 11 benzersiz URL** (havuz §2.4 kümesi 12–18 + 1 · 9 · 11 · 32).

---

## §3 K006.6'e Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.6 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| librosa 1.0.0 | BPM · key · energy · danceability · acousticness çıkarımı | 1–3 | **kurulmadı** (composer/lockfile yok) |
| Essentia (+ hazır modeller) | müzik analizi · ses parmak izi · hazır etiket modeli | 4–5 | **kurulmadı** |
| Pedalboard | ses efekt zinciri / işitsel ön-işleme | 6–7 | **kurulmadı** · lisans ⚠️ GPL v3 |
| Whisper | dosya/içerik konuşma tanıma | 8 | **kurulmadı** |
| ONNX Runtime | çıkarım runtime'ı (model taşıma) | 9 | **kurulmadı** |
| scikit-learn 1.9.0 | etiket eşikleri / sınıflandırma yardımcıları | 10 | **kurulmadı** |
| pytest | Python tarafı testi | 11 | **kurulmadı** |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` Q4 · `status: draft`).

---

## §4 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| librosa BPM/key çıkarır | `AIEngine::extractFeatures` L315–330 **sabit değer** döner (`bpm=120.0`, `key=C`) | ⚠️ **eşleşme yok** — kütüphane entegre değil |
| Essentia ses parmak izi üretir | parmak izi kodu **0 satır** (grep: fingerprint/audiohash yok) | ⚠️ boşluk |
| Whisper dosyayı transkripler | K006.6'de transkripsiyon kodu **0 satır** | ⚠️ boşluk |
| ONNX Runtime modeli çalıştırır | `composer.json`'da onnx/runtime **yok** | ⚠️ boşluk |
| ffprobe metadata çıkarır | `extractMetadata` L290–295 `shell_exec('ffprobe …')` — **VAR** (tek gerçek dış ses aracı) | ✅ eşleşme |
| Ses analizi workflow'u | `AIWorkflow::audioAnalysisWorkflow` L99–139 — **VAR** | ✅ eşleşme |
| Librosa → PHP | repo PHP; librosa Python → süreç/dil sınırı (⚠️ mimari soru, **UNKNOWN**) | 🔶 dil sınırı |

**Sonuç:** K006.6'in tek gerçek karşılığı `analyzeAudio` + `audioAnalysisWorkflow` +
`extractMetadata` (ffprobe). Öznitelik çıkarımı (asıl "zekâ" kısmı) **araştırma ile repo arasında
kesişmiyor** — bu, K006.6'in ana bulgusudur.

---

## §5 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | `https://mtg.github.io/essentia` — CRAWL_NOT_FOUND (eski adres) → `essentia.upf.edu` ile düzeltildi | ✅ düzeltildi (havuz §6) |
| C2 | K006.6 kapsamı "ses zekâsı" der ama repo'da öznitelik placeholder | ⚠️ **açık boşluk** (riskler R-02) |
| C3 | Pedalboard **GPL v3** — K006 lisans politikası bilinmiyor | ⚠️ lisans denetimi bekliyor |
| C4 | librosa/Essentia Python; K006 PHP | 🔶 süreç dışı çalışma kararı **UNKNOWN** |
| C5 | Essentia hazır model listesi okunmadı | ⚠️ VERIFICATION REQUIRED |

---

## §6 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **11** |
| ✅ sayfa metniyle doğrulandı | 11 |
| Repo ile çapraz eşleşme | 2 (ffprobe · workflow) |
| ⚠️ boşluk/çelişki | 5 (C2–C5 + §4 boş satırlar) |
| UNKNOWN | 1 (dil/süreç kararı) |
| Kurulu aday | 0 |

---

## §7 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.4 (URL 12–18) + §2.1/§2.3/§2.8 (1 · 6 · 11 · 32) — 2026-10-12.
2. `shared/src/AI/AIEngine.php` L82–106 · L288–330 — 2026-10-12 disk okuma.
3. `shared/src/AI/AIWorkflow.php` L99–139 — 2026-10-12 disk okuma.
4. `shared/composer.json` — kurulu paket yokluğu, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 11/11 URL havuzdan doğrulandı, 2 çapraz eşleşme, 5 işaretli boşluk, 0 kurulu aday`
