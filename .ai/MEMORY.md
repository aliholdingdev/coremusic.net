---
type: system
category: memory-management
updated: 2026-08-09
status: active
version: 20.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Memory System Index

**Zorunlu Baglantilar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[brain.md]] · [[keys.md]] · [[log.md]]

---

## 1. Amac

CoreMusic bellek sistemi, oturumlar arasi persistent state yonetimini standartlastirir ve vault ile kod arasindaki tutarliligi korur. MSA (Sparse Attention) ile token asimi engellenir, audit trail ile izlenebilirlik garanti edilir. Bu dosya, tum AI ajanlarinin oturum baslangicinda okumasi gereken 9 zorunlu dosyadan biridir.

---

## 2. Terminoloji

| Terim | Tanim |
|-------|-------|
| Persistent State | Oturumlar kapansa dahi silinmeyen bellek |
| Vault-Sync | Degisikliklerin `.ai/` dosyalarina senkronizasyonu |
| MSA (Sparse Attention) | Gorev basina max 15 dosya okuma protokolu |
| Context Lock | Dosyanin gecici sure dondurulmesi |
| Audit Trail | Tum degisikliklerin timestamp ile loglanmasi |
| SSOT | Single Source of Truth — Tek Dogruluk Kaynagi |
| Memory Hierarchy | Bellek oncelik sirasi: brain > index > AGENTS > MEMORY > log |
| Hard Guardrail | Asilamayan sert mimari kural |
| Zero Code Before Plan | Planlama yapilmadan kod yazma yasagi |
| BCNF | Boyce-Codd Normal Form — 9 DB icin zorunlu normalizasyon |
| Immutability | ADR 001-037 frozen, degistirilemez |
| Append-Only | Sadece ekleme, gecmis satirlar dokunulmaz |
| Frontmatter | Dosya basligi, 7 zorunlu alan |
| Wiki-Link | `[[dosya/yolu]]` formatinda capraz referans |

---

## 3. Memory Hierarchy

| Dosya | Oncelik | Icerik | Mod | Max Boyut |
|-------|---------|--------|-----|-----------|
| `brain.md` | En yuksek | Mimari kararlar, ADR 001-050 | Read-Write | 1000 satir |
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

## 5. 10-Step Boot Protocol

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
| 10 | `.claude/rules/*` | Tum kurallar | P2 | 5s |
| 11 | `.ai/ui-design/00-mockup-index.md` | Mockup esleme tablosu — frontend gorevlerinde ZORUNLU | P2 | 3s |

**Toplam boot suresi:** Max 28 saniye. P0 -> P1 -> P2 sirasiyla okunur. Paralel okuma desteklenmez (sirali bagimlilik).

**Frontend Gorev Kurali:** CSS/HTML/JS/layout/bileşen görevlerinde `00-mockup-index.md` okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve bildir. Görsel referanslar (`.ai/.png/**`) 15 dosya MSA limitine dahil değildir.

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

## 7. Session Vault Sync — Bitis (6 Adim)

| # | Adim | Kontrol | Sure |
|---|------|---------|------|
| 1 | Degisiklikleri vault'a yaz (in-place) | Dosya boyutu | 5s |
| 2 | `log.md`'ye timestamp ekle | Format dogrulama | 2s |
| 3 | MEMORY.md session state guncelle | Session indeks | 3s |
| 4 | Wiki-link'leri dogrula | Regex pattern | 5s |
| 5 | MSA limit kontrolu (15 dosya) | Dosya sayaci | 2s |
| 6 | Hallusinasyon sweep | `VERIFICATION REQUIRED` taramasi | 3s |

**Toplam:** Max 20 saniye. Wiki-link dogrulama regex: `\[\[([^\]]+)\]\]`.

---

## 8. MSA Sparse Attention (ADR-042/C5)

**Gorev basina max 15 dosya okunur.**

| Oncelik | Dosya Grubu | Max | Toplam |
|---------|-------------|-----|--------|
| P0 (Kritik) | CLAUDE.md, AGENTS.md, WORKFLOW.md | 3 | 3 |
| P1 (Yuksek) | index.md, keys.md, brain.md, MEMORY.md, log.md | 5 | 8 |
| P2 (Gorev) | decisions/accepted/ADR-NNN, architecture/L[0-3]/* | 5 | 13 |
| P3 (Dusuk) | testing/*, ui-design/*, personas/* | 2 | 15 |

**Kurallar:**
1. P0 -> P1 -> P2 -> P3 sirasiyla okunur
2. Fallback: `index.md`
3. Limit asilirsa `log.md`'ye WARN yazilir
4. Token asimi onlenir: gereksiz dosya okunmaz
5. Secici okuma (Sparse Attention) uygulanir

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
| Token overflow | MSA >15 dosya | Gorev parcalama | HIGH |
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
| 5 | **MSA Limit = 15 dosya** | Token Ekonomisi | ADR-042 |
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
| Token sinirlamasi | Context window sinirli | MSA (secici okuma) |
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
| 2026-08-09 | Platform Rewrite Vault Update | ✅ 4 yeni ADR + architecture guncellendi | [[ADR-051/052/053/054]] | MO |
| 2026-08-09 | Project Structure Plan | ✅ ADR-055 olusturuldu — 20 adimlik implementasyon plani | [[ADR-055-project-structure-plan]] | MO |
| 2026-08-09 | Auth Module Implementation | ✅ ADR-056 olusturuldu — 26 adimlik auth modulu plani | [[ADR-056-auth-module-implementation]] | Security |
| 2026-08-09 | Router & Middleware Implementation | ✅ ADR-057 olusturuldu — 22 adimlik router+middleware plani | [[ADR-057-router-middleware-implementation]] | Backend |
| 2026-08-09 | Electronics Vault Integration | ✅ 50+ dosya, L6 katmani, 3 yeni ADR (061-063) | [[ADR-061/062/063-electronics]] | MO |
| 2026-08-09 | AI Architecture + Vault Update | ✅ 12 yeni dosya (9 AI + 1 BCNF + 2 Security), OWASP 2025, PCM3168A duzeltmesi | [[ADR-030/035/036/049-ai]] | MO |

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
| `MEMORY.md` | [[ADR-042-vault-restructuring-2026-08-03]] | MSA limit | ADR-042 |
| `MEMORY.md` | [[ADR-004-multi-domain-spa]] | Vault versiyonlama | ADR-004 |
| `MEMORY.md` | [[ADR-022-database-hardened-security]] | Guvenlik | ADR-022 |
| `MEMORY.md` | [[ADR-010-csrf-protection-strategy]] | CSRF | ADR-010 |
| `MEMORY.md` | [[ADR-011-session-management]] | Session | ADR-011 |

---

## 20. Quality Report

| Metrik | Deger |
|--------|-------|
| Version | 19.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 20 |
| SSOT Authority | Memory System Index |
| Last Updated | 2026-08-08 |
| ADR Coverage | ADR-001/002/004/007/008/010/011/022/034/038/040/042/043/044 |
| MSA Uyumlu | ✅ |
| Security Boundary | ✅ REDACTED policy |
| Session History | 11 oturum |
| Cross References | 12 capraz referans |
| Terminology | 14 terim |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode