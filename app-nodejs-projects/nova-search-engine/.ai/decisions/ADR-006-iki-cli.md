# ADR-006 — İki CLI (Parametrik + Menü Sihirbazı)

- **Durum:** Kabul
- **Tarih:** 2026-10-16
- **Alan:** Kullanıcı arayüzü (CLI)

## Bağlam

Teknik kullanıcı `nova-search search "x" -ai -r 100` ister; acemi kullanıcı
menü ister. Kullanıcı kararı: **iki CLI da olacak**.

## Karar

1. **`nova-search`** — parametrik (commander 12): `search`, `collect`, `stats`
2. **`nova-search-wizard`** — etkileşimli menü (readline, yeni bağımlılık yok)

Kritik kural: **tek iş mantığı.** Her iki CLI da `packages/cli/src/run.ts`
üzerinden `buildQuery → engine.search` çağırır; menü yalnız farklı giriş
biçimidir.

## Gerekçe

- DRY: iş mantığı tek kaynakta; hata yüzeyi küçük
- Bayrak kısaltmaları kullanıcının belirttiği sözlüğe uyar (`-ai`, `-eq`,
  `-minv`, `-maxv`, `-mins`, `-qa`, `-udate`, `-sb`) — argv normalizasyonu ile

## Sonuçlar

- Menü yeni özellik isterse yalnız `run.ts`'e yeni çağrı eklenir
- Bayrak seti değişirse iki CLI otomatik senkron kalır
