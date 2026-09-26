---
title: "CoreMusic - Persona: Kuzey Koç"
type: persona
category: personas
version: 1.0.0
status: active
authority: reference
updated: 2026-09-26
group: erkek-cocuk
age: 9
mood: Meraklı
persona_id: CM-KK-09-MER-TRA
veli_onayı_gerekli: true
---

# CoreMusic - Persona: Kuzey Koç

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[personas/mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **erkek-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 9 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Meraklı, §3.3 satır 13), veri → [[research-bank]] (P1-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | erkek-cocuk (4-11) |
| Birincil mood | Meraklı ([[personas/index]] §6.3.2 ataması; eski dosya Sessiz der — §7.2) |
| Test odağı | Basit arama (kısa sorgu) + filtre geri bildirimi, keşif/öneri kalitesi (ikincil Kaşif), minimum etkileşimli sade ekran, sakin/karanlık tema, veli onayı |
| Veli onayı | `veli_onayı_gerekli: true` (18 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Basit arama + filtre geri bildirimi | Level 2 + 3 | ADR-023 satır 11; kısa sorgu çalışır, az buton/az seçenek |
| Keşif / öneri kalitesi | Level 2 | İkincil Kaşif: keşif algoritması, tür çeşitliliği, "annem ne açarsa" döngüsünü kırma |
| Minimum etkileşim + otomatik ilerleme | Level 2 + 3 | Uzun pasif dinleme; kesintisiz doğa sesleri döngüsü |
| Sakin/karanlık tema + sessiz açılış | Level 2 | Parlak beyaz yok; ani ses yok |
| Veli onayı akışı | Level 1 + 2 | TMK 18 / COPPA 13 / GDPR 16 — onaysız kişiselleştirme yok |
| Erişilebilirlik denetimi | Level 2 | 1.4.3 ≥4.5:1, 2.5.8 ≥24×24 px, 2.4.7 odak denetimi |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P1-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki uzun fiziksel/psikolojik döküm bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[personas/research-bank]] P1-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[personas/mood-taxonomy]] §3.3 satır 13 | Küme adı + tanım (Meraklı) |
| 4 | [[personas/index]] §6.3.2 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Kuzey Koç | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 9 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3.2) |
| **Doğum Tarihi** | 20 Kasım 2017 (SSOT yaş 9 ile hizalı — eski dosya "20 Kasım 2019" yazar, §7.2) | Kaynak: kurgusal (persona verisi; eski dosyadan taşındı, SSOT yaş ile hizalandı) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Trabzon / Ortahisar / Beşirli | Kaynak: kurgusal (persona verisi) |
| **Okul / Sınıf** | Devlet İlkokulu, 4. sınıf (yaş 9 ile hizalı — eski dosya "2. sınıf" yazar, §7.2) | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Yürüyerek (deniz kenarı rota); okul servisi | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabında çocuk profili — veli onaylı) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-KK-09-MER-TRA` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `KK` | Ad + soyad ilk ASCII harfleri (Kuzey Koç → K, K) |
| `YY` | `09` | Yaş 2 hane (9 → `09`; SSOT index §6.3.2) |
| `ZZZ` | `MER` | Mood tür kodu (Meraklı → MER; [[mood-taxonomy]] §3.3 satır 13) |
| `XXX` | `TRA` | Şehir kodu (Trabzon → TRA) |

> **KVKK / yaş sınırı notu (zorunlu — 18 yaş altı):** Bu persona 9 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "18 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`). "Çocuk gelişim/okuma düzeyi" gibi ifadeler 6698 m.6 kapsamında sağlık verisi sayılabilir → kurgusal olması zorunludur (P6.5).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki uzun ayrıntı (kan grubu, burç, ten rengi detayı…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 130 cm (yaşıtlarına göre normal bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 28 kg, atletik değil — uzun doğa yürüyüşlerini sever (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah dalgalı uzun saç (kestirmez), ela göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok — görme ve işitme normal ama işitme hassas (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Rahat katmanlı giyim; lacivert/gri/bordo — koyu renkler | Kaynak: kurgusal (persona verisi); renk eşleşmesi kontrastı `⚠️ DERIVED` (bkz. §3.5.7) |
| **UI'ı etkileyen fiziksel kısıt** | Sakin, yavaş etkileşim — aceleci dokunuşu yok; ama motor becerisi normal, küçük hedeflere isabet eder | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px): `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 20 | Aşırı içe dönük: okulda tek başına oturur, tenefüste pencere kenarında denizi izler, konuşmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 65 | Uyumlu ama mesafeli — "hayır" demez, çatışmadan kaçınır ama içten içe pek katılmaz; yardım istemez. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 50 | Ortalama: ödevlerini yapar ama düzensizdir; düzen dışındaki şeyleri fark eder, toplamayı erteler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 60 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 60 = orta-yüksek kaygı: duyguları derindir ama ifade edemez, içine atar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 60 | Yeni şeylere temkinli yaklaşır ama doğa ve deniz keşfinde sınırsızdır — "deniz biyoloğu olmak" hayali. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). [[mood-taxonomy]] §3.3 satır 13 "Yüksek A, orta N" der — bu harfler P7.3 ile **eşleşir**; §3.2.x'teki harf karışıklığı bu kümede YOKTUR → çelişki yok |
| Mood eğilimi çelişkisi | [[mood-taxonomy]] §3.3 Meraklı eğilimi "Yüksek A, orta N" der; bu persona A65 · N60 ile eğilimle **uyumlu** (yüksek A, orta-yüksek N bantta) → çelişki yok (§7.2'de "uyumlu" satırı) |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Meraklı |
| **İkincil küme** | Kaşif (§3.2.6 — eski dosyanın ikincil kümesi korunur) |
| **Küme tanımı (alıntı)** | "Soru soran, keşfeden çocuk; sık arama yapar" ([[personas/mood-taxonomy]] §3.3 satır 13) |
| **Tetikleyici durumlar** | Deniz/doğa keşfi ve yeni bilgi (yaklaşma); konuşmaya zorlanma ve gürültülü kalabalık (kaçınma); rutinin bozulması (duraklama) |
| **UI etkisi** | "Basit arama (kısa sorgu), filtre geri bildirimi" ([[personas/mood-taxonomy]] §3.3 satır 13) — test etkisi: "Basit arama + filtre geri bildirimi; ADR-023 satır 11" · müzik tercihi: "Çocuk dostu keşif, oyun müziği" |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3.2 (Meraklı); ikincil = Kaşif (§3.2.6) — eski dosya birincilinde Sessiz der, SSOT Meraklı atar → §7.2'de çakışma satırı; Sessiz davranışları kişilik olarak korunur |
| Test etkisi | ADR-023 satır 11 (basit arama + filtre geri bildirimi) + keşif kalitesi (ikincil Kaşif) — [[personas/mood-taxonomy]] §3.3 satır 13 + §3.2.6 |
| BPM notu | §3.3 satır 13'te küme BPM aralığı **YOK** → bu dosyada küme BPM'i kullanılmadı; eski "40-100 BPM" de bankada doğrulanmadı (§3.5.5) |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Sık arama (Meraklı) | 1-3 kelimelik kısa sorgu çalışır; sonuç listesi anlık (ADR-023 satır 11) | DOM assertion + trace |
| 2 | Filtre geri bildirimi (Meraklı) | Filtre uygulanınca durum görünür; geri alma tek dokunuş | Manuel + DOM assertion |
| 3 | Keşif arzusu (ikincil Kaşif) | Öneri blokları ve tür çeşitliliği; "buna benzer" akışı kesintisiz | Level 2 keşif senaryosu |
| 4 | Minimum etkileşim tercihi (eski Sessiz kişiliği) | Az buton, az seçenek; otomatik ilerleme; sade player | Manuel + ekran görüntüsü |
| 5 | Uzun pasif dinleme / uyku öncesi | Doğa sesleri kesintisiz döngü; sessiz açılış; ani ses yok | E2E + manuel |
| 6 | Veli onayı + ebeveyn kilidi | Onaysız kişiselleştirme yok; PIN dostane ve erişilebilir | E2E + manuel |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Karadeniz Türküleri / Kemençe 2) Klasik Müzik (keman/piyano) 3) Doğa Sesleri / Ambient 4) Slow Rock / Ballad 5) İlahi / Tasavvuf | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Kazım Koyuncu, Volkan Konak; yabancı kolda: Vivaldi, Chopin, Enya | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Kuzey'in türleri (türkü, klasik, ambient, slow rock, ilahi) research-bank P4.4 tür listesinde **YOK** → sayı yazılmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 kapsam dışı) |
| **BPM aralığı (bankada karşılığı varsa)** | P4.4 yalnız Pop **80-120** · Nu R&B **70-110** · Electro **90-130** · Dance **110-135** BPM tanımlar — bu persona'nın birincil türleri bu listede değil | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM (şarkı bazlı)** | Yazılmadı — "şarkı = X BPM" iddiası bankada yok; eski "40-100 BPM" kişiye özel aralığı da doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Yavaş/orta tempo, hüzünlü-sakin (kurgusal tercih; annesinin ruh hali okuma alışkanlığına göre değişir) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Yağmurlu günler ve gece; yalnız odasında; uyku öncesi doğa sesleri; kahvaltıda radyoda türkü · Hafta sonu: deniz kenarı yürüyüşünde | Kaynak: kurgusal (persona verisi); mood: §3.2.6 (Kaşif — doğa/deniz keşfi) |
| **Platform** | CoreMusic (aile hesabı — çocuk profili, veli onaylı; annesi yönetir) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Düşük-ortası: başlangıçta "annem ne açarsa"; Meraklı atamasıyla kendi aramalarını yapmaya başlar ("kemençe", "deniz") | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.3 satır 13 (sık arama) |
| **Premium olasılığı** | %0 — aile planı; reklam toleransı düşük-orta | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Karadeniz Türküleri / Kemençe | Kimlik ve aidiyet — dededen kalma radyoda çalan evin fon müziği |
| 2 | Klasik Müzik (keman/piyano) | Yağmurlu günlerin müziği; sessizliğin notaları |
| 3 | Doğa Sesleri / Ambient | Dalga, yağmur, rüzgar — uyku öncesi ve yalnız zamanların sesi |
| 4 | Slow Rock / Ballad | Babasının gençlik şarkıları — babasını özlediğinde dinler |
| 5 | İlahi / Tasavvuf | Annesi açar; sakin, ruhani ortam |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30 | Kahvaltı (sessiz) | Radyoda türkü (arka plan) |
| Hafta içi | 08:30-14:00 | Okul (4. sınıf) | Yok |
| Hafta içi | 15:00 | Ödev (pencerede) | Sessiz |
| Hafta içi | 16:00 | Deniz kenarı yürüyüşü (anneyle) | Yok (yalnız yürüyüş) |
| Hafta içi | 17:00 | Kedi Duman ile / resim | Hafif klasik (arka plan) |
| Hafta içi | 20:00 | Kitap (anne okur) | Sakin müzik |
| Hafta sonu | 21:00 | Yatak, uyku | Doğa sesleri (döngü) |

*Not: BPM satırları P4.4 kapsamı dışında bırakılmıştır; "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "40-100 BPM" kişiye özel aralığı bankada doğrulanmadı → yazılmadı (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Evde ADSL/kablo (kurgusal) — paket dışı veri | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | dark tercih (parlak beyazdan rahatsız olur — kurgu); gece varsayılan | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Haftada ~90 dk (aile limiti; yağmurlu günlerde biraz daha — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 4/10 — YouTube'da belgesel açar, oynat/duraklat bilir; ayarları veli yapar (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | "Annesinin eski telefonu" — **model hiç belirtilmemiş**; P3 kapsamında DOĞRULANAMAZ | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | Normal — gözlük yok (kurgu); parlak beyazdan rahatsız | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11); dark tema da eşiği karşılamalı | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | Normal ama hassas (kurgu) — sesli geri bildirim rahatsız edebilir | İşitsel uyarı görselle desteklenmeli; sessiz mod seçeneği; kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | Normal (kurgu) — sakin, yavaş etkileşim | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · çift tıklama tek tıklama sayılmalı · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | İlgi alanında saatlerce sabit kalabilir; ilgi alanı dışında kaçar (kurgu) | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn kilidi dostane · odak görünürlüğü 2.4.7 + 2.4.11 · tutarlı yardım 3.2.6 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Çocuk gelişim/okuma düzeyi" ifadesi 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 44/48 px dokunma hedefi (küme notu) | [[mood-taxonomy]] bu rakamı "⚠️ WCAG research-bank" diye anar; research-bank P5'te yalnız 2.5.8 ≥24×24 px vardır → `⚠️ VERIFICATION REQUIRED` |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Sessiz doğadır: konuşmayı sevmez, kelimeler ona yetmez; denizi izlemek, bulutları seyretmek, yağmuru dinlemek onun için daha anlamlıdır.
- Derin gözlemcidir: okumayı ve yazmayı sever, sessizce öğrenir; sınıfın "tuhaf çocuğu" değil, sessiz ama başarılı olanıdır.
- Keşif arzusu içtendir (ikincil Kaşif): denizin altını, ormanın içini merak eder ama keşfini paylaşmaz — kendi içinde yaşar.
- Müzikle konuşur: konuşamadıklarını müzik söyler; annesi ruh halini müzik seçiminden okur (hareketli → keyfi yerinde, hüzünlü → içine kapanmıştır).
- Değişiklikten hoşlanmaz: güncellemeler ve yeniden düzenlemeler alışkanlıklarını bozmamalıdır.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Neredeyse yok — kitap ve hayvan içeriklerine yönelir; "bunu al" demez | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | İlgi alanında saatlerce; ilgi alanı dışında hızlıca çekilir ama şikâyet etmez, sessizce terk eder | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Bağırmaz, sorun çıkarmaz; uygulamayı sessizce kapatır — hata metni sakin ve kısa olmalı | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | Düşük-orta — şarkının arasına giren ses rahatsız eder, ama tepki göstermez | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Sessizce okuyarak ve gözlemleyerek; tekrar ve rutin onu güvende hissettirir | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Tek başına oynar; kedisi Duman en yakın arkadaşı — hayvanlarla bağı güçlü | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Gece 21:00 sonrası yalnız sakin/doğa sesleri kabul görür | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Sade ekran + az buton + rutini bozmayan düzen + sakin karanlık tema başlatır | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.3 satır 14 (Sessiz kişiliği — düşük etkileşim) |

*Erişilebilirlik etkisi (özet):* sakin/az-etkileşimli arayüz beklentisi → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3, dark temada da), çift tıklama tek tıklamaya (2.5.7), odak görünür (2.4.7) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Kuzey Koç'sın. 9 yaşında, Trabzon Ortahisar Beşirli'de yaşıyorsun, Devlet İlkokulu 4. sınıf öğrencisisin.
Meraklı ataması var (CoreMusic SSOT): arama yaparsın, deniz ve doğa hakkında yeni şeyler öğrenmek istersin, "kemençe nedir?", "balinalar nasıl uyur?" soruları aklındadır.
İkincil olarak Kaşif'sin: keşiflerini içinde yaşarsın, paylaşmazsın; denizin altını, ormanın içini merak edersin.
Doğan sessizdir — konuşmayı sevmezsin, kelimeler yetmez sana; denizi izlemek, yağmuru dinlemek konuşmaktan daha anlamlıdır (eski dosyanın Sessiz kişiliği — küme ataması SSOT'a göre Meraklı'dır, §7.2).
Müzik zevkin: Karadeniz türküleri, kemençe, hüzünlü keman, piyano, doğa sesleri, slow rock ballad. En sevdiklerin: Kazım Koyuncu, Volkan Konak, Vivaldi, Chopin, Enya.
Samsung Galaxy A34 5G kullanıyorsun (test referans); parlak beyaz yerine karanlık/sakin tema istersin, aile hesabında çocuk profisisin.
Görme ve işitme kısıtın yok ama sesli geri bildirim rahatsız edebilir — sessiz mod olsun; yüksek sesli animasyonlardan hoşlanmazsın.
Baban balıkçı, denizde çok zaman geçirir — onu özlersin; kedin Duman en yakın arkadaşındır.
Arayüz sade olmalı: çok buton ve seçenek olmamalı, düzen değişmemeli.
Reklam toleransın düşük-orta; premium'u bilmezsin, aile planı vardır. Sosyal özellikleri kullanmazsın (veli onayı yok).
Test edilecek akışlar: basit arama + filtre geri bildirimi (ADR-023 satır 11), keşif/öneri kalitesi, minimum etkileşim + otomatik ilerleme, sakin/karanlık tema, veli onayı, erişilebilirlik denetimi.
Davranış notları:
- Çok buton/çok seçenek görürsen sessizce terk edersin; sade ekran şart.
- Sesli geri bildirim rahatsız edebilir — sessiz mod seçeneği bulunmalı.
- Müzik senin için konuşmanın yerine geçer; doğru şarkıyı bulmak (arama) kritik.
- Değişiklikten hoşlanmazsın — güncellemeler alışkanlıklarını bozmamalı.
- Kişiselleştirme veli onayıyla başlar; onaysız akış bozulmadan devam etmeli.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — çocuk persona'sı karmaşık komut kabul etmez |
| Sürpriz yasak | Ani yüksek ses/şaşırtıcı animasyon yok; kilit ve hata ekranı dostane |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |
|---|------|-------|----------|-----------|--------|------|
| 1 | Ana sayfa yükleme (çocuk profili) | CoreMusic'i aç | LCP ≤ **2500 ms**, iskelet düzen, CLS ≤ **0.1**, sessiz açılış (ani ses yok) | Performance trace + screenshot | LCP ≤2500 ms · CLS ≤0.1 (P8.1) | 1.4.3 |
| 2 | Basit arama (kısa sorgu) | "kemençe" yaz, ara | 1-3 kelimelik sorgu çalışır; sonuç anlık, odak arama kutusunda (ADR-023 satır 11) | DOM assertion + trace | INP ≤200 ms (P8.1) | 2.4.7 |
| 3 | Filtre geri bildirimi | Tür/sakin filtresini aç-kapat | Filtre durumu görünür; geri alma tek dokunuş; sonuç listesi güncellenir | Manuel + DOM assertion | filtre yanıt süresi | 2.4.11 |
| 4 | Oynat / duraklat (büyük hedef) | Tek dokunuşla kontrol | Çift tıklama tek sayılır; şarkı ATLANMAZ | Manuel + DOM assertion | hata oranı 0 | 2.5.7 |
| 5 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Menü ve sanatçı adlarını aç | Karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | mojibake = 0 | - |
| 6 | Otomatik ilerleme / doğa sesleri döngüsü | Doğa sesleri listesini başlat | Kesintisiz döngü; döngü geçişi duyulmaz; uzun pasif dinlemede durmaz | E2E + trace | otomatik ilerleme sürekliliği | - |
| 7 | Ebeveyn kilidi (PIN) | Ayarlara gir | PIN korumalı; dostane ve erişilebilir kimlik doğrulama; kurtarma yolu var | E2E (Playwright) + manuel | erişilebilir kimlik doğrulama (P5.4) | 3.3.8 |
| 8 | Veli onayı akışı | Kaydı başlat | Onay istemi görünür; reddedince kişiselleştirme başlamaz | E2E (Playwright) + manuel | TMK 18 / COPPA 13 / GDPR 16 (P6.4) | 3.3.7 |
| 9 | Reklam kapatma (ücretsiz) | Reklam X'ine bas | Kapatma ≥**24×24 CSS px**; odak reklama hapsolmaz | a11y audit + DOM assertion | dokunma hedefi (P5.4) | 2.5.8 |
| 10 | Bağlantı yavaşlatma | Ağ throttling uygula | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) | 150 ms · 1.6/0.75 Mbps (P8.3) | - |
| 11 | Hata sayfaları (kopma) | Bağlantıyı kes | Türkçe, sakin, kısa metin; yüksek sesli uyarı yok; büyük "Tekrar Dene" hedefi | Manuel + screenshot | INP ≤200 ms | 2.5.8 |
| 12 | Erişilebilirlik denetimi | Lighthouse + axe çalıştır | 1.4.3 / 1.4.11 / 2.4.7 / 2.5.7 / 2.5.8 ihlali yok (dark temada dahil) | Lighthouse / axe | Lighthouse A11y (P8.6) | 1.4.11 |
| 13 | Kırılma noktası kontrolü | Viewport değiştir | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) düzen bozulmaz | Playwright `setViewportSize` + screenshot | 360×780 viewport (`⚠️ DERIVED`) | 1.4.13 |
| 14 | Klavye / odak sırası | Klavye ile gez | Odak sırası mantıklı; odak görünür; gizli odak yok | Klavye navigasyon testi + DOM assertion | - | 2.4.7 |
| 15 | Tutarlı yardım | Sayfalar arasında gez | Yardım erişimi her sayfada aynı yerde; düzen değişmez | DOM assertion | konum tutarlılığı | 3.2.6 |
| 16 | TBT / uzun görev kontrolü | Ağır listede kaydır | TBT ≤ 50 ms; main thread bloklanmaz (INP proxy'si) | Performance trace (P8.6) | TBT ≤50 ms | - |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik` · `WCAG`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir). WCAG sütunu yalnız P5 listesindeki kriterleri taşır.*

**Ek doğrulama notları (test koşusundan önce — research-bank P8 `VERIFIED`):**

- **TBT:** > 50 ms ise uzun görev (long task) main thread'i bloklar; TBT, INP'nin laboratuvar proxy'sidir. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/optimize-vitals-lighthouse — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Test kırılım noktaları (P8.5 — 9 adet):** 320 × 568 · 375 × 667 · 375 × 812 · 414 × 896 · 768 × 1024 · 1280 × 720 · 1600 × 1200 · 1920 × 1080 · 2560 × 1440 — persona viewport'u (`360 × 780` / `412 × 915`, `⚠️ DERIVED`) bu setle çapraz kontrol edilir. `Kaynak: [k1] + [k2]` (playwright.dev/docs/emulation + mintlify mobile-emulation — research-bank P8.5 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; persona'ya özel metrik hedefi bu dosyaya yazılmaz. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, okul, ulaşım, aile detayları (kedi Duman, balıkçı baba) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (9) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3.2 | — | `Kaynak: kurgusal (persona verisi; index §6.3.2 ataması)` |
| 3 | `veli_onayı_gerekli: true` (politika sonucu) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Doğum tarihi 20 Kasım 2017 (yaş 9 ile hizalı) | Kurgusal + hizalama | eski salt-okunur persona dosyası (20 Kasım 2019) | [[personas/index]] §6.3.2 | `⚠️ VERIFICATION REQUIRED` — §7.2'de çelişki |
| 6 | Fiziksel özet satırları (boy, kilo, giyim) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | Big Five puanları (E20 · A65 · C50 · N60 · O60) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi (P7.3 tam adları ↔ mood-taxonomy §3.3 harfleri) | Vault içi kontrol | research-bank P7.3/P7.5 | [[personas/mood-taxonomy]] §3.3 satır 13 ("Yüksek A, orta N") | Harfler P7.3 ile eşleşir — çelişki YOK |
| 12 | Mood küme adı (Meraklı) | Vault referanslı atama | [[personas/mood-taxonomy]] §3.3 satır 13 | [[personas/index]] §6.3.2 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı ("Soru soran, keşfeden çocuk; sık arama yapar") | Vault verisi | [[personas/mood-taxonomy]] §3.3 satır 13 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 14 | Eski mood (birincil Sessiz / ikincil Kaşif) | Çelişkili | eski salt-okunur persona dosyası | [[personas/index]] §6.3.2 | `⚠️ VERIFICATION REQUIRED` — birincil Sessiz→Meraklı §7.2'de; ikincil Kaşif korundu |
| 15 | Küme eğilimi (yüksek A, orta N) ↔ persona A65/N60 | Uyumlu | [[personas/mood-taxonomy]] §3.3 satır 13 | Big Five tablosu (bu dosya) | Eğilim ile puan uyumlu — çelişki yok |
| 16 | §3.3 satır 13'te küme BPM aralığı | Kümede yok | [[personas/mood-taxonomy]] §3.3 satır 13 | — | Küme BPM'i kullanılmadı — sayısal iddia yok |
| 17 | Tür BPM aralıkları (P4.4: Pop · Nu R&B · Electro · Dance) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 18 | Türkü/klasik/ambient/slow rock/ilahi için tür-BPM aralığı | Kapsam dışı | research-bank P4.4 (bu türler listede yok) | — | `⚠️ VERIFICATION REQUIRED` — sayı yazılmadı |
| 19 | Şarkı bazlı BPM; eski "40-100 BPM" iddiası | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — şarkı BPM'i yazılmadı |
| 20 | Favori sanatçı adları (Kazım Koyuncu, Volkan Konak, Vivaldi, Chopin, Enya) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 21 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 22 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 23 | Eski cihaz ("annenin eski telefonu" — model belirtilmemiş) | Doğrulanamaz (EXCLUDED) | research-bank P3.3 / X-1 (model yok → spec yok) | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 24 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 25 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 26 | Persona renk kombinasyonu kontrastı (koyu palet dahil) | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 27 | 44/48 px dokunma hedefi iddiası (küme notu) | Doğrulanmadı | [[personas/mood-taxonomy]] (⚠️ WCAG research-bank ibaresi) | research-bank P5 (yalnız 24×24 var) | `⚠️ VERIFICATION REQUIRED` |
| 28 | P5 kapsamında olmayan AA kriterleri (1.4.1, 1.4.4, 2.4.3, 3.3.1, 4.1.2…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 29 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Eski dosyadaki istatistik/gelir/kan grubu/burç iddiaları (~20.000 TL, 0 RH-, Akrep dahil) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 4 | Zero Hallucination (ADR-005) | Her satır §3.5.11 etiketi taşır; bankada olmayan iddia yazılmaz | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`kuzey-koc.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 18 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `türkü 70 BPM` | Tür aralığı P4.4 dışıysa: `⚠️ VERIFICATION REQUIRED`; şarkı BPM'i her koşulda: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Eski telefon modeli = viewport değeri` (eski veri) | `yalnız A34 VERIFIED; eski telefonun modeli belirtilmemiş ⚠️ VERIFICATION REQUIRED (X-1)` |
| `Meraklı kümesi yüksek A, o zaman persona bilimsel olarak yüksek A` | `küme eğilimi ≠ bilimsel ölçüm; puan kurgusal, uyum §7.2'de` |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[personas/research-bank]] P1-P8 + [[personas/mood-taxonomy]] §3.3 satır 13 + [[personas/index]] §6.3.2 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra kaynak etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append (raporlanır) | Doğrulanmış dosya |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 → §6.1 → VERIFY → LOG
```

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/documentation/docs-md-template.md` §4.2-§4.6 | Her üretimde |
| 2 | research-bank P1 (demografi), P2 (okul), P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya salt-okunur; yalnız kurgusal kimlik/müzik/rutin taşınır | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §3.5.11 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift `{{` | §4 bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-ölçüsüz eşik uydurma | "Kuzey için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Eski SSOT çelişki unutulması | Yaş 7 veya mood Sessiz taşıması | §7.2 çakışma satırını ekle; index kazanır |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 7 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 6 sütun (`| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |` birebir)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 18 yaş altı: `veli_onayı_gerekli: true` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok; türkü/klasik/ambient/slow rock/ilahi kapsam dışı bırakıldı
- [ ] Mood küme adı `mood-taxonomy` §3.3 satır 13 (Meraklı) ile birebir; eski mood (Sessiz) çelişkisi §7.2'de kayıtlı
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 16 (asgari 10, 6 sütun) |
| Kaynak & Doğrulama satırı | 31 |
| Frontmatter alan | 7 zorunlu + 5 ek |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only (bu turda raporlanır, dosyaya yazılmaz) |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[personas/research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[personas/mood-taxonomy]]` | Mood küme adları ve tanımları (§3.3 satır 13 Meraklı) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 7 | 9 | [[personas/index]] §6.3.2 kazanır; doğum tarihi 2019→2017 hizalandı |
| Doğum Tarihi | 20 Kasım 2019 | 20 Kasım 2017 (yaş 9 ile tutarlı) | SSOT yaş kazanır; tarih kurgusal olarak hizalandı |
| Mood (birincil) | Sessiz | Meraklı | **ÇAKIŞMA** — [[personas/index]] §6.3.2 kazanır; Sessiz davranışları kişilik olarak korundu, küme ataması olarak taşınmadı (§2.2: Erkek Çocuk Meraklı ×2: Kerem, Kuzey) |
| Mood (ikincil) | Kaşif | Kaşif (korunur) | Çelişki yok — küme §3.2.6'da mevcut; "eski ikincil korunur" kuralı |
| Sınıf | 2. sınıf | 4. sınıf (yaş 9 ile hizalı) | SSOT yaş kazanır; sınıf kurgusal olarak hizalandı |
| Birincil cihaz | Annesinin eski telefonu (model belirtilmemiş) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3; eski cihaz modeli yok → spec yazılmadı |
| Ekran çözünürlüğü | Bilinmiyor (eski cihaz) | 1080×2340 → viewport 360×780 (`⚠️ DERIVED`) | X-1: doğrulanmamış cihaz spec'i yazılmadı |
| BPM aralığı | 40-100 BPM | Yalnız tür aralıkları (P4.4) + kurgusal tercih | X-2: türleri P4.4 dışı; şarkı/türe özel doğrulanmamış BPM yazılmadı |
| Küme Big Five eğilimi | "Yüksek A, orta N" | Persona A65 · N60 | Eğilim ile puan **uyumlu** — çelişki yok; §3.5.3'te kayıtlı |
| Küme harf eşlemesi | §3.3 satır 13: "A", "N" | P7.3: A=Agreeableness, N=Neuroticism | Harfler eşleşir — §3.2.x'teki karışıklık bu kümede YOK |
| Gelir/kan grubu/burç iddiaları | ~20.000 TL, 0 RH-, Akrep | Yazılmadı (bankada yok) | X-10: doğrulanamaz iddia bu şablonda taşınmaz |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P1-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı, yaş 7→9 index §6.3.2 ile düzeltildi, mood Sessiz→Meraklı SSOT çakışması §7.2'ye kaydedildi (eski davranışlar kişilikte korundu), test tablosu 6 sütuna genişletildi, veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/erkek-cocuk/kuzey-koc
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
