---
type: guide
category: ai-thinking
title: "CoreMusic — Ultra Thinking Protocol"
date: 2026-08-16
updated: 2026-08-16
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Ultra Thinking Protocol

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Amaç

Bu dosya, OpenCode AI'ın her kod yazma işleminden önce uyması gereken **ultra düşünme protokolünü** tanımlar. AI'ın hatalı kod yazmasını, .ai referanslarını takip etmemesini ve eksik dosya oluşturmasını önler.

---

## 2. Ultra Düşünme Protokolü (5 Adım)

Her kod yazma işleminden ÖNCE bu 5 adımı uygula:

### Adım 1: Vault Oku (ZORUNLU)
```
 Kod yazmadan önce bu dosyaları OKU:
 → .ai/CLAUDE.md (AI anayasası, guardrails)
 → .ai/AGENTS.md (Agent sınırları, domain boundary)
 → .ai/brain.md (Mimari kararlar)
 → .ai/ROLE.md (Rol tanımı)
 → İlgili ADR'ler (decisions/accepted/)
 → İlgili template'ler (.ai/.templates/)
```

### Adım 2: Bağlamı Anla
```
 Kendine sor:
 → Bu görev hangi domain'de? (Backend, Frontend, Security, DB, HW, FW)
 → Hangi katmanda çalışıyorum? (L0-L3)
 → Hangi dosyaları etkileyeceğim?
 → Mevcut kod yapısını anladım mı?
 → Bağımlılıklar neler?
```

### Adım 3: Hata Kontrolü
```
 Kod yazmadan önce kontrol et:
 → Syntax doğru mu? (brackets, semicolons, types)
 → Import'lar mevcut mu? (doğru paketlerden)
 → TypeScript uyumlu mu? (any kullanımı yasak)
 → Mevcut kod stilini takip ediyor mu?
 → Security riski var mı? (hardcoded secret, injection)
```

### Adım 4: Sonuç Tahmini
```
 Düşün:
 → Bu değişiklik başka dosyaları etkiler mi?
 → Dependency flow'a uygun mu?
 → Edge case'ler var mı?
 → Test yazılabilir mi?
 → Performance etkisi var mı?
```

### Adım 5: Doğrulama
```
 Kodu yazdıktan sonra kontrol et:
 → LSP hataları var mı? (otomatik kontrol)
 → TypeScript compile ediyor mu?
 → Mevcut testleri bozar mı?
 → Template'e uygun mu?
 → Cross-reference'lar geçerli mi?
```

---

## 3. .ai Referans Takibi

### 3.1 Zorunlu Okuma Sırası

Her görev başında bu sırayla oku:

| Sıra | Dosya | Amaç | Timeout |
|------|-------|------|---------|
| 1 | `.ai/CLAUDE.md` | AI anayasası, guardrails | 3s |
| 2 | `.ai/AGENTS.md` | Agent sınırları, routing | 3s |
| 3 | `.ai/WORKFLOW.md` | Süreçler, fazlar | 3s |
| 4 | `.ai/brain.md` | Mimari kararlar | 4s |
| 5 | `.ai/ROLE.md` | Rol tanımı | 3s |
| 6 | `.ai/index.md` | Master katalog | 4s |
| 7 | `.ai/keys.md` | Keyword haritası | 3s |
| 8 | `.ai/MEMORY.md` | Session hafızası | 3s |
| 9 | `.ai/log.md` | Audit trail (son 20 satır) | 2s |
| 10 | İlgili ADR'ler | Karar referansları | Değişken |
| 11 | İlgili template'ler | Dosya şablonları | Değişken |

### 3.2 Domain-Based Okuma

Her agent kendi domain'indeki dosyaları okur:

| Agent | Zorunlu Okuma |
|-------|---------------|
| Backend | `architecture/l2-routing/*.md`, `decisions/accepted/ADR-083*.md`, `shared/src/PageRouter/` gerçek kod |
| Frontend | `ui-design/00-mockup-index.md` → `01-component-inventory.md` → `tokens/design-tokens-master.md` → `architecture/l3-presentation/*.md` |
| Security | `architecture/l1-security/*.md`, `decisions/accepted/ADR-010*.md`, `shared/src/Middleware/` gerçek kod |
| Data | `architecture/l0-infrastructure/*.md`, `.ai/.sql/mysql/*.sql` (18 şema — kök `.sql/` DEĞİL), `shared/src/Database/` |
| Embedded | `projects/NevaEngine/*.md`, `electronic/dsp/*.md`, `electronic/firmware/*.md` |
| QA | `ui-design/03-accessibility-gaps.md`, `ui-design/screens/**/*.md`, `reports/` raporları (`.ai/testing/` dizini YOK) |
| DevOps | `architecture/02-deployment/*.md`, `ecosystem/*.md` |

**Faz 1 düzeltmesi:** Bu tablodaki `testing/*.md` ve `.sql/*.sql` eski referansları gerçek mevcut yollarla değiştirildi (2026-09-08). Kural: domain okuma tablosu her revizyonda Test-Path taramasından geçirilir.

### 3.3 Okuma Derinlik Kuralları

| Görev Tipi | Derinlik | Kanıt Gereksinimi |
|-----------|----------|-------------------|
| Tek dosya düzeltme | Hedef dosya + 2 komşu | Satır referansı |
| Domain revizyonu | Domain index + ilgili ADR + gerçek kod | İzlenebilirlik tablosu |
| Vault revizyonu (bu görev) | Boot 12 + domain index + cross-check | IMPLEMENTED/PLANNED etiketi |
| Yeni özellik | Kontrat + şema + mockup | ADR/plan onayı |

---

## 4. Otomatik Temizlik Protokolü

### 4.1 Hata Tespiti
```
 LSP hata tespit edildiğinde:
 → HATA: "LSP ERRORS DETECTED IN THIS FILE - FIX IMMEDIATELY"
 → Kritik: "CRITICAL: The file you just wrote has errors"
 → Zorunlu: "You MUST fix them before continuing"
 → Yasak: "Do NOT proceed to other tasks until this file has no errors"
```

### 4.2 Otomatik Düzeltme
```
 Hatalı dosya tespit edildiğinde:
 → 1. Hemen düzelt veya sil
 → 2. Gerekirse revert et
 → 3. Kullanıcıya ne olduğunu açıkla
 → 4. Düzeltmeyi doğrula (typecheck, test)
```

### 4.3 Kalite Kontrolü
```
 Her dosya için kontrol et:
 → Syntax doğru mu?
 → Import'lar mevcut mu?
 → Types uyumlu mu?
 → Style tutarlı mı?
 → Security riski yok mu?
 → Template'e uygun mu?
```

---

## 5. Dosya Oluşturma Kuralları

### 5.1 Template Zorunlu (Guardrail #16)
```
 Yeni dosya oluştururken:
 → 1. .ai/.templates/index.md'den uygun template seç
 → 2. Template'i kopyala
 → 3. Değişkenleri doldur ({{VARIABLE}})
 → 4. Gereksiz bölümleri kaldır
 → 5. Frontmatter'i tamamla (7 zorunlu alan)
```

### 5.2 Frontmatter Zorunlu Alanları
```yaml
---
type: guide|system|template|adr
category: kategori-adı
title: "Dosya Başlığı"
date: YYYY-MM-DD
updated: YYYY-MM-DD
status: active|draft|archived
version: X.Y.Z
---
```

### 5.3 Wiki-Link Formatı
```
 Doğru: [[dosya/yolu]] veya [[dosya/yolu|gösterim adı]]
 Yanlış: [dosya](dosya/yolu) veya düz metin link
```

---

## 6. Hata Durumları

### 6.1 Hallüsinasyon Kontrolü
```
 Doğrulanamayan bilgi tespit edildiğinde:
 → Etiketle: "VERIFICATION REQUIRED"
 → Kaynak belirt: Hangi dosyadan geldiğini göster
 → Kullanıcıya sor: "Bu bilgiyi doğrulayabilir misin?"
```

### 6.2 Çelişki Durumu
```
 Vault'ta çelişki varsa:
 → 1. DUR
 → 2. Hangi dosyalarda çelişki olduğunu belirle
 → 3. Kullanıcıya sor: "Vault'ta çelişki var, nasıl devam edeyim?"
 → 4. Onay bekle
```

### 6.3 Eksik Bilgi
```
 Eksik bilgi tespit edildiğinde:
 → 1. Hangi bilginin eksik olduğunu belirle
 → 2. Kullanıcıya sor: "Bu bilgi eksik, ne yapayım?"
 → 3. Alternatif sun (eğer mümkünse)
```

---

## 7. Takip Mekanizması

### 7.1 Dosya Takibi
```
 Her görev için:
 → Oluşturulan dosyaları listele
 → Düzeltilen dosyaları listele
 → Silinen dosyaları listele
 → Değişiklik özetini çıkar
```

### 7.2 Todo Takibi
```
 Her adımda:
 → Başlangıç: "Starting task: [görev adı]"
 → İlerleme: "Step X of Y completed"
 → Tamamlanma: "Task completed: [özet]"
 → Hata: "Error at step X: [hata açıklaması]"
```

### 7.3 Audit Trail
```
 Her değişiklik için:
 → Dosya yolu
 → Değişiklik türü (create/edit/delete)
 → Timestamp
 → Sorumlu agent
 → ADR referansı (varsa)
```

---

## 8. Kalite Standartları

### 8.1 Kod Kalitesi
| Standart | Değer |
|----------|-------|
| TypeScript | `any` kullanımı yasak |
| Comments | Sadece gerekli yerlerde |
| Naming | Descriptive, project conventions |
| Security | Hardcoded secret yasak |
| Testing | Her public function test edilmeli |

### 8.2 Dosya Kalitesi
| Standart | Değer |
|----------|-------|
| Frontmatter | 7 zorunlu alan |
| Wiki-link | Geçerli referanslar |
| Cross-reference | Tutarlı linkler |
| Template | Uygun template kullanımı |
| Boyut | Max 1000 satır/dosya |

---

## 9. Uyarılar

| # | Uyarı | Sonuc |
|---|-------|-------|
| 1 | Vault okumadan kod yazma | Kod geçersiz, revert |
| 2 | Template kullanmadan dosya oluşturma | Dosya geçersiz |
| 3 | Hallüsinasyonwithout verification | İçerik silinir |
| 4 | Domain boundary ihlali | Sistem durur |
| 5 | Security riski olan kod | Kod revert edilir |
| 6 | Eksik dosya oluşturma | Görev başarısız |

---

## 10. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | AI anayasası, guardrails |
| [[AGENTS.md]] | Agent sınırları, routing |
| [[WORKFLOW.md]] | Süreçler, fazlar |
| [[brain.md]] | Mimari kararlar |
| [[ROLE.md]] | Rol tanımı |
| [[index.md]] | Master katalog |
| [[keys.md]] | Keyword haritası |
| [[MEMORY.md]] | Session hafızası |
| [[log.md]] | Audit trail |
| [[engine.md]] | Orkestrasyon motoru |
| [[glossary.md]] | Terim sözlüğü |

---

## 11. Teknoloji Karar Matrisi (Dynamic Stack)

Düşünme aşamasında "hangi dili kullanacağım" sorusunun cevap ağacı. İlke: **teknoloji yığını proje gereksinimine göre seçilir** ([[engine.md]] §9; [[ROLE.md]] §11) — varsayılan dil dogması yoktur.

| Problem Tipi | Önerilen Teknoloji | Karar Öncesi Doğrulama | Etiket |
|--------------|--------------------|------------------------|--------|
| Web servis/API | PHP 8.4 + PDO | composer.json PHP sürümü, ADR-002 uyumu | IMPLEMENTED çekirdek |
| Web panel frontend | Vanilla JS + ITCSS + BEM | ADR-001 kapsamı (web panel); PNG/component envanteri | Spec IMPLEMENTED, kod PLANNED |
| Audio DSP / embedded | C++20 (+ XMOS xcc firmware) | electronic/ spec'leri, ADR-017/038 | PLANNED (kod), spec IMPLEMENTED |
| Yüksek eşzamanlı I/O | Node.js 20+ LTS | ADR-026 kapsam kontrolü, servis sınırı | PLANNED |
| Windows platform araçları | C# / C++ (WDK) | Cross-platform ihtiyaç var mı? ([[engine.md]] §9.5.3) | PLANNED |
| Otomasyon scriptleri | PowerShell | Windows hedefi, `.ai/scripts/` kataloğu | IMPLEMENTED (katalog) |
| Veri modelleme | SQL (BCNF) | `.ai/.sql/mysql/` 18 şema, ADR-003/040 | Şema IMPLEMENTED |
| Bilinmiyor / yeni alan | — | KULLANICIYA SOR + ADR taslağı | — |

