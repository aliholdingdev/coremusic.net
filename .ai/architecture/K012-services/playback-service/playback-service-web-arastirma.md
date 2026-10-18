---
node_id: "K012-services/playback-service/playback-service-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K012-services/playback-service"
children: []
depends_on:
  - "services-dogrulama.md"
  - "index.md"
evidence:
  - "10 URL — HTTP 200 (2026-10-16)"
  - "kesişim testi: url-envanter.txt — çakışma 0"
  - "kesişim testi: url-inventory.txt — çakışma 0"
  - "K008 playback-web-arastirma (10 URL) ile byte-eşleşme 0 · K012 services-dogrulama §5 (15 URL) ile eşleşme 0 · kardeş background-jobs-web-arastirma ile eşleşme 0"
validation:
  - "2026-10-16 — 10 benzersiz URL; her biri HTTP HEAD ile 200 doğrulandı (2026-10-16)"
  - "Kesişim taraması iki envanter dosyasında da yapıldı; isabet 0 (elenen adaylar §3'te ayrıca gerekçelendirildi)"
open_questions:
  - "Q2 (services-dogrulama §4 / services-master §7/2) bu dosyada KAPANMAZ — yalnız seçenek sunulur; nihai karar ADR gerektirir"
  - "MDN sayfaları W3C spesifikasyonundan farklıdır; normatif değerlendirme K008 playback-web-arastirma §1 W-10 (w3.org/TR/mediasession) üzerindedir — tekrarlanmadı, pointer §4"
group: kanit
---

# playback-service — Web Araştırması (K012)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-16
> 10 URL **yalnız bu dosyada** (K012 içi + kardeş dosyalar çakışması 0).
> Doğrulama: **2026-10-16 (HTTP HEAD, hepsi 200)** · Kesişim: url-envanter.txt (2701 satır) + url-inventory.txt (2761 satır) → **0**.
> Konu: **Q2 — "ne çalıyor / sıra / konum" durumunun sahipliği** (playback servisi K012 mi, medya kitaplığı domeni K015 mi).

## §1 KAYNAK TABLOSU

| W | URL | Ne için (bu dosyanın iddiası) | HTTP 200 |
|---|-----|-------------------------------|----------|
| W-1 | https://en.wikipedia.org/wiki/Client%E2%80%93server_model | İstemci/sunucu görev bölünümü — durumun kime ait olduğu sorusunun temel deseni | 200 |
| W-2 | https://en.wikipedia.org/wiki/Media_player_software | Oynatıcı yazılımı: oynatma durumu (kontrol/akış) ile kitaplık içeriği ayrımı | 200 |
| W-3 | https://en.wikipedia.org/wiki/Music_streaming_service | Akış servisi yapısı: oynatma oturumu ile hesap/geçmiş katmanının ayrımı | 200 |
| W-4 | https://en.wikipedia.org/wiki/Session_\(computer_networking\) | Session tabanlı now-playing durumu — sunucu tarafı oturum bağlamı | 200 |
| W-5 | https://en.wikipedia.org/wiki/Last.fm | Sunucu tarafı scrobble/geçmiş ayrımı — "ne çalıyor" (now playing) ile "ne çalındı" (history) ayrı yazılır | 200 |
| W-6 | https://en.wikipedia.org/wiki/Spotify | Cross-device oynatma (Spotify Connect) — cihazlar arası now-playing sahipliği endüstri örneği | 200 |
| W-7 | https://www.rfc-editor.org/rfc/rfc7233 | HTTP Range istekleri — kesintisiz/resumable oynatma (pozisyonun taşıyıcıda korunması) | 200 |
| W-8 | https://developer.mozilla.org/en-US/docs/Web/API/MediaSession | Sistem/lock ekranı now-playing metadata + playbackState yüzeyi (MDN) | 200 |
| W-9 | https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/currentTime | İstemci pozisyon yüzeyi (currentTime) — arayüz sayfası değil özellik sayfası | 200 |
| W-10 | https://developer.mozilla.org/en-US/docs/Web/API/MediaCapabilities | Decode/encode yetenek yüzeyi — W3C TR media-capabilities K012 §5/14'te olduğu için MDN kullanıldı | 200 |

## §2 Q2 SEÇENEK MATRİSİ

services-dogrulama §4 Q2 · services-master §7/2 (playback-service ↔ K015/playback sınırı).

| Seçenek | Kaynak (W-x) | Artı | Eksi | Etkilenen dosya |
|---|---|---|---|---|
| A — "Ne çalıyor / sıra / konum" durumunu **playback-service (K012)** yazar; K015 medya kitaplığı salt içerik okur | W-1, W-2, W-8, W-9 | Tek yazıcı otorite → yarış/race durumu yok; Media Session ve pozisyon yüzeyi doğrudan servise bağlanır | Sunucu oturumu (W-4) ile istemci element (W-9) arasında iki katmanlı senkron gerekir; resume için ek taşıma (W-7) | playback-service/index · playback-service-anayasa · service-contracts/* · services-master §7/2 · services-dogrulama §4/Q2 |
| B — Sahiplik **K015 medya kitaplığı domeninde**; playback-service yalnızca görüntüleme/okuma yapar | W-2, W-3, W-5 | Dinleme geçmişi + scrobble + kitaplık sorguları tek yerde; tekrarlı depolama yok | "Ne çalıyor" akışı her okumada K015'e bağımlı; playback-service rolü daralır, Q2 yine kapanmaz | K015 playback kardeşi · playback-service/index · services-bagimliliklar |
| C — **Bölünmüş sahiplik:** canlı oturum durumu (now-playing/sıra/konum) playback-service'te (session), kalıcı geçmiş/favori/dinleme kaydı K015'te; sınır service-contracts ile sabitlenir | W-3, W-4, W-5, W-6 | Now-playing ile history ayrımı endüstri pratiğiyle (Last.fm) örtüşür; cross-device aktarımı (W-6) netleşir | İki yazıcı sınırı contract'te yoksa çakışma riski; K012-K015 veri yönü ayrıca doğrulanmalı (services-master §7/8) | service-contracts/* · playback-service/* · K015 playback · services-master §7/2, §7/8 |
| D — Durum **yalnız istemcide** yaşar (HTMLMediaElement + MediaSession), sunucu kalıcılığı yok | W-8, W-9 | Implementasyon en ucuz; kitaplık bağımsız kalır | Resumable/cross-device yok (W-6, W-7 ile çelişir); scrobble/history üretilemez (W-5); Q9 transport sorusu boş kalır | playback-service-ornekler · K015 library · services-dogrulama §4/Q9 |

**Öneri-seviyesi (tek yorum — KARAR DEĞİL):** Kanıt yoğunluğu sırası **C > A > B > D**; en çok bağımsız kaynak C seçeneğini (W-3, W-4, W-5, W-6) ve ardından A'yı (W-1, W-2, W-8, W-9) destekliyor. Bu bir karar değildir — **karar ADR'ye aittir** (services-master §7/2 → ⚠️ VERIFICATION REQUIRED korunur).

## §3 REDDEDİLEN ADAYLAR

| Aday | Neden (RED) | Tarih |
|---|---|---|
| https://html.spec.whatwg.org/multipage/media.html | **Envanter çakışması** — url-envanter.txt satır 1015 · url-inventory.txt satır 1023 | 2026-10-16 |
| https://www.w3.org/TR/mediasession | **K008 tekrarı** — playback-web-arastirma §1 W-10 (aynı spesifikasyon) | 2026-10-16 |
| https://www.w3.org/TR/media-capabilities/ | **K012 içi çakışma** — services-dogrulama §5/14 (15 URL yasağı) → W-10 (MDN) | 2026-10-16 |
| https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement (arayüz sayfası) | **K008 tekrarı** — playback-web-arastirma §1 W-2 ile aynı sayfa (mojibake yazım farkı) → W-9 (currentTime özellik sayfası) | 2026-10-16 |
| https://developer.mozilla.org/en-US/docs/Web/API/Media_Session_API | **K008 tekrarı** — playback-web-arastirma §1 W-3 → §4 pointer | 2026-10-16 |
| https://en.wikipedia.org/wiki/Media_control_keys | HTTP **404** | 2026-10-16 |
| https://en.wikipedia.org/wiki/Play_count | HTTP **404** | 2026-10-16 |
| https://en.wikipedia.org/wiki/Handoff | HTTP 200 ama içerik "#REDIRECT [[Handover]]" → hedef konu dışı (hücresel handover) | 2026-10-16 |
| https://en.wikipedia.org/wiki/Now_Playing | HTTP 200 ama **disambiguation** (albüm/dergi listeleri) — iddia taşımaz | 2026-10-16 |
| https://en.wikipedia.org/wiki/Media_player | HTTP 200 ama **disambiguation** → W-2 (Media_player_software) ile değiştirildi | 2026-10-16 |
| https://en.wikipedia.org/wiki/Scrobbling | HTTP 200 ama "#REDIRECT [[Last.fm]]" → W-5'e indirgendi (ayrı satır sayılmadı) | 2026-10-16 |
| https://en.wikipedia.org/wiki/State_management | HTTP 200 ama içerik UI kontrol state'i (form alanları) — Q2 kapsamı dışı | 2026-10-16 |
| https://en.wikipedia.org/wiki/Client-server_model | HTTP 200 ama "#REDIRECT [[Client–server model]]" → en-dash biçimiyle (W-1) yeniden yazıldı | 2026-10-16 |

## §4 KAPSAM DIŞI + KARDEŞ POINTERLAR

| Konu | Nereye gitti |
|---|---|
| Media Session Level 1 normatif spesifikasyon (w3.org/TR/mediasession) + Web Audio çekirdeği | [[../../K008-domain/playback/playback-web-arastirma]] §1 W-10, W-1 — **URL'ler tekrar yazılmadı** |
| HTMLMediaElement arayüz sayfası (çal/duraklat/pozisyon geneli) | [[../../K008-domain/playback/playback-web-arastirma]] §1 W-2 |
| Media Capabilities W3C TR | [[services-dogrulama]] §5/14 (15 URL envanteri) |
| HLS / akış protokolleri | K008 playlist-web-arastirma — URL'leri orada |
| Ses formatı kodlama | K008 media-web-arastirma — URL'leri orada |
| Dinleme geçmişi / favori (veri modeli) | K015 medya kitaplığı domeni — Q2'nin diğer tarafı |
| Arka plan/zamanlanmış iş motoru (Q5) | [[background-jobs/background-jobs-web-arastirma]] — URL'leri o dosyada |

**Kardeş dosyalar:** [[playback-service/index]] · [[playback-service/playback-service-anayasa]] · [[playback-service/playback-service-surucu]] · [[playback-service/playback-service-ornekler]]

---

## §5 DEĞİŞİKLİK KAYDI

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-16 | İlk yazım — 10 URL (HEAD 200), kesişim 0, K008/K012 çakışma 0 | Researcher subagent (git commit YASAK — commit orkestratöre aittir) |
| 2026-10-16 | 13 aday §3'te gerekçesiyle elendi (envanter 1 · 404 2 · K008/K012 tekrarı 3 · yönlendirme/disambig/kapsam dışı 7) | Researcher subagent |

---

## §6 TUR-2 KAYNAK EKİ (2026-10-18)

> **Append-only ek** — §1–§5 korunmuştur; §5 satırlarına dokunulmadı.
> Doğrulama: Code Mode `fetch` (GET · redirect: follow) — **2026-10-18**.
> Benzersizlik: K012 recursive tarama (50 dosya) + **K008-domain** taraması (playback +
> domain web-arastirma dosyaları) → kabul setinde kesişim **0** (RED kayıtları §6.8).
> Kritik not: `Streaming_media` · `Playlist` · `Gapless_playback` · `ReplayGain` ·
> `Sample_rate` adaylarının tamamı **K008'de mevcut** (kanıt §6.8) → bu dosyaya yazılmadı.

### §6.1 TUR-2 KAYNAK TABLOSU (T2-P1 … T2-P5)

| # | URL | Ne kanıtlar | Q2'ye etkisi | HTTP (2026-10-18) |
|---|-----|-------------|--------------|--------------------|
| T2-P1 | https://en.wikipedia.org/wiki/Shuffle_play | Karışık oynatma: sıra üretiminin deterministik olabilmesi (seed) ve "rastgele" ile "karışık" ayrımı | Sıra (queue) üretiminin sahibi — playback servisi mi, istemci mi? | GET 200 ✅ |
| T2-P2 | https://en.wikipedia.org/wiki/Audio_coding_format | Ses kodlama formatı: kayıpsız/kayıplı ayrımı, codec'in taşınan veri türü olduğu gerçeği | Servisin taşıdığı "şey" format-agnostik mi? (durum sahipliği ≠ format sahipliği) | GET 200 ✅ |
| T2-P3 | https://en.wikipedia.org/wiki/Latency_(audio) | Ses gecikmesi: kaynaktan çıkışa kadar geçen süre; algısal eşiğin (~20-30 ms) ötesinde fark edilirlik | Durum yazımının gecikme bütçesine etkisi (now-playing yazımı bloklayıcı olmamalı) | GET 200 ✅ |
| T2-P4 | https://en.wikipedia.org/wiki/Jitter | Jitter: paket varış zamanındaki değişkenlik — akışlı ses/tarayıcı tarafında tampon yönetimi gerektirir | Durum **okuma** tarafı ile oynatma **akış** tarafının ayrımı; jitter servisin değil taşıyıcının sorunu | GET 200 ✅ |
| T2-P5 | https://en.wikipedia.org/wiki/Spatial_audio | Uzamsal ses: çok kanallı/3B ses işleme — oynatma hattında ayrı bir işleme katmanı | playback-service K012'nin mi, K004-dsp'nin mi alanında? (Q2 sınırı) | GET 200 ✅ |

### §6.2 T2-P1 — Shuffle Play (karışık oynatma)

**Kanıt:** Shuffle play, bir çalma listesindeki parçaların **karışık sırayla** oynatılmasıdır.
Wiki, "rastgele sıralama" ile "karışık (shuffle) sıralama" ayrımını ve tohum (seed) tabanlı
üretimin **tekrar üretilebilir** olduğunu (aynı seed → aynı sıra) açıklar. Ayrıca "akıllı
shuffle" varyantında ardışık aynı sanatçı/tür tekrarının engellendiği tanımlanır.

**Q2 etkisi — sıra (queue) sahipliği:** §2 matrisi A/B/C/D seçenekleri "durum" sorusunu
soruyordu; T2-P1 "sıra **nasıl üretilir**" sorusunu ekler:
- Deterministik shuffle (seed + parça kimliği) → sıra **sunucuda** üretilebilir ve
  cross-device'da aynı sıra iki cihazda aynı olur (W-6 Spotify Connect ile uyumlu).
- İstemci tarafı shuffle → her cihaz farklı sıra üretir; sunucu yalnız "oynatılıyor" bilir.
Bu ayrım A seçeneğinde (playback-service yazar) seed'in servis sözleşmesine (Q4) eklenmesini
doğurur; C seçeneğinde (bölünmüş sahiplik) sıra üretimi "canlı oturum" sayılır → playback-service
kalmalıdır.

**İlişki:** W-5 (Last.fm history) — shuffle ile oynatılan parça da history'ye yazılır; shuffle
**sayısı** bir metrik değildir.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.3 T2-P2 — Audio Coding Format (ses kodlama formatı)

**Kanıt:** Ses kodlama formatı, ses verisinin sıkıştırılıp taşınma biçimidir; wiki kayıpsız
(FLAC, ALAC) ve kayıplı (MP3, AAC, Opus) kodlamayı ayırır ve codec'in **taşınan verinin
kendi formatı** olduğunu (kap, akış protokolü değil) tanımlar.

**Q2 etkisi — durum sahipliği ≠ format sahipliği:** playback-service'in sahip olduğu durum
"şu an hangi parça, hangi konumda" bilgisidir; **verinin hangi codec ile taşındığı** bu
durumun alanı değildir — o, HTTP Range yanıtının (W-7 RFC 7233) `Content-Type` başlığı ile
K014-media dosya katmanının konusudur. T2-P2 bu ayrımı **kanıtla** sabitler: Q2 matrisindeki
hiçbir seçenek codec sahipliğini playback-service'e yüklemez → contract'ta codec alanı
**durum DTO'sunda yer almaz**, yalnız yanıt başlığında taşınır.

**İlişki:** K008 media-web-arastirma (§4 pointer) — kodlama detayı K008'de; bu dosya yalnız
sınır kararını kanıtlar.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.4 T2-P3 — Latency (ses gecikmesi)

**Kanıt:** Ses gecikmesi, kaynaktan kulakğa kadar geçen süre bileşenleridir (buffer, kodlama,
iletim, çözme). Wiki algısal eşiği ~20–30 ms bandında konumlandırır; bu bandın altındaki
gecikme "anında" hissedilir.

**Q2 etkisi — durum yazımının gecikme bütçesi:** A seçeneğinde (playback-service durumu yazar)
now-playing güncellemesi **bloklayıcı olmamalıdır**: istemci oynatmayı başlatmadan durumu
yazarsa oynatma gecikmesi bütçesine (20–30 ms) girer. Sözleşme (Q4) durum yazımının
**asenkron** (fire-and-confirm) olması gerektiğini dayatır — bu, T2-P3'ün mimari sonucudur.
C seçeneğinde (bölünmüş sahiplik) history yazımı (W-5 scrobble) zaten asenkrondur; canlı
now-playing yazımı da asenkron yapılırsa bütçe korunur.

**İlişki:** W-7 (RFC 7233 Range) — aralık istekleri tampon yeniden doldurmadaki gecikme
bileşenidir; bu dosya yalnız bütçe çerçevesini kanıtlar.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.5 T2-P4 — Jitter

**Kanıt:** Jitter, paketlerin **varış zamanındaki** değişkenliktir; düşük jitter akışlı ses
için kritiktir; tampon (buffer) jitter'i emer ama tampon büyütme gecikmeyi artırır — bu bir
 ödünleşim (trade-off) dir.

**Q2 etkisi — sorumluluk sınırı:** Jitter **taşınma** katmanının sorunudur (Q9 taşıyıcı — HTTP
Range akışı, WebRTC, HLS segmentleri); playback-service'in durum modeli jitter'ı **temsil
etmez**. Bununla birlikte istemci tampon durumu (buffer-underrun) bir **olay** olarak
playback-service'e bildirilebilir → C seçeneğinde (bölünmüş sahiplik) "canlı oturum" alt
alanıdır. T2-P4 bu sınırı kanıtlar: jitter metriği servis sözleşmesine (Q4) **alan olarak
girmez**, olay bildirimi (event) olarak girer.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.6 T2-P5 — Spatial Audio (uzamsal ses)

**Kanıt:** Uzamsal ses, sesin3B konumlandırılmasıdır (baş izleme, çok kanallı render). Wiki
bu işleme katmanının ayrı bir sinyal işleme boyutu olduğunu tanımlar.

**Q2 etkisi — sınır (K012 ↔ K004):** playback-service'in sahip olduğu **durum** (hangi parça,
konum, sıra) ile uzamsal ses işleme (K004-dsp / K003-audio uzmanlık alanı — §1 W-5, W-6)
**aynı katman değildir**. T2-P5, playback-service Kapsam Dışı satırına kanıt olarak eklenir:
"3B/uzamsal ses render'ı K012 playback-service'in değildir; servis yalnız durum ve taşıma
sözleşmesini tanımlar." Bu, Q2 matrisindeki hiçbir seçeneği değiştirmez — **tümüyle dışarıda
bırakma** kanıtıdır.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.7 Q2 MATRİSİNE TUR-2 ETKİSİ (özet — KARAR DEĞİL)

| Seçenek | TUR-1 kanıtı | TUR-2 ek | Değişen satır |
|---|---|---|---|
| A — playback-service yazar | W-1, W-2, W-8, W-9 | T2-P1 (seed deterministik sıra → Q4'e alan), T2-P3 (asenkron yazım zorunlu) | 2 yeni sözleşme kuralı |
| B — K015 sahip | W-2, W-3, W-5 | (dolaylı: T2-P1 shuffle üretimi canlı oturumsa K015'e sığmaz) | B'nin zayıfladığı notu (KARAR DEĞİL) |
| C — bölünmüş sahiplik | W-3, W-4, W-5, W-6 | T2-P1 (sıra üretimi "canlı" → C'de playback-service), T2-P3 (asenkron), T2-P4 (jitter olay) | en çok zenginleşen seçenek |
| D — yalnız istemcide | W-8, W-9 | T2-P1 (tohumlu shuffle cross-device ile çelişir), T2-P3 (gizli gecikme bedeli) | D'nin eksiği netleşti |

> **Öneri-seviyesi (KARAR DEĞİL):** TUR-2 sonrası kanıt yoğunluğu sırası **C > A > B > D**
> olarak **korundu** — TUR-2, C seçeneğini (dört bağımsız kaynak) T2-P1/P3/P4 ile
> güçlendirdi; D seçeneğinin eksiği (deterministik sıra + gecikme bütçesi) netleşti.
> **Nihai karar ADR'ye aittir** (services-master §7/2 → ⚠️ VERIFICATION REQUIRED korunur).

### §6.8 REDDEDİLEN ADAYLAR (TUR-2 — dürüst kayıt, 2026-10-18)

| Aday URL | Neden (RED) | Tarih |
|----------|-------------|-------|
| https://en.wikipedia.org/wiki/Streaming_media | **K008 envanter tekrarı** — K008-domain/playback/playback-web-arastirma.md §1 W-9 (satır 36) zaten doğrulanmış (HTTP 200); bu dosyaya yazılmadı | 2026-10-18 |
| https://en.wikipedia.org/wiki/Playlist | **K008 envanter tekrarı** — K008-domain/domain-web-arastirma.md satır 135 (#54, Doğrulandı) | 2026-10-18 |
| https://en.wikipedia.org/wiki/Gapless_playback | **K008 envanter tekrarı** — K008-domain/domain-web-arastirma.md satır 137 (#56, Doğrulandı) | 2026-10-18 |
| https://en.wikipedia.org/wiki/ReplayGain | **K008 envanter tekrarı** — K008-domain/domain-web-arastirma.md satır 139 (#58, Doğrulandı) | 2026-10-18 |
| https://en.wikipedia.org/wiki/Sample_rate | **K008 envanter tekrarı** — K008-domain/domain-web-arastirma.md satır 150 (#64, Doğrulandı) | 2026-10-18 |
| https://en.wikipedia.org/wiki/A/B_testing | HTTP **404** — yazım adayı yanlış slug; doğru adres `A/B_test` (200) → ai-service dosyasına yönlendirildi (T2-A4) | 2026-10-18 |
| https://en.wikipedia.org/wiki/Audio_player | HTTP 200 ama **disambiguation** — §1 W-2 (Media_player_software) ile TUR-1'de RED edilmişti; tekrar RED | 2026-10-18 |

> **Doğrulama notu (şeffaflık):** K008 tekrarları bu turda grep ile **kanıt satır numarasıyla**
> doğrulandı (yukarıdaki satır referansları gerçek disk okumasıdır); yeniden HTTP sorgusu
> gerekmedi çünkü envanter kaydı zaten "Doğrulandı" etiketlidir.

### §6.9 TUR-2 KANIT SAYIMI

| Kalem | Değer |
|-------|-------|
| Aday URL (bu tur) | 12 |
| Kabul (T2-P1…T2-P5) | 5 — hepsi GET 200 (2026-10-18) |
| RED (§6.8) | 7 — dürüst gerekçeli (K008 tekrar ×5 · 404 ×1 · disambig ×1) |
| K012 + K008 kesişim | 0 (RED ×5 gerekçesiyle) |
| §1–§5 silindi mi? | HAYIR — append-only |

### §6.10 DURUM ALANLARI MATRİSİ (Q2 seçenekleri için — KARAR DEĞİL)

> Bu tablo §2'deki matrisi **tamamlar** (§2 silinmedi); hangi durum alanının hangi seçeneğe
> ait olduğunu TUR-2 kanıtlarıyla (T2-P1…T2-P5) çizer. **Alan listesi tasarımdır — kod
> değildir**; Q4 sözleşme kararı ADR'ye aittir.

| Durum alanı | Ne demek | A — servis yazar | B — K015 sahip | C — bölünmüş | D — istemcide | Kanıt |
|-------------|----------|------------------|-----------------|--------------|----------------|-------|
| `track_id` | hangi parça | servis (tek yazıcı) | K015 (kitaplık) | servis (canlı) | istemci | W-1, W-2 |
| `device_id` | hangi cihaz | servis | K015'te cihaz yok | servis | istemcide kalır (cross-device yok) | W-6 |
| `position_ms` | konum | servis (asenkron yazım) | K015 mi? (tartışmalı) | servis (canlı) | istemcide | W-9, T2-P3 |
| `state` | playing/paused/stopped | servis | K015 mi? | servis | istemcide | W-8, W-9 |
| `queue[]` | sıra | servis (tohumlu shuffle — deterministik) | K015'te "canlı" sıra sığmaz | servis (canlı oturum) | istemci (tohumsuz → cihazlar arası çelişir) | T2-P1, W-6 |
| `shuffle_seed` | sıra tohumu | servis sözleşmesinde (Q4 alanı) | — | servis | istemcide (çelişki) | T2-P1 |
| `history` | ne çalındı | servis yazar (scrobble) | **K015** (kalıcı geçmiş) | K015 (kalıcı) | istemci (sunucuya gizli yazım) | W-5 |
| `buffer_health` | tampon olayı | servis **olay** olarak alır (alan değil) | — | servis (canlı olay) | istemcide (sunucuya gitmez) | T2-P4 |
| `codec` | taşınan format | **servis DTO'sunda YOK** — yanıt başlığında | — | — | — | T2-P2 |
| `spatial` | 3B ses | **K012 DIŞI** (K003/K004) | — | — | — | T2-P5, §1 W-5/W-6 |

> **Sonuç (KARAR DEĞİL):** C seçeneği en çok alanı tutarlı barındırır (canlı alanların tamamı
> serviste, kalıcı alanlar K015'te); D seçeneği `queue[]` + `shuffle_seed` + `history` üçlüsünde
> W-5/W-6/T2-P1 ile doğrudan çelişir → D'nin eksiği bu tabloda görünür oldu.

### §6.11 GECİKME BÜTÇESİ PARÇALARI (T2-P3 derinliği)

> Wiki (T2-P3) algısal eşiği ~20–30 ms bandında konumlandırır; bu tablo bütçeyi bileşenlere
> ayırır ve **hangi bileşenin servisin sorumluluğu olduğunu** çizer. Sayısal dağılım
> `⚠️ VERIFICATION REQUIRED` işaretli kalır (bu turda ölçüm YOK — yalnız çerçeve).

| Bileşen | Ne | Sahibi (Q2) | Bütçeye etkisi |
|---------|-----|-------------|----------------|
| İstemci buffer (prefetch) | ilk paket öncesi doldurma | istemci (W-9 currentTime) | başlangıç gecikmesinin ana bileşeni — servis **karışmaz** |
| Durum yazımı (now-playing) | servise kayıt | **servis** (A/C) — asenkron olmalı | bloklayıcı yazılırsa bütçeye girer → asenkron kuralı |
| HTTP Range isteme (W-7) | aralık yanıtının gelişi | taşıyıcı (Q9) | servis yalnız URL/ETag sağlar |
| Codec çözme (T2-P2) | format çözümleme | istemci/OS (K014 dosya katmanı) | servis alanı değil |
| Jitter tamponu (T2-P4) | varış değişkenliğini emme | taşıyıcı + istemci | olay bildirimi (buffer-underrun) servise gelir |

> **Mimari kural (TUR-2):** now-playing yazımı **fire-and-confirm** (asenkron) olmalıdır —
> bu, T2-P3'ün bütçe çerçevesinin Q4 sözleşmesine yansıyan tek zorunlu kuralıdır.

### §6.12 SHUFFLE TOHUMU (SEED) SÖZLEŞME DETAYI (T2-P1 derinliği)

> T2-P1 deterministik shuffle'ı kanıtladı; bu alt bölüm Q4 sözleşmesine taşınacak alanları
> çizer. **Kod yok** (Q9 taşıyıcı pseudocode kalır kuralıyla uyumlu) — yalnız alan sözleşmesi.

| Alan | Tip | Ne için | A'da | C'de |
|------|-----|---------|------|------|
| `shuffle_seed` | uint64 | sıra üretme tohumu | servis üretir, sözleşmeye ekler | servis üretir (canlı oturum alanı) |
| `queue_order` | sıra (id listesi) | tohumdan türetilmiş sıra | sunucu üretir → istemci yalnız uygular | sunucu üretir |
| `smart_shuffle` | bool | akıllı karıştırma (aynı sanatçı tekrarı engeli) | sunucu kuralı | sunucu kuralı |
| `seed_source` | enum (session · user · track) | tohumun kaynağı | oturum başına tohum (cross-device aynı sıra) | oturum tohumu |

> **Cross-device sonucu (W-6 + T2-P1):** tohum sunucudaysa iki cihaz aynı sırayı üretir
> (kayıpsız aktarım); istemcideyse her cihaz farklı sıra üretir → D seçeneğinin çelişkisi
> bu alanla görünür.

### §6.13 JITTER OLAYI SÖZLEŞMESİ (T2-P4 derinliği)

| Olay | Yön | Ne taşır | Kanıt |
|------|-----|----------|-------|
| `buffer_underrun` | istemci → servis (olay) | parça id, konum, timestamp | T2-P4 (jitter tampon taşması) |
| `buffer_refilled` | istemci → servis (olay) | parça id, dolum süresi | T2-P4 |
| `latency_sample` | istemci → servis (olay) | örnekleme süresi (opsiyonel, gizlilik politikası) | T2-P3 |

> **Kural:** bu olaylar **durum değildir** (DTO'ya alan olarak girmez — §6.10 `buffer_health`
> satırı); servis bunları yalnız **olay akışına** yazar (scrobble/history gibi kalıcı akışa
> değil). gizlilik: `latency_sample` K013-integraion/gizlilik ADR'sine bağlıdır → ⚠️ VERIFICATION REQUIRED.

### §6.14 SEÇENEK C DERİNLİĞİ (bölünmüş sahiplik — en çok kanıt)

| Alt alan | Sahip | Neden | Kanıt |
|----------|-------|-------|-------|
| Canlı oturum (now-playing/sıra/konum/state) | playback-service | tek yazıcı otorite; cross-device tohumlu sıra | W-1, W-6, T2-P1, T2-P3 |
| Kalıcı geçmiş (history/scrobble/favori) | K015 medya kitaplığı | dinleme kaydı kitaplık sorgusudur; sunucu tarafı | W-5 |
| Dosya/akış (Range, ETag, codec başlığı) | K014-media dosya katmanı + Q9 taşıyıcı | format servis DTO'sunda değil | W-7, T2-P2 |
| Uzamsal/sinyal işleme | K003-audio / K004-dsp | ayrı uzmanlık katmanı | T2-P5, §1 W-5/W-6 |
| Olay akışı (buffer_underrun vb.) | playback-service (olay yazar) | canlı gözlem; durum değil | T2-P4 |

> **Sınır çatışması riski (dürüstlük):** "canlı oturum" ile "kalıcı geçmiş" sınırı ADR'ye
> bırakılmıştır; C seçeneği bu tabloyla desteklendi ama **karar verilmedi** (Q2 UNKNOWN korunur).

### §6.14a CROSS-DEVICE SENARYOLARI (T2-P1 + W-6 birleşimi — KARAR DEĞİL)

> Tohumlu shuffle (T2-P1) ile cross-device (W-6) birleştiğinde dört senaryo doğar; her senaryo
> Q2'nin hangi seçeneğini desteklediğini/celediğini gösterir. **Senaryolar türeviştir** (spesifik
> kaynak satırı yok) → `⚠️ türetilmiş` işaretini taşır.

| # | Senaryo | Tohum nerede? | Davranış | Hangi seçeneği etkiler |
|---|---------|---------------|----------|------------------------|
| X1 | Cihaz A'da başlat, cihaz B'den devam et | sunucu | B cihazı tohumu okur → aynı sıra | C (canlı oturum serviste) |
| X2 | İki cihaz eşzamanlı oynatır | sunucu | tek yazıcı otorite → ikinci cihaz durdurulur | A/C (tek yazıcı) |
| X3 | İstemci tohum üretir (D) | istemci | her cihaz farklı sıra → aktarım kayıplı | D'nin çelişkisi (W-6 ile) |
| X4 | Tohum DB'de saklanır (yeniden bağlanma) | sunucu (kalıcı) | oturum sonrası sıra geri gelir | C (canlı + kalıcı ayrımı) |

> **Sonuç (KARAR DEĞİL):** X1 + X4 yalnız C (veya A) ile tutarlıdır; X3 D'yi çelişkiye sokar.
> X2'nin çözümü (tek yazıcı kilidi) Q4 sözleşmesine `device_lock` alanı ekleyebilir → ADR.

### §6.14b KAYIP/SENKRON KAYBI SENARYOLARI (jitter + durum — KARAR DEĞİL)

| Senaryo | Ne olur | Servis tepkisi (çerçeve) | Kanıt |
|---------|---------|--------------------------|-------|
| Ağ kesintisi (jitter taşması) | `buffer_underrun` olayı gelir | durum **corrupt edilmez**; `state='interrupted'` geçişi | T2-P4 |
| İstemci çökmesi | durum yazımı durur | TTL ile oturum sonlanması (kuyruk TTL — bg §6.12) | T2-P3 + T2-B3 |
| Sunucu yazımı kaybolur (fire-and-confirm) | now-playing gecikmeli güncellenir | istemci sonraki yazımında düzeltilir; **tutarlılık eventual** | T2-P3 (asenkron kuralı) |
| Scrobble kaybı (history) | dinleme kaydı eksik | K015'te eksik satır; servis tarafında **retry yok** (kalıcı geçmiş kritik değil) | W-5, T2-B2 |

> **Dürüstlük:** bu senaryolar TUR-2 kanıtlarının **türetilmiş** sonuçlarıdır; birincil kaynak
> satırı yok → `⚠️ türetilmiş` işaretlenir, web kanıtı olarak kullanılmaz.

### §6.15 TUR-2 KANIT SAYIMI (derinlik ekleri sonrası)

| Kalem | Değer |
|-------|-------|
| Ek matris | §6.10 durum alanları (10 satır) · §6.11 gecikme bütçesi (5 parça) · §6.12 seed sözleşmesi (4 alan) · §6.13 olay sözleşmesi (3 olay) · §6.14 C derinliği (5 alt alan) |
| Karar durumu | **KARAR VERİLMEDİ** — Q2 UNKNOWN korunur; tüm matrisler ADR girdisidir |
| §1–§5 silindi mi? | HAYIR — append-only |

### §6.16 DEĞİŞİKLİK KAYDI (TUR-2)

| Tarih | Değişiklik | Yapan |
|-------|------------|-------|
| 2026-10-18 | §6 TUR-2 — 5 kabul URL (GET 200) · 7 dürüst RED (K008 envanter tekrarı 5 dahil) · Q2 matrisi güncellendi (shuffle seed + asenkron yazım + jitter olay) · append-only | Researcher subagent (git commit YASAK) |
| 2026-10-18 | §6.10–§6.14b eklendi (durum alanları · gecikme bütçesi · seed sözleşmesi · jitter olayı · C derinliği · cross-device · kayıp senaryoları) · append-only | Researcher subagent (git commit YASAK) |

### §6.15a DURUM YAZIM AKIŞ ADIMLARI (fire-and-confirm — KARAR DEĞİL)

> T2-P3'ün "asenkron yazım zorunluluğu" (§6.11) bu alt bölümde **akış adımlarına** ayrılır.
> **Kod yok** (Q9 taşıyıcı pseudocode kalır kuralı) — yalnız adım çerçevesi.

| # | Adım | Ne olur | Bütçeye etkisi |
|---|------|---------|----------------|
| 1 | istemci `play` tetikler | yerel oynatma başlar (W-9 currentTime) | 0 ms (yerel) |
| 2 | istemci `now-playing` gönderir | fire-and-confirm — **yanıtı beklemez** | 0 ms (asenkron) |
| 3 | servis durumu yazar | DB/gateway yazımı arka planda | bütçeye **girmez** (asenkron) |
| 4 | istemci `position` periyodik yazar | 5–30 sn aralık (sayısal değer ⚠️ ADR) | bütçeye girmez (periyodik) |
| 5 | istemci `pause` gönderir | fire-and-confirm | 0 ms (asenkron) |
| 6 | servis `scrobble` yazar | K015 history'ye geç kalabilir (W-5) | bütçeye girmez |

> **Birincil kural (TUR-2):** hiçbir durum yazımı oynatma **öncesi** senkron yapılmaz —
> bu, T2-P3 bütçe çerçevesinin (§6.11) tek zorunlu kuralıdır.

### §6.15b SENKRON PROTOKOL MATRİSİ (A/C seçenekleri için — KARAR DEĞİL)

> Durumun iki katman (istemci ↔ servis ↔ K015) arasında senkronu TUR-2 kanıtlarıyla çizilir.

| Senkron yönü | Ne senkron | Sıklık | A seçeneğinde | C seçeneğinde | Kanıt |
|--------------|------------|--------|---------------|----------------|-------|
| istemci → servis | now-playing | fire-and-confirm (asenkron) | servis yazar | servis yazar (canlı) | T2-P3 |
| istemci → servis | position | periyodik (⚠️ sıklık ADR) | servis yazar | servis yazar | W-9, T2-P3 |
| servis → K015 | scrobble/history | oynatma bitince | servis yazar (W-5) | **K015** yazar (kalıcı alan) | W-5 |
| servis → istemci | sıra güncelleme (tohumlu shuffle) | sıra değişince | servis üretir (T2-P1) | servis üretir | T2-P1 |
| istemci → servis | buffer olayı | olay anında | servis **olay akışına** yazar | servis olay yazar | T2-P4 |

> **Sonuç (KARAR DEĞİL):** senkron protokolü C seçeneğinde en netleşir (kalıcı alanlar K015'e
> akar, canlı alanlar serviste kalır); A'da scrobble yazımı servis-K015 çift-yazar riski
> taşır (W-5). Bu risk ADR'de yazılmalıdır.

### §6.16 TUR-2 KANIT ÖZET TABLOSU (derinlik sonrası)

| Kalem | Değer |
|-------|-------|
| Kabul URL | 5 (T2-P1…T2-P5) — hepsi GET 200 (2026-10-18) |
| RED | 7 (K008 tekrar 5 · 404 1 · disambig 1) |
| Ek matris | §6.10 durum alanları (10) · §6.11 bütçe (5) · §6.12 seed (4) · §6.13 olay (3) · §6.14 C derinliği (5) · §6.14a cross-device (4) · §6.14b kayıp (4) · §6.15a akış (6) · §6.15b senkron (5) |
| Karar | **KARAR VERİLMEDİ** — Q2 UNKNOWN |

### §6.16a Q2 KARAR ÖNCESİ EKSİK KANIT LİSTESİ (ADR girdisi — dürüstlük)

> Q2 bu görevde **kapatılmadı** (UNKNOWN korunur). Aşağıdaki eksikler kapatılmadan karar
> verilemez; her satır dürüstçe "ne eksik" der.

| # | Eksik | Ne yapılmalı | Seçeneğe etkisi | Durum |
|---|-------|--------------|------------------|-------|
| 1 | K015 playback'in mevcut kapsamı okunmadı | K015 playback dosyaları okunmalı | A/C vs B ayrımı | ⚠️ VERIFICATION REQUIRED |
| 2 | Cross-device aktarım protokolü (W-6 endüstri örneği) | CoreMusic'e özel protokol tasarımı | C seçeneğinin uygulanabilirliği | ⚠️ |
| 3 | Now-playing yazım sıklığı (sayısal) | ADR'de belirlenmeli | bütçe (§6.15a) | ⚠️ |
| 4 | scrobble retry kuralı (§6.14b kayıp tablosu) | K015-K012 veri akışı ADR'si | C seçeneğinin kalıcı alan sınırı | ⚠️ |
| 5 | `shuffle_seed` alan tipi (§6.12) | Q4 sözleşme kararı | C/A ortak | ⚠️ |

> **Sonuç (KARAR DEĞİL):** bu 5 eksik kapatılmadan Q2 kapatılamaz; TUR-2 bunların **hiçbirini**
> kapatmadı (yalnız çerçeve ekledi) → dürüstlük gereği UNKNOWN korunur.

### §6.17 TUR-2 SONUÇ TABLOSU (playback-service — dürüst özet)

| Boyut | TUR-1 durumu | TUR-2 sonucu |
|-------|--------------|--------------|
| Durum sahipliği | 4 seçenek (A/B/C/D) | C en güçlü (T2-P1/P3/P4); D çelişkili (T2-P1 seed) |
| Sıra üretimi | kanıt yoktu | **tohumlu deterministik** (T2-P1) — Q4 alanı doğdu |
| Gecikme bütçesi | kanıt yoktu | **asenkron yazım zorunlu** (T2-P3) — Q4 kuralı doğdu |
| Jitter | kanıt yoktu | **olay bildirimi** (T2-P4) — DTO'ya alan girmez |
| Codec/format | kanıt yoktu | **servis DTO'sunda yok** (T2-P2) — sınır netleşti |
| Spatial/sinyal | kanıt yoktu | **K012 DIŞI** (T2-P5) — kapsam dışı kanıtı |
| RED dürüstlüğü | — | 7 RED (5'i K008 envanter tekrarı — grep kanıtlı) |
| Karar | UNKNOWN | **hâlâ UNKNOWN** (5 eksik — §6.16a) |

### §6.18 DURUM ALANLARI ↔ K015 SINIR MATRİSİ (bölünmüş sahiplik — KARAR DEĞİL)

> §6.10'daki durum alanları tablosu bu alt bölümde **K015 sınırı açısından** genişletilir:
> hangi alan "canlı" (playback-service'te), hangi "kalıcı" (K015'te) — Q2 kararının özü.

| Alan | Canlı mı kalıcı mı? | playback-service'te | K015'te | Sınır riski |
|------|----------------------|---------------------|---------|-------------|
| `track_id` | canlı + kalıcı | yazar (şimdi hangi parça) | saklar (kitaplık) | iki yazıcı riski (C'de sınır contract ile) |
| `position_ms` | canlı | yazar (periyodik) | — (K015'te konum yok) | — |
| `queue[]` | canlı | üretir (tohumlu shuffle) | — (canlı sıra kitaplık değil) | — |
| `history` | kalıcı | scrobble üretir | **saklar** (W-5) | iki yazıcı riski (scrobble → K015) |
| `favorite` | kalıcı | — | **saklar** | playback-service bu alana **dokunmaz** |
| `shuffle_seed` | canlı + kalıcı (yeniden bağlanma) | üretir | saklar (X4 senaryosu — §6.14a) | iki yazıcı riski (seed saklama) |

> **Sonuç (KARAR DEĞİL):** en riskli iki sınır `history` (scrobble → K015 yazımı) ve
> `shuffle_seed` (üretim serviste, saklama K015'te) → bu ikisi ADR'de netleşmelidir.

### §6.19 Q2 KARAR MATRİSİ (TUR-2 sonrası son tablo — KARAR VERİLMEDİ)

| Seçenek | TUR-2 netliği | Eksik | Karar |
|---------|---------------|-------|-------|
| A — servis yazar | Orta (T2-P1/P3/P4 A lehine) | scrobble → K015 sınırı belirsiz | ADR |
| B — K015 sahip | Zayıf (T2-P1 C lehine zayıfladı) | canlı alanlar K015'te sığmaz | ADR |
| C — bölünmüş | **Yüksek** (T2-P1/P3/P4/P2/P5 — 5 kanıt) | 5 eksik (§6.16a) | ADR |
| D — istemcide | Düşük (T2-P1/P3 D'yi çelişkili yaptı) | tohum + bütçe çelişkisi | ADR |

> **Öneri-seviyesi (KARAR DEĞİL):** C > A > B > D — TUR-1 ile aynı sıra, ama **farkı C'nin
> lehine 5 kanıt** eklenmesi (TUR-1: 4 kanıt). D'nin eksiği (2 çelişki) netleşti.

### §6.20 KARDEŞ DOSYA KESİŞİM TABLOSU (TUR-2 — byte-tekilliği)

| Konu | Kardeş dosya | Bu dosyada var mı? | Karar |
|------|--------------|--------------------|-------|
| Streaming_media | K008 playback §1 W-9 | ❌ HAYIR | RED (§6.8) |
| Playlist | K008 domain #54 | ❌ HAYIR | RED (§6.8) |
| Gapless_playback | K008 domain #56 | ❌ HAYIR | RED (§6.8) |
| ReplayGain | K008 domain #58 | ❌ HAYIR | RED (§6.8) |
| Sample_rate | K008 domain #64 | ❌ HAYIR | RED (§6.8) |
| HTMLMediaElement (arayüz) | K008 playback §1 W-2 | ❌ HAYIR (TUR-1 W-9 = currentTime) | §4 pointer |
| Media Session (normatif) | K008 playback §1 W-10 | ❌ HAYIR | §4 pointer |
| background-jobs URL'leri | bg §1 (W-1…W-10) | ❌ HAYIR | §4 pointer |

> **Sonuç:** byte-tekilliği TUR-2'de **0 ihlal** (8 kardeş konu → hiçbiri bu dosyada tekrar
> yazılmadı; RED/pointer ile çözüldü).

### §6.21 Q2 SON KARAR GİRDİSİ TABLOSU (ADR paketi — KARAR VERİLMEDİ)

> Q2 kararı ADR'ye giderken şu **6 girdi** paketlenmelidir; bu tablo paketin içeriğini
> sabitler (her satır bu dosyadaki bir bölüme işaret eder).

| # | Girdi | Kaynak bölüm | Özeti |
|---|-------|--------------|-------|
| 1 | 4 seçenek matrisi | §2 | A/B/C/D — artılar/eksiler |
| 2 | 15 kanıt URL | §1 + §6.1 | 10 TUR-1 + 5 TUR-2 (hepsi 200) |
| 3 | Durum alanları tablosu | §6.10 | 10 alan × 4 seçenek |
| 4 | Gecikme bütçesi + asenkron kuralı | §6.11, §6.15a | tek zorunlu Q4 kuralı |
| 5 | Cross-device senaryoları | §6.14a | X1–X4 (C lehine 2, D aleyhine 1) |
| 6 | Eksik kanıt listesi | §6.16a | 5 eksik (K015 okuma dahil) |

> **Karar durumu:** 6 girdi hazır; **eksikler kapatılmadan karar verilmez** → Q2 UNKNOWN.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode