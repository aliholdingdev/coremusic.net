---
title: "CoreMusic — .workflows Klasör Context"
type: docs
category: workflow
docType: context
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/WORKFLOW.md + kök AGENTS.md"
---

# .workflows — CONTEXT.md

**docType:** context · **Klasör:** `.workflows/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`.workflows/`, çalıştırılabilir **süreç akışlarını** (workflow-instruction) barındırır: oturum başlatma, ADR üretimi, vault senkronizasyonu, güvenlik denetimi, dağıtım, halüsinasyon kontrolü, orkestrasyon ve mimari yazım. Kapsam notu: **.workflows = vault-sync/security/session-init gibi süreç akışları** — bu dosyalar kod değil, agent'ların uyması gereken adım kılavuzlarıdır; kök `WORKFLOW.md` bu klasöre işaret eder. Bu doküman klasörün disk envanterini (9 dosya / 2.442 satır) ve her akışın tetikleyici-çıktısını tanımlar.

| Karar | Kaynak (disk) |
|-------|---------------|
| 8 süreç akışı + 1 kural defteri | `.workflows/` depth-1 ölçüm (2026-10-03): 9 `.md` |
| Dosya tipi `workflow-instruction` | Her dosyanın frontmatter `type:` alanı (8/8) |
| Vault-sync sürümü 1.1 (diğerleri 1.0/1.0.0) | `vault-sync.md` frontmatter `version: 1.1` |
| ADR hedefi iki ayrı seri | `.workflows/CLAUDE.md` §2 tablosu → `.ai/.decisions/` (numaralı) + `.ai/architecture/adr/` (023-026) |
| Kök pointer | Kök `AGENTS.md` §25.4: FULL boot listesinde `.workflows` dizini |

> **eli10 (basit):** Bu klasör, "önce ne yapacaksın" sorusunun cevaplarını tutar: oturum aç, senkron et, denetle.
> **eli15 (detay):** Ayrı yazıldı çünkü süreç ile kural farklı hızda değişir; `.ai/CLAUDE.md` içine gömülse her revizyonda süreç bloğu yeniden yazılırdı. Okunması, işe başlamadan sırayı ve kapıları gösterir. Yazılması, her oturumun aynı yolu izlemesini garanti eder. Akışlar olmazsa herkes kendi sırasını kurar, denetim ve senkron kaybolur.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.workflows/` kök envanteri (9 dosya, depth 1) | `.ai/` vault içeriği (SSOT; bu dosyalar onun uygulama talimatıdır) |
| Her akışın tetikleyici + çıktı özeti | Klasör içinde adım/şablon detayları → ilgili `*.md` dosyası |
| Akış ↔ vault/faz kapıları ilişkisi | CI/CD tanımları → `.github/` |
| Mevcut `CLAUDE.md` ↔ disk tutarlılık kontrolü | Kod (PHP/JS/CSS) süreçleri → ilgili katmanın `WORKFLOW.md`'si |

- **Kullananlar:** Tüm agent'lar (akış okuyucu), MO (vault-sync + adr-creation), Security (security-audit), DevOps (deployment), orkestratör (orchestrator-flow).
- **Ön koşul:** Klasör diskte mevcut; sayımlar `vault-utf8-writer verify` ile alınmış; sayı yoksa `UNKNOWN`.
- **Dokunulmaz:** `CLAUDE.md` bu görevde değiştirilmez; yalnız wiki-link ile bağlanır.

---

## 3. Mimari

### 3.1 Kök Envanter (depth 1 — disk ölçümü 2026-10-03: 9 dosya / 2.442 satır)

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|--------------|--------------------|---------------|-----------|---------------------|-------|-------|
| `session-init.md` (249 satır) | Her oturum başlangıcında vault boot akışı | Frontmatter (`workflow-instruction`) + adım/kapı talimatları | Her oturum aynı okuma sırasıyla başlasın | Boot listesi / okuma sırası değişince | Oturum açılırken izlenen hazırlık listesi. | Ayrı dosyadır çünkü boot davranışı her görevden önce çalışır; genel kural dosyasına gömülse okuma sırası kaybolur. Okunması, hangi vault dosyalarının önce okunacağını gösterir. Yazılması, oturum tekrarlanabilirliğini sağlar. Atlanırsa agent kör başlar, çelişkili bilgiyle çalışır. |
| `adr-creation.md` (257 satır) | Mimari karar kaydı üretimi | İki seri hedefi: `.ai/.decisions/` + `.ai/architecture/adr/` | Kararlar tek formatta kaydedilsin | ADR şablonu/seri kuralı değişince | Mimari karar defterini doldurma kılavuzu. | Ayrıdır çünkü karar üretimi tek başına tekrarlanabilir bir iştir; yazı içine gömülse şablon değişince tüm metin değişir. Okunması, doğru hedef klasörü ve frontmatter'ı gösterir. Yazılması, frozen ADR disiplinini korur. Atlanırsa numarasız/şablonsuz karar doğar. |
| `vault-sync.md` (239 satır, version 1.1) | Vault değişikliği sonrası senkronizasyon + index güncelleme | Adım listesi + index kayıt kuralı | Vault dosyaları birbiriyle tutarlı kalsın | Vault yapısı değişince | Vault'ta değişen her şeyi yerine yazan kapanış işlemi. | Ayrıdır çünkü senkron kapanışa özeldir; süreç kodla karışsa kapanış unutulur. Okunması, hangi dosyaların güncelleneceğini gösterir. Yazılması, SSOT bütünlüğünü sağlar. Atlanırsa log/index drift'i birikir. |
| `security-audit.md` (269 satır) | Güvenlik değişikliği sonrası denetim raporu | Denetim adımları + rapor formatı | Güvenlik değişikliği kanıtla kapanır | Denetim kapsamı değişince | Güvenlik işinin sonunda yazılan denetim raporu kılavuzu. | Ayrıdır çünkü denetim, işten bağımsız bir kapıdır; kod içine gömülse denetim "işin parçası" sanılır ve atlanır. Okunması, hangi kontrollerin zorunlu olduğunu gösterir. Yazılması, kanıt bırakır. Atlanırsa açık sessizce üretime girer. |
| `deployment.md` (282 satır) | Sürüm çıkışı dağıtım onay akışı | Onay adımları + geri dönüş notları | Dağıtım onaysız yapılmaz | Dağıtım altyapısı değişince | Yayına alma işleminin onay ve geri dönüş listesi. | Ayrıdır çünkü dağıtım, koddan farklı risk taşır ve geri dönüş planı ister. Okunması, onay kapılarını gösterir. Yazılması, geri izlenebilir yayın bırakır. Atlanırsa onaysız yayına girilir. |
| `hallucination-control.md` (273 satır) | Belirsiz bilgi tespiti → doğrulama protokolü | Kontrol adımları + `VERIFICATION REQUIRED` kuralları | Uydurma bilgi vault'a/girme | Doğrulama kuralı değişince | "Emin değilsem yazmam, işaretlerim" kuralı. | Ayrıdır çünkü doğrulama her işte bağımsız çalışır; tek bir dosyaya gömülse yalnız o işte hatırlanır. Okunması, kanıt standardını gösterir. Yazılması, Zero-Hallucination'ı işletir. Atlanırsa tahmin yazılır, SSOT bozulur. |
| `orchestrator-flow.md` (310 satır) | Multi-agent görev dağıtım akışı | Görev grafiği + rol dağıtım adımları | Doğru ajana doğru görev gitsin | Registry/routing değişince | Görevlerin ajanlara dağıtılma sırası. | Ayrıdır çünkü dağıtım, görevin kendisinden önce gelir. Okunması, hangi rolün ne yapacağını gösterir. Yazılması, paralel işi düzenler. Atlanırsa görevler yanlış ajana gider. |
| `architecture-write.md` (511 satır) | Mimari dosya yazımı (katman md batch) | Batch kuralı: %31 dosya, 0 silme, K0-K20/A0-A5 denetimli rapor | Büyük mimari yazım kaybolmasın | Mimari yazım kuralı değişince | Mimari dosyaları toplu yazma ve denetim kılavuzu. | Ayrıdır çünkü batch yazımın kendi güvenlik kuralları vardır (0 silme). Okunması, toplu işlemin sınırını gösterir. Yazılması, kayıp/yanlış silmeyi önler. Atlanırsa denetimsiz toplu değişiklik olur. |
| `CLAUDE.md` (52 satır) | Bu klasörün boot kuralı (MEVCUT — dokunulmaz) | Frontmatter + bağlam + tetikleyici/çıktı tablosu + komşu ilişkiler | Klasör kuralları tek yerde dursun | Yalnız vault-sync (MO) | Bu klasörde çalışırken uyulacak kuralların defteri. | Kural ile akış farklı hızda değişir; akış dosyasına gömülse revizyonda kaybolur. Okunması, tetikleyici-çıktı eşleşmesini gösterir. Bu görevde değiştirilmez; düzeltme vault-sync'e rapor edilir. |

> **eli10 (basit):** Dokuz dosyanın sekizi bir işin nasıl yapılacağını anlatır, biri bu klasörün kuralıdır.
> **eli15 (detay):** Envanter ayrı yazıldı çünkü ölçüm yazılmadan iddia edilmez (ZERO-HALLUCINATION). Okunması, hangi akışın ne zaman çalıştığını ve kime ait olduğunu gösterir. Yazılması, plan-uygulama hizasını kanıtlar. Ölçüm yoksa "kaç akış var" tahminle cevaplanır ve klasör büyürken envanter şaşar.

### 3.2 Tetikleyici → Çıktı Haritası (disk ölçümü)

| Dosya | Tetikleyici | Çıktı |
|-------|-------------|-------|
| `session-init.md` | Her oturum başlangıcı | Vault boot (okuma sırası) |
| `adr-creation.md` | Mimari karar ihtiyacı | ADR dosyası (`.ai/.decisions/` veya `.ai/architecture/adr/`) |
| `vault-sync.md` | Vault değişikliği sonrası | Senkronize vault + index kaydı |
| `security-audit.md` | Güvenlik değişikliği | Denetim raporu |
| `deployment.md` | Sürüm çıkışı | Onaylı dağıtım + geri dönüş planı |
| `hallucination-control.md` | Belirsiz bilgi tespiti | `VERIFICATION REQUIRED` işareti |
| `orchestrator-flow.md` | Multi-agent görev | Görev dağıtım grafiği |
| `architecture-write.md` | Mimari dosya yazımı | Batch rapor (K0-K20/A0-A5 denetimli) |

> **eli10 (basit):** Her dosyanın bir "ne zaman çalışır" ve "ne verir" satırı vardır.
> **eli15 (detay):** Harita ayrı tablodur çünkü tetikleyici ile çıktı eşleşmesi klasörün özeldir. Okunması, doğru akışı doğru anda başlatmayı sağlar. Yazılması, çıktı sahipliğini görünür kılar. Eşleşme unutulursa akış yanlış anda tetiklenir ya da çıktı kaybolur.

### 3.3 Komşu İlişkiler

| Komşu | Yön | Ne taşır (kanıt) |
|-------|-----|------------------|
| `../AGENTS.md` (kök) | kök → .workflows | §25.4 FULL boot listesinde `.workflows` dizini |
| `../.ai/WORKFLOW.md` | vault → .workflows | Süreç anayasası; bu klasör uygulama detayıdır (`CLAUDE.md` §3) |
| `../.ai/log.md` | .workflows → vault | Her workflow çalışması append-only kayıt bırakır |
| `../.ai/.decisions/index.md` | .workflows → vault | `adr-creation.md` çıktı hedefi |
| `../.github/` | .workflows ↔ .github | CI/deploy kapısı (`deployment.md` G5) |
| `../.claude/` | .workflows → Claude Code | `settings.json` `instructions` listesi boot'u tetikler |

> **eli10 (basit):** Akışlar vault'tan talimat alır, yine vault'a kayıt bırakır; CI ve Claude tarafıyla konuşur.
> **eli15 (detay):** İlişkiler ayrı tabloda çünkü çelişkide hangi kaynağın kazanacağı belli olsun (vault kazanır). Okunması, akışın nereye yazdığını ve nereden okuduğunu gösterir. Yazılması, bağımlılık görünür olur. Tablo unutulursa kayıt nereye gider sorusu kaybolur.

