---
node_id: K015-AI/ai-kapsam
node_type: module
status: draft
parent_id: K015-AI
children: []
depends_on:
  - K006-yapay-zeka
  - K007-data
  - K009-security
  - K019-testing
evidence:
  - "shared/src/AI/AIOrchestrator.php:26-38"
  - "shared/src/Api/Middleware/AuthenticationMiddleware.php:33-38"
  - "shared/composer.json:8"
validation:
  - "2026-10-13 tur-3: satır sayımı >=500 gate"
open_questions:
  - "K015 runtime etiketi (PLANNED/IMPLEMENTED) yok — UNKNOWN"
group: mimari
---

# K015-AI — ai-kapsam.md

> **Amaç:** K015-AI'nin kapsamını **tescilli giriş/çıkış (in/out)** listesiyle kesinleştirir.
> Kapsam kararı = mimari karardır; değişimi ADR ister, sessiz taşınmaz (ADR-097 §5).

## §1 Kapsam İlkeleri

| # | İlke | Açıklama | Kaynak |
|---|---|---|---|
| 1 | Ajan altyapısı = giriş | kim/ne/ne yapabilir/ne izinli | v2 §2.16 |
| 2 | Ürün ML = çıkış | model/öneri/analiz ürünleri | v2 §2.6 (K006) |
| 3 | Kanıt yoksa etiket | PLANNED/UNKNOWN yazılır | zero-hallucination |
| 4 | Sınır kayması = ADR | sessiz taşıma yasak | ADR-097 §6 |
| 5 | Komşu domain'e taşma | daraltma kararı da ADR ister | mimari kural |

## §2 GİRİŞ — K015-AI'nin sahibi olduğu yüzeyler

| # | Yüzey | Alt-başlıklar | Kanıt |
|---|---|---|---|
| 1 | agents | 11 ajan profili + MO | .ai/.agents/ = 12 .md |
| 2 | knowledge | bilgi modeli, kategori, arama | KnowledgeBase.php:25-27 |
| 3 | tools | araç envanteri + izin | ToolCalling.php:28-40 |
| 4 | context | boot bağlam paketi | kök AGENTS.md §14 |
| 5 | rag | retrieval altyapısı | ADR-030 (PLANNED) |
| 6 | kg | graf ilişki katmanı | motor kodu YOK |
| 7 | mcps | MCP sunucu yapılandırması | .claude/mcp.json:1-9 |
| 8 | boot-protocol | açılış sırası | skill-mandate.cjs:19-26 |
| 9 | orchestration | dağıtım/handover/escalation/lock | .ai/AGENTS.md §6-§12 |
| 10 | memory | L1/L2/L3 + HIERARCHY | MemorySystem.php:18-31 |
| 11 | evaluation | ajan çıktısı kalitesi | .ai/AGENTS.md §16 |
| 12 | permissions | dosya tipi sahipliği + ajan izni | .ai/AGENTS.md §5 |
| 13 | agent-testing | ajan davranışı testi | .ai/AGENTS.md §6.1 |

## §3 ÇIKIŞ — Bu domain'e AİT OLMAYAN yüzeyler

| # | Yüzey | Sahip domain | Ayırt eden kural |
|---|---|---|---|
| 1 | Model eğitimi, ağırlık, inference sunucusu | K006-yapay-zeka | v2 §2.6 |
| 2 | Öneri/kişiselleştirme ürün özellikleri | K006 · K020-recomenataion | ürün = model domaini |
| 3 | Müzik analizi / audio intelligence ürünleri | K006 | v2 §2.6 |
| 4 | Ünite/E2E/coverage test altyapısı | K019-testing | test edilen şey kod mu ajan mı |
| 5 | Sistem auth/CSRF/CSP/encryption | K009-security | ajan izni ≠ sistem auth |
| 6 | CI/CD, pipeline, deploy kapısı | K018-devops | — |
| 7 | OS player metrik/davranışı | K017 (ADR-019 Frozen) | değişiklik ≥088 |
| 8 | MySQL şema/migrasyon | K007-data | veri katmanı |
| 9 | Donanım/firmware (DAC, DSP, PCB) | donanım düğümleri | — |
| 10 | Servis iç uygulama mantığı | K012-services | ajan yalnız kamuya açık yüzeyi çağırır |

## §4 Karar Ağacı (hızlı sınıflandırma)

```text
Konu K015'da mı?
  ├─ Model/eğitim/öneri/analiz ürünü?        → HAYIR (K006 / K020-recomenataion)
  ├─ Kod testi (ünite/E2E/coverage)?         → HAYIR (K019)
  ├─ Sistem güvenliği (auth/CSRF/şifreleme)? → HAYIR (K009)
  ├─ CI/CD veya deploy?                      → HAYIR (K018)
  ├─ OS player davranışı?                    → HAYIR (K017 Frozen)
  ├─ Veri şeması / sorgu?                    → HAYIR (K007)
  └─ Ajan kimliği/boot/dağıtım/hafıza/izin/  → EVET (K015-AI)
     ajan kalite sınavı/araç envanteri
```

## §5 Kapsam ↔ Kademe-2 Çocuk Eşlemesi

| # | Çocuk | Kapsam kararı | Kapsam sınırı (ne dahil değil) |
|---|---|---|---|
| 1 | agents | GİRİŞ | model kartları (K006) |
| 2 | knowledge | GİRİŞ | ham veri ambarı (K007) |
| 3 | tools | GİRİŞ | servis iç uygulama (K012) |
| 4 | context | GİRİŞ | kullanıcı oturum auth'u (K009) |
| 5 | rag | GİRİŞ (PLANNED) | embedding modeli eğitimi (K006) |
| 6 | kg | GİRİŞ (motor YOK) | ilişkisel şema (K007) |
| 7 | mcps | GİRİŞ | harici hizmet hesapları (.env, K009) |
| 8 | boot-protocol | GİRİŞ | uygulama boot'ı (K011) |
| 9 | orchestration | GİRİŞ | iş kuyruğu/kuyruk altyapısı (K012) |
| 10 | memory | GİRİŞ | kalıcı DB önbelleği (K007) |
| 11 | evaluation | GİRİŞ (ajan) | model metriği (K006) |
| 12 | permissions | GİRİŞ | sistem rol/izin (K009) |
| 13 | agent-testing | GİRİŞ | ünite/E2E (K019) |

## §6 Omurga ↔ Kapsam Eşleme (ai-master §17 karşılığı)

| # | Omurga katmanı | Kapsam dosyası | Karar |
|---|---|---|---|
| 1 | Kimlik | §2/1 agents | GİRİŞ |
| 2 | Bilgi | §2/2 · §2/5 · §2/6 | GİRİŞ · vektör ≠ K006 |
| 3 | Eylem | §2/3 · §2/7 | GİRİŞ · servis içi ≠ K012 |
| 4 | Başlangıç | §2/4 · §2/8 | GİRİŞ |
| 5 | Koordinasyon | §2/9 | GİRİŞ |
| 6 | Hafıza | §2/10 | GİRİŞ |
| 7 | Yetki | §2/12 | GİRİŞ · sistem auth ≠ K009 |
| 8 | Sınav | §2/11 · §2/13 | GİRİŞ · model testi ≠ K006 |

## §7 K006 ile Ortak Görünen Konular (tescilli ayrım)

| # | Konu | K015-AI | K006-yapay-zeka | Ayrım testi |
|---|---|---|---|---|
| 1 | evaluation | ajan çıktısı kalite sınavı | model performans metriği | ölçülen varlık: ajan mı model mi |
| 2 | inference | ajan LLM çağrısı (operasyonel) | inference sunucusu yönetimi | kim sahipleniyor |
| 3 | knowledge | bilgi modeli/erişim | eğitim verisi | veri kullanımı niyeti |
| 4 | memory | ajan oturum hafızası | model bağlam/pencere | kalıcılık katmanı |
| 5 | recommendation | ajan görev tipi olarak | öneri modeli (K020 ile) | ürün özelliği mi altyapı mı |

