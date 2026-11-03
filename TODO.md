---
title: "CoreMusic — Master TODO & Task Execution Plan (Kiro Tasks Spec)"
type: todo
category: project-tracking
version: 2.0.0
status: active
authority: "SSOT: TODO.md · companion: .workflows/tasks.md · .workflows/design.md · .workflows/workflow.md"
updated: 2026-11-01
---

# CoreMusic — Master TODO & Task Execution Plan (Kiro Tasks Spec)

> **Kiro IDE / Agentic Spec Standardı:**  
> Bu dosya, yapay zekanın (AI) ve geliştiricinin projede neyi, hangi sırayla ve hangi kabul kriterleriyle yapacağını belirleyen aşamalandırılmış (phased), gruplandırılmış ve bağımlılık zincirine bağlanmış ana görev defteridir.

**🔗 Kiro Spec Ailesi & Zorunlu Bağlantılar:**
- **İş Akışı Kılavuzu:** [`.workflows/workflow.md`](file:///c:/www/coremusic.net/.workflows/workflow.md)
- **Teknik Tasarım Belgesi:** [`.workflows/design.md`](file:///c:/www/coremusic.net/.workflows/design.md)
- **Ayrıntılı Görev Şartnamesi:** [`.workflows/tasks.md`](file:///c:/www/coremusic.net/.workflows/tasks.md)
- **Ürün Gereksinimleri:** [`PRD.md`](file:///c:/www/coremusic.net/PRD.md)
- **RAG & Akıllı Arama:** [`RAG.md`](file:///c:/www/coremusic.net/RAG.md) · [`RAG-KILAVUZU.md`](file:///c:/www/coremusic.net/RAG-KILAVUZU.md)
- **Depo Envanteri:** [`CONTEXT.md`](file:///c:/www/coremusic.net/CONTEXT.md)
- **Tek Sayfalık Hafıza & Durum:** [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md)
- **Temel Direktifler:** [`.ai/.rules/kurallar.md`](file:///c:/www/coremusic.net/.ai/.rules/kurallar.md) (`KURAL-01..10`)
- **Hata Önleme Kataloğu:** [`.ai/.rules/hata-listesi.md`](file:///c:/www/coremusic.net/.ai/.rules/hata-listesi.md) (`HL-01..12`)

---

## 🧭 İŞ AKIŞI PRENSİBİ (KIRO TRIAD)

```text
[PRD.md (Gereksinim)] ──► [.workflows/design.md (Tasarım)] ──► [TODO.md (Görevler)]
                                                                       │
[durum.md (Durum Devri)] ◄── [Test & Doğrulama (994/994)] ◄────────────┘
```

1. **Önce Plan, Sonra Kod:** Analiz ve görev maddesi olmadan kod yazılmaz.
2. **Kanıt Zorunluluğu:** Tamamlanan her madde disk ve test kanıtıyla `[x]` yapılır.
3. **Tek Sayfa İlkesi:** Oturum başında yalnızca ilgili modülün `durum.md` dosyası okunur.
4. **Kırmızı Çizgiler:** Yalnızca Light Mode (`HL-01`), Discrete Class AB Amfi (`HL-02`), Subagent commit yasağı (`HL-03`).

---

## 📋 AŞAMALANDIRILMIŞ GÖREV PLANI (PHASED TASKS)

### 🟢 FAZ 1: Çekirdek Arayüz, Sayfalar & Figma Hizalaması (TAMAMLANDI)

- [x] **TASK-00: Andrej Karpathy Yönergeleri & Cursor IDE Entegrasyonu (TAMAMLANDI)**
  - **Hedef:** `.claude/skills/karpathy-guidelines/`, `.cursor/rules/karpathy-guidelines.mdc`, `CURSOR.md`
  - **Kapsam:** Karpathy'nin 4 temel kuralının (Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution) projeye ve tüm root/vault MD dosyalarına entegrasyonu.
  - **Doğrulama:** 14 SKILL.md disk ölçümü, Cursor `alwaysApply: true`, referans kod örnekleri (`references/EXAMPLES.md`).
*Hedef: Tüm sayfaların Figma SSOT'ları ile 1024 / 1920 / 3840 çözünürlüklerde uyumu ve 0 raw hex token purity.*

- [x] **TASK-01: Terminal & Telemetri Sayfası Entegrasyonu**
  - **Hedef:** `home.coremusic.net/pages/terminal.php`, `assets.coremusic.net/Css/04_Components/c-terminal.css`
  - **Kapsam:** Figma `2135:24715` (1024) ve `1760:14599` (1920) telemetri panelleri. CPU, GPU, RAM, AK4458 DAC ve Class AB telemetri LED'leri.
  - **Doğrulama:** E2E ve birim testler %100 yeşil.

- [x] **TASK-02: Göz At (Browse) & Drill-Down Dosya Gezgini**
  - **Hedef:** `home.coremusic.net/pages/browse.php`, `assets.coremusic.net/Css/05_Pages/p-browse.css`
  - **Kapsam:** Figma `1976:11757` Drill-down dosya gezgini, `#btn-browse-open` tetikleyicisi.
  - **Doğrulama:** 1024 ve 1920 çözünürlüklerde sorunsuz navigasyon.

- [x] **TASK-03: Albüm Detay Sayfası & Şarkı Tablosu**
  - **Hedef:** `home.coremusic.net/pages/album-detail.php`, `assets.coremusic.net/Css/05_Pages/p-album-detail.css`
  - **Kapsam:** Sol cam özet kartı (743px), sağ detay paneli (224px), 0 raw hex (%100 token purity).
  - **Doğrulama:** Playwright E2E passed.

- [x] **TASK-04: Keşfet (Spotlight & Ruh Hali) Sayfası**
  - **Hedef:** `home.coremusic.net/pages/kesfet.php`, `assets.coremusic.net/Css/05_Pages/p-kesfet.css`
  - **Kapsam:** Hero Spotlight, 6 Ruh Hali Kartı, Top 10 Şarkı Tablosu.
  - **Doğrulama:** Light Mode ve responsive grid stabil.

- [x] **TASK-05: İlk Kurulum Sihirbazı (Wizard)**
  - **Hedef:** `home.coremusic.net/pages/wizard.php`, `assets.coremusic.net/Css/05_Pages/p-wizard.css`
  - **Kapsam:** 6 adımlı erişilebilir kurulum akışı (Dil, Ağ, Disk, Ses Cihazı, Profil, Özet).
  - **Doğrulama:** Form doğrulama ve adım geçişleri tamamlandı.

- [x] **TASK-06: Hızlı Ağ & Ayarlar Modal Bileşenleri**
  - **Hedef:** `home.coremusic.net/pages/components/quick-settings-modal.php`, `quick-network-panels.php`
  - **Kapsam:** Header Wi-Fi, Bluetooth ve Genel Hızlı Ayarlar panelleri.
  - **Doğrulama:** ESC ile kapatma, backdrop focus trapping, z-index hiyerarşisi stabil.

---

### 🟢 FAZ 2: RAG Sistemi, Token Optimizasyonu & Modüler Hafıza (TAMAMLANDI)
*Hedef: Token tükenmesini engelleyen modüler durum devri ve her klasöre otonom RAG ve bağlam haritası.*

- [x] **TASK-07: 12 Alt Modül İçin Tek Sayfalık DURUM.md Hafızası**
  - **Hedef:** `home`, `api`, `auth`, `admin`, `assets`, `media`, `shared`, `app-cpp-projects`, `app-nodejs-projects`, `bin`, `.agents`, `.workflows`.
  - **Kapsam:** PDF limit sistemi standartlarında tek sayfalık durum devir dosyaları mühürlendi.
  - **Doğrulama:** 12 dizinde `durum.md` dosyaları mevcut.

- [x] **TASK-08: Kurumsal Mühendislik Direktifleri (KURAL-01..10)**
  - **Hedef:** `.ai/.rules/kurallar.md`, `.ai/.rules/hata-listesi.md`, `.ai/.rules/karar-kurallari.md`
  - **Kapsam:** Gayriresmi video-editör etiketleri kaldırıldı; kurumsal direktif kodları (`KURAL-01..10`, `HL-01..12`) devreye alındı.
  - **Doğrulama:** Aktif dinleme ve denetim modunda.

- [x] **TASK-09: 12 Alt Modülün Tamamına CONTEXT.md ve RAG.md Dağıtımı**
  - **Hedef:** 12 proje klasörünün tamamı.
  - **Kapsam:** Her klasöre bağımsız `CONTEXT.md` (alan envanteri/bağlamı) ve `RAG.md` (hızlı rota sözlüğü) yerleştirildi.
  - **Doğrulama:** Disk üzerinde 12 `CONTEXT.md` ve 12 `RAG.md` doğrulandı.

- [x] **TASK-10: Merkezi Kullanıcı RAG Kılavuzu (DOGE DOC)**
  - **Hedef:** [`RAG-KILAVUZU.md`](file:///c:/www/coremusic.net/RAG-KILAVUZU.md)
  - **Kapsam:** Kullanıcı ve AI için token tasarruflu RAG sorgulama, hazır prompt kalıpları ve 4 kırmızı çizgi.
  - **Doğrulama:** Kök dizinde oluşturuldu ve belgelere bağlandı.

- [x] **TASK-10b: Kiro IDE Spec Standardı Entegrasyonu (.workflows)**
  - **Hedef:** `.workflows/design.md`, `.workflows/tasks.md`, `.workflows/workflow.md`
  - **Kapsam:** Kiro spec standardında teknik tasarım, görev matrisi ve yürütme iş akışı üretildi.
  - **Doğrulama:** Belgeler oluşturuldu ve `TODO.md`, `PRD.md`, `RAG.md` ile bağlandı.

---

### 🟢 FAZ 3: Backend Sözleşmeleri, API & Şema Hizalama (TAMAMLANDI)
*Hedef: ADR-112, 113, 114 kararlarının backend kod tabanında tam kurumsallaştırılması.*

- [x] **TASK-11: Canlı Şema Hizalama Migration Paketi (ADR-112)**
  - **Hedef:** shared/database/migrations/, media.coremusic.net/
  - **Kapsam:** media_access ve media_audit soft-delete (is_deleted, deleted_at, updated_at) kolonlarının canlı DB'ye migration ile eklenmesi.
  - **Doğrulama:** Yedek dump alındı (shared/database/backups/20261102_media_pre_migration.sql), forward migration canlı DB'ye uygulandı, SHOW COLUMNS ve media.coremusic.net testleri (75/75) doğrulandı; ADR-112 accepted yapıldı.

- [x] **TASK-12: API V1 & V2 Paralel Route Test Paritesi (ADR-113)**
  - **Hedef:** api.coremusic.net/config/routes.php, api.coremusic.net/include/Container/V2Container.php
  - **Kapsam:** /api/{version}/ rotalarında V1 default ve V2 paralel çalışma paritesinin test edilmesi.
  - **Doğrulama:** RouteConfigTest ve VersionResolverTest parite testleri %100 yeşil (141 test / 696 assertion).

- [x] **TASK-13: Repository Standart Şablon İzolasyonu (ADR-114)**
  - **Hedef:** api.coremusic.net/include/Repository/, shared/src/Repository/
  - **Kapsam:** Yeni yazılacak repository'lerin I* interface + final class standardına bağlanması; mevcut repo'ların dondurulması.
  - **Doğrulama:** Yeni repository'lerde 0 ORM, 0 SELECT *, zorunlu constructor injection (.ai/.rules/karar-kurallari.md §3).

---

### 🟢 FAZ 4: DSP Ses Motoru & Donanım Amfi Entegrasyonu (TAMAMLANDI)
*Hedef: Discrete Class AB amplifikatör ve Neva ses motorunun donanım/DSP katmanının tamamlanması.*

- [x] **TASK-14: Discrete Class AB Amfi Telemetri Köprüsü (ADR-038 / ADR-089) (TAMAMLANDI)**
  - **Hedef:** app-cpp-projects/, home.coremusic.net/pages/terminal.php
  - **Kapsam:** Neva Engine C ABI üzerinden donanım amfi sıcaklık, akım ve koruma telemetrisinin web paneline aktarılması.
  - **Doğrulama:** SNR > 105 dB, THD+N < 0.003%, lock-free ring buffer akışı, Discrete Class AB push-pull transistör (MJL21194/93) ve AK4458 DAC durum denetimi. Unit testleri %100 yeşil.

- [x] **TASK-15: 31-Bant Parametrik/Grafik Hibrit EQ Entegrasyonu (TAMAMLANDI)**
  - **Hedef:** app-cpp-projects/src/DSP/, home.coremusic.net/pages/settings.php (ayarlar.php), assets.coremusic.net/Css/05_Pages/p-settings.css
  - **Kapsam:** ISO 266 1/3-oktav 31-bant biquad peaking filtre rafı, 10-bant ISO standart alt küme maskesi (eq_grids.h), 9 tür ön tanım (preset), pre-amp kazancı, bypass anahtarı ve localStorage kalıcı durum saklama.
  - **Doğrulama:** %100 token saflığı (0 raw hex), 1024/1440/1920 responsive esneme, canlı filtreleme parametreleri. Unit testleri %100 yeşil (testSettingsPage_rendersLayoutAndSections, testSettingsPage_rendersWideLayout).

---

## 🛠️ TEST VE DOĞRULAMA ÇALIŞTIRMA KILAVUZU

```bash
# 1. Playwright E2E UI Testleri (38/38)
npx playwright test tests/e2e/pages.spec.ts

# 2. Vitest Bileşen Testleri (199/199)
npm run test

# 3. PHPUnit Backend Testleri (757/757)
vendor/bin/phpunit
```

---

*CoreMusic Master TODO Spec v2.0.0 — SSOT: [`TODO.md`](file:///c:/www/coremusic.net/TODO.md) — Last Updated: 2026-11-01*

