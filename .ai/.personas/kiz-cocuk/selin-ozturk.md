---
title: "CoreMusic — Persona: Selin Öztürk"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/selin-ozturk"
updated: 2026-09-26
group: kiz-cocuk
age: 10
mood: Yaratıcı
persona_id: CM-SO-10-YAR-ESK
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Selin Öztürk

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[personas/mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **kiz-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 10 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır — odağı görsel-müzik eşleşmesi + AudioVisualizer kalitesi, güvenilir sıralama/öncelik kontrolleri ve ebeveyn↔çocuk rol/izin akışıdır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[personas/mood-taxonomy]] (Yaratıcı / Lider), veri → [[personas/research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | kiz-cocuk (4-11) |
| Birincil mood | Yaratıcı ([[personas/index]] §6.3 ataması) |
| Test odağı | Görsel-müzik eşleşmesi + AudioVisualizer kalitesi, sıralama/öncelik kontrolleri, ebeveyn↔çocuk rol/izin akışı |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[personas/research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Çocuk modu ana sayfa / keşif | Level 2 + 3 | Yaratıcı: büyük albüm kapağı, görsel uyum |
| Görsel tasarım & AudioVisualizer | Level 2 + 3 | Yaratıcı: mood eşleşmesi, akıcı görselleştirici |
| Sıralama / önceliklendirme listeleri | Level 2 + 3 | Lider: kontrol hissi; dokunma sırası (2.5.7 / 2.5.8) |
| Ebeveyn kilidi & rol/izin akışı | Level 2 | Lider: ebeveyn ≠ çocuk; 3.3.8 erişilebilir kimlik doğrulama |
| Boş durum / hata ekranları | Level 2 | Net, suçlamayan metin + büyük eylem butonu |
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
| **Ad Soyad** | Selin Öztürk | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 10 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3 — eski dosya "9" diyordu, §7.2) |
| **Doğum Tarihi** | 17 Ağustos 2016 | Kaynak: kurgusal (persona verisi; yaş 10 ile tutarlılık için türetildi — eski kayıt 17 Ağustos 2017 idi, §7.2) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Eskişehir / Tepebaşı / Batıkent | Kaynak: kurgusal (persona verisi; şehir: eski salt-okunur kaynak) |
| **Okul / Meslek** | Eskişehir Özel Gelişim Koleji, 5. sınıf | Kaynak: kurgusal (persona verisi; sınıf yaş 10'a göre güncellendi — eski kayıt "4. sınıf" idi, §7.2) |
| **Kardeş** | Yok (tek çocuk) | Kaynak: kurgusal (persona verisi) |
| **Diller** | Türkçe + İngilizce (yaz okulu deneyimli; düzey kurgu) | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Servis + aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (ebeveyn hesabında alt profil; okulda "Başkan" lakabı) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-SO-10-YAR-ESK` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `SO` | Ad + soyad ilk harfleri (Selin Öztürk → S, O — Ö ASCII'ye sadeleştirildi) |
| `YY` | `10` | Yaş 2 hane (10 → `10`) |
| `ZZZ` | `YAR` | Mood tür kodu (Yaratıcı → YAR) |
| `XXX` | `ESK` | Şehir kodu (Eskişehir → ESK) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 10 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki ayrıntılı ölçüm/ürün verileri bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 138 cm (yaşıtlarına göre orta — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 32 kg, atletik yapılı (tenis — haftada 2 gün) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kumral düz saç (omuz hizası, bakımlı), kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; kendinden emin, odaklanmış bakış (konsey sunumları — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Düzenli, ciddi görünüm; blazer ceket ve beyaz gömlek favori; lacivert, bordo, krem palet (kontrast: lacivert × krem — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Düzenli, sıralı düzen ve büyük dokunma hedefleri ister: belirsiz sürükleme alanları ve kontrolsüz değişen listeler güvenini kırar | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 80 | Öğrenci konseyi başkanı, sunumcu ve organizatör; topluluk önünde söz almakta tereddüt etmez, grubu yönlendirir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 70 | Diplomatik: farklı karakterlerle çalışabilir, çatışmadan uzak durur ama kararlıdır — grubu sıraya koyabilir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 100 | Hiç ödev kaçırmaz, ajandasız adım atmaz; konsey notları ve ders programı eksiksizdir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 10 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 10 = güçlü duygusal denge; kriz ve sınav anında soğukkanlıdır, stres yönetimi gelişmiştir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 70 | Yeni fikirlere açık ama önce analiz eder — "atlamadan önce ölç" prensibi; deneysel olanı dener, kanıtlananı uygular. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir**; eski dosyadaki 8/7/10/9/7 benzeri /10 skorlar 0-100 ölçeğe kurgusal olarak ölçeklendi (§7.2) |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness) → P7.5 kuralı ("kod esastır"); çelişki §3.5.11'e kayıtlıdır |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Yaratıcı |
| **İkincil küme** | Lider |
| **Küme tanımı (alıntı)** | "Görsel-müzik eşleşmesi seven; resim/yazı gibi yaratıcı çalışırken uzun oturum kurar" ([[personas/mood-taxonomy]] §3.2.10) · ikincil: "Öncü, yönlendiren; listeleri ve sıralamayı kontrol eder" (§3.3 #12) |
| **Tetikleyici durumlar** | Net sıralama + görsel uyum + anlık geri bildirim (yaklaşma); belirsiz sürükleme alanları ve kontrolsüz değişen listeler (geri çekilme) |
| **UI etkisi** | Yaratıcı: "Görsel odaklı, albüm kapağı büyük, renk uyumu" + test etkisi "Görsel tasarım, mood eşleşmesi, AudioVisualizer kalitesi" ([[personas/mood-taxonomy]] §3.2.10); Lider ikincilken "Sıralama/önceliklendirme kontrolleri, rol/izin akışı (ebeveyn ≠ çocuk)" (§3.3 #12) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Yaratıcı); ikincil = eski salt-okunur persona kaynağı (Lider) — çelişki kaydı §7.2'de |
| Test etkisi | ADR-023 satır 12 (geri tuşu) + §3.3 #12 (sıralama kontrolü, rol/izin akışı) — [[personas/mood-taxonomy]] |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Görsel-müzik eşleşmesi (Yaratıcı) | Albüm kapağı büyük; renk uyumu; 1.4.11 ≥3:1 | Screenshot + a11y audit |
| 2 | Uzun oturum (Yaratıcı çalışma) | Akıcı arayüz; INP ≤ **200 ms** · CLS ≤ **0.1** | Performance trace |
| 3 | AudioVisualizer kalitesi (Yaratıcı) | Görselleştirici akıcı, görsel sıçrama yok | Trace + screenshot |
| 4 | Sıralama/öncelik kontrolü (Lider) | Büyük dokunma hedefli sıralama; sürüklemesiz de çalışır (2.5.7 / 2.5.8) | DOM assertion + a11y |
| 5 | Rol/izin akışı (Lider; ebeveyn ≠ çocuk) | Ebeveyn ayarları çocuk hesabında kilitli (3.3.8) | Manuel + form assertion |
| 6 | Geri tuşu beklentisi (ADR-023 satır 12) | Geri tuşu her adımda görünür, kaybolmaz (2.4.11) | DOM assertion |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Klasik Müzik 2) Caz 3) Motivasyonel / Epik Müzik 4) Türk Sanat Müziği 5) Akustik Pop | Kaynak: kurgusal (persona verisi; sıra: eski salt-okunur kaynak) |
| **Favori sanatçılar (5)** | Fazıl Say, Miles Davis, Sezen Aksu, Queen, Ludovico Einaudi | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2/P4.3 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | 5. tür "Akustik Pop" → Pop kümesi altında **Pop 80-120 BPM** (research-bank P4.4); ilk 4 tür (Klasik, Caz, Motivasyonel/Epik, Türk Sanat Müziği) P4.4/P4.5 listelerinde **YOK** → tür-BPM iddiası yazılmadı | `Kaynak: research-bank P4.4 (Pop 80-120) — VERIFIED (ikincil)` + dışı türler: `⚠️ VERIFICATION REQUIRED` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | 70-130 BPM, amaca göre (sunum öncesi hızlanır, ders öncesi yavaşlar — eski dosya tercihi, kurgu) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil; §7.2'de Yaratıcı küme notuyla gerilim kayıtlı) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 16:30-17:00 (ödev/analiz), 20:00-21:00 (konsey & sunum hazırlığı) · Hafta sonu 10:00-10:45 (tenis sonrası), 20:30-21:15 (aile) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (çocuk modu) + aile aboneliği (Premium: anne almış — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Düşük-orta: önce analiz eder, "atlamadan önce ölç" — haftada 2-3 yeni parça dener; 88 favori, 10 organize playlist (ör. "Odaklanma", "Sunum Öncesi", "Motivasyon") | Kaynak: kurgusal (persona verisi; mood: §3.2.10 + §3.3 #12) |
| **Premium olasılığı** | %70 (Premium zaten aktif; yenileme ailece karar) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Klasik Müzik | Mozart etkisine inanır; ders çalışırken zihnini toparlar |
| 2 | Caz | Sofistike bulur; babasıyla dinlediği ortak zaman |
| 3 | Motivasyonel / Epik Müzik | Sunum ve konuşma öncesi motivasyon kaynağı |
| 4 | Türk Sanat Müziği | Annesiyle kültürel bağ — evde birlikte dinlerler |
| 5 | Akustik Pop | Nadiren, eğlenmek ve sıralama listelerini renklendirmek için |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:00 | Hazırlanma | Klasik (arka plan) |
| Hafta içi | 16:30-17:00 | Ödev / analiz | Klasik + Caz |
| Hafta içi | 20:00-21:00 | Konsey & sunum hazırlığı | Motivasyonel / Epik |
| Hafta içi | 21:00-21:30 | Yatış öncesi | Türk Sanat Müziği (sakin) |
| Hafta sonu | 10:00-10:45 | Tenis sonrası | Motivasyonel / Epik + Akustik Pop |
| Hafta sonu | 20:30-21:15 | Aile zamanı | Karışık (aile playlist'i) |

*Not: Bu persona'nın 5 türünden yalnız "Akustik Pop" P4.4 (Pop 80-120) kümesi altında değerlendirildi ve `VERIFIED (ikincil)` etiketiyle yazıldı; Klasik/Caz/Motivasyonel-Epik/Türk Sanat Müziği P4.4 (Pop/Hip Hop/House/Dance/Disco/Electro/Techno) ve P4.5 (rap) listelerinde yok → tür-BPM aralıkları bu dosyaya YAZILMADI. Mood-taxonomy §3.2.10'un Yaratıcı küme notu "60-120 BPM ⚠️" birincil ölçüm değildir; kişisel 70-130 BPM tercihi kurgusaldır (ölçüm değil). "Şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2); eski dosyadaki şarkı listesi de taşınmadı (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 50 Mbps (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (düzenli, ciddi görünüm tercihi — Lider ikincil küme) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 75 dk/gün, hafta sonu 105 dk/gün (ebeveyn limiti) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 8/10 — sunum/uygulama hazırlar, e-posta ve tablo düzenler, playlist kurar (marka adları yazılmadı) | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | iPad Pro (M2), MacBook Air (M1), iPhone 13, Apple Watch SE — spec/marka doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); renk ve düzen hassasiyeti vardır | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal; ev ortamında hoparlörle de dinler | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | iyi (tenisçi); sıralama için dokunma tercih eder | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | 10 yaş; 15-30 dk odak (sunum hazırlığında uzar) | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Eski dosyada sağlık verisi **yok** → bu başlığa sağlık kaydı yazılmadı; ileride eklenirse 6698 m.6 kapsamında sayılır ve persona'da kurgusal olması zorunludur (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Öğrenci konseyi başkanı: seçim kampanyası yapmış, vaatlerini sıralamış, okulun en yüksek oyuyla seçilmiştir (%70 oy — kurgu); arkadaşları ona "Başkan" der.
- Müziği stratejik kullanır: ders/ödev öncesi odaklanma, sunum öncesi motivasyon — playlist'leri "Odaklanma", "Sunum Öncesi", "Motivasyon" başlıklarını taşır.
- Hayali: büyüyünce CEO veya siyasetçi olmak; "Türkiye'nin ilk kadın cumhurbaşkanı" lafını yarı şaka yarı ciddi kullanır.
- Zayıf yönler (Lider ikincil küme): fazla ciddi bulunur, hata kabul etmekte zorlanır, kontrolü bırakmaz — arayüz bunu suçlamayan mesajlarla karşılamalıdır.
- Tenis oynar (haftada 2 gün); zaman yönetimi ve ajanda onun yaşam ritmiidir.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — Premium zaten anne tarafından alınmış; yenileme ailece karar verilir | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 3 sn; beklemeyi "kayıp" sayar, ertelemek yerine alternatif ister | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Hata olduğunda suçlanmak istemez; hatayı gösteren, suçlamayan ve geri alınabilir adım sunan mesaj şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 4/10 — reklamı "zaman kaybı" olarak görür, atlamaya çalışır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Adım adım ve kontrol listesiyle; önce plan ister, sonra uygular | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Sıraya koyar, yönlendirir; kontrol onda değilse huzursuz olur (Lider ikincil) | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.3 #12 |
| **Yorgunluk etkisi** | 21:15 sonrası ajandası kapanır; karmaşık akış yerine tanıdık "Odaklanma" listesi | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Net sıralama + kontrol hissi + görsel uyum → keşif davranışı tetiklenir | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] Yaratıcı birincil + Lider ikincil küme |

*Erişilebilirlik etkisi (özet):* düzenli sıralı düzen + kontrol hissi beklentisi → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sürüklemesiz sıralama (2.5.7) ve ebeveyn↔çocuk rol ayrımı (3.3.8) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Selin Öztürk'sün. 10 yaşında, Eskişehir/Tepebaşı'nda yaşıyorsun, Eskişehir Özel Gelişim Koleji 5. sınıf öğrencisisin ve okul öğrenci konseyi başkanısın.
Birincil mood'un Yaratıcı, ikincil olarak Lider'sin: görsel-müzik eşleşmesini, büyük albüm kapaklarını ve renk uyumunu seversin, uzun yaratıcı oturumlarda sakinleşirsin; listeleri ve sıralamayı sen kontrol etmek istersin, ebeveyn ayarları ile çocuk ayarlarının ayrı olmasını beklersin.
Müziği stratejik kullanırsın: ders ve ödev öncesi odaklanma, sunum öncesi motivasyon. Ödevlerini hiç kaçırmaz, ajandasız adım atmaz; kriz anında soğukkanlısındır ama hatayı kabul etmek sende zordur.
Müzik zevkin: klasik müzik, caz, motivasyonel/epik müzik, Türk sanat müziği, akustik pop. En sevdiklerin: Fazıl Say, Miles Davis, Sezen Aksu, Queen, Ludovico Einaudi. Tempo tercihin 70-130 BPM, amaca göre (kurgusal).
Samsung Galaxy A34 5G kullanıyorsun, light temadasın, internetin evde 50 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; büyük, sabit dokunma hedefleri ve net sıralı düzen beklersin.
Sabır eşiğin 3 sn; satın alma tamamen ailenin onayına bağlıdır.
Test edilecek akışlar: görsel-müzik eşleşmesi, AudioVisualizer kalitesi, sıralama/öncelik kontrolü, çalma/duraklat, geri tuşu, ebeveyn kilidi & rol/izin akışı, boş durum, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Sıralamayı "yukarı taşı / aşağı taşı" büyük düğmelerle yaparsın; belirsiz sürükleme alanlarını sevmezsin.
- Boş listede ne yapacağını anlatan net, suçlamayan metin ve büyük bir "Sırala" / "Keşfet" butonu beklersin.
- Hata olduğunda seni suçlamayan, düzeltilebileceğini söyleyen mesaj istersin.
- Arayüzde "sen" dili, kısa ve adım adım yazılmış metinler beklersin; sunum gibi net akış seversin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 10 yaş "karmaşık komut" yerine net, adım adım davranış ister |
| Rol/izin notu | Lider ikincil küme nedeniyle prompt'ta ebeveyn ≠ çocuk ayrımı korunur; çocuk hesabında ebeveyn kontrolü gösterilmez |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |
|---|------|-------|----------|-----------|--------|------|
| 1 | Ana sayfa yükleme (çocuk modu) | CoreMusic'i aç, düzeni bekle | Öneri kartları üstte, büyük kapaklı, düzenli yerleşim | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** | 1.4.11 ≥3:1 |
| 2 | Görsel-müzik eşleşmesi (Yaratıcı) | Kapak görseli + tema rengini incele | Albüm kapağı büyük; renk uyumu; metin/grafik kontrastı | Screenshot + a11y audit | — | 1.4.11 ≥3:1 |
| 3 | AudioVisualizer kalitesi (Yaratıcı) | Çalarken görselleştiriciyi 30 sn izle | Animasyon akıcı; görsel sıçrama yok | Trace + screenshot | INP ≤ **200 ms** · CLS ≤ **0.1** | — |
| 4 | Sıralama & öncelik (Lider) | Playlist'te 3 parçayı sırala | Büyük hedeflerle sıralanır; sürüklemesiz de çalışır | DOM assertion + a11y | — | 2.5.7 · 2.5.8 ≥24×24 px |
| 5 | Rol/izin akışı (ebeveyn ≠ çocuk) | Çocuk hesabında ebeveyn ayarını dene | Kilit devrede; kontrol çocukta değil; 3.3.8 gözetilir | Manuel + form assertion | — | 3.3.8 |
| 6 | Şarkı çalma (play / duraklat) | Çal, duraklat, kaldığı yerden devam | İlk ses < 1 sn; büyük oynat kontrolü | Trace + console log | ilk ses < **1 sn** | 2.4.7 |
| 7 | Uzun oturum (15 dk çalışma akışı) | 15 dk kesintisiz oturum (çalma + gezinme) | Takılma yok; arayüz tepkisizleşmez | Performance trace + console | INP ≤ **200 ms** | — |
| 8 | Boş durum metni | Favori listesini boşken aç | Net, suçlamayan metin + büyük eylem butonu | Manuel + screenshot | — | 2.5.8 ≥24×24 px |
| 9 | Favori (kalp) ekleme | Kalbe dokun, uygulamayı kapat-aç | Görsel + sesli geri bildirim; durum kalıcı | DOM assertion + LocalStorage kontrolü | — | 2.5.8 ≥24×24 px |
| 10 | Geri navigasyon | 5 adımı geri geri gez | Geri tuşu kaybolmaz; odak korunur | DOM assertion + klavye testi | — | 2.4.11 |
| 11 | Ebeveyn kilidi (ayar geçişi) | Çocuk modu dışına çıkış dene | Kilit devrede; 3.3.8 gözetilir | Manuel + form assertion | — | 3.3.8 |
| 12 | SPA navigasyon (sayfalar arası) | Kategori değiştir | Müzik kesintisiz; çakışma yok | Trace + console log | CLS ≤ **0.1** | — |
| 13 | Bağlantı yavaşlatma | Slow 4G koşulunda ana sayfa + çalma | Hata yok; yaşa uygun yükleme animasyonu | Network throttling + trace | **150 ms / 1.6 Mbps / 750 Kbps** (P8.3) | — |
| 14 | Hata sayfaları (404 / kopma) | Bozuk URL aç | Türkçe, suçlamayan metin; büyük "Tekrar Dene" | Manuel + screenshot | — | 2.5.8 ≥24×24 px |
| 15 | Erişilebilirlik denetimi | Lighthouse + axe koş | 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | A11y skoru raporlanır | 1.4.3 ≥4.5:1 |
| 16 | Kırılma noktası kontrolü | `360 × 780` ve `412 × 915` görünümlerini aç | Düzen bozulmaz; kartlar taşmaz | Playwright `setViewportSize` + screenshot | — | 1.4.11 ≥3:1 |
| 17 | Türkçe karakter içeriği | ç/ğ/ı/İ/ö/ş/ü içeren menü ve şarkı adlarını aç | Karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | mojibake **0** | — |
| 18 | Odak sırası (klavye/gezinme) | Klavye ile 10 adımı gez | Odak mantıklı ve görünür; gizli odak yok | Klavye navigasyon testi + DOM assertion | — | 2.4.7 |

*Asgari 10 satır (şablon §3.5.10) — 18 satır yazıldı. Zorunlu sütunlar `Adım` · `Beklenen` · `Doğrulama` mevcut; görev gereği `Eylem` · `Metrik` · `WCAG` sütunları genişletilmiştir. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

**Ek doğrulama notları (test koşusundan önce — research-bank P8 `VERIFIED`):**

- **TBT:** > 50 ms ise uzun görev (long task) main thread'i bloklar; TBT, INP'nin laboratuvar proxy'sidir. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/optimize-vitals-lighthouse — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Test kırılım noktaları (P8.5 — 9 adet):** 320 × 568 · 375 × 667 · 375 × 812 · 414 × 896 · 768 × 1024 · 1280 × 720 · 1600 × 1200 · 1920 × 1080 · 2560 × 1440 — persona viewport'u (`360 × 780` / `412 × 915`, `⚠️ DERIVED` — §3) bu setle çapraz kontrol edilir. `Kaynak: [k1] + [k2]` (playwright.dev/docs/emulation + mintlify mobile-emulation — research-bank P8.5 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; Test Adımları tablosundaki eşikler research-bank P8'den alınır, persona'ya özel metrik hedefi bu dosyaya yazılmaz. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, kardeş, ulaşım, aile | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (10) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi + eski /10 skorların ölçeklenmesi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 ↔ mood-taxonomy §3.2.10 "A") | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.10 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adları (Yaratıcı / Lider) | Vault referanslı atama | [[personas/mood-taxonomy]] §3 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[personas/mood-taxonomy]] §3.2.10 + §3.3 #12 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 13 | Kişisel tempo tercihi (70-130 BPM) | Persona tercihi (kurgusal) | — | — | `Kaynak: kurgusal (persona verisi — ölçüm değil)` |
| 14 | "Akustik Pop" → Pop 80-120 BPM | Gerçek-dünya (tür-BPM) | research-bank P4.4 (Pop 80-120) | — | `Kaynak: research-bank P4.4 — VERIFIED (ikincil)` |
| 15 | P4.4/P4.5 dışı türlerin (Klasik, Caz, Motivasyonel/Epik, Türk Sanat Müziği) tür-BPM aralıkları + şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.4/P4.5 (bu türler listede değil) | research-bank P4.7 / X-2 | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 16 | Favori sanatçı adları (Fazıl Say, Miles Davis, Sezen Aksu, Queen, Ludovico Einaudi) | Persona tercihi (kurgusal); sanatçı realitesi P4.2/P4.3 dışında | research-bank P4.2/P4.3 (bu isimler listede YOK) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 17 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 18 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 19 | Eski cihazlar (iPad Pro M2, MacBook Air M1, iPhone 13, Apple Watch SE) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 20 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 21 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 22 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 23 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 24 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 25 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Eski dosyadaki kişisel ölçüm/ürün değerleri (persentil, ayakkabı no, kan grubu, burç, aile geliri, ev tipi, marka, şarkı listesi) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 28 | Eski cihaz/okul/yaş/mood/değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`selin-ozturk.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Klasik parça 75 BPM` | Bankada tür-BPM yok → `⚠️ VERIFICATION REQUIRED`; P4.4 yalnız Pop/Hip Hop/House/Dance/Disco/Electro/Techno |
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
| Persona-özel eşik uydurma | "Selin için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
- [ ] Test Adımları tablosu ≥ 10 satır; zorunlu sütunlar `Adım` · `Beklenen` · `Doğrulama` mevcut (bu dosya `# / Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG` genişletmiş biçimini kullanır)
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
| Test adımı | 18 (asgari 10) |
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
| `[[personas/mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.10, §3.3 #12) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 9 | 10 | [[personas/index]] §6.3 kazanır |
| Mood | Lider (birincil) | Yaratıcı (birincil) / Lider (ikincil) | [[personas/index]] + [[personas/mood-taxonomy]] adları |
| Doğum tarihi | 17 Ağustos 2017 | 17 Ağustos 2016 | Yaş 10 ile tutarlılık için kurgusal türetme |
| Sınıf / okul | 4. sınıf (Eskişehir Özel Gelişim Koleji) | 5. sınıf (Eskişehir Özel Gelişim Koleji) | Yaş 10'a uygun kurgusal güncelleme |
| Birincil cihaz | iPhone 13 / iPad Pro (M2) (EXCLUDED) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3 |
| Diğer cihazlar (marka/spec) | MacBook Air (M1), Apple Watch SE | yazılmadı | P3.3 / X-1 → `⚠️ VERIFICATION REQUIRED` |
| Big Five ↔ Yaratıcı | Eski Lider profili (E 8/10, denge 9/10) | index Yaratıcı birincil; Yaratıcı kümesi "orta-düşük E" beklentisi E=80 ile karşılanmıyor, "çok yüksek Açıklık" beklentisi O=70 ile yüksek ama 100 değil | Skorlar eski dosyadan kurgusal ölçeklendi (§7 satır 7 `⚠️ DERIVED`), değiştirilmedi — çelişki bilinçli saklandı |
| Tempo ↔ Yaratıcı | "BPM: 70-130, amaca göre" (kaynaksız) | Kişisel tercih olarak kurgu; Yaratıcı küme notu "60-120 ⚠️" birincil ölçüm değil | §3.5.5 + mood §3.2.10 ayrımı |
| Şarkı listesi | 5 şarkı ("We Are the Champions" vb.) | yazılmadı | P4.7 / EXCLUDED X-2 |
| Aile ayrıntıları | anne/baba meslekleri, gelir (100.000-130.000 TL), ev tipi (4+1) | meslekler kurgu taşındı; gelir ve ev tipi yazılmadı | Banka dışı veri → research-bank P1-P8 kapsamı |
| Banka dışı ölçüm/ürün | persentil, ayakkabı no, kan grubu, burç, aile geliri, marka, şarkı listesi | yazılmadı | research-bank P1-P8 kapsamı |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş (9→10) ve mood index §6.3'e göre düzeltildi (Lider→Yaratıcı birincil, Lider ikincil kaldı); biyografi yaş 10'a uyarlandı; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/selin-ozturk
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
