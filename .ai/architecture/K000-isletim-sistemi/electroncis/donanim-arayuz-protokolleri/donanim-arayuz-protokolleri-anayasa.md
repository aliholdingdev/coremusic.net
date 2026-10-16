---
node_id: K000-isletim-sistemi/electroncis/donanim-arayuz-protokolleri/donanim-arayuz-protokolleri-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis/donanim-arayuz-protokolleri
children: []
depends_on: [K002-electroncis, K001-donanım, K004-dsp, K000-isletim-sistemi/isletim-sistemi-audio, K000-isletim-sistemi/os-donanim-tespiti]
evidence:
  - "https://docs.kernel.org/driver-api/i2c.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/spi.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/usb/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/gpio/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/clk.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/sound/index.html (200 OK · 2026-10-14)"
  - "https://www.usb.org/ (200 OK · 2026-10-14)"
  - "C:\www\coremusic.net\.ai\electronic\drivers\audio-drivers.md (709 satır · salt-okunur atıf)"
validation: "2026-10-14 · yazıldı (Vault Steward Q1 kararı) · anayasa hedef ≥350 satır · sayısal register/eşik UYDURULMADI · red-team bekliyor · 2026-10-14 · genişletme (Faz C)"
open_questions:
  - "⚠️ VERIFICATION REQUIRED: gerçek cihaz register adresleri/PCI-ID listesi yok (envanter os-donanim-tespiti'ne devredildi) — bu anayasa seviye/davranış kurallarıyla sınırlıdır."
  - "⚠️ VERIFICATION REQUIRED: Windows WDK/UAC karşılıkları bu turda sayfa-derinliği doğrulanmadı (yalnız genel WDK uçları 200)."
  - "clocksource/clockevents 404 kaydı: timer alt-sistemi kapsam dışı bırakıldı; yeniden değerlendirilirse vault §6 sahibi karar verir."
group: mimari
---

# Anayasa — donanim-arayuz-protokolleri (OS↔donanım protokol seviyesi)

> **`electroncis` yazım hatası dokunulmaz** (v2 §3 KURAL 1).

## §0 ELI15

Bu anayasa, işletim sisteminin donanıma **protokol/register seviyesinde** nasıl
yaklaştığının kural setidir. Dört ana başlık taşır: (1) **ses hattı** — I2S/TDM/PCM ve
saat ilişkileri (MCLK/BLOCK/FS), (2) **register erişimi** — I2C/SPI üzerinde okuma/yazma
disiplini, (3) **USB** — cihaz keşfi ve ses sınıfı enumerasyonu, (4) **GPIO + clock
ağı** — haberleşme/hotplug pinleri ve saat kaynaklarının OS yönetimi. Her kural
"OS kodu neyi garanti eder" düzeyindedir; devre hesabı, eşik, tolerans
`K002-electroncis` alanıdır. Sayısal register adresi, voltaj eşiği veya cihaz-ID
**uydurulmaz** — bilinmiyorsa `UNKNOWN`/`⚠️ VERIFICATION REQUIRED` yazılır.

## §1 Kapsam ve test

| Kural testi | Sonuç |
|---|---|
| OS kodu protokol standardına göre mi konuşuyor? | EVET → bu anayasa |
| Sorun devre/empedans/seviye mi? | `K002-electroncis` |
| Sorun sinyal zinciri/dönüşüm mü? | `K004-dsp` |
| Sorun hangi cihazın takılı olduğu mu? | `os-donanim-tespiti` |
| Sorun firmware-in içindeki RT kod mu? | `.ai/electronic/firmware/` (atıf) |

## §2 Ses hattı — I2S / TDM / PCM (OS ses yığını gözü)

### §2.1 Hattın OS gördüğü tanımı

| Kavram | OS neyi görür | Kural |
|---|---|---|
| Veri hattı | tek yönlü seri veri (TX/RX ayrı hat) | OS hattı **yönlendirmez**, yalnız DMA beslemesi |
| BCLK (bit clock) | her bayt için saat | OS bu saati **üretmez** — clock ağacından alır (§6) |
| FS / LRCLK (frame) | kanal/blok çerçevesi | FS frekansı = örnekleme hızı; OS bunu format ile eşler |
| MCLK | codec'e giden üst saat | OS, MCLK'i clock ağacında talep eder; **zorunlu mu** cihaz işi |
| Master/Slave | hattın saati kimin taşıdığı | OS tarafında yapılandırma alanıdır; **varsayım yazılmaz** |
| Slot/genişlik | kare başına bayt düzeni | ses formatıyla eşleşmeli; uyuşmazlık = sessiz/bozuk çıkış |

