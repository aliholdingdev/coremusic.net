---
title: "CoreMusic — api.coremusic.net İş Akışı"
type: docs
category: api
docType: workflow
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# api.coremusic.net — WORKFLOW.md

**docType:** workflow · **Klasör:** `api.coremusic.net/` · **Sorumlu:** MO (workflow)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[CONTEXT.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `api.coremusic.net/` üzerinde bir değişikliğin adım adım akışını (adım → neden → atlarsan ne olur) ve kapıları tanımlar. API sözleşmecidir: hata biçimi/middleware sırası değişikliği güvenlik olayıdır, bu yüzden kapılar zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Test komutu | `api.coremusic.net/composer.json` → require-dev `phpunit/phpunit ^10.5` (test koşusu kök `AGENTS.md` §7 ile `composer`/phpunit üzerinden) |
| Middleware sırası ADR-020 ile sabit | `index.php` pipeline yorumu |
| Hata sözleşmesi | `index.php` başlık yorumu |
| Commit subagent atmaz | Kök `AGENTS.md` §7/§8 |

> ⚠️ VERIFICATION REQUIRED: `api.coremusic.net/composer.json` içinde `scripts` bloğu **yok** (doğrudan okundu) — bu yüzden "composer test" yerine phpunit çalıştırma yolu `phpunit.xml` üzerinden tanımıdır; script tanımı eklenmesi MO/Backend kararına bağlıdır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Uç/route/controller değişiklik akışı | `shared/` içi ortak katman akışı → [[../shared/WORKFLOW.md]] |
| Kapılar: keşif → uygulama → test → ADR → rapor | Deploy (DevOps), CI (GitHub Actions) |
| Commit kuralı | Vault `.ai/` süreçleri |

- **Kullananlar:** Backend, Security, QA, MO.
- **Ön koşul:** [[CONTEXT.md]] + [[CLAUDE.md]] okunmuş; hedef dosya diskte mevcut.

---

## 3. Mimari

### 3.1 Adım Tablosu

| # | Adım | Çıktı | Neden | Atlarsan ne olur | eli10 | eli15 |
|---|------|-------|-------|------------------|-------|-------|
| 1 | Görevi netleştir (prompt-maker / soru kapısı) | Onaylı öz | Doğru iş | İsraf + revert | Önce ne istendiğini anlamak. | Kapı (kök `AGENTS.md` §1) onaysız başlamaz. Tanım okunmadan kod yazılırsa beklenti ile sistem ayrışır. Çıktı, ölçülebilir özettir. Atlarsan yanlış iş riski. |
| 2 | [[CLAUDE.md]] §4 + [[CONTEXT.md]] §3.2 akışını oku | Kural + akış | Sözleşme/sıra yasakları | Güvenlik sözleşmesi ihlali | Bu API'nin kurallarını öğrenmek. | Kural ayrı dosyadır; okunmadan girilirse JSON-only/hata biçimi kuralı çiğnenir. ADR-020 sırası böyle öğrenilir. Kapı, kural bilgisiyle açılır. Atlarsan sözleşme kırarsın. |
| 3 | `index.php` + hedef dosyayı 1. kez oku | Kanıt | Anti-overthink | Tekrar okuma, gecikme | Dosyayı bir kez okuyup karar vermek. | İlk okuma yeterli kanıtı verir; ikinci okuma israftır (kök `AGENTS.md` §5). Karar ilk okumadan verilir. Atlarsan token israfı + gecikme. |
| 4 | Değişiklik: `include/` veya `config/` | Diff | Sorumluluk sınırı | Dağınık kod, çakışma | Doğru masada çalışmak. | Kod yalnız ilgili dizine girer; `index.php` yalnız ADR'li satırlar için (CLAUDE #3). Kapsam dar kalınca inceleme kolaylaşır. Atlarsan sınır ihlali + revert. |
| 5 | Route değiştiyse `config/routes.php` + alias kontrolü | Route kaydı | Tek kabul noktası | Çift route = çift davranış | Adres defterini tek yerde tutmak. | `/v1` aliası zaten `/api/v1`'e çevirir; ayrı route yazmak ikinci kaynak doğurur. Kayıt tek olunca istemci kırılmaz. Atlarsan çift davranış + bakımsızlık. |
| 6 | Güvenlik yüzeyi değiştiyse Security handover (AGENTS §3.3) | Onay | CORS/sıra/sızıntı A1 yüzeyi | Yetkisiz güvenlik değişikliği | Güvenlik işini güvenlikçiye bırakmak. | CORS ve başlık satırları Security yetkisindedir (AGENTS §3.1 #2). Onaysız değişim denetimden kaçar. Kapı, onayla kapanır. Atlarsan güvenlik açığı + log ERROR. |
| 7 | Test ekle: `tests/Unit/` (Smoke/RouteConfig/AuthController deseni) | Test | Sözleşme sabitleme | Sessiz kırılma | Doğruluğu sınavla kanıtlamak. | QA yüzeyi test dosyalarıdır; yeni uç = yeni test (AGENTS §3.1 #3). Test kodu taklit etmez, yalnız doğrular. Kapı yeşil olunca geçilir. Atlarsan hata production'da bulunur. |
| 8 | phpunit koş (`phpunit.xml` üzerinden) | Yeşil kapı | Regresyon kapısı | Kırık değişiklik git'e girer | Sınavı çalıştırıp geçmek. | `phpunit.xml` + `require-dev phpunit ^10.5` disk kanıtıdır; script bloğu yok (§1 notu). Kapıdan geçmeyen iş bitmiş sayılmaz. Atlarsan kırık uç yayılır. |
| 9 | UI/istemci etkisi varsa browser testi (gerçek sayfa) | Görsel kanıt | Kök `AGENTS.md` §7 #7 | Bozuk istemci | Değişikliği tarayıcıda görmek. | JSON API'nin tüketicisi UI'dır; sözleşmeyi UI deneyimi doğrular. Görsel kapı geçince güven sağlanır. Atlarsan gizli kırık. |
| 10 | Rapor; **commit ATMAZ** | Rapor | Yetki sınırı | Düzensiz tarih, revert | Haber vermek; commit'i başkası atar. | Commit yetkisi MO/orkestratördedir (kök `AGENTS.md` §7/§8). Tarih tek elden yazılır. Rapor kapıyı teslim eder. Atlarsan iş görünmez ya da düzensiz log. |

### 3.2 Kapılar

```text
[K1 KEŞİF: kural+akış oku] ─onay─▶ [K2 UYGULAMA: include/config]
      ─ security yüzeyi ise K3 SECURITY ONAYI ─▶ [K4 TEST: phpunit yeşil]
      ─ UI etkisi varsa K5 BROWSER ─▶ [K6 RAPOR → MO → COMMIT (MO)]
```

| Kapı | Koşul | Red durumunda |
|------|-------|---------------|
| K1 | CLAUDE + CONTEXT okundu, 1. okumadan karar | Görev başlamaz |
| K2 | Diff yalnız `include/`, `config/`, `tests/` | Kapsam daraltılır |
| K3 | Security onayı (CORS/sıra/hata biçimi) | Handover / ADR'ye gider (AGENTS §3.3) |
| K4 | `phpunit.xml` koşusu yeşil | `.ai/.rules/error-recovery.md` → yeniden yaz → tekrar (kök `AGENTS.md` §7) |
| K5 | Gerçek sayfa doğrulaması | Düzelt, tekrar test |
| K6 | Rapor tam; commit MO'da | Subagent commit atmaz |

### 3.3 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük | İnceleme kör olur |
| 2 | **Tek sorumluluk** | Adımın sahibi | Sahipsiz adım |
| 3 | **Token tek kaynak** | Ortak değer tek yerde | Tutarsızlık |
| 4 | **Cihaz izolasyonu** | Cihaz farkı ayrı | Yayılır, drift |
| 5 | **Vendor karantinası** | `vendor/` ayrı | Güncelleme kodu bozar |

> **eli10 (basit):** Adımlar ve kapılar ayrı kutularda; bir kutu değişince diğeri bozulmasın.
> **eli15 (detay):** Süreç ayrı dosyadır çünkü kapılar (onay/test/ADR) koddan farklı hızda değişir; kod içine gömülse kapı unutulur. Okunması, işe sırayla başlamayı sağlar. Yazması, tekrarlanabilir ve denetlenebilir akış bırakır. Bölünmezse "hangi kapı kaldı" sorusu cevapsız kalır — güvenlik onayı kaybolur.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Faz 1 keşif onaysız kod yok | Yanlış dosyaya yazımı engeller (kök `AGENTS.md` §4) |
| 2 | Aynı dosya 2. kez okunmaz | Anti-overthink (kök `AGENTS.md` §5) |
| 3 | Güvenlik yüzeyi → Security onayı (AGENTS §3.3) | A1 sınırı |
| 4 | Hata → `.ai/.rules/error-recovery.md` → yeniden yaz → tekrar kontrol | Kör düzeltme döngüsü durur (kök `AGENTS.md` §7) |
| 5 | UI etkisi → browser testi | Görsel kanıt zorunlu (kök `AGENTS.md` §7) |
| 6 | **Commit subagent ATMAZ** | Tarih/entegrasyon MO'da (kök `AGENTS.md` §7/§8) |

> **eli10 (basit):** Bu kurallar işin sırayla ve onayla yürümesini, commit'in tek elden atılmasını sağlar.
> **eli15 (detay):** Kurallar ayrıdır çünkü süreç, kodla farklı anda değişir; kod içine gömülse revizyonda kaybolur. Okunması, kapıların önceden bilinmesini sağlar. Yazması, denetimi garantiler. İhlalde 3 başarısız deneme → DUR + 1 soru (kök `AGENTS.md` §5).

---

## 5. Workflow

```text
GÖREV → [K1: KURAL+AKIŞ+1. okuma] → [K2: include/config diff]
  → (güvenlikse [K3: SECURITY]) → [K4: test + phpunit]
  → (UI ise [K5: BROWSER]) → [K6: RAPOR — commit MO'da]
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 alan + `docType: workflow` |
| 2 | Bölüm sırası | §1–§7 |
| 3 | Adım tablosu | Her adımda Neden + Atlarsan dolu |
| 4 | Commit kuralı | §3.1 #10 + §4 #6 "ATMAZ" |
| 5 | Placeholder | `{{` kalmadı |
| 6 | Wiki-link | `[[...]]`, hedefler var |
| 7 | eli10 + eli15 | §3.1 satırlarında etiketli blok |
| 8 | Disk kanıtı | `composer.json` scripts YOK notu gerçek (doğrudan okundu) |
| 9 | Dokunulmaz | Üretim kodu + mevcut doküman değişmedi |
| 10 | Emoji | Yalnız `[[...]]` |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Kural listesi |
| Klasör rolleri | [[AGENTS.md]] | Routing + handover |
| Klasör context | [[CONTEXT.md]] | Akış haritası |
| Kök master | [[../AGENTS.md]] | §1 kapı, §4 keşif, §7 loop |
| shared süreç | [[../shared/WORKFLOW.md]] | Ortak katman akışı |
| Hata kurtarma | `../.ai/.rules/error-recovery.md` | K4 kapısı |
| Test yapılandırması | `api.coremusic.net/phpunit.xml` | Kapı kanıtı |
| Template kaynağı | `../.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
