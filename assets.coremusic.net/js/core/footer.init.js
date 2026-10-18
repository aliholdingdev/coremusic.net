(function () {
  'use strict';

  /**
   * footer.init.js — CoreMusic Player Footer Initialization
   * Safe binding for Seek, Volume, Playback, and Progress modules.
   */

  /* Medya tipi çözümü (data-kind ipucu + uzantı) → CorePlayerShared.resolveMediaType
     (spec kapsamı; uzantı SSOT: media.coremusic.net/bin/ingest.php L157). */

  function initCorePlayerModules() {
    const audio = document.getElementById('audio');

    /* CSP-safe başlangıç fill genişlikleri: PHP inline style attribute'u CSP style-src
       tarafından engellenir; data-progress değerleri CSSOM ile uygulanır (ADR-012).
       SPA router main içeriğini sonradan değiştirebildiğinden load + gecikmeli geçiş var. */
    const applyProgressFills = function () {
      document.querySelectorAll('[data-progress]').forEach((el) => {
        const pct = parseFloat(el.dataset.progress);
        if (!isNaN(pct)) el.style.width = Math.max(0, Math.min(100, pct)) + '%';
      });
    };
    applyProgressFills();
    window.addEventListener('load', applyProgressFills);
    setTimeout(applyProgressFills, 300);

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

    /* Session → cookie rehidrasyonu (S3): cookie silinmişse sunucunun
       session'dan doğrulayıp bastığı CM_PLAYER_BOOT değerleri cookie'ye geri yazılır.
       setter'lar allowlist uygular → boot'ta geçersiz değer olsa bile cookie'ye sızmaz. */
    if (window.CorePlayerShared && window.CM_PLAYER_BOOT && typeof CorePlayerShared.getCookie === 'function') {
      const boot = window.CM_PLAYER_BOOT;
      if (CorePlayerShared.getCookie('mediaType') === null && typeof boot.mediaType === 'string') {
        CorePlayerShared.setMediaType(boot.mediaType);
      }
      if (CorePlayerShared.getCookie('isPlaying') === null && typeof boot.mediaStatus === 'string') {
        CorePlayerShared.savePlaybackStatus(boot.mediaStatus);
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

    /* ── Oynatma durumu kalıcılığı (isPlaying cookie) ────────────────
       Aktif media elementi kancaları: play → 'play' · pause → 'pause'
       (stop yolu hariç) · ended → 'stop'. Medya tipi değişince olay
       (cm:player:mediatype) kanıca yeni aktif elementi taşır. */
    if (window.CorePlayerShared && typeof CorePlayerShared.getMediaElement === 'function'
      && !document.documentElement.hasAttribute('data-cm-status-bound')) {
      document.documentElement.setAttribute('data-cm-status-bound', '1');

      let statusMedia = null;

      const onMediaPlay = function () {
        CorePlayerShared.savePlaybackStatus('play');
      };

      const onMediaPause = function () {
        /* stop() yolu pause olayı üretir; cookie'de 'stop' varken
           pause kancası bu yazımı 'pause' ile ezmesin (olay asenkron) */
        if (CorePlayerShared.getCookie('isPlaying') === 'stop') return;
        CorePlayerShared.savePlaybackStatus('pause');
      };

      const onMediaEnded = function () {
        CorePlayerShared.savePlaybackStatus('stop');
      };

      const bindStatusHooks = function () {
        const media = CorePlayerShared.getMediaElement();
        if (!media || media === statusMedia) return;
        if (statusMedia) {
          statusMedia.removeEventListener('play', onMediaPlay);
          statusMedia.removeEventListener('pause', onMediaPause);
          statusMedia.removeEventListener('ended', onMediaEnded);
        }
        statusMedia = media;
        media.addEventListener('play', onMediaPlay);
        media.addEventListener('pause', onMediaPause);
        media.addEventListener('ended', onMediaEnded);
      };

      bindStatusHooks();
      document.addEventListener('cm:player:mediatype', bindStatusHooks);
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

    /* ── Şarkı kartı → stream oynatma ──────────────────────────────
       Delegated listener: SPA router main içeriğini yeniden render ettiğinde
       kartlar değişir, listener hayatta kalır (documentElement bayrağı ile
       çift bağlama engellenir). PlayerInfoComponent.updateTrack selector'ları
       ve ComponentBase.emit adlandırma (cm: prefix) kullanılır; yeni global yok. */
    const updateNowPlaying = function (title, artist) {
      if (title) {
        const footerSong = document.getElementById('footer_songname');
        if (footerSong) footerSong.textContent = title;
        const infoTitle = document.querySelector('.player-info__title .now-playing__meta-value, .now-playing__title');
        if (infoTitle) infoTitle.textContent = title;
      }
      if (artist) {
        const footerArtist = document.getElementById('footer_sanatci');
        if (footerArtist) footerArtist.textContent = artist;
        const infoArtist = document.querySelector('.player-info__singer .now-playing__meta-value, .now-playing__artist');
        if (infoArtist) infoArtist.textContent = artist;
      }
      document.dispatchEvent(new CustomEvent('cm:player:trackchange', {
        detail: { title: title, artist: artist },
        bubbles: true,
        composed: true,
      }));
    };

    const formatTime = function (seconds) {
      const total = isFinite(seconds) && seconds > 0 ? Math.floor(seconds) : 0;
      const h = Math.floor(total / 3600);
      const m = Math.floor((total % 3600) / 60);
      const s = total % 60;
      const pad = function (n) { return n < 10 ? '0' + n : String(n); };
      return pad(h) + ':' + pad(m) + ':' + pad(s);
    };

    const bindTrackStream = function () {
      const root = document.documentElement;
      if (root.hasAttribute('data-cm-stream-bound')) return;
      root.setAttribute('data-cm-stream-bound', '1');

      if (audio) {
        audio.addEventListener('loadedmetadata', function () {
          const dur = document.getElementById('footer_sure');
          if (dur) dur.textContent = formatTime(audio.duration);
        });
      }

      document.addEventListener('click', function (e) {
        const target = e.target instanceof Element
          ? e.target.closest('a[data-stream]')
          : null;
        if (!target) return;

        const stream = target.getAttribute('data-stream');
        if (!stream) return;
        e.preventDefault();

        /* Otomatik medya tipi seçimi — data-kind ipucu (markup, k alanından)
           ÖNCE, yoksa stream URL uzantısı → anlık mediaType cookie güncellemesi */
        if (window.CorePlayerShared && typeof CorePlayerShared.setMediaType === 'function'
          && typeof CorePlayerShared.resolveMediaType === 'function') {
          CorePlayerShared.setMediaType(
            CorePlayerShared.resolveMediaType(stream, target.getAttribute('data-kind'))
          );
        }

        /* Play hedefi: mediaType cookie'sine göre aktif element (tek kaynak) */
        const media = (window.CorePlayerShared && typeof CorePlayerShared.getMediaElement === 'function')
          ? CorePlayerShared.getMediaElement()
          : document.getElementById('audio') || document.getElementById('main-audio');
        if (!media) return;

        media.src = stream;
        const played = media.play();
        if (played && typeof played.catch === 'function') played.catch(function () {});

        updateNowPlaying(
          target.getAttribute('data-title') || '',
          target.getAttribute('data-artist') || ''
        );
      });
    };

    bindTrackStream();

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
