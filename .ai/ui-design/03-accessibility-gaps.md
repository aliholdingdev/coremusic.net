---
type: reference
category: ui-design
title: "CoreMusic — Accessibility Gaps (WCAG 2.2 AA, v3.1.0)"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 3.2.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/03-accessibility-gaps.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/keys.md"
    - ".ai/MEMORY.md"
    - ".ai/log.md"
    - ".ai/engine.md"
  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
    - "Existing codebase patterns"
  project_structure:
    - "coremusic.net/"
    - "shared/"
    - "api.coremusic.net/"
    - "auth.coremusic.net/"
    - "music.coremusic.net/"
    - "admin.coremusic.net/"
    - "home.coremusic.net/"
    - "car.coremusic.net/"
    - "studio.coremusic.net/"
    - "pro.coremusic.net/"
    - "media.coremusic.net/"
    - "download.coremusic.net/"
  decision_priority:
    - "ADR decisions"
    - "Architecture documentation"
    - "Security requirements"
    - "Existing implementation"
    - "User requirements"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "file rename"
      - "directory move"
      - "architecture change"
      - "database schema change"
      - "security policy change"
  skills:
    - path: ".opencode/skills/ui-code-generator/SKILL.md"
      purpose: "UI/CSS kod üretimi, responsive tasarım, WCAG erişilebilirlik"
    - path: ".opencode/skills/ui-analyzer/SKILL.md"
      purpose: "UI analizi, mevcut tasarım değerlendirme"
    - path: ".opencode/skills/skill-maker/SKILL.md"
      purpose: "Yeni skill oluşturma, skill template sistemi"
    - path: ".opencode/skills/red-team-truth-mode/SKILL.md"
      purpose: "Güvenlik testi, truth mode, adversarial analiz"
    - path: ".opencode/skills/prompt-maker/SKILL.md"
      purpose: "Prompt mühendisliği, AI talimat tasarımı"
    - path: ".opencode/skills/composer-sync/SKILL.md"
      purpose: "Composer dependency yönetimi, vendor senkronizasyonu"
    - path: ".opencode/skills/agent-orchestrator/SKILL.md"
      purpose: "Agent görev dağıtımı, multi-agent koordinasyonu"
    - path: ".opencode/skills/human-mode/SKILL.md"
      purpose: "İnsan modu iletişimi, onay süreçleri"
    - path: ".opencode/skills/hallucination-control/SKILL.md"
      purpose: "Halüsinasyon kontrolü, doğrulama protokolleri"
    - path: ".opencode/skills/database-normalize-maker/SKILL.md"
      purpose: "BCNF normalizasyonu, şema tasarımı"
  templates:
    adr:
      - path: ".ai/.templates/adr/adr-template.md"
        purpose: "ADR şablonu"
      - path: ".ai/.templates/adr/adr-frontend-template.md"
        purpose: "Frontend ADR şablonu"
      - path: ".ai/.templates/adr/adr-database-template.md"
        purpose: "Database ADR şablonu"
      - path: ".ai/.templates/adr/adr-security-template.md"
        purpose: "Security ADR şablonu"
      - path: ".ai/.templates/adr/adr-audio-template.md"
        purpose: "Audio/Hardware ADR şablonu"
      - path: ".ai/.templates/adr/adr-index.md"
        purpose: "ADR navigasyon rehberi"
    backend:
      - path: ".ai/.templates/backend/php-template.md"
        purpose: "PHP 8.4 backend geliştirme şablonu"
      - path: ".ai/.templates/backend/nodejs-template.md"
        purpose: "Node.js 20+ backend geliştirme şablonu"
    frontend:
      - path: ".ai/.templates/frontend/js-template.md"
        purpose: "Vanilla JS ES6+ frontend geliştirme şablonu"
      - path: ".ai/.templates/frontend/css-template.md"
        purpose: "ITCSS 9-layer, BEM CSS şablonu"
    testing:
      - path: ".ai/.templates/testing/phpunit-template.md"
        purpose: "PHPUnit 10+ test şablonu"
      - path: ".ai/.templates/testing/vitest-template.md"
        purpose: "Vitest JS/TS test şablonu"
    infrastructure:
      - path: ".ai/.templates/infrastructure/migration-template.md"
        purpose: "MySQL 9 BCNF migration şablonu"
      - path: ".ai/.templates/infrastructure/docker-template.md"
        purpose: "Docker 24+ Compose v2 şablonu"
      - path: ".ai/.templates/infrastructure/github-actions-template.md"
        purpose: "GitHub Actions CI/CD şablonu"
    documentation:
      - path: ".ai/.templates/documentation/api-doc-template.md"
        purpose: "API dokümantasyon şablonu"
      - path: ".ai/.templates/documentation/security-audit-template.md"
        purpose: "Güvenlik denetimi şablonu"
      - path: ".ai/.templates/documentation/WikiPage-Template.md"
        purpose: "Wiki sayfası şablonu"
    hardware:
      - path: ".ai/.templates/hardware/arduino-template.md"
        purpose: "Arduino/IoT prototipleme şablonu"
      - path: ".ai/.templates/hardware/avr-template.md"
        purpose: "AVR mikrodenetleyici şablonu"
      - path: ".ai/.templates/hardware/pic-template.md"
        purpose: "PIC mikrodenetleyici şablonu"
    query:
      - path: ".ai/.templates/query/Query-Template.md"
        purpose: "SQL sorgu şablonu"
    other:
      - path: ".ai/.templates/other/c-template.md"
        purpose: "C11 GCC embedded/driver şablonu"
      - path: ".ai/.templates/cpp-template.md"
        purpose: "C++20 JUCE/ASIO şablonu"
  ui_cross_references:
    root_files:
      - path: ".ai/ui-design/00-mockup-index.md"
        purpose: "PNG master kataloğu — gap ölçümleri PNG'lerden çıkarıldı"
      - path: ".ai/ui-design/01-component-inventory.md"
        purpose: "C01-C16 bileşen detayları — touch target matrisi"
      - path: ".ai/ui-design/02-implementation-plan.md"
        purpose: "CSS uygulama planı — gap düzeltmeleri planlandığı adımlar"
      - path: ".ai/ui-design/04-vault-registration.md"
        purpose: "Vault kayıt durumu"
    subdirectories:
      - path: ".ai/ui-design/screens/"
        purpose: "Screen spec dosyaları — gap'lerin kaynak ekranları"
      - path: ".ai/ui-design/screens/_components/"
        purpose: "Bileşen spec'leri — WCAG uyumluluk durumları"
      - path: ".ai/ui-design/reference/"
        purpose: "Design token, erişilebilirlik referansları"
    png_directories:
      - path: ".ai/.png/home-1024/"
        purpose: "12 PNG — piksel düzeyinde touch target ölçümleri"
      - path: ".ai/.png/shared-1024/"
        purpose: "6 PNG — auth bileşen WCAG ölçümleri"
  ui_adr_references:
    - path: ".ai/decisions/accepted/ADR-001-vanilla-js-itcss.md"
      purpose: "Vanilla JS — ARIA label, focus-visible, keyboard navigasyon"
    - path: ".ai/decisions/accepted/ADR-044-dynamic-user-theme-engine.md"
      purpose: "Tema motoru — renk kontrastı, WCAG 1.4.3 uyumluluğu"
  ui_skills:
    - path: ".opencode/skills/ui-code-generator/SKILL.md"
      purpose: "UI/CSS kod üretimi — WCAG erişilebilirlik, responsive tasarım"
    - path: ".opencode/skills/ui-analyzer/SKILL.md"
      purpose: "UI analizi — erişilebilirlik denetimi, piksel doğrulama"
---

# CoreMusic — Accessibility Gaps (WCAG 2.2 AA, v3.1.0)

**home-1024 (Linux Embedded RPi5, 1024×600)** için tespit edilen WCAG ihlalleri ve çözüm önerileri.

> **⚠️ RPi5 dokunmatik ekran optimizasyonları dahildir.** Hover yok, fare yok, sadece parmak.

---

## 1. WCAG 2.2 AA Standartları

| Kriter | Minimum | RPi5 İçin | Kaynak |
|--------|---------|-----------|--------|
| Renk kontrastı (normal text) | ≥4.5:1 | ≥4.5:1 | WCAG 1.4.3 |
| Renk kontrastı (large text) | ≥3:1 | ≥3:1 | WCAG 1.4.3 |
| Touch target (mobil) | ≥44×44px | ≥48×48px | WCAG 2.5.8 |
| Focus indicator | Görünür olmalı | Görünür olmalı | WCAG 2.4.7 |
| Screen reader | ARIA labels zorunlu | ARIA labels zorunlu | WCAG 4.1.2 |
| Klavye navigasyonu | Tüm interaktif | — | WCAG 2.1.1 |

---

## 2. Tespit Edilen Gap'ler

### Gap 1: "Başla" Butonu (Hoş Geldin Modalı)

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/_home.css` (welcome modal) |
| **Screen Spec** | `screens/B-home/welcome-popup.md` |
| **ASCII Art** | `screens/00-ascii-art-views.md` §2 |
| **Mevcut boyut** | 105 × 25 px |
| **WCAG minimum** | 44 × 44 px |
| **RPi5 hedef** | 48 × 48 px |
| **Uyum** | ❌ İHLAL |

**Sorun:** Buton yüksekliği 25px, WCAG minimumu olan 44px'in çok altında. RPi5 dokunmatik ekranında kullanılamaz.

**Çözüm Seçenekleri:**

| Seçenek | Değişiklik | Gerekçe | Öneri |
|---------|------------|---------|-------|
| A: Mockup değiştir | Butonu 105×48px yap | Mockup zaten eski | ✅ **ÖNERİLEN** |
| B: Kod değiştir | CSS ile min-height: 48px | Mockup korunur | Alternatif |

**Öneri:** Mockup değiştirilsin. "Başla" butonu en az 105×48px olmalı. Bu, modal layout'unu bozmaz.

---

### Gap 2: Göz At Sidebar Satırları

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/_home.css` (browse layout) |
| **Screen Spec** | `screens/E-filemanager/disk-browser.md` + `file-list.md` |
| **ASCII Art** | `screens/00-ascii-art-views.md` §8-9 |
| **Mevcut yükseklik** | 21 px |
| **Mevcut aralık** | 28 px |
| **WCAG minimum** | 44 × 44 px |
| **RPi5 hedef** | 48 × 48 px |
| **Uyum** | ❌ İHLAL |

