# Vault Update

Proje değişimlerine göre vault'u güncelle, indeksleri ve referansları tazele.

## Ne Yapar?

1. **Degisiklikleri Tespit Eder** — git diff ile son degisiklikleri bulur
2. **Indeksi Gunceller** — .ai/index.md'yi tazeler
3. **Referanslari Kontrol Eder** — Kirik linkleri duzeltir
4. **Yeni Dosyalari Ekler** — Yeni eklenen dosyalari indeksler
5. **Silinen Dosyalari Cikarir** — Silinen dosyalari indeksten temizler
6. **Rapor Olusturur** — Guncelleme raporunu .ai/log.md'ye yazar

## Adımlar

```
ADIM 1: Degisiklikleri Tespit Et
  → git diff --name-only
  → Yeni/degisen dosyalari listele

ADIM 2: Indeksi Guncelle
  → .ai/index.md'yi guncelle
  → Yeni dosyalari ekle
  → Silinen dosyalari cikar

ADIM 3: Referanslari Kontrol Et
  → [[wiki-link]] dogrulamasi yap
  → Kirik linkleri duzelt

ADIM 4: Rapor Olustur
  → .ai/log.md'ye INFO yaz
  → Degisiklik ozetini paylas
```

## Kurallar

- In-Place: Dosya adı degismez
- Frozen ADR (001-037): Degistirilemez
- Yeni ADR: ADR-061+ numaralandirmasi
- log.md: Append-only
