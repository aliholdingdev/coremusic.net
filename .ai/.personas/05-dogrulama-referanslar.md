---
title: "Doğrulama & Referanslar"
type: persona-index
category: personas
version: 1.0.1
status: active
authority: reference
updated: 2026-09-29
---# Doğrulama & Referanslar## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] `type: persona-index`, `category: personas`, `version: 1.0.0`, `status: active`, `updated: 2026-09-26`
- [ ] H1 + Zorunlu Bağlantılar satırı (`[[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[.templates/index]]`)
- [ ] §1-§7 başlıkları eksiksiz (docs-md iskeleti)
- [ ] §4.0 Şablon-Önce bloğu birebir mevcut ve §4'ün ilk maddesi
- [ ] 6 grup tablosu kişi sayılarıyla **17/17/12/12/5/5 = 68**
- [ ] §6.3 tam liste **68 satır** + grup başına alt başlıklar
- [ ] Her gerçek-dünya iddiası `⚠️ VERIFICATION REQUIRED` etiketli ve `[[.personas/research-bank]]` pointer'ı var
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
| Test senaryosu dosyası | 6 | `test-senaryolari/` (✅ diskte — 6 dosya, 2026-09-26) |
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
| 31 | `.ai/.personas/genc-kiz/zeynep-sahin-enerjik.md` | Zeynep Şahin | 15 | Enerjik |
| 32 | `.ai/.personas/genc-kiz/elif-bulut-melankolik.md` | Elif Bulut | 16 | Melankolik |
| 33 | `.ai/.personas/genc-kiz/asel-kaya-moody.md` | Asel Kaya | 15 | Moody |
| 34 | `.ai/.personas/genc-kiz/defne-demir-sosyal.md` | Defne Demir | 17 | Sosyal |
| 35 | `.ai/.personas/genc-kiz/azra-karaca-romantik.md` | Azra Karaca | 14 | Romantik |
| 36 | `.ai/.personas/genc-kiz/nehir-deniz-enerjik.md` | Nehir Deniz | 16 | Enerjik |
| 37 | `.ai/.personas/genc-kiz/asya-aydin-melankolik.md` | Asya Aydın | 13 | Melankolik |
| 38 | `.ai/.personas/genc-kiz/irem-celik-romantik.md` | İrem Çelik | 13 | Romantik |
| 39 | `.ai/.personas/genc-kiz/sude-yildiz-enerjik.md` | Sude Yıldız | 14 | Enerjik |
| 40 | `.ai/.personas/genc-kiz/yagmur-koc-sosyal.md` | Yağmur Koç | 16 | Sosyal |
| 41 | `.ai/.personas/genc-kiz/ece-arslan-moody.md` | Ece Arslan | 15 | Moody |
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
| 5 | Mood tipleri 10 temel + 8 rol + 7 arabesk/dans türevi = **25** küme | Ayrıntı: [[.personas/mood-taxonomy]] |
| 6 | 68 persona + 6 senaryo + methodology diskte (updated: 2026-09-26); bu oturumda üretilmedi (farklı adım) | §2.4 + görev şartı |

### §6.5 Mood × Grup Yoğunluk Özeti

> Mood adlarının tam tanımı → [[.personas/mood-taxonomy]]; bu tablo yalnız yoğunluk sayımıdır.

| Mood kümesi | Kız çocuk | Erkek çocuk | Genç kız | Genç erkek | Yetişkin K | Yetişkin E | Toplam |
|---|---|---|---|---|---|---|---|
| Enerjik | 5 | 3 | 4 | 0 | 1 | 1 | **14** |
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
envanter sayımıdır; temsil/iddia için [[.personas/research-bank]] gerekir (⚠️ VERIFICATION REQUIRED).

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
| `[[.personas/mood-taxonomy]]` | Mood küme sınıflandırması (kardeş dosya) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[.personas/test-scenarios-mapping]]` | Persona × senaryo eşlemesi (kardeş dosya) | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[.personas/research-bank]]` | Gerçek-dünya kaynak bankası | 🔄 **BAŞKA AJAN YAZIYOR** — `.ai/.personas/research-bank.md` (bu oturumda dokunulmaz) |
| `[[.personas/methodology]]` | Level 1/2/3 test metodolojisi | ✅ (2026-09-26) — `.ai/.personas/methodology.md` |
| `[[.decisions/accepted/ADR-023-persona-driven-testing]]` | Şart 1c + 20 persona test matrisi | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |
| `[[.personas/index]]` | Master katalog — §12 Personas satırı (kırık link notu) | ✅ `.ai/index.md` |
| `[[log]]` | Audit trail (append-only) | ✅ `.ai/log.md` |
| `[[keys.md]]` | Keyword haritası | ✅ `.ai/keys.md` |
| `[[.personas/index]]` | Persona şablonundaki kısaltma biçimi | ✅ karşılığı bu dosya — `.ai/.personas/index.md` (şablonda `personas/` yazımı kalan eski biçimdir) |

*`📋 planlanan` satırlar hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda satır `✅`ye çevrilir ve `log.md`'ye append edilir. `🔄` satırı eşzamanlı yazım nedeniyle bu oturumda değiştirilemez.*

### §7.2 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — `.ai/.personas/` dizini kuruldu; 68 persona kataloğu (6 grup), dizin ağacı, Guardrail #16 + ADR-005 kuralları, tam liste §6.3 (ADR-023 şart 1c) | vault-updater (subagent) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
