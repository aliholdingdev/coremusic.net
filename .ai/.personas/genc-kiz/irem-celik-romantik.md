---
title: "CoreMusic — Persona: İrem Çelik"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-kiz/irem-celik-romantik"
updated: 2026-09-26
group: genc-kiz
age: 13
mood: Romantik
persona_id: CM-IC-13-ROM-EDR
veli_onayı_gerekli: true
---

# CoreMusic — Persona: İrem Çelik

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-kiz (13-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 13 yaşındaki bir genç kızın gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Romantik / Melankolik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-kiz (13-17) |
| Birincil mood | Romantik ([[personas/index]] §6.3 ataması) |
| Test odağı | Pastel/sevimli arayüz, büyük dokunma hedefleri, yaşa uygun içerik filtresi, ebeveyn kontrolü, tablet+dizüstü kırılma noktaları |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Mood-geçiş (ADR-023 satır 16) | Level 2 + 3 | Romantik arayüz → pastel palet, yavaş geçiş; geçersiz mood = error |
| Kayıt / yaş kontrolü (13 yaş) | Level 2 + 3 | Veli onayı akışı, minimum alan, yaşa uygun dil |
| Keşif & içerik filtresi | Level 2 + 3 | Yaşa uygun öneri; olgun içerik elemesi |
| Player & dokunma hedefleri | Level 2 + 3 | Büyük butonlar (2.5.8 ≥24×24 px), sezgisel kontrol |
| Erişilebilirlik denetimi | Level 2 | 1.4.3 ≥4.5:1 pastel-üzeri kontrast, 2.4.7 odak |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 30+ satırlık fiziksel/aile detayı (makyaj, gelir, burç, kan grubu…) bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.2.1 | Küme adı + tanım alıntısı (Romantik; ikincil §3.2.3 Melankolik) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | İrem Çelik | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 13 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 22 Eylül 2013 | Kaynak: kurgusal (persona verisi; yaş 13 ile tutarlılık için — eski kayıtla aynı) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Edirne / Merkez | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Edirne Ortaokulu, 7. sınıf | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Yaya / aile arabası (okul merkeze yakın — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | irem.hayalperest | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-IC-13-ROM-EDR` | Kaynak: kurgusal (persona verisi; eski footer `GC-IC-…` → şablon formatına uyarlandı, §7.2) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `IC` | Ad + soyad ilk harfleri (İrem Çelik → İ→I, C) |
| `YY` | `13` | Yaş 2 hane (13 → `13`; şablon örnek aralığı 10-99'a uyar) |
| `ZZZ` | `ROM` | Mood tür kodu (Romantik → ROM) |
| `XXX` | `EDR` | Şehir kodu (Edirne → EDR) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 13 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). 13 yaş ayrıca TMK'ya göre reşit değil (18 altı) — hesap açış/oturum testlerinde veli onayı akışı zorunlu senaryodur. Persona tamamen kurgusaldır; gerçek kişi verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (P6.4 VERIFIED).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 30+ satırlık ayrıntı (giyim tarzı, aksesuar, burç, kan grubu, aile geliri…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 152 cm (ergenlik öncesi dönem — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 42 kg, minyon yapı | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Kestane uzun saç (genelde iki yandan örgülü), kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (görme ve işitme iyi — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Pastel tonlar (pembe, lila, bebek mavisi), çiçekli/sevimli stil; profil fotoğrafında soft ışık | Kaynak: kurgusal (persona verisi); renk eşleşmesi `⚠️ DERIVED` (bkz. §3.5.7) |
| **UI'ı etkileyen fiziksel kısıt** | Küçük eller → dokunma hedefi duyarlılığı; tablet/paylaşımlı cihazda parmak izi kalıntısı/görüş açısı testi | Kaynak: kurgusal (persona verisi); kriter: `Kaynak: [k1] + [k2]` (P5.4 — 2.5.8) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 40 | Ailesiyle çok rahat, tanıdıklara açıktır ama yabancılarla ve kalabalık gruplarda utangaçtır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 90 | Çok uyumlu ve söz dinler; çatışmadan kaçınır, öğretmenler ve ailesiyle ilişkisi uyumlu. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 70 | Ödevlerini zamanında yapar, odasını toplar; düzenli ama mükemmel değil. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 50 | **Ters kodlama notu:** yüksek N = düşük denge. Eski dosya "Duygusal Denge 5/10 — orta, kırılgan, çabuk üzülür"; doğrudan ×10 ile 50 taşındı, **yön doğrulanmadı** → §7.2 + `⚠️ VERIFICATION REQUIRED`. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 80 | Hayalperestir; masal/hikâye dünyasında yaşar, kendi hikâyelerini yazar, yeni hikâyeler ve filmler keşfetmeye açıktır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| N ekseni yönü | Eski başlık "Duygusal Denge" (yüksek = iyi denge) ↔ IPIP-NEO N (yüksek = nörotizm). Dönüşüm doğrudan ×10 (ters çevrilmedi); yön tartışması `⚠️ VERIFICATION REQUIRED` + §7.2 çakışma satırı |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Romantik |
| **İkincil küme** | Melankolik ([[mood-taxonomy]] §3.2.3) |
| **Küme tanımı (alıntı)** | "Aşk/duygu odaklı dinleyici; şarkıya duygusal bağ kurar, az skip eder, tekrar dinler" ([[mood-taxonomy]] §3.2.1) |
| **Tetikleyici durumlar** | Duygusal öneri kartı / sevimli görsel ve pastel palet (yaklaşma); ani yüksek kontrast flaş, kaba hata metni ve kalabalık düzen (geri çekilme) |
| **UI etkisi** | "Yumuşak renkler, yavaş animasyonlar, sıcak tonlar"; test etkisi: "Duygusal UI, renk paleti, slow geçiş animasyonları; ADR-023 satır 16 (mood-geçiş)" ([[mood-taxonomy]] §3.2.1) |
| **İkincil küme etkisi** | Melankolik: "Derin dinleyici; şarkı sözü okur, az etkileşim kurar, gece ve yalnız dinler" → gece/uyku öncesi dinleme ve düşük etkileşim akışı (§3.2.3) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Romantik); ikincil = eski salt-okunur persona kaynağı (Melankolik) — §7.2'de kayıtlı |
| Test etkisi | ADR-023 satır 16 (mood-geçiş, BPM eşikleri) + slow geçiş animasyonları — [[mood-taxonomy]] §3.2.1 |
| Taksonomi BPM notu | §3.2.1 "60-100 BPM ⚠️" işaretli kurgusal önermedir (§1.2); test eşikleri olarak research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Az skip, tekrar dinleme | Son dinlenenler / devam listesi doğru sırada | DOM assertion + localStorage kontrolü |
| 2 | Slow geçiş animasyonları | Ani kesme yok; geçiş ~300 ms bandında hissedilir ama INP ≤ **200 ms** | Performance trace + CSS assertion |
| 3 | Pastel / sıcak tonlar | Boş durum metni sevimli ve yönlendirici; kontrast ≥ **4.5:1** (1.4.3) korunur | Kontrast denetimi + screenshot |
| 4 | Mood-geçiş (ADR-023 satır 16) | Geçerli mood → arayüz uyarlanır; geçersiz değer → hata (error) | Manuel + hata akışı testi |
| 5 | Kırılganlık (eleştiriye kapalı) | Hata metni suçlayıcı değil, "sevimli karakter" tonunda ve yönlendirici | Manuel denetim |
| 6 | Melankolik (ikincil): gece dinleme | Uyku öncesi düşük etkileşim modu; az animasyon | Manuel + emülasyon |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Türkçe pop slow 2) Türkçe pop (hareketli) 3) Film/dizi müzikleri 4) Akustik cover 5) Çocuk şarkıları (nostalji) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Semicenk, Dedublüman, Mabel Matiz, BLOK3, Zeynep Bastık | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Pop **80-120** · Dance **110-135** · Electro **90-130** · House **110-128** BPM (bankada olan türler) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM (şarkı bazlı; slow/cover/çocuk türü)** | Yazılmadı — parçaya özgü ve kapsam dışı tür BPM'leri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Yavaş-orta (**60-100 BPM** — eski dosyadan; taksonomi §3.2.1 notuyla örtüşür ama kurgusal) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 08:00 (hafif), 15:00-16:00 (okul dönüşü), 18:00 (oyun/hayal), 20:30 (hikâye yazarken), 22:00 (uyku müziği) · Hafta sonu 4-5 sa (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (aile tabletine indirilmiş — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | YouTube önerileri + arkadaş önerileri; "Senin İçin" önerilerine güvenir | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %20 (aileye söylemeye çekinir — harçlık bütçesi) | Kaynak: kurgusal (persona verisi) |
| **Dinleme cihazı (kişisel)** | Aile tableti + kablolu kulaklık (telefonu yok — LGS'den sonra söz verildi) | Kaynak: kurgusal (persona verisi); test cihazı §3.6 (A34, VERIFIED) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türkçe pop slow | Romantik sözler, hayal kurmalık — masal dünyasının fonu |
| 2 | Türkçe pop (hareketli) | Odasında dans etmek için; bazen enerjik olur |
| 3 | Film/dizi müzikleri | Sevdiği filmlerin şarkıları (özellikle animasyon/Disney tarzı) |
| 4 | Akustik cover | Basit ve duygulu; izlemesi kolay |
| 5 | Çocuk şarkıları (nostalji) | Küçükken dinledikleri; hâlâ seviyor |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:00 | Uyanma / kahvaltı | Hafif pop |
| Hafta içi | 15:00-16:00 | Okul dönüşü | Pop / dans |
| Hafta içi | 16:00-18:00 | Ödev + kitap | Arka plan (film müzikleri) |
| Hafta içi | 18:00-19:30 | Oyun, hayal kurma | Yoğun dinleme (slow) |
| Hafta içi | 20:30-22:00 | YouTube, hikâye yazma | Akustik cover / slow |
| Hafta içi | 22:00 | Uyku | Uyku müziği (melankolik ikincil) |
| Hafta sonu | 11:00-13:00 | Dans / oyun | Hareketli pop |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "60-100 BPM" tercihi de bankada doğrulanmadı → kurgusal tercih olarak taşındı; taksonomi §3.2.1'deki "60-100 ⚠️" de kurgusal önermedir (§1.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 50 Mbps (ev, kurgusal) / aile paylaşımı — yavaş ağ testi için aday | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (pastel/sevimli arayüz tercihi — kurgu) | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.1 (sıcak tonlar) |
| **Ekran süresi / kullanım** | Hafta içi 2-3 sa/gün, hafta sonu 4-5 sa/gün (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 4/10 — temel kullanım; menüler basit ve anlaşılır olmalı | Kaynak: kurgusal (persona verisi) |
| **Kişisel cihaz durumu (eski dosya)** | Telefonu YOK (LGS'den sonra söz verildi); aile tableti (kardeşiyle paylaşımlı), aile bilgisayarı (ödev), kablolu kulaklık — model/spec'ler bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor). Tablet görünümü için cihaz spec'i bankada yok → `⚠️ VERIFICATION REQUIRED`.

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); gözlük/lens yok | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — pastel-üzeri metin kritik | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; kablolu kulaklıkla dinleme | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | normal, ama küçük el + tablet → dokunma hedefi duyarlı | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | 13 yaş; basit/sezgisel akış bekler, karmaşık menü kaybeder | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 — hata metni sevimli ve yönlendirici | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | 13 yaş → veli onayı zorunlu (P6.4); "yaş kontrolü" akışları bu dosyanın test kapsamındadır |
| Renk kombinasyonu (pastel) | Persona pastel palet kontrastı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERISHED — pastel × beyaz riski testte kontrol edilir |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Hayalperest: masal ve hikâye dünyasında yaşar, kendi hikâyelerini yazar; "yazar veya anaokulu öğretmeni" olmak ister.
- Uyumlu ve nazik: söz dinler, öğretmenleri sevindirir; çatışmadan kaçınır, kalabalık gruplardan çekinir.
- Kırılgan: çabuk üzülür, azarlanmaktan korkar; hata mesajları suçlayıcı olmamalı.
- Sosyal medya yeni: YouTube ağırlıklı; TikTok yok (aile izni yok), WhatsApp yalnız aile grubu.
- Sevimli/renkli arayüz bekler: pastel palet, büyük butonlar, basit menüler.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Aile onaylı; harçlığını biriktirir, küçük şeyler alır — premium istemeye çekinir | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 3 sn; spinner'a ~6 sn dayanır, sonra sıkılır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Üzülür, odasına çekilir; hata ekranı "sevimli karakter" + yönlendirici ton ister | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.1 |
| **Reklam toleransı** | 7/10 — kısa reklamı idare eder, uzunda sıkılır ama sabreder | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + adım adım; basit ve anlaşılır menüler, büyük butonlar | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | En yakın arkadaşıyla birlikte dans/şarkı; playlist'lerini az kişiyle paylaşır | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 22:00 sonrası uykulu; karmaşık akış reddedilir, uyku müziği tercih edilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tanıdık şarkı/film müziği görünürse keşif davranışı tetiklenir | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.1 (az skip, tekrar) |

*Erişilebilirlik etkisi (özet):* pastel tema + küçük el → kontrast ≥ **4.5:1** (1.4.3) ve dokunma hedefi ≥ **24×24 CSS px** (2.5.8) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen İrem Çelik'sin. 13 yaşında, Edirne/Merkez'de yaşıyorsun, Edirne Ortaokulu 7. sınıf öğrencisisin.
Romantik ve hayalperest bir kişiliğin var: masallara, hikâyelere ve duygusal şarkılara bayılırsın, az atarsın, sevdiğin şarkıyı tekrar dinlersin; ikincil olarak Melankolik'sin — gece uyku öncesi düşük sesle dinlemeyi seversin.
Müzik zevkin: Türkçe pop slow, Türkçe pop (hareketli), film/dizi müzikleri, akustik cover, nostaljik çocuk şarkıları. En sevdiklerin: Semicenk, Dedublüman, Mabel Matiz, BLOK3, Zeynep Bastık.
Telefonun yok (LGS'den sonra söz verildi); aile tabletini kardeşinle paylaşıyorsun, kablolu kulaklık kullanıyorsun. Test cihazı Samsung Galaxy A34 5G'dir, light temada, internetin evde 50 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; küçük ellerin var — büyük butonlar ve basit menüler beklersin.
Sabır eşiğin 3 sn, spinner'a ~6 sn dayanırsın; hata metinlerinde suçlayıcı dil seni üzer, sevimli ve yönlendirici dil beklersin.
Test edilecek akışlar: mood-geçiş (pastel arayüz), kayıt/yaş kontrolü (veli onayı), keşif ve içerik filtresi, playlist oluşturma, player büyük butonlar, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Sevimli, renkli ve pastel arayüz seversin; karmaşık/yoğun düzenden sıkılırsın.
- "Senin İçin" önerilerine güvenir, sevdiğin türden kartı hemen açarsın.
- Yaşına uygun içerik filtresi beklersin; olgun/rahatsız edici içerik görmemelisin.
- Ebeveyn kontrolü ve zaman sınırı akışları senin için normaldir; çatışma çıkarmazsın.
- Hikâye yazmayı ve hayal kurmayı seversin; playlist açıklamalarında kısa, sevimli ifadeler kullanırsın.
- Türkçe telaffuzun doğaldır; arayüzde "sen" dili, sıcak ve kısa metin beklersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler; suçlayıcı/otoriter ton yok |
| Sürpriz yasak | Hassas persona: korkutucu/utanç verici senaryo yok; veli onayı senaryolarında onay akışı ihlal edilmez |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme | LCP ≤ **2500 ms**, CLS ≤ **0.1**, pastel karşılama kartı üstte | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Mood-geçiş (Romantik arayüz) | Yavaş animasyonlar devrede; geçersiz mood değeri hata (error) üretir (ADR-023 satır 16) | Manuel + hata akışı testi |
| 3 | Kayıt / yaş kontrolü (13 yaş) | Veli onayı akışı görünür; minimum alan; yaşa uygun dil (3.3.8) | Manuel + form assertion |
| 4 | Keşif / öneri (yaş filtresi) | Yaşa uygun öneri; olgun içerik elemesi; "Senin İçin" kartı üstte | DOM assertion + screenshot |
| 5 | Arama (basit sorgu) | Basit arama; yanlış yazım toleransı; sonuç listesi okunaklı | Manuel + DOM assertion |
| 6 | Playlist oluşturma | Tek dokunuşla oluşturma; sevimli kapak seçimi; sürükleme yerine dokunuş | DOM assertion + LocalStorage kontrolü |
| 7 | Player kontrolü (play/pause/skip) | Büyük butonlar ≥ **24×24 CSS px** (2.5.8); ilk ses < 1 sn | DOM assertion + trace |
| 8 | Şarkı çalma (devam) | Az atlama davranışı desteklenir; son dinlenenler sırada kalır | DOM assertion + sayaç kontrolü |
| 9 | SPA navigasyon | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 10 | Favori (kalp) ekleme | Sevimli geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 11 | Tema geçişi (light / pastel) | Geçiş müziği kesmez; iki temada da kontrast ≥ **4.5:1** (1.4.3) | Manuel + ekran görüntüsü + kontrast denetimi |
| 12 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok; yavaş yükleme sevimli skeleton | Network throttling + trace (P8.3 `VERIFIED`) |
| 13 | Hata sayfaları (404 / kopma) | Türkçe, sevimli, suçlayıcı olmayan metin + yönlendirici buton | Manuel + screenshot |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe |
| 15 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz; tablet görünümü spec'siz → `⚠️ VERIFICATION REQUIRED` | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 16 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Şarkı/sanatçı adlarında karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü |
| 17 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, ilçe, okul, sınıf, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (13) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.2 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | N ekseni yönü: eski "Duygusal Denge 5/10" ↔ N=50 | Vault içi belirsizlik | eski persona dosyası (5/10) | research-bank P7.3 | `⚠️ VERIFICATION REQUIRED` (doğrudan ×10; ters çevrilmedi — §7.2) |
| 12 | Mood küme adları (Romantik / Melankolik) | Vault referanslı atama | [[mood-taxonomy]] §3.2.1 + §3.2.3 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımları (Romantik + Melankolik) | Vault verisi | [[mood-taxonomy]] §3.2.1 | [[mood-taxonomy]] §3.2.3 | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Tür BPM aralıkları (Pop 80-120 · Dance 110-135 · Electro 90-130 · House 110-128) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 15 | Rap/Trap BPM (gerekirse: boom-bap 85-95 · trap 70-110 · modern rap ~140) | Gerçek-dünya (ikincil) | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED`) |
| 16 | Şarkı bazlı BPM; kişisel 60-100 tercihi; §3.2.1 "60-100" notu; slow/cover/çocuk türü BPM | Doğrulanmadı (EXCLUDED / kapsam dışı) | research-bank P4.7 / X-2 + P4 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — şarkı BPM'i yazılmadı; tercih kurgusal |
| 17 | Favori sanatçı adları (Semicenk, Dedublüman, Mabel Matiz, BLOK3, Zeynep Bastık) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 18 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 19 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 20 | Kişisel cihazlar (aile tableti, aile bilgisayarı, kablolu kulaklık; telefon YOK) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 21 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 22 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 23 | Persona pastel renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 24 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 25 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Eski dosyadaki istatistik/Platform iddiaları (YouTube kullanım sıklığı, "Spotify TR 2025" tarzı satırlar) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 29 | Eski cihaz/okul/mood değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`irem-celik-romantik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `romantik 60 BPM` | Tür aralığı (Pop 80-120, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.1/§3.2.3 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-özel eşik uydurma | "İrem için INP 500 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.1 / §3.2.3) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 13 (tutarlı) | 13 | Çelişki yok — [[personas/index]] §6.3 |
| Mood | Romantik (birincil) / Melankolik (ikincil) | Romantik / Melankolik | Tutarlı — index §6.3 + mood-taxonomy §3.2.1/§3.2.3 |
| Kullanıcı ID | `GC-IC-13-ROM-EDR` (eski footer) | `CM-IC-13-ROM-EDR` (şablon §3.5.1 `CM-XX-YY-ZZZ-XXX`) | Şablon formatı kazanır |
| Birincil test cihazı | Aile tableti (EXCLUDED — spec yok) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[research-bank]] P3; tablet spec'i `⚠️ VERIFICATION REQUIRED` |
| Kişisel cihazlar | Telefon YOK (LGS sözü), aile PC, kablolu kulaklık | Korundu (kurgusal) + spec yazılmadı | P3.3 / X-1 |
| N ekseni | "Duygusal Denge 5/10" (orta — kırılgan) | N = 50 (doğrudan ×10) | Çelişki saklı; eksen yönü yeniden doğrulamak `⚠️ VERIFICATION REQUIRED` (P7.5) |
| BPM tercihi | 60-100 (kişiye özel) | Yalnız tür aralıkları (P4.4) + kurgusal tercih | X-2: şarkı/türe özel doğrulanmamış BPM yazılmaz |
| Platform istatistikleri | YouTube sıklığı, "Spotify TR 2025" tarzı satırlar (AI prompt içinde) | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma) |
| Fiziksel/aile detayı | 30+ satır (giyim, burç, kan grubu, gelir…) | 6 satır (şablon §3.5.2 yasak listesi) | Şablon kazanır; detaylar eski dosyada korunur |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı; ID formatı şablona uyarlandı; veli_onayı `true` + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-kiz/irem-celik-romantik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode