---
type: adr
category: ai
title: "ADR-035: System Prompt Engineering"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-035: System Prompt Engineering

## 1. Amaç

CoreMusic'te AI system prompt mühendisliği standartlarını tanımlar. [[ADR-035-system-prompt-engineering]] Frozen karardır. Bu karar, tüm AI ajanlarının ve mühendislerinin uyması gereken prompt engineering kurallarını kapsar.

Bu ADR'nin amacı:
- Zero Hallucination politikasını uygulamak
- Doğrulama protokollerini tanımlamak
- Prompt kalite standartlarını koymak
- AI güvenilirliğini sağlamak
- Yanlış bilgi üretimini önlemek
- Red Team · Truth Mode uygulamak

## 2. Bağlam

| Faktör | Değer |
|--------|-------|
| **Kullanım** | Tüm AI ajanları |
| **Hedef** | Doğru, güvenilir AI çıktısı |
| **Politika** | Zero Hallucination |
| **Doğrulama** | Web araması + kaynak |
| **Mod** | Truth Mode · Human Mode |
| **Review** | Red Team adversarial |
| **Logging** | Tüm prompt'lar loglanır |
| **Monitoring** | Kalite takibi |
| **Training** | Prompt training standartları |
| **Compliance** | CoreMusic kuralları |

### 2.1 Neden Prompt Engineering?

- **Doğruluk:** Yanlış bilgiyi önleme
- **Güvenilirlik:** Tutarlı çıktı
- **Verimlilik:** Daha az iterasyon
- **Güvenlik:** Zararlı içerik önleme
- **Kalite:** Yüksek kalite çıktı
- **İzlenebilirlik:** Audit trail

### 2.2 Prompt Engineering Türleri

| Tür | Kullanım | Öncelik |
|-----|----------|---------|
| **System Prompt** | AI davranış tanımlama | CRITICAL |
| **User Prompt** | Kullanıcı isteği | HIGH |
| **Few-shot** | Örnek ile yönlendirme | MEDIUM |
| **Chain-of-thought** | Adım adım düşünme | MEDIUM |
| **Retrieval** | Dış kaynak entegrasyonu | LOW |

## 3. Karar

### 3.1 Prompt Engineering Kararı

| Karar | Değer | Gerekçe |
|-------|-------|---------|
| **Zero Hallucination** | ✅ Zorunlu | Doğruluk |
| **Web Doğrulama** | ✅ Zorunlu | Kaynak doğrulama |
| **Truth Mode** | ✅ Zorunlu | Gerçek mod |
| **Red Team** | ✅ Zorunlu | Adversarial review |
| **Logging** | ✅ Zorunlu | İzlenebilirlik |
| **Monitoring** | ✅ Zorunlu | Kalite takibi |
| **Training** | ✅ Zorunlu | Sürekli iyileştirme |
| **Compliance** | ✅ Zorunlu | Kural uyumu |
| **Source Citation** | ✅ Zorunlu | Kaynak gösterme |
| **Confidence Score** | ✅ Zorunlu | Güven skoru |

### 3.2 Zero Hallucination Kuralları

| Kural | Açıklama | İhlal |
|-------|----------|-------|
| **Doğrulanamayan bilgi** | `VERIFICATION REQUIRED` etiketi | İçerik silinir |
| **Kaynak gösterme** | Her iddia için kaynak | Güvenilirlik düşer |
| **Web araması** | Teknoloji/ API doğrulama | Yanlış bilgi |
| **Güncellik** | 2026 verileri | Eski bilgi |
| **Kesinlik** | Belirsizlik ifadesi | Yanlış iddia |
| **Tutarlılık** | Çelişki yok | Güven kaybı |

## 4. Teknik Detaylar

### 4.1 System Prompt Formatı

```markdown
# System Prompt: CoreMusic AI Agent

## Identite
Sen CoreMusic ekosisteminde çalışan bir AI asistanısın.

## Kurallar
1. **Zero Hallucination:** Doğrulanamayan bilgiyi ASLA üretme. `VERIFICATION REQUIRED` yaz.
2. **Truth Mode:** Her zaman gerçeği söyle. Yanlış bilgi vermeyin.
3. **Human Mode:** İnsanasaygılı ol. Kibar ve yardımcı ol.
4. **Red Team:** Adversarial review uygula. Her çıktıyı sorgula.
5. **Source Citation:** Her iddia için kaynak göster.

## Yasaklar
- Uydurma bilgi üretmek
- Kaynaksız iddia yapmak
- Eski/bilinmeyen bilgi vermek
- Tehlikeli içerik oluşturmak
- Kişisel veri ifşa etmek

## Çıktı Formatı
- Kısa ve öz
- Madde işaretleri ile
- Kod örnekleri ile
- Kaynak referansları ile
```

### 4.2 Doğrulama Protokolü

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

class PromptValidator
{
    /**
     * ✅ Prompt'u doğrula
     */
    public function validate(string $prompt): array
    {
        $issues = [];

        // 1. Hallüsinasyon kontrolü
        if ($this->containsHallucination($prompt)) {
            $issues[] = 'Hallüsinasyon tespit edildi';
        }

        // 2. Kaynak kontrolü
        if (!$this->hasSource($prompt)) {
            $issues[] = 'Kaynak eksik';
        }

        // 3. Güncellik kontrolü
        if ($this->isOutdated($prompt)) {
            $issues[] = 'Eski bilgi tespit edildi';
        }

        // 4. Tehlike kontrolü
        if ($this->containsDangerous($prompt)) {
            $issues[] = 'Tehlikeli içerik tespit edildi';
        }

        // 5. Tutarlılık kontrolü
        if ($this->hasInconsistency($prompt)) {
            $issues[] = 'Tutarsızlık tespit edildi';
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'confidence' => $this->calculateConfidence($prompt),
        ];
    }

    /**
     * ✅ Hallüsinasyon kontrolü
     */
    private function containsHallucination(string $prompt): bool
    {
        // Bilinen yanlış kalıpları kontrol et
        $hallucinationPatterns = [
            '/benim bildiğim kadarıyla/i',
            '/muhtemelen/i',
            '/sanırım/i',
            '/emin değilim/i',
            '/doğrulanamayan/i',
        ];

        foreach ($hallucinationPatterns as $pattern) {
            if (preg_match($pattern, $prompt)) {
                return true;
            }
        }

        return false;
    }

    /**
     * ✅ Kaynak kontrolü
     */
    private function hasSource(string $prompt): bool
    {
        $sourcePatterns = [
            '/\[\[.*\]\]/', // Wiki-link
            '/ADR-\d+/', // ADR referansı
            '/docs:\//', // Doküman referansı
            '/kaynak:/i', // Kaynak belirtme
            '/referans:/i', // Referans belirtme
        ];

        foreach ($sourcePatterns as $pattern) {
            if (preg_match($pattern, $prompt)) {
                return true;
            }
        }

        return false;
    }

    /**
     * ✅ Güncellik kontrolü
     */
    private function isOutdated(string $prompt): bool
    {
        // Eski yıl referanslarını kontrol et
        $currentYear = (int)date('Y');
        $oldYears = range(2020, $currentYear - 2);

        foreach ($oldYears as $year) {
            if (strpos($prompt, (string)$year) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * ✅ Tehlike kontrolü
     */
    private function containsDangerous(string $prompt): bool
    {
        $dangerPatterns = [
            '/hack/i',
            '/exploit/i',
            '/vulnerability/i',
            '/injection/i',
            '/password.*=.*["\'][^"\']*["\']/', // Hardcoded password
            '/api[_-]?key.*=.*["\'][^"\']*["\']/', // Hardcoded API key
        ];

        foreach ($dangerPatterns as $pattern) {
            if (preg_match($pattern, $prompt)) {
                return true;
            }
        }

        return false;
    }

    /**
     * ✅ Tutarlılık kontrolü
     */
    private function hasInconsistency(string $prompt): bool
    {
        // Çelişkili ifadeleri kontrol et
        $contradictions = [
            ['yasak', 'zorunlu'],
            ['mümkün', 'imkansız'],
            ['her zaman', 'asla'],
        ];

        foreach ($contradictions as $pair) {
            if (strpos($prompt, $pair[0]) !== false && 
                strpos($prompt, $pair[1]) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * ✅ Güven skoru hesapla
     */
    private function calculateConfidence(string $prompt): float
    {
        $score = 1.0;

        // Kaynak varsa +0.2
        if ($this->hasSource($prompt)) {
            $score += 0.2;
        }

        // Hallüsinasyon varsa -0.3
        if ($this->containsHallucination($prompt)) {
            $score -= 0.3;
        }

        // Tehlike varsa -0.5
        if ($this->containsDangerous($prompt)) {
            $score -= 0.5;
        }

        return max(0.0, min(1.0, $score));
    }
}
```

### 4.3 Web Doğrulama

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

class WebVerifier
{
    /**
     * ✅ Web'de doğrula
     */
    public function verify(string $claim): array
    {
        $results = [
            'verified' => false,
            'sources' => [],
            'confidence' => 0.0,
        ];

        // Web araması yap
        $searchResults = $this->search($claim);
        
        // Kaynakları doğrula
        foreach ($searchResults as $result) {
            if ($this->supportsClaim($claim, $result)) {
                $results['sources'][] = $result;
                $results['confidence'] += 0.2;
            }
        }

        $results['verified'] = $results['confidence'] >= 0.6;
        $results['confidence'] = min(1.0, $results['confidence']);

        return $results;
    }

    /**
     * ✅ Web araması
     */
    private function search(string $query): array
    {
        // Web arama API'si kullan
        // Placeholder: Gerçek uygulamada search API kullanılacak
        return [];
    }

    /**
     * ✅ İddiayı doğrula
     */
    private function supportsClaim(string $claim, array $source): bool
    {
        // Kaynağın iddiayı destekleyip desteklemediğini kontrol et
        // Placeholder: Gerçek uygulamada NLP kullanılacak
        return false;
    }
}
```

### 4.4 Red Team Review

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

class RedTeamReviewer
{
    /**
     * ✅ Adversarial review
     */
    public function review(string $output): array
    {
        $issues = [];

        // 1. Güvenlik açığı kontrolü
        $securityIssues = $this->checkSecurity($output);
        $issues = array_merge($issues, $securityIssues);

        // 2. Hallüsinasyon kontrolü
        $hallucinationIssues = $this->checkHallucination($output);
        $issues = array_merge($issues, $hallucinationIssues);

        // 3. Tutarlılık kontrolü
        $consistencyIssues = $this->checkConsistency($output);
        $issues = array_merge($issues, $consistencyIssues);

        // 4. Kalite kontrolü
        $qualityIssues = $this->checkQuality($output);
        $issues = array_merge($issues, $qualityIssues);

        return [
            'passed' => empty($issues),
            'issues' => $issues,
            'score' => $this->calculateScore($output),
        ];
    }

    private function checkSecurity(string $output): array
    {
        $issues = [];

        // Hardcoded secret kontrolü
        if (preg_match('/password\s*=\s*["\'][^"\']+["\']/', $output)) {
            $issues[] = 'Hardcoded password tespit edildi';
        }

        if (preg_match('/api[_-]?key\s*=\s*["\'][^"\']+["\']/', $output)) {
            $issues[] = 'Hardcoded API key tespit edildi';
        }

        // SQL injection kontrolü
        if (preg_match('/SELECT\s+\*\s+FROM/i', $output)) {
            $issues[] = 'SELECT * kullanımı tespit edildi';
        }

        return $issues;
    }

    private function checkHallucination(string $output): array
    {
        $issues = [];

        // Doğrulanamayan iddia kontrolü
        if (preg_match('/\d{4}\s+yıl/', $output)) {
            $issues[] = 'Yıl referansı doğrulanmalı';
        }

        return $issues;
    }

    private function checkConsistency(string $output): array
    {
        $issues = [];

        // Çelişki kontrolü
        $contradictions = [
            ['yasak', 'zorunlu'],
            ['mümkün', 'imkansız'],
        ];

        foreach ($contradictions as $pair) {
            if (strpos($output, $pair[0]) !== false && 
                strpos($output, $pair[1]) !== false) {
                $issues[] = "Tutarsızlık: {$pair[0]} ve {$pair[1]}";
            }
        }

        return $issues;
    }

    private function checkQuality(string $output): array
    {
        $issues = [];

        // Uzunluk kontrolü
        if (strlen($output) < 50) {
            $issues[] = 'Çıktı çok kısa';
        }

        // Yapı kontrolü
        if (strpos($output, '##') === false && 
            strpos($output, '-') === false) {
            $issues[] = 'Yapı eksik';
        }

        return $issues;
    }

    private function calculateScore(string $output): float
    {
        $score = 1.0;

        $issues = $this->review($output);
        $score -= count($issues['issues']) * 0.1;

        return max(0.0, min(1.0, $score));
    }
}
```

## 5. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR | İhlal Sonucu |
|----------|----------|-----|-------------|
| Uydurma bilgi | Zero Hallucination | ADR-035 | Yanlış bilgi |
| Kaynaksız iddia | Kaynak gösterme | ADR-035 | Güvenilirlik düşüklüğü |
| Eski bilgi | Güncel bilgi | ADR-035 | Yanlış bilgi |
| Tehlikeli içerik | Güvenli içerik | ADR-035 | Güvenlik açığı |
| Hardcoded secrets | .env + vault | ADR-034 | Veri sızıntısı |
| SQL injection | Prepared statement | ADR-002 | Güvenlik açığı |
| innerHTML | DOMParser | ADR-001 | XSS açığı |
| No verification | Doğrulama zorunlu | ADR-035 | Yanlış bilgi |
| No source | Kaynak zorunlu | ADR-035 | Güvenilirlik düşüklüğü |
| No review | Red Team review | ADR-035 | Kalite düşüklüğü |

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Bilinmeyen teknoloji** | `VERIFICATION REQUIRED` | ADR-035 |
| **Çelişkili bilgi** | Kaynak önceliği | ADR-035 |
| **Eski bilgi** | Güncelleme + uyarı | ADR-035 |
| **Tehlikeli istek** | Reddetme + loglama | ADR-035 |
| **Hallüsinasyon yayılımı** | Sweep + düzeltme | ADR-035 |
| **Düşük kalite** | Retry + iyileştirme | ADR-035 |
| **Kaynak eksik** | Web araması | ADR-035 |
| **Tutarlısızlık** | Düzeltme + loglama | ADR-035 |
| **Güvenlik açığı** | Red Team review | ADR-035 |
| **Performans** | Cache + optimization | ADR-035 |
| **Monitoring** | Kalite metrikleri | ADR-035 |
| **Logging** | Audit trail | ADR-004 |
| **Training** | Sürekli iyileştirme | ADR-035 |
| **Fallback** | Alternatif çıktı | ADR-035 |
| **Emergency** | Acil durum protokolü | ADR-035 |

## 7. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | Zero Hallucination zorunlu | ADR-035 | Yanlış bilgi |
| 2 | Web doğrulama zorunlu | ADR-035 | Doğrulanamayan bilgi |
| 3 | Truth Mode zorunlu | ADR-035 | Yanlış bilgi |
| 4 | Red Team review zorunlu | ADR-035 | Kalite düşüklüğü |
| 5 | Kaynak gösterme zorunlu | ADR-035 | Güvenilirlik düşüklüğü |
| 6 | Logging zorunlu | ADR-004 | İzlenebilirlik kaybı |
| 7 | Monitoring zorunlu | ADR-035 | Kalite takibi |
| 8 | Compliance zorunlu | ADR-035 | Kural ihlali |
| 9 | Confidence score zorunlu | ADR-035 | Güven skoru eksikliği |
| 10 | Emergency protocol zorunlu | ADR-035 | Acil durum yönetimi |

## 8. İlgili ADR'ler

| ADR | İlişki | Açıklama |
|-----|--------|----------|
| [[ADR-035-system-prompt-engineering]] | Bu karar | Prompt engineering |
| [[ADR-030-ai-strategy-core]] | AI stratejisi | AI stratejisi |
| [[ADR-036-multi-project-prompt-maker]] | Prompt maker | Çoklu proje |
| [[ADR-005-ultrathink-protocol]] | Ultrathink | Düşünme protokolü |
| [[ADR-002-pdo-mandatory-no-orm]] | DB erişim | Veritabanı |
| [[ADR-001-vanilla-js-itcss]] | Frontend | UI teknolojisi |
| [[ADR-004-multi-domain-spa]] | SPA | Multi-domain |
| [[ADR-022-database-hardened-security]] | Güvenlik | DB sertleştirme |

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 Teknik | [[projects/NevaEngine/ai-models]] | AI modelleri |
| § 4 Teknik | [[architecture/06-audio/coremusic-ai-service]] | AI servisi |
| § 5 Yasak | [[ADR-030-ai-strategy-core]] | AI stratejisi |
| § 5 Yasak | [[ADR-005-ultrathink-protocol]] | Ultrathink |
| § 6 Edge | [[ADR-002-pdo-mandatory-no-orm]] | DB erişim |
| § 6 Edge | [[ADR-001-vanilla-js-itcss]] | Frontend |
| § 7 Guardrails | [[ADR-004-multi-domain-spa]] | SPA |
| § 7 Guardrails | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 8 İlgili | [[ADR-036-multi-project-prompt-maker]] | Prompt maker |
| § 8 İlgili | [[ADR-003-multi-db-9-databases]] | 9 DB |

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Zero Hallucination** | Uydurma bilgi üretme yasağı |
| **Truth Mode** | Gerçek mod — her zaman doğru |
| **Human Mode** | İnsana saygılı mod |
| **Red Team** | Adversarial review |
| **Hallüsinasyon** | Uydurma bilgi üretme |
| **VERIFICATION REQUIRED** | Doğrulanamayan bilgi etiketi |
| **Confidence Score** | Güven skoru |
| **Source Citation** | Kaynak gösterme |
| **Chain-of-thought** | Adım adım düşünme |
| **Few-shot** | Örnek ile yönlendirme |
| **System Prompt** | Sistem talimatı |
| **User Prompt** | Kullanıcı isteği |
| **Audit Trail** | İzlenebilirlik günlüğü |
| **Monitoring** | İzleme |
| **Training** | Eğitim |
| **Compliance** | Uyumluluk |
| **Adversarial** | Düşmancasına |
| **Consistency** | Tutarlılık |
| **Quality** | Kalite |
| **Fallback** | Alternatif |

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır Sayısı** | ≥500 |
| **Status** | FROZEN (değiştirilemez) |
| **ADR Uyumlu** | ✅ 001, 002, 004, 005, 022, 030, 034, 035, 036 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **Cross-Reference** | ✅ 10 referans |
| **Guardrails** | ✅ 10 kural |
| **Edge Cases** | ✅ 15 senaryo |
| **Yasak Örüntü** | ✅ 10 kural |
| **Terim Sayısı** | ✅ 20 terim |
| **Kod Örnekleri** | ✅ 3 örnek |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
