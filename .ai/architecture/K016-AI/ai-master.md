---
title: "K016-AI — Master (Domain Anayasa)"
node_id: K016-AI
node_type: domain-master
status: draft
authority: "Derived — SSOT: v2 spec §2.16 + ADR-097 (draft)"
updated: 2026-10-12
version: 1.0.0
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

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-12
