(function () {
  'use strict';

  /**
   * footer.init.js — CoreMusic Player Footer Initialization
   * Safe binding for Seek, Volume, Playback, and Progress modules.
   */

  function initCorePlayerModules() {
    const audio = document.getElementById('audio');

    /* Volume % tek-kaynak senkronu — HATA DÜZELTMESİ:
       MM_Volume cookie yokken VolumeRepository default 0.5 döndürüp
       PHP'nin bastığı input değerini (session volume) %50'ye eziyordu.
       Cookie yoksa input değeri cookie'ye yazılır → gösterge/slider tek kaynaktan. */
    const volumeInit = document.getElementById('volume');
    if (volumeInit && window.CorePlayerShared && typeof CorePlayerShared.getCookie === 'function') {
      if (CorePlayerShared.getCookie('MM_Volume') === null) {
        CorePlayerShared.setCookie('MM_Volume', volumeInit.value || '1', 365);
      }
    }

    if (audio) {
      if (window.SeekRepository && typeof SeekRepository.setMediaElement === 'function') SeekRepository.setMediaElement(audio);
      if (window.VolumeRepository && typeof VolumeRepository.setMediaElement === 'function') VolumeRepository.setMediaElement(audio);
      if (window.PlaybackRepository && typeof PlaybackRepository.setMediaElement === 'function') PlaybackRepository.setMediaElement(audio);
      if (window.ProgressRepository && typeof ProgressRepository.setMediaElement === 'function') ProgressRepository.setMediaElement(audio);
    }

    /* VolumeController */
    if (window.VolumeController && typeof VolumeController.init === 'function') {
      try {
        /* iconBasePath override: res-default klasörü assets'te yok — theme varsa onu, yoksa res-pink */
        const _gender = document.documentElement?.getAttribute('data-gender') || 'female';
        const _themeMap = { female: 'res-pink', male: 'res-blue' };
        VolumeController.init({
          percentSelector: 'p.c-footer__volume-slider-volume-size',
          iconBasePath: '//assets.coremusic.net/Image/' + (_themeMap[_gender] || 'res-pink') + '/',
        });
      } catch (e) {
        if (window.dbg) window.dbg.warn('[footer.init] VolumeController.init error:', e);
      }
    }

    /* SeekController */
    if (window.SeekController && typeof SeekController.init === 'function') {
      try {
        SeekController.init({
          container: '#seekbarclick',
          input: '#seekbar',
          fill: '#seekbar2',
          timeCurrent: '#gettime_audio',
          timeDuration: '#footer_sure',
        });
      } catch (e) {
        if (window.dbg) window.dbg.warn('[footer.init] SeekController.init error:', e);
      }
    }

    /* PlaybackController */
    if (window.PlaybackController && typeof PlaybackController.init === 'function') {
      try {
        PlaybackController.init();
      } catch (e) {
        if (window.dbg) window.dbg.warn('[footer.init] PlaybackController.init error:', e);
      }
    }

    /* ProgressController */
    if (window.ProgressController && typeof ProgressController.init === 'function') {
      try {
        ProgressController.init();
      } catch (e) {
        if (window.dbg) window.dbg.warn('[footer.init] ProgressController.init error:', e);
      }
    }

    /* Media Play butonları — KAİ global API (mplay/mpause/mstop/mgeri/mileri)
       CSP uyumlu: inline onclick yerine addEventListener (ADR-012 nonce + strict-dynamic) */
    const bindPlayerAction = function (id, action) {
      const btn = document.getElementById(id);
      if (btn) {
        btn.addEventListener('click', function () {
          if (typeof action === 'function') action();
        });
      }
    };

    bindPlayerAction('prevBtn', function () { if (window.mgeri) window.mgeri(); });
    bindPlayerAction('playBtn', function () { if (window.mplay) window.mplay(); });
    bindPlayerAction('pauseBtn', function () { if (window.mpause) window.mpause(); });
    bindPlayerAction('stopBtn', function () { if (window.mstop) window.mstop(); });
    bindPlayerAction('nextBtn', function () { if (window.mileri) window.mileri(); });

    /* Setup volume input range sync with percentage text & fill */
    const volumeInput = document.getElementById('volume');
    const volumeFill = document.getElementById('volume2');
    const volumePct = document.querySelector('.c-footer__volume-slider-volume-size');

    if (volumeInput) {
      const updateVolumeUi = function () {
        const val = parseFloat(volumeInput.value) || 0;
        const pct = Math.round(val * 100);
        if (volumePct) volumePct.textContent = '% ' + pct;
        if (volumeFill) volumeFill.style.width = (val * 100) + '%';
      };

      volumeInput.addEventListener('input', updateVolumeUi);
      updateVolumeUi();
    }

    /* Setup seekbar input range sync */
    const seekInput = document.getElementById('seekbar');
    const seekFill = document.getElementById('seekbar2');

    if (seekInput && seekFill) {
      seekInput.addEventListener('input', function () {
        const val = parseFloat(seekInput.value) || 0;
        seekFill.style.width = val + '%';
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCorePlayerModules);
  } else {
    initCorePlayerModules();
  }
})();
