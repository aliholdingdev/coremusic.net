<?php declare(strict_types=1);
/**
 * pages/terminal.php — Core AI Terminal & Diagnostics Console
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 04_Components (c-terminal.css)
 * SSOT     : Figma node 2135:24715 (Linux 1024 - Terminal)
 * Route    : /terminal
 * ------------------------------------------------------------------
 */

use CoreMusic\Device\DeviceManager;

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}

$is4k    = $dm->is4k();
$isWide  = $dm->shouldRenderWideLayout() || $is4k;

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : 'http://assets.coremusic.net';
$cspNonce = (string)($_SESSION['csp_nonce'] ?? '');
$nonceAttr = $cspNonce !== '' ? ' nonce="' . $h($cspNonce) . '"' : '';
?>

<div class="terminal-page-layout <?= $isWide ? 'terminal-page-layout--wide' : '' ?> page-layout <?= $h($dm->allClasses()) ?>" <?= $dm->dataAttributes() ?>>
    <div class="terminal-main-console" style="display:flex; flex-direction:column; min-width:0; flex:1;">
    <!-- Terminal Header (Figma 2135:24773) -->
    <header class="terminal-header">
        <div class="terminal-title-group">
            <span class="terminal-status-dot" aria-hidden="true"></span>
            <h1 class="terminal-title">Core AI Terminal</h1>
        </div>
        <a href="/home" class="terminal-close-btn" aria-label="Terminalden çık" title="Ana Sayfaya Dön" style="text-decoration:none;">✕</a>
    </header>

    <!-- Mod Butonları (Figma 2135:24757) -->
    <nav class="terminal-modes-bar" aria-label="Terminal Çalışma Modları">
        <button type="button" class="terminal-mode-btn is-active" data-terminal-mode="komutlar">Komutlar</button>
        <button type="button" class="terminal-mode-btn" data-terminal-mode="sistem">Sistem</button>
        <button type="button" class="terminal-mode-btn" data-terminal-mode="ag">Ağ</button>
        <button type="button" class="terminal-mode-btn" data-terminal-mode="aygitlar">Aygıtlar</button>
        <button type="button" class="terminal-mode-btn" data-terminal-mode="medya">Medya</button>
    </nav>

    <!-- Akış / Stream Konsol Alanı (Figma 2135:24744 - 24756) -->
    <div class="terminal-stream" id="terminal-stream" role="log" aria-live="polite">
        <div class="terminal-line">
            <span class="terminal-time">22:14:00</span>
            <span class="terminal-tag">Core AI</span>
            <span class="terminal-msg">AI Sistemi Başlatılıyor....</span>
        </div>
        <div class="terminal-line">
            <span class="terminal-time">22:14:01</span>
            <span class="terminal-tag">Core AI</span>
            <span class="terminal-msg">Tüm donanım ve DSP modülleri kontrol ediliyor (AK4458 DAC OK, Class AB OK).</span>
        </div>
        <div class="terminal-line">
            <span class="terminal-time">22:14:02</span>
            <span class="terminal-tag">Core AI</span>
            <span class="terminal-msg">Kütüphane taraması tamamlandı: 8 sürücü bağlı, 1.420 kayıpsız parça indekslendi.</span>
        </div>
        <div class="terminal-line">
            <span class="terminal-time">22:14:03</span>
            <span class="terminal-tag">Core AI</span>
            <span class="terminal-msg">AI Sistemi hazır. Merhaba, Bayram Ali. Nasıl yardımcı olabilirim?</span>
        </div>

        <!-- Örnek Komut 1: Şarkı Çalma -->
        <div class="terminal-line">
            <span class="terminal-time">22:14:15</span>
            <span class="terminal-tag terminal-tag--user">Bayram</span>
            <span class="terminal-msg">Göksel sevil neşelen çal</span>
        </div>
        <div class="terminal-line">
            <span class="terminal-time">22:14:16</span>
            <span class="terminal-tag">Core AI</span>
            <span class="terminal-msg">Komut analiz edildi. Göksel sanatçısı bulundu &mdash; Sevil Neşelen parçası yürütülüyor.</span>
        </div>
        <div class="terminal-card terminal-card--song">
            <div class="terminal-card-row">
                <img class="terminal-card-cover" src="<?= $assetsUrl ?>/Image/res-pink/album-goksel.png" alt="Göksel" width="36" height="36">
                <div class="terminal-card-info">
                    <div class="terminal-card-title">Göksel &mdash; Sevil Neşelen</div>
                    <div class="terminal-card-sub">Hayat Rüya Gibi &bull; FLAC 24-bit / 96 kHz</div>
                </div>
                <span style="font-size:11px; color:var(--cm-primary); font-weight:700;">Çalıyor</span>
            </div>
            <div class="terminal-progress-bar">
                <div class="terminal-progress-fill"></div>
            </div>
        </div>

        <!-- Örnek Komut 2: Ses Seviyesi -->
        <div class="terminal-line">
            <span class="terminal-time">22:14:32</span>
            <span class="terminal-tag terminal-tag--user">Bayram</span>
            <span class="terminal-msg">Ses seviyesini 50 yap</span>
        </div>
        <div class="terminal-line">
            <span class="terminal-time">22:14:33</span>
            <span class="terminal-tag">Core AI</span>
            <span class="terminal-msg">Analog master ses kazancı ayarlandı. Ses seviyesi %50 olarak güncellendi.</span>
        </div>
        <div class="terminal-card" style="display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:12px; font-weight:600;">Ses Seviyesi</span>
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:120px; height:4px; background:rgba(255,255,255,0.15); border-radius:2px; overflow:hidden;">
                    <div style="width:50%; height:100%; background:var(--cm-primary);"></div>
                </div>
                <span style="font-size:11px; font-weight:700; color:var(--cm-primary);">%50</span>
            </div>
        </div>
    </div>

    <!-- Komut Satırı Girişi (Figma 2135:24724) -->
    <form class="terminal-input-bar" id="terminal-form" autocomplete="off">
        <span class="terminal-prompt-symbol">$</span>
        <input type="text" class="terminal-input" id="terminal-input" placeholder="Komut yazın... (Örn: çal Göksel, ses 80, wifi status, help)" aria-label="Terminal komut girişi" required>
        <button type="submit" class="terminal-send-btn">Çalıştır</button>
    </form>
    </div><!-- /.terminal-main-console -->

    <?php if ($isWide): ?>
        <!-- ════════════════════════════════════════════════════════════
             SAĞ PANEL: Core AI Telemetri ve Ses Motoru Monitörü
             Figma node 1760:14599 (Core AI Dashboard Variant 1)
             ════════════════════════════════════════════════════════════ -->
        <aside class="terminal-telemetry-sidebar" aria-label="Core AI Sistem ve Donanım Telemetrisi">
            <!-- Hardware Metrics (Figma 1760:14803) -->
            <div class="terminal-telemetry-card">
                <div class="terminal-telemetry-header">
                    <span class="terminal-telemetry-title">SİSTEM TELEMETRİSİ</span>
                    <span class="terminal-status-badge">ONLINE</span>
                </div>
                <div class="terminal-meter-row">
                    <div class="terminal-meter-label">CPU</div>
                    <div class="terminal-meter-bar-wrap">
                        <div class="terminal-meter-bar" style="width: 12%;"></div>
                    </div>
                    <div class="terminal-meter-val">12%</div>
                </div>
                <div class="terminal-meter-row">
                    <div class="terminal-meter-label">GPU</div>
                    <div class="terminal-meter-bar-wrap">
                        <div class="terminal-meter-bar" style="width: 45%;"></div>
                    </div>
                    <div class="terminal-meter-val">45%</div>
                </div>
                <div class="terminal-meter-row">
                    <div class="terminal-meter-label">AI LOAD</div>
                    <div class="terminal-meter-bar-wrap">
                        <div class="terminal-meter-bar" style="width: 60%; background: var(--cm-gradient-primary);"></div>
                    </div>
                    <div class="terminal-meter-val">60%</div>
                </div>
                <div class="terminal-meter-row">
                    <div class="terminal-meter-label">RAM</div>
                    <div class="terminal-meter-bar-wrap">
                        <div class="terminal-meter-bar" style="width: 22%;"></div>
                    </div>
                    <div class="terminal-meter-val">1.8 / 8 GB</div>
                </div>
            </div>

            <!-- Audio DSP Telemetry (Figma 1760:14777 - 14791) -->
            <div class="terminal-telemetry-card">
                <div class="terminal-telemetry-header">
                    <span class="terminal-telemetry-title">AUDIO DSP TELEMETRİ</span>
                    <span class="terminal-badge--hi-res">HI-RES</span>
                </div>
                <div class="terminal-stat-grid">
                    <div class="terminal-stat-cell">
                        <span class="terminal-stat-k">DAC / CODEC</span>
                        <span class="terminal-stat-v">AK4458 (32-bit)</span>
                    </div>
                    <div class="terminal-stat-cell">
                        <span class="terminal-stat-k">TOPOLOJİ</span>
                        <span class="terminal-stat-v">Class AB Pure</span>
                    </div>
                    <div class="terminal-stat-cell">
                        <span class="terminal-stat-k">ÖRNEKLEME</span>
                        <span class="terminal-stat-v">96 kHz / 24-bit</span>
                    </div>
                    <div class="terminal-stat-cell">
                        <span class="terminal-stat-k">TAMPON / GECİKME</span>
                        <span class="terminal-stat-v">512 smp / 5.2 ms</span>
                    </div>
                    <div class="terminal-stat-cell">
                        <span class="terminal-stat-k">SNR / THD+N</span>
                        <span class="terminal-stat-v">&gt;105 dB / 0.003%</span>
                    </div>
                    <div class="terminal-stat-cell">
                        <span class="terminal-stat-k">MASTER GAIN</span>
                        <span class="terminal-stat-v">100% (0 dBFS)</span>
                    </div>
                </div>
            </div>

            <!-- Now Playing Mini Card (Figma 1760:14628 - 14638) -->
            <div class="terminal-telemetry-card">
                <div class="terminal-telemetry-header">
                    <span class="terminal-telemetry-title">ŞU AN ÇALAN</span>
                    <span class="terminal-badge--live">CANLI</span>
                </div>
                <div class="terminal-nowplaying-row">
                    <img src="<?= $h($assetsUrl) ?>/Image/res-pink/album-goksel.png" width="44" height="44" alt="Göksel" class="terminal-nowplaying-img">
                    <div class="terminal-nowplaying-info">
                        <div class="terminal-nowplaying-title">Göksel &mdash; Sevil Neşelen</div>
                        <div class="terminal-nowplaying-album">Hayat Rüya Gibi</div>
                        <div class="terminal-nowplaying-meta">350 kbps &bull; FLAC 24-bit Lossless</div>
                    </div>
                </div>
            </div>
        </aside>
    <?php endif; ?>
