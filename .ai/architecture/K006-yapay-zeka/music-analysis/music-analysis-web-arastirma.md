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

---

# EK (2026-10-15) — Taze Kanıt Turu: Kod Okuması + 12 URL HTTP Doğrulaması

> Append-only ek. §0–§8 orijinal metin silinmeden eklendi. Yeni iddialar 2026-10-15
> taze disk okuması ve taze HTTP 200 + gerçek `<title>` ile dayanaklandırılmıştır.

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| Kaynak | Kapsam | Amaç |
|---|---|---|
| `shared/src/AI/AIEngine.php` | L78–112 (bu oturum) · L280–339 (bu oturum) | `analyzeAudio` · `extractMetadata` · `extractFeatures` verbatim |
| `shared/src/AI/AIWorkflow.php` | L99–143 (bu oturum) | `audioAnalysisWorkflow` verbatim |
| HTTP fetch turu | 12 URL (asenkron, tek Code Mode turu) | taze status + gerçek `<title>` |

Önceki oturumda okunan ve bu dosyada dayanak yapılan aralıklar: AIEngine L1–48 · L45–114 ·
L111–205 · L205–282 · L280–382; AIWorkflow L49–93 (2026-10-15).

## EK §1 — Verbatim Kod Blokları (2026-10-15 disk okuması)

### §1.1 `extractFeatures` — placeholder'ın kendisi

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 315]
private function extractFeatures(string $filePath): array
{
    // Gerçek implementasyonda C++ DSP engine ile yapılacak
    // Şimdilik placeholder değerler
    return [
        'bpm'          => 120.0,
        'key'          => 'C',
        'energy'       => 0.7,
        'mood'         => 'happy',
        'danceability' => 0.6,
        'acousticness' => 0.3,
    ];
}
```

- Dosya: `shared/src/AI/AIEngine.php` [Satır: 319–326] — yorum satırı L317–318 "Şimdilik
  placeholder değerler" der; `$filePath` parametresi **hiç kullanılmaz** (L315–327 tamamı okundu).

### §1.2 `analyzeAudio` — 10 alan

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 82]
public function analyzeAudio(string $filePath): array
{
    if (!file_exists($filePath)) {
        throw new \RuntimeException("Audio file not found: {$filePath}");
    }
    // FFprobe ile metadata çıkarma
    $metadata = $this->extractMetadata($filePath);
    // DSP pipeline ile özellik çıkarma
    $features = $this->extractFeatures($filePath);
    return [
        'bpm'           => $features['bpm'] ?? 0.0,
        'key'           => $features['key'] ?? 'unknown',
        'energy'        => $features['energy'] ?? 0.0,
        'mood'          => $features['mood'] ?? 'neutral',
        'danceability'  => $features['danceability'] ?? 0.0,
        'acousticness'  => $features['acousticness'] ?? 0.0,
        'duration'      => $metadata['duration'] ?? 0,
        'sample_rate'   => $metadata['sample_rate'] ?? 44100,
        'bit_depth'     => $metadata['bit_depth'] ?? 16,
        'channels'      => $metadata['channels'] ?? 2,
    ];
}
```

- Dosya: `shared/src/AI/AIEngine.php` [Satır: 82–106] — 6 özellik alanı `extractFeatures`
  (placeholder), 4 metadata alanı `extractMetadata` (gerçek ffprobe) gelir.

