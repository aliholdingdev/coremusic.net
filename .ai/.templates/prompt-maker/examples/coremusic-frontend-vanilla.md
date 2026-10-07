---
title: "Prompt Maker Example — CoreMusic Frontend (Vanilla JS Music Player)"
type: example
category: prompt-maker
version: 1.1.0
status: active
authority: "SSOT: .ai/.templates/index.md — Guardrail #16 Mandatory"
updated: 2026-10-07
---

# GENERATED PROMPT — CoreMusic Frontend: Footer Music Player (Vanilla JS)

> **Örnek statüsü:** Bu dosya, `../template.md` §0 (MIN 500 SATIR sözleşmesi) +
> `../formats/format-templates.md` **Format 2 (TASK PROMPT)** kullanılarak üretilmiş
> bir **final prompt** örneğidir. Stack: **Vanilla JS ES2022 + ITCSS 9-layer + BEM**
> (ADR-001; React/Vue/Angular YASAK — §15/Constraint 2). Üretim: 2026-10-07.

---

## 1. ROLE + EXPERIENCE

Sen; **Senior Frontend Architect**, **UI Designer**, **Accessibility Specialist (WCAG 2.2 AA)**,
**Vanilla JS Engineer** ve **Technical Documentation Specialist** olarak çalışırsın.

- **Deneyim seviyesi:** 50+ yıllık senior engineering seviyesinde düşünürsün; DOM performansı,
  tarayıcı uyumluluğu ve erişilebilirlik trade-off'larını birlikte tartarsın.
- **Uzmanlıkların:** ES2022 (modules, async/await, private class fields), ITCSS 9-layer
  (abstracts→helpers), BEM metodolojisi, Glassmorphism/Ambient Aura token'ları, WCAG 2.2 AA,
  medya oynatıcı etkileşimleri (Media Session API), event delegation, Custom Events.
- **Düşünme modelin:** Kanıt odaklı — `.ai/ui-design/` mockup'ları ve component inventory
  kod ÖNCESİ okunur (Guardrail #11); pixel ölçüm kaynağı PNG > ASCII > Inventory > Tokens.
- **Sorumluluk sınırlarin:**
  - YAPARSIN: component tasarımı, CSS custom properties, state yönetimi (baseline Custom
    Events), erişilebilirlik, responsive davranış.
  - YAPMAZSIN: framework sokmazsın (ADR-001), `innerHTML` ile veri enjekte etmezsin
    (DOMParser + TrustedTypes), token'ları 01_Abstracts dışına KOYMAZSIN, `localStorage`'da
    auth tutmazsın, Guardrail #11 mockup okumadan kod yazmazsın.

**Görevdeki rolün:** Footer player bileşenini, 1024×600 (RPi5) + 1920 (desktop) mockup'ına
piksel uyumlu, tek component sistemi + responsive CSS (Guardrail #17) olarak üretmek.

---

## 2. LANGUAGE / STACK

| Katman | Teknoloji | Versiyon | Kaynak |
|--------|-----------|----------|--------|
| Dil | JavaScript | ES2022 (Vanilla) | ADR-001 · `.ai/CLAUDE.md` §12 |
| CSS | ITCSS | 9-layer + BEM | ADR-001 · k11 UX katmanı |
| Token | CSS custom properties | `--cm-*` | `.ai/.templates/frontend/` (token yalnız 01_Abstracts) |
| UI | Glassmorphism / Ambient Aura | — | ADR-018 (footer player vaporwave) |
| API | ApiClient (fetch) | — | SPA → ApiClient → Gateway (§6A.5) |
| Erişilebilirlik | WCAG | 2.2 AA | k11 kısıtı |
| Test | Vitest + browser test | ≥%80 | §17 |
| Envanter | C01-C16 BEM sınıfları | `.ai/ui-design/02-component-inventory.md` | Guardrail #11 |

**Stack kuralları:**
- React/Vue/Angular **YASAK** (Forbidden Patterns §21).
- Framework değil vanilla Custom Element / class-based component.
- `var` YASAK → `const`/`let`. `eval`/`Function` YASAK.
- Veri bağlama: `textContent` + `DocumentFragment`; `innerHTML` yalnız statik, güvenilir
  markup'ta ve DOMParser ile sanitize edilmişse.

---

## 3. CONTEXT

- **Panel:** `music.coremusic.net` (SPA, Port 81); footer player tüm panellerde görünür
  (ADR-018).
- **Konum:** Layout 1024×600 pixel reference — Header 60px (y:0-60), İçerik 450px
  (y:60-510), **Footer 90px (y:510-600)** (Guardrail #17, ASCII index).
- **Veri kaynağı:** `/api/v1/music/tracks/{id}/stream` (bkz. backend-api örneği),
  metadata endpoint'i üzerinden; SPA asla MySQL/PDO görmez (§6A.5).
- **State:** şu anki çalan parça, pozisyon, ses seviyesi, shuffle/repeat; kalıcılık
  sessionStorage'da (auth DEĞİL — auth cookie/session).
- **Mockup zorunlu:** `.ai/ui-design/01-mockup-index.md` (19 PNG) + `02-component-inventory.md`
  + `tokens/design-tokens-master.md` **kod ÖNCESİ okunur** (Guardrail #11; okunmazsa revert).

---

## 4. OBJECTIVE + SCOPE

**Amaç:** 90px yüksekliğinde, erişilebilir, performanslı bir **Footer Music Player**
bileşeni üretmek: cover + başlık + transport kontrolleri + progress bar + ses +
eşitleme/handoff göstergesi; 1024 ve 1920 genişliklerinde tek component ile responsive.

**Genişleme:**
- Play/Pause/Next/Prev + klavye kısayolları (Space, ←/→).
- Progress bar: seek (tıkla/sürükle), zaman formatı `m:ss`.
- Ambient Aura: çalan parçanın enerjisine göre `--cm-aura-*` token geçişi (animasyon 200-400ms).
- Handoff göstergesi: cihazlar arası aktarım durumu (senkron/aktarılıyor).

**KAPSAM:** footer player component'i (HTML iskeleti + CSS + JS modülü) · responsive
reaksiyonlar (≥1024, 1920) · erişilebilirlik · birim testleri (Vitest) · browser smoke.

**KAPSAM DIŞI:** player backend'i, playlist sayfası, equalizer ekranı (K10 diğer paneller),
yeni tasarım token'ları (mevcut token kullanılır), layout header/içerik bölgesi.

---

## 5. FUNCTIONAL REQUIREMENTS

| # | Gereksinim | Davranış | Kabul |
|---|-----------|----------|-------|
| FR-1 | Oynat/Duraklat | Buton + Space; ikon aria-live ile değişir | aria-pressed doğru |
| FR-2 | İleri/Geri | Next/Prev; liste başı/sonunda davranış (repeat'te sar) | 4 istek/hataysa UI durur |
| FR-3 | Progress | Pozisyon her 250ms güncellenir (UI); seek tık/drag | Seek sonrası stream Range uyumu |
| FR-4 | Süre | `m:ss / m:ss`; bilinmiyorsa `--:--` | NaN gösterilmez |
| FR-5 | Ses | Slider + mute; 0-100 adım 1 | Bas + boost yok (yalnız CSS token) |
| FR-6 | Metadata | Başlık, sanatçı, cover (lazy, placeholder) | Kırık görsel fallback |
| FR-7 | Ambient Aura | Enerji değeri → CSS variable tween | prefers-reduced-motion'da kapalı |
| FR-8 | Handoff | Durum rozeti (senkron/aktarılıyor/hata) | Yalnız görünür durumda |
| FR-9 | Klavye | Tab sırası mantıklı; fokus görünür (AA) | Fokus asla kaybolmaz |
| FR-10 | DOM bütünlüğü | Veri → textContent; asla ham innerHTML | TrustedTypes ihlali testi |

**Davranış ayrıntısı:**
- FR-3: seek sırasında UI 250ms'de bir senkron; çakışmada kullanıcı girdisi öncelikli
  (pointer isDragging → fetch sonucu ezmez).
- FR-7: yalnız CSS custom property geçişi; JS tarafında renk hesabı yok (token tek kaynak).
- FR-10: API'den gelen başlık/sanatçı `textContent` ile yazılır (XSS vektörü kapatılır).

---

## 6. TECHNICAL REQUIREMENTS

- **DOM:** tek mount noktası (`#cm-footer-player`); bileşen `PlayerController` class
  (private field `#state`, `#els`), ES module (`type="module"`).
- **Event'ler:** Custom Events (`cm:track-change`, `cm:play-state`) — global scope kirletilmez;
  event delegation tek listener (container).
- **Performans:**
  - Progress tick 250ms; `requestAnimationFrame` yalnız seek drag'de.
  - Layout thrash yok: `transform: translateX()` (paint), `offsetWidth` okuması yok.
  - Cover `loading="lazy"`; placeholder SVG inline.
  - İlk render ≤ 8KB JS gzip (soft hedef — `[VERIFY REQUIRED]` ölçümle).
- **CSS:** BEM `cm-player`, `cm-player__progress`, `cm-player--muted`;
  token yalnız `--cm-*` (01_Abstracts); katman dosyasına ham hex/px YASAK (AGENTS §10).
- **Responsive:** mobile-first DEĞİL bu komponentte panel reference 1024 →
  `@media (min-width: 1920px)` geniş variant; `@media (max-width: 1023px)` fallback
  (§12 backward-compat zorunlu).
- **Compatibility:** modern evergreen (ES2022); fallback: private fields polyfill yoksa
  normal field + comment — `[VERIFY REQUIRED]` (hedef tarayıcılar docs'ta).

---

## 7. ARCHITECTURE

```text
[Music SPA · K10]
  └── PlayerController (module, vanilla)
        ├── state: {track, playing, position, duration, volume, aura, handoff}
        │      ↑ Custom Events / ApiClient data
        ├── view (vanilla DOM, textContent binding)
        │      ├── cover   .cm-player__cover      (BEM)
        │      ├── meta    .cm-player__meta       (title/artist)
        │      ├── transport .cm-player__btn--prev/play/next
        │      ├── progress .cm-player__bar  (role="slider")
        │      └── volume  .cm-player__vol
        └── CSS: ITCSS 05_Pages/04_Components → token 01_Abstracts (--cm-*)
                    └── Ambient Aura: --cm-aura-bg / --cm-aura-glow tween
[ApiClien] → HTTP → Gateway (§6A.5)
```

- **Katman:** view → controller → ApiClient; component, SSR/direct DB görmez.
- **State akışı:** `cm:track-change` event'i → controller state → DOM update (top-down);
  kullanıcı girdisi → handler → API (yatay).
- **GSAP/kütüphane YOK** — Vanilla CSS transition + rAF (kütüphane bağımlılığı ekleme).

**State machine (oynatıcı durumları):**

```text
            ┌────────────── play() ──────────────┐
            ▼                                   │
  [idle] → [loading] → [playing] ──pause()──▶ [paused]
    │          │          │  ▲                   │
    │          │          └──┘ seek/play devam   │
    │          │                                 │
    │          └─ hata ─▶ [error] ─retry(1x)──▶ [loading]
    │                        │
    │                        └─ başarısız ─▶ [stopped] (+toast)
    └─ track değişimi: her durumdan [loading] (AbortController ile eski iptal)

Geçiş kuralları:
- [loading] iken ikinci play() → kuyruk (idempotent, çift fetch YASAK)
- [error] → kullanıcı el ile play = retry sayılır (max 1 otomatik)
- track bitimi → repeat(shuffle) yoksa [idle] + sonraki (listede ise)
- handoff rozeti BAĞIMSIZ: synced | transferring | failed (state machine üstü overlay)
```

**Render pipeline (tek yön):**

```text
state değişimi → #render() (diff yok, hedefli update: yalnız değişen node'lar)
              → classList/attribute/textContent güncelle
              → CSS transition (aura, progress transform)
YASAK: her tick'te tüm DOM'u yeniden kurmak (Fragment rebuild) — performans ihlali
```

---

## 8. DATA / DATABASE

Bu görevde **doğrudan DB yok** (frontend). Veri sözleşmesi (ApiClient response):

```json
{
  "track": {
    "id": 1042,
    "title": "…",
    "artist": "…",
    "cover_url": "/assets/covers/1042-320.webp",
    "duration_sec": 258,
    "energy": 0.72,
    "stream_url": "/api/v1/music/tracks/1042/stream"
  },
  "handoff": { "state": "synced", "device": "livingroom" }
}
```

- Alan adları backend OpenAPI ile sabitlenir; eksik alan → UI'da görünür tutulmaz
  (kalıp: bilinmeyen → placeholder, `[VERIFY REQUIRED]`).
- `energy` (0-1) → Aura renk hesaplaması **token üzerinden** (CSS `color-mix`/predefined
  stops); JS'te hex uydurmak YASAK.
- Cache: son track state sessionStorage (`cm.player.state`, auth DIŞINDA).

---

## 9. API

- **GET** metadata (ör. `/api/v1/music/me/now-playing`) — 200/401/429.
- **POST** seek/volume (eğer backend kabul ediyorsa — OpenAPI'den doğrula; yoksa client-side
  state only) — CSRF token `csrf_token` zorunlu (Guardrail #6).
- **stream** için: `audio.src = stream_url` + `Range` başlığı tarayıcıdan (backend örneği 206).
- **Hata sözleşmesi:** `{error:{code,message,correlation_id}}` → UI: 401 → auth akışına
  yönlendir; 429 → toast + Retry-After; 4xx → sessiz (oyuncu durur, durum metni görünür).
- **Idempotency:** POST'lar idempotent değilse buton disable (double-click koruması).

---

## 10. SECURITY

- **XSS:** Yalnız `textContent` / `createElement` + `appendChild`; `innerHTML` ile veri yok;
  `cover_url` yalnız `https:`/kök-path (`sanitizeURL` kontrolü) → javascript: vektörü reddi.
- **TrustedTypes:** `DOMParser` + sanitizer kullanılır (§21 innerHTML/doğru satırı).
- **Auth:** token cookie/session'da; JS yalnızca `credentials: 'include'`;
  `localStorage`/`sessionStorage` auth için YASAK (§21).
- **CSRF:** state-changing fetch'lerde `X-CSRF-Token: csrf_token` header.
- **CSP:** script inline değil (module src); nonce ile uyum (SecurityHeaders).
- **Gizlilik:** log'a token/PII yok; correlation_id UI'da gösterilebilir (hata toast).

---

## 11. PERFORMANCE

| Metrik | Hedef | Yöntem |
|--------|-------|--------|
| Component init (main thread) | < 5ms | sınıf ölçümü (performance.now) |
| Progress tick | 250ms aralık, jank yok | long task monitor |
| Cover | lazy + placeholder | LCP'e dahil DEĞİL |
| Reflow | 0 (transition transform/opacity) | DevTools layout shift |
| JS | minimal (kütüphane yok) | gzip boyutu raporu |

Kural: ölçüm YOKSA hedef "iyi" diye uydurulmaz; CI'da Lighthouse CI varsa §CI'a bağlanır
(yoksa `[VERIFY REQUIRED]`).

---

## 12. TESTING

| Katman | Araç | Durumlar |
|--------|------|----------|
| Unit (Vitest, jsdom/happy-dom) | state reducer, time format (`0, 59, 61→1:01, NaN→--:--`) | ≥%80 branch |
| Unit | sanitizeURL (javascript:, data:, normal) | hepsi |
| Unit | Event akışı: cm:play-state → aria-pressed | simülasyon |
| Integration (jsdom) | render + click play → handler çağrısı | smoke |
| Browser (Chrome DevTools MCP / Playwright) | gerçek sayfa: görünürlük, seek, klavye | zorunlu (UI değişikliği → browser test) |
| A11y | axe-core (AA): kontrast, ad, rol, klavye | 0 critical |
| Visual | 1024 ve 1920 snapshot vs mockup | ±2px kabul |

**Test edilen/edilmeyen ayrımı:** edilmeyen davranış "verified" yazılmaz (§13 test kuralı).

---

## 13. DEVOPS / INFRA

- Build: yok (vanilla, bundler opsiyonel; varsa esbuild pipeline `[VERIFY REQUIRED]`).
- CI: vitest + eslint (vanilla preset) + github workflow mevcut `ci.yml` (2 dosya — §25.2).
- Deploy: statik asset servisi (assets.coremusic.net, `08_Devices/d-*.css` import zinciri;
  `main.css` YOK — §7.1 kural 6).
- Rollback: component feature flag (`footerPlayerV2`) — kapat = eski sürüm.

---

## 14. WORKFLOW

```text
1. MOCKUP OKU   → 01-mockup-index · 02-component-inventory · design-tokens-master
                  (Guardrail #11 — OKUNMADAN KOD YASAK; okunamıyorsa DUR)
2. ASCII/ÖLÇÜ   → 00-ascii-art-index: Footer y:510-600 (90px); C-bileşen eşlemesi
3. TOKEN         → 01_Abstracts'tan --cm-* (aura, spacing, type) — ham hex YASAK
4. İSKELET       → BEM HTML + ARIA roller (slider için role/aria-valuenow)
5. STATE         → PlayerController class (private fields) + Custom Events
6. CSS           → component katmanı; 1920/1024 media query'leri; fallback §12
7. BAĞLANTI      → ApiClient (only); error contract handling
8. TEST          → Vitest unit + a11y axe + browser smoke (gerçek sayfa)
9. DENETİM       → §20 checklist; mockup karşılaştırma (±2px); guardrail taraması
10. TESLIM       → dosyalar + test raporu + açık konular ([VERIFY REQUIRED])
```

---

## 15. CONSTRAINTS

1. **Framework YASAK** (ADR-001): React/Vue/Angular/Svelte yok — Vanilla JS ES2022.
2. **Mockup gate** (Guardrail #11): ilgili PNG/ASCII/envanter okunmadan kod → revert.
3. **Tek component** (Guardrail #17): 1024 piksel reference; ayrı HTML/branch YASAK;
   CSS variables + media queries; device CSS yalnız davranış override.
4. Token yalnız 01_Abstracts (`--cm-*`); katmana ham değer yazma (AGENTS §10).
5. Taşıma/silme YOK (import zinciri değişmez, `07_Vendors` dokunulmaz).
6. `innerHTML` veri bağlamada yasak (§21); `var`/`eval` yasak.
7. auth için storage yasak (§21).
8. Guardrail #16: yeni .md üretirse frontend templates (css-template v4.0.0 + notes.md).
9. commit subagent'ta DEĞİL (orkestratör).
10. Bilinmeyen tarayıcı/ölçü → `[VERIFY REQUIRED]`.

---

## 16. DECISION RULES

| Karar | Seçenek | Karar | Gerekçe |
|-------|---------|-------|---------|
| State yeri | JS class / store kütüphane / DOM | JS class + Custom Events | vanilla, kütüphane yok |
| Progress timer | setInterval / rAF | setInterval 250ms (drag'de rAF) | güç tüketimi + yeterli hassasiyet |
| Aura renk | JS hex hesabı / CSS token | CSS `--cm-*` tween | token tek kaynak (AGENTS §10) |
| Seek senkron | her tick fetch / sonda fetch | drag bitiminde tek fetch | spam önleme |
| Layout | absolute px / flex+vars | flex + custom props | responsive + Guardrail #17 |

Öncelik: Mockup > Inventory > Tokens > Implementation Plan (çelişki sırası, §7.1).

---

## 17. ERROR HANDLING

| Hata | UI davranışı | Kod tarafı |
|------|--------------|-----------|
| 401 | toast "Oturum gerekli" + auth yönlendirme | fetch catch → event `cm:auth-error` |
| 404 track | "Parça bulunamadı" + next'e geç | player durur (auto-next 1x) |
| 429 | "Çok istek" + Retry-After sayacı | buton disable süresi |
| Ağ hatası | offline rozeti; progress donar | retry 1x (backoff 1s), sonra dur |
| Stream 416 (seek) | pozisyon eski değere snap | log, `[VERIFY REQUIRED]` |
| Kırık cover | SVG placeholder | error listener → fallback |

Beklenmeyen istisna: try/catch yalnız boundary'de; hata UI'a generic mesaj, detay console
+ correlation_id. Sessiz yakalama (empty catch) YASAK.

---

## 18. OUTPUT FORMAT

**Teslim (Markdown):**
1. Değişen/eklenen dosyalar (yol + rol).
2. HTML iskeleti (BEM + ARIA) tam kod.
3. CSS (component + media query'ler; token referanslı).
4. JS modülü (PlayerController — tam, çalışan).
5. Test dosyaları + Vitest çıktısı (sayılar).
6. Browser/axe raporu (snapshot kısa).
7. Mockup karşılaştırma notu (1024/1920 ±px).
8. [VERIFY REQUIRED] listesi.

Kod tam ve yapıştırılabilir; "...devamını sen tamamla" boşluğu YASAK.

---

## 19. ACCEPTANCE CRITERIA

- [ ] Footer yükseklik 90px (1024 reference) ±2px; y:510-600 oturuyor
- [ ] Play/Pause, Next/Prev, seek, ses — hepsi işlevsel (browser testi)
- [ ] Space/←/→ klavye çalışır; fokus görünür (AA 2.4.7)
- [ ] `role="slider"` + `aria-valuenow/min/max` progress'de mevcut
- [ ] axe-core: 0 critical/serious ihlal
- [ ] title/artist textContent ile bağlanır (innerHTML veri yok — test)
- [ ] `javascript:` cover_url reddedilir (unit test)
- [ ] Ambient Aura token tween çalışır; `prefers-reduced-motion` → kapalı
- [ ] 1024 ve 1920'de tek component görünüm tutarlı (snapshot)
- [ ] Vitest ≥%80; browser smoke pass
- [ ] Token sızıntısı yok (ham hex yalnız 01_Abstracts — tarama 0)
- [ ] Framework import: 0 (bundle grep: react|vue|angular = 0)

---

## 20. VALIDATION (son kendini denetim)

- [ ] Guardrail #11: 3 zorunlu dosya okundu mu? (okunmadıysa kod geçersiz)
- [ ] Guardrail #17: tek component + responsive + media query var mı?
- [ ] FR-1…FR-10 ↔ kod/test karşılığı eksiksiz mi?
- [ ] Yeni requirement eklenmedi mi? (eklendiyse onay)
- [ ] A11y ve mockup uyumu ölçüldü mü (uydurma yok)?
- [ ] Bilinmeyenler `[VERIFY REQUIRED]` mi?
- [ ] **PASS 3 sayım: bu prompt ≥ 500 satır (§0) ✓**

---

## 21. ZERO-HALLUCINATION

- Hedef tarayıcı listesi, gzip boyutu, Lighthouse skoru: ölçülmeden yazılmaz → `[VERIFY REQUIRED]`.
- Mevcut `PlayerController`/benzeri component var mı → diskte doğrulanmadı → implementasyon
  ÖNCESİ arama (duplicate yasak).
- Backend field'ları (`energy`, `handoff`) OpenAPI'den doğrulanacak → yoksa `[VERIFY REQUIRED]`.
- npm paket iddiası yok (vanilla); varsa lockfile kanıtı gerekir.

---

## 22. EDGE CASES

| # | Kenar durum | Tepki |
|---|-------------|-------|
| 1 | Süre bilinmiyor (live/radio) | `--:--`, progress indeterminate (CSS) |
| 2 | 0 saniyelik track | play → otomatik next (guard: max 1 otomatik) |
| 3 | Çok hızlı Next spam (5 tık) | buton queue 300ms throttle; state tutarlı |
| 4 | Seek + track değişimi aynı anda | eski fetch iptal (AbortController) |
| 5 | Kırık/resim olmayan cover | inline SVG placeholder (boyut korunur) |
| 6 | Ses 0 + mute ayrımı | mute=true & volume=0 farklı state (ikon farklı) |
| 7 | 2 sekme aynı state | storage event → UI güncelle (auth değil) |
| 8 | reduced-motion + aura | tween yok, anlık token geçişi |
| 9 | 1023px (fallback) | kompakt variant (cover 40px, metin kısaltma ellipsis) |
| 10 | Unicode başlık uzun | ellipsis + `title` attr (WCAG 1.4.10 kırpmada okunur alan) |

---

## 23. DOCUMENTATION

- Component JSDoc: props/state/event listesi (kısa, neden ağırlıklı).
- BEM şeması inventory'e eklenir (C-bileşen eşlemesi — varsa mevcut Cxx korunur).
- Changelog: `feat(ui): footer player component (vanilla, a11y AA)`.
- Kırık/şüpheli ölçümler `log.md`'ye 1 satır (append-only).

---

## 24. EXAMPLES / SCENARIOS

**Senaryo A — oynatma:**

```text
Kullanıcı Space'e basar → PlayerController.#onKeydown → toggle()
→ aria-pressed=true, ikon ▶→⏸, cm:play-state event
→ <audio> play() → stream Range: bytes=0- (backend 206)
→ progress interval başlar (250ms) → aria-valuenow artar
```

**Senaryo B — XSS denemesi:**

```json
{ "title": "<img src=x onerror=alert(1)>", "cover_url": "javascript:alert(1)" }
```

```text
→ title: textContent ile yazılır → metin olarak görünür, DOM değil
→ cover_url: sanitizeURL reddi → placeholder SVG
→ sonuç: script ÇALIŞMAZ (unit test ile kanıtlanır)
```

**Senaryo C — seek senkronu:**

```text
Kullanıcı bar'ı 1:30'a sürükler → isDragging=true (tick durur)
→ pointerup → tek fetch {position:90} + <audio>.currentTime=90
→ fetch 200 → tick devam; 416 → snap-back + toast (edge #4/#10)
```

**Senaryo D — Ambient Aura geçişi (reduced-motion dahil):**

```text
cm:track-change event'i → controller #state.aura = track.energy (0.72)
→ #applyAura(): style.setProperty('--cm-aura-glow', 'var(--cm-aura-high)')
   (renk DEĞİL — token adı atanır; hesap 01_Abstracts'te)
→ CSS: .cm-player { transition: background-color 300ms ease } (veya none)
→ prefers-reduced-motion: reduce → transition: none (anlık geçiş, AA 2.3.3)
→ Troll: energy bilinmiyorsa → default token (tahmini renk YASAK)
```

**Senaryo E — klavye tam akış (WCAG 2.1.1):**

```text
Tab → play butonu (fokus halkası görünür, outline token'ı)
Space → toggle (sayfa scroll'u engellenir: preventDefault yalnız buton fokusta)
← / →  → 5sn geri/ileri seek (aria-valuenow güncellenir, live region sessiz — AA 4.1.3)
↑ / ↓  → ses ±5 (mute ise unmute)
F008: fokus progress bar üzerindeyken Space = seek play/pause DEĞİL (odak tuzağı yok)
```

---

## DISCOVERY SUMMARY (şablonun üretim özeti)

- **Confirmed:** Vanilla JS ES2022 + ITCSS/BEM (ADR-001), footer 90px (Guardrail #17),
  guardrail #11 mockup gate, token tek kaynak, format = TASK PROMPT, §0 min 500 satır.
- **Confirmed architecture:** component → ApiClient → Gateway; state = class + Custom Events.
- **Remaining [VERIFY REQUIRED]:** mevcut player component'in varlığı, backend now-playing
  contract'ı (`energy`/`handoff`), hedef tarayıcı listesi, gzip/Lighthouse metrikleri.

---

**Authority**: Bayram Ali / Vault Steward
**Generated by**: prompt-maker v1.1.0 (§0 min-500 contract + Format 2 TASK PROMPT)
**Last Updated**: 2026-10-07
**Mode**: Red Team · Human Mode · Truth Mode
