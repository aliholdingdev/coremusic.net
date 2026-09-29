---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Bluetooth Connect Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Bluetooth Connect Flow

## 1. Akış Diyagramı (Decision Flow)

### Pairing Flow

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Bluetooth Açık? │
└────────┬────────┘
  Kapalı─┤─Açık
  │      │     │
┌─▼────┐ │  ┌──▼────────────┐
│Toggle│ │  │Cihaz Taraması  │
│Aç    │ │  │(5sn timeout)  │
└──┬───┘ │  └──┬────────────┘
   │     │     │
   │  ┌──▼────────────┐
   │  │Cihaz Bulundu? │
   │  └──┬────────────┘
   │ Yok─┤─Var
   │  │  │     │
   │┌─▼──▼──┐  │
   ││Tarama │  │
   ││Yenile │  │
   │└───────┘  │
   │        ┌──▼────────────┐
   │        │Daha önce       │
   │        │eşleştirilmiş?  │
   │        └──┬────────────┘
   │  Hayır─┤─Evet
   │    │   │      │
   │┌───▼───┐│ ┌───▼────────┐
   ││Eşleştir││ │Otomatik    │
   ││Başlat  ││ │Bağlan      │
   │└───┬───┘│ └───┬────────┘
   │    │   │     │
   │┌───▼───┐│     │
   ││Eşleşme││     │
   ││Kodu   ││     │
   ││Göster ││     │
   │└───┬───┘│     │
   │    │   │     │
   │┌───▼───┐│     │
   ││Onayla ││     │
   ││(Karşı ││     │
   ││taraf) ││     │
   │└───┬───┘│     │
   │    │   │     │
   │    └───┴─────┘
   │          │
   │    ┌─────▼─────┐
   │    │Bağlan      │
   │    └─────┬─────┘
   │      ┌───┴───┐
   │      │       │
   │  ┌───▼───┐┌──▼──────┐
   │  │Başarılı││Başarısız│
   │  │[OK]    ││[X] Tekrar│
   │  └───────┘└─────────┘
```

## 1A. State Machine

```
┌──────────────┐  Scan   ┌──────────────┐  Select  ┌──────────┐
│     Off      │────────▶│  Scanning    │────────▶│  Found   │
└──────────────┘         └──────────────┘         └────┬─────┘
       ▲                                                │
       │ Off                                     Pairing│
       │                                                │
┌──────┴───────┐                               ┌───────▼──────┐
│     Off      │◀──────────────────────────────│   Pairing    │
└──────────────┘                               └───────┬──────┘
                                                       │
                                                ┌──────▼──────┐
                                                │  Connected  │
                                                └─────────────┘
```

### Durum Tablosu

| Mevcut Durum | Event | Yeni Durum | Aksiyon |
|:------------:|:-----:|:----------:|---------|
| Off | Toggle ON | Scanning | Bluetooth aç |
| Off | Toggle OFF | Off | — |
| Scanning | Cihaz bulundu | Found | Listeyi göster |
| Scanning | Timeout (5sn) | Off | "Cihaz bulunamadı" |
| Found | Eşleştir | Pairing | Eşleşme başlat |
| Found | Otomatik bağla | Connected | Bağlan (bilinen) |
| Pairing | Onaylandı | Connected | Bağlantıyı kur |
| Pairing | Reddedildi | Found | Listeye dön |
| Connected | Bağlantıyı kes | Off | — |

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: Bluetooth Modal                                     │
│                                                              │
│ +--- MODAL (w:480, glass) ------------------------------+   |
│ |                                                        |   |
│ |  🔵 Bluetooth                Bluetooth Cihazları       |   |
│ |  Bluetooth ⟷ (toggle: pembe)                           |   |
│ |                                                        |   |
│ |  --- Bağlı Cihazlar ---                               |   |
│ |  🔵 AirPods Pro    [✓]Bağlı   [Bağlantıyı Kes]       |   |
│ |                                                        |   |
│ |  --- Kullanılabilir Cihazlar ---                       |   |
│ |  🔵 JBL Speaker    [Eşleştir]                         |   |
│ |  🔵 Samsung TV     [Eşleştir]                         |   |
│ |  🔵 Xiaomi Band    [Eşleştir]                         |   |
│ |                                                        |   |
│ |  [+ Yeni Cihaz Tara]                                  |   |
│ |                                                        |   |
│ +--------------------------------------------------------+   |
└──────────────────────────────────────────────────────────────┘
       │
       │ Cihaz seçildi
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 2: Eşleşme Onayı                                      │
│                                                              │
│ +--- MODAL (w:400, glass) ------------------------------+   |
│ |                                                        |   |
│ |  🔵 JBL Speaker ile eşleşme                           |   |
│ |                                                        |   |
│ |  Eşleşme kodu: 123456                                  |   |
│ |  (Karşı taraftaki kodu kontrol edin)                   |   |
│ |                                                        |   |
│ |  [İptal]  [Eşleştir]                                  |   |
│ |                                                        |   |
│ +--------------------------------------------------------+   |
└──────────────────────────────────────────────────────────────┘
       │
       │ Eşleşme onaylandı
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 3: Bağlantı Durumu                                    │
│                                                              │
│ +--- MODAL ---------------------------------------------+   |
│ |                                                        |   |
│ |  🔵 JBL Speaker                                        |   |
│ |                                                        |   |
│ |  ○ Bağlanıyor... (spinner)                             |   |
│ |                                                        |   |
│ |  veya                                                  |   |
| |                                                        |   |
│ |  [OK] Bağlantı başarılı!                               |   |
| |  Şarj: %85                                             |   |
| |                                                        |   |
| |  veya                                                  |   |
| |                                                        |   |
│ |  [X] Bağlantı başarısız. Tekrar dene.                  |   |
| |                                                        |   |
│ +--------------------------------------------------------+   |
└──────────────────────────────────────────────────────────────┘
```

## 3. Hata Senaryoları

| Hata | Çözüm | Max Retry |
|------|-------|:---------:|
| Eşleşme başarısız | "Eşleşme başarısız, tekrar dene" | 3 |
| Cihaz bulunamadı | "Cihaz bulunamadı" + tarama yenile | — |
| Timeout (10sn) | "Bağlantı kesildi" | — |
| Şarj düşük | "Cihazın şarjı düşük" uyarısı | — |
| Sinyal zayıf | "Sinyal gücü düşük" uyarısı | — |

## 4. Tier-Bazlı Varyasyonlar

| Tier | Modal Tipi | Cihaz Listesi | Eşleşme |
|------|------------|---------------|---------|
| **Phone** | Full-screen | Scroll list | Onay butonu |
| **Tablet** | Split-panel | List | Onay butonu |
| **Embedded** | Modal overlay | List | Onay butonu |
| **Desktop** | Side panel | Detailed list | Onay butonu |
| **TV** | Full-screen modal | Large cards | Remote onay |
| **Car** | Auto-connect | Saved only | Otomatik |
| **Watch** | Micro modal | Saved only | Crown onay |

---
## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.modal`, `.modal__content` | blok — MODAL (w:480 / w:400 / w:400, glass) ekranları (bu dosya L118, L141, L158); Embedded tier "Modal overlay" (L193) | closed, opening, open, closing | 02-component-inventory.md L95 (C07) |
| `.toggle`, `.toggle--active` | blok — "Bluetooth ⟷ (toggle: pembe)" (bu dosya L121); durum tablosu Toggle ON/OFF (L102-L103) | off, on, disabled | 02-component-inventory.md L105 (C08) |
| `.btn`, `.btn--primary`, `.btn--secondary` | blok — [Eşleştir] (L127-L129), [Bağlantıyı Kes] (L124), [+ Yeni Cihaz Tara] (L131), [İptal] (L148) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |
| `.toast`, `.toast--error` | blok — "Eşleşme başarısız" / "Cihaz bulunamadı" / "Sinyal gücü düşük" uyarıları (bu dosya L181-L185) | showing, hiding, success, error, info | 02-component-inventory.md L185 (C16) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`).

---

## 6. Adımlar

> ⚠️ VERIFICATION REQUIRED — Kaynak dosyada bu bölüm yok; numaralı, tek-eylem adım listesi akış doğrulamasından sonra doldurulacak.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
