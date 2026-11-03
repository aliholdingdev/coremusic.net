---
title: "CoreMusic — Veritabanı Kuralları (MySQL 9 · 18 BCNF · Migration)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-11-01
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §18 (ADR-040) · .ai/.rules/kurallar.md · .ai/.rules/karar-kurallari.md (ADR-112)"
docType: claude
---

# CoreMusic — Veritabanı Kuralları (Database Rules)

**docType:** claude · **Klasör:** `.ai/.rules/` · **Sorumlu:** data-engineer

**Zorunlu Bağlantılar:** [[RULES.md]] · [[kurallar.md]] · [[karar-kurallari.md]] · [[backend-rules.md]] · [[security-rules.md]] · [[.ai/CLAUDE.md]]

> **eli10:** 18 BCNF veritabanı SSOT'u SQL dump'larıdır; canlı DB ile dump arasındaki farklar kod esnetilerek değil, onaylı migration ile kapatılır; ORM ve SELECT * yasaktır.

---

## 1. Amaç

CoreMusic MySQL 9 veritabanı standartlarını; 18 BCNF şema envanterini, migration disiplinini (ADR-112), veri erişim kurallarını ve şema sözleşmelerini vault SSOT'larından toplar. Data kalite hedefi: **%100 BCNF, sıfır ORM, sıfır SELECT \*, onaylı migration** (`.ai/AGENTS.md` §16).

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| MySQL 9 şemaları (18 BCNF + ek 2 DB) ve 156 tablo | Backend controller/use-case → [[backend-rules.md]] |
| `.ai/.sql/mysql/*.sql` sözleşme dump'ları | Secret ve şifreleme yönetimi → [[security-rules.md]] |
| Migration paketleri (`shared/database/migrations/`) | Cache ve APCu katmanı → [[architecture-rules.md]] |
| BCNF audit / normalizasyon / index planlaması | UI ve frontend kuralları → [[frontend-rules.md]] |

---

## 3. Mimari

### 3.1 18 BCNF Veritabanı Serisi (156 Tablo — ADR-040)

| # | Veritabanı Adı | Sorumluluk Alanı / İçerik |
|---|---|---|
| 1 | `coremusic_auth` | Kullanıcılar, roller, oturumlar, tokenler, credential vault, API anahtarları |
| 2 | `coremusic_user` | Profiller, kullanıcı tercihleri, geçmiş, favoriler, takipçiler |
| 3 | `coremusic_musics` | Şarkılar, sanatçılar, türler, şarkı sözleri, podcast'ler, radyo istasyonları |
| 4 | `coremusic_albums` | Albüm koleksiyonları, diskler, yayın yılları, istatistikler |
| 5 | `coremusic_playlist` | Kullanıcı ve AI çalma listeleri, listeler arası iş birlikleri |
| 6 | `coremusic_catalog` | Referans katalog verileri (enstrümanlar, modlar, global etiketler) |
| 7 | `coremusic_logs` | Uygulama logları, audit izleri, telemetri, performans metrikleri |
| 8 | `coremusic_media` | Medya dosyaları, erişim hakları (`media_access`), audit kayıtları (`media_audit`) |
| 9 | `coremusic_system` | Sistem ayarları, konfigürasyon, EQ presetleri, bildirimler, i18n |
| 10 | `coremusic_social` | Yorumlar, paylaşımlar, sosyal etkileşim, dinleme odaları |
| 11 | `coremusic_wireless` | WiFi ve Bluetooth ağ bağlantıları, eşleştirilmiş cihazlar |
| 12 | `coremusic_ai` | AI tercih modelleri, dinleme karakteristiği vektörleri, öneriler |
| 13 | `coremusic_api` | Harici API anahtarları, rate limit sayaçları, webhook tanımları |
| 14 | `coremusic_cms` | Sayfalar, blog yazıları, etiketler, statik medya varlıkları, SSS |
| 15 | `coremusic_download` | İndirme kuyruğu, indirme geçmişi, önbellek dosyaları |
| 16 | `coremusic_neva` | Neva Engine DSP ayarları, routing matrisi, spektrum profilleri |
| 17 | `coremusic_studio` | Stüdyo kayıt oturumları, kanallar, DAW presetleri, ekipmanlar |
| 18 | `coremusic_patch` | Şema sürümleri, migration geçmişi, uygulanan yamalar |

### 3.2 Ek Sistemler (18'in Dışındaki Bağımsız Veritabanları)

| Veritabanı | Tablo | Canlı Durum / Not |
|---|:---:|---|
| `media_catalog` | 9 | `.sql` dump dosyası mevcuttur; henüz canlı ortama **deploy edilmemiştir** (PLANNED). |
| `novasearch` | 7 | Canlıda aktiftir; **knex** ile yönetilir (ADR-014 runner istisnası). Dump: `.ai/.sql/mysql/novasearch.sql`. |

*Not: Toplam `.sql/mysql/` dosya sayısı 20 adettir (18 seri + 2 ek). Ek sistemler 18 BCNF serisine dahil edilerek sayı uydurulmaz.*

### 3.3 Şema Sözleşme Hizalama & Migration Standardı (ADR-112)

1. **Sözleşme SSOT'u:** `.ai/.sql/mysql/*.sql` dosyaları mutlak şema sözleşmesidir. Canlı veritabanı bu sözleşmeye **yalnızca migration ile** hizalanır (`HL-08`).
2. **Şema Drift'inde Kod Esnetilemez:** Canlıda bir kolon eksik veya farklı diye backend kodunda fallback/sessiz try-catch yazılamaz. Sistem fail-closed davranır; drift raporlanır ve migration ile kapatılır.
3. **Migration Paketi Standardı:**
   - İleri yönlü yama (`*.up.sql`) ve geri alma yaması (`*.down.sql`) ayrı dosyalar halinde hazırlanır.
   - İdempotent pre-check (`information_schema.COLUMNS`) içermelidir.
   - Canlıda çalıştırmadan önce `mysqldump --single-transaction` yedeği alınır.
   - Çalıştırma işlemi KESİNLİKLE kullanıcı onayı ile yürütülür (`KURAL-07`).
4. **Soft Delete Standardı:** Silme kontrolleri sözleşmedeki kolon referansıyla yazılır (`WHERE is_deleted = 0`). Örn. `coremusic_media.sql:167-168, 193-194`.
5. **Veri Erişim Kısıtlamaları:**
   - **ORM Kesinlikle Yasaktır (`HG-09`):** Yalnızca raw PDO kullanılır.
   - **`SELECT *` Kesinlikle Yasaktır:** İhtiyaç duyulan kolonlar tek tek listelenir.
   - **Parametreli Sorgular Zorunludur:** Tüm değişkenler prepared statement ile bağlanır.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|---|---|
| 1 | BCNF ihlali tespit edildiğinde audit ve düzeltme planı hazırlanır | Escalation L1(Data)→L2 |
| 2 | ORM kullanımı, `SELECT *` veya parametresiz sorgu | Derhal revert |
| 3 | Canlı veritabanında doğrudan manuel DDL veya `CREATE DATABASE` (`HL-08`) | Yasak; migration paketi ve onay şarttır |
| 4 | Ek 2 DB'yi 18'lik seriye dahil edip sayı uydurmak | Zero-Hallucination ihlali |
| 5 | Domain boundary: `*.sql` dosyaları data-engineer agent yetkisindedir | Layer violation → revert |
| 6 | Subagent commit çalıştırması (`HL-03`) | İhlal; orkestratör yetkisindedir |

---

## 5. Workflow

```text
Veritabanı / Şema / Migration İşi
  → Vault: @.ai/CLAUDE.md §18 (ADR-040) + karar-kurallari.md (ADR-112) + .ai/.sql/mysql/*.sql
  → Skill: database-normalize-maker (BCNF analizi)
  → Migration Dosyaları Üretimi (shared/database/migrations/ - up ve down SQL)
  → Idempotent Pre-Check Doğrulaması
  → Kullanıcı Onayı Alınması (KURAL-07) → Test Ortamında Çalıştırma & Rollback Testi
  → durum.md güncelleme + log.md append
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---|---|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | 18 DB adı ve tablo sayıları | Eksiksiz ve vault ile uyumlu |
| 3 | Ek 2 DB durumu | media_catalog (deploy edilmedi) & novasearch (knex) ayrımı net |
| 4 | ADR-112 migration sözleşmesi | Up/down SQL + pre-check + onay vurgulandı |
| 5 | Veri erişim kuralları | Raw PDO · No ORM · No SELECT * · Prepared |
| 6 | Hata listesi uyumu | `HL-03`, `HL-08` kuralları işlendi |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|---|---|---|
| Anayasa §18 | [[.ai/CLAUDE.md]] | 18 BCNF şema tanımı ve ADR-040 |
| Karar Kuralları | [[karar-kurallari.md]] | ADR-112 şema sözleşme hizalama kuralı |
| Temel Direktifler | [[kurallar.md]] | KURAL-04 veritabanı guardrail'ları |
| Hata Listesi Kataloğu | [[hata-listesi.md]] | HL-03 commit yasağı, HL-08 migration zorunluluğu |
| Şema Dump'ları | `.ai/.sql/mysql/*.sql` | Şema sözleşme SSOT'u (20 dosya) |
| Migration Dizini | `shared/database/migrations/` | Migration yama dosyaları |

---

**Authority:** Bayram Ali / Kurucu & Mimari Otorite  
**Last Updated:** 2026-11-01 · **Version:** 2.0.0
