---
node_id: K006.5-music-analysis-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/AI/AIEngine.php L82–106 · L288–330 (disk okuma 2026-10-12)"
  - "shared/src/AI/AIWorkflow.php L99–139 (katalog kaydı adımı L118–127)"
validation: "2026-10-12 · 9 benzersiz URL havuzdan, iddialar sayfa metniyle · status=verified"
open_questions:
  - "Katalog analizinin batch (toplu) mi, istek-anında mı çalışacağı → UNKNOWN"
  - "Pedalboard GPL v3 lisansı K006 lisans politikasıyla uyumlu mu → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K006.5-music-analysis — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **müzik analizi**
> ayağı. Tüm URL'ler 2026-10-12 doğrulanmıştır; yeni URL üretilmedi — yalnız havuz §2.
> K006.5 ile K006.6 (audio-intelligence) arasındaki ayrım v2'de tanımlı değildir (⚠️) —
> burada K006.5 = **katalog tarafı** (parça başına öznitelik + kalıcı kayıt) ele alınır.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Katalog öznitelikleri (BPM/key/energy…) hangi kütüphanelerle çıkarılır? | `extractFeatures` placeholder — katalog verisinin kaynağı bugün sahte | §2.4 (12–14) |
| Müzik analizi + hazır etiket modelleri? | tür/ruh hali etiketleme adayı | §2.4 (15–16) |
| Endüstri standardı ses araçları? | ekosistem doğrulaması | §2.4 (17–18) |
| Ses içerik tanıma gerekir mi? | otomatik içerik eşleştirme | §2.1 (6) |
| Etiket eşikleri nasıl kurulur? | sınıflandırma eşiği | §2.3 (11) |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.5 madde |
|---|---|---|---|
| 1 | https://librosa.org/ | librosa — Python ses analizi kütüphanesi ana sayfası | öznitelik çıkarım adayı |
| 2 | https://librosa.org/doc/latest/ | librosa dokümantasyonu — sürüm **1.0.0** | sürüm kanıtı |
| 3 | https://github.com/librosa/librosa | librosa GitHub — **8.664★**, lisans **ISC** (2026-10-12) | lisans/yıldız |
| 4 | https://essentia.upf.edu/ | Essentia — müzik analizi ve ses parmak izi kütüphanesi (MTG/UPF) | öznitelik + fingerprint adayı |
| 5 | https://essentia.upf.edu/models.html | Essentia **hazır modeller** sayfası | hazır etiket modeli |
| 6 | https://spotify.github.io/ | Spotify açık kaynak yayın sayfası (ses araçları) | ekosistem |
| 7 | https://github.com/spotify/pedalboard | Pedalboard — **6.346★**, **GPL v3** | ses ön-işleme adayı |
| 8 | https://github.com/openai/whisper | Whisper — **110.308★**, **MIT** | söz-içerik analizi (opsiyonel) |
| 9 | https://scikit-learn.org/stable/ | scikit-learn — metrik/ölçekleme/veri ayrımı; sürüm **1.9.0** | etiket eşikleri |

**Toplam K006.5 için: 9 benzersiz URL** (havuz §2.4 · §2.1 · §2.3).

---

## §3 İddia Ayrıntıları (sayfa metni dayanaklı)

| İddia | Dayanak URL | Ne söylüyor | K006.5'e etkisi |
|---|---|---|---|
| BPM/key/enerji çıkarımı kütüphane işidir | 1–3 | librosa sürüm 1.0.0 · ISC · 8.664★ | birincil katalog kaynağı adayı |
| Müzik analizi + parmak izi birlikte gelir | 4 | Essentia MTG/UPF — analiz + fingerprint | tekrar tespiti için de aday |
| Hazır etiket modelleri yayımlanır | 5 | hazır modeller sayfası | sıfır eğitimle etiketleme |
| Endüstri referansı Spotify'dır | 6–7 | Spotify OSS + Pedalboard (GPL v3) | doğrulama + lisans uyarısı |
| Söz-içerik analizi mümkündür | 8 | Whisper MIT | söz metni → katalog metni |
| Sınıf eşikleri sklearn ile kurulur | 9 | sınıflandırma yardımcıları | etiket güven eşiği |

