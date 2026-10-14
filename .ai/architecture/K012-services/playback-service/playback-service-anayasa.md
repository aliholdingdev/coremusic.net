---
node_id: K012-services/playback-service/playback-service-anayasa
node_type: module
status: draft
parent_id: K012-services/playback-service
role: anayasa
children: []
depends_on:
  - "K012-services/playback-service/index.md"
evidence:
  - "home.coremusic.net/include/Component/PlayerInfoComponent.php — 66 satır, 2026-10-13 tam okundu"
  - "home.coremusic.net/include/Class/AbstractComponent.php — 101 satır, 2026-10-13 tam okundu"
  - "home.coremusic.net/include/Class/ComponentLoader.php — 116 satır, 2026-10-13 tam okundu"
  - "home.coremusic.net/footer.php — 80+ satır okundu (kısım), 2026-10-13"
  - "home.coremusic.net/pages/components/player-info.php — 60+ satır okundu (kısım), 2026-10-13"
validation:
  - "Beş dosya diskten okundu; session anahtarları ve registry eşlemeleri satır numarasıyla kaydedildi (2026-10-13)"
open_questions:
  - "playback sahipliği: K012 mi K015 mi? → ⚠️ VERIFICATION REQUIRED — çözülmedi, asla uydurulmadı"
  - "PlayerInfoComponent.js (assets.coremusic.net) okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "Session anahtarlarını YAZAN taraf (playback komut işleyicisi) repo'da tespit edilemedi → UNKNOWN"
group: mimari
---

# playback-service — Anayasa (kademe-3 · module · rol: anayasa)

**ELI15:** Playback servisi "ne çalıyor / ne çalacak" durumunu gösterir. Kod kanıtı şimdilik **görüntüleme katmanındadır**: `PlayerInfoComponent` (66 satır) session'dan okur, `footer.php` aynı session anahtarlarını okur, `ComponentLoader` kayıt merkezidir. Durumu **yazan** taraf (oynat/duraklat/sıradaki komutu işleyen kod) 2026-10-13 taramasında bu klasörde **tespit edilemedi** → UNKNOWN, uydurulmadı.

**Sınır (ne DEĞİL):** Ses üretimi K003-audio · dosya servisi K012/music-service (stream handler) · durum saklama K007-data · playback sahipliği K015 ile **çatışma açık** (index §A.2).

**Kod kanıtı kuralı:** Her iddia `Dosya: … / Satır: …` ile bağlanır.

**Durum:** draft · PHP runtime yok — `php -l` ⚠️ VERIFICATION REQUIRED · kademe-4 ADR-097 K4 faz (C) bekliyor.

## §1 Dosya Envanteri (playback görselleştirme yüzeyi)

| # | Dosya | Satır | Rol |
|---|---|---|---|
| 1 | include/Component/PlayerInfoComponent.php | 66 | Now Playing paneli veri kaynağı |
| 2 | include/Class/AbstractComponent.php | 101 | Taban: render, escape, session okuma, asset |
| 3 | include/Class/ComponentLoader.php | 116 | Bileşen registry + composition root |
| 4 | pages/components/player-info.php | 60+ (kısım okundu) | View partial (wide + embedded) |
| 5 | footer.php | 80+ (kısım okundu) | Footer player bar (seekbar, meta, ikonlar) |

**Toplam okunan:** 66 + 101 + 116 + parçalı 120+ satır. `pages/components/player-info.php` ve `footer.php` tamamlanmadı — devamı ⚠️ VERIFICATION REQUIRED.

## §2 Session Anahtarları (Playback Durumu Sözlüğü)

| Anahtar | Tip | ComponentLoader varsayılan (satır) | footer.php varsayılan (satır) |
|---|---|---|---|
| `current_song` | string | `Göksel - Sevil Neşelen` (PlayerInfo 40) | `Göksel - Sevil Neşelen` (footer 38) |
| `current_album` | string | `Hayat Rüya Gibi` (41) | `Hayat Rüya Gibi` (39) |
| `current_artist` | string | `Göksel` (42) | `Göksel` (40) |
| `current_bitrate` | string | `350 kbps` (43) | `350 kbps` (43) |
| `current_elapsed` | string | `00:00:00` (44) | `00:00:00` (41) |
| `current_duration` | string | `00:05:00` (45) | `00:05:00` (42) |
| `progress_pct` | int | `30` (46) | `65` (37) |
| `current_art` | string (URL) | `album-goksel.png` (47) | `album-goksel.png` (44) |
| `volume` | int 0-100 | yok | `100` + clamp `max(0, min(100, …))` (45) |
| `csp_nonce` | string | partial 15 | footer 34 |

**ÇELİŞKİ KAYDI (çözülmedi):** `progress_pct` varsayılanı iki yerde FARKLIDIR — bileşende **30**, footer'da **65**. Hangisinin doğru olduğu belirsiz; revize edilmedi, yalnız raporlandı (2026-10-13). Figma varsayılan metinleri footer dokümanında belgelenmiştir (footer satır 5-17); `30` için kaynak kodda gerekçe bulunamadı → UNKNOWN.

## §3 PlayerInfoComponent — Alan Envanteri (19 readonly)

| # | Alan | Tip | Kaynak (satır) |
|---|---|---|---|
| 1 | `$song` | string | session + `h()` (40) |
| 2 | `$album` | string | session + `h()` (41) |
| 3 | `$artist` | string | session + `h()` (42) |
| 4 | `$bitrate` | string | session + `h()` (43) |
| 5 | `$elapsed` | string | session + `h()` (44) |
| 6 | `$duration` | string | session + `h()` (45) |
| 7 | `$seekPct` | int | `sessionInt` (46) — ESCAPE YOK (int, 47. satır yorumu gerekmez: int olduğu için HTML escape gerekmez) |
| 8 | `$imgCover` | string | `$_SESSION['current_art']` fallback asset (47) — `h()` UYGULANMAZ (47) |
| 9-19 | `$imgStar`, `$iconPlay/Music/Cd/Mic/Star/Bitrate/Timer/Mp3/Mp4/Vlc` | string | sabit asset yolları (48-58) |

**Gözlem (satır 47):** `$imgCover` `$_SESSION`'dan doğrudan alınır, `h()`'den GEÇMEZ; `alt` niteliğinde `$currentSong` (escape'li) kullanılır (partial satır 32, 52). `src` niteliği XSS'e kapalı değildir — `javascript:` gibi bir değer session'a yazılırsa doğrudan `src`'ye girer → ⚠️ SECURITY NOT: `current_art` yazan taraf güvenilir olmalı; yazan taraf bilinmiyor (UNKNOWN, §6).

## §4 Render Zinciri

| # | Adım | Dosya/Satır |
|---|---|---|
| 1 | `ComponentLoader::display('player-info', $variant)` (belge satır 20-21 örneği) | ComponentLoader 103-106 |
| 2 | `make()` → `new PlayerInfoComponent($variant)` (registry `player-info`, satır 50) | ComponentLoader 85-95 |
| 3 | ctor: session okuma + asset yolları | PlayerInfo 36-59 |
| 4 | `render()` → `ob_start` + `require partial()` | AbstractComponent 34-44 |
| 5 | partial yolu = `pages/components/{key}.php` | AbstractComponent 29-32 |
| 6 | partial iki varyant: wide (`player-info--wide`) / embedded (`now-playing--embedded`) | partial 29-60 |

**Composition root kuralı:** "DB bağımlılığı yalnız burada injection edilir (component içi `new DatabaseManager` yasak — ADR-002 / katman kuralı)" (ComponentLoader satır 82-83) — `RecentTracksComponent` özel dalı `MusicRepository::fromEnvironment()` enjekte eder (satır 90-92); `PlayerInfoComponent` DB ENJEKTE EDİLMEZ (satır 94 genel dal).

## §5 Footer Player Bar (footer.php)

| Bölge | İçerik | Satır |
|---|---|---|
| Figma SSOT | 1024: Footer 1025×91, Album 85×85, Progressbar 1025×4.8; 1920: Footer 1923×92 | 5-11 (yorum) |
| DeviceManager | viewport fallback + tier class (`--4k`/`--wide`/`--embedded`) | 22-27, 62-64 |
| Çalma durumu | §2 tablosu (session) | 36-46 |
| Seekbar | `role="progressbar"` + `input[type=range]` 0-100 step 0.0001 | 69-72 |
| Meta | cover + Şarkı/Albüm satırları `id="footer_songname"` vb. | 76-80 |
| Erişilebilirlik | `role="contentinfo"`, `aria-label="Oynatıcı"`, progressbar `aria-valuenow` | 66, 69 |

**Gözlem:** Footer ve PlayerInfoComponent AYNI session anahtarlarını ayrı ayrı okur (footer satır 37-44 ↔ PlayerInfo 40-47) — tek kaynak, iki okuyucu. Varsayılan farkı §2'de kayıtlıdır.

## §6 Yazan Taraf (durum üretimi) — Tarama Sonucu

| Arama | Kapsam | Sonuç |
|---|---|---|
| `$_SESSION['current_song'] =` (yazma) | home.coremusic.net tüm PHP | 2026-10-13 grep turunda İSABET YOK (okuma: footer 38, PlayerInfo 40) |
| `progress_pct'] =` | aynı | İSABET YOK (okuma: footer 37, PlayerInfo 46, partial 21/43/59) |
| `volume'] =` | aynı | İSABET YOK (okuma: footer 45) |

**Sonuç:** Playback durumunu **yazan** PHP kodu home.coremusic.net'te bulunamadı. Olası açıklamalar (hangisinin doğru olduğu doğrulanmadı): (a) JS tarafı (`PlayerInfoComponent.js`, partial satır 10 referansı) bir endpoint üzerinden yazar, (b) session yazımı başka subdomain'de olur, (c) şimdilik tamamen demo varsayılanlarıyla çalışır. **Cevap bilinmiyor → UNKNOWN.**

## §7 Komut Yüzeyi (görünen ama işlenmeyen)

| Öge | Nerede | Durum |
|---|---|---|
| Play ikonu | footer 50 (`media-play.png`) + partial 42 | Statik `<img>` — `<button>`/handler DEĞİL |
| Pause/Stop/Back/Next ikonları | footer 51-53 | Statik `<img>` |
| Seekbar `input[type=range]` | footer 70 | Sunucu tarafı `onchange` YOK — JS tarafı bilinmiyor |
| `data-cm-component="cm-player-info"` + `data-cm-config` | partial 30, 50 | JS bileşen köprüsü (config: song/artist/progress, partial 16-27) |

**Kod kanıtı:** Sunucu tarafında oynat/duraklat/sıradaki komut işleyicisi **YOK** (beş dosyanın 2026-10-13 okuması) — komutlar JS tarafına devredilmiş görünüyor; JS dosyası okunmadı → ⚠️ VERIFICATION REQUIRED.

## §8 JSON Güvenlik Köprüsü (partial)

| Katman | Satır | Mekanizma |
|---|---|---|
| `strip_tags` | partial 19-21 | song/artist önce etiketlerden arındırılır |
| `json_encode` | partial 17 | `JSON_HEX_TAG \| JSON_HEX_APOS` |
| `htmlspecialchars` | partial 16 | `ENT_QUOTES`, UTF-8 |
| `csp_nonce` | partial 15 | script nonce (`$_SESSION['csp_nonce']`) |

**Sıra kuralı:** `strip_tags` → `json_encode` (hex flag) → `htmlspecialchars` — üç katmanlı; `data-cm-config='…'` tırnak kaçışı `ENT_QUOTES` ile sağlanır.

## §9 Açık Sorular (dosyaya özgü)

| # | Soru | Durum |
|---|---|---|
| 1 | Playback sahipliği K012 ↔ K015 sınırı | ⚠️ VERIFICATION REQUIRED — index §A.2'den taşındı, çözülmedi |
| 2 | `progress_pct` varsayılan 30 mu 65 mi? (§2 çelişkisi) | UNKNOWN — iki farklı varsayılan, gerekçe yok |
| 3 | Durum yazan taraf kim? (§6) | UNKNOWN — home'da yazım yok |
| 4 | `PlayerInfoComponent.js` komut mantığı | ⚠️ VERIFICATION REQUIRED — okunmadı |
| 5 | `current_art` yazımı güvenilir mi? (§3 notu) | UNKNOWN — yazan taraf bilinmiyor |

## §10 İddia Kayıt Defteri (yeni URL yok)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| ADR-002 katman kuralı (composition root) | URL yok — kod yorumu ComponentLoader satır 83 | 2026-10-13 | geçerli |
| Figma node 1646:17727 / 2849:21489 | URL yok — partial satır 9 yorumu | 2026-10-13 | geçerli |

**Yeni URL eklenmedi** — envanter kesişimi 0.

## §11 Bilinen Boşluklar

| # | Boşluk | Durum |
|---|---|---|
| 1 | `php -l` kanıtı yok | ⚠️ VERIFICATION REQUIRED |
| 2 | `pages/components/player-info.php` tamamlanmadı (60+ sonrası) | ⚠️ VERIFICATION REQUIRED |
| 3 | `footer.php` tamamlanmadı (80+ sonrası) | ⚠️ VERIFICATION REQUIRED |
| 4 | `PlayerInfoComponent.js` (assets.coremusic.net) okunmadı | ⚠️ VERIFICATION REQUIRED |
| 5 | Yazan taraf (§6) — ses motoru/endpoint belli değil | UNKNOWN |
| 6 | WidgetGridComponent / WelcomeBannerComponent playback ile ilişkisi | UNKNOWN — okunmadı |

## §12 Verbatim Kanıt Blokları

### §12.1 PlayerInfoComponent.php — Dosya: home.coremusic.net/include/Component/PlayerInfoComponent.php / Satır: 1-66 (tamamı)

```php
<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Interfaces\ComponentInterface;

/**
 * PlayerInfoComponent — Player Info / Now Playing Merged Panel
 *
 * View partial: pages/components/player-info.php
 */
final class PlayerInfoComponent extends AbstractComponent
{
    public readonly string $song;
    public readonly string $album;
    public readonly string $artist;
    public readonly string $bitrate;
    public readonly string $elapsed;
    public readonly string $duration;
    public readonly int    $seekPct;
    public readonly string $imgCover;
    public readonly string $imgStar;
    public readonly string $iconPlay;
    public readonly string $iconMusic;
    public readonly string $iconCd;
    public readonly string $iconMic;
    public readonly string $iconStar;
    public readonly string $iconBitrate;
    public readonly string $iconTimer;
    public readonly string $iconMp3;
    public readonly string $iconMp4;
    public readonly string $iconVlc;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);

        $this->song      = $this->h($this->sessionString('current_song', 'Göksel - Sevil Neşelen'));
        $this->album     = $this->h($this->sessionString('current_album', 'Hayat Rüya Gibi'));
        $this->artist    = $this->h($this->sessionString('current_artist', 'Göksel'));
        $this->bitrate   = $this->h($this->sessionString('current_bitrate', '350 kbps'));
        $this->elapsed   = $this->h($this->sessionString('current_elapsed', '00:00:00'));
        $this->duration  = $this->h($this->sessionString('current_duration', '00:05:00'));
        $this->seekPct   = $this->sessionInt('progress_pct', 30);
        $this->imgCover  = (string)($_SESSION['current_art'] ?? $this->asset('/Image/res-pink/album-goksel.png'));
        $this->imgStar   = $this->asset('/Image/res-pink/star-filled.png');
        $this->iconPlay    = $this->asset('/Image/res-pink/play.png');
        $this->iconMusic   = $this->asset('/Image/res-pink/music.png');
        $this->iconCd      = $this->asset('/Image/res-pink/cd-ico.png');
        $this->iconMic     = $this->asset('/Image/res-pink/mic-1.png');
        $this->iconStar    = $this->asset('/Image/res-pink/star.png');
        $this->iconBitrate = $this->asset('/Image/res-pink/bit-rate.png');
        $this->iconTimer   = $this->asset('/Image/res-pink/timer.png');
        $this->iconMp3     = $this->asset('/Image/res-pink/mp3.png');
        $this->iconMp4     = $this->asset('/Image/res-pink/mp4.png');
        $this->iconVlc     = $this->asset('/Image/res-pink/vlc.png');
    }

    public function key(): string
    {
        return 'player-info';
    }
}
```

### §12.2 AbstractComponent — Yardımcı Methodlar / Satır: 28-73

```php
    /** View partial yolu — key() üzerinden türetilir. */
    protected function partial(): string
    {
        return dirname(__DIR__, 2) . '/pages/components/' . $this->key() . '.php';
    }

    final public function render(): string
    {
        ob_start();
        try {
            require $this->partial();
            return (string)ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    final public function display(): void
    {
        echo $this->render();
    }

    /** HTML escape. */
    final protected function h(string $v): string
    {
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** ASSETS_URL önekli varlık adresi. */
    final protected function asset(string $path): string
    {
        return (defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net') . $path;
    }

    /** Session string okuma (salt okuma). */
    final protected function sessionString(string $key, string $default): string
    {
        return (string)($_SESSION[$key] ?? $default);
    }

    /** Session int okuma. */
    final protected function sessionInt(string $key, int $default): int
    {
        return (int)($_SESSION[$key] ?? $default);
    }
```

### §12.3 ComponentLoader — make / display / Satır: 85-106

```php
    public function make(string $key, HomeLayoutVariant $variant = HomeLayoutVariant::Embedded): ComponentInterface
    {
        $class = $this->registry[$key]
            ?? throw new \InvalidArgumentException("Bilinmeyen bileşen: {$key}");

        if ($class === RecentTracksComponent::class) {
            return new $class($variant, null, MusicRepository::fromEnvironment());
        }

        return new $class($variant);
    }

    public function render(string $key, HomeLayoutVariant $variant = HomeLayoutVariant::Embedded): string
    {
        return $this->make($key, $variant)->render();
    }

    /** Bileşen HTML'ini doğrudan çıktıya yazar (include akışı). */
    public function display(string $key, HomeLayoutVariant $variant = HomeLayoutVariant::Embedded): void
    {
        echo $this->make($key, $variant)->render();
    }
```

## §13 Method Eksenleri

### §13.1 PlayerInfoComponent

| Method | İmza | Satır | Davranış |
|---|---|---|---|
| `__construct` | `public function __construct(HomeLayoutVariant $variant)` | 36-59 | 6 session string + 1 session int + 1 session URL + 11 asset |
| `key` | `public function key(): string` | 61-64 | `'player-info'` — partial ve registry anahtarı |

**Toplam:** 2 method (biri sözleşmesel `key()`); yardımcılar türünden methodsuz — tüm mantık ctor'da.

### §13.2 AbstractComponent (playback'ı ilgilendiren kısım)

| Method | Satır | Playback rolü |
|---|---|---|
| `__construct` | 23-26 | `readonly HomeLayoutVariant` saklar |
| `partial` | 29-32 | `pages/components/player-info.php` yolu |
| `render` | 34-44 | `ob_start` + `require` + `\Throwable` yeniden-fırlatma |
| `h` | 52-55 | `ENT_QUOTES \| ENT_SUBSTITUTE` escape |
| `asset` | 58-61 | `ASSETS_URL` fallback `http://assets.coremusic.net` |
| `sessionString` | 64-67 | `(string)($_SESSION[$key] ?? $default)` |
| `sessionInt` | 70-73 | `(int)($_SESSION[$key] ?? $default)` |

### §13.3 ComponentLoader (playback kayıt)

| Method | Satır | Playback rolü |
|---|---|---|
| `defaultRegistry` | 46-59 | `'player-info' => PlayerInfoComponent::class` (satır 50) |
| `register` | 66-72 | `is_subclass_of` denetimi — ComponentInterface zorunlu |
| `make` | 85-95 | playback dalı: `new PlayerInfoComponent($variant)` (genel dal, DB yok) |
| `has` | 74-77 | anahtar denetimi |
| `keys` | 112-115 | 4 anahtar: player-info, recent-tracks, widget-grid, welcome-banner |

## §14 Varyant Matrisi (player-info partial)

| Özellik | Wide (≥1025px) | Embedded (≤1024px) |
|---|---|---|
| Kök etiket | `<section class="player-info player-info--wide">` (partial 30) | `<section class="now-playing now-playing--embedded">` (partial 50) |
| Kapak | `player-info__cover` (31-33) | `now-playing__art--embedded` (51-53) |
| Meta satırları | Şarkı/Albüm/Sanatçı/Yıldız/Bit rate/Süre (35-40) | Şarkı/Albüm/Sanatçı (55-57) |
| Yıldız | 5 yıldız img (38) | yok |
| Badge'ler | MP3/MP4/VLC (39) | yok |
| İlerleme | `player-info__progress` (43-45) | `media-progress` + süre metni (59-60) |
| Figma | node 2849:21489 (1920 · 469×184) (partial 9) | node 1646:17727 (1024 · 392×131) |

## §15 Session Okuma Matrisi (tam karşılaştırma)

| Okuyucu | Dosya/Satır | Anahtarlar | Escape |
|---|---|---|---|
| PlayerInfoComponent | 40-47 | song, album, artist, bitrate, elapsed, duration, progress_pct, current_art | `h()` 6'da; `seekPct` int; `imgCover` escape YOK |
| footer.php | 37-44 | song, album, artist, elapsed, duration, bitrate, current_art, progress_pct | `$h` closure (29-31) |
| footer.php volume | 45-46 | volume (0-100 clamp) + `volumeRatio` | — |
| player-info partial | 15 | csp_nonce | `h()` |
| player-info partial | 19-21 | song/artist/progress → JSON config | `strip_tags` + hex + `htmlspecialchars` |

**Yazıcı:** HİÇBİRİ — hepsi salt okunur (§6 taraması: yazım isabeti 0).

## §16 Erişilebilirlik Kayıtları (okunan kısımda)

| Öge | Nitelik | Satır |
|---|---|---|
| Footer kökü | `role="contentinfo"`, `aria-label="Oynatıcı"` | footer 66 |
| Footer seekbar | `role="progressbar"`, `aria-valuenow/min/max` | footer 69 |
| Range girdisi | `aria-label="Şarkı pozisyonu"` | footer 70 |
| Wide panel | `aria-label="Şu an çalan"` | partial 30 |
| Yıldız | `role="img"`, `aria-label="Değerlendirme: 5 üzerinden 5 yıldız"` | partial 38 |
| İlerleme | `role="progressbar"`, `aria-valuemin/max/now` | partial 43, 59 |
| Embedded ilerleme | `aria-valuetext="elapsed / duration"` | partial 59 |

**Durum:** Bu kayıtlar okunan kısımlarla sınırlıdır; WCAG denetimi bu dosyanın kapsamı değildir (ui-designer agent alanı).

## §17 İddia Kayıt Defteri (genişletilmiş · yeni URL yok)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| ADR-002 katman kuralı | URL yok — ComponentLoader satır 83 | 2026-10-13 | geçerli |
| Figma node 1646:17727 / 2849:21489 | URL yok — partial satır 9 | 2026-10-13 | geçerli |
| Figma footer 1639:9773 ölçümleri | URL yok — footer satır 5 | 2026-10-13 | geçerli |
| Yazım isabeti 0 (negatif iddia) | URL yok — 2026-10-13 grep turu | 2026-10-13 | geçerli |

**Yeni URL eklenmedi** — envanter kesişimi 0.

## §18 Playback Görselleştirme Bileşenleri — Tam Karşılaştırma

| Kriter | PlayerInfoComponent | RecentTracksComponent |
|---|---|---|
| Dosya | include/Component/PlayerInfoComponent.php (66 satır) | include/Component/RecentTracksComponent.php (217 satır) |
| `key()` | `player-info` (satır 63) | `recent-tracks` (satır 113) |
| Veri kaynağı | yalnız session (satır 40-47) | session YOK — override/DB/PNG (satır 67) |
| DB bağımlılığı | yok (ComponentLoader genel dal, satır 94) | var — injection (satır 90-92) |
| Readonly alan | 19 (satır 16-34) | 4 (satır 46-56) |
| Varyant davranışı | iki HTML iskeleti (partial 29/49) | kart sayısı değişimi (satır 83-84) |
| CSS kökü | `player-info--wide` / `now-playing--embedded` | alt satır bölümleri (bileşen dokümanı 14-19) |
| JS köprüsü | `data-cm-component="cm-player-info"` (partial 30, 50) | JS referansı okunmadı → ⚠️ VERIFICATION REQUIRED |
| HTML escape | `h()` ctor'da (satır 40-45) | `HomeSongButton::html` içindedir → okunmadı → ⚠️ |
| Session yazımı | yok | yok |

**Ortak payday:** İkisi de `AbstractComponent`'ten gelir, `key()` sözleşmesini uygular, `ComponentLoader` üzerinden üretilir ve durum **yazmaz** — yalnız okur.

## §19 Tarama Kayıtları (negatif iddialar — yöntem)

| # | Tarama | Desen | Kapsam | Sonuç |
|---|---|---|---|---|
| 1 | Durum yazımı | `current_song.*=` | home.coremusic.net `*.php` | 0 isabet (okuma: footer 38, PlayerInfo 40) |
| 2 | İlerleme yazımı | `progress_pct.*=` | aynı | 0 isabet (okuma: footer 37, PlayerInfo 46) |
| 3 | Ses yazımı | `volume.*=` | aynı | 0 isabet (okuma: footer 45) |
| 4 | Playback sınıfı | `class Player` | aynı | yalnız `PlayerInfoComponent` (görüntüleme) |
| 5 | Yazma çağrıları | `session_put` / `SessionManager::set` | home.coremusic.net | İSABET YOK — 2026-10-13 turu |

**Yöntem notu:** Negatif iddialar yalnız grep ile VERİLEBİLİR; "repo'da YOK" demek için arama deseni + kapsam birlikte kaydedilir (bu tablo). İsim benzeri yanıltıcı eşleşmeler (örn. `PlayerInfoComponentTest`) kapsam dışı bırakıldı.

**Sonuç (bağlayıcı):** playback-service'in home.coremusic.net'teki tek gerçek kodu görüntülemedir. Durum üretim akışı (oynatma motoru → session) bu depoda **tespit edilemedi** → sahiplik K012 ↔ K015 çatışmasıyla birlikte ⚠️ VERIFICATION REQUIRED olarak kalır; asla tahmin edilmedi.

## §20 footer.php — Okunan Kısım Bloğu (1-65)

**Dosya:** `home.coremusic.net/footer.php` · **Satır:** 1-65 (kısım — devamı okunmadı, §11.3) · **Ölçüm:** 2026-10-13

```php
<?php declare(strict_types=1);
/**
 * footer.php — CoreMusic Footer Player Bar (v3.0)
 *
 * Figma SSOT (API extraction — node-id=1639-9773):
 *   1024: Footer 1025×91, Album 85×85, Progressbar 1025×4.8,
 *         Player Buttons 297×62 (4 btn, 35×35 each, gap 22),
 *         MediaInfo Avalon 10px w500 ls:1.35 lh:13.5,
 *         Volume 186×14, Text icons 15×15
 *   1920: Footer 1923×92, Progressbar 1921×4.8,
 *         Player Buttons 126×27, Volume 353×14
 *
 * Figma Font: Avalon 10px w500 (all footer text)
 * Figma Effects: text drop-shadow rgba(0,0,0,0.8) 0.5,0.5,0.1
 * BEM: .footer-player, .footer-player__cover, .footer-player__info, etc.
 * Layer: L3 Presentation · 03_Layout (_footer.css)
 * Version: 3.0.0 — 2026-09-23 (Figma API pixel-perfect)
 */

use CoreMusic\Device\DeviceManager;

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}

$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';
$nonce     = $h((string)($_SESSION['csp_nonce'] ?? ''));

/* ─── Çalma durumu (Figma varsayılan metinleri) ─── */
$progressPct     = (int)($_SESSION['progress_pct'] ?? 65);
$currentSong     = $h((string)($_SESSION['current_song'] ?? 'Göksel - Sevil Neşelen'));
$currentAlbum    = $h((string)($_SESSION['current_album'] ?? 'Hayat Rüya Gibi'));
$currentArtist   = $h((string)($_SESSION['current_artist'] ?? 'Göksel'));
$currentElapsed  = $h((string)($_SESSION['current_elapsed'] ?? '00:00:00'));
$currentDuration = $h((string)($_SESSION['current_duration'] ?? '00:05:00'));
$currentBitrate  = $h((string)($_SESSION['current_bitrate'] ?? '350 kbps'));
$currentArt      = $_SESSION['current_art'] ?? ($assetsUrl . '/Image/res-pink/album-goksel.png');
$volumePct       = max(0, min(100, (int)($_SESSION['volume'] ?? 100)));
$volumeRatio     = number_format($volumePct / 100, 4, '.', '');

/* ─── İkonlar (PNG birebir) ─── */
$iconBack  = $h($assetsUrl . '/Image/res-pink/media-back.png');
$iconPlay  = $h($assetsUrl . '/Image/res-pink/media-play.png');
$iconPause = $h($assetsUrl . '/Image/res-pink/media-pause.png');
$iconStop  = $h($assetsUrl . '/Image/res-pink/media-stop.png');
$iconNext  = $h($assetsUrl . '/Image/res-pink/media-ileri.png');
$iconVol   = $h($assetsUrl . '/Image/res-pink/volume-high.png');
$iconMusic   = $h($assetsUrl . '/Image/res-pink/music.png');
$iconCd      = $h($assetsUrl . '/Image/res-pink/cd-ico.png');
$iconMic     = $h($assetsUrl . '/Image/res-pink/mic-1.png');
$iconTimer   = $h($assetsUrl . '/Image/res-pink/timer.png');
$iconBitrate = $h($assetsUrl . '/Image/res-pink/bit-rate.png');

/* ─── Tier class ─── */
$footerTierClass = $dm->shouldRender4kLayout()
    ? 'footer-player--4k'
    : ($dm->shouldRenderWideLayout() ? 'footer-player--wide' : 'footer-player--embedded');
```

**Gözlemler:** (1) `$currentArt` `h()`'den GEÇMEZ (satır 44) — PlayerInfo ile aynı örüntü (§3 notu). (2) `DeviceManager::instance` `$_SERVER['VIEWPORT_*']` okur (satır 24-25) — viewport sunucu tarafında set edilir, kaynağı okunmadı → ⚠️. (3) `$nonce` satır 34'te üretilir — kullanıldığı yer blok dışında (§11.3).

## §21 Değişiklik Kaydı

| Tarih | Değişiklik | Kanan |
|---|---|---|
| 2026-10-13 | İlk yazım — 5 dosya envanteri, session sözlüğü (30/65 çelişkisi kayıtlı), render zinciri, yazan taraf taraması (0 isabet) | Vault Steward oturumu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
