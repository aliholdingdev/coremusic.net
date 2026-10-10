---
type: architecture
category: layer
title: "K011 — UX"
date: 2026-10-09
status: active
version: 2.0.1
authority: SSOT
---

# K011 — UX

## §1 Kimlik
- Katman: K011 · Alan: **A3** (K10-K11 — Presentation/Gösterim).
- Kapsam: sunum katmanı — vanilla JS SPA + ITCSS token sistemi, widget grid, ViewModes.

## §2 Sorumluluk
1. Vanilla JS ES6+; framework YASAK (ADR-001 frozen; R-001/003/004 reddedilenler).
2. ITCSS 11 katman + `--cm-*` token tek kaynak; sabit değer yalnız `01_Abstracts` (kök AGENTS.md §10).
3. ViewModes tek yükleme yolu `<link id="cm-view-css">` (ADR-093).
4. Widget grid kanonik: 1024 → satır 2×2+1×5+1×5 = 12 slot · 1920 → 4×4+1×8+1×8 = 20 slot (kullanıcı kuralı, bağlayıcı — AGENTS §13.9.1).
5. WCAG 2.2 AA ve ui-design C01-C16 uyumu (AGENTS §16).
6. MockupBefore Frontend: `.ai/ui-design/` görseli okunmadan kod yazılmaz (AGENTS §13).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K009 API (JSON sözleşmesi) · K014 ağ (subdomain/statik varlık) — kod importu YASAK, yalnız HTTP/API (plan §3).
- **Üst:** tarayıcı (istemci — en üst katman; bu katmanın üstünde K katmanı yoktur).

## §4 ADR Bağlantıları
ADR-001 (vanilla JS+ITCSS) · ADR-093 (ViewModes tek yükleme yolu) · ADR-018 (footer player) · ADR-044 (theme engine) ·
ADR-045/046 (view mode, cross-view state) · ADR-048 (View Transition) · ADR-004 (multi-domain SPA).

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): `assets.coremusic.net/Css/01_Abstracts/*-tokens.css` (`a-widget-grid-tokens.css` dahil) · `assets.coremusic.net/Css/09_ViewModes/v-{home,pro,studio,car}.css` · `.ai/ui-design/` 21 ekran (AGENTS §13.8).

## §6 Risk / Not
- `tokens-3840.json` / `tokens-tv.json` boş → 3840/TV katmanları PLANNED (AGENTS §24.3 veri bütünlüğü notu).
- Widget grid kuralına aykırı sayım/render revize edilir, kural değil (AGENTS §13.9.1).
## §7 Makro Katman Karşılığı
K011-ux → Makro **K4** (bağlayıcı gruplama, 2026-10-10) · Kök eşleme: [[../00-master-index]] (Makro Eşleme bölümü) · makro özet: [[../katmanli-mimari-k0-k4/index]]

- K4 içinde K011 = **sunum/tasarım sistemi yüzeyi**: vanilla JS SPA + ITCSS token sistemi ile kullanıcıya görünen her şeyin sahibi (makro özet §1 K4 kapsamı).
- Makro komşular: alt = K4 içi K009 (API — JSON sözleşmesi) ve K014 (ağ — subdomain/statik varlık); ikisiyle de kenar **yalnız HTTP/API**'dir, kod importu YASAK (matris §2, plan §3) · üst: tarayıcı/istemci — bu katmanın üzerinde K katmanı yoktur (§3).
- Makro zincirin tamamı: K4 → K3 → K2 → K1 → K0; K011 doğrudan K3/K2'ye bağımlı değildir (makro özet §2.1 Dependency Rule).