---

## §4 K006.5'e Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.5 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| librosa 1.0.0 | BPM/key/energy çıkarımı (katalog) | 1–3 | **kurulmadı** |
| Essentia + hazır modeller | tür/ruh hali etiketleme + fingerprint | 4–5 | **kurulmadı** |
| Pedalboard | ön-işleme zinciri | 6–7 | **kurulmadı** · lisans ⚠️ GPL v3 |
| Whisper | söz-içerik → katalog metni | 8 | **kurulmadı** |
| scikit-learn 1.9.0 | etiket eşikleri | 9 | **kurulmadı** |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` · `status: draft`).

---

## §5 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| librosa BPM/key çıkarır | `extractFeatures` L315–330 **sabit değer** döner (`bpm=120.0`, `key=C`) | ⚠️ **eşleşme yok** |
| Essentia etiket/fingerprint üretir | fingerprint/etiket kodu **0 satır** (grep → 0, 2026-10-12) | ⚠️ boşluk |
| Katalog analizi kalıcıdır | `audioAnalysisWorkflow` L118–127 → `callTool('db-query', …)` + `UPDATE tracks SET bpm=…` (`:bpm` placeholder + `params`) — **VAR** | ✅ eşleşme (katalog kaydı deseni hazır) |
| Metadata (süre/format) | `extractMetadata` L288–313 (ffprobe) — **VAR** | ✅ eşleşme |
| Analiz kapısı | `analyzeAudio` L82–106 → 10 alan döner | ✅ eşleşme |
| Söz içerik analizi | grep `whisper|transcri` → **0** | ⚠️ boşluk |
| Dil sınırı | librosa/Essentia Python; K006 PHP | 🔶 UNKNOWN |

**Sonuç:** K006.5'in **kayıt tarafı hazırdır** (workflow → `db-query` → `tracks`), ama
**öznitelik kaynağı sahtedir** (placeholder sabitler). Katalog analizinin tüm "zekâ" katmanı
dış kütüphanelere bağlıdır ve hiçbiri kurulu değildir. Bu, K006.5'in ana bulgusudur.

---

## §6 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | `mtg.github.io/essentia` — CRAWL_NOT_FOUND → `essentia.upf.edu` düzeltildi | ✅ düzeltildi (havuz §6.2) |
| C2 | Katalogda `genre` daima `''` (workflow yorumu L112) — tür sorgusu boş | ⚠️ **açık boşluk** |
| C3 | BPM=120.0/key=C placeholder'ı katalogda gerçek veri gibi durur | ⚠️ veri bütünlüğü riski (R-02/R-05) |
| C4 | Pedalboard GPL v3 — lisans denetimi bekliyor | ⚠️ lisans |
| C5 | Ayrım (K006.5 vs K006.6) v2'de tanımlı değil | ⚠️ VERIFICATION REQUIRED |

---

## §7 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **9** |
| ✅ sayfa metniyle doğrulandı | 9 |
| Repo ile çapraz eşleşme | 3 (katalog kaydı · metadata · analiz kapısı) |
| ⚠️ boşluk/çelişki | 5 (C2–C5 + placeholder) |
| UNKNOWN | 2 (dil sınırı · batch/istek-anı kararı) |
| Kurulu aday | 0 |

---

## §8 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.1 · §2.3 · §2.4 — 2026-10-12.
2. `shared/src/AI/AIEngine.php` L82–106 · L288–330 — 2026-10-12 disk okuma.
3. `shared/src/AI/AIWorkflow.php` L99–139 — 2026-10-12 disk okuma.
4. `shared/src/**` grep (`librosa|essentia|pedalboard|fingerprint|whisper`) → 0 eşleşme, 2026-10-12.
5. `shared/composer.json` — müzik analiz paketi yok, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 9/9 URL havuzdan doğrulandı, 3 çapraz eşleşme, 5 işaretli boşluk, 0 kurulu aday`