**Sorun:** Sidebar satır yüksekliği 21px, aralık 28px. RPi5 dokunmatik ekranında satırları seçmek neredeyse imkansız.

**Çözüm Seçenekleri:**

| Seçenek | Değişiklik | Gerekçe | Öneri |
|---------|------------|---------|-------|
| A: Mockup değiştir | Satır yüksekliğini 48px yap | Mockup eski | Alternatif |
| B: Kod değiştir | CSS ile min-height: 48px, padding ekle | Mockup korunur | ✅ **ÖNERİLEN** |

**Öneri:** Kod değiştirilsin. Sidebar satır yüksekliği CSS ile 48px'e çıkarılabilir.

---

### Gap 3: Footer Utility Icons

| Özellik | Değer |
|---------|-------|
| **Dosya** | `03_Layout/_footer.css` |
| **Screen Spec** | `screens/00-ascii-art-views.md` §1 (Footer) |
| **Mevcut boyut** | 13 × 13 px |
| **WCAG minimum** | 44 × 44 px |
| **RPi5 hedef** | 48 × 48 px |
| **Uyum** | ❌ İHLAL |

**Sorun:** Footer'daki utility ikonları (repeat, shuffle, equalizer, fullscreen) 13×13px. RPi5'de dokunulamaz.

**Çözüm Seçenekleri:**

| Seçenek | Değişiklik | Gerekçe | Öneri |
|---------|------------|---------|-------|
| A: İkon boyutunu büyüt | 22×22px → 44×44px | İkonlar büyüür | Alternatif |
| B: Hit area büyüt | İkon 22px kalsın, hit area 44×44px | Görsel korunur | ✅ **ÖNERİLEN** |

**Öneri:** Hit area büyütülsün. İkon görsel boyutu 22px kalsın ama `padding` ile hit area 44×44px'e çıkarılsın.

---

### Gap 4: Nav Link Touch Target

| Özellik | Değer |
|---------|-------|
| **Dosya** | `03_Layout/_header.css` |
| **Screen Spec** | `screens/00-ascii-art-views.md` §1 (Header) |
| **Mevcut hit area** | ~24 × 24 px |
| **WCAG minimum** | 44 × 44 px |
| **RPi5 hedef** | 48 × 48 px |
| **Uyum** | ❌ İHLAL |

**Sorun:** Nav link'lerin touch hit area'sı 24×24px. 8 link yan yana, aralarında 4px boşluk var.

**Çözüm Seçenekleri:**

| Seçenek | Değişiklik | Gerekçe | Öneri |
|---------|------------|---------|-------|
| A: Padding artır | Link padding'ini 12px→20px yap | Hit area büyür | ✅ **ÖNERİLEN** |
| B: Font küçült, padding artır | Font 10px, padding 16px | Sıkışma olabilir | Alternatif |

**Öneri:** Padding artırılsın. Mevcut 10px font korunur ama padding 20px'e çıkar.

---

### Gap 5: Genre Tab Touch Target

| Özellik | Değer |
|---------|-------|
| **Dosya** | `04_Components/c-genre-tabs.css` |
| **Screen Spec** | `screens/C-music/albums.md` + `artists.md` |
| **Mevcut yükseklik** | ~32 px |
| **WCAG minimum** | 44 × 44 px |
| **RPi5 hedef** | 48 × 48 px |
| **Uyum** | ❌ İHLAL |

**Sorun:** Genre sekmeleri 32px yüksekliğinde. RPi5'de seçmek zor.

**Çözüm:** CSS ile `min-height: 48px` ekle.

---

### Gap 6: Star Rating Touch Target

| Özellik | Değer |
|---------|-------|
| **Dosya** | `04_Components/c-star-rating.css` |
| **Screen Spec** | `screens/C-music/album-detail.md` + `playlist.md` |
| **Mevcut yıldız boyutu** | ~20 × 20 px |
| **WCAG minimum** | 44 × 44 px |
| **RPi5 hedef** | 48 × 48 px |
| **Uyum** | ❌ İHLAL |

**Sorun:** Yıldızlar 20×20px. RPi5'de tek tek yıldız seçmek imkansız.

**Çözüm:** Yıldız boyutunu 48×48px'e çıkar VEYA tam satırı tıklanabilir yap (tüm 5 yıldız tek hit area).

---

### Gap 7: Track Row Height

| Özellik | Değer |
|---------|-------|
| **Dosya** | `04_Components/c-track-list.css` |
| **Screen Spec** | `screens/C-music/album-detail.md` + `playlist.md` |
| **Mevcut yükseklik** | ~40 px |
| **WCAG minimum** | 44 × 44 px |
| **RPi5 hedef** | 48 × 48 px |
| **Uyum** | ⚠️ SINIRDA |

**Sorun:** Track satır yüksekliği 40px, 44px'in altında.

**Çözüm:** CSS ile `min-height: 48px` ekle.

---

### Gap 8: Toggle Switch Size

| Özellik | Değer |
|---------|-------|
| **Dosya** | `04_Components/c-toggle.css` |
| **Screen Spec** | `screens/F-quickpanel/wifi.md` + `bluetooth.md` |
| **Mevcut boyut** | ~50 × 28 px |
| **WCAG minimum** | 44 × 44 px |
| **RPi5 hedef** | 48 × 48 px |
| **Uyum** | ❌ İHLAL |

**Sorun:** Toggle yüksekliği 28px. RPi5'de açma/kapama zor.

**Çözüm:** Toggle boyutunu 56×32px'e çıkar VEYA tam satırı tıklanabilir yap.

---

### Gap 9: Auth Screen Gender Buttons

| Özellik | Değer |
|---------|-------|
| **Bileşen** | C07 Gender Button |
| **Mevcut boyut** | ~284×60px |
| **WCAG minimum** | 44×44px |
| **Durum** | ✅ UYGUN (60px > 48px) |

**Not:** Gender buttons are large enough for touch.

---

### Gap 10: Auth Screen Social Login Buttons

| Özellik | Değer |
|---------|-------|
| **Bileşen** | C08 Social Login |
| **Mevcut boyut** | 52×52px |
| **WCAG minimum** | 44×44px |
| **Durum** | ✅ UYGUN (52px > 48px) |

---

### Gap 11: WiFi/BT Toggle

| Özellik | Değer |
|---------|-------|
| **Bileşen** | C15 Toggle |
| **Mevcut boyut** | ~50×28px |
| **WCAG minimum** | 44×44px |
| **Durum** | ❌ İHLAL |

**Çözüm:** Toggle'ı tam satır tıklanabilir yap (row-level touch target).

---

## 3. Uygun Bileşenler (Sorun Yok)

| Bileşen | Mevcut | Hedef | Durum | Screen Spec |
|---------|--------|-------|-------|-------------|
| C04 Primary Button | 56px | 48px | ✅ UYGUN | — |
| C05 Secondary Button | 48px | 48px | ✅ UYGUN | — |
| C06 Form Input | 56px | 48px | ✅ UYGUN | — |
| C07 Gender Button | 120px | 48px | ✅ UYGUN | gender-select.md |
| C08 Social Button | 52px | 48px | ✅ UYGUN | login.md |
| C09 Media Card | 140px | 48px | ✅ UYGUN | dashboard.md |
| C14 Modal Close | 44px | 48px | ✅ UYGUN | wifi.md |
| C16 Network Row | 48px | 48px | ✅ UYGUN | wifi.md |

---

## 4. Düzeltme Özeti

| # | Bileşen | Mevcut | Hedef | Kim Değiştirecek | Öncelik | Screen Spec |
|---|---------|--------|-------|------------------|---------|-------------|
| 1 | "Başla" butonu | 105×25px | 105×48px | Mockup (tasarım) | HIGH | welcome-popup.md |
| 2 | Göz At sidebar satır | 21px | 48px | Kod (CSS) | HIGH | disk-browser.md |
| 3 | Footer utility icons | 13×13px | 44×44px hit area | Kod (CSS) | HIGH | ASCII §1 |
| 4 | Nav link touch | 24×24px | 48×48px | Kod (CSS + header height) | MEDIUM | ASCII §1 |
| 5 | Genre tabs | 32px | 48px | Kod (CSS) | MEDIUM | albums.md |
| 6 | Star rating | 20×20px | 48×48px | Kod (CSS) | MEDIUM | album-detail.md |
| 7 | Track row | 40px | 48px | Kod (CSS) | LOW | playlist.md |
| 8 | Toggle switch | 28px | 32px+ | Kod (CSS) | LOW | wifi.md |
| 9 | Gender buttons | 60px | 48px | — (UYGUN) | LOW | gender-select.md |
| 10 | Social login | 52px | 48px | — (UYGUN) | LOW | login.md |
| 11 | WiFi/BT toggle row | 28px | 48px | Kod (CSS) | LOW | wifi.md |

---

## 5. Renk Kontrast Kontrolü

| Öğe | Ön Plan | Arka Plan | Oran | WCAG | Durum |
|-----|---------|-----------|------|------|-------|
| Nav link (default) | rgba(255,255,255,0.85) | #0d0a14 | ~12:1 | ✅ | UYGUN |
| Nav link (active) | #E91E8C | #0d0a14 | ~4.6:1 | ✅ | SINIRDA |
| Body text | #FFFFFF | #0d0a14 | ~17:1 | ✅ | UYGUN |
| Secondary text | rgba(255,255,255,0.72) | #0d0a14 | ~12:1 | ✅ | UYGUN |
| Muted text | rgba(255,255,255,0.48) | #0d0a14 | ~8:1 | ✅ | UYGUN |
| Footer text | rgba(255,255,255,0.88) | footer bg | ~14:1 | ✅ | UYGUN |

**Not:** Active nav link (#E91E8C on #0d0a14) 4.6:1 ile WCAG AA sınırdan geçiyor.

---

## 6. Focus-visible Kontrolü

| Öğe | Focus Stili | Görünürlük | Durum |
|-----|-------------|------------|-------|
| Nav links | `2px solid var(--theme-primary)` | ✅ | UYGUN |
| Buttons | `2px solid var(--theme-primary)` | ✅ | UYGUN |
| Form inputs | `border-focus: var(--theme-primary)` | ✅ | UYGUN |
| Modal close | `2px solid var(--theme-primary)` | ✅ | UYGUN |
| Toggle | `2px solid var(--theme-primary)` | ✅ | UYGUN |

---

## 7. ARIA Labels Kontrolü

| Öğe | ARIA Label | Durum |
|-----|------------|-------|
| `<nav>` | `aria-label="Ana navigasyon"` | ✅ |
| Active link | `aria-current="page"` | ✅ |
| WiFi icon | `aria-label="WiFi durumu"` | ✅ |
| BT icon | `aria-label="Bluetooth durumu"` | ✅ |
| Battery | `aria-label="Pil durumu"` | ✅ |
| User pill | `aria-label="Kullanıcı menüsü"` | ✅ |
| Modal | `role="dialog"`, `aria-modal="true"` | ✅ |
| Seek slider | `role="slider"`, `aria-label="Şarkı pozisyonu"` | ✅ |
| Volume slider | `role="slider"`, `aria-label="Ses seviyesi"` | ✅ |
| Gender buttons | `role="radiogroup"` | ⚠️ EKSİK |
| Genre tabs | `role="tablist"` | ⚠️ EKSİK |
| Track rows | `role="button"` | ⚠️ EKSİK |

---

## 8. Klavye Navigasyonu

| Kısayol | Aksiyon | Durum |
|---------|---------|-------|
| `Tab` | Sonraki interaktif element | ✅ |
| `Shift+Tab` | Önceki interaktif element | ✅ |
| `Enter` | Link/button aktivasyonu | ✅ |
| `Escape` | Modal kapat | ✅ |
| `Arrow Left/Right` | Seek slider | ✅ |
| `Arrow Up/Down` | Volume slider | ✅ |

---

## 9. Öncelik Sıralaması

| Öncelik | Gap Sayısı | Toplam Düzeltme |
|---------|------------|-----------------|
| HIGH | 3 | "Başla" butonu, Göz At sidebar, Footer icons |
| MEDIUM | 3 | Nav links, Genre tabs, Star rating |
| LOW | 5 | Track row, Toggle switch, Gender buttons, Social login, WiFi/BT toggle |

---

## 10. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | PNG master kataloğu |
| [[01-component-inventory]] | C01-C16 detayları |
| [[00-ascii-art-views]] | Piksel düzeyinde ASCII art'lar |
| [[02-implementation-plan]] | CSS uygulama planı |
| [[research/verified/wcag-22-aa]] | WCAG 2.2 AA referansı |

---

## 11. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.1.0 |
| Total Gaps | 11 |
| HIGH Priority | 3 |
| MEDIUM Priority | 3 |
| LOW Priority | 5 |
| Compliant Components | 10/16 (%63) |
| Renk Kontrast | ✅ Tümü uygun |
| Focus Visible | ✅ Tümü uygun |
| ARIA Labels | ⚠️ 3 eksik |
| Klavye Navigasyonu | ✅ Tümü uygun |
| Screen Spec References | 18 dosya |
| ASCII Art References | 18 PNG |

---

*Accessibility Gaps v3.1.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
