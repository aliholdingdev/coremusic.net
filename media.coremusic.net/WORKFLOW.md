---
title: "CoreMusic — media.coremusic.net İş Akışı"
type: docs
category: media
docType: workflow
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/WORKFLOW.md + kök AGENTS.md"
---

# media.coremusic.net — WORKFLOW.md

**docType:** workflow · **Klasör:** `media.coremusic.net/` · **Sorumlu:** MO (workflow)

**Zorunlu Bağlantılar:** [[CONTEXT]] · [[CLAUDE]] · [[AGENTS]] · [[../AGENTS.md]] · [[../.ai/WORKFLOW.md]]

---

## 1. Amaç

Bu doküman `media.coremusic.net/` klasöründe bir **görevin adım adım nasıl yürütüldüğünü** (çıktı · neden · atlanırsa ne olur) ve kapıların (gates) hangi koşulda açıldığını tanımlar. Geri alınamaz tek iş bu klasörde **taşımadır** (`ingest --commit`); o yüzden kapılar burada zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Lint kapısı tek komut: 8 dosya `php -l` | `composer.json` `scripts.lint` (29-38) |
| Taşıma dry-run + STDIN onayı | `bin/ingest.php` (`--commit` yokken tek bayt yok) |
| Kapı kanıtı: kanıtsız `- [x]` yok | `docs/checklist.md` kural satırı |
| P0 kapanmadan P2 başlamaz | `docs/todos.md` sıra kuralı |
| Commit subagent'a ait değil | Kök `AGENTS.md` §7/§8 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Bu klasörde görev akışı: keşif → uygulama → kapı → rapor | Vault `.ai/` genel fazları → [[../.ai/WORKFLOW]] |
| CLI kapıları (`php -l`, `scan --dry-run`, `audit`) | DB DDL yükleme adımları → `.ai/.sql/mysql/media_catalog.sql` + Data Engineer |
| Taşıma (`ingest`) onay kapısı | Faz 3 GUI ekran üretimi → `.ai/ui-design/` Kalıp D akışı |
| Commit kuralı (subagent atmaz) | Deploy/port :5000/:6000 → DevOps (ADR-039) |

- **Kullananlar:** Backend Architect (yürütücü), Data/Security/QA/DevOps (kapı ortakları), MO (kapı + rapor + commit).
- **Ön koşul:** Hedef dosya diskte mevcut; [[CONTEXT]] envanteri okunmuş; şablon (Guardrail #16) okunmuş.

---

## 3. Mimari

### 3.1 Adım Tablosu (adım → çıktı → neden → atlarsan ne olur)

| # | Adım | Çıktı | Neden (bu adım neden var) | Atlarsan ne olur |
|---|------|-------|---------------------------|------------------|
| 1 | Görev tanımı + onay kapısı (prompt-maker / soru kapısı) | Onaylı görev özeti | Yanlış iş yapmayı baştan önler | İstenmeyen değişiklik, revert |
| 2 | Klasör kurallarını oku: [[CLAUDE]] + [[AGENTS]] | Kural + rol listesi | Bu klasörün yasakları (master, gitignore, dry-run) orada | Yasak ihlali (ör. `media/`'ya kod yazmak) |
| 3 | Envanter ölç + 1. kez oku | Ölçülmüş sayı, karar | Anti-overthink: aynı dosya 2. kez okunmaz; sayı uydurulmaz | Token israfı + uydurma sayı (`⚠️ VR`) |
| 4 | Hedef dosyayı değiştir (yalnız kapsam) | Kod/config/docs diff'i | Kapsam disiplini — eşzamanlı oturumlar var | Başka oturumun çalışması bozulur, çakışma |
| 5 | Lint kapısı: `composer lint` → `php -l` **8 dosya** | 8/8 "No syntax errors" | Syntax hatası üretimde kırıcıdır (ölçüm: 8/8 geçti) | Bozuk PHP commit edilir |
| 6 | İlgili CLI kapısı: `scan --dry-run` / `audit` | exit 0 (audit: 0 = hata yok) | Kural ihlali (slug/ULID/sema) ancak koşunca görünür | Bozuk veri arşive sızar, temizliği zor |
| 7 | Kapı kanıtı: `docs/checklist.md` `Denet:` satırı koşulur | `- [ ]` → `- [x]` + kanıt etiketi | Kanıtsız kapanan kapı kapanmamıştır | Denetim değersizleşir |
| 8 | UI/GUI etkisi varsa browser testi (Faz 3) | Ekran doğrulaması | DOM/layout iddiası kodla kanıtlanamaz | Bozuk yerleşim kullanıcıya ulaşır |
| 9 | Rapor yaz; **commit ATMA** | Rapor + `log.md` append | Yetki sınırı — tarih tek elden | Düzensiz git geçmişi, iz sürme bozulur |

**eli10/eli15 blokları (§3.1 — 9 adım):**

**Adım 1 — Görev tanımı + onay**
> **eli10 (basit):** Önce ne istendiğini anlamak ve onay almak.
> **eli15 (detay):** Kapı (ASKING QUESTIONS) onaysız sonraki aşamaya geçmez (kök `AGENTS.md` §1). Tanım okunmadan başlarsan beklenti ile iş ayrışır. Kapıdan geçince elinde ölçülebilir bir çıktı olur. Atlarsan yanlış işin üretimi ve zaman kaybı.

**Adım 2 — Klasör kuralları**
> **eli10 (basit):** Bu klasörün özel yasaklarını öğrenmek.
> **eli15 (detay):** Kurallar klasör dosyalarında tutulur çünkü genel kuralın tüm detayı taşıması imkânsızdır. Okunmadan girilirse master dokunulmazlık ve dry-run kuralı fark edilmez. Kural bilgisiyle başlayınca revizyon ilk seferde doğru olur. Atlarsan güvenlik/yapı ihlali.

**Adım 3 — Envanter ölç + 1. okuma**
> **eli10 (basit):** Dosyayı bir kez okuyup sayıları ölçmek, sonra karar vermek.
> **eli15 (detay):** Anti-overthink kuralı aynı dosyayı görevde 2. kez okumayı yasaklar (kök `AGENTS.md` §5). Sayı ölçülmeyen yerde `⚠️ VERIFICATION REQUIRED` yazılır, tahmin edilmez. İlk okuma yeterli kanıtı verir. Atlarsan tekrarlı okuma ve uydurma sayı.

**Adım 4 — Değişiklik**
> **eli10 (basit):** İstenen yeri değiştirmek, başka yere dokunmamak.
> **eli15 (detay):** Değişiklik hedefe kilitlenir çünkü eşzamanlı oturumlar ve context lock vardır. Kapsam genişlerse başka oturumun çalışması bozulur. Kilitli diff incelemeyi ve revert'i kolaylaştırır. Atlarsan çakışma ve temizlik işi.

**Adım 5 — Lint kapısı**
> **eli10 (basit):** Sekiz dosyanın da dilbilgisi hatasız mı diye kontrol etmek.
> **eli15 (detay):** Tek komut `composer lint` olarak tanımlıdır, elle kontrol güvenilmez. Ölçümde 8/8 geçti (2026-10-03). Kapıdan geçmeyen iş tamamlanmış sayılmaz. Atlarsan hatalı PHP git geçmişine girer.

**Adım 6 — CLI kapısı**
> **eli10 (basit):** Komutu prova modunda çalıştırıp hata var mı bakmak.
> **eli15 (detay):** `scan --dry-run` yazmadan, `audit` salt-okunur çalışır ve ihlali kural kimliğiyle raporlar. Kapı, veri sözleşmesinin (slug/ULID/sema) korunmasını sağlar. exit 0 olmadan geçilmez. Atlarsan bozuk kayıt arşive girer.

**Adım 7 — Kapı kanıtı**
> **eli10 (basit):** Kontrol çıktısını listeye kanıtıyla yazmak.
> **eli15 (detay):** Checklist kuralı "kanıtsız `- [x]` yok" der; kanıt türü `dosya:satır`, `grep`, `commit` veya `kayıt` olarak etiketlenir. Kapı koşulu satırda yazılıdır. Kanıtsız işaretlenirse denetim değeri kalmaz. P0 kapanmadan P2'ye geçilmez (todos kuralı).

**Adım 8 — Browser testi**
> **eli10 (basit):** Arayüz değiştiyse sayfayı tarayıcıda görmek.
> **eli15 (detay):** Kök `AGENTS.md` §7 bu adımı zorunlu kılar; DOM/layout iddiası kod ile kanıtlanamaz. Faz 3 GUI uygulaması başlayınca devreye girer. Görsel kapı geçince kullanıcı deneyimi güvenceye alınır. Atlarsan gizli yerleşim hatası.

**Adım 9 — Rapor + commit yok**
> **eli10 (basit):** İş bitince haber vermek; commit'i başkasının atması.
> **eli15 (detay):** Subagent commit atmaz (kök `AGENTS.md` §7/§8); commit yetkisi orkestratördedir. Tarih tek elden yazılmazsa iz sürme bozulur. Rapor, kapıyı orkestratöre teslim eden çıktıdır. Atlarsan iş görünmez kalır ya da düzensiz commit oluşur.

### 3.2 Kapılar (Gate)

```text
[G1 KEŞİF: kural + envanter + şablon] ──onay──▶ [G2 UYGULAMA: diff]
        │                                            │
        └── red: görev geri döner                     ├── lint/CLI kırmızı: geri dön
                                                      ▼
                                        [G3 DOĞRULAMA: php -l · CLI · checklist]
                                                      │
                                                      ▼
                                        [G4 RAPOR → ORKESTRATÖR (commit)]
```

| Kapı | Koşul (kanıt) | Red durumunda |
|------|---------------|---------------|
| **G1 Keşif** | [[CLAUDE]] + [[AGENTS]] + [[CONTEXT]] okundu; hedef dosya diskte var; şablon (Guardrail #16) okundu | Görev uygulamaya alınmaz |
| **G2 Uygulama** | Diff yalnız hedef kapsamdaki; `media/` ağacına yazma YOK | Kapsam daraltılır, tekrar denenir |
| **G3 Doğrulama** | `composer lint` → `php -l` 8/8 **VE** ilgili CLI `exit 0` **VE** checklist kanıtı | `.ai/.rules/error-recovery.md` → yeniden yaz → tekrar kontrol (3 başarısız → DUR + 1 soru) |
| **G4 Rapor** | Rapor + `log.md` append; **commit ORCHESTRATÖRDE** | Subagent commit atmaz |
| **G5 Taşıma (özel)** | `ingest` dry-run CSV raporu + STDIN `evet` onayı → yalnız `--commit` | `ingest.php` kilidi zaten bayt kopyalamaz (tek koruma `:328`) |

**eli10/eli15 blokları (§3.2 — 5 kapı):**

**G1 Keşif**
> **eli10 (basit):** İşe başlamadan kuralların ve dosyanın okunduğundan emin olmak.
> **eli15 (detay):** Kapı ayrıdır çünkü yanlış okumadan gelen değişiklik geri alınması pahalıdır. Çıktısı "okundu + karar verildi" işaretidir. Redde görev başlamaz. Atlanırsa kural ihlaliyle üretilir.

**G2 Uygulama**
> **eli10 (basit):** Değişikliğin sadece istenen dosyayla sınırlı kalması.
> **eli15 (detay):** Kapı kapsamı korur; çıktı tek diff'tir. Genişlerse başka oturumun çalışması çakışır. Redde kapsam daraltılır. Yoksa revert alanı büyür.

**G3 Doğrulama**
> **eli10 (basit):** Lint, komut ve kanıtın üçü birden yeşil olmak zorunda.
> **eli15 (detay):** Çıktı: 8/8 lint + CLI exit 0 + checklist kanıtı. Neden ayrı: her biri başka hatayı (sözdizimi · kural · kanıt) yakalar. Hata olursa kurtarma dosyasına gidilip yeniden yazılır. Kapı kapanmadan iş tamam sayılmaz.

**G4 Rapor**
> **eli10 (basit):** Sonucu yazıp commit'i orkestratöre bırakmak.
> **eli15 (detay):** Kapının çıktısı rapordur; commit bu kapıda ATILMAZ. Neden: tarih tek elden yazılmalı. Redde iş görünmez kalır. Kök kural §7/§8'dedir.

**G5 Taşıma (özel kapı)**
> **eli10 (basit):** Dosya taşımak için önce prova, sonra onay gerekir.
> **eli15 (detay):** Kapı ayrıdır çünkü taşıma geri alınamaz tek işlemdir (master + 38 GB ölçek). Çıktısı dry-run CSV raporudur. Onay yoksa tek bayt kopyalanmaz (`ingest.php` kilidi). Redde arşiv değişmez — en güvenli durumdur.

### 3.3 Kapı Sahipleri ve Sıra (todos/checklist ile eşleşme)

| Kapı | Sahip (todos) | Bağımlılık | Kapanış kanıtı |
|------|---------------|------------|----------------|
| P0-1 · CLI denetimi (`php -l` + `audit` + `scan --dry-run`) | backend-architect | PHP kurulu makine | 3 komut exit 0 → checklist **VR-2** |
| P0-2 · MySQL DDL (9 tablo + `taxonomy` = 98) | data-engineer | MySQL 9 erişimi | `COUNT(*)` = 98 + `SHOW TABLES` → **VR-3** |
| P0-3 · Kaynak sayım (7.551 dosya / 38,33 GB) | verinin olduğu PC | veri erişimi | iki sayı tutarsa → **VR-1** |
| P0-4 · `--deep` / yasaklı çağrı denetimi | qa-engineer | yok | **8 PASS / 0 FAIL** (2026-09-29 kaydı: kapanmış) |
| P1 · Karar kapıları (breakpoint, port rolleri, spec → Kalıp D) | sen (onay) → ui-designer / vault-updater | yok | ilgili VR kapanır |
| P2 · `ingest --commit` canlı akışı · `public/` web iskeleti · gerçek medyada audit | backend / qa | P0-1 + veri erişimi | dry-run CSV 0 hata → audit 0 hata |

**eli10/eli15 blokları (§3.3 — kapı sahipleri):**

> **eli10 (basit):** Her kapının bir sahibi ve bir de kanıt koşulu var; iş sırayla, sahiplerine göre yürüyor.
> **eli15 (detay):** Tablo ayrıdır çünkü sahiplik, adımdan bağımsız kimin sorumlu olduğunu gösterir. P0/P1/P2 sırası kritiktir: P0 kapanmadan P2 başlamaz (todos kuralı). Okunması, kimi bekleyeceğinizi söyler. Yazılması, kapı kapanınca checklist'teki `- [ ]` işaretlenebilmesini sağlar. Tablo olmasa kapılar sahipsiz kalır ve süresiz açık durur.

### 3.4 Çelişki / Bilinmeyen Kapısı (Zero-Hallucination)

| Durum | Aksiyon | Sahip |
|-------|---------|-------|
| Vault ↔ disk çelişkisi | Disk kazanır + `⚠️ VERIFICATION REQUIRED` + MO'ya rapor | İlk farkeden |
| Ölçülmüş olmayan sayı | `UNKNOWN` yazılır, tahmin edilmez | Yazar |
| PHP/CLI çalıştırılamadı | Kapı `⚠️ VR` kalır; "geçti" yazılmaz | QA / Backend |
| 3 başarısız düzeltme | DUR + şüpheli varsayım + 1 kısa soru | Yürütücü → MO |
| Secret/anahtar gördü | `[REDACTED]` + hiçbir `.md`'ye yazma | Security |

**eli10/eli15 blokları (§3.4):**

> **eli10 (basit):** Emin olunmayan şey yazılmaz; bilinmeyen yerde kapı açık kalır ve işaretlenir.
> **eli15 (detay):** Kapı ayrıdır çünkü uydurma kanıt, kanıtsız kapının daha tehlikelisidir. Çıktısı `⚠️ VERIFICATION REQUIRED` işaretidir. Disk her zaman vault'un üstündedir. İşaretsiz bırakılırsa sonraki oturum yanlışı doğru sanır.

### 3.5 Sık Yapılan Hatalar (bu klasörde)

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | `scan`/`audit` çıktısını okumadan "geçti" demek | `exit` kodu + satır sayısı raporlanır (0 = hata yok) |
| 2 | `--commit` yazmadan taşıma yapıldığını sanmak | `--commit` yokken tek bayt kopyalanmaz; dry-run raporu tek çıktıdır |
| 3 | `taxonomy.json`'a elle değer eklemek | Kapalı liste: 1 satır + enum senkronu + checklist kanıtı |
| 4 | `catalog.jsonl`'ı elle düzenlemek | Türetilmiş indeks: yalnız `scan.php --rebuild` üretir |
| 5 | `media/` yolunu elle taşıyıp indeksi güncellememek | `path_history` + yeniden `scan` + `audit` 0 hata |
| 6 | Aynı kapı iki oturumda ayrı ayrı kapatmak | Context lock + tek checklist satırı (MO kapanır) |
| 7 | Eski ölçümü ("62 dizin", "PHP yok") tekrarlamak | 2026-10-03 ölçümü CONTEXT §3.7'ye göre yeniden ölçülür |

**eli10/eli15 blokları (§3.6):**

> **eli10 (basit):** Bu işte en sık yapılan yedi hata ve doğrusu yan yana yazılı.
> **eli15 (detay):** Hata tablosu ayrıdır çünkü hatalar süreç içinde tekrar eder, kural metninde kaybolur. Her satırda yanlış ile doğrusu bitişiktir. Okunması aynı hatanın ikinci kez yapılmasını engeller. Yeni hata görülünce bu tabloya eklenir — kapalı liste değildir.

### 3.6 Görev Tipi → Şablon/Kapı Eşlemesi

| Görev tipi | Şablon (Guardrail #16) | Zorunlu kapı | Çıktı |
|------------|------------------------|--------------|-------|
| Yeni `.md` (vault/kapsam içi) | `context-template` §3.4 iskelet + 7 alan + `docType` | §6 kontrol 10/10 | Doküman + 1 index satırı |
| Yeni PHP sınıfı/komut | `declare(strict_types=1)` + `namespace Media;` | `composer lint` 8/8 → 9/9 | Dosya + lint kanıtı |
| Yeni/degisen meta alanı | `media.schema.json` (`version` artır) + `taxonomy.json` (gerekirse) | `audit` exit 0 + checklist kanıtı | Şema diff'i |
| CLI çalıştırma (denetim) | — | `scan --dry-run` / `audit` exit 0 | Rapor satırı |
| Taşma (`ingest`) | — | **G5**: dry-run CSV → STDIN `evet` → `--commit` | `reports/ingest-*.csv` |
| Faz 3 ekranı | `.ai/.templates/ui-design/screen-spec-template` (Kalıp D) | `screens-frontmatter-check` kökten | Ekran dosyası |
| DB indeksleme | `.ai/.sql/mysql/media_catalog.sql` | `EXPLAIN` (P2-10) + VR-3 | SQL diff'i |

**eli10/eli15 blokları (§3.9):**

> **eli10 (basit):** Ne yapacağına göre hangi şablonun ve hangi kontrolün zorunlu olduğu tek tabloda.
> **eli15 (detay):** Eşleme ayrıdır çünkü şablon kuralı (Guardrail #16) her dosya tipinde farklı uygulanır. Her satırda hem şablon hem kapı hem çıktı vardır. Okunması işe doğru şablonla başlanmasını sağlar. Şablon değişince (templates revizyonu) yalnız bu tablo güncellenir. Atlanırsa üretilen dosya vault standardına uymaz ve reddedilir.

### 3.7 Kapı Komutları (tek satır — çalıştırılabilir kanıt)

| Kapı | Komut (repo kökünden) | Beklenen çıktı | Sahip |
|------|-----------------------|----------------|-------|
| Lint | `composer lint` | 8 dosyada "No syntax errors" | backend |
| Tarama (prova) | `php bin/scan.php --dry-run` | yalnız yazılacaklar basılır, dosya değişmez | backend |
| Tarama (yeniden kurulum) | `php bin/scan.php --rebuild` | `catalog/catalog.jsonl` yeniden üretilir | backend |
| Deep hash | `php bin/scan.php --deep` | `MOD: deep` · fark → `HASH-FARK` (HATA) | qa |
| Denetim | `php bin/audit.php` | exit **0** = hata yok · 1 = HATA · 2 = kullanım | qa |
| Deep denetim | `php bin/audit.php --deep` | kasıtlı sapma `HASH-FARK` üretmeli | qa |
| Taşıma (prova) | `php bin/ingest.php` | `reports/ingest-*.csv` + **0 bayt** yazım | backend + security |
| DB kapısı | `mysql … < ../.ai/.sql/mysql/media_catalog.sql` → `SELECT COUNT(*) FROM taxonomy;` | **98** + 9 tablo + 1 view | data |

**eli10/eli15 blokları (§3.7):**

> **eli10 (basit):** Kapıyı geçmenin tek satırlık komutları burada; elle tahmin yok.
> **eli15 (detay):** Komut tablosu ayrıdır çünkü kapı koşulu insan hafızasında tutulmaz, kopyala-çalıştır olmalıdır. Her satırda beklenen çıktı da yazılıdır — "çalıştı" demek yetmez. Ölçüm bu komutlarla yapıldı (lint 8/8). Değişince (yeni bayrak/komut) bu tablo + `composer.json` birlikte güncellenir.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Kod öncesi keşif (kural + envanter + şablon) — onaysız değişiklik yok | Yanlış katman/yanlış dosya yazımı (kök `AGENTS.md` §4) |
| 2 | Aynı dosya görevde 2. kez okunmaz; sayı ölçülmeyen yerde `UNKNOWN` | Anti-overthink + Zero-Hallucination (kök §5) |
| 3 | Hata → `.ai/.rules/error-recovery.md` → yeniden yaz → tekrar kontrol | Kör düzeltme döngüsü (kök §7) |
| 4 | Taşıma yalnız dry-run + onay → `--commit` | Geri alınamaz işlem (ADR-092 §8 m.11) |
| 5 | Kapı kanıtı checklist'e yazılır; kanıtsız `- [x]` yok | Denetim değeri (`docs/checklist.md`) |
| 6 | UI/GUI değişimi → browser testi | DOM/layout kanıtı kodla verilemez (kök §7) |
| 7 | **Commit subagent ATMAZ** | Tarih ve entegrasyon yetkisi orkestratörde (kök §7/§8) |

> **eli10 (basit):** Kurallar sırayı ve güvenliği koruyor; en önemlisi taşımanın onaylanması ve commit'in başkasına bırakılması.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile kod farklı hızda değişir; kod içine gömülse revizyonda kaybolur. Okunmaları kapıların önceden bilinmesini sağlar. Yazmaları denetimin tekrarlanmasını garantiler. Kural değişince (kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → [G1: CLAUDE + AGENTS + CONTEXT + şablon OKU]
  → [K2: ENVANTER ÖLÇ — 1. okumadan karar] → UYGULA (hedef dosya)
  → [G2: KAPSAM KONTROL] → [G3: php -l 8/8 → CLI --dry-run → checklist kanıtı]
  → (Faz 3/GUI ise BROWSER TESTİ) → [G4: RAPOR + log.md append — COMMIT YOK]
  → ORKESTRATÖR (commit)  ·  (Taşıma varsa: G5 dry-run → onay → --commit)
```

Adım ve kapıların tamamı §3.1 / §3.2 tablolarındadır; buradaki zincir sırayı özetler.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: workflow` |
| 2 | Bölüm sırası | §1–§7 sabit, ≤3 başlık seviyesi |
| 3 | Adım tablosu | §3.1 9 satır; her satırda "Neden" + "Atlarsan ne olur" dolu |
| 4 | Kapılar | §3.2 5 kapı; koşul + red sütunu dolu |
| 5 | Commit kuralı | §3.1 #9 + §4 #7 "ATMAZ" ifadesi mevcut |
| 6 | Kapı sahipleri | §3.3 todos/checklist ile eşleşiyor (P0-1..P2) |
| 7 | Placeholder | `{{` kalmadı · emoji yok (yalnız `⚠️ VERIFICATION REQUIRED`) |
| 8 | Wiki-link | `[[...]]` formatı, hedefler diskte var |
| 9 | eli10 + eli15 | §3.1 9 adım + §3.2 5 kapı + §3.3-§3.6 maddelerinde blok var mı (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 10 | Dokunulmaz | Mevcut `CONTEXT`/`CLAUDE`/`AGENTS` değişmedi; commit atılmadı |
| 11 | Görev eşlemesi | §3.6 her satırda şablon + kapı + çıktı dolu |
| 12 | Uzunluk | 300–500 satır; § 7 sabit, alt başlık ≤3 seviye |
| 13 | Kapı komutları | §3.7 her satırda komut + beklenen çıktı + sahip dolu |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Klasör envanteri | [[CONTEXT]] | Ölçüm + çelişki kaydı |
| Klasör kuralları | [[CLAUDE]] | Yasak + guardrail |
| Klasör rolleri | [[AGENTS]] | Dosya → rol + handover |
| Kök master kurallar | [[../AGENTS]] | §4 keşif · §5 anti-overthink · §7 loop |
| Vault süreç | [[../.ai/WORKFLOW]] | Faz kapıları + ADR yaşam döngüsü |
| Hata kurtarma | `../.ai/.rules/error-recovery.md` | G3 kapısı prosedürü |
| Lint kapısı | `composer.json` (`scripts.lint`) | 8 dosyalık `php -l` kanıtı |
| Kapı kanıtı | `docs/checklist.md` · `docs/todos.md` | VR maddeleri + P0/P1/P2 |
| Dizin/adlandırma kuralı | `docs/dizin-yapisi.md` · `docs/adlandirma.md` | G1 okuma listesi |
| Karar kaynağı | [[../.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid]] | Taşıma + sha256 + master |
| Şablon | [[../.ai/.templates/frontend/context-template]] | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** workflow
