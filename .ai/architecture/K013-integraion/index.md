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
validation:
  - "Çocuk adları v2 §2.13 ile birebir karşılaştırıldı (8/8) — 2026-10-12"
  - "Web kanıtları integraion-dogrulama.md §3'e işlendi — 2026-10-12"
open_questions:
  - "YouTube Music resmi public API yok iddiası → TOS riski → ⚠️ VERIFY REQUIRED"
  - "synchronization (K013) ile K015/synchronization sınırı → ⚠️ VERIFICATION REQUIRED"
  - "Dış API'nin sahibi K013 mü, çağıran K012/integration-service mi → ⚠️ VERIFICATION REQUIRED"
  - "event-contracts olay taşıma biçimi (webhook/olay akışı) → ⚠️ UNKNOWN"
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
| depends_on → | K010-identitiy | OAuth/PKCE akışlarında kimlik doğrulama |
| depends_on → | K014-network | Taşıma (HTTP vb.) |
| ← used_by | K011-application | Uygulama akışının dış çağrısı |
| ← used_by | K012-services | integration-service kontrat tüketimi |
| ← used_by | K015-media | Dış katalog senkronu (kapsam sınırı ⚠️) |

## 4. Durum & açık sorular

- **Durum:** draft — kademe-3/4 genişletmesi ADR-097 K4 fazı (C) bekliyor.
- Ayrıntı: integraion-bagimliliklar.md (§1–§5) · integraion-dogrulama.md (§1–§6).
- Kısa liste:
  1. YouTube Music resmi public API yok → TOS riski → ⚠️ VERIFY REQUIRED
  2. synchronization sahipliği (K013 ↔ K015) → ⚠️ VERIFICATION REQUIRED
  3. Dış API sahibi K013 mü, çağıran K012 mi → ⚠️ VERIFICATION REQUIRED
  4. Yeni dosya ön eki "integraion" (düğüm adından) — master "integration" (v2) → bilinçli karışıklık

**Kaynak:** v2 §2.13 · .ai/architecture/index.md §5/13 · ADR-097 §5
