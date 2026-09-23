---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Bluetooth Connect Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Bluetooth Connect Flow

## 1. Akış Diyagramı (Pairing Flow)

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

## 2. State Machine

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

## 3. Ekran Akışı

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

## 4. Hata Senaryoları

| Hata | Çözüm | Max Retry |
|------|-------|:---------:|
| Eşleşme başarısız | "Eşleşme başarısız, tekrar dene" | 3 |
| Cihaz bulunamadı | "Cihaz bulunamadı" + tarama yenile | — |
| Timeout (10sn) | "Bağlantı kesildi" | — |
| Şarj düşük | "Cihazın şarjı düşük" uyarısı | — |
| Sinyal zayıf | "Sinyal gücü düşük" uyarısı | — |

## 5. Tier-Bazlı Varyasyonlar

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

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