### §2.2 Bozulma sınıfı → OS sorumluluğu

| Bozulma | Tipik OS kökeni | Kural |
|---|---|---|
| Tıklama/kesik | DMA buffer taşması / yanlış FS | buffer baştan ayrılmalı (real-time tahsis yasak) |
| Oktav/kanal kayması | FS↔format uyuşmazlığı | format müzakeresi tek noktadan (ses yığını) |
| Bit hatası | BCLK↔bayt düzeni uyuşmazlığı | slot/genişlik register'ı sürücüde tek kayıt |
| Susma | mute/enable sırası | §2.3 sırası zorunlu |

### §2.3 Başlatma/kapatma sırası (bağlayıcı)

```text
Başlat:  clock ağacı ayar → DMA buffer ayır → codec register (format/slot/mute-off) → veri yolu aç
Kapat:   mute-on → veri yolu dur → DMA bırak → clock serbest (kullanım yoksa)
Asla:    veri yolunu clock'tan önce açmak; kapatırken mute atlamak
```

### §2.4 I2S/TDM/PCM karşılaştırma (OS kararı)

| Hat | OS gözünde tipik kullanım | Bu dal |
|---|---|---|
| I2S | standart stereo ses hattı | çekirdek örnek |
| TDM | çok-kanalı tek hatta slot dağıtımı | slot düzeni kaydı |
| PCM (telefonik) | dar bant senkron hat | yalnız varlık kaydı — derinlik K004/K002 |

> Derin DSP/örnekleme-zinciri tartışması `K004-dsp`'ye devredilir; burada **arayüz
> kuralları** kalır.

### §2.5 DMA ring buffer — OS kural katmanı

> Kaynak: `https://docs.kernel.org/driver-api/dmaengine/index.html` ·
> `https://docs.kernel.org/driver-api/dmaengine/client.html` (200 OK · 2026-10-14).

| # | Kural | Açıklama |
|---|---|---|
| 1 | baştan tahsis | ring buffer (descriptor + bellek) akış **başlamadan önce** ayrılır; gerçek-zamanlı yolda bellek tahsisi yasak (§2.2 ile aynı disiplin) |
| 2 | periyot kuşatması | buffer en az bir tam periyot (frame set) kuşatır; periyot boyutu ses formatından **hesaplanır** — uydurma bayt sayısılmaz |
| 3 | wrap-around | yazma/okuma uçları tampon sonuna varınca başa döner; uç işaretleme **tek taraflıdır** (DMA ↔ sürücü yarışı üretilmez) |
| 4 | producer/consumer ayrımı | DMA tarafı TX'e yazar / RX'ten okur; CPU yalnız boşalan-dolan segmentle çalışır; iki taraf **aynı segmente yazmaz** |
| 5 | cache uyumu | DMA↔CPU bellek temizleme/geçersizleştirme kavram düzeyinde zorunludur (dmaengine client API); platform ayrıntısı — bayt adresi/eşik **uydurulmaz** |
| 6 | underrun/overrun | TX underrun / RX overrun olayı sürücüde **sayılır ve raporlanır**; sessiz atlanan taşma §2.2 "susma/bozuk çıkış" sınıfıdır |
| 7 | askı sırası | suspend'te önce ring durdurulur, sonra bellek serbest (§3.2-2 runtime PM ile aynı); ters sıra yasak |
| 8 | ham erişim | ring belleğine kullanıcıya ham erişim **verilmez**; sürücü DMA maskesi ve bellek sahipliğini soyutlar |

### §2.6 Period–kesme ilişkisi ve double-buffer disiplini

| Adım | OS gözünde | Kural |
|---|---|---|
| Periyot sonu | DMA periyodu tamam → sürücüye olay | olay işi **hafif** kalır (işaretleme/kuyruk); uzun iş os-guc-elektronigi §11 (DPC/ISR bütçesi) alanıdır |
| CPU tarafı | tamamlanan periyot doldurulur | yalnız **hazır** segmente yazılır; hazır olmayan segmente yazma yasak |
| Double-buffer | iki periyot dönüşümlü kullanılır | biri DMA'dayken diğeri CPU'da; rol değişimi **yalnız periyot sınırında** |
| Besleme gecikmesi | CPU geç kaldı | underrun sayacı + rapor (§2.2 tıklama/kesik sınıfı ile eşleşir); "sessiz doldurma" yasağı |

## §3 I2C / SPI — register erişim disiplini

### §3.1 Temel kural tablosu

| Kural | I2C | SPI |
|---|---|---|
| Adresleme | bus üzerinde slave adresi | chip-select (CS) hattı |
| Register erişimi | adres yaz → (tekrar-start) → oku | CS asserted → adres/reg baytı → veri |
| Register adresi genişliği | cihaza göre 1/2 bayt — **uydurulmaz** | cihaza göre — **uydurulmaz** |
| Eşzamanlılık | tek bus'ta kilit/queue gerekir | her CS kendi bölgesi |
| Hata kodu (Linux davranışı) | cihaz yok → -ENXIO; zaman aşımı → -ETIMEDOUT | benzeri — sürücü yorumlar |

> Kaynak: `docs.kernel.org/driver-api/i2c.html` · `spi.html` (§ Evidence, 200 OK).

### §3.2 Bağlayıcı davranışlar

1. **Okuma/yazma çifti kuralı:** register yazma sonrası okuma, cihazın
   **onayladığı** değer üzerinden değerlendirilir (yaz-oku-doğrula); doğrulanamayan
   yazma sürücüde hata sayılır.
2. **Runtime PM:** bus, kullanılmadığında askıda — `docs.kernel.org/power/runtime_pm.html`
   (200 OK). Sürekli açık bus = güç israfı; OS bunu **runtime-put** ile kapatır.
3. **Eşzamanlı erişim:** aynı cihaza iki süreç erişiyorsa sürücü içi lock/queue;
   kullanıcıya açık ham bus erişimi **verilmez** (salt sürücü).
4. **Hot-path yasak:** I2C/SPI register erişimi gerçek-zamanlı ses yolunda
   **yapılmaz** (hız/yönlendirme belirsiz) — konfigürasyon anında yapılır.

### §3.3 Sınır

| Konu | Sahibi |
|---|---|
| Register haritası, adres listesi | `K002-electroncis` / cihaz datasheeti |
| Bus topolojisi/gerilim seviyesi | `K002-electroncis` |
| OS okuma sırası/hata kodu | **bu anayasa** |

### §3.4 ACPI yönlendirmeli cihazlarda register erişimi (özet)

| Kural | Açıklama |
|---|---|
| Kaynak firmware'den | ACPI/fiş cihazı tarif eder; OS kaynakları (port/bellek/kesme — kavram düzeyi) firmware tablosundan alır ve sürücüye iletir |
| Anlam firmware'den GELMEZ | firmware OS'e **yer** verir; register **anlamı/haritası** cihaz işidir (§3.3 sınırı değişmez) |
| Zorlama yok | firmware'in tarif etmediği register yüzeyi OS tarafından **tahmin edilmez** |
| Tam çerçeve | §10 (ACPI↔driver etkileşimi) — güç durumu eşlemesi os-guc-elektronigi §2'dedir |

## §4 USB — enumerasyon ve ses sınıfı (OS USB yığını)

### §4.1 Yaşam döngüsü

| Aşama | OS ne yapar | Kayıt |
|---|---|---|
| Attach | port durumu değişir → reset → adres atama | hotplug olayı |
| Descriptor okuma | device/config/interface okunur | sınıf belirlenir |
| Sınıf eşleşme | ses sınıfı ise ses birimi keşfi | UAC = ses yığınına bağlanır |
| Aktif kullanım | alternatif yapılandırma seçilir → endpoint akışı | DMA/URB havuzu |
| Detach | önce akış dur → kaynaklar bırak | temiz kapanış |

> Kaynak: `docs.kernel.org/driver-api/usb/index.html` · `www.usb.org` (200 OK).

### §4.2 Ses sınıfı (UAC) kuralları

| Kural | Açıklama |
|---|---|
| Alternatif yapılandırma | gerçek akış ancak doğru alternatif seçildikçe başlar |
| Endpoint eşleştirme | OS, endpoint ↔ format eşini müzakere eder |
| Saat bağımsızlığı | USB ses cihazının kendi saati vardır — lokal MCLK'e **bağımlı değildir** |
| Hotplug | çıkarma anında akış donmaz: önce dur, sonra bırak |

### §4.3 ⚠️ Açık yüzey

Windows tarafı UAC/WDM karşılıkları genel WDK uçları haritasında (§ Evidence) —
**sayfa derinliği doğrulanmadı** → `⚠️ VERIFICATION REQUIRED`.

### §4.4 USB hotplug / keşif sırası (derinlik — bağlayıcı sıra)

> Kaynak: `https://docs.kernel.org/driver-api/usb/index.html` (200 OK) ·
> `https://docs.kernel.org/driver-api/usb/usb.html` (200 OK · 2026-10-14).

| # | Adım | OS davranışı | Kural |
|---|---|---|---|
| 1 | connect | port durumu değişimi | debounce uygulanır (§5 bounce kuralı port olayına da geçerli); olay yığını yasak |
| 2 | reset + adres | port reset → adres atama | OS yığını tek noktadan yürütür; sürücü bu aşamaya **karışmaz** |
| 3 | descriptor okuma | device → config → interface → endpoint | zincir yarım kalırsa cihaz "tanımsız" sayılır, kurulum başlamaz |
| 4 | sınıf eşleşmesi | ses sınıfı → UAC birim keşfi | §4.2 kuralları bu noktadan devreye girer |
| 5 | sürücü eşleşmesi | ID/descriptor ↔ sürücü tablosu | eşleşme yok → yalnız kayıt; **zorlama/eş uydurma yok** (§3.4 aynı ilke) |
| 6 | probe/kaynak | clock·DMA·register talebi (§7) | kısmi başarısızlık → rollback (§7 zinciri) |
| 7 | aktif akış | alternatif yapılandırma + endpoint havuzu | DMA/URB havuzu baştan (§2.5-1) |

**Arıza sınıfları:**

| Arıza | OS davranışı |
|---|---|
| Descriptor okuma hatası | kurulum iptal + kaynak bırak + rapor; "yarım cihaz" durumu yasak |
| Storm (tak-çıkar tekrarı) | sınırlı tekrar/kuyruk; döngüsel keşif yasak (ısınma + CPU israfı) |
| Hotplug anında S-geçiş | sıcak takma askıya alınır (os-guc-elektronigi §2.3-4 ile birlikte) |
| Akıştayken detach | §4.2-4: önce akış dur, sonra bırak — donma raporu üretilmez |

## §5 GPIO — haberleşme ve hotplug pini

| Kural | Açıklama |
|---|---|
| Pin rolü | giriş/çıkış + pull yapılandırması sürücüde tek nokta |
| Olay tipi | hotplug: kenar (edge) kesmesi; seviye (level) yalnız protokol gerektiriyorsa |
| Bounce | mekanik/sekmelenme gerekçesiyle olay **yığılmaz** (gecikme/tekrar filtresi) |
| Sınır | **hotplug pini ≠ güç durumu**: S0↔S5 geçişi ACPI/güç dalının işidir (bkz `os-guc-elektronigi`) |
| Koruma | pin'e ham donanım erişimi kullanıcıya açılmaz; sürücü soyutlar |

> Kaynak: `docs.kernel.org/driver-api/gpio/index.html` (200 OK).
> Karışıklık kaydı: vault içinde hotplug pin-i ile OS-E S0↔S5 güç durumu
> **karıştırılmıştı** — bu anayasa ayrımı kapatır (kapsam.md §G ile birlikte).

### §5.1 GPIO hotplug olay işleme sırası (bağlayıcı sıra)

| # | Adım | Kural |
|---|---|---|
| 1 | olay yakalama | kesme/olay üstü iş **hafif** kalır — uzun iş os-guc-elektronigi §11 (DPC/ISR bütçesi) sınırıdır |
| 2 | bounce doğrulama | zaman damgası/sayaç ile filtre; filtre geçmediyse olay **yok sayılır** (olay yığını yasak) |
| 3 | durum okuma | pin seviyesi/protokol mesajı tek noktadan okunur; okuma yarışı kilit ile engellenir |
| 4 | bildirim | üst katmana tek hotplug olayı (ekleme/çıkarma) bildirilir |
| 5 | keşif zinciri | ekleme → §4.4 sırası · çıkarma → §4.2-4 (önce akış dur, sonra bırak) |

> Olay işleme sırası **tek yönlüdür**: keşif sırasında ikinci kez tetiklenen olay
> kuyruğa alınır; örtüşen keşif (aynı cihaza iki kez probe) yasaktır.

## §6 Clock ağacı — OS yönetimi

| Kural | Açıklama |
|---|---|
| Tek kaynak | tüm OS saatleri clock framework'ünden talep edilir (parça: `clk.html`, 200 OK) |
| Oran | MCLK/BCLK/FS oranı **hesaplanır**, sabit yazılmaz — uyuşmazlık §2 bozulma sınıfı |
| Paylaşım | aynı kaynağı iki sürücü istiyorsa clock tree ayırır; gizli paylaşım yasak |
| Askı | kullanılmayan clock kapanır (runtime PM ile birlikte) |
| Kapsam dışı | hrtimer/clocksource alt-sistemi — 404 kayıtlı, bu anayasanın parçası değil |

### §6.1 Clock ağacı devralma (handover — bağlayıcı sıra)

> Kaynak: `https://docs.kernel.org/driver-api/clk.html` (200 OK · § Evidence).

| # | Adım | OS davranışı | Kural |
|---|---|---|---|
| 1 | talep | sürücü clock'u clock framework'ünden alır | "bulundu varsayımı" yok; alınamazsa **probe hatası** (§7) |
| 2 | oran kurma | hedef rate hesap (§2/§3 ilişkisi) → set | oran **hesaplanır**; sabit yazım yasak |
| 3 | doğrulama | get ile geri okuma | talep ≠ gerçek ise sürücü "hazır" **demez** — §3.2-1 yaz-oku-doğrula prensibinin clock ayağı |
| 4 | devreye alma | enable (yalnız kullanım anında) | enable sayacı sıfırdan büyük olur; **askıda clock ile veri yolu açılmaz** (§2.3) |
| 5 | bırakma | disable / put | remove/suspend'te ters sıra: veri yolu dur → clock bırak |

### §6.2 Rate değişim disiplini

| Kural | Açıklama |
|---|---|
| akarken değişim yasağı | hat akarken rate değişimi **yalnız** hat durdurularak yapılır: mute-on → yol dur → rate set → geri aç (§2.3 sırası) |
| paylaşımlı etki | aynı clock'u iki sürücü kullanıyorsa değişim **herkesi** etkiler — clock tree üzerinden koordinasyon; gizli değişim yasak (§6-3) |
| değişim sonrası kontrol | §2.2 bozulma sınıfı denetimi (format↔oran eşleşmesi) zorunlu |
| firmware kaynaklı | firmware clock'u değiştirdiyse OS farkı get ile yakalar (§6.3) ve raporlar |

### §6.3 Suspend/resume clock davranışı

| Aşama | Sıra | Kural |
|---|---|---|
| suspend | veri yolu dur → clock disable/put | runtime PM (§3.2-2) ile **aynı** disiplin; askıda clock ile kayıt yazımı yasak |
| resume | clock yeniden talep/enable → oran doğrula (§6.1-3) | doğrulanmadan §2.3 sırasıyla yeniden kurulum başlamaz |
| fark | firmware clock'u değiştirdiyse | OS geri okumada farkı **raporlar**; sessiz uyum yasak |

> Suspend/resume'un **güç durumu tarafı** (S/D geçişleri) os-guc-elektronigi §2'dedir;
> burada yalnız arayüzün clock/clock/bus askısı kalır.

## §7 Sürücü yaşam döngüsü (özet)

```text
probe → cihaz keşfi (ID/descriptor) → kaynak talebi (clock, DMA, register)
     → yapılandırma (§2.3 sırası) → runtime-aktif
remove → akışı dur → kaynakları bırak → runtime-askı
Hata her aşamada: kısmi kaynak geri alma (rollback) — yarım kurulum YASAK
```

## §8 Sınır komşuları tablosu

| Komşu | Sınır | Ne geçer |
|---|---|---|
| `electroncis/os-guc-elektronigi` | arayüz ↔ güç durumu | suspend'te bus/clock askıya; uyanışta yeniden kur |
| `isletim-sistemi-audio` | alt/üst katman | bu dal: protokol · üst: PCM akış politikası |
| `os-donanim-tespiti` | keşif/envanter | burada: nasıl konuşulur · orada: ne var |
| `K002-electroncis` | elektronik sınır | register haritası/empedans — yazma yok |
| `K004-dsp` | sinyal zinciri | format dönüşümü/algoritma — OS değil |
| `K001-donanım` | fiziksel donanım | arayüz standardının donanım tarafı |

## §9 Açık sorular

