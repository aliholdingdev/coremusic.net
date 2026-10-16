---
node_id: "K008-discovery-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-discovery-kapsam"
evidence:
  - ".ai/.sql/mysql/coremusic_logs.sql satır 398–414 · 416–431"
  - ".ai/.sql/mysql/coremusic_musics.sql satır 221–234"
validation: "2026-10-15 — 3 örnek (SQL ×3); hepsi disk'ten birebir kopyalandı"
open_questions:
  - "Arama PHP/JS örneği → tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# discovery — Örnekler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Üç örnek de SQL şemasıdır ve birebir disk'ten kopyalanmıştır. Keşif için
> PHP/JS uygulama örneği bu oturumda **tarama edilmedi** (kayıt: §4).

## §1 ÖRNEK 1 — SQL: search_logs (arama kaydı · satır 398–414)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_logs.sql · satır 398-414
CREATE TABLE `search_logs` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) DEFAULT NULL,
  `search_query` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `search_type` enum('music','artist','album','playlist','podcast','radio','video') COLLATE utf8mb4_unicode_ci DEFAULT 'music',
  `results_count` int DEFAULT '0',
  `selected_result_id` binary(16) DEFAULT NULL,
  `selected_result_position` int DEFAULT NULL,
  `response_time_ms` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_search_logs_user_id` (`user_id`),
  KEY `idx_search_logs_query` (`search_query`),
  KEY `idx_search_logs_type` (`search_type`),
  KEY `idx_search_logs_created_at` (`created_at`),
  FULLTEXT KEY `ft_search_logs_query` (`search_query`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Search query logging for analytics, search improvement, and trending analysis';
```

**Ne kanıtlıyor:** arama bir **ölçüm olayıdır** — sorgu (satır 401), tip (402),
sonuç sayısı (403), seçilenin pozisyonu (405) ve yanıt süresi (406) tek satırda;
comment "trending analysis" der (414).

## §2 ÖRNEK 2 — SQL: user_activity_logs (aktivite akışı · satır 416–431)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_logs.sql · satır 416-431
CREATE TABLE `user_activity_logs` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `activity_type` enum('login','logout','play','pause','skip','like','unlike','follow','unfollow','download','share','search','playlist_add','playlist_remove') COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` binary(16) DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','car','studio','home') COLLATE utf8mb4_unicode_ci DEFAULT 'desktop',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_activity_user_id` (`user_id`),
  KEY `idx_user_activity_type` (`activity_type`),
  KEY `idx_user_activity_created_at` (`created_at`),
  KEY `idx_user_activity_user_type` (`user_id`,`activity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User activity tracking for analytics, personalization, and recommendations';
```

**Ne kanıtlıyor:** 14 değerli `activity_type` (satır 419) hem keşif hem öneriyi
besleyebilir — ama bu tablo **keşif ile recommendation arasındaki sınırın** canlı
kanıtıdır (bağlam sahipliği çelişkisi: master §6 C2). `metadata` json (satır 422) şemasız alan bırakır.

## §3 ÖRNEK 3 — SQL: music_similar (benzerlik · satır 221–234)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_musics.sql · satır 221-234
CREATE TABLE `music_similar` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz benzerlik tanımlayıcı',
  `music_id` binary(16) NOT NULL COMMENT 'Müzik ID',
  `similar_music_id` binary(16) NOT NULL COMMENT 'Benzer müzik ID',
  `similarity_score` decimal(3,2) NOT NULL COMMENT 'Benzerlik puanı (0.00-1.00)',
  `algorithm` enum('collaborative','content_based','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hybrid' COMMENT 'Benzerlik algoritması',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_similar_unique` (`music_id`,`similar_music_id`) COMMENT 'Benzersiz müzik çifti',
  KEY `idx_music_similar_target` (`similar_music_id`) COMMENT 'Hedef müzik filtresi',
  KEY `idx_music_similar_score` (`similarity_score`) COMMENT 'Benzerlik puanı sıralaması',
  CONSTRAINT `fk_music_similar_source` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_music_similar_target` FOREIGN KEY (`similar_music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Benzer müzik tablosu';
```

**Ne kanıtlıyor:** benzerlik **çift yönlu değil tekil** tutulur (satır 229 unique),
puan 0.00–1.00 aralığında ondalıklıdır (satır 225) ve üç algoritma adı şemada
gömezlidir — `hybrid` varsayılan (satır 226).

## §4 ÖRNEK YOK (negatif kayıt)

| Aranan örnek türü | Durum | Kanıt |
|---|---|---|
| Arama servisi (PHP) | **UNKNOWN** | tarama yapılmadı → [[discovery-dogrulama]] D-DS13 ⚠️ |
| Arama UI/modülü (JS) | **UNKNOWN** | aynı |

**Kardeş dosyalar:** [[discovery-kapsam]] · [[discovery-dogrulama]] · [[discovery-web-arastirma]] · [[discovery-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
