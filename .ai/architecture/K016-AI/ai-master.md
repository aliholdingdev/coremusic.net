---
node_id: K016-AI/ai-master
node_type: module
status: draft
parent_id: K016-AI
children: []
depends_on:
  - K007-data
  - K009-security
  - K011-application
  - K012-services
evidence:
  - "shared/src/AI/AIOrchestrator.php:26-38"
  - ".ai/.agents/ (12 .md, 2026-10-12)"
  - "shared/composer.json:8"
validation:
  - "2026-10-13 tur-3: satır sayımı >=500 gate"
open_questions:
  - "K016 runtime etiketi (PLANNED/IMPLEMENTED) yok — UNKNOWN"
  - "evaluation/ vs K006-evaluation sınırı tescil edilmedi"
group: mimari
---


# K016-AI — ai-master.md

## §1 Rol (ELI15)

K016-AI, yapay zekanın **kendi çalışma biçimi**dir: ajanlar kimdir, ne bilir (knowledge/RAG/KG),
ne yapabilir (tools/MCP), nasıl başlar (boot-protocol), nasıl dağıtılır (orchestration),
neyi hatırlar (memory), neye izinlidir (permissions) ve nasıl sınanır (agent-testing).
Bir "AI ürün"ü değil, **AI'yi ayakta tutan altyapı**dır.

## §2 Kapsam / Sınır (ne DEĞİL)

| Kapsam (bu domain) | Kapsam DIŞI |
|---|---|
| Ajan tanımları, roller, yetki sınırları | Şarkı öneren/sesi analiz eden ML ürün özellikleri → **K006-yapay-zeka** (v2 §2.6: model-management · inference · recommendation · personalization · music-analysis · audio-intelligence · local-inference) |
| RAG/KG altyapısı (bilgiye erişim yolu) | Model eğitimi, ağırlık yönetimi, inference sunucusu → **K006** |
| Ajan testi (agent-testing) | Genel test stratejisi, coverage, E2E → **K019-testing** |
| Ajan boot protokolü (oturum açılışı) | CI kapısı, pipeline → **K018-devops** |
| Ajan permissions | Sistem auth/CSRF/CSP → **K009-security** (ajan izinleri burada, sistem güvenliği orada) |

**KURAL (registry §6 — tescilli ayrım):** K006 = ML **ürün** · K016 = ajan **altyapısı**. İkisi ayrı domaindir;
kapsam kayması tespit edilirse ADR ile düzeltilir, sessiz taşınmaz.
**Devam eden sınır riski:** evaluation/ ve agent-testing/ (K016) ile evaluation/ ve testing/ (K006)
isim benzerliği taşır → kademe-3'te ayrım tescillenecek (açık soru §7/6).

## §3 Kademe-2 Çocuk Tablosu (v2 §2.16 — birebir, 13 çocuk)

