---
title: "donanaım-type/cables — Örnekler (Katman Ayrım Örnekleri, Çalışma Sayfaları, Şablonlar)"
type: kod
node_id: K001-donanım/donanaım-type/cables/ornekler
node_type: donanaım-type-ornekler
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - index.md
  - cables-kapsam.md
  - cables-dogrulama.md
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Repo kod ölçümü 2026-10-13: *.cpp=0 · *.h=0 · *.c=0 · *.ino=0 · *.xc=0 · *.cs=0 · *.go=0 · *.rs=0 · *.py=1 → kablo/konnektör GERÇEK KODU YOK"
  - "Bu dosya yalnızca ŞABLON ve ÇALIŞMA SAYFASI içerir — gerçek üretim kodu değil, doldurulacak formlar"
validation: "proposed — 2026-10-13 yazıldı; anayasa revizyonu YAPILMADI (kapsam §0); 0 gerçek kod"
open_questions:
  - "Q-C11: kablo/konnektör kod yüzeyi (pin eşleme, envanter scripti) ne zaman yazılacak? — bu turda YOK"
group: kod
date: 2026-10-13
updated: 2026-10-13
---

# cables — Örnekler (K001-donanım · donanaım-type · cables)

> Bu dosya `cables/` sınıfının **örnek/şablon** kaydıdır. **Önemli (Truth Mode):**
> bu klasörde gerçek kablo/konnektör **kod dosyası YOKTUR** (repo ölçümü 2026-10-13:
> `*.cpp=0 · *.h=0 · *.c=0 · *.ino=0 · *.py=1`); bu dosyadaki her şey
> **doldurulacak şablon / çalışma sayfası**dır — üretim kodu değil, uydurma kod değil.
> Kapsam/çelişki: `cables-kapsam.md` §0 · kapılar: `cables-dogrulama.md` §2.

---

## §1 Gerçek Repo Kodu — YOK (Kanıtla)

| # | Dosya tipi | Repo sayısı | Yorum |
|---|---|---|---|
| 1 | `*.cpp` | 0 | kablo/konnektör kodu yok |
| 2 | `*.h` | 0 | — |
| 3 | `*.c` | 0 | — |
| 4 | `*.ino` | 0 | — |
| 5 | `*.xc` | 0 | — |
| 6 | `*.cs` | 0 | — |
| 7 | `*.go` | 0 | — |
| 8 | `*.rs` | 0 | — |
| 9 | `*.py` | 1 | alakasız (vault scripti) |
| 10 | `*.php` | 9470 | web uygulaması — kablo kodu DEĞİL |
| 11 | `*.js` | 1682 | frontend — kablo kodu DEĞİL |

**Sonuç:** `cables/` sınıfının kod yüzeyi **boştur**; bu dosya yalnızca gelecekteki
kod/ölçüm oturumları için **şablon** sağlar. Uydurma üretim kodu yazılmaz.

## §2 Katman Ayrım Örneği — Doğru/Yanlış Kayıt

> Bu bölüm `cables-kapsam.md` §4 katman kuralının **uygulama örneğidir**; şablondur.

### §2.1 Yanlış (katman karışımı — K-C2 RED)

```text
YANLIŞ KAYIT:
  type     : "USB 3.2 Gen 2 10 Gbps USB-C kablosu"
  neden    : KATMAN-1 (montaj) + KATMAN-4 (protokol) tek tipte birleşti.
  sonuç    : K-C2 RED — protokol, tip tanımı olamaz.
```

### §2.2 Doğru (katman ayrımı — K-C2 GEÇ)

```text
DOĞRU KAYIT:
  type_id      : C-T02
  name_tr      : "USB Type-C kablo montajı"
  category     : cables
  subcategory  : veri-baglanti
  katman       : KATMAN-1 (montaj) + KATMAN-2 (Type-C konektör)
  protocols    : "USB 2.0/3.x · Power Delivery — yalnız etiket (katman-4)"
  kanıt        : usb.org 200 (HEAD 2026-10-13)
  durum        : proposed
```

### §2.3 Katman etiketleme şablonu (her kayıt için)

```text
KAYIT ŞABLONU (26 alan — doldurulacak):
  [ 1] type_id                      : C-T__
  [ 2] name_tr                      : 
  [ 3] name_en                      : 
  [ 4] category                     : cables
  [ 5] subcategory                  : veri-baglanti | kart-arasi-dijital | ekran-kablo | analog-kesit | guc-baglanti | konnektor-sinifi | donusturucu
  [ 6] description                  : 
  [ 7] physical_component_or_device : 
  [ 8] primary_function             : 
  [ 9] interfaces                   : 
  [10] protocols                    :  (yalnız etiket — katman-4)
  [11] power_requirements           : 
  [12] operating_environment        : 
  [13] os_dependencies              :  (K000)
  [14] driver_requirements          :  (K000)
  [15] firmware_dependency          :  (K000)
  [16] hardware_abstraction_requirement : 
  [17] audio_relevance              : 
  [18] coremusic_relevance          : 
  [19] parent_domain                : K001-donanım
  [20] owner_domain                 :  (K001 envanter · K000 sürücü · K002 devre)
  [21] existing_document            : 
  [22] evidence                     : 
  [23] status                       : proposed
  [24] confidence                   : low | medium | high
  [25] open_questions               : 
  [26] proposed_path                : donanaım-type/cables/
```

## §3 Pin/Katman Çalışma Sayfaları (BOŞ — doldurulacak)

### §3.1 Pin Eşleme Çalışma Sayfası (KATMAN-2 · S5)

| # | Tip | Pin adı | Pin no | Yön (in/out/bidir) | Karşılık | Doğrulandı mı? |
|---|---|---|---|---|---|---|
| 1 | C-T02 | — | — | — | — | ❌ BOŞ |
| 2 | C-T05 | BCLK | — | — | — | ❌ BOŞ |
| 3 | C-T05 | LRCLK | — | — | — | ❌ BOŞ |
| 4 | C-T05 | DATA | — | — | — | ❌ BOŞ |
| 5 | C-T06 | SCLK | — | — | — | ❌ BOŞ |
| 6 | C-T06 | MOSI | — | — | — | ❌ BOŞ |
| 7 | C-T06 | MISO | — | — | — | ❌ BOŞ |
| 8 | C-T06 | CS | — | — | — | ❌ BOŞ |
| 9 | C-T07 | SDA | — | — | — | ❌ BOŞ |
| 10 | C-T07 | SCL | — | — | — | ❌ BOŞ |
| 11 | C-T08 | TX | — | — | — | ❌ BOŞ |
| 12 | C-T08 | RX | — | — | — | ❌ BOŞ |
| 13 | C-T09 | GPIO_x | — | — | — | ❌ BOŞ |
| 14 | C-T13 | XLR-1 (GND) | — | — | — | ❌ BOŞ |
| 15 | C-T13 | XLR-2 (HOT) | — | — | — | ❌ BOŞ |
| 16 | C-T13 | XLR-3 (COLD) | — | — | — | ❌ BOŞ |
| 17 | C-T16 | M.2 pin | — | — | — | ❌ BOŞ |

### §3.2 Katman Doğrulama Çalışma Sayfası (KATMAN-3 · S4)

| # | Tip | Ölçüm | Hedef | Gerçek | Alet | Tarih |
|---|---|---|---|---|---|---|
| 1 | C-T01 | VBUS DC | 5 V ⚠️ | — | — | — |
| 2 | C-T02 | VBUS PD | ⚠️ | — | — | — |
| 3 | C-T03 | empedans çift | ⚠️ (pcisig 403) | — | — | — |
| 4 | C-T10 | empedans çift | ⚠️ | — | — | — |
| 5 | C-T11 | empedans çift | ⚠️ | — | — | — |
| 6 | C-T12 | empedans | 75 Ω ⚠️ (aes 403) | — | — | — |
| 7 | C-T13 | empedans dengeli | ⚠️ | — | — | — |
| 8 | C-T15 | AC gerilim | şebeke ⚠️ | — | — | — |

### §3.3 Envanter Çalışma Sayfası (KATMAN-2 · S5)

| # | Tip | Marka | Model | Kesit | Uzunluk | Pin | Kaynak | Tarih |
|---|---|---|---|---|---|---|---|---|
| 1 | C-T01 | UNKNOWN | UNKNOWN | — | — | — | — | — |
| 2 | C-T02 | UNKNOWN | UNKNOWN | — | — | — | — | — |
| 3 | C-T12 | UNKNOWN | UNKNOWN | — | — | — | — | — |
| 4 | C-T13 | UNKNOWN | UNKNOWN | — | — | — | — | — |
| 5 | C-T15 | UNKNOWN | UNKNOWN | — | — | — | — | — |

## §4 Hata Akışı Şablonları (Red Team — uydurma DEĞİL, boş kalıplar)

### §4.1 Ölçüm Hatası Akışı

```text
[1] Ölçüm başlar (alet + tarih kaydedilir)
  → [2] Değer hedef aralıkta mı?
      → EVET  : §3.2 formuna yaz · kaydın evidence alanına işlenir
      → HAYIR : §4.2'ye geç
```

### §4.2 Kanıt Uyuşmazlığı Akışı

```text
[1] Ölçüm/iddia vault atfıyla uyuşmuyor
  → [2] Atıf taze mi? (satır no güncel mi — bkz. Q-C4 stale atıf)
      → EVET  : iddia REVİZE edilir (kaynak lehine)
      → HAYIR : ⚠️ VERIFICATION REQUIRED etiketi kalır
  → [3] 3 turda çözülmezse: DUR + üst karar (Vault Steward)
```

### §4.3 URL Red Akışı (K-C4)

```text
[1] Kaynak URL HEAD ile test edilir (2026-10-13 standardı)
  → [2] HTTP 200/202?
      → EVET  : KULLAN · §4.1 tablosuna kaydet
      → 403   : RED · ⚠️ VERIFICATION REQUIRED (alan adı 403) · §4.2'ye kaydet
      → 404   : RED · kullanma
      → timeout/DNS : RED · kullanma
  → [3] RED kaynak asla 200'ün yerine KONMAZ
```

### §4.4 Çelişki Kapı Akışı (K-C8)

```text
[1] Kapsam dosyası yazıldı (status: proposed)
  → [2] Vault Steward anayasa revizyonunu onayladı mı?
      → EVET  : anayasa §2/§10/§12 + kapsam §2/A6/§7 revize · status: confirmed
      → HAYIR : status: proposed KALIR · dosyalar korunur · raporlanır
  → [3] Onay olmadan metin DEĞİŞTİRİLMEZ (frozen ADR benzeri koruma)
```

## §5 Doldurulmuş Örnek Kayıt (şablon — C-T01 mantığı, gerçeğin provası)

> Aşağıdaki kayıt **örnek/şablondur**; `cables-kapsam.md` §5'teki C-T01 kaydının
> **görsel provası** olarak kullanılır. Değerler aynıdır (uydurma yok).

| # | Alan | Değer |
|---|---|---|
| 1 | `type_id` | C-T01 |
| 2 | `name_tr` | USB-A kablo montajı |
| 3 | `name_en` | USB-A cable assembly |
| 4 | `category` | cables |
| 5 | `subcategory` | veri-baglanti |
| 6 | `description` | USB-A uçlu fiziksel data kablosu; katman-1/2 |
| 7 | `physical_component_or_device` | kablo montajı + USB-A konektörler |
| 8 | `primary_function` | konak↔çevre birimi arası fiziksel taşıma |
| 9 | `interfaces` | USB-A erkek/yuva |
| 10 | `protocols` | USB 2.0/3.x — yalnız etiket (katman-4) |
| 11 | `power_requirements` | VBUS tipik 5 V ⚠️ VERIFY REQUIRED |
| 12 | `operating_environment` | indoor masaüstü ⚠️ |
| 13 | `os_dependencies` | K001'de değil — K000 |
| 14 | `driver_requirements` | K001'de değil — K000 |
| 15 | `firmware_dependency` | K001'de değil — K000 |
| 16 | `hardware_abstraction_requirement` | tip/envanter kaydı — soyutlama K000 |
| 17 | `audio_relevance` | USB ses arabirimi için taşıyıcı |
| 18 | `coremusic_relevance` | envanter tipi (6. alt bağlam) |
| 19 | `parent_domain` | K001-donanım |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 |
| 22 | `evidence` | repo: USB=258 · usb.org 200 |
| 23 | `status` | proposed |
| 24 | `confidence` | medium |
| 25 | `open_questions` | Q-C1 · Q-C3 |
| 26 | `proposed_path` | `donanaım-type/cables/` |

## §6 Kapı Formu (K-C6/K-C7 ölçüm oturumu — BOŞ)

```text
KAPı FORMU (doldurulacak):
  oturum ID   : 
  tarih       : 
  alet        : 
  tip         : 
  ölçüm türü  : empedans | DC direnç | kesit | uzunluk | gerilim | akım | süreklilik
  hedef değer : 
  ölçülen     : 
  sonuç       : GEÇ | GEÇMEDİ
  kaydeden    : 
```

## §7 Kapanış Notu ve Kullanım Kuralı

| Kural | Ayrıntı |
|---|---|
| Bu dosya üretim kodu değildir | yalnız şablon/çalışma sayfası — gerçek kod ölçümü §1'de 0 |
| Uydurma ölçüm/URL/marka yazılmaz | her boş hücre `—` veya `UNKNOWN` taşır |
| Doldurma sahibi | Vault Steward ölçüm oturumu (K-C6/K-C7) |
| Kapsam/çelişki | `cables-kapsam.md` §0 · kapılar: `cables-dogrulama.md` §2 |
| Kapanış | anayasa revizyonu onayı + ölçüm + envanter (kapsam §0.1) |

## §8 Tip-Bazlı Katman Provası (17 tip × 4 katman — doldurulacak)

### §8.1 C-T01 · USB-A

```text
KATMAN-1 (kablo)      : montaj — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : USB-A erkek/yuva — pin: —
KATMAN-3 (elektriksel): VBUS 5 V ⚠️ · GND · D+/D- ⚠️
KATMAN-4 (protokol)   : USB 2.0/3.x — etiket (usb.org 200)
```

### §8.2 C-T02 · USB Type-C

```text
KATMAN-1 (kablo)      : montaj — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : Type-C — pin: ⚠️ VERIFY REQUIRED
KATMAN-3 (elektriksel): VBUS PD 5-20 V ⚠️ · CC pinleri
KATMAN-4 (protokol)   : USB 2.0/3.x · Power Delivery — etiket (usb.org 200)
```

### §8.3 C-T03 · PCIe

```text
KATMAN-1 (kablo)      : riser/mezra — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : PCIe x1…x16 yuva — pin: ⚠️
KATMAN-3 (elektriksel): diferansiyel çift ⚠️ empedans (pcisig 403)
KATMAN-4 (protokol)   : PCI Express — etiket ⚠️ (pcisig.com 403)
```

### §8.4 C-T04 · SATA

```text
KATMAN-1 (kablo)      : veri + güç montajı — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : SATA 7-pin data · 15-pin power
KATMAN-3 (elektriksel): diferansiyel ⚠️ (snia.org 403)
KATMAN-4 (protokol)   : AHCI/NVM — etiket (sata-io.org 200)
```

### §8.5 C-T05 · I2S/TDM

```text
KATMAN-1 (kablo)      : düz kablo/FPC — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : pin-header — pin: BCLK · LRCLK · DATA ⚠️
KATMAN-3 (elektriksel): mantık seviyesi ⚠️
KATMAN-4 (protokol)   : I2S · TDM — etiket (kernel/sound 200)
```

### §8.6 C-T06 · SPI

```text
KATMAN-1 (kablo)      : düz kablo/FPC — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : pin-header — SCLK · MOSI · MISO · CS ⚠️
KATMAN-3 (elektriksel): mantık seviyesi ⚠️
KATMAN-4 (protokol)   : SPI — etiket (kernel/spi 200)
```

### §8.7 C-T07 · I2C

```text
KATMAN-1 (kablo)      : düz kablo/FPC — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : pin-header — SDA · SCL ⚠️
KATMAN-3 (elektriksel): open-drain + pull-up ⚠️
KATMAN-4 (protokol)   : I2C — etiket (kernel/i2c 200)
```

### §8.8 C-T08 · UART

```text
KATMAN-1 (kablo)      : düz kablo — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : pin-header — TX · RX · GND ⚠️
KATMAN-3 (elektriksel): TTL seviyesi ⚠️
KATMAN-4 (protokol)   : seri (UART) — etiket (kernel/tty 200)
```

### §8.9 C-T09 · GPIO

```text
KATMAN-1 (kablo)      : jumper kablo — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : pin-header — GPIO_x ⚠️
KATMAN-3 (elektriksel): mantık seviyesi ⚠️
KATMAN-4 (protokol)   : yok (ham dijital I/O)
```

### §8.10 C-T10 · DisplayPort

```text
KATMAN-1 (kablo)      : montaj — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : DP — pin: ⚠️ VERIFY REQUIRED
KATMAN-3 (elektriksel): diferansiyel çift ⚠️
KATMAN-4 (protokol)   : DisplayPort — etiket (displayport.org 200)
```

### §8.11 C-T11 · HDMI

```text
KATMAN-1 (kablo)      : montaj — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : HDMI — pin: ⚠️ VERIFY REQUIRED
KATMAN-3 (elektriksel): diferansiyel çift (TMDS) ⚠️
KATMAN-4 (protokol)   : HDMI — etiket (hdmi.org 200)
```

### §8.12 C-T12 · Koaksiyel

```text
KATMAN-1 (kablo)      : 75 Ω koaksiyel ⚠️ ölçüm yok — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : RCA / BNC ⚠️
KATMAN-3 (elektriksel): tek uçlu ⚠️ empedans (aes.org 403)
KATMAN-4 (protokol)   : S/PDIF — etiket ⚠️
```

### §8.13 C-T13 · Dengeli (XLR)

```text
KATMAN-1 (kablo)      : twist-pair + kalkan — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : XLR 3-pin (GND/HOT/COLD) ⚠️
KATMAN-3 (elektriksel): diferansiyel (dengeli) ⚠️ (aes.org 403)
KATMAN-4 (protokol)   : analog ses — protokol yok
```

### §8.14 C-T14 · Dengesiz (RCA)

```text
KATMAN-1 (kablo)      : koaksiyel tek uçlu — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : RCA 2-pin ⚠️
KATMAN-3 (elektriksel): tek uçlu ⚠️ (aes.org 403)
KATMAN-4 (protokol)   : analog ses — protokol yok
```

### §8.15 C-T15 · Güç

```text
KATMAN-1 (kablo)      : güç kablosu — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : IEC 60320 · ATX · Molex · SATA-power · barrel ⚠️
KATMAN-3 (elektriksel): AC şebeke / DC ⚠️
KATMAN-4 (protokol)   : yok (güç taşıyıcı)
```

### §8.16 C-T16 · Konnektör Sınıfları

```text
KATMAN-1 (kablo)      : — (konnektör sınıfı)
KATMAN-2 (konektör)   : pin-header · FPC/FFC · card-edge · M.2 ⚠️
KATMAN-3 (elektriksel): akım kapasitesi ⚠️ (formfactors DNS fail)
KATMAN-4 (protokol)   : yok (fiziksel geçiş)
```

### §8.17 C-T17 · Adapter

```text
KATMAN-1 (kablo)      : adaptör gövdesi + iki uç — kesit: — · uzunluk: —
KATMAN-2 (konektör)   : USB-C ↔ USB-A ⚠️ · DP ↔ HDMI ⚠️
KATMAN-3 (elektriksel): ⚠️ (pasif/aktif)
KATMAN-4 (protokol)   : protokol dönüştürme (aktif) ⚠️ — etiket
```

## §9 Envanter Toplama Şablonu (S5 — bir sonraki oturum)

```text
ENVANTER ŞABLONU (doldurulacak):
  tip         : C-T__
  marka       : UNKNOWN
  model       : UNKNOWN
  kesit       : 
  uzunluk     : 
  pin sayisi   : 
  kaynak      : (görsel · multimetre · üretici dokümanı)
  tarih       : 
  kaydeden    : 
```

## §10 Ölçüm Oturumu Akışı (K-C6 için hazır kalıp)

```text
[1] Oturum başlat (ID + tarih + alet kaydı)
  → [2] §3.1–3.4 formlarından hedef seç
  → [3] Ölçümü yap (uydurma YASAK — alet + değer + tarih)
  → [4] Sonucu forma yaz
      → GEÇ     : kaydın evidence alanına işle (kapsam §5)
      → GEÇMEDİ : §4.2 akışına göre işaretle
  → [5] Tüm formlar doldu mu?
      → EVET  : K-C6 KAPANIR · kayıtlar `confirmed`'a geçebilir
      → HAYIR : oturum tekrarlanır · `proposed` KALIR
```

## §11 Kısaltmalar (bu dosya)

| Kısaltma | Anlam |
|---|---|
| S4/S5 | Fiziksel ölçüm / envanter seviyesi |
| K-C6/K-C7 | Ölçüm / envanter kapıları |
| NP | NOT PROVIDED |
| VR | VERIFY REQUIRED |

## §12 Tip-Bazlı Eksiklik Matrisi (şablon — hangi tip ne bekliyor)

| Tip | KATMAN-1 eksik | KATMAN-2 eksik | KATMAN-3 eksik | KATMAN-4 durum |
|---|---|---|---|---|
| C-T01 | kesit · uzunluk | pin | VBUS 5 V ⚠️ | etiket ✅ (usb.org 200) |
| C-T02 | kesit · uzunluk | pin ⚠️ | VBUS PD ⚠️ | etiket ✅ (usb.org 200) |
| C-T03 | uzunluk | yuva pin ⚠️ | empedans ⚠️ | etiket ❌ (pcisig 403) |
| C-T04 | kesit · uzunluk | pin | güç hattı ⚠️ | etiket ✅ (sata-io 200) |
| C-T05 | kesit · uzunluk | BCLK/LRCLK/DATA | mantık ⚠️ | etiket ✅ (kernel/sound 200) |
| C-T06 | kesit · uzunluk | SCLK/MOSI/MISO/CS | mantık ⚠️ | etiket ✅ (kernel/spi 200) |
| C-T07 | kesit · uzunluk | SDA/SCL | pull-up ⚠️ | etiket ✅ (kernel/i2c 200) |
| C-T08 | kesit · uzunluk | TX/RX | TTL ⚠️ | etiket ✅ (kernel/tty 200) |
| C-T09 | kesit · uzunluk | GPIO_x | mantık ⚠️ | protokol yok |
| C-T10 | kesit · uzunluk | pin ⚠️ | empedans ⚠️ | etiket ✅ (displayport.org 200) |
| C-T11 | kesit · uzunluk | pin ⚠️ | TMDS ⚠️ | etiket ✅ (hdmi.org 200) |
| C-T12 | kesit ⚠️ · uzunluk | RCA/BNC | 75 Ω ⚠️ | etiket ⚠️ (aes.org 403) |
| C-T13 | kesit ⚠️ · uzunluk | XLR-3 | dengeli ⚠️ | analog — protokol yok |
| C-T14 | kesit ⚠️ · uzunluk | RCA-2 | tek uçlu ⚠️ | analog — protokol yok |
| C-T15 | kesit · uzunluk | IEC/ATX/Molex | AC/DC ⚠️ | protokol yok |
| C-T16 | — | pin-header/FPC/M.2 ⚠️ | akım ⚠️ | protokol yok |
| C-T17 | uzunluk | iki uç ⚠️ | pasif/aktif ⚠️ | etiket ⚠️ |

**Toplam eksik:** 17 tip × (KATMAN-1 + KATMAN-2 + KATMAN-3) = 51 eksik alan ·
0/51 dolduruldu (bu oturumda ölçüm/envanter YOK) → tüm tipler `proposed`.

## §13 Tip → Dosya Referans Matrisi (17 tip — hangi tip hangi dosyada)

| # | Tip | Kapsam kaydı | Doğrulama kaydı | Örnek/şablon |
|---|---|---|---|---|
| 1 | C-T01 USB-A | kapsam §5-1 | dogrulama §9 · §17 | ornekler §8.1 |
| 2 | C-T02 USB-C | kapsam §5-2 | dogrulama §9 · §17 | ornekler §8.2 |
| 3 | C-T03 PCIe | kapsam §5-3 | dogrulama §9 · §17 | ornekler §8.3 |
| 4 | C-T04 SATA | kapsam §5-4 | dogrulama §9 · §17 | ornekler §8.4 |
| 5 | C-T05 I2S/TDM | kapsam §5-5 | dogrulama §9 · §17 | ornekler §8.5 |
| 6 | C-T06 SPI | kapsam §5-6 | dogrulama §9 · §17 | ornekler §8.6 |
| 7 | C-T07 I2C | kapsam §5-7 | dogrulama §9 · §17 | ornekler §8.7 |
| 8 | C-T08 UART | kapsam §5-8 | dogrulama §9 · §17 | ornekler §8.8 |
| 9 | C-T09 GPIO | kapsam §5-9 | dogrulama §9 · §17 | ornekler §8.9 |
| 10 | C-T10 DisplayPort | kapsam §5-10 | dogrulama §9 · §17 | ornekler §8.10 |
| 11 | C-T11 HDMI | kapsam §5-11 | dogrulama §9 · §17 | ornekler §8.11 |
| 12 | C-T12 Koaksiyel | kapsam §5-12 | dogrulama §9 · §17 | ornekler §8.12 |
| 13 | C-T13 Dengeli | kapsam §5-13 | dogrulama §9 · §17 | ornekler §8.13 |
| 14 | C-T14 Dengesiz | kapsam §5-14 | dogrulama §9 · §17 | ornekler §8.14 |
| 15 | C-T15 Güç | kapsam §5-15 | dogrulama §9 · §17 | ornekler §8.15 |
| 16 | C-T16 Konnektör | kapsam §5-16 | dogrulama §9 · §17 | ornekler §8.16 |
| 17 | C-T17 Adapter | kapsam §5-17 | dogrulama §9 · §17 | ornekler §8.17 |

> **Kapsam içi tutarlılık:** her tipin üç dosyada da karşılığı vardır; eksik
> karşılık = şema ihlali (K-C3). Bu matris K-C3'ün görsel kanıtıdır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
