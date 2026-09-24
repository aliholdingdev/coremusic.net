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
