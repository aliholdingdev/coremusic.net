# Widget Alanı Grid Kuralları

> **ÖNCELİK:** Bu dosyadaki kurallar **kullanıcı kuralıdır**. Figma API verisiyle
> çelişirse bu dosyadaki kural geçerlidir. Figma'dan gelen ölçüler yalnızca
> bilgi amaçlıdır, uygulama davranışını belirlemez.

Kaynak: kullanıcı talimatı (2026-09-24).
Figma dosyası: Core Music (`NFpX9bq58oApWJPgBK5Heo`), lastModified `2026-09-21T17:51:56Z`.

---

## 1024 breakpoint

**Kural (uygulanacak):**

- Figma'da widget alanı **aslında 2x2 grid** görünüyor.
- Uygulama ise şu şekilde olacak:
  - **1. satır: 2x2**
  - **2. satır: 1x5**
  - **3. satır: 1x5**
- Yani uygulama **2x2 grid'e DİREKT değil** — 2. ve 3. satırlar 1x5 sıralıdır.

**Figma API'den gelen bilgiler (bilgi amaçlı, kural değil):**

| Öğe | Değer | Kaynak |
|---|---|---|
| Widget alanı node | `1639:9775` ad: `Diiv2 Button` tip: `GROUP` | `raw/nodes-1024-1920.json` |
| Widget alanı boyutu | w=365 h=171 | `absoluteBoundingBox` |
| Auto-layout | yok (`layoutMode` alanı bu node'da yok) | API |
| Grid tanımı (`layoutGrids`) | **API'den gelmedi** — ham JSON'da dolu `layoutGrids` = 0 / 11 üst düzey node | API |
| Doğrudan çocuk sayısı | 13 (mutlak konumlu, GROUP içinde) | API |

---

## 1920 breakpoint

**Kural (uygulanacak):**

- Figma'da widget alanı **aslında 4x4 grid** görünüyor.
- Uygulama ise şu şekilde olacak:
  - **1. satır: 4x4**
  - **2. satır: 1x8**
  - **3. satır: 1x8**
- Yani uygulama **4x4 grid'e DİREKT değil** — 2. ve 3. satırlar 1x8 sıralıdır.

**Figma API'den gelen bilgiler (bilgi amaçlı, kural değil):**

| Öğe | Değer | Kaynak |
|---|---|---|
| Widget alanı node | `2850:21494` ad: `Div2 Button` tip: `GROUP` | `raw/nodes-1024-1920.json` |
| Widget alanı boyutu | w=752 h=184.44 | `absoluteBoundingBox` |
| Auto-layout | yok (`layoutMode` alanı bu node'da yok) | API |
| Grid tanımı (`layoutGrids`) | **API'den gelmedi** | API |
| Doğrudan çocuk sayısı | 15 (mutlak konumlu, GROUP içinde) | API |

---

## Diğer breakpoint'ler

| Breakpoint | Durum |
|---|---|
| mobile | **tasarım var** — ham JSON `raw/page-2003-24752.json` 40 node / 21 renk; `figma-tokens.ps1` → `node=40 · c=21 t=7 s=2 r=11 sp=2 sz=35 · DURUM: PASS` |
| tablet | **tasarım yok, türetilecek** — ⚠️ VERIFICATION REQUIRED: çekim sayfası yok ("tasarım yok" iddiası raw sette kanıtlanamaz) |
| 3840 | **tasarım yok, türetilecek** — gerekçe: `raw/page-326-3386.json` yalnız kök node (node=1), 6 token bölümünün tamamı 0 → `figma-tokens.ps1` → `BOS: tasarim yok` · `DURUM: BOS` |
| 2560 | **tasarım yok, türetilecek** — ⚠️ VERIFICATION REQUIRED: çekim sayfası yok ("tasarım yok" iddiası raw sette kanıtlanamaz) |

Bu breakpoint'ler için Figma dosyasında bu görev kapsamında node çekilmedi (mobile hariç —
`raw/page-2003-24752.json` 40 node çekilmiştir); kurallar henüz tanımlı değil, mevcut tasarım/sistemden türetilecek.

---

## Figma ile kuralın ilişkisi (çelişki notu)

- Figma REST API `layoutGrids` alanı **yalnız `raw/nodes-1024-1920.json` alt kümesinde`
  hiçbir node'da bulunmuyor (o alt kümede dolu `layoutGrids` = 0; üst düzey node key 11).
  Bu nedenle "Figma'da 2x2 / 4x4 grid" ifadesi API verisiyle doğrulanamaz — kullanıcı beyanıdır ve **kural olarak geçerlidir**.
- Widget alanı node'ları `GROUP` tipindedir; GROUP'lar Figma'da auto-layout ve
  grid taşımaz. API'de görünen çocuklar mutlak `x/y` koordinatlarıyla durur.
- API'deki çocuk koordinatları yukarıdaki satır düzenlerine (2x2 / 1x5 / 1x5 ve
  4x4 / 1x8 / 1x8) **uymaz**; bu bir çelişki değil, Figma'daki mevcut çizim
  düzenidir. Uygulamada **bu dosyadaki kural** esastır.

**`layoutGrids` gerçek sayım tablosu (2026-09-29, tüm raw set — ham JSON regex sayımı):**

| Raw dosya | Breakpoint/grup | Dolu `layoutGrids` |
|---|---|---|
| `raw/page-1047-15802.json` | 1024 home | 1 |
| `raw/page-1988-14156.json` + `raw/page-15-403.json` | 1920 grubu | 1 + 1 = 2 |
| `raw/page-18-2907.json` | system | 21 |
| `raw/page-326-3386.json` | 3840 | 0 |
| `raw/page-16-106.json` | tv | 0 |
| `raw/page-2003-24752.json` | mobile | 0 |
| `raw/nodes-user-12.json` | system node'larını içerir (tekrar sayma) | 21 |
| **Toplam (sayfa bazında, tekrarsız)** | **1024:1 · 1920:2 · system:21 · 3840:0 · tv:0** | **24** |

> `.ai/CLAUDE.md` L681: *"O ölçüm yalnız `nodes-1024-1920.json` **alt kümesi** için geçerlidir."*
> Bu dosyadaki "hiçbir node'da yok" ifadesi de yalnız o alt küme içindir; tüm raw sette
> dolu `layoutGrids` vardır (yukarıdaki tablo).

---

## Doğrulama

- **Ölçüm tarihi:** 2026-09-29
- **Yöntem:** ham JSON regex sayımı (`"layoutGrids": [{` = dolu) + `figma-tokens.ps1` kapısı (repo kökünden çalıştırıldı).
- **`figma-tokens.ps1` kapısı:** `SONUC -> toplam=7 | pass=5 | bos=2 | fail=0 | bos_bp=3840,tv` → `BITTI`, exit 0.
- **Kapı kanıtı (2026-09-29):** `mobile -> tokens-mobile.json · node=40 · c=21 t=7 s=2 r=11 sp=2 sz=35 · DURUM: PASS` · `3840 -> tokens-3840.json · node=1 · c=0 t=0 s=0 r=0 sp=0 sz=0 · BOS: tasarim yok · DURUM: BOS`.
- **Ham dosya:** `raw/nodes-1024-1920.json` → `lastModified 2026-09-21T17:51:56Z` / `version 2401752188763178639` (değişmedi; başlıklarla birebir).
- **Bu revizyonda düzeltilen 4 hata:** (1) `layoutGrids` satırındaki yeniden üretilemeyen sayı çıkarıldı, ölçülen değer yazıldı; (2) "hiçbir node'da yok" genellemesi alt kümeye sınırlandırıldı + gerçek sayım tablosu; (3) `mobile` satırı "tasarım var" (40 node / 21 renk, PASS); (4) `3840` satırına gerekçe eklendi, `tablet`/`2560` ⚠️ VERIFICATION REQUIRED olarak işaretlendi.
- **Değişmeyenler (kullanıcı kuralı):** 1024 → 2×2 + 1×5 + 1×5 (12 slot) · 1920 → 4×4 + 1×8 + 1×8 (20 slot) · ÖNCELİK bloğu · GROUP notu.
