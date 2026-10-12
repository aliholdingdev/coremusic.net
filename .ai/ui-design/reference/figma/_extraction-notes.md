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
