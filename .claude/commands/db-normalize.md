# DB Normalize

Veritabanı semasını normalize et, BCNF kontrolü yap, migration oluştur.

## Normalizasyon Seviyeleri

| Seviye | Kural | Kontrol |
|--------|-------|---------|
| 1NF | Her hücre tek değer | Atomic values |
| 2NF | Partial dependency yok | Full functional dependency |
| 3NF | Transitive dependency yok | Non-key → non-key bağlantısı |
| BCNF | Her determinant candidate key | Determinant → candidate key |

## Analiz Adımları

```
ADIM 1: Sema Analizi
  → Tabloları listele
  → Kolon türlerini kontrol et
  → Primary key'leri belirle
  → Foreign key'leri çıkar

ADIM 2: Fonksiyonel Bağımlılık
  → X → Y bağımlılıklarını bul
  → Determinant'ları belirle
  → Candidate key'leri hesapla

ADIM 3: BCNF Kontrolü
  → Her determinant candidate key mi?
  → İhlal varsa ayır
  → Yeni tablo oluştur

ADIM 4: Migration Oluştur
  → ALTER TABLE / CREATE TABLE yaz
  → Veri taşıma planı hazırla
  → Rollback senaryosu yaz

ADIM 5: Doğrulama
  → Yeni semayı test et
  → BCNF uyumluluğunu doğrula
  → Performance testi yap
```

## 9 BCNF Veritabanı (ADR-040)

| # | Veritabanı | Amaç |
|---|------------|------|
| 1 | coremusic_auth | Users, roles, sessions, Argon2id |
| 2 | coremusic_user | Profiles, preferences, history |
| 3 | coremusic_musics | Songs, artists, genres, metadata |
| 4 | coremusic_albums | Album collections |
| 5 | coremusic_playlist | User and AI playlists |
| 6 | coremusic_catalog | Download queues, service status |
| 7 | coremusic_logs | Application logs, audit trail |
| 8 | coremusic_media | Media file metadata |
| 9 | coremusic_system | System configuration |

## Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| ORM (Eloquent, Doctrine) | Raw PDO |
| `SELECT *` | Explicit columns |
| Hardcoded secret | `.env` / credential vault |
| Soft delete yok | `is_deleted = 0` zorunlu |
