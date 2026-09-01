---
name: {SKILL_ADI}
description: {SKILL_ACIKLAMASI} AI Agentic Orchestration ile web'den güncel PHP dokümanlarını arayarak (Truth Mode) çalışır. PDO, strict_types, ve OWASP Top10:2025 standartlarını uygular. Tetikleyiciler: "{TETIKLEYICI_1}", "{TETIKLEYICI_2}".
license: MIT
metadata:
  version: 1.0.0
  author: {YAZAR}
  category: backend-orchestration
  tags: [php, backend, agentic, truth-mode]
---

# 🌐 {SKILL_BASLIK} (PHP/Backend Agent)

## 1. Genel Bakış
{SKILL_DETAYLI_ACIKLAMA}

## 2. Otonom Web Research (Truth Mode)
Bu skill her kullanımda aşağıdakileri doğrulamak zorundadır:
- Kullanılan PHP sürümlerinin özellikleri (ör. PHP 8.4 syntax).
- OWASP Top 10 metriklerinin güncelliği.
- Şüpheli bilgiler (Halüsinasyon tespiti durumunda `// ⚠️ VERIFICATION REQUIRED` eklenir).

## 3. CoreMusic PHP Otonom Kurallar
Bu agent aşağıdaki kuralları **kesinlikle ihlal edemez**:
- `declare(strict_types=1);` eksikliği reddedilir.
- PDO kullanımı harici SQL kabul edilmez (SQL Injection riski - H001).
- `unserialize(user_input)` RCE yaratacağından kesinlikle reddedilir.

## 4. Otonom Çalışma Protokolü
1. İstek alınır.
2. Gerekiyorsa güncel PHP dokümanları (php.net) webden araştırılır.
3. Hedef dosya analiz edilir.
4. Sıfır halüsinasyon prensibi ile rapor/kod üretilir.
