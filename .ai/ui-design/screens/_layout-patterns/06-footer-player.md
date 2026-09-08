---
title: "CoreMusic — Footer Player Layout (Seekbar + Volume Fix)"
type: reference
category: layout-pattern
date: 2026-09-08
status: active
version: 1.0.0
authority: PNG Visual Analysis (Linux 1024 - Home Page.png + kullanıcı screenshot)
source_of_truth: ".ai/.png/home-1024/Linux  1024 - Home Page.png"
related:
  - "assets.coremusic.net/Css/03_Layout/_footer.css"
  - "home.coremusic.net/footer.php"
  - "assets.coremusic.net/js/coreplayer/coreplayer.seekbar.js"
  - "assets.coremusic.net/js/coreplayer/coreplayer.volume.js"
---

# Footer Player — Seekbar + Volume ASCII Art

**1024×600 footer player bar.** PNG mockup birebir ölçüler + tespit edilen 2 bug'ın düzeltme spec'i.

---

## 1. Footer Genel Görünüm (PNG birebir)

```
FOOTER — y:510-600, h:90px (token: --footer-h)
┌──────────────────────────────────────────────────────────────────────────────────┐ y:0
│ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ │ ← seekbar
│ ┌────────┐ ♪ Şarkı Adı : Göksel - Sevil Neşelen              ⏮   ▶   ⏹   ⏭     │
│ │ KAPAK  │ ● Album : Hayat Rüya Gibi                                          │
│ │ 88×88  │ ♪ Sanatçı : Göksel                          🔈 ▬▬▬▬▬▬▬ ▭  % 100   │
│ └────────┘ ⏱ 00:00:00 / 00:05:00 / 🎧 350 kbps                            ↑dip │
└──────────────────────────────────────────────────────────────────────────────────┘ y:90
   ZON 1 (sol, %34)            ZON 2 (orta, merkez)         ZON 3 (sağ, right:30px)
```

---

## 2. BUG 1 — Seekbar (footer üstü, KAİ sözleşme)

### 2.1 Mevcut (HATALI) yapı

```
.footer__progress                    input#seekbar
h: 3px  (track)                      h: 16px, top: -6px  ← FOOTER ÜSTÜNDEN 6px TAŞIYOR
┌─────────────────────────┐
│ ▓▓▓▓▓▓▓ 3px bar         │ ─┐
└─────────────────────────┘  │ 3px ≠ 16px  → YÜKSEKLİK EŞİT DEĞİL
   ▲── footer üst kenarı ────┘
▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒  ← input -6px'ten başlıyor, footer DIŞINA taşıyor
```

**Belirtiler:** input 16px + top:-6px → tıklama alanı footer üstünden 6px taşar; bar 3px,
input 16px → container/input yükseklikleri eşit değil. PNG bar ≈4px, mevcut 3px.

### 2.2 Hedef (DÜZELTİLMİŞ) yapı

```
.footer__progress                    input#seekbar
top:0, h:16px (hit alanı)            top:0, h:16px  ← CONTAINER = INPUT (EŞİT)
┌─────────────────────────┐
│ ▓▓▓▓▓▓▓▓ 4px bar (z:1)  │ ← footer ÜST KENARINA bitişik (PNG birebir)
│ ░░░░░░░░ 4px track (z:0)│ ← ::before track
│                         │ ← kalan 12px şeffaf tıklama alanı (footer içinde)
└─────────────────────────┘
   ▲── footer üst kenarı — TAŞMA YOK ▲
```

### 2.3 Spec

| Özellik | Eski | Yeni | Gerekçe |
|---------|------|------|---------|
| `.footer__progress` height | 3px | **16px** | hit alanı, taşmasız |
| `.footer__progress` bg | rgba(255,255,255,0.15) | transparent | track `::before`'a taşındı |
| `::before` (track) | — | top:0, h:**4px**, rgba(255,255,255,0.15) | PNG bar ölçümü |
| `input#seekbar` top | -6px | **0** | taşma yok |
| `input#seekbar` height | 16px | **16px** | container = input → EŞİT |
| `.footer__progress-bar` | h:100% (3px), in-flow | absolute top:0, h:**4px** | PNG ölçümü |

**KAİ ID Sözleşmesi (DEĞİŞMEZ):** `#seekbarclick` (container) · `#seekbar` (input) · `#seekbar2` (bar)

---

## 3. BUG 2 — Volume Slider (ZON 3 sağ)

### 3.1 Mevcut (HATALI) yapı

```
.footer__volume-track          input#volume (thumb 14px DAİRE)
w:115px, h:8px                 h:8px'te 14px thumb → TAŞMA
┌───────────┐
│ ▬▬▬▬▬●    │  ← thumb (○ daire) 8px track içinde ortalanmış, üstten/alttan 3px taşar
└───────────┘
 track 8px ≠ thumb 14px  → INPUT/DİV DENGESİZ
 thumb: border-radius:50% (DAİRE)  → KARE DEĞİL
```

### 3.2 Hedef (DÜZELTİLMİŞ) yapı — Referans proje birebir (v2.1.0)

```
.volume-set-slider (flex, align-items:center, gap:10px)
┌──────┐   ┌───────────────────┐   ┌────────┐
│ 🔈   │   │        ▭          │   │ % 100  │
│20×20 │   │  #volumeclick     │   │        │
│ icon │   │  180×10           │   └────────┘
└──────┘   └───────────────────┘
            Dosya: 04_Components/c-footer-volume.css (ID seçici — tek kaynak)

#volumeclick (container — RELATIVE, 180×10, overflow:hidden + clip-path:inset(0)):
┌───────────────────┐ 10px
│████████████ [■]   │  ← #volume2 fill (ABSOLUTE top:0, h:10, w:% JS, #ff00d5)
│████████████       │  ← #volume input (ABSOLUTE top:0 — fill ÜSTÜNDE, track #f1f1f1)
└───────────────────┘
   ::after kenar fade: sağ ve soldan %10 beyaz opacity 0.5 gradient
   input ::-webkit-slider-runnable-track: 100%×10 #f1f1f1 (radius:0)
   thumb: 10×10 KARE (radius:0) #ff00d5 — container 10px içinde flush

TÜM YÜKSEKLİKLER = 10px (container = track = fill = thumb) → EŞİT
KENARLAR ROUNDED DEĞİL (radius:0 — thumb, track, fill; seekbar bar dahil)
```

### 3.3 Spec (referans proje: `Yeni klasör/c-footer-volume.css` birebir)

| Özellik | Eski (hatalı) | Yeni | Gerekçe |
|---------|------|------|---------|
| `#volumeclick` | class 115×8 (class kaybediyordu) | **ID: relative, 180×10, overflow:hidden, clip-path** | ID seçici tek kaynak, çakışma yok |
| `#volume2` (fill) | class top:0 h:8 | **ID: absolute top:0, h:10, w:%** | referans birebir |
| `#volume` (input) | class 100%×14 | **ID: absolute top:0, 180×10** | referans: hem fill hem input absolute |
| native track | yok (şeffaf) | **10px, #f1f1f1** | referans görsel track |
| thumb | 14×14 radius:50% beyaz (daire) | **10×10 radius:0 #ff00d5 (KARE)** | referans + "kare" talebi |
| yükseklikler | 8≠14 ≠14 | **hepsi 10px = EŞİT** | "height eşit değil" kök çözüm |
| kaynak dosya | _footer.css (03_Layout, class) | **c-footer-volume.css (04_Components, ID)** | specificity: ID her zaman kazanır |

**Önceki v1.0 notu:** 14px wrapper + ::before rail yaklaşımı `_footer.css`'te (class) olduğundan
`#volume::-webkit-slider-thumb` (ID, 04_Components) tarafından EZİLİYORDU — canlıda beyaz daire
render ediliyordu. Çözüm: geometri ID seçicilerle component katmanına taşındı (v2.1.0).

---

## 4. Hizalama Kuralları

```
TEK EKSEN — ZON 3 dikey hizalama (footer 90px):
  🔈 icon (20px) ─┐
  track (14px)   ─┼─ hepsi .volume-set-slider flex center → tek merkez ekseni
  % 100 (12px)  ─┘
  rail+fill+thumb: bottom-flush (dip) — thumb tabanı = rail tabanı = fill tabanı
```

## 5. Responsive (değişmez — mevcut)

| Breakpoint | track w | icon/util |
|-----------|---------|-----------|
| default | 115px | --fp-util-size:22px |
| ≤1024px | 145px | 13px |
| ≥2561px | 180px | — |
| <768px | utility gizli | — |

## 6. WCAG

| Öğe | Boyut | Durum |
|-----|-------|-------|
| seekbar input | 16px (genişlik full) | ⚠️ slider — KAİ click alanı container genişliği telafi eder |
| volume thumb | 14px | ⚠️ track wrapper (115×14) tıklama alanı — kabul |
| focus-visible | outline 2px --fp-accent | ✅ |

## 7. Uygulama Dosyaları

| Dosya | Katman | Değişiklik |
|-------|--------|-----------|
| `assets.coremusic.net/Css/03_Layout/_footer.css` | ITCSS 03_Layout | seekbar + volume blokları (v2.2.0) |
| `home.coremusic.net/footer.php` | L3 | DEĞİŞMEZ (ID sözleşmesi korundu) |
| `js/coreplayer/*.js` | L3 | DEĞİŞMEZ |

---

*Footer Player Layout v1.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Date: 2026-09-08*
