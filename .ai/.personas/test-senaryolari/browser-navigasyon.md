---
title: "CoreMusic — Test Senaryosu: Tarayıcı Navigasyonu (BNV)"
type: test-scenario
category: testing
version: 1.0.0
status: active
authority: "Test Senaryosu — SSOT: personas/test-senaryolari/browser-navigasyon"
updated: 2026-09-26
---

# CoreMusic — Test Senaryosu: Tarayıcı Navigasyonu (BNV)

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/methodology]] · [[personas/research-bank]] · [[personas/test-scenarios-mapping]] · [[personas/persona-template]] · [[ADR-023-persona-driven-testing]] · [[personas/test-senaryolari/a11y-erisilebilirlik]]

---

| Alan | Değer |
|------|-------|
| Dosya Adı | `browser-navigasyon.md` |
| Dosya Yolu | `.ai/.personas/test-senaryolari/browser-navigasyon.md` |
| Dosya Tipi | Test senaryosu (6 senaryodan 1'i) — **karar DEĞİLDİR** (ADR değil) |
| Hedef Kitle | QA Engineer (birincil), DevOps Engineer (performans/CI), UI Designer (responsive) |
| Yapı | H1 + Zorunlu Bağlantılar + §1 Amaç → §7 Referanslar (7 bölüm + 7 alanlı frontmatter) |
| ADR-023 Karşılığı | 20 persona matrisi **satır 17 — Tarayıcı/Navigasyon kişisi** (§2.2a; kaynak: eski vault `test-senaryolari/browser-navigasyon.md`) |
| Test Seviyeleri | Seviye 1 AI Rol · Seviye 2 Browser MCP · Seviye 3 Playwright · Seviye 4 Rapor (`[[personas/methodology]]` §2.2) |
| Eşik Kaynağı | `[[personas/research-bank]]` **P8 (Test Metodolojisi — `VERIFIED` eşikler)** + **P5 (WCAG AA kriter numaraları)** |
| Metrik Kapsamı | LCP · INP · CLS · FCP · TBT (P8.1/P8.3 — 75. yüzdelik) |
| Adım Sayısı | **16** (asgari 10 — §5.3) |
| Test Blokları | BNV-001 … BNV-011 (11 blok — §3.1) |
| Zorunlu Blok | §4.0 Şablon-Önce Kural Bloğu (silinemez) |
| Eski Kaynak | `coremusic.net.old/.ai/personas/test-senaryolari/browser-navigasyon.md` (684 satır, **salt okunur**) — iskelet alındı, P8 eşikleriyle yeniden bağlandı |
| Kayıt Kuralı | Değişiklik Geçmişi **append-only** — mevcut satıra dokunulmaz |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya/klasör adı ASCII |
| Authority | `Test Senaryosu — SSOT: personas/test-senaryolari/browser-navigasyon` |
| Governance | Red Team · Human Mode · Truth Mode |
| Versiyon | 1.0.0 (ilk üretim — 2026-09-26) |

---

## §1 Amaç

Bu dosya, CoreMusic'in **tarayıcı navigasyonu test senaryosudur**: sayfa yükleme metrikleri (FCP/LCP/TBT), SPA navigasyonu, auth akışları, oturum yönetimi, hata sayfaları, tarayıcı uyumluluğu, responsive breakpoint taraması ve grup bazlı (çocuk/genç/yetişkin) navigasyon davranışlarının hangi adımlarla, hangi `VERIFIED` eşiklerle test edileceğini tanımlar. Eski vault'taki 684 satırlık senaryo **kopyalanmamış**; iskelet alınıp `[[personas/research-bank]]` P8 metrik eşikleriyle **yeniden bağlanmıştır** (ADR-005 Zero Hallucination).

| Boyut | Değer |
|-------|-------|
| Ne taşır | BNV test blokları, ≥10 adımlık adım tablosu (Adım · Eylem · Beklenen · Doğrulama · Metrik · WCAG), breakpoint/emülasyon matrisi, Playwright kod bloğu, hata modları, persona grup notları |
| Ne taşımaz | Metrik eşiklerinin kaynağı (→ `[[personas/research-bank]]` P8), test seviyesi/akışı (→ `[[personas/methodology]]`), senaryo × grup matrisi (→ `[[personas/test-scenarios-mapping]]`), karar (→ `[[ADR-023-persona-driven-testing]]`) |
| Neden yazıldı | Eski senaryo research-bank'tan **önce** yazılmıştı; "ADR 006 hedefleri" kaynaksızdı → P8.1/P8.3 eşikleriyle yeniden bağlandı |
| Kim okumalı | E2E navigasyon/perf testi çalıştıran her ajan (QA birincil, DevOps ikincil) |
| Kanıt zinciri | `research-bank` P8 (metrik eşik) → `methodology` (seviye/akış) → persona dosyası → **bu dosya (senaryo)** → `ADR-023` (gate) |

### §1.1 Bu Dosya Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Dosya | Bu Dosya Kullanılmaz |
|---------|-------------|----------------------|
| Navigasyon/perf test adımları ve breakpoint taraması | ✅ Bu dosya | — |
| LCP/INP/CLS/FCP/TBT eşik değerlerinin kaynağı | `[[personas/research-bank]]` P8 | ❌ (burada yalnız **taşınır**) |
| Test seviyesi, PREPARE→EXECUTE→REPORT akışı | `[[personas/methodology]]` | ❌ |
| Hangi persona grubu hangi senaryoda | `[[personas/test-scenarios-mapping]]` | ❌ |
| "Coverage %90 / PR gate" kararı | `[[ADR-023-persona-driven-testing]]` | ❌ (burada yalnız başvurulur) |
| WCAG kriter numarası/eşiği | `[[personas/research-bank]]` P5 | ❌ |

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Sayfa yükleme metrikleri (FCP, LCP, TBT, INP, CLS) — P8.1/P8.3 eşikleriyle | Metrik tanım/eşik kaynak araştırması (→ research-bank P8) |
| SPA istemci navigasyonu, geri/ileri, route değişimi | Routing mimarisi kararı (→ brain.md / ADR'ler) |
| Login/kayıt akışı navigasyonu, form geçişleri | Auth güvenlik mimarisi (→ Security Engineer alanı) |
| Oturum yönetimi: zaman aşımı, çok sekme, logout senkronu | Session token kriptografisi (→ security) |
| Hata sayfaları (404/500/offline) ve geri dönüş yolları | Sunucu tarafı hata yönetimi kodu (→ backend) |
| Tarayıcı uyumluluk matrisi + responsive breakpoint taraması (P8.5) | Tarayıcı sürüm envanteri (`⚠️ VERIFICATION REQUIRED` — P3'te YOK) |
| Grup bazlı navigasyon davranışları (çocuk 48px+ ⚠️, genç, yetişkin) | Persona içerik üretimi (→ persona dosyaları) |
| Klavye/odak navigasyonu davranış doğrulaması (2.4.7, 2.4.11) | Tam a11y denetimi (→ `[[personas/test-senaryolari/a11y-erisilebilirlik]]`) |

### §2.2 Hedef Kitle

| Persona | Bu senaryoda rolü |
|---------|-------------------|
| QA Engineer (`qa`) | Birincil yürütücü — Seviye 2/3 testleri |
| DevOps Engineer (`devops`) | İkincil — CI'da Lighthouse eşiği kapısı |
| UI Designer (`ui`) | Responsive/odak görsel doğrulama |
| Vault Steward | Denetim — §6 envanter sayımı |

### §2.3 Kapsam Dışı İstisnalar

| # | İstisna | Nereye Gider |
|---|---------|--------------|
| 1 | Coğrafi konum (`geolocation`) API'si | `⚠️ VERIFICATION REQUIRED` (P8'de doğrulanmış API listesinde **yok**) — §6.5 |
| 2 | Gerçek cihaz/lab ölçümü | `[[personas/methodology]]` Seviye 3+ notu |
| 3 | Sunucu uptime/CDN metrikleri | DevOps gözlemleme katmanı |

---

## §3 Mimari — Senaryo Yapısı

### §3.0 Dizin ve Bağımlılık Ağacı

```
.ai/.personas/test-senaryolari/browser-navigasyon.md   ← bu dosya (SSOT: senaryo)
├── [[personas/research-bank]] P8.1–P8.5               (eşik — VERIFIED)
├── [[personas/research-bank]] P5.2                    (WCAG kriter numaraları)
├── [[personas/methodology]] §2.2, §3.3, §3.4          (seviye, emülasyon, eşik kullanımı)
├── [[personas/test-scenarios-mapping]] §4.2–§4.8      (grup × senaryo, ortak katman)
├── [[personas/mood-taxonomy]] §2                      (mood adları — burada ad taşınır)
└── [[ADR-023-persona-driven-testing]] satır 17        (eşleme + coverage gate)
```

### §3.1 Test Blokları (BNV-001 → BNV-011)

| # | Blok | Kapsam | Birincil Metrik / Kriter |
|---|------|--------|--------------------------|
| BNV-001 | Sayfa Yükleme Testleri | FCP, LCP, TBT, INP, CLS ölçümü (throttling'li) | LCP ≤ 2500 ms · FCP 1 sn · TBT > 50 ms |
| BNV-002 | SPA Navigasyon Testleri | Route değişimi, geri/ileri, prefetch, scroll koruması | INP ≤ 200 ms · CLS ≤ 0.1 |
| BNV-003 | Login/Kayıt Akış Testleri | Form navigasyonu, hata durumları, doğrulama akışı | 3.3.8 · 3.3.7 · 3.2.6 |
| BNV-004 | Session Yönetimi Testleri | Zaman aşımı, çok sekme senkronu, logout | 2.2.1 `⚠️ VERIFICATION REQUIRED` |
| BNV-005 | Hata Sayfası Testleri | 404/500/offline, geri dönüş yolu, yeniden deneme | 1.4.3 · 2.4.7 |
| BNV-006 | Tarayıcı Uyumluluk Testleri | Chrome/Firefox/Safari/Edge + mobil tarayıcı matrisi | Sürüm envanteri `⚠️ VERIFICATION REQUIRED` |
| BNV-007 | Responsive Tasarım Testleri | P8.5 breakpoint'lerinde viewport taraması | 1.4.10 · 2.5.8 `⚠️` |
| BNV-008 | Çocuk Kullanıcı Testleri | 48px+ buton ⚠️, sadeleştirilmiş navigasyon, veli PIN popup yönü | 2.5.8 · KVKK veli onayı `⚠️ DERIVED` |
| BNV-009 | Genç Kullanıcı Testleri | Yaş kilidi akış navigasyonu ⚠️, e-posta doğrulama 24s | 3.3.1 `⚠️ VERIFICATION REQUIRED` |
| BNV-010 | Yetişkin Kullanıcı Testleri | Çoklu oturum, ayarlar derin navigasyon, geri bildirim | 3.2.6 · 2.4.7 |
| BNV-011 | Erişilebilirlik Hızlı Kontrolü | Klavye ile tam rota, odak görünürlüğü, odak gizlenmesi | 2.4.7 · 2.4.11 · 2.5.7 |

> ADR-023 satır 17 eşlemesi: `[[personas/test-scenarios-mapping]]` §4.2-S1..S6 kapsamının navigasyon yüzeyi.

#### §3.1.1 Blok Detayları (Açıklama — Başarı Kriteri)

**BNV-001 — Sayfa Yükleme:** Cold-load koşusudur; önbellek temiz, throttling aktif (§3.5). Başarı: FCP ≤ 1 sn, LCP ≤ 2500 ms, CLS ≤ 0.1, TBT ≤ 50 ms (P8.1/P8.3 `VERIFIED`). Eşik aşımında blok **kal**tır, neden (long task / görsel boyutu) rapora yazılır.

**BNV-002 — SPA Navigasyon:** Tam sayfa yüklemesi olmadan route değişimi; geri/ileri state koruması, scroll pozisyonu, prefetch edilmiş rota. Başarı: INP ≤ 200 ms, CLS ≤ 0.1; geri tuşu kaybolmuş state üretmez.

**BNV-003 — Login/Kayıt:** Form alanları arası sekme sırası, hata durumları, tekrar girilen verinin korunması (3.3.7), erişilebilir kimlik doğrulama (3.3.8). Başarı: hiçbir alanda odak kaybı yok, hata mesajı alana bağlı, zihinsel test gerektiren adım yok (`⚠️ VERIFICATION REQUIRED` — P5.5 form kriterleri tek tek açılmadı).

**BNV-004 — Session:** Zaman aşımı, çok sekme senkronu, logout sonrası geri tuşu koruması. Başarı: korumalı rota oturum düşüşünde login'e döner; ikinci sekmedeki oturum da düşer. Zaman aşımı değeri `⚠️ VERIFICATION REQUIRED` (30 dk varsayılan iddiası).

**BNV-005 — Hata Sayfaları:** 404 (geçersiz URL), 500 (sunucu hatası simülasyonu — gerçek üretim verisi yok), offline (ağ kesme). Başarı: her hata sayfasında görünür geri dönüş yolu; metin kontrastı ≥ 4.5:1 (1.4.3).

**BNV-006 — Tarayıcı Uyumluluk:** Chrome/Firefox/Safari/Edge masaüstü + mobil tarayıcı taraması. Başarı: kritik akışlar (auth, oynatma, navigasyon) 4 tarayıcıda da çalışır. Tarayıcı **sürümü** raporda `⚠️ VERIFICATION REQUIRED` ile kaydedilir (P3'te YOK — P8.7).

**BNV-007 — Responsive:** §3.4'teki 9 breakpoint sırayla uygulanır. Başarı: her viewport'ta menü kullanılabilir, yatay kaydırma (kaydırma çubuğu) görünmez, hedef boyutlar 2.5.8'e uyar. 1024×1366 listede olmadığından bu koşu `⚠️ VERIFICATION REQUIRED` ister.

**BNV-008 — Çocuk:** Sadeleştirilmiş navigasyon, büyük dokunma hedefi (48px+ `⚠️ DERIVED`), veli PIN popup'ının yönü ve iptal yolu. Başarı: çocuk menüsü S4/S6 rotalarına erişimi **reddeder ve reddi görünürdür**; veli onayı akışı `⚠️ DERIVED` (P6.4) olarak kaydedilir.

**BNV-009 — Genç:** Yaş kilidi ekranının navigasyonu (geri yolu var mı?), e-posta doğrulama bekleme ekranı, trend akışına derin link. Başarı: yaş kilidi ekranı çıkmaz sokağa sokmaz; e-posta doğrulama 24s akışı `⚠️ VERIFICATION REQUIRED` (mapping §4.4).

**BNV-010 — Yetişkin:** Derin ayarlar hiyerarşisi, breadcrumb/geri davranışı, çoklu oturum. Başarı: 3 seviyeden derin rota tek adımda geri dönülebilir; yardım konumu sabit (3.2.6).

**BNV-011 — A11y Hızlı Kontrol:** Klavye ile tam rota turlanır; odak her adımda görünür (2.4.7), odak hiçbir yerde sticky alt bilgi altında gizlenmez (2.4.11), sürükleme gereken her öğenin klavye alternatifi vardır (2.5.7). Derin denetim → `[[personas/test-senaryolari/a11y-erisilebilirlik]]`.

#### §3.1.2 Edge Case'ler

| # | Edge Case | Beklenen |
|---|-----------|----------|
| 1 | Ağ ortada kesilir (offline) | Offline durum mesajı + yeniden deneme; boş ekran yok |
| 2 | Aynı rota çift tıklanır | İkinci istek yok sayılır/yüklenmez (INP korunur) |
| 3 | Geri tuşu + form verisi | Form verisi korunur (3.3.7) veya net uyarı verilir |
| 4 | Modal açıkken geri tuşu | Modal kapanır, arka plan state korunur; odak geri gelir |
| 5 | 200% zoom (P8.5 küçük viewport + zoom) | İçerik kesilmez, yatay kaydırma kabul edilir ama menü çalışır |
| 6 | Sistem dilı farklı (locale alanı P8.4) | Tarih/sayı formatı locale'e uyar (`⚠️ VERIFICATION REQUIRED` — yerelleştirme kapsamı) |

### §3.2 WCAG Kriter — Eşik Tablosu (research-bank P5 — `VERIFIED`)

| Kriter | Başlık | Eşik / Değişken | Durum |
|--------|--------|-----------------|-------|
| 1.4.3 | Contrast (Minimum) | Metin ≥ 4.5:1; büyük metin ≥ 3:1 | `VERIFIED` |
| 1.4.11 | Non-text Contrast | UI bileşenleri/grafikler ≥ 3:1 | `VERIFIED` |
| 1.4.13 | Content on Hover or Focus | AA | `VERIFIED` |
| 2.4.7 | Focus Visible | AA | `VERIFIED` |
| 2.4.11 | Focus Not Obscured (Minimum) | AA — WCAG 2.2 **YENİ** | `VERIFIED` |
| 2.5.7 | Dragging Movements | AA — WCAG 2.2 **YENİ** | `VERIFIED` |
| 2.5.8 | Target Size (Minimum) | ≥ 24×24 CSS px (5 istisna: Spacing, Equivalent, Inline, User Agent Control, Essential) | `VERIFIED` |
| 3.2.6 | Consistent Help | A | `VERIFIED` |
| 3.3.7 | Redundant Entry | A | `VERIFIED` |
| 3.3.8 | Accessible Authentication (Minimum) | AA — WCAG 2.2 **YENİ** | `VERIFIED` |

> P5.5 kuralı: bu tabloda olmayan kriter numaraları (ör. 2.4.3 Focus Order, 1.4.5, 2.2.1) bu dosyada kullanılırsa `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.

### §3.3 Persona / Mood Eşlemesi (`[[personas/test-scenarios-mapping]]`)

| Grup (n) | Bu senaryoda beklenen navigasyon davranışı | Kısıt |
|---|---|---|
| Kız çocuk (17) | Büyük dokunma hedefi (48px+ ⚠️), az menü derinliği, veli PIN popup yönü | S4/S6 ❌ erişim reddi görünür |
| Erkek çocuk (12) | Aynı + oyunlaştırma akışında geri dönüş koruması | S4/S6 ❌ |
| Genç kız (17) | Hızlı sekme geçişleri, trend akışına derin link, yaş kilidi ekranı ⚠️ | Yaş kilidi 16 ⚠️ |
| Genç erkek (12) | Klavye kısayolları, gamer modu geri bildirimi | Yaş kilidi 16 ⚠️ |
| Yetişkin kadın (5) | Derin ayarlar navigasyonu, breadcrumb bekler | S6 mesaj ❌ / akış ❌ |
| Yetişkin erkek (5) | Çoklu oturum, sekme arası tutarlılık | S6 kısıtlı `⚠️` |

Mood notu (taşınan adlar → `[[personas/mood-taxonomy]]`): Melankolik → gece modu tercihi navigasyona dâhil mi? (S5 odaklı); Kaşif → geri dönüş korumazsa filtre kaybı (tipik hata modu).

### §3.4 Cihaz / Breakpoint Matrisi (research-bank P8.5 — `VERIFIED`)

| # | Viewport | # | Viewport |
|---|----------|---|----------|
| 1 | 320 × 568 | 6 | 1280 × 720 |
| 2 | 375 × 667 | 7 | 1600 × 1200 |
| 3 | 375 × 812 | 8 | 1920 × 1080 |
| 4 | 414 × 896 | 9 | 2560 × 1440 |
| 5 | 768 × 1024 | — | — |

> ⚠️ **VERIFICATION REQUIRED:** Eski mapping'te geçen **1024×1366 (tablet)** bu listede **YOK** — listede olmayan viewport ile test edilecekse önce P8.5'e eklenmelidir (§6.5).

### §3.5 Emülasyon Ayarlar (P8 — `VERIFIED`)

| Ayar | Değer | Kaynak |
|------|-------|--------|
| Ağ gecikmesi (mobil preset) | **150 ms** | P8.3 `VERIFIED` |
| Download | **1.6 Mbps** | P8.3 `VERIFIED` |
| Upload | **750 Kbps** | P8.3 `VERIFIED` |
| Paket kaybı | yok | P8.3 `VERIFIED` |
| Karşılık gelen bağlantı | ~%85 persentil; Lighthouse **"Slow 4G"** (eski adı "Fast 3G") | P8.3 `VERIFIED` |
| DevTools preset'leri | Slow 3G · Fast 3G · Slow 4G · Fast 4G (+ özel profil) | P8.3 `VERIFIED` |
| TBT | INP'nin laboratuvar proxy'si; **> 50 ms** long task main thread'i bloklar | P8.3 `VERIFIED` |

---

## §4 Kurallar

### §4.0 ZORUNLU: ŞABLON ÖNCE (Template-First)

Bu dosya yazılırken `.ai/.templates/documentation/docs-md-template.md` okunmuş ve §3.3 zorunlu blok (bu §4.0) korunmuştur. **Bu blok silinemez**; yeni bir BNV bloğu eklenirken önce şablon, sonra bu §4.x kuralları okunur (Guardrail #16).

### §4.1 Bağlayıcı Kurallar

| # | Kural |
|---|-------|
| 1 | Tüm eşikler research-bank'tan **taşınır**; burada eşik **uydurulmaz/rahatlatılmaz** |
| 2 | `VERIFIED` değeri `⚠️`'ya, `⚠️` asla `VERIFIED`'a yükseltilmez (ADR-005) |
| 3 | Frozen ADR'ler (001–037) değiştirilmez; yalnız başvurulur |
| 4 | Dosya adı değişikliği **onay** gerektirir (In-Place Refactoring) |
| 5 | Her değişiklik `log.md`'ye **append-only** kaydedilir |

### §4.2 Frontmatter — 7 Zorunlu Alan

`title` · `type: test-scenario` · `category: testing` · `version` · `status` · `authority` · `updated` — eksik alan dosyayı geçersiz kılar (§6.1).

### §4.3 Dil, Kod Adı ve Mojibake (UTF-8)

Türkçe karakterler bozuk yazılamaz; yazım yalnız `node .ai/scripts/vault-utf8-writer.mjs` ile yapılır; PowerShell yazma cmdlet'leri **YASAK**.

### §4.4 Wiki-Link ve Bağlantı Formatı

Biçim: `[[relative/path/to/file]]` — zorunlu 7 bağlantı bu dosyanın H1 hemen altındadır (§7.1 ile aynı küme).

### §4.5 Kaynak Etiketleme — Research-Bank Durum Aktarımım (ADR-005)

| Durum | Bu Dosyada Kullanım |
|-------|---------------------|
| `VERIFIED` | Eşik/kural olarak taşınır, kaynağı P# ile anılır |
| `SINGLE-SOURCE` / `CONFLICT` | Taşınır + çelişki notu aynen korunur |
| `⚠️ DERIVED` | Türetme olduğuyla birlikte taşınır |
| `⚠️ VERIFICATION REQUIRED` | Aynen taşınır; doğrulanmadan kullanılmaz |

### §4.6 Derinlik Standardı (500+)

Her senaryo dosyası **≥ 500 satır**tır; ekleme yaparken §6.1 kontrol listesi tekrar çalıştırılır.

### §4.7 REDACTED, KVKK ve Sür Politikası

Gerçek kimlik/iletişim/anahtar verisi vault'a yazılmaz (`[REDACTED]`); 16 yaş altı persona adımlarında veli onayı `⚠️ DERIVED` etiketiyle taşınır (P6.4); dosya adı değişikliği yapılmaz.

### §4.8 Yasak / Doğru Tablosu

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| Eşik uydurmak (`LCP < 3000 iyi olur`) | P8.1: LCP ≤ 2500 ms |
| `⚠️`'yı `VERIFIED` yapmak | Etiket aynen korunur |
| Coğrafi konum API'sini P8'de varmış gibi yazmak | `⚠️ VERIFICATION REQUIRED` (§5.4/§6.5) |
| PowerShell ile dosya yazmak | `vault-utf8-writer.mjs` |
| 500 satır altına düşmek | §4.6 |

---

## §5 Workflow

### §5.1 Genel Akış

```
PREPARE (§5.2) → EXECUTE (§5.3 adım tablosu + §5.4 kod bloğu) → REPORT (§5.5)
```

Seviyeler: 1 AI Rol · 2 Browser MCP · 3 Playwright · 4 Rapor — `[[personas/methodology]]` §2.2.

### §5.2 PREPARE — Hazırlık Adımları

| # | Hazırlık | Çıktı |
|---|----------|-------|
| P1 | research-bank P8.1–P8.5 oku (eşikler) | Eşik listesi |
| P2 | test-scenarios-mapping §4.4/§4.8 oku (grup kısıtları) | Grup notları |
| P3 | Cihaz/viewport seçimi (§3.4) | Viewport listesi |
| P4 | Ağ throttling profili seç (§3.5) | 150 ms / 1.6 Mbps / 750 Kbps |
| P5 | Tarayıcı sürümü kaydı (`⚠️ VERIFICATION REQUIRED` — P3'te YOK) | Sürüm satırı rapora |

### §5.3 Adım Tablosu (16 adım — asgari 10)

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Throttling profili uygula: 150 ms gecikme, 1.6 Mbps down, 750 Kbps up (P8.3) | Ağ koşulu aktif | DevTools Network Conditions satırı okunur | 150 ms · 1.6 Mbps · 750 Kbps (`VERIFIED`) | — |
| 2 | Ana sayfayı yüklemeye başla (cold load, önbellek kapalı) | İlk boyama başlar | FCP ölçümü | FCP ≤ 1 sn (`VERIFIED`) | — |
| 3 | LCP elementini bekle (hero/görsel) | LCP kaydedilir | Performance panel LCP | LCP ≤ 2500 ms; kötü > 4000 ms (`VERIFIED`) | — |
| 4 | Main thread long task taraması | TBT kaydedilir | Long task satırları | TBT > 50 ms ise bloklista (`VERIFIED`) | — |
| 5 | Layout shift ölç (yeniden yükleme/görsel yüklenmesi) | CLS kaydedilir | Layout Shift satırı | CLS ≤ 0.1; kötü > 0.25 (`VERIFIED`) | — |
| 6 | SPA'da 3 farklı route'a tıkla (iç sayfa → liste → detay) | Route değişir, tam sayfa yüklenmez | URL + DOM değişimi | INP ≤ 200 ms (`VERIFIED`) | 2.4.7 |
| 7 | Tarayıcı geri/ileri butonları | Geçmiş korunur, state geri gelir | `popstate` + görünüm | INP ≤ 200 ms (`VERIFIED`) | 2.4.7 |
| 8 | Login formuna klavye ile ilerle (Tab sırası) | Odak sırası mantıklı, her adımda odak görünür | Görsel odak halkası | — | 2.4.7 `VERIFIED` |
| 9 | Login formunda hata durumu üret (boş/yanlış alan) | Hata alanla ilişkili, anlaşılır | Metin + `aria` görünümü | — | 3.3.8 · 3.3.7 `VERIFIED` |
| 10 | Yardım erişimini bul (yardım bağlantısı konumu sabit mi) | Yardım her sayfada aynı yerde | 3 sayfada konum karşılaştırması | — | 3.2.6 `VERIFIED` |
| 11 | Oturumu 30 dk zaman aşımına bırak (hızlandırılmış) | Oturum düşer, korumalı rota login'e döner | Redirect gözlemi | Zaman aşımı değeri `⚠️ VERIFICATION REQUIRED` | 2.2.1 `⚠️ VERIFICATION REQUIRED` |
| 12 | Hata sayfasına git (geçersiz URL) | 404 görünür, ana rota/geri yolu var | Sayfa + dönüş butonu | — | 1.4.3 · 2.4.7 |
| 13 | P8.5 breakpoint listesini sırayla uygula (`page.setViewportSize`) | Her viewport'ta navigasyon kullanılabilir | 9 viewport × menü | 9 breakpoint (`VERIFIED`) | 2.5.8 ≥ 24×24 px (`VERIFIED`) |
| 14 | 320×568'de dokunma hedeflerini ölç | Küçük hedef yok (istisnalar dâhil) | Piksel ölçümü | — | 2.5.8 (`VERIFIED`) |
| 15 | Çocuk personası ile menüyü gez (48px+ buton ⚠️, sade akış) | Büyük hedefler, veli PIN yönü görünür | Görsel ölçüm + ekran | 48px+ `⚠️ DERIVED` (mapping §4.4) | 2.5.8 · KVKK `⚠️ DERIVED` |
| 16 | Raporu üret (Seviye 4) — eşik karşılaştırmalı | Geç/ kal raporu | §6.1 checklist | P8.1 hepsi (`VERIFIED`) | §6.2 tablosu |

### §5.4 Playwright / CDP Kod Bloğu (P8 — `VERIFIED` API adlar)

```js
// ✅ P8.4'te doğrulanmış API adları — uydurma API kullanılmaz
const { devices, chromium } = require('@playwright/test');

// 1) Cihaz emülasyonu (registry alanları: userAgent, screenSize, viewport,
//    hasTouch, deviceScaleFactor, isMobile, colorScheme, locale, timezoneId)
const context = await browser.newContext({ ...devices['iPhone 11'] });
const page = await context.newPage();

// 2) Viewport override (P8.4)
await page.setViewportSize({ width: 375, height: 812 });

// 3) Offline testi (P8.4) — context'i offline kur, navigasyonu gözlemle
//    (createContextSnapshot yerine: use: { offline: true } — test config)

// 4) CDP ağ emülasyonu (P8.4 — Network.emulateNetworkConditions)
const cdp = await context.newCDPSession(page);
await cdp.send('Network.emulateNetworkConditions', {
  offline: false,
  latency: 150,                        // P8.3 mobil preset gecikme
  downloadThroughput: 1.6 * 1024 * 1024 / 8,   // 1.6 Mbps → byte/s
  uploadThroughput: 750 * 1024 / 8,            // 750 Kbps → byte/s
});
```

```js
// ❌ YASAK — P8'de doğrulanmAMIŞ API'ler (varlıkları ispatlanmamıştır):
//   geolocation emülasyonu, browser.setGeolocation, custom UA zincirleri
//   → kullanılırsa: // ⚠️ VERIFICATION REQUIRED  etiketi düşer (§6.5)
```

### §5.5 REPORT (Seviye 4)

| Rapor Alanı | İçerik |
|-------------|--------|
| Ortam | Tarayıcı + sürüm (`⚠️ VERIFICATION REQUIRED`), viewport listesi, throttling profili |
| Metrik tablosu | FCP/LCP/TBT/INP/CLS ölçüm vs P8.1 eşiği (geç/kal) |
| Kırılma noktaları | Eşiği aşan breakpoint'ler |
| A11y hızlı sonuç | 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 · 3.3.7 · 3.3.8 durumu |
| Grup notları | Çocuk/genç/yetişkin adımları (15, 16) sonuçları |
| Etiket dökümü | §6.5 sayımı |

### §5.6 Hata Modları

| # | Hata Modu | Tipik Neden | Müdahale |
|---|-----------|-------------|----------|
| 1 | LCP eşiği aşım | Optimizasyon yok / görsel boyutu | DevOps+UI eskalasyon |
| 2 | INP > 200 ms | Long task (TBT > 50 ms ile korelasyonlu) | JS kırpması |
| 3 | Geri tuşu state kaybı | SPA router yapılandırması | Frontend düzeltme |
| 4 | Küçük dokunma hedefi (320×568) | Responsive eksik | UI düzeltme, 2.5.8 |
| 5 | Odak kayboluyor (modal sonrası) | Focus management | A11y senaryosuna handover |
| 6 | Zaman aşımı davranışı tutarsız | Sunucu/client çelişkisi | Security+Backend eskalasyon |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] Frontmatter 7 alan dolu
- [ ] Zorunlu 7 wiki-link H1 altında
- [ ] §1–§7 eksiksiz, §4.0 blok mevcut
- [ ] Adım tablosu ≥ 10 satır, 6 sütun (§5.3 → 16)
- [ ] Tüm eşikler P8 ile etiketli (`VERIFIED`/`⚠️`)
- [ ] Playwright bloğu yalnız P8.4 API adları
- [ ] ≥ 500 satır
- [ ] Mojibake 0, BOM yok
- [ ] log.md append-only giriş yapıldı

### §6.2 WCAG Kriter Durum Tablosu (boş — test sonunda doldurulur)

| Kriter | Adım(lar) | Durum | Kanıt |
|--------|-----------|-------|-------|
| 1.4.3 | 12 | ⬜ | — |
| 1.4.11 | 13 | ⬜ | — |
| 1.4.13 | 6 (hover/focus öğesi varsa) | ⬜ | — |
| 2.4.7 | 6, 7, 8, 12 | ⬜ | — |
| 2.4.11 | 8 | ⬜ | — |
| 2.5.7 | 13 (sürükleme varsa) | ⬜ | — |
| 2.5.8 | 13, 14, 15 | ⬜ | — |
| 3.2.6 | 10 | ⬜ | — |
| 3.3.7 | 9 | ⬜ | — |
| 3.3.8 | 9 | ⬜ | — |

### §6.3 EXCLUDED Listesine Atıf (research-bank §6.4)

Eski vault'tan devralınan iddialar (indirilebilirlik 41 vs 39, kapsam sayıları vb.) bu senaryoda **kullanılmaz**; araştırma §6.4'te `EXCLUDED` ise burada da atlanır. Sayım iddiaları: `[[personas/test-scenarios-mapping]]` §5.3 uyarısınca `⚠️ VERIFICATION REQUIRED`.

### §6.4 Quality Report (Bu Dosyanın Kendisi)

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Bölüm | §1–§7 (7 bölüm) |
| Blok | BNV-001 … BNV-011 (11) |
| Adım | 16 (§5.3) |
| Breakpoint | 9 (P8.5 `VERIFIED`) |
| Status | Red Team · Human Mode · Truth Mode |

### §6.5 Bu Dosyadaki ⚠️ Etiketleri (Toplam)

**⚠️ VERIFICATION REQUIRED — 6 adet:**

| # | Konu | Kaynak |
|---|------|--------|
| 1 | Tarayıcı sürüm envanteri (P3'te YOK) | P8.7 |
| 2 | 1024×1366 tablet viewport P8.5'te yok | mapping §5.1 + P8.5 |
| 3 | Zaman aşımı 30 dk değeri | mapping §4.6 (S1 ortak katman) |
| 4 | Retry-After 60 sn | mapping §4.6 |
| 5 | RFC 5322 e-posta doğrulama biçimi | mapping §4.6 |
| 6 | Yaş kilidi 16 akışı (genç) | mapping §4.8 / P6.5 ("KVKK 16 diyor" YANLIŞTIR) |
| + | "gerçek 4G hızı" ifadesi yasak (simülasyondur) | P8.7 |

**⚠️ DERIVED — 3 adet:**

| # | Konu | Kaynak |
|---|------|--------|
| 1 | 16 yaş altı → veli onayı (politika sonucu) | P6.4 |
| 2 | Çocuk 48px+ buton hedefi | mapping §4.4 (eskiden gelme) |
| 3 | Persona'ya özel metrik hedefi yazılsa | P8.7 |

> Not: Coğrafi konum API'si bu dosyada **kullanılmadı**; kullanılırsa ek `⚠️ VERIFICATION REQUIRED` (§2.3/§5.4).

---

### §6.6 Test Çalıştırma Kontrol Listesi & Test Koşusu Şablonu

#### §6.6.1 Çalıştırma Öncesi (Run Gate)

| # | Kontrol | Kaynak |
|---|---------|--------|
| 1 | research-bank P8 eşikleri okundu mu? (eşik uydurulmayacak) | P8.1–P8.5 |
| 2 | Throttling profili seçildi mi? (150 ms / 1.6 Mbps / 750 Kbps) | P8.3 |
| 3 | Viewport listesi hazır mı? (§3.4 — 9 breakpoint) | P8.5 |
| 4 | Grup kısıtları okundu mu? (§3.3 — çocuk/genç/yetişkin) | mapping §4.4/§4.8 |
| 5 | Tarayıcı sürümü kayda hazır mı? (`⚠️ VERIFICATION REQUIRED`) | P8.7 |
| 6 | Template-First bloğu (§4.0) korunuyor mu? | Guardrail #16 |

#### §6.6.2 Test Koşusu — [TARIH]

**Ortam**

| Alan | Değer |
|------|-------|
| Tarayıcı + sürüm | `[kayıt]` — `⚠️ VERIFICATION REQUIRED` (P3'te YOK) |
| Throttling | 150 ms · 1.6 Mbps · 750 Kbps (P8.3) |
| Viewport seti | §3.4 (9 breakpoint) |
| Seviye | 1 AI Rol · 2 Browser MCP · 3 Playwright · 4 Rapor |

**Metrik Sonuçları**

| Metrik | Eşik (P8.1/P8.3) | Ölçüm | Geç/Kal |
|--------|------------------|-------|---------|
| FCP | ≤ 1 sn | — | ⬜ |
| LCP | ≤ 2500 ms (kötü > 4000) | — | ⬜ |
| INP | ≤ 200 ms (kötü > 500) | — | ⬜ |
| CLS | ≤ 0.1 (kötü > 0.25) | — | ⬜ |
| TBT | ≤ 50 ms (> 50 blokler) | — | ⬜ |

**Kırılan Breakpoint'ler:** `[liste]` — 1024×1366 hariç (§6.5 madde 2).

**Adım Sonuçları:** §5.3 — 16 adımın her satırı PASS/FAIL + kanıt linki.

**Etiket Dökümü:** §6.5 — 6 `⚠️ VERIFICATION REQUIRED` + 3 `⚠️ DERIVED`.

**Sonuç:** `[GEÇ / KAL]` — Kalışta neden + eskalasyon (§5.6).

---

## §7 Referanslar

### §7.1 Wiki-link Referanslar

| Bağlantı | Rol |
|----------|-----|
| [[personas/index]] | Persona ana indeksi |
| [[personas/methodology]] | Test metodolojisi (seviye, akış) |
| [[personas/research-bank]] | P5/P6/P8 eşik kaynağı |
| [[personas/test-scenarios-mapping]] | Grup × senaryo matrisi |
| [[personas/persona-template]] | Persona alan şablonu |
| [[ADR-023-persona-driven-testing]] | Karar + coverage gate |
| [[personas/test-senaryolari/a11y-erisilebilirlik]] | Kardeş senaryo (a11y derinliği) |

### §7.2 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yapan |
|-------|-------|-----------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — eski iskelet (684 satır) research-bank P8 ile yeniden bağlandı; 16 adım, 11 blok, 9 breakpoint | Vault Documentation Specialist |
