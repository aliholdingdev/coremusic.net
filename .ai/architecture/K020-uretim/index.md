---
type: architecture
category: layer
title: "K020 — Üretim"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K020 — Üretim

## §1 Kimlik
- Katman: K020 · Alan: **A5** (K16-K20).
- Kapsam: elektronik platform üretimi — seri üretim mimarisi ve SKU yönetimi.

## §2 Sorumluluk
1. Elektronik platform üretim mimarisi (ADR-064).
2. Kanal varyant SKU'larının üretim planına taşınması (ADR-090).
3. BOM/imalat dosyalarının toplanması (K019 PCB çıktısı).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K016 · K017 · K018 · K019 (donanım bileşenleri) → A0 zemini.
- **Üst:** yok — A5 içindeki en üst katman.
- A5 içi kenarların tamamı plan §3'te tanımlanmadı → **UNKNOWN**.

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-064 | Electronics Platform Architecture |
| ADR-090 | Kanal Varyant Ürün Ailesi (mono → 8+1 SKU) |

## §5 Durum
**PLANNED** — ADR-064 dosyası diskte (`.ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md`); üretim süreci/üretici kanıtı yok.

## §6 Risk / Not
- Üretim agent'ı AGENTS §4 registry'sinde yok → sahiplik **UNKNOWN** (geçici: audio-hardware-engineer).

---

## §7 Makro Katman Karşılığı (K0–K4)

| Bu K | §6 durumu | Makro eşleme | Makro kimliği |
|---|---|---|---|
| K020 | PLANNED | **Yok** — A5 (K016–K020) makro K0–K4'e girmez | A5 (AGENTS §5) · `[[../katmanli-mimari-k0-k4/index]]` §6.1 |

- Eşleme: `[[../00-master-index]]` §8 · kenarlar: `[[../katman-baglilik-matrisi]]` §4 · makro tanımı: `[[../katmanli-mimari-k0-k4/index]]` §1.1 · §6.1.
- K020, A5'in **en üst katmanıdır** (§3) — yine de makro K0–K4 hiyerarşisine girmez; ayrı düğüm ayrı grafik demektir ve **çizilmez**.

## §8 Arayüz (Girdi/Çıktı & API)

| Kenar | Yön | Girdi → Çıktı | Kanıt |
|---|---|---|---|
| K019 → K020 | girdi | BOM/imalat dosyaları (Gerber, IPC-2581, netlist, BOM CSV) | K020 §2-3 · ADR-063 §2(c)/(d) |
| K016/K017/K018 → K020 | girdi | bileşen şartnameleri → üretim planı | K020 §3 |
| ADR-090 → K020 | SKU | kanal varyant SKU matrisi → üretim planı | K020 §2-2 · ADR-090 authority |
| K020 → dış | çıktı | seri üretim çıktısı / ürün | K020 §2-1 |
| Üst katman | — | **yok** (A5 içinde en üst) | K020 §3 |

## §9 Hata Modları ve Güvenlik Sınırı

| Hata modu | Sonuç | Belirlenmiş kontrol | Kanıt durumu |
|---|---|---|---|
| Şema↔BOM↔layout uyumsuzluğu | hatalı üretim | tek revizyon seti + ChangeRec kaydı | ADR-063 §2(c) — PLANNED (CAD=0) |
| İşçilik kalitesi düşük | saha arızası | IPC-A-610 **Class 2** %100 görsel kabul | ADR-063 §2 (`k20:118` alıntısı) |
| Fabrika testi eksik | ölçülmemiş ürün | AES17-2020 M1–M6 + %100 elektrik test | ADR-063 §2(e) (ADR-038'e bağlanır) |
| Bileşen ömrü / obsolescence | tedarik kırılması | IPC-6012 §4 + IEC 62402 (ADR-061'e bağlı) | ADR-063 §2(e) — "yeniden alınmaz" |
| Sahiplik belirsizliği | sorumsuz üretim | **Kontrol yok** — üretim agent'ı registry'de yok | K020 §6 |

**Güvenlik sınırı:** K020 **üretim çıktısı** üretir, yazılım güvenlik katmanına (K006) dokunmaz. Üretim dosyaları henüz **var değil** (PLANNED); dokunulmaz yüzeyler (kök AGENTS §16.4: `*.php`/`*.js`/`*.css`/`*.sql`, `.ai/.png/**`) bu katmanda **üretilmez, yalnız tüketilir**.

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

1. **UNKNOWN** — A5 içi kenarların **tamamı** plan §3'te tanımlanmadı (§3); §8'deki kenarlar K020 §2-§3 + ADR-063/090 okumasından türetilmiştir.
2. **⚠️ VERIFICATION REQUIRED (öncelikli)** — K020 §2-1, ADR-064'ü *"elektronik platform **üretim mimarisi***" olarak atfeder. ADR-064'ün authority/künyesi ise *"L0-L6 · 5 Cihaz Sınıfı · Cihaz↔Servis Eşleme Matrisi"* dir ve dosyada `üretim|production|BOM|manufactur` araması = **0 isabet** (grep, 2026-10-10). K020 ↔ ADR-064 bağının kapsamı **teyit edilmedi**.
3. **UNKNOWN** — üretim süreci/üretici kanıtı yok (§5); ADR-063'ün dokümantasyon ve AES17 standardı **PLANNED/☐**.
4. **UNKNOWN** — üretim agent'ı AGENTS §4 registry'sinde yok (§6) → sahiplik geçici atamadır.
5. **⚠️ VERIFICATION REQUIRED** — ADR-064 §20 künyesi ADR-063'ü *"tasarım standartları — kapsam dışı"* der; üçlü sınır (ADR-061/062/063/064) korunur → K020 bu ADR'lerin konusunu **yeniden almaz**.
