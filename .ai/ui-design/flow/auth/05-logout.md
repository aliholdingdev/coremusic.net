---
title: CoreMusic — Auth Flow: Logout (Detaylı)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/00-ascii-art-views]] §1 (Header)
  - [[01-component-inventory]] C03
  - [[ADR-011-session-management]]
---

# Auth Flow: Logout — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          LOGOUT AKIŞI                                        │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Header'da    │ →  │ Onay         │ →  │ Session      │                  │
│  │ ⏻ butonu    │    │ Dialog       │    │ Silme        │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Onay    │  │ İptal     │        │
│                                         └────┬────┘  └───────────┘        │
│                                              │              │               │
│                                         ┌────▼────┐        │               │
│                                         │ Cookie  │        │               │
│                                         │ Silme   │        │               │
│                                         └────┬────┘        │               │
│                                              │              │               │
│                                         ┌────▼────┐        │               │
│                                         │ Login   │        │               │
│                                         │ Sayfası │        │               │
│                                         └─────────┘        │               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. LOGOUT BUTONU (Header)

### 2.1 — Konum

```
Header'da (y:0-60):
┌─────────────────────────────────────────────────────────────────────────────┐
│ "Core Music" [Nav links...] [Bayram Ali ▾] [📶✳] [🔋] [⚙] [⏻]          │
│                                                                    ↑        │
│                                                              Logout butonu  │
│                                                              22×20px icon   │
│                                                              44×44px hit    │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 — Özellikler

| Özellik | Değer |
|---------|-------|
| İkon | ⏻ (power icon) |
| İkon boyutu | 22×20px |
| Hit area | 44×44px (min touch target) |
| Konum | Header sağ köşe, settings butonunun sağı |
| Tooltip | "Çıkış Yap" |
| ARIA | `aria-label="Çıkış Yap"` |

---

## 3. ONAY DIALOG'U

### 3.1 — Modal Yapısı

```
┌── OVERLAY (rgba(0,0,0,0.5), blur(4px)) ──────────────────────────────────┐
│                                                                             │
│    ┌── MODAL (~300×180px) ────────────────────────────────────────────┐   │
│    │                                                                    │   │
│    │  Çıkış Yap                                                        │   │
│    │                                                                    │   │
│    │  Hesabınızdan çıkış yapmak istediğinize                            │   │
│    │  emin misiniz?                                                     │   │
│    │                                                                    │   │
│    │  [İptal] (C05, sınır)        [Çıkış Yap] (C04, pembe)            │   │
│    │                                                                    │   │
│    └────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 3.2 — Modal Özellikleri

| Özellik | Değer |
|---------|-------|
| Boyut | ~300×180px |
| Merkez | x=512, y=300 |
| Glass | `backdrop-filter: blur(20px)` |
| Başlık | "Çıkış Yap" (16px, 600) |
| Metin | "Hesabınızdan çıkış yapmak istediğinize emin misiniz?" (12px, muted) |
| Butonlar | İptal (C05) + Çıkış Yap (C04) |

---

## 4. DAVRANIŞ DETAYLARI

### 4.1 — Logout Tıklama

```
Kullanıcı ⏻ butonuna tıklar
  → Modal açılır (fade-in: 200ms)
  → Backdrop blur uygulanır
  → "Çıkış Yap" butonu focuslanır
  → Escape tuşu ile kapatılabilir
```

### 4.2 — Onay

```
Kullanıcı "Çıkış Yap"'a tıklar
  → Buton loading durumuna geçer
  → Backend: POST /auth/logout
    → Request: { session_id, csrf_token }
    → Response: { success: true }
  → Cookie'ler silinir:
    → COREMUSIC_SESS (session cookie)
    → theme_gender (tema cookie'si korunur)
    → welcome_shown (hoş geldin cookie'si korunur)
  → LocalStorage temizlenir (sadece auth ile ilgili veriler)
  → Session storage temizlenir
  → Login sayfasına yönlendirme (/auth/login)
  → Geçiş animasyonu: fade-out (200ms)
```

### 4.3 — İptal

```
Kullanıcı "İptal"'e tıklar veya backdrop'a tıklar veya Escape basar
  → Modal kapatılır (fade-out: 150ms)
  → Backdrop blur kaldırılır
  → Header'daki butona geri focus
  → Hiçbir değişiklik yapılmaz
```

---

## 5. TEMİZLİK DETAYLARI

### 5.1 — Silinen Veriler

| Veri | Konum | Silinir mi? |
|------|-------|-------------|
| Session token | Cookie | ✅ EVET |
| CSRF token | Cookie | ✅ EVET |
| User info | Cookie | ✅ EVET |
| Theme gender | Cookie | ❌ HAYIR (korunur) |
| Welcome shown | Cookie | ❌ HAYIR (korunur) |
| Auth data | LocalStorage | ✅ EVET |
| Cache | SessionStorage | ✅ EVET |
| WebSocket | Bağlantı | ✅ EVET (kapatılır) |

### 5.2 — Backend Temizliği

```
POST /auth/logout
  → Session tablosundan kayıt sil
  → Token tablosundan tüm refresh token'ları sil
  → Rate limit sayacını sıfırla
  → Audit log'a logout kaydı ekle
```

---

## 6. GÜVENLİK NOTLARI

| Kural | Değer |
|-------|-------|
| Session silme | Backend'de de silinmeli (sadece cookie yeterli değil) |
| CSRF token | Logout request'inde de zorunlu |
| Audit log | Logout işlemi loglanmalı |
| Concurrent session | Tüm session'lar mı iptal edilecek? (opsiyonel) |
| Token revocation | Refresh token'lar da iptal edilmeli |

---

## 7. HATA DURUMLARI

| Hata | Davranış |
|------|----------|
| Backend hatası | "Çıkış yapılamadı. Lütfen tekrar deneyin." |
| Network hatası | Cookie'ler silinir, login sayfasına yönlendirme |
| Session zaten dolmuş | Doğrudan login sayfasına yönlendirme |

---

## 8. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (logout) | ✅ 44×44px |
| Touch target (modal buton) | ✅ 48px |
| Focus trap (modal) | ✅ |
| Escape ile kapatma | ✅ |
| ARIA | ✅ `role="dialog"`, `aria-modal="true"` |
| Screen reader | ✅ "Çıkış yap" mesajı |

---

## 9. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/00-ascii-art-views]] §1 | Header ASCII art |
| [[01-component-inventory]] C03 | User Pill |
| [[ADR-011-session-management]] | Session yönetimi |
| [[flow/auth/01-login]] | Login akışı (sonraki adım) |

---

*Logout Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
