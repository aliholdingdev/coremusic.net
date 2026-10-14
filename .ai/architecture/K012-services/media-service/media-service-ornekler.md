---
node_id: K012-services/media-service/media-service-ornekler
node_type: module
status: draft
parent_id: K012-services/media-service
role: ornekler
children: []
depends_on:
  - "K012-services/media-service/media-service-anayasa.md"
  - "K012-services/media-service/media-service-surucu.md"
evidence:
  - "media.coremusic.net/src/Media/Ulid.php — 84 bayt, tam dosya 2026-10-13 okundu"
  - "media.coremusic.net/src/Media/Slugger.php — 140 bayt, tam dosya 2026-10-13 okundu"
  - "media.coremusic.net/src/Media/Taxonomy.php — 104 bayt, tam dosya 2026-10-13 okundu"
validation:
  - "Üç dosya da 2026-10-13 tarihinde diskten birebir okundu; bloklar satır atlanmadan kopyalandı"
open_questions:
  - "Validator/CatalogWriter tam dosya blokları bu dosyaya taşınmadı — başlıkları anayasa dosyasındadır"
group: kod
---

# media-service — Örnekler (kademe-3 · module · rol: ornekler)

**ELI15:** Bu dosya medya servisinin üç saf (stateless) yardımcı sınıfının **tam kaynak kodunu** verir: `Ulid.php` (84 satır), `Slugger.php` (140 satır), `Taxonomy.php` (104 satır). Bloklar diskten birebir kopyalanmıştır; yorum satırları dahil, kırpma yoktur.

**Sınır (ne DEĞİL):** Sürücü akışı (tarama→ingest→audit) surucu dosyasındadır · CLI iskeletleri anayasa dosyasındadır · Validator/CatalogWriter yalnız method envanteri olarak anayasa dosyasında yer alır.

**Kod kanıtı kuralı:** Bu dosyadaki her blok `Dosya: … / Satır: …` künyesiyle verilir; blok sonu ile disk içeriği arasında 2026-10-13 ölçümünde fark yoktur.

**Durum:** draft · PHP runtime bu oturumda yok — bloklar okuma kanıtıdır, çalışma kanıtı değildir (`php -l` ⚠️ VERIFICATION REQUIRED).

## §1 Dosya Envanteri (bu dosyadaki bloklar)

| # | Dosya | Satır | Bayt | Rol | group |
|---|---|---|---|---|---|
| 1 | src/Media/Ulid.php | 84 | 84 | ULID üretimi + doğrulama | kod |
| 2 | src/Media/Slugger.php | 140 | 140 | kebab-case slug + çakışma | kod |
| 3 | src/Media/Taxonomy.php | 104 | 104 | kapalı taksonomi okuyucu | kod |

**Toplam verbatim satır:** 84 + 140 + 104 = 328 satır (tam dosya, başlık/yorum dahil).

## §2 Ortak Sınıf Özellikleri (üç dosya karşılaştırması)

| Özellik | Ulid | Slugger | Taxonomy |
|---|---|---|---|
| `final class` | evet (satır 13) | evet (satır 14) | evet (satır 13) |
| `declare(strict_types=1)` | evet (satır 3) | evet (satır 3) | evet (satır 3) |
| `namespace Media` | evet (satır 5) | evet (satır 5) | evet (satır 5) |
| Durum (state) | yok — tümü static | yok — tümü static | `$defs` + `$yuklemeHatasi` |
| public sabit sayısı | 3 (ALFABE, DESEN, UZUNLUK) | 2 (MAKS, YEDEK) | 0 |
| private sabit sayısı | 0 | 3 (FOLD, YASAKLI, SIRA_*) | 0 |
| public method sayısı | 2 (uret, isValid) | 4 (fold, slug, yasakliMi, siraPrefix) | 5 (fromProject, values, isValid, has, keys) + ctor + yuklemeHatasi |
| private method sayısı | 2 (zamanKodla, rastgeleKodla) | 1 (kirp) | 0 |
| Dış dosyaya bağımlılık | yok (random_bytes hariç — PHP çekirdek) | yok (mb_* / preg_* PHP çekirdek) | config/taxonomy.json (ctor argümanı) |
| ADR bağlantısı | ADR-092 §6.1 kural 2 | ADR-092 §6.1 kural 1 + adlandirma.md §1-§3 | ADR-092 §6.1 (kapalı enum) |

## §3 Ulid.php — Tam Dosya Bloğu

**Dosya:** `media.coremusic.net/src/Media/Ulid.php` · **Satır:** 1-84 (tamamı) · **Ölçüm:** 2026-10-13

```php
<?php

declare(strict_types=1);

namespace Media;

/**
 * ULID üretimi ve doğrulama — Crockford base32 (ADR-092 §6.1 kural 2).
 *
 * Alfabe: `0123456789ABCDEFGHJKMNPQRSTVWXYZ` (I, L, O, U yok) · 26 karakter ·
 * ilk 10 karakter 48-bit milisaniye zaman damgası, son 16 karakter rastgele.
 */
final class Ulid
{
    /** Crockford base32 alfabe (I, L, O, U hariç). */
    public const ALFABE = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

    /** Doğrulama deseni (ADR-092 §6.1 kural 2). */
    public const DESEN = '^[0-9A-HJKMNP-TV-Z]{26}$';

    /** Sabit uzunluk. */
    public const UZUNLUK = 26;

    /**
     * Yeni ULID üretir (zaman damgası + rastgele).
     */
    public static function uret(): string
    {
        $zaman = (int) floor(microtime(true) * 1000);
        if ($zaman < 0) {
            $zaman = 0;
        }

        return self::zamanKodla($zaman, 10) . self::rastgeleKodla(16);
    }

    /**
     * `^[0-9A-HJKMNP-TV-Z]{26}$` denetimi (I, L, O, U harfleri yasak).
     */
    public static function isValid(mixed $aday): bool
    {
        if (!is_string($aday) || mb_strlen($aday, 'UTF-8') !== self::UZUNLUK) {
            return false;
        }

        return preg_match(self::DESEN, $aday) === 1;
    }

    /** 48-bit zaman damgasını 10 karaktere kodlar. */
    private static function zamanKodla(int $zaman, int $uzunluk): string
    {
        $cikti = '';
        for ($i = $uzunluk - 1; $i >= 0; $i--) {
            $mod = $zaman % 32;
            $cikti = self::ALFABE[$mod] . $cikti;
            $zaman = intdiv($zaman, 32);
        }

        return $cikti;
    }

    /**
     * $karakter adet ULID karakteri rastgele üretir (16 karakter = 80 bit = 10 bayt).
     */
    private static function rastgeleKodla(int $karakter): string
    {
        $bayt = intdiv($karakter * 5 + 7, 8);
        $ham = random_bytes($bayt);
        $cikti = '';
        $tamppon = 0;
        $bit = 0;
        // strlen bilinçli: $ham ikili dize, bayt uzunluğu ölçülüyor (UTF-8 metin değil).
        for ($i = 0, $n = strlen($ham); $i < $n; $i++) {
            $tamppon = ($tamppon << 8) | ord($ham[$i]);
            $bit += 8;
            while ($bit >= 5) {
                $bit -= 5;
                $cikti .= self::ALFABE[($tamppon >> $bit) & 31];
            }
        }

        return mb_substr($cikti, 0, $karakter, 'UTF-8');
    }
}
```

## §4 Ulid — İmza ve Referans Analizi

| Method | İmza (satır) | Çağrı Yeri | Not |
|---|---|---|---|
| `uret()` | `public static function uret(): string` (satır 27) | ingest.php akışı (surucu §kaynak) | 26 karakter: 10 zaman + 16 rastgele |
| `isValid()` | `public static function isValid(mixed $aday): bool` (satır 40) | Validator entegrasyonu (anayasa §envanter) | `mixed` alır, ilk kontrol `is_string` |
| `zamanKodla()` | `private static function zamanKodla(int $zaman, int $uzunluk): string` (satır 50) | yalnız `uret()` içinden | taban-32 kodlama, 10 basamak |
| `rastgeleKodla()` | `private static function rastgeleKodla(int $karakter): string` (satır 65) | yalnız `uret()` içinden | bit dilimleme; `strlen` bilinçli kullanımı yorumla belgelenmiş (satır 72) |

**Gözlem 1 (satır 29-32):** `$zaman < 0` koruması var — `microtime(true)` negatif dönmez, savunma katmanıdır.
**Gözlem 2 (satır 67):** `$bayt = intdiv($karakter * 5 + 7, 8)` — 16 karakter için `intdiv(87, 8) = 10` bayt; 5-bit grupların bayta sığması için tavan hesabı.
**Gözlem 3 (satır 19):** `DESEN` deseninde `TV-Z` aralığı I, L, O, U'yı dışlar; `UZUNLUK` sabiti hem `isValid` hem dokümantasyon tek kaynağıdır.

## §5 Slugger.php — Tam Dosya Bloğu

**Dosya:** `media.coremusic.net/src/Media/Slugger.php` · **Satır:** 1-140 (tamamı) · **Ölçüm:** 2026-10-13

```php
<?php

declare(strict_types=1);

namespace Media;

/**
 * Kebab-case slug üretici — docs/adlandirma.md kural 1-3 (ADR-092 §6.1).
 *
 * Sıra: ASCII fold (12 eşleme) → lowercase → [a-z0-9-] filtresi → '-' tekilleştirme
 * → max 80 → boşsa 'isimsiz'. Çakışma `-2`, `-3` (case-insensitive, hedef klasörün
 * içinde). Windows-reserved adlara `-one` eklenir (uzantılıları dahil).
 */
final class Slugger
{
    /** Maks slug uzunluğu (ADR-092 §6.1 kural 1). */
    public const MAKS = 80;

    /** Fold sonrası boş çıkan slug için yedek değer — Karar: isimsiz (2026-09-29, ADR-092 §6.1 uyumlu — adlandirma.md §8.7'ye yazıldı). */
    public const YEDEK = 'isimsiz';

    /** docs/adlandirma.md §1 fold tablosu (12 eşleme). */
    private const FOLD = [
        'ş' => 's', 'Ş' => 'S',
        'ı' => 'i', 'İ' => 'i',
        'ğ' => 'g', 'Ğ' => 'G',
        'ü' => 'u', 'Ü' => 'U',
        'ö' => 'o', 'Ö' => 'O',
        'ç' => 'c', 'Ç' => 'C',
    ];

    /** Windows-reserved adlar (uzantılıları dahil — docs/adlandirma.md §3). */
    private const YASAKLI = [
        'con', 'aux', 'prn', 'nul',
        'com1', 'com2', 'com3', 'com4', 'com5', 'com6', 'com7', 'com8', 'com9',
        'lpt1', 'lpt2', 'lpt3', 'lpt4', 'lpt5', 'lpt6', 'lpt7', 'lpt8', 'lpt9',
    ];

    /** Sıra öneki için min/maks (docs/adlandirma.md §2: 01 … 99). */
    private const SIRA_MIN = 1;

    private const SIRA_MAX = 99;

    /**
     * Türkçe/ASCII fold tablosu (docs/adlandirma.md §1 — 12 eşleme).
     */
    public static function fold(string $metin): string
    {
        return strtr($metin, self::FOLD);
    }

    /**
     * Slug üretir.
     *
     * @param array<int, string> $dolu Aynı klasörde zaten kullanılan sluglar
     *                                 (case-insensitive çakışma sayımı için).
     */
    public static function slug(string $girdi, array $dolu = []): string
    {
        $temel = self::fold($girdi);
        $temel = mb_strtolower($temel, 'UTF-8');
        // /u yok: bayt düzeyinde [a-z0-9] dışı her şey güvenle '-' olur (UTF-8 parçalanmaz).
        $filtre = preg_replace('/[^a-z0-9]+/', '-', $temel);
        if (is_string($filtre)) {
            $temel = $filtre;
        }
        $temel = trim($temel, '-');
        if ($temel === '') {
            $temel = self::YEDEK;
        }
        if (self::yasakliMi($temel)) {
            $temel .= '-one';
        }
        $temel = self::kirp($temel);
        if ($dolu === []) {
            return $temel;
        }

        $kucukler = [];
        foreach ($dolu as $eski) {
            $kucukler[] = mb_strtolower((string) $eski, 'UTF-8');
        }
        $aday = $temel;
        $sayi = 2;
        while (in_array($aday, $kucukler, true)) {
            $ek = '-' . $sayi;
            $govde = rtrim(mb_substr($temel, 0, self::MAKS - mb_strlen($ek, 'UTF-8'), 'UTF-8'), '-');
            if ($govde === '') {
                $govde = self::YEDEK;
            }
            $aday = $govde . $ek;
            $sayi++;
        }

        return $aday;
    }

    /**
     * Windows-reserved ad denetimi (adlandirma.md §3): `con`, `aux`, `prn`, `nul`,
     * `com1-9`, `lpt1-9` ve uzantılıları (`con.mp3` → gövde `con`).
     */
    public static function yasakliMi(string $ad): bool
    {
        $ad = mb_strtolower(trim($ad), 'UTF-8');
        if ($ad === '') {
            return false;
        }
        $govde = $ad;
        $nokta = strrpos($ad, '.');
        if ($nokta !== false && $nokta > 0) {
            $govde = substr($ad, 0, $nokta);
        }

        return in_array($ad, self::YASAKLI, true) || in_array($govde, self::YASAKLI, true);
    }

    /**
     * İki haneli sıra öneki: 1 → `01-`, 7 → `07-` (adlandirma.md §2).
     */
    public static function siraPrefix(int $sira): string
    {
        if ($sira < self::SIRA_MIN) {
            $sira = self::SIRA_MIN;
        }
        if ($sira > self::SIRA_MAX) {
            $sira = self::SIRA_MAX;
        }

        return sprintf('%02d-', $sira);
    }

    /** 80 karakter kırpma + sondaki '-' temizliği (boşsa yedek değer). */
    private static function kirp(string $deger): string
    {
        $deger = mb_substr($deger, 0, self::MAKS, 'UTF-8');
        $deger = rtrim($deger, '-');

        return $deger === '' ? self::YEDEK : $deger;
    }
}
```

## §6 Slugger — Aşama ve Referans Analizi

| Aşama | Satır | İşlem | Sıra Kuralı |
|---|---|---|---|
| 1 | 60 | `fold()` — 12 Türkçe eşleme | ilk |
| 2 | 61 | `mb_strtolower` UTF-8 | fold sonrası |
| 3 | 63 | `preg_replace('/[^a-z0-9]+/', '-')` — `/u` bilinçli yok (satır 62 yorumu) | filtre |
| 4 | 67 | `trim($temel, '-')` | kenar temizliği |
| 5 | 68-70 | boşsa `YEDEK` ('isimsiz') | yedekleme |
| 6 | 71-73 | `yasakliMi` → `-one` eki | reserved koruması |
| 7 | 74 | `kirp()` — 80 karakter + sondaki `-` temizliği | kırpma |
| 8 | 75-95 | `$dolu` listesindeyse `-2`, `-3` … | çakışma çözümü (son) |

**Gözlem 1 (satır 85-93):** Çakışma döngüsü `$sayi`'yi 2'den başlatır; ek uzunluğu gövdeden kırparken `MAKS - mb_strlen($ek)` hesaplar — toplam asla 80'i aşmaz.
**Gözlem 2 (satır 108-112):** `yasakliMi` uzantıyı noktadan ayırır: `con.mp3` → gövde `con` → yasaklı. `$nokta > 0` şartı `.htaccess` gibi nokta-ile-başlayan adlarda gövdeyi boş bırakmaz.
**Gözlem 3 (satır 120-130):** `siraPrefix` clamp yapar: `SIRA_MIN` altı → `01-`, `SIRA_MAX` üstü → `99-`.
**Gözlem 4 (satır 47-50):** `fold` tek `strtr` çağrısıdır — döngü yok, 12 eşlemenin tamamı tek geçişte uygulanır.

## §7 Taxonomy.php — Tam Dosya Bloğu

**Dosya:** `media.coremusic.net/src/Media/Taxonomy.php` · **Satır:** 1-104 (tamamı) · **Ölçüm:** 2026-10-13

```php
<?php

declare(strict_types=1);

namespace Media;

/**
 * Kapalı (closed) taksonomi okuyucusu — config/taxonomy.json (17 anahtar).
 *
 * DB/JSON drift'inin tek referansı burasıdır: `values()` / `isValid()` yalnız
 * bu dosyadaki seti döner. `etiket[]` açık (open) set olduğundan denetlenmez.
 */
final class Taxonomy
{
    /** @var array<string, list<string>> */
    private array $defs = [];

    private ?string $yuklemeHatasi = null;

    public function __construct(string $dosya)
    {
        if ($dosya === '') {
            $this->yuklemeHatasi = 'taxonomy dosyasi verilmedi';

            return;
        }
        $ham = @file_get_contents($dosya);
        if ($ham === false) {
            $this->yuklemeHatasi = "taxonomy okunamadi: {$dosya}";

            return;
        }
        $cozulmus = json_decode($ham, true);
        if (!is_array($cozulmus)) {
            $this->yuklemeHatasi = 'taxonomy JSON gecersiz: ' . json_last_error_msg();

            return;
        }
        $tanimlar = $cozulmus['definitions'] ?? null;
        if (!is_array($tanimlar)) {
            $this->yuklemeHatasi = "taxonomy 'definitions' yok: {$dosya}";

            return;
        }
        foreach ($tanimlar as $anahtar => $degerler) {
            if (!is_string($anahtar) || !is_array($degerler)) {
                continue;
            }
            $liste = [];
            foreach ($degerler as $d) {
                if (is_string($d)) {
                    $liste[] = $d;
                }
            }
            $this->defs[$anahtar] = $liste;
        }
    }

    /** Proje kökünden (config/taxonomy.json) yükler. */
    public static function fromProject(?string $kok = null): self
    {
        $kok ??= dirname(__DIR__, 2);

        return new self($kok . '/config/taxonomy.json');
    }

    /**
     * @return list<string> Alanın kapalı seti; bilinmeyen alan → []
     */
    public function values(string $alan): array
    {
        return $this->defs[$alan] ?? [];
    }

    /**
     * Değer kapalı sette mi? Bilinmeyen alan → false.
     */
    public function isValid(string $alan, mixed $deger): bool
    {
        if ($this->defs === [] || !array_key_exists($alan, $this->defs)) {
            return false;
        }

        return in_array($deger, $this->defs[$alan], true);
    }

    public function has(string $alan): bool
    {
        return array_key_exists($alan, $this->defs);
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return array_keys($this->defs);
    }

    public function yuklemeHatasi(): ?string
    {
        return $this->yuklemeHatasi;
    }
}
```

## §8 Taxonomy — Hata Yolu ve Referans Analizi

| Hata Yolu | Tetikleyen Satır | `$yuklemeHatasi` Mesajı | Sonuç |
|---|---|---|---|
| Boş dosya adı | 22-26 | `taxonomy dosyasi verilmedi` | `defs` boş kalır |
| Dosya okunamıyor | 27-32 | `taxonomy okunamadi: {dosya}` | `@` ile susturulmuş uyarı |
| JSON bozuk | 33-38 | `taxonomy JSON gecersiz: {json_last_error_msg}` | hata mesajı PHP çekirdeğinden |
| `definitions` yok | 39-44 | `taxonomy 'definitions' yok: {dosya}` | yapı sözleşmesi ihlali |
| Anahtar/değer tipi yanlış | 46-48 | sessiz `continue` | hatalı kayıt atlanır |

**Gözlem 1 (satır 78-85):** `isValid` bilinmeyen alanı `false` döner — `$this->defs === []` kontrolü ayrıca yükleme-hatası durumunu da kapsar (henüz hiçbir alan geçerli değildir).
**Gözlem 2 (satır 60-65):** `fromProject` `dirname(__DIR__, 2)` ile proje kökünü hesaplar: `src/Media/` → iki seviye yukarı → kök; `config/taxonomy.json` köke göre çözülür.
**Gözlem 3 (satır 84):** `in_array(..., true)` strict — tip kaynaklı sahte eşleşme (int 0 vs string "0") engelli.
**Gözlem 4 (doküman, satır 8-11):** `etiket[]` açık settir ve denetlenmez — kapalı enum yalnız `definitions` altındaki 17 anahtara bağlıdır.

## §9 Üç Sınıfın Ortak Davranışları

| Davranış | Ulid | Slugger | Taxonomy |
|---|---|---|---|
| Hata istisnası fırlatmaz | evet — hep string/bool döner | evet — hep string döner | evet — hata alanı `yuklemeHatasi`'nde |
| Dosya sistemine yazar | hayır | hayır | hayır — yalnız okur |
| DB'ye yazar | hayır | hayır | hayır |
| Durum değiştirir | hayır (static) | hayır (static) | hayır (ctor'da doldurulur, sonra salt-okunur) |
| UTF-8 duyarlı | evet (`mb_strlen`/`mb_substr`) | evet (`mb_strtolower`/`mb_substr`) | veriye bağlı |
| ADR-092 bağlantısı | §6.1 kural 2 | §6.1 kural 1 | §6.1 kapalı enum |

**Sonuç:** Üç sınıf da yan etkisizdir (immutable usage); medya ağacına yazma yalnız `CatalogWriter` + CLI commit kapısı üzerinden olur (surucu §commit kapısı).

## §10 Blok Bütünlük Kaydı (dosya ↔ blok eşlemesi)

| Dosya | Blok § | Satır aralığı | Eksik/Yanlış satır |
|---|---|---|---|
| Ulid.php | §3 | 1-84 | yok — 84/84 kopyalandı |
| Slugger.php | §5 | 1-140 | yok — 140/140 kopyalandı |
| Taxonomy.php | §7 | 1-104 | yok — 104/104 kopyalandı |

**Doğrulama yöntemi:** 2026-10-13 oturumunda üç dosya da `read` ile tam okundu; bloklar okuma çıktısının birebir devamıdır. Kırpma, özetleme veya yorum ekleme yapılmamıştır.

## §11 İddia Kayıt Defteri

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| PSR-14 EventDispatcher referansı | kullanılmadı — bu dosya yalnız medya saf sınıfı içerir | 2026-10-13 | geçerli |
| ULID spesifikasyonu (ulidspec) | URL kullanılmadı — sınıf dokümanı yalnız ADR-092'yi kaynak gösterir | 2026-10-13 | geçerli |
| Crockford base32 alfabe tanımı | URL kullanılmadı — kaynak: Ulid.php satır 10 yorumu | 2026-10-13 | geçerli |
| Windows-reserved ad listesi | URL kullanılmadı — kaynak: Slugger.php satır 33-37 + adlandirma.md §3 | 2026-10-13 | geçerli |

**Yeni URL:** Bu dosyada hiçbiri eklenmedi — envanterdeki 2.929 URL ile kesişim 0.

## §12 Bilinen Boşluklar

| # | Boşluk | Durum |
|---|---|---|
| 1 | Validator.php tam dosya bloğu yok — 10348 bayt, yalnız envanter var (anayasa §envanter) | ⚠️ VERIFICATION REQUIRED — ayrı genişletme |
| 2 | CatalogWriter.php tam dosya bloğu yok — 491 bayt, yalnız envanter var | ⚠️ VERIFICATION REQUIRED — ayrı genişletme |
| 3 | `php -l` 3/3 kanıtı bu oturumda çalıştırılamadı (PHP runtime yok) | ⚠️ VERIFICATION REQUIRED |
| 4 | Slugger döngüsünün sonsuz döngü sınırı yok — `$sayi` taşarsa `mb_substr` gövdeyi YEDEK'e düşürür; teorik durum test edilmedi | UNKNOWN — test kanıtı yok |

## §13 Değişiklik Kaydı

| Tarih | Değişiklik | Kanan |
|---|---|---|
| 2026-10-13 | İlk yazım — 3 tam dosya bloğu (Ulid 84, Slugger 140, Taxonomy 104) + analiz tabloları | Vault Steward oturumu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
