---
type: adr
category: documentation
title: "ADR-024: Ecosystem Modular Docs"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-024: Ecosystem Modular Docs

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Documentation
**İlgili Agent:** Tüm agent'lar

---

## 1. Amaç

Bu ADR, CoreMusic ekosistemindeki tüm dokümantasyon yapısını modüler olarak tanımlar. Dosya formatı standartları, wiki-link sistemi, frontmatter zorunlulukları, versiyonlama ve navigasyon kurallarını kapsar. Tüm vault dosyaları bu sözleşmeyle bağlıdır.

---

## 2. Bağlam

CoreMusic 404+ markdown dosyasından oluşan devasa bir vault'a sahiptir:
- `.ai/` dizini: Ana vault (CLAUDE.md, AGENTS.md, vb.)
- `decisions/` dizini: 50 ADR dosyası
- `architecture/` dizini: L0-L3 katman dokümantasyonu
- `projects/` dizini: Neva Engine, Neva Player, vb.
- `electronic/` dizini: Donanım tasarımı
- `testing/` dizini: Test stratejileri
- `ecosystem/` dizini: Servis entegrasyonu

Bu kadar geniş bir dokümantasyon ekosisteminde tutarlılık, navigasyon ve bakım kritik öneme sahiptir.

---

## 3. Karar

CoreMusic'te **modüler dokümantasyon** yapısı kullanılacak. Her konu ayrı bir dosyada, standart formatta, çapraz referanslı olarak tutulacaktır.

### 3.1 Temel Prensipler

| Prensip | Açıklama | ADR |
|---------|----------|-----|
| Modülerlik | Her konu ayrı dosya | Bu ADR |
| SSOT | Tek doğruluk kaynağı | [[ADR-042-vault-restructuring-2026-08-03]] |
| Cross-reference | Wiki-link sistemi | Bu ADR |
| Versioning | Her dosya versiyonlu | Bu ADR |
| Frontmatter | 7 zorunlu alan | Bu ADR |
| Navigasyon | Index ve keys ile | [[ADR-042-vault-restructuring-2026-08-03]] |

---

## 4. Teknik Detaylar

### 4.1 Frontmatter Standardı

Her markdown dosyası aşağıdaki frontmatter alanlarını içermek **zorundadır**:

```yaml
---
type: adr|guide|system|architecture|project|persona
category: security|routing|testing|documentation|audio|architecture|infrastructure|...
title: "Dosya Başlığı"
date: YYYY-MM-DD
updated: YYYY-MM-DD
status: draft|review|active|frozen
version: X.Y.Z
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---
```

#### 4.1.1 Zorunlu Alanlar

| Alan | Format | Örnek | Açıklama |
|------|--------|-------|----------|
| `type` | enum | `adr` | Dosya türü |
| `category` | enum | `security` | Kategori |
| `title` | string | `"ADR-020: ..."` | Dosya başlığı |
| `date` | date | `2026-05-15` | Oluşturma tarihi |
| `updated` | date | `2026-08-08` | Son güncelleme |
| `status` | enum | `frozen` | Durum |
| `version` | semver | `1.0.0` | Versiyon |
| `authority` | string | `SSOT` | Otorite |
| `governance` | string | `Red Team...` | Yönetim |

#### 4.1.2 Type Enum Değerleri

| Değer | Kullanım Alanı |
|-------|---------------|
| `adr` | Architecture Decision Record |
| `guide` | Kılavuz/talimat dosyası |
| `system` | Sistem tanımı |
| `architecture` | Mimari dokümantasyon |
| `project` | Proje dokümantasyonu |
| `persona` | Kullanıcı personası |
| `workflow` | İş akışı tanımı |
| `template` | Şablon dosyası |
| `research` | Araştırma notu |

#### 4.1.3 Status Enum Değerleri

| Değer | Anlamı | Değişiklik |
|-------|--------|------------|
| `draft` | Taslak | Tamamen düzenlenebilir |
| `review` | İnceleme | Kısıtlı değişiklik |
| `active` | Aktif | Minor güncelleme |
| `frozen` | Donmuş | Değiştirilemez |

### 4.2 Wiki-Link Sistemi

#### 4.2.1 Wiki-Link Formatı

```markdown
[[dosya/yolu]]
[[dosya/yolu#section]]
[[dosya/yolu|Görünen Ad]]
```

#### 4.2.2 Wiki-Link Kuralları

| Kural | Değer | İhlal |
|-------|-------|-------|
| Path formatı | Kökten başlamaz (`.ai/` hariç) | Kırık link |
| Section link | `#section-id` ile | Kırık anchor |
| Display text | Opsiyonel, ama tavsiye edilen | Görünüm düşüklüğü |
| Dosya varlığı | Link verilen dosya mevcut olmalı | Kırık link |
| Recursive check | Depth-first traversal | Sonsuz döngü |

#### 4.2.3 Wiki-Link Doğrulama

```regex
\[\[([^\]]+)\]\]
```

Bu regex ile tüm wiki-link'ler taranır:
1. Link hedefinin mevcut olup olmadığı kontrol edilir
2. Section reference'larının geçerliliği doğrulanır
3. Kırık link'ler tespit edilir ve düzeltilir

### 4.3 Dosya Yapısı

#### 4.3.1 Ana Vault Yapısı

```
.ai/
├── CLAUDE.md              ← Ana sözleşme (SSOT)
├── AGENTS.md              ← Agent kayıt defteri
├── WORKFLOW.md            ← Süreçler
├── index.md               ← Master katalog
├── keys.md                ← Keyword haritası
├── brain.md               ← Mimari kararlar
├── MEMORY.md              ← Session hafızası
├── log.md                 ← Audit trail
├── engine.md              ← Orkestrasyon motoru
├── decisions/
│   ├── accepted/          ← Kabul edilmiş ADR'ler (50 dosya)
│   ├── draft/             ← Taslak ADR'ler
│   └── rejected/          ← Reddedilmiş ADR'ler
├── architecture/
│   ├── l0-infrastructure.md
│   ├── l1-security.md
│   ├── l2-routing.md
│   ├── l3-presentation.md
│   ├── 05-data/
│   ├── 06-audio/
│   └── 07-security/
├── projects/
│   ├── NevaEngine/
│   ├── NevaPlayer/
│   └── download-service/
├── testing/
│   ├── strategy.md
│   └── coverage-targets.md
├── ecosystem/
│   └── 7-service-integration.md
├── electronic/
│   └── *.md
├── personas/
│   └── index.md
├── ui-design/
│   └── 00-index.md
├── sessions/
├── registry/
├── scaffold/
├── reports/
├── prompt-system/
├── knowledge/
│   ├── verified/
│   └── unverified/
├── research/
│   └── verified/
├── subdomains/
│   └── *.md
└── .templates/
    └── *.md
```

#### 4.3.2 Dosya Boyut Kısıtlamaları

| Dosya Türü | Max Satır | Max Boyut | İhlal |
|------------|-----------|-----------|-------|
| ADR | 500+ satır | — | Eksik bilgi |
| CLAUDE.md | 500+ satır | — | Eksik bilgi |
| index.md | 500+ satır | — | Eksik katalog |
| Brain | 500+ satır | — | Eksik karar |
| Strategy | 200+ satır | — | Eksik strateji |
| Persona | 100+ satır | — | Eksik tanım |
| Template | 200+ satır | — | Eksik şablon |

### 4.4 Cross-Reference Sistemi

#### 4.4.1 Çapraz Referans Formatı

```markdown
| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-010-csrf-protection-strategy]] | CSRF token kullanımı |
```

#### 4.4.2 Çapraz Referans Kuralları

| Kural | Değer | İhlal |
|-------|-------|-------|
| Tablo formatı | Markdown tablosu | Okunamaz |
| Hedef | Wiki-link formatı | Kırık link |
| İlişki | Kısa açıklama | Anlaşılmaz |
| Minimum | 3 çapraz referans | Eksik bağlantı |
| Maximum | 10 çapraz referans | Aşırı bağlantı |

### 4.5 Navigasyon Sistemi

#### 4.5.1 Ana Navigasyon Noktaları

| Nokta | Dosya | Amaç |
|-------|-------|------|
| Ana katalog | `index.md` | Tüm vault yapısı |
| Keyword haritası | `keys.md` | Keyword → dosya yönlendirme |
| Mimari kararlar | `brain.md` | ADR ve kararlar |
| Session hafızası | `MEMORY.md` | Persistent state |
| Audit trail | `log.md` | Aktivite günlüğü |

#### 4.5.2 Navigasyon Akışı

```
Kullanıcı isteği
  → index.md (genel bakış)
    → keys.md (keyword arama)
      → İlgili dosya
        → Çapraz referanslar
          → İlgili diğer dosyalar
```

### 4.6 Versiyonlama Sistemi

#### 4.6.1 Versiyon Formatı

```
MAJOR.MINOR.PATCH

MAJOR: Yapısal değişiklik (ADR frozen → yeni ADR)
MINOR: İçerik ekleme/güncelleme
PATCH: Düzeltme (yazım hatası, link düzeltme)
```

#### 4.6.2 Versiyon Kuralları

| Değişiklik | Versiyon | Onay |
|-----------|---------|------|
| Yeni bölüm ekleme | MINOR | — |
| ADR güncelleme (active) | MINOR | Arch Lead |
| Frozen ADR değiştirme | Yeni ADR | Vault Steward |
| Yazım hatası | PATCH | — |
| Yapısal değişiklik | MAJOR | Vault Steward |

### 4.7 Dosya Adlandırma Standartları

#### 4.7.1 İsimlendirme Formatı

| Tür | Format | Örnek |
|-----|--------|-------|
| ADR | `ADR-NNN-kebab-case.md` | `ADR-020-api-public-security.md` |
| Architecture | `lN-*.md` | `l1-security.md` |
| Project | `proje-adi/*.md` | `NevaEngine/overview.md` |
| Template | `*.md` | `php.md`, `js.md` |

#### 4.7.2 İsimlendirme Kuralları

| Kural | Değer | İhlal |
|-------|-------|-------|
| Kebab-case | Küçük harf, tire | Okunamaz |
| Boşluk yasak | `_` veya `-` | Hata |
| Uzantı | `.md` zorunlu | Tanınmaz |
| Anlamlı isim | İçeriği yansıtmalı | Karışıklık |

### 4.8 Template Sistemi

#### 4.8.1 Mevcut Şablonlar (19 Dil)

| # | Şablon | Dil |
|---|--------|-----|
| 1 | PHP | tr/en |
| 2 | JavaScript | tr/en |
| 3 | CSS | tr/en |
| 4 | C++ | tr/en |
| 5 | PHPUnit | tr/en |
| 6 | Vitest | tr/en |
| 7 | Migration | tr/en |
| 8 | Docker | tr/en |
| 9 | GitHub Actions | tr/en |
| 10 | API-doc | tr/en |
| 11 | Security-audit | tr/en |
| 12 | ADR | tr/en |
| 13 | Arduino | tr/en |
| 14 | AVR | tr/en |
| 15 | PIC | tr/en |
| 16 | C | tr/en |
| 17 | Node.js | tr/en |
| 18 | ASP.NET | tr/en |
| 19 | WikiPage | tr/en |

#### 4.8.2 Şablon Kullanım Kuralları

| Kural | Değer | İhlal |
|-------|-------|-------|
| Zorunlu frontmatter | 7 alan | Geçersiz dosya |
| Code block | Dil belirtilmeli | Syntax hatası |
| Section yapısı | Standart bölüm başlıkları | Karışıklık |
| Cross-reference | Min 3 referans | Eksik bağlantı |

### 4.9 Dokümantasyon Yaşam Döngüsü

```
Oluşturma → Review → Active → Frozen
    │          │        │        │
    │          │        │        └── Değiştirilemez
    │          │        └── Minor güncelleme
    │          └── İnceleme bekliyor
    └── İlk taslak
```

### 4.10 Dosya Boyut Optimizasyonu

#### 4.10.1 Büyük Dosya Stratejisi

| Durum | Aksiyon | Örnek |
|-------|---------|-------|
| >500 satır | Böl ve link'le | `brain.md` → parçalar |
| >1000 satır | Zorunlu arşivleme | `log.md` → rotation |
| >100KB | Sıkıştır veya parçalama | JSON dump'ları |
| Tekrar eden blok | Template'e çıkar | Common sections |

#### 4.10.2 Arşiv Politikası

| Tür | Süre | Saklama |
|-----|------|---------|
| Eski ADR sürümleri | Sonsuz | `decisions/archive/` |
| Silinen dosyalar | 1 yıl | `archive/deleted/` |
| Eski versiyonlar | 6 ay | `archive/versions/` |
| Session logları | 1 yıl | `sessions/archive/` |

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| Frontmatter eksik | 7 zorunlu alan | Geçersiz dosya |
| Wiki-link kırık | Doğrulanmış link | Navigasyon bozuk |
| Büyük dosya (>1000 satır) | Parçalama | Okunamaz |
| Frontmatter olmayan dosya | Frontmatter zorunlu | Standard dışı |
| Büyük harf dosya adı | kebab-case | Karışıklık |
| Boşluklu dosya adı | tire/alt çizgi | Hata |
| Code block dil belirtme eksik | `php`, `javascript`, vb. | Syntax hatası |
| Versiyon belirtme eksik | Semver formatı | Takip edilemez |
| Çapraz referans eksik | Min 3 referans | İzole dosya |
| Aşırı çapraz referans | Max 10 referans | Karmaşıklık |

---

## 6. Edge Cases

| Edge Case | Tetikleyici | Çözüm |
|-----------|-------------|-------|
| Kirık wiki-link | Dosya silinme | Otomatik tarama + düzeltme |
| Circular reference | A → B → A döngüsü | Depth limit + uyarı |
| Encoding hatası | Unicode karakter | UTF-8 encoding zorunlu |
| Large file | >1000 satır | Parçalama + arşivleme |
| Merge conflict | Eşzamanlı düzenleme | Context lock + sıralı merge |
| Template uyumsuzluğu | Eski şablon | Otomatik güncelleme |
| Frontmatter parse hatası | Yanlış format | Validation + düzeltme |
| Duplicate content | Yinelenen bilgi | Birleştirme + redirect |
| Language mismatch | Yanlış dil | Otomatik tespit |
| Version drift | Eski versiyon | Otomatik güncelleme |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Frontmatter zorunlu — 7 alan eksiksiz | Dosya geçersiz |
| 2 | Wiki-link doğrulama — Tüm link'ler çalışmalı | Navigasyon bozuk |
| 3 | Dosya adı standartı — kebab-case | Okunamaz |
| 4 | Encoding UTF-8 — Tüm dosyalar | Bozuk karakter |
| 5 | Max 1000 satır — Büyük dosyalar parçalanmalı | Bakım zorluğu |
| 6 | Cross-reference min 3 — İzole dosya yasak | Bağlantısız bilgi |
| 7 | Versiyon semver — Format zorunlu | Takip edilemez |
| 8 | Template kullanımı — Yeni dosyalar için zorunlu | Standard dışı |
| 9 | Status management — Draft → Active → Frozen | Süreç ihlali |
| 10 | SSOT authority — Bilgi sadece vault'tan | Yanlış bilgi |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-004-multi-domain-spa]] | Vault versiyonlama | Dosya versiyonlama |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination | Doğrulama |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault yeniden yapılandırma | Dosya yapısı |
| [[ADR-040-database-authority]] | DB authority | Veri dokümantasyonu |
| [[ADR-001-vanilla-js-itcss]] | Frontend standartları | Frontend docs |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı |
| § 4.1 Frontmatter | [[index.md]] | Master katalog |
| § 4.2 Wiki-link | [[keys.md]] | Navigasyon |
| § 4.3 Dosya yapısı | [[architecture/l0-infrastructure]] | Altyapı |
| § 4.5 Navigasyon | [[brain.md]] | Mimari kararlar |
| § 4.8 Template | [[.templates/index]] | Şablon kataloğu |
| § 5 Yasak | [[CLAUDE.md]] §7 | Hard guardrails |
| § 7 Guardrails | [[WORKFLOW.md]] | Süreçler |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **SSOT** | Single Source of Truth — Tek Doğruluk Kaynağı |
| **ADR** | Architecture Decision Record — Mimari karar kaydı |
| **Wiki-Link** | `[[dosya/yolu]]` formatında çapraz referans |
| **Frontmatter** | Dosya başlığı metadata bloğu |
| **Kebab-case** | Küçük harf, tire ile ayrılmış isimlendirme |
| **Semver** | Semantic Versioning — MAJOR.MINOR.PATCH |
| **Cross-reference** | Dosyalar arası çapraz referans |
| **Navigation** | Vault içinde yön bulma |
| **Modülerlik** | Her konu ayrı dosya prensibi |
| **Immutability** | Değiştirilemezlik (frozen status) |
| **Encoding** | Karakter kodlama (UTF-8) |
| **Template** | Yeni dosya için şablon |
| **Draft** | Taslak durumu |
| **Active** | Aktif durum |
| **Frozen** | Donmuş, değiştirilemez durum |
| **Merge Conflict** | Eşzamanlı düzenleme çelişkisi |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| SSOT Authority | ADR-024 Ecosystem Modular Docs |
| Last Updated | 2026-08-08 |
| ADR References | 5 |
| Cross References | 8 |
| Edge Cases | 10 |
| Hard Guardrails | 10 |
| Forbidden Patterns | 10 |
| Glossary Terms | 16 |
| Frontmatter Fields | 9 (7 zorunlu + 2 opsiyonel) |
| Wiki-Link Rules | 5 |
| File Size Limits | 7 kategori |
| Template Count | 19 dil |
| Status Types | 4 (draft, review, active, frozen) |
| Type Enums | 9 |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Last Updated | 2026-08-08 |
| Mode | Red Team · Human Mode · Truth Mode |
| Governance | Single Source of Truth (SSOT) |
| Immutability | Frozen — Değiştirilemez |
| İstisna | Sadece hayati güvenlik hatası |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
