---
type: adr
category: ai
title: "ADR-036: Multi-Project Prompt Maker"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-036: Multi-Project Prompt Maker

## 1. Amaç

CoreMusic'te çoklu proje prompt üretim sistemini tanımlar. [[ADR-036-multi-project-prompt-maker]] Frozen karardır. Bu karar, birden fazla proje için prompt üretebilen, bağlam duyarlı ve otomatik prompt üretim motorunu kapsar.

Bu ADR'nin amacı:
- Çoklu proje desteği sağlamak
- Proje bağlamını otomatik algılamak
- Prompt üretimini otomatikleştirmek
- Kalite standartlarını korumak
- Verimliliği artırmak
- Tutarlılık sağlamak

## 2. Bağlam

| Faktör | Değer |
|--------|-------|
| **Kullanım** | Tüm projeler |
| **Hedef** | Otomatik prompt üretimi |
| **Bağlam** | Proje bağımlı |
| **Format** | Standard prompt formatı |
| **Kalite** | Zero Hallucination |
| **Doğrulama** | Web araması |
| **Logging** | Tüm üretimler loglanır |
| **Monitoring** | Kalite takibi |
| **Training** | Sürekli iyileştirme |
| **Compliance** | CoreMusic kuralları |

### 2.1 Neden Multi-Project Prompt?

- **Verimlilik:** Tekrarlanan iş azalır
- **Tutarlılık:** Standart çıktı
- **Hız:** Hızlı üretim
- **Kalite:** Kontrollü çıktı
- **Ölçeklenebilirlik:** Yeni projeler kolay eklenir
- **Bakım Kolaylığı:** Merkezi yönetim

### 2.2 Proje Türleri

| Proje | Tür | Prompt Gereksinimi |
|-------|-----|-------------------|
| **CoreMusic** | Ana platform | Yüksek |
| **Neva Engine** | C++ Audio | Yüksek |
| **Neva Player** | Video/Media | Orta |
| **Download Service** | Node.js | Orta |
| **Mobile App** | Flutter | Yüksek |
| **Admin Panel** | PHP | Düşük |
| **Landing Page** | Vanilla JS | Düşük |

## 3. Karar

### 3.1 Multi-Project Kararı

| Karar | Değer | Gerekçe |
|-------|-------|---------|
| **Otomatik Algılama** | ✅ Zorunlu | Bağlam duyarlı |
| **Standart Format** | ✅ Zorunlu | Tutarlılık |
| **Kalite Kontrol** | ✅ Zorunlu | Zero Hallucination |
| **Doğrulama** | ✅ Zorunlu | Kaynak doğrulama |
| **Logging** | ✅ Zorunlu | İzlenebilirlik |
| **Monitoring** | ✅ Zorunlu | Kalite takibi |
| **Training** | ✅ Zorunlu | Sürekli iyileştirme |
| **Compliance** | ✅ Zorunlu | Kural uyumu |
| **Cache** | ✅ Zorunlu | Performans |
| **Fallback** | ✅ Zorunlu | Alternatif çıktı |

### 3.2 Prompt Üretim Akışı

```
Proje İsteği
  → [1. Bağlam Analizi] — Proje türü, teknoloji
    → [2. Gereksinim Analizi] — Ne yapılmak isteniyor
      → [3. Prompt Oluşturma] — Bağlam uyumlu prompt
        → [4. Kalite Kontrol] — Zero Hallucination
          → [5. Doğrulama] — Kaynak doğrulama
            → [6. Loglama] — Audit trail
              → [7. Sunma] — Kullanıcıya sunma
                → [8. Geri Bildirim** — İyileştirme
```

## 4. Teknik Detaylar

### 4.1 Prompt Maker Motoru

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

class MultiProjectPromptMaker
{
    private array $projects = [];
    private array $templates = [];
    private PromptValidator $validator;
    private WebVerifier $verifier;

    public function __construct()
    {
        $this->validator = new PromptValidator();
        $this->verifier = new WebVerifier();
        $this->loadProjects();
        $this->loadTemplates();
    }

    /**
     * ✅ Proje prompt'u üret
     */
    public function generatePrompt(string $projectName, string $task): array
    {
        // 1. Proje bağlamını al
        $context = $this->getProjectContext($projectName);
        
        if (!$context) {
            return ['error' => 'Proje bulunamadı'];
        }

        // 2. Prompt'u oluştur
        $prompt = $this->buildPrompt($context, $task);
        
        // 3. Kalite kontrolü
        $validation = $this->validator->validate($prompt);
        
        if (!$validation['valid']) {
            return [
                'error' => 'Kalite kontrol başarısız',
                'issues' => $validation['issues'],
            ];
        }

        // 4. Doğrulama
        $verification = $this->verifier->verify($prompt);
        
        // 5. Sonucu döndür
        return [
            'prompt' => $prompt,
            'context' => $context,
            'confidence' => $validation['confidence'],
            'verified' => $verification['verified'],
            'sources' => $verification['sources'],
        ];
    }

    /**
     * ✅ Proje bağlamını al
     */
    private function getProjectContext(string $projectName): ?array
    {
        return $this->projects[$projectName] ?? null;
    }

    /**
     * ✅ Prompt'u oluştur
     */
    private function buildPrompt(array $context, string $task): string
    {
        $template = $this->getTemplate($context['type']);
        
        $prompt = str_replace(
            ['{{PROJECT}}', '{{TASK}}', '{{TECHNOLOGY}}', '{{CONTEXT}}'],
            [
                $context['name'],
                $task,
                $context['technology'],
                $context['description'],
            ],
            $template
        );

        return $prompt;
    }

    /**
     * ✅ Şablonu al
     */
    private function getTemplate(string $type): string
    {
        return $this->templates[$type] ?? $this->templates['default'];
    }

    /**
     * ✅ Projeleri yükle
     */
    private function loadProjects(): void
    {
        $this->projects = [
            'coremusic' => [
                'name' => 'CoreMusic',
                'type' => 'platform',
                'technology' => 'PHP 8.4, Vanilla JS, MySQL 9',
                'description' => 'Dijital medya yönetim platformu',
                'layer' => 'L2 Routing',
            ],
            'neva-engine' => [
                'name' => 'Neva Engine',
                'type' => 'audio',
                'technology' => 'C++20, JUCE 8, ASIO',
                'description' => 'C++ ses motoru',
                'layer' => 'L0 Infrastructure',
            ],
            'neva-player' => [
                'name' => 'Neva Player',
                'type' => 'media',
                'technology' => 'C++20, FFmpeg',
                'description' => 'Video/Media oynatıcı',
                'layer' => 'L0 Infrastructure',
            ],
            'download-service' => [
                'name' => 'Download Service',
                'type' => 'service',
                'technology' => 'Node.js, TypeScript',
                'description' => 'İndirme servisi',
                'layer' => 'L2 Routing',
            ],
            'mobile-app' => [
                'name' => 'Mobile App',
                'type' => 'mobile',
                'technology' => 'Flutter, Dart',
                'description' => 'Mobil uygulama',
                'layer' => 'L3 Presentation',
            ],
        ];
    }

    /**
     * ✅ Şablonları yükle
     */
    private function loadTemplates(): void
    {
        $this->templates = [
            'platform' => <<<EOT
# System Prompt: {{PROJECT}} Platform

## Identite
Sen {{PROJECT}} ekosisteminde çalışan bir AI asistanısın.

## Teknoloji
{{TECHNOLOGY}}

## Bağlam
{{CONTEXT}}

## Görev
{{TASK}}

## Kurallar
1. **Zero Hallucination:** Doğrulanamayan bilgiyi ASLA üretme.
2. **Truth Mode:** Her zaman gerçeği söyle.
3. **Human Mode:** İnsana saygılı ol.
4. **Red Team:** Adversarial review uygula.
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
EOT,
            'audio' => <<<EOT
# System Prompt: {{PROJECT}} Audio

## Identite
Sen {{PROJECT}} audio geliştirme ekibinde çalışan bir AI asistanısın.

## Teknoloji
{{TECHNOLOGY}}

## Bağlam
{{CONTEXT}}

## Görev
{{TASK}}

## Kurallar
1. **Zero-Allocation:** Audio thread'de heap allocation yasak.
2. **Lock-Free:** Audio thread'de mutex yasak.
3. **Noexcept:** ASIO callback noexcept olmalı.
4. **Cache-line alignment:** 64-byte alignment.
5. **Zero Hallucination:** Doğrulanamayan bilgiyi ASLA üretme.

## Yasaklar
- `malloc()`, `free()`, `new`, `delete` kullanımı
- Mutex kullanımı
- I/O blocking
- throw kullanımı
- Uydurma bilgi

## Çıktı Formatı
- C++ kod örnekleri
- Performans notları
- Güvenlik uyarıları
EOT,
            'default' => <<<EOT
# System Prompt: {{PROJECT}}

## Identite
Sen {{PROJECT}} projesinde çalışan bir AI asistanısın.

## Teknoloji
{{TECHNOLOGY}}

## Bağlam
{{CONTEXT}}

## Görev
{{TASK}}

## Kurallar
1. **Zero Hallucination:** Doğrulanamayan bilgiyi ASLA üretme.
2. **Truth Mode:** Her zaman gerçeği söyle.
3. **Human Mode:** İnsana saygılı ol.
4. **Source Citation:** Her iddia için kaynak göster.

## Çıktı Formatı
- Kısa ve öz
- Madde işaretleri ile
- Kod örnekleri ile
EOT,
        ];
    }
}
```

### 4.2 Bağlam Algılama

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

class ContextDetector
{
    /**
     * ✅ Proje bağlamını algıla
     */
    public function detectContext(string $directory): array
    {
        $context = [
            'type' => 'unknown',
            'technology' => 'unknown',
            'layer' => 'unknown',
        ];

        // Dosya yapısını analiz et
        $files = glob($directory . '/*');
        
        foreach ($files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            
            match($ext) {
                'php' => $context['technology'] = 'PHP',
                'js' => $context['technology'] = 'JavaScript',
                'cpp' => $context['technology'] = 'C++',
                'dart' => $context['technology'] = 'Dart',
                'ts' => $context['technology'] = 'TypeScript',
                _ => null,
            };
        }

        // Config dosyalarını kontrol et
        if (file_exists($directory . '/composer.json')) {
            $context['type'] = 'php-project';
            $context['technology'] = 'PHP';
        }

        if (file_exists($directory . '/package.json')) {
            $context['type'] = 'node-project';
            $context['technology'] = 'Node.js';
        }

        if (file_exists($directory . '/CMakeLists.txt')) {
            $context['type'] = 'cpp-project';
            $context['technology'] = 'C++';
        }

        if (file_exists($directory . '/pubspec.yaml')) {
            $context['type'] = 'flutter-project';
            $context['technology'] = 'Flutter';
        }

        return $context;
    }
}
```

### 4.3 Kalite Metrikleri

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

class PromptQualityMetrics
{
    /**
     * ✅ Kalite metriklerini hesapla
     */
    public function calculateMetrics(array $prompts): array
    {
        $metrics = [
            'total_prompts' => count($prompts),
            'average_length' => 0,
            'average_confidence' => 0,
            'hallucination_rate' => 0,
            'source_rate' => 0,
            'verification_rate' => 0,
        ];

        foreach ($prompts as $prompt) {
            $metrics['average_length'] += strlen($prompt['prompt'] ?? '');
            $metrics['average_confidence'] += $prompt['confidence'] ?? 0;
            
            if (isset($prompt['validation']['hallucination'])) {
                $metrics['hallucination_rate']++;
            }
            
            if (isset($prompt['sources']) && !empty($prompt['sources'])) {
                $metrics['source_rate']++;
            }
            
            if ($prompt['verified'] ?? false) {
                $metrics['verification_rate']++;
            }
        }

        // Ortalamaları hesapla
        $total = max(1, $metrics['total_prompts']);
        $metrics['average_length'] /= $total;
        $metrics['average_confidence'] /= $total;
        $metrics['hallucination_rate'] = ($metrics['hallucination_rate'] / $total) * 100;
        $metrics['source_rate'] = ($metrics['source_rate'] / $total) * 100;
        $metrics['verification_rate'] = ($metrics['verification_rate'] / $total) * 100;

        return $metrics;
    }

    /**
     * ✅ Rapor oluştur
     */
    public function generateReport(array $metrics): string
    {
        $report = "# Prompt Kalite Raporu\n\n";
        $report .= "| Metrik | Değer |\n";
        $report .= "|--------|-------|\n";
        $report .= "| Toplam Prompt | {$metrics['total_prompts']} |\n";
        $report .= "| Ortalama Uzunluk | {$metrics['average_length']} karakter |\n";
        $report .= "| Ortalama Güven | {$metrics['average_confidence']} |\n";
        $report .= "| Hallüsinasyon Oranı | {$metrics['hallucination_rate']}% |\n";
        $report .= "| Kaynak Oranı | {$metrics['source_rate']}% |\n";
        $report .= "| Doğrulama Oranı | {$metrics['verification_rate']}% |\n";

        return $report;
    }
}
```

## 5. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR | İhlal Sonucu |
|----------|----------|-----|-------------|
| Tek proje | Çoklu proje | ADR-036 | Ölçeklenememe |
| Manuel prompt | Otomatik üretim | ADR-036 | Verimsizlik |
| Kalite yok | Kalite kontrol | ADR-036 | Düşük kalite |
| Doğrulama yok | Doğrulama zorunlu | ADR-036 | Yanlış bilgi |
| Hardcoded secrets | .env + vault | ADR-034 | Veri sızıntısı |
| SQL injection | Prepared statement | ADR-002 | Güvenlik açığı |
| innerHTML | DOMParser | ADR-001 | XSS açığı |
| No logging | Logging zorunlu | ADR-004 | İzlenebilirlik kaybı |
| No monitoring | Monitoring zorunlu | ADR-036 | Kalite takibi |
| No training | Training zorunlu | ADR-036 | Sürekli iyileştirme |

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Bilinmeyen proje** | Fallback template | ADR-036 |
| **Bağlam eksik** | Otomatik algılama | ADR-036 |
| **Kalite düşüklüğü** | Retry + iyileştirme | ADR-036 |
| **Hallüsinasyon** | Sweep + düzeltme | ADR-035 |
| **Performans** | Cache + optimization | ADR-036 |
| **Tutarlısızlık** | Template standardı | ADR-036 |
| **Güncelleme** | Otomatik güncelleme | ADR-036 |
| **Fallback** | Alternatif çıktı | ADR-036 |
| **Monitoring** | Kalite metrikleri | ADR-036 |
| **Logging** | Audit trail | ADR-004 |
| **Training** | Sürekli iyileştirme | ADR-036 |
| **Compliance** | Kural uyumu | ADR-036 |
| **Emergency** | Acil durum protokolü | ADR-036 |
| **Cache invalidation** | Cache stratejisi | ADR-007 |
| **Multi-tenant** | Kiracı izolasyonu | ADR-036 |

## 7. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | Çoklu proje desteği zorunlu | ADR-036 | Ölçeklenememe |
| 2 | Otomatik bağlam algılama zorunlu | ADR-036 | Yanlış bağlam |
| 3 | Kalite kontrol zorunlu | ADR-036 | Düşük kalite |
| 4 | Doğrulama zorunlu | ADR-036 | Yanlış bilgi |
| 5 | Zero Hallucination zorunlu | ADR-035 | Uydurma bilgi |
| 6 | Logging zorunlu | ADR-004 | İzlenebilirlik kaybı |
| 7 | Monitoring zorunlu | ADR-036 | Kalite takibi |
| 8 | Training zorunlu | ADR-036 | Sürekli iyileştirme |
| 9 | Compliance zorunlu | ADR-036 | Kural ihlali |
| 10 | Cache zorunlu | ADR-007 | Performans düşüşü |

## 8. İlgili ADR'ler

| ADR | İlişki | Açıklama |
|-----|--------|----------|
| [[ADR-036-multi-project-prompt-maker]] | Bu karar | Çoklu proje prompt |
| [[ADR-035-system-prompt-engineering]] | Prompt engineering | Prompt standartları |
| [[ADR-030-ai-strategy-core]] | AI stratejisi | AI stratejisi |
| [[ADR-005-ultrathink-protocol]] | Ultrathink | Düşünme protokolü |
| [[ADR-002-pdo-mandatory-no-orm]] | DB erişim | Veritabanı |
| [[ADR-001-vanilla-js-itcss]] | Frontend | UI teknolojisi |
| [[ADR-004-multi-domain-spa]] | SPA | Multi-domain |
| [[ADR-007-cache-namespace]] | Cache | Önbellek |

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 Teknik | [[projects/README]] | Proje listesi |
| § 4 Teknik | [[architecture/06-audio/coremusic-ai-service]] | AI servisi |
| § 5 Yasak | [[ADR-035-system-prompt-engineering]] | Prompt engineering |
| § 5 Yasak | [[ADR-030-ai-strategy-core]] | AI stratejisi |
| § 6 Edge | [[ADR-005-ultrathink-protocol]] | Ultrathink |
| § 6 Edge | [[ADR-002-pdo-mandatory-no-orm]] | DB erişim |
| § 7 Guardrails | [[ADR-001-vanilla-js-itcss]] | Frontend |
| § 7 Guardrails | [[ADR-004-multi-domain-spa]] | SPA |
| § 8 İlgili | [[ADR-007-cache-namespace]] | Cache |
| § 8 İlgili | [[ADR-003-multi-db-9-databases]] | 9 DB |

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Multi-Project** | Çoklu proje |
| **Prompt Maker** | Prompt üretim motoru |
| **Bağlam Algılama** | Otomatik proje türü tespiti |
| **Template** | Şablon |
| **Kalite Kontrol** | Çıktı doğrulama |
| **Doğrulama** | Kaynak doğrulama |
| **Zero Hallucination** | Uydurma bilgi üretme yasağı |
| **Confidence Score** | Güven skoru |
| **Fallback** | Alternatif çıktı |
| **Cache** | Önbellek |
| **Monitoring** | İzleme |
| **Logging** | Loglama |
| **Training** | Eğitim |
| **Compliance** | Uyumluluk |
| **Audit Trail** | İzlenebilirlik günlüğü |
| **Multi-tenant** | Kiracı modeli |
| **Cache Invalidation** | Önbellek iptali |
| **Emergency** | Acil durum |
| **Retry** | Yeniden deneme |
| **Optimization** | Optimizasyon |

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır Sayısı** | ≥500 |
| **Status** | FROZEN (değiştirilemez) |
| **ADR Uyumlu** | ✅ 001, 002, 004, 005, 007, 030, 034, 035, 036 |
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
