# 🌐 skill-maker — Agentic Orchestration Örnekleri

## 1. Otonom Skill Üretim Senaryosu

**Kullanıcı:** `Yeni bir veritabanı analiz skill'i oluştur, adı sql-optimizer olsun.`

**Agentic İş Akışı (AI Otonom Kararları):**
1. **Trigger:** `sql-optimizer` isteği alındı.
2. **Web Research:** AI, MySQL 8 BCNF ve N+1 problemlerini, güncel EXPLAIN komutlarını web'den arar. (Zorunlu)
3. **Truth Mode Doğrulaması:** Bulduğu tekniklerin geçerliliğini teyit eder (Score 90-100).
4. **Dosya Üretimi:** 2000 satır kısıtına uyarak `SKILL.md` ve referansları oluşturur.
5. **Kural Enjeksiyonu:** `coremusic-rules.md` dosyasındaki SQL standartlarını (SELECT * yasağı vb.) skill'in içine hard-code eder.
6. **Kapanış:** Kullanıcıya Zero Hallucination belgesiyle rapor verir.

## 2. Örnek SKILL.md Çıktısı (Kısaltılmış)

```yaml
---
name: sql-optimizer
description: Veritabanı sorgularını otonom analiz eder. N+1, SELECT * kullanımı ve eksik index'leri tespit eder. Tetikleyici: "sql analiz", "optimize et".
license: MIT
metadata:
  version: 1.0.0
  author: Bayram Ali
  compatibility: Kiro IDE
  category: database-orchestration
---

# 🌐 sql-optimizer — Otonom Analiz
...
## 1. Mandatory Rules
- Sadece `EXPLAIN` veya `EXPLAIN ANALYZE` kullanılır.
- Uydurma index tahmini YASAKTIR. (// ⚠️ VERIFICATION REQUIRED)
...
```
