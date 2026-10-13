---
node_id: K014-network/comms
node_type: subdomain
status: draft
parent_id: K014-network
children:
  - http
  - websocket
  - webrtc
  - tcp-ip
  - device-communication
depends_on: []
evidence:
  - "v2 §2.14 — comms/ altındaki 5 torun adı birebir (http · websocket · webrtc · tcp-ip · device-communication)"
  - "WebRTC = IETF standardı — 2026-10-12 beyanı (URL yok ⚠️)"
validation:
  - "Torun adları v2 §2.14 ile birebir karşılaştırıldı (5/5) — 2026-10-12"
open_questions:
  - "Her torunun kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "device-communication hangi yerel protokolleri kullanır → ⚠️ UNKNOWN"
  - "http ve websocket için kütüphane/dil → ⚠️ UNKNOWN (kanıt yok)"
group: mimari
---


# comms — K014-network (kademe-2 · subdomain)

**ELI15:** İletişimin temel protokol ailesi: internet konuşması (http), canlı çift
yönlü bağlantı (websocket), doğrudan medya yolu (webrtc), yerel ağ taşıması
(tcp-ip) ve evdeki cihazlar arası konuşma (device-communication).

**Sınır (ne DEĞİL):** Donanım/sürücü K001-donanım · şifreleme K009-security ·
protokolün taşıdığı iş anlamı K012/K013 · medya içeriği K015-media.

## Kademe-3 torun tablosu (v2 birebir)

| # | Torun (bounded-context) | ELI15 rol (TASARIM İDDİASI) | Durum |
|---|---|---|---|
| 1 | http | İstek/yanıt üzerinden internet konuşması | draft |
| 2 | websocket | Sürekli çift yönlü canlı bağlantı | draft |
| 3 | webrtc | Doğrudan medya/veri yolu (standard iddiası ⚠️) | draft |
| 4 | tcp-ip | Yerel ağ düzeyi taşıma | draft |
| 5 | device-communication | Evdeki cihazlar arası konuşma | draft |

**Bağımlılık (özet):** network-bagimliliklar.md §1 · torun ayrıntısı kendi index'lerinde.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
