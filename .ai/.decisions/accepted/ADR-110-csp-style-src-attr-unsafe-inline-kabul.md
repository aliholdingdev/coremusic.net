---
title: "CoreMusic — ADR-110: CSP style-src-attr 'unsafe-inline' (Kabul Edilmiş Risk)"
type: adr
category: security
date: 2026-10-24
status: accepted
authority: ADR-110 Karar Metni (SSOT)
related: "ADR-012 (CSP nonce strict-dynamic — script-src politikası)"
---

# ADR-110: CSP `style-src-attr 'unsafe-inline'` — Kabul Edilmiş Risk

**Durum:** accepted
**Tarih:** 2026-10-24
**Karar Veren:** Kullanıcı onayı (seçenek 1: ADR ile kabul et)
**Bulgu kaynağı:** Güvenlik denetimi SEC-08 (Severity LOW)

## 1. Bağlam

ADR-012 `script-src` için nonce + `strict-dynamic` ve `unsafe-inline`/`unsafe-eval` yasağını kalıcı kılar. Ancak CSP'de `style-src-attr` için `'unsafe-inline'` açık:

- `shared/src/PageRouter/ResponseEmitter.php:14`
- `shared/src/Middleware/SecurityHeadersMiddleware.php:66`

Yani HTML `style="..."` attribute'ları çalışır. `script-src` sıkı olduğundan XSS ile JS çalıştırma yüzeyi kapalıdır; açık kalan yüzey CSS tabanlı veri sızdırma (CSS exfiltration) ve UI manipülasyonudur.

Inline style attribute'larının tamamen kaldırılması birden çok sayfada CSS/markup taşımasını gerektirir. Bu iş ayrı bir sprint'tir.

## 2. Karar

`style-src-attr 'unsafe-inline'` **şimdilik kabul edilmiş risk** olarak tutulur. Politika değiştirilmez.

Kapsam sınırları (bu ADR bunları değiştirmez):
- `script-src` nonce + `strict-dynamic` aynen kalır (ADR-012).
- `style-src-elem` ve `style-src` için `unsafe-inline` **eklenmez**. Sadece attribute kanalı açıktır.
- `unsafe-eval` kapalı kalır.

## 3. Gerekçe

- Etki sınırlı: script çalıştırma kapalı, `style-src-attr` yalnız stil kanalı.
- Inline style kaldırma işi çok sayfayı etkiler; güvenlik kazancı bu maliyeti tek başına haklı kılmaz.
- Açık, belgeli ve izlenebilir: bu ADR olmadan bu gevşeklik sessizce kalırdı.

## 4. Sonuçlar

- Kabul: CSS exfiltration riski bilinçli olarak kabul edilmiştir.
- Yan etki: Denetimlerde "style-src-attr açık" bulgusu tekrar açılmaz; bu ADR'ye atıf yapılır.

## 5. Gözden Geçirme Tetikleyicileri

Bu karar şu durumlarda yeniden değerlendirilir:
1. Bir CSS exfiltration / UI redressing olayı görülürse.
2. Inline style temizliği için bir sprint planlanırsa (o zaman hedef: `style-src-attr 'none'`).
3. Yeni bir sayfa/bileşen inline `style` attribute'ı eklemeyi gerektirirse (yeni inline stil eklenmemeli, bu ADR kapsamında değil).

## 6. Doğrulama

- Kod kanıtı: `ResponseEmitter.php:14` ve `SecurityHeadersMiddleware.php:66` (2026-10-24 okundu).
- `script-src` politikası değişmedi; ADR-012 testleri etkilenmez.
