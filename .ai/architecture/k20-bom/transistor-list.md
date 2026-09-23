---
title: "Transistör Listesi"
layer: K20
category: "BOM & Üretim"
date: 2026-09-20
---

# Transistör Listesi

## Genel Bakış

COREMUSIC güç amplifikatöründe kullanılan transistörler,Class AB ve Class D topolojileri için seçilmiştir. Çıkış transistörleri, sürücü transistörleri ve regülatör transistörleri olmak üzere üç ana kategoride sınıflandırılır. Her transistör için alternatif tedarikçiler belirlenmiştir.

## Bileşen Listesi

### Çıkış Transistörleri (MJL21194/93 Serisi)

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| NPN Power | MJL21194G | TO-264 | 4 | $2.85 | ON Semi |
| PNP Power | MJL21193G | TO-264 | 4 | $3.10 | ON Semi |
| NPN Alt | 2SA1943 | TO-3P | 4 | $1.95 | Toshiba |
| PNP Alt | 2SC5200 | TO-3P | 4 | $2.10 | Toshiba |

### Sürücü Transistörleri (BD139/140 Serisi)

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| NPN Driver | BD139 | TO-126 | 8 | $0.18 | STMicro |
| PNP Driver | BD140 | TO-126 | 8 | $0.20 | STMicro |
| NPN Alt | MJE15032G | TO-225 | 4 | $0.35 | ON Semi |
| PNP Alt | MJE15033G | TO-225 | 4 | $0.38 | ON Semi |

### Sinyal Transistörleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| PNP Small Signal | 2SA1015GR | TO-92 | 16 | $0.02 | Rohm |
| NPN Small Signal | 2SC1815GR | TO-92 | 16 | $0.02 | Rohm |
| PNP Alt | BC557B | TO-92 | 8 | $0.015 | NXP |
| NPN Alt | BC547B | TO-92 | 8 | $0.015 | NXP |

## Seçim Kriterleri

### MJL21194/93 Seçim Nedenleri

| Kriter | MJL21194/93 | 2SA1943/2SC5200 |
|--------|-------------|-----------------|
| VCEO | 250V | 230V |
| IC max | 16A | 15A |
| PD | 200W | 150W |
| hFE min | 55 | 55 |
| fT | 4MHz | 3MHz |
| Fiyat | $2.85 | $1.95 |

### BD139/140 Seçim Nedenleri

| Kriter | BD139/140 | MJE15032/33 |
|--------|-----------|-------------|
| VCEO | 80V | 250V |
| IC | 1.5A | 8A |
| PD | 12.5W | 50W |
| fT | 190MHz | 30MHz |
| Fiyat | $0.18 | $0.35 |

## Tedarikçi Bilgisi

| Tedarikçi | Ürün Kodu | Stok | Min Sipariş |
|-----------|-----------|------|-------------|
| Mouser | 863-MJL21194G | Stokta | 1 |
| DigiKey | MJL21194GOS-ND | Stokta | 1 |
| RS Components | 771-MJL21194 | Stokta | 10 |
| LCSC | C142405 | Stokta | 100 |

## Bağımlılıklar

- Soğutma sistemi (K6) - TO-264 ve TO-3P paketleri için heatsink
- Koruma devresi (K9) - Termal koruma devresi
- Güç kaynağı (K3) -/+35V besleme gerilimi

## Durum: Implementasyon

Tüm transistörler seçilmiş ve doğrulanmıştır. İlk prototip üretiminde test edilmiştir.
