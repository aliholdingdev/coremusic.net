---
title: "CoreMusic — ADR-114: Repository Pattern Standardı — Yeni Kod İçin Kural (Kod Donduruldu)"
type: adr
category: architecture
date: 2026-11-01
status: draft
authority: ADR-114 Karar Metni (SSOT)
related: "ADR-002 (raw PDO prepared statement · SELECT * yasağı) · AGENTS §5 Domain Boundaries · .ai/.rules/karar-kurallari.md §3"
---

# ADR-114: Repository Pattern Standardı — Yeni Kod İçin Kural (Kod Donduruldu)

**Durum:** draft — kullanıcı kararı ALINDI (2026-11-01, seçenek A; refactor DONDU — yalnız kural yazıldı)
**Karar Veren:** Bayram Ali
**Bulgu kaynağı:** karar turu #3 — "repository pattern standart hangi taban, mevcut kod'a dokunulacak mı" (2026-11-01)

## 1. Bağlam

Repository pattern sistem genelinde **zaten uygulanmış** — dosya envanteri (2026-11-01: PowerShell `Get-ChildItem -Recurse -Filter *.php`, `\Repository\` dizinleri + dosya-adı `*Repository*` eşleşmesi, `vendor` hariç; **29 dosya**):

| Yüzey | Concrete repo | Interface | Test/fixture |
|---|---|---|---|
| `api` | 6 (`Billing/History/Music/Notification/Playlist/UserProfileRepository`) | 6 (`I*`) | — |
| `home` | 2 (`Music`, `Playlist`) | — | 1 (`HomeRepositoryRegistryTest`) |
| `auth` | 1 (`UserRepository`) | — | 1 (`UserRepositoryTest`) |
| `admin` | 1 (`AdminUserRepository`) | — | — |
| `media` | 2 (`MySqlCatalogRepository`, `MySqlMediaAccessRepository`) | 1 (`MediaAccessRepositoryInterface`) | 1 (`InMemoryMediaAccessRepository` fake) |
| `shared` | 2 (`ApiKeyRepository`, `OAuthRepository`) | 1 (`IUserRepository` — Contracts/Auth) | 4 (`ApiKeyRepositoryTest`, `ApiKeyCreateCommandTest`, `Sqlite/SqliteDatabaseManager+SqliteDatabaseRegistry`) |

**Aritmetik kanıt:** 14 concrete + 8 interface + 7 test/fixture = 29 dosya.

Taban şablon kanıtı (api, 2026-11-01 okundu):

- `api.coremusic.net/include/Repository/MusicRepository.php:15` — `final class MusicRepository implements IMusicRepository`
- `:19-21` — `IDatabaseRegistry` constructor injection (lazy registry, DB ayrımı `DB_KEY`)
- `:29-43` — explicit kolonlar + `UNHEX(:id)` parametre; `is_deleted = 0` soft-delete filtresi
- `api.coremusic.net/include/Repository/IMusicRepository.php:8-11` — tip sözleşmeli method seti; header notu: "ADR-002: PDO prepared statement yalnız · SELECT * yasak"

Kullanıcı kararı: taban şablon **api yüzeyi**; kural yalnız yeni kodu bağlar — mevcut repo dosyaları BUGÜN dokunulmaz, refactor ayrı onaylı görev (sonraki iş).

## 2. Karar

**Her yeni repository bu şablona uyar; mevcut kod dondurulur:**

1. Yeni repo bileşenleri: (a) `I*` interface — tip sözleşmeli method seti, (b) `final class` concrete implements, (c) `IDatabaseRegistry`/PDO constructor injection, (d) raw PDO prepared statement + explicit kolon (SELECT * yasak — ADR-002), (e) soft-delete kolonlu tablolarda `is_deleted = 0` filtresi (dump sözleşme satır referanslı — ADR-112 kural 4).
2. Taban şablon referans dosyası: `api.coremusic.net/include/Repository/` (interface + concrete ayrı dosya).
3. Mevcut repository dosyaları (§1 envanteri, 29 dosya) dokunulmaz; repository refactor ayrı onaylı görev olarak planlanır.
4. Subdomain'lerin diğer subdomain'in repository sınıfını IMPORT etmemesi korunur (AGENTS §5/§6: shared + sözleşme interfaceleri).
5. Yeni repo için en az bir birim test dosyası eşlenir (mevcut örnek: `shared/tests/Repository/ApiKeyRepositoryTest.php`).

## 3. Gerekçe

| Seçenek | Sonuç | Değerlendirme |
|---|---|---|
| **A (kabul): api taban, yalnız kural; kod dondurulur** | tek şablon, sıfır davranış değişikliği | api en zengin örnek (6 concrete + 6 interface); standardizasyon yeni kodda otomatik yakalanır |
| B: media-first taban | aktif yüzey (ADR-112 işiyle çakışır) | seti api'den küçük (2 concrete + 1 interface + 1 fake) — taban örneği için daha dar |
| C: tam refactor şimdi | 22 bileşen dosyası dokunuşu (14 concrete + 8 interface) | davranış riski + kapsam; ertelendi (kullanıcı kararı) |

## 4. Sonuçlar

- Yeni repo standardının diskte canlı kalıcı örneği sabitlendi (MusicRepository + IMusicRepository).
- Davranış değişikliği yok; `composer test` kapısı etkilenmez.
- Mevcut repo'lar arasında şablon dışı yapılar varsa kabul edilmiş teknik borç sayılır (⚠️ VERIFICATION REQUIRED — bu ADR repo sınıfları arasında satır-madde/düzeyinde şablon farkı analizi yapmadı); giderilmesi ayrı görevin konusudur.

## 5. Gözden Geçirme Tetikleyicileri

1. Yeni subdomain / yeni tablo seti geldiğinde (kural birebir uygulanır).
2. `IDatabaseRegistry` sözleşmesi değişirse (tüm repo'ları etkileyen shared contract revizyonu — ayrı ADR).
3. Repository refactor gündeme geldiğinde (kapanmış köşe: bu ADR'den muaf; yeni ADR gerekir).

## 6. Draft → Accepted Geçiş Şartı (tümü zorunlu)

- [ ] `.ai/.decisions/index.md`'ye draft kayıt satırı eklendi (kapanış paketiyle birlikte)
- [ ] `.ai/.rules/karar-kurallari.md` §3 ve `.ai/.rules/kurallar.md` özeti güncellendi (bu oturumda yapılıyor)
- [ ] (Öneri) Taban şablon örnekleri (MusicRepository + IMusicRepository) brain.md özetinde işlenen

## 7. Doğrulama Kanıtları

- Dosya envanteri (PowerShell ölçüm, 2026-11-01): **29 php dosyası** = 14 concrete + 8 interface + 7 test/fixture (§1 tablosu dağılımı)
- `MusicRepository.php:15, 19-21, 29-43` · `IMusicRepository.php:8-11` (2026-11-01 okundu)
- Kural türevi: `.ai/.rules/karar-kurallari.md` §3