**Düşünce kuralları:**
1. Teknoloji seçimi "alışkanlık" ile değil, problem tipi tablosu ile yapılır.
2. Tablo dışı ihtiyaç → uncertainty flag (`teknik`, `orta`) + ADR taslağı; asla sessiz dil değişimi.
3. IMPLEMENTED alanında değişim → ADR + migration zorunlu; PLANNED alanında seçim serbest ama kayıt şart.

---

## 12. Vault Doğrulama Protokolü (Faz 0 Metodolojisi)

Her iddianın kaynakla eşleşmesi bu protokolle sınanır. Aşağıdaki adımlar Faz 0'da (2026-09-08) fiilen uygulanmıştır ve tekrarlanabilir komutlarla desteklenir.

### 12.1 Adımlar

| # | Adım | Yöntem | Çıktı |
|---|------|--------|-------|
| 1 | Envanter | `Get-ChildItem -Recurse -Filter *.md` + satır sayacı | dosya/satır tablosu |
| 2 | Yol doğrulama | MD'lerden çıkarılan `[[...]]` hedeflerine Test-Path | kırık/kayıp listesi |
| 3 | Kod eşleme | Sınıf/dosya adı grep + dosya okuma (ilk ~30 satır) | sınıf → amaç haritası |
| 4 | Paket doğrulama | composer.json okuma (name, require, autoload) | stack matrisi |
| 5 | Sayım cross-check | PNG/ADR/template/skill sayıları ↔ boot iddiaları | tutarlılık tablosu |
| 6 | Etiketleme | Kanıt var → kanıt yolu; yok → `DOĞRULAMA GEREKLİ` | IMPLEMENTED/PLANNED |

### 12.2 Karar Kuralları

1. **Kod kanıtı dokümana üstündür** — çelişkide doküman düzeltilir, kayıt `log.md`'ye girer.
2. **Kanıt yoksa iddia yazılmaz** — `VERIFICATION REQUIRED` / `DOĞRULAMA GEREKLİ` etiketi kullanılır (ADR-005 zero hallucination).
3. **PLANNED etiketi** kod yokluğunun dürüst beyanıdır; PLANNED dosyalara kod-referansı YAZILMAZ, hedef mimari spec yazılır.
4. **IMPLEMENTED etiketi** yalnız Test-Path + dosya okuma ile doğrulanmış iddiaya verilir.
5. **Tekraredilebilirlik** — her bulgu bir komutla yeniden üretilebilir olmalı ([[ROLE.md]] §22.1 komut seti örneği).

### 12.3 Etiket Akışı

```
İddia bulundu → Kodda ara (grep/glob)
   ├─ Bulundu  → Dosya oku → imza/davranış notu → IMPLEMENTED + kanıt yolu
   ├─ Yok      → Hedef mimaride var mı? (architecture-master)
   │              ├─ Evet → PLANNED etiketi + spec referansı
   │              └─ Hayır → DOĞRULAMA GEREKLİ (kullanıcıya sor)
   └─ Belirsiz → uncertainty flag (güvenlik/performans ise yüksek severite)
```

---

## 13. Düşünce Senaryoları (Gerçek Vakalar)

### 13.1 Senaryo — Login Bug Triage (gerçek oturum kaydı)

```
BELİRTİ: İlk login başarısız, ikinci başarılı; /home'da redirect loop.
ADIM 1 (Vault): ADR-011 (Session), ADR-043 (Auth konsolidasyon) okunur.
ADIM 2 (Kod): SessionInitializer.php:104 — `_session_created_at` null → return true (HATA).
ADIM 3 (Hipotez): Session "oluştu" sanılıyor ama timestamp yok → guard yeniden yönlendirir → loop.
ADIM 4 (Çözüm): `return false` + `@mkdir($savePath)` (3 nokta) + çift form handler düzeltmesi.
ADIM 5 (Doğrulama): Login → callback → /home akışı console/network ile sınanır.
ADIM 6 (Kayıt): log.md append + MEMORY.md özet.
```

