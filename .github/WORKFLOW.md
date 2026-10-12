---
title: "CoreMusic — .github Klasör İş Akışı"
type: docs
category: infrastructure
docType: workflow
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/WORKFLOW.md + kök AGENTS.md"
---

# .github — WORKFLOW.md

**docType:** workflow · **Klasör:** `.github/` · **Sorumlu:** MO (workflow)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[CONTEXT.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `.github/` klasöründe bir görevin (yeni workflow, mevcut CI değişikliği, issue şablonu güncelleme) adım adım nasıl yürütüldüğünü tanımlar: her adımın çıktısı, girdisi/kapısı, neden olduğu ve atlanırsa ne olacağı. Kapsam notu: **.github = CI/CD + issue şablonları (DevOps domain)** — değişiklik her push'ta herkese çalıştığı için kapılar (onay + ölçüm) burada zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Yeni workflow şablondan türetilir | ci.yml satır 2 yorumu → `.ai/.templates/infrastructure/github-actions-template.md` §3.2 |
| CI planı K13 | ci.yml satır 3 yorumu → `.ai/architecture/k13-cicd/README.md` + `ci-pipeline.md` |
| Onay kapısı (taslak → onay → uygulama) | [[CLAUDE.md]] §4 Değişiklik Protokolü |
| Commit subagent'a ait değil | Kök `AGENTS.md` §7/§8 |
| Dağıtım kapısı | [[../.workflows/deployment.md]] |

> **eli10 (basit):** Bu akış, depo ayarlarının izinli, ölçülü ve kayıtlı değişmesini sağlar.
> **eli15 (detay):** Ayrı yazıldı çünkü süreç ile dosya içeriği farklı hızda değişir; `ci.yml` içine gömülse her revizyonda süreç aranır. Okunması, işe başlamadan kapıları gösterir. Yazılması, tekrarlanabilir ve denetlenebilir akış bırakır. Akış olmazsa onaysız yml değişir ve CI tüm ekibi kilitler.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Yeni workflow ekleme / mevcut `ci.yml`-`secret-scan.yml` değiştirme akışı | `.ai/` vault süreçleri → [[../.ai/WORKFLOW.md]] |
| Issue şablonu güncelleme akışı | Vault-sync/session-init gibi genel süreçler → [[../.workflows/vault-sync.md]] |
| Kapılar: keşif → onay → uygulama → ölçüm → rapor | PHP/JS/CSS kod akışı (ilgili katmanın `WORKFLOW.md`'si) |
| CI kapısı düştüğünde (fail) geri dönüş yolu | GitHub arayüz ayarları (diskte değil → UNKNOWN) |

- **Kullananlar:** DevOps Engineer (yürütücü), Security (secret-scan kapsamı), MO (kapı + rapor), kullanıcı (onay).
- **Ön koşul:** Hedef dosya diskte mevcut; envanter ölçülmüş (`.github/CONTEXT.md` §3.1); şablon okunmuş (Guardrail #16).

---

## 3. Mimari

### 3.1 Adım Tablosu (adım → çıktı → girdi/kapı → neden → atlarsan ne olur)

| # | Adım | Çıktı | Girdi / Kapı | Neden | Atlarsan ne olur | eli10 | eli15 |
|---|------|-------|--------------|-------|------------------|-------|-------|
| 1 | Görev tanımını netleştir | Onaylı görev özeti | Görev kapısı (soru/onay) | Yanlış iş yapmayı önler | İstenmeyen değişiklik, revert | Önce ne istendiğini anlamak. | Görev kapısı onaysız sonraki aşamaya geçmez (kök `AGENTS.md` §1). Tanım okunmadan başlarsan beklenti ile tanım ayrışır. Kapıdan geçince ölçülebilir bir çıktı (özet) elde edersin. Atlarsan yanlış iş üretimi ve zaman kaybı. |
| 2 | Kural + envanter oku: [[CLAUDE.md]] + [[AGENTS.md]] + [[CONTEXT.md]] | Kural listesi + 5 dosyalık ölçüm | G1 Keşif | Klasör yasakları ve gerçek sayılar | Gizli bilgi/izin ihlali, uydurma sayı | Klasör kurallarını ve dosya sayısını öğrenmek. | Kurallar klasör dosyalarındadır; genel kural her detayı taşıyamaz. Ölçüm okunmadan "2 workflow var" gibi iddia tahmin olur. G1 geçince elinde kanıt ve sınır vardır. Atlarsan çelişkili/uydurma bilgiyle çalışma. |
| 3 | Şablon + plan oku (Guardrail #16) | Şablon iskeleti + K13 planı | G1 Keşif (şablon zorunlu) | Tutarlı pipeline standardı | Şablonsuz yml, kendi kurallarına göre büyür | Yeni işi hazır kalıba koymak. | Yml şablondan türetilir (ci.yml/secret-scan.yml yorumları). Şablon okunmadan yazılan workflow gate/deploy düzenine uymaz. Plan (K13) işin neden var olduğunu kanıtlar. Atlarsan standartsız, tekrar eden pipeline. |
| 4 | Kullanıcı onayı al | Onay kaydı | **G2 Onay** (zorunlu) | Yalnız onaylı yml değişir | Sürpriz CI çalışması, kaynak israfı | Birinin "evet" demesini beklemek. | Onay, [[CLAUDE.md]] §4 protokolünün kapısıdır. Onaysız değişiklik push'ta herkese çalışır. Onay kaydı denetim izi bırakır. Atlarsan yetkisiz davranış ve revert riski. |
| 5 | Değişikliği uygula (yalnız hedef dosya) | Diff | G3 Kapsam | Kapsam disiplini (context lock) | Fazla dosyaya dokunma, çakışma | İstenen yeri değiştirmek, başka yere dokunmamak. | Eşzamanlı oturumlar var; diff kilitli kalınca inceleme ve revert kolay olur. Kapsam genişlerse başka oturumun işi bozulur. Atlarsan çakışma ve temizlik işi. |
| 6 | Doğrulama: yml geçerliliği + kapılar | Ölçüm/kontrol çıktısı | G4 Doğrulama | Hatalı yml hiç çalışmaz | Kapı devre dışı, sessiz kırılma | Dosyanın geçerli olduğunu kanıtlamak. | Geçersiz yml GitHub tarafından reddedilir ya da yanlış koşar; bu yüzden ölçüm zorunludur. Doğrulanmayan iddia `VERIFICATION REQUIRED` işaretlenir. Atlarsan bozuk kapı üretime girer. |
| 7 | CI kapısı raporu + gerekirse `../.workflows/deployment.md` | CI/deploy raporu | G5 teslim | Dağıtım onayı ayrı kapıda | Onaysız dağıtım | CI sonucunu haber vermek. | Dağıtım akışı ayrı dosyadır (`.workflows/deployment.md`); CI geçince iş bitmez. Rapor, kapıyı orkestratöre teslim eden çıktıdır. Atlarsan iş görünmez kalır. |
| 8 | Rapor yaz; **commit ATMA** | Rapor + `log.md` append | G6 Rapor | Yetki sınırı | Yetkisiz tarih, revert riski | İş bitince haber vermek; commit'i başkasının atması. | Subagent commit atmaz (kök `AGENTS.md` §7/§8); commit yetkisi orkestratörde. Tarih tek elden yazılmazsa iz sürme bozulur. Atlarsan düzensiz geçmişi birlikte toplarsın. |

> **eli10 (basit):** Akış basit: anlamadan başlama, kuralları oku, izin al, tek yeri değiştir, kanıtla ve haber ver.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazılması, tekrarlanabilir ve denetlenebilir bir akış bırakır. Adım atlanırsa (özellikle onay ve ölçüm) hatalı değişiklik doğrudan depoya girer ve tüm ekibin CI'ını etkiler.

### 3.2 Kapılar (Gate)

```text
[G1 KEŞİF: kural+envanter+şablon] --onay--> [G2 ONAY: kullanıcı]
        --> [G3 UYGULAMA: tek dosya] --ölçüm--> [G4 DOĞRULAMA]
        --> [G5 CI + DEPLOY teslim] --> [G6 RAPOR: commit ORCHESTRATÖRDE]
   red: görev geri döner | ölçüm başarısız: VERIFICATION REQUIRED + geri dön
```

| Kapı | Koşul | Red durumunda |
|------|-------|---------------|
| G1 Keşif | Kural + envanter + şablon okundu, sayılar ölçüldü | Görev uygulamaya alınmaz |
| G2 Onay | Kullanıcı onayı yazılı | Değişiklik yapılmaz (protokol gereği) |
| G3 Uygulama | Diff yalnız hedef dosyada | Kapsam daraltılır, tekrar denenir |
| G4 Doğrulama | Yml geçerli + ölçüm kanıtlı | `VERIFICATION REQUIRED` → düzelt → tekrar |
| G5 CI/Deploy | CI yeşil; deploy ayrı onay | [[../.workflows/deployment.md]] red akışı |
| G6 Rapor | Commit ORCHESTRATÖRDE | Subagent commit atmaz |

> **eli10 (basit):** Kapılar, işin her aşamasında "dur, kanıtla, izin al" noktalarıdır.
> **eli15 (detay):** Kapılar ayrı tablodur çünkü red durumunun ne olacağı peşinen yazılmalıdır. Okunması, nerede durulacağını gösterir. Yazılması, hatanın erken yakalanmasını sağlar. Kapı yoksa onaysız yml değişir ve CI sessizce kırılır.

### 3.3 CI Fail Geri Dönüş Yolu (özel durum)

| # | Gözlem | Aksiyon | Sorumlu |
|---|--------|---------|---------|
| 1 | `php-lint` düşer (PHPStan) | Hata → `.ai/.rules/` (kural dosyası yoksa `UNKNOWN`) → düzelt → tekrar | Backend Architect + DevOps |
| 2 | `php-test` düşer | Test/ kod uyumsuzluğu → ilgili katman agent'ı; auth yalnız `--testsuite Unit` koşulur | QA Engineer |
| 3 | `composer-audit` düşer | Bağımlılık güvenlik açığı → Security + DevOps | Security Engineer |
| 4 | `secret-scan` düşer | Sızan anahtar tespiti → `[REDACTED]` + anahtar iptali + log ERROR | Security Engineer |

> **eli10 (basit):** Bir kapı düşerse nereye gidileceği önceden yazılıdır; kimse tahminle düzeltmez.
> **eli15 (detay):** Geri dönüş ayrı yazıldı çünkü fail yolu panik anında en çok aranan bilgidir. Okunması, hatayı doğru ajana yönlendirir. Yazılması, tekrarlı kör düzeltmeyi durdurur. Tablo olmazsa her fail aynı şekilde yanlış yere düşer.

### 3.4 Dosya Bazlı Değişiklik Akışı

| Hedef dosya | Akış (adım özeti) | Kapı | eli10 | eli15 |
|-------------|-------------------|------|-------|-------|
| `workflows/ci.yml` | Şablon + K13 oku → onay → düzenle → ölçümlü doğrulama | G2 + G4 | Yeni iş kuralı ekler gibi, ama önce izin. | En kritik dosyadır; push'ta herkese çalışır. Onaysız değişirse tüm ekip etkilenir. Ölçüm (job adı/satır) olmadan iddia yazılmaz. Kapılar geçince diff dar kalır. |
| `workflows/secret-scan.yml` | Şablon oku → Security kapsamı → onay → düzenle | G2 + G5 | Tarama ayarını güvenlikle birlikte değiştir. | Config (`.gitleaks.toml`) eklemek ayrı karardır; diskte henüz yok (dosya yorumu). Tarama kapanırsa sızıntı avlanmaz. Onay + kapsam doğrulaması zorunlu. |
| `ISSUE_TEMPLATE/01-bug-report.md` | Alan ihtiyacı → onay → düzenle → rapor | G2 | Hata formuna alan eklemek onay ister. | Form GitHub akışında kullanılır; alan değişimi rapor kalitesini doğrudan etkiler. ASCII yazım (`Adimlar`) ölçümle kayıtlıdır, düzeltilmesi vault-sync işlemidir. |
| Yeni `.yml` (ilk kez) | Şablon oku → taslak → kullanıcı onayı → uygulama → kapı | G1 + G2 | Sıfırdan iş, hazır kalıpla girer. | İlk workflow en risklisidir; yanlış tanım ilk push'ta sürpriz çalışır. Şablon (Guardrail #16) ve K13 planı olmadan üretilmez. Onay kaydı denetim izidir. |
| `CLAUDE.md` (talep) | Ölç → işaretle → rapor → vault-sync | G6 | Kural defterine dokunulmaz, talep gider. | Elle düzeltme SSOT'u bozar. Bulunan çelişki (§3.6) raporlanır, düzeltilmez. Kapı, düzeltmenin doğru yerde yapılmasını sağlar. |

> **eli10 (basit):** Her dosyanın kendi adımı ve kapısı vardır; kural defterleri yalnız raporlanır.
> **eli15 (detay):** Dosya bazlı akış ayrı tablodur çünkü tek genel sıra her dosyanın riskini karşılamaz. Okunması, hedefe göre kapıyı gösterir. Yazması, onay ve ölçümün dosyaya göre uygulanmasını sağlar. Tablo olmazsa tüm değişiklikler aynı muameleyi görür ve kritik dosya korumasız kalır.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük ve güvenli | Her şey değişince inceleme kör olur |
| 2 | **Tek sorumluluk** | Kapının tek sahibi olur | "Kapı kimde" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak değer tek yerde (ör. PHP 8.4 tek `env` bloğu) | Her yerde ayrı sürüm → tarama gerekir |
| 4 | **Cihaz izolasyonu** | Ortam/runner farkı yayılmaz | Fark her yere sıçrar, drift |
| 5 | **Vendor karantinası** | 3. taraf aksiyonlar kendi yml'lerinde | Güncelleme bizim kodu bozar, güvenlik yamaları şaşar |

> **eli10 (basit):** Adımlar küçük kutulara bölünmüş; çünkü bir kutuyu değiştirmek diğerini bozmaz.
> **eli15 (detay):** Süreç ayrı dosyalarda tutulur ki her klasör kendi kapısını bilsin: `.github/` kapısı CI, `.workflows/` kapısı süreç, `.ai/` kapısı faz. Aynı yerde toplansa her iş için tüm süreç okunur, odak kaybolur. Okunması, işe başlamadan sırayı gösterir. Bölünmezse "adım atlandı mı" sorusu cevapsız kalır.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Kod/ayar öncesi keşif (kural + envanter + şablon) — onaysız değişiklik yok | Yanlış dosya/yanlış kapı yazımını engeller (kök `AGENTS.md` §4) |
| 2 | Aynı dosya görevde 2. kez okunmaz | Anti-overthink / token israfı (kök `AGENTS.md` §5) |
| 3 | Secret değeri vault `.md` dosyalarına yazılmaz (REDACTED) | Anahtar sızıntısını kapatır |
| 4 | Yeni workflow şablondan türetilir (Guardrail #16) | Standart dışı pipeline doğmasını önler |
| 5 | **Commit subagent tarafından ATILMAZ** | Tarih ve entegrasyon yetkisi orkestratörde (kök `AGENTS.md` §7/§8) |
| 6 | 3 başarısız düzeltme → DUR + 1 kısa soru | Kör deneme döngüsüne girilmez (kök `AGENTS.md` §5) |
| 7 | Ölçülmeyen sayı/iddia yazılmaz → `VERIFICATION REQUIRED` | Zero-Hallucination (kök `AGENTS.md` §3) |

> **eli10 (basit):** Bu kurallar işin hızlı ve doğru yürümesini; izin, ölçüm ve commit disiplinini korur.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile kod farklı hızda değişir; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazılmaları, denetimin tekrarlanmasını garantiler. Kural değişince (kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → [K1: KURAL + ENVANTER + ŞABLON oku] → [K2: ONAY]
  → UYGULA (yalnız hedef yml/md) → [K3: ÖLÇÜM + GEÇERLİLİK]
  → (CI kapısı: ../workflows/*.yml) → [K4: RAPOR — commit yok] → ORKESTRATÖR
```

Adım ve kapıların tamamı §3.1 / §3.2 tablolarındadır; buradaki zincir sırayı özetler.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: workflow` |
| 2 | Bölüm sırası | §1–§7 sabit, ≤3 başlık seviyesi |
| 3 | Adım tablosu | Her adımda çıktı + kapı + neden + atlarsan dolu |
| 4 | Commit kuralı | §3.1 #8 + §4 #5 "ATMAZ" ifadesi mevcut |
| 5 | Placeholder | Dosyada `{{` kalmadı |
| 6 | eli10 + eli15 | §1, §3.1, §3.2, §3.3, §3.4, §4 bloklarında etiketli blok var |
| 7 | Disk kanıtı | ci.yml/secret-scan.yml yorum ve job iddiaları gerçek dosyayla uyumlu |
| 8 | Wiki-link | `[[...]]` formatı; hedefler diskte mevcut |
| 9 | Dokunulmaz | Mevcut `CLAUDE.md` dosyaları değiştirilmedi |
| 10 | Emoji | Dekoratif emoji yok |
| 11 | Uzunluk | 200–350 satır bandı (küçük dizin istisnası) |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Değişiklik protokolü (MEVCUT) |
| Klasör rolleri | [[AGENTS.md]] | Routing + yetki sınırları |
| Klasör context | [[CONTEXT.md]] | Envanter + çelişki kaydı |
| Kök master kurallar | [[../AGENTS.md]] | §4 keşif, §5 anti-overthink, §7 loop |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| CI planı | [[../.ai/architecture/k13-cicd/README.md]] | K13 (ci.yml yorumu) |
| Workflow şablonu | [[../.ai/.templates/infrastructure/github-actions-template.md]] | Yeni workflow iskeleti |
| Deployment akışı | [[../.workflows/deployment.md]] | G5 dağıtım kapısı |
| Secret tarama planı | [[../.ai/architecture/k13-cicd/security-scanning.md]] | K13.4.3 (secret-scan.yml yorumu) |
| Issue şablonu kuralı | [[ISSUE_TEMPLATE/CLAUDE.md]] | Form protokolü (MEVCUT) |
| Issue şablonu kanıtı | `.github/ISSUE_TEMPLATE/01-bug-report.md` | 38 satır form ölçümü |
| CI iş kanıtı | `.github/workflows/ci.yml` | 3 job, kapıların kaynağı |
| Secret tarama kanıtı | `.github/workflows/secret-scan.yml` | GitLeaks job (32 satır) |
| Agent routing | [[AGENTS.md]] | Sahiplik + yetki kapıları |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
