---
title: "CoreMusic — Persona: Ceyda Demir"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/yetiskin-kadin/ceyda-demir-melankolik"
updated: 2026-09-26
group: yetiskin-kadin
age: 35
mood: Melankolik
persona_id: CM-CD-35-MEL-ANK
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Ceyda Demir

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **yetiskin-kadin** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 35 yaşındaki, **Melankolik** kümesine atanmış bir yetişkin kadının — derin dinleyicinin — gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Melankolik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], senaryo eşlemesi → [[personas/test-scenarios-mapping]], registry → [[personas/index]] §6.3 (satır 62).
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | yetiskin-kadin (25-45) |
| Birincil mood | Melankolik ([[personas/index]] §6.3 ataması) |
| Test odağı | S2 Discovery (gelişmiş filtre + fuzzy arama), S5 Settings (gece modu / düşük kontrast), S4 Download (toplu indirme) |
| Veli onayı | `veli_onayı_gerekli: false` (yetişkin — 16 yaş altı politikası uygulanmaz) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| S2 Music Discovery | Level 2 + 3 | Yetişkin satırı: gelişmiş filtre (yıl, süre, BPM) + fuzzy arama; Melankolik önceliği S2 + S5 ([[personas/test-scenarios-mapping]] §4.4/§4.5) |
| S4 Download | Level 3 | Yetişkin satırı: toplu indirme, kalite seçimi, cihazlar arası senkron |
| S5 Settings | Level 2 + 3 | Gece modu / düşük kontrast tercihi; GDPR dışa aktarma + tüm cihazlardan çıkış |
| Gece / düşük ışık dinleme akışı | Level 2 | dark tema kontrastı (1.4.3), az animasyon, düşük etkileşim |
| Hata ekranları & yavaş ağ | Level 2 + 3 | Sade Türkçe hata metni; "Slow 4G" koşulunda kesintisiz devam |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (6 sütun: Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel/aile detayı bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.2.3 | Melankolik küme adı + tanım alıntısı |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT — satır 62) |
| 5 | [[personas/test-scenarios-mapping]] §4.4-§4.5 | Yetişkin × Melankolik senaryo satırları |
| 6 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Ceyda Demir | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 35 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3 satır 62) |
| **Doğum Tarihi** | 12 Kasım 1991 | Kaynak: kurgusal (persona verisi; yaş 35 ile tutarlılık için türetildi — eski dosyada 12 Kasım 1988) |
| **Cinsiyet** | Kadın | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Ankara / Çankaya / Gaziosmanpaşa | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Hukuk eğitimli avukat — kendi hukuk bürosu (aile hukuku); üniversite adı YAZILMADI (research-bank X-6) | Kaynak: kurgusal (persona verisi) + `⚠️ VERIFICATION REQUIRED` (üniversite adı kapsam dışı) |
| **Ulaşım** | Kendi aracı (iş + okul) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (hesaplarda gerçek ad) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-CD-35-MEL-ANK` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `CD` | Ad + soyad ilk harfleri (Ceyda Demir → C, D) |
| `YY` | `35` | Yaş 2 hane (35 → `35`) |
| `ZZZ` | `MEL` | Mood tür kodu (Melankolik → MEL) |
| `XXX` | `ANK` | Şehir kodu (Ankara → ANK) |

*Not (yetişkin persona):* `veli_onayı_gerekli: false` — KVKK/yaş sınırı notu 16 yaş altı persona'lar içindir; bu persona için uygulanmaz. Bu dosya tamamen kurgusaldır: gerçek kişi verisi yazılmaz (6698 m.5/1 → `Kaynak: [k1] + [k2]`, research-bank P6). Persona'nın kurgusal çocuğu/ailеsi verisi **her zaman kurgusal** kalır (P6.5).

*Yaş çelişkisi notu:* Eski salt-okunur dosya **38** yazar; bu dosya **35** kullanır ([[personas/index]] §6.3 = SSOT). İki değer de §7.2'de kayıtlıdır, biri indirgenmemiştir (bkz. §3.5.11 satır 3).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (detaylı kıyafet markaları, kilo takibi, burç, kan grubu…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 172 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 64 kg, ince-uzun siluet (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah düz uzun saç (topuz/at kuyruğu), koyu kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Hafif miyop gözlük — bilgisayarda mavi ışık filtreli cam (kurgu); işitme cihazı yok | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Sade-şık ofis görünümü; profil fotoğrafında koyu/ölçülü zemin (kontrast eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Gece uzun okuma → düşük parlaklık + dark tema beklentisi; belge/uzun metin okumada yazı büyütme → satır yüksekliği ve okunabilirlik (1.4.3, 3.2.6) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |

*Sağlık verisi notu:* Eski dosyadaki sağlık ayrıntıları (migren, gastrit, sigara, uyku düzeni vb.) 6698 m.6 kapsamında **sağlık verisidir** → bu dosyada **yazılmadı**; persona'da herhangi bir sağlık kısıtı varsa dahi **kurgusal olması zorunludur** (research-bank P6.5).

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 40 | Mesleği gereği sürekli insanla iletişimdedir ama bu onu yorar; iş dışında yalnız kalmayı, 2-3 yakın arkadaşıyla ayda bir görüşmeyi tercih eder. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 50 | Profesyonel hayatta mücadelecidir; özel hayatında yumuşaktır (özellikle çocuğuna karşı); yeni insanlara temkinli, savunmacı yaklaşır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 90 | Dava takvimi, belge ve bütçe takibi titizdir; erteleme alışkanlığı yoktur, verdiği sözleri ve çocuk programını aksatmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** Melankolik yapı: yoğun duygu dalgalanması, gece/yalnız dinleme; müziği duygularını düzenleme aracı olarak kullanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 70 | Roman/felsefe, sergi ve sanat sineması merakı yüksektir; yeni fikirlere açıktır ama duygusal risk taşıyan yeni deneyimlerde temkinlidir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; eski dosyadaki `/10` puanı 10 ile çarpılarak ölçeklendi (4/10 → 40, 5/10 → 50, 9/10 → 90, 3/10 → 30, 7/10 → 70) → normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ters kodlama (zorunlu) | N boyutunda **yüksek puan = düşük duygusal denge**; tersi (düşük puan) daha dengeli okunur. Eski dosyanın 3/10 değeri bu yönle doğrudan örtüşmez → iki okuma da kaydedildi, `⚠️ VERIFICATION REQUIRED` (§3.5.11 satır 12) |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Melankolik |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — eski dosyanın "Güçlü/Dirençli" ikinciliği mood-taxonomy §3 listesinde YOK → §7.2 çelişki kaydı) |
| **Küme tanımı (alıntı)** | "Derin dinleyici; şarkı sözü okur, az etkileşim kurar, gece ve yalnız dinler" ([[mood-taxonomy]] §3.2.3) |
| **Tetikleyici durumlar** | Gece / düşük ışık / yalnız dinleme; hüzünlü içerik ve nostalji; algoritma önerisine güvensizlik; karmaşık/renkli arayüz yorgunluğu |
| **UI etkisi** | Karanlık tema, minimalist düzen, az animasyon; test etkisi: "Karanlık tema, keşif algoritması, düşük etkileşim" + dark/light kontrast (WCAG 1.4.3) ([[mood-taxonomy]] §3.2.3) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 satır 62 (Melankolik); ikincil eski dosyada "Güçlü/Dirençli" olup listede yok → §7.2'de çelişki olarak saklandı |
| Test etkisi | [[personas/test-scenarios-mapping]] §4.5: Melankolik → **S2, S5** ("Gece modu / düşük kontrast tercihi"); §4.7: Melankolik öncelikli kontrol = "Düşük kontrast tercihi ihmal" |
| §1.2 toplu etiket | mood-taxonomy'deki BPM/tür/Big Five eşlemeleri `⚠️ VERIFICATION REQUIRED` taşır — bu dosyada yalnız **küme adı** sorunsuz kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Gece/yalnız dinleme | Varsayılan dark tema; otomatik geçiş; göz yormayan kontrast (1.4.3) | Level 2 + a11y audit |
| 2 | Algoritma şüphesi | Küratörlü/insan listesi görünür; öneri gerekçesi açıklanabilir | İçerik assertion + manuel |
| 3 | Düşük etkileşim, az animasyon | Geçişler kısa; INP ≤ **200 ms** | Trace + console log (P8.1) |
| 4 | Düşük kontrast tercihi (S5) | Tercih kaydedilir, oturumlar arası korunur | Manuel + LocalStorage kontrolü |
| 5 | Derin dinleyici (albüm bütünlüğü) | Liste/çalma sırası sade; sürükleyici karmaşık kuyruk arayüzü yok (2.5.7) | DOM assertion + a11y audit |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Klasik müzik (piyano) 2) Alternatif rock / indie 3) Türk sanat müziği 4) Jazz (özellikle vokal jazz) 5) Türkçe slow / hüzünlü şarkılar | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Sezen Aksu, Teoman, Mabel Matiz, Radiohead, Billie Holiday | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); Teoman hariç gerçek-dünya bilgileri research-bank P4 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM — tür aralığı (persona türleri)** | Klasik / alternatif rock-indie / TSM / jazz türlerinin P4.4-P4.5 tablosunda karşılığı YOK → tür-BPM iddiası YAZILMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 listesi dışı + P4.7 / EXCLUDED X-2) |
| **BPM — Türkçe pop (çocuğuyla ortak dinleme)** | Pop **80-120** BPM | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM — rap (çocuğuyla ortak dinleme)** | boom-bap **85-95** · trap **70-110** | Kaynak: nevamuzik.com.tr + vocuno.com/tr/bpm-algilayıcı (research-bank P4.5 `VERIFIED`) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | 60-100 BPM (yavaş-orta; gece/yalnız dinleme ağırlıklı) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Mood kümesi BPM notu** | Melankolik kümesi **60-90 BPM** (eski taksonomi) | `⚠️ VERIFICATION REQUIRED` ([[mood-taxonomy]] §3.2.3 + §1.2 toplu etiket — research-bank P4'te doğrulanmadı) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:00 (klasik, evden önce) · 10:00-17:00 müzik yok (odak) · 21:30-23:00 (kişisel "müzik saati") · 23:00-00:00 (yalnız, en duygusal) · Hafta sonu 22:00+ (ev/kültür günü sonrası) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (deniyor — yerli içerik + Türkçe arşiv) + Apple Music (ana platform, kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Düşük: algoritmaya şüpheyle yaklaşır, güvendiği listelere döner; filmde duyduğunu Shazam'lar; yılda ~20-30 yeni şarkı keşfeder | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %50 (reklamsız + kayıpsız ses kalitesi şartıyla) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Klasik müzik (piyano) | Çocukluk fonu; stresli günlerde sakinleşme aracı |
| 2 | Alternatif rock / indie | Üniversite yıllarının müziği; karanlık dönemlerde eşlikçi |
| 3 | Türk sanat müziği | Annesiyle özdeşleşen "yuva" sesi |
| 4 | Jazz (vokal) | Yeniden keşif döneminde girdi; terapi müziği |
| 5 | Türkçe slow / hüzünlü | Melankolik ruh haline eşlik eden, gece dinlenen tür |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:00 | Evden önce kahve | Klasik / piyano |
| Hafta içi | 10:00-17:00 | Ofis (dosya çalışması) | Müzik yok (odak) |
| Hafta içi | 21:30-23:00 | Kişisel zaman | Melankolik şarkılar, jazz |
| Hafta içi | 23:00-00:00 | Yalnız, düşük ışık | Alternatif / slow |
| Hafta sonu | 14:00-17:00 | Kültür günü (müze/sergi) | Yolda hafif jazz |
| Hafta sonu | 22:00+ | Ev, planlama sonrası | Klasik / piyano |

*Not: BPM satırları yalnız tür aralığı düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Kişisel 60-100 BPM tercihi kurgusaldır; [[mood-taxonomy]] §3.2.3 Melankolik BPM 60-90 birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone 15 Pro Max (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka cihazı: `1080 × 2340` (A34, P3.1 `VERIFIED`) → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | iOS sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Safari (mobil) + masaüstü tarayıcı (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev: kablolu fiber; mobil: 4.5G/5G (marka/hız kurgusal — paket dışı) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **Kulaklık** | Ev/kablolu dinleme kulaklığı + taşınabilir kulaklık — model/spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | dark (varsayılan — gece dinleme) + light (gündüz, otomatik) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 10-12 saat toplam (iş ağırlıklı 8-10 + kişisel 2-3 — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 8/10 — dijital dosya yönetimi ve ofis/ımza araçlarına hâkim, Apple ekosistemine alışkın (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Masaüstü bilgisayar, tablet, ev hoparlörü, araç multimedya, televizyon — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); `1080 × 2340` yalnız A34'ün **doğrulanmış** çözünürlüğüdür (P3.1 VERIFIED). Persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | kısıt yok (kurgu); hafif miyop gözlük, gece uzun okuma | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11); hover/focus içerik kontrolü (1.4.13) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | kısıt yok (kurgu); ev/ofis normal | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; uzun belge okumada metin büyütme | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürüklemesiz alternatif (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | dikkat yüksek ama gece yorgunluk; kısa, tutarlı akış beklentisi | Tutarlı yardım erişimi (3.2.6) · tekrar eden bilgi istenmemesi (3.3.7) · erişilebilir kimlik doğrulama (3.3.8) · odak görünür (2.4.7) ve örtülmemiş (2.4.11) | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Veri gizliliği persona için pazarlık dışıdır; "KVKK 16" ifadesi **YANLIŞTIR** — doğru karşılaştırmalar TMK m.11 = **18**, GDPR m.8 = **16**, COPPA = **13** (research-bank P6.3/P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED |
| 72×72 / 64×64 px gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |
| P5 dışı kriterler (2.1.1, 2.2.1, 3.3.1, 3.2.2, 4.1.2, 1.4.12 vb.) | Senaryo eşlemesinde geçer ama P5'te açılmadı → bu dosyada kullanılmadı (`⚠️ VERIFICATION REQUIRED`, X-7) |

### Kişilik & Davranış

- Derin ve içe dönüktür: işi insanlarla doludur ama özel hayatında yalnızlık ve sessizlik ister.
- Melankoliyi zayıflık değil, empati ve derin düşünme kaynağı olarak görür; müzik onun düzenleme aracıdır.
- Güven inşası yavaştır: algoritmalara, platformlara ve yeni insanlara önce mesafe koyar; güvenince sadık kalır.
- Kalite ve sadece sever: karmaşık, renkli, "gençlere yönelik" arayüzleri çocukça bulur.
- Gece ritüeli güçlüdür: 21:30 sonrası onun "müzik saati"dir; bu saatte ekran parlaklığı ve gürültü kritikleşir.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Araştırmacı ve hesaplı: büyük alımlar öncesi detaylı araştırma; net değer önerisi görmeden abonelik değiştirmez | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | Orta-yüksek okuma sabri (~8 sn), ama yükleme belirsizse ve adım tekrarı varsa hızla sıkılır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Sakin ama kesindir: hata kodunu okur, sade çözüm adımı ister; teknik yığın izni görürse güven kaybeder | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 2/10 — reklamsız/kayıpsız deneyime alışkın, aralarda reklama dayanamaz | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Metin + sade görsel; video izlemez, kısa kılavuz ve tutarlı konum ister (3.2.6) | Kaynak: kurgusal (persona verisi) |
| **Gizlilik davranışı** | Ayarları gerçekten okur; veri dışa aktarma ve "tüm cihazlardan çıkış" gibi kontrolleri arar (S5) | Kaynak: kurgusal (persona verisi); senaryo: [[personas/test-scenarios-mapping]] §4.4 S5 |
| **Paylaşım davranışı** | Neredeyse yok: müzik onun özel alanıdır; listeleri paylaşmaz | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Gece 23:00 sonrası tek dokunuşlu, az animasyonlu, düşük parlaklıklı akış ister | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.3 |

*Erişilebilirlik etkisi (özet):* gece okuma + hafif miyop + yorgunluk → kontrast ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11), hedef ≥ **24×24 CSS px** (2.5.8), odak kaybolmaz (2.4.11), yardım/gizlilik konumu tutarlı (3.2.6) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Ceyda Demir'sin. 35 yaşında, Ankara/Çankaya'da yaşıyorsun, hukuk eğitimlisin ve kendi aile hukuku büronu işletiyorsun.
Melankolik bir kişiliğin var: derin dinler, söz okur, gece ve yalnız dinlersin; duygularını müzikle düzenlersin.
Müzik zevkin: klasik müzik (piyano), alternatif rock/indie, Türk sanat müziği, jazz, Türkçe slow/hüzünlü şarkılar. En sevdiklerin: Sezen Aksu, Teoman, Mabel Matiz, Radiohead, Billie Holiday.
Telefonun (model spec'i doğrulanmadı); masaüstü bilgisayarın, tablet'in, ev hoparlörü ve aracının multimedyası var ama hiçbiri bu turda doğrulanmadı; teknoloji seviyen 8/10; dark temada, gece dinlersin.
Görme / işitme / hareket kısıtların yok; hafif miyop gözlüğün var — gece düşük parlaklık, büyük ve net hedefler beklersin.
Sabır eşiğin ~8 sn; yükleme belirsizse ve adım tekrarı görürsen sıkılırsın; reklam toleransın 2/10, kayıpsız ses kalitesi ve sade arayüz beklersin.
Test edilecek akışlar: S2 Discovery (gelişmiş filtre, fuzzy/typo arama), S4 Download (toplu indirme, kalite seçimi), S5 Settings (gece modu/düşük kontrast, veri dışa aktarma, tüm cihazlardan çıkış), arama (Türkçe karakter), hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Algoritma önerisine önce mesafe koyarsın; küratörlü listeyi ve gerekçeyi görünce güvenirsin.
- Gece dinlerken ekranın gözünü yakmamasını, animasyonların kısa olmasını beklersin.
- Dosya/araç gibi kullanırken sade ve yetişkin arayüz istersin; neon/gösterişli butonlar seni uzaklaştırır.
- Türkçe karakterde bozulma (mojibake) veya belirsiz hata metni görürsen platformdan soğursun.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; çift süslü parantez yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7 — yasa: TMK 18 / GDPR 16 / COPPA 13).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, net ve saygılı ton — yetişkin derin dinleyici uzun muhabbet kabul etmez |
| Çocuk/ailе verisi | Prompt'ta gerçek kişi verisi yok; aile ilişkileri kurgudur (P6.5) |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Ana sayfa yükleme (gece/dark) | Dark temada hızlı açılış; melankoli listesi üstte; kayma yok | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** (P8.1) | 1.4.3 (dark/light metin kontrastı) |
| 2 | Giriş / kayıt akışı | Türkçe karakterli ad kabul; ≤3 adım; net hata geri bildirimi | Manuel + form assertion | FCP **1 sn** (P8.1) | 3.3.8 |
| 3 | Karşılama / melankoli listesi | Öneriler derin dinleyiciye uygun; küratörlü liste görünür | İçerik assertion | — | — |
| 4 | Arama — fuzzy / typo (S2 yetişkin) | Yazım hatasında sonuç döner; sonuç sayfası hızlı | DOM assertion + timing | INP ≤ **200 ms** (P8.1) | 2.4.7 |
| 5 | Gelişmiş filtre — yıl, süre, BPM (S2 yetişkin) | Filtre birikir; temizleme tek dokunuş; odak görünür | Manuel + DOM assertion | — | 2.4.7 |
| 6 | Sanatçı sayfası / diskografi | Kronolojik diskografi; benzer sanatçılar | Manuel + screenshot | — | — |
| 7 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; kayma yok | Trace + console log | INP ≤ **200 ms** · CLS ≤ **0.1** (P8.1) | 2.4.11 |
| 8 | Player kontrol (oynat / duraklat) | Net oynat kontrolü; hedef ≥ **24×24 CSS px**; klavye erişimi | Erişilebilirlik audit | INP ≤ **200 ms** (P8.1) | 2.5.8 |
| 9 | Kuyruk / sıra değiştirme (sürükleme) | Sürükleme olmadan da sıralama yapılabilen alternatif sunulur | DOM assertion + a11y audit | — | 2.5.7 |
| 10 | Çalma listesi oluştur / düzenle | Türkçe ad kabul; kayıt hızlı; kalıcılık korunur | DOM assertion + LocalStorage | — | 3.3.7 |
| 11 | Toplu indirme + kalite seçimi (S4 yetişkin) | Toplu seçim çalışır; ilerleme görünür; aynı bilgi tekrar istenmez | Manuel + DOM assertion | — | 3.3.7 |
| 12 | Ayarlar — gece modu / düşük kontrast tercihi (S5) | Tercih kaydedilir, oturumlar ve cihazlar arası korunur | Manuel + LocalStorage kontrolü | — | 1.4.3 |
| 13 | Ayarlar — veri dışa aktarma + tüm cihazlardan çıkış (S5 yetişkin) | CSV/JSON çıktı; oturum kırma görünür geri bildirim verir | Manuel + oturum assertion | — | 3.2.6 |
| 14 | Bağlantı yavaşlatma ("Slow 4G") | Koşulda hata yok; yeniden deneme seçeneği sunulur | Network throttling + trace | **150 ms / 1.6 Mbps ↓ / 750 Kbps ↑** (P8.3) | — |
| 15 | Hata sayfaları (404 / timeout) | Sade Türkçe; hata kodu + "Tekrar Dene"; ayrıntı hover/focus'ta açılır | Manuel + screenshot | — | 1.4.13 |
| 16 | Tema geçişi (dark ↔ light, otomatik) | Geçiş kayıpsız; eşikler korunur; tercih kalıcı | Manuel + a11y audit | — | 1.4.11 |
| 17 | Erişilebilirlik denetimi | Lighthouse A11y + axe; belirtilen kriterlerde ihlal yok | Lighthouse / axe | — | 1.4.3 · 1.4.11 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 · 3.2.6 · 3.3.7 · 3.3.8 |

*Asgari 10 satır (şablon §3.5.10) — **17 satır** yazıldı. Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik (research-bank P8 değeri)` · `WCAG kriteri`. Metrikler yalnız research-bank P8'den (P8.1/P8.3 `VERIFIED`); WCAG kodları yalnız P5.2/P5.4 listesinden; persona'ya özel eşik yazılmadı (X-10 → gerekirse `⚠️ DERIVED`). Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, meslek, ulaşım, online rumuz | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (35) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 satır 62 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Yaş çelişkisi: eski dosya **38** ↔ SSOT **35** | Çelişki — iki değer kaydedildi | eski salt-okunur persona dosyası (38) | [[personas/index]] §6.3 (35) | `⚠️ VERIFICATION REQUIRED` — iki değer §7.2'de; 35 kullanıldı |
| 4 | Doğum tarihi 12 Kasım 1991 | Türetilmiş kurgu (yaş tutarlılığı) | eski dosya gün/ay (12 Kasım) + yaş 35 | — | `⚠️ DERIVED` |
| 5 | `veli_onayı_gerekli: false` (yetişkin) | Kural sonucu | research-bank P6.3/P6.4 (16 yaş altı politikası bu persona için geçersiz) | — | `Kaynak: kurgusal + kural (P6.4 uygulaması)` |
| 6 | Yaş karşılaştırması ("KVKK 16" = YANLIŞ; TMK 18 / GDPR 16 / COPPA 13) | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 7 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | Sağlık verisi yazılmadı (m.6 hassasiyeti) | Kural uygulaması | research-bank P6.5 | — | `Kaynak: [k1] + [k2]` (P6 `VERIFIED`) + uygulama: yazılmadı |
| 9 | Big Five puanları (40 / 50 / 90 / 30 / 70) | Kurgusal (eski `/10` × 10) | eski salt-okunur dosya (4/10, 5/10, 9/10, 3/10, 7/10) | — | `Kaynak: kurgusal (persona verisi)` |
| 10 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 11 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 12 | N ters kodlama yönü + boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 13 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 14 | Mood küme adı (Melankolik) | Vault referanslı atama | [[personas/index]] §6.3 satır 62 | [[mood-taxonomy]] §3.2.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 15 | Küme tanımı alıntısı + test etkisi (S2/S5, dark kontrast) | Vault verisi | [[mood-taxonomy]] §3.2.3 | [[personas/test-scenarios-mapping]] §4.5 satır 207 | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 16 | İkincil mood "Güçlü/Dirençli" | Listede yok | eski salt-okunur persona dosyası | mood-taxonomy §3 (yok) | `⚠️ VERIFICATION REQUIRED` — yazılmadı; §7.2 çelişki |
| 17 | Melankolik küme BPM **60-90** | Eski taksonomi (doğrulanmadı) | mood-taxonomy §3.2.3 + §1.2 toplu etiket | research-bank P4 (karşılığı yok) | `⚠️ VERIFICATION REQUIRED` |
| 18 | Kişisel tempo **60-100 BPM** | Persona tercihi (kurgusal) | eski salt-okunur dosya (tercih bağlamı) | — | `Kaynak: kurgusal (persona verisi)` |
| 19 | Tür BPM aralıkları: Pop **80-120** (P4.4) · boom-bap **85-95** / trap **70-110** (P4.5) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute + nevamuzik.com.tr | nevamuzik.com.tr + vocuno.com/tr/bpm-algilayıcı | `Kaynak: [k1] + [k2]` (P4.4/P4.5 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 20 | Persona türlerinin (klasik, alternatif rock/indie, TSM, jazz) P4.4'te karşılığı + şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.4 (liste dışı) + P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — tür/şarkı BPM iddiası yazılmadı |
| 21 | Favori sanatçılar (Sezen Aksu, Mabel Matiz, Radiohead, Billie Holiday) | Persona tercihi (kurgusal); realitesi P4 dışı | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 22 | Teoman — alternatif rock; "melankolik, akustik ağırlıklı" | Gerçek-dünya | tr.wikipedia.org/wiki/Teoman | prmedya.com | `Kaynak: [k1] + [k2]` (P4.6 `VERIFIED`) |
| 23 | Cihaz: iPhone 15 Pro Max + masaüstü/tablet/kulaklık/ev hoparlörü/araç multimedya | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 24 | Test viewport ≈**360 × 780** (DPR 3) / ≈**412 × 915** (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080 × 2340 ÷ DPR (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 25 | A34 ekranı **1080 × 2340** (6.6" Super AMOLED) — platform test cihazı | Gerçek-dünya | gsmarena.com/samsung_galaxy_a34-12074.php | samsung.com resmi spec + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 26 | Tarayıcı sürümü, OS payı, internet hızı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 27 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.2/P5.4 `VERIFIED`) |
| 28 | Persona renk/kontrast kombinasyonu | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 29 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.1.1, 2.2.1, 3.3.1, 3.2.2, 4.1.2 …) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 30 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.1/P8.6 `VERIFIED`) |
| 31 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.3/P8.6 `VERIFIED`) |
| 32 | Eski dosyadaki üniversite adları, maaş aralığı, sağlık ayrıntıları, gelir/yüzde istatistikleri ve kaynak listesi (TÜİK, MÜ-YAP, BTK, Apple, Ipsos iddiaları) | Bankada YOK | research-bank X-6 + P6.5 + P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ceyda-demir-melankolik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; sağlık verisi kurgusal (P6.5) | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 15 Pro Max viewport = 430×932` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka A34 türevi ⚠️ DERIVED (1080×2340 ÷ DPR)` |
| `Chopin Nocturne = 66 BPM` | Tür aralığı yok (P4.4/P4.5 liste dışı) → `⚠️ VERIFICATION REQUIRED`; kişisel 60-100 tercihi kurgusal |
| `KVKK rıza yaşı 16` | `TMK m.11 = 18; GDPR m.8 = 16; COPPA = 13; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| Yaş = 38 (eski dosya tek başına) | Yaş = 35 (index §6.3) + her iki değer §7.2'de `⚠️` ile kayıtlı |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.3 + [[personas/index]] §6.3 + [[personas/test-scenarios-mapping]] §4.4-§4.5 oku | Etiketli veri + küme adı + yaş/mood + senaryo satırları |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 (6 sütun) → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/documentation/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri); mood-taxonomy §3.2.3 Melankolik satırı; mapping §4.4 S2/S4/S5 + §4.5 Melankolik satırı | Her üretimde |
| 3 | Eski dosya: `.ai/.personas/yetiskin-kadin/` salt-okunur eski vault; yalnız kurgusal kimlik/müzik/rutin taşınır (yaş 38 → 35 düzeltmesi §7.2'ye) | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX/MAPPING OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 (6 SÜTUN) → §6.1 → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| §3'te kalmış `{{VARIABLE}}` | Yer tutucu sızıntısı | Yer tutucuyu doldur veya sil (yalnız §4/§5'te kural metni olabilir) |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Ceyda için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Yaş/ikincil mood çelişkinin susulması | Tek değer yazılıp diğeri silinirse | §7.2'ye iki değer + `⚠️` ile kaydet (indirgeme yasak) |

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
- [ ] Test Adımları tablosu ≥ 10 satır ve **6 sütun** (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, çift süslü parantez yer tutucusu kalmamış (yalnız §4/§5'te kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] Yetişkin persona: `veli_onayı_gerekli: false`; 16 yaş altı KVKK notu YOK (yalnız genel kural satırı)
- [ ] Yaş 35 (index §6.3); eski 38 değeri §7.2'de `⚠️` ile saklı
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; persona cihazları `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5.2/P5.4 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4/P4.5); şarkı BPM'i yok; melankolik 60-90 `⚠️`
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 17 (asgari 10) — 6 sütun |
| Kaynak & Doğrulama satırı | 32 |
| Frontmatter alan | 7 zorunlu + 5 ek (12) |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi (§6.3 satır 62) | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.3 Melankolik) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi (§4.4-§4.5) | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | **38** | **35** | [[personas/index]] §6.3 kazanır; iki değer de bu satırda `⚠️` ile korunur, indirgenmez |
| Doğum tarihi | 12 Kasım 1988 | 12 Kasım 1991 | Yaş 35 ile tutarlılık için kurgusal türetme (`⚠️ DERIVED`) |
| İkincil mood | Güçlü/Dirençli | Yok | "Güçlü/Dirençli" mood-taxonomy §3 listesinde YOK → yazılmadı, §7.2'de saklı |
| Big Five ölçeği | `/10` (4, 5, 9, 3, 7) | 0-100 (40, 50, 90, 30, 70) | ×10 normalize → `⚠️ DERIVED` (P7.4); puanlar kurgusal |
| BPM | Kişisel 60-100 + taksonomi Melankolik 60-90 | İkisi de `⚠️ VERIFICATION REQUIRED` | P4.4/P4.5 dışı tür-BPM yazılmadı; iki aralık da kayıtlı |
| Birincil cihaz + diğer cihazlar | iPhone 15 Pro Max, masaüstü, tablet, kulaklık, araç multimedya, ev hoparlörü (spec'li) | Test viewport: banka A34 türevi (`1080×2340` VERIFIED → `360×780` `⚠️ DERIVED`) | [[research-bank]] P3 — persona cihaz spec'i yazılmadı (X-1) |
| Üniversite adları | İki üniversitenin adı ve yılları | yazılmadı | research-bank X-6 (kapsam dışı) → `⚠️ VERIFICATION REQUIRED` |
| Maaş / gelir aralığı | Aylık gelir aralığı | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma; ADR-005) |
| Sağlık ayrıntıları (m.6) | Migren, gastrit, sigara, uyku süresi | yazılmadı | P6.5 — sağlık verisi kurgusal olmak zorunda; bu dosyada silindi |
| Kaynak listesi (TÜİK, MÜ-YAP, BTK, Apple, Ipsos yüzdeleri) | Kaynaksız istatistik/yüzde iddiaları | yazılmadı | research-bank P1-P8 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` |
| Yaş sınırı dili | "KVKK ve GDPR" hassasiyeti (sayı yok) | TMK m.11 = 18 · GDPR m.8 = 16 · COPPA = 13 | "KVKK 16" ifadesi YANLIŞ (P6.5) → bu dosyada sayılar P6.3'ten |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı, yaş index §6.3'e göre düzeltildi (38→35) ve iki değer de çelişki tablosunda saklandı; yetişkin statüsü (veli onayı false), ikincil mood çelişki kaydı, sağlık verisi kırpımı, 6 sütunlu 17 test adımı ve 32 satırlık Kaynak & Doğrulama tablosu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/yetiskin-kadin/ceyda-demir-melankolik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
