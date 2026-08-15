---
title: "CoreMusic — Halüsinasyon Kontrol Akışı"
type: workflow-instruction
version: 1.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Truth Verification
  - Hallucination Prevention
  - Source Validation
  - Claim Verification
  - Evidence-Based Output
  - Red Team Review
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
      - "truth mode policy change"
      - "verification rule change"
      - "hallucination detection change"
      - "evidence requirement change"
changelog:
  - version: 1.0
    date: 2026-08-15
    changes:
      - Initial hallucination control workflow
      - Added hallucination type taxonomy
      - Added verification checkpoints
      - Added Truth Mode rules
      - Added Red Team validation protocol
---

# Halüsinasyon Kontrol Akışı

## 1. Amaç

AI'ın uydurma bilgi üretmesini engellemek ve her iddianın doğrulanmasını sağlamak.

## 2. Akış Diyagramı

```
AI ÇIKTISI
      |
      v
┌──────────────────────────┐
│  1. İDDİA TESPİTİ        │  Hangi iddialar var?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  2. KAYNAK KONTROLÜ      │  Kaynak mevcut mu?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  3. DOĞRULAMA            │  Bilgi doğru mu?
└──────────┬───────────────┘
           v
     ┌─────┴─────┐
     │           │
  DOĞRU    ŞÜPHELİ
     │           │
     v           v
  KABUL ET   VERIFICATION
             REQUIRED
                 |
                 v
          ┌──────┴──────┐
          │             │
       KANIT         KANIT
       VAR           YOK
          │             │
          v             v
       KABUL ET    REDDET
                   Uydurma
                   olarak işaretle
```

## 3. Halüsinasyon Türleri

| Tür | Açıklama | Örnek |
|-----|----------|-------|
| **Uydurma API** | Var olmayan API | "Bu endpoint 200 döndürür" (kontrol edilmemiş) |
| **Uydurma Dosya Yolu** | Var olmayan dosya | "src/auth/login.php" (dosya yok) |
| **Uydurma Sürüm** | Yanlış versiyon bilgisi | "MySQL 9.2" (henüz çıkmamış) |
| **Uydurma Benchmark** | Kanıtsız performans | "%50 hızlı" (test edilmemiş) |
| **Uydurma Güvenlik Açığı** | Yanlış güvenlik raporu | "Bu kodda SQL injection var" (olmayan açık) |
| **Uydurma Test Sonucu** | Sahte test | "34 test geçti" (çalıştırılmamış) |
| **Uydurma Referans** | Yanlış referans | "ADR-099'da belirtildiği üzere" (ADR yok) |

## 4. Kontrol Noktaları

### 4.1 Dosya Yolu Kontrolü

```
İddia: "src/auth/login.php dosyasında..."
       |
       v
Dosya gerçekten var mı?
       |
   ┌───┴───┐
   EVET    HAYIR
   |         |
   Devam    VERIFICATION REQUIRED
```

### 4.2 API Kontrolü

```
İddia: "GET /api/users endpoint'i..."
       |
       v
Endpoint gerçekten tanımlı mı?
       |
   ┌───┴───┐
   EVET    HAYIR
   |         |
   Devam    VERIFICATION REQUIRED
```

### 4.3 Versiyon Kontrolü

```
İddia: "MySQL 9.x özelliği..."
       |
       v
Bu özellik bu sürümde var mı?
       |
   ┌───┴───┐
   EVET    HAYIR
   |         |
   Devam    VERIFICATION REQUIRED
```

### 4.4 Test Sonucu Kontrolü

```
İddia: "Testler başarılı..."
       |
       v
Test gerçekten çalıştırıldı mı?
       |
   ┌───┴───┐
   EVET    HAYIR
   |         |
   Devam    VERIFICATION REQUIRED + Test çalıştır
```

## 5. Truth Mode Kuralları

| Kural | Açıklama |
|-------|----------|
| **Kanıt yoksa kabul edilmez** | Her iddia için kanıt gerekli |
| **Bilinmeyen → VERIFICATION REQUIRED** | Emin olunmayan durumda belirt |
| **Doğrulanamamış bilgi → Red flag** | Kuşkulu bilgiyi işaretle |
| **Varsayım yapma** | Kesin bilgi olmadan tahmin üretme |
| **Kaynak göster** | Bilgi kaynağını belirt |

## 6. Doğrulama Formatı

### Doğru Kullanım

```markdown
Bu API endpoint'i [kaynak] doğrultusunda çalışmaktadır.
Doğrulama: [nasıl doğrulandı]
```

```markdown
Bu dosya yolu [dosya adı] olarak doğrulanmıştır.
Doğrulama: Dosya sistemi kontrolü yapıldı.
```

### Yanlış Kullanım

```markdown
# YANLIŞ
Bu API kesin destekleniyor.

# DOĞRU
Bu API desteği doğrulanmalıdır. [VERIFICATION REQUIRED]
```

```markdown
# YANLIŞ
Testler başarılı (34/34).

# DOĞRU
Testler çalıştırılmadı. Çalıştırılması gerekiyor. [VERIFICATION REQUIRED]
```

## 7. Red Team Halüsinasyon Kontrolü

Her kritik görev sonrası:

| Kontrol | Soru |
|---------|------|
| Dosya referansları | Tüm dosya yolları doğru mu? |
| API referansları | Tüm endpoint'ler var mı? |
| Versiyon bilgileri | Sürüm numaraları doğru mu? |
| Test sonuçları | Sonuçlar gerçekten çalıştırıldı mı? |
| Güvenlik iddiaları | Açıklar gerçekten var mı? |
| Performans iddiaları | Rakamlar test edildi mi? |
| ADR referansları | ADR'ler gerçekten var mı? |

## 8. Hata Yönetimi

| Durum | Aksiyon |
|-------|---------|
| Uydurma tespit | İddiyi sil, `VERIFICATION REQUIRED` yaz |
| Şüpheli bilgi | Kaynak iste, doğrulamadan kullanma |
| Eksik bilgi | Tamamla veya `VERIFICATION REQUIRED` yaz |
| Çelişkili bilgi | İnsan onayı iste |

## 9. Yasaklar

- Doğrulanmamış bilgiyi kesin gibi sunma
- Kaynak göstermeden iddia üretme
- Test sonucunu çalıştırmdan raporlama
- Versiyon bilgisini doğrulamadan yazma
- Dosya yolunu kontrol etmeden referans verme

## 10. İlgili Dosyalar

- `.ai/CLAUDE.md` — Truth Mode kuralları
- `.ai/AGENTS.md` — Halüsinasyon önleme
- `.opencode/skills/hallucination-control/SKILL.md` — Halüsinasyon kontrol skill'i
- `.opencode/skills/red-team-truth-mode/SKILL.md` — Red team + Truth mode

## 11. Aktivasyon

"halüsinasyon kontrol", "doğrulama", "gerçeklik kontrol", "yalan kontrol"

---

*Halüsinasyon Kontrol Akışı v1.0.0 — CoreMusic Workflow System*
*Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
