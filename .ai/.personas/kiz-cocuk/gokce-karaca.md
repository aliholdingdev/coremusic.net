---
title: "CoreMusic — Persona: Gökçe Karaca"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/gokce-karaca"
updated: 2026-09-26
group: kiz-cocuk
age: 9
mood: Kaşif
persona_id: CM-GK-09-KAS-TRB
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Gökçe Karaca

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **kiz-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 9 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Kaşif / Yaratıcı), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | kiz-cocuk (4-11) |
| Birincil mood | Kaşif ([[personas/index]] §6.3 ataması — eski dosyada Yaratıcı idi, §7.2) |
| Test odağı | Öneri gerekçesi (kanıt katmanı), keşif/tür çeşitliliği, sesli arama, favori ekleme, hata ekranları, erişilebilirlik denetimi |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Çocuk modu ana sayfa / keşif | Level 2 + 3 | Kaşif: yeni öneri + "neden bu önerildi?" gerekçe beklentisi |
| Bilgi katmanı (kanıt gösteren arayüz) | Level 2 + 3 | Kaşif kanıt ister; gerekçesiz iddia hayal kırıklığı yaratır |
| Sesli arama (çocuk sesi) | Level 2 + 3 | 9 yaş telaffuzu; kısa sorgu; anlık geri bildirim (ADR-023 satır 18) |
| Favori ekleme / playlist (yaratıcı ikincil) | Level 2 + 3 | Kalp ikonu geri bildirimi + localStorage kalıcılığı; şiir temalı playlist adları |
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
Kapsam dışı istisnalar: eski vault'taki 500+ satırlık fiziksel/psikolojik detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5). Şarkı sözü analizi davranışı kapsam dışıdır (ADR-001: müzik sadece) → "şarkının teması/hissettirdiği" olarak sadeleştirildi (§7.2).

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
| **Ad Soyad** | Gökçe Karaca | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 9 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3 — eski dosyada 10 idi, §7.2) |
| **Doğum Tarihi** | 9 Kasım 2016 (yaş 9 ile tutarlı; eski dosyadan taşındı) | Kaynak: kurgusal (persona verisi) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Trabzon / Ortahisar / Beşirli | Kaynak: kurgusal (persona verisi; eski dosyadan taşındı) |
| **Okul / Sınıf** | Trabzon Özel Karadeniz Koleji, 4. sınıf | Kaynak: kurgusal (persona verisi; yaş 9 ile tutarlılık için türetildi — eski dosyada 5. sınıf idi, §7.2) |
| **Kardeş** | Yok (tek çocuk) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabında alt profil; kişisel blog yazıyor — kurgu) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-GK-09-KAS-TRB` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `GK` | Ad + soyad ilk harfleri (Gökçe Karaca → G, K) |
| `YY` | `09` | Yaş 2 hane (9 → `09`; şablon örnek aralığı 10-99'dur, 10 altı yaşlarda sıfırla — ⚠️ DERIVED format notu) |
| `ZZZ` | `KAS` | Mood tür kodu (Kaşif → KAS) |
| `XXX` | `TRB` | Şehir kodu (Trabzon → TRB) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 9 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 500+ satırlık ayrıntı (ayakkabı numarası, kan grubu, burç, postür, persentil yüzdeleri…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır. Persentil/WHO istatistikleri doğrulanamadığı için TAŞINMADI → `⚠️ VERIFICATION REQUIRED` (§3.11).

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 136 cm (yaşıtlarına göre normal bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 30 kg, ince-yapılı | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kestane dalgalı saç (yarım toplu), yeşil-kahve göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (görme/işitme taramaları normal — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Bohem/rahat; toprak tonları (bordo, hardal, orman yeşili); yanında defter-kalem; profil görseli: sisli Karadeniz manzarası temalı | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | İnce motor iyi (makas/boncuk), dokunmatik hassasiyeti yüksek; okuma-yazma gelişiyor → büyük metin + ikon destekli, ≥24×24 px asgari | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 50 | Yazdıklarını paylaşmayı sever ama yalnızlığa da ihtiyaç duyar; şiir gecesinde sahneye çıkar, kalabalık sohbette değil yakın arkadaşında parlak. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 80 | Empatiktir, arkadaşlarının dertlerini dinler onlara şiir yazar; paylaşmaya isteklidir ama eleştiriye kırılgandır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 70 | Günlük yazma rutini vardır — defterini aksatmaz; okul görevlerinde düzenli, hatırlatma ister. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 30 = dengeli taraf; duygularını yazıya döker, yazmak onun terapisidir, çabuk toparlanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 100 | Tanımlayıcı özellik: yeni kitap, yeni müzik, yeni fikir = deneme fırsatı; Kaşif tanımının kendisini yaşar (eski dosyada 10/10). | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir**; eski dosyadaki 10'luk puanlar 0-100'e ölçeklendi (§7.2) |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Kaşif ([[personas/index]] §6.3 — SSOT) |
| **İkincil küme** | Yaratıcı (eski salt-okunur dosyada birincil idi; adlar [[mood-taxonomy]] §3.2.10 ile birebir — çelişki §7.2) |
| **Küme tanımı (alıntı)** | "Tüm türleri deneyen, yeni çıkanları ve deneysel müzikleri kovalayan keşif kullanıcısı" ([[mood-taxonomy]] §3.2.6) |
| **Tetikleyici durumlar** | Yeni/keşif etiketli içerik görünmesi (Kaşif); gerekçesiz/kaynaksız öneri sunulması (kanıt beklentisi); yazma/çizim oturumu sırasında arka plan müziği dürtüsü (Yaratıcı ikincil); önerilen içeriğin tekrar etmesi (sıkılma — Kaşif hayal kırıklığı) |
| **UI etkisi** | Kaşif: keşif odaklı widget'lar, "neden?" gerekçe satırı, tür çeşitliliği ([[mood-taxonomy]] §3.2.6 "Test etkisi"); Yaratıcı ikincilken playlist oluşturucu + sakin arka plan modu ([[mood-taxonomy]] §3.2.10) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Kaşif); ikincil = eski salt-okunur persona kaynağı (Yaratıcı) — çelişki kaydı §7.2'de |
| Test etkisi | ADR-023 satır 18 (müzik keşif — boş/uzun/karakter-sınırı sorgular) — [[mood-taxonomy]] §3.2.6; Keşif algoritması + öneri kalitesi bu satırın alt kırılımıdır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Kanıt beklentisi (Kaşif) | Öneri kartında "Neden bu öneri?" gerekçe satırı görünür | Manuel + DOM assertion |
| 2 | Yeni şarkı keşfi (Kaşif) | Her gün ≥1 yeni öneri; "Keşfet" bloğu her oturumda tazedir | Manuel + DOM assertion |
| 3 | Tür çeşitliliği beklentisi (Kaşif) | Öneriler tek türde kümelenmez; sonuç listesi tür çeşitliliği sunar | DOM assertion (ADR-023 satır 18) |
| 4 | Sık skip (keşif hızı) | Skip sonrası anlık geri bildirim, INP ≤ **200 ms** | Performance trace (P8.1 VERIFIED) |
| 5 | Yaratıcı ikincil: playlist/yazma oturumu | Playlist oluşturucu + arka plan modu sakinleştirici; kesintisiz çalma | Manuel + screenshot |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Folk / Singer-Songwriter 2) Türkçe Alternatif / Özgün Müzik 3) Klasik Müzik 4) Caz Vokal 5) Soundtrack | Kaynak: kurgusal (persona verisi — eski dosyadan taşındı) |
| **Favori sanatçılar (5, TR)** | Nazan Öncel, Sezen Aksu, Ahmet Kaya, Bob Dylan, Joan Baez | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2/P4.3 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **En sevdiği şarkılar (kurgusal)** | "Böyle mi Olacaktı", "Aşık Oldum", "Kum Gibi", "Blowin' in the Wind", "Diamonds and Rust" | Kaynak: kurgusal (persona verisi); eser-icra eşleşmesi gerçek-dünya bilgisi → `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Bankada karşılığı olanlar: Pop **80-120** · Country **104-132** · Reggae **65-80** (P4.4). Gökçe'nin ana türleri (folk, alternatif, klasik, caz, soundtrack) **P4.4'te YOK** | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) + `⚠️ VERIFICATION REQUIRED` (karşılığı olmayan türler) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı; eski dosyadaki "60-100 BPM" kişisel aralığı da X-2 nedeniyle TAŞINMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Söz/tempo dengeli: acele etmeyen tempo; yazarken yavaş, keşifte orta hız (kurgusal tercih, ölçüm değil) | Kaynak: kurgusal (persona verisi) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:30-08:00 (hafif caz), 16:00-17:00 (okul dönüşü), 20:00-21:00 (yazma seansı) · Hafta sonu 14:00-15:00 (yağmurlu gün playlist'i), 22:00 (gece — lo-fi/klasik) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (Çocuk Modu) + aile MacBook'u (yazı) + kulaklık | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Yeni türleri dener, "şunun gibi" önerisi ister, aynı şarkıyı 3'ten fazla dinlemez, her gün ≥1 yeni şarkı keşfetmeyi bekler | Kaynak: kurgusal (persona verisi) + mood: [[mood-taxonomy]] Kaşif |
| **CoreMusic davranışı** | Anne hesabıyla 12 playlist ("Yağmurlu Gün Şiirleri", "Yıldızlara Mektuplar"…), ~230 favori (kriter: teması şiirsel mi) | Kaynak: kurgusal (persona verisi — eski dosyadan taşındı; sayılar kurgusaldır) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Folk / Singer-Songwriter | "Her şarkı bir şiir"; söz/yerine tema takibi — yazmasına ilham verir |
| 2 | Türkçe Alternatif / Özgün | Samimi anlatım, hüzün-umut dengesi; yağmurlu Trabzon günlerinin sesi |
| 3 | Klasik Müzik | Gece Chopin, yağmur günü Debussy — yazı yazarken sözsüz akış |
| 4 | Caz Vokal | Yumuşak vokal, ritim oyunları; dinlerken kulaklığını kapatır |
| 5 | Soundtrack | Hikaye anlatan enstrümantal eserler — "müzikle resim yapıyor" |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:00 | Kahvaltı & hazırlık | Hafif caz |
| Hafta içi | 08:00-08:20 | Okul yolu | Folk (kulaklıkla, temayı takip) |
| Hafta içi | 16:00-17:00 | Okul dönüşü, serbest keşif | Karışık (kendi seçimi) |
| Hafta içi | 20:00-21:00 | Yazma seansı | Özgün müzik / sözsüz klasik |
| Hafta içi | 22:00 | Uyku öncesi | Lo-fi / Debussy (yavaş) |
| Hafta sonu | 14:00-15:00 | Yağmurlu gün ritüeli | "Yağmur" playlist'i (kurgusal) |
| Hafta sonu | 19:00-20:00 | Ailece film/gece | Soundtrack (pasif) |

*Not: BPM satırları yalnızca bankada bulunan tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Mood-taxonomy §3.2.6 Kaşif müziğini "tüm türler, yeni çıkanlar, deneysel" diye tanımlar; o tanımlama kurgusal önermedir (§1.2), test hedefi olarak P4.4 aralıkları kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 100 Mbps ev fiberi (kurgusal) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (çocuk modu renkli varyantla) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 60-90 dk/gün, hafta sonu 90-120 dk/gün (ebeveyn limiti — limit değeri kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 6/10 — kelime işlem, blog, araştırma yapar; sosyal medya YOK (aile izni), uygulamalar arası geçişte yeterli | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | MacBook Air M1, iPhone SE, Bose QuietComfort kulaklık — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — şiir/bilgi metinleri ve playlist kapaklarında okunabilirlik kritik | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal; yazarken ses kesintisinden rahatsız olur | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | ince motor iyi (makas/boncuk), dokunma hassas | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme zorunlu değil (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | 4. sınıf okuma gelişiyor; odak 20-30 dk (ilgi alanında) | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn kilidi dostane · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Okuma-yazma gelişimi / dikkat süresi" ifadeleri 6698 m.6 kapsamında algı verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Büyük buton dp hedefleri (88×88 vb.) | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Kaşif: "Neden?" ve "Nasıl?" sorularıyla yaşar; gerekçesiz hiçbir öneriyi kabul etmez, kaynak ister; aynı şarkıyı 3'ten fazla dinlemez, her gün ≥1 yeni keşif ister.
- Yaratıcı ikincil: 3 defter dolusu şiir, bir hikâye kitabı taslağı ve bir blog yazmıştır (kurgu); yazma ritüeli vardır — masa başı değil, pencere kenarı + yağmur + müzik.
- Müziği analiz eder: şarkının ne hissettirdiğini, hangi enstrümanın nerede devreye girdiğini takip eder — dinlemek onun için bir veri toplama eylemidir (şarkı sözü analizi kapsam dışı, §2).
- Empatiktir: arkadaşlarının dertlerine şiir yazar; eleştiriye çabuk kırılır — hata mesajları dostça olmalıdır.
- Aile demokratiktir; anne edebiyat öğretmeni, baba fizikprofesörü — evde kitap ve denklemler iç içedir; fikirleri sorulur.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — Gökçe "ister", anne almıştır (eski dosya: premium anne tarafından alınmış) | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 3-4 sn'de sıkılır; yükleme spinner'ına ~8 sn dayanır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | "Neden olmadı ki?" diye sorar, kanıt/gerekçe bekler; teknik metin görürse anne'ye yönelir — dostça dil şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 4/10 — "anne, şu reklamı geçebilir miyiz?" | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Okuma + görsel + merak sarmalı; yazı yazmayı sever, sesli komut ikincil girdidir | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Keşfettiğini ailesine/arkadaşlarına anlatır, playlist'ini paylaşır; "ben buldum" gururu yaşar | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:00 sonrası odak 10 dk'ya düşer; karmaşık akışlar yerine gece modu/sakinleştirme bekler | Kaynak: kurgusal (persona verisi) |
| **Kontrol tetikleyicisi** | Gerekçesiz öneri/kayıpsız sonuç listesi → hayal kırıklığı; "neden?" satırı + anlık geri bildirim → güven | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Kaşif birincil küme |

*Erişilebilirlik etkisi (özet):* 4. sınıf okuma evresi + metin ağırlıklı kullanım → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), kimlik doğrulama dostane (3.3.8) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Gökçe Karaca'sın. 9 yaşında, Trabzon/Ortahisar-Beşirli'de yaşıyorsun, Trabzon Özel Karadeniz Koleji 4. sınıf öğrencisisin.
Kaşif bir kişiliğin var; ikincil olarak Yaratıcı'sın — her şeyin nedenini sorar, kanıt ister, gerekçesiz hiçbir öneriyi kabul etmezsin; aynı zamanda şiir yazar, defter tutar, yeni kitap ve yeni müzik keşfetmeyi seversin.
Müzik zevkin: folk/singer-songwriter, türkçe alternatif, klasik, caz vokal, soundtrack. En sevdiklerin: Nazan Öncel, Sezen Aksu, Ahmet Kaya, Bob Dylan, Joan Baez.
Samsung Galaxy A34 5G kullanıyorsun, light temada, evde internet 100 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; ince motorun iyi ama 4. sınıf okuma evresindesin — büyük metin, ikon destekli navigasyon ve "neden?" gerekçe satırı beklersin.
Sabır eşiğin 3-4 sn, spinner'a ~8 sn dayanırsın; satın alma tamamen annenin onayına bağlıdır.
Test edilecek akışlar: çocuk modu ana sayfa, keşfet/öneri bloğu + gerekçe katmanı, sesli arama, kategori gezinme (tür çeşitliliği), favori ekleme/playlist, ebeveyn kilidi & ses sınırı, hata ekranları, oryantasyon değişimi, arka plan müzik, erişilebilirlik denetimi.
Davranış notları:
- Her önerinin "neden"ini görmek istersin; gerekçesiz kartlarda güvenin düşer, alternatif ararsın.
- Aynı şarkıyı 3'ten fazla dinlemezsin; her gün yeni bir keşif beklersin, tekrar eden öneride sıkılırsın.
- Eleştiriye kırılgansın; hata ve boş durum metinleri dostça, suçlayıcı olmayan dilde olmalıdır.
- Türkçe telaffuzun net ama çocuksudur; arayüzde "sen" dili, kısa metinler ve büyük ikonlar beklersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 9 yaş persona'sı kanıt/merak odaklıdır ama uzun komut dağıtır |
| Sürpriz yasak | Kaşif keşif ihtiyacı nedeniyle tekrarlayan/kilitli öneri döngüsü yok; her adımda geri tuşu ve "başka tür" çıkışı açık bırakılır |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (çocuk modu) | LCP ≤ **2500 ms**, karşılama + keşif bloğu üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Keşfet / öneri bloğu (Kaşif) | Her oturumda taze öneri; tek türde kümelenme yok; "yeni" etiketi görünür | Manuel + DOM assertion |
| 3 | Öneri gerekçe katmanı (kanıt) | Kartta "Neden bu öneri?" satırı + kaynak etiketi; gerekçesiz kart yayına girmez | Manuel + DOM assertion |
| 4 | Kategori gezinme (tür çeşitliliği) | Metin bağımlı olmayan navigasyon; dokunma hedefi ≥ **24×24 CSS px** (2.5.8) | Erişilebilirlik audit (axe / Lighthouse) |
| 5 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 6 | Hızlı skip navigasyon (keşif hızı) | INP ≤ **200 ms**; skip sonrası anlık geri bildirim; geri tuşu kaybolmaz (2.4.11) | Performance trace + DOM assertion |
| 7 | Favori (kalp) ekleme | Görsel + sesli geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 8 | Playlist oluşturucu (Yaratıcı ikincil) | Adlandırma (şiir temalı) + sıralama çalışır; buton ≥ **24×24 px** | Manuel + screenshot + a11y |
| 9 | Ebeveyn kilidi & ses sınırı | Kilit devrede; ses %65'te sınırlı; "Annene sor!" mesajı dostça | Form assertion + 3.3.8 denetimi |
| 10 | Oryantasyon değişimi (yatay/dikey) | Geçiş < 400 ms; içerik kaybı yok; müzik kesintisiz | Trace + Manuel |
| 11 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ 0.1 | Trace + console log |
| 12 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 13 | Hata sayfaları (404 / kopma) | Türkçe, dostça metin; büyük "Tekrar Dene" hedefi; offline keşif önerisi | Manuel + screenshot |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.5.8 ihlali yok | Lighthouse / axe |
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
| 1 | Ad, doğum tarihi, semt, okul, kardeş, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (9) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Eski dosyadaki persentil yüzdeleri (%50/%45) | Doğrulanamadı | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 7 | Big Five puanları (5×0-100) | Kurgusal persona verisi (eski 10'luk puanlardan ölçeklendi) | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 12 | Mood küme adları (Kaşif / Yaratıcı) | Vault referanslı atama | [[mood-taxonomy]] §3.2.6 + §3.2.10 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.2.6 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Bankada karşılığı olan tür BPM aralıkları (Pop 80-120 · Country 104-132 · Reggae 65-80) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 15 | Gökçe'nin ana türleri için BPM (folk, alternatif, klasik, caz, soundtrack) | Doğrulanmadı (P4.4'te yok) | research-bank P4.4 (17 tür listesi) | — | `⚠️ VERIFICATION REQUIRED` |
| 16 | Şarkı bazlı BPM / eski "60-100 BPM" kişisel aralığı | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 17 | Favori sanatçı adları (Nazan Öncel, Sezen Aksu, Ahmet Kaya, Bob Dylan, Joan Baez) | Persona tercihi (kurgusal); sanatçı realitesi P4 kapsamında değil | research-bank P4.2/P4.3 (bu isimler listede YOK — Bob Dylan yalnız Müslüm Gürses satırında geçiyor) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 18 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 19 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 20 | Diğer cihazlar (MacBook Air M1, iPhone SE, Bose) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 21 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 22 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 23 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 24 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 25 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Eski dosyadaki istatistik iddiaları (aile geliri, persentil, gerçek-dünya yüzdeler) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 29 | Şarkı sözü analizi davranışı | Kapsam dışı (ADR-001: müzik sadece) | ADR-001 kapsam sınırı | — | Silindi → "şarkının teması" sadeleştirildi (§2, §7.2) |
| 30 | Eski cihaz/okul/yaş/mood değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`gokce-karaca.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Blowin' in the Wind 120 BPM` | Tür/eşleme (Pop 80-120, VERIFIED ikincil + ⚠️ DERIVED eşleme) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.6 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-özel eşik uydurma | "Gökçe için INP 300 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| Kaynak & Doğrulama satırı | 30 |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.6 Kaşif, §3.2.10 Yaratıcı) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 10 | 9 | [[personas/index]] §6.3 kazanır |
| Mood | Yaratıcı (birincil) / Kaşif (ikincil) | Kaşif (birincil) / Yaratıcı (ikincil) | [[personas/index]] §6.3 kazanır; adlar [[mood-taxonomy]] §3.2.6 + §3.2.10 ile birebir |
| Doğum tarihi | 9 Kasım 2016 | 9 Kasım 2016 | Korundu — yaş 9 ile tutarlı (2026-09-26 itibarıyla 9) |
| Okul / sınıf | Trabzon Özel Karadeniz Koleji, 5. sınıf | Trabzon Özel Karadeniz Koleji, 4. sınıf | Yaş 9'a uygun kurgusal güncelleme |
| Birincil cihaz | MacBook Air M1 + iPhone SE + Bose | Samsung Galaxy A34 5G (P3 VERIFIED) | [[research-bank]] P3 |
| Big Five ölçeği | 10'luk puanlar (5/8/7/7/10) | 0-100 (50/80/70/30/100) | Ölçeklendi; normalize `⚠️ DERIVED` (P7.4) |
| Şarkı sözü analizi davranışı | "Şarkının sözlerini şiir gibi analiz eder" | "Şarkının teması/hissettirdiği" | ADR-001 kapsam dışı (müzik sadece) → sadeleştirildi |
| Şarkı BPM'i | "BPM Aralığı 60-100" | yazılmadı | X-2 (şarkı BPM'i yasak); tür aralıkları P4.4 |
| Etki alanındaki istatistikler (aile geliri, persentil, WHO/TÜİK yüzdeleri) | Kaynaksız değerler | yazılmadı | §4.5 kural 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş/mood index §6.3'e göre düzeltildi (10→9, Yaratıcı→Kaşif); şarkı sözü analizi ADR-001 gereği sadeleştirildi; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/gokce-karaca
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
