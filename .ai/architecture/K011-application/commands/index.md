---
node_id: K011-application/commands
node_type: subdomain
status: draft
parent_id: K011-application
children:
  - K011-application/commands/commands-kapsam.md
  - K011-application/commands/commands-dogrulama.md
  - K011-application/commands/commands-web-arastirma.md
  - K011-application/commands/commands-ornekler.md
  - K011-application/commands/commands-bilesenler.md
depends_on: []
evidence:
  - "v2 §2.11 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.11 ile birebir karşılaştırıldı — 2026-10-12"
  - "children: 5 dosya — HEPSİ ÜRETİLDİ (2026-10-15), kalan 0"
  - "Gate: 6/6 dosya · index 53 (40–100) · diğerleri 507/509/514/542/536 (≥500)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "CQRS olarak mı okunacak? → TASARIM İDDİASI (kanıt değil)"
group: mimari
---

# commands — K011-application (kademe-2 · subdomain)

**ELI15:** Kullanıcıdan gelen 'yap' talepleri — yazma eylemlerinin taşıyıcısı.

**Sınır (ne DEĞİL):** Yazma kuralı K008-domain'de · kalıcılık K007-data'da.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler application-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `commands-kapsam.md` | Sınır, yazma yüzeyleri, komut katalogu | Kademe-2 |
| kanit | `commands-dogrulama.md` | Kapı ölçümü ve iddia kontrolü | Kademe-2 |
| kanit | `commands-web-arastirma.md` | Resmi kaynak araştırması (havuz) | Kademe-2 |
| kod | `commands-ornekler.md` | Çok dilli kod örnekleri | Kademe-2 |
| mimari | `commands-bilesenler.md` | Bileşen envanteri | Kademe-2 |

## §TAMAMLAMA — 6/6 dosya üretim kaydı (2026-10-15)

| # | Dosya | Grup | Satır | Durum |
|---|---|---|---|---|
| 1 | `index.md` | mimari | 53 | ✅ (40–100 aralığında) |
| 2 | `commands-kapsam.md` | mimari | 507 | ✅ |
| 3 | `commands-dogrulama.md` | kanit | 509 | ✅ |
| 4 | `commands-web-arastirma.md` | kanit | 514 | ✅ |
| 5 | `commands-ornekler.md` | kod | 542 | ✅ |
| 6 | `commands-bilesenler.md` | mimari | 536 | ✅ |
| | **Toplam** | — | **2661** | ✅ |

### Özgün bulgular (bu klasöre özgü)

| # | Bulgu | Kanıt | Dosya |
|---|---|---|---|
| 1 | **İki middleware katmanı** (11 HTTP + 7 API = 18 kapı) | `src/Middleware/` 11 · `src/Api/Middleware/` 7 | bilesenler §F |
| 2 | `getPdo()` tx bypass yolu | DatabaseManager · 64 | dogrulama §H.1 |
| 3 | `ATTR_PERSISTENT => true` bağlantı riski | DatabaseManager · 26 | dogrulama §H.2 |
| 4 | 5 middleware adı çifti (duplicate-pr adayı) | §F.1–F.5 | bilesenler §K |
| 5 | `ToastComponent.js` iki dizinde | composites + interactive | bilesenler §K.2 |
| 6 | İstemci JS'in yalnızca %3.2'si yazma | 3/94 | bilesenler §I |
| 7 | `X-CSRF-Token` header'ı iki ayrı kaynaktan | oauth-manager · 53 · gender-select · 53 | ornekler §5.3 |
| 8 | 5/6 boşluk **gerçek boşluk** olarak kanıtlandı | negatif envanter | dogrulama §8 |

### Düzeltilen uydurma/yanlış adlar (dürüstlük kaydı)

| # | Üretilen | Gerçek (disk) | Düzeltme |
|---|---|---|---|
| 1 | `StructuredLogger.php` | `LoggerFactory.php` · `FileHandler.php` · `Redactor.php` | 3 dosya |
| 2 | `js/core/FetchWrapper.js` | `js/router/FetchWrapper.js` | yol |
| 3 | `OriginMiddleware` · `SessionMiddleware` | `OriginCheckMiddleware` · `SessionManagerMiddleware` | ad |
| 4 | `RequestValidationMiddleware` (src/Middleware) | `Api/Middleware/` altında | katman |
| 5 | `ResponseNormalizationMiddleware` (src/Middleware) | `Api/Middleware/` altında | katman |
| 6 | `c-toast.js` · `c-form.js` · `c-spinner.js` | `c-toast.css` · `c-forms.css` · (spinner yok) | 3 dosya |
| 7 | `connect(platform, gender)` | `connect(provider)` | imza |
| 8 | `shared/src/Database/Config/` | `shared/src/Database/DatabaseConfig.php` | yol |
| 9 | `shared/src/Api/**` ≈35 · JS 98 | **34** · **94** | sayı |
| 10 | `OAuthProvider (12 adet)` | 12 dosya = 1 Base + 1 arayüz + **10 platform** | ayrıştırma |

**Kural:** Bu 10 satır **silinmez** — ilk taslak hatasının disk ölçümüyle
nasıl düzeltildiğinin kaydıdır (zero-hallucination disiplini).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

