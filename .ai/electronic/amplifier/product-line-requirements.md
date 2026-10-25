---
type: requirements
category: amplifier-architecture
title: "CoreMusic Electronics — Amfi Ürün Hattı & Güç/Batarya Gereksinimleri"
date: 2026-10-25
updated: 2026-10-25
status: active
version: 1.0.0
authority: "Derived — SSOT: kullanıcı kararı 2026-10-25 · ADR-111 (draft yazıldı 2026-10-25 · next 112)"
governance: Red Team · Human Mode · Truth Mode
---

# Amfi Ürün Hattı & Güç/Batarya Gereksinimleri

**Zorunlu Bağlantılar:** [[index]] · [[class-ab]] · [[class-d]] · [[power-supply]] · [[protection]] · [[thermal]] · [[hardware/index]] · [[brain.md]]

**Etiketler:** `KULLANICI KARARI` = sahibin 2026-10-25 beyanı (ölçüm değildir) · `⚠️ VERIFICATION REQUIRED` = hesap/ölçüm/kaynak bekliyor · `PLANNED` = kod/üretim yok. Hesapsız güç/termal iddia bu dosyaya yazılmaz (hardware-electronics skill §5 kapısı).

---

## §1 Kapsam & Kaynak

| Kalem | Değer |
|---|---|
| Kaynak | Kullanıcı kararı, 2026-10-25 OpenCode oturumu (güç sınıfı + ürün hattı + koruma modu cevapları) |
| Kapsam | Amfi ürün hattı SKU'ları · azami watt/kanal kuralı · yazılım Power Governor · 18650+adaptör politikası |
| Kapsam dışı | Şema/PCB/BOM/firmware üretimi (skill kural 6 → `dsp-firmware-engineer` / `embedded-systems`); bu dosya yalnız gereksinim kaydıdır |
| Bağlı ADR | ADR-038 (DAC zinciri) · ADR-061/064 (L0-L6 elektronik platform) · ADR-089 (8×50W studio/home SKU — **dokunulmaz, korunur**) · **ADR-111 = draft YAZILDI (2026-10-25) — next 112** |
| Bağlı envanter | `PROJECTS.md` §7.2.1-7.2.3 (revize edilecek) · `VISION.md` §10.2 (SKU satırı eklenecek) · `PRD.md` §5 (`pro` yüzeyi YOK) |

---

## §2 Ürün Hattı — SKU Matrisi (KULLANICI KARARI 2026-10-25)

| # | SKU | Kanal | Kanal gücü | Bas / +1 | Durum |
|---|---|---|---|---|---|
| 1 | Mono küçük | 1 | 15 W | — | KULLANICI KARARI |
| 2 | Mono büyük | 1 | 35 W | — | KULLANICI KARARI |
| 3 | 2 kanal | 2 | 35 W/kanal | — | KULLANICI KARARI |
| 4 | 2+1 | 2 + 1 bas | 35 W/kanal (bu okuma ile) ⚠️ | 10 W bas ⚠️ **NETLEŞTİRİLECEK** | OKUMA BELİRSİZ |
| 5 | 4 kanal | 4 | 60 W/kanal | — | KULLANICI KARARI |
| 6 | 5+1 | 5 + 1 bas | 50 W/kanal | 20 W | KULLANICI KARARI |
| 7 | 6+1 | 6 + 1 bas | 80 W/kanal | 30 W | KULLANICI KARARI |
| 8 | 7+1 | 7 + 1 bas | 100 W/kanal | 50 W | KULLANICI KARARI |
| 9 | 8+1 | 8 + 1 bas | **500 W/kanal (azami)** | 50 W bas · opsiyonel 1000 W'lık kabin için **100 W** kanal (kabin = rating dayanım, sürücü 100 W ile çalışır — kullanıcı 2026-10-25) | bütçe KAPANDI |

### §2.1 Genel güç kuralları (KULLANICI KARARI)

