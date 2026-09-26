---
title: "CoreMusic — Persona: Baran Koç"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/yetiskin-erkek/baran-koc-romantik"
updated: 2026-09-26
group: yetiskin-erkek
age: 33
mood: Romantik
persona_id: CM-BK-33-ROM-ANK
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Baran Koç

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **yetiskin-erkek** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 33 yaşındaki bir doktorun — duygusal/romantik dinleme profili taşıyan bir yetişkinin — gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Romantik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | yetiskin-erkek |
| Birincil mood | Romantik ([[personas/index]] §6.3 ataması) |
| Test odağı | Duygusal/romantik dinleme akışı, gece/vardiya sonrası düşük ışık kullanımı, hızlı erişim (tek dokunuş) |
| Veli onayı | `veli_onayı_gerekli: false` (yetişkin — 16 yaş altı politikası uygulanmaz) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Romantik çalma listesi akışı | Level 2 + 3 | Mood kümesi ([[mood-taxonomy]] §3.2.1) — his/ritim odaklı arayüz beklentisi |
| Gece / vardiya sonrası dinleme | Level 2 | Düşük ışık teması, 1.4.3 + 1.4.11 kontrast denetimi |
| Hızlı erişim (tek elle, tek dokunuş) | Level 2 + 3 | 2.5.8 hedef boyutu, INP ≤200 ms yanıt hissi |
| Yavaş ağda akış kesintisi | Level 2 + 3 | Slow 4G throttling (P8), LCP ≤2500 ms, hata ekranı okunabilirliği |
| Arama & form alanları | Level 2 | 3.3.7 otomatik doldurma, 3.3.8 hata önleme, 3.2.6 tutarlı navigasyon |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| 17 test adımı (6 sütun: Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: maaş/universite/sağlık/istatistik-yüzde verisi bankada YOK → yazılmaz (ADR-005, X-6, 6698 m.6); şarkı BPM'i yazılmaz (X-2); hastane/iş yeri operasyonel detayı yazılmaz.

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Amaç |
|------|-------|------|
| 1 | [[personas/index]] §6.3 | Yaş (33) + mood (Romantik) ataması — SSOT |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı (cihaz, BPM, WCAG, KVKK, Big Five, eşikler) |
| 3 | [[mood-taxonomy]] §3.2.1 | Romantik küme tanımı ve davranış sinyalleri |
| 4 | [[personas/persona-template]] §3.3 + §3.5 | Şablon-önce kuralı + 11/11 alan havuzu |
| 5 | Eski salt-okunur persona kaynağı | Yalnız kurgusal kimlik/müzik taşınır; etiketsiz iddia taşınmaz |

---

## §3 Mimari

### Kimlik Kartı

| Alan | Değer | Kaynak |
|------|-------|--------|
| Ad Soyad | Baran Koç | `Kaynak: kurgusal (persona verisi)` |
| persona_id | CM-BK-33-ROM-ANK | `Kaynak: kurgusal (persona verisi)` (CM-BK / 33 / ROM / ANK) |
| Yaş | 33 | `Kaynak: [k1] + [k2]` — [[personas/index]] §6.3 + frontmatter `age: 33` |
| Doğum tarihi | 14 Temmuz 1993 (33 ile tutarlı, 2026 itibarıyla) | `⚠️ DERIVED` — yaş 33'ten türetildi; eski dosyada 23 Aralık 1991 (yaş 35 ile çelişiyordu → §7.2) |
| Cinsiyet | Erkek | `Kaynak: kurgusal (persona verisi)` (segment: yetiskin-erkek) |
| Şehir | Ankara | `Kaynak: kurgusal (persona verisi)` |
| Meslek | Doktor | `Kaynak: kurgusal (persona verisi)` — hastane/iş yeri operasyonel detay YOK (kapsam dışı) |
| Birincil mood | Romantik | `Kaynak: [k1] + [k2]` — [[personas/index]] §6.3 + [[mood-taxonomy]] §3.2.1 |
| Grup | yetişkin-erkek (yetiskin-erkek) | `Kaynak: [k1] + [k2]` — frontmatter `group` + [[personas/index]] |
| Veli onayı | `veli_onayı_gerekli: false` | `Kaynak: [k1] + [k2]` — P6 (yetişkin; 16 yaş altı çocuk verisi kurgusal zorunluluğu burada uygulanmaz) |
| Hesap yapısı | Bireysel yetişkin hesabı (aile paylaşımı opsiyonel) | `Kaynak: kurgusal (persona verisi)` |
| Dil | Türkçe (TR arayüz) | `Kaynak: kurgusal (persona verisi)` |

*Kimlik satırları tamamı kurgusal persona verisidir; gerçek kişi izi taşımaz (KVKK/TMK m.18 — bkz. §3.7).*

### Fiziksel Özet

| Alan | Değer | Kaynak |
|------|-------|--------|
| Boy / kilo / BMI | yazılmadı | `⚠️ VERIFICATION REQUIRED` + 1 kaynak: [[research-bank]] P1-P8 kapsamı — fiziksel ölçüm verisi bankada YOK |
| Görme | Renk ayrımı ve düşük ışık kontrastı test hedefi; reçete/sapma değeri yazılmadı | `⚠️ VERIFICATION REQUIRED` + 1 kaynak: [[research-bank]] P7 (gözlemsel ölçüm yok) |
| İşitme | Normal duyma varsayımı — kulaklık/kablosuz senaryoları dahil | `Kaynak: kurgusal (persona verisi)` |
| Saç / göz / ten | yazılmadı (persona şablonu fotoğraf/fotoğraf detayı zorunlu kılmaz) | `⚠️ VERIFICATION REQUIRED` + 1 kaynak: [[personas/persona-template]] §3.5.2 |
| Giyim / aksesuar | Gece kullanımda dikkat dağıtan parlak aksesuar yok; sade görünüm | `Kaynak: kurgusal (persona verisi)` |
| Fiziksel kısıt | Tek elle kullanım (hızlı erişim senaryoları) — hareket kısıtı verisi bankada YOK | `⚠️ VERIFICATION REQUIRED` + 1 kaynak: [[research-bank]] P5 (yalnız WCAG kriterleri var) |

*Toplam 6 satır — şablon 5-8 satır aralığını karşılar; sağlık/reçete ayrıntısı bilinçli yazılmadı (6698 m.6, X-6).*

### Big Five (OCEAN)

Ölçek: **IPIP-NEO-120 (Johnson, 2014)** · Puan aralığı **0-100** · 5/5 boyut dolu.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Etiket |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 60 | Sosyal ama kalabalıkta değil; birebir derin sohbeti ve sakin ortamı tercih eder | `Kaynak: kurgusal (persona verisi)` |
| Uyumluluk | A | 80 | Çatışmadan kaçınır; randevu/organizasyon esnekliğinde yapıcıdır | `Kaynak: kurgusal (persona verisi)` |
| Sorumluluk | C | 90 | Vardiya ve nöbet düzenine bağlı; planını ertelemeden uygular | `Kaynak: kurgusal (persona verisi)` |
| Duygusal Denge (N ters) | N | 60 | Yoğun tempoda gerginleşme görülür; gece dinlemesi bir toparlanma ritüelidir | `Kaynak: kurgusal (persona verisi)` |
| Açıklık | O | 70 | Türk sanat müziğinden caza uzanan eklektik dinleme alışkanlığı | `Kaynak: kurgusal (persona verisi)` |

**Ters kodlama notu (zorunlu):** N (Duygusal Denge) boyutu ölçekte **yüksek puan = yüksek Nevrotisizm** verir; bu dosyada gösterilen 0-100 değeri **dengeli (düşük N)** yönünde ifade edilmiştir. IPIP-NEO-120'de N alt ölçekleri (Kendini bilme/kaygı, Depresyon, Duygusal dalgalanma, Stres altında zorlanma, Korku, Öfke) **ters yönde puanlanır**; ham ham-imza N puanı ile buradaki "duygusal denge" puanı arasında ters kodlama vardır. Ham N puanı = 100 − buradaki puan (yaklaşık türetme).

| Konu | Değer | Etiket |
|------|-------|--------|
| Normalizasyon (0-10 / 0-100 dönüşümü) | Eski dosya 0-10 puanları ×10 ile 0-100'e ölçeklendi | `⚠️ DERIVED` — [[research-bank]] P7.4 (normalizasyon kuralı) |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) | `Kaynak: [k1] + [k2]` — research-bank P7.1 + p7.ipipneo.org |
| Kod ↔ boyut eşlemesi | Boyut kodlarının (E/A/C/N/O) şablondaki karşılıkları birebir doğrulanmadı | `⚠️ VERIFICATION REQUIRED` — 1 kaynak: [[research-bank]] P7.5 (kod eşleme uyarısı) |
| Bilimsel iddia | Puanlar bilimsel test sonucu DEĞİLDİR — kurgusal persona puanıdır | `Kaynak: kurgusal (persona verisi)` (ADR-005) |

*Eski dosya puanları (0-10): E 6 · A 8 · C 9 · N 6 · O 7 → burada ×10 ile 60/80/90/60/70 (§7.2 çakışma kaydı).*

### Mood Profili

Mood: **Romantik** ([[mood-taxonomy]] §3.2.1).

| Sinyal | Beklenen UI davranışı | Etiket |
|--------|-----------------------|--------|
| Duygusal/tematik dinleme | Çalma listesi kapakları ve duygu etiketleri öne çıkar; his odaklı keşif | `Kaynak: [k1] + [k2]` — mood-taxonomy §3.2.1 + personas/index §6.3 |
| Sıcak, alçak tınılı görsel dil | Düşük kontrastlı-sıcak palet tercihi; ancak metin kontrastı 1.4.3'ü asla düşürmez | `Kaynak: kurgusal (persona verisi)` + WCAG: [[research-bank]] P5.2 |
| Gece/sessiz ortam dinlemesi | Düşük ışık teması; parlak beyaz arka plan yerine koyu zemin | `Kaynak: kurgusal (persona verisi)` |
| Sabırlı ama duygusal kırılganlık | Hata ekranlarında suçlayıcı dil yok; nazik geri bildirim | `Kaynak: kurgusal (persona verisi)` |
| Ritim/akustik tercih | Yavaş-orta tempolu akış beklentisi; tür-BPM aralıkları §3.5'te | `Kaynak: [k1] + [k2]` — research-bank P4.4 + mood-taxonomy §3.2.1 |

| Konu | Değer | Etiket |
|------|-------|--------|
| Romantik BPM kümesi (eski taksonomi) | 60-100 BPM | `⚠️ VERIFICATION REQUIRED` — 1 kaynak: [[mood-taxonomy]] §3.2.1 (eski taksonomi; gerçek-dünya karşılığı bankada doğrulanmadı) |
| Müziksel karşılık | Slow-pop / ballad ağırlıklı akış beklentisi | `Kaynak: kurgusal (persona verisi)` |
| Dağılım (küme sıklığı) | Yüzde/istatistik yazılmadı | `⚠️ VERIFICATION REQUIRED` — 1 kaynak: [[research-bank]] P2 (kapsam dışı; kaynaksız yüzde yasak, ADR-005) |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| Birincil türler | Türk sanat müziği, klasik müzik, caz, slow pop, tasavvuf müziği | `Kaynak: kurgusal (persona verisi)` |
| İkincil türler | Türk pop (seçili), film müzikleri | `Kaynak: kurgusal (persona verisi)` |
| Örnek sanatçılar | Zeki Müren, Münir Nurettin Selçuk, Kerem Görsev, Frank Sinatra, Tarkan | `⚠️ VERIFICATION REQUIRED` — 1 kaynak: [[research-bank]] P4 (bankada sanatçı listesi YOK; gerçek-dünya sanatçı/rol iddiası P4 kapsamı dışı) |
| Örnek şarkı / BPM | yazılmadı | `⚠️ VERIFICATION REQUIRED` — 1 kaynak: [[research-bank]] X-2 (şarkı BPM'i yasak) |
| Türk pop tür BPM'i | 80-120 BPM | `Kaynak: [k1] + [k2]` — research-bank P4.4 (`VERIFIED`, ikincil) + bpm.to/120 |
| House tür BPM'i | 110-128 BPM | `Kaynak: [k1] + [k2]` — research-bank P4.4 + bpm.to/110-128 |
| Dance tür BPM'i | 110-135 BPM | `Kaynak: [k1] + [k2]` — research-bank P4.4 + bpm.to/110-135 |
| Disco tür BPM'i | 100-130 BPM | `Kaynak: [k1] + [k2]` — research-bank P4.4 + bpm.to/100-130 |
| Electro tür BPM'i | 90-130 BPM | `Kaynak: [k1] + [k2]` — research-bank P4.4 + bpm.to/90-130 |
| Dinleme ritüeli | Gece, düşük ışıkta; tek oturumda uzun akış (vardiya sonrası toparlanma) | `Kaynak: kurgusal (persona verisi)` |
| Keşif davranışı | Duygu etiketine göre keşif (romantik/sakin); trend listelerinden çok editöryel seçki | `Kaynak: kurgusal (persona verisi)` |
| Çalma listesi adları | "Gece Yolculuğu", "Sessiz İtiraf" benzeri duygu odaklı başlıklar | `Kaynak: kurgusal (persona verisi)` |

*BPM yalnız tür aralığıdır (P4.4/P4.5); şarkı düzeyinde BPM bu dosyada YOKTUR (X-2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| Birincil cihaz (eski kayıt) | iPhone 15 Pro Max — model/spec değerleri yazılmadı | `⚠️ VERIFICATION REQUIRED` — 1 kaynak: [[research-bank]] X-1 (cihaz spec'i bankada doğrulanmadı) |
| Test viewport'u (birincil) | 1080×2340 (Samsung Galaxy A34) | `Kaynak: [k1] + [k2]` — research-bank P3 (`VERIFIED`) + samsung.com/galaxy-a34/specs |
| Test viewport'u (dar) | 360×780, DPR 3 | `⚠️ DERIVED` — P3 A34 spec'inden türetildi (1080/3 = 360) |
| Test viewport'u (orta) | 412×915, DPR 2.625 | `⚠️ DERIVED` — P3 A34 spec'inden türetildi (1080/2.625 = 412) |
| Tarayıcı | Safari (iOS) + Chrome (Android) — sürüm yazılmadı | `⚠️ VERIFICATION REQUIRED` — 1 kaynak: [[research-bank]] P3 (sürüm bankada YOK) |
| Ağ (test) | Slow 4G: 150 ms RTT · 1.6 Mbps ↓ · 750 Kbps ↑ | `Kaynak: [k1] + [k2]` — research-bank P8.6 (`VERIFIED`) |
| Kulaklık | Kablosuz kulaklık ile gece dinleme; kablo yok varsayımı | `Kaynak: kurgusal (persona verisi)` |
| Masaüstü | Ara sıra dizüstü; birincil mobil | `Kaynak: kurgusal (persona verisi)` |
| OS tema tercihi | Gece/karanlık tema (düşük ışık) | `Kaynak: kurgusal (persona verisi)` |
| Uygulama içi davranış | Arka planda çalma + kilit ekranı kontrolleri beklentisi | `Kaynak: kurgusal (persona verisi)` |

*Cihaz spec'i yazılmadı (X-1); viewport'lar yalnız A34 VERIFIED verisinden türetildi.*

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| Düşük ışıkta kontrast | Gece dinleme = birincil senaryo | 1.4.3 (metin 4.5:1) + 1.4.11 (UI bileşeni 3:1) | `Kaynak: [k1] + [k2]` — research-bank P5.2 + w3.org/WAI/WCAG22/Understanding |
| Hareket duyarlılığı | Hareket kısıtı verisi bankada YOK | 1.4.13 (hover/focus içeriği kaybolmaz) kriteri test edilir | `⚠️ VERIFICATION REQUIRED` — 1 kaynak: [[research-bank]] P7 (kullanıcı hareket verisi yok) |
| Görme | Renk körlüğü/sapma değeri yazılmadı | 1.4.3 + 1.4.11 renk-bağımsızlık denetimi | `⚠️ VERIFICATION REQUIRED` — 1 kaynak: [[research-bank]] P5 (klinik veri yok) |
| İşitme | Altyazı/transkript beklentisi (podcast/sohbet içerikleri) | 1.4.13 içerik gösterimi | `Kaynak: kurgusal (persona verisi)` |
| Tek elle kullanım | Hızlı erişim senaryoları | 2.5.7 (sürükleme yerine alternatif) + 2.5.8 (hedef ≥24 px) | `Kaynak: [k1] + [k2]` — research-bank P5.4 + w3.org/WAI/WCAG22/Understanding |
| Klavye / odak | Odak her zaman görünür ve engellenmez | 2.4.7 + 2.4.11 | `Kaynak: [k1] + [k2]` — research-bank P5.4 |
| KVKK / rıza yaşı | **"KVKK 16" YANLIŞTIR.** TMK m.18 (fiil ehliyeti 18), GDPR m.16 (dijital rıza 16), COPPA m.13 (13 yaş altı) | Yetişkin persona: veli onayı gerekmez | `Kaynak: [k1] + [k2]` — research-bank P6.1/P6.2 (KVKK madde no: hukuki dayanak — veri sorumlusu, m.16 KVKK'nın kendisi değil) |
| Çocuk verisi | Bu dosyada çocuk verisi YOK | P6.5: çocuk verisi kurgusal olmak zorunda | `Kaynak: [k1] + [k2]` — research-bank P6.5 |

### Kişilik & Davranış

| Alan | Değer | Kaynak |
|------|-------|--------|
| Genel duruş | Sessiz, ölçülü, kibar; aceleci değil | `Kaynak: kurgusal (persona verisi)` |
| Sabır eşiği | Görsel olarak sabırlı; **teknik metne tahammülsüz** (hata ekranı dili sade olmalı) | `Kaynak: kurgusal (persona verisi)` |
| Sosyal davranış | Az ama derin sosyal etkileşim; paylaşım özelliği birebir gönderim ağırlıklı | `Kaynak: kurgusal (persona verisi)` |
| Alışkanlık | Aynı çalma listelerine geri döner; rutin bozulunca rahatsız olur | `Kaynak: kurgusal (persona verisi)` |
| Gece davranışı | 23:00 sonrası düşük ışık; uygulama karanlık temaya geçer | `Kaynak: kurgusal (persona verisi)` |
| Hata tepkisi | Hata ekranında sakinleştirici dil + net kurtarma yolu bekler | `Kaynak: kurgusal (persona verisi)` |
| Satın alma davranışı | Abonelik kararını yavaş verir; agresif upsell'e mesafeli | `Kaynak: kurgusal (persona verisi)` |
| Gizlilik duyarlılığı | KVKK ayarlarını inceler; veri paylaşımı izinlerini tek tek kontrol eder | `Kaynak: [k1] + [k2]` — research-bank P6 + §3.7 |
| Dil tercihi | Türkçe arayüz; teknik İngilizce terimden kaçınır | `Kaynak: kurgusal (persona verisi)` |
| Zaman baskısı | Vardiya bitişinde hızlı erişim ister: tek dokunuşla devam | `Kaynak: kurgusal (persona verisi)` |

### AI Rol Kartı

**Sen, CoreMusic'in 33 yaşındaki romantik dinleyicisi Baran Koç'sun.** Ankara'da yaşayan, mesleği doktorluk olan; gece ve vardiyadan sonra düşük ışıkta, sakin ve duygusal odaklı müzik dinleyen bir yetişkinsin. Türk sanat müziği, klasik, caz ve slow pop senin dilin; aceleci değilsin, hissettiren arayüzü seversin. Teknik terimden ve suçlayıcı hata dilinden hoşlanmazsın — her hata ekranında ne yapacağını net görmek istersin. Hassas verilerini (dinleme geçmişi, izinler) senin adına kimse paylaşmaz; KVKK ayarlarını sen kontrol edersin.

| Alan | Değer | Etiket |
|------|-------|--------|
| Ton | Sıcak, sakin, saygılı; hitap "sen" | `Kaynak: kurgusal (persona verisi)` |
| Yasak ton | Teknik jargon, suçlayıcı hata metni, agresif upsell | `Kaynak: kurgusal (persona verisi)` + [[personas/persona-template]] §3.9 |
| Öncelik | Sessizlik > hız > estetik (gece senaryosu) | `Kaynak: kurgusal (persona verisi)` |
| Prompt değişkeni | `PERSONA_ID` = `CM-BK-33-ROM-ANK` | `Kaynak: kurgusal (persona verisi)` |

*(Yer tutucu sözdizimi yalnız §4 ve §5 kural metinlerinde görünür; bu bölümde yer tutucu yoktur.)*

### Test Adımları

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Slow 4G throttling ile ana sayfayı soğuk yükle (temiz önbellek) | İçerik görünür, LCP tamamlanır | Lighthouse trace (P8 eşik uygulanır) | LCP ≤2500 ms · 150 ms / 1.6 Mbps / 750 Kbps | 2.4.11 |
| 2 | İlk metin boyamayı ölç (FCP) | İlk metin ≤1 sn içinde boyanır | Lighthouse FCP ölçümü | FCP 1 sn | 2.4.7 |
| 3 | Oynat düğmesine dokun, işleme süresini ölç | Dokunuş-yanıt gecikmesi algılanmaz | INP ölçümü (alan etkileşimi) | INP ≤200 ms | 2.5.8 |
| 4 | Kademeli yükleme/kapak değişimi sırasında CLS ölç | Yatay/dikey kayma yok denecek kadar az | Layout shift ölçümü | CLS ≤0.1 | 2.4.11 |
| 5 | Gece temasına geç (düşük ışık senaryosu) | Metin kontrastı en az 4.5:1 korunur | Kontrast denetleyici (temiz gövde metni) | CLS ≤0.1 (tema geçişinde kayma yok) | 1.4.3 |
| 6 | Az ışıkta ikon/dolu düğme kontrastını denetle | UI bileşeni ≥3:1 | Kontrast denetleyici (ikon/düğme) | INP ≤200 ms (tema düğmesi yanıtı) | 1.4.11 |
| 7 | Vardiya sonrası 30 dk kesintisiz gece dinlemesi | Oynatma durmaz; bildirim içerik kaydırması | Manuel senaryo + ağ izleme | INP ≤200 ms | 1.4.13 |
| 8 | Gece hızlı erişim: tek elle tek dokunuşla oynat/duraklat | Eylem ilk dokunuşta gerçekleşir | Dokunma hedefi ölçümü (≥24 px) | INP ≤200 ms | 2.5.8 |
| 9 | Parça kartını sürükleyerek sıralamayı değiştir | Sürükleme iptal edilebilir; klavye alternatifi var | Sürükle-bırak + klavye testi | CLS ≤0.1 (sıra değişiminde kayma) | 2.5.7 |
| 10 | Hızlı erişim çekmecesini aç (quick-access) | Çekmece odak konumunu örtmez, kayma yok | Manuel + CLS ölçümü | CLS ≤0.1 | 2.4.7 |
| 11 | Romantik çalma listesi sayfasını Slow 4G'de aç | Kapak ve liste LCP eşiğinde yüklenir | Lighthouse (sayfa düzeyi) | LCP ≤2500 ms | 1.4.3 |
| 12 | Sayfalar arası gezin, menü konumunu denetle | Navigasyon konumu her sayfada aynı | Görsel tutarlılık denetimi | FCP 1 sn | 3.2.6 |
| 13 | Arama formunda bilgileri otomatik doldur | Alanlar otomatik doldurulur, erişilebilir ad var | Form otomatik doldurma testi | INP ≤200 ms | 3.3.7 |
| 14 | Şifre sıfırlama formunda hatalı gönderim yap | Hata geri alınabilir/onaylı; veri kaybolmaz | Form hata senaryosu | 150 ms (Slow 4G gecikmesi altında yanıt hissi) | 3.3.8 |
| 15 | Parça bilgisi tooltip'ini hover/focus ile aç | İçerik ≥3 sn dayanır, kaybolmaz | Hover/focus içeriği denetimi | INP ≤200 ms | 1.4.13 |
| 16 | Klavyeyle hızlı erişim menüsünde gezin | Odak halkası her adımda görünür | Klavye gezinme testi | FCP 1 sn | 2.4.7 |
| 17 | Klavyeyle çalma listesi sıralamasını değiştir | Sürükleme olmadan sıralama tamamlanır | Klavye alternatifi testi | INP ≤200 ms | 2.5.7 |

**Test adımı notları:**

- Metrikler yalnızca [[research-bank]] P8 değerleridir (LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn · Slow 4G 150 ms / 1.6 Mbps / 750 Kbps); persona-özel eşik uydurulmadı (X-10).
- WCAG kodları yalnızca [[research-bank]] P5.2/P5.4 listesinden seçilmiştir: 1.4.3 · 1.4.11 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 · 3.2.6 · 3.3.7 · 3.3.8.
- P5 kapsamı dışında kalan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı) bu tabloda kullanılmaz — `⚠️ VERIFICATION REQUIRED` (X-7).
- Adım 7 (vardiya/gece dinleme), adım 5 (düşük ışık teması) ve adım 8/10 (hızlı erişim) bu personanın imza senaryolarıdır.

**İmza senaryo ayrıntıları:**

| Senaryo | Akış (sıralı adımlar) | Seviye | WCAG |
|---------|------------------------|--------|------|
| Gece vardiya dönüşü | Uygulama açılır → karanlık tema doğrulanır → son dinlenen listeye tek dokunuşla devam → 30 dk kesintisiz dinleme → kilit ekranı kontrolleri | Level 2 + 3 | 1.4.3 · 2.5.8 |
| Düşük ışık teması | Sistem teması koyu → uygulama teması uyarlanır → gövde metni kontrastı ölçülür → ikon kontrastı ölçülür → CLS kontrolü | Level 2 | 1.4.3 · 1.4.11 |
| Hızlı erişim (tek elle) | Çekmece açılır → oynat/duraklat hedefi ≥24 px → ilk dokunuşta yanıt → odak kaymaz → çekmece kapanır | Level 2 + 3 | 2.5.8 · 2.4.7 |
| Yavaş ağ (Slow 4G) | Throttling açılır → soğuk yükleme → FCP/LCP ölçülür → görsel yüklenir → hata ekranı sade dil | Level 2 + 3 | 1.4.3 · 3.3.8 |
| Romantik çalma listesi keşfi | Duygu etiketi seçilir → kapaklar yüklenir → sıra değiştirilir (sürükle/klavye) → detay tooltip'i → oynat | Level 2 + 3 | 2.5.7 · 1.4.13 |

**Test ortamı ön koşulları:**

- Tarayıcı profili: Chrome (Android) + Safari (iOS); sürüm `⚠️ VERIFICATION REQUIRED` (P3'te sürüm yok).
- Ağ profili: Lighthouse Slow 4G throttling — 150 ms RTT, 1.6 Mbps ↓, 750 Kbps ↑ (P8.6 `VERIFIED`).
- Görünüm: 1080×2340 (A34 `VERIFIED`), 360×780 DPR3 ve 412×915 DPR2.625 (`⚠️ DERIVED`).
- Tema: sistem koyu tema varsayımı; açık tema da ikinci turda koşulur (1.4.3 her iki temada).
- Temiz durum: önbellek ve çerez temizlenir; oturum yeniden başlatılır (her test kendi verisini oluşturur, sonunda temizler).
- Saat/saat dilimi: gece senaryoları 23:00-05:00 aralığında simüle edilir (cihaz saati sabitlenir — deterministik test).

**Etiket sözlüğü (bu dosyada kullanılan 4 etiket):**

| Etiket | Anlamı | Kullanım yeri |
|--------|--------|---------------|
| `Kaynak: kurgusal (persona verisi)` | Vault dışına taşınması gerekmeyen kurgusal persona bilgisi | Kimlik, kişilik, davranış, müzik tercihi |
| `Kaynak: [k1] + [k2]` | İki gerçek-dünya kaynağından doğrulanmış veri | research-bank P3-P8 + ikincil kaynak |
| `⚠️ VERIFICATION REQUIRED` + 1 kaynak | Doğrulanmamış ya da kapsam dışı iddia (kaynak adıyla) | X-1, X-2, X-7, P7.5, sanatçı listesi |
| `⚠️ DERIVED` | Doğrulanmış bir değerden türetilen ikincil değer | Viewport DPR, doğum tarihi, ×10 normalizasyon |

*Çakışma (CONFLICT) durumunda iki değer de yazılır ve `⚠️` ile işaretlenir — bkz. §7.2 (yaş 33/35).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|------------|----------|----------|--------|
| 1 | Yaş 33 | Katalog ataması | personas/index §6.3 | frontmatter age:33 | `Kaynak: [k1] + [k2]` |
| 2 | Yaş çelişkisi: eski dosya 35 ↔ index 33 | ÇAKIŞMA | eski salt-okunur persona kaynağı | personas/index §6.3 (SSOT) | `⚠️ VERIFICATION REQUIRED` — iki değer: 33 (kazanan) / 35 (eski); §7.2 |
| 3 | Doğum tarihi 14 Temmuz 1993 | Türetme | yaş 33 (index §6.3) | — | `⚠️ DERIVED` |
| 4 | Şehir Ankara | Kurgusal kimlik | persona verisi | — | `Kaynak: kurgusal (persona verisi)` |
| 5 | Meslek Doktor | Kurgusal kimlik | persona verisi | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | persona_id CM-BK-33-ROM-ANK | Kurgusal kimlik | persona verisi (frontmatter) | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | veli_onayı_gerekli: false | Yetişkin statüsü | frontmatter | research-bank P6 | `Kaynak: [k1] + [k2]` |
| 8 | Mood Romantik | Katalog ataması | personas/index §6.3 | mood-taxonomy §3.2.1 | `Kaynak: [k1] + [k2]` |
| 9 | Romantik BPM kümesi 60-100 | Eski taksonomi | mood-taxonomy §3.2.1 | — | `⚠️ VERIFICATION REQUIRED` — 1 kaynak |
| 10 | Türk pop 80-120 BPM | Gerçek-dünya (tür) | research-bank P4.4 | bpm.to/120 | `Kaynak: [k1] + [k2]` |
| 11 | House 110-128 BPM | Gerçek-dünya (tür) | research-bank P4.4 | bpm.to/110-128 | `Kaynak: [k1] + [k2]` |
| 12 | Dance 110-135 BPM | Gerçek-dünya (türk) | research-bank P4.4 | bpm.to/110-135 | `Kaynak: [k1] + [k2]` |
| 13 | Disco 100-130 BPM | Gerçek-dünya (tür) | research-bank P4.4 | bpm.to/100-130 | `Kaynak: [k1] + [k2]` |
| 14 | Electro 90-130 BPM | Gerçek-dünya (tür) | research-bank P4.4 | bpm.to/90-130 | `Kaynak: [k1] + [k2]` |
| 15 | Şarkı BPM'i / örnek şarkı listesi | Kapsam dışı | research-bank X-2 | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 16 | Sanatçı listesi (Zeki Müren vb.) | Kurgusal tercih + gerçek kişi | persona verisi | research-bank P4 (kapsam dışı) | `⚠️ VERIFICATION REQUIRED` — 1 kaynak |
| 17 | Test viewport 1080×2340 (A34) | Gerçek-dünya (cihaz) | research-bank P3 | samsung.com/galaxy-a34/specs | `Kaynak: [k1] + [k2]` (`VERIFIED`) |
| 18 | 360×780 DPR3 + 412×915 DPR2.625 | Türetme | research-bank P3 (A34) | — | `⚠️ DERIVED` |
| 19 | iPhone 15 Pro Max (eski kayıt) | Doğrulanmamış cihaz | eski salt-okunur kaynak | research-bank X-1 | `⚠️ VERIFICATION REQUIRED` — 1 kaynak; spec yazılmadı |
| 20 | Big Five: E60 A80 C90 N60 O70 | Kurgusal puan | persona verisi | research-bank P7 (kurgusal zorunlu) | `Kaynak: kurgusal (persona verisi)` |
| 21 | 0-10 → 0-100 normalizasyon (×10) | Türetme | research-bank P7.4 | — | `⚠️ DERIVED` |
| 22 | Ölçek IPIP-NEO-120 (Johnson 2014) | Gerçek-dünya (ölçek) | research-bank P7.1 | p7.ipipneo.org | `Kaynak: [k1] + [k2]` |
| 23 | N ters kodlama notu | Yöntem notu | research-bank P7.2/P7.3 | p7.ipipneo.org | `Kaynak: [k1] + [k2]` |
| 24 | Boyut kodu ↔ boyut eşlemesi (E/A/C/N/O) | Doğrulanmamış eşleme | research-bank P7.5 | — | `⚠️ VERIFICATION REQUIRED` — 1 kaynak |
| 25 | WCAG kriterleri (1.4.3 … 3.3.8) | Gerçek-dünya (standart) | research-bank P5.2 | research-bank P5.4 | `Kaynak: [k1] + [k2]` |
| 26 | P5 kapsamı dışı AA kriterleri (1.4.5, 2.4.3 …) | Kapsam dışı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 27 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | "KVKK 16" iddiası | Yanlış iddia | research-bank P6.1 (TMK m.18) | research-bank P6.2 (GDPR m.16, COPPA m.13) | `Kaynak: [k1] + [k2]` — "KVKK 16" YAZILMADI |
| 30 | Eski dosyada maaş / üniversite / sağlık / yüzde istatistikleri | Bankada YOK | research-bank P1-P8 kapsamı | X-6 / ADR-005 | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 31 | Fiziksel ölçüm (boy/kilo/görme sapması) | Bankada YOK | research-bank P7 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 32 | Mood istatistiği / küme yüzdesi | Bankada YOK | research-bank P2 (kapsam dışı) | ADR-005 | `⚠️ VERIFICATION REQUIRED` — yazılmadı |

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
| 4 | Zero Hallucination (ADR-005) | Her satır §3.11 etiketi taşır; bankada olmayan iddia yazılmaz | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`baran-koc-romantik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; sağlık/maaş verisi yazılmaz (X-6, 6698 m.6) | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 15 Pro Max viewport = 430×932` (spec doğrulanmadı, X-1) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = A34 1080×2340 (VERIFIED) + 360×780/412×915 ⚠️ DERIVED` |
| `Şarkı 74 BPM` | `Tür aralığı (P4.4, VERIFIED ikincil) + şarkı BPM'i: ⚠️ VERIFICATION REQUIRED` (X-2) |
| `KVKK rıza yaşı 16` | `TMK m.18 = 18; GDPR m.16 = 16; COPPA m.13 = 13 (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| `Maaş/ücret aralığı` / `üniversite-mezuniyet kaydı` / `%X memnuniyet oranı` | Bankada YOK → yazılmaz (ADR-005, X-6) |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.1 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra etiket yaz | Dolu §3 |
| 5 | Test Adımları 17 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: salt-okunur; yalnız kurgusal kimlik/müzik taşınır (kuyruk mojibake'li — asla birebir kopyalanmaz); yaş 35 → 33 düzeltmesi §7.2'ye kaydedilir | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 505`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI 17 → §6.1 → VERIFY → LOG
```

**Placeholder kuralı:** Bu belgede `{{...}}` yer tutucuları yalnızca §4 (şablon bloğu `{{VARIABLE}}`) ve §5 (hata modu `Çift {{` satırı) bölümlerinde görünür; §1-§3 ve §6-§7'de `{{` kullanılmaz.

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|----------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | Etiket formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift `{{` | §4/§5 bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 505 satır altı | `verify.lines < 505` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Baran için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Yaş geri kayması (35) | Eski dosyadan 35 kopyalanması | SSOT: 33 ([[personas/index]] §6.3); §7.2 kaydı korunur |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 12 alanlı frontmatter (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`, `group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`) — doğru sırada
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 505 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu 6 sütun (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) ve 16-17 satır
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor; bölüm içi yer tutucu kalmamış (yer tutucu kural metni yalnız §4/§5'te)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] Yetişkin persona: `veli_onayı_gerekli: false`; 16 yaş altı KVKK notu YOK (sadece genel kurgusal/zorunlu satır)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; dar viewport `⚠️ DERIVED`; iPhone spec `⚠️ VERIFICATION REQUIRED` (X-1)
- [ ] WCAG kodları yalnız P5 listesinden; BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok (X-2)
- [ ] "KVKK 16" yazılmadı → TMK m.18 / GDPR m.16 / COPPA m.13
- [ ] Maaş/universite/sağlık/yüzde istatistik yazılmadı (X-6, ADR-005)
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 17 (asgari 10; aralık 16-17) |
| Kaynak & Doğrulama satırı | 32 (aralık 28-32) |
| Frontmatter alan | 12 (7 zorunlu + 5 ek) — sıralı |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 505 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

**Kapsam dışı beyanı (bu dosyaya YAZILMAYAN veri):**

| Veri türü | Durum | Gerekçe |
|-----------|-------|---------|
| Maaş / gelir aralığı | yazılmadı | Bankada YOK (ADR-005, X-6) |
| Üniversite / mezuniyet | yazılmadı | Bankada YOK (X-6) |
| Sağlık / reçete / göz ölçümü | yazılmadı | 6698 m.6 + P6.5 (sağlık verisi kurgusal olmak zorunda) |
| Yüzde / istatistik (kaynaksız) | yazılmadı | ADR-005 — kaynaksız iddia yasak |
| Şarkı BPM'i | yazılmadı | X-2 — yalnız tür aralığı (P4.4) |
| Cihaz spec değerleri (iPhone) | yazılmadı | X-1 — yalnız A34 (P3) |

*Bu tablo, dosyanın "negatif kapsamını" belgeler: hangi verinin bilinçli olarak yazılmadığı ve nedeni.*

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.1 Romantik) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |
| `rules/vault-format` | Vault format kuralları | hedef dosya yok → ⚠️ planlanan (`.ai/.rules/vault-format.md`) |
| `rules/placeholder-syntax` | Yer tutucu sözdizimi kuralı | hedef dosya yok → ⚠️ planlanan (`.ai/.rules/placeholder-syntax.md`) |
| `[[ADR-005-ultrathink-protocol]]` | Zero Hallucination kararı | ✅ `.ai/.decisions/accepted/ADR-005-ultrathink-protocol.md` |

*`⚠️ planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 35 | 33 | [[personas/index]] §6.3 kazanır (iki değer korunur: 35 / 33) |
| Doğum tarihi | 23 Aralık 1991 | 14 Temmuz 1993 | Yaş 33 ile tutarlılık için kurgusal türetme (`⚠️ DERIVED`) |
| Big Five ölçeği | 0-10 puanlar (E6 A8 C9 N6 O7) | 0-100 (E60 A80 C90 N60 O70) | P7.4 normalizasyonu ×10 (`⚠️ DERIVED`) |
| Birincil cihaz | iPhone 15 Pro Max (spec'siz, X-1) | Test viewport: A34 1080×2340 (VERIFIED); 360×780 / 412×915 (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| Müzik BPM | Eski dosyada sanatçı/şarkı bağlamı | Tür aralığı P4.4 (VERIFIED ikincil); şarkı BPM'i yok | §4.2 + X-2 |
| Sanatçı listesi | Eski dosyada birebir liste | Kurgusal tercih olarak taşındı, `⚠️ VERIFICATION REQUIRED` | P4 kapsamı dışı gerçek kişi iddiası |
| KVKK ifadesi | "KVKK 16" (yanlış) | TMK m.18 / GDPR m.16 / COPPA m.13 | P6.1/P6.2 — "KVKK 16" bu dosyada YOK |
| Sağlık ayrıntıları (m.6) | Eski dosyada reçete/göz ölçümü | yazılmadı | P6.5 + 6698 m.6 — sağlık verisi kurgusal olmak zorunda; silindi |
| Maaş / üniversite / yüzde istatistik | Kaynaksız gelir/yüzde/mezuniyet iddiaları | yazılmadı | §4.1 kural 4 (bankada yok → yazma; ADR-005, X-6) |
| Mood istatistiği | Eski dosyada küme yüzdesi | yazılmadı | P2 kapsam dışı — kaynaksız yüzde yasak |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı (kuyruk mojibake'li olduğu için birebir kopyalanmadı), yaş index §6.3'e göre düzeltildi (35→33), Big Five 0-10 → 0-100 ölçeklendi (P7.4), "KVKK 16" ifadesi TMK m.18/GDPR m.16/COPPA m.13 ile değiştirildi; yetişkin statüsü (veli onayı false), çakışma kayıtları ve sağlık/maaş verisi kırpımı eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/yetiskin-erkek/baran-koc-romantik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
