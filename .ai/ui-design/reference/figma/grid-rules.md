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
| Grid tanımı (`layoutGrids`) | **API'den gelmedi** — 1077 node'un hiçbirinde yok | API |
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
| mobile | **tasarım yok, türetilecek** |
| tablet | **tasarım yok, türetilecek** |
| 3840 | **tasarım yok, türetilecek** |
| 2560 | **tasarım yok, türetilecek** |

Bu breakpoint'ler için Figma dosyasında bu görev kapsamında node çekilmedi;
kurallar henüz tanımlı değil, mevcut tasarım/sistemden türetilecek.

---

## Figma ile kuralın ilişkisi (çelişki notu)

- Figma REST API `layoutGrids` alanı **hiçbir node'da bulunmuyor** (1077 node
  içinde 0 isabet). Bu nedenle "Figma'da 2x2 / 4x4 grid" ifadesi API verisiyle
  doğrulanamaz — kullanıcı beyanıdır ve **kural olarak geçerlidir**.
- Widget alanı node'ları `GROUP` tipindedir; GROUP'lar Figma'da auto-layout ve
  grid taşımaz. API'de görünen çocuklar mutlak `x/y` koordinatlarıyla durur.
- API'deki çocuk koordinatları yukarıdaki satır düzenlerine (2x2 / 1x5 / 1x5 ve
  4x4 / 1x8 / 1x8) **uymaz**; bu bir çelişki değil, Figma'daki mevcut çizim
  düzenidir. Uygulamada **bu dosyadaki kural** esastır.
