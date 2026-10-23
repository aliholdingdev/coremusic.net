---
title: "CoreMusic — admin.coremusic.net İş Akışı"
type: docs
category: admin
docType: workflow
date: 2026-10-23
updated: 2026-10-23
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# admin.coremusic.net — WORKFLOW.md

**docType:** workflow · **Klasör:** `admin.coremusic.net/` · **Sorumlu:** MO (workflow)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[CONTEXT.md]] · [[PRD.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `admin.coremusic.net/` üzerinde bir değişikliğin adım adım akışını (adım → neden → atlarsan ne olur) ve kapıları tanımlar. Yönetim paneli auth sözleşmesine (ADR-043/058) ve CSRF/session yüzeyine dayandığından güvenlik kapıları zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Test komutu: `vendor/bin/phpunit` (`phpunit.xml` üzerinden) | `phpunit.xml` (bootstrap `tests/bootstrap.php`, kapsam `include/`) |
| Auth akışı ADR-043 ile sabit | `AuthAliasRedirector.php:29-34` + ADR-043 |
| Markup sözleşmesi testi ADR-058 | `tests/Unit/AdminMarkupContractTest.php` (auth formları `../auth.coremusic.net/pages/admin/`'i okur) |
| Commit subagent atmaz | Kök `AGENTS.md` §7/§8 |

> ⚠️ VERIFICATION REQUIRED: `composer.json` içinde `scripts` bloğu **yok** (2026-10-23 okuma) — test yolu `phpunit.xml` üzerinden tanımlıdır; script eklenmesi MO/Backend kararına bağlıdır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Sayfa/route/bridge değişiklik akışı | `../auth.coremusic.net/` içi akış → [[../auth.coremusic.net/WORKFLOW.md]] |
| Kapılar: keşif → uygulama → güvenlik → test → rapor | Deploy (DevOps), CI (GitHub Actions) |
| Commit kuralı | Vault `.ai/` süreçleri |

- **Kullananlar:** Backend, Security, QA, DevOps, MO.
- **Ön koşul:** [[CONTEXT.md]] + [[CLAUDE.md]] okunmuş; hedef dosya diskte mevcut.

---

## 3. Mimari

### 3.1 Adım Tablosu

| # | Adım | Çıktı | Neden | Atlarsan ne olur | eli10 | eli15 |
|---|------|-------|-------|------------------|-------|-------|
| 1 | Görevi netleştir (prompt-maker / soru kapısı) | Onaylı öz | Doğru iş | İsraf + revert | Önce ne istendiğini anlamak. | Kapı (kök `AGENTS.md` §1) onaysız başlamaz. Tanım okunmadan kod yazılırsa beklenti ile sistem ayrışır. |
| 2 | [[CLAUDE.md]] §4 + [[CONTEXT.md]] §3 oku | Kural + envanter | ADR-043/058 + shared yasağı | Sözleşme ihlali | Bu panelin kurallarını öğrenmek. | Kural ayrı dosyadır; okunmadan girilirse form/auth ayrımı ve CSRF deseni çiğnenir. Kapı kural bilgisiyle açılır. |
| 3 | Hedef dosyayı 1. kez oku (`pages/` · `include/` · `config/`) | Kanıt | Anti-overthink | Tekrar okuma, gecikme | Dosyayı bir kez okuyup karar vermek. | İlk okuma yeterli kanıtı verir; ikinci okuma israftır (kök `AGENTS.md` §5). |
| 4 | Değişiklik: `pages/admin-*.php` ya da `include/Routing/` | Diff | Sorumluluk sınırı | Dağınık kod, çakışma | Doğru masada çalışmak. | Sayfa işi `pages/`'te, I/O-sız yönlendirme `include/Routing/`'te kalır; `$h` escaping korunur (CLAUDE #6). Atlarsan XSS yüzeyi + revert. |
| 5 | Route eklendi mi? `config/routes.php` + `requiresAuth:true` kontrolü | Route kaydı | Yetki işareti | Yetkisiz sayfa (auth'suz erişim) | Adres defterini tek yerde tutmak. | `auth/callback` dışındaki her rota `requiresAuth:true` taşır; işaret unutulursa panel açık kalır. `shared/config/routes.php` asla değişmez. |
| 6 | Auth/CSRF/session yüzeyine dokunuldu mu? → Security handover (AGENTS §3.3) | Onay | A1 yüzeyi | Yetkisiz güvenlik değişikliği | Güvenlik işini güvenlikçiye bırakmak. | `AdminAuthBridge`, cookie, debug kapısı satırları Security yetkisindedir (CLAUDE #7/#8). Onaysız değişim denetimden kaçar. |
| 7 | Yeni sorgu/şema ihtiyacı mı? → Data handover (`../shared`) | Onay | `coremusic_auth` erişimi tek kapı | Dağınık DB erişimi | DB işini veri uzmanına bırakmak. | Mevcut tek sorgu `admin-dashboard.php:36`; yenisi Data ile `shared/src/Database/` üzerinden eklenir. |
| 8 | Test ekle/koru: `tests/Unit/` deseni | Test | Sözleşme sabitleme | Sessiz kırılma | Doğruluğu sınavla kanıtlamak. | Yeni sayfa/route = yeni test satırı (RouteResolution/ViewMode deseni). Markup değiştiyse ADR-058 testi de koşulur. |
| 9 | phpunit koş (`phpunit.xml` üzerinden) | Yeşil kapı | Regresyon kapısı | Kırık değişiklik git'e girer | Sınavı çalıştırıp geçmek. | Kapsam yalnız `include/`'tır; `failOnRisky/Warning=true`. Kapıdan geçmeyen iş bitmiş sayılmaz. |
| 10 | UI etkisi varsa browser testi (gerçek sayfa, `assets` bağlı) | Görsel kanıt | Kök `AGENTS.md` §7 #7 | Bozuk panel | Değişikliği tarayıcıda görmek. | Panelin JS/CSS'i `assets.coremusic.net`'ten gelir (`admin-shell.js`); yerelde sunucu ayağa kalkmadan doğrulanamaz. |
| 11 | Rapor; **commit ATMAZ** | Rapor | Yetki sınırı | Düzensiz tarih, revert | Haber vermek; commit'i orkestratör atar. | Commit yetkisi MO/orkestratördedir (kök `AGENTS.md` §7/§8). Rapor kapıyı teslim eder. |

### 3.2 Kapılar

```text
[K1 KEŞİF: CONTEXT+CLAUDE+kural oku] ─onay─▶ [K2 UYGULAMA: pages/include/config]
      ─ auth/CSRF yüzeyi ise K3 SECURITY ONAYI ─▶ [K4 DATA ONAYI (yeni sorgu ise)]
      ─▶ [K5 TEST: phpunit yeşil + ADR-058 markup] ─▶ [K6 RAPOR → MO → COMMIT]
```

| Kapı | Ölçüt | Red durumunda |
|------|-------|----------------|
| K1 | Kural + envanter okundu | Okumadan başlama yasak |
| K2 | Diff yalnız yetkili dosyalarda | Sınır ihlali → revert + log ERROR |
| K3 | Security onayı (auth/CSRF/session/debug) | Handover (AGENTS §3.3) |
| K4 | Data onayı (yeni sorgu/şema) | `../shared` içinde çözülür |
| K5 | phpunit yeşil; markup testi korunmuş | Düzelt + tekrar koş |
| K6 | Rapor + log append (MO) | İş görünmez sayılır |

> **eli10 (basit):** On bir adım, altı kapı: önce oku, sonra yaz, güvenlikçiye sor, sınavı geç, raporla.
> **eli15 (detay):** Adımlar ayrı satırlardır çünkü her adımın çıktısı ve gerekçesi ölçülür; kapılar ayrıdır ki üretim ile denetim ayrılsın. Onay kapısı atlanırsa auth yüzeyine izinsiz dokunulur ve tüm paneller etkilenir.

---

## 4. Kurallar

| # | Kural | Neden | Kaynak |
|---|-------|-------|--------|
| 1 | Mockup gate bu yüzeyde uygulanmaz (server-rendered panel; yeni görsel panel işi varsa `.ai/ui-design/` okunur) | Panel CSS/JS'i assets'te | Kök `AGENTS.md` §13 |
| 2 | `.ai/scripts/*.ps1` yalnız repo kökünden çalıştırılır | Göreli yol hatası | `.ai/AGENTS.md` §13.6 |
| 3 | Vault'a yazım yalnız MO (log append-only) | SSOT bütünlüğü | [[../.ai/AGENTS.md]] §25.3 |
| 4 | Üç başarız turda dur + üst karar iste | Kör retry yasağı | Kök `AGENTS.md` §2 |

---

## 5. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 alan + `docType: workflow` |
| 2 | Adım tablosu | Her satırda eli10 + eli15 |
| 3 | Kapı şeması | §3.2 altı kapı, sıralı |
| 4 | Placeholder | `{{` kalmadı |
| 5 | Wiki-link | `[[...]]`, hedefler diskte var |
| 6 | Komut iddiaları | `phpunit.xml` disk kanıtına dayanır; `⚠️` notu korunmuş |
| 7 | Emoji | Yok |
| 8 | Dokunulmaz | Kod + kardeş dokümanlar değişmedi |

---

## 6. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Kural listesi |
| Klasör rolleri | [[AGENTS.md]] | Routing + handover |
| Klasör context | [[CONTEXT.md]] | Envanter + auth akışı |
| Klasör gereksinimleri | [[PRD.md]] | PRD-ADM-001..007 |
| Kök master kurallar | [[../AGENTS.md]] | §7 Execution Loop |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Genel fazlar |
| Auth komşusu | [[../auth.coremusic.net/WORKFLOW.md]] | Form tarafı akışı |
| Template kaynağı | `.ai/.templates/documentation/workflow-md-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-23
