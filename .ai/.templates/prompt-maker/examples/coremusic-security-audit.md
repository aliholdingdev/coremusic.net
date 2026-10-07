---
title: "Prompt Maker Example — CoreMusic Security Audit (OWASP)"
type: example
category: prompt-maker
version: 1.1.0
status: active
authority: "SSOT: .ai/.templates/index.md — Guardrail #16 Mandatory"
updated: 2026-10-07
---

# GENERATED PROMPT — CoreMusic Security Audit: AuthN/AuthZ & API Surface (OWASP Top 10:2025)

> **Örnek statüsü:** Bu dosya, `../template.md` §0 (MIN 500 SATIR sözleşmesi) +
> `../formats/format-templates.md` **Format 4 (SECURITY AUDIT)** kullanılarak üretilmiş
> bir **final prompt** örneğidir. Üretim: 2026-10-07 · Kanıt zorunlu (uydurma bulgu YASAK).

---

## 1. SCOPE

**Denetlenen yüzey:**
- **AuthN/AuthZ zinciri:** middleware #8 (Auth: JWT+Session), #9 (Permission: RBAC),
  #7 (BypassAuth — prod'da devre dışı olmalı), session yönetimi (ADR-011).
- **API Gateway:** `api.coremusic.net` — rate limit, validation, correlation ID (§6A.1).
- **Ortak endpoint'ler:** music playback, auth.coremusic.net (SSO), download kuyruğu,
  admin konsolu (yüksek yetki).
- **Veri:** `coremusic_auth` (sessions, tokens, credential vault), `coremusic_user`.
- **Middleware pipeline tamamı** (10 adım, §6 — sırası immutable).
- **CSRF/CSP:** `csrf_token` stratejisi (ADR-010), CSP nonce (ADR-012).

**Kapsam dışı (explicit):**
- Donanım/firmware yüzeyi (K1–K3) — ayrı denetime tabi.
- Fiziksel erişim, sosyal mühendislik.
- Kapsam dışı servis kodu (yalnız burada adı geçenler).
- Performans/DoS geniş denetimi (yalnız rate-limit ile ilişkili mikro-doğrulama).

**Denetim derinliği:**
1. **Statik:** PHP kod + middleware kayıtları + yapılandırma (`.env` okunmaz — yalnız
   anahtar ADLARI ve kod kullanımı incelenir; değer asla kopyalanmaz).
2. **Dinamik:** Harici araç yoksa manuel HTTP senaryoları (Postman/curl listesi) —
   bu prompt yalnızca **denetim planını + bulgu formatını** üretir.
3. **Tasarım:** Threat model (STRIDE benzeri, satır içi).

**Kanıt standardı:** Her bulgu = `dosya:satır` veya `HTTP istek/yanıt` ile kanıtlanır.
Kanıtsız bulgu YAZILMAZ (uydurma severity/cve yasak — §5).

---

## 2. ASSET

| Varlık | Kritiklik | Veri | Koruma gereği |
|--------|:---------:|------|---------------|
| User credentials (Argon2id hash) | KRİTİK | parola özeti | geri döndürülemezlik, brute-force koruması |
| Credential Vault (AES-256-GCM) | KRİTİK | 3. taraf sırları | anahtar yönetimi, REUSABLE değil |
| Session cookie (HTTPOnly) | KRİTİK | oturum kimliği | hijack koruması, timeout 3600s |
| JWT access/refresh | YÜKSEK | kimlik+yetki | imzalama, yenileme, iptal |
| RBAC matrisi (regular/premium/studio/car/admin/system) | YÜKSEK | yetki | ihlal = tam yetki kaybı |
| API keys + rate limit kayıtları | ORTA | quota | sızıntı =quota hırsızlığı |
| Audit log (`coremusic_logs`) | YÜKSEK | olay geçmişi | bütünlük (append-only), manipülasyon |
| Müzik kütüphane verisi (kişisel) | ORTA | PII/ilgi | erişim sahipliği |
| `.env` / vault anahtarları | KRİTİK | sırlar | kod/log'a sızmaz (REDACTED) |

**Varlık kuralı:** Her bulgu, etkilediği varlık satırıyla eşleştirilir — eşleşmeyen
bulgu kapsam dışıdır.

---

## 3. ATTACK SURFACE

**Giriş noktaları (envanter):**

| # | Giriş | Protokol/port | Middleware öncesi riski |
|---|-------|---------------|------------------------|
| 1 | `coremusic.net` (landing) | 80 | statik, düşük |
| 2 | `music.coremusic.net` | 81 | SPA + API |
| 3 | `admin.coremusic.net` | 80 | YÜKSEK yetki — yetki ihlali kritik |
| 4 | `auth.coremusic.net` | — | credential processing |
| 5 | `api.coremusic.net` gateway | — | tüm BFF'lerin tek kapısı |
| 6 | `download.coremusic.net` | 3001 | dosya işleme (path/command riski) |
| 7 | WebSocket (audio 9742) | WS | auth upgrade, replay |

**Middleware pipeline analizi (sıra DEĞİŞTİRİLEMEZ — §6):**

```text
1 OriginCheck   → köken doğrulama (whitelist); bypass CDP:Origin yok mu?
2 Cors          → wildcard Origin? credentials:true + * kombinasyonu?
3 RateLimiter   → APCu 60/60s; anahtar (IP? user? ikisi?); bypass (XFF spoof)
4 SecurityHeaders→ CSP strict-dynamic + nonce; HSTS; nosniff; frame
5 SessionManager→ cookie flags (HttpOnly, Secure, SameSite); nonce kaydı
6 Csrf          → POST/PUT/DELETE token; GET'te yok (doğru); token karşılaştırma timing
7 BypassAuth    → PROD'DA DEVRE DIŞI olmalı (?_bypass=1) — KRİTİK kontrol
8 Auth          → JWT doğrulama (imza, exp, aud, iss) + session bağlama
9 Permission    → RBAC matrisi (premium içerik, admin route'ları)
10 Validation   → DTO/param; sonra Controller
```

