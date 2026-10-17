---
node_id: K010-network
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
  - K0100-identitiy
evidence:
  - "v2 §2.14 K010-network/ — çocuk listesi kaynağı (7 çocuk, birebir; comms'un 5 torunu da v2'de)"
  - ".ai/architecture/index.md §5/14 — network-master.md adı kaydı"
  - "WebRTC = IETF standardı (teknik terim) — 2026-10-12 beyanı; tur-3'te W3C TR/webrtc + RFC 8825/8826 ile kanıtlandı (network-dogrulama.md §30.5, S1)"
  - "tur-3 (2026-10-15): 12 klasör × (*-standart-kanit.md + *-kod-ornekleri.md) = 24 yeni dosya; 58 benzersiz S1 URL (network-dogrulama.md §30.2)"
validation:
  - "Çocuk adları v2 §2.14 ile birebir karşılaştırıldı (7/7 + comms 5/5) — 2026-10-12"
  - "Web kanıtları network-dogrulama.md §3'e işlendi — 2026-10-12"
  - "tur-3 URL doğrulama: 58 S1 / 5 ölü URL elendi (başarıyla doğrulanmadan hiçbir URL kaydedilmedi) — network-dogrulama.md §30 (2026-10-15)"
open_questions:
  - "handoff ile multi-room arasındaki sınır (teslim mi, senkron mu) → ⚠️ VERIFICATION REQUIRED"
  - "offline-sync (K014) ile K015/offline ve K013/synchronization sınırı → ⚠️ VERIFICATION REQUIRED"
  - "service-discovery kapsamı (yerel cihaz keşfi mi, servis kaydı mı) → ⚠️ VERIFICATION REQUIRED"
  - "comms torunlarının kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
group: mimari
---


# K010-network — Bağlantı Katmanı

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
> K010-network/comms/*/index.md dosyaları vardır; module'e ininmez.

## 3. Bağımlılık özeti (tam metin: network-bagimliliklar.md)

| Yön | Düğüm | Ne için (TASARIM İDDİASI) |
|---|---|---|
| depends_on → | K001-donanım | Ağ donanımı/sürücü (bağlantının fiziksel tarafı) |
| depends_on → | K009-security | TLS/şifreleme ve güvenli oturum kuralları |
| depends_on → | K0100-identitiy | Cihaz kimliği ile yetkilendirme |
| ← used_by | K011-application | Arayüzün ağ çağrıları |
| ← used_by | K012-services | Servisler arası taşıma |
| ← used_by | K013-integraion | Dış API taşıması |
| ← used_by | K014-media | Akış/indirme taşıması |

## 4. Durum & açık sorular

- **Durum:** draft — kademe-3/4 genişletmesi ADR-097 K4 fazı (C) bekliyor.
- Ayrıntı: network-bagimliliklar.md (§1–§5) · network-dogrulama.md (§1–§32) · network-master.md (§4/§6/§7/§15).
- **tur-3 (2026-10-15):** her 12 klasöre `*-standart-kanit.md` (S1 URL matrisi) + `*-kod-ornekleri.md` (7 dil TASARIM ÖRNEĞİ) eklendi — 24 dosya; per-klasör satırlar kendi index.md §GRUPLAR'ında.
- Kısa liste:
  1. handoff ↔ multi-room sınırı (teslim mi, senkron mu) → ⚠️ VERIFICATION REQUIRED
  2. offline-sync ↔ K015/offline ↔ K013/synchronization üçlüsü → ⚠️ VERIFICATION REQUIRED
  3. service-discovery kapsamı → ⚠️ VERIFICATION REQUIRED
  4. WebRTC standardı kanıtı (IETF) → ✅ kapandı (tur-3): W3C https://www.w3.org/TR/webrtc/ + RFC 8825/8826 (S1, §30.5); kalan açık: sürüm sahipliği/bölünme kararı (faz C)

**Kaynak:** v2 §2.14 · .ai/architecture/index.md §5/14 · ADR-097 §5


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `network-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `network-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi (§30–§32: tur-3) | Kademe-1 |
| mimari | `network-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| kanit | `*/​*-standart-kanit.md` (12 dosya — 5 comms + 7 kademe-2) | Per-klasör S1 standart/URL kanıt matrisi (tur-3, 2026-10-15) — satır listesi her klasörün `index.md` §GRUPLAR'ında | Kademe-2/3 |
| kod | `*/​*-kod-ornekleri.md` (12 dosya — 5 comms + 7 kademe-2) | 7 dil TASARIM ÖRNEĞİ + §8 dil matrisi (tur-3, 2026-10-15) — satır listesi her klasörün `index.md` §GRUPLAR'ında | Kademe-2/3 |

> tur-3 notu: 24 yeni dosyanın tamamı `group: kanit` / `group: kod` frontmatter'lıdır;
> birebir satırlar ilgili klasör `index.md` §GRUPLAR tablolarındadır (kök tablo
> tekrarı önlemek için grup satırı olarak özetlenmiştir).

## §BAĞLANTILAR

> 2026-10-17 · orkestratör · kaynak: K000–K013 arasi gercek dosya taramasi (ad gecen K eslesmesi) + v2 S4.2 depends_on · iliskiler dogrulanmadir, kesin sozlesme degildir.

| Hedef K | Wiki-link | Göreli dosya yolu | İlişki |
|---|---|---|---|
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | akis protokolleri (RTP/DLNA) |
| K009-security | [[K009-security]] | ../K009-security/index.md | TLS ve zaman sinirlama |
| K011-application | [[K011-application]] | ../K011-application/index.md | ag uygulama katmani |
| K012-services | [[K012-services]] | ../K012-services/index.md | servis kesfi ve load balancing |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | gateway ve protokoller |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