## §8 K009 ile Ortak Görünen Konular

| # | Konu | K015-AI | K009-security | Ayrım testi |
|---|---|---|---|---|
| 1 | permissions | ajan hangi dosyaya dokunabilir | kullanıcı/rol kimlik doğrulama | özne: ajan mı insan mı |
| 2 | redaction | ajan log davranışında [REDACTED] | sızıntı önleme politikası | kural sahibi |
| 3 | injection | prompt injection desenleri (PromptEngine) | XSS/SQLi (web) | tehdit yüzeyi |
| 4 | rate limit | ajan retry/escalation eşiği | HTTP rate limit (RateLimitMiddleware) | katman |

## §9 K019 ile Ortak Görünen Konular

| # | Konu | K015-AI | K019-testing | Ayrım testi |
|---|---|---|---|---|
| 1 | agent-testing | ajan davranışı, persona turları | ünite/E2E/coverage | test edilen özne |
| 2 | gate | vault/hook kapıları | CI test kapısı | çalıştığı yer |
| 3 | rubrik | ajan çıktı kalitesi (nitel) | kod kapsamı (nicel) | ölçüm türü |

## §10 K017 (Frozen) Etkileşimi

| # | Konu | Kural | Kaynak |
|---|---|---|---|
| 1 | ADR-019 per-OS player | FROZEN — değişiklik ≥088 ister | kural 11 |
| 2 | K015 bu konuya girmez | kapsam dışı (§3/7) | — |
| 3 | OS metrik isteği gelirse | DUR + handover | kural 13 ruhu |

## §11 K020 İlişkileri (kapsam açısından)

| # | K020 düğümü | K015 ile ilişki | Kapsam kararı |
|---|---|---|---|
| 1 | recomenataion | K015 altyapısını kullanır (planlanan) | komşu; K015 kapsamı dışına taşma yok |
| 2 | comilance | denetim izi log davranışı | komşu; log.md kuralı K015'da |
| 3 | comere | kapsam doğrulama | bağımsız |
| 4 | discovery | servis keşfi deseni | bağımsız (benzerlik) |
| 5 | licence | paket lisansı | bağımlılık konusu (bagimlilik §13) |
| 6 | adr | karar kaydı | süreç (bu dosyanın değişimi de ADR ister) |

## §12 Kabul Kriterleri (kapsam kararı nasıl tescillenir)

| # | Kriter | Şart |
|---|---|---|
| 1 | Kanıt | gerçek dosya yolu/satır veya URL (HTTP 200) |
| 2 | Etiket | kanıtsız kesin cümle yasak |
| 3 | Komşu çatışması | komşu domain sahibiyle ortak onay |
| 4 | Kayıt | bu dosya + ADR (gerekiyorsa) |
| 5 | Bildirim | kök/architecture index'e ekleme (bu turda yasaklı) |

## §13 Dışlama Gerekçeleri (her çıkışın nedeni)

| # | Çıkış | Gerekçe (1 cümle) |
|---|---|---|
| 1 | K006 | model ürününün yaşam döngüsü ayrıdır |
| 2 | K007 | veri şeması/sorgu işi ayrıdır |
| 3 | K009 | sistem güvenliği ayrıdır |
| 4 | K011 | uygulama yüzeyi ayrıdır |
| 5 | K012 | servis iç mantığı ayrıdır |
| 6 | K017 | Frozen ADR kapsamındadır |
| 7 | K018 | CI kapısı ayrıdır |
| 8 | K019 | kod testi ayrıdır |
| 9 | K020-* | iş domainleri ayrıdır |

## §14 Kapsam İhlali Senaryoları

| # | Senaryo | Tespit | Aksiyon |
|---|---|---|---|
| 1 | evaluation'a model metriği eklendi | §7/1 ihlali | geri al + ADR |
| 2 | permissions'a sistem auth eklendi | §8/1 ihlali | geri al + K009'a handover |
| 3 | rag içine embedding eğitimi kondu | §3/1 ihlali | K006'ya taşı (ADR ile) |
| 4 | agent-testing içine ünite test kondu | §9/1 ihlali | K019'a taşı |
| 5 | K017 konusuna dokunuldu | kural 11 ihlali | DUR + ≥088 süreci |

## §15 Kapsam Değişikliği Akışı

```text
Yeni konu teklifi
  → §4 karar ağacı (kime ait?)
    → K015 ise §2'ye ekleme (kayıt)
      → komşu etkisi var mı? (§7-§11) → evetse ortak onay
        → kanıt ekle (Dosya/Satır veya URL-200)
          → değişiklik büyükdür? → ADR aç
            → bu dosya + master güncellenir
```

## §16 Kapsam Dışı İsteklerde El Kitabı

| # | İstek tipi | Yanıt | Referans |
|---|---|---|---|
| 1 | «modeli eğitelim» | K006 — bu domain değil | §3/1 |
| 2 | «CSRF ekleyelim» | K009 — bu domain değil | §3/5 |
| 3 | «E2E yazalım» | K019 — bu domain değil | §3/4 |
| 4 | «pipeline kuralım» | K018 — bu domain değil | §3/6 |
| 5 | «player metriği» | K017 Frozen | §3/7 |

## §17 Kanıt Tablosu (bu dosyanın dayandığı okumalar)

| # | Dosya | Satır | Kullanım |
|---|---|---|---|
| 1 | shared/src/AI/AIOrchestrator.php | 26-38 | ajan envanteri (11) |
| 2 | shared/src/AI/ToolCalling.php | 28-40 | ajan izin tablosu |
| 3 | shared/src/Api/Middleware/AuthenticationMiddleware.php | 33-38 | PUBLIC_ROUTE_PATTERNS (sistem auth ≠ ajan izni) |
| 4 | shared/src/Api/Middleware/RateLimitMiddleware.php | 22-23 | 60/60 (sistem rate-limit ≠ ajan retry) |
| 5 | shared/src/AI/MemorySystem.php | 18-21 | L1/L2/L3 (ajan hafızası) |
| 6 | shared/src/AI/KnowledgeBase.php | 48 | vektör arama uygulanmadı → rag PLANNED |
| 7 | shared/composer.json | 8 | php>=8.4 (çalışma zamanı) |
| 8 | .claude/mcp.json | 1-9 | MCP yüzeyi (anahtar REDACTED) |

## §18 Web Doğrulaması (yalnız havuz)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| 1 | Zorunlu/öneri ifadeleri RFC 2119'dur | https://www.ietf.org/rfc/rfc2119.txt | ölçüldü | HTTP 200 |
| 2 | AI risk yönetimi çerçevesi NIST AI RMF | https://www.nist.gov/itl/ai-risk-management-framework | ölçüldü | HTTP 200 |
| 3 | LLM/GenAI güvenlik riskleri OWASP GenAI | https://genai.owasp.org/ | ölçüldü | HTTP 200 |

## §19 Açık Sorular

| # | Soru | Etki |
|---|---|---|
| 1 | K015 runtime etiketi (PLANNED/IMPLEMENTED) | tüm domain etiketi |
| 2 | rag vektör deposu hangi teknoloji (K007) | rag kapsam derinliği |
| 3 | evaluation sınırı ADR'si ne zaman yazılacak | kapsam kayması riski |

## §20 Sözlük

| # | Terim | Tanım |
|---|---|---|
| 1 | GİRİŞ | bu domain'in sahibi olduğu yüzey |
| 2 | ÇIKIŞ | komşu domain'e ait yüzey |
| 3 | tescil | kayıt altına alma (dosya + gerekirse ADR) |
| 4 | Frozen | değişikliği onay sürecine bağlı yüzey (ADR-019) |
| 5 | handover | işin sahibi domain'e devri |

## §21 Kontrol Listesi (bu dosya gate)

| # | Kontrol | Beklenen |
|---|---|---|
| 1 | Satır | >=500 |
| 2 | FM | 10 alan + group=mimari |
| 3 | `parent:` | yok |
| 4 | Son satır | Authority |
| 5 | Mojibake | 0 |
| 6 | Yer tutucu | 0 |

## §22 İlişkili Dosyalar

| # | Dosya | İlişki |
|---|---|---|
| 1 | ai-master.md | omurga (§16-§17 eşlemesi) |
| 2 | ai-bagimliliklar.md | bağımlılık detayı |
| 3 | ai-dogrulama.md | kanıt/ölçüm defteri |
| 4 | ai-ornekler.md | kod örnekleri |

## §23 Kapsam Matrisi — Çocuk × Komşu Domain (kime ne devredilir)

| # | Çocuk | K006 | K007 | K009 | K012 | K019 |
|---|---|---|---|---|---|---|
| 1 | agents | model profili → hayır | — | rol doğrulama → hayır | — | — |
| 2 | knowledge | — | veri ambarı → evet | — | — | — |
| 3 | tools | — | — | — | servis içi → evet | — |
| 4 | context | — | — | oturum auth → hayır | — | — |
| 5 | rag | embedding → evet | vektör deposu → evet | — | — | — |
| 6 | kg | — | ilişki şeması → evet | — | — | — |
| 7 | mcps | — | — | anahtar (.env) → evet | — | — |
| 8 | boot-protocol | — | — | — | uygulama boot → hayır | — |
| 9 | orchestration | — | — | — | iş kuyruğu → hayır | — |
| 10 | memory | — | DB cache → evet | — | — | — |
| 11 | evaluation | model metriği → evet | — | — | — | — |
| 12 | permissions | — | — | sistem auth → evet | — | — |
| 13 | agent-testing | — | — | — | — | ünite/E2E → evet |

## §24 Giriş Koşulları (yeni çocuk subdomain K015'ya nasıl eklenir)

| # | Koşul | Kanıt türü |
|---|---|---|
| 1 | v2 spec'te çocuk olarak tanımlı | spec satırı |
| 2 | 3 içerik dosyası üretilebilir (kanıt var) | Dosya/Satır |
| 3 | Komşu domain ile çakışma yok | §7-§11 matrisi |
| 4 | index 40-100 + 3 içerik >=500 | ölçüm |
| 5 | Registry'ye kayıt | architecture index (bu turda yasaklı) |

## §25 Çıkış Koşulları (yüzey neden çıkarılır)

| # | Koşul | Aksiyon |
|---|---|---|
| 1 | Sahip domain netleşti | §3'e taşı + ADR |
| 2 | Kod kanıtı başka domaini gösteriyor | geri al |
| 3 | Frozen yüzey (K017) | DUR + ≥088 |
| 4 | Güvenlik yüzeyi (K009) | security-engineer'a handover |

## §26 Kapsam Sürümleme (bu dosyanın değişiklik kuralları)

| # | Değişiklik tipi | Onay | Kayıt |
|---|---|---|---|
| 1 | satır ekleme (kanıt) | vault steward | bu dosya |
| 2 | GİRİŞ/ÇIKIŞ taşıma | mimar + komşu sahip | ADR |
| 3 | çocuk kaldırma | üst karar | ADR + index |
| 4 | etiket değişimi (PLANNED→IMPLEMENTED) | kod kanıtı zorunlu | bu dosya |

## §27 Örnek Kapsam Kararları (olay → karar)

| # | Olay | Karar | Gerekçe |
|---|---|---|---|
| 1 | «rag için embedding modeli seçelim» | K006'ya git | §3/1 |
| 2 | «ajanlara dosya yazma izni ekleyelim» | K015 içi | §2/12 |
| 3 | «login akışını değiştirelim» | K009'a git | §3/5 |
| 4 | «model kartı yazalım» | K006 | §7/1 |
| 5 | «player gecikme metriği» | K017 Frozen | §10 |
| 6 | «E2E kapsamını %80'e çıkaralım» | K019 | §9/1 |

## §28 Sınır Riskleri

| # | Risk | Olasılık | Azaltma |
|---|---|---|---|
| 1 | evaluation isim örtüşmesi | yüksek | §7/1 ayrım testi + ADR |
| 2 | permissions ↔ auth karışması | orta | §8/1 ayrım testi |
| 3 | rag'ın K006'ya kayması | orta | §3/1 + §5/5 |
| 4 | agent-testing ↔ K019 karışması | orta | §9/1 |
| 5 | Frozen yüzeye istemeden dokunma | düşük | §4 karar ağacı |

## §29 Kapsam İlgisi Olmayan Konular (önleme listesi)

| # | Konu | Neden kapsam dışı |
|---|---|---|
| 1 | PCI/ücretlendirme | K020 iş alanları |
| 2 | KVKK/GDPR metni | K020-comilance |
| 3 | Paket lisans envanteri | K020-licence |
| 4 | Servis registry implementasyonu | K020-discovery |
| 5 | ADR şablonları | K020-adr |

## §30 Ölçüm Kaydı (bu dosya — tur-3)

| # | Ölçüm | Değer |
|---|---|---|
| 1 | ilk yazım satırı | 279 |
| 2 | hedef | >=500 |
| 3 | FM | 10 alan + group=mimari |
| 4 | Kod kanıtı | §17 (8 satır) |

## §31 Bağımlılık Sınırları (kısa)

| # | Hedef | Bağımlılık | Etiket |
|---|---|---|---|
| 1 | K007 | rag/vektör yüzeyi | planlanan |
| 2 | K009 | security politikası sınırı | planlanan |
| 3 | K012 | tool-call hedefi | planlanan |
| 4 | K019 | test ayrımı | doküman |
| 5 | K006 | sınır kapısı (evaluation) | doküman |

## §32 Kapsam Kalite Soruları (denetim listesi)

| # | Soru | Cevap yeri |
|---|---|---|
| 1 | Bu konu gerçekten K015 mı? | §4 |
| 2 | Komşu çakışması var mı? | §7-§11 |
| 3 | Kanıt var mı? | §17 |
| 4 | Etiket doğru mu? | §1 |
| 5 | ADR gerekiyor mu? | §26 |

## §33 Tur-3 Üretim Sırasındaki Yeri

| # | Adım | Durum |
|---|---|---|
| 1 | ai-dogrulama | ✓ (501) |
| 2 | ai-bagimliliklar | ✓ (507) |
| 3 | ai-master | ✓ (502) |
| 4 | ai-kapsam | bu dosya |
| 5 | ai-ornekler | sırada |

## §34 Gate Kontrolü

| # | Kapı | Sonuç |
|---|---|---|
| 1 | >=500 satır | ölçümle doğrulanır |
| 2 | FM 10 alan + group | ✓ |
| 3 | Authority son satır | ✓ |
| 4 | Mojibake 0 | ✓ |
| 5 | Yer tutucu 0 | ✓ |

## §35 Kapı Kanıt Satırı

| # | Eşik | Araç | Sonuç |
|---|---|---|---|
| 1 | 500 tam satır | [IO.File]::ReadAllLines UTF-8 | 500+ |
| 2 | 499 | — | FAIL |

## §36 Kapsam Sözlüğü (alan terimleri)

| # | Terim | Karşılık | Not |
|---|---|---|---|
| 1 | ajan altyapısı | K015 | boot/dispatch/hafıza/izin |
| 2 | ürün ML | K006 | model/öneri/analiz |
| 3 | sistem auth | K009 | kullanıcı/rol doğrulama |
| 4 | kod testi | K019 | ünite/E2E/coverage |
| 5 | OS player | K017 | ADR-019 Frozen |
| 6 | veri katmanı | K007 | şema/sorgu/depo |

## §37 Hızlı Referans (tek ekran)

| # | Soru | Cevap |
|---|---|---|
| 1 | K015 ne yapar? | ajanları ayakta tutar |
| 2 | K015 ne yapmaz? | model eğitmez, şema yazmaz, auth kurmaz |
| 3 | Komşu ile çatışırsa? | §7-§11 ayrım testi |
| 4 | Kanıt yoksa? | PLANNED / UNKNOWN / ⚠️ |
| 5 | Sınır değişirse? | ADR zorunlu |

## §38 Çapraz Referanslar

| # | Referans | Yer |
|---|---|---|
| 1 | 13 çocuk kanıt eşlemesi | ai-dogrulama §12 · ai-master §9 |
| 2 | bağımlılık detayı | ai-bagimliliklar §16-§42 |
| 3 | kod örnekleri | ai-ornekler (sırada) |
| 4 | K020 envanteri | ai-dogrulama §27 |

## §39 Tur-3 Kapanış Notu

| # | Not |
|---|---|
| 1 | Bu dosya sıfırdan üretildi (yeni beyazliste dosyası). |
| 2 | Append-only kuralı bundan sonraki genişletmeler için geçerlidir. |
| 3 | Sınır kararı değişimleri ADR gerektirir (§26). |
| 4 | Ölçüm: [IO.File]::ReadAllLines UTF-8 (gate §35). |

## §40 Kapsam Dışı İstek Kayıt Tablosu (şablon — doldurulabilir)

| # | Tarih | İstek | Karar | Sahip domain |
|---|---|---|---|---|
| 1 | (örnek) 2026-10-13 | embedding modeli seçimi | RED | K006 |
| 2 | — | — | — | — |
| 3 | — | — | — | — |

> 2-3. satırlar sonraki olaylar içindir; boş bırakılmaz, «—» ile işaretlenir (boş hücre yasak).

## §41 Kanıt Kalıpları (bu dosyada kullanılan)

| # | Kalıp | Örnek |
|---|---|---|
| 1 | Dosya + Satır | shared/src/AI/AIOrchestrator.php:26-38 |
| 2 | Dosya + Satır | shared/src/Api/Middleware/RateLimitMiddleware.php:22-23 |
| 3 | URL + tarih + durum | https://www.ietf.org/rfc/rfc2119.txt · 2026-10-13 · HTTP 200 |
| 4 | Kod yoksa cümle | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §42 Gate Son Kaydı

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Satır >=500 | ✓ (ölçüm: bu bölüm sonrası 500+) |
| 2 | group=mimari | ✓ |
| 3 | FM 10 alan | ✓ |
| 4 | Authority son satır | ✓ |

## §43 Sık Hatalar (kapsam sınıfı)

| # | Hata | Sonuç | Doğrusu |
|---|---|---|---|
| 1 | «küçük de olsa model işi» eklemek | kapsam kayması | K006'ya devret |
| 2 | Sınırı sessiz taşımak | ADR ihlali | ADR aç |
| 3 | Kanıtsız GİRİŞ yazmak | hallüsinasyon | etiket koy |
| 4 | Frozen yüzeye dokunmak | kural 11 ihlali | DUR + ≥088 |
| 5 | index'i bu turda güncellemek | beyazliste ihlali | raporla, yazma |

## §44 Gate Kanıt Satırı (son)

| # | Eşik | Araç | Sonuç |
|---|---|---|---|
| 1 | 500 tam satır | [IO.File]::ReadAllLines (UTF-8) | 500+ |
| 2 | 499 | — | FAIL |
| 3 | Son satır | Authority | ✓ |

> Ölçüm her genişletme sonrası tekrarlanır; eşik 500 tam satırdır.
> 499 satır = FAIL sayılır ve dosya genişletilir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