- **Azami kanal sayısı: 8.**
- **Her bir çıkışın azami gücü: 500 W** (8+1 SKU'su bu tavana oturur; üstü kabul edilmez).
- 2+1 satırı ham ifade "1 bas 10w bas 35w kanal başına" şeklinde okundu → **35 W/kanal + 10 W bas** olarak kaydedildi; ters okuma mümkün olduğu için **NETLEŞTİRİLECEK** işaretlidir, sessiz varsayım yapılmaz.

### §2.2 Güç bütçesi — KAPANDI (kullanıcı açıklaması 2026-10-25)

Kullanıcı netleştirdi: eldeki **1000 W'lık bas kabını rating (dayanım) değeridir** — kabin **100 W'lık bas kanalıyla** çalıştırılır (kabin 1000 W'a kadar dayanır, sürücüye yalnız 100 W uygulanır → **alt sürüş, aşırı besleme yok**). Bu nedenle "1000 W ↔ 100 W" çelişkisi **yoktur**; §6'daki P0 madde kapatıldı.

Kalan doğrulama (P1): kabin empedansı (Ω) + sürücü sayısı → Power Governor watt kilidi ve PSU/rail hesabı için girdi; değerler girdi-sonuç formatında yazılacak (ADR-111 §Açık Maddeler).

---

## §3 Power Governor — Yazılım Watt/Kanal Kontrolü (KULLANICI KARARI)

**Amaç:** amfi çıkışı bağlı hoparlörün güç/empedans rating'ini aşmasın; çıkışlar yazılım kontrol panelinden ayarlanabilsin.

| Mod | Davranış | Kaynak |
|---|---|---|
| **Otomatik (default)** | Hoparlör profili seçilir → **empedans doğrulaması** → watt tavanı hoparlör rating'ine kilitlenir | KULLANICI KARARI (Q3) |
| **Manuel** | Panelde kanal başına watt slider'ı + empedans kilidi (4/8/16 Ω) | KULLANICI KARARI |
| **Güvenlik (her iki modda)** | True Peak Brickwall Limiter (mevcut, PRD-PLT-003) + termal geri besleme + acil mute + **her değişiklik audit log'a** | türetilmiş |

**Entegrasyon yüzeyi:** UI = `pro.coremusic.net` (PROJECTS §5 — DSP parametre + amfi telemetrisi) · API = Audio service :9741/:9742 · veri = hoparlör profili tablosu (BCNF → `coremusic_neva` ailesi, data-engineer) · **Mockup gate:** `.ai/ui-design/` görseli okunmadan UI kodu yazılmaz.

---

## §4 Batarya / Adaptör Politikası — Bas Cihazları (KULLANICI KARARI)

Tüm bas cihazları **18650 pil + adaptör** ile çalışır; pil **manuel devre dışı bırakılabilir**. **Açılışta pil şarj devresi otomatik başlamaz.**

| Durum | Tetikleyici | Davranış |
|---|---|---|
| Boot | Cihaz açılır | Şarj devresi **beklemede** — hemen güç verilmez, otomatik şarj başlamaz |
| Adaptör takıldı | Ekranda seçim sorulur | **"Pili devre dışı bırak (bypass)"** → pil devreden çıkar, şarj yok, güç yalnız adaptörden |
| Adaptör takıldı | Ekranda seçim sorulur | **"Şarj et"** → balans şarj devresi devrede, pil şarj olur |
| Pil ile | Adaptör yok | Pilden çalışma (şarj durumu cihaz durumuna göre belirlenir) |

**Gerekçe (kullanıcı):** uzun süreli adaptör kullanımında **pil ömrünü korumak** (gereksiz şarj yıpratmasın) · **maliyeti kısma** · **üretim kalitesini sürdürme** · **ucuz satma**. Birden fazla cihaz + pil senaryosu olacağı için politika **ortak ve katmanlı yazılımda** kalır.

`⚠️ VERIFICATION REQUIRED:` 18650 balans şarj + bypass devresi MPN'leri, şarj akımı, pil ömrü hesabı — ölçüm/kaynak bekliyor.

---

## §5 Uygulama Sırası (Kullanıcı Direktifi)

1. **Güç katmanıyla başla** — L0-L6 katmanlı mimari (ADR-061/064) içinde güç katmanı önce: PSU → rail → koruma → termal.
2. **Paralel yürüt** — 3 parça: (a) donanım güç/termal hesapları · (b) Power Governor yazılımı + mockup · (c) belge/ADR hattı (ADR-111, PROJECTS §7.2.x, VISION §10.2, `pro` PRD satırı).
3. **Kayıt zorunlu** — tüm kararlar `.ai/` vault'a girilir; hafızaya (memory) dayalı tek kaynak kullanılmaz.

---

## §6 Açık Maddeler (ADR-111'e girer)

| # | Madde | Seviye |
|---|---|---|
| 1 | 2+1 SKU netleştirme (35 W/kanal + 10 W bas mı, tersi mi?) | P1 |
| 2 | ~~1000 W bas kabı ↔ 100 W kanal bütçesi~~ → **KAPANDI 2026-10-25** (kullanıcı: kabin rating 1000 W, sürücü 100 W alt sürüş) · kalan: kabin empedansı/sürücü sayısı (P1, Governor kilidi girdisi) | P1 |
| 3 | 8+1 (500 W/kanal) topoloji seçimi — [[index]] §4 verim/ısı tablosu Class D'yi öne çıkarır; **karar ADR-111'de** | **P0** |
| 4 | SKU başına PSU/rail/termal hesabı (girdi+sonuç formatı) | P1 |
| 5 | ADR-111 **taslak YAZILDI** — `draft/ADR-111-amfi-urun-ailesi-power-governor-18650-bypass.md` (2026-10-25); kalan: debate + onay · next **112** | P1 |
| 6 | `PROJECTS.md` §7.2.1-7.2.3 + `VISION.md` §10.2 SKU revizyonu (ADR-089'a dokunmadan) | P1 |
| 7 | `pro.coremusic.net` PRD satırı — `PRD.md` §5 (34 satır) içinde **YOK** | P1 |
| 8 | `.ai/index.md` + `.ai/keys.md` katalog/keyword satırı bu dosya için | P2 |

---

## §7 Değişiklik Günlüğü

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-25 | 1.0.0 | İlk kayıt — kullanıcı kararları (SKU matrisi, Power Governor, 18650 bypass politikası) bu oturumdan vault'a alındı |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-25
**Mode:** Red Team · Human Mode · Truth Mode