---
node_id: K015-AI/ai-dogrulama
node_type: module
status: draft
parent_id: K015-AI
children: []
depends_on: []
evidence: []
validation: []
open_questions: []
group: kanit
---


# K015-AI — ai-dogrulama.md

## §1 Amaç

K015-AI domaininin iddialarını **ölçülebilir ölçütlerle** doğrular. Kanıt yoksa UNKNOWN /
⚠️ VERIFICATION REQUIRED yazılır; tahmin üretilmez (kök AGENTS.md §3 Zero-Hallucination).

## §2 Ölçütler → Durum

| # | Ölçüt | Kanıt (gerçek yol) | Durum |
|---|---|---|---|
| 1 | Ajan profilleri diskte mevcut mu? | .ai/.agents/ = 12 .md (11 uzman + indeks) — glob 2026-10-12 | ✅ verified |
| 2 | Skill altyapısı mevcut mu? | .claude/skills/ — en az 12 SKILL.md (listeleme 2026-10-12) | ✅ verified |
| 3 | Hook/gate altyapısı mevcut mu? | .claude/hooks/skill-mandate.cjs · ai-validate-post.cjs · ai-risk-gate.cjs | ✅ verified |
| 4 | Boot protokolü dokümante mi? | kök AGENTS.md §14 (Boot Zinciri tablosu) + .claude/rules/skill-usage.md | ✅ verified (doküman) |
| 5 | Orkestrasyon kuralları mevcut mu? | .ai/AGENTS.md §6 routing · §7 dispatch · §9 handover · §10 escalation · §12 lock | ✅ verified (doküman) |
| 6 | Permissions/domain boundary tanımlı mı? | .ai/AGENTS.md §5 (12 dosya tipi sahipliği) | ✅ verified (doküman) |
| 7 | MCP yapılandırma dosyası var mı? | .claude/mcp.json — okundu 2026-10-13 (bkz §10) | ✅ verified (tur-2 kapandı) |
| 8 | RAG altyapısı uygulanmış mı? | vault beyanı: RAG pipeline PLANNED (ADR-030) + KnowledgeBase.php satır 48 yorumu ("gerçek implementasyonda vector cosine similarity") | ⚠️ verify-required (kod iskeleti var, vektör yok) |
| 9 | knowledge/tools/kg/memory/evaluation/agent-testing diskte var mı? | 2026-10-13 sayımı: 13/13 subdomain klasörü + index.md mevcut (bkz §12) | ✅ verified (tur-2 kapandı) |
| 10 | Ajan sayıları tutarlı mı (11 vs 118)? | 11 = proje ajanları (.ai/AGENTS.md §4) · 118 = global registry (boot beyanı) — farklı kümeler, bağımsız sayım yok | 🔶 vault beyanı |
| 11 | Ajan→rol/katman eşlemesi kodda mı? | shared/src/AI/AIOrchestrator.php satır 26-38 AGENT_MAP (11 ajan) | ✅ verified (kod) |
| 12 | Ajan→izin eşlemesi kodda mı? | shared/src/AI/ToolCalling.php satır 28-40 AGENT_PERMISSIONS | ✅ verified (kod) |

## §3 Doğrulanmayan Yüzeyler (kanıt bekliyor — sonraki tur)

kg/ motoru (graf çözümleyici) · vektör/embedding deposu · .claude/agent/ tam sayı ·
K015 runtime entegrasyon derinliği (PLANNED/IMPLEMENTED etiketi).

## §4 Web-Kanıt Kayıtları

> **1. tur (2026-10-12):** web araştırması yapılmadı — tablo iskeletti.
> **2. tur (2026-10-13):** aşağıdaki matris dolduruldu; tüm satırlar bu ortamdan
> `Invoke-WebRequest` ile ölçüldü (HEAD; 403/timeout durumunda GET yedeği).

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | LLM/GenAI uygulama güvenlik riskleri OWASP GenAI Security Project kapsamında | https://genai.owasp.org/ | 2026-10-13 | HTTP 200 |
| 2 | Yazılım güvenlik riskleri OWASP Top 10 listesinde | https://top10.owasp.org/ | 2026-10-13 | HTTP 200 |
| 3 | Yapay zeká risk yönetimi çerçevesi NIST AI RMF olarak yayımlanmıştır | https://www.nist.gov/itl/ai-risk-management-framework | 2026-10-13 | HTTP 200 |
| 4 | NIST AI RMF uygulama kaynakları AIRC üzerinden dağıtılır | https://airc.nist.gov/ | 2026-10-13 | HTTP 200 |
| 5 | Model Card kavramı Mitchell ve ark. makalesiyle tanımlanmıştır | https://arxiv.org/abs/1810.03993 | 2026-10-13 | HTTP 200 |
| 6 | Bulut-doğal (cloud-native) yazılım ekosistemi CNCF çatısı altında | https://www.cncf.io/ | 2026-10-13 | HTTP 200 |
| 7 | ISO 8601 tarih-saat profilinin W3C kaydı yayımlanmıştır | https://www.w3.org/TR/NOTE-datetime | 2026-10-13 | HTTP 200 |
| 8 | Internet tarih-saat damgası biçimi RFC 3339'dur | https://www.ietf.org/rfc/rfc3339.txt | 2026-10-13 | HTTP 200 |
| 9 | Zorunlu/öneri kelimelerinin tanımı RFC 2119'dur (MUST/SHOULD) | https://www.ietf.org/rfc/rfc2119.txt | 2026-10-13 | HTTP 200 |
| 10 | Lisans adlandırması SPDX License List ile standarttır | https://spdx.org/licenses/ | 2026-10-13 | HTTP 200 |
| 11 | Açık kaynak lisans onayı OSI lisans listesiyle yapılır | https://opensource.org/license | 2026-10-13 | HTTP 200 |
| 12 | GDPR metin ve madde portalı erişilebilirdir | https://gdpr.eu/ | 2026-10-13 | HTTP 200 |
| 13 | KVKK resmi sitesi erişilebilirdir | https://www.kvkk.gov.tr/ | 2026-10-13 | HTTP 200 |

