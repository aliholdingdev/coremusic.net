---
name: {SKILL_ADI}
description: {SKILL_ACIKLAMASI} AI Agentic Orchestration ile "Zero Hallucination" ilkelerini korur, gerektiğinde webden araştırma yapar. Tetikleyiciler: "{TETIKLEYICI_1}", "{TETIKLEYICI_2}".
license: MIT
metadata:
  version: 1.0.0
  author: {YAZAR}
  category: agentic-orchestration
  tags: [{TAG_1}, {TAG_2}, truth-mode]
---

# 🌐 {SKILL_BASLIK}

## 1. Genel Bakış
{SKILL_DETAYLI_ACIKLAMA}

## 2. Zero Hallucination & Truth Mode
- **Web Research:** Bilinmeyen veya güncel olmayan tüm teknolojiler/API'ler mutlaka webden (resmi kaynaklardan) doğrulanmalıdır. Tahmin yapılamaz.
- **Şüpheli Kod:** Teyit edilemeyen bilgiler için kodu durdur veya `// ⚠️ VERIFICATION REQUIRED` etiketi koy.
- **Kritik Red (H001):** Geçerliliğini yitirmiş (deprecated) veya projeyle uyumsuz tüm bilgiler reddedilir.

## 3. Otonom Çalışma Protokolü
1. **Analiz:** Kullanıcı talebi incelenir.
2. **Doğrulama (Truth Mode):** İhtiyaç halinde web araştırması yapılır, bilgiler 2-3 kez çapraz teyit edilir.
3. **Execution:** {ADIM_1}
4. **Execution:** {ADIM_2}
5. **Raporlama:** Sıfır halüsinasyon garantisi ile kullanıcıya çıktı sunulur.

## 4. Güvenlik Kısıtlamaları
- Hiçbir API anahtarı veya şifre (secret) hardcode edilemez.
- Kullanıcının doğrudan onayı olmadan yıkıcı (silme, deploy) komutlar otonom çalıştırılamaz.
