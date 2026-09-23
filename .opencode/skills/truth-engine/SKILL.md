---
title: "CoreMusic — Truth Engine"
type: skill-instruction
version: 1.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Zero-Hallucination Enforcement
  - Confidence-Based Verification
  - Adversarial Review
  - H001-H039 Rejected Pattern Database
  - Attack Path Documentation
triggers:
  - "red team"
  - "truth mode"
  - "hallucination"
  - "doğrulama"
  - "verification"
  - "confidence check"
  - "source citation"
  - "her mimari karar"
  - "her kod üretimi"
  - "her vault güncellemesi"
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/brain.md"
changelog:
  - version: 1.0
    date: 2026-09-20
    changes:
      - Birleştirilmiş: hallucination-control + red-team-truth-mode
      - H001-H039 tek dosyada
      - Gereksiz agentic orchestration kaldırıldı
---

# TRUTH ENGINE — CoreMusic

## 1. Kimlik

**Bu skill zorunludur. İstisnasız tüm agent çıktılarına uygulanır.**

Temel prensip: **Doğrulanamayan bilgi üretilmez. Tahmin yok, kanıt var.**

---

## 2. Beş Mutlak Kural

1. **UYDURMAK KESİNLİKLE YASAKTIR.**
2. **HER TEKNİK İDDİA EN AZ 2 DOĞRULANABİLİR KAYNAK GEREKTİRİR.**
3. **EMİN DEĞİLSEN DUR.** `// [!]️ VERIFICATION REQUIRED` yaz.
4. **TEK KAYNAK:** Yerel vault (.ai) ve resmi dokümantasyon.
5. **WEB ARAMA YASAKTIR.** Doğrulama vault, script, CI/CD ile yapılır.

---

## 3. Güven Skoru Sistemi

| Aralık | Durum | Aksiyon |
|--------|-------|---------|
| 90-100 | **VERIFIED** | Kod yaz, uygula |
| 60-89 | **UNVERIFIED** | Kullanılabilir ama riskli — kullanıcı onayı gerekli |
| <60 | **REJECTED** | YASAK — doğru alternatifi öner |

### Ağırlıklı Puanlama

| Kategori | Puan | Kaynaklar |
|----------|------|-----------|
| Resmi Dokümantasyon | 40 | php.net, MDN, dev.mysql.com, owasp.org |
| Standartlar (RFC/ISO) | 25 | OWASP Top 10 2025, PSR-12 |
| Vault Kanıtı | 15 | .ai/decisions/, brain.md, CLAUDE.md |
| Topluluk (yüksek puan) | 10 | StackOverflow accepted, GitHub issues |
| Bilinmeyen/Eski kaynak | -50 | Blog, Medium, Wikipedia, pre-2024 |

### Atomics İddia Ayrıştırması

Her teknik çıktı atomik iddialara ayrıştırılır. Her iddia kendi güven skorunu alır.

**Final skor = minimum(atomic iddia skorları).**

---

## 4. Red Team İnceleme Protokolü

Her agent çıktısı **3 bağımsız düşmanca inceleme** geçer:

```
Agent Çıktısı
    ↓
+----+----+----+
| TECH | SEC | ARCH |
+----+----+----+
    ↓
BİRLEŞTİR → Saldırı yolları
    ↓
TESLİM (veya REDDET)
```

| İnceleme | Soru |
|----------|------|
| **Teknik** | "Bu kod production'da çöker mü? Bellek sızıntısı? Race condition?" |
| **Güvenlik** | "OWASP Top 10 uyumu? Credential hardcoded? CSRF/CSP bypass?" |
| **Mimari** | "Layer violation? Middleware sırası? BCNF ihlali? ADR uyumu?" |

### Saldırı Yolu Dokümantasyonu

