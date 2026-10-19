---
title: "recommendation — Doğrulama"
type: architecture-verification
node_id: K006-yapay-zeka/recommendation
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# recommendation — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa ile bulunan **resmi URL'ler** · (b) bu oturumda okunan **gerçek disk yolları**.
> Yasak: URL, sürüm, algoritma, bibliyotek uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk adı "recommendation" v2 §2.6 satır 261'de birebir geçer | `.ai/architecture/K006-yapay-zeka/recommendation/index.md` (evidence) | 2026-10-12 | ✅ dosya okundu |
| 2 | Rol tanımı v2'de yok → `⚠️ VERIFICATION REQUIRED` | `recommendation/index.md` (open_questions) | 2026-10-12 | ✅ dosya okundu |
| 3 | Repo'da hybrid öneri akışı 6 adımda uygulanır | `shared/src/AI/AIEngine.php` satır 49–77 | 2026-10-12 | ✅ dosya okundu |
| 4 | Hybrid ağırlık 0.6 collaborative / 0.4 content-based | `shared/src/AI/AIEngine.php` satır 65 | 2026-10-12 | ✅ dosya okundu |
| 5 | Collaborative tarafı henüz boş — ALS yorumu var, kod yok | `shared/src/AI/AIEngine.php` satır 216–217 | 2026-10-12 | ✅ dosya okundu |
| 6 | Öneri sahipliği K006 · K008 · K020 arasında açık | `.ai/architecture/K006-yapay-zeka/ai-master.md` §7/2 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 7 | Hybrid öneri mimarisi "collaborative-filtering veya content-based" kullanımı sağlar (LibRecommender) | https://librecommender.readthedocs.io/en/stable/ | 2026-10-12 | ✅ doğrulandı |
| 8 | Microsoft Recommenders, ALS/SAR/LightGCN gibi CF algoritmalarını ve offline metrikleri içerir | https://github.com/recommenders-team/recommenders | 2026-10-12 | ✅ doğrulandı |
| 9 | Collaborative filtering (CF) tanımı ve kategorileri (memory-based / model-based) resmi ders kaynağındadır | http://www.d2l.ai/chapter_recommender-systems/recsys-intro.html | 2026-10-12 | ✅ doğrulandı |
| 10 | Amazon Personalize, öneri skorlarını 0–1 aralığında **göreli** üretir | https://docs.aws.amazon.com/personalize/latest/dg/recommendations.html | 2026-10-12 | ✅ doğrulandı |
| 11 | CoreMusic'in hangi CF algoritmasını (ALS vb. kullanacağı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 12 | CoreMusic'in LibRecommender / Recommenders / Personalize'i kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 13 | Skor ağırlıkları 0.6/0.4'ün tasarım kararı olduğu | `shared/src/AI/AIEngine.php` satır 65 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 14 | K006 teknoloji ekosistemi (model formatı / runtime) | `ai-master.md` §4 | 2026-10-12 | **UNKNOWN** |
| 15 | 9 çocuğun kademe-3 (bounded-context) yapısı | ADR-097 K4 | 2026-10-12 | ⚠️ BEKLİYOR |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 15 |
| İşlenen benzersiz URL | 4 (7 · 8 · 9 · 10) |
| Gerçek dosya yolu satırı | 7 (1 · 2 · 3 · 4 · 5 · 6 · 13) |
| ✅ doğrulandı | 9 (1–5 · 7–10) |
| ⚠️ VERIFICATION REQUIRED | 3 (6 · 13 · 15) |
| UNKNOWN | 3 (11 · 12 · 14) |

**Geçiş kuralı:** satır 6 sahiplik ADR'siyle, 13 tasarım onayıyla, 15 ADR-097 K4 ile; satır 11–12 ve 14
teknoloji seçim kaydıyla (`yapay-zeka-dogrulama.md` §4) kapanır — kanıt gelene kadar UNKNOWN kalır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

---

# §3–§13 Ek — step-2 turu (2026-10-15 · append-only)

> §1–§2 ve §Authority (yukarıda) **aynen korunmuştur**; bu bölüm yalnızca altına eklenmiştir.
> **Ölçüm usulü:** satır sayısı `[System.IO.File]::ReadAllLines($yol).Length` ile alınır
> (`Measure-Object -Line` kullanılmaz).
> **Kanıt biçimi:** `Dosya: <gerçek yol> [Satır: n]`. Erişilemeyen iddia `⚠️ VERIFICATION REQUIRED`.
> **Repo C++ durumu (2026-10-15, bu oturum ölçümü):** `**/*.cpp` → **0 dosya** · `**/*.h` → **0 dosya**.
> "Repo'da C++ kodu vardır" iddiası **YASAK**tır; Neva Engine belgelerindeki C++ görünümlü bloklar
> **vault spesifikasyon metnidir**, kaynak kod değildir.

## §3 Taze disk okuması — Neva Engine vault (`.ai/projects/NevaEngine/`, 2026-10-15)

| # | İddia (dosyanın kendi metninden) | Dosya | Satır | Durum |
|---|---|---|---|---|
| N1 | EQ presets dosyası §1 "Genre-Based EQ Presets (Rule-Based)" başlığıyla açılır | `.ai/projects/NevaEngine/legacy-2026-07/eq-presets.md` | 12 | ✅ okundu |
| N2 | §1 tablosu 16 tür satırı taşır (Arabesk → Ambient) | `.ai/projects/NevaEngine/legacy-2026-07/eq-presets.md` | 16–33 | ✅ okundu |
| N3 | §2 "Special EQ Presets" 9 hazır preset içerir (Flat · Smiley Face · Vocal Boost · Bass Boost · Treble Boost · Mid Scoop · Loudness · Night Mode · Podcast/Voice) | `.ai/projects/NevaEngine/legacy-2026-07/eq-presets.md` | 35–49 | ✅ okundu |
| N4 | §3 "User EQ Presets" — kullanıcının kendi preset'ini kaydettiği JSON şeması vardır (preset_id · name · created_by · eq_type · bands · fx_chain · is_public · tags) | `.ai/projects/NevaEngine/legacy-2026-07/eq-presets.md` | 51–74 | ✅ okundu |
| N5 | §4 "FX Sound Reference Presets" 6 satır taşır (General · Music · Movies · Gaming · Voice · Transcription) | `.ai/projects/NevaEngine/legacy-2026-07/eq-presets.md` | 76–90 | ✅ okundu |
| N6 | Dosyanın güncelleme tarihi 2026-06-03'tür | `.ai/projects/NevaEngine/legacy-2026-07/eq-presets.md` | 4 | ✅ okundu |
| N7 | 5 kullanıcı modu tanımlıdır: Ev, Araba, TV, Stüdyo, Gamer | `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` | 10 | ✅ okundu |
| N8 | Mod tablosu 5 satırdır; her satırda varsayılan EQ tipi · UI stili · hedef kullanıcı vardır | `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` | 18–24 | ✅ okundu |
| N9 | Kayıt akışında 3 soru sorulur (kullanım yeri · EQ detay seviyesi · müzik türü tercihi) | `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` | 28–47 | ✅ okundu |
| N10 | Müzik türü tercih sorusu "AI için" olarak **opsiyonel** işaretlenmiştir | `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` | 43–44 | ✅ okundu |
| N11 | Mod değişiminde 4 şey değişir: EQ band/tip, UI teması, preset listesi, FX zinciri | `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` | 168–179 | ✅ okundu |
| N12 | Ayarlardan band sayısı seçimi 6 seçenek sunar (2 · 5 · 10 · 31 · 6-cell parametric · custom) | `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` | 183–200 | ✅ okundu |
| N13 | AI Auto-EQ "3-tier hybrid: Genre Detection → ML Fine-Tuning → Environmental Calibration" olarak tanımlanır | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 14 | ✅ okundu |
| N14 | Tier-1 FFT tabanlı tespiti 5 sinyal kullanır: spectral centroid · bass ratio · mid ratio · high ratio · BPM · rhythm regularity | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 18–62 | ✅ okundu |
| N15 | §2 tür-bazlı EQ tablosu 14 satır taşır (Arabesk → Voice/Podcast; Gamer FPS/RPG ve Film satırları dahil) | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 64–83 | ✅ okundu |
| N16 | §3 kullanıcı tercih modelinde `listening_history` kaydı `song_id · genre · eq_profile · skip` alanları taşır | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 87–96 | ✅ okundu |
| N17 | §3 `skip_patterns` iki alan içerir: `too_bright` ve `too_muddy` | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 103–106 | ✅ okundu |
| N18 | Online Learning 4 sinyal tanımlar: skip tespiti · tam dinleme · volume değişikliği · manuel EQ override | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 113–117 | ✅ okundu |
| N19 | Öğrenme kuralı: "Incremental gradient descent with forgetting factor (α = 0.95)" | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 119 | ✅ okundu |
| N20 | §5 workflow 8 adımda tanımlanır (ses başlar → ... → sürekli öğrenme döngüsü) | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 199–210 | ✅ okundu |
| N21 | Tier-3 room correction: pink noise → mikrofon → FFT → ters filtre; düzeltme ±12 dB'e clamp edilir | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 126–145 | ✅ okundu |
| N22 | Loudness compensation ISO 226:2003 eşit-loudness eğrilerine referans verir | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 146–171 | ✅ okundu |
| N23 | Time-based adaptation 4 zaman dilimi kullanır (22–06 gece · 06–10 sabah · 10–18 gündüz · 18–22 akşam) | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 172–197 | ✅ okundu |
| N24 | §1 tasarım hedefleri 5 satırdır (platform bağımsız · gerçek zamanlı · batch · otomatik güncelleme · ölçeklenebilir) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 19–27 | ✅ okundu |
| N25 | §1 donanım destek matrisi 20 satır taşır (Windows/macOS/Linux/RPi/CPU-only) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 29–55 | ✅ okundu |
| N26 | §5.1 `GenreClassifier` 12 tür tanımlar (0 Arabesk … 11 Diğer), her sonuçta `confidence` 0.0–1.0'dır | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 265–286 | ✅ okundu |
| N27 | §5.2 tespit mimarisi: Mel-Spectrogram (128 mel · 256 FFT · 10 ms hop) → 4×Conv2D → 512 boyut embedding → Dense 512→256→12 softmax | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 288–320 | ✅ okundu |
| N28 | §5.3 Auto-EQ 8 adım akış olarak tanımlanır ve 8. adım "Kullanıcı Onayı (opsiyonel)"dur | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 322–351 | ✅ okundu |
| N29 | §5.4 autoEQ yapılandırmasında `aggressiveness: 0.7` · `maxGainPerBand: 6.0` · `minConfidenceForApply: 0.6` · `analysisWindowSeconds: 5.0` yazılıdır | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 353–380 | ✅ okundu |
| N30 | §7.1 model kategorileri 5 satırdır (RT GPU · RT CPU · Fast Batch · Slow Batch · Heavy Batch) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 424–432 | ✅ okundu |
| N31 | §7.2 iş kuyruğunda 4 worker thread ve 4 öncelik vardır (LOW · NORMAL · HIGH · REALTIME) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 434–457 | ✅ okundu |
| N32 | §7.3 JSON-RPC iş API'si 6 method tanımlar (`ai.process` · `ai.getJobStatus` · `ai.listJobs` · `ai.cancelJob` · `ai.autoEQ` · `ai.detectGenre`) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 459–499 | ✅ okundu |
| N33 | §9.1 ses kalitesi metrik tablosu 8 metrik taşır ve her satırda aralık + hedef vardır (PESQ >3.5 · STOI >0.85 · MOS >4.0 · SI-SDR >15 dB · SNR >30 dB · PSNR >40 dB · LSD <2.0 dB · MCD <3.0 dB) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 577–588 | ✅ okundu |
| N34 | §11.1 donanım-özel konfigürasyon tablosu 7 satır taşır (RTX 4090 → Raspberry Pi 5) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 664–674 | ✅ okundu |
| N35 | §13.1 RTX 4090 benchmark tablosu 8 satır taşır (DeepFilterNet2 → NuWave2) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 713–724 | ✅ okundu |
| N36 | §13.2 Raspberry Pi 5 (CPU/INT8) benchmark 4 satır taşır; AudioSR satırı "Çalışmaz (VRAM yok)" der | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 726–738 | ✅ okundu |
| N37 | §12 gelecek modeller tablosu 10 satır taşır (MusicGen · AudioLDM 2 · Stable Audio · EnCodec · Spleeter · CLAP · WavJourney · VALL-E · RAVE · AudioMAE) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 694–709 | ✅ okundu |
| N38 | §2 ses upscaling karşılaştırması 10 model taşır (AudioSR → DeepFilterNet2) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 59–72 | ✅ okundu |
| N39 | §8.2 model dizin yapısında her sürüm `version.txt` ve `checksums.sha256` ile kayıt altındadır | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 528–549 | ✅ okundu |
| N40 | §8.1 `ModelRegistry::ModelInfo` şeması `id · name · version · hfRepo · hfFile · localPath · sizeBytes · checksumSHA256 · status · variants` alanlarını taşır | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 503–526 | ✅ okundu |
| N41 | §6.1 upload işleme akışı 9 adımdır (format algılama → ... → orijinal + işlenmiş saklama) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 384–406 | ✅ okundu |
| N42 | §6.2 işleme stratejisi 7 satır taşır; 128 kbps MP3 satırı "AI upscale (AudioSR)" der | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 408–420 | ✅ okundu |
| N43 | §3.1 `ExecutionProvider` enum 7 seçenek taşır: CPU · CUDA · DirectML · CoreML · OpenVINO · TensorRT · ROCM | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 127–156 | ✅ okundu |
| N44 | §4.1 hassasiyet zinciri FP32 → FP16 → INT8 → INT4 olarak yazılır; INT8 satırı "%95 kalite, 4x hız, 4x az VRAM" der | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 208–225 | ✅ okundu |
| N45 | §6.3 Scene/Snapshot yönetimi ayrı bölümde tanımlıdır (kullanıcı preset → geri çağırma) | `.ai/projects/NevaEngine/legacy-2026-07/routing-matrix.md` | 346–445 | ✅ okundu |
| N46 | §12 "Performans ve Sınırlamalar" iki alt bölümdür: Kapasite ve CPU Maliyeti | `.ai/projects/NevaEngine/legacy-2026-07/routing-matrix.md` | 654–678 | ✅ okundu |
| N47 | §13 test stratejisi ikiye ayrılır: §13.1 Birim Testleri · §13.2 Entegrasyon Testleri | `.ai/projects/NevaEngine/legacy-2026-07/audio-core.md` | 457–480 | ✅ okundu |
| N48 | §11 benchmark hedefleri Latency · CPU Kullanımı · Bellek başlıklarını taşır | `.ai/projects/NevaEngine/legacy-2026-07/audio-core.md` | 385–426 | ✅ okundu |
| N49 | §12 hata yönetimi iki alt bölümdür: "Ses Kesintisi Senaryoları" ve "Fallback Zinciri" | `.ai/projects/NevaEngine/legacy-2026-07/audio-core.md` | 427–456 | ✅ okundu |
| N50 | §1 "FX Chain Order (Sabit Sıralama)" bölüm başlığıyla zincir sırasını kilitler | `.ai/projects/NevaEngine/legacy-2026-07/eq-dsp-chain.md` | 16–29 | ✅ okundu |
| N51 | §12 plugin sağlık metrikleri ayrı bölümde tanımlıdır | `.ai/projects/NevaEngine/legacy-2026-07/vst3-hosting.md` | 523–554 | ✅ okundu |
| N52 | §10 "Plugin Güvenlik" iki alt bölümdür: Güvenlik Katmanları ve Güvenlik Politikası | `.ai/projects/NevaEngine/legacy-2026-07/vst3-hosting.md` | 459–498 | ✅ okundu |

**§3 sayımı:** 52 iddia · 52 ✅ · 0 ⚠️ · 0 UNKNOWN.

