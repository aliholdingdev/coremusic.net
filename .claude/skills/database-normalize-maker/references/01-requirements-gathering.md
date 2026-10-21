# 01 — Gereksinim Toplama

> Şema tasarımı, doğru sorular sorulmadan başlamaz. Tahminle şema üretmek yasak.
> Eksik bilgi varsa DUR ve sor. Bu adım SKILL.md §8 "Adım 1: MODEL" karşılığıdır.

## Zorunlu soru seti

| # | Soru | Neden | Örnek cevap |
|---|------|-------|-------------|
| 1 | Hangi varlıklar (tablolar) gerekli? | Varlık listesi | users, orders, products |
| 2 | İlişkiler nasıl (1:M, M:M, 1:1)? | FK tasarımı | user 1:M order |
| 3 | Her varlığın doğal anahtarı ne? | Aday anahtar | email, (playlist_id,track_id) |
| 4 | Hangi alanlar PII/sensitive? | Şifreleme + audit | email, ip, payment |
| 5 | Beklenen veri miktarı/ Büyüme? | Tip + index ölçeği | 1M satır/yıl |
| 6 | Ana sorgu kalıpları hangileri? | Index stratejisi | WHERE user_id+status ORDER BY created_at |
| 7 | Soft delete gerekli mi? | deleted_at kolonu | evet (yasak: hard delete) |
| 8 | Audit trail gerekli mi? | _audit tablosu | evet (kullanıcı/ödeme) |
| 9 | Silme stratejisi (ON DELETE)? | Veri bütünlüğü | RESTRICT / CASCADE / SET NULL |
| 10 | Hedef MySQL sürümü? | Feature kullanılabilirliği | 8.0.x (LTS) — kesin patch VERIFICATION REQUIRED |

## Gereksinim → Şema izdüşümü

| Gereksinim | Şema karşılığı |
|-----------|----------------|
| “Aynı kullanıcı birden fazla e-posta tutabilir” | 1:M → `user_email` tablosu (1NF) |
| “Sipariş satırında ürün adı da görünsün” | Join mi, denormalize mi? → JOIN tercih, ADR ile değilse |
| “Ödeme değişiklikleri denetlenmeli” | `payments_audit` + trigger |
| “Arama metni içinde geçsin” | FULLTEXT index |

## Eksik bilgi bildirim şablonu

```markdown
Şemayı doğru tasarlamak için şu bilgilere ihtiyacım var:

1. **Varlıklar:** Hangi tablolar? (anahtar alanlarla)
2. **İlişkiler:** Tablolar arası nasıl bağ var?
3. **Doğal anahtar:** Hangi alan tekliği garanti eder?
4. **Güvenlik:** Şifrelenmesi/audit edilmesi gereken alan?
5. **Ölçek:** Yaklaşık satır sayısı ve büyüme?
6. **Sorgular:** En sık WHERE/ORDER BY kalıpları?
7. **Silme:** Hangi ilişkiler CASCADE, hangileri RESTRICT?
```

## Çıktı

- Net varlık + ilişki listesi (gerekirse ASCII ER taslağı).
- Aday anahtar hipotezleri (SKILL.md §3'te doğrulanır).
- PII/audit gereksinim listesi → [04-security-audit.md](04-security-audit.md).
