---
title: "CoreMusic — .workflows Agent Talimatları"
type: guide
category: workflow
docType: agents
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/AGENTS.md + kök AGENTS.md"
---

# .workflows — AGENTS.md

**docType:** agents · **Klasör:** `.workflows/` · **Sorumlu:** MO (registry)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[CONTEXT.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `.workflows/` klasöründe **her akış dosyasının sahibini, yetki sınırını ve devreye giriş anını** tanımlar: hangi rol hangi akışı yürütür, hangi kapıdan geçer, kime eskale eder. Kapsam notu: **.workflows = vault-sync/security/session-init gibi süreç akışları** — dosyalar talimat olduğu için sahiplik, kod sahipliğinden çok "süreç sahipliği"dir. Bu dosya türevdir; asıl registry [[../.ai/AGENTS.md]]'dir, çelişkide o kazanır.

| Karar | Kaynak (disk) |
|-------|---------------|
| ADR/vault/documentation → MO (vault-updater) | `.ai/AGENTS.md` §6 routing satırı |
| Güvenlik denetimi → Security Engineer | `.ai/AGENTS.md` §6 security satırı |
| Dağıtım → DevOps Engineer | `.ai/AGENTS.md` §6 CI/CD/deploy satırı |
| Görev dağıtım → Master Orchestrator | `.ai/AGENTS.md` §4 #1 (MO görev tanımı) |
| Her akış öncesi şablon zorunlu (Guardrail #16) | `.ai/AGENTS.md` §14 #6 + `.ai/.templates/frontend/context-template.md` |

> **eli10 (basit):** Bu dosya, sekiz sürecin kimin işi olduğunu ve kimden izin gerektiğini yazar.
> **eli15 (detay):** Ayrı yazıldı çünkü süreç sahipliği, akış içeriğiyle aynı hızda değişmez; akış dosyasına gömülse rol değişince tüm akış revize edilir. Okunması, işe başlamadan doğru ajana gitmeyi sağlar. Yazılması, denetlenebilir dağıtım bırakır. Rol belirsiz olunca herkes her akışı çalıştırır ve kapılar anlamsızlaşır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 8 akış dosyası sahipliği + yetki sınırları (`.workflows/` envanteri) | Genel agent registry'si (SSOT → [[../.ai/AGENTS.md]]) |
| Akış bazlı routing, handover, eskalasyon | Akışların adım detayları → ilgili `*.md` dosyası |
| Onay kapıları: deployment, adr-creation, vault-sync | CI/CD dosya sahipliği → [[../.github/AGENTS.md]] |
| Görev → akış eşlemesi (bu klasör için) | Kod (PHP/JS/CSS) sahipliği → ilgili katman agent'ı |

- **Kullananlar:** MO (birincil — boot/senkron/ADR), Security (denetim), DevOps (dağıtım), tüm agent'lar (halüsinasyon kontrolü), kullanıcı (onay kapıları).
- **Ön koşul:** Envanter ölçülmüş (`.workflows/CONTEXT.md` §3.1 — 9 dosya / 2.442 satır); sayı yoksa `UNKNOWN`.
- **Dokunulmaz:** `CLAUDE.md` — yalnız okunur.

---

## 3. Mimari

### 3.1 Akış Sahipliği Tablosu (disk ölçümü: 8 akış — 2026-10-03)

| Dosya | Sorumlu Rol | Yetki Sınırı | Neden bu rol | Ne zaman devreye girer | eli10 | eli15 |
|-------|-------------|--------------|-------------|------------------------|-------|-------|
| `session-init.md` (249 satır) | **MO** (boot) · tüm agent'lar (okuyucu) | Boot sırası değiştirilemez; okuma listesi MO'da | Oturum standardı koordinasyon işidir | Her oturum başlangıcı | Oturum açılış listesinin sahibi orkestratördür. | Sıra herkes için aynı olmalı; aksi halde agent farklı kapıdan başlar. MO sahip çünkü boot, tüm rolleri etkiler. Ne zaman: her oturumun ilk saniyesinde. Sıra değişirse çelişkili bilgiyle çalışma başlar. |
| `adr-creation.md` (257 satır) | **MO (vault-updater)** · ilgili uzman (içerik) | Karar metni uzmandan, kayıt MO'dan; frozen ADR değişmez | ADR/vault routing'i MO'ya ait | Mimari karar ihtiyacı | Karar defterini kayıt eden vault bakımcısıdır. | İçerik (mimari gerekçe) uzman agent'tan gelir; kaydı ve numarayı MO yapar. Ne zaman: yeni mimari karar verilince. Kayıt MO'suz olursa numarasız/şablonsuz karar doğar. |
| `vault-sync.md` (239 satır, v1.1) | **MO (vault-updater)** | Yalnız senkron; frozen dokunulmaz; append-only log | Vault koordinasyonu MO tekelinde | Vault değişikliği sonrası / kapanış | Vault'un senkronundan tek kişi sorumludur. | Senkron, SSOT'un bütünlüğünü korur; yetki genişletilirse herkes vault yazar. Ne zaman: her değişiklik kapanışında. Atlansa index/log drift'i birikir. |
| `security-audit.md` (269 satır) | **Security Engineer** (birincil) · QA (doğrulama) | Denetim kapsamı Security'de; kapatma onayı kullanıcı/QA | Güvenlik domaini (registry §6) | Güvenlik değişikliği | Güvenlik işinin denetimini güvenlikci yapar. | Denetim bağımsız kapıdır; işi yapan kişi kendi denetimini kapatamaz (ayrım). Ne zaman: auth/middleware/şifreleme değişince. Atlansa açık sessizce üretime girer. |
| `deployment.md` (282 satır) | **DevOps Engineer** · kullanıcı (onay) | Yayına alma onayı kullanıcıda; yml DevOps'ta | CI/CD domaini (registry §5/§6) | Sürüm çıkışı | Yayına alma işinin sahibi DevOps, onayı kullanıcıdadır. | Dağıtım riski koddan farklıdır: geri dönüş planı ister. Ne zaman: sürüm çıkarken. Onaysız yayına girilirse geri dönüş pahalı olur. |
| `hallucination-control.md` (273 satır) | **Tüm agent'lar** (uygulayıcı) · MO (rapor) | İşaret zorunlu; doğrulanmayan silinmez, işaretlenir | Zero-Hallucination herkesin kuralı | Belirsiz bilgi tespiti | Herkesin uyması gereken "emin değilsem yazmam" kuralı. | Kural kişiye değil role bağlıdır; tek ajana verilirse diğerleri serbest kalır. Ne zaman: kanıtsız iddia anında. İşaretlenmezse tahmin vault'a girer. |
| `orchestrator-flow.md` (310 satır) | **Master Orchestrator** | Görev dağıtımı tek elden; roller registry'de | MO görev tanımı (registry §4 #1) | Multi-agent görev | Görevleri dağıtan anahtarın sahibi orkestratördür. | Dağıtım tek elden olmazsa çakışma ve kilit (context lock) sorunları doğar. Ne zaman: birden çok rol içeren işte. Atlansa yanlış ajana görev gider. |
| `architecture-write.md` (511 satır) | **MO (vault-updater)** · ilgili katman agent'ı (içerik) | %31 dosya sınırı, 0 silme, denetim raporu zorunlu | Vault/mimari yazım işi MO + uzman | Mimari dosya yazımı | Toplu mimari yazımın hem sahibi hem denetçisi vardır. | Batch yazımın kendi güvenlik kuralları vardır (0 silme); tek başına yapılırsa kayıp yaşanır. Ne zaman: katman md'leri toplu güncellenirken. Kural atlansa veri kaybı olur. |

> **eli10 (basit):** Her akışın tek bir sahibi var; güvenlik ve dağıtımda iş yapanla denetleyen ayrıdır.
> **eli15 (detay):** Sahiplik ayrı yazıldı çünkü yetki ile içerik farklı belgelerdedir. Okunması, kime sorulacağını ve kimden onay alınacağını gösterir. Yazılması, eskalasyonu ve denetimi mümkün kılar. Sahip belirsiz olunca akış "kimse"nin olur ve kapılar formaliteye döner.

### 3.2 Görev → Akış → Rol Routing

| Tetikleyici (keyword) | Akış dosyası | Birincil rol | İkincil rol |
|-----------------------|--------------|--------------|-------------|
| oturum, boot, session, başlangıç | [[session-init.md]] | MO | Tüm agent'lar |
| ADR, karar, decision | [[adr-creation.md]] | MO (vault-updater) | İlgili uzman |
| senkron, sync, index, kapanış | [[vault-sync.md]] | MO (vault-updater) | — |
| güvenlik, audit, denetim, OWASP | [[security-audit.md]] | Security Engineer | QA Engineer |
| deploy, dağıtım, sürüm, release | [[deployment.md]] | DevOps Engineer | Security Engineer |
| halüsinasyon, doğrula, VERIFICATION | [[hallucination-control.md]] | Tüm agent'lar | MO (rapor) |
| multi-agent, görev dağıt, orkestrasyon | [[orchestrator-flow.md]] | Master Orchestrator | — |
| mimari yaz, katman md, batch | [[architecture-write.md]] | MO (vault-updater) | İlgili katman agent'ı |

> **eli10 (basit):** Hangi kelime gelirse gelsin, hangi listenin açılacağı burada yazıyor.
> **eli15 (detay):** Routing ayrı tablo çünkü registry'nin tamamı değil, yalnız bu klasörün özeti burada. Okunması, görevin ilk saniyede doğru ajana gitmesini sağlar. Yazması, hızlı dağıtımı garantiler. Tablo olmazsa her görev orkestratörde birikir veya yanlış ajanda boşa çalışır.

### 3.3 Yetki Sınırları (bu klasör için yasaklar)

| # | Sınır | İhlal durumunda |
|---|-------|-----------------|
| 1 | Akış dosyasını sahibi olmayan rol DEĞİŞTİRMEZ | Yetkisiz süreç değişikliği → revert + log |
| 2 | Frozen ADR metni kopyalanmaz/değiştirilmez, yalnız wiki-link | revert |
| 3 | `log.md` yalnız append (geçmiş satıra dokunulmaz) | SSOT/audit ihlali |
| 4 | Subagent `git commit` ATMAZ | Yetkisiz commit → orkestratör |
| 5 | Doğrulanmayan bilgi `VERIFICATION REQUIRED` işaretlenir, yazılmaz | Halüsinasyon etiketi |
| 6 | Kullanıcı onayı gerektiren kapılar (deployment, yeni akış) onaysız geçilmez | Onaysız yayına alma / süreç değişikliği |
| 7 | Mevcut `CLAUDE.md` bu görevde düzenlenmez | Dokunulmazlık ihlali → geri al |

> **eli10 (basit):** Kimse başka birinin akışını değiştirmez; onaysız yayına alma, izinsiz commit ve uydurma bilgi yoktur.
> **eli15 (detay):** Sınırlar ayrı yazılır çünkü her madde bir kaybı (süreç bozulması, veri kaybı, sızıntı) önler. Okunması, işe başlamadan hattı gösterir. Yazması, denetimi mümkün kılar. Sınır kalkarsa süreçler sessizce değişir ve kapılar anlamını kaybeder.

### 3.4 Komşu İlişkiler

| Komşu | Yön | Ne taşır |
|-------|-----|----------|
| `../.ai/AGENTS.md` | registry → .workflows | Yetkinin kaynağı (SSOT) |
| `../.ai/WORKFLOW.md` | vault → .workflows | Faz kapıları; bu klasör uygulama detayı |
| `../.ai/.decisions/index.md` | .workflows → vault | `adr-creation.md` çıktı hedefi |
| `../.github/` | .workflows ↔ .github | CI/deploy kapısı (`deployment.md`) |
| `../.claude/settings.json` | .workflows → Claude Code | `instructions` boot listesi |
| `../AGENTS.md` (kök) | kök → .workflows | §7 loop, commit kuralı |

> **eli10 (basit):** Yetki vault'tan gelir, çıktı yine vault'a ve CI kapısına gider.
> **eli15 (detay):** İlişkiler ayrı tabloda çünkü çelişkide hangi kaynağın kazanacağı belli olsun (registry kazanır). Okunması, türev-SSOT sınırını gösterir. Yazması, bağımlılık görünür olur. Tablo unutulursa bu dosya yanlışlıkla ikinci registry sanılır.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada, diff küçük | Her şey değişince inceleme kör olur |
| 2 | **Tek sorumluluk** | Her akışın tek sahibi olur | "Bunu kim durdurdu" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Yetki tek registry'de (`../.ai/AGENTS.md`) | Her akışta ayrı yetki → tutarsızlık |
| 4 | **Cihaz izolasyonu** | Ortam/agent farkı yayılmaz | Fark tüm akışlara sıçrar, drift |
| 5 | **Vendor karantinası** | 3. taraf araç adımları kendi akışında | Dış kod içimize karışır, güncellemede kaybolur |

> **eli10 (basit):** Bilgiler küçük kutulara ayrılmış; bir kutuyu değiştirmek ötekini bozmaz.
> **eli15 (detay):** Roller ayrı yazılır ki her akışın sahibi tek satırda görünsün. Okunması, kime gidileceğini hızlandırır. Yazması, denetlenebilir dağıtım bırakır. Tek dosyada toplansa hem rol hem akış değişir, sorumluluk kaybolur.

---

## 4. Kurallar

| # | Kural | Neden var | Kaynak |
|---|-------|-----------|--------|
| 1 | Görev önce §3.2 routing'den geçer | Yanlış ajana giden görev geri döner | [[../.ai/AGENTS.md]] §6 |
| 2 | ADR kaydı MO; frozen metin dokunulmaz | Karar geçmişi bozulmaz | `adr-creation.md` + registry §5 |
| 3 | Denetimi işi yapan kapatamaz (Security/QA ayrımı) | İç denetim körleşmesi önlenir | `security-audit.md` + registry §9 |
| 4 | Deployment onayı kullanıcıda | Onaysız yayına alma olmaz | `deployment.md` + kök `AGENTS.md` §1 |
| 5 | Handover onaysız tamamlanmaz (max 3 retry) | Yetki devri kaybolmaz | [[../.ai/AGENTS.md]] §9 |
| 6 | Escalation L1 → L2 → L3 → insan (30/60/120 sn) | Kilitlenme uzamaz | [[../.ai/AGENTS.md]] §10 |
| 7 | Subagent commit ATMAZ | Tarih/entegrasyon orkestratörde | Kök `AGENTS.md` §7/§8 |
| 8 | Çelişkide disk kazanır; doğrulanamayan `VERIFICATION REQUIRED` | Zero-Hallucination + SSOT | Kök `AGENTS.md` §3 |

> **eli10 (basit):** Kurallar, işin doğru kişiye, izinle ve kanıtla gitmesini; commit'in hep orkestratörde kalmasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile akış içeriği farklı hızda değişir. Okunmaları, başlamadan kapıları gösterir. Yazması, denetimi tekrarlanabilir kılar. Kural değişince (registry revizyonu) yalnız bu tablo güncellenir; akış dosyaları sessizce değişmez.

---

## 5. Workflow

```text
GÖREV → ROUTING (§3.2: tetikleyici → akış → rol) → SAHİP DOĞRULA (§3.1)
  → KURAL + AKIŞ OKU (CLAUDE.md + hedef *.md) → UYGULA → KAYIT (log append)
  → (onay kapıları: deployment/ADR) → RAPOR (commit ORCHESTRATÖRDE)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Tetikleyiciye göre akış seç (§3.2) | Doğru `*.md` | Yanlış akış yanlış kapıda durur | Eksik kapı, uyumsuz süreç |
| 2 | Sahip rolü doğrula (§3.1) | Atanan rol | Yetki sınırı baştan belli | Yetkisiz değişiklik, geri alma |
| 3 | [[CLAUDE.md]] + akış dosyasını oku | Kural + adımlar | Klasör kuralları orada | Kural ihlali |
| 4 | Uygula + kayıt (`log.md` append) | Çıktı + iz | İzlenebilirlik | Kayıp geçmiş, drift |
| 5 | Onay kapısı (gerekirse) + rapor | Onaylı teslim | Yetki devri görünür | Onaysız yayına alma |

> **eli10 (basit):** Doğru listeyi seç, sahibini bul, kuralı oku, uygula, kaydet ve haber ver.
> **eli15 (detay):** Adımlar ayrı satırlardır çünkü her adımın çıktısı ve gerekçesi ölçülür. Okunması, sırayı önceden bilmeyi sağlar. Yazması, tekrarlanabilir dağıtım bırakır. Sahip adımı atlanırsa yetkisiz rol süreci değiştirir.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: agents` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada `{{` kalmadı |
| 4 | 4 ağırlık sütunu | §3.1'de ne için / neyden / neden var / ne zaman dolu |
| 5 | eli10 + eli15 | §1, §3, §4, §5 bloklarında etiketli blok; eli10 ≤2 cümle, eli15 3-4 cümle |
| 6 | Disk kanıtı | 8 akış sahipliği + satır sayıları ölçümle uyumlu |
| 7 | Wiki-link | `[[...]]` formatı; hedefler diskte mevcut |
| 8 | Dokunulmaz | Mevcut `CLAUDE.md` değiştirilmedi |
| 9 | Türevlik | SSOT iddiası yok; `authority` türev yazıldı |
| 10 | Emoji | Dekoratif emoji yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Tetikleyici/çıktı + protokol (MEVCUT) |
| Klasör context | [[CONTEXT.md]] | Envanter + çelişki kaydı |
| Klasör süreç | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | Routing, handover, escalation |
| Kök master kurallar | [[../AGENTS.md]] | §7 loop, commit kuralı |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| Oturum akışı | [[session-init.md]] | Boot sahipliği |
| ADR akışı | [[adr-creation.md]] | Karar kaydı sahipliği |
| Senkron akışı | [[vault-sync.md]] | Kapanış sahipliği |
| Denetim akışı | [[security-audit.md]] | Güvenlik sahipliği |
| Dağıtım akışı | [[deployment.md]] | Yayın sahipliği + onay |
| CI kapısı | [[../.github/AGENTS.md]] | `.yml` sahipliği |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
