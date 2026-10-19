# anti-ban-strategy.md — Sorumlu Toplama Stratejisi

> Kaynak: proje implementasyonu + doğrulanmış pratik. Amaç "ban atlatmak"
> DEĞİL; uzun süre kesintisiz, sürdürülebilir çalışmadır (ADR-001).

## Temel kurallar

1. **Cache-first:** aynı sorgu 2. kez YouTube'a gitmez (dış maliyet 0).
2. **İnsansı ritim:** istek arası `jitter(2500ms, ±%20)` — eşit aralıklı
   mikro-saniye istekleri bot imzasıdır, kullanılmaz.
3. **Token bucket:** dakikada 12 istek tavanı (eşzamanlı worker başına).
4. **Exponential backoff + jitter:** `min(base·2^n, 60sn)`, retry max 3.
5. **Deep search sınırı:** sorgu başına max 3 varyant, continuation max 50 sayfa.
6. **Tekilleştirme:** aynı video iki kez çekilmez (dedup upsert).
7. **Hata davranışında sessizlik:** 429/timeout'ta agresif retry YOK; backoff.

## Ölçüm

- Cache isabetinde dış istek: **0**
- 100+ canlı istekte: ban/engel gözlenmedi (yerel/kişisel kullanım)
- Offline arama: 4-9ms (hiçbir ağ teması yok)

## Yapılmayacaklar

- Proxy rotasyonu / user-agent spoofing **bilinçli olarak yok** (V2 değerlendirmesi)
- Sürekli polling / daemon tarama yok; toplama istek üzerine + kuyruk
