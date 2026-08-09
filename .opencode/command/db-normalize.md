---
description: "Veritabani semasini normalize et, BCNF kontrolu yap, migration olustur"
agent: data-engineer
---

# DB Normalize Komutu

Veritabani semasini normalize eder ve BCNF kontrolu yapar.

## Nasil Calisir?

1. Mevcut semayi analiz et
2. BCNF ihlallerini tespit et
3. Normalizasyon plani olustur
4. Migration olustur
5. Dogrulama yap

## Kullanim

```
/db-normalize [tablo adi veya "all"]
```

## Normalizasyon Seviyeleri

| Seviye | Kural | Kontrol |
|--------|-------|---------|
| 1NF | Her hucre tek deger | Atomic values |
| 2NF | Partial dependency yok | Full functional dependency |
| 3NF | Transitive dependency yok | Non-key → non-key baglantisi |
| BCNF | Her determinant candidate key | Determinant → candidate key |

## Analiz Adimlari

```
ADIM 1: Sema Analizi
  → Tablolari listele
  → Kolon turlerini kontrol et
  → Primary key'leri belirle
  → Foreign key'leri cikar

ADIM 2: Fonksiyonel Bagimlilik
  → X → Y bagimliliklarini bul
  → Determinant'lari belirle
  → Candidate key'leri hesapla

ADIM 3: BCNF Kontrolu
  → Her determinant candidate key mi?
  → IHlal varsa ayir
  → Yeni tablo olustur

ADIM 4: Migration Olustur
  → CREATE TABLE / ALTER TABLE
  → Veri tasi (INSERT INTO ... SELECT)
  → Foreign key ekle
  → Index olustur

ADIM 5: Dogrulama
  → BCNF uyumluluk kontrolu
  → Veri butunlugu kontrolu
  → Performance testi
```

## CoreMusic DB Kurallari

```
✅ 9 BCNF veritabani (ADR-040)
✅ PDO Prepared Statements (ADR-002)
✅ ORM YASAK
✅ SELECT * YASAK
✅ Soft delete (is_deleted)
✅ Audit trail (created_at, updated_at)
✅ Timestamp (created_at, updated_at)
```

## Vault Context

- `.ai/decisions/accepted/ADR-040-database-authority` — 9 BCNF DB
- `.ai/decisions/accepted/ADR-041-database-normalization-supplementary`
- `.ai/decisions/accepted/ADR-002-pdo-mandatory-no-orm`
- `.ai/architecture/l0-infrastructure/` — DB mimarisi
- `.claude/rules/database-standards.md`

## Cikti Formati

```markdown
# DB Normalizasyon Raporu

## Tablo: [tablo_adi]
## Mevcut Seviye: [1NF/2NF/3NF/BCNF]
## Hedef Seviye: BCNF

## Tespit Edilen Ihlallar
| # | Ihlal | Aciklama | Cozum |
|---|-------|----------|-------|
| 1 | [ihlal] | [aciklama] | [cozum] |

## Migration
```sql
[SQL migration kodu]
```

## Dogrulama
- [ ] BCNF uyumlu
- [ ] Veri butunlugu saglandi
- [ ] Performance etkilenmedi
```
