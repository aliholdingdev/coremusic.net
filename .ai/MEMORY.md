---
title: "CoreMusic — Memory System Index"
type: system
category: memory-management
date: 2026-08-13
updated: 2026-09-05
status: active
version: 24.4.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/MEMORY.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/WORKFLOW.md · .ai/brain.md · .ai/index.md"
---

# CoreMusic — Memory System Index

**Zorunlu Baglantilar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[brain.md]] · [[keys.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

---

## 1. Amac

CoreMusic bellek sistemi, oturumlar arasi persistent state yonetimini standartlastirir ve vault ile kod arasindaki tutarliligi korur. audit trail ile izlenebilirlik garanti edilir. Bu dosya, tum AI ajanlarinin oturum baslangicinda okumasi gereken 9 zorunlu dosyadan biridir.

---

## 2. Terminoloji

| Terim | Tanim |
|-------|-------|
| Persistent State | Oturumlar kapansa dahi silinmeyen bellek |
| Vault-Sync | Degisikliklerin `.ai/` dosyalarina senkronizasyonu |
| Context Lock | Dosyanin gecici sure dondurulmesi |
| Audit Trail | Tum degisikliklerin timestamp ile loglanmasi |
| SSOT | Single Source of Truth — Tek Dogruluk Kaynagi |
| Memory Hierarchy | Bellek oncelik sirasi: brain > index > AGENTS > MEMORY > log |
| Hard Guardrail | Asilamayan sert mimari kural |
| Zero Code Before Plan | Planlama yapilmadan kod yazma yasagi |
| BCNF | Boyce-Codd Normal Form — 18 BCNF DB icin zorunlu normalizasyon |
| Immutability | ADR 001-037 frozen, degistirilemez |
| Append-Only | Sadece ekleme, gecmis satirlar dokunulmaz |
| Frontmatter | Dosya basligi, 7 zorunlu alan |
| Wiki-Link | `[[dosya/yolu]]` formatinda capraz referans |

---

## 3. Memory Hierarchy

| Dosya | Oncelik | Icerik | Mod | Max Boyut |
|-------|---------|--------|-----|-----------|
| `brain.md` | En yuksek | Mimari kararlar, ADR 001-087 | Read-Write | 1000 satir |
| `index.md` | Yuksek | Master katalog, tum vault indeksi | Read-Write | 1000 satir |
| `AGENTS.md` | Yuksek | Agent tanimlari, yetkiler, handover | Read-Write | 1000 satir |
| `MEMORY.md` | Orta | Session state, bu dosya | Read-Write | 1000 satir |
| `log.md` | En dusuk | Append-only audit trail | Append-Only | 1000 satir |

**Bellek Modlari:**
- **Read-Only:** Boot protokolu sirasinda
- **Append-Only:** `log.md` icin kalici mod
- **Read-Write:** `MEMORY.md` ve digerleri icin
- **Locked:** Context Lock sirasinda (max 30s)

**Oncelik Siralamasi:** En yuksek -> en dusuk: brain -> index -> AGENTS -> MEMORY -> log. Cakisma durumunda ust seviye kazanir.

---

## 4. Session Lifecycle

| Asama | Aciklama | Sure | Cikti |
|-------|----------|------|-------|
| 1. Initialize | Boot protokolu (10 dosya oku) | Max 25s | Session state |
| 2. Sync Start | 5 soru ile vault durumunu analiz et | Max 10s | Degisiklik listesi |
| 3. Execute Task | Vault dosyalarini oku ve gorevi yurut | Degisken | Gorev cikti |
| 4. Log Actions | Degisiklikleri `log.md`'ye yaz | Anlik | Audit trail |
| 5. Vault-Sync | Vault'u guncelle (gerekirse) | Degisken | Guncellenmis vault |
| 6. Sync End | 6 adim ile oturumu kapat | Max 15s | Kapanis kaydi |
| 7. Session Close | Final state kaydi | Max 5s | Final state |

**Zamanlayici:** Toplam session suresi ortalama 5-15 dakika. Boot 25s, sync start 10s, sync end 15s, session close 5s = 55s sabit. Kalan: gorev yurutme.

---

## 5. 16-Step Boot Protocol

| # | Dosya | Amac | Oncelik | Timeout |
|---|-------|------|---------|---------|
| 1 | `.ai/CLAUDE.md` | Kanonik AI talimati | P0 | 3s |
| 2 | `.ai/AGENTS.md` | Agent kayit defteri | P0 | 3s |
| 3 | `.ai/WORKFLOW.md` | Surecler | P0 | 3s |
| 4 | `.ai/index.md` | Master katalog | P1 | 4s |
| 5 | `.ai/keys.md` | Anahtar kelime haritasi | P1 | 3s |
| 6 | `.ai/AGENTS.md` | Agent yetkileri (tekrar) | P1 | 3s |
| 7 | `.ai/brain.md` | Mimari kararlar | P1 | 4s |
| 8 | `.ai/MEMORY.md` | Oturum hafizasi | P1 | 3s |
| 9 | `.ai/log.md` | Aktivite gunlugu (son 20 satir) | P1 | 2s |
| 10 | `.ai/ROLE.md` | Senior Software Architect rol tanimi | P1 | 3s |
| 11 | `.claude/rules/*` | Tum kurallar | P2 | 5s |
| 12 | `.ai/archives/prompt0-genel-ana-prompt-2026-08-13` | Ana genel prompt: 11 alt domain, 10 panel, 20 analiz gorevi, zorunlu kurallar | P2 | 5s |
| 13 | `.ai/archives/prompt1-spa-router-2026-08-13` | SPA Router: Enterprise router gereksinimleri, SOLID, PSR, DI | P2 | 3s |
| 14 | `.ai/archives/prompt2-auth-2026-08-13` | Auth: Merkezi auth.coremusic.net, hybrid JWT+session, RBAC, middleware | P2 | 3s |
| 15 | `.ai/archives/prompt3-api-2026-08-13` | API: API-First, Gateway, BFF, CQRS, Event Driven, 14 servis | P2 | 3s |
| 16 | `.ai/ui-design/00-mockup-index.md` | Mockup esleme tablosu — frontend gorevlerinde ZORUNLU | P2 | 3s |

**Toplam boot suresi:** Max 36 saniye. P0 -> P1 -> P2 sirasiyla okunur. Paralel okuma desteklenmez (sirali bagimlilik).

**Frontend Gorev Kurali:** CSS/HTML/JS/layout/bileşen görevlerinde `00-mockup-index.md` okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve bildir. Görsel referanslar: `.ai/.png/home-1024/` (12 PNG) + `.ai/.png/shared-1024/` (6 PNG) = toplam 18 PNG mockup

---

## 6. Session Vault Sync — Baslangic (5 Soru)

| # | Soru | Kontrol Yontemi | Kaynak |
|---|------|-----------------|--------|
| 1 | Son session'dan bu yana ne degisti? | `git log --since="last session"` + `log.md` tail | git, log.md |
| 2 | Yeni ADR var mi? | `decisions/accepted/` dizin taramasi | filesystem |
| 3 | Kod degisikligi oldu mu? | `git diff --name-only` | git |
| 4 | Vault'ta eski bilgi var mi? | `VERIFICATION REQUIRED` etiket taramasi | grep |
| 5 | Skills durumu nedir? | `.opencode/skills/` + `.claude/skills/` kontrolu | filesystem |

**Senaryo:** Her oturum basinda bu 5 soru cevaplanir. Cevaplar `log.md`'ye INFO olarak kaydedilir. Vault'ta eski bilgi varsa duzeltilir.

---

## 7. Session Vault Sync — Bitis (5 Adim)

| # | Adim | Kontrol | Sure |
|---|------|---------|------|
| 1 | Degisiklikleri vault'a yaz (in-place) | Dosya boyutu | 5s |
| 2 | `log.md`'ye timestamp ekle | Format dogrulama | 2s |
| 3 | MEMORY.md session state guncelle | Session indeks | 3s |
| 4 | Wiki-link'leri dogrula | Regex pattern | 5s |
| 5 | Hallusinasyon sweep | `VERIFICATION REQUIRED` taramasi | 3s |

**Toplam:** Max 20 saniye. Wiki-link dogrulama regex: `\[\[([^\]]+)\]\]`.

---

**Kurallar:**
1. P0 -> P1 -> P2 -> P3 sirasiyla okunur
2. Fallback: `index.md`
3. Token asimi onlenir: gereksiz dosya okunmaz

---

## 9. Persistent State

| Kategori | Dosya | Guncelleme Sikligi | Mod |
|----------|-------|-------------------|-----|
| Mimari Kararlar | `brain.md` | Yeni ADR'de | Read-Write |
| Session Hafizasi | `MEMORY.md` | Her oturum sonunda | Read-Write |
| Audit Trail | `log.md` | Her kritik olayda | Append-Only |
| Agent Tanimlari | `AGENTS.md` | Degisiklikte | Read-Write |
| Surecler | `WORKFLOW.md` | Degisiklikte | Read-Write |
| Master Katalog | `index.md` | Yeni dosya eklendiginde | Read-Write |
| Keyword Haritasi | `keys.md` | Yeni kelime eklendiginde | Read-Write |

**Kurallar:**
- Immutability: ADR 001-037 frozen, degistirilemez
- Append-Only: `log.md` gecmis satirlari silinemez
- Timestamp zorunlu: Her giris UTC timestamp icermeli
- No Secrets: Hassas veri ASLA vault'a yazilmaz
- Cross-Reference: Tum wiki-link'ler gecerli olmali

**Guncellenme Akisi:** Degisiklik tespit -> Ilgili dosya belirle -> In-place uygula -> Cross-reference guncelle -> `log.md`'ye yaz -> MEMORY.md session state guncelle -> Wiki-link'leri dogrula.

---

## 10. Cache Strategies

| Seviye | Aciklama | Omur | Gecersizlastirma |
|--------|----------|------|------------------|
| L1 (Hot) | SSOT dosyalari (CLAUDE, AGENTS, WORKFLOW) | Oturum sonu | Otomatik |
| L2 (Warm) | Gorev dosyalari (ADR, architecture) | Gorev sonu | Dosya degisikligi |
| L3 (Cool) | Referans dosyalari (testing, ui-design) | Istege bagli | Manuel |

**Politika:** Read-Through, Write-Through, LRU eviction. P0 dosyalari eviction'a ugramaz.

---

## 11. Backup & Recovery

| Yontem | Siklik | Saklama | Kullanim |
|--------|--------|---------|----------|
| Git History | Her commit | Sonsuz | Birincil kurtarma |
| Manuel Snapshot | Haftalik | 3 ay | Haftalik yedek |
| Tam Vault Yedegi | Aylik | 1 yil | Tam kurtarma |
| Session Backup | Her oturum sonu | 30 gun | Session kurtarma |

| Durum | Kurtarma Yontemi | Hedef Sure |
|-------|------------------|------------|
| Dosya bozulmasi | `git checkout <hash> -- .ai/dosya.md` | <1 dk |
| Kirik wiki-link | index.md + keys.md guncelle | <5 dk |
| Vault silinmesi | `git restore .ai/` | <5 dk |
| Session kaybi | log.md'den resume | <2 dk |
| ADR cakiskisi | L1 -> L2 -> L3 -> Insan | <30 dk |
| Vault corruption | `git checkout` + son commit | <5 dk |
| Cache bozulmasi | L1 flush, yeniden yukle | <1 dk |

---

## 12. Security Boundaries

| Veri Turu | Sinif | Vault'a Yazilabilir mi? | Loglanirken |
|-----------|-------|--------------------------|-------------|
| API Key | SECRET | ❌ ASLA | `[REDACTED]` |
| DB Password | SECRET | ❌ ASLA | `[REDACTED]` |
| JWT Secret | SECRET | ❌ ASLA | `[REDACTED]` |
| Session Token | SECRET | ❌ ASLA | `[REDACTED]` |
| ARL Token | SECRET | ❌ ASLA | `[REDACTED]` |
| Credential Vault Sifresi | SECRET | ❌ ASLA | `[REDACTED]` |
| User Email (masked) | PII | ✅ Kisim | Kisim maskeleme |
| User ID | PUBLIC | ✅ | Yok |
| ADR Karari | PUBLIC | ✅ | Yok |
| Port Numarasi | PUBLIC | ✅ | Yok |
| Dosya Yolu | PUBLIC | ✅ | Yok |

**Dogru:** `API Key: [REDACTED] (service: deezer)` | **Yanlis:** `API Key: abc123` (ASLA!)

**Redaction Kontrolu:** Her oturum sonunda `Select-String -Path .ai/log.md -Pattern "password|api[_-]?key|secret|token"` ile tarama yapilir.

---

## 13. Memory Conflict Resolution

| Cakisma Turu | Belirti | Cozum | Sorumlu |
|---------------|---------|-------|---------|
| Write-Write | Iki ajan ayni dosyayi duzenlemek ister | Context Lock + Queue | MO |
| Read-Write | Bir ajan okurken digeri yazar | Read lock (eszamanli okuma serbest) | Otomatik |
| Version Conflict | Farkli versiyonlar olusturulur | Master Orchestrator mudahalesi | MO |
| Reference Conflict | Kirik wiki-link'ler olusur | Cross-reference update | MO |
| ADR Conflict | Cakiskili kararlar | Escalation (L1->L2->L3->Insan) | L3 |

**Context Lock:**
- Max 30 saniye
- Deadlock'da MO en eski kilidi kirar
- Oncelik sirasi: CRITICAL > HIGH > MEDIUM > LOW
- Lock acquire/release `log.md`'ye yazilir

---

## 14. Memory Debugging

| Sorun | Belirti | Cozum | Oncelik |
|-------|---------|-------|---------|
| Kirik wiki-link | `[[dosya]]` gecersiz | Dogru dosya yolunu bul, link'i guncelle | HIGH |
| Eksik frontmatter | 7 zorunlu alan eksik | Frontmatter'i tamamla | MEDIUM |
| Boyut limit asimi | Dosya >1000 satir | Dosyayi bol veya arsivle | MEDIUM |
| Hallusinasyon | `VERIFICATION REQUIRED` etiketi yok | Etiketi ekle | CRITICAL |
| Session kaybi | Oturum yarim kaldi | log.md'den resume | MEDIUM |
| Vault tutarsizligi | Cakiskili dosyalar | Cross-reference update | HIGH |
| Eski bilgi | `VERIFICATION REQUIRED` var | Dogrula veya sil | MEDIUM |
| Frontmatter eksik | metadata alani yok | 7 zorunlu alani ekle | LOW |
| Timestamp hatasi | UTC formati yanlis | Formati duzelt | LOW |

**Dogrulama Araclari:**
- `vault-integrity-check.ps1` — tam vault taramasi
- `git log --oneline` — degisiklik gecmisi
- Regex pattern matching — wiki-link dogrulama
- `Select-String` — hassas veri taramasi

---

## 15. Warnings

| # | Uyari | Kategori | ADR |
|---|-------|----------|-----|
| 1 | Hassas veri ASLA `.ai/` dizinine yazilmaz | Guvenlik | ADR-022 |
| 2 | `log.md` Append-Only, gecmis silinemez | Butunluk | ADR-004 |
| 3 | Session timestamp'leri UTC immutable olmali | Izlenebilirlik | ADR-004 |
| 4 | ADR 001-037 FROZEN, yeni karar icin ADR-038+ | Immutability | ADR-042 |
| 6 | **music.coremusic.net = Port 81, PHP 8.4** | Altyapi | ADR-042 |
| 7 | **CSRF Token Key = `csrf_token`** | Guvenlik | ADR-010 |
| 8 | Layer Violation: L0->L3 import yasak | Mimari | CLAUDE.md |
| 9 | ORM yasak — sadece PDO prepared | Veritabani | ADR-002 |
| 10 | Framework yasak — sadece Vanilla JS | Frontend | ADR-001 |
| 11 | Middleware sirasi degismez | Guvenlik | ADR-010/011/012/013/022 |
| 12 | PCM5122 yasak — 8.1 icin yetersiz | Donanim | ADR-038 |

---

## 16. Limitations

| Kisit | Aciklama | Cozum |
|-------|----------|-------|
| Dosya boyutu | Max 1000 satir/dosya | Moduler yapi, arsivleme |
| Eszamanli erisim | Lock tabanli basit cozum | Context Lock + Queue |
| Vault boyutu | Max 100MB | Gereksiz kopyalari arsivle |
| Boot suresi | Max 25 saniye | Paralel okuma optimizasyonu |
| Memory boyutu | Max 10MB toplam | Arsivleme, sikistirma |
| Link dogrulama | Regex tabanli | Otomatik duzeltme |

---

## 17. Future Roadmap

| Surum | Hedef | Tahmini |
|-------|-------|---------|
| v19.0 | Vektor DB (pgvector/ChromaDB) ile semantic search | 2026 Q4 |
| v20.0 | Cross-Project Memory (WirelessConnect entegrasyonu) | 2027 Q1 |
| v21.0 | Otomatik bellek yonetimi (Auto-Memory, Smart Cache) | 2027 Q2 |
| v22.0 | Tam otonom bellek (Zero Human Intervention) | 2027 Q3 |
| v23.0 | Multi-device memory sync | 2027 Q4 |

---

## 18. Session History

| Tarih | Konu | Durum | ADR | Agent |
|-------|------|-------|-----|-------|
| 2026-08-04 | Dynamic Theme Engine | Vault tamamlandi, kodlama yok | [[ADR-044-dynamic-user-theme-engine]] | UI |
| 2026-08-05 | Auth SOLID Fixes + Tests | ✅ Tamamlandi (56 test, 0 failure) | [[ADR-010-csrf-protection-strategy]] | Security |
| 2026-08-05 | Vault Activasyon + Session | ✅ Tamamlandi (12 adim) | [[ADR-042-vault-restructuring-2026-08-03]] | MO |
| 2026-08-06 | .workflows/ Trim | ✅ Tamamlandi (-87%, 584 satir) | — | MO |
| 2026-08-06 | Template Vault v3.0.0 | ✅ Tamamlandi (19 template, 26K satir) | — | MO |
| 2026-08-08 | Vault Rewrite (engine, MEMORY, log) | ✅ Tamamlandi | [[ADR-042-vault-restructuring-2026-08-03]] | MO |
| 2026-08-09 | Platform Rewrite Vault Update | ✅ 4 yeni ADR + architecture guncellendi | [[ADR-053/054]] | MO |
| 2026-08-09 | Electronics Vault Integration | ✅ 50+ dosya, L6 katmani, 3 yeni ADR (061-063) | [[ADR-061/062/063-electronics]] | MO |
| 2026-08-09 | AI Architecture + Vault Update | ✅ 12 yeni dosya (9 AI + 1 BCNF + 2 Security), OWASP 2025, PCM3168A duzeltmesi | [[ADR-030/035/036/049-ai]] | MO |
| 2026-08-12 | Katmanlı Mimari Plan + ADR-083/084/085/086 + Vault Güncellemeleri | ✅ Tamamlandı | ADR-083, ADR-084, ADR-085, ADR-086 | MO |
| 2026-08-13 | Master Implementation Plan + ADR-087 + Tüm Vault Revize | ✅ Tamamlandı (22 bölüm, 5 faz, 40 gün) | ADR-087, master-implementation-plan.md | MO |
| 2026-08-13 | Vault Sync — Master Implementation Plan doğrulama + MEMORY/log güncelleme | ✅ Tamamlandı | master-implementation-plan.md | MO |
| 2026-08-13 | Vault Restructuring — Templates Entegrasyon, Agent Yeniden Yapılandırma, Prompt Arsivleme | ✅ 5 faz, ~43 dosya | log.md | MO |
| 2026-08-13 | Prompt Entegrasyonu — prompt0-3 okundu, .ai vault güncellendi, ADR revizeleri | ✅ brain.md, ADR-083, ADR-084 güncellendi, opencode.json optimize edildi | ADR-083 v2.0, ADR-084 v2.0 | MO |
| 2026-08-13 | opencode.json Yeniden Yapılandırma — 12 agent prompt'u düzeltildi, template entegrasyonu, kesik metin giderildi, n@ hatası düzeltildi | ✅ 12 agent'a @.ai/.templates/index.md + @.ai/ROLE.md referansları eklendi, master-orchestrator prompt'u 7-adımlı task dispatch ile tamamlandı | ADR-042, ADR-083, ADR-084, ADR-085, ADR-086 | MO |
| 2026-08-13 | .ai Beyin Yeniden Yapılandırma — Çelişki düzeltme, template entegrasyonu, prompt bağlama, hibrit kurallar | ✅ Tamamlandı (~20 dosya) | ADR-042, ADR-087 | MO |
| 2026-08-15 | 18 BCNF Vault Senkronizasyonu — SQL dosyalarına göre vault tamamen yeniden yapılandırıldı | ✅ coremusic_download.sql oluşturuldu, database_master.md yeniden yazıldı (18 DB, 156 tablo), 27+ dosya güncellendi | — | Data Engineer |
| 2026-08-15 | Architecture Vault Veri Tutarlılık Düzeltmesi — 12 dosya, ~20 değişiklik | ✅ architecture-master.md oluşturuldu, CLAUDE.md DB 11→18, index.md ADR 78→87, brain.md L0-L3→L0-L6, keys.md L4-L6 keywords, tüm layer dosyaları L0-L6 güncellendi | — | MO |
| 2026-08-18 | Responsive CSS Architecture — a-layout-tokens.css v2.0.0, token konsolidasyonu, 4 breakpoint media query, device CSS dönüşümü | ✅ brain.md §18A, keys.md responsive keyword'leri, log.md entry | — | MO |
| 2026-08-18 | Responsive CSS Architecture Rule — Vault'a zorunlu kural olarak yerleştirildi. Guardrail #17 (CLAUDE.md §7), brain.md §18A güncellendi (Responsive CSS Mimarisi Kuralı, yasak örüntüleri, dosya yapısı), AGENTS.md §15.3 UI Designer'a responsive kuralı eklendi | — | MO |
| 2026-08-19 | Responsive Device Mode Architecture — .ai/ui-design/responsive-device-mode.md oluşturuldu (17 bölüm), tek component + embedded device override kuralı, Guardrail #17 uyumlu, 3 cross-reference güncellendi | — | MO |
| 2026-09-01 | Prompt Processing Session — 8 prompt işlendi, 2 duplicate temizlendi, 4 arşiv + 4 vault güncellendi | ✅ prompt0-3 → 2026-09-01 versiyonları, auth-architecture v2.0, api-architecture-master v2.0, electronics-overview v3.0, spa-router v7.0, CLAUDE.md prompt referansları güncellendi | — | MO |
| 2026-09-01 | Device-Aware Frontend Rendering — Component token sistemi kuruldu, hardcoded media query'ler kaldırıldı, inline style temizlendi | ✅ 6 dosya: a-layout-tokens.css v3.0.0 (+11 component token × 7 breakpoint), _home-components.css v4.0.0 (-200 satır hardcoded), _home-layout.css v4.0.0 (token-based grid), footer.php v5.0.0 (inline→token), _footer.css v3.0.0, d-embedded.css v4.0.0 + vault: responsive-frontend-architecture.md v2.0.0, device-css.md, responsive-device-mode.md | — | UI |
| 2026-09-02 | Home.php Embedded Rewrite — PNG mockup birebir uyum, BEM sınıfları, sosyal medya ikonları | ✅ 2 dosya: home.php v5.2.0 (Split 42/58 + widget grid + social row), _home-components.css (social-row + social-btn CSS) | — | UI |
| 2026-09-02 | DeviceManager — PHP-side device-aware rendering, 5 cihaz bloğu, feature toggles | ✅ 5 dosya: DeviceManager.php (yeni — central device management), home.php v6.0.0 (5 cihaz HTML bloğu), header.php v5.0.0 (dm nav), footer.php v6.0.0 (dm feature toggles), .ai vault 5 dosya güncelleme | — | UI |
| 2026-09-03 | Phase 1-5 Device-Aware Cleanup — PageRouter viewport fix, manuel require temizliği, inline style→token dönüşümü, CSS token uyumluluğu, welcome modal doğrulama | ✅ 8 dosya: PageRouter.php (viewportW/H eklendi), HtmlShellRenderer.php (viewportW/H eklendi), header.php (require kaldırıldı, inline→CSS custom property), footer.php (require kaldırıldı, inline→CSS class), home.php (require kaldırıldı, inline→CSS), b-base-core.css (body-bg-image token), _footer.css (mobile+embedded playctrl CSS), _home-components.css (text-decoration, playlist-btn embedded) | — | MO |
| 2026-09-03 | Phase 1 Technical Architecture Assessment — 3 görev: (1) mimari dokümantasyon envanteri (50+ dosya, 15 kategori), (2) ADR-083/084/085/086/087 + kritik belge analizi (API Gateway, SPA Router, Shared Library, Middleware Pipeline, Event Driven), (3) Faz 1 teknik mimari değerlendirme raporu (24 deliverable, ~95% tamamlandı, API kontratları, veri akışları, kural setleri, middleware pipeline detayları) | ✅ .ai/reports/phase1-technical-architecture-assessment.md oluşturuldu (9 bölüm, 10 kalite metriği) | ADR-083/084/085/086/087 | vault-updater |
| 2026-09-04 | Welcome Popup Responsive — shouldRenderWelcomePopup() tüm cihazlara açıldı, home.php v8.1.0, _home-components.css 4 responsive media query (mobile/tablet/laptop/desktop/4K TV) | ✅ Guardrail #17 uyumlu: tek component + CSS responsive | — | UI |
| 2026-09-04 | Footer Utility Icons + Seek Slider — footer.php v8.0.0, DeviceManager v1.1.0, 9 icon + seek slider, _footer.css responsive düzeltmeleri | ✅ 3 dosya: DeviceManager.php (showUtilityIcons, showFooterSeekSlider), footer.php (9 icon + seek slider), _footer.css (embedded display:flex, phone max-width fix) | — | UI |
| 2026-09-04 | Koşullu Render Mimarisi — 3-way conditional rendering (Embedded/Wide/Fallback), DeviceManager +4 metot, home.php v9.0.0, JS viewport cookie, PHP cookie fallback | ✅ 7 dosya: DeviceManager.php v2.0.0 (+shouldRenderEmbeddedLayout/WideLayout/ShowFallback/isSupportedResolution), home.php v9.0.0 (3 render bloğu), _home-layout.css v5.0.0 (fallback stili), _home-components.css v5.0.0 (wide component), device-loader.js (cookie yazma), PageRouter.php (cookie okuma), HtmlShellRenderer.php (cookie okuma) + vault: responsive-device-mode.md v2.0.0, brain.md §18B, keys.md keywords | — | MO |
| 2026-09-04 | Hibrit Scale Motoru Refactor (SOLID ES6+) — scale*.js 4 dosya silindi, ScaleManager.js (TierResolver + TransformApplier + declarative rules + DPR/aspect + EventBus), main.js v6.0.0, a-scale-hybrid.css v3.0.0, header/footer temizlik, DevTools 5-tier canlı test | ✅ 5 dosya değişti + 4 silindi: ScaleManager.js (yeni v6.0.0), main.js v5→v6 (import+init+registerModule), a-scale-hybrid.css v2→v3 (referans senkron), header.php (ölü koşul temizliği), footer.php (Çince karakter düzeltme); silinen: scale.coordinator.js, header.scale.js, footer.scale.js, home.scale.js. Test: 1024 embedded ✅, 1920 desktop ✅, 2564 2k ✅, 500 phone ✅, EventBus scale:applied doğrulandı | — | UI |
| 2026-09-04 | CLAUDE.md Rewrite — Root CLAUDE.md v4.0.0 yeniden yazıldı, mükerrer bölümler kaldırıldı, .ai/models/index.md, .ai/issues/index.md, .ai/scripts/index.md oluşturuldu | ✅ Root CLAUDE.md v4.0.0 (18 bölüm, temiz yapı), 3 yeni index dosyası | — | MO |
| 2026-09-04 | 40-Day Implementation Plan — .ai/architecture/03-contracts/40-day-implementation-plan.md oluşturuldu (5 faz, 40 gün, 200+ görev) | ✅ 5 faz (Foundation, Backend, Frontend, Integration, Production), bağımlılık grafisi, risk matrisi, kalite kapıları | ADR-087 | vault-updater |
| 2026-09-05 | Device-Aware Rendering Vault Update — brain.md §18C (Backend/Frontend sorumluluk sınırları, token değerleri, WCAG 2.2 AA, katman ihlal kontrolü), keys.md §3.4A (8 yeni device-aware keyword), responsive-device-mode.md v3.0.0 (4-Tier Conditional Rendering) | ✅ 3 vault dosyası güncellendi: brain.md (§18C Device-Aware Rendering Kuralları), keys.md (+8 keyword), MEMORY.md (session history +1) | — | vault-updater |

---

## Q&A Kararlari (2026-08-13)

| Soru | Cevap | Kaynak |
|------|-------|--------|
| Shared library yapisi | Hybrid: tek shared/, moduler namespace (CoreMusic\Auth, CoreMusic\Security) | Kullanici |
| Veritabani motoru | MySQL 9.7 (local, zaten kurulu) | Kullanici |
| Web server | Apache (:81) + IIS (:80) paralel | Kullanici |
| PHP versiyonu | PHP 8.5.8 (C:\Php858\) — zaten kurulu | Kullanici |
| MySQL kullanici | Ali / ali** (root degil) | Kullanici |
| Entry point yapisi | Her subdomain kendi index.php'si (PROJECT_ROOT pattern) | Kullanici + MO |
| Session storage | File-based baslangic → DB gecis plani | Kullanici + MO |
| JWT key uretimi | FAZ 0'da RS256 key pair uretimi (openssl) | Kullanici + MO |
| Composer versiyonlari | psr/http-server-handler ^1.0 (2.0 yok), respect/validation ^2.0, phpstan ^2.0 | MO (hata duzeltme) |
| Master Implementation Plan | 5 faz, 40 gun, 22 bolum, 30 cikti, shared/ hybrid yapı, 25 enterprise paket | MO (ADR-087) |
| Referans proje analizi | monolitik shared, 448 satir AuthController, 3 origin CORS, test yok tespit edildi | MO (ADR-087) |
| SOLID ihlalleri | SRP (6 uzun sinif), ISP (2 buyuk interface), OCP (yeni provider eklenemez) tespit edildi | MO (ADR-087) |
| Katman ihlalleri | Controller→Repository direkt, Config global constant, PSR-15 uyumsuzluk tespit edildi | MO (ADR-087) |

---

## 19. Cross References

| Kaynak | Hedef | Tip | ADR |
|--------|-------|-----|-----|
| `MEMORY.md` | [[CLAUDE.md]] | Zorunlu baglanti | ADR-042 |
| `MEMORY.md` | [[AGENTS.md]] | Zorunlu baglanti | — |
| `MEMORY.md` | [[WORKFLOW.md]] | Zorunlu baglanti | — |
| `MEMORY.md` | [[index.md]] | Zorunlu baglanti | — |
| `MEMORY.md` | [[keys.md]] | Zorunlu baglanti | — |
| `MEMORY.md` | [[brain.md]] | Zorunlu baglanti | — |
| `MEMORY.md` | [[log.md]] | Zorunlu baglanti | — |
| `MEMORY.md` | [[ADR-004-multi-domain-spa]] | Vault versiyonlama | ADR-004 |
| `MEMORY.md` | [[ADR-022-database-hardened-security]] | Guvenlik | ADR-022 |
| `MEMORY.md` | [[ADR-010-csrf-protection-strategy]] | CSRF | ADR-010 |
| `MEMORY.md` | [[ADR-011-session-management]] | Session | ADR-011 |

---

## 20. Current Session State

| Ozellik | Deger |
|---------|-------|
| Session Date | 2026-09-04 |
| Active Task | 40-Day Implementation Plan — Oluşturuldu ve vault'a kaydedildi |
| Domain | Architecture Planning (40-day detailed implementation plan) |
| Last Action | 40-day implementation plan oluşturuldu: 5 faz, 40 günlük görev listesi, bağımlılık grafisi, risk matrisi, kalite kapıları. Dosya: .ai/architecture/03-contracts/40-day-implementation-plan.md |
| Changed Files | 40-day-implementation-plan.md (yeni), log.md (+1 entry), MEMORY.md (session history +1, session state) |
| Known Issue | Auth redirect loop — Session lifecycle mismatch (önceki session'dan devam). CRITICAL priority fix gerekli. |

### Frontend Mimarisi (v2.0.0 — 2026-09-05)

```
CSS Katmanı (ITCSS 9-layer):
  01_Abstracts/  → Token'lar (colors, fonts, layout, breakpoints, theme, device tokens)
    a-layout-tokens.css  → Cihaz bazlı token: --header-h (60/70/80px), --footer-h (90/104/120px), --content-h (450/906/1960px)
    Device-Aware Token: @media (min-width: 1920px) → Wide override, @media (min-width: 3840px) → 4K override
  02_Base/       → Reset, base styles
  03_Layout/     → Header (.site-header--embedded/desktop/tv), Footer (.footer--embedded/desktop/tv)
  04_Components/ → Scrollbar, Footer seek/volume
  05_Pages/      → Home layout (.home-layout--embedded/laptop/desktop/tv), home components
  06_Utilities/  → Helper classes
  07_Vendors/    → Bootstrap (minimal)
  08_Devices/    → 7 device CSS (behavioral overrides: hover, touch, scrollbar)
    d-embedded.css  → Touch optimization: hover disabled, min 48px touch targets
    d-desktop.css   → Mouse interaction: hover active, cursor: pointer
    d-4k.css        → Spacing scale: --spacing-scale: 1.5
  09_ViewModes/  → 4 view modes (home, pro, studio, car)

Backend Sorumluluk Sınırları (brain.md §18C):
  PHP tarafında YALNIZCA davranışsal konfigürasyonlar:
  ✅ widgetCount(), recentCardCount(), playlistCount(), upNextCount()
  ✅ showVolume(), showFullMetadata(), showSeekBar(), showSidebar()
  ✅ navLinks(), allClasses(), dataAttributes(), layoutClass()
  ❌ margin, padding, width, height, font-size → CSS'e aittir

Frontend Sorumluluk Sınırları (brain.md §18C):
  CSS tarafında TÜM sunum kararları:
  ✅ Token tanımları: a-layout-tokens.css → --header-h, --footer-h, --content-h
  ✅ Token override: Media query ile cihaz bazlı değer değişimi
  ✅ Behavioral override: 08_Devices/d-{device}.css → hover, touch, scrollbar
  ✅ Layout grid: _home-layout.css → grid-template-columns, gap
  ✅ Component yerleşimi: _home-components.css → boyut, konum

Tek Bileşen İlkesi (Guardrail #17):
  ✅ Tek HTML: home.php, header.php, footer.php → tek dosya
  ❌ home-1024.php, home-desktop.html → KESİNLİKLE YASAK
  ✅ Fark CSS'te: media query + CSS variables
  ❌ PHP'de sunum kararı → Layer violation

JS Katmanı (ES Modules):
  main.js                    ← Entry point: Router + tüm modülleri başlatır
  core/
    EventBus.js              → Pub/sub (bağımsız)
    CoreMusicApp.js          → Lifecycle manager
  managers/
    DeviceManager.js         → Cihaz tespiti (device-loader.js bridge)
    ThemeManager.js          → ADR-044 gender theme
    ViewModeManager.js       → ADR-045 view mode
  features/
    PlayerController.js      → State machine (STOPPED/PLAYING/PAUSED)
    WidgetManager.js         → Home widgets
    CardManager.js           → Event delegation
    ScrollManager.js         → Route scroll restore
    TouchManager.js          → Embedded touch gestures
  router/
    Router.js + guards.js    → Mevcut SPA router (main.js import ediyor)
    SPARouterAdapter.js      → DEPRECATED (main.js doğrudan Router kullanıyor)
    21+ modül               → GuardPipeline, CacheLayer, DomPatcher, vb.
  device-loader.js           → Cihaz tespiti (IIFE, non-module, TV & 1024 laptop sync)
  device-layout-updater.js   → Cihaz değişikliğinde layout güncelleme

Backend (shared/src):
  Device/
    DeviceDetector.php       → Cihaz tespiti (Smart TV + 1024x768 laptop desktop OS desteği)
    DeviceCssMap.php         → CSS haritası (mevcut)
    DeviceManager.php        → Merkezi cihaz yönetimi (deviceProfile(), isSmallDesktop(), isTv())
  Theme/
    ThemeManager.php         → Gender tema yönetimi (ADR-044)
  ViewMode/
    ViewModeManager.php      → View mode yönetimi (ADR-045)
  PageRouter/
    PageRouterKernel.php     → Ana kernel
    PageRouter.php           → Tekil sayfa çözücü (DeviceTemplateResolver bağımlılığı kaldırıldı)
    HtmlShellRenderer.php    → HTML shell (ThemeManager + ViewModeManager entegre)

Backend (home.coremusic.net):
  index.php              → Entry point (PageRouterKernel)
  header.php             → Single Header View (Phone / Embedded / TV / Desktop-Laptop conditional structural rendering)
  footer.php             → Single Footer View (Phone / Embedded / TV / Desktop-Laptop conditional structural rendering)
  pages/home.php         → Single Home View (Embedded / Wide / Fallback conditional rendering v9.0.0)
  include/               → Auth, Session, Container
  config/                → Constants, app config
```

---

## 21. Quality Report

| Metrik | Deger |
|--------|-------|
| Version | 24.3.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 21 |
| SSOT Authority | Memory System Index |
| Last Updated | 2026-09-04 |
| ADR Coverage | ADR-001 through ADR-087 (37 Frozen + 50 Active) |
| Security Boundary | REDACTED policy |
| Session History | 24 oturum |
| Cross References | 12 capraz referans |
| Terminology | 14 terim |
| Frontend Modules | 14 (main.js v5.0 — Router entegre) |
| PHP Backend Modules | 3 (DeviceManager.php, ThemeManager.php, ViewModeManager.php) |
| PHP Single Views | home.php, header.php, footer.php (Tek dosya, sıfır kopya, conditional rendering) |
| CSS Device Files | 7 (phone, tablet, embedded, laptop, desktop, 4k-tv, 4k-monitor) |
| CSS View Modes | 4 (home, pro, studio, car) |
| Automated Test Suite | 37/37 pass (PART 62 matrisi + Rendering + Alias metodları) |
| Conditional Rendering | 4 Tier (Phone ≤767 / Embedded ≤1024 / Wide 1025-2560 / 4K ≥2561) |

---

## 22. Recent Revisions (2026-09-05)

### 22.1 Device-Aware Rendering Revizyonu

**Kapsam.** 4 device CSS dosyasında eksik font/scale import'ları, body background global token'a taşındı, PHP dosyalarında inline dokümantasyon güçlendirildi.

**Değişen Dosyalar.**

| Dosya | Değişiklik |
|-------|-----------|
| `assets.coremusic.net/Css/08_Devices/d-embedded.css` | `a-fonts-token.css` + `a-scale-hybrid.css` import'ları eklendi |
| `assets.coremusic.net/Css/08_Devices/d-desktop.css` | Aynı |
| `assets.coremusic.net/Css/08_Devices/d-4k-tv.css` | Aynı |
| `assets.coremusic.net/Css/08_Devices/d-4k-monitor.css` | Aynı |
| `assets.coremusic.net/Css/01_Abstracts/a-layout-tokens.css` | `--body-bg-image` default `welcome-popup-girl.png` olarak güncellendi |
| `home.coremusic.net/pages/home.php` | Layout karar matrisi tablosu eklendi (4 tier × container/split/popup) |
| `home.coremusic.net/header.php` | Tier sınıf zinciri dokümantasyonu eklendi |
| `home.coremusic.net/footer.php` | 3-Zone × 4-Tier davranış matrisi eklendi |

**Doğrulanan Mevcut Yapı (değişmedi).**
- `shared/src/Device/DeviceManager.php` v2.0.0 (frozen) — tüm 4 Tier karar metotları
- `home.coremusic.net/pages/home.php` v10.0.0 → v10.0.1 — yalnızca doc version bump
- `home.coremusic.net/header.php` v8.0.0 → v8.0.1 — yalnızca doc version bump
- `home.coremusic.net/footer.php` v11.0.0 → v11.0.1 — yalnızca doc version bump
- Welcome modal CSS — `_home-components.css` L640+ zaten tam tanımlı (`.welcome-modal-overlay`, `.welcome-modal`, `.welcome-modal__*` + 4 media query)

**Doğrulama (browser doğrulaması Faz 6'da yapılacak).**
- 1024×600 → `data-device="embedded"`, welcome popup açık
- 1920×1080 → `data-device="desktop"`, `.home-layout--wide` 3-sütun
- 3840×2160 → `data-device="4k-monitor"`, `.home-layout--4k`

**Referans.** Bu revizyon planı: `plan.md` (session bXZzXzNiZThmODVmMzNjMTQzMDU5NWJjYzVkZjFiMjQ1YTFj)

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-05
**Mode:** Red Team · Human Mode · Truth Mode