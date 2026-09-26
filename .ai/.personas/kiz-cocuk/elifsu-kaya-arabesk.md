---
title: "CoreMusic — Persona: Elifsu Kaya-Arabesk"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/elifsu-kaya-arabesk"
updated: 2026-09-26
group: kiz-cocuk
age: 9
mood: Arabesk Kaşif
persona_id: CM-EK-09-ARB-ADA
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Elifsu Kaya-Arabesk

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

## §1 Amaç

Bu persona, CoreMusic çocuk deneyimini 9 yaşındaki Elifsu üzerinden — aileden gelen arabesk alışkanlığı + keşif dürtüsü ekseninde — test etmek için tanımlanmıştır. Kaynak: eski salt-okunur `.ai/personas/kiz-cocuk/elifsu-kaya-arabesk.md` (yalnız kurgusal kimlik/müzik/rutin taşındı) + [[personas/index]] §6.3 (yaş/mood SSOT).

| Alan | Değer |
|------|-------|
| Persona sahibi | Elifsu Kaya-Arabesk |
| Yaş | 9 (index §6.3 SSOT) |
| Mood (birincil) | Arabesk Kaşif |
| Persona ID | CM-EK-09-ARB-ADA |
| Veli onayı gerekli | ✅ true (13 yaş altı — ADR-023) |
| Birincil cihaz | Samsung Galaxy A34 5G (P3.3 VERIFIED) |

**Test kapsamı senaryo aileleri:**

| # | Senaryo ailesi | Bu perspektiften test edilen |
|---|----------------|------------------------------|
| 1 | Çocuk dostu arayüz (A1) | Büyük dokunma hedefleri, ikon ağırlıklı navigasyon, "sen" dili |
| 2 | İçerik filtresi / KKB (A3) | Yaşa uygun arabesk doğrulama, 18+ tema engeli |
| 3 | Aile paylaşımı (F) | Ebeveyn–çocuk paylaşım akışı, veli onay adımı |
| 4 | Mood keşfi (M) | Arabesk Kaşif → tür/palet yönlendirmesi (§3.4) |
| 5 | Erişilebilirlik (E) | 7 WCAG kriteri (P5) çocuk arayüzünde |

## §2 Kapsam

| Kapsam | Kapsam dışı |
|--------|-------------|
| Çocuk arayüzü, ikon navigasyon, "sen" dili | Şarkı sözü gösterme/çeviri (ADR-001) |
| Yaş filtresi ve KKB içerik doğrulaması | Kibariye diskografisi (P4.2 dışı → ⚠️VR) |
| Aile paylaşımı + veli onay akışı | 18+ arabesk temaları (RTÜK derleme — ⚠️VR) |
| Arabesk tür keşfi, kuşak dinleme | Persentil/gelir/istatistik taşımak (⚠️VR satırı) |
| Mood → palet/öneri yönlendirmesi | Şarkı-bazlı BPM iddiası (EXCLUDED X-2) |

**Okuma sırası:**

| # | Sıra | Dosya | Amaç |
|---|------|-------|------|
| 1 | Bu persona | `kiz-cocuk/elifsu-kaya-arabesk.md` | Kimlik/müzik/rutin |
| 2 | SSOT yaş/mood | `personas/index.md` §6.3 | Yaş 9 + Arabesk Kaşif |
| 3 | Mood tanımı | `mood-taxonomy.md` §3.4 satır 21 | Küme tanımı + test etkisi |
| 4 | Araştırma | `research-bank.md` P1-P8 | Cihaz/müzik/WCAG/eşik |
| 5 | Şablon | `.templates/personas/persona-template.md` | İskelet (Guardrail #16) |
| 6 | Karar | `ADR-023` + `ADR-001` | Veli onayı + kapsam sınırı |

## §3 Mimari

### Kimlik Kartı

| Alan | Değer | Kaynak |
|------|-------|--------|
| Ad | Elifsu Kaya-Arabesk | Kaynak: kurgusal (persona verisi) |
| Doğum | 17 Ağustos 2017 | Kaynak: kurgusal (persona verisi — 9 yaşla uyumlu) |
| Yaş | 9 | [[personas/index]] §6.3 (SSOT) |
| Şehir / semt | Adana / Seyhan | Kaynak: kurgusal (persona verisi) |
| Okul | Mehmet Akif İlkokulu — 3. sınıf | Kaynak: kurgusal (persona verisi) |
| Aile | Anne-baba + anneanne + dede (kuşak dinleme) | Kaynak: kurgusal (persona verisi) |
| Mood (birincil) | Arabesk Kaşif | [[personas/index]] §6.3 + [[mood-taxonomy]] §3.4 |
| Mood (eski dosya) | Duygusal/Sadık | ÇAKIŞMA → §7.2 (index kazanır) |
| veli_onayı_gerekli | true | Türetilmiş: P6.3/P6.4 (TMK m.18 · COPPA 13 · GDPR 16) |

**ID parça dökümü:** `CM-EK-09-ARB-ADA` = **CM** CoreMusic · **EK** Elifsu Kaya · **09** yaş · **ARB** Arabesk Kaşif · **ADA** Adana.

> **KVKK notu:** 13 yaş altı → veli onayı zorunlu (ADR-023). Silme/saklama talebi: KVKK m. 18 · COPPA 13 · GDPR 16. **"KVKK 16" YANLIŞ** (research-bank P6.4 VERIFIED). Bu dosyada gerçek kişi verisi yoktur; tüm alanlar kurgusaldır.

### Fiziksel Özet

| Alan | Değer | Kaynak |
|------|-------|--------|
| Boy | ~1.30 m (9 yaş orta bandı) | `⚠️ VERIFICATION REQUIRED` — persentil verisi (TÜİK/WHO) doğrulanmadı, sayı yazılmadı |
| Saç | Koyu kumral, iki örgü | Kaynak: kurgusal (persona verisi) |
| Göz | Kestane | Kaynak: kurgusal (persona verisi) |
| Giyim | Çiçekli elbise + anneanne tülbent detayı | Kaynak: kurgusal (persona verisi) |
| Aksesuar | Anneannesi hediyesi minik metal anahtarlık kolye | Kaynak: kurgusal (persona verisi) |
| Duruş | Sahne paylaşma cesareti (aile meclisi) | Kaynak: kurgusal (persona verisi) |

### Big Five (OCEAN)

| Faktör | Puan (0-100) | Gerekçe (1 satır) |
|--------|--------------|-------------------|
| Dışadönüklük (O) | 65 | Aile meclisinde şarkı söylemeyi sever; utangaç değil ama sahne değil salon tercih eder |
| Uyumluluk (A) | 85 | Dedesiyle sırayla şarkı seçer, paylaşır, sıra beklemeyi öğrenmiş |
| Sorumluluk (N) | 55 | Okul görevlerini tamamlar ama öncelik keşif/eğlence |
| Duygusal Duyarlılık (N) | 40 | Arabesk dinlerken duygulanır, ağlamaklı olur ama çabuk toparlanır |
| Açıklık (E) | 70 | Aileden arabeske ek olarak yeni türleri ve çocuk keşif listelerini dener |

| Kural | Değer |
|-------|-------|
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) — research-bank P7.4 `VERIFIED` |
| Normalizasyon 0-100 | `⚠️ DERIVED` (P7.4/P7.5; persona'ya özel bilimsel ölçüm DEĞİL) |
| N ters-kodlama | ✅ uygulandı (Duygusal Duyarlılık düşük puan = dayanıklı; P7.5) |
| "IPIP-NEO Türkçe geçerli" | `⚠️ VERIFICATION REQUIRED` (P7.5 / X-9 — yazılmadı, iddia edilmedi) |

*Boyut harfleri (O/A/N/E) `⚠️ VERIFICATION REQUIRED` işaretlidir: P7.5 "kod esastır" der; şablon-taksonomi eşlemesi vault içi doğrulanmadı (elif-kaya §3.3.10 ile aynı bulgu).*

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Arabesk Kaşif |
| **İkincil küme** | — (eski dosyanın "Duygusal/Sadık"ı taxonomy'de YOK — 0 hit → taşınmadı, §7.2) |
| **Küme tanımı (alıntı)** | "Aileden öğrenilmiş arabesk + keşif" ([[mood-taxonomy]] §3.4 satır 21) |
| **Eğilim** | Yüksek A / orta N ([[mood-taxonomy]] §3.4) |
| **Müzik tanımı** | "Arabesk + çocuk keşif" (BPM yok — §3.4) |
| **Tetikleyici durumlar** | Dede/eve dönüş saatine denk gelen dinleme; yeni keşif listesi görünür olması; tekrar eden öneride sıkılma; 18+ içerik görünürse rahatsızlık (KKB ihlali) |
| **UI etkisi (test etkisi)** | "Çocuk dostu UI, aile paylaşımı, içerik filtresi" ([[mood-taxonomy]] §3.4 satır 21) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; "Sadık" gibi adlar uydurulmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Arabesk Kaşif); eski dosyadaki mood çelişkisi §7.2'de saklı |
| Test etkisi | ADR-023 + [[mood-taxonomy]] §3.4: çocuk dostu UI, aile paylaşımı, içerik filtresi |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Aileden gelen arabesk beklentisi | Ana sayfada "Ailece" / kuşak bloğu görünür | Manuel + screenshot |
| 2 | Keşif dürtüsü (Arabesk Kaşif) | Her oturumda ≥1 yeni keşif önerisi; tekrar eden öneri mikro-önerisiyle kırılır | DOM assertion |
| 3 | Çocuk dostu UI beklentisi | Dokunma hedefi ≥ **24×24 CSS px** (2.5.8); ikon ağırlıklı gezinme | A11y audit (axe) |
| 4 | İçerik filtresi | 18+ arabesk tema/şarkı filtrelenir; filtre bilgilendirmesi görünür | Manuel + DOM |
| 5 | Aile paylaşımı | "Aileme gönder" akışında veli onay adımı bekler | Manuel + form assertion |
| 6 | Kuşak dinleme (dede) | Aile modu geçişi kesintisiz; müzik durmaz | Trace + Manuel |
| 7 | Yeniden ziyaret (2. oturum) | Kuşak bloğu kalıcı; keşif listesi tazelenmiş | DOM assertion |
| 8 | Çocuk modundan çıkış | Ebeveyn kilidi devrede; "Annene sor!" dostça | Form assertion + 3.3.8 denetimi |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (sıralı)** | 1) Arabesk (birincil) 2) Çocuk keşif listesi (pop) 3) Oyun havası / halka özgü neşe | Kaynak: kurgusal (persona verisi); Arabesk = research-bank P4.2 |
| **Favori sanatçılar (TR)** | Müslüm Gürses, Ferdi Tayfur, Orhan Gencebay, İbrahim Tatlıses | `Kaynak: [k1] + [k2]` (research-bank P4.2 VERIFIED + kurgusal tercih sırası) |
| **Kibariye** | Seviyor (eski dosya) | `⚠️ VERIFICATION REQUIRED` — P4.2 listesinde YOK; sanatçı realitesi doğrulanmadı |
| **BPM (tür aralığı)** | Arabesk **60-90** (P4.4) | `Kaynak: [k1] + [k2]` (research-bank P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Kuşak dinlemesinde yavaş (60-90), keşifte canlı (100-130) | Kaynak: kurgusal (persona verisi — tercih, ölçüm değil) |
| **Dinleme saati** | Hafta içi 07:00-07:30 (okul öncesi çocuk listesi), 16:00-17:00 (dedeyle arabesk), 19:30-20:30 (aile keşfi) · Hafta sonu 11:00-12:00 (kuzen kolektif), 20:00-21:00 (aile meclisi) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (Çocuk Modu) + aile salonu hoparlörü + televizyon | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Her hafta ≥2 yeni çocuk keşif şarkısı ister; arabesk-seçkisini ailesine sorar | Kaynak: kurgusal (persona verisi) |
| **Şarkı sözü ekleme** | Eski dosyada özellik → ADR-001 kapsam dışı → basitleştirildi: aile paylaşımı notu | ADR-001 + §7.2 log |
| **Premium olasılığı** | %70 (baba "reklamsız çocuk modu" şartı koşuyor — kurgusal) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Arabesk | Anneannenin plakları + dedenin teybi; "büyüklerin duygusu" |
| 2 | Çocuk keşif (pop) | Okulda duyduğu yeni şarkıları takip eder |
| 3 | Oyun havası | Aile meclisinde herkes kalkar; neşe + hareket |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:00-07:30 | Kahvaltı & hazırlık | Çocuk keşif listesi |
| Hafta içi | 16:00-17:00 | Dedeyle dinleme (teybin önü) | Arabesk (60-90) |
| Hafta içi | 19:30-20:30 | Ödev sonrası aile keşfi | Karışık (aile oylaması) |
| Hafta içi | 20:30-21:00 | Uyku öncesi | Yavaş (sakin arabesk / ninni) |
| Hafta sonu | 11:00-12:00 | Kuzen kolektif dinleme | Çocuk keşif + oyun havası |
| Hafta sonu | 20:00-21:00 | Aile meclisi | Arabesk + oyun havası |

*Not: BPM satırları yalnız bankada bulunan tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Mood-taxonomy §3.4 müziği "Arabesk + çocuk keşif" diye tanımlar, BPM vermez — test hedefi P4.4 aralığıdır (60-90).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com) |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); 2. bağımsız kaynak DEĞİL |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | `Kaynak: [k1] + [k2]` (GSMArena + Samsung resmi — P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 100 Mbps fiber (ev, kurgusal) | Kaynak: kurgusal (persona verisi); P3.4 (hız verisi paket dışı) |
| **Tema** | light (çocuk modu renkli varyantla; arabik motif vurgulu) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi** | Hafta içi 45-60 dk/gün, hafta sonu 60-90 dk/gün (ebeveyn limiti — limit değeri kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 4/10 — ikon bazlı gezer, video izler, yazı yazmaya yeni başladı; arama çoğunlukla sesli | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Galaxy Tab A8, aile salonu hoparlörü, çocuk kulaklığı — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — arabik motif renkleri metni okunaksızlaştırmamalı | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 `VERIFIED`) |
| **İşitme** | normal | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | ince motor gelişiyor (boncuk/örgü), dokunma geniş | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürükleme zorunlu değil (2.5.7) — playlist sıralamasında dokunma alternatifi şart | Kaynak: kurgusal (persona verisi); kriter: w3.org + quickref (P5.4 `VERIFIED`) |
| **Kognitif / dikkat** | 3. sınıf okuma gelişiyor; odak 15-25 dk | Erişilebilir kimlik doğrulama min. (3.3.8) — yaş alanı toleranslı · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 `VERIFIED`) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Okuma-yazma gelişimi / dikkat süresi" ifadeleri 6698 m.6 kapsamında algı verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Büyük buton dp hedefleri (88×88 vb.) | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |
| Kullanılan kriter listesi | Yalnız P5: 1.4.3 · 1.4.11 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 · 3.3.8 — ek kod eklenmez |

### Kişilik & Davranış

- Arabesk Kaşif: dedesinin teybinin başında oturur, "bu hanginin sesi?" diye sorar; kuşak bilgisini ailesinden alır.
- Keşif dürtüsü: haftada en az 2 yeni çocuk keşif şarkısı ister; tekrar eden öneride "Anne bunu biliyoruz" der.
- Paylaşımcı: bulduğu şarkıyı hemen ailesine dinletir, "bunu babama gönder" der — paylaşım onun sosyal para birimidir.
- Görseldir: yazı okumakta yenisin → ikon, kapak görseli ve renk onun için ana bilgidir.
- Duygusaldır ama dayanıklıdır (N 40): arabesk dinlerken duygulanır, hemen toparlanır; hata ekranında "neden olmadı?" diye sorar, alternatif dener.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — Elifsu "ister", baba "reklamsız çocuk modu" ister | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 4-5 sn'de sıkılır; spinner'a ~10 sn dayanır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | "Neden olmadı ki?" der, alternatif dener; teknik metin görürse dedesine/ebeveyne yönelir — dostça dil şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 3/10 — 10. saniyede "baba, geçebilir miyiz?" | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + ezber (şarkı tekrarı) + ses; yazı yazmak zayıf yönü | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Kuzenlerle kolektif dinleme; sırayı paylaşır ama "ben buldum" gururu yaşar | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:00 sonrası odak 10 dk'ya düşer; karmaşık akış yerine gece modu/sakinleştirme bekler | Kaynak: kurgusal (persona verisi) |
| **Kontrol tetikleyicisi** | Paylaşım başarısızsa hayal kırıklığı; "aileme gönder" + anlık geri bildirim → güven | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.4 Arabesk Kaşif |

*Erişilebilirlik etkisi (özet):* 3. sınıf okuma evresi + ikon bağımlılığı → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), yaş alanında 3.3.8 tolerans — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 `VERIFIED`).

**Kısıt → test eşlemesi:**

| Kısıt | Teste yansıması | Doğrulama |
|-------|-----------------|-----------|
| 3. sınıf okuma evresi | Metin bağımlı gezinme yasak; ikon + kapak görseli birincil | Manuel + screenshot |
| İnce motor gelişimi | Sürükleme-kaydırma (2.5.7) zorunlu olmamalı; dokunma alternatifi | A11y audit (axe) |
| Odak 15-25 dk | Akış ≤3 dokunuşta hedefe ulaşır; uzun form yok | Manuel sayaç |
| Sesli arama | Mikrofon ikonu ana ekranda belirgin; çocuk sesine toleranslı sonuç | Manuel + form assertion |
| Gürültülü ev/aile ortamı | İşitsel geri bildirim görselle çiftlenir (kriter P5 dışı → ⚠️VR) | Manuel |

### AI Rol Kartı

Sen Elifsu Kaya-Arabesk'sın. 9 yaşında, Adana/Seyhan'de yaşıyorsun, Mehmet Akif İlkokulu 3. sınıf öğrencisisin.
Arabeski aileden öğrendin: dedenle teybin başında Müslüm Gürses, Ferdi Tayfur, Orhan Gencebay, İbrahim Tatlıses dinlersin; anneannenin tülbentini takarsın. Ama okulda duyduğun yeni çocuk şarkılarını da keşfetmeyi seversin — "Arabesk Kaşif"sın.
Samsung Galaxy A34 5G kullanıyorsun, light temada, evde 100 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; ince motorun gelişiyor, yazı yazmakta yenisin — ikon, kapak görseli ve sesli arama beklersin.
Sabır eşiğin 4-5 sn, spinner'a ~10 sn dayanırsın; satın alma tamamen ebeveyn onayına bağlıdır.
Test edilecek akışlar: çocuk modu ana sayfa, ailece/kuşak bloğu, arabesk tür kartı, keşif listesi, aile paylaşımı + veli onayı, içerik filtresi bilgilendirmesi, playlist sıralama, hata ekranları, oryantasyon değişimi, erişilebilirlik denetimi.
Davranış notları:
- Aynı şarkıyı çok tekrar sevmezsin (keşif) ama arabesk-seçkini değiştirmezsin (aile geleneği).
- Yeni keşif önerisini hemen "babama göndermek" istersin; paylaşım başarısızsa sorarsın ("Anne, gitmedi").
- Türkçe telaffuzun net ama çocuksudur; arayüzde "sen" dili, kısa metinler ve büyük ikonlar beklersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 9 yaş persona'sı ikon/ses odaklıdır |
| Sürpriz yasak | Arabesk Kaşif keşif ihtiyacı nedeniyle tekrarlayan/kilitli öneri döngüsü yok; her adımda geri tuşu ve "başka tür" çıkışı açık bırakılır |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.9 tablosundaki "Beklenen" sütunundan okunur |
| Sürpriz 2 | Şarkı sözü isteği çıkarsa ADR-001 ile reddet; yerine "aileme gönder" akışına yönlendir |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (çocuk modu) | LCP ≤ **2500 ms**, karşılama + ailece bloğu üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Arabesk Kaşif karşılama (mood) | Mood paleti + "Ailece" bloğu görünür; mood adı doğru yazılır | Manuel + DOM assertion |
| 3 | Arabesk tür kartı | KKB filtresi bilgilendirmesi görünür; tür açıklaması çocuk dilinde | Manuel + DOM |
| 4 | Müslüm Gürses sanatçı kartı | Doğrulanmış sanatçı verisi + kapak görseli | `Kaynak: [k1] + [k2]` P4.2 + API kontrolü |
| 5 | Kibariye araması | Eksik/uyarı durumu dostça gösterilir | `⚠️ VERIFICATION REQUIRED` (P4.2 dışı) + API kontrolü |
| 6 | Çocuk keşif listesi | Yaşa uygun içerik filtresi; ≥1 yeni öneri her oturumda | DOM assertion + içerik kontrolü |
| 7 | "Şarkıyı aileme gönder" | Paylaşım akışı tamamlanır (ADR-001: söz İSTEMEZ) | Ekran kaydı + form assertion |
| 8 | "Babama gönder" (veli onayı) | Onay adımı bekler; reddet dostça | Manuel + form assertion (ADR-023) |
| 9 | Dedeyle aile modu (kuşak) | Aile modu geçişi kesintisiz, müzik durmaz | Trace + Manuel |
| 10 | Playlist sıralama (sürükleme) | Klaz/touch alternatifi; zorunlu sürükleme yok | WCAG 2.5.7 + 2.5.8 (axe) |
| 11 | Tülbent/motif rozetli bileşen | Farkındalık yalnız renge dayanmaz | WCAG 1.4.11 + 1.4.3 (kontrast ölçümü) |
| 12 | Hızlı skip navigasyon | INP ≤ **200 ms**; skip sonrası anlık geri bildirim; geri tuşu kaybolmaz (2.4.11) | Performance trace + DOM assertion |
| 13 | Oryantasyon değişimi (yatay/dikey) | Geçiş < 400 ms; içerik kaybı yok; müzik kesintisiz | Trace + Manuel |
| 14 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 15 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.4.11 / 2.5.7 / 2.5.8 / 3.3.8 ihlali yok | Lighthouse / axe |
| 16 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Menü ve şarkı adlarında karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

**Ek doğrulama notları (test koşusundan önce — research-bank P8 `VERIFIED`):**

- **TBT:** > 50 ms ise uzun görev (long task) main thread'i bloklar; TBT, INP'nin laboratuvar proxy'sidir. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/optimize-vitals-lighthouse — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Test kırılım noktaları (P8.5 — 9 adet):** 320 × 568 · 375 × 667 · 375 × 812 · 414 × 896 · 768 × 1024 · 1280 × 720 · 1600 × 1200 · 1920 × 1080 · 2560 × 1440 — persona viewport'u (`360 × 780` / `412 × 915`, `⚠️ DERIVED` — §3.6) bu setle çapraz kontrol edilir. `Kaynak: [k1] + [k2]` (playwright.dev/docs/emulation + mintlify mobile-emulation — research-bank P8.5 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; Test Adımları tablosundaki eşikler research-bank P8'den alınır, persona'ya özel metrik hedefi bu dosyaya yazılmaz. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (9) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (13 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: ADR-023 |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları (boy dahil) | Kurgusal / doğrulanmadı | eski dosya | — | `Kaynak: kurgusal` + boy: `⚠️ VERIFICATION REQUIRED` (persentil) |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adı (Arabesk Kaşif) | Vault referanslı atama | [[mood-taxonomy]] §3.4 satır 21 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.4 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 13 | Eski mood "Duygusal/Sadık" | Vault içi çelişki (0 hit) | eski salt-okunur persona dosyası | [[mood-taxonomy]] §3 (listingde YOK) | ÇAKIŞMA → §7.2; index kazanır |
| 14 | Mood test etkisi: çocuk UI + aile paylaşımı + içerik filtresi | Test etkisi | [[mood-taxonomy]] §3.4 satır 21 | ADR-023 | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 15 | Arabesk BPM 60-90 | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 16 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 17 | Favori sanatçılar: Müslüm Gürses, Ferdi Tayfur, Orhan Gencebay, İbrahim Tatlıses | Sanatçı listesi + persona tercihi | research-bank P4.2 (bu isimler listede VAR) | eski dosya (tercih sırası) | `Kaynak: [k1] + [k2]` (P4.2 `VERIFIED` + kurgusal sıralama) |
| 18 | Kibariye sanatçısı | Doğrulanmadı (P4.2 dışı) | research-bank P4.2 (isim listede YOK) | — | `⚠️ VERIFICATION REQUIRED` |
| 19 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 20 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 21 | Diğer cihazlar (Galaxy Tab A8, hoparlör, kulaklık) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 22 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 23 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 24 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 25 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 26 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Eski dosyadaki istatistik iddiaları (RTÜK/18+ yüzdeleri, TÜİK vb.) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 30 | Şarkı sözü ekleme özelliği (eski dosya) | Özellik (kapsam dışı) | eski salt-okunur persona dosyası | ADR-001 (şarkı sözü kapsam dışı) | KAPSAM DIŞI → basitleştirildi (§7.2) |
| 31 | Eski cihaz/okul/mood değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`elifsu-kaya-arabesk.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 13 yaş altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Müslüm Gürses şarkısı 72 BPM` | Tür aralığı: `Arabesk 60-90 (P4.4)` + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| `Kibariye doğrulanmış sanatçı` | `⚠️ VERIFICATION REQUIRED` (P4.2 listesinde yok) |
| Şarkı sözü gösterme/çeviri özellikleri | `ADR-001 kapsam dışı → aile paylaşımı akışı` |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.4 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3 11 alt bölümü doldur → her satıra §3.11 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
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
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §3.11 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eski mood taşınmış | "Duygusal/Sadık" yazıyor | index'e çek; çelişkiyi §7.2'ye yaz |
| Şarkı sözü istemi | ADR-001 ihlali | Basitleştir (aile paylaşımı) + §7.2 log |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift `{{` | §4 bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Elifsu için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (7 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 3 sütun (Adım / Beklenen / Doğrulama yöntemi) — bu dosyada 16
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 16 yaş altı: `veli_onayı_gerekli: true` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok
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
| Kayıt | `log.md` append-only (bu oturumda raporla iletildi — dosyaya yazılmadı) |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.4 satır 21 Arabesk Kaşif) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | 📋 planlanan — `.ai/.personas/test-scenarios-mapping.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |
| `ADR-001` | Şarkı sözü özellikleri kapsam dışı sınırı | ✅ frozen ADR (okundu, değiştirilmedi) |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 9 | 9 | Çelişki yok (index ile uyumlu) |
| Mood | Duygusal/Sadık | Arabesk Kaşif | [[personas/index]] §6.3 kazanır; "Sadık" [[mood-taxonomy]] §3'te YOK (0 hit) → taşınmadı |
| Şarkı sözü ekleme | Özellik olarak mevcut | Kapsam dışı (ADR-001) | Basitleştirildi → aile paylaşımı akışı |
| Kibariye | Sevilen sanatçı | P4.2'de yok | `⚠️ VERIFICATION REQUIRED` etiketi |
| Birincil cihaz | Galaxy Tab A8 (EXCLUDED) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[research-bank]] P3 |
| Etki alanındaki istatistikler (RTÜK, 18+ yüzdeleri, TÜİK) | Kaynaksız yüzdeler | yazılmadı | §4.1 kural 4 (bankada yok → yazma) |
| BPM iddiaları | Parçaya özgü olabilir | Yalnız tür aralığı 60-90 | EXCLUDED X-2 → P4.4 |
| Okul / sınıf | Mehmet Akif İlkokulu, 3. sınıf | 9 yaş (index) | Tutarlı; sınıf eski veriden kurgusal taşındı |
| Kardeş/kuzen detayları | Kuzen kolektif dinleme | — | Kurgusal taşındı; çelişki yok |

*Not: Eski dosyadaki hiçbir doğrulanmamış istatistik (RTÜK/WHO/TÜİK yüzdeleri, persentil, gelir) bu dosyaya TAŞINMADI; ilgili alanlar §3.11'de `⚠️ VERIFICATION REQUIRED` satırı olarak kayıtlıdır (§4.1 kural 4).*

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı, mood index §6.3'e göre Arabesk Kaşif'e çekildi, ADR-001 kapsam dışı şarkı sözü özelliği basitleştirildi; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/elifsu-kaya-arabesk
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
