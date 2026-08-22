---
title: "CoreMusic — Memory System Index"
type: system
category: memory-management
date: 2026-08-13
updated: 2026-08-21
status: active
version: 22.1.0
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
| Session Date | 2026-08-22 |
| Active Task | Backend PHP modülleri — ThemeManager + ViewModeManager tamamlandı |
| Domain | home.coremusic.net |
| Page | home.php |
| Components | header.php, footer.php, main.js v5 |
| CSS Status | Responsive token'lar tamamlandı, device CSS'ler mevcut |
| JS Status | main.js v5 — Router + 11 modül entegre edildi |
| PHP Status | ThemeManager.php + ViewModeManager.php oluşturuldu, HtmlShellRenderer entegre |
| PHP Changes | HtmlShellRenderer → main.js + ThemeManager::detect() + ViewModeManager::detect() |
| Device Targets | mobile, tablet, 1024 embedded, laptop, desktop, 4K TV, 4K monitor |

### Frontend Mimarisi

```
CSS Katmanı (ITCSS 9-layer):
  01_Abstracts/  → Token'lar (colors, fonts, layout, breakpoints, theme)
  02_Base/       → Reset, base styles
  03_Layout/     → Header, Footer
  04_Components/ → Scrollbar, Footer seek/volume
  05_Pages/      → Home layout, home components
  06_Utilities/  → Helper classes
  07_Vendors/    → Bootstrap (minimal)
  08_Devices/    → 7 device CSS (phone, tablet, embedded, laptop, desktop, 4k-tv, 4k-monitor)
  09_ViewModes/  → 4 view modes (home, pro, studio, car)

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
  device-loader.js           → Cihaz tespiti (IIFE, non-module)

Backend (shared/src):
  Device/
    DeviceDetector.php       → Cihaz tespiti (mevcut)
    DeviceCssMap.php         → CSS haritası (mevcut)
  Theme/
    ThemeManager.php         → Gender tema yönetimi (YENİ — ADR-044)
  ViewMode/
    ViewModeManager.php      → View mode yönetimi (YENİ — ADR-045)
  PageRouter/
    PageRouterKernel.php     → Ana kernel
    HtmlShellRenderer.php    → HTML shell (ThemeManager + ViewModeManager entegre)

Backend (home.coremusic.net):
  index.php              → Entry point (PageRouterKernel)
  header.php             → Header partial (C01-C03)
  footer.php             → Footer partial (Player)
  pages/home.php         → Home page (Split 42/58)
  include/               → Auth, Session, Container
  config/                → Constants, app config

PHP Changes (HtmlShellRenderer.php):
  - mainJsFile: router/main.js → main.js
  - script tag: /js/router/main.js → /js/main.js
  - gender: inline session read → ThemeManager::detect()
  - viewMode: DeviceCssMap::sanitizeViewMode() → ViewModeManager::detect()
  - device-loader.js: aynen kalıyor (IIFE)
```

---

## 21. Quality Report

| Metrik | Deger |
|--------|-------|
| Version | 22.2.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 21 |
| SSOT Authority | Memory System Index |
| Last Updated | 2026-08-22 |
| ADR Coverage | ADR-001 through ADR-087 (37 Frozen + 50 Active) |
| Security Boundary | REDACTED policy |
| Session History | 16 oturum |
| Cross References | 12 capraz referans |
| Terminology | 14 terim |
| Frontend Modules | 14 (main.js v5.0 — Router entegre) |
| PHP Backend Modules | 2 yeni (ThemeManager.php, ViewModeManager.php) |
| PHP Changes | HtmlShellRenderer → main.js + ThemeManager + ViewModeManager |
| CSS Device Files | 7 (phone, tablet, embedded, laptop, desktop, 4k-tv, 4k-monitor) |
| CSS View Modes | 4 (home, pro, studio, car) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-21
**Mode:** Red Team · Human Mode · Truth Mode