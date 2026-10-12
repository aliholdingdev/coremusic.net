<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;

/**
 * WidgetGridComponent — Üst satır "Div2 Button" widget kümesi (embedded + wide/4K)
 *
 * Figma SSOT: node 2831:13747 (1920) → "Div2 Button" (id 2850:21494, 752×184)
 *              node 1639:10160 (1024) → aynı kompozisyon 365×171
 *   - Ses Kaynağı             (id 2850:21553, 173×55)  row1
 *   - Hava Durmu              (id 2850:21545, 172×55)  row1
 *   - traih Saat Widet        (id 2850:21512, 109×43)  row2
 *   - EEquaizer Quick Acess   (id 2850:21519, 42×43)   row2
 *   - Dosya Yönetici Btn      (id 2850:21539, 129×43)  row3
 *   - Youtube Music Btn       (id 2850:21534, 42×43)   row3
 *   - Youtube Btn             (id 2850:21529, 42×43)   row3
 *   - Favori (kalp)           (id 2850:21524, 42×43)   row3 — PNG SSOT ikonu
 *   - Bit Hızı (dalga)        (id 2850:21495, 42×43)   row3 — PNG SSOT ikonu
 *
 * Slot sayımı (aaa.md §13.9 — çelişkide kural PNG/Figma'yı ezer):
 *   embedded 12 → row1 2 · row2 5 · row3 5
 *   wide/4K  20 → row1 4 · row2 8 · row3 8
 * Boş slotlar görünür cam kart (.widget-card--empty) olarak üretilir.
 * "Depelama Yöneticisi" Figma'da visible:false → üretilmez (eski kapsam kararı geçersiz).
 *
 * View partial: pages/components/widget-grid.php
 * JS: assets.coremusic.net/js/components/composites/WidgetAreaComponent.js (saat/tarih tick)
 */
final class WidgetGridComponent extends AbstractComponent
{
    public readonly string $iconClock;
    public readonly string $iconEqualizer;
    public readonly string $iconSpeaker;
    public readonly string $iconWeather;
    public readonly string $iconFolder;
    public readonly string $iconYoutubeMusic;
    public readonly string $iconYoutube;
    public readonly string $iconFavori;
    public readonly string $iconBitrate;

    public readonly string $clockTime;
    public readonly string $clockDate;
    public readonly string $weatherCondition;
    public readonly string $weatherCity;
    public readonly string $speakerLabel;
    public readonly string $speakerName;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);

        $this->iconClock        = $this->asset('/Image/res-pink/saat.png');
        $this->iconEqualizer    = $this->asset('/Image/res-pink/quick-bar/equalizer.png');
        $this->iconSpeaker      = $this->asset('/Image/res-pink/volume-high.png');
        $this->iconWeather      = $this->asset('/Image/res-pink/info-mavi.png');
        $this->iconFolder       = $this->asset('/Image/res-pink/folder.png');
        $this->iconYoutubeMusic = $this->asset('/Image/res-pink/app/youtube-music.png');
        $this->iconYoutube      = $this->asset('/Image/res-pink/app/youtube.png');
        $this->iconFavori       = $this->asset('/Image/res-pink/actions/favori.png');
        $this->iconBitrate      = $this->asset('/Image/res-pink/bit-rate.png');

        /* Saat/tarih — sunucu render değeri; JS (WidgetAreaComponent) client-side günceller */
        $now = new \DateTimeImmutable('now');
        $this->clockTime = $this->h($this->sessionString('widget_clock_time', $now->format('H:i')));
        $this->clockDate = $this->h($this->sessionString('widget_clock_date', $this->turkishDate($now)));

        /* Hava durumu — statik placeholder (gerçek API entegrasyonu kapsam dışı, R5) */
        $this->weatherCondition = $this->h($this->sessionString('widget_weather_condition', 'Güneşli 13°'));
        $this->weatherCity      = $this->h($this->sessionString('widget_weather_city', 'İstanbul'));

        /* Ses kaynağı — statik placeholder */
        $this->speakerLabel = $this->h($this->sessionString('widget_speaker_label', 'Hoparlör'));
        $this->speakerName  = $this->h($this->sessionString('widget_speaker_name', 'Core Music - Hoparlör'));
    }

    public function key(): string
    {
        return 'widget-grid';
    }

    /** Figma metni "5 Haziran 2026" formatına uygun TR tarih. */
    private function turkishDate(\DateTimeImmutable $date): string
    {
        $months = [
            1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan', 5 => 'Mayıs', 6 => 'Haziran',
            7 => 'Temmuz', 8 => 'Ağustos', 9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık',
        ];

        return sprintf('%d %s %d', (int)$date->format('j'), $months[(int)$date->format('n')], (int)$date->format('Y'));
    }
}