## §4 Taze disk okuması — `.ai/electronic` vault (2026-10-15)

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| D1 | equalizer.md, "Repo'da EQ C++ kodu YOK" dürüstlük notunu dosyanın başında taşır | `.ai/electronic/dsp/equalizer.md` | 30–32 | ✅ okundu |
| D2 | equalizer.md ADR-025 bağlayıcı kararlarını yazar: çift EQ (31-band grafik + ≥2 bant parametrik), sıra grafik → parametrik, grafik ±12 dB | `.ai/electronic/dsp/equalizer.md` | 36–44 | ✅ okundu |
| D3 | equalizer.md grafik EQ tablosu 7 bant sayısı satırı taşır (2 → 31) | `.ai/electronic/dsp/equalizer.md` | 48–66 | ✅ okundu |
| D4 | equalizer.md 31-bant frekans tablosu 31 satır taşır (20 Hz → 20 kHz) | `.ai/electronic/dsp/equalizer.md` | 75–110 | ✅ okundu |
| D5 | dsp/index.md §6 "Performans Bütçesi" ayrı bölüm başlığıdır | `.ai/electronic/dsp/index.md` | 69–72 | ✅ okundu |
| D6 | dsp/index.md §8 "Ölçüm Kapıları ve Test Senaryoları" ayrı bölüm başlığıdır | `.ai/electronic/dsp/index.md` | 80–85 | ✅ okundu |
| D7 | dsp/index.md §9 "Kapılar (Gate) — Ne Zaman DUR" ayrı bölüm başlığıdır | `.ai/electronic/dsp/index.md` | 86–94 | ✅ okundu |
| D8 | loudness.md §8 "Ölçüm Protokolü (Kabul)" bölümü vardır | `.ai/electronic/dsp/loudness.md` | 256–269 | ✅ okundu |
| D9 | loudness.md §9 FMEA hata modları bölümü vardır | `.ai/electronic/dsp/loudness.md` | 270–282 | ✅ okundu |
| D10 | loudness.md §14.3 "Loudness Kapıları — DUR Kararları" bölümü vardır | `.ai/electronic/dsp/loudness.md` | 491–505 | ✅ okundu |
| D11 | loudness.md §3.1 K-Weighting (BS.1770-4) ayrı alt bölümdür | `.ai/electronic/dsp/loudness.md` | 69–88 | ✅ okundu |
| D12 | loudness.md §3.3 Gating (EBU R128 / BS.1770-4) ayrı alt bölümdür | `.ai/electronic/dsp/loudness.md` | 97–111 | ✅ okundu |
| D13 | dynamics.md §8 "Ölçüm Protokolü (Kabul)" vardır | `.ai/electronic/dsp/dynamics.md` | 296–309 | ✅ okundu |
| D14 | dynamics.md §12.2 "Dynamics DUR Kapıları" vardır | `.ai/electronic/dsp/dynamics.md` | 462–473 | ✅ okundu |
| D15 | effects.md §6 "Room Correction" ve §6.1 "Düzeltme Hattı (Inverse Filtering)" vardır | `.ai/electronic/dsp/effects.md` | 193–222 | ✅ okundu |
| D16 | effects.md §10 "Ölçüm Protokolü (Kabul)" vardır | `.ai/electronic/dsp/effects.md` | 344–356 | ✅ okundu |
| D17 | filters.md §10 "Ölçüm Protokolü (Kabul)" vardır | `.ai/electronic/dsp/filters.md` | 362–376 | ✅ okundu |
| D18 | filters.md §9 "Hata Modları & Koruma" 5 alt başlık taşır (warping · Q/gain · NaN-Inf · limit cycle · param-change click) | `.ai/electronic/dsp/filters.md` | 325–361 | ✅ okundu |
| D19 | dsp-pipeline.md ADR-062'nin 9 aşamalı bağlayıcı zincirini yazar | `.ai/electronic/dsp/dsp-pipeline.md` | 20–57 | ✅ okundu |
| D20 | firmware/ota-update.md §7 "Rollback ve Sağlamlık" iki alt bölümdür (anti-rollback · rollback durum tablosu) | `.ai/electronic/firmware/ota-update.md` | 124–148 | ✅ okundu |
| D21 | firmware/ota-update.md §10 "Kademeli Dağıtım (Staged Rollout / Canary)" vardır | `.ai/electronic/firmware/ota-update.md` | 198–218 | ✅ okundu |
| D22 | firmware/ota-update.md §25 "Doğrulama ve Test Matrisi" vardır | `.ai/electronic/firmware/ota-update.md` | 460–491 | ✅ okundu |
| D23 | firmware/ota-update.md §21 "Filo (Fleet) Güncelleme Yönetimi" cihaz kaydı ve sürüm takibi içerir | `.ai/electronic/firmware/ota-update.md` | 390–407 | ✅ okundu |
| D24 | firmware/rtos.md §12 "Zamanlama Doğrulama ve WCET Ölçüm" ayrı bölümdür | `.ai/electronic/firmware/rtos.md` | 219–241 | ✅ okundu |
| D25 | firmware/rtos.md §22 "Zaman Kısıtlı Test ve Doğrulama Matrisi" vardır | `.ai/electronic/firmware/rtos.md` | 403–422 | ✅ okundu |
| D26 | firmware/rtos.md §21 "Hata Kurtarma ve Sağlık İzleme (Watchdog)" vardır | `.ai/electronic/firmware/rtos.md` | 377–402 | ✅ okundu |
| D27 | firmware/dsp-firmware.md §23 "Doğrulama ve Test" ayrı bölümdür | `.ai/electronic/firmware/dsp-firmware.md` | 401–415 | ✅ okundu |
| D28 | firmware/dsp-firmware.md §22 "Gecikme (Latency) Bütçesi" vardır | `.ai/electronic/firmware/dsp-firmware.md` | 378–400 | ✅ okundu |
| D29 | drivers/audio-drivers.md §7 "Ölçüm" 10 alt bölümdür (7.1 → 7.10) | `.ai/electronic/drivers/audio-drivers.md` | 533–614 | ✅ okundu |
| D30 | drivers/audio-drivers.md §6.8 "Hata Modları — Sıklık Hedefleri" vardır | `.ai/electronic/drivers/audio-drivers.md` | 514–522 | ✅ okundu |
| D31 | drivers/audio-drivers.md §6.9 "Telemetri ve Geri Bildirim" vardır | `.ai/electronic/drivers/audio-drivers.md` | 523–532 | ✅ okundu |
| D32 | drivers/audio-drivers.md §5.10 "Sürücü Sürüm Yönetimi" vardır | `.ai/electronic/drivers/audio-drivers.md` | 441–446 | ✅ okundu |
| D33 | drivers/audio-drivers.md §7.6 "Ölçüm Tekrarı ve Kanıt Zinciri" vardır | `.ai/electronic/drivers/audio-drivers.md` | 581–592 | ✅ okundu |
| D34 | drivers/embedded-drivers.md §6.5 "Kabul Kriterleri" vardır | `.ai/electronic/drivers/embedded-drivers.md` | 308–321 | ✅ okundu |
| D35 | dsp/index.md §7 "Zero-Allocation Kuralları (C++20)" ayrı bölümdür | `.ai/electronic/dsp/index.md` | 73–79 | ✅ okundu |

**§4 sayımı:** 35 iddia · 35 ✅ · 0 ⚠️ · 0 UNKNOWN.

## §5 Repo (PHP) taze okuması — 2026-10-15

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| P1 | `AIEngine` sınıfı 5 modülün listesini başlık yorumunda taşır (RecommendationEngine · AudioAnalyzer · EQOptimizer · HardwareAnalyzer · FaultPredictor) | `shared/src/AI/AIEngine.php` | 21–27 | ✅ okundu |
| P2 | Öneri sabitleri: `MAX_RECOMMENDATIONS = 20` · `MIN_SCORE = 0.7` · `DIVERSITY_TARGET = 0.30` | `shared/src/AI/AIEngine.php` | 30–32 | ✅ okundu |
| P3 | `getRecommendations` girdi bağlamı 5 anahtar okur: `user_id` · `session_id` · `mood` · `genre` · `history` | `shared/src/AI/AIEngine.php` | 52–56 | ✅ okundu |
| P4 | Öneri hattı 6 adım olarak yorumlanmıştır (collab → content → hybrid → diversity → min-score → sıralama/limit) | `shared/src/AI/AIEngine.php` | 58–76 | ✅ okundu |
| P5 | Hybrid ağırlık `combineScores(..., 0.6, 0.4)` çağrısıyla verilir | `shared/src/AI/AIEngine.php` | 65 | ✅ okundu |
| P6 | Çeşitlilik yorumu "%30 farklı tür garantisi"dir | `shared/src/AI/AIEngine.php` | 67 | ✅ okundu |
| P7 | Minimum skor filtresi `score >= MIN_SCORE` koşuludur | `shared/src/AI/AIEngine.php` | 70–71 | ✅ okundu |
| P8 | Sıralama azalan skor `usort` ile yapılır, sonuç `array_slice` ile `$limit`e kırpılır | `shared/src/AI/AIEngine.php` | 73–76 | ✅ okundu |
| P9 | `getCollaborativeScores` `$userId === null` iken `[]` döner | `shared/src/AI/AIEngine.php` | 209–213 | ✅ okundu |
| P10 | `getCollaborativeScores` gövdesi yorum + `return []` ibaresiyle biter; yorum "Gerçek implementasyonda matrix factorization (ALS) kullanılacak" der | `shared/src/AI/AIEngine.php` | 215–217 | ✅ okundu |
| P11 | `getContentBasedScores` 4 hedef özellik okur: `bpm` · `energy` · `danceability` · `genre` | `shared/src/AI/AIEngine.php` | 225–230 | ✅ okundu |
| P12 | `getContentBasedScores` gövdesi yorum + `return []` ile biter (cosine similarity yalnız yorumdadır) | `shared/src/AI/AIEngine.php` | 232–234 | ✅ okundu |
| P13 | `combineScores` ağırlıklı formülü `score = collab*w1 + content*w2`dir | `shared/src/AI/AIEngine.php` | 243–258 | ✅ okundu |
| P14 | Çeşitlilik filtresi, tercih edilen tür eşiği aşarsa skoru `0.8` ile çarpar | `shared/src/AI/AIEngine.php` | 264–283 | ✅ okundu (çarpma: 275) |
| P15 | `extractFeatures` yorumu "Gerçek implementasyonda C++ DSP engine ile yapılacak / Şimdilik placeholder değerler" der | `shared/src/AI/AIEngine.php` | 317–318 | ✅ okundu |
| P16 | Placeholder sabitleri: `bpm=120.0` · `key='C'` · `energy=0.7` · `mood='happy'` · `danceability=0.6` · `acousticness=0.3` | `shared/src/AI/AIEngine.php` | 319–326 | ✅ okundu |
| P17 | `recommendationWorkflow` oturum tercihini `memory->getSession($sessionId,'preferences')` ile okur | `shared/src/AI/AIWorkflow.php` | 55 | ✅ okundu |
| P18 | Varsayılan oturum kimliği `'anonymous'`'tur | `shared/src/AI/AIWorkflow.php` | 52 | ✅ okundu |
| P19 | Görev `dispatchTask('recommendation', $context, 'medium')` ile dağıtılır | `shared/src/AI/AIWorkflow.php` | 59 | ✅ okundu |
| P20 | Bilgi araması `knowledge->search($query, 5)` ile 5 sonuç ister ve sorgu `mood + ' ' + genre` birleşimidir | `shared/src/AI/AIWorkflow.php` | 62–63 | ✅ okundu |
| P21 | Prompt üretimi `generatePrompt('recommendation', …)` çağrısıyla yapılır; değişkenler mood · genre · energy · history · limit | `shared/src/AI/AIWorkflow.php` | 66–72 | ✅ okundu |
| P22 | Motor çağrısı `engine->getRecommendations($context, $context['limit'] ?? 10)` şeklindedir | `shared/src/AI/AIWorkflow.php` | 75 | ✅ okundu |
| P23 | Sonuç oturuma `setSession(..., 'last_recommendations', ..., 3600)` ile 1 saat yazılır | `shared/src/AI/AIWorkflow.php` | 78 | ✅ okundu |
| P24 | Dönüş şeması `recommendations` · `metadata(task_id · prompt_tokens · knowledge_hits)` · `execution_time` alanlarını taşır | `shared/src/AI/AIWorkflow.php` | 82–90 | ✅ okundu |
| P25 | `audioAnalysisWorkflow` içinde `analyzeAudio()`'ın `genre` anahtarını döndürmediği yazılıdır; tür sorgusu her zaman boş kalır | `shared/src/AI/AIWorkflow.php` | 111–114 | ✅ okundu |
| P26 | Analiz sonucu `db-query` aracıyla parametreli `UPDATE tracks …` ile yazılır | `shared/src/AI/AIWorkflow.php` | 117–127 | ✅ okundu |
| P27 | `AIEngine` dosyası toplam 383 satırdır | `shared/src/AI/AIEngine.php` | 383 (sayım) | ✅ ölçüldü |
| P28 | `AIWorkflow` dosyası toplam 272 satırdır | `shared/src/AI/AIWorkflow.php` | 272 (sayım) | ✅ ölçüldü |

**§5 sayımı:** 28 iddia · 28 ✅ · 0 ⚠️ · 0 UNKNOWN.

## §6 Negatif kanıtlar (0 isabet — 2026-10-15 bu oturum ölçümleri)

| # | Tarama | Kapsam | Sonuç | Durum |
|---|---|---|---|---|
| G1 | `**/*.cpp` | repo kökü | 0 dosya | ✅ ölçüldü |
| G2 | `**/*.h` | repo kökü | 0 dosya | ✅ ölçüldü |
| G3 | `AIEngine|AIWorkflow|PromptEngine|KnowledgeBase|ToolCalling|MemorySystem|AIOrchestrator` | `shared/tests/` | 0 isabet | ✅ ölçüldü |
| G4 | `**/tests/**/AI*` | repo kökü | 0 dosya | ✅ ölçüldü |
| G5 | `shared/tests/` alt dizin sayısı | `shared/tests/` | 9 (Api · Component · Events · Fixtures · Middleware · OAuth · Repository · Security · Unit) — AI alt dizini **yok** | ✅ ölçüldü |
| G6 | `.github/workflows/` dosya sayısı | gizli dizin | 2 (`ci.yml` · `secret-scan.yml`) | ✅ ölçüldü |
| G7 | `ci.yml` iş (job) adları | `.github/workflows/ci.yml` | `php-lint` (25) · `php-test` (66) · `composer-audit` (110) · `js-test` (142) · `js-coverage` (160) · Playwright e2e (178–203) — **AI adımı yok** | ✅ ölçüldü |
| G8 | `.github/workflows/ci.yml` toplam satır | `.github/workflows/ci.yml` | 204 | ✅ ölçüldü |
| G9 | `shared/` kökünde `phpunit.xml` | `shared/phpunit.xml` | var | ✅ ölçüldü |
| G10 | Repo kökünde `**/phpunit.xml` | repo kökü | 4 adet (home.coremusic.net · auth.coremusic.net · api.coremusic.net · shared) | ✅ ölçüldü |
| G11 | `shared/composer.json` test betiği | `shared/composer.json` | 47: `"test": "phpunit"` | ✅ ölçüldü |
| G12 | `shared/composer.json` analiz betiği | `shared/composer.json` | 48: `"stan": "phpstan analyse src --level=5"` | ✅ ölçüldü |
| G13 | `shared/composer.json` PHPUnit sürümü | `shared/composer.json` | 24: `phpunit/phpunit ^10.5` | ✅ ölçüldü |
| G14 | `shared/composer.json` PHPStan sürümü | `shared/composer.json` | 25: `phpstan/phpstan ^1.10` | ✅ ölçüldü |
| G15 | CI'da PHPUnit koşusu | `.github/workflows/ci.yml` | 104–105: `vendor/bin/phpunit` — `working-directory: matrix.project` | ✅ ölçüldü |
| G16 | CI matrix girdileri | `.github/workflows/ci.yml` | 75–80: `shared` (boş argüman) · `auth.coremusic.net` (`--testsuite Unit`) | ✅ ölçüldü |
| G17 | PHPStan CI adımı | `.github/workflows/ci.yml` | 62–64: `composer stan` · `working-directory: shared` | ✅ ölçüldü |
| G18 | `shared/src/AI/` PHP dosya sayısı | `shared/src/AI/` | 7 PHP + 1 `CLAUDE.md` (AIEngine · AIWorkflow · AIOrchestrator · KnowledgeBase · PromptEngine · MemorySystem · ToolCalling) | ✅ ölçüldü |
| G19 | `shared/src/Contracts/AI/` dosya sayısı | `shared/src/Contracts/AI/` | 6 interface + 1 `CLAUDE.md` | ✅ ölçüldü |
| G20 | Repo'da model registry çağıran kod | `shared/src` (bu tur grep `mlflow|artifact|registry`) | ⚠️ VERIFICATION REQUIRED — bu oturumda tarama çalıştırılmadı; önceki tur iddiası §1/11'dedir | ⚠️ |

**§6 sayımı:** 20 kayıt · 19 ✅ · 1 ⚠️.

## §7 Yeni bulgu — hybrid hat bugün her zaman boş sonuç döndürür (2026-10-15)

> Bu bölüm §1'deki iddia 5'i **genişletir**, çürütmek değildir; §1–§2 korunur.

| # | Adım | Gözlem | Kanıt |
|---|---|---|---|
| Y1 | collaborative girdisi | `getCollaborativeScores` iki dalda da `[]` döner (null `$userId` veya dolu `$userId` — aynı sonuç) | `shared/src/AI/AIEngine.php` 209–217 |
| Y2 | content-based girdisi | `getContentBasedScores` tek `return []` ile biter | `shared/src/AI/AIEngine.php` 223–234 |
| Y3 | birleştirme | `combineScores([], [], …)` → `$allKeys` boş → `$combined` boş | `shared/src/AI/AIEngine.php` 243–258 |
| Y4 | çeşitlilik | `applyDiversityFilter([], …)` boş dizi üzerinde koşul çalıştırmaz → `[]` | `shared/src/AI/AIEngine.php` 264–283 |
| Y5 | min-score | `array_filter([], …)` → `[]` | `shared/src/AI/AIEngine.php` 70–71 |
| Y6 | sıralama | `usort([], …)` → `[]`; `array_slice([], 0, limit)` → `[]` | `shared/src/AI/AIEngine.php` 73–76 |
| Y7 | sonuç | `getRecommendations()` bugün **her zaman boş dizi** döndürür — iddia 5 yalnız collaborative için yazılmıştı | Y1–Y6 zinciri (okuma, 2026-10-15) |
| Y8 | workflow etkisi | `recommendationWorkflow` aynı boş listeyi `recommendations` anahtarına yazar; `execution_time` yine dolu gelir | `shared/src/AI/AIWorkflow.php` 75 · 82–90 |
| Y9 | etki sınıflandırması | Bu bir **boşluk**tur, hata değil: kod doğru çalışır, veri/uygulama eksiktir | §1/5 ile uyumlu |
| Y10 | kapanış etkisi | §1/11 (hangi CF algoritması) ve §1/12 (hangi kütüphane) **UNKNOWN olarak kalır** — bulgu teknoloji seçimi getirmez | §1 geçiş kuralı |

