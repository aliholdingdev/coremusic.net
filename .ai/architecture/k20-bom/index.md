---
title: "K20 BOM & Üretim Katmanı"
layer: K20
category: "BOM & Üretim"
date: 2026-09-20
---

# K20 BOM & Üretim Katmanı

## Genel Bakış

K20 katmanı, COREMUSIC donanım tasarımının Bill of Materials (BOM) yapısını ve üretim süreçlerini tanımlar. Her bir bileşen için tedarikçi bilgileri, maliyet tahminleri ve üretim araçları bu katmanda belgelenir. Bu katman, K0-K19 katmanlarındaki tüm donanım bileşenlerinin üretim ve tedarik zincirini entegre eder.

## BOM Yapısı

COREMUSIC BOM'u hiyerarşik bir yapıya sahiptir:

| Seviye | Açıklama | Örnek |
|--------|----------|-------|
| L1 | Ana modül | Ana kart, Güç kaynağı |
| L2 | Alt modül | Amplifikatör kartı, DAC kartı |
| L3 | Bileşen grubu | Pasifler, aktifler, konnektörler |
| L4 | Tekil bileşen | R1, C1, Q1 |

## Modül Haritası

| Modül | Açıklama | Kategori |
|-------|----------|----------|
| M1 | Ana amplifikatör kartı | Güç |
| M2 | DAC ve ön amplifikatör | Sinyal |
| M3 | Güç kaynağı ünitesi | Güç |
| M4 | Çıkış filtresi ve choke | Sinyal |
| M5 | Giriş/konnektör paneli | Mekanik |
| M6 | Termal yönetim | Mekanik |
| M7 | Koruma devresi | Güvenlik |

## Dosya Yapısı

```
k20-bom/
├── index.md                  # Bu dosya
├── transistor-list.md        # Transistör BOM'u
├── diode-list.md             # Diyot BOM'u
├── resistor-list.md          # Direnç BOM'u
├── capacitor-list.md         # Kondansatör BOM'u
├── inductor-list.md          # Bobin BOM'u
├── ic-list.md                # IC BOM'u
├── connector-list.md         # Konnektör BOM'u
├── cost-estimation.md        # Maliyet analizi
└── production-tools.md       # Üretim araçları
```

## Toplam Bileşen Özeti

| Kategori | Adet | Yüzde |
|----------|------|-------|
| Transistör | 24 | %4.8 |
| Diyot | 18 | %3.6 |
| Direnç | 156 | %31.2 |
| Kondansatör | 142 | %28.4 |
| Bobin | 12 | %2.4 |
| IC | 16 | %3.2 |
| Konnektör | 28 | %5.6 |
| Diğer | 104 | %20.8 |
| **Toplam** | **500** | **%100** |

## Tedarikçi Stratejisi

| Strateji | Açıklama |
|----------|----------|
| Birincil tedarikçi | Mouser Electronics - ana bileşenler |
| İkincil tedarikçi | DigiKey - alternatif ve acil ihtiyaçlar |
| Yerel tedarikçi | RS Components - pasif bileşenler |
| Uzakdoğu tedarikçi | LCSC - yüksek hacimli üretim |

## Kalite Kontrol

- IPC-A-610 Class 2 standartlarına uygunluk
- Her batch için First Article Inspection (FAI)
- RoHS ve REACH uyumluluğu
- AQL 2.5 seviyesinde rastgele kontrol

## Durum: Implementasyon

Bu katman aktif olarak güncellenmektedir. İlk prototip BOM'u tamamlanmış olup, seri üretim için optimizasyon aşamasındadır.
