---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon/kesme-ve-senkronizasyon-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon
children: []
depends_on: []
evidence:
  - https://docs.kernel.org/locking/lockstat.html
  - https://docs.kernel.org/locking/locktorture.html
  - https://docs.kernel.org/RCU/stallwarn.html
  - https://docs.kernel.org/RCU/torture.html
validation: "2026-10-13 · 4 ölçüm/test URL'si envanterden (HTTP 200 · -dogrulama.md §E) · repo'da kilit/kesme ölçüm kodu YOK (grep 0: spinlock/flock/pcntl_ · kardeş dal kanıtı) · jitter/çekişme ölçüm sonucu → PLANNED (ölçülmedi) · hedef çekirdek UNKNOWN (Q4)"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
  - "Ölçüm hedefi (512 örnek · 48 kHz · lock-free callback) bu çekirdekte geçerli mi? → ⚠️ VERIFICATION REQUIRED (S3)"
group: mimari
---

# kesme-ve-senkronizasyon-surucu — SMP, Tutarlılık ve Ölçüm (Kademe-4 · §4-§6)

## §0 ELI15

Birden fazla çekirdek aynı belleğe dokunduğunda **iki** sorun doğar: (1)
veri **tutarlılığı** — hangi yazma sırayla görünür? (2) **çekişme** — kilit
için kaç çekirdek bekliyor? Bu dosya bu iki sorunun SMP doktrinini (§4),
ölçüm tasarımını (§5) ve referans/risk kapanışını (§6) taşır. Anayasa
(§0-§3) kilit/kesme **kurallarını** kurdu; bu dosya onun **ölçülebilir
yüzünü** sürer. Bağlayıcıdır: ölçüm sonuçları bu ağaçta **hiçbir yerde
yoktur** (PLANNED); burada yalnız **tasarım** taşınır.

| Öğe | Değer |
|---|---|
| Dosya rolü | SMP/ölçüm/risk omurgası (Kademe-4) |
| Bölüm omurgası | §4-§6 (bu dosya) · §0-§3 (anayasa) · §1-§6 (`-ornekler`) |
| Bağlı dosyalar | `kesme-ve-senkronizasyon-anayasa.md` · `kesme-ve-senkronizasyon-ornekler.md` · kök `index.md` §1 |
| Kapsam | SMP tutarlılık · bellek modeli · ölçüm (jitter/çekişme) · risk · referans |
| Kapsam dışı | Zamanlama politikası (`-plan`) · güvenlik (`-security`) · gözlem sayacı (`-observability`) · test (`-testing`) |

> **Ölçüm yok → sonuç yok.** Bu dosyadaki hiçbir sayısal eşik/süre
> **üretilmemiştir**; hepsi `UNKNOWN` veya `PLANNED` işaretlidir (İlke B10).

## §4 SMP ve bellek tutarlılığı

### §4.1 Bellek modeli — neden "anında görünmez"?

| Kavram | Ne demek | Sonuç |
|---|---|---|
| önbellek (cache) | her çekirdeğin kendi kopyası | yazma gecikmeli yayılır |
| yazma sırası (store order) | işlemler farklı sırayla görünür | "sıra ihlali" |
| bellek bariyeri | belirli noktada sırayı zorlar | tutarlılık |
| tutarlılık modeli | çekirdeğin kabul ettiği kurallar | çekirdeğe bağlı |

**Doktrin (4.1.1):** SMP'de **hiçbir çekirdek diğerinin yazısını anında
görmez** varsayılır; doğru davranış bariyer/senkronizasyon ile kurulur.
Bu kural çekirdek seçimi (Q4) **fark etmez** — detay API değişir, model
aynı kalır.

### §4.2 Yarış koşulu (race condition) — tanım ve örnek

| Öğe | Değer |
|---|---|
| tanım | sonucun zamanlamaya bağlı olduğu hata |
| koşullar | eşzamanlı erişim + yazma + koruma yok |
| örnek (model) | iki çekirdek aynı tampona yazar |
| tespit | lockdep/torture (§6.3) |
| önleme | kilit/desen (anayasa §2) |

**Doktrin (4.2.1):** Yarış koşulu **her zaman** hata **vermez** — bu onu
tehlikeli yapar; "çalışıyordu" kanıt **değildir**. Tek doğrulama
senkronizasyon **doğruluğudur** (anayasa B-K4..B-K6).

### §4.3 Hafıza-modeli bariyerleri (kavram)

| Bariyer | Ne zorlar | Tipik kullanım |
|---|---|---|
| yazma-yazma (store-store) | yazma sırası | bayrak yayınlamadan önce veri |
| okuma-okuma (load-load) | okuma sırsı | bayrak okuduktan sonra veri |
| okuma-yazma (load-store) | karışık sı | kilit bırakma |
| tam bariyer | hepsi | en pahalı, en güvenli |

**Doktrin (4.3.1):** Bariyerler **pahalıdır** ve **minimum** kullanılmalıdır;
kilit zaten bariyer içerir — ayrı bariyer çoğu zaman gereksizdir. Doğru
kullanım hedef çekirdeğin API'sine bağlıdır (⚠️ VERIFICATION REQUIRED, Q4).

### §4.4 Kilit çekişmesi (lock contention) — kavram

| Terim | Ne demek |
|---|---|
| çekişme | kilit için bekleyen çekirdek sayısı |
| kritik bölüm | kilitin tutulduğu aralık |
| dolambaç (convoying) | kilit bırakınca sıradaki hemen alır — kuyruk |
| şeffaf kilit | donanım destekli (TSO/CAS) — çekirdeğe bağlı |

**Doktrin (4.4.1):** Çekişme **ölçülmeden** azaltılmaz; ilk adım sayaç
(§6.3), ikinci adım tasarım (kritik bölüm kısaltma / kilit kırma desen).

### §4.5 Kilitsiz desenler (lock-free) — kavram

| Desen | Ne yapar | Maliyet |
|---|---|---|
| atomik karşılaştırma-değiştir (CAS) | kilitsiz güncelleme | başarısız deneme döngüsü |
| tek yazıcı deseni | yazan tek çekirdek | tasarım kısıtı |
| kuyruk (ring buffer) | eşzamanlı oku-yaz | taşma yönetimi |

**Doktrin (4.5.1):** "Lock-free" **hiçbir çekirdeğin bloke olmaması**
demektir; **hatasız** demek **değildir** — yarış koşulu yine mümkündür
(§4.2). Ses yolu hedefi (512 örnek · 48 kHz · lock-free callback) bu
dosyada **taşınır, doğrulanmaz** (S3 — ⚠️).

## §5 Ölçüm tasarımı (jitter · çekişme · kesme)

> Bu bölüm **ölçüm tasarımdır**; hiçbir değer bu turda **ölçülmemiştir**.

### §5.1 Ölçüm arayüzleri (kapsam sözleşmesi — çalıştırılmadı)

| # | Arayüz | Ne ölçer | Durum |
|---|---|---|---|
| 1 | lockstat (§6.3) | kilit bekleme/çekişme | kavram |
| 2 | locktorture (§6.3) | kilit stresi (torture) | kavram |
| 3 | RCU stall warn (§6.3) | grace period gecikmesi | kavram |
| 4 | trace/histogram (envanter) | olay dağılımı | kavram |
| 5 | CoreMusic ölçüm | — | **PLANNED** (yok) |

### §5.2 Jitter tanımı ve türleri (tasarım)

| Tür | Tanım | Ölçüm |
|---|---|---|
| kaynak jitter | gecikmenin değişkenliği (std/arka arkaya fark) | dağılım |
| deadline jitter | hedef süreden sapma | en kötü sapma |
| kesme jitter | işleyici başlangıç değişkenliği | zaman damgası farkı |

**Doktrin (5.2.1):** Jitter **sayısal olarak** tanımlanmadan raporlanmaz;
"az jitter" **kanıt değildir**. Sartname değerleri (48 kHz → 20.83 µs;
512 örnek → 10.666 ms) **aritmetiktir**, ölçüm **değildir** — ikisi
karıştırılmaz.

### §5.3 Ölçüm metodolojisi (adımlar — PLANNED)

| Adım | Eylem | Araç | Beklenen çıktı |
|---|---|---|---|
| 1 | arayüz seçimi (§5.1) | lockstat/trace | sayaç listesi |
| 2 | zaman damgası ekleme | trace | mikro-saniye damgaları |
| 3 | örnekleme | monitor | dağılım |
| 4 | en kötü durum yakalama | stress (torture) | kuyruk/çekişme |
| 5 | eşik üretimi | §5.4 | meşru eşik |
| 6 | alarm bağlama | `-observability` | alarm kuralı |

**Plan durumu:** Adımlar 1-6 **PLANNED**'dir; hiçbiri bu turda çalıştırılmadı.

### §5.4 Ölçüm hedefi (CoreMusic — sartname, ölçülmemiş)

| Hedef | Değer | Tür |
|---|---|---|
| örnekleme | 48 kHz | sartname (aritmetik: 20.83 µs/kare) |
| tampon | 512 örnek | sartname (aritmetik: 10.666 ms/kare) |
| yol | lock-free callback | hedef (PLANNED) |
| jitter toleransı | **UNKNOWN** | ölçülmedi → yazılmaz |
| çekişme toleransı | **UNKNOWN** | ölçülmedi → yazılmaz |

**Doktrin (5.4.1):** Sartname **hedeftir**, ölçüm **değildir**; hedefin
tutulup tutulmadığı yalnız ölçümle anlaşılır. "Ölçüldü, tuttu" iddiası
bu ağaçta **hiçbir yerde yoktur**.

### §5.5 Ölçüm yokluğunun risk tablosu

| Risk | Olasılık | Etki | Mevcut koruma |
|---|---|---|---|
| üretimde jitter taşması | `UNKNOWN` | yüksek | koruma **yok** (ölçüm yok) |
| kilit çekişmesi şişmesi | `UNKNOWN` | orta | koruma **yok** |
| RCU stall (eğer RCU kullanılırsa) | `UNKNOWN` | orta | RCU yok (repo) |
| yanlış eşik yazma | korunuyor | yüksek | `UNKNOWN` işaretleri |
| sartnameyi ölçüm sanma | düşük (§5.2 doktrini) | orta | doktrin |

**Risk kuralı:** "Olasılık" sütununda sayı **yoktur** — olasılık için de
ölçüm gerekir. Bu tablo eksikliğin kendisini kayda geçirir, tahmin üretmez.

## §6 Referans, test arayüzleri ve kapanış

### §6.1 Repo kanıtı (negatif — bu dalın dürüstlük kaydı)

| # | Aranan | Kapsam | İsabet | Yorum |
|---|---|---|---|---|
| R1 | `spinlock` | tüm `*.php` | 0 | çekirdek kilit kodu yok |
| R2 | `flock` / `pcntl_` | `home.coremusic.net/` | 0 | süreç/kilit betiği yok |
| R3 | kilit/kesme ölçümü | tüm `*.php` | 0 | ölçüm kodu yok |

**Dürüstlük cümlesi:** 0 isabet "**repoda o kod yok**" der; "**sistemde
kilit yok**" **demez** — çekirdek her syscall'un altında çalışır
(`cekirdek-yonetim` ornekler §2.7 köprüsü).

### §6.2 Ölçüm × arayüz eşlemesi (tasarım — çalıştırılmadı)

| Ölçüm hedefi | Arayüz | Beklenen çıktı |
|---|---|---|
| kilit bekleme | lockstat | bekleme süresi dağılımı |
| kilit stresi | locktorture | hata/askıya alma |
| RCU gecikmesi | stallwarn | stall uyarısı |
| kesme jitter | trace/histogram | olay dağılımı |

### §6.3 Kaynakça (envanter · HTTP 200 · 2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| S-W1 | Lockstat kilit istatistik altyapısı | https://docs.kernel.org/locking/lockstat.html | 200 (envanter) |
| S-W2 | Locktorture kilit stres testi | https://docs.kernel.org/locking/locktorture.html | 200 (envanter) |
| S-W3 | RCU stall uyarı mekanizması | https://docs.kernel.org/RCU/stallwarn.html | 200 (envanter) |
| S-W4 | RCU stres testi (rcutorture) | https://docs.kernel.org/RCU/torture.html | 200 (envanter) |

**Doğrulama notu:** Dört URL de `-dogrulama.md` §E envanterinden yeniden
kullanıldı (2026-10-13 HTTP 200); bu dosyada **yeni** URL doğrulanmadı
(envanter yeterli).