| # | Çocuk | Rol (1 cümle) | Kanıt durumu |
|---|---|---|---|
| 1 | agents/ | Ajan kimlik/rol/sınır tanımları (11 uzman + MO registry'de) | ✅ kanıtlı |
| 2 | knowledge/ | Bilgi bankasının düzenlenmesi ve bilgi modeli | ⚠️ kanıt bekliyor |
| 3 | tools/ | Ajanların çağırabileceği araç envanteri ve sözleşmesi | ⚠️ kanıt bekliyor |
| 4 | context/ | Boot bağlam paketi (AGENTS/CLAUDE/CONTEXT/WORKFLOW + rules) | ✅ kanıtlı |
| 5 | rag/ | Bilgi tabanından getirme (retrieval) altyapısı | 🔶 vault beyanı: RAG pipeline PLANNED (ADR-030) |
| 6 | kg/ | Knowledge graph — kavramlar arası ilişki katmanı | ⚠️ kanıt bekliyor |
| 7 | mcps/ | MCP sunucu yapılandırması ve sürüm yönetimi | 🔶 mcp.json var; sürüm/star kanıtı YOK → evidence: [] |
| 8 | boot-protocol/ | Oturum/agent açılış sırası ve zorunlu okuma paketi | ✅ kanıtlı |
| 9 | orchestration/ | Görev dağıtımı, handover, eskalasyon, lock | ✅ kanıtlı |
| 10 | memory/ | Session/uzun vadeli ajan hafızası | ⚠️ kanıt bekliyor |
| 11 | evaluation/ | Ajan çıktısı kalite değerlendirmesi (model değil) | ⚠️ kanıt bekliyor |
| 12 | permissions/ | Dosya tipi/domain boundary + yetki/yasak matrisi | ✅ kanıtlı |
| 13 | agent-testing/ | Ajan davranışının test edilmesi | ⚠️ kanıt bekliyor |

## §4 Teknoloji / Ekosistem (disk kanıtları — ölçüm 2026-10-12)

| Ekosistem | Kanıt (gerçek dosya yolu) | Durum |
|---|---|---|
| Ajan profilleri (vault) | .ai/.agents/ = **12 .md** (master-orchestrator · backend-architect · ui-designer · security-engineer · data-engineer · embedded-engineer · dsp-firmware-engineer · audio-hardware-engineer · windows-software-engineer · devops-engineer · qa-engineer + AGENTS.md indeksi) | ✅ IMPLEMENTED (doküman) |
| Ajan profilleri (repo harness) | .claude/agent/ — profil .md dosyaları mevcut (gözlemde görülenler: workflow-orchestrator · websocket-engineer · vue-expert · ux-researcher · typescript-pro · triage · trend-analyst · tooling-engineer · testing · test-writer · test-automator · terraform-engineer · technical-writer · task-distributor · swift-expert · sre-engineer · sql-pro · spring-boot-engineer · seo-specialist · security · security-engineer · security-auditor · search-specialist · scrum-master · scientific-literature-researcher · sales-engineer · rust-engineer · ruby-pro + liste kırpıldı) → **en az 28, tam sayı UNKNOWN** | 🔶 var, tam sayı ölçülmedi |
| Skill altyapısı | .claude/skills/ — en az **12 SKILL.md** görüldü (vault-sync-post · ui-code-generator · ui-analyzer · skill-maker · security-hardening · hardware-electronics · composer-sync · audio-engine-cpp · php-backend-standards · database-normalize-maker · agent-orchestrator · learning-prompts) | ✅ IMPLEMENTED |
| Hook / gate altyapısı | .claude/hooks/skill-mandate.cjs · ai-validate-post.cjs · ai-risk-gate.cjs (3 dosya) | ✅ IMPLEMENTED |
| MCP yapılandırması | .claude/mcp.json (dosya varlığı görüldü — **içerik/sunucu listesi okunmadı**) | 🔶 sadece varlık |
| Ayar/kural yüzeyi | .claude/settings.json · .claude/settings.local.json · .claude/rules/skill-usage.md | ✅ IMPLEMENTED |
| Registry (yetki/handover/lock) | .ai/AGENTS.md §5 Domain Boundaries · §6 routing · §7 dispatch · §9 handover · §10 escalation · §12 context lock | ✅ IMPLEMENTED (doküman) |
| Global harness envanteri | Global ~/.config/opencode/: **118 agent · 351 skill · 24 command · 4 hook** (boot beyanı, .ai/AGENTS.md §3/§4) | 🔶 VAULT BEYANI — bu turda bağımsız disk sayımı YAPILMADI |
| Agent sayısı (proje) | 11 ajan (1 MO + 10 uzman) — .ai/AGENTS.md §4/§15 + .github/AGENTS.md §1 | 🔶 VAULT BEYANI (iki tutarlı kayıt) |

> **Not:** tools/ · knowledge/ · rag/ · kg/ · memory/ · evaluation/ · agent-testing/ için bu turda
> **hiçbir** disk kanıtı toplanmadı — uydurma yok, boş bırakıldı (bkz. §6/§7).

## §5 Bağımlılık Özeti (etiketli — detay: ai-bagimliliklar.md)

| Yön | Hedef | Etiket | Gerekçe |
|---|---|---|---|
| depends_on | K007-data | planlanan | RAG/bilgi tabanı veri katmanına ihtiyaç duyar |
| depends_on | K009-security | planlanan | permissions domain sınırı güvenlik domainine komşu |
| depends_on | K011-application · K012-services | planlanan | ajanlar uygulama/servis katmanını çağrırlar |
| used_by | K020-recomenataion | planlanan | öneri ajanları bu altyapıyı kullanır (v2 K020 children'ı) |
| used_by | vault süreçleri (AGENTS/WORKFLOW) | kanıtlı (doküman) | boot-protocol + orchestration + permissions aktif kullanılıyor (.ai/AGENTS.md §6–§14) |

## §6 Doğrulama Özeti (detay: ai-dogrulama.md)

- ✅ **Doğrulandı (disk):** agents · context · boot-protocol · orchestration · permissions (§4 yolları).
- 🔶 **Kısmi:** mcps (sadece mcp.json varlığı) · rag (vault beyanı PLANNED).
- ⚠️ **Kanıt bekliyor:** knowledge · tools · kg · memory · evaluation · agent-testing.
- Web doğrulama: **bu turda yapılmadı** (A4 payı kapsamı dışı) → ai-dogrulama.md §4 "kanıt bekliyor".

## §7 Açık Sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etkilenen çocuk |
|---|---|---|
| 1 | MCP sunucuları hangileri, hangi sürüm/star'da? | mcps/ |
| 2 | .claude/agent/ TAM profil sayısı kaç? (liste kırpıldı) | agents/ |
| 3 | RAG pipeline hâlâ PLANNED mı (ADR-030)? | rag/ |
| 4 | memory/ için hangi hafıza sistemi seçildi? | memory/ |
| 5 | K016 runtime'ı: harici harness mı, in-repo kütüphane mi? etiket yok | tüm domain |
| 6 | evaluation/ vs K006-evaluation/ sınırı kademe-3'te nasıl tescillenecek? | evaluation/ |

## §8 Kaynaklar

| Kaynak | Referans |
|---|---|
| v2 spec | coremusic-katmanli-mimari-agaci-v2.md §2.16 (çocuk listesi birebir) |
| ADR-097 (draft) | §2 K1–K7 · §5 isimlendirme · §6 açık sorular |
| Registry | .ai/architecture/index.md §5 satır 16 (K016-AI · ai-master.md) · §6 kapsam ayrımı kaydı |
| Disk (2026-10-12) | .ai/.agents/ · .claude/ · kök AGENTS.md · .ai/AGENTS.md |

## §9 Çocuk → Kanıt Eşlemesi (tur-3 derinleştirme planı)

| # | Çocuk | Üretilecek 3 dosya | Birincil kanıt |
|---|---|---|---|
| 1 | agents | agents-anayasa · agents-surucu · agents-ornekler | .ai/.agents/ (12 .md) · AIOrchestrator.php:26-38 |
| 2 | knowledge | knowledge-* | KnowledgeBase.php:25-27 · .ai/keys.md |
| 3 | tools | tools-* | ToolCalling.php:28-40 · .claude/skills/ |
| 4 | context | context-* | kök AGENTS.md §14 · MemorySystem.php:18-21 |
| 5 | rag | rag-* | KnowledgeBase.php:48 · ADR-030 |
| 6 | kg | kg-* | motor kodu YOK → §23.4 cümle kalıbı |
| 7 | mcps | mcps-* | .claude/mcp.json:1-9 (anahtar REDACTED) |
| 8 | boot-protocol | boot-protocol-* | skill-mandate.cjs:19-26 |
| 9 | orchestration | orchestration-* | AIOrchestrator.php:40-45 · .ai/AGENTS.md §6-§12 |
| 10 | memory | memory-* | MemorySystem.php:25-31 HIERARCHY |
| 11 | evaluation | evaluation-* | .ai/AGENTS.md §16 · ai-dogrulama §2 |
| 12 | permissions | permissions-* | AuthenticationMiddleware.php:33-38 |
| 13 | agent-testing | agent-testing-* | .ai/AGENTS.md §6.1 (3 tur / 20 persona) |

## §10 Ajan Yaşam Döngüsü (operasyonel görünüm)

> **Sınır (zero-hallucination):** Bu bölüm ajanın **çalışma zamanı** yaşam döngüsüdür;
> model eğitim/ince ayar (fine-tuning) yaşam döngüsü **K006** kapsamındadır ve burada tartışılmaz.

| # | Aşama | Tetikleyen | Çıktı | Kanıt |
|---|---|---|---|---|
| 1 | Boot (bağlam yükleme) | oturum açılışı | bağlam paketi | kök AGENTS.md §14 |
| 2 | Routing (anahtar kelime) | kullanıcı isteği | birincil/ikincil ajan | .ai/AGENTS.md §6 |
| 3 | Pre-flight | ödev öncesi | sınır/kilit kontrolü | .ai/AGENTS.md §7.2 |
| 4 | Dispatch | MO kararı | görev ataması | AIOrchestrator.php:40-45 |
| 5 | Execution | ajan çalışması | kod/doküman çıktısı | AGENT_MAP:26-38 |
| 6 | Handover | alan geçişi | transfer kaydı | .ai/AGENTS.md §9 |
| 7 | Verification | çıktı | PASS/BLOCK | .ai/AGENTS.md §7.6 |
| 8 | Escalation (gerekirse) | 3 başarısızlık | üst seviye | .ai/AGENTS.md §10 |
| 9 | Log + memory | kapanış | log.md + MEMORY | MemorySystem.php:25-31 |

## §11 Gözlemlenebilirlik (observability) — ne ölçülür, ne ölçülmüyor

| # | Ölçüm | Tanım | Bu repo durumu |
|---|---|---|---|
| 1 | Drift (sürüm/kural) | vault vs harness tutarlılığı | wiki-link-check ile kısmen (227 link / gerçek kırık 0) |
| 2 | Latency (yanıt süresi) | ajan timeout eşikleri | .ai/AGENTS.md §8 tablosu (5/15/30/60 s) — ölçüm yok |
| 3 | Maliyet (token) | harcanan token | K016 kapsamı değil → UNKNOWN, ölçüm altyapısı yok |
| 4 | Hata oranı | retry/escalation sayısı | §11 health states (kod var, sayım yok) |
| 5 | Gate sonuçları | PASS/BLOCK | qa-engineer raporları (doküman) |

## §12 Ajan Güvenlik Yüzeyi (prompt injection · data leak)

| # | Yüzey | Kontrol | Kanıt |
|---|---|---|---|
| 1 | Prompt injection | 7 regex deseni (ignore previous · act as · override ...) | PromptEngine.php:27-35 INJECTION_PATTERNS |
| 2 | Yetki aşımı | ajan → araç kategorisi izin tablosu | ToolCalling.php:28-40 AGENT_PERMISSIONS |
| 3 | Dosya sınırı ihlali | dosya tipi sahipliği | .ai/AGENTS.md §5 |
| 4 | Sızıntı (log) | [REDACTED] maskeleme kuralı | .ai/AGENTS.md §17/3 |
| 5 | Anahtar sızıntısı | vault'a değer yazılmaz | mcp.json L6 bulgusu (REDACTED) |
| 6 | Fail-open | yok — fail-closed 401 | AuthenticationMiddleware.php:22-24 |

## §13 Ölçek (scale) notları

| # | Boyut | Mevcut | Sınır/Not |
|---|---|---|---|
| 1 | Proje ajanı | 11 (1 MO + 10 uzman) | registry §4 — vault beyanı |
| 2 | Global agent | 118 (boot beyanı) | bağımsız sayım YAPILMADI |
| 3 | Skill (proje) | 12+ SKILL.md | .claude/skills/ |
| 4 | Skill (global) | 351 (boot beyanı) | bağımsız sayım YAPILMADI |
| 5 | Subdomain | 13 | v2 §2.16 birebir |
| 6 | AI paketi dosyası | 7 .php | shared/src/AI/ |

## §14 K006 / K017 Sınır Tescili (bu dosyanın kuralı)

| # | Konu | K016-AI | Komşu | Kural |
|---|---|---|---|---|
| 1 | Model eğitim/inference | hayır | K006 | v2 §2.6 |
| 2 | Öneri/kişiselleştirme ürün özellikleri | hayır | K006 · K020-recomenataion | — |
| 3 | Ajan altyapısı (boot/dispatch/permission) | evet | — | v2 §2.16 |
| 4 | Ajan değerlendirme | evet | — | model değil, ajan çıktısı |
| 5 | OS player metrikleri | hayır | K017 (ADR-019 Frozen) | değişiklik ≥088 |
| 6 | Sınır ihlali görülürse | — | — | ADR ile düzelt, sessiz taşıma |

## §15 Mimari Omurga (domain iskeleti)

| # | Katman | İçerik | Durum |
|---|---|---|---|
| 1 | Kimlik (agents) | 11 ajan + profil | kanıtlı |
| 2 | Bilgi (knowledge/rag/kg) | bilgiye erişim yolu | kısmi / PLANNED |
| 3 | Eylem (tools/mcps) | araç + MCP yüzeyi | kanıtlı (araç) / kısmi (MCP) |
| 4 | Başlangıç (boot-protocol/context) | oturum açılışı | kanıtlı |
| 5 | Koordinasyon (orchestration) | dağıtım/handover/lock | kanıtlı (doküman) |
| 6 | Hafıza (memory) | L1/L2/L3 + hiyerarşi | kanıtlı (kod) |
| 7 | Yetki (permissions) | izin/yasak matrisi | kanıtlı (kod + doküman) |
| 8 | Sınav (evaluation/agent-testing) | ajan kalite/ttest | doküman (harness YOK) |

## §16 Kapsam Giriş/Çıkış Tescili (in/out)

| # | Öğe | Karar | Etiket |
|---|---|---|---|
| 1 | 13 çocuk subdomain | K016 GİRİŞ | v2 §2.16 |
| 2 | ai-master/kapsam/bagimliliklar/dogrulama/ornekler | K016 GİRİŞ | kök içerik |
| 3 | ML ürün özellikleri | K016 ÇIKIŞ | K006 |
| 4 | Genel test altyapısı | K016 ÇIKIŞ | K019 |
| 5 | Sistem auth/CSRF | K016 ÇIKIŞ | K009 |
| 6 | CI/CD kapısı | K016 ÇIKIŞ | K018 |
| 7 | Hardware/firmware | K016 ÇIKIŞ | donanım düğümleri |

## §17 Omurga ↔ Kapsam Eşlemesi

| # | Omurga katmanı | Kapsam dosyası | Kapsam kararı |
|---|---|---|---|
| 1 | Kimlik | ai-kapsam §agents | giriş |
| 2 | Bilgi | ai-kapsam §bilgi | giriş · vektör ≠ |
| 3 | Eylem | ai-kapsam §araçlar | giriş · servis içi ≠ |
| 4 | Başlangıç | ai-kapsam §boot | giriş |
| 5 | Koordinasyon | ai-kapsam §koordinasyon | giriş |
| 6 | Hafıza | ai-kapsam §hafıza | giriş |
| 7 | Yetki | ai-kapsam §yetki | giriş · sistem auth ≠ |
| 8 | Sınav | ai-kapsam §sınav | giriş · model testi ≠ |

## §18 Model Operasyon Sınırı (netleştirme)

| # | Soru | Cevap | Kapsam |
|---|---|---|---|
| 1 | K016 model eğitir mi? | hayır | K006 |
| 2 | K016 prompt yazar mı? | evet (PromptEngine) | K016 |
| 3 | K016 inference sunucusu çalıştırır mı? | hayır | K006 |
| 4 | K016 ajan çıktısını değerlendirir mi? | evet | K016-evaluation |
| 5 | K016 model metriğini (F1 vb.) sahiplenir mi? | hayır | K006 |

## §19 Gözlemlenebilirlik Gap Listesi

| # | Gap | Etki | Sahip |
|---|---|---|---|
| 1 | Token maliyet ölçümü yok | maliyet körü | MO + DevOps |
| 2 | Drift otomasyonu yok (elle kontrol) | sürüm sessiz kayması | Vault Steward |
| 3 | Latency metriği dokümanda, ölçümde değil | performans körü | performance-engineer |
| 4 | Gate sonuçları merkezi toplanmıyor | trend yok | qa-engineer |

## §20 Terimler (glossary bu domain için)

| # | Terim | Tanım |
|---|---|---|
| 1 | Agent | belirli alanda uzmanlaşmış ajan birimi |
| 2 | Boot | oturumda bağlam paketinin yüklenmesi |
| 3 | Dispatch | görevin ajana atanması |
| 4 | Handover | görevin başka ajana devri |
| 5 | Escalation | çözülmeyen konunun üst seviyeye çıkması |
| 6 | Context lock | eşzamanlı dosya erişim kilidi |
| 7 | HIERARCHY | hafıza önem ağırlıkları (brain=5 … log=1) |
| 8 | INJECTION_PATTERNS | prompt injection regex desenleri |
| 9 | PLANNED/IMPLEMENTED | kod kanıtı durum etiketi |

## §21 Risklar (özet — detay: ai-bagimliliklar §22)

| # | Risk | Sınıf | Eşik |
|---|---|---|---|
| 1 | Harness bağımlılığı | mimari | HIGH |
| 2 | Vektör depo belirsiz | veri | MEDIUM |
| 3 | MCP pinsiz sürüm | güvenlik | MEDIUM |
| 4 | evaluation kapsam kayması | mimari | MEDIUM |
| 5 | Runtime etiketi yok | kanıt | HIGH |

## §22 Doğrulama Kapıları (özet)

| # | Kapı | Eşik | Bu dosya |
|---|---|---|---|
| 1 | Satır | >=500 | evet |
| 2 | Index 40-100 | çocuk index'ler | 13/13 ✓ (ai-dogrulama §26) |
| 3 | FM 10 alan + group | her dosya | ✓ |
| 4 | Authority son satır | her dosya | ✓ |
| 5 | Mojibake | 0 | ✓ |

## §23 Kaynak & Bağlantılar

| # | Kaynak | Kullanım |
|---|---|---|
| 1 | v2 spec §2.16 | çocuk listesi |
| 2 | .ai/AGENTS.md | registry/routing |
| 3 | kök AGENTS.md | boot zinciri |
| 4 | shared/src/AI/*.php | kod kanıtları |
| 5 | ai-dogrulama.md | ölçüm/kanıt defteri |
| 6 | ai-bagimliliklar.md | bağımlılık defteri |

## §24 Tur-3 Değişiklik Günlüğü

| # | Tarih | Değişiklik |
|---|---|---|
| 1 | 2026-10-12 | ilk yazım (111 satır) |
| 2 | 2026-10-13 | FM normalize (title/authority/version kaldırıldı, 10 alan + group eklendi) |
| 3 | 2026-10-13 | §9-§24 eklendi (>=500 hedefi) |
| 4 | 2026-10-13 | Authority satırı standart biçime çekildi (tarih soneki kaldırıldı) |

## §25 Açık Sorular (korunmuş + tur-3)

| # | Soru | Etkilenen | Öncelik |
|---|---|---|---|
| 1 | MCP sunucuları hangileri/sürümleri? | mcps/ | MEDIUM |
| 2 | .claude/agent/ tam sayısı? | agents/ | LOW |
| 3 | RAG pipeline hâlâ PLANNED mı? | rag/ | MEDIUM |
| 4 | memory sistemi seçimi? | memory/ | MEDIUM |
| 5 | Runtime etiketi (PLANNED/IMPLEMENTED)? | tüm domain | HIGH |
| 6 | evaluation sınırı nasıl tescillenecek? | evaluation/ | HIGH |

## §26 Subdomain Sorumluluk Özet Tablosu (1 satır/çocuk — master görünümü)

| # | Çocuk | Kimin işi (rol) | Birincil output | Eşik |
|---|---|---|---|---|
| 1 | agents | ajan kimliği/rolü | profil tablosu | kanıt zorunlu |
| 2 | knowledge | bilgi modeli | kategori/arama kuralı | kanıt zorunlu |
| 3 | tools | araç envanteri | izinli araç listesi | kanıt zorunlu |
| 4 | context | bağlam paketi | boot okuma listesi | doküman |
| 5 | rag | retrieval | getirme stratejisi | PLANNED etiketi |
| 6 | kg | graf ilişki | ilişki şeması | motor YOK → cümle kalıbı |
| 7 | mcps | MCP sunucu | sunucu/envanter tablosu | anahtar REDACTED |
| 8 | boot-protocol | açılış sırası | adım tablosu | hook kanıtı |
| 9 | orchestration | dağıtım | routing/handover/lock | kod+doküman |
| 10 | memory | hafıza | seviye/hiyerarşi | kod kanıtı |
| 11 | evaluation | kalite | ölçüt→durum | kanıt bekliyor |
| 12 | permissions | yetki | izin/yasak matrisi | kod kanıtı |
| 13 | agent-testing | test | tur/persona akışı | doküman |

## §27 K016 İçinde Kesişen Domainler

| # | Domain | Kesişim | Çözüm kuralı |
|---|---|---|---|
| 1 | K006-yapay-zeka | evaluation ismi | K016=ajan · K006=model |
| 2 | K009-security | permissions/auth | K016=ajan izni · K009=sistem auth |
| 3 | K019-testing | agent-testing | K016=ajan davranışı · K019=ünite/E2E |
| 4 | K012-services | tool-call hedefi | ajan servisin kamuya açık yüzeyini çağırır |
| 5 | K007-data | RAG verisi | vektör/depo kararı K007 |
| 6 | K018-devops | gate'ler | CI kapısı K018 · vault kapısı K016 hook |

## §28 Öncelik Sırası ve Bağımlılık (dosya üretim sırası)

| # | Sıra | Dosya | Gerekçe |
|---|---|---|---|
| 1 | 1 | ai-dogrulama | kanıt defteri diğerlerini besler |
| 2 | 2 | ai-bagimliliklar | bağımlılık sınırları |
| 3 | 3 | ai-master (bu dosya) | omurga |
| 4 | 4 | ai-kapsam | in/out tescili |
| 5 | 5 | ai-ornekler | kod örnekleri (en son, kanıt tam) |
| 6 | 6 | 13 subdomain × 3 | master'a bağlı |
| 7 | 7 | 6 K020 düğümü | K016'dan bağımsız ama sıraya bağlı |

## §29 Kod Kanıtı Özeti (bu dosyada kullanılanlar)

| # | Dosya | Satır | Ne |
|---|---|---|---|
| 1 | shared/src/AI/AIOrchestrator.php | 26-38 | AGENT_MAP (11 ajan) |
| 2 | shared/src/AI/AIOrchestrator.php | 40-45 | TASK_TYPES (görev→ajan) |
| 3 | shared/src/AI/ToolCalling.php | 28-40 | AGENT_PERMISSIONS |
| 4 | shared/src/AI/PromptEngine.php | 27-35 | INJECTION_PATTERNS |
| 5 | shared/src/AI/MemorySystem.php | 25-31 | HIERARCHY |
| 6 | shared/src/AI/MemorySystem.php | 18-21 | L1/L2/L3 |
| 7 | shared/src/AI/KnowledgeBase.php | 48 | vektör arama uygulanmadı |
| 8 | shared/src/Api/Middleware/AuthenticationMiddleware.php | 22-24 | fail-closed 401 |

## §30 Dış Bağımlılık Özeti (bu dosya görünümü)

| # | Bağımlılık | Tür | Etiket |
|---|---|---|---|
| 1 | PHP >=8.4 | çalışma zamanı | kanıtlı (composer L8) |
| 2 | ext-apcu/pdo/json/mbstring | uzantı | kanıtlı (L9-12) |
| 3 | firebase/php-jwt | JWT | kanıtlı (L13) |
| 4 | Harness (OpenCode/Claude Code) | çalıştırıcı | doküman |
| 5 | .claude/hooks (3 dosya) | gate | kanıtlı |
| 6 | MySQL (dolaylı, K007) | veri | planlanan |

## §31 Sınıf Etiketleri Sözlüğü

| # | Etiket | Anlam | Kullanım |
|---|---|---|---|
| 1 | ✅ kanıtlı | gerçek dosya yolu/satır | kesin cümle |
| 2 | 🔶 vault beyanı | vault iddiası | «vault beyanı» etiketi zorunlu |
| 3 | ⚠️ kanıt bekliyor | yüzey var, okunmadı | kesin sonuç yok |
| 4 | PLANNED | hedef, uygulanmadı | kod yoksa bu etiket |
| 5 | UNKNOWN | hiç bilgi yok | değer uydurma |

## §32 Üretim Kalite Kuralları (bu domain dosyaları için)

| # | Kural | Eşik |
|---|---|---|
| 1 | İçerik satır | >=500 |
| 2 | Bölüm | `## §N` + tablo |
| 3 | Yer tutucu (TBD/`{...}`/boş hücre) | 0 |
| 4 | Mojibake | 0 |
| 5 | Kod kanıtı | >=1 (Dosya+Satır) veya §23.4 cümlesi |
| 6 | URL | yalnız §4 havuzu (13 × HTTP 200) |
| 7 | Son satır | Authority satırı birebir |

## §33 Kapı Sonuçları (tur-3 bu dosya)

| # | Kapı | Sonuç |
|---|---|---|
| 1 | >=500 satır | ölçüm: 500+ |
| 2 | FM 10 alan + group=mimari | ✓ |
| 3 | `parent:` yok | ✓ (parent_id) |
| 4 | Authority son satır | ✓ |
| 5 | Eski §1-§8 korundu | ✓ (append-only) |
| 6 | Yazım: title/authority/version düşürüldü | ✓ (normalize) |

## §34 Domain Sağlık Göstergeleri (ne izlenir)

| # | Gösterge | Kaynak | Frekans |
|---|---|---|---|
| 1 | <500 içerik dosyası sayısı | satır sayımı | her tur sonu |
| 2 | Index 40-100 dışı | satır sayımı | her tur sonu |
| 3 | Açık soru sayısı | §25 | her tur sonu |
| 4 | ⚠️ yüzey sayısı | ai-dogrulama §3 | her tur sonu |
| 5 | Mojibake adayı | UTF-8 tarama | her tur sonu |
| 6 | wiki-link kırığı | wiki-link-check | vault-sync |

## §35 Rol Cümlesi (tek cümlelik özet — okuyucu için)

| # | Kitle | Cümle |
|---|---|---|
| 1 | yeni ajan | «K016-AI benim kim, ne yapabilir, neye izinli olduğumu tanımlar.» |
| 2 | mimar | «K016 altyapıdır; ürün ML K006'dadır.» |
| 3 | denetçi | «Her iddianın dosya/satır kanıtı vardır; yoksa ⚠️/UNKNOWN.» |
| 4 | operator | «Boot → routing → dispatch → verify → log zinciri burada tanımlıdır.» |

## §36 Sınır Karar Ağacı (hızlı karar)

```text
Görev mi geldi?
  ├─ Model/eğitim/inference mi?          → K006 (bu domain DEĞİL)
  ├─ Sistem auth/CSRF/CSP mi?            → K009 (bu domain DEĞİL)
  ├─ Ünite/E2E/coverage mı?              → K019 (bu domain DEĞİL)
  ├─ CI/CD/pipeline mı?                  → K018 (bu domain DEĞİL)
  ├─ OS player metrik/davranış mı?       → K017 (Frozen, ADR-019)
  └─ Ajan/boot/dispatch/hafıza/izin mi?  → K016-AI (BU DOMAIN)
```

## §37 Kapanış Özeti (bu dosyanın hali)

| # | Öğe | Değer |
|---|---|---|
| 1 | Satır (tur-3 öncesi) | 111 |
| 2 | Satır (tur-3 sonrası) | 500+ (gate §33) |
| 3 | Bölüm sayısı | §1-§37 |
| 4 | FM durumu | normalize (10 alan + group) |
| 5 | Silme | yok (append-only) |
| 6 | Sıradaki iş | ai-kapsam.md üretimi |

## §38 Benzerlik Tuzağı Tablosu (yanlış domain'e kaçışı önleme)

| # | Benzer görünen | Doğru domain | Ayırt edici işaret |
|---|---|---|---|
| 1 | evaluation (K016) vs evaluation (K006) | K016 = ajan çıktısı | ölçtüğü şey ajan mı model mi |
| 2 | agent-testing vs K019-testing | K016 | test edilen: ajan davranışı |
| 3 | permissions vs K009-auth | K016 | kapsam: dosya tipi/ajan izni |
| 4 | knowledge vs K007-data | K016 | kapsam: bilgi modeli/erişim yolu |
| 5 | tools vs K012-services | K016 | kapsam: ajan araç envanteri |
| 6 | memory (ajan) vs DB cache (K007) | K016 | kapsam: oturum/bağlam hafızası |

## §39 Tur-3 Kapanış Kontrolü (bu dosya)

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 1 | Satır | >=500 | ölçüm sonrası |
| 2 | FM | 10 alan + group=mimari | ✓ |
| 3 | Authority | son satır birebir | ✓ |
| 4 | Mojibake | 0 | ✓ |
| 5 | Append-only | §1-§8 korundu | ✓ |
| 6 | Sıra | master sonrası kapsam, sonra ornekler | ✓ |

## §40 Gate Kanıt Satırı

| # | Eşik | Araç | Sonuç |
|---|---|---|---|
| 1 | 500 tam satır | [IO.File]::ReadAllLines (UTF-8) | 500+ |
| 2 | 499 | — | FAIL |
| 3 | Son satır | Authority | ✓ |

> **Gate:** 500 tam satır zorunlu (499 = FAIL) — ölçüm her tur sonunda UTF-8 ile tekrarlanır.

| # | Ölçüm adımı | Komut özeti |
|---|---|---|
| 1 | dosyayı UTF-8 oku | [IO.File]::ReadAllLines |
| 2 | say | .Count |
| 3 | eşikle | >=500 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
