---
title: "CoreMusic — Prompt Maker Format Templates"
type: template
category: prompt-maker
version: 1.1.0
status: active
authority: "SSOT: .ai/.templates/index.md — Guardrail #16 Mandatory"
updated: 2026-10-07
---

# Prompt Maker — Format Templates (8 Format)

> **BAGLAYICI UZUNLUK KURALI (§0 — template.md v1.1.0):**
> Bu dosyadaki HANGİ format kullanılırsa kullanılsın, üretilen final prompt
> **EN AZ 500 SATIR** olmalıdır. Her format iskeleti, kendi min satır bütçesiyle
> verilmiştir. Bütçenin altındaki teslim GEÇERSİZDİR (PASS 3 sayımı zorunlu).
> Format ≠ kısaltma: format, içeriği düzenler; 500 satır alt sınırını düşürmez.

---

## Format Seçim Matrisi

| # | Format | Tetikleyici (hangi görevde) | Min satır | Zorunlu çekirdek bölümler |
|---|--------|---------------------------|:---------:|---------------------------|
| 1 | SYSTEM PROMPT | Yeni AI agent / bot tanımlama | 500 | ROLE, CONTEXT, OBJECTIVE, RULES, CONSTRAINTS, WORKFLOW, OUTPUT, VALIDATION |
| 2 | TASK PROMPT | Somut uygulama görevi verme | 500 | TASK, INPUT, REQUIREMENTS, CONSTRAINTS, ACCEPTANCE CRITERIA, OUTPUT |
| 3 | CODE REVIEW | Kod inceleme / PR review | 500 | CONTEXT, CODE, ANALYSIS, FINDINGS, RISK, FIX, VERIFICATION |
| 4 | SECURITY AUDIT | Güvenlik denetimi | 500 | SCOPE, ASSET, ATTACK SURFACE, THREATS, FINDINGS, IMPACT, MITIGATION, VERIFICATION |
| 5 | REVERSE ENGINEERING | Kod/binary analizi | 500 | INPUT, STRUCTURE, DEPENDENCIES, CALL FLOW, DATA FLOW, SECURITY, ARCHITECTURE, REPORT |
| 6 | REFACTOR | Yeniden yapılandırma | 500 | CURRENT STATE, PROBLEMS, ROOT CAUSE, TARGET STATE, SAFE CHANGES, TESTING, ROLLBACK |
| 7 | ARCHITECTURE | Mimari tasarım / ADR | 500 | REQUIREMENTS, CONTEXT, DOMAIN, COMPONENTS, DATA FLOW, API, SECURITY, SCALABILITY, TRADE-OFFS, ADR |
| 8 | MIGRATION | Teknoloji / versiyon taşıma | 500 | CURRENT STATE, TARGET STATE, GAP, STRATEGY, PHASES, COMPATIBILITY, RISK, ROLLBACK, VALIDATION |

**Seçim kuralı:** Görev tipine en yakın format seçilir. İkisi uygulanıyorsa birleştir (ör. SECURITY AUDIT +
TASK). Format seçimi gerekçesi promptun başına bir satırla yazılır.

**Ortak zorunluluklar (her formatta):**
1. Frontmatter yok — çıktı doğrudan prompt metnidir.
2. Zero-Hallucination (§18): bilinmeyen `[UNKNOWN]` / `[VERIFY REQUIRED]` ile işaretlenir.
3. CoreMusic bağlamı gerekiyorsa: 16 Hard Guardrail, `csrf_token` (NOT `_csrf_token`), Raw PDO (NO ORM),
   Vanilla JS (NO React/Vue/Angular), Port 81 = music.coremusic.net, ADR-001/002/010 referansları.
4. Acceptance criteria ÖLÇÜLEBİLİR olmalıdır (checkbox maddeleri).
5. Her bölüm min bütçesi aşılmadan PASS 3 sayımı yapılır.

---

## FORMAT 1 — SYSTEM PROMPT

**Kullanım:** Yeni AI agent, bot veya hizmet tanımlanırken.

**İskelet + min satır bütçesi:**

```text
# SYSTEM PROMPT

## 1. ROLE (min 20 satır)
- Kimlik: unvan + uzmanlık alanları (ör. Senior Backend Architect)
- Deneyim seviyesi ve düşünme modeli
- Sorumluluk sınırları (neyi YAPAR, neyi YAPMAZ)

## 2. CONTEXT (min 30 satır)
- Proje / sistem tanımı
- Ekosistem bileşenleri (CoreMusic: 10 panel, 7 servis, 18 BCNF DB)
- Hedef kullanıcılar ve kullanım ortamı

## 3. OBJECTIVE (min 20 satır)
- Birincil amaç (tek cümle + genişletme)
- Başarı tanımı (ölçülebilir)
- Öncelik sırası

## 4. RULES (min 60 satır)
- Davranış kuralları (madde madde, her madde GEREKÇE + ÖRNEK)
- Zero-Hallucination kuralı
- Çelişki çözüm sırası
- DUR (stop) koşulları

## 5. CONSTRAINTS (min 50 satır)
- Teknik kısıtlar (stack, versiyon, bağımlılık yasakları)
- Güvenlik kısıtları
- Performans kısıtları
- İzin verilmeyen eylemler

## 6. WORKFLOW (min 60 satır)
- Adım adım iş akışı (fazlar, kapılar, çıktılar)
- Her adımın girdisi/çıktısı
- Hata durumunda geri dönüş yolu

## 7. OUTPUT (min 40 satır)
- Çıktı formatı (Markdown/JSON + bölüm yapısı)
- Uzunluk kuralları
- Örnek çıktı iskeleti

## 8. VALIDATION (min 40 satır)
- Kendini denetim checklist'i
- Kabul kriterleri (checkbox)
- Yaygın hatalar listesi

## 9. EDGE CASES (min 25 satır)
- Kenar durumlar + beklenen tepkiler

## 10. EXAMPLES (min 25 satır)
- 1-2 tam örnek etkileşim (input → davranış → output)
```

**Toplam min bütçe:** ~370 satır + bölüm araları + giriş/çıkış ≈ **500+ satır.**

---

## FORMAT 2 — TASK PROMPT

**Kullanım:** Somut uygulama görevi (endpoint, component, script, şema) verilirken.

**İskelet + min satır bütçesi:**

```text
# TASK PROMPT

## 1. TASK (min 15 satır)
- Görev tek cümlede + kapsam genişletmesi
- Neden bu görev? (bağlam)

## 2. INPUT (min 30 satır)
- Verilen girdiler (dosya, veri, API, şema)
- Mevcut durum kanıtları (gerçek dosya yolları)
- [NOT PROVIDED] ile eksik girdiler

## 3. REQUIREMENTS (min 80 satır)
- Functional requirements (madde madde + davranış)
- Technical requirements (stack, versiyon, standartlar)
- Security requirements (OWASP, auth, validasyon)
- Performance requirements (hedefler, metrikler)

## 4. ARCHITECTURE (min 40 satır)
- Dokunulan katmanlar (CoreMusic K-tablosu referansı)
- Bağımlılıklar ve etkileşim diyagramı (ASCII/Mermaid)

## 5. CONSTRAINTS (min 40 satır)
- Yasak kalıplar (CoreMusic §21 Forbidden Patterns)
- In-Place kuralları, dosya adı değişikliği yasağı
- Geri dönüş (rollback) koşulları

## 6. ERROR HANDLING (min 30 satır)
- Hata sınıfları ve tepki stratejisi
- Retry/backoff politikası
- Log formatı

## 7. ACCEPTANCE CRITERIA (min 50 satır)
- Ölçülebilir checkbox maddeleri
- Test senaryoları (geçmeli/geçmemeli)
- Performans kabulü

## 8. OUTPUT (min 30 satır)
- Teslim formatı (kod + diff + açıklama)
- Dosya yapısı
- Örnek çıktı

## 9. WORKFLOW (min 25 satır)
- Uygulama sırası (analiz → plan → kod → test → rapor)

## 10. EDGE CASES (min 25 satır)
- Kenar durumlar + tepkiler

## 11. VALIDATION (min 15 satır)
- Son checklist
```

**Toplam min bütçe:** ~380 satır + başlıklar ≈ **500+ satır.**

---

## FORMAT 3 — CODE REVIEW

**Kullanım:** PR / dosya inceleme görevlerinde.

**İskelet + min satır bütçesi:**

```text
# CODE REVIEW PROMPT

## 1. CONTEXT (min 40 satır)
- İncelenen kodun rolü (hangi katman, hangi servis)
- Proje konvansiyonları (PSR-12, BEM, strict_types)
- İnceleme kapsamı (dosya listesi, commit aralığı)

## 2. CODE (min 40 satır)
- İncelenecek kod blokları / dosya yolları
- Diff bağlamı (ne değişti, neden)

## 3. ANALYSIS (min 80 satır)
- Kontrol edilecek boyutlar (madde madde):
  correctness · security · performance · readability
  · SOLID · duplicate code · error handling · testing
- Her boyut için ÖLÇÜT (neye göre judged edilir)

## 4. FINDINGS (min 60 satır)
- Bulgu formatı: Severity → Evidence (file:line) → Root Cause → Impact
- Severity tanım tablosu (CRITICAL/HIGH/MEDIUM/LOW)
- Bulgusuz boyut için "no finding" zorunluluğu (uydurma bulgu yasak)

## 5. RISK (min 40 satır)
- Her bulgu için risk senaryosu (girdi → hatalı davranış)
- Üretim etkisi olasılığı

## 6. FIX (min 60 satır)
- Önerilen düzeltme (kod örnekli)
- Alternatif düzeltme + trade-off
- Düzeltme sırası (önceliklendirme)

## 7. VERIFICATION (min 40 satır)
- Düzeltme sonrası doğrulama adımları
- Test senaryoları
- Regresyon kontrolü

## 8. ACCEPTANCE (min 30 satır)
- İnceleme kabul kriterleri (checkbox)
- Red koşulları

## 9. OUTPUT FORMAT (min 25 satır)
- Rapor yapısı (tablo + detay)
- Özet istatistik formatı

## 10. EDGE CASES (min 25 satır)
```

**Toplam min bütçe:** ~440 satır + başlıklar ≈ **500+ satır.**

---

## FORMAT 4 — SECURITY AUDIT

**Kullanım:** OWASP denetimi, güvenlik incelemesi, threat modeling.

**İskelet + min satır bütçesi:**

```text
# SECURITY AUDIT PROMPT

## 1. SCOPE (min 40 satır)
- Denetlenen yüzey (endpoint, middleware, DB, dosya, ağ)
- Kapsam dışı (explicit)
- Denetim derinliği (statik + dinamik + manuel)

## 2. ASSET (min 35 satır)
- Korunacak varlıklar (veri, kimlik, sır, IP)
- Varlık kritiklik tablosu
- CoreMusic spesifik: credential vault, session, JWT, API key

## 3. ATTACK SURFACE (min 45 satır)
- Giriş noktaları (HTTP, WS, dosya, CLI, BLE…)
- Middleware pipeline analizi (10 adım — CoreMusic §6)
- Ağ görünürlüğü (subdomain'ler, portlar)

## 4. THREATS (min 60 satır)
- OWASP Top 10:2025 madde madde
- Tehdit senaryoları: Adversary → Vector → Goal
- Her tehdit için ön koşullar

## 5. FINDINGS (min 60 satır)
- Bulgu formatı: Severity → Evidence → Root Cause → Impact
- Doğrulanabilir kanıt zorunluluğu (file:line, request örneği)
- Kanıtsız bulgu YASAK (uydurma CVE/severity yasak)

## 6. IMPACT (min 35 satır)
- Etki analizi: veri kaybı / yetki kaybı / süreklilik
- Olasılık × etki matrisi

## 7. MITIGATION (min 55 satır)
- Kök neden düzeltmesi (geçici patch değil)
- Savunma katmanları (defense in depth)
- CoreMusic guardrail bağlantısı (hangi § ile örtüşüyor)

## 8. VERIFICATION (min 40 satır)
- Düzeltme doğrulama adımları (yeniden test)
- Regresyon testi
- Sürekli izleme (alert/log)

## 9. ACCEPTANCE (min 30 satır)
- Kabul kriterleri (checkbox)
- CRITICAL/HIGH kapanış zorunluluğu

## 10. OUTPUT FORMAT (min 25 satır)
- Yönetici özeti + teknik detay ayrımı

## 11. EDGE CASES (min 25 satır)
- Yanlış pozitif yönetimi, doğrulanamayan iddia
```

**Toplam min bütçe:** ~450 satır + başlıklar ≈ **500+ satır.**

---

## FORMAT 5 — REVERSE ENGINEERING

**Kullanım:** Verilen kod/tablo/binary analiz edilirken.

**İskelet + min satır bütçesi:**