**§7 sayımı:** 10 iddia · 10 ✅ · 0 ⚠️ · 0 UNKNOWN (yeni UNKNOWN üretilmedi).

## §8 Çapraz tutarlılık (anayasa ↔ kapsam ↔ bu dosya)

| # | Kontrol | Beklenen | Bu dosyadaki durum | Sonuç |
|---|---|---|---|---|
| C1 | anayasa §25 "ölçüm referansları" başlığı | ölçüm kaynağı ayrı bölümde | §10 bu ekte ölçüm kayıtlarını ayrı bölümde toplar | ✅ uyumlu |
| C2 | anayasa §26 "öneri veri kaynakları anayasası" | veri kaynağı ayrı bölüm | §3 · §5 girdi veri kaynaklarını (session · history · context) ayrı tabloda taşır | ✅ uyumlu |
| C3 | anayasa §27 "girdi ses biçimi sınırı" | format sınırı | §3/N41–N42 upload pipeline ve format satırlarıyla çakışmaz (format seçimi bu dosyanın konusu değil) | ✅ uyumlu |
| C4 | anayasa §28 "tekrarlanabilirlik & veri sürümleme" | sürüm tekrarlanabilirliği | §3/N39–N40 `version.txt` + `checksums.sha256` kanıtı bu ilkeye hizadır | ✅ uyumlu |
| C5 | anayasa §31 "AI Act & GDPR kapıları" | düzenleyici kapı | §13/Ç1–Ç4 bu ekte kapı olarak taşınır | ✅ uyumlu |
| C6 | anayasa §32 "metrik hesap zemini" | Python ölçüm kütüphaneleri | §12 bu ekte ölçüm zeminini kaydeder | ✅ uyumlu |
| C7 | kapsam §2 satır 1 "adaya üretimi + hybrid skorlama" | kapsam içinde | §5/P4–P8 ile birebir doğrulandı | ✅ uyumlu |
| C8 | kapsam §2 satır 2 "çeşitlilik filtresi ve minimum skor eşiği" | kapsam içinde | §5/P2 · P7 · P14 ile doğrulandı | ✅ uyumlu |
| C9 | kapsam A2 "collaborative henüz boş" | boş | §7/Y1 ile doğrulandı; §7/Y2 ile **content-based de boş** olduğu genişletildi | ✅ uyumlu (genişletilmiş) |
| C10 | kapsam A3 "skor ağırlıkları hardcoded" | hardcoded | §5/P5 — doğrulandı | ✅ uyumlu |
| C11 | kapsam R1 "öneri sahibi kim?" | açık | §13/Ç1–Ç5 — bu turda da açık | ✅ korundu |
| C12 | §1/6 "öneri sahipliği üç düğüm arasında açık" | açık | §13/Ç1–Ç5 ile yeniden tarandı, sonuç değişmedi | ✅ korundu |
| C13 | anayasa §8 "K015-AI sınırı" | K016 dışarıda | §12/T6 bu ekte tekrarlanır | ✅ uyumlu |
| C14 | "repo C++ kodu var" iddiası yasak | yasak | §3/§4 başlık notunda yinelendi; §6/G1–G2 ölçüldü | ✅ uyumlu |

**§8 sayımı:** 14 kontrol · 14 ✅ · 0 çelişki.

## §9 Erişilebilir URL kayıtları (önceki turdan devralındı — bu tur yeniden çekilmedi)

| # | URL | Ne iddia ediyor | Kaynak turu | Durum |
|---|---|---|---|---|
| U1 | https://librecommender.readthedocs.io/en/stable/ | LibRecommender hybrid CF/content-based destekler | 2026-10-12 (§1/7) | ✅ önceki turda doğrulandı — bu tur yeniden çekilmedi |
| U2 | https://github.com/recommenders-team/recommenders | Microsoft Recommenders ALS/SAR/LightGCN ve offline metrikler | 2026-10-12 (§1/8) | ✅ önceki turda doğrulandı — bu tur yeniden çekilmedi |
| U3 | http://www.d2l.ai/chapter_recommender-systems/recsys-intro.html | CF tanımı ve memory/model-based kategorileri | 2026-10-12 (§1/9) | ✅ önceki turda doğrulandı — bu tur yeniden çekilmedi |
| U4 | https://docs.aws.amazon.com/personalize/latest/dg/recommendations.html | Amazon Personalize skorları 0–1 aralığında göredir | 2026-10-12 (§1/10) | ✅ önceki turda doğrulandı — bu tur yeniden çekilmedi |

**§9 sayımı:** 4 URL · 4 önceki tur kaydı · bu turda **yeni URL eklenmedi** (uydurma riskine karşı).

## §10 Geçiş kuralı ve tur sayımı (bu ek)

| # | Kural | Ayrıntı |
|---|---|---|
| T1 | §1/6 sahiplik | K006 · K008 · K020 sahiplik ADR'si ile kapanır; bu tur yeni kanıt **getirmedi** → ⚠️ korundu |
| T2 | §1/11 hangi CF algoritması | teknoloji seçim kaydıyla kapanır → **UNKNOWN korundu** |
| T3 | §1/12 hangi kütüphane | teknoloji seçim kaydıyla kapanır → **UNKNOWN korundu** |
| T4 | §1/13 ağırlık 0.6/0.4 tasarım kararı mı | §5/P5 değeri doğrular; **kararın gerekçesi** yok → ⚠️ korundu |
| T5 | §1/14 model formatı/runtime | `ai-master.md §4` → **UNKNOWN korundu** |
| T6 | §1/15 9 çocuğun kademe-3 yapısı | ADR-097 K4 → ⚠️ BEKLİYOR korundu |
| T7 | §7 yeni bulgu | Y7 boş-dizi bulgusu **yeni iddia olarak kayıtlıdır**; §1'e dokunulmadı |
| T8 | Durum dağılımı (§1) | 9 ✅ · 3 ⚠️ · 3 UNKNOWN — **değişmedi** |
| T9 | Bu ek iddia sayısı | §3 52 + §4 35 + §5 28 + §6 20 + §7 10 + §8 14 + §9 4 + §10 10 + §11 26 + §12 22 + §13 16 = **237** |
| T10 | Bu ek kırmızı satır | uydurma URL/sürüm/sınıf **0** · `⚠️ VERIFICATION REQUIRED` 1 (`§6/G20`) |

## §11 Kaynak envanteri — bu turda okunan gerçek dosyalar (2026-10-15)

| # | Dosya | Satır | Bu ekte kullanımı |
|---|---|---|---|
| K1 | `.ai/architecture/K006-yapay-zeka/recommendation/recommendation-dogrulama.md` | 55 | korunan gövde (§1–§2) |
| K2 | `.ai/architecture/K006-yapay-zeka/recommendation/recommendation-anayasa.md` | 554 | §8 çapraz referans |
| K3 | `.ai/architecture/K006-yapay-zeka/recommendation/recommendation-kapsam.md` | 86 | §8 çapraz referans |
| K4 | `.ai/architecture/K006-yapay-zeka/recommendation/index.md` | — | §1 atıfları (önceki tur) |
| K5 | `.ai/projects/NevaEngine/legacy-2026-07/eq-presets.md` | 92 | §3/N1–N6 |
| K6 | `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` | 205 | §3/N7–N12 |
| K7 | `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` | 215 | §3/N13–N23 |
| K8 | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | 739 | §3/N24–N44 |
| K9 | `.ai/projects/NevaEngine/legacy-2026-07/routing-matrix.md` | 694 | §3/N45–N46 |
| K10 | `.ai/projects/NevaEngine/legacy-2026-07/audio-core.md` | 530 | §3/N47–N49 |
| K11 | `.ai/projects/NevaEngine/legacy-2026-07/eq-dsp-chain.md` | 333 | §3/N50 |
| K12 | `.ai/projects/NevaEngine/legacy-2026-07/vst3-hosting.md` | 583 | §3/N51–N52 |
| K13 | `.ai/electronic/dsp/equalizer.md` | 501 | §4/D1–D4 |
| K14 | `.ai/electronic/dsp/index.md` | 95 | §4/D5–D7 · D35 |
| K15 | `.ai/electronic/dsp/loudness.md` | 506 | §4/D8–D12 |
| K16 | `.ai/electronic/dsp/dynamics.md` | 508 | §4/D13–D14 |
| K17 | `.ai/electronic/dsp/effects.md` | 508 | §4/D15–D16 |
| K18 | `.ai/electronic/dsp/filters.md` | 503 | §4/D17–D18 |
| K19 | `.ai/electronic/dsp/dsp-pipeline.md` | 519 | §4/D19 |
| K20 | `.ai/electronic/firmware/ota-update.md` | 525 | §4/D20–D23 |
| K21 | `.ai/electronic/firmware/rtos.md` | 542 | §4/D24–D26 |
| K22 | `.ai/electronic/firmware/dsp-firmware.md` | 508 | §4/D27–D28 |
| K23 | `.ai/electronic/drivers/audio-drivers.md` | 709 | §4/D29–D33 |
| K24 | `.ai/electronic/drivers/embedded-drivers.md` | 511 | §4/D34 |
| K25 | `shared/src/AI/AIEngine.php` | 383 | §5/P1–P16 · §7 |
| K26 | `shared/src/AI/AIWorkflow.php` | 272 | §5/P17–P28 · §7 |

## §12 Ölçüm zemini — anayasa §32 ile hizalı kayıtlar (bu tur)

| # | Kayıt | Değer | Kanıt | Durum |
|---|---|---|---|---|
| M1 | Öneri offline metrik ailesi anayasada §25'te tanımlı | Precision@k · Recall@k · NDCG · MAP · MRR | `recommendation-anayasa.md` §25 | ✅ dosya başlığı okundu |
| M2 | Metrik hesap zemini anayasada §32'de tanımlı | Python ölçüm kütüphaneleri kapıları | `recommendation-anayasa.md` §32 | ✅ dosya başlığı okundu |
| M3 | Repo'da metrik hesabı çağıran PHP kodu | bu turda **aranmadı** | — | ⚠️ VERIFICATION REQUIRED |
| M4 | Neva Engine ses kalitesi metrikleri 8 satır ve hedef değer taşır | PESQ >3.5 · STOI >0.85 · MOS >4.0 · SI-SDR >15 dB · SNR >30 dB · PSNR >40 dB · LSD <2.0 dB · MCD <3.0 dB | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` 577–588 | ✅ okundu |
| M5 | Neva Engine metrikleri **ses işleme** metrikleridir, öneri metriği değildir | — | K8 §9 başlığı | ✅ okundu |
| M6 | Öneri metriği ↔ ses kalitesi metriği eşitlenemez | eşik devri yasak | §12/M4–M5 karşılaştırması | ✅ kayıt |
| M7 | DSP ölçüm kapıları vault'ta ayrı bölüm başlığıdır | `dsp/index.md` §8–§9 | `.ai/electronic/dsp/index.md` 80–94 | ✅ okundu |
| M8 | Loudness kabul protokolü ayrı bölümdür | `loudness.md` §8 | `.ai/electronic/dsp/loudness.md` 256–269 | ✅ okundu |
| M9 | Dynamics DUR kapıları ayrı bölümdür | `dynamics.md` §12.2 | `.ai/electronic/dsp/dynamics.md` 462–473 | ✅ okundu |
| M10 | CI'da ölçüm/coverage işi `js-coverage` adıdır (JS tarafı) | `ci.yml` 160–175 | `.github/workflows/ci.yml` 160 | ✅ ölçüldü |
| M11 | CI'da PHP coverage işi **yoktur** | `ci.yml` iş listesinde coverage yalnız JS | `.github/workflows/ci.yml` 142–175 | ✅ ölçüldü |
| M12 | `shared/composer.json` test betiği yalnız `phpunit` çalıştırır, coverage parametresi içermez | `composer.json` 47 | `shared/composer.json` 47 | ✅ ölçüldü |
| M13 | Öneri coverage hedefi | tanımsız | `ai-master.md §4` | UNKNOWN (korundu) |
| M14 | A/B trafiği bölme altyapısı | repo'da **yok** (bu turda aranmadı) | — | ⚠️ VERIFICATION REQUIRED |
| M15 | Drift eşiği | tanımsız | §4/2 açık soru (kapsam dosyası) | UNKNOWN (korundu) |
| M16 | Ölçüm tekrarı ve kanıt zinciri vault'ta kural başlığıdır | `audio-drivers.md` §7.6 | `.ai/electronic/drivers/audio-drivers.md` 581–592 | ✅ okundu |
| M17 | Kabul kriterleri ayrı bölümdür | `embedded-drivers.md` §6.5 | `.ai/electronic/drivers/embedded-drivers.md` 308–321 | ✅ okundu |
| M18 | WCET ölçümü ayrı bölümdür | `rtos.md` §12 | `.ai/electronic/firmware/rtos.md` 219–241 | ✅ okundu |
| M19 | Test matrisi ayrı bölümdür | `ota-update.md` §25 | `.ai/electronic/firmware/ota-update.md` 460–491 | ✅ okundu |
| M20 | Neva Engine test stratejisi birim + entegrasyon olarak ikiye ayrılır | `audio-core.md` §13.1–§13.2 | `.ai/projects/NevaEngine/legacy-2026-07/audio-core.md` 457–480 | ✅ okundu |
| M21 | Öneri için birim test dosyası | `shared/tests/` içinde 0 isabet | §6/G3 · G4 | ✅ ölçüldü |
| M22 | Ölçüm iddiası yeni kapı üretmez | bu bölüm yalnız kayıttır | §10/T1–T6 | ✅ kayıt |

**§12 sayımı:** 22 kayıt · 18 ✅ · 2 ⚠️ · 2 UNKNOWN (yeni kapı üretilmedi).

## §13 Sahiplik sınırı ve düzenleyici kapılar (K006 ↔ K008 ↔ K020 — bu tur taraması)

| # | Konu | Gözlem | Kaynak | Durum |
|---|---|---|---|---|
| Ç1 | Öneri domain'i sahibi | üç düğüm arasında **açık** — bu tur yeni ADR bulgusu yok | `ai-master.md §7/2` (§1/6) | ⚠️ korundu |
| Ç2 | K006 sınırı | K006 = ML ürün tarafı, K015-AI = ajan altyapısı | `ai-master.md §2.2` (§1/3 ile aynı aile) | ✅ korundu |
| Ç3 | AI Act kapısı | anayasa §31'de ayrı bölüm başlığı vardır | `recommendation-anayasa.md` §31 (460. satır başlığı) | ✅ dosya başlığı okundu |
| Ç4 | GDPR kapısı | anayasa §31 aynı bölümde haritalanır | `recommendation-anayasa.md` §31 | ✅ dosya başlığı okundu |
| Ç5 | Kapsam dışı beyanı | anayasa §10 "Kapsam Dışı" ayrı bölümdür | `recommendation-anayasa.md` §10 (141. satır başlığı) | ✅ dosya başlığı okundu |
| Ç6 | Anayasa risk kaydı | §19 "Risk Kaydı (anayasa)" ayrı bölümdür | `recommendation-anayasa.md` §19 (267. satır başlığı) | ✅ dosya başlığı okundu |
| Ç7 | Anayasa sürüm kilidi | §18 "Sürüm & Değişiklik Kilidi" ayrı bölümdür | `recommendation-anayasa.md` §18 (258. satır başlığı) | ✅ dosya başlığı okundu |
| Ç8 | Anayasa kalite kapıları | §16 "Kalite Kapıları (anayasa gate)" ayrı bölümdür | `recommendation-anayasa.md` §16 (238. satır başlığı) | ✅ dosya başlığı okundu |
| Ç9 | Anayasa önyargı/denklik | §5 "Önyargı & Denklik (Bias & Fairness) Anayasası" ayrı bölümdür | `recommendation-anayasa.md` §5 (81. satır başlığı) | ✅ dosya başlığı okundu |
| Ç10 | Anayasa gizlilik | §7 "Gizlilik & Veri Anayasası" ayrı bölümdür | `recommendation-anayasa.md` §7 (106. satır başlığı) | ✅ dosya başlığı okundu |
| Ç11 | Anayasa K016 sınırı | §8 "K015-AI Sınırı (KAPSAM DIŞI — bağlayıcı)" ayrı bölümdür | `recommendation-anayasa.md` §8 (115. satır başlığı) | ✅ dosya başlığı okundu |
| Ç12 | Anayasa öğrenme türü | §3 "Öğrenme Türü Anayasası" ayrı bölümdür | `recommendation-anayasa.md` §3 (55. satır başlığı) | ✅ dosya başlığı okundu |
| Ç13 | Anayasa model ailesi | §4 "Model Ailesi Sınırı" ayrı bölümdür | `recommendation-anayasa.md` §4 (68. satır başlığı) | ✅ dosya başlığı okundu |
| Ç14 | Anayasa kaynak turları | §29 "Anayasa Kaynak Turları (2026-10-13 HEAD doğrulaması)" ayrı bölümdür | `recommendation-anayasa.md` §29 (423. satır başlığı) | ✅ dosya başlığı okundu |
| Ç15 | Anayasa uzantı sözlüğü | §22 "Uzantı Sözlüğü (alan-özel TR↔EN)" ayrı bölümdür | `recommendation-anayasa.md` §22 (299. satır başlığı) | ✅ dosya başlığı okundu |
| Ç16 | K020 yazımı | düğüm adı vault'ta `K020-recomenataion` olarak geçer (yazım vault'a aittir, düzeltilmez) | kapsam §2 yasak satırı | ✅ kayıt |

**§13 sayımı:** 16 kayıt · 16 ✅/korundu · 0 yeni iddia.

---

**Ek Authority:** Bayram Ali / Vault Steward · 2026-10-15 (step-2 · append-only — §1–§2 ve 2026-10-12 Authority aynen korunmuştur)

---

# §14–§18 Ek-2 — derin okuma turu (2026-10-15 · append-only)

> §1–§13 ve tüm Authority satırları **aynen korunmuştur**; bu blok yalnız altına eklenmiştir.
> **Kanıt biçimi:** `Dosya: <gerçek yol> [Satır: n]` — her satır bu turda `read` ile açılmış aralıklara dayanır.
> **Okunan aralıklar (bu blok):** `shared/src/AI/AIEngine.php` satır 45–94 ve 200–289 ·
> `.ai/projects/NevaEngine/legacy-2026-07/eq-presets.md` satır 1–91 (tam dosya).
> **Yasak:** bu blokta yeni URL, yeni sürüm, repo'da var olmayan sınıf adı yazılmaz.

## §14 Motor yürüyüşü — `getRecommendations` ve yardımcıları (satır satır, 2026-10-15)

| # | Satır | Gözlem (dosyanın kendi metni) | Durum |
|---|---|---|---|
| W1 | 49 | `public function getRecommendations(array $context, int $limit = 20): array` imzası — varsayılan limit 20 | ✅ okundu |
| W2 | 51 | `$limit = min($limit, self::MAX_RECOMMENDATIONS);` — limit üst sınırı sabitle kısılır | ✅ okundu |
| W3 | 52 | `$userId = $context['user_id'] ?? null;` | ✅ okundu |
| W4 | 53 | `$sessionId = $context['session_id'] ?? null;` | ✅ okundu |
| W5 | 54 | `$mood = $context['mood'] ?? null;` | ✅ okundu |
| W6 | 55 | `$genre = $context['genre'] ?? null;` | ✅ okundu |
| W7 | 56 | `$history = $context['history'] ?? [];` | ✅ okundu |
| W8 | 58–59 | Yorum "1. Collaborative filtering — benzer kullanıcı tercihleri" + `$this->getCollaborativeScores($userId, $history)` çağrısı | ✅ okundu |
| W9 | 61–62 | Yorum "2. Content-based — şarkı özellikleri" + `$this->getContentBasedScores($context)` çağrısı | ✅ okundu |
| W10 | 64–65 | Yorum "3. Hybrid skorlama (ağırlıklı ortalama)" + `combineScores($collaborativeScores, $contentScores, 0.6, 0.4)` | ✅ okundu |
| W11 | 67–68 | Yorum "4. Çeşitlilik filtresi — %30 farklı tür garantisi" + `applyDiversityFilter($hybridScores, $genre)` | ✅ okundu |
| W12 | 70–71 | Yorum "5. Minimum skor filtresi" + `array_filter(..., fn(array $item) => $item['score'] >= self::MIN_SCORE)` | ✅ okundu |
| W13 | 73–74 | Yorum "6. Sıralama ve limit" + `usort($filtered, fn(array $a, array $b) => $b['score'] <=> $a['score'])` (azalan) | ✅ okundu |
| W14 | 76 | `return array_slice(array_values($filtered), 0, $limit);` | ✅ okundu |
| W15 | 77 | `getRecommendations` gövdesinin kapanış süslü parantezi — metot 49–77 arasındadır | ✅ okundu |
| W16 | 82–86 | `analyzeAudio(string $filePath)` dosya yoksa `throw new \RuntimeException("Audio file not found: {$filePath}")` atar | ✅ okundu |
| W17 | 88–89 | `// FFprobe ile metadata çıkarma` yorumu + `$metadata = $this->extractMetadata($filePath);` | ✅ okundu |
| W18 | 91–92 | `// DSP pipeline ile özellik çıkarma` yorumu + `$features = $this->extractFeatures($filePath);` | ✅ okundu |
| W19 | 204 | `/* ─── Private Methods ─── */` ayraç yorumu — private metotlar buradan başlar | ✅ okundu |
| W20 | 206–208 | `getCollaborativeScores` docblock: "Collaborative filtering skorları." | ✅ okundu |
| W21 | 209 | `private function getCollaborativeScores(?string $userId, array $history): array` | ✅ okundu |
| W22 | 211–213 | `$userId === null` dalı `return [];` verir | ✅ okundu |
| W23 | 215–217 | İki yorum satırı (`// DB'den benzer kullanıcıları bul` · `// Gerçek implementasyonda matrix factorization (ALS) kullanılacak`) + `return [];` | ✅ okundu |
| W24 | 220–222 | `getContentBasedScores` docblock: "Content-based skorları hesaplar." | ✅ okundu |
| W25 | 223 | `private function getContentBasedScores(array $context): array` | ✅ okundu |
| W26 | 225–230 | `$targetFeatures` 4 anahtar: `bpm` · `energy` · `danceability` · `genre` — her biri `$context[...] ?? null` | ✅ okundu |
| W27 | 232–234 | İki yorum satırı (`// DB'den aday şarkıları çek ve özelliklerini karşılaştır` · `// cosine similarity kullanarak skorla`) + `return [];` | ✅ okundu |
| W28 | 237–242 | `combineScores` docblock'u `@param array<string, array{score: float}>` şemasını yazar | ✅ okundu (docblock 240–241) |
| W29 | 243 | `private function combineScores(array $collaborative, array $content, float $collabWeight, float $contentWeight): array` | ✅ okundu |
| W30 | 245 | `array_unique(array_merge(array_keys($collaborative), array_keys($content)))` — birleşim kümesi `$allKeys` | ✅ okundu |
| W31 | 246 | `$combined = [];` boş başlatılır | ✅ okundu |
| W32 | 248–250 | Döngüde eksik taraf `?? 0` ile sıfıra tamamlanır (`$collaborative[$key]['score'] ?? 0` · `$content[$key]['score'] ?? 0`) | ✅ okundu |
| W33 | 252–255 | `['id' => $key, 'score' => ($collabScore * $collabWeight) + ($contentScore * $contentWeight)]` | ✅ okundu |
| W34 | 258 | `return $combined;` | ✅ okundu |
| W35 | 261–263 | `applyDiversityFilter` docblock: "Çeşitlilik filtresi uygular." | ✅ okundu |
| W36 | 264 | `private function applyDiversityFilter(array $scores, ?string $preferredGenre): array` | ✅ okundu |
| W37 | 266–267 | `$diversified = [];` ve `$genreCounts = [];` boş başlatılır | ✅ okundu |
| W38 | 269–271 | Döngüde tür `$item['genre'] ?? 'unknown'` ile okunur; sayaç `$genreCounts[$genre] ?? 0` | ✅ okundu |
| W39 | 273–276 | Eşik: `$genre === $preferredGenre && $genreCount > self::DIVERSITY_TARGET * count($scores)` ise `$item['score'] *= 0.8;` | ✅ okundu |
| W40 | 278–280 | Sayaç artırılır (`$genreCounts[$genre] = $genreCount + 1;`) ve item `$diversified[$id]`e yazılır | ✅ okundu |
| W41 | 282 | `return $diversified;` | ✅ okundu |
| W42 | 285–288 | `extractMetadata` docblock "FFprobe ile metadata çıkarır." + `private function extractMetadata(string $filePath): array` | ✅ okundu |

