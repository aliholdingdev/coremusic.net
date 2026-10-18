# Figma Çıkarma Notları (`_extraction-notes.md`)

Tarih: 2026-09-24
Dosya: Core Music — key `NFpX9bq58oApWJPgBK5Heo` (anahtar yalnızca `.ai/.env.figma` içinde)
Araç: Windows PowerShell 5.1 — `Invoke-WebRequest` + `X-Figma-Token` header

---

## 1. İstek kaydı

| # | İstek | Sonuç | Ayrıntı |
|---|---|---|---|
| 1 | `GET /v1/files/{key}/nodes?ids=<11 id>` | **BAŞARILI (200)** | Gövde 1.018.486 byte → `raw/nodes-1024-1920.json`. Gelen node: **11/11** (`1639:10160`, `1646:17727`, `1639:9775`, `1639:9773`, `1639:9904`, `1639:9892`, `1639:9910`, `2831:10267`, `2831:13747`, `2849:21489`, `2850:21494`) |
| 2 | `GET /v1/images/{key}?ids=...&format=png&scale=2` (ilk deneme) | **BAŞARISIZ — HTTP 400** | Yanıt gövdesi: `{"status":400,"err":"{\"params\":[\"file_key\"],\"query\":[\"ids\"]} are required."}` |
| 3 | Aynı istek (düzeltildi) | **BAŞARILI (200)** | 11/11 görsel URL döndü → `raw/images-1024-1920.json` |
| 4 | PNG indirme (11 URL) | **BAŞARILI** | **12 dosya** indirildi, 0 hata (`reference/figma/png/`) |

### İstek #2 neden başarısız oldu (kod hatası, API hatası değil)

PowerShell'de `".../images/$key?ids=..."` ifadesinde `$key?ids` tek bir değişken
adı olarak yorumlandı (PowerShell değişken adı `?` karakterini kabul eder). Bu
yüzden URL `.../v1/images/=...` biçimine dönüştü → `file_key` ve `ids`
parametreleri hiçbir şekilde iletilmedi. Düzeltme: parçaları `+` ile
birleştirmek (`'https://api.figma.com/v1/images/' + $key + '?ids=' + ...`).
İkinci denemede yanıt 200 OK.

---

## 2. Çekilen node sayısı

- Benzersiz node: **11**
- 1024 breakpoint: **8** node (`extracted-1024.md`)
- 1920 breakpoint: **4** node (`extracted-1920.md`)
- Ortak node: `2831:10267` (Welcome popup) her iki breakpoint'te de kullanıldı → 8 + 4 = 12, benzersiz 11
- Ağaç taramasında toplam alt node: **1077** (her iki istek toplamı, tekrarlı)
- PNG: **12 dosya** (welcome popup her iki adlandırmaya birer kez yazıldı)

---

## 3. API'den gelmeyen / eksik veriler

Aşağıdakiler API yanıtında **yer almiyordu**; ilgili dosyalarda `API'den gelmedi`
ile işaretlendi. Uydurma değer kullanılmadı.

| Veri | Durum | Etki |
|---|---|---|
| `layoutGrids` (grid tanımı) | **1077 node'un 0'ında yok** | Figma grid'i API'den doğrulanamıyor → `grid-rules.md` içindeki kullanıcı kuralı geçerli |
| `letterSpacingUnit` | TEXT `style` nesnesinde yok (değer var, birimi yok) | Token'larda `letterSpacingUnit: "API'den gelmedi"` |
| Gölge `spread` | `DROP_SHADOW`/`INNER_SHADOW` nesnelerinde `spread` alanı yok | Shadow token'da `spread: null` + `spreadNote: "spread: API'den gelmedi"` |
| `fillStyleId` | **0 node'da yok** | Dolgular Figma style'larına bağlı değil; renk token adları node adından türetildi (`<node adı> / fill`, `/ stroke`, `/ gradient stop n`) |
| `opacity` | Yalnız **100/1077** node'da tanımlı | Diğerlerinde `API'den gelmedi (alan node'da yok)` |
| `layoutMode` / padding / gap | Yalnız **58/1077** node'da var | GROUP/RECTANGLE/VECTOR node'larda auto-layout yok (Figma davranışı) |
| `cornerRadius` | 326 node'da `cornerRadius`, 62 node'da `rectangleCornerRadii` | Diğerlerinde radius yok olarak yazıldı |
| IMAGE fill rengi | **260 dolgu IMAGE tipinde** (147 + 113) | Renk yok → renk token'ına çevrilmedi, `renk yok` olarak yazıldı |
| Üst seviye `components` map'i | Yok (yanıt kökünde `components` alanı bulunmuyor) | Her node girişinin kendi `components` map'i kullanıldı; 45 INSTANCE için `componentId → ad` eşlemesi yapılabildi |
| Dosya meta | `name`, `lastModified`, `version` var; `thumbnailUrl` var | Kullanıldı |

---

## 4. Grid: Figma ↔ kullanıcı kuralı ilişkisi

| Konu | Figma API | Kullanıcı kuralı | Sonuç |
|---|---|---|---|
| 1024 widget grid | `layoutGrids` **yok**; node `1639:9775` `GROUP` (365x171), 13 mutlak konumlu çocuk, auto-layout yok | "Aslında 2x2; uygulama 1. satır 2x2, 2. satır 1x5, 3. satır 1x5 — 2x2'ye DİREKT değil" | **Kullanıcı kuralı geçerli.** API'de çelişen bir grid verisi yok (veri hiç yok) |
| 1920 widget grid | `layoutGrids` **yok**; node `2850:21494` `GROUP` (752x184.44), 15 mutlak konumlu çocuk, auto-layout yok | "Aslında 4x4; uygulama 1. satır 4x4, 2. satır 1x8, 3. satır 1x8 — 4x4'e DİREKT değil" | **Kullanıcı kuralı geçerli** |
| mobile / tablet / 3840 / 2560 | Bu görev kapsamında node çekilmedi | Tanımlı değil | **tasarım yok, türetilecek** |

Çocuk koordinatları (API'den, bilgi amaçlı) `extracted-*.md` içindeki
`### alt node agaci` bölümünde ve `tokens-*.json → sizes` içinde
(`"<widget node adı> / tile: <çocuk adı>"` anahtarlarıyla) yer alır.

---

## 5. Üretilen dosyalar

| Dosya | İçerik |
|---|---|
| `reference/figma/raw/nodes-1024-1920.json` | Ham API yanıtı (1.018.486 byte) |
| `reference/figma/raw/images-1024-1920.json` | Görsel URL listesi (11 URL) |
| `reference/figma/png/*.png` | 12 PNG @ scale=2 |
| `reference/figma/extracted-1024.md` | 1024 node detayı + tam alt ağaç (8 node) |
| `reference/figma/extracted-1920.md` | 1920 node detayı + tam alt ağaç (4 node) |
| `tokens/tokens-1024.json` | colors 14, typography 26, shadows 22, radii 9, spacing 6, sizes 21 |
| `tokens/tokens-1920.json` | colors 12, typography 23, shadows 17, radii 9, spacing 6, sizes 19 |
| `reference/figma/grid-rules.md` | Kullanıcı grid kuralları (öncelikli) |
| `reference/figma/_extraction-notes.md` | Bu dosya |

Çıkarım betiği: `C:\temp\opencode\extract-figma.ps1` (yeniden çalıştırılabilir).

## 6. Güvenlik

- API anahtarı yalnızca `C:\www\coremusic.net\.ai\.env.figma` içinde
  (`FIGMA_TOKEN=...`). Bu dosya `.gitignore` satırı `.ai/.env.*` ile yok sayılır
  (doğrulandı: `git check-ignore` eşleşiyor).
- Anahtar hiçbir `.md` dosyasına, ham JSON'a, PNG'ye, loga veya
  `_extraction-notes.md` içine yazılmamıştır.

---

## 7. Extract: node 1047:15802 (2026-09-27)

- **Talep:** Figma node `1047:15802` (file `NFpX9bq58oApWJPgBK5Heo`) API'den çekilecek;
  ham JSON, PNG, extracted Markdown, token güncellemesi `.ai\ui-design` altına yazılacak.
- **Node:** `Core Music - Linux Pi` / tipi `CANVAS` (sayfa) / breakpoint **1024**
  (27 child'ın çoğu 1024×600 FRAME) → hedef `tokens-1024.json`.
- **API sürümü:** `lastModified=2026-09-21T17:51:56Z`, `version=2401752188763178639`.
- **Token kaynağı:** yalnız `C:\www\coremusic.net\.ai\.env.figma` (bu dosyaya yazılmadı).

### 7.1 Üretilen dosyalar

| Dosya | Boyut |
| --- | --- |
| `reference\figma\raw\node-1047-15802.json` | 11.754.459 byte |
| `reference\figma\png\Core Music - Linux Pi.png` (scale=2) | 18.897.781 byte |
| `reference\figma\extracted-1047-15802.md` (13.314 satır, 13.225 node) | 3.089.672 byte |
| `tokens\tokens-1024.json` (birleştirildi) | 189.634 byte |

### 7.2 Token öncesi → sonrası (`tokens-1024.json`)

| Section | Önce | Sonra | Eklenen | Çakışan |
| --- | ---: | ---: | ---: | ---: |
| colors | 14 | 190 | +176 | 3 anahtar |
| typography | 26 | 130 | +104 | 12 anahtar |
| shadows | 22 | 84 | +62 | 2 anahtar |
| radii | 9 | 26 | +17 | 2 anahtar |
| spacing | 6 | 42 | +36 | 0 |
| **toplam** | | | **395 ekleme** | **567 çakışma / 19 anahtar** |

Doğrulama: eski hiçbir anahtar veya değer ezilmedi, `sizes` hiç değişmedi
(yedek: `C:\temp\opencode\tokens-1024.backup.json`).

### 7.3 Çakışma özeti (19 benzersiz anahtar — hiçbiri ezilmedi)

| Section | Anahtar | Mevcut (korundu) | Gelen (reddedildi) | Adet |
| --- | --- | --- | --- | ---: |
| colors | `bg / gradient stop 0` | `#FF00C8` | `#FFD0F5` | 1 |
| colors | `ProgresbarValue / fill` | `#FF65E9` | `#FF00D5`, `#FF3CE3` | 9 |
| colors | `Stroke Effect / fill` | `#373737` | `#E2E2E2` | 221 |
| radii | `Background radius` | `3` | `[2,2,0,0]` | 6 |
| radii | `KursatGurel radius` | `5` | `47`, `344.341` | 19 |
| shadows | `Progressbar shadow` | `y=0 blur=1 a=0.25` | `y=1 blur=1 a=0.15` | 9 |
| shadows | `Text shadow` | `0/0.1/1 a=0.8` | `0.1..0.5 / 0.5 / 0.5` varyantları | 14 |
| typography | `Avalon Medium 10.5px w500` | ls=0.368 | ls=0.105 | 2 |
| typography | `Avalon Medium 10px w500` | ls=1.35 | ls=0 / 0.1 / 0.2 / 0.85 | 16 |
| typography | `Avalon Medium 11px w500` | ls=0.825 | ls=0 | 1 |
| typography | `Avalon Medium 12px w500` | ls=0.936 | ls=0.12 | 1 |
| typography | `Avalon Medium 13px w500` | ls=1.014 | ls=0.13 | 1 |
| typography | `Avalon Medium 5px w500` | ls=0.675 | ls=0 / 0.39 | 17 |
| typography | `Avalon Medium 6.5px w500` | ls=0.488 | ls=0 | 2 |
| typography | `Avalon Medium 7px w500` | ls=0.546 | ls=0.595 / 0.945 | 45 |
| typography | `Avalon Medium 8.5px w500` | ls=0.638 | ls=0.298 / 1.148 | 89 |
| typography | `Avalon Medium 8px w500` | ls=0.6 | ls=0 / 1.08 | 41 |
| typography | `Avalon Medium 9.5px w500` | ls=0.712 | ls=1.283 | 50 |
| typography | `Avalon Medium 9px w500` | ls=0.495 | ls=0 / 0.765 | 23 |

12 typography çakışmasının **tamamı yalnız `letterSpacing` farkıdır**; `fontFamily`,
`fontSize`, `fontWeight`, `lineHeight`, `lineHeightUnit` birebir aynı.

### 7.4 Ekran spec kararı

**Screen spec üretilmedi.** `1047:15802` bir ekran değil, **CANVAS (sayfa)**;
27 doğrudan child'ı ayrı ekran/frame. Tek bir Kalıp D screen spec'i bu 27 ekranın
hiçbirini doğru temsil etmez; ayrıca `screens\00-ascii-art-index.md` CANVAS seviyesinde
anlamsız olurdu. Ekran spec'i istenirse child frame'lerden (ör. `1639:10160`
"Linux  1024 - Home Page") tek tek üretilmeli.

### 7.5 API'de olmayan alanlar (`API'den gelmedi` işaretlendi)

- `letterSpacingUnit` → API düz sayı döndürür, birim alanı yok.
- Gölge `spread` → API'de yok (`spreadNote` alanı ile işaretlendi).
- `absoluteBoundingBox` → CANVAS düğümünde yok.
- GLASS efektinde `radius` → API yanıtında yok.
- Bazı auto-layout'lerde `itemSpacing`/padding alanları eksik.
---

## Extract: tam cekim (2026-09-29 13:18:41)

| Node | Ad | Breakpoint | Top | Toplam | Boyut | Durum |
|---|---|---|---|---|---|---|
| `1991:12056` | Plan | bp=- | top=624 | toplam node=2618 | 2248 KB | OK |
| `1047:15802` | Linux-Pi | bp=1024 | top=27 | toplam node=13225 | 11478 KB | OK |
| `462:5874` | Linux-1920 | bp=1920 | top=15 | toplam node=758 | 714 KB | OK |
| `2161:12438` | Music-Admin | bp=1920 | top=4 | toplam node=223 | 229 KB | OK |
| `2135:19832` | Linux-Pi-Sonn-Kullanici | bp=1024 | top=2 | toplam node=66 | 61 KB | OK |
| `2003:24752` | Mobil-Sonn-Kullanici | bp=mobile | top=2 | toplam node=39 | 33 KB | OK |
| `1988:14156` | Kurumsal | bp=1920 | top=16 | toplam node=10831 | 9228 KB | OK |
| `319:2789` | Web-Laptop-1920 | bp=1920 | top=3 | toplam node=2752 | 2432 KB | OK |
| `326:3386` | Web-Monitor-3840 | bp=3840 | top=0 | toplam node=0 | 1 KB | BOS (tasarim yok) |
| `608:11052` | Web-Sonn-Kullanici-1920 | bp=1920 | top=2 | toplam node=1144 | 1106 KB | OK |
| `16:106` | Tizen-OS-Samsung | bp=tv | top=0 | toplam node=0 | 1 KB | BOS (tasarim yok) |
| `1801:12472` | Windows-CPP-App | bp=1920 | top=0 | toplam node=0 | 1 KB | BOS (tasarim yok) |
| `1801:12473` | Windows-CPP-Fullscreen | bp=1920 | top=0 | toplam node=0 | 1 KB | BOS (tasarim yok) |
| `15:403` | Web-Design-Eski | bp=1920 | top=46 | toplam node=9777 | 8892 KB | OK |
| `18:2907` | Design-System-XD | bp=system | top=38 | toplam node=21767 | 15840 KB | OK |

---

## Extract: tam cekim (2026-09-29 13:20:22)

| Node | Ad | Breakpoint | Top | Toplam | Boyut | Durum |
|---|---|---|---|---|---|---|
| `12 node` | kullanici linkleri | - | - | - | 16834 KB | OK |

---

## Extract: tam cekim (2026-09-29 13:35:43)

| Node | Ad | Breakpoint | Top | Toplam | Boyut | Durum |
|---|---|---|---|---|---|---|
| PNG | 151 hedef | scale=2 | - | - | - | 129 dosya indirildi |

---

## Extract: tam cekim (2026-09-29 14:38:25)

| Node | Ad | Breakpoint | Top | Toplam | Boyut | Durum |
|---|---|---|---|---|---|---|
| PNG | 151 hedef | scale=2 | - | - | - | 149 dosya indirildi |

---

## Extract: tam cekim (2026-09-29 19:16:45)

| Node | Ad | Breakpoint | Top | Toplam | Boyut | Durum |
|---|---|---|---|---|---|---|
| PNG | 151 hedef | scale=2 | - | - | - | 149 dosya indirildi |

---

## PNG hedef kırılımı (Faz 8b, 2026-09-29)

> Bu bölüm **üstteki ham çekim loglarını yalanlamaz** — `149 dosya indirildi` satırları o anki betik çıktısıdır ve korunur. Aşağıda o 149 sayısının **nasıl oluştuğu** ve neden "151 hedefin 2 eksiği" olarak **okunamayacağı** vardır.

**Ölçüm (API üzerinden kanıtlandı):**

```
151 hedef = 136 indirilen + 15 indirilemeyen (gizli node)
149 dizin = 136 id-prefixed (`^\d+-\d+-`) + 13 legacy (eski adlandırma)
136 + 15 = 151
```

- **Hedef = 151**: 11 kullanıcı node'u + 15 sayfanın top-level export edilebilir child'ları; `figma-extract.ps1` çıktısındaki "PNG hedef sayisi: 151" ile aynı.
- **İndirilen = 136**: id-prefixed dosya sayısı dizinden elle sayıldı.
- **Dizin toplamı 149**: 136 yeni + 13 eski adlandırmalı dosya → **149 sayısı "151 − 2" diye yorumlanamaz**, çünkü o 13'ün çoğu için id-prefixed karşılık zaten diskte duruyor.

**İndirilemeyen 15 node — kök neden `visible: false`:**

Figma `/v1/images` endpoint'i bu 15 node için **NULL** döndürüyor (HTTP 200, `images.<id> = null`). Test kapsamı: **scale=1** ve **scale=2**, **20'lik parti** sorgu ve **tek tek** sorgu → sonuç **15/15 NULL**. Ham JSON'dan geometri okunarak **15/15 node'un `visible: false` (gizli)** olduğu kanıtlandı. Figma export API gizli node'lara PNG vermez; bu yüzden `-ImagesOnly` ile yeniden denemek sonucu değiştirmez.

| id | type | W×H | page | name |
|---|---|---|---|---|
| `1491:37281` | GROUP | 4456×1476 | page-1047-15802 | Pink - Dark |
| `1491:37282` | GROUP | 4503×1569 | page-1047-15802 | other mavi |
| `1047:29966` | FRAME | 2170×60 | page-1047-15802 | Frame 4 |
| `1976:11757` | FRAME | 1024×600 | page-1047-15802 | Linux 1024 - Göz At - Tıklama Clikced |
| `1976:12013` | FRAME | 1024×600 | page-1047-15802 | Linux 1024 - Göz At - Tıklama Clikced |
| `1980:13448` | FRAME | 1024×600 | page-1047-15802 | Linux 1024 - Göz At - Tıklama Clikced |
| `1980:13692` | FRAME | 1024×600 | page-1047-15802 | Linux 1024 - Göz At - Tıklama Clikced |
| `1491:37718` | GROUP | 3880×1080 | page-462-5874 | mavi |
| `2831:13458` | INSTANCE | 506.2×198.2 | page-462-5874 | Playlist Status Div |
| `2161:12439` | GROUP | 3880×1080 | page-2161-12438 | mavi |
| `1988:15821` | GROUP | 4456×1476 | page-1988-14156 | Pink - Dark |
| `1988:16433` | GROUP | 4503×1569 | page-1988-14156 | other mavi |
| `1988:17767` | FRAME | 2170×60 | page-1988-14156 | Frame 4 |
| `1988:18007` | FRAME | 1024×600 | page-1988-14156 | Linux 1024 - Singer Page Serach = Dilso'z |
| `1988:18031` | FRAME | 1024×600 | page-1988-14156 | Linux 1024 - Singer Page Serach = S |

**13 legacy dosya** (id-prefixed olmayan ad → önceki çekim adlandırmasından; çoğu için id-prefixed karşılık diskte mevcut):

1. `1024 - Diiv2 Button.png`
2. `1024 - Footer.png`
3. `1024 - Menu En Son Şarkılar.png`
4. `1024 - Menu Oynatma Listesi.png`
5. `1024 - Player Info.png`
6. `1024 - Sıradaki Şarkı.png`
7. `1024 - Welcome Div.png`
8. `1920 - Div2 Button.png`
9. `1920 - Player Info.png`
10. `1920 - Welcome Div.png`
11. `Core Music - Linux Pi.png`
12. `Linux  1024 - Home Page.png`
13. `Linux - 1920 - Home.png`

**Kapanan iddiası:** "dizin 149 = hedef 151'den 2 eksik" **geçersizdir**. Doğru ifade: **151 hedef · 136 indirilen · 15 gizli node (`visible: false`) API'den NULL → indirilemez · 13 legacy · dizin toplamı 149.** Kaynak: `ui-design/reference/04-verification.md` §7.1.

## Extract: taze çekim — sayfa 462:5874 "Core Music" Linux 1920 (2026-10-16)

**Tetikleyici:** Figma tasarımı güncellendi → eski çekim (v2401752188763178639, 2026-09-21T17:51:56Z) geçersiz; kullanıcı talimatı: önce compare, sonra sıfırdan taze çekim.

**Yeni SSOT:** `raw/page-462-5874.json` — version `2410933437250453593` · lastModified `2026-10-16T12:56:08Z` · 3.212.649 bayt · 4 root child (CANVAS + 3452 node). Eski sürüm git geçmişinde (`c46f08a`); geçici yedek oturum temp'inde.

**Compare (eski → yeni):** 14 root çocuk silindi · 3 yeni frame eklendi (Göz At `3155:18252` · Playlist `3155:19834` · Geçmiş `3155:22392`) · Home `2831:13747` 328 → 803 node (yeniden tasarım: 5 kart satırı + widget paneli). 4 frame de 1920×1080; frame orijinleri y=-576.

**Üretilen dosyalar:**

1. `reference/figma/extracted-462-5874.md` (661.602 bayt · 3897 satır · Kalıp A) — 4 frame tam node ağacı (frame-rel koordinat, fill/stroke/effect/radius/AL/tipografi), 4 ASCII bölge haritası + lejant, §8 API-boşluk sayımı (letterSpacingUnit 0/781 · layoutGrids 0 · spread yok), hygiene kayıtları (grup adı takası · Navbar ×2 tekrar · "Fiel Manager" yazım · 461 stale TEXT adı).
2. `tokens/tokens-1920.json` yeniden üretildi — figma-tokens.ps1 Faz 9 gate: `toplam=7 | pass=5 | bos=2 | fail=0 | bos_bp=3840,tv` + `BITTI` (1920: node=28672, korunan eski=2227, PASS); çakışma günlüğü `token-conflicts.md` (400 satır).

**PNG referansları:** `.ai/.png/home-1920/` artık **4 PNG** (Home · Göz At · Playlist · Geçmiş — 2026-10-16 export); ASCII ve frame tabloları bu dosyalara bağlı, existsSync 4/4. **Not:** `01-mockup-index.md` ve `screens/00-ascii-art-index.md` "home-1920 = 1 PNG / toplam 19" sayaçları eskimiştir (diskte 25 PNG: admin-1920 3 · home-1024 12 · home-1920 4 · shared-1024 6 — envantere yeni dmin-1920/ dizini de dahil değil) — sonraki vault-sync'te düzeltilecek.

**Supersession:** `extracted-1920.md` §"Doğrulanan raw dosya" satırındaki `page-462-5874.json` atfı (2026-09-21 sürümü) artık geçersizdir; `extracted-462-5874.md` sayfa-geneli taze SSOT'tur (`extracted-1920.md`'nin birincil kaynağı `nodes-1024-1920.json`dur, dosya silinmez).

**Doğrulamalar:** vault-utf8-writer `verify` → BOM 0 · mojibake 0 · CJK 0 · NUL 0 · 3897 satır · wiki-link-check repo kökünden (aşağıdaki sonuç).

## Extract: refresh çekim - sayfa 462:5874 "Core Music" Linux 1920 (2026-10-17)

**Tetikleyici:** kullanıcı talimatı - 2026-10-16 çekimi ertesi gün eskidi; tüm figma verisi + ASCII art tazelenecek.

**Yeni SSOT:** `raw/page-462-5874.json` - version `2411290726990257863` - lastModified `2026-10-17T12:59:45Z` - 3.414.381 bayt - 4 root child (CANVAS + 3644 node). Eski sürüm yedekte: `C:\temp\opencode\page-462-5874-OLD-20261016.json` (v`2410933437250453593` - 3.212.649 bayt).

**Compare (eski → yeni):** node 3452 → 3644 (+192; Home `2831:13747` 803 sabit · Göz At `3155:18252` +64 · Playlist `3155:19834` +64 · Geçmiş `3155:22392` +64) - root child silindi/eklenmedi - TEXT 781 → 844 - gizli efekt 0 → 3 - **frame rename:** `Linux - 1920 - Home` → `Linux 1920 - Home` (PNG adı da değişti).

**Üretilen dosyalar:**

1. `reference/figma/extracted-462-5874.md` v1.1.0 (710.478 bayt · 4091 satır · Kalıp A) - format kalibrasyonu: eski doc ile satır-hizalı diff **3814/3814 satır**, kalan 3 fark bilinçli (eski PNG adı diskte yok ×2; §9 ağaç satırı 3448 → gerçek 3447 düzeltmesi). Sayımlar: 3644 node · 3639 ağaç satırı · wiki-link 16 · TEXT mismatch 485 · AL 98 · efekt 1458 (gizli 3) · INSTANCE 287 · PNG 4/4 (existsSync).
2. `tokens/tokens-1920.json` yeniden üretildi - figma-tokens.ps1 Faz 9 gate: `toplam=7 | pass=5 | bos=2 | fail=0 | bos_bp=3840,tv` + `BITTI` (1920: node=28864, korunan eski=2469, PASS); çakışma günlüğü `token-conflicts.md` (400 satır).

**PNG referans güncelle (15 satır):** `Linux - 1920 - Home.png` → `Linux 1920 - Home.png` - `01-mockup-index.md` ×3 · `screens/00-ascii-art-index.md` ×1 · `screens/T17-monitor-22fhd/home-dashboard.md` ×2 · `welcome-popup.md` ×2 (yalnız aktif PNG satırları) · `reference/04-verification.md` ×1 · `reference/legacy-inventory.md` ×1 (1 → 4 PNG) · `screens/T07-embedded/{browse,home-dashboard}.md` ×2 · `tokens/{design-tokens-master,color-palettes}.md` ×2 (node adı) · `.png/home-1920/CLAUDE.md` (1 → 4 PNG + rename notu). **Tarihsel kayıt satırları DEĞİŞTİRİLMEDİ** (welcome-popup 2026-09-28 notları ×3 · 00-ascii-art-index L121 · extracted-1920.md L13 · 13 legacy dosya listesi).

**Gate ölçümü (repo kökünden, 2026-10-17):** screens-frontmatter `24 dosya / 0 sorunlu` · kalip-abc `A:21·0 / B:21·0 / C:52·1` (tek sorunlu: `prompt/component/aaaa.md` - commit `c46f08a` ile önceden var, bu oturum dışı) · wiki-link-check `347 link / 6 raporlanan` = 6 sahte (§13.7 listesi ile birebir) → gerçek kırık 0 · extracted doc vault-utf8-writer verify: BOM yok, UTF-8 geçerli.

**Açık konu:** `.templates/ui-design/screen-spec-template.md` L42 "19 PNG / home-1920 1" envanter iddiası eskimiş (diskte 25 PNG / home-1920 4) - şablon statik, sahip onayı ile güncellenmeli.

**Commit:** atılmadı (subagent kuralı - orkestratöre ait).

---

## Extract: refresh çekim — 462:5874 (Linux 1920) + YENİ sayfa 3175:13501 (Linux 3840) — 2026-10-18

**Tetikleyici:** kullanıcı talimatı — yeni Figma verisi (462:5874 güncellendi + yeni sayfa "Core Music - Linux 3840") API'den çekilecek; eskiler tazelenecek; `.png` referansları verilecek.

**Çekim:** `C:\temp\opencode\fetch-20261018.ps1` (token yalnız `.ai/.env.figma`). İki sayfa tek oturumda çekildi (21:00).

| Sayfa | Ad | Sürüm | lastModified | Bayt | Node (CANVAS+frame) | Durum |
|---|---|---|---|---|---|---|
| `462:5874` | Core Music (Linux 1920) | `2411746660332637475` | `2026-10-18T18:01:58Z` | 3.424.959 | 3654 | **tazeleme** (önceki `2411290726990257863` / `2026-10-17T12:59:45Z` yedekte `C:\temp\opencode\page-462-5874-OLD-20261017.json`) |
| `3175:13501` | Core Music - Linux 3840 | `2411746660332637475` | `2026-10-18T18:01:58Z` | 814.298 | 814 | **yeni sayfa** (vault'a ilk kez giriyor) |

**Compare (462:5874 — 2026-10-17 → 2026-10-18):** toplam node 3644 → 3654 (+10); yalnız Home `2831:13747` değişti (803 → 813 node · 1. seviye çocuk 12 → 13 · TEXT 844 → 847); diğer 3 frame sabit (Göz At 338 · Playlist 793 · Geçmiş 1709). Root child silinmedi/eklenmedi, rename yok.

**3175:13501 (yeni):** 1 frame `3175:13502` "Linux 3840 - Home" 3840×2160 · 13 çocuk · TEXT 222 · INSTANCE 53 · VECTOR 152 · grup adları 1920 ile aynı (Footer · Navbar · Player Info · Div2 Button · Önerilen Sanacılar/Albumler · En Son Dinlenen …). Boş öncül sayfa `326:3386` ("Web-Monitor-3840") ile ilişkisi extracted doc §2.2'dedir — farklı sayfalardır, biri diğerinin sürümü değildir; `raw/page-326-3386.json` korunur.

**PNG export (`png-export-20261018.ps1`):**

| Hedef | Ölçek | Dosya | Bayt |
|---|---|---|---|
| `.ai/.png/home-1920/` ×4 | scale=2 | Home · Göz At · Playlist · Geçmiş | 6.095.027 – 7.717.587 |
| `.ai/.png/home-3840/` ×1 | scale=1 | `Linux 3840 - Home.png` | 6.665.774 |
| `reference/figma/png/` (id-prefixed) ×5 | scale=2/1 | 4× `3155-*`/`2831-*` + `3175-13502-Linux-3840---Home` | — |

`.ai/.png` toplam: **26 PNG** (admin-1920 3 · home-1024 12 · home-1920 4 · home-3840 1 · shared-1024 6) · `reference/figma/png` **154** (149 + 5). `.png/home-3840/CLAUDE.md` oluşturuldu; `.png/CLAUDE.md` envanteri 19 → 26'ya güncellendi.

> ⚠️ **Eşzamanlı oturum uyarısı:** `.png/home-1920/Home.png` + `.png/home-3840/` bu oturumdan **önce** (20:58) başkası tarafından yazılmış — paralel oturum ihtimali. PNG'ler sürümle senkron yeniden export edildi (21:12–21:15); extracted/token yüzeylerine başka yazım tespit edilmedi.

**Token üretimi (`figma-tokens.ps1`, repo kökünden):** Faz 9 gate `toplam=7 | pass=6 | bos=1 | fail=0 | bos_bp=tv` + `BITTI` — **3840 ilk kez doldu** (`tokens-3840.json`: colors 278 · typography 168 · shadows 153 · radii 64 · spacing 50 · sizes 634; boş öncül `326:3386` kaynağı boş kaldı). 1920 tazelendi (node=28874 · korunan eski=2469). Çakışma günlüğü `token-conflicts.md` (400 satır). Yedek: `C:\temp\opencode\tokens-backup-20261018/`. `figma-tokens.ps1` §SRC 3840 satırı güncellendi (`page-3175-13501.json` + `page-326-3386.json`); `EXPECT_EMPTY` artık yalnız `tv`.

**Extracted doc üretimi (`C:\temp\opencode\gen-extracted.mjs` — parametrik):**

1. `extracted-462-5874.md` **v1.2.0** (2026-10-18) — 713.086 bayt · 4101 satır · 3654 node · 3649 ağaç satırı · wiki-link 16 · PNG 4/4 · §2.2 eski→yeni karşılaştırma (eski 3644 → yeni 3654, Home +10) · statik prosa id kontrolü TAMAMI mevcut. Eski doc yedekte `C:\temp\opencode\extracted-462-5874-OLD-20261018.md`.
2. `extracted-3175-13501.md` **v1.0.0** (2026-10-18, YENİ) — 1055 satır · 814 node · 812 ağaç satırı · wiki-link 16 · PNG 1/1 · §2.2 boş-öncül ilişkisi · §5.3'te 1920'ye özgü hygiene notları basılmadı (farklı sayfa, dinamik sayım).

Üreteç düzeltmeleri: §istatistik/§Quality Report numaraları artık dinamik (`SN = 4 + frame`, 1920 → §8/§9 · 3840 → §5/§6) · `--created` parametresi (regen'de `date:` korunur) · Ham JSON SSOT satırı `PAGEFILE`'e bağlandı · statik-prosa id uyarısı yalnız 462:5874'te.

**Vault senkron (bu oturum):** `01-mockup-index.md` v6.4.0 (26 PNG · §3.5 Linux 4K · figma arşivi 154 · CatID tablosuna `3175:13501` eklentisi) · `screens/00-ascii-art-index.md` v6.4.0 (toplam PNG 26 · §7 madde 6: spec'siz PNG 3 → 4) · `.png/CLAUDE.md` · `.png/home-3840/CLAUDE.md` (yeni) · `tokens/design-tokens-master.md` + `tokens/color-palettes.md` (3840 artık dolu) · `reference/04-verification.md` · `reference/legacy-inventory.md`.

**Açık konu:** `.templates/ui-design/screen-spec-template.md` L42 envanter iddiası ("19 PNG / home-1920 1") eskimiş — şablon statik, sahip onayı ile güncellenmeli (2026-10-17'de de açıktı).

**Commit:** atılmadı (subagent kuralı — orkestratöre ait).
