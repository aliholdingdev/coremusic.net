# SQLite — Rezerve Klasör

> Durum (2026-10-22): Vault Steward onayıyla ilk içerik eklendi.

- Ana otorite: MySQL 9, 18 BCNF (ADR-003 + ADR-040)
- `novasearch.sql` (2026-10-22): nova-search-engine **kod şeması** — SQLite dialekt,
  3 tablo (channels / videos / search_log) + 4 indeks. Kaynak (tek doğruluk):
  `app-nodejs-projects/nova-search-engine/packages/database/src/schema.ts` (`ddl()` + `INDEXES`).
  MySQL ikizi: `../mysql/novasearch.sql`. DROP yok (idempotent, veri silmez).
  Doğrulama: kod `migrate()` ile yapısal eşitlik + üretim repo round-trip = PASS (2026-10-22).
- Genel SQLite gomülü/offline planı: YOK (VERIFICATION REQUIRED)
- İçerik eklemek için Vault Steward onayi gerekir
