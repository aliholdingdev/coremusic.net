# Claude Skills Oluşturma - Önemli Kurallar ve Hatalardan Kaçınma Rehberi

Bu doküman, Claude için özel beceriler (Skills) oluştururken dikkat edilmesi gereken önemli kuralları ve yaşanan hatalardan alınan dersleri içerir.

## 📋 İçindekiler
1. [YAML Frontmatter Kuralları](#yaml-frontmatter-kuralları)
2. [Beceri Adlandırma Kuralları](#beceri-adlandırma-kuralları)
3. [ZIP Paketleme Kuralları](#zip-paketleme-kuralları)
4. [Dosya Yapısı ve Organizasyon](#dosya-yapısı-ve-organizasyon)
5. [En İyi Uygulamalar](#en-iyi-uygulamalar)
6. [Yaygın Hatalar ve Çözümleri](#yaygın-hatalar-ve-çözümleri)

---

## YAML Frontmatter Kuralları

### ✅ İzin Verilen Zorunlu Alanlar

Her Skill.md dosyası YAML frontmatter ile başlamalıdır. İzin verilen **ana alanlar**:

```yaml
---
name: beceri-adi
description: Becerinin ne yaptığının ve ne zaman kullanılacağının açıklaması
license: MIT (opsiyonel)
allowed-tools: [] (opsiyonel)
metadata:
  version: 1.0.0
  dependencies: python>=3.8, pandas>=1.5.0
  custom_field: herhangi bir değer
---
```

### ❌ Yaygın Hata #1: version ve dependencies doğrudan kullanımı

**YANLIŞ:**
```yaml
---
name: beceri-adi
description: Açıklama
version: 1.0.0
dependencies: python>=3.8
---
```

**Hata Mesajı:**
```
unexpected key in SKILL.md frontmatter: properties must be in 
('name', 'description', 'license', 'allowed-tools', 'metadata')
```

**DOĞRU:**
```yaml
---
name: beceri-adi
description: Açıklama
metadata:
  version: 1.0.0
  dependencies: python>=3.8
---
```

### 📝 Ana Alan Açıklamaları

| Alan | Zorunlu | Açıklama | Örnek |
|------|---------|----------|-------|
| `name` | ✅ Evet | Beceri adı (sadece küçük harf, sayı, tire) | `veri-analiz-uzmani` |
| `description` | ✅ Evet | Ne işe yarar, ne zaman kullanılır (max 200 karakter) | `Excel dosyalarını analiz eder...` |
| `license` | ❌ Hayır | Lisans türü | `MIT`, `Apache-2.0` |
| `allowed-tools` | ❌ Hayır | İzin verilen araçlar listesi | `["bash", "python"]` |
| `metadata` | ❌ Hayır | Tüm ek bilgiler buraya | Aşağıya bakın |

### 📦 Metadata Alanı Kullanımı

`metadata` alanının altına istediğiniz her türlü ek bilgiyi ekleyebilirsiniz:

```yaml
metadata:
  version: 1.0.0
  author: Harun Seyhan
  dependencies: python>=3.8, pandas>=1.5.0, numpy>=1.20.0
  last_updated: 2025-10-29
  category: data-analysis
  tags: [excel, analytics, visualization]
  custom_info: Her türlü özel bilgi
```

---

## Beceri Adlandırma Kuralları

### ❌ Yaygın Hata #2: Geçersiz beceri adı

**YANLIŞ:**
```yaml
name: Veri Analiz Uzmanı
```

**Hata Mesajı:**
```
Skill name in SKILL.md can only contain lowercase letters, numbers, 
and hyphens. Try 'veri-analiz-uzmanı'.
```

**DOĞRU:**
```yaml
name: veri-analiz-uzmani
```

### 📏 Adlandırma Kuralları

| Kural | Durum | Örnekler |
|-------|-------|----------|
| Küçük harf (lowercase) | ✅ Zorunlu | `analiz`, `rapor`, `dashboard` |
| Sayılar | ✅ İzin verilir | `analiz-v2`, `report-2024` |
| Tire (-) | ✅ İzin verilir | `veri-analiz`, `excel-rapor` |
| Büyük harf | ❌ YASAK | ~~`Veri`~~, ~~`Analiz`~~ |
| Boşluk | ❌ YASAK | ~~`veri analiz`~~ |
| Alt çizgi (_) | ❌ YASAK | ~~`veri_analiz`~~ |
| Türkçe karakter (ı,ş,ğ,ü,ö,ç) | ❌ YASAK | ~~`uzmanı`~~, ~~`görselleştirme`~~ |
| Özel karakterler | ❌ YASAK | ~~`@`, `#`, `!`, `.`~~ |

### 💡 İyi Adlandırma Örnekleri

```yaml
✅ veri-analiz-uzmani
✅ excel-rapor-olusturucu
✅ finans-dashboard-v2
✅ musteri-segmentasyon-analiz
✅ satislar-trend-analizi
✅ marka-yonergeleri
✅ pdf-form-doldurma
✅ dokuman-analiz-asistani
```

### ❌ Kötü Adlandırma Örnekleri

```yaml
❌ Veri Analiz Uzmanı          → büyük harf ve boşluk
❌ veri_analiz_uzmanı          → alt çizgi
❌ VeriAnalizUzmanı            → büyük harf
❌ veri.analiz.uzmani          → nokta
❌ veri-analiz-uzmanı          → Türkçe karakter (ı)
❌ Excel Rapor!                → büyük harf, boşluk, özel karakter
❌ FinansRaporu                → büyük harf
```

### 🔄 Türkçe Karakter Dönüşüm Tablosu

| Türkçe | İngilizce Karşılığı |
|--------|---------------------|
| ı | i |
| ş | s |
| ğ | g |
| ü | u |
| ö | o |
| ç | c |
| İ | i |
| Ş | s |
| Ğ | g |
| Ü | u |
| Ö | o |
| Ç | c |

**Örnek Dönüşümler:**
- "Görselleştirme Uzmanı" → `gorsellestirme-uzmani`
- "Müşteri Analizi" → `musteri-analizi`
- "Ürün Raporlama" → `urun-raporlama`
- "İçerik Oluşturucu" → `icerik-olusturucu`

---

## ZIP Paketleme Kuralları

### ❌ Yaygın Hata #3: İç içe ZIP veya yanlış yapı

**Hata Mesajı:**
```
Zip cannot contain nested zip files
```

### ✅ Doğru ZIP Yapısı

```
veri-analiz-uzmani.zip
└── veri-analiz-uzmani/
    ├── Skill.md
    ├── README.md (opsiyonel)
    ├── resources/ (opsiyonel)
    │   ├── logo.png
    │   └── template.xlsx
    └── scripts/ (opsiyonel)
        ├── analyze.py
        └── visualize.py
```

### ❌ Yanlış ZIP Yapıları

**Yanlış #1: Dosyalar doğrudan ZIP kökünde**
```
❌ veri-analiz-uzmani.zip
   ├── Skill.md
   ├── README.md
   └── resources/
```

**Yanlış #2: İç içe klasörler**
```
❌ veri-analiz-uzmani.zip
   └── veri-analiz-uzmani/
       └── veri-analiz-uzmani/
           └── Skill.md
```

**Yanlış #3: Yanlış isimde ana klasör**
```
❌ veri-analiz-uzmani.zip
   └── my-skill/
       └── Skill.md
```

### 🔧 Doğru Paketleme Komutları

**Linux/Mac:**
```bash
# Klasör içindeyken
cd veri-analiz-uzmani
zip -r ../veri-analiz-uzmani.zip .

# VEYA klasör dışındayken (ÖNERİLEN)
zip -r veri-analiz-uzmani.zip veri-analiz-uzmani/
```

**Kontrol:**
```bash
# ZIP içeriğini kontrol et
unzip -l veri-analiz-uzmani.zip

# Beklenen çıktı:
# veri-analiz-uzmani/
# veri-analiz-uzmani/Skill.md
# veri-analiz-uzmani/resources/...
```

### ⚠️ Paketlerken Dikkat Edilmesi Gerekenler

1. ✅ **Ana klasör adı, ZIP dosya adı ile aynı olmalı**
   - ZIP: `veri-analiz-uzmani.zip`
   - İçindeki klasör: `veri-analiz-uzmani/`

2. ❌ **ZIP içinde başka ZIP dosyası olmamalı**
   - Hiçbir alt klasörde `.zip` dosyası bulunmamalı

3. ❌ **Gereksiz dosyalar olmamalı**
   - `.DS_Store` (Mac)
   - `Thumbs.db` (Windows)
   - `.git/` klasörü
   - `node_modules/` klasörü
   - `__pycache__/` klasörü
   - `.env` dosyaları

4. ✅ **Temiz paketleme önerisi:**
```bash
# Gereksiz dosyaları hariç tut
zip -r veri-analiz-uzmani.zip veri-analiz-uzmani/ \
    -x "*.DS_Store" \
    -x "*__pycache__*" \
    -x "*.pyc" \
    -x ".git/*" \
    -x "node_modules/*"
```

---

## Dosya Yapısı ve Organizasyon

### 📁 Temel Dosya Yapısı

```
veri-analiz-uzmani/
├── Skill.md                 # Zorunlu - Ana beceri dosyası
├── README.md                # Opsiyonel - Kullanım kılavuzu
├── LICENSE                  # Opsiyonel - Lisans dosyası
├── resources/               # Opsiyonel - Kaynak dosyaları
│   ├── templates/
│   │   ├── rapor_sablonu.docx
│   │   └── sunum_sablonu.pptx
│   ├── images/
│   │   └── logo.png
│   └── data/
│       └── ornek_veri.xlsx
├── scripts/                 # Opsiyonel - Python/JS scriptleri
│   ├── __init__.py
│   ├── analyze.py
│   ├── visualize.py
│   └── utils.py
└── tests/                   # Opsiyonel - Test dosyaları
    └── test_analyze.py
```

### 📝 Skill.md Tam Örnek

```markdown
---
name: veri-analiz-uzmani
description: Excel dosyalarının kapsamlı veri analizini yapar; istatistiksel analiz, trend analizi, görselleştirme ve otomatik içgörüler sunar. Word, PowerPoint, Excel veya PDF formatında raporlar oluşturur.
metadata:
  version: 1.0.0
  author: Harun Seyhan
  dependencies: python>=3.8, pandas>=1.5.0, numpy>=1.20.0
  last_updated: 2025-10-29
  category: data-analysis
---

# Veri Analiz Uzmanı

## Genel Bakış

Bu beceri, Excel dosyalarını derinlemesine analiz ederek...

## Temel Özellikler

### 1. İstatistiksel Analiz
- Tanımlayıcı istatistikler
- Dağılım analizleri
...

## Kullanım Senaryoları

Claude bu beceriyi aşağıdaki durumlarda kullanmalıdır:
- Kullanıcı Excel dosyası yüklediğinde
- "Analiz et" gibi tetikleyici kelimeler kullanıldığında
...

## Kod Örnekleri

\```python
import pandas as pd
import numpy as np

# Veri analizi kodu
df = pd.read_excel('dosya.xlsx')
\```

## Kaynaklar

- resources/templates/ klasöründe şablonlar
- scripts/ klasöründe yardımcı scriptler
```

---

## En İyi Uygulamalar

### 1. 📊 Description (Açıklama) Alanı

**Çok önemli:** Claude bu açıklamayı kullanarak beceriyi ne zaman tetikleyeceğine karar verir.

✅ **İyi Açıklama:**
```yaml
description: Excel dosyalarının kapsamlı veri analizini yapar; istatistiksel analiz, trend analizi, görselleştirme ve otomatik içgörüler sunar. Word, PowerPoint, Excel veya PDF formatında raporlar oluşturur.
```

Neden iyi:
- Ne yaptığını açıkça belirtiyor
- Hangi dosya tiplerinde çalıştığını söylüyor
- Hangi çıktıları üretebileceğini belirtiyor
- 200 karakter limitine uygun

❌ **Kötü Açıklama:**
```yaml
description: Veri analizi yapar.
```

Neden kötü:
- Çok kısa ve belirsiz
- Hangi tip verilerle çalıştığı belli değil
- Ne zaman kullanılacağı net değil

### 2. 🎯 Tetikleme Koşulları

Skill.md içinde açıkça belirtin:

```markdown
## Ne Zaman Kullanılmalı?

Claude bu beceriyi şu durumlarda kullanmalıdır:

### Otomatik Tetikleme:
- Kullanıcı `.xlsx`, `.xls` veya `.csv` dosyası yüklediğinde
- Şu kelimelerden biri kullanıldığında:
  - "analiz", "analiz et", "incele"
  - "rapor hazırla", "rapor oluştur"
  - "trendleri göster", "grafik çiz"
  - "dashboard", "görselleştir"
  
### Kullanmama Durumları:
- Sadece veri gösterimi isteniyorsa
- Basit hesaplama gereken durumlarda
- Excel yerine başka araç daha uygunsa
```

### 3. 📚 Örnekler Ekleyin

Her zaman kullanım örnekleri ekleyin:

```markdown
## Örnekler

### Örnek 1: Satış Analizi

**Kullanıcı:** "Bu satış verilerini analiz et"

**Claude'un Yaklaşımı:**
1. Excel dosyasını yükle
2. Temel istatistikleri hesapla
3. Trendleri tespit et
4. Görselleştirmeler oluştur
5. Rapor hazırla

**Beklenen Çıktı:**
- Dashboard görseli (PNG)
- Detaylı analiz raporu (DOCX)
```

### 4. 🔒 Güvenlik ve Gizlilik

```markdown
## Güvenlik Notları

**Claude'un Dikkat Etmesi Gerekenler:**

1. Hassas veriler: Kişisel, finansal bilgiler varsa uyar
2. Veri gizliliği: Analiz sonuçlarını paylaşmadan önce sor
3. API anahtarları: Asla kod içine gömmeme
4. Dosya boyutu: Çok büyük dosyalar için uyarı ver
```

### 5. 📖 Dokümantasyon

```markdown
## Kullanılan Kütüphaneler

- **pandas (>=1.5.0)**: Veri manipülasyonu
- **numpy (>=1.20.0)**: Sayısal hesaplamalar
- **matplotlib (>=3.5.0)**: Görselleştirme
- **seaborn (>=0.12.0)**: İstatistiksel grafikler

## Sınırlamalar

- Maksimum 1 milyon satır desteklenir
- Çok büyük dosyalar için örnekleme yapılır
- Gerçek zamanlı veri akışı desteklenmez
```

---

## Yaygın Hatalar ve Çözümleri

### Hata 1: YAML Frontmatter

**Hata:**
```
unexpected key in SKILL.md frontmatter
```

**Çözüm:**
- `version`, `dependencies` ve diğer özel alanları `metadata` altına taşıyın

### Hata 2: Beceri Adı

**Hata:**
```
Skill name can only contain lowercase letters, numbers, and hyphens
```

**Çözüm:**
- Sadece küçük harf, sayı ve tire kullanın
- Türkçe karakterleri İngilizce karşılıklarıyla değiştirin
- Boşlukları tire ile değiştirin

### Hata 3: ZIP Yapısı

**Hata:**
```
Zip cannot contain nested zip files
```

**Çözüm:**
- ZIP içinde başka ZIP olmadığından emin olun
- Ana klasör adının ZIP adıyla eşleştiğini kontrol edin
- Temiz bir şekilde yeniden paketleyin

### Hata 4: Dosya Bulunamadı

**Hata:**
```
SKILL.md not found
```

**Çözüm:**
- Dosya adının tam olarak `Skill.md` (büyük S, küçük k) olduğundan emin olun
- ZIP içinde doğru konumda olduğunu kontrol edin

---

## 🧪 Test Checklist

Beceriyi yüklemeden önce:

- [ ] `name` sadece küçük harf, sayı ve tire içeriyor mu?
- [ ] `description` 200 karakterden kısa mı ve açıklayıcı mı?
- [ ] `version` ve `dependencies` `metadata` altında mı?
- [ ] Skill.md dosya adı doğru mu? (büyük S, küçük k)
- [ ] ZIP yapısı doğru mu? (ana klasör → Skill.md)
- [ ] ZIP içinde başka ZIP dosyası yok mu?
- [ ] Gereksiz dosyalar temizlendi mi? (.DS_Store, __pycache__, vb.)
- [ ] ZIP dosya adı ile içindeki klasör adı aynı mı?
- [ ] Tetikleme koşulları açıkça belirtilmiş mi?
- [ ] Örnek kullanımlar eklenmiş mi?

---

## 🚀 Hızlı Başlangıç Şablonu

```yaml
---
name: ornek-beceri-adi
description: Ne yapar, ne zaman kullanılır - açık ve net açıklama (max 200 karakter)
metadata:
  version: 1.0.0
  author: İsminiz
  dependencies: gerekli kütüphaneler
---

# Başlık

## Genel Bakış
Becerinin detaylı açıklaması...

## Temel Özellikler
- Özellik 1
- Özellik 2

## Ne Zaman Kullanılmalı?
- Tetikleme koşulu 1
- Tetikleme koşulu 2

## Kullanım Örnekleri
### Örnek 1
...

## Teknik Detaylar
...

## Önemli Notlar
...
```

---

## 📚 Ek Kaynaklar

- [Anthropic Skills GitHub Repo](https://github.com/anthropics/skills)
- [Claude Dokümantasyonu](https://docs.claude.com)
- Skill Oluşturma Resmi Kılavuzu

---

## 📝 Versiyon Geçmişi

| Versiyon | Tarih | Değişiklikler |
|----------|-------|---------------|
| 1.0.0 | 2025-10-29 | İlk versiyon - YAML, adlandırma ve ZIP hataları |

---

## 💡 Son Tavsiyeler

1. **Basit başlayın**: Karmaşık scriptler eklemeden önce temel Skill.md ile başlayın
2. **İteratif test edin**: Her değişiklikten sonra yükleyip test edin
3. **Dokümante edin**: Ne yaptığınızı açıkça yazın
4. **Örnekler ekleyin**: Kullanım örnekleri Claude'un doğru çalışmasını sağlar
5. **Sürüm takibi yapın**: Her değişikliği version numarasında belirtin

**Başarılar! 🎉**
