---
title: "CoreMusic — Test Senaryosu: Erişilebilirlik (A11Y)"
type: test-scenario
category: testing
version: 1.0.0
status: active
authority: "Test Senaryosu — SSOT: personas/test-senaryolari/a11y-erisilebilirlik"
updated: 2026-09-26
---

# CoreMusic — Test Senaryosu: Erişilebilirlik (A11Y)

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/methodology]] · [[personas/research-bank]] · [[personas/test-scenarios-mapping]] · [[personas/persona-template]] · [[ADR-023-persona-driven-testing]] · [[personas/test-senaryolari/a11y-erisilebilirlik]]

---

| Alan | Değer |
|------|-------|
| Dosya Adı | `a11y-erisilebilirlik.md` |
| Dosya Yolu | `.ai/.personas/test-senaryolari/a11y-erisilebilirlik.md` |
| Dosya Tipi | Test senaryosu (6 senaryodan 1'i) — **karar DEĞİLDİR** (ADR değil) |
| Hedef Kitle | QA Engineer (persona-test), UI Designer (a11y), Vault Steward (denetim) |
| Yapı | H1 + Zorunlu Bağlantılar + §1 Amaç → §7 Referanslar (7 bölüm + 7 alanlı frontmatter) |
| ADR-023 Karşılığı | 20 persona matrisi **satır 15 — Erişilebilirlik kişisi** (§2.2a; kaynak: eski vault `test-senaryolari/a11y-erisilebilirlik.md`) |
| Test Seviyeleri | Seviye 1 AI Rol · Seviye 2 Browser MCP · Seviye 3 Playwright · Seviye 4 Rapor (`[[personas/methodology]]` §2.2) |
| Eşik Kaynağı | `[[personas/research-bank]]` **P5 (WCAG 2.2 AA — 13 satır `VERIFIED`)** + **P8 (Test Metodolojisi — 11 satır `VERIFIED`)** |
| WCAG Kapsamı | P5.2'deki **10 AA kriteri** — bu senaryoda her AA kriteri için ayrı satır vardır |
| Adım Sayısı | **20** (asgari 10 — §5.3) |
| Zorunlu Blok | §4.0 Şablon-Önce Kural Bloğu (silinemez) |
| Eski Kaynak | `coremusic.net.old/.ai/personas/test-senaryolari/a11y-erisilebilirlik.md` (785 satır, **salt okunur**) — iskelet alındı, yeniden yazıldı + doğrulandı + genişletildi |
| Kayıt Kuralı | Değişiklik Geçmişi **append-only** — mevcut satıra dokunulmaz |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya/klasör adı ASCII |
| Authority | `Test Senaryosu — SSOT: personas/test-senaryolari/a11y-erisilebilirlik` |
| Governance | Red Team · Human Mode · Truth Mode |
| Versiyon | 1.0.0 (ilk üretim — 2026-09-26) |

---

## §1 Amaç

Bu dosya, CoreMusic'in **erişilebilirlik test senaryosudur**: WCAG 2.2 AA kriterlerinin (kontrast, odak görünürlüğü, hedef boyutu, sürükleme, erişilebilir kimlik doğrulama) hangi adımlarla, hangi metriklerle ve hangi persona gruplarına göre test edileceğini tanımlar. Eski vault'taki 785 satırlık senaryo **kopyalanmamış**; iskelet alınıp `[[personas/research-bank]]` P5/P8 eşikleriyle **yeniden doğrulanarak** genişletilmiştir (ADR-005 Zero Hallucination).

| Boyut | Değer |
|-------|-------|
| Ne taşır | A11Y test blokları, ≥10 adımlık adım tablosu (Adım · Eylem · Beklenen · Doğrulama · Metrik · WCAG), AA kriter karşılıkları, persona/mood eşlemesi, KVKK çocuk adımı |
| Ne taşımaz | WCAG kriter tanımlarının kaynağı (→ `[[personas/research-bank]]` P5), test seviyesi/akışı (→ `[[personas/methodology]]`), senaryo × grup matrisi (→ `[[personas/test-scenarios-mapping]]`), karar (→ `[[ADR-023-persona-driven-testing]]`) |
| Neden yazıldı | Eski senaryo research-bank'tan **önce** yazılmıştı; eşikler kaynaksızdı → P5/P8 ile yeniden bağlandı |
| Kim okumalı | A11y audit çalıştıran her ajan (QA Engineer birincil, UI Designer ikincil) |
| Kanıt zinciri | `research-bank` P5/P8 (eşik) → `persona-template` (alan) → persona dosyası → **bu dosya (senaryo)** → `ADR-023` (gate) |

### §1.1 Bu Dosya Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Dosya | Bu Dosya Kullanılmaz |
|---------|-------------|----------------------|
| WCAG 2.2 AA test adımları ve kriter karşılıkları | ✅ Bu dosya | — |
| Kriter numarası/eşiğinin kaynağı | `[[personas/research-bank]]` P5 | ❌ (burada yalnız **taşınır**) |
| Test seviyesi, PREPARE→EXECUTE→REPORT akışı | `[[personas/methodology]]` | ❌ |
| Hangi persona grubu hangi senaryoda | `[[personas/test-scenarios-mapping]]` | ❌ |
| "Coverage %90 / PR gate" kararı | `[[ADR-023-persona-driven-testing]]` | ❌ (burada yalnız başvurulur) |

**Ayırıcı test:** "Bu bir test **adımı mı**?" → bu dosya. "Bu bir **eşik/kaynak mı**?" → research-bank. "Bu bir **karar mı**?" → ADR.

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| WCAG 2.2 AA kriteri başına test adımı (P5.2 onayı) | Kriter numaralarının araştırma süreci (→ `[[personas/research-bank]]` P5) |
| Klavye · ekran okuyucu · kontrast · hedef boyutu · kimlik doğrulama testleri | Test seviyesi tanımı ve rapor formatı (→ `[[personas/methodology]]`) |
| Performans eşikleriyle (P8) a11y adımlarının çapraz kontrolü | Persona profili, Big Five, mood tanımları (→ `[[personas/persona-template]]`, `[[personas/mood-taxonomy]]`) |
| 16 yaş altı (çocuk) senaryolarında KVKK/veli onayı adımı (P6) | Coverage gate'i ve CI iş akışı (→ `[[ADR-023-persona-driven-testing]]`) |

*Alt konular:* AA kriter matrisi → klavye/odak → ekran okuyucu → kontrast → hedef boyutu → form/kimlik doğrulama → çocuk erişilebilirliği → performans çapraz kontrol → rapor.
Kapsam dışı için: eşik → `[[personas/research-bank]]`, akış → `[[personas/methodology]]`, karar → `[[ADR-023-persona-driven-testing]]`.

### §2.2 Hedef Kitle

| Kitle | Bu Dosyayı Nasıl Kullanır |
|-------|---------------------------|
| QA Engineer | §5.3 adım tablosunu sırayla çalıştırır; §6.1 ile kapatır |
| UI Designer | §3.4'teki kriter → bileşen eşleşmesini tasarım denetiminde kullanır |
| Persona-test ajanı | §3.3 persona/mood eşlemesinden satırını okur |
| Vault Steward | §6.4 Quality Report ile denetler |

### §2.3 Kapsam Dışı İstisnalar

| Durum | Ne Yapılır |
|-------|-----------|
| P5'te olmayan bir AA kriter numarası isteniyor (ör. 1.4.5, 2.4.3, 3.3.x eksiksiz) | `⚠️ VERIFICATION REQUIRED` (X-7) — kriter **test edilmez**, rapora boş satır olarak girer |
| Kontrast **hesaplaması** isteniyor (persona renkleri) | Eşik VERIFIED, hesap `⚠️ DERIVED` (P5.5) — renkler ayrıca ölçülür |
| "AAA uyumlu" iddiası | **Yasak** — AAA CoreMusic hedefi değildir (P5.5) |
| Dosya adı/yer değişikliği | In-Place → onay olmadan **DEĞİŞTİRİLMEZ** |

---

## §3 Mimari — Senaryo Yapısı

### §3.0 Dizin ve Bağımlılık Ağacı

```text
.ai/.personas/
├── index.md                    → 68 persona / 6 grup kataloğu (DOKUNULMAZ)
├── mood-taxonomy.md            → 25 mood kümesi (DOKUNULMAZ)
├── test-scenarios-mapping.md   → senaryo × grup matrisi (DOKUNULMAZ)
├── research-bank.md            → P1-P8 veri (66 bulgu / 58 VERIFIED) — eşik kaynağı (DOKUNULMADI)
├── methodology.md              → 4 test seviyesi + PREPARE→EXECUTE→REPORT (DOKUNULMADI)
└── test-senaryolari/
    ├── a11y-erisilebilirlik.md     → BU DOSYA (ADR-023 satır 15)
    ├── browser-navigasyon.md       → kardeş senaryo (satır 17)
    ├── muzik-kesfi.md              → kardeş senaryo (satır 18)
    ├── playlist-olusturma.md       → kardeş senaryo (satır 19)
    ├── sosyal-paylasim.md          → kardeş senaryo (satır 20)
    └── arabesk-dans-mood-gecis.md  → kardeş senaryo (satır 16)

Bağımlılık: research-bank P5/P8 (eşik) → methodology (seviye) → BU DOSYA (adım) → ADR-023 (gate)
```

### §3.1 Test Blokları (A11Y-001 … A11Y-014)

| Blok | Konu | Birincil AA Kriteri | Seviye |
|------|------|---------------------|--------|
| A11Y-001 | WCAG 2.2 AA genel tarama (POUR) | 1.4.3 · 1.4.11 · 2.4.7 | 2 · 3 |
| A11Y-002 | Klavye navigasyonu + odak yönetimi | 2.4.7 · 2.4.11 | 2 · 3 |
| A11Y-003 | Ekran okuyucu — NVDA (Windows) | 1.3.1 ⚠️ · 4.1.2 ⚠️ | 2 |
| A11Y-004 | Ekran okuyucu — VoiceOver (macOS/iOS) | 1.3.1 ⚠️ · 4.1.2 ⚠️ | 2 |
| A11Y-005 | Ekran okuyucu — TalkBack (Android) | 1.3.1 ⚠️ · 4.1.2 ⚠️ | 2 |
| A11Y-006 | Renk kontrastı (metin + UI) | 1.4.3 · 1.4.11 | 2 · 3 |
| A11Y-007 | Metin büyütme %200 / reflow | 1.4.10 ⚠️ VERIFICATION REQUIRED | 2 |
| A11Y-008 | Hareket azaltma (`prefers-reduced-motion`) | 2.3.3 ⚠️ VERIFICATION REQUIRED | 2 |
| A11Y-009 | Dokunmatik hedef boyutu | 2.5.8 (≥24×24 CSS px) · 2.5.7 | 2 · 3 |
| A11Y-010 | Form etiketi ve hata mesajı | 3.3.8 · 3.3.7 | 2 · 3 |
| A11Y-011 | ARIA nitelikleri (5 Rules of ARIA) | 4.1.2 ⚠️ VERIFICATION REQUIRED | 2 |
| A11Y-012 | Çocuk dostu erişilebilirlik + veli onayı | 2.5.8 · KVKK P6 (`⚠️ DERIVED`) | 1 · 2 |
| A11Y-013 | Lighthouse a11y audit (otomatik) | 1.4.3 · 2.4.7 · 2.5.8 | 3 |
| A11Y-014 | Performans çapraz kontrol (P8 eşikleri) | — (performans adımı) | 2 · 3 |

*`⚠️ VERIFICATION REQUIRED` işaretli kriterler P5'te **tek tek açılmadı** (X-7) → numara olarak kullanılır ama `kaynak` satırı olmadan "doğrulanmış" sayılmaz.*

### §3.2 AA Kriter → Eşik Tablosu (research-bank P5 — `VERIFIED`)

| Kriter | Başlık | Eşik | WCAG 2.2 Yeni mi |
|--------|--------|------|------------------|
| 1.4.3 | Contrast (Minimum) | Metin ≥ **4.5:1**; büyük metin ≥ 3:1 | Hayır |
| 1.4.11 | Non-text Contrast | UI bileşenleri/grafikler ≥ **3:1** | Hayır |
| 1.4.13 | Content on Hover or Focus | AA | Hayır |
| 2.4.7 | Focus Visible | AA | Hayır |
| 2.4.11 | Focus Not Obscured (Minimum) | AA | **EVET** |
| 2.5.7 | Dragging Movements | AA | **EVET** |
| 2.5.8 | Target Size (Minimum) | İşaretçi hedefi ≥ **24×24 CSS px** (5 istisna: Spacing, Equivalent, Inline, User Agent Control, Essential) | **EVET** |
| 3.2.6 | Consistent Help | A | Hayır |
| 3.3.7 | Redundant Entry | A | Hayır |
| 3.3.8 | Accessible Authentication (Minimum) | AA | **EVET** |

### §3.3 Persona / Mood Eşlemesi (`[[personas/test-scenarios-mapping]]`)

| Persona grubu | n | A11Y odağı | Mood örnekleri | Beklenen |
|---|---|---|---|---|
| Kız çocuk | 17 | Büyük hedef (48px+), sade ARIA, veli onayı | Enerjik, Kaşif, Yaratıcı, Utangaç, Lider, Arabesk Kaşif, Dans Enerjik | Kısıtlı ama erişilebilir arayüz |
| Erkek çocuk | 12 | Büyük hedef, klavye tuzağı yok, veli onayı | Kaşif, Enerjik, Sporcu, Meraklı, Sessiz, Arabesk Meraklı, Dans Sporcu | Kısıtlı ama erişilebilir arayüz |
| Genç kız | 17 | Odak görünürlüğü, karanlık tema kontrastı | Enerjik, Romantik, Melankolik, Sosyal, Moody, Arabesksever, Danssever | Tam AA turu |
| Genç erkek | 17 (envanter 12) ⚠️ | Kısayol çakışması, gamer odak akışı | Sporcu, Romantik, Sosyal, Hip-Hop, Gamer, Arabesk Melankolik, Dans Enerjik | Tam AA turu |
| Yetişkin kadın | 5 | Form + otomatik doldurma (3.3.8), anne hesabı | Romantik, Enerjik, Melankolik, Profesyonel, Anne | Tam AA turu |
| Yetişkin erkek | 5 | Form + 2FA erişilebilirliği | Romantik, Enerjik, Melankolik, Profesyonel, Baba | Tam AA turu |

⚠️ **VERIFICATION REQUIRED:** grup persona sayıları `[[personas/test-scenarios-mapping]]` §1.3 ile `[[personas/mood-taxonomy]]` §2.2 arasında farklıdır (genç erkek 12 vs. mood dağılımında 12; kız çocuk yaş aralığı 4-11 vs. 6-11 çelişkisi mapping §1.3'te işaretlidir) → sayı **kaynaksız tek değer yazılmaz**.

### §3.4 Cihaz / Breakpoint Matrisi (research-bank P8.5 — `VERIFIED`)

| # | Viewport | Kullanım |
|---|----------|----------|
| 1 | 320 × 568 | En dar reflow + hedef boyutu kontrolü |
| 2 | 375 × 667 | Mobil temel |
| 3 | 375 × 812 | Mobil ana test viewport'u (mapping §5.1) |
| 4 | 414 × 896 | Büyük telefon |
| 5 | 768 × 1024 | Tablet (mapping §5.1 `1024×1366` ⚠️ — P8'de yok) |
| 6 | 1280 × 720 | Düşük çözünürlüklü masaüstü |
| 7 | 1920 × 1080 | Masaüstü tam (mapping §5.1) |

### §3.5 Emülasyon Ayarları (P8 — `VERIFIED`)

| Ayar | Değer |
|------|-------|
| Mobil laboratuvar throttling | Gecikme **150 ms** · indirme **1.6 Mbps** · yükleme **750 Kbps** · paket kaybı yok ("Slow 4G", eski "Fast 3G") |
| Seviye 3 cihaz emülasyonu | `playwright.devices` (Desktop Chrome, iPhone 11, Pixel 5, Galaxy S9+, iPad Pro …) |
| Viewport override | `page.setViewportSize()` |
| CDP ağ emülasyonu | `Network.emulateNetworkConditions` — `downloadThroughput: 500*1024/8`, `uploadThroughput` aynı, `latency: 400` |
| Çevrimdışı | `use: { offline: true }` |
| Geolocation emülasyon API'si | **`⚠️ VERIFICATION REQUIRED`** (P8'de yok — `[[personas/methodology]]` §3.3.4) |

---

## §4 Kurallar

### §4.0 ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

### §4.1 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Template Mandatory (Guardrail #16) | Bu dosya docs-md iskeletinden üretilir | Dosya geçersiz, revert |
| 2 | Şablon Önce | Yazmadan `.ai/.templates/` okunur | ERROR log, yazıma devam yok |
| 3 | SSOT | Eşik research-bank'te; seviye methodology'de; karar ADR-023'te — **burada yalnız adım** | Çakışan sayı silinir |
| 4 | Zero Hallucination (ADR-005) | Etiketsiz kriter/ölçü yazılmaz; verilmeyen eklenmez | İddia silinir + `log.md` ERROR |
| 5 | Kaynak Etiketleme | Her eşik research-bank durumuyla etiketlenir (§4.5) | Etiketsiz satır test edilemez |
| 6 | In-Place Refactoring | Dosya adı/yolu onaysız DEĞİŞTİRİLMEZ (`a11y-v2.md` üretilmez) | Dosya geri yüklenir |
| 7 | Frozen ADR 001-037 | Okunur, referans edilir; değiştirilmez | revert + log ERROR |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya `repair` ile onarılır |
| 10 | Domain boundary | Dosya QA Engineer (test) + MO (vault) alanıdır | Layer violation → revert |

### §4.2 Frontmatter — 7 Zorunlu Alan

| Alan | Zorunlu | Bu Dosyadaki Değer |
|------|---------|---------------------|
| `title` | ✅ | `"CoreMusic — Test Senaryosu: Erişilebilirlik (A11Y)"` |
| `type` | ✅ | `test-scenario` |
| `category` | ✅ | `testing` |
| `version` | ✅ | `1.0.0` |
| `status` | ✅ | `active` |
| `authority` | ✅ | `"Test Senaryosu — SSOT: personas/test-senaryolari/a11y-erisilebilirlik"` |
| `updated` | ✅ | `2026-09-26` |

### §4.3 Dil, Kod Adı ve Mojibake (UTF-8)

| Kural | Detay |
|-------|-------|
| Ana dil | Türkçe — ç ğ ı İ ö ş ü doğru yazılır |
| Dosya adı | ASCII: `a11y-erisilebilirlik.md` |
| Teknik terim | Gerektiği yerde İngilizce kalır (focus, viewport, throttling, landmark) |
| Mojibake yasak | `Ã-` dizileri ve U+FFFD yer tutucuları `verify` ile tespit edilir |
| Yazım aracı | `node .ai/scripts/vault-utf8-writer.mjs` (`write` / `append` / `insert-before-marker`) |
| PowerShell yazım yasağı | `Set-Content`, `Out-File`, `Add-Content`, `echo >` kullanılmaz (Windows-1254/BOM/UTF-16 bozar) |
| Doğrulama | Yazım sonrası `verify --file` → `mojibake: 0`, `hasBom: false`, `lines ≥ 500` |

### §4.4 Wiki-Link ve Bağlantı Formatı

| ✅ Doğru | ❌ Yanlış |
|----------|-----------|
| `[[personas/research-bank]]` | `[rb](research-bank.md)` |
| `[[personas/test-senaryolari/a11y-erisilebilirlik]]` | `[a](C:\www\coremusic.net\.ai\...a11y.md)` |
| `[[ADR-023-persona-driven-testing]]` | `https://iç-sistem/adr-023` |
| `[[.templates/index]]` | `[reg](../.templates/index.md)` |
| Harici URL düz metin (ör. `w3.org/TR/WCAG22/`) | `[[https://w3.org/...]]` (wiki-link yapılmaz) |

### §4.5 Kaynak Etiketleme — Research-Bank Durum Aktarımı (ADR-005)

| # | Banka Durumu | Bu Dosyadaki Karşılığı | Testte Uygulaması |
|---|--------------|------------------------|-------------------|
| 1 | `VERIFIED` | P5 AA kriter eşikleri · P8 metrik/throttling/breakpoint | Eşik doğrudan hedef yazılır → `Kaynak: research-bank P5/P8` |
| 2 | `SINGLE-SOURCE ⚠️` | Kullanılmıyor (bu senaryoda tek kaynaklı iddia yok) | Kullanılırsa `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| 3 | `CONFLICT ⚠️` | Persona grup sayıları (§3.3 uyarısı) | Çelişki gizlenmez; iki durum + `⚠️ VERIFICATION REQUIRED` |
| 4 | `DERIVED ⚠️` | Persona renklerinin kontrast **hesabı**; 16 yaş altı veli onayı (P6) | `⚠️ DERIVED` + dayanak satırı birlikte |
| 5 | `⚠️ VERIFICATION REQUIRED` | X-7 (1.4.5, 2.4.3, 3.3.x eksiksiz, 1.3.1, 4.1.2, 1.4.10, 2.3.3) · geolocation API | İddia yazılmaz ya da 1 kaynakla işaretli yazılır |

| ✅ Doğru | ❌ Yanlış (Hallüsinasyon) |
|----------|---------------------------|
| `1.4.3 metin kontrastı ≥ 4.5:1 (research-bank P5 — VERIFIED)` | `kontrast 5:1 yaptık` (kaynaksız) |
| `2.5.8 hedef ≥ 24×24 CSS px (P5 — VERIFIED)` | `hedef 44×44 şart` (bu, mapping §5.2 iç tercihidir → `⚠️ DERIVED`) |
| `1.4.10 reflow — ⚠️ VERIFICATION REQUIRED (X-7)` | `1.4.10 VERIFIED` (P5'te yok) |
| `16 yaş altı → veli onayı (P6 — ⚠️ DERIVED)` | `KVKK 16 diyor` (yanlış atıf) |

### §4.6 Derinlik Standardı (500+)

| Kural | Değer |
|-------|-------|
| Bu dosya | ≥ 500 satır (ham ölçüm — `vault-utf8-writer verify` → `lines`) |
| 6 senaryo dosyası | Hepsi ≥ 500 (eski `arabesk-dans` 201 ve `sosyal-paylasim` 361 satırdı → genişletildi) |
| İhlal | 500 altındaysa dosya tamamlanmış sayılmaz |

### §4.7 REDACTED, KVKK ve Sır Politikası

| Durum | Aksiyon |
|-------|---------|
| API key, token, parola, `.env` değeri | Test raporuna/vault'a yazılmaz; `[REDACTED]` |
| Gerçek kullanıcı verisi (ad, e-posta, sağlık) | Kurguya çevrilir; 6698 m.6 özel nitelikli veri → `Kaynak: kurgusal (persona verisi)` |
| 16 yaş altı persona (KVKK m.5/1 · P6) | Kurgusal olduğu belirtilir; **gerçek çocuk verisi asla** |
| Ekran okuyucu kaydı / ekran görüntüsünde kişisel veri | Maskelenir |

### §4.8 Yasak / Doğru Tablosu

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `WCAG 2.2 AAA uyumlu` | `WCAG 2.2 AA hedefi (AAA değil — P5.5)` |
| `Lighthouse a11y 100 şart` (sürüm/kaynak belirsiz) | `P5 kriter eşikleri + P8 metrikleri` ile ölç |
| `kaynak: internet` | `Kaynak: research-bank P5 (W3C Rec + Quick Ref)` |
| `2.5.8 = 44×44px` | `2.5.8 ≥ 24×24 CSS px (P5 — VERIFIED); 48/44 iç tercih → ⚠️ DERIVED` |
| Dosyayı `a11y-v2.md` yapmak | In-Place: aynı ad, genişletilmiş içerik |

---

## §5 Workflow

### §5.1 Genel Akış

```text
OKU (research-bank P5/P8 + methodology + mapping) → PREPARE (P1-P7)
  → EXECUTE (§5.3 — 20 adım, her adımda COLLECT)
  → REPORT (§5.5) → ADR-023 MATRİS (satır 15) → UTF-8 VERIFY → LOG APPEND
```

### §5.2 PREPARE — Hazırlık Adımları

| # | Adım | Kaynak | Çıktı |
|---|------|--------|-------|
| P1 | Persona dosyasını oku (11/11 alan) + A11Y / kısıt satırı | `[[personas/persona-template]]` §3.5 | Test girdisi |
| P2 | Grup + mood satırını al (§3.3) | `[[personas/test-scenarios-mapping]]` §4.4 | Odağı belirlenmiş senaryo |
| P3 | Breakpoint seç (§3.4) | research-bank P8.5 | Viewport W×H |
| P4 | Tema seç (light/dark) | persona Cihaz & Teknoloji | `colorScheme` değeri |
| P5 | Ağ koşulu: 150 ms · 1.6 Mbps / 750 Kbps | research-bank P8.3 | Throttling profili |
| P6 | Ekran okuyucu ortamı (NVDA / VoiceOver / TalkBack) | Senaryo bloğu A11Y-003…005 | Test ortamı |
| P7 | Temiz profil + temiz veri başlangıcı | methodology §3.1.1 | Deterministik koşu |

### §5.3 Adım Tablosu (20 adım — asgari 10)

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | 320×568 viewport'ta sayfayı yükle | Yatay kaydırma yok; içerik tek sütunda okunur | `page.setViewportSize` + screenshot | FCP ≤ **1 s** (P8 `VERIFIED`) | 1.4.10 ⚠️ VERIFICATION REQUIRED (X-7) |
| 2 | Lighthouse a11y audit çalıştır (Seviye 3) | Audit çıktısı üretilir; kırıklar listelenir | CI/yerel Lighthouse çıktısı | LCP ≤ **2500 ms** (P8 `VERIFIED`) | 1.4.3 · 2.5.8 (özet) |
| 3 | Normal metin kontrastını ölç (≥4.5:1) | Tüm gövde metni ≥ 4.5:1 | Kontrast ölçer + DOM renk örneği | — (P8 dışı; eşik P5) | **1.4.3** (`VERIFIED`) |
| 4 | Büyük metin kontrastını ölç (≥3:1) | H1-H3 ve ≥18.66px kalın metin ≥ 3:1 | Kontrast ölçer | — | **1.4.3** (`VERIFIED`) |
| 5 | UI bileşeni/grafik kontrastını ölç (≥3:1) | Buton kenarı, ikon, form çerçevesi ≥ 3:1 | Kontrast ölçer | — | **1.4.11** (`VERIFIED`) |
| 6 | Hover/focus ile beliren içeriği aç-kapat | İçerik kaybolmadan kapatılabilir, hover ile sürüklenmez | Manuel + DOM | — | **1.4.13** (`VERIFIED`) |
| 7 | Tab ile gezin: ilk odak görünür mü | Her odakta görünür işaret (outline) var | Klavye turu + screenshot | — | **2.4.7** (`VERIFIED`) |
| 8 | Sticky alt çubuk/overlay altında odak testi | Odak öğesi tamamen görünür kalır (örtbas yok) | 320×568 + klavye turu | — | **2.4.11** (`VERIFIED` — WCAG 2.2 YENİ) |
| 9 | Sürükle-bırak öğesini klavyeyle eşdeğer yap | Klavye alternatifi var (ok tuşları/buton) | Klavye ile taşıma | — | **2.5.7** (`VERIFIED` — WCAG 2.2 YENİ) |
| 10 | Tıklama hedefi boyutunu ölç | İşaretçi hedefi ≥ **24×24 CSS px** (5 istisna değerlendirilir) | DOM ölçüümü | — | **2.5.8** (`VERIFIED` — WCAG 2.2 YENİ) |
| 11 | Çocuk personasıyla hedef ölç (48px+ iç tercih) | Büyük butonlar; iç tercih `⚠️ DERIVED` olarak raporlanır | DOM ölçüümü + mapping §5.2 | — | **2.5.8** + `⚠️ DERIVED` |
| 12 | Yardım bloğunu 3 sayfada karşılaştır | Yardım yeri/türü tutarlı | 3 sayfa turu | — | **3.2.6** (`VERIFIED`, seviye A) |
| 13 | Formda aynı bilgiyi tekrar iste | İkinci isteğe gerek yok (otomatik taşıma) | Form turu | INP ≤ **200 ms** (P8 `VERIFIED`) | **3.3.7** (`VERIFIED`, seviye A) |
| 14 | Kimlik doğrulama adımını denetle (bilişsel test yok) | Zihinsel işlem/puzzle yok; metin/paste izinli | Auth akışı gözlemi | — | **3.3.8** (`VERIFIED` — WCAG 2.2 YENİ) |
| 15 | NVDA/VoiceOver/TalkBack ile form gönder | Etiket duyurulur; hata sesli bildirilir | Ekran okuyucu oturumu (A11Y-003…005) | — | 1.3.1 · 4.1.2 ⚠️ (X-7) |
| 16 | %200 metin büyütme + `prefers-reduced-motion` | İçerik kaybı yok; animasyon azaltılabilir | Tarayıcı ayarı + screenshot | CLS ≤ **0.1** (P8 `VERIFIED`) | 1.4.10 · 2.3.3 ⚠️ (X-7) |
| 17 | 16 yaş altı (çocuk) senaryosunda veli onayı | `veli_onayı_gerekli: true` akışı görünür; gerçek veri yok | Form + KVKK kontrolü | — | KVKK P6 — `⚠️ DERIVED` (TMK m.11=18, COPPA 13, GDPR 16) |
| 18 | Throttling (150 ms · 1.6/750) altında adımları tekrarla | Yavaş bağlantıda a11y geri bildirimi kaybolmaz | `Network.emulateNetworkConditions` + trace | TBT **> 50 ms** long task uyarısı (P8 `VERIFIED`) | 2.4.7 · 4.1.3 ⚠️ |
| 19 | Breakpoint turu (§3.4 — 7 viewport) | Her breakpoint'te odak/hedef/kontrast korunur | 7 × screenshot + DOM ölçümü | LCP ≤ **2500 ms** · CLS ≤ **0.1** | 1.4.11 · 2.5.8 |
| 20 | Sonuçları raporla + ADR-023 satır 15'e işaretle | Raporda her kriterin durumu + `⚠️` etiketleri var | §5.5 şablonu + `log.md` append | — | — |

### §5.4 Playwright / CDP Kod Bloğu (P8 — `VERIFIED` API adları)

```javascript
const { devices, chromium } = require('@playwright/test');

// Cihaz registry — research-bank P8.4 (VERIFIED)
const iphone11 = devices['iPhone 11'];
const context = await browser.newContext({
  ...iphone11,
  colorScheme: 'dark',            // devices alanları: userAgent, screenSize, viewport,
});                               // hasTouch, deviceScaleFactor, isMobile, colorScheme, locale, timezoneId

const page = await context.newPage();

// Viewport override — research-bank P8.4 (VERIFIED)
await page.setViewportSize({ width: 320, height: 568 });   // en dar breakpoint (P8.5)

// CDP ağ emülasyonu — research-bank P8.4 (VERIFIED): Network.emulateNetworkConditions
const cdp = await context.newCDPSession(page);
await cdp.send('Network.enable');
await cdp.send('Network.emulateNetworkConditions', {
  offline: false,
  latency: 150,                              // P8.3 mobil preset gecikme: 150 ms
  downloadThroughput: (1.6 * 1024 * 1024) / 8,   // 1.6 Mbps
  uploadThroughput: (750 * 1024) / 8,            // 750 Kbps
});

// Çevrimdışı — research-bank P8.4 (VERIFIED)
// use: { offline: true }
```

> ⚠️ **VERIFICATION REQUIRED:** CDP **geolocation** emülasyon methodunun adı research-bank P8'de yer almıyor → bu adımda API adı **uydurulmaz** (`[[personas/methodology]]` §3.3.4).

### §5.5 REPORT (Seviye 4)

| Çıktı | İçerik |
|-------|--------|
| Test raporu (markdown) | Ortam · viewport · tema · network · 20 adım tablosu · AA kriter durumları · öneriler |
| AA kriter tablosu | 10 satır (P5.2): kriter · eşik · sonuç · `⚠️` var mı |
| Ekran görüntüsü seti | Her adımda 1 (odak/kontrast kanıtı) |
| ADR-023 matris satırı | Satır 15 (Erişilebilirlik kişisi): birim H/E/B + entegrasyon H/E/B durumu |
| `log.md` | Append-only kayıt |

### §5.6 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| P5'te olmayan kriter numarası raporlanmış | §6.1 "kaynak etiketi" düşer | `⚠️ VERIFICATION REQUIRED` (X-7) olarak işaretle |
| Kontrast sonucu "doğrulanmış" sunuldu | `⚠️ DERIVED` etiketi yok | Eşik VERIFIED, hesap DERIVED — ikisini ayır |
| Çocuk senaryosunda veli adımı atlandı | §3.3 + P6 satırı işlenmemiş | Adım 17'yi ekle; KVKK notunu yaz |
| Konsol hatası raporlanmadı | Konsol 0 değilken "geçti" | Adım `error` işaretle; tam metni rapora yaz |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Dosya 500 satır altında | `verify.lines < 500` | §3/§5 tablolarını genişlet; `write` ile yenile |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] `type: test-scenario` · `category: testing` · `version: 1.0.0` · `status: active` · `updated: 2026-09-26`
- [ ] `authority` = `"Test Senaryosu — SSOT: personas/test-senaryolari/a11y-erisilebilirlik"`
- [ ] Tek H1 var (`# `) + Zorunlu Bağlantılar satırı (≥ 7 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz
- [ ] §4.0 Şablon-Önce bloğu birebir mevcut ve §4'ün ilk maddesi
- [ ] §5.3 adım tablosu **≥ 10 adım** ve **6 sütunlu** (Adım · Eylem · Beklenen · Doğrulama · Metrik · WCAG)
- [ ] Her AA kriteri (1.4.3 · 1.4.11 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 · 3.2.6 · 3.3.7 · 3.3.8) için **ayrı satır** var
- [ ] Metrikler research-bank P8 ile birebir (LCP 2500 · INP 200 · CLS 0.1 · FCP 1s · TBT 50ms · 150ms/1.6/750)
- [ ] Breakpoint listesi P8.5 ile uyumlu (320×568 · 375×667 · 375×812 · 414×896 · 768×1024 · 1280×720 · 1920×1080)
- [ ] Çocuk senaryosu için KVKK/veli onayı adımı var (`⚠️ DERIVED` etiketiyle)
- [ ] Playwright/CDP bloğunda yalnız P8'de `VERIFIED` API adları geçiyor; geolocation API `⚠️ VERIFICATION REQUIRED`
- [ ] Her gerçek-dünya satırında research-bank durum etiketi var (§4.5)
- [ ] §2.1 Kapsam/Kapsam Dışı ≥ 3 satır; §5.6 hata modları var
- [ ] §7.1 wiki-link tablosu ≥ 7 hedef; §7.2 Değişiklik Geçmişi append-only
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false); dosya derinliği **≥ 500 satır**
- [ ] Verilmeyen hiçbir sayı eklenmedi; REDACTED/KVKK ihlali yok; başka `.personas` dosyasına yazılmadı

### §6.2 AA Kriter Durum Tablosu (boş — test sonunda doldurulur)

| Kriter | Eşik (P5) | Sonuç | Durum |
|--------|-----------|-------|-------|
| 1.4.3 | 4.5:1 / 3:1 | — | ⏳ |
| 1.4.11 | 3:1 | — | ⏳ |
| 1.4.13 | AA | — | ⏳ |
| 2.4.7 | AA | — | ⏳ |
| 2.4.11 | AA (yeni) | — | ⏳ |
| 2.5.7 | AA (yeni) | — | ⏳ |
| 2.5.8 | 24×24 px (yeni) | — | ⏳ |
| 3.2.6 | A | — | ⏳ |
| 3.3.7 | A | — | ⏳ |
| 3.3.8 | AA (yeni) | — | ⏳ |

### §6.3 EXCLUDED Listesine Atıf (research-bank §6.4)

| # | İddia | Bu Senaryoda Ne Yapılır |
|---|-------|--------------------------|
| X-7 | WCAG AA kriterlerinin tamamı (1.4.5, 2.4.3, 3.3.x eksiksiz) | Yalnız §3.2 listesi gerekçe sayılır; fazlası `⚠️ VERIFICATION REQUIRED` |
| X-10 | Persona'ya özel a11y hedefi (ör. %95 skor) | Yalnız `⚠️ DERIVED` + P8 dayanağı |
| — | AAA kriterleri | CoreMusic hedefi **değil** → "AAA uyumlu" yazılmaz (P5.5) |

### §6.4 Quality Report (Bu Dosyanın Kendisi)

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter (7 alan) |
| Test bloğu | 14 (A11Y-001 … A11Y-014) |
| Adım | 20 (asgari 10) |
| AA kriter satırı | 10/10 (P5.2) |
| Zorunlu blok | 1 (§4.0 Şablon-Önce — silinemez) |
| `⚠️ VERIFICATION REQUIRED` | 8 (§6.5) |
| `⚠️ DERIVED` | 3 (§6.5) |
| Wiki-link (§7.1) | 8 hedef |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

### §6.5 Bu Dosyadaki `⚠️` Etiketleri (Toplam)

| # | Etiket | Konu |
|---|--------|------|
| 1 | `⚠️ VERIFICATION REQUIRED` | 1.4.10 reflow (X-7) |
| 2 | `⚠️ VERIFICATION REQUIRED` | 2.3.3 hareket azaltma (X-7) |
| 3 | `⚠️ VERIFICATION REQUIRED` | 1.3.1 / 4.1.2 ekran okuyucu kriter numaraları (X-7) |
| 4 | `⚠️ VERIFICATION REQUIRED` | 4.1.3 durum mesajı (adım 18) |
| 5 | `⚠️ VERIFICATION REQUIRED` | CDP geolocation emülasyon API adı (P8'de yok) |
| 6 | `⚠️ VERIFICATION REQUIRED` | Tablet `1024×1366` breakpoint (P8.5'te yok) |
| 7 | `⚠️ VERIFICATION REQUIRED` | Persona grup sayıları çelişkisi (§3.3) |
| 8 | `⚠️ VERIFICATION REQUIRED` | Çocuk yaş aralığı 4-11 vs 6-11 (mapping §1.3) |
| 9 | `⚠️ DERIVED` | Kontrast **hesabı** (eşik VERIFIED, persona renkleri hesaplanır) |
| 10 | `⚠️ DERIVED` | 48px/44px iç hedef boyutu (2.5.8 resmi eşiği 24×24) |
| 11 | `⚠️ DERIVED` | 16 yaş altı veli onayı (P6 — TMK m.11=18 · COPPA 13 · GDPR 16) |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | 68 persona / 6 grup kataloğu — senaryonun girdisi | ✅ `.ai/.personas/index.md` |
| `[[personas/methodology]]` | 4 test seviyesi + PREPARE→EXECUTE→REPORT (§5 akışı kaynağı) | ✅ `.ai/.personas/methodology.md` |
| `[[personas/research-bank]]` | P5 (WCAG) + P8 (metrik/throttling/breakpoint) eşikleri | ✅ `.ai/.personas/research-bank.md` |
| `[[personas/test-scenarios-mapping]]` | Senaryo × grup × mood eşlemesi (§3.3 kaynağı) | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/persona-template]]` | 11 alan + Test Adımları (§5.2 PREPARE kaynağı) | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[ADR-023-persona-driven-testing]]` | 20 persona matrisi **satır 15** + %90 coverage gate | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |
| `[[personas/test-senaryolari/a11y-erisilebilirlik]]` | Bu dosyanın kendisi (kalıcı bağlantı) | ✅ `.ai/.personas/test-senaryolari/a11y-erisilebilirlik.md` |
| `[[personas/mood-taxonomy]]` | 25 mood kümesi (§3.3 mood örnekleri kaynağı) | ✅ `.ai/.personas/mood-taxonomy.md` |

### §7.2 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — eski vault `a11y-erisilebilirlik.md` (785 satır, salt okunur) iskeleti alınıp yeniden yazıldı: 10 AA kriteri için ayrı satır (P5), 20 adımlık 6 sütunlu adım tablosu, P8 metrik/throttling/breakpoint bağlaması, KVKK veli onayı adımı (`⚠️ DERIVED`), Playwright/CDP bloğu, 8 `⚠️ VERIFICATION REQUIRED` + 3 `⚠️ DERIVED` envanteri | test-scenario-agent (vault-updater) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Test Senaryosu — SSOT: personas/test-senaryolari/a11y-erisilebilirlik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
