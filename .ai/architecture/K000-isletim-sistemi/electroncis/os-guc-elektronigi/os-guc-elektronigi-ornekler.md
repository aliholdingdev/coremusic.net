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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
