---
title: "CoreMusic — Technical Design Specification (Kiro Design Spec)"
type: spec-design
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .workflows/design.md · companion: PRD.md · TODO.md · workflow.md"
updated: 2026-11-01
---

# CoreMusic — Technical Design Specification (Kiro Design Spec)

> **Amaç:** Kiro IDE / Agentic Spec standardında, CoreMusic ekosisteminin teknik mimarisini, bileşen hiyerarşisini, veri sözleşmelerini ve sistem arayüzlerini tanımlayan merkezi teknik tasarım belgesi (`design.md`).

**Zorunlu Bağlantılar:** [[../PRD.md]] · [[../TODO.md]] · [[workflow.md]] · [[tasks.md]] · [[../RAG.md]] · [[../CONTEXT.md]] · [[../.ai/durum.md]]

---

## 1. Sistem Mimarisi & Katman Ayrımı (Layered Architecture)

CoreMusic, hibrit bir mimari (Backend PHP 8.4 SPA + Frontend Vanilla JS + C++20 Neva DSP) üzerine oturur:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                              │
│  - home.coremusic.net (PHP Shell + Vanilla JS SPA Router)               │
│  - assets.coremusic.net (ITCSS 11 Katman, CSS Tokens, SVG Icons)       │
│  - Multi-Viewport Adapters: Phone (360) | Embedded (1024) | Wide (1920)│
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ HTTP / JSON
┌───────────────────────────────────▼────────────────────────────────────┐
│                        APPLICATION & API LAYER                         │
│  - api.coremusic.net (V1 & V2 Paralel Routes - ADR-113)                │
│  - auth.coremusic.net (JWT, Session, CSRF, RBAC)                       │
│  - admin.coremusic.net (DevLoginGate, Audit, User Management)          │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Constructor Injection (I*)
┌───────────────────────────────────▼────────────────────────────────────┐
│                          DOMAIN / DATA LAYER                           │
│  - shared/src/Database/ConnectionFactory (Raw PDO, Parameterized)       │
│  - Repository Pattern: I* Interface + Final Class (ADR-114)             │
│  - 18 BCNF MySQL Schemas (.ai/.sql/mysql/*.sql - ADR-112)               │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ C ABI / Shared Memory
┌───────────────────────────────────▼────────────────────────────────────┐
│                    DSP & AUDIO ENGINE LAYER (HARDWARE)                 │
│  - app-cpp-projects/ (C++20 Neva Engine, JUCE 9, ASIO, WASAPI)         │
│  - Discrete Class AB Amplifier Interface (ADR-038, SNR >105dB)         │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Arayüz & Görünüm Katmanı Tasarımı (Multi-Viewport Specs)

Tüm arayüzler istisnasız **LIGHT MODE** standardındadır (`HL-01`). Dark mode tasarımı yoktur.

### 2.1 Viewport Eşikleri & Figma Hizalaması
| Viewport | Çözünürlük | Figma Sahnesi | Navigasyon & Düzen |
|---|---|---|---|
| **Tier 1: Phone** | 360px – 480px | `T01-phone` | Alt bar navigasyon, tek sütun kart akışı |
| **Tier 2: Embedded** | 1024×600px | `T07-embedded` (`2135:24715`) | Sabit sol dock (72px), 2×2 + 1×5 + 1×5 grid |
| **Tier 3: Desktop Wide** | 1920×1080px | `T10-desktop` (`1760:14599`) | Genişletilmiş sidebar, 4×4 + 1×8 + 1×8 grid |
| **Tier 4: Monitor 4K** | 3840×2160px | `T20-monitor-4k` | Yüksek yoğunluklu kart matrisi, çoklu panel |

### 2.2 ITCSS 11 Katman Mimarisi
1. `01_Abstracts`: CSS değişkenleri (`--cm-*`, `--color-*`, `--radius-*`) — **Tek sabit değer kaynağı**.
2. `02_Base`: Tarayıcı sıfırlama, tipografi, kutu modeli.
3. `03_Layout`: Grid konteynerleri, sayfa iskeletleri.
4. `04_Components`: Tekil bileşenler (`c-terminal.css`, `_wide-sidebar.css`, `c-quick-network.css`).
5. `05_Pages`: Sayfa özel stilleri (`p-browse.css`, `p-album-detail.css`, `p-kesfet.css`, `p-wizard.css`).
6. `06_Utilities`: Yardımcı sınıflar (`.u-hidden`, `.u-flex-center`).
7. `07_Vendors`: Üçüncü parti kütüphane stilleri.
8. `08_Devices`: Cihaz özel medya sorguları.
9. `09_ViewModes`: Kompakt ve geniş görünüm modları.
10. `10_Helpers`: Erişilebilirlik ve yardımcı araçlar.
11. `11_OAuth`: Sosyal giriş ve harici auth stilleri.

---

## 3. Veri Modelleri & Sözleşmeler (Data Contracts)

### 3.1 Repository Standart Şablonu (ADR-114)
```php
namespace CoreMusic\Repository;

interface IMusicRepository {
    public function findById(int $id): ?MusicTrack;
    public function searchTracks(string $query, int $limit, int $offset): array;
}

final class MusicRepository implements IMusicRepository {
    public function __construct(private \PDO $pdo) {}
    
    public function findById(int $id): ?MusicTrack {
        $stmt = $this->pdo->prepare("SELECT id, title, artist, duration FROM tracks WHERE id = :id AND is_deleted = 0");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? MusicTrack::fromRow($row) : null;
    }
}
```

### 3.2 Canlı Şema Hizalama & Migration İlkeleri (ADR-112)
- Canlı DB şeması `.ai/.sql/mysql/*.sql` dump dosyalarına daima **migration ile** hizalanır.
- Şema drift bulunduğunda kod esnetilmez, fail-closed davranış korunur.
- Her tabloda soft-delete (`is_deleted = 0`) standardı zorunludur.

---

## 4. Ses Motoru & Donanım Tasarımı (C++20 Neva Engine)

- **Amfi Topolojisi:** Kesinlikle **Discrete Class AB** (Class D yasaktır — `HL-02`).
- **Hedef Donanım Metrikleri:**
  - SNR > 105 dB
  - THD+N < 0.003% @ 1kHz
  - Örnekleme: 32-bit Float dahili boru hattı, 96 kHz / 192 kHz destekli
  - DAC Entegrasyonu: AK4458 / PCM3168A
  - Ses Sürücüsü: ASIO 2.3.4 (Windows) / ALSA-WASAPI Lock-free ring buffer

---

## 5. Doğrulama & Kabul Kriterleri (Verification Matrix)

| Katman | Test Aracı | Kabul Eşiği | Doğrulama Komutu |
|---|---|---|---|
| **E2E UI Testleri** | Playwright | 38/38 (%100 Başarı) | `npx playwright test tests/e2e/pages.spec.ts` |
| **Component Testleri**| Vitest | 199/199 (%100 Başarı) | `npm run test` (assets) |
| **Backend Birim** | PHPUnit | 757/757 (%100 Başarı) | `vendor/bin/phpunit` |
| **Stil Uyumu** | Token Purity | 0 Raw Hex | CSS taraması (`04_Components`, `05_Pages`) |
| **Tema Doğrulaması** | Light Mode Check | 0 Dark Rule | `@media (prefers-color-scheme: dark)` yokluğu |

---

*CoreMusic Technical Design Spec v1.0.0 — Kiro Standard — 2026-11-01*
