---
node_id: K008-domain
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - music
  - media
  - user
  - library
  - playlist
  - playback
  - discovery
  - recommendation
  - device-management
  - context-mapping
depends_on: []
evidence:
  - "v2 §2.8 — coremusic-katmanli-mimari-agaci-v2.md satır 290–308"
  - ".ai/architecture/index.md §5 satır 8 (master = domain-master.md)"
  - "https://github.com/koel/koel (2026-10-12 · 17175★ · MIT · Laravel)"
  - "https://github.com/ampache/ (2026-10-12 · 3793★ · AGPL-3.0)"
validation:
  - "2026-10-12 — children listesi v2 mini-ağaçı ile karşılaştırıldı: 10/10 birebir"
  - "2026-10-12 — Koel ve Ampache depoları açıldı (K007 ile ortak kanıt)"
open_questions:
  - "depends_on BİLİNÇLİ BOŞ: domain dışa bağımlı olmaz ilkesi (TASARIM İDDİASI) → -bagimliliklar.md §2"
  - "recommendation/discovery sahipliği: K006 · K020-recomenataion · K020-discovery ile örtüşme → ⚠️ VERIFICATION REQUIRED"
  - "Çocukların rol tanımları v2'de yok → ⚠️ VERIFICATION REQUIRED (ADR-097 K4)"
---

# K008-domain — Domain Künyesi

## ELI15 rol (v2 §2.8 — birebir)

> **İşin kuralları — "çal", "listeye ekle", "beğen" gibi kelimelerin anlamı.**

## Çocuk kataloğu (kademe-2)

v2 §2.8 bu çocuklara **yalnızca ad verir**; hiçbiri için rol tanımı yok → hepsi `⚠️ VERIFICATION REQUIRED».

| # | Çocuk | v2 satır | Durum |
|---|---|---|---|
| 1 | music | 298 | draft |
| 2 | media | 299 | draft |
| 3 | user | 300 | draft |
| 4 | library | 301 | draft |
| 5 | playlist | 302 | draft |
| 6 | playback | 303 | draft |
| 7 | discovery | 304 | draft |
| 8 | recommendation | 305 | draft |
| 9 | device-management | 306 | draft |
| 10 | context-mapping | 307 | draft |

Her çocuğun kendi `index.md»'si bu klasörde vardır (kademe-2 künyesi, `children: []»).

## Dosyalar (çoklu-MD şeması — ADR-097 K2)

| Dosya | Rol |
|---|---|
| `index.md» | bu künye (8 zorunlu alan) |
| `domain-master.md» | anayasa — v2'deki ad, dokunulmaz |
| `domain-bagimliliklar.md» | depends_on / used_by / entegrasyon sınırı |
| `domain-dogrulama.md» | ölçütler · açık sorular · web-kanıt kayıtları |

## Durum

| Alan | Değer |
|---|---|
| status | draft (registry §5 satır 8) |
| Web kanıtı | ✅ var — Koel + Ampache (master §4, -dogrulama §4) |
| depends_on | BİLİNÇLİ BOŞ (domain dışa bağımlı olmaz — TASARIM İDDİASI) |
| Açık sorular | 3 · master §7 ve -dogrulama §3 |

> **Etiket notu:** `depends_on: []» bir **idjadır** (bilinçli boşluk), kanıtlanmış bağımsızlık değildir.