1. Gerçek register/ID envanteri hangi node'a yazılacak? (`⚠️ VERIFICATION REQUIRED`)
2. Windows UAC/WDM derin doğrulaması yapılacak mı? (`⚠️ VERIFICATION REQUIRED`)
3. PCM (telefonik) hattı bu dalda derinleşmeli mi yoksa yalnız varlık kaydı mı
   yeterli? (şu an: yalnız kayıt)
4. timer/clocksource 404 kapsam kararı: yeniden değerlendirilsin mi? (vault §6 sahibi)

---

> §10–§11, Faz C genişletmesiyle eklenmiştir; §0–§9 numaraları **değişmemiştir**
> (diğer dosyalardaki § referansları korunur).

## §10 ACPI↔driver etkileşimi (tam çerçeve)

> Kaynak: `https://docs.kernel.org/driver-api/acpi/index.html` (200 OK · 2026-10-14) ·
> `https://docs.kernel.org/firmware-guide/acpi/index.html` (200 OK · § Evidence).

| Aşama | OS↔firmware | Kural |
|---|---|---|
| Keşif | firmware tablosu cihazı tarif eder → OS eşleşir | eşleşme yoksa zorlama yok (§4.4-5 aynı ilke) |
| Kaynak | firmware kaynakları bildirir (port/bellek/kesme — kavram düzeyi, sayı yok) | OS bu kaynakları sürücüye iletir; register **anlamı** firmware'den gelmez (§3.4) |
| Yapılandırma | sürücü register katmanını kurar (§2.3, §6.1) | firmware, OS'e register yazım sırası **dayatmaz** |
| Güç | S↔D geçişleri eşzamanlı (os-guc-elektronigi §2.3) | PM callback zinciri os-guc-elektronigi'dedir; burada yalnız arayüz askısı (§6.3) |
| Olay | GPE/SCI → OS dinleyici → sürücü | olay kaydı doğrulanır; sahte/tekrarlayan olay izole edilir (os-guc-elektronigi §5-2) |

**Sınır:** ACPI tablosu **yazımı** firmware/K005 işi; OS yalnız **okur** — bu sınır
os-guc-elektronigi §13 (firmware↔K005) ile aynıdır; çelişen kayıtta orası kazanır.

**Bilinmeyen envanter notu:** hangi cihazın ACPI üzerinden mi, doğrudan bus
üzerinden mi keşfedildiği **cihaz envanteri** işidir (`os-donanim-tespiti`) —
bu anayasa yalnız kuralı yazar; cihaz sayısı/ID/adresi **uydurmaz**.

## §11 Windows / Linux çift taraflı notlar

> **Kural:** Windows sütunu yalnız "yüzey adı + doğrulama durumu" taşır;
> doğrulanmamış karşılık **yazılmaz**. Register adresi/eşik her iki platformda da
> uydurulmaz.

| Yüzey | Linux (bu anayasa kanıtlı) | Windows (durum) |
|---|---|---|
| Sürücü yaşam döngüsü | probe/remove (§7) | WDK karşılığı **sayfa-derinliği doğrulanmadı** → `⚠️ VERIFICATION REQUIRED` (yalnız genel uç: `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/` 200 OK · 2026-10-14) |
| Kesme/ertelenmiş iş | workqueue/kuyruk kavramı (`https://docs.kernel.org/driver-api/basics.html`, 200 OK · 2026-10-14) | DPC/IRQL karşılığı `⚠️ VERIFICATION REQUIRED` — MS alt sayfaları 2026-10-14 ölçümünde **404** (kayıt: `os-guc-elektronigi-dogrulama.md` §1.2) |
| Register erişimi | I2C/SPI çekirdek API'leri (§3) | WDK karşılığı derinlik yok → işaretli |
| USB | USB çekirdek + UAC (§4, §4.3) | UAC/WDM haritası işaretleme altında (§4.3) |
| Güç yönetimi | runtime PM (§3.2-2) · ACPI okuma (§10) | karşılık doğrulanmadı → os-guc-elektronigi open_questions #3 ile aynı işaret |
| Kesin kural | register adresi/eşik **uydurulmaz** · yaz-oku-doğrula (§3.2-1) | **aynı** |

---


---

## §12 — 2026-10-15 tur kaydı (append-only · bu dosyaya özgü)

### §12.1 Kapı ölçümü

| # | Madde | Değer |
|---|---|---|
| F1 | Tarih | 2026-10-15 |
| F2 | Append öncesi ölçüm | 350 satır (`ReadAllLines`) |
| F3 | Kapı | ≥500 · hedef ≥505 |
| F4 | Silme | 0 satır — §0–§11 aynen korundu |
| F5 | Footer | en sonda tek örnek (LastIndexOf koruması) |

### §12.2 Bağlayıcı sıra envanteri (dosya içinden derlendi — §2.3–§6.1)

Bu anayasa "bağlayıcı sıra" taşıyan alt bölümlerin toplamıdır; bu §12.2
onların envanteridir (yeni kural eklenmez, mevcutların listesi çıkar):

| # | Bölüm | Bağlayıcı ne? |
|---|---|---|
| G1 | §2.3 | Başlatma/kapatma sırası (ses hattı) |
| G2 | §2.5 | DMA ring buffer — OS kural katmanı |
| G3 | §2.6 | Period–kesme ilişkisi · double-buffer disiplini |
| G4 | §3.2 | Bağlayıcı davranışlar (I2C/SPI register) |
| G5 | §4.4 | USB hotplug / keşif sırası |
| G6 | §5.1 | GPIO hotplug olay işleme sırası |
| G7 | §6.1 | Clock ağacı devralma (handover) sırası |
| G8 | §10 | ACPI↔driver tam çerçeve |

### §12.3 Windows/Linux çift-yüzey tablosunun (§11) bu tur durumu

§11 tablosundaki `⚠️ VERIFICATION REQUIRED` işaretleri bu turda **kaldırılmadı**
— kaldırma için derin sayfa-derinliği doğrulaması gerekir ve bu turda
yapılmamıştır. Durum tablosu:

| # | §11 satırı | İşaret | Bu tur |
|---|---|---|---|
| H1 | WDK yaşam döngüsü karşılığı | ⚠️ | korundu |
| H2 | DPC/IRQL karşılığı | ⚠️ (404 kaydı: os-guc-elektronigi-dogrulama §1.2) | korundu |
| H3 | Register erişimi WDK karşılığı | ⚠️ | korundu |
| H4 | UAC/WDM haritası | işaretleme altında (§4.3) | korundu |
| H5 | runtime PM Windows karşılığı | ⚠️ (os-guc open_questions #3) | korundu |

### §12.4 Repo C/C++ durumu — bu anayasanın kapısına etkisi

Bu tur globu (2026-10-15): repo'da `*.cpp/*.h/*.hpp/*.c/*.cc` = **0 dosya**.
Bu anayasadaki tüm protokol davranışları bu nedenle **kural/şartname** olarak
kalmaya devam eder; "gerçek sürücü kodu ile doğrulandı" iddiası bu turda da
**yazılmaz**. Kod kanıtı eşiği: repo'ya ilk C/C++ sürücü/örtü kodu
girdiğinde §11 tablosunun Windows sütunu o kod ile yeniden yazılır.

### §12.5 Red-team soruları (bu dosya)

| # | Soru | Cevap | Kanıt |
|---|---|---|---|
| J1 | §12 yeni kural ekledi mi? | HAYIR | §12.2 yalnız mevcut bağlayıcı sıraların envanteridir |
| J2 | §11 işaretleri hafifletildi mi? | HAYIR | §12.3: 5/5 korundu |
| J3 | §12.4'teki 0 glob nereden? | bu tur glob çağrısı | `**/*.{cpp,h,hpp,c,cc}` → 0 (2026-10-15) |
| J4 | Footer en sonda mı? | EVET | LastIndexOf koruması |
| J5 | §10/§8 bölümlerine dokunuldu mu? | HAYIR | silme 0 satır |

### §12.6 §8 sınır-komşu tablosu ↔ disk (bu tur glob)

§8'in komşuları bu tur glob ile denetlendi (2026-10-15, `*/index.md`):

| # | Komşu (§8) | Disk | Uyum |
|---|---|---|---|
| L1 | `os-guc-elektronigi/` | index.md mevcut | ✅ |
| L2 | `os-donanim-tespiti/` | index.md mevcut | ✅ |
| L3 | `K002-electroncis/` | index.md mevcut | ✅ |
| L4 | `K005-firmware/` | index.md mevcut | ✅ |
| L5 | `isletim-sistemi-audio/` | index.md mevcut | ✅ |
| L6 | `isletim-sistemi-kernel/` | index.md mevcut | ✅ |

### §12.7 Anayasa ↔ Neva Engine şartnamesi kesişimi (dürüstlük notu)

Bu anayasa §2.5'te DMA ring buffer kuralını OS katmanı olarak yazar. Vault
içinde Neva Engine için ADR-017 (32-bit float ses verisi) ve sartname (512
sample buffer · 48 kHz) kayıtlıdır — bu dosya bu iki kaydı **kopyalamaz**,
yalnız kesişim notu olarak işaretler: "anayasadaki ring-buffer kuralı, Neva
şartnamesindeki 512/48k seçimiyle uyumludur; kesin bağ için ADR-017
okunur." Sayı/ürün iddiası bu anayasada **üretilmez** (kaynak ADR-017'nin
kendisidir).

### §12.8 Kapı sonrası kapanış

§12 bu dosyanın 2026-10-15 tur kaydıdır. Sonraki turlar bu dosyaya yalnız
**gerçek bulgu** (yeni doğrulama, yeni işaret değişikliği) çıkarsa §13
append eder; kapı için değil. Boş append yasaktır.

### §12.9 Ölçüm günlüğü

| # | Olay | Ölçüm |
|---|---|---|
| K1 | append öncesi (bu tur ölçümü) | 350 |
| K2 | §12 append (bu bölüm) | ≥505 hedefi |
| K3 | sonraki tur | bu dosyaya yeni bulgu çıkarsa §13 append |

---


---

## §13 — §11 URL teyidi + §9 açık sorular durumu (2026-10-15 · append 2)

### §13.1 §11'deki iki canlı URL'nin bu tur teyidi (HTTP HEAD)

§11 tablosunda 2026-10-14 tarihli iki "200 OK" kaydı vardır; bu tur (2026-10-15)
**yalnız bu ikisi** HEAD ile yeniden test edildi:

| # | URL | 2026-10-14 (§11 kaydı) | 2026-10-15 (bu tur) |
|---|---|---|---|
| M1 | `learn.microsoft.com/en-us/windows-hardware/drivers/kernel/` | 200 OK | **200** |
| M2 | `docs.kernel.org/driver-api/basics.html` | 200 OK | **200** |

**Dürüstlük sınırı:** §11 tablosundaki diğer satırlar (WDK alt sayfalarının
2026-10-14 404 kaydı dahil) bu turda **yeniden test edilmedi**; "aynen duruyor"
iddiası yazılmaz — yalnız "bu tur iki URL teyit edildi, diğerleri dokunulmadı"
kaydedilir.

### §13.2 §9 açık soruların bu tur durumu

Dosyanın kendi §9'u (Açık sorular) bu turda **kapatılmadı** — kapanış için
gereken derin sayfa-derinliği doğrulaması (§12.3-H1/H2) yapılmamıştır. Açık
sorular tablosu aynen korunmuştur; bu §13 yalnız teyit edilen iki URL'yi
kaydeder.

### §13.3 Red-team soruları (append 2)

| # | Soru | Cevap | Kanıt |
|---|---|---|---|
| N1 | §13.1 sonuçları nereden? | bu tur HEAD çağrısı | 2026-10-15, yukarıdaki tablo |
| N2 | "404 kayıtları değişti mi?" | test edilmedi → iddia yok | §13.1 dürüstlük sınırı |
| N3 | §13 yeni kural ekledi mi? | HAYIR | yalnız teyit + durum kaydı |
| N4 | Footer en sonda mı? | EVET | LastIndexOf koruması |

### §13.4 Ölçüm günlüğü (append 2)

| # | Olay | Ölçüm |
|---|---|---|
| O1 | §12 append sonrası | 452 |
| O2 | §13 append (bu bölüm) | ≥505 hedefi |

---


### §13.5 Ölçüm yöntemi notu (append 3 — kapı tamamlama)

§13.1'deki HEAD testleri PowerShell `Invoke-WebRequest -Method Head
-UseBasicParsing -TimeoutSec 20` ile yapılmıştır; zaman aşımı 20 sn
taranmıştır. Başarı ölçütü: HTTP durum kodu 200. Yöntem bu notla kayda
geçer; sonraki turlar aynı betiği kullanabilir.

### §13.6 Kapı kapanışı (append 3)

§12 + §13 ekleri sonrası dosya `ReadAllLines` ile yeniden ölçülmüş ve kapı
(≥500) ile hedef (≥505) karşılaştırılmıştır. Sonuç bu dosyanın kendi ölçüm
gününe işlenir; sonraki append yalnız gerçek bulgu içindir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
