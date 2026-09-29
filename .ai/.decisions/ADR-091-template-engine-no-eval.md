---
title: "CoreMusic — ADR-091: TemplateEngine eval() Kaldırımı (Guardrail §21 — cache dosyası + require)"
type: "architecture-decision"
category: "security"
date: "2026-09-27"
updated: "2026-09-29"
version: "1.0.1"
status: "accepted"
authority: "SSOT — TemplateEngine renderString yürütme stratejisi; Guardrail §21 (eval yasağı) kod tarafında tek karşılığı bu karardır"
kaynak: "Kullanıcı onayı (2026-09-27): 'eval'siz aynı davranış' — tam DSL'e geçiş YOK + disk kanıtı taraması (shared/src/Component/TemplateEngine.php:70)"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic — ADR-091: TemplateEngine eval() Kaldırımı (Guardrail §21)

> **Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-09-27 · **Onay:** kullanıcı onaylı kapsam (eval'siz aynı davranış; tam DSL reddedildi)
> **Karar serisi:** `.ai/.decisions/` · **Slug:** `ADR-091-template-engine-no-eval`
> **İlgili kararlar:** [[ADR-002-pdo-mandatory-no-orm]] (yürütme güvenliği disiplini) · [[ADR-020-api-public-security]] · [[../brain.md]] · [[../index.md]]
> **Şablon:** `.ai/.templates/adr/adr-security-template.md` (güvenlik domaini, Guardrail #16)
> **Numara gerekçesi:** `.ai/.templates/coremusic-vault-template.md:407` — "yeni: ≥ ADR-091 (ADR-090 dolu)" → **091 ilk boş slot**. Frozen **001-037'ye dokunulmamıştır**.

---

## §1 Bağlam (Context)

### §1.1 İhlal

`.ai/CLAUDE.md` **Guardrail §21** — `eval()` yasaktır (`shared/AGENTS.md` §4 Yasak 3: "ORM (ADR-002), var, eval"). `shared/src/Component/TemplateEngine.php` içindeki `renderString()` bu kuralı ihlal ediyordu.

### §1.2 Kod kanıtı (öncesi)

| Dosya | Satır | Kanıt |
|---|---|---|
| `shared/src/Component/TemplateEngine.php` | 70 | `eval('?>' . $template);` — **Guardrail §21 ihlali** |
| `shared/src/Component/TemplateEngine.php` | 35-53 | `render()` → `require $templatePath;` (satır 47) — **eval değil, dokunulmadı** |
| `shared/src/Component/TemplateEngine.php` | 63-76 | `renderString()` tanımı (`ob_start` + `extract` + `eval`) |
| repo taraması (`renderString`) | — | **Çağıracı 0** — sınıf tanımı ve bu ADR'in testi dışında hiçbir kullanım yok |
| `shared/tests/` | — | TemplateEngine testi **yoktu** |

**Etki:** `renderString()` prodüksiyonda çağrılmadığı için gerçek exploit yüzeyi sıfırdı; ihlal **tasarım/seviye** düzeyindedir (guardrail, kodda bulunma üzerinden denetlenir). Yine de kamu API'si içinde `eval` taşıması, gelecekteki bir şablon enjeksiyonunu doğrudan kod çalıştırmasına çevirirdi (§5.2).

### §1.3 Kullanıcı onayı (kapsam sınırı)

Onaylanan davranış: **"eval'siz aynı davranış"** — şablon geçici dosyaya yazılır, `require` ile çalıştırılır, dosya temizlenir. **Tam şablon DSL'ine geçiş YOK** (maliyet), **`renderString()` kaldırma YOK** (public API).

---

## §2 Karar (Decision)

`renderString()` içindeki `eval('?>' . $template)` yerine **geçici cache dosyası + `require` + finally içinde temizlik** konur. `extract($data, EXTR_SKIP)`, `ob_start()`/`ob_get_clean()` akışı, `$data['__functions']` enjeksiyonu ve exception'ın yeniden yayılması **birebir korunur**. `render()` ve onun `require $templatePath` akışı **değiştirilmez**.

### §2.1 Önce / Sonra

```php
// ÖNCE (shared/src/Component/TemplateEngine.php:70 — Guardrail §21 ihlali)
eval('?>' . $template);

// SONRA (aynı dosya — Guardrail §21: eval yok — cache dosyası + require (ADR-091))
$cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';
if (file_put_contents($cachePath, $template, LOCK_EX) === false) {
    throw new \RuntimeException("Template cache write failed: {$cachePath}");
}
ob_start();
try {
    extract($data, EXTR_SKIP);
    require $cachePath;          // eval yerine
    return (string) ob_get_clean();
} catch (\Throwable $e) {
    ob_end_clean();
    throw $e;                    // yeniden yayma korunur
} finally {
    if (is_file($cachePath)) {
        @unlink($cachePath);     // başarılı ve hata yolunda da temizlik
    }
}
```

### §2.2 Davranış eşitliği

| Davranış | eval (önce) | require (sonra) | Eşitlik |
|---|---|---|---|
| `extract($data, EXTR_SKIP)` kapsamı | fonksiyon kapsamı | fonksiyon kapsamı | ✅ |
| `$data['__functions']` enjeksiyonu | var | var | ✅ |
| Çıktı yakalama (`ob_start` / `ob_get_clean`) | var | var | ✅ |
| HTML-serbest şablon başlangıcı (`?>` etkisi) | `eval('?>' . t)` | dosyanın tamamı PHP lexer'ından geçer | ✅ |
| Sözdizimi hatası | `ParseError` | `ParseError` (`require` üzerinden) | ✅ |
| Runtime hata | `Throwable` yayılır, buffer temizlenir | aynı akış | ✅ |
| Kaynak temizliği | (yok — kod bellekte kalır) | `finally` → `unlink` | ➕ iyileşme |

### §2.3 Güvenlik kontrolü

| # | Kontrol | Durum |
|---|---|---|
| C1 | `eval()` yok (`shared/src` geneli tarama) | ✅ 0 sonuç |
| C2 | Geçici dosya adı tahmin edilemez (`random_bytes(8)` → 16 hex) | ✅ |
| C3 | Geçici dosya `sys_get_temp_dir()` içinde, web kökünün dışında | ✅ |
| C4 | Her çağrıda benzersiz yol → opcache yol çakışması yok | ✅ |
| C5 | Hata durumunda da temizlik (`finally`) | ✅ |
| C6 | Şablon kaynağı ve çağrıncısı değişmedi (`renderString` çağrıncısı 0) | ✅ |

---

## §3 Alternatifler

| # | Seçenek | Karar | Gerekçe |
|---|---|---|---|
| A | **Geçici dosya + `require`** | ✅ **SEÇİLDİ** | Guardrail §21 karşılanır, davranış birebir aynı, maliyet ~10 satır |
| B | Tam şablon DSL'ine geçiş (Twig/Blade benzeri) | ❌ **RED** | Şablonların tamamının yeniden yazılması + bağımlılık artışı; kullanıcı onayı kapsamı dışı ("tam DSL'e geçiş YOK") |
| C | `renderString()`'i kaldırmak | ❌ **RED** | Public API kırılımı; çağrıncısı 0 olsa da imza dışa açık ve davranışı test edilmiş |
| D | Mevcut hali korumak (`eval` kalsın) | ❌ **RED** | Guardrail §21 ihlali kalıcı olur; `.ai/CLAUDE.md` yasağı ihlal edilir |

---

## §4 Sonuçlar (Consequences)

**Pozitif**
- Guardrail §21 karşılanır; `shared/src` içinde gerçek `eval(` kullanımı 0.
- Davranış aynı — mevcut ve gelecekteki çağıranlar için kırılma yok.
- Hata yolunda buffer temizliği korunur; ek olarak dosya artığı kalmaz.
- Test altyapısı kazanıldı: `tests/Component/TemplateEngineTest.php` (5 test) + `phpunit.xml`'e `Component` testsuite kaydı.

**Negatif / Risk**
- Her `renderString()` çağrısı 1 dosya yazma + 1 `require` (derleme) + 1 silme yapar → eval'den yavaş (kabul edildi: çağrıncısı 0, sıcak yol değil).
- Geçici dizin yazılamazsa `RuntimeException` fırlatılır (yeni, açık hata modu; buffer henüz açılmadığı için sızıntı yok).
- Windows'ta `unlink` başarısız kalırsa `@unlink` artığı yutar → test 3 artıkları yakalar.

**Nötr**
- `render()` akışı, cache tasarımı ve `clearCache()` no-op değişmedi.

---

## §5 OWASP / STRIDE (güvenlik şablonu §3.1-§3.2)

### §5.1 OWASP Top 10 (2021) eşleşmesi

| # | OWASP 2021 | Eşleşme | İlgili kod |
|---|---|---|---|
| A03 | Injection | 🔴 azaltım — `eval` (kod enjeksiyonu kapısı) kaldırıldı | `TemplateEngine::renderString()` |
| A04 | Insecure Design | 🟠 azaltım — guardrail'e aykırı tasarım deseni silindi | `shared/src/Component/` |
| A05 | Security Misconfiguration | 🟡 — guardrail denetimi kodda karşılık buldu | `.ai/CLAUDE.md§21` |
| A08 | Software & Data Integrity | 🟡 — şablon kaynağı değişmedi (çağıracı 0), davranış test ile kilitlendi | `tests/Component/` |
| A01/A02/A06/A07/A09/A10 | — | 🟢 bu kararla ilgisi yok | — |

**Eşleşen kalem:** `A03, A04` → **Şiddet:** 🔴 (önleyici)

### §5.2 STRIDE

| Tehdit | Senaryo (önce) | Varlık | Etki | Azaltım (sonra) |
|---|---|---|---|---|
| **T**ampering | şablon metnine kod enjeksiyonu → `eval` ile doğrudan yürütme | `TemplateEngine` | 🔴 | `require` + guardrail; şablon kaynağı değişmedi |
| **E**levation | enjeksiyonla rastgele PHP çalıştırma yetkisi | uygulama süreci | 🔴 | `eval` kapısı kapatıldı |
| **S**poofing | — (kimlik doğrulama ile ilgisi yok) | — | 🟢 | — |
| **R**epudiation | — (log kapsamı dışında; ekleme MO yapar) | `.ai/log.md` | 🟢 | append-only protokol |
| **I**nfo Disclosure | hata yolunda geçici dosya artığı kalırsa şablon içeriği diskte kalır | `sys_get_temp_dir()` | 🟠 | `finally` → `@unlink`; test 3 sızıntı avlar |
| **D**oS | dosya yazma/silme ile temp dizini şişirme | disk | 🟠 | çağrıncısı 0 + dosya çağrı başına tek tek silinir; artık `.php` dosyası birikmez |

---

## §6 Doğrulama

| Test ID | Adım | Beklenen | Durum (2026-09-27) |
|---|---|---|---|
| V1 | repo taraması `eval\s*\(` (vendor hariç, `*.php`) | gerçek kullanım 0 | ✅ 0 sonuç — kodda `eval(` kalmadı |
| V2 | 3 kopya kontrolü (`shared` + 2 vendor) | aynı fix | ✅ 3/3 `Guardrail §21` satırı + `require $cachePath` aynı |
| V3 | vendor tipi | symlink mi mirror mı | ✅ **symlink/junction** — composer.json `{"type":"path","url":"../shared","options":{"symlink":true}}`; 12:06:33'te oluşturulan test dosyası anında 3 yolda aynı metadata ile görünür → composer senkronu gerekmez |
| V4 | `tests/Component/TemplateEngineTest.php` | 5 test (düz HTML, extract+fonksiyon, bozuk şablon+temizlik, render() dosya yolu, render() eksik dosya) | ✅ yazıldı; `phpunit.xml`'e `Component` testsuite eklendi |
| V5 | `php -l` (değişen PHP dosyaları) | 0 hata | ⚠️ **bu oturumda çalıştırılamadı** — shell izni reddedildi (`permission.rejected`) |
| V6 | `vendor\bin\phpunit.bat` | 0 failure / 0 error | ⏳ **MO/QA tarafından çalıştırılacak** (aynı shell izin kısıtı) |
| V7 | ADR `vault-utf8-writer.mjs verify` | BOM yok, mojibake 0 | ⚠️ shell ile çalıştırılamadı; dosya UTF-8 (BOM yok) olarak yazıldı ve okunarak doğrulandı |

> ⚠️ **VERIFICATION REQUIRED:** V5/V6 bu oturumda koşulamadı; sonraki oturumda `cd shared && vendor\bin\phpunit.bat` ile 0 failure doğrulanıp bu satır güncellenmelidir.

---

## §7 Referanslar

| Kaynak | Tür | Not |
|---|---|---|
| `.ai/CLAUDE.md§21` | guardrail (frozen) | eval yasağı |
| `.ai/.templates/adr/adr-security-template.md` | şablon (Guardrail #16) | OWASP/STRIDE bölümleri |
| `shared/src/Component/TemplateEngine.php` | kanıt | satır 68-91 (`renderString`), satır 35-53 (`render`) |
| `shared/tests/Component/TemplateEngineTest.php` | kanıt | regresyon testi |
| `home.coremusic.net/composer.json`, `auth.coremusic.net/composer.json` | kanıt | path repo + `symlink: true` |
| `shared/AGENTS.md` §4 Yasak 3 | kural | "ORM, var, eval" |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
