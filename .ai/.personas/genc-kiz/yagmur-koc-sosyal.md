---
title: "CoreMusic — Persona: Yağmur Koç"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-kiz/yagmur-koc-sosyal"
updated: 2026-09-26
group: genc-kiz
age: 16
mood: Sosyal
persona_id: CM-YK-16-SOS-AYD
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Yağmur Koç

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-kiz (13-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 16 yaşındaki bir genç kızın gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Sosyal / Enerjik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-kiz (13-17) |
| Birincil mood | Sosyal ([[personas/index]] §6.3 ataması) |
| Test odağı | Paylaşım akışı, trend/viral listeleri, estetik playlist kapakları, hızlı navigasyon |
| Veli onayı | `veli_onayı_gerekli: false` (16 yaş — politika eşiği, ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Paylaşım → yayılım (ADR-023 satır 20) | Level 2 + 3 | Sosyal paylaşım, 280 karakter sınırı, gizli hesap uyarısı |
| Mood-geçiş (ADR-023 satır 16) | Level 2 + 3 | Sosyal/Enerjik arayüz; geçersiz mood değeri = error |
| Keşif & trend listeleri (ADR-023 satır 18) | Level 2 + 3 | Boş/uzun/karakter-sınırı sorgular |
| Playlist oluşturma & estetik kapak | Level 2 + 3 | Kapak seçimi, paylaşım sınırı |
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
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel detay (makyaj, marka, aksesuar) bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.2.7 + §3.2.2 | Küme adı + tanım alıntısı (Sosyal / Enerjik) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Yağmur Koç | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 16 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 8 Nisan 2010 | Kaynak: kurgusal (persona verisi; 2026-09-26 itibarıyla yaş 16 ile tutarlı) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Aydın / Kuşadası | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Kuşadası Anadolu Lisesi, 11. sınıf; yazın aile restoranında çalışır | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Yaya + servis (ev-okul ~15 dk, kurgu) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | yagmur.trend | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-YK-16-SOS-AYD` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `YK` | Ad + soyad ilk harfleri (Yağmur Koç → Y, K) |
| `YY` | `16` | Yaş 2 hane (16 → `16`; şablon örnek aralığı 10-99'a uyar) |
| `ZZZ` | `SOS` | Mood tür kodu (Sosyal → SOS) |
| `XXX` | `AYD` | Şehir kodu (Aydın → AYD) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş ve üzeri):** Bu persona 16 yaşındadır → frontmatter'de `veli_onayı_gerekli: false`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). CoreMusic politikası 16 yaş altı için veli onayı ister; 16 → `false` politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek kişi verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (makyaj, marka listesi, aksesuar…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 166 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 53 kg, ince-uzun yapı | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Açık kumral saç, kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (görme ve işitme iyi — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Trend takipçisi modern/şık stil, bej-krem-zeytin tonları; profil fotoğrafında estetik kompozisyon (renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Uzun süreli telefonda içerik üretimi → yorgunluk; görsel estetik beklentisi yüksek | Kaynak: kurgusal (persona verisi); kontrast kriteri: `Kaynak: [k1] + [k2]` (P5.4) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 90 | Sosyal kelebek: herkesle iletişim kurar, kalabalıkta parlar, içerik üretmekten çekinmez. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 70 | Uyumlu ama popülerlik bilinci vardır — trend olanla hizalanır, çatışmadan kaçınır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 60 | Okul orta seviyededir; sosyal hayat ve içerik üretimi önceliklidir, düzeni kendi içinde kurar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 70 | **Eksen belirsizliği:** eski kaynakta "Duygusal Denge 7/10 — dengeli, dışa dönük, pozitif" yazar → ×10 = 70. N (nörotisizm) ekseninde ters kodlama varsa bu puan orta-yüksek gerginlik anlamına gelir; iki okuma da uygulanabilir → `⚠️ VERIFICATION REQUIRED` (bkz. §7.2). | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120; eksen: `⚠️ VERIFICATION REQUIRED` |
| Deneyime Açıklık | O | 80 | Yeni trendleri hemen benimser; yeni türleri, yeni estetikleri ilk o dener. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

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
| **Birincil küme** | Sosyal |
| **İkincil küme** | Enerjik ([[mood-taxonomy]] §3.2.2) |
| **Küme tanımı (alıntı)** | "Paylaşan, takip eden, akışta dolaşan; müzik sosyalleşme aracıdır" ([[mood-taxonomy]] §3.2.7) |
| **Tetikleyici durumlar** | Paylaşım butonu / trend kartı (yaklaşma); estetik bozukluk, yavaş yükleme ve reklam (geri çekilme) |
| **UI etkisi** | "Paylaşım butonları, sosyal özellikler öne çıkar"; test etkisi: "Paylaşım akışı, işbirlikli playlist, sosyal feed; ADR-023 satır 20 (paylaşım → yayılım / gizli hesap / 280 karakter sınırı)" ([[mood-taxonomy]] §3.2.7) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Sosyal); ikincil = eski salt-okunur persona kaynağı (Enerjik) — §7.2'de kayıtlı |
| Test etkisi | ADR-023 satır 20 (paylaşım → yayılım / 280 karakter) + satır 18 (müzik keşif) + satır 12 (hızlı navigasyon) — [[mood-taxonomy]] §3.2.7 / §3.2.2 |
| Taksonomi BPM notu | §3.2.7 "100-140 BPM ⚠️" ve §3.2.2 "120-160 BPM ⚠️" işaretli kurgusal önermedir (§1.2); test eşikleri olarak research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Paylaşma alışkanlığı | Paylaş butonu her kartta erişilebilir; akış 280 karakter sınırını uygular (ADR-023 satır 20) | Manuel + sınır testi |
| 2 | Trend/viral takibi | Trend listesi güncel görünür; boş/uzun/karakter-sınırı sorgu davranışları anlamlı (ADR-023 satır 18) | Manuel + sorgu testleri |
| 3 | Estetik beklenti (kapak/renk) | Kapak seçimi çalışır; düzen bozulmaz; CLS ≤ **0.1** | DOM assertion + screenshot |
| 4 | Sık sekme/gezinme (Enerjik ikincil) | Hızlı geçiş, geri tuşu durumu korur; INP ≤ **200 ms** | Trace + console log |
| 5 | Yüksek kontrast beklentisi | Açık/bej tema dahil kontrast ≥ **4.5:1** (1.4.3) korunur | Kontrast denetimi + screenshot |
| 6 | Mood-geçiş (ADR-023 satır 16) | Geçerli mood → arayüz uyarlanır; geçersiz değer → hata (error) | Manuel + hata akışı testi |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Viral/Trend Pop 2) Türkçe Pop (yeni) 3) Afrobeat/Amapiano 4) Latin Pop 5) Lo-fi | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | BLOK3, Lvbel C5, Semicenk, Tyla, Doja Cat | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Pop **80-120** · Dance **110-135** · Electro **90-130** · House **110-128** · Hip Hop **80-130** BPM | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 / P4.5 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Tür-özel BPM (Afrobeat / Amapiano / Lo-fi / Viral alt türleri)** | Bankada bu alt türler için ayrı aralık YOK → yazılmadı | `⚠️ VERIFICATION REQUIRED` (P4.4 yalnız genel Pop/Dance/Electro/House kapsar) |
| **Kişisel tempo tercihi** | 90-120 BPM (trend pop aralığı — eski dosya tercihi) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | 07:30 sabah rutini (lo-fi) · 16:00 içerik üretimi (trend) · 18:00 arkadaşlarla · 20:00 restoran · 22:00 sosyal medya | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (kişisel profil) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Sosyal medya trendleri, viral listeleri, arkadaş çevresi | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %85 (aile paketiyle) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Viral/Trend Pop | Yükselen her şeyi anında yakalar |
| 2 | Türkçe Pop (yeni) | En yeni çıkanlar, yerli trendler |
| 3 | Afrobeat/Amapiano | Yeni global akım, yaz trendi |
| 4 | Latin Pop | Kuşadası yazlık modu |
| 5 | Lo-fi | Sabah rutini estetiği |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:00 | Sabah rutini + kahvaltı | Lo-fi |
| Hafta içi | 08:30-15:30 | Okul | — |
| Hafta içi | 16:00-18:00 | İçerik üretimi | Viral/Trend Pop |
| Hafta içi | 18:00-20:00 | Arkadaşlarla buluşma | Türkçe Pop (yeni) |
| Hafta içi | 20:00-22:00 | Aile restoranı / akşam | Latin Pop / Afrobeat |
| Hafta sonu | 22:00-00:00 | Sosyal medya + parti hazırlığı | Party / trend listeleri |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "90-120 BPM" tercihi bankada doğrulanmadı → kurgusal tercih olarak taşındı.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Evde fiber (kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (bej/krem estetik); akşam karanlık tema adayı | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 7-9 sa/gün (içerik üretimi yoğun; kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 8/10 — içerik üretimi, video düzenleme, tema/estetik ayarları akıcı | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | iPhone 15, iPad, AirPods Pro 2 — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); uzun süreli ekran (içerik üretimi) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — açık/bej temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; kulaklıkla dinleme yoğun | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | normal; tek elle hızlı dokunma (akışta gezerken) | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | hızlı tarama; trend odaklı | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 — hata metni kısa ve estetik | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | 16 yaşında (ergin değil); persona kurgusaldır → gerçek kişi verisi yok (6698 m.5/1, `Kaynak: [k1] + [k2]`, P6.4) |
| Renk kombinasyonu | Bej/krem palet kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil — açık ton × beyaz riski testte kontrol edilir |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Trend öngörüsü: yükseleni ilk o keşfeder, arkadaşına o önerir.
- Sosyal zeka: kalabalıkta rahat, turistlerle iletişimi kuvvetli.
- Estetik hassasiyet: kapak, renk ve düzen detayını hemen fark eder.
- Müzik = sosyal statü: "doğru şarkıları" bilmek önemlidir.
- Marka/trend sadakati yüksek; yenilikçi ama yüzeysellik riski taşır.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Aile paketi içinde premium; tek başına almaz | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 3 sn; spinner'a ~6 sn dayanır; estetik olmayan ekranı hemen bırakır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | "Kötü görünüyorsa" paylaşmaz; hata ekranının da şık olmasını bekler | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 1/10 — premium kullanıcısı, reklamı tolere etmez | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + trend referansı; kısa video/demo ile öğrenir | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Şarkıyı hikâyede paylaşır; arkadaş listesiyle yarışır | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.7 (paylaşan, takip eden) |
| **Yorgunluk etkisi** | 23:30 sonrası paylaşımı bırakır, yalnız dinler | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tanıdık trend şarkısını görürse hemen paylaşır | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.7 / §3.2.2 |

*Erişilebilirlik etkisi (özet):* uzun ekran süresi + açık tema → kontrast ≥ **4.5:1** (1.4.3) ayrıca açık temada test edilir; tek elle akış gezinmesi → dokunma hedefi ≥ **24×24 CSS px** (2.5.8) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Yağmur Koç'sun. 16 yaşında, Aydın/Kuşadası'nda yaşıyorsun, Kuşadası Anadolu Lisesi 11. sınıf öğrencisisin.
Sosyal ve trend takipçisi bir kişiliğin var: yükselen her şeyi ilk sen keşfeder, paylaşır, arkadaşına önerirsin; ikincil olarak Enerjik'sin — hızlı gezer, hızlı karar verirsin.
Müzik zevkin: Viral/Trend Pop, Türkçe Pop (yeni), Afrobeat/Amapiano, Latin Pop, Lo-fi. En sevdiklerin: BLOK3, Lvbel C5, Semicenk, Tyla, Doja Cat.
Samsung Galaxy A34 5G kullanıyorsun, açık (bej/krem) temada, internetin evde hızlı (kurgusal).
Görme / işitme / hareket kısıtların yok; uzun süre ekrandasın — estetik düzen ve hızlı akış senin için önemlidir.
Sabır eşiğin 3 sn, spinner'a ~6 sn dayanırsın; reklamı sevmezsin, estetik olmayan ekranı hemen bırakırsın.
Test edilecek akışlar: paylaşım akışı (hikâye/link), trend/viral listeleri, keşif araması, playlist kapakları, hızlı navigasyon, tema, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Trend olan her şeyi takip edersin; haftalık trend listeleri senin için kritiktir.
- Müzik senin için sosyal statü göstergesidir; paylaşırken estetik ve doğru şarkı önemlidir.
- Paylaşım akışında 280 karakter sınırı ve gizlilik seçenekleri seni ilgilendirir.
- Arayüzde "sen" dili, kısa ve şık metin beklersin; dağınık/kaba metin soğutur.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler; suçlayıcı/otoriter ton yok |
| Sürpriz yasak | Hassas persona: korkutucu/utanç verici senaryo yok |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |
| Eski prompt temizliği | Eski AI_PROMPT'taki "Spotify TR 2025: BLOK3 ve Lvbel C5 trend liderleri" + takipçi sayıları bu prompta KOPYALANMADI → `⚠️ VERIFICATION REQUIRED` (§7.2) |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme | LCP ≤ **2500 ms**, trend kartları üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Mood-geçiş (Sosyal arayüz) | Sosyal özellikler öne çıkar; geçersiz mood değeri hata (error) üretir (ADR-023 satır 16) | Manuel + hata akışı testi |
| 3 | Paylaşım akışı (ADR-023 satır 20) | Paylaş butonu çalışır; 280 karakter sınırı uygulanır; gizli hesap uyarısı doğru | Manuel + sınır testi |
| 4 | Keşif / trend listesi (ADR-023 satır 18) | Boş, çok uzun ve karakter sınırını aşan sorgularda anlamlı sonuç/uyarı | Manuel + sorgu testleri |
| 5 | Playlist oluşturma (estetik kapak) | Kapak seçimi çalışır; isimlendirme kaydedilir; düzen bozulmaz | DOM assertion + LocalStorage kontrolü |
| 6 | Hızlı navigasyon + geri tuşu (ADR-023 satır 12) | Sekmeler arası geçiş anlık; geri tuşu durumu korur; INP ≤ **200 ms** | Trace + console log |
| 7 | Skip performansı (trend akışında atlama) | Ardışık skip'te kilit/gecikme yok; sıralama tutarlı | Manuel + sayaç kontrolü |
| 8 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; geçiş akıcı; INP ≤ **200 ms** | Trace + console log |
| 9 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 10 | Tema geçişi (light / dark) | Geçiş müziği kesmez; iki temada da kontrast ≥ **4.5:1** (1.4.3) | Manuel + ekran görüntüsü + kontrast denetimi |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Çevrimdışı / yavaş ağ davranışı | Yavaş ağda yumuşak yükleme animasyonu; teknik hata metni görünmez | CDP `Network.emulateNetworkConditions` + manuel |
| 13 | Hata sayfaları (404 / kopma) | Türkçe, kısa, estetik ve suçlayıcı olmayan metin | Manuel + screenshot |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe |
| 15 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 16 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Şarkı/sanatçı adlarında karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü |
| 17 | Gün boyu açık oturum | Uzun oturumda oturum canlı kalır; durum/çalma kaybı olmaz | Manuel oturum testi + durum kontrolü |

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
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.7 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | N ekseni yönü ("Duygusal Denge 7/10" ↔ N nörotisizm ters kodlama) | Belirsiz dönüşüm | eski salt-okunur persona dosyası (Duygusal Denge 7/10) | persona-template §3.5.3 (N ters kodlama) | `⚠️ VERIFICATION REQUIRED` — ham ×10 değeri yazıldı (§7.2) |
| 12 | Mood küme adları (Sosyal / Enerjik) | Vault referanslı atama | [[mood-taxonomy]] §3.2.7 + §3.2.2 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.2.7 | [[mood-taxonomy]] §3.2.2 | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Mood↔Big Five eğilimi (Sosyal: "yüksek Dışadönüklük/Uyumluluk") | Kurgusal önerme (§1.2) | mood-taxonomy §1.2 + §3.2.7 | bu dosya §3.5.3 | `⚠️ VERIFICATION REQUIRED` — taksonomi önermesidir, veri hatası değildir |
| 15 | Tür BPM aralıkları (Pop 80-120 · Dance 110-135 · Electro 90-130 · House 110-128) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 16 | Rap/Trap BPM (gerekirse: boom-bap 85-95 · trap 70-110 · modern rap ~140) | Gerçek-dünya (ikincil) | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED`) |
| 17 | Şarkı bazlı BPM; alt tür BPM (Afrobeat/Amapiano/Lo-fi); kişisel 90-120 tercihi | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — şarkı/alt-tür BPM'i yazılmadı; tercih kurgusal |
| 18 | Favori sanatçı adları (BLOK3, Lvbel C5, Semicenk, Tyla, Doja Cat) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 19 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 20 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 21 | Diğer cihazlar (iPhone 15, iPad, AirPods Pro 2) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 22 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 23 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 24 | Persona renk kombinasyonu (bej/krem palet) kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 25 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 26 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Eski dosyadaki istatistik iddiaları (Spotify TR 2025, takipçi sayıları, aile geliri) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`yagmur-koc-sosyal.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `trend pop 100 BPM` | Tür aralığı (Pop 80-120, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.7/§3.2.2 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-özel eşik uydurma | "Yağmur için INP 500 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.7 / §3.2.2) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 16 (8 Nisan 2010 — 2026-09-26'da 16) | 16 | Çelişki yok |
| Mood | Sosyal (birincil) / Enerjik (ikincil) | Sosyal / Enerjik | Tutarlı — index §6.3 + mood-taxonomy §3.2.7/§3.2.2 |
| Kullanıcı ID | `GC-YK-16-SOS-AYD` (eski footer) | `CM-YK-16-SOS-AYD` (şablon §3.5.1 `CM-XX-YY-ZZZ-XXX`) | Şablon formatı kazanır |
| Birincil cihaz | iPhone 15 (EXCLUDED) + iPad + AirPods Pro 2 | Samsung Galaxy A34 5G (P3 VERIFIED) | [[research-bank]] P3 |
| N ekseni | "Duygusal Denge 7/10 — dengeli, pozitif" | N = 70 (ham ×10); ters kodlama okuması farklı | `⚠️ VERIFICATION REQUIRED` — dönüşüm yönü doğrulanana kadar ham değer korunur (§3.5.3 / satır 11) |
| Mood↔Big Five önermesi | — | Taksonomi §3.2.7 önermesi ile persona puanları kurgusal önerme düzeyinde | Kurgusal önerme (§1.2) — veri hatası değil, not satırı |
| BPM tercihi | 90-120 (kişiye özel) | Yalnız tür aralıkları (P4.4/P4.5) + kurgusal tercih | X-2: şarkı/türe özel doğrulanmamış BPM yazılmaz |
| AI prompt istatistiği | "Spotify TR 2025: BLOK3 ve Lvbel C5 trend liderleri" | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma) |
| Sosyal medya takipçi/aile geliri | Kaynaksız sayılar (5.200/8.400 takipçi, ~90.000 TL/ay) | yazılmadı | §4.5 kuralı 4 |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı; ID formatı şablona uyarlandı; veli_onayı (false) + KVKK notu eklendi; N ekseni belirsizliği §7.2'ye kaydedildi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-kiz/yagmur-koc-sosyal
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode