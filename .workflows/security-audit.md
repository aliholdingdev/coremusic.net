---
title: "CoreMusic — Güvenlik Denetim Akışı"
type: workflow-instruction
version: 1.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - OWASP Top 10 Compliance
  - Security Vulnerability Detection
  - Red Team Review
  - Authentication Validation
  - Authorization Audit
  - Input Security Verification
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
      - "security policy change"
      - "OWASP rule change"
      - "middleware order change"
      - "authentication mechanism change"
      - "authorization model change"
changelog:
  - version: 1.0
    date: 2026-08-15
    changes:
      - Initial security audit workflow
      - Added OWASP Top 10 checklist
      - Added CoreMusic security controls
      - Added Red Team protocol
      - Added audit report format
---

# Güvenlik Denetim Akışı

## 1. Amaç

CoreMusic sistemindeki güvenlik açıklarını tespit etmek ve düzeltmek.

## 2. Akış Diyagramı

```
GÜVENLİK TALEBİ / PERİYODİK DENETİM
       |
       v
┌──────────────────────────┐
│  1. KAPSAM BELİRLEME     │  Hangi alanlar taranacak?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  2. OWASP TARAMASI       │  Top 10 kontrol listesi
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  3. KOD İNCELEMESİ       │  Güvenlik açığı taraması
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  4. YAPI KONTROLÜ        │  Middleware sırası, auth akışı
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  5. RAPOR OLUŞTUR        │  Bulguları sınıflandır
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  6. ÖNCELİKLENDİRME      │  Kritik / Yüksek / Orta / Düşük
└──────────┬───────────────┘
           v
     ┌─────┴─────┐
     │           │
  KRİTİK     DÜŞÜK
     │           │
     v           v
  DURDUR      DÜZELT
  İnsan onayı  ve devam
  iste
```

## 3. OWASP Top 10 Kontrol Listesi

| # | Tehdit | Kontrol | Durum |
|---|--------|---------|-------|
| 1 | SQL Enjeksiyonu | PDO prepared statements | ☐ |
| 2 | Kırılgan Kimlik Doğrulama | Parola hash, brute-force koruması | ☐ |
| 3 | Hassas Veri İfşası | Şifreleme, log güvenliği | ☐ |
| 4 | XML Dış Varlık (XXE) | XML parser yapılandırması | ☐ |
| 5 | Erişim Kontrolü İhlali | RBAC, yetki matrisi | ☐ |
| 6 | Yanlış Güvenlik Yapılandırması | Varsayılan şifreler, gereksiz servisler | ☐ |
| 7 | Kırılgan Script (XSS) | Çıktı kodlama, CSP | ☐ |
| 8 | Kırılgan Deserializasyon | Güvenli deserializasyon | ☐ |
| 9 | Bilinen Açıklarla Kullanım | Bağımlılık taraması | ☐ |
| 10 | Günlük ve İzleme Eksikliği | Yapılandırılmış loglama | ☐ |

## 4. CoreMusic Özel Kontroller

### 4.1 Middleware Sırası (Değiştirilemez)

```
OriginCheck → Cors → RateLimiter → SecurityHeaders →
SessionManager → Csrf → BypassAuth → Auth →
Permission → Validation
```

**Kural:** Bu sıra asla değiştirilemez.

### 4.2 CSRF Token

- Token adı: `csrf_token` (NOT `_csrf_token`)
- Her formda zorunlu
- Her API isteğinde kontrol

### 4.3 Kimlik Doğrulama

| Kontrol | Kural |
|---------|-------|
| Parola hash | bcrypt veya Argon2 |
| Oturum yönetimi | Secure cookie, HttpOnly |
| Token süresi | Jawpring ile sınırlı |
| Brute-force | Rate limiting + hesap kilitleme |

### 4.4 Yetkilendirme

| Kontrol | Kural |
|---------|-------|
| RBAC | Rol bazlı erişim kontrolü |
| Yetki matrisi | Her endpoint için tanımlı |
| Yetki yükseltme | Engellemek için testler |

### 4.5 Giriş Güvenliği

| Kontrol | Kural |
|---------|-------|
| SQL enjeksiyonu | PDO prepared statements |
| XSS | Çıktı kodlama |
| CSRF | Token doğrulama |
| Komut enjeksiyonu | Input doğrulama |
| Dosya yükleme | Güvenli dosya tipi kontrolü |

### 4.6 Veri Koruma

| Kontrol | Kural |
|---------|-------|
| Şifreleme | Hassas veriler şifreli |
| Secret yönetimi | .env dosyası, kodda yok |
| Kişisel veri | Gereksiz toplama yok |
| Veri ifşası | Hata mesajlarında hassas bilgi yok |
| Log güvenliği | Şifre/logonlanmış bilgi loglanmaz |

## 5. Red Team Protokolü

Her kritik görev sonrası:

```
RED TEAM İNCELEMESİ
       |
       v
Saldırı Yüzeyi Analizi
       |
       v
Zayıf Nokta Tespiti
       |
       v
Risk Raporu
       |
       v
Azaltma Planı
```

## 6. Rapor Formatı

```markdown
# GÜVENLİK DENETİM RAPORU

## Tarih: YYYY-MM-DD
## Kapsam: [Alan]
## Denetci: security-engineer

### Bulgu Özeti
- Kritik: X
- Yüksek: X
- Orta: X
- Düşük: X

### Detaylar
#### [Bulgu 1]
- **Öncelik:** Kritik/Yüksek/Orta/Düşük
- **Alan:** [Etkilenen dosya/bölüm**
- **Açıklama:** [Sorunun açıklaması**
- **Öneri:** [Düzeltme önerisi]
- **ADR:** [İlgili ADR varsa]

### Öneri Önceliği
1. [En kritik düzeltme]
2. [İkinci düzeltme]
3. [Üçüncü düzeltme]
```

## 7. Hata Yönetimi

| Durum | Aksiyon |
|-------|---------|
| Kritik açık | Durdur, insan onayı iste |
| Yüksek açık | Acil düzeltme planla |
| Orta açık | Bir sonraki sprint'e planla |
| Düşük açık | Dokümante et, planla |

## 8. Yasaklar

- Güvenlik kontrolünü atlama
- Kritik açığı görmezden gelme
- Üretim ortamında test yapma
- Secret'ları loglama
- Güvenlik mekanizmasını devre dışı bırakma

## 9. İlgili Dosyalar

- `.ai/CLAUDE.md` — Güvenlik kuralları
- `.ai/AGENTS.md` — Security engineer tanımı
- `.ai/brain.md` — Middleware pipeline
- `.claude/rules/core-rules.md` — Güvenlik kuralları

## 10. Aktivasyon

"security audit", "güvenlik denetimi", "OWASP kontrol", "güvenlik tarama"

---

*Güvenlik Denetim Akışı v1.0.0 — CoreMusic Workflow System*
*Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
