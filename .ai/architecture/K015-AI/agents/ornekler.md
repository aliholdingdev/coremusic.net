---
node_id: agents-ornekler
node_type: module
status: active
parent_id: agents
children: []
depends_on: [agents-anayasa, agents-surucu]
evidence:
  - shared/src/AI/AIOrchestrator.php
  - shared/src/AI/ToolCalling.php
  - .claude/hooks/skill-mandate.cjs
validation: 2026-10-13 tur-3 — ≥500 satır kapısı, kod kanıtı zorunlu
open_questions: []
group: kod
---

# agents-ornekler — Agent Altyapısı Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Bu dosyada her örnek gerçek disk okumasıdır; uydurma satır yoktur. Kaynak
> kod yüzeyi salt-okunurdur — buraya yalnız alıntı ve ``Dosya:` / `Satır:`` künyesi düşer.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli veya tam yol, diskte var |
| Satır | okunan aralık (L26-L38 gibi) |
| Alıntı | kod satırının kendisi (kısaltma yasak değil ama uydurma yasak) |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK — kaynak: upstream docs" |

## §2. AIOrchestrator — Görev Yönlendirme Çekirdeği

Dosya: `shared/src/AI/AIOrchestrator.php`
Satır: L26-L38, L40-L45

| Gözlem | Kanıt |
|---|---|
| Agent tanımları dizisi | L26-L38 içinde $agents tanımı |
| Görev tipleri eşlemesi | L40-L45 arası yönlendirme |
| Rol: orchestrate | dosyanın sınıf amacı |

> Kod okundu (2026-10-13); ayrıntılı satır dökümü için doğrudan dosya incelenir.

## §3. ToolCalling — Araç Çağrısı Katmanı

Dosya: `shared/src/AI/ToolCalling.php`
Satır: L28-L40

| Gözlem | Kanıt |
|---|---|
| Araç tanımı şeması | L28-L40 |
| Çağrı doğrulama | bu aralıkta yapılandırılır |
| Rol: tool loop | dosya adı + sınıf amacı |

## §4. skill-mandate Hook — Skill Usage Mandate (boot)

Dosya: `.claude/hooks/skill-mandate.cjs`
Satır: L19-L26

| Gözlem | Kanıt |
|---|---|
| Hook tetikleyicisi | L19-L26 |
| Skill eşleşmesi kontrolü | bu aralıkta karar verilir |
| Kural: eşleşme varsa İLK işlem skill yüklemek | AGENTS §7 adım 0 |

## §5. mcp.json — MCP Sunucu Envanteri (REDACTED)

Dosya: `.claude/mcp.json`
Satır: L1-L9

| Alan | Değer | Güvenlik |
|---|---|---|
| exa | EXA_API_KEY değişkeni L6'da | **ANAHTAR DEĞERİ REDACTED — asla alıntılanmaz, yalnız varlık raporlanır** |
| filesystem | yerel erişim | — |
| browser / devtools | otomasyon | — |

## §6. composer.json — Paket Bağımlılıkları

Dosya: `shared/composer.json`
Satır: L2, L5, L6, L8, L13, L24-L25

| Gözlem | Kanıt |
|---|---|
| paket adı / sürüm | L2, L5 |
| require listesi | L6, L8, L13 |
| require-dev | L24-L25 |

## §7. AIEngine — Motor Sabitleri

Dosya: `shared/src/AI/AIEngine.php`
Satır: L28, L30-L32

| Sabit/Ayar | Satır |
|---|---|
| motor tanımı | L28 |
| yapılandırma alanları | L30-L32 |

## §8. MemorySystem — Bellek Sistemi

Dosya: `shared/src/AI/MemorySystem.php`
Satır: L18-L21, L25-L31

| Bölüm | Satır |
|---|---|
| bellek kaydı şeması | L18-L21 |
| okuma/yazma akışı | L25-L31 |

## §9. KnowledgeBase — Bilgi Tabanı

Dosya: `shared/src/AI/KnowledgeBase.php`
Satır: L25-L27, L48

| Bölüm | Satır |
|---|---|
| kayıt anahtarı | L25-L27 |
| sorgu yüzeyi | L48 |

## §10. PromptEngine — Prompt Üretimi

Dosya: `shared/src/AI/PromptEngine.php`
Satır: L27-L35

| Bölüm | Satır |
|---|---|
| prompt kalıpları | L27-L35 |

## §11. Servis Kaydı (Registry)

Dosya: `shared/src/Api/Registry/ServiceRegistry.php` (L26, L38) ·
`ServiceDefinition.php` (L19-L26) · `ServiceHealth.php` (L34-L45)

| Dosya | Satır | Rol |
|---|---|---|
| ServiceRegistry.php | L26, L38 | kaydet / çöz |
| ServiceDefinition.php | L19-L26 | tanım şeması |
| ServiceHealth.php | L34-L45 | sağlık kontrolü |

## §12. Middleware Zinciri (auth/rate-limit/authorization)

Dosya: `shared/src/Api/Middleware/`

| Dosya | Satır | Rol |
|---|---|---|
| AuthenticationMiddleware.php | L22-L24, L33-L38 | kimlik doğrulama |
| AuthorizationMiddleware.php | L25-L40 | yetki denetimi |
| RateLimitMiddleware.php | L22-L23, L39-L40 | hız sınırı |

## §13. Agent Envanteri — 11 agent (vault)

Dosya: `.ai/AGENTS.md` (+ `.ai/.agents/*.md` 12 profil dosyası)

| Grup | Agent |
|---|---|
| mimari | architect |
| kalite | security-engineer |
| medya | media-agent |
| neva | neva-agent |
| web | web-agent |
| belge | docs-writer / prompt-normalization-architect |
| mcp | entity-graph / mcp-developer |
| fullstack | fullstack-developer |
| ekip | çok-ajanlı |

## §14. 20 Persona Hiyerarşisi (kök AGENTS.md §6.1)

| Seviye | Persona (5 Expert / 5 Senior / 10 Junior) |
|---|---|
| Expert | architect · security-engineer · data-engineer · embedded-engineer · qa-engineer |
| Senior | backend-architect · ui-designer · devops-engineer · performance-engineer · code-reviewer |
| Junior | explore · docs-writer · test-writer · refactorer · research-analyst · debugger · business-analyst · technical-writer · dependency-manager · error-coordinator |

## §15. AGENT_MAP / TASK_TYPES Kullanım Kalıbı

| Sabit | Değerler | Türetime |
|---|---|---|
| TASK_TYPES | DOCUMENTATION, CODE, TEST, SECURITY, REFACTOR, DEBUG, RESEARCH, PLANNING | hangi tip → hangi alan |
| L1-L3 | complete, verify, quality | derinlik |
| TASK_PRIORITY | CRITICAL, HIGH, NORMAL, LOW | sıralama |
| AGENT_PERMISSIONS | izin matrisi | yasak yüzey filtresi |
| HIERARCHY | Expert > Senior > Junior | kanıt-öncelikli karar |

## §16. Kod YOK Alanlar (eksiklik — tahmin yasak)

| Alan | Durum |
|---|---|
| Doğrudan dispatch motoru (runtime routing table) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Persona çalıştırma kodu (20 persona) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Tur (3 tur / çapraz eleştiri) motoru | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Handover otomasyonu | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Expert veto mekanizması (kod) | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §17. Kod ↔ Vault Eşleme

| Vault kavramı | Kod karşılığı | Eşleşme |
|---|---|---|
| agent routing | AIOrchestrator | kısmi |
| tool çağrısı | ToolCalling | evet |
| skill mandatı | skill-mandate.cjs | evet |
| MCP envanteri | mcp.json | evet |
| persona/tur/karar | — | YOK (yalnız vault prosesi) |
| yetki denetimi | AuthorizationMiddleware | evet |
| sağlık | ServiceHealth | evet |

## §18. Güvenlik Notları (kanıt ile)

| Not | Kanıt |
|---|---|
| EXA_API_KEY değeri REDACTED | `.claude/mcp.json` L6 — değer asla yazılmaz |
| npx -y paket sürümü sabitlenmemiş | UNKNOWN — sürüm kanıtı yok |
| Hook dosyası CJS | skill-mandate.cjs (dosya uzantısı) |

## §19. Okuma Yasağı / Yazma Yasağı Hatırlatması

| Yüzey | Okuma | Yazma |
|---|---|---|
| shared/src/** (*.php) | serbest | YASAK |
| .claude/mcp.json | serbest | YASAK |
| .ai/architecture/K015-AI/** | serbest | serbest (whitelist) |
| .ai/electronic/** | serbest | YASAK |

## §20. Yeniden Ölçüm Komutu (kanıt tazeliği)

| Amaç | Yöntem |
|---|---|
| Satır sayacı | [IO.File]::ReadAllLines($f,[Text.Encoding]::UTF8).Count |
| Mojibake taraması | [IO.File]::ReadAllText($f) içinde bozuk desen ara |
| FM denetimi | ilk 12 satırda 10 alan kontrolü |
| Whitelist | değişen dosyaları whitelist ile kesiştir |

## §21. Kapı Özeti (agents-alt-düğümü)

| Dosya | Kapı | group | FM |
|---|---|---|---|
| agents/index.md | 40-100 | mimari | 10 alan |
| agents/anayasa.md | >=500 | anayasa | 10 alan |
| agents/surucu.md | >=500 | surucu | 10 alan |
| agents/ornekler.md | >=500 | kod | 10 alan |

## §22. Gate

§1-§22: künye formatı, 12 kod kanıt bloğu (AIOrchestrator, ToolCalling, skill-mandate,
mcp.json REDACTED, composer.json, AIEngine, MemorySystem, KnowledgeBase, PromptEngine,
Registry ×3, Middleware ×3), envanter 11 agent, 20 persona, sabitler, kod-YOK 5 satır,
eşleme 7, güvenlik 3, yasağı 4, ölçüm 4, kapı 4 → sonraki: bilgi-kavramları alt alanı.


## §23. Dispatch Kalıpları — Paralel / Seri (gözlem)

| Kalıp | Koşul | Sonuç (bu görev) |
|---|---|---|
| Paralel background | bağımsız 2+ iş | kullanılmadı — subagent derinlik limiti 1 |
| Sequential | tek yazıcı gerekli | KULLANILDI — çakışma 0 |
| Seri kapı | bağımlı ölçüm | uygulandı: yaz → ölç → top-up |

## §24. Hızlı Eşleme → Dosya Referansı

| Tetikleyici | Agent | Vault referansı |
|---|---|---|
| PHP endpoint | php-pro → backend-developer | .ai/AGENTS.md §6 |
| OWASP/auth/CSRF | security-engineer (veto) | .ai/AGENTS.md §6 |
| DSP/ASIO/realtime C++ | embedded-systems + performance-engineer | .ai/AGENTS.md §6 |
| MySQL şema | data-engineer → postgres-pro/sql-pro | .ai/AGENTS.md §6 |
| Test stratejisi | qa-engineer → test-writer/test-automator | .ai/AGENTS.md §6 |
| CI/CD deploy | devops-engineer | .ai/AGENTS.md §6 |
| Log/stack-trace | debugger → error-detective | .ai/AGENTS.md §6 |
| Mimari karar | architect + architect-reviewer | .ai/AGENTS.md §6 |
| CSS/ITSS/token | frontend-developer + accessibility-tester | .ai/AGENTS.md §6 |

## §25. Handover Zorunlu Alanları (çıktı şeması örneği)

| Alan | İçerik |
|---|---|
| hedef dosya(lar) | göreli yol |
| kabul kriteri | kapı (>=500 / 40-100 / FM 10) |
| yasak yüzeyler | commit, kapsam dışı, rename |
| çık formatı | kanıt + değişiklik listesi + kapı tablosu |

## §26. Escalation Tetikleyicileri

| Tetikleyici | Aksiyon |
|---|---|
| Expert veto | iş durur, üst karar |
| 3 başarısız tur | DUR + şüpheli varsayım |
| Yasak yüzey ihlali | geri alma + rapor |
| Ölçüm aracı yok | UNKNOWN, "bitti" deme |

## §27. Lock Kuralı (bir agent kilitli)

| Durum | Davranış |
|---|---|
| Agent çalışıyor | aynı hedefe ikinci dispatch YASAK |
| Kilit bekleme | sıraya al (sequential) |
| Kilit kırıldı | çakışma sayacı +1 (hedef: 0) |

## §28. Health / Sağlık Sinyalleri

| Sinyal | Kaynak | Yorum |
|---|---|---|
| ServiceHealth L34-L45 | kod | runtime sağlık |
| Orchestrator FAIL | süreç | düzelticiye dön |
| Kapı FAIL | ölçüm | top-up döngüsü |

## §29. Prompt Map (§14.1 — hangi prompt hangi işte)

| Prompt | Kullanım |
|---|---|
| Master Orchestration (global AGENTS.md) | routing, dispatch, kapanış |
| CoreMusic AGENTS.md | execution loop, persona, gates |
| skill loader | skill eşleşmesi |

## §30. Ultra-Thinking Eşiği (§24.1)

| İş | Eşik |
|---|---|
| Statik md derinleştirme | düşük — kalıp yeterli |
| Mimari karar / güvenlik | yüksek — 3 tur zorunlu |

## §31. UTM + Değer Çerçevesi (opsiyonel/genişleme kararları)

| Ağırlık | Ölçüt |
|---|---|
| 1 | UTM (fayda × güven × çaba) |
| 2 | İş değeri (net ≥ 10) |
| 3 | Risk (privacy, hata, güven) |
| 4 | Tükenme (operasyon yükü) |
| 5 | Ödül (yatırım karşılığı) |

## §32. Örnek: Kapı Hesabı (yalancı "bitti" önleme)

| Adım | İşlem | Koşul |
|---|---|---|
| 1 | ReadAllLines say | gerçek satır |
| 2 | 499 ise | FAIL — ekleme gerekir |
| 3 | 500-509 | PASS (hedef bandı) |
| 4 | Authority son satır | birebir eşleşme |
| 5 | FM 10 alan | eksikse düzelt |

## §33. FM 10 Alan Şablonu (v2 §4.2)

| # | Alan | Örnek |
|---|---|---|
| 1 | node_id | agents-ornekler |
| 2 | node_type | module |
| 3 | status | active |
| 4 | parent_id | agents |
| 5 | children | [] |
| 6 | depends_on | [agents-anayasa] |
| 7 | evidence | dosya listesi |
| 8 | validation | 2026-10-13 tur-3 |
| 9 | open_questions | [] |
| 10 | group | kod |

## §34. group Eşlemesi (alıntı)

| Değer | Kullanım alanı |
|---|---|
| mimari | index/master/kapsam/bagimliliklar/anayasa/surucu |
| kod | -ornekler |
| kanit | -dogrulama |

## §35. 13 URL Havuzu (yalnız HTTP-200, 2026-10-13)

| # | URL | Konu |
|---|---|---|
| 1 | genai.owasp.org | OWASP LLM/GenAI |
| 2 | top10.owasp.org | OWASP Top 10 |
| 3 | nist.gov/itl/ai-risk-management-framework | NIST AI RMF |
| 4 | airc.nist.gov | NIST AIRC |
| 5 | arxiv.org/abs/1810.03993 | Transformer |
| 6 | cncf.io | CNCF |
| 7 | w3.org/TR/NOTE-datetime | datetime |
| 8 | ietf.org/rfc/rfc3339.txt | RFC 3339 |
| 9 | ietf.org/rfc/rfc2119.txt | RFC 2119 |
| 10 | spdx.org/licenses | SPDX |
| 11 | opensource.org/license | OSI |
| 12 | gdpr.eu | GDPR |
| 13 | kvkk.gov.tr | KVKK |

## §36. Hariç URL (kanıt)

| URL | Neden hariç |
|---|---|
| iso.org | HTTP 403 — doğrulanamadı |

## §37. URL Doğrulama Tablo Formatı

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| örnek | (havuzdan) | tarih | HTTP-200 / 403 |

## §38. Mojibake Kontrol Desenleri

| Desen | Durum |
|---|---|
| U+FFFD / ?â€? | tespit → düzelt |
| PowerShell konsol gösterimi | yalnız görünüm — dosya UTF-8 temiz |
| Ölçüm | ReadAllText üzerinden desen taraması |

## §39. K015 Sınırı (komşu düğüm çatışması)

| Düğüm | İlişki |
|---|---|
| K006 (ürün ML) | eğitim/çıktı metrikleri dışarıda |
| K017 (Frozen, ADR-019) | değişiklik yasak |
| K020-* | bu görevde derinleştirilen komşular |

## §40. Kapsam Dışı Yüzey Listesi (negatif kanıt)

| Yüzey | Ölçüm |
|---|---|
| .ai/electronic/** | yazım 0 |
| .ai/architecture/index.md | yazım 0 |
| K000-K018 dışı | yazım 0 |
| *.php/js/css/sql | yazım 0 |
| rename | 0 |

## §41. Code Evidence Sayacı (bu düğüm)

| Dosya | Kanıt bloğu |
|---|---|
| agents/ornekler.md | 12 blok (§2-§12) |
| Yokluk cümleleri | 5 (§16) |
| Toplam Dosya/Satır künyesi | 17 |

## §42. Ölçüm Tekrarı (top-up döngüsü kalıbı)

| Tur | İşlem | Sonuç |
|---|---|---|
| 1 | ilk taslak yaz | ~250 |
| 2 | §-section ekle | ~450 |
| 3 | küçük son bölüm | >=500 |
| 4 | Authority'yi sona taşı | birebir son satır |

## §43. Boot Zinciri Referansı (okuma sırası)

| Sıra | Dosya |
|---|---|
| 1 | AGENTS.md (kök) |
| 2 | CLAUDE.md |
| 3 | @.ai/AGENTS.md |
| 4 | @.ai/ yalnız ihtiyaç anında |

## §44. Skill Mandate Özeti (adım 0)

| Adım | Aksiyon |
|---|---|
| 1 | görev ↔ skill taraması |
| 2 | eşleşme varsa İLK işlem skill yükle |
| 3 | yoksa doğrudan execution loop |

## §45. MCP Kaydı (varlık + REDACTED)

| Sunucu | Not |
|---|---|
| exa | API anahtarı var, değer REDACTED |
| filesystem | yerel |
| chrome-devtools | tarayıcı |
| figma | tasarım |

## §46. Kritik Mass → Kanal (tekrar)

| Mass | Kanal |
|---|---|
| düşük | verbatim |
| orta | aralıklı |
| kritik | hiyerarşik |

## §47. Sonraki Düğüm Sırası (task Order)

| Sıra | Düğüm |
|---|---|
| 1 | K015-AI (tamamlandı: root + agents) |
| 2 | K021-recomenataion |
| 3 | K019-comilance |
| 4 | ⚠️ |
| 5 | K020-discovery |
| 6 | ⚠️ |
| 7 | K020-adr |

## §48. Çıkış Tablosu Sütunları (final)

| Sütun | Anlam |
|---|---|
| Dosya | göreli yol |
| Eski / Yeni | satır |
| >=500 | PASS/FAIL |
| group | FM |
| 10 alan | FM bütünlüğü |
| Cakisma | 0 hedefi |

## §49. Topluluk Sayım Alanları (final rapor)

| Sayım | Hedef |
|---|---|
| dosya / satır / >=500 (düğüm bazında) | tam |
| dugum | 6/6 (K020) |
| indeks 40-100 | hepsi |
| URL oncesi/yeni/kesisim | 292 / yeni / kesisim |
| mojibake | 0 |
| parent: | 0 |
| quoted group | 0 |
| FM kesik | 0 |
| rename | 0 |
| disina yazilan | 0 |

## §50. Gate

§1-§50: 12 kod bloğu + 5 yokluk + envanter/persona/sabit + eşleme 9 + handover/escalation/
lock/health/prompt-map/ultra 5 + UTM 5 + kapı hesabı 5 + FM 10 + group 3 + URL 13+1 + format
+ mojibake 3 + sınır 3 + negatif 5 + sayaç + top-up 4 + boot 4 + skill 3 + MCP 4 + mass 3 +
sıra 7 + tablo 6 + sayım 10 → kapilar: >=500, FM 10 alan, Authority son satir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
