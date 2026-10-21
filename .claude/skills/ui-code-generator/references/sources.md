# Truth Mode Kaynakları — ui-code-generator

> Bu dosya, skill'in teknik iddialarının dayandığı **resmi web kaynaklarını** listeler.
> Truth Mode protokolü (skill-maker §N5 / kök §5 Research Mandate): mimari ve standart
> iddiaları model hafızasıyla değil, **erişilmiş resmi kaynak** ile desteklenir.
>
> Her kaynak için: URL · erişim tarihi · doğrulanan iddia · varsa kısıt notu.
> Doğrulanamayan hiçbir iddia bu dosyada "doğrulanmış" sayılmaz.

---

## 1. WCAG 2.2 — W3C Recommendation

| Alan | Değer |
|---|---|
| Başlık | Web Content Accessibility Guidelines (WCAG) 2.2 |
| URL | https://www.w3.org/TR/WCAG22/ |
| Erişim tarihi | 2026-10-21 |
| Yayın durumu | W3C Recommendation — 12 December 2024 |

### Doğrulanan iddialar (bu skill'de kullanılan Success Criterion değerleri)

| SC | Başlık | Seviye | Doğrulanan değer | Skill'de kullanım |
|---|---|---|---|---|
| 1.4.3 | Contrast (Minimum) | AA | Normal metin **4.5:1**, büyük metin **3:1** | §WCAG checklist, kontrast token kontrolü |
| 1.4.10 | Reflow | AA | **320 CSS px** genişlik (400% zoom) içerik tek sütunda akar, 2-boyutlu scroll yok | Responsive tier + 1024×600 fallback testi |
| 1.4.11 | Non-text Contrast | AA | UI bileşenleri ve grafiksel nesneler **3:1** | Border/ikon/focus-ring kontrastı |
| 1.4.12 | Text Spacing | AA | line-height 1.5×, paragraph 2×, letter-spacing 0.12×, word-spacing 0.16× — kayıpsız | Bileşen yükseklik hesapları |
| 2.4.7 | Focus Visible | AA | Klavye odağı görünür olmalı (genişlik değeri SC'de tanımlı değil) | Focus outline zorunlu |
| 2.4.11 | Focus Not Obscured (Minimum) | AA | Odaklanmış bileşen sticky/overlay ile tamamen örtülmemeli (2.2'de YENİ) | Sticky header/footer altında kalan odak kontrolü |
| 2.5.7 | Dragging Movements | AA | Tek işaretçi sürükleme işleminin tek işaretçisiz alternatifi olmalı (2.2'de YENİ) | Karşılaştırma/sıralama bileşenlerinde alternatif |
| 2.5.8 | Target Size (Minimum) | AA | Tıklanabilir hedef **en az 24×24 CSS px** (2.2'de YENİ) | Minimum dokunma hedefi |
| 3.2.6 | Consistent Help | A | Yardım erişimi sayfalar arası tutarlı konumda | Header/footer yerleşim tutarlılığı |
| 3.3.7 | Redundant Entry | A | Aynı bilgiyi tekrar girmeyi gerektiren formlarda önceki giriş erişilebilir olmalı | Form akışları |
| 3.3.8 | Accessible Authentication (Minimum) | AA | Kimlik doğrulama testi zihinsel işlev gerektirmemeli (cognitive function test yasağı) | Giriş/şifre akışları |

### Önemli düzeltme notları (skill içi eski iddiaların akıbeti)

1. **4.1.1 Parsing — WCAG 2.2'de KALDIRILDI.** Eski sürümlerdeki "4.1.1 name/role/value"
   çerçevesi AA setinden çıkarıldı. Bu skill'de 4.1.1'e dayanan herhangi bir PASS/BLOCK
   kapısı artık geçersizdir; yerine 1.3.1 (Info and Relationships) ve 4.1.2 (Name, Role, Value)
   kullanılır. (Kaynak: WCAG 2.2 girişinde 2.2 sürüm değişiklikleri listesi.)
2. **2.4.11 Focus Not Obscured (Minimum) — 2.2'de YENİ AA maddesidir.** Eski WCAG 2.1
   checklist'lerinde yoktu; sticky header + focus kombinasyonunda test edilmelidir.
3. **2.5.8 Target Size (Minimum) — 2.2'de YENİ AA maddesidir.** Eski "≥24×24px" kuralı
   2.1'de gönüllü bir en iyi uygulamaydı; 2.2 ile AA zorunluğuna dönüştü.
   Üstelik SC'nin kendisinde "CSS px" ifadesi geçer.
4. **Focus outline genişliği (3px) WCAG'de tanımlı değildir.** WCAG 2.4.7 yalnızca
   "görünür" olmasını ister; 3px değeri CoreMusic projesinin kendi iç kuralıdır
   (bkz `references/coremusic-rules.md`). Bu skill'de 3px asla "WCAG gereği" diye
   sunulmaz.

---

## 2. CSS Grid Layout — MDN Web Docs

| Alan | Değer |
|---|---|
| Başlık | CSS grid layout (MDN) |
| URL | https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_grid_layout |
| Erişim tarihi | 2026-10-21 |
| Kaynak türü | MDN — geliştirici referansı (W3C spesifikasyon tabanlı) |

### Doğrulanan iddialar

- CSS Grid Layout Module Level 2, **iki boyutlu** (satır + sütun) sayfa düzeni sağlar.
- Temel birimler: `grid-template-columns` / `grid-template-rows`, `repeat()`, `minmax()`, `fr` birimi.
- Grid, sayfa geneli makro yerleşim içindir; bileşen içi tek-eksen dağılım için flexbox
  (CSS Flexible Box Layout) tercih edilir — bu ikili kullanımı MDN grid sayfası hem grid hem
  flexbox'a referans vererek anlatır.
- Grid container / grid item / grid line / grid track / grid cell / grid area kavramları
  MDN'de tanımlanmıştır.
- `grid-auto-flow` ile dolgu sırası yönetilir; `grid-area` / `grid-row` / `grid-column`
  ile yerleşim ataması yapılır.

### Kullanım sınırı

Bu skill grid'i yalnızca **sayfa ve bileşen yerleşimi** için referans alır. Grid içi animasyon
veya grid-specific JS davranışı bu skill'in kapsamı dışındadır.

---

## 3. Design Tokens Format Module — W3C Design Tokens Community Group

| Alan | Değer |
|---|---|
| Başlık | Design Tokens Format Module |
| URL | https://design-tokens.github.io/community-group/format/ |
| Erişim tarihi | 2026-10-21 |
| Yayın durumu | **W3C Community Group — "Preview Draft"** (2025-10-28) |

### Doğrulanan iddialar

- Token alanları `$` işaretli anahtarlarla ayrılır: `$value`, `$type`, `$description`
  (dolar işareti, CSS özellik adlarıyla çakışmayı önler).
- Token değerlerine **alias** ile referans verilir: `{group.token}` sözdizimi
  (nokta ile grup/token ayrılır; `{a-b}` ayraçlı grup adları da desteklenir).
- Grup içi `$type` miras edilir; `"$type": "color"` bir gruba tanımlanınca alt token'lar
  kendi `$type` yazmasa da color olarak okunur.
- Token adında yasaklı karakterler: `.` (nokta, alias sözdizimiyle çakışır) ve `{`/`}`
  (alias işaretleri); token adı `$` ile başlayamaz.

### Kısıt notu — bu skill için kritik

DTCG modülünün kendi giriş metni, **"Preview Draft"** durumundadır ve modülün
"fully authoritative specification" olarak uygulanmaması gerektiğini,
uygulayıcıların "independent implementations" ile ilerlemesi gerektiğini açıkça belirtir.

Sonuç: bu skill, `--cm-*` token adlandırma ve yapılandırmayı **DTCG zorunluluğu gibi
sunmaz.** `--cm-*` öneki ve dosya isimlendirmesi CoreMusic projesinin kendi iç
sözleşmesidir (bkz `references/coremusic-rules.md` ve vault
`.ai/.templates/frontend/css-template.md`). DTCG yalnızca "token'ın $value/$type/
$description ile ayrıştırılması ve alias kavramı" düzeyinde genel referanstır.
⚠️ DTCG'nin sürüm/alan sınırları hakkında kesin iddia kurulacaksa **VERIFICATION
REQUIRED** — çünkü preview draft'tır ve değişebilir.

---

## 4. Doğrulanamayan / kapsam dışı iddialar (bilinçli boşluklar)

| İddia | Durum | Gerekçe |
|---|---|---|
| CoreMusic `src/css/` katman disk yapısı | **UNKNOWN (bu oturumda)** | `src/css/` dizini ve `main.css` bu çalışma ağacında mevcut değil (disk kanıtı: `Test-Path false`). Katman sırası **şablon** düzeyinde vault `.ai/.templates/frontend/css-template.md`'den alınır; gerçek dosya listesi her görevde diskten doğrulanır. |
| "ITCSS 7 katman" ifadesi | **GEÇERSİZ** | Vault şablonu v4.0.0 **11 katman** tanımlar (01_Abstracts→11_OAuth). Eski skill'deki 7 katmanlı listeye ait içerik yeni sürümde düzeltildi. |
| Framework kimlik doğrulama / OAuth 2.1 spesifik akışlar | **KAPSAM DIŞI** | 11_OAuth katmanı yalnızca token ve yardımcı CSS'i kapsar; OAuth protokol akışı backend/JS katmanıdır, bu skill'in dışındadır. |
| Belirli bir tarayıcı sürümünde Grid/Flexbug davranışları | **VERIFICATION REQUIRED** | Tarayıcı-bazlı bug listesi her yazım anında güncellenir; bu skill'de sabitlenmez. |

---

## 5. Truth Mode işlem özeti

| Adım | Sonuç |
|---|---|
| WCAG 2.2 resmi metinden SC değerleri çıkarıldı | 11 SC doğrulandı (§1) |
| MDN grid kaynağından grid/flexbox ayrımı doğrulandı | §2 |
| DTCG kaynağı "preview draft" olduğu için zorunlu kural sayılmadı | §3 kısıt notu |
| `src/css/` disk gerçekliği kontrol edildi | YOK — §4'te UNKNOWN olarak işaretlendi |
| Doğrulanamayan iddia "bilgi verildi" diye yazılmadı | §4 boşluk tablosu |

> Bu dosya her yeni Truth Mode turunda güncellenir. Erişim tarihleri silinmez, yalnız
> yenisi eklenir (eski erişim tarihi bir geçmiş kanıt olarak kalır).