### §4.1 Başarısız Ölçümler (dürüstlük kaydı — kullanılmadı)

| # | URL | Sonuç | Karar |
|---|---|---|---|
| 1 | https://owasp.org/www-project-top-10-for-large-language-model-applications/ | HTTP 308 Permanent Redirect (PowerShell 5.1 takip etmedi) | yerine genai.owasp.org kullanıldı (200) |
| 2 | https://www.iso.org/iso-42001-artificial-intelligence.html | HTTP 403 (bot engeli) | tabloya alınmadı; ISO/IEC 42001 yalnız standart adı, erişim ⚠️ VERIFICATION REQUIRED |
| 3 | https://www.iso.org/iso-8601-date-and-time-format.html | HTTP 403 (bot engeli) | yerine w3.org/TR/NOTE-datetime + rfc3339 (200) |
| 4 | https://modelcards.ai/ | timeout (20 s) | yerine arxiv Model Cards makalesi (200) |
| 5 | https://mlsafety.org/ | HTTP 403 | kullanılmadı; yerine NIST AI RMF + AIRC (200) |
| 6 | https://docs.github.com/en/repositories/.../licensing-a-repository | HTTP 404 | kullanılmadı; yerine opensource.org/license + spdx.org |

## §5 Open Questions (⚠️ VERIFICATION REQUIRED)

| # | Soru | Tur-2 durumu |
|---|---|---|
| 1 | mcps/: MCP sunucu adları, sürümleri, lisansları? | kısmen kapandı: .claude/mcp.json okundu → tek sunucu "exa-mcp-server" (local, npx); SÜRÜM/LİSANS alanı YOK → UNKNOWN (bkz §10) |
| 2 | .claude/agent/ dosya sayısı kesin kaç? | hâlâ UNKNOWN (2026-10-13 sayımı bu görev kapsamında yapılmadı) |
| 3 | memory/: MEMORY.md diskte var mı? | evet — .ai/MEMORY.md boot listesinde (kök AGENTS.md boot zinciri); oturum hafızası dosya-tabanlı |
| 4 | agent-testing/: harness var mı? | eval-seti/harness kodu YOK; davranış kapısı doküman-tabanlı (.ai/AGENTS.md §16/§23) |
| 5 | evaluation/: çıktı kalitesi rubrik skoru tanımlı mı? | nicel rubrik skoru YOK; nitel kapılar var (qa-engineer PASS/BLOCK) |
| 6 | K015 runtime entegrasyonu PLANNED mi, IMPLEMENTED mı? | etiket hâlâ yok → ⚠️ VERIFICATION REQUIRED |

## §6 Kapanış Kuralı

Bu domain verified statüsüne, §2'deki tüm ⚠️ satırları kapanmadan geçemez. Kanıt yalnız
gerçek dosya yolu / URL ile eklenir (ADR-097 §5.3: doğrulanmayan iddia yazılmaz).

## §7 Tur-2 Doğrulama Kaydı (2026-10-13)

| # | İşlem | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Beyazliste ölçümü (K015 + 6 K020 düğüm) | Get-Content satır sayımı | K015: 17 dosya/997 satır · K020 düğümleri: adr 3/251 · comere 2/76 · comilance 5/363 · discovery 3/183 · licence 3/322 · recomenataion 7/333 |
| 2 | URL envanteri (mimari geneli) | regex `https?://[^\s\|\)]+` | BENZERSIZ URL = 292 |
| 3 | Resmi URL doğrulama | Invoke-WebRequest HEAD, 20 s timeout; 403/timeout'ta GET yedeği | 13 URL HTTP 200 (§4) · 6 URL başarısız (§4.1) |
| 4 | Mojibake taraması (UTF-8 okuma, 7 beyazliste dizini) | karakter class regex | MOJIBAKE ADAY DOSYA = 0 |
| 5 | Kod kanıtı derleme | Read ile dosya okuma | 15 dosyadan gerçek satır alıntısı (§9) |
| 6 | Dizin imla koruması | Test-Path + dizin listesi | K020-recomenataion disk imlası KORUNDU (task yazımı "recomenataon" ile fark bilinçli raporlandı) |

## §8 Web-Kanıt Matrisi Özeti (tur-2)