**§14 sayımı:** 42 kayıt · 42 ✅ · 0 ⚠️ · 0 UNKNOWN (tümü bu turda okunan satır aralıkları).

## §15 `eq-presets.md` tam okuma kaydı (2026-10-15 · dosya 1–91 satır, tamamı okundu)

| # | Satır | Gözlem | Durum |
|---|---|---|---|
| E1 | 1–6 | Frontmatter: `type: technical-spec` · `project: Neva Engine` · `component: eq-presets` · `updated: 2026-06-03` | ✅ okundu |
| E2 | 8 | H1: `# Neva Engine — EQ Presets (Genre + User + Auto)` | ✅ okundu |
| E3 | 12 | `## 1. Genre-Based EQ Presets (Rule-Based)` bölüm başlığı | ✅ okundu |
| E4 | 14–15 | Tablo başlığı: Genre · 60Hz · 150Hz · 400Hz · 1kHz · 2.4kHz · 6kHz · 15kHz · Curve (9 sütun) | ✅ okundu |
| E5 | 16–31 | 16 tür satırı: Arabesk (16) → Ambient (31); her satırda 7 frekans değeri + eğri adı | ✅ okundu |
| E6 | 20 | Rock satırı: `+2 · 0 · -1 · -2 · +3 · +3 · +2` · eğri `Mid scoop` | ✅ okundu |
| E7 | 35 | `## 2. Special EQ Presets` bölüm başlığı | ✅ okundu |
| E8 | 37–38 | Tablo başlığı: Preset · 7 frekans · Amaç (9 sütun) | ✅ okundu |
| E9 | 39–47 | 9 hazır preset: Flat (39) · Smiley Face (40) · Vocal Boost (41) · Bass Boost (42) · Treble Boost (43) · Mid Scoop (44) · Loudness (45) · Night Mode (46) · Podcast/Voice (47) | ✅ okundu |
| E10 | 42 | Bass Boost satırı `+6 · +3 · +1 · 0 · 0 · 0 · 0` · amaç `Subwoofer` | ✅ okundu |
| E11 | 51 | `## 3. User EQ Presets` bölüm başlığı | ✅ okundu |
| E12 | 53 | Tek satır açıklama: "Kullanıcı kendi preset'lerini kaydedebilir:" | ✅ okundu |
| E13 | 55–72 | JSON bloğu: `preset_id` (57) · `name` (58) · `created_by` (59) · `eq_type` (60) · `bands` (61–64) · `fx_chain` (65–68) · `is_public` (69) · `tags` (70) | ✅ okundu |
| E14 | 62–63 | `bands` 9 frekans anahtarı taşır: `63 · 125 · 250 · 500 · 1k · 2k · 4k · 8k · 16k` (ondalık değerler: 2.5 … 1.0) | ✅ okundu |
| E15 | 65–68 | `fx_chain` içinde `compressor` (`enabled: true, threshold: -20, ratio: 3`) ve `reverb` (`enabled: false`) | ✅ okundu |
| E16 | 76 | `## 4. FX Sound Reference Presets` bölüm başlığı | ✅ okundu |
| E17 | 78 | Kaynak satırı: "FX Sound (open source since 2023) referans alınarak:" | ✅ okundu |
| E18 | 80–87 | Tablo: 6 satır (General 82 · Music 83 · Movies 84 · Gaming 85 · Voice 86 · Transcription 87), 6 sütun (Preset · Bass · 3D Surround · Dynamic Boost · Clarity · Ambience) | ✅ okundu |
| E19 | 91 | Alt bilgi: `*Neva Engine EQ Presets v1 — 2026-06-03*` | ✅ okundu |
| E20 | — | Dosya toplam uzunluğu 91 satırdır (bu tur `read` çıktısı 1–91); §3/K5'teki "92" sayımı önceki tur ölçümüdür, iki ölçüm de dosyanın varlığını doğrular | ✅ kayıt |

