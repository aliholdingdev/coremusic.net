---
title: "CoreMusic — Persona: Defne Demir"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/defne-demir"
updated: 2026-09-26
group: kiz-cocuk
age: 10
mood: Yaratıcı
persona_id: CM-DD-10-YAR-IZM
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Defne Demir

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **kiz-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 10 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Yaratıcı / Enerjik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | kiz-cocuk (4-11) |
| Birincil mood | Yaratıcı ([[personas/index]] §6.3 ataması) |
| Test odağı | Görsel-müzik eşleşmesi & albüm kapağı büyüklüğü, AudioVisualizer kalitesi, renk uyumu, dans edilebilir içerik keşfi, hızlı skip |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Çocuk modu ana sayfa / keşif | Level 2 + 3 | Yaratıcı görsel odak (kapak büyük, renk uyumu), Enerjik hızlı skip |
| Görsel tasarım / mood eşleşmesi / AudioVisualizer | Level 2 + 3 | §3.2.10 test etkisi: görsel kalite + çizim bütünlüğü (CLS ≤ 0.1) |
| Dans/kategori gezinme + büyük play & shuffle | Level 2 + 3 | İnce motor orta → büyük hedef (2.5.8), tek dokunuş oynat |
| Ebeveyn kilidi & rol/izin akışı | Level 2 | 3.3.8 erişilebilir kimlik doğrulama; çocuk ≠ yetişkin rol sınırı |
| Erişilebilirlik denetimi (kontrast / hedef boyu) | Level 2 | 2.5.8 ≥24×24 px, 1.4.3 ≥4.5:1 denetimi |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 500+ satırlık fiziksel/psikolojik detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] | Küme adı + tanım alıntısı (yalnız §3 listesi) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Defne Demir | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 10 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 22 Nisan 2016 | Kaynak: kurgusal (persona verisi; yaş 10 ile tutarlılık için türetildi — eski kaynakta 2021, §7.2) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | İzmir / Karşıyaka / Bostanlı | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Karşıyaka İlkokulu, 4. sınıf | Kaynak: kurgusal (persona verisi; yaş 10 ile tutarlılık için türetildi — eski kaynakta anasınıfı, §7.2) |
| **Ulaşım** | Yürüyerek (okul ~500 m, anneyle) + aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabında alt profil) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-DD-10-YAR-IZM` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `DD` | Ad + soyad ilk harfleri (Defne Demir → D, D) |
| `YY` | `10` | Yaş 2 hane (10 → `10`) |
| `ZZZ` | `YAR` | Mood tür kodu (Yaratıcı → YAR) |
| `XXX` | `IZM` | Şehir kodu (İzmir → IZM; Türkçe karakter katlamı: İ→I) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 10 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 500+ satırlık ayrıntı (ayakkabı numarası, kan grubu, burç, evcil hayvan listesi…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 145 cm (yaşıtlarına göre ortalama bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 36 kg, atletik (yüzme + dans) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Açık kumral dalgalı omuz hizası saç, açık kahverengi (bal) göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (sağlık taramaları normal — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Rengarenk tulum/şort; favori renkler kırmızı-pembe-turuncu; hareketi kısıtlamayan kıyafet (profil fotoğrafında sıcak tonlar — kontrast eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Kaba motor ileri, ince motor ortalama → hızlı ama hassas olmayan dokunma; yanlış yere basma eğilimi | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) + 2.5.7 (sürükleme) kriterleri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 85 | Sosyal kelebek: sahilde tanımadığı çocuklara "merhaba" der, 2 dakikada oyun arkadaşı olur; yalnız kalmaktan hoşlanmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 62 | Genelde uyumludur ama enerjisi taştığında "dur" komutunu duymaz; empati vardır, dürtüsü bazen önüne geçer. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 40 | Dağınıktır, eşyalarını kaybedir; nedeni tembellik değil bir sonraki aktiviteye odaklanmasıdır — hatırlatma ister. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 30 = dengeli taraf; çabuk üzülür kin tutmaz, ama öfkesi ani ve patlayıcıdır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 92 | Yeni dans figürü, yeni şarkı, yeni oyun — "yapamam" demez, "nasıl yapılır?" demeden atlar; korkusuz keşif. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Mood eğilimi çelişkisi | [[mood-taxonomy]] §3.2.10 Yaratıcı eğilimi "orta-düşük E" der (kurgusal eğilim); bu persona E=85 → persona puanı kurgusal kalır, çelişki §7.2'ye kayıtlıdır → `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon/mood-taksonomi satırındaki `A` harfi "Deneyime Açıklık" için kullanılmıştır (§3.2.10 "A") → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Yaratıcı |
| **İkincil küme** | Enerjik |
| **Küme tanımı (alıntı)** | "Görsel-müzik eşleşmesi seven; resim/yazı gibi yaratıcı çalışırken uzun oturum kurar" ([[mood-taxonomy]] §3.2.10) |
| **Tetikleyici durumlar** | Görsel-müzik eşleşmesi (albüm kapağı/renk), yaratıcı çalışma anı (resim, yazı) ve dans edilebilir ritim (Enerjik tetikleyicisi); görsel-mood uyumsuzluğu/bozuk çizim (şikâyet) |
| **UI etkisi** | Yaratıcı: "Görsel odaklı, albüm kapağı büyük, renk uyumu" + test etkisi "Görsel tasarım, mood eşleşmesi, AudioVisualizer kalitesi" ([[mood-taxonomy]] §3.2.10); Enerjik ikincilken canlı renkler, hızlı animasyon, anlık geri bildirim + kolay geri tuşu ([[mood-taxonomy]] §3.2.2) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Yaratıcı); ikincil = eski salt-okunur persona kaynağı (Enerjik) — çelişki kaydı §7.2'de |
| Test etkisi | ADR-023 satır 9 (kız çocuk grubu temsilcisi: içerik filtresi + ebeveyn kontrolü) — [[mood-taxonomy]] §3.6; Yaratıcı'ya özgü görsel/AudioVisualizer testleri §3.2.10 "Test etkisi" satırıdır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Görsel-müzik eşleşmesi (Yaratıcı) | Albüm kapağı büyük görünür, renk uyumu bozulmaz | Manuel + screenshot |
| 2 | AudioVisualizer kalitesi (Yaratıcı) | Çizim bütünlüğü, CLS ≤ **0.1**; kapak/animasyon kayması yok | Performance trace + DOM assertion |
| 3 | Sık skip (Enerjik) | Skip sonrası anlık geri bildirim, INP ≤ **200 ms** | Performance trace (P8.1 VERIFIED) |
| 4 | Geri tuşu beklentisi (ADR-023) | Geri tuşu her adımda görünür, kaybolmaz (2.4.11) | DOM assertion |
| 5 | Yaratıcı oturum sürekliliği | Uzun dinleme oturumunda oturum kopmaz, çalma sürer | Manuel + trace |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Latin / Salsa / Samba 2) Türkçe Pop (enerjik) 3) Çocuk dans şarkıları 4) Rock'n Roll / eski rock 5) Türk Halk Müziği (hareketli — zeybek/harmandalı) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Hadise, Gülşen, Kenan Doğulu, Ezo Sunal, Murat Boz | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2/P4.3 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **Yabancı favoriler (kurgusal)** | Shakira, Elvis Presley, Pitbull, KIDZ BOP Kids — dans partisi repertuvarı | Kaynak: kurgusal (persona verisi); gerçek-dünya sanatçı bilgisi `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Pop **80-120** · Dance **110-135** · Disco **100-130** · House **110-128** · Electro **90-130** | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM (bankada olmayan tür)** | Latin · Rock'n Roll · Türk Halk Müziği · "çocuk dans" eşlemesi → P4.4'te yok | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 kapsamı dışı; eşleme önermesi bile `⚠️ DERIVED` gerektirir) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Dans için hızlı: 120-150 aralığını sever, 110 altını "uyku şarkısı" diye reddeder | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:30-08:00 (sabah dansı), 15:30-16:30 (sahil/koşu), 19:45-20:30 (aile dans partisi) · Hafta sonu 14:00-15:00 (ev dans partisi), 19:30-20:30 (film müziği dansı) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (Çocuk Modu) + ev hoparlörü / TV | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Sosyal keşif: annesinin arabada açtığı şarkılar, dans kursu listeleri, ablasının dans videoları, YouTube dans keşifleri — tek başına aramaz | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %90 (reklam "dansımı bölüyor" — annesi onaylar) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Latin / Salsa / Samba | Vurmalılar ve trompet ritmi hissettiği an dans başlatır; "bu müzikte durmak imkansız" |
| 2 | Türkçe Pop (enerjik) | Anneyle arabada söylenen, nakaratı kolay kapılan dans edilebilir pop |
| 3 | Çocuk dans şarkıları | Dans kursunda öğrenilen koreografiler; arkadaşla grup dansı |
| 4 | Rock'n Roll / eski rock | Babasının Elvis şarkıları; rock'n roll dansı öğrenme çabası |
| 5 | Türk Halk Müziği (hareketli) | Dedesiyle izlenen zeybek/harmandalı — Ege bağı + dans |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:00 | Sabah enerji dansı | Latin / enerjik pop |
| Hafta içi | 08:15-08:30 | Okul yolu (anneyle şarkı) | Türkçe pop |
| Hafta içi | 09:00-15:00 | Okul (telefon/tablet yok) | — |
| Hafta içi | 15:30-16:30 | Sahilde koşu / oyun | Karışık hareketli |
| Hafta içi | 19:45-20:30 | Aile dans partisi | Latin / rock'n roll / pop |
| Hafta sonu | 14:00-15:00 | Ev dans partisi | Dans/pop (son ses) |
| Hafta sonu | 19:30-20:30 | Film müziği dansı | Müzikal / soundtrack |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Mood-taxonomy §3.2.10 Yaratıcı için "60-120 BPM" önerir (⚠️ işaretli kurgusal öneri) — o da kurgusal önermedir (§1.2); test hedefi olarak P4.4 tür aralıkları kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 50 Mbps VDSL (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (çocuk modu renkli varyantla) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 60 dk/gün, hafta sonu 90 dk/gün (ebeveyn limiti — Google Family Link benzeri kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 3/10 — video seçer/başlatır, sesi açar/kapar, şarja takar; metin girişi anne yardımıyla | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Lenovo Tab M10 Plus (10.3"), JBL Flip 6 hoparlör, anne telefonu, Vestel Smart TV — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); renk körlüğü yok, canlı renk tercihi | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — görsel-mood eşleşmesi testinin dayanağı | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal; yüksek sesle dinleme eğilimi (anne uyarır) | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kaba motor ileri, ince motor ortalama; hızlı ama hassas değil | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş seçeneği (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | 4. sınıf; odak 10-15 dk (dans/müzikte 25+ dk) | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn kilidi · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Dikkat süresi / motor gelişim" ifadeleri 6698 m.6 kapsamında veri sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Büyük buton dp hedefleri (88×88 vb.) | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Yaratıcı: resim/yazı yaparken uzun dinleme oturumu kurar; albüm kapağı ve renk uyumuna duyarlıdır, görsel bozukluğu hemen fark eder.
- Enerjik ikincil küme: sabah yatakta zıplar, "yoruldun mu?" sorusunun cevabı hep "hayır"dır; müziği duyar duymaz dans eder.
- Sosyal: sahilde tanımadığı çocuğa merhaba der; oyunun/koreografinin kurallarını kendisi belirler.
- Dürtüsellik + dağınıklık: "topla" dendiğinde "sonra" der; arayüzde uzun onay zincirleri ve karmaşık akışlar düşmanıdır.
- Hızlı karar: ilk görende karar kılar — geri tuşu ve iptal çok görünür olmalı.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — Defne "ister", anne (stüdyo sahibi) içerik ve süre onayı verir | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 3 sn'de sıkılır; yükleme spinner'ına en fazla 8 sn dayanır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Dans ortasında hata görürse "dansımı bölüyor!" der; teknik metin görürse bırakır — dostça dil şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 1/10 — reklam gelirse oynamayı durdurur, sıkılır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + kinestetik (yaparak-öğrenme); koreografiyi izleyerek kopyalar, uzun metin okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Dansı/grup oyununu kendisi yönetir; arkadaşının temposuna yetişemeyince oyunu bırakıp yenisine geçer | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:00 sonrası dikkat 5 dk'ya düşer; karmaşık akışlar reddedilir | Kaynak: kurgusal (persona verisi) |
| **Kontrol tetikleyicisi** | Hareket/ritim tetikli: dans edilebilir akış + büyük play → güven; uzun sessiz beklemede öfke | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Yaratıcı birincil + Enerjik ikincil küme |

*Erişilebilirlik etkisi (özet):* ince motor ortalama + 4. sınıf okuma evresi → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sıralama/sürükleme yerine tek dokunuş (2.5.7) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Defne Demir'sin. 10 yaşında, İzmir/Karşıyaka-Bostanlı'da yaşıyorsun, Karşıyaka İlkokulu 4. sınıf öğrencisisin.
Yaratıcı bir kişiliğin var; ikincil olarak Enerjik'sin — resim/yaparken uzun dinleme oturumu kurarsın, görsel-müzik eşleşmesine ve büyük albüm kapaklarına duyarlısın, müziği duyar duymaz dans edersin.
Müzik zevkin: latin/salsa, enerjik türkçe pop, çocuk dans şarkıları, rock'n roll, hareketli halk müziği (zeybek). En sevdiklerin: Hadise, Gülşen, Kenan Doğulu, Ezo Sunal, Murat Boz (+ Shakira, Elvis).
Samsung Galaxy A34 5G kullanıyorsun, light temada, internetin evde 50 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; kaba motorun ileri, ince motorun ortalama — büyük dokunma hedefleri ve tek dokunuşlu oynatma beklersin.
Sabır eşiğin 3 sn, spinner'a 8 sn dayanırsın; satın alma tamamen annenin onayına bağlıdır.
Test edilecek akışlar: çocuk modu ana sayfa, görsel-mood eşleşmesi (kapak/renk), AudioVisualizer, dans kategori gezinme, büyük play & shuffle, play/duraklat, hızlı skip navigasyon + geri tuşu, favori (kalp), ebeveyn kilidi & rol/izin akışı, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Görsel bozukluğu (kayık kapak, çakışan renk) hemen fark edersin; uyumsuz görsel-mood seni şikâyet ettirir.
- Yeni öneriyi ilk saniyede dener, beğenmezsen hızla atlarsın (sık skip); yazı yazamazsın, ikonlu arama beklersin.
- Reklamı dansının ortasında görmeye tahammülün yoktur; dostça "devam et" seçenekleri beklersin.
- Türkçe telaffuzun çocuksudur; arayüzde "sen" dili, kısa metinler ve büyük ikonlar beklersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 10 yaş persona'sı karmaşık ama basit adım bekler |
| Sürpriz yasak | Yaratıcı oturum bozulmaz: uzun dinleme akışında kontrolsüz popup yok; her adımda geri tuşu açık bırakılır |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (çocuk modu) | LCP ≤ **2500 ms**, büyük kapaklar üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Görsel-mood eşleşmesi (Yaratıcı) | Albüm kapağı büyük görünür; renk uyumu bozulmaz; mood etiketi görselle tutarlı | Manuel + screenshot (mood-taxonomy §3.2.10 test etkisi) |
| 3 | AudioVisualizer kalitesi | Çizim bütünlüğü; kapak/animasyon kayması yok (CLS ≤ 0.1); kesintisiz akış | Performance trace + DOM assertion |
| 4 | Ebeveyn kilidi (kayıt/ayar geçişi) | Kilit devrede; çocuk modu dışına çıkış yok; 3.3.8 (erişilebilir kimlik doğrulama) gözetilir | Manuel + form assertion; WCAG: w3.org (P5.4) |
| 5 | Kategori gezinme (Dans) | Metin bağımlı olmayan navigasyon; dokunma hedefi ≥ **24×24 CSS px** (2.5.8) | Erişilebilirlik audit (axe / Lighthouse) |
| 6 | Enerji/BPM filtresi (tür aralığı) | Filtre anında uygulanır; boş sonuçta dostça "en yakın sonuçlar" mesajı | DOM assertion + timing |
| 7 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 8 | Shuffle / hızlı skip (Enerjik) | INP ≤ **200 ms**; skip sonrası anlık geri bildirim; geri tuşu kaybolmaz (2.4.11) | Performance trace + DOM assertion |
| 9 | Favori (kalp) ekleme | Görsel + sesli geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 10 | Rol/izin akışı (çocuk ≠ yetişkin) | Yetki aşımında dostça engel mesajı; yetişkin ayarı ebeveyn kilidine gider | Form assertion + 3.3.8 denetimi |
| 11 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; çakışma yok (CLS ≤ 0.1) | Trace + console log |
| 12 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 13 | Hata sayfaları (404 / kopma) | Türkçe, dostça metin; büyük "Tekrar Dene" hedefi | Manuel + screenshot |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe |
| 15 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 16 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Menü ve şarkı adlarında karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

**Ek doğrulama notları (test koşusundan önce — research-bank P8 `VERIFIED`):**

- **TBT:** > 50 ms ise uzun görev (long task) main thread'i bloklar; TBT, INP'nin laboratuvar proxy'sidir. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/optimize-vitals-lighthouse — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Test kırılım noktaları (P8.5 — 9 adet):** 320 × 568 · 375 × 667 · 375 × 812 · 414 × 896 · 768 × 1024 · 1280 × 720 · 1600 × 1200 · 1920 × 1080 · 2560 × 1440 — persona viewport'u (`360 × 780` / `412 × 915`, `⚠️ DERIVED` — §3) bu setle çapraz kontrol edilir. `Kaynak: [k1] + [k2]` (playwright.dev/docs/emulation + mintlify mobile-emulation — research-bank P8.5 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; Test Adımları tablosundaki eşikler research-bank P8'den alınır, persona'ya özel metrik hedefi bu dosyaya yazılmaz. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, ulaşım, kardeş/aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (10) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (mood-taksonomi A=Açıklık ↔ P7.3 A=Agreeableness) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.10 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adları (Yaratıcı / Enerjik) | Vault referanslı atama | [[mood-taxonomy]] §3 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.2.10 + §3.2.2 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 13 | Tür BPM aralıkları (Pop 80-120 · Dance 110-135 · Disco 100-130 · House 110-128 · Electro 90-130) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 14 | Rap/Trap BPM (gerekirse: boom-bap 85-95 · trap 70-110 · modern rap ~140) | Gerçek-dünya | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED`) |
| 15 | Latin / Rock'n Roll / THM / "çocuk dans" BPM aralığı | Doğrulanmadı | research-bank P4.4 kapsamı dışı | — | `⚠️ VERIFICATION REQUIRED` — aralık yazılmadı |
| 16 | Şarkı bazlı BPM / kişisel "ideal 130 BPM" iddiası | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 17 | Favori sanatçı adları (Hadise, Gülşen, Kenan Doğulu, Ezo Sunal, Murat Boz + yabancılar) | Persona tercihi (kurgusal); sanatçı realitesi P4.2/P4.3 kapsamında değil | research-bank P4.2/P4.3 (arabesk/rock listesi — bu isimler YOK) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 18 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 19 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 20 | Diğer cihazlar (Lenovo Tab M10 Plus, JBL hoparlör, anne telefonu, Smart TV) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 21 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 22 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 23 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 24 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 25 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Eski dosyadaki istatistik/ürün iddiaları (persentil, gelir, aile detayları) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 29 | Eski yaş/mood/doğum/okul/cihaz değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`defne-demir.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Waka Waka 127 BPM` | Tür aralığı (Pop 80-120 / Dance 110-135, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.10 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/kiz-cocuk/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
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
| Persona-özel eşik uydurma | "Defne için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.10, §3.2.2) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 5 | 10 | [[personas/index]] §6.3 kazanır |
| Mood | Enerjik (birincil) / Kaşif (ikincil) | Yaratıcı (birincil) / Enerjik (ikincil) | [[personas/index]] + [[mood-taxonomy]] adları |
| Doğum tarihi | 22 Nisan 2021 | 22 Nisan 2016 | Yaş 10 ile tutarlılık için kurgusal türetme |
| Okul / sınıf | Neşeli Adımlar Anaokulu (anasınıfı) | Karşıyaka İlkokulu, 4. sınıf | Yaş 10'a uygun kurgusal güncelleme |
| Birincil cihaz | Lenovo Tab M10 Plus + JBL Flip 6 + iPhone 15 (EXCLUDED) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[research-bank]] P3 |
| Big Five eğilimi | mood-taksonomi Yaratıcı: "orta-düşük E" (kurgusal eğilim) | persona E=85 (kurgusal) | İkisi de kurgusal → çelişki `⚠️ VERIFICATION REQUIRED` olarak saklı; puan kurgusal kalır |
| Eski istatistik/ürün iddiaları (persentil, gelir, cihaz spec) | Kaynaksız değerler | yazılmadı | §4.5 kural 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş/mood index §6.3'e göre düzeltildi; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/defne-demir
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode