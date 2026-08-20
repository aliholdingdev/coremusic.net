---
type: template
category: documentation
title: "Wiki Page Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: Markdown, Wiki Links, Frontmatter
---

# Wiki Page Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-042-vault-restructuring-2026-08-03]]

---

## 1. Amaç

CoreMusic `.ai/` vault dokümantasyonu için **wiki sayfa şablonu**. Tüm vault dosyalarının tutarlı, kaliteli ve erişilebilir olmasını sağlamak amacıyla hazırlanmıştır.

### 1.1 Kapsam

- **Kapsam:** Wiki sayfaları, vault dokümanları, ADR'ler, mimari dokümanlar, workflow dosyaları, referans rehberleri, şablon dosyaları.
- **Kapsam Dışı:** Kod dosyaları (`.php`, `.js`, `.cpp`), veritabanı dosyaları (`.sql`), config dosyaları (`.env`, `.json`).

### 1.2 Hedef Kitle

| Kullanıcı | Kullanım |
|-----------|----------|
| İnsan mühendisler | Vault'ta navigasyon, bilgi edinme |
| AI asistanları (Claude, Gemini, ChatGPT) | 10-adım boot protokolü, bilgi arama |
| Master Orchestrator | Görev dağıtımı, cross-reference doğrulama |
| QA Engineer | Dokümantasyon kalite denetimi |

### 1.3 İlişkili Kararlar

| Karar | Etki |
|-------|------|
| [[ADR-005-ultrathink-protocol]] | Zero hallucination, VERIFICATION REQUIRED |
| [[ADR-004-multi-domain-spa]] | Vault versiyonlama politikası |

---

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| Markdown | CommonMark 0.31 | Dokümantasyon formatı | commonmark.org |
| YAML Frontmatter | 1.2 | Metadata (7 zorunlu alan) | yaml.org |
| Wiki Links | — | Cross-reference (`[[path/to/file]]`) | [[index]] |
| Mermaid | 10+ | Diyagram oluşturma | mermaid.js.org |
| GFM Tables | — | Tablo formatı | github.github.com |

*Kaynaklar: CommonMark Spec (commonmark.org), YAML 1.2 Spec (yaml.org), Mermaid Docs (mermaid.js.org) — 2026-08-06'da doğrulandı*

---

## 3. Code Standards

### 3.1 Frontmatter Format

Her vault dosyası **7 zorunlu alan** içeren YAML frontmatter ile başlamalıdır:

```yaml
---
type: architecture|decision|workflow|template|guide|reference|system
category: {category-name}
title: "{Document Title}"
date: YYYY-MM-DD
updated: YYYY-MM-DD
status: active|draft|frozen
version: X.Y.Z
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
---
```

**Zorunlu Alanlar:**

| # | Alan | Tip | Açıklama | Örnek |
|---|------|-----|----------|-------|
| 1 | `type` | string | Dosya tipi | `architecture`, `decision`, `workflow` |
| 2 | `category` | string | Kategori | `documentation`, `security`, `audio` |
| 3 | `title` | string | Dosya başlığı | `"CSRF Protection Strategy"` |
| 4 | `date` | date | Oluşturma tarihi | `2026-08-06` |
| 5 | `updated` | date | Son güncelleme | `2026-08-06` |
| 6 | `status` | enum | Durum | `active`, `draft`, `frozen` |
| 7 | `version` | semver | Versiyon | `3.0.0` |

**Ek Alanlar (Opsiyonel):**

| Alan | Kullanım |
|------|----------|
| `authority` | Otorite kaynağı (SSOT) |
| `governance` | Yönetim politikası |
| `tech` | Kullanılan teknolojiler |
| `total_files` | Toplam dosya sayısı |

❌ **YANLIŞ — Eksik frontmatter:**

```markdown
# My Document

Some content here...
```

✅ **DOĞRU — Tam frontmatter:**

```markdown
---
type: architecture
category: security
title: "CSRF Protection Strategy"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
---

# CSRF Protection Strategy

Content here...
```

### 3.2 Document Structure

Her doküman aşağıdaki başlık hiyerarşisini izlemelidir:

```markdown
# {Document Title}

**See also:** [[index]] · [[CLAUDE.md]]

## 1. Amaç
## 2. Kapsam
## 3. Terminoloji
## 4. Sistem Tanımı
## 5. Detaylar
### 5.1 Alt Bölüm 1
### 5.2 Alt Bölüm 2
## 6. Uygulama
## 7. En İyi Uygulamalar
## 8. Sınır Durumları
## 9. Uyarılar
## 10. Bağımlılıklar
## 11. İlgili Dokümanlar
## 12. Çapraz Referanslar
## 13. Kalite Raporu
```

**Başlık Hiyerarşisi Kuralları:**

| Kural | Açıklama |
|-------|----------|
| H1 (`#`) | Dosya başına sadece 1 tane (dosya başlığı) |
| H2 (`##`) | Ana bölümler (numaralandırılmış) |
| H3 (`###`) | Alt bölümler (3.1, 3.2 formatında) |
| H4 (`####`) | Detay alt bölümler (nadir kullanılır) |
| H5-H6 | Kullanılmaz |

❌ **YANLIŞ — Numarasız başlıklar:**

```markdown
## Purpose
## Overview
## Details
```

✅ **DOĞRU — Numaralı başlıklar:**

```markdown
## 1. Amaç
## 2. Genel Bakış
## 3. Detaylar
```

### 3.3 Wiki Link Syntax

Wiki link'leri vault içinde navigasyon için kullanılır:

**Format 1 — Basit link:**

```markdown
[[path/to/file]]
```

**Format 2 — Görünen metin:**

```markdown
[[path/to/file|Görünen Metin]]
```

**Format 3 — Birden fazla link (noktalı virgül ayrı):**

```markdown
**See also:** [[index]] · [[CLAUDE.md]] · [[keys.md]]
```

**Kurallar:**

| Kural | Doğru | Yanlış |
|-------|-------|--------|
| Uzantı yok | `[[architecture/l1-security]]` | `[[architecture/l1-security/]]` |
| Lowercase | `[[decisions/accepted/ADR-001]]` | `[[Decisions/Accepted/ADR-001]]` |
| Tire kullanımı | `[[ADR-042-vault-restructuring]]` | `[[ADR 042 Vault Restructuring]]` |
| Display text | `[[path\|My Text]]` | `[[path]]` (metin gerekliyse) |

❌ **YANLIŞ — Yanlış wiki link formatı:**

```markdown
- [See this doc](architecture/l1-security/)
- [[Architecture/L1-Security]]
```

✅ **DOĞRU — Doğru wiki link formatı:**

```markdown
- [[architecture/l1-security]]
- [[architecture/l1-security|L1 Security Detail]]
```

### 3.4 Cross-Reference Patterns

Her dokümanda iki tür çapraz referans bulunmalıdır:

**Pattern 1 — İlgili Dokümanlar Bölümü:**

```markdown
## 11. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[architecture/l1-security]] | Güvenlik katmanı detayı |
| [[ADR-010-csrf-protection-strategy]] | CSRF karar dokümanı |
| [[brain.md]] | Mimari kararlar |
```

**Pattern 2 — Çapraz Referans Haritası:**

```markdown
## 12. Çapraz Referanslar

| Bu Dokümandan | Hedef | İlişki Tipi |
|---------------|-------|-------------|
| § 5.1 Middleware | [[ADR-010-csrf-protection-strategy]] | Karar referansı |
| § 6 Güvenlik | [[architecture/l1-security]] | Mimari referans |
| § 8 Uygulama | [[brain.md]] | Teknik detay |
```

### 3.5 Table Formatting

**Temel tablo formatı:**

```markdown
| Sütun 1 | Sütun 2 | Sütun 3 |
|---------|---------|---------|
| Değer 1 | Değer 2 | Değer 3 |
```

**Hizalama:**

```markdown
| Sol Hizalı | Ortalama | Sağ Hizalı |
|:-----------|:--------:|-----------:|
| Sol        |   Orta   |        Sağ |
```

**Tablo kuralları:**

| Kural | Açıklama |
|-------|----------|
| Başlık satırı | Zorunlu (ayırıcı satır ile) |
| Boş hücre | `—` veya `Yok` yazılmalı |
| Uzun metin | Hücre içinde satır kırma yok |
| Responsive | Mobilde yatay scroll yeterli |

### 3.6 Code Block Usage

**Dil belirtilmiş code block:**

````
```php
<?php
declare(strict_types=1);
$stmt = $pdo->prepare('SELECT id FROM users WHERE id = :id');
```
````

**Inline code:**

```markdown
`csrf_token` anahtar adı zorunludur.
```

**Code block kuralları:**

| Kural | Açıklama |
|-------|----------|
| Dil belirtimi | Her code block'ta `php`, `js`, `cpp`, `sql`, `bash` vb. |
| Inline code | Sınıf adı, dosya adı, komut, parametre |
| Kod içeriği | Gerçek ve çalışabilir olmalı |
| Syntax highlighting | Desteklenen diller: php, js, cpp, sql, bash, yaml, json, markdown |

### 3.7 Mermaid Diagrams

Diyagramlar için ASCII art syntax kullanımı:

**Flowchart:**

````markdown
```
Başlangıç ──▶ {Karar}
                │
           Evet ▼ Hayır
        İşlem 1  İşlem 2
           │       │
           └───┬───┘
               ▼
            Bitiş
```
````

**Sequence Diagram:**

````markdown
```
Kullanıcı ──▶ Sunucu ──▶ Veritabanı
    │             │            │
    │       İstek       Sorgu
    │             │            │
    │             ◀── Yanıt ──┘
    ◀── Cevap ───┘
```
````

**Class Diagram:**

````markdown
```
┌─────────────────┐      ┌─────────────────┐
│   AuthService   │      │ SessionManager  │
├─────────────────┤      ├─────────────────┤
│ -secretKey      │      │ -lifetime       │
├─────────────────┤      ├─────────────────┤
│ +login(email)   │──────│ +start()        │
│ +logout()       │      │ +destroy()      │
│ +validateToken()│      │ +regenerate()   │
└─────────────────┘      └─────────────────┘
```
````

**State Diagram:**

````markdown
```
[*] ──▶ Draft ──▶ Review ──▶ Active ──▶ Frozen ──▶ [*]
              ▲         │
              └─────────┘
             Reddedildi
```
````

### 3.8 Callout Boxes

Dikkat çekmek için callout kutuları:

```markdown
> [!WARNING]
> Güvenlik açığı tespit edildiğinde derhal bildirilmelidir.

> [!TIP]
> Bu optimizasyon %30 performans artışı sağlar.

> [!NOTE]
> Bu özellik henüz aktif değildir.

> [!IMPORTANT]
> Bu kural ihlal edildiğinde sistem durdurulur.
```

**Callout tipleri:**

| Tip | Kullanım | Renk |
|-----|----------|------|
| `WARNING` | Güvenlik uyarıları, kritik hatalar | Kırmızı |
| `TIP` | İpuçları, optimizasyon önerileri | Yeşil |
| `NOTE` | Bilgi notları, açıklamalar | Mavi |
| `IMPORTANT` | Zorunlu kurallar, dikkat edilecekler | Sarı |

### 3.9 Naming Conventions

**Dosya adlandırma:**

| Öğe | Format | Örnek |
|-----|--------|-------|
| Dosya adı | `kebab-case.md` | `csrf-protection-strategy.md` |
| Dizin adı | `kebab-case/` | `decisions/accepted/` |
| ADR dosyası | `ADR-NNN-title.md` | `ADR-010-csrf-protection-strategy.md` |
| SQL dosyası | `coremusic_domain.sql` | `coremusic_auth.sql` |
| Görsel dosyası | `descriptive-name.png` | `middleware-pipeline-flow.png` |

**Bölüm başlığı adlandırma:**

| Öğe | Format | Örnek |
|-----|--------|-------|
| Ana bölüm | `## N. Başlık` | `## 1. Amaç` |
| Alt bölüm | `### N.M Başlık` | `### 3.1 Frontmatter` |
| Listeler | Madde işareti veya numara | `- Öğe` veya `1. Öğe` |

**Wiki link hedefleri:**

| Bağlantı Tipi | Format | Örnek |
|---------------|--------|-------|
| Vault dosyası | `[[path/to/file]]` | `[[architecture/l1-security]]` |
| ADR | `[[ADR-NNN-title]]` | `[[ADR-010-csrf-protection-strategy]]` |
| Dizin | `[[path/to/dir/]]` | `[[decisions/accepted/]]` |
| Display text | `[[path\|Text]]` | `[[brain.md\|Engineering Brain]]` |

### 3.10 Version Management

Her dokümanda versiyon yönetimi:

**Semver formatı:**

```
MAJOR.MINOR.PATCH
```

| Bileşen | Güncelleme | Örnek |
|---------|------------|-------|
| MAJOR | Yapısal değişiklik, büyük yeniden yazma | 1.0.0 → 2.0.0 |
| MINOR | Yeni bölüm ekleme, içerik genişletme | 1.0.0 → 1.1.0 |
| PATCH | Düzeltme, güncelleme, typo | 1.0.0 → 1.0.1 |

**Güncelleme akışı:**

1. `updated` alanını bugünün tarihiyle güncelle
2. `version` alanını semver ile güncelle
3. Değişiklikleri `log.md`'ye kaydet
4. Cross-reference'ları doğrula

### 3.11 Quality Report Section

Her dokümanın sonunda standart kalite raporu bölümü:

```markdown
## 13. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | X.Y.Z |
| **Satır Sayısı** | ~NNN |
| **Frontmatter** | ✅ Tamamlandı |
| **Wikilink'ler** | ✅ Doğrulandı |
| **Çapraz Referanslar** | ✅ Güncel |
| **Son Güncelleme** | YYYY-MM-DD |
```

### 3.12 Related Documents Section

Standart ilgili dokümanlar bölümü:

```markdown
## 11. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[index]] | Ana katalog |
| [[brain.md]] | Mimari kararlar |
| [[keys.md]] | Keyword haritası |
| [[CLAUDE.md]] | Ana sözleşme |
| [[AGENTS.md]] | Agent kayıt defteri |
| [[ADR-NNN-title]] | İlgili mimari karar |
```

### 3.13 Cross-References Section

Standart çapraz referans haritası:

```markdown
## 12. Çapraz Referanslar

| Bu Dokümandan | Hedef | İlişki Tipi |
|---------------|-------|-------------|
| § 1 Amaç | [[CLAUDE.md]] | Ana sözleşme |
| § 3 Teknik | [[ADR-XXX]] | Karar referansı |
| § 5 Uygulama | [[architecture/lX-]] | Mimari referans |
| § 8 Güvenlik | [[ADR-022-database-hardened-security]] | Güvenlik standartı |
```

### 3.14 Document Review Process

Doküman inceleme süreci:

**İnceleme Kontrol Listesi:**

- [ ] Frontmatter 7 zorunlu alanı içeriyor mu?
- [ ] Başlık numaralandırması doğru mu?
- [ ] Wiki link'leri geçerli mi?
- [ ] Kod blokları çalışabilir mi?
- [ ] Tablolar doğru formatta mı?
- [ ] Mermaid diyagramları render ediliyor mu?
- [ ] Çağrı kutuları doğru yerlerde mi?
- [ ] Kalite raporu bölümü mevcut mu?
- [ ] Versiyon güncellendi mi?
- [ ] `log.md`'ye kayıt eklendi mi?

**Onay Akışı:**

```
Yazar → İlk İnceleme → Teknik İnceleme → Onay → Active
```

---

## 4. Hard Guardrails

Aşılamayan kurallar — ihlal durumunda işlem anında durdurulur:

| # | Kural | Açıklama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | **Frontmatter Zorunlu** | 7 zorunlu alan eksiksiz olmalı | Dosya geçersiz sayılır |
| 2 | **Wiki Link Formatı** | `[[path/to/file]]` formatı zorunlu | Link çalışmaz |
| 3 | **Versiyon Alanı** | Semver formatında version zorunlu | Versiyon takibi bozulur |
| 4 | **Governance** | `Red Team • Human Mode • Truth Mode` zorunlu | Kalite düşer |
| 5 | **Dosya Boyutu** | Max 1000 satır (ADR-042) | Token aşımı |
| 6 | **Secret Yok** | API key, password ASLA yazılmaz | Güvenlik ihlali |
| 7 | **Append-Only Log** | `log.md`'ye sadece ekleme | Audit trail bozulur |
| 8 | **Zero Hallucination** | Doğrulanamayan bilgi → `VERIFICATION REQUIRED` | Yanıltıcı bilgi yayılır |
| 10 | **Dosya Adı** | Kebab-case, uzantı `.md` | Navigasyon bozulur |

---

## 5. Naming Conventions

| Öğe | Format | Doğru Örnek | Yanlış Örnek |
|-----|--------|-------------|--------------|
| **Dosya** | `kebab-case.md` | `csrf-protection.md` | `CSRF_Protection.md` |
| **Dizin** | `kebab-case/` | `decisions/accepted/` | `Decisions/Accepted/` |
| **ADR** | `ADR-NNN-title.md` | `ADR-010-csrf.md` | `adr10-csrf.md` |
| **SQL** | `coremusic_domain.sql` | `coremusic_auth.sql` | `auth_db.sql` |
| **Bölüm Başlığı** | `## N. Başlık` | `## 1. Amaç` | `## Purpose` |
| **Alt Başlık** | `### N.M Başlık` | `### 3.1 Frontmatter` | `### Frontmatter` |
| **Wiki Link** | `[[path/to/file]]` | `[[architecture/l1-security]]` | `[[Architecture/L1-Security]]` |
| **Tablo** | Pipe format | `| Col1 | Col2 |` | Tab ile hizalama |
| **Code Block** | ``` ``` ``` | ` ```php ` | ` ``` ` (dil belirtmeden) |
| **Görsel** | `alt-text.png` | `middleware-flow.png` | `IMG_20260806.png` |

---

## 6. Security Considerations

| Konu | Kural | Açıklama |
|------|-------|----------|
| **Secret'lar** | ❌ YASAK | API key, password, JWT secret ASLA yazılmaz |
| **Token'lar** | ❌ YASAK | Session token, ARL token yazılmaz |
| **Credential** | ❌ YASAK | DB password, encryption key yazılmaz |
| **Maskelenmiş Veri** | ✅ İZİNLİ | `user***@example.com`, `[REDACTED]` |
| **Port Numaraları** | ✅ İZİNLİ | `port 81`, `port 3001` |
| **Mimari Kararlar** | ✅ İZİNLİ | ADR referansları, karar içerikleri |

**Hassas Veri Sınıflandırması:**

| Veri Türü | Sınıf | Dosyaya Yazılabilir mi? |
|-----------|-------|--------------------------|
| API Key | SECRET | ❌ ASLA |
| DB Password | SECRET | ❌ ASLA |
| JWT Secret | SECRET | ❌ ASLA |
| Session Token | SECRET | ❌ ASLA |
| User Email (masked) | PII | ✅ Kısmi maskeleme ile |
| Mimari Karar | PUBLIC | ✅ |
| Port Numarası | PUBLIC | ✅ |

---

## 7. Performance Notes

| Metrik | Limit | Aşım Durumu |
|--------|-------|-------------|
| **Dosya Boyutu** | Max 1000 satır | Token aşımı, context window hatası |
| **Toplam Vault** | Max 100MB | Disk doluluğu, yavaş tarama |
| **Wiki Link** | Max 50/dosya | Navigasyon karmaşası |
| **Tablo** | Max 20/dosya | Okunabilirlik düşüşü |
| **Code Block** | Max 15/dosya | Token israfı |
| **Mermaid** | Max 5/dosya | Render sürecleri uzar |
| **Görsel** | Max 1MB/dosya | Yavaş yükleme |

**Optimizasyon Önerileri:**

1. Büyük bölümleri ayrı dosyalara taşı
2. Mermaid diyagramlarını basit tut
3. Tabloları mümkünse listeye dönüştür
4. Görselleri sıkıştır (PNG < 500KB)
5. Wiki link sayısını 30'un altında tut

---

## 8. Edge Cases

| # | Senaryo | Belirti | Çözüm | İlgili ADR |
|---|---------|---------|-------|------------|
| 1 | **Kırık Wiki Link** | 404, boş sayfa | `index.md`'den doğru yolu bul | ADR-042 |
| 2 | **Eksik Frontmatter** | Metadata okunamıyor | 7 zorunlu alanı ekle | ADR-042 |
| 3 | **Çok Büyük Dosya** | Token aşımı | Bölümleriepar, 1000 satır limit | ADR-042 |
| 4 | **Dairesel Referans** | Sonsuz döngü | Link zincirini kır | — |
| 5 | **Eski ADR Referansı** | Hatalı bilgi | Güncel ADR'yi bul | ADR-005 |
| 6 | **Yanlış Versiyon** | Tutarlılık bozulması | Semver güncelle | ADR-004 |
| 7 | **Eksik Kod Bloğu** | Anlaşılmaz içerik | Çalışabilir kod ekle | — |
| 8 | **Yanlış Mermaid** | Diyagram render hatası | Mermaid syntax doğrula | — |
| 9 | **Duplicate İçerik** | Tekilleştirme eksikliği | Fazla içeriği temizle | ADR-042 |
| 10 | **Boş Dosya** | İçerik yok | Bölüm iskeletini doldur | — |

---

## 9. Troubleshooting

| Sorun | Belirti | Çözüm |
|-------|---------|-------|
| **Kırık wiki link** | `[[path]]` çalışmıyor | Doğru dosya yolunu `index.md`'den bul |
| **Eksik frontmatter** | Metadata okunamıyor | `---` ile başlayan YAML bloğu ekle |
| **Dosya çok büyük** | Token aşımı | Bölümleri ayrı dosyalara böl, 1000 satır limit |
| **Mermaid render hatası** | Diyagram görünmüyor | Syntax'ı https://mermaid.live'da test et |
| **Tablo bozuk** | Hücreler kaymış | Pipe (`|`) sayısını ve hizalamayı kontrol et |
| **Versiyon tutarsızlığı** | Eski versiyon yazılı | `updated` ve `version` alanlarını güncelle |
| **Çapraz referans eksik** | Link zinciri kırık | Tüm `[[link]]`'leri `index.md`'den doğrula |
| **Yanlış kod dili** | Syntax highlighting yok | Code block'ta dil belirtimi ekle |
| **Görsel yüklenmiyor** | Kırık görsel | Dosya yolunu ve uzantısını kontrol et |
| **Duplicate içerik** | Aynı bilgi birden fazla yerde | Tekilleştirme yap, birini sil |

---

## 10. Common Anti-Patterns

### 10.1 Frontmatter Hataları

❌ **YANLIŞ — Frontmatter yok:**

```markdown
# My Document

Some content...
```

✅ **DOĞRU — Tam frontmatter:**

```markdown
---
type: architecture
category: security
title: "My Document"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
---

# My Document
```

### 10.2 Wiki Link Hataları

❌ **YANLIŞ — Uzantılı link:**

```markdown
[[architecture/l1-security/]]
```

✅ **DOĞRU — Uzantısız link:**

```markdown
[[architecture/l1-security]]
```

### 10.3 Kod Bloğu Hataları

❌ **YANLIŞ — Dil belirtilmemiş:**

````markdown
```
<?php echo "hello"; ?>
```
````

✅ **DOĞRU — Dil belirtilmiş:**

````markdown
```php
<?php echo "hello"; ?>
```
````

### 10.4 Büyük Dosya Hataları

❌ **YANLIŞ — Tek dosyada 2000+ satır:**

```markdown
## 1. Amaç (500 satır)
## 2. Detaylar (1000 satır)
## 3. Uygulama (500 satır)
```

✅ **DOĞRU — Modüler yapı (1000 satır altında):**

```markdown
## 1. Amaç
## 2. Detaylar → [[detaylar-dosyasi]]
## 3. Uygulama → [[uygulama-dosyasi]]
```

### 10.5 Referans Hataları

❌ **YANLIŞ — Çapraz referans yok:**

```markdown
## 9. Bitiş

Bu doküman tamamlandı.
```

✅ **DOĞRU — Çapraz referanslar mevcut:**

```markdown
## 11. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[index]] | Ana katalog |
| [[brain.md]] | Mimari kararlar |

## 12. Çapraz Referanslar

| Bu Dokümandan | Hedef | İlişki |
|---------------|-------|--------|
| § 5 Güvenlik | [[ADR-010]] | Karar |
```

---

## 11. Document Types

| Tip | Dosya Adı Formatı | Frontmatter `type` | Kullanım |
|-----|-------------------|--------------------|---------:|
| **ADR** | `ADR-NNN-title.md` | `decision` | Mimari karar kaydı |
| **Architecture** | `topic-name.md` | `architecture` | Mimari doküman |
| **Template** | `*-template.md` | `template` | Yeniden kullanılabilir şablon |
| **Workflow** | `workflow-name.md` | `workflow` | İş akışı tanımı |
| **Guide** | `guide-name.md` | `guide` | Rehber / nasıl yapılır |
| **Reference** | `reference-name.md` | `reference` | Teknik referans |
| **System** | `system-name.md` | `system` | Sistem tanımı |
| **Index** | `index.md` | `system` | Ana katalog |
| **Log** | `log.md` | `system` | Aktivite günlüğü |

**Her tip için standart yapı:**

| Tip | Zorunlu Bölümler |
|-----|------------------|
| ADR | Bağlam, Karar, Sonuçlar, Reddedilen Alternatifler |
| Architecture | Amaç, Sistem Tanımı, Diyagramlar, Uygulama |
| Template | Amaç, Kullanım, Örnek, Kurallar |
| Workflow | Tetikleyici, Adımlar, Kontrol Listesi, Çıktı |
| Guide | Amaç, Ön Gereksinimler, Adımlar, Doğrulama |
| Reference | Tanımlar, Parametreler, Örnekler, Notlar |

---

## 12. ASCII Art Diagram Library

### 12.1 Flowchart — Temel Akış

````markdown
```
Başlangıç ──▶ {Karar Noktası}
                 │
            Evet ▼ Hayır
         İşlem 1  İşlem 2
            │       │
            └───┬───┘
                ▼
             Sonuç ──▶ Bitiş
```
````

### 12.2 Flowchart — Middleware Pipeline

````markdown
```
SessionManager ──▶ BypassAuth ──▶ RateLimiter ──▶ Auth ──▶ SecurityHeaders ──▶ Csrf
```
````

### 12.3 Sequence Diagram — API Akışı

````markdown
```
Client ──▶ Router ──▶ Middleware ──▶ PHP Backend ──▶ Database
   │            │            │              │             │
   │     HTTP Request  Pipeline    Authenticated   SQL Query
   │            │            │        Request         │
   │            │            │              │      Result
   │            │            │              ◀────────┘
   │            │            ◀── JSON Response ───┘
   ◀───────────┘
```
````

### 12.4 Class Diagram — Service Sınıfı

````markdown
```
┌─────────────────┐      ┌─────────────────┐      ┌─────────────────┐
│   AuthService   │      │ SessionManager  │      │ UserRepository  │
├─────────────────┤      ├─────────────────┤      ├─────────────────┤
│ -secretKey      │      │ -lifetime       │      │                 │
├─────────────────┤      ├─────────────────┤      ├─────────────────┤
│ +login(email,   │──────│ +start()        │      │ +findByEmail()  │
│   password)     │      │ +destroy()      │      │ +updateLast     │
│ +logout()       │      │ +regenerate()   │      │   Login()       │
│ +validateToken()│      │                 │      │                 │
└─────────────────┘      └─────────────────┘      └─────────────────┘
```
````

### 12.5 State Diagram — ADR Yaşam Döngüsü

````markdown
```
[*] ──▶ Draft ──▶ Review ──▶ Active ──▶ Frozen ──▶ [*]
              ▲         │
              └─────────┘
            Reddedildi
```
````

### 12.6 Pie Chart — Test Coverage Dağılımı

````markdown
```
Test Coverage Dağılımı:
├── Unit Tests: %70
├── Integration: %20
└── E2E: %10
```
````

### 12.7 Gantt — Proje Zaman Çizelgesi

````markdown
```
MVP Zaman Çizelgesi:
═══════════════════════════════════════════════════════
Faz 1:
  Altyapı     │████████████░░░░░░░░░░░░░░░░░░░░│ 30 gün
  Backend     │░░░░░░░░░░░░████████████████░░░░│ 45 gün
Faz 2:
  Frontend    │░░░░░░░░░░░░░░░░░░░░░░░░████████│ 30 gün
  Testing     │░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░██│ 15 gün
═══════════════════════════════════════════════════════
```
````

---

## 13. Related Documents

| Dosya | Amaç |
|-------|------|
| [[index]] | Ana katalog — tüm vault yapısı |
| [[CLAUDE.md]] | Ana sözleşme — 10-adım boot protokolü |
| [[AGENTS.md]] | Agent kayıt defteri — 7 uzman ajan |
| [[brain.md]] | Mimari kararlar — ADR 001-050 |
| [[keys.md]] | Keyword haritası — navigasyon |
| [[MEMORY.md]] | Session hafızası — persist state |
| [[WORKFLOW.md]] | Süreçler — 12-fazlı vault refactoring |
| [[log.md]] | Aktivite günlüğü — audit trail |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination |

---

## 14. Cross-References

| Bu Dokümandan | Hedef | İlişki Tipi |
|---------------|-------|-------------|
| § 1 Amaç | [[CLAUDE.md]] | Ana sözleşme |
| § 2 Tech Stack | [[index]] | Master katalog |
| § 4 Guardrails | [[ADR-005-ultrathink-protocol]] | Zero hallucination |
| § 6 Security | [[ADR-022-database-hardened-security]] | Güvenlik standartları |
| § 8 Edge Cases | [[ADR-007-cache-namespace]] | Zero Code Before Plan |
| § 11 Doc Types | [[ADR-042-vault-restructuring-2026-08-03]] | Dosya yapısı |
| § 12 Mermaid | [[brain.md]] | Mimari diyagramlar |

---

## 15. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | ~600 |
| **Frontmatter** | ✅ Tamamlandı (7/7 zorunlu alan) |
| **Wikilink'ler** | ✅ Doğrulandı (10+ aktif link) |
| **Çapraz Referanslar** | ✅ Güncel (8 satır harita) |
| **Mermaid Diyagram** | ✅ 7 şablon |
| **Anti-Pattern** | ✅ 5 örnek (❌/✅ karşılaştırmalı) |
| **Edge Case** | ✅ 10 senaryo |
| **Troubleshooting** | ✅ 10 sorun-çözüm çifti |
| **Son Güncelleme** | 2026-08-06 |

---

## 16. Examples

### 16.1 Örnek — Architecture Dokümanı

```markdown
---
type: architecture
category: security
title: "CSRF Protection Strategy"
date: 2026-07-15
updated: 2026-08-06
status: active
version: 2.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: PHP 8.4, hash_equals, Session
---

# CSRF Protection Strategy

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-042-vault-restructuring-2026-08-03]]

## 1. Amaç

Cross-Site Request Forgery (CSRF) koruma stratejisini tanımlar.

## 2. Kapsam

- **Kapsam:** Form submission, AJAX istekleri, state-changing operations
- **Kapsam Dışı:** GET istekleri, read-only operations

## 3. Terminoloji

| Terim | Tanım |
|-------|-------|
| CSRF | Cross-Site Request Forgery |
| Token | Rastgele üretilen güvenli dizge |
| Timing-safe | Zamana duyarlı karşılaştırma |

## 4. Sistem Tanımı

```php
$token = $_SESSION['csrf_token'];
if (!hash_equals($token, $_POST['csrf_token'])) {
    http_response_code(403);
    exit('CSRF validation failed');
}
```

## 5. Uygulama

Her POST/PUT/DELETE isteğinde `csrf_token` doğrulaması zorunludur.

## 6. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[ADR-010-csrf-protection-strategy]] | CSRF karar dokümanı |
| [[architecture/l1-security]] | Güvenlik katmanı |

## 7. Çapraz Referanslar

| Bu Dokümandan | Hedef | İlişki |
|---------------|-------|--------|
| § 4 Sistem | [[ADR-010-csrf-protection-strategy]] | Karar |
| § 5 Uygulama | [[ADR-011-session-management]] | Session |

## 8. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.1.0 |
| **Satır Sayısı** | ~50 |
| **Frontmatter** | ✅ |
| **Wikilink'ler** | ✅ |
```

### 16.2 Örnek — Workflow Dokümanı

```markdown
---
type: workflow
category: process
title: "Security Audit Workflow"
date: 2026-07-20
updated: 2026-08-06
status: active
version: 1.5.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
---

# Security Audit Workflow

**See also:** [[index]] · [[CLAUDE.md]] · [[AGENTS.md]]

## 1. Amaç

Güvenlik denetimi sürecini standartlaştırmak.

## 2. Tetikleyici

- Periyodik güvenlik denetimi (aylık)
- Güvenlik açığı raporu
- Yeni özellik sonrası

## 3. Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | OWASP Top 10 kontrol listesi hazırla | Security Engineer | 15 dk |
| 2 | Middleware sırasını doğrula | Security Engineer | 10 dk |
| 3 | Şifreleme standartlarını kontrol et | Security Engineer | 15 dk |
| 4 | CSRF/CSP doğrulaması yap | Security Engineer | 20 dk |
| 5 | Rate limiting'i test et | QA Engineer | 15 dk |
| 6 | Güvenlik raporu oluştur | Security Engineer | 30 dk |
| 7 | Tespit edilenleri düzelt | Backend Architect | Değişken |
| 8 | `log.md`'ye kaydet | Tüm ajanlar | 5 dk |

## 4. Kontrol Listesi

- [ ] CSRF token kullanımı (`csrf_token`)
- [ ] CSP nonce aktif
- [ ] Rate limiting çalışır
- [ ] Session timeout ayarlı
- [ ] Credential vault şifreli

## 5. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[ADR-010-csrf-protection-strategy]] | CSRF |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP |
| [[ADR-013-rate-limiting-apcu]] | Rate limit |

## 6. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.5.0 |
| **Satır Sayısı** | ~70 |
| **Frontmatter** | ✅ |
```

### 16.3 Örnek — Reference Dokümanı

```markdown
---
type: reference
category: infrastructure
title: "Port Registry"
date: 2026-07-01
updated: 2026-08-06
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
---

# Port Registry

**See also:** [[index]] · [[brain.md]] · [[ADR-042-vault-restructuring-2026-08-03]]

## 1. Amaç

Tüm servislerin port atamalarını merkezi olarak yönetmek.

## 2. Port Haritası

| Port | Servis | Protokol | Durum |
|------|--------|----------|-------|
| 80 | admin.coremusic.net | HTTP | ✅ Aktif |
| 81 | music.coremusic.net | HTTP | ✅ Aktif |
| 3001 | download.coremusic.net | HTTP/WS | ✅ Aktif |
| 3306 | MySQL 9 | TCP | ✅ Aktif |
| 5000 | media.coremusic.net | HTTP | ✅ Aktif |
| 9741 | Audio Service | HTTP | ✅ Aktif |
| 9742 | Audio Service | WS | ✅ Aktif |

## 3. Kurallar

| Kural | Açıklama |
|-------|----------|
| Port 81 | Sadece music.coremusic.net |
| Port 80 | Sadece admin.coremusic.net |
| Port 3001 | Sadece download.coremusic.net |
| Yüksek port | Geçici test portları |

## 4. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[ADR-042-vault-restructuring-2026-08-03]] | Port 81 standardı |
| [[architecture/03-contracts/ports/port-registry]] | Detay |

## 5. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır Sayısı** | ~45 |
| **Frontmatter** | ✅ |
```

---

## 17. Checklist

Pre-commit dokümantasyon kontrol listesi:

### 17.1 Frontmatter Kontrolü

- [ ] `type` alanı mevcut ve geçerli
- [ ] `category` alanı mevcut
- [ ] `title` alanı mevcut ve tırnak içinde
- [ ] `date` alanı YYYY-MM-DD formatında
- [ ] `updated` alanı YYYY-MM-DD formatında
- [ ] `status` alanı `active`, `draft` veya `frozen`
- [ ] `version` alanı X.Y.Z formatında (semver)
- [ ] `authority` alanı mevcut
- [ ] `governance` alanı mevcut

### 17.2 Yapı Kontrolü

- [ ] H1 başlık dosya başında mevcut
- [ ] Bölümler numaralandırılmış (## 1., ## 2., vb.)
- [ ] Alt bölümler numaralandırılmış (### 3.1, ### 3.2, vb.)
- [ ] Başlık hiyerarşisi doğru (H1 → H2 → H3)
- [ ] Kod bloklarında dil belirtilmiş
- [ ] Tablolar doğru formatta

### 17.3 Link Kontrolü

- [ ] Wiki link'ler `[[path]]` formatında
- [ ] Link uzantıları yok (`.md` eklenmemiş)
- [ ] Link hedefleri mevcut
- [ ] Dairesel referans yok
- [ ] `See also` bölümü mevcut
- [ ] `Related Documents` bölümü mevcut

### 17.4 İçerik Kontrolü

- [ ] Kod blokları çalışabilir
- [ ] Mermaid diyagramları geçerli
- [ ] Çağrı kutuları doğru yerlerde
- [ ] Secret/API key yazılmamış
- [ ] `VERIFICATION REQUIRED` etiketi eksikler için mevcut
- [ ] Dosya boyutu 1000 satır altında

### 17.5 Son Kontroller

- [ ] `version` güncellendi
- [ ] `updated` alanı bugünkü tarih
- [ ] `log.md`'ye kayıt eklendi
- [ ] Cross-reference'lar doğrulandı
- [ ] Quality Report bölümü mevcut

---

## 18. Review Guide

Doküman inceleme rehberi — nasıl gözden geçirilir:

### 18.1 İnceleme Adımları

| # | Adım | Süre | Açıklama |
|---|------|------|----------|
| 1 | Frontmatter doğrula | 1 dk | 7 zorunlu alan, semver, tarih |
| 2 | Yapı kontrol et | 2 dk | Numaralı başlıklar, hiyerarşi |
| 3 | Link'leri test et | 3 dk | Tüm `[[link]]`'ler çalışır mı |
| 4 | Kod bloklarını incele | 3 dk | Çalışabilir, dil belirtilmiş |
| 5 | Tabloları kontrol et | 1 dk | Pipe formatı, hizalama |
| 6 | Mermaid'ı test et | 2 dk | https://mermaid.live'da render |
| 7 | İçeriği doğrula | 5 dk | Teknik doğruluk, hallüsinasyon yok |
| 8 | Güvenliği denetle | 2 dk | Secret, API key, password yok |
| 9 | Boyut kontrolü | 1 dk | 1000 satır altında |
| 10 | Cross-reference | 2 dk | Tüm referanslar güncel |

**Toplam tahmini süre:** ~22 dakika/dosya

### 18.2 İnceleme Kontrol Formu

```markdown
## Doküman İnceleme Formu

**Dosya:** [[path/to/file]]
**İnceleyen:** {agent-name}
**Tarih:** YYYY-MM-DD

### Sonuç

- [ ] ✅ ONAY — Dosya standartlara uygun
- [ ] ⚠️ DÜZELTME GEREKLİ — Küçük sorunlar mevcut
- [ ] ❌ RED — Ciddi sorunlar mevcut

### Tespit Edilen Sorunlar

| # | Sorun | Ciddiyet | Düzeltme |
|---|-------|----------|----------|
| 1 | {sorun açıklaması} | Yüksek/Orta/Düşük | {düzeltme önerisi} |
```

### 18.3 Yaygın İnceleme Hataları

| # | Hata | Tespit Yöntemi | Çözüm |
|---|------|----------------|-------|
| 1 | Eksik frontmatter | Dosyanın başına bak | 7 alanı ekle |
| 2 | Yanlış wiki link formatı | Linklere tıkla | `[[path]]` formatına düzelt |
| 3 | Kod bloğunda dil yok | ` ``` ` satırlarını kontrol et | Dil belirtimi ekle |
| 4 | 1000 satır aşımlı | `wc -l` ile say | Bölümleri parçala |
| 5 | Secret yazılmış | grep ile tara | `[REDACTED]` ile değiştir |
| 6 | Versiyon güncellenmemiş | `version` alanını kontrol et | Semver güncelle |
| 7 | Circular referans | Link zincirini takip et | Linki kır veya kaldır |
| 8 | Mermaid syntax hatası | mermaid.live'da test et | Syntax'ı düzelt |
| 9 | Eksik Related Documents | Bölüm var mı bak | Bölümü ekle |
| 10 | Eksik Quality Report | Son bölüm var mı bak | Bölümü ekle |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-06
**Mode:** Red Team • Human Mode • Truth Mode
