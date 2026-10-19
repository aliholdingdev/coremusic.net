# Medya Arşivi — Adlandırma Kuralları

> **Kapsam:** `C:\www\coremusic.net\media.coremusic.net\` · **Tarih:** 2026-09-29 · **Görev:** F1.3
> **Yetki (SSOT):** ADR-092 `media-dizin-ekseni-ve-ulid` §6.1 · **Kardeş dosya:** [dizin-yapisi.md](./dizin-yapisi.md)

**6 kural:** 1) ASCII fold · 2) Ön ekler · 3) Çakışma · 4) Kimlik (ULID) · 5) Kodlama · 6) Mojibake

## İçindekiler

1. [ASCII fold](#1-ascii-fold)
2. [Ön ekler](#2-ön-ekler)
3. [Çakışma ve yasaklı adlar](#3-çakışma-ve-yasaklı-adlar)
4. [Kimlik: ULID](#4-kimlik-ulid)
5. [Kodlama](#5-kodlama)
6. [Mojibake düzeltmesi](#6-mojibake-düzeltmesi)
7. [Örnek yürüyüş](#7-örnek-yürüyüş)
8. [Yasaklı kalıplar](#8-yasaklı-kalıplar)
9. [İlgili bağlantılar](#9-ilgili-bağlantılar)

---

## 1. ASCII fold

**Dosya ve klasör adı her zaman ASCII'dir.** Dönüşüm sırası:

```
Türkçe/ASCII dışı harf fold  →  lowercase  →  [a-z0-9-] filtresi  →  max 80 karakter
```

### Karşılık tablosu

| Kaynak | → | Çıktı | Kaynak | → | Çıktı |
|---|---|---|---|---|---|
| `ş` | → | `s` | `Ş` | → | `S` |
| `ı` | → | `i` | `İ` | → | `i` |
| `ğ` | → | `g` | `Ğ` | → | `G` |
| `ü` | → | `u` | `Ü` | → | `U` |
| `ö` | → | `o` | `Ö` | → | `O` |
| `ç` | → | `c` | `Ç` | → | `C` |

### Adım adım

1. **Fold:** tablodaki 12 eşleme uygulanır → tüm harfler ASCII.
2. **Lowercase:** fold sonrası büyük harf `A-Z` → `a-z`.
3. **Filtre:** yalnız `^[a-z0-9-]` kalır; boşluk, noktalama ve kalan her şey atılır (bitişik sözcükler tek `-` ile birleşir).
4. **Kırpma:** **max 80 karakter** (ADR-092 §6.1 kural 1). Kırpma sonrası kimlik **ULID'dedir** — slug değişebilir.

**Örnek:**

| Kaynak ad | Fold | Lowercase + filtre | Slug |
|---|---|---|---|
| `Şarkı` | `Sarki` | `sarki` | `sarki` |
| `Çalıkuşu` | `Calikusu` | `calikusu` | `calikusu` |
| `Gece Yarısı` | `Gece Yarisi` | `gece-yarisi` | `gece-yarisi` |

> `_cesitli` klasör adı da aynı tabloya girer ve **HER ZAMAN a-z** olmalıdır (ADR-092 §6.1 kural 3).

---

## 2. Önekler

Her seviye kendi öneki ile üretilir:

| Seviye | Kalıp | Örnek | Açıklama |
|---|---|---|---|
| **Parça** | `{ss}-{slug}` | `01-sarki` | **2 haneli sıra** + slug (`01` … `99`) |
| **Albüm** | `{yil}-{slug}` | `1975-ya-yarin` | Yıl önde |
| **Albüm (yıl yoksa)** | `yil-yok-{slug}` | `yil-yok-bir-sarki` | **`yil-yok-` başlangıcı** |
| **Klip** | `{yil}-klip-{slug}` | `2012-klip-sarki` | Yıl + `klip` sabiti + slug |

- Sıra numarası albüm içi sıradaştır; albüm değişirse sıra yeniden yazılır (meta içinde), kimlik değişmez.
- Albüm yılı `album.json` içindeki `yil` alanından gelir; alan yoksa `yil-yok-` öneki düşer.

---

## 3. Çakışma ve yasaklı adlar

| Durum | Çözüm |
|---|---|
| Aynı klasörde aynı slug | `-2`, `-3` … eklenir → `01-sarki`, `01-sarki-2` |
| Büyük/küçük harf farkı | Tarama **case-insensitive** yapılır → `Sarki` ve `sarki` **aynı** kabul edilir, ikincisi çakışma sayılır |
| Windows-reserved adlar | **YASAK** — `con`, `aux`, `prn`, `nul`, `com1`-`com9`, `lpt1`-`lpt9` **ve uzantılıları** (`con.mp3` dahil) |

- Çakışma sayımı **hedef klasörün içindedir**; yeni slug üretildiğinde tekrar denenir.
- Reserved kontrolü **fold + lowercase sonrası** yapılır (Windows adı büyük/küçük harf duyarsızdır).

---

## 4. Kimlik: ULID

| Alan | Değer |
|---|---|
| **Slug** | **Takma ad** (grep/URL estetiği) — değiştirilebilir |
| **Kalıcı kimlik** | **ULID** — `^[0-9A-HJKMNP-TV-Z]{26}$` (**I, L, O, U harfleri YOK**) |
| **URL** | `/a/{id}` → `/a/01J...` (id = ULID) |
| **Yeniden adlandırma** | Yalnız `bin\rename --dry-run` (faz 2) — doğrudan Explorer/elle taşıma YOK |

- **Kimlik slug'da değil ULID'dedir:** slug değişirse dosya taşınmaz, indeks kırılmaz (ADR-092 §2.1 madde 3).
- ULID üretimi ve regex denetimi audit'te zorunludur (ADR-092 §6.1 kural 2).
- `rename` aracı **faz 2**'dedir; bu fazda yeniden adlandırma yapılmaz → **⚠️ VERIFICATION REQUIRED** (aracın implementasyonu henüz yok, yalnız kural yazılı).

---

## 5. Kodlama

| Nerede | Kodlama | Kural |
|---|---|---|
| **Dosya adı / klasör adı** | **ASCII** | Her zaman; kural 1 fold'u zorunlu kılar |
| **Meta JSON içi metin** | **UTF-8** | Türkçe **korunur** — `ad`, `etiket[]`, `tur[]` alanları bozulmaz |

- **Ayrım:** diskte ASCII, içerikte UTF-8. Slug ASCII diye Türkçe bilgi kaybolmaz; Türkçe asıl ad `meta.json` içinde yaşar.
- İlgili kabul kriteri: slug ASCII + meta UTF-8 Türkçe, mojibake YOK (ADR-092 §7.1 kriter 5).

---

## 6. Mojibake düzeltmesi

Kaynak koleksiyonda **bozuk Türkçe adlar** vardır (yanlış kodlama). Bunlar **ingest sırasında** `config\mojibake-fix.json` **eşlemesiyle** normalize edilir — slug üretilmeden **önce**.

**Örnek** (JSON kaçışlı gösterim — ham bozuk dizge bu belgede yazılmaz):

| Bozuk giriş (JSON kaçışı) | Normalize |
|---|---|
| `M\u00C3\u00BCzik` | `Müzik` |
| `S\u00C4\u00B1ark\u0131` | `Sarkı` |

- Eşleme çift yönlü değildir: **bozuk → doğru** yönünde çalışır.
- Slug üretimi **normalizasyondan sonra** çalışır; aksi hâlde `M\u00C3\u00BCzik` benzeri bozuk adlar `m-c3-bc-zik` gibi hatalı slug üretir (ADR-092 §4.2 son madde).
- Eşlemenin kendisi `config\mojibake-fix.json` içinde yaşar → **diskte mevcut ✓**.

---

## 7. Örnek yürüyüş

**1 kaynak dosya → slug → klasör yolu:**

```
KAYNAK   : D:\gelen\Yeni\Şarkılar\01 Şarkı.flac
```

| # | Adım | Girdi | Çıktı |
|---|---|---|---|
| 1 | Mojibake fix | `01 Şarkı.flac` (bozuk değil, geç) | `01 Şarkı.flac` |
| 2 | ASCII fold (kural 1) | `Şarkı` | `Sarki` |
| 3 | Lowercase + `[a-z0-9-]` | `Sarki` | `sarki` |
| 4 | Boyut sınırı | `sarki` (5 ≤ 80) | `sarki` |
| 5 | Sıra öneki (kural 2) | `01-` + `sarki` | **`01-sarki`** |
| 6 | Sanatçı slug | `Örnek Sanatçı` | **`ornek-sanatci`** |
| 7 | Albüm slug | `Ya Yârin` + yıl `1975` | **`1975-ya-yarin`** |
| 8 | Uzantı korunur (kural 4/format) | `.flac` | **`audio.flac`** |

**SONUÇ YOLU:**

```
C:\www\coremusic.net\media.coremusic.net\media\audio\ornek-sanatci\1975-ya-yarin\01-sarki\audio.flac
                                                                                                    ├─ meta.json   (UTF-8 Türkçe)
                                                                                                    ├─ cover.jpg   (master kapak)
                                                                                                    └─ audio.mp3   (varyant, varsa)
```

Aynı parça `_tekli` olsaydı: `media\audio\ornek-sanatci\_tekli\01-sarki\audio.flac`.
Sanatçıya bağlanamayan derleme olsaydı: `media\audio\_cesitli\o\ornek-koleksiyon\01-sarki\audio.flac`.

---

## 8. Yasaklı kalıplar

Üretilen **hiçbir** dosya/klasör adı şunları içeremez:

1. **Windows-reserved:** `con`, `aux`, `prn`, `nul`, `com1`-`com9`, `lpt1`-`lpt9` + uzantılıları.
2. **Geçersiz karakter:** `<` `>` `:` `"` `/` `\` `|` `?` `*` ve kontrol karakterleri (0-31) — fold + filtre zaten temizler.
3. **Bitişik nokta/boşluk:** adın sonunda `.` veya boşluk.
4. **Yol kaçışı:** `..`, `.`, boş slug, mutlak yol parçası (`C:`).
5. **ASCII dışı harf:** Türkçe/başka kodlama karakteri (kural 1 ve 5 ihlali).
6. **Çok-eksen klasör adı:** tür, dönem, ruh hâli, kullanım **klasör adı yapılamaz** — bunlar `meta.json` tag'idir (`../docs/dizin-yapisi.md` §1.1).
7. **Boş slug (fold sonrası 0 karakter):** slug üretilmez, **yedek değer `isimsiz`** — **Karar (2026-09-29):** fold sonrası boş çıkan slug için `Slugger::YEDEK = 'isimsiz'` (ADR-092 §6.1 ile uyumlu). Kod: `../src/Media/Slugger.php:19`.

---

## 9. İlgili bağlantılar

| Kaynak | Ne verir |
|---|---|
| `C:\www\coremusic.net\.ai\.decisions\accepted\ADR-092-media-dizin-ekseni-ve-ulid.md` | Yetki: disk ekseni, slug ≤80 ASCII, ULID regex, `_cesitli` a-z, yaşam döngüsü |
| `../config/taxonomy.json` | Kapalı taksonomi + `durum` değerleri (`inbox/aktif/sakli/tekrar/arsiv`) — **diskte ✓ (1.488 B)** |
| `../config/mojibake-fix.json` | Bozuk → doğru ad eşlemesi — **diskte ✓ (2.103 B)** |
| `../config/media.schema.json` | Meta alan şeması — **diskte ✓ (26.856 B)** |
| `../docs/dizin-yapisi.md` | Dizin ağacı, format tablosu, master kuralı, ölçek eşikleri |
| `../docs/adlandirma.md` | Bu dosya (aynı klasörde: `./adlandirma.md`) |

---

**Authority:** ADR-092 Karar Metni (SSOT) · **Son güncelleme:** 2026-09-29
