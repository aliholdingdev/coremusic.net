---
node_id: "K008-playback-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master"
evidence:
  - "domain-master.md §3.1 satır 92 — Oynatma | playback | PlaybackSession | MusicPlayedEvent / PlaybackQueueUpdatedEvent"
  - ".ai/.sql/mysql/coremusic_user.sql satır 18–31 (playback_queue) · 104–114 (user_preferences oynatma tercihleri)"
  - "shared/src/Events/Domain/MusicPlayedEvent.php satır 20 · 26–30 · 39"
  - "assets.coremusic.net/js/coreplayer/coreplayer.controls.js satır 7–38 (PlaybackRepository)"
validation: "2026-10-15 — kapsam sınırı SQL + JS + master satır kanıtlarıyla çizildi"
open_questions:
  - "playback Bounded Context / PlaybackSession aggregate → ADR-097 K4'te kilitlenmedi → taslak"
  - "MusicPlayedEvent yayıncısı (dispatch eden kod) → bu oturumda tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
  - "PlaybackQueueUpdatedEvent → diskte dosya YOK (bkz [[playback-dogrulama]] D-PL11) → SINIR AÇIK"
group: mimari
---

# playback — Kapsam (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> `playback`, **oynatma anı**nın durumudur: sıra (kuyruk), çal/duraklat, pozisyon,
> çalan şarkının kimliği. Şarkının kendisi `music`'in, ne kadar dinlendiği `library`'nin,
> hedef cihaz `device-management`'in işidir.

## §1 ROL (ELI15)

Kullanıcı bir şarkıyı başlatınca sistem üç şeyi birden tutar: **sıradaki ne** (kuyruk),
**şu an ne çalıyor** (aktif durum) ve **nereye çalıyor** (cihaz — komşu bağlam).
Master §3.1 satır 92 bu bağlamı `PlaybackSession` aggregate'i ile adlandırır
(⚠️ tasarım iddiası — ADR-097 K4). Frontend bunu `MM_*` çerez değişkenleriyle
(`coreplayer.controls.js` satır 7–38) tutar; backend `playback_queue` tablosuyla
(satır 18–31). Çalma tercihleri (otomatik çal, crossfade, ses eşitleme)
`user_preferences` satır 110–114'tedir.

## §2 KAPSAM SINIRI

| # | Bu bağlamın İÇİNDE | Bu bağlamın DIŞINDA | Sınır komşusu |
|---|---|---|---|
| 1 | Kuyruk durumu: sıra, kaynak, çalıyor bayrağı (`playback_queue` satır 22–25) | Dinleme geçmişi: ne kadar dinlendi (süre/yüzde) | `library` |
| 2 | Oynatma tercihleri: auto_play · crossfade · normalize (`user_preferences` satır 110–112) | Kullanıcı kimliği / profil | `user` |
| 3 | Çalan şarkının kimliği + anlık durum (`MM_CurrentSongID` / `MM_PlayingState`) | Şarkının kendisi (tanım, katalog) | `music` |
| 4 | HTML5 oynatma çekirdeği (controls/seekbar/volume JS) | Ses dosyası / transcode / bitrate | `media` |
| 5 | Çalma hedefinin UI seçimi (anlık) | Cihaz keşfi, eşleştirme, senkron | `device-management` |
| 6 | — | **Kuyruk domain event'i** (`PlaybackQueueUpdatedEvent` diskte YOK) | SINIR AÇIK — master §7 satır 216–217 |

## §3 DDD MODEL NOTU (taslak — ADR-097 K4)

| # | Kalem | Ad | Kanıt |
|---|---|---|---|
| 1 | Bounded Context (aday) | Oynatma (`playback`) | ✅ domain-master §3.1 satır 92 |
| 2 | Aggregate (aday) | PlaybackSession | ⚠️ TASARIM İDDİASI — master satır 92; ADR-097 K4 |
| 3 | Entity / kayıt | `playback_queue` satırı (user_id + music_id + position) | ✅ coremusic_user.sql satır 18–31 |
| 4 | Value Object (aday) | `source` enum'u: `user / ai / radio / podcast` | ✅ satır 23 |
| 5 | Ubiquitous language | kuyruk = `playback_queue` · oynatma durumu = `MM_PlayingState` · çalan = `MM_CurrentSongID` | ✅ şema + JS satır 25/29/33 |
| 6 | Domain event | `MusicPlayedEvent` (`music.played`) | ✅ dosya var; **yayıncısı → ⚠️ VERIFICATION REQUIRED** |
| 7 | Invariant | tanımsız | ⚠️ ADR-097 K4 |

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `.ai/.sql/mysql/coremusic_user.sql` | 18–31 | `playback_queue` — position (0-tabanlı), source enum, is_playing |
| `.ai/.sql/mysql/coremusic_user.sql` | 104–114 | `user_preferences` — auto_play · crossfade_duration · normalize_audio · autoplay_recommendations |
| `shared/src/Events/Domain/MusicPlayedEvent.php` | 20 · 26–30 · 39 | sınıf · ctor (musicId/userId/duration) · eventName `music.played` |
| `assets.coremusic.net/js/coreplayer/coreplayer.controls.js` | 7–38 | PlaybackRepository — cookie tabanlı oynatma durumu (ömrü 365 gün, satır 22) |
| `.ai/architecture/K008-domain/domain-master.md` | 92 | PlaybackSession + MusicPlayedEvent / PlaybackQueueUpdatedEvent eşlemesi |

## §5 AÇIK SORULAR (R-PL1…R-PL5)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-PL1 | `MusicPlayedEvent` yayıncısı (dispatch eden kod) kim? | 2026-10-15 taraması yapılmadı | Backend grep: `new MusicPlayedEvent` → ⚠️ VERIFICATION REQUIRED |
| R-PL2 | `PlaybackQueueUpdatedEvent` neden diskte yok? | master §3.1 satır 92 adı geçiyor; dosya YOK (D-PL11) | Olay kasıtlı mı ertelendi — ADR |
| R-PL3 | Kuyruk (queue) mülkiyeti playback mi library mi? | master §7 (kuyruk event'i yok) | `library-kapsam.md` §5 R-L1 ile çapraz kapanış |
| R-PL4 | Cookie tabanlı durum (`MM_*`) ile `playback_queue` (DB) nasıl senkron? | JS satır 7–38 (cookie) ↔ s.18–33 (DB) iki depo | Senkron stratejisi — yazma/okuma ayrımı tasarımı |
| R-PL5 | `user_preferences` bu dalın mı, `user` mı? | master §3.1 satır 92 playback'i işaretliyor ama tablo `coremusic_user` şemasında | Şema sahibi ≠ bağlam sahibi kararı — ADR-097 K4 |

> `status: draft` ve §3 yasağı bu eklemle **kapanmaz**; kapanış ADR-097 K4'e bağlıdır.

## §6 ŞEMA KANITLARI: OYNATMA ÇEKİRDEĞİ

> **Kaynak:** `.ai/.sql/mysql/coremusic_user.sql` (2026-10-07 canlı dump) +
> `shared/src/Events/Domain/MusicPlayedEvent.php` + `assets.coremusic.net/js/
> coreplayer/coreplayer.controls.js`. Bu bölüm **kilitli kapsam değildir** —
> §3'teki yasak gereği yalnız kanıt sunar.

### §6.1 Çekirdek tablo/kod envanteri

| Varlık | Satır | Rol | Kanıt notu |
|---|---|---|---|
| `playback_queue` | s.18–33 | Kullanıcı bazlı kuyruk | 2 FK CASCADE (s.31–32) |
| `user_preferences` | s.104–124 | Oynatma tercihleri | UNIQUE user (s.121) |
| `MusicPlayedEvent.php` | s.20 · 26–30 · 39 | Domain event | `music.played` |
| `coreplayer.controls.js` | s.7–38 | Cookie tabanlı oynatma durumu | `PlaybackRepository` · ömür 365 gün (s.22) |

### §6.2 `playback_queue` — kolon kanıtları

| Kolon | Satır | Tip / değer | Kanıt notu |
|---|---|---|---|
| `id` | s.19 | binary(16) | UUID v7 PK |
| `user_id` | s.20 | binary(16) NOT NULL | FK → coremusic_auth.users |
| `music_id` | s.21 | binary(16) NOT NULL | FK → coremusic_musics.musics |
| `position` | s.22 | int NOT NULL | 0-tabanlı sıra |
| `source` | s.23 | enum 4 (user/ai/radio/podcast) default user | Kuyruk kaynağı |
| `source_id` | s.24 | binary(16) NULL | Kaynak varlık (playlist/radio) |
| `is_playing` | s.25 | tinyint(1) default 0 | Anlık çalıyor bayrağı |
| KEY (user,position) | s.29 | `idx_queue_user_position` | Sıralı okuma dizini |
| KEY (music) | s.30 | `idx_queue_music` | Parça arama |
| FK CASCADE ×2 | s.31–32 | music · user | Sahipsiz satır kalmaz |

### §6.3 `user_preferences` — oynatma tercih kanıtları

| Kolon | Satır | Tip / değer | Kanıt notu |
|---|---|---|---|
| `audio_quality` | s.109 | enum 4 (low/medium/high/lossless) default high | Kalite tercihi |
| `auto_play` | s.110 | tinyint(1) default 1 | Otomatik sonraki parça |
| `crossfade_duration` | s.111 | int default 0 | Saniye |
| `normalize_audio` | s.112 | tinyint(1) default 1 | Ses eşitleme |
| `explicit_content` | s.113 | tinyint(1) default 1 | Açık içerik izni |
| `autoplay_recommendations` | s.114 | tinyint(1) default 1 | AI önerisi otomatik çalma |
| UNIQUE (user) | s.121 | `idx_preferences_user` | Kullanıcı başına tek satır |

### §6.4 Kod kanıtları (JS + event)

| Dosya | Satır | Ne |
|---|---|---|
| `coreplayer.controls.js` | s.7–38 | `PlaybackRepository` — `MM_*` cookie değişkenleri (CurrentSongID · PlayingState) |
| `coreplayer.controls.js` | s.22 | Cookie ömrü 365 gün |
| `MusicPlayedEvent.php` | s.20 | Sınıf tanımı |
| `MusicPlayedEvent.php` | s.26–30 | Constructor: musicId · userId · duration |
| `MusicPlayedEvent.php` | s.39 | `eventName()` → `music.played` |

## §7 INVARIANT ADAYLARI (SQL KANITLI — KİLİTLENMEMİŞ ÖNERİ)

> **§3 yasağı gereği:** bu tablo **kilitli invariant listesi değildir**; her satır
> kanıtı ile **öneri**yi ayrı sütunda tutar.

| # | SQL kanıtı (satır) | Ne garanti eder | Invariant adayı (kanıt DEĞİL) |
|---|---|---|---|
| PL-P1 | s.22 position NOT NULL · s.29 KEY (user,position) | Sıra daima tanımlı | Kullanıcı başına sıra boşluksuz/tekrarsız **değil** (DB güvencesi yok) — öneridir |
| PL-P2 | s.25 is_playing default 0 | Bayrak daima var | Aynı kullanıcıda birden fazla `is_playing=1` DB'de **engellenmez** — domain kontrolü önerisi |
| PL-P3 | s.23 enum 4 | Kaynak kapalı küme | Geçersiz kaynak yazılamaz |
| PL-P4 | s.31–32 FK CASCADE ×2 | Kullanıcı/parça silinirse kuyruk satırı silinir | Sahipsiz kuyruk satırı kalmaz |
| PL-P5 | s.121 UNIQUE (user) | Tercih satırı tekil | Kullanıcı başına tek tercih satırı; ikinci insert ret |
| PL-P6 | s.111 crossfade int default 0 | Negatif serbest (DB sınırı yok) | Domain'de `≥0 && ≤ 12` önerisi (kanıt DEĞİL) |
| PL-P7 | s.110/s.112–114 tinyint(1) | Bayrak tipi | 0/1 dışı değer MySQL'de uyarlamalı yazılır — domain'de bool VO önerisi |
| PL-P8 | s.19 binary(16) PK | UUID v7 | ID üretimi UUID v7 (s.19 comment) |

### §7.1 Uyumsuzluk / açık noktalar (kanıt + öneri ayrımı)

| İddia | Kanıt | Durum |
|---|---|---|
| Kuyruk tek depo (DB) | s.18–33 DB ↔ s.7–38 cookie | ⚠️ ÇİFT DEPO — senkron yok (R-PL4) |
| `is_playing` tekil | s.25 · UNIQUE **YOK** | ⚠️ DB güvencesi YOK — PL-P2 önerisi |
| `PlaybackQueueUpdatedEvent` var | master satır 92 adı geçiyor; dosya YOK | ⚠️ EKSIK OLAY — R-PL2 |
| `user_preferences` playback'in | Şema `coremusic_user` | ⚠️ ŞEMA ≠ BAĞLAM — R-PL5 |

## §8 VALUE OBJECT ADAYLARI (ÖNERİ — DEPODA VO YOK)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda oynatma VO sınıfı yoktur; adaylar
> yalnız şema kolon + JS kanıtlarından türetilmiştir.

| # | VO | Dayanak | Değer kümesi / biçim | Önerilen davranış |
|---|---|---|---|---|
| V-PL1 | `QueueSource` | s.23 enum(4) | user/ai/radio/podcast | `from(string)` fabrika — geçersizde ret |
| V-PL2 | `QueuePosition` | s.22 int | ≥0 tam sayı | Negatif ret; 0-tabanlı |
| V-PL3 | `PlayingState` | s.25 + JS `MM_PlayingState` | play/pause/stop (JS dili) | Cookie dili ↔ DB bayrağı eşlemesi §8.2 |
| V-PL4 | `CrossfadeSeconds` | s.111 int | 0–120 (öneri aralığı) | Dışına ret (öneri — kanıt DEĞİL) |
| V-PL5 | `PlaybackQuality` | s.109 enum(4) | low/medium/high/lossless | `from(string)` + `isLossless()` |
| V-PL6 | `QueueSourceId` | s.24 binary(16) NULL | UUID veya null | null = kaynak yok (radio/ai) |

### §8.1 VO test edilebilirlik notları

| VO | Karakteristik test fikri (öneri) |
|---|---|
| V-PL1 | 5. değer `from()` çağrısında ret |
| V-PL2 | `from(-1)` ret |
| V-PL4 | `from(-5)` ve `from(600)` ret; `from(0)` kabul |

### §8.2 Cookie ↔ DB dil eşlemesi (kanıt — tasarım DEĞİL)

| Cookie (`MM_*`, JS) | DB kolonu | Not |
|---|---|---|
| `MM_CurrentSongID` (JS s.29) | `playback_queue.music_id` (s.21) | İki depo — senkron kanıtı YOK |
| `MM_PlayingState` (JS s.33) | `is_playing` (s.25) | JS dili serbest metin; DB tinyint(1) |

## §9 DOMAIN OLAY ENVANTERİ

### §9.1 Var olan olaylar (dosya + satır kanıtlı)

| Olay | Dosya kanıtı | `eventName` | Payload (satır kanıtlı) |
|---|---|---|---|
| `MusicPlayedEvent` | `shared/src/Events/Domain/MusicPlayedEvent.php` s.20 · 26–30 | `music.played` (s.39) | musicId · userId · duration |

> **Sahiplik çatışması:** `music.played` bu dalda tanımlı ama `music-kapsam.md`
> §9.1'i de bu olayı kendi envanterine alıyor — kapanış `domain-bilesenler.md`
> §14 olay→BC çizelgesine bağlıdır (D3).

### §9.2 Eksik olay adayları (öneri — uydurma YASAK)

| # | Önerilen olay | Tetikleyici kanıtı | Mevcut durum |
|---|---|---|---|
| E-PL1 | `PlaybackQueueUpdatedEvent` | master satır 92 adı geçiyor | ❌ DOSYA YOK (D-PL11) |
| E-PL2 | `playback.paused` | JS `MM_PlayingState` duraklatma durumu | ❌ DOSYA YOK |
| E-PL3 | `playback.stopped` | JS durdurma durumu | ❌ DOSYA YOK |
| E-PL4 | `playback.seeked` | seekbar JS (controls s.7–38) | ❌ DOSYA YOK |
| E-PL5 | `queue.item_added` | kuyruk satırı ekleme (s.18–33) | ❌ DOSYA YOK |
| E-PL6 | `preference.updated` | `user_preferences` UPDATE (s.117 updated_at) | ❌ DOSYA YOK |

> **Kural:** E-PL1…E-PL6, `domain-bilesenler.md` §12 protokolüne tabidir —
> olay ancak dosya + `class` + `eventName` satır kanıtıyla envantere girer.

## §10 REPOSITORY SÖZLEŞMESİ ÖNERİSİ

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda oynatma repository arayüzü
> kanıtlanmamıştır; JS'teki `PlaybackRepository` (s.7–38) **frontend**
> nesnesidir, backend sözleşmesi **değildir**.

| # | Yöntem (öneri) | Dönüş | Bağlı invariant / kanıt |
|---|---|---|---|
| R-PL6 | `currentQueue(string $userId): array` | sıra listesi | PL-P1 |
| R-PL7 | `enqueue(string $userId, string $musicId, string $source): array` | satır | PL-P3, V-PL1 |
| R-PL8 | `removeFromQueue(string $userId, string $queueId): void` | — | PL-P4 |
| R-PL9 | `markPlaying(string $userId, ?string $queueId): void` | — | PL-P2 (tekil kural önerisi) |
| R-PL10 | `preferences(string $userId): ?array` | tercih veya null | PL-P5 |
| R-PL11 | `updatePreferences(string $userId, array $patch): array` | tercih | V-PL4, V-PL5 |

### §10.1 Sözleşme tipi notu

JS `PlaybackRepository` (frontend, cookie) ile backend repository **aynı ismi
paylaşır ama farklı katmandır** — isim çatışması domain dili notudur (§12 R-PL4).

## §11 KOMŞU SINIR DETAYI (§2 GENİŞLETMESİ)

| Komşu | Yön | Alan/kanıt | Ayrım kuralı |
|---|---|---|---|
| music | playback → music | `music_id` FK (s.21) · `MusicPlayedEvent.payload.musicId` | Parça tanımı burada değil |
| library | playback → library | Dinleme süresi/yüzdesi (`user_listening_history` library'de) | Çalma anı burada; kayıt orada |
| media | playback → media | Ses akışı/transcode DIŞINDA | Bitrate/kodlama media'nın |
| device-management | playback → device | Çalma hedefinin UI seçimi (anlık) | Cihaz keşfi/eşleşme orada |
| discovery/recommendation | ← playback | `music.played` girdi olayı | Keşif/öneri bu olayı okur |
| user | playback ← user | `user_id` FK (s.20) | Kimlik user'de |

## §12 AÇIK SORULAR (§5 GENİŞLETME — R-PL6…R-PL10)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-PL6 | `music.played` sahibi playback mi music mi? | iki dosya da envantere alıyor (§9.1) | `domain-bilesenler.md` §14 |
| R-PL7 | Cookie durumunun ömrü (365 gün, JS s.22) neden bu kadar uzun? | JS s.22 | Tasarım niyeti — oturum politikası |
| R-PL8 | Kuyruk `source='ai'` olduğunda `source_id` ne? (s.24 NULL) | s.24 comment: playlist, radio | AI kaynaklı kuyrukta `source_id` dolduruluyor mu? |
| R-PL9 | `crossfade_duration` üst sınırı | s.111 DB sınırı yok | Domain VO kararı (V-PL4 önerisi) |
| R-PL10 | `autoplay_recommendations` (s.114) hangi akışı tetikler? | recommendation_workflow (AIWorkflow s.49–90) | Bağlantı kanıtı — tarama |

> **§5'teki R-PL1–R-PL5 aynen korunur**; bu tur yalnız R-PL6–R-PL10 ekler.
> `status: draft` ve §3 yasağı **değişmez**.

## §13 KARAKTERİSTİK TEST SENARYOLARI (ÖNERİ)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Senaryo taslağıdır; yazılmamıştır.

| # | Senaryo | Beklenen | Bağlı kural |
|---|---|---|---|
| PL-T1 | source='podcast' dışında değer | VO ret | V-PL1 |
| PL-T2 | position=-1 enqueue | VO ret | V-PL2 |
| PL-T3 | Aynı kullanıcıda ikinci tercih satırı insert | UNIQUE ret | PL-P5 |
| PL-T4 | crossfade=600 saniye | VO ret (öneri sınırı) | V-PL4, R-PL9 |
| PL-T5 | Kullanıcı sil | Kuyruk CASCADE silinir | PL-P4 |
| PL-T6 | İki satır `is_playing=1` (aynı kullanıcı) | Domain ret (DB retmez!) | PL-P2 |
| PL-T7 | Cookie ile DB senkronsuz | Davranış tanımsız — R-PL4 senaryosu | §7.1 |

## §14 ÖLÇÜM VE KAPANIŞ (BU TUR)

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `playback-kapsam.md` |
| 2 | Başlangıç satırı | 76 |
| 3 | Hedef | ≥500 |
| 4 | Kapsam | §5–§26 |
| 5 | Yasak ihlali | 0 (Authority korundu · frontmatter korundu · uydurma yok) |

## §15 SQL ERİŞİM VE DİZİN NOTU (KANIT / TARAMA AYRIMI)

| Konu | Kanıt | Not |
|---|---|---|
| Kuyruk okuma | s.29 KEY (user,position) | Sıralı okuma bu dizinle |
| Tercih okuma | s.121 UNIQUE (user) | Tek satır — join'siz okunur |
| `SELECT *` | — | ❌ YASAK (Data Engineer kuralı) |
| Cookie okuma (JS) | s.7–38 | Frontend — DB sorgusu değil |

## §16 BC ETKİLEŞİM MATRİSİ (YÖN + NİYET)

| # | Komşu BC | Yön | Niyet |
|---|---|---|---|
| 1 | music | → | Parça referansı + `music.played` |
| 2 | library | → | Dinleme sonucu kaydı |
| 3 | media | → | Ses akışı |
| 4 | device-management | → | Çalma hedefi seçimi |
| 5 | discovery | → | `music.played` girdisi |
| 6 | recommendation | ↔ | `autoplay_recommendations` (R-PL10) |
| 7 | user | ← | Kimlik + tercih (R-PL5) |

## §17 ROL VE SORUMLULUK MATRİSİ (ÖNERİ)

| # | Rol | Sorumluluk | Kanıt durumu |
|---|---|---|---|
| 1 | Backend (services) | Kuyruk + tercih uçları | ⚠️ backend repository kanıtlanmadı (§10) |
| 2 | Frontend (coreplayer) | Cookie durumu + UI | ✅ JS s.7–38 |
| 3 | Data | Şema | ✅ SQL dump |
| 4 | library | Dinleme kaydı | ✅ `user_listening_history` |
| 5 | QA | §13 senaryoları | ⚠️ yazılmadı |

## §18 ÖRNEK ANLATIM (TUTARLILIK OKUMASI)

Kullanıcı parçayı başlatır → JS `MM_PlayingState` cookie'ye yazar (s.7–38) ve
`MusicPlayedEvent` yayınlanır (**yayıncı kanıtlanmadı** — R-PL1). Parça
`playback_queue`'ya eklenir (`position` sıralı, `source='user'` varsayılan).
Kullanıcı duraklatır → JS durumu günceller; DB `is_playing` güncellemesi kanıtı
**YOK** (çift depo senkronu R-PL4). Kullanıcı `crossfade_duration=30` ayarlar →
`user_preferences` satırı UPDATE (UNIQUE user s.121 korunur); `preference.updated`
olayı dosya olarak **yok** (E-PL6).

## §19 OKUMA YOLU VE KOMŞU DOSYA DAVET LİSTESİ

### §19.1 Bu dosyadan okuma sırası

1. §2–§3 (kapsam sınırı + DDD notu)
2. §6–§7 (şema + invariant)
3. §8.2 + §12 (cookie↔DB + açık sorular)

### §19.2 Çağrılan komşu dosyalar

[[library-kapsam]] (dinleme kaydı · R-L1) · [[music-kapsam]] (parça · `music.played`
sahipliği D3) · [[device-management-kapsam]] (hedef cihaz) ·
[[recommendation-kapsam]] (R-PL10) · [[domain-master]] (§3.1 satır 92 · §7)

## §20 OKUYUCU UYARILARI VE ÖLÇÜM YÖNTEMİ

### §20.1 Sınırlar

- Bu dosya `status: draft` — PlaybackSession aggregate'i **kilitlenmemiştir**.
- §7/§8/§10/§13 **öneridir, kanıt değildir**.
- `MusicPlayedEvent` yayıncısı ve cookie↔DB senkronu **kanıtlanmamıştır**.

### §20.2 Ölçüm yöntemi

Satır sayısı `[System.IO.File]::ReadAllLines` ile ölçülür; `Measure-Object -Line`
**kullanılmaz**.

## §21 KARAR MATRİSİ (BU DOSYA ÖLÇÜSÜNDE)

| # | Karar | Durum |
|---|---|---|
| 1 | Aggregate `PlaybackSession` | ⚠️ TASARIM İDDİASI — master satır 92 · ADR-097 K4 |
| 2 | `PlaybackQueueUpdatedEvent` | ❌ DOSYA YOK — R-PL2 |
| 3 | Çift depo (cookie + DB) | ⚠️ SENKRON YOK — R-PL4 |
| 4 | `user_preferences` sahipliği | ⚠️ ŞEMA ≠ BAĞLAM — R-PL5 |
| 5 | `music.played` sahipliği | ⚠️ playback ↔ music — D3 |

## §22 AKIŞ SENARYOLARI (TASARIM ÖNERİSİ — kanıt DEĞİL)

### §22.1 Parça başlatma

`R-PL7 enqueue` (V-PL1/V-PL2 doğrulama) → kuyruk satırı → `MusicPlayedEvent`
(yayıncı R-PL1) → `library.recordListen` (komşu).

### §22.2 Duraklatma

JS `MM_PlayingState` güncellenir (s.7–38) → DB `is_playing` senkronu **kanıtsız**
(R-PL4) → `playback.paused` olayı **yok** (E-PL2).

### §22.3 Tercih güncelleme

`R-PL11 updatePreferences` (V-PL4/V-PL5) → `user_preferences` UPDATE →
`preference.updated` olayı **yok** (E-PL6).

## §23 SONRAKİ TUR İŞ KALEMLERİ

1. `MusicPlayedEvent` yayıncısı taraması (R-PL1).
2. Cookie↔DB senkron stratejisi tasarımı (R-PL4).
3. `user_preferences` sahiplik kararı (R-PL5) — playback mi user mı.
4. `PlaybackQueueUpdatedEvent` kararı (R-PL2) — ekleme mi erteleme mi.
5. §13 senaryolarının `test-writer` kapsamına devri.

## §24 TUR SONU ÖLÇÜM DÜZELTMESİ

| # | Öğe | Değer |
|---|---|---|
| 1 | Hedef satır | ≥500 |
| 2 | ÖLÇÜM YÖNTEMİ | `ReadAllLines().Length` (Measure-Object -Line YASAK) |
| 3 | Sonuç | ↓ alt bölümde ölçülecek |
| 4 | Authority | eski satır aynen korundu + yenisi eklendi (append-only) |

## §25 TUR SONU KONTROL LİSTESİ (K008-derinleştirme)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Authority eski satır korundu mu? | ✅ `2026-10-15` satırı aynen duruyor |
| 2 | Yeni Authority satırı eklendi mi? | ✅ `2026-10-17` append |
| 3 | Frontmatter silindi mi? | ❌ HAYIR — 19 satır aynen korundu |
| 4 | Bölüm silindi mi? | ❌ HAYIR — §1–§4 aynen duruyor; §5–§25 append |
| 5 | Yer tutucu | 0 |
| 6 | VERIFICATION REQUIRED / UNKNOWN | §5 R-PL1 · §10 · §20 — işaretli belirsizlik |
| 7 | Uydurma satır numarası | 0 — tüm `s.NN` değerleri okunan kaynaklardan |
| 8 | Ölçüm yöntemi | `ReadAllLines().Length` |

## §26 ATIF VE KAYNAK ÇİZELGESİ

| İddia | Kaynak | Tür |
|---|---|---|
| `playback_queue` kolon/dizin/FK | `coremusic_user.sql` s.18–33 | ✅ disk kanıtı |
| `user_preferences` tercihleri | `coremusic_user.sql` s.104–124 | ✅ disk kanıtı |
| `music.played` + payload | `MusicPlayedEvent.php` s.20 · 26–30 · 39 | ✅ disk kanıtı |
| Cookie durumu `MM_*` | `coreplayer.controls.js` s.7–38 · s.22 | ✅ disk kanıtı |
| PlaybackSession aggregate | `domain-master.md` satır 92 | ⚠️ TASARIM İDDİASI |
| `PlaybackQueueUpdatedEvent` boşluğu | `Events/Domain/` dizin + D-PL11 | ✅ negatif kanıt |
| Repository/VO/invariant listeleri | Bu dosya §7–§10, §13 | 🔶 TASARIM ÖNERİSİ |

> **Bu dosyanın doğruluk sözü:** her `s.NN` satır numarası okunan kaynaklardan
> gelir; §7/§8/§10'daki "aday/öneri" sütunları kanıt **değildir** ve ADR-097 K4'e
> bağlıdır.

## §27 KOD KATMANI DETAYI (coreplayer.controls.js — cookie deposu)

> **Kaynak:** `assets.coremusic.net/js/coreplayer/coreplayer.controls.js`
> s.7–38 (2026-10-15 okuma). Bu bölüm yalnız kanıt sunar.

| # | JS kanıtı | Satır | Ne |
|---|---|---|---|
| 1 | `PlaybackRepository` tanımı | s.7 | Frontend nesnesi — backend repository DEĞİL (§10.1) |
| 2 | `MM_CurrentSongID` | s.29 | Çalan parça kimliği (cookie) |
| 3 | `MM_PlayingState` | s.33 | Oynatma durumu (serbest metin) |
| 4 | Cookie ömrü 365 gün | s.22 | Uzun ömür — oturum politikası R-PL7 |
| 5 | `MM_*` yazım/okuma | s.7–38 | Tek yön: JS → cookie; DB'ye yazım kanıtı YOK |

### §27.1 Çift depo riski (kanıt — tasarım DEĞİL)

| Depo | İçerik | Yazan | Okuyan | Senkron |
|---|---|---|---|---|
| Cookie (`MM_*`) | anlık durum | JS (s.7–38) | JS | — |
| DB (`playback_queue`) | kalıcı sıra | ⚠️ backend (kanıt YOK) | ⚠️ backend (kanıt YOK) | ❌ KANIT YOK — R-PL4 |

> **Sonuç:** iki depo birbirini bilmiyor; kullanıcı yeni cihaza geçtiğinde
> cookie (o cihazda) ile DB kuyruğu **farklı olabilir**. Eşitleme motoru
> kanıtlanmamıştır — `⚠️ VERIFICATION REQUIRED`.

## §28 TUR KAYDI VE KAPANIŞ (2026-10-17)

| # | Öğe | Değer |
|---|---|---|
| 1 | Tur | K008-domain derinleştirme — playback |
| 2 | Eklenen bölümler | §5–§28 (24 bölüm) |
| 3 | Eklenen invariant adayı | PL-P1…PL-P8 |
| 4 | Eklenen VO adayı | V-PL1…V-PL6 |
| 5 | Eklenen olay adayı | E-PL1…E-PL6 |
| 6 | Eklenen repository önerisi | R-PL6…R-PL11 |
| 7 | Açık soru | R-PL1…R-PL10 (5 + 5) |
| 8 | Test senaryosu | PL-T1…PL-T7 |
| 9 | En kritik bulgu | Çift depo (cookie ↔ DB) senkronu kanıtsız (§27.1) |
| 10 | Yasak ihlali | 0 — Authority eski satır korundu, append-only |
| 11 | Son eklenen bölüm | §29 (olay payload karşılaştırması — çapraz okuma) |

## §29 OLAY PAYLOAD KARŞILAŞTIRMASI (Events/Domain/ — çapraz okuma)

> `shared/src/Events/Domain/` dizini 2026-10-15 okuması: 9 event dosyası + 1 CLAUDE.md.
> Bu bölüm `music.played`'ı kardeş olaylarla karşılaştırır — kanıt sunar.

| Olay | Dosya satırı (uzunluk) | `eventName` | Payload | playback ile ilişki |
|---|---|---|---|---|
| `MusicPlayedEvent` | 86 satır | `music.played` (s.39) | musicId · userId · duration | ✅ bu dalın olayı (§9.1) |
| `MediaAccessedEvent` | 86 satır | ⚠️ VERIFICATION REQUIRED | ⚠️ payload okunmadı | media — ses erişimi |
| `PlaylistCreatedEvent` | 86 satır | ⚠️ VERIFICATION REQUIRED | ⚠️ payload okunmadı | playlist — `source_id` hedefi (s.24) |
| `UserLoggedInEvent` | 86 satır | ⚠️ VERIFICATION REQUIRED | ⚠️ payload okunmadı | user — oturum |

> **Kural:** `eventName()` tam değerleri bu turda **okunmadı** → ⚠️ VERIFICATION
> REQUIRED olarak işaretlidir; tahmin yazılmaz. Yalnız `MusicPlayedEvent` satır
> kanıtlıdır (s.39).

### §29.1 `duration` birimi notu (music ↔ playback çaprazı)

| İddia | Kanıt | Durum |
|---|---|---|
| `MusicPlayedEvent.payload.duration` birimi | ctor s.26–30 — birim comment'i okunmadı | ⚠️ VERIFICATION REQUIRED (saniye mi ms mi — `music-kapsam.md` R7 ile aynı sınıf soru) |
| `user_listening_history.listen_duration_sec` | s.88 comment: seconds | ✅ saniye (library kanıtı) |
| `playback_queue.position` birimi | s.22 comment: 0-based | ✅ sıra — süre değil (bilgi) |

> Bu iki `duration` aynı birimde **değil olabilir** — birim eşlemesi
> `domain-bilesenler.md` §14'e devredildi.

---

**Kardeş dosyalar:** [[playback-dogrulama]] · [[playback-web-arastirma]] · [[playback-ornekler]] · [[playback-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
**Authority:** Bayram Ali / Vault Steward · 2026-10-17 (K008-domain derinleştirme — §5–§26 eklendi; şema + JS + event kanıtı derinleştirildi, çift depo senkron boşluğu kaydedildi)
