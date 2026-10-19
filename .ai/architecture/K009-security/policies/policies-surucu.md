---
node_id: K009-security/policies/policies-surucu
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on: K009-security/policies/policies-anayasa
evidence: .ai/architecture/K009-security/policies/index.md · shared/src/PageRouter/PageRouterKernel.php:322-341
validation: pending
open_questions: ''
group: mimari
---

# K009 · policies · policies-surucu

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Mimari Sürücü
> Bu belge `policies-anayasa.md`'deki **P-01..P-18** bağlayıcı politikalarını
> (anayasa §4 — bu dosyanın tek numaralandırma kaynağı) **kod yüzeyine** ve
> **test zincirine** bağlar. Politika olmadan kod, kod olmadan test yazılmaz;
> her satır buradaki yönlendirme tablolarından geçer.

## BAĞLANTILAR

- [[policies-anayasa]] — 18 bağlayıcı politika (P-01..P-18, RFC2119) — **SSOT**
- [[index]] — bu dizinin indeksi (§1 bağlayıcı politika listesi)
- [[security-master]] — K009-security kök indeksi
- ADR-010 (csrf) — `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` (accepted) — CSRF stratejisi (P-11..P-13)
- [[../security-testing/index]] — test zinciri hedefi
- [[../audit/index]] — RED bulgularının landığı kanıt katmanı

---

## 1. Sürücü Eserleri

1. **Yönlendirme tablosu**: her politika (P-xx) → kod dosyası (Dosya·Satır) → test dosyası (§2).
2. **Pipeline analizi**: çalışma zamanı sırası = politika sırası (§3).
3. **İhlal akışı**: politikasız/kısmi kod yakalandığında kapı nasıl durur (§6).
4. **Kanıt toplama noktaları**: hangi politika hangi ölçümden beslenir (§7).
5. **Rol dağılımı**: ihlal sınıfına göre karar mercii (§8).

Kural: bir kod satırı bu belgede karşılığı olmayan bir güvenlik davranışı içeriyorsa
**politikasız kod ihlali** (C1) sayılır ve §6 akışına girer.

---

## 2. Politika → Kod → Test Yönlendirme Tablosu

Numaralandırma anayasa §4'ten alınmıştır (P-01..P-04 parola · P-05..P-06 kripto ·
P-07 sır · P-08 katman · P-09 fail-safe · P-10 OWASP · P-11..P-13 CSRF ·
P-14..P-15 CSP/başlık · P-16 log · P-17 rate-limit · P-18 bypass).

| Politika | Konu | Kod yüzeyi (Dosya·Satır) | Test zinciri | Durum |
|---|---|---|---|---|
| P-01 | Parola min 8 | `Password.php:14,28-30` | auth test (§7) | verify-required |
| P-02 | Argon2id | `Password.php:44-48` · `.ai/CLAUDE.md:336` | auth test (§7) | verify-required |
| P-03 | Pepper fail-closed | `Password.php:40-42,56-58` | auth test (§7) | verify-required |
| P-04 | Ham parola yazılmaz | `Password.php:9` · `auth AGENTS.md:63` | `LoggerRedactionTest.php:74` | verify-required |
| P-05 | AES-256-GCM / ChaCha20 | `.ai/CLAUDE.md:336` · `index.md:35` | — (security-audit) | verify-required |
| P-06 | MD5/SHA-1/DES yasak | `security-master.md:49` | — (tarama §7) | verify-required |
| P-07 | Sır `.env` / `.md`.log yasak | `.ai/CLAUDE.md:687,721` · `auth AGENTS.md:61` | `LoggerRedactionTest.php:74-82` | verify-required |
| P-08 | Katman tek yön + K6 bypass yok | `.ai/CLAUDE.md:112-113` · `PageRouterKernel.php:322-341` | `MiddlewarePipelineTest.php` | verify-required |
| P-09 | Fail-closed | `BypassAuthMiddleware.php:29-31,42-43` · `RateLimiterMiddleware.php:55-57` | `BypassAuthMiddlewareFailClosedTest.php` | verify-required |
| P-10 | OWASP %100 | `.ai/AGENTS.md` §16 (571-577) | — (QA gate §7) | verify-required |
| P-11 | `csrf_token` adı | `CsrfMiddleware.php:42` · `.ai/CLAUDE.md:682` | `CsrfMiddlewareTest.php:11,330` | verify-required |
| P-12 | State-changing token zorunlu | `CsrfMiddleware.php:24,49-63` | `CsrfMiddlewareTest.php` | verify-required |
| P-13 | CSRF 3 katman | `ADR-010:104,123-126,180` | kısmi (katman 3 PLANNED) | open |
| P-14 | CSP nonce + sıra | `SecurityHeadersMiddleware.php:54-56` · `.ai/CLAUDE.md:207,719` | `MiddlewarePipelineTest.php:13` | verify-required |
| P-15 | Güvenlik başlıkları | `SecurityHeadersMiddleware.php:27-35,71,73` | header testi YOK | verify-required |
| P-16 | Log `[REDACTED]` | `LoggerRedactionTest.php:74-82,127` | `LoggerRedactionTest.php` | verify-required |
| P-17 | Rate-limit fail-closed | `RateLimiterMiddleware.php:15-16,20-21,55-57,66-67` | — (test yok) | verify-required |
| P-18 | Bypass yasağı | `BypassAuthMiddleware.php:29-31` · `shared/AGENTS.md:85` | `BypassAuthMiddlewareFailClosedTest.php` | verify-required |

**Okuma kuralı**: “Durum” bu modülün kendi doğrulama seviyesidir; anayasadaki
politika durumu ile çakışırsa **anayasa (SSOT) kazanır**.

### 2.1 Alt Politika → Kod Yüzeyi Eşlemesi

Anayasa §5-§11'in alt politikaları (P-xxharf) — sürüşte ayrı satır:

| Alt politika | Konu | Kod yüzeyi | Durum |
|---|---|---|---|
| P-01a/b | min 8 + ValidationException | `Password.php:13-14,28-30` | kod GREEN · test yok |
| P-01c | 8 vs 12+ fark kaydı | `index.md:21,49` | ⚠️ ONAY BEKLİYOR |
| P-02a/b | Argon2id + maliyet | `Password.php:44-48` | kod GREEN · ADR teyidi yok |
| P-03a/b | pepper fail-closed (hash/verify) | `Password.php:40-42,56-58` | kod GREEN · test yok |
| P-04a/b | ham parola saklanmaz + `password=` redaksiyon | `Password.php:8-9` · `LoggerRedactionTest.php:74` | GREEN |
| P-05a/b/c | AES/ChaCha + ADR gerekçe | `.ai/CLAUDE.md:336` · `index.md:35` | RED (tarama/ADR yok) |
| P-06a-d | MD5/SHA-1/DES/ECB yasak | `security-master.md:49` · `encryption-kapsam.md:136` | RED (tarama yok) |
| P-07a-f | hardcoded yasak · .env · log/ADR redaksiyon | `.ai/CLAUDE.md:687,721` · `auth AGENTS.md` §4 · `ADR-010:97` | kısmi (AD-17 RED) |
| P-08a-f | tek yön · K6 · revert · shared yasağı · pipeline kaydı | `.ai/CLAUDE.md:112-113` · `shared/AGENTS.md` §4 · `PageRouterKernel.php:322-341` | GREEN (pipeline) |
| P-09a-f | fail-closed genel + bypass + rate + Origin | `BypassAuth:29-31,41-44` · `RateLimiter:55-57,66-67` · `OriginCheck:39-41` | P-09f OPEN |
| P-10a-e | OWASP %100 · yasaklı kalıp · ad · risk eşleme · 12+ onay | `.ai/AGENTS.md` §16 · `.ai/CLAUDE.md:680-691` | kısmi |
| P-11a | token adı | `CsrfMiddleware.php:35,39,42` · `.ai/CLAUDE.md:682` | GREEN |
| P-12a-d | metot · hash_equals · 403+olay · bypass boş | `CsrfMiddleware.php:22-27,42-44,51-63,15-17` | GREEN |
| P-13a/b | SameSite · Origin/Referer 403 | `ADR-010:124` (katman 1-2) · `ADR-010:180` (katman 3) | 1 GREEN · 2 PLANNED |
| P-14a/b | nonce+strict-dynamic · sıra | `SecurityHeadersMiddleware.php:16-19,54-56` · `MiddlewarePipelineTest.php:13` | kod GREEN · yanıt testi yok |
| P-15a/b | frame/form · Trusted Types/nosniff | `SecurityHeadersMiddleware.php:26-36,71,73` | kod GREEN · yanıt testi yok |