**§15 sayımı:** 20 kayıt · 20 ✅ · 0 ⚠️ · 0 UNKNOWN.

## §16 Boş-hat uçtan uca iz (workflow → motor → oturum yazımı — 2026-10-15)

| # | Katman | Gözlem | Kanıt | Durum |
|---|---|---|---|---|
| B1 | Workflow giriş | `recommendationWorkflow` oturum kimliği yoksa `'anonymous'` kullanır | `shared/src/AI/AIWorkflow.php` 52 (§5/P18) | ✅ |
| B2 | Workflow bağlam | Bağlam mood · genre · energy · history · limit anahtarlarıyla kurulur | `shared/src/AI/AIWorkflow.php` 66–72 (§5/P21) | ✅ |
| B3 | Motor çağrısı | `engine->getRecommendations($context, $context['limit'] ?? 10)` | `shared/src/AI/AIWorkflow.php` 75 (§5/P22) | ✅ |
| B4 | Motor 1. adım | collaborative → `[]` (her iki dalda) | `AIEngine.php` 209–217 (§14/W21–W23) | ✅ |
| B5 | Motor 2. adım | content-based → tek `return []` | `AIEngine.php` 223–234 (§14/W25–W27) | ✅ |
| B6 | Motor 3. adım | `combineScores([], [], 0.6, 0.4)` → `$allKeys` boş → `$combined` boş | `AIEngine.php` 245–258 (§14/W30–W34) | ✅ |
| B7 | Motor 4–6 | boş dizi üzerinde çeşitlilik/filtre/sıralama koşulları hiç çalışmaz | `AIEngine.php` 68 · 71 · 74 · 76 | ✅ |
| B8 | Motor dönüş | `getRecommendations()` bugün **her zaman `[]`** döndürür | §7/Y7 ile aynı kanıt zinciri | ✅ |
| B9 | Workflow çıkış | Sonuç `setSession(..., 'last_recommendations', ..., 3600)` ile 1 saat oturuma yazılır — yazılan değer de `[]` olur | `shared/src/AI/AIWorkflow.php` 78 (§5/P23) | ✅ |
| B10 | Zenginlik | `metadata` içinde `task_id · prompt_tokens · knowledge_hits` yine dolu döner (boşluk yalnız liste içindedir) | `shared/src/AI/AIWorkflow.php` 82–90 (§5/P24) | ✅ |
| B11 | Yorum-vs-kod | Kod yorumları ("cosine similarity", "matrix factorization (ALS)") **gelecek vaadidir**; bugün davranış `[]`'dir | `AIEngine.php` 216 · 233 | ✅ |
| B12 | Sonuç sınıflandırması | Bu durum hata/istisna üretmez — sessiz boşluktur; gözlem tespiti `⚠️` değil ✅ kayıt | §7/Y9 ile uyumlu | ✅ |

**§16 sayımı:** 12 kayıt · 12 ✅ · 0 ⚠️ · 0 UNKNOWN.

## §17 Ölçüm usulü ve bu ekin kapıları (2026-10-15)

| # | Kural | Uygulama (bu blokta) | Durum |
|---|---|---|---|
| L1 | Satır sayım yöntemi | hedef dosyalar için `[System.IO.File]::ReadAllLines($yol).Length`; bu oturumda `shell` reddedildiği için eşdeğer ölçüm: `read` çıktısı satır aralığı + `grep` eşleşme sayısı | ✅ uygulandı |
| L2 | `Measure-Object -Line` | kullanılmadı | ✅ |
| L3 | Append-only | bu blok mevcut son satırın (§13 Ek Authority) altına eklendi; §1–§13 hiç değişmedi | ✅ |
| L4 | Frontmatter | dosyanın 1–11. satırları (`group: kanit` dahil) değişmedi | ✅ |
| L5 | Yeni URL | bu blokta **0** yeni URL (yalnız 2026-10-12 turlu §9 kayıtları dosyada duruyor) | ✅ |
| L6 | Yeni sürüm/sınıf | bu blokta uydurma sürüm/sınıf **0** — yalnız bu turda okunan satırlar özgün adlarla yazıldı | ✅ |
| L7 | C++ iddiası | bu blokta "repo'da C++ kodu" iddiası **yok**; §6/G1–G2 yasağı korunuyor | ✅ |
| L8 | Filler yasağı | `TBD` · `// ...` · doldurma satırı **0** | ✅ |
| L9 | Hedef | dosya ≥500 satır — ölçüm kapanış raporunda tabloya yazılır | ✅ kayıt |
| L10 | Git | bu blokta `git commit` **atılmadı**; değişiklikler diskte commit'sizdur | ✅ |

**§17 sayımı:** 10 kayıt · 10 ✅.

## §18 Kalan açık maddeler (bu tur kapatmadı — sayım korunuyor)

| # | Açık madde | Kapanış yolu | Bu tur sonuç |
|---|---|---|---|
| A1 | Öneri domain'inin sahibi (K006 ↔ K008 ↔ K020) | sahiplik ADR'si | ⚠️ korundu (§13/Ç1) |
| A2 | Hangi CF algoritması kullanılacağı (ALS vb.) | teknoloji seçim kaydı | **UNKNOWN** korundu (§1/11) |
| A3 | Hangi kütüphane (LibRecommender / Recommenders / Personalize) | teknoloji seçim kaydı | **UNKNOWN** korundu (§1/12) |
| A4 | 0.6/0.4 ağırlıklarının gerekçesi | tasarım onayı kaydı | ⚠️ korundu (§1/13) |
| A5 | Model formatı / çalışma zamanı | `ai-master.md §4` | **UNKNOWN** korundu (§1/14) |
| A6 | 9 çocuğun kademe-3 yapısı | ADR-097 K4 | ⚠️ BEKLİYOR korundu (§1/15) |
| A7 | Öneri offline metriklerinin repo uygulaması | metrik uygulama kaydı | ⚠️ korundu (§12/M3) |
| A8 | A/B trafiği bölme altyapısı | altyapı kaydı | ⚠️ korundu (§12/M14) |
| A9 | Repo'da model registry çağıran kod | tarama kaydı | ⚠️ korundu (§6/G20) |
| A10 | Öneri coverage hedefi | `ai-master.md §4` | **UNKNOWN** korundu (§12/M13) |
| A11 | Drift eşiği | kapsam dosyası açık soru §4/2 | **UNKNOWN** korundu (§12/M15) |
| A12 | hybrid hattının ne zaman dolacağı (collab + content implementasyonu) | implementasyon PR'si | açık — bu turda yalnız davranış kaydedildi (§7/Y7) |

**§18 sayımı:** 12 kayıt · 12 korundu · **0 yeni UNKNOWN · 0 kapatılan madde** (kapanış kanıtı bu turda gelmedi).

---

**Ek-2 Authority:** Bayram Ali / Vault Steward · 2026-10-15 (append-only · §14 42 + §15 20 + §16 12 + §17 10 + §18 12 = 96 kayıt)
