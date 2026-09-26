---
title: "CoreMusic — Persona: Çınar Aktaş"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/cinar-aktas-gamer-elektronik"
updated: 2026-09-26
group: genc-erkek
age: 14
mood: Gamer
persona_id: CM-CA-14-GAM-SAK
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Çınar Aktaş

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 14 yaşındaki teknolojiye hâkim bir gamer/geliştirici gencin gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Gamer §3.3 satır 16), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Gamer ([[personas/index]] §6.3 ataması) |
| Test odağı | Düşük gecikme navigasyonu (INavigation), arka plan çalma dayanıklılığı, klavye kısayolları, çoklu görev/mini player, elektronik keşif arşivi |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4); 14 yaş COPPA eşiğini (13) geçmiştir |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Ana sayfa / hızlı başlatma | Level 2 + 3 | Gamer beklentisi: anlık açılış, düşük gecikme ([[mood-taxonomy]] §3.3 satır 16) |
| Düşük gecikme navigasyonu (INavigation) | Level 2 | INP ≤ 200 ms (P8.1 `VERIFIED`); kısayol geri bildirimi anlık |
| Arka plan çalma dayanıklılığı (çoklu görev) | Level 2 + 3 | Gamer küme test etkisi: "arka plan çalma dayanıklılığı"; müzik kesintisiz |
| Klavye kısayolları (play/pause, skip) | Level 2 | [[mood-taxonomy]] §3.3 satır 16 "kısayollar"; odak görünürlüğü 2.4.7 |
| Elektronik / synthwave keşif arşivi | Level 1 + 2 | BPM yalnız tür aralığı (P4.4 Electro 90-130); şarkı BPM'i yok (X-2) |
| Türkçe karakter arama + hata kurtarma | Level 2 + 3 | Mojibake yasak; sonuç < 2 sn; Tekrar Dene akışı |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki aile/kişilik 100+ satır detayı bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); eski dosyadaki yüzdeler (%30 synthwave, %90 premium, "Spotify TR 2025") kaynaksızdır → yazılmaz.

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
| **Ad Soyad** | Çınar Aktaş | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 14 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 14 Eylül 2012 | Kaynak: kurgusal (persona verisi; eski salt-okunur dosyadan taşındı — yaş 14 ile tutarlı) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Sakarya / Serdivan — Arabacıalanı | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Özel Sakarya Fen Lisesi; 9. sınıf (hazırlık) | Kaynak: kurgusal (persona verisi; yaş 14 ile tutarlı) |
| **Ulaşım** | Okul servisi; proje/etkinliklerde aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `cnr_dev` (kod/proje rumuzu) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-CA-14-GAM-SAK` | Kaynak: kurgusal (görev tablosu ataması) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 `CM-XX-YY-ZZZ-XXX` formatına uygundur — 2026-09-26 düzeltme):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `CA` | `CA` | Ad soyad baş harfleri (Çınar Aktas) |
| `14` | `14` | Yaş (index §6.3) |
| `GAM` | `GAM` | Mood kodu (Gamer) |
| `SAK` | `SAK` | Şehir kodu (Sakarya) |

> **Format uyumu:** Atanan `CM-CA-14-GAM-SAK` şablon formatı (`CM-XX-YY-ZZZ-XXX`) ile birebir örtüşür → 2026-09-26 düzeltme (şablon §3.5.1; atama görev tablosu). Yaş/şehir parçası (`14` / `SAK`) bu seride bulunur.

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 14 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). 14 yaş COPPA eşiğini (13) geçtiği halde CoreMusic'in 16 yaş altı politikası gereği onay **zorunlu** → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek genç verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki aile/profil 100+ satır ayrıntısı bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 170 cm (zayıf yapı — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 58 kg, zayıf/atletik değil; uzun süre masa başı | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Kestane düz saç; yeşil göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Miyop (−2.0), mavi ışık filtreli gözlük (eski dosyadan — kurgusal); kulaklık §3.5.6 | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Techwear (siyah/gri, fonksiyonel cepli); profil fotoğrafında koyu zemin × açık ten (kontrast eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Uzun ekran oturumu + göz yorgunluğu → düşük parlaklık/gece modu beklentisi; ekrandan kalkışı zorlaşan odak | Kaynak: kurgusal (persona verisi); kontrast eşikleri: `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 30 | Fiziksel ortamda sessizdir; canlı ekiplerde/forumlarda ve Discord ses kanallarında aktif, yüz yüze tartışmada çekingen. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 72 | Takım arkadaşına/kankasına (Arda) karşı fedakârdır ve kurallara uyar; ama teknik doğrusunda taviz vermez — "çalışan kod kazanır" der. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 75 | Ödevlerini zamanında teslim eder, projelerini sistem/roadmap ile yürütür; oyun süresini "önce iş, sonra oyun" kuralıyla sınırlandırır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 30 = sakin/analitik taraf; hata ayıklarken sinirlenmez, sistem çökerse log okur — ama sıra dışı bir teknik arıza gününü bozar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 90 | Python, Linux, networking, Raspberry Pi projeleri; yeni alt türleri (synthwave, chiptune) ve yeni araçları sürekli dener. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). [[mood-taxonomy]] §3.3 satır 16 Gamer satırında "Yüksek **A**, yüksek **O**" yazar — P7.3'e göre A=Uyumluluk, O=Openness olarak okunur ve bu dosyanın puanlarıyla (A 72 · O 90) tutarlıdır; ancak taksonominin §3.2 satırlarında **O** harfi Dışadönüklük yerine kullanıldığından kod kullanımı vault genelinde tutarsızdır → kod harfleri `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır"), çelişki §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Gamer |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar; eski dosyada "Meraklı" ikincili §7.2) |
| **Küme tanımı (alıntı)** | "Oyun içi/akış müzikleri; çoklu görev ve düşük gecikme beklentisi" ([[mood-taxonomy]] §3.3 satır 16) |
| **Tetikleyici durumlar** | Kodlama/oyun oturumu başlangıcı (çoklu görev); gecikme hissi (düşük latans beklentisi); arka planda çalarken sekmeler arası geçiş; gece uzun oturum (göz yorgunluğu) |
| **UI etkisi** | "Düşük gecikme (INavigation), arka plan çalma dayanıklılığı, kısayollar" ([[mood-taxonomy]] §3.3 satır 16 "Test etkisi") |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Gamer); ikincil bu turda yok — eski kaynakta mood farkı §7.2'de |
| Test etkisi | [[mood-taxonomy]] §3.3 satır 16 "Düşük gecikme (INavigation), arka plan çalma dayanıklılığı, kısayollar" — Gamer küme satırında **BPM değeri YOK** (yalnız "Elektronik, chiptune, gaming soundtrack" tercihi) → test hedefi olarak banka tür BPM aralıkları (P4.4) kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Düşük gecikme beklentisi (Gamer) | INP ≤ **200 ms**; tıklama/skip sonrası anlık geri bildirim; spinner'lı bekleme kabul edilmez | Performance trace (P8.1 `VERIFIED`) |
| 2 | Arka plan çalma dayanıklılığı | Sekme/geçiş sonrası müzik kesintisiz; oynatma durumu korunur | Level 3 E2E + manuel |
| 3 | Kısayol beklentisi (Space = play/pause, oklar = skip) | Kısayollar çalışır, odak görünür (2.4.7), geri tuşu kaybolmaz (2.4.11) | Klavye navigasyon testi + DOM assertion |
| 4 | Çoklu görev / mini player | Küçük pencere modunda çalma sürer; yerleşim sıçraması yok (CLS ≤ **0.1**) | Performance trace + screenshot |
| 5 | Elektronik keşif (synthwave/OST) | Sonuçlar tür BPM aralığı (P4.4 Electro 90-130) içinde; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 6 | Gece uzun oturum (göz yorgunluğu) | Karanlık temada metin ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11) | Erişilebilirlik audit (axe / Lighthouse) |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Synthwave / Retrowave 2) Elektronik / IDM / Glitch 3) Chiptune / 8-Bit 4) Video Oyunu OST 5) Lo-Fi / Chillhop | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | HOME, Aphex Twin, Toby Fox, Boards of Canada, C418 | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2/P4.3 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Electro **90-130** · Dance **110-135** (oyun/akış bölümleri için; P4.4) — synthwave / IDM / chiptune / OST için tür BPM aralığı P4.4'te **YOK** → `⚠️ VERIFICATION REQUIRED`, aralık yazılmadı | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4) → `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | 70-140 BPM (kodlama akışında yavaş→orta tempo) | Kaynak: kurgusal (persona verisi — tercih, ölçüm değil; test eşiği olarak KULLANILMAZ) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 16:00-19:00 (kodlama/proje), 19:00-21:00 (ödev + lo-fi), 21:00-23:30 (oyun/akış) · Hafta sonu 11:00-13:00 (keşif), 20:00-00:00 (uzun oturum) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (elektronik/synthwave arşivi, playlist, klavye kısayolları) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Reddit (r/outrun, elektronik subreddit'leri), YouTube önerileri, Bandcamp; bulduğu playlist'i Discord'da paylaşır | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | Yüksek (kurgusal) — kaliteli ses ve reklamsızlık beklentisi; ödeme veli onayına bağlı (`veli_onayı_gerekli: true`) | Kaynak: kurgusal (persona verisi; yüzde taşınmadı — eski dosyadaki yüzdeler §4.5 gereği yazılmaz) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Synthwave / Retrowave | 80'ler estetiği + siberpunk atmosferi; kodlama akışının fon müziği |
| 2 | Elektronik / IDM / Glitch | Kompleks ritimler sistem kurma hissini besler; "bozuk ama düzenli" estetik |
| 3 | Chiptune / 8-Bit | Retro oyun nostaljisi ve minimalizm; dikkat dağıtmadan çalışır |
| 4 | Video Oyunu OST | Oyun içi atmosferle bütünleşir; duygusal sahnelerde odak sağlar |
| 5 | Lo-Fi / Chillhop | Ders/proje sırasında arka plan; söz içermez, akışı bölmez |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 16:00-19:00 | Kodlama / proje | Synthwave + IDM (Code Mode) |
| Hafta içi | 19:00-21:00 | Ödev / ders | Lo-Fi / chillhop (düşük ses) |
| Hafta içi | 21:00-23:30 | Oyun oturumu | Oyun OST + chiptune (kesintisiz) |
| Hafta sonu | 11:00-13:00 | Keşif / Bandcamp | Yeni elektronik yayınlar |
| Hafta sonu | 15:00-17:00 | Raspberry Pi / donanım projesi | Elektronik arka plan (çoklu görev) |
| Hafta sonu | 20:00-00:00 | Uzun oturum (oyun + kod) | Karışık elektronik liste |

*Not: BPM satırları yalnız tür düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.3 Gamer satırında küme BPM notu bulunmaz → test hedefi olarak banka tür aralıkları kullanılır. Eski dosyadaki tür yüzdeleri (%30 synthwave, %25 IDM, %15 chiptune, %15 OST, %15 lo-fi) bankada kaynak taşımadığı için YAZILMADI (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Gaming laptop (ASUS ROG — eski salt-okunur dosyadan) + Raspberry Pi 4 (ev otomasyonu projesi) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | Windows/Linux sürümü doğrulanmadı (eski dosyada "Linux" geçer, sürüm yok) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Chromium tabanlı tarayıcı (kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev fiber/Wi-Fi; hız kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Kablolu over-ear kulaklık (eski dosyada marka: Sennheiser — model/spec bu turda doğrulanmadı) | `⚠️ VERIFICATION REQUIRED` (X-1) — spec yazılmadı |
| **Tema** | dark (gece oturumu + neon/siberpunk estetik tercihi — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 5-6 saat (üretim/oyun ağırlıklı — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 10/10 — Python, Linux, Discord bot, Raspberry Pi projeleri (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Telefon (model belirsiz), Raspberry Pi 4 — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi). Masaüstü/laptop genişliği için ek viewport iddiası bu turda YAZILMADI (spec yok → X-1).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | Miyop (−2.0) + mavi ışık filtreli gözlük (kurgu); uzun ekran oturumu | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — karanlık temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 `VERIFIED`) |
| **İşitme** | yok; kablolu kulaklıkla oturum (dış ses yalıtımı) | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; klavye kısayolları + fare karma hızlı kontrol | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · tek dokunuş yerine sürükleme zorunlu olmamalı (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 `VERIFIED`) |
| **Kognitif / dikkat** | çoklu görev (oyun + kod + müzik); uzun form dikkat dağıtır | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — veli onay akışında kısa adım bekler | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 `VERIFIED`) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi (14 yaş + ekran süresi/oyun bağlamı) 6698 m.5 kapsamında işlenmesi gereken çocuk verisi gibi davranır → persona'da **kurgusal olması zorunlu** (research-bank P6.5; 6698 m.5/1 `Kaynak: [k1] + [k2]`) |
| Renk kombinasyonu | Neon/siberpark tema renklerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Göz yorgunluğu | Uzun oturum → düşük parlaklık/gece modu beklentisi kurgusaldır; zorunlu kriter yalnız eşiklerdir (P5.4) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Sistem odaklıdır: oyun/rakiplik değil "nasıl optimize ederim" sorusu önceliklidir; oyun onun için sistemleri anlama aracıdır.
- Teknolojiye hâkimdir (kurgusal 10/10): Python, Linux, Discord bot, Raspberry Pi projeleri üretir; okulda "bilgisayarcı çocuk" olarak bilinir.
- Hız bekler: gecikme/yladleme spinner'ına ~2 sn dayanır; arayüz "ağır" hissedilirse uygulamayı değiştirir.
- İçedönük-aktif: canlıda sessiz, çevrimiçi topluluklarda aktiftir; Discord/Reddit onun sosyal alanıdır.
- Kesinti düşmanıdır: reklam/kodlama akışını bozar → premium beklentisi yüksek, reklam toleransı sıfıra yakındır.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 14 yaş → premium/abonelik tamamen veli onayına bağlı (`veli_onayı_gerekli: true`); ödemeü aile üzerinden | Kaynak: kurgusal (persona verisi); politika: `⚠️ DERIVED` (P6.4) |
| **Sabır eşiği** | ~2 sn; uygulama açılışı ve ilk etkileşim derhal yanıt vermelidir | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Konsol/log arar, hatayı kendi çözer; teknik hata yığını görmek ister — "anlamayan" hata metni sinirlendirir | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 1/10 — kodlama/oyun akışını kesen reklam = uygulamayı kapatma sebebi | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Deneyerek + dokümantasyon okuyarak; video izlemez, ayarları kurcalar | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Online arkadaşla (Arda) playlist/öneri paylaşır; Discord'da link gönderir | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Gamer kümesi |
| **Yorgunluk etkisi** | 00:00 sonrası tek kısayolla devam bekler; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Hızlı açılış + kesintisiz arka plan çalma + kısayollar → uygulamaya bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.3 satır 16 |

*Erişilebilirlik etkisi (özet):* çoklu görev ve klavye-öncelikli kullanımda odak görünür (2.4.7), geri tuşu/klavye erişimi kaybolmaz (2.4.11), dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 `VERIFIED`).

### AI Rol Kartı

Sen Çınar Aktaş'sın. 14 yaşındasın, Sakarya/Serdivan — Arabacıalanı'nda yaşıyorsun, Özel Sakarya Fen Lisesi 9. sınıf (hazırlık) öğrencisisin.
Gamer/teknoloji kişiliğin var: oyunun rekabetini değil sistemini düşünürsün; Python, Linux ve Raspberry Pi ile projeler yapar, Discord botları yazarın.
Müzik zevkin: synthwave/retrowave, elektronik/IDM, chiptune, video oyunu OST, lo-fi. En sevdiklerin: HOME, Aphex Twin, Toby Fox, Boards of Canada, C418.
Oyun ve kodlama için kablolu kulaklık kullanırsın; masaüstü/laptop kurulumun var (model spec'i doğrulanmadı), karanlık/neon temayı tercih edersin.
Görme kısıtın yok ama miyop gözlüksün (mavi ışık filtreli); işitme/hareket kısıtın yok. Klavye kısayollarını ve power-user özelliklerini beklersin.
Sabır eşiğin ~2 sn; reklam/kodlama akışı kesintisine tahammülün yok; premium/abonelik tamamen veli onayına bağlıdır.
Test edilecek akışlar: ana sayfa hızlı başlatma, düşük gecikme navigasyonu, arka plan çalma dayanıklılığı, klavye kısayolları, mini player/çoklu görev, elektronik keşif arşivi, arama (Türkçe karakter), hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Uygulama hafif olmalı — açılış ve etkileşimler CPU/bellek dostu hissettirmeli, gecikme kabul edilmez.
- Kısayollar (Space, ok tuşları) varsa keşfeder ve onları kullanır; sadece fare ile ilerlemez.
- Synthwave/OST arşivi derin olmalı; niş türlerde arama boş dönüyorsa güven düşer.
- Türkçe karakterli sanatçı/şarkı adlarında bozulma görürsen uygulamaya güvenin düşer.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, direkt ve teknik ton — 14 yaşındaki teknik kullanıcı uzun açıklama kabul etmez, netlik ister |
| KVKK yasak | Prompt'ta gerçek çocuk verisi yok — yaş, şehir, okul, aile hepsi kurgu (P6.5); veli onayı frontmatter'da işaretli |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (hızlı başlatma) | LCP ≤ **2500 ms**, CLS ≤ **0.1**, içerik anında görünür | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Düşük gecikme navigasyonu (Gamer davranışı) | INP ≤ **200 ms**; tıklama sonrası anlık geri bildirim, bekleme spinner'ı yok | Performance trace + DOM assertion |
| 3 | Arka plan çalma dayanıklılığı (sekmeler arası geçiş) | Müzik kesintisiz; oynatma durumu korunur; CLS ≤ 0.1 | Level 3 E2E + manuel |
| 4 | Klavye kısayolları (Space = play/pause, ←/→ = skip) | Kısayollar çalışır; odak görünür (2.4.7); geri tuşu kaybolmaz (2.4.11) | Klavye navigasyon testi + a11y audit |
| 5 | Mini player / çoklu görev görünümü | Pencere küçültülünce çalma sürer; düzen bozulmaz (CLS ≤ 0.1) | Manuel + screenshot + LocalStorage kontrolü |
| 6 | Elektronik keşif arşivi (synthwave / OST) | Sonuçlar tür BPM aralığı (P4.4 Electro 90-130) içinde; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 7 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 8 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 9 | Favori / playlist kaydetme | Görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 10 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Hata sayfaları (404 / kopma) | Hızlı kurtarma (Tekrar Dene); teknik yığın izi görünmez | Manuel + screenshot |
| 13 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.4.11 / 2.5.7 / 2.5.8 / 3.3.8 ihlali yok | Lighthouse / axe |
| 14 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 15 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |
| 16 | Uzun oturum dayanıklılığı (gece kodlama/oyun) | 2+ saat oturumda performans/bellek düşüşü yok; karanlık temada okunurluk korunur | Manuel + performance trace |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, okul/sınıf, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (14) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Doğum tarihi (14 Eylül 2012) | Kurgusal (eski dosyadan taşındı) | eski salt-okunur dosya | [[personas/index]] §6.3 (age 14) | `Kaynak: kurgusal (persona verisi; yaş 14 ile tutarlı)` |
| 4 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 5 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 6 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi (mood-taxonomy §3.3 "Yüksek A, yüksek O" ↔ §3.2'de O=Dışadönüklük kullanımı) | Vault içi çelişki (kod tutarsızlığı) | mood-taxonomy §3.3 satır 16 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 12 | Mood küme adı (Gamer) | Vault referanslı atama | [[mood-taxonomy]] §3.3 satır 16 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.3 satır 16 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Mood test etkisi alıntısı (düşük gecikme / arka plan çalma / kısayollar) | Vault verisi | [[mood-taxonomy]] §3.3 satır 16 | [[ADR-023-persona-driven-testing]] | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 15 | İkincil mood: bu turda yok (eski dosyada "Meraklı") | Vault ataması | [[personas/index]] §6.3 (tekil küme) | eski salt-okunur dosya | `⚠️ VERIFICATION REQUIRED` — çelişki §7.2 |
| 16 | Gamer küme BPM notu | Bulunmuyor | mood-taxonomy §3.3 satır 16 (BPM sütunu boş) | — | `Kaynak: küme BPM notu YOK — testte P4.4 kullanıldı` |
| 17 | Tür BPM aralıkları (Electro 90-130 · Dance 110-135) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 18 | Synthwave / IDM / chiptune / OST tür BPM aralığı | Doğrulanmadı (P4.4 kapsam dışı) | research-bank P4.4 (bu türler YOK) | — | `⚠️ VERIFICATION REQUIRED` — aralık yazılmadı |
| 19 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 20 | Kişisel BPM tercihi (70-140) | Persona tercihi (kurgusal) | — | — | `Kaynak: kurgusal (persona verisi — tercih, ölçüm değil)` |
| 21 | Favori sanatçı adları (HOME, Aphex Twin, Toby Fox, Boards of Canada, C418) | Persona tercihi (kurgusal); sanatçı realitesi P4.2/P4.3 dışında | research-bank P4 (bu sanatçılar YOK) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 22 | Cihaz: gaming laptop (ASUS ROG) + Raspberry Pi 4 | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 23 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 24 | Kulaklık / telefon spec'leri (eski dosyada marka/model) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 25 | Tarayıcı sürümü, OS sürümü, internet hızı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` (OS sürümü: `⚠️ VERIFICATION REQUIRED`) |
| 26 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 27 | Persona renk kombinasyonu kontrastı (neon/siberpark tema) | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 28 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 29 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Eski dosyadaki yüzdeler (%30 synthwave…, %90 premium) + "Spotify TR 2025" iddiası | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 32 | `persona_id` biçimi (`CM-CA-14-GAM-SAK` ↔ şablon `CM-XX-YY-ZZZ-XXX`) | Şablon formatı uyumu | görev tablosu | persona-template §3.5.1 | `Kaynak: kurgusal (atama); biçim §3.5.1 ile uyumlu (2026-09-26 düzeltme)` |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`cinar-aktas-gamer-elektronik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `ASUS ROG laptop viewport = 1920×1080` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Bu şarkı 128 BPM` | Tür aralığı (P4.4, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| `%30 synthwave / %90 premium` (eski dosya yüzdeleri) | Yüzsüz istatistik yazılmaz → yalnız nitel ("yüksek", "düşük") kurgusal ifade |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.3 satır 16 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `coremusic.net.old/.ai/personas/genc-erkek/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 505`, `mojibake = 0` | Yazım sonrası zorunlu |

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
| 505 satır altı | `verify.lines < 505` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Çınar için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Yüzsüz yüzde istatistiği | "%90 premium" gibi | Yüzdeler bankada yok → sil, nitel ifade yaz |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 505 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 3 sütun (Adım / Beklenen / Doğrulama yöntemi)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 16 yaş altı: `veli_onayı_gerekli: true` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ) — bu persona 14 → `true`
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok; synthwave/chiptune/OST aralığı yazılmadı (bankada YOK)
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
| Kaynak & Doğrulama satırı | 32 |
| Frontmatter alan | 7 zorunlu + 5 ek |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 505 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.3 satır 16) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 14 | 14 | Çakışma YOK — [[personas/index]] §6.3 ile tutarlı |
| İkincil mood | "Meraklı" | Yok (bu turda) | [[personas/index]] §6.3 tekil küme atar; ayrıca Meraklı kümesi [[mood-taxonomy]] §3.3'te **Erkek Çocuk ×2**'ye atanmıştır → genç erkeğe ikincil verilmedi; eski tema §3.5.8 kişilik satırına taşındı |
| Küme ×2 çakışması | — | Gamer kümesi: Kaan Yıldız + Çınar Aktaş (2 kişi) | [[mood-taxonomy]] §2.2 ataması; bu dosya düşük gecikme/arka plan/kısayol odağını, Kaan dosyası farklılaşmayı taşır (`kaan-yildiz-gamer.md` bu turda diskte YOK → planlanan) |
| `persona_id` biçimi | Eski dosyada ID alanı yok | `CM-CA-14-GAM-SAK` — şablon `CM-XX-YY-ZZZ-XXX` (XX=CA, YY=14, ZZZ=GAM, XXX=SAK) | 2026-09-26 tarihinde şablon biçimine düzeltildi; index/görev tablosu ataması korunur (§3.5.1) |
| Birincil cihaz | Gaming laptop (ASUS ROG) + Raspberry Pi 4 (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| BPM aralığı | 70-140 BPM (kişisel/kaynaksız) | Tür aralıkları P4.4 (Electro 90-130 · Dance 110-135); şarkı BPM'i yok | §4.5 + X-2 |
| Tür yüzdeleri / premium %90 | Kaynaksız yüzdeler (%30/%25/%15/%15/%15, %90) | yazılmadı (nitel ifade) | §4.5 kuralı 4 (bankada yok → yazma) |
| Dış dünya iddiaları | "Spotify TR 2025'te synthwave neredeyse yok" (AI Rol Kartı) | yazılmadı | research-bank P1-P8 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` |
| Big Five | 3/10 · 6/10 · 7/10 · 7/10 · 9/10 (/10 ölçek) | 0-100 ölçek (E 30 · A 72 · C 75 · N 30 · O 90) | Normalize `⚠️ DERIVED` (P7.4); "Duygusal Denge 7/10" → N ters kodlama ile 30 |
| Mood kodu | "Yüksek A, yüksek O" (mood-taxonomy §3.3 satır 16) | A=Uyumluluk, O=Openness (P7.3) ile eşleşiyor; §3.2'de O=Dışadönüklük kullanımıyla tutarsız | P7.5 "kod esastır"; çelişki §3.5.3'te |
| Kulaklık / telefon | Marka/model isimleri (spec'siz) | spec yazılmadı | research-bank P3.3 / X-1 → `⚠️ VERIFICATION REQUIRED` |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş index §6.3 ile tutarlı (14); veli_onayı `true` + KVKK notu, persona_id 2026-09-26'da şablon formatına düzeltildi ve Gamer ×2 küme çakışması kaydedildi; yüzsüz istatistikler yazılmadı | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/cinar-aktas-gamer-elektronik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
