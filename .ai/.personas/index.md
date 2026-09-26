---
title: "CoreMusic — Persona Kataloğu (68 Persona · 6 Grup)"
type: persona-index
category: personas
version: 1.0.0
status: active
authority: reference
updated: 2026-09-26
---

# CoreMusic — Persona Kataloğu (68 Persona · 6 Grup)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[.templates/index]]

---

## §1 Amaç

Bu dosya, CoreMusic platformunun **persona envanterinin kök kataloğudur**: 6 grup / 68 kurgusal kullanıcının dosya yollarını, isimlerini, yaşlarını ve mood tiplerini tek tabloda toplar, dizin yapısını ve persona üretim kurallarını tanımlar. Persona, hedef kullanıcının **kurgusal ama kaynak-destekli** profilidir — AI o kişi gibi davranarak Level 1 rol testi yapar, o kişinin cihaz/tema/internet koşullarında Level 2/3 testini yürütür (şablon: [[.templates/personas/persona-template]]). Bu katalog **ADR-023 şart 1c** kapsamında var olmuştur: debate 3/20'de "eski persona vault'ta yok (dead reference)" itirazı → "` .ai/.personas/` taşıma" şartı olarak bağlayıcı hâle gelmiştir (kaynak: [[../.decisions/accepted/ADR-023-persona-driven-testing]] §5.4).

| Alan | Değer |
|------|-------|
| Doküman tipi | Persona kataloğu (kök index — SSOT değil, `authority: reference`) |
| Persona sayısı | **68** (eski vault disk sayımı ile birebir; §6 tam listesi) |
| Grup sayısı | **6** (kız çocuk · genç kız · erkek çocuk · genç erkek · yetişkin kadın · yetişkin erkek) |
| Hedef kitle | QA Engineer (test ataması), UI Designer (erişilebilirlik/temsil), Master Orchestrator (vault senkronu) |
| Tetikleyici olay | ADR-023 debate şartı 1c (2026-09-25 — 18/2/0 KABUL) + `[[../index]]` §12'de kırık `personas/*` linkleri |
| Vault bağlantısı | Şablon → [[.templates/personas/persona-template]] · Mood → [[mood-taxonomy]] · Eşleme → [[test-scenarios-mapping]] |
| Karar kaynağı | [[../.decisions/accepted/ADR-023-persona-driven-testing]] (frozen YOK — active, Arch Lead ⏳) |
| Sayım kaynağı | Eski vault `.ai/personas/` disk listesi (salt-okunur; 2026-09-26 sayımı) |
| Kopyalama durumu | Persona içerikleri (735 satırlık detaylar) **KOPYALANMADI** — bu dosya katalog/iskelettir |

### §1.1 Bu Dosya Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Dosya | Bu Dosya Kullanılmaz |
|---------|-------------|----------------------|
| 68 persona'nın nerede olduğu, hangi grupta olduğu | ✅ Bu dosya (`[[index]]`) | — |
| Mood küme adı, Big Five eğilimi, müzik eşlemesi | [[mood-taxonomy]] | ❌ |
| Persona × test senaryosu eşlemesi, cihaz/WCAG matrisi | [[test-scenarios-mapping]] | ❌ |
| Gerçek-dünya araştırması (nüfus, cihaz, WCAG, KVKK) | [[research-bank]] (⚠️ başka ajan yazıyor) | ❌ |
| Test seviyeleri (Level 1/2/3) ve başarı metrikleri | `personas/methodology` 📋 planlanan | ❌ |
| Tek persona profili üretimi | [[../.templates/personas/persona-template]] | ❌ |
| 20 satırlık bağlayıcı test matrisi (karar metni) | [[../.decisions/accepted/ADR-023-persona-driven-testing]] §2.2a | ❌ |

**Ayırıcı test:** "Dosya bir kişiyi mi listeliyor?" → Evet ise bu dosya. "Bir mood'u mu tanımlıyor?" → `mood-taxonomy`. "Bir kararı mı kaydediyor?" → ADR.

### §1.2 Neden Şimdi (2026-09-26)

1. **Şart 1c açık:** ADR-023 §5.4 şart 1c — "eski vault persona envanteri `.ai/.personas/` altına taşınır (eski disk korunur; dosya adı değişmez)" → 3 şart kapanmadan karar Active/Frozen olmaz.
2. **`[[../index]]` §12 kırık linkleri:** `Personas | [[personas/index]], [[personas/methodology]], [[personas/mood-taxonomy]]` satırındaki linkler vault'ta hedefsiz (2026-09-06 güncellik notu).
3. **Şablon kaydı #37:** `[[.templates/personas/persona-template]]` 2026-09-26'da registry'ye girdi (`[[.templates/index]]` §7.1.14) — katalog olmadan şablonun hedefi boşta kalır.
4. **ADR-023 §1.1-C envanteri:** eski vault 80 md (68 persona + 4 kök + 6 senaryo + 2 rapor şablonu) → bu vault'a yalnız **özet/kaıt** taşındı, dosya kopyalanmadı (AIU).

---

## §2 Kapsam

### §2.1 Grup Dağılımı (6 grup — eski `personas/index.md` dağılımı korunarak doğrulandı)

| Grup | Yaş | Kişi Sayısı | Mood Tipleri |
|------|-----|------------|-------------|
| **Kız Çocuk** (`kiz-cocuk/`) | 4-11 | **17** | Kaşif, Enerjik, Yaratıcı, Utangaç, Lider, **Arabesk Kaşif**, **Dans Enerjik** |
| **Genç Kız** (`genc-kiz/`) | 12-17 | **17** | Romantik, Enerjik, Melankolik, Moody, Sosyal, **Arabesksever**, **Danssever** |
| **Erkek Çocuk** (`erkek-cocuk/`) | 4-11 | **12** | Kaşif, Enerjik, Sporcu, Meraklı, Sessiz, **Arabesk Meraklı**, **Dans Sporcu** |
| **Genç Erkek** (`genc-erkek/`) | 12-17 | **12** | Hip-Hop, Gamer, Sporcu, Romantik, Sosyal, **Arabesk Melankolik**, **Dans Enerjik** |
| **Yetişkin Kadın** (`yetiskin-kadin/`) | 25-45 | **5** | Profesyonel, Romantik, Enerjik, Melankolik, Anne |
| **Yetişkin Erkek** (`yetiskin-erkek/`) | 25-45 | **5** | Profesyonel, Romantik, Sporcu, Melankolik, Baba |
| **TOPLAM** | — | **68** | — |

**Doğrulama (2026-09-26, eski vault disk listesi):** `kiz-cocuk` **17** · `genc-kiz` **17** · `erkek-cocuk` **12** · `genc-erkek` **12** · `yetiskin-kadin` **5** · `yetiskin-erkek` **5** → **17+17+12+12+5+5 = 68** ✅ (tam liste §6).

> ⚠️ **VERIFICATION REQUIRED — yaş aralığı çelişkisi:** Eski `index.md` ve ADR-023 grupları **4-11** yazar; eski `test-scenarios-mapping.md` §2A/§2B başlıkları **6-11** yazar. Bu katalog **4-11** alır (ADR-023 §2.2a satır 9/11 ile aynı); 6-11 ifadesi eski eşleme dosyasında kalmıştır. Çözüm: [[research-bank]] + Vault Steward onayı. → `[[personas/research-bank]]` (başka ajan yazıyor — bu dosyaya DOKUNMA).

### §2.2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 68 persona'nın dosya yolu / isim / yaş / mood kataloğu (§6) | Persona içeriği (kimlik, Big Five, test adımları — persona-template ile üretilir) |
| 6 grup dağılımı, grup yaş/mood tablosu (§2.1) | Mood tanımları ve müzik eşlemeleri (→ [[mood-taxonomy]]) |
| Dizin ağacı ve dosya rolleri (§3) | Persona × senaryo × cihaz matrisi (→ [[test-scenarios-mapping]]) |
| Üretim/taşıma kuralları, Guardrail #16, kaynak etiketleme (§4) | Test seviyeleri ve metrikleri (→ `personas/methodology` 📋) |
| Doğrulama listesi ve 68 satırlık tam sayım (§6) | Gerçek-dünya araştırması (→ [[research-bank]] ⚠️) |
| ADR-023 şart 1c kapsamı (taşınan envanter özeti) | ADR-023 karar metni (→ ADR dosyası; frozen değil, aktif karar) |

*Alt konular:* amaç → grup dağılımı → dizin ağacı → kurallar → üretim akışı → doğrulama/tam liste → referanslar.
Kapsam dışı için: mood → [[mood-taxonomy]] · eşleme → [[test-scenarios-mapping]] · araştırma → [[research-bank]] · persona şablonu → [[../.templates/personas/persona-template]].

### §2.3 Hedef Kitle

| Kitle | Bu Dosyayı Nasıl Kullanır |
|-------|---------------------------|
| QA Engineer | §6 tablosundan persona seçer → `@group persona-NN` (ADR-023 §5.1/3) |
| UI Designer | Grup yaş/mood dağılımına göre temsil, dokunma hedefi ve kontrast kararları |
| Master Orchestrator / vault-updater | Kırık `personas/*` linklerini bu dizine bağlar; registry senkronu |
| Test otomasyonu (Playwright) | Dosya yolundan persona dosyasını okur, cihaz/viewport satırını script'e besler |
| Yeni ekip üyesi | Bu dosyayı okuyarak persona sisteminin haritasını öğrenir |

### §2.4 Kapsam Dışı İstisnalar

| Durum | Ne Yapılır |
|-------|-----------|
| Dosya hem katalog hem persona profili taşıyor | DUR → Contradiction Gate (MO); profil `personas/<grup>/<ad>.md`'ye ayrılır |
| Eski vault'taki 735 satırlık persona detayı | Kopyalanmaz — In-Place yasağı + AIU; salt-okunur kaynak olarak okunur |
| Gerçek kişi verisi tespit edilirse | DUR → KVKK (§4.5); kurguya çevrilir veya `[REDACTED]` |
| Yeni persona grubu/kişi eklenmek istenirse | ADR-023 §2.2a: **yeni persona = yeni ADR** (ADR-088+) — bu dosya tek başına genişletilemez |
| `research-bank.md` içeriği | Başka ajanın alanı — bu oturumda **yazılmaz/dokunulmaz** |

---

## §3 Mimari

### §3.1 Dizin Ağacı (hedef yapı — `.ai/.personas/`)

```text
.ai/.personas/
├── index.md                     → bu dosya — 68 persona kataloğu (kök)
├── methodology.md               → 📋 planlanan — Level 1/2/3 test seviyeleri (ADR-023 referanslı)
├── mood-taxonomy.md             → ✅ bu oturumda yazıldı — mood küme sınıflandırması
├── test-scenarios-mapping.md    → ✅ bu oturumda yazıldı — persona × senaryo eşlemesi
├── research-bank.md             → 🔄 BAŞKA AJAN YAZIYOR — gerçek-dünya araştırma bankası
├── test-senaryolari/            → 📋 planlanan — 6 test senaryosu dosyası (farklı adım)
│   ├── a11y-erisilebilirlik.md
│   ├── arabesk-dans-mood-gecis.md
│   ├── browser-navigasyon.md
│   ├── muzik-kesfi.md
│   ├── playlist-olusturma.md
│   └── sosyal-paylasim.md
├── kiz-cocuk/                   → 17 persona (4-11)
├── genc-kiz/                    → 17 persona (12-17)
├── erkek-cocuk/                 → 12 persona (4-11)
├── genc-erkek/                  → 12 persona (12-17)
├── yetiskin-kadin/              → 5 persona (25-45)
└── yetiskin-erkek/              → 5 persona (25-45)
```

### §3.2 Dosya Roller Tablosu

