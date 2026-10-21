# 02 — Normalizasyon Kuralları (Derin)

> 1NF → 2NF → 3NF → BCNF (→ 4NF/5NF). Aday anahtar ve fonksiyonel bağımlılık
> çıkarımı deterministik olmalıdır. Uydurma FD yasak.

## Fonksiyonel bağımlılık (FD) & Armstrong

- `X → Y`: X belirliysen Y de belirlidir.
- Aksiyomlar: reflexivity (`X→X`), augmentation (`X→Y ⟹ XZ→YZ`), transitivity (`X→Y, Y→Z ⟹ X→Z`).
- Türetilmiş: union (`X→Y, X→Z ⟹ X→YZ`), decomposition (`X→YZ ⟹ X→Y, X→Z`).
- **Kapanım `X+`:** X'ten bu aksiyomlarla türetilebilir tüm attribute'lar.

## Aday anahtar algoritması

1. Attribute kümesi `U`.
2. Her `X ⊆ U` için `X+` hesapla.
3. `X+ = U` olan ve minimal olan (hiç alt kümesi superkey olmayan) X → aday anahtar.
4. Bir aday anahtar `PRIMARY KEY`; diğerleri `UNIQUE` olarak korunur.

```text
FD: A→B, B→C, D→C ; U={A,B,C,D}
A+ = {A,B,C}  (A→B→C) ≠ U → anahtar değil
AD+ = {A,B,C,D} = U ; A+≠U, D+={C,D}≠U → (A,D) minimal superkey = aday anahtar
Prime: A,D ; Non-prime: B,C
```

## Form tanımları (sıkı)

| NF | Koşul | İhlal | Örnek çözüm |
|----|-------|-------|-------------|
| 1NF | Atomik değer, tekrar grup yok, PK var | `tags` kolonunda virgülle liste; `phone1,phone2` | pivot/junction tablo |
| 2NF | 1NF + non-prime tam bağımlı | Composite PK'da `product_name` yalnız `product_id`'ye bağlı | ürünü ayrı tabloya |
| 3NF | 2NF + non-prime yalnız aday anahtara bağımlı | `dept_id→dept_name` transitive | dept'i ayrı tabloya |
| BCNF | Her non-trivial FD `X→Y` için X superkey | determinant (instructor_id) superkey değil | determinant'ı tabloya ayır |
| 4NF | BCNF + non-trivial multivalued dep. yok | bir tabloda hem `X↠Y` hem `X↠Z` | her ↠ için ayrı tablo |

## BCNF ayrıştırma (decomposition) kuralları

- **Bozulmasız (lossless) ayrıştırma:** Birleştirme orijinal veriyi korumalı. `X` aday anahtarlardan biri değilse `X`'i parçalara ayırırken kayıp olabilir — her zaman `X+` ile doğrula.
- **Bağımlılığı koru (dependency preserving):** Ayrıştırma sonrası her FD en az bir parçada korunmalı; yoksa uygulama katmanında ek sorgu gerekir.

```sql
-- BCNF İHLALİ: determinants superkey değil
-- FD: (student_id, course_id) → instructor_id ; instructor_id → room, instructor_name
-- Çözüm: instructor_id'yi kendi tablosuna taşı (lossless, dependency-preserving)
CREATE TABLE instructors (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    room VARCHAR(20) NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE enrollments (
    student_id BIGINT UNSIGNED NOT NULL,
    course_id  BIGINT UNSIGNED NOT NULL,
    instructor_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (student_id, course_id),
    KEY idx_enrollments_instructor (instructor_id),
    CONSTRAINT fk_enrollments_instructors FOREIGN KEY (instructor_id) REFERENCES instructors (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

## Denormalizasyon kapısı

Yalnız üç şartta (SKILL.md §3.4): tarihsel dondurma / ölçülmüş ağır okuma / raporlama önbelleği.
Her denormalize kolon **ADR ile gerekçelendirilir** ve tek source-of-truth normalize tablo kalır.

## Kontrol soruları (hızlı)

- Her kolon atomik mi? (1NF)
- Composite PK var mı → kısmi bağımlılık var mı? (2NF)
- `A → B → C` zinciri, B non-prime mı? (3NF)
- Her determinant superkey mi? (BCNF)
- Çok-değerli bağımsız attribute tek tabloda mı? (4NF)
