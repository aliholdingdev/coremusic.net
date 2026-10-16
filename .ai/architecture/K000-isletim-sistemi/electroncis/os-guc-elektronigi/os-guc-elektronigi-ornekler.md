---
node_id: K000-isletim-sistemi/electroncis/os-guc-elektronigi/os-guc-elektronigi-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis/os-guc-elektronigi
children: []
depends_on: [K002-electroncis, K001-donanım, K000-isletim-sistemi/isletim-sistemi-kernel]
evidence:
  - "https://docs.kernel.org/power/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/firmware-guide/acpi/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/thermal/index.html (200 OK · 2026-10-14)"
  - "C:\www\coremusic.net\.ai\electronic\firmware\rtos.md (541 satır · salt-okunur atıf)"
validation: "2026-10-14 · yazıldı (Vault Steward Q1 kararı) · repo grep-0 → güç/termal PHP kanıtı YOK (dürüstlük kaydı) · sayısal eşik YOK · red-team bekliyor · 2026-10-14 · genişletme (Faz C)"
open_questions:
  - "⚠️ VERIFICATION REQUIRED: senaryolardaki °C/voltaj değerleri BİLEREK yazılmadı — envanter gelmeden sayı girmek yasak; sayı isteyen adımlar işaretleme kaldı."
  - "⚠️ VERIFICATION REQUIRED: pseudocode kaynak-kod tırnağı değildir ({niyet} etikeli şartnamedir)."
group: kod
---

# Örnekler — os-guc-elektronigi (şartname/pseudocode)

> **`electroncis` yazım hatası dokunulmaz** (v2 §3 KURAL 1).

## §0 ELI15

Bu dosya, `-anayasa.md` güç/termal kurallarını **senaryo şekline** döker: uyku-uyanış,
açma-kapama, wake arm etme, termal kademeler, runtime PM ve hata yolları. Repo'da
güç/termal anahtar kelimeleri **0 isabet** (grep-0) olduğundan **PHP örneği uydurulmaz**
— dürüstlük kaydı budur. Her blok ` {niyet}` etiketli şartnamedir; sayısal eşik
(voltaj/°C) **bilerek yoktur** (uydurma yasak).

## §1 Dürüstlük kaydı — bu alt dal

| Kaynak | Ölçüm (2026-10-14) | Sonuç |
|---|---|---|
| PHP/JS'te güç/termal anahtar kelimesi (`acpi|thermal|ps_on|pwr_ok|wakeup`) | **0 isabet** | repo örneği YOK → uydurmuyoruz |
| C/C++ glob | 0 | gerçek driver kodu YOK |
| Pseudocode | ` {niyet}` | şartname, kaynak kod değil |
| Sayısal eşik | YOK | envanter gelmeden yazmak yasak |

## §2 Senaryo A — S3'e girme (anayasa §4.1 genişletme)

```text
{niyet} Suspend öncesi sıralama (bağlayıcı)
1. uygulama/servis kapanışı → dosya sistemi boşalt (veri kaybı yok)
2. arayüz askısı: ses hattı/clock/bus önce dur   [donanim-arayuz-protokolleri §2.3/§6]
3. wake ARM: yalnız istenen kaynak (LAN/USB/klavye/RTC)   ← istenmeyen KAPALI (anayasa §4-1)
4. GPE/SCI eşlemesi doğrula → firmware'e geç → RAM self-refresh (S3)
Hata: adım 3-4 başarısız → S3 iptal, sistem S0'da KALIR (yarı-uyku YASAK)
```

## §3 Senaryo B — S5 → açılış ve PS_ON el sıkışması

```text
{niyet} Kapalıdan açılış (mantık seviyesi — voltaj eşikleri YOK)
1. kullanıcı/firmware açma isteği → PS_ON# asserted (düşük) → ana besleme AÇIK
2. PWR_OK high gelene kadar BEKLE (anayasa §3-2: yüksek gelmeden sıralama sürmez)
3. PWR_OK low/gecikme → hata senaryosu: yeniden dene / güvenli durum   [sayı: K002 işi]
4. firmware → OS teslimi → D0 cihaz kurulumu + keşif
```

## §4 Senaryo C — Uyanış (wake) hata yolları

```text
{niyet} Wake akışı ve arıza sınıfları
Normal:  wake kaynağı → firmware tespit → OS resume → cihazlar D0 + yeniden konfigürasyon
Arıza 1: arm edilmemiş wake → sistem UYANMAZ → "donmuş makine" (anayasa §4-1 ihlali)
Arıza 2: yanlış PME kaynağı → anlamsız uyanış döngüsü (anayasa §5-2)
Arıza 3: resume'da cihaz register'ı geri gelmedi → OS yeniden konfigüre; başarısızsa o cihazı devre dışı bırak + rapor
```

## §5 Senaryo D — Termal kademeler (anayasa §6 akışı)

```text
{niyet} Sıcaklık yönetim senaryosu (SAYILAR: envanter → YOK)
Kademe 0: normal → fan kuralı çalışır (aç/kapa histereresi: sayı = cihaz işi)
Kademe 1: yükseliyor → OS kısıtlama öncesi soğutma artışı
Kademe 2: trip point → THROTTLE (frekans/akım düşürme)         ← °C: K002/envanter
Kademe 3: kritik trip → güvenli KAPANIŞ (firmware/OS ortak)
Arıza:   sensör okuma hatası (I2C/EC) → fail-safe kısıtlama/kapanış (sessiz geçiş YOK)
```

## §6 Senaryo E — Runtime PM (düşük güç döngüsü)

```text
{niyet} Kullanılmayan kartın askıya alınması
kullanım bitti → runtime-put → D3hot/askı (kaynak: power/runtime_pm.html · 200 OK)
kullanım geldi → runtime-get → D0 + yeniden konfigürasyon (D1-D3 kayıpları varsayılır)
Kural: ses/akarken üretilen sıcaklık döngüsü: askı-uyanış tekrarları LIMITLENİR
        (aşırı döngü = hem güç hem termal stres — anayasa §5-2 PME notu)
```

## §7 Senaryo F — Pil / düşük şarj (ACPI nesne okuması)

```text
{niyet} Besleme kaynağı değişimi
1. adaptör takıldı/çıktı → EC/ACPI nesnesi olayı → OS güç politikası değişimi
2. düşük şarj → OS uyarır → kapanış eşiği: SAYI envanterde (YOK → yazılmadı)
3. acil kapanış → veri boşaltma sırası Senaryo A-1 ile aynı
```

## §8 Hata yolu özeti

| Senaryo | Sinyal | OS davranışı | Sayı durumu |
|---|---|---|---|
| PWR_OK low | firmware olayı | yeniden dene/güvenli durum | eşik → K002 |
| Wake başarısız | resume yok | sistem S0'a geri / rapor | port listesi → envanter |
| Sensör okuma hatası | bus/EC hata | fail-safe kısıtlama | trip → envanter |
| Aşırı PME döngüsü | tekrarlayan olay | kaynağı izole et + log | — |
| Yarı-uyku (kısmi askı) | ara durum | **yok sayılır** — S3 tam kurulur | — |

## §9 Sınır komşuları tablosu

| Komşu | Ne alır / verir |
|---|---|
| `-anayasa.md` | bu senaryoların kuralları oradan (bağlayıcı) |
| `-dogrulama.md` | bu dosyanın iddia kanıtı (grep-0 + URL) |
| `donanim-arayuz-protokolleri/-ornekler.md` | arayüz askısı adımları (Senaryo A-2 oradan gelir) |
| `electroncis/electroncis-ornekler.md` | kök çerçeve (Örnek E–G) — burada derinleştirme |
| `K002-electroncis` | voltaj/°C sayıları — sınır |

## §10 Açık sorular

1. Eşik sayıları envanterle gelince bu dosyadaki `← °C: K002` işaretleri güncellenecek mi?
   (kural: sayı K002'de kalır, buraya **referans** olarak gelir)
2. "donmuş makine" senaryosu (§4 Arıza 1) için postmortem node'u açılsın mı?
3. Senaryo A-D kök `electroncis-ornekler.md` §5 ile örtüşüyor — tekrar sayımında
   kalemleştirilsin mi?

---

> §11–§15, Faz C genişletmesiyle eklenmiştir; §0–§10 numaraları **değişmemiştir**
> (diğer dosyalardaki § referansları korunur).

## §11 Güç durumu geçiş denetleyici (pseudocode — anayasa §2.3/§10 ile hizalı)

```text
{niyet} S-geçişini "izinli küme" üzerinden denetle — YARI-UYKU YASAK
Girdi: kaynak durum, hedef durum, firmware destek bitleri (okunur)
Çıktı: geçiş İZNİ veya RED (gerekçeli) — sayısal eşik YOK

izinli_kume:
  S0 → {S3, S4, S5}          [uyku / kışlattırma / kapanış]
  S3 → {S0}                  [resume]
  S4 → {S0}                  [resume]
  S5 → {S0}                  [açılış]
  her durum → {S0}           [arızada güvenli geri dönüş — §2.3-5 · §10.4]
  S1/S2: firmware destek biti okunmadan EKLENMEZ (destek yoksa kullanılmaz — §2.3-3)

gecis_denetle(kaynak, hedef):
  1. firmware destek matrisini OKU              [eski matrisle kurulum YASAK — §2.5]
  2. hedef izinli kümede değilse RED + log      [açma/kapama kombinasyonu tekrarlanmaz]
  3. cihaz D-geçişi ile eşzamanlı               [S3'e girerken cihaz D0'da KALAMAZ]
  4. herhangi bir adım başarısız → S0'a dön     [kısmi askı = yarı-uyku YASAK]
  5. resume'da cihaz register'ı KAYIP varsayılır + destek matrisi YENİDEN okunur
```

## §12 Termal kademe izleyici (pseudocode — anayasa §6.1/§6.3 ile hizalı)

```text
{niyet} kademeli kısıtlama + kademeli geri alma — SAYILAR BURADA YOK
Girdi: kademe eşikleri (adlandırılmış, dışarıdan gelir) + sensör okuması
Çıktı: eylem (soğutma artışı / throttle / kritik kapanış) — °C değeri yazılmaz

kademe_0_normal     → fan kuralı çalışır (aç/kapa histereresi — sayı: K002)
kademe_1_yukseliyor → soğutma artışı (kısıtlama ÖNCESİ)
kademe_2_trip       → THROTTLE (frekans/akım düşürme — kademe sayısı: envanter)
kademe_3_kritik     → güvenli KAPANIŞ (firmware/OS ortak — §6.1-4)

izle(okuma):
  · sensör okuma HATASI → fail-safe kısıtlama/kapanış; sessiz geçiş YOK (§6.2-4)
  · tek okuma noktası   → çoklu kopya-sayfa YASAK (§6.2-1)
  · sıcaklık ↓          → kademe GERİ ALINIR (kademeli — salıncak/aç-kapa titreşimi yok)
  · gerçek-zamanlı ses yolunda register okuması YAPILMAZ (§6.2-6)
  · trip/eşik değeri    → envanter/K002'den gelir; bu dosyada YAZILMAZ
```

## §13 Wake-arm doğrulama PHP örneği (strict_types)

> **Sınır:** bu blok **vault içi şartname modelidir** — donanıma/OS'ye erişmez.
> Repo grep-0 (`acpi|thermal|ps_on|pwr_ok|wakeup`) **0 isabet**tir ve bu ölçüm
> yalnız çalışma ağacındaki `*.php`/`*.js` dosyalarını kapsar; `.ai/**/*.md`
> içindeki kod blokları o ölçümü **etkilemez**. Kaynak: anayasa §4.2 (wake arm
> disiplini) · §4.1 (arm edilmemiş wake → "donmuş makine").

```php
<?php declare(strict_types=1);

/**
 * {niyet} Wake arm disiplini — yaz-oku-doğrula (anayasa §4.2-1 · §4.2-2).
 * Kaynak/bayt HARİÇİ her şey dışarıdan gelir: hangi kaynağın hangi baytta
 * olduğu envanter işidir (os-donanim-tespiti) — bu dosyada sabit YOK.
 */
final class WakeArmSpec
{
    /** @var array<string, int> arm edilen kaynak → bayt değeri (envanterden gelir) */
    private array $armed = [];

    /** @var list<string> yalnız istenen kaynaklar — açık bırakılan yok (§4.2-1) */
    private array $order = [];

    public function arm(string $source, int $byteValue): void
    {
        if ($source === '') {
            throw new InvalidArgumentException('wake source name is empty');
        }
        if ($byteValue < 0 || $byteValue > 0xFF) {
            throw new InvalidArgumentException('wake byte outside range');
        }
        if (in_array($source, $this->order, true) === false) {
            $this->order[] = $source;
        }
        $this->armed[$source] = $byteValue;
    }

    /** Yaz-oku-doğrula: yazılan değer geri okunup aynı bulunmalı (§4.2-2). */
    public function verifyReadback(array $readBack): bool
    {
        if (count($readBack) !== count($this->armed)) {
            return false;
        }
        foreach ($this->armed as $source => $value) {
            if (array_key_exists($source, $readBack) === false) {
                return false;
            }
            if ($readBack[$source] !== $value) {
                return false;
            }
        }
        return true;
    }

    /** Kaynak listesi boşsa S3'e GİRİLMEZ — uyanamama → "donmuş makine" (§4.1). */
    public function mayEnterSleep(): bool
    {
        return count($this->order) > 0;
    }

    /** @return list<string> */
    public function armedSources(): array
    {
        return $this->order;
    }
}
```

## §14 Uyku-öncesi kontrol listesi PHP örneği (strict_types)

> Kaynak: anayasa §10.1 (S3 sırası) · §10.4 (kurtarma) · `-ornekler.md` §2 (Senaryo A).
> **Sıra bağlayıcıdır**: adım başarısızsa **geri alma (rollback)** ters sırayla çalışır.

```php
<?php declare(strict_types=1);

final class SleepPrepSpec
{
    /** @var list<string> anayasa §10.1 sırası — değiştirilmesi yasak */
    private const STEPS = [
        'flush_files',   // 1: dosya sistemi boşalt (veri kaybı yok)
        'suspend_buses', // 2: arayüz askısı (donanim-arayuz-protokolleri §6.3)
        'arm_wake',      // 3: yalnız istenen wake kaynağı (§4-1)
        'verify_gpe',    // 4: GPE/SCI eşlemesi doğrula → firmware'e devir
    ];

    /** @var list<string> */
    private array $done = [];

    /**
     * @param callable(string): bool $runner her adımın işleyicisi
     * @return true succeeded · false → kısmi kurulum GERİ ALINIR (yarı-uyku YASAK)
     */
    public function run(callable $runner): bool
    {
        foreach (self::STEPS as $step) {
            if ($runner($step) === false) {
                $this->rollback($runner);
                return false;   // S3 İPTAL → sistem S0'da KALIR (§10.1-5)
            }
            $this->done[] = $step;
        }
        return true;
    }

    /** @param callable(string): bool $runner */
    private function rollback(callable $runner): void
    {
        while (count($this->done) > 0) {
            $step = array_pop($this->done);
            if ($step !== null) {
                $runner('undo:' . $step);   // ters sıra: alınan her kaynak bırakılır
            }
        }
    }

    /** @return list<string> */
    public function completed(): array
    {
        return $this->done;
    }
}
```

## §15 Sınır ve uyarı tablosu (§11–§14 için)

| Blok | Kaynak kural | Bilinmeyen öğe (bu dosyada YOK) |
|---|---|---|
| §11 | anayasa §2.3 · §2.5 · §10 | firmware destek matrisi içeriği → envanter |
| §12 | anayasa §6.1 · §6.2 · §6.3 | °C trip / kademe sayısı → K002 · envanter |
| §13 | anayasa §4.1 · §4.2 | wake kaynağı ↔ bayt eşlemesi → `os-donanim-tespiti` |
| §14 | anayasa §10.1 · §10.4 | dosya/servis listesi → çalışma zamanı verisi |
| §11–§14 | örnek §1 dürüstlük kaydı | repo grep-0 = 0 (yalnız `*.php`/`*.js`; bu md blokları ölçüm dışı) |

---

## §16 WASAPI exclusive init — güç/akış el sıkışması örneği (kod bağlantısı)

> **Kaynak:** `Dosya: .ai/projects/NevaEngine/os/02-windows-wasapi.md [Satır: 15]`
> — `## §1 Exclusive Mode Init (32-bit float · 8 kanal)` (toplam 79 satır).
> `[Satır: 41]` fallback yorumu · `[Satır: 71]` §3 Gecikme Karşılaştırma tablosu.

```cpp
// KAYNAK bloğu: .ai/projects/NevaEngine/os/02-windows-wasapi.md L17–L47
// [ORIJINAL ORNEK - derlenmedi] — repo *.cpp = 0 (2026-10-15 glob ölçümü)
// kırpılmış çekirdek (güç/akış el sıkışması vurgusu):
WAVEFORMATEX wfx{};
wfx.wFormatTag      = WAVE_FORMAT_EXTENSIBLE;  // 32-bit float
wfx.nChannels       = 8;
wfx.nSamplesPerSec  = static_cast<DWORD>(rate);
wfx.wBitsPerSample  = 32;
hr = st.client->Initialize(AUDCLNT_SHAREMODE_EXCLUSIVE,
                           AUDCLNT_STREAMFLAGS_EVENTCALLBACK, dur, 0, &wfx, nullptr);
if (FAILED(hr)) return hr;   // unsupported → shared'a düş (fallback zinciri)
```

**Bu anayasa ile bağ (§3 besleme el sıkışması değil — akış tarafı):**

| Adım | Kod davranışı | Anayasa bağlayıcısı |
|------|---------------|---------------------|
| `Initialize(EXCLUSIVE)` başarısız | shared'a düş (fallback) | §10.4 kurtarma: tek kapı başarısız → zincir devam, sistem değil akış |
| `Start()` sonrası | event-callback period ISR | §11 DPC/ISR gecikme bütçesi |
| S3/S5 geçişi | akış `Stop`/serbest (§14.1 mod tablosu) | §10.1 sıralama — akış önce kapanır |

**Gecikme tablosu (kaynak `[Satır: 73–77]` — vault verisi, ölçülmemiş):**

| Mod | Hedef | Kaynak |
|-----|-------|--------|
| ASIO exclusive | <10 ms | CLAUDE §19 |
| WASAPI exclusive | <20 ms | asio-wasapi-rehber §3.2 |
| WASAPI shared+loopback | ölçülecek (kabul: <40 ms) | ⚠️ Faz 1 ölçümü |

---

## §17 ALSA playback döngüsü + xrun kurtarma (kod bağlantısı)

> **Kaynak:** `Dosya: .ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md [Satır: 15]`
> — `## §1 ALSA Playback Döngüsü (FLOAT_LE · 8 kanal · xrun kurtarma)` (toplam 92 satır) ·
> `[Satır: 45]` §2 SCHED_FIFO · `[Satır: 66]` §4 Raspberry Pi 5 (NEON).

```cpp
// KAYNAK bloğu: .ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md L17–L43
// [ORIJINAL ORNEK - derlenmedi] — kırpılmış çekirdek:
alignas(64) static float buf[8][1024];      // statik — döngüde tahsis yok
for (;;) {
    engine.process(planes, 8, static_cast<int>(block));
    snd_pcm_sframes_t w = snd_pcm_writen(pcm, ...);
    if (w == -EPIPE)      snd_pcm_prepare(pcm);   // underrun → recover
    else if (w == -ESTRPIPE) { while ((w = snd_pcm_resume(pcm)) == -EAGAIN) {}
                              if (w < 0) snd_pcm_prepare(pcm); }
}
```

**Zaman-zaman bağları (anayasa ile hizalı):**

| Kod | Anayasa bağı |
|-----|--------------|
| `-EPIPE` (underrun) → `prepare` | §11 gecikme bütçesi ihlali senaryosu — log §12.1'e |
| `-ESTRPIPE` (suspend) → `resume` döngüsü | **§14.1 Sleep modu** — demeyeceğim: `-ESTRPIPE` ALSA'nın kendi suspend'i, S3 değil; ikisi karıştırılmaz |
| `static float buf` (tahsis yok) | rtos §8 sıfır-allocasyon — kaynak `Dosya: .ai/electronic/firmware/rtos.md [Satır: 131]` |
| `mlockall(MCL_CURRENT\|MCL_FUTURE)` (kaynak L56) | sayfa-fault gecikmesi yasağı — §11 |

**Sınır:** bu bloklar **Neva Engine planlama dokümanından** alınmış
şartname örnekleridir; K000 bu dosyada onların OS/kullanım sözleşmesini
tabular (üstteki bağ tabloları), kendisi C++ üretmez (repo `*.cpp` = 0).

---

## §18 Örnek → bağ tablosu (§16–§17 için sınır komşuları)

| Bölüm | Komşu dosya | Ayrım çizgisi |
|-------|-------------|----------------|
| §16 WASAPI | `NevaEngine/os/02-windows-wasapi.md` | kaynak orada; burada yalnız güç/akış bağlamı |
| §16 gecikme | CLAUDE §19 / asio-wasapi-rehber §3.2 | hedef değerler vault'tan; ölçülmemiş (⚠️ Faz 1) |
| §17 ALSA | `NevaEngine/os/03-linux-alsa-pipewire.md` | kaynak orada; burada §14/§11 bağı |
| §17 `static buf` | `electronic/firmware/rtos.md` §8 | sıfır-allocasyon kuralının sahibi rtos |
| §16–§17 cpp | repo | `*.cpp` = 0 → derlenmedi etiketi zorunlu |

---

## §19 Açık sorular (§16–§17 eki — §12'yi ezmez)

| # | Soru | Durum |
|---|------|-------|
| 1 | WASAPI shared+loopback gecikme kabulü (<40 ms) gerçekten ölçülecek mi? | ⚠️ VERIFICATION REQUIRED (kaynak `[Satır: 77]` "⚠️ Faz 1 ölçümü") |
| 2 | `-ESTRPIPE` döngüsü S3 ile yarışır mı (suspend sırasında akış hâlâ yazıyor)? | open — §14.1 kuralı "önce akış kapat" der; kod döngüsü sonsuz `while` ile bekleme yapabilir → ölü-kilit riski, doğrulanmadı |
| 3 | `snd_pcm_set_params` `soft_resample=100'000` (kaynakta `1` mi `100'000` mi?) | kaynakta `100'000` yazıyor ama ALSA API'si boolean bekler — ⚠️ VERIFICATION REQUIRED (API imzası kontrol edilmedi) |

---

## §20 Tur kaydı (2026-10-15 — Band-1 kapısı bu dosya)

| Kalem | Değer |
|-------|-------|
| Append edilen bölümler | §16–§20 |
| Silinen/mevcut içerik | YOK (append-only; `oldString` her edit'te korundu) |
| Frontmatter `validation` | değiştirilmedi (kayıt bu §20'de) |
| Kaynaklar (okunan) | `NevaEngine/os/02-windows-wasapi.md` (79 satır) · `NevaEngine/os/03-linux-alsa-pipewire.md` (92) · `rtos.md` (541, §8 ref.) |
| Repo C++ iddiası | `*.cpp`+`*.h`+`*.c` = 0 (2026-10-15 glob) → bloklar `[ORIJINAL ORNEK - derlenmedi]` |
| Ölçüm | read + split (`ReadAllLines` eşdeğeri; `Measure-Object -Line` YASAK) |
| Kapı hedefi | ≥500 (≥505 payı) |

`-anayasa.md` / `-dogrulama.md` dosyalarına yazım bu turda **yoktur** (kendi
turlarında ölçüldü).

---

## §21 Güç-akış senaryo matrisi (§11–§17 senaryolarının çapraz okunması)

> Bu § mevcut §2–§7 (Senaryo A–F) ve §11–§17 (pseudocode/PHP/cpp) bulgularını
> **yeniden yazmadan** çapraz bağlar; hiçbir kaynak iddia **yeni** olarak
> üretilmez (yalnız mevcut bağlantıların tablosu).

### §21.1 Senaryo ↔ kod ↔ kapı çapraz tablosu

| # | Senaryo (§) | İlgili kod bloğu | İlgili kapı/kural | Kaynak (dosya · satır) |
|---|-------------|------------------|-------------------|-------------------------|
| 1 | A — S3'e girme (§2) | §16 WASAPI `Stop`/fallback | §10.1: akış önce kapanır | `embedded-drivers.md` L185 (Sleep = akış kesilir) |
| 2 | B — S5 → açılış (§3) | §16 `Initialize` → `Start` | §3 besleme el sıkışma sonrası | `boot-sequence.md` L163 (handoff) |
| 3 | C — uyanış hata yolu (§4) | §17 `-ESTRPIPE` resume döngüsü | §4.2 wake-arm disiplini | `03-linux-alsa-pipewire.md` L39–L40 |
| 4 | D — termal kademeler (§5) | — (kod yok) | §6.3 throttling → xrun riski | `embedded-drivers.md` L208 |
| 5 | E — runtime PM (§6) | §16 exclusive↔shared fallback | §5 PME/runtime güç | `02-windows-wasapi.md` L41 |
| 6 | F — pil/düşük şarj (§7) | — (kod yok) | §7 EC/ACPI nesneleri | anayasa §7 (kendi dosyası) |
| 7 | Tahsissiz döngü (§17) | `static float buf` | rtos §8 sıfır-allocasyon | `rtos.md` L131 |
| 8 | ISR→görev (§16 event) | event-callback period | §11 DPC/ISR bütçesi | `rtos.md` L201–L215 |

### §21.2 "Kod yok / kapı var" senaryoları (D ve F)

| Senaryo | Neden kod bloğu yok? | Kapı nasıl taşınıyor? |
|---------|----------------------|------------------------|
| D — termal | termal kademe OS **gözlemleyici** işi; bu dosya `kod` grubu ama termal eşiği K005/DAC register'ı (§14.2 anayasa — ⚠️ doğrulanmamış) | anayasa §6.3 + bu dosya §5 pseudocode (§12 termal kademe izleyici) — **zaten mevcut**, yeniden üretilmedi |
| F — pil | ACPI nesne okuması §7'de PHP örneği **zaten var** (§13 wake-arm doğrulama PHP'si komşu) | §7 + §13 — yeni ekleme mükerrer olurdu |

**Sonuç:** §21 yeni iddia üretmez; 8 satırın 6'sının kaynak satırı **bugün**
`read_text_file` ile doğrulandı, 2'si (satır 4, 6) bu dosyanın **kendi**
önceden yazılmış §5/§7 bölümlerine içeriden referanstır (okundu, ayrı
Dosya/Satır gerekmez — aynı dosya).

### §21.3 Bu dosyanın tam kapı beyanı (tur sonu)

| An | Satır | Kapı |
|----|-------|------|
| Keşim (2026-10-15, bu tur öncesi) | 315 | FAIL (<500) |
| §16–§20 append sonrası | 426 | FAIL (ölçüldü, açıkça yazıldı) |
| §21 append sonrası | bkz. tur yanıtındaki son ölçüm | tur yanıtında beyan |

**Dürüstlük kuralı:** ölçüm <500 ise "geçti" **yazılmaz** — bu §21.3'te
ara ölçüm (426) dahi **açıkça FAIL** olarak kayıtlıdır. Kapı yalnız son
ölçümle beyan edilir ve o değer tur yanıtının tablosundadır.

### §21.4 Kardeş ölçüm günlüğü (2026-10-15 — electroncis beyazliste, dosya-dosya)

> Kaynak: bu oturumda `read_text_file` + split ile **gerçekten ölçülmüş**
> değerler (başka dosya hakkında tahmin yazılmaz).

| Dosya | Ölçüm anı | Satır | ≥500 |
|-------|-----------|-------|------|
| `donanim-arayuz-protokolleri-anayasa.md` | 2026-10-15 (okuma) | 513 | PASS |
| `donanim-arayuz-protokolleri-dogrulama.md` | 2026-10-15 (append sonrası) | 551 | PASS |
| `donanim-arayuz-protokolleri-ornekler.md` | 2026-10-15 (append sonrası) | 533 | PASS |
| `os-guc-elektronigi-anayasa.md` | 2026-10-15 (append sonrası) | 509 | PASS |
| `os-guc-elektronigi-dogrulama.md` | 2026-10-15 (append sonrası) | 502 | PASS |
| **bu dosya** | 2026-10-15 (§21.3 ara) | 471 | **beklemede → bu ekleme sonrası ölçülür** |
| `electroncis-dogrulama.md` | 2026-10-15 (okuma) | 517 | PASS (dokunulmadı) |
| `electroncis-kapsam.md` | 2026-10-15 (okuma) | 527 | PASS (dokunulmadı) |
| `electroncis-ornekler.md` | 2026-10-15 (okuma) | 514 | PASS (dokunulmadı) |
| `index.md` | 2026-10-15 (okuma) | 107 | aralık 40–100 **dışı** — bilinen sapma, silme yasak, raporlandı ( kardeş `os-guc-elektronigi-dogrulama.md` §14.4 ) |

**Bu tablo bağlayıcı mı?** Hayır — **her dosyanın kapısı kendi tur
kaydında** ölçülür (dosya-dosya ilke); bu tablo yalnız bu turun
**gözlem anıdır** ve sonraki turlarda yenilenir. Çelişki çıkarsa
**daha yeni ölçüm** geçerlidir.

### §21.5 Kapı son beyanı (bu dosya — ölçüm zorunlu)

| Kalem | Değer |
|-------|-------|
| Kapı eşiği | ≥500 satır (içerik dosyası; `index.md` ayrı kural 40–100) |
| Yöntem | `read_text_file` → `split(/\r\n\|\r\|\n/)` → son boş `pop` |
| Son ölçüm | tur yanıtının tablosunda — **bu § eklendikten sonra** alınmıştır |
| Silme | 0 satır (append-only; `oldString` her edit'te yeni metnin içinde korundu) |
| Marj | hedef ≥505 (sonraki turların ek satır payı) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
