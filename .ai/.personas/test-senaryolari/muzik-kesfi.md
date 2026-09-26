---
title: "CoreMusic — Test Senaryosu: Müzik Keşfi (MZK)"
type: test-scenario
category: testing
version: 1.0.0
status: active
authority: "Test Senaryosu — SSOT: personas/test-senaryolari/muzik-kesfi"
updated: 2026-09-26
---

# CoreMusic — Test Senaryosu: Müzik Keşfi (MZK)

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/methodology]] · [[personas/research-bank]] · [[personas/test-scenarios-mapping]] · [[personas/persona-template]] · [[ADR-023-persona-driven-testing]] · [[personas/test-senaryolari/a11y-erisilebilirlik]]

---

| Alan | Değer |
|------|-------|
| Dosya Adı | `muzik-kesfi.md` |
| Dosya Yolu | `.ai/.personas/test-senaryolari/muzik-kesfi.md` |
| Dosya Tipi | Test senaryosu (6 senaryodan 1'i) — **karar DEĞİLDİR** (ADR değil) |
| Hedef Kitle | QA Engineer (birincil), UI Designer (arama/filtre UI), Vault Steward (denetim) |
| Yapı | H1 + Zorunlu Bağlantılar + §1 Amaç → §7 Referanslar (7 bölüm + 7 alanlı frontmatter) |
| ADR-023 Karşılığı | 20 persona matrisi **satır 18 — Müzik Keşfi kişisi** (§2.2a; kaynak: eski vault `test-senaryolari/muzik-kesfi.md`) |
| Mapping Karşılığı | **S2 Music Discovery** (68/68 uygulanabilir; çocukta içerik filtresi) — `[[personas/test-scenarios-mapping]]` §4.2 |
| Test Seviyeleri | Seviye 1 AI Rol · Seviye 2 Browser MCP · Seviye 3 Playwright · Seviye 4 Rapor (`[[personas/methodology]]` §2.2) |
| Eşik Kaynağı | `[[personas/research-bank]]` **P8 (metrik)** + **P5 (WCAG)** + **P6 (KVKK/yaş — veli onayı `⚠️ DERIVED`)** |
| Adım Sayısı | **15** (asgari 10 — §5.3) |
| Test Blokları | MZK-001 … MZK-011 (11 blok — §3.1) |
| Zorunlu Blok | §4.0 Şablon-Önce Kural Bloğu (silinemez) |
| Eski Kaynak | `coremusic.net.old/.ai/personas/test-senaryolari/muzik-kesfi.md` (568 satır, **salt okunur**) — iskelet alındı, P8/P5/P6 ile yeniden bağlandı |
| Kayıt Kuralı | Değişiklik Geçmişi **append-only** — mevcut satıra dokunulmaz |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya/klasör adı ASCII |
| Authority | `Test Senaryosu — SSOT: personas/test-senaryolari/muzik-kesfi` |
| Governance | Red Team · Human Mode · Truth Mode |
| Versiyon | 1.0.0 (ilk üretim — 2026-09-26) |

---

## §1 Amaç

Bu dosya, CoreMusic'in **müzik keşfi test senaryosudur**: metin arama, sesli arama, tür gezinme, sanatçı/album sayfaları, öneri algoritması, listeler, radyo/autoplay ve grup bazlı keşif davranışlarının (çocuk içerik filtresi, genç trend akışı, yetişkin gelişmiş filtre) hangi adımlarla, hangi `VERIFIED` eşiklerle test edileceğini tanımlar. Eski vault'taki 568 satırlık senaryo **kopyalanmamış**; iskelet alınıp `[[personas/research-bank]]` P8/P5/P6 ile **yeniden bağlanmıştır** (ADR-005 Zero Hallucination).

| Boyut | Değer |
|-------|-------|
| Ne taşır | MZK test blokları, ≥10 adımlık adım tablosu (Adım · Eylem · Beklenen · Doğrulama · Metrik · WCAG), KVKK/veli onayı adımı, çocuk içerik filtresi, mood eşlemesi (Arabesk/Dans türevleri S2 birincil), Playwright kod bloğu |
| Ne taşımaz | Eşiklerin kaynağı (→ research-bank P8/P5), KVKK hukuki yorumu (→ P6 + gerekirse ADR-088+), test akışı (→ methodology), grup matrisi (→ mapping), karar (→ ADR-023) |
| Neden yazıldı | Eski senaryo research-bank'tan **önce** yazılmıştı → eşikler/filtre iddiaları P8/P5/P6 ile yeniden bağlandı |
| Kim okumalı | Keşif/arama testi çalıştıran her ajan (QA birincil) |
| Kanıt zinciri | `research-bank` P6/P8/P5 → `methodology` → persona dosyası → **bu dosya** → `ADR-023` (gate) |

### §1.1 Bu Dosya Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Dosya | Bu Dosya Kullanılmaz |
|---------|-------------|----------------------|
| Keşif/arama/öneri test adımları | ✅ Bu dosya | — |
| Metrik eşik (LCP/INP/CLS…) kaynağı | `[[personas/research-bank]]` P8 | ❌ (taşınır) |
| WCAG kriter numarası/eşiği | `[[personas/research-bank]]` P5 | ❌ (taşınır) |
| 16 yaş altı veli onayı politikası | `[[personas/research-bank]]` P6.4 (`⚠️ DERIVED`) | ❌ (taşınır) |
| Test seviyeleri/akış | `[[personas/methodology]]` | ❌ |
| Grup × senaryo kısıtları | `[[personas/test-scenarios-mapping]]` §4.4-S2 | ❌ |
| Coverage gate kararı | `[[ADR-023-persona-driven-testing]]` | ❌ |

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Metin arama: debounce, yazarken sonuç, temizleme, boş sonuç | Arama motoru altyapısı/k indeksi (→ backend) |
| Tür filtresi (çoklu seçim), sanatçı/album gezinme | İçerik lisans/katalog hakları (→ hukuk) |
| Öneri algoritması davranışı (yenile, mood-kişiselleştirme) | Algoritma tasarımı kararı (→ brain/ADR) |
| Listeler/charts, radyo/autoplay davranışı | Streaming protokolü (→ architecture/k15-medya-streaming) |
| Çocuk keşfi: explicit kapalı, onaylı türler, veli onayı | Veli onayının hukuki yorumu (→ P6 + ADR-088+) |
| Grup bazlı keşif davranışları (çocuk/genç/yetişkin) | Persona içerik üretimi |
| Keşif akışında performans (FCP/LCP/INP) ve WCAG doğrulaması | Derin a11y denetimi (→ `[[personas/test-senaryolari/a11y-erisilebilirlik]]`) |

### §2.2 Hedef Kitle

| Persona | Bu senaryoda rolü |
|---------|-------------------|
| QA Engineer (`qa`) | Birincil yürütücü |
| UI Designer (`ui`) | Filtre/arama UI doğrulama |
| Security Engineer (`security`) | Çocuk filtresi/veli onayı akış denetimi (ikincil) |
| Vault Steward | Denetim — §6 envanter |

### §2.3 Kapsam Dışı İstisnalar

| # | İstisna | Nereye Gider |
|---|---------|--------------|
| 1 | Sesli arama ASR doğruluğu/kelime hatası oranı | `⚠️ VERIFICATION REQUIRED` (P8'de doğrulanmış ölçüm yok) |
| 2 | "Fuzzy arama" eşik değeri (kaç karakter typo toleransı) | `⚠️ VERIFICATION REQUIRED` |
| 3 | BPM aralığı iddiaları (60–100 / 120–160 vb.) | `[[personas/mood-taxonomy]]` §3.4 — `⚠️` işaretli |

---

## §3 Mimari — Senaryo Yapısı

### §3.0 Dizin ve Bağımlılık Ağacı

```
.ai/.personas/test-senaryolari/muzik-kesfi.md          ← bu dosya (SSOT: senaryo)
├── [[personas/research-bank]] P6.3/P6.4               (yaş/veli onayı — VERIFIED + DERIVED)
├── [[personas/research-bank]] P8.1–P8.5               (metrik — VERIFIED)
├── [[personas/research-bank]] P5.2                    (WCAG kriter numaraları)
├── [[personas/methodology]] §2.2, §3.3, §3.4          (seviye, emülasyon, eşik kullanımı)
├── [[personas/test-scenarios-mapping]] §4.4-S2        (grup beklentileri)
├── [[personas/mood-taxonomy]] §3.4                    (Arabesk/Dans türev küme adları)
└── [[ADR-023-persona-driven-testing]] satır 18        (eşleme + coverage gate)
```

### §3.1 Test Blokları (MZK-001 → MZK-011)

| # | Blok | Kapsam | Birincil Kriter / Not |
|---|------|--------|----------------------|
| MZK-001 | Metin Arama | Debounce, yazarken sonuç, temizle (×), boş sonuç durumu | 2.4.7 · 4.1.3 `⚠️` |
| MZK-002 | Sesli Arama | Mikrofon izni, sonuç ekranı, hata/iptal | `⚠️ VERIFICATION REQUIRED` (§2.3) |
| MZK-003 | Tür Bazlı Keşif | Çoklu filtre, filtre birikimi, animasyon akıcılığı | 1.4.11 · 2.5.7 |
| MZK-004 | Sanatçı Sayfası | Diskografi, alfabetik sıralama, sayfalama | 2.4.7 · 3.2.6 |
| MZK-005 | Albüm Sayfası | Parça listesi, oynatma akışına geçiş | 2.5.8 · 1.4.3 |
| MZK-006 | Öneri Algoritması | "Tadına göre", mood-kişiselleştirme, yenile butonu | INP ≤ 200 ms |
| MZK-007 | Charts/Listeler | Sıralama, güncelleme, kısayollar | CLS ≤ 0.1 |
| MZK-008 | Radyo/Autoplay | Otomatik devam, oturum sonunda durma davranışı | 1.4.13 `⚠️` (autoplay kuralı) |
| MZK-009 | Çocuk Keşfi | Explicit kapalı, yalnız onaylı türler, büyük kapak (200px+ ⚠️), veli onayı | KVKK `⚠️ DERIVED` · 2.5.8 |
| MZK-010 | Genç Keşfi | Trend listeleri, mood önerisi, yaş kilidi 16 ⚠️ | mapping §4.4-S2 |
| MZK-011 | Yetişkin Keşfi | Gelişmiş filtre (yıl/süre/BPM ⚠️), fuzzy arama ⚠️ | 2.4.7 · 3.3.7 |

> ADR-023 satır 18 eşlemesi; mapping §4.4-S2 "Mood örnekleri" sütunundaki küme adları `[[personas/mood-taxonomy]]` §3.4'ten alınır (uydurulmaz).

### §3.1.1 Blok Detayları (Açıklama — Başarı Kriteri)

**MZK-001 — Metin Arama:** Kutu görünür, 300 ms debounce (mapping §4.6-S2), yazarken sonuç gelir, temizle (×) çalışır. Başarı: boş sorguda anlamlı boş-durum ekranı; sonuç sayısı art arda sorgularda tutarlı.

**MZK-002 — Sesli Arama:** Mikrofon izni istenir/ret edilir; ret sonrası klavye aramasına yönlendirme vardır. Başarı: izin reddi çıkmaz sokak değildir. ASR doğruluğu ölçülmez → `⚠️ VERIFICATION REQUIRED`.

**MZK-003 — Tür Bazlı Keşif:** Tüm türler seçilebilir, çoklu seçim destekli, animasyon jank'sız (mapping §4.6-S2). Başarı: filtre seçimi arama sorgusunu bozmaz; filtre temizleme geri alınabilir (Kaşif tipik hata modu: "filtre kaybı, geri dönüş korumaz" — mapping §4.7).

**MZK-004/005 — Sanatçı/Albüm:** Alfabetik sıralama, grid duyarlı, "daha fazla yükle/sayfalama". Başarı: sayfalama sonrası odak korunur; parça listesinde hedefler ≥ 24×24 px.

**MZK-006 — Öneri:** "Tadına göre" bölümü, mood-kişiselleştirmesi, yenile butonu. Başarı: yenile sonrası liste değişir ve CLS ≤ 0.1 (yer tutucu korunur).

**MZK-007 — Charts:** Liste sıralaması okunabilir, güncelleme zamanı görünür (`⚠️ VERIFICATION REQUIRED` — güncelleme sıklığı iddiası kaynaksız).

**MZK-008 — Radyo/Autoplay:** Oynatma sırası devam eder; sekme kapatma/oturum sonunda durur. Başarı: kullanıcının kontrolü (durdur/atla) her zaman erişilebilir; otomatik oynatma bildirimi görünür (1.4.13 gibi içerik görünürlüğü — numara P5.5 kapsamında `⚠️ VERIFICATION REQUIRED`).

**MZK-009 — Çocuk (KVKK):** Explicit içerik kapalı; yalnız onaylı türler listelenir; büyük kapak (200px+ `⚠️ DERIVED`, eski vault iddiası); veli onayı akışı görünür. **KVKK/veli onayı adımı zorunlu** (§5.3 adım 12): P6.4'e göre 16 yaş altı = veli onayı `⚠️ DERIVED` (GDPR m.8=16, TMK m.11=18, COPPA=13; "KVKK 16 diyor" **YANLIŞTIR** — P6.5).

**MZK-010 — Genç:** Trend listeleri + mood-kişiselleştirilmiş öneri; yaş kilidi ekranı 12–17 aralığında kademeli ⚠️. Başarı: öneri motoru yaşa göre filtre uygular (id dia `⚠️ VERIFICATION REQUIRED`).

**MZK-011 — Yetişkin:** Gelişmiş filtre (yıl, süre, BPM — BPM `⚠️ VERIFICATION REQUIRED`), typo ile arama (fuzzy `⚠️ VERIFICATION REQUIRED`), filtre birikimi. Başarı: filtreler birikir; temizleme tek adımda.

### §3.1.2 Edge Case'ler

| # | Edge Case | Beklenen |
|---|-----------|----------|
| 1 | Boş sonuç + filtre aktif | "Sonuç yok" + filtre temizleme çağrısı görünür |
| 2 | Aşırı hızlı yazma (debounce) | Son istek geçerli; eski yanıt UI'ı ezmez (INP korunur) |
| 3 | 1 karakterlik sorgu | Boş-durum veya minimum uzunluk mesajı |
| 4 | Çocukta explicit sorgu yazma | Sonuç verilmez/filtrelenir + açıklayıcı durum |
| 5 | Veli onayı iptal edilir | Keşif kısıtlı modda kalır; hesap değişmez |
| 6 | Ağ kesintisi sırasında arama | Hata durum mesajı; eski sonuçlar bayat olarak işaretlenir |

### §3.1.3 Mood → Keşif Öncelik Çaprazı (mapping §4.5/§4.7 — taşınan)

| Küme | Etkilediği senaryo | Öncelikli kontrol (bu senaryoda) |
|---|---|---|
| Arabesk/Dans türevi (8) | S2 birincil | Tür filtresi + küme tutarlı öneri (§3.3) |
| Kaşif (5) | S2 birincil | Filtre kaybı yok; geri dönüş korumazsa FAIL |
| Romantik (7) | S2 birincil | Öneri kalitesi + gece modu tercihi |
| Melankolik (5) | S2 ikincil | Düşük kontrast tercihi ihmal edilmez |
| Enerjik (13) | S2 ikincil | Öneri yenileme animasyonu jank'sız |

**Öneri kalitesi ölçümü (bu dosyanın iddiası değil):** "öneri kalitesi" nicel eşiği research-bank'ta **YOKTUR** → herhangi bir başarı yüzdesi/“doğru öneri oranı” yazılacaksa `⚠️ VERIFICATION REQUIRED` (§6.5'e eklenir; bu dosyada sayısal öneri eşiği **yazılmamıştır**).

### §3.2 WCAG Kriter — Eşik Tablosu (research-bank P5 — `VERIFIED`)

| Kriter | Başlık | Eşik / Değişken | Durum |
|--------|--------|-----------------|-------|
| 1.4.3 | Contrast (Minimum) | Metin ≥ 4.5:1; büyük metin ≥ 3:1 | `VERIFIED` |
| 1.4.11 | Non-text Contrast | UI bileşenleri/grafikler ≥ 3:1 | `VERIFIED` |
| 1.4.13 | Content on Hover or Focus | AA | `VERIFIED` |
| 2.4.7 | Focus Visible | AA | `VERIFIED` |
| 2.4.11 | Focus Not Obscured (Minimum) | AA — WCAG 2.2 **YENİ** | `VERIFIED` |
| 2.5.7 | Dragging Movements | AA — WCAG 2.2 **YENİ** | `VERIFIED` |
| 2.5.8 | Target Size (Minimum) | ≥ 24×24 CSS px (5 istisna) | `VERIFIED` |
| 3.2.6 | Consistent Help | A | `VERIFIED` |
| 3.3.7 | Redundant Entry | A | `VERIFIED` |
| 3.3.8 | Accessible Authentication (Minimum) | AA — WCAG 2.2 **YENİ** | `VERIFIED` |

### §3.3 Persona / Mood Eşlemesi (`[[personas/test-scenarios-mapping]]`)

| Grup (n) | S2 beklentisi (mapping §4.4) | Doğrulama yöntemi |
|---|---|---|
| Çocuk (29) | Explicit kapalı, yalnız onaylı türler, büyük kapak (200px+ ⚠️) | Filtre çıktısı denetimi |
| Genç (29) | Trend listeleri, mood-kişiselleştirilmiş öneri | Öneri yenileme + sonuç sayısı |
| Yetişkin (10) | Gelişmiş filtre (yıl, süre, BPM ⚠️), fuzzy arama | Typo ile arama + filtre birikimi |

Mood çaprazı (mapping §4.7): **Arabesk/Dans türevi (8) → birincil S2**; Kaşif (5) → S2 birincil (tipik hata: filtre kaybı); Romantik (7) → S2; Melankolik (5) → ikincil S2. Küme adları: Arabesksever · Danssever · Arabesk Kaşif · Arabesk Meraklı · Dans Sporcu · Arabesk Melankolik · Dans Enerjik (`[[personas/mood-taxonomy]]` §3.4).

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
| Ağ gecikmesi | **150 ms** | P8.3 |
| Download / Upload | **1.6 Mbps / 750 Kbps** | P8.3 |
| TBT | **> 50 ms** long task bloklar | P8.3 |
| Cihaz registry | `playwright.devices` alanları (userAgent, screenSize, viewport, hasTouch, deviceScaleFactor, isMobile, colorScheme, locale, timezoneId) | P8.4 |
| Viewport override | `page.setViewportSize()` | P8.4 |
| Offline | `use: { offline: true }` | P8.4 |
| CDP ağ | `Network.emulateNetworkConditions` | P8.4 |

### §3.5.1 Persona-Spesifik Notlar (Grup Bazlı)

| Grup | Keşif davranışı notu | Doğrulanacak şey | Kısıt kaynağı |
|---|---|---|---|
| Kız çocuk (Elifsu — Arabesk Kaşif) | Arabesk + çocuk keşif karışımı; aile paylaşımı | Onaylı tür filtresi arabesk içeriyor mu? Küme tutarlılığı | mood-taxonomy §3.4 (21) |
| Kız çocuk (Mina — Dans Enerjik) | Yüksek tempolu keşif, büyük dokunma hedefi | 48px+ hedef ⚠️; BPM filtresi çocukta **görünmez** | mapping §4.4-S2 |
| Erkek çocuk (Yiğit Can — Arabesk Meraklı) | Ortak dinleme/keşif; ebeveyn kontrolü | Ortak oturumda keşif geçmişi veliye görünür mü? | mood-taxonomy §3.4 (22) |
| Erkek çocuk (Egehan — Dans Sporcu) | BPM bazlı öneri beklentisi çocukta sadeleşir | BPM filtresi çocuk UI'ında yok (yalnız onaylı türler) | mapping §4.4-S2 |
| Genç kız (Zeliha — Arabesksever) | Duygusal UI, slow tempo, vokal öncelikli öneri | Öneri kümeyle tutarlı (§3.1.3) | mood-taxonomy §3.4 (19) |
| Genç kız (Buse — Danssever) | Hızlı navigasyon, yüksek BPM filtresi | Filtre + öneri eşleşmesi | mood-taxonomy §3.4 (20) |
| Genç erkek (Furkan — Arabesk Melankolik) | Gece modu/slow geçişler | Gece modu tercihi keşifte korunur (Melankolik notu) | mapping §4.5 |
| Genç erkek (Atakan — Dans Enerjik) | Workout playlist, yüksek BPM | Öneri yenileme animasyonu jank'sız (INP) | mapping §4.7 |
| Yetişkin kadın/erkek | Gelişmiş filtre + fuzzy arama | Adım 9–10 | mapping §4.4-S2 |

> Persona adları `[[personas/index]]` altındaki persona dosyalarından alınmıştır; bu dosyada persona **üretilmez**. Kişisel veri niteliğinde ayrıntı (sağlık vb.) yazılmaz (`[REDACTED]` — §4.7).

---

## §4 Kurallar

### §4.0 ZORUNLU: ŞABLON ÖNCE (Template-First)

Bu dosya `.ai/.templates/documentation/docs-md-template.md`'ye göre yazılmıştır; bu blok **silinemez**. Yeni MZK bloğu eklenirken önce şablon, sonra §4.x okunur (Guardrail #16).

### §4.1 Bağlayıcı Kurallar

| # | Kural |
|---|-------|
| 1 | Eşikler research-bank'tan **taşınır**; burada uydurulmaz |
| 2 | `VERIFIED` ↓ `⚠️` olur; `⚠️` ↑ `VERIFIED` **olmaz** (ADR-005) |
| 3 | Frozen ADR'ler (001–037) değiştirilmez |
| 4 | Dosya adı değişikliği onay gerektirir |
| 5 | Her değişiklik `log.md`'ye append-only |

### §4.2 Frontmatter — 7 Zorunlu Alan

`title` · `type: test-scenario` · `category: testing` · `version` · `status` · `authority` · `updated` — eksik alan dosyayı geçersiz kılar.

### §4.3 Dil, Kod Adı ve Mojibake (UTF-8)

Türkçe karakterler bozuk yazılamaz; yazım yalnız `vault-utf8-writer.mjs` ile; PowerShell yazma cmdlet'leri **YASAK**.

### §4.4 Wiki-Link ve Bağlantı Formatı

`[[relative/path/to/file]]` — zorunlu 7 bağlantı H1 altında (§7.1).

### §4.5 Kaynak Etiketleme — Research-Bank Durum Aktarımı (ADR-005)

| Durum | Bu Dosyada |
|-------|-----------|
| `VERIFIED` | Taşınır + P# kaynağı |
| `SINGLE-SOURCE` / `CONFLICT` | Notuyla taşınır |
| `⚠️ DERIVED` | Türetme olduğuyla taşınır (ör. veli onayı) |
| `⚠️ VERIFICATION REQUIRED` | Aynen taşınır; doğrulanmadan iddia edilmez |

### §4.6 Derinlik Standardı (500+)

Her senaryo ≥ 500 satır; ekleme sonrası §6.1 tekrar çalıştırılır.

### §4.7 REDACTED, KVKK ve Sür Politikası

Gerçek kişi/anahtar verisi yazılmaz (`[REDACTED]`); **16 yaş altı veli onayı `⚠️ DERIVED`** olarak taşınır (P6.4); "KVKK 16 diyor" ifadesi yasaktır (P6.5); dosya adı değişmez.

### §4.8 Yasak / Doğru Tablosu

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| Eşik uydurmak | P8/P5 değerleri aynen |
| Veli onayını "KVKK 16" diye gerekçelendirmek | GDPR m.8=16 + TMK 18 + COPPA 13 → `⚠️ DERIVED` |
| Sesli arama doğruluğu iddiası | `⚠️ VERIFICATION REQUIRED` |
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
| P1 | P8 eşikleri + P5 kriterleri oku | Eşik listesi |
| P2 | P6.3/P6.4/P6.5 oku (yaş/veli onayı) | KVKK notu `⚠️ DERIVED` |
| P3 | mapping §4.4-S2 + §4.7 oku | Grup/mood notları |
| P4 | Test hesabı seç (çocuk hesabı = 16 yaş altı) | Hesap listesi |
| P5 | Viewport + throttling seç (§3.4/§3.5) | Koşu profili |

### §5.3 Adım Tablosu (15 adım — asgari 10)

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Throttling uygula (150 ms, 1.6 Mbps/750 Kbps) | Ağ koşulu aktif | Network Conditions okunur | 150 ms · 1.6/0.75 Mbps (`VERIFIED`) | — |
| 2 | Keşif sayfasını yükle (cold) | FCP/LCP kaydedilir | Performance panel | FCP ≤ 1 sn · LCP ≤ 2500 ms (`VERIFIED`) | — |
| 3 | Arama kutusuna yaz (300 ms debounce) | Yazarken sonuç gelir | Sonuç sayısı değişimi | INP ≤ 200 ms (`VERIFIED`) | 2.4.7 |
| 4 | Sonuç vermek için temizle (×) | Liste sıfırlanır, odak kutuya döner | Odak konumu | — | 2.4.7 · 2.4.11 |
| 5 | Boş sonuç senaryosu üret | Boş-durum ekranı + filtre temizleme çağrısı | Ekran görüntüsü | — | 1.4.3 |
| 6 | Çoklu tür filtresi seç (4 tür) | Filtreler birikir; animasyon jank'sız | Sonuç+CLS | CLS ≤ 0.1 (`VERIFIED`) | 1.4.11 · 2.5.7 |
| 7 | Sanatçı sayfasına gir, alfabetik liste + sayfalama | Sıralama doğru; sayfalama sonrası odak korunur | DOM + odak | — | 2.4.7 · 2.5.8 |
| 8 | Öneri "yenile" butonuna bas | Liste değişir, yer tutucu kayması yok | CLS ölçümü | CLS ≤ 0.1 (`VERIFIED`) | — |
| 9 | Typo ile arama (yetişkin) | Sonuçlar döner **veya** "sonuç yok" net (`⚠️` fuzzy) | Sonuç sayısı | `⚠️ VERIFICATION REQUIRED` | 3.3.7 |
| 10 | BPM/yıl/süre filtresi (yetişkin) | Filtre birikimi; temizleme tek adım | Filtre durumu | BPM `⚠️ VERIFICATION REQUIRED` | 2.5.8 |
| 11 | Mood önerisi kontrolü (Arabesksever/Danssever) | Öneri kümeyle tutarlı (§3.3) | Öneri listesi | — | — |
| 12 | **KVKK/veli onayı adımı (çocuk hesabı)** — 16 yaş altı veli onayı iste | Onay akışı görünür; ret edilirse keşif kısıtlı | Ekran + e-posta `⚠️` | Veli onayı `⚠️ DERIVED` (P6.4) | 2.5.8 (büyük buton) |
| 13 | Çocukta explicit sorgu dene | Explicit sonuç **verilmez** | Filtre çıktısı | — | 1.4.3 (ret mesajı okunur) |
| 14 | 320×568 + 768×1024 viewport'ta keşif tekrarı | Menü/filtre kullanılabilir; hedef ≥ 24 px | Piksel ölçümü | 2 breakpoint (`VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 15 | Rapor (Seviye 4) — eşik karşılaştırmalı | Geç/kal raporu | §6.1 | P8.1 (`VERIFIED`) | §6.2 |

### §5.4 Playwright / CDP Kod Bloğu (P8 — `VERIFIED` API adlar)

```js
const { devices } = require('@playwright/test');

// Cihaz emülasyonu (P8.4 registry alanları)
const context = await browser.newContext({ ...devices['Pixel 5'] });
const page = await context.newPage();

// Viewport override (P8.4)
await page.setViewportSize({ width: 414, height: 896 });

// Çocuk akışı: offline testi (P8.4: use: { offline: true })

// CDP ağ emülasyonu (P8.4)
const cdp = await context.newCDPSession(page);
await cdp.send('Network.emulateNetworkConditions', {
  offline: false,
  latency: 150,                              // P8.3
  downloadThroughput: 1.6 * 1024 * 1024 / 8, // 1.6 Mbps
  uploadThroughput: 750 * 1024 / 8,          // 750 Kbps
});

// ❌ YASAK: P8'de doğrulanmamış API'ler (geolocation, ASR ölçüm API'si vb.)
// → kullanılırsa: // ⚠️ VERIFICATION REQUIRED
```

### §5.5 REPORT (Seviye 4)

| Alan | İçerik |
|------|--------|
| Ortam | Tarayıcı+sürüm `⚠️ VERIFICATION REQUIRED`, viewport, throttling |
| Metrik | FCP/LCP/INP/CLS vs P8.1 |
| Grup sonuçları | Çocuk (12–13), genç (10), yetişkin (9–11) adımları |
| Filtre sonucu | Çocuk explicit ret kanıtı |
| KVKK | Veli onayı akışının `⚠️ DERIVED` olduğu açıkça yazılır |
| Etiket dökümü | §6.5 |

### §5.6 Hata Modları

| # | Hata Modu | Tipik Neden | Müdahale |
|---|-----------|-------------|----------|
| 1 | Filtre kaybı (geri dönüş) | State yönetimi | UI düzeltme (Kaşif modu) |
| 2 | Öneri yenilemede CLS > 0.1 | Yer tutucu yok | UI düzeltme |
| 3 | Çocukta explicit sızıntı | Filtre sunucu tarafı yok | **CRITICAL** → Security+Backend |
| 4 | Veli onayı atlanabilir | Akış zorunlu değil | CRITICAL → Security + KVKK notu |
| 5 | Sonuç listesi INP > 200 ms | Uzun task | JS kırpması |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] Frontmatter 7 alan
- [ ] Zorunlu 7 wiki-link
- [ ] §1–§7 + §4.0 blok
- [ ] Adım tablosu ≥ 10 satır, 6 sütun (§5.3 → 15)
- [ ] KVKK/veli onayı adımı mevcut (§5.3/12)
- [ ] Eşikler P8/P5/P6 etiketli
- [ ] Playwright bloğu yalnız P8.4 API adları
- [ ] ≥ 500 satır; mojibake 0; BOM yok
- [ ] log.md append-only giriş

### §6.2 WCAG Kriter Durum Tablosu (boş — test sonunda doldurulur)

| Kriter | Adım(lar) | Durum | Kanıt |
|--------|-----------|-------|-------|
| 1.4.3 | 5, 13 | ⬜ | — |
| 1.4.11 | 6 | ⬜ | — |
| 1.4.13 | 8 (hover öğesi varsa) | ⬜ | — |
| 2.4.7 | 3, 4, 7 | ⬜ | — |
| 2.4.11 | 4 | ⬜ | — |
| 2.5.7 | 6 (sürükleme filtresi varsa) | ⬜ | — |
| 2.5.8 | 7, 14 | ⬜ | — |
| 3.2.6 | 5 (yardım konumu) | ⬜ | — |
| 3.3.7 | 9 | ⬜ | — |
| 3.3.8 | — (auth bu senaryoda değil) | N/A | — |

### §6.3 EXCLUDED Listesine Atıf (research-bank §6.4)

Kapsam/indirilebilirlik sayım iddiaları bu senaryoda kullanılmaz; `EXCLUDED` kayıtlar burada da atlanır (`[[personas/test-scenarios-mapping]]` §5.3 notu: 41/55/46 sayıları `⚠️ VERIFICATION REQUIRED`).

### §6.4 Quality Report (Bu Dosyanın Kendisi)

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Bölüm | §1–§7 |
| Blok | MZK-001 … MZK-011 (11) |
| Adım | 15 (§5.3) |
| KVKK adımı | 1 (§5.3/12) |
| Status | Red Team · Human Mode · Truth Mode |

### §6.5 Bu Dosyadaki ⚠️ Etiketleri (Toplam)

**⚠️ VERIFICATION REQUIRED — 10 adet:**

| # | Konu | Kaynak |
|---|------|--------|
| 1 | Sesli arama ASR doğruluğu ölçümü | §2.3 |
| 2 | Fuzzy arama tolerans değeri | §2.3 |
| 3 | BPM aralıkları (60–100 / 120–160 vb.) | mood-taxonomy §3.4 (`⚠️`) |
| 4 | Yaş kilidi 16 kademeli akış | mapping §4.8 |
| 5 | Charts güncelleme sıklığı | MZK-007 |
| 6 | Autoplay/hover içerik kuralı numarası (P5.5 açık değil) | P5.5 |
| 7 | Büyük kapak 200px+ hedefi | mapping §4.4 (`⚠️` değil — eski iddia → `⚠️`) |
| 8 | Tarayıcı sürüm envanteri | P8.7 |
| 9 | 41/55/46 kapsam sayıları | mapping §5.3 |
| 10 | "Öneri kalitesi" nicel eşiği (yok — sayısal iddia yazılamaz) | §3.1.3 |

**⚠️ DERIVED — 3 adet:**

| # | Konu | Kaynak |
|---|------|--------|
| 1 | 16 yaş altı → veli onayı (politika sonucu) | P6.4 |
| 2 | Persona'ya özel metrik hedefi yazılsa | P8.7 |
| 3 | "16 yaş altı keşif kısıtlaması" CoreMusic politika uygulaması | P6.5 (ADR-088+'a taşınabilir) |

---

## §6.6 Test Çalıştırma Kontrol Listesi & Test Koşusu Şablonu

### §6.6.1 Çalıştırma Öncesi (Run Gate)

| # | Kontrol | Kaynak |
|---|---------|--------|
| 1 | P8 eşikleri + P6 veli notu okundu mu? | P8/P6 |
| 2 | Çocuk test hesabı hazır mı? (16 yaş altı) | P6.4 `⚠️ DERIVED` |
| 3 | Viewport seti seçildi mi? (§3.4) | P8.5 |
| 4 | Grup kısıtları (mapping §4.4-S2) okundu mu? | mapping |
| 5 | §4.0 blok korunuyor mu? | Guardrail #16 |

### §6.6.2 Test Koşusu — [TARIH]

**Ortam:** Tarayıcı+sürüm `⚠️ VERIFICATION REQUIRED` · throttling 150 ms/1.6/0.75 Mbps · viewport seti §3.4 · Seviye 1–4.

**Metrik Sonuçları:** FCP ≤ 1 sn · LCP ≤ 2500 ms · INP ≤ 200 ms · CLS ≤ 0.1 · TBT ≤ 50 ms → ölçüm/geç-kal tablosu.

**Grup Sonuçları:** Çocuk adımları 12–13 (KVKK `⚠️ DERIVED`) · Genç adım 11 · Yetişkin adımları 9–10.

**Sonuç:** `[GEÇ / KAL]` — kalışta neden + eskalasyon (§5.6).

---

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
| 2026-09-26 | 1.0.0 | İlk üretim — eski iskelet (568 satır) P8/P5/P6 ile yeniden bağlandı; 15 adım, 11 blok, KVKK/veli adımı eklendi | Vault Documentation Specialist |