```text
# REVERSE ENGINEERING PROMPT

## 1. INPUT (min 35 satır)
- Analiz girdisi (repo/ZIP/dosya/binary)
- Bilinen ipuçları
- Analiz sınırları (neye dokunulmaz)

## 2. STRUCTURE (min 45 satır)
- Proje/file tree çıkarımı
- Build yapılandırması
- Giriş noktaları (entry points)
- Dizin/dosya sorumluluk tablosu

## 3. DEPENDENCIES (min 35 satır)
- Paket/bağımlılık çıkarımı (gerçek dosyalardan)
- Versiyonlar (bilinmiyorsa [VERIFY REQUIRED])
- Çevrim içi/çevrim dışı bileşenler

## 4. CALL FLOW (min 50 satır)
- Entry Point → Handler → Service → Domain → DB zinciri
- Route tabloları
- Middleware/interceptor sırası

## 5. DATA FLOW (min 40 satır)
- Veri dönüşümü (input → storage → output)
- Şema/ilişki çıkarımı
- Cache/queue akışları

## 6. SECURITY (min 50 satır)
- Güvenlik gözlemleri (kanıtla: file:line)
- Authn/authz akışı
- Sırların nerede yaşadığı

## 7. ARCHITECTURE (min 45 satır)
- Çıkarılan mimari (mevcut durum — varsayım değil kanıt)
- Katman eşlemesi
- Teknik borç gözlemleri

## 8. REPORT (min 60 satır)
- Bulgular özeti (tablo)
- Bilinmeyenler listesi ([UNKNOWN] / [VERIFY REQUIRED])
- Yeniden yazım ÖNERİSİ YOK (önce analiz → sonra karar)
- Sonraki adımlar

## 9. VALIDATION (min 25 satır)
- Çıkarım doğrulama checklist'i
- Uydurma yapı yasak kontrolü

## 10. EDGE CASES (min 25 satır)
- Eksik dosya, bozuk build, çelişki tespiti
```

**Toplam min bütçe:** ~410 satır + başlıklar ≈ **500+ satır.**

---

## FORMAT 6 — REFACTOR

**Kullanım:** Davranış-koruyucu yeniden yapılandırma.

**İskelet + min satır bütçesi:**

```text
# REFACTOR PROMPT

## 1. CURRENT STATE (min 50 satır)
- Mevcut kodun davranışı (ne yaptığı)
- Mevcut yapı (dosya, sınıf, bağımlılıklar)
- Kanıt: gerçek dosya yolları + satır referansları

## 2. PROBLEMS (min 50 satır)
- Sorun listesi (Severity + Evidence + Root Cause)
- Anti-pattern eşlemesi (God Class, Fat Controller, …)
- Sorunun maliyeti (bakım, hata, performans)

## 3. ROOT CAUSE (min 40 satır)
- Her sorunun KÖK NEDENİ (belirti değil)
- Kök neden → sorun ağacı

## 4. TARGET STATE (min 45 satır)
- Hedef yapı (davranış DEĞİŞMEZ — sadece yapı)
- Hedef mimari + arayüzler
- Öncesi/sonrası karşılaştırması

## 5. SAFE CHANGES (min 60 satır)
- Değişiklik adımları (küçük, geri alınabilir)
- Değişiklik sırası (bağımlılık sırasına göre)
- Her adımın riski + geri alma (undo) yöntemi

## 6. TESTING (min 55 satır)
- Karakterizasyon testleri (refactor ÖNCESİ)
- Yeni testler
- Coverage hedefi + regresyon kontrolü

## 7. ROLLBACK (min 35 satır)
- Geri alma planı (commit stratejisi)
- Riskli adım stop koşulları

## 8. CONSTRAINTS (min 30 satır)
- In-Place (dosya adı değişmez), davranış-koruyucu
- Forbidden patterns

## 9. ACCEPTANCE (min 30 satır)
- Checkbox: davranış aynı mı? testler geçiyor mu? vb.

## 10. EDGE CASES (min 25 satır)
```

**Toplam min bütçe:** ~420 satır + başlıklar ≈ **500+ satır.**

---

## FORMAT 7 — ARCHITECTURE

**Kullanım:** Mimari tasarım, ADR üretimi, katman planı.

**İskelet + min satır bütçesi:**

