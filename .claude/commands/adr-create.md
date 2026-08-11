# ADR Create

Yeni Architecture Decision Record oluştur.

## ADR Yaşam Döngüsü

```
Draft → Review → Active → Frozen
```

| Aşama | Değişiklik | Süre | Onay |
|-------|------------|------|------|
| Draft | Tamamen düzenlenebilir | Süresiz | Gerekmez |
| Review | Kısıtlı değişiklik | 7 gün | Tech Lead |
| Active | Sadece minor güncelleme | Kalıcı | Arch Lead |
| Frozen | Hiçbir değişiklik | Sonsuz | — |

## Adımlar

```
ADIM 1: Bağlamı Tanıla
  → Hangi karar alınacak?
  → Neden şimdi?
  → Mevcut durum nedir?

ADIM 2: Mevcut ADR'leri Kontrol Et
  → Benzer karar var mı?
  → Çelişki var mı?
  → Frozen ADR (001-037) etkileniyor mu?

ADIM 3: Seçenekleri Değerlendir
  → En az 3 alternatif sun
  → Artıları/eksileri listele
  → Risk analizi yap

ADIM 4: Karar Ver
  → En iyi seçeneği belirle
  → Gerekçeyi yaz
  → Uygulama planını hazırla

ADIM 5: Dokümante Et
  → decisions/draft/ADR-NNN-*.md oluştur
  → Frontmatter ekle (status: draft)
  → Cross-reference'ları güncelle

ADIM 6: Onay İste
  → Tech Lead'e gönder
  → Review aşamasına al
  → Onay sonrası Active'e taşı
```

## ADR Dosya Formatı

```markdown
---
type: decision
category: [architecture|security|database|audio|frontend|backend]
title: "ADR-NNN: [Karar Başlığı]"
date: YYYY-MM-DD
status: draft
version: 1.0.0
---

# ADR-NNN: [Karar Başlığı]

## Bağlam
[Kararın alındığı bağlam]

## Karar
[Alınan karar]

## Gerekçe
[Kararın gerekçesi]

## Alternatifler
[Değerlendirilen alternatifler]

## Sonuçlar
[Kararın sonuçları]
```

## Kurallar

- Frozen ADR (001-037): ASLA değiştirilemez
- Yeni ADR: ADR-061+ numaralandirmasi
- Cross-reference: İlgili ADR'leri referans al
- log.md: Her ADR girişi loglanır
