---
reference_doc: "CoreMusic UI Design — Web Araştırması"
title: "CoreMusic — UI Design Web Araştırması Raporu (16 Konu)"
type: research
category: ui-design
date: 2026-09-24
updated: 2026-09-24
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic UI Design — Web Araştırması Raporu

> Tarih: 2026-09-24
> Yöntem: exa web_search_exa ile 16 konuda arama; her konu için 2-4 kaynak.
> İşaretler: **[doğrulanamadı]** = kaynaklarda doğrudan teyit edilemedi, mantık yürütme ile eklenmiş not.
> Not: Bu dosya bir **araştırma girdisidir** — Kalıp C üretim promptu değildir (üretim promptları `prompt/{component,page,screen,layout}/NN-*.md` altında Kalıp C ile üretilir).

---

## 1. Figma MCP API — nedir, nasıl bağlanır, AI agent Figma'yı nasıl okur

- **Figma MCP server**, Figma'nın Model Context Protocol (MCP) standardını izleyen resmi sunucusudur; AI agent'lara tasarım bağlamı (değişkenler, bileşenler, stil bilgisi) taşır ve remote uçtan "canvas'a yazma" imkânı verir.
- İki bağlantı modu vardır: **Remote MCP server** (`https://mcp.figma.com/mcp`, önerilen — OAuth giriş gerektirir, masaüstü uygulaması gerekmez) ve **Desktop MCP server** (Figma masaüstü uygulaması üzerinden yerel çalışır, kurumsal/kısıtlı kullanım için).
- Claude Code'a bağlama komutu: `claude mcp add --transport http figma https://mcp.figma.com/mcp` (tüm projeler için `--scope user`). Codex: `codex mcp add figma --url https://mcp.figma.com/mcp`. Cursor `mcp.json` içine `{"servers": {"figma": {"url": "https://mcp.figma.com/mcp", "type": "http"}}}`.
- **Okuma akışı link tabanlıdır**: Figma'da katman/seçim üzerine sağ tık → "Copy link to selection" → MCP client'a URL yapıştırılır; istemci URL'den `node-id`'yi çıkarır, MCP sunucusu o node'un bilgilerini döner. İstemcinin URL'ye doğrudan gitmesi gerekmez.
- MCP sunucusunun başlıca araçları: (1) kod/bağlam aracı, (2) görsel aracı, (3) değişken tanımı aracı. **MCP sunucusu kod üretmez, yalnızca tasarım bağlamını ve detaylarını gönderir**; final kodu agent (Claude vb.) üretir.
- Dev Mode + **Code Connect** bağlanırsa, agent Figma'daki bileşenin karşılık geldiği gerçek kod dosyasının yolunu alabilir; screenshot ile kıyaslandığında token/kullanım önemli ölçüde düşer.
- Sadece Figma MCP Kataloğu'nda listelenen istemciler bağlanabilir (Claude Code, Cursor, VS Code, Copilot CLI, Gemini CLI, Windsurf vb. — destek tablosu resmi kılavuzda).
- Write-to-canvas (tasarımı geriye yazma) yalnızca remote sunucu ile çalışır ve Figma'nın "skills" paketinin kurulmasını önerir.
- Skills (beceriler), MCP araçlarının **nasıl sıralanacağını** agent'a öğretir; MCP tool'un kendisi yeni yetenek eklemez. Örnek workflow'lar: Code Connect ile bileşen eşleme, kod tabanıyla uyumlu design system kuralları üretimi, tasarımı production koda çevirme.
- MCP istemci desteği tablosu (resmi kılavuzdan): Claude Code / Cursor / VS Code / Copilot CLI / Warp / Xcode (remote) ✓ write-to-canvas; Amazon Q / Openhands yalnızca desktop; Replit yalnızca remote.

### Bağlantı yapılandırmaları (doğrudan kopyalanabilir)

```bash
# Claude Code (önerilen: Figma plugin'i ile skills de gelir)
claude mcp add --transport http figma https://mcp.figma.com/mcp
claude mcp add --scope user --transport http figma https://mcp.figma.com/mcp
# Codex: codex mcp add figma --url https://mcp.figma.com/mcp
# Cursor / VS Code mcp.json: {"servers":{"figma":{"url":"https://mcp.figma.com/mcp","type":"http"}}}
```

**Kaynaklar**
- https://developers.figma.com/docs/figma-mcp-server/
- https://help.figma.com/hc/en-us/articles/32132100833559-Guide-to-the-Figma-MCP-server
- https://developers.figma.com/docs/figma-mcp-server/remote-server-installation/
- https://www.figma.com/blog/introducing-figma-mcp-server/

---

## 2. Figma REST API — /v1/files/:key/nodes, /v1/images/:key, X-Figma-Token header

- Tüm REST uçları tek taban URL ile: `https://api.figma.com` — örn. `GET https://api.figma.com/v1/files/:key`.
- Kimliklendirme: kişisel erişim token'ı (PAT) **`X-Figma-Token` header**'ında gönderilir (script/kişisel kullanım); uygulamalar için OAuth2 (`file_content:read`, `files:read` scope'ları).
- `GET /v1/files/:key/nodes?ids=1:2,1:3` — virgülle ayrılmış node ID listesi ile yalnızca istenen alt ağaçları döner. Query parametreleri: `ids` (zorunlu), `version`, `depth` (bu uçta derinlik istenen node'dan sayılır, kökten değil), `geometry=paths` (vektör verisi için; **varsayılan olarak vektör verisi dönmez**), `plugin_data`.
- `file_key` ve `node-id` herhangi bir Figma URL'sinden ayrıştırılabilir: `https://www.figma.com/file/{file_key}/{title}?node-id={id}`.
- Dönen şema: `name`, `lastModified`, `editorType`, `thumbnailUrl`, `document` (DOCUMENT tipinde kök), `components` (node ID → bileşen meta verisi; instance'ın hangi bileşenden geldiğini bulmak için), `styles` (style ID → meta). **`nodes` map'i `null` değerler içerebilir** (node o dosyada yoksa).
- `GET /v1/images/:key?ids=...&format=png|jpg|svg|pdf&scale=0.01..4` — node'ları render eder; `images` map'i node ID → görsel URL döner. **Görsel URL'leri 30 gün sonra geçersizleşir**; maksimum 32 megapiksel, üzeri ölçeklenir. SVG için `svg_outline_text`, `svg_include_id`, `svg_include_node_id` bayrakları vardır.
- Hata kodları: 400 (geçersiz parametre, `err` alanı belirtir), 403 (token geçersiz/süresi dolmuş), 404 (dosya yok), 429 (rate limit), 500.
- Resmi OpenAPI spesifikasyonu açık kaynakta: `figma/rest-api-spec` reposu (openapi.yaml).

### Tipik istekler

```bash
# Node alt ağacını çek (derinlik 2: hedef + doğrudan çocuklar)
curl -H "X-Figma-Token: $FIGMA_TOKEN" \
  "https://api.figma.com/v1/files/FILE_KEY/nodes?ids=1:2,1:3&depth=2"

# Vektör path'leriyle (geometry=paths) ve SVG render (node-id atributlu)
curl -H "X-Figma-Token: $FIGMA_TOKEN" \
  "https://api.figma.com/v1/files/FILE_KEY/nodes?ids=0:1&geometry=paths"
curl -H "X-Figma-Token: $FIGMA_TOKEN" \
  "https://api.figma.com/v1/images/FILE_KEY?ids=0:1&format=svg&svg_include_node_id=true"
```

**Pratik notlar**

- Dosya tamamı yerine **her zaman `/nodes` + `ids` + `depth`** kullanın; tam dosya büyük projelerde MB'larca JSON döner ve timeout yaratır.
- `depth` bu uçta hedef node'dan sayılır (1 = sadece doğrudan çocuklar).
- Görsel URL'leri imzalıdır ve **30 gün** yaşar; cache'lenebilir ama sonsuz değil.
- PAT'ları istemciye gömmeyin; ortam değişkeni (`FIGMA_TOKEN`) veya OAuth kullanın.

**Kaynaklar**
- https://developers.figma.com/docs/rest-api/file-endpoints/
- https://developers.figma.com/docs/rest-api/
- https://raw.githubusercontent.com/figma/rest-api-spec/main/openapi/openapi.yaml
- https://developers.figma.com/docs/rest-api/llms.txt

---

## 3. Figma UI okuma — node ağacı, auto-layout, component instance okuma

- Her katman/obje dosyada bir **node (alt ağaç)** olarak temsil edilir; `GET /v1/files/:key` tüm belgeyi, `/nodes` ucu seçili alt ağaçları döner. Node tipleri: `DOCUMENT`, `CANVAS`, `FRAME`, `TEXT`, `RECTANGLE`, `INSTANCE`, `COMPONENT` vb.
- **Auto-layout okumak için** ilgili alanlar: `layoutMode` (`NONE` | `HORIZONTAL` | `VERTICAL` | `GRID`), `primaryAxisSizingMode` / `counterAxisSizingMode` (`FIXED` | `AUTO`), `primaryAxisAlignItems` (`MIN` | `MAX` | `CENTER` | `SPACE_BETWEEN` | `SPACE_EVENLY` | `SPACE_AROUND`), `counterAxisAlignItems` (… | `BASELINE`), `itemSpacing`, `counterAxisSpacing`, `paddingLeft/Right/Top/Bottom`, `layoutWrap` (`NO_WRAP` | `WRAP`).
- **HUG / FILL / FIXED** karşılığı: `layoutSizingHorizontal` ve `layoutSizingVertical` alanları Figma UI'daki HUG/FILL/FIXED seçimiyle doğrudan eşleşir (docs: "maps directly to the Figma UI").
- Çocuk hizalama/büyüme: `layoutAlign` (`MIN` | `CENTER` | `MAX` | `STRETCH` | `INHERIT`), `layoutGrow` (0 = sabit, 1 = stretch), `layoutPositioning` (`AUTO` = auto-layout tarafından, `ABSOLUTE` = manuel konum). `minWidth` / `maxWidth` / `minHeight` / `maxHeight` desteklenir.
- GRID auto-layout için: `gridRowCount`, `gridColumnCount`, `gridRowGap`, `gridColumnGap`, `gridRowSizes`, `gridChildHorizontalAlign` vb. alanlar vardır.
- **Component instance okuma**: `INSTANCE` tipinde node'un `componentId` alanı hangi ana bileşenden geldiğini verir; dosya yanıtındaki `components` map'i bu ID'den bileşen meta verisine (key, name, componentSetId, remote) ulaşımı sağlar.
- Instance bazlı değerler: `componentProperties` (örn. `{Size: {type: 'VARIANT', value: 'Large'}, ButtonText#0:1: {type: 'TEXT', value: 'login'}}`) ve `overrides` (düzeltmelerin listesi). Bileşen tanımındaki `componentPropertyDefinitions` ise tüm olası varyantları (`variantOptions`) içerir.
- REST `styles` alanı, bir node'un hangi stil ID'lerini miras aldığını verir; stil adları dosyanın üst düzey `styles` alanında aranır.
- Plugin API dokümanı (InstanceNode, Shared Node Properties) REST ile aynı alan adlarını tanımlar; okuma mantığı ortaktır.

### Örnek: auto-layout + instance okunan node (REST çıktısı şekli)

```json
{
  "id": "12:34",
  "name": "PlayButton",
  "type": "INSTANCE",
  "componentId": "5:6",
  "absoluteBoundingBox": { "x": 40, "y": 120, "width": 160, "height": 48 },
  "layoutMode": "HORIZONTAL",
  "primaryAxisSizingMode": "AUTO",
  "counterAxisSizingMode": "AUTO",
  "primaryAxisAlignItems": "CENTER",
  "counterAxisAlignItems": "CENTER",
  "itemSpacing": 8,
  "paddingLeft": 16, "paddingRight": 16,
  "paddingTop": 12, "paddingBottom": 12,
  "layoutSizingHorizontal": "HUG",
  "layoutSizingVertical": "HUG",
  "layoutAlign": "INHERIT",
  "layoutGrow": 0,
  "componentProperties": {
    "Size":  { "type": "VARIANT", "value": "Large" },
    "Disabled": { "type": "BOOLEAN", "value": false },
    "ButtonText#0:1": { "type": "TEXT", "value": "Play" }
  }
}
```

### Figma auto-layout → CSS eşlemesi (okuma çıktısından layout çıkarma)

| Figma alanı | CSS karşılığı |
|---|---|
| `layoutMode: HORIZONTAL` | `display: flex; flex-direction: row` |
| `layoutMode: VERTICAL` | `display: flex; flex-direction: column` |
| `layoutMode: GRID` | `display: grid` (+ `grid-template-columns` türetilir) |
| `layoutWrap: WRAP` | `flex-wrap: wrap` |
| `itemSpacing` | `gap` |
| `paddingLeft/Right/Top/Bottom` | `padding` |
| `primaryAxisAlignItems: SPACE_BETWEEN` | `justify-content: space-between` |
| `counterAxisAlignItems: CENTER` | `align-items: center` |
| `layoutSizingHorizontal: FILL` | `flex: 1 1 auto` (veya grid'de `1fr`) |
| `layoutSizingHorizontal: HUG` | `width: max-content` / `fit-content` |
| `layoutPositioning: ABSOLUTE` | `position: absolute` (auto-layout dışı) |

- Bu eşleme Figma'nın Resmi bir tablosu değil, API alanlarının semantik karşılığıdır — **[doğrulanamadı]** resmi dokümanda tek tablo olarak verilmiyor; ancak `layoutSizing*` alanlarının dokümanı "maps directly to the Figma UI" der.

**Kaynaklar**
- https://developers.figma.com/docs/rest-api/file-node-types/
- https://developers.figma.com/docs/plugins/api/InstanceNode/
- https://developers.figma.com/docs/plugins/api/node-properties/
- https://developers.figma.com/docs/plugins/working-with-component-properties/

---

## 4. Figma effect okuma — gölge/blur/layer effect değerlerini API'den çıkarma

- Node'lardaki görsel efektler `effects` alanında durur; her efektin `type` enum'u: **`INNER_SHADOW` | `DROP_SHADOW` | `LAYER_BLUR` | `BACKGROUND_BLUR` | `TEXTURE` | `NOISE`**.
- Ortak alanlar: `visible` (bool), `radius` (blur/gölge yarıçapı; progresif blur'da bitiş yarıçapı), `blendMode` (gürültü/gölge için), `boundVariables` (variable binding).
- **Gölgeye özgü alanlar**: `color` (RGBA), `offset` (x/y vektörü), `spread` (varsayılan 0), `showShadowBehindNode` (yalnızca drop shadow; saydam piksellerin arkasında görünür mü).
- **Blur'a özgü alanlar**: `blurType` (`NORMAL` | `PROGRESSIVE`, beta); progresif blur için `startRadius`, `startOffset`, `endOffset` (normalize edilmiş koordinat: sol üst 0,0; sağ alt 1,1).
- **TEXTURE / NOISE'a özgü**: `noiseSize`, `noiseType` (`MONOTONE` | `DUOTONE` | `MULTITONE`), `density` (beta alanlar).
- CSS karşılığı çıkarımı: `DROP_SHADOW` → `box-shadow: offsetX offsetY radius spread color` (içe doğru gölge ise `INNER_SHADOW` → aynı `box-shadow` + `inset`); `LAYER_BLUR` → `filter: blur(radius)`; `BACKGROUND_BLUR` → `backdrop-filter: blur(radius)`. Bu eşleme resmi Figma belgesinde tek satırlık bir tablo olarak verilmemiştir; API alanlarının CSS karşılığı olarak yorumlanır — **[doğrulanamadı]** kesin resmi eşleme tablosu bulunamadı, fakat alan adları (radius, offset, spread, color) CSS ile birebir örtüşür.
- Figma gradient box-shadow sıklıkla `layerBlur` + `backgroundBlur` kombinasyonu ile taklit edilir; REST çıktısında bunlar ayrı effectler olarak listelenir.

### Örnek: effect array'i → CSS dönüşümü

```json
"effects": [
  { "type": "DROP_SHADOW", "visible": true, "radius": 24,
    "color": { "r": 0, "g": 0, "b": 0, "a": 0.25 },
    "offset": { "x": 0, "y": 8 }, "spread": 0, "blendMode": "NORMAL" },
  { "type": "DROP_SHADOW", "visible": true, "radius": 0,
    "color": { "r": 1, "g": 0.4, "b": 0.1, "a": 0.4 },
    "offset": { "x": 0, "y": 0 }, "spread": 2 },
  { "type": "BACKGROUND_BLUR", "visible": true, "radius": 16 }
]
```

```css
/* Yukarıdaki effects[] karşılığı */
.card {
  /* Figma'da listede önde olan ÜSTTE görür — box-shadow'da ilk önce gelir */
  box-shadow:
    0 8px 24px rgba(0, 0, 0, 0.25),
    0 0 0 2px rgba(255, 102, 26, 0.4);
  backdrop-filter: blur(16px);   /* BACKGROUND_BLUR */
}
.layer-blurred { filter: blur(var(--radius)); }                 /* LAYER_BLUR */
.inner-shadow  { box-shadow: inset 0 2px 4px rgba(0,0,0,.2); } /* INNER_SHADOW */
```

- RGBA dönüşümü: Figma renk kanalları 0-1 arası float'tır → `255` ile çarpıp `rgba()` yazın.
- `spread` box-shadow'un 4. uzunluğudur; `LAYER_BLUR`'ta `spread` yoktur, sadece `radius`.
- `visible: false` effect'ler render edilmez — filtreleyin.
- Bir node'un birden fazla `DROP_SHADOW`'u olabilir; CSS'te virgülle sıralayın (Figma sırası üsttekinden başlar).

### Düşük güvenceli / doğrulanamayan iddialar

- Figma'nın **progressive blur** (`blurType: PROGRESSIVE`) çıktısının CSS karşılığı: standart `filter: blur()` ile tam eşleşmez (konumlu gradyan blur gerektirir) — **[doğrulanamadı]** kesin CSS karşılığı bulunamadı.
- `TEXTURE` / `NOISE` effect'lerinin CSS karşılığı: spec'te doğrudan karşılığı yok, raster overlay olarak üretilir — **[doğrulanamadı]**.

**Kaynaklar**
- https://developers.figma.com/docs/rest-api/file-property-types/
- https://developers.figma.com/docs/rest-api/file-node-types/

---

## 5. Figma → HTML/PHP export — sınırlamalar (Figma Dev Mode, html.to.design, converter araçlar)

- **Figma Dev Mode** tasarım→kod yönündedir: CSS snippet'leri, ölçülendirme, asset export, Code Connect sunar; **mevcut bir Figma dosyasını okumayı varsayar, HTML'den Figma katmanı üretmez, kod üretmez sadece bağlam sağlar.** Ödemeli Figma seat gerektirir.
- Figma'nın resmi konumu: MCP sunucusu **kod değil, bağlam taşır**; kod agent tarafından üretilir. Dev Mode MCP'nin çıktısı React + Tailwind odaklıdır; doğrudan "temiz vanilla HTML/PHP" çıktısı sunmaz.
- **Yön ayrımı**: `html.to.design` / `html2design` = kod → Figma (mevcut HTML'i Figma'ya içeren araçlar); `figma.to.code` / Builder Visual Copilot / Locofy / Anima = Figma → kod. html2design'ın FAQ'ı bu ayrımı açıkça vurgular: Dev Mode "okuma", html2design "içe aktarma" aracıdır ve birbirinin rakibi değildir.
- **figma.to.code** (‹div›RIOTS): AI kullanmadan, HTML'i tek adımda dönüştürdüğünü iddia eder (1 kredi/kare); AI tabanlı araçlara göre hız/maliyet/ doğruluk avantajı sunduğunu savunur.
- **DesignToHTML** (Figma eklentisi): auto-layout → flexbox dönüşümünü (direction, gap, padding, alignment, HUG/FILL) yaptığını, her yerde "CSS'in Figma ile eşleşemediği" noktayı işaretleyen drift paneli + güven skoru verdiğini iddia eder; %100 yerel çalışır (`networkAccess: none`).
- **Sınırlılık konsensüsü (bağımsız test)**: Locofy/Anima/v0 gibi araçlar enterprise kalitesinde temiz kod üretemiyor; sonuç Figma'nın iyi uygulanmış olmasına (doğru hiyerarşi, frame adlandırma, auto-layout) çok bağlı. v0 yalnızca Next.js hedefinde başarılı; Figma'ya "piksel mükemmel" uzaklık devam ediyor.
- **PHP export için doğrudan araç bulunamadı** — araçların çıktısı HTML/CSS, React, Vue, Svelte, Tailwind ağırlıklıdır; PHP'ye özel (echo/templating) export sunan bir ürün **[doğrulanamadı]**. Pratik yol: HTML çıktısını PHP şablonlarına elle taşımak.

### Yöne göre araç haritası

| Yön | Araçlar | Çıktı | Sınırlılık |
|---|---|---|---|
| Figma → kod | Dev Mode, MCP server, Builder Visual Copilot, Locofy, Anima, v0, figma.to.code, DesignToHTML | HTML/CSS, React, Vue, Tailwind | Piksel sadakati + temizlik Figma disiplinine bağlı; PHP çıktısı yok |
| kod → Figma | html.to.design, html2design | Düzenlenebilir Figma katmanları | CSS snippet export etmez |
| Canlı UI → Figma | MCP "capture live UI" | Yeni Figma dosyası | Remote MCP + belirli istemciler |

### CoreMusic'e özel sonuç

- Hedef **PHP + vanilla HTML/CSS/JS** olduğundan: Figma MCP (bağlam okuma) + REST `/nodes` + `/images` (asset export) + elle temiz CSS yazımı en doğru kombinasyon; hazırcı converter çıktıları React/Tailwind ağırlıklı olduğu için yeniden yazım maliyeti yüksek.
- MCP yalnızca bağlam gönderdiği için "AI'a ver node-id, temiz semantik HTML + vanilla CSS yazdır" akışı araç kısıtından kaçınır.
- Piksel eşitlik hedefleniyorsa: `/images?format=png` ile referans görsel alıp screenshot karşılaştırması (diff) yapmak, converter çıktısını güvenilir kılar.

**Kaynaklar**
- https://www.figma.com/dev-mode/
- https://html2design.com/blog/figma-dev-mode-vs-html2design
- https://divriots.com/blog/introducing-figma-to-code
- https://www.builder.io/blog/convert-figma-to-html
- https://medium.com/@erezcohentlv/putting-design-to-code-ai-tools-to-the-test-fa1b0b94fa04

---

## 6. UI/UX design ilkeleri (2025-2026 güncel)

- 7 temel UX ilkesi güncelliğini koruyor: **kullanıcı odaklılık, tutarlılık, hiyerarşi, bağlam, kullanıcı kontrolü, erişilebilirlik, kullanılabilirlik** — 2026 güncellemesi bu ilkelere AI/multimodal bağlam ekliyor.
- 2026 trendinin ana ekseni: **bilişsel netlik > duyusal zenginlik** — sakin arayüzler (calm UI), gürültüsüz akış, beyaz alan ve azaltılmış karar yükü.
- **Erişilebilirlik "altyapı" olarak ele alınıyor**, özellik olarak değil: high-contrast, klavye navigasyonu, reduced motion, net dil wireframe'ten itibaren gömülü. EAA (European Accessibility Act) 28 Haziran 2025'ten beri uygulanabilir durumda.
- WCAG 2.2 AA eşikleri: gövde metni **4.5:1**, büyük metin **3:1**, UI grafik öğeleri **3:1** kontrast; touch target min **24×24 CSS px** (2.5.8); odak göstergesi görünür olmalı (2.4.7); sticky header odaklamayı gizlememeli (2.4.11).
- Form erişilebilirliği: görünür kalıcı label (placeholder-only değil), `autocomplete`, blur'da doğrulama (her tuş vuruşunda değil), `aria-live` ile asenkron durum duyurusu.
- Motion kuralı: **~300 ms üzerindeki her animasyonun reduced-motion alternatifi** olmalı; otomatik oynatma durdurulabilir olmalı; parallax reduced-motion altında kapanmalı; saniyede 3 kezden fazla flaş yok (2.3.1 nöbet riski).
- AI-native arayüzlerde şeffaflık zorunlu: öneri neden görünüyor, sistem ne kadar emin, iptal yolu ne — kontrolü kullanıcıya bıraktırmayan tasarım güven kaybettirir.
- Anti-kalıp: AAA'yı her yere uygulamak pahalı ve çoğu zaman yanlış; **AA her yerde, AAA yalnızca kritik akışlarda**.

### 2026 UI karar listesi (uygulanabilir)

1. **Sakin arayüz**: tek accent renk + nötr palet; dekoratif gradyan/parıltı yerine boşluk ve hiyerarşi.
2. **Erişilebilirlik token'ları**: her renk rolü (body-on-surface, accent-on-surface) AA kontrastlı tanımlı; state asla renk tek başına ile verilmez (icon/label eşlik eder).
3. **Hareket açıklama amaçlı**: 300 ms üstü her animasyonun reduced-motion alternatifi; autoplay yok / pause 2 sekme içinde.
4. **Şeffaf AI**: öneri "neden görünüyor" + override yolu sunar.
5. **Token tabanlı sistem**: renk, spacing, tipografi, radius, motion timing değişkenlerde — tema değişimi koda dokunmadan olur.
6. **Formlar**: görünür label, blur'da doğrulama, hata alanı + ikon + düzeltme metni, `autocomplete`.
7. **Test**: gerçek kullanıcı + ekran okuyucu (NVDA/VoiceOver) + klavye-only tur; otomatik araçlar WCAG ihlallerinin ~%30-50'sini yakalar, geri kalanı elle.

### Ölçülebilir eşikler

| Kriter | AA değeri |
|---|---|
| Gövde metni kontrastı | 4.5:1 |
| Büyük metin (18pt / 14pt bold) | 3:1 |
| UI/grafik öğesi | 3:1 |
| Minimum touch target | 24×24 CSS px (mobil için 44×44 ideal) |
| Odak göstergesi | görünür (2.4.7), sticky header altında kalmamalı (2.4.11) |
| Metin büyütme | %200'de içerik kaybı yok |
| Reflow | 320px viewport'a kadar içerik kaybı yok |
| Satır boyu | ~45-75 karakter |

**Kaynaklar**
- https://www.uxdesigninstitute.com/blog/ux-design-principles-2026/
- https://elements.envato.com/learn/ux-ui-design-trends
- https://www.forasoft.com/blog/article/ai-accessibility-ui-ux-design
- https://uxplaybook.org/articles/ui-fundamentals-best-practices-for-ux-designers

---

## 7. Güvenli/temiz CSS — modern layout (grid/flex), vanilla CSS en iyi pratikler

- **Karar kuralı** (Google Chrome rehberi): basit satır/sütun → flexbox; sayfa/komponent iskeleti (satır+sütun) → grid; iç içe hizalama → subgrid; içerik boyutuna göre paketleme → grid + `grid-auto-flow: dense`.
- **Fallback yok, intrinsic sizing kullan**: `min-content` / `max-content` / `fit-content()`, `fr`, `minmax()` sabit `width`/`height`'ten önce gelir → daha az media query, daha dayanıklı layout.
- **Mantıksal (logical) özellikler**: `inline-size`, `block-size`, `margin-inline`, `padding-block`, `inset-inline-start` — yazma yönüne duyarlı, RTL/yerelleştirme için güvenli.
- **Flexbox tuzakları**: uzun kırılmayan içerik (URL, kod) için çocuklara `min-inline-size: 0`; `flex-wrap: wrap` + `overflow` ihmal edilmez; hizalamada `safe center` öneki odaklanabilir içeriğin kırpılmasını engeller; tek öğeyi uca itmek için `margin-inline-start: auto`; `gap` child margin yerine.
- **Cascade Layers** (`@layer reset, base, layout, utilities;`) specificity savaşlarını önler — sıralama önemlidir, sonraki katman üstteki kuraldır; katmamış stiller en üstte sayılır.
- **Container queries** ile layout viewport yerine kendi boyutuna tepki verir; **Subgrid** torun hizalaması için tam tarayıcı desteğiyle hazır.
- Tekrarlanan layout'lar için modern vanilla CSS yeterli: framework'süz 4-5 utility class (`repeating-grid`, `fluid-grid`, `repeating-flex`, `fluid-flex`) ile Grid/Flex + container query kapsanır.
- `place-content` / `place-items` / `place-self` kısaltmaları iki ekseni tek bildirimde hizalar.

### Vanilla CSS iskeleti (CoreMusic için hazır kalıp)

```css
/* 1) Sıralama — specificity savaşını bitirir */
@layer reset, base, layout, components, utilities;

@layer reset {
  *, *::before, *::after { box-sizing: border-box; }
  body, h1, h2, h3, p, ul, figure { margin: 0; }
  img, svg, video { display: block; max-width: 100%; }
}

@layer base {
  :root {
    --bg: #0e0f13; --fg: #e8e9ed; --accent: #ff6a1a;
    --space-1: .25rem; --space-2: .5rem; --space-3: 1rem;
    --radius: 12px;
  }
  html { color-scheme: dark; }
  body {
    font: 1rem/1.6 system-ui, sans-serif;
    background: var(--bg); color: var(--fg);
    padding-inline: var(--space-3);   /* mantıksal özellik: RTL/LTR duyarlı */
  }
}

@layer layout {
  /* tekrar eden grid — framework'süz */
  .fluid-grid {
    --_min: var(--fluid-min, 16rem);
    --_gap: var(--fluid-gap, var(--space-3));
    display: grid;
    gap: var(--_gap);
    grid-template-columns:
      repeat(auto-fit, minmax(min(var(--_min), 100%), 1fr));
  }
  /* tek satır/sütun dağılım */
  .row { display: flex; flex-wrap: wrap; gap: var(--space-2); }
  .row > .grow { flex: 1 1 16rem; min-inline-size: 0; }
}
```

### Grid mi Flex mi — hızlı karar

| Durum | Seçim |
|---|---|
| Basit satır **veya** sütun | flexbox |
| Sayfa/komponent iskeleti (satır+sütun) | grid |
| Torunun büyükanne hizasına oturması | subgrid |
| İçerik boyutuna göre paketleme | `auto-fit` + `minmax()` |
| Yükseklikleri farklı, sıkıştırılmış dizi | grid + `grid-auto-flow: dense` |
| Yalnızca tek öğeyi uca itmek | `margin-inline-start: auto` |

**Kaynaklar**
- https://github.com/GoogleChrome/modern-web-guidance-src/blob/main/guides/css-layout/css-layout/guide.md
- https://www.smashingmagazine.com/2024/05/modern-css-layouts-no-framework-needed/

---

## 8. CSS efektleri — backdrop-filter, transforms, prefers-reduced-motion

- **En ucuz animasyon değerleri `transform` ve `opacity`** — tüm modern tarayıcılarda doğrudan compositor (GPU) katmanında çalışır; hardware acceleration hedefleniyorsa bu ikisine sadık kalın.
- `box-shadow` yerine `filter: drop-shadow(...)` animasyonu önerilir (Chrome/Firefox `filter`'ı compositor'da işler); `borderRadius` yerine `clipPath: inset(0 round Npx)` adayı olarak sunulur. `will-change: transform` katman ipucu verir ama **GPU belleği tüketir, az kullanın**.
- **backdrop-filter** yalnızca elemanın arkasındaki piksellere uygulanır; efektin görünmesi için elemanın kendisinin (veya background'ının) saydam/yarı saydam olması gerekir.
- **Backdrop root** sınırlaması (kritik tuzak): kök eleman (`<html>`), `filter: none` olmayan, `opacity < 1` olan, `mask`/`clip-path` değeri olan, başka bir `backdrop-filter` veya `mix-blend-mode` uygulayan her eleman **backdrop root** olur — yani `opacity: 0.9`'lu bir parent, child'daki backdrop-filter'ın sadece o parent ile child arasını bulanıklaştırmasına yol açar ("efekt çalışmıyor" yanılgısının kaynağı).
- İç içe backdrop-filter'lar spec'e göre **üstel performans cezası** doğurur; her katman arka planı yeniden render etmek zorunda (çift render + GPU belleği). Mümkünse tek katman glassmorphism tercih edin.
- **prefers-reduced-motion**: vestibüler bozukluğu olan ~20 yetişkinden 1'ini etkileyen hareketler (tam ekran geçiş, parallax, autoplay) için zorunlu fallback — cross-fade veya anlık kesme. 300 ms üstü animasyonlara reduced-motion varyantı eşlik etmeli (bkz. madde 6).

**Kaynaklar**
- https://motion.dev/docs/performance
- https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Properties/backdrop-filter
- https://drafts.csswg.org/filter-effects-2/

---

## 9. HTML — semantik HTML, custom elements (custom HTML tag) kuralları

- **Semantik HTML**: elemanı görünüşüne göre değil anlamına göre seç. landmark roller (`banner`, `navigation`, `main`, `contentinfo`) ekran okuyucu navigasyonunu ve AOM'de (Accessibility Object Model) yapı sağlar; landmark olan `<header>`/`<footer>` iç içe kullanımda banner/contentinfo rolünü kaybeder.
- Klasik semantik iskelet: `<header>` + `<nav>` → `<main>` → `<section>`/`<article>` (her biri başlıklı) → `<footer>`. `<div>`/`<span>` yalnızca stil salatası için.
- **Custom element adı kuralları** (WHATWG HTML 4.13): ASCII küçük harf ile başlamalı; **içinde `-` (tire) bulunmak zorunda** (namespacing + ileride HTML/SVG/MathML'e tireli eleman eklenmeyeceğinin garantisi); ASCII büyük harf içeremez; boşluk/NULL/`/`/`>` içeremez; `annotation-xml`, `color-profile`, `font-face*`, `missing-glyph` gibi rezerve adlar olamaz.
- İki tür: **autonomous custom element** (`customElements.define('my-el', class extends HTMLElement)`) ve **customized built-in** (`{extends: 'p'}` + `HTMLParagraphElement`'den miras). Autonomous olan yaygın olanıdır; `is` özniteliği autonomous elemanda kullanılmaz/etkisizdir.
- Attribute reflection: izlenecek öznitelikler `static get observedAttributes()` ile listelenir, değişimler `attributeChangedCallback()` ile yakalanır.
- Kayıt globaldir: `window.customElements` üzerinde tanımlanan ad **sayfada benzersiz** olmalı; iki kütüphane aynı `<x-card>`'ı tanımlarsa biri başarısız olur. **Scoped registries** (shadow root'a özel registry) bu çakışmayı çözür.

### Semantik iskelet iskeleti (tipik sayfa)

```html
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CoreMusic — Sayfa Başlığı</title>
</head>
<body>
  <header>                      <!-- banner landmark -->
    <a href="/" aria-label="CoreMusic ana sayfa">…</a>
    <nav aria-label="Ana menü"> … </nav></header>
  <main id="main">              <!-- sayfada tek main -->
    <section aria-labelledby="h-playlist">
      <h2 id="h-playlist">Çalma Listeleri</h2>
      <ul> … </ul>
    </section>
    <article> … </article>
  </main>
  <footer> … </footer>          <!-- contentinfo landmark -->
</body>
</html>
```

### Custom element asgari iskeleti

```html
<music-player src="/api/track/1"></music-player>
```

```js
class MusicPlayer extends HTMLElement {
  static get observedAttributes() { return ['src', 'autoplay']; }

  connectedCallback() {
    if (!this.hasAttribute('role')) this.setAttribute('role', 'region');
    if (!this.shadowRoot) this.attachShadow({ mode: 'open' });
    this.shadowRoot.innerHTML = `
      <style>:host { display: block; }</style>
      <slot></slot>
      <button part="toggle">Play</button>`;
    // lazy property upgrade
    if (this.hasOwnProperty('src')) {
      const v = this.src; delete this.src; this.src = v;
    }
  }

  attributeChangedCallback(name, oldV, newV) {
    if (oldV === newV) return;
    if (name === 'src') this._load(newV);   // yan etki
  }

  disconnectedCallback() { /* temizlik: observer/interval kapat */ }
}
customElements.define('music-player', MusicPlayer);
```

### Kural özeti

- Tire **zorunlu** (`<music-player>` ✓, `<musicplayer>` ✗), büyük harf **yasak**.
- Kurulum `connectedCallback`'ta; constructor'da sadece saf kurulum (shadow root).
- Attribute isimleri **lower-case-kebab** (`data-track-id`); `observedAttributes` dinamik olamaz (static olmalı).
- `<slot>` ile ışık DOM'u içeriği; desteklemeyen tarayıcıda içerik yine görünür.
- Custom element + Shadow DOM yalnızca "stil yalıtımı" için değil, **erişilebilirliği korumak** için de slot kullanılmasını önerir.

**Kaynaklar**
- https://html.spec.whatwg.org/multipage/custom-elements.html
- https://developer.mozilla.org/en-US/docs/Web/API/CustomElementRegistry/define
- https://web.dev/learn/html/semantic-html

---

## 10. JavaScript ES2026 — özellikleri + eski tarayıcı fallback stratejisi (caniuse, transpile sınırı)

- ECMA-262'nin **17. baskısı ES2026**, 30 Haziran 2026'da onaylandı; **7 teklif**: `Array.fromAsync`, `Error.isError`, `Math.sumPrecise`, `Uint8Array.fromBase64()/toBase64()`, `Iterator.concat`, Map/WeakMap upsert (`getOrInsert`), JSON source text access (raw JSON).
- **Önemli**: yayın yılı ≠ tarayıcı yılı. `Array.fromAsync` 2023-24'te tarayıcıya girdi ama ES2026'ya yazıldı. Edition atarken kaynaktaki tabloya bakın, hafızadan söylemeyin.
- **Kaynak çelişkisi — dikkat**: PkgPulse rehberi Iterator Helpers / Float16Array / Promise.try / Temporal'ı "ES2026" olarak; robust-skills sürümü ise bunları **ES2025** ve Temporal'ı **ES2027 (Stage 4, henüz baskıda)** olarak listeliyor. Nitekim robust-skills daha ayrıntılı (TC39 cutoff mantığı açıklanmış); **Temporal için "yerel değil, polyfill" varsayımı daha güvenli**. Kesin baskı üyeliği çelişkili ⇒ not düşüldü.
- Triage katmanları (roster): (a) **her yerde güvenli**: ES2023'e kadar olan her şey + `Object.groupBy` / `Promise.withResolvers` / `Array.fromAsync` (Node 21-22+, Safari 16.4+); (b) **modern baseline** (2024-25 tarayıcı, Node 22-23+): Set methods, iterator helpers, import attributes, `Promise.try`; (c) **uç**: Node 24+, 2025+ tarayıcılar — `RegExp.escape`, `Float16Array`, `using/await using`, `Error.isError`, Uint8Array base64; (d) **her zaman transpile**: Decorators (hâlâ Stage 3).
- **Fallback stratejisi**: caniuse/MDN BCD ile hedef tarayıcı listesi belirle → `core-js` v3.38+ polyfill'ler + Babel/SWC `@babel/preset-env` `useBuiltIns: "usage"` (yalnızca gerektiren polyfill'i enjekte eder). Temporal için resmi `@js-temporal/polyfill`. `using`/`await using` yalnızca transpile (TypeScript 5.2+ veya `@babel/plugin-proposal-explicit-resource-management`) — runtime polyfill'i yok.
- **Transpile sınırı**: syntax (class fields, optional chaining) transpile edilebilir; **API'ler (`.toSorted`, `Array.fromAsync`) transpile edilemez, polyfill gerekir**. Runtime'da olmayan API `TypeError` üretir (ör. Node 18'de `arr.toSorted is not a function`).
- Node tarafı: ES2026 çekirdek API'lerin çoğu Node 22+ ile yerel; sunucu kodu için Node 22+ hedefi polyfill yükünü kaldırır. Records & Tuples **iptal edildi** — o sözdizimi hiç üretilmemeli.

### Tarayıcı/Node sürüm tablosu (MDN BCD, 2026-08-13 kontrolü)

| Özellik | Chrome | Firefox | Safari | Node.js | Katman |
|---|---:|---:|---:|---:|---|
| `Array.fromAsync` | 121 | 115 | 16.4 | 22.0.0 | geniş |
| `Error.isError` | 134 | 138 | 18.4 (kısmi) | 24.3.0 | uç |
| `Math.sumPrecise` | 147 | 137 | 26.2 | — (yok) | uç |
| `Uint8Array` base64/hex | 140 | 133 | 18.2 | 25.0.0 | uç |
| `Iterator.concat` | 146 | 147 | 26.4 | 26.0.0 | uç |
| Map/WeakMap upsert | 145 | 144 | 26.2 | 26.0.0 | uç |
| JSON source access (raw JSON) | 114 | 135 | 18.4 | 21.0.0 | geniş |

- `Error.isError` Safari 18.4'te `DOMException`'ı tanımaz (kısmi) — host hata nesneleri için davranış testi yapın.
- `Math.sumPrecise` için Node'da kayıtlı yerel destek yok (MDN BCD, 2026-08).

### Fallback kalıbı (Babel + core-js)

```js
// babel.config.js — hedef listesi browserslist/caniuse'den gelir
module.exports = {
  presets: [
    ['@babel/preset-env', {
      useBuiltIns: 'usage',      // yalnızca ihtiyaç duyulan polyfill'i enjekte eder
      corejs: 3.38,
      targets: '> 0.5%, last 2 versions, not dead'
    }]
  ],
  plugins: ['@babel/plugin-proposal-explicit-resource-management'] // using
};
```

```js
// Runtime güvenli yazım — polyfill yoksa düşer, patlamaz
const sorted = typeof arr.toSorted === 'function'
  ? arr.toSorted((a, b) => a - b)
  : [...arr].sort((a, b) => a - b);

// Temporal: yerel yoksa resmi polyfill
import { Temporal } from '@js-temporal/polyfill';
```

### Sınır özeti

| Değişken | Transpile edilir mi? | Ne gerekir |
|---|---|---|
| Sözdizimi (`?.`, `??`, class fields, `using`) | ✅ Evet | Babel/TS plugin |
| Yeni API (`.toSorted`, `Array.fromAsync`, `getOrInsert`) | ❌ Hayır | core-js polyfill + davranış testi |
| RegExp semantiği (`/v`, `escape`) | Kısmen | regex başına kontrol |
| Temporal | Hayır | `@js-temporal/polyfill` |

**Kaynaklar**
- https://github.com/ccheney/robust-skills/blob/HEAD/skills/modern-javascript/references/ES2026.md
- https://github.com/ccheney/robust-skills/blob/main/skills/modern-javascript/SKILL.md
- https://www.pkgpulse.com/guides/ecmascript-2026-new-javascript-features

---

## 11. Custom JS element yazma (Web Components)

- Lifecycle: `connectedCallback` (DOM'a her eklemede — **kurulum mümkün olduğunca burada, constructor yerine**), `disconnectedCallback`, `adoptedCallback`, `attributeChangedCallback` (yalnızca `observedAttributes` listesindekiler), `connectedMoveCallback` (`Element.moveBefore()` ile state koruyan taşıma).
- **Shadow DOM**: stil/tüm iç yapı constructor'da shadow root içinde oluşturulur; dışarıdan gelen içerik `<slot>` ile projekte edilir (desteklenmeyen tarayıcıda içerik yine görünür/erişilebilir kalır).
- **Sayfa yazarını ezme**: `role`, `tabindex` gibi global öznitelikler önceden atanmışsa üzerine yazma — `if (!this.hasAttribute('role')) this.setAttribute('role', 'checkbox')`.
- **Lazy property**: `connectedCallback` içinde `_upgradeProperty('checked')` kalıbı, tanımdan önce atanmış değerleri yakalar (hasOwnProperty → delete → yeniden set).
- **Reentrancy tuzağı**: attributeChangedCallback içinde `setAttribute` yapıp setter'dan da reflection yapmak **sonsuz döngü** yaratır. Çözüm: getter'ı attribute'tan türet veya reflection'ı tek yönde tut; `attributeChangedCallback` yalnızca yan etki (ARIA state) uygulasın.
- Event disiplini: **içsel durum değişiminde** event fırlat (timer, yükleme); ama **host property set ettiğinde** event fırlatma — host zaten biliyor, veri bağlama sistemlerinde döngü riski.
- Accessibility: içeriği `<slot>` ile ışık DOM'undan al, `aria-*` ve focus yönetimi elemanın kendi sorumluluğunda.

### Tam örnek — küçük bir `<track-row>` elementi (CoreMusic kalıbı)

```html
<track-row title="Ay fragments" artist="Neva" duration="3:42"
           href="/track/42"></track-row>
```

```js
const template = document.createElement('template');
template.innerHTML = `
  <style>
    :host { display: block; }
    .row { display: flex; gap: var(--space-2); align-items: center; }
    :host([disabled]) button { opacity: .5; pointer-events: none; }
  </style>
  <div class="row" part="row">
    <span class="title"><slot name="title"></slot></span>
    <span class="artist"><slot name="artist"></slot></span>
    <button type="button" aria-label="Oynat">▶</button>  <!-- ≥44px -->
  </div>`;

class TrackRow extends HTMLElement {
  static observedAttributes = ['disabled'];          // class field (ES2022)

  constructor() {
    super();
    this.attachShadow({ mode: 'open' })
         .appendChild(template.content.cloneNode(true));
  }

  connectedCallback() {
    if (!this.hasAttribute('role')) this.setAttribute('role', 'listitem');
    this._onClick = () => {
      // içsel olay → host'a bildir (yukarı akış)
      this.dispatchEvent(new CustomEvent('track-play', {
        bubbles: true, composed: true,
        detail: { href: this.getAttribute('href') }
      }));
    };
    this.shadowRoot.querySelector('button')
        .addEventListener('click', this._onClick);
  }

  attributeChangedCallback(name, _, newValue) {
    if (name === 'disabled')
      this.shadowRoot.querySelector('button')
          ?.toggleAttribute('disabled', newValue !== null);
  }

  disconnectedCallback() {
    this.shadowRoot.querySelector('button')
        ?.removeEventListener('click', this._onClick);
  }
}
customElements.define('track-row', TrackRow);
```

### Kalıp özeti

| Konu | Kural |
|---|---|
| Şablon | `<template>` + `cloneNode(true)` — constructor'da bir kez |
| Olay yukarı akış | `CustomEvent` (`bubbles`, `composed: true`) |
| Öznitelik aşağı akış | attribute/property reflection, tek yön (sonsuz döngü yok) |
| Stil | `:host`, `part=` ile dışa açılan stiller |
| Kayıt | Sayfada bir kez; tekrar `define()` `NotSupportedError` atar |
| Degrade | Custom elements desteklenmiyorsa `<slot>` içeriği görünür kalır |

**Kaynaklar**
- https://web.dev/articles/custom-elements-best-practices
- https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements

---

## 12. Figma to HTML/JS/CSS converter araç karşılaştırması

- **Builder.io Visual Copilot**: AI + "Mitosis" derleyicisi; HTML/CSS, React, Vue, Angular, Tailwind çıktıları; CLI (beta) `npx` ile doğrudan editöre içe aktarma; responsive media query'leri otomatik ürettiğini iddia eder.
- **Locofy.ai**: React, React Native, Next.js, HTML/CSS; "production-ready" iddiası ama bağımsız testte **Figma'da sıkı best-practice (her frame'e isim, doğru hiyerarşi) uygulanmadan kaliteli kod vermiyor** ve diğerlerine göre daha pahalı bulundu.
- **Anima**: HTML/CSS/React; iki kullanım yolu (playground "vibe coding" vs VS Code eklentisi) **aynı tasarım için farklı kalite** verdi — playground daha temiz çıktı, VS Code eklentisi tek büyük blok üretti.
- **v0**: Figma + prompt'tan çalışan Next.js uygulaması üretti (navigasyon dahil, ilk çalıştırmada çalışıyordu) ancak Figma'ya uzaktı; Vite SPA isteğinde karışık çıktı verdi; mevcut projeye genişletilemiyor.
- **figma.to.code (‹div›RIOTS)**: AI'sız deterministik dönüşüm, 1 kredi/kare; html.to.design'ın (HTML→Figma) tersi.
- **DesignToHTML** (Figma eklentisi): güven skoru + drift paneli, auto-layout→flexbox, ZIP + `export-manifest.json` (AI agent'lar için), tamamen yerel.
- **html2design**: yönü ters (HTML→Figma); localhost/auth-gateway sayfaları dahil; manuel HTML veya URL import (Pixel = kayıpsız, Editable = best-effort).
- **Genel hüküm** (bağımsız test): enterprise-grade, bakılabilir kod için hiçbiri tek başına yeterli değil; araçlar **Figma disiplinine** (isimlendirme, hiyerarşi, auto-layout) çok bağımlı; hepsi "yarı yol" üretiyor, insan/AI temizliği üstleniyor.
- Çıktı çerçeveleri çoğunlukla React/Tailwind/Vue; **PHP hedefli export sunan araç doğrulanamadı**.

**Kaynaklar**
- https://www.builder.io/blog/convert-figma-to-html
- https://medium.com/@erezcohentlv/putting-design-to-code-ai-tools-to-the-test-fa1b0b94fa04
- https://www.dhiwise.com/post/figma-to-code-tools-comparison
- https://html2design.com/faq

---

## 13. PHP ile HTML admin panel mimarisi

- **Front controller**: tüm HTTP istekleri `public/index.php` üzerinden Apache rewrite ile girer; `Router` URI+method'u `routes/web.php` ile eşler, middleware (auth / guest / `perm:NAME`) çalıştırır, controller'a dispatch eder. Temiz URL'ler (`/users`, `/users/5/edit`).
- **Katmanlar**: `app/Config` (bootstrap, PDO singleton Connection, dotenv), `app/Controllers` (düz — alt dizin yok), `app/Core` (Controller, Model, Router, Auth, AssetRegistry, ErrorHandler), `app/Middleware`, `app/Models`, `app/Services` (ImageService, MailService, DashboardCache, LoginThrottleService, AuditLogger); `views/` PHP şablonları (`layouts/header.php`, `sidebar.php`, `footer.php` içeriği sarar); `public/` statik asset + index.php; `vendor/` Composer.
- **Autoloading**: Composer + PSR-4 (`App\*` → `app/*`); sınıf adı dizin yapısıyla eşleşir.
- **İki seviyeli yetki**: doğrudan kullanıcı atamaları + rolden miras alınan izinler (UNION, tekrarsız); kontrol başına **0 DB sorgusu** (muhtemelen önbellek — doğrudan "0 sorgu" iddiası kaynakta böyle, mekanizma **[doğrulanamadı]**); menü izin kontrolü ile uyarlanır.
- **Denetim kuralı (audit log)**: append-only; login/logout, kullanıcı/rol/iz CRUD; modül, eylem, kullanıcı, tarih aralığına göre filtre; hassas kolonlar için önce/sonra değerleri.
- Modern üretim mimarisi PHP 8.x + MySQL/MariaDB/PostgreSQL üzerinde; **RBAC veritabanı kaydı olarak modellenir, `if ($user === 'admin')` gibi kod içi sabitlerle değil**; yan etkiler (mail, ödeme API) controller'a tıkanmaz, service/queue'ya konur.
- Üç pratik yol: sıfırdan custom (aylarca mühendislik), hazır şablon/paket (AdminLTE, Laravel Nova/Filament/Backpack, Symfony EasyAdmin — haftalar), platform (dış UI; günler). CoreMusic gibi özel bir ürün için custom + hafif şablon sırtı en gerçekçi orta yol.
- Temel ekranlar: auth + dashboard, CRUD (liste + sayfalama, detail, create/update form + server-side validation, soft/hard delete onay), arama/filtre/toplu işlem, activity log.

### İstek akışı (front controller)

```text
HTTP istek → public/index.php (tek giriş)
  → Router::match(method, path)   routes/web.php
    → Middleware: auth → guest|perm:NAME
      → Controller → Service (iş kuralı) → Model (PDO prepared)
      → View (PHP layout + partial)
```

```php
// routes/web.php — deklaratif rota tablosu (güvenlik kontrolü rota üzerinde)
return [
    ['method' => 'GET',  'path' => '/tracks',           'controller' => 'Track@index',  'middleware' => ['auth', 'perm:tracks']],
    ['method' => 'POST', 'path' => '/tracks',           'controller' => 'Track@store', 'middleware' => ['auth', 'perm:tracks']],
    ['method' => 'GET',  'path' => '/tracks/{id}/edit', 'controller' => 'Track@edit',  'middleware' => ['auth', 'perm:tracks']],
    ['method' => 'POST', 'path' => '/logout',           'controller' => 'Auth@logout', 'middleware' => ['auth']]];  // auth-only örnek
```

```php
// views/layouts/sidebar.php — menü izin kontrolü (sunucu tarafı)
<?php if (\App\Core\Auth::hasPermission('tracks')): ?>
  <li><a href="<?= e(URL) ?>tracks">Parçalar</a></li>
<?php endif; ?>
```

### Mimari kararlar (CoreMusic bağlamı)

- **Framework yasak (ADR-001)** ⇒ custom Router + PSR-4 Composer autoload; hazır paket yerine ince katmanlar.
- **Validation her iki tarafta**: public akıştaki kurallar admin akışta da geçerli (envanter limiti vb.); Form Request yerine Service katmanında ortak validator.
- **RBAC veritabanında**: `roles`, `permissions`, `role_user`, `permission_role` tabloları; kod içi `if (role === 'admin')` yasak.
- **Audit log append-only** + `log.md` gibi asla satır üzerinde düzenleme yok.
- **Asset orgusu**: `public/css/modules/<sayfa>/`, `public/js/modules/<sayfa>/` — sayfa başına izole asset.
- Hata sayfaları 403/404/500 tek layout (`views/layouts/error.php`), WCAG AA token'ları, dark mode.
- Anti-FOUC: tema tercihi `localStorage` + `prefers-color-scheme` fallback + satır içi anti-flash script.

**Kaynaklar**
- https://github.com/Jandres25/php-mvc-admin-starter
- https://www.jetadmin.io/blog/php-admin-panel-architecture-options-and-how-to-build-one-that-lasts/

---

## 14. PHP music player — açık kaynak yaklaşımlar, mimari

- **PHP Music (hirotakadango/php-music)** — en dolu referans: tek `index.php` hem SPA fronted hem de REST JSON backend (`?action=...` router'ı); gömülü SQLite (`music.db`) ile sıfır kurulum; getID3 ile metadata/ indeksleme.
- **Ses motoru**: iki HTML5 `<audio>` elemanı (audio + audioB) Web Audio API ile `Source → Gain → 5 bantlı BiquadFilter (EQ) → DynamicsCompressor (AGC/ses normalizasyon) → Destination` zincirine bağlanır; 3 saniyelik gapless crossfade böyle sağlanır.
- **Stream endpoint'i** (`get_stream`): `HTTP_RANGE` isteklerini destekler, **206 Partial Content** döner (seek için şart). Service Worker stream isteklerini yakalayıp Cache Storage'taki tampondan offline range dilimleri üretir — çevrimdışı seek bile çalışır.
- **Metadata/artwork**: getID3 (PHP tarafı) — format, bitrate, süre, kapak; ID3 yazma da PHP'de yapılır (PHPAudio editöründe trim/amplify/etiket güncelleme).
- **Media Session API**: Android/iOS/macOS/Windows kilit ekranı meta verisi ve sistem prev/next/seek tuşları; `NoSleep.js` (sessiz video döngüsü) ile mobil ekran kararması engellenir.
- **Diğer açık kaynak yaklaşım — radioplay (acosonic)**: iki mod: (1) **Browser Mode** — PHP yalnızca backend (Arama proxy'si, ICY `StreamTitle`'ı raw TCP socket ile parse edip JSON döner, yt-dlp ile URL çıkarma), çalma `<audio>`'da; (2) **Server Mode** — PHP, sudo wrapper script'leri ile VLC'yi başlatır/kontrol eder (`posix_kill($pid, 0)` ile sağlık kontrolü, `wpctl` ile PipeWire ses).
- Karar: **CoreMusic için Browser Mode + HTTP Range + Web Audio API** mimarisi en düşük sunucu yükünü verir; server-side playback yalnızca yerel hoparlör hedefinde anlamlı.
- Mimari örüntüler: endpoint başına `?action=` yerine gerçek router (bkz. madde 13); streaming için asla dosyayı `echo` ile tam okuma, Range + `Content-Type` + `Content-Length` doğru gönderilmeli.

### Streaming endpoint iskeleti (PHP, Range destekli)

```php
<?php declare(strict_types=1);
$path = resolve_track_path((int)($_GET['id'] ?? 0));  // allowlist → gerçek dosya
if ($path === null) { http_response_code(404); exit; }

$size  = filesize($path);
$type  = mime_content_type($path) ?: 'audio/mpeg';
$start = 0; $end = $size - 1;

if (isset($_SERVER['HTTP_RANGE'])
    && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $m)) {
    if ($m[1] !== '') $start = (int)$m[1];
    if ($m[2] !== '') $end   = min((int)$m[2], $size - 1);
    http_response_code(206);
    header(sprintf('Content-Range: bytes %d-%d/%d', $start, $end, $size));
}

header('Accept-Ranges: bytes');
header('Content-Type: ' . $type);
header('Content-Length: ' . ($end - $start + 1));

$fh = fopen($path, 'rb');
fseek($fh, $start);
fpassthru($fh);            // tam dosyayı belleğe alma
fclose($fh);
```

### Mimari karşılaştırma

| Yaklaşım | Sunucu yükü | Seek/Range | Karmaşıklık |
|---|---|---|---|
| PHP stream (Range) + HTML5 `<audio>` | Düşük (byte aktarımı) | Yerel | Düşük — **CoreMusic için önerilen** |
| Web Audio API + dual `<audio>` (crossfade/EQ) | Düşük | Yerel | Orta — gapless + 5-band EQ |
| Server-side playback (VLC vb.) | Yüksek (sürekli süreç) | Sunucu üzerinden | Yüksek — sudo/ses aygıtı bağımlı |
| Byte-range proxy (harici radyo) | Orta | Proxy | Orta — ICY metadata parse |

- `getID3` PHP tarafında okunur; uzun kütüphaneler için taramayı CLI/job'a bölün (web request içinde binlerce dosya tarama **timeout** yaratır) **[doğrulanamadı]** — php-music tek dosyada yapar ama ölçek notu vermez.
- PWA/service worker yalnızca `https` + güvenli bağlamda çalışır (localhost hariç) **[doğrulanamadı]** — genel servis çalışanı kuralı.

**Kaynaklar**
- https://github.com/hirotakadango/php-music/
- https://hirotakadango.github.io/php-music-wiki/
- https://github.com/acosonic/radioplay

---

## 15. PHP ↔ C++ backend kod çalıştırma

- PHP'de harici süreç başlatmanın denetlenen yolu **`proc_open`**'tur: `descriptor_spec` ile pipe/stdin/stdout/stderr tanımlanır, `$pipes` üzerinden okunur, `proc_get_status` / `proc_close` ile süreç takip edilir. `popen()` yalnızca tek yönlü ise önerilir.
- **Komutu dizi olarak vermek shell'i otomatik bypass eder**: PHP kaynağı (`proc_open.c`) gösteriyor ki komut bir **array** olarak verildiğinde Windows'ta `bypass_shell = 1` ayarlanır ve argv tek tek birleştirilir; Unix'te `get_command_from_array` argv zinciri kurar. Yani `proc_open(['/path/app.exe', $arg], ...)` → **cmd.exe/shell hiç devreye girmez** → enjeksiyon yüzeyi sıfıra yakın.
- C++ backend'i çalıştırmanın pratik kalıbı: PHP `proc_open` ile C++ binary'sini argv array'i olarak başlatır → stdin'e JSON yazar → stdout'tan JSON okur → timeout uygular. Alternatifler: uzun süreç için socket/queue (bkz. radioplay'ın `/tmp/vlcnow.json` + PID sağlık kontrolü kalıbı), veya PHP extension (FFI/C) — **[doğrulanamadı]** doğrudan "PHP'den C++ FFI" örneği kaynaklarda teyit edilmedi.
- **Windows tuzakları** (PHP.net notları): `stream_set_blocking()` Windows'ta pipe'larda işe yaramaz (yalnızca socket) → **deadlock riski**; pipe read `PeekNamedPipe` ile ~32 sn polling yapar (`blocking_pipes` seçeneği bunu süresiz bloğa çevirir); stdin+stdout'a büyük veri yazıp beklemek klasik deadlock yaratır → alternatif okuma/yazma veya `stream_select`.
- `proc_open`'ın "socket" descriptor tipi Windows'ta son chunk'ların kaybına ve race condition'a yol açabilir (PHP.net uyarısı) — üretimde kullanılmamalı.
- Windows'ta `exec()` önce `cmd.exe` başlatır; `cmd.exe`'yi hiç istemiyorsan **`proc_open` + `bypass_shell`** (veya dizi komut) zorunlu.
- Uzun işler için: arka plana alma yalnızca Unix'te `> /path/file &` sözdizimiyle olur; PHP süreci child bitene kadar bekler — HTTP request içinde uzun C++ işi **timeout'a bağlıdır**, job kuyruğuna devredilmeli **[doğrulanamadı]** (kaynaklarda kuyruk önerisi doğrudan geçmiyor, ancak "PHP hang olur" riski PHP.net'te açık).

### Örnek: PHP → C++ binary (argv array, shell yok, timeout'lu)

```php
<?php declare(strict_types=1);

function run_cpp_tool(string $binary, array $args, string $stdinJson, float $timeoutSec = 10.0): array
{
    // 1) Binary yolu sabit — kullanıcı asla executable seçmez
    $allowed = ['/opt/coremusic/bin/analyzer', '/opt/coremusic/bin/encoder'];
    if (!in_array($binary, $allowed, true)) throw new RuntimeException('binary not allowed');
    // 2) Argüman allowlist — format regex'i, meta karakter yok
    foreach ($args as $a) {
        if (!preg_match('/^[a-zA-Z0-9._-]{1,128}$/', $a)) throw new RuntimeException('bad arg');
    }
    // 3) Dizi komut => Unix'te argv, Windows'ta bypass_shell=1 (shell hiç girmez)
    $desc = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $proc = proc_open(array_merge([$binary], $args), $desc, $pipes, null, null);
    if (!is_resource($proc)) throw new RuntimeException('spawn failed');
    stream_set_blocking($pipes[2], false);   // Windows 32 sn polling tuzağına karşı
    fwrite($pipes[0], $stdinJson);
    fclose($pipes[0]);
    // 4) Timeout zorunlu — sonsuz bekleme yok
    $deadline = microtime(true) + $timeoutSec;
    $stdout = '';
    while (true) {
        $chunk = fread($pipes[1], 65536);
        if ($chunk !== false && $chunk !== '') $stdout .= $chunk;
        $status = proc_get_status($proc);
        if (!$status['running']) break;
        if (microtime(true) > $deadline) {
            proc_terminate($proc, 9);
            fclose($pipes[1]); proc_close($proc);
            throw new RuntimeException('cpp tool timeout');
        }
        usleep(20000);
    }
    $stdout .= stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exit = proc_close($proc);
    if ($exit !== 0) throw new RuntimeException("exit={$exit}: {$stderr}");
    return json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
}
```

### Seçim tablosu

| İhtiyaç | Mekanizma | Neden |
|---|---|---|
| Tek yönlü çıktı, basit | `popen()` | en kolay, tek yön |
| Denetimli çift yönlü + timeout | **`proc_open` (dizi)** | shell yok, pipe + status + terminate |
| Windows'ta cmd.exe tamamen dışarı | `proc_open` + `bypass_shell` | `cmd /c` meta karakter riski kalkar |
| Uzun/ağır iş | Job/queue + CLI worker | HTTP timeout + deadlock riski |
| Yüksek frekanslı küçük çağrılar | PHP extension / FFI | süreç başaştırma overhead'i |

- `proc_open` pipe'larında **büyük veri klasik deadlock** yaratır (stdin dolu → stdout okunmuyor): alternatif okuma/yazma veya `stream_select` gerekir (PHP.net notu).
- Çıkış kodu her zaman `proc_close`/`proc_get_status` ile kontrol edilmeli; başarısızlık sessiz yutulmamalı.

**Kaynaklar**
- https://www.php.net/function.proc_open
- https://github.com/php/php-src/blob/master/ext/standard/proc_open.c
- https://www.php.net/manual/en/function.exec.php

---

## 16. PHP shell_exec/exec/system — OWASP riskleri (komut enjeksiyonu) ve risksiz alternatifler

- **OWASP savunma seçenekleri (OS Command Injection Defense Cheat Sheet)** — üç katman:
  1. Mümkünse **hazır kütüphane fonksiyonu** kullan (komut string'i hiç oluşmasın; CISA örneği: `os.mkdir()` yerine shell çağırmamak).
  2. **Parametrik çağrı**: komutu ve argümanları ayıran dizi tabanlı API (`proc_open` dizi formu ↔ dilde `subprocess.run([...])` analojisi) — "shell'i tamamen bypass et".
  3. kaçınılmazsa **parametrizasyon + allowlist input validation**: komut **izin listesinden** doğrulanır (asla kullanıcı seçmez), argümanlar allowlist regex ile (`^[a-z0-9]{3,10}$` gibi; `& | ; $ > < \` \ !` ve boşluk regex'e girmez), **seçenekler kodda sabitlenir**, POSIX'e uygun **`--` ayıracı** kullanılır (`curl -- $url`, `tar cf my.tar -- ...`).
- **escapeshellarg() tek başına yeterli değildir** — PHP.net'teki klasik açık: `tar` örneğinde her argüman doğru kaçırılsa bile argüman `-I bash -c ...` gibi **seçenek olarak** yorulur → kaçış doğru, enjeksiyon yine mümkün. Çözüm: `--` sonrası konumlandırma argümanı veya `-` ile başlayan reddi.
- `escapeshellarg()` Windows'ta farklı davranır: `%`, `!`, `"` karakterlerini **boşluğa çevirip** çift tırnak ekler (kayıpsız değil); çok bayt karakterler locale'e göre strip edilir. Windows'ta gerçek kaçış: her argüman `escape_win32_argv` + tüm satır `^` ile `escape_win32_cmd`; **`proc_open(..., ['bypass_shell' => true])`** iki mekanizmayı da atlar.
- **OWASP ASVS tartışması (2025)**: platformdan bağımsız evrensel bir shell kaçış standardı **yoktur**; tercih sırası: (1) shell'i tamamen bypass eden dil-yerel API'ler, (2) kaçınılmazsa OS/shell'e özel kaçış + anlamsal doğrulama, (3) kullanıcı kontrollü string asla birleştirme, (4) ayrıcalıksız kullanıcı + seccomp/sandbox.
- `escapeshellcmd()` yalnızca komutun kendisi için (tümü için); **argümanlar için değil**. `system()` çıktıyı basar (echo eder) — web çıktısına sızma riski; `passthru()` binary çıktı için; hepsi shell üzerinden geçer.
- **Riskli kalıp → güvenli kalıp**:
  - ❌ `exec("convert " . $_GET['f'] . " out.png")`
  - ⚠️ `exec("convert " . escapeshellarg($f) . " out.png")` (komut injection kapalı ama `f` hâlâ option olabilir / allowlist yok)
  - ✅ `proc_open(['convert', $validatedFile, 'out.png'], $desc, $pipes)` + dosya adı `^[a-zA-Z0-9_-]{1,64}\.(mp3|png)$` allowlist + `--` + timeout + `proc_close` exit code kontrolü.
- OWASP kategorisi: **A03 Injection** (Top 10:2021). Ek katman: `disable_functions` ile production'da `shell_exec,exec,system,passthru` kapatılıp yalnızca belirli süreç köprüsüne izin vermek **[doğrulanamadı]** (OWASP kaynaklarında doğrudan önerilmiyor; ops практиği olarak biliniyor).
- CISA "Secure by Design" uyarısı: üreticiler PR kurallarıyla riskli çağrıları (dizisiz `subprocess.run`, `shell=True`) yasaklamalı — PHP karşılığı: kod incelemesinde string birleştirilmiş `exec/shell_exec` çağrısını reddeden kural.

### Riskli → güvenli kod karşılaştırması

```php
<?php
// ❌ 1) DOĞRUDAN ENJEKSİYON — kullanıcı komutu belirler
exec("ffmpeg -i " . $_GET['file'] . " out.wav");

// ❌ 2) Kaçış var ama hâlâ kırılabilir — allowlist yok, option injection:
//      file="-f lavfi -i anullsrc ..."
exec("ffmpeg -i " . escapeshellarg($_GET['file']) . " out.wav");

// ✅ 3) DİZİ komut (shell yok) + argüman allowlist + sabit binary + format sabit
$cmd = [
    '/usr/bin/ffmpeg',            // sabit yol — kullanıcı seçmez
    '-nostdin', '-hide_banner',   // seçenekler kodda sabit
    '-i', $validatedPath,         // allowlist regex'ten geçmiş
    '-f', 'wav',                  // format sabit
    '/var/media/out.wav',         // hedef dizin sabit
];
proc_open($cmd, $desc, $pipes);   // Windows'ta otomatik bypass_shell
```

### Katmanlı savunma (uygulama sırası)

```text
[1] Komut gerekli mi?  → hazır PHP fonksiyonu (finfo, getimagesize) var → shell'e hiç çıkma
[2] Sabitle            → binary yolu + tüm opsiyonlar kodda; kullanıcı yalnız veri seçer
[3] Allowlist doğrula  → regex: izinli karakter seti + max uzunluk (^[a-z0-9]{3,10}$)
[4] Kaçır              → her argüman AYRI escapeshellarg() (Windows: argv kaçışı)
[5] Ayırıcı ekle       → konumlandırıcıdan önce `--` (tar cf a.tar -- <files>)
[6] Çalıştır           → proc_open(dizi) + timeout + çıkış kodu kontrolü
[7] Ayrıcalık düşür    → child süreç ayrıcalıksız kullanıcı, capability'ler düşürülmüş
```

### Karar tablosu

| Durum | Kullanılacak | Not |
|---|---|---|
| Komut tamamen sabit, veri yok | `proc_open` dizi / `exec` sabit string | en güvenli |
| Kullanıcıdan argüman geliyor | dizi + allowlist + `escapeshellarg` | tek başına kaçış yetmez |
| Windows + cmd.exe istenmiyor | `proc_open(..., ['bypass_shell' => true])` | `%`/`!` sorunu da kalkar |
| Kullanıcı komut/ executable seçiyor | **REDDET** | allowlist zorunlu |
| Tarayıcı/sunucu sistemi shell meta içeriyor | `--` + allowlist regex | `-` option injection |
| Uzun süren iş | CLI worker + kuyruk | HTTP timeout |

### OWASP / ASVS bağlamı

- Kategori: **A03 Injection** (OWASP Top 10:2021); spesifik tehlike: **OS Command Injection** (CWE-78).
- ASVS v1.2.5 tartışması sonucu yönelim: "kaçış fonksiyonu" değil, **dil-yerel, argüman vektörlü API kullanımı** birinci tercih; kaçış kaçınılmazsa OS/shell'e özel + anlamsal doğrulama sonrası.
- Günlük kod incelemesi kuralı: `exec|shell_exec|system|passthru|popen` çağrılarında **string birleştirme (`.`) tespiti = red**; dizi formu + validator katmanı zorunlu.
- `.env` / secret'lar komut satırına **argv olarak** yazılmamalı (görünür süreç listesi) — pipe (`proc_open` descriptor 0) veya ortam değişkeni ile taşıyın **[doğrulanamadı]** (PHP.net, geç parola aktarımı için ek dosya tanımı öneriyor — "passphrases via extra file descriptors").

**Kaynaklar**
- https://cheatsheetseries.owasp.org/cheatsheets/OS_Command_Injection_Defense_Cheat_Sheet.html
- https://cheatsheetseries.owasp.org/cheatsheets/Injection_Prevention_Cheat_Sheet
- https://www.php.net/manual/en/function.exec.php
- https://www.php.net/manual/en/function.escapeshellarg.php
- https://www.cisa.gov/resources-tools/resources/secure-design-alert-eliminating-os-command-injection-vulnerabilities
- https://github.com/OWASP/ASVS/issues/3211

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
