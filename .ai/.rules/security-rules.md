---
title: "CoreMusic — Güvenlik Kuralları (OWASP Top 10:2025 · Veto Yetkisi)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-11-01
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §6, §7, §21-§23 · .ai/.rules/kurallar.md · .ai/.rules/hata-listesi.md (HL-07, HL-09, HL-10)"
docType: claude
---

# CoreMusic — Güvenlik Kuralları (Security Rules)

**docType:** claude · **Klasör:** `.ai/.rules/` · **Sorumlu:** security-engineer (**bağımsız veto yetkisi mevcuttur**)

**Zorunlu Bağlantılar:** [[RULES.md]] · [[kurallar.md]] · [[hata-listesi.md]] · [[architecture-rules.md]] · [[backend-rules.md]] · [[.ai/CLAUDE.md]]

> **eli10:** Güvenlik katmanı asla bypass edilemez; şifreleme AES-256-GCM/Argon2id, CSRF adı csrf_token, sırlar asla markdown/log dosyalarına yazılmaz (Figma token yalnız .ai/.env.figma'da).

---

## 1. Amaç

OWASP Top 10:2025 standardına tam uyumlu CoreMusic güvenlik disiplinini; kriptografi, kimlik doğrulama, CSRF/CSP, gizli anahtar (secret) izolasyonu (`HL-07`), standart RBAC rolleri (`HL-09`) ve antivirüs ayrıştırması (`HL-10`) halinde vault SSOT'larından toplar. **K6 Güvenlik katmanı hard guardrail'dır; asla bypass edilemez.**

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Kimlik doğrulama, oturum yönetimi, CSRF, CSP, rate limit | Mimari sıralama detayları → [[architecture-rules.md]] §3.3 |
| Secret yüzeyi (.env, credential vault, `.ai/.env.figma`) | Veritabanı şema tasarımı → [[database-rules.md]] |
| Kriptografik standartlar ve yasak pattern'ler | Donanım DC offset rölesi → [[audio-hardware-rules.md]] |
| RBAC yetkilendirme sözlüğü (`HL-09`) ve CSP başlık denetimi (`HL-10`) | CSS ve tasarım tokenleri → [[frontend-rules.md]] |

---

## 3. Mimari

### 3.1 Kriptografi Standartları (`@.ai/CLAUDE.md` §12)

| Güvenlik Alanı | Zorunlu Standart | Yasaklar & Kısıtlamalar |
|---|---|---|
| **Simetrik Şifreleme** | **AES-256-GCM** (NIST SP 800-38D) / ChaCha20-Poly1305 | ECB, CBC modları yasaktır. Nonce her işlemde benzersiz üretilir; nonce yeniden kullanımı yasaktır. |
| **Parola Hashing** | **Argon2id** | MD5, SHA-1, SHA-256, DES ve düz metin parola saklamak **KESİNLİKLE YASAKTIR**. |
| **CSRF Koruması** | Token adı: **`csrf_token`** (`HG-06`, ADR-010) | `_csrf_token` adı yasaktır. Tüm state değiştiren (POST, PUT, DELETE, PATCH) isteklerde zorunludur. |
| **Oturum Güvenliği** | **3600s Idle Timeout & HTTPOnly Cookie** | Oturum kimliği URL'de taşınamaz; `localStorage`/`sessionStorage` içine auth token konulamaz. |

### 3.2 Middleware Güvenlik Pipeline'ı (Immutable — `@.ai/CLAUDE.md` §6)

CSP nonce üretimi **SecurityHeaders (#4)** içindedir; **SessionManager (#5)** bu nonce'u oturuma kaydeder.
- **Sıra Değişimi Yasaktır (`HG-07`):** Sıra değiştirilirse CSP bozulur veya CSRF bypass olur.
- **Rate Limiting (#3):** APCu tabanlı 60 istek / 60 saniye sınırı (brute-force savunması).
- **Test Bypass Koruması (#7):** `?_bypass=1` yalnız yerel test ortamında çalışır; prodüksiyon ortamında kesinlikle devre dışıdır.
- **Fail-Closed İlkesi:** Herhangi bir auth veya yetki middleware hatasında sistem varsayılan olarak erişimi **engeller** (fail-open yasaktır).

### 3.3 Gizli Anahtar (Secret) İzolasyonu (`HL-07` & `@.ai/CLAUDE.md` §23)

1. **Figma Token İzolasyonu (`HL-07`):** `FIGMA_TOKEN` ve `FIGMA_FILE_KEY` anahtarları ASLA `.md`, `.json`, `.log` veya `.yml` içine yazılmaz. Yalnızca `.ai/.env.figma` dosyasından okunur (`.gitignore:69`).
2. **Genel Sırlar:** Veritabanı şifreleri, API key'ler ve JWT secret'ları yalnızca sunucu ortam değişkenlerinden veya `.env` dosyasından okunur.
3. **Vault Maskeleme:** Dokümantasyon veya loglarda görünen sırlar istisnasız `[REDACTED]` olarak maskelenir.
4. **CI Denetimi:** Git repository'sinde GitLeaks taraması temiz olmalıdır; commit geçmişine sızan anahtarlar anında revoke edilir.

### 3.4 Standart Yetkilendirme Sözlüğü (RBAC — `HL-09`)

Yetkilendirme kontrollerinde (`PermissionMiddleware`) yalnızca aşağıdaki standart CoreMusic rol sözlüğü kullanılır. Sözlük dışı uydurma roller (`requiredRole`) 403 Access Denied hatasına sebep olur:
- `regular` (standart dinleyici)
- `premium` (Hi-Fi abonelik)
- `studio` (profesyonel DAW/stüdyo kullanıcısı)
- `car` (araç arayüzü modu)
- `admin` (yönetim paneli kullanıcısı)
- `system` (iç mikroservis iletişim kimliği)

### 3.5 Ağ & Başlık Analizi (Antivirüs Ayrıştırması — `HL-10`)

İstemci tarafında çalışan antivirüs yazılımlarının (Kaspersky vb.) web sayfalarına enjekte ettiği `kasperskylab_mac` scriptleri veya style blokları sunucu taraflı CSP ihlali sanılmamalıdır. Sunucu güvenlik başlıkları daima doğrudan `curl -I https://[domain]` komutuyla doğrulanır.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|---|---|
| 1 | K6 Güvenlik katmanını bypass etmeye çalışmak | Sistem derhal durdurulur |
| 2 | Gizli anahtarın dokümana veya koda açık yazılması (`HL-07`) | CRITICAL güvenlik ihlali; key revoke |
| 3 | `_csrf_token` kullanımı (`HG-06`) | İstek reddedilir |
| 4 | Sözlük dışı uydurma RBAC rolü kullanımı (`HL-09`) | 403 hatası; kod reddi |
| 5 | Güvenlik açığı bulan ajan kendisi düzeltmez | Security engineer agent'a handover edilir |
| 6 | Subagent commit çalıştırması (`HL-03`) | İhlal; orkestratör yetkisindedir |

---

## 5. Workflow

```text
Güvenlik Etkili İş (Auth / Oturum / Kripto / Middleware / Secret)
  → Skill: security-hardening
  → Vault: @.ai/CLAUDE.md §6 + §21-§23 + karar-kurallari.md
  → Gizli Anahtar İzolasyon Denetimi (.ai/.env.figma kontrolü — HL-07)
  → RBAC Rol Doğrulaması (HL-09)
  → OWASP Top 10:2025 Güvenlik Taraması
  → durum.md güncelleme + log.md append
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---|---|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | Kripto algoritmaları | AES-256-GCM ve Argon2id eksiksiz |
| 3 | CSRF token adı | `csrf_token` tek isim |
| 4 | Figma token izolasyonu | `.ai/.env.figma` ve `.gitignore` referansı mevcut (`HL-07`) |
| 5 | RBAC rol sözlüğü | 6 standart rol tanımlı (`HL-09`) |
| 6 | Antivirüs ayrıştırması | `HL-10` kuralı işlendi |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|---|---|---|
| Anayasa §6 | [[.ai/CLAUDE.md]] | Middleware pipeline ve güvenlik zinciri |
| Anayasa §21-§23 | [[.ai/CLAUDE.md]] | Yasak güvenlik pattern'leri |
| Hata Listesi Kataloğu | [[hata-listesi.md]] | HL-03, HL-07, HL-09, HL-10 güvenlik kuralları |
| Temel Direktifler | [[kurallar.md]] | KURAL-04 güvenlik ilkeleri |
| Master Kurallar | [[RULES.md]] | Genel aktivite kuralları |
| Figma Environment | `.ai/.env.figma` | FIGMA_TOKEN ve FILE_KEY SSOT |

---

**Authority:** Bayram Ali / Kurucu & Mimari Otorite  
**Last Updated:** 2026-11-01 · **Version:** 2.0.0
