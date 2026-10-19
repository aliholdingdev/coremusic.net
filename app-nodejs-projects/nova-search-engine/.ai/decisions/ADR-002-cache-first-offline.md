# ADR-002 — Cache-First ve Offline-First

- **Durum:** Kabul
- **Tarih:** 2026-10-16
- **Alan:** Performans / Dayanıklılık

## Bağlam

Her aramada dış kaynağa gitmek hem yavaş hem sürdürülemez. İnternet kapalıyken
bile daha önce keşfedilenler aranabilmeli.

## Karar

Karar zinciri: **RAM Cache (L1) → Disk Cache (L2) → Yerel DB → YouTube (son çare).**
Arama motoru bu sırayı asla atlamaz; `--offline` yalnız ilk üç katmanı kullanır.

## Gerekçe

- Tekrarlanan aramaların dış maliyeti 0 olmalı ("aynı veri bir daha çekilmez")
- Offline çalışma temel özelliktir: `--offline` ile 9ms lokal yanıt ölçüldü
- RAM: LRU + TTL (5dk) · Disk: JSON + TTL (1sa) — `packages/cache`

## Sonuçlar

- Cache anahtarları normalize + sha1 (`queryKey`)
- Cache isabet oranı `stats` komutuyla izlenir
- Metadata yenileme V2'ye ertelendi (tazeleme politikaları)