Her inceleme için:
```markdown
## Saldırı Yolu: [Saldırı Adı]
**Hedef:** [bileşen/endpoint/iddea]
**Giriş Noktası:** [sistem nasıl giriyor]
**Yayılım:** [sistemde nasıl hareket ediyor]
**Etki:** [hangi hasarı veriyor]
**Ciddiyet:** [CRITICAL / HIGH / MEDIUM / LOW]
**Düzeltme:** [somut çözüm]
```

---

## 5. Reddedilen Örüntüler (H001-H039)

**Eşleşme = anında reddetme, istisnasız.**

### 5.1 Donanım/Ses (H001-H009)

| ID | Örüntü | Doğru Alternatif |
|----|--------|-------------------|
| H001 | PCM5122 ile 8.1 surround | PCM3168A veya AK4458 |
| H002 | RPi GPIO 5V → 3.3V | Level shifter gerekli |
| H003 | XMOS XU316 ASIO SDK olmadan | ASIO SDK zorunlu |
| H004 | Class AB amp SNR >100dB (THD+N olmadan) | Her ikisi de gerekli |
| H005 | 8+1 surround I2S ile DSP olmadan | DSP zorunlu |
| H006 | ADAU1467 programlanmadan | XMOS DSP ikame edilemez |
| H007 | 0.5ms gecikme ASIO Exclusive olmadan | ASIO Exclusive zorunlu |
| H008 | PCM3168A specs yanlış | TSSOP-48, 8ch DAC + 6ch ADC |
| H009 | AK4458 specs yanlış | 32-bit, 8ch DAC, DSD512 |

### 5.2 Güvenlik/Kripto (H010-H019)

| ID | Örüntü | Doğru Alternatif |
|----|--------|-------------------|
| H010 | JWT secret hardcoded | credential_vault AES-256-GCM |
| H011 | MD5/SHA-1 şifreleme | Argon2id veya AES-256-GCM |
| H012 | Hardcoded API key / DB password | .env veya vault |
| H013 | $_GET/$_POST doğrudan erişim | Input filtreleme |
| H014 | CSRF token '_csrf_token' | 'csrf_token' |
| H015 | CSP nonce 16 byte | 256-bit random_bytes(32) |
| H016 | Session idle 1800s | 3600s |
| H017 | Session name 'PHPSESSID' | 'COREMUSIC_SESS' |
| H018 | Rate limit key 'rate_limit:' | 'rl:' . md5($ip) |
| H019 | MFA/TOTP iddiası | CoreMusic'de yok (ADR-011) |

### 5.3 Veritabanı/SQL (H020-H029)

| ID | Örüntü | Doğru Alternatif |
|----|--------|-------------------|
| H020 | Versiyon uyumsuz SQL fonksiyonu | MySQL 9 docs kontrol |
| H021 | SELECT * kullanımı | Açık kolon listesi |
| H022 | ORM kullanımı | PDO prepared (ADR-002) |
| H023 | Çapraz DB foreign key | 9 DB BCNF izolasyonu |
| H024 | DELETE soft delete olmadan | deleted_at zorunlu |
| H025 | DB adı 'coremusic_music' | 'coremusic_musics' |
| H026 | DB adı 'coremusic_download' | 'coremusic_catalog' |
| H027 | 'coremusic_neva' veya 'coremusic_credential' | Config'de yok |
| H028 | 10 veritabanı iddiası | Tam olarak 9 |
| H029 | SQL string birleştirme | Prepared statement |

### 5.4 API/Middleware (H030-H039)

| ID | Örüntü | Doğru Alternatif |
|----|--------|-------------------|
| H030 | '/api/v2/auth/login' endpoint | Gerçek route: '/login' |
| H031 | Olmayan class'lar (FileUploadHandler vb.) | system.database kontrol |
| H032 | Olmayan dizinler (storage/, uploads/) | Root yapısını kontrol |
| H033 | Middleware sırası değişikliği | Session→BypassAuth→RateLimit→Auth→SecurityHeaders→Csrf |
| H034 | die()/header() middleware'de | Return ['halt' => true] |
| H035 | $_POST/$_SERVER doğrudan | Normalize $request |
| H036 | 'COREMUSIC_SID' session | 'COREMUSIC_SESS' |
| H037 | Cookie flags eksik | HttpOnly=1, Secure=1, SameSite=Lax |
| H038 | SecurityHeaders SessionManager'dan önce | SessionManager önce |
| H039 | BypassAuth production'da | APP_ENV=production'da devre dışı |

