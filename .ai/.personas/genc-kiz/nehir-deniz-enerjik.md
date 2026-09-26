---
title: "CoreMusic — Persona: Nehir Deniz"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-kiz/nehir-deniz-enerjik"
updated: 2026-09-26
group: genc-kiz
age: 16
mood: Enerjik
persona_id: CM-ND-16-ENR-MGL
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Nehir Deniz

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-kiz (13-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 16 yaşındaki bir genç kızın gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Enerjik / Sosyal), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-kiz (13-17) |
| Birincil mood | Enerjik ([[personas/index]] §6.3 ataması) |
| Test odağı | Hızlı navigasyon, anlık geri bildirim, skip performansı, paylaşım akışı |
| Veli onayı | `veli_onayı_gerekli: false` (16 yaş — politika eşiği, ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Mood-geçiş (ADR-023 satır 16) | Level 2 + 3 | Enerjik arayüz; geçersiz mood değeri = error |
| Hızlı navigasyon (ADR-023 satır 12) | Level 2 + 3 | Sık sekme geçişi + geri tuşu, anlık geri bildirim |
| Paylaşım → yayılım (ADR-023 satır 20) | Level 2 + 3 | QR / link paylaşımı, 280 karakter sınırı |
| Playlist oluşturma & iş listeleri | Level 2 + 3 | Tema bazlı playlist, crossfade, kesintisiz çalma |
| Erişilebilirlik denetimi | Level 2 | 1.4.3 ≥4.5:1, 2.5.8 ≥24×24 px denetimi |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.2.2 + §3.2.7 | Küme adı + tanım alıntısı (Enerjik / Sosyal) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Nehir Deniz | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 16 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 19 Temmuz 2010 | Kaynak: kurgusal (persona verisi; 2026-09-26 itibarıyla yaş 16 ile tutarlı) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Muğla / Bodrum (yaz ayları merkez) | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Bodrum Anadolu Lisesi, 11. sınıf; yazın aile butik otelinde çalışır | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Yaya + aile aracı (ev-iş ~10 dk, kurgu) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | nehir.mavi | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-ND-16-ENR-MGL` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `ND` | Ad + soyad ilk harfleri (Nehir Deniz → N, D) |
| `YY` | `16` | Yaş 2 hane (16 → `16`; şablon örnek aralığı 10-99'a uyar) |
| `ZZZ` | `ENR` | Mood tür kodu (Enerjik → ENR) |
| `XXX` | `MGL` | Şehir kodu (Muğla → MGL; Bodrum ilçesi, il kodu kullanılır) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş ve üzeri):** Bu persona 16 yaşındadır → frontmatter'de `veli_onayı_gerekli: false`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). CoreMusic politikası 16 yaş altı için veli onayı ister; 16 → `false` politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek kişi verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (makyaj, aksesuar, kıyafet listesi…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 170 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 56 kg, uzun ve atletik yapı (yüzme / plaj voleybolu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Doğal sarı-kumral dalgalı saç, mavi-yeşil karışık göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (görme ve işitme iyi — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Beyaz-mavi keten/pamuk yaz stili; profil fotoğrafında plaj ışığı (renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Güneş altında parlak ekran kullanımı yoğun → yüksek parlaklık/glürlük testi adayı; kısıt (görme/işitme) yok | Kaynak: kurgusal (persona verisi); kontrast kriteri: `Kaynak: [k1] + [k2]` (P5.4) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 90 | Turizm çocuğu: herkesle konuşur, teknede DJ'lik yapar, kalabalıkta enerjisi artar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 70 | Naziktir ama gerektiğinde sınır koyar — müşteri ilişkilerinden öğrendiği bir beceri. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 80 | Otelde resepsiyon yardımı yapar, iş disiplini vardır; okul görevlerini aksatmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 90 | **Eksen belirsizliği:** eski kaynakta "Duygusal Denge 9/10 (dengeli, olgun)" yazar → ×10 = 90. N (nörotisizm) ekseninde ters kodlama varsa bu puan **düşük** denge anlamına gelir; iki okuma da uygulanabilir → `⚠️ VERIFICATION REQUIRED` (bkz. §7.2). | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120; eksen: `⚠️ VERIFICATION REQUIRED` |
| Deneyime Açıklık | O | 80 | Farklı kültürlerden insanlarla büyüdü, yeni türleri ve yabancı sanatçıları açık fikirle dener. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Dönüşüm yöntemi | Eski 10'luk puan × 10 (doğrusal); dönüşümün kendisi `⚠️ DERIVED` — ölçek eşdeğerliği iddia edilmez |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Enerjik |
| **İkincil küme** | Sosyal ([[mood-taxonomy]] §3.2.7) |
| **Küme tanımı (alıntı)** | "Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici" ([[mood-taxonomy]] §3.2.2) |
| **Tetikleyici durumlar** | Hızlı geri bildirim / canlı renkli kartlar (yaklaşma); yavaş yükleme ve reklam kesintisi (geri çekilme) |
| **UI etkisi** | "Canlı renkler, hızlı animasyonlar, yüksek kontrast"; test etkisi: "Hızlı navigasyon, anlık geri bildirim, skip performansı; ADR-023 satır 12 (hızlı navigasyon + geri tuşu)" ([[mood-taxonomy]] §3.2.2) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Enerjik); ikincil = eski salt-okunur persona kaynağı (Sosyal) — §7.2'de kayıtlı |
| Test etkisi | ADR-023 satır 12 (hızlı navigasyon + geri tuşu) + satır 20 (paylaşım → yayılım) — [[mood-taxonomy]] §3.2.2 / §3.2.7 |
| Taksonomi BPM notu | §3.2.2 "120-160 BPM ⚠️" ve §3.2.7 "100-140 BPM ⚠️" işaretli kurgusal önermedir (§1.2); test eşikleri olarak research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Sık skip / hızlı geçiş | Üst üste atlama kilidi ve gecikme yok; INP ≤ **200 ms** | Performance trace + sayaç kontrolü |
| 2 | Yeni şarkı keşfi | Öneri kartı tıklanabilir; keşif listesi tür çeşitliliği korunur | DOM assertion + liste kontrolü |
| 3 | Playlist oluşturma sıklığı | Oluşturma akışı tek ekranda; anlık "oluşturuldu" geri bildirimi | DOM assertion + LocalStorage kontrolü |
| 4 | Sosyal paylaşım (Sosyal küme) | Paylaş butonu öne çıkar; 280 karakter sınırı uygulanır (ADR-023 satır 20) | Manuel + sınır testi |
| 5 | Yüksek kontrast beklentisi | Canlı renkler ama kontrast ≥ **4.5:1** (1.4.3) korunur | Kontrast denetimi + screenshot |
| 6 | Mood-geçiş (ADR-023 satır 16) | Geçerli mood → arayüz uyarlanır; geçersiz değer → hata (error) | Manuel + hata akışı testi |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Türkçe Pop (yaz hit'leri) 2) Mediterranean House 3) Latin Pop 4) Türkçe Slow 5) Chillwave | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Semicenk, BLOK3, Mabel Matiz, Gülşen, Mert Demir · uluslararası: Kygo, Dua Lipa, Calvin Harris, The Weeknd, Sade | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Pop **80-120** · Dance **110-135** · House **110-128** · Electro **90-130** · Hip Hop **80-130** BPM | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 / P4.5 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Tür-özel BPM (Mediterranean House / Latin Pop / Chillwave)** | Bankada bu alt türler için ayrı aralık YOK → yazılmadı | `⚠️ VERIFICATION REQUIRED` (P4.4 yalnız genel House/Dance/Pop kapsar) |
| **Kişisel tempo tercihi** | 100-128 BPM (tekne/otel temposu — eski dosya tercihi) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / iş)** | 07:30 koşu (enerjik pop) · 09:00-16:00 otel görevi (çalan liste) · 19:00 gün batımı (slow) · 23:00 tekne partisi (yüksek enerji) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (kişisel + kurumsal/otel kullanımı) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Otel misafirlerinden, teknedeki turistlerden, Shazam benzeri tanıma, DJ setleri | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %95 (iş için kullanıyor; reklam tolerance'ı 0/10) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türkçe Pop (yaz hit'leri) | Otelde ve teknede en çok çalan; enerjik ve neşeli |
| 2 | Mediterranean House | Bodrum akşamlarının müziği; saksafonlu deep house |
| 3 | Latin Pop | Tekne partilerinin dans ritmi |
| 4 | Türkçe Slow | Gün batımına karşı, sakin anlar |
| 5 | Chillwave | Otel lobisi ve spa alanı için fon müziği |

**Dinleme zaman çizelgesi (kurgusal — yaz sezonu):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30 | Sahil koşusu | Enerjik pop |
| Hafta içi | 09:00-12:00 | Otel lobisi / karşılama | Chill house (listeden) |
| Hafta içi | 12:00-16:00 | Plaj / poolside yardım | Yaz hit'leri |
| Hafta içi | 16:00-19:00 | Boş zaman: deniz, kitap | Kişisel dinleme (karışık) |
| Hafta içi | 19:00-21:00 | Gün batımı, otel terası | Türkçe slow / akustik |
| Hafta sonu | 23:00-01:00 | Tekne partisi / arkadaşlarla | Yüksek enerji (dance, house) |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "100-128 BPM" tercihi bankada doğrulanmadı → kurgusal tercih olarak taşındı.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Otel Wi-Fi + ev fiberi (kurgusal; "Starlink" iddiası bankada yok) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (güneş altında kullanım → yüksek parlaklık testi adayı) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 5-7 sa/gün; iş modunda kesintisiz uzun oturum (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 8/10 — ses sistemleri, sosyal medya yönetimi, rezervasyon yazılımı akıcı | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | iPhone 15 Pro, iPad Pro, AirPods Pro 2, JBL hoparlör sistemi — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); güneş altında parlak ortam kullanımı | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — parlak güneş senaryosunda da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; açık hava/parti ortamında ses yüksek | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | normal; hareket hâlinde (koşu, tekne) tek elle kullanım | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | hızlı tempolu; sabırsız | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 — hata metni kısa ve yönlendirici | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | 16 yaşında (ergin değil); persona kurgusaldır → gerçek kişi verisi yok (6698 m.5/1, `Kaynak: [k1] + [k2]`, P6.4) |
| Renk kombinasyonu | Canlı/yüksek kontrast palet kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil — canlı renk × beyaz riski testte kontrol edilir |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Olgun ve iş disiplinli: yazın otelde çalışır, sorumluluk almaktan çekinmez.
- Sosyal keşif: her milletten insanla konuşur; müzik onun için hem iş hem eğlence.
- Pratik: playlist'leri işe göre ayırır (lobi, spa, plaj, tekne).
- Sabırsız ama kibar: yavaş ekranda usulca çıkar, reklam görürse bırakır.
- Doğa ve deniz duyarlı; huzurlu/sakin anlarda slow-chill dinler.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | İş gerekçesiyle premium alır (aile bütçesi + otel gideri) | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 3 sn; spinner'a ~6 sn dayanır, sonra işi bırakır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Hızlıca alternatife geçer; hata metni kısa ve çözüm yol gösteren olmalı | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 0/10 — iş ortamında reklam olmamalı | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Pratik + görsel; hızlı deneyip öğrenir | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Playlist'ini QR/kodla misafirlerle paylaşır; işbirlikli listelere açık | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.7 (paylaşan, akışta dolaşan) |
| **Yorgunluk etkisi** | Gece 01:00 sonrası sadece otomatik çalma ister | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tanıdık yaz hit'i çalarsa hemen paylaşır; sosyal geri bildirim keşfi besler | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.2 / §3.2.7 |

*Erişilebilirlik etkisi (özet):* güneş altında kullanım → kontrast ≥ **4.5:1** (1.4.3) ayrıca yüksek parlaklıkta test edilir; hareket hâlinde tek elle kullanım → dokunma hedefi ≥ **24×24 CSS px** (2.5.8) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Nehir Deniz'sin. 16 yaşında, Muğla/Bodrum'da yaşıyorsun, Bodrum Anadolu Lisesi 11. sınıf öğrencisisin.
Enerjik ve sosyal bir kişiliğin var: yüksek tempolu, sık şarkı atlar, yeni şarkı keşfeder ve çok playlist oluşturursun; ikincil olarak Sosyal'sın — bulduğun şeyi anında paylaşırsın.
Ailenin butik otelinde çalışıyor, babanın teknesinde DJ'lik yapıyorsun; müzik senin işinin bir parçası.
Müzik zevkin: Türkçe Pop (yaz hit'leri), Mediterranean House, Latin Pop, Türkçe Slow, Chillwave. En sevdiklerin: Semicenk, BLOK3, Mabel Matiz, Gülşen, Mert Demir; uluslararası: Kygo, Dua Lipa, Calvin Harris.
Samsung Galaxy A34 5G kullanıyorsun, light temada; otelde ve evde hızlı internet (kurgusal).
Görme / işitme / hareket kısıtların yok; güneş altında parlak ekranda bakarsın — yüksek kontrast ve hızlı arayüz senin için önemlidir.
Sabır eşiğin 3 sn, spinner'a ~6 sn dayanırsın; reklam hiç sevmezsin, yavaş ekranda işini bırakırsın.
Test edilecek akışlar: hızlı navigasyon + geri tuşu, skip performansı, playlist oluşturma, QR/paylaşım akışı, crossfade/kesintisiz çalma, karanlık/aydınlık tema, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Üst üste şarkı atlar, her atlama anında geri bildirim beklersin; kilitten/gecikmeden rahatsız olursun.
- Playlist'lerini işe göre ayırırsın (lobi, spa, plaj, tekne) ve misafirlerle paylaşırsın.
- Hızlı, canlı renkli ekranlarda kendini iyi hissedersin; yavaş yüklemede soğursun.
- Arayüzde "sen" dili ve kısa, pratik metin beklersin; şiirsellik seni yavaşlatır.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler; suçlayıcı/otoriter ton yok |
| Sürpriz yasak | Hassas persona: korkutucu/utanç verici senaryo yok |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |
| Eski prompt temizliği | Eski AI_PROMPT'taki "Spotify TR 2025: …" istatistik satırı bu prompta KOPYALANMADI → `⚠️ VERIFICATION REQUIRED` (§7.2) |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme | LCP ≤ **2500 ms**, canlı karşılama kartı üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Mood-geçiş (Enerjik arayüz) | Hızlı animasyonlar devrede; geçersiz mood değeri hata (error) üretir (ADR-023 satır 16) | Manuel + hata akışı testi |
| 3 | Hızlı navigasyon + geri tuşu (ADR-023 satır 12) | Sekmeler arası geçiş anlık; geri tuşu durumu korur; INP ≤ **200 ms** | Trace + console log |
| 4 | Skip performansı (üst üste atlama) | Ardışık skip'te kilit/gecikme yok; parça sırası tutarlı | Manuel + sayaç kontrolü |
| 5 | Playlist oluşturma (iş listesi: lobi/spa/plaj) | Tek ekranda oluşturma; "oluşturuldu" geri bildirimi anlık | DOM assertion + LocalStorage kontrolü |
| 6 | Paylaşım (QR / link — ADR-023 satır 20) | Paylaş akışı açılır; 280 karakter sınırı uygulanır; gizli hesap uyarısı doğru | Manuel + sınır testi |
| 7 | Kurumsal hesap girişi | Rol/izin akışı doğru; yetkisiz işlemde hata (error) mesajı | Manuel + hata akışı testi |
| 8 | Şarkı çalma + crossfade | İlk ses < 1 sn; kesintisiz geçiş; INP ≤ **200 ms** | Trace + console log |
| 9 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 10 | Tema geçişi (light / dark) | Geçiş müziği kesmez; iki temada da kontrast ≥ **4.5:1** (1.4.3) | Manuel + ekran görüntüsü + kontrast denetimi |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Çevrimdışı / offline yedek müzik | Kopmada yumuşak durum metni; teknik hata kodu görünmez | CDP `Network.emulateNetworkConditions` + manuel |
| 13 | Hata sayfaları (404 / kopma) | Türkçe, kısa, çözüm yol gösteren metin | Manuel + screenshot |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe |
| 15 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 16 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Şarkı/sanatçı adlarında karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü |
| 17 | Uzun oturum (iş modu, saatlerce kesintisiz) | Oturum canlı kalır; cache/oturum sınırı (TTL) davranışında veri kaybı yok | Manuel oturum testi + cache kontrolü (öneri; ADR-023 satır 7 ile ilişkili) |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, ilçe, okul, sınıf, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (16) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: false` (16 yaş politika eşiği) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi (eski 10'luk ×10) | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.2 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | N ekseni yönü ("Duygusal Denge 9/10" ↔ N nörotisizm ters kodlama) | Belirsiz dönüşüm | eski salt-okunur persona dosyası (Duygusal Denge 9/10) | persona-template §3.5.3 (N ters kodlama) | `⚠️ VERIFICATION REQUIRED` — ham ×10 değeri yazıldı (§7.2) |
| 12 | Mood küme adları (Enerjik / Sosyal) | Vault referanslı atama | [[mood-taxonomy]] §3.2.2 + §3.2.7 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.2.2 | [[mood-taxonomy]] §3.2.7 | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Mood↔Big Five eğilimi (Enerjik: "düşük N") ile persona N=90 uyumu | Kurgusal önerme (§1.2) | mood-taxonomy §1.2 + §3.2.2 | bu dosya §3.5.3 | `⚠️ VERIFICATION REQUIRED` — taksonomi önermesidir, veri hatası değildir (§7.2) |
| 15 | Tür BPM aralıkları (Pop 80-120 · Dance 110-135 · Electro 90-130 · House 110-128) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 16 | Rap/Trap BPM (gerekirse: boom-bap 85-95 · trap 70-110 · modern rap ~140) | Gerçek-dünya (ikincil) | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED`) |
| 17 | Şarkı bazlı BPM; alt tür BPM (Mediterranean House / Latin Pop / Chillwave); kişisel 100-128 tercihi | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — şarkı/alt-tür BPM'i yazılmadı; tercih kurgusal |
| 18 | Favori sanatçı adları (Semicenk, BLOK3, Mabel Matiz, Gülşen, Mert Demir, Kygo, Dua Lipa, Calvin Harris, The Weeknd, Sade) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 19 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 20 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 21 | Diğer cihazlar (iPhone 15 Pro, iPad Pro, JBL sistem) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 22 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 23 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 24 | Persona renk kombinasyonu (canlı/yaz paleti) kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 25 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 26 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Eski dosyadaki istatistik iddiaları (Spotify TR 2025, otel geliri, takipçi sayıları) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

---

## §4 Kurallar

### ZORUNLU: ŞABLON ÖNCE (Template-First) — Persona

**Bu persona dosyasını yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonu oku:**

1. Şablonu `[[../.templates/index]]` §7.1 tablolarından seç → persona için `personas/persona-template.md`.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz anlatım bloklarını kaldır (§3.5 alan havuzu KALIR).
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** bu belgedeki 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan persona dosyası **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/personas/persona-template.md` VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | 8-bölüm formatına göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

*(Bu blok persona-template §3.3'ten birebir kopyalanmıştır; §4'ün ilk maddesidir, silinemez.)*

### §4.1 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Template Mandatory (Guardrail #16) | Bu dosya persona-template'den üretildi | Dosya geçersiz, revert |
| 2 | Şablon Önce | Yazmadan `.ai/.templates/` okundu | ERROR log, yazıma devam yok |
| 3 | SSOT | Persona kataloğu [[personas/index]]; araştırma verisi [[research-bank]] | İçerik silinir |
| 4 | Zero Hallucination (ADR-005) | Her satır §4.5 etiketi taşır; bankada olmayan iddia yazılmaz | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`nehir-deniz-enerjik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `tekne 124 BPM` | Tür aralığı (House 110-128, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.2/§3.2.7 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 → §6.1 → VERIFY → LOG
```

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/genc-kiz/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift `{{` | §4 bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Nehir için INP 500 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 3 sütun (Adım / Beklenen / Doğrulama yöntemi)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 16 yaş ve üzeri: `veli_onayı_gerekli: false` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4/P4.5); şarkı BPM'i yok
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 17 (asgari 10) |
| Kaynak & Doğrulama satırı | 29 |
| Frontmatter alan | 7 zorunlu + 5 ek |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.2 / §3.2.7) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 16 (19 Temmuz 2010 — 2026-09-26'da 16) | 16 | Çelişki yok |
| Mood | Enerjik (birincil) / Sosyal (ikincil) | Enerjik / Sosyal | Tutarlı — index §6.3 + mood-taxonomy §3.2.2/§3.2.7 |
| Kullanıcı ID | `GC-ND-16-ENR-MGL` (eski footer) | `CM-ND-16-ENR-MGL` (şablon §3.5.1 `CM-XX-YY-ZZZ-XXX`) | Şablon formatı kazanır |
| Birincil cihaz | iPhone 15 Pro (EXCLUDED) + iPad Pro + JBL | Samsung Galaxy A34 5G (P3 VERIFIED) | [[research-bank]] P3 |
| N ekseni | "Duygusal Denge 9/10 — dengeli, olgun" | N = 90 (ham ×10); ters kodlama okuması çelişir | `⚠️ VERIFICATION REQUIRED` — dönüşüm yönü doğrulanana kadar ham değer korunur (§3.5.3 / satır 11) |
| Mood↔Big Five önermesi | — | Taksonomi §3.2.2 "düşük N" önermesi vs persona N=90 | Kurgusal önerme (§1.2) — veri hatası değil, not satırı (satır 14) |
| BPM tercihi | 100-128 (kişiye özel) | Yalnız tür aralıkları (P4.4/P4.5) + kurgusal tercih | X-2: şarkı/türe özel doğrulanmamış BPM yazılmaz |
| AI prompt istatistiği | "Spotify TR 2025: BLOK3, Semicenk yaz hitleri" | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı; ID formatı şablona uyarlandı; veli_onayı (false) + KVKK notu eklendi; N ekseni belirsizliği §7.2'ye kaydedildi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-kiz/nehir-deniz-enerjik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode