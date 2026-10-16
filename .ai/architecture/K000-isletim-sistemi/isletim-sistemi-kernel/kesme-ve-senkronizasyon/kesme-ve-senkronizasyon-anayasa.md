---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon/kesme-ve-senkronizasyon-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon
children: []
depends_on: []
evidence:
  - https://docs.kernel.org/core-api/genericirq.html
  - https://docs.kernel.org/locking/locktypes.html
  - https://docs.kernel.org/locking/lockdep-design.html
  - https://docs.kernel.org/locking/mutex-design.html
  - https://docs.kernel.org/locking/rt-mutex.html
  - https://man7.org/linux/man-pages/man7/futex.7.html
validation: "2026-10-13 · 6 resmi URL envanterden/kontrol edilmiş doğrulamadan (genericirq EN + RCU EN bu oturumda webfetch 200; kalanları -dogrulama.md §E envanteri) · repo'da spinlock/flock/pcntl_ kodu YOK (grep 0 isabet, kardeş dal kanıtı) · hedef çekirdek bilinmiyor → Linux örnekleri model olarak taşınır"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
  - "Ölçüm hedefi (512 örnek · 48 kHz · lock-free callback) bu çekirdekte geçerli mi? → ⚠️ VERIFICATION REQUIRED (S3)"
group: mimari
---

# kesme-ve-senkronizasyon-anayasa — Kesme Yolu ve Kilit Doktrini (Kademe-4 · §0-§3)

## §0 ELI15

Bir donanım örneğin "ses kartı veri hazır dedi" dediğinde çekirdeğin
**aniden** durması ve veriye bakması gerekir — buna **kesme** (interrupt)
denir. Aynı anda başka bir çekirdek de aynı veriye dokunuyorsa **çakışma**
riski doğar; bunu önleyen yapılar **kilitlerdir** (spinlock, mutex, rwsem).
Bu anayasa iki doktrini kurar: **kesme yolu** (kesme ne zaman gelir, kim
işler, ne yapamaz) ve **kilit doktrini** (hangi durumda hangi kilit, kilit
hiç bırakılmazsa ne olur — deadlock/starvation). Bağlayıcıdır: `-surucu`
(SMP/tutarlılık/ölçüm) ve `-ornekler` bu doktrine dayanır.

| Öğe | Değer |
|---|---|
| Dosya rolü | Anasözleşme (Kademe-4 · `group: mimari`) |
| Bölüm omurgası | §0-§3 (bu dosya) · §4-§6 (`-surucu`) · §1-§6 (`-ornekler`) |
| Bağlı dosyalar | `kesme-ve-senkronizasyon-surucu.md` · `kesme-ve-senkronizasyon-ornekler.md` · kök `index.md` §1 |
| Kapsam | IRQ/NMI/vektör · kilit türleri · deadlock/starvation |
| Kapsam dışı | Zamanlama/tick kesmesi politikası (`-plan`) · güvenlik kilit bypass (`-security`) · gözlem sayacı (`-observability`) · test (`-testing`) |

> **Hedef çekirdek bilinmiyor (Q4 = UNKNOWN).** Bu dosyadaki mekanizma
> örnekleri Linux çekirdeği üzerinden anlatılır ve **model** olarak taşınır;
> "hedef OS budur" iddiası **yazılmaz**. Çelişkide `⚠️ VERIFICATION REQUIRED`.

## §1 Kesme yolu (interrupt)

### §1.1 Kesme türleri

| Tür | Ne tetikler | Bağlam | Öncelik |
|---|---|---|---|
| donanım kesmesi (IRQ) | cihaz (ağ · ses · disk) | asenkron | hat numarasına bağlı |
| yazılım kesmesi (INT/ trap) | CPU (divide · syscall · debug) | senkron | sabit vektör |
| NMI | hata dedektörü · watchdog | asenkron, iç içe girilebilir | en yüksek |
| IPI (çekirdekler arası) | çekirdek → çekirdek | asenkron | yüksek |
| zamanlayıcı kesmesi (tick) | zamanlayıcı | periyodik | `−plan` dalı |

**Doktrin (1.1.1):** Bu dosya **yalnız** IRQ/NMI/IPI/trap yolunu işler;
zamanlayıcı kesmesinin politikası (tick oranı, EEVDF kararı) `-plan`
dalındadır — burada tekrar edilmez.

### §1.2 Kesme akışı — adımlar (doktrin)

| # | Adım | Ne olur | Kilit etkisi |
|---|---|---|---|
| 1 | cihaz sinyal verir | kesme hattı yükselir | — |
| 2 | CPU kesmeyi kabul eder | mevcut iş askıya alınır (durum korunur) | askıdaki kilit **kalır** |
| 3 | vektör bulunur | kesme numarası → işleyici adresi | — |
| 4 | işleyici çalışır | `irq_handler` kısa çalışır | kilit alınırsa **hızlı** bırakılmalı |
| 5 | EOI / sonlandırma | donanıma "tamam" denir | — |
| 6 | askıdaki iş döner | durum geri yüklenir | kilitler yerinde |

**Doktrin (1.2.1):** Kesme işleyicisi **kısa** çalışır; uzun iş yığına
(planla → softirq/tasklet/Threaded IRQ) devredilir. Nedeni: kesme sırasında
tutulan kilitler **askıdadır** ve bekleyen her çekirdek bloke olur.

### §1.3 Kesme bağlamı ile süreç bağlamı (doktrin)

| Özellik | kesme bağlamı | süreç bağlamı |
|---|---|---|
| çalışan | kesme işleyicisi | süreç/iş parçası |
| uyuma | **yok** (sleep yasak) | serbest |
| öncelik | askıdaki sürecin üstünde | normal |
| kilit | yalnız spinlock (kısa) | mutex/spinlock |
| kaynak | kesme bağlamında tahsis dikkatli | serbest |

**Doktrin (1.3.1):** Kesme bağlamında **uyumak yasaktır** — çünkü o
bağlam bir süreçe **ait değildir**; uyanmak kime ait olurdu? Bu kural
`cekirdek-yonetim` anayasa §6.19 ile aynı doktrindir (iki dosya aynı
aşağıdaki kuralı paylaşır, tekrar edilmez).

### §1.4 Kesme maskesi ve devre dışı bırakma

| Kavram | Ne demek | Ne zaman | Risk |
|---|---|---|---|
| kesme maskesi | belirli kesmeler geçici susturulur | kritik kısa bölüm | susturma uzarsa kayıp |
| devre dışı bırakma (`local_irq_disable`) | tüm kesmeler susturulur | çok kısa kritik bölüm | gecikme/JITTER |
| Eski çekirdek (UP) | global susturma | — | SMP'de yanlış |

**Doktrin (1.4.1):** Kesme susturma **en son çare** ve **en kısa** olmalıdır;
uzun susturma ses gecikmesine (jitter) yol açar. Ölçüm `-surucu` §6'nın
alanıdır; burada eşik **yazılmaz** (İlke B10).

### §1.5 IRQ vs NMI karşılaştırması

| Kriter | IRQ | NMI |
|---|---|---|
| kaynağı | donanım hattı | iç dedektör |
| tekrar girebilir mi? | aynı hat için hayır | **evet** (tekrar girişi) |
| kilit uygun mu? | spinlock (kısa) | kilit **dikkat** (tekrar giriş) |
| tipik kullanım | ağ, ses, disk | watchdog, hata |
| ölçülebilir mi? | `-observability` | `-observability` |

**Doktrin (1.5.1):** NMI, kilit alan kodun **üstünde de** çalışabilir — bu
"lockup detection"u mümkün kılar ama kilit kodunu **karmaşıklaştırır**.
NMI içinde kilit alınması yalnız NMI-dostu kilitlerle yapılır
(⚠️ VERIFICATION REQUIRED — hedef çekirdek bilinmiyor).

## §2 Kilit türleri (doktrin)

### §2.1 Kilit aileleri

| Kilit | Bekleme | Kilit altında sleep | Tipik kullanım |
|---|---|---|---|
| spinlock | döner (busy-wait) | **hayır** | kısa kritik bölüm, kesme bağlamı |
| mutex | uyur (sleep) | evet | uzun kritik bölüm, süreç bağlamı |
| rwsem (okuma-yazma) | uyur | evet | çok okuyucu / az yazıcı |
| semaphore (sayaç) | uyur | evet | kaynak sayacı |
| RCU | kilit yok (okuma çok hızlı) | — | okuma-yoğun, nadir yazma |
| futex (kullanıcı uzayı) | çekirdek yardımıyla | evet | kütüphane kilitleri |

**Doktrin (2.1.1):** "Spinlock mu, mutex mi?" sorusunun cevabı: **kritik
bölüm kısa mı, bağlam kesme mi?** Kısa + kesme → spinlock; uzun + süreç
→ mutex. Yanlış seçim ya **boşa CPU** (mutex yerine spinlock) ya da
**deadlock** (spinlock altında sleep).

### §2.2 Kilit seçimi — karar matrisi

| Durum ↓ / Kriter → | kısa bölüm | uzun bölüm | kesme bağlamı | sleep gerekiyor |
|---|---|---|---|---|
| kısa + kesme | spinlock | — | spinlock (zorunlu) | hayır |
| kısa + süreç | spinlock | mutex | — | hayır |
| uzun + süreç | — | mutex | yasak | evet |
| çok okuyucu | rwsem / RCU | rwsem / RCU | — | evet (rwsem) |
| kullanıcı uzayı | futex | futex | — | evet |

**Doktrin (2.2.1):** Matris **zorunluluk** ifade eder, öneri değil; hedef
çekirdek farklı API sunsa bile **mantık** aynıdır (kısa/spin · uzun/mutex).

### §2.3 Kilit türleri — karşılaştırma (derin)

| Kriter | spinlock | mutex | rwsem | RCU |
|---|---|---|---|---|
| bekleyen | döner | uyur | uyur | okuma beklemesi yok |
| CPU kullanımı | yüksek (dönen) | düşük | düşük | okuma çok düşük |
| öncelik tersine çevrilmesi | hassas (deadlock riski) | rt-mutex çözer | hassas | — |
| kesme altında | evet (kısa) | **hayır** | hayır | evet (okuma) |
| tekrar giriş (recursive) | hayır (varsayılan) | evet (seçenek) | hayır | — |
| çekişme ölçümü | lockstat | lockstat | lockstat | RCU sayaçları |

**Doktrin (2.3.1):** RCU **kilit değildir** — "okuma yolu kilitsiz"
tekniğidir; yazma tarafı senkronizasyonu taşır. Ses yolu (callback) gibi
**okuma-yoğun** senaryolar için uygundur, ancak kullanımı hedef çekirdeğe
bağlıdır (⚠️ VERIFICATION REQUIRED, Q4).

### §2.4 Kullanıcı uzayı kilitleri — çekirdek köprüsü

| Kilit | Katman | Çekirdek karşılığı |
|---|---|---|
| `pthread_mutex` | kütüphane | futex syscall'i |
| `flock` / `fcntl` (dosya) | VFS | dosya kilidi |
| Windows `CRITICAL_SECTION` | Win32 | kullanıcı uzayı kilidi |
| Windows `Mutex` nesnesi | Win32 | çekirdek nesnesi |

**Doktrin (2.4.1):** Kullanıcı uzayı kilitleri **çekirdek API'sini**
çağırır (Linux'ta futex); kilit türü seçimi uygulama katmanında, uygulaması
çekirdekte olur. CoreMusic'te bu sınır PHP → libc → syscall köprüsüdür
(`cekirdek-yonetim` ornekler §2.7).

### §2.5 Ölçüm arayüzleri (kavram — ölçüm `-surucu` §6'da)

| Arayüz | Ne ölçer | Durum |
|---|---|---|
| lockstat | kilit beklemesi/çekişme | kavram (URL §4) |
| locktorture | kilit stresi | kavram (URL §4) |
| RCU stall warn | RCU uyarma gecikmesi | kavram (URL §4) |
| CoreMusic ölçüm | — | **PLANNED** (yok) |

## §3 Deadlock ve starvation (doktrin)

### §3.1 Deadlock — dört koşul (Coffman)

| # | Koşul | Ne demek | Önleme |
|---|---|---|---|
| 1 | karşılıklı tutma | iki taraf birbirinin kilidini tutuyor | tek sıra kuralı |
| 2 | tut ve bekle | kilit tutulurken başka kilit beklenir | tümü tek seferde al |
| 3 | dışarıdan zorla alma | kilit zorla alınamaz | tasarım gereği yok |
| 4 | döngüsel bekleme | A→B→A döngüsü | hiyerarşi (kilit sırası) |

**Doktrin (3.1.1):** Deadlock **dört koşul birlikte** olmak zorunda; tek
bir koşulun kırılması yeterlidir. En yaygın kırma: **tüm kilitleri sabit
sırada almak** (hiyerarşi). Lockdep bunu çalışma zamanında **tespit eder**
(kaynak §4 lockdep-design).

### §3.2 Deadlock tespiti ve önleme — yöntemler

| Yöntem | Ne yapar | Maliyet | Kaynak |
|---|---|---|---|
| kilit hiyerarşisi (sıra) | döngüyü tasarımsal kırar | düşük | doktrin |
| lockdep | çalışma zamanında uyarır | orta (tarama) | §4 |
| timeout ile kilit | süresiz beklemeyi kırar | orta | uygulama |
| rt-mutex (öncelik) | öncelik tersine çevirmeyi çözer | orta | §4 |
| try-lock + geri çekilme | döngüyü kırar | uygulama karmaşık | doktrin |

**Doktrin (3.2.1):** Önleme **tasarım** işidir, çalışma zamanı düzeltmesi
değil; lockdep bir **yakalama** aracıdır, önleme **değil**.

### §3.3 Starvation (açlıktan bekleme)

| Tür | Ne olur | Sonuç | Önleme |
|---|---|---|---|
| kilit açlığı | bir süreç kilit hep kapalı yakalar | bekleyen ölür/geç kalır | adalet/fifo |
| CPU açlığı | düşük öncelik hep geç kalır | iş yapılmaz | öncelik/aging |
| RCU uyarma açlığı | grace period hiç kapanmaz | RCU stall | §3.4 |

**Doktrin (3.3.1):** Deadlock **herkes** durur; starvation **bir taraf**
durur — ikisi karıştırılmamalıdır. Starvation tespiti `-observability`
alanındadır (sayacın kendisi burada **yazılmaz**).

### §3.4 RCU stall ve uyarılma (kavram)

| Kavram | Ne demek | Etki |
|---|---|---|
| grace period | tüm okuyucuların dönmesi beklenir | yazma gecikir |
| RCU stall | grace period olağanüstü uzar | uyarı/log |
| tehlike | okuyucu kilit altında/kesmede kalır | uyarma kilitlenir |

**Doktrin (3.4.1):** RCU kullanan kod **kısa ve kesme-dostu** olmalı;
uzun okuma yolu RCU'yu bozar. Stall tespiti mekanizması kaynak §4
(RCU/stallwarn); CoreMusic'te RCU kullanımı **yok** (repo'da çekirdek kodu
yok — `cekirdek-yonetim` ornekler §3).

### §3.5 Özet — bu dosyanın bağlayıcı 6 kuralı

| # | Kural | Bölüm |
|---|---|---|
| B-K1 | kesme işleyicisi kısa çalışır, uzun iş devredilir | §1.2 |
| B-K2 | kesme bağlamında sleep yasak | §1.3 |
| B-K3 | kesme susturma en kısa ve en son çare | §1.4 |
| B-K4 | kısa+kesme → spinlock; uzun+süreç → mutex | §2.2 |
| B-K5 | tüm kilitler sabit sırada alınır (hiyerarşi) | §3.1 |
| B-K6 | lockdep yakalar, önleme tasarımdadır | §3.2 |

## §4 Kaynakça (envanter · HTTP 200 · 2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| K-W1 | Genel IRQ (kesme) çekirdek API'si | https://docs.kernel.org/core-api/genericirq.html | 200 (bu oturum webfetch) |
| K-W2 | Kilit türleri karşılaştırma (spin/mutex/rwsem) | https://docs.kernel.org/locking/locktypes.html | 200 (envanter) |
| K-W3 | Lockdep kilit tasarım dokümanı (deadlock tespiti) | https://docs.kernel.org/locking/lockdep-design.html | 200 (envanter) |
| K-W4 | Mutex tasarım dokümanı | https://docs.kernel.org/locking/mutex-design.html | 200 (envanter) |
| K-W5 | RT-mutex (öncelikli mutex) | https://docs.kernel.org/locking/rt-mutex.html | 200 (envanter) |
| K-W6 | `futex(7)` hızlı kullanıcı uzayı kilidi | https://man7.org/linux/man-pages/man7/futex.7.html | 200 (envanter) |
| K-W7 | Lockstat kilit istatistiği | https://docs.kernel.org/locking/lockstat.html | 200 (envanter) |
| K-W8 | Locktorture kilit stres testi | https://docs.kernel.org/locking/locktorture.html | 200 (envanter) |
| K-W9 | RCU stall uyarı mekanizması | https://docs.kernel.org/RCU/stallwarn.html | 200 (envanter) |
| K-W10 | WW-mutex (çoklu kilitleme) | https://docs.kernel.org/locking/ww-mutex-design.html | 200 (envanter) |

**Doğrulama notu:** K-W1 ve RCU dizini bu oturumda webfetch ile HTTP 200
doğrulandı (2026-10-13); kalanlar `-dogrulama.md` §E envanterinden yeniden
kullanıldı. Bu dosyada yeni URL doğrulanmadı (envanter yeterli).

### §4.1 URL × bölüm eşlemesi

| Bölüm | Destekleyen URL |
|---|---|
| §1 (kesme yolu) | K-W1 |
| §2 (kilit türleri) | K-W2, K-W3, K-W4, K-W5, K-W6, K-W10 |
| §2.5 (ölçüm) | K-W7, K-W8 |
| §3 (deadlock/starvation) | K-W3, K-W5, K-W9 |

## §5 Açık / doğrulanamayan iddialar (bu dosya)

| # | İddia | Etiket | Neden |
|---|---|---|---|
| A1 | hedef çekirdek Linux'tur | ⚠️ (Q4) | UNKNOWN — model olarak taşınıyor |
| A2 | hedef çekirdekte RCU vardır | ⚠️ (A1 bağlı) | çekirdek bilinmiyor |
| A3 | NMI-dostu kilit seçimi | ⚠️ VERIFICATION REQUIRED | çekirdek API'si okunmadı |
| A4 | kesme susturma eşiği | yazılmaz | ölçüm yok (İlke B10) |
| A5 | jitter toleransı | PLANNED | `-surucu` §6 ölçüm planı |
| A6 | repo'da kilit kodu yok | ✅ (grep 0) | kardeş dal kanıtı (2026-10-13) |

**Kapanış kuralı:** A1-A3 yalnız hedef çekirdek **netleşince** kapanır;
A4-A5 yalnız **ölçümle** kapanır; A6 tekrar grep ile **tekrarlanabilir**.

## §6 Derinleştirme — kesme ve kilit pratiği (doktrin eki)

> Bu bölüm §1-§3'ün **pratik ayrıntısını** taşır; kapsam yine kesme +
> kilit'tir. Ölçüm sonuçları burada **yoktur** (hepsi `-surucu` §6'ya
> kapılıdır).

### §6.1 IRQ yaşam döngüsü — kayıt ve serbest bırakma

| # | Adım | Ne olur | Bağlı kural |
|---|---|---|---|
| 1 | kesme kaydı | hat + işleyici eşlenir | — |
| 2 | etkinleştirme | hat açılır | — |
| 3 | işleyici çağrısı | §1.2 akışı | B-K1 |
| 4 | serbest bırakma | hat kapanır | — |

**Doktrin (6.1.1):** Kayıt/serbest bırakma **eşleştirilmelidir**; kayıt
kayıp (leak) veya çift serbest bırakma → çekirdek hatası. CoreMusic'te bu
yüzey **yok** (`request_irq` grep 0 — cekirdek-yonetim ornekler N5).

### §6.2 IRQ affinity — kesme hangi çekirdeğe düşer?

| Kavram | Ne demek | Neden önemli |
|---|---|---|
| affinity | kesmenin düşeceği çekirdek | ses çekirdeğini koruma |
| dengesiz dağılım | bir çekirdeğe yığılma | o çekirdek şişer |
| izolasyon | çekirdek belirli işlere ayrılır | gecikme kontrolü |

**Doktrin (6.2.1):** Kesme dağılımı **gözlemlenmeden** düzenlenmez; önce
sayı (`-observability`), sonra politika (`-plan`). Bu dosya yalnız kavramı
taşır.

### §6.3 Threaded IRQ — işleyiciyi süreç yapma

| Tür | Bağlam | Sleep | Uzun iş |
|---|---|---|---|
| eski (hardirq handler) | kesme | yasak | devre dışı |
| threaded IRQ | süreç benzeri | evet | serbest |

**Doktrin (6.3.1):** Uzun kesme işi **threaded** yapılır; hardirq içinde
uzun iş B-K1 ihlalidir.

### §6.4 Kilit hiyerarşisi — somut kural tablosu

| # | Kural | Örnek | İhlal sonucu |
|---|---|---|---|
| 1 | kilitler sabit sırayla alınır | A → B → C | deadlock olasılığı |
| 2 | ters sıra alınmaz | B → A (A-B sonrası) | deadlock |
| 3 | iç içe kilit yasak (mümkünse) | A → A | livelock riski |
| 4 | kilit altında bekleme sınırı | uzun I/O | starvation |

**Doktrin (6.4.1):** Hiyerarşi **tüm kod tabanında** tutarlı olmalıdır;
parça parça farklı sıralar deadlock üretir. Lockdep bunu otomatik
denetler (K-W3).

### §6.5 Kilit zehirlenmesi (lock poisoning) — kavram

| Kavram | Ne demek | Sonuç |
|---|---|---|
| poisoned lock | kilit hata sonrası "kullanılamaz" işaretlenir | sonraki kullanım uyarı verir |
| kullanım | kurtarma sırasında | sessiz yürümesin |

**Doktrin (6.5.1):** Zehirlenme bir **uyarı mekanizmasıdır**; hata
kurtarma `-error-recovery`/security alanıdır, burada yalnız kavram taşınır.

### §6.6 Spinlock vs mutex — CPU davranışı

| Davranış | spinlock | mutex |
|---|---|---|
| beklerken CPU | kullanır (döner) | bırakır (sleep) |
| uyandırma | kilit bırakınca döner döner | uyandırma çağrısı |
| öncelik tersine çevirme | tehlikeli (deadlock) | rt-mutex (K-W5) ile çözer |
| kesme altında | uygun (kısa) | uygun değil |

**Doktrin (6.6.1):** "Dönen bekleme" **bilinçli** bir takastır: daha düşük
gecikme karşılığı CPU. Ses yolunda gecikme kritikse anlamlıdır; uzun
bölümde CPU israfıdır (§2.1 doktrini ile aynı).

### §6.7 RCU — üç varlık

| Varlık | Ne yapar | Kural |
|---|---|---|
| okuyucu | kilitsiz okur | kısa tutulur |
| yazıcı | veriyi değiştirir | grace period bekler |
| grace period | tüm okuyucuların dönmesi | uzarsa stall (K-W9) |

**Doktrin (6.7.1):** RCU'da **asıl zorluk yazıcı tarafındadır**; okuma
yolu "bedava" görünür ama yazma senkronizasyonu zordur. CoreMusic'te RCU
**yok** (çekirdek kodu yok).

### §6.8 Özet tablo — §6'nın bağlayıcı ek kuralları

| # | Kural | Bölüm |
|---|---|---|
| B-K7 | IRQ kaydı/serbest bırakması eşleştirilir | §6.1 |
| B-K8 | uzun kesme işi threaded yapılır | §6.3 |
| B-K9 | kilit hiyerarşi tüm kod tabanında tutarlı | §6.4 |
| B-K10 | spinlock bekleme CPU kullanır — bilinçli takas | §6.6 |

### §6.9 Kesme × kilit kesişimi — hangi bağlamda hangi kilit (tam matris)

| Bağlam ↓ / Kilit → | spinlock | mutex | rwsem | RCU okuma | kilit yok |
|---|---|---|---|---|---|
| hardirq (§1.2) | ✓ (kısa) | yasak | yasak | ✓ | ✓ |
| threaded IRQ (§6.3) | ✓ | ✓ | ✓ | ✓ | ✓ |
| süreç (kullanıcı) | ✓ (kısa) | ✓ | ✓ | ✓ | ✓ |
| NMI (§1.5) | ⚠️ (dostu) | yasak | yasak | ⚠️ | ✓ |

**Matris kuralı:** "yasak" **kesin yasaktır** (B-K2 ihlali → çökme/askıya
alma); "⚠️" hedef çekirdeğe bağlıdır (A3 — NMI-dostu kilit API'si
okunmadı); "✓" uygunluktur, ölçüm değil.

### §6.10 Kesme-kilit senaryoları — tipik 4 olay

| # | Olay | Doğru davranış | Yanlış davranış |
|---|---|---|---|
| 1 | ses kesmesi geldi, tampon dolu | kısa oku, EOI, çık | tamponu işlemede beklet |
| 2 | iki çekirdek aynı tamponu yazıyor | tek yazıcı (spinlock/kilitsiz desen) | iki taraf da yazsın (yarış) |
| 3 | kesme sırasında kilit bekleniyor | spinlock (kısa) | mutex dene (uyur → askıda) |
| 4 | uzun log yazımı | threaded IRQ'a taşı | hardirq içinde yaz (B-K1 ihlal) |

**Senaryo kuralı:** Bu 4 olay **model** senaryolardır; CoreMusic'te
karşılık gelen çekirdek kodu **yok** (grep 0) — repoda karşılığı olan
senaryolar `-ornekler` dosyasındadır (PHP → syscall köprüsü).

### §6.11 Ölçüm kapıları — bu dosyanın beklediği, üretmediği sayılar

| Sayı | Ne ölçülür | Üretilemez çünkü | Nereye kapılı |
|---|---|---|---|
| kesme gecikme süresi | işleyici başlangıcı → bitişi | ölçüm yok | `-surucu` §6 |
| kilit çekişme oranı | bekleyen/alan oranı | ölçüm yok | `-surucu` §6 |
| grace period süresi | RCU beklemesi | RCU yok (repo) | `-surucu` §6 |
| susturma süresi toplamı | irq disable toplamı | ölçüm yok | `-surucu` §6 |

**Kapı kuralı:** Bu dosya **soru** üretir, **sayı üretmez**; sayı yalnızca
ölçümle gelir ve eşiği `-surucu` §6.3'te üretilir (uydurma yasak).

## §7 Belirsizlik ve sınır tablosu (bu dosyanın kapanışı)

| Konu | Durum | Nereye kapılı |
|---|---|---|
| zamanlayıcı kesmesi politikası | bu dosya dışı | `isletim-sistemi-plan` |
| kesme sayaçları / telemetry | bu dosya dışı | `isletim-sistemi-observability` |
| kilit bypass güvenlik etkisi | bu dosya dışı | `isletim-sistemi-security` |
| kilit determinizm testi | bu dosya dışı | `isletim-sistemi-testing` |
| dosya/VFS kilitleri (`flock`) | kardeş dal | `cekirdek-yonetim` |
| hedef çekirdek API ayrıntıları | **UNKNOWN** (Q4) | üst karar |
| repo kilit kodu | **yok** (grep 0) | cekirdek-yonetim ornekler §3 |

**Sınır kuralı:** Bu dosya **yalnız** kesme yolu + kilit doktrinini işler;
her satır ya bu dosyada kanıtlanır ya da yukarıdaki tabloya kapılır —
**belirsizlik taşınmaz, işaretlenir** (`⚠️ VERIFICATION REQUIRED` / `UNKNOWN`).

## §8 Sık yapılan hatalar — bu dalda görülmesi muhtemel 8 hata

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| H1 | mutex ile spinlock karıştırma | kesme altında mutex → çökme | §2.2 karar matrisi |
| H2 | kilit altında sleep | spinlock altında bekleme | B-K2 (§1.3) |
| H3 | kilit sırasızlığı | ara sıra deadlock | B-K5 (§6.4) |
| H4 | kesmeyi susturup unutma | sistem takılır | B-K3 (§1.4) |
| H5 | lockdep'i önleme sanmak | tasarım ihlali devam eder | §3.2 doktrini |
| H6 | deadlock ile starvation karıştırma | yanlış teşhis | §3.3 ayrımı |
| H7 | RCU'yu kilit sanmak | yazma tarafı ihmal | §2.3 / §6.7 |
| H8 | ölçüm olmadan eşik yazmak | kanıtsız sayı | İlke B10 (A4) |

**Hata kuralı:** H1-H4 **çökme/askıya alma** üretir; H5-H6 **yanlış teşhis**;
H7-H8 **bilgi hatası**. Doğruluk hatası (H8) bir kez işlendiğinde dosya
`draft`'tan çıkmaz (yeniden denetim gerekir).

### §8.1 Hata × kapı ilişkisi

| Hata | Hangi kapıda yakalanır | Kapı durumu |
|---|---|---|
| H1/H2/H3 | kod incelemesi (kilit sıra denetimi) | ❌ açık (repo'da kilit kodu yok) |
| H4 | gözlem (sayaç) | ❌ açık (`-observability` PLANNED) |
| H5/H6 | kavram denetimi | ✅ geçti (§3 doktrini) |
| H7 | kavram denetimi | ✅ geçti (§2.3/§6.7) |
| H8 | eşik kapısı (K3) | ✅ geçti (eşik yazılmadı) |

## §9 Değişiklik günlüğü (bu dosya)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0-§5) | eksik alt-dal derinleştirme görevi |
| 2026-10-13 | §6-§8 derinleştirme eklendi | dosya derinliği hedefi |
| 2026-10-13 | 6 URL envanter/kontrol doğrulamasıyla taşındı | kanıt kapısı |

## §10 Bağlayıcı özet — doktrin omurgası ve belirsizlik karnesi (2026-10-15)

> Append-only ekleme: §0–§9 değişmemiştir; bu bölüm mevcut bölümleri derler,
> yeni doktrin / yeni ölçüm eklemez.

### §10.1 Bölüm haritası

| Grup | Bölümler | Not |
|---|---|---|
| Doktrin | §1 Kesme yolu (interrupt) · §2 Kilit türleri · §3 Deadlock ve starvation | çekirdek kural kaynakları |
| Kaynak | §4 Kaynakça (envanter · HTTP 200 · 2026-10-13) | tarihli |
| Dürüstlük | §5 Açık / doğrulanamayan iddialar | işaretli eksiklik |
| Pratik | §6 Derinleştirme — kesme ve kilit pratiği (doktrin eki) | doktrin eki |
| Kapanış | §7 Belirsizlik ve sınır tablosu · §8 Sık yapılan hatalar (8) · §9 Değişiklik günlüğü | — |

### §10.2 Belirsizlik karnesi (dosya içi)

| Kalemden | Dayanak |
|---|---|
| Açık / doğrulanamayan iddialar ayrı bölümde taşınır | §5 |
| Belirsizlik ve sınır tablosu kapanıştır | §7 |
| 8 sık yapılan hata ayrı sayım olarak taşınır | §8 |
| Kaynakça tarihlidir (HTTP 200 · 2026-10-13) | §4 |

### §10.3 Sınır beyanı

- Bu bölüm anayasa seviyesindedir; ölçüm protokolü ve SMP pratiği komşu sürücü
  dalındadır (kardeş dosya) — burada taşınmaz.
- Ölçüm/çalışan sistem iddiası bu dosyada YOKTUR.
- Kavramsal doktrin "ölçüldü" diye sunulamaz.
- `git commit` bu oturumda ATILMADI — bu ekleme working tree'dedir (commit orkestratöre ait).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
