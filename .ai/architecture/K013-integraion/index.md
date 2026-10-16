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
  - K010-identitiy
  - K014-network
evidence:
  - "v2 §2.13 K013-integraion/ — çocuk listesi kaynağı (8 çocuk, birebir)"
  - ".ai/architecture/index.md §5/13 — integration-master.md adı kaydı"
  - "https://github.com/jellyfin/jellyfin — (K011/K015 ile ortak referans, 2026-10-12)"
  - "Kademe-3 genişletme 2026-10-16 — 7 çocuk × 5 dosya = 35 yeni MD, her çocuk index.md güncellendi"
  - "Her içerik dosyası ≥350 satır (subagent raporları, 2026-10-16 disk ölçümü)"
validation:
  - "Çocuk adları v2 §2.13 ile birebir karşılaştırıldı (8/8) — 2026-10-12"
  - "Web kanıtları integraion-dogrulama.md §3'e işlendi — 2026-10-12"
  - "7 çocuk openapi kalıbında genişletildi (index + anayasa + sözleşme + doğrulama + kod-ornekleri + web-arastirma) — 2026-10-16"
  - "URL doğrulama: her iddia fetch ile; 404 elenen adaylar gerekçesiyle çocuk *-web-arastirma.md §9'da"
  - "external-systems: 2026-10-12 beyanlarının URL'siz 3'ü resmi kaynakla kanıtlandı (Apple ES256+kid ✅ · hub-and-spoke ✅ · YouTube Music kanıt sunuldu)"
open_questions:
  - "event-contracts olay taşıma biçimi (webhook/olay akışı) → ⚠️ UNKNOWN (karar ADR bekliyor)"
  - "synchronization (K013) ile K015/synchronization sınırı → ⚠️ VERIFICATION REQUIRED (sync-anayasa §11 iki ihtimal)"
  - "Dış API'nin sahibi K013 mü, çağıran K012/integration-service mi → ⚠️ VERIFICATION REQUIRED"
  - "error-handling: CoreMusic retry/breaker parametreleri → ⚠️ UNKNOWN (ADR bekliyor)"
  - "integration-testing: K013↔K019 sınır sahibi + live-test gate adresi → ⚠️ VERIFICATION REQUIRED"
group: mimari
---


# K013-integraion — Dış Sistem Köprüsü

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
| 1 | api-contracts | Dış API'nin istek/yanıt sözleşmesi | kademe-3 ✅ (openapi dalı) |
| 2 | adapters | Üçüncü taraf sistemlere adaptör | kademe-3 ✅ (2026-10-16) |
| 3 | external-systems | Tanımlı dış sistem envanteri | kademe-3 ✅ (2026-10-16) |
| 4 | event-contracts | Dış olayların sözleşmesi | kademe-3 ✅ (2026-10-16) |
| 5 | protocol-adapters | Protokol dönüşüm köprüleri | kademe-3 ✅ (2026-10-16) |
| 6 | synchronization | Dış sistemlerle veri senkronizasyonu | kademe-3 ✅ (2026-10-16) |
| 7 | error-handling | Hata, tekrar-dene ve sınır yönetimi | kademe-3 ✅ (2026-10-16) |
| 8 | integration-testing | Entegrasyon testleri (canlı/sahte çevre) | kademe-3 ✅ (2026-10-16) |

## 3. Bağımlılık özeti (tam metin: integraion-bagimliliklar.md)

| Yön | Düğüm | Ne için (TASARIM İDDİASI) |
|---|---|---|
| depends_on → | K007-data | Senkron sonucunun saklanması (synchronization) |
| depends_on → | K008-domain | Kontrat tipleri / domain modeli |
| depends_on → | K009-security | Anahtar, imza, gizli bilgi güvenliği |
| depends_on → | K010-identitiy | OAuth/PKCE akışlarında kimlik doğrulama |
| depends_on → | K014-network | Taşıma (HTTP vb.) |
| ← used_by | K011-application | Uygulama akışının dış çağrısı |
| ← used_by | K012-services | integration-service kontrat tüketimi |
| ← used_by | K015-media | Dış katalog senkronu (kapsam sınırı ⚠️) |

## 4. Durum & açık sorular

- **Durum:** kademe-2 tamamlandı + kademe-3 genişletme YAPILDI (2026-10-16) — kademe-4 ve bounded-context kararları ADR-097 K4 fazı (C) bekliyor.
- Ayrıntı: integraion-bagimliliklar.md (§1–§5) · integraion-dogrulama.md (§1–§6) · çocuk *-web-arastirma.md dosyaları (URL kanıtları).
- Kısa liste:
  1. event-contracts taşıma biçimi (webhook/olay akışı) → ⚠️ UNKNOWN (ADR bekliyor)
  2. synchronization sahipliği (K013 ↔ K015) → ⚠️ VERIFICATION REQUIRED (sync-anayasa §11)
  3. Dış API sahibi K013 mü, çağıran K012 mi → ⚠️ VERIFICATION REQUIRED
  4. error-handling retry/breaker parametreleri → ⚠️ UNKNOWN (ADR bekliyor)
  5. integration-testing K013↔K019 sınır sahibi + live-test gate adresi → ⚠️ VERIFICATION REQUIRED

## 4b. Kademe-3 genişletme kaydı (2026-10-16 — openapi kalıbı)

> Her çocuk, `api-contracts/openapi/` kalıbında 5 içerik dosyasıyla genişletildi:
> `[konu]-anayasa.md` · `[konu]-sozlesme.md` · `[konu]-dogrulama.md` · `[konu]-kod-ornekleri.md` · `[konu]-web-arastirma.md`.
> Kod örnekleri **12 dilde** (PHP · JS · TS · Python · Go · Java · C# · Rust · C++ · Ruby · Kotlin · Swift · Elixir).
> Web kanıtı: her iddia `İddia · URL · 2026-10-16 · Durum` — fetch ile doğrulandı; 404 elenenler gerekçesiyle §9'da.

| Çocuk | Dosya öneki | İçerik satır (en küçük–en büyük) | URL doğrulama |
|---|---|---|---|
| adapters | `adapters-` | 355–700 | 21 benzersiz çekildi · 7 elendi |
| external-systems | `external-` | 353–579 | 14/14 çekildi · 3 elendi (gerekçeli) |
| event-contracts | `event-` | 371–790 | 15 çekildi · 10 404 elendi |
| protocol-adapters | `proto-` | 354–743 | 15 çekildi · 0 elenen |
| synchronization | `sync-` | 380–812 | 18 benzersiz · 4 elenen |
| error-handling | `error-` | 351–704 | 13/13 çekildi · 0 elenen |
| integration-testing | `ittest-` | 350–599 | 17 çekildi · 12 reddedildi · 4 ⚠️ |

**Kapatılan 2026-10-12 beyanları:** Apple Music ES256+kid ✅ (2 resmi kaynak) · hub-and-spoke/open-playlist-engine ✅ (README birebir) · YouTube Music kanıt sunuldu (karar üst otoritede).

**Kalan ⚠️ (karar VERİLMEZ):** K013↔K015 sync sahipliği · K013↔K019 test sahipliği · webhook vs akış · retry/breaker parametreleri · canlı test gate adresi.

**Kaynak:** v2 §2.13 · .ai/architecture/index.md §5/13 · ADR-097 §5


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `integraion-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `integraion-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `integration-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |

> **Kademe-3 (2026-10-16):** Her çocuk klasöründe 5 içerik dosyası (mimari ×2 · kod ×2 · kanit ×1)
> + güncellenmiş `index.md` vardır — ayrıntı ve `group` alanları her çocuk `index.md`'sindeki
> §GRUPLAR tablosundadır (toplam 35 yeni MD, 7 index güncellemesi).