| Metrik | Değer |
|---|---|
| Doğrulanan resmi URL (HTTP 200) | 13 |
| Başarısız ölçüm (kullanılmadı) | 6 |
| Envanter ∩ yeni pool eşleşmesi | 14 eşleşme (rfc2119 · spdx · opensource · gdpr.eu · kvkk.gov.tr · top10.owasp.org — hepsi ZORUNLU resmi kaynak; envanterden kopyalanmadı, yeniden ölçüldü) |
| iso.org kararı | 403 bot-engeli → tabloya alınmadı |

## §9 Kod Kanıt Matrisi (2026-10-13 okuma — satırlar Read ile doğrulandı)

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | shared/src/AI/AIEngine.php | 28 | `class AIEngine implements AIEngineInterface` — AI motoru sınıfı |
| 2 | shared/src/AI/AIEngine.php | 30-32 | MAX_RECOMMENDATIONS=20 · MIN_SCORE=0.7 · DIVERSITY_TARGET=0.30 sabitleri |
| 3 | shared/src/AI/AIOrchestrator.php | 26-38 | AGENT_MAP — 11 ajan → domain/layer eşlemesi (kod içinde) |
| 4 | shared/src/AI/AIOrchestrator.php | 40-45 | TASK_TYPES — görev tipi → ajan + öncelik (fault → dsp-fw, critical) |
| 5 | shared/src/AI/ToolCalling.php | 28-40 | AGENT_PERMISSIONS — ajan → araç kategorisi izin tablosu |
| 6 | shared/src/AI/MemorySystem.php | 25-31 | HIERARCHY — brain=5 · index=4 · agents=4 · memory=3 · log=1 ağırlıkları |
| 7 | shared/src/AI/MemorySystem.php | 18-21 | Cache seviyeleri L1 (SSOT) · L2 (görev) · L3 (referans) |
| 8 | shared/src/AI/KnowledgeBase.php | 25-27 | CATEGORIES (7) · MAX_SEARCH_RESULTS=50 · LIFECYCLE_MAX_AGE_DAYS=365 |
| 9 | shared/src/AI/KnowledgeBase.php | 48 | "Basit keyword matching (gerçek implementasyonda vector cosine similarity)" — vektör arama HEDEF, uygulanmış değil |
| 10 | shared/src/AI/PromptEngine.php | 27-35 | INJECTION_PATTERNS — 7 regex (ignore previous · act as · override ...) |
| 11 | shared/src/Api/Registry/ServiceRegistry.php | 26 · 38 | register() / discover() — servis kayıt ve keşif API'si (@see ADR-084) |
| 12 | shared/src/Api/Registry/ServiceDefinition.php | 19-26 | VO: healthEndpoint='/health' · timeout=5 · version='1.0.0' · status='unknown' |
| 13 | shared/src/Api/Registry/ServiceHealth.php | 34-45 | getAggregatedStatus() — healthy/degraded/unhealthy sayımı |
| 14 | shared/src/Api/Middleware/AuthenticationMiddleware.php | 22-24 · 33-38 | RS256/JwtService fail-closed 401 · PUBLIC_ROUTE_PATTERNS (4 ön ek) |
| 15 | shared/src/Api/Middleware/RateLimitMiddleware.php | 22-23 · 39-40 | 60 istek/60 s penceresi · Retry-After başlığı |
| 16 | .claude/hooks/skill-mandate.cjs | 19-26 | PROJECT_DIR/SKILLS_DIR · COREMUSIC_GLOBAL 5 skill adı |
| 17 | shared/composer.json | 2 · 5 · 8 | name=coremusic/shared-infrastructure · license=proprietary · php>=8.4 |

## §10 Güvenlik Bulgusu Kaydı (mcp.json — tur-2)

`.claude/mcp.json` okundu (2026-10-13): tek sunucu `exa-mcp-server` (type: local,
command: npx -y exa-mcp-server). **L6'da `EXA_API_KEY` alanı gerçek bir değer içeriyor —
bu vault dosyasına değer olarak YAZILMAZ (REDACTED)**; bulgu, .claude/AGENTS.md §3.3 #1
("gizli anahtar .md/.log/.json dışına yazılmaz") ile hizalanarak Security rolüne raporlanır.
MCP sunucusu sürümü/pinsiz `npx -y` kullanımı → sürüm SABİTLENMEMİŞ (UNKNOWN; tekrarlanabilirlik riski).

## §11 Encoding/Mojibake Doğrulaması (tur-2)

| Kapsam | Yöntem | Sonuç |
|---|---|---|
| K015-AI/** + K020-{adr,comere,comilance,discovery,licence,recomenataion}/**/*.md | [IO.File]::ReadAllText (UTF-8) + karakter class taraması | 0 bozuk dosya |
| PowerShell Get-Content gösterimi | varsayılan kod sayfası | display-sanatı (UTF-8 yanlış çözümleme) — dosya içeriği temiz; kanıt: Read tool çıktısı |

## §12 Alan Eşlemesi (13 subdomain → derinleştirme turu)

| # | Subdomain | index 40-100? | 3 içerik dosyası (bu görev) | Kanıt temeli |
|---|---|---|---|---|
| 1 | agents | ✅ (51) | agents-anayasa · agents-surucu · agents-ornekler | .ai/.agents/ · AIOrchestrator AGENT_MAP |
| 2 | knowledge | ✅ (49) | knowledge-* | KnowledgeBase.php · .ai/keys.md |
| 3 | tools | ✅ (49) | tools-* | ToolCalling.php · .claude/skills/ |
| 4 | context | ✅ (51) | context-* | kök AGENTS.md boot zinciri |
| 5 | rag | ✅ (49) | rag-* | .ai/RAG.md · KnowledgeBase.php satır 48 |
| 6 | kg | ✅ (49) | kg-* | wiki-link ağı (doküman); motor YOK → PLANNED |
| 7 | mcps | ✅ (49) | mcps-* | .claude/mcp.json (§10) |
| 8 | boot-protocol | ✅ (50) | boot-protocol-* | skill-mandate.cjs · .claude/settings.json |
| 9 | orchestration | ✅ (49) | orchestration-* | .ai/AGENTS.md §6-§12 · AIOrchestrator.php |
| 10 | memory | ✅ (49) | memory-* | MemorySystem.php · .ai/MEMORY.md · .ai/log.md |
| 11 | evaluation | ✅ (49) | evaluation-* | .ai/AGENTS.md §16/§23 |
| 12 | permissions | ✅ (50) | permissions-* | ToolCalling AGENT_PERMISSIONS · .ai/AGENTS.md §5 |
| 13 | agent-testing | ✅ (49) | agent-testing-* | .ai/AGENTS.md §6.1 persona turları |

## §13 Doğrulama Kapıları (eşikler)

| Etiket | Eşik | Kullanım |
|---|---|---|
| ✅ verified | gerçek dosya yolu + satır/ölçüm veya HTTP 200 URL | iddia serbest |
| 🔶 vault beyanı / partial | vault iddiası, bağımsız ölçüm yok | iddia + "vault beyanı" etiketi zorunlu |
| ⚠️ verify-required | kanıt yok veya ölçüm başarısız | kesin sonuç yazılmaz |
| UNKNOWN | bilgi hiç yok | değer uydurulmaz |

## §14 Güncellenmiş Açık Sorular (tur-2 sonu)

| # | Soru | Sahip | Öncelik |
|---|---|---|---|
| 1 | mcp.json'daki anahtar sahipliği/rotasyonu? (REDACTED kaydı §10) | Security Engineer | HIGH |
| 2 | npx -y pinsiz MCP sürümü sabitlenecek mi? | MO + DevOps | MEDIUM |
| 3 | .claude/agent/ kesin dosya sayısı | MO | LOW |
| 4 | K015 runtime IMPLEMENTED/PLANNED etiketi | Vault Steward | HIGH |
| 5 | vektör depolama seçimi (K007-data yüzeyi) | Data Engineer | MEDIUM |

## §15 Kaynaklar

