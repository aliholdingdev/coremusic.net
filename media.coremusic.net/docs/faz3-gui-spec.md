---
reference_doc: "media.coremusic.net/docs/faz3-gui-spec.md"
title: "CoreMusic — Faz 3 GUI Ön Spec (media.coremusic.net web arayüzü)"
type: spec
category: ui-design
date: 2026-09-29
status: active
version: 0.1.0
authority: "ÖN spec — asıl screen-spec'ler `.ai/ui-design/screens/` serbestleşince Kalıp D ile yazılır (Guardrail #16)"
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: "media.coremusic.net/docs/faz3-gui-spec.md"
  source_of_truth: "output.md · media.coremusic.net/docs/dizin-yapisi.md · config/taxonomy.json · config/media.schema.json · .ai/.sql/mysql/media_catalog.sql"
---

# CoreMusic — Faz 3 GUI Ön Spec (media.coremusic.net)

**Zorunlu Bağlantılar:** [[media.coremusic.net/docs/dizin-yapisi]] · [[media.coremusic.net/docs/adlandirma]] · [.ai/.templates/ui-design/screen-spec-template] · [.ai/architecture/k11-ux/itcss-9-layer]

---

## 1. Kapsam ve faz sınırı

| Kapsam | Kapsam dışı (§8) |
|--------|------------------|
| `media.coremusic.net` **web arayüzü** — PHP 8.4, ADR-039 yerleşimi, port **5000/6000** | Oynatma motoru (faz 4) |
| `public\` dizini **faz 3'te kurulur** (router + statik varlıklar) — diskte **henüz yok** | Dışa aktarma (CSV/JSON indirme) |
| Katalog tarama/filtreleme · sanatçı→albüm→parça gezinme | Kullanıcı hesabı / kimlik doğrulama |
| Ingest review (CSV onay satırlarının görüntülenmesi) | Dosya indirme |
| Audit raporu görüntüleme (`--deep` HASH-FARK vurgusu) | Metaveri düzenleme yazma (GUI editör = ileri faz) |
| Koleksiyonlar (`.m3u`/`.pls` → koleksiyon tanımı görüntüleme) | `media\` ağacına yazma (salt okunur ekranlar) |

**Faz sınırı:** Bu doküman **kod üretmez** (HTML/CSS/JS yasak). Faz 2 araçları (`bin\scan.php`, `bin\audit.php`, `bin\ingest.php`) CLI olarak kalır; GUI bunların **çıktılarını okur**, komut **çalıştırmaz** (inceleme §7.2).

**Veri gerçeği:** Kaynak = `catalog\catalog.jsonl` + MySQL **`media_catalog`** (her ikisi de **türetilmiş indeks**). **JSON = SSOT** (`meta.json` + `artist.json` + `album.json` + `koleksiyon.json`); DB/JSONL `scan.php --rebuild` ile yeniden üretilir — DB çökse arşiv yaşar (dizin-yapisi §3.2).

---

## 2. Veri modeli (ekranların tek kaynağı)

### 2.1 SSOT zinciri

```
config\media.schema.json + config\taxonomy.json   (şema + 17 kapalı sözlük)
        │  yazan: bin\scan.php (validate)
        ▼
media\**\meta.json · artist.json · album.json · koleksiyon.json   ← SSOT
        │  üreten: bin\scan.php
        ├──► catalog\catalog.jsonl     (türetilmiş, git'e girmez)
        └──► MySQL media_catalog       (BCNF, 9 tablo + v_asset_search)  ← türetilmiş
```

### 2.2 Ekran → kolon eşlemesi (uydurma yok)

| Ekran | Kaynak | Temel alanlar |
|-------|--------|---------------|
| Katalog / Filtre | `v_asset_search` | `asset_id, asset_slug, baslik, asset_tip, sira, disk, asset_durum, dosya_yolu, sure_sn, sha256, album_id, album_slug, album, album_tip, album_yil, album_fiziksel, sanatci_id, sanatci_slug, sanatci, sanatci_tip, tur` |
| Filtre sözlükleri | `config\taxonomy.json` | `tur, kullanim, media_tipi, donem, ruh, klip_tipi, dil` (kapalı set) + `etiket[]` (açık set) |
| Sanatçı detay | `artist` tablosu / `artist.json` | `kimlik, biyografi, muzik, iliski, kaynak_id, gorsel, varsayilan_tag` |
| Albüm detay | `album` tablosu / `album.json` | `kimlik, ust, tarih, bicim, kapsam, gorsel, kredi, haklar, tag` |
| Parça detay | `asset` tablosu / `meta_base` | `teknik.*` (sha256, sure_sn, bitrate, lufs, peak_db, format, boyut_bayt, kanal, orneklem, derinlik, dosya, varyantlar) · `muzikal.*` · `etiket[]` |
| Ingest review | `reports\ingest-*.csv` | `durum, kaynak, hedef, slug, ulid, sha256, not` (ingest.php:274) |
| Audit raporu | `bin\audit.php` stdout | `SEVIYE \| yol \| kural \| neden` + `TOPLAM` özeti (audit.php:10) |
| Koleksiyonlar | `koleksiyon` tablosu / `koleksiyon.json` | `koleksiyon, kaynak_tur, sarki_sayisi, tag, durum` |

> **Sınır notu:** `v_asset_search` yalnız `tur` alanını `JSON_EXTRACT` ile açar; `kullanim/donem/ruh/dil/media_tipi/klip_tipi` filtreleri view'da **yoktur** → bu filtreler `asset_tag`/`taxonomy` FK üzerinden veya JSONL üzerinden uygulanır (§7.1).

---

## 3. Ekran envanteri (8 ekran)

| # | Ekran | Route (public router) | Ana görev |
|---|-------|----------------------|-----------|
| E1 | Katalog | `/katalog` | Sayfalama + sıralama + tablo/kart liste |
| E2 | Filtre paneli | `/katalog?f=…` (E1 ile birleşik) | 7 kapalı sözlük + serbest `etiket[]` arama |
| E3 | Sanatçı detay | `/sanatci/{slug}` | Diskografi; >50.000 sanatçıda baş-harf katmanı uyarısı |
| E4 | Albüm detay | `/album/{sanatci}/{slug}` | Parça listesi + etiket/katalog kredileri |
| E5 | Parça detay | `/parca/{ulid}` | Teknik meta + arşiv görüntüleme (oynatma = faz 4) |
| E6 | Ingest review | `/ingest/review` | CSV onay: `yeni` \| `tekrar` \| `atla` \| `hata` |
| E7 | Audit raporu | `/audit` | `SEVIYE\|yol\|kural\|neden` + `--deep` HASH-FARK |
| E8 | Koleksiyonlar | `/koleksiyonlar` | `.m3u`/`.pls` adayları → koleksiyon tanımı |

---

## 4. Ekran detayları

> Her ekran için **amaç · veri kaynağı · durumlar · 1 ASCII wireframe · BEM bileşen listesi · erişilebilirlik**.

### 4.1 E1 — Katalog

- **Amaç:** 1M+ varlık ölçekte tek listede gezinme; sanatçı→albüm→parça eksenini koruyan sıralama ve sayfalama.
- **Veri kaynağı:** `GET /api/assets` → `v_asset_search` (sayfalı, sıralı). Alternatif fallback: `catalog.jsonl` (MySQL yoksa).
- **Durumlar:** `yükleniyor` = skeleton satır (`.cm-catalog__row--skeleton`) · `boş` = "Filtrelerle eşleşen kayıt yok" + filtre temizle butonu · `hata` = `.cm-alert--error` + tekrar dene · `sayfa dışı` = 404 kartı.

```
┌──────────────────────────────────────────────────────────────────────┐
│ KATALOG                     [arama]        Filtreler(3)  1.284 kayıt│
├───────────────┬──────────────────────────────────────────────────────┤
│ FACET SİDEBAR │ #  Başlık          Sanatçı      Albüm   Yıl  Süre    │
│ (E2)          │ ─────────────────────────────────────────────────── │
│ tür     [14]  │ 1  Düğün Arabesk   Sanatçı A    1975    1975 3:42   │
│ kullanım[8]   │ 2  Nostalji Damar  Çeşitli      —       1998 4:10   │
│ dönem   [7]   │ 3  Slow Enstr.     Sanatçı B    2011    2011 5:05   │
│ ruh     [5]   │ …                                                    │
│ dil     [12]  │                                                      │
│ media   [6]   │ ┌──────────────────────────────────────────────────┐ │
│ klip    [5]   │ │ < 1  2  3 … 129  >        sıra: [başlık ▾]     │ │
│ ───────────   │ └──────────────────────────────────────────────────┘ │
│ etiket []     │                                                      │
└───────────────┴──────────────────────────────────────────────────────┘
```

- **BEM bileşenleri:**

| Block | Element / Modifier |
|-------|--------------------|
| `.cm-catalog` | `__toolbar`, `__count`, `__table`, `__row`, `__row--skeleton`, `__card` |
| `.cm-pager` | `__btn`, `__btn--active`, `__status` |
| `.cm-sort` | `__select`, `__label` |
| `.cm-search` | `__input`, `__clear` |
| `.cm-alert` | `--error`, `--info` |

- **Erişilebilirlik:** Tablo `<table>` + `<th scope="col">`; sıralama `<th>` içinde `aria-sort`. Sonuç sayısı `aria-live="polite"`. Sayfalama `<nav aria-label="Sayfalama">`, aktif sayfa `aria-current="page"`. Odak halkası `var(--cm-focus-ring)` (kontrast ≥3:1). Metin AA: `--cm-text-primary` / `--cm-bg-surface` ≥4.5:1.

### 4.2 E2 — Filtre paneli

- **Amaç:** 50 yıllık koleksiyon bilgisini (`taxonomy.json` 17 kapalı sözlük) tek ekranda sorgulamak; serbest `etiket[]` ile kapalı sözlüğü aşan arama.
- **Veri kaynağı:** `GET /api/filters` → `taxonomy.json` (enum listeleri + sonuç sayımları). Uygulama `GET /api/assets?f[tur]=arabesk&…`. Kapalı sözlük **değiştirilemez** (yeni değer = taxonomy.json'a 1 satır + audit).
- **Durumlar:** `yükleniyor` = facet skeleton · `0 sonuç` = "Bu kombinasyon boş — X temizle" · `hata` = sözlük yüklenemedi (filtreler devre dışı `aria-disabled`, katalog çalışmaya devam).

```
┌──────────────────────────────────────────────────────────────┐
│ FİLTRELER                          [Tümünü temizle] [Uygula] │
├──────────────────────────────────────────────────────────────┤
│ TÜR (kapalı)                        SONUÇ                    │
│ [x] arabesk  128   [x] damar   96   ─────────────────────── │
│ [ ] turk-pop 40    [ ] oryantel 33   EŞLEŞEN: 224 / 1.284   │
│                                                              │
│ KULLANIM · DÖNEM · RUH · DİL · MEDYA_TİPİ · KLİP_TİPİ       │
│ (aynı kural: kapalı set + sayaç)                             │
│ ─────────────────────────────────────────────────────────── │
│ SERBEST ETİKET (açık set)                                    │
│ [ "düğün 1998"________________________ ]  [+]                │
│ etiket: [düğün-1998 x] [plak x]                              │
└──────────────────────────────────────────────────────────────┘
```

- **BEM bileşenleri:**

| Block | Element / Modifier |
|-------|--------------------|
| `.cm-filter` | `__panel`, `__footer`, `--drawer`, `--inline` |
| `.cm-facet` | `__group`, `__toggle`, `__count`, `__legend` |
| `.cm-chip` | `__label`, `__remove`, `--active` |
| `.cm-tag-input` | `__input`, `__suggestion` |
| `.cm-btn` | `--primary`, `--ghost`, `--sm` |

- **Erişilebilirlik:** Her facet grubu `<fieldset>` + `<legend>`; tekil seçim `role="radiogroup"`, çoklu `input[type=checkbox]`. Etiket kutusu `role="combobox"` + `aria-expanded`. Değişiklik sonrası eşleşme sayısı `aria-live="polite"`. Drawer kapanışı `Esc` + odak tuzağı; açan butona odak geri döner. Etiket sayaçları AA için `--cm-text-secondary` (≥4.5:1), sayaç rengi tek başına değil **metin+ikon** ile.

### 4.3 E3 — Sanatçı detay

- **Amaç:** Sanatçının diskografisini (albüm → tekli → klip) tek eksende sunmak; katalog büyüdüğünde baş-harf katmanı uyarısını hatırlatmak.
- **Veri kaynağı:** `GET /api/artists/{slug}` → `artist` (`kimlik, biyografi, muzik, gorsel, varsayilan_tag`) + `album` satırları + `v_asset_search.sanatci_id` filtresi.
- **Durumlar:** `yükleniyor` = kapak+satır skeleton · `sanatçı yok` = 404 · `>50.000 sanatçı` = `.cm-banner--warn` (baş-harf katmanı, ADR-092 §6.1 k.4 — mevcut ağaca taşıma YOK) · `diskografi boş` = "_tekli/" ve albüm yok bilgisi.

```
┌──────────────────────────────────────────────────────────────────────┐
│ ← Katalog   [avatar] SANATÇI ADI            tur: [arabesk] [damar]  │
│ ⚠ Sanatçı sayısı eşiği (50.000) aşıldı → baş-harf katmanı: a/ b/ … │
├──────────────────────────────────────────────────────────────────────┤
│ BIOGRAFİ (kısa) · muzik.* · kaynak_id                               │
├──────────────────────────────────────────────────────────────────────┤
│ DİSKOGRAFİ            [sıra: yıl ▾]   12 albüm · 148 parça          │
│ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐   (_tekli ayrı grupta)  │
│ │ cover  │ │ cover  │ │ cover  │ │ cover  │                          │
│ │ 1975   │ │ 1981   │ │ 1998   │ │ 2011   │                          │
│ └────────┘ └────────┘ └────────┘ └────────┘                          │
└──────────────────────────────────────────────────────────────────────┘
```

- **BEM bileşenleri:**

| Block | Element / Modifier |
|-------|--------------------|
| `.cm-artist` | `__header`, `__avatar`, `__name`, `__tags` |
| `.cm-banner` | `--warn`, `__icon`, `__text` |
| `.cm-discog` | `__grid`, `__group`, `__group--tekli` |
| `.cm-card` | `__img`, `__title`, `__meta`, `--album` |
| `.cm-crumb` | `__link`, `__sep` |

- **Erişilebilirlik:** Baş-harf katmanı uyarısı `role="status"` (sürekli görünür, `aria-live` gerekmez — sessiz değişiklik değil). Kapaklı kart `<a>` tam hedef, görsel `alt="{sanatçı} — {albüm} ({yıl})"`. Breadcrumb `<nav aria-label="Konum">`. Klavye: kartlar arasında `Tab`, detay `Enter`.

### 4.4 E4 — Albüm detay

- **Amaç:** Albüm kimliği + kapak + parça listesi + **etiket/katalog kredileri** tek ekranda.
- **Veri kaynağı:** `GET /api/albums/{sanatci}/{slug}` → `album` (`kimlik, tarih, bicim, kapsam, gorsel, kredi, haklar, tag`) + parça `asset` satırları (`sira`, `disk`, `sure_sn`, `durum`).
- **Durumlar:** `yükleniyor` = satır skeleton · `kapak yok` → `meta.cover` yoksa albüm kapağına düşülmüyor (output.md:406 kuralı: `meta.cover yok → GUI albüm kapağına düşer`) · `kredi boş` = "kredi girilmemiş" (boş hücre yazma) · `>60 parça` = disk grubu `cd1/cd2` rozetleri.

```
┌──────────────────────────────────────────────────────────────────────┐
│ ← Sanatçı   ALBÜM ADI (1975)        [studyo] [lp] [arabesk]         │
├──────────────────────┬───────────────────────────────────────────────┤
│ ┌──────────────────┐ │ PARÇA LİSTESİ              disk: [1] [2]     │
│ │    cover.jpg     │ │ #  Başlık        Süre   Durum                │
│ │                  │ │ ─────────────────────────────────────────── │
│ └──────────────────┘ │ 01 Parça bir     3:42   aktif                │
│ etiket: [düğün]      │ 02 Parça iki     4:10   aktif                │
│ kredi: plak · katalog│ 03 …                                        │
│ haklar: telif ©1975  │                                              │
└──────────────────────┴───────────────────────────────────────────────┘
```

- **BEM bileşenleri:**

| Block | Element / Modifier |
|-------|--------------------|
| `.cm-album` | `__hero`, `__cover`, `__title`, `__facts` |
| `.cm-tracklist` | `__table`, `__row`, `__row--active`, `__disk-tabs` |
| `.cm-badge` | `--album-tip`, `--fiziksel`, `--durum` |
| `.cm-kredi` | `__list`, `__item`, `__empty` |
| `.cm-tag-list` | `__item`, `__item--free` |

- **Erişilebilirlik:** Parça listesi gerçek `<table>`; satır sırası DOM sırasıyla aynı. `durum` yalnız renkle değil **rozet metniyle** (AA: `--cm-text-primary` üstünde rozet zemini ≥4.5:1). Disk sekmeleri `role="tablist"` + ok tuşları. Kapak `alt` metni zorunlu; dekoratif ayraç `aria-hidden`.

### 4.5 E5 — Parça detay

- **Amaç:** Teknik meta denetimi (sha256/sure/bitrate/lufs) + arşiv dosyasının salt-okunur görüntülenmesi. **Oynatma faz 4'tedir.**
- **Veri kaynağı:** `GET /api/assets/{ulid}` → `asset.teknik` (`dosya, format, boyut_bayt, sha256, sure_sn, bitrate, kanal, orneklem, derinlik, lufs, peak_db, varyantlar`) + `muzikal` (`bpm, ton, enerji`) + `dosya_yolu`.
- **Durumlar:** `yükleniyor` = meta skeleton · `sha256 null` = "HASH OLMAYAN" (scan hafif mod çıktısı) · `varyant yok` = `audio.mp3` satırı "yok" · `dosya_yolu 404` = `.cm-alert--error` (arşiv bütünlüğü ihlali).

```
┌──────────────────────────────────────────────────────────────────────┐
│ ← Albüm   PARÇA BAŞLIĞI                      [aktif] [ses]          │
├──────────────────────────────────────────────────────────────────────┤
│ TEKNİK                                   MÜZİKAL (varsa)            │
│ sha256  a3f9…(64) [kopyala]              bpm 92   ton Am            │
│ süre    3:42  (222 sn)                   enerji 6/10                │
│ bitrate 1411 kbps · kanal 2              intro 1.20 sn · outro 0    │
│ örneklem 44100 Hz · derinlik 16 bit                                 │
│ format  flac · boyut 31.4 MB · lufs -14.2 · peak -1.1 dB            │
│ varyantlar: audio.mp3 ✓                                            │
├──────────────────────────────────────────────────────────────────────┤
│ ARŞİV (salt okunur)  media\audio\{sanatci}\{albüm}\{parca}\audio.flac│
│ [oynat — faz 4]   (devre dışı, aria-disabled)                       │
└──────────────────────────────────────────────────────────────────────┘
```

- **BEM bileşenleri:**

| Block | Element / Modifier |
|-------|--------------------|
| `.cm-parca` | `__grid`, `__section`, `__title` |
| `.cm-tech` | `__list`, `__label`, `__value`, `__value--mono` |
| `.cm-hash` | `__text`, `__copy`, `--missing` |
| `.cm-variant` | `__item`, `__state`, `--yok` |
| `.cm-btn` | `--disabled`, `--sm` |

- **Erişilebilirlik:** `sha256` 64 karakter → `word-break` + `<wbr>` / yatay kaydırma (ekran okuyucuya tam değer `aria-label` ile). Oynatma butonu `aria-disabled="true"` + açıklama tooltip. Değer etiketleri `<dl>/<dt>/<dd>`. Mono yazı tipi AA kontrastı `--cm-text-primary` ≥4.5:1.

### 4.6 E6 — Ingest review

- **Amaç:** `ingest.php` CSV çıktısını satır satır inceleme; karar durumu `yeni | tekrar | atla | hata` özetini gösterme. **Kopyalama/commit CLI'da kalır** (`--commit` + STDIN `evet`).
- **Veri kaynağı:** `GET /api/ingest/review` → `reports\ingest-YYYYMMDD-HHMM.csv` başlıkları: `durum, kaynak, hedef, slug, ulid, sha256, not`.
- **Durumlar:** `rapor yok` = "Son ingest raporu bulunamadı — `bin\ingest.php --dry-run` çalıştırın" · `yükleniyor` = skeleton · `hata satırı` = `.cm-decision--hata` · `commit yapıldı` = rozet "kopyalama tamam" (salt bilgi).

```
┌──────────────────────────────────────────────────────────────────────┐
│ INGEST REVIEW   rapor: ingest-20260929-1430.csv   [MOD: dry-run]     │
│ toplam: 128 satır   yeni 96 · tekrar 21 · atla 8 · hata 3           │
├──────────────────────────────────────────────────────────────────────┤
│ durum     kaynak → hedef                    sha256   not            │
│ ─────────────────────────────────────────────────────────────────── │
│ [yeni]    Music\x\01-a.mp3 → audio\…\01-a   a3f9…   probe ok        │
│ [tekrar]  USB\b\02-b.mp3  → —               71c0…   katalogda var   │
│ [atla]    x\kulaklik.lnk  → —               —       uzantı reddedildi│
│ [hata]    y\03-c.mp3      → —               —       slug-regex      │
└──────────────────────────────────────────────────────────────────────┘
```

- **BEM bileşenleri:**

| Block | Element / Modifier |
|-------|--------------------|
| `.cm-ingest` | `__header`, `__summary`, `__table`, `__filter` |
| `.cm-decision` | `__badge`, `--yeni`, `--tekrar`, `--atla`, `--hata` |
| `.cm-path` | `__src`, `__arrow`, `__dst`, `__mono` |
| `.cm-note` | `__text`, `--error` |
| `.cm-banner` | `--info` (dry-run uyarısı) |

- **Erişilebilirlik:** Durum **renk + metin** (renk tek başına yasak) — `.cm-decision__badge` her zaman metin taşır. Tablo `<th>` + `aria-sort="none"`; filtre radyo grubu. Özet sayaçları `aria-live="polite"` (rapor değişince). `hata` satırları `role="alert"` yalnız sayfa ilk açılışta değil, filtre ile göründüğünde değil — sessiz `role="status"` yeterli (aşırı duyuru önlemi).

### 4.7 E7 — Audit raporu

- **Amaç:** `bin\audit.php` çıktısını (`SEVIYE | yol | kural | neden`) filtrelenebilir listelemek; `--deep` modunda **HASH-FARK** satırlarını vurgulamak.
- **Veri kaynağı:** `GET /api/audit?deep=0|1` → audit stdout/stderr (HATA → STDERR, UYARI → STDOUT) + `TOPLAM | n HATA | n UYARI | n dosya` + `MOD:` satırı. Kural kimlikleri: `json, sema, addl-props, tag-enum, mojibake, slug-regex, slug-klasor, dosya-var, dosya-desen, yol-260, sha256-tekrar(UYARI), HASH-FARK(--deep)`.
- **Durumlar:** `0 hata` = `.cm-banner--success` ("arşiv temiz") · `hata var` = liste + sayaç · `deep kapalı` = bilgi "yeniden hash yapılmadı (hafif mod)" · `tarandı 0 dosya` = boş durum + scan önerisi.

```
┌──────────────────────────────────────────────────────────────────────┐
│ AUDIT RAPORU        [x] --deep (yeniden hash)   [seviye ▾] [kural ▾] │
│ MOD: hafif     TOPLAM | 4 HATA | 9 UYARI | 62 dosya                 │
├──────────────────────────────────────────────────────────────────────┤
│ seviye   yol                       kural          neden              │
│ ─────────────────────────────────────────────────────────────────── │
│ HATA     media/audio/X/1975-y      slug-regex     ASCII dışı karakter│
│ HATA     media/audio/A/…/01-z      HASH-FARK      kayıt: a3f9… ≠ 71c0│  ← vurgu
│ UYARI    media/audio/B/…/02-y      sha256-tekrar  iki dizinde aynı   │
│ ▸ 4 HATA · 9 UYARI  [yalnız hata]  [tümü]                            │
└──────────────────────────────────────────────────────────────────────┘
```

- **BEM bileşenleri:**

| Block | Element / Modifier |
|-------|--------------------|
| `.cm-audit` | `__controls`, `__summary`, `__list`, `__empty` |
| `.cm-severity` | `__cell`, `--hata`, `--uyari`, `--hash-fark` |
| `.cm-kural` | `__code`, `__desc` |
| `.cm-filterbar` | `__toggle`, `__select`, `__count` |
| `.cm-banner` | `--success`, `--warn` |

- **Erişilebilirlik:** HASH-FARK vurgusu **arka plan + sol kenarlık + metin** (renk tek başına değil; AA ≥4.5:1). Liste `role="table"` değil — gerçek `<table>` + `<caption>` (özet metni). `--deep` onay kutusu etiketli. Sonuç sayısı `aria-live="polite"`; filtreyi temizleme `Esc` ile değil, açık butonla (yanlış temizleme önlemi). Yol kolonu `dir` bağımsız `unicode-bidi: plaintext`.

### 4.8 E8 — Koleksiyonlar

- **Amaç:** `.m3u`/`.pls` playlist'lerin parse edilerek ürettiği koleksiyon tanımını listelemek (playlist dosyası kendisi rapora girer, dosya adına dönüşmez — dizin-yapisi §4).
- **Veri kaynağı:** `GET /api/filters?type=koleksiyon` yerine `GET /api/assets?tip=koleksiyon` → `koleksiyon` (`koleksiyon, kaynak_tur, sarki_sayisi, tag, durum, eklenme`) + `_cesitli\{a-z}\{slug}` yolu.
- **Durumlar:** `koleksiyon yok` = "_cesitli/ altında henüz kayıt yok" · `yükleniyor` = skeleton · `hata` = `.cm-alert--error` · `parça 0` = uyarı rozeti.

```
┌──────────────────────────────────────────────────────────────────────┐
│ KOLEKSİYONLAR (.m3u/.pls → tanım)          12 koleksiyon · 1.904 parça│
├──────────────────────────────────────────────────────────────────────┤
│ KOLEKSİYON                  kaynak   parça  durum   harf              │
│ ─────────────────────────────────────────────────────────────────── │
│ 15 Nostalji Damar Şarkılar  usb      15     aktif   1                │
│ Ali Alınan 2025             disk     677    sakli   a                │
│ Düğün Seti                  indirme  48     aktif   d                │
│ ─────────────────────────────────────────────────────────────────── │
│ gerçek yol: media\audio\_cesitli\{a-z}\{koleksiyon-slug}\            │
└──────────────────────────────────────────────────────────────────────┘
```

- **BEM bileşenleri:**

| Block | Element / Modifier |
|-------|--------------------|
| `.cm-collection` | `__header`, `__table`, `__row`, `__path` |
| `.cm-badge` | `--kaynak`, `--durum` |
| `.cm-alpha` | `__link`, `--active` (a-z bölmeleri) |
| `.cm-stat` | `__value`, `__label` |
| `.cm-alert` | `--info` |

- **Erişilebilirlik:** Sayısal özet `<dl>` ile. a-z bölmeleri `<nav aria-label="Harf bölmesi">`, eksik harfler `aria-disabled="true"`. Tablo sıralaması E1 ile aynı sözleşmeyi paylaşır (`aria-sort`). Durum rozetleri metinli (§4.6 ile aynı kural).

---

## 5. Tasarım sistemi bağlantısı

### 5.1 ITCSS katman yerleşimi (9 katman — `.ai/architecture/k11-ux/itcss-9-layer.md`)

| Katman | Faz 3 GUI'de ne taşır |
|--------|------------------------|
| `1-settings` | `--cm-*` token adayları (§5.3) — stil üretmez |
| `2-tools` | breakpoint mixin'leri (§6) |
| `3-generic` | reset / box-sizing |
| `4-elements` | `table`, `button`, `fieldset` temel görünümü |
| `5-objects` | `.cm-layout`, `.cm-sidebar`, `.cm-grid` (E1/E4 iskeleti) |
| `6-components` | §4'teki tüm `.cm-*` blokları |
| `7-utilities` | `.cm-u-visually-hidden`, `.cm-u-mono` |
| `8-themes` | tema varyantı (faz 3'te 1 tema) |
| `9-trumps` | override — sadece `!important` gerekçeli |

> **Katman ihlali = K11 kuralı 2** (`.ai/architecture/k11-ux/CLAUDE.md:19`): ITCSS sırası değişmez.

### 5.2 BEM konvansiyonu

- Önek **`cm-`** zorunlu; biçim `.cm-block__element--modifier`.
- Tek dosya = tek blok; element ebeveyn bloğun adını baştan alır (`.cm-tracklist__row`, `.cm-tracklist__row--active`).
- Durum modifiersi `--` ile (`--active`, `--error`, `--warn`); layout `__` ile.
- Bileşen envanteri `.ai/ui-design/02-component-inventory.md` **salt referanstır** — bu ön spec'te `Cxx` numarası **uydurulmaz**; kesin olmayan eşleşme `≈`, karşılıksız `—` ile yazılır. **Ön eşleme: §5.4 (VR-6)** — asıl screen-spec'te eşleme tazelenir. Envanter kapsamı **C01–C19** (dosya başlığı "C01–C16" eskimiş → Tespit A, §5.4).

### 5.3 `--cm-*` token adayları (yalnız AD — değer kopyalanmaz)

| Grup | Aday token adları (mevcut token dosyalarıyla aynı isimlendirme) |
|------|------------------------------------------------------------------|
| Renk | `--cm-primary`, `--cm-primary-rgb`, `--cm-bg-surface`, `--cm-bg-elevated`, `--cm-bg-primary`, `--cm-text-primary`, `--cm-text-secondary`, `--cm-text-tertiary`, `--cm-border-subtle`, `--cm-border-focus`, `--cm-success`, `--cm-warning`, `--cm-error`, `--cm-info` |
| Uzay | `--cm-space-1` … `--cm-space-16`, `--cm-section-gap`, `--cm-section-padding`, `--cm-grid-gap`, `--cm-inline-gap`, `--cm-container-md/lg`, `--cm-content-max-w`, `--cm-content-padding` |
| Tipografi | `--cm-font-body`, `--cm-font-mono`, `--cm-text-xs/sm/base/lg/xl`, `--cm-font-semibold`, `--cm-leading-normal`, `--cm-tracking-wide` |
| Yarıçap/gölge | `--cm-radius-sm/md/lg/xl`, `--cm-shadow-sm/md/lg`, `--cm-focus-ring` |
| Bileşen | `--cm-card-bg/border/radius/padding`, `--cm-input-bg/border/radius/min-h`, `--cm-btn-md-h/radius`, `--cm-badge-bg/radius/padding-x`, `--cm-progress-h/fill`, `--cm-skeleton-bg/radius` |
| Düzen/süre | `--cm-header-h`, `--cm-sidebar-w`, `--cm-grid-cols`, `--cm-duration-fast/normal`, `--cm-ease-out`, `--cm-z-header/sidebar/modal/toast` |

**Kural:** Tabloya **değer yazılmaz** (ham hex/px yasak) — değer master token dosyasından okunur; bu spec yalnız **ad uyumunu** bağlar.

### 5.4 BEM ↔ Bileşen Envanteri Eşlemesi (VR-6)

**Kaynak:** `.ai/ui-design/02-component-inventory.md` (updated 2026-09-29 · **salt okunur** — bu eşleme envantere **yazmaz**). **Kapsam:** §4'teki **46** BEM bloğu (`--cm-*` tasarım token'ları blok değildir, §5.3). **İşaret:** `Cxx` = kesin eşleşme · `≈` = en yakın aday (kesin değil) · `—` = envanterde karşılık yok.

| Spec bloğu | Envanter (C-no) | Not |
|------------|-----------------|-----|
| **① Eşleşenler (kesin) — 5 blok** | | |
| `.cm-btn` | `C04` Button | `--primary` / `--ghost` / `--sm` / `--disabled` (§4.2, §4.5) |
| `.cm-card` | `C03` Card | albüm kartı `--album` + `__img` / `__title` / `__meta` (§4.3) |
| `.cm-badge` | `C10` Badge | `--album-tip` / `--fiziksel` / `--durum` / `--kaynak` (§4.4, §4.8) |
| `.cm-search` | `C05` Input | arama girişi `__input` + `__clear` (§4.1) |
| `.cm-tag-input` | `C05` Input | serbest `etiket[]` girişi → `C05`'in varyantı (§4.2) |
| `.cm-note` | — | envanterde karşılık yok → **boşluk** (Tespit C); kalıcı satır içi not ≠ `C16` Toast (geçici) |
| **② Domain blokları — 21 blok** | — | **domain bloğu (envanter dışı, arşive özel):** `.cm-album` · `.cm-artist` · `.cm-parca` · `.cm-tracklist` · `.cm-discog` · `.cm-catalog` · `.cm-collection` · `.cm-ingest` · `.cm-audit` · `.cm-kredi` · `.cm-kural` · `.cm-hash` · `.cm-path` · `.cm-tech` · `.cm-variant` · `.cm-stat` · `.cm-decision` · `.cm-decision--hata` · `.cm-severity` · `.cm-alpha` · `.cm-block` — medya arşivinin alan modelini taşır (E1–E8) |
| **③ Yardımcılar — 2 blok** | — | **utility (C-numarası yok):** `.cm-u-mono` · `.cm-u-visually-hidden` — ITCSS `7-utilities` (§5.1) |
| **④ Eşleşmeyen UI blokları — 17 blok (esas bulgu)** | | |
| `.cm-sidebar` | ≈ `C17` Widget Area | E1 facet konteyneri; C17 "widget alanı" ile örtüşmesi tartışmalı → P1'de doğrula |
| `.cm-crumb` | ≈ `C01` NavLink | breadcrumb ≠ ana menü; en yakın konum linki (§4.3) |
| `.cm-filter` | — | **boşluk** — filtre paneli konteyneri (`__panel` / `__footer` / `--inline`, §4.2); en yakın C yok |
| `.cm-filter--drawer` | ≈ `C07` Modal | davranış örtüşmesi: `Esc` + odak tuzağı + açana odak iadesi (§4.2); drawer ≠ modal → P1 |
| `.cm-filterbar` | — | **boşluk** — yatay denetim çubuğu (`__toggle` / `__select` / `__count`, §4.7); `__select` tek başına `C14`'e düşer ama blok yok |
| `.cm-sort` | ≈ `C14` Dropdown | `__select` bir dropdown; blok etiketli sıralama seçicisi (§4.1) |
| `.cm-pager` | — | **boşluk** — sayfalama (§4.1); `C01` NavLink değil (sayfa düğmesi ≠ link listesi) |
| `.cm-grid` | — | **boşluk** — ITCSS `5-objects` (§5.1); envanter bileşen seviyesinde değil |
| `.cm-layout` | — | **boşluk** — ITCSS `5-objects` (§5.1) |
| `.cm-chip` | ≈ `C10` Badge | `__remove` ile kaldırılabilir etiket ≠ kalıcı rozet (§4.2) |
| `.cm-banner` | ≈ `C16` Toast | **takdir:** en yakın `C16`; ama spec banner'ı **kalıcı** (`role="status"`), toast **geçici** → `C02` Hero Banner **değil** (hero = sayfa üstü görsel blok, ≠ uyarı şeridi) |
| `.cm-banner--success` | ≈ `C16` Toast | aynı gerekçe (§4.7) |
| `.cm-banner--warn` | ≈ `C16` Toast | aynı gerekçe (§4.3) |
| `.cm-alert` | — | **boşluk** — kalıcı hata bölgesi + "tekrar dene" (§4.1); `C16` geçici, `C10` içerik rozeti → eşleşme yok |
| `.cm-alert--error` | — | **boşluk** (§4.1, §4.5, §4.8) |
| `.cm-facet` | ≈ `C08` Toggle | yalnız `__toggle` yüzeyi örtüşür; blok `<fieldset>` grubu → envantere facet grubu yok (§4.2) |
| `.cm-tag-list` | ≈ `C10` Badge | etiket rozeti listesi, `--free` açık set (§4.4) |

> `cm-facet` ve `cm-tag-list` girdi sınıflandırmasında yer almıyordu; bu eşlemede ④ grubuna alındı (her ikisi de `≈`).

**Sayım (46 = 46):** kesin eşleşen **5** + `≈` **10** + boşluk/`—` **8** + domain **21** + utility **2** = **46**.

- **Tespit A — envanter başlığı eskimiş:** `02-component-inventory.md` başlığı "C01–C16" der, dosya içeriği **C01–C19** taşır (19 bileşen) → başlık/İçerik uyuşmazlığı. Düzeltme envanter dosyasında yapılır; **sahip: ui-design oturumu**. Bu spec envantere **yazmaz** (salt okunur referans).
- **Tespit B — envanterde olup bu spec'te kullanılmayanlar (13):** `C02` Hero Banner · `C06` Tab · `C07` Modal · `C08` Toggle · `C09` Slider · `C11` Avatar · `C12` Tooltip · `C13` Skeleton · `C14` Dropdown · `C15` Progress · `C17` Widget Area · `C18` Quick Apps Row · `C19` Mini Card. §4'te **dolaylı yüzey** görünenler: `C06` → `.cm-tracklist__disk-tabs` (`role="tablist"`, §4.4), `C11` → `.cm-artist__avatar` (§4.3), `C12` → E5 "açıklama tooltip" (§4.5), `C13` → `__row--skeleton` / facet skeleton (§4.1–§4.2), `C07` → `.cm-filter--drawer`, `C14` → `.cm-sort__select`, `C17` → `.cm-sidebar`; **`C02`, `C09`, `C15`, `C18`, `C19` için §4'te karşılık yok** → 8 ekran bunları zorunlu kılıyor mu? Faz 4 screen-spec'inde karar (P1).
- **Tespit C — boşluk listesi → P1 kararı:** kesin boşluk **8 blok**: `.cm-note`, `.cm-filter`, `.cm-filterbar`, `.cm-pager`, `.cm-grid`, `.cm-layout`, `.cm-alert`, `.cm-alert--error` (+ **10** aday `≈` onay bekliyor). Faz 3'te iki seçenek: (a) bu bloklar için spec'e **yeni `Cxx` talebi**, (b) blokların **envantere eklenmesi** → **karar: kullanıcı onayı (P1)**.

> Bu eşleme VR-6 kapısıdır; envanter (`02-component-inventory.md`) yeniden yazıldığında (Faz 4, 2026-09-29) eşleme tazeledi. Detay: docs/checklist.md VR-6.

---

## 6. Responsive davranış (4 kırılım)

| Kırılım | Aralık | Filtre (E2) | Liste (E1) | Detay (E3–E5) |
|---------|--------|-------------|------------|----------------|
| **640** | `<640px` | **Drawer** (alt sheet) — `.cm-filter--drawer`, açılış `Filtreler` butonu | **Tablo → kart** (`.cm-catalog__card`, 1 kolon) | Tek kolon, meta `<dl>` yığını |
| **768** | `640–767px` | Drawer (tam genişlik) | Kart, **2 kolon grid** | 2 kolon (görsel + bilgi) |
| **1024** | `768–1023px` | **Inline** sticky sidebar (240px) | Tablo (dar sütun: başlık/sanatçı/süre) | 2 kolon sabit + takma listesi |
| **1440** | `≥1024px` | Inline sidebar (280px) + facet iki sütun | Tam tablo (tüm sütunlar + kredi) | 3 kolon (görsel / liste / meta) |

- **Tablo → kart dönüşümü:** `1024` altında `<table>` yerine aynı veriyi taşıyan `.cm-catalog__card` listesi render edilir (aynı DOM sırası, `<dl>` alan etiketleri korunur) — erişilebilirlik için `role="list"`/`role="listitem"` + etiketli değerler.
- **≥1440:** `.cm-content-max-w` ile satır uzunluğu sınırlanır; 3 kolon `grid-template-columns: 280px 1fr 360px`.
- **⚠️ VERIFICATION REQUIRED:** Kırılım adları (640/768/1024/1440) bu spec gereği; `.ai/architecture/k11-ux` breakpoint token'ları farklı değerlerde (`576/768/992/1200/1400`) — **çakışma**, asıl screen-spec öncesi karar + ADR gerekir.

---

## 7. Veri akışı

### 7.1 Akış (ASCII)

```
 [CLI — faz 2]                       [Veri katmanı]                    [Web — faz 3]
 bin\scan.php ────────► catalog\catalog.jsonl ─┐
   (tarama, --rebuild)                        │   okuma (salt okunur)
 bin\audit.php ───────► stdout/stderr ─────────┼──► reports\ (HATA|UYARI)
   (lint, --deep)                             │
 bin\ingest.php ──────► reports\ingest-*.csv ──┘
   (--dry-run / --commit + STDIN "evet")      │
        │                                     ▼
        │                     MySQL media_catalog (BCNF, türetilmiş)
        │                          └── v_asset_search (FULLTEXT)
        │                                     │
        └─────────────────────────────────────┤
                                              ▼
                        public\router.php  (PHP 8.4 · ADR-039 :5000/:6000)
                        ├── sayfa rotaları  /katalog · /sanatci/... · /album/...
                        └── /api/*  JSON    ← E1–E8 bileşenleri
```

**SSOT oku:** Web katmanı `media\` ve `config\` dosyalarına **yazmaz**; yalnız `catalog.jsonl` / MySQL / `reports\` okur. `media\` mutlak salt okunur.

### 7.2 API uçları (kısa)

| Uç | Metod | Amaç |
|----|-------|------|
| `/api/assets` | GET | Katalog sorgu: `sayfa`, `sira`, `q`, `f[…]` filtreleri → `v_asset_search` (fallback: JSONL) |
| `/api/filters` | GET | 17 kapalı sözlük enum'ları + facet sayımları (`taxonomy.json`) |
| `/api/audit` | GET | Audit satırları (`SEVIYE\|yol\|kural\|neden`) + `TOPLAM`/`MOD`; `?deep=1` → HASH-FARK |
| `/api/ingest/review` | GET | Son ingest CSV satırları (`durum/kaynak/hedef/slug/ulid/sha256/not`) |
| `/api/artists/{slug}` | GET | Sanatçı başlığı + diskografi grupları |
| `/api/assets/{ulid}` | GET | Parça teknik/müzikal meta + `dosya_yolu` |

> **Yazma uçları yok:** commit/`--rebuild`/`--commit` CLI'da kalır (GUI'den komut çalıştırma faz 3 kapsamı dışı).

---

## 8. Kapsam dışı

| Kapsam dışı | Neden |
|-------------|-------|
| Oynatma motoru (E5 `[oynat]`) | **Faz 4** — `--cm-now-playing-*` token'ları bu spec'te yalnız aday |
| Dışa aktarma (CSV/JSON indirme) | Faz 3'te yok |
| Kullanıcı hesabı / oturum | `auth.coremusic.net` ayrı servis (ADR-039) |
| Dosya indirme | Arşiv tek makine; indirme gereksinimi tanımsız |
| `media\` ağacına yazma | SSOT koruması (ADR-092); GUI salt okunur |
| Metaveri düzenleme formu | "GUI editörü" output.md:605'te **F2 GUI** olarak anılıyor — kapsam kilidi gerekir ⚠️ VERIFICATION REQUIRED |

---

## 9. ⚠️ Bu doküman ÖN spec'tir

Bu dosya **girdi**dir; `.ai/ui-design/` kuralı (Guardrail #16) gereği **asıl screen-spec'ler** `.ai/ui-design/screens/<tier>/<ekran>.md` alanında o alan **serbestleşince** `.ai/.templates/ui-design/screen-spec-template.md` (Kalıp D — 9 bölüm sırası: `ASCII Layout → BEM → Token → Touch Target → WCAG → Glassmorphism → PNG Referansı → Responsive → State`) ile yeniden yazılacaktır.

- `.ai/ui-design/**` altına **yazma yapılmamıştır** (salt okunur referans).
- Kalıp D'nin `reference.source_of_truth` = birebir PNG adıdır; bu spec'te **PNG yok** → ilgili alan `⚠️ VERIFICATION REQUIRED` olacaktır.
- Bu dokümadaki ASCII wireframe'ler **piksel düzeyinde değildir** (kategori/layout şemasıdır); piksel Layout Kalıp D §3.4'e aittir.

---

## 10. Doğrulama

| # | Kontrol | Sonuç |
|---|---------|-------|
| 1 | Ekran sayısı ≥ 8 | **8** (§3) |
| 2 | ASCII wireframe | **8** (bir ekran başına 1) |
| 3 | Veri modeli disk kaynağından mı | `taxonomy.json` (17 anahtar) · `media.schema.json` ($defs: artist/album/meta/koleksiyon) · `media_catalog.sql` (`v_asset_search` 22 kolon) · `dizin-yapisi.md` §3.2/§4/§6/§9 |
| 4 | Kod (HTML/CSS/JS) yazıldı mı | **Hayır** — salt spec |
| 5 | `.ai/ui-design/**` yazma | **Yok** (git status ile kanıtlanır) |
| 6 | Mojibake / BOM | `mojibake=0 · BOM yok` (UTF-8, doğrulandı) |
| 7 | Breakpoint çakışması | ⚠️ VERIFICATION REQUIRED (§6) |
| 8 | BEM `Cxx` envanter eşleşmesi | **Eşleme §5.4 (VR-6):** 46 = 5 kesin + 10 `≈` + 8 boşluk + 21 domain + 2 utility — boşluk/`≈` kararı P1 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
