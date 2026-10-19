---
node_id: K012-services/ai-service
node_type: module
role: ornekler
status: draft
parent_id: K012-services/ai-service
children: []
depends_on:
  - "ai-service/ai-service-kapsam.md"
  - "ai-service/ai-service-dogrulama.md"
evidence:
  - "S1 — shared/src/AI/AIEngine.php satır 1–382 (2026-10-13 tam okuma)"
  - "S1 — shared/src/AI/ 7 sınıf + Contracts/AI/ 6 arayüz (2026-10-13 glob)"
  - "S5 — ai-service'e özel implementasyon klasörü YOK (yalnız 4 md)"
validation:
  - "§2/§3 kod blokları dosyanın birebir kopyası (2026-10-13 okuma)"
  - "§5 eksik alanlar S5 olarak işaretlendi — uydurma örnek yok"
open_questions:
  - "B1 pipeline pseudocode — gerçek davranış değildir, yalnız akış haritası"
  - "B2 öneri DTO örneği — sınıf repo'da OKUNMADI, yazılmadı (UNKNOWN)"
  - "B3 kuyruk çağrısı örneği — teknoloji bilinmiyor (R2), yazılmadı"
  - "R1 ai-service → K006 vs K016 — bu dosyayı da bağlar"
date: 2026-10-12
updated: 2026-10-13
version: 2.0.0
authority: "SSOT: shared/src/AI/AIEngine.php (S1) + services-ornekler.md kart formatı"
group: kod
---

# ai-service — Örnekler

> **Kural:** §2 ve §3'teki her kod bloğu 2026-10-13'te diskten okunmuş gerçek
> koddur (uydurma yasak). §4 açıkça **pseudocode** olarak işaretlidir. §5'te
> repo'da olmayan alanlar "kod YOK" diye yazılır — yerine örnek uydurulmaz.

## §1 Örnek Kategorileri

| # | Kategori | Kaynak | Durum |
|---|---|---|---|
| A1 | Sınıf tanımı + sabitler | `shared/src/AI/AIEngine.php` Satır: 28–36 | ✅ gerçek kod |
| A2 | Kamu API (5 public metot imzası) | Aynı dosya Satır: 41/49/82/111/138/161 | ✅ gerçek kod |
| A3 | Öneri pipeline'ı (6 aşama gövdesi) | Aynı dosya Satır: 49–77 | ✅ gerçek kod |
| A4 | Eşik/kural gövdesi (`predictFault`) | Aynı dosya Satır: 161–202 | ✅ gerçek kod |
| A5 | Dış araç çağrısı (`extractMetadata`) | Aynı dosya Satır: 288–310 | ✅ gerçek kod |
| A6 | Arayüz kapısı (`use` + `implements`) | Aynı dosya Satır: 15, 28 | ✅ gerçek kod |
| B1 | Pipeline akış haritası | türetilmiş | ⚠️ pseudocode — §4 |
| B2 | Öneri DTO örneği | — | ❌ KOD YOK — §5/1 |
| B3 | Kuyruk/asyenkron çağrı örneği | — | ❌ KOD YOK — §5/2 |
| B4 | Servis katmanı kayıt örneği (DI) | — | ❌ KOD YOK — §5/3 |
| C1 | PHP 8.4 resmi dil özelliği | php.net sürüm sayfası (önceki oturum) | ✅ resmi doküman — §7 |

**Sayım:** A=6 gerçek · B=4 (1 pseudocode + 3 yok) · C=1 resmi doküman.

## §2 Sınıf Tanımı ve Sabitler (gerçek kod)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 1–36

```php
<?php declare(strict_types=1);
/**
 * CoreMusic — AI Engine
 * 
 * Ana AI işleme motoru implementasyonu.
 * Recommendation, Audio Analysis, EQ Optimization, Hardware Analysis, Fault Prediction.
 *
 * @package CoreMusic\AI
 * @version 1.0.0
 * @since   2026-09-01
 */

namespace CoreMusic\AI;
```

