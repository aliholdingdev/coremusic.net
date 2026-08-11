# Vault Check

Vault bütünlük kontrolü yap, checksum'ları doğrula, rapor oluştur.

## Ne Yapar?

1. Tum vault dosyalarini tara
2. Kirik referanslari bul
3. Indeksi dogrula
4. Boyut limitlerini kontrol et
5. Rapor olustur

## Kontrol Alanlari

| Alan | Kontrol |
|------|---------|
| Wiki-links | [[referans]] formatindaki baglantilar |
| File paths | Dosya yollari gercekten var mi? |
| Index | .ai/index.md guncel mi? |
| ADR | Frozen ADR degistirilmis mi? |
| Size | Dosyalar 1000 satir sinirini asiyor mu? |
| Secrets | Hassas veri log'da var mi? |

## Adımlar

```
ADIM 1: Dosya Taramasi
  → .ai/ dizinindeki tum .md dosyalari
  → .claude/rules/ dizinindeki tum dosyalar

ADIM 2: Referans Dogrulama
  → [[...]] pattern'lerini bul
  → Hedef dosyalarin varligini kontrol et

ADIM 3: Indeks Dogrulama
  → index.md'deki tum referanslari kontrol et
  → Eksik/gereksiz girisleri tespit et

ADIM 4: Rapor
  → Sonuclari listele
  → Uyari ve hatalari belirt
```
