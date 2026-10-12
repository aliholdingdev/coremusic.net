---
title: "CoreMusic — Dağıtım Akışı"
type: workflow-instruction
version: 1.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Production Deployment
  - Pre-flight Validation
  - Health Check Verification
  - Rollback Management
  - Distribution Control
  - Deployment Monitoring
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
      - "deployment target change"
      - "infrastructure change"
      - "CI/CD pipeline change"
      - "production config change"
      - "rollback procedure change"
changelog:
  - version: 1.0
    date: 2026-08-15
    changes:
      - Initial deployment workflow
      - Added pre-flight checklist
      - Added health check protocol
      - Added rollback procedure
      - Added monitoring rules
---

# Dağıtım Akışı

## 1. Amaç

CoreMusic bileşenlerinin güvenli ve kontrollü şekilde dağıtılmasını sağlamak.

## 2. Akış Diyagramı

```
DAĞITIM TALEBİ
       |
       v
┌──────────────────────────┐
│  1. ÖN KONTROLLER        │  Dağıtıma hazır mı?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  2. GÜVENLİK DOĞRULAMA   │  Güvenlik kontrolleri
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  3. TEST DOĞRULAMA       │  Test sonuçları
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  4. ONAY                 │  İnsan onayı
└──────────┬───────────────┘
           v
     ┌─────┴─────┐
     │           │
  ONAY       RED
     │           │
     v           v
  DAĞIT       DURDUR
     |         Düzelt
     v
┌──────────────────────────┐
│  5. DAĞITIM              │  Uygulamayı dağıt
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  6. SAĞLIK KONTROLÜ      │  Çalışıyor mu?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  7. İZLEME               │  Performans izleme
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  8. LOG                  │  Dağıtım kaydı
└──────────┬───────────────┘
           v
       DAĞITIM TAMAM
```

## 3. Aşama Ayrıntıları

### Aşama 1: Ön Kontroller

| Kontrol | Durum |
|---------|-------|
| Kod incelemesi tamamlandı mı? | ☐ |
| Testler başarılı mı? | ☐ |
| Güvenlik taraması yapıldı mı? | ☐ |
| Dokümantasyon güncellendi mi? | ☐ |
| Changelog yazıldı mı? | ☐ |
| Migration dosyası hazır mı? | ☐ |

### Aşama 2: Güvenlik Doğrulaması

- Security engineer onayı
- Güvenlik açığı taraması
- Secret'lar doğru mu?
- Ortam değişkenleri doğru mu?

### Aşama 3: Test Doğrulaması

| Test Türü | Durum |
|-----------|-------|
| Birim testleri | ☐ |
| Entegrasyon testleri | ☐ |
| E2E testleri | ☐ |
| Güvenlik testleri | ☐ |
| Performans testleri | ☐ |

### Aşama 4: Onay

| Risk Seviyesi | Onay |
|---------------|------|
| Düşük | Agent self-review |
| Orta | Agent + peer review |
| Yüksek | İnsan onayı |
| Kritik | İnsan + güvenlik + mimari onay |

### Aşama 5: Dağıtım

**Dağıtım ortamları:**

| Ortam | Hedef |
|-------|-------|
| Geliştirme | Local development |
| Test | Test sunucusu |
| Üretim | production.coremusic.net |

**Dağıtım adımları:**
1. Yedekleme al
2. Migration'ı çalıştır
3. Dosyaları yükle
4. Config'i güncelle
5. Servisi başlat

### Aşama 6: Sağlık Kontrolü

| Kontrol | Amaç |
|---------|------|
| HTTP 200 | Ana sayfa çalışıyor mu? |
| API yanıtı | Endpoint'ler çalışıyor mu? |
| Veritabanı | Bağlantı sağlıklı mı? |
| Log'lar | Hata logu var mı? |
| Performans | Yanıt süresi normal mi? |

### Aşama 7: İzleme

- Hata oranı izleme
- Performans metrikleri
- Kaynak kullanımı
- Kullanıcı geri bildirimi

### Aşama 8: Log

```markdown
## YYYY-MM-DD HH:mm — DAĞITIM

**İşlem:** Üretim dağıtımı
**Agent:** devops-engineer
**Versiyon:** X.Y.Z
**Ortam:** production
**Sonuç:** Başarılı/Başarısız
**Notlar:** Ek bilgiler
```

## 4. Geri Dönüş (Rollback)

Dağıtım başarısız olursa:

```
BAŞARISIZLIK TESPİTİ
       |
       v
┌──────────────────────────┐
│  1. DURDUR               │  Dağıtımı durdur
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  2. ETKİ ANALİZİ         │  Ne kadar etkilendi?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  3. GERİ DÖNÜŞ           │  Önceki sürüme dön
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  4. DOĞRULAMA            │  Geri dönüş çalıştı mı?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  5. KÖK NEDEN ANALİZİ    │  Neden başarısız oldu?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  6. DÜZELTME             │  Sorunu çöz
└──────────┬───────────────┘
           v
       TEKRAR DENE
```

## 5. Hata Yönetimi

| Durum | Aksiyon |
|-------|---------|
| Test başarısız | Dağıtımı durdur, düzelt |
| Güvenlik açığı | Acil durdur |
| Performans düşüklüğü | Geri dönüş yap |
| Bağlantı hatası | Tekrar dene |
| 3 deneme başarısız | İnsan onayı iste |

## 6. Yasaklar

- Testler geçmeden dağıtım yapma
- İnsan onayı olmadan üretime dağıtma
- Yedekleme almadan migration çalıştırma
- Geri dönüş planı olmadan dağıtma
- Güvenlik doğrulamasını atlama

## 7. İlgili Dosyalar

- `.ai/CLAUDE.md` — Dağıtım kuralları
- `.ai/AGENTS.md` — DevOps engineer tanımı
- `.workflows/security-audit.md` — Güvenlik doğrulaması
- `docker-compose.yml` — Container yapılandırması
- `.github/workflows/` — CI/CD pipeline

## 8. Aktivasyon

"deploy", "dağıt", "yayınla", "production", "dağıtım"

---

*Dağıtım Akışı v1.0.0 — CoreMusic Workflow System*
*Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
