---
title: "Amaç & Kapsam"
type: persona-index
category: personas
version: 1.0.1
status: active
authority: reference
updated: 2026-09-29
---# Amaç & Kapsam## §1 Amaç

Bu dosya, CoreMusic platformunun **persona envanterinin kök kataloğudur**: 6 grup / 68 kurgusal kullanıcının dosya yollarını, isimlerini, yaşlarını ve mood tiplerini tek tabloda toplar, dizin yapısını ve persona üretim kurallarını tanımlar. Persona, hedef kullanıcının **kurgusal ama kaynak-destekli** profilidir — AI o kişi gibi davranarak Level 1 rol testi yapar, o kişinin cihaz/tema/internet koşullarında Level 2/3 testini yürütür (şablon: [[.templates/personas/persona-template]]). Bu katalog **ADR-023 şart 1c** kapsamında var olmuştur: debate 3/20'de "eski persona vault'ta yok (dead reference)" itirazı → "` .ai/.personas/` taşıma" şartı olarak bağlayıcı hâle gelmiştir (kaynak: [[.decisions/accepted/ADR-023-persona-driven-testing]] §5.4).

| Alan | Değer |
|------|-------|
| Doküman tipi | Persona kataloğu (kök index — SSOT değil, `authority: reference`) |
| Persona sayısı | **68** (eski vault disk sayımı ile birebir; §6 tam listesi) |
| Grup sayısı | **6** (kız çocuk · genç kız · erkek çocuk · genç erkek · yetişkin kadın · yetişkin erkek) |
| Hedef kitle | QA Engineer (test ataması), UI Designer (erişilebilirlik/temsil), Master Orchestrator (vault senkronu) |
| Tetikleyici olay | ADR-023 debate şartı 1c (2026-09-25 — 18/2/0 KABUL) + `[[.personas/index]]` §12'de kırık `personas/*` linkleri |
| Vault bağlantısı | Şablon → [[.templates/personas/persona-template]] · Mood → [[.personas/mood-taxonomy]] · Eşleme → [[.personas/test-scenarios-mapping]] |
| Karar kaynağı | [[.decisions/accepted/ADR-023-persona-driven-testing]] (frozen YOK — active, Arch Lead ⏳) |
| Sayım kaynağı | Eski vault `.ai/personas/` disk listesi (salt-okunur; 2026-09-26 sayımı) |
| Kopyalama durumu | Persona içerikleri (735 satırlık detaylar) **KOPYALANMADI** — bu dosya katalog/iskelettir |

### §1.1 Bu Dosya Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Dosya | Bu Dosya Kullanılmaz |
|---------|-------------|----------------------|
| 68 persona'nın nerede olduğu, hangi grupta olduğu | ✅ Bu dosya (`[[.personas/index]]`) | — |
| Mood küme adı, Big Five eğilimi, müzik eşlemesi | [[.personas/mood-taxonomy]] | ❌ |
| Persona × test senaryosu eşlemesi, cihaz/WCAG matrisi | [[.personas/test-scenarios-mapping]] | ❌ |
| Gerçek-dünya araştırması (nüfus, cihaz, WCAG, KVKK) | [[.personas/research-bank]] (⚠️ başka ajan yazıyor) | ❌ |
| Test seviyeleri (Level 1/2/3) ve başarı metrikleri | `personas/methodology` ✅ | ❌ |
| Tek persona profili üretimi | [[.templates/personas/persona-template]] | ❌ |
| 20 satırlık bağlayıcı test matrisi (karar metni) | [[.decisions/accepted/ADR-023-persona-driven-testing]] §2.2a | ❌ |

**Ayırıcı test:** "Dosya bir kişiyi mi listeliyor?" → Evet ise bu dosya. "Bir mood'u mu tanımlıyor?" → `mood-taxonomy`. "Bir kararı mı kaydediyor?" → ADR.

### §1.2 Neden Şimdi (2026-09-26)

1. **Şart 1c açık:** ADR-023 §5.4 şart 1c — "eski vault persona envanteri `.ai/.personas/` altına taşınır (eski disk korunur; dosya adı değişmez)" → 3 şart kapanmadan karar Active/Frozen olmaz.
2. **`[[.personas/index]]` §12 kırık linkleri:** `Personas | [[.personas/index]], [[.personas/methodology]], [[.personas/mood-taxonomy]]` satırındaki linkler vault'ta hedefsiz (2026-09-06 güncellik notu).
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
| **Yetişkin Erkek** (`yetiskin-erkek/`) | 25-45 | **5** | Hip-Hop, Enerjik, Romantik, Melankolik, Baba |
| **TOPLAM** | — | **68** | — |

**Doğrulama (2026-09-26, eski vault disk listesi):** `kiz-cocuk` **17** · `genc-kiz` **17** · `erkek-cocuk` **12** · `genc-erkek` **12** · `yetiskin-kadin` **5** · `yetiskin-erkek` **5** → **17+17+12+12+5+5 = 68** ✅ (tam liste §6).

> ⚠️ **VERIFICATION REQUIRED — yaş aralığı çelişkisi:** Eski `index.md` ve ADR-023 grupları **4-11** yazar; eski `test-scenarios-mapping.md` §2A/§2B başlıkları **6-11** yazar. Bu katalog **4-11** alır (ADR-023 §2.2a satır 9/11 ile aynı); 6-11 ifadesi eski eşleme dosyasında kalmıştır. Çözüm: [[.personas/research-bank]] + Vault Steward onayı. → `[[.personas/research-bank]]` (başka ajan yazıyor — bu dosyaya DOKUNMA).

### §2.2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 68 persona'nın dosya yolu / isim / yaş / mood kataloğu (§6) | Persona içeriği (kimlik, Big Five, test adımları — persona-template ile üretilir) |
| 6 grup dağılımı, grup yaş/mood tablosu (§2.1) | Mood tanımları ve müzik eşlemeleri (→ [[.personas/mood-taxonomy]]) |
| Dizin ağacı ve dosya rolleri (§3) | Persona × senaryo × cihaz matrisi (→ [[.personas/test-scenarios-mapping]]) |
| Üretim/taşıma kuralları, Guardrail #16, kaynak etiketleme (§4) | Test seviyeleri ve metrikleri (→ `personas/methodology` ✅) |
| Doğrulama listesi ve 68 satırlık tam sayım (§6) | Gerçek-dünya araştırması (→ [[.personas/research-bank]] ⚠️) |
| ADR-023 şart 1c kapsamı (taşınan envanter özeti) | ADR-023 karar metni (→ ADR dosyası; frozen değil, aktif karar) |

*Alt konular:* amaç → grup dağılımı → dizin ağacı → kurallar → üretim akışı → doğrulama/tam liste → referanslar.
Kapsam dışı için: mood → [[.personas/mood-taxonomy]] · eşleme → [[.personas/test-scenarios-mapping]] · araştırma → [[.personas/research-bank]] · persona şablonu → [[.templates/personas/persona-template]].

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

