/**
 * coreplayer.shared.js — Shared utilities for all CorePlayer modules
 *
 * DRY: Extracts getCookie, setCookie, getMediaElement, formatTime
 * that were duplicated across PlaybackRepository, ProgressRepository,
 * SeekRepository, and VolumeRepository.
 *
 * Must be loaded BEFORE all other coreplayer scripts.
 * Exposes: window.CorePlayerShared
 */
(function (window) {
    'use strict';

    function getCookie(name) {
        const v = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
        return v ? v.pop() : null;
    }

    function setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + '=' + value + ';expires=' + d.toUTCString() + ';path=/';
    }

    function getMediaElement() {
        const type = getCookie('MM_PlaybackType');
        if (type === '1') {
            return document.querySelector('video');
        }
        return document.getElementById('audio');
    }

    function formatTime(secs) {
        if (!isFinite(secs) || secs < 0) return '0:00';
        const s = Math.floor(secs);
        const h = Math.floor(s / 3600);
        const m = Math.floor((s % 3600) / 60);
        const sec = s % 60;
        return (h > 0 ? h + ':' : '') + String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
    }

    window.CorePlayerShared = {
        getCookie: getCookie,
        setCookie: setCookie,
        getMediaElement: getMediaElement,
        formatTime: formatTime
    };

}(window));
