---
node_id: K021-recomenataion
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - recommendation-master.md
  - recommendation-kod-ornekleri.md
  - recommendation-arastirma.md
  - ranking
  - personalization
  - evaluation
depends_on:
  - "K000–K5000 Architecture Registry"
  - K020-discovery
evidence:
  - "v2 §2.20 mini-ağaç (→ index.md · recommendation-master.md · ranking/ · personalization/ · evaluation/) — G:/Drive'ım/Projelerim/Project-notes/Bayram Ali Prompt Arşivi & Notlar/Coremusic/coremusic-katmanli-mimari-agaci-v2.md (satır 577–584)"
  - ".ai/architecture/index.md §5 satır 88 (draft) · §6 satır 97 (imla: 'recomenataion' KORUNUR)"
  - "2026-10-17 — 2 yeni dosya: recommendation-kod-ornekleri.md (22 dil) · recommendation-arastirma.md (28 URL HTTP doğrulamalı)"
validation:
  - "2026-10-12 — v2 §2.20 şekli birebir karşılaştırıldı (5 öğe)"
## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K021-recomenataion | [[K021-recomenataion]] | ./K021-recomenataion/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | → | bu K referanslıyor | M→7 · M←0 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | → | bu K referanslıyor | M→5 · M←0 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | → | bu K referanslıyor | M→2 · M←0 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | → | bağımlı (depends_on) | M→9 · M←0 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K021-recomenataion |
| **K Adı** | Öneri (Recommendation) |
| **Kapsam** | "Senin için ne çalsam?" sorusunun nasıl cevaplanacağının planı: sıralama (ranking/), kişiselleştirme (personalization/) ve işe yaradığını ölçme (evaluation/). recommendation-master.md programın anayasası (S1–S6 boru hattı + kapılar); kod-ornekleri 22 dil referansı; arastirma 28 URL HTTP doğrulamalı. Yazım `recomenataion` korunur. |
| **Kritik Bağımlılıklar** | K020-discovery (kanıt: FM depends_on — öneri, keşif/katalog verisi olmadan çalışmaz) |
| **Durum** | draft (3 kademe-2 çocuk + 3 kök dosya · algoritma/eşik tanımı yok) |
| **Son Güncelleme** | 2026-10-17 (ID K020→K021 dönüşümü + iki-ekseni şema · 2 yeni dosya) |

## ELI15 — Bu klasör ne işe yarar?

"Senin için ne çalsam?" sorusunun **nasıl** cevaplanacağının planıdır: sıralama
(`ranking/`), kişiselleştirme (`personalization/`) ve **işe yaradığını ölçme**
(`evaluation/`). `recommendation-master.md` programın anayasası (S1–S6 boru hattı +
kapılar); `recommendation-kod-ornekleri.md` 22 dil uygulama referansı;
`recommendation-arastirma.md` HTTP doğrulamalı kaynak kanıtı.

## Çocuk kataloğu (v2 §2.20 + 2026-10-17 genişleme)

| Öğe | Tür | Rol | Durum |
|---|---|---|---|
| `recommendation-master.md` | dosya | Program anayasası: girdi/çıktı, S1–S6, kapılar, riskler | draft (≥500) |
| `recommendation-kod-ornekleri.md` | dosya | **22 dil** kod matrisi (S1–S6 aşamaları) — `group: kod` | draft |
| `recommendation-arastirma.md` | dosya | Web araştırması: 28 URL · 15×200 · 9×404 elendi · 4×403 dürüst kayıt — `group: kanit` | draft |
| `ranking/` | dizin | Sıralama alt bölgesi (`ranking/index.md`) | draft |
| `personalization/` | dizin | Kişiselleştirme alt bölgesi (`personalization/index.md`) | draft |
| `evaluation/` | dizin | Öneri değerlendirmesi/metricleri (`evaluation/index.md`) | draft |

## Durum

- **status:** `draft` — şekil v2 birebir + içerik derinleştirildi; **algoritma/eşik tanımı yok** (uydurma yasak).
- **depends_on:** registry + `K020-discovery` (öneri, keşif/katalog verisi olmadan çalışmaz).
- **Kapı:** öneri tasarımı, veri/gizlilik onayı olmadan **üretilmez** (KVKK kapsamı tanımsız — `K020-comilance` açık sorusu).
- **⚠️ ID/bant kaydı:** klasör diskte `K021-recomenataion` — Band-2 kapısı ADR-098 öncesi (registry §4); 40+ vault referansı K020 yazar. Üst karar bekliyor, **düzeltme YAPILMADI**.

## §BAĞLANTILAR

> 2026-10-17 · orkestratör · kapsam: **bu grup içi 7 dosya + 2 dış komşu** (K000–K013 bağlantıları kullanıcı kararıyla kapsam dışı).

| Hedef | Wiki-link | Göreli dosya yolu | İlişki |
|---|---|---|---|
| Master | `[[recommendation-master]]` | `recommendation-master.md` | S1–S6 boru hattı + kapılar |
| Ranking | `[[ranking/index]]` | `ranking/index.md` | S2/S3 sıralama |
| Personalization | `[[personalization/index]]` | `personalization/index.md` | profil + KVKK kapıları |
| Evaluation | `[[evaluation/index]]` | `evaluation/index.md` | S6 offline/online/izleme |
| Kod örnekleri | `[[recommendation-kod-ornekleri]]` | `recommendation-kod-ornekleri.md` | 22 dil referansı |
| Araştırma | `[[recommendation-arastirma]]` | `recommendation-arastirma.md` | HTTP 200 kaynaklar |
| Dış komşu | `[[../K020-discovery/index]]` | `../K020-discovery/index.md` | dış katalog keşfi |
| Dış komşu | `[[../../K006-yapay-zeka/recommendation/recommendation-kapsam]]` | `../../K006-yapay-zeka/recommendation/recommendation-kapsam.md` | ML tarafı sınırı |

## İlişkili kaynaklar

| Kaynak | Ne verir |
|---|---|
| `@.accepted/ADR-030` (AI strategy) | YZ stratejisi çerçevesi |
| `@.accepted/ADR-029` (listening rooms) · `ADR-025` (EQ) | Sosyal/EQ tarafındaki ilgili kararlar |
| `shared/src/AI/AIEngine.php` | Faz-1 repo kanıtı (30-32, 49-77, 65) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `recommendation-master.md` | Master künye + sorumluluk haritası | Kademe-1 |
| kod | `recommendation-kod-ornekleri.md` | 22 dil uygulama referansı | Kademe-2 |
| kanit | `recommendation-arastirma.md` | Web araştırması + HTTP doğrulama | Kademe-2 |
| mimari | `ranking/index.md` · `personalization/index.md` · `evaluation/index.md` | Alt bölge kapıları | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