**Tespit edilecek yüzey soruları:**
- OriginCheck atlanabilir mi? (header spoof, CORS reflection)
- RateLimiter X-Forwarded-For ile kandırılabilir mi? (güvenilmeyen proxy varsayımı)
- CSP nonce üretimi SecurityHeaders (#4) içinde — (#5) kaydetmezse her istekte taze nonce
  mı? (yalnız cache sorunu değil, inline script yanlışlıkla patlar)
- WebSocket upgrade'i Auth öncesi veri aktarıyor mu?
- CORS: `Allow-Origin` refleksiyonu + `Allow-Credentials: true` (en tehlikeli kombinasyon)

---

## 4. THREATS

**OWASP Top 10:2025 — madde madde denetim listesi:**

| ID | Tehdit | Bu projede vektör adayı | Vektör yoksa |
|----|--------|--------------------------|--------------|
| A01 Broken Access Control | IDOR: `/tracks/{id}` sahiplik; admin route RBAC; horizontal escalation (user A → B playlist) | "yok" diye KAPANMAZ — test senaryosu yazılır |
| A02 Cryptographic Failures | Parola: Argon2id parametreleri; JWT alg (HS256 vs RS); TLS zorlaması; vault key rotation | parametreler dosyada aranır |
| A03 Injection | SQL (prepared YASAK除外 ihlali var mı); XSS (innerHTML); Command injection (download service — yt/deemix args); path traversal (media file) | §21 forbidden patterns taraması |
| A04 Insecure Design | password reset akışı; rate limit login'de yoksa brute-force tasarımı; BypassAuth varlığı (tasarım kararı ADR-008) | tasarım gözden geçirme |
| A05 Security Misconfiguration | debug modu prod'da; default creds; wildcard CORS; verbose error (stack trace); `.env` web erişiminde | config taraması |
| A06 Vulnerable Components | composer/npm audit; bilinen CVE (sürüm iddiası YOKSA `[VERIFY REQUIRED]` — uydurma CVE yasak) | SBOM varsa oku |
| A07 AuthN Failures | JWT exp/aud/iss kontrolü; session fixation (session id yenileme login'de); credential stuffing rate limit; `?_bypass=1` prod | §6 #7 KRİTİK |
| A08 Integrity Failures | CI/CD secret sızıntısı (GitLeaks workflow); unsigned update; deserialization (PHP `unserialize` kullanıcı verisinde) | `unserialize` grep |
| A09 Logging Failures | audit log'da auth olayları (başarılı/başarısız login, yetki reddi); token'lar log'a sızıyor mu (REDACTED) | log formatı denetimi |
| A10 SSRF | webhook/URL parametresi olan servisler (download URL, remote cover fetch); internal IP erişimi | URL parametre envanteri |

**Tehdit senaryosu formatı (her madde için):**

```text
Adversary: kim? (oturumsuz saldırgan / düşük yetkili kullanıcı / içeriden)
Vector:     hangi girişten? (endpoint/header/çerez)
Goal:       ne kazanır? (veri, yetki, para, hizmet dışı)
Preconditions: ne gerek? (kayıtlı hesap, belirli rol, timing)
Sequence:   adım adım (1..n)
Impact:     varlık tablosu (§2) ile eşleşme
```

**Örnek derin tehdit — BypassAuth sızıntısı:**

```text
Adversary: ağdaki herhangi biri (MITM veya kötü niyetli link)
Vector:    ?_bypass=1 (eğer prod'da aktifse — ADR-008 test bypass)
Goal:      auth atlayıp korumalı kaynaklara erişim
Preconditions: BypassAuth middleware'inin prod'da ETKİN olması (yanlış config)
Sequence:  1) herhangi bir URL'e ?_bypass=1 ekle 2) Auth middleware skip
           3) Permission da skip ediliyse → admin'e kadar erişim
Impact:    KRİTİK — tüm AuthN/AuthZ katmanı anlamsızlaşır
Doğrulama: config'de BypassAuth'ın prod'da disable olduğunun KANITI (kod satırı)
```

---

## 5. FINDINGS

**Bulgu formatı (zorunlu — kanıtsız bulgu YOK):**

```text
[F-{NN}] {Kısa başlık}
  Severity : CRITICAL | HIGH | MEDIUM | LOW  (aşağıdaki tanım tablosu)
  Evidence : {dosya}:{satır}  veya  HTTP: {istek} → {yanıt}
  RootCause: {kök neden — belirti değil}
  Impact   : {etkilenen varlık (§2) + sonuç}
  OWASP    : A0X
```

**Severity tanım tablosu:**

| Seviye | Tanım | Karar |
|--------|-------|-------|
| CRITICAL | Auth bypass, RCE, tam veri sızıntısı, vault anahtarı sızıntısı | derhal fix + release blocker |
| HIGH | yetki yükseltme, XSS stored, SQLi (doğrulanmış), kritik config açık | 24-48s fix |
| MEDIUM | zayıf cookie bayrakları, eksik log, idor (sınırlı veri) | planlanan sprint |
| LOW | bilgi sızdırmayan header eksikliği, kod kalitesi-güvenlik | backlog |

- **Severity yalnızca kanıt varsa verilir.** "Olabilir" → bulgu DEĞİL, "kontrol edilecek"
  satırı (§VALIDATION listesine).
- **Uydurma CVE:** sürüm + CVE eşleşmesi doğrulanamazsa `[VERIFY REQUIRED]`.
- **Bulgusuz OWASP maddesi:** "no finding — kanıt: tarama komutu + sonuç" satırı ZORUNLU
  (sessiz geçiş yasak; ama uydurma bulgu da yasak — ikisi de).

---

## 6. IMPACT

**Etki analizi (her bulgu için):**

| Boyut | Soru | Örnek (BypassAuth) |
|-------|------|---------------------|
| Veri | ne kaybolur? | tüm kullanıcı + vault verisi |
| Yetki | kim ne kazanır? | saldırgan = admin |
| Süreklilik | hizmet durur mu? | evet (tam yetkisiz erişim) |
| Itibar/finans | regülasyon/fatura? | KVKK ihlali riski |

**Olasılık × Etki matrisi:**

| | Etki Düşük | Etki Yüksek |
|---|---|---|
| **Olasılık Yüksek** | MEDIUM | HIGH |
| **Olasılık Düşük** | LOW | HIGH (kritik varlık ise CRITICAL) |

- Olasılık: ön koşul sayısı, kullanıcı erişimi, otomasyon kolaylığı ile gerekçelendirilir —
  "bence yüksek" yeterli GEREKÇE DEĞİL.

---

## 7. MITIGATION

**Kural:** Geçici yama değil **kök neden düzeltmesi**; her düzeltme savunma katmanına
(yalnız tek katman değil — defense in depth) bağlanır.

```text
Root Cause → [Layer 1 fix] + [Layer 2 detective control] + [Verification]
```

**Örnek — BypassAuth:**

```text
Root Cause: test bypass mekanizmasının prod config'ine sızması
L1 (önleme): BypassAuth, ortam flag'ine bağlı — prod'da register bile edilmez
             (kod: if (!env('TESTING')) unregister BypassAuth)  ← kanıt satırı
L2 (tespit): ?_bypass=1 içeren istekler CRITICAL audit event'i (alert)
L3 (doğrulama): prod config diff test — CI'da "bypass aktifse fail"
```

**Mitigation kalıpları (proje bağlamı):**
- Access control: policy'yi **kaynağa** koy (her controller'da tek tek değil —
  merkezi Permission middleware), deny-by-default.
- Injection: prepared statement (ADR-002) + output encoding (textContent).
- CSRF: çift gönderim token + SameSite=strict (ADR-010/011).
- Rate limit: login/email endpoint'lerinde kullanıcı-bazlı ek kova (IP değil).
- Logging: REDACTED politikası (`.ai/AGENTS.md` §13.9) — token asla log'a.
- Vault: anahtar rotation prosedürü + audit.

**CoreMusic guardrail bağlantısı:** Her düzeltme hangi guardrail/ADR ile hizalanır
(köprü tablosu: bulgu → guardrail § → ADR).

---

## 8. VERIFICATION

**Düzeltme sonrası doğrulama (her bulgu için zorunlu):**

1. **Re-test:** aynı PoC senaryosu → artık BAŞARISIZ olmalı (negatif test).
2. **Regresyon:** ilgili katman testleri geçmeli (PHPUnit ≥%80 o modülde).
3. **Yan etki:** düzeltme başka endpoint'i kırmadı mı (contract test).
4. **Izleme:** detective control (alert/log) gerçekten tetikleniyor mu (sahte olay testi).
5. **Kod incelemesi:** diff 2. göz (PR review) — tek kişi kapatamaz (CRITICAL/HIGH).

**Doğrulama kanıtı formatı:**

```text
V-{NN}: {bulgu id} → test adı → beklenen sonuç → GERÇEK sonuç (pass/fail)
```

fail = bulgu AÇIK kalır (kapatılamaz); "fixlendi ama test yok" = kabul EDİLMEZ.

---

## 9. ACCEPTANCE

**Denetim kabul kriterleri (checkbox):**

- [ ] OWASP Top 10:2025'in **10/10'ı** işlendi (bulgu veya "no finding + kanıt")
- [ ] Her bulgu Format §5'e uymakta (Severity+Evidence+RootCause+Impact+OWASP)
- [ ] Kanıtsız bulgu: 0 (uydurma CVE/severity: 0)
- [ ] CRITICAL: 0 açık (varsa release BLOCKER)
- [ ] HIGH: her biri için düzeltme planı + tarih
- [ ] §4 A07 kontrolü: `?_bypass=1` prod'da ETKİNSİZ (kanıt: config/kod satırı)
- [ ] §3 pipeline sırası korunmuş (Değiştirilemez — §6 ihlali = bulgu)
- [ ] Log'lar token/REDACTED uyumlu (§2 A09)
- [ ] audit trail'de auth olayları kayıtlı (login success/fail, authz red)
- [ ] Tüm bulgular varlık tablosuyla eşleşiyor (kapsamsız bulgu yok)
- [ ] [VERIFY REQUIRED] listesi açık (doğrulanamayan sürüm/CVE'ler)

**Red koşulları:** CRITICAL açık; kanıtsız CRITICAL iddiası; OWASP maddesinin
sessizce atlanması.

---

## 10. OUTPUT FORMAT

**Rapor yapısı:**

```markdown
# CoreMusic Security Audit Raporu — {tarih}
## 1. Yönetici Özeti (≤10 satır: bulgu sayıları, release kararı)
## 2. Denetim Bilgisi (kapsam, derinlik, yapılan taramalar)
## 3. Bulgu Tablosu (id, başlık, severity, owasp, durum)
## 4. Bulgu Detayları (her biri §5 formatında)
## 5. OWASP 10/10 Kapsam Tablosu (bulgu veya no-finding+kanıt)
## 6. Doğrulama Planı (§8)
## 7. [VERIFY REQUIRED] / Açık Sorular
## 8. Ekler: tarama komutları + çıktıları (REDACTED)
```

- Yönetici özeti teknik DETAY İÇERMEZ (süre, sayı, karar).
- Metrik: `toplam bulgu / CRITICAL / HIGH / MEDIUM / LOW / no-finding`.

---

## 11. EDGE CASES

| # | Kenar durum | Tepki |
|---|-------------|-------|
| 1 | Bulgu doğrulanamıyor (salt okuma erişimi yok) | `INCONCLUSIVE` + kanıt isteği — açık bulgu yazma |
| 2 | Aynı bulgu 2 yerde | tek bulgu, 2 evidence satırı (şişirme yasak) |
| 3 | Test ortamı ile prod davranışı farklı | bulgu ortam etiketi: `[prod]`/`[test]`/`[?]` |
| 4 | BypassAuth test ortamında aktif (Soft Constraint #4) | beklenen davranış — bulgu DEĞİL; prod kanıtı şart |
| 5 | CVE sürümü doğrulanamıyor | `[VERIFY REQUIRED]` — iddia yok |
| 6 | Log'da token bulundu | CRITICAL + [REDACTED] derhal + tarihçe (ne zamandan beri) |
| 7 | Denetim sırasında kritik açık keşfi | canlı exploit YOK (etik) → kanıt: kod satırı + PoC ANLATIMI |
| 8 | Bulgu → düzeltme → re-test aynı oturumda | yine de bağımsız PoC tekrarı gerekir |
| 9 | Kapsam tartışması (bu servis mi?) | kapsam dışı + gerekçe — sessizce atma |
| 10 | Uydurma "geçti" baskısı (release) | §9 red koşulları bağlayıcı — rapor KRİTİK ise DUR + bildir |

---

## 12. VALIDATION (denetim kendini denetimi)

- [ ] §1 kapsamı daraltılmış mı (kapsam genişletme onaysız YASAK)?
- [ ] Her bulgu kanıtlı mı (file:line / HTTP)?
- [ ] Severity tanımına sadık mı (duygu ile değil tabloyla)?
- [ ] Root cause ≠ symptom yazıldı mı?
- [ ] OWASP 10/10 tablosu eksiksiz mi?
- [ ] [VERIFY REQUIRED] doğru yerlerde mi (uydurma yok)?
- [ ] Çıktı formatı §10'a uygun mu?
- [ ] **PASS 3 satır sayımı: bu prompt ≥ 500 satır (§0) ✓**

---

## 13. ZERO-HALLUCINATION

- Sürüm/CVE eşleşmesi doğrulanmadan YAZILMAZ.
- Mevcut kod davranışı (ör. RateLimiter anahtarı) dosya okunmadan iddia edilmez.
- "Test edildi, geçti" iddiası: test KAYDI yoksa yazılmaz.
- BypassAuth'ın prod durumu: config kanıtı olmadan "kapalı" DİYİLEMEZ — kontrol maddesi.

---

## 14. WORKFLOW (denetim sırası)

```text
1. KAPSAM    → §1'i kullanıcı ile kilitle (onaysız genişleme yok)
2. ENVANTER  → endpoint/middleware/config listesi (gerçek dosyalardan)
3. STATİK    → grep taramaları: unserialize|SELECT \*|_csrf_token|innerHTML|bypass
4. TASARIM   → §3 pipeline + tehdit modeli (§4)
5. DİNAMİK   → §4 senaryolarının manuel PoC listesi (etik sınırlar içinde)
6. BULGU     → §5 formatı; kanıt EKLE; no-finding satırlarını da yaz
7. ETKİ      → §6 matrisi
8. MITIGASYON→ §7 kök neden + katmanlar
9. DOĞRULAMA → §8 re-test planı
10. RAPOR    → §10 formatı + §9 kabul checklist'i
```

---

## 15. CONSTRAINTS

1. **Canlı exploit / yıkıcı test YASAK** — etik denetim (kod+PoC anlatımı yeterli).
2. **Sırlar asla** rapora/vault'a kopyalanmaz → `[REDACTED]`.
3. Pipeline sırası **immutable** (§6) — bulgu olarak yazılır, DÜZELTMESİ emergency ADR ister.
4. Kanıtsız bulgu/uydurma CVE yasak (§5).
5. Rapor `log.md`'ye değil, `.ai/reports/`'a; log'a yalnız 1 satır özet (append-only).
6. Vault çelişkisi: vault ↔ disk → disk kazanır + işaretlenir.

---

## 16. DECISION RULES

- Öncelik: User intent → kapsam → Correctness of evidence → Severity tablosu →
  Maintainability of fix → Simplicity.
- Çelişki (bulgu vs bulgu): kanıt gücü kazanır; eşitse severity yüksek olanın lehine
  **araştırma** açılır (oto-kapatma yasak).
- "Bulgu yok" kararı: yalnız §5 no-finding + kanıt satırıyla verilir.
- Release kararı: §9 red koşulları → BLOCKER; aksi yalnız Tech Lead + Vault Steward onayı.

---

## 17. ERROR HANDLING (denetim süreci hataları)

| Durum | Tepki |
|-------|-------|
| Dosya okunamadı (izin) | `UNKNOWN` + erişim talebi — tahmin yok |
| Tarama komutu başarısız | komut + hata raporlanır; "temiz" sanılmaz |
| Çelişki (2 kaynak farklı) | CONFLICT + disk/kanıt lehine çözüm + işaretleme |
| Scope creep isteği | DUR + onay (Guardrail #1) |

---

## 18. OUTPUT FORMAT (özet — §10)

Markdown rapor; bulgular tablo + detay; OWASP 10/10; ek olarak tarama komutları.
Uzunluk: rapor BOYU sabit DEĞİL (içerik belirler); **bu denetim PROMPTU §0 kapsamında
≥500 satırdır.**

---

## 19. ACCEPTANCE CRITERIA (kısa tekrar — §9'a bağlanır)

- [ ] 10/10 OWASP işlendi
- [ ] Kanıtsız bulgu 0 · uydurma CVE 0
- [ ] CRITICAL 0 açık (release blocker çalışır)
- [ ] BypassAuth prod kanıtı mevcut
- [ ] Re-test planı her HIGH/CRITICAL için var

---

## 20. VALIDATION (prompt teslim öncesi)

- [ ] §5 formatı tüm bulgularda
- [ ] §6 matrisi dolduruldu
- [ ] [VERIFY REQUIRED] işaretleri doğru
- [ ] **PASS 3: bu prompt ≥ 500 satır (§0) ✓**

---

## 21. ZERO-HALLUCINATION (tekrar — bağlayıcı)

Sürüm/CVE/test sonucu doğrulanmadan yazılmaz. Bilinmeyen = `[UNKNOWN]` / `[VERIFY REQUIRED]`.

---

## 22. EDGE CASES (kısa ek)

- Aynı açık iki serviste: 1 bulgu × 2 evidence (şişirme yok, kapsam net).
- WAF/IPS varlığı bilinmiyor: `[VERIFY REQUIRED]` (kontrolü etkileyecek varsayım kurulmaz).

---

## 23. DOCUMENTATION

- Rapor `.ai/reports/security-audit-YYYY-MM-DD.md` (şablon: `documentation/security-audit-template.md`).
- `log.md`: 1 satır (append-only): bulgu sayıları + karar.
- ADR gerekirse (yeni kalıcı kural): `.ai/.decisions/` — mevcut ADR'leri tekrarlama.

---

## 24. EXAMPLES / SCENARIOS

**Örnek tam bulgu (format gösterimi):**

```text
[F-01] BypassAuth prod'da etkin görünüyor
  Severity : CRITICAL
  Evidence : shared/src/Middleware/PipelineConfig.php:88  (env prod dalında
             BypassAuth register satırı)  +  HTTP GET /api/v1/me?_bypass=1
             → 200 (authsuz) — PoC anlatımı
  RootCause: ortam bayrağı ≠ middleware register mantığı (test helper prod'a taşındı)
  Impact    : varlık: Session/JWT + tüm RBAC → tam auth bypass (§2 KRİTİK)
  OWASP     : A07 (+ A04 insecure design)
```

**Örnek no-finding:**

```text
[F-nf-06] A03 — SQL Injection
  No finding. Kanıt: grep -rn "query(\\|exec(" shared/src/Database/ → 0 sonuç;
  tüm sorgular PDO prepared (ADR-002 örneği: TrackRepository.php:41-57).
```

**Örnek red (kanıt yetersiz):**

```text
"RateLimit XFF spoof edilebilir" → Kanıt YOK (proxy config okunmadı) →
INCONCLUSIVE + madde §VALIDATION listesine eklenir (açık bulgu sayılmaz).
```

**Örnek tam senaryo — IDOR (A01) denetim akışı:**

```text
ENVANTER: GET /api/v1/playlists/{id} (Auth #8 + Permission #9 üzerinden)
TASARIM SORUSU: Permission, kaynak SAHİPLİĞİ kontrol ediyor mu, yoksa yalnız
                "giriş yapmış mı" mı bakıyor?
STATİK KANIT:  PlaylistController.php:XX  → servis çağrısı; serviste
               userId karşılaştırması var mı? (varsa satır no ile; yoksa KANIT YOK)
DİNAMİK PoC (anlatım):  kullanıcı A token'ı ile /playlists/{B'nin id'si} →
               beklenen: 403/404 (tutarlı) · gözlenen: ??? (denetim ortamında doldurulur)
IMPACT:        varlık: coremusic_social/user playlists → horiz. veri erişimi
MITIGATION:    kaynak-merkezli policy (Permission'da ownership) + deny-by-default
VERIFICATION:  PoC tekrarı → 404; regresyon testi: tests/Security/PlaylistIdorTest
```

**Örnek — CSP nonce zinciri (A05) denetim notu:**

```text
Soru: nonce, SecurityHeaders (#4) üretilip SessionManager (#5) session'a mı kaydediliyor?
Kanıt aranacak: PipelineConfig.php + SecurityHeaders.php (nonce üretim satırı) +
                 SessionManager.php (kayıt satırı) + template render (nonce kullanımı)
Risk (kanıtlanırsa): her istekte farklı nonce → inline script çalışmaz (availability)
                     veya tersi: nonce tahminilebilirse XSS bypass (güvenlik)
Bulgu eşiği: yalnız gerçek davranış kodu okununca yazılır; "olabilir" DEĞİL.
```

---

## DISCOVERY SUMMARY (şablonun üretim özeti)

- **Confirmed:** kapsam (authn/authz + API surface + pipeline §6), format = SECURITY AUDIT
  (Format 4), kanıt zorunluluğu, OWASP Top 10:2025, §0 min 500 satır.
- **Confirmed architecture:** 10-step middleware (immutable), ADR-008/010/011/012/013/022.
- **Remaining [VERIFY REQUIRED]:** BypassAuth prod durumu, RateLimiter anahtarlama,
  komponent sürümleri/CVE eşleşmesi, WebSocket upgrade auth sırası.

---

**Authority**: Bayram Ali / Vault Steward
**Generated by**: prompt-maker v1.1.0 (§0 min-500 contract + Format 4 SECURITY AUDIT)
**Last Updated**: 2026-10-07
**Mode**: Red Team · Human Mode · Truth Mode
