---
title: "CoreMusic — Oturum Başlatma Akışı"
type: workflow-instruction
version: 1.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - AI Session Initialization
  - Context Loading
  - Vault Verification
  - Memory Restoration
  - ADR Check
reference:
  authority: ".ai/WORKFLOW.md"
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
    - ".ai/.decisions/"
    - ".ai/architecture/adr/"
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
      - "session protocol change"
      - "context loading change"
      - "boot sequence change"
      - "vault structure change"
changelog:
  - version: 1.1
    date: 2026-09-24
    changes:
      - L0-L3 referansları K0-K20 / A0-A5 ile değiştirildi
      - İki ADR serisi gerçeği eklendi (.ai/.decisions + .ai/architecture/adr)
  - version: 1.0
    date: 2026-08-15
    changes:
      - Initial session init workflow
      - Added 12-step boot protocol
      - Added context loading rules
      - Added verification checklist
---

# Oturum Başlatma Akışı

## 1. Amaç

Her yeni AI oturumunun doğru bağlam ile başlamasını sağlamak.

**Session Checklist (2026-09-29):** Her oturum **başında** `CHECKLIST.md` §A (🔴 Başlama), **ortasında** §B (🟡 mid checkpoint), **kapanışında** §C (🟢) uygulanır ve `[ ]` → `[x]` işaretlenir. Açık iş listesi: `TODO.md` (P0/P1/P2, kaynak notlu). Bağlantılar: `.ai/CLAUDE.md` §16A · `.ai/WORKFLOW.md` §8.7B · `.workflows/session-init.md` · `.workflows/vault-sync.md` Aşama 8.
**Hedef seti — 20 dosya (2026-09-29 disk ölçümü):** 3 kök (`CLAUDE.md` · `README.md` · `WORKFLOW.md`) + 17 `.ai/` kök md. Sınıflandırma `CHECKLIST.md` §A0: 🟣 **CRITICAL 16** (boot'ta okunur — iki CLAUDE + AGENTS/WORKFLOW/brain/ROLE/index/keys/MEMORY/ULTRA-THINKING/engine/glossary/VISION/PROJECTS + CHECKLIST(A1) + TODO(A3)) · 🟡 **ON-DEMAND 3** (kök README · kök WORKFLOW · `.ai/broken-links-report.md`) · ⚫ **LOG 1** (`.ai/log.md` — append-only, boot'ta son 20 satır). Sınıf dışına çıkan dosya zorla okunmaz; güçlendirme `CHECKLIST.md` §C ve `.ai/CLAUDE.md` §16A ile bağlanır.

## 2. Akış Diyagramı

```
YENİ OTURUM
      |
      v
┌──────────────────────────┐
│  1. SİSTEM KONTEKSİ      │  Çalışma ortamını algıla
│     YÜKLEME              │
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  2. CLAUDE.MD OKU        │  AI anayasası, guardrails
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  3. AGENTS.MD OKU        │  Agent kayıtları, routing
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  4. WORKFLOW.MD OKU      │  Süreç tanımları
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  5. BRAIN.MD OKU         │  Mimari kararlar
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  6. ADR KONTROLÜ         │  Güncel ADR'leri tara
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  7. LOG OKU              │  Son işlemleri incele
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  8. MEMORY KONTROLÜ      │  Önceki oturum bilgisi
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  9. INDEX KONTROLÜ       │  Vault indeksini doğrula
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│ 10. KEYS KONTROLÜ        │  Keyword haritasını yükle
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│ 11. ENGINE KONTROLÜ      │  Orkestrasyon motoru indeksi
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│ 12. HAZIRLIK DOĞRULAMA   │  Tüm dosyalar yüklendi mi?
└──────────┬───────────────┘
           v
     ┌─────┴─────┐
     │           │
  HAZIR      EKSİK
     │           │
     v           v
  BAŞLAT     UYARI VER
             Eksik dosyayı
             listele
```

## 3. Aşama Ayrıntıları

### Aşama 1: Sistem Konteksi Yükleme

| Bilgi | Kaynak |
|-------|--------|
| Çalışma dizini | `C:\www\coremusic.net` |
| Platform | win32 |
| Tarih | Sistem tarihi |
| Git durumu | `git status` |

### Aşama 2-4: Temel Dosyalar

| Dosya | Amaç |
|-------|------|
| `.ai/CLAUDE.md` | AI anayasası, 16 Hard Guardrail |
| `.ai/AGENTS.md` | 11 agent, routing tablosu, handover |
| `.ai/WORKFLOW.md` | 12-faz vault refactoring, 20-faz yaşam döngüsü |

### Aşama 5: Mimari Kararlar

- `.ai/brain.md` oku
- K0-K20 katman yapısını ve A0-A5 alan etiketlerini anla (kanonik: [[../.ai/architecture/katman-baglilik-matrisi]]; adlandırma: [[../.ai/architecture/adlandirma-kurali]])
- Middleware pipeline sırasını kontrol et
- C++ audio kurallarını yükle

### Aşama 6: ADR Kontrolü

- İki ADR serisini ayır: `.ai/.decisions/` (karar serisi) + `.ai/architecture/adr/` (mimari seri, ADR-023…026) — birleştirme REDDEDİLDİ (ADR-026 §3.4)
- Frozen ADR'leri tara (ADR-001 → ADR-037, immutable)
- Aktif ADR'leri listele (`.ai/.decisions/index.md` — active ADR-038…089, draft yok)
- Çakışma var mı kontrol et (aynı numara → slug + konum birlikte okunur)

### Aşama 7-8: Geçmiş Bilgisi

| Dosya | Amaç |
|-------|------|
| `.ai/log.md` | Son işlemler, audit trail |
| `.ai/MEMORY.md` | Önceki oturum bilgisi, kalıcı durum |

### Aşama 9-11: Vault İndeksleri

| Dosya | Amaç |
|-------|------|
| `.ai/index.md` | Master katalog, 570+ dosya |
| `.ai/keys.md` | Keyword haritası, K0-K20 / A0-A5 mapping |
| `.ai/engine.md` | Orkestrasyon motoru indeksi |

### Aşama 12: Hazırlık Doğrulama

**Kontrol listesi:**
- [ ] CLAUDE.md yüklendi
- [ ] AGENTS.md yüklendi
- [ ] WORKFLOW.md yüklendi
- [ ] brain.md yüklendi
- [ ] ADR kontrolü yapıldı
- [ ] log.md okundu
- [ ] MEMORY.md kontrol edildi
- [ ] index.md doğrulandı
- [ ] keys.md yüklendi
- [ ] engine.md kontrol edildi
- [ ] `.ai/CHECKLIST.md` §A (Session Başlama) uygulandı — 5/5 `[x]`

## 4. Hata Yönetimi

| Durum | Aksiyon |
|-------|---------|
| Dosya bulunamadı | Uyarı ver, devam et |
| Vault tutarsız | `vault-sync` çalıştır |
| Eski oturum bilgisi | MEMORY.md'den devral |
| ADR çelişkisi | İnsan onayı iste |

## 5. Yasaklar

- Bağlam yüklemeden göreve başlama
- Eksik dosyayı görmezden gelme
- Önceki oturum bilgisini yok sayma

## 6. İlgili Dosyalar

- `.ai/CLAUDE.md`
- `.ai/AGENTS.md`
- `.ai/WORKFLOW.md`
- `.ai/brain.md`
- `.ai/index.md`
- `.ai/keys.md`
- `.ai/engine.md`
- `.ai/MEMORY.md`
- `.ai/log.md`
- `.ai/CHECKLIST.md` — session yaşam döngüsü checklist'i (baş/orta/kapanış)
- `.ai/TODO.md` — proje iş listesi (P0/P1/P2)

## 7. Aktivasyon

"session init", "oturum başlat", "yeni oturum", "başlat", "boot"

---

*Oturum Başlatma Akışı v1.0.0 — CoreMusic Workflow System*
*Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
