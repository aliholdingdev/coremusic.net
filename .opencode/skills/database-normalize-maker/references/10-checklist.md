# 10. QA & Orchestrator Final Checklist

## Nihai QA ve Orkestratör Kontrol Listesi

Orkestratör SQL çıktısını üretip dosyaya yazmadan hemen önce, tüm sanal ajanların (Data, Security, Backend) kurallarının karşılandığından emin olmak için aşağıdaki denetim listesini (Checklist) çalıştırır.

Eğer bu listedeki herhangi bir madde başarısız olursa, çıktı **kullanıcıya verilmez** ve yeniden araştırma/tasarım döngüsüne girilir.

### ✅ Data Engineer (Performans & Normalizasyon)
- [ ] Tablolar **BCNF** standartlarına uyumlu mu?
- [ ] Gerekçesi olmayan virgülle ayrılmış değer (1NF ihlali) veya tekrar eden kolonlar var mı?
- [ ] Tüm Yabancı Anahtarlara (Foreign Keys) uygun **indeksleme** yapıldı mı?
- [ ] Büyük veriler için JSONB/JSON Virtual Index stratejisi doğru kurgulandı mı?

### ✅ Security Engineer (Güvenlik)
- [ ] PII, Finans veya Sağlık verisi içeren kolonlar açıkça tespit edilip şifreleme uyarısı/metodu eklendi mi?
- [ ] Kritik tablolar (Kullanıcılar, Ödemeler) için `_audit` (Denetim) tabloları tasarlandı mı?
- [ ] Döngüsel yabancı anahtar (Cyclic FK) bağımlılıkları engellendi mi?

### ✅ Backend Architect (Entegrasyon)
- [ ] Tablo ve kolon isimlendirmeleri (snake_case, plural/singular) CoreMusic standartlarına uygun mu?
- [ ] `created_at` ve `updated_at` (zaman damgaları) tüm ana varlık tablolarında mevcut mu?
- [ ] Birincil anahtarlar (Primary Keys) ID için `BIGINT UNSIGNED` veya `UUID` standartlarında mı?

### ✅ Zero-Hallucination & Web Search
- [ ] Kullanılan veri tipleri (Örn: `JSON`, `TIMESTAMPTZ`, `VARCHAR`) hedeflenen motorun **resmi dökümanlarıyla doğrulandı mı?**
- [ ] Projenin sektörüyle ilgili **zorunlu web araması** (Mandatory Web Search) yapıldı mı ve sonuçları tasarıma dahil edildi mi?

> **Orkestratör Notu:** Bu kontrol listesindeki maddeler kullanıcıya son rapor (Technical Report veya Output Console) eşliğinde başarı durumuyla birlikte yazdırılmalıdır.