### 3.4 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak) | Disk ölçümü (2026-10-03) | Karar |
|---|----------------|--------------------------|-------|
| 1 | `.workflows/CLAUDE.md` §2 tablosu 8 akış sayar | 8 akış `.md` + `CLAUDE.md` = 9 dosya | **Tutarlı** — çelişki yok |
| 2 | Kök `AGENTS.md` §25.4: FULL boot = 3 dizin (`.workflows`, `.ai/.templates`, `.ai/.agents`) | `.workflows/` mevcut | **Tutarlı** |
| 3 | ADR iki seri iddiası (`.ai/.decisions/` + `.ai/architecture/adr/`) | `.ai/.decisions/index.md` → True; `.ai/architecture/adr/` içeriği OKUNMADI | VERIFICATION REQUIRED — ikinci seri içerik ölçülmedi |

> **eli10 (basit):** Klasörün kendi sayımı ile defterindeki sayı uyuşuyor; bir seri sadece işaretli, içi okunmadı.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar düzeltilemez, yalnız işaretlenir. Okunması, hangi bilginin doğrulandığını gösterir. Yazılması, ölçüm kanıtıyla olur. Tablo olmazsa doğrulanmamış iddia "gerçek" gibi kullanılır.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi akışın ne yaptığı kaybolur |
| 2 | **Tek sorumluluk (tek iş)** | Her akışın tek tetikleyicisi ve tek sahibi olur | Dosya 511+ satıra büyür, okunmaz; "bunu kim yazdı, neden" cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak kural tek yerde (`.ai/CLAUDE.md` Hard Guardrails) | Her akışta ayrı kural → tutarsızlık, tarama gerekir |
| 4 | **Cihaz izolasyonu** | Ortam farkı (terminal/agent) yayılmaz | Fark tüm akışlara sıçrar, drift |
| 5 | **Vendor karantinası** | 3. taraf araç adımları kendi akışında | Dış adım içimize karışır, güncellemede kaybolur |

> **eli10 (basit):** Bilgiler küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: boot `session-init`'te, senkron `vault-sync`'te, denetim `security-audit`'te durur. Böylece biri değişince sadece o değişir, diğerleri bozulmaz. Hepsi tek dosyada toplansa kapanış ve boot kaybolurdu.

---

## 4. Kurallar

| # | Kural | Neden var (gerekçe) | Kaynak |
|---|-------|---------------------|--------|
| 1 | Süreç dosyaları `workflow-instruction` tipi ve frontmatter alanlarıyla yazılır | Akışlar taranabilir ve denetlenebilir kalır | 8 dosyanın frontmatter'ı (disk) |
| 2 | Her akış çalışması sonrası `log.md` append-only kayıt bırakılır | İzlenebilirlik; geçmiş satıra dokunulmaz | `.workflows/CLAUDE.md` §3 |
| 3 | ADR çıktısı doğru seriye gider (`.ai/.decisions/` numaralı seri) | Kararlar yanlış klasörde kaybolmaz | `adr-creation.md` + `.workflows/CLAUDE.md` §2 |
| 4 | Süreç adımları şablondan üretilir (Guardrail #16) | Tutarlı iskelet (§1–§7, frontmatter 7+docType) | `.ai/.templates/frontend/context-template.md` §3.2/§3.4 |
| 5 | Mevcut `CLAUDE.md` bu görevde değiştirilmez | Dokunulmazlık + SSOT hiyerarşisi | Görev kuralı |
| 6 | Çelişki (vault ↔ disk) → disk kazanır + §3.4'te işaretlenir | SSOT korunur | `../.ai/CLAUDE.md` Hard Guardrails |
| 7 | Doğrulanamayan iddia `VERIFICATION REQUIRED` (§3.4 #3 gibi) | Zero-Hallucination | Kök `AGENTS.md` §3 |

> **eli10 (basit):** Kurallar, akışların hep aynı biçimde yazılmasını, çalışmasının kayda geçmesini ve uydurma bilgi girmemesini sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile akış farklı hızda değişir; akış içine gömülse revizyonda kaybolur. Okunmaları, işe başlamadan sınırları gösterir. Yazılmaları, denetimin tekrarlanabilir olmasını sağlar. Kural değişince (registry/anasaya revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
İHTİYAÇ DOĞAR (boot / karar / senkron / denetim / dağıtım)
  → İLGİLİ AKIŞ DOSYASI SEÇ (.workflows/*.md) → KURAL OKU (CLAUDE.md + kök AGENTS.md)
  → AKIŞI UYGULA (adım sırası) → KAYIT (log.md append) → RAPOR (commit atmaz)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | İhtiyaca göre doğru akışı seç (§3.2 tetikleyici tablosu) | Seçilen `*.md` | Yanlış akış yanlış kapıda durur | Uygunsuz sırayla iş, eksik kapı |
| 2 | [[CLAUDE.md]] + ilgili akış dosyasını 1. kez oku | Kural + adım listesi | Anti-overthink: ilk okumadan karar | Tekrarlı okuma, karar gecikmesi |
| 3 | Adımları uygula (vault / kod / rapor) | Akış çıktısı | Sıra bozulmaz | Kapı atlanır, kayıt düşer |
| 4 | `log.md` append + gerekirse `vault-sync.md` | Kayıt | İzlenebilirlik | Drift birikir, SSOT zayıflar |
| 5 | Rapor yaz; **commit ATMA** | Rapor | Yetki sınırı | Yetkisiz commit, revert riski |

> **eli10 (basit):** İhtiyaca göre doğru listeyi aç, sırayla uygula, yaptığını kaydet ve haber ver.
> **eli15 (detay):** Adımlar ayrı satırlardır çünkü her adımın çıktısı ve gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazılması, tekrarlanabilir akış bırakır. Kayıt adımı atlanırsa geçmiş kaybolur ve sonraki oturum yanlış bilgiyle başlar.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada `{{` kalmadı |
| 4 | Envanter | 9 dosya + satır sayıları `verify` ölçümüyle uyumlu |
| 5 | Disk kanıtı | `workflow-instruction` tipi + `version: 1.1` iddiaları frontmatter'da gerçek |
| 6 | Wiki-link | `[[...]]` formatı; hedefler diskte mevcut |
| 7 | eli10 + eli15 | §1, §3, §4, §5 bloklarında etiketli blok var; sıralama eli10 → eli15 |
| 8 | Halüsinasyon | Okunmayan içerik (`.ai/architecture/adr/`) iddia edilmedi, işaretli |
| 9 | Dokunulmaz | Mevcut `CLAUDE.md` değiştirilmedi |
| 10 | Emoji | Yalnız `[[...]]` referansı; dekoratif emoji yok |
| 11 | Uzunluk | 200–350 satır bandı (küçük dizin istisnası: 9 dosyalık süreç klasörü) |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Tetikleyici/çıktı tablosu + protokol (MEVCUT) |
| Klasör rolleri | [[AGENTS.md]] | Kim hangi akıştan sorumlu |
| Klasör süreç | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Oturum başlatma | [[session-init.md]] | Boot akışı |
| ADR üretimi | [[adr-creation.md]] | Karar kaydı akışı |
| Vault senkronu | [[vault-sync.md]] | Kapanış akışı (version 1.1) |
| Güvenlik denetimi | [[security-audit.md]] | Denetim akışı |
| Dağıtım | [[deployment.md]] | Onay akışı |
| Halüsinasyon kontrolü | [[hallucination-control.md]] | Doğrulama protokolü |
| Orkestrasyon | [[orchestrator-flow.md]] | Görev dağıtım akışı |
| Mimari yazım | [[architecture-write.md]] | Batch yazım akışı |
| Vault süreç anayasası | [[../.ai/WORKFLOW.md]] | Faz kapıları (SSOT) |
| Kök master kurallar | [[../AGENTS.md]] | §7 loop, commit kuralı |
| CI kapısı | [[../.github/workflows/ci.yml]] | Dağıtım öncesi test kapısı |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