| Dosya | Tip | Rol | Durum (2026-09-26) | Yazar |
|-------|-----|-----|--------------------|-------|
| `index.md` | `persona-index` | Kök katalog — 68 kişi, 6 grup, dizin ağacı | ✅ bu dosya | Vault Steward (subagent) |
| `mood-taxonomy.md` | `reference` | 18 mood kümesinin tam sınıflandırması | ✅ bu oturumda üretildi | Vault Steward (subagent) |
| `test-scenarios-mapping.md` | `reference` | 20 persona test matrisi + 6 senaryo eşlemesi | ✅ bu oturumda üretildi | Vault Steward (subagent) |
| `research-bank.md` | `reference` | Gerçek-dünya kaynak bankası (nüfus/cihaz/WCAG/KVKK) | 🔄 başka ajan yazıyor | **DOKUNULMAZ** |
| `methodology.md` | `guide` | Level 1/2/3 test seviyeleri, başarı metrikleri | 📋 planlanan | QA Engineer |
| `<grup>/<ad-s-soyad>-<mood>.md` | `persona` | Tek persona profili (11 alan havuzu, ≥500 satır) | 📋 planlanan (68 dosya) | QA Engineer + UX Researcher |
| `test-senaryolari/*.md` | `reference` | 6 E2E test senaryosu (eski vault'tan taşınacak) | 📋 planlanan (6 dosya) | QA Engineer |

### §3.3 Bağımlılık Grafiği

```text
[[.templates/personas/persona-template]] (şablon #37)
        │  üretir
        ▼
  <grup>/<persona>.md ──── mood adı alır ───► [[mood-taxonomy]]
        │                                        │
        │ senaryo atar                          │ test etkisi
        ▼                                        ▼
[[test-scenarios-mapping]] ◄── bağlar ── [[../.decisions/accepted/ADR-023-persona-driven-testing]]
        │                                   (§2.2a 20 satır · şart 1c)
        ▼
  test-senaryolari/*.md (6) ── kod'a taşınır ──► shared/tests · auth tests (@group persona-NN)
        │
        └── gerçek-dünya iddiaları ──► [[research-bank]] (⚠️ VERIFICATION REQUIRED pointer'ı)
```

| Bağımlılık | Yön | Not |
|------------|-----|-----|
| persona-template → bu katalog | şablon §7.1'de `[[personas/index]]` 📋 planlanan satırı | hedef artık `.ai/.personas/index.md` |
| ADR-023 şart 1c → bu dizin | karar → uygulama | eski disk korunur, kopya yok (yalnız özet) |
| mood-taxonomy → persona dosyaları | küme adı zorunlu kaynağı | uydurma küme adı yazılmaz (şablon §3.5.4) |
| test-scenarios-mapping → ADR-023 §2.2a | 20 satır birebir eşleşir | §6'da çapraz kontrol |
| research-bank → her 3 kök dosya | `⚠️ VERIFICATION REQUIRED` pointer'ı | yazılmadan önce link `📋` kalır |

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
| Şablon okundu ama çelişiyor | DUR → `[[CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

*Bu dosya `[[.templates/documentation/docs-md-template]]` (8-bölüm iskelet) + `[[.templates/personas/persona-template]]` (persona domaini) okunarak üretilmiştir.*

### §4.1 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | **Guardrail #16 — Template Mandatory** | Her `.md` ilgili şablondan üretilir (`docs-md` + `personas/persona-template`) | Dosya geçersiz, revert |
| 2 | **Şablon Önce** | Yazmadan `.ai/.templates/` okunur | ERROR log, yazıma devam yok |
| 3 | **Kaynak etiketleme (ADR-005)** | Her gerçek-dünya iddiası etiketli: `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` | Etiketsiz iddia silinir |
| 4 | **SSOT** | Bu dosya `authority: reference` — persona bilgisi persona dosyalarındadır; katalog yalnız indeksler | SSOT self-claim kaldırılır |
| 5 | **In-Place Refactoring** | Dosya adı/yolu değişmez; 68 persona adı eskisiyle korunur (ADR-023 şart 1c) | Dosya geri yüklenir |
| 6 | **Derinlik 500+** | Bu katalog dahil kök persona dokümanları ≥ 500 satır (`vault-utf8-writer verify` → `lines`) | Dosya tamamlanmış sayılmaz |
| 7 | **Dil / mojibake** | Türkçe (ç ğ ı İ ö ş ü); `Ã-` dizileri ve U+FFFD yasak; tek yazma arayüzü `vault-utf8-writer` | `repair` ile onarılır |
| 8 | **REDACTED / KVKK** | Secret, gerçek kişi verisi asla yazılmaz; 18 altı persona kurgusaldır | Sızıntı sayılır |
| 9 | **Append-only log** | Tüm değişiklikler `[[../log.md]]`'ye eklenir | Geçmiş satır değişmez |
| 10 | **Persona şişirme yasağı** | Matris 20 satırda sabit; yeni persona = yeni ADR (ADR-088+) | Yeni satır silinir, karar reddedilir |
| 11 | **Domain boundary** | Persona dosyaları QA Engineer; katalog/registry MO (vault-updater) | Layer violation → revert |
| 12 | **Dizin mülkiyeti** | `research-bank.md` başka ajana ait — bu oturumda dokunulmaz | Eşzamanlı yazım → context lock |

### §4.2 Yasak / Doğru Tablosu

| ✅ Yasak | ✅ Doğru |
|---------|---------|
| 68 persona dosyasının içeriğini bu kataloğa kopyalamak | §6'da dosya yolu / isim / yaş / mood satırı taşımak |
| Eski vault'a yazmak (`coremusic.net.old/.ai/personas/`) | Salt-okunur okuma; tüm üretim `.ai/.personas/` altında |
| `personas/index.md` (noktasız) oluşturmak | `.ai/.personas/index.md` (ADR-023 şart 1c yolu) |
| Kaynaksız gerçek-dünya iddiası (nüfus, cihaz, WCAG %) | `⚠️ VERIFICATION REQUIRED` + `[[research-bank]]` pointer'ı |
| `Set-Content` / `Out-File` / `echo >` ile yazım | `node .ai/scripts/vault-utf8-writer.mjs write/append` |
| Dosya adı değişikliği (`-v2.md`, `(1).md`) | In-Place — ad sabit, içerik düzeltilir |
| `research-bank.md`'yi bu oturumda yazmak | `📋 BAŞKA AJAN` işareti + geri dönüş raporunda bildirim |

### §4.3 Wiki-Link ve Bağlantı Formatı

| ✅ Doğru | ❌ Yanlış |
|----------|-----------|
| `[[mood-taxonomy]]` (aynı dizin kardeşi) | `[mood](mood-taxonomy.md)` |
| `[[../.templates/personas/persona-template]]` | `[şablon](C:\www\coremusic.net\.ai\...)` |
| `[[../.decisions/accepted/ADR-023-persona-driven-testing]]` | `https://iç-sistem/adr-023` |
| Harici URL düz metin | `https://...` (wiki-link yapılmaz) |
| `[[CLAUDE.md]]` (Zorunlu Bağlantılar satırı biçimi) | Mutlak Windows yolu |

**Biçim notu:** kök dokümanların "Zorunlu Bağlantılar" satırı `.ai/` kök adlarıyla yazılır (`[[CLAUDE.md]]`, `[[.templates/index]]`); kardeş dosyalara göreli wiki-link kullanılır. Persona şablonundaki kısaltma `[[personas/...]]` bu vault'ta **`.ai/.personas/`** dizinine karşılık gelir (§7.1 durum sütunu).

### §4.4 Derinlik Standardı (500+)

| Kural | Değer |
|-------|-------|
| Kök persona dokümanı (`index`, `mood-taxonomy`, `test-scenarios-mapping`) | ≥ 500 satır (ham ölçüm — `verify` → `lines`) |
| Üretilen persona dosyası | ≥ 500 satır (persona-template §4.6 — istisnası yok) |
| Kapsam dışı | `session-log-template.md` (144 satır) |
| İhlal | 500 altındaysa dosya tamamlanmış sayılmaz; §5.2'ye göre derinleştirilir |

### §4.5 REDACTED, KVKK ve Kaynak Etiketi

| Durum | Aksiyon |
|-------|---------|
| API key, token, parola | Vault'a asla yazılmaz; `[REDACTED]` |
| Gerçek kullanıcı verisi (ad, e-posta, telefon) | Persona dosyasına yazılmaz; kurguya çevrilir |
| 18 yaş altı persona (29 çocuk + 29 teen) | Kurgusal olduğu açıkça yazılır; gerçek çocuk verisi asla |
| Gerçek-dünya iddiası (nüfus, cihaz spec, WCAG kriteri, KVKK/COPPA) | ≥2 bağımsız kaynak **veya** `⚠️ VERIFICATION REQUIRED` + `[[research-bank]]` |
| Sanatçı / BPM değeri | 2 kaynak (diskografi + müzik veritabanı); yoksa `⚠️` |
| Log'a sızan secret | `[[../log.md]]` girişinde `[REDACTED]` |

---

## §5 Workflow

### §5.1 Persona Üretim / Taşıma Adımları (şart 1c akışı)

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | Bu kataloğu + `[[.templates/personas/persona-template]]` + `[[mood-taxonomy]]` oku | Küme adı + 11 alan havuzu | 5 dk |
| 2 | Hedef grup ve mood'u §6 tablosundan seç (persona adı **değiştirilmez**) | Seçilen dosya yolu | 2 dk |
| 3 | Eski vault persona dosyasını **salt-okunur** incele (içerik kaynağı) | Alan verisi | 10 dk |
| 4 | `persona-template`'i kopyala → `{{VARIABLE}}` doldur → 11/11 alan | Taslak persona | 30 dk |
| 5 | Her satıra kaynak etiketi yaz (§4.5); gerçek-dünya iddiası → `[[research-bank]]` | Kaynaklı dosya | 10 dk |
| 6 | Test Adımları tablosu ≥ 10 satır (Adım/Beklenen/Doğrulama) | Test planı | 10 dk |
| 7 | `vault-utf8-writer verify --file` → `lines ≥ 500`, mojibake 0 | UTF-8 raporu | <1 dk |
| 8 | `[[test-scenarios-mapping]]` §6'da çapraz kontrol + `log.md` append | Audit trail | 3 dk |

```text
KATALOG OKU → GRUP/MOOD SEÇ → ESKİ DOSYAYI SALT-OKUNUR OKU → ŞABLONLA ÜRET → 11/11 ALAN → KAYNAK ETİKETLE → VERIFY(≥500) → EŞLEME ÇAPRAZ KONTROL → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Şablon okunmadan yazım | §6.1'de 3+ madde düşer | Şablondan yeniden üret (Guardrail #16) |
| Sayım uyuşmazlığı | §6 toplam ≠ 68 | Eski disk listesiyle karşılaştır → `log.md` + geri dönüş raporu |
| Mood adı uydurma | `mood-taxonomy`'de eşleşmiyor | Küme adını taksonomiden al; yoksa `⚠️ VERIFICATION REQUIRED` |
| Kırık wiki-link | Hedef diskte yok | §7.1'de `📋 planlanan` işaretle + `log.md` |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| `research-bank.md`'ye dokunma | Eşzamanlı yazım riski | DUR → handover (AGENTS.md §9) — bu oturumda yasak |
| Yaş aralığı çelişkisi (4-11 vs 6-11) | §2.1 uyarısı | `[[research-bank]]` + Vault Steward onayı |

### §5.3 Bitiş Koşulları

- [ ] 3 kök dosya hedef yolda (`.ai/.personas/`), adı ASCII, `.md` uzantılı
- [ ] `vault-utf8-writer verify` temiz (BOM yok, mojibake 0, `lines ≥ 500`)
- [ ] `research-bank.md` bu oturumda oluşturulmadı/silinmedi
- [ ] `log.md` append edildi
- [ ] Wiki-link'ler geçerli ya da §7.1'de durum sütunuyla işaretli

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] `type: persona-index`, `category: personas`, `version: 1.0.0`, `status: active`, `updated: 2026-09-26`
- [ ] H1 + Zorunlu Bağlantılar satırı (`[[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[.templates/index]]`)
- [ ] §1-§7 başlıkları eksiksiz (docs-md iskeleti)
- [ ] §4.0 Şablon-Önce bloğu birebir mevcut ve §4'ün ilk maddesi
- [ ] 6 grup tablosu kişi sayılarıyla **17/17/12/12/5/5 = 68**
- [ ] §6.3 tam liste **68 satır** + grup başına alt başlıklar
- [ ] Her gerçek-dünya iddiası `⚠️ VERIFICATION REQUIRED` etiketli ve `[[research-bank]]` pointer'ı var
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] Wiki-link'ler `[[...]]` biçiminde; hedefi olmayanlar §7.1'de `📋` işaretli
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] `research-bank.md`'ye dokunulmadı (bu oturum)
- [ ] §7.2 Değişiklik Geçmişi append-only tablo var + Authority footer

### §6.2 Sayım Metriği

| Metrik | Beklenen | Ölçüm |
|--------|----------|-------|
| Grup sayısı | 6 | §2.1 tablo satırı |
| Persona toplamı | **68** | §6.3 satır sayısı (grup başlıkları hariç) |
| Kök doküman | 4 + research-bank | `index` · `methodology` 📋 · `mood-taxonomy` · `test-scenarios-mapping` · `research-bank` 🔄 |
| Test senaryosu dosyası | 6 | `test-senaryolari/` (📋 planlanan) |
| Dosya derinliği | ≥ 500 satır | `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/.personas/index.md` |

### §6.3 68 Persona — Tam Liste (eski vault disk adları)

> Kaynak: eski vault `.ai/personas/<grup>/*.md` disk listesi (salt-okunur, 2026-09-26 sayımı). Yaş/mood sütunları eski `test-scenarios-mapping.md` §2 tablolarından; dosya adları disk gerçeğidir. Persona içeriği kopyalanmamıştır (§2.4).

#### §6.3.1 Kız Çocuk — `kiz-cocuk/` (17 · 4-11)

| # | Dosya yolu | İsim | Yaş | Mood |
|---|-----------|------|-----|------|
| 1 | `.ai/.personas/kiz-cocuk/zeynep-yilmaz.md` | Zeynep Yılmaz | 7 | Enerjik |
| 2 | `.ai/.personas/kiz-cocuk/elif-kaya.md` | Elif Kaya | 9 | Kaşif |
| 3 | `.ai/.personas/kiz-cocuk/defne-demir.md` | Defne Demir | 10 | Yaratıcı |
| 4 | `.ai/.personas/kiz-cocuk/asya-aydin.md` | Asya Aydın | 8 | Utangaç |
| 5 | `.ai/.personas/kiz-cocuk/ada-celik.md` | Ada Çelik | 6 | Lider |
| 6 | `.ai/.personas/kiz-cocuk/duru-arslan.md` | Duru Arslan | 11 | Enerjik |
| 7 | `.ai/.personas/kiz-cocuk/gokce-karaca.md` | Gökçe Karaca | 9 | Kaşif |
| 8 | `.ai/.personas/kiz-cocuk/ilayda-erdem.md` | İlayda Erdem | 7 | Enerjik |
| 9 | `.ai/.personas/kiz-cocuk/ipek-koc.md` | İpek Koç | 10 | Yaratıcı |
| 10 | `.ai/.personas/kiz-cocuk/masal-yildiz.md` | Masal Yıldız | 8 | Utangaç |
| 11 | `.ai/.personas/kiz-cocuk/nehir-sahin.md` | Nehir Şahin | 6 | Lider |
| 12 | `.ai/.personas/kiz-cocuk/peri-bulut.md` | Peri Bulut | 11 | Enerjik |
| 13 | `.ai/.personas/kiz-cocuk/pinar-deniz.md` | Pınar Deniz | 9 | Kaşif |
| 14 | `.ai/.personas/kiz-cocuk/ruya-aktas.md` | Rüya Aktaş | 7 | Enerjik |
| 15 | `.ai/.personas/kiz-cocuk/selin-ozturk.md` | Selin Öztürk | 10 | Yaratıcı |
| 16 | `.ai/.personas/kiz-cocuk/elifsu-kaya-arabesk.md` | Elifsu Kaya | 9 | Arabesk Kaşif |
| 17 | `.ai/.personas/kiz-cocuk/mina-celik-dans.md` | Mina Çelik | 10 | Dans Enerjik |

#### §6.3.2 Erkek Çocuk — `erkek-cocuk/` (12 · 4-11)

| # | Dosya yolu | İsim | Yaş | Mood |
|---|-----------|------|-----|------|
| 18 | `.ai/.personas/erkek-cocuk/yusuf-bulut.md` | Yusuf Bulut | 8 | Kaşif |
| 19 | `.ai/.personas/erkek-cocuk/goktug-kaya.md` | Göktuğ Kaya | 10 | Enerjik |
| 20 | `.ai/.personas/erkek-cocuk/efe-demir.md` | Efe Demir | 7 | Sporcu |
| 21 | `.ai/.personas/erkek-cocuk/kerem-aydin.md` | Kerem Aydın | 9 | Meraklı |
| 22 | `.ai/.personas/erkek-cocuk/deniz-yilmaz.md` | Deniz Yılmaz | 11 | Sessiz |
| 23 | `.ai/.personas/erkek-cocuk/mert-celik.md` | Mert Çelik | 8 | Enerjik |
| 24 | `.ai/.personas/erkek-cocuk/emir-yildiz.md` | Emir Yıldız | 6 | Kaşif |
| 25 | `.ai/.personas/erkek-cocuk/atlas-arslan.md` | Atlas Arslan | 10 | Enerjik |
| 26 | `.ai/.personas/erkek-cocuk/kuzey-koc.md` | Kuzey Koç | 9 | Meraklı |
| 27 | `.ai/.personas/erkek-cocuk/arda-sahin.md` | Arda Şahin | 7 | Sporcu |
| 28 | `.ai/.personas/erkek-cocuk/yigit-can-arabesk.md` | Yiğit Can | 10 | Arabesk Meraklı |
| 29 | `.ai/.personas/erkek-cocuk/egehan-yildiz-dans.md` | Egehan Yıldız | 11 | Dans Sporcu |

#### §6.3.3 Genç Kız — `genc-kiz/` (17 · 12-17)

| # | Dosya yolu | İsim | Yaş | Mood |
|---|-----------|------|-----|------|
| 30 | `.ai/.personas/genc-kiz/alya-yilmaz-romantik.md` | Alya Yılmaz | 14 | Romantik |
| 31 | `.ai/.personas/genc-kiz/zeynep-sahin-enerjik.md` | Zeynep Şahin | 16 | Enerjik |
| 32 | `.ai/.personas/genc-kiz/elif-bulut-melankolik.md` | Elif Bulut | 13 | Melankolik |
| 33 | `.ai/.personas/genc-kiz/asel-kaya-moody.md` | Asel Kaya | 15 | Moody |
| 34 | `.ai/.personas/genc-kiz/defne-demir-sosyal.md` | Defne Demir | 17 | Sosyal |
| 35 | `.ai/.personas/genc-kiz/azra-karaca-romantik.md` | Azra Karaca | 14 | Romantik |
| 36 | `.ai/.personas/genc-kiz/nehir-deniz-enerjik.md` | Nehir Deniz | 16 | Enerjik |
| 37 | `.ai/.personas/genc-kiz/asya-aydin-melankolik.md` | Asya Aydın | 13 | Melankolik |
| 38 | `.ai/.personas/genc-kiz/irem-celik-romantik.md` | İrem Çelik | 15 | Romantik |
| 39 | `.ai/.personas/genc-kiz/sude-yildiz-enerjik.md` | Sude Yıldız | 17 | Enerjik |
| 40 | `.ai/.personas/genc-kiz/yagmur-koc-sosyal.md` | Yağmur Koç | 14 | Sosyal |
| 41 | `.ai/.personas/genc-kiz/ece-arslan-moody.md` | Ece Arslan | 16 | Moody |
| 42 | `.ai/.personas/genc-kiz/ceren-ozturk-melankolik.md` | Ceren Öztürk | 13 | Melankolik |
| 43 | `.ai/.personas/genc-kiz/dilara-aktas-enerjik.md` | Dilara Aktaş | 15 | Enerjik |
| 44 | `.ai/.personas/genc-kiz/begum-erdem-sosyal.md` | Begüm Erdem | 17 | Sosyal |
| 45 | `.ai/.personas/genc-kiz/zeliha-demir-arabesk.md` | Zeliha Demir | 15 | Arabesksever |
| 46 | `.ai/.personas/genc-kiz/buse-yilmaz-dans.md` | Buse Yılmaz | 16 | Danssever |

#### §6.3.4 Genç Erkek — `genc-erkek/` (12 · 12-17)

| # | Dosya yolu | İsim | Yaş | Mood |
|---|-----------|------|-----|------|
| 47 | `.ai/.personas/genc-erkek/metehan-sahin-sporcu.md` | Metehan Şahin | 15 | Sporcu |
| 48 | `.ai/.personas/genc-erkek/alparslan-demir-romantik.md` | Alparslan Demir | 17 | Romantik |
| 49 | `.ai/.personas/genc-erkek/emirhan-celik-hiphop.md` | Emirhan Çelik | 14 | Hip-Hop |
| 50 | `.ai/.personas/genc-erkek/kaan-yildiz-gamer.md` | Kaan Yıldız | 16 | Gamer |
| 51 | `.ai/.personas/genc-erkek/berkay-arslan-sosyal.md` | Berkay Arslan | 13 | Sosyal |
| 52 | `.ai/.personas/genc-erkek/ege-koc-sporcu.md` | Ege Koç | 15 | Sporcu |
| 53 | `.ai/.personas/genc-erkek/doruk-ozturk-hiphop-rap.md` | Doruk Öztürk | 17 | Hip-Hop |
| 54 | `.ai/.personas/genc-erkek/cinar-aktas-gamer-elektronik.md` | Çınar Aktaş | 14 | Gamer |
| 55 | `.ai/.personas/genc-erkek/ruzgar-bulut-romantik-rock.md` | Rüzgar Bulut | 16 | Romantik |
| 56 | `.ai/.personas/genc-erkek/toprak-erdem-sosyal-trend.md` | Toprak Erdem | 13 | Sosyal |
| 57 | `.ai/.personas/genc-erkek/furkan-sahin-arabesk.md` | Furkan Şahin | 17 | Arabesk Melankolik |
| 58 | `.ai/.personas/genc-erkek/atakan-demir-dans.md` | Atakan Demir | 16 | Dans Enerjik |

#### §6.3.5 Yetişkin Kadın — `yetiskin-kadin/` (5 · 25-45)

| # | Dosya yolu | İsim | Yaş | Mood |
|---|-----------|------|-----|------|
| 59 | `.ai/.personas/yetiskin-kadin/ebru-arslan-anne.md` | Ebru Arslan | 38 | Anne |
| 60 | `.ai/.personas/yetiskin-kadin/ayse-yilmaz-romantik.md` | Ayşe Yılmaz | 32 | Romantik |
| 61 | `.ai/.personas/yetiskin-kadin/buket-kaya-enerjik.md` | Buket Kaya | 28 | Enerjik |
| 62 | `.ai/.personas/yetiskin-kadin/ceyda-demir-melankolik.md` | Ceyda Demir | 35 | Melankolik |
| 63 | `.ai/.personas/yetiskin-kadin/deniz-ozturk-profesyonel.md` | Deniz Öztürk | 41 | Profesyonel |

#### §6.3.6 Yetişkin Erkek — `yetiskin-erkek/` (5 · 25-45)

| # | Dosya yolu | İsim | Yaş | Mood |
|---|-----------|------|-----|------|
| 64 | `.ai/.personas/yetiskin-erkek/baran-koc-romantik.md` | Baran Koç | 33 | Romantik |
| 65 | `.ai/.personas/yetiskin-erkek/emre-bulut-baba.md` | Emre Bulut | 37 | Baba |
| 66 | `.ai/.personas/yetiskin-erkek/ahmet-celik-hiphop.md` | Ahmet Çelik | 29 | Hip-Hop |
| 67 | `.ai/.personas/yetiskin-erkek/can-yildiz-enerjik.md` | Can Yıldız | 31 | Enerjik |
| 68 | `.ai/.personas/yetiskin-erkek/doruk-erdem-melankolik.md` | Doruk Erdem | 39 | Melankolik |

**Toplam: 17 + 12 + 17 + 12 + 5 + 5 = 68** ✅

### §6.4 Sayım Notları ve Anormallıklar

| # | Not | Etki |
|---|-----|------|
| 1 | Eski `index.md` "8 yeni persona (2026-06-03)" der — bu 8 persona tablonun içinde (16-17, 28-29, 45-46, 57-58 numaralılar) | Toplam 68'e dahil, çakışma yok |
| 2 | Yaş aralığı: kök index **4-11**, eski eşleme başlıkları **6-11** (çocuk grupları) | ⚠️ VERIFICATION REQUIRED → §2.1 |
| 3 | Eski eşlemede 19. satır adı "Göktüğ Kaya" yazılmış; dosya `goktug-kaya.md` | Yazı hatası; katalogda **Göktuğ Kaya** |
| 4 | "Deniz" adı hem kız çocuk (22) hem yetişkin kadın (63) persona'sında geçer | Farklı grup/farklı dosya — çakışma değil |
| 5 | Mood tipleri 10 temel + 8 arabesk/dans türevi = **18** küme | Ayrıntı: [[mood-taxonomy]] |
| 6 | 68 dosyanın hiçbiri bu oturumda üretilmedi (farklı adım) | §2.4 + görev şartı |

### §6.5 Mood × Grup Yoğunluk Özeti

> Mood adlarının tam tanımı → [[mood-taxonomy]]; bu tablo yalnız yoğunluk sayımıdır.

| Mood kümesi | Kız çocuk | Erkek çocuk | Genç kız | Genç erkek | Yetişkin K | Yetişkin E | Toplam |
|---|---|---|---|---|---|---|---|
| Enerjik | 5 | 3 | 3 | 0 | 1 | 1 | **13** |
| Kaşif | 3 | 2 | 0 | 0 | 0 | 0 | **5** |
| Yaratıcı | 3 | 0 | 0 | 0 | 0 | 0 | **3** |
| Utangaç / Sessiz | 2 | 1 | 0 | 0 | 0 | 0 | **3** |
| Lider / Meraklı | 2 | 2 | 0 | 0 | 0 | 0 | **4** |
| Sporcu | 0 | 2 | 0 | 2 | 0 | 0 | **4** |
| Romantik | 0 | 0 | 3 | 2 | 1 | 1 | **7** |
| Melankolik | 0 | 0 | 3 | 0 | 1 | 1 | **5** |
| Moody | 0 | 0 | 2 | 0 | 0 | 0 | **2** |
| Sosyal | 0 | 0 | 3 | 2 | 0 | 0 | **5** |
| Hip-Hop | 0 | 0 | 0 | 2 | 0 | 1 | **3** |
| Gamer | 0 | 0 | 0 | 2 | 0 | 0 | **2** |
| Anne / Baba / Profesyonel | 0 | 0 | 0 | 0 | 1+1 | 1 | **3** |
| Arabesk türevleri (4) | 1 | 1 | 1 | 1 | 0 | 0 | **4** |
| Dans türevleri (4) | 1 | 1 | 1 | 1 | 0 | 0 | **4** |
| **TOPLAM** | **17** | **12** | **17** | **12** | **5** | **5** | **68** |

**Okuma notu:** Yetişkin gruplarda mood başına 1'er persona vardır (n=5 → az küme);
çocuk/genç gruplarda Enerjik ve Romantik yoğundur. Yoğunluk ≠ popülerlik — bu yalnız
envanter sayımıdır; temsil/iddia için [[research-bank]] gerekir (⚠️ VERIFICATION REQUIRED).

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[CLAUDE.md]]` | Vault anayasası — Guardrail #16 kaynağı | ✅ `.ai/CLAUDE.md` |
| `[[AGENTS.md]]` | Agent routing §6 (test, persona → QA) + §9 handover | ✅ `.ai/AGENTS.md` |
| `[[WORKFLOW.md]]` | Süreçler, boot protokolü | ✅ `.ai/WORKFLOW.md` |
| `[[.templates/index]]` | Şablon registry'si (#37 persona-template) | ✅ `.ai/.templates/index.md` |
| `[[.templates/personas/persona-template]]` | Persona üretim şablonu (kayıt #37) | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[.templates/documentation/docs-md-template]]` | Bu dosyanın yapısal anahtarı (8-bölüm, 7 alan) | ✅ `.ai/.templates/documentation/docs-md-template.md` |
| `[[mood-taxonomy]]` | Mood küme sınıflandırması (kardeş dosya) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[test-scenarios-mapping]]` | Persona × senaryo eşlemesi (kardeş dosya) | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[research-bank]]` | Gerçek-dünya kaynak bankası | 🔄 **BAŞKA AJAN YAZIYOR** — `.ai/.personas/research-bank.md` (bu oturumda dokunulmaz) |
| `[[personas/methodology]]` | Level 1/2/3 test metodolojisi | 📋 planlanan — `.ai/.personas/methodology.md` |
| `[[../.decisions/accepted/ADR-023-persona-driven-testing]]` | Şart 1c + 20 persona test matrisi | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |
| `[[../index]]` | Master katalog — §12 Personas satırı (kırık link notu) | ✅ `.ai/index.md` |
| `[[../log.md]]` | Audit trail (append-only) | ✅ `.ai/log.md` |
| `[[../keys.md]]` | Keyword haritası | ✅ `.ai/keys.md` |
| `[[personas/index]]` | Persona şablonundaki kısaltma biçimi | ✅ karşılığı bu dosya — `.ai/.personas/index.md` (şablonda `personas/` yazımı kalan eski biçimdir) |

*`📋 planlanan` satırlar hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda satır `✅`ye çevrilir ve `log.md`'ye append edilir. `🔄` satırı eşzamanlı yazım nedeniyle bu oturumda değiştirilemez.*

### §7.2 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — `.ai/.personas/` dizini kuruldu; 68 persona kataloğu (6 grup), dizin ağacı, Guardrail #16 + ADR-005 kuralları, tam liste §6.3 (ADR-023 şart 1c) | vault-updater (subagent) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