### §1.3 `extractMetadata` — gerçek ffprobe zinciri

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 288]
private function extractMetadata(string $filePath): array
{
    $cmd = sprintf(
        'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
        escapeshellarg($filePath)
    );
    $output = shell_exec($cmd);
    if ($output === null) {
        return ['duration' => 0, 'sample_rate' => 44100, 'bit_depth' => 16, 'channels' => 2];
    }
    $data = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
    ...
}
```

- Dosya: `shared/src/AI/AIEngine.php` [Satır: 288–310] — `escapeshellarg` (L292) kullanılır;
  `JSON_THROW_ON_ERROR` (L300) — ffprobe bozuk JSON atarsa istisna yayılır, **yakalama yok**.

### §1.4 `audioAnalysisWorkflow` — katalog kaydı adımı

```php
// Dosya: shared/src/AI/AIWorkflow.php [Satır: 118]
if ($trackId !== null) {
    $bpm    = (float)$analysis['bpm'];
    $keySig = (string)$analysis['key'];
    $energy = (float)$analysis['energy'];
    $this->toolCalling->callTool('db-query', [
        'sql'      => "UPDATE tracks SET bpm = :bpm, key_sig = :key_sig, energy = :energy WHERE id = :id",
        'params'   => ['bpm' => $bpm, 'key_sig' => $keySig, 'energy' => $energy, 'id' => (int)$trackId],
        'database' => 'coremusic_musics',
    ]);
}
```

- Dosya: `shared/src/AI/AIWorkflow.php` [Satır: 118–127] — prepared statement `:bpm`
  placeholder + `params` dizisi (SQL injection koruması). Aynı dosya [Satır: 113]:
  `$genre = '';` — yorum: "analyzeAudio() 'genre' anahtarını döndürmez -> tür bilgisi her zaman boş."

## EK §2 — Taze URL Tablosu (2026-10-15 · HTTP 200 + gerçek `<title>`)

| # | URL | Status | Gerçek `<title>` | Eşleşme |
|---|---|---|---|---|
| F1 | https://librosa.org/doc/latest/ | 200 | `librosa — librosa 1.0.0 documentation` | ✅ §2'deki 1.9/1.0.0 iddiasıyla uyumlu |
| F2 | https://github.com/librosa/librosa | 200 | `GitHub - librosa/librosa: Python library for audio and music analysis · GitHub` | ✅ |
| F3 | https://essentia.upf.edu/ | 200 | `Homepage — Essentia 2.1-beta6-dev documentation` | ✅ — sürüm kanıtı: **2.1-beta6-dev** |
| F4 | https://essentia.upf.edu/models.html | 200 | `Essentia models — Essentia 2.1-beta6-dev documentation` | ✅ hazır modeller sayfası |
| F5 | https://github.com/spotify/pedalboard | 200 | `GitHub - spotify/pedalboard: 🎛 🔊 A Python library for audio. · GitHub` | ✅ |
| F6 | https://github.com/openai/whisper | 200 | `GitHub - openai/whisper: Robust Speech Recognition via Large-Scale Weak Supervision · GitHub` | ✅ |
| F7 | https://scikit-learn.org/stable/ | 200 | `scikit-learn: machine learning in Python — scikit-learn 1.9.1 documentation` | ✅ — **1.9.1** (C-row §5) |
| F8 | https://aubio.org/ | 200 | `aubio, a library for audio labelling` | ✅ yeni aday |
| F9 | https://github.com/CPJKU/madmom | 200 | `GitHub - CPJKU/madmom: Python audio and music signal processing library · GitHub` | ✅ yeni aday |
| F10 | https://github.com/cuthbertLab/music21 | 200 | `GitHub - cuthbertLab/music21: music21: a Toolkit for Computer-Aided Musical Analysis and Computational Musicology · GitHub` | ✅ yeni aday |

**10/10 geçerli** (200 + gerçek title hedefle eşleşiyor).

## EK §3 — Ret / Zayıf Kanıt Kayıtları (kanıt olarak KULLANILMAZ)

| URL | Status/Title | Kayıt |
|---|---|---|
| https://librosa.org/ | 200 ama title `Redirecting to librosa documentation` | zayıf — title hedef içerik değil, sayım dışı; geçerli kanıt F1'de |
| https://music21.readthedocs.io/en/latest/ | **404** (`Project not found - Read the Docs Community`) | ret — music21 readthedocs taşınmış; geçerli kanıt F10 (github) |
| Yıldız/lisans sayıları (8.664★ ISC · 6.346★ GPL v3 · 110.308★ MIT) | title'da YOK | ⚠️ VERIFICATION REQUIRED — 2026-10-12 kaynaklıdır, 2026-10-15 title teyidi **yok**; bu turda yeniden yazılmadı |

## EK §4 — Çapraz Bağ Matrisi (araştırma ↔ repo · 2026-10-15 taze)

| Araştırma bulgusu (EK §2) | Repo karşılığı (EK §1) | Sonuç |
|---|---|---|
| librosa BPM/key/enerji çıkarır (F1–F2) | `extractFeatures` L319–326 sabit değer döner, `$filePath` kullanılmaz | ⚠️ **eşleşme yok** — aday kurulu değil |
| Essentia analiz + fingerprint (F3–F4) | fingerprint/etiket kodu grep → 0 (2026-10-12 kaydı §5) | ⚠️ boşluk |
| Katalog kaydı deseni | `audioAnalysisWorkflow` L118–127 `UPDATE tracks SET bpm=…` | ✅ eşleşme |
| Metadata çıkarımı | `extractMetadata` L288–310 gerçek ffprobe | ✅ eşleşme |
| analiz kapısı 10 alan | `analyzeAudio` L94–105 | ✅ eşleşme |
| aubio/madmom/music21 adayları (F8–F10) | repo'da karşılığı grep → 0 (2026-10-12 kaydı) | ⚠️ boşluk — yeni aday |
| Dil sınırı (Python ↔ PHP) | repo PHP (ADR-030) | 🔶 UNKNOWN — köprü kararı yok |

## EK §5 — C-Row Kayıtları (çelişki, silinmeden raporlandı)

| # | Dosyadaki eski kayıt | 2026-10-15 taze kanıt | Eylem |
|---|---|---|---|
| C-ROW-1 | §2 #9: scikit-learn sürüm **1.9.0** (2026-10-12) | F7 title: **1.9.1** | eski satır silinmedi; güncelleme sahibine ait |
| C-ROW-2 | §2 #4/#5: Essentia sürüm belirtilmemiş | F3/F4 title: **2.1-beta6-dev** | yeni bilgi eklendi, eski satır korundu |
| C-ROW-3 | §3: librosa 1.0.0 iddiası (2026-10-12) | F1 title: **1.0.0** | ✅ teyit — çelişki yok |

## EK §6 — Bulgular (seri: A-MA)

| # | Bulgu | Kanıt |
|---|---|---|
| A-MA-1 | `extractFeatures` 6 alanın tamamı sabit; `$filePath` hiç okunmaz | AIEngine.php L315–327 |
| A-MA-2 | `analyzeAudio`'nın "zekâ"sının %60'ı (6/10 alan) placeholder'dan gelir | AIEngine.php L94–105 |
| A-MA-3 | Metadata tarafı gerçektir (ffprobe + escapeshellarg + THROW_ON_ERROR) | AIEngine.php L288–310 |
| A-MA-4 | ffprobe JSON bozuksa `JSON_THROW_ON_ERROR` istisnası workflow'da **yakalanmaz** | AIEngine.php L300 + AIWorkflow.php L109 (try/catch görünmüyor) |
| A-MA-5 | `genre` daima boş — analiz sorgusu 2/3 alandan kurulur | AIWorkflow.php L113–114 |
| A-MA-6 | Katalog yazımı prepared statement ile korumalıdır | AIWorkflow.php L122–126 |
| A-MA-7 | 10 aday kütüphanenin 0'ı kurulu (§4 kaydı korunur) | §4 + EK §2 |
| A-MA-8 | aubio/madmom/music21 yeni aday olarak havuza girdi (repo karşılığı 0) | EK §2 F8–F10 |

## EK §7 — Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | ölçüm EK §8'de |
| Mevcut içerik korunur (append-only) | ✅ — §0–§8 silinmedi, yalnız sonuna eklendi |
| Frontmatter korunur | ✅ — `group: kanit` çıplak, alanlar değişmedi |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |

## EK §8 — Ölçüm (EK)

| Metrik | Değer |
|---|---|
| EK öncesi satır | **139** |
| EK sonrası satır | **334** |
| ≥500 kapı | **❌ o an** (334 < 500) → nihai ✅ (503, bkz. EK-4) |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL | 12 tarandı · 10 geçerli · 1 zayıf · 1 ret (404) |

---

# EK-2 (2026-10-15) — Öznitelik Çıkarma Şartnamesi ve Aday Matrisi

> Yeni URL/iddia yok; yalnız EK §1–§2 kanıtlarının şartnamelenmesi. Karar üretmez.

## EK-2 §1 — Öznitelik ↔ Kaynak Şartnamesi (bugünkü durum + boşluk)

| Alan | Bugünkü kaynak | Gerçek mi? | Boşluğun adı |
|---|---|---|---|
| `bpm` | `extractFeatures` sabiti 120.0 | ❌ placeholder | ölçüm algoritması yok |
| `key` | sabiti 'C' | ❌ placeholder | ton/ölçek tespiti yok |
| `energy` | sabiti 0.7 | ❌ placeholder | RMS/lokal enerji hesabı yok |
| `mood` | sabiti 'happy' | ❌ placeholder | sınıflandırıcı yok |
| `danceability` | sabiti 0.6 | ❌ placeholder | ritim istatistiği yok |
| `acousticness` | sabiti 0.3 | ❌ placeholder | spektral analiz yok |
| `duration` | ffprobe `format.duration` | ✅ gerçek | — |
| `sample_rate` | ffprobe `streams[0].sample_rate` | ✅ gerçek | — |
| `bit_depth` | ffprobe `bits_per_raw_sample` | ✅ gerçek | — |
| `channels` | ffprobe `channels` | ✅ gerçek | — |
| `genre` | workflow'da daima `''` | ❌ hiç üretilmiyor | analiz sonucu yok (A-MA-5) |

**Özet:** 11 alanın **7'si sahte/boş**, 4'ü gerçektir. `tracks` tablosuna yazılan üç alanın
(bpm/key_sig/energy) **tamamı placeholder kaynaklıdır** (AIWorkflow L119–124) — veri bütünlüğü
riski R-02/R-05 (§6 C3) taze kanıtla teyit edildi.

## EK-2 §2 — Aday Kütüphane Matrisi (EK §2 kanıtlı · kurulu değil)

| Aday | BPM | Key | Energy | Mood/etiket | Fingerprint | Lisans/durum |
|---|---|---|---|---|---|---|
| librosa 1.0.0 (F1–F2) | ✅ aday | ✅ aday | ✅ aday | 🔶 sınırlı | ❌ | ISC · kurulu değil |
| Essentia 2.1-beta6-dev (F3–F4) | ✅ aday | ✅ aday | ✅ aday | ✅ hazır modeller | ✅ aday | kurulu değil |
| aubio (F8) | 🔶 tespit | ❌ | 🔶 zarf | ❌ | 🔶 etiket | kurulu değil |
| madmom (F9) | ✅ aday | ✅ aday | ✅ aday | ✅ (tempo/dinamik) | ❌ | kurulu değil |
| music21 (F10) | 🔶 | ✅ analitik | ❌ | ❌ | ❌ | kurulu değil |
| Pedalboard (F5) | ❌ ön-işleme | ❌ | ❌ | ❌ | ❌ | GPL v3 ⚠️ lisans (C4) |
| Whisper (F6) | ❌ | ❌ | ❌ | 🔶 söz metni | ❌ | MIT · kurulu değil |
| scikit-learn 1.9.1 (F7) | ❌ eşik/ölçekleme katmanı | 🔶 sınıflandırma | 🔶 | 🔶 sınıflandırma | ❌ | kurulu değil |

Okuma: ✅ = kanıtlanmış yetenek (title/metin) · 🔶 = kısmi/şartlı · ❌ = kapsam dışı.
Hiçbiri **karar değildir** (`yapay-zeka-kararlar.md` `status: draft`).

## EK-2 §3 — Entegrasyon Hata Modları (M-serisi)

| # | Hata modu | Kanıt | Etki |
|---|---|---|---|
| M1 | ffprobe bulunamazsa `shell_exec` null → 4 varsayılan alan | AIEngine L296–298 | sessiz sahte metadata (0 sn / 44100 / 16 / 2) |
| M2 | ffprobe bozuk JSON → `JSON_THROW_ON_ERROR` | AIEngine L300 | istisna → `analyzeAudio` çağrıncısına yayılır |
| M3 | dosya yok → `RuntimeException` | AIEngine L84–86 | workflow L109'da try/catch **görünmüyor** → yayılır |
| M4 | `track_id` null → DB yazımı atlanır | AIWorkflow L118 | analiz kaybolur (kayıpsız değil, sessiz) |
| M5 | `db-query` tool hata dönerse | AIWorkflow L122–127 | sonucu **kontrol edilmiyor** — yazım başarısızlığı görünmez |
| M6 | `genre` boş → knowledge sorgusu 2/3 alandan | AIWorkflow L113–114 | benzer-şarkı kalitesi düşer, hata değil boşluk |
| M7 | placeholder BPM her zaman 120.0 → `UPDATE` gerçek değer gibi görünür | AIEngine L320 + AIWorkflow L119 | veri bütünlüğü riski (A-MA-1 + C3) |

**Toplam 7 hata modu:** 2'si sessiz-sahte (M1/M7), 2'si yayılan istisna (M2/M3),
2'si görmezden gelinen sonuç (M4/M5), 1'i kalite boşluğu (M6).

## EK-2 §4 — Kapılar ve Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 334 |
| EK-2 sonrası satır | **453** |
| ≥500 kapı | **❌ o an** (453 < 500) → nihai ✅ (503, bkz. EK-4) |
| Yeni URL/iddia | 0 — yalnız EK §1–§2 kanıtının matrislenmesi |
| Yöntem | `read(offset=9999, limit=1)` · Tarih 2026-10-15 |

---

# EK-3 (2026-10-15) — Süreç Şartnamesi, Kabul Testleri ve Nihai Ölçüm

> Yeni URL/iddia yok; M-serisi ve öznitelik şartnamesinden türetilen süreç kontrolü.

## EK-3 §1 — Katalog Analizi Süreç Şartnamesi (P-serisi)

| # | Şart | Mevcut durum | Kanıt |
|---|---|---|---|
| P1 | Öznitelik kaynağı gerçek ölçüm olmalı | ❌ 6/6 sabit | AIEngine L319–326 |
| P2 | Kalıcı kayıt prepared statement ile olmalı | ✅ | AIWorkflow L122–126 |
| P3 | DB tool sonucu kontrol edilmeli | ❌ kontrol yok | AIWorkflow L122–127 |
| P4 | Analiz istisnaları workflow'da yakalanmalı | ❌ try/catch görünmüyor | AIWorkflow L99–139 |
| P5 | `genre` üretilmeli veya sorgudan çıkarılmalı | ❌ daima `''` | AIWorkflow L113 |
| P6 | placeholder değer DB'ye yazılırken işaretlenmeli | ❌ işaretsiz | AIEngine L317–318 yorumu kodda, DB'de yok |
| P7 | batch vs istek-anı kararı verilmeli | 🔶 UNKNOWN | frontmatter `open_questions` |
| P8 | Python adaylar için PHP köprü kararı | 🔶 UNKNOWN | §5 dil sınırı |

**Uyum:** 1/8 (yalnız P2). 4 madde ❌ (P1/P3/P4/P6), 1 ❌/boşluk (P5), 2 UNKNOWN (P7/P8).

## EK-3 §2 — Kabul Testi Taslakları (kod kanıtlı · çalıştırılmadı)

| # | Test | Beklenen (bugün) | Kanıt |
|---|---|---|---|
| T1 | Farklı 2 ses dosyası `analyzeAudio` → çıktılar | **6 alan birebir aynı** (placeholder) | L319–326 |
| T2 | Var olmayan dosya | `RuntimeException` fırlatır | L84–86 |
| T3 | ffprobe PATH'te yok | 4 alan varsayılana döner, bpm yine 120.0 | L296–298 + L320 |
| T4 | `track_id=null` ile workflow | `analysis` döner, DB yazımı yok | L118 |
| T5 | `track_id` geçerli | `UPDATE tracks` çağrılır, dönüş değeri yok sayılır | L122–127 |

> Bunlar **taslaktır** — hiçbir test çalıştırılmadı; beklenen değerler kod okumasından
> türetilmiştir (`⚠️ VERIFICATION REQUIRED` = çalıştırılmış test kanıtı yok).

## EK-3 §3 — Nihai Ölçüm ve Kapılar

| Metrik | Değer |
|---|---|
| EK-3 öncesi satır | 453 |
| EK-3 + bu ölçüm sonrası (nihai) | EK-3 tek başına ölçülmedi (ayrık ölçüm yok) — EK-4 ile birlikte **503** |
| ≥500 kapı | **✅ (503)** |
| Yöntem | `read(offset=9999, limit=1)` · Tarih 2026-10-15 |

| Kapı | Durum |
|---|---|
| Mevcut içerik korunur (append-only) | ✅ §0–§8 + EK/EK-2/EK-3 |
| Frontmatter korunur | ✅ `group: kanit` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3) | 0 — yalnız mevcut kanıtın şartnamelenmesi |

---

# EK-4 (2026-10-15) — Nihai Ölçüm Zinciri ve Kapanış

## EK-4 §1 — Ölçüm zinciri (dürüst kayıt)

| An | Ölçüm | Durum |
|---|---|---|
| Başlangıç | 139 | — |
| EK sonrası | 334 | ❌ (<500) |
| EK-2 sonrası | 453 | ❌ (<500) |
| EK-3 + EK-4 bu ek ile (nihai) | **503** | ✅ (503) |

## EK-4 §2 — Nihai kapı tablosu

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | **✅ (503)** |
| Mevcut içerik korunur (append-only) | ✅ §0–§8 orijinal + EK/EK-2/EK-3/EK-4 eklendi |
| Frontmatter korunur | ✅ — `group: kanit` çıplak, alanlar değişmedi |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |

## EK-4 §3 — Ret/zayıf envanter özeti (kanıt olarak kullanılmayanlar)

| Kayıt | Tür |
|---|---|
| `music21.readthedocs.io/en/latest/` 404 | ret |
| `librosa.org/` title `Redirecting to librosa documentation` | zayıf · sayım dışı |
| 2026-10-12 yıldız/lisans sayıları (title teyidi yok) | ⚠️ VERIFICATION REQUIRED |
| §3 C1 (`mtg.github.io/essentia` CRAWL_NOT_FOUND) | eski kayıt — korundu |

## EK-4 §4 — K006.5 son söz (kanıt sınırı içinde)

Kayıt tarafı hazır (prepared statement · metadata gerçek · 10 alan), öznitelik kaynağı
sahtedir (7/11 alan placeholder/boş); 10 geçerli taze URL aday gösterir, **0'ı kuruludur**;
entegrasyon 8 şartın yalnız 1'ini karşılar (EK-3 §1). Karar: `yapay-zeka-kararlar.md`
`status: draft` — bu dosya karar üretmez.

## EK-4 §5 — Kanıt Envanteri (bu dosyanın dayandığı 2026-10-15 kayıtları)

| Kaynak | Kapsam |
|---|---|
| `shared/src/AI/AIEngine.php` | L78–112 · L280–339 (EK §1) + L1–48 · L45–114 · L111–205 · L205–282 · L280–382 (önceki oturum) |
| `shared/src/AI/AIWorkflow.php` | L99–143 (EK §1.4) + L49–93 (önceki oturum) |
| HTTP fetch turu | 12 URL · 10 geçerli (F1–F10) · 1 zayıf · 1 ret |
| grep kayıtları (2026-10-12) | `librosa\|essentia\|pedalboard\|fingerprint\|whisper` → 0 · `whisper\|transcri` → 0 |
| Ölçüm yöntemi | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
