---
title: "Icon Asset Catalog"
type: reference
category: assets
updated: 2026-08-11
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Icon Asset Catalog

**Zorunlu Baglantilar:** [[00-mockup-index]] · [[01-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. Amaç

CoreMusic arayüzünde kullanılacak tüm ikon ve görsel varlıkların tek kataloğudur. Ajanlar bu dosyadan ikon yollarını ve tema varyasyonlarını okur.

---

## 2. Asset Root

Tüm görseller şu kök dizinde saklanır:

```
assets.coremusic.net/
```

---

## 3. Theme Variants

| Tema | Dizin | Kullanım |
|------|-------|----------|
| **Pink (Default)** | `Image/res-pink/` | Varsayılan tema (female) |
| **Blue** | `Image/res-blue/` | Erkek teması |
| **Default (Neutral)** | `Image/res-default/` | Cinsiyet belirtilmemiş |

### 3.1 Tema Seçim Mantığı

```
if user.gender === 'female'
    → Image/res-pink/
else if user.gender === 'male'
    → Image/res-blue/
else
    → Image/res-default/
```

---

## 4. Icon Inventory

### 4.1 Network Icons

| İkon | Dosya Adı | Boyut | Kullanım |
|------|-----------|-------|----------|
| WiFi Full | `wifi-full.png` | 24×24 | Güçlü sinyal |
| WiFi Medium | `wifi-medium.png` | 24×24 | Orta sinyal |
| WiFi Weak | `wifi-weak.png` | 24×24 | Zayıf sinyal |
| WiFi None | `wifi-none.png` | 24×24 | Bağlantı yok |
| WiFi Scanning | `wifi-scanning.png` | 24×24 | Tarama |
| Bluetooth | `bluethoot.png` | 24×24 | BT durumu |
| Bluetooth Connected | `bluetooth-connected.png` | 24×24 | BT bağlı |

### 4.2 System Icons

| İkon | Dosya Adı | Boyut | Kullanım |
|------|-----------|-------|----------|
| Battery Full | `battery-full.png` | 24×24 | Pil dolu |
| Battery Medium | `battery-medium.png` | 24×24 | Pil orta |
| Battery Low | `battery-low.png` | 24×24 | Pil düşük |
| Battery Charging | `battery-charging.png` | 24×24 | Şarj oluyor |
| Settings | `settings.png` | 24×24 | Ayarlar |
| Power | `power.png` | 24×24 | Güç butonu |

### 4.3 Navigation Icons

| İkon | Dosya Adı | Boyut | Kullanım |
|------|-----------|-------|----------|
| Search | `search.png` | 24×24 | Arama |
| Back | `back.png` | 24×24 | Geri |
| Menu | `menu.png` | 24×24 | Hamburger menü |
| Close | `close.png` | 24×24 | Kapat |
| Arrow Left | `arrow-left.png` | 24×24 | Sol ok |
| Arrow Right | `arrow-right.png` | 24×24 | Sağ ok |
| Chevron Down | `chevron-down.png` | 24×24 | Açılır menü |

### 4.4 Player Icons

| İkon | Dosya Adı | Boyut | Kullanım |
|------|-----------|-------|----------|
| Play | `play.png` | 24×24 | Çal |
| Pause | `pause.png` | 24×24 | Duraklat |
| Stop | `stop.png` | 24×24 | Durdur |
| Next | `next.png` | 24×24 | Sonraki |
| Prev | `prev.png` | 24×24 | Önceki |
| Volume | `volume.png` | 24×24 | Ses |
| Volume Mute | `volume-mute.png` | 24×24 | Sessiz |
| Shuffle | `shuffle.png` | 24×24 | Karıştır |
| Repeat | `repeat.png` | 24×24 | Tekrarla |
| Repeat One | `repeat-one.png` | 24×24 | Tekrarla (1) |
| Equalizer | `equalizer.png` | 24×24 | EQ |
| Fullscreen | `fullscreen.png` | 24×24 | Tam ekran |

### 4.5 Action Icons

| İkon | Dosya Adı | Boyut | Kullanım |
|------|-----------|-------|----------|
| Heart | `heart.png` | 24×24 | Favori |
| Heart Filled | `heart-filled.png` | 24×24 | Favori (dolu) |
| Download | `download.png` | 24×24 | İndir |
| Share | `share.png` | 24×24 | Paylaş |
| Delete | `delete.png` | 24×24 | Sil |
| Edit | `edit.png` | 24×24 | Düzenle |
| Copy | `copy.png` | 24×24 | Kopyala |
| Paste | `paste.png` | 24×24 | Yapıştır |
| Add | `add.png` | 24×24 | Ekle |
| Remove | `remove.png` | 24×24 | Çıkar |
| Refresh | `refresh.png` | 24×24 | Yenile |
| Info | `info.png` | 24×24 | Bilgi |
| Warning | `warning.png` | 24×24 | Uyarı |
| Error | `error.png` | 24×24 | Hata |
| Success | `success.png` | 24×24 | Başarılı |

### 4.6 File Type Icons

| İkon | Dosya Adı | Boyut | Kullanım |
|------|-----------|-------|----------|
| Folder | `folder.png` | 24×24 | Klasör |
| Folder Open | `folder-open.png` | 24×24 | Açık klasör |
| File Audio | `file-audio.png` | 24×24 | Ses dosyası |
| File Image | `file-image.png` | 24×24 | Görsel dosya |
| File Video | `file-video.png` | 24×24 | Video dosyası |
| File Text | `file-text.png` | 24×24 | Metin dosyası |
| File Generic | `file-generic.png` | 24×24 | Genel dosya |
| Disk | `disk.png` | 24×24 | Disk |

---

## 5. Image Sizes

| Boyut | Kullanım | Dizin |
|-------|----------|-------|
| 24×24 | Icon (inline) | `icons/` |
| 32×32 | Icon (button) | `icons/` |
| 48×48 | Icon (large) | `icons/` |
| 64×64 | Avatar (small) | `avatars/` |
| 128×128 | Avatar (medium) | `avatars/` |
| 256×256 | Album art (thumb) | `covers/` |
| 512×512 | Album art (large) | `covers/` |
| 1024×1024 | Album art (full) | `covers/` |

---

## 6. Icon Naming Convention

| Pattern | Örnek | Anlam |
|---------|-------|-------|
| `{category}-{state}.png` | `wifi-full.png` | WiFi, dolu durum |
| `{category}-{variant}.png` | `heart-filled.png` | Heart, dolu varyasyon |
| `{category}.png` | `settings.png` | Tek durumlu ikon |

### 6.1 State Suffixleri

| Suffix | Anlam | Örnek |
|--------|-------|-------|
| `-full` | Tam/dolu | `wifi-full.png` |
| `-medium` | Orta | `wifi-medium.png` |
| `-low` | Düşük | `battery-low.png` |
| `-none` | Yok | `wifi-none.png` |
| `-filled` | Dolu (toggle) | `heart-filled.png` |
| `-active` | Aktif | `play-active.png` |
| `-disabled` | Pasif | `play-disabled.png` |

---

## 7. Responsive Image Handling

```html
<!-- Doğru: srcset ile responsive -->
<img
    src="assets.coremusic.net/Image/res-pink/icons/wifi-full.png"
    srcset="
        assets.coremusic.net/Image/res-pink/icons/wifi-full.png 1x,
        assets.coremusic.net/Image/res-pink/icons/wifi-full@2x.png 2x
    "
    alt="WiFi Bağlantısı"
    class="icon icon--wifi"
    width="24"
    height="24"
/>

<!-- ❌ Yanlış: hardcoded path -->
<img src="/static/icons/wifi.png" />
```

---

## 8. CSS Background Icon Kullanımı

```css
/* Doğru: CSS custom property ile */
.icon-wifi {
    background-image: url('assets.coremusic.net/Image/res-pink/icons/wifi-full.png');
}

/* Tema değişikliğinde */
[data-gender="male"] .icon-wifi {
    background-image: url('assets.coremusic.net/Image/res-blue/icons/wifi-full.png');
}

/* ❌ Yanlış: hardcoded tema */
.icon-wifi {
    background-image: url('/static/icons/wifi-pink.png');
}
```

---

## 9. Quick Reference

| Kullanım | Dizin | Boyut |
|----------|-------|-------|
| Inline icon | `icons/` | 24×24 |
| Button icon | `icons/` | 32×32 |
| Large icon | `icons/` | 48×48 |
| Avatar | `avatars/` | 64-128px |
| Album cover | `covers/` | 256-1024px |
| Pink tema | `res-pink/` | — |
| Blue tema | `res-blue/` | — |
| Neutral tema | `res-default/` | — |

---

## 10. Cross References

| Kaynak | Hedef |
|--------|-------|
| Bu dosya | [[00-mockup-index]] — PNG referansları |
| Bu dosya | [[01-component-inventory]] — Bileşen ikonları |
| Bu dosya | [[tokens/design-tokens-master]] — Tema renkleri |
| Bu dosya | [[ADR-044-dynamic-user-theme-engine]] — Tema engine |

---

## 11. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Total Icons | 50+ |
| Categories | 6 |
| Theme Variants | 3 |
| Image Sizes | 8 |
| Status | Red Team · Human Mode · Truth Mode verified |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-11
**Mode:** Red Team · Human Mode · Truth Mode
