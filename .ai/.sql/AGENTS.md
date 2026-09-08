# CoreMusic — .sql Agent Talimatlari

**Zorunlu Baglantilar:** [[../AGENTS.md]] · [[../CLAUDE.md]]

## 1. Amac

.sql klasoru vault yapisal bilesenidir. Bolum basliklari ve mevcut dosya organizasyonu korunur.

## 2. Envanter

- `mysql/` — 18 BCNF veritabani dump dosyalari (canli otorite)
- `mssql/`, `postgresql/`, `sqlite/` — rezerve, her birinde konvansiyonel README (aktif plan YOK)

## 3. Kurallar

1. In-place duzenleme; dosya adi/konumu onaysiz degismez
2. Bu klasore uydurma icerik yazilmaz (ADR-005)
3. Degisiklikler [[../log.md]]'ye kaydedilir
4. Mikro rezerve alt klasorler (mssql/postgresql/sqlite) icin ayri AGENTS/CLAUDE uretilmez; README yeterlidir
