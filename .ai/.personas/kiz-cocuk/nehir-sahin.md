---
title: "CoreMusic — Persona: Nehir Şahin"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/nehir-sahin"
updated: 2026-09-26
group: kiz-cocuk
age: 6
mood: Lider
persona_id: CM-NS-06-LID-ADA
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Nehir Şahin

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[personas/mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **kiz-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 6 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır — odağı **sıralama/önceliklendirme kontrolleri, rol/izin akışı (ebeveyn ≠ çocuk) ve amaca göre playlist düzeni**nin Lider kümesiyle test edilmesidir.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[personas/mood-taxonomy]] (Lider / Kaşif), veri → [[personas/research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | kiz-cocuk (4-11) |
| Birincil mood | Lider ([[personas/index]] §6.3 ataması — eski dosyayla BİREBİR uyumlu, §7.2) |
| Test odağı | Sıralama/önceliklendirme kontrolleri, rol/izin akışı (ebeveyn ≠ çocuk), düzenli playlist yönetimi, güven veren hata ekranları |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[personas/research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Çocuk modu ana sayfa / keşif | Level 2 + 3 | Lider: sıralama ve önceliklendirme kontrolleri görünür |
| Playlist yönetimi (oluşturma/düzenleme) | Level 2 + 3 | Lider: 12 organize playlist davranışı — sürükleme yerine tek dokunuş |
| Boş durum / hata ekranları | Level 2 | Kaşif ikincil: güven veren metin, hata mesajı tonu |
| Geri navigasyon & odak | Level 2 + 3 | Büyük geri tuşu beklentisi (2.4.11), sade düzen |
| Ebeveyn kilidi & rol/izin akışı | Level 2 | 3.3.8 erişilebilir kimlik doğrulama; ebeveyn ≠ çocuk ayrımı görünür |
| Erişilebilirlik denetimi (kontrast / hedef boyu) | Level 2 | 2.5.8 ≥24×24 px, 1.4.3 ≥4.5:1 denetimi |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki ayrıntılı fiziksel/ekonomik veri bu şablonda yasaktır (§3.5.2 → 5-8 satır; persentil, ayakkabı no, kan grubu, aile geliri YAZILMAZ); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); şarkı bazlı BPM yazılmaz (P4.7 / X-2).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[personas/research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[personas/mood-taxonomy]] | Küme adı + tanım alıntısı (yalnız §3 listesi) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Nehir Şahin | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 6 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3 — eski dosyayla uyumlu) |
| **Doğum Tarihi** | 15 Ocak 2020 | Kaynak: kurgusal (persona verisi; yaş 6 ile tutarlılık için korundu — eski kayıtla aynı) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Adana / Çukurova / Güzelyalı Mahallesi | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Adana TED Koleji, 1. sınıf | Kaynak: kurgusal (persona verisi; okul adı kurgusal) |
| **Kardeş** | Kaan (2 yaş) — abla olarak sorumluluk payı | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Servis + aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (ebeveyn hesabında alt profil) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-NS-06-LID-ADA` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `NS` | Ad + soyad ilk harfleri (Nehir Şahin → N, S) |
| `YY` | `06` | Yaş 2 hane (6 → `06`) |
| `ZZZ` | `LID` | Mood tür kodu (Lider → LID) |
| `XXX` | `ADA` | Şehir kodu (Adana → ADA) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 6 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki ölçüm/ürün verileri (persentil, ayakkabı numarası, kan grubu, burç, aile geliri) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 119 cm (uzun boylu — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 21 kg, atletik (yüzme + satranç — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah düz örgü saç, koyu kahverengi göz, buğday ten | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; okul formasını düzenli giyer | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Düzenli, ütülü (lacivert/bordo/beyaz); profil fotoğrafında dik poz, kararlı ifade (kontrast: koyu kıyafet × buğday ten — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | 6 yaş — ince motor gelişmekte; sürükleme yerine tek dokunuş ve ≥24×24 px hedefler şart; okuma-yazma gelişiyor → kısa etiketler | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 80 | Sınıf başkanı ve organizatör; grubu yönlendirmekten çekinmez, sahneyi sever. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | Kendi fikrini savunur ama haksızsa özür diler; arabuluculuk yapar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 90 | Ödevlerini hatırlatılmadan yapar, saatine bakar; playlist'lerini düzenli tutar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 20 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 20 = yüksek denge; duygularını kontrol eder, yenilgiyi "bir dahaki sefere" diye karşılar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 70 | Yeni şeyleri dener ama önce planlar; Kaşif ikincil kümesi bu davranışı besler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir**; eski dosyadaki 8/10 benzeri skorlar 0-100 ölçeğe kurgusal olarak ölçeklendi (§7.2) |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness) → P7.5 kuralı ("kod esastır"); mood-taxonomy §3.3 #12'nin "Yüksek O, yüksek E" eğilimi bu eşlemeyle uyumludur |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Lider |
| **İkincil küme** | Kaşif (kurgusal ikincil atama — eski dosyada ikincil mood alanı yok) |
| **Küme tanımı (alıntı)** | "Öncü, yönlendiren; listeleri ve sıralamayı kontrol eder" ([[personas/mood-taxonomy]] §3.3 #12) · ikincil: "Tüm türleri deneyen, yeni çıkanları ve deneysel müzikleri kovalayan keşif kullanıcısı" (§3.2.6) |
| **Tetikleyici durumlar** | Sıralanabilir liste/öncelik kontrolleri ve net kurallar (yaklaşma); kontrolsüz karmaşık düzen, izinsiz değişiklik ve geri tuşunun kaybolması (geri çekilme) |
| **UI etkisi** | Lider: "Sıralama/önceliklendirme kontrolleri, rol/izin akışı (ebeveyn ≠ çocuk)" + müzik eğilimi "Dans/pop, kararlı ritim" ([[personas/mood-taxonomy]] §3.3 #12); Kaşif ikincilken "Keşif odaklı widget'lar, öneri blokları" (§3.2.6) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Lider) — eski dosya mood'uyla BİREBİR aynı (§7.2); ikincil = kurgusal atama (Kaşif, §3.2.6) |
| Big Five uyumu | §3.3 #12 eğilimi "Yüksek O, yüksek E" → bu dosyada O=70, E=80 (tutarlı) |
| Test etkisi | ADR-023 satır 12 (geri tuşu görünürlüğü) + sıralama kontrolleri — [[personas/mood-taxonomy]] |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Sıralama kontrolü (Lider) | Playlist'te sıralama/önceliklendirme kontrolü görünür ve çalışır | DOM assertion + Manuel |
| 2 | Rol/izin akışı (Lider) | Çocuk ve ebeveyn rolleri ayrık görünür (ebeveyn ≠ çocuk); çocuk ebeveyn ekranına giremez | Manuel + form assertion |
| 3 | Geri tuşu beklentisi (ADR-023) | Geri tuşu her adımda görünür, kaybolmaz (2.4.11) | DOM assertion |
| 4 | Kararlı ritim (Lider) | Dans/pop önerileri kararlı; atlamasız akış, CLS ≤ **0.1** | Performance trace |
| 5 | Keşif blokları (Kaşif ikincil) | Öneri blokları keşif odaklı; tür çeşitliliği görünür | A11y audit + DOM |
| 6 | Düzen ve kontrol (Lider) | Sürpriz popup yok; değişiklik onaylı | Manuel denetim |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Klasik Müzik 2) Motivasyon/Epik 3) Seçme Türkçe Pop 4) Epik Film Müzikleri 5) Türk Sanat Müziği | Kaynak: kurgusal (persona verisi; sıra: [[personas/index]] §6.3 + eski salt-okunur kaynak) |
| **Favori sanatçılar (5)** | Fazıl Say, Sezen Aksu, Barış Manço, Tarkan, Mazhar Alanson | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2/P4.3 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Seçme Türkçe Pop için **80-120** (P4.4 satırı); Klasik/Motivasyon-Epik/Epik Film/TSM için P4.4/P4.5'te tür satırı **YOK** → yazılmadı | Pop: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4 `VERIFIED (ikincil)`) · diğerleri: `⚠️ VERIFICATION REQUIRED` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi (amaca göre)** | Odak: 70-90 BPM · Enerji: 110-130 BPM · Sakinleşme: 60-80 BPM | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil; eski dosyanın kaynaksız "80-130" satırı §7.2'de) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 16:30-17:30 (ödev — odak listesi), 20:00-20:30 (uyku öncesi sakin) · Hafta sonu 10:00-11:00 (satranç öncesi motivasyon), 18:00-19:00 (aile) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (çocuk modu) + okul etkinlikleri için ortak ekran | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Kaşif ikincil: haftada 4-6 yeni parça; amaca uygunsa listesine alır | Kaynak: kurgusal (persona verisi; Kaşif kümesi: [[personas/mood-taxonomy]] §3.2.6) |
| **Premium olasılığı** | %60 (amaca hizmet ediyorsa ve reklam odağı bozuyorsa ailece karar) | Kaynak: kurgusal (persona verisi) |
| **Playlist düzeni** | 12 organize playlist, ~95 favori; kriter: "amaca hizmet ediyor mu?" | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Klasik Müzik | Ders çalışırken odaklanma sağlaması "planlı" oluşu |
| 2 | Motivasyon/Epik | Yüzme yarışı öncesi enerji verir, "kazanma" hissi |
| 3 | Seçme Türkçe Pop | Sınıf etkinliklerinde arkadaşlarıyla eşleşir |
| 4 | Epik Film Müzikleri | Film sahneleri gibi "büyük kararlar" hissi |
| 5 | Türk Sanat Müziği | Annesiyle hafta sonu dinleme rutini |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:00 | Kahvaltı + gün planı | Seçme Türkçe pop (hafif) |
| Hafta içi | 16:30-17:30 | Ödev | Klasik (odak listesi) |
| Hafta içi | 18:00-19:00 | Yüzme (haftada 2) | Motivasyon/epik |
| Hafta içi | 20:00-20:30 | Uyku öncesi | TSM / sakin (60-80 BPM, kurgu) |
| Hafta sonu | 10:00-11:00 | Satranç kulübü öncesi | Epik film müzikleri |
| Hafta sonu | 18:00-19:00 | Aile dinleme saati | TSM (anneyle) |

*Not: BPM satırları yalnız tür aralığı düzeyindedir (P4.4 — yalnız Pop); Klasik/Motivasyon-Epik/Epik Film/TSM bankada tür-BPM verisi YOK → yazılmadı. "Şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Amaca göre BPM tercihleri kurgusal persona tercihidir, ölçüm değildir.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 60 Mbps (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (okul/plan modu tercihi) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 50 dk/gün, hafta sonu 80 dk/gün (ebeveyn limiti) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 5/10 — okur, listeleri düzenler, arar; ayar ve paylaşım yapmaz | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | iPad Air (eski kayıt: "okul verdi") — spec/marka doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | 6 yaş; ince motor gelişmekte | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | okuma-yazma gelişiyor; 10-15 dk odak | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Aile geliri/kan grubu gibi veriler 6698 kapsamında hassas sayılabilir → persona'da **kurgusal olması zorunlu**, bu dosyada YAZILMADI |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Minyatür organizatör: sınıf başkanı, etkinlik planlayıcısı; kardeşi Kaan'ın rutinlerini takip eder.
- Müziği amaca göre seçer: odak, motivasyon, sakinleşme — playlist'leri bu başlıkları taşır.
- Düzen ve kural sever; izinsiz değişiklik ve kontrolsüz düzen onu rahatsız eder.
- Adalet duygusu güçlü: haksızlıkta itiraz eder, gerektiğinde özür diler (arabulucu).
- Yenilgiyi çabuk toparlar; "bir dahaki sefere" mottosu.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — çocuk "ister", anne/baba karar verir | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 8 sn; kontrol edemediği düzenlerde hızlı sıkılır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Hata görürse nedenini ister; "Tekrar Dene" + açıklama şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 4/10 — odak modunda reklamı "gereksiz" bulur, ister | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Önce kuralları öğrenir, sonra dener; yönerge ister | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Sırayı kendi kurar; adaletsizlikte sesini yükseltir | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 20:30 sonrası karmaşık akış yerine "Sakinleşme" listesi | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Sıralanabilir, kontrolü görünür liste görünümü → keşif davranışı tetiklenir | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] Lider + Kaşif ikincil küme |

*Erişilebilirlik etkisi (özet):* 6 yaş ince motor + okuma-yazma evresi → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sürükleme yerine tek dokunuş (2.5.7) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Nehir Şahin'sun. 6 yaşında, Adana/Çukurova'da yaşıyorsun, Adana TED Koleji 1. sınıf öğrencisisin.
Birincil mood'un Lider, ikincil olarak Kaşif'sin: sınıf başkanısın, her şeyi planlar ve organize edersin; listeleri ve sıralamayı sen kontrol etmek istersin ama yeni türleri de merakla denersin.
Doktor bir annen ve avukat bir baban var; 2 yaşındaki kardeşin Kaan'ın rutinlerini takip edersin. Yüzme yaparsın, satranç kulübüne gidersin.
Müzik zevkin: Klasik, Motivasyon/Epik, Seçme Türkçe Pop, Epik Film Müzikleri, Türk Sanat Müziği. En sevdiklerin: Fazıl Say, Sezen Aksu, Barış Manço, Tarkan, Mazhar Alanson. Müziği amaca göre seçersin: odak (70-90 BPM), enerji (110-130 BPM), sakinleşme (60-80 BPM) — kurgusal tercih.
Samsung Galaxy A34 5G kullanıyorsun, light temadasın, internetin evde 60 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; büyük, sabit ve sade dokunma hedefleri beklersin.
Sabır eşiğin 8 sn; satın alma tamamen ailenin onayına bağlıdır; ebeveyn ekranına sen giremezsin.
Test edilecek akışlar: sıralama/önceliklendirme kontrolleri, playlist oluşturma ve düzenleme, rol/izin akışı (ebeveyn ≠ çocuk), güven veren boş durum metni, çalma/duraklat, geri tuşu, ebeveyn kilidi, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Yeni öneriyi "amaca hizmet ediyor mu?" diye değerlendirir; hizmet etmiyorsa listesine almaz.
- Sürpriz popup görürsen düzeni bozulduğu için çıkar; onaylı, öngörülebilir bildirim istersin.
- Boş listede "kaydın yok" gibi güven veren metin ve büyük bir "Keşfet" butonu beklersin.
- Türkçe telaffuzun nettir; arayüzde kısa, "sen" diliyle yazılmış ve net kurallar içeren metinler beklersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 6 yaş "karmaşık komut" yerine net davranış ister |
| Sürpriz yasak | Düzen/kontrol ihtiyacı nedeniyle prompt'ta ani korkutucu/sürpriz senaryo yok |
| Rol akışı | "Ebeveyn ekranına sen giremezsin" satırı rol/izin akışı testinin girdisidir |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |
|---|------|-------|----------|-----------|--------|------|
| 1 | Ana sayfa yükleme (çocuk modu) | CoreMusic'i aç, sade düzeni bekle | Büyük kapak kartları üstte, sakin yerleşim | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** | 1.4.11 ≥3:1 |
| 2 | Sıralama kontrolü (Lider) | Playlist'i sırala/önceliklendir | Kontrol görünür ve çalışır; durum kalıcı | DOM assertion + Manuel | — | 2.5.8 ≥24×24 px |
| 3 | Rol/izin akışı | Çocuk rolünden ebeveyn ekranına geç dene | Geçiş reddedilir; rol ayrımı görünür | Manuel + form assertion | — | 3.3.8 |
| 4 | Şarkı çalma (play / duraklat) | Çal, duraklat, kaldığı yerden devam | İlk ses < 1 sn; büyük oynat kontrolü | Trace + console log | ilk ses < **1 sn** | 2.4.7 |
| 5 | Amaca göre playlist (odak) | Odak listesini aç, oynat | Klasik akışı; atlamasız | Manuel + trace | INP ≤ **200 ms** | — |
| 6 | Geri navigasyon | 5 adımı geri geri gez | Geri tuşu kaybolmaz; odak korunur | DOM assertion + klavye testi | — | 2.4.11 |
| 7 | Favori (kalp) ekleme | Kalbe dokun, uygulamayı kapat-aç | Görsel geri bildirim; durum kalıcı | DOM assertion + LocalStorage kontrolü | — | 2.5.8 ≥24×24 px |
| 8 | Sürpriz kontrolü (popup) | Oturumda 5 dk gez | Onaysız sürpriz popup/açılır pencere yok | Manuel denetim + konsol | — | 3.3.8 |
| 9 | Ebeveyn kilidi (ayar geçişi) | Çocuk modu dışına çıkış dene | Kilit devrede; 3.3.8 gözetilir | Manuel + form assertion | — | 3.3.8 |
| 10 | SPA navigasyon (sayfalar arası) | Kategori değiştir | Müzik kesintisiz; çakışma yok | Trace + console log | CLS ≤ **0.1** | — |
| 11 | Bağlantı yavaşlatma | Slow 4G koşulunda ana sayfa + çalma | Hata yok; yaşa uygun yükleme animasyonu | Network throttling + trace | **150 ms / 1.6 Mbps / 750 Kbps** (P8.3) | — |
| 12 | Hata sayfaları (404 / kopma) | Bozuk URL aç | Türkçe, güven veren metin; büyük "Tekrar Dene" | Manuel + screenshot | — | 2.5.8 ≥24×24 px |
| 13 | Erişilebilirlik denetimi | Lighthouse + axe koş | 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | A11y skoru raporlanır | 1.4.3 ≥4.5:1 |
| 14 | Kırılma noktası kontrolü | `360 × 780` ve `412 × 915` görünümlerini aç | Düzen bozulmaz; kapaklar taşmaz | Playwright `setViewportSize` + screenshot | — | 1.4.11 ≥3:1 |
| 15 | Türkçe karakter içeriği | ç/ğ/ı/İ/ö/ş/ü içeren menü ve şarkı adlarını aç | Karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | mojibake **0** | — |
| 16 | Odak sırası (klavye/gezinme) | Klavye ile 10 adımı gez | Odak mantıklı ve görünür; gizli odak yok | Klavye navigasyon testi + DOM assertion | — | 2.4.7 |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Zorunlu sütunlar `Adım` · `Beklenen` · `Doğrulama` mevcut; görev gereği `Eylem` · `Metrik` · `WCAG` sütunları genişletilmiştir. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

**Ek doğrulama notları (test koşusundan önce — research-bank P8 `VERIFIED`):**

- **TBT:** > 50 ms ise uzun görev (long task) main thread'i bloklar; TBT, INP'nin laboratuvar proxy'sidir. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/optimize-vitals-lighthouse — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Test kırılım noktaları (P8.5 — 9 adet):** 320 × 568 · 375 × 667 · 375 × 812 · 414 × 896 · 768 × 1024 · 1280 × 720 · 1600 × 1200 · 1920 × 1080 · 2560 × 1440 — persona viewport'u (`360 × 780` / `412 × 915`, `⚠️ DERIVED` — §3) bu setle çapraz kontrol edilir. `Kaynak: [k1] + [k2]` (playwright.dev/docs/emulation + mintlify mobile-emulation — research-bank P8.5 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; Test Adımları tablosundaki eşikler research-bank P8'den alınır, persona'ya özel metrik hedefi bu dosyaya yazılmaz. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, kardeş, aile meslekleri (doktor anne, avukat baba) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (6) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | eski salt-okunur dosya (aynı değer) | `Kaynak: kurgusal (persona verisi; index §6.3 ataması — çelişki yok)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi + eski 8/10 → 80 ölçeklemesi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.3 #12 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adları (Lider / Kaşif) | Vault referanslı atama | [[personas/mood-taxonomy]] §3 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[personas/mood-taxonomy]] §3.3 #12 + §3.2.6 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 13 | Lider Big Five eğilimi ("Yüksek O, yüksek E") | Vault verisi | [[personas/mood-taxonomy]] §3.3 #12 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 14 | Pop tür BPM aralığı (80-120) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 15 | Klasik/Motivasyon-Epik/Epik Film/TSM tür-BPM aralıkları | Doğrulanmadı (bankada yok) | research-bank P4.4 (bu türler listede değil) | research-bank P4.5 | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 16 | Şarkı bazlı BPM (eski "80-130 amaca göre" satırı dahil) | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 17 | Favori sanatçı adları (Fazıl Say, Sezen Aksu, Barış Manço, Tarkan, Mazhar Alanson) | Persona tercihi (kurgusal); sanatçı realitesi P4.2/P4.3 dışında | research-bank P4.2/P4.3 (bu isimler listede YOK) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 18 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 19 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 20 | Diğer cihaz (iPad Air) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 21 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 22 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 23 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 24 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 25 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Eski dosyadaki ölçüm/ekonomik veriler (persentil, ayakkabı no, kan grubu, burç, aile geliri) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 28 | Eski cihaz/teknoloji değerleri (iPad Air, BPM 80-130) + mood/yaş çelişki kontrolü | Vault içi çelişki / banka dışı | eski salt-okunur persona dosyası | [[personas/index]] §6.3 + research-bank P3/P4 | Yaş/mood: çelişki YOK; cihaz: A34 kazanır; BPM: §7.2'de saklı |

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
| 3 | SSOT | Persona kataloğu [[personas/index]]; araştırma verisi [[personas/research-bank]] | İçerik silinir |
| 4 | Zero Hallucination (ADR-005) | Her satır §4.5 etiketi taşır; bankada olmayan iddia yazılmaz | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`nehir-sahin.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Epik film müziği 120 BPM` | Bankada tür-BPM yok → `⚠️ VERIFICATION REQUIRED`; P4.4 yalnız Pop/Hip Hop/House/Dance/Disco/Electro/Techno |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Aile geliri 120.000-150.000 TL/ay` (eski dosya) | Bankada YOK → yazılmadı; yalnız `Kaynak: kurgusal` kimlik satırı |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[personas/research-bank]] P3-P8 + [[personas/mood-taxonomy]] §3 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/documentation/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `coremusic.net.old/.ai/personas/kiz-cocuk/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 → §6.1 → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift `{{` | §4 bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Nehir için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
- [ ] Test Adımları tablosu ≥ 10 satır; zorunlu sütunlar `Adım` · `Beklenen` · `Doğrulama` mevcut (bu dosya `# / Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG` genişletilmiş biçimini kullanır)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 16 yaş altı: `veli_onayı_gerekli: true` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
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
| Test adımı | 16 (asgari 10) |
| Kaynak & Doğrulama satırı | 28 |
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
| `[[personas/research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[personas/mood-taxonomy]]` | Mood küme adları ve tanımları (§3.3 #12, §3.2.6) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 6 | 6 | ✅ Çelişki YOK — index §6.3 ile birebir aynı |
| Mood | Lider | Lider (birincil) / Kaşif (ikincil — kurgusal atama) | ✅ Çelişki YOK; ikincil küme eklendi |
| Sınıf | 1. sınıf | 1. sınıf | ✅ Çelişki YOK (yaş 6 ile tutarlı) |
| Birincil cihaz | iPad Air ("okul verdi") | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3 |
| iPad Air (marka/spec) | Cihaz detayları | yazılmadı | P3.3 / X-1 → `⚠️ VERIFICATION REQUIRED` |
| BPM (80-130 "amaca göre") | Kaynaksız tek satır | Pop 80-120 (P4.4 VERIFIED ikincil) + amaca göre tercih kurgu | Kurgu/gerçek ayrımı §3.5.5'te; diğer türler `⚠️ VERIFICATION REQUIRED` |
| Big Five (8/10, 6/10…) | 10'luk ölçek | 0-100 ölçek (80, 60, 90, 20, 70) | Kurgusal ölçekleme → `⚠️ DERIVED` (§3.5.3) |
| Burç / kan grubu / persentil / ayakkabı no | Mevcut | yazılmadı | Bankada YOK → `⚠️ VERIFICATION REQUIRED` (§6.1) |
| Aile geliri (120.000-150.000 TL/ay) | Mevcut | yazılmadı | Bankada YOK + KVKK hassasiyeti → yazılmadı |
| Şarkı listesi ("O Fortuna" vb. 5 parça) | Mevcut | yazılmadı | Şarkı bazlı veri kapsam dışı (X-2 dolaylı); sanatçı listesi taşındı |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı; yaş (6) ve mood (Lider) index §6.3 ile uyumlu — çelişki yok; bankada olmayan ölçüm/ekonomik veriler çıkarıldı; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/nehir-sahin
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