### 13.2 Senaryo — Kırık Referans Temizliği (Faz 0 vakası)

```
BELİRTİ: Boot dosyalarında archives/prompt*-2026-08-13 hedefleri bulunamıyor (12 link).
ADIM 1: Glob ile archives/ gerçek dosya adları listelenir → 2026-09-01 versiyonları mevcut.
ADIM 2: Kural çıkarımı: tarih uzantılı arşiv referansları sürüm değişince kırılır.
ADIM 3: 5 boot dosyasında tarih hedefleri tek kural ile güncellenir.
ADIM 4: Test-Path ile 0 kırık hedef doğrulanır; log.md append.
DERS: Arşiv dosya adları sürümlüdür; referans verirken uzantıyı sabitleme yerine
      arşiv index'ine bağla (Vault Revizyon Önerisi).
```

### 13.3 Senaryo — Teknoloji Seçimi (dinamik stack)

```
GÖREV: Download servisi kurulacak.
ADIM 1: §11 matris → "Yüksek eşzamanlı I/O" → Node.js 20+ adayı.
ADIM 2: Kapsam kontrolü — ADR-026 (Download Service Architecture) okunur.
ADIM 3: IMPLEMENTED/PLANNED → dizin Test-Path: download.coremusic.net YOK → PLANNED.
ADIM 4: engine §9.4 karar kaydı şablonu doldurulur (gerekçe/trade-off).
ADIM 5: Kullanıcı onayı (PLANNED→APPROVED) → iskelet → sözleşme → kod.
YASAK: Onaysız package.json/npm bağımlılığı eklemek.
```

### 13.4 Senaryo — Mockup'a Uyumlu UI Düzeltmesi

```
GÖREV: C15 toggle bileşeni hedef boyuta çekilecek (WCAG ~32px → 48px).
ADIM 1: 01-component-inventory C15 satırı + 03-accessibility-gaps okunur.
ADIM 2: PNG karşılığı bulunur (.ai/.png/shared-1024 seti) — görsel doğrulama.
ADIM 3: Token kontrolü — a-design-tokens.css ilgili `--space-*`/`--radius-*`.
ADIM 4: Tek bileşen + responsive CSS — ayrı HTML/branch YASAK (brain §18C).
ADIM 5: Tarayıcı testi (console + render) + satır sayacı + log.md append.
DERS: PNG en yüksek otoritedir; token'dan sapma yalnız PNG gerektiriyorsa.
```

### 12.4 Faz 0 Kanıt Kaydı

### 12.4 Faz 0 Kanıt Kaydı

| Protokol Adımı | Faz 0 Sonucu |
|----------------|--------------|
| 1. Envanter | 741 md / 136.261 satır (boş-hariç) |
| 2. Yol doğrulama | ~150 yol sınandı; 60+ kırık küme |
| 3. Kod eşleme | PageRouter/AuthGuard/Middleware/Session/Cache/HomeAuthBridge haritası |
| 4. Paket doğrulama | 4 composer.json → stack matrisi |
| 5. Sayım cross-check | 8 sayım çelişkisi (PNG 19, ADR 79 vb.) |
| 6. Etiketleme | IMPLEMENTED 5 / PLANNED 9 domain ayrımı |

### 12.5 Protokolün Kendi Hatası (meta-dogrulama)

Faz 0'da 4 paralel explorer'dan 3'ü görev yerine bekleme yanıtı döndürdü — protokolün kendisi de hata yapabilir. Ders: protokol çıktısı, protokolün izlediği adımlar kadar güvenilirdir; çıktı boşsa adım tekrarlanır, protokol değiştirilmez.

---

---

## 14. Düşünme Kalitesi Kontrol Listesi

Her görev kapanışında bu liste zihnen (veya açıkça) işaretlenir:

| # | Soru | Evet Kriteri |
|---|------|--------------|
| 1 | Vault okundu mu? | §3.1 sırası tamamlandı |
| 2 | Domain boundary ihlal edildi mi? | [[AGENTS.md]] §2 kontrolü yapıldı |
| 3 | Her iddianın kanıtı var mı? | Kod yolu / Test-Path / etiket |
| 4 | Çelişki taraması yapıldı mı? | Kod ↔ doküman uyumu |
| 5 | Template kullanıldı mı? | Yeni dosya Guardrail #16 |
| 6 | Doğrulama adımı çalıştı mı? | LSP/test/satır sayacı |
| 7 | Audit kaydı düştü mü? | log.md append |
| 8 | Teknoloji kararı kayıtlı mı? | §11 matris + ADR gerekli mi |
| 9 | Belirsizlik raporlandı mı? | uncertainty flag / checkpoint |
| 10 | Truth Mode korundu mu? | 0 sessiz hallüsinasyon |

10/10 "evet" olmadan görev kapatılmaz; kapatılması gerekiyorsa kalan maddeler `log.md`'ye açık borç olarak yazılır.

---

## 15. Bağlam Paketleme Şablonu

Görev dağıtımından önce bağlam şu şablonla paketlenir; eksik alanlı paket görev sırasına girmez:

```text
BAĞLAM:         [görevin tanımı — 1-2 cümle]
PROJE OKUMA:    [dosya listesi + her biri için "neden okunmalı"]
GÖREV:          [net çıktı tanımı — kabul kriteriyle]
KISITLAR:       [ADR'ler, Guardrails, kapsam sınırları, stack etiketi]
DOĞRULAMA:      [nasıl kapanacak — test/komut/satır sayacı]
UNCERTAINTY:    [bilinen belirsizlikler — varsa]
```

**Paketleme kuralları:**
1. `PROJE OKUMA` listesi Test-Path ile doğrulanmış yollar içerir — kırık yollu paket reddedilir.
2. `KISITLAR` alanına stack etiketi (IMPLEMENTED/PLANNED) dahildir — PLANNED alanda "kod var" varsayımı yasaktır.
3. `DOĞRULAMA` alanı §14 kontrol listesinin hangi maddelerini kapatacağını belirtir.
4. Paket, alt agent'a tek seferde verilir; eksik bilgi için ikinci tur paketleme "bağlam yetersizliği" olarak `log.md`'ye yazılır.

---

## 16. Agent'e Özel Düşünce İpuçları

Her profilin düşünme odağı, tipik hatası ve kanıt kaynağı:

| Agent | Düşünme Odağı | Tipik Hata | Kanıt Kaynağı |
|-------|---------------|------------|----------------|
| Master Orchestrator | Görev parçalama + onay kapıları | Kapsamı büyütüp onaysız ilerlemek | engine §5, §10 |
| Backend Architect | Sözleşme önce, sonra kod | Router/middleware sırasını bozmak | routes.php, Middleware sınıfları |
| UI Designer | PNG ↔ bileşen ↔ token üçlüsü | C01-C16 dışına çıkmak | ui-design çekirdek + tokens |
| Security Engineer | Hattın sırası + bypass minimizasyonu | Bypass listesini rastgele büyütmek | CsrfMiddleware kodu |
| Data Engineer | BCNF + şema-önce | SELECT *, ORM cazibesi | 18 şema, DatabaseManager |
| Embedded Engineer | Donanım sınırı + zamanlama | Çip spec'ini dokümandan kopyalayıp doğrulamamak | electronic/hardware |
| QA Engineer | Kabul kriteri önce | Test'i koddan sonra yazmak | reports/, composer dev |
| DevOps Engineer | Tekrarlanabilirlik | Ortama bağımlı script | 02-deployment |
| Audio HW Engineer | Sinyal zinciri uçtan uca | Tek çipa odaklanıp zinciri kırmak | audio-interface.md |
| DSP Firmware Engineer | Zaman bütçesi (buffer/latency) | RTOS/bare-metal karışımı | rtos.md seçim tablosu |
| Windows SW Engineer | Platform sınırları | Cross-platform ihtiyacı C#'a sıkıştırmak | engine §9.5.3 |

---

## 17. Karar Ağacı Örnekleri

### 17.1 "Yeni bir endpoint ekliyorum"

```
Kontrat var mı? (03-contracts)
 ├─ Evet → routes.php'e ekle → AuthGuard kapsaması? → middleware ihtiyacı?
 │         → test → log.md append
 └─ Hayır → kontrat yaz (template) → onay → yukarıdaki dal
```

### 17.2 "Dokümanda X var ama kodda bulamıyorum"

```
§12.3 etiket akışını çalıştır:
kod ara → yok → hedef mimaride var mı?
 ├─ Evet → PLANNED etiketi + spec referansı
 └─ Hayır → DOĞRULAMA GEREKLİ → kullanıcıya sor → karar → log.md
```

### 17.3 "Dil seçmem gerekiyor"

```
§11 matris → problem tipi eşleşmesi
 ├─ Eşleşti → teknoloji + doğrulama adımı → stack bildirimi (log.md)
 ├─ Eşleşmedi → uncertainty flag (teknik/orta) → ADR taslağı → onay
 └─ IMPLEMENTED alan değişimi → ADR + migration zorunlu (§11 kural 3)
```

---

## 18. Protokol Karar Günlüğü

| Tarih | Karar | Gerekçe |
|-------|-------|---------|
| 2026-08-16 | Protokol v1.0.0 oluşturuldu | İlk ultra thinking kuralları |
| 2026-09-08 | §3.2 domain okuma yolları gerçek dosyalara bağlandı | Faz 0: `testing/`, `.sql/` kırık referans düzeltmesi |
| 2026-09-08 | §11 Teknoloji Karar Matrisi eklendi | Dinamik stack ilkesi (Node.js/C++/C#/PHP) |
| 2026-09-08 | §12 Vault Doğrulama Protokolü kodifiye edildi | Faz 0 metodolojisinin tekrarlanabilir hale getirilmesi |
| 2026-09-08 | §13 gerçek senaryolar eklendi | Login bug, kırık referans, stack seçimi vakaları |
| 2026-09-08 | §14-§18 kalite/paket/agent bölümleri eklendi | 500+ satır hedefi + yürütme disiplini |

---

## 19. Paralel Yürütme Kuralları

Çoklu agent yürütmesinde düşünme disiplini:

| Kural | Açıklama | İhlal Sonucu |
|-------|----------|--------------|
| 1. Bağımsız görev paralel | Paylaşılan dosya/kilit içermeyen görevler aynı anda çalıştırılır | Gereksiz seri beklemeler |
| 2. Bağımlı görev sıralı | Şema → API → UI zinciri paralelleştirilemez | Yarım entegrasyon |
| 3. Tek yazar ilkesi | Bir dosyaya aynı anda tek agent yazar | Çelişen edit'ler |
| 4. Checkpoint her 2-3 agent | Sonuç toplanmadan yeni grup açılmaz | Uncertainty kaçağı |
| 5. Prompt doğrulama | Alt agent'a doğrudan komut cümlesi verilir | Bekleme moduna düşme (engine §8.4 S-04) |
| 6. Devam protokolü | Takılan oturum `task_id` ile devam ettirilir | Görev kaybı |

Faz 0 uygulaması: 4 explorer paralel açıldı; 3'ü bekleme moduna düştü → kural 5-6 ile aynı oturumlardan tam rapor alındı (~13-15 dk ek maliyet). Bu kayıt kural 5'in doğrudan kanıtıdır.

---

## 20. Çıktı Formatı Kuralları

Düşünce çıktısının dosyaya/kullanıcıya akış biçimi:

| Çıktı | Format | Zorunlu Alan |
|-------|--------|--------------|
| Kod | Proje konvansiyonu (PHP: strict+final; JS: ES6 module) | Syntax doğrulama |
| Doküman | Vault frontmatter (7 alan) + wiki-link | [[...]] geçerliliği |
| Rapor | Tablo öncelikli, iddia→kanıt sütunu | Kanıt yolu |
| Belirsizlik | `<uncertainty ... />` XML etiketi | type/severity/options |
| Checkpoint | Numaralı blok + onay sorusu | İlerleme restatement |
| Audit | log.md tek satır append | dosya+tür+timestamp+agent |

Kural: Format ihlali içeriğin kabulünü engellemez ama §14 kontrol listesinde 1 borç üretir; 3 borç checkpoint zorunluluğu doğurur.

### 20.1 İlerleme Restatement Örnekleri

```text
"Adım 3/5 tamamlandı — glossary 440/500 satır, kalan: 3 ek blok."
"Faz 1: 4/12 dosya 500+ — engine ✓, glossary ~, ROLE ~, ULTRA-THINKING ~."
"Hata at step 4: LSP tanımsız sabit (§8.1 #7) — kod onayı bekliyor, devam."
```

---

## 21. Ek Düşünme Disiplinleri

### 21.1 Tersine Düşünme (inversion)

Kod yazmadan önce "bu nasıl bozulur?" sorusu: en olası 3 bozulma modu listelenir, her biri için önlem tanımlanır. Örnek: session düzeltmesinde — (1) çift handler, (2) save path yok, (3) timestamp null — üçü de gerçek vakada yaşanmıştır (§13.1).

### 21.2 Kanıt Ağırlığı

Kanıt sıralaması: çalışan kod > test çıktısı > composer/package manifest > boot doküman > arşiv prompt. Çelişkide üst kanıt kazanır; alt kanıt güncellenir ve fark `log.md`'ye yazılır.

### 21.3 Zaman Bütçesi

| Karar Tipi | Bütçe | Aşılırsa |
|-----------|-------|----------|
| Tek dosya edit | 2 dk düşünme | Küçük parçalara böl |
| Domain revizyonu | Faz planına göre | Faz kontrol listesine taşı |
| Teknoloji seçimi | §11 matris + ADR (hız sınırı yok — onay kapısı) | — |
| Kırık referans | Tek kural taraması | Kural tanımla, hepsine uygula |

### 21.4 Dur Bildiğin An

Şu üç durumda düşünmeyi durdur ve kullanıcıya aktar: (1) vault'ta SSOT çelişkisi, (2) kod-doküman uyumsuzluğunun düzeltme yönü belirsiz, (3) 3 başarısız düzeltme (L4). Dur raporu formatı: konum + neden + önerilen çözüm + onay sorusu.

### 21.5 Ölçülebilir Düşünme

Düşünce kalitesini iddia etmek yerine ölç:

| Ölçüt | Nasıl Ölçülür | Hedef |
|-------|---------------|-------|
| Kanıt oranı | Kanıtlı iddia ÷ toplam iddia (dosyada) | ≥95% |
| Kırık referans | Test-Path taraması | 0 |
| Etiket disiplini | Etiketsiz domain iddiası | 0 |
| Senaryo kapsamı | §13 tarzı gerçek vaka sayısı | Her revizyonda ≥1 |
| Satır meşruiyeti | Fluff/doldurma satırı | 0 — her satır bilgi taşımalı |

Bu ölçütler Faz raporlarında sayısallaştırılır ([[engine.md]] §12.6 kontrol listesi) — "düşündüm" iddiası yerine ölçülür sonuç.

---

---

## 22. Sık Düşünce Tuzakları

| # | Tuzak | Belirti | Panzehir |
|---|-------|---------|----------|
| 1 | Varsayımla kodlama | "Sanırım böyle çalışıyor" cümlesi | §12.1 kanıt zinciri |
| 2 | Doküman dogmatizmi | Kodla çelişen MD'yi savunmak | Kanıt ağırlığı (§21.2) |
| 3 | Kapsam şişmesi | "Hepsini birden yapayım" | Faz + kontrol listesi |
| 4 | Placeholder dolgu | 500 satır hedefi için boş içerik | İzlenebilirlik tablosu zorunlu |
| 5 | Seri bekleme | Paralel olabileceği seri yürütmek | §19 kural 1 |
| 6 | Etiket atlaması | IMPLEMENTED/PLANNED'siz domain iddiası | engine §9.2 matrisi |
| 7 | Tekrarlı keşif | Aynı kodu ikinci kez okumak | §18 karar günlüğü + MEMORY |
| 8 | Onay esnetmesi | "Küçük değişiklik, onaysız olsun" | WORKFLOW onay kapısı |

Tuzak tespitinde: ilgili panzehir uygulanır ve olay (tarih + tuzak no) §18 günlüğüne eklenir.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-16
**Mode:** Red Team · Human Mode · Truth Mode
