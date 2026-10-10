---
type: architecture
category: layer-macro
title: "K0 — İşletim Sistemi Master Index"
date: 2026-10-10
status: active
version: 1.0.0
---

# K0 — İşletim Sistemi & Donanım Soyutlama

> **Makro etiket:** K0 = `K000-isletim-sistemi` + `K001-donanim` (index §5 eşlemesi).
> SSOT: `[[../K000-isletim-sistemi/os-master]]` · `[[../K001-donanim/donanım-master]]` · `[[../katman-baglilik-matrisi.md]]`
> Bu doküman **türetilmiş özdür**; K0 bağımsız katman numarası değildir.

## §1 Kimlik ve Tanım

- **Katman kimliği:** K0 — **türetilmiş etiket** (yeni K numarası SSOT değildir).
- **Mikro karşılıkları:** `K000-isletim-sistemi` (IMPLEMENTED) · `K001-donanim` (PLANNED).
- **Kapsam:** İşletim sistemi/barındırma zemini (subdomain kökleri, paylaşımlı composer autoload, DB kayıt/erişim zemini) + donanım soyutlaması (hedef donanım bileşenleri, fiziksel üretim zincirine giden A5'in zemini).
- **Temel amaç:** Tüm üst katmanların (K1–K4) dayandığı **en dip zemin** olmak; donanım gerçekliğini üst katmanlardan gizlemek (Information Hiding). K0 hiçbir katmanı çağırmaz — yalnız çağrılır.
- **A5 ilişkisi:** K016–K020 (A5 fiziksel üretim zinciri) makro K0–K4'e girmez; **K0'un hedef donanımıdır** (index §6.1).

## §2 Sorumluluklar

1. **Subdomain kök zemini:** 5 subdomain barındırma zemini — `api.` `auth.` `home.` `media.` `assets.coremusic.net`. Composer zemini olan subdomain = **4** (`api.` `auth.` `home.` `media.` — her birinde `composer.json`); `assets.coremusic.net/` altında **composer.json YOK** (disk ölçümü; kasıt UNKNOWN). `AGENTS §25.2` "3 composer.json" iddiası diskteki 5 (shared + 4 subdomain) ile çelişir → §6.3/index §7-18.
2. **Paylaşımlı autoload:** `shared/composer.json` ile paylaşımlı sınıfların autoload zemini (K000 — `shared/src/**` bu zeminden yüklenir).
3. **DB kayıt/erişim zemini:** `shared/src/Database/DatabaseManager.php` · `shared/src/Database/DatabaseRegistry.php` — veri katmanının (K005) dayandığı fiziksel erişim zemini (K000).
4. **Karar kayıtları:** ADR-015 · ADR-082 · ADR-085 (K000 — başlıklar/durumlar için SSOT `[[../K000-isletim-sistemi/index]]` §6).
5. **Donanım hedefi (PLANNED):** ADR-061 (L6 electronics) · ADR-038 (PCM3168A + XMOS) — K001 için **yalnız ADR kanıtı** vardır; disk kanıtı YOK.
6. **Barındırma işletim sistemi soyutlaması:** Tüm yazılım katmanlarının OS'ye doğrudan dokunmak yerine K0 zeminine dayanması (K000 zemin sağlar; K001 donanım tarafını hedefler).

## §3 Bağımlılık Kuralları

- **İzinli alt katmanlar:** **YOK** — K0 en dip makro katmandır; hiçbir katmana bağımlı değildir (SSOT matris §2: yalnız `K001→K000` mikro kenarı içundedir).
- **İzinli üst katmanlar (çağıranlar):** K1 (mikro: `K002→K001` · `K001→K000`), K2 (mikro skip: `K005→K000`, `K006→K000`, `K007→K000` — §6.2 istisna kaydı), cross-cutting (mikro: `K012→K000`, `K013→K000`), A5 (aralık bağımlılığı "A0 zemini (K000–K005)").
- **Kesinlikle yasak:**
  - K0'ın bir üst katmanı (K1–K4) **import etmesi** — dairesel bağımlılık.
  - K0'ın iç-detayının (DB kayıt düzeni, composer kasıtı, subdomain dosya düzeni) üst katmanlara **sızması**; üst katman yalnız yayınlanan zemin/özelliği kullanır.
  - K001'in (donanım) PLANNED iken **varmış gibi** sunulması (uydurma IMPLEMENTED).

### §3.1 K0 Kenar Özeti (SSOT matris §2'den — ikinci matris üretilmez, yalnız gösterim)

| Kenar | Yön | Dayanak | Makro durum |
|---|---|---|---|
| K001 → K000 | aşağı | plan §2-1 (sayısal) | K0 içi — izinli |
| K005 → K000 | skip (K2→K0) | MySQL/PDO zemini (ADR-002 frozen) | **skip-edge** — index §6.2-8 |
| K006 → K000 | skip (K2→K0) | session/token tabloları (ADR-095 jti) | **skip-edge** — index §6.2-16 |
| K007 → K000 | skip (K2→K0) | APCu zemini (matris §2, plan §4 sıra 1-4) | **skip-edge** — index §6.2-17 |
| K012 → K000 | cross-cutting → dip | stdout/barındırma zemini | istisna — index §6.1 |
| K013 → K000 | cross-cutting → dip | CI/deploy zemini | istisna — index §6.1 |
| (K016–K019) → A0 bandı | aralık | matris §2 "A5 → A0 zemini (K000–K005)" | istisna — index §6.1/§6.2-11 |

## §4 Arayüz (Girdi/Çıktı & API)

Yalnız diskte kanıtlı isimler; okunmamış method/imza `UNKNOWN`tır — uydurulmaz.

| Arayüz / Varlık | Disk kanıtı | Rol |
|---|---|---|
| Subdomain kökleri | `api.` `auth.` `home.` `media.` `assets.coremusic.net` (K000 ölçümü) | Barındırma zemini — üst katmanların çalıştığı kök |
| `shared/composer.json` | `shared/composer.json` (K000) | Paylaşımlı autoload sözleşmesi |
| `DatabaseManager` | `shared/src/Database/DatabaseManager.php` (K000) | DB erişim zemini — method imzaları bu görevde okunmadı → `UNKNOWN` |
| `DatabaseRegistry` | `shared/src/Database/DatabaseRegistry.php` (K000) | DB kayıt/registry zemini — method imzaları `UNKNOWN` |
| Donanım hedef arayüzü | ADR-061 · ADR-038 (K001) | PLANNED — disk kanıtı YOK |

**Girdi/çıktı sözleşmesi:** K0, üst katmanlara yalnız **zemin servisi** (autoload, DB erişim köprüsü, barındırma kökü) sunar; istek/yanıt DTO'su K0'ın işi değildir (onu K3/K4 taşır).

### §4.1 Beklenen Veri Yapıları (yalnız kanıtlı; yapı detayı okunmadıysa UNKNOWN)

| Veri yapı | Kaynak | Durum |
|---|---|---|
| Composer autoload tanımı | `shared/composer.json` (K000) | IMPLEMENTED — içeriği bu görevde okunmadı → `UNKNOWN` |
| DB kayıt girdisi (registry) | `DatabaseRegistry` (K000) | IMPLEMENTED — yapı/imza `UNKNOWN` |
| DB erişim köprüsü | `DatabaseManager` (K000) | IMPLEMENTED — yapı/imza `UNKNOWN` |
| Donanım hedef tanımı (DAC/XMOS) | ADR-038 (K001) | **PLANNED** — veri yapısı `UNKNOWN` |
| A5 bileşen zinciri verisi | K016–K020 (istisna) | **PLANNED** — PCB dosyası glob=0; yapı UNKNOWN |

## §5 Hata Yönetimi ve Güvenlik

- **Sınır geçişleri:** K0 → üst katman geçişinde **yetkilendirme uygulanmaz** (K0 güvenlik politikası sahibi değil — sınır K2/K4'tedir); K0 yalnızca erişim zeminini sağlar.
- **İzolasyon:** DB hatası K000 sınırında yakalanır; iç hata yığnı üst katmana **taşınmaz** (Hata-İzolasyon, index §2.3). Uzak çağırıda timeout+retry+fallback kuralı K012'nin cross-cutting kuralıdır, K0'ın iç uygulaması `UNKNOWN`.
- **Failure Modes:**
  1. DB kayıt zinciri hatası (DatabaseManager/Registry) → veri katmanı K005'i etkiler.
  2. `assets.coremusic.net` altında composer.json **YOK** — statik varlık olduğu varsayılıyor ama kasıt doğrulanmadı → autoload kapsamı `UNKNOWN` (K000, index §7-2).
  3. K001 donanım zemini PLANNED — donanım yokluğunda K1 (K002/K003) hiç başlayamaz.
- **⚠️ işaretleri:** ADR-015 uygulama kanıtı UNKNOWN (index §7-1) · assets'ta composer.json yokluğu kasıtı UNKNOWN (§7-2) · K001 disk kanıtı YOK (§7-3) · A5 içi grafik/ölçüm verisi yok (K001).

## §6 ADR & Disk Kanıtı

| ADR no | Başlık / Konu | Durum |
|---|---|---|
| ADR-015 | K000 — işletim sistemi/subdomain zemini kararı (uygulama kanıtı UNKNOWN) | IMPLEMENTED (zemin) · uygulama kanıtı ⚠️ UNKNOWN |
| ADR-082 | K000 — mimari karar (başlık SSOT K000 dosyasında) | IMPLEMENTED |
| ADR-085 | K000 — mimari karar (başlık SSOT K000 dosyasında) | IMPLEMENTED |
| ADR-061 | K001 — L6 electronics (donanım) | **PLANNED** (disk kanıtı YOK) |
| ADR-038 | K001 — PCM3168A + XMOS donanım hedefi | **PLANNED** (disk kanıtı YOK) |

**Gerçek dosya yolları (disk kanıtı):**
- `api.coremusic.net/` · `auth.coremusic.net/` · `home.coremusic.net/` · `media.coremusic.net/` · `assets.coremusic.net/` (K000 — 5 subdomain)
- `shared/composer.json` (K000)
- `shared/src/Database/DatabaseManager.php` · `shared/src/Database/DatabaseRegistry.php` (K000)
- K001: **dosya kanıtı YOK** (yalnız ADR-061/ADR-038)

### §6.1 Durum Notu

K000 **IMPLEMENTED** (2026-10-09 ölçümü — disk kanıtlı) · K001 **PLANNED** (yalnız ADR-061/ADR-038; disk kanıtı YOK). Makro K0 bu nedenle **KARIŞIK** durumdadır; K0 için tek başına "IMPLEMENTED" nitelemesi yazılamaz.

## §7 Bilinmeyenler

| # | Bilinmeyen | Mikro katman |
|---|---|---|
| 1 | ADR-015 uygulama kanıtı — uygulandığı kod yolu doğrulanamadı | K000 |
| 2 | assets.coremusic.net altında **composer.json YOK** — kasıt/gerekçe UNKNOWN (uydurma composer eklenmez) | K000 |
| 3 | DatabaseManager/DatabaseRegistry method imzaları bu görevde okunmadı → `UNKNOWN` (uydurma yok) | K000 |
| 4 | K001 donanım: disk kanıtı YOK; A5 içi grafik, ölçüm verisi yok | K001 |
| 5 | AGENTS §25.2 "3 composer.json" vs diskte 5 (shared + 4 subdomain) uyuşmazlığı | K000 |

---

**İlişki:** Üst makro → `[[k1-surucular-cihaz-yonetimi]]` · SSOT → `[[../katman-baglilik-matrisi.md]]` · `[[../K000-isletim-sistemi/index]]` · `[[../K001-donanim/index]]`