---

## 6. Kaynak Doğrulama

### Vault Bakım Sırası

```
1. .ai/knowledge/verified/     → Doğrudan kullan
2. .ai/knowledge/unverified/   → 30 gün kontrol + kullanıcı onayı
3. .ai/knowledge/rejected/     → Reddedilen örüntü kontrolü
4. .ai/decisions/accepted/     → ADR kararları
5. .ai/brain.md                → Merkezi kararlar
6. .ai/architecture/           → Katman tanımları
7. CLAUDE.md                   → Proje kuralları
```

### Kaynak Formatı

| Kaynak Tipi | Format |
|-------------|--------|
| Vault referansı | `[[.ai/decisions/accepted/ADR-XXX]]` |
| Resmi docs | `[PHP Manual - PDO::prepare](url)` |
| Datasheet | `[TI PCM3168A Datasheet](url)` |
| Standart | `[OWASP SQL Injection](url)` |
| Kod referansı | `dosya/yolu#satır` |

---

## 7. Doğrulama Kontrol Listesi

Her görev TÜM maddeleri tamamlamalı:

- [ ] H001-H039 reddeeilen örüntü taraması (eşleşme yok)
- [ ] Vault bilgi bankası bakımı
- [ ] Güven skoru hesaplama (ağırlıklı rubrik)
- [ ] 3 yönlü Red Team incelemesi (Teknik / Güvenlik / Mimari)
- [ ] Saldırı yolları dokümante
- [ ] Skor ≥90 → VERIFIED, kod yaz
- [ ] Skor 60-89 → UNVERIFIED, VERIFICATION REQUIRED ekle
- [ ] Skor <60 → REJECTED, doğru alternatifi öner
- [ ] Truth Mode bloğu çıktının sonuna ekle
- [ ] Tüm kaynaklar referansla

---

## 8. Truth Mode Çıktı Bloğu (Zorunlu)

Her kritik çıktı bu bloğu içermeli:

```markdown
---
### Truth Engine Doğrulama
- **Durum:** VERIFIED / UNVERIFIED / REJECTED
- **Güven Skoru:** [skor]/100
- **Red Team:** Teknik [OK] | Güvenlik [OK] | Mimari [OK]
- **Kaynaklar:**
  1. [kaynak 1] — [kategori] ([puan] pts)
  2. [kaynak 2] — [kategori] ([puan] pts)
- **Kontroller:**
  - [x] H001-H039 taraması
  - [x] Vault bakımı
  - [x] Recency filtresi (2024+)
  - [x] Cross-reference (3+ kaynak)
  - [x] Red Team: Teknik / Güvenlik / Mimari
---
```

---

## 9. Yasaklar

| Yasaklı | Neden |
|---------|-------|
| Doğrulanamayan bilgi üretme | Zero hallucination |
| Tek kaynakla VERIFIED deme | Cross-validation zorunlu |
| H001-H039'ı atlama | Anında reddetme |
| Vault'ta olmayan class/endpoint iddia etme | H030-H039 |
| Web aramasıyla doğrulama | Sadece vault + resmi docs |

---

## 10. Quick Reference

| İhtiyaç | Bölüm |
|---------|-------|
| Güven skoru nasıl hesaplanır? | §3 |
| Hangi örüntüler reddedilir? | §5 (H001-H039) |
| Kaynak nasıl doğrulanır? | §6 |
| Red Team nasıl yapılır? | §4 |
| Truth bloğu nasıl yazılır? | §8 |

---

*Truth Engine v1.0 — CoreMusic*
*Authority: Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
*Zero tolerance for hallucinations*