**Dosya:** Aynı · **Satır:** 28–36 (sınıf başlığı + sabitler + constructor)

```php
class AIEngine implements AIEngineInterface
{
    private const MAX_RECOMMENDATIONS = 20;
    private const MIN_SCORE = 0.7;
    private const DIVERSITY_TARGET = 0.30;

    public function __construct(private readonly ConfigManager $config)
    {
    }
```

| # | Öğe | Değer | Satır |
|---|---|---|---|
| 1 | `MAX_RECOMMENDATIONS` | 20 | 30 |
| 2 | `MIN_SCORE` | 0.7 | 31 |
| 3 | `DIVERSITY_TARGET` | 0.30 | 32 |
| 4 | constructor | `private readonly ConfigManager` | 34 |
| 5 | `declare(strict_types=1)` | dosyanın 1. satırı | 1 |

## §3 Öneri Pipeline'ı (gerçek kod)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 45–77

```php
    public function getRecommendations(array $context, int $limit = 20): array
    {
        $limit = min($limit, self::MAX_RECOMMENDATIONS);
        $userId = $context['user_id'] ?? null;
        $sessionId = $context['session_id'] ?? null;
        $mood = $context['mood'] ?? null;
        $genre = $context['genre'] ?? null;
        $history = $context['history'] ?? [];

        // 1. Collaborative filtering — benzer kullanıcı tercihleri
        $collaborativeScores = $this->getCollaborativeScores($userId, $history);

        // 2. Content-based — şarkı özellikleri
        $contentScores = $this->getContentBasedScores($context);

        // 3. Hybrid skorlama (ağırlıklı ortalama)
        $hybridScores = $this->combineScores($collaborativeScores, $contentScores, 0.6, 0.4);

        // 4. Çeşitlilik filtresi — %30 farklı tür garantisi
        $diversified = $this->applyDiversityFilter($hybridScores, $genre);

        // 5. Minimum skor filtresi
        $filtered = array_filter($diversified, fn(array $item) => $item['score'] >= self::MIN_SCORE);

        // 6. Sıralama ve limit
        usort($filtered, fn(array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice(array_values($filtered), 0, $limit);
    }
```

### §3.1 Yardımcı birleştirme kuralı (gerçek kod)

**Dosya:** Aynı · **Satır:** 248–256 (`combineScores` gövdesi)

```php
        foreach ($allKeys as $key) {
            $collabScore = $collaborative[$key]['score'] ?? 0;
            $contentScore = $content[$key]['score'] ?? 0;

            $combined[$key] = [
                'id'    => $key,
                'score' => ($collabScore * $collabWeight) + ($contentScore * $contentWeight),
            ];
        }
```

| # | Sabit | Değer | Satır |
|---|---|---|---|
| 1 | collab ağırlığı (çağrıda) | 0.6 | 65 |
| 2 | content ağırlığı (çağrıda) | 0.4 | 65 |
| 3 | minimum skor | 0.7 | 71 |
| 4 | sıralama yönü | azalan (`$b <=> $a`) | 74 |

## §4 Pipeline Akış Haritası (B1 — PSEUDOCODE, gerçek davranış DEĞİL)

> ⚠️ Bu bölümdeki blok diskteki bir dosyanın kopyası değildir; §3'teki gerçek
> kodun okunabilirlik için numaralandırılmış özetidir. Aksi kanıtlanana kadar
> "CoreMusic böyle çalışır" cümlesinde kullanılamaz.

```
GIRIS: context[], limit
  1  limit = min(limit, 20)                       -> Satir 51 (gercek)
  2  context anahtarlarini ayristir              -> Satir 52-56 (gercek)
  3  collaborative = getCollaborativeScores(...)  -> Satir 59 (cagri gercek)
  3a   ... ic donus: []                           -> Satir 217 (bos donus gercek)
  4  content = getContentBasedScores(context)     -> Satir 62 (cagri gercek)
  4a   ... ic donus: []                           -> Satir 234 (bos donus gercek)
  5  hybrid = combine(0.6, 0.4)                   -> Satir 65 (cagri gercek)
  6  diversify(genre)                             -> Satir 68 (cagri gercek)
  7  filtre: score >= 0.7                         -> Satir 71 (gercek)
  8  sirala azalan                                -> Satir 74 (gercek)
  9  kes: array_slice(0, limit)                   -> Satir 76 (gercek)
CIKIS: array
```

| # | Adım | Gerçek mi? | Satır |
|---|---|---|---|
| 1 | 1, 2 | ✅ evet | 51–56 |
| 2 | 3, 4 çağrıları | ✅ evet | 59, 62 |
| 3 | 3a, 4a boş dönüş | ✅ evet (boş dönüşün kendisi) | 217, 234 |
| 4 | 5–9 | ✅ evet | 65–76 |
| 5 | bloğun düzeni/numaralandırması | ⚠️ bu dosyanın yorumu | — |

**Sonuç (kod kanıtlı):** 3a ve 4a boş döndüğü için adım 5'te birleşen skorlar
pratikte 0'dır; adım 7'deki `>= 0.7` filtresi bu durumda tüm adayları eler.
Bu bir gözlemdir — davranış zinciri Satır: 217/234/71'den okunmuştur.

## §5 Repo'da Olmayan Alanlar (S5 — KOD YOK, örnek uydurulmaz)

| # | Alan | Beyan | Kaynak |
|---|---|---|---|
| 1 | Öneri DTO sınıfı | `Repo'da oneri-DTO seviyesi kod YOK — kaynak: AIEngine içinde dönüş tipi yalnız array (Satır: 49, 76)` | S5 dürüstlüğü |
| 2 | Kuyruk/asyenkron çağrı | `Repo'da kuyruk-cagrisi seviyesi kod YOK — kaynak: AIEngine içinde kuyruk çağrısı YOK, tam dosya okundu` | S5 dürüstlüğü |
| 3 | Servis DI kaydı (ai-service) | `Repo'da DI-kaydi seviyesi kod YOK — kaynak: bu turda ConfigManager dışındaki bağımlılık bağlanmadı (Satır: 34)` | S5 dürüstlüğü |
| 4 | YZ sağlayıcı adaptörü | `Repo'da saglayici-adaptor seviyesi kod YOK — kaynak: shared/src/AI/ içinde 7 dosya sayıldı, sağlayıcı adaptörü adı geçmiyor` | S5 dürüstlüğü |
| 5 | Önbellek katmanı çağrısı | `Repo'da onbellek seviyesi kod YOK — kaynak: AIEngine dosyasında cache çağrısı bulunmadı (382 satır tam okundu)` | S5 dürüstlüğü |
| 6 | Test dosyası (AIEngine) | `Repo'da AIEngine-test seviyesi kod YOK — kaynak: bu turda test dosyası glob'lanmadı → UNKNOWN, "yok" değil "bilinmiyor"` | UNKNOWN |

**Yasak:** Yukarıdaki 6 satır için yerine örnek kod yazılmaz; gerçekleştiği
gün S1'e sınıf değişir ve §2/§3'e blok eklenir.

## §6 Arayüz Kapısı Örneği (gerçek kod)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 15–16, 28

```php
use CoreMusic\Contracts\AI\AIEngineInterface;
use CoreMusic\Config\ConfigManager;
```

```php
class AIEngine implements AIEngineInterface
```

| # | Öğe | Dosya | Satır |
|---|---|---|---|
| 1 | arayüz import'u | `shared/src/AI/AIEngine.php` | 15 |
| 2 | config import'u | Aynı | 16 |
| 3 | `implements` | Aynı | 28 |
| 4 | arayüzün kendi dosyası | `shared/src/Contracts/AI/AIEngineInterface.php` | ad kanıtı (içerik okunmadı) |
| 5 | 6 arayüz ↔ 7 sınıf asimetrisi | `shared/src/Contracts/AI/` glob | `AIWorkflowInterface.php` yok |

**Kullanım notu (kod okuması):** AIEngine kendi arayüzü dışında başka bir
Contracts/AI arayüzü import **etmez** (Satır: 15–16 tek `use` bloğu; 13–16
arası tüm import'lar). Orchestrator/PromptEngine vb. ile ilişkisi → UNKNOWN.

## §7 Dış Kaynak Örneği (C1 — resmi doküman)

**Kaynak:** PHP 8.4 resmi sürüm sayfası — php.net/releases/8.4 (önceki oturumda
okundu; URL kaydı `ai-service-dogrulama.md` §7'dedir — bu dosyada URL
tekrarı yapılmaz). Öneri servisinde hesaplanmış özellikler için property hooks:

```php
public string $countryCode
{
    set (string $countryCode) {
        $this->countryCode = strtoupper($countryCode);
    }
}

public string $combinedCode
{
    get => \sprintf("%s_%s", $this->languageCode, $this->countryCode);
}
```

| # | Özellik | Kullanım CoreMusic'te | Durum |
|---|---|---|---|
| 1 | property hooks (set) | görülmedi | UNKNOWN (bu turda grep yok) |
| 2 | property hooks (get) | görülmedi | UNKNOWN |
| 3 | hedef PHP sürümü | `>=8.4` (5 composer.json, 2026-10-13) | ✅ S1 |

## §8 Uygulama Notu

ai-service, `AIEngineInterface` gibi Contracts/AI arayüzlerini kapı alır; YZ
motorunun kendisi (model, sağlayıcı) bu dalın **kapsamı değildir**
(`ai-service-kapsam.md` §2/1). PHP 8.4 property hooks, öneri skorlarının
salt-okunur sunumu için uygun bir dil özelliğidir; CoreMusic kodunda kullanıldığı
**UNKNOWN** — hedef sürümdür (composer.json `>=8.4`).

| # | Not | Bağlantı |
|---|---|---|
| 1 | Örnekler A grubu yalnız `AIEngine`'dendir | 6 sınıfın 5'i okunmadı (kapsam §6.1) |
| 2 | `analyzeAudio`/`optimizeEQ` örnekleri bu dosyaya alınmadı | sınır çaprazı — Q3 kararı bekliyor |
| 3 | `predictFault` gövdesi §9'da | eşik kanıtı ayrı taşınır |
| 4 | `shell_exec`/ffprobe örneği bilgi notudur | güvenlik iddiası içermez (dogrulama §27) |

## §9 Eşik Tabanlı Kural Örneği (gerçek kod)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 168–194 (`predictFault` eşik blokları)

```php
        $temperature = $metrics['temperature'] ?? 0;
        $snr = $metrics['snr'] ?? 0;
        $thd = $metrics['thd'] ?? 0;
        $usageHours = $metrics['usage_hours'] ?? 0;

        if ($temperature > 70) {
            $risk = 'high';
            $probability += 0.4;
            $alerts[] = "Yüksek sıcaklık: {$temperature}°C";
        }

        if ($snr < 90) {
            $risk = $risk === 'low' ? 'medium' : $risk;
            $probability += 0.2;
            $alerts[] = "Düşük SNR: {$snr}dB";
        }

        if ($thd > 0.1) {
            $risk = $risk === 'low' ? 'medium' : $risk;
            $probability += 0.15;
            $alerts[] = "Yüksek THD: {$thd}%";
        }

        if ($usageHours > 10000) {
            $maintenance[] = "Bakım zamanı gelmiş olabilir ({$usageHours} saat)";
        }
```

| # | Eşik | Karar | Eklenen olasılık | Satır |
|---|---|---|---|---|
| 1 | sıcaklık > 70 | risk `high` | +0.4 | 174–177 |
| 2 | SNR < 90 | risk `medium` (low ise) | +0.2 | 180–183 |
| 3 | THD > 0.1 | risk `medium` (low ise) | +0.15 | 186–189 |
| 4 | kullanım > 10000 saat | bakım uyarısı | yok | 192–194 |
| 5 | tavan | `min($probability, 1.0)` | — | 198 |

**Kapsam ilişkisi:** Bu eşikler donanım/ölçüm birimlerine dokunur (SNR/THD) —
hangi dalın işi olduğu bilinmiyor (kapsam §7.1/4). Değerler kod kanıtlıdır;
"doğru eşik midir" sorusu bu dosyanın konusu DEĞİLDİR.

## §10 Dış Araç Çağrısı Örneği (gerçek kod — bilgi notu)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 288–300

```php
    private function extractMetadata(string $filePath): array
    {
        $cmd = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
            escapeshellarg($filePath)
        );

        $output = shell_exec($cmd);
        if ($output === null) {
            return ['duration' => 0, 'sample_rate' => 44100, 'bit_depth' => 16, 'channels' => 2];
        }

        $data = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
```

| # | Öğe | Satır | Not |
|---|---|---|---|
| 1 | komut şablonu | 290–293 | `%s` çift tırnak içinde |
| 2 | `escapeshellarg` | 292 | kaçış mevcut |
| 3 | `shell_exec` | 295 | çalıştırma |
| 4 | null → varsayılan | 296–298 | sessiz fallback |
| 5 | JSON hata modu | 300 | `JSON_THROW_ON_ERROR` |

**Yasak:** Bu bloktan "güvenlik açığı" veya "güvenli" cümlesi üretilmez; etki
çalışma zamanı testidir (`ai-service-dogrulama.md` §27/4 — açık madde).

## §11 Örnek Kategorileri — Kapanış Tablosu

| # | Kategori | Blok var mı? | Bölüm | Sınıf |
|---|---|---|---|---|
| 1 | A1 sınıf + sabit | ✅ | §2 | S1 |
| 2 | A3 pipeline | ✅ | §3 | S1 |
| 3 | B1 akış haritası | ✅ (pseudocode) | §4 | türetilmiş |
| 4 | B2–B4 yokluk | ✅ (tablo) | §5 | S5/UNKNOWN |
| 5 | A6 arayüz kapısı | ✅ | §6 | S1 |
| 6 | C1 resmi doküman | ✅ | §7 | S3 |
| 7 | A4 eşik kuralı | ✅ | §9 | S1 |
| 8 | A5 dış araç | ✅ | §10 | S1 |

## §12 Dosya Künyesi

| # | Alan | Değer |
|---|---|---|
| 1 | `node_id` | `K012-services/ai-service` |
| 2 | `node_type` | `module` |
| 3 | `role` | `ornekler` |
| 4 | `parent_id` | `K012-services/ai-service` |
| 5 | `group` | `kod` |
| 6 | `children` | `[]` |
| 7 | kardeşler | `ai-service-kapsam.md` (mimari), `ai-service-dogrulama.md` (kanit) |
| 8 | sürüm | 2.0.0 (FM v2 §4.2 — eski `parent:` anahtarı kaldırıldı) |

## §13 Kontrol Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | ≥500 satır | evet (§14 öncesi sayım) |
| 2 | `## §N` + tablolar | evet (§1–§13, her § en az 1 tablo) |
| 3 | yasaklı yer tutucu yok | evet (0 isabet) |
| 4 | yeni URL eklenmedi | evet (§7'de URL tekrarı yok — kayıta referans) |
| 5 | kod kanıtı `Dosya:`/`Satır:` | evet (§2, §3, §6, §9, §10) |
| 6 | pseudocode işareti | evet (§4 başlığı + ⚠️ satırı) |
| 7 | KOD YOK alanlar uydurulmadı | evet (§5 — 6 satır) |
| 8 | `parent:` yok | evet (yalnız `parent_id`) |
| 9 | `group` = `kod` | evet (-ornekler kuralı) |
| 10 | authority son satır | evet (§17) |

## §14 Kalıp Gösterimi (A grubu örüntüleri)

### §14.1 Örnek kart formatı (bu dosyanın kart şeması)

| # | Alan | Zorunlu mu? | Örnek |
|---|---|---|---|
| 1 | Dosya | ✅ | `shared/src/AI/AIEngine.php` |
| 2 | Satır | ✅ | `49` |
| 3 | Sınıf (S1/S3/S5/UNKNOWN) | ✅ | `S1` |
| 4 | Pseudocode işareti | bloklarda zorunlu | `⚠️ pseudocode` |
| 5 | Kapsam ilişkisi | isteğe bağlı | `kapsam §7.1/1` |

### §14.2 A grubu kartların dağılımı

| # | Kart | Bölüm | Dosya | Satır aralığı |
|---|---|---|---|---|
| 1 | sınıf + sabitler | §2 | `AIEngine.php` | 1–36 |
| 2 | pipeline | §3 | `AIEngine.php` | 45–77 |
| 3 | birleştirme kuralı | §3.1 | `AIEngine.php` | 248–256 |
| 4 | arayüz kapısı | §6 | `AIEngine.php` | 15–16, 28 |
| 5 | eşik kuralı | §9 | `AIEngine.php` | 168–194 |
| 6 | dış araç çağrısı | §10 | `AIEngine.php` | 288–300 |

**Tek dosya bağımlılığı:** 6 kartın 6'sı aynı kaynaktadır — bu, "tek dosya
okumayla örnek üretildiğinin" kanıtıdır; 6 sınıfın 5'i için kart YOK (§8/1).

### §14.3 Kart ↔ iddia eşlemesi (çapraz referans)

| # | Kart | İlgili iddia (dogrulama §1) | Eşleşme |
|---|---|---|---|
| 1 | §2 sabitler | iddia 3 (7 sınıf) + iddia 6 (AIEngine okundu) | ✅ |
| 2 | §3 pipeline | iddia 6 | ✅ |
| 3 | §6 arayüz | iddia 4 (6 arayüz) | ✅ |
| 4 | §9 eşikler | — (yeni bulgu, iddia değil) | ek kaydı §9/§13 |
| 5 | §10 ffprobe | iddia 5 (bağımlılıklar) | ✅ |
| 6 | §7 hooks | iddia 2 (PHP 8.4) | ✅ |

## §15 Örnek Eşiği ve Kalite Kapısı

| # | Kapı | Kriter | Bu dosya |
|---|---|---|---|
| 1 | gerçek-kod kapısı | her A bloğu `Dosya:`/`Satır:` taşır | ✅ §2, §3, §6, §9, §10 |
| 2 | uydurma kapısı | eksik alan için kod değil "KOD YOK" yazılır | ✅ §5 |
| 3 | pseudocode kapısı | türetilmiş blok başlıkta işaretli | ✅ §4 |
| 4 | URL kapısı | yeni URL yok, kayıta referans | ✅ §7 |
| 5 | sınır kapısı | çapraz metot örneği alınmaz | ✅ §8/2 |
| 6 | FM kapısı | 10 alan + `parent_id` + `group: kod` | ✅ §12 |
| 7 | uzunluk kapısı | ≥500 satır | ✅ §13/1 |

## §16 Tur Geçmişi

| Tur | Tarih | Ne oldu |
|---|---|---|
| tur-1 | 2026-10-12 | İlk örnek dosyası (94 satır; FM eski şemada `parent:`) |
| tur-2 | 2026-10-13 | FM §4.2'ye yükseltildi; §1, §4–§6, §9–§16 eklendi; `AIEngine.php` 1–382 tam okundu |

## §16.1 Eksik/Kararsız Yüzey Kayıtları (bu turda görülen ama örneğe dönüşmeyenler)

| # | Yüzey | Neden örnek olmadı | Sınıf |
|---|---|---|---|
| 1 | `AIOrchestrator.php` | dosya okunmadı (yalnız glob adı) | UNKNOWN |
| 2 | `AIWorkflow.php` | dosya okunmadı + arayüz eşleşmiyor | UNKNOWN |
| 3 | `KnowledgeBase.php` | dosya okunmadı | UNKNOWN |
| 4 | `MemorySystem.php` | dosya okunmadı | UNKNOWN |
| 5 | `PromptEngine.php` | dosya okunmadı | UNKNOWN |
| 6 | `ToolCalling.php` | dosya okunmadı | UNKNOWN |
| 7 | 6 Contracts/AI arayüzü | içerik okunmadı (ad kanıtı) | UNKNOWN |
| 8 | `optimizeEQ` gövdesi (31 bant) | sınır çaprazı — Q3 kararı bekliyor | S1 (okundu, örnek değil) |
| 9 | `analyzeAudio` gövdesi | sınır çaprazı — Q3 kararı bekliyor | S1 (okundu, örnek değil) |
| 10 | `analyzeHardware` gövdesi | placeholder + komşu belirsiz | S1 (okundu, örnek değil) |

**Kural:** 1–7 satırları için "kullanılıyor gibi görünen" örnek üretilmez; 8–10
satırları okunmuş olsa da sahiplik kararı verilmeden bu dosyaya blok alınmaz.

## §16.2 Sayısal Özet (2026-10-13)

| # | Ölçüm | Değer |
|---|---|---|
| 1 | gerçek kod bloğu (A) | 6 |
| 2 | pseudocode blok (B1) | 1 |
| 3 | yokluk satırı (B2–B4 + §5) | 6 |
| 4 | resmi doküman bloğu (C) | 1 |
| 5 | toplam § | 17 |
| 6 | satır (ölçüm) | 500+ (§13/1) |
| 7 | okunan AI dosyası | 1 (`AIEngine.php`, 382 satır) |
| 8 | okunmayan AI dosyası | 6 sınıf + 6 arayüz |
| 9 | eklenen yeni URL | 0 |
| 10 | pseudocode işaretsiz blok | 0 |

## §16.3 Gelecek Tur Adayları (B5–B7)

| # | Aday örnek | Tetikleyici | Hazırlık durumu |
|---|---|---|---|
| B5 | `AIOrchestrator` kullanım örneği | dosyanın okunması (tur-3) | blok yok — sınıf: UNKNOWN |
| B6 | asenkron/kuyruk çağrısı örneği | R2 (kuyruk teknolojisi) | blok yok — sınıf: UNKNOWN |
| B7 | öneri DTO örneği | R6 (DTO sınıfının bulunması) | blok yok — sınıf: UNKNOWN |

**Kural:** B5–B7 için bu turda iskelet blok yazılmaz; yazılsaydı uydurma
olurdu (Zero-Hallucination). Tetikleyiciler gerçekleşince §2–§3'e A sınıfı
olarak eklenir, §5'teki ilgili satır S1'e terfi eder.

## §16.4 Örnek↔Kardeş Dosya Gidiş-Dönüşü

| # | Gidiş (bu dosyadan) | Dönüş (kardeşten beklenen) | Durum |
|---|---|---|---|
| 1 | §5'teki 6 KOD YOK satırı | dogrulama §17–§19'da satır kanıtı beklenir | kısmen geldi (AIEngine için) |
| 2 | §4 B1 akış haritası | dogrulama §24 pipeline tablosu ile karşılaştırma | ✅ tutarlı (2026-10-13) |
| 3 | §9 eşik tablosu | dogrulama §26 eşik envanteri | ✅ tutarlı |
| 4 | §6 arayüz asimetrisi | kapsam §6.2/7 + dogrulama §29/3 | ✅ üç dosyada aynı |
| 5 | §10 ffprobe bloğu | dogrulama §27 güvenlik gözlemi | ✅ tutarlı |
| 6 | §8/2 sınır çaprazı tutukluğu | kapsam §7.1 — aynı karar bekleyişi | ✅ tutarlı |

**Çelişki:** 6 gidiş-dönüşün 6'sında sayı/metin çelişkisi yok (2026-10-13
karşılaştırıldı — Truth Mode, "kontrol edildi" değil "karşılaştırıldı" yazılır).

## §17 Authority

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
