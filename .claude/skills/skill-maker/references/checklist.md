# 🌐 skill-maker — Agentic Production-Grade Kalite Kontrol Listesi

## 1. Web Research & Zero Hallucination Kontrolü
```text
[ ] Yeni skill için kullanılan teknolojiler webden minimum 2-3 kaynaktan araştırıldı mı?
[ ] Öğrenilen bilgiler "Verified (Score 90-100)" durumunda mı?
[ ] Tam emin olunamayan satırlara `// ⚠️ VERIFICATION REQUIRED` etiketi eklendi mi?
[ ] H001 Kritik Reddi (Deprecated / Uyumsuz teknoloji) kuralına takılan bir yapı var mı?
```

## 2. SKILL.md Otonom Orkestrasyon Kontrolü
```text
[ ] Dosya adı kesinlikle SKILL.md (büyük harflerle) mi?
[ ] name alanı sadece lowercase-hyphen formatında mı? (Max 64 karakter)
[ ] description alanı otonom tetiklemeye uygun, açık ve net mi? (Max 1024 karakter)
[ ] Kiro YAML frontmatter kurallarına (metadata vb.) %100 uyuldu mu?
[ ] Dosya 2000 satır kısıtını aşıyor mu? (Aşmamalıdır)
```

## 3. Klasör & Mimari Kontrol
```text
[ ] .claude/skills/{skill-adi}/ klasörü doğru oluşturuldu mu?
[ ] references/ klasörüne detaylı kurallar aktarıldı mı? (Progressive Disclosure)
[ ] Agent'ın kullanacağı templates/ veya scripts/ mevcutsa, güvenli mi?
```

## 4. CoreMusic Kuralları (Domain Enjeksiyonu)
```text
[ ] PHP Domain: strict_types=1, PDO kullanımı, xss/csrf korumaları enjekte edildi mi?
[ ] JS/Frontend Domain: TypeScript/no-any, DOM-safe rendering, async/await zorunluluğu var mı?
[ ] Security: OWASP Top10:2025 standartlarına uyumlu çıktı üretmesi sağlandı mı?
```

## 5. Security & Isolation Kontrol
```text
[ ] Üretilen skill içerisinde sabit kodlanmış (hardcoded) API Key, token var mı? (Olmamalıdır)
[ ] Otonom yıkıcı komutlar (silme, deploy vb.) "Kullanıcı Onayı" prosedürüne bağlandı mı?
```

## 6. Otonom Sonlandırma
```text
[ ] Kullanıcıya "Zero Hallucination" güvencesiyle rapor verildi mi?
```
