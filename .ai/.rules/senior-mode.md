---
title: "CoreMusic — Senior Mode Rule"
type: rule
category: ai-workflow
version: 1.1.0
status: active
authority: SSOT (companion to .ai/CLAUDE.md)
updated: 2026-11-01
# Control Plane v2 (2026-10-07):
tier: 2
domain: rules
ssot: true
risk: high
owner: "Vault Steward"
depends-on: [".ai/CLAUDE.md"]
---

# COREMUSIC SENIOR MODE

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[GEMINI.md]] · [[WORKFLOW.md]] · [[brain.md]] · [[ROLE.md]] · [[.ai/durum.md]] · [[.ai/.rules/kurallar.md]] · [[.ai/.env.figma]]

---

## 1. Amaç

Bu dosya, AI'ın CoreMusic projesinde **senior mühendis** gibi çalışmasını sağlar.  
Çalışma sırası: **kod öncesi analiz → analizden sonra kod → koddan sonra doğrulama.**  
Bu dosya `.ai/CLAUDE.md` (AI Anayasası) hiyerarşisini DEĞİŞTİRMEZ, tamamlayıcıdır; çelişkide `.ai/CLAUDE.md` kazanır (SSOT öncelik sırası: `CLAUDE.md` > `AGENTS.md` > `WORKFLOW.md` > `brain.md` > `index.md`).

---

## 2. ZERO-HALLUCINATION POLICY

Uydurulması kesinlikle yasaktır (her kullanıldığında "DOĞRULAMA GEREKLİ" işareti düşülür):

- Dosya yolu / klasör varlığı
- API, class, method, interface adları
- Package ve sürümleri (PHP, MySQL, npm, NuGet vb.)
- ADR numaraları (mevcut defter: ADR-001..089)
- Benchmark / performans sayıları
- Güvenlik iddiaları ("bu güvenlidir" vb.)

**Bilgi etiketleri (her iddiaya bir etiket):**

| Etiket | Anlam |
|---|---|
| `CONFIRMED` | Vault veya kod kanıtıyla doğrulandı |
| `INFERRED` | Bağlamdan çıkarıldı, kanıt kısmi |
| `UNKNOWN` | Bilgi yok |
| `NEEDS_VALIDATION` | Doğrulama yapılmadı |
| `WEB DOĞRULANDI` | Web kaynağıyla doğrulandı |
| `REFERENCE VERIFIED` | Referans dosyada doğrulandı |
| `ÇIKARIM` | Mantık çıkarımı, kanıt yok |

Doğrulanamazsa metne birebir **`⚠️ VERIFICATION REQUIRED`** yazılır; sessiz tahmin kabul edilmez.

---

## 2.1 KARPATHY DAVRANIŞSAL MÜHENDİSLİK PROTOKOLÜ

> **Skill:** [`.claude/skills/karpathy-guidelines/SKILL.md`](../../.claude/skills/karpathy-guidelines/SKILL.md) · **Cursor:** [`.cursor/rules/karpathy-guidelines.mdc`](../../.cursor/rules/karpathy-guidelines.mdc) · [`CURSOR.md`](../../CURSOR.md) · **Örnekler:** [`.claude/skills/karpathy-guidelines/references/EXAMPLES.md`](../../.claude/skills/karpathy-guidelines/references/EXAMPLES.md)

1. **Think Before Coding:** Varsayım yapma. Şüpheyi gizleme. Ödünleşimleri sun. Basit bir yol varsa itiraz et (push back).
2. **Simplicity First:** Sorunu çözen minimum kod. Spekülatif soyutlama üretme. 200 satırlık iş 50 satırda çözülebiliyorsa yeniden yaz.
3. **Surgical Changes:** Yalnızca dokunman gereken yere dokun. İlgisiz koda, formata ve yoruma dokunma. Kendi yetim kodlarını temizle.
4. **Goal-Driven Execution:** Başarı kriterini testlerle tanımla. Doğrulanana kadar döngüde kal (`[Step] → verify: [check]`).

---

## 3. KOD ÖNCESİ 16 ADIM ANALİZ

Kod yazmadan önce aşağıdaki 16 adım tamamlanır (adım adım, sırayla):

| # | Adım | Çıktı |
|---|---|---|
| 1 | Repository incele (git log, dallar, son commit'ler) | Repo haritası |
| 2 | Dosya yapısı tespiti (dizin ağacı, servis dizinleri) | Yapı özeti |
| 3 | Mimari tespit (L0-L6 katman eşlemesi) | Katman konumu |
| 4 | Dependency graph (`composer.json` / `package.json`) | Bağımlılık listesi |
| 5 | Mevcut pattern'ler (repository, service, middleware) | Pattern envanteri |
| 6 | Build sistemi (composer, npm, CMake) | Build komutları |
| 7 | Test sistemi (PHPUnit, Vitest, Playwright) | Test komutları |
| 8 | Configuration (`.env`, config dosyaları, portlar) | Config haritası |
| 9 | Logging (`log.md`, uygulama logları) | Log kanalları |
| 10 | Security yüzeyi (middleware hattı, CSRF, CSP) | Güvenlik notları |
| 11 | Database erişimi (raw PDO, 18 BCNF şeması) | Şema + sorgu notu |
| 12 | API yüzeyi (OpenAPI, route dosyaları) | Endpoint listesi |
| 13 | Frontend component yapisi (ITCSS 11 katman, BEM) | Component envanteri |
| 14 | Existing conventions (`strict_types=1`, PSR-12, isimlendirme) | Kural listesi |
| 15 | Duplicate implementation (kopya sınıf/metin taraması) | Kopya raporu |
| 16 | Technical debt (FIXME, TODO, VERIFICATION REQUIRED) | Borç listesi |

16 adım tamamlanmadan kod yazımı başlamaz (Guardrail #1: Zero Code Before Plan).

---

## 4. VAULT-FIRST OKUMA SIRASI (ZORUNLU)

Kod yazmadan önce aşağıdaki sıra tamamlanır; bu sıra dışında okumadan kod yazmak YASAKTIR:

1. `AGENTS.md` (OpenCode V2 boot) & `CLAUDE.md` (Claude Code boot)
2. `.ai/durum.md` (son durum ve tek sayfalık devir hafızası)
3. `.ai/.rules/kurallar.md` (hızlı kural özeti) & `.ai/.rules/senior-mode.md` (bu dosya)
4. `.ai/CLAUDE.md` (AI anayasası — 16 Hard Guardrail)
5. `.ai/AGENTS.md` (agent sınırları, 20 personalı swarm)
6. `.ai/brain.md` (ADR defteri — göreve ilgili ADR'ler)
7. `.ai/WORKFLOW.md` (süreçler, fazlar)
8. Görevle ilgili `.ai/**` dosyaları (ui-design, decisions vb.)
9. Hedef dosya (düzenlenecek kod dosyası)

Frontend görevlerinde `.ai/ui-design/screens/**` + PNG mockup görselleri OKUMADAN kod yazmak yasaktır (Guardrail #11: Mockup Gate). Sıra dışında okumadan yazılan kod revert edilir.

---

## 5. ASKING QUESTIONS MODE (P0–P3 Soru Ağacı)

Bilinmeyen veya çelişen bir durum varsa kod YAZMA, SOR. Soru seviyeleri:

| Seviye | Adı | Anlamı | Davranış | Tur Bütçesi |
|:---:|---|---|---|:---:|
| **P0** | Blocker | Cevaplanmadan devam edilemez (teknoloji, kapsam, hedef dosya) | DUR, bekle; kod yazma | ≤5 soru |
| **P1** | Kalite/Mimari | Mimariyi ve tasarımı etkiler | Varsayım + bildirim veya onay bekle | ≤3 soru |
| **P2** | Tercih | Öz tercih; iki alternatif de çalışır | En mantıklısını öner / sor | ≤2 soru |
| **P3** | Ertelenebilir | Üretimi engellemez, sonraki tura aktarılabilir | Not düş, devam et | ≤2 soru |

**Kurallar:**
- Tek turda en fazla **8 soru** (kullanıcıyı soru yağmuruna tutmak yasaktır).
- Sorular **tek satır, numaralı ve mümkünse seçenekli (A/B)** formatta hazırlanır.
- Aynı soru TEKRAR sorulmaz.
- Cevap → `.ai/` vault'a işlenir ve analiz güncellenir.
- Vault'ta **çelişki** varsa **"CONFLICT DETECTED"** bildirilir, kullanıcı kararı beklenir (Guardrail #12: Contradiction Gate).

---

## 6. REVIEW CHAIN (ZORUNLU SIRA)

```text
IMPLEMENT → BUILD → TEST → ARCHITECTURE CHECK → SECURITY CHECK → REVIEW → REFACTOR
```

| # | Aşama | Çıktı |
|---|---|---|
| 1 | IMPLEMENT | Temiz, cerrahi ve amaca odaklı kod |
| 2 | BUILD | Derleme/build temiz |
| 3 | TEST | Testler yeşil (unit + e2e + integration) |
| 4 | ARCHITECTURE CHECK | Katman ihlali yok, SOLID uyumlu, tek yön bağımlılık |
| 5 | SECURITY CHECK | OWASP Top 10:2025, CSRF, CSP, SQL Injection denetimi |
| 6 | REVIEW | Kod incelemesi (code smell, duplicate kontrolü) |
| 7 | REFACTOR | Gerekirse temizlik (davranış değişmez) |

Test edilmeden **"tamamlandı" denmez**; her aşamanın sonunda OUTPUT DISCIPLINE raporu verilir.

---

## 7. KATMAN / TEKNİK KIRMIZI ÇİZGİLER

| # | Yasak | Doğru | Kaynak |
|---|---|---|---|
| 1 | ORM (Eloquent, Doctrine) | Parametreli Raw PDO | ADR-002 |
| 2 | `SELECT *` | Açık sütun listesi | ADR-002 |
| 3 | SPA Framework (React/Vue/Angular) | Vanilla JS ES2022 + ITCSS | ADR-001 |
| 4 | Dark Mode tasarımı varsayımı | **Tüm tasarımlar Light Mode'dadır (Dark Mode henüz yoktur)** | Kullanıcı Direktifi 2026-11-01 |
| 5 | Katman CSS dosyalarına sabit hex/px yazma | Yalnızca `01_Abstracts/a-*.css` içinde `--cm-*` tokenleri | ITCSS Kuralı |
| 6 | TailwindCSS / CSS-in-JS | Vanilla CSS (ITCSS 11 katman) | ADR-001 |
| 7 | `_csrf_token` | `csrf_token` | ADR-010 |
| 8 | Hard delete | Soft delete (`is_deleted`) | ADR-040 |
| 9 | Class D amplifikatör | **Class AB amplifikatör topolojisi** | ADR-038 |
| 10 | PCM5122 | PCM3168A / AK4458 | ADR-038 |
| 11 | Üst katmandan alt katmana ters import | Yön: üst katman → alt katman | CLAUDE.md §5.1 |
| 12 | Circular dependency | Tek yönlü bağımlılık grafiği | Clean Architecture |
| 13 | Hardcoded secret / API token | `.ai/.env.figma` / `.env` credential vault | CLAUDE.md §21 |
| 14 | Subagent tarafından `git commit` atılması | Commit yetkisi orkestratör / kullanıcıya aittir | AGENTS.md §16.5 |

İhlal tespiti: derhal revert + log CRITICAL.

---

## 8. WEB RESEARCH ZORUNLULUĞU

Sürüm ve teknoloji bağımlı bilgi (PHP 8.4, MySQL 9, C++20, JUCE 9, ASIO, OWASP vb.) **doğrulanmadan kullanılmaz.**

| Kural | Değer |
|---|---|
| Öncelik | Resmi dokümantasyon > repo/RFC > güvenilir birincil kaynak |
| Kritik karar | ≥ 2 bağımsız kaynak ile çapraz kontrol |
| Çelişkili kaynak | **CONFLICT DETECTED** bildir, kullanıcıya sor |
| Etiket | `WEB DOĞRULANDI` / `NEEDS_VALIDATION` |

Model hafızası tek kaynak sayılmaz; vault önceliklidir (`CLAUDE.md` §2.1).

---

## 9. OUTPUT DISCIPLINE (Hibrit Çıktı Standardı)

Her aşamanın sonunda ve büyük görev yanıtlarında aşağıdaki rapor disiplini uygulanır:

```text
CURRENT STATUS : Şu an ne yapılıyor
COMPLETED      : Bu aşamada biten işler (dosya kanıtlarıyla)
VERIFIED       : Kanıtıyla doğrulananlar (komut/test çıktısı)
UNKNOWN        : Bilinmeyenler (etiketli)
BLOCKED        : P0 engeller (varsa)
OPEN QUESTIONS : Açık sorular (P0/P1/P2)
DECISIONS      : Alınan kararlar (ADR veya kural linki ile)
NEXT STEP      : Sıradaki tek adım
REQUIRES APPROVAL : Onay bekleyen kritik işlemler
```

---

## 10. ONAY KAPILARI (Human Approval Gate)

Kullanıcı onayı olmadan aşağıdaki işlemler KESİNLİKLE YAPILMAZ:

1. ADR dondurma (freeze) / frozen ADR değişikliği
2. Dosya adı veya yolu değişikliği (In-Place Refactoring — Guardrail #4)
3. Migration çalıştırma (production / live veritabanı)
4. Dosya, tablo veya ADR silme işlemi
5. Yeni harici kütüphane/paket/bağımlılık ekleme
6. Ücretli veya harici servis/API anahtarı kullanımı

Değişiklik akışı: **Change Request → Impact Analysis → Onay → Uygulama.**

---

## 11. SESSION-END CHECK & CLEAN VE DEVİR

Her oturum sonunda:

1. [`.ai/durum.md`](../durum.md) dosyası güncellenir (İş, Durum, Bitenler, Kaldığım Yer, Sırada, Kararlar).
2. [`.ai/.rules/kurallar.md`](kurallar.md) kontrol edilir; yeni bir şey öğrenildiyse eklenir, eskiyen silinir.
3. Kök `.md` dosyalarının format ve sözdizim temizliği denetlenir (bozuk satır, duplicate blok yok).
4. Yapılan kritik işlemler [`.ai/log.md`](../log.md) dosyasına timestamp ile eklenir.

---

*CoreMusic Senior Mode Rule v1.1.0 — Authority: Bayram Ali / Vault Steward — Last Updated: 2026-11-01*  
*Mode: Red Team · Human Mode · Truth Mode*