**Sayım**: 50 alt politika (anayasa §5-§11: 9+7+6+6+6+5+11) — eşlemesi olmayan: 0.

### 2.2 Test Bileşeni Boşlukları (RED → §6)

| Politika | Eksik kanıt | Aksiyon |
|---|---|---|
| P-01..P-03 | Parola/pepper roundtrip testi okunmadı | `security-testing` kapsamına yaz (AD) |
| P-05, P-06 | Kripto taraması çalıştırılmadı | tarama çalıştır → AD |
| P-13 katman 3 | Origin/Referer fail-closed PLANNED (ADR-010:180) | ADR-013/010 kapısı |
| P-15 | Başlık varlık testi yok | test yaz |
| P-17 | Rate-limit testi yok | test yaz |

---

## 3. Çalışma Zamanı Sırası = Politika Sırası

Request pipeline'ı politikaların fiziksel sırasıdır
(`PageRouterKernel.php:322-341`; kanıt `MiddlewarePipelineTest.php:13`):

| # | Middleware | Bağlı politika | Not |
|---|---|---|---|
| 1 | `SessionCookieMiddleware` | P-13a (SameSite=Lax + HttpOnly, ADR-010:124) | oturum bayrağı |
| 2 | `CorrelationIdMiddleware` | P-16 (redaksiyonli telemetri) | kimlik |
| 3 | `SecurityHeadersMiddleware` | P-14 (nonce burada üretilir), P-15 | sıra değişmezse CSP bozulur |
| 4 | `LocaleMiddleware` | — | güvenlik dışı |
| 5 | `RateLimiterMiddleware` | P-17, P-09 (auth uçları fail-closed) | brute-force kapısı |
| 6 | `OriginCheckMiddleware` | P-13b / P-09f | ⚠️ kod fail-open (anayasa §9 P-09f) |
| 7 | `CsrfMiddleware` | P-11, P-12 | 403 `csrf_invalid` |
| 8 | `BypassAuthMiddleware` | P-09 (P-09b/c), P-18 | fail-closed |
| 9 | `MethodOverrideMiddleware` | — | POST→GET override; token koruması P-12 kapsamı |
| 10 | `ApiRouterMiddleware` | P-08f (pipeline kaydı) | routing |

Kurallar:

- Sıra **değiştirilemez** (`.ai/CLAUDE.md:207,719`) → ihlal C4 = RED (§5).
- Yeni middleware **MUST** pipeline kaydına eklenir (anayasa P-08f,
  `shared/AGENTS.md:78`) → kayıt yoksa istek o middleware'den **geçmez**
  (sessiz güvenlik boşluğu) → C1 ihlali.
- Auth'a özel pipeline `auth.coremusic.net/include/Middleware/` içinde ayrıdır;
  onun da sırası ADR-010/011/013'e bağlıdır (kapsam sınırı: kapsam.md §2).

---

## 4. Test Zinciri Mantığı

Her politika için zincir üç halkadır:

```
politika (anayasa P-xx, RFC2119: MUST/SHOULD + kaynak + kapı)
  → kod yüzeyi (§2 Dosya·Satır)
    → test (RED/GREEN kanıtı, §2 test sütunu)
      → kanıt (dosya·satır + tarih, policies-dogrulama.md AD-xx)
```

- **Eksik halka** → doğrulama `verify-required` kalır (AD satırı `RED`).
- **Test yok** → politika “kağıt üstünde” kalır; `policies-dogrulama.md` §1'de
  “Beklenen kanıt” sütunu dolu, Durum `RED` ise o satır raporlanır.
- Yeni kod yazarken **önce** §2 tablosuna satır eklenir; tabloda karşılığı
  olmayan güvenlik davranışı üretilmez (politikasız kod yasağı, §6).
- Test çalıştırma bu modülde yapılmaz (kanıt = okuma); “GREEN” = “beklenen
  assertion dosyada mevcut”, koştuğu anlamına gelmez → CI'da güncellenir.

---

## 5. Ihlal Sınıfları (anayasa §12 ile hizalı)

| Sınıf | Karşılık (anayasa §12) | Tanım | Örnek | Aksiyon |
|---|---|---|---|---|
| C1 politikasız kod | RED | Kod davranışı §2'de karşılıksız | yeni endpoint'te P-12 kontrolü yok | §6 akışı, RED |
| C2 politika-kod çelişkisi | DUR | Kod anayasaya aykırı | OriginCheck fail-open vs P-09f | üst karar / ADR |
| C3 test eksikliği | BLOCK | Politika-test bağlantısı kopuk | P-15 başlık testi yok | test yaz → AD |
| C4 sıra ihlali | RED | Pipeline sırası değişti | SecurityHeaders #3 yerinden | revert (P-14b) |
| C5 kanıt sahteciliği | ⚠️/RED | Uydurma Dosya·Satır | var olmayan satır atfı | zero-hallucination ihlali |
| C6 yetki/sınır ihlali | VETO | Güvenlik zafiyeti veya bypass genişletme | P-18 bypass listesine rota ekleme | security-engineer veto |

---

## 6. Politikasız Kod Ihlali Akışı

```text
kod değişikliği önerilir
  → §2 tablosu taranır: karşılık var mı?
      VAR  → politika durumu oku (anayasa P-xx + §12 sınıfı)
                MUST/MUST NOT ihlali   → RED  (revert, iş durur)
                SHOULD ihlali/eksik test→ BLOCK (gerekçe/kanıt istenir)
                Kaynak çelişkisi        → DUR  (üst karar / ADR)
                Uygulanamayan iddia     → ⚠️   (AD satırına düşer)
      YOK  → C1 İHLALİ (RED)
  → rapor: Dosya·Satır + hangi politika eklenecek
  → politika anayasa §4'e eklenir (P-19..) ONAY ile
  → kod tekrar yazılır → test GREEN → policies-dogrulama.md AD-xx'e işlenir
```

**Kapı kuralı**: C1/C4/C5'te üretim kodu commit edilmez (subagent `git commit`
atmaz; karar orkestratördedir). C2'de karar üst mercie çıkar (`⚠️ ONAY BEKLİYOR`).
C6'da security-engineer veto kullanır (anayasa §12 VETO).

**Senaryo A — yeni middleware**: P-08f yoksa kayıt atlanır → C1 → anayasa P-08f
satırı zaten varsa kod §2'deki `PageRouterKernel.php:322-341` yüzeyine yazılır,
`MiddlewarePipelineTest` sırayı doğrular.
**Senaryo B — Origin fail-open**: P-09f “Origin yoksa 403” diyor, kod geçiyor
(anayasa §9 P-09f kaydı) → C2 DUR → ADR-010/013 üzerinden karar bekler
(`policies-dogrulama.md` AD-12 OPEN).

---

## 7. Kanıt Toplama Noktaları

| Politika grubu | Kanıt toplayıcı | Çıktı |
|---|---|---|
| P-01..P-04 (parola) | Password.php + auth test | min/Argon2id/pepper/ham-parola kaydı |
| P-05, P-06 (kripto) | kod taraması (MD5/SHA-1/DES/ECB) | yasaklı çağrılı satır listesi (boşsa GREEN) |
| P-07 (sır) | `.env` okuma kuralı + `.gitignore` | secret envanteri — **değerler yazılmaz** |
| P-09, P-18 (fail-closed/bypass) | BypassAuthMiddlewareFailClosedTest | 401/500 kararı |
| P-10 (OWASP) | `.ai/AGENTS.md` §16 gate | QA raporu |
| P-11..P-13 (CSRF) | CsrfMiddlewareTest + ADR-010 | token adı + 403 + katman durumu |
| P-14, P-15 (CSP/başlık) | gerçek yanıt başlıkları (browser test) | başlık adı/değeri |
| P-16 (log) | LoggerRedactionTest | redaksiyon desenleri |
| P-17 (rate-limit) | RateLimiter testi (yok → RED) | 429 eşiği |

---

## 8. Rol Dagilimi (karar mercii)

| İhlal sınıfı | Karar mercii | Kaynak |
|---|---|---|
| C1/C3/C4 | orkestratör (commit kapısı) + backend-architect | §6 kapı kuralı |
| C2 (çelişki) | Vault Steward / ADR (üst karar) | `.ai/AGENTS.md` §24.4 |
| C5 (uydurma) | Vault Steward — zero-hallucination | `.ai/CLAUDE.md` §3 |
| C6 (zafiyet) | security-engineer — **veto** | anayasa §12 · routing §6 |
| Test/ölçüm | qa-engineer | `.ai/AGENTS.md` §16 |

---

## 9. Politika Yasam Dongusu

| Durum | Anlamı | Geçiş |
|---|---|---|
| `draft` | Politika yazıldı, kanıt bekliyor | kanıt (AD-xx) → verify-required |
| `verify-required` | Kanıt kısmi/RED | test GREEN → doğrulama güncellenir |
| `accepted` | Davranış ADR ile mühürlü | değişiklik yalnız yeni ADR ile |
| (kaldırma) | Politika eskidi | ONAY + anayasa revizyon kaydı |

Bu dosyanın kendi `status: draft`'ı = §2 tablosunun 18/18 eşlemesinin tamam,
kanıt toplamanın (§7) henüz RED ayağının kapanmaması demektir.

---

## 10. Sürücü Kontrol Listesi

- [x] 18 politikanın tamamı §2 tablosunda kod/test karşılığına bağlandı (18/18).
- [x] Numaralandırma anayasa §4 ile birebir (P-01..P-18).
- [x] Pipeline sırası kanıtlandı (`PageRouterKernel.php:322-341` + §3).
- [x] İhlal sınıfları anayasa §12 ile hizalandı (§5) ve akış tanımlandı (§6).
- [ ] Test bileşeni boşlukları (§2.2): P-01..P-03, P-05/06, P-10, P-15, P-17 → RED.
- [ ] C2 çelişki kararı (OriginCheck fail-open, P-09f) → `⚠️ ONAY BEKLİYOR`.

---

## 11. Senaryo Kutuphanesi (adım adım sürüş)

**S1 — Yeni state-changing endpoint açılıyor.**
1. §2 taranır: P-12 “state-changing her istek token ister” → evet, bu endpoint kapsamda.
2. Kod: `CsrfMiddleware` zaten pipeline #7'de → ek kod gerekmez; route token üretmeli.
3. Test: `CsrfMiddlewareTest` tipine yeni vaka (endpoint adı) eklenir.
4. Kanıt: AD satırı (örn. AD-05'e komşu) → `verify-required` → GREEN.

**S2 — Yeni middleware ekleniyor (P-08f).**
1. `PageRouterKernel.php:322-341` listesine eklenir (sıra kararı: P-14b nonce #3'te kalır).
2. `MiddlewarePipelineTest` sırayı doğrular → RED ise C4 = revert.
3. surucu §3 tablosuna satır + politika (P-xx) anayasaya eklenir (ONAY).
4. `shared/AGENTS.md` §4 Zorunlu 2 (config güncelleme) tamamlanır.

**S3 — OriginCheck “boş Origin” olayı (mevcut C2).**
1. Anayasa P-09f: Origin yoksa 403 (ADR-010:179-180). Kod: `OriginCheckMiddleware.php:39-41` geçer.
2. Durum: DUR (§5 C2) — kod revert edilmez, karar üst mercide.
3. Kayıt: dogrulama AD-14 `OPEN` + `⚠️ ONAY BEKLİYOR`.
4. Karar (fail-closed yap) → ADR-010 ile uyum sağlanır → AD-14 GREEN.

**S4 — Parola eşiği sorusu (P-01c).**
1. `index.md:21/49` çelişkiyi kayıtlı tutar (8 vs 12+).
2. Kapı: kapatılmaz — `⚠️ ONAY BEKLİYOR` (anayasa §15 U-1).
3. Karar sonrası: `Password.php:13-14` değişir → P-01a revize → ornekler §3 + AD-09 güncellemesi.

**S5 — Log'a token sızdı (P-16/P-07d).**
1. RED sınıfı (MUST ihlali) → kod revert + olay kaydı (anayasa §12).
2. Kanıt: `LoggerRedactionTest` (AD-07) — desen eklenir.
3. İnceleme: `.ai/AGENTS.md` §17-3 `[REDACTED]` kuralı hatırlatılır.

**S6 — Uydurma satır atfı (C5).**
1. Doğrulanamayan Dosya·Satır → `⚠️ VERIFICATION REQUIRED` + AD satırı silinmez, düzeltilir.
2. Zero-hallucination (`.ai/CLAUDE.md` §3): iddia kanıtlanana kadar sayılmaz.
3. Tekrarlayan ihlalde Vault Steward raporu (§5 C5).

**S7 — Güvenlik başlığı ekleniyor (P-15).**
1. `SecurityHeadersMiddleware.php:26-36` başlık dizisine satır eklenir.
2. Yanıt testi AD-12/AD-13'i günceller (RED → GREEN yalnız koşturla).
3. CSP etkisi varsa P-14 (nonce/directive) anayasadan ayrıca kontrol edilir.

**S8 — Rate-limit eşiği değişiyor (P-17).**
1. `RateLimiterMiddleware` eşiği ADR-013 şartıyla (genel uç fail-open + log, P-09e).
2. Auth ucu davranışının fail-closed kaldığı doğrulanır (P-09d, AD-15).
3. surucu §2 + §7 (RateLimiter satırı) güncellenir; ölçüm olmadan kabul yok.

**S9 — Log/redaksiyon deseni genişliyor (P-16).**
1. `LoggerRedactionTest`'e yeni desen (ör. `bearer=`) eklenir (AD-07).
2. `.ai/AGENTS.md` §17-3 `[REDACTED]` kuralı ihlal edilmez.
3. Ham değer log'a basıldıysa S5 akışı (RED) işletilir.

## 12. Inceleme (Review) Kapisi — 12 soru

1. Değişen kod, §2 tablosunda karşılığı olan bir P-xx mi üretiyor? (yoksa C1)
2. Alt politika düzeyinde de karşılık var mı? (§2.1 — P-xxharf)
3. Pipeline sırasına dokunuldu mu? (P-08f/P-14b — `MiddlewarePipelineTest`)
4. Fail-open davranışı eklendi mi ve ADR gerekçesi var mı? (P-09)
5. Yeni secret/sabit literal eklendi mi? (P-07 — `.env`)
6. Token adı/taşınan metot değişti mi? (P-11/P-12)
7. Başlık/CSP direktifi değişti mi? (P-14/P-15)
8. Log'a veri ekleniyor mu; redaksiyon testi güncellendi mi? (P-16)
9. Bypass kapsamına rota/credential eklendi mi? (P-18 — yalnız ADR)
10. Test GREEN ise “koştu” kanıtı var mı? (dogrulama §2 — CI)
11. `⚠️` işaretli satırlar korunmuş/sayılmış mı? (anayasa §19)
12. Commit karar orkestratörde mi? (subagent commit atmaz)

## 13. Kisa Sozluk (sürücü dili)

| Terim | Tanım (bu belgede) | Karşılık |
|---|---|---|
| Politika | RFC2119 MUST/SHOULD + kaynak + kapı satırı | anayasa §4 |
| Alt politika | P-xxharf kırılımı (50 adet) | anayasa §5-§11 |
| Yüzey | Politikanın uygulandığı dosya·satır | §2 |
| Zincir | Politika → kod → test → kanıt halkaları | §4 |
| Kapı | RED / BLOCK / PASS / DUR | anayasa §12 |
| C1..C6 | İhlal sınıfları | §5 |
| AD-xx | Doğrulama kaydı (4 alan) | dogrulama §1 |
| GREEN | İlk el kanıt dosyada görüldü (koştu değil) | dogrulama §2 |
| OPEN | Karar üst mercide | dogrulama §6 |
| Politikasız kod | §2'de karşılığı olmayan güvenlik davranışı | §6 |
| Sıra mühürü | Pipeline sırasının test ile kilitlenmesi | §3 · P-14b |
| VETO | security-engineer durdurma yetkisi | anayasa §12 |
| Devir | Kapsam dışı işin sahip node'a aktarımı | kapsam §7 · anayasa §14 |
| Yarım değişiklik | Zorunlu bileşenlerin tek turda güncellenmemesi | §3 etki analizi |
| İlk el kanıt | Doğrulayanın dosyayı bizzat okuması | dogrulama §3 |

## 14. Surum Notu

| Tarih | Değişiklik |
|---|---|
| 2026-10-15 | İlk yazım — 18 politika + 50 alt politika eşlemesi, pipeline kanıtı, C1-C6 akışı |
| 2026-10-15 | Numaralandırma anayasa §4 ile hizalandı (P-01..P-18); §2.1 alt politika tablosu eklendi |
| 2026-10-15 | §11 senaryo (S1-S9) + §12 inceleme kapısı + §13 sözlük eklendi |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
