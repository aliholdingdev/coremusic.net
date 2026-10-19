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
updated: 2026-10-18
---


# K010-network — Bağlantı Katmanı

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K010-network |
| **K Adı** | Ağ / Bağlantı (Network) |
| **Kapsam** | Bağlantı kablolarının yazılımı — internet, evdeki cihazlar arası iletişim. Paketin nereye gideceği ve nasıl gideceği burada bilinir: HTTP, WebSocket, WebRTC, TCP/IP ve device-communication (comms/ altında 5 torun). Bağlantının kendisi donanım işidir (K001-donanım), güvenliği K009'dur. 7 kademe-2 çocuk. |
| **Kritik Bağımlılıklar** | K001-donanım · K009-security (kanıt: FM depends_on — FM'deki K0100-identitiy kaydının diske karşılığı YOK ⚠️) |
| **Durum** | draft (7 kademe-2 çocuk · tur-3 2026-10-15 · 58 S1 URL) |
| **Son Güncelleme** | 2026-10-15 (tur-3: 12 klasör × standart-kanıt + kod-ornekleri = 24 yeni dosya) |

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
## §GRUPLAR

> İki-ekseni gruplama (registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**. `Kademe` ataması şeffaf rol kuralıdır (index/master/kapsam → Kademe-1; destek → Kademe-2) — diskte `kademe` alanı yok, uydurma yok.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Bağlantı katmanı künyesi | Kademe-1 |
| mimari | `network-master.md` | Network master | Kademe-1 |
| mimari | `network-kapsam.md` | Kapsam (kademe-1) | Kademe-1 |
| mimari | `network-bagimliliklar.md` | Bağımlılıklar | Kademe-2 |
| kanit | `network-web-arastirma.md` | Doğrulanmış kaynaklar | Kademe-2 |
| kanit | `network-dogrulama.md` | Doğrulama | Kademe-2 |


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

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K010-network | [[K010-network]] | ./K010-network/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | → | bağımlı (depends_on) | M→20 · M←0 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ← | karşı taraf referanslıyor | M→0 · M←1 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | ← | karşı taraf referanslıyor | M→0 · M←1 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | ↔ | bağımlı (depends_on) | M→20 · M←12 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | → | bu K referanslıyor | M→9 · M←0 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | ↔ | iki yönlü güçlü referans | M→12 · M←15 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ↔ | iki yönlü güçlü referans | M→14 · M←10 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | ↔ | iki yönlü güçlü referans | M→17 · M←24 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ← | karşı taraf referanslıyor | M→0 · M←8 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |

