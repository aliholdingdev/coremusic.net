<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;

/**
 * WidgetGridComponent — Wide/4K üst satır "Diiv2 Button" widget kümesi
 *
 * Figma SSOT: node 2831:13747 (1920 Home) → "Div2 Button" grubu (id 2850:21494, 752×184)
 *   - traih Saat Widet        (id 2850:21512, 109×43)
 *   - EEquaizer Quick Acess   (id 2850:21519, 42×43)
 *   - Ses Kaynağı             (id 2850:21553, 173×55)
 *   - Hava Durmu              (id 2850:21545, 172×55)
 *   - Depelama Yöneticisi     (id 2850:21560, 173×43 — Figma'da visible:false, görev kapsamında dahil edildi)
 *   - Dosya Yönetici Btn      (id 2850:21539, 129×43)
 *   - Youtube Music Btn       (id 2850:21534, 42×43)
 *   - Youtube Btn             (id 2850:21529, 42×43)
 *   - Deezer Btn              (id 2850:21524, 42×43)
 *
 * Sadece WIDE/4K üst satırında render edilir (embedded'de Figma'da eşdeğer
 * "Div2 Button" kompozisyonu doğrulanamadı — R6, zorla eklenmedi).
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
    public readonly string $iconStorage;
    public readonly string $iconFolder;
    public readonly string $iconYoutubeMusic;
    public readonly string $iconYoutube;
    public readonly string $iconDeezer;

    public readonly string $clockTime;
    public readonly string $clockDate;
    public readonly string $weatherCondition;
    public readonly string $weatherCity;
    public readonly string $speakerLabel;
    public readonly string $speakerName;
    public readonly string $storageUsed;
    public readonly string $storageTotal;
    public readonly int    $storagePct;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);

        $this->iconClock        = $this->asset('/Image/res-pink/saat.png');
        $this->iconEqualizer    = $this->asset('/Image/res-pink/quick-bar/equalizer.png');
        $this->iconSpeaker      = $this->asset('/Image/res-pink/volume-high.png');
        $this->iconWeather      = $this->asset('/Image/res-pink/info-mavi.png');
        $this->iconStorage      = $this->asset('/Image/res-pink/disk/hard-disk-drive.png');
        $this->iconFolder       = $this->asset('/Image/res-pink/folder.png');
        $this->iconYoutubeMusic = $this->asset('/Image/res-pink/app/youtube-music.png');
        $this->iconYoutube      = $this->asset('/Image/res-pink/app/youtube.png');
        $this->iconDeezer       = $this->asset('/Image/res-pink/music-2.png');

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

        /* Depolama — statik placeholder */
        $this->storageUsed  = $this->h($this->sessionString('widget_storage_used', '32GB'));
        $this->storageTotal = $this->h($this->sessionString('widget_storage_total', '128GB'));
        $this->storagePct   = $this->sessionInt('widget_storage_pct', 25);
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
