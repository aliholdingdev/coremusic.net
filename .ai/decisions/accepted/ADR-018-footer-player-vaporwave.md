---
type: adr
category: ui
title: "ADR-018: Footer Player Vaporwave"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-018: Footer Player Vaporwave

**Status:** Frozen (değiştirilemez)
**Kategorisi:** UI
**İlgili Agent:** [[.agents/ui-designer]]

---

## 1. Amaç

Bu ADR, CoreMusic platformunun tüm panellerinde kullanılacak sabit footer player'ın tasarımını, vaporwave estetiğini ve teknik implementasyonunu tanımlar. Kullanıcı deneyimini iyileştiren, her sayfada erişilebilir ve CoreMusic'e özgü bir kimlik oluşturan footer player standartlarını belirler. [[ADR-044-dynamic-user-theme-engine]] ile uyumludur.

---

## 2. Bağlam

### 2.1 Problem Tanımı

Müzik dinleme platformlarında player kontrolü her zaman erişilebilir olmalıdır:

| Sorun | Çözüm |
|-------|-------|
| Player sayfada kayboluyor | Sabit footer |
| Estetik tutarsız | Vaporwave kimliği |
| Mobil uyumsuzluk | Responsive tasarım |
| Erişilebilirlik eksik | WCAG 2.2 AA |
| Performans | CSS transition, minimal JS |
| Tema uyumsuzluğu | Cinsiyet bazlı tema |

### 2.2 Vaporwave Estetiği

Vaporwave, 1980'lerin synthpop ve consumer elektronik kültüründen ilham alan bir sanat akımıdır:

| Vaporwave Unsuru | Uygulama |
|------------------|----------|
| Neon renkleri | Pembe, mor, turkuaz gradient |
| Grid çizgileri | Retro-fütüristik arkaplan |
| Synth paleti | Kozmik gradient |
| Retro font | Monospace ve sans-serif |
| Glitch efektleri | Hover ve transition animasyonları |
| Palmiye ağaçları | Dekoratif unsurlar |
| Sunset gradient | Header ve player arkaplanı |
| CRT efekti | Scanline overlay |

### 2.3 İlişkili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-044-dynamic-user-theme-engine]] | Dinamik tema motoru |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS |
| [[ADR-045-multi-domain-view-mode-architecture]] | Multi-domain view mode |
| [[ADR-046-cross-view-state-preservation]] | Cross-view state |
| [[ADR-048-view-transition-api-integration]] | View Transition API |

---

## 3. Karar

CoreMusic'te **vaporwave estetiğinde** footer player kullanılacak:

| Karar | Değer |
|-------|-------|
| Estetik | Vaporwave (synthwave, neon) |
| Konum | Footer sabit (fixed bottom) |
| Yükseklik | 80px (mobil: 64px) |
| Z-index | 9999 |
| Kontroller | Play, pause, skip, volume |
| Progress | Seek bar (draggable) |
| Metadata | Şarkı, sanatçı, albüm kapak görseli |
| Mobil | Responsive, swipe gesture |
| Tema | Cinsiyet bazlı renk değişimi |
| Animasyonlar | CSS transitions (300ms) |
| Erişilebilirlik | WCAG 2.2 AA |

---

## 4. Teknik Detaylar

### 4.1 HTML Yapısı

```html
<footer class="cm-player" role="region" aria-label="Music Player">
  <div class="cm-player__progress">
    <input type="range" class="cm-player__seekbar" min="0" max="100" value="0" aria-label="Seek" />
  </div>
  <div class="cm-player__controls">
    <div class="cm-player__left">
      <div class="cm-player__cover">
        <img src="" alt="Album Cover" class="cm-player__cover-img" />
      </div>
      <div class="cm-player__info">
        <span class="cm-player__title"></span>
        <span class="cm-player__artist"></span>
      </div>
    </div>
    <div class="cm-player__center">
      <button class="cm-player__btn cm-player__btn--prev" aria-label="Previous">&#x23EE;</button>
      <button class="cm-player__btn cm-player__btn--play" aria-label="Play">&#x25B6;</button>
      <button class="cm-player__btn cm-player__btn--next" aria-label="Next">&#x23ED;</button>
    </div>
    <div class="cm-player__right">
      <div class="cm-player__volume">
        <button class="cm-player__btn cm-player__btn--mute" aria-label="Mute">&#x1F50A;</button>
        <input type="range" class="cm-player__volume-slider" min="0" max="100" value="80" aria-label="Volume" />
      </div>
      <button class="cm-player__btn cm-player__btn--shuffle" aria-label="Shuffle">&#x1F500;</button>
      <button class="cm-player__btn cm-player__btn--repeat" aria-label="Repeat">&#x1F501;</button>
    </div>
  </div>
  <div class="cm-player__time">
    <span class="cm-player__current">0:00</span>
    <span class="cm-player__duration">0:00</span>
  </div>
</footer>
```

### 4.2 CSS Tasarımı (ITCSS + BEM)

```css
/* === 05-OBJECTS === */
.cm-player {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 80px;
  z-index: 9999;
  background: linear-gradient(135deg, #1a0a2e 0%, #16213e 50%, #0f3460 100%);
  border-top: 2px solid #e94560;
  display: flex;
  flex-direction: column;
  font-family: 'Courier New', monospace;
  color: #fff;
  box-shadow: 0 -4px 20px rgba(233, 69, 96, 0.3);
}

.cm-player__progress { width: 100%; height: 4px; background: rgba(255,255,255,0.1); }

.cm-player__seekbar {
  width: 100%; height: 4px; -webkit-appearance: none;
  background: linear-gradient(90deg, #e94560, #ff6b9d, #c44dff);
  cursor: pointer; transition: height 0.2s;
}

.cm-player__seekbar:hover { height: 8px; }

.cm-player__controls {
  display: flex; justify-content: space-between; align-items: center;
  padding: 0 16px; flex: 1;
}

.cm-player__left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }

.cm-player__cover {
  width: 48px; height: 48px; border-radius: 4px;
  overflow: hidden; border: 1px solid rgba(233,69,96,0.5);
}

.cm-player__cover-img { width: 100%; height: 100%; object-fit: cover; }

.cm-player__info { display: flex; flex-direction: column; min-width: 0; }

.cm-player__title {
  font-size: 14px; font-weight: bold; color: #e94560;
  text-shadow: 0 0 10px rgba(233,69,96,0.5);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.cm-player__artist {
  font-size: 12px; color: #ff6b9d;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.cm-player__center { display: flex; align-items: center; gap: 8px; }

.cm-player__btn {
  background: none; border: none; color: #fff; font-size: 20px;
  cursor: pointer; padding: 8px; border-radius: 50%; transition: all 0.3s;
}

.cm-player__btn:hover { background: rgba(233,69,96,0.2); text-shadow: 0 0 15px #e94560; }

.cm-player__btn--play {
  font-size: 28px; border: 2px solid #e94560; width: 48px; height: 48px;
  display: flex; align-items: center; justify-content: center;
}

.cm-player__btn--play:hover { background: #e94560; box-shadow: 0 0 20px rgba(233,69,96,0.6); }

.cm-player__right { display: flex; align-items: center; gap: 8px; flex: 1; justify-content: flex-end; }

.cm-player__volume { display: flex; align-items: center; gap: 4px; }

.cm-player__volume-slider {
  width: 80px; height: 4px; -webkit-appearance: none;
  background: linear-gradient(90deg, #c44dff, #e94560);
}

.cm-player__time {
  display: flex; justify-content: space-between;
  padding: 0 16px; font-size: 11px; color: rgba(255,255,255,0.6);
}
```

### 4.3 JavaScript Implementasyonu

```javascript
class CMPlayer {
  #audioElement;
  #isPlaying = false;
  #currentTrack = null;

  constructor() {
    this.#audioElement = new Audio();
    this.#audioElement.crossOrigin = 'anonymous';
    this.#initEventListeners();
  }

  #initEventListeners() {
    const playBtn = document.querySelector('.cm-player__btn--play');
    const prevBtn = document.querySelector('.cm-player__btn--prev');
    const nextBtn = document.querySelector('.cm-player__btn--next');
    const seekbar = document.querySelector('.cm-player__seekbar');
    const volumeSlider = document.querySelector('.cm-player__volume-slider');

    playBtn?.addEventListener('click', () => this.togglePlay());
    prevBtn?.addEventListener('click', () => this.previousTrack());
    nextBtn?.addEventListener('click', () => this.nextTrack());
    seekbar?.addEventListener('input', (e) => this.seek(e.target.value));
    volumeSlider?.addEventListener('input', (e) => this.setVolume(e.target.value));
    this.#audioElement.addEventListener('timeupdate', () => this.#updateProgress());
    this.#audioElement.addEventListener('ended', () => this.nextTrack());
  }

  togglePlay() {
    if (this.#isPlaying) { this.#audioElement.pause(); }
    else { this.#audioElement.play(); }
    this.#isPlaying = !this.#isPlaying;
    this.#updatePlayButton();
  }

  seek(value) {
    const time = (value / 100) * this.#audioElement.duration;
    this.#audioElement.currentTime = time;
  }

  setVolume(value) { this.#audioElement.volume = value / 100; }

  #updateProgress() {
    const seekbar = document.querySelector('.cm-player__seekbar');
    const currentTime = document.querySelector('.cm-player__current');
    if (seekbar && this.#audioElement.duration) {
      seekbar.value = (this.#audioElement.currentTime / this.#audioElement.duration) * 100;
    }
    if (currentTime) {
      currentTime.textContent = this.#formatTime(this.#audioElement.currentTime);
    }
  }

  #updatePlayButton() {
    const btn = document.querySelector('.cm-player__btn--play');
    if (btn) {
      btn.textContent = this.#isPlaying ? '\u23F8' : '\u25B6';
      btn.setAttribute('aria-label', this.#isPlaying ? 'Pause' : 'Play');
    }
  }

  #formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  }

  loadTrack(track) {
    this.#currentTrack = track;
    this.#audioElement.src = track.url;
    this.#updateMetadata(track);
  }

  #updateMetadata(track) {
    const title = document.querySelector('.cm-player__title');
    const artist = document.querySelector('.cm-player__artist');
    const cover = document.querySelector('.cm-player__cover-img');
    if (title) title.textContent = track.title;
    if (artist) artist.textContent = track.artist;
    if (cover) cover.src = track.coverUrl;
  }
}
```

### 4.4 Responsive Breakpoints

| Cihaz | Yükseklik | Düzen |
|-------|-----------|-------|
| Desktop (>1024px) | 80px | Tam kontroller |
| Tablet (768-1024px) | 72px | Kısmi kontroller |
| Mobil (<768px) | 64px | Minimal kontroller |

### 4.5 Tema Entegrasyonu

```css
[data-gender="female"] .cm-player {
  background: linear-gradient(135deg, #2d1b4e 0%, #4a1942 50%, #6b2fa0 100%);
  border-top-color: #ff69b4;
}

[data-gender="male"] .cm-player {
  background: linear-gradient(135deg, #0d1b2a 0%, #1b2838 50%, #2c3e50 100%);
  border-top-color: #3498db;
}

[data-gender="neutral"] .cm-player {
  background: linear-gradient(135deg, #1a0a2e 0%, #16213e 50%, #0f3460 100%);
  border-top-color: #e94560;
}
```

### 4.6 Erişilebilirlik (WCAG 2.2 AA)

| Kural | Uygulama |
|-------|----------|
| ARIA labels | Tüm butonlarda `aria-label` |
| Keyboard navigation | Tab sırası, Enter/Space tetikleme |
| Focus visible | `:focus-visible` ile belirginleştirme |
| Renk kontrastı | Minimum 4.5:1 ratio |
| Screen reader | `role="region"`, `aria-label` |
| Reduced motion | `prefers-reduced-motion` desteği |

---

## 5. Yasak Örüntüleri

| Yasak | Dogru |
|-------|-------|
| innerHTML | DOMParser + TrustedTypes |
| var | const / let |
| eval() | Safe alternatives |
| localStorage auth | Session-based auth |
| Framework (React/Vue) | Vanilla JS (ADR-001) |
| Inline script | External JS files |
| !important abuse | BEM specificity |
| Hardcoded colors | CSS custom properties |
| position: absolute | position: fixed |
| Z-index > 9999 | Managed z-index |
| Mouse-only events | Touch + Pointer events |
| No focus styles | :focus-visible |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Cozum |
|---------|-------------|-------|
| Audio yuklenmedi | Bos player | Varsayilan gorsel |
| Uzun baslik | 50+ karakter | Text ellipsis |
| Mobil landscape | Yatay mod | Compact player |
| PWA install | Tam ekran | Player gizlenir |
| Dark/light mode | Tema degisimi | CSS custom properties |
| Touch device | Dokunmatik | Swipe gesture |
| Multiple tabs | Birden fazla sekme | Tek player instance |
| Keyboard only | Klavye kullanimi | Tam keyboard support |
| Screen reader | Ekran okuyucu | ARIA tam destek |
| Slow network | Yavas internet | Loading indicator |
| Autoplay blocked | Browser policy | User interaction gerekli |
| CORS error | crossOrigin | Fallback to no-CORS |

---

## 7. Hard Guardrails

| # | Kural | Ihlal Sonucu |
|---|-------|-------------|
| 1 | Vaporwave estetigi | Ret |
| 2 | Fixed bottom | UX ihlali |
| 3 | WCAG 2.2 AA | Yasal risk |
| 4 | Vanilla JS | Ret |
| 5 | BEM formati | Ret |
| 6 | ITCSS | Ret |
| 7 | Z-index 9999 | Player gizlenir |
| 8 | No innerHTML | XSS riski |
| 9 | Keyboard support | Erisilebilirlik |
| 10 | Responsive | UX ihlali |

---

## 8. Ilgili ADR'ler

| ADR | Konu | Iliski |
|-----|------|--------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS | Frontend teknolojisi |
| [[ADR-044-dynamic-user-theme-engine]] | Dinamik tema | Tema entegrasyonu |
| [[ADR-045-multi-domain-view-mode-architecture]] | Multi-domain view | View mode uyumu |
| [[ADR-046-cross-view-state-preservation]] | Cross-view state | Player state koruma |
| [[ADR-048-view-transition-api-integration]] | View Transition | Animasyon destegi |

---

## 9. Capraz Referanslar

| Bolum | Hedef | Iliski |
|-------|-------|--------|
| 2.2 | [[prompt-system/coremusic-theme-prompt]] | Vaporwave prompt |
| 4.2 | [[ui-design/04-design-system]] | Design system |
| 4.3 | [[architecture/l3-presentation]] | L3 presentation katmani |
| 4.4 | [[ui-design/02-design-tokens]] | Design tokens |
| 4.5 | [[ADR-044-dynamic-user-theme-engine]] | Tema motoru |
| 4.6 | [[testing/coverage-targets]] | Test coverage |
| 5 | [[ADR-001-vanilla-js-itcss]] | Yasak oruntuleri |

---

## 10. Sozluk

| Terim | Tanim |
|-------|-------|
| Vaporwave | 1980'lerden ilham alan sanat akimi |
| Footer Player | Sabit alt kismindaki muzik oynatici |
| Neon | Canli, parlak renkler |
| Synthwave | Elektronik muzik turu |
| BEM | Block Element Modifier - CSS metodolojisi |
| ITCSS | Its Time to Create Scaleable Stylesheets |
| WCAG | Web Content Accessibility Guidelines |
| ARIA | Accessible Rich Internet Applications |
| DOMParser | HTML/XML parse mekanizmasi |
| TrustedTypes | XSS koruma politikasi |
| CSS Custom Properties | CSS degiskenleri |
| Z-index | Katman siralamasi |
| Seek bar | Ilerleme cubugu |
| Responsive | Cihaza gore uyumlu tasarim |

---

## 11. Kalite Raporu

| Metrik | Deger |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team, Human Mode, Truth Mode verified |
| ADR Status | Frozen (degistirilemez) |
| Sections | 11 |
| Hard Guardrails | 10 |
| Edge Cases | 12 |
| Yasak Oruntuleri | 12 |
| Ilgili ADR'ler | 5 |
| Capraz Referanslar | 7 |
| Sozluk Terimleri | 14 |
| Footer Yuksekligi | 80px (mobil: 64px) |
| Z-index | 9999 |
| Tema Sayisi | 3 (female/male/neutral) |
| Breakpoint Sayisi | 3 (desktop/tablet/mobil) |
| WCAG Seviyesi | AA |
| Animasyon Suresi | 300ms |

---

## 12. Authority

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team, Human Mode, Truth Mode
**Immutability:** ADR 001-037 frozen, degistirilemez
**Scope:** CoreMusic footer player vaporwave tasarimi
**Governance:** Red Team, Human Mode, Truth Mode
---

## 13. Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| Space | Play/Pause |
| Left Arrow | Seek backward 5s |
| Right Arrow | Seek forward 5s |
| Up Arrow | Volume up 10% |
| Down Arrow | Volume down 10% |
| M | Mute/Unmute |
| S | Shuffle toggle |
| R | Repeat toggle |
| N | Next track |
| P | Previous track |

### 13.1 Keyboard Navigation

`javascript
class KeyboardManager {
  constructor(player) {
    this.player = player;
    document.addEventListener('keydown', (e) => this.handleKey(e));
  }

  handleKey(e) {
    if (e.target.tagName === 'INPUT') return;
    switch (e.code) {
      case 'Space': e.preventDefault(); this.player.togglePlay(); break;
      case 'ArrowLeft': this.player.seekRelative(-5); break;
      case 'ArrowRight': this.player.seekRelative(5); break;
      case 'ArrowUp': this.player.volumeRelative(10); break;
      case 'ArrowDown': this.player.volumeRelative(-10); break;
      case 'KeyM': this.player.toggleMute(); break;
      case 'KeyS': this.player.toggleShuffle(); break;
      case 'KeyR': this.player.toggleRepeat(); break;
      case 'KeyN': this.player.nextTrack(); break;
      case 'KeyP': this.player.previousTrack(); break;
    }
  }
}
`

---

## 14. Touch Gestures

| Gesture | Action |
|---------|--------|
| Swipe left | Next track |
| Swipe right | Previous track |
| Swipe up | Volume up |
| Swipe down | Volume down |
| Tap center | Play/Pause |
| Long press | Context menu |

---

## 15. Performance Metrics

| Metric | Target | Measurement |
|--------|--------|-------------|
| First paint | <100ms | LCP |
| Interaction to next paint | <200ms | INP |
| Layout shift | <0.1 | CLS |
| Animation frame rate | 60fps | requestAnimationFrame |
| Memory usage | <50MB | heap size |

---

## 16. Browser Support

| Browser | Version | Status |
|---------|---------|--------|
| Chrome | 90+ | Full support |
| Firefox | 88+ | Full support |
| Safari | 14+ | Full support |
| Edge | 90+ | Full support |
| Opera | 76+ | Full support |
| Samsung Internet | 14+ | Full support |
| iOS Safari | 14+ | Full support |
| Chrome Android | 90+ | Full support |

---

## 17. PWA Integration

| Feature | Support |
|---------|---------|
| Service Worker | Offline playback |
| Web App Manifest | Install prompt |
| Background Audio | Media Session API |
| Notification | Track change |
| Shortcuts | Play, Shuffle, Queue |

---

## 18. Analytics Events

| Event | Payload | Trigger |
|-------|---------|---------|
| player_play | track_id, timestamp | Play button |
| player_pause | track_id, timestamp | Pause button |
| player_seek | track_id, position | Seek bar |
| player_volume | volume_level | Volume change |
| player_track_end | track_id, duration | Track complete |
| player_error | error_code, message | Playback error |

---

## 19. Quality Report (Updated)

| Metric | Value |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team, Human Mode, Truth Mode verified |
| Sections | 19 |
| Hard Guardrails | 10 |
| Edge Cases | 12 |
| Keyboard Shortcuts | 11 |
| Touch Gestures | 6 |
| Browser Support | 8 |
| Analytics Events | 6 |

---

## 20. Accessibility Checklist

| Check | Status | Notes |
|-------|--------|-------|
| ARIA labels on all buttons | Required | screen-reader |
| Keyboard navigation complete | Required | tab order |
| Focus visible styles | Required | :focus-visible |
| Color contrast >= 4.5:1 | Required | WCAG AA |
| Reduced motion support | Required | prefers-reduced-motion |
| Screen reader announcements | Required | aria-live |
| Touch target size >= 44px | Required | mobile |
| Skip navigation link | Required | accessibility |
| Error messages announced | Required | aria-live=polite |
| Loading states announced | Required | aria-busy |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team, Human Mode, Truth Mode
**Immutability:** ADR 001-037 frozen, degistirilemez
**Scope:** CoreMusic footer player vaporwave tasarimi
**Governance:** Red Team, Human Mode, Truth Mode
