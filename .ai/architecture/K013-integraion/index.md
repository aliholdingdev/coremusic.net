---
node_id: K013-integraion
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - api-contracts
  - adapters
  - external-systems
  - event-contracts
  - protocol-adapters
  - synchronization
  - error-handling
  - integration-testing
depends_on:
  - K007-data
  - K008-domain
  - K009-security
  - K010-network
evidence:
  - "v2 §2.13 K013-integraion/ — çocuk listesi kaynağı (8 çocuk, birebir)"
  - ".ai/architecture/index.md §5/13 — integration-master.md adı kaydı"
  - "https://github.com/jellyfin/jellyfin — (K011/K015 ile ortak referans, 2026-10-12)"
validation:
  - "Çocuk adları v2 §2.13 ile birebir karşılaştırıldı (8/8) — 2026-10-12"
  - "Web kanıtları integraion-dogrulama.md §3'e işlendi — 2026-10-12"
open_questions:
  - "YouTube Music resmi public API yok iddiası → TOS riski → ⚠️ VERIFY REQUIRED"
  - "synchronization (K013) ile K015/synchronization sınırı → ⚠️ VERIFICATION REQUIRED"
  - "Dış API'nin sahibi K013 mü, çağıran K012/integration-service mi → ⚠️ VERIFICATION REQUIRED"
  - "event-contracts olay taşıma biçimi (webhook/olay akışı) → ⚠️ UNKNOWN"
group: mimari
updated: 2026-10-18
---


# K013-integraion — Dış Sistem Köprüsü

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K013-integraion |
| **K Adı** | Entegrasyon (Integration — dış sistem köprüsü) |
| **Kapsam** | Dış dünya ile köprü — API konuşmaları, adaptörler, üçüncü taraf sistemler. CoreMusic dışındaki sistemlerle (akış/servis sağlayıcılar) konuşan tek taraftır: kontratı yazar, adaptörü çevirir, hatayı sınırlar, senkronu yürütür. İç servisler dış dünyaya çıkmaz, bu kapıdan çıkar (Dependency Rule). Yazım `integraion` kaynaka sadık, dokunulmaz. 8 kademe-2 çocuk. |
| **Kritik Bağımlılıklar** | K007-data · K008-domain · K009-security · K010-network (kanıt: FM depends_on — K0100-identitiy ölü referansı 2026-10-18'de silindi ⚠️) |
| **Durum** | draft (8 kademe-2 çocuk) |
| **Son Güncelleme** | 2026-10-12 (çocuk adları v2 §2.13 ile birebir 8/8) |

## 1. ELI15 — bu oda ne işe yarar?

v2 §2.13: *"Dış dünya ile köprü — API konuşmaları, adaptörler, üçüncü taraf sistemler."*
CoreMusic dışındaki sistemlerle (akış/servis sağlayıcılar vb.) konuşan **tek** taraftır:
kontratı yazar, adaptörü çevirir, hatayı sınırlar, senkronu yürütür.
İç servisler dış dünyaya çıkmaz; **bu kapıdan** çıkar (Dependency Rule).
Yazım notu: klasör adı "integraion" **kaynağa sadık, dokunulmaz** (v2 §3 KURAL 1).

## 2. Çocuk kataloğu (kademe-2 — v2 §2.13 ile birebir)

> ELI15 sütunu **TASARIM İDDİASI**'dır (isimden türetildi, kanıt değildir).

| # | Çocuk | ELI15 rol (tasarım iddiası) | Durum |
|---|---|---|---|
| 1 | api-contracts | Dış API'nin istek/yanıt sözleşmesi | draft |
| 2 | adapters | Üçüncü taraf sistemlere adaptör | draft |
| 3 | external-systems | Tanımlı dış sistem envanteri | draft |
| 4 | event-contracts | Dış olayların sözleşmesi | draft |
| 5 | protocol-adapters | Protokol dönüşüm köprüleri | draft |
| 6 | synchronization | Dış sistemlerle veri senkronizasyonu | draft |
| 7 | error-handling | Hata, tekrar-dene ve sınır yönetimi | draft |
| 8 | integration-testing | Entegrasyon testleri (canlı/sahte çevre) | draft |

## 3. Bağımlılık özeti (tam metin: integraion-bagimliliklar.md)

| Yön | Düğüm | Ne için (TASARIM İDDİASI) |
|---|---|---|
| depends_on → | K007-data | Senkron sonucunun saklanması (synchronization) |
| depends_on → | K008-domain | Kontrat tipleri / domain modeli |
| depends_on → | K009-security | Anahtar, imza, gizli bilgi güvenliği |
| depends_on → | K010-network | Taşıma (HTTP vb.) |
| ← used_by | K011-application | Uygulama akışının dış çağrısı |
| ← used_by | K012-services | integration-service kontrat tüketimi |
| ← used_by | K014-media | Dış katalog senkronu (kapsam sınırı ⚠️) |

## 4. Durum & açık sorular

- **Durum:** draft — kademe-3/4 genişletmesi ADR-097 K4 fazı (C) bekliyor.
- Ayrıntı: integraion-bagimliliklar.md (§1–§5) · integraion-dogrulama.md (§1–§6).
- Kısa liste:
  1. YouTube Music resmi public API yok → TOS riski → ⚠️ VERIFY REQUIRED
  2. synchronization sahipliği (K013 ↔ K015) → ⚠️ VERIFICATION REQUIRED
  3. Dış API sahibi K013 mü, çağıran K012 mi → ⚠️ VERIFICATION REQUIRED
  4. Yeni dosya ön eki "integraion" (düğüm adından) — master "integration" (v2) → bilinçli karışıklık

**Kaynak:** v2 §2.13 · .ai/architecture/index.md §5/13 · ADR-097 §5


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `integraion-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `integraion-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `integration-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |

## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K013-integraion | [[K013-integraion]] | ./K013-integraion/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ← | karşı taraf referanslıyor | M→0 · M←1 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | ↔ | bağımlı (depends_on) | M→16 · M←31 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | → | bağımlı (depends_on) | M→10 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | ↔ | bağımlı (depends_on) | M→9 · M←8 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | ↔ | bağımlı (depends_on) | M→10 · M←14 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | ↔ | iki yönlü atıf | M→4 · M←9 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | ↔ | iki yönlü güçlü referans | M→7 · M←15 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | ↔ | iki yönlü güçlü referans | M→6 · M←18 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |

