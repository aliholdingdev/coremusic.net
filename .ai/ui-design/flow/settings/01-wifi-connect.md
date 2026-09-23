---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — WiFi Connect Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# WiFi Connect Flow

## 1. Akış Diyagramı (Connection Flow)

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ WiFi Modal Aç   │
│ (Ayarlar'dan)   │
└────────┬────────┘
         │
┌────────▼────────┐
│ WiFi Açık mı?   │
└────────┬────────┘
  Kapalı─┤─Açık
  │      │     │
┌─▼────┐ │  ┌──▼────────────┐
│Toggle│ │  │Ağ Taraması    │
│Aç    │ │  │(3sn timeout)  │
└──┬───┘ │  └──┬────────────┘
   │     │     │
   │  ┌──▼────────────┐
   │  │Ağ Bulundu mu? │
   │  └──┬────────────┘
   │ Yok─┤─Var
   │  │  │     │
   │┌─▼──▼──┐  │
   ││Tarama │  │
   ││Yenile │  │
   │└───────┘  │
   │        ┌──▼────────────┐
   │        │Ağ Şifreli mi? │
   │        └──┬────────────┘
   │  Şifresiz─┤─Şifreli
   │    │      │      │
   │┌───▼───┐┌─▼────┐┌▼──────────┐
   ││Bağlan ││Şifre ││Bağlan     │
   ││Direkt ││Gir   ││           │
   │└───┬───┘└─┬────┘└┬──────────┘
   │    │      │      │
   │    └──────┴──────┘
   │           │
   │    ┌──────▼──────┐
   │    │Bağlantı     │
   │    │Durumu       │
   │    └──────┬──────┘
   │      ┌────┴────┐
   │      │         │
   │  ┌───▼───┐ ┌───▼──────┐
   │  │Başarılı│ │Başarısız │
   │  │[OK]    │ │[X] Tekrar│
   │  └───────┘ └──────────┘
```

## 2. State Machine

```
┌──────────────┐  Scan   ┌──────────────┐  Select  ┌──────────┐
│ Disconnected │────────▶│  Available   │────────▶│ Password │
└──────┬───────┘         └──────────────┘         └────┬─────┘
       ▲                                                │
       │ Disconnect                               Connect│
       │                                                │
┌──────┴───────┘                               ┌───────▼──────┐
│ Disconnected │◀──────────────────────────────│  Connected   │
└──────────────┘                               └──────────────┘
```

### Durum Tablosu

| Mevcut Durum | Event | Yeni Durum | Aksiyon |
|:------------:|:-----:|:----------:|---------|
| Disconnected | Toggle ON | Scanning | WiFi aç |
| Disconnected | Toggle OFF | Disconnected | WiFi kapat |
| Scanning | Ağ bulundu | Available | Listeyi göster |
| Scanning | Timeout (3sn) | Disconnected | "Ağ bulunamadı" |
| Available | Ağ seç | Password | Şifre modalı |
| Available | Şifresiz ağ seç | Connecting | Direkt bağlan |
| Password | Bağlan tıkla | Connecting | Şifre gönder |
| Connecting | Başarılı | Connected | [OK] göster |
| Connecting | Başarısız | Available | Hata mesajı |
| Connected | Bağlantıyı Kes | Disconnected | Toggle OFF |

## 3. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: WiFi Modal (Ayarlar'dan açılır)                    │
│                                                              │
│ +--- MODAL (w:480, glass) ------------------------------+   |
│ |                                                        |   |
│ |  📶 Wi-Fi                     Wi-Fi Ağlanları          |   |
│ |  Wi-Fi ⟷ (toggle: pembe)                              |   |
│ |                                                        |   |
│ |  --- Bağlı Olduğu Ağ ---                              |   |
│ |  📶 Bayram Ali Home  [+]Mevcut  🟣WPA2  [Bağlantıyı Kes]|   |
│ |                                                        |   |
│ |  --- Kullanılabilir Ağlar ---                          |   |
│ |  📶 Bayram Ali Home  🟣WPA2  🟡5GHz  [Bağlan]         |   |
│ |  📶 Misafir Ağ       🟣WPA2  🟡2.4GHz [Bağlan]        |   |
│ |  📶 Ofis WiFi         🔒WPA3  🟡5GHz  [Bağlan]         |   |
│ |                                                        |   |
│ +--------------------------------------------------------+   |
└──────────────────────────────────────────────────────────────┘
       │
       │ Ağ seçildi (şifreli)
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 2: Şifre Giriş Modalı                                │
│                                                              │
│ +--- MODAL (w:400, glass) ------------------------------+   |
│ |                                                        |   |
│ |  📶 Bayram Ali Home - Wi-Fi                            |   |
│ |  5GHz · Mükemmel sinyal · 100% · Güvenli Bağlantı     |   |
│ |                                                        |   |
│ |  Kablosuz Ağ Şifresi                                  |   |
│ |  +--------------------------------------------------+  |   |
│ |  | [*][*][*][*][*][*][*][*]                          |  |   |
│ |  +--------------------------------------------------+  |   |
│ |                                                        |   |
│ |  ☐ Kablosuz ağa her zaman otomatik bağlan             |   |
│ |                                                        |   |
│ |  [İptal]  [Bağlan] (pembe buton)                      |   |
│ |                                                        |   |
│ +--------------------------------------------------------+   |
└──────────────────────────────────────────────────────────────┘
       │
       │ Şifre girildi, Bağlan tıklandı
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 3: Bağlantı Durumu                                    │
│                                                              │
│ +--- MODAL ---------------------------------------------+   |
│ |                                                        |   |
│ |  📶 Bayram Ali Home                                    |   |
│ |                                                        |   |
│ |  ○ Bağlanıyor... (spinner)                             |   |
│ |                                                        |   |
│ |  veya                                                  |   |
│ |                                                        |   |
│ |  [OK] Bağlantı başarılı!                               |   |
│ |                                                        |   |
│ |  veya                                                  |   |
│ |                                                        |   |
│ |  [X] Bağlantı başarısız. Şifreyi kontrol edin.         |   |
│ |                                                        |   |
│ +--------------------------------------------------------+   |
└──────────────────────────────────────────────────────────────┘
```

## 4. Hata Senaryoları

| Hata | Çözüm | Max Retry |
|------|-------|:---------:|
| Şifre hatalı | "Şifre yanlış, tekrar girin" | 3 |
| Ağ bulunamadı | "Ağ bulunamadı" + tarama yenile | — |
| Timeout | "Bağlantı kesildi" + tekrar dene | 3 |
| DHCP hatası | "IP alınamadı" + tekrar dene | 2 |
| DNS hatası | "DNS çözülemedi" + tekrar dene | 2 |
| Sinyal zayıf | "Sinyal gücü düşük" uyarısı | — |

## 5. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.wifi-modal` | Modal container |
| `.wifi-modal__toggle` | WiFi açma/kapama |
| `.wifi-network` | Ağ satırı |
| `.wifi-network__badge` | Badge (Mevcut, WPA2, 5GHz) |
| `.wifi-network__signal` | Sinyal gücü ikonu |
| `.wifi-network__action` | Bağlan/Bağlantıyı Kes butonu |
| `.wifi-password__input` | Şifre inputu |
| `.wifi-password__connect` | Bağlan butonu |
| `.wifi-status__spinner` | Bağlantı spinner'ı |
| `.wifi-status__success` | Başarılı mesajı |
| `.wifi-status__error` | Hata mesajı |

## 6. Tier-Bazlı Varyasyonlar

| Tier | Modal Tipi | Ağ Listesi | Şifre |
|------|------------|------------|-------|
| **Phone** | Full-screen | Scroll list | On-screen keyboard |
| **Tablet** | Split-panel | List | On-screen keyboard |
| **Embedded** | Modal overlay | List | On-screen keyboard |
| **Desktop** | Side panel | Detailed list | Physical keyboard |
| **TV** | Full-screen modal | Large cards | Remote input |
| **Car** | Simplified list | Auto-connect | Saved passwords |
| **Watch** | Micro modal | Saved only | Auto-connect |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
