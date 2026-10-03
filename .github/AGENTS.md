---
title: "CoreMusic — .github Agent Talimatları"
type: guide
category: infrastructure
docType: agents
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/AGENTS.md + kök AGENTS.md"
---

# .github — AGENTS.md

**docType:** agents · **Klasör:** `.github/` · **Sorumlu:** MO (registry)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[CONTEXT.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `.github/` klasöründe **kimin hangi dosyaya ne zaman dokunabileceğini** tanımlar: rol, yetki sınırı, devreye giriş anı ve eskalasyon. Kapsam notu: **.github = CI/CD + issue şablonları (DevOps domain)** — buradaki dosyalar depo davranışını değiştirdiğinden yetki sınırı koddan daha dardır. Bu dosya türevdir; asıl registry [[../.ai/AGENTS.md]]'dir, çelişkide o kazanır.

| Karar | Kaynak (disk) |
|-------|---------------|
| `*.yml` sahibi = DevOps Engineer | [[../.ai/AGENTS.md]] §5 Domain Boundaries (`*.yml / *.yaml`) |
| Secret tarama = Security + DevOps | `secret-scan.yml` (GitLeaks job) + `.ai/AGENTS.md` §6 keyword routing |
| CI test geçişi = QA standardı | `ci.yml` `php-test` job + `.ai/AGENTS.md` §16 (coverage/CI hedefi) |
| Vault doküman = MO (vault-updater) | `.ai/AGENTS.md` §6 "vault, documentation" satırı |
| Yetkisiz commit yasağı | Kök `AGENTS.md` §7/§8 — subagent commit atmaz |

> **eli10 (basit):** Bu dosya, depo ayarlarını kimin değiştirebileceğini ve kimden izin alınacağını yazar.
> **eli15 (detay):** Ayrı yazıldı çünkü rol bilgisi ile dosya içeriği farklı hızda değişir; `ci.yml` içine gömülse her CI revizyonunda rol revize edilir. Okunması, işe başlamadan yetki sınırını gösterir. Yazılması, denetlenebilir bir dağıtım bırakır. Rol net olmazsa herkes yml düzenler ve CI sessizce kırılır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.github/` içindeki dosya sahipliği ve yetki sınırları | `.ai/` vault registry'si (SSOT → [[../.ai/AGENTS.md]]) |
| CI/CD keyword routing (bu klasöre gelen görevler) | Ajanların genel yetkileri ve iletişim protokolü (registry §6–§12) |
| Onay kapıları: yeni workflow, secret, issue şablonu | GitHub arayüz ayarları (org/repo seviyesi, diskte değil) |
| Eskalasyon ve handover (bu klasör için) | İş mantığı, PHP/JS/CSS kodu (ilgili katman agent'ı) |

- **Kullananlar:** MO (routing + bu doküman), DevOps Engineer (yürütücü), Security/QA (denetim), Claude Code (boot okuması).
- **Ön koşul:** Envanter ölçülmüş olmalı (`.github/CONTEXT.md` §3.1 — 5 dosya); sayı yoksa `UNKNOWN`.
- **Dokunulmaz:** `CLAUDE.md` (kök + `ISSUE_TEMPLATE/`) — yalnız okunur.

---

## 3. Mimari

### 3.1 Dosya Sahipliği Tablosu (disk ölçümü: 5 dosya — 2026-10-03)

| Dosya | Sorumlu Rol | Yetki Sınırı | Neden bu rol | Ne zaman devreye girer | eli10 | eli15 |
|-------|-------------|--------------|-------------|------------------------|-------|-------|
| `workflows/ci.yml` | **DevOps Engineer** (birincil) · QA Engineer (test kapsamı) | Düzenleme yalnız onayla; job silme/ekleme yasak | CI/CD domaini (`.ai/AGENTS.md` §5) | CI davranışı değişince, yeni dizin test kapsamına girince | Depoya giriş kapılarının sahibi DevOps'tur. | Yml, push/PR'da herkese çalıştığı için tek elden değişir. QA yalnız test job'ının kapsamını (matrix) ister. Ne zaman: yeni servis test edileceğinde ya da lint/test ajanı ekleneceğinde. Onaysız değişiklik tüm ekibin CI'ını kilitler. |
| `workflows/secret-scan.yml` | **Security Engineer** (kapsam) · DevOps Engineer (yml) | Config (`.gitleaks.toml`) eklemek ADR/Security onayı ister | Secret taraması güvenlik domaini | Tarama politikası değişince, ihlal tespit edilince | Gizli anahtar tarayıcısının ayarları güvenlikcinin işidir. | Tarama ajanı güvenlikten gelir çünkü yanlış kural sızıntıya göz yumar; yml'yi DevOps yazar çünkü pipeline sahibi odur. Ne zaman: yeni secret kuralı ya da ihlal vakası çıkınca. Onaysız config değişirse tarama körleşir. |
| `ISSUE_TEMPLATE/01-bug-report.md` | **MO** (doküman) · QA Engineer (alan önerisi) | Alan/etiket değişikliği kullanıcı onaylı | Rapor biçimi doküman alanı | Rapor alanları değişince | Hata formunu doküman sorumlusu günceller. | Form doküman sayıldığı için MO sahiptir; içerik (hangi bilgi istenir) QA önerir. Ne zaman: yeni hata türünde ek bilgi gerektiğinde. Onaysız değişirse ekipler eksik rapor açar. |
| `CLAUDE.md` (kök + `ISSUE_TEMPLATE/`) | **MO (vault-updater)** | **DOKUNULMAZ** — düzeltme yalnız vault-sync ile | SSOT hiyerarşisi + bu görev kısıtı | Vault-sync / kullanıcı onayı | Kural defterlerini yalnız vault bakımcısı günceller. | Kural dosyası kutsaldır; çelişki bile olsa düzeltme aceleye getirilmez, ölçülüp vault-sync'e yazılır. Ne zaman: yalnız senkronizasyon oturumunda. Yanlış elle düzenleme git geçmişi ve SSOT'u bozar. |
| Yeni `.yml` (gelecek) | **DevOps Engineer** | Taslak → kullanıcı onayı → uygulama | CI domaini | Yeni kapı/deploy ihtiyacı | Yeni otomatik kural, DevOps'un taslağıyla girer. | Sıfırdan workflow, şablondan türetilir (Guardrail #16). Ne zaman: yeni test/tarama/deploy ihtiyacı doğunca. Onaysız eklenen workflow ilk push'ta sürpriz çalışır ve kaynak/israf üretir. |

> **eli10 (basit):** Her dosyanın tek bir sahibi var; en hassası kural defterleri, o yüzden onlara kimse ellemez.
> **eli15 (detay):** Sahiplik ayrı yazıldı çünkü yetki ile içerik farklı belgelerdedir. Okunması, kime sorulacağını gösterir. Yazılması, denetimi ve eskalasyonu mümkün kılar. Sahip belirsiz olunca herkes düzenler, kimse sahip çıkmaz ve CI sessizce kırılır.

### 3.2 Görev → Rol Routing (bu klasör keyword'leri)

| Keyword grubu | Birincil rol | İkincil rol | Kaynak |
|---------------|--------------|-------------|--------|
| workflow, ci, yml, pipeline, github actions, runner, deploy | DevOps Engineer | QA Engineer | `.ai/AGENTS.md` §6 (CI/CD satırı) |
| secret, gitleaks, token, sızıntı, security scan | Security Engineer | DevOps Engineer | `.ai/AGENTS.md` §6 (security satırı) |
| issue, bug report, etiket, şablon, template | MO (vault-updater) | QA Engineer | `.ai/AGENTS.md` §6 (template/vault satırları) |
| test, phpunit, coverage (CI içinde) | QA Engineer | DevOps Engineer | `.ai/AGENTS.md` §6 (test satırı) |
| CONTEXT/AGENTS/WORKFLOW dokümanı üretimi | MO (vault-updater) | — | Guardrail #16 + `.ai/.templates/frontend/context-template.md` |

> **eli10 (basit):** Görev gelince kelimesine göre doğru kişiye gider: yml işi DevOps'a, sızıntı işi güvenlikye.
> **eli15 (detay):** Routing ayrı tablo çünkü registry'nin tamamı bu dosyada değil, yalnız bu klasörün özeti. Okunması, yanlış ajana giden görevi baştan önler. Yazılması, hızlı dağıtımı sağlar. Tablo boş kalısa her görev orkestratörde birikir.

### 3.3 Yetki Sınırları (yasaklar)

| # | Sınır | İhlal durumunda |
|---|-------|-----------------|
| 1 | Subagent `git commit` ATMAZ (kök `AGENTS.md` §7/§8) | Yetkisiz commit → revert + log |
| 2 | Secret değeri `.md`/`.log`/`.json` içine yazılmaz | `[REDACTED]` + anahtar iptali (Security) |
| 3 | Mevcut `CLAUDE.md` dosyaları düzenlenmez | Dokunulmazlık ihlali → derhal geri al |
| 4 | `.yml`'e kullanıcı onayı olmadan job eklenmez/çıkarılmaz | CI kapısı beklenmedik çalışır/kalkar |
| 5 | Vault `.ai/` içine yazma yalnız MO yetkisinde | SSOT ihlali → disk kazanır + işaretlenir |
| 6 | Frozen ADR metni kopyalanmaz, yalnız wiki-link verilir | revert |

> **eli10 (basit):** Bunlar kesin yasaklar: izinsiz commit, gizli bilgi yazma ve kimsesiz dosya değişikliği yok.
> **eli15 (detay):** Sınırlar ayrı yazıldı çünkü ceza değil gerekçe taşırlar: her madde bir kaybı (sızıntı, kör commit, SSOT bozulması) önler. Okunması, işe başlamadan hattı gösterir. Yazılması, denetimi mümkün kılar. Sınır kalkarsa onaysız değişiklik doğrudan depoya girer.

### 3.4 Komşu İlişkiler (kim hangi klasörle konuşur)

| Komşu | Yön | Ne taşır |
|-------|-----|----------|
| `../.ai/AGENTS.md` | registry → .github | Yetkinin kaynağı (SSOT); bu dosya türevdir |
| `../.ai/architecture/k13-cicd/` | plan → .github | CI planı (README, ci-pipeline, security-scanning) |
| `../.ai/.templates/infrastructure/github-actions-template.md` | şablon → .github | Yeni workflow iskeleti (Guardrail #16) |
| `../.workflows/deployment.md` | süreç → .github | Dağıtım onay akışı (CI kapısını tamamlar) |
| `../AGENTS.md` (kök) | kök → .github | Master kurallar (keşif, anti-overthink, commit) |

> **eli10 (basit):** Yetki bu dosyadan değil, vault'taki ana registry'den gelir; burası sadece bu klasörün özeti.
> **eli15 (detay):** İlişkiler ayrı tabloda çünkü çelişkide hangi kaynağın kazanacağı belli olsun: registry kazanır. Okunması, türev-SSOT sınırını gösterir. Yazılması, bağımlılık görünür olur. Tablo unutulursa bu dosya yanlışlıkla ikinci SSOT sanılır.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada, diff küçük | Her şey değişince inceleme kör olur |
| 2 | **Tek sorumluluk** | Her rolün tek sahibi ve tek yetkisi olur | "Kim yaptı" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Yetki tek registry'de (`../.ai/AGENTS.md`) | Her klasörde ayrı yetki → tutarsızlık, tarama gerekir |
| 4 | **Cihaz izolasyonu** | Ortam/runner farkı yayılmaz | Fark tüm işlere sıçrar, drift |
| 5 | **Vendor karantinası** | 3. taraf aksiyonlar kendi yml'lerinde | Dış kod güncellenemez, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler küçük kutulara ayrılmış; çünkü bir kutuyu değiştirmek ötekini bozmaz.
> **eli15 (detay):** Roller ayrı yazılır ki her dosyanın sahibi tek satırda görünsün. Okunması, kime gidileceğini hızlandırır. Yazılması, denetlenebilir dağıtım bırakır. Tek dosyada toplansa hem rol hem içerik hem süreç değişir, sorumluluk kaybolur.

---

## 4. Kurallar

| # | Kural | Neden var | Kaynak |
|---|-------|-----------|--------|
| 1 | Görev önce keyword routing'den geçer (§3.2) | Yanlış ajana giden görev geri döner, zaman kaybı olmaz | [[../.ai/AGENTS.md]] §6 |
| 2 | Yeni workflow: taslak → kullanıcı onayı → uygulama | Onaysız CI kapısı çalışmaya başlar | [[CLAUDE.md]] §4 |
| 3 | Handover onaysız tamamlanmaz (max 3 retry) | Yetki devri sessizce kaybolmaz | [[../.ai/AGENTS.md]] §9 |
| 4 | Escalation: L1 → L2 → L3 → insan (30/60/120 sn) | Kilitlenme süresi uzamaz | [[../.ai/AGENTS.md]] §10 |
| 5 | Subagent commit ATMAZ | Tarih/entegrasyon yetkisi orkestratörde | Kök `AGENTS.md` §7/§8 |
| 6 | Çelişki durumunda disk ölçümü kazanır | SSOT korunur | [[../.ai/CLAUDE.md]] Hard Guardrails |
| 7 | Doğrulanamayan iddia `VERIFICATION REQUIRED` | Halüsinasyon yasak | Kök `AGENTS.md` §3 |

> **eli10 (basit):** Kurallar, işin doğru kişiye ve izinle gitmesini; commit'in hep orkestratörde kalmasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile dosya içeriği farklı hızda değişir. Okunmaları, başlamadan önce kapıları gösterir. Yazılmaları, denetimi tekrarlanabilir kılar. Kural değişince (registry revizyonu) yalnız bu tablo güncellenir; `ci.yml` sessizce değişmez.

---

## 5. Workflow

```text
GÖREV → KEYWORD ROUTING (§3.2) → DOSYA SAHİBİ DOĞRULA (§3.1)
  → ONAY KAPISI (yml/secret/şablon: kullanıcı onayı) → UYGULA (yalnız hedef dosya)
  → KAPI (ilgili kontrol) → RAPOR (log append; commit ORCHESTRATÖRDE)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Routing tablosundan rol seç | Atanan rol | Yetki sınırı baştan belli olsun | Yetkisiz değişiklik, geri alma |
| 2 | [[CLAUDE.md]] + [[CONTEXT.md]] oku | Kural + envanter | Klasör kuralları ve sayılar | Çelişkili bilgiyle çalışma |
| 3 | Onay kapısı (gerekirse) | Onaylı istek | Yalnız kullanıcı onaylı yml değişir | Sürpriz CI davranışı |
| 4 | Değişiklik + ölçüm | Diff + ölçüm kaydı | Sayı iddiası kanıtlanır | Halüsinasyon raporu |
| 5 | Rapor + `log.md` append | Kayıt | İzlenebilirlik | Kayıp geçmiş, sorumluluk belirsizliği |

> **eli10 (basit):** İş önce doğru kişiye gider, izin alınır, değişiklik ölçülür ve kayda geçer.
> **eli15 (detay):** Adımlar ayrı satırlardır çünkü her adımın çıktısı ve gerekçesi ölçülür. Okunması, sırayı önceden bilmeyi sağlar. Yazılması, tekrarlanabilir dağıtım bırakır. Onay kapısı atlanırsa yetkisiz değişiklik depoya girer.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: agents` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada `{{` kalmadı |
| 4 | 4 ağırlık sütunu | §3.1'de ne için / neyden / neden var / ne zaman dolu |
| 5 | eli10 + eli15 | §1, §3, §4, §5 bloklarında etiketli blok; eli10 ≤2 cümle, eli15 3-4 cümle |
| 6 | Disk kanıtı | 5 dosya sahipliği ölçümle uyumlu; registry iddiaları `[[../.ai/AGENTS.md]]` |
| 7 | Wiki-link | `[[...]]` formatı; hedefler diskte mevcut |
| 8 | Dokunulmaz | Mevcut `CLAUDE.md` dosyaları değiştirilmedi |
| 9 | Türevlik | SSOT iddiası yok; `authority` türev yazıldı |
| 10 | Emoji | Dekoratif emoji yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Bu klasör boot kuralı (MEVCUT — değişmez) |
| Klasör context | [[CONTEXT.md]] | Envanter + çelişki kaydı |
| Klasör süreç | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | Routing, handover, escalation |
| Kök master kurallar | [[../AGENTS.md]] | §7 execution loop, commit kuralı |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| DevOps profili | [[../.ai/.agents/devops-engineer.md]] | `*.yml` sahibi rol profili |
| CI planı | [[../.ai/architecture/k13-cicd/README.md]] | K13 iş planı |
| Deployment akışı | [[../.workflows/deployment.md]] | Dağıtım onay kapısı |
| CI iş kanıtı | `.github/workflows/ci.yml` | 3 job — sahiplik dayanağı |
| Secret tarama kanıtı | `.github/workflows/secret-scan.yml` | GitLeaks kapsamı |
| Issue şablonu kanıtı | `.github/ISSUE_TEMPLATE/01-bug-report.md` | Form sahipliği dayanağı |
| Alt dizin kuralı | [[ISSUE_TEMPLATE/CLAUDE.md]] | Şablon protokolü (MEVCUT) |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
