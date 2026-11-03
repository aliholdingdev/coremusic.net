<?php declare(strict_types=1);

namespace CoreMusic\Home\Tests\Unit\Page;

use PHPUnit\Framework\TestCase;

/**
 * NewPagesTest — Yeni Sayfalar (Albümler, Sanatçılar, Albüm Detay, Ayarlar) Birim Testleri
 */
final class NewPagesTest extends TestCase
{
    private function renderPage(string $page, int $viewportW = 1024): string
    {
        \CoreMusic\Device\DeviceManager::resetInstance();
        $_SERVER['VIEWPORT_W'] = (string)$viewportW;
        $_SERVER['VIEWPORT_H'] = ($viewportW === 1024) ? '600' : '1080';
        $_SERVER['REQUEST_URI'] = '/' . $page;
        $_GET = [];

        ob_start();
        $pageFile = dirname(__DIR__, 3) . '/pages/' . $page . '.php';
        if (file_exists($pageFile)) {
            require $pageFile;
        }
        return (string)ob_get_clean();
    }

    public function testAlbumsPage_rendersLayoutAndSidebar(): void
    {
        $html = $this->renderPage('albums', 1024);

        $this->assertStringContainsString('albums-layout', $html);
        $this->assertStringContainsString('albums-header', $html);
        $this->assertStringContainsString('albums-tabs', $html);
        $this->assertStringContainsString('albums-grid', $html);
        $this->assertStringContainsString('album-card', $html);
        $this->assertStringContainsString('albums-empty-state', $html);
        $this->assertStringContainsString('albums-sidebar', $html);
        $this->assertStringContainsString('Nöbetçi Eczane Ferhat Kasetleri', $html);
    }

    public function testAlbumDetailPage_rendersLayoutAndTrackList(): void
    {
        $html = $this->renderPage('album-detail', 1024);

        $this->assertStringContainsString('album-detail-layout', $html);
        $this->assertStringContainsString('album-detail-tracks', $html);
        $this->assertStringContainsString('album-track-row', $html);
        $this->assertStringContainsString('album-tracks-empty-state', $html);
        $this->assertStringContainsString('album-detail-sidebar', $html);
        $this->assertStringContainsString('Hayat Rüya Gibi', $html);
        $this->assertStringContainsString('Sevil Neşelen', $html);
    }

    public function testArtistsPage_rendersLayoutAndCircularCards(): void
    {
        $html = $this->renderPage('artists', 1024);

        $this->assertStringContainsString('artists-layout', $html);
        $this->assertStringContainsString('artists-header', $html);
        $this->assertStringContainsString('artists-tabs', $html);
        $this->assertStringContainsString('artists-grid', $html);
        $this->assertStringContainsString('artist-card', $html);
        $this->assertStringContainsString('artists-empty-state', $html);
        $this->assertStringContainsString('artists-sidebar', $html);
        $this->assertStringContainsString('Sibel Can', $html);
        $this->assertStringContainsString('Dilso&#039;z', $html);
    }

    public function testSettingsPage_rendersLayoutAndSections(): void
    {
        $html = $this->renderPage('settings', 1024);

        $this->assertStringContainsString('settings-page', $html);
        $this->assertStringContainsString('settings-title', $html);
        $this->assertStringContainsString('settings-section', $html);
        $this->assertStringContainsString('settings-options', $html);
        $this->assertStringContainsString('Class AB Çıkış', $html);
        $this->assertStringContainsString('settings-section--eq', $html);
        $this->assertStringContainsString('Parametrik &amp; Grafik Hibrit EQ', $html);
        $this->assertStringContainsString('settings-eq-rack', $html);
        $this->assertStringContainsString('eq-fader--iso-highlight', $html);
        $this->assertStringContainsString('eq-preset-select', $html);
        $this->assertStringContainsString('eq-preamp-gain', $html);
        $this->assertStringContainsString('NEVA AUDIO DSP', $html);
        $this->assertStringContainsString('section-wifi-title', $html);
        $this->assertStringContainsString('section-bt-title', $html);
        $this->assertStringContainsString('CoreMusic_Studio_5G', $html);
        $this->assertStringContainsString('Sony WH-1000XM5', $html);
        $this->assertStringContainsString('settings-wifi-modal-overlay', $html);
        $this->assertStringContainsString('settings-wifi-modal', $html);
        $this->assertStringContainsString('wifi-password-input', $html);
        $this->assertStringContainsString('wifi-auto-connect', $html);
    }

    public function testSettingsPage_rendersWideLayout(): void
    {
        $html = $this->renderPage('settings', 1920);

        $this->assertStringContainsString('page-settings--wide', $html);
        $this->assertStringContainsString('settings-section--eq', $html);
    }

    public function testBrowsePage_rendersDriveGridAndExplorerView(): void
    {
        $html = $this->renderPage('browse', 1024);

        $this->assertStringContainsString('browse-glass', $html);
        $this->assertStringContainsString('browse-toolbar', $html);
        $this->assertStringContainsString('browse-disk-grid', $html);
        $this->assertStringContainsString('browse-disk-card', $html);
        $this->assertStringContainsString('Sistem Diskleri', $html);
        $this->assertStringContainsString('HDD &amp; SSD Diskleri', $html);
        $this->assertStringContainsString('USB Diskleri', $html);
        $this->assertStringContainsString('browse-empty-state', $html);
        $this->assertStringContainsString('browse-explorer-view', $html);
        $this->assertStringContainsString('browse-explorer__back-btn', $html);
        $this->assertStringContainsString('browse-explorer__path-text', $html);
        $this->assertStringContainsString('browse-explorer__tree', $html);
        $this->assertStringContainsString('browse-explorer__files', $html);
        $this->assertStringContainsString('explorer-empty-state', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
    }

    public function testPlaylistPage_rendersTrackListAndEmptyState(): void
    {
        $html = $this->renderPage('playlist', 1024);

        $this->assertStringContainsString('player-glass', $html);
        $this->assertStringContainsString('player-table', $html);
        $this->assertStringContainsString('player-empty-row', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
    }

    public function testDiskPlayerPage_rendersVinylPlayer(): void
    {
        $html = $this->renderPage('diskplayer', 1024);

        $this->assertStringContainsString('player-content--diskplayer', $html);
        $this->assertStringContainsString('player-visual--vinyl-center', $html);
        $this->assertStringContainsString('player-visual__vinyl', $html);
        $this->assertStringContainsString('player-mini-card', $html);
        $this->assertStringContainsString('player-playlist--wide', $html);
    }

    public function testKesfetPage_rendersHeroMoodsAndCharts(): void
    {
        $html = $this->renderPage('kesfet', 1024);

        $this->assertStringContainsString('kesfet-page', $html);
        $this->assertStringContainsString('kesfet-hero', $html);
        $this->assertStringContainsString('Sevil Neşelen', $html);
        $this->assertStringContainsString('kesfet-chips-bar', $html);
        $this->assertStringContainsString('kesfet-mood-card', $html);
        $this->assertStringContainsString('kesfet-charts-panel', $html);
        $this->assertStringContainsString('kesfet-track-row', $html);
        $this->assertStringContainsString('kesfet-sidebar-panel', $html);
    }

    public function testTerminalPage_rendersHeaderModesAndStream(): void
    {
        $html = $this->renderPage('terminal', 1024);

        $this->assertStringContainsString('terminal-page-layout', $html);
        $this->assertStringContainsString('terminal-header', $html);
        $this->assertStringContainsString('Core AI Terminal', $html);
        $this->assertStringContainsString('terminal-modes-bar', $html);
        $this->assertStringContainsString('data-terminal-mode="komutlar"', $html);
        $this->assertStringContainsString('data-terminal-mode="sistem"', $html);
        $this->assertStringContainsString('data-terminal-mode="aygitlar"', $html);
        $this->assertStringContainsString('terminal-stream', $html);
        $this->assertStringContainsString('AK4458 DAC OK, Class AB OK', $html);
        $this->assertStringContainsString('terminal-input-bar', $html);
        $this->assertStringContainsString('terminal-form', $html);
    }

    public function testWizardPage_rendersStepperPanesAndControls(): void
    {
        $html = $this->renderPage('wizard', 1024);

        $this->assertStringContainsString('page-wizard', $html);
        $this->assertStringContainsString('wizard-glass', $html);
        $this->assertStringContainsString('wizard-sidebar', $html);
        $this->assertStringContainsString('wizard-step-item', $html);
        $this->assertStringContainsString('Hoş Geldiniz', $html);
        $this->assertStringContainsString('Cinsiyet &amp; Enerji', $html);
        $this->assertStringContainsString('Lisans Sözleşmesi', $html);
        $this->assertStringContainsString('wizard-panes', $html);
        $this->assertStringContainsString('wizard-pane', $html);
        $this->assertStringContainsString('wizard-prev-btn', $html);
        $this->assertStringContainsString('wizard-next-btn', $html);
    }

    public function testTerminalPage_rendersWideTelemetrySidebar(): void
    {
        $html = $this->renderPage('terminal', 1920);

        $this->assertStringContainsString('terminal-page-layout--wide', $html);
        $this->assertStringContainsString('terminal-telemetry-sidebar', $html);
        $this->assertStringContainsString('SİSTEM TELEMETRİSİ', $html);
        $this->assertStringContainsString('AUDIO DSP TELEMETRİ', $html);
        $this->assertStringContainsString('AK4458 (32-bit)', $html);
        $this->assertStringContainsString('Class AB Pure', $html);
        $this->assertStringContainsString('ŞU AN ÇALAN', $html);
    }

    public function testBrowsePage_rendersWideSidebarAndToolbarOpenBtn(): void
    {
        $html = $this->renderPage('browse', 1920);

        $this->assertStringContainsString('page-browse--wide', $html);
        $this->assertStringContainsString('btn-browse-open', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
    }

    public function testAlbumsPage_rendersWideLayoutAndWideSidebar(): void
    {
        $html = $this->renderPage('albums', 1920);

        $this->assertStringContainsString('page-albums--wide', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
    }

    public function testAlbumDetailPage_rendersWideLayoutAndWideSidebar(): void
    {
        $html = $this->renderPage('album-detail', 1920);

        $this->assertStringContainsString('page-album-detail--wide', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
    }

    public function testArtistsPage_rendersWideLayoutAndWideSidebar(): void
    {
        $html = $this->renderPage('artists', 1920);

        $this->assertStringContainsString('page-artists--wide', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
    }

    public function testPlaylistPage_rendersWideLayout(): void
    {
        $html = $this->renderPage('playlist', 1920);

        $this->assertStringContainsString('page-player--wide', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
        $this->assertStringContainsString('page-sidebar__status-card', $html);
    }

    public function testPlaylistPage_rendersFullscreenWideLayout(): void
    {
        \CoreMusic\Device\DeviceManager::resetInstance();
        $_SERVER['VIEWPORT_W'] = '1920';
        $_SERVER['VIEWPORT_H'] = '1080';
        $_SERVER['REQUEST_URI'] = '/playlist';
        $_GET = ['mode' => 'fullscreen'];

        ob_start();
        $pageFile = dirname(__DIR__, 3) . '/pages/playlist.php';
        require $pageFile;
        $html = (string)ob_get_clean();

        $this->assertStringContainsString('page-player--fullscreen', $html);
        $this->assertStringContainsString('player-playlist--wide', $html);
    }

    public function testPlaylistPage_renders4kLayout(): void
    {
        $html = $this->renderPage('playlist', 3840);

        $this->assertStringContainsString('page-player--4k', $html);
        $this->assertStringContainsString('page-player--wide', $html);
        $this->assertStringContainsString('player-glass', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
    }

    public function testBrowsePage_renders4kLayout(): void
    {
        $html = $this->renderPage('browse', 3840);

        $this->assertStringContainsString('page-browse--4k', $html);
        $this->assertStringContainsString('browse-glass', $html);
        $this->assertStringContainsString('browse-disk-grid', $html);
    }

    public function testHistoryPage_renders4kLayout(): void
    {
        $html = $this->renderPage('history', 3840);

        $this->assertStringContainsString('page-history--4k', $html);
        $this->assertStringContainsString('page-history--wide', $html);
        $this->assertStringContainsString('history-glass', $html);
    }

    public function testAlbumsPage_renders4kLayout(): void
    {
        $html = $this->renderPage('albums', 3840);

        $this->assertStringContainsString('page-albums--4k', $html);
        $this->assertStringContainsString('albums-grid', $html);
    }
}

