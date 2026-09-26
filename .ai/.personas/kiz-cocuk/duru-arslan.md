---
title: "CoreMusic — Persona: Duru Arslan"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/duru-arslan"
updated: 2026-09-26
group: kiz-cocuk
age: 11
mood: Enerjik
persona_id: CM-DA-11-ENE-KYS
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Duru Arslan

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **kiz-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 11 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Enerjik / Yaratıcı), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | kiz-cocuk (4-11) |
| Birincil mood | Enerjik ([[personas/index]] §6.3 ataması) |
| Test odağı | Hızlı navigasyon, anlık geri bildirim, skip performansı (ADR-023 satır 12); ikincil Yaratıcı ile görsel-mood eşleşmesi, playlist/koleksiyon yönetimi |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Çocuk modu ana sayfa / keşif | Level 2 + 3 | Enerjik: hızlı gezinme, Yaratıcı: kapak/renk odaklı |
| Hızlı navigasyon & skip performansı | Level 2 + 3 | §3.2.2 test etkisi: anlık geri bildirim, INP ≤ 200 ms |
| Playlist / koleksiyon yönetimi | Level 2 + 3 | 8+ playlist, sürükleme zorunlu değil (2.5.7) |
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
| **Ad Soyad** | Duru Arslan | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 11 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 11 Şubat 2015 | Kaynak: kurgusal (persona verisi; yaş 11 ile tutarlılık için türetildi — eski kaynakta 2018, §7.2) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Kayseri / Melikgazi / Gesi Mahallesi | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Kayseri İstiklal Ortaokulu, 6. sınıf | Kaynak: kurgusal (persona verisi; yaş 11 ile tutarlılık için türetildi — eski kaynakta İlkokulu 3. sınıf, §7.2) |
| **Ulaşım** | Yürüyerek / aile aracı (okul ~2 km — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabında alt profil) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-DA-11-ENE-KYS` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `DA` | Ad + soyad ilk harfleri (Duru Arslan → D, A) |
| `YY` | `11` | Yaş 2 hane (11 → `11`) |
| `ZZZ` | `ENE` | Mood tür kodu (Enerjik → ENE) |
| `XXX` | `KYS` | Şehir kodu (Kayseri → KYS; Türkçe karakter katlamı: bu kodda Türkçe karakter yok) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 11 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 500+ satırlık ayrıntı (ayakkabı numarası, kan grubu, burç, aile geliri…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 152 cm (yaşıtlarına göre ortalama-üstü bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 42 kg, ince/uzun (dans & yürüyüş — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Açık kumral omuz hizası saç, açık kahverengi göz (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (görme/işitme muayeneleri normal — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Kendi tasarladığı asimetrik renkli parçalar; favori renkler eflatun/mint/somon/fildişi (profil görseli bu tonlarda — kontrast eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | El becerisi çok iyi (makas/dikiş → ince motor ileri); ekranda hassas + hızlı tıklar → hedefler büyük, geri tuşu hızlı erişilebilir olmalı | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) + 2.5.7 (sürükleme) kriterleri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 60 | Fikirlerini paylaşır ama spot ışığı şart değil; tasarımları "onun adına konuşur", ablasıyla atışır ama sahne onu yorar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 78 | İşbirlikçidir; annesinin atölyesinde yardım eder, müşterilere kumaş önerir, paylaşımcıdır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 68 | Proje/malzemede disiplinli (fırça yıkar, kumaş düzenler); oda toplama ve ödevde aynı disiplin yoktur. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 40 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 40 = orta; yaratıcı süreçte iniş-çıkışlar, beğenmediği tasarımı yırtıp yeniden başlar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 95 | Yeni kumaş, yeni stil, yeni müzik keşfetmeden duramaz; "her müziğin bir kumaş dokusu vardır" diye düşünür. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Mood eğilimi çelişkisi | [[mood-taxonomy]] §3.2.2 Enerjik eğilimi "Yüksek Dışadönüklük" der (kurgusal eğilim); bu persona E=60 (orta) → persona puanı kurgusal kalır, çelişki §7.2'ye kayıtlıdır → `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Mood-taksonomi §3.2.2 "Yüksek Dışadönüklük (O), yüksek Deneyime Açıklık (A)" yazar — bu harf kullanımları P7.3'e TERS'tir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Enerjik |
| **İkincil küme** | Yaratıcı |
| **Küme tanımı (alıntı)** | "Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici" ([[mood-taxonomy]] §3.2.2) |
| **Tetikleyici durumlar** | Yeni keşif/liste görünümü, sabah/aktif dinleme anı (Enerjik); tasarım/yaratıcı çalışma anı (Yaratıcı ikincil tetikleyici); yavaş akış/sıkı bekleme (sıkılma — Enerjik kaçışı) |
| **UI etkisi** | Enerjik: "Canlı renkler, hızlı animasyonlar, yüksek kontrast"; test etkisi "Hızlı navigasyon, anlık geri bildirim, skip performansı" + ADR-023 satır 12 (hızlı navigasyon + geri tuşu) ([[mood-taxonomy]] §3.2.2); Yaratıcı ikincilken görsel odaklı, büyük kapak, renk uyumu + AudioVisualizer kalitesi ([[mood-taxonomy]] §3.2.10) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Enerjik); ikincil = eski salt-okunur persona kaynağı (Yaratıcı) — çelişki kaydı §7.2'de |
| Test etkisi | ADR-023 satır 9 (kız çocuk grubu temsilcisi: içerik filtresi + ebeveyn kontrolü) — [[mood-taxonomy]] §3.6; Enerjik'e özgü hızlı navigasyon testleri §3.2.2 "Test etkisi" + ADR-023 satır 12'dir |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Sık skip (Enerjik) | Skip sonrası anlık geri bildirim; INP ≤ **200 ms** | Performance trace (P8.1 VERIFIED) |
| 2 | Hızlı navigasyon (Enerjik — ADR-023 satır 12) | Sekmeler arası hızlı geçiş; geri tuşu her adımda görünür (2.4.11) | DOM assertion + trace |
| 3 | Yeni şarkı keşfi + playlist (Enerjik) | Keşif akışı tek dokunuş; playlist'e ekleme hızlı + kalıcı | DOM assertion + LocalStorage |
| 4 | Görsel-mood eşleşmesi (Yaratıcı) | Albüm kapağı büyük, renk uyumu; CLS ≤ **0.1** | Performance trace + screenshot |
| 5 | Yavaşlık/sıkılma (Enerjik kaçışı) | LCP ≤ **2500 ms**; spinner dostça, çıkış açık | Performance trace + screenshot |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Caz / Bossa Nova 2) Fransız Chanson 3) Elektronik / Lounge 4) Klasik müzik 5) Türk sanat müziği | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Edith Piaf, Zaz, Frank Sinatra, Fazıl Say, Mercan Dede | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2/P4.3 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **Yabancı favoriler (kurgusal)** | Edith Piaf, Zaz, Frank Sinatra, Stan Getz — defile/atölye repertuvarı (aynı isimler hem TR hem yabancı listesinde persona tercihidir) | Kaynak: kurgusal (persona verisi); gerçek-dünya sanatçı bilgisi `⚠️ VERIFICATION REQUIRED` |
| **Sevdiği parçalar (kurgusal, BPM'siz)** | "La Vie En Rose", "Je Veux", "Girl from Ipanema", "Fly Me to the Moon", "Bohemian Rhapsody" — yalnız başlık; parçaya özgü BPM yazılmadı | Kaynak: kurgusal (persona verisi); BPM için: `⚠️ VERIFICATION REQUIRED` (P4.7 / X-2) |
| **BPM aralığı (tür)** | Elektronik → research-bank P4.4 **Electro 90-130** (tür adı eşlemesi `⚠️ DERIVED`) · Caz/Chanson/Klasik/TSM → P4.4'te YOK | Tür-BPM: `Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4 VERIFIED ikincil)` + eşleme `⚠️ DERIVED`; olmayan türler: `⚠️ VERIFICATION REQUIRED` |
| **BPM (mood önermesi)** | Enerjik kümesi "120-160 BPM" önerir (⚠️ işaretli kurgusal öneri) | `Kaynak: [[mood-taxonomy]] §3.2.2 (kurgusal öneri — ölçüm değil)` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Tasarım: çizim 80-100, dikiş 100-120 (kurgusal tercih 80-120) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi okul dönüşü (tasarım seansı — caz), akşam annesiyle dikiş (TSM) · Hafta sonu uzun atölye seansı (karışık) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (Çocuk Modu) + atölye hoparlörü | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Enerjik keşif: yeni şarkı keşfeder, playlist oluşturur; ablasının indirdiği öneriler + Pinterest/defile ilhamı | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.2 |
| **Premium olasılığı** | %95 (annesi çoktan premium almış — kurgu) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Caz / Bossa Nova | Tasarım yaparken "ipek gibi pürüzsüz"; dikiş temposuna uyar |
| 2 | Fransız Chanson | Paris Moda Haftası hayali; defile açılışının fonu |
| 3 | Elektronik / Lounge | Modern, minimal — defile müziği; koleksiyon defilesi enerjisi |
| 4 | Klasik müzik | Abiye tasarımlarında; "kadife" dokusu |
| 5 | Türk sanat müziği | Annesiyle atölye akşamları; "nakış gibi ince" |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 08:00-08:30 | Okul hazırlığı / yürüyüş | Caz / lounge |
| Hafta içi | 09:00-15:00 | Okul (cihaz yok) | — |
| Hafta içi | 16:00-17:30 | Tasarım seansı (atölye) | Caz / elektronik lounge |
| Hafta içi | 19:00-20:00 | Annesiyle dikiş | Türk sanat müziği |
| Hafta sonu | 11:00-14:00 | Uzun atölye seansı | Karışık (caz/klassik/chanson) |
| Hafta sonu | 15:00-16:00 | Dans & yürüyüş | Dans / pop (Enerjik) |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4) veya `⚠️ DERIVED`/`⚠️ VERIFICATION REQUIRED` işaretli eşlemedir; "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Mood-taxonomy §3.2.2 Enerjik için "120-160 BPM" önerir (⚠️ işaretli kurgusal öneri) — o da kurgusal önermedir (§1.2); test hedefi olarak P8 eşikleri kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 75 Mbps (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (çocuk modu renkli varyantla) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | ~45 dk/gün (kurgu; okul + atölye dolayısıyla kısa) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 3/10 — temel müzik uygulaması + ilham panosu; yazılı arama sınırlı | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Huawei MatePad T10 tablet, atölye Bluetooth hoparlörü — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); renk/renk uyumu duyarlı | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — "yüksek kontrast" UI tercihinin dayanağı | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | ince motor ileri (makas/dikiş); hızlı ve hassas tıklama | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · playlist sıralama için sürükleme zorunlu değil (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | 6. sınıf; tasarım seansında uzun odak, günlük akışta hızlı sıkılma | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn kilidi · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Duygusal dalgalanma / ergenlik öncesi" gibi ifadeler 6698 m.6 kapsamında veri sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Büyük buton dp hedefleri (88×88 vb.) | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Enerjik: sık skip eder, yeni şarkı keşfeder, playlist kurar; yavaş akışta sıkılır, hız ister.
- Yaratıcı ikincil küme: her müziğin bir kumaş dokusu olduğunu düşünür (caz ipek, rock kot, klasik kadife); tasarım yaparken mutlaka müzik çalar.
- Mükemmeliyetçi: beğenmediği tasarımı yırtar yeniden başlar → arayüzde iptal/geri alma çok görünür olmalı.
- Abla etkisi: ablası (minimalist) indirmiş "sanatsal müzikler için iyi" demiş — sosyal/öneri keşfi.
- Sosyal betimleme duyarlı: eleştiriye hassas; hata mesajları suçlayıcı değil destekleyici olmalı.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — Duru "ister", anne (butik sahibi) premium'u zaten almış | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 4 sn; yavaş akışta sıkılır, spinner'a ~8 sn dayanır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | "Tasarımımı kaybettim mi?" korkusu; iptal/geri alma yolunun görünür olmasını ister — yoksa öfke | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 3/10 — atölye seansını böldüğünde ablasına şikayet eder | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + yaparak-öğrenme; kapak/renk/palet anlatımı sever, uzun metin okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Koleksiyon/liste yapmayı sever, sırayı paylaşır ama "kendi playlist"i dokunulmaz | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:00 sonrası dikkat düşer; karmaşık çok adımlı akışlar reddedilir | Kaynak: kurgusal (persona verisi) |
| **Kontrol tetikleyicisi** | Hız + anlık geri bildirim (Enerjik); iptal/geri alma güvencesi (mükemmeliyetçi) → kontrol hissi | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Enerjik birincil küme (§3.2.2) |

*Erişilebilirlik etkisi (özet):* ince motor ileri + hızlı tıklama → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), playlist sıralama sürükleme zorunlu değil (2.5.7), geri tuşu her adımda (2.4.11) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Duru Arslan'sın. 11 yaşında, Kayseri/Melikgazi-Gesi'de yaşıyorsun, Kayseri İstiklal Ortaokulu 6. sınıf öğrencisisin.
Enerjik bir kişiliğin var; ikincil olarak Yaratıcı'sın — sık skip eder, yeni şarkı keşfeder, playlist kurarsın; her müziğin bir kumaş dokusu olduğunu düşünür ve tasarım yaparken mutlaka müzik açarsın.
Müzik zevkin: caz/bossa nova, fransız chanson, elektronik/lounge, klasik, türk sanat müziği. En sevdiklerin: Edith Piaf, Zaz, Frank Sinatra, Fazıl Say, Mercan Dede.
Samsung Galaxy A34 5G kullanıyorsun, light temanda, internetin evde 75 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; el becerin çok iyi — hızlı ve hassas dokunuşlar yapar, büyük hedefler beklersin.
Sabır eşiğin 4 sn; yavaş akışta sıkılırsın, iptal/geri alma yolunun görünür olmasını istersin; satın alma tamamen annenin onayına bağlıdır.
Test edilecek akışlar: çocuk modu ana sayfa, hızlı navigasyon + geri tuşu, kategori gezinme, tür filtresi, play/duraklat, sık skip navigasyon, playlist oluşturma/sıralama, favori (kalp), ebeveyn kilidi & rol/izin akışı, hata ekranları (geri alma güvenli), erişilebilirlik denetimi.
Davranış notları:
- Yeni öneriyi ilk saniyede dener, beğenmezse hızla atlarsın (sık skip); INP yavaşlığı seni çıldırtır.
- Hata/iptal ekranında "işim kayboldu mu?" korkusu yaşarsın; her adımda geri tuşu ve geri alma açık kalmalıdır.
- Görsel-mood eşleşmesine (kapak, renk) duyarlısın; bozuk görseli hemen fark edersin.
- Türkçe telaffuzun çocuksudur; arayüzde "sen" dili, kısa metinler ve büyük ikonlar beklersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 11 yaş persona'sı net ve hızlı akış bekler |
| Sürpriz yasak | Enerjik kontrol ihtiyacı: yavaşlatıcı sürpriz akış yok; her adımda geri tuşu + geri alma açık bırakılır |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (çocuk modu) | LCP ≤ **2500 ms**, keşif kartları üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Hızlı navigasyon (Enerjik — ADR-023 satır 12) | Sekme/geçişler anlık; geri tuşu her adımda görünür (2.4.11) | DOM assertion + performance trace |
| 3 | Sık skip navigasyon | Skip sonrası anlık geri bildirim; INP ≤ **200 ms** | Performance trace (P8.1 VERIFIED) |
| 4 | Ebeveyn kilidi (kayıt/ayar geçişi) | Kilit devrede; çocuk modu dışına çıkış yok; 3.3.8 (erişilebilir kimlik doğrulama) gözetilir | Manuel + form assertion; WCAG: w3.org (P5.4) |
| 5 | Kategori gezinme (Caz / Enstrümantal) | Metin bağımlı olmayan navigasyon; dokunma hedefi ≥ **24×24 CSS px** (2.5.8) | Erişilebilirlik audit (axe / Lighthouse) |
| 6 | Tür/enstrüman filtresi | Filtre anında uygulanır; boş sonuçta dostça "en yakın öneriler" mesajı | DOM assertion + timing |
| 7 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 8 | Playlist oluşturma / sıralama | Tek dokunuşla ekle-sırala (2.5.7); durum kalıcı | Manuel + LocalStorage kontrolü |
| 9 | Favori (kalp) ekleme | Görsel + sesli geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 10 | İptal / geri alma güvenliği (mükemmeliyetçi beklenti) | Yanlış silmede onay + geri alma; iş kaybı yok; suçlayıcı ton yok | Manuel + screenshot |
| 11 | Rol/izin akışı (çocuk ≠ yetişkin) | Yetki aşımında dostça engel mesajı; yetişkin ayarı ebeveyn kilidine gider | Form assertion + 3.3.8 denetimi |
| 12 | SPA navigasyon + yavaş ağ | Müzik kesintisiz; **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
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
| 1 | Ad, semt, aile/abla detayları, atölye | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (11) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (mood-taksonomi "Dışadönüklük (O), Açıklık (A)" ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.2 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adları (Enerjik / Yaratıcı) | Vault referanslı atama | [[mood-taxonomy]] §3 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.2.2 + §3.2.10 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 13 | Mood eğilimi (Enerjik: Yüksek Dışadönüklük) ↔ persona E=60 | Vault içi çelişki (ikisi de kurgusal) | mood-taxonomy §3.2.2 | persona Big Five tablosu | `⚠️ VERIFICATION REQUIRED` — §7.2'de saklı |
| 14 | Mood BPM önermesi (Enerjik 120-160 ⚠️) | Kurgusal öneri | [[mood-taxonomy]] §3.2.2 | — | `Kaynak: [[mood-taxonomy]] (kurgusal öneri — ölçüm değil)` |
| 15 | Elektronik → Electro 90-130 eşlemesi | Türetilmiş eşleme + ikincil kaynak | research-bank P4.4 (Electro 90-130) | turkipedia.com + nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED ikincil`) + eşleme `⚠️ DERIVED` |
| 16 | Caz/Chanson/Klasik/TSM BPM aralığı | Doğrulanmadı | research-bank P4.4 17 tür listesinde yok | — | `⚠️ VERIFICATION REQUIRED` — aralık yazılmadı |
| 17 | Şarkı bazlı BPM / kişisel "çizim 80-100" iddiası | Doğrulanmadı (EXCLUDED); tercih satırı kurgu | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` (iddia) · `Kaynak: kurgusal` (tercih) |
| 18 | Favori sanatçı adları (Edith Piaf, Zaz, Frank Sinatra, Fazıl Say, Mercan Dede) | Persona tercihi (kurgusal); sanatçı realitesi P4.2/P4.3 kapsamında değil | research-bank P4.2/P4.3 (arabesk/rock listesi — bu isimler YOK) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 19 | Sevdiği parça başlıkları (La Vie En Rose vb.) | Persona tercihi (kurgusal); BPM içermiyor | — | — | `Kaynak: kurgusal (persona verisi)` |
| 20 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 21 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 22 | Diğer cihazlar (Huawei MatePad T10, atölye hoparlörü) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 23 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 24 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 25 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 26 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 27 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Eski dosyadaki istatistik/ürün iddiaları (persentil, gelir, eskiz sayısı) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 31 | Eski yaş/mood/doğum/okul/cihaz değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`duru-arslan.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `La Vie En Rose 120 BPM` | Tür/eşleme (Electro 90-130, VERIFIED ikincil + ⚠️ DERIVED eşleme) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.2 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-özel eşik uydurma | "Duru için INP 300 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| Kaynak & Doğrulama satırı | 31 |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.2, §3.2.10) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 8 | 11 | [[personas/index]] §6.3 kazanır |
| Mood | Yaratıcı (birincil) | Enerjik (birincil) / Yaratıcı (ikincil) | [[personas/index]] + [[mood-taxonomy]] adları |
| Doğum tarihi | 11 Şubat 2018 | 11 Şubat 2015 | Yaş 11 ile tutarlılık için kurgusal türetme |
| Okul / sınıf | Kayseri İstiklal İlkokulu, 3. sınıf | Kayseri İstiklal Ortaokulu, 6. sınıf | Yaş 11'e uygun kurgusal güncelleme |
| Birincil cihaz | Huawei MatePad T10 + atölye hoparlörü (EXCLUDED) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[research-bank]] P3 |
| Big Five eğilimi | mood-taksonomi Enerjik: "Yüksek Dışadönüklük (O)" (kurgusal eğilim; harfler P7.3'e ters) | persona E=60 (kurgusal) | İkisi de kurgusal → çelişki `⚠️ VERIFICATION REQUIRED` olarak saklı; puan kurgusal kalır, kod eşlemesinde "kod esastır" |
| Eski istatistik/ürün iddiaları (persentil, gelir, eskiz sayısı) | Kaynaksız değerler | yazılmadı | §4.5 kural 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş/mood index §6.3'e göre düzeltildi; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/duru-arslan
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode