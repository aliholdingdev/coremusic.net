# Bug Fix

Mevcut bug'ları düzelt, hata kökünü analiz et, çözüm uygula.

## Adımlar

```
ADIM 1: Hatayı Anla
  → Hata mesajını oku
  → Stack trace'i analiz et
  → Tekrarlanabilirlik kontrolü

ADIM 2: Kök Nedeni Bul (Root Cause Analysis)
  → Hangi dosyada?
  → Hangi satırda?
  → Neden oluştu?

ADIM 3: Çözüm Planı Oluştur
  → Kısa vadeli fix (hotfix)
  → Uzun vadeli çözüm (refactoring)
  → Etkilenen dosyaları listele

ADIM 4: Kodu Düzelt
  → Minimal değişiklik yap
  → ADR uyumluluğunu koru
  → Layer violation kontrolü

ADIM 5: Test Et
  → Regression testi çalıştır
  → İlgili unit testleri kontrol et
  → Edge case'leri test et

ADIM 6: Log Yaz
  → .ai/log.md'ye ERROR veya WARN ekle
  → Düzeltme açıklamasını yaz
```

## Öncelik & Süre

| Öncelik | Süre | Örnek |
|---------|------|-------|
| CRITICAL | 1 saat | Auth bypass, veri sızıntısı |
| HIGH | 4 saat | Kritik işlev kaybı |
| MEDIUM | 1 gün | Normal hata |
| LOW | 1 hafta | Kozmetik hata |

## Kök Neden Analiz Teknikleri

| Teknik | Kullanım |
|--------|----------|
| 5 Neden | Her adımda "Neden?" sorusu |
| Fishbone | İnsan, Süreç, Teknoloji, Çevre |
| Binary Search | Kodu yarıya bölerek daraltma |
| Git Bisect | Hangi commit bozdu? |
