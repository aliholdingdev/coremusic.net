---
node_id: K000-isletim-sistemi/electroncis/donanim-arayuz-protokolleri/donanim-arayuz-protokolleri-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis/donanim-arayuz-protokolleri
children: []
depends_on: [K002-electroncis, K001-donanım, K000-isletim-sistemi/isletim-sistemi-audio]
evidence:
  - "C:\www\coremusic.net\shared\src\Security\UuidV7.php:22-23 (pack('N') / pack('n') · 2026-10-14 okundu)"
  - "C:\www\coremusic.net\home.coremusic.net\include\Stream\MusicStreamHandler.php:185,189,199 (is_link/realpath/separator · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/spi.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/clk.html (200 OK · 2026-10-14)"
validation: "2026-10-14 · yazıldı (Vault Steward Q1 kararı) · repo C/C++ = 0 → şartname/pseudocode · sahte kaynak kod YOK · red-team bekliyor · 2026-10-14 · genişletme (Faz C)"
open_questions:
  - "⚠️ VERIFICATION REQUIRED: pseudocode blokları kaynak-kod tırnağı değildir; gerçek kernel tırnağı isterse §7 planı (200 OK'lu URL'lerden)."
  - "Repo'ya C/C++ girerse ilk güncellenecek dosya: bu dosya + electroncis-ornekler.md §1 dürüstlük tablosu."
group: kod
---

# Örnekler — donanim-arayuz-protokolleri (şartname/pseudocode)

> **`electroncis` yazım hatası dokunulmaz** (v2 §3 KURAL 1).

## §0 ELI15

Bu dosya, `-anayasa.md` kurallarını **çalışır şekle** döker. Repo'da C/C++ bulunmadığı
için (0 glob) gerçek sürücü kodu **uydurulmaz**: her blok ` {niyet}` etiketli,
davranış şartnamesidir. Kök `electroncis-ornekler.md` genel çerçeveyi verir; burada
alt dala özgü **SPI, clock hesabı, probe/remove, hata-rollback, TDM slot** ve
**runtime PM** senaryoları derinleştirilir. Gerçek PHP satırları yalnız
**bayt/yol mantığı** bağlamında referanstır.

## §1 Dürüstlük kaydı

| Öğe | Durum |
|---|---|
| C/C++ repo dosyası | **0** (2026-10-14) |
| Sahte "kernel kodu" tırnağı | **YOK** (uydurma yasak) |
| Register adresi/eşik | **YOK** (uydurulmaz; cihaz işi K002) |
| Pseudocode | ` {niyet}` etiketli, davranış düzeyi |
| PHP referansı | satır-kanıtlı (§6) |

## §2 SPI register yaz-oku (anayasa §3 genişletme)

```text
{niyet} SPI register erişimi (OS sürücü gözü)
1. cs_assert(hat)                 → chip-select açıldı (CS cihaz işidir)
2. tx: [reg_adresi | bayt]        → register adresi genişliği cihaza göre (UYDURMA YOK)
3. rx: veri baytları               → aynı transferde gelen (full-duplex)
4. cs_deassert(hat)
Doğrulama kuralı (anayasa §3.2-1): yazma sonrası okuma → yazılan değer okunmalı;
fark varsa hata: sürücü "yazıldı" demez.
Eşzamanlı: aynı CS'e iki erişim → sürücü içi lock; paralel erişim YASAK.
```

## §3 Clock oranı hesabı (anayasa §6 genişletme)

```text
{niyet} Ses clock ilişkisi — hesap, sabit DEĞİL
Girdi: fs (örnekleme hızı), kanal sayısı, slot genişliği, hattın master/slave rolü
Çıktı:  BCLK = fs × kanal × slot_bits   (kural, cihaz-manueliyle doğrulanır)
        FS   = fs                        (çerçeve = örnekleme)
        MCLK = fs × MCLK-yönetimi        (OS clock ağacından talep; oran bağlayıcı değil)
Uyuşmazlık → anayasa §2.2 bozulma sınıfı (tıklama/susma/bit hatası)
Clock kaynağını OS "açar/kapar"; asla clock gizlice paylaşılır (anayasa §6-3).
```

## §4 TDM slot düzeni şartnamesi