</div>

<script<?= $nonceAttr ?>>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('terminal-form');
    const input = document.getElementById('terminal-input');
    const stream = document.getElementById('terminal-stream');

    function getTimeStr() {
        const d = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        return pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
    }

    function appendLine(tag, msg, isUser = false) {
        const line = document.createElement('div');
        line.className = 'terminal-line';
        line.innerHTML = `
            <span class="terminal-time">${getTimeStr()}</span>
            <span class="terminal-tag ${isUser ? 'terminal-tag--user' : ''}">${tag}</span>
            <span class="terminal-msg">${msg}</span>
        `;
        stream.appendChild(line);
        stream.scrollTop = stream.scrollHeight;
    }

    if (form && input && stream) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const cmd = input.value.trim();
            if (!cmd) return;

            appendLine('Bayram', cmd, true);
            input.value = '';

            const lower = cmd.toLowerCase();

            setTimeout(() => {
                if (lower.includes('amfi') || lower.includes('amp') || lower.includes('amplifier')) {
                    appendLine('Core AI', '<b>[DONANIM] Discrete Class AB Güç Amplifikatörü</b><br>' +
                        '&bull; Topoloji: Saf Discrete Class AB (Push-Pull MJL21194 / MJL21193)<br>' +
                        '&bull; Boost Regülatör: LM5122 Senkron Çift Faz (Ray Voltajı: &plusmn;38.5V DC)<br>' +
                        '&bull; Sükunet Akımı (Bias): 45 mA &bull; DC Offset: +0.02 mV (Röle Koruması: &lt;0.5V OK)<br>' +
                        '&bull; Soğutucu Sıcaklığı: 42.4&deg;C &bull; SNR: 108.2 dB &bull; THD+N: 0.0024% @ 1kHz<br>' +
                        '&bull; Çıkış Gücü: 65W RMS / 8&Omega; (Kanal Başına Dinamik Rezerv)');
                } else if (lower.includes('telemetri') || lower.includes('telemetry') || lower.includes('sensör')) {
                    appendLine('Core AI', '<b>[TELEMETRİ] Sistem & Donanım Gerçek Zamanlı Veri</b><br>' +
                        '&bull; Voltaj Rayları: +38.5V / -38.5V / +5.0V / +3.3V / +1.2V Core (Tolerans &lt;%0.5)<br>' +
                        '&bull; Akım Tüketimi: 1.25A Bekleme / 3.8A Tepe Dinamik<br>' +
                        '&bull; Termal: Amfi Bloğu 42.4&deg;C &bull; DAC 38.1&deg;C &bull; DSP SoC 44.0&deg;C<br>' +
                        '&bull; Akustik Soğutma: Pasif Doğal Konveksiyon (0 dB Fan Gürültüsü)');
                } else if (lower.includes('dsp') || lower.includes('neva') || lower.includes('dac')) {
                    appendLine('Core AI', '<b>[DSP & DAC] Neva Audio Engine C++20 Çekirdek</b><br>' +
                        '&bull; DAC & Codec: AKM AK4458VN 32-bit / TI PCM3168A 24-bit 96kHz<br>' +
                        '&bull; Filtreleme: 31-Bant ISO 266 1/3-Oktav Biquad Peaking EQ (Direct Form II)<br>' +
                        '&bull; Tampon (Ring Buffer): Lock-Free Atomic Çift Yönlü (512 smp / 5.2ms gecikme)<br>' +
                        '&bull; Akış Sağlığı: 0 Drop, 0 Underrun, Bit-Perfect Kayıpsız FLAC Yürütme');
                } else if (lower.includes('çal') || lower.includes('play')) {
                    appendLine('Core AI', `"${cmd}" komutu işlendi. İlgili parça kütüphanede bulundu ve oynatıcı boru hattına aktarıldı.`);
                } else if (lower.includes('ses') || lower.includes('vol')) {
                    const numMatch = lower.match(/\d+/);
                    const vol = numMatch ? numMatch[0] : '75';
                    appendLine('Core AI', `Master analog ses çıkışı %${vol} seviyesine getirildi.`);
                } else if (lower.includes('wifi') || lower.includes('ağ') || lower.includes('net')) {
                    appendLine('Core AI', 'Ağ Durumu: Wi-Fi bağlı (5GHz - Sinyal: %100 - SSID: Bayram Ali - Home - IP: 192.168.1.105)');
                } else if (lower.includes('status') || lower.includes('durum')) {
                    appendLine('Core AI', 'Sistem Durumu: Çekirdek v3.2.0 | CPU: %12 | RAM: 1.8GB / 8GB | DSP: Kilitlendi | THD+N: 0.003%');
                } else if (lower.includes('help') || lower.includes('yardım')) {
                    appendLine('Core AI', 'Kullanılabilir komutlar: amfi, telemetri, dsp, çal [şarkı/sanatçı], ses [0-100], wifi, status, clear, reboot');
                } else if (lower === 'clear' || lower === 'temizle') {
                    stream.innerHTML = '';
                    appendLine('Core AI', 'Terminal akışı temizlendi.');
                } else {
                    appendLine('Core AI', `"${cmd}" komutu algılandı. AI alt sistemi bu komutu değerlendirdi: İşlem onaylandı.`);
                }
            }, 300);
        });
    }

    // Mod butonları
    const modeBtns = document.querySelectorAll('.terminal-mode-btn');
    modeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modeBtns.forEach(b => b.classList.remove('is-active'));
            this.classList.add('is-active');
            const mode = this.getAttribute('data-terminal-mode') || '';
            appendLine('Sistem', `Terminal çalışma modu değiştirildi: [${mode.toUpperCase()}]`);
            if (mode === 'sistem') {
                appendLine('Sistem', 'CPU: %12 (4 Çekirdek ARM64) &bull; RAM: 1.8GB / 8GB (%22) &bull; Soğutucu: 42.4&deg;C &bull; DC Raylar: &plusmn;38.5V Normal');
            } else if (mode === 'ag') {
                appendLine('Ağ', 'Wi-Fi: 5GHz Aktif (SSID: Bayram Ali - Home, %100 sinyal, WPA3) &bull; IP: 192.168.1.105');
            } else if (mode === 'aygitlar') {
                appendLine('Aygıtlar', 'AK4458 DAC: Hazır &bull; Class AB Amfi: Push-Pull Aktif (Bias 45mA) &bull; XMOS XU316: Bağlı &bull; PCM3168A: OK');
            } else if (mode === 'medya') {
                appendLine('Medya', '8 Sürücü / 1.420 FLAC Parça İndekslendi &bull; Çalma Motoru: Neva C++20 Ring Buffer Hazır');
            }
        });
    });
});
</script>