```text
# ARCHITECTURE PROMPT

## 1. REQUIREMENTS (min 50 satır)
- Fonksiyonel gereksinimler
- Fonksiyonel olmayan gereksinimler (performans, güvenlik, ölçek)
- Kısıtlar

## 2. CONTEXT (min 40 satır)
- Mevcut sistem bağlamı
- Paydaşlar ve hedefler
- CoreMusic katman eşlemesi (K000–K499 / mevcut K-tablosu)

## 3. DOMAIN (min 40 satır)
- Domain / subdomain (gerekiyorsa DDD)
- Bounded context, ubiquitous language
- Domain kuralları

## 4. COMPONENTS (min 50 satır)
- Bileşen listesi + sorumluluklar
- Arayüzler (interface sözleşmeleri)
- Bileşen diyagramı (ASCII/Mermaid)

## 5. DATA FLOW (min 40 satır)
- Veri akışı adımları
- Event/queue akışları (gerekiyorsa)
- Durum geçişleri

## 6. API (min 40 satır)
- Endpoint listesi + sözleşmeler
- Hata kodları, versioning, pagination

## 7. SECURITY (min 40 satır)
- Authn/authz modeli
- Tehditler + tasarım düzeyi savunmalar

## 8. SCALABILITY (min 35 satır)
- Ölçeklenme senaryoları
- Darboğaz tahminleri (kanıtsız benchmark YASAK)

## 9. TRADE-OFFS (min 50 satır)
- Seçenekler tablosu (artı/eksi/maliyet)
- Reddedilen alternatifler + GEREKÇE

## 10. ADR (min 45 satır)
- Decision: tek cümle karar
- Context / Consequences (Nygard formatı)
- Doğrulama maddeleri

## 11. WORKFLOW (min 25 satır)
- Uygulama fazları

## 12. EDGE CASES (min 25 satır)
```

**Toplam min bütçe:** ~440 satır + başlıklar ≈ **500+ satır.**

---

## FORMAT 8 — MIGRATION

**Kullanım:** Teknoloji, versiyon veya sistem taşıması.

**İskelet + min satır bütçesi:**

```text
# MIGRATION PROMPT

## 1. CURRENT STATE (min 45 satır)
- Mevcut teknoloji/versiyon + kanıt (lockfile, config)
- Mevcut davranışın envanteri
- Bağımlılıklar (kim bu teknolojiye bağlı)

## 2. TARGET STATE (min 40 satır)
- Hedef teknoloji/versiyon
- Hedef davranış (değişen/değişmeyen)
- Başarı tanımı

## 3. GAP (min 45 satır)
- Fark analizi tablosu (özellik ↔ özellik)
- Kaldırılan/eklenen/değişen API'ler
- Uyumsuzluklar (breaking changes)

## 4. STRATEGY (min 45 satır)
- Strateji seçimi (parallel run, strangler, big-bang…)
- Neden bu strateji (gerçekçe)
- Reddedilen stratejiler + gerekçe

## 5. PHASES (min 50 satır)
- Faz tablosu (adım, çıktı, kapı/hard gate)
- Her fazın geri alınabilirliği

## 6. COMPATIBILITY (min 40 satır)
- Geriye dönük uyumluluk
- Veri formatı uyumluluğu
- API sözleşmesi korunumu

## 7. RISK (min 40 satır)
- Riskler + olasılık × etki
- Mitigasyon her risk için
- Veri kaybı senaryoları

## 8. ROLLBACK (min 35 satır)
- Faz bazlı rollback planı
- Veri geri alma (down-migration)
- Karar noktaları (stop koşulları)

## 9. VALIDATION (min 45 satır)
- Doğrulama adımları (her faz sonunda)
- Test stratejisi + kabul kriterleri
- Performans karşılaştırması (önce/sonra)

## 10. ACCEPTANCE (min 30 satır)
- Final checkbox listesi

## 11. EDGE CASES (min 25 satır)
- Kısmi başarısızlık, zaman aşımı, veri tutarsızlığı
```

**Toplam min bütçe:** ~440 satır + başlıklar ≈ **500+ satır.**

---

## ORTAK DOĞRULAMA (her format tesliminden önce — PASS 3)

- [ ] **Çıktı ≥ 500 satır?** (sayım yapıldı — yoksa teslim YASAK)
- [ ] Format iskeletinin ZORUNLU bölümleri eksiksiz mi?
- [ ] Her bölüm min bütçesini karşıladı mı?
- [ ] Tek cümlelik bölüm / açıklamasız madde listesi var mı? (varsa derinleştir)
- [ ] Dolgu/tekrar ile şişirme yok mu? (derinlik gerçek mi)
- [ ] Zero-Hallucination: `[UNKNOWN]` / `[VERIFY REQUIRED]` doğru kullanıldı mı?
- [ ] CoreMusic bağlamı gerekiyorsa guardrail referansları doğru mu?
- [ ] Acceptance criteria ölçülebilir mi?

**Format çelişirse §0 (template.md v1.1.0) kazanır.**

---

**Authority**: Bayram Ali / Vault Steward
**Last Updated**: 2026-10-07
**Mode**: Red Team · Human Mode · Truth Mode
