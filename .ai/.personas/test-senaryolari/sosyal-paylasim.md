---
title: "CoreMusic — Test Senaryosu: Sosyal Paylaşım (SOS)"
type: test-scenario
category: testing
version: 1.0.0
status: active
authority: "Test Senaryosu — SSOT: personas/test-senaryolari/sosyal-paylasim"
updated: 2026-09-26
---

# CoreMusic — Test Senaryosu: Sosyal Paylaşım (SOS)

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/methodology]] · [[personas/research-bank]] · [[personas/test-scenarios-mapping]] · [[personas/persona-template]] · [[ADR-023-persona-driven-testing]] · [[personas/test-senaryolari/a11y-erisilebilirlik]]

---

| Alan | Değer |
|------|-------|
| Dosya Adı | `sosyal-paylasim.md` |
| Dosya Yolu | `.ai/.personas/test-senaryolari/sosyal-paylasim.md` |
| Dosya Tipi | Test senaryosu (6 senaryodan 1'i) — **karar DEĞİLDİR** (ADR değil) |
| Hedef Kitle | QA Engineer (birincil), Security Engineer (gizlilik/izin), UI Designer (akış UI) |
| Yapı | H1 + Zorunlu Bağlantılar + §1 Amaç → §7 Referanslar (7 bölüm + 7 alanlı frontmatter) |
| ADR-023 Karşılığı | 20 persona matrisi **satır 20 — Sosyal Paylaşım kişisi** (§2.2a; kaynak: eski vault `test-senaryolari/sosyal-paylasim.md`) |
| Mapping Karşılığı | **S6 Social** — 46/68 uygulanabilir; **çocuk ❌ · yetişkin ⚠️ kısıtlı (mesaj ❌, akış ❌)** — `[[personas/test-scenarios-mapping]]` §4.2/§4.3 |
| Test Seviyeleri | Seviye 1 AI Rol · Seviye 2 Browser MCP · Seviye 3 Playwright · Seviye 4 Rapor |
| Eşik Kaynağı | `[[personas/research-bank]]` **P8 (metrik)** + **P5 (WCAG)** + **P6 (KVKK/yaş — `⚠️ DERIVED`)** |
| Adım Sayısı | **15** (asgari 10 — §5.3) |
| Test Blokları | SOS-001 … SOS-010 (10 blok — §3.1) |
| Zorunlu Blok | §4.0 Şablon-Önce Kural Bloğu (silinemez) |
| Eski Kaynak | `coremusic.net.old/.ai/personas/test-senaryolari/sosyal-paylasim.md` (468 satır, **salt okunur**) — iskelet alındı, P8/P5/P6 + mapping §4.4-S6 ile yeniden bağlandı |
| Kayıt Kuralı | Değişiklik Geçmişi **append-only** |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya/klasör adı ASCII |
| Authority | `Test Senaryosu — SSOT: personas/test-senaryolari/sosyal-paylasim` |
| Governance | Red Team · Human Mode · Truth Mode |
| Versiyon | 1.0.0 (ilk üretim — 2026-09-26) |

---

## §1 Amaç

Bu dosya, CoreMusic'in **sosyal paylaşım test senaryosudur**: playlist/şarkı/profil paylaşımı, işbirlikçi özellikler, sosyal akış, bildirimler, gizlilik kontrolleri ve grup bazlı sosyal davranışların (çocuk ❌ erişim reddi · genç tam · yetişkin ⚠️ kısıtlı) hangi adımlarla test edileceğini tanımlar. Kısıt matrisi `[[personas/test-scenarios-mapping]]` §4.3'ten **taşınır**: S6 = kız çocuk ❌ · erkek çocuk ❌ · genç ✅ · yetişkin ⚠️ kısıtlı. Eski vault'taki 468 satırlık senaryo **kopyalanmamış**; iskelet research-bank ile **yeniden bağlanmıştır** (ADR-005 Zero Hallucination).

| Boyut | Değer |
|-------|-------|
| Ne taşır | SOS test blokları, ≥10 adımlık adım tablosu (Adım · Eylem · Beklenen · Doğrulama · Metrik · WCAG), erişim-red kanıtı adımları, KVKK/veli adımı, gizlilik matrisi, Playwright kod bloğu |
| Ne taşımaz | Eşik kaynağı (→ research-bank), test akışı (→ methodology), kısıt matrisi (→ mapping §4.3), karar (→ ADR-023), moderasyon politikası (`⚠️ VERIFICATION REQUIRED`) |
| Neden yazıldı | Eski senaryo research-bank'tan önce yazılmıştı → kısıtlar/eşikler kaynakla bağlandı |
| Kim okumalı | Sosyal/gizlilik testi çalıştıran her ajan (QA birincil, Security ikincil) |
| Kanıt zinciri | `research-bank` P5/P6/P8 → `methodology` → persona → **bu dosya** → `ADR-023` (gate) |

### §1.1 Bu Dosya Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Dosya | Bu Dosya Kullanılmaz |
|---------|-------------|----------------------|
| Sosyal paylaşım test adımları + erişim-red kanıtları | ✅ Bu dosya | — |
| Kısıt matrisi (çocuk ❌ / yetişkin ⚠️) | `[[personas/test-scenarios-mapping]]` §4.3-S6 | ❌ (taşınır) |
| WCAG/metrik eşikleri | `[[personas/research-bank]]` P5/P8 | ❌ (taşınır) |
| Veli onayı politikası | `[[personas/research-bank]]` P6.4 `⚠️ DERIVED` | ❌ (taşınır) |
| Test akışı/seviyeleri | `[[personas/methodology]]` | ❌ |
| Coverage gate | `[[ADR-023-persona-driven-testing]]` | ❌ |

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Playlist/şarkı/profil paylaşım akışları (bağlantı üretimi, önizleme) | Paylaşım altyapısı/URL imzalama (→ backend/security) |
| İşbirlikçi özellikler (ortak liste yorumu vb. — kapsam mapping'e bağlı `⚠️`) | Yorum/raporlama altyapısı (→ backend) |
| Sosyal akış görünümü (genç: ✅; yetişkin: ❌ kısıt görünür) | Akış öneri algoritması (→ muzik-kesfi senaryosu) |
| Bildirimler (gelen/okundu/susturma) | Push altyapısı (→ devops) |
| Gizlilik kontrolleri (3 görünürlük modu — mapping §4.4-S5 köprüsü) | Profil veri modeli |
| Çocuk erişim reddi UI'ı (**zorunlu kanıt**) + KVKK/veli onayı | Veli onayının hukuki yorumu (→ P6 `⚠️ DERIVED`, gerekirse ADR-088+) |
| Yetişkin kısıtı: paylaş/takip/yorum ✅ · **mesaj ❌ · akış ❌** | Kısıt politikasının kaynağı kararı (→ mapping) |
| Moderasyon/raporlama davranışı (`⚠️ VERIFICATION REQUIRED`) | İçerik moderasyon sistemi tasarımı |

### §2.2 Hedef Kitle

| Persona | Bu senaryoda rolü |
|---------|-------------------|
| QA Engineer (`qa`) | Birincil yürütücü |
| Security Engineer (`security`) | Gizlilik/izin/erişim reddi denetimi (ikincil) |
| UI Designer (`ui`) | Akış/bildirim UI doğrulama |
| Vault Steward | Denetim — §6 envanter |

### §2.3 Kapsam Dışı İstisnalar

| # | İstisna | Nereye Gider |
|---|---------|--------------|
| 1 | Moderasyon/raporlama SLA'sı (kaç saatte karar) | `⚠️ VERIFICATION REQUIRED` |
| 2 | Takipçi sayısı/sınır değerleri | `⚠️ VERIFICATION REQUIRED` |
| 3 | Bildirim frekansı eşiği | `⚠️ VERIFICATION REQUIRED` (mapping §4.4-S5 "bildirim frekansı" kaynağı yok) |

---

## §3 Mimari — Senaryo Yapısı

### §3.0 Dizin ve Bağımlılık Ağacı

```
.ai/.personas/test-senaryolari/sosyal-paylasim.md      ← bu dosya (SSOT: senaryo)
├── [[personas/test-scenarios-mapping]] §4.3/§4.4-S6   (kısıt matrisi — VERIFIED mapping)
├── [[personas/research-bank]] P6.3/P6.4               (yaş/veli onayı — DERIVED)
├── [[personas/research-bank]] P8.1–P8.5               (metrik — VERIFIED)
├── [[personas/research-bank]] P5.2                    (WCAG kriter numaraları)
├── [[personas/methodology]] §2.2, §3.3, §3.4
├── [[personas/mood-taxonomy]] §3.x                    (Sosyal küme + paylaşım izni notu)
└── [[ADR-023-persona-driven-testing]] satır 20        (eşleme + coverage gate)
```

### §3.1 Test Blokları (SOS-001 → SOS-010)

| # | Blok | Kapsam | Birincil Kriter / Not |
|---|------|--------|----------------------|
| SOS-001 | Playlist Paylaşım | Bağlantı üretimi, önizleme, iptal | 2.4.7 · 1.4.3 |
| SOS-002 | Şarkı Paylaşım | Tek şarkı paylaşımı, panoya kopyalama | 2.5.8 · 3.3.7 |
| SOS-003 | Profil Paylaşım | Profil bağlantısı, görünür alanlar (gizlilik modu ile kesişim) | 1.3.1 `⚠️` (P5.5) |
| SOS-004 | İşbirlikçi Özellikler | Ortak liste yorumu/davet (kapsam `⚠️ VERIFICATION REQUIRED`) | 3.2.6 |
| SOS-005 | Sosyal Akış | Akış görünümü — **genç ✅**; **yetişkin ❌ kısıt görünür** | Kısıt red'i görünür (1.4.3) |
| SOS-006 | Bildirimler | Gelen kutusu, okundu, susturma | 2.4.7 · frekans `⚠️` |
| SOS-007 | Gizlilik Kontrolleri | 3 görünürlük modu geçişi (mapping §4.4-S5) | 3.2.6 |
| SOS-008 | Çocuk Sosyal | **S6 ❌ 29 çocuk** — erişim reddi UI'ı + KVKK/veli onayı | 1.4.3 (ret okunur) · `⚠️ DERIVED` |
| SOS-009 | Genç Sosyal | Paylaş/takip/yorum/mesaj/akış tam; yaş kilidi 16 ⚠️ | mapping §4.4-S6 |
| SOS-010 | Yetişkin Sosyal | Paylaş/takip/yorum ✅ · **mesaj ❌ · akış ❌** kısıtı görünür | Kısıt reddi görünür |

> ADR-023 satır 20 eşlemesi. mapping §4.3: S6 = Kız çocuk ❌ · Erkek çocuk ❌ · Genç kız ✅ · Genç erkek ✅ · Yetişkin kadın ⚠️ · Yetişkin erkek ⚠️.

### §3.1.1 Blok Detayları (Açıklama — Başarı Kriteri)

**SOS-001/002/003 — Paylaşım:** Üç paylaşım yüzeyi de aynı kalıbı izler: paylaş → yöntem seç (bağlantı/pano) → başarı geri bildirimi → iptal edilebilir. Başarı: bağlantı üretilir; önizleme görünür; geri bildirim okunur (1.4.3).

**SOS-004 — İşbirlikçi:** Kapsam **mapping'e bağlı**dır ve `⚠️ VERIFICATION REQUIRED` işaretlidir (mapping §4.4-S6 yalnızca "paylaşım, takip, yorum, mesaj, akış" sayar; işbirlikçi yorum kapsamı net değil). Bu blok kapsam doğrulaması içerir: kapsam netleşene kadar sonuç **bilgi** sayılır, PASS/FAIL sayılmaz.

**SOS-005 — Akış:** Genç için akış tam çalışır. **Yetişkin akışı ❌** (mapping §4.4-S6: "akış ❌") → kısıt UI'da görünür ve anlaşılır red olarak görünür; sessizce boş akış **FAIL**.

**SOS-006 — Bildirimler:** Gelen kutusu, okundu işareti, susturma çalışır. Frekans eşiği research-bank'ta yok → `⚠️ VERIFICATION REQUIRED`.

**SOS-007 — Gizlilik:** 3 görünürlük modu geçişi anlık (yeniden yükleme yok); mod değişikliği profil görünümüne yansır.

**SOS-008 — Çocuk (KVKK):** 29 çocuk S6'ya **uygulanamaz** → "Erişim reddi görünür" (mapping §4.4-S6 doğrulama yöntemi: Erişim reddi UI'ı; WCAG etkisi: 1.4.3 kontrast). KVKK/veli onayı adımı zorunlu (§5.3/11): P6.4 `⚠️ DERIVED` (TMK m.11=18 · COPPA 13 · GDPR m.8=16; "KVKK 16 diyor" YANLIŞTIR — P6.5).

**SOS-009 — Genç:** Paylaş/takip/yorum/mesaj/akış tam; yaş kilidi 16 kademeli ⚠️. Mood notu (mood-taxonomy): paylaşım izni boundary'si genç kızda gözetilir.

**SOS-010 — Yetişkin:** Paylaş/takip/yorum ✅; **mesaj ❌ ve akış ❌** kısıtının her ikisi de ayrı ayrı görünür red ile doğrulanır; kısıt metni okunur (1.4.3).

### §3.1.2 Edge Case'ler

| # | Edge Case | Beklenen |
|---|-----------|----------|
| 1 | Çocuk derin bağlantı ile paylaşım sayfasına girer | Yönlendirme + görünür red; işlev gizli ama erişim denetimi sunucu tarafı |
| 2 | Yetişkin mesaj kutusunu arama ile bulur | Mesaj ❌ kısıtı aramada da geçerli; bulunamaz **veya** görünür red |
| 3 | Paylaşım bağlantısında oturumsuz açılma | Önizleme kamu verisiyle sınırlı; oturum istemi net |
| 4 | Gizlilik modu "gizli" iken profil paylaşılır | Paylaşım görünmezlik kuralını ihlal etmez |
| 5 | Bildirim susturulur ama akışa düşmeye devam eder | Susturma etkilidir (okundu/sustur idempotent) |
| 6 | Veli onayı reddedilir | Sosyal işlevler kalıcı kapalı; hesap değişmez |
| 7 | Paylaşım sonrası gizlilik modu "gizli"ye çevrilir | Mevcut paylaşımın görünürlüğü yeni moda uyar/red edilir |
| 8 | Aynı ret ekranı 2. kez görülür | Mesaj tutarlı; ikinci kez farklı red metni yok |
| 9 | Yetişkin paylaşılan profil bağlantısını açar | Paylaş/takip/yorum görünür; mesaj/akış erişimi red (8–9 ile tutarlı) |

### §3.2 WCAG Kriter — Eşik Tablosu (research-bank P5 — `VERIFIED`)

| Kriter | Başlık | Eşik / Değişken | Durum |
|--------|--------|-----------------|-------|
| 1.4.3 | Contrast (Minimum) | Metin ≥ 4.5:1; büyük ≥ 3:1 | `VERIFIED` |
| 1.4.11 | Non-text Contrast | UI bileşenleri ≥ 3:1 | `VERIFIED` |
| 1.4.13 | Content on Hover or Focus | AA | `VERIFIED` |
| 2.4.7 | Focus Visible | AA | `VERIFIED` |
| 2.4.11 | Focus Not Obscured (Minimum) | AA — WCAG 2.2 **YENİ** | `VERIFIED` |
| 2.5.7 | Dragging Movements | AA — WCAG 2.2 **YENİ** | `VERIFIED` |
| 2.5.8 | Target Size (Minimum) | ≥ 24×24 CSS px (5 istisna) | `VERIFIED` |
| 3.2.6 | Consistent Help | A | `VERIFIED` |
| 3.3.7 | Redundant Entry | A | `VERIFIED` |
| 3.3.8 | Accessible Authentication (Minimum) | AA — WCAG 2.2 **YENİ** | `VERIFIED` |

### §3.3 Persona / Mood Eşlemesi (`[[personas/test-scenarios-mapping]]`)

| Grup (n) | S6 beklentisi (mapping §4.4-S6) | Doğrulama yöntemi |
|---|---|---|
| Çocuk (29) | ❌ Uygulanamaz | Erişim reddi UI'ı |
| Genç (29) | Paylaşım, takip, yorum, mesaj, akış | Akış + moderasyon akışı (`⚠️` kapsam) |
| Yetişkin (10) | ⚠️ Kısıtlı: paylaş/takip/yorum var; **mesaj ❌, akış ❌** | Kısıt reddi görünür |

Mood çaprazı (mapping §4.7): **Sosyal (5) → birincil S6** (tipik hata: "mesaj/akış kısıtları belirsiz"); Enerjik (13) → ikincil S6; Gamer (2) → ikincil (kısayol çakışması). Paylaşım izni boundary notu: mood-taxonomy §3.6 (Genç kız satırı).

### §3.1.3 Kısıt Matrisi Doğrulama Tablosu (mapping §4.3 — taşınan)

| Grup | Beklenen | Bu Senaryoda Adım | Kanıt Tipi |
|---|---|---|---|
| Kız çocuk (17) | ❌ | 10, 11 | Görünür red ekranı |
| Erkek çocuk (12) | ❌ | 10, 11 | Görünür red ekranı |
| Genç kız (17) | ✅ | 5–9 | Tam akış PASS |
| Genç erkek (12) | ✅ | 5–9 | Tam akış PASS |
| Yetişkin kadın (5) | ⚠️ kısıtlı | 6, 8, 9 | Mesaj ❌ + akış ❌ ayrı red |
| Yetişkin erkek (5) | ⚠️ kısıtlı | 6, 8, 9 | Mesaj ❌ + akış ❌ ayrı red |

> Bu tablo mapping §4.3'ten **taşınır**; sayılar (17/12/5) mapping sayımıdır; bilinen persona sayı çelişkileri mapping §1.3'te `⚠️ VERIFICATION REQUIRED` olarak durur ve **burada çözülmez**.

### §3.4 Cihaz / Breakpoint Matrisi (research-bank P8.5 — `VERIFIED`)

| # | Viewport | # | Viewport |
|---|----------|---|----------|
| 1 | 320 × 568 | 6 | 1280 × 720 |
| 2 | 375 × 667 | 7 | 1600 × 1200 |
| 3 | 375 × 812 | 8 | 1920 × 1080 |
| 4 | 414 × 896 | 9 | 2560 × 1440 |
| 5 | 768 × 1024 | — | — |

### §3.5 Emülasyon Ayarlar (P8 — `VERIFIED`)

| Ayar | Değer | Kaynak |
|------|-------|--------|
| Ağ gecikmesi / down / up | **150 ms · 1.6 Mbps / 750 Kbps** | P8.3 |
| TBT | **> 50 ms** long task bloklar | P8.3 |
| Cihaz registry | `playwright.devices` (userAgent, screenSize, viewport, hasTouch, deviceScaleFactor, isMobile, colorScheme, locale, timezoneId) | P8.4 |
| Viewport override | `page.setViewportSize()` | P8.4 |
| Offline | `use: { offline: true }` | P8.4 |
| CDP ağ | `Network.emulateNetworkConditions` | P8.4 |

### §3.5.1 Persona-Spesifik Notlar (Grup Bazlı)

| Grup | Sosyal davranışı | Doğrulanacak şey | Kaynak |
|---|---|---|---|
| Çocuk (Sosyal olanlar dâhil) | S6 ❌ — sosyal yok | Ret ekranı görünür; derin bağlantı bile geçmez | mapping §4.3 |
| Genç (Sosyal) | Paylaşım + mesaj + akış tam | 5–9 tam akış | mapping §4.4-S6 |
| Genç (Enerjik/Gamer) | Hızlı paylaşım, kısayollar | Kısayol çakışması yok (tipik hata) | mapping §4.7 |
| Yetişkin (Anne/Baba) | Paylaş/takip/yorum ✅; mesaj/akış ❌ | Ayrı ayrı 2 red ekranı | mapping §4.4-S6 |
| Yetişkin (Romantik) | Profil paylaşımı görünür modla tutarlı | Adım 4 + 7 | mood §3.6 notu |

### §3.1.4 Paylaşım İzin Boundary Kontrolü (mood-taxonomy §3.6 — taşınan)

| # | Grup | Mood notu | Paylaşım beklenen durum | Adım |
|---|---|---|---|---|
| 10 | Genç kız | "Paylaşım izni boundary" | İzinli paylaşım tam; izinsiz alanlarda red görünür | 3–6 |
| 14 | Yetişkin | Anne/Baba — ortak dinleme | Paylaş ✅; mesaj/akış ❌ ayrı red | 8, 9 |
| 16 | Mood-geçiş kişisi | Arabesksever ↔ Danssever (tür paylaşımları) | Paylaşılan tür bilgisi kümeyle tutarlı | 3 |

> Bu tablo mood-taxonomy §3.6'dan **taşınan** özet; küme tanımları `[[personas/mood-taxonomy]]`'tedir, burada tekrar tanımlanmaz.

---

## §4 Kurallar

### §4.0 ZORUNLU: ŞABLON ÖNCE (Template-First)

Bu dosya `.templates/documentation/docs-md-template.md`'ye göre yazılmıştır; bu blok **silinemez**. Yeni SOS bloğu eklenirken önce şablon, sonra §4.x (Guardrail #16).

### §4.1 Bağlayıcı Kurallar

| # | Kural |
|---|-------|
| 1 | Eşik/kısıt research-bank + mapping'ten taşınır; uydurulmaz |
| 2 | `VERIFIED` ↓ `⚠️` olur; `⚠️` ↑ `VERIFIED` olmaz (ADR-005) |
| 3 | Frozen ADR'ler (001–037) değiştirilmez |
| 4 | Dosya adı değişikliği onay gerektirir |
| 5 | Her değişiklik `log.md` append-only |

### §4.2 Frontmatter — 7 Zorunlu Alan

`title` · `type` · `category` · `version` · `status` · `authority` · `updated`.

### §4.3 Dil, Kod Adı ve Mojibake (UTF-8)

Yazım yalnız `vault-utf8-writer.mjs`; PowerShell yazma cmdlet'leri **YASAK**.

### §4.4 Wiki-Link ve Bağlantı Formatı

`[[relative/path/to/file]]` — zorunlu 7 bağlantı H1 altında (§7.1).

### §4.5 Kaynak Etiketleme — Durum Aktarımı (ADR-005)

`VERIFIED` taşınır + P# kaynağı; `⚠️ DERIVED` türetme olduğuyla; `⚠️ VERIFICATION REQUIRED` aynen — doğrulanmadan iddia edilmez.

### §4.6 Derinlik Standardı (500+)

≥ 500 satır; ekleme sonrası §6.1 tekrar çalıştırılır.

### §4.7 REDACTED, KVKK ve Sür Politikası

Gerçek kişi/iletişim/anahtar verisi yazılmaz (`[REDACTED]`); **16 yaş altı veli onayı `⚠️ DERIVED`** (P6.4); "KVKK 16" gerekçesi yasak (P6.5); dosya adı değişmez.

### §4.8 Yasak / Doğru Tablosu

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| Çocukta sosyalı "test edilebilir" saymak | S6 ❌ + görünür red kanıtı |
| Yetişkin kısıtını "kısmi PASS" saymak | Mesaj ❌ ve akış ❌ ayrı red'ler |
| Moderasyon SLA'sı iddia etmek | `⚠️ VERIFICATION REQUIRED` |
| PowerShell ile yazma | `vault-utf8-writer.mjs` |
| 500 satır altı | §4.6 |

---

## §5 Workflow

### §5.1 Genel Akış

```
PREPARE (§5.2) → EXECUTE (§5.3 + §5.4) → REPORT (§5.5)
```

### §5.2 PREPARE — Hazırlık Adımları

| # | Hazırlık | Çıktı |
|---|----------|-------|
| P1 | P8 eşikleri + P5.2 oku | Eşik listesi |
| P2 | mapping §4.3 + §4.4-S6 + §4.8 oku | Kısıt matrisi |
| P3 | P6.3–P6.5 oku (KVKK/veli) | `⚠️ DERIVED` notu |
| P4 | 3 test hesabı (çocuk/genç/yetişkin) + 2 yetişkin | Hesap listesi |
| P5 | Viewport + throttling seç | Koşu profili |

### §5.3 Adım Tablosu (15 adım — asgari 10)

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Throttling uygula (150 ms · 1.6/0.75 Mbps) | Ağ koşulu aktif | Network Conditions | 150 ms · 1.6/0.75 (`VERIFIED`) | — |
| 2 | Sosyal sayfasını yükle (genç hesap, cold) | FCP/LCP kaydedilir | Performance panel | FCP ≤ 1 sn · LCP ≤ 2500 ms (`VERIFIED`) | — |
| 3 | Playlist paylaş (bağlantı üret) | Bağlantı + başarı geri bildirimi | UI + bağlantı | INP ≤ 200 ms (`VERIFIED`) | 2.4.7 |
| 4 | Profil paylaş (gizlilik modu "gizli" iken) | Görünürlük kuralı ihlal edilmez | Mod + paylaşım sonucu | — | 1.4.3 |
| 5 | Genç: sosyal akışa gir | Akış görünür ve dolu/tutarlı | Akış DOM'u | CLS ≤ 0.1 (`VERIFIED`) | 1.3.1 `⚠️` |
| 6 | Genç: mesaj gönder (2. hesaba) | Mesaj gider + görünür | 2. hesapta alım | — | 2.5.8 |
| 7 | Bildirim gelir → okundu → sustur | Üçü de çalışır; susturma etkili | Bildirim durumu | Frekans `⚠️ VERIFICATION REQUIRED` | 2.4.7 |
| 8 | Yetişkin: mesaj kutusunu dene | **❌ Kısıt — görünür red** | Red ekranı | Kısıt: mapping §4.4-S6 | 1.4.3 |
| 9 | Yetişkin: sosyal akışa gir | **❌ Kısıt — görünür red** (ayrı ekran) | Red ekranı | Kısıt: mapping §4.4-S6 | 1.4.3 |
| 10 | Çocuk: paylaşım sayfasına git | **S6 ❌ — erişim reddi görünür** | Ret ekranı + yetki | Ret: mapping §4.4-S6 | 1.4.3 (ret okunur) |
| 11 | **KVKK/veli onayı (çocuk)** — veli akışını tetikle | Onay akışı görünür; ret sosyali kapalı tutar | Ekran + onay `⚠️` | Veli onayı `⚠️ DERIVED` (P6.4) | 2.5.8 (büyük buton) |
| 12 | Çocuk: derin bağlantı ile atla dene | Yönlendirme + ret; işlev yok | URL + ekran | — | — |
| 13 | 320×568 + 768×1024'de paylaşım tekrarı | Paylaşım menüsü her viewport'ta kullanılabilir | 2 viewport | 2 breakpoint (`VERIFIED`) | 2.5.8 ≥ 24 px |
| 14 | Klavye ile akış + ret akışlarını tur | Odak görünür; ret ekranı klavye ile geçilir | Odak turu | — | 2.4.7 · 2.4.11 |
| 15 | Rapor (Seviye 4) — kısıt kanıtları ekle | Geç/kal + etiket dökümü | §6.1 | P8.1 (`VERIFIED`) | §6.2 |

### §5.4 Playwright / CDP Kod Bloğu (P8 — `VERIFIED` API adlar)

```js
const { devices } = require('@playwright/test');

// Çocuk hesabı koşusu — mobil viewport (P8.4 registry)
const context = await browser.newContext({ ...devices['iPhone 11'] });
const page = await context.newPage();

// Viewport override (P8.4)
await page.setViewportSize({ width: 414, height: 896 });

// CDP ağ emülasyonu (P8.4)
const cdp = await context.newCDPSession(page);
await cdp.send('Network.emulateNetworkConditions', {
  offline: false,
  latency: 150,                              // P8.3
  downloadThroughput: 1.6 * 1024 * 1024 / 8, // 1.6 Mbps
  uploadThroughput: 750 * 1024 / 8,          // 750 Kbps
});

// ❌ YASAK: P8'de doğrulanmamış API'ler (bildirim/push API'leri, paylaşma
//    Web Share API'si P8'de YOK) → kullanılırsa: // ⚠️ VERIFICATION REQUIRED
```

### §5.5 REPORT (Seviye 4)

| Alan | İçerik |
|------|--------|
| Ortam | Tarayıcı+sürüm `⚠️ VERIFICATION REQUIRED`, viewport, throttling |
| Metrik | FCP/LCP/INP/CLS vs P8.1 |
| Kısıt kanıtları | Çocuk ret (adım 10–12) · yetişkin mesaj ❌ (8) · yetişkin akış ❌ (9) ekran linkleri |
| KVKK | Veli onayı `⚠️ DERIVED` notu |
| Etiket dökümü | §6.5 |

### §5.6 Hata Modları

| # | Hata Modu | Tipik Neden | Müdahale |
|---|-----------|-------------|----------|
| 1 | Çocuk sosyali açılıyor | Sunucu tarafı kısıt yok | **CRITICAL** → Security + KVKK |
| 2 | Yetişkin kısıtı sessiz boşluk | Red UI yok | UI + mapping ihlali |
| 3 | Derin bağlantı bypass | Yalnız UI gizleme | **CRITICAL** → Security |
| 4 | Bildirim susturma etkisiz | Idempotence yok | Backend |
| 5 | Akışta CLS > 0.1 | Yer tutucu yok | UI |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] Frontmatter 7 alan
- [ ] Zorunlu 7 wiki-link
- [ ] §1–§7 + §4.0
- [ ] Adım tablosu ≥ 10 satır, 6 sütun (§5.3 → 15)
- [ ] Çocuk ret adımı + KVKK/veli adımı mevcut (10–11)
- [ ] Yetişkin çift red (mesaj + akış) mevcut (8–9)
- [ ] Eşikler P5/P8/P6 etiketli; kısıtlar mapping kaynaklı
- [ ] ≥ 500 satır; mojibake 0; BOM yok
- [ ] log.md append-only giriş

### §6.2 WCAG Kriter Durum Tablosu (boş — test sonunda doldurulur)

| Kriter | Adım(lar) | Durum | Kanıt |
|--------|-----------|-------|-------|
| 1.4.3 | 4, 8, 9, 10 | ⬜ | — |
| 1.4.11 | 3 (paylaş ikonu) | ⬜ | — |
| 1.4.13 | 3 (hover ipucu varsa) | ⬜ | — |
| 2.4.7 | 3, 7, 14 | ⬜ | — |
| 2.4.11 | 14 | ⬜ | — |
| 2.5.7 | 5 (akışta sürükleme varsa) | ⬜ | — |
| 2.5.8 | 6, 11, 13 | ⬜ | — |
| 3.2.6 | 7 (yardım konumu) | ⬜ | — |
| 3.3.7 | 6 (giriş tekrarı yok) | ⬜ | — |
| 3.3.8 | — (auth yok) | N/A | — |

### §6.3 EXCLUDED Listesine Atıf (research-bank §6.4)

Kapsam/41-55-46 sayı iddiaları kullanılmaz; `EXCLUDED` kayıtlar atlanır.

### §6.4 Quality Report (Bu Dosyanın Kendisi)

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Bölüm | §1–§7 |
| Blok | SOS-001 … SOS-010 (10) |
| Adım | 15 (§5.3) |
| Kısıt red'i | 3 (çocuk · yetişkin mesaj · yetişkin akış) |
| Status | Red Team · Human Mode · Truth Mode |

### §6.5 Bu Dosyadaki ⚠️ Etiketleri (Toplam)

**⚠️ VERIFICATION REQUIRED — 12 adet:**

| # | Konu | Kaynak |
|---|------|--------|
| 1 | Moderasyon/raporlama SLA'sı | §2.3 |
| 2 | Takipçi sınır değerleri | §2.3 |
| 3 | Bildirim frekansı eşiği | §2.3 / mapping §4.4-S5 |
| 4 | İşbirlikçi özelliklerin S6 kapsamı | §3.1 (SOS-004) |
| 5 | 1.3.1 bilgi ilişkisi numarası P5.5'te tek tek açılmadı | P5.5 |
| 6 | Yaş kilidi 16 | mapping §4.8 |
| 7 | Persona sayı çelişkileri (17/12/5 — çözülmedi) | mapping §1.3/§4.3 |
| 8 | 41/55/46 sayıları | mapping §5.3 |
| 9 | Tarayıcı sürüm envanteri | P8.7 |
| 10 | Oturum zaman aşımı 30 dk | mapping §4.6 |
| 11 | Retry-After 60 sn | mapping §4.6 |
| 12 | RFC 5322 e-posta doğrulama | mapping §4.6 |

**⚠️ DERIVED — 3 adet:**

| # | Konu | Kaynak |
|---|------|--------|
| 1 | 16 yaş altı → veli onayı (politika sonucu) | P6.4 |
| 2 | Çocuk sosyal kısıtı = KVKK uygulama politikası (ADR-088+'a taşınabilir) | P6.5 |
| 3 | Persona'ya özel metrik hedefi | P8.7 |

---

## §6.6 Test Çalıştırma Kontrol Listesi & Test Koşusu Şablonu

### §6.6.1 Çalıştırma Öncesi (Run Gate)

| # | Kontrol | Kaynak |
|---|---------|--------|
| 1 | mapping §4.3 kısıt matrisi okundu mu? | mapping |
| 2 | 4 test hesabı hazır mı? (çocuk, genç, yetişkin×2) | §5.2-P4 |
| 3 | mapping §4.4-S6 beklentileri okundu mu? | mapping |
| 4 | Viewport + throttling seçildi mi? | P8.5/P8.3 |
| 5 | Kısıt ekranlarının ekran görüntüsü planı hazır mı? | §5.5 rapor |
| 6 | P6 veli notu (`⚠️ DERIVED`) + KVKK red metni hazır mı? | P6.4 |
| 7 | P5.2 kriter listesi + P8 eşikleri okundu mu? | P5/P8 |
| 8 | §4.0 blok korunuyor mu? | Guardrail #16 |

### §6.6.2 Test Koşusu — [TARIH]

**Ortam:** Tarayıcı+sürüm `⚠️ VERIFICATION REQUIRED` · throttling 150 ms/1.6/0.75 · viewport §3.4 · Seviye 1–4.

**Metrik Sonuçları:** FCP ≤ 1 sn · LCP ≤ 2500 ms · INP ≤ 200 ms · CLS ≤ 0.1 · TBT ≤ 50 ms → ölçüm/geç-kal.

**Kısıt Kanıtları:** Çocuk ❌ (10–12) · Yetişkin mesaj ❌ (8) · Yetişkin akış ❌ (9) · Genç ✅ (5–7).

**Sonuç:** `[GEÇ / KAL]` — neden + eskalasyon (§5.6).

---

## §6.7 Ek Doğrulama Dayanakları (research-bank P8 — `VERIFIED`)

- **FCP iyi eşiği 1 saniye** — Lighthouse v8 score eğrisi bu eşiğe hizalıdır; §6.6.2'deki "FCP ≤ 1 sn" hedefinin dayanağıdır. `Kaynak: [k1] + [k2]` (web.dev — "existing FCP good threshold is 1 second" + github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz alınır). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Ağ throttling preset adı:** "Slow 4G" (eski adı "Fast 3G") ≈ %85 persentil mobil bağlantı, paket kaybı yok; DevTools preset'leri = Slow 3G · Fast 3G · Slow 4G · Fast 4G (+ özel profil). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/throttling.md + developer.chrome.com/docs/devtools/network/reference — research-bank P8.6 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; persona'ya özel metrik hedefi bu dosyaya yazılmaz (kapsam sınırı: §6.5 "Persona'ya özel metrik hedefi" kaydı). `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

## §7 Referanslar

### §7.1 Wiki-link Referanslar

| Bağlantı | Rol |
|----------|-----|
| [[personas/index]] | Persona ana indeksi |
| [[personas/methodology]] | Test metodolojisi |
| [[personas/research-bank]] | P5/P6/P8 eşik kaynağı |
| [[personas/test-scenarios-mapping]] | Grup × senaryo matrisi |
| [[personas/persona-template]] | Persona alan şablonu |
| [[ADR-023-persona-driven-testing]] | Karar + coverage gate |
| [[personas/test-senaryolari/a11y-erisilebilirlik]] | Kardeş senaryo (a11y derinliği) |

### §7.2 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yapan |
|-------|-------|-----------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — eski iskelet (468 satır) mapping §4.3 kısıtları + P5/P6/P8 ile yeniden bağlandı; 15 adım, 10 blok, 3 kısıt red'i + KVKK adımı | Vault Documentation Specialist |
