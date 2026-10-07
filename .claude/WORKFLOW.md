---
title: "CoreMusic — .claude Klasör İş Akışı"
type: docs
category: config
docType: workflow
date: 2026-10-07
updated: 2026-10-07
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/WORKFLOW.md + kök AGENTS.md"
---

# .claude — WORKFLOW.md

**docType:** workflow · **Klasör:** `.claude/` · **Sorumlu:** MO (workflow)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[CONTEXT.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `.claude/` içinde bir görevin (ayar değişikliği, skill üretimi, boot düzeltme talebi) adım adım nasıl yürütüldüğünü tanımlar: her adımın çıktısı, girdisi/kapısı, neden olduğu ve atlanırsa ne olacağı. Kapsam notu: **.claude = Claude Code ayarları + skill kopyaları** — buradaki değişiklik tüm oturumları etkilediği için kapılar (onay + secret denetimi + ölçüm) zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Boot listesi `settings.json` `instructions[]` (4 kayıt) | `.claude/settings.json` |
| Yeni skill şablondan türetilir | `.ai/AGENTS.md` §14 #6 (Guardrail #16) |
| Secret değeri vault `.md`'ye yazılmaz | Kök `AGENTS.md` §3 (REDACTED) |
| Geçici plan vault'a taşınmaz | `.claude/plans/README.md` |
| Commit subagent'a ait değil | Kök `AGENTS.md` §7/§8 |

> **eli10 (basit):** Bu akış, ayar ve yetenek değişikliklerinin izinli, ölçülü ve gizli bilgi sızdırmadan yapılmasını sağlar.
> **eli15 (detay):** Ayrı yazıldı çünkü süreç ile içerik farklı hızda değişir; `settings.json` içine gömülse araç okuyamaz. Okunması, işe başlamadan kapıları gösterir. Yazması, tekrarlanabilir ve denetlenebilir akış bırakır. Akış olmazsa onaysız ayar değişir, tüm oturumlar etkilenir.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Ayar değişikliği (`settings*.json`) akışı | `.ai/` vault süreçleri → [[../.ai/WORKFLOW.md]] |
| Skill üretimi/güncelleme akışı (şablon + domain içerik) | Skill içeriğinin kendi adımları → ilgili `skills/*/SKILL.md` |
| Subagent tanımı ekleme/güncelleme akışı (`agent/` — 118 dosya, onaylı) | Subagent çalışma zamanı davranışı (runtime → UNKNOWN) |
| Slash komutu ekleme/güncelleme akışı (`command/` — 24 dosya, onaylı) | Komutun tetiklediği işin kendi süreci → hedef skill/workflow |
| Boot düzeltme talebi akışı (dokunulmaz → rapor) | CI/CD kapıları → [[../.github/workflows/ci.yml]] |
| Kapılar: kural → onay → uygulama → ölçüm → secret → rapor | AI aracının runtime davranışı (diskte değil → UNKNOWN) |

- **Kullananlar:** Kullanıcı (onay), MO (doküman/kayıt), Security (secret denetimi), ilgili domain agent'ı (skill içeriği), araç (çalıştırıcı).
- **Ön koşul:** Envanter ölçülmüş (`.claude/CONTEXT.md` §3.1–§3.2 — 304 dosya); şablon okunmuş (Guardrail #16); hedef dosya diskte mevcut.

---

## 3. Mimari

### 3.1 Adım Tablosu (adım → çıktı → girdi/kapı → neden → atlarsan ne olur)

| # | Adım | Çıktı | Girdi / Kapı | Neden | Atlarsan ne olur | eli10 | eli15 |
|---|------|-------|--------------|-------|------------------|-------|-------|
| 1 | Görev tanımını netleştir | Onaylı görev özeti | Görev kapısı (soru/onay) | Yanlış iş yapmayı önler | İstenmeyen değişiklik, revert | Önce ne istendiğini anlamak. | Görev kapısı onaysız sonraki aşamaya geçmez (kök `AGENTS.md` §1). Tanım okunmadan başlarsan beklenti ile iş ayrışır. Kapıdan geçince ölçülebilir çıktı (özet) elde edersin. Atlarsan yanlış iş üretimi ve zaman kaybı. |
| 2 | Routing + sahip doğrula ([[AGENTS.md]] §3.1–§3.2) | Atanan rol + hedef dosya | Yetki kapısı | Yetki sınırı baştan belli olsun | Yetkisiz değişiklik, geri alma | Kimin işi olduğunu bulmak. | Sahiplik tablosu dosyaya göre verilir; ayar onaylı, skill domainde, CLAUDE dokunulmaz. Yetki doğrulanmadan başlarsan başka birinin alanına girersin. Kapı geçince doğru muhatap belli olur. Atlarsan geri alınabilirlik ve sorumluluk kaybolur. |
| 3 | Kural + envanter oku ([[CLAUDE.md]] + [[CONTEXT.md]]) | Kural listesi + 304 dosyalık ölçüm | G1 Keşif | Klasör yasakları ve gerçek sayılar | Çelişkili/uydurma bilgiyle çalışma | Kural ve dosya sayısını öğrenmek. | Kurallar klasör dosyasındadır; ölçüm iddiaların tek kanıtıdır. Okunmadan başlarsan REDACTED/dokunulmazlık gibi sınırları görmezsin. Kapı geçince elinde kanıt ve sınır vardır. Atlarsan uydurma envanter ve kural ihlali. |
| 4 | Yeni skill ise şablon oku (Guardrail #16) | Şablon iskeleti | G1 Keşif (şablon zorunlu) | Tutarlı iskelet + frontmatter | Şablonsuz skill, yapı sapması | Yeni işi hazır kalıba koymak. | Şablon `.ai/.templates/` altındadır ve zorunludur. Okunmadan yazılan skill iskeletsiz kalır ve denetimde reddedilir. Şablonlu üretim tekrar edilebilir olur. Atlarsan herkes farklı skill yazar. |
| 5 | Kullanıcı onayı (ayar/skill) | Onay kaydı | **G2 Onay** (zorunlu) | Sürpriz izin/MCP değişikliği olmasın | Güvenlik sürprizi, tüm oturumlar etkilenir | Birinin "evet" demesini beklemek. | Onay, Faz 1 kuralının kapısıdır. Onaysız `settings.json` değişirse izin sınırı sessizce oynar. Onay kaydı denetim izi bırakır. Atlarsan yetkisiz davranış ve revert riski. |
| 6 | Değişikliği uygula (yalnız hedef dosya) | Diff | G3 Kapsam | Kapsam disiplini | Fazla dosyaya dokunma, çakışma | İstenen yeri değiştirmek. | Eşzamanlı oturumlar vardır; diff kilitli kalınca inceleme ve revert kolay olur. Kapsam genişlerse başka oturumun işi bozulur. Atlarsan çakışma ve temizlik işi. |
| 7 | Ölçüm + `vault-utf8-writer verify` | Doğrulama çıktısı | G4 Doğrulama | UTF-8 + yapı bozulmaz | Bozuk dosya, mojibake | Dosyanın sağlam olduğunu kanıtlamak. | Verify, BOM/UTF-8/mojibake raporu verir. Doğrulanmayan iddia `VERIFICATION REQUIRED` işaretlenir. Kapı geçmeyen iş tamamlanmış sayılmaz. Atlarsan bozuk dosya üretime girer. |
| 8 | Secret denetimi: anahtar `.md`'ye yazıldı mı? | REDACTED kontrolü | **G5 Secret** | Anahtar sızıntısını kapatır | Sızan anahtar → iptal + log ERROR | Gizli bilginin yazılmadığını kontrol etmek. | Ayar dosyasında API anahtarı vardır; onu `.md`'ye kopyalamamak zorunludur. Kapı, kök `AGENTS.md` §3 ve `EXA_API_KEY` (REDACTED) kuralının uygulamasıdır. Atlarsan anahtar yayılır ve iptal edilir. |
| 9 | Boot düzeltme ise RAPOR (dokunma) | Talep satırı | G2/G6 | Dokunulmaz dosya elle düzeltilmez | SSOT ihlali, boot ile vault farkı büyümesi | Eksik/kırık referansı bildirmek. | `settings.json` `references.rules` örneği gibi eksikler işaretlenip vault-sync'e gider. Düzeltilirse yer tutarlılık bozulur; düzeltme yetkisi orkestratörde/vault-sync'tedir. Atlarsan eksiklik gizlenir. |
| 10 | Rapor + `log.md` append; **commit ATMA** | Rapor + kayıt | G6 Rapor | Yetki sınırı | Yetkisiz tarih, revert riski | İş bitince haber vermek. | Subagent commit atmaz (kök `AGENTS.md` §7/§8). Kayıt, kapıyı orkestratöre teslim eden çıktıdır. Atlarsan iş görünmez kalır ya da düzensiz commit oluşur. |

> **eli10 (basit):** Akış: anlamadan başlama, sahibini ve kuralı bul, izin al, ölçerek değiştir, gizli bilgiyi denetle ve haber ver.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazması, tekrarlanabilir ve denetlenebilir akış bırakır. Onay veya secret kapısı atlanırsa güvenlik olayı doğar ve tüm oturumlar etkilenir.

### 3.2 Kapılar (Gate)

```text
[G1 KEŞİF: kural + envanter + şablon] --onay--> [G2 ONAY: kullanıcı]
  --> [G3 UYGULAMA: tek dosya] --ölçüm--> [G4 VERIFY (UTF-8)]
  --> [G5 SECRET: REDACTED denetimi] --> [G6 RAPOR: commit ORCHESTRATÖRDE]
   red: görev geri döner | verify başarısız: repair + tekrar | secret ihlali: anahtar iptali
```

| Kapı | Koşul | Red durumunda |
|------|-------|---------------|
| G1 Keşif | Kural + envanter + şablon okundu, sayılar ölçüldü | Görev başlamaz |
| G2 Onay | Kullanıcı onayı yazılı (ayar/skill) | Değişiklik yapılmaz |
| G3 Uygulama | Diff yalnız hedef dosyada | Kapsam daraltılır, tekrar denenir |
| G4 Verify | BOM yok, UTF-8 geçerli, mojibake 0 | `repair` (yedekli) → tekrar verify |
| G5 Secret | `.md` içinde anahtar değeri yok (REDACTED) | Düzelt + Security'e bildir (anahtar iptali) |
| G6 Rapor | Rapor + log append; commit ORCHESTRATÖRDE | Subagent commit atmaz |

> **eli10 (basit):** Kapılar, "dur, izin al, ölç, gizli bilgiyi kontrol et, haber ver" noktalarıdır.
> **eli15 (detay):** Kapılar ayrı tablodur çünkü red durumunun ne olacağı peşinen yazılmalıdır. Okunması, nerede durulacağını gösterir. Yazması, hatanın erken yakalanmasını sağlar. Kapı yoksa onaysız ayar değişir ve bozuk dosya doğar.

### 3.3 Özel Akışlar

| # | Akış | Sıra | Neden | Atlarsan ne olur |
|---|------|------|-------|------------------|
| 1 | **Yeni skill üretimi** | Şablon oku → domain içerik → `SKILL.md` yaz → MO kaydı → verify | Yapı sapması + kayıt zorunlu | Şablonsuz, kayıtsız skill |
| 2 | **Ayar değişikliği** | Onay → düzenle → verify → secret denetimi → rapor | İzin/MCP/secret yüzeyi | Sürpriz izin, anahtar sızıntısı |
| 3 | **Boot düzeltme talebi** | Ölç → işaretle → rapor → vault-sync | `CLAUDE.md`/ayar dokunulmaz | Elle düzeltme → SSOT ihlali |
| 4 | **Geçici plan temizliği** | `plans/` içeriği vault'a taşınmaz (`README.md` kuralı) | SSOT kirlenmesi | Geçici dosya kalıcı sanılır |

> **eli10 (basit):** Üç özel durum var: skill üretmek, ayarı değiştirmek ve bozuk olanı raporlamak; her birinin ayrı sırası var.
> **eli15 (detay):** Özel akışlar ayrı tablodur çünkü genel adımların dışında kritik farkları vardır (şablon, onay, dokunulmazlık). Okunması, doğru sırayı verir. Yazması, hatayı baştan önler. Karıştırılırsa onaysız ayar değişir ya da vault kirlenir.

### 3.4 Dosya Bazlı Uygulama Notları

| Dosya | Uygulama farkı | Kapı | eli10 | eli15 |
|-------|----------------|------|-------|-------|
| `settings.json` | Yalnız onayla değişir; `instructions` sırası korunur | G2 | Ayar dosyasına izinsiz dokunulmaz. | İzin/MCP/secret aynı dosyadadır; onaysız değişiklik tüm oturumları etkiler. Onay kaydı denetim izidir. |
| `settings.local.json` | Yerel `allow` listesi genişletme onaylı | G2 | Yerel izin listesi kullanıcıya aittir. | Genişletilen izin (ör. dosya yazma) güvenlik sürprizi doğurur; ortak ayardan ayrı durur. |
| `skills/*/SKILL.md` | Şablon zorunlu + domain içerik + MO kaydı | G1 + G2 | Yeni yetenek, hazır kalıpla girer. | Şablonsuz skill yapı sapması üretir; kayıt yoksa envanter şaşar. |
| `agent/*.md` (118 subagent tanımı) | Yeni tanım onaylı; `permission` bloğu kısıtlanabilir | G2 | Subagent eklemek = yetki eklemek, onaysız olmaz. | Onaysız subagent, edit/bash izinleriyle doğar — sürpriz yetki genişlemesi. |
| `command/*.md` (24 slash komutu) | Yeni komut onaylı; davranış kısayolu ekler | G2 | Kısayol eklemek davranış ekler, onaysız olmaz. | Onaysız komut, mevcut iş akışını sessizce değiştirebilir. |
| `CLAUDE.md` (kök + skills altı) | Dokunulmaz; düzeltme yalnız vault-sync | G6 | Kural defterine ellemezsin, bildirirsin. | Elle düzeltme boot ile vault farkını büyütür (SHA256 farkı zaten var). |
| `plans/` | Vault'a taşınmaz; kalıcı doküman `.ai/`'de | G3 | Geçici dosya kalıcı yerde durmaz. | Taşınırsa vault kirlenir; `README.md` kuralı bunu yasaklar. |

> **eli10 (basit):** Her dosyanın kendi kuralı var: ayar onaylı, kural defteri dokunulmaz, geçici dosya taşınmaz.
> **eli15 (detay):** Dosya bazlı notlar ayrı tablodur çünkü ortak akış tek başına bu farkları kaçırır. Okunması, hedefe göre kapıyı gösterir. Yazması, onay ve gizlilik kurallarının dosyaya göre uygulanmasını sağlar. Tablo olmazsa en hassas dosya (ayar/secret) en az korunan dosya olur.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük ve güvenli | Her şey değişince inceleme kör olur |
| 2 | **Tek sorumluluk** | Kapının tek sahibi olur | "Kapı kimde" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Boot listesi tek yerde (`settings.json`) | Her yerde ayrı boot → çelişkili sıra |
| 4 | **Cihaz izolasyonu** | Yerel izin ortak ayarı bozmaz | Yerel izin ekibe yayılır, sürpriz |
| 5 | **Vendor karantinası** | MCP/skill 3. taraf kodu kendi klasöründe | Güncelleme bizim ayarı bozar |

> **eli10 (basit):** Adımlar küçük kutulara bölünmüş; bir kutuyu değiştirmek diğerini bozmaz.
> **eli15 (detay):** Süreç ayrı dosyalarda tutulur ki her klasör kendi kapısını bilsin: `.claude/` ayar kapısı, `.workflows/` süreç kapısı, `.ai/` faz kapısı. Aynı yerde toplansa her iş için tüm süreç okunur. Okunması, sırayı önceden gösterir. Bölünmezse "adım atlandı mı" cevapsız kalır.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Görev öncesi keşif (kural + envanter + şablon) — onaysız değişiklik yok | Yanlış dosya/yanlış kapı yazımını engeller (kök `AGENTS.md` §4) |
| 2 | Aynı dosya görevde 2. kez okunmaz | Anti-overthink / token israfı (kök `AGENTS.md` §5) |
| 3 | Secret değeri `.md` dosyalarına yazılmaz (REDACTED) | Anahtar sızıntısını kapatır (G5) |
| 4 | Yeni skill şablondan üretilir (Guardrail #16) | Yapı sapmasını önler |
| 5 | Mevcut `CLAUDE.md` elle düzeltilmez; talep raporlanır | Dokunulmazlık + SSOT (boot farkı VERIFICATION REQUIRED) |
| 6 | Yazım sonrası `vault-utf8-writer verify` zorunlu | UTF-8 bozulması / mojibake (G4) |
| 7 | **Commit subagent tarafından ATILMAZ** | Yetki orkestratörde (kök `AGENTS.md` §7/§8) |
| 8 | 3 başarısız düzeltme → DUR + 1 kısa soru | Kör deneme döngüsü (kök `AGENTS.md` §5) |

> **eli10 (basit):** Kurallar işin izinli, ölçülü, gizli bilgisiz ve kayıtlı yürümesini; commit'in başkasında kalmasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile içerik farklı hızda değişir; JSON içine gömülse araç okuyamaz. Okunmaları, kapıların önceden bilinmesini sağlar. Yazması, denetimin tekrarlanmasını garantiler. Kural değişince (kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → [K1: KURAL + ENVANTER + ŞABLON oku] → [K2: SAHİP + ONAY]
  → UYGULA (yalnız hedef dosya) → [K3: VERIFY (UTF-8) + ÖLÇÜM]
  → [K4: SECRET denetimi — REDACTED] → [K5: RAPOR — commit yok] → ORKESTRATÖR
```

Adım ve kapıların tamamı §3.1 / §3.2 / §3.3 tablolarındadır; buradaki zincir sırayı özetler.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: workflow` |
| 2 | Bölüm sırası | §1–§7 sabit, ≤3 başlık seviyesi |
| 3 | Adım tablosu | §3.1'de çıktı + kapı + neden + atlarsan dolu |
| 4 | Commit kuralı | §3.1 #10 + §4 #7 "ATMAZ" ifadesi mevcut |
| 5 | Placeholder | Dosyada `{{` kalmadı |
| 6 | eli10 + eli15 | §1, §3.1, §3.2, §3.3, §3.4, §4 bloklarında etiketli blok var |
| 7 | Disk kanıtı | `instructions` 4 kayıt, `allow` 3 satır, 304 dosya iddiaları ölçümle uyumlu |
| 8 | REDACTED | Anahtar değeri dosyada yok; yalnız `REDACTED` yazıldı |
| 9 | Wiki-link | `[[...]]` formatı; hedefler diskte mevcut |
| 10 | Dokunulmaz | Mevcut `CLAUDE.md` dosyaları değiştirilmedi |
| 11 | Emoji | Dekoratif emoji yok |
| 12 | Uzunluk | 200–350 satır bandı (küçük dizin istisnası) |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Boot kuralı (MEVCUT — değişmez) |
| Klasör rolleri | [[AGENTS.md]] | Sahiplik + routing |
| Klasör context | [[CONTEXT.md]] | Envanter + çelişki kaydı |
| Kök master kurallar | [[../AGENTS.md]] | §3 Zero-Hallucination, §4 keşif, §7 loop |
| Vault süreç anayasası | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| Vault senkron akışı | [[../.workflows/vault-sync.md]] | Talep kapanışı (düzeltme raporu) |
| Skill şablonu | `../.ai/.templates/` | Guardrail #16 kaynağı |
| CI kapısı | [[../.github/workflows/ci.yml]] | Kod tarafı test kapısı |
| CI fail yolu | [[../.github/WORKFLOW.md]] | Kod tarafı kırmızı akış |
| Agent registry | [[../.ai/AGENTS.md]] | Routing + yetki dayanağı (§3.1–§3.3) |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails (G4/G5 kuralı) |
| Ayar kanıtı | `.claude/settings.json` | Boot + MCP + izin (REDACTED) |
| Geçici dizin kuralı | `.claude/plans/README.md` | Vault dışı kural |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-07
