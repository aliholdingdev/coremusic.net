---
node_id: K012-services/playback-service/playback-service-ornekler
node_type: module
status: draft
parent_id: K012-services/playback-service
role: ornekler
children: []
depends_on:
  - "K012-services/playback-service/playback-service-anayasa.md"
  - "K012-services/playback-service/playback-service-surucu.md"
evidence:
  - "home.coremusic.net/include/Class/AbstractComponent.php — 1-101 TAMAMEN okundu, 2026-10-13"
  - "home.coremusic.net/include/Class/ComponentLoader.php — 1-116 TAMAMEN okundu, 2026-10-13"
  - "home.coremusic.net/pages/components/player-info.php — 1-65 TAMAMEN okundu, 2026-10-13"
validation:
  - "Üç blok kaynak dosyadan birebir kopyalandı (2026-10-13 read kanıtı)"
  - "Kaçış envanteri §6 — ham echo bulgusu satır numaralı"
open_questions:
  - "$this->song/$this->album/$this->artist partial 35-37/55-57 ham echo — yazar/niyet UNKNOWN"
  - "JS katmanı (PlayerInfoComponent.js) okunmadı → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# playback-service — Örnekler (kademe-3 · module · rol: ornekler)

**ELI15:** Bu dosya playback görüntüleme katmanının üç gerçek dosyasını **tam metin** (verbatim) taşır ve her birini analiz tablolarıyla çözümler. Amaç: bir sonraki okuyucunun kaynak dosyalara gitmeden kodu görmesi.

**Sınır (ne DEĞİL):** Kod okuma/anlaşma anayasa §12'dedir · sürüş sırası surucu §1'dedir. Buradaki bloklar **kanıt**tur, yorum üstünlüğü yoktur.

**Verbatim kuralı:** Üç blok da kaynak dosyanın tamamıdır (AbstractComponent 1-101 · ComponentLoader 1-116 · player-info.php 1-65). Kırpma, yeniden biçimlendirme, satır atlama YOKTUR.

**Durum:** draft · PHP runtime yok — `php -l` çalıştırılmadı ⚠️ VERIFICATION REQUIRED.

## §1 Dosya Envanteri

| Dosya | Satır | Bayt | Blok | Rol |
|---|---|---|---|---|
| `home.coremusic.net/include/Class/AbstractComponent.php` | 101 | 3298 | §2 | template-method taban |
| `home.coremusic.net/include/Class/ComponentLoader.php` | 116 | 3874 | §3 | registry + composition root |
| `home.coremusic.net/pages/components/player-info.php` | 65 | 6133 | §4 | wide/embedded partial |

> Bayt ölçümü: `get_file_info` · 2026-10-13 (dosya boyutu, satır sonu dahil).

**Kapsam notu:** `PlayerInfoComponent.php` (66 satır) ve `footer.php` (1-65) verbatim blokları anayasa §12/§20'dedir — burada TEKRARLANMAZ (SSOT).

## §2 AbstractComponent.php — TAM METİN (1-101)

> Dosya: `home.coremusic.net/include/Class/AbstractComponent.php` / Satır: 1-101 · okundu: 2026-10-13

```php
<?php declare(strict_types=1);

namespace CoreMusic\Home\Class;

use CoreMusic\Home\Interfaces\ComponentInterface;

/**
 * AbstractComponent — template-method taban (v3.0.0)
 *
 * Figma pixel-perfect measurement helpers eklenmiştir.
 * Tüm bileşenler Figma API'dan çekilen gerçek boyutları kullanır.
 *
 * Figma measurements (API extraction):
 *   1024: node-id=1639-10160 → 1024×600
 *   1920: node-id=2831-13747 → 1920×1080
 *
 * Alt sınıf sözleşmesi:
 *   1. key()                          — registry + partial adı
 *   2. __construct(HomeLayoutVariant) — parent::__construct($variant) ÇAĞRILMAK ZORUNDA
 */
abstract class AbstractComponent implements ComponentInterface
{
    public function __construct(
        public readonly HomeLayoutVariant $variant,
    ) {
    }

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

    /* ═══════════════════════════════════════════════════════════════
       Figma Pixel-Perfect Measurement Helpers
       Tüm boyutlar Figma API'dan extract edilmiştir.
       ═══════════════════════════════════════════════════════════════ */

    /** Wide (1920+) layout mu? */
    final protected function isWide(): bool
    {
        return $this->variant->isWide();
    }

    /**
     * Cihaz bazlı boyut döndür
     * @param string $key — Token adı (ör: 'player', 'cover', 'footer')
     * @return array{width: int|float, height: int|float}
     */

    /**
     * CSS class döndür (tier'a göre)
     * @param string $base — Base class adı (ör: 'player-info')
     * @return string
     */
    final protected function tierClass(string $base): string
    {
        return $this->isWide() ? "{$base}--wide" : "{$base}--embedded";
    }
}
```

### §2.1 Method Envanteri

| # | Method | Erişim | Satır | Gözlem |
|---|---|---|---|---|
| 1 | `__construct(HomeLayoutVariant)` | public readonly property promotion | 23-26 | tek zorunlu argüman |
| 2 | `partial()` | protected | 29-32 | `dirname(__DIR__, 2)` → proje kökü |
| 3 | `render()` | final public | 34-44 | `final` — alt sınıf değiştirmez |
| 4 | `display()` | final public | 46-49 | yalnız `echo` |
| 5 | `h()` | final protected | 52-55 | `ENT_QUOTES \| ENT_SUBSTITUTE` |
| 6 | `asset()` | final protected | 58-61 | `ASSETS_URL` sabiti + HTTP fallback |
| 7 | `sessionString()` | final protected | 64-67 | `(string)` + `??` |
| 8 | `sessionInt()` | final protected | 70-73 | `(int)` + `??` |
| 9 | `isWide()` | final protected | 81-84 | varyant sarmalayıcı |
| 10 | `tierClass()` | final protected | 97-100 | 2'li sınıf: `--wide` / `--embedded` |

### §2.2 Gözlem Tablosu

| # | Gözlem | Satır | Not |
|---|---|---|---|
| 1 | Docblock'ta `Cihaz bazlı boyut döndür` başlığı var ama gövde YOK | 86-90 | iki docblock bitişik (86-91, 92-96) — gövdesiz doküman |
| 2 | `asset()` HTTP fallback | 60 | `http://assets.coremusic.net` — HTTPS dışı sabit |
| 3 | `render` `final` | 34 | alt sınıflar yalnız `key()` ve ctor bağlar |
| 4 | `tierClass` yalnız 2 sonuç | 99 | `--4k` sınıfı bu sınıfta ÜRETİLMEZ (surucu §13) |

## §3 ComponentLoader.php — TAM METİN (1-116)

> Dosya: `home.coremusic.net/include/Class/ComponentLoader.php` / Satır: 1-116 · okundu: 2026-10-13

```php
<?php declare(strict_types=1);

namespace CoreMusic\Home\Class;

use CoreMusic\Home\Interfaces\ComponentInterface;
use CoreMusic\Home\Component\PlayerInfoComponent;
use CoreMusic\Home\Component\RecentTracksComponent;
use CoreMusic\Home\Component\WelcomeBannerComponent;
use CoreMusic\Home\Component\WidgetGridComponent;
use CoreMusic\Home\Repository\MusicRepository;

/**
 * ComponentLoader — dinamik bileşen kayıt + yükleme merkezi (v3.0.0)
 *
 * Figma pixel-perfect component registry.
 * Tüm component'ler Figma API'dan extract edilen boyutları kullanır.
 *
 * Kullanım (pages/home.php):
 *
 *   $loader = new ComponentLoader();
 *   $loader->display('player-info', $variant);
 *   $loader->display('widget-grid', $variant);
 *
 * Yeni bileşen ekleme:
 *   a) Kalıcı: defaultRegistry()'ye sınıf ekle
 *   b) Dinamik: $loader->register('anahtar', BileşenSınıfı::class)
 *
 * Yükleme sözleşmesi: tüm bileşen constructor'ları tek argüman alır:
 *   __construct(HomeLayoutVariant $variant, ...opsiyonel)
 */
final class ComponentLoader
{
    /** @var array<string, class-string<ComponentInterface>> */
    private array $registry;

    public function __construct()
    {
        $this->registry = self::defaultRegistry();
    }

    /**
     * Varsayılan component registry (Figma pixel-perfect)
     *
     * @return array<string, class-string<ComponentInterface>>
     */
    public static function defaultRegistry(): array
    {
        return [
            /* ─── Player & Audio ─── */
            'player-info'      => PlayerInfoComponent::class,
            'recent-tracks'    => RecentTracksComponent::class,

            /* ─── Wide/4K Widget Grid ─── */
            'widget-grid'      => WidgetGridComponent::class,

            /* ─── Wide/4K Orta Sütun ─── */
            'welcome-banner'   => WelcomeBannerComponent::class,
        ];
    }

    /**
     * Runtime bileşen kaydı — mevcut anahtarı ezer, yenisini ekler.
     *
     * @param class-string<ComponentInterface> $class
     */
    public function register(string $key, string $class): void
    {
        if (!is_subclass_of($class, ComponentInterface::class)) {
            throw new \InvalidArgumentException("{$class} ComponentInterface uygulamıyor");
        }
        $this->registry[$key] = $class;
    }

    public function has(string $key): bool
    {
        return isset($this->registry[$key]);
    }

    /**
     * Anahtar + varyant ile bileşen örneği üretir (immutable).
     *
     * Composition root: DB bağımlılığı yalnız burada injection edilir
     * (component içi `new DatabaseManager` yasak — ADR-002 / katman kuralı).
     */
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

    /**
     * Tüm registered component anahtarlarını döndür
     * @return array<string>
     */
    public function keys(): array
    {
        return array_keys($this->registry);
    }
}
```

### §3.1 Registry Envanteri (defaultRegistry)

| Anahtar | Sınıf | Satır | Özel dal (make) |
|---|---|---|---|
| `player-info` | `PlayerInfoComponent` | 50 | hayır — `new $class($variant)` (94) |
| `recent-tracks` | `RecentTracksComponent` | 51 | EVET — `new $class($variant, null, MusicRepository::fromEnvironment())` (91) |
| `widget-grid` | `WidgetGridComponent` | 54 | hayır |
| `welcome-banner` | `WelcomeBannerComponent` | 57 | hayır |

### §3.2 Method Envanteri

| # | Method | Satır | Sözleşme |
|---|---|---|---|
| 1 | `__construct()` | 36-39 | `defaultRegistry()` kopyası |
| 2 | `defaultRegistry()` (static) | 46-59 | 4 kayıt — sabit liste |
| 3 | `register()` | 66-72 | `is_subclass_of` denetimi + ekleme/ezme |
| 4 | `has()` | 74-77 | `isset` |
| 5 | `make()` | 85-95 | composition root — DB yalnız burada |
| 6 | `render()` | 97-100 | `make` + `render` |
| 7 | `display()` | 103-106 | `echo make->render()` |
| 8 | `keys()` | 112-115 | `array_keys` |

### §3.3 Gözlem Tablosu

| # | Gözlem | Satır | Not |
|---|---|---|---|
| 1 | `make` `final` değil ama sınıf `final` | 31 | sınıf-seviyesi yeterli |
| 2 | Özel dal yalnız tek sınıf | 90-92 | `MusicRepository::fromEnvironment()` yalnız orada |
| 3 | `register` mevcut anahtarı EZER | 62 | docblock açıkça söylüyor |
| 4 | Docblock `pages/home.php` kullanımı gösteriyor | 18-22 | home.php okunmadı → ⚠️ |
| 5 | Fallback `null` argümanı (91) | 91 | ikinci param `null` — ne anlama geliyor → RecentTracks ctor'a havale |

## §4 player-info.php — TAM METİN (1-65)

> Dosya: `home.coremusic.net/pages/components/player-info.php` / Satır: 1-65 · okundu: 2026-10-13

```php
<?php declare(strict_types=1);
/**
 * pages/components/player-info.php — Player Info & Now Playing (Merged)
 *
 * HTML contract → assets.coremusic.net/Css/04_Components/_player-info.css (v2.0)
 *   wide     (isWide ≥1025px) → <section class="player-info player-info--wide">
 *   embedded (≤1024px)        → <section class="now-playing now-playing--embedded">
 *
 * Figma SSOT: node 1646:17727 (1024 · 392×131) · node 2849:21489 (1920 · 469×184)
 * JS: assets.coremusic.net/js/components/interactive/PlayerInfoComponent.js
 *
 * @var \CoreMusic\Home\Component\PlayerInfoComponent $this
 */
$isWide   = $this->variant->isWide();
$nonce    = $this->h((string)($_SESSION['csp_nonce'] ?? ''));
$cmConfig = htmlspecialchars(
    json_encode(
        [
            'song'     => strip_tags($this->song),
            'artist'   => strip_tags($this->artist),
            'progress' => $this->seekPct,
        ],
        JSON_HEX_TAG | JSON_HEX_APOS
    ),
    ENT_QUOTES,
    'UTF-8'
);
?>
<?php if ($isWide): ?>
<section class="player-info player-info--wide" aria-label="Şu an çalan" data-cm-component="cm-player-info" data-cm-config='<?= $cmConfig ?>'>
    <div class="player-info__cover">
        <img src="<?= $this->h($this->imgCover) ?>" alt="Çalan şarkının albüm kapağı" loading="lazy">
    </div>
    <div class="player-info__info-panel">
        <h1 class="player-info__row player-info__title"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconMusic) ?>" class="player-info__label-icon-img player-info__label-icon-img--music" alt="" loading="lazy"></span>Şarkı Adı :</span> <span class="now-playing__meta-value"><?= $this->song ?></span></h1>
        <p class="player-info__row player-info__album"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconCd) ?>" class="player-info__label-icon-img player-info__label-icon-img--cd" alt="" loading="lazy"></span>Albüm :</span> <span class="now-playing__meta-value"><?= $this->album ?></span></p>
        <p class="player-info__row player-info__singer"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconMic) ?>" class="player-info__label-icon-img player-info__label-icon-img--mic" alt="" loading="lazy"></span>Sanatçı :</span> <span class="now-playing__meta-value"><?= $this->artist ?></span></p>
        <p class="player-info__row player-info__star"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconStar) ?>" class="player-info__label-icon-img player-info__label-icon-img--star" alt="" loading="lazy"></span>Yıldız :</span> <span class="player-info__stars" role="img" aria-label="Değerlendirme: 5 üzerinden 5 yıldız"><img src="<?= $this->h($this->imgStar) ?>" alt="" loading="lazy"><img src="<?= $this->h($this->imgStar) ?>" alt="" loading="lazy"><img src="<?= $this->h($this->imgStar) ?>" alt="" loading="lazy"><img src="<?= $this->h($this->imgStar) ?>" alt="" loading="lazy"><img src="<?= $this->h($this->imgStar) ?>" alt="" loading="lazy"></span></p>
        <p class="player-info__row player-info__bitrate"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconBitrate) ?>" class="player-info__label-icon-img player-info__label-icon-img--bitrate" alt="" loading="lazy"></span>Bit rate :</span> <span class="now-playing__meta-value"><?= $this->bitrate ?></span><span class="player-info__badges"><img src="<?= $this->h($this->iconMp3) ?>" class="player-info__badge player-info__badge--mp3" alt="MP3" loading="lazy"><img src="<?= $this->h($this->iconMp4) ?>" class="player-info__badge player-info__badge--mp4" alt="MP4" loading="lazy"><img src="<?= $this->h($this->iconVlc) ?>" class="player-info__badge player-info__badge--vlc" alt="VLC" loading="lazy"></span></p>
        <p class="player-info__row player-info__duration"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconTimer) ?>" class="player-info__label-icon-img player-info__label-icon-img--timer" alt="" loading="lazy"></span>Süre :</span> <span class="now-playing__meta-value"><?= $this->elapsed ?> / <?= $this->duration ?></span></p>
        <div class="player-info__transport">
            <img src="<?= $this->h($this->iconPlay) ?>" class="player-info__text-img player-info__play" alt="Oynat" loading="lazy">
            <div class="player-info__progress" role="progressbar" aria-label="Medya ilerlemesi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $this->seekPct ?>">
                <div class="player-info__progress__bar"><div class="player-info__progress__fill" data-progress="<?= $this->seekPct ?>"></div></div>
            </div>
        </div>
    </div>
</section>
<?php else: ?>
<section class="now-playing now-playing--embedded" aria-label="Şu an çalan" data-cm-component="cm-player-info" data-cm-config='<?= $cmConfig ?>'>
    <div class="now-playing__art now-playing__art--embedded">
        <img src="<?= $this->h($this->imgCover) ?>" alt="Çalan şarkının albüm kapağı" loading="lazy">
    </div>
    <div class="now-playing__info now-playing__info--embedded">
        <h1 class="now-playing__title"><?= $this->song ?></h1>
        <p class="now-playing__subtitle"><?= $this->album ?></p>
        <p class="now-playing__artist"><?= $this->artist ?></p>
    </div>
    <div class="media-progress" role="progressbar" aria-label="Medya ilerlemesi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $this->seekPct ?>" aria-valuetext="<?= $this->elapsed ?> / <?= $this->duration ?>">
        <span class="media-progress__time" id="np_time_current"><?= $this->elapsed ?></span>
        <div class="media-progress__bar"><div class="media-progress__fill" data-progress="<?= $this->seekPct ?>"></div></div>
        <span class="media-progress__time media-progress__time--total" id="np_time_total"><?= $this->duration ?></span>
    </div>
</section>
<?php endif; ?>
```

## §5 Değişken Kullanım Envanteri (partial)

| Değişen | Ham echo | `h()` ile | cmConfig | Satır(lar) |
|---|---|---|---|---|
| `$this->song` | EVET (35, 55) | hayır | EVET (`strip_tags`, 19) | 19, 35, 55 |
| `$this->album` | EVET (36, 56) | hayır | hayır | 36, 56 |
| `$this->artist` | EVET (37, 57) | hayır | EVET (`strip_tags`, 20) | 20, 37, 57 |
| `$this->imgCover` | hayır | EVET (32, 52) | hayır | 32, 52 |
| `$this->seekPct` | EVET (int, 43-44, 59, 61) | gerekmez | EVET (21) | 21, 43, 44, 59, 61 |
| `$this->elapsed` | EVET (40, 60) | hayır | hayır | 40, 60 |
| `$this->duration` | EVET (40, 62) | hayır | hayır | 40, 62 |
| `$this->bitrate` | EVET (39) | hayır | hayır | 39 |
| `$this->icon*` (7 yol) | hayır | EVET | hayır | 35, 36, 37, 38, 39, 40, 42 |
| `$this->imgStar` | hayır | EVET (5×) | hayır | 38 |
| `$cmConfig` | hayır (atribüt) | EVET (üretimde 25) | — | 30, 50 |
| `$nonce` | KULLANILMIYOR | üretildi 15 | hayır | 15 |

### §5.1 Düzeltme Kaydı (anayasa §21 çelişkisi no 2)