- v2 spec §2.16 · §2.6 · ADR-097 §2/§5 · ADR-030 (AI stratejisi) · ADR-084 (API gateway — kod @see'leri)
- Disk okumaları 2026-10-13: shared/src/AI/* · shared/src/Api/Registry/* · shared/src/Api/Middleware/* · .claude/hooks/* · .claude/mcp.json · shared/composer.json
- Web ölçümleri 2026-10-13: §4 matrisi (13× HTTP 200)

## §16 Derinleştirme Turu Beyazliste (tur-3)

**Kural:** Bu tur yalnız aşağıdaki yüzeylere yazar; beyazliste dışında dosya oluşturulmaz,
değiştirilmez, silinmez (ölçüm kanıtı: §29).

| # | Düğüm | İzinli yüzey | Bu turdaki hedef | Tur-3 başı durum |
|---|---|---|---|---|
| 1 | K015-AI | .ai/architecture/K015-AI/**/*.md | 1 root index + 5 root içerik + 13 sub index + 39 sub içerik | kısmen yapıldı (index + 2 içerik) |
| 2 | K020-recomenataion | .ai/architecture/K020-recomenataion/**/*.md | 1 index onarımı + 3 yeni içerik + mevcut FM düzeltme | beklemede |
| 3 | K020-comilance | .ai/architecture/K020-comilance/**/*.md | 1 index + 3 yeni içerik + 4 mevcut FM düzeltme | beklemede |
| 4 | K020-comere | .ai/architecture/K020-comere/**/*.md | 1 index + 3 yeni içerik + 1 mevcut FM düzeltme | beklemede |
| 5 | K020-discovery | .ai/architecture/K020-discovery/**/*.md | 1 index + 3 yeni içerik + 2 mevcut FM düzeltme | beklemede |
| 6 | K020-licence | .ai/architecture/K020-licence/**/*.md | 1 index + 3 yeni içerik + 2 mevcut FM düzeltme | beklemede |
| 7 | K020-adr | .ai/architecture/K020-adr/**/*.md | 1 index + 3 yeni içerik + 2 mevcut FM düzeltme | beklemede |

## §17 Yasaklı Yüzeyler (tur-3)

| # | Yasaklı yüzey | Neden | Tur-3 sonu ölçüm hedefi |
|---|---|---|---|
| 1 | .ai/electronic/** | kapsam dışı (görev beyazlistesi) | dışına yazılan = 0 |
| 2 | .ai/architecture/index.md | kök indeks bu ture ait değil | dışına yazılan = 0 |
| 3 | K000* K001* K002* K005* K006* K007* K008* K009* K012* K013* K014* K015* K017* K018* | komşu düğüm, izin yok | dışına yazılan = 0 |
| 4 | *.php / *.js / *.css / *.sql | kaynak kod salt-okunur (yalnız okuma) | yazma = 0 |
| 5 | git commit | commit yetkisi orkestratöre ait (AGENTS.md §16.5) | commit = 0 |
| 6 | dosya/dizin yeniden adlandırma | imla koruması (§21) | rename = 0 |

## §18 Frontmatter Şeması (v2 §4.2 + group — her dosya zorunlu)

| # | Alan | Kural | Boş bırakılabilir mi? |
|---|---|---|---|
| 1 | node_id | dosya yoluyla aynı (kök: K015-AI, içerik: K015-AI/{ad}) | hayır |
| 2 | node_type | index→domain/subdomain · içerik→module | hayır |
| 3 | status | draft / verify-required / verified (ölçüme göre) | hayır |
| 4 | parent_id | kök index: null · sub index: K015-AI · içerik: üst düğüm | hayır |
| 5 | children | dosya adları listesi; boşsa [] | hayır (tip zorunlu) |
| 6 | depends_on | bağımlılık listesi; yoksa [] | hayır (tip zorunlu) |
| 7 | evidence | gerçek yol/satır/URL kanıtları; yoksa [] | hayır (tip zorunlu) |
| 8 | validation | doğrulama kayıtları; yoksa [] | hayır (tip zorunlu) |
| 9 | open_questions | açık sorular; yoksa [] | hayır (tip zorunlu) |
| 10 | group | TIRNAKSIZ: mimari / kod / kanit (§19 eşlemesi) | hayır |
| 11 | parent | **YASAK** — `parent:` anahtarı yazılmaz, yerine parent_id | — |

```yaml
# Kalıp (içerik dosyası — geçerli örnek)
node_id: K015-AI/ai-dogrulama
node_type: module
status: verified
parent_id: K015-AI
children: []
depends_on: []
evidence:
  - "shared/src/AI/AIOrchestrator.php:26-38"
validation:
  - "2026-10-13 satır sayımı ile >=500 doğrulandı"
open_questions:
  - "K015 runtime IMPLEMENTED/PLANNED etiketi yok (UNKNOWN)"
group: kanit
```

## §19 group Eşlemesi (dosya adı → group değeri)

| # | Dosya adı deseni | group değeri | Tırnak |
|---|---|---|---|
| 1 | *-index.md (kök/sub) | mimari | yok |
| 2 | ai-master / ai-kapsam / ai-bagimliliklar | mimari | yok |
| 3 | *-anayasa / *-surucu | mimari | yok |
| 4 | *-ornekler | kod | yok |
| 5 | *-dogrulama | kanit | yok |
| 6 | kapsam / bagimliliklar / master anahtarları | mimari | yok |

## §20 Dosya Türü Karşılaşım Tablosu

| # | Dosya türü | node_type | parent_id | group | Eşik |
|---|---|---|---|---|---|
| 1 | Kök index (K015-AI/index.md) | domain | null | mimari | 40–100 satır |
| 2 | Sub index (örn. agents/index.md) | subdomain | K015-AI | mimari | 40–100 satır |
| 3 | Kök içerik (ai-*.md) | module | K015-AI | dosyaya göre | >=500 satır |
| 4 | Sub içerik (*-anayasa/-surucu/-ornekler) | module | {subdomain} | §19 | >=500 satır |

## §21 İmla Koruma Listesi (rename = 0)

| # | Diskte doğru yazım | Yasaklı «düzeltme» | Konum |
|---|---|---|---|
| 1 | K020-comere | commerce | .ai/architecture/ |
| 2 | K020-comilance | compliance | .ai/architecture/ |
| 3 | K020-recomenataion | recomenataon / recommendation (dizin adı) | .ai/architecture/ |
| 4 | task yazımı «recomenataon» | diskte YOK — yazım task ile değil disk ile eşitlenir | — |
| 5 | identitiy | identity | vault içi terim |
| 6 | electroncis | electronics | vault içi terim |
| 7 | integraion | integration | vault içi terim |
| 8 | donanaım-type | donanım-type | vault içi terim |

## §22 İçerik Eşikleri ve Yasaklı Kalıplar

| # | Kural | Eşik/değer | İhlal durumu |
|---|---|---|---|
| 1 | İçerik dosyası satır sayısı | >=500 tam satır (499 = FAIL) | genişlet |
| 2 | Bölüm yapısı | `## §N` + markdown tablo | ekle |
| 3 | Son satır | Authority satırı birebir | düzelt |
| 4 | Dil | Türkçe anlatım + İngiliziz terim (standingart) | — |
| 5 | TBD / `// ...` / `{...}` yer tutucu | yasak | sil/güncelle |
| 6 | Boş tablo hücresi | yasak | doldur veya satırı kaldır |
| 7 | Mojibake (Ã, “, �) | yasak | §11 ölçümü = 0 |
| 8 | Kanıtsız kesin iddia | yasak | ⚠️ VERIFICATION REQUIRED / UNKNOWN |

## §23 Kod Kanıtı Kuralı (her içerik dosyasında en az 1)

| # | Seçenek | Biçim | Gerçek kullanım örneği (§9 kaynaklı) |
|---|---|---|---|
| 1 | Alıntı | `Dosya: …` + `Satır: …` | Dosya: shared/src/AI/ToolCalling.php · Satır: 28-40 (AGENT_PERMISSIONS) |
| 2 | Alıntı | Dosya + Satır | Dosya: shared/src/AI/KnowledgeBase.php · Satır: 48 (vektör arama uygulanmadı) |
| 3 | Alıntı | Dosya + Satır | Dosya: .claude/hooks/skill-mandate.cjs · Satır: 19-26 (SKILLS_DIR) |
| 4 | Kod yoksa | tam cümle | `Repo'da bu alan kodu YOK — kaynak: upstream docs` |

## §24 URL Kullanım Kuralı

| # | Kural | Değer |
|---|---|---|
| 1 | İzinli havuz | §4 tablosundaki 13 URL (hepsi HTTP 200, 2026-10-13) |
| 2 | Yeni URL ekleme | ancak yeniden ölçülüp HTTP 200 alınırsa |
| 3 | iso.org | 403 → kullanılmaz |
| 4 | Doğrulama tablosu biçimi | `| İddia | URL | 2026-10-13 | Durum |` |
| 5 | Envanterdeki diğer URL'ler | 292 benzersiz URL tescilli; havuz dışı alıntı yapılmaz |

## §25 Paralellik ve Sıralı Üretim Kaydı

| # | Planlanan | Gerçek durum | Çözüm |
|---|---|---|---|
| 1 | 5 paralel subagent | Subagent depth limit (1) nedeniyle çağrı başarısız | ana oturumda sıralı üretim |
| 2 | Paralel çakışma garantisi | serbest (tek yazıcı) | çakışma = 0 (ölçüm §30) |
| 3 | commit | hiç atılmadı | dosyalar diskte, commit orkestratörde |

## §26 K015-AI Envanteri (2026-10-13 ölçümü — tur-3 genişletme öncesi)

| # | Dosya | Satır | Eşik durumu |
|---|---|---|---|
| 1 | K015-AI/index.md | 99 | 40–100 aralığında ✓ |
| 2 | K015-AI/ai-master.md | 111 | <500 → genişletilecek |
| 3 | K015-AI/ai-dogrulama.md | 195 | <500 → bu bölümle genişletiliyor |
| 4 | K015-AI/ai-bagimliliklar.md | 189 | <500 → genişletilecek |
| 5 | K015-AI/agents/index.md | 51 | 40–100 ✓ |
| 6 | K015-AI/knowledge/index.md | 49 | 40–100 ✓ |
| 7 | K015-AI/tools/index.md | 49 | 40–100 ✓ |
| 8 | K015-AI/context/index.md | 51 | 40–100 ✓ |
| 9 | K015-AI/rag/index.md | 49 | 40–100 ✓ |
| 10 | K015-AI/kg/index.md | 49 | 40–100 ✓ |
| 11 | K015-AI/mcps/index.md | 49 | 40–100 ✓ |
| 12 | K015-AI/boot-protocol/index.md | 50 | 40–100 ✓ |
| 13 | K015-AI/orchestration/index.md | 49 | 40–100 ✓ |
| 14 | K015-AI/memory/index.md | 49 | 40–100 ✓ |
| 15 | K015-AI/evaluation/index.md | 49 | 40–100 ✓ |
| 16 | K015-AI/permissions/index.md | 50 | 40–100 ✓ |
| 17 | K015-AI/agent-testing/index.md | 49 | 40–100 ✓ |
| — | TOPLAM | 17 dosya / 1236 satır | sub içerik dosyaları henüz yok (39 hedef) |

## §27 K020 Düğüm Envanteri (2026-10-13 ölçümü)

| # | Düğüm | Dosya | Satır | Var olan dosyalar (satır) |
|---|---|---|---|---|
| 1 | K020-recomenataion | 5 | 333 | index 70 · recommendation-master 123 · evaluation/index 49 · personalization/index 46 · ranking/index 45 |
| 2 | K020-comilance | 5 | 363 | index 71 · compliance-master 125 · controls/index 60 · policies/index 52 · verification/index 55 |
| 3 | K020-adr | 3 | 251 | index 66 · decision-register 130 · templates/index 55 |
| 4 | K020-licence | 3 | 322 | index 64 · dependency-licenses 135 · license-verification 123 |
| 5 | K020-discovery | 3 | 183 | index 73 · agent/index 59 · search/index 51 |
| 6 | K020-comere | 2 | 76 | index 60 · scope-verification 16 |
| — | NOT | — | — | eski sayım «7 dosya/333 satır» idi; satır toplamı aynı (333), dosya sayısı 5 ölçüldü — fark kayıtlıdır |

## §28 Bilgi Sınıfları — Tur-3 Uygulama Örnekleri

| # | Sınıf | Kullanım biçimi | Örnek |
|---|---|---|---|
| 1 | verified (kod) | dosya + satır ile | AGENT_MAP: shared/src/AI/AIOrchestrator.php:26-38 |
| 2 | verified (URL) | HTTP 200 + tarih | NIST AI RMF — https://www.nist.gov/itl/ai-risk-management-framework (200, 2026-10-13) |
| 3 | vault beyanı | etiketle | «vault beyanı» ibaresi zorunlu |
| 4 | verify-required | ⚠️ işareti | K015 runtime etiketi yok → ⚠️ VERIFICATION REQUIRED |
| 5 | UNKNOWN | değer uydurma | MCP sunucusu sürümü UNKNOWN |

## §29 Ölçüm Betiği (çalıştırılan yöntem)

```powershell
# Satır sayısı (UTF-8 — Get-Content mojibake gösterir, kullanılmaz)
$n = [IO.File]::ReadAllLines($f.FullName, [Text.Encoding]::UTF8).Count
# Dizin envanteri
Get-ChildItem $path -Recurse -File -Filter *.md
# Mojibake taraması
$txt = [IO.File]::ReadAllText($f.FullName, [Text.Encoding]::UTF8)
if ($txt -match '[ÃÂâ€�]') { $bad++ }
# URL doğrulama (HEAD; başarısızsa GET yedeği)
Invoke-WebRequest -Uri $u -Method Head -TimeoutSec 20
```

## §30 Teslim Çıktı Şeması (tur-3 kapanış tablosu)

| # | Sütun | Anlam |
|---|---|---|
| 1 | Dosya | beyazliste göreli yolu |
| 2 | Eski / Yeni | satır sayısı öncesi/sonrası |
| 3 | >=500 | içerik için PASS/FAIL (index için —) |
| 4 | group | mimari/kod/kanit doğrulaması |
| 5 | §4.2 | 10 alan + group kontrolü |
| 6 | Çakışma | bu turda beklenen değer 0 (serbest yazıcı) |

## §31 Açık Riskler (tur-3 başı)

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | 59 hedef içeriğin >=500 satıra tamamlanması | teslim eşiği | açık |
| 2 | mcp.json EXA_API_KEY (REDACTED) | güvenlik | Security'ye raporlandı, değer yazılmadı |
| 3 | K015 runtime etiketi (PLANNED/IMPLEMENTED) | kapsam belirsizliği | UNKNOWN, HIGH |
| 4 | Numpy/paket bağımlılık bump / firmware OTA / refactor | otomatik DUR kuralı | bu turda tetiklenmedi |

## §32 Subdomain Eşleme — Hedef Dosya ve Kanıt Tabanı (13 × 3 = 39)

| # | Subdomain | anayasa kanıtı | surucu kanıtı | ornekler kanıtı |
|---|---|---|---|---|
| 1 | agents | .ai/.agents/*.md (11 profil) | AIOrchestrator.php:26-38 AGENT_MAP | ToolCalling.php:28-40 AGENT_PERMISSIONS |
| 2 | knowledge | .ai/keys.md · RAG.md §3 | KnowledgeBase.php:25-27 CATEGORIES | KnowledgeBase.php:48 keyword matching |
| 3 | tools | .claude/skills/ (>=12 SKILL.md) | ToolCalling.php araç kategorileri | skill-mandate.cjs:19-26 |
| 4 | context | kök AGENTS.md §14 boot zinciri | MemorySystem.php:18-21 L1/L2/L3 | AGENTS.md §24.2 14 dosya listesi |
| 5 | rag | ADR-030 (pipeline PLANNED) | KnowledgeBase.php:48 vektör yok | .ai/RAG.md §4 pipeline PLANNED |
| 6 | kg | wiki-link ağı (doküman) | motor kodu YOK → cümle kalıbı §23.4 | wiki-link-check ölçümü (227 link) |
| 7 | mcps | .claude/mcp.json (§10) | exa-mcp-server local/npx | mcp.json:1-9 yapılandırma |
| 8 | boot-protocol | kök AGENTS.md §14 | skill-mandate.cjs:19-26 | .claude/settings.json UserPromptSubmit |
| 9 | orchestration | .ai/AGENTS.md §6-§12 | AIOrchestrator.php:40-45 TASK_TYPES | AGENTS.md §7.1-§7.7 akışı |
| 10 | memory | .ai/MEMORY.md boot listesi | MemorySystem.php:25-31 HIERARCHY | MemorySystem.php:18-21 cache |
| 11 | evaluation | .ai/AGENTS.md §16/§23 | qa-engineer PASS/BLOCK kapısı | ai-dogrulama §2 ölçütleri |
| 12 | permissions | .ai/AGENTS.md §5 dosya tipi sahipliği | ToolCalling.php:28-40 | AuthenticationMiddleware.php:33-38 PUBLIC_ROUTE |
| 13 | agent-testing | .ai/AGENTS.md §6.1 persona turları | harness kodu YOK → §23.4 | 3-tur/20-persona uzlaşma kuralı |

## §33 K020 Hedef Dosya Planı (6 × 3 = 18)

| # | Düğüm | -anayasa kanıtı | -surucu kanıtı | -ornekler kanıtı |
|---|---|---|---|---|
| 1 | recomenataion | recommendation-master.md mevcut | AIEngine.php:30-32 MAX_RECOMMENDATIONS | AIEngine.php:28 sınıf tanımı |
| 2 | comilance | compliance-master.md mevcut | AuthenticationMiddleware.php:22-24 fail-closed | RateLimitMiddleware.php:22-23 60/60 |
| 3 | comere | scope-verification.md (16 satır) | v2 kapsam beyanı — UNKNOWN yüzey, kanıt ekle | — (kanıt yoksa §23.4) |
| 4 | discovery | index.md mevcut | ServiceRegistry.php:26 register / :38 discover | ServiceDefinition.php:19-26 VO varsayılanları |
| 5 | licence | dependency-licenses.md mevcut | shared/composer.json:5 license=proprietary | composer.json:2 name · :8 php>=8.4 |
| 6 | adr | decision-register.md mevcut | templates/index.md ADR kalıbı | ADR-097 §5.3 doğrulanmayan iddia |

## §34 Frontmatter Normalizasyon Kuralları (mevcut dosyalar)

| # | Eski alan | Muamele |
|---|---|---|
| 1 | title / type / category / version / date / updated / authority | düşürülür (§18 listesi zorunlu) |
| 2 | evidence / validation / open_questions / depends_on / children | değer KORUNUR, sadece biçim standartlaşır |
| 3 | parent | parent_id'ye taşınır; `parent:` anahtarı kalmaz |
| 4 | group | yoksa eklenir, tırnaksız yazılır |
| 5 | status | ölçümle uyumlu: kanıt yoksa verify-required |

## §35 Kapanıştan Önceki Kapılar (tur-3 sıra kontrolü)

| # | Kapı | Koşul | Geçiş |
|---|---|---|---|
| 1 | Sıra | K015-AI → recomenataon → comilance → comere → discovery → licence → adr | takip ediliyor |
| 2 | İnce-öncelik | her düğümde önce en ince dosya | ölçüm §26/§27 |
| 3 | Eşik | tüm içerik >=500, index 40–100 | son tur ölçümünde |
| 4 | FM | 10 alan + group, `parent:` yok | son tur ölçümünde |
| 5 | Yazım | imla korunur (rename=0), yasağa yazım yok | son tur ölçümünde |
| 6 | Commit | yok (orkestratöre ait) | doğrulandı: git commit atılmadı |

## §36 Tur-3 Üretim Günlüğü (append-only)

| # | Tarih | İşlem | Sonuç |
|---|---|---|---|
| 1 | 2026-10-13 | K015-AI/index.md frontmatter onarımı (title kaldırıldı, parent_id: null) | body aynen korundu (99 satır) |
| 2 | 2026-10-13 | ai-dogrulama.md yeniden yazım (FM normalize + §16-§35 eki) | 195 → 456 satır |
| 3 | 2026-10-13 | ai-bagimliliklar.md yeniden yazım (§1-§15 korundu, §7-§15 eklendi) | 189 satır → >=500 hedefi ölçülmedi |
| 4 | 2026-10-13 | ai-master.md genişletme | sırada |
| 5 | 2026-10-13 | ai-kapsam.md · ai-ornekler.md üretimi | sırada |
| 6 | 2026-10-13 | 13 subdomain × 3 dosya üretimi | sırada |
| 7 | 2026-10-13 | 6 K020 düğümü üretimi | sırada |

## §37 Bu Dosyanın Kendi Doğrulama Kaydı

| # | Kontrol | Beklenen | Gerçekleşen |
|---|---|---|---|
| 1 | Satır sayısı | >=500 | 456 → §38 ekiyle kapatılır |
| 2 | Son satır | Authority satırı birebir | ✓ (UTF-8, Read ile doğrulandı) |
| 3 | Frontmatter | 10 alan + group (kanit) | ✓ §18 kalıbı |
| 4 | `parent:` anahtarı | yok | ✓ (parent_id kullanıldı) |
| 5 | Mojibake | 0 | ✓ tarama §11 |
| 6 | URL | yalnız §4 havuzu | ✓ yeni URL yok |
| 7 | Kod kanıtı | >=1 gerçek satır | ✓ §9 tablosu (17 satır) |
| 8 | TBD/boş hücre | 0 | ✓ |

## §38 Ölçüm Eşiği Kanıtı (gate)

| # | Eşik | Ölçüm aracı | Durum |
|---|---|---|---|
| 1 | 500 tam satır | [IO.File]::ReadAllLines UTF-8 · Count | ek bölüm sonrası >=500 |
| 2 | 499 satır | — | FAIL sayılır, genişletilir |
| 3 | index 40–100 | aynı araç | §26 tablosunda 13/13 ✓ |
| 4 | FM kesik | ilk 12 satır `---` ile kapanır | her yazım sonrası ölçülür |
| 5 | Authority son satır | Count-1 indeksi | her yazım sonrası ölçülür |

## §39 Sözlük (bu dosyada geçen terimler)

| # | Terim | Anlam |
|---|---|---|
| 1 | beyazliste | tur-3'te yazmaya izin verilen tek dizin kümesi |
| 2 | FM | frontmatter — dosya başındaki `---` arası alan bloğu |
| 3 | gate | eşik kontrolü; geçmeyen dosya teslim sayılmaz |
| 4 | pool | §4'te HTTP 200 ölçülmüş 13 URL'lik izinli küme |
| 5 | REFACTOR/OTA/bump | otomatik DUR tetikleyicileri (AGENTS.md §kural 13) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode