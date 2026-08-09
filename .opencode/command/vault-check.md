---
description: "Vault butunlugu kontrol et, kirik referanslari bul, indeksi guncelle"
agent: build
---

# Vault Check Komutu

Vault dosyalarinin butunlugunu kontrol eder.

## Nasil Calisir?

1. Tum vault dosyalarini tara
2. Kirik referanslari bul
3. Indeksi dogrula
4. Boyut limitlerini kontrol et
5. Rapor olustur

## Kullanim

```
/vault-check
```

## Kontrol Alanlari

| Alan | Kontrol |
|------|---------|
| Wiki-links | [[referans]] formatindaki baglantilar |
| File paths | Dosya yollari gercekten var mi? |
| Index | .ai/index.md guncel mi? |
| Size | Dosyalar 1000 satiri asiyor mu? |
| Frontmatter | YAML frontmatter mevcut mu? |
| ADR refs | ADR referoslari gecerli mi? |

## Analiz Adimlari

```
ADIM 1: Tarama
  → .ai/ dizinindeki tum .md dosyalari
  → .claude/ dizinindeki tum dosyalar
  → .opencode/ dizinindeki tum dosyalar

ADIM 2: Wiki-link Kontrolu
  → [[dosya]] formatindaki referanslari bul
  → Her referansin hedefinin var oldugunu dogrula
  → Kirik linkleri listele

ADIM 3: Dosya Yolu Kontrolu
  → Referans edilen dosya yollari gecerli mi?
  → Relative path'ler dogru mu?
  → Symlink'ler calisiyor mu?

ADIM 4: Indeks Kontrolu
  → .ai/index.md'deki tum dosyalar mevcut mu?
  → Yeni dosyalar indekste var mi?
  → Silinen dosyalar indeksten cikarildi mi?

ADIM 5: Boyut Kontrolu
  → Hangi dosyalar 1000 satiri asiyor?
  → Buyuk dosyalar parcalanmali mi?

ADIM 6: Rapor
  → Toplam dosya sayisi
  → Kirik referans sayisi
  → Buyuk dosya sayisi
  → Oneriler
```

## Vault Context

- `.ai/index.md` — Ana indeks
- `.ai/keys.md` — Navigasyon haritasi
- `.ai/AGENTS.md` — Agent tanimlari
- `.ai/WORKFLOW.md` — Surecler

## Cikti Formati

```markdown
# Vault Kontrol Raporu

## Tarih: [tarih]

## Ozet
- Toplam dosya: X
- Kirik referans: X
- Buyuk dosya (1000+ satir): X
- Eksik frontmatter: X

## Kirik Referanslar
| Dosya | Referans | Hedef |
|-------|----------|-------|
| [dosya] | [[ref]] | [hedef] |

## Buyuk Dosyalar
| Dosya | Satir | Limit |
|-------|-------|-------|
| [dosya] | X | 1000 |

## Oneriler
1. [oneri 1]
```
