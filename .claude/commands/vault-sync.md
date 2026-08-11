# Vault Sync

Vault dosyalarını senkronize et, bütünlüğü doğrula, referansları kontrol et.

## Ne Yapar?

1. **Tarama** — Tum .ai/ ve .claude/rules/ dosyalarini tarar
2. **Indeksleme** — Yeni dosyalari .ai/index.md'ye ekler
3. **Referans Dogrulama** — Tum [[wiki-link]] referanslarini kontrol eder
4. **Butunluk** — Dosya checksum'larini dogrular
5. **Rapor** — Senkronizasyon raporu olusturur

## Adımlar

```
ADIM 1: Tarama
  → Get-ChildItem -Path .ai -Recurse -Filter *.md
  → Get-ChildItem -Path .claude/rules -Filter *.md

ADIM 2: Indeksleme
  → Yeni dosyalari index.md'ye ekle
  → Silinen dosyalari index.md'den cikar

ADIM 3: Referans Dogrulama
  → [[wiki-link]] formatindaki baglantilari kontrol et
  → Kirik referanslari tespit et

ADIM 4: Butunluk
  → Dosya checksum'larini hesapla
  → Tutarliligi dogrula

ADIM 5: Rapor
  → Sonuclari .ai/log.md'ye yaz
  → Uyarilari listele
```

## Kurallar

- In-Place Refactoring: Dosya adı/yolu değişmez
- SSOT: Bilgi sadece .ai/ vault'tan okunur
- Append-Only: log.md'ye sadece ekleme yapılır
- MSA Limit: Max 15 dosya okunur
