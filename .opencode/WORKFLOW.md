---
title: "CoreMusic — .opencode/ Çalışma Ortamı İş Akışı"
type: docs
category: opencode
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/WORKFLOW.md + kök WORKFLOW.md"
docType: workflow
---

# CoreMusic — .opencode/ Çalışma Ortamı İş Akışı

**docType:** workflow · **Klasör:** `.opencode/` · **Sorumlu:** MO (workflow), tooling (skill/command)

**Zorunlu Bağlantılar:** [[CONTEXT.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[../AGENTS.md]] · [[../WORKFLOW.md]] · [[../.ai/WORKFLOW.md]]

---

## 1. Amaç

Bu doküman `.opencode/` dizininde bir görevin **adım adım nasıl** yürütüldüğünü tanımlar: her adımın çıktısı, nedeni ve atlanırsa ne olacağı. Dizin hem aracı (config, paket) hem kılavuz (skill, command) hem de akış (.workflows) dosyalarını taşıdığı için kapılar (gates) burada zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Oturum başlangıcı ayrı akıştır | `.opencode/.workflows/session-init.md` (4 aşama — §3.3) |
| Oturum kapanışı vault senkronudur | `.opencode/.workflows/vault-sync.md` (5 soru + 6 adım) |
| Güvenlik denetimi ayrı akıştır (8 adım) | `.opencode/.workflows/security-audit.md` |
| Kapılar ve onay | Kök [[../AGENTS]] §1 (ASKING QUESTIONS) · [[../WORKFLOW]] |
| Commit subagent'ta değil | Kök [[../AGENTS]] §7/§8 |

> **eli10 (basit):** Bu sayfa, bu klasörde iş yaparken hangi adımı hangi sırayla atacağımızı ve nerede durup kontrol edeceğimizi gösterir.
> **eli15 (detay):** Ayrı dosyadır çünkü süreç, kural ve envanter farklı hızda değişir; hepsi tek yerde toplansa revizyonda kaybolur. İçine adım tabloları, kapılar ve mevcut akışların listesi girer. İşe başlarken okunur. Kapı ya da adım değişince bu dosya güncellenir.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.opencode/` üzerinde skill/command/config/doc üretim akışı | Vault fazlarının tamamı → [[../.ai/WORKFLOW]] (12/20 faz) |
| Oturum başlangıcı/kapanışı kapısı (mevcut `.workflows` dosyaları) | Deploy/CI/CD → [[../.ai/AGENTS]] §25 + kök `WORKFLOW.md` |
| QA denetim kapısı (frontmatter · § · placeholder · link · eli10/eli15) | Kod testleri (PHPUnit/Vitest) → ilgili klasör WORKFLOW |
| Yetki sınırı (commit orkestratörde) | Model/sağlayıcı seçimi kararı → kullanıcı |

- **Kullananlar:** MO (kapı + senkron), tooling (üretim), Security (`security-audit`), QA (denetim), DevOps (paket adımı).
- **Ön koşul:** Hedef dosya diskte mevcut; ilgili şablon (Guardrail #16) okunmuş; `git` durumu okunabilir (salt okunur komut).
- **Not:** `docType: workflow` = süreç ağırlıklı; her adımda "Neden" ve "Atlarsan ne olur" doludur.

> **eli10 (basit):** Kapsam, hangi işin burada yürüdüğünü ve hangisinin başka dosyaya ait olduğunu ayırır.
> **eli15 (detay):** Kapsam ayrı çizildi ki bu dosya tüm vault sürecini tekrarlamasın; yalnız bu dizinin kapısını tarif eder. İçine üretim, denetim ve kayıt adımları girer. Süreç tartışmasında önce burası okunur. Kapsam genişlerse önce [[../.ai/WORKFLOW]]'ye sorulur.

---

## 3. Mimari

### 3.1 Adım Tablosu (adım → çıktı → neden → atlarsan ne olur)

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Görevi netleştir (gerekirse `command/prompt-maker`) | Onaylı görev özeti | Yanlış iş yapmayı önler | İstenmeyen değişiklik, revert |
| 2 | Kural oku: kök [[../AGENTS]] + `.opencode/CLAUDE.md` + ilgili `.ai/` dosyası | Kural listesi | Ortam + vault kuralları orada | Guardrail ihlali |
| 3 | İlgili skill yükle (`skills/*/SKILL.md`) | Doğru reçete | Her iş tek reçete ile yürümez | Eksik/yanlış sıralı iş |
| 4 | Hedef dosyayı 1. kez oku + disk ölç | Kanıt + envanter | Anti-overthink: ilk okumadan KARAR | Aynı dosya 2. kez okunur, token israfı |
| 5 | Şablon seç ve üret (`CONTEXT`/`AGENTS`/`WORKFLOW`/skill/command) | Yeni/revize dosya | Guardrail #16 — tutarlı iskelet | Denetlenemez, dağınık içerik |
| 6 | UTF-8 yaz: `vault-utf8-writer.mjs` (`write`/`append`) | BOM'suz dosya | Tek yazma arayüzü | Mojibake, bozuk encoding |
| 7 | QA denetimi: frontmatter · §1-§7 · placeholder · wiki-link · eli10/eli15 | Onay/red | Kırık iş girmesin | Sessiz hata, kırık link |
| 8 | İlgili `.workflows` kapısını işlet (`session-init` / `security-audit` / `vault-sync`) | Kapı kaydı | Akış tekrarlanabilir olsun | Kapı atlanır, iz kalmaz |
| 9 | `log.md`'ye **append** (yalnız ekleme) | Audit satırı | Geçmiş tek yönlü | Denetim izi kaybolur |
| 10 | Rapor yaz; **commit ATMA** | Teslim raporu | Yetki sınırı | Yetkisiz tarih, revert riski |

**Adım eli10/eli15 blokları (başlıca 5 madde):**

**Adım 1 — Görev netleştirme**
> **eli10 (basit):** Önce tam olarak ne istediğimizi anlaşılır hâle getirmek.
> **eli15 (detay):** Ayrı adım çünkü yanlış anlaşılmış görevin çıktısı ne kadar iyi olursa olsun işe yaramaz. Kapı (ASKING QUESTIONS) onaysız sonraki aşamaya geçmez. Özeti yazarsan tartışma tek yerde kalır. Atlarsan tüm iş boşa gider.

**Adım 3 — Skill yükleme**
> **eli10 (basit):** İşe uygun hazır kılavuzu başlamadan açmak.
> **eli15 (detay):** Ayrı adım çünkü kılavuz yoksa herkes aynı işi farklı sırayla yapar. İçerik okunmadan üretim başlarsa şablon ve doğrulama kriterleri atlanır. Kapıyı atlarsan tutarsız dosya kümesi oluşur.

**Adım 6 — UTF-8 yazım**
> **eli10 (basit):** Dosyayı bozmadan, doğru harflerle yazan tek aracı kullanmak.
> **eli15 (detay):** Ayrı adım çünkü el yazımı (PowerShell/Add-Content) Türkçe karakterleri bozar. Araç yedek alır ve yazarken doğrular. Kapıyı atlarsan metin okunmaz hâle gelir ve onarım gerekir.

**Adım 7 — QA denetimi**
> **eli10 (basit):** Yazılanı başkasının gözden geçirmesi: form, sıra, bağlantılar ve basitlik blokları.
> **eli15 (detay):** Ayrı adım çünkü yapan ile denetleyen aynı olursa hata kaçar. Her yeni `.md` için zorunlu son kapıdır. Geçemezse dosya yazana geri döner. Atlarsan kırık link ve eksik bölüm yayılır.

**Adım 10 — Rapor, commit yok**
> **eli10 (basit):** İş bitince haber vermek; kaydı (commit) başkasının atması.
> **eli15 (detay):** Ayrı adım çünkü tarih ve birleştirme yetkisi tek elde toplanmalıdır. Rapor, kapıyı orkestratöre teslim eden çıktıdır. Atlarsan iş görünmez kalır ya da düzensiz commit oluşur.

### 3.2 Kapılar (Gate)

```text
[Adım 1-4 KEŞİF] ──onay──▶ [Adım 5-6 ÜRETİM] ──denetim──▶ [Adım 7-8 DOĞRULAMA]
        │                          │                            │
        └── red: görev geri döner ──┴── red: yazan düzeltir ─────┘
                                        → [Adım 9-10 KAYIT + RAPOR → ORKESTRATÖR]
```

| Kapı | Koşul | Red durumunda |
|------|-------|---------------|
| G1 Keşif | Kural + şablon + disk kanıtı okundu, karar verildi | Görev uygulamaya alınmaz |
| G2 Yetki | Dosya tipi doğru sahipte (AGENTS §3.2 matrisi) | Dosya geri alınır, doğru role gider |
| G3 Şablon/Sır | Guardrail #16 iskeleti + REDACTED (sır yok) | Düzeltme; sır varsa derhal durdurma |
| G4 Doğrulama | §6 tablosu 10/10 | `.ai/.rules/` kural dosyası yok → kök `AGENTS.md` §7 hata akışı (VERIFICATION REQUIRED: `error-recovery.md` diskte YOK) |
| G5 Kayıt | `log.md` append + rapor | Commit **orkestratörde** — subagent atmaz |

> **eli10 (basit):** Kapılar, işin belirli noktalarında durup "her şey doğru mu" diye sormak.
> **eli15 (detay):** Kapılar ayrı tanımlanır çünkü her aşamanın riski farklıdır: yanlış yetki dosyayı, sır ise tüm projeyi riske atar. Red durumunda ne yapılacağı önceden yazılıdır; aksi halde tartışma döngüsü başlar. Kapı atlanırsa hata sonraki aşamaya taşınır. Kapı değişince bu tablo güncellenir.

### 3.3 Mevcut Akış Dosyaları (envanter — 2026-10-03 ölçümü)

| Akış | Yer | Dosya/boyut | Aşama özeti | Ne zaman çalışır |
|------|-----|-------------|-------------|------------------|
| `session-init` | `.opencode/.workflows/` (v1.1.0) | 1 dosya · 69 satır | 4 aşama: (1) Boot 16 dosya / max 36s, (2) Prompt entegrasyonu 4 dosya / max 14s, (3) Session vault sync 5 soru / max 10s, (4) Session başlangıç kaydı şablonu | Oturum başında |
| `vault-sync` | `.opencode/.workflows/` (v1.0.0) | 1 dosya · 44 satır | Başlangıç 5 soru + Bitiş 6 adım (vault'a yaz · `log.md` timestamp · `MEMORY.md` güncelle · wiki-link doğrula · halüsinasyon sweep · root MD güncelle) + root MD güncelleme sırası | Oturum kapanışında |
| `security-audit` | `.opencode/.workflows/` (v1.0.0) | 1 dosya · 43 satır | 8 adımlı kontrol listesi (OWASP, middleware sırası, şifreleme, CSRF/CSP, session, credential vault, rapor, düzeltme) + OWASP Top 10:2025 matrisi (A01-A10) | Güvenlik talebinde / periyodik |
| Kök akış seti | `.workflows/` (repo kökü) | 9 dosya | `adr-creation` · `architecture-write` · `CLAUDE` · `deployment` · `hallucination-control` · `orchestrator-flow` · `security-audit` · `session-init` · `vault-sync` | İlgili iş anında |

- **İlişki:** `.opencode/.workflows/` (3 dosya) kök `.workflows/` (9 dosya) ile **konu olarak örtüşür**; kök seti daha geniştir. Dosyaların birbirinin kopyası olup olmadığı OKUNMADI — VERIFICATION REQUIRED (satır bazında karşılaştırma yapılmadı).
- **Çelişki notu:** `session-init` boot listesi 16 satır taşır; `.ai/AGENTS.md` §25.4 kanonik boot'u 14 `.ai/` kök dosya + FULL 17 öğe olarak tanımlar. İki sayı **farklı listelerdir** (session-init `ui-design` 2 + `CHECKLIST` ekler) — çelişki sayısal, birleştirme için MO kararı gerekir.

> **eli10 (basit):** Üç hazır akış var: oturumu başlat, güvenlik taraması yap, bitince kaydı güncelle; ayrıca kökte daha geniş bir set duruyor.
> **eli15 (detay):** Akışlar ayrı dosyalardır çünkü her biri farklı kapıda çalışır ve ayrı güncellenir. İçine adım tabloları ve süre üstleri yazılır. Oturum başında, güvenlik talebinde ve kapanışta okunurlar. Liste değişince bu tablo güncellenir; sayılar ölçümle kanıtlanır.

### 3.4 Skill ve Komut Çağrım Sırası

```text
KULLANICI İSTEĞİ → keyword eşlemesi (AGENTS §3.4)
  → skill seç (skills/<ad>/SKILL.md) → komut çağrısı (command/<ad>.md) varsa
  → §3.1 adımları → kapılar → log.md append → rapor
```

| Durum | Ne çağrılır | Örnek |
|-------|-------------|-------|
| Görev başlangıcı | `command/prompt-maker` | Ham istek → yapılandırılmış görev |
| Koordinasyon/çok-agent | `skills/orchestration` | Paralel iş dağıtımı |
| İddia doğrulama | `skills/truth-engine` | Sayı/versiyon iddiası |
| CSS/JS/ITCSS işi | `skills/ui-workbench` | Katman/şablon üretimi |
| Şema/sorgu işi | `skills/db-engine` | MySQL/BCNF |
| Paket senkronu | `skills/composer-sync` | composer güncellemesi |
| Doğrulama döngüsü | `skills/verify-loop` | PHP/CSS/JS kontrolü |
| İşlem sonrası kayıt | `skills/vault-sync-post` + `.workflows/vault-sync` | Oturum kapanışı |
| İkinci bakış | `skills/agent-debate` | Karar tartışması |
| Bağlam özeti | `skills/context-report` | Rapor teslimi |

### 3.5 Vault İddiası ↔ Disk Çelişkileri (disk kazanır)

| İddia (kaynak) | Disk gerçeği (2026-10-03) | İşaret |
|----------------|---------------------------|--------|
| `.ai/AGENTS.md` §14: 8 aktif skill | **9 `SKILL.md`** (`verify-loop` fazladan) | VERIFICATION REQUIRED |
| Kök `AGENTS.md` §7 #6: `.ai/.rules/error-recovery.md` | `.ai/.rules/` = yalnız `senior-mode.md` | VERIFICATION REQUIRED — G4 kapısında kurtarma dosyası YOK |
| `session-init` boot 16 öğe ↔ `.ai/AGENTS.md` §25.4 (14 + FULL 17) | İki farklı liste — birleştirme yapılmadı | VERIFICATION REQUIRED |
| `.opencode/.workflows` ↔ kök `.workflows` | 3 dosya ↔ 9 dosya; içerik karşılaştırması yapılmadı | VERIFICATION REQUIRED |
| Skill arşivi `_archive-keep/` 20 dosya | **20 dosya** — doğrulandı | dogrulandi |

### 3.6 Adım → Kapı Eşlemesi

| Adım (§3.1) | Kapı (§3.2) | Çıktının nereye gittiği | Red kaynaklı geri dönüş |
|-------------|-------------|-------------------------|-------------------------|
| 1 Görev netleştirme | G1 Keşif | Onaylı özet → MO | Kullanıcıya soru (gate) |
| 2 Kural okuma | G1 Keşif | Kural listesi → üretim | Kural yoksa/çelişiyorsa DUR |
| 3 Skill yükleme | G1 Keşif | Doğru reçete | Yanlış skill → routing'e dön |
| 4 Ölçüm + karar | G1 Keşif | Kanıtlı karar | Ölçüm yoksa `UNKNOWN` yazılır |
| 5 Üretim (şablon) | G2 Yetki + G3 Şablon | Yeni dosya | Yetkisiz dosya → doğru role |
| 6 UTF-8 yazım | G3 Şablon/Sır | Doğrulanabilir dosya | Mojibake → `repair` (yedekli) |
| 7 QA denetimi | G4 Doğrulama | Onay/red | Red → yazan düzeltir |
| 8 Akış kapısı | G4 Doğrulama | Kapı kaydı (session/security/sync) | Kapı başarısız → adıma geri |
| 9 `log.md` append | G5 Kayıt | Audit satırı | Append dışında yazım yasak |
| 10 Rapor | G5 Kayıt | Teslim → orkestratör | Commit orkestratörde kalır |

**eli10 / eli15 (bu bölümün kendi bloğu):**

> **eli10 (basit):** Hangi adımın hangi kontrol noktasına bağlandığını gösteren tablo.
> **eli15 (detay):** Eşleme ayrı yazılır çünkü adım ile kapı farklı kişilerde olabilir; bağ koparsa denetimsiz iş biter. İçine adım numarası, kapı ve red kaynaklı dönüş yolu girer. Üretim sırasında bakılır. Kapı ya da adım değişince bu satır güncellenir.

### 3.7 Bilinen Riskler ve Sınırlar (2026-10-03 ölçümü)

| # | Risk / sınır | Etki | Davranış |
|---|--------------|------|----------|
| 1 | `.ai/.rules/error-recovery.md` **diskte YOK** (`.rules/` = yalnız `senior-mode.md`) | G4 kapısında kurtarma dosyasına erişilemez | Kök `AGENTS.md` §7 hata akışı kullanılır — VERIFICATION REQUIRED |
| 2 | `session-init` boot 16 öğe ↔ `.ai/AGENTS.md` §25.4 (14 + FULL 17) | Boot sırasında hangi listenin geçerliği belirsiz | İki farklı liste — MO kararı gerekir |
| 3 | `.opencode/.workflows` (3) ↔ kök `.workflows` (9) — içerik karşılaştırması yapılmadı | Çift akış riski | Kopya olup olmadığı bilinmiyor |
| 4 | Skill içerikleri okunmadı (yalnız dosya adı/ölçüm) | Reçete detayı bilinmiyor | Gerektiğinde ilgili `SKILL.md` okunur |
| 5 | `opencode.json` tamamı okunmadı (44.862 bayt) | Ayar iddiası yazılamaz | Yalnız bilinen anahtar yazılır |
| 6 | `rules/` dizini boş | Kural dosyası bekleniyor sanılabilir | 0 dosya olarak yazılır, var sayılmaz |
| 7 | Eşzamanlı oturum | Aynı dosyaya çift yazım | Context lock; en esik kilidi MO kırar |
| 8 | Uzun bekleme / tekrarlı düzeltme | Token israfı, kör döngü | Anti-overthink: dosya 2. kez okunmaz; 3 hatada DUR |

> **eli10 (basit):** Burada işin nelere takılabileceği ve ne yapacağımız yazılı.
> **eli15 (detay):** Riskler ayrı tablodur çünkü gizlenirse kapılar sahte geçilir. İçine risk, etki ve davranışı yazarız. Üretimden önce okunur. Risk kapanınca satır güncellenir; kapanmayan açık olarak kalır.

---

### 3.8 Oturum Kapanış Kontrol Listesi (imza satırları)

| # | Kontrol | Kanıt | İmza sahibi |
|---|---------|-------|-------------|
| 1 | Üretilen dosyalar yazıldı mı (4 hedef) | `vault-utf8-writer verify` çıktısı | yazan (tooling/MO) |
| 2 | BOM / mojibake / CJK sıfır mı | verify: `hasBom=false`, `mojibake=0`, `cjk=0` | yazan |
| 3 | Bölüm sırası §1–§7 tam mı (7/7) | başlık sayımı | QA |
| 4 | Doldurulmamış placeholder kalmadı mı | arama sonucu 0 | QA |
| 5 | Wiki-link hedefleri diskte var mı | `wiki-link-check.ps1` (kökten) | QA |
| 6 | eli10 + eli15 blokları eşit mi | etiket sayımı (her maddede 1+1) | QA |
| 7 | Envanter sayıları ölçümlü mü | §3 tabloları + ölçüm tarihi | MO |
| 8 | `log.md`'ye eklendi mi (append) | `log.md` son satır | MO |
| 9 | Commit atılmadı mı | `git status` (salt okunur) | orkestratör |
| 10 | Açık `VERIFICATION REQUIRED` listesi raporlandı mı | Rapor satırları | MO |

> **eli10 (basit):** İş kapanırken tek tek işaretlenen on maddelik liste; hangi işi kimin onayladığı belli olsun diye.
> **eli15 (detay):** Liste ayrıdır çünkü kapanış, üretimden sonraki son denetimdir ve imzasız iş bitmiş sayılmaz. İçine kontrol, kanıt ve imza sahibi yazılır. Her oturum kapanışında okunur. Kriter değişince satır güncellenir; eksik madde varsa iş orkestratöre geri döner.

---

### 3.9 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi adımın ne yaptığı kaybolur |
| 2 | **Tek sorumluluk (tek iş)** | Her akışın tek görevi ve tek sahibi olur | Dosya büyüyüp okunmaz; "bu adımı kim ekledi" cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak değer/kurulum tek yerde (config) durur | Ayar her dosyada tekrar eder → değişince hepsi taranır |
| 4 | **Cihaz izolasyonu** | Ortam ayarı proje verisine yayılmaz | Ayar kod içine yayılınca sürüm değişimi kodu bozar |
| 5 | **Vendor karantinası** | Paket ve arşiv ayrı durur (`node_modules/`, `_archive-keep/`) | Dış içerik içimize karışıp güncellemede kaybolur |

> **eli10 (basit):** Adımlar küçük kutulara bölünmüş; çünkü bir kutuyu değiştirmek diğerini bozmaz.
> **eli15 (detay):** Süreç ayrı dosyalarda tutulur ki her işin kendi kapısı belli olsun: oturum başlangıcı, güvenlik ve kapanış ayrı reçetelerdir. Aynı yerde toplansa her iş için tüm süreç okunur, odak kaybolur. Okunması, işe başlamadan sırayı görmeyi sağlar. Bölünmezse "adım atlandı mı" sorusu cevapsız kalır.

---

## 4. Kurallar

| # | Kural | Neden var | Ref |
|---|-------|-----------|-----|
| 1 | Onaysız kapı geçilmez (ASKING QUESTIONS) | Yanlış işi baştan önler | Kök [[../AGENTS]] §1 |
| 2 | Aynı dosya görevde 2. kez okunmaz | Anti-overthink / token israfı | Kök [[../AGENTS]] §5 |
| 3 | Yeni `.md` Guardrail #16 şablonundan üretilir | Tutarlı denetim | [[../.ai/.templates/frontend/context-template]] |
| 4 | Yazım yalnız `vault-utf8-writer.mjs`; `log.md` yalnız `append` | UTF-8 ve bozulmaz geçmiş | `.ai/scripts/vault-utf8-writer.mjs` |
| 5 | `log.md` geçmişine dokunulmaz (append-only) | Denetim izi | [[../.ai/AGENTS]] §25.3 #3 |
| 6 | QA denetimi üretimden ayrı eldedir | Yapan ≠ denetleyen | [[AGENTS]] §3.1 |
| 7 | Sır/anahtar `.md` içine yazılmaz | REDACTED | Kök [[../AGENTS]] §3 |
| 8 | 3 başarısız düzeltme → DUR + 1 kısa soru | Kör deneme döngüsü | Kök [[../AGENTS]] §5 |
| 9 | **Commit subagent tarafından ATILMAZ** | Tarih/entegrasyon yetkisi orkestratörde | Kök [[../AGENTS]] §7/§8 |
| 10 | Emoji/dekoratif işaret yasak; doldurulmamış placeholder kalmaz | Şablon denetimi | [[../.ai/.templates/frontend/context-template]] §4.1 #6/#8 |

> **eli10 (basit):** Bu kurallar işin hızlı ve doğru yürümesini sağlar; en önemlisi önce onay, sonra üretim, en sonda kontrol.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile kod farklı hızda değişir; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazılmaları, denetimin tekrarlanmasını garantiler. Kural değişince (kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → [G1: KURAL + ŞABLON + ÖLÇÜM oku] → SKILL SEÇ → [G2: DOĞRU SAHİP]
  → ÜRET (UTF-8 writer) → [G3: ŞABLON/SIR] → QA DENETİMİ [G4]
  → İLGİLİ AKIŞ KAPISI (session-init | security-audit | vault-sync)
  → log.md APPEND → [G5: RAPOR — commit yok] → ORKESTRATÖR
```

Adım ve kapıların tamamı §3.1 / §3.2 tablolarındadır; buradaki zincir sırayı özetler.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: workflow` |
| 2 | Bölüm sırası | §1–§7 sabit, ≤3 başlık seviyesi |
| 3 | Adım tablosu | Her adımda "Çıktı" + "Neden" + "Atlarsan ne olur" dolu |
| 4 | Kapılar | §3.2'de G1-G5; G4/G5 red durumu yazılı |
| 5 | Commit kuralı | §3.1 #10 + §4 #9 + G5 "ATMAZ" ifadesi (3 yerde) |
| 6 | Envanter | `.opencode/.workflows` 3 dosya (69/44/43 satır) · kök `.workflows` 9 dosya — ölçümle eşit |
| 7 | eli10 + eli15 | §1-§5 maddelerinin altında etiketli blok; eli10 ≤2 cümle, eli15 3-4 cümle |
| 8 | İçerik neden ayrı | §3.9 tablosu 5 satır |
| 9 | Placeholder / Wiki-link | Doldurulmamış placeholder yok · wiki-link formatı, hedefler diskte var |
| 10 | Dokunulmaz | `CLAUDE.md` / kök boot / `.ai/` boot dosyaları değiştirilmedi; commit atılmadı |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Bu dizin envanteri | [[CONTEXT]] | Ölçülmüş dosya/dizin sayıları |
| Bu dizin rolleri | [[AGENTS]] | Sahiplik · routing · handover |
| Ortam kuralı (MEVCUT) | [[CLAUDE]] | Çalışma ortamı kural özeti |
| Kök master kurallar | [[../AGENTS]] | §1 kapı · §5 anti-overthink · §7 loop · §8 prompt-maker |
| Kök süreç | [[../WORKFLOW]] | Genel faz/kapı tanımı |
| Vault süreç (SSOT) | [[../.ai/WORKFLOW]] | 12/20 faz, ADR lifecycle, hard gates |
| Agent registry | [[../.ai/AGENTS]] | Routing §6 · handover §9 · boot §24.2 |
| Context şablonu | [[../.ai/.templates/frontend/context-template]] | İskelet + §4.1 + §5 workflow zinciri |
| Oturum başlangıcı | `.opencode/.workflows/session-init.md` | 4 aşama boot akışı |
| Oturum kapanışı | `.opencode/.workflows/vault-sync.md` | 5 soru + 6 adım |
| Güvenlik taraması | `.opencode/.workflows/security-audit.md` | 8 adım + OWASP matrisi |
| Kök akış seti | `.workflows/` (repo kökü) | 9 dosya — geniş akış arşivi |
| Yazım/doğrulama aracı | `.ai/scripts/vault-utf8-writer.mjs` | UTF-8 yazma + verify |
| Link denetimi | `.ai/scripts/wiki-link-check.ps1` | Wiki-link doğrulama (kökten) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** workflow
