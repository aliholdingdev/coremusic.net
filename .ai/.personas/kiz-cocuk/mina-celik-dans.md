---
title: "CoreMusic — Persona: Mina Çelik"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/mina-celik-dans"
updated: 2026-09-26
group: kiz-cocuk
age: 10
mood: Dans Enerjik
persona_id: CM-MC-10-DAN-IST
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Mina Çelik

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[personas/mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **kiz-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 10 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır — odağı dans/enerji odaklı dinleme ve BPM bazlı gezinmedir.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Dans Enerjik / Yaratıcı), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | kiz-cocuk (4-11) |
| Birincil mood | Dans Enerjik ([[personas/index]] §6.3 ataması) |
| Test odağı | BPM filtreleme, dans modu (tam ekran), playlist paylaşımı, hızlı skip, ebeveyn kilidi |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| BPM filtreleme & sıralama | Level 2 + 3 | Dans Enerjik: yüksek BPM aralığı beklentisi, hızlı sonuç |
| Dans modu (tam ekran / ayna) | Level 2 | Kesintisiz çalma + dikkat dağıtmayan arayüz |
| Çocuk modu ana sayfa / keşif | Level 2 + 3 | Yaratıcı ikincil küme: görsel-odaklı öneri kartı |
| Ebeveyn kilidi & veli onayı akışı | Level 2 | 3.3.8 erişilebilir kimlik doğrulama; kilit dostanlığı |
| Müzik kesintisizliği (SPA gezinme) | Level 3 | CLS ≤ 0.1, INP ≤ 200 ms eşikleri |
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
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); şarkı bazlı BPM yazılmaz (P4.7 / X-2).

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
| **Ad Soyad** | Mina Çelik | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 10 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 23 Nisan 2016 | Kaynak: kurgusal (persona verisi; yaş 10 ile tutarlılık için korundu) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | İstanbul / Kadıköy / Moda | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Özel İstanbul Koleji, İlkokul 4. sınıf | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Okul servisi (ev-okul ~2 km) + aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Aile hesabında "minadans" (ebeveyn gözetiminde) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-MC-10-DAN-IST` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `MC` | Ad + soyad ilk harfleri (Mina Çelik → M, Ç → ASCII `C`) |
| `YY` | `10` | Yaş 2 hane (10 → `10`) |
| `ZZZ` | `DAN` | Mood tür kodu (Dans Enerjik → ilk kelime "Dans" → `DAN`) |
| `XXX` | `IST` | Şehir kodu (İstanbul → IST; Türkçe karakter katlamı: İ→I) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 10 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (ayakkabı numarası, diş teli, postür, persentil…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 142 cm (yaşıtlarına göre uzunca — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 32 kg, atletik-zayıf (dans nedeniyle esnek) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Açık kestane düz saç (omuz hizası), yeşil göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (görme ve işitme taraması normal — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Dans kıyafeti odaklı (tayt, spor üst); profil fotoğrafında hareketli poz (kontrast: koyu kıyafet × açık zemin — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Hızlı hareket/dans sırasında titreşimli dokunma → hedef kaçırma riski; büyük ve sabit dokunma hedefleri bekler | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 80 | Sahnede dans etmeyi, yeni arkadaşlar edinmeyi ve sınıfında söz almayı sever; "sahne onun doğal ortamıdır". | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | Grup dansında takım oyuncusudur ama koreografi konusunda fikrini söylemekten çekinmez ("bu hareket şöyle daha iyi"). | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 50 | Dans pratiğini aksatmaz; okul ödevlerinde aynı disiplini göstermez — "önce ödev, sonra dans" kuralını hatırlatma ister. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 30 = dengeli taraf; yarışma kaybında birkaç saat üzülür, müziği açıp dans ederek toparlanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 90 | Yeni dans stillerini, yeni türleri ve yeni koreografileri ilk deneyen odur; son 6 ayda K-Pop'tan Latin'e sıçramıştır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness) → P7.5 kuralı ("kod esastır"); çelişki §3.5.11'e kayıtlıdır |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Dans Enerjik |
| **İkincil küme** | Yaratıcı |
| **Küme tanımı (alıntı)** | "Dans kursı/workout + hareketli müzik (çocuk ve genç erkek ortak adı)" ([[mood-taxonomy]] §3.4 #25) · ikincil: "Görsel-müzik eşleşmesi seven; resim/yazı gibi yaratıcı çalışırken uzun oturum kurar" (§3.2.10) |
| **Tetikleyici durumlar** | Dans edilebilir BPM aralığında yeni öneri; koreografi/klip görseli (yaklaşma); reklamla müziğin kesilmesi (ritim kopması → öfke) |
| **UI etkisi** | Dans Enerjik: "Koreografi videoları, workout playlist, yüksek BPM" + canlı renkler, hızlı animasyon, yüksek kontrast ([[mood-taxonomy]] §3.4 #25 / §3.2.2 "UI tercihi"); Yaratıcı ikincilken albüm kapağı/görsel önceliği (§3.2.10) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Dans Enerjik); ikincil = eski salt-okunur persona kaynağı (Yaratıcı) — çelişki kaydı §7.2'de |
| Test etkisi | ADR-023 satır 12 (hızlı navigasyon + geri tuşu) + §3.4 #25 "yüksek BPM filtresi" — [[personas/mood-taxonomy]] |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Sık skip (Dans Enerjik/Enerjik) | Skip sonrası anlık geri bildirim, INP ≤ **200 ms** | Performance trace (P8.1 VERIFIED) |
| 2 | BPM aralığına göre filtreleme | Filtre < 500 ms; sonuçlar istenen aralıkta | DOM assertion + timing |
| 3 | Dans moduna geçiş | Tam ekran, bildirim kapalı, < 500 ms geçiş | Manuel + screenshot |
| 4 | Görsel-öncelikli keşif (Yaratıcı) | Albüm kapağı büyük; 1.4.11 ≥3:1 kontrast | A11y audit + DOM |
| 5 | Geri tuşu beklentisi (ADR-023) | Geri tuşu her adımda görünür, kaybolmaz (2.4.11) | DOM assertion |
| 6 | Reklam/kesinti tetikleyicisi | Kesintide durum mesajı + kaldığı yerden devam | Manuel + trace |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Türkçe Pop (hareketli) 2) Hip-Hop / Rap (Türkçe) 3) EDM / Elektronik Dans 4) Latin Pop / Reggaeton 5) K-Pop | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Tarkan, Demet Akalın, Hande Yener, Aleyna Tilki, Murat Boz | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **Favori yabancı sanatçılar (5)** | Shakira, Blackpink, David Guetta, Dua Lipa, J Balvin | Kaynak: kurgusal (persona verisi) + `⚠️ VERIFICATION REQUIRED` (sanatçı realitesi P4 dışı) |
| **BPM aralığı (tür)** | Pop **80-120** · Dance **110-135** · Disco **100-130** · Electro **90-130** · House **110-128** · Hip Hop **80-130** · Techno **130-140** | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **Rap/Trap BPM** | boom-bap **85-95** · trap **70-110** · modern rap beat'i **~140** | Kaynak: nevamuzik.com.tr + vocuno.com/tr/bpm-algilayici (research-bank P4.5 `VERIFIED`) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Dans pratiği için 100-135 BPM; soğuma/uyku öncesi 70-90 BPM | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:00-07:40 (uyanma/dans), 16:30-17:30 (ödev arka plan), 20:00-21:00 (ev pratiği) · Hafta sonu 09:00-11:00 (koreografi keşfi), 15:00-17:00 (aile dansı) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (çocuk modu + aile premium) + YouTube (koreografi izleme, ebeveyn gözetiminde) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | BPM değerine bakar → 30 sn dinler "dans edilebilir mi?" testi → favoriye alır; haftada 10+ yeni şarkı | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %100 (aile premium planı zaten alınmış — reklam ritmi bozar) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türkçe Pop (hareketli) | Dans edilebilir güçlü ritim; playlist'inin çoğu bu tür oluşturur |
| 2 | Hip-Hop / Rap | Hip-hop dans dersleriyle paralel; güçlü beat koreografiye uyar |
| 3 | EDM / Elektronik | Drop'lar dans finali için ideal — "dansın yakıtı" |
| 4 | Latin Pop / Reggaeton | Dans kursunda Latin figürleri öğrendi; ritim kalçayı hareket ettirir |
| 5 | K-Pop | Senkronize koreografileri YouTube'dan izleyip öğreniyor |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:00-07:40 | Uyanma + evde ısınma | Türkçe Pop (enerjik) |
| Hafta içi | 08:00-08:20 | Okul servisi | Arkadaşla paylaşımlı pop |
| Hafta içi | 16:30-17:30 | Ödev (arka plan) | Enstrümantal / lo-fi |
| Hafta içi | 17:45-19:15 | Dans kursu (haftada 3 gün) | Kurs müziği (Pop/EDM/Hip-Hop) |
| Hafta içi | 20:00-21:00 | Ev pratiği (ayna karşısı) | Koreografi playlist'i (100-135 BPM) |
| Hafta sonu | 09:00-11:00 | Yeni koreografi keşfi | K-Pop / Latin / EDM |
| Hafta sonu | 15:00-17:00 | Ailece dans gecesi | Karışık aile playlist'i |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Mood-taxonomy §3.4 #25'nin 120-160 BPM küme notu da birincil ölçüm değildir; test hedefi olarak VERIFIED tür aralıkları kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 100 Mbps fiber (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (çocuk modu renkli varyantla; dans modunda koyu) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 90 dk/gün, hafta sonu 120 dk/gün (ebeveyn limiti; dans içeriği aile kuralıyla hariç) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 6/10 — video çeker/düzenler, filtre kullanır, playlist paylaşır; SMS/metin yazımı hızlı değil | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | iPad, aile PC'si, Smart TV — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — dans modu koyu temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; kulaklıkla yüksek ses tercihi (kurgu) | Çocuk kulaklık ses güvenliği eşiği research-bank P5'te YOK → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | ince motor iyi; hareket halindeyken dokunma | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | 4. sınıf okuyucusu; dans odakında hiperfokus | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Motor gelişim / duygusal düzenleme" ifadeleri 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Dans Enerjik: müzik duyduğu anda hareket eder; sabah ilk işi enerjik bir şarkı açmaktır, sık skip eder.
- Yaratıcı ikincil küme: kendi koreografilerini üretir, görsel-müzik eşleşmesine önem verer (kapak görseli, renk uyumu).
- Rekabetçi ama sportmence: yarışma kaybında birkaç saat üzülür, dans ederek toparlanır.
- Sosyal onay ihtiyacı yüksek: alkış ve takdir en büyük motivasyonu; sıra beklemede sabırlıdır ama sırayı kimin yöneteceğine karışmak ister.
- Tekrar güvenir: ezberlediği koreografiyi loop'a alıp tekrar tekrar dinler.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — aile premium planı zaten var; çocuk "ister", anne/baba karar verir | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 4 sn'de sıkılır; dans sırasında spinner'a 5 sn dayanır, sonrası öfke | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Teknik hata metni görürse "bütün enerjim gitti" der; dostça + kaldığı yerden devam şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 0/10 — ritim kopması = en büyük tetikleyici; Premium isteğinin kaynağı | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + tekrar: koreografi videosu izleyip taklit eder, yazıdan çok hareket | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Dans kursunda arkadaşlarına playlist ve figür öğretir; sırayı paylaşır | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:00 sonrası dikkat düşer; karmaşık akış yerine "Uyku Öncesi" listesi ister | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tanıdık dans/koreografi görseli → keşif davranışı tetiklenir | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] Dans Enerjik |

*Erişilebilirlik etkisi (özet):* hareket halinde kullanım + hızlı atlama davranışı → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sürükleme yerine tek dokunuş (2.5.7) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Mina Çelik'sin. 10 yaşında, İstanbul/Kadıköy-Moda'da yaşıyorsun, Özel İstanbul Koleji 4. sınıf öğrencisisin.
Birincil mood'un Dans Enerjik, ikincil olarak Yaratıcı'sın: müzik duyduğunda dans etmeden duramazsın, kendi koreografilerini yaratırsın.
Dans kursuna haftada 3 gün gidiyorsun (modern, hip-hop, caz/Latin); ailen yaratıcıdır, bir de 6 yaşındaki erkek kardeşin ve bir köpeğin var.
Müzik zevkin: hareketli Türkçe pop, Türkçe hip-hop/rap, EDM, Latin pop, K-Pop. Favori Türk sanatçıların: Tarkan, Demet Akalın, Hande Yener; yabancı: Shakira, Blackpink, Dua Lipa.
Samsung Galaxy A34 5G kullanıyorsun, light temasındasın (dans modunda koyu), internetin evde 100 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; hareket halindeyken büyük ve sabit dokunma hedefleri beklersin.
Sabır eşiğin 4 sn; reklam ritmini bozduğu için reklamdan nefret edersin (Premium ailenizde).
Test edilecek akışlar: BPM filtreleme, dans modu (tam ekran + bildirim kapalı), playlist oluşturma/paylaşma, hızlı skip navigasyon + geri tuşu, favori, ebeveyn kilidi, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- BPM değerine göre filtrelersin; aradığın aralıkta sonuç yoksa "en yakın aralık" yönlendirmesi beklersin.
- 30 saniyede "dans edilebilir mi?" testi yapar, beğenmezsen hızla atlarsın (sık skip).
- Dans modunda dikkat dağıtan hiçbir şey (bildirim, popup) istemezsin; albüm kapağını/görseli büyük görürsün.
- Türkçe telaffuzun nettir; arayüzde "sen" dili ve kısa, enerjik metinler beklersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 10 yaş "karmaşık komut" yerine net davranış ister |
| Sürpriz yasak | Dans modu senaryosunda ansızın ses yükseltme/kesme sürprizi yok (ritim kopması tetikleyicisi) |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |
|---|------|-------|----------|-----------|--------|------|
| 1 | Ana sayfa yükleme (çocuk modu) | CoreMusic'i aç, kişiselleştirilmiş ana sayfayı bekle | Dans/enerji önerileri üstte, büyük kartlar | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** | 1.4.11 ≥3:1 |
| 2 | BPM filtreleme | Dance için 110-135 BPM aralığını seç | Sonuçlar yalnız istenen aralıkta; etiket görünür | DOM assertion + timing | filtre < **500 ms** | 2.5.8 ≥24×24 px |
| 3 | Dans modu (tam ekran) | Dans Modu'nu aç, 30 sn bekle | Minimal arayüz, bildirim yok, < 500 ms geçiş | Manuel + screenshot | geçiş < **500 ms** | 2.4.7 odak görünür |
| 4 | Hızlı skip navigasyon | 5 şarkıyı art arda atla | Anlık geri bildirim; geri tuşu kaybolmaz | Performance trace + DOM assertion | INP ≤ **200 ms** | 2.4.11 |
| 5 | Playlist oluşturma & paylaşma | "Latin Ateşi 2" playlist'i aç, 15 şarkı ekle, paylaş | Tek dokunuş ekleme; paylaşım link/QR ile | Manuel + LocalStorage kontrolü | oluşturma < **1 dk** | 2.5.7 |
| 6 | Şarkı çalma (play / duraklat) | Çal, duraklat, kaldığı yerden devam et | İlk ses < 1 sn; büyük oynat kontrolü | Trace + console log | ilk ses < **1 sn** | 2.4.7 |
| 7 | Favori (kalp) ekleme | Kalbe dokun, uygulamayı kapat-aç | Görsel + sesli geri bildirim; durum kalıcı | DOM assertion + LocalStorage kontrolü | — | 2.5.8 ≥24×24 px |
| 8 | SPA navigasyon (sayfalar arası) | Dans modu açıkken kategoriye geç | Müzik kesintisiz; çakışma yok | Trace + console log | CLS ≤ **0.1** | — |
| 9 | Ebeveyn kilidi (ayar geçişi) | Çocuk modu dışına çıkış dene | Kilit devrede; 3.3.8 gözetilir | Manuel + form assertion | — | 3.3.8 |
| 10 | Bağlantı yavaşlatma | Slow 4G koşulunda ana sayfa + çalma | Hata yok; yaşa uygun yükleme animasyonu | Network throttling + trace | **150 ms / 1.6 Mbps / 750 Kbps** (P8.3) | — |
| 11 | Hata sayfaları (404 / kopma) | Bozuk URL aç | Türkçe, dostça metin; büyük "Tekrar Dene" | Manuel + screenshot | — | 2.5.8 ≥24×24 px |
| 12 | Erişilebilirlik denetimi | Lighthouse + axe koş | 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | A11y skoru raporlanır | 1.4.3 ≥4.5:1 |
| 13 | Kırılma noktası kontrolü | `360 × 780` ve `412 × 915` görünümlerini aç | Düzen bozulmaz, dans kartları taşmaz | Playwright `setViewportSize` + screenshot | — | 1.4.11 ≥3:1 |
| 14 | Çevrimdışı / yavaş ağ davranışı | Ağ kopart | Teknik hata metni görünmez; durum dostane | CDP `emulateNetworkConditions` + manuel | — | — |
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
| 1 | Ad, doğum tarihi, semt, okul, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (10) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.2 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adları (Dans Enerjik / Yaratıcı) | Vault referanslı atama | [[personas/mood-taxonomy]] §3 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[personas/mood-taxonomy]] §3.4 #25 + §3.2.10 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 13 | Tür BPM aralıkları (Pop 80-120 · Dance 110-135 · Disco 100-130 · Electro 90-130 · House 110-128 · Hip Hop 80-130 · Techno 130-140) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 14 | Rap/Trap BPM (boom-bap 85-95 · trap 70-110 · modern rap ~140) | Gerçek-dünya | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED`) |
| 15 | Şarkı bazlı BPM ("Şımarık 110 BPM" vb. eski kayıtlar) | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 16 | Favori sanatçı adları (Tarkan, Demet Akalın, Hande Yener, Aleyna Tilki, Murat Boz, Shakira, Blackpink, David Guetta, Dua Lipa, J Balvin) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 17 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 18 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 19 | Diğer cihazlar (iPad, aile PC'si, TV) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 20 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 21 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 22 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 23 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 24 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 25 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Eski dosyadaki istatistik iddiaları (TÜİK/RTÜK/WHO/Apple Screen Time yüzdeleri) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 28 | Eski cihaz/okul/yaş/mood değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`mina-celik-dans.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şımarık 110 BPM` | Tür aralığı (Dance 110-135, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

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
| Persona-özel eşik uydurma | "Mina için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| `[[personas/mood-taxonomy]]` | Mood küme adları ve tanımları (§3.4 #25, §3.2.10) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 10 | 10 | Çelişki yok (index ile aynı) |
| Mood | Enerjik (birincil) / Yaratıcı (ikincil) | Dans Enerjik (birincil) / Yaratıcı (ikincil) | [[personas/index]] §6.3 "Dans Enerjik" kazanır; ad [[personas/mood-taxonomy]] §3.4 #25 listesinden |
| Birincil cihaz | Apple iPad 9. nesil (EXCLUDED) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3 |
| Şarkı bazlı BPM ("Şımarık 110 BPM" vb.) | Eski kaynak tablosunda yazıyordu | yazılmadı | P4.7 / X-2 → `⚠️ VERIFICATION REQUIRED` |
| BPM istekleri (dans stili bazlı) | Stil-BPM listesi (kurgu) | Persona tercihi olarak korundu + tür aralıkları P4.4 ile etiketlendi | Kurgu/gerçek ayrımı §3.5.5'te |
| Etki alanındaki istatistikler (TÜİK/RTÜK/WHO/Apple) | Kaynaksız yüzdeler | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma) |
| Fiziksel detay (100+ satır: ayakkabı, diş, postür) | Ayrıntılı | 5-8 satıra indirildi | Şablon §3.5.2 standardı |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, mood index §6.3'e göre "Dans Enerjik" olarak düzeltildi; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/mina-celik-dans
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
