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
  - "WebRTC = IETF standardı — 2026-10-12 beyanı; tur-3'te W3C TR/webrtc + RFC 8825/8826 ile kanıtlandı (S1, 2026-10-15)"
  - "tur-3: 5 toruna *-standart-kanit.md + *-kod-ornekleri.md eklendi (10 dosya) + aile seviyesi comms-standart-kanit.md / comms-kod-ornekleri.md (2 dosya) — toplam 12 (2026-10-15)"
validation:
  - "Torun adları v2 §2.14 ile birebir karşılaştırıldı (5/5) — 2026-10-12"
  - "tur-3 URL doğrulama: torun başı S1 kanıt dosyaları — network-dogrulama.md §30.3 (2026-10-15)"
open_questions:
  - "Her torunun kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "device-communication hangi yerel protokolleri kullanır → ⚠️ UNKNOWN"
  - "http ve websocket için kütüphane/dil seçimi → adaylar *-standart-kanit.md'de S1 kanıtlandı, SEÇİM faz C'de açık"
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
| 3 | webrtc | Doğrudan medya/veri yolu (standard kanıtlı — S1, 2026-10-15) | draft |
| 4 | tcp-ip | Yerel ağ düzeyi taşıma | draft |
| 5 | device-communication | Evdeki cihazlar arası konuşma | draft |

**Bağımlılık (özet):** network-bagimliliklar.md §1 · torun ayrıntısı kendi index'lerinde.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `comms-standart-kanit.md` | Aile seviyesi çapraz-okuma (rollup) — 5 torun kanıt dosyasına referans, yeni iddia üretmez (tur-3, 2026-10-15) | Kademe-2 |
| kod | `comms-kod-ornekleri.md` | 7 dilde aile Transport-seam örneği (TASARIM ÖRNEĞİ · §8 dil matrisi — tur-3, 2026-10-15) | Kademe-2 |
| kanit | `*/​*-standart-kanit.md` (5 dosya: http · websocket · webrtc · tcp-ip · device-communication) | Per-torun S1 standart/URL kanıt matrisi (tur-3, 2026-10-15) — satır listesi her torunun `index.md` §GRUPLAR'ında | Kademe-3 |
| kod | `*/​*-kod-ornekleri.md` (5 dosya) | 7 dil TASARIM ÖRNEĞİ + §8 dil matrisi (tur-3, 2026-10-15) — satır listesi her torunun `index.md` §GRUPLAR'ında | Kademe-3 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
