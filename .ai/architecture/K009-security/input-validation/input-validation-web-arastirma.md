---
node_id: K009-security/input-validation/input-validation-web-arastirma
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/input-validation/input-validation-anayasa
evidence: "8 benzersiz URL (HTTP 200, 2026-10-15) + §2 kod xref"
validation: "≥8 URL · kök havuz (security-web-arastirma) ve index §5 4 URL ile tekrar 0 · SSRF CS kullanılmadı"
open_questions: "CWE-20 tam katalogu okunmadı (yalnız iddia satırı) · rfc7578 dosya sınırı kuralı kodda kanıtlanmadı ⚠️"
group: kanit
---

# K009 · input-validation · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Havuz: OWASP(2) + CWE(1) + IETF(2) + PHP(3) = 8 URL.
> Tekrar yasağı: bu URL'ler kök `security-web-arastirma.md` havuzunda ve `index.md` §5'teki
> 4 URL'de KULLANILMAZ; SSRF Prevention CS kök havuza tahsisli olduğundan bu dosyada yok.

## §1 KAYNAK TABLOSU (8 URL — 2026-10-15 HTTP 200)

| İddia | URL | Tarih: 2026-10-15 | Durum |
|---|---|---|---|
| Girdi doğrulama OWASP kuralı: whitelist zorunlu, blacklist tek başına yetersiz, doğrulama sunucuda yapılır | https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html | Tarih: 2026-10-15 | HTTP 200 |
| XSS önleme OWASP kuralı: çıktının bağlamına göre kaçış (contextual output encoding) | https://cheatsheetseries.owasp.org/cheatsheets/XSS_Prevention_Cheat_Sheet.html | Tarih: 2026-10-15 | HTTP 200 |
| CWE-20: Input Validation — girdinin varlık/düzlem/denetime uygunluk denetimi | https://cwe.mitre.org/data/definitions/20.html | Tarih: 2026-10-15 | HTTP 200 |
| RFC 3986 URI'nin bileşenlerini (scheme/host/path/query) ve normalize kurallarını tanımlar | https://www.rfc-editor.org/rfc/rfc3986.html | Tarih: 2026-10-15 | HTTP 200 |
| filter_var: tek çağrıda filtreleme/doğrulama; FILTER_VALIDATE_* false döner | https://www.php.net/manual/en/function.filter-var.php | Tarih: 2026-10-15 | HTTP 200 |
| filter_input: süperglobal girdiyi filtreleme/doğrulama ile okur (ham `$_GET`/`$_POST` yerine) | https://www.php.net/manual/en/function.filter-input.php | Tarih: 2026-10-15 | HTTP 200 |
| preg_match: desen eşler; 1 eşleşme, 0 eşleşme, false (hata) üç sonucu ayırır | https://www.php.net/manual/en/function.preg-match.php | Tarih: 2026-10-15 | HTTP 200 |
| RFC 7578 multipart/form-data: dosya alanı adı ve sınır (boundary) kuralları | https://www.rfc-editor.org/rfc/rfc7578.html | Tarih: 2026-10-15 | HTTP 200 |

## §2 KAYNAK — KOD X-REF

| Kaynak (§1) | Kural | Kod Kanıtı (Dosya·Satır) | Eşleşme |
|---|---|---|---|
| Input Validation CS | whitelist zorunlu | `ReturnUrlPolicy.php:7-15,177-181` (7 host + `str_ends_with`) · `Validator.php:84-86` (`in` enum) | ✅ EŞLEŞİYOR |
| Input Validation CS | doğrulama sunucuda | `RequestValidationMiddleware.php:46-96` · `ValidationMiddleware.php:17-83` | ✅ EŞLEŞİYOR |
| Input Validation CS | tip/uzunluk/format katmanları | `Validator.php:126-139,115-123,78-82` | ✅ EŞLEŞİYOR |
| XSS Prevention CS | bağlama göre çıktı kaçışı | — (kaçış katmanı kapsamı bu çocukta OKUNMADI) | ⚠️ VERIFICATION REQUIRED (anayasa U-3) |
| CWE-20 | girdi denetimi sınıflandırması | anayasa §5 katman matrisi | ✅ (kavramsal eşleşme; CWE tam metni okunmadı → kısmi) |
| RFC 3986 | `scheme/host/path/query` ayrıştırma | `ReturnUrlPolicy.php:76-86` (`parse_url` raw+decode) · `RequestNormalizer.php:49` (`FILTER_SANITIZE_URL`) | ⚠️ Kısmi — tam URI normalize karşılaştırması yok (anayasa U-7) |
| filter_var | `FILTER_VALIDATE_EMAIL` | `ValidationMiddleware.php:59` · `Validator.php:143` | ✅ EŞLEŞİYOR |
| filter_var | `FILTER_VALIDATE_URL` | `ReturnUrlPolicy.php:157` | ✅ EŞLEŞİYOR |
| filter_input | süperglobal filtreli okuma | — (repo `$_GET`/`$_POST`/`php://input` okuyor: `RequestValidationMiddleware.php:81,151,154`) | ⚠️ KULLANILMIYOR — doğrulama ayrı katmanda |
| preg_match | 1/0/false ayrımı | `Validator.php:79` (`@preg_match(...) !== 1`) | ⚠️ Kısmi — `false` (hata) `0` gibi ele alınıyor, `@` ile uyarı yutuluyor (anayasa U-6) |
| preg_match | desenin kendisi de girdidir (ReDoS) | `routes.php:73` (`/^[a-zA-Z0-9_]{3,30}$/` — lineer, ReDoS yok) | ✅ (bu desen için) · genel ReDoS taraması ⚠️ KANIT YOK (U-5) |
| RFC 7578 | multipart dosya alanı | — (dosya yükleme yüzeyi bu çocukta OKUNMADI) | ⚠️ VERIFICATION REQUIRED |

