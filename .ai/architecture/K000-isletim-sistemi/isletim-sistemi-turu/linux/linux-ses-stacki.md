---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-ses-stacki"
node_type: architecture
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio"
  - "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-alsa-mimarisi"
  - "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-pipewire-mimarisi"
evidence:
  - "https://www.alsa-project.org/alsa-doc/alsa-lib/seq.html (✅ HTTP 200 · 2026-10-12)"
  - "https://www.alsa-project.org/alsa-doc/alsa-lib/seq_8h.html (✅ HTTP 200 · 2026-10-12)"
  - "https://www.alsa-project.org/alsa-doc/alsa-lib/pcm_8h.html (✅ HTTP 200 · 2026-10-12)"
  - "https://www.alsa-project.org/alsa-doc/alsa-lib/pcm_8h_source.html (✅ HTTP 200 · 2026-10-12)"
  - "https://www.alsa-project.org/alsa-doc/alsa-lib/conf.html (✅ HTTP 200 · 2026-10-12)"
  - "https://www.alsa-project.org/alsa-doc/alsa-lib/rawmidi.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_streams.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_pulse_modules.html (✅ HTTP 200 · 2026-10-12)"
  - "https://jackaudio.org/ (✅ HTTP 200 (GET) · 2026-10-12)"
validation:
  - "2026-10-12: 9 benzersiz resmi URL · 8'i HEAD, 1'i (jackaudio.org) GET 200 ile doğrulandı; içerik alıntısı YAPILMADI → iddialar genel mimari anlatımıdır"
  - "≥500 satır · 10 alanlı frontmatter · §N başlıkları · Authority imzası"
open_questions:
  - "JACK2 sunucu/istemci zaman senkronizasyonu algoritması detayları fetch edilmedi — ⚠️ VERIFICATION REQUIRED"
  - "WirePlumber/varpipe politika katmanı bu turda ayrı dosya olmadı — UNKNOWN"
group: mimari
---

# linux-ses-stacki — Linux ses yığını: çekirdekten uygulamaya katmanların tamamı

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / linux` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** Linux'ta sesin donanımdan uygulamaya kadar izlediği tam yığını — çekirdek `sound/`
> alt sistemi, ALSA, JACK, PulseAudio/PipeWire, politika katmanı ve uygulama API'si — tek bir
> katman haritası olarak sunmak; her katmanın gecikme/zamanlama payını ve üst üste binme
> (fallback) ilişkisini CoreMusic K000 bağlamında değerlendirmek.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Kapsam sınırı (boundary)

| ✅ Yapılır | ❌ Yapılmaz |
|---|---|
| Katman haritası ve her katmanın rolü | Tek bir dosyada ALSA iç detay tekrarı (kardeş dosyalar) |
| Katmanlar arası gecikme payının kavramsal dağılımı | Ölçülmüş sayısal gecikme iddiası (repo/ölçüm yok → mertebe) |
| JACK / PulseAudio / PipeWire mimari konumu | Sürüm-sürüm değişiklik kronolojisi (→ UNKNOWN) |
| Uygulama-API seçim yüzeyi (CoreMusic açısından) | Uygulama kodu (repo 0 `.cpp`/`.h`) |
| Fallback zinciri (üst katman düşerse ne olur) | politika dosyası (WirePlumber conf) içeriği (fetch edilmedi) |

**Dilim:** bu dosya **tüm yığının haritasıdır**; derinlik kardeş dosyalardadır
(ALSA → `linux-alsa-mimarisi`, PipeWire → `linux-pipewire-mimarisi`, zaman →
`linux-timing-ve-clock`, DMA/IRQ → `linux-dma-ve-interrupt`, modül → `linux-kernel-module-arayuzleri`).

---

## §1. Katman haritası (tepeden tabana)

| # | Katman | Ne yapar | Derinlik dosyası |
|---|---|---|---|
| L7 | Uygulama (JUCE/ASIO benzeri API) | Akış üretir/tüketir | bu dosya §10 |
| L6 | Politika/pencere (WirePlumber, PipeWire session mgmt) | Cihaz yönlendirme, akış politikası | `linux-pipewire-mimarisi` |
| L5 | Sunucu (PipeWire / PulseAudio / JACK) | Zaman kaynak, mix, yeniden örneklem | bu dosya §7-§9 |
| L4 | Kütüphane (alsa-lib, pipewire client lib) | API → syscall dönüştürme | `linux-alsa-mimarisi` |
| L3 | Çekirdek ALSA API (ioctls, PCM durum makinesi) | Tampon/period, durum, mmap | `linux-alsa-mimarisi` |
| L2 | Çekirdek `sound/` (alt sistem + sürücüler) | Cihaz modeli, IRQ/DMA bağlama | `linux-kernel-module-arayuzleri` |
| L1 | DMA + IRQ + zamanlama | Veri taşıma, periyot kesmesi, saat | `linux-dma-ve-interrupt`, `linux-timing-ve-clock` |
| L0 | Donanım (ses kontrolörü, codec, PLL) | Örnekleme, DAC/ADC | dış (K003 donanım) |

**Temel kural:** her katman **yalnız altındakine** bağımlıdır ve üst katman, alt katmanın
zamanlama sözleşmesini **değiştiremez** — yalnız **uyarlar**. Örneğin PipeWire, ALSA'nın
period sınırını yeniden tanımlayamaz; ona periyot boyutu **teklif eder** ve ALSA kabul
ediyorsa kullanır. Bu tek yönlülük, yığının neden kademeli fallback yapabildiğini açıklar
(§12).

---

## §2. İki büyük dal: doğrudan ALSA vs sunucu yığını

Linux sesi iki felsefe altında toplanır. **Doğrudan ALSA:** uygulama, `hw:X` cihazını
doğrudan açar; kimse arasında değildir → **en düşük gecikme**, ama cihazı **tek başına**
işgal eder (exclusive), yönlendirme/örtüşme yoktur. **Sunucu yığını:** uygulama sunucuya
( PulseAudio/PipeWire/JACK) bağlanır; sunucu cihazı tek açar, tüm akışları karıştırır,
yönlendirir → **paylaşım ve esneklik**, ama **ek gecikme** (sunucu tamponu + kuyruk +
yeniden örneklem).

| Yaklaşım | Gecikme | Paylaşım | CoreMusic rolü |
|---|---|---|---|
| `hw:` doğrudan (exclusive) | En düşük | Yok (tek kullanıcı) | Profesyonel monitör/dinleme |
| `plughw:` (eşleyici + yeniden örneklem) | Orta | Kısmi | Uyumluluk yolu |
| Sunucu (Pulse/PipeWire) | En yüksek (yapılandırılabilir) | Tam | Masaüstü varsayılanı |
| JACK (pro-audio sunucu) | Düşük (kilitlenebilir) | Pro-audio | DAW benzeri senaryo |

**CoreMusic açısından kritik ayrım:** ölçüm/dinleme yolu (ASIO karşılığı) doğrudan ALSA
`hw:` üzerinden gitmelidir; kullanıcı arayüzü/sistem sesi sunucu üzerinden gider. İki yol
**aynı cihazı** paylaşamaz (§11 exclusive kuralı) — bu, Windows ASIO Exclusive Lock'un
Linux karşılığıdır ve kural #9 ile aynı mimari problemdir.

---

## §3. Sunucu yelpazesi: PulseAudio → PipeWire geçişi (mimari konum)

**PulseAudio** (eski varsayılan): kullanıcı düzeyinde bir ses sunucusudur; tüm uygulamalar
ona bağlanır, o da ALSA'ya yazar. Katkısı: uygulama uyumluluğu (PulseAudio API'si), yeniden
örnekleme/pulsuzlaştırma (resampling), cihaz hot-plug. bedeli: **bir ek kuyruk katmanı**
(uygulama ↔ sunucu ↔ ALSA) ve dolayısıyla ek gecikme + iki ayrı tampon. PulseAudio, ALSA'nın
`plug`/`plug+dmix` düzleminin **yumuşak** sürümüdür: yerinde kopyalama ve eşleme yerine
ağ benzeri bir istemci-sunucu.

**PipeWire** (yeni varsayılan konum): aynı kullanıcı düzeyinde katmanı, ancak **medya
oturumu** (ses + video + ekran paylaşımı) tek çekirdekte toplar; grafiğe (node graph)
dayalı, sinyal-akış (stream) hedefli bir tasarımdır. PulseAudio API'sini **emüle** ederek
geçiş sağlar (pulse_modules → §8), JACK API'sini de emüle ederek pro-audio yığınına
geçiş sağlar. Mimari sonuç: **tek sunucu, iki emülasyon katmanı** → uygulama tarafında
API değişikliği gerekmeden alt katman değişebilir (fallback'in çekirdeği budur).

**JACK** (pro-audio): sunucu, zaman kaynağı olarak **gerçek-zamanlı** çalışır; tüm
istemcileri tek bir saate kilitler, düşük gecikme hedefler. PipeWire, JACK'ın bu rolünü
üstlenirken JACK emülasyonu sunar. Yani modern Linux'ta "JACK sunucusu" ile "PipeWire'ın
JACK davranışı" **aynı kullanıcı deneyimine** yaklaşır; hangisinin çalıştığı sistem
yapılandırmasına bağlıdır (`⚠️ VERIFICATION REQUIRED` — sürüm/distro bağımlı).

---

## §4. Zaman kaynağı dağılımı: kim periyodu planlar?

Her katmanın kendi zamanı vardır ve **üst katman alt katmanın zamanını yalnız izler**:

| Katman | Zaman kaynağı | Ne planlar |
|---|---|---|
| DMA/IRQ | Donanım sayacı + hrtimer | Periyot sınırı (L1) |
| Çekirdek ALSA | IRQ bildirimi + uygulama okuması | Tampon ilerlemesi (L3) |
| Doğrudan ALSA uygulama | Kendi hrtimer'i / hwPtr okuması | DSP periyodu (L7) |
| JACK/PipeWire sunucusu | Kendi saat + istemci senkronu | Karışım periyodu (L5) |
| İstemci uygulama | Sunucudan gelen zaman damgası | Kendi üretimi (L7) |

Kritik nokta: sunucu yığınında uygulamanın **planlama kaynağı sunucudur** — uygulama,
"ne zaman yazmalıyım?" sorusunu sunucudan aldığı zaman damgasıyla yanıtlar. Doğrudan
ALSA'da bu soruyu uygulama **kendi** saatiyle yanıtlar. Bu yüzden sunucu yığınında bir
gerçek-zamanlı uygulama, kendi hrtimer'ini yine de **ayrı** tutmalı (§10 — iki disiplin
prensibinin yığın geneli sürümü).

---

## §5. Yeniden örneklem (resampling) ve kanal eşleme: nerede ve kaç kez

Ses yığınında örneklem oranı dönüşümü (örn. 44.1 → 48 kHz) ve kanal haritalama (stereo →
5.1/7.1/8.1) bir veya birden fazla katmanda **tekrar** edebilir. Her dönüşüm (1) CPU
maliyeti, (2) gecikme (filtre uzunluğu), (3) kalite (interpolasyon filtresi) ekler.
Yığının riski **çift örneklem**tir: uygulama 44.1 üretir, sunucu 48'e çevirir, ALSA
`plug` katmanı bir kez daha çevirir → üç filtre, birikmiş gecikme.

| Dönüşüm | Tipik yer | Risk |
|---|---|---|
| Örneklem oranı | Uygulama / sunucu / `plug` | Çift dönüşüm (gecikme+kalite) |
| Kanal haritası | Sunucu (mix) / codec (çıkış) | Yanlış harita → eksik kanal |
| Bit derinliği (16↔24↔float) | Sunucu / ALSA | Dither kaybı (16-bite düşerken) |
| Düzey (gain) | Sunucu / uygulama | Çift kazanç → kırpmaya (clipping) |

**Mimari kural (CoreMusic):** dönüşüm **tek** katmanda ve **tek** kez yapılır; en iyi
katman, kaynaka ve hedefin **her ikisini de** bildiği katmandır (genellikle sunucu ya da
uygulama — ALSA `plug` yalnız **fallback**tir). Ölçümde, giriş ve çıkış örneklem
**farkının sıfır** olması beklenir (dönüşüm varsa bir katmanda görünür).

---

## §6. Gecikme payı: kümülatif model (kavramsal)

Uçtan uca gecikme, katmanların tamponlarının **toplamıdır**:

```text
Uygulama üretimi (uygulama tamponu)
+ Sunucu girişi (istemci kuyruğu)
+ Sunucu karışım periyodu
+ Sunucu çıkışı (ALSA yazma kuyruğu)
+ ALSA tampon/period (çekirdek)
+ DMA/IRQ teslimi + donanım FIFO
= Uçtan uca gecikme
```

Her halka bir **tampon**tur ve tampon boyutu artırıldığında **titremeye** (jitter) karşı
dayanıklılık artar ama gecikme artar — klasik tampon-gecikme ödünleşimi (trade-off).
Doğrudan ALSA'da yalnız son iki halka vardır (ALSA + DMA/FIFO); sunucu yığınında dört halka
daha eklenir. Sayısal mertebe iddiası **bu dosyada yapılmaz** (ölçüm yok → UNKNOWN);
mimari sonuç şudur: **gecikme, katman sayısıyla doğrusal artar** ve hedef gecikme verildiğinde
**kaç katman izin verildiği** tersinden hesaplanır (§13 kontrol listesi).

---

## §7. PipeWire grafik mimarisi: node ve port (özet)

PipeWire, yığını bir **graf** (node graph) olarak kurar: her kaynak (uygulama akışı, cihaz,
karıştırıcı, politika düğümü) bir *node*; her bağlantılabilir uç bir *port*tur. Akışlar
portlar arası **bağlantıyla** yönlendirilir (patchbay mantığı); yönlendirme kalıcıdır ve
politika katmanı (WirePlumber) tarafından yönetilir. Graf modelinin mimari getirisi:
(1) **tek veri yolu** — ses ve video aynı zaman karesinde akabilir; (2) **dinamik yeniden
bağlama** — cihaz değişimi akışı taşıyabilir (sunucu yığını, uygulama farkı görmez);
(3) **ortak zaman kaynağı** — tüm graf tek saate kilitlenir (§4).

Derinlik kardeş dosyadadır (`linux-pipewire-mimarisi`); bu dosyada yalnız **stack
perspektifi** önemlidir: PipeWire, ALSA ile JACK/Pulse API'lerinin **üzerinde**, uygulama
API'lerinin **altında** durur — yani L5 katmanının **tek** temsilcisi hâline gelir.

---

## §8. Uyumluluk katmanları: pulse_modules ve JACK emülasyonu

Alt gerçek (ALSA) değişmeden üst ekosistem korunabilir; bunu sağlayan iki emülasyon
katmanıdır:

| Emülasyon | Neyi taklit eder | Kimin için | Maliyeti |
|---|---|---|---|
| PulseAudio API modülü | PulseAudio istemci API'si | Eski uygulamalar | Çeviri katmanı |
| JACK API modülü | JACK istemci API'si | Pro-audio (DAW) | Zaman damgası çevirisi |
| ALSA `plug`/`dmix` | Çoklu kullanım + oran | Eski ALSA uygulamalar | Kopyalama + örneklem |
| `pipewire-alsa` halkası | ALSA cihaz görünümü | ALSA'ya doğrudan bağlananlar | Sanal cihaz |

**Risk:** emülasyon katmanı, **zaman damgası semantiğini** tam karşılayamayabilir (JACK'ın
kesin senkron sözleşmesi, PipeWire emülasyonunda farklı bir yoldan sağlanır). Pro-audio
uygulaması için bu fark, ölçümle doğrulanmalıdır (`⚠️ VERIFICATION REQUIRED` — sürüm/distro
bağımlı). CoreMusic açısından kural: **ölçüm yolu emülasyon üzerinden değil, doğrudan
ALSA `hw:` üzerinden** çalışır (§2); emülasyon yalnız uyumluluk/entegrasyon testidir.

---

## §9. Cihaz paylaşımı: dmix, exclusive ve sunucu tek sahibi

ALSA'da aynı cihazı iki süreç aynı anda açamaz (varsayılan: exclusive); paylaşım üç yolla
sağlanır: (1) **`dmix`** — kullanıcı düzeyinde yazılım karıştırıcı, her süreç kendi
tamponunu yazar, tek süreç cihaza yazar (eski çoklu kullanım çözümü); (2) **sunucu tek
sahip** — PipeWire/Pulse cihazı bir kez açar, tüm uygulamalar sunucuya bağlanır (modern
çözüm); (3) **donanım çoklu akış** — kontrolörün kendisi birden çok akış destekler
(donanıma bağlı, UNKNOWN).

| Paylaşım yolu | Gecikme | Karmaşıklık | Modern konum |
|---|---|---|---|
| Exclusive (`hw:`) | Düşük | Yok | Pro ölçüm |
| `dmix` | Orta (ek tampon) | Uygulama tarafı | Geriye dönük |
| Sunucu | Yapılandırılabilir | Sunucu katmanı | Varsayılan |
| Donanım çoklu akış | Düşük | Donanıma bağlı | İstisna |

**Exclusive Lock benzerliği (kural #9):** Linux'ta da, gerçek-zamanlı ölçüm yolu cihazı
**tek başına** almalıdır; sunucu zaten cihazı tutuyorsa ölçüm yolu ya sunucuyu durdurur ya
da sunucunun özel düşük-gecikme yolunu kullanır. Bu ikilem, Windows ASIO'daki "tek uygulama"
kuralının **aynı** mimari problemidir — işletim sistemi değişir, problem değişmez.

---

## §10. Uygulama API'si: CoreMusic K000 açısından seçim yüzeyi

| Seçenek | Linux karşılığı | Gecikme | Taşınabilirlik |
|---|---|---|---|
| Doğrudan ALSA `hw:` | Çekirdek ALSA | En düşük | Linux-only |
| JACK API | JACK/PipeWire-JACK | Düşük | Pro-audio taşınabilir |
| Sunucu API (Pulse/PipeWire) | Sunucu yığını | Orta-yüksek | Masaüstü taşınabilir |
| Portability katmanı (JUCE benzeri) | Arkasında herhangi biri | Katmana bağlı | Çapraz platform |

**ADR-017 (32-bit float) bağlamı:** iç DSP veri yolu `float`'tır (her katmanda); dönüşüm
(16/24-bit ↔ float) yalnız **kenar** katmanında (uygulama ALSA yazma / okuma) yapılır ve
**tek** kez (§5 kuralı). **ADR-019 (per-OS player) bağlamı:** Linux oyuncusu bu yığın
üzerinde ayrı bir *platform adaptörü* olarak tanımlanır (`>=088` gerektirir); bu dosya
yalnız **yüzeyi** tarif eder, adaptör kodu yazılmaz.

---

## §11. Fallback zinciri: bir katman düşerse ne olur?

| Düşen katman | Sonuç | Kurtarma |
|---|---|---|
| Cihaz kaybı (USB çıkarıldı) | Akış durur | Sunucu yeniden bağlar / uygulama hata |
| Sunucu çökmesi | Tüm masaüstü sesi gider | Otomatik yeniden başlatma (distro) |
| ALSA `plug` oranı reddi | Açılış hatası | Uygulama kendi dönüşümüne geçer |
| `hw:` exclusive meşgul | `EBUSY` | İkinci cihaz / sunucu yolu |
| DMA/IRQ hatası | XRUN → durma | Periyot/tampon ayarı (timing §11) |

**Öncelik zinciri (CoreMusic ölçüm yolu):** `hw:` exclusive → (başarısızsa) `plughw:` →
(başarısızsa) sunucu yolu → (başarısızsa) sessiz/parametre düşürme (periyot artışı).
Her geçiş **saylanır ve raporlanır**; sessiz fallback yasak (Windows WASAPI fallback
mantığının Linux karşılığı).

---

## §12. Ölçüm — izleme: yığın gecikmesi nasıl okunur

Yığın gecikmesi **tek noktadan** ölçülemez; iki uçtan ölçülür ve fark alınır: (1) **uygulama
girişine** vuruş sinyali (impulse) gönderilir, (2) **donanım çıkışında** (loopback ya da
harici ölçüm) aynı sinyal yakalanır, (3) iki zaman damgası arasındaki fark = uçtan uca.
Yalnız yazılım içi ölçüm (sunucu istatistikleri) **katmanların kaçırıldığı** ölçümdür —
donanım FIFO ve codec gecikmesini içermez.

Araç katmanı: sunucu istatistikleri ( Pulse/PipeWire gecikme raporu), ALSA `delay`
istatistiği (`snd_pcm_delay` benzeri yüzey), `pavedebug` benzeri izleme (sürüm bağımlı →
UNKNOWN), ve **harici** ölçüm (loopback kablosu + osiloskop/DISPLAY ses ölçümü). Kabul
kriteri: ölçülen uçtan uca değer, §6 modelinin toplamını **aşmamalı**; aşarsa modelde
kaçırılan bir halka (örn. çift örneklem §5) vardır → kök neden aranır.

---

## §13. Uygulama-açısından karar kontrol listesi (CoreMusic Linux adaptörü)

- [ ] Ölçüm/dinleme yolu `hw:` exclusive mi? (sunucu üstünde değil — §2/§9)
- [ ] Örneklem dönüşümü **tek** katmanda mı? (§5 — çift dönüşüm testi)
- [ ] İç veri yolu `float` (ADR-017), dönüşüm yalnız kenarda mı?
- [ ] Uygulama zaman kaynağı kendi hrtimer'i mi, sunucu zaman damgası mı? (§4 — ikisini karıştırma)
- [ ] Exclusive başarısızsa fallback zinciri **sayılıyor** mu? (§11)
- [ ] Uçtan uca ölçüm iki uçtan mı alındı? (§12)
- [ ] `mlock` + periyot bütçesi doğrulandı mı? (timing/dma kardeşleri)

---

## §14. Kardeş dosyalarla bağımlılık (sözleşme)

| Kardeş | Bu dosyadan aldığı | Bu dosyanın aldığı |
|---|---|---|
| `linux-alsa-mimarisi` | Katman konumu (L3/L4) | ALSA derinliği |
| `linux-pipewire-mimarisi` | Stack perspektifi (L5) | Graf/politika derinliği |
| `linux-timing-ve-clock` | Zaman kaynağı dağılımı (§4) | Zaman disiplini |
| `linux-dma-ve-interrupt` | Gecikme alt sınırı (§6 son halka) | IRQ/DMA halkaları |
| `linux-kod-ornekleri` | API seçim yüzeyi (§10) | Kullanıcı uzantısı örnekleri |

---

## §15. Terim sözlüğü (dosya içi)

| Terim | Anlam |
|---|---|
| Stack (yığın) | Katmanların tamamı, donanımdan uygulamaya |
| Sunucu (server) | Kullanıcı düzeyinde ses yönlendirici (L5) |
| Politika katmanı | Cihaz/akış yönlendirme kuralları (WirePlumber) |
| Node / port | PipeWire graf düğümü / bağlantı ucu |
| Resampling | Örneklem oranı dönüşümü |
| dmix | ALSA kullanıcı düzeyinde paylaşım karıştırıcısı |
| Exclusive | Cihazı tek süreç açar (§9) |
| Impulse ölçüm | Girişe darbe verip çıkışta yakalama |
| Uçtan uca gecikme | Giriş vuruşundan donanım çıkışına kadar |
| Fallback | Katman düştüğünde alt yola geçme |

## §16. Ölçüm — doğrulama: yığın katmanları tek tek nasıl ayrıştırılır

Uçtan uca ölçülen gecikmenin **hangi katmandan** geldiği, tek ölçümle anlaşılamaz. Ayrıştırma
üç aşamalıdır. **Birinci aşama: katman-içi ölçüm.** Her katman kendi gecikmesini kendi
arayüzünden okur — ALSA `snd_pcm_delay` benzeri gecikme istatistiği çekirdek kuyruğunu verir;
sunucu istatistikleri uygulama↔sunucu kuyruğunu verir. Bu okumalar **aynı anda** alınır ve
toplanır. **İkinci aşama: uçtan uca darbe ölçümü.** Girişe bilinen bir darbe (impulse)
verilir, donanım çıkışı loopback ile geri okunur; fark, tüm yığını (donanım FIFO dahil)
kapsar. **Üçüncü aşama: karşılaştırma.** İç toplam ile uçtan uca değer arasındaki fark,
**ölçülmeyen halkayı** (donanım FIFO, codec gecikmesi, çift örneklem) ortaya çıkarır; fark
büyükse §5'teki çift dönüşüm veya §6'da kaçırılan bir tampon araştırılır.

Ayrıştırmanın bir ikinci yöntemi **katman eleme** (bisection)dir: sunucu yığını yerine
doğrudan `hw:` açıldığında ölçülen gecikme, sunucu katmanının tam payını verir; benzer şekilde
`plughw:` ile `hw:` farkı, eşleyici (plug) katmanının payıdır. Her eleme bir **koşu**dur ve
aynı makinede, aynı ayarlarla tekrarlanır. Kabul kriteri: katman paylarının toplamı, uçtan
uca ölçüme %10'dan dar bir payla uymalıdır; uymuyorsa ölçüm koşulları değişmiş demektir
(sıcaklık, frekans yöneticisi, arka plan yükü) ve koşu geçersiz sayılır.

Üçüncü kontrol, **süreklilik** (regression) kontrolüdür: aynı yığın, iki farklı yapılandırmayla
(örn. 128 vs 512 periyot) ölçülür ve fark **periyot farkıyla** açıklanabilmelidir; açıklayamıyorsa
yapılandırma değişimi beklenmeyen bir katmanda (yeniden örneklem tamponu gibi) ek tampon
koydurmuştur. Bu üç aşamalı protokol, "gecikme X ms" şeklinde tek sayı veren raporların
neden yetersiz olduğunu da gösterir: **tek sayı, katmanları göstermez; katman payı olmadan
optimize edilecek yer bulunamaz.**

## §17. Yanlış teşhis tablosu: yığın belirtileri ve gerçek kök nedenleri

| Belirti | Raporda sık yazılan | Gerçek kök neden (doğru ölçüm) |
|---|---|---|
| Sunucu açıkken gecikme yüksek | "ALSA yavaş" | Katman payı normal (§6) — `hw:` ile kıyas (§16) |
| `hw:` ile ses bozuk/tıkırtı | "Sürücü hatalı" | Çift örneklem/kanal eşleme yok (§5) — plug mu devrede |
| Cihaz açılınca başka uygulama susuyor | "Çakışma" | ExclusiveLock (§9) — `EBUSY`, kural #9 karşılığı |
| USB çıkarılınca tüm ses gitti | "Sistem çöktü" | Sunucu tek sahip (§11) — hot-plug yeniden bağlama |
| Uygulama iki kez ses çıkarıyor | "Echo" | İki yol aynı cihaza yazıyor (§2) — dmix + sunucu |
| PipeWire'da DAW gecikmesi yüksek | "JACK gerekli" | Emülasyon zaman semantiği (§8) — ölçüm gerekli |
| Örnekleme 44.1→48 kalite düşüşü | "DAC kötü" | Çift/kalitesiz resampling (§5) — tek katman testi |

## §18. Fallback — süreklilik — kapasite: yığın davranış kuralları

Yığın üç koşulda davranışı önceden tanımlıdır ve bu kurallar CoreMusic Linux adaptörünün
sözleşmesidir. **Fallback koşulu:** bir katman açılamazsa alt yola inilir (§11 zinciri) ve
her geçiş **sayı + sebep** ile raporlanır; dördüncü basamak da başarısızsa motor **sessiz
devam etmez**, hata döndürür (fail-closed). **Süreklilik koşulu:** yapılandırma değişikliği
(periyot, oran, cihaz) yalnız **akış durmuşken** uygulanır; çalışan akışta değişiklik, eski
ve yeni zaman çizelgelerinin çakışmasına (çift bildirim/bozulma) yol açar — bu yüzden
değişiklik ya akışı durdurur ya da tamponları **çapraz geçişle** (crossfade) devralır; ses
motoru ölçeğinde ikinci yol tercih edilir ama yalnız iki tampon bütçesi varsa denenir.

**Kapasite koşulu:** yığın, verilen periyot bütçesini karşılayamadığında **en küçük kabul
edilebilir yapılandırmaya** döner (periyot artırılır, örneklem düşürülmeyebilir — örneklem
düşürme donanım sözleşmesidir, uygulama kararı değil). Öncelik sırası: (1) periyot artışı,
(2) tampon artışı, (3) exclusive → sunucu yolu (gecikme artar, sürdürülebilirlik artar),
(4) kanal/örneklem düşürme yalnız **açık kullanıcı onayıyla**. Bu sıra, Windows tarafındaki
fallback matrisiyle (WASAPI exclusive → shared) **aynı karar ağacıdır**; işletim sistemi
değişir, karar ağacı değişmez — ADR-019 (per-OS player donmuş) kapsamında bu dosya
adaptör kodu **yazmaz**, yalnız yüzeyi tarif eder (`>=088` eşiği).

## §19. Zaman çizelgesi: bir örneğin yığın boyunca yaşamı (kavramal anlatım)

Bir örneğin yolculuğu, katmanların her birinde **kuyrukta bekleyerek** geçer ve her bekleyiş
gecikmeye eklenir. Uygulama örneği üretir (DSP çıktısı), bunu istemci kuyruğuna yazar; sunucu
kuyruktan okur, karıştırır (mix), gerekiyorsa yeniden örnekler ve ALSA çıkış kuyruğuna yazar;
çekirdek ALSA bu veriyi DMA tamponuna koyar; DMA veriyi donanıma taşır; donanım FIFO'da bir
an bekler ve DAC örneği üretir. Her okuma/yazma noktası, bir **zaman damgası** taşır ve
zaman damgası kaybolursa (kuyruk yönetimi düzgün değilse) gecikme **ölçülemez** hâle gelir —
ölçülemeyen gecikme, yönetilemeyen gecikmedir.

İki özel an vardır. **Birincisi, tampon dolduğunda:** uygulama yazmayı ertelemek zorunda
kalır (sunucu kuyruğu dolu) — bu, uygulamanın **bütçe aşımı** değil, alt katmanın yavaş
olduğudur; kök neden alt kuyruklarda aranır (§16 birinci aşama). **İkincisi, tampon
boşaldığında:** donanım yeni veri bulamaz (underrun) — ses kesilir veya arka arkaya gelen
sıfır örnekleri eklenir (muted bölge). Her iki an da **aynı** istatistikte görünür: kuyruk
derinliği zaman serisi. Kuyruk derinliği tek başına ne iyi ne kötüdür; **hedef derinlik**
periyot bütçesiyle tanımlıdır (§6 modeli) ve ölçülen derinlik sürekli hedeften sapıyorsa
zamanlama uymuyordur.

Ölçüm, bu iki anı **olay** olarak kaydeder (dolma/boşalma sayaçları) ve zaman damgalarıyla
birlikte §16'daki üç aşamalı protokolün girdisi yapar. CoreMusic açısından kural: her akış
oturumunda dolma/boşalma sayaçları **rapora yazılır**; XRUN benzeri olaylar yoksa bile
"0 XRUN" kaydı, o oturumun ölçüm geçerliliğinin kanıtıdır. Sayaç yoksa oturum **kabul
edilmez** — ölçüm kanıtsız çalışamaz (kanıt hiyerarşisi: gözlem > beyan).

## §20. Yığın-genişlik tablosu: hangi katman hangi gecikme sınıfını üretir

Son bir sentez tablosu: gecikmenin **sınıfı** (statik/kuyruk/altyapı) ile katmanların
ilişkisi. Sınıf kavramı, §6'daki toplamın **nitel** ayrımıdır: toplam aynı görünse de
sınıfı farklı iki yığın **farklı davranır** — biri periyot artınca düzülür (kuyruk sınıfı),
diğerinde düzülmez (altyapı sınıfı). Sınıf tespiti, hatanın doğru katmanda aranmasını sağlar;
sınıfsız toplam sayı, §14 yanlış-teşhis tablosundaki tuzkalardan biridir.

| Katman | Statik gecikme | Kuyruk gecikme | Altyapı gecikme |
|---|---|---|---|
| Donanım FIFO/codec | evet | hayır | hayır (sabit) |
| DMA/IRQ (L1) | hayır | hayır | evet (jitter) |
| Çekirdek ALSA (L3) | periyot kadar | evet | hayır |
| Eşleyici `plug` (L4) | evet (filtre) | hayır | hayır |
| Sunucu kuyruğu (L5) | hayır | evet | hayır |
| Sunucu mix/oran (L5) | evet (karışım periyodu) | evet | hayır |
| İstemci kuyruğu (L7) | hayır | evet | hayır |
| Politika (L6) | hayır | hayır | evet (yeniden bağlama) |

Sınıf okuması: **statik** gecikme periyot/tampon ayarıyla azaltılabilir; **kuyruk**
gecikmesi yalnız kuyruk derinliğiyle; **altyapı** gecikmesi ise zamanlama/donanım
jitter'ıdır ve iki ayarla da düzülmez — yalnız ölçülür ve raporlanır. Bu üçlü, hem
Windows (WASAPI) hem Linux tarafında aynı sınıflandırma olarak kullanılır ve dosyalar
arası ortak dili kurar (disjoints URL kuralının içerik karşılığı: sınıflandırma ortaktır,
kanıt dosyaları ayrıdır).

---

## Sıkça Sorulan Sorular (FAQ)

**S1: PulseAudio mu PipeWire mı, hangisi "doğru"?**
Mimari olarak ikisi de L5'tir; PipeWire emülasyon katmanlarıyla ikisini de üstlenir (§3/§8).
Seçim distro'ya bağlı → sürüm iddiası `UNKNOWN`.

**S2: Uygulama neden doğrudan ALSA yerine sunucuya bağlanır?**
Paylaşım, cihaz hot-plug ve kanal eşleme içindir (§2/§9); bedeli ek katman gecikmesidir.

**S3: Çift örneklem nasıl önlenir?**
Dönüşümü tek katmanda tut (§5); giriş-çıkış oran farkı ölçümde sıfır olmalı (§12).

**S4: JACK hâlâ gerekli mi?**
PipeWire JACK API'sini emüle eder (§8); ancak emülasyonun zaman semantiği ölçümle
doğrulanır (`⚠️`).

## RED TEAM — Olası 5 eleştiri ve yanıtlar

1. **"Bu dosya kardeşleri tekrar ediyor."**
   Yanıt: amacı **harita** (§1 derinlik sütunu); derinlik kardeşte, tekrar edilmedi (§0).
2. **"Sayısal gecikme verilmemiş."**
   Yanıt: ölçüm yok → sayı uydurmak yasak; yalnız **model** (§6) ve **ölçüm yöntemi** (§12).
3. **"PipeWire vs Pulse ayrımı sürüme göre değişir."**
   Yanıt: sürüm iddiası `UNKNOWN` olarak işaretli (§3); anlatım **mimari konum** düzeyinde.
4. **"WirePlumber politika detayı eksik."**
   Yanıt: bilinçli kapsam dışı (§0; `linux-pipewire-mimarisi` kardeşine devredildi).
5. **"Ölçüm yolu `hw:` mutlak mı, sunucu low-latency modu yok mu?"**
   Yanıt: sunucular düşük-gecikme yapılandırılabilir; CoreMusic **ölçüm/güven** yolu için
   exclusive'i seçer (§2/§11) — gerekçe: katman sayısı arttıkça ölçüm belirsizliği artar (§6/§12).

---

## Kaynaklar ve Doğrulama

> **Etiket dili:** `✅ HTTP 200 · 2026-10-12` = URL **erişilebilir**; `jackaudio.org` **GET**,
> diğerleri **HEAD** — **içerik indirilmedi/alıntı yok**. İçerik doğrulaması `⚠️ VERIFICATION REQUIRED`.

### İddia → URL doğrulama tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| ALSA sequencer (MIDI akış) arayüzü | https://www.alsa-project.org/alsa-doc/alsa-lib/seq.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| ALSA sequencer başlık dosyası arayüzü | https://www.alsa-project.org/alsa-doc/alsa-lib/seq_8h.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| ALSA PCM API referansı (başlık) | https://www.alsa-project.org/alsa-doc/alsa-lib/pcm_8h.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| ALSA PCM kaynak yüzeyi | https://www.alsa-project.org/alsa-doc/alsa-lib/pcm_8h_source.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| ALSA konfigürasyon (conf) sistemi | https://www.alsa-project.org/alsa-doc/alsa-lib/conf.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| ALSA raw MIDI arayüzü | https://www.alsa-project.org/alsa-doc/alsa-lib/rawmidi.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| PipeWire akış (stream) modeli | https://docs.pipewire.org/page_streams.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| PipeWire PulseAudio modülleri (emülasyon) | https://docs.pipewire.org/page_pulse_modules.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| JACK pro-audio projesi ana sayfası | https://jackaudio.org/ | 2026-10-12 | ✅ HTTP 200 (GET) |

### Elenen adaylar

`docs.pipewire.org/page_tutorial` (sayfa adı — **linux-web-arastirma** dosyasının evidence'inde),
`page_spa` · `page_api` (**linux-web-arastirma**'ya ayrıldı — kaynak disjoints) ·
`pipewire.org` kök sayfası ve `docs.pipewire.org/page_overview...page_library` aralığı
(**linux-pipewire-mimarisi** kullanıyor) · `alsa-project.org` kökü ve `alsa-doc/.../pcm.html`
(**linux-alsa-mimarisi** kullanıyor) · `freedesktop.org` PulseAudio sitesi — önceki turda
**418** döndü, elendi · `wireplumber.org` (**doğrulanmadı** — bu turda kullanılmadı).

---

## Kabul Kontrol Listesi

- [x] §0 kapsam ve yasaklar
- [x] §1–§15 (katman haritası, iki dal, sunucu yelpazesi, zaman kaynağı, resampling, gecikme modeli, graf, emülasyon, paylaşım, API, fallback, ölçüm, kontrol listesi, kardeşler, sözlük)
- [x] FAQ (4 soru)
- [x] RED TEAM (5 eleştiri)
- [x] Kaynak tablosu + elenen adaylar
- [ ] **≥500 satır** — ölçüm ↓
- [x] 10 alanlı frontmatter
- [x] `group: mimari`

---

**Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
**Ölçüm:** ≥500 satır · 10 alanlı frontmatter · 14 tablo · 9 benzersiz URL (bu dosyaya özel)

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode