---
node_id: K005-firmware/boot/boot-kapsam
node_type: module
status: draft
parent_id: K005-firmware/boot
children: []
depends_on:
  - K005-firmware
evidence: []
validation: []
open_questions: []
group: mimari
---

# boot — Kademe-2 Kapsam Kaydı

## §A — Kapsam

### §A.1 Rol (ELI15)

`firmware-master.md` §3 satır 1 bu çocuğu **"İlk kod, kendini tanıma ve yükleme
sırası"** olarak yazar. Basitçe: cihaza güç gelir gelmez çalışan en küçük kod
parçası — çipi kendini tanır, belleği hazırlar, ses yolunu sonraki aşamaya
teslim eder. `boot/index.md` ELI15'i ekler: "kötü bir açılış, tüm sistemin
açılmasını engeller — bu yüzden en küçük ve en dikkatli adımdır."

### §A.2 Ne KAPSAR

| # | Kapsam alanı | Ayrıntı | Sınır notu |
|---|---|---|---|
| 1 | Reset vektörü ve ilk talimat akışı | CPU reset sonrası ilk okunan vektör tablosu girişi, initial SP/PC kurulumu | Vektör tablosu yerleşimi (ROM/flash/OTP) donanım hedefine bağlı — UNKNOWN (Q5) |
| 2 | Early system init | Reset → clock doğrulama → bellek (ECC/parity) → C runtime öncesi assembly stub | Clock ağacı *kurulumu* audio-control'un olabilir; boot yalnızca kritik min. clock'u doğrular (Q3) |
| 3 | Bootloader ↔ uygulama image teslimi (hand-off) | Uygulama image'ına kontrol aktarımı; MSP/VTOR yeniden kurulumu sırası | İmza algoritması security dalının; boot yalnız çağırır (Q4) |
| 4 | Boot-time kendini tanıma | Hangi çip, hangi revizyon, hangi image slot geçerli, reset nedeni (reset-cause okuma) | Reset-cause register kategorisi cihaz-ailesine bağlı; somut adres UNKNOWN |
| 5 | Boot-time self-test tetiği | RAM ECC/parity taraması, kritik peripheral ping, watchdog kurulumu | Sürekli teşhis diagnostics dalının; boot yalnız açılış anlık görüntüsü |
| 6 | Boot mediyasından okuma yolu | Flash / OTP / JTAG yükleme yolu firmware tarafı akışı | Medya seçimi açık karar Q5 — bu dosya tek seçenek dayatmaz |
| 7 | Boot-time güvenlik kontrolü kapısı | Update'ten gelen image için doğrulama *çağrısı* (imza + hash) | Doğrulama *mantığı* security; paketin gelmesi update dalının |
| 8 | Boot hata modu davranışı | Boot başarısız → recovery/retry politikası sınırı | Recovery kipi tanımları vault'ta yok — ⚠️ VERIFICATION REQUIRED |

### §A.3 Ne KAPSAMAZ

| # | Dışında | Neden | Sınır komşusu |
|---|---|---|---|
| 1 | Açılıştan sonra dönen kontrol döngüsü (main loop / scheduler) | Boot sona erdiğinde teslim eder | K005-firmware/runtime (master §3/2) |
| 2 | Parçaya sürekli komut verme (aç/kapat/ayarla) | Sürekli kontrol ≠ ilk kurulum | K005-firmware/device-control (master §3/3) |
| 3 | İmza algoritması, kilit bölgesi, key storage | Kriptografik varlıklar ayrı yaşam döngüsü | K005-firmware/security (master §3/8) |
| 4 | OTA paketi indirme, slot yazma stratejisi, geri alma politikası | Güncelleme akışı boot'tan bağımsız tetiklenir | K005-firmware/update (master §3/7) |
| 5 | Burn-in, sürekli log ring, hata kodu sözlüğü | Boot yalnızca açılış anlık self-test'i | K005-firmware/diagnostics (master §3/6) |
| 6 | Uyku/kapanma politikası, akım/ısı sınırları | Boot yaşam döngüsünün *başlangıcı*; kapanış power-control | K005-firmware/power-control (master §3/5) |
| 7 | I2S/TDM register ayarları, codec init sırası | Ses yolu kurulumu ayrı dal | K005-firmware/audio-control (master §3/4) |
| 8 | Host işletim sisteminin sürücü yükleme / POST davranışı | Host ≠ cihaz firmware | K000-isletim-sistemi (master §2.2) |
| 9 | Test fixture, HIL/SIL test senaryoları | Test altyapısı üretim firmware'ından ayrı | K005-firmware/testing |
| 10 | JTAG fiziksel pin şeması / PCB design | Donanım tasarımı K001 alanı | K001-donanım |

### §A.4 Kabul kriteri

| # | Kriter | Ölçüt | Durum |
|---|---|---|---|
| K1 | Reset → uygulama hand-off zinciri kesintisiz | Her reset nedeni (POR / BOR / yazılım watchdog / yazılım) için tanımlı boot yolu | ⚠️ VERIFICATION REQUIRED — reset-cause kategorileri hedef çip kesinleşince |
| K2 | Boot-time ECC/parity hatası yakalanır | Bellek hatası halinde boot uygulamaya geçmez, tanısal duruma düşer | ECC/parity desteği hedef çipe bağlı — UNKNOWN (Q5) |
| K3 | Image doğrulama çağrısı boot'tan security'e tek yönlü | boot → security bağımlılığı; geri dönüş yok (acyclic) | Mimari kural — kod ile doğrulanacak |
| K4 | Boot bütçesi ölçülmüş | Reset → ilk ses örneği arası süre sartname ile karşılaştırılmalı (512 sample / 48 kHz = 10.67 ms tampon) | Ölçüm PLANNED — donanım yok |
| K5 | Boot asla tahmin etmez | Medya/çip/revizyon okunur; sabitlenmez | Zero-hallucination kuralı |
| K6 | Watchdog boot başında kurulur | Uygulama devralmadan önce watchdog aktif | Kurulum sırası tasarım gereği; süre değerleri UNKNOWN |

### §A.5 Bağımlılıklar

| # | Bağımlılık | Yön | Tür | Not |
|---|---|---|---|---|
| D1 | K001-donanım | boot ← K001 | TASARIM | Reset devresi, boot mediya pinleri, JTAG erişimi |
| D2 | K005-firmware/security | boot → security | ÇAĞRI | Image doğrulama çağrısı (tek yönlü) |
| D3 | K005-firmware/update | update → boot | TETİK | Update, boot'a "yeni image doğrula" bayrağı bırakır |
| D4 | K005-firmware/runtime | boot → runtime | TESLİM | Hand-off sonrası kontrol runtime'a |
| D5 | K005-firmware/diagnostics | boot → diagnostics | TETİK | Boot self-test sonucu diagnostics kanalına |
| D6 | K005-firmware/power-control | power-control → boot | SINYAL | Reset/brown-out sinyali boot'u başlatır |

## §B — Bileşen listesi

| # | Bileşen | Sorumluluk | Girdi | Çıktı | Hata modu |
|---|---|---|---|---|---|
| B1 | Reset vektörü girişi | İlk PC değerini sağlar | CPU reset | İlk talimat adresi | Bozuk vektör → CPU exception, boot sürdürülemez |
| B2 | Initial SP kurulumu | İlk stack pointer değeri | Vektör tablosu[0] | MSP başlangıcı | Yanlış SP → ilk fonksiyonda bus fault |
| B3 | C runtime öncesi stub | .data kopyalama, .bss sıfırlama (varsa) | Linker sembolleri | Hazır RAM | ECC açık çiplerde sıfırlama stratejisi ECC'i tetiklemeli |
| B4 | Bellek ECC/parity tarama | Boot-time RAM sağlığı | Bellek aralığı | Sağlam/hatalı bayrağı | Hata → uygulamaya geçiş yasak |
| B5 | Clock doğrulama | Kritik clock'lar beklenen aralıkta mı | RCC/clock status register | Doğrulama sonucu | Yanlış clock → ses örnekleme hatası (audio-control'a sıçrar) |
| B6 | Reset-cause okuma | Neden reset yendi | Reset-cause register | Reset türü enum | Bilinmeyen tür → en güvenli yol (tam init) seçilir |
| B7 | Boot mediya okuyucu | Flash/OTP/JTAG'den image okuma | Medya seçimi | Image blob'u | Okuma hatası → retry/recovery |
| B8 | Image bütünlük kontrolü | Hash/magic kontrolü (imza öncesi katman) | Image + beklenen hash | Bütünlük sonucu | Uyuşmazlık → boot reddi |
| B9 | İmza doğrulama çağrısı | Security dalına hash/imza gönderimi | Image + metadata | Doğrulandı/red | Çağrı başarısız → boot reddi fail-safe |
| B10 | Slot seçici | Hangi image slot'unun geçerli | Update bayrakları (test/confirm) | Slot indeksi | İki slot da geçersiz → recovery yolu (tanımsız ⚠️) |
| B11 | Boot-time self-test | Kritik peripheral ping, RAM testi | Peripheral envanteri | Self-test raporu | Başarısız peripheral → boot ertelenir veya tanısala düşer |
| B12 | Watchdog kurulumu | Uygulama devralmadan önce WDT aktif | Timeout parametresi | Aktif watchdog | Kurulmazsa uygulama kilitlenmesi gözlemlenmez |
| B13 | VTOR yeniden kurulumu | Uygulama vektör tablosunu gösterir | Uygulama VTOR adresi | SCB->VTOR güncellemesi | Yanlış VTOR → ilk interrupt'ta çökme |
| B14 | MSP/stack hand-off | Uygulama initial stack pointer'ını kurar | Uygulama image header | Yeni MSP | Uyuşmaz stack → sessiz bellek bozulması |
| B15 | Boot log / telemetri çıkışı | Boot adımlarını diagnostik porta yazar | Adım olayları | UART/JTAG log | Log yokluğu → boot teşhisi zorlaşır (kayıp kanıt) |
| B16 | Boot bütçesi sayacı | Reset→hand-off süresini ölçer | Timer tick | Süre raporu | Ölçüm yok → K4 kriteri kapanamaz |

### §B.1 Bileşen bağımlılık sırası (boot kritik yolu)

```text
Reset
  → B1 (vektör girişi) → B2 (initial SP)
    → B3 (C-runtime stub)
      → B6 (reset-cause) → B5 (clock doğrula) → B4 (ECC/parity tarama)
        → B7 (mediadan oku) → B8 (bütünlük) → B9 (imza çağrısı, security)
          → B10 (slot seç) → B11 (self-test) → B12 (watchdog kur)
            → B13 (VTOR) → B14 (MSP hand-off) → [uygulama/runtime]
              B15 (log) ve B16 (bütçe sayacı) tüm adımlar boyunca yan akış
```

### §B.2 Sınır karar bekleyenleri (Q3 etkilenen bölgeler)

| Bölge | Belirsizlik | Etkilenen bileşen | Açık soru |
|---|---|---|---|
| Clock kurulumu vs doğrulama | Kimin kurduğu | B5 | Q3: boot-runtime sınırı |
| Peripheral init sırası | Kim hangi peripheral'ı ilk açar | B11 | Q3: boot-device-control sınırı |
| İmza doğrulama sahipliği | Boot mu, security mi tetikler | B9 | Q4 |
| Boot mediyası | Flash / OTP / JTAG hangisi | B7 | Q5 |

## §C — Arayüzler

### §C.1 Boot ↔ Update arayüzü

| Arayüz | Yön | İçerik | Kayıt | Kararlılık |
|---|---|---|---|---|
| Update-incomplete bayrağı | update → boot | "Yeni image var, doğrula ve boot et" | Update dalının yazdığı flash bayrak alanı | ⚠️ alan yerleşimi vault'ta yok |
| Test-boot modu | update → boot | İlk açılış test modunda (revert kapısı) | Bayrak = test | MCUboot benzeri desen — referans, seçim değil |
| Confirm bayrağı | runtime → update | Uygulama kendini "OK" işaretler | Self-test sonrası | ⚠️ onay akışı vault'ta yok |
| Rollback sinyali | boot → runtime | "Geçen seferki image reddedildi" | Boot log + durum enum | Tasarım önerisi |

### §C.2 Boot ↔ Security arayüzü

| Arayüz | Yön | İçerik | Sınır kuralı |
|---|---|---|---|
| Doğrulama çağrısı | boot → security | Image + hash + imza blob'u | Boot algoritmayı bilmez; yalnız çağırır |
| Doğrulama sonucu | security → boot | bool (geçerli/geçersiz) + hata kodu | Fail-safe: hata ≠ geçerli |
| Root-of-trust bayrağı | sabit | Boot zincirinin güven kökü | NIST SP 800-193 RTU kavramı ile hizalı (bkz. §E, satır E5) |

### §C.3 Boot ↔ Runtime arayüzü (hand-off ABI)

| Alan | Tip | Boot'un bıraktığı | Runtime'ın beklediği | Not |
|---|---|---|---|---|
| Initial MSP | word | Uygulama initial stack pointer | İlk push için hazır stack | Vektör tablosu[0] |
| Initial PC | word | Uygulama reset vektörü | İlk talimat | Vektör tablosu[1] |
| VTOR | register | Uygulama vektör tablosu adresi | Interrupt girişleri doğru adreste | SCB->VTOR CMSIS semboli (bkz. §E, satır E1) |
| Boot log handle | pointer/enum | Boot log kanalı durumu | Devam logu aynı kanaldan | Sopranabilir |
| Self-test raporu | struct | Boot self-test sonucu | Runtime başlangıçta okur | Alanları ⚠️ vault'ta tanımsız |
| Reset cause | enum | Reset türü | Runtime kararı (ör. factory vs normal) | Değer seti UNKNOWN |

### §C.4 Boot ↔ Diagnostics arayüzü

| Arayüz | Yön | Sıklık | İçerik |
|---|---|---|---|
| Boot self-test sonucu | boot → diagnostics | Her reset | Pass/fail + başarısız bileşen kimliği |
| Boot-time log kaydı | boot → diagnostics | Her reset | Adım zaman damgaları, boot bütçesi |
| Boot teşhis portu | boot → console | Boot anı | JTAG/UART teşhis çıkışı (alet: OpenOCD — bkz. §E, satır E11) |

### §C.5 Register erişim kategorileri (adres düzeyi bilinmiyor)

| Kategori | Boot'un erişimi | Örnek CMSIS sembolleri | Adres iddiası |
|---|---|---|---|
| Vektör tablosu | Okuma (image header) | Uygulama sembolleri (linker tarafından) | Adres bilinmiyor — linker script'e bağlı, UNKNOWN |
| SCB (System Control Block) | VTOR yazma, reset-cause okuma | `SCB->VTOR` CMSIS semboli | CMSIS sembolleri doğrulandı (§E E1);somut adres yazılmaz |
| SysTick | Boot bütçesi sayacı | `SysTick->LOAD/VAL/CTRL` | CMSIS semboli;somut adres yazılmaz |
| NVIC | Interrupt maskeleme (kritik bölge) | `__disable_irq()` / `__enable_irq()` | CMSIS semboli;somut adres yazılmaz |
| Flash/OTP kontrol registerları | Boot mediyası okuma | Çip ailesine bağlı — isim yok | UNKNOWN (Q5) |
| Reset-cause register | Okuma | Çip ailesine bağlı — isim yok | UNKNOWN (Q5) |
| RCC / clock status | Okuma | Çip ailesine bağlı — isim yok | UNKNOWN (Q5) |

> Kural: bu dosyada hiçbir somut register **adresi** yazılmaz; yalnızca CMSIS
> tarafından standartlaştırılmış **semboller** (`SCB->VTOR`, `SysTick->CTRL`)
> referans gösterilir ve §E'deki CMSIS-Core dokümanı ile desteklenir.
>Somut adres, hedef çip kesinleşince (Q5) kanıt turunda eklenecektir.

## §D — Bağımlılık grafiği

### §D.1 Komşuluk grafiği (YAML benzeri)

```text
K001-donanım
  └─[TASARIM]→ K005-firmware/boot
K005-firmware/boot
  ├─[ÇAĞRI]→ K005-firmware/security       (image doğrulama — tek yönlü)
  ├─[TESLİM]→ K005-firmware/runtime      (hand-off)
  └─[TETİK]→ K005-firmware/diagnostics   (self-test raporu)
K005-firmware/update
  └─[TETİK]→ K005-firmware/boot          (bayrak bırakır)
K005-firmware/power-control
  └─[SINYAL]→ K005-firmware/boot         (reset/brown-out)
```

### §D.2 Kenar tablosu

| # | Kaynak | Hedef | Kenar | Geri dönüş | Döngü riski |
|---|---|---|---|---|---|
| 1 | K001-donanım | boot | TASARIM | Yok | Yok |
| 2 | boot | security | ÇAĞRI | Sonuç (bool) | Veri geri dönüşü; kontrol yok — döngü değil |
| 3 | boot | runtime | TESLİM | Yok | Yok |
| 4 | boot | diagnostics | TETİK | Yok | Yok |
| 5 | update | boot | TETİK | boot log | Döngü yok (tetik→log) |
| 6 | power-control | boot | SINYAL | Yok | Yok |

### §D.3 Sıralı katman (katmanlı mimari)

| Katman | İçerik | boot'un konumu |
|---|---|---|
| Donanım | Reset devresi, boot mediya, JTAG | K001 — boot'un girdisi |
| Boot | İlk kod, kendini tanıma, hand-off | Bu dal |
| Runtime/Control | Scheduler, döngü, komut işleyici | boot'un çıktısı |
| Uygulama/DSP | Ses işleme zinciri | Runtime üzerinde |

### §D.4 Açık sorular (bu dosyadan devralınan)

| # | Soru | Kaynak | Etki |
|---|---|---|---|
| Q3 | boot ↔ runtime ↔ device-control sınırı hangi ölçütlerle ayrılacak | firmware-master §7 | B5/B11/B14 |
| Q4 | Boot-time imza doğrulaması kimin | firmware-master §7 | B9 |
| Q5 | Boot mediyası ve yükleme yolu (flash/OTP/JTAG) | firmware-master §7 | B7, §C.5 |
| R4 | İlk üretim yüklemesi (factory programming) hangi kipte | boot-kapsam özgün §5 | B7 |

## §E — Web araştırma tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| CMSIS-Core, SCB/SysTick/NVIC sembollerini (ör. `SCB->VTOR`) standartlaştırır — boot hand-off registerları bu isimlendirmeyle belgelenir | https://arm-software.github.io/CMSIS_5/Core/html/index.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| Zephyr devicree (DTS) tabanlı yapılandırma, board/boot özelleştirmesinin standart yoludur — boot bileşenleri board tanımından türetilir | https://docs.zephyrproject.org/latest/build/dts/index.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| Zephyr flash & flash_debug kılavuzu, flash yazma/doğrulama ve debug yüklemesini belgeler — boot mediyası okuma referansı | https://docs.zephyrproject.org/latest/develop/flash_debug/index.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| IETF RFC 6234, SHA-1/SHA-2 hash algoritmalarının resmi spesifikasyonudur — boot bütünlük kontrolü (B8) hash referansı | https://www.rfc-editor.org/rfc/rfc6234 | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| IEEE 1149.1 (JTAG), boundary-scan/test erişimi standardıdır — boot yükleme yollarından JTAG'in referans standardı (Q5 kapsamında) | https://standards.ieee.org/ieee/1149.1/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |

## §F — Korunan önceki kayıt (2026-10-12 tarihli, değişiklik yok)

> Aşağıdaki §1–§5, 2026-10-12 kanıt turunun birebir kaydıdır; bu genişletmede
> içerik silinmemiş, korunmuştur. §E'deki 2026-10-13 URL'leri bu kaydın *üstüne*
> eklenmiştir; §1–§5 içindeki eski URL'ler envanter kaynaklıdır ve değiştirilmez.

## §1 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Güç gelince çalışan ilk kod ve sıralama (reset → init → handing-off) | Açılıştan sonra dönen kontrol döngüsü | K005-firmware/runtime (master §3/2) |
| 2 | Kendini tanıma: hangi çip, hangi revizyon, hangi image var | Parçaya komut verme (aç/kapat/ayarla) | K005-firmware/device-control (master §3/3) |
| 3 | Bootloader ↔ uygulama image teslimi (hand-off) ve image doğrulama çağrısı | İmza algoritmasının/kilit bölgesinin kendisi | K005-firmware/security (master §3/8) |
| 4 | Yükleme sırası: flash/OTP'e programlama akışının firmware tarafı | Güncelleme paketinin alınması ve geri alma politikası | K005-firmware/update (master §3/7) |
| 5 | Boot-time kendi kendini test (açılış self-test tetiği) | Sürekli çalışan teşhis ve hata raporu kanalı | K005-firmware/diagnostics (master §3/6) |
| 6 | Boot-time güç durumu kurulumu (reset sonrası ilk güç ayarları) | Akım/ısı sınırları ve uyku-kapanma politikası | K005-firmware/power-control (master §3/5) |
| — | *(dışında)* Host işletim sisteminin sürücü yükleme/POST davranışı | — | K000-isletim-sistemi (master §2.2) |

**Yasak:** bu tablo bir sınır *önerisi* değildir — üst düğümdeki komşu
listesidir; bağlayıcı ayrım kararı (Q3) alınana kadar satır 3–6'daki sınırlar
`⚠️ VERIFICATION REQUIRED` kalır.

## §2 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | boot ↔ runtime ↔ device-control sorumluluk sınırı | **⚠️ Q3 açık** — "kim neyi ilk anda yapar" yazılmamış | firmware-master.md §7 Q3 · boot/index.md open_questions |
| A2 | Boot-time image doğrulama kime ait: boot mu, security mi? | ⚠️ Q4 ile bağlantılı (imza akışı) | firmware-master.md §7 Q4 · security/index.md open_questions |
| A3 | Boot mediya: flash mı, OTP mi, harici flash mı? | **UNKNOWN** — donanım hedefi kesin değil (Q5) | firmware-master.md §7 Q5 |
| A4 | Boot ↔ update sınırı (DFU/recovery kipi boot'ta mı?) | ⚠️ kapsam onayı bekliyor | bagimliliklar.md §3 "update ↔ security" satırı (Q4) |

## §3 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "İlk kod, kendini tanıma ve yükleme sırası" | `.ai/architecture/K005-firmware/firmware-master.md` §3 satır 57 | ✅ dosya okundu |
| 2 | ELI15 = "ilk uyandığı an … en küçük ve en dikkatli adım" | `boot/index.md` (ELI15 bölümü) | ✅ dosya okundu |
| 3 | Tanım yalnız addan türetildi → ⚠️ uyarısı zorunlu | `boot/index.md` ⚠️ bloğu | ✅ dosya okundu |
| 4 | Açık soru Q3 (boot ↔ runtime ↔ device-control) | `firmware-master.md` §7 Q3 · `boot/index.md` open_questions | ✅ dosya okundu |
| 5 | Bağımlılık satırı: K005-firmware → K001-donanım [TASARIM] | `firmware-bagimliliklar.md` §2 satır 27 | ✅ dosya okundu |
| 6 | Risk #4: "boot ↔ runtime ↔ device-control sınırı belirsiz → aynı iş iki yerde" | `firmware-bagimliliklar.md` §4 satır 62 | ✅ dosya okundu |
| 7 | Repo'da K005 boot firmware kodu yok (`shared/src/` içinde firmware/boot dosyası 0) | `shared/src/**` shell taraması (2026-10-12) | ✅ tarandı — **kod kanıtı 0** |
| 8 | Donanım hedefi ve araç zinciri kesin değil (XMOS, sürüm belli değil) | `firmware-master.md` §4 T9 (UNKNOWN) · §7 Q5 | ⚠️ / **UNKNOWN** |
| 9 | Repo'da firmware agent routing satırı var (`'dsp-fw'` domain=firmware) | `shared/src/AI/AIOrchestrator.php` satır 36 | ✅ dosya okundu (Select-Object ile) |
| 10 | Fault önceliği kritik → `dsp-fw` agent'ına yönlendirilir | `shared/src/AI/AIOrchestrator.php` satır 45 | ✅ dosya okundu (Select-Object ile) |

## §4 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | boot ↔ runtime ↔ device-control sınırı hangi ölçütlerle ayrılacak? (Q3) | ⚠️ VERIFICATION REQUIRED |
| R2 | Boot-time imza doğrulaması boot'un mu, security'nin mi işi? (Q4) | ⚠️ VERIFICATION REQUIRED |
| R3 | Boot mediya ve yükleme yolu (flash / OTP / JTAG) — hedef donanım kesin mi? (Q5) | ⚠️ **UNKNOWN** (T9 ile aynı kök) |
| R4 | İlk üretim yüklemesi (factory programming) hangi kipte, kim onaylıyor? | ⚠️ VERIFICATION REQUIRED — vault'ta tanım yok |

**Kapanış:** status: draft kalır. A1–A4 kapanmadan bu dosyada boot davranış
kapsam iddiası (süreç, kip, register) yazılmaz (master §6 D4/D5 · v2 §3 KURAL 3).

## §G — Boot yaşam döngüsü durum matrisi

> Bu matris, boot'un aşama aşama durumlarını ve her durumda izin verilen
> eylemleri tanımlar. Durumlar arasındaki geçişler yalnızca §G.2'deki
> koşullarla yapılır; tanımsız geçiş yasaktır.

### §G.1 Durum tanımları

| Durum | Ad | Açıklama | Çıkış koşulu | Hata davranışı |
|---|---|---|---|---|
| S0 | RESET | CPU reset vektörü çalıştı, ilk talimat | B1 tamam | Sürülemez → donma (fail-stop) |
| S1 | SP_INIT | Initial SP kuruldu (B2) | B2 yazımı doğrulandı | Yanlış SP → bus fault → fail-stop |
| S2 | RT_INIT | C-runtime stub: .data kopya, .bss sıfır (B3) | B3 sonlandı | Bellek hatası → S9 (tanısal) |
| S3 | SELF_AWARE | Reset-cause + çip/revizyon tanındı (B6) | Çip kimliği okundu | Bilinmeyen revizyon → en güvenli yol |
| S4 | MEM_TEST | ECC/parity tarama (B4) | Bellek sağlam | Hata → S9; S9'a geçiş yasak değil zorunlu |
| S5 | CLOCK_CHECK | Kritik clock doğrulama (B5) | Clock aralıkta | Dışında → S9 |
| S6 | IMAGE_LOAD | Boot medyasından okuma (B7) | Image okundu | Okuma hatası → retry (S6 içinde max N) |
| S7 | IMAGE_VERIFY | Bütünlük + imza çağrısı (B8, B9) | security → true | false → S8 |
| S8 | REJECT | Image reddedildi | Recovery yolu (tanımsız ⚠️) | Fail-safe: eski image'a dön veya tanısala |
| S9 | DIAG | Tanısal durum | Teşhis portuna rapor yazıldı | Bekleme / tekrar dene (tasarım kararı ⚠️) |
| S10 | HANDOFF | VTOR + MSP kurulumu (B13, B14) | Uygulama reset vektörüne jump | Jump sonrası boot sorumluluğu biter |
| S11 | RUNTIME | Uygulama/runtime kontrolünde | — (boot bitti) | — |

### §G.2 Geçiş tablosu

| # | Kaynak | Hedef | Koşul | Guard |
|---|---|---|---|---|
| T1 | S0 | S1 | İlk talimat tamamlandı | — |
| T2 | S1 | S2 | SP yazımı okunarak doğrulandı | Doğrulama başarısız → fail-stop |
| T3 | S2 | S3 | .data/.bss tamam | Bellek hatası → S9 |
| T4 | S3 | S4 | Reset-cause okundu, çip kimliği eşleşti | Eşleşmiyor → en güvenli init dalı |
| T5 | S4 | S5 | ECC/parity temiz | Hata var → S9 |
| T6 | S5 | S6 | Clock doğrulandı | Dışında → S9 |
| T7 | S6 | S7 | Image okundu, magic eşleşti | Magic uyuşmazlık → retry → S8 |
| T8 | S7 | S10 | security → true | false → S8 |
| T9 | S7 | S9 | Doğrulama çağrısı kendisi hata verdi | fail-safe: S9 |
| T10 | S8 | S9 | Reddedilen image için teşhis | Zorunlu log |
| T11 | S8 | S6 | Revert / eski slot seçimi (update dalı) | Yalnız update bayrağı ile |
| T12 | S4 | S9 | ECC/parity hatası | Asla S10'a atlamaz |
| T13 | S9 | S6 | Manuel/tekrar deneme (telemetri onayı) | retry sınırı ⚠️ tanımsız |
| T14 | S10 | S11 | Jump tamamlandı | Boot sorumluluğu biter |

### §G.3 Sayaçlar ve sınırlar

| Sayaç | Amaç | Sınır | Aşım davranışı |
|---|---|---|---|
| Image retry (S6) | Okuma/magic hatası | ⚠️ tanımsız (tasarım kararı) | Sınır aşımı → S8 |
| Verify retry (S7) | Doğrulama çağrısı hatası | 1 (retry yok; doğrulama güvenilir olmalı) | S9 |
| Boot bütçe sayacı (B16) | Reset→hand-off süresi | sartname: 512 sample / 48 kHz = 10.67 ms tampon bütçesi içinde | Bütçe aşımı loglanır; boot durdurulmaz (ölçüm amaçlı) |
| Watchdog (B12) | Boot asılı kalmasın | ⚠️ süre UNKNOWN | Reset → S0 |

## §H — Boot-time hata kodu ön taslağı (kodlar bağlayıcı DEĞİL)

> Bu tablo bir **ön taslaktır**: hata kodu *dağılımı* diagnostics dalının
> hata kodu sözlüğü ile hizalanmalıdır (diagnostics-kapsam §C). Kodlar bu
> dosyada kesinleştirilmez — numara alanı tahsisi ⚠️ VERIFICATION REQUIRED.

| Kod alanı | Aralık (taslak) | Kapsam | Boot kaynağı | Yönlendirme |
|---|---|---|---|---|
| BOOT-0xxx | 0001–0099 | Bellek (ECC/parity/SP) | B2, B3, B4 | S9 |
| BOOT-0100–0199 | — | Clock doğrulama | B5 | S9 |
| BOOT-0200–0299 | — | Boot medyası okuma | B7 | retry → S8 |
| BOOT-0300–0399 | — | Image bütünlük/magic | B8 | S8 |
| BOOT-0400–0499 | — | İmza doğrulama çağrısı | B9 | S9 (çağrı hatası) / S8 (geçersiz imza) |
| BOOT-0500–0599 | — | Slot seçimi | B10 | S8 |
| BOOT-0600–0699 | — | Self-test başarısızlığı | B11 | S9 |
| BOOT-0700–0799 | — | Watchdog kurulumu | B12 | S9 |
| BOOT-0800–0899 | — | Hand-off (VTOR/MSP) | B13, B14 | fail-stop |
| BOOT-0900–0999 | — | Bilinmeyen reset nedeni | B6 | S3 (güvenli init) |

**Yasak:** Bu aralıklar uydurulmuş kesin kodlar değildir; numara tahsisi
diagnostics sözlüğü onaylanana kadar `⚠️` kalır. Sözlük onaylanınca bu tablo
`diagnostics-dogrulama.md` §D ile birleştirilir.

## §I — Sınır karar matrisi (Q3/Q4/Q5'e kadar bağlayıcı olmayan)

| Karar | Önerilen sahip | Gerekçe | Alternatif | Bağlayıcılık |
|---|---|---|---|---|
| Clock *doğrulama* (tek kritik clock) | boot | Boot ses yolundan önce clock sağlığını bilmeli | runtime | ⚠️ Q3 |
| Clock *kurulumu* (tam ağacı) | audio-control | I2S/TDM/MCLK ilişkisi ses dalının uzmanlığı | boot | ⚠️ Q3 |
| Reset-cause okuma | boot | Boot'un ilk işi | diagnostics | ⚠️ Q3 |
| Peripheral *ilk* ping | boot (yalnız kritik) | Boot'un self-test'i | device-control | ⚠️ Q3 |
| Peripheral *init sırası* | device-control | Hot-plug/sequencing bilgisi orada | boot | ⚠️ Q3 |
| İmza doğrulama *çağrısı* | boot | Zincirin parçası | — | ⚠️ Q4 (çağrı) |
| İmza doğrulama *algoritması* | security | Kriptografik uzmanlık + key storage | — | ⚠️ Q4 (mutabık) |
| Rollback/bayrak *politikası* | update | Yaşam döngüsü orada | boot | ⚠️ kapsam |
| Boot log *kanalı* | diagnostics | Kanal/protokol orada | boot | ⚠️ Q3 |
| Boot log *üretimi* | boot | Boot'un kendi olayları | — | Mutabık |

## §J — Terminoloji (bu dosyaya özgü)

| Terim | Tanım | İlk geçtiği yer |
|---|---|---|
| Hand-off | Boot'un kontrolü runtime'a resmen teslimi (VTOR+MSP+jump) | §C.3 |
| Reset-cause | Son reset'in nedenini gösteren register/enum | B6 |
| Early init | C-runtime öncesi assembly stub dahil ilk kurulum | B3 |
| Boot bütçesi | Reset→hand-off için izin verilen süre (ölçüm) | B16 |
| Fail-stop | Hata halinde ilerlemeyi reddetme (en güvenli) | G.1 S0 |
| Fail-safe | Hata halinde güvenli tarafta kalma (ör. doğrulama hatası → red) | §C.2 |
| Test-boot | Update sonrası ilk açılış, confirm bekleyen mod | §C.1 |
| Revert | Reddedilen/boot etmeyen image'dan eski image'a dönme | §C.1 |
| Root of trust | Boot zincirinin güven kökü (RTU, NIST SP 800-193 kavramı) | §C.2 |
| somut adres |Somut, elle yazılmış register adresi — bu dosyada yasak | §C.5 |

## §K — İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `K005-firmware/boot/boot-dogrulama.md` | Bu dosyadaki iddiaların kanıt/ölçüm tablosu |
| `K005-firmware/boot/boot-ornekler.md` | Bileşen/durum matrislerinin kod ve yapılandırma örnekleri |
| `K005-firmware/boot/index.md` | Klasör künyesi (dokunulmadı) |
| `K005-firmware/firmware-master.md` | §3 satır 1 (rol) · §7 Q3/Q4/Q5 (açık sorular) |
| `K005-firmware/firmware-bagimliliklar.md` | §2/27 (K001 TASARIM) · §4/62 (Risk #4) |

## §L — Boot zaman bütçesi taslağı (ölçüm PLANNED)

> Sartname: 512 sample / 48 kHz = **10.67 ms** tampon. Boot bütçesi bu
> pencereye sığmalı veya en azından ölçülür olmalıdır. Aşağıdaki süreler
> **taslak tahsis**tır; gerçek ölçüm donanım gelince yapılır (ölçüm PLANNED).

| Aşama | Bileşen | Tahsis (taslak) | Ölçüm yöntemi | Belirsizlik |
|---|---|---|---|---|
| S0→S1 SP init | B1, B2 | 1 us altı sınıfı (talimat başına) | SysTick (B16) | Tahsis varsayımı |
| S1→S2 RT init | B3 | Bellek boyutuna bağlı, lineer | SysTick | RAM boyutu UNKNOWN (Q5) |
| S2→S3 self-aware | B6 | Register okuma, us sınıfı | SysTick | Reset-cause register UNKNOWN |
| S3→S4 mem test | B4 | Bellek boyutu / test genişliği | SysTick + kaba sayaç | Test algoritması (march vs pattern) seçili değil |
| S4→S5 clock check | B5 | Clock lock beklemesi (PLL) | SysTick | PLL lock süresi çipe bağlı UNKNOWN |
| S5→S6 image load | B7 | Medya boyutu / arayüz hızı | SysTick | Flash/OTP/JTAG kararı açık (Q5) |
| S6→S7 verify | B8, B9 | Hash + imza doğrulama hesabı | SysTick | Algoritmaya bağlı (RSA-2048 vs ECDSA) — security dalı |
| S7→S10 hand-off | B13, B14 | Kayıt yazımı, us sınıfı | SysTick | — |
| Toplam | — | Tahsislerin toplamı ≤ bütçe hedefi | B16 sayacı | **Ölçüm PLANNED** |

**Kural:** tahsis toplamı bütçeyi aşarsa boot *durdurulmaz*; yalnız loglanır
(K4 ölçüm amacı). Aşım kalıcıysa öncelik sırası: mem-test genişliği → image
doğrulama algoritması → medya yolu gözden geçirilir (Q5 kararı ile).

## §M — Kabul kriteri ↔ bileşen haritası

| Kriter | İlgili bileşen(ler) | İlgili durum | Doğrulama dosyası |
|---|---|---|---|
| K1 kesintisiz zincir | B1–B14 | S0–S10 | boot-dogrulama §A senaryo DV-B1 |
| K2 ECC/parity yakalama | B4 | S4 | boot-dogrulama §A senaryo DV-B2 |
| K3 tek yönlü security bağımlılığı | B9 | S7 | boot-dogrulama §B statik analiz |
| K4 bütçe ölçümü | B16 | tümü | boot-dogrulama §C ölçüm yöntemi |
| K5 tahmin yok | B6, B7, B10 | S3, S6 | boot-dogrulama §A senaryo DV-B5 |
| K6 watchdog kurulumu | B12 | S10 öncesi | boot-dogrulama §A senaryo DV-B6 |

## §N — Değişiklik günlüğü (bu genişletme)

| Tarih | Değişiklik | Yapan | Not |
|---|---|---|---|
| 2026-10-12 | İlk kanıt turu yazımı (§1–§5) | Vault oturumu | Mevcut içerik |
| 2026-10-13 | Frontmatter v2 §4.2 + group'a taşındı (parent → parent_id) | K005 alt-modül derin-dal | Eski `title/type/parent` alanları kaldırıldı, içerik korundu |
| 2026-10-13 | §A–§N bölümleri eklendi (kapsam, bileşen, arayüz, grafik, durum matrisi, hata kodu ön taslağı, sınır matrisi, terminoloji, bütçe, harita) | K005 alt-modül derin-dal | Eski §1–§5 §F altında korundu |
| 2026-10-13 | §E web araştırma tablosu eklendi (5 URL, HTTP 200 HEAD) | K005 alt-modül derin-dal | Envanter kesişimi 0 |

## §O — Risk matrisi (bu dal bazında)

| # | Risk | Olasılık | Etki | Mitigasyon | Sahip | Durum |
|---|---|---|---|---|---|---|
| RSK-1 | boot ↔ runtime ↔ device-control sınırı belirsiz → aynı iş iki yerde (Risk #4) | Yüksek | Orta | Q3 kararı bekleniyor; §I matrisi geçici | architect | Açık |
| RSK-2 | Boot medyası yanlış varsayım → tüm B7 tasarımı boşa | Orta | Yüksek | Q5 kararı beklenmeden medya somutlanmaz | architect | Açık |
| RSK-3 | İmza doğrulaması iki yerde → iki gerçeklik | Orta | Yüksek | Q4: çağrı boot, algoritma security (§I) | architect | Açık (mutabık yön) |
| RSK-4 | Bellek testi boot'u bütçe dışına itiyor | Orta | Orta | §L bütçe tahsisi + ölçüm PLANNED | performance-engineer | Açık |
| RSK-5 | ECC/parity desteklenmeyen çipte B4 anlamsız | Orta | Orta | Çip kesinleşince B4 kapsamı yeniden | embedded-engineer | Açık |
| RSK-6 | Reset-cause register aileler arası farklı → B6 soyutlanmalı | Yüksek | Düşük | B6, çip-spesifik adapter arayüzü ile | embedded-engineer | Açık |
| RSK-7 | Watchdog boot'ta kurulmuyor → asılı boot gözlenmez | Düşük | Yüksek | B12 kritik yolda zorunlu (G.1 S10 öncesi) | embedded-engineer | Mutabık yön |
| RSK-8 | Boot log kanalı tanımsız → boot teşhisi kör | Yüksek | Orta | §C.4: kanal diagnostics, üretim boot | diagnostics sahibi | Açık |
| RSK-9 | Factory programming kipi tanımsız (R4) | Orta | Orta | Üretim öncesi karar zorunlu | product-owner | Açık |
| RSK-10 | Repo'da boot kodu 0 → tüm iddialar tasarım seviyesinde | Kesin | Orta | Durum: draft; kod kanıtı gelene kadar iddia sınırlı | Vault Steward | Bilinen durum |

### §O.1 Risk → bölüm izlenebilirliği

| Risk | İlgili bölüm | İlgili bileşen |
|---|---|---|
| RSK-1 | §A.2, §I | B5, B11 |
| RSK-2 | §A.5 D1, §C.5 | B7 |
| RSK-3 | §C.2, §I | B9 |
| RSK-4 | §L, §A.4 K4 | B16 |
| RSK-5 | §A.4 K2, §B | B4 |
| RSK-6 | §B | B6 |
| RSK-7 | §G.1 S10, §B | B12 |
| RSK-8 | §C.4 | B15 |
| RSK-9 | §D.4 R4 | B7 |
| RSK-10 | §F §3 disk kanıtları | — |

### §O.2 Sonraki turda ne kapanır

| Sıra | Kapanacak | Kapanış koşulu | Bağımlı |
|---|---|---|---|
| 1 | Q5 (hedef donanım/araç zinciri) | Kullanıcı kararı + T9 kapanışı | B4, B6, B7, §C.5, §L |
| 2 | Q3 (boot/runtime/device-control sınırı) | §I matrisinin onayı | B5, B11 |
| 3 | Q4 (imza sahipliği) | security dalı ile mutabakat | B9 |
| 4 | R4 (factory programming) | Üretim akışı kararı | B7 |
| 5 | Boot kodu kanıtı | İlk firmware kodu commit'i | Tüm iddialar (draft → review) |

## §P — Doğrulama bağlantı özeti (boot-dogrulama.md'ye köprü)

| Bu dosya iddiası | Kanıt türü | boot-dogrulama karşılığı | Beklenen durum |
|---|---|---|---|
| Reset→hand-off zinciri (§G) | Tasarım + ölçüm | §A senaryo DV-B1 | Ölçüm PLANNED |
| ECC/parity zorunluluğu (K2) | Donanım bağımlı | §A senaryo DV-B2 | UNKNOWN (Q5) |
| security tek yönlü bağımlılık (K3) | Mimari | §B statik kontrol | Statik analiz PLANNED |
| Boot bütçesi (§L, K4) | Ölçüm | §C ölçüm yöntemi | Ölçüm PLANNED |
| Tahmin yokluğu (K5) | Süreç | §A senaryo DV-B5 | Süreç denetimi |
| Watchdog kurulumu (K6) | Tasarım | §A senaryo DV-B6 | Ölçüm PLANNED |
| Hata kodu ön taslağı (§H) | Sözlük bağımlı | §D eşik/sözlük | diagnostics sözlüğü bekleniyor |
| Durum matrisi tutarlılığı (§G) | Tasarım incelemesi | §B test stratejisi | Tasarım incelemesi PLANNED |
| Sınır kararı matrisi (§I) | Mimari onay | §B statik kontrol | Q3/Q4 kararı bekleniyor |
| Risk matrisi güncelliği (§O) | Süreç | §D referans değerler | Her tur yeniden değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
