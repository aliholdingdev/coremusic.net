---
title: "CoreMusic — Persona: Atakan Demir"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/atakan-demir-dans"
updated: 2026-09-26
group: genc-erkek
age: 16
mood: Dans Enerjik
persona_id: CM-AD-16-DAN-ANT
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Atakan Demir

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 16 yaşındaki spor/dans yapan bir genç için arayüzü Dans Enerjik gözden deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Dans Enerjik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Dans Enerjik ([[personas/index]] §6.3 ataması) |
| Test odağı | Workout playlist tek dokunuş, yüksek BPM (yalnız tür aralığı), hızlı navigasyon/INP, kesintisiz antrenman-parti dinlemesi, hata anında müziğin durmaması |
| Veli onayı | `veli_onayı_gerekli: false` (16 yaş — CoreMusic 16 eşiğinde; dayanak §3.5.1) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Ana sayfa / hızlı başlatma | Level 2 + 3 | Dans Enerjik: workout playlist üstte, canlı/enerjik beklenti ([[mood-taxonomy]] §3.4 satır 25 test etkisi) |
| Workout modu & BPM filtresi | Level 1 + 2 | BPM yalnız tür aralığı (P4.4/P4.5); şarkı-BPM iddiası yok |
| Crossfade / kesintisiz dinleme | Level 2 + 3 | Antrenman/parti sırasında kesinti kabul edilmez; CLS ≤ 0.1 |
| Keşif & öneri | Level 1 + 2 | Dans/club/workout ağırlıklı öneri beklentisi (yüksek O) |
| Hızlı dokunma & hata kurtarma | Level 2 | 2.5.8 ≥24×24 px (VERIFIED); crash sonrası "kaldığı yerden devam" |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski salt-okunur dosyadaki 100+ satırlık fiziksel/aile detayı bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); kaynaksız yüzdeler/istatistikler (yüzde, saat, takipçi sayısı) taşınmaz.

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] | Küme adı + tanım alıntısı (yalnız §3 listesi) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename/persona_id ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Atakan Demir | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 16 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 19 Nisan 2010 | Kaynak: kurgusal (persona verisi; 2026-09-26 itibarıyla yaş 16 ile tutarlı) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Antalya / Muratpaşa / Lara | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Lise 10. sınıf; Anadolu lisesi | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Bisiklet (okul/salon); yağmurda otobüs | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `atakan.energy.boost` | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-AD-16-DAN-ANT` | Kaynak: kurgusal (atama: [[personas/index]] §6.3) |

**`CoreMusic Kullanıcı ID` parça dökümü** (index §6.3 atanan format — persona-template §3.5.1'in `CM-XX-YY-ZZZ-XXX` şablonuyla uyumludur; 2026-09-26 düzeltme):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `AD` | `AD` | Ad soyad baş harfleri (Atakan Demir) |
| `16` | `16` | Yaş (index §6.3) |
| `DAN` | `DAN` | Mood kodu (Dans) |
| `ANT` | `ANT` | Şehir kodu (Antalya) |

> **KVKK / yaş sınırı notu (zorunlu — 16+):** Bu persona 16 yaşındadır → frontmatter'de `veli_onayı_gerekli: false`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; CoreMusic politikası **16 yaş altı** için veli onayı ister (`⚠️ DERIVED`, [[research-bank]] P6.4 son satır) — 16 bu eşiğin **dahilinde üst sınırı** olduğundan onay gerekmez. Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). Persona tamamen kurgusaldır; gerçek genç verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

| Düzenleme / Politika | Yaş sınırı | Anlamı | Etiket |
|----------------------|-----------|--------|--------|
| TMK m.11 (Türkiye) | 18 | Erginlik = hukuki ehliyet (evlenme, dava, imza) — veli onayının genel sınırı | `Kaynak: [k1] + [k2]` (P6.4 VERIFIED) |
| GDPR m.8 (AB) | 16 | Bilgi topluma sunma hizmetlerinde rıza verme yaşı — persona tam eşiği (16) | `Kaynak: [k1] + [k2]` (P6.4 VERIFIED) |
| COPPA (ABD) | 13 | 13 altı için ebeveyn izni zorunlu | `Kaynak: [k1] + [k2]` (P6.4 VERIFIED) |
| CoreMusic politikası | 16 | <16 → `veli_onayı_gerekli: true`; ≥16 → `false` | `⚠️ DERIVED` (P6.4 son satır) |

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski salt-okunur dosyadaki 100+ satırlık ayrıntı (ayakkabı numarası, cilt bakımı, takı listesi…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 175 cm (atletik yapı — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 62 kg, atletik/fit (düzenli spor — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Açık kahverengi dalgalı saç, mavi göz (güneşe hassas — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; güneş gözlüğü kullanır (ışık hassasiyeti — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Spor/renkli giyim; profil fotoğrafında canlı zemin (kontrast: koyu metin × açık zemin — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Antrenmanda terli parmak + hareketli ortam → büyük hedef, az adım, anlık geri bildirim; güneşte ekran okunurluğu beklentisi (kurgusal davranış) | Kaynak: kurgusal (persona verisi); asgari hedef: 2.5.8 ≥24×24 CSS px `Kaynak: [k1] + [k2]` (P5.4 VERIFIED) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 88 | Sosyal ortamlarda enerjisi bulaşıcı, herkesle hemen kaynaşır; paylaşmayı ve öne çıkmayı sever. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 48 | Arkadaş canlısıdır ama rekabetçidir; kazanmayı ve kendi doğrularını savunmayı tercih eder. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 45 | Sporda disiplinlidir; okul/plan tarafında ise işi son ana bırakır, organize olmakta zorlanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 26 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 26 = yüksek duygusal denge; stresi spor ve müzikle atar, hızlı toparlanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 90 | Yeni müzik, yeni spor, yeni insan; keşif ve macera arayışı sürekli — önerilerde yenilik bekler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Dans Enerjik |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar) |
| **Küme tanımı (alıntı)** | "Dans kursı/workout + hareketli müzik (çocuk ve genç erkek ortak adı)" ([[mood-taxonomy]] §3.4 satır 25) |
| **Big Five eğilimi (alıntı)** | "Yüksek O, düşük N" ([[mood-taxonomy]] §3.4 satır 25 "Big Five eğilimi (kurgusal)") — Big Five puanlarıyla (O 90 · N 26) **tutarlı**; bağ: `⚠️ DERIVED` |
| **Tetikleyici durumlar** | Antrenman/parti saati gelmesi (workout playlist beklentisi); yavaş/kaba geçiş ve hissedilir gecikme (INP); düşük tempo/uzun intros (skip dürtüsü); hata anında müziğin durması (kabul edilemez) |
| **UI etkisi** | "Koreografi videoları, workout playlist, yüksek BPM" ([[mood-taxonomy]] §3.4 satır 25 "Test etkisi") |
| **Mood müziği (not)** | §3.4 satır 25'in "Dans, club, workout · **120-160 BPM** ⚠️" hücresi taksonomi kendisi ⚠️ ile işaretler → birincil ölçüm değildir, testte kullanılmaz (`⚠️ VERIFICATION REQUIRED`, §3.5.11 satır 16) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Dans Enerjik); ikincil bu turda yok — eski salt-okunur dosyada "ikincil Sosyal" yazar, §7.2'de |
| Ad tekliği | Eski "Dans Enerjik (Erkek)" ayrı ad taşısa da taksonomi §3.4'te küme **tek satırdır**; dosya adı/atanan ad `atakan-demir-dans` / "Dans Enerjik" (mood-taxonomy §3.4 eski tablo notu) |
| Test etkisi | "Koreografi videoları, workout playlist, yüksek BPM" (§3.4 satır 25) — BPM beklentisi yalnız tür aralığıyla karşılanır (P4.4/P4.5); geçerli eşikler P5.4/P8 |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Workout playlist beklentisi | Antrenman listesi tek dokunuşla; arka plan çalma kesintisiz | Manuel + LocalStorage kontrolü |
| 2 | Yüksek BPM beklentisi | BPM/tür filtresi çalışır; sonuçlar tür aralığı (P4.4/P4.5) içinde; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 3 | Hızlı navigasyon / enerjik akış | INP ≤ **200 ms**; skip sonrası anlık geri bildirim | Performance trace + DOM assertion |
| 4 | Kesintisiz dinleme (antrenman/parti) | Geçişte sıçrama yok (CLS ≤ **0.1**); parça kesintisi kabul edilmez | Trace + console log |
| 5 | Keşif (yüksek O) | Öneriler dans/club/workout ağırlıklı; "yeni çıkanlar" görünür | DOM assertion + içerik kontrolü |
| 6 | Düşük N (hata/gecikme anı) | Hızlı kurtarma; teknik yığın izi görünmez; akış "kaldığı yerden devam" | Manuel + screenshot |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Electronic / Dance / EDM 2) House (deep/tech/progressive) 3) Hip-Hop / Trap (workout) 4) Latin / Reggaeton 5) Pop (Türkçe + yabancı) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5+5)** | TR: Mahmut Orhan, Tarkan, Aleyna Tilki, Buray, Burak Yeter · Yabancı: Martin Garrix, Tiësto, Calvin Harris, David Guetta, Bad Bunny | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); bu isimler research-bank P4.2'de YOK → gerçek-dünya bilgileri `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Dance **110-135** · House **110-128** · Electro **90-130** · Techno **130-140** (P4.4) · trap **70-110** · modern rap **~140** (P4.5, workout rap için) | turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4) · nevamuzik.com.tr + vocuno.com/tr/bpm-algilayici (P4.5) → `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Yüksek/hızlı tempo ister; antrenman ve mix akışını taşır (sayısal değer yazılmadı — persona tutumu, ölçüm değil) | Kaynak: kurgusal (persona verisi) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi: sabah koşusu, okul yolu, antrenman saati, akşam DJ pratiği · Hafta sonu: uzun sabah koşusu, spor, plaj, gece parti | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (workout listeleri + BPM filtresi + mix sıralama) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Agresif keşif: sürekli yeni ses/çıktı arar, "yeni çıkan dans" ve keşif listelerini her hafta tarar | Kaynak: kurgusal (persona verisi; yüksek O ile tutarlı) |
| **Premium olasılığı** | Yüksek — ücretli abonelik kullanır, reklam beklemez | Kaynak: kurgusal (persona verisi; yüzdeler yazılmadı) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Electronic / Dance / EDM | Sporun ve partinin enerjisi; drop anı motivasyonu |
| 2 | House | DJ pratiğinin temeli; akıcı, uzun mix taşır |
| 3 | Hip-Hop / Trap (workout) | Ağırlık/set anında agresif, kontrollü enerji |
| 4 | Latin / Reggaeton | Plaj/sosyal ortam; hareketli, dans ettirir |
| 5 | Pop | Arkadaş grubunun ortak dili; her ortamda çalışır |

*Favori sanatçıların **hiçbiri** research-bank P4.2'de yer almaz → gerçek-dünya bilgileri `⚠️ VERIFICATION REQUIRED`; yalnızca persona tercihi olarak kullanılır (kurgusal). P4.6'da VERIFIED olan Türkçe sanatçılar (Duman, Teoman, Ezhel) testte "benzer sanatçı/katalog" senaryolarında veri olarak kullanılabilir → `Kaynak: [k1] + [k2]`.*

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 06:15 | Sabah koşusu | Melodic/dans (tempolu koşu) |
| Hafta içi | 16:00 | Antrenman | Workout mix (yüksek tempo) |
| Hafta içi | 20:30 | DJ pratiği | House / elektronik (mix sıralı) |
| Hafta sonu | 14:30 | Plaj / parti | Latin + dans (kesintisiz) |

*Not: BPM satırları yalnız tür aralığı düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.4 satır 25'in "120-160 BPM" notu da birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`; test hedefi olarak banka tür aralıkları kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone 14 Pro (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | iOS sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Safari (mobil — kurgusal; P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Evde hızlı sabit bağlantı + mobil sınırsız (kurgusal — sayısal hız yazılmadı) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Spor kulaklığı + kablolu stüdyo kulaklık (eski dosyada modeller) — modeller spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | Aydınlık/canlı tercih (kurgusal — mood tanımında tema zorunluluğu yok) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Yoğun gündüz+akşam kullanımı (kurgusal — sayısal saat yazılmadı) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 8/10 — DJ yazılımı, mix araçları, paylaşım akışları aktif kullanır | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Dizüstü bilgisayar (DJ pratiği) + tablet — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); açık havada/güneşte ekran bakışı | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — açık temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal; yüksek ses kültürü, kulaklıkla antrenman | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; terli parmakla hızlı/kaba kontrol | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · tek dokunuş yerine sürükleme zorunlu değil (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | kısa dikkat, çabuk sıkılma; az adım + anlık geri bildirim | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — hızlı akış bekler | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi (okul/spor/aile bağlamı) gerçek kişiye ait değil, tamamı kurgu (6698 m.5/1 — `Kaynak: [k1] + [k2]`, P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| ≥64 px dokunma hedefi | Eski dosyada "butonlar en az 64×64px (terli parmak)" yazar; research-bank P5'te 64 px EŞİĞİ YOK → `⚠️ VERIFICATION REQUIRED`; asgari güvenli eşik 2.5.8 (≥24×24 CSS px, VERIFIED) |
| "Sesli komut / kadans senkron" iddiası | P5 kapsamında değil → `⚠️ VERIFICATION REQUIRED`; test kapsamına alınmadı |

### Kişilik & Davranış

- Enerjik ve sosyaldir: her aktivitenin bir playlist'i vardır, paylaşmayı ve öne çıkmayı sever.
- Hız bekler: yavaş/gecikmeli akışta sıkılır; INP ≤ 200 ms ve anlık geri bildirim onun için belirleyicidir.
- Rekabetçidir: her şeyde en iyisini ister; BPM/keşif listelerinde "yeni" ve "yüksek" arar.
- Müziğin durması felakettir: hata/crash sonrası "kaldığı yerden devam" beklentisi güçlüdür.
- Değişikliği sever ve hemen dener (yeni tasarım/A-B'de meraklıdır) — Furkan'ın tersi.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 16 yaş → kendi kararı (CoreMusic 16 eşiğinde); ücretli abone, donanım/içerik harcamasına açık | Kaynak: kurgusal (persona verisi); politika: `⚠️ DERIVED` (P6.4) |
| **Sabır eşiği** | ~2 sn; yüklenme/spinner karşısında hemen sıçrar (skip) | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Hızlı kurtarma ister; müzik durursa uygulamayı kapatıp tekrar dener — teknik metni görmezden gelir | Kaynak: kurgusal (persona verisi); düşük N ile tutarlı (sinirlenmez, çözüme geçer) |
| **Reklam toleransı** | Düşük — akışın kesilmesini sevmez; ücretli kullanıcı | Kaynak: kurgusal (persona verisi; yüzdeler yazılmadı) |
| **Öğrenme biçimi** | Hızlı dener, öğrenir; dokunarak keşfeder, kılavuz okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Listeleri paylaşır, ortak çalma/ekleme bekler | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Gece geç saatte tek dokunuş "devam" ve otomatik akış bekler | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tanıdık workout playlist'i + kesintisiz çalma + hızlı arayüz → uygulamaya bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.4 satır 25 |

*Erişilebilirlik etkisi (özet):* antrenman/parti ortamında kaba-hızlı kontrol → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), geri tuşu/skip kaybolmaz (2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED); ≥64 px hedefi `⚠️ VERIFICATION REQUIRED`.

### AI Rol Kartı

Sen Atakan Demir'sin. 16 yaşında, Antalya/Muratpaşa-Lara'da yaşıyorsun, Anadolu lisesinde 10. sınıfta okuyorsun ve düzenli spor yapıyorsun (koşu, fitness, yüzme, bisiklet).
Dans Enerjik bir kişiliğin var: dışa dönüksün, enerjiksinsin, rekabetçisin; her aktivitenin bir playlist'i vardır — "workout playlist, yüksek BPM" senin beklentindir.
Müzik zevkin: electronic/dance/EDM, house, hip-hop/trap (workout), Latin/reggaeton, pop. Türk favorilerin: Mahmut Orhan, Tarkan, Aleyna Tilki, Buray, Burak Yeter; yabancı favorilerin: Martin Garrix, Tiësto, Calvin Harris, David Guetta, Bad Bunny.
Telefonun (model spec'i doğrulanmadı), dizüstü bilgisayarın ve spor kulaklığın var; DJ pratiği yaparsın.
Görme / işitme / hareket kısıtların yok; antrenmanda terli parmakla hızlı kontrol yaparsın — büyük butonlar, az adım ve anlık geri bildirim beklersin.
Sabır eşiğin ~2 sn; müzik durursa felaket, "kaldığı yerden devam" istersin; yeni tasarım/A-B değişikliğini heyecanla denersin.
Test edilecek akışlar: ana sayfa (enerjik, workout playlist üstte), BPM/tür filtresi, crossfade/kesintisiz dinleme, hızlı skip/navigasyon, keşif & öneri, arama (Türkçe karakter), playlist paylaşımı, hata/kurtarma, çevrimdışı antrenman, erişilebilirlik denetimi.
Davranış notları:
- Antrenman için tek dokunuşla workout listeni açarsın; slow/balad akışta hemen sıçrarsın.
- BPM filtresinde tür aralığı (P4.4/P4.5) dışında sayısal söz verilmez; "şarkı X BPM" duyarsan itibarını kaybedersin.
- Önerilerin dans/club/workout ağırlıklı olmasını beklersin; algoritma pop ağırlığına düşerse "beni tanımıyor musun" dersin.
- Hızlı karar verirsin, yeniliği hemen denersin; sabırlı yükleme ekranı sevmezsin.
- Türkçe karakter bozukluğu görürsen güvenin düşer; paylaşım akışında gecikme kabul edilmez.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa ve enerjik ton — uzun ve yavaş açıklama kabul etmez |
| Gerçek kişi verisi | Prompt'ta gerçek kişi/aile/okul verisi yok — hepsi kurgu (P6.5); favori sanatçı bilgileri P4.2 dışıdır → kurgusal tercih |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (enerjik) | LCP ≤ **2500 ms**, workout playlist üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Workout playlist tek dokunuş | Antrenman listesi anında; arka plan çalma kesintisiz | Manuel + LocalStorage kontrolü |
| 3 | BPM/tür filtresi | Filtre çalışır; sonuçlar tür aralığı (Dance 110-135 · House 110-128 · Electro 90-130 · Techno 130-140 · trap 70-110) içinde; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 4 | Crossfade / kesintisiz geçiş | Geçişte sıçrama yok (CLS ≤ 0.1); parça kesintisi yok | Trace + console log |
| 5 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; büyük oynat kontrolü (≥ **24×24 CSS px**, 2.5.8); odak görünür (2.4.7) | Trace + console log + a11y audit |
| 6 | Hızlı skip navigasyon | INP ≤ **200 ms**; skip sonrası anlık geri bildirim; geri tuşu kaybolmaz (2.4.11) | Performance trace + DOM assertion |
| 7 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; çakışma yok (CLS ≤ 0.1) | Trace + console log |
| 8 | Keşif & öneri | Öneriler dans/club/workout ağırlıklı; "yeni çıkanlar" görünür | DOM assertion + içerik kontrolü |
| 9 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 10 | Playlist oluşturma & paylaşım | Oluşturma < 2 sn; görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok, akış korunur | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Hata sayfaları / kurtarma | Hızlı "kaldığı yerden devam"; teknik yığın izi görünmez; müzik durmaz (kurgusal beklenti) | Manuel + screenshot |
| 13 | Çevrimdışı / antrenman modu | İndirilen/önbellek liste erişilebilir; durum göstergesi doğru | CDP `Network.emulateNetworkConditions` + manuel |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.4.11 / 2.5.7 / 2.5.8 / 3.3.8 ihlali yok (64 px iddiası test edilmez — VR) | Lighthouse / axe |
| 15 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 16 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, okul, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (16) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Doğum tarihi (19 Nisan 2010) | Kurgusal (yaş 16 ile tutarlı — 2026-09-26) | — | — | `Kaynak: kurgusal (persona verisi)` |
| 4 | `veli_onayı_gerekli: false` (16+ politikası) | Politika sonucu (türetilmiş) | research-bank P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 5 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 6 | Persona verisi kurgu; gerçek genç verisi yok | Gerçek-dünya (hukuki) | research-bank P6.5 | 6698 m.5/1 | `Kaynak: [k1] + [k2]` |
| 7 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 9 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 10 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 11 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 12 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 13 | Mood küme adı (Dans Enerjik) | Vault referanslı atama | [[mood-taxonomy]] §3.4 satır 25 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 14 | Mood küme tanımı + test etkisi alıntısı ("Dans kursı/workout + hareketli müzik …" · "Koreografi videoları, workout playlist, yüksek BPM") | Vault verisi | [[mood-taxonomy]] §3.4 satır 25 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 15 | Mood eğilimi ("Yüksek O, düşük N") | Vault verisi (kurgusal eğilim) | [[mood-taxonomy]] §3.4 satır 25 | — | `Kaynak: [[mood-taxonomy]]` + bağ: `⚠️ DERIVED` |
| 16 | Mood BPM notu (120-160 ⚠️) | Doğrulanmadı (taksonomi notu) | mood-taxonomy §3.4 (⚠️ işaretli) | research-bank P4 (böyle aralık YOK) | `⚠️ VERIFICATION REQUIRED` — testte kullanılmadı |
| 17 | Dans/club/workout tür BPM aralıkları (Dance 110-135 · House 110-128 · Electro 90-130 · Techno 130-140) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 18 | Rap/trap workout BPM (trap 70-110 · modern rap ~140) | Gerçek-dünya (ikincil) | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED`) |
| 19 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 20 | Favori sanatçı adları (TR 5 + yabancı 5) | Persona tercihi (kurgusal); P4.2 dışı | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 21 | P4.2'de VERIFIED Türkçe sanatçılar (Duman, Teoman, Ezhel) — katalog testi verisi | Gerçek-dünya | tr.wikipedia Duman/Teoman/Ezhel | prmedya + tr.wikipedia Türkçe rock | `Kaynak: [k1] + [k2]` (P4.6 `VERIFIED`; Ezhel Spotify sıralaması `SINGLE-SOURCE ⚠️` — kullanılmadı) |
| 22 | Cihaz: iPhone 14 Pro (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 23 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 24 | Diğer cihazlar (dizüstü, tablet, kulaklıklar) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 25 | Tarayıcı sürümü, internet hızı, OS sürümü | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 26 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 27 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 28 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 29 | Dokunma hedefi ≥64 px (eski dosya iddiası) | Doğrulanmadı | eski salt-okunur dosya | research-bank P5 (64 px EŞİK YOK) | `⚠️ VERIFICATION REQUIRED` — asgari 2.5.8 (≥24×24 px) kullanıldı |
| 30 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 32 | Küme adı tekliği (eski "Dans Enerjik (Erkek)" ayrı ad iddiası) | Vault içi not | mood-taxonomy §3.4 eski tablo notu | [[personas/index]] §6.3 | `Kaynak: [[mood-taxonomy]] + [[personas/index]]` (tek satır: "Dans Enerjik") |
| 33 | Eski dosyadaki istatistik/yüzde iddiaları (dinleme saatleri, takipçi sayıları, keşif yüzdeleri, premium yüzdesi, aile geliri) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`atakan-demir-dans.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 14 Pro viewport = 2556×1179` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Bu şarkı 132 BPM` | Tür aralığı (P4.4/P4.5, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| `Keşif kaynakları %25 Spotify` / `premium %95` | Yüzdeler bankada yok → nitel ("yüksek/düşük") + `Kaynak: kurgusal (persona verisi)` |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.4 satır 25 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood/persona_id |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia/istatistik taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK/veli), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: salt-okunur kaynak; yalnız kurgusal kimlik/müzik/rutin taşınır; yüzde/saat/takipçi istatistikleri taşınmaz | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI (İSTATİSTİK YOK) → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 → §6.1 → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Kaynaksız yüzde/istatistik (eski dosyadan) | X-10 / §4.5 ihlali | Sil; nitel ifadeye çevir + `Kaynak: kurgusal` |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Atakan için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu + mood eğilimi (Yüksek O, düşük N) uyumu
- [ ] Test Adımları tablosu ≥ 10 satır ve 3 sütun (Adım / Beklenen / Doğrulama yöntemi)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 16+: `veli_onayı_gerekli: false` + KVKK/GDPR/COPPA karşılaştırma tablosu ("KVKK 16" YANLIŞ)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; iPhone 14 Pro `⚠️ VERIFICATION REQUIRED`
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
| Kaynak & Doğrulama satırı | 33 |
| Frontmatter alan | 7 zorunlu + 5 ek |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood/persona_id atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.4 satır 25) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş / doğum tarihi | 16 · 19 Nisan 2010 | 16 · 19 Nisan 2010 | Tutarlı — [[personas/index]] §6.3 onaylar (çakışma yok) |
| Persona ID | `CM-AD-16-ENE-ANT` / `GE-AD-16-ENE-ANT` | `CM-AD-16-DAN-ANT` | [[personas/index]] §6.3 kazanır; şablon §3.5.1 `CM-XX-YY-ZZZ-XXX` ile uyumlu (2026-09-26 düzeltme) |
| Birincil cihaz | iPhone 14 Pro (2556×1179 spec'li) | Test viewport: banka A34 türevi (`⚠️ DERIVED`); spec yazılmadı | [[research-bank]] P3 — cihaz spec'i yazılmadı (X-1) |
| BPM aralığı | "128-140 / 130-150 (kişisel)" + taksonomi "120-160 ⚠️" | Tür aralıkları P4.4/P4.5 (VERIFIED ikincil); şarkı BPM'i yok | §4.5 + X-2 |
| Şarkı BPM/şarkı listesi okumaları | Eski dosyada şarkı bazlı okumalar | yazılmadı | X-2 |
| Dokunma hedefi | "Butonlar en az 64×64px (terli parmak)" | ≥24×24 CSS px (2.5.8, P5.4 VERIFIED); 64 px `⚠️ VERIFICATION REQUIRED` | research-bank P5 kazanır |
| Big Five ölçeği | 0-10 puanlar (9/10, 4/10 …) | 0-100 normalize (`⚠️ DERIVED`) | P7.4; eski 0-10 değerleri puan olarak taşınmadı |
| İkincil mood | "İkincil mod tipi: Sosyal" | ikincil bu turda yok | [[personas/index]] §6.3 tekil küme atar |
| Küme adı | "Dans Enerjik / Workout Enerjik" + eski "Dans Enerjik (Erkek)" ayrı adı | "Dans Enerjik" (tek satır) | mood-taxonomy §3.4 eski tablo notu + index §6.3 |
| Yüzdeler/istatistikler (dinleme saati, takipçi, keşif %, premium %95, aile geliri, harçlık) | Kaynaksız sayılar | yazılmadı (nitel ifadeye çevrildi) | §4.5 kuralı 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kurgusal kimlik/müzik/rutin taşındı, istatistikler atlandı; persona_id/index ataması uygulandı; veli_onayı false + KVKK/GDPR/COPPA karşılaştırma tablosu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/atakan-demir-dans
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
