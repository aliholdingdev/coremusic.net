# CoreMusic — .sql Baglam

**Zorunlu Baglantilar:** [[../CLAUDE.md]] · [[./AGENTS.md]]

## 1. Baglam

SQL dump klasoru: mysql altinda 18 BCNF veritabani dump dosyalari + destek dosyalari (20 dosya). Otorite: ADR-003 + ADR-040. mssql/postgresql/sqlite alt klasorleri rezerve — aktif plan yok.

## 2. Protokol

- Boot protocol uygulanir ([[../CLAUDE.md]] §16)
- Degisiklikler [[../log.md]] audit izine yazilir
