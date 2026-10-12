---
node_id: K007-data
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - schema
  - migrations
  - repositories
  - transactions
  - indexing
  - cache
  - search
  - synchronization
  - backup
  - data-quality
depends_on:
  - K008-domain
  - K009-security
  - K000-isletim-sistemi
evidence:
  - "v2 §2.7 — coremusic-katmanli-mimari-agaci-v2.md satır 270–288"
  - ".ai/architecture/index.md §5 satır 7 (master = data-master.md)"
  - "https://github.com/koel/koel (2026-10-12 · 17175★ · MIT · Laravel)"
  - "https://github.com/ampache/ (2026-10-12 · 3793★ · AGPL-3.0)"
validation:
  - "2026-10-12 — children listesi v2 mini-ağaçı ile karşılaştırıldı: 10/10 birebir"
  - "2026-10-12 — Koel ve Ampache depoları açıldı, star + lisans + framework doğrulandı"
open_questions:
  - "CoreMusic K007'nin kendi DBMS/ORM seçimi → UNKNOWN (manifest yalnız referans verdi)"
  - "depends_on satırları TASARIM İDDİASIdır → -bagimliliklar.md §6"
  - "Çocukların rol tanımları v2'de yok → ⚠️ VERIFICATION REQUIRED (ADR-097 K4)"
---

# K007-data — Domain Künyesi

## ELI15 rol (v2 §2.7 — birebir)

> **Veritabanı — şarkılar, kullanıcılar, listeler, yedekler.**

## Çocuk kataloğu (kademe-2)

v2 §2.7 bu çocuklara **yalnızca ad verir**; hiçbiri için rol tanımı yok → hepsi `⚠️ VERIFICATION REQUIRED».

| # | Çocuk | v2 satır | Durum |
|---|---|---|---|
| 1 | schema | 278 | draft |
| 2 | migrations | 279 | draft |
| 3 | repositories | 280 | draft |
| 4 | transactions | 281 | draft |
| 5 | indexing | 282 | draft |
| 6 | cache | 283 | draft |
| 7 | search | 284 | draft |
| 8 | synchronization | 285 | draft |
| 9 | backup | 286 | draft |
| 10 | data-quality | 287 | draft |

Her çocuğun kendi `index.md»'si bu klasörde vardır (kademe-2 künyesi, `children: []»).

## Dosyalar (çoklu-MD şeması — ADR-097 K2)

| Dosya | Rol |
|---|---|
| `index.md» | bu künye (8 zorunlu alan) |
| `data-master.md» | anayasa — v2'deki ad, dokunulmaz |
| `data-bagimliliklar.md» | depends_on / used_by / entegrasyon sınırı |
| `data-dogrulama.md» | ölçütler · açık sorular · web-kanıt kayıtları |

## Durum

| Alan | Değer |
|---|---|
| status | draft (registry §5 satır 7) |
| Web kanıtı | ✅ var — Koel + Ampache (master §4, -dogrulama §4) |
| Teknoloji seçimi (kendi yığınımız) | UNKNOWN — manifest referans verdi, seçim vermedi |
| Açık sorular | 3 · master §7 ve -dogrulama §3 |

> **Etiket notu:** frontmatter `depends_on» satırları **TASARIM İDDİASI**dır — `verified» değildir.
