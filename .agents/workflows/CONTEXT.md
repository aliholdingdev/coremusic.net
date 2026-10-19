---
title: "CoreMusic — .workflows Context"
type: docs
category: workflow
date: 2026-10-03
updated: 2026-10-12
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/WORKFLOW.md + kök AGENTS.md"
docType: context
---

# CoreMusic — .workflows Context

**docType:** context · **Klasör:** `.workflows/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`.workflows/`, agent'ların uyması gereken çalıştırılabilir süreç akışlarını (`workflow-instruction`) tutar: oturum başlatma, ADR üretimi, vault senkronizasyonu, güvenlik denetimi, dağıtım, halüsinasyon kontrolü ve orkestrasyon. Bu doküman klasörün disk envanterini (11 dosya, 0 alt dizin — ölçüm 2026-10-12), her dosyanın rolünü ve vault iddiaları ile disk arasındaki çelişkileri kanıtlarıyla tanımlar; kaynak kod üretmez, yalnız okunur ve ölçümle güncellenir.

Not: Önceki sürümdeki K-space mimari atıfları (K000–K020, A0–A5 katman kodları ve `.ai/architecture/` yolları) bu revizyonda kaldırıldı; yerine yenisi üretilmedi.

| Karar | Kaynak (disk) |
|-------|---------------|
| Envanter: 11 dosya, 0 alt dizin, tamamı `.md` | `list_directory` ölçümü (`.workflows/`, 2026-10-12) |
| 7 süreç akışının `type:` alanı `workflow-instruction` | 7 dosya frontmatter okuması (2026-10-12) |
| 7 akış toplam 1.873 satır; bu dosya hariç 10 dosya toplam 2.335 satır | `read_text_file` satır ölçümü + toplama (2026-10-12) |
| `architecture-write.md` diskte YOK | `get_file_info` hata döndürdü (2026-10-12) |
| `vault-sync.md` `version: 1.1`; diğer 5 akış `1.0`; `adr-creation.md`'de `version` alanı yok | frontmatter okuma (2026-10-12) |
| Süreç otoritesi (SSOT) | [[../.ai/WORKFLOW.md]] · kural otoritesi kök [[../AGENTS.md]] |

> **eli10 (basit):** Bu klasör, işlerin "nasıl yapılır" adım kılavuzlarını tutar; buradaki dosyalar kod değil, agent süreç talimatlarıdır.
> **eli15 (detay):** Ayrı yazıldı çünkü süreç talimatı kodun içinde duramaz ve her akış farklı bir kapıyı yönetir. Okunması, hangi işin hangi kapıdan geçeceğini önceden gösterir. Yazılması, envanter ile disk gerçeğini hizalar. Bu dosyalar olmazsa oturum, ADR, denetim ve dağıtım rastgele yürür.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.workflows/` kök envanteri (11 dosya, 0 alt dizin) | `.ai/` vault içeriği (SSOT — okunur, burada üretilmez) |
| 7 `workflow-instruction` dosyasının rolü, tetikleyicisi ve sahibi | PHP/JS/CSS kaynak kod (ilgili katmanın context dosyası) |
| Klasör dosyaları ↔ vault otorite ilişkisi (§3.3) | Vault içi ADR/şablon detayları (ilgili vault dosyalarının konusu) |
| Eski envanter iddiaları ↔ disk çelişki kaydı (§3.4) | git geçmişi / commit tarihleri (bu ortamda git yok → `UNKNOWN`) |

- **Kullananlar:** tüm agent'lar (akış okuyucu), MO (vault-sync + adr-creation), Security (security-audit), DevOps (deployment), orkestratör (orchestrator-flow) — sahiplik tablosu [[AGENTS.md]] içindedir.
- **Ön koşul:** hedef klasör diskte mevcut ve envanter bu oturumda ölçülmüş; kanıtı olmayan sayısal iddia `UNKNOWN` taşır.
- **Dokunulmaz:** `CLAUDE.md`, `AGENTS.md`, `WORKFLOW.md` ve 7 akış dosyası bu görevde değiştirilmez; yalnız bu dosya yeniden yazılır.

> **eli10 (basit):** Burada yalnız bu klasörün dosyaları ve ilişkileri anlatılır; vault içeriği ve kaynak kod konu dışıdır.
> **eli15 (detay):** Kapsam ayrı yazıldı çünkü sınır yoksa bu doküman vault tarifine döner ve okunmaz. Okunması, hangi sorunun buradan cevaplanacağını gösterir. Yazılması, ölçüm ve çelişki kaydının nerede durduğunu sabitler. Sınır kayarsa her görev bu dosyayı yeniden keşfeder.

---

## 3. Mimari

### 3.1 Kök Envanter (`list_directory` ölçümü 2026-10-12: 11 dosya, 0 alt dizin)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `session-init.md` (248 satır) | Her oturum başında vault boot akışı | frontmatter (`workflow-instruction`, `version: 1.0`) + aşama ve yasak bölümleri | Oturum her aynı okuma sırasıyla başlasın | Boot okuma sırası değişince |
| `adr-creation.md` (256 satır) | Mimari karar (ADR) üretimi | frontmatter (`category: architecture-decisions`, `version` alanı yok) + onay matrisi | Kararlar tek formatta kaydedilsin | ADR şablonu/onay kuralı değişince |
| `vault-sync.md` (238 satır, `version: 1.1`) | Vault değişikliği sonrası senkron + index/log kaydı | 5 soru analizi + aşama detayları | Vault bütünlüğü ve audit korunsun | Vault yapısı veya index kuralı değişince |
| `security-audit.md` (268 satır) | Güvenlik değişikliği sonrası denetim raporu | OWASP kontrol listesi + Red Team protokolü | Açık üretime sessizce girmesin | Denetim kapsamı değişince |
| `deployment.md` (281 satır) | Sürüm dağıtımı onay akışı | aşama detayları + geri dönüş (rollback) bölümü | Dağıtım onaysız yapılmasın | Dağıtım altyapısı/onay kuralı değişince |
| `hallucination-control.md` (272 satır) | Belirsiz bilgi tespiti → doğrulama protokolü | halüsinasyon türleri + Truth Mode kuralları | Uydurma bilgi vault'a girmesin | Doğrulama kuralı değişince |
| `orchestrator-flow.md` (309 satır) | Multi-agent görev dağıtım akışı | aşama detayları + yasaklar | Doğru ajana doğru görev gitsin | Agent routing değişince |
| `CLAUDE.md` (51 satır) | Bu klasörün boot kuralı (dokunulmaz) | bağlam + tetikleyici/çıktı tablosu (8 satır) + komşu ilişkiler + değişiklik protokolü | Klasör kuralları tek yerde dursun | Yalnız vault-sync (MO) |
| `AGENTS.md` (208 satır) | Dosya sahipliği, yetki sınırları ve routing | rol tabloları + tetikleyici eşlemesi | Kimin hangi akışa dokunacağı baştan belli olsun | Agent registry revizyonunda (MO) |
| `WORKFLOW.md` (203 satır) | Klasör süreç akışı ve kapılar (G1-G6) | adım/kapı tabloları | Onaysız değişiklik yapılmasın | Süreç değişince (MO) |
| `CONTEXT.md` (bu dosya) | Klasör envanteri + çelişki kaydı | 7 bölüm + 5 sütunlu envanter tablosu | Sayılar ölçümle tek yerde dursun | Envanter ölçümü değişince |

**`session-init.md`**
> **eli10 (basit):** Her oturum başında izlenecek hazırlık ve okuma listesi.
> **eli15 (detay):** Ayrı dosyadır çünkü boot davranışı her görevden önce çalışır; genel kural dosyasına gömülse okuma sırası kaybolur. Okunması, hangi vault dosyalarının önce okunacağını gösterir. Yazılması, oturum tekrarlanabilirliğini sağlar. Atlanırsa agent kör başlar ve çelişkili bilgiyle çalışır.

**`adr-creation.md`**
> **eli10 (basit):** Mimari kararların nasıl kaydedileceğini anlatan kılavuz.
> **eli15 (detay):** Ayrıdır çünkü karar üretimi tekrarlanabilir bağımsız bir iştir; başka dosyaya gömülse şablon değişince tüm metin değişir. Okunması, doğru hedef dizini ve onay matrisini gösterir. Yazılması, tek format disiplinini korur. Atlanırsa numarasız ve şablonsuz karar doğar.

**`vault-sync.md`**
> **eli10 (basit):** Vault değişikliğinden sonra her şeyi yerine yazan kapanış işlemi.
> **eli15 (detay):** Ayrıdır çünkü senkron kapanışa özeldir; süreç kodla karışsa kapanış unutulur. Okunması, hangi dosyaların güncelleneceğini ve log kaydının nasıl atılacağını gösterir. Yazılması, SSOT bütünlüğünü sağlar. Atlanırsa index ve log drift'i birikir.

**`security-audit.md`**
> **eli10 (basit):** Güvenlik işinin sonunda çalışan denetim raporu kılavuzu.
> **eli15 (detay):** Ayrıdır çünkü denetim işten bağımsız bir kapıdır; kod içine gömülse denetim işin parçası sanılır ve atlanır. Okunması, hangi OWASP kontrollerinin zorunlu olduğunu gösterir. Yazılması, kanıt bırakır. Atlanırsa açık sessizce üretime girer.

**`deployment.md`**
> **eli10 (basit):** Yayına alma işleminin onay ve geri dönüş listesi.
> **eli15 (detay):** Ayrıdır çünkü dağıtım koddan farklı risk taşır ve geri dönüş planı ister. Okunması, onay kapılarını önceden gösterir. Yazılması, geri izlenebilir bir yayın bırakır. Atlanırsa onaysız yayına girilir.

**`hallucination-control.md`**
> **eli10 (basit):** "Emin değilsem yazmam, işaretlerim" kuralının adım listesi.
> **eli15 (detay):** Ayrıdır çünkü doğrulama her işte bağımsız çalışır; tek bir dosyaya gömülse yalnız o işte hatırlanır. Okunması, kanıt standardını ve doğrulama formatını gösterir. Yazılması, Zero-Hallucination kuralını işletir. Atlanırsa tahmin yazılır ve SSOT bozulur.

**`orchestrator-flow.md`**
> **eli10 (basit):** Görevlerin ajanlara dağıtılma sırası ve koordinasyon akışı.
> **eli15 (detay):** Ayrıdır çünkü dağıtım görevin kendisinden önce gelir. Okunması, hangi rolün ne yapacağını ve hata yönetiminin nasıl yürütüldüğünü gösterir. Yazılması, paralel işi düzenler. Atlanırsa görevler yanlış ajana gider.

**`CLAUDE.md`**
> **eli10 (basit):** Bu klasörde çalışırken uyulacak kuralların yazılı olduğu defter.
> **eli15 (detay):** Kural ile akış farklı hızda değişir; akış dosyasına gömülse her revizyonda kural aranır. Okunması, klasör protokolünü ve komşu ilişkileri gösterir. Yalnız vault-sync (MO) tarafından düzenlenir. Elle değişilirse otorite zinciri ve audit geçmişi bozulur.

**`AGENTS.md`**
> **eli10 (basit):** Bu klasörde kimin hangi dosyaya dokunabileceğini yazar.
> **eli15 (detay):** Yetki ile akış içeriği farklı hızda değişir; akışa gömülse her revizyonda rol revize edilir. Okunması, işe başlamadan sorumluyu ve yasakları gösterir. Agent registry revizyonunda MO günceller. Sahip belirsiz olunca herkes düzenler ve akış sessizce kırılır.

**`WORKFLOW.md`**
> **eli10 (basit):** Bir görevin kapı kapı nasıl yürütüleceğini anlatan klasör süreci.
> **eli15 (detay):** Süreç ile akış dosyası farklı hızda değişir; bir akışa gömülse her revizyonda süreç aranır. Okunması, onay ve ölçüm kapılarını önceden gösterir. Süreç değişince MO günceller. Kapı atlanırsa onaysız değişiklik doğrudan işlenir.

**`CONTEXT.md` (bu dosya)**
> **eli10 (basit):** Klasörün kaç dosya içerdiğini ve eski bilgilerle disk arasındaki farkları yazan kayıt.
> **eli15 (detay):** Ayrı yazıldı çünkü sayı iddiaları tek yerde ve ölçümle durmalıdır. Okunması, envanter ve çelişki kaydını verir; kimse tahminle "kaç akış var" sorusunu cevaplamaz. Envanter ölçümü değişince düzenlenir. Bu dosya olmazsa eski sayılar yeni oturumları yanlış yönlendirir.

### 3.2 Akış Dosyaları Yapısı (frontmatter ölçümü 2026-10-12)

| Dosya | `type` | `version` | Öne çıkan bölümler |
|-------|--------|-----------|---------------------|
| `session-init.md` | `workflow-instruction` | `1.0` | `3. Aşama Ayrıntıları` · `5. Yasaklar` |
| `adr-creation.md` | `workflow-instruction` | alan YOK | `Workflow Steps` · `Onay Matrisi` |
| `vault-sync.md` | `workflow-instruction` | `1.1` | `3. 5 Soru Analizi` · `4. Aşama Ayrıntıları` |
| `security-audit.md` | `workflow-instruction` | `1.0` | `3. OWASP Top 10 Kontrol Listesi` · `5. Red Team Protokolü` |
| `deployment.md` | `workflow-instruction` | `1.0` | `3. Aşama Ayrıntıları` · `4. Geri Dönüş (Rollback)` |
| `hallucination-control.md` | `workflow-instruction` | `1.0` | `4. Kontrol Noktaları` · `5. Truth Mode Kuralları` |
| `orchestrator-flow.md` | `workflow-instruction` | `1.0` | `3. Aşama Ayrıntıları` · `5. Yasaklar` |

Ortak ölçümler: 7/7 dosyada `authority: SSOT` ve `mode:` bloğu (`Red Team` · `Truth Mode` · `Human Mode`) var.

> **eli10 (basit):** Yedi akış dosyası aynı iskeletle yazılmış; tek fark `vault-sync` 1.1 sürümünde olması.
> **eli15 (detay):** Yapı ayrı tablodur çünkü frontmatter taranabilirliği denetimi taşır: tip, sürüm ve otorite satırı okunmadan akış doğru sınıflandırılamaz. Okunması, hangi dosyanın hangi sürümde olduğunu ve öne çıkan bölüm başlıklarını gösterir. Yazılması, eski "8 akış" iddiasının önünü keser. Ölçüm tablo olmazsa sürüm ve tip tahminle yazılır.

### 3.3 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

```text
kök (AGENTS.md + CLAUDE.md) → .workflows/ (CLAUDE.md → AGENTS.md → WORKFLOW.md → CONTEXT.md)
  → ilgili akış dosyası (ihtiyaç anında) → .ai/ vault yalnız ihtiyaç anında (@)
```

| Bu klasör dosyası | Otorite satırı (frontmatter ölçümü) | Taşıdığı bilgi |
|-------------------|--------------------------------------|----------------|
| `.workflows/CLAUDE.md` | `authority: Single Source of Truth (SSOT)` — kendi iddiası | Bağlam + tetikleyici/çıktı tablosu + değişiklik protokolü |
| `.workflows/AGENTS.md` | `authority: "Derived — SSOT: .ai/AGENTS.md + kök AGENTS.md"` | Dosya sahipliği + yetki (SSOT → [[../.ai/AGENTS.md]]) |
| `.workflows/WORKFLOW.md` | `authority: "Derived — SSOT: .ai/WORKFLOW.md + kök AGENTS.md"` | Adım → kapı → rapor (SSOT → [[../.ai/WORKFLOW.md]]) |
| `.workflows/CONTEXT.md` | `authority: "Derived — SSOT: .ai/WORKFLOW.md + kök AGENTS.md"` | Envanter + çelişki kaydı (iskelet → §7 şablonlar) |
| 7 akış dosyası | `authority: SSOT` (kendi iddiası) | Uygulama talimatı; süreç otoritesi [[../.ai/WORKFLOW.md]] |

> **eli10 (basit):** Her dosyanın kimi takip ettiği frontmatter'da yazılıdır; türev dosyalar SSOT kaynağına işaret eder.
> **eli15 (detay):** İlişki ayrı tabloda çünkü çelişkide hangi kaynağa bakılacağı belli olsun: türev dosyalar kazanamaz, SSOT kaynağı kazanır. Okunması, boot sırasını ve otorite zincirini gösterir. Yazılması, klasör dosyalarının ikinci SSOT sanılmasını engeller. Tablo unutulursa her dosya kendi iddiasıyla bağımsız çalışır ve bilgi ayrışır.

### 3.4 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak, okuma ölçümü 2026-10-12) | Disk ölçümü (2026-10-12) | Karar |
|---|------------------------------------------|---------------------------|-------|
| 1 | Eski `CONTEXT.md` (satır 23 ve 55): "9 dosya / 2.442 satır — 8 süreç akışı + 1 kural defteri" | `list_directory`: 11 dosya (7 akış + `CLAUDE.md` + `AGENTS.md` + `WORKFLOW.md` + `CONTEXT.md`); bu dosya hariç 10 dosya 2.335 satır | **Disk kazanır** — bu dosya yeniden yazıldı |
| 2 | Eski `CONTEXT.md` envanteri: `architecture-write.md` (511 satır) satırı | `get_file_info`: dosya diskte YOK | **Disk kazanır** — envanterden çıkarıldı |
| 3 | `.workflows/CLAUDE.md` §2 tablosu 8 satır sayar; son satır diskte olmayan `architecture-write.md` akışı | diskte 7 `workflow-instruction` dosyası | **Disk kazanır** — `CLAUDE.md` dokunulmaz; düzeltme vault-sync'e rapor edilir |
| 4 | `AGENTS.md` satır 66: "`architecture-write.md` (511 satır)" · satır 82: `architecture-write.md` wiki-link'i | dosya yok → kırık hedef | **Disk kazanır** — yalnız işaretlenir |
| 5 | `WORKFLOW.md` satır 65, 108 ve 191: `architecture-write.md` wiki-link'i | dosya yok → kırık hedef | **Disk kazanır** — yalnız işaretlenir |
| 6 | Eski `CONTEXT.md` satır sayıları (ör. `session-init.md` 249, `CLAUDE.md` 52) | bu oturum okuma ölçümü: 248 ve 51 (diğer dosyalarda da 1 satır fark) | **Disk kazanır** — ölçüm yöntemi farkının kaynağı `UNKNOWN` |

**Bilinmeyenler (`UNKNOWN`)**

| Soru | Durum |
|------|-------|
| `architecture-write.md` neden diskte yok (silindi mi, hiç yazılmadı mı) | `UNKNOWN` — git geçmişi yok |
| Eski satır sayılarındaki 1 satırlık farkın kaynağı | `UNKNOWN` — sayımda kullanılan yöntem arşivde yok |
| Dosya son değişiklik tarihleri / commit bilgisi | `UNKNOWN` — bu ortamda git deposu değil |

> **eli10 (basit):** Eski dosyada "9 dosya", "8 akış" ve var olmayan bir akış dosyası yazıyor; gerçeği disk söylüyor.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar düzeltilemez, yalnız işaretlenir. Okunması, hangi bilginin eski olduğunu ve neyin kanıtlandığını gösterir. Yazılması, ölçüm kanıtıyla olur. Bu tablo olmazsa eski "9 dosya / 2.442 satır" bilgisi yeni oturumları yanlış yönlendirir.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Her şey birlikte değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her akışın tek tetikleyicisi ve tek sahibi olur (boot → session-init, denetim → security-audit) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak kural tek yerde durur (`.ai/CLAUDE.md` Hard Guardrails) | Her akışta ayrı kural → tutarsızlık, tarama gerekir |
| 4 | **Ortam izolasyonu** | Ortam/agent farkı başka akışlara yayılmaz | Ortam farkı tüm akışlara sıçrar, drift doğar |
| 5 | **Vendor karantinası** | 3. taraf araç adımları kendi akışında ayrı durur | Dış adım içimize karışır, güncellemede kaybolur |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: boot `session-init`'te, senkron `vault-sync`'te, denetim `security-audit`'te durur. Böylece biri değişince yalnız o değişir, diğer kapılar bozulmaz. Tek dosyada toplansa boot ile kapanış birbirine karışırdı. Hepsi tek dosyada toplansa bakım ve denetim kaybolurdu.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Her sayısal iddia bu oturumun ölçümüyle kanıtlanır; kanıt yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 2 | Çelişki (vault ↔ disk) → **disk kazanır** + §3.4 tablosunda işaretlenir | SSOT ihlali |
| 3 | `CLAUDE.md`, `AGENTS.md`, `WORKFLOW.md` ve 7 akış dosyası bu görevde değiştirilmez | Dokunulmazlık ihlali |
| 4 | Yeni akış ekleme → [[../.ai/WORKFLOW.md]] dosya tablosuna kayıt + kullanıcı onayı | Onaysız süreç doğması |
| 5 | Her akış çalışması sonrası [[../.ai/log.md]] append-only kayıt bırakılır | Audit boşluğu, iz kaybı |
| 6 | Wiki-link `[[...]]` formatı; diskte olmayan hedef üretilmez (bkz. §3.4 #4-#5) | Kırık link |
| 7 | Yeni doküman §1–§7 sırası ve şablon frontmatter'i ile üretilir (Guardrail #16) | Şablon ihlali |
| 8 | Secret/anahtar değerleri bu `.md` dosyalarına YAZILMAZ | Anahtar sızıntısı |
| 9 | Subagent `git commit` ATMAZ — rapor orkestratöre gider | Yetkisiz commit, revert riski |

> **eli10 (basit):** Bu kurallar, dosyaların izinsiz değişmesini, uydurma sayı girmesini ve anahtar sızmasını engeller.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile akış farklı hızda değişir; akış içine gömülse her revizyonda kural revize edilir. Okunmaları, bu klasörde işe başlamadan sınırları gösterir. Yazılmaları, denetimin tekrarlanabilir olmasını sağlar. Kural değişince yalnız bu tablo güncellenir; akış dosyaları sessizce değişmez.

---

## 5. Workflow

```text
ÖLÇ (envanter — ilk okumada) → YAZ (şablon iskeleti, yalnız CONTEXT.md)
  → ÇELİŞKİ KAYDI (vault ↔ disk → disk kazanır) → DOĞRULAMA (§6 tablosu)
  → RAPOR (commit ORCHESTRATÖRDE — subagent atmaz)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Kural oku: [[CLAUDE.md]] + [[AGENTS.md]] | Kural listesi | Klasör yasakları baştan belli olsun | Yasak ihlali, dokunulmaz dosyaya yazma |
| 2 | Envanter ölç (`list_directory` + okuma; sayı uydurulmaz) | Ölçüm tablosu | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, yanlış "9 dosya" iddiasının sürmesi |
| 3 | Şablondan yaz (§1–§7, 5 sütun, eli10/eli15) | Bu dosya | Tutarlı iskelet (Guardrail #16) | Şablon dışı, denetlenemez dosya |
| 4 | Çelişki tablosunu güncelle (§3.4) | Karar satırları | Eski bilgi yeni oturumu yanıltır | "8 akış + architecture-write" efsanesi sürer |
| 5 | §6 doğrulama tablosunu çalıştır | 12/12 | Teslimden önce kanıt | Hatalı dosya teslim edilir |
| 6 | Rapor ver; **commit ATMA** | Rapor | Yetki sınırı (kök `AGENTS.md` §7/§8) | Yetkisiz commit, revert |

> **eli10 (basit):** İş sırayla yürür: dosyaları say, şablona göre yaz, farkları kaydet, kontrol et ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazılması, tekrarlanabilir ve denetlenebilir bir akış bırakır. Adım atlanırsa (özellikle ölçüm ve doğrulama) hatalı iddia dosyaya girer ve sonradan pahalıya bulunur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | Şablon alanları + `docType: context` (frontend şablon §3.2) |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Değişken yer tutucusu (çift süslü parantezli desen) kalmadı |
| 4 | Envanter | 11 dosya, 0 alt dizin, 7 akış iddiası bu oturum ölçümüyle uyumlu |
| 5 | Disk kanıtı | Her sayı `list_directory`/`read_text_file` ölçümünden gelir; kanıtsız iddia `UNKNOWN` |
| 6 | Wiki-link | `[[...]]` formatı; §7 hedefleri diskte mevcut (2026-10-12 doğrulandı) |
| 7 | eli10 + eli15 | §1, §2, §3.1 (11 envanter maddesi), §3.2–§3.5, §4, §5 bloklarında çift var (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 8 | Çelişki kuralı | §4'te "disk kazanır" var |
| 9 | Boot sırası | Kök ↔ vault ilişkisi tablosu §3.3'te var |
| 10 | Kaldırılan atıf | Yalnız §1'deki tek satırlık kaldırıldı notu; başka K-space atıfı yok |
| 11 | Emoji | Dekoratif emoji yok |
| 12 | Uzunluk | 250–400 satır bandı |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Bu klasör boot kuralı (MEVCUT — okunur, değişmez) |
| Klasör rolleri | [[AGENTS.md]] | Dosya sahipliği + yetki sınırları |
| Klasör süreci | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Oturum başlatma | [[session-init.md]] | Boot akışı |
| ADR üretimi | [[adr-creation.md]] | Karar kaydı akışı |
| Vault senkronu | [[vault-sync.md]] | Kapanış akışı (`version: 1.1`) |
| Güvenlik denetimi | [[security-audit.md]] | Denetim akışı |
| Dağıtım | [[deployment.md]] | Onay akışı |
| Halüsinasyon kontrolü | [[hallucination-control.md]] | Doğrulama protokolü |
| Orkestrasyon | [[orchestrator-flow.md]] | Görev dağıtım akışı |
| Kök master kurallar | [[../AGENTS.md]] | §4 keşif, §5 anti-overthink, §7 loop |
| Kök boot pointer | [[../CLAUDE.md]] | Skill registry + vault bağlantıları |
| Vault süreç (SSOT) | [[../.ai/WORKFLOW.md]] | Faz kapıları — bu klasör uygulama detayıdır |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | Dosya sahipliği kuralları |
| Audit trail | [[../.ai/log.md]] | Değişiklik kaydı (append) |
| ADR indeksi | [[../.ai/.decisions/index.md]] | `adr-creation.md` çıktı hedefi |
| Template registry | [[../.ai/.templates/index.md]] | Şablon kayıt satırı |
| Bağlayıcı iskelet | [[../.ai/.templates/frontend/context-template.md]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[../.ai/.templates/documentation/context-md-template.md]] | Envanter tablosu formatı + sayım disiplini |
| Bitmiş örnek (model) | [[../.github/CONTEXT.md]] | Aynı revizyon kalıbı (v2.0.0, 2026-10-12) |
| Bitmiş örnek (model) | [[../.claude/CONTEXT.md]] | İkinci tamamlanmış context dosyası |

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12
