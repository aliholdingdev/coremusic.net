<?php declare(strict_types=1);
/**
 * CoreMusic Home — Ayarlar Sayfası
 * Tema ayarları: Dark/Light mode + Gender teması
 * Version: 1.0.0 — 2026-09-01
 */

$currentUser = $_SESSION['MM_Username'] ?? 'Misafir';
$gender = $_SESSION['cm_gender'] ?? $_SESSION['MM_Gender'] ?? 'neutral';
$colorMode = $_SESSION['cm_color_mode'] ?? null;
?>
<main class="settings-page" role="main">
    <div class="settings-container">
        <h1 class="settings-title">Ayarlar</h1>

        <!-- ============================================================
             COLOR MODE (DARK/LIGHT)
             ============================================================ -->
        <section class="settings-section">
            <h2 class="settings-section__title">Tema Modu</h2>
            <p class="settings-section__desc">Koyu veya aydınlık tema seçin. OS ayarını otomatik olarak takip edebilirsiniz.</p>

            <div class="settings-options" role="radiogroup" aria-label="Tema modu">
                <!-- Otomatik (OS) -->
                <button
                    class="settings-option <?= $colorMode === null ? 'settings-option--active' : '' ?>"
                    data-action="set-mode"
                    data-mode=""
                    role="radio"
                    aria-checked="<?= $colorMode === null ? 'true' : 'false' ?>"
                >
                    <span class="settings-option__icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    </span>
                    <span class="settings-option__label">Otomatik</span>
                    <span class="settings-option__desc">OS ayarını takip et</span>
                </button>

                <!-- Dark -->
                <button
                    class="settings-option <?= $colorMode === 'dark' ? 'settings-option--active' : '' ?>"
                    data-action="set-mode"
                    data-mode="dark"
                    role="radio"
                    aria-checked="<?= $colorMode === 'dark' ? 'true' : 'false' ?>"
                >
                    <span class="settings-option__icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>
                    </span>
                    <span class="settings-option__label">Koyu</span>
                    <span class="settings-option__desc">Karanlık tema</span>
                </button>

                <!-- Light -->
                <button
                    class="settings-option <?= $colorMode === 'light' ? 'settings-option--active' : '' ?>"
                    data-action="set-mode"
                    data-mode="light"
                    role="radio"
                    aria-checked="<?= $colorMode === 'light' ? 'true' : 'false' ?>"
                >
                    <span class="settings-option__icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0 .39-.39.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96c.39-.39.39-1.03 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36c.39-.39.39-1.03 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z"/></svg>
                    </span>
                    <span class="settings-option__label">Aydınlık</span>
                    <span class="settings-option__desc">Beyaz tema</span>
                </button>
            </div>
        </section>

        <!-- ============================================================
             GENDER THEME
             ============================================================ -->
        <section class="settings-section">
            <h2 class="settings-section__title">Renk Teması</h2>
            <p class="settings-section__desc">Kişisel renk tercihinizi seçin.</p>

            <div class="settings-options" role="radiogroup" aria-label="Renk teması">
                <!-- Neutral -->
                <button
                    class="settings-option <?= $gender === 'neutral' ? 'settings-option--active' : '' ?>"
                    data-action="set-gender"
                    data-gender="neutral"
                    role="radio"
                    aria-checked="<?= $gender === 'neutral' ? 'true' : 'false' ?>"
                >
                    <span class="settings-option__swatch" style="background: linear-gradient(135deg, #b8a9c9, #8b6bae);"></span>
                    <span class="settings-option__label">Nötr</span>
                    <span class="settings-option__desc">Lavanta</span>
                </button>

                <!-- Female -->
                <button
                    class="settings-option <?= $gender === 'female' ? 'settings-option--active' : '' ?>"
                    data-action="set-gender"
                    data-gender="female"
                    role="radio"
                    aria-checked="<?= $gender === 'female' ? 'true' : 'false' ?>"
                >
                    <span class="settings-option__swatch" style="background: linear-gradient(135deg, #e8b4b8, #d496a0);"></span>
                    <span class="settings-option__label">Kadın</span>
                    <span class="settings-option__desc">Pembe</span>
                </button>

                <!-- Male -->
                <button
                    class="settings-option <?= $gender === 'male' ? 'settings-option--active' : '' ?>"
                    data-action="set-gender"
                    data-gender="male"
                    role="radio"
                    aria-checked="<?= $gender === 'male' ? 'true' : 'false' ?>"
                >
                    <span class="settings-option__swatch" style="background: linear-gradient(135deg, #5b8fb9, #89c4d9);"></span>
                    <span class="settings-option__label">Erkek</span>
                    <span class="settings-option__desc">Mavi</span>
                </button>
            </div>
        </section>
    </div>
</main>

<!-- Settings JS -->
<script type="module">
    import EventBus from '/js/core/EventBus.js';

    const eventBus = new EventBus();

    // Mode toggle
    document.querySelectorAll('[data-action="set-mode"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const mode = btn.dataset.mode || null;

            // UI güncelle
            document.querySelectorAll('[data-action="set-mode"]').forEach(b => {
                b.classList.remove('settings-option--active');
                b.setAttribute('aria-checked', 'false');
            });
            btn.classList.add('settings-option--active');
            btn.setAttribute('aria-checked', 'true');

            // Mode uygula
            if (mode === null) {
                document.documentElement.removeAttribute('data-mode');
                document.cookie = 'cm_color_mode=; path=/; domain=.coremusic.net; max-age=0; samesite=Lax';
            } else {
                document.documentElement.setAttribute('data-mode', mode);
                document.cookie = `cm_color_mode=${mode}; path=/; domain=.coremusic.net; max-age=31536000; samesite=Lax`;
            }

            // Server'a kaydet
            fetch('/api/settings/mode', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mode }),
            }).catch(() => {});
        });
    });

    // Gender toggle
    document.querySelectorAll('[data-action="set-gender"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const gender = btn.dataset.gender;

            // UI güncelle
            document.querySelectorAll('[data-action="set-gender"]').forEach(b => {
                b.classList.remove('settings-option--active');
                b.setAttribute('aria-checked', 'false');
            });
            btn.classList.add('settings-option--active');
            btn.setAttribute('aria-checked', 'true');

            // Gender uygula
            document.documentElement.setAttribute('data-gender', gender);
            document.cookie = `cm_gender=${gender}; path=/; domain=.coremusic.net; max-age=31536000; samesite=Lax`;

            // Theme token'larını uygula
            const themes = {
                female: { '--accent': '#ff4fd8', '--accent-hover': '#ff7ae3', '--accent-soft': 'rgba(255,79,216,0.15)', '--glass-bg': 'rgba(255,79,216,0.08)' },
                male: { '--accent': '#4f8fff', '--accent-hover': '#7ab0ff', '--accent-soft': 'rgba(79,143,255,0.15)', '--glass-bg': 'rgba(79,143,255,0.08)' },
                neutral: { '--accent': '#a855f7', '--accent-hover': '#c084fc', '--accent-soft': 'rgba(168,85,247,0.15)', '--glass-bg': 'rgba(168,85,247,0.08)' },
            };

            if (themes[gender]) {
                Object.entries(themes[gender]).forEach(([key, value]) => {
                    document.documentElement.style.setProperty(key, value);
                });
            }

            // Server'a kaydet
            fetch('/api/settings/gender', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ gender }),
            }).catch(() => {});
        });
    });
</script>

<style>
/* Settings Page Styles */
.settings-page {
    padding: var(--content-padding-top, 11px) var(--page-padding-x, 16px);
    max-width: 600px;
    margin: 0 auto;
}

.settings-title {
    font-family: var(--font-body, 'Arima', sans-serif);
    font-size: var(--text-xl, 16px);
    font-weight: var(--font-bold, 700);
    color: var(--text-primary);
    margin-bottom: var(--section-gap, 16px);
}

.settings-section {
    margin-bottom: var(--section-gap, 24px);
    padding: var(--card-padding, 12px);
    background: var(--bg-surface);
    border-radius: 12px;
    border: 1px solid var(--border-subtle);
}

.settings-section__title {
    font-family: var(--font-body, 'Arima', sans-serif);
    font-size: var(--text-lg, 14px);
    font-weight: var(--font-semibold, 600);
    color: var(--text-primary);
    margin: 0 0 4px 0;
}

.settings-section__desc {
    font-family: var(--font-body, 'Arima', sans-serif);
    font-size: var(--text-sm, 11px);
    color: var(--text-muted);
    margin: 0 0 var(--grid-gap, 8px) 0;
}

.settings-options {
    display: flex;
    gap: var(--grid-gap, 8px);
    flex-wrap: wrap;
}

.settings-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: var(--grid-gap, 8px) var(--grid-gap-lg, 16px);
    background: var(--bg-elevated);
    border: 2px solid transparent;
    border-radius: 12px;
    cursor: pointer;
    transition: border-color 150ms ease, background 150ms ease;
    min-width: 100px;
    min-height: var(--touch-min, 48px);
    color: var(--text-primary);
    font-family: var(--font-body, 'Arima', sans-serif);
}

.settings-option:hover {
    background: var(--bg-overlay);
}

.settings-option--active {
    border-color: var(--theme-primary);
    background: var(--bg-overlay);
}

.settings-option__icon {
    font-size: 24px;
    color: var(--theme-primary);
}

.settings-option__swatch {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid var(--border-default);
}

.settings-option__label {
    font-size: var(--text-base, 12px);
    font-weight: var(--font-semibold, 600);
}

.settings-option__desc {
    font-size: var(--text-xs, 10px);
    color: var(--text-muted);
}
</style>