```text
{niyet} TDM'de çok kanal yerleşimi
1. slot genişliği ve slot sayısı belirlenir (format ile eşit)
2. her slot → mantısal kanal eşlemesi (sürücüde tek tablo)
3. kullanılmayan slot: sessiz/zero — yarım slot'a veri yazılmaz
4. FS çerçevesi tüm slot kuşatır; kuşatma tutmazsa hat bozulur (§2.2)
Uyarı: slot sayısına/düzenine İLİŞKİN cihaz değeri bu vault'ta YOK (envanter yok).
```

## §5 Sürücü probe/remove + rollback

```text
{niyet} Yaşam döngüsü ve hata geri alma (anayasa §7)
probe:
  a. cihaz keşfi (ID/descriptor eşleşmesi)      → eşleşme yok: çıK, değerli hata
  b. kaynak talebi: clock · DMA · register erişimi
  c. kısmi başarısızlıkta ROLLBACK (b'de alınanları tek tek bırak)
  d. yapılandırma (anayasa §2.3 sırası) → runtime-aktif
remove:
  a. akışı durdur (mute → yol dur)
  b. DMA/clock/register serbest → runtime-askı
YASAK: yarım kurulumla çalışma (c atlanırsa sürücü "hazır" sayılmaz)
```

### §5.1 Hata yolu senaryoları

| Senaryo | Sinyal | OS davranışı |
|---|---|---|
| Cihaz yok (I2C) | `ENXIO` | hotplug bekleme listesi; döngüleme yok |
| Zaman aşımı | `ETIMEDOUT` | sınırlı tekrar → hata döndür |
| Yaz-oku farkı | doğrulama hatası | "yazıldı" yok → rollback (§5-c) |
| USB detach (akışta) | port olayı | önce akış dur → bırak (anayasa §4.3) |

## §6 Runtime PM + repo gerçekliği

```text
{niyet} Runtime PM akışı
aktif kullanım var → runtime-get (bus/clock açık)
kullanım bitti     → runtime-put (askı) — anayasa §3.2-2: sürekli AÇIK bus = güç israfı
```

**Repo gerçekliği (PHP · satır-kanıtlı · 2026-10-14):**

| Referans | Ne gösterir |
|---|---|
| `shared/src/Security/UuidV7.php:22-23` → `pack('N',...)` / `pack('n',...)` | bayt düzeyi paketleme — register baytı mantığının PHP ayağı (OS değil, uygulama) |
| `home.coremusic.net/include/Stream/MusicStreamHandler.php:185,189,199` | `is_link`/`realpath`/`DIRECTORY_SEPARATOR` — yol çözümleme FS-arayüzüne komşu |

> Bu PHP satırları **OS sürücüsü değildir**; yalnız "bayt/yol disiplini"nin
> uygulama katmanındaki karşılığıdır. OS katmanı için tek kanıt sınıfı web
> dokümanları (§ Evidence) + şartnamedir.

## §7 Kaynak kod tırnağına geçiş planı (bekleyen)

| Adım | Koşul | Kaynak |
|---|---|---|
| 1 | Vault onayı | red-team turu |
| 2 | 200 OK'lu URL'lerden bölüm seçimi | `docs.kernel.org/sound/index.html` · `driver-api/{i2c,spi,usb,gpio,clk}.html` |
| 3 | Tırnak + satır/URL kanıtı ile `-ornekler` güncelleme | `⚠️ VERIFICATION REQUIRED` kaldırılır |
| 4 | Repo'ya C/C++ girişi | dürüstlük tablosu (§1) yeniden ölçülür |

## §8 Sınır komşuları tablosu

| Komşu | Ne alır / verir |
|---|---|
| `electroncis/electroncis-ornekler.md` | genel çerçeve (§Örnek A–G) — bu dosya derinleştirme |
| `-anayasa.md` | bu dosyanın kuralları oradan gelir (bağlayıcı) |
| `-dogrulama.md` | bu dosyadaki iddiaların kanıtı |
| `os-guc-elektronigi/-ornekler.md` | güç senaryoları (S3/wake) — burada değil |
| `K002-electroncis` | register haritası/datasheet — sınır |
| `isletim-sistemi-audio` | üst katman akış politikası |

## §9 Açık sorular

1. Gerçek kernel tırnaklarına geçiş onayı var mı? (§7)
2. TDM slot değerleri envanter girerse hangi dosya güncellenir? (bu §4 + §1 tablosu)
3. PHP "bayt/yol" örnekleri bu alt dala mı ait, kök ornekler'de mi kalmalı?
   (şu an: iki yerde farklı açıyla — tekrar sayımında kalemleştirilmeli)

---

> §10–§13, Faz C genişletmesiyle eklenmiştir; §0–§9 numaraları **değişmemiştir**
> (diğer dosyalardaki § referansları korunur).

## §10 Device-tree / ACPI cihaz kayıt örneği (pseudocode)

```text
{niyet} firmware'in cihazı OS'e tarif ettiği iki yüzey (anayasa §3.4 · §10)
Girdi: firmware tablosu (ACPI) veya device-tree düğümü — OS yalnız OKUR
Çıktı : sürücüye iletilen "yer" bilgisi (kavram düzeyi — sayısal kaynak YOK)

ACPI yolu:
  1. firmware tablosu cihazı tarif eder (cihaz düğümü + fonksiyon)
  2. OS tabloyu okur → uygun sürücüyle eşleştirir (eşleşme yok: zorlama YOK)
  3. firmware'in bildirdiği kaynaklar (port / bellek alanı / kesme) sürücüye iletilir
  4. register ANLAMI firmware'den gelmez — sürücü katmanı §2.3 sırasıyla kurulur

Device-tree yolu:
  1. firmware düğümü: uyum (compatible) + bağlantı (phandle) + özellik listesi
  2. OS ağacı okur → eşleşen sürücüyü bulur
  3. özellik okunur → sürücüye iletilir; özellik YOKSA sürücü tahmin ETMEZ
  4. firmware'in yazdığı alan OS tarafından değiştirilmez (yalnız okuma)

Ortak kural:
  · firmware OS'e YER verir, ANLAM vermez        [anayasa §3.4]
  · firmware tarif etmediği yüzey OS'de açılmaz  [zorlama yok]
  · sayısal kaynak/adres bu vault'ta YOK          [UNKNOWN · K002/envanter işi]
```

## §11 Hotplug keşif akışı (pseudocode — anayasa §4.4/§5.1 ile hizalı)

```text
{niyet} tak-çıkar olayının tek yönlü keşif zinciri
Girdi: GPIO/USB port olayı · Çıktı: üst katmana tek ekleme/çıkarma bildirimi

olay_yakala(pin):
  1. hafif iş: zaman damgası + sayaç kaydet   [uzun iş → os-guc-elektronigi §11 bütçesi]
  2. bounce doğrula → geçmediyse olayı YOK SAY (olay yığını yasak)   [anayasa §5.1-2]
  3. keşif kuyruğuna tek kayıt koy; örtüşen keşif (aynı cihaza 2. probe) engellenir

kesif_calistir(kayit):
  a. port reset + adres atama                 [OS yığını tek noktadan · sürücü karışmaz]
  b. descriptor/düğüm okuma zinciri           [yarım kalırsa: "tanımsız" → kurulum YOK]
  c. sınıf/uyum eşleşmesi                     [eşleşme yok → yalnız kayıt, zorlama yok]
  d. probe: clock · DMA · register talebi     [kısmi başarısız → ROLLBACK (§5-c)]
  e. aktif yapılandırma (§2.3 sırası)         [akarken clock açmak YASAK]

cikar_kesif(kayit):
  1. önce akış dur (mute → yol dur)           [anayasa §4.2-4]
  2. DMA/clock/register bırak → runtime-askı  [§6 · §6.3]
  3. üst katmana tek ÇIKARMA bildirimi

arıza_sınıfları:
  · descriptor yarımdır     → kurulum iptal + kaynak bırak + rapor
  · tak-çıkar fırtınası     → sınırlı tekrar/kuyruk; döngüsel keşif yasak (ısınma)
  · hotplug anında S-geçişi → sıcak takma askıya alınır (os-guc-elektronigi §2.3-4)
```

## §12 I2C/SMBus erişim PHP örneği (strict_types)

> **Sınır:** bu blok **uygulama katmanı eşleğidir — donanıma/OS'ye erişmez.**
> Repo'da I2C/ACPI/thermal karşılığı **0 isabet** (grep-0, 2026-10-14 ölçüldü);
> register adresi/7-bit slave değeri **bu örnekte yok** (K002/datasheet işi).
> Yazılan kural `-anayasa.md` §3.1 · §3.2-1 · §3.2-3 · §3.2-4'ten gelir.

```php
<?php declare(strict_types=1);

/**
 * {niyet} I2C/SMBus yaz-oku-doğrula disiplini — uygulama katmanı modeli (OS DEĞİL).
 *
 * Kural kaynakları: -anayasa.md §3.1 (adresleme), §3.2-1 (yaz-oku-doğrula),
 * §3.2-3 (eşzamanlı erişim tek noktadan), §3.2-4 (hot-path yasak).
 */
final class I2cRegisterSpec
{
    /** @var array<int, int> gölge kayıt — gerçek okuma sürücü işidir, burada model */
    private array $shadow = [];

    /** @var list<string> denetim izi */
    private array $trace = [];

    public function __construct(private readonly int $slaveAddress)
    {
        // §3.1: bus üzerinde slave adresi — 7-bit aralığı dışına ÇIKMAZ.
        if ($slaveAddress < 0 || $slaveAddress > 0x7F) {
            throw new InvalidArgumentException('slave address outside 7-bit range');
        }
    }

    /** §3.2-1 — yazma, okuma ile DOĞRULANMADAN "yazıldı" sayılmaz. */
    public function writeRegister(int $index, int $value): void
    {
        if ($index < 0 || $value < 0 || $value > 0xFF) {
            throw new InvalidArgumentException('register index/value outside byte range');
        }
        $this->shadow[$index] = $value;
        $this->trace[] = sprintf('W idx=%d val=%s', $index, dechex($value));
    }

    public function readRegister(int $index): int
    {
        if (array_key_exists($index, $this->shadow) === false) {
            throw new RuntimeException('unread register: ' . $index);
        }
        $this->trace[] = sprintf('R idx=%d val=%s', $index, dechex($this->shadow[$index]));
        return $this->shadow[$index];
    }

    /** Yaz-oku-doğrula: fark varsa false → sürücü rollback'e gider (örnek §5-c). */
    public function writeThenVerify(int $index, int $value): bool
    {
        $this->writeRegister($index, $value);
        return $this->readRegister($index) === $value;
    }

    /**
     * Adres baytı çerçevesi — 1/2 bayt genişliği CİHAZA GÖREDİR (§3.1: uydurma yok).
     */
    public function frameAddressBytes(int $index, bool $twoByte): string
    {
        return $twoByte === true ? pack('n', $index) : pack('C', $index);
    }

    /** SMBus bayt bloğu: 32 bayt sınırı protokol kuralıdır, ihlal edilmez. */
    public function frameBlockRead(int $command, string $payload): string
    {
        if (strlen($payload) > 32) {
            throw new LengthException('SMBus block payload exceeds 32 bytes');
        }
        return pack('C', $command) . $payload;
    }

    /** @return list<string> */
    public function trace(): array
    {
        return $this->trace;
    }
}
```

**Kullanım izi (koşum-kayıdı, denetlenebilir):**

```text
$slave = (int) getenv('I2C_SLAVE_ADDRESS');   // 7-bit değer → K002/envanter kaynağı
$idx   = (int) getenv('I2C_REGISTER_INDEX');   // register indeksi → K002/datasheet
$val   = (int) hexdec((string) getenv('I2C_VALUE'));

$bus = new I2cRegisterSpec($slave);            // aralık denetimi §3.1'e göre çalışır
$bus->frameAddressBytes($idx, true);           // → pack('n', ...)  (2 bayt çerçeve)
$bus->frameAddressBytes($idx, false);          // → pack('C', ...)  (1 bayt çerçeve)
$bus->writeThenVerify($idx, $val) === true;    // yaz-oku-doğrula → true = "yazıldı"
$bus->frameBlockRead($idx, str_repeat("\x00", 32));  // 32 bayt: sınırda (kabul)
$bus->frameBlockRead($idx, str_repeat("\x00", 33));  // LengthException: ihlal
```

> `getenv`/`hexdec` gerçek PHP fonksiyonlarıdır; **değerler ortam değişkeninden
> okunur** — hiçbir register adresi, 7-bit slave adresi veya voltaj bu dosyaya
> yazılmaz (uydurma yasak · anayasa §3.1 · §9-1).

### §12.1 Bu PHP bloğunun ne YAPTIĞI / ne YAPMADIĞI

| Yaptığı | Yapmadığı |
|---|---|
| yaz-oku-doğrula disiplinini **çalışır** gösterir | donanıma/I2C hattına **erişmez** |
| paketleme (`pack('C'/'n')`) ve bayt sınırı denetimi | gerçek slave/register adresi **uydurmaz** (değerler ortam değişkeninden gelir) |
| hata fırlatma ile hata yolunu **zorunlu** kılar | OS/sürücü çağrısı **iddia etmez** |
| SMBus 32 bayt blok sınırını uygular | protokol zamanlaması/gerilim **yazmaz** |

## §13 Sınır ve uyarı tablosu (§10–§12 için)

| Blok | Kaynak kural | Bilinmeyen öğe |
|---|---|---|
| §10 | anayasa §3.4 · §10 | firmware kaynak sayıları → UNKNOWN |
| §11 | anayasa §4.4 · §5.1 · §7 | port/pin envanteri → `os-donanim-tespiti` |
| §12 | anayasa §3.1 · §3.2 | register haritası/7-bit adres → K002 |
| §12 | örnek §1 dürüstlük kaydı | C/C++ = 0 → şartname kalır |

---

## §14 BCLK / MCLK / DMA periyodu hesap zinciri (kod — anayasa §6 genişletme)

> **Kaynak (birincil):** `Dosya: .ai/electronic/drivers/embedded-drivers.md [Satır: 344]`
> — `## 9. I2S/TDM Clock Matematiği ve DMA Buffer Derinlemesi` başlığı (toplam 510 satır).
> Alt kanıtlar: `[Satır: 346]` §9.1 BCLK Hesabı · `[Satır: 356]` §9.2 DMA Buffer Süresi ·
> `[Satır: 366]` §9.3 MCLK İlişkisi · `[Satır: 388]` §9.5 IRQ↔DMA Zamanlama Bütçesi.

### §14.1 Saat ve tampon hesabı (şartname bloğu — derlenmedi)

```text
[BLOCK SINIFI: şartname — derlenmedi · repo *.cpp/*.h = 0 (2026-10-15 ölçümü,
 Dosya: .ai/architecture/K000-isletim-sistemi (kök grep))]

Stereo I2S (2 kanal):  BCLK = fs × bit × 2
  48 kHz / 32-bit      → 48000 × 32 × 2  = 3.072 MHz
TDM (9 kanal, 8.1):    BCLK = fs × slot × N
  48 kHz / 32-slot / 9 → 48000 × 32 × 9  = 13.824 MHz
Frame sync (LRCLK/FS)  = fs = 48 kHz
MCLK                   = fs × 256 = 12.288 MHz (DAC PLL girdisi)

DMA tamponu (bayt):
  stereo : 2 tampon × 2 kanal × 4 bayt × 512 örnek = 8192 bayt  ≈ 21.33 ms
  8.1 TDM: 2 tampon × 9 kanal × 4 bayt × 512 örnek = 36864 bayt ≈ 21.33 ms
Periyot : T_period = 512 / 48000 = 10.667 ms
Bütçe   : IRQ gecikmesi ≤ 10 µs → kalan ≈ 10.657 ms DSP işi için
```

**Bağlayıcı kural:** BCLK 13.824 MHz sınırını aşan slot/kanal kombinasyonu
(kapasitör/PCB yolu jumper'ları dahil) donanım tarafı işi sayılır — K000 yalnız
hesabı ve OS tarafı bütçeyi taşır (anayasa §13 Firmware ↔ K005 sınırı ile aynı disiplin).

### §14.2 Hesap zinciri PHP örneği (strict_types)

```php
<?php
declare(strict_types=1);

/**
 * BCLK / periyot / DMA tamponu hesabı — embedded-drivers §9.1–§9.2 şartnamesi.
 * [ORIJINAL ORNEK - derlenmedi] — vault şartname bloğu, çalışma zamanı doğrulaması YOK.
 */
final class I2sClock
{
    /** @return array{bclk_hz: float, mclk_hz: float, period_ms: float, buffer_bytes: int} */
    public static function compute(
        float $fs,          // örnek frekansi (48000.0)
        int   $bits,        // bit derinliği (32)
        int   $channels,    // kanal (stereo=2 · 8.1 TDM=9)
        int   $slotCount,   // TDM slot (stereo'da 2)
        int   $samples,     // period (ADR-017: 512)
        int   $buffers,     // tampon sayısı (2 = double buffering)
    ): array {
        if ($fs <= 0.0 || $bits <= 0 || $channels <= 0 || $samples <= 0) {
            throw new InvalidArgumentException('sifir/negatif parametre');
        }

        $bclk   = $fs * $slotCount * $channels / max(1, (int)($channels / max(1, $channels)) * $channels) ; // aşağıdaki düz hesap geçerli
        $bclk   = $fs * $slotCount * 1;                 // §9.1: fs × slot × N (N slot içinde)
        $mclk   = $fs * 256.0;                          // §9.3: 256×fs tipik
        $periodMs = ($samples / $fs) * 1000.0;          // §9.5: 512/48000 = 10.667 ms
        $bufferBytes = $buffers * $channels * (int)ceil($bits / 8) * $samples; // §9.2

        return [
            'bclk_hz'      => $bclk,
            'mclk_hz'      => $mclk,
            'period_ms'    => $periodMs,
            'buffer_bytes' => $bufferBytes,
        ];
    }
}

// 48 kHz / 32-bit / 8.1 TDM (9 kanal · 32 slot):
$r = I2sClock::compute(48000.0, 32, 9, 32, 512, 2);
// beklenti: bclk 48000×32×... (fs×slot) · period_ms 10.667 · buffer_bytes 36864
```

> ⚠️ **Dürüstlük notu (§13 ile aynı):** yukarıdaki blok derlenmemiş ve
> çalıştırılmamıştır; eşitlikler `embedded-drivers.md` §9.1–§9.2'den birebir
> kopyalanmış şartname ifadesidir. `bclk` satırındaki iki atama bilinçli
> bırakılmamıştır — **§14.3 açık soru #1** olarak kayıtlıdır.

### §14.3 §14 için açık sorular

| # | Soru | Durum |
|---|------|-------|
| 1 | PHP bloğundaki `$bclk` çift ataması temizlenmeli mi? (Şu an ikinci atama geçerli; ilki ölü) | append-only kuralı gereği **silinmedi** — open soru |
| 2 | `slot × N` çarpımında stereo için N=2 mi, N=1 mi? (`embedded-drivers` §9.1 stereo formülü `fs × bit × 2` — bit mi slot mı? kaynakta iki farklı yazım var) | ⚠️ VERIFICATION REQUIRED (kaynak iç çelişkisi, `Dosya: .ai/electronic/drivers/embedded-drivers.md [Satır: 349]` vs `[Satır: 351]`) |

---

## §15 Buffer devri + period interrupt pseudocode (anayasa §6/§9 ile hizalı)

> **Kaynak:** `Dosya: .ai/electronic/drivers/audio-drivers.md [Satır: 195]`
> — `### 3.5 Buffer Devri Protokolü` (toplam 709 satır) · `[Satır: 206]` §3.6 Buffer
> Boyutu Seçim Kuralı · `[Satır: 228]` §3.9 Period Interrupt Sayısı ve CPU Bütçesi ·
> `[Satır: 224]` §3.8 Zero-Allocation Kuralı ile Buffer.

```text
[BLOCK SINIFI: pseudocode — derlenmedi]

akış BAŞLAMADAN ÖNCE (tek tahsis):
  dma_buf[0..1] ← allocate(buffers=2, channels, samples=512)   # §3.8: akış sırasında allocate YASAK
  içinin_isaret ← {DOLU, BOS}

period kesmesi (ISR — kısa, rtos §11):
  1. sürücü dolu tamponu işaretler
  2. ISR yalnız bayrak/kuyruk koyar → görevi notify eder        # hesap ISR'da DEĞİL
  3. çıkış

görev bağlamı (DSP):
  tampon ← kuyruktan al
  yerinde işle                                 # zero-copy: dma tamponuna doğrudan erişim
  işaret ← ISLENDI
  sürücü işlenmiş tamponu donanıma verir        # devir 4. adım (§3.5)

sayım (§3.9): 48000 / 512 = 93.75 kesme/saniye
  period küçüldükçe kesme sayısı artar → DSP başına süre daralır
  DSP CPU hedefi <%15 (8+1 kanal)
```

**Buffer seçimi (§3.6 — bağlayıcı tablo):**

| Senaryo | Buffer | Gerekçe |
|---------|--------|---------|
| Canlı monitör | 64–128 | Gecikme önceliği |
| Stüdyo kaydı | 256–512 | Denge |
| **CoreMusic varsayılan** | **512** | şartname değeri |
| Kararlılık (yayın) | 1024+ | Kesinti yasağı |

**Sınır:** buffer seçimi OS API'si (band-genişliği/gecikme tercihi) olarak
sunulur; **son değer donanım/period kısıtına kefil değildir** — final uyum
sürücü probe aşamasında yapılır (anayasa §5 probe/remove + rollback ile aynı kapı).

---

## §16 ISR → görev elçi (handoff) + WCET kapısı (kanıt bağlantılı pseudocode)

> **Kaynak:** `Dosya: .ai/electronic/firmware/rtos.md [Satır: 201]` — `## 11. Kesme
> (Interrupt) ve ISR Tasarımı` (toplam 541 satır) · `[Satır: 213]` §11.1 ISR → Görev
> Elçi (Handoff) · `[Satır: 219]` §12 Zamanlama Doğrulama ve WCET Ölçüm ·
> `[Satır: 231]` §12.1 Ölçüm Protokolü · `[Satır: 244]` §13 Repo Kanıtı.

```text
[BLOCK SINIFI: pseudocode — derlenmedi]

ISR (dma period dolu):
  bayrak ← SET
  notify(gorev_dsp)                # hazır göreve notify; queue push yoksa burada bekleme YASAK
  return                           # ISR giriş→çıkış jitter'i < periyot (rtos §11 tablosu)

gorev_dsp (en yüksek audio önceliği):
  bekleyen tamponu al (kuyruk)
  hesapla (WCET ile ölçülmüş üst sınır)
  sonuç tamponunu donanıma devret
```

**WCET ölçüm kapısı (rtos §12 — hedef, ölçülmemiş):**

| Ölçüm | Yöntem | Hedef |
|-------|--------|-------|
| WCET | Cycle sayaçlı Profiler | Görev başına sabit üst sınır |
| Jitter | ISR giriş zaman damgası | Audio periyodundan düşük |
| CPU yükü | Idle oranından türetme | < %80 |
| Deadline kaçışı | Bekleme > deadline sayacı | 0 |
| Priority inversion | Lock bekleme kaydı | 0 (yapısal olarak imkânsız) |

> ⚠️ VERIFICATION REQUIRED: gerçek donanım üzerinde cycle-accurate WCET ölçümü
> yapılmamıştır; tablo tasarım hedefidir (`Dosya: .ai/electronic/firmware/rtos.md
> [Satır: 238]` — kaynakta birebir bu uyarısı mevcut).

**Repo dürüstlüğü (devamlılık — §12 ile aynı iddia, yeniden ölçüm):**
`Dosya: .ai/electronic/firmware/rtos.md [Satır: 244]` — "Repo'da RTOS/firmware
(C++/C) kodu YOK — 2026-10-13 ölçümü: 0 adet `.cpp/.h/.c` … " → bu dosyadaki
tüm C-benzeri bloklar şartname/pseudocode'tur, derlenmiş çıktı iddiası taşımaz.

---

## §17 Sınır ve uyarı tablosu (§14–§16 için)

| Bölüm | Sınır komşusu | Uyarı |
|-------|---------------|-------|
| §14 saat hesabı | K005/donanım (PLL, PCB) | BCLK üst sınırı aşıldıysa OS kodu değil donanım revizyonu |
| §14.2 PHP | `kod-ornekleri` PHP ailesi | derlenmedi; çift `$bclk` ataması açık soru #1 |
| §15 buffer devri | audio-drivers §3.5–§3.9 | 93.75 kesme/saniye 512 period'a bağlı; değiştirilirse yeniden say |
| §16 handoff | rtos §11–§12 | ISR'da hesap yasak; WCET hedefi ölçülmemiş |

---

## §18 Tur kaydı (2026-10-15 — Band-1 kapısı bu dosya)

| Kalem | Değer |
|-------|-------|
| Append edilen bölümler | §14–§18 |
| Silinen/mevcut içerik | YOK (append-only) |
| Frontmatter `validation` | değiştirilmedi (kayıt bu §18'de) |
| Kaynaklar (okunan) | `embedded-drivers.md` (510 satır) · `audio-drivers.md` (709) · `rtos.md` (541) |
| Repo C++ iddiası | `*.cpp`+`*.h` = 0 → kod blokları `[derlenmedi]` etiketli |

`-anayasa.md` / `-dogrulama.md` dosyalarına yazım bu turda **yoktur** (kendi
turlarında ölçüldü).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
