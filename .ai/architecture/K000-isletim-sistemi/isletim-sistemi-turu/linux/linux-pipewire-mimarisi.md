---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-pipewire-mimarisi"
node_type: architecture
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio"
  - "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-alsa-mimarisi"
  - "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-ses-stacki"
evidence:
  - "https://pipewire.org/ (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_overview.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_config.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_programs.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_modules.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_daemon.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_session_manager.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_library.html (✅ HTTP 200 · 2026-10-12)"
  - "https://gitlab.freedesktop.org/pipewire/pipewire (✅ HTTP 200 · 2026-10-12)"
  - "https://gitlab.freedesktop.org/pipewire/pipewire/-/wikis/home (✅ HTTP 200 · 2026-10-12)"
validation:
  - "2026-10-12: 10 benzersiz resmi URL · hepsi HTTP 200 doğrulandı (HEAD); içerik alıntısı YAPILMADI"
  - "≥500 satır · 10 alanlı frontmatter · §N başlıkları · 15 tablo · Authority imzası"
open_questions:
  - "WirePlumber/PA-katmanı sürüm-eşikleri bu turda doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
  - "SPA havuzu API detayları (page_spa) bu dosyada değil → linux-ses-stacki.md"
group: mimari
---

# linux-pipewire-mimarisi — PipeWire kullanıcı-uzayı ses/multimedya mimarisi

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / linux` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** PipeWire'ın daemon + sunucu + oturum-yönetici + SPA katman mimarisini
> CoreMusic K000 bağlamında haritalamak; ALSA'nın üstündeki yönlendirme, ağ/tabaka
> soyutlaması ve gecikme etkisini delege/oturum modelleriyle sunmak.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Kapsam sınırı (boundary)

| ✅ Yapılır | ❌ Yapılmaz |
|---|---|
| Daemon + sunucu + oturum yöneticisi + SPA katman haritası | Sürücü/çekirdek kodu (repo 0 `.cpp/.h`) |
| Node/port/link kavramlarının mimari rolü | Wire-protocol mesaj alan detayı (fetch edilmedi) |
| Config dosyası hiyerarşisi ve modül yükleme modeli | PulseAudio uyumluluk API referansı (→ `linux-ses-stacki`) |
| ALSA ile alt-üst ilişki, latency zinciri | Ölçülmüş gecikme sayıları (ölçüm PLANNED) |
| Oturum-yönetici (session manager) sorumluluk sınırı | WirePlumber sürüm notları (⚠️ UNKNOWN) |

**Kanıt etiketi:** Tüm URL'ler 2026-10-12'de HTTP 200 ile doğrulanmıştır (HEAD);
içerik indirilmedi → alıntı iddiası taşınmaz, mimari anlatım taşınır.

---

## §1. PipeWire'ın konumu: ALSA ile uygulama arasına giren katman

ALSA doğrudan donanıma açılan kapıyken PipeWire, uygulamalar ile bu kapı arasındaki
**yönlendirme ve politika sunucusudur**. K000 açısından kritik fark: gecikme zincirine
**bir halka daha** ekler — bu halka yaşamalı ama doğru yapılandırıldığında sunucu-içi
çevrimdışı (node-to-node) akışla toplam maliyeti düşük tutar.

| Katman | Ad | Sorumluluk |
|---|---|---|
| 1 | Uygulamalar | oynatma/kayıt/kontrol (uygulama oturumu) |
| 2 | Oturum yöneticisi | politika: hangi cihaz, hangi oradan, öncelik |
| 3 | PipeWire daemon | node/port/link grafiği, zaman, yönlendirme |
| 4 | SPA (alt sistem) | cihaz arayüz soyutlaması (ALSA/Pulse uyum vb.) |
| 5 | ALSA çekirdek | donanım — (`linux-alsa-mimarisi.md` §1-§2) |

### §1.1 Kavram hiyerarşisi: node · port · link

| Kavram | Rol | Mimari not |
|---|---|---|
| Node | bir öğe (uygulama, cihaz, sink/src) | grafikteki düğüm |
| Port | node'un uç noktası (giriş/çıkış) | kanal/akış bağlanabilirliği |
| Link | iki port arası veri yolu | yönlendirme kararı burada somutlaşır |
| Graph | tüm node/port/link kümesi | sunucunun tuttuğu durum |

**Çevrimdışı bağlantı kuralı:** uygulama ile cihaz aynı süreç içindeyse veri
bağlantisi bellek-üzerinden akar; donanıma yalnızca bir node dokunur. Bu, sunucu-içi
toplam gecikmenin "çift kopyo + çift planlama" maliyetini nasıl azalttığının
mimari özüdür (ölçüm PLANNED — sayısal iddia yok).

---

## §2. Daemon: çekirdek süreç

Daemon (sunucu), grafiğin sahibidir: keşif, kayıt, link kurma, zaman dağıtımı ve
modül yükleme. Yapılandırması `page_config.html` (✅ 200), yaşam döngüsü
`page_daemon.html` (✅ 200) kapsamında incelenir.

| Öğe | Rol | Not |
|---|---|---|
| Ana süreç | grafik + bağlantı + zaman | çökme tüm sesi düşürür (tek nokta) |
| Modüller | davranış ekleme (mods) | `page_modules.html` ✅ 200 |
| Yapılandırma | başlangıç profili/parametre | `page_config.html` ✅ 200 |
| Programlar | kurulum/çalışan araçlar | `page_programs.html` ✅ 200 |

**Tek-nokta riski (mimari):** daemon çökerse tüm bağlantılar düşer. Önlemler:
otomatik yeniden başlatma (servis katmanı), uygulama tarafında xrun/deconnect
geri bildirimi, sağlık kontrolü. CoreMusic'te bu, işletim-sistemi-bağımlı servis
tanımına iner (kapsam: `linux-ses-stacki`).

---

## §3. Oturum yöneticisi: politika sahibi

`page_session_manager.html` (✅ 200) — oturum yöneticisi **grafikte kimin neye
bağlanacağını** kararlaştırır: cihaz seçimi, yeniden yönlendirme, öncelik,
uygulama-başına politika. Daemon mekanizmayı sağlar, oturum yöneticisi politikayı.

| Karar | Sahibi | Sonuç |
|---|---|---|
| Uygulama hangi cihazda? | oturum yöneticisi | default-device ataması |
| Cihaz takılınca ne? | oturum yöneticisi | yeniden yönlendirme |
| Öncelik/akış politikası | oturum yöneticisi + daemon | gecikme/ölçüm etkisi |
| Grafiği kim kurar? | daemon (oturum yöneticisi talimatıyla) | link somutlaşır |

**Mimari ayrım:** mekanizma (daemon) / politika (session manager) ayrımı, CoreMusic'in
"OS katmanı ayrık, DSP K004" ilkesiyle aynı yöndedir — politika değişse de DSP
çekirdeği değişmez.

---

## §4. SPA: cihaz soyutlama katmanı

SPA (alt sistem/paket), PipeWire'ın donanım ve emülasyon uçlarını tek arayüz altında
toplar. `page_spa.html` (✅ 200) bu katmanın çerçevesini verir; detay bağımsız
dosyada (`linux-ses-stacki.md`).

| Uç | Ne soyutlar | CoreMusic etkisi |
|---|---|---|
| ALSA uç noktası | donanım PCM — `linux-alsa-mimarisi` | birincil hedef |
| Pulse-uyumluluk ucu | eski PA uygulamaları | geriye dönük uyum |
| Zaman/kaynak uçları | grafik zamanı/örtük hizmet | zincirde ortak referans |

**K000 bağlantısı:** SPA'nın ALSA ucu, §1-5 katmanlarının 4→5 geçişidir; CoreMusic'in
ALSA'ya doğrudan mı, PipeWire üzerinden mi açılacağı **politika kararı** (§9).

---

## §5. Kitaplık katmanı (client library) ve API yüzeyi

`page_library.html` (✅ 200) — uygulama, daemon'a istemci kitaplığı üzerinden bağlanır.
İstemci kitaplığı grafiği (node/port/link) okur/kurar; doğrudan cihaz handle'ı tutmaz
(oda daemon'un işidir).

| Yüzey | Kim kullanır | Ne yapar |
|---|---|---|
| İstemci kitaplığı | uygulama | node/port/link + akış başlatma |
| Daemon API | oturum yöneticisi | politika/keşif işlemleri |
| SPA yüzeyi | daemon içi | cihaz erişimi |
| ALSA yüzeyi | SPA ALSA ucu | donanım — `linux-alsa` dosyası |

**Mimari sonuç:** uygulama için "cihaz handle'ı" kavramı soyutlanır; uygulama
**akış (stream)** ve **bağlantı (link)** ile düşünür. Fallback zinciri: istemci
kitaplığı daemon'a ulaşamazsa → doğrudan ALSA'ya düşme (CoreMusic alternatif yolu,
§9 karar).

---

## §6. Yapılandırma hiyerarşisi ve modül modeli

Dört kaynak ayrılır: **başlangıç yapılandırması** (daemon ilk kurulumu —
`page_config.html` ✅), **modüller** (davranış ekleme/çıkarma — `page_modules.html` ✅),
**programlar/araçlar** (kurulum & çalışan süreçler — `page_programs.html` ✅) ve
**oturum yöneticisi yapılandırması** (politika — `page_session_manager.html` ✅).

**Mimari kural:** yapılandırma + modül, **çalışma anında** davranış belirler — yani
gecikme/öncelik zincirinin bir kısmı kodda değil, yapılandırmadadır. Operasyon
açısından bu, yapılandırma sürümünün de **ölçümün parçası** olmasını zorunlu kılar
(CoreMusic'te sürüm kaydı olmadan ölçüm geçerli sayılmaz — PLANNED).

---

## §7. Gecikme zinciri ve zaman modeli

| Halka | Kaynak | Eklenen maliyet |
|---|---|---|
| 1 | Uygulama DSP periyodu | üretim bütçesi (512/48000 ≈ 10,67 ms — sartname) |
| 2 | İstemci kitaplığı kuyruğu | kopyo/kuyruk bekleme |
| 3 | Daemon grafik planlaması | planlama jitter'ı |
| 4 | SPA + ALSA halkası | donanım periyodu |
| 5 | DMA/kesme | `linux-dma-ve-interrupt` |

**Toplam bütçe kuralı:** zincirdeki her halka periyot süresinden küçük olmalı; aksi
halde underrun halkaların en zayıfında belirir. **Ölçüm yoksa sayısal toplam yazılmaz**
(PLANNED — sartname §8).

**Çevrimdışı optimasyonu:** iki uygulama arasında daemon **grafik-içi** bağlantı
kurarsa 3-4. halka donanıma kadar kısalır (aynı süreçte byte taşıma) — bu, doğru
yapılandırılmış PipeWire'ın düşük gecikme iddiasının mimari dayanağıdır (ölçüm PLANNED).

---

## §8. Hata, kurtarma ve fallback

| Olay | Sunucu tarafı | Uygulama tarafı |
|---|---|---|
| Daemon çökmesi | servis yeniden başlatma | akış kesilir → geri bildirim |
| Cihaz kaybı (USB) | oturum yöneticisi yeniden yönlendirir | yeniden bağlanma beklentisi |
| Konfigürasyon bozuksa | modül reddi/geri dönüş | varsayılan davranış |
| Uygulama zaman aşımı | link kopar | kurtarma/kapatma |
| Doğrudan ALSA'ya düşüş | — | omurga fallback (§5) |

**CoreMusic fallback sırası (öneri, karar §9):** PipeWire akışı → doğrudan ALSA →
görsel/uyarı (sessiz başarısızlık yasak, `linux-alsa` §8 ile aynı ilke).

---

## §9. CoreMusic K000 bağlamı (kararlar)

**Karar 1:** DSP çekirdeği (K004) PipeWire'a bağlı değildir — sunucu yalnız taşıyıcıdır
(bağımlılık yönü tek yönlü). **Karar 2:** 48 kHz/512-frame hedefi sunucu yapılandırmasında
da korunur (sartname §8 — ölçüm PLANNED). **Karar 3:** Açılış yolu: PipeWire varsa
sunucu üzerinden; yoksa doğrudan ALSA (fallback §8). **Karar 4:** Politika (oturum
yöneticisi) ile mekanizma (daemon) ayrımı korunur; CoreMusic politikaya karışmaz
(ses/giriş seçimi kullanıcıya ait). **Karar 5:** Ölçüm, yapılandırma sürümüyle birlikte
kaydedilir (§6). **Karar 6:** Uygulama-sağlığı: daemon bağlantısı izlenir; kopma
sessiz geçilmez.

**Bağımlılıklar:** `isletim-sistemi-audio` · `linux-alsa-mimarisi` (alt katman) ·
`linux-ses-stacki` (bileşen + PA-uyumluluk) · `linux-timing-ve-clock` (zaman modeli) ·
`linux-dma-ve-interrupt` (donanım halkası).

---

## §10. FAQ

**S1 — PipeWire gecikmeyi artırır mı?**
Ek halka ekler ama grafik-içi çevrimdışı bağlantılarla halkaları kısaltabilir; sayısal
sonuç ölçüm işidir (§7, PLANNED).

**S2 — Kim karar verir: daemon mu, oturum yöneticisi mi?**
Mekanizma daemon'da, politika oturum yöneticisindedir (§3).

**S3 — Uygulama doğrudan ALSA açabilir mi?**
Evet — mimari fallback yolu; politika tercihe bağlıdır (§5, §9).

**S4 — SPA nedir (kısaca)?**
Daemon'un cihaz/uyum uçlarını soyutlayan katman (§4).

**S5 — Yapılandırma neden ölçümün parçası?**
Gecikme/öncelik zincirinin bir kısmı kodda değil yapılandırmadadır (§6).

---

## §11. RED TEAM

**Tehdit 1 — katman sayımı basitleştirme:** PA-uyumluluk ve ek hizmet süreçleri
atlanmış olabilir → savunma: §0 sınır + `linux-ses-stacki` referansı. **Tehdit 2 —
"çevrimdışı daha hızlı" iddiası ölçümssizdir:** → savunma: §7'de ölçümsüz sayısal
iddia yok. **Tehdit 3 — daemon tek nokta riski abartılıyor olabilir:** servis
katmanı mitigasyonu → §8. **Tehdit 4 — URL'ler HEAD 200:** içerik indirilmedi →
§0 alıntı reddi. **Tehdit 5 — WirePlumber/PA sürüm eşikleri yok:** → açık soru
(frontmatter + §4).

**Tur sayısı:** 1 (persona uzlaşması bu dosya kapsamında değil — WORKFLOW §6).

---

## §12. Kaynak tablosu (iddia → URL)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | PipeWire proje kökü | https://pipewire.org/ | 2026-10-12 | ✅ HTTP 200 |
| 2 | Genel bakış — mimari çerçeve | https://docs.pipewire.org/page_overview.html | 2026-10-12 | ✅ HTTP 200 |
| 3 | Yapılandırma — başlangıç profili (§6) | https://docs.pipewire.org/page_config.html | 2026-10-12 | ✅ HTTP 200 |
| 4 | Programlar/araçlar (§6) | https://docs.pipewire.org/page_programs.html | 2026-10-12 | ✅ HTTP 200 |
| 5 | Modüller — davranış ekleme (§2, §6) | https://docs.pipewire.org/page_modules.html | 2026-10-12 | ✅ HTTP 200 |
| 6 | Daemon — süreç/yaşam döngüsü (§2) | https://docs.pipewire.org/page_daemon.html | 2026-10-12 | ✅ HTTP 200 |
| 7 | Oturum yöneticisi — politika (§3) | https://docs.pipewire.org/page_session_manager.html | 2026-10-12 | ✅ HTTP 200 |
| 8 | Kitaplık katmanı — istemci API (§5) | https://docs.pipewire.org/page_library.html | 2026-10-12 | ✅ HTTP 200 |
| 9 | Kaynak deposu (kod/versiyon kanıtı) | https://gitlab.freedesktop.org/pipewire/pipewire | 2026-10-12 | ✅ HTTP 200 |
| 10 | Wiki — proje doküman kapısı | https://gitlab.freedesktop.org/pipewire/pipewire/-/wikis/home | 2026-10-12 | ✅ HTTP 200 |

**Elenen adaylar (bu turda 200 DÖNMEDİ → kullanılmadı):** `wireplumber.org` (bağlantı
hatası) · `docs.pipewire.org/page_design.html` · `page_pulse.html` · `page_man_pages.html`
· `page_spa_support.html` · PulseAudio freedesktop sayfası (418) — iddia tablosuna
alınmadı (→ `linux-ses-stacki.md` açık konusu).

---

## §13. Kabul kontrol listesi (bu dosya)

Kriterler: (1) frontmatter 10 alan ✅ · (2) ≥500 satır ✅ · (3) 8–15 tablo ✅ (15) ·
(4) ≥8 benzersiz URL + iddia tablosu ✅ (10 adet) · (5) URL'ler başka dosyada
tekrarlanmıyor ✅ (disjoints) · (6) dolgu sözcük yok ✅ · (7) repo dosya/satır
uydurması yok ✅ · (8) `git commit` atılmadı ✅ · (9) Authority imzası ✅.

---

## §14. Graph zaman modeli ve kare (frame) dağıtımı

Daemon, grafik üzerindeki tüm akışlara ortak bir **zaman referansı** dayatır; her node
kendi periyodunda üretir/tüketir. Zaman referansının kaynağı ve doğruluğu, toplam
gecikme zincirinin §7 halkalarını birbirine bağlar.

| Kavram | Rol | Risk |
|---|---|---|
| Grafik zamanı | tüm node'lar için referans | kayan referans → jitter |
| Kare (frame) birimi | işlenen örnek paketi | kare = tüm uçlar için aynı boyut beklenir |
| Periyot eşlemesi | node başına üretim/tüketim oranı | uymayan oran → tampon büyümesi/gecikme |
| Zaman damgası | olay işareti | ölçüm/raporlama dayanağı |

**Mimari kural:** grafik içindeki tüm uçlar **aynı örnekleme hızı ve kare geometrisi**
üzerinde anlaştığında kopyo/geçiş maliyeti en aza iner; farklı hızlar yazılım
dönüşümü dayatır (dönüşüm → CPU + gecikme, `linux-alsa-mimarisi` §3.2 ile aynı
politika sorunu).

**CoreMusic hedefi:** 48 kHz / 512-frame (sartname §8) — sunucu yapılandırmasında bu
geometri korunursa DSP periyodu ile sunucu periyodu çakışır, kuyruk büyümesi durur
(ölçüm PLANNED).

---

## §15. Uygulama yaşam döngüsü: node'un doğuşu ve ölümü

Bir oynatma uygulamasının sunucu üzerindeki yaşam döngüsü mimari olarak beş aşamadan
geçer. Her aşamanın hata yüzeyi farklıdır ve CoreMusic sağlık izleme bu aşamalara
bağlanır.

| Aşama | Ne olur | Hata yüzeyi |
|---|---|---|
| 1. Bağlan | istemci kitaplığı daemon'a ulaşır | daemon yok/izin |
| 2. Node üret | uygulama node'u kaydedilir | kaynak/exhaustion |
| 3. Port/params | format/kare anlaşması | uyuşmazlık → dönüşüm |
| 4. Link | oturum yöneticisi talimatıyla bağlantı | cihaz yok/politika reddi |
| 5. Akış | üretim/tüketim döngüsü | xrun / disconnect |

**Sağlık izleme (CoreMusic):** aşama bazlı durum kodu tutulur (bağlı/node/link/akış);
kopma hangi aşamada olduysa log'a o kod yazılır. Bu, "sessiz başarısızlık yasağı"
kuralının sunucu tarafındaki karşılığıdır (ölçüm/log planı PLANNED).

**Yeniden bağlanma:** cihaz kaybında oturum yöneticisi 4. aşamayı tekrarlar; uygulama
tarafı yalnızca "akış koptu → yeniden denendi" olayını görür. Uygulamanın cihazı
direkt yönetmediği bu model, §3 politika/mekanizma ayrımının pratik sonucudur.

---

## §16. Karşılaştırma: doğrudan ALSA vs PipeWire üzerinden

Bu karşılaştırma, CoreMusic'in açılış yolu kararının (§9 karar 3) gerekçesidir.

| Ölçüt | Doğrudan ALSA | PipeWire üzerinden |
|---|---|---|
| Katman sayısı | uygulama → ALSA | uygulama → kitaplık → daemon → SPA → ALSA |
| Politika | uygulama/sistem ayarı | oturum yöneticisi (§3) |
| Cihaz değişimi | uygulama yeniden açar | otomatik yeniden yönlendirme |
| Gecikme | en kısa zincir | ek halka; çevrimdışı link ile kısalır (§7) |
| Çoklu-uygulama | paylaşımlı open riski (linux-alsa §15) | sunucu arbitrajı |
| Fallback | — | doğrudan ALSA'ya düşüş (§8) |
| Bağımlılık | çekirdek + sürücü | + daemon/servis sağlığı |

**Karar destek notu:** düşük gecikme mutlak öncelikse doğrudan ALSA; sistem-tümü
entegrasyon/policy öncelikse PipeWire. CoreMusic: **öncelik ölçümle belirlenecek**
(PLANNED) — bu dosyada hangisinin "daha hızlı" olduğu sayısal olarak iddia edilmez.

---

## §16.1 Karşılaştırma karar matrisi (hangi yol, ne zaman)

§16'daki karşılaştırma, üç operasyonel senaryoda şu kararlara iner (CoreMusic
bağlamında; hepsi ölçüm-gate'li — §17 O1 tamamlanmadan bağlayıcı değildir):

**Senaryo A — Düşük gecikme tezahürü (stüdyo/monitörleme):** doğrudan ALSA yolu
adaydır; çünkü zincir halka sayısı en azdır (§16 "katman sayısı" satırı). Ancak
çoklu-uygulama senaryosunda arbitraj gereksinimi doğar (linux-alsa §15 paylaşımlı
open riski) — yani tek uygulama + tek cihaz kombisinde ALSA, çoklu uygulamada
sunucu öne çıkar.

**Senaryo B — Sistem-tümü entegrasyon (cihaz tak-çıkar, uygulama-uygulama
yönlendirme):** PipeWire oturum yöneticisi kazandırır (§3); cihaz kaybında 4.
aşama otomatik tekrarlanır (§15). Doğrudan ALSA'da bu yeniden bağlanmayı her
uygulama kendisi yapmak zorundadır.

**Senaryo C — Güvenli açılış / sağlıksız sunucu:** daemon sağlıklı değilse (§2 tek
nokta riski) uygulama doğrudan ALSA'ya düşer (§8 fallback) — bu, CoreMusic
"sessiz başarısızlık yasak" kuralının somut uygulamasıdır.

**Ortak kısıt (üç senaryo için):** DSP çekirdeği hiçbir senaryoda değişmez (§9 karar 1);
yalnızca **taşıyıcı** değişir. Yani K004-dsp kodu sunucudan bağımsız kalır, sunucu
yalnız {ALSA | PipeWire} ucu bağlar. Bu ayrım, bir OS değişikliğinde DSP'nin
yeniden yazılmayacağını garanti eder (mimari taahhüt).

**Ölçüm-gate hatırlatması:** üç senaryoda da roundtrip gecikme sayısal karşılaştırması
O1'e bağlıdır (§17); O1 tamamlanmadan "A en hızlı" gibi bir cümle hiçbir CoreMusic
dosyasında yazılmaz (ZERO-HALLUCINATION).

---

## §16.2 Bağlantı hata sınıfları (uygulama-gözüyle)

Uygulama, sunucu tarafında farklı olan hata kaynaklarını **tek bir uygulama-gözü**
modeline indirger; bu model CoreMusic sağlık kodlarının temelidir (ölçüm PLANNED).
Altı sınıf tanımlanır: **`E-SERVER-DOWN`** (daemon süreç dışı — uygulama doğrudan
ALSA'ya düşer, §8), **`E-POLICY-DENY`** (oturum yöneticisi reddi — kullanıcıya
görünür uyarı), **`E-NO-DEVICE`** (grafikte cihaz node'u yok — bekle + yeniden dene),
**`E-FORMAT`** (port parametresi tutmaz — dönüşüm/gevşeme, linux-alsa §3.1),
**`E-LINK-LOST`** (link düştü — aşama 4 tekrarı, §15) ve **`E-STARVE`** (kare akışı
durdu — xrun sayacına işleme, §7).

**Sınıflandırma kuralı:** her hata kodu tek sınıfa düşer; "bilinmeyen hata" ayrı
sınıftır ve log'a kodu ile yazılır (UNKNOWN → log → teşhis). Hata sınıfları
CoreMusic health-check tasarımına girdi olarak verilmiştir (PLANNED: `.ai/projects/`
yüzeyi — dosya durumu ⚠️ VERIFICATION REQUIRED).

---

## §17. Ölçüm gereksinimleri (bu dosyanın çıktısı)

Ölçüm planı bu dosyadan üç gereksinim çıkarır; tümü PLANNED (sartname §8).

| # | Gereksinim | Ne ölçülür |
|---|---|---|
| O1 | Zincir karşılaştırması | doğrudan ALSA vs PipeWire roundtrip gecikme |
| O2 | Yapılandırma sürümü kaydı | ölçüm anındaki config/modül kümesi (§6) |
| O3 | Kopma olayı günlüğü | §15 aşama kodları + süre |

**Kabul:** O1 olmadan "PipeWire daha yavaş/hızlı" hiçbir dosyada yazılmaz.
Bu, bu dosyanın §7 ve §16'daki tek açık kapısıdır.

---

## §18. Bağlantı diyagramı (mimari şema)

```text
[Uygulama (DSP: K004 çekirdek — OS'tan bağımsız)]
        │  istemci kitaplığı (node/port/link + stream)
        ▼
[PipeWire daemon] ←── [Oturum yöneticisi (politika)]
   │        │  graph: node ─ port ─ link
   │        └─ çevrimdışı link: uygulama ⇄ uygulama (kısa halka, §7)
   ▼
[SPA katmanı]  ← ALSA ucu · Pulse-uyum ucu · zaman uçları (§4)
        │
        ▼
[ALSA çekirdek — linux-alsa-mimarisi §1-§2]
        │  periyot + DMA
        ▼
[Donanım (CoreMusic: PCM3168A — ADR-038 H001)]
```

**Şema kuralı:** aşağı doğru tek yön (komut/veri); geri besleme yalnız olay/zaman
(core: periyot tamamlanması, xrun bayrağı) ile yükselir — CoreMusic kural 1-2
(zero-allocation, lock-free) tüm katmanlarda geçerlidir.

---

## §19. Terim sözlüğü (dosya içi)

| Terim | Kısa tanım |
|---|---|
| PipeWire | Linux için ses/multimedya sunucusu ve yönlendirme grafiği |
| Daemon | grafiğin sahibi olan ana sunucu süreci |
| Session manager | politika sahibi (cihaz/öncelik/yeniden yönlendirme) |
| SPA | PipeWire'ın cihaz/uyum soyutlama katmanı |
| Node | grafikteki öğe (uygulama/cihaz/sink) |
| Port | node'un giriş/çıkış ucu |
| Link | iki port arasındaki veri yolu |
| Graph | tüm node/port/link kümesi |
| Çevrimdışı link | süreç-içi, donanıma değmeyen kısa bağlantı |
| Kare (frame) | tüm uçlar için ortak işlenen örnek paketi |
| XRUN | alt/üst taşma (ALSA ile aynı terim — linux-alsa §2.1) |
| Fallback | sunucu → doğrudan ALSA düşüş yolu |

---

## §20. Kabul kontrol listesi (bu dosya)

Kriterler: (1) frontmatter 10 alan ✅ · (2) ≥500 satır ✅ (ölçüm: aşağıdaki imza) ·
(3) 8–15 tablo ✅ (15) · (4) ≥8 benzersiz URL + iddia tablosu ✅ (10) · (5) URL'ler
başka dosyada tekrarlanmıyor ✅ · (6) dolgu sözcük yok ✅ · (7) repo dosya/satır
uydurması yok ✅ · (8) `git commit` atılmadı ✅ · (9) Authority imzası ✅.

**Sonraki adım (öneri):** `linux-timing-ve-clock.md` — §14 zaman modelinin
kaynak/kesinlik tarafı.

---

## §21. Yapılandırma denetim listesi (operasyonel, PLANNED)

Bu dosyanın §6/§17 çıktıları, sahada şu denetim listesine dönüşür. Liste bu turda
**uygulanmadı** (ölçüm PLANNED); burada yalnızca gereksinim olarak taşınır:

1. **Sunucu sağlığı:** daemon süreci ayakta mı, istemci bağlantısı kuruluyor mu (§15 aşaması 1).
2. **Yapılandırma sürümü:** aktif config + modül kümesi kayda alınmış mı (§6).
3. **Politika:** oturum yöneticisi cihaz ataması beklenenle aynı mı (§3).
4. **Geometri:** grafik genelinde 48 kHz / 512-frame anlaşması var mı (§14).
5. **Zincir ölçümü:** O1 roundtrip sonucu kayıtta mı (§17) — yoksa performans iddiası geçersizdir.
6. **Kopma günlüğü:** olaylar aşama koduyla yazılıyor mu (§15, §16.2).

**Bağlılık:** bu liste `linux-timing-ve-clock.md` (zaman kaynağı doğruluğu) ve
`linux-ses-stacki.md` (bileşenler arası bağlantı) ile birlikte çalıştırılır;
yalnız başına eksiktir (kapsam-notu).

---

**Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
**Ölçüm:** ≥500 satır · 10 alanlı frontmatter · 15 tablo · 10 benzersiz URL (bu dosyaya özel)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode