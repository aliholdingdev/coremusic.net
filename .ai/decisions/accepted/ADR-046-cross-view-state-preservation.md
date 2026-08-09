---
type: adr
category: ui
title: "ADR-046: Cross-View State Preservation"
date: 2026-08-03
updated: 2026-08-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-046: Cross-View State Preservation

**Status:** Active (güncellenebilir)
**Kategorisi:** UI State Management
**İlgili Agent:** [[.agents/ui-designer]]
**İlgili Division:** Software Division

---

## 1. Amaç

Bu ADR, CoreMusic platformundaki görünüm modları arası (cross-view) durum koruma mekanizmasını, state management stratejisini, persistans katmanını ve 10 panel arası durum senkronizasyonunu tanımlar.

CoreMusic'in cross-view state preservation hedefi:
- Görünümler arası durum koruma: Home ↔ Pro ↔ Studio ↔ Car
- Real-time senkronizasyon: Anlık güncelleme
- Persistent state: Oturumlar arası dayanıklılık
- Performans: Minimal overhead
- Hata dayanıklılığı: State kaybı durumunda fallback

---

## 2. Bağlam

### 2.1 Mevcut Durum

CoreMusic'te 4 görünüm modu bulunmaktadır (ADR-045):
- Home (ev medya merkezi)
- Pro (profesyonel)
- Studio (stüdyo)
- Car (araç içi)

Kullanıcı görünüm modları arasında geçiş yaptığında bazı durumların korunması gerekir:
- Oynatma listesi
- Ses seviyesi
- EQ ayarları
- Seçili şarkı
- Görünüm tercihleri

### 2.2 Gereksinimler

| # | Gereksinim | Değer | Kaynak |
|---|------------|-------|--------|
| R1 | State sync | Görünümler arası senkronizasyon | ADR-046 |
| R2 | Persistent | Oturumlar arası koruma | ADR-046 |
| R3 | Real-time | Gerçek zamanlı güncelleme | ADR-046 |
| R4 | Performans | < 50ms sync | ADR-046 |
| R5 | Hata toleransı | State kaybında fallback | ADR-046 |
| R6 | Minimal storage | localStorage limitli | ADR-046 |
| R7 | Cross-tab | Çoklu sekme desteği | ADR-046 |
| R8 | Security | Hassas veri yok | ADR-046 |

### 2.3 Kısıtlamalar

| # | Kısıt | Açıklama |
|---|-------|----------|
| C1 | localStorage 5MB | Tarayıcı limiti |
| C2 | Session-based auth | localStorage'da auth yasak |
| C3 | Framework yasak | Vanilla JS (ADR-001) |
| C4 | Performans | 50ms altında sync |
| C5 | Cross-browser | Chrome, Firefox, Safari, Edge |

---

## 3. Karar

CoreMusic'te **cross-view state** koruması sağlanacak.

### 3.1 State Kategorileri

| Kategori | Saklama | Ömür | Örnek |
|----------|---------|------|-------|
| **Session State** | Memory | Oturum sonu | Current song, volume |
| **Persistent State** | localStorage | Kalıcı | Theme, view preferences |
| **Server State** | DB | Kalıcı | Playlist, history |
| **Ephemeral State** | Memory | Anlık | Scroll position, focus |

### 3.2 State Manager Mimarisi

```
┌─────────────────────────────────────────────────┐
│ View Mode Değişikliği (ADR-045)                  │
│  └→ "home" → "pro" geçişi                        │
└──────────────────────┬──────────────────────────┘
                       ↓
┌──────────────────────────────────────────────────┐
│ State Manager (CrossViewStateManager.js)          │
│  └→ Mevcut state'i kaydet (pre-transition)        │
│  └→ Yeni view mode'a geç                          │
│  └→ State'i geri yükle (post-transition)          │
└──────────────────────┬───────────────────────────┘
                       ↓
┌──────────────────────────────────────────────────┐
│ Persistans Katmanı                                │
│  └→ localStorage (persistent state)               │
│  └→ sessionStorage (session state)                │
│  └→ DB API (server state)                         │
└──────────────────────────────────────────────────┘
```

### 3.3 Kaydedilen State'ler

| State | Key | Tip | View Mode | Süre |
|-------|-----|-----|-----------|------|
| current_song | cm-current-song | Object | Tümü | Session |
| volume | cm-volume | Number | Tümü | Persistent |
| eq_settings | cm-eq | Object | Tümü | Persistent |
| playlist | cm-playlist | Array | Tümü | Session |
| view_preferences | cm-view-prefs | Object | Tümü | Persistent |
| scroll_position | cm-scroll | Number | Per-view | Session |
| sidebar_state | cm-sidebar | Boolean | Pro, Studio | Session |
| player_mode | cm-player-mode | String | Tümü | Persistent |

---

## 4. Teknik Detaylar

### 4.1 CrossViewStateManager.js

```javascript
const CrossViewStateManager = {
    KEYS: {
        CURRENT_SONG: 'cm-current-song',
        VOLUME: 'cm-volume',
        EQ: 'cm-eq',
        PLAYLIST: 'cm-playlist',
        VIEW_PREFS: 'cm-view-prefs',
        SCROLL: 'cm-scroll',
        SIDEBAR: 'cm-sidebar',
        PLAYER_MODE: 'cm-player-mode'
    },

    save(key, value) {
        try {
            const serialized = JSON.stringify(value);
            localStorage.setItem(key, serialized);
        } catch (e) {
            // localStorage dolu veya erişilemez
            console.warn('State save failed:', e);
        }
    },

    load(key, defaultValue = null) {
        try {
            const serialized = localStorage.getItem(key);
            return serialized ? JSON.parse(serialized) : defaultValue;
        } catch (e) {
            return defaultValue;
        }
    },

    remove(key) {
        localStorage.removeItem(key);
    },

    clearAll() {
        Object.values(this.KEYS).forEach(key => this.remove(key));
    },

    preTransition(fromMode, toMode) {
        // Geçiş öncesi state'i kaydet
        this.save(`cm-state-${fromMode}`, {
            scroll: window.scrollY,
            focus: document.activeElement?.id,
            timestamp: Date.now()
        });
    },

    postTransition(fromMode, toMode) {
        // Geçiş sonrası state'i geri yükle
        const saved = this.load(`cm-state-${toMode}`);
        if (saved) {
            window.scrollTo(0, saved.scroll || 0);
            if (saved.focus) {
                document.getElementById(saved.focus)?.focus();
            }
        }
    },

    syncAcrossTabs() {
        window.addEventListener('storage', (e) => {
            if (e.key?.startsWith('cm-')) {
                // Cross-tab senkronizasyonu
                this.onStateChange(e.key, JSON.parse(e.newValue));
            }
        });
    },

    onStateChange(key, value) {
        // State değişikliği tetikleyicileri
        document.dispatchEvent(new CustomEvent('cm:state-change', {
            detail: { key, value }
        }));
    }
};
```

### 4.2 State Persistans Stratejisi

| State Tipi | Saklama | Otomatik Temizleme | Max Boyut |
|------------|---------|-------------------|-----------|
| Session | sessionStorage | Oturum kapatıldığında | 5MB |
| Persistent | localStorage | Manuel | 5MB |
| Server | DB API | Otomatik (30 gün) | Sınırsız |
| Ephemeral | Memory | Sayfa yenilendiğinde | Sınırsız |

### 4.3 Cross-Tab Senkronizasyonu

```javascript
// storage event ile cross-tab senkronizasyonu
window.addEventListener('storage', (e) => {
    if (e.key === 'cm-current-song') {
        // Diğer sekmede şarkı değişti
        updatePlayerUI(JSON.parse(e.newValue));
    }
    if (e.key === 'cm-volume') {
        // Diğer sekmede ses seviyesi değişti
        updateVolumeSlider(JSON.parse(e.newValue));
    }
});
```

### 4.4 State Conflict Resolution

| Senaryo | Çözüm |
|---------|-------|
| İki sekme aynı state'i değiştiriyor | Son yazan kazanır (last-write-wins) |
| Session süresi doldu | DB'den yeniden yükle |
| localStorage dolu | Eski state'leri temizle |
| Server-state uyuşmazlığı | DB master, local cache fallback |

### 4.5 Performans Metrikleri

| İşlem | Hedef | Ölçüm |
|-------|-------|-------|
| State save | < 10ms | localStorage write |
| State load | < 5ms | localStorage read |
| Cross-tab sync | < 50ms | storage event |
| Pre-transition | < 20ms | Kaydetme |
| Post-transition | < 30ms | Geri yükleme |

---

## 5. Yasak Örüntüler

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | localStorage'da auth token | Cookie-based session | ADR-011 |
| 2 | Hassas veri (API key) | [REDACTED] | ADR-034 |
| 3 | Senkron XHR | Async fetch | ADR-046 |
| 4 | Büyük state objeleri | Minimal state | ADR-046 |
| 5 | Framework kullanımı | Vanilla JS | ADR-001 |
| 6 | var kullanımı | const/let | ADR-001 |
| 7 | State'de password | ASLA | ADR-022 |
| 8 | Console.log production'da | Disabled | ADR-046 |

