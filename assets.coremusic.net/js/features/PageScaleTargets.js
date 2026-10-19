/**
 * CoreMusic — PageScaleTargets v1.0.0
 * Sayfa-ozel ScaleManager hedefleri: Gecmis (history) ve Simdi Oynatiliyor (nowplaying).
 *
 * SSOT Figma:
 *   History 1920  : node 1339:23464 "Laptop 1920 - Gecmis Page"
 *   NowPlaying 1920: node 1390:24448 "Laptop 1920 - Playlist Page"
 *   Fullscreen 1920: node 2135:34323 "Laptop 1920 - Playlist Page full screen mode 2"
 *
 * Kullanim: main.js -> scaleManager baslatildiktan sonra bu modulu cagir.
 *   import { registerPageScaleTargets } from './features/PageScaleTargets.js';
 *   registerPageScaleTargets(scaleManager);
 *
 * Scale kurallari (ScaleManager.ScaleCalculator kurali formati):
 *   embedded (<=1024) : skip -- CSS @media kontrollu
 *   wide (1025+)      : skip (v1.1.0) — history/player wide fluid CSS ile 100% genişlik
 *
 * @module features/PageScaleTargets
 * @version 1.1.0
 */

'use strict';

const HISTORY_WIDE_SCALE_TARGET = {
    name: 'history-wide',
    selector: '.page-history--wide',
    origin: 'top left',
    compensateWidth: false,
    customProp: '--scale-history-wide',
    // v1.1.0: wide sayfa ölçeklenmez — 1025px+ her ekranda 100% genişlik, fluid CSS (ortalama/scale yok)
    rules: [
        { min: 0, skip: true },
    ],
};

const PLAYER_WIDE_SCALE_TARGET = {
    name: 'player-wide',
    selector: '.page-player--wide:not(.page-player--fullscreen)',
    origin: 'top left',
    compensateWidth: false,
    customProp: '--scale-player-wide',
    // v1.1.0: wide sayfa ölçeklenmez — 1025px+ her ekranda 100% genişlik, fluid CSS (ortalama/scale yok)
    rules: [
        { min: 0, skip: true },
    ],
};

const PLAYER_FULLSCREEN_SCALE_TARGET = {
    name: 'player-fullscreen',
    selector: '.page-player--wide.page-player--fullscreen',
    origin: 'top left',
    compensateWidth: false,
    customProp: '--scale-player-fullscreen',
    rules: [
        { min: 0, skip: true },
    ],
};

const HISTORY_EMBEDDED_SCALE_TARGET = {
    name: 'history-embedded',
    selector: '.page-history:not(.page-history--wide)',
    origin: 'top left',
    compensateWidth: false,
    customProp: '--scale-history-embedded',
    rules: [
        { max: 1024, scale: 1.00 },
        { min: 1025, max: 1920, from: 1.00, to: 1.875 },
        { min: 1921, scale: 1.875 },
    ],
};

const PLAYER_EMBEDDED_SCALE_TARGET = {
    name: 'player-embedded',
    selector: '.page-player--embedded',
    origin: 'top left',
    compensateWidth: false,
    customProp: '--scale-player-embedded',
    rules: [
        { max: 1024, scale: 1.00 },
        { min: 1025, max: 1920, from: 1.00, to: 1.875 },
        { min: 1921, scale: 1.875 },
    ],
};

export function registerPageScaleTargets(scaleManager) {
    if (!scaleManager || typeof scaleManager.registerTarget !== 'function') {
        console.warn('[PageScaleTargets] ScaleManager bulunamadi veya registerTarget metodu yok.');
        return;
    }

    scaleManager.registerTarget(HISTORY_WIDE_SCALE_TARGET);
    scaleManager.registerTarget(HISTORY_EMBEDDED_SCALE_TARGET);
    scaleManager.registerTarget(PLAYER_WIDE_SCALE_TARGET);
    scaleManager.registerTarget(PLAYER_FULLSCREEN_SCALE_TARGET);
    scaleManager.registerTarget(PLAYER_EMBEDDED_SCALE_TARGET);

    scaleManager.requestApply();
}

export {
    HISTORY_WIDE_SCALE_TARGET,
    HISTORY_EMBEDDED_SCALE_TARGET,
    PLAYER_WIDE_SCALE_TARGET,
    PLAYER_FULLSCREEN_SCALE_TARGET,
    PLAYER_EMBEDDED_SCALE_TARGET,
};
