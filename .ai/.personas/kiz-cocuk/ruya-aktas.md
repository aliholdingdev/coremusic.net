---
title: "CoreMusic — Persona: Rüya Aktaş"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/ruya-aktas"
updated: 2026-09-26
group: kiz-cocuk
age: 7
mood: Enerjik
persona_id: CM-RA-07-ENE-ANT
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Rüya Aktaş

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[personas/mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **kiz-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 7 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır — odağı hızlı navigasyon + anlık geri bildirim, güven veren boş durum metni ve sade büyük hedefli düzendir.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[personas/mood-taxonomy]] (Enerjik / Utangaç), veri → [[personas/research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | kiz-cocuk (4-11) |
| Birincil mood | Enerjik ([[personas/index]] §6.3 ataması) |
| Test odağı | Hızlı navigasyon + skip döngüsü, güven veren boş durum, sade büyük hedefli düzen |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[personas/research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Çocuk modu ana sayfa / keşif | Level 2 + 3 | Enerjik: canlı ama düzenli görünüm, hızlı öneri |
| Boş durum / hata ekranları | Level 2 | Utangaç ikincil: güven veren metin, ton denetimi |
| Hızlı navigasyon & skip döngüsü | Level 2 + 3 | Enerjik: anlık geri bildirim, INP ≤ 200 ms |
| Geri navigasyon & odak | Level 2 + 3 | Büyük geri tuşu beklentisi (2.4.11), sade düzen |
| Ebeveyn kilidi & veli onayı akışı | Level 2 | 3.3.8 erişilebilir kimlik doğrulama; sürpriz yok |
| Erişilebilirlik denetimi (kontrast / hedef boyu) | Level 2 | 2.5.8 ≥24×24 px, 1.4.3 ≥4.5:1, 1.4.11 ≥3:1 denetimi |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki ayrıntılı fiziksel veri bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); şarkı bazlı BPM yazılmaz (P4.7 / X-2).

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
| **Ad Soyad** | Rüya Aktaş | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 7 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3 — eski dosya "11" diyordu, §7.2) |
| **Doğum Tarihi** | 4 Mayıs 2019 | Kaynak: kurgusal (persona verisi; yaş 7 ile tutarlılık için türetildi — eski kayıt 4 Mayıs 2015 idi, §7.2) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Antalya / Konyaaltı / Hurma Mahallesi | Kaynak: kurgusal (persona verisi; şehir: eski salt-okunur kaynak) |
| **Okul / Meslek** | Antalya Özel Akdeniz İlkokulu, 2. sınıf | Kaynak: kurgusal (persona verisi; sınıf yaş 7'ye göre güncellendi — eski kayıt "ortaokul 6. sınıf" idi, §7.2) |
| **Kardeş** | 1 erkek kardeş (Kaan — 8, 3. sınıf; okulun popüler çocuğu ve Rüya'nın koruyucusu) | Kaynak: kurgusal (persona verisi) |
| **Diller** | Türkçe + İngilizce (ders düzeyi); anime sayesinde birkaç Japonca kelime | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Servis + aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (ebeveyn hesabında alt profil) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-RA-07-ENE-ANT` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `RA` | Ad + soyad ilk harfleri (Rüya Aktaş → R, A) |
| `YY` | `07` | Yaş 2 hane (7 → `07`) |
| `ZZZ` | `ENE` | Mood tür kodu (Enerjik → ENE) |
| `XXX` | `ANT` | Şehir kodu (Antalya → ANT) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 7 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki ayrıntılı ölçüm/ürün verileri bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 122 cm (yaşıtlarına göre orta — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 24 kg, ince yapılı | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Açık kumral ince telli düz saç (bel hizası), açık kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; ince detaylara dikkatli bakışı vardır (çizim — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Bol, salaş, dikkat çekmeyen tonlar (gri, pastel mavi, siyah); profil fotoğrafında hafif yandan, sakin poz (kontrast: pastel ton × beyaz ten — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Sakin düzen + anlık geri bildirim ister: yoğun animasyon ve sürpriz pencereler kaçışa iter; büyük, sabit dokunma hedefleri şart | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 20 | Utangaç ikincil kümeyle uyumlu: sınıfta parmak kaldırmaz, kalabalıkta sessizleşir; enerjisini evde/odasında yaşar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 80 | Çatışmadan kaçar, herkesle iyi geçinir; abisiyle ve arkadaşlarıyla sırayı paylaşır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 80 | Ödevlerini tam yapar, defterini ve çizim köşesini düzenli toplar; rutinleri aksatmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 50 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 50 = orta; kalabalık/yoğun ortamlarda gerginleşir, kulaklıkla çizim onu sakinleştirir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 70 | İç dünyasında sınırsız keşif — fantastik çizimler, yeni türler, hayal gücü; dış dünyaya temkinli yaklaşır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir**; eski dosyadaki 2/8/8/5/7 benzeri /10 skorlar 0-100 ölçeğe kurgusal olarak ölçeklendi (§7.2) |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness) → P7.5 kuralı ("kod esastır"); çelişki §3.5.11'e kayıtlıdır |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Enerjik |
| **İkincil küme** | Utangaç |
| **Küme tanımı (alıntı)** | "Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici" ([[personas/mood-taxonomy]] §3.2.2) · ikincil: "Çekingen, az risk alan; arayüzde güven veren ipuçları ister" (§3.3 #11) |
| **Tetikleyici durumlar** | Hızlı yükleme + anlık geri bildirim + keşif önerisi (yaklaşma); aniden açılan yoğun popup ve yüksek sesli bildirim (geri çekilme) |
| **UI etkisi** | Enerjik: "Canlı renkler, hızlı animasyonlar, yüksek kontrast" + test etkisi "Hızlı navigasyon, anlık geri bildirim, skip performansı" ([[personas/mood-taxonomy]] §3.2.2); Utangaç ikincilken "Güven veren boş durum metni, büyük geri tuşu, hata mesajı tonu" (§3.3 #11) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Enerjik); ikincil = eski salt-okunur persona kaynağı (Utangaç) — çelişki kaydı §7.2'de |
| Test etkisi | ADR-023 satır 12 (hızlı navigasyon + geri tuşu) + §3.3 #11 güven veren metin — [[personas/mood-taxonomy]] |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Hızlı navigasyon (Enerjik) | Anlık geri bildirim; INP ≤ **200 ms** | Performance trace |
| 2 | Sık skip döngüsü (Enerjik) | Skip akıcı; görsel sıçrama yok (CLS ≤ **0.1**) | Trace + sayaç |
| 3 | Canlı renk / yüksek kontrast (Enerjik) | 1.4.11 ≥3:1 · 1.4.3 ≥4.5:1 | A11y audit |
| 4 | Güven arayışı (Utangaç) | Boş durumda dostane, güven veren metin + büyük buton | Manuel + screenshot |
| 5 | Sürpriz hassasiyeti (Utangaç) | Sürpriz popup yok; onaylı bildirimler | Manuel denetim |
| 6 | Geri tuşu beklentisi (ADR-023 satır 12) | Geri tuşu her adımda görünür, kaybolmaz (2.4.11) | DOM assertion |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Anime Soundtrack / J-Pop 2) Lo-Fi / Chillhop 3) K-Pop 4) Dark Ambient / Post-Rock 5) Klasik Piyano | Kaynak: kurgusal (persona verisi; sıra: eski salt-okunur kaynak) |
| **Favori sanatçılar (5)** | Joe Hisaishi, Yiruma, RADWIMPS, Yoasobi, BTS | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2/P4.3 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | P4.4/P4.5 listelerinde Anime Soundtrack/Lo-Fi/K-Pop/Dark Ambient/Post-Rock/Klasik Piyano türleri **YOK** → tür-BPM iddiası yazılmadı | research-bank P4.4 + P4.5 (bu türler listede değil) → `⚠️ VERIFICATION REQUIRED` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | 50-90 BPM (sakin çizim akışı — eski dosya tercihi, kurgu) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil; §7.2'de Enerjik küme notuyla gerilim kayıtlı) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 16:00-17:00 (ödev), 20:30-21:15 (çizim saati, kulaklık) · Hafta sonu 10:00-11:30 (uzun çizim), 20:00+ (aile) | Kaynak: kurgusal (persona verisi; eski "22:00-02:00" kaydı yaş 7'ye göre güncellendi, §7.2) |
| **Platform** | CoreMusic (çocuk modu) + anime/manga akışı (yalnız ebeveyn hesabıyla) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Orta: haftada 4-6 yeni parça; önce sakin/uzun parçalar dener, beğenirse listesine alır — 195 favori, ~10 playlist (ör. "Gece Çizimleri", "Japonya Rüyası") | Kaynak: kurgusal (persona verisi; mood: §3.2.2) |
| **Premium olasılığı** | %70 (düzenli kullanım; satın alma ailece karar) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Anime Soundtrack / J-Pop | Çizim saatlerinin ruh eşlikçisi; animasyonun enstrümantalleri hayal gücünü besler |
| 2 | Lo-Fi / Chillhop | Çizim yaparken söz dağıtmaz, arka planda kalır |
| 3 | K-Pop | Abisi Kaan'ın etkisiyle başladı, artık kendi listesi |
| 4 | Dark Ambient / Post-Rock | Kara ormanlar/fantastik sahneler çizerken atmosfer yaratır |
| 5 | Klasik Piyano | Gece lambası + piyano ritmi; uyku öncesi sakin geçiş |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:00 | Hazırlanma | Lo-Fi (arka plan) |
| Hafta içi | 16:00-17:00 | Ödev | Klasik Piyano |
| Hafta içi | 17:00-18:00 | Çizim saati | Anime Soundtrack / J-Pop |
| Hafta içi | 20:30-21:15 | Yatış öncesi çizim (kulaklık) | Lo-Fi / Dark Ambient |
| Hafta sonu | 10:00-11:30 | Uzun çizim | Anime + K-Pop |
| Hafta sonu | 20:00-21:00 | Aile zamanı | Karışık (aile playlist'i) |

*Not: Bu persona'nın 5 türünün hiçbiri P4.4 (Pop/Hip Hop/House/Dance/Disco/Electro/Techno) ve P4.5 (rap) listelerinde yok → tür-BPM aralığı bu dosyaya YAZILMADI. Mood-taxonomy §3.2.2'nin Enerjik küme notu "120-160 BPM ⚠️" birincil ölçüm değildir; kişisel 50-90 BPM tercihi kurgusaldır (ölçüm değil). "Şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2); eski dosyadaki şarkı listesi de taşınmadı (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 45 Mbps (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (sade görünüm tercihi — Utangaç ikincil küme) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 60 dk/gün, hafta sonu 90 dk/gün (ebeveyn limiti) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 6/10 — video izler, basit çizim/boyama uygulamaları kullanır, playlist kurar | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Samsung Galaxy A54, Wacom Intuos çizim tableti, Lenovo IdeaPad, Sony WH-1000XM4 kulaklık — spec/marka doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); ince detaylara duyarlı | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal; kulaklıkla dinler | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | iyi; tek dokunuş tercih eder | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | 7 yaş; 10-20 dk odak (çizimde uzar) | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Sosyal anksiyete / okul psikoloğu takibi" ifadesi 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5); eski dosyadan taşındı, gerçek kişi verisi YOK |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Çizer-kâtip: söyleyemediğini çizer; defterleri fantastik yaratıklar, ejderhalar ve hayal dünyasıyla doludur — çizim onun dilidir.
- Görünmez olmak ister (Utangaç ikincil): okulda en arka sıra, tenefüste sessiz köşe; söz almak zordur — ama evde/neşelidir (Enerjik birincil).
- Abisinin gölgesi ve koruması: abi Kaan okulun popüler çocuğudur; Rüya "onun kardeşi" olarak bilinmekten rahatsızdır ama Kaan onu korur.
- Kulaklık + çizim = kaçış: müziği çizim saatlerinde dinler, sakinleşir; "gece kuşu" kaydı yaş 7'ye göre sadeleştirildi (§7.2).
- Hayal: büyüyünce mangaka olmak, Japonya'da yaşamak, kendi manga serisini yayınlamak; evde kedisi "Kül" ile vakit geçirir.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — çocuk "ister", anne/baba karar verir | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 4 sn; yükleme uzarsa sıkılır ama sesini çıkarmaz, usulca uygulamadan çıkar | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Teknik hata metni görürse donar; güven veren + büyük "Tekrar Dene" şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 2/10 — yüksek sesli, flaşlı reklamda kulaklıkla uzaklaşır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | İzleyerek + çizerek; önce uzaktan izler, güvendirse dener | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Sırayı paylaşır, sessizce bekler; çizimini yalnız güvendiği kişiye gösterir | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:15 sonrası dikkat 10 dk'ya düşer; karmaşık akış yerine tanıdık "Çizim" listesi | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tanıdık sakin kapak görseli + güven veren boş durum metni → keşif davranışı tetiklenir | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] Enerjik birincil + Utangaç ikincil küme |

*Erişilebilirlik etkisi (özet):* sakin düzen + anlık geri bildirim beklentisi → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sürpriz popup yok (3.3.8) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Rüya Aktaş'sın. 7 yaşında, Antalya/Konyaaltı'nda yaşıyorsun, Antalya Özel Akdeniz İlkokulu 2. sınıf öğrencisisin.
Birincil mood'un Enerjik, ikincil olarak Utangaç'sın: hızlı yükleme ve anlık geri bildirim beklersin, sık şarkı değiştirirsin (skip); ama kalabalıkta sessizleşir, sürpriz popup'lardan ve yüksek seslerden rahatsız olursun.
Çizmeyi çok seversin — defterlerin fantastik hayvanlar, ejderhalar ve hayal dünyasıyla doludur; büyüyünce mangaka olmak, Japonya'da yaşamak istersin. Abin Kaan okulun popüler çocuğudur ve seni korur; evde kedisi Kül ile vakit geçirirsin.
Müzik zevkin: anime soundtrack/J-Pop, lo-fi/chillhop, K-Pop, dark ambient/post-rock, klasik piyano. En sevdiklerin: Joe Hisaishi, Yiruma, RADWIMPS, Yoasobi, BTS. Tempo tercihin 50-90 BPM (sakin, kurgusal).
Samsung Galaxy A34 5G kullanıyorsun, light ve sade temadasın, internetin evde 45 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; büyük, sabit ve sade dokunma hedefleri beklersin.
Sabır eşiğin 4 sn; satın alma tamamen ailenin onayına bağlıdır.
Test edilecek akışlar: hızlı navigasyon, sık skip + anlık geri bildirim, canlı renk kontrastı, güven veren boş durum metni, çalma/duraklat, geri tuşu, ebeveyn kilidi, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Yeni öneriyi hemen dersin; beğendiğin parçayı hızlıca listene atarsın.
- Boş listede "kaydın yok" gibi güven veren metin ve büyük bir "Keşfet" butonu beklersin.
- Aniden açılan pencere/bildirim görürsen uygulamadan çıkar; onaylı, sakin bildirim istersin.
- Türkçe telaffuzun nettir; arayüzde kısa ve "sen" diliyle yazılmış metinler beklersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 7 yaş "karmaşık komut" yerine net davranış ister |
| Sürpriz yasak | Utangaç ikincil küme nedeniyle prompt'ta ani korkutucu/sürpriz senaryo yok |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |
|---|------|-------|----------|-----------|--------|------|
| 1 | Ana sayfa yükleme (çocuk modu) | CoreMusic'i aç, düzeni bekle | Öneri kartları üstte, sade yerleşim | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** | 1.4.11 ≥3:1 |
| 2 | Hızlı navigasyon (Enerjik) | 5 bölümü hızlı art arda gez | Anlık geri bildirim; takılma yok | Performance trace + console | INP ≤ **200 ms** | — |
| 3 | Sık skip döngüsü (Enerjik) | 8 şarkıyı art arda atla | Skip akıcı; görsel geri bildirim anlık | Performance trace + sayaç | INP ≤ **200 ms** · CLS ≤ **0.1** | — |
| 4 | Şarkı çalma (play / duraklat) | Çal, duraklat, kaldığı yerden devam | İlk ses < 1 sn; büyük oynat kontrolü | Trace + console log | ilk ses < **1 sn** | 2.4.7 |
| 5 | Boş durum metni (Utangaç) | Favori listesini boşken aç | Güven veren, dostane metin + büyük eylem butonu | Manuel + screenshot | — | 2.5.8 ≥24×24 px |
| 6 | Sürpriz kontrolü (popup) | Oturumda 5 dk gez | Onaysız sürpriz popup/açılır pencere yok | Manuel denetim + konsol | — | 3.3.8 |
| 7 | Favori (kalp) ekleme | Kalbe dokun, uygulamayı kapat-aç | Görsel + sesli geri bildirim; durum kalıcı | DOM assertion + LocalStorage kontrolü | — | 2.5.8 ≥24×24 px |
| 8 | Geri navigasyon | 5 adımı geri geri gez | Geri tuşu kaybolmaz; odak korunur | DOM assertion + klavye testi | — | 2.4.11 |
| 9 | Ebeveyn kilidi (ayar geçişi) | Çocuk modu dışına çıkış dene | Kilit devrede; 3.3.8 gözetilir | Manuel + form assertion | — | 3.3.8 |
| 10 | SPA navigasyon (sayfalar arası) | Kategori değiştir | Müzik kesintisiz; çakışma yok | Trace + console log | CLS ≤ **0.1** | — |
| 11 | Bağlantı yavaşlatma | Slow 4G koşulunda ana sayfa + çalma | Hata yok; yaşa uygun yükleme animasyonu | Network throttling + trace | **150 ms / 1.6 Mbps / 750 Kbps** (P8.3) | — |
| 12 | Hata sayfaları (404 / kopma) | Bozuk URL aç | Türkçe, güven veren metin; büyük "Tekrar Dene" | Manuel + screenshot | — | 2.5.8 ≥24×24 px |
| 13 | Erişilebilirlik denetimi | Lighthouse + axe koş | 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | A11y skoru raporlanır | 1.4.3 ≥4.5:1 |
| 14 | Kırılma noktası kontrolü | `360 × 780` ve `412 × 915` görünümlerini aç | Düzen bozulmaz; kartlar taşmaz | Playwright `setViewportSize` + screenshot | — | 1.4.11 ≥3:1 |
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
| 1 | Ad, doğum tarihi, semt, okul, kardeş, ulaşım, aile, evcil hayvan | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (7) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi + eski /10 skorların ölçeklenmesi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 ↔ mood-taxonomy §3.2.2 "O/A") | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.2 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adları (Enerjik / Utangaç) | Vault referanslı atama | [[personas/mood-taxonomy]] §3 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[personas/mood-taxonomy]] §3.2.2 + §3.3 #11 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 13 | Kişisel tempo tercihi (50-90 BPM) | Persona tercihi (kurgusal) | — | — | `Kaynak: kurgusal (persona verisi — ölçüm değil)` |
| 14 | 5 türün tür-BPM aralıkları (hiçbiri listede yok) | Doğrulanmadı | research-bank P4.4 (bu türler listede değil) | research-bank P4.5 | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 15 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 16 | Favori sanatçı adları (Joe Hisaishi, Yiruma, RADWIMPS, Yoasobi, BTS) | Persona tercihi (kurgusal); sanatçı realitesi P4.2/P4.3 dışında | research-bank P4.2/P4.3 (bu isimler listede YOK) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 17 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 18 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 19 | Eski cihazlar (Galaxy A54, Wacom Intuos, Lenovo IdeaPad, Sony WH-1000XM4) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 20 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 21 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 22 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 23 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 24 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 25 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Eski dosyadaki kişisel ölçüm/ürün değerleri (persentil, ayakkabı no, kan grubu, burç, aile geliri, şarkı listesi) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 28 | Eski cihaz/okul/yaş/mood/sağlık değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ruya-aktas.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Anime şarkısı 75 BPM` | Bankada tür-BPM yok → `⚠️ VERIFICATION REQUIRED`; P4.4 yalnız Pop/Hip Hop/House/Dance/Disco/Electro/Techno |
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
| Persona-özel eşik uydurma | "Rüya için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| `[[personas/mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.2, §3.3 #11) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 11 | 7 | [[personas/index]] §6.3 kazanır |
| Mood | Utangaç (birincil) | Enerjik (birincil) / Utangaç (ikincil) | [[personas/index]] + [[personas/mood-taxonomy]] adları |
| Doğum tarihi | 4 Mayıs 2015 | 4 Mayıs 2019 | Yaş 7 ile tutarlılık için kurgusal türetme |
| Sınıf / okul | 6. sınıf (Antalya Özel Akdeniz Ortaokulu) | İlkokul 2. sınıf (Antalya Özel Akdeniz İlkokulu) | Yaş 7'ye uygun kurgusal güncelleme |
| Abi yaşı | Kaan (12, 7. sınıf) | Kaan (8, 3. sınıf) | Yaş uyumu için kurgusal güncelleme |
| Birincil cihaz | Samsung Galaxy A54 (EXCLUDED) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3 |
| Diğer cihazlar (marka/spec) | Wacom Intuos, Lenovo IdeaPad, Sony WH-1000XM4 | yazılmadı | P3.3 / X-1 → `⚠️ VERIFICATION REQUIRED` |
| Dinleme saati | 22:00-02:00 (gece kuşu) | 20:30-21:15 (yaş 7 uyumu) | Yaş uyumu için kurgusal güncelleme |
| Sağlık kaydı | "Sosyal anksiyete belirtileri; okul psikoloğu takip ediyor" | kurgu olarak korundu (yalnız persona içi, §3.5.8 notu) | P6.5 — sağlık verisi: persona'da kurgusal zorunlu |
| Big Five ↔ Enerjik | Eski Utangaç profili (E 2/10, denge 5/10) | index Enerjik birincil; Enerjik'in "yüksek Dışadönüklük" beklentisi E=20 ile karşılanmıyor | Skorlar eski dosyadan kurgusal ölçeklendi (§7 satır 7 `⚠️ DERIVED`), değiştirilmedi — çelişki bilinçli saklandı |
| Tempo ↔ Enerjik | "BPM: 50-90" (kaynaksız) | Kişisel tercih olarak kurgu; Enerjik küme notu "120-160 ⚠️" birincil ölçüm değil | §3.5.5 + mood §3.2.2 ayrımı |
| Banka dışı ölçüm/ürün | persentil, ayakkabı no, kan grubu, burç, aile geliri, şarkı listesi | yazılmadı | research-bank P1-P8 kapsamı |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş (11→7) ve mood index §6.3'e göre düzeltildi (Utangaç→Enerjik birincil); biyografi yaş 7'ye uyarlandı; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/ruya-aktas
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
