---
type: architecture
category: l2
title: "URL Normalization"
date: 2026-08-08
updated: 2026-09-08
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# URL Normalization

**Zorunlu Bağlantılar:** [[index]] · [[ADR-016-url-normalization]] · [[ADR-009-clean-url-redirect]]

---

## 1. Amaç

URL formatı standardizasyonu ve clean URL redirect stratejisini tanımlar. [[ADR-016-url-normalization]] ve [[ADR-009-clean-url-redirect]] ile uyumludur.

Bu sürüm (v5.0.0) Faz 2c revizyonu ile (2026-09-08) Türkçe transliterasyon detayı, redirect matrisi, SEO/SPA uyumları ve test senaryolarıyla genişletildi.

---

## 2. Normalization Kuralları

| Kural | Örnek | Sonuç |
|-------|-------|-------|
| Trailing slash kaldır | `/songs/` | `/songs` |
| Double slash tekille | `/songs//1` | `/songs/1` |
| Lowercase | `/Songs/1` | `/songs/1` |
| UTF-8 normalize | `/şarkılar` | `/sarkilar` |
| Query string koru | `/songs?page=2` | `/songs?page=2` |
| Hash fragment koru | `/songs#top` | `/songs#top` |
| Kök `/` muaf | `/` | `/` (slash kaldırılmaz) |

---

## 3. Implementation

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class UrlNormalizer
{
    public function normalize(string $url): string
    {
        // Trailing slash kaldır
        $url = rtrim($url, '/');

        // Double slash tekille
        $url = preg_replace('#/+#', '/', $url);

        // Lowercase
        $url = strtolower($url);

        // UTF-8 normalize
        $url = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $url);

        return $url;
    }
}
```

### 3.1 Gerçek Kod Durumu (Faz 0 — 2026-09-08)

| Öğe | Durum |
|-----|-------|
| `UrlNormalizer` sınıfı | **DOĞRULAMA GEREKLİ** — `shared/src/PageRouter/` 16 dosyasında isim düzeyinde görülmedi |
| Normalize adımı | PageRouter dispatch hattında var (§17 adım 4) — hangi sınıf/metot, kod okumasında netleşecek |
| `transliterator_*` | PHP intl uzantısı gerektirir — ortamda var mı DOĞRULAMA GEREKLİ |

**Sonuç:** Kurallar ADR-016 ile FROZEN'dır; implementasyon sınıfı üretim kodunda kanıtlanınca §3.1 tablosu güncellenir.

---

## 4. Türkçe Transliterasyon Detayı

| Kaynak | Hedef Slug | Kural |
|--------|------------|-------|
| ş, Ş | s | Any-Latin → Latin-ASCII |
| ı, İ | i | dotted/dotless I dikkat |
| ğ, Ğ | g | — |
| ü, Ü | u | — |
| ö, Ö | o | — |
| ç, Ç | c | — |
| â, î, û | a, i, u | şapkalı harfler |

**Dotless I riski:** PHP `strtolower()` Türkçe locale'de `I` → `ı` üretebilir (locale bağımlılık). Çözüm: locale bağımsız işleme (mb_strtolower 'en' veya transliterator). Bu, slug standardının "ASCII küçük harf" olma nedenidir (route-config §25).

**intl uzantısı yoksa fallback:** sabit `str_replace` tablosu (yukarıdaki 14 karakter çifti) — tablo sabiti `UrlSlugMap` önerilir; her iki yol aynı çıktıyı üretmeli (test şartı).

---

## 5. Clean URL Redirect (ADR-009)

### 5.1 Redirect Kuralları

| Kaynak | Hedef | Tip |
|--------|-------|-----|
| `index.php?page=songs` | `/songs` | 301 |
| `index.php?page=songs&id=1` | `/songs/1` | 301 |
| `index.php?page=admin` | `/admin` | 301 |
| `http://` | `https://` | 301 |
| `/songs/` | `/songs` | 301 |
| `/Songs` | `/songs` | 301 |

### 5.2 Implementation

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class CleanUrlRedirect
{
    public function handle(): void
    {
        $uri = $_SERVER['REQUEST_URI'];
        $query = $_SERVER['QUERY_STRING'] ?? '';

        if (str_contains($uri, 'index.php')) {
            $cleanUrl = $this->convertToCleanUrl($uri, $query);
            header("Location: {$cleanUrl}", true, 301);
            exit;
        }
    }

    private function convertToCleanUrl(string $uri, string $query): string
    {
        parse_str($query, $params);
        $page = $params['page'] ?? 'home';
        unset($params['page']);

        $path = '/' . $page;

        if (isset($params['id'])) {
            $path .= '/' . $params['id'];
            unset($params['id']);
        }

        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        return $path;
    }
}
```

### 5.3 Gerçek Kod Durumu

| Öğe | Durum |
|-----|-------|
| `CleanUrlRedirect` sınıfı | DOĞRULAMA GEREKLİ — üretim kodunda kanıt yok |
| `index.php?page=` eski kalıbı | Gerçek domainler front controller + route config kullanır; eski kalıp yalnız referans projeden gelme geçiş hedefidir |
| `.htaccess`/webserver redirect | Deployment katmanı kararı ([[../02-deployment/index]]) |

---

## 6. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| `/songs/` trailing slash | `/songs` | ADR-016 |
| `//songs//1` double slash | `/songs/1` | ADR-016 |
| `/Songs/1` uppercase | `/songs/1` | ADR-016 |
| `index.php?page=` | `/songs` | ADR-009 |
| Karışık redirect zinciri | Tek atımlık 301 | ADR-009 |
| Query'de case dönüşümü | Query birebir korunur | ADR-016 |
| Slug'da Türkçe karakter | `sarkilar` | ADR-016 + §4 |

---

## 7. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **URL Encoding** | `urlencode()`/`urldecode()` | ADR-016 |
| **Query String** | Korunur (case/sıra korunur) | ADR-016 |
| **Hash Fragment** | Korunur (sunucuya gitmez — JS tarafı) | ADR-016 |
| **UTF-8** | transliterator veya fallback tablo | ADR-016 |
| **SEO** | 301 redirect | ADR-009 |
| **API endpoint'ler** | Normalize edilmez — sözleşme URL'leri sabittir | ADR-084 |
| **`/api/` öneki** | Route config'te literal; normalize dışı tutulmalı | Faz 2c devam |

---

## 8. SEO Uyumları

| Konu | Kural |
|------|-------|
| Canonical | Her sayfa tek normalize URL'i canonical bildirir |
| Duplicate content | Çoklu varyasyon (case/slash) 301 ile tekileşir |
| Sitemap | Yalnız normalize edilmiş URL'ler listelenir |
| Redirect zinciri | ≤1 atım — 301'den 301'e zincir yasak |
| Sayfa başlığı | SpaRoute.title canonical URL ile eşleşir (route-config §2) |

---

## 9. SPA Uyumlulukları

| Konu | Kural |
|------|-------|
| pushState öncesi | JS Router URL'i aynı normalize kurallarıyla hazırlar — sunucu-istemci tutarlılığı |
| popstate | Geri dönüşte normalize edilmiş URL beklenir; eski varyasyon 301'i sunucuda karşılanır |
| Hash fragment | SPA içinde görünüm durumu; sunucu gönderimine girmez |
| DOM patch sonrası adres çubuğu | normalize edilmiş path yazılır (History API) |
| Deep-link | İlk istek PHP'ye gelir → normalize → route resolve (ADR-083 hibrit) |

---

## 10. Redirect Matrisi — Port ve Host

| Kaynak | Hedef | Not |
|--------|-------|-----|
| `:80` http | `:443` https | production standardı |
| `www.coremusic.net` | `coremusic.net` (veya tersi) | **KARAR BEKLİYOR** — tek canonical host seçilmeli |
| `home.coremusic.net:81` (dev) | — | Dev ortamda port korunur (guard-pipeline §13 callback suffix) |

**Açık karar:** www canonical hedefi seçilmemiş — deployment kararı ([[../02-deployment/index]]) + SEO kararı birlikte verilmeli. Karar gelene dek bu satır `DOĞRULAMA GEREKLİ` kalır.

---

## 11. Test Senaryoları

| # | Girdi | Beklenen |
|---|-------|----------|
| 1 | `/songs/` | 301 → `/songs` |
| 2 | `/songs//1` | 301 → `/songs/1` |
| 3 | `/Songs` | 301 → `/songs` |
| 4 | `/şarkılar` | 301 → `/sarkilar` |
| 5 | `/songs?page=2&sort=asc` | 200, query birebir |
| 6 | `/songs#top` | fragment korunur (JS) |
| 7 | `/` | 200 (muaf) |
| 8 | `index.php?page=songs&id=1` | 301 → `/songs/1` |
| 9 | `/api/v1/Users` | normalize DIŞI — 200 (sözleşme) |
| 10 | `/ALBUM/42` | 301 → `/album/42` |
| 11 | Çift redirect denemesi | ≤1 zincir |
| 12 | intl yok ortamda `/şarkılar` | fallback tablo → `/sarkilar` |

---

## 12. Diagnostics (tekrarlanabilir)

```powershell
# 1. Normalize adımının kod konumu (dispatch hattında)
Select-String -LiteralPath "shared\src\PageRouter\PageRouter.php" -Pattern "normali|strtolower|transliterate"

# 2. UrlNormalizer sınıfı var mı (beklenen: yoksa §3.1 DOĞRULAMA kalır)
Get-ChildItem -LiteralPath "shared\src" -Recurse -Filter "UrlNormalizer.php" -ErrorAction SilentlyContinue

# 3. intl uzantısı kontrolü (transliterator için)
php -m | Select-String "intl"

# 4. ADR metin bütünlüğü
Select-String -LiteralPath ".ai\decisions\accepted\ADR-016-url-normalization.md" -Pattern "lowercase|slash" -ErrorAction SilentlyContinue
```

---

## 13. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | intl uzantısı eksik → transliterator fatal | Orta | Yüksek | §4 fallback tablo + test |
| 2 | Locale'e bağımlı strtolower | Orta | Yüksek (I→ı) | Locale bağımsız işleme kuralı |
| 3 | API endpoint'lerin normalize edilmesi | Orta | Kritik (sözleşme kırılır) | §7 `/api/` istisnası |
| 4 | www kararı verilmemesi | Kesin | Düşük (şimdilik) | §10 açık karar satırı |
| 5 | Çift redirect zinciri | Düşük | Orta | §8 zincir ≤1 |
| 6 | Query'de case dönüşümü | Düşük | Orta (token/param bozulması) | §6 yasak satırı |

---

## 14. SSS

**S: `UrlNormalizer` gerçekten kodda var mı?**
C: Kanıt yok (§3.1) — kurallar FROZEN ama sınıf kanıtlanmadı. Dispatch hattındaki normalize adımının hangi kodla yapıldığı Faz 2c devam görevidir.

**S: Query string'deki `Sort=asc` normalize edilir mi?**
C: Hayır — yalnız path normalize edilir. Query param adları/değerleri sözleşmedir; dokunulmaz.

**S: Türkçe sayfa başlıkları slug'a nasıl iner?**
C: §4 tablosu: transliterator (intl varsa) veya sabit str_replace fallback. İki yol aynı çıktıyı vermeli — test şartı.

**S: Neden 301, 302 değil?**
C: Kalıcı adres değişimi SEO kanonikleştirmesi ister (ADR-009). Geçici durumlar için 302 ayrı karardır.

**S: Hash fragment sunucuya ulaşır mı?**
C: Ulaşmaz — tarayıcı kırpar. SPA içinde yaşam döngüsü JS'e aittir (§9).

**S: `/api/` neden muaf?**
C: API sözleşme URL'leri (ADR-084) büyük/küçük harf duyarlı istemci implementasyonlarıyla imzalanmıştır; normalize etmek breaking change olur.

**S: `ŞARKILAR` üst geçerse?**
C: Lowercase kuralı → `/şarkılar` → transliterasyon → `/sarkilar` — tek atımda birleşik işlenir (ayrı zincir değil).

---

## 15. İzlenebilirlik Tablosu

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| 5 normalize kuralı | ADR-016 | FROZEN metin |
| Redirect tipleri | ADR-009 | FROZEN metin |
| örnek sınıflar (UrlNormalizer/CleanUrlRedirect) | Bu dosya §3/§5.2 | Hedef desen — kod kanıtı yok (§3.1) |
| dispatch adım 4 normalize | l2 index §17 | Faz 2c hattı |
| route-config slug standardı | [[route-config]] §25 | Çapraz okuma |
| `İ`→`i` locale riski | PHP locale davranışı | Genel bilgi — ortam testi önerilir |

---

## 16. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 4.0.0 | 2026-08-08 | İlk doküman |
| 5.0.0 | 2026-09-08 | Faz 2c: §3.1 gerçek-kod durumu; §4 Türkçe transliterasyon + locale riski; §8 SEO; §9 SPA uyum; §10 www açık kararı; §11-13 test/diagnostics/risk; §14 SSS |

---

## 17. Normalize Hattı — İşlem Sırası

Normalize kurallarının uygulama sırası davranışı değiştirir; kanonik sıra:

```
1. Ham URI al (REQUEST_URI, query hariç)
2. URL decode (tek kez — çift decode yasak, %25 şaibesi)
3. Çift slash tekilleştir        → preg_replace('#/+#','/')
4. Trailing slash kaldır          → rtrim('/')  (kök "/" muaf)
5. Transliterasyon (Türkçe vb.)   → §4 tablo
6. Lowercase                       → locale bağımsız
7. Query yeniden ekle (dokunmadan)
8. Karşılaştır: ham ≠ normalize → 301; eşit → devam
```

**Sıra gerekçeleri:**
- Slash işlemleri decode ÖNCESİ değil SONRASI: `%2F` (encoded slash) path ayırıcı sayılmaz — decode sonra slash'a dönüşen veri segment içi kalmalıdır.
- Lowercase transliterasyon SONRASI: önce lowercase yapılırsa Türkçe `İ` locale'a göre `i`/`ı`'ya iner — önce ASCII'ye indirip sonra lowercase güvenlidir.
- Query en son eklenir: query içindeki değere asla path kuralları uygulanmaz.

---

## 18. Case Duyarlılık İstisnaları

| Bölge | Normalize edilir mi? | Neden |
|-------|---------------------|-------|
| Path segmentleri | ✅ Evet | Route key standardı ASCII küçük harf |
| Query param adları | ❌ Hayır | Sözleşme (örn. `Sort` vs `sort` API farkı) |
| Query değerleri | ❌ Hayır | Token/id birebir |
| Hash fragment | ❌ Hayır | Sunucuya ulaşmaz |
| `/api/` altı path | ❌ Hayır | ADR-084 sözleşme URL'leri |
| Dosya uzantılı istekler | ⚠️ Karar bekliyor | `logo.PNG` gibi asset'ler — assets domain sorunu |

---

## 19. RouteRegistry Entegrasyonu

Normalize edilmiş URI, Registry `resolve()`'a bu halden girer (route-config §3):

```
normalize('/Sarkilar/42/')
  → '/sarkilar/42'
     → Registry: tam eşleşme yok → 'sarkilar/{id}' pattern → SpaRoute
```

**Tasarım kuralı:** Registry'de key'ler zaten normalize formda tutulur (route-config §16 kural 3); normalize edilmemiş key Registry'ye yazılamaz. Böylece resolve öncesi ekstra karşılaştırma gerekmez.

**301 çıktısı:** Normalize edilmiş hâl kullanıcının gördüğü adrestir; Registry yalnız normalize formu görür — iki formun da aynı route'a inmesi test edilir (§11 senaryo 10).

---

## 20. 301 Cache Davranışı

| Tarayıcı/Proxy | Davranış | Not |
|----------------|----------|-----|
| Tarayıcı | 301 kalıcıdır — sonraki istekler direkt hedefe gider | Test için "cache devre dışı" gerekebilir |
| CDN | 301 cache'lenir | Yanlış 301 verilirse düzeltmesi zor — dikkat |
| PageCache (L0) | 301 yanıtını cache'lememelidir | Redirect'ler cache dışı |

**Tehlike:** Yanlışlıkla verilen 301 tarayıcıda yapışkalıdır. Bu nedenle normalize kural değişikliği önce ADR kaydı + deployment sırasında dikkat ister (ADR-016 frozen olduğundan kural değişimi zaten yeni ADR ister).

---

## 21. Ek Test Senaryoları

| # | Girdi | Beklenen |
|---|-------|----------|
| 13 | `/%73ongs` (encoded s) | decode → `/songs` → 200 (tek decode) |
| 14 | `/%2520` çift encode | tek decode → `/%20...` (çift decode yasak) |
| 15 | `//` (yalnız çift slash) | 301 → `/` |
| 16 | `/İSTANBUL` | → `/istanbul` (dotless riski yok — en locale) |
| 17 | `/api/Users` | 200 — normalize DIŞI |
| 18 | `/songs/?page=2` | 301 → `/songs?page=2` |
| 19 | `/SONGS#top` | 301 → `/songs` (fragment tarayıcıda) |
| 20 | `/şŞğĞ` | → `/sg` (tablo birleşimi) |

---

## 22. Ek SSS

**S: Neden çift decode yasak?**
C: `%2520` → decode → `%20` → decode → boşluk. İkinci decode, path traversal girişimlerini (`%252e%252e` → `..`) gizleyebilir; tek decode + kalan `%`'leri veri saymak güvenli yaklaşımdır.