### §6.4 Sık yapılan hatalar — bu dosyada 6 hata

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| M1 | sartnameyi ölçüm sanmak | "jitter 20.83 µs" der | §5.2/§5.4 doktrini |
| M2 | ölçüm olmadan eşik yazmak | kanıtsız sayı | İlke B10 |
| M3 | lockdep'i ölçüm sanmak | tespit ≠ ölçüm | §6.2 |
| M4 | "lock-free = hatasız" sanmak | yarış koşulu ihmal | §4.5 doktrini |
| M5 | Windows/Linux sayaçlarını eşlemek | yanlış yorum | kardeş dal İlke B11 |
| M6 | 0 isabeti "sistemde yok" diye genişletmek | iddia taşması | §6.1 dürüstlük |

### §6.5 Açık sorular ve kapanış yolları

| # | Soru | Etiket | Kapanış yolu |
|---|---|---|---|
| S1 | core ↔ kernel sınırı (Q2) | ⚠️ VERIFICATION REQUIRED | üst karar |
| S2 | hedef çekirdek (Q4) | UNKNOWN | ortam/prod okuması |
| S3 | 512/48k hedefi bu çekirdekte geçerli mi? | ⚠️ VERIFICATION REQUIRED | hedef çekirdek + ölçüm |
| S4 | jitter toleransı ne olmalı? | PLANNED | §5.3 ölçümü |
| S5 | çekişme eşiği? | PLANNED | §5.3 ölçümü |
| S6 | lock-free callback bu çekirdekte destekli mi? | UNKNOWN | hedef çekirdek |

**Kapanış kuralı:** S1-S3 üst karar/hedef-çekirdek ister; S4-S5 yalnız
ölçümle kapanır; S6 hedef çekirdek netleşince kapanır.

### §6.6 Dosyanın komşulara verdiği özet (handover satırı)

| Alıcı dal | Verilen bilgi | Biçim |
|---|---|---|
| `kesme-ve-senkronizasyon-anayasa` | doktrine bağlı ölçüm/ölçü yokluğu | §5, §6.1 |
| `kesme-ve-senkronizasyon-ornekler` | ölçüm arayüzleri + risk tablosu | §5.1, §5.5 |
| `isletim-sistemi-plan` | jitter tanımı + sartname aritmetiği | §5.2, §5.4 |
| `isletim-sistemi-observability` | lockstat/trace arayüzleri (tasarım) | §6.2 |
| `isletim-sistemi-testing` | locktorture/rcutorture arayüzleri (tasarım) | §6.2, §6.3 |
| `isletim-sistemi-security` | kilit bypass etkisi (kilit yoksa güvenlik etkisi) | §4.2 |

**Handover kuralı:** Bu tablodaki alıcılar bilgiyi **kullanır**, bu dosyanın
satırlarını **değiştirmez**; değişiklik ADR/üst karar ister.

## §7 Derinleştirme — SMP ve ölçüm pratiği (ek)

### §7.1 Kilit deseni kataloğu — hangi desen ne çözer

| # | Desen | Ne çözer | Maliyet | Bağlı kural |
|---|---|---|---|---|
| 1 | tek kilit (big lock) | basitlik | yüksek çekişme | — |
| 2 | kilit hiyerarşisi | deadlock | tasarım zahmeti | anayasa §6.4 |
| 3 | kilit kırma (split lock) | çekişme | karmaşıklık | §4.4 |
| 4 | okuma-yazma ayrımı (rwsem) | çok okuyucu | yazıcı bekler | anayasa §2.3 |
| 5 | tek yazıcı deseni | kilitsiz okuma | yazıcı kısıtı | §4.5 |
| 6 | RCU | kilitsiz okuma | yazma senkronu | anayasa §6.7 |

**Doktrin (7.1.1):** Desen seçimi **ölçüm sonrası** yapılır; ölçüm yokken
büyük kilit (1) **varsayılan**dır — "en basit doğru" (YAGNI).

### §7.2 Ölçüm × hedef eşleme — hangi ölçüm hangi soruyu yanıtlar

| Ölçü | Soru | Eşik (üretilmez) | Durum |
|---|---|---|---|
| kilit bekleme (lockstat) | kilit ne kadar tutuyor? | yazılmaz (İlke B10) | PLANNED |
| çekişme oranı | kaç çekirdek bekliyor? | yazılmaz | PLANNED |
| kesme jitter | kesme ne kadar düzensiz? | yazılmaz | PLANNED |
| RCU grace period | yazma ne kadar bekliyor? | yazılmaz | PLANNED |
| stall uyarısı | sistem takıldı mı? | kavram (uyarı var) | kavram |

**Eşleme kuralı:** Her ölçünün **tek bir sorusu** vardır; soru cevaplanmadan
eşik yazılmaz.

### §7.3 SMP hata senaryoları — 5 tipik olay

| # | Olay | Belirti | Kök neden | Azaltım |
|---|---|---|---|---|
| 1 | veri yarışı | bozuk sayı/tampon | koruma yok (§4.2) | kilit/desen |
| 2 | deadlock | sistem askıda | sıra ihlali (anayasa §3.1) | hiyerarşi |
| 3 | livelock | CPU çalışıyor, iş yok | kilit döngüsü | backoff |
| 4 | starvation | bir çekirdek geç kalır | adaletsiz kilit | fifo/aging |
| 5 | RCU stall | yazma hiç bitmez | uzun okuma (anayasa §3.4) | okuma kısaltma |

**Senaryo kuralı:** Bu 5 olay **model** senaryolardır; CoreMusic'te
karşılık gelen çekirdek kodu **yok** (§6.1 R1-R3) — repoda karşılığı olan
senaryolar `-ornekler` dosyasındadır.

### §7.4 Ölçüm — bu dosyanın kendi ölçüm tablosu

| # | Ölçü | Değer | Durum |
|---|---|---|---|
| 1 | ölçüm arayüzü sayısı | 4 kavram (lockstat/locktorture/stallwarn/rcutorture) | ölçüldü (2026-10-13) |
| 2 | repo ölçüm kanıtı | 0 (grep R1-R3) | ölçüldü (2026-10-13) |
| 3 | URL (yeniden kullanım) | 4 (HTTP 200) | ölçüldü (2026-10-13) |
| 4 | yeni URL doğrulama | 0 | bilinçli (envanter yeterli) |
| 5 | gerçek jitter değeri | — | `UNKNOWN` (ölçülmedi) |
| 6 | gerçek çekişme değeri | — | `UNKNOWN` (ölçülmedi) |
| 7 | açık soru | S1-S6 (6) | açık |

**Ölçüm dürüstlüğü:** Tablodaki 1-4 **yapısal ölçüm** (kavram/grep/URL);
5-7 **durum ölçümüdür** ve gerçek değer üretilmemiştir.

### §7.5 Değişiklik günlüğü (bu dosya)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0-§6) | eksik alt-dal derinleştirme görevi |
| 2026-10-13 | §7 derinleştirme eklendi | dosya derinliği hedefi |
| 2026-10-13 | 4 ölçüm URL'si envanterden taşındı | kanıt kapısı |

## §8 Ölçüm protokolü — adım adım (tasarım, çalıştırılmadı)

> Bu bölüm §5.3'ün **uygulanabilir protokolünü** taşır; hiçbir adımı bu
> turda çalıştırmadık. Adımlar **sıralıdır** — atlanan adım sonrakinin
> çıktısını geçersiz kılar.

### §8.1 Protokol adımları

| # | Adım | Girdi | Çıktı | Bağlı adım |
|---|---|---|---|---|
| P1 | ortam kaydı | hedef çekirdek/çekirdek sayısı | ortam künyesi | — |
| P2 | arayüz seçimi (§5.1) | ortam | sayaç listesi | P1 |
| P3 | referans ölçümü (boş yük) | sayaç listesi | taban dağılım | P2 |
| P4 | yük ölçümü (stres) | locktorture/rcutorture | stres dağılım | P3 |
| P5 | karşılaştırma | taban + stres | fark tablosu | P4 |
| P6 | en kötü durum | stres dağılım | kuyruk/tepe | P4 |
| P7 | eşik üretimi | P3-P6 | meşru eşik | P5, P6 |
| P8 | tekrarlanabilirlik | P7 | aynı koşulda tekrar | P7 |

**Protokol kuralı:** P1 **her zaman** ilk adımdır — ortam bilinmeden hiçbir
ölçüm **anlam taşımaz** (Q4 UNKNOWN durumunda P1 bile tamamlanamaz).

### §8.2 Protokol × durum tablosu (bu turda ne yapıldı)

| Adım | Durum | Neden |
|---|---|---|
| P1 | ❌ yapılamadı | hedef çekirdek UNKNOWN (Q4) |
| P2-P8 | ❌ yapılamadı | P1'e bağlı |

**Durum kuralı:** Bu tablo "plan var, uygulama yok" durumunu kayda geçirir;
"yakında yapılacak" gibi bir tarih **yazılmaz** (uydurma takvim yasak).

### §8.3 Ölçüm araçları — beklenen çıktı türü (değer değil)

| Araç | Beklenen çıktı türü | Değer verilir mi? |
|---|---|---|
| lockstat | sayısal dağılım | hayır (çalıştırılmadı) |
| locktorture | hata/süre raporu | hayır |
| RCU stallwarn | uyarı olayı | hayır |
| trace/histogram | olay sayısı/histogram | hayır |

**Araç kuralı:** Bu dosyada araç **adı** ve **çıktı türü** taşınır;
araç **çıkış değeri** taşınmaz (çalıştırılmadı → yok).

## §9 Komşu sınır matrisi (bu dosya ↔ diğer dallar)

| Dışarıda olan | Nereye gider | Neden |
|---|---|---|
| zamanlama politikası (tick) | `isletim-sistemi-plan` | politika `-plan` |
| jitter ölçüm protokolünün yürütmesi | `isletim-sistemi-plan` | protokol `-plan`'da |
| güvenlik kilit bypass etkisi | `isletim-sistemi-security` | politika `-security` |
| kilit çekişme telemetry | `isletim-sistemi-observability` | sayaç `-observability` |
| kilit determinizm testi | `isletim-sistemi-testing` | test `-testing` |
| dosya/VFS kilitleri | `cekirdek-yonetim` | kardeş dal |
| OS türü (kesme/kilit modeli) | `isletim-sistemi-turu` | tür `-turu` |

**Sınır kuralı:** Bu dosya **yalnız** SMP/ölçüm/risk omurgasını işler;
her satır ya bu dosyada kanıtlanır ya da yukarıdaki tabloya kapılır —
**belirsizlik taşınmaz, işaretlenir**.

## §10 Sık yapılan hatalar (ölçüm/SMP) — bu dosyanın 7 hatası

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| M7 | ölçüm yapmadan "geçti" demek | kanıtsız onay | §8.2 durum tablosu |
| M8 | taban ölçümü (P3) atlamak | stres ölçümü anlamsız | §8.1 sıralama |
| M9 | ortamı kaydetmemek (P1) | tekrarlanamaz ölçüm | §8.1 P1 |
| M10 | sartname aritmetiğini ölçüm sunmak | "20.83 µs jitter" | §5.4 doktrini |
| M11 | kilit desenini ölçüm seçmeden değiştirmek | tahmine dayalı tasarım | §7.1 doktrini |
| M12 | "lock-free" iddiasını kanıtsız kullanmak | kavram kayması | §4.5 doktrini |
| M13 | 0 isabeti geniş yorumlamak | iddia taşması | §6.1 dürüstlük |

**Hata kuralı:** M7-M10 **ölçüm doğruluğu** hatalarıdır; M11-M12 **tasarım**
hatalarıdır; M13 **bilgi** hatasıdır. Ölçüm doğruluğu hatası bir kez
işlendiğinde dosya `draft`'tan çıkmaz (yeniden denetim gerekir).

### §10.1 Hata × kapı ilişkisi

| Hata | Hangi kapıda yakalanır | Kapı durumu |
|---|---|---|
| M7/M8/M9 | protokol denetimi (§8) | ✅ geçti (adımlar işaretli, hiçbiri çalıştırılmadı) |
| M10/M11/M12 | kavram denetimi | ✅ geçti (doktrinler yazılmış) |
| M13 | K4 (olumsuz kanıt) | ✅ geçti (2026-10-13) |

## §11 Derin örnek — kilit deseni karar ağacı (tam akış)

```text
[ölçüm var mı?]
     |
     +-- hayır --> [big lock (varsayılan, §7.1)]
     |
     +-- evet --> [çekişme yüksek mi?]
                       |
                       +-- hayır --> [big lock yeter]
                       |
                       +-- evet --> [kritik bölüm kısa mı?]
                                          |
                                          +-- evet --> [split lock]
                                          |
                                          +-- hayır --> [desen: rwsem / RCU / tek yazıcı]
```

| Düğüm | Soru | Cevap yoksa |
|---|---|---|
| D1 | ölçüm var mı? | big lock varsayılan (§7.1.1) |
| D2 | çekişme yüksek mi? | `UNKNOWN` → big lock |
| D3 | bölüm kısa mı? | `UNKNOWN` → tasarımcı kararı + risk kaydı |

**Ağaç kuralı:** Ağaç **karar sürecini** taşır, **kararı vermez**; karar
için D1'den itibaren **ölçüm** gerekir (bu turda D1 bile kapalı — P1 ❌).

## §12 Kapanış tablosu — bu dosyanın ne taşıdığı, ne taşımadığı

| Taşıdığı | Taşımadığı (işaretli) |
|---|---|
| SMP tutarlılık doktrini (§4) | kilit beklemesi sayısı (`UNKNOWN`) |
| jitter/çekişme tanımı (§5.2) | jitter tolerans değeri (`UNKNOWN`) |
| ölçüm protokolü tasarımı (§8) | protokol sonucu (❌ çalıştırılmadı) |
| 4 ölçüm/test arayüzü (§6.3) | araç çıktıları (çalıştırılmadı) |
| risk tablosu (§5.5) | olasılık sayıları (yok — ölçüm gerekir) |
| 6 açık soru (§6.5) | kapanmış soru (yok) |

**Kapanış kuralı:** Sağ sütun **hiçbir zaman** soldakiyle değiştirilemez;
değişiklik yalnız gerçek ölçüm/üst kararla olur.

## §13 Sartname aritmetiği — bu dosyanın izin verdiği tek sayı kümesi

> Aşağıdaki sayılar **aritmetik**tir (sartnameden türetildi), ölçüm
> **değildir**. Türetme: `1 / 48000 = 20.833... µs` · `512 / 48000 = 10.666... ms`.

| # | Hesap | Sonuç | Tür |
|---|---|---|---|
| 1 | 1 kare süresi (48 kHz) | 20.833 µs | aritmetik |
| 2 | 512 örnek süresi (48 kHz) | 10.666 ms | aritmetik |
| 3 | 1 saniyede kare sayısı (512 örnek) | 93.75 kare/s | aritmetik |
| 4 | jitter eşiği | **yazılmaz** | ölçüm gerekir |
| 5 | çekişme eşiği | **yazılmaz** | ölçüm gerekir |

**İzin kuralı:** Tabloda 1-3 **türetildi** (çarpma/bölme); 4-5 **üretilmez**
(ölçüm şartı). İki sayı türü karıştırılırsa §5.2 doktrini ihlal edilir.

### §13.1 Türetme şeffaflığı — hangi sayı nereden geldi

| Sayı | Girdi | İşlem | Sonuç |
|---|---|---|---|
| 20.833 µs | 48000 Hz | 1/48000 | bir örnekleme aralığı |
| 10.666 ms | 512, 48000 | 512/48000 | bir tampon süresi |
| 93.75 | 48000, 512 | 48000/512 | tampon/saniye |

**Şeffaflık kuralı:** Her türetme **girdisiyle** birlikte yazılır; girdi
sartnameden gelir (sartname kaynağı: kardeş dal `cekirdek-yonetim`
surucu §6.4 — bu dosya yalnız tekrar eder, yeni sartname **üretmez**).

### §13.2 Aritmetik × ölçüm ayrımı — özet kontrol

| Kontrol | Soru | Cevap |
|---|---|---|
| sayı sartnameden mi? | 48 kHz / 512 örnek | evet → aritmetik |
| sayı ölçümle mi geldi? | lockstat/trace çıktısı | hayır → yazılmaz |
| sayı tahmin mi? | "yaklaşık X olur" | hayır → yazılmaz |

**Kontrol kuralı:** Üç sorudan yalnız ilki "evet" olabilir; ikinci ve
üçüncü "evet" ise sayı **silinir** (İlke B10 ihlali).

### §13.3 Dosyanın izinli/yasaklı sayı türleri (tek tablo)

| Tür | Örnek | Durum |
|---|---|---|
| aritmetik (sartname türevi) | 20.833 µs · 10.666 ms · 93.75 kare/s | ✅ izinli |
| ölçüm sonucu | jitter σ · çekişme oranı | ❌ ölçülmedi |
| tahmin | "yaklaşık 10 µs olur" | ❌ yasak |
| eşik | tolerans değeri | ❌ ölçüm gerekir |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
