# 02. Normalizasyon Kuralları

## Genel Bakış

Veritabanı motoru, oluşturulan tabloları BCNF (Boyce-Codd Normal Form) seviyesine kadar denetler. **Normalizasyon ihlalleri sessizce geçiştirilemez.** Denormalizasyon yapılacaksa, bu mutlaka ADR ile gerekçelendirilmelidir.

## 1NF (First Normal Form) — Atomik Veri

**Kural:** Her kolon bölünemez tek bir (atomik) değer tutmalıdır. Virgülle ayrılmış değerler YASAKTIR.

```sql
-- YANLIŞ (1NF ihlali)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hobbies TEXT  -- "yüzme, koşu, bisiklet"
);

-- DOĞRU (1NF uyumlu)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_hobbies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    hobby VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kontrol Sorusu:** `phone1, phone2, phone3` gibi tekrarlayan gruplar var mı? JSON kolonu kullanılıyorsa, ilişkisel veri mi yoksa esnek meta-veri mi tuttuğu kontrol edilir.

## 2NF (Second Normal Form) — Kısmi Bağımlılık

**Kural:** 1NF + Kompozit Primary Key varsa, non-key kolonlar tam bağımlı olmalı.

```sql
-- YANLIŞ (2NF ihlali)
CREATE TABLE order_items (
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    product_name VARCHAR(100),  -- sadece product_id'ye bağlı
    quantity INT NOT NULL,
    PRIMARY KEY (order_id, product_id)
);

-- DOĞRU (2NF uyumlu)
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kontrol Sorusu:** Composite PK varsa, her non-key kolon her iki key'e de bağımlı mı? Her tablonun tekil bir PK'sı varsa bu sorun temelden çözülür.

## 3NF (Third Normal Form) — Geçici Bağımlılık

**Kural:** 2NF + Non-key kolonlar sadece primary key'e bağlı olmalı.

```sql
-- YANLIŞ (3NF ihlali)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city_id BIGINT UNSIGNED NOT NULL,
    city_name VARCHAR(100),   -- transitive dependency
    city_country VARCHAR(100) -- transitive dependency
);

-- DOĞRU (3NF uyumlu)
CREATE TABLE cities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kontrol Sorusu:** `A → B → C` gibi zincirleme bağımlılık var mı? `department_name` gibi veriler kendi tablolarına çıkarılmalı.

## BCNF (Boyce-Codd Normal Form)

**Kural:** 3NF + Her determinant (belirleyici) bir candidate key olmalı.

```sql
-- YANLIŞ (BCNF ihlali)
CREATE TABLE course_enrollments (
    student_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    instructor_id BIGINT UNSIGNED NOT NULL,
    instructor_name VARCHAR(100),
    PRIMARY KEY (student_id, course_id)
);

-- DOĞRU (BCNF uyumlu)
CREATE TABLE instructors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE courses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    instructor_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE course_enrollments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Denormalizasyon Ne Zaman Kabul Edilir?

Denormalizasyon SADECE şu şartlarda kabul edilir:

| Şart | Gerekçe |
|------|---------|
| Verinin tarihsel olarak dondurulması | Audit trail, log snapshot |
| Yoğun okunma senaryosu | Join maliyeti ölçülmüş ve çok yüksek |
| ADR ile gerekçelendirilmiş | `ADR-XXX: total_price denormalize edildi, çünkü...` |

```sql
-- Denormalizasyon örneği (ADR gerekçeli)
-- ADR-XXX: 'total_price' kolonu denormalize edildi
-- Gerekçe: Raporlama ekranında çok ağır Join işlemlerini engellemek
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    item_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kural:** Denormalizasyon her zaman `updated_at` trigger ile senkronize edilmeli.