## §8 Arayüz (Girdi/Çıktı & API)
- **Girdi (üstten):** tarayıcı isteği + sunucudan gelen HTML/JSON; veri K009 API'den HTTP/JSON ile alınır (matris §2: "K011 UX → K009 API · K014 ağ · HTTP/API sözleşmesi; kod importu YASAK").
- **Çıktı:** render edilmiş DOM + `--cm-*` token'lı CSS; ViewMode çıktısı `v-{home,pro,studio,car}.css` ile tek yükleme yolu `<link id="cm-view-css">` üzerinden (ADR-093, §2.3).
- **Diskte kanıtlı somut varlıklar (§5, 2026-10-09 ölçümü):**

| Varlık | Gerçek yol | Rol |
|---|---|---|
| Token katmanı | `assets.coremusic.net/Css/01_Abstracts/*-tokens.css` (incl. `a-widget-grid-tokens.css`) | `--cm-*` design token sözleşmesi — sabit değer tek kaynak |
| ViewMode CSS | `assets.coremusic.net/Css/09_ViewModes/v-{home,pro,studio,car}.css` | 4 görünüm modu |
| Ekran spesifikasyonları | `.ai/ui-design/` — 21 ekran | UI sözleşmesi (mockup gate, AGENTS §13) |

- **İzinli/yasaklı çağrı özeti:** K011 → K009/K014 **yalnız HTTP/API**; JS/CSS kod bağımlılığı YASAK (plan §3; makro özet §3) · framework YASAK (ADR-001 **frozen**; §2.1) · sabit CSS değeri yalnız `01_Abstracts` (kök AGENTS §10) · widget grid kanonik kuralı bağlayıcı: 1024 → 12 slot · 1920 → 20 slot (AGENTS §13.9.1).
- ViewMode CSS dosyalarının içeriği (token kullanımı) bu görevde okunmadı → **UNKNOWN** (makro özet §7-7).

## §9 Hata Modları ve Güvenlik Sınırı
- **Failure modes (katman sınırında):**
  1. **`tokens-3840.json` / `tokens-tv.json` boş** — 4K/TV katmanı render edilemez; uydurma token YASAK, boş kalır (§6; makro özet §5-FM4).
  2. **Widget grid ihlali** — kanonik sayıma aykırı render revize edilir (kural değil, AGENTS §13.9.1).
  3. **ITCSS katman sırası ihlali / token aşımı** — ham hex/px katman dosyasına yazılmaz (kök AGENTS §10); ihlal LINT kapısında yakalanır.
  4. **ViewMode yükleme yolu ihlali** — ADR-093 tek yol kırılırsa tema/mod durumu bozulur.
- **İzolasyon:** K011'in hatası istemci tarafında izole edilir; sunucu katmanına kod yayılmaz (kod importu YASAK — matris §2). SPA router hataları istemcide izole (ADR-021, k4 makro §5).
- **Güvenlik sınırı (kim neyi doğrular):** K011 XSS/CSP uygulamaz — CSP nonce **edge'de K014** üretilir ve dağıtılır (ADR-012; [[../K014-ag/index]] §2.3); auth/CSRF/RateLimit K009/K007 sınırındadır (kanonik sıra: OriginCheck+CSRF → RateLimit → CSP nonce → Auth → PageRouter — k2 makro §3.2). K011'in sınır kapısı: WCAG 2.2 AA + ui-design C01-C16 + MockupBefore Frontend (AGENTS §13, §16).

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

| Bulgu | Durum |
|---|---|
| `tokens-3840.json` / `tokens-tv.json` boş — 4K/TV tasarımı yok | ⚠️ PLANNED — uydurma yasak (§6; makro özet §7-4) |
| ViewMode CSS dosyalarının içeriği (token kullanımı) | UNKNOWN — okunmadı (makro özet §7-7) |
| JS tarafı (vanilla SPA router uygulaması) disk kanıtı bu görevde taranmadı | ⚠️ VERIFICATION REQUIRED |
| ui-design C01-C16 denetim sonuçları (canlı ölçüm) | UNKNOWN — bu görevde çalıştırılmadı |
| Widget grid render sayım denetimi (canlı DOM ölçümü) | UNKNOWN — bu görevde çalıştırılmadı |