---

## 6. Edge Cases

| # | Edge Case | Tetikleyici | Çözüm | ADR |
|---|-----------|-------------|-------|-----|
| 1 | localStorage dolu | QuotaExceededError | Eski state'leri temizle | ADR-046 |
| 2 | Private browsing | Safari ITP | sessionStorage fallback | ADR-046 |
| 3 | Cross-tab conflict | İki sekme aynı anda | Last-write-wins | ADR-046 |
| 4 | State corruption | Bozuk JSON | try-catch + default | ADR-046 |
| 5 | View mode değişikliği | Ani geçiş | Pre/post transition | ADR-045 |
| 6 | Network yok | Offline | Local state korunur | ADR-046 |
| 7 | Session timeout | 3600s idle | State DB'de korunur | ADR-011 |
| 8 | Browser restart | Sayfa kapatıldı | Persistent state korunur | ADR-046 |
| 9 | Storage event yok | Eski tarayıcı | Polling fallback | ADR-046 |
| 10 | State boyutu > 5MB | Büyük playlist | Server-side state | ADR-046 |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | Auth token yasak | localStorage'da auth yok | Güvenlik açığı |
| G2 | Hassas veri yasak | API key, password yok | Veri sızıntısı |
| G3 | Minimal state | Gereksiz veri saklanmaz | Performans düşüşü |
| G4 | Cross-tab sync | storage event ile | Tutarsızlık |
| G5 | Error handling | try-catch zorunlu | State kaybı |
| G6 | Fallback | Default state mevcut | Uygulama çökmesi |
| G7 | Performans < 50ms | Sync süresi | Gecikme |
| G8 | Temizleme | Gereksiz state'ler silinir | localStorage dolması |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS | Frontend stack |
| [[ADR-011-session-management]] | Session yönetimi | Session state |
| [[ADR-044-dynamic-user-theme-engine]] | Dynamic theme | Persistent state |
| [[ADR-045-multi-domain-view-mode-architecture]] | Multi-domain view | View mode geçişi |
| [[ADR-048-view-transition-api-integration]] | View Transition API | Geçiş animasyonu |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef Dosya | İlişki |
|-------|-------------|--------|
| § 3.1 | [[ADR-045-multi-domain-view-mode-architecture]] | View mode tanımları |
| § 4.1 | [[architecture/l3-presentation]] | JS kuralları |
| § 4.2 | [[MEMORY.md]] §10 | Cache strategies |
| § 4.4 | [[ecosystem/state-machines]] | State machine'ler |
| § 5 | [[brain.md]] §18 | Coding standards |
| § 6 | [[testing/coverage-targets]] | Test coverage |
| § 7 | [[CLAUDE.md]] §7 | Hard guardrails |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Cross-view State** | Görünümler arası durum |
| **Session State** | Oturum süresince korunan durum |
| **Persistent State** | Kalıcı durum (localStorage) |
| **Server State** | Veritabanında saklanan durum |
| **Ephemeral State** | Anlık durum (memory) |
| **State Manager** | Durum yönetim nesnesi |
| **Pre-transition** | Geçiş öncesi işlem |
| **Post-transition** | Geçiş sonrası işlem |
| **Last-write-wins** | Son yazanın kazandığı strateji |
| **Storage Event** | localStorage değişiklik olayı |
| **JSON Serialization** | Obje → string dönüştürme |
| **QuotaExceededError** | Depolama alanı dolma hatası |
| **Session Storage** | Oturum tabanlı depolama |
| **Local Storage** | Kalıcı depolama |
| **Cross-tab** | Çoklu sekme desteği |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 2.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Sections | 11 |
| State Kategorisi | 4 (Session, Persistent, Server, Ephemeral) |
| State Key Sayısı | 8 |
| Persistans Katmanı | 4 (localStorage, sessionStorage, DB, Memory) |
| Performans hedefi | < 50ms sync |
| Edge Cases | 10 |
| Hard Guardrails | 8 |
| Yasak Örüntü | 8 |
| İlgili ADR | 5 |
| Çapraz Referans | 7 |
| Sözlük Terim | 15 |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Review Status | Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Version | 2.0.0 |
| Immutability | Active (güncellenebilir) |
| Next Review | Yeni state kategorisi eklendiğinde |
| Related Division | Software Division |
| Risk Seviyesi | Orta (UX + veri dayanıklılığı) |

---

## 13. Deployment Considerations

| # | Konu | Detay |
|---|------|-------|
| 1 | localStorage quota | 5MB limiti |
| 2 | Cross-tab sync | storage event |
| 3 | Server-side state | DB backup |
| 4 | Cache invalidation | State değişikliğinde |
| 5 | Monitoring | State boyutu |
| 6 | Fallback stratejisi | Default state |
| 7 | Documentation | API dokümantasyonu |
| 8 | Analytics | State kullanımı |

---

## 14. Testing Strategy

| Test Türü | Kapsam | Framework |
|-----------|--------|-----------|
| Unit Test | State Manager | Vitest |
| Integration Test | Cross-tab sync | Vitest |
| E2E Test | View mode geçişi | Playwright |
| Performance Test | State sync süresi | Performance API |
| Edge Case Test | localStorage dolu | Vitest |
| Persistence Test | State korunması | Playwright |

---

## 15. Risk Assessment

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|----------|------|------------|
| R1 | State kaybı | Düşük | Yüksek | DB backup |
| R2 | localStorage dolu | Düşük | Orta | Temizleme |
| R3 | Cross-tab conflict | Orta | Düşük | Last-write-wins |
| R4 | Performance | Düşük | Düşük | Minimal state |
| R5 | Security leak | Düşük | Yüksek | Hassas veri yok |
| R6 | Browser support | Düşük | Düşük | Fallback |
| R7 | Memory leak | Düşük | Orta | Cleanup |
| R8 | Serialization error | Düşük | Düşük | try-catch |

---

## 16. Maintenance Guidelines

| # | Görev | Siklik | Sorumlu |
|---|-------|--------|---------|
| 1 | State audit | Aylık | UI Designer |
| 2 | Performance check | Üç aylık | QA Engineer |
| 3 | Security audit | Aylık | Security Engineer |
| 4 | localStorage cleanup | Aylık | UI Designer |
| 5 | Cross-tab test | Yeni sürümde | QA Engineer |
| 6 | Documentation | Değişiklikte | UI Designer |

---

## 17. Future Considerations

| # | Konu | Durum | Notlar |
|---|------|-------|--------|
| 1 | IndexedDB | Planlanıyor | Büyük state için |
| 2 | Service Worker | Araştırılıyor | Offline state |
| 3 | Broadcast Channel | Planlanıyor | Cross-tab API |
| 4 | State machine | Gelecek | Formal state management |
| 5 | Undo/redo | Planlanıyor | State history |
| 6 | State sync++ | Araştırılıyor | Real-time sync |

---

## 18. State Schema Reference

| State Key | Type | Default | Max Size | Serialization |
|-----------|------|---------|----------|---------------|
| cm-current-song | Object | null | 1KB | JSON |
| cm-volume | Number | 0.8 | 16B | JSON |
| cm-eq | Object | defaults | 2KB | JSON |
| cm-playlist | Array | [] | 100KB | JSON |
| cm-view-prefs | Object | {} | 512B | JSON |
| cm-scroll | Number | 0 | 16B | JSON |
| cm-sidebar | Boolean | true | 8B | JSON |
| cm-player-mode | String | "full" | 32B | JSON |

---

## 19. Cross-Tab Sync Events

| Event | Trigger | Handler | Aksiyon |
|-------|---------|---------|---------|
| storage:cm-current-song | Şarkı değişikliği | updatePlayerUI | Player güncelle |
| storage:cm-volume | Ses değişikliği | updateVolumeSlider | Slider güncelle |
| storage:cm-eq | EQ değişikliği | updateEQDisplay | EQ panel güncelle |
| storage:cm-playlist | Playlist değişikliği | updatePlaylist | Liste güncelle |
| storage:cm-view-prefs | Tercih değişikliği | updateView | Görünüm güncelle |

---

## 20. State Size Limits

| State Tipi | Max Boyut | İhlal Stratejisi |
|------------|-----------|------------------|
| Session state | 1MB | Eski state'leri temizle |
| Persistent state | 5MB | Compact representation |
| Server state | Sınırsız | DB pagination |
| Cross-tab state | 100KB | Minimal payload |

---

## 21. State Recovery Procedures

| Durum | Kurtarma Yöntemi | Öncelik |
|-------|------------------|---------|
| localStorage dolu | Eski state'leri temizle, server'dan yükle | HIGH |
| Session timeout | DB'den yeniden yükle | MEDIUM |
| Browser crash | Persistent state korunur | LOW |
| Network yok | Local state korunur | LOW |
| State corruption | Default value kullan | HIGH |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
