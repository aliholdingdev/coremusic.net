---
node_id: K014-network
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - comms
  - connectivity
  - service-discovery
  - offline-sync
  - handoff
  - multi-room
  - network-testing
depends_on:
  - K001-donanım
  - K009-security
  - K010-identitiy
evidence:
  - "v2 §2.14 K014-network/ — çocuk listesi kaynağı (7 çocuk, birebir; comms'un 5 torunu da v2'de)"
  - ".ai/architecture/index.md §5/14 — network-master.md adı kaydı"
  - "WebRTC = IETF standardı (teknik terim) — 2026-10-12 beyanı (URL yok ⚠️)"
validation:
  - "Çocuk adları v2 §2.14 ile birebir karşılaştırıldı (7/7 + comms 5/5) — 2026-10-12"
  - "Web kanıtları network-dogrulama.md §3'e işlendi — 2026-10-12"
open_questions:
  - "handoff ile multi-room arasındaki sınır (teslim mi, senkron mu) → ⚠️ VERIFICATION REQUIRED"
  - "offline-sync (K014) ile K015/offline ve K013/synchronization sınırı → ⚠️ VERIFICATION REQUIRED"
  - "service-discovery kapsamı (yerel cihaz keşfi mi, servis kaydı mı) → ⚠️ VERIFICATION REQUIRED"
  - "comms torunlarının kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
group: mimari
---


# K014-network — Bağlantı Katmanı

## 1. ELI15 — bu oda ne işe yarar?

v2 §2.14: *"Bağlantı kablolarının yazılımı — internet, evdeki cihazlar arası iletişim."*
Paketin **nereye gideceğini ve nasıl gideceğini** burası bilir: internet (HTTP),
canlı bağlantı (WebSocket), doğrudan medya yolu (WebRTC), yerel ağ (TCP/IP),
cihazlar arası konuşma (device-communication) — hepsi **comms/** altında toplanır.
Bağlantının kendisi donanım işidir (K001-donanım), güvenliği K009'dur.

## 2. Çocuk kataloğu (kademe-2 — v2 §2.14 ile birebir)

> ELI15 sütunu **TASARIM İDDİASI**'dır (isimden türetildi, kanıt değildir).

| # | Çocuk | ELI15 rol (tasarım iddiası) | Durum |
|---|---|---|---|
| 1 | comms | Temel iletişim protokolleri ailesi (5 torun: http · websocket · webrtc · tcp-ip · device-communication) | draft |
| 2 | connectivity | Bağlantı kurma/sürme, bağlantı durumu | draft |
| 3 | service-discovery | Ulaşılacak noktayı bulma (keşif) | draft |
| 4 | offline-sync | Çevrimdışı kuyruk ve bağlantı gelince eşitleme | draft |
| 5 | handoff | Bir cihazdan diğerine teslim | draft |
| 6 | multi-room | Çok odada çalma senkronu | draft |
| 7 | network-testing | Ağ testleri (gecikme/kopma/simülasyon) | draft |

> comms torunları **kademe-3 (bounded-context)** türündedir: ayrı
> K014-network/comms/*/index.md dosyaları vardır; module'e ininmez.

## 3. Bağımlılık özeti (tam metin: network-bagimliliklar.md)

| Yön | Düğüm | Ne için (TASARIM İDDİASI) |
|---|---|---|
| depends_on → | K001-donanım | Ağ donanımı/sürücü (bağlantının fiziksel tarafı) |
| depends_on → | K009-security | TLS/şifreleme ve güvenli oturum kuralları |
| depends_on → | K010-identitiy | Cihaz kimliği ile yetkilendirme |
| ← used_by | K011-application | Arayüzün ağ çağrıları |
| ← used_by | K012-services | Servisler arası taşıma |
| ← used_by | K013-integraion | Dış API taşıması |
| ← used_by | K015-media | Akış/indirme taşıması |

## 4. Durum & açık sorular

- **Durum:** draft — kademe-3/4 genişletmesi ADR-097 K4 fazı (C) bekliyor.
- Ayrıntı: network-bagimliliklar.md (§1–§5) · network-dogrulama.md (§1–§6).
- Kısa liste:
  1. handoff ↔ multi-room sınırı (teslim mi, senkron mu) → ⚠️ VERIFICATION REQUIRED
  2. offline-sync ↔ K015/offline ↔ K013/synchronization üçlüsü → ⚠️ VERIFICATION REQUIRED
  3. service-discovery kapsamı → ⚠️ VERIFICATION REQUIRED
  4. WebRTC standardı kanıtı (IETF) → ⚠️ URL yok, tur-2

**Kaynak:** v2 §2.14 · .ai/architecture/index.md §5/14 · ADR-097 §5


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `network-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `network-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `network-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