**S: transliterator çıktısı deterministik mi?**
C: intl sürümüne göre kenar durumlarda değişebilir. Determinizm şartı: slug üretimi tek seferde yapıp saklanır (DB'de); çalışma anında tekrar üretilmez — bu, route key stabilitesini garantiler.

**S: Uppercase path'e neden izin yok?**
C: Dosya sistemi ve route key standardı ASCII küçük harf; case-insensitive Windows / case-sensitive Linux dağıtım farkını kökten kaldırır ( taşınabilirlik).

**S: Sitemap hangi URL'leri listeler?**
C: Yalnız normalize + 200 dönen adresler; 301 veren varyasyonlar ve 404'ler listelenmez (§8).

**S: Normalize adımı hangi sınıfta — hâlâ belirsiz mi?**
C: Evet (§3.1). Dispatch hattında adım var, sınıf kanıtı yok. İki olasılık: PageRouter içinde inline veya ayrı helper — §12 komut 1 sonraki turda netleştirir.

## 26. Slug Üretim Hattı (İçerik → URL)

Slug'ın nerede üretildiği, normalize hattıyla ilişkisi:

```
İçerik girişi (başlık: "Yeni Şarkılar 2026")
  → Slug üretimi (YAYIN ANINDA, TEK KEZ)
     → transliterasyon (§4) + tire + lowercase
        → "yeni-sarkilar-2026"
           → DB'de saklanır (coremusic_musics.slug)
              → routes.php / pattern route ile çözümleme
```

**Kritik kural:** Slug **yayın anında bir kez** üretilir ve saklanır — her istekte yeniden üretilmez.intl sürümü değişse, tablo değişse, eski slug değişmez (stabilite). Normalize hattı (§17) yalnız **gelen istekleri** kanonik forma indirir; slug üretimi ayrı bir ön işlemdir.

| Aşama | Nerede | Tekrar |
|-------|--------|--------|
| Slug üretimi | İçerik yayın akışı (admin/indirme hattı) | Bir kez |
| İstek normalize | PageRouter dispatch adım 4 | Her istek |
| Registry key | Route config yazımı | Config değişince |

Bu üçlü ayrım, §22'deki "transliterator determinizmi" sorusunun pratik cevabıdır — determinizmi beklemek yerine üretip saklamak.

---

## 27. Redirect Loglama

301/redirect üretimi izlenebilirlik için kaydedilir:

| Kayıt Alanı | Örnek | Neden |
|-------------|-------|-------|
| ham istek | `/Songs/1/?ref=x` | desen analizi |
| normalize çıktı | `/songs/1` | doğrulama |
| kural tetikleyici | `lowercase+trailing` | hangi kural çalıştı |
| istemci tipi | SPA/web | ADR-084 ayrımı |
| timestamp | ISO 8601 | audit |

**Hedef:** PSR-3 log (`info` seviye), prod'da örnekleme (her redirect değil — hacim riski). Dev ortamda tam log. Log disiplini [[../07-security/deep-logging-system]] dokümanına (874) bağlanır; secret'lı query `[REDACTED]` kuralı geçerlidir.

**Analiz değeri:** redirect log'ları (a) kırık dış link tespiti, (b) eski URL kalıbı kullanan istemci sürümlerini, (c) normalize kural hatası tekrarlarını gösterir — ADR-016 uyum denetiminin veri kaynağıdır.

---

## 28. Entegrasyon Örneği — Uçtan Uca İstek

```
İstek: GET /Sarkilar/42/?ref=newsletter   (SPA değil)
  dispatch adım 4 (normalize):
    decode → tek slash → rtrim → transliterasyon → lowercase
    → "/sarkilar/42"  (query korunur: ?ref=newsletter)
  ham ≠ normalize → 301 Location: /sarkilar/42?ref=newsletter
    → log: kural "translit+case+trailing"

İkinci istek: GET /sarkilar/42?ref=newsletter
  normalize → aynı form → 301 YOK → dispatch devam
    → RouteRegistry: tam eşleşme yok → 'album/{id}' değil,
      'artist/{id}' değil → pattern taraması...
    (Bu örnek için route tanımına bağlı — sarkilar/{id} tanımlıysa 200)
```

Bu örnek §17 sırasını, §19 Registry geçişini ve §27 log alanlarını tek hatta birleştirir — test senaryosu 4+18'in uçtan uca hali.

---

## 29. İlgili ADR Tablosu

| ADR | Konu | Bu Dosyadaki Etkisi |
|-----|------|---------------------|
| ADR-009 | Clean URL Redirect | §5 redirect kuralları, 301 tipi |
| ADR-016 | URL Normalization | §2 kurallar (FROZEN) |
| ADR-021 | SPA Router Immutable Contract | Registry key sözleşmesi (§19) |
| ADR-084 | API Gateway | `/api/` istisnası (§6/§18) |
| ADR-083 | SPA Router | Deep-link normalize (§9) |

Kural: Bu dokümandaki kural setinin değişmesi yalnız ADR-016/009'u revize eden YENİ ADR ile olur — Frozen istisna süreci haricinde.

---

## 30. Ek Test Senaryoları

| # | Girdi | Beklenen |
|---|-------|----------|
| 21 | `/sarkilar/42` (zaten normalize) | 301 YOK — direkt 200/404 route'a göre |
| 22 | `/x//y///z` | 301 → `/x/y/z` (tek atım) |
| 23 | `/ŞARKILAR?Ref=A` | 301 → `/sarkilar?Ref=A` (query case korunur) |
| 24 | intl'siz ortam + `/güncel` | fallback tablo → `/guncel` |
| 25 | redirect hedefi tekrar normalize gerektiriyor | ikinci 301 YASAK — üretim anında birleşik işlenir |

---

## 31. Ek SSS

**S: Normalize kuralı ile slug üretimi neden iki ayrı işlem?**
C: İlk kez üretilen slug kalıcıdır (DB); gelen her istek normalize edilir. İkisini birleştirirseniz, transliterasyon değişikliği mevcut URL'leri bozar — istikrar kaybedilir (§26).

**S: 301 ile content cache'i çakışır mı?**
C: Redirect'ler cache dışıdır (§20); 301 sonrası 200 yanıtı PageCache'e girer. Ayrı katmanlar.

**S: `ref=newsletter` gibi query'ler SEO'yu bozar mı?**
C: Canonical (§8) temiz URL'i bildirir; tracking parametreleri canonical dışıdır. Google varyasyonları aynı sayfa sayar — sorun değil.

**S: Encode edilmiş slash (`%2F`) gerçekten path ayırıcı olmamalı mı?**
C: HTTP standardında yok — segment içi veridir. Route pattern `[^/]+` zaten segment içi tutar; decode sırası (§17 adım 2) buna uygundur.

**S: Bu kurallar JS Router'da da mı?**
C: Evet — §9: pushState öncesi JS aynı normalize formu yazar; aksi halde sunucu 301'i SPA'yı döngüye sokar. js-router.md (546) çapraz kontrolü Faz 2c devamında.

**S: Türkçe karakterli bir dış link normalize edilir mi?**
C: Evet — dış referanslı URL'ler de normalize hattından geçer; transliterasyon tablosu (§4) kök nedenleri tekilleştirir. Eski dış linkler 301 ile kanonik forma yönlendirilir — backlink SEO değeri korunur.

**S: `rawurldecode` yerine `urldecode` neden tercih edilir?**
C: `urldecode` `+`'ı boşluğa çevirir — path'te `+` meşru karakterdir. rawurldecode yalnız `%XX` çözer; path için doğru semantik budur (§17 adım 2 tasarımı).

**S: Normalize çıktısı tekrar normalize edilirse aynı mı?**
C: Evet — idempotent tasarım şarttır: normalize(normalize(x)) = normalize(x). Test senaryolarının çoğu (§11/§21/§30) bu özelliği dolaylı doğrular; idempotency testi eklenmesi önerilir.

**S: Karakter seti karışımı (UTF-8 olmayan eski linkler) ne olur?**
C: transliterator UTF-8 bekler; ISO-8859-9 kalıntı linklerde mojibake üretir. Çözüm: entry noktasında charset tespiti (mb_check_encoding) + 301 — PLANNED edge.

---

## 23. Risk Kaydı Ek

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 7 | Çift decode güvenlik açığı | Düşük (kural var) | Kritik | §17 sıra + test 14 |
| 8 | Locale'e bağlı lowercase taşınma farkı | Orta | Yüksek | §4 detay |
| 9 | CDN'de yanlış 301 yapışması | Düşük | Yüksek | §20 dikkat notu |
| 10 | intl sürüm farkı slug değişimi | Düşük | Orta | §22 SSS — slug saklanır |
| 11 | `/api/` istisnasının unutulması | Orta | Kritik | §6 + §18 tablo |

---

## 24. İzlenebilirlik Tablosu Ek

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| Normalize sırası (8 adım) | Bu dosya §17 | Hedef tasarım — kod teyidi devam |
| Çift decode yasağı | §17 + §21-14 | Güvenlik kuralı |
| `/api/` istisnası | ADR-084 türevi | Çapraz karar |
| Registry normalize key | route-config §16 | Çapraz okuma |
| PageCache 301 dışı | §20 | Tasarım kuralı — kod PLANNED |
| www kararı bekliyor | §10 | Kullanıcı kararı |

---

## 25. Revizyon Geçmişi (Güncel)

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 5.0.0 | 2026-09-08 | Faz 2c tam revizyon (§1-16) |
| 5.1.0 | 2026-09-08 | §17 işlem sırası; §18 case istisnaları; §19 Registry entegrasyonu; §20 301 cache; §21-24 ek testler/SSS/risk |
| 5.2.0 | 2026-09-08 | §26 slug üretim hattı; §27 redirect loglama; §28 uçtan uca örnek; §29 ADR tablosu; §30-31 ekler; sözlük |

---

## 32. Sözlük

| Terim | Tanım |
|-------|-------|
| **Normalize** | URL'i kanonik forma indirgeme |
| **Trailing Slash** | Yol sonu `/` — kaldırılır |
| **Transliterasyon** | Unicode → ASCII dönüşümü (ş→s) |
| **301** | Kalıcı redirect — SEO kanonikleştirme |
| **Canonical** | Tek doğru adres bildirimi |
| **Fallback Tablo** | intl yoksa sabit karakter eşlemesi |
| **Slug** | İçerikten üretilmiş URL parçası |
| **Double Decode** | İki kez URL decode — yasak |
| **Path Segment** | `/` ile ayrılan yol parçası |
| **Query Preservation** | Query'nin birebir korunması |
| **Registry Key** | Route tanımının normalize anahtarı |
| **Deep Link** | SPA içi doğrudan adres |

---

## 33. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.2.0 |
| **Bölüm Sayısı** | 33 |
| **ADR Uyumlu** | ✅ 009, 016 (+021/083/084 çapraz) |
| **Test Senaryosu** | 25 (§11 + §21 + §30) |
| **Kod Kanıtı** | Hedef desen (§3.1 açık — DOĞRULAMA GEREKLİ kalemle) |
| **Risk Kaydı** | 11 kalem |
| **Zero Hallucination** | ✅ (www kararı, asset davranışı açık bekletildi) |
| **Üretim Planı** | §34 — UrlNormalizer iskeleti (strtr fallback, locale bağımsız lowercase) |
| **Slug Disiplin** | §26 — üret-se sakla, çalışma anında yeniden üretim yok |
| **Çapraz Doküman** | route-config §25 (slug), l0 §17 (cache), csp.md §21 (nonce) |
| **Açık Kullanıcı Kararları** | www canonical (§10), assets davranışı (§22 SSS) |
| **Normalize Sırası** | §17 8 adım — decode→slash→rtrim→translit→lowercase→query |
| **Güvenlik Kuralı** | Çift decode yasak — path traversal gizler (§21-14) |
| **Idempotency** | normalize(normalize(x)) = normalize(x) — test önerisi (§27 SSS) |
| **Karakter Semantiği** | rawurldecode path'te doğru — `+` korunur (§27 SSS) |
| **Normalize Sırası** | §17 8 adım — decode→slash→rtrim→translit→lowercase→query |
| **Güvenlik Kuralı** | Çift decode yasak — path traversal gizler (§21-14) |
| **Üretim Görevi** | UrlNormalizer sınıfı kod onayı → §3.1 IMPLEMENTED dönüşü |
| **API İstisnası** | `/api/` altı normalize dışı (ADR-084) — §18 tablo |

---

## 34. UrlNormalizer Üretim Planı (Hedef Sınıf İskeleti)

§3.1'de kanıtı olmayan sınıf için üretim taslağı (D1 tarzı — ADR-016 zaten frozen, kural hazır):

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

final class UrlNormalizer
{
    /** intl yoksa kullanılacak sabit harita — §4 tablosunun kod hali */
    private const SLUG_MAP = [
        'ş' => 's', 'Ş' => 's', 'ı' => 'i', 'İ' => 'i', 'ğ' => 'g', 'Ğ' => 'g',
        'ü' => 'u', 'Ü' => 'u', 'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c',
        'â' => 'a', 'î' => 'i', 'û' => 'u',
    ];

    public function normalize(string $path): string
    {
        $path = rawurldecode($path);                    // tek decode (§17-2)
        $path = (string) preg_replace('#/+#', '/', $path);
        $path = rtrim($path, '/');
        $path = strtr($path, self::SLUG_MAP);           // transliterasyon (intl'siz güvenli)
        $path = mb_strtolower($path, 'en');             // locale bağımsız (§4 risk)
        return $path === '' ? '/' : $path;              // kök muafiyeti (§2)
    }
}
```

**Tasarım notları:**
1. `strtr` fallback birincil yapılabilir — intl bağımlılığı tamamen kaldırılır (taşınabilirlik > marjinal transliterasyon kalitesi). ADR kaydı gerekir ( Davranış değişimi).
2. `mb_strtolower(..., 'en')` — Türkçe locale trap'ini kökten kapatır.
3. Query/fragment bu sınıfa girmez — yalnız path (§18 istisnalar).

Üretim onayı: Faz kontrol listesi + `log.md` — kod geldiğinde §3.1 tablosu IMPLEMENTED'e döner.

---

## 35. İlgili Dosyalar (Genişletilmiş)

| Dosya | İlişki |
|-------|--------|
| [[index]] | L2 ana dizin |
| [[spa-router]] | Dispatch hattı (adım 4 normalize) |
| [[route-config]] | Slug standardı (§25), Registry key |
| [[guard-pipeline]] | Redirect karar zinciri |
| [[../conditional-rendering-php-guide]] | Sayfa render sonrası URL |
| [[../l1-security/csp]] | Normalize → nonce'lu shell devri |
| [[ADR-016-url-normalization]] | Kurallar (FROZEN) |
| [[ADR-009-clean-url-redirect]] | Redirect tipleri (FROZEN) |

---
