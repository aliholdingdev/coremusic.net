---
title: "CoreMusic — .workflows Klasör İş Akışı"
type: docs
category: workflow
docType: workflow
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/WORKFLOW.md + kök AGENTS.md"
---

# .workflows — WORKFLOW.md

**docType:** workflow · **Klasör:** `.workflows/` · **Sorumlu:** MO (workflow)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[CONTEXT.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `.workflows/` içinde bir sürecin (boot, ADR, senkron, denetim, dağıtım...) nasıl seçildiğini ve adım adım yürütüldüğünü tanımlar: her adımın çıktısı, girdisi/kapısı, neden olduğu ve atlanırsa ne olacağı. Kapsam notu: **.workflows = vault-sync/security/session-init gibi süreç akışları** — süreçler `.ai/` anayasasının uygulama talimatıdır; kapılar (boot, onay, kayıt, rapor) burada zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Süreç dosyaları `workflow-instruction` (8/8) | Her `*.md` frontmatter `type:` alanı |
| Kök `WORKFLOW.md` bu klasöre işaret eder | `.workflows/CLAUDE.md` §1 |
| Her akış `log.md` append-only kayıt bırakır | `.workflows/CLAUDE.md` §3 |
| Commit subagent'a ait değil | Kök `AGENTS.md` §7/§8 |
| ADR çıktısı `.ai/.decisions/` numaralı seri | `.workflows/CLAUDE.md` §2 + `adr-creation.md` |

> **eli10 (basit):** Bu akış, hangi listenin açılacağını, hangi kapıların geçileceğini ve nereye kayıt düşeceğini yazar.
> **eli15 (detay):** Ayrı yazıldı çünkü süreç ile kural farklı hızda değişir; kural dosyasına gömülse revizyonda kaybolur. Okunması, işe başlamadan sırayı gösterir. Yazılması, tekrarlanabilir ve denetlenebilir akış bırakır. Akış olmazsa herkes kendi sırasını kurar, kapılar formaliteye döner.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Doğru akış seçimi + ortak kapılar (G1–G6) | `.ai/` vault içeriği ve fazları → [[../.ai/WORKFLOW.md]] |
| Akış bazlı geri dönüş yolları (fail/red) | CI/CD kapıları → [[../.github/workflows/ci.yml]] |
| Kayıt kuralı (`log.md` append) + rapor | Kod akışları (PHP/JS/CSS) → ilgili katman `WORKFLOW.md`'si |
| Onay kapıları: deployment, yeni akış ekleme | Akış dosyalarının kendi iç adımları → ilgili `*.md` |

- **Kullananlar:** Tüm agent'lar (akış okuyucu/yürütücü), MO (kapı + kayıt), kullanıcı (onay).
- **Ön koşul:** Envanter ölçülmüş (`.workflows/CONTEXT.md` §3.1 — 9 dosya); hedef akış dosyası diskte mevcut; şablon okunmuş (Guardrail #16).

---

## 3. Mimari

### 3.1 Akış Seçim Tablosu (tetikleyici → akış → ilk kapı)

| # | Tetikleyici | Akış dosyası | İlk kapı | Çıktı | Neden | Atlarsan ne olur | eli10 | eli15 |
|---|-------------|--------------|----------|-------|-------|------------------|-------|-------|
| 1 | Oturum başlangıcı | [[session-init.md]] | G1 Boot (okuma sırası) | Hazırlanmış vault bağlamı | Herkes aynı sırayla başlasın | Kör başlangıç, çelişkili bilgiyle çalışma | Oturum açılışının ilk adımıdır. | Boot ayrı akıştır çünkü her görevden önce çalışır; diğer akışlara gömülse unutulur. Okunması, sırayı verir. Yazılması, tekrarlanabilir açılış bırakır. Atlarsan agent eksik bağlamla çalışır. |
| 2 | Mimari karar ihtiyacı | [[adr-creation.md]] | G1 Şablon + G2 Hedef seri | Kayıtlı ADR | Kararlar tek formatta dursun | Numarasız/şablonsuz karar, frozen bozulması | Karar defterine kayıt kılavuzu. | ADR akışı ayrıdır çünkü kayıt kalıcıdır ve frozen kuralı vardır. Okunması, doğru klasörü ve frontmatter'ı gösterir. Yazması, karar geçmişini korur. Atlarsan karar kaybolur ya da yanlış seriye düşer. |
| 3 | Vault değişikliği kapanışı | [[vault-sync.md]] | G3 Kayıt (index + log) | Senkron vault | SSOT bütünlüğü | Index/log drift'i birikir | Değişiklikleri yerine yazan kapanıştır. | Senkron ayrıdır çünkü kapanışa özeldir; süreç içine gömülse unutulur. Okunması, hangi dosyaların güncelleneceğini verir. Yazması, bütünlüğü sağlar. Atlarsan vault birikir ve çelişki doğar. |
| 4 | Güvenlik değişikliği | [[security-audit.md]] | G2 Bağımsız denetim | Denetim raporu | İç denetim körleşmesin | Açık sessizce üretime girer | Güvenlik işinin denetim kapısıdır. | Denetim ayrıdır çünkü işi yapan kendi denetimini kapatamaz. Okunması, kontrolleri gösterir. Yazması, kanıt bırakır. Atlarsan güvenlik iddiası kanıtsız kalır. |
| 5 | Sürüm çıkışı | [[deployment.md]] | G4 Kullanıcı onayı | Onaylı dağıtım | Onaysız yayına alma yok | Geri dönüşü pahalı yayın | Yayına alma onay kılavuzudur. | Dağıtım ayrıdır çünkü geri dönüş planı ister. Okunması, onay kapılarını gösterir. Yazması, geri izlenebilir yayın bırakır. Atlarsan onaysız yayına girilir. |
| 6 | Belirsiz bilgi tespiti | [[hallucination-control.md]] | G1 Kanıt | `VERIFICATION REQUIRED` | Tahmin vault'a girmez | SSOT bozulur, yanlış karar | "Emin değilsem yazmam" kapısıdır. | Doğrulama ayrıdır çünkü her işte bağımsız çalışır. Okunması, kanıt standardını verir. Yazması, Zero-Hallucination'ı işletir. Atlarsan uydurma bilgi yayılır. |
| 7 | Multi-agent görev | [[orchestrator-flow.md]] | G2 Rol atama | Görev dağıtım grafiği | Doğru görev doğru ajana | Çakışma, kilit, boşa iş | Görevlerin dağıtılma sırasıdır. | Dağıtım ayrıdır çünkü işten önce gelir. Okunması, rolleri gösterir. Yazması, paralel işi düzenler. Atlarsan yanlış ajana görev gider. |
| 8 | Mimari dosya yazımı | [[architecture-write.md]] | G3 Batch sınırı (%31, 0 silme) | Denetimli rapor | Toplu yazımda veri kaybı olmasın | Dosya kaybı, denetimsiz değişiklik | Toplu mimari yazımın güvenlik kılavuzudur. | Batch ayrıdır çünkü kendi sınırları vardır (0 silme). Okunması, sınırı verir. Yazması, kaybı önler. Atlarsan denetimsiz toplu değişiklik olur. |

> **eli10 (basit):** Her ihtiyacın ayrı bir listesi var; tetikleyiciye göre doğru liste açılır.
> **eli15 (detay):** Seçim tablosu ayrı yazıldı çünkü doğru akışın hangisi olduğu peşinen belli olmalıdır. Okunması, ilk kapıdan itibaren sırayı gösterir. Yazması, tekrarlanabilir seçim bırakır. Seçim yanlışsa tüm akış yanlış kapıda durur ve çıktı kaybolur.

### 3.2 Ortak Kapılar (G1–G6)

| Kapı | Koşul | Kapsanan akışlar | Red durumunda |
|------|-------|------------------|---------------|
| G1 Boot/Şablon | Okuma sırası + şablon (Guardrail #16) okundu | 1, 2, 6 | Görev başlamaz; okuma tekrarlanır |
| G2 Onay/Bağımsız denetim | Kullanıcı onayı veya ikinci rol denetimi | 2, 4, 7 | Değişiklik yapılmaz / denetim tekrarlanır |
| G3 Kayıt/Batch sınırı | `log.md` append + index + %31/0-silme sınırı | 3, 8 | Kayıt yoksa iş tamamlanmış sayılmaz |
| G4 Kullanıcı onayı (dağıtım) | Yayına alma yazılı onayı | 5 | Yayın durur |
| G5 CI kapısı | `../.github/workflows/*.yml` yeşil | 5 (dağıtım öncesi) | [[../.github/WORKFLOW.md]] fail yolu |
| G6 Rapor | Rapor + commit ORCHESTRATÖRDE | hepsi | Subagent commit atmaz |

> **eli10 (basit):** Kapılar, "dur, kanıtla, izin al" noktalarıdır ve her akış hangi kapıdan geçeceğini bilir.
> **eli15 (detay):** Kapılar ayrı tablodur çünkü red durumunun ne olacağı peşinen yazılmalıdır. Okunması, nerede durulacağını gösterir. Yazması, hatanın erken yakalanmasını sağlar. Kapı yoksa onaysız süreç işler ve kayıt düşmez.

### 3.3 Kayıt ve Rapor Akışı

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | `log.md` append (bayt-seviyesi, geçmişe dokunmadan) | Audit satırı | İzlenebilirlik | Kayıp geçmiş, sorumluluk belirsizliği |
| 2 | İlgili index güncelle (ör. `.ai/.templates/index.md`, `.ai/.decisions/index.md`) | Kayıt satırı | Katalog taze kalır | Bulunamayan doküman |
| 3 | `MEMORY.md` / session durumu güncellemesi | Session hafızası | Sonraki oturum bağlamı | Bağlam kaybı, tekrarlı iş |
| 4 | Rapor (ne değişti → hangi dosya → sonraki adım) | Teslim raporu | Orkestratöre kapı | İş görünmez kalır |
| 5 | Commit **ATMAZ** | — | Yetki sınırı | Yetkisiz commit, revert riski |

> **eli10 (basit):** İş bitince izini yaz, index'i tazele, rapor ver; commit'i sen atmazsın.
> **eli15 (detay):** Kayıt ayrı yazıldı çünkü en sık atlanan adımdır. Okunması, nereye yazılacağını gösterir. Yazması, denetimi mümkün kılar. Adım atlanırsa sonraki oturum eski bilgiyle başlar ve drift birikir.

### 3.4 Dosya Bazlı Uygulama Notları

| Dosya | Uygulama farkı | Kapı | eli10 | eli15 |
|-------|----------------|------|-------|-------|
| `session-init.md` | Boot sırası değiştirilemez; okuma listesi eksiksiz | G1 | Oturumun ilk adımı, atlanmaz. | Sıra herkes için aynı olmalı; eksik okuma çelişkili bilgi doğurur. Kapı, boot'un tamamlanmasını şart koşar. |
| `adr-creation.md` | Hedef seri seçimi + frontmatter + frozen kuralı | G1 + G2 | Karar kaydı, doğru deftere düşer. | Yanlış seriye kaybolan karar bulunamaz. Onay ve şablon, karar geçmişinin tutarlılığını korur. |
| `vault-sync.md` | `log.md` append + index kaydı (v1.1) | G3 | Kapanışta her şey yerine yazılır. | Kayıt atlanırsa drift birikir; sonraki oturum eski bilgiyle başlar. |
| `security-audit.md` | Bağımsız denetim; işi yapan kapatamaz | G2 | Denetimi ikinci bir göz yapar. | İç denetim körleşirse açık sessizce geçer. |
| `deployment.md` | Kullanıcı onayı + CI kapısı (G5) | G2 + G4 + G5 | Yayına alma izinli ve testli olur. | Onaysız yayın geri dönüşü pahalıdır; CI yeşil olmadan deploy edilmez. |
| `hallucination-control.md` | Kanıtsız iddia işaretlenir, yazılmaz | G1 | Emin değilsem yazmam kuralı. | İşaret yoksa tahmin vault'a girer ve SSOT bozulur. |
| `orchestrator-flow.md` | Rol ataması registry ile eşleşmeli | G2 | Görev doğru ajana gider. | Registry dışı atama çakışma ve kilit doğurur. |
| `architecture-write.md` | %31 dosya sınırı + 0 silme + rapor | G3 | Toplu yazımda kayıp olmaz. | Sınır aşılırsa veri kaybı ve denetimsiz değişiklik olur. |

> **eli10 (basit):** Her dosyanın küçük bir uygulama farkı ve kendi kapısı vardır; genel akış bunları ayrı ayrı uygular.
> **eli15 (detay):** Dosya bazlı notlar ayrı tablodur çünkü ortak akış tek başına bu farkları kaçırır. Okunması, hangi dosyada hangi ek kuralın geçtiğini gösterir. Yazması, kapıların dosyaya göre uygulanmasını sağlar. Tablo olmazsa en kritik sınır (0 silme, onay) gözden kaçar.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada, diff küçük | Her şey değişince inceleme kör olur |
| 2 | **Tek sorumluluk** | Kapının tek sahibi olur | "Kapı kimde" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak kural tek yerde (`.ai/CLAUDE.md`) | Her akışta ayrı kural → tutarsızlık |
| 4 | **Cihaz izolasyonu** | Ortam/agent farkı yayılmaz | Fark her yere sıçrar, drift |
| 5 | **Vendor karantinası** | 3. taraf araç adımları kendi akışında | Güncelleme bizim akışı bozar |

> **eli10 (basit):** Adımlar küçük kutulara bölünmüş; bir kutuyu değiştirmek diğerini bozmaz.
> **eli15 (detay):** Süreç ayrı dosyalarda tutulur ki her klasör kendi kapısını bilsin: `.workflows/` süreç kapısı, `.ai/` faz kapısı, `.github/` CI kapısı. Aynı yerde toplansa her iş için tüm süreç okunur. Okunması, sırayı önceden gösterir. Bölünmezse "adım atlandı mı" cevapsız kalır.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Önce doğru akış seçilir (§3.1 tetikleyici tablosu) | Yanlış akış yanlış kapıda durur |
| 2 | Akış + kural 1. kez okunur; aynı dosya 2. kez okunmaz | Anti-overthink (kök `AGENTS.md` §5) |
| 3 | `log.md` yalnız append; geçmiş satıra dokunulmaz | Audit bütünlüğü (`.workflows/CLAUDE.md` §3) |
| 4 | ADR/frozen metin değiştirilmez, yalnız wiki-link verilir | Karar geçmişi korunur |
| 5 | Deployment/yeni akış eklemesi onay ister | Onaysız süreç değişikliği olmaz |
| 6 | **Commit subagent tarafından ATILMAZ** | Yetki orkestratörde (kök `AGENTS.md` §7/§8) |
| 7 | 3 başarısız düzeltme → DUR + 1 kısa soru | Kör deneme döngüsü (kök `AGENTS.md` §5) |
| 8 | Ölçülmeyen sayı/iddia yazılmaz → `VERIFICATION REQUIRED` | Zero-Hallucination (kök `AGENTS.md` §3) |

> **eli10 (basit):** Kurallar, akışın doğru, izinli, kayıtlı ve kanıtlı yürümesini sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile kod farklı hızda değişir; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazması, denetimin tekrarlanmasını garantiler. Kural değişince (kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
İHTİYAÇ → AKIŞ SEÇ (§3.1) → [G1: KURAL + AKIŞ + ŞABLON oku]
  → [G2: ONAY / BAĞIMSIZ DENETİM (gerekirse)] → UYGULA
  → [G3: KAYIT (log append + index)] → (dağıtımda G4 onay + G5 CI)
  → [G6: RAPOR — commit yok] → ORKESTRATÖR
```

Adım ve kapıların tamamı §3.1 / §3.2 / §3.3 tablolarındadır; buradaki zincir sırayı özetler.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: workflow` |
| 2 | Bölüm sırası | §1–§7 sabit, ≤3 başlık seviyesi |
| 3 | Adım tablosu | §3.1'de çıktı + kapı + neden + atlarsan dolu |
| 4 | Commit kuralı | §3.3 #5 + §4 #6 "ATMAZ" ifadesi mevcut |
| 5 | Placeholder | Dosyada `{{` kalmadı |
| 6 | eli10 + eli15 | §1, §3.1, §3.2, §3.3, §3.4, §4, §5 bloklarında etiketli blok var |
| 7 | Disk kanıtı | 8 akış + `workflow-instruction` + `version 1.1` iddiaları ölçümlü |
| 8 | Wiki-link | `[[...]]` formatı; hedefler diskte mevcut |
| 9 | Dokunulmaz | Mevcut `CLAUDE.md` değiştirilmedi |
| 10 | Emoji | Dekoratif emoji yok |
| 11 | Uzunluk | 200–350 satır bandı (küçük dizin istisnası) |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Tetikleyici/çıktı + protokol (MEVCUT) |
| Klasör rolleri | [[AGENTS.md]] | Sahiplik + routing |
| Klasör context | [[CONTEXT.md]] | Envanter + çelişki kaydı |
| Oturum akışı | [[session-init.md]] | Boot adımları |
| ADR akışı | [[adr-creation.md]] | Karar kaydı adımları |
| Senkron akışı | [[vault-sync.md]] | Kapanış adımları (v1.1) |
| Denetim akışı | [[security-audit.md]] | Denetim adımları |
| Dağıtım akışı | [[deployment.md]] | Onay adımları |
| Halüsinasyon akışı | [[hallucination-control.md]] | Doğrulama adımları |
| Orkestrasyon akışı | [[orchestrator-flow.md]] | Dağıtım adımları |
| Mimari yazım akışı | [[architecture-write.md]] | Batch sınırı |
| Kök master kurallar | [[../AGENTS.md]] | §4 keşif, §5 anti-overthink, §7 loop |
| Vault süreç anayasası | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| CI kapısı | [[../.github/workflows/ci.yml]] | G5 test kapısı |
| CI fail yolu | [[../.github/WORKFLOW.md]] | G5 kırmızı durum akışı |
| Agent registry | [[../.ai/AGENTS.md]] | Rol/routing dayanağı (§3.1) |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails (§4) |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
