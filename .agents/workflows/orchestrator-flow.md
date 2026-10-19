---
title: "CoreMusic — Ana Orkestrasyon Akışı"
type: workflow-instruction
version: 1.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Task Analysis
  - Agent Routing
  - Multi Agent Coordination
  - Workflow Governance
  - Handover Management
  - Architecture Protection
  - Output Validation
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
      - "orchestration change"
      - "routing change"
      - "agent permission change"
      - "workflow change"
      - "architecture change"
changelog:
  - version: 1.0
    date: 2026-08-15
    changes:
      - Initial orchestration flow
      - Added task analysis pipeline
      - Added agent routing rules
      - Added handover protocol
      - Added validation pipeline
---

# Ana Orkestrasyon Akışı

## 1. Amaç

Bu dosya, CoreMusic AI sistemindeki tüm görevlerin Master Orchestrator tarafından nasıl analiz edildiğini, yönlendirildiğini ve tamamlandığını tanımlar.

## 2. Akış Diyagramı

```
KULLANICI TALEBİ
       |
       v
┌──────────────────────────┐
│  1. TALEP ALMA           │  Girdi: Kullanıcı mesajı
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  2. TALEP ANALİZİ        │  Keyword çıkarma, niyet belirleme
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  3. ALAN SINIFLANDIRMA   │  Backend / Frontend / Security / Data / Embedded / HW / FW
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  4. RİSK DEĞERLENDİRME   │  Düşük / Orta / Yüksek / Kritik
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  5. AGENT SEÇİMİ         │  Routing tablosundan eşleştirme
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  6. BAĞLAM YÜKLEME       │  Vault dosyalarını oku
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  7. GÖREVİ GÖNDER        │  Agent'a task dağıt
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  8. ÇIKTI DOĞRULAMA      │  Mimari + Güvenlik + Kalite kontrol
└──────────┬───────────────┘
           v
     ┌─────┴─────┐
     │           │
  GEÇTİ      GEÇMEDİ
     │           │
     v           v
┌─────────┐ ┌──────────────┐
│ 9a.     │ │ 9b. REDDEDİLDİ│
│ TAMAMLA │ │ Yeniden analiz│
└─────────┘ │ veya düzeltme │
            └──────────────┘
     |
     v
┌──────────────────────────┐
│ 10. HANDOVER (gerekirse) │  Çapraz alan geçişleri
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│ 11. LOG + VAULT GÜNCELLE  │  .ai/log.md yaz, vault sync
└──────────┬───────────────┘
           v
       GÖREV TAMAM
```

## 3. Aşama Ayrıntıları

### Aşama 1: Talep Alma

- Kullanıcı mesajını dinle
- Acele etmeden oku
- Belirsizlik varsa kısa soru sor

**Kontrol:**
- Talep net mi?
- Eksik bilgi var mı?
- Aciliyet durumu nedir?

### Aşama 2: Talep Analizi

- Keyword'leri çıkar
- Kullanıcı niyetini belirle
- Etkilenen sistemi tanımla

**Kontrol:**
- Hangi domain etkileniyor?
- Tek mi çok mu alanlı?
- Mevcut ADR ile çelişiyor mu?

### Aşama 3: Alan Sınıflandırması

| Alan | Keyword Örnekleri |
|------|-------------------|
| Backend | API, PHP, controller, middleware, endpoint |
| Frontend | CSS, JS, UI, responsive, ITCSS, BEM |
| Security | CSRF, XSS, OWASP, auth, şifreleme |
| Veritabanı | SQL, MySQL, BCNF, migration, schema |
| Embedded | C++, JUCE, ASIO, ses, DSP |
| Donanım | DAC, ADC, PCB, amplifikatör, KiCad |
| Firmware | XMOS, I2S, TDM, register |
| DevOps | Docker, CI/CD, deploy, pipeline |
| Platform | WASAPI, COM, WinRT, sürücü |

### Aşama 4: Risk Değerlendirmesi

| Seviye | Örnek | Onay |
|--------|-------|------|
| Düşük | Dokümantasyon, typo, renk değişimi | Gerekmez |
| Orta | Yeni bileşen, yeni API, refactoring | Agent incelemesi |
| Yüksek | Auth değişimi, DB schema, mimari | İnsan onayı zorunlu |
| Kritik | Üretim verisi, güvenlik açığı, root | İnsan + güvenlik incelemesi |

### Aşama 5: Agent Seçimi

Routing tablosunu kullan (SKILL.md §3):
- Keyword'leri tabloyla eşleştir
- Öncelik sırasına göre seç
- Çapraz alan ise handover planla

### Aşama 6: Bağlam Yükleme

**Her zaman yükle:**
- `.ai/CLAUDE.md`
- `.ai/AGENTS.md`
- `.ai/WORKFLOW.md`

**Domain'e göre ekle:**
- Backend → `.ai/brain.md`, ilgili ADR
- Frontend → `.ai/brain.md`, ITCSS yapısı
- Security → OWASP kuralları
- Veritabanı → `.ai/brain.md` DB bölümü, şema dosyaları

**Kurallar:**
- Bağlam olmadan gönderim yapma
- Bağlam eksik ise `VERIFICATION REQUIRED` yaz
- Bağlam ADR ile çelişirse insana yükselt
- Gönderim başına maks 3 bağlam dosyası

### Aşama 7: Görevi Gönder

- Agent'a detaylı prompt gönder
- Bağlamı dahil et
- Kısıtlamaları belirt
- Beklenen çıktıyı tanımla

### Aşama 8: Çıktı Doğrulama

```
AGENT ÇIKTISI
      |
      v
[1] YAPI KONTROLÜ — Dosya var mı? Yol doğru mu?
      |
      v
[2] MİMARİ KONTROL — Katman ihlali? SOLID? Pattern?
      |
      v
[3] GÜVENLİK KONTROLÜ — Auth, input, secret?
      |
      v
[4] KALİTE KONTROLÜ — Kod kalitesi, okunabilirlik, DRY?
      |
      v
[5] DOKÜMANTASYON — Yorumlar, ADR, changelog?
      |
      v
[6] NİHAİ ONAY — Tüm kontroller geçti → UYGULA veya REDDET
```

### Aşama 9: Tamamlama veya Red

**Geçti ise:**
- Uygulamayı onayla
- Log yaz
- Vault güncelle

**Geçmedi ise:**
- Hata sebebini analiz et
- Eksik bağlam mı?
- Yanlış agent mı seçildi?
- Düzelt ve yeniden gönder (maks 3 deneme)

### Aşama 10: Handover (Gerekirse)

Çapraz alan görevlerde handover protokolünü uygula:
- Kaynak agent → Hedef agent
- Bağlamı koru
- Onay al
- Devam et

### Aşama 11: Log ve Vault Güncelleme

Her işlem sonrası:
- `.ai/log.md`'ye yaz
- Vault indeksini güncelle
- Çapraz referansları kontrol et

## 4. Hata Yönetimi

| Durum | Aksiyon |
|-------|---------|
| Agent başarısız oldu | Hata sebebini analiz et, bağlamı yeniden yükle |
| 3 deneme başarısız | Dur ve insana rapor ver |
| ADR çelişkisi | ADR'yi koru, alternatif çözüm öner |
| Güvenlik riski | Dur, güvenlik incelemesi iste |
| Belirsizlik | `VERIFICATION REQUIRED` yaz, onay bekle |

## 5. Yasaklar

- Analiz yapmadan görev dağıtma
- Bağlam yüklemeden agent gönderme
- Güvenlik incelemesini atlama
- ADR'yi yok sayma
- İnsan onayı olmadan kritik işlem yapma
- Dosya adını/yerini değiştirme
- Üretim verisiyle oynama

## 6. İlgili Dosyalar

- `.opencode/skills/agent-orchestrator/SKILL.md` — Orchestrator tanımı
- `.workflows/session-init.md` — Oturum başlatma
- `.workflows/adr-creation.md` — ADR oluşturma
- `.workflows/vault-sync.md` — Vault senkronizasyonu
- `.workflows/security-audit.md` — Güvenlik denetimi
- `.workflows/deployment.md` — Dağıtım süreci
- `.workflows/hallucination-control.md` — Halüsinasyon kontrolü

## 7. Aktivasyon

"dağıt", "yönlendir", "koordine et", "görev ata", "agent başlat"

---

*Ana Orkestrasyon Akışı v1.0.0 — CoreMusic Workflow System*
*Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
