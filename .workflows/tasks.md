---
title: "CoreMusic — Implementation Tasks Specification (Kiro Tasks Spec)"
type: spec-tasks
category: task-tracking
version: 1.0.0
status: active
authority: "SSOT: .workflows/tasks.md · companion: TODO.md · PRD.md · design.md"
updated: 2026-11-01
---

# CoreMusic — Implementation Tasks Specification (Kiro Tasks Spec)

> **Amaç:** Kiro IDE / Agentic Spec standardında, platformun ve aktif özelliklerin aşamalandırılmış (phased), gruplandırılmış ve bağımlılık zincirine bağlanmış uygulama görev listesi (`tasks.md`).

**Zorunlu Bağlantılar:** [[../TODO.md]] · [[design.md]] · [[workflow.md]] · [[../PRD.md]] · [[../RAG.md]] · [[../.ai/durum.md]]

---

## 📋 FAZ BAZLI GÖREV MATRİSİ (PHASED EXECUTION PLAN)

### 🟢 FAZ 1: Çekirdek Arayüz, Sayfalar & Figma Hizalaması (TAMAMLANDI)

- [x] **TASK-00: Andrej Karpathy Yönergeleri & Cursor IDE Entegrasyonu**
  - **Hedef:** `.claude/skills/karpathy-guidelines/`, `.cursor/rules/karpathy-guidelines.mdc`, `CURSOR.md`
  - **Kapsam:** Karpathy'nin 4 temel kuralının (Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution) projeye ve tüm root/vault MD dosyalarına entegrasyonu.
  - **Doğrulama:** 14 SKILL.md disk ölçümü, Cursor `alwaysApply: true`, referans kod örnekleri (`references/EXAMPLES.md`).

- [x] **TASK-01: Terminal & Telemetri Sayfası Entegrasyonu**
  - **Hedef:** `home.coremusic.net/pages/terminal.php`, `assets.coremusic.net/Css/04_Components/c-terminal.css`
  - **Kapsam:** Figma `2135:24715` (1024 Embedded) ve `1760:14599` (Desktop Wide) telemetri panelleri.
  - **Doğrulama:** CPU/GPU/RAM/AK4458 DAC ve Class AB telemetri LED göstergeleri aktif. E2E test passed.

- [x] **TASK-02: Göz At (Browse) & Drill-Down Dosya Yöneticisi**
  - **Hedef:** `home.coremusic.net/pages/browse.php`, `assets.coremusic.net/Css/05_Pages/p-browse.css`
  - **Kapsam:** Figma `1976:11757` Drill-down dosya gezgini, `#btn-browse-open` tetikleyicisi.
  - **Doğrulama:** 1024 ve 1920 çözünürlüklerde açılır ağaç yapısı ve şarkı seçimi çalışıyor.

- [x] **TASK-03: Albüm Detay & Şarkı Tablosu Sayfası**
  - **Hedef:** `home.coremusic.net/pages/album-detail.php`, `assets.coremusic.net/Css/05_Pages/p-album-detail.css`
  - **Kapsam:** Sol cam özet kartı (743px), sağ detay paneli (224px), şarkı listesi.
  - **Doğrulama:** 0 raw hex (%100 token purity), 1024/1920/3840 tam uyum.

- [x] **TASK-04: Keşfet (Spotlight & Ruh Hali) Sayfası**
  - **Hedef:** `home.coremusic.net/pages/kesfet.php`, `assets.coremusic.net/Css/05_Pages/p-kesfet.css`
  - **Kapsam:** Hero Spotlight, 6 Ruh Hali Kartı, Top 10 Şarkı Tablosu.
  - **Doğrulama:** Token purity tam, grid responsive esneme stabil.

- [x] **TASK-05: İlk Kurulum Sihirbazı (Wizard)**
  - **Hedef:** `home.coremusic.net/pages/wizard.php`, `assets.coremusic.net/Css/05_Pages/p-wizard.css`
  - **Kapsam:** 6 adımlı kurulum akışı (Dil, Ağ, Depolama, Ses Cihazı, Profil, Özet).
  - **Doğrulama:** Adım geçişleri, erişilebilirlik, Light Mode uyumu tam.

- [x] **TASK-06: Hızlı Ağ & Ayarlar Modal Bileşenleri**
  - **Hedef:** `home.coremusic.net/pages/components/quick-settings-modal.php`, `quick-network-panels.php`
  - **Kapsam:** Header Wi-Fi, Bluetooth ve Genel Hızlı Ayarlar panelleri.
  - **Doğrulama:** Z-index katman hiyerarşisi, backdrop kapatma, klavye ESC kontrolü.

---

### 🟡 FAZ 2: RAG Sistemi, Token Optimizasyonu & Modüler Hafıza (TAMAMLANDI)

- [x] **TASK-07: 12 Alt Modül İçin Tek Sayfalık DURUM.md Hafızası**
  - **Hedef:** `home`, `api`, `auth`, `admin`, `assets`, `media`, `shared`, `app-cpp-projects`, `app-nodejs-projects`, `bin`, `.agents`, `.workflows`.
  - **Kapsam:** PDF limit sistemi standartlarında modüler durum devri dosyaları.
  - **Doğrulama:** Her dizinde `durum.md` mevcut ve güncel.

- [x] **TASK-08: Kurumsal Mühendislik Direktifleri (KURAL-01..10)**
  - **Hedef:** `.ai/.rules/kurallar.md`, `.ai/.rules/hata-listesi.md`, `.ai/.rules/karar-kurallari.md`
  - **Kapsam:** Gayriresmi video-editör etiketlerinin kurumsal direktif kodlarına dönüştürülmesi.
  - **Doğrulama:** Kural isimleri standartlaştırıldı, 12 hata önleme kodu kataloglandı.

- [x] **TASK-09: 12 Alt Modül İçin CONTEXT.md ve RAG.md Dağıtımı**
  - **Hedef:** 12 proje klasörünün tamamı.
  - **Kapsam:** Her klasöre bağımsız `CONTEXT.md` ve `RAG.md` router yerleştirilmesi.
  - **Doğrulama:** 12 dizinin tamamında `CONTEXT.md` ve `RAG.md` disk üzerinde doğrulandı.

- [x] **TASK-10: Merkezi Kullanıcı RAG Rehberi (DOGE DOC)**
  - **Hedef:** `RAG-KILAVUZU.md` (kök)
  - **Kapsam:** Kullanıcı ve AI için token tasarruflu RAG sorgulama, prompt şablonları ve 4 kırmızı çizgi.
  - **Doğrulama:** Kılavuz kök dizinde üretildi, RAG ve CONTEXT dosyalarına bağlandı.

---

### 🟢 FAZ 3: Backend Sözleşmeleri, API & Şema Hizalama (TAMAMLANDI)

- [x] **TASK-11: Canlı Şema Hizalama Migration Paketi (ADR-112)**
  - **Hedef:** `shared/database/migrations/`, `media.coremusic.net/`
  - **Kapsam:** `media_access` ve `media_audit` soft-delete (`is_deleted`, `deleted_at`, `updated_at`) kolonlarının canlı DB'ye migration ile eklenmesi.
  - **Doğrulama:** Yedek dump alındı (`shared/database/backups/20261102_media_pre_migration.sql`), forward migration canlı DB'ye uygulandı, `SHOW COLUMNS` ve `media.coremusic.net` testleri (75/75) doğrulandı; ADR-112 accepted yapıldı.


- [x] **TASK-12: API V1 & V2 Paralel Route Test Paritesi (ADR-113)**
  - **Hedef:** `api.coremusic.net/config/routes.php`, `api.coremusic.net/include/Container/V2Container.php`
  - **Kapsam:** `/api/{version}/` rotalarında V1 default ve V2 paralel çalışma paritesinin test edilmesi.
  - **Kabul Kriteri:** `RouteConfigTest` parite testleri %100 yeşil (141 test / 696 assertion).

- [x] **TASK-13: Repository Standart Şablon İzolasyonu (ADR-114)**
  - **Hedef:** `api.coremusic.net/include/Repository/`, `shared/src/Repository/`
  - **Kapsam:** Yeni yazılacak repository'lerin `I*` interface + `final class` standardına bağlanması; mevcut repo'ların dondurulması.
  - **Kabul Kriteri:** Yeni repository'lerde 0 ORM, 0 `SELECT *`, zorunlu constructor injection (`.ai/.rules/karar-kurallari.md` §3).

---

### 🟣 FAZ 4: DSP Ses Motoru & Donanım Amfi Entegrasyonu (TAMAMLANDI)

- [x] **TASK-14: Discrete Class AB Amfi Telemetri Köprüsü (ADR-038 / ADR-089)**
  - **Hedef:** `app-cpp-projects/`, `home.coremusic.net/pages/terminal.php`
  - **Kapsam:** Neva Engine C ABI üzerinden donanım amfi sıcaklık, akım ve koruma telemetrisinin web paneline aktarılması. Terminal komutları (`amfi`, `telemetri`, `dsp`) ve 1920/4K yan panel telemetrisi.
  - **Kabul Kriteri:** SNR > 105 dB, THD+N < 0.003%, lock-free ring buffer akışı, Discrete Class AB push-pull transistör (MJL21194/93) ve AK4458 DAC durum denetimi. Unit testleri %100 yeşil.

- [x] **TASK-15: 31-Bant Parametrik/Grafik Hibrit EQ Entegrasyonu**
  - **Hedef:** `app-cpp-projects/src/DSP/`, `home.coremusic.net/pages/settings.php` (`ayarlar.php`), `assets.coremusic.net/Css/05_Pages/p-settings.css`
  - **Kapsam:** ISO 266 1/3-oktav 31-bant biquad peaking filtre rafı, 10-bant ISO standart alt küme maskesi (`eq_grids.h`), 9 tür ön tanım (preset), pre-amp kazancı, bypass anahtarı ve localStorage kalıcı durum saklama.
  - **Kabul Kriteri:** %100 token saflığı (0 raw hex), 1024/1440/1920 responsive esneme, canlı filtreleme parametreleri. Unit testleri %100 yeşil (`testSettingsPage_rendersLayoutAndSections`, `testSettingsPage_rendersWideLayout`).

---

## 🛠️ TEST & DOĞRULAMA KOMUTLARI

```bash
# E2E UI Testleri (Playwright)
npx playwright test tests/e2e/pages.spec.ts

# Component Testleri (Vitest)
npm run test

# PHPUnit Testleri (Tüm Alt Projeler)
vendor/bin/phpunit
```

---

*CoreMusic Tasks Specification v1.0.0 — Kiro Standard — 2026-11-01*
