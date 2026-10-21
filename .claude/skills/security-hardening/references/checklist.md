# security-hardening — Kalite Kapısı (Quality Gate)

> **Kullanım:** Güvenlik değişikliği tamamlandığında bu dosyayı çalıştır.
> Her madde `PASS` / `BLOCK` / `N/A` ile işaretlenir. **Tek bir `BLOCK` = teslim YOK.**
> Çıktı biçimi: `GATE: PASS|BLOCK` + maddeler + kanıt (dosya:satır / ADR no / URL).
> Kalıp: `skill-maker/references/checklist.md` (N1-N10) + bu skill'in kapıları.

**Skill:** security-hardening · **Format:** Claude Skill v3.0 (N1-N10) · **Updated:** 2026-10-21

---

## A. SKILL.md Biçim Kapıları (N1-N10 — teslimde bir kez)

| # | Madde | Kanıt |
|---|-------|-------|
| A1 (N1) | Frontmatter yalnız `name/description/license/metadata` | head |
| A2 (N2) | description "Use when…" · ≤1024 · Türkçe tetikleyiciler ('güvenlik değişikliği', 'CSRF ekle', 'CSP ayarı', 'auth kodu') | head |
| A3 (N3) | SKILL.md 500–2000 satır · dizin toplamı ≥1000 (`(Get-Content).Count`) | ölçüm |
| A4 (N4) | Her `references/` dosyası §4 tablosundan linkli — **orphan=0** | link taraması |
| A5 (N5) | `examples/` varsa ≥1 tam döngü örnek; yoksa N/A | dosya |
| A6 (N6) | `name` = klasör 1:1 (`security-hardening`) | head |
| A7 (N8) | `CLAUDE.md` sidecar YOK | dosya yok |
| A8 (N9) | Footer = `metadata.version` + `Updated: 2026-10-21` senkron | tail |
| A9 (N10) | Kök `changelog[]` yok · sürüm geçmişi `references/changelog.md` | head |
| A10 | UTF-8 BOM yok (ilk 3 bayt `EF BB BF` değil) | bayt okuma |

## B. Güvenlik Değişikliği Kapıları (her görevde — SKILL.md akışı)

### B1 — Kripto (§12 + `references/crypto-standards.md`)

```text
[ ] AES-256-GCM / ChaCha20-Poly1305 — ECB/CBC/DES/3DES/RC4 YOK
[ ] MD5 / SHA-1 YOK (parola hash'i Argon2id; eski hash'ler migration planıyla)
[ ] Nonce/IV: rastgele + tekrarsız (GCM'de aynı anahtarla asla yeniden kullanılmaz)
[ ] Anahtar kaynak kodunda/.md/.log/.json'da DEĞİL (`.env` / env substitution, REDACTED)
[ ] Rastgelelik: `random_bytes()` (CSPRNG) — `rand()`/`mt_rand()` YOK
```

### B2 — Auth & session (§11)

```text
[ ] Parola: Argon2id + unique salt · doğrulama sabit zamanda (timing-safe)
[ ] Session: sunucu taraflı ID · cookie'de `Secure; HttpOnly; SameSite=Lax|Strict`
[ ] Session fixation: login sonrası ID yenilenir · logout'ta server-side yok edilir
[ ] Roll/biçim doğrulama her istekte (auth middleware + rol) — yalnız URL'de değil
[ ] Rate limit / lockout: brute-force girişimlerinde uygulanıyor (fail-safe)
```

### B3 — CSRF & XSS (§8 + `references/forbidden-patterns.md`)

```text
[ ] CSRF token: double-submit veya server-side; formda + header'da; expiry var
[ ] State-changing her istekte CSRF doğrulaması (GET'de yan etki YOK)
[ ] `innerHTML` + kullanıcı verisi YOK → `textContent` / `createElement`
[ ] `unserialize(user_input)` YOK · `eval` / `new Function` YOK
[ ] Çıkış kaçışı (escape) her çıktı bağlamında (HTML/JS/URL/attr)
```

### B4 — CSP & header'lar (§9 — ADR-012, frozen)

```text
[ ] CSP: `default-src 'self'` temelinde; nonce veya hash — `unsafe-inline` genel değil
[ ] `script-src` genişletmesi dosya+sırada (CDN: SRI + spesifik alan adı, wildcard YOK)
[ ] fail-open YOK — CSP/header üretim hattında sessizce devre dışı kalmaz
[ ] Yanıt başlıkları: `X-Content-Type-Options: nosniff` · `Referrer-Policy` · frame kısıtı
```

### B5 — Veri & hata (PDO + log)

```text
[ ] Parametreli sorgu zorunlu — string concat SQL YOK · `SELECT *` YOK
[ ] Hata yanıtında stack trace / SQL / d sürümü sızdırılmıyor (genel hata + log)
[ ] Log'a secret/parola/token/cookie YOK (log sızıntısı — `references/forbidden-patterns.md`)
[ ] Doğrulama: hata = fail (fail-safe), sessizce geçiş değil
```

## C — Middleware Sırası (§6 frozen — `references/middleware-security-order.md`)

```text
[ ] Sıra bozulmadı: Security headers → CORS → RequestId → Log → Auth → CSRF → RateLimit → Route
[ ] Yeni middleware eklendiyse kayıt: SKILL.md §6 tablosu + pasif-kod/uygulanmayan kayıt tuzağı yok
[ ] Auth, route/controller'dan ÖNCE — controller içinde kontrol tekrarı eklemeden
```

## D — Doğrulama & rapor (§5-§16)

```text
[ ] Yasaklı kalıp taraması çalıştırıldı (`references/forbidden-patterns.md`) → 0 isabet
[ ] Her iddia dosya+satır / ADR / kaynak ile kanıtlı (Guardrail #3)
[ ] Doğrulanamayan iddia `VERIFICATION REQUIRED` etiketli
[ ] OWASP Top 10:2025 eşlemesi (§7) ilgili maddeleri kapatıldı
[ ] Kanıtsız "güvenli" yazımı YOK — test/ölçüm çıktısı var
```

## E — Yasaklar (bu skill'in kendisi)

```text
[ ] `git commit` ATILMADI (orkestratöre ait)
[ ] `.ai/` vault'a YAZMA YOK (salt okunur; aramada hidden: true)
[ ] Frozen ADR metni kopyalanmadı/değiştirilmedi
[ ] Secret değeri hiçbir `.md`/`.log`/`.json`'a yazılmadı (REDACTED)
```

---

## Çıktı şablonu

```
GATE: PASS|BLOCK
- A: 10/10 PASS (N3 ölçüm: SKILL=645, toplam=2339)
- B1: PASS (5/5) · B2: BLOCK — B2.3: session fixation yok, src/auth.php:88
- C: PASS (3/3) · D: PASS · E: PASS
Kanıt: dosya:satır
```
