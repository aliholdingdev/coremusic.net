/**
 * coreplayer.shared.js — Shared utilities for all CorePlayer modules
 *
 * DRY: Extracts getCookie, setCookie, getMediaElement, formatTime
 * that were duplicated across PlaybackRepository, ProgressRepository,
 * SeekRepository, and VolumeRepository.
 *
 * Must be loaded BEFORE all other coreplayer scripts.
 * Cookie contract: mediaType = audio|video (default audio)
 *                  isPlaying = play|pause|stop (default stop)
 * Exposes: window.CorePlayerShared (getCookie, setCookie, getMediaElement,
 *          setMediaType, savePlaybackStatus, formatTime)
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
        // C4d: Secure yalnız https'te (http://localhost'da cookie kırılmasın)
        const secure = window.location && window.location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = name + '=' + value + ';expires=' + d.toUTCString() + ';path=/' + secure;
    }

    function getMediaElement() {
        const type = getCookie('mediaType');
        if (type === 'video') {
            return document.getElementById('video') || document.querySelector('video');
        }
        return document.getElementById('audio') || document.getElementById('main-audio');
    }

    /** Persist media type — allowlist: audio|video (rejects anything else). */
    function setMediaType(type) {
        if (type !== 'audio' && type !== 'video') return false;
        setCookie('mediaType', type, 365);
        document.dispatchEvent(new CustomEvent('cm:player:mediatype', {
            detail: { type: type },
            bubbles: true,
            composed: true
        }));
        return true;
    }

    /** Persist playback status — allowlist: play|pause|stop (rejects anything else). */
    function savePlaybackStatus(status) {
        if (status !== 'play' && status !== 'pause' && status !== 'stop') return false;
        setCookie('isPlaying', status, 365);
        document.dispatchEvent(new CustomEvent('cm:player:status', {
            detail: { status: status },
            bubbles: true,
            composed: true
        }));
        return true;
    }

    /* Medya tipi çözümü — URL uzantısı + markup ipucu (data-kind).
       data-kind sunucudan (HomeSongButton 'k' alanından) geldiği için ÖNCELYINIR;
       geçersiz/eksik ipucu → uzantıya düşer; uzantısız stream → 'audio'.
       Uzantı SSOT: media.coremusic.net/bin/ingest.php L157 $videoUzanti. */
    const VIDEO_EXTENSIONS = Object.freeze(['mp4', 'mkv', 'avi', 'webm']);

    function resolveMediaType(url, hint) {
        if (hint === 'video' || hint === 'audio') return hint;
        const clean = String(url).split(/[?#]/)[0];
        const base = clean.slice(clean.lastIndexOf('/') + 1);
        const dot = base.lastIndexOf('.');
        const ext = dot > -1 ? base.slice(dot + 1).toLowerCase() : '';
        return VIDEO_EXTENSIONS.indexOf(ext) !== -1 ? 'video' : 'audio';
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
        setMediaType: setMediaType,
        savePlaybackStatus: savePlaybackStatus,
        resolveMediaType: resolveMediaType,
        formatTime: formatTime
    };

}(window));
