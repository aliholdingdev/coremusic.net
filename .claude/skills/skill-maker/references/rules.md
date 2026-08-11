# 🌐 skill-maker — Agentic Rules & Kiro Formatı

## 1. Zero Hallucination & Truth Mode Kuralları
- **Web Research Zorunluluğu:** Skill içerisine yazılacak hiçbir teknoloji, kütüphane veya config tahmini (halüsinasyon) olamaz. MDN, OWASP, W3C gibi otoriter kaynaklardan doğrulanmalıdır.
- **Doğrulanmamış Bilgi İşareti:** Araştırmaya rağmen %100 emin olunamayan durumlarda kod satırının üstüne `// ⚠️ VERIFICATION REQUIRED` eklenmelidir.
- **Kritik Reddi (H001):** Mimariye ters düşen, eski veya güvensiz yapılar reddedilir ve kullanıcı uyarılır.

## 2. YAML Frontmatter Formatı (Zorunlu)
Oluşturulan her `SKILL.md` dosyasının başında bu yapı KESİNLİKLE bulunmalıdır:

```yaml
---
name: skill-adi              # Sadece küçük harf, tire (-). Örn: php-security-analyzer
description: açıklama        # Otonom tetikleme için net tanım. Tetikleyici kelimeleri içerir. (Max 1024 char)
license: MIT
metadata:
  version: 1.0.0
  author: Bayram Ali (ULTRATHINK Engineering)
  compatibility: Kiro IDE
  category: agentic-orchestration
  tags: [tag1, tag2]
---
```

## 3. Dosya ve İsimlendirme Kuralları
- **Dosya Adı:** Mutlaka `SKILL.md` (Büyük harflerle, `.md` küçük). `skill.md` YASAKTIR.
- **Klasör Adı = Skill Name:** YAML `name` alanı ile klasör adı 1:1 aynı olmalıdır.
- **İzin Verilmeyen Karakterler:** Boşluk, alt çizgi (`_`), Türkçe karakterler (ı, ş, ğ, ü, ö, ç), büyük harfler.

## 4. Otonom Güvenlik ve Kısıtlamalar (Security Enforcements)
- API anahtarı, token veya herhangi bir secret `SKILL.md` içine hardcode EDİLEMEZ.
- Zararlı olabilecek terminal komutları (`rm -rf`, vb.) için "Kullanıcı Onayı" prosedürü zorunludur.
- Skill çıktısı her zaman Kiro IDE ve CoreMusic kuralları ile tam uyumlu (SOLID, OWASP Top10:2025) olmak zorundadır.