> Bu blok okunduktan sonra iki iddia **düzeltildi** (append-only — eski satırlar anayasa'da durur):

| Eski iddia | Kanıt | Yeni durum |
|---|---|---|
| `$imgCover` kaçışsız `src` | partial 32, 52 → `$this->h($this->imgCover)` | **YANLIŞ** — imgCover kaçışlıdır |
| `current_art` kaçışlı mı? | `$this->h()` uygulanmış | **Evet** (partial 32/52) |

**Yerine geçen gerçek bulgu:** `$this->song`, `$this->album`, `$this->artist` metin gövdesinde **ham echo** ediliyor (35-37 · 55-57) — `h()` uygulanmamış. cmConfig içinde `strip_tags` var ama HTML gövde metni için `strip_tags` tam kaçış değildir. Yazar/niyet UNKNOWN → sahibine devredilir.

## §6 Erişilebilirlik ve Nitelik Envanteri (partial)

| # | Nitelik/rol | Satır | Değer |
|---|---|---|---|
| 1 | `aria-label` (section) | 30, 50 | "Şu an çalan" |
| 2 | `role="progressbar"` | 43, 59 | medya ilerlemesi |
| 3 | `aria-valuemin/max/now` | 43, 59 | 0 / 100 / seekPct |
| 4 | `aria-valuetext` | 59 | `elapsed / duration` |
| 5 | `role="img"` + `aria-label` (yıldız) | 38 | "5 üzerinden 5 yıldız" |
| 6 | `loading="lazy"` | tüm `<img>` | 12+ yerde |
| 7 | `alt` metinleri | 32, 42, 52 | kapak + "Oynat"; ikonlarda `alt=""` |
| 8 | `id="np_time_current"` / `np_time_total` | 60, 62 | JS kancaları |
| 9 | `data-progress` | 44, 61 | JS okuma yüzeyi |
| 10 | `data-cm-component` | 30, 50 | `cm-player-info` |
| 11 | `data-cm-config` | 30, 50 | tek tırnaklı atribüt |
| 12 | `$nonce` partial içinde KULLANILMADI | 15 | üretildi ama israf — gözlem |

## §7 İddia Kayıt Defteri (yeni URL yok)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Üç dosya da 1-son satır tam okundu | URL yok — 2026-10-13 read kanıtı | 2026-10-13 | geçerli |
| imgCover kaçışlı (§5.1 düzeltmesi) | URL yok — partial 32/52 kanıtı | 2026-10-13 | geçerli |
| song/album/artist ham echo | URL yok — partial 35-37/55-57 kanıtı | 2026-10-13 | geçerli (niyet UNKNOWN) |
| `php -l` çalıştı | çalıştırılmadı (runtime yok) | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |

**Yeni URL eklenmedi** — envanter kesişimi 0.

## §8 Bilinen Boşluklar

| # | Boşluk | Durum |
|---|---|---|
| 1 | `PlayerInfoComponent.js` (JS katmanı) | ⚠️ VERIFICATION REQUIRED |
| 2 | `pages/home.php` (çağrı sırası) | ⚠️ VERIFICATION REQUIRED |
| 3 | `RecentTracksComponent` ctor 2. param `null` anlamı | UNKNOWN |
| 4 | `HomeLayoutVariant::isWide()` implementasyonu | ⚠️ VERIFICATION REQUIRED |
| 5 | `$nonce` neden üretilip kullanılmadı | UNKNOWN |
| 6 | ham echo kararı bilinçli mi (escapesiz) | UNKNOWN |

## §9 Parçalı Blok — AbstractComponent `render` Ayrıştırması

> Bu blok, §2'deki tam dosyanın render omurgasının satır bazlı çözümüdür (tekrar değil, çözümleme).

| Satır | İfade | Etki |
|---|---|---|
| 34 | `final public function render(): string` | alt sınıf override edemez |
| 36 | `ob_start()` | tampon açılır |
| 37 | `try {` | tek yakalama bölgesi |
| 38 | `require $this->partial();` | partial include edilir — partial'daki `echo` tampona düşer |
| 39 | `return (string)ob_get_clean();` | tampon string'e çevrilip kapanır |
| 40 | `catch (\Throwable $e)` | her şey yakalanır (Error dahil) |
| 41 | `ob_end_clean();` | yarım HTML atılır |
| 42 | `throw $e;` | yeniden-fırlatma — bilgi kaybı yok |
| 43 | `}` | catch kapanışı |
| 44 | `}` | method kapanışı — string dönüş garantisi |
| 35 | `{` | method gövde açılışı — tek satır |
| 37-42 | try/catch bloğu tek seviye içeriktir | iç içe try YOK |

**Sonuç:** Partial `echo` ile çalışır; `render()` string döndürür. İkisi `display()`'de birleşir (46-49) — surucu §6 buffer matrisiyle birebir örtüşür.

## §10 Değişiklik Kaydı

| Tarih | Değişiklik | Kanıt |
|---|---|---|
| 2026-10-13 | İlk yazım — 3 tam dosya bloğu + 6 analiz tablosu + imgCover düzeltme kaydı | Vault Steward oturumu |
| 2026-10-13 | §9 render ayrıştırması + bayt değerleri get_file_info ile düzeltildi (3298/3874/6133) | Vault Steward oturumu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