## §3 HAVUZ TESLİM KAYDI (TEKRAR YASAĞI)

| Havuz | Sahip | Bu dosyada kullanılan URL |
|---|---|---|
| Kök `security-web-arastirma.md` §1-§2/§17 (42 URL; SSRF CS §17/2. satır dahil) | kök dosya | **0** — tekrar yok |
| `input-validation/index.md` §5 (SQL_Injection CS · Query_Parameterization CS · CWE-89 · WSTG proje sayfası) | index | **0** — tekrar yok |
| `authentication-web-arastirma.md` (rfc-editor 2 + php.net 5 + MDN 2) | kardeş çocuk | **0** — php.net havuzunda filter-var/filter-input/preg-match YOK (orada password_*/random_int/hash_hmac var) |

| Tekrar Uyarısı | |
|---|---|
| Bu 8 URL bu dosyanın havuzudur | kök ve kardeş dosyalarda yeniden yazılmaz |
| SSRF Prevention CS | kök havuza tahsisli (`security-web-arastirma.md:318`) → bu dosyada **kullanılmaz**, yalnız anayasa §12'de ⚠️ olarak anılır |
| Yeni URL doğrulanırsa | HTTP 200 kontrolü + kendi havuzuna ekleme |

## §4 DOĞRULANAMAYANLAR (BU ÇOCUK)

| İddia | Durum |
|---|---|
| XSS bağlam kaçışı katmanı tam uygulanmış | ⚠️ VERIFICATION REQUIRED (anayasa U-3 — katman okunmadı) |
| Tüm rotalar tek şemadan doğrulanıyor | ⚠️ VERIFICATION REQUIRED (anayasa U-1) |
| `required_fields` üreten production route var | ⚠️ KANIT YOK (grep 2026-10-15: yalnız test + kernel + middleware — anayasa U-2) |
| SSRF outbound hedefi host/şema listesinden geçiyor | ⚠️ VERIFICATION REQUIRED (`shared/src` SSRF grep 0 — anayasa U-7) |
| Takvim (calendar) doğrulaması | ⚠️ KANIT YOK (anayasa U-4) |
| ReDoS sınır taraması | ⚠️ KANIT YOK (anayasa U-5) |
| RFC 7578 dosya sınırı/kuralı uygulanıyor | UNKNOWN (dosya yükleme yüzeyi okunmadı) |
| `filter_input` kullanımı | KULLANILMIYOR (kod `$_GET`/`php://input` okuyor; doğrulama ayrı katmanda — §2) |

## §5 ÖNCELİK (BU ÇOCUK)

| Öncelik | Kaynak | Kullanım |
|---|---|---|
| 1 | OWASP Input Validation CS | whitelist/sunucu-tarafı kuralları (anayasa §4/§6) |
| 2 | CWE-20 | kavram sınıflandırması (anayasa §3) |
| 3 | IETF RFC (3986/7578) | URI/multipart sözleşmesi (surucu §9) |
| 4 | PHP Manual | fonksiyon davranışı kanıtı (`filter_var`, `preg_match`) |

## §6 TERİMLER — KAYNAK BAĞLANTILI (anayasa §3'e ek)

| Terim | İlgili kaynak (§1) | Bu repo'daki karşılığı |
|---|---|---|
| Allowlist doğrulama | Input Validation CS | `ReturnUrlPolicy::ALLOWED_HOSTS` (`:7-15`) · `in`/`enum` |
| Contextual output encoding | XSS Prevention CS | ⚠️ U-3 — katman okunmadı |
| Input validation (CWE-20) | CWE-20 | `Validator::validate` (`:35-57`) |
| URI normalize | RFC 3986 | `parse_url` + `urldecode` (`ReturnUrlPolicy.php:58-86`) — tam normalize YOK |
| Filter var/validate | PHP manual | `FILTER_VALIDATE_EMAIL` (`:59`, `:143`) · `FILTER_VALIDATE_URL` (`:157`) |
| Regex eşleşme sonucu | PHP manual | `preg_match` 1/0/false → `Validator.php:79` `!== 1` (false≠0 ayrımı kayıp) |
| multipart/form-data | RFC 7578 | ⚠️ dosya yükleme yüzeyi okunmadı (kapsam K-1) |

## §7 İDDİA — DOĞRULAMA DURUMU ÖZETİ

| # | İddia | Kaynak | Durum |
|---|---|---|---|
| 1 | Whitelist zorunlu, blacklist yetersiz | Input Validation CS | ✅ kod kanıtlı (AD-01/AD-02) |
| 2 | Doğrulama sunucu tarafında | Input Validation CS | ✅ AD-01..AD-08 (adım 3) |
| 3 | XSS bağlam kaçışı | XSS Prevention CS | ⚠️ U-3 |
| 4 | URI bileşen ayrıştırma standardı | RFC 3986 | ⚠️ kısmi (`parse_url` ≠ RFC normalize) |
| 5 | `filter_var` ile format denetimi | PHP manual | ✅ AD-03 |
| 6 | `filter_input` filtreli okuma | PHP manual | ❌ kullanılmıyor (§2) |
| 7 | `preg_match` false ayrımı | PHP manual | ⚠️ AD-16 (`@` + `!== 1`) |
| 8 | multipart dosya alanı kuralları | RFC 7578 | UNKNOWN (kapsam K-1) |

## §7.1 TEKRAR KONTROL KAYDI (2026-10-15 grep)

| Kontrol | Kapsam | Sonuç |
|---|---|---|
| Bu dosyanın 8 URL'si kök `security-web-arastirma.md` içinde geçiyor mu? | 42 kök satır (grep 2026-10-15) | 0 tekrar |
| Bu 8 URL `input-validation/index.md` §5 içinde geçiyor mu? | index 4 URL | 0 tekrar (index'teki 4 URL: SQL_Injection CS · Query_Parameterization CS · CWE-89 · WSTG proje sayfası) |
| Bu 8 URL kardeş `authentication-web-arastirma.md` içinde geçiyor mu? | 9 kardeş URL | 0 tekrar |
| SSRF Prevention CS bu dosyada mı? | kök `security-web-arastirma.md:318` | YOK (tahsisli — §3) |
| Kod içindeki URL'ler (test fixture: `evil.com`, `home.coremusic.net@evil.com`, ...) | `input-validation-ornekler.md`/`surucu` | **araştırma URL'si değildir** — havuz sayımına girmez (test girdisi) |

## §8 KAYNAK GÜVENİLİRLİK NOTU

| Kaynak sınıfı | Neden | Ağırlık |
|---|---|---|
| OWASP CheatSheet Series | topluluk kabulü, sürüm takibi açık | birincil (kural) |
| CWE (MITRE) | zayıflık sınıflandırması, kavramsal | ikincil (terminoloji) |
| IETF RFC | standart metni, normatif dil | birincil (sözleşme) |
| PHP Manual | dil davranışı kanıtı | birincil (fonksiyon) |

| Uyarı | |
|---|---|
| Kaynak ≠ uygulama kanıtı | §2'deki "⚠️/❌" satırları kod tarafının durumunu taşır; URL 200 olmak kodun uygun olduğu anlamına gelmez |
| Tarih disiplini | tüm URL'ler tek ölçüm tarihi: 2026-10-15 |

## §9 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[index]] | üst | bu çocuk künyesi |
| [[input-validation-anayasa]] | üst | §18 Uyum matrisi bu dosyanın §1-§7'sine referans verir |
| [[input-validation-kapsam]] | yan | §4 doğrulanmayanlar ↔ kapsam §8 kararları |
| [[input-validation-dogrulama]] | yan | AD-xx satırları bu dosyanın kod xref'ini doğrular |
| [[security-web-arastirma]] | havuz | kök URL tahsisleri (SSRF CS dahil) — tekrar yasağı §3 |

## §10 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 8 URL (HTTP 200) + kod xref; havuz çakışması 0 | Vault Steward (subagent) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
