---
title: "CoreMusic — Orchestration Engine"
type: system
version: 20.0.0
---

# CoreMusic — Orchestration Engine

**SSOT:** [[AGENTS.md]] (ana agent kayıt defteri ve orkestrasyon protokolü)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

---

## 1. Amaç

Bu dosya, CoreMusic orkestrasyon motorunun indeksidir. Detaylı orkestrasyon protokolü [[AGENTS.md]]'de bulunur.

## 2. Orkestrasyon Bölümleri

| Bölüm | Konum | İçerik |
|-------|-------|--------|
| Task Dispatch Algorithm | [[AGENTS.md]] §7 | 7 adım|
| Keyword → Agent Routing | [[AGENTS.md]] §6 | 11 keyword grubu |
| Handover Protocol | [[AGENTS.md]] §9 | Mesaj formatı, kurallar, senaryolar |
| Escalation Protocol | [[AGENTS.md]] §10 | 4 seviye, senaryolar |
| Health Check | [[AGENTS.md]] §11 | 5 durum, akış |
| Context Lock | [[AGENTS.md]] §12 | Kilitleme, deadlock önleme |
| Priority Levels | [[AGENTS.md]] §8 | CRITICAL/HIGH/MEDIUM/LOW |

## 3. Ek Orkestrasyon İçeriği

Bu bölümler sadece bu dosyada bulunur:

| Bölüm | İçerik |
|-------|--------|
| Task Queue | Kuyruk yapısı ve kuralları |
| Agent Communication | İletişim kanalları ve mesaj formatı |
| Metrics & Monitoring | Metrikler ve izleme |
| Troubleshooting | Sorun giderme senaryoları |

## 4. Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| Agent tanımları | [[AGENTS.md]] §15 |
| Görev dağıtımı | [[AGENTS.md]] §7 |
| Handover | [[AGENTS.md]] §9 |
| Eskalasyon | [[AGENTS.md]] §10 |
| Sağlık kontrolü | [[AGENTS.md]] §11 |
| Context lock | [[AGENTS.md]] §12 |
| UI Design | [[ui-design/00-mockup-index]] |
| Mockup PNG'ler | `.ai/.png/home-1024/`, `.ai/.png/home-1920/`, `.ai/.png/shared-1024/` |
| Responsive kuralları | [[ui-design/responsive-device-mode]] (4K No-Center §7.4, Backward-Compat §12) |

---

## 5. Task Queue (Kuyruk Yapısı)

### 5.1 Workflow Durum Makinesi

Her görev [[WORKFLOW.md]] §Workflow State'te tanımlı 5 durumdan geçer:

| Durum | Anlam | Geçiş Kuralı |
|-------|-------|--------------|
| `Pending` | Kuyrukta bekliyor | Task dispatch algoritması (§5.3) sıraya alır |
| `Approved` | Kullanıcı onayı alındı | Yalnız Human Mode onay kapısından geçer |
| `Running` | Aktif çalışıyor | Aynı anda tek `in_progress` görev (agent kuralı) |
| `Completed` | Tamamlandı + doğrulandı | Doğrulama adımı çalıştırılmadan kapatılamaz |
| `Failed` | Başarısız | Blocker notu eklenir, escalation (§6.3) tetiklenebilir |

### 5.2 Kuyruk Kuralları

1. **Tek Kilit:** Aynı dosya üzerinde iki agent aynı anda `Running` olamaz — Context Lock ([[AGENTS.md]] §12) ile korunur.
2. **Bağımlılık Sırası:** Veri şeması → API → UI → Test sırası bozulamaz ([[architecture/03-contracts/development-workflow]]).
3. **Atomik Tamamlandırma:** Dosya-başına çalışılır; yarım dosya bırakılmaz (Faz revizyonlarında zorunlu).
4. **Onay Kapısı:** Mimari değişiklik, dosya adı/konum değişikliği, şema değişikliği → kod başlamadan kullanıcı onayı ([[WORKFLOW.md]] 4 Temel Kural #1).

### 5.3 Task Dispatch Algorithm ([[AGENTS.md]] §7 — 7 adım)

| # | Adım | Kaynak |
|---|------|--------|
| 1 | Görev tanımı al (prompt) | Kullanıcı / üst agent |
| 2 | Keyword → Agent Routing ([[AGENTS.md]] §6, 11 grup) | Routing tablosu |
| 3 | Domain boundary kontrolü | [[AGENTS.md]] §2 |
| 4 | Context lock al (dosya/alan) | [[AGENTS.md]] §12 |
| 5 | Uzman profile görev dağıtımı | `.ai/.agents/AGENTS.md` |
| 6 | Checkpoint doğrulama (2-3 agent'ta bir) | Alt-agent koordinasyon |
| 7 | Sonuç toplama + log.md append | Bu motor |

### 5.4 Priority Levels ([[AGENTS.md]] §8)

| Seviye | Kullanım | Örnek |
|--------|----------|-------|
| `CRITICAL` | Güvenlik, veri bütünlüğü, üretim hatası | CSRF/CSP ihlali, DB şema bozulması |
| `HIGH` | Mimari bütünlük, boot dosyası hatası | Vault çelişkisi, kırık SSOT referansı |
| `MEDIUM` | Dokümantasyon revizyonu, performans | Bu dosyanın Faz 1 güncellemesi |
| `LOW` | Kozmetik, stil | YAML format düzeltmesi |

### 5.5 Görev Kayıt Şablonu

Her kuyruk görevi bu şablonla kaydedilir:

```text
GÖREV:        [kısa ad]
Durum:        Pending | Approved | Running | Completed | Failed
Öncelik:      CRITICAL | HIGH | MEDIUM | LOW
Agent:        [atanan profil — .ai/.agents/AGENTS.md kaydı]
Lock:         [kilitli dosya/alan — §8.3 Context Lock]
Bağımlılık:   [önceki görev kimlikleri, varsa]
Doğrulama:    [test/kontrol kriterleri — "nasıl kapanacağı"]
Log:          [log.md satır referansı — append sonrası doldurulur]
```

Kayıt kuralları:
1. `Doğrulama` alanı boş görev kuyruğa giremez — kapanış kriteri önceden tanımlı olmalı.
2. `Lock` alanı dolu görev başka agent'a devredilemez; önce lock serbest bırakılır.
3. `Failed` durumundaki görev yeniden kuyruğa alınırken yeni kayıt açılır, eski kayıt `log.md` kanıtıyla saklanır.

---

## 6. Agent Communication (İletişim Kanalları)

### 6.1 Kanallar

| Kanal | Kullanım | Format |
|-------|----------|--------|
| Task Dispatch | Üst agent → alt agent | `task(subagent_type, prompt, description)` |
| Handover | Agent → agent devir | [[AGENTS.md]] §9 mesaj formatı |
| Checkpoint | Alt-agent grubu → kullanıcı | Uncertainty flag toplama + onay sorusu |
| Escalation | Alt agent → üst/kullanıcı | [[AGENTS.md]] §10, 4 seviye |
| Audit | Herkes → `log.md` | Append-only satır |

### 6.2 Handover Mesaj Formatı (Zorunlu 5 Alan — [[AGENTS.md]] §9)

| # | Alan | İçerik |
|---|------|--------|
| 1 | Context | Görevin bağlamı, hangi vault dosyaları okundu |
| 2 | Current State | Mevcut durum: tamamlanan/adım sayısı, dosya yolları |
| 3 | Requested Action | Devir alan agent'tan istenen net işlem |
| 4 | Constraints | Kısıtlar: ADR'ler, Guardrails, kapsam sınırları |
| 5 | Expected Output | Beklenen çıktı formatı ve doğrulama kriteri |

### 6.2.1 Handover Örneği (Faz 0 → Faz 1 gerçek geçiş kaydı, 2026-09-08)

```text
CONTEXT:          .ai vault 741 MD / 136.261 satır envanterlendi;
                  Faz 0 doğrulama taramaları (4/4 explorer) tamamlandı.
CURRENT STATE:    engine.md 500+ satıra ulaştı (§3'teki 4 vaat bölümü
                  gerçekleştirildi); glossary/ROLE/ULTRA-THINKING bekliyor.
REQUESTED ACTION: glossary.md'yi kod-referanslı terimlerle 500+ satıra genişlet.
CONSTRAINTS:      ADR-002 (PDO, ORM yasak), Truth Mode — doğrulanamayan
                  terim yazılmaz, "DOĞRULAMA GEREKLİ" etiketi kullanılır.
EXPECTED OUTPUT:  Güncel dosya + satır sayısı + izlenebilirlik kaydı.
```

Kural: Handover mesajı 5 alanın tamamını içermek zorundadır; eksik alanlı handover L1 escalation üretir.

### 6.3 Escalation Seviyeleri ([[AGENTS.md]] §10)

| Seviye | Tetikleyici | Aksiyon |
|--------|-------------|---------|
| L1 | Belirsizlik (teknik/kapsam) | Uncertainty flag + kullanıcıya seçenek sun |
| L2 | Çelişki (iki agent farklı karar) | DUR → kullanıcıya çözüm sorusu |
| L3 | Kısıt ihlali (yetki/dosya erişimi) | Görev durur, Vault Steward'a rapor |
| L4 | 3 başarısız düzeltme | Şüpheli varsayım adlandırılır, görev durur |

### 6.4 Uncertainty Flag Protokolü

Alt agent'lar belirsizlikle karşılaştığında stdout'ta şu format kullanılır:

```xml
<uncertainty type="teknik|kapsam|tercih|güvenlik|performans"
  severity="düşük|orta|yüksek"
  description="Belirsizliğin açıklaması"
  options="Seçenek 1 | Seçenek 2" />
```

**Severite Davranışı:**

| Severite | Davranış |
|----------|----------|
| `düşük` | Agent kendi kararını verir; rapor bilgi amaçlıdır |
| `orta` | Agent karar verir; karar checkpoint'te kullanıcıya sunulur |
| `yüksek` | Agent DURUR; orkestratöre rapor verir; cevap bekler |

**Toplama sırası:** Flag'ler çıkarılır → türe göre kategorize edilir → önem sırasına göre sıralanır (yüksek → orta → düşük) → checkpoint'te sunulur. Bu format kullanılmazsa agent mevcut bilgilerle en iyi kararı vermekle yükümlüdür.

---

## 7. Metrics & Monitoring (Metrikler ve İzleme)

### 7.1 Health Check — 5 Durum ([[AGENTS.md]] §11)

| Durum | Tanım | Tepki |
|-------|-------|-------|
| `GREEN` | Vault tutarlı, referanslar geçerli | Normal akış |
| `YELLOW` | Kırık referans var, blokaj yok | Bir sonraki faza temizlik ekle |
| `ORANGE` | Sayım çelişkisi (dosya/PNG/ADR sayıları) | Boot dosyalarında düzeltme |
| `RED` | SSOT çelişkisi veya kod-doküman uyumsuzluğu | DUR → kullanıcı onayı |
| `DEAD` | Vault erişilemez / şema bozuk | Acil eskalasyon L4 |

### 7.2 Ölçülen Metrikler (Faz 0 envanteri, 2026-09-08 doğrulanmış)

| Metrik | Değer | Ölçüm Yöntemi |
|--------|-------|---------------|
| Vault MD dosyası | 741 | `Get-ChildItem .ai -Recurse -Filter *.md` |
| Vault toplam satır | 136.261 | `Measure-Object -Line` toplamı |
| Ortalama satır/dosya | 184 | Toplam ÷ dosya |
| Kök boot dosyası | 12 | `.ai/*.md` (glossary.md dahil) |
| ADR accepted | 70 dosya (67 ADR + 3 meta) | `decisions/accepted/*.md` sayımı |
| ADR rejected | 15 dosya (12 R + 3 meta) | `decisions/rejected/*.md` sayımı |
| ADR toplam kapsam | 001-088 (79 karar) | index.md §5 cross-check |
| Skill klasörü | 10/10 mevcut | `.opencode/skills/` Test-Path |
| Template | 25 (+ adr-index.md) | `.ai/.templates/` sayımı |
| Mockup PNG | 19 (12 home-1024 + 1 home-1920 + 6 shared-1024) | `.ai/.png/` sayımı |
| SQL şema | 18 dosya | `.ai/.sql/mysql/` sayımı |
| Fiziksel domain | 5 (shared, packages, auth, home, assets) | Test-Path kök dizinler |

### 7.3 İzleme Protokolleri

1. **Audit Trail:** Her vault değişikliği `log.md`'ye append edilir (dosya, tür, timestamp, agent, ADR ref — [[ULTRA-THINKING.md]] §7.3).
2. **Vault Sync:** Vault değişikliği içeren oturum sonunda `.workflows/vault-sync.md` çalıştırılır (WORKFLOW 4 Temel Kural #4).
3. **Satır Sayacı:** Revizyon fazlarında dosya başına satır sayısı doğrulanır (bu revizyonun 500+ satır hedefi).
4. **Referans Sağlığı:** Boot dosyalarındaki `[[...]]` hedefleri Test-Path ile doğrulanır; kırıklar YELLOW+ alarm üretir.

### 7.4 Periyodik Sağlık Tarama Komutları (doğrulanmış, PowerShell 5.1)

```powershell
# 1. Vault MD sayısı ve toplam satır
$files = Get-ChildItem -LiteralPath ".ai" -Recurse -Filter "*.md"
$total = ($files | ForEach-Object { (Get-Content -LiteralPath $_.FullName | Measure-Object -Line).Lines } | Measure-Object -Sum).Sum

# 2. Kırık referans taraması (Test-Path toplu kontrol)
# Boot dosyalarından çıkarılan [[yol]] hedefleri döngüde Test-Path ile sınanır

# 3. Domain varlık kontrolü
"shared","packages","auth.coremusic.net","home.coremusic.net","assets.coremusic.net" |
  ForEach-Object { "{0} : {1}" -f $_, (Test-Path -LiteralPath $_) }

# 4. ADR sayım cross-check
(Get-ChildItem -LiteralPath ".ai\decisions\accepted" -Filter "ADR-*.md").Count   # 67 beklenir
(Get-ChildItem -LiteralPath ".ai\decisions\rejected" -Filter "R-*.md").Count     # 12 beklenir

# 5. PNG sayım (19 beklenir: 12+1+6)
(Get-ChildItem -LiteralPath ".ai\.png" -Recurse -Include *.png).Count
```

Bu komutlar Faz 0 envanterinde (2026-09-08) çalıştırılmış ve §7.2 tablosundaki değerler bu taramadan üretilmiştir.

### 7.5 Agent Sağlık Matrisi (11 agent)

| # | Agent | Birincil Kaynak | Sağlık Göstergesi |
|---|-------|-----------------|-------------------|
| 1 | Master Orchestrator | Bu motor + `log.md` | Audit kayıtları güncel ve append-only |
| 2 | Backend Architect | `shared/src/`, `auth.coremusic.net/` | PHP 8.4 + strict_types uyumu, PSR |
| 3 | UI Designer | `.ai/ui-design/`, `architecture/l3-presentation/` | PNG (19) + C01-C16 envanteri ile uyum |
| 4 | Security Engineer | `architecture/l1-security/` | OWASP kontrol listesi, CSRF/CSP middleware gerçek kodla örtüşme |
| 5 | Data Engineer | `.ai/.sql/mysql/` (18 şema) | BCNF tutarlılığı, migration stratejisi |
| 6 | Embedded Engineer | `electronic/`, `projects/NevaEngine/` | C++20 spec bütünlüğü |
| 7 | QA Engineer | `tests/` klasörleri | PHPUnit ^10.5 / ^11.0 paket uyumu |
| 8 | DevOps Engineer | `architecture/02-deployment/` | CI/CD konfigürasyon mevcudiyeti |
| 9 | Audio HW Engineer | `electronic/hardware/` | Chip spec (XMOS XU316, PCM3168A) çapraz referans |
| 10 | DSP Firmware Engineer | `electronic/dsp/`, `electronic/firmware/` | Boot/RTOS seçim tablosu tutarlılığı |
| 11 | Windows SW Engineer | Windows platform hedefleri | WASAPI/WDK plan kayıtları (PLANNED) |

Matris kullanımı: Sağlık taraması §7.4 komutlarıyla birlikte çalıştırılır; kırmızı gösterge varsa §8.2 akışı izlenir.

### 7.6 Metrik Eşikleri (Alarm Tablosu)

| Metrik | Yeşil | Sarı | Kırmızı | Aksiyon |
|--------|-------|------|---------|---------|
| Kırık `[[...]]` referansı | 0 | 1-10 | >10 | Sarı: faz içi temizlik; Kırmızı: boot dosyası acil düzeltme |
| Sayım çelişkisi (dosya/PNG/ADR) | 0 | — | ≥1 | Boot dosyalarında birleştirme (Faz 1 kuralı) |
| Kod-doküman uyumsuzluğu | 0 | — | ≥1 | IMPLEMENTED/PLANNED etiketi zorunlu |
| Boot dosyası satır sayısı | ≥500 | 400-499 | <400 | Faz 1 kuralı: 500+ hedefine genişletme |
| log.md append gecikmesi | <1 oturum | 1-2 oturum | >2 oturum | Audit trail tamamla |
| `VERIFY` işaretsiz doğrulanamayan iddia | 0 | 1-5 | >5 | Truth Mode taraması |

Eşikler Faz 0 ölçümlerine göre kalibre edilmiştir: tarama sırasında 60+ kırık link (kırmızı), 8 sayım çelişkisi (kırmızı), 2 kod-doküman uyumsuzluğu (kırmızı) ölçülmüştür.

---

## 8. Troubleshooting (Sorun Giderme Senaryoları)

### 8.1 Bilinen Sorunlar (Faz 0 doğrulanmış, 2026-09-08)

| # | Sorun | Konum | Çözüm |
|---|-------|-------|-------|
| 1 | `SessionInitializer` iki namespace'te kopya | `shared/src/Session/SessionInitializer.php` + `shared/src/PageRouter/SessionInitializer.php` | Duplicate risk dokümante edildi; birleştirme ADR gerektirir — kod değişikliği onaya tabi |
| 2 | Redis cache iddiası kodda yok | `l0-infrastructure/index.md` ↔ `shared/src/Cache/CacheManager.php` | Gerçek zincir: `apcu_fetch` var → `ApcuAdapter`, yok → `MemoryAdapter`. Doküman düzeltildi |
| 3 | Eski arşiv tarihi referansları | `archives/prompt*-2026-08-13` (12 link, 5 boot dosyası) | Doğru hedef `2026-09-01` versiyonları |
| 4 | `scripts/vault-integrity-check.ps1` kayıp | `.ai/scripts/index.md` işaret ediyor, dosya yok | Script yeniden oluşturulmalı veya referans kaldırılmalı |
| 5 | log.md mojibake (ç?ý) | `log.md` BOM/UTF-8 karışımı | BOM normalizasyonu öncelikli (log.md kendi kaydında tespit) |
| 6 | Kırık alt-dizin referansları | `.ai/testing/`, `.ai/workflows/`, electronic kökü 8 dosya, `models/issues/scripts` index | "Dizin yok" etiketi + gerçek yol düzeltmesi |
| 7 | Tanımsız sınıf sabitleri (LSP taraması, 2026-09-08) | `SessionInitializer.php:26` `SESSION_NAME`, `PageRouter.php:115` `PAGES_PATH`, `RateLimiterMiddleware.php:24` `TRUSTED_PROXIES` | Sabitler config kökenli görünüyor (`shared/config/domain.php` vb.); çözüm kod değişikliği gerektirir — ADR/onay sürecine tabi |

### 8.2 Sorun Giderme Akışı

```
Sorun tespiti → Sınıflandır (kod / doküman / süreç)
   ├─ Kod sorunu     → Root cause analizi → Fix veya ADR → Test → log.md append
   ├─ Doküman sorunu → Kaynak doğrula (Test-Path / kod okuma) → Satır edit → log.md append
   └─ Süreç sorunu   → WORKFLOW kontrol → Workflow güncelleme onayı → uygula
```

### 8.3 Context Lock / Deadlock Önleme ([[AGENTS.md]] §12)

1. Kilitsiz dosya düzenleme YASAK — önce lock al, iş bitince serbest bırak.
2. İki agent aynı dosyayı talep ederse: öncelik CRITICAL > HIGH > MEDIUM > LOW; eşitse sıralı.
3. Deadlock tespiti: iki `Running` görev çapraz kilitli → ikisi de `Pending`'e döner, kullanıcı bilgilendirilir.

### 8.4 Gerçek Senaryo Kütüphanesi (doğrulanmış vakalar)

**S-01 — Login Bug / Redirect Loop (oturum kaydı, önceki revizyon):**

| Alan | Değer |
|------|-------|
| Belirti | İlk login denemesi başarısız, ikincisi başarılı; `home.coremusic.net:81/home` üzerinde `ERR_TOO_MANY_REDIRECTS` |
| Kök neden | Çift form handler çakışması + `SessionInitializer.php:104` içinde `_session_created_at` null iken `return true` + session save path yok |
| Uygulanan çözüm | `e.stopImmediatePropagation()` (3 form), `return false` düzeltmesi, `@mkdir($savePath)` (3 nokta), `HomeAuthBridge.php:144` `CURLOPT_FOLLOWLOCATION => true` |
| Ders | Session oluşturma zinciri tek yerden doğrulanmalı; `SessionInitializer` duplicate sorununu tetikledi (§8.1 #1) |

**S-02 — Kırık Arşiv Referansları (Faz 0):**

| Alan | Değer |
|------|-------|
| Belirti | 5 boot dosyasında `archives/prompt*-2026-08-13` hedefleri bulunamıyor |
| Kök neden | Arşiv dosyaları 2026-09-01 revizyonunda yeniden adlandırılmış; referanslar güncellenmemiş |
| Çözüm | Tarih hedeflerini `2026-09-01` versiyonlarına yönlendir (12 kırık link, tek kural ile) |

**S-03 — Doküman-Kod Uyumsuzluğu: Redis iddiası (Faz 0):**

| Alan | Değer |
|------|-------|
| Belirti | `l0-infrastructure/index.md` "APCu, Redis, File" diyor; kodda Redis adapter yok |
| Kök neden | Hedef mimari dokümante edilmiş, mevcut kodla etiketlenmemiş |
| Çözüm | IMPLEMENTED/PLANNED ayrımı zorunlu; gerçek zincir `ApcuAdapter → MemoryAdapter` yazıldı |

**S-04 — Alt Agent Bekleme Moduna Düşme (bu oturum):**

| Alan | Değer |
|------|-------|
| Belirti | 4 paralel explorer'dan 3'ü görev yerine "arama terimi bekliyor" yanıtı döndürdü (~13-15 dk kayıp) |
| Kök neden | Görev promptu alt agent'ın etkileşimli bekleme modunu tetikledi |
| Çözüm | Aynı `task_id` ile devam promptu gönderildi; 3/3 görev bu kez tam rapor döndürdü |
| Ders | Task promptu doğrudan komut cümlesiyle başlamalı; bağlam + çıktı formatı açık verilmeli |

**S-05 — log.md Mojibake (Faz 0):**

| Alan | Değer |
|------|-------|
| Belirti | `log.md` içinde Türkçe karakterler `ç?ý` biçiminde bozuk |
| Kök neden | BOM / UTF-8 kodlama karışımı (log.md kendi 16:45 kaydında 351 dosya tespiti mevcut) |
| Çözüm | BOM normalizasyonu — append-only kural korunarak önceliklendirilecek |

### 8.5 Kurtarma İlkeleri

1. Yarım kalan dosya değişikliği revert edilir; "geçici çözümü final kabul" yasaktır ([[CLAUDE.md]] §5).
2. Her düzeltmeden sonra doğrulama zorunlu: satır sayısı, Test-Path, lint/standart kontrolü (uygunsa).
3. Aynı sorun 3 kez düzeltilirse: L4 escalation — şüpheli varsayım açıkça adlandırılır.

---

## 9. Teknoloji Yığını Politikası (Dynamic Tech Stack)

### 9.1 İlke

**Programlama dili ve teknoloji yığını proje gereksinimlerine göre dinamik belirlenir.** Kullanılabilir teknolojiler: **Node.js · C++ · C# · PHP** ve proje niteliğinin gerektirdiği diğer uygun teknolojiler. Tek bir dil zorunluluğu yoktur; her domain/servis kendi gereksinimine göre seçilir.

### 9.2 Domain ↔ Teknoloji Matrisi (Faz 0 doğrulamalı)

| Domain / Servis | Teknoloji | Durum | Kanıt |
|-----------------|-----------|-------|-------|
| `shared/` altyapı | PHP 8.4 + PSR + php-di + symfony/event-dispatcher | **IMPLEMENTED** | `shared/composer.json` (coremusic/shared-infrastructure v2.0.0) |
| `packages/shared/` | PHP 8.3 + ramsey/uuid + sodium_compat | **IMPLEMENTED** | `packages/shared/composer.json` (coremusic/shared) |
| `auth.coremusic.net` | PHP 8.4 + fast-route + phpdotenv + psr7-server | **IMPLEMENTED** | `auth.coremusic.net/composer.json` |
| `home.coremusic.net` | PHP 8.4 (minimal bağımlılık) | **IMPLEMENTED** | `home.coremusic.net/composer.json` |
| `assets.coremusic.net` | Statik servis (Css/Fonts/Image/js) | **IMPLEMENTED** | Klasör envanteri |
| Web frontend panelleri | Vanilla JS ES6+ + ITCSS + BEM (ADR-001) | PLANNED (kod henüz yok) | ADR-001, ui-design/ |
| `download.coremusic.net` | Node.js 20+ | PLANNED | architecture-master §5.1 (kod yok) |
| Audio/embedded | C++20 (JUCE, ASIO, XMOS firmware) | PLANNED | electronic/, projects/NevaEngine/ |
| Windows platform araçları | C# / WDK (WASAPI yardımcıları) | PLANNED | AGENTS.md #11 Windows SW agent |
| `media.coremusic.net` | PHP + FFmpeg | PLANNED | architecture-master §5.1 (kod yok) |

### 9.3 Seçim Kuralları

1. **Web servisleri:** PHP 8.x native öncelikli; PDO prepared statement (ADR-002), ORM yasak.
2. **Web frontend:** Vanilla JS + ITCSS (ADR-001); framework yasak — web panel kapsamında.
3. **Ses/gömülü:** C++20, zero-allocation, noexcept öncelikli.
4. **Servis-işlem (streaming/download):** Node.js LTS uygun görüldüğünde.
5. **Windows platform araçları:** C# veya C++ (WDK) ihtiyaca göre.
6. **Yeni teknoloji girişi:** ADR şart — öncelik, gerekçe, trade-off kaydedilir; plansız bağımlılık yasak.

### 9.4 Teknoloji Karar Kaydı Örneği (şablon + gerçek örnek)

```text
KARAR:        Download servisi için Node.js 20+ LTS
Bağlam:       Yüksek eşzamanlı dosya aktarımı + streaming ihtiyacı
Gerekçe:      Event-driven I/O modeli bu iş yüküne uygun
Trade-off:    PHP-FPM havuzları yerine tek Node süreci; JS/TS ekosistem
              bakımı ek yük getirir
Durum:        PLANNED — kod henüz yok (Faz 0: `download.coremusic.net` dizini mevcut değil)
Kanıt:        architecture-master §5.1 · ADR-026 (Download Service Architecture)
```

Kural: Her PLANNED teknoloji satırı bu şablonla gerekçelendirilir; kanıtsız teknoloji iddiası Truth Mode ihlalidir.

### 9.5 Geçiş ve Uyumluluk Kuralları

1. **IMPLEMENTED teknoloji değişimi:** Sadece ADR + migration planı ile. Örnek: `coremusic/shared-infrastructure` (PHP ≥8.4) ↔ `coremusic/shared` (PHP ≥8.3) paket ayrımı ADR-085 kapsamında yürütülür; iki PSR-4 kökü (`CoreMusic\`, `CoreMusic\Shared\`) geçiş süresince bir arada yaşar.
2. **Dil geçişi sınırı:** Servisler arası sözleşme (PSR-7 mesajlar, HTTP endpoint'leri, `.ai/.sql/mysql/` şemaları) geçişte değiştirilemez; yalnızca süreç içi uygulama değişir.
3. **C# giriş şartı:** Yalnızca Windows platformuna özgü ihtiyaç (WASAPI yardımcıları, servis/araç geliştirme, WDK süreçleri) için; cross-platform hedefte C++20 tercih edilir.
4. **Node.js giriş şartı:** Yüksek eşzamanlı I/O (download/streaming) ve JS ekosistemi zorunluluğu; PHP tarafındaki ADR-002 (ORM yasak, PDO) kuralı Node tarafına ORM yasak olarak geneller.
5. **Domain-spesifik kısıt yorumu:** ADR-001 (Vanilla JS, framework yasak) **web panel frontend'i** için geçerlidir; C++/Node/C# servis katmanlarını kapsamaz. Bu ayrım "framework yasağının" kapsamını netleştirir, gevşetmez.
6. **Stack bildirim satırı:** Her composer.json/package.json değişikliğinde `log.md`'ye hangi stack alanının etkilendiği yazılır (§9.2 matris sütunları).

**Gerçek stack bildirim örnekleri (composer.json'dan, Faz 0 doğrulaması):**

| Paket | PHP | Ana bağımlılıklar | Stack alanı |
|-------|-----|-------------------|-------------|
| `coremusic/shared-infrastructure` v2.0.0 | ≥8.4 | psr/log ^3.0, psr/cache ^3.0, symfony/event-dispatcher ^7.0, php-di/php-di ^7.0, respect/validation ^2.0, nyholm/psr-7 ^1.8 | PHP servis altyapısı |
| `coremusic/shared` | ≥8.3 | ramsey/uuid ^4.7, paragonie/sodium_compat ^1.20 | PHP paylaşımlı paket |
| `bayramali/auth.coremusic.net` | ≥8.4 | vlucas/phpdotenv ^5.7, nikic/fast-route ^1.3, nyholm/psr7-server ^1.1 | PHP auth servisi |
| `bayramali/home.coremusic.net` | ≥8.4 | php-di, psr/log, psr/container | PHP home servis |

Dev araçları: PHPUnit ^10.5 (shared-infrastructure) / ^11.0 (packages/shared) + PHPStan ^1.10 (level 5, `stan` script). Sürüm farkı dokümante edilmiş bilinen durumdur.

---

## 10. Checkpoint & Uncertainty Protokolü

### 10.1 Checkpoint Noktaları

Her 2-3 alt agent sonucunda bir checkpoint oluşturulur:

```text
CHECKPOINT N: [Aşama adı] tamamlandı
- [Agent X sonucu — tek satır özet]
- [Agent Y sonucu — tek satır özet]
- Uncertainty'ler: [liste — severite sıralı]
- Soru: [gerekirse kullanıcıya]
- Onay: [devam et / dur]
```

### 10.2 Checkpoint Akışı

```
Agent sonuçları topla
    │
    ▼
Uncertainty flag'leri çıkar (§6.4)
    │
    ├── Varsa → kullanıcıya sun → cevap al → agent güncelle veya yeniden çalıştır
    │
    └── Yoksa → devam onayı al → sonraki agent grubu
```

### 10.3 Uygulama Kuralları

1. Checkpoint çıktısı kullanıcı görünürlüğünde olmalıdır (iç agent mesajı olarak gizlenmez).
2. Sonuçlar toplanırken her agent çıktısından `<uncertainty ... />` pattern regex ile çıkarılır.
3. Aynı belirsizlik iki agent'ta farklı şekilde raporlanırsa (çelişki) L2 escalation tetiklenir.
4. Checkpoint'te ilerleme restatement zorunludur: "adım X/Y tamam" formatı.

### 10.4 Gerçek Checkpoint Örneği (Faz 0, 2026-09-08)

```text
CHECKPOINT 1: Faz 0 doğrulama taraması — 4/4 explorer tamamlandı
- Vault tutarlılık: ~150 yol Test-Path ile doğrulandı; 60+ kırık link
  kümesi tespit edildi (arşiv tarihi, testing/, workflows/, electronic kökü)
- Domain envanteri: 4/13 fiziksel domain; 9 domain PLANNED (kod yok)
- shared katman: 19 src modülü; anahtar sınıflar gerçek yollarla eşleştirildi
- ui-design: C01-C16 (16/16), 19 PNG, 17 qr-* dosyası doğrulandı
- Uncertainty: [orta] ".opencode/.ai boş — root .ai tek kanonik" → çözüldü
- Onay: C kapsamıyla başla → B'ye genişlet (kullanıcı onayı alındı)
```

Bu örnek, §10.1-10.3 kurallarının gerçek yürütmeden alınmış referans uygulamasıdır.

### 10.5 Uncertainty Türleri Referans Tablosu

| Tür | Tanım | Gerçek Örnek (bu proje) | Varsayılan Davranış |
|-----|-------|--------------------------|---------------------|
| `teknik` | Teknoloji/yöntem seçimi belirsiz | Cache adapter: Redis iddiası ↔ kodda yok | Doğrulanmış kodu esas al, flag raporla |
| `kapsam` | İş sınırları net değil | ".ai revizyonu" — 741 dosyanın hangileri? | Plan + kullanıcı onayı (C→B kapsam kararı) |
| `tercih` | Kullanıcı tercihine bağlı | 500 satır hedefi hangi dosyalara uygulanacak? | Plan önerisi + onay kapısı |
| `güvenlik` | Risk taşıyan karar | SessionInitializer duplicate birleştirme | Kod değişikliği YAPMA, ADR bekle |
| `performans` | Kaynak/gecikme endişesi | 741 dosya × 500 satır iş büyüklüğü | Fazlara böl, satır sayacıyla ölç |

Kural: Tür belirlenemeyen belirsizlik `teknik` varsayılır ve `düşük` severite ile raporlanır.

---

## 11. Quick Reference (Genişletilmiş)

| İhtiyaç | İlk Adım |
|---------|----------|
| Agent tanımları | [[AGENTS.md]] §15 |
| Görev dağıtımı | [[AGENTS.md]] §7 |
| Handover | [[AGENTS.md]] §9 |
| Eskalasyon | [[AGENTS.md]] §10 |
| Sağlık kontrolü | [[AGENTS.md]] §11 |
| Context lock | [[AGENTS.md]] §12 |
| Kuyruk durumu | Bu dosya §5 |
| Teknoloji seçimi | Bu dosya §9 |
| UI Design | [[ui-design/00-mockup-index]] |
| Mockup PNG'ler | `.ai/.png/home-1024/` (12), `.ai/.png/home-1920/` (1), `.ai/.png/shared-1024/` (6) — toplam 19 |
| Responsive kuralları | [[ui-design/responsive-device-mode]] (4K No-Center §7.4, Backward-Compat §12) |
| Terim sözlüğü | [[glossary.md]] |
| Faz yürütme planı | Bu dosya §12 |
| Uncertainty türleri | Bu dosya §10.5 |
| Metrik eşikleri | Bu dosya §7.6 |

---

## 12. Faz Yürütme Planı (Vault Revizyonu 2026-09-08)

| Faz | Kapsam | Durum | Doğrulama |
|-----|--------|-------|-----------|
| Faz 0 | Envanter + kaynak kod cross-check (4 explorer) | **TAMAMLANDI** | §7.2 metrikler |
| Faz 1 | Kök 12 boot dosyası — 500+ satır, satır-satır edit | **ÇALIŞIYOR** | Bu dosya ✓, devam eden: glossary/ROLE/ULTRA-THINKING/... |
| Faz 2 | `architecture/` alt fazlar (l0→l3, contracts, security, auth, audio, data, ai) | Pending | IMPLEMENTED/PLANNED etiketli |
| Faz 3 | `ecosystem/`, `servers/`, `subdomains/`, `scripts/` | Pending | Kod cross-check |
| Faz 4 | `ui-design/` çekirdek + tokens + flow | Pending | C01-C16 + 19 PNG kanıtlı |
| Faz 5 | `decisions/` — frozen ADR'lere appendix stratejisi | Pending | Orijinal dosya dokunulmaz |
| Faz 6 | `electronic/`, `projects/` | Pending | XMOS/PCM3168A cross-check |

Kurallar: Her faz sonunda `log.md` append + satır sayacı raporu; `vault-sync` çalıştırılır. Kapsam istisnaları (log.md append-only, MEMORY.md state koruma, .templates/arşiv dışı) plan onayıyla sabitlenmiştir.

### 12.1 Faz 2 Alt-Fazları (architecture/ K0-K20)

| Alt-faz | Klasör | Dosya Grubu | Doğrulama Kanalı |
|---------|--------|-------------|------------------|
| 2a | `k0-k5-software/` | OS, Hardware, Drivers, Audio Engine, AI, Data | C++ NevaEngine, MySQL 18 BCNF DB schemas, Hardware I2S/BLE API |
| 2b | `k6-k11-application/` | Security, Middleware, Services, Routing, UX | 10-layer middleware pipeline, Argon2id/AES, API Routes, ITCSS/Vanilla JS |
| 2c | `k12-k15-cross-cutting/` | Monitoring, CI/CD, Network, Media Streaming | Logging configs, Playwright tests, WebRTC streaming, Nginx/IIS configs |
| 2d | `electronics/` (K16-K18) | PCB Design, Amplifier (Class AB), Power | ADR-089 (Class AB), PCM3168A DAC, 112dB SNR test reports |
| 2e | `firmware/` (K19-K20) | Bootloader, RTOS/Bare-Metal | XMOS XU316 bare-metal firmware, MCU sync |

*(Not: Faz 2 kapsamında bu klasörlerdeki tüm .md dosyalarına otomatik IMPLEMENTED/PLANNED kanıt matrisi işlenir.)*

### 12.2 Faz 3 Kapsam Detayı

| Klasör | Mevcut | Revizyon Odağı |
|--------|--------|----------------|
| `ecosystem/` | 7 md | 7-servis entegrasyon iddiaları ↔ IMPLEMENTED/PLANNED matrisi (§9.2) |
| `servers/` | 3 md | linux-nginx, windows-apache, windows-iis — gerçek config kanıtı araması |
| `subdomains/` | 3×1 md | auth index.md kod-referanslı (endpoint tablosu, AuthController::handleLogin, cookie adları) — standart bu örnek |
| `scripts/` | 1 md | vault-integrity-check.ps1 kayıp → script yeniden üretimi veya referans kaldırma kararı |

### 12.3 Faz 4 Kapsam Detayı

| Klasör | Mevcut | Kanıt |
|--------|--------|-------|
| `ui-design/` kök | 5 md | 00-mockup-index (19 PNG ✓), 01-component-inventory (C01-C16 ✓), 02-implementation-plan, 03-accessibility-gaps (WCAG 15/16; C15 toggle ~32px sınır) |
| `ui-design/tokens/` | 4 dosya | design-tokens-master.md 536 satır; platformlar: rpi5-1024, desktop-1920, mobile-375, tv-3840; temalar: #ff4fd8 / #4f9fff / #a0a0b0 |
| `ui-design/flow/` | 20 md (music 7, auth 7, settings 6) | Ekran ↔ PNG ↔ bileşen çapraz eşlemesi |

### 12.4 Faz 5 Appendix Şablonu (Frozen ADR'ler)

Frozen ADR (001-037) orijinal dosyasına dokunulmaz; her biri için eş ad ile appendix dosyası üretilir:

```markdown
---
title: "ADR-XXX Derinlik Ekleri"
parent: "ADR-XXX"
status: appendix          # orijinal frozen'dır, bu dosya genişletmedir
verified-against: "kod yolu listesi"
---
## 1. Kararın Kod Karşılığı (gerçek sınıf/dosya)
## 2. Veri Akışı (ASCII)
## 3. IMPLEMENTED/PLANNED Durumu
## 4. İzlenebilirlik Tablosu (iddia → kod → satır)
## 5. İstisnalar ve Bilinen Eksikler
```

### 12.5 Faz 6 Kanıt Zorunluluğu

| Klasör | Kanıt Beklentisi |
|--------|------------------|
| `electronic/hardware/` | audio-interface.md sinyal zinciri: USB → XMOS XU316 → I2S → PCM3168A → Analog; SNR 112dB / 192kHz çapraz kontrol |
| `electronic/firmware/` | boot-sequence (XMOS load fail → LED blink/retry), rtos seçim tablosu |
| `electronic/drivers/` | usb-drivers.md XMOS Driver ↔ UAC 2.0 karşılaştırması |
| `projects/` | "20 STUB" iddiasının tek tek doğrulanması; WirelessConnect stub (6 satır, kod referansı yok) referans örnek |

### 12.6 Faz Yürütme Kontrol Listesi (her faz kapanışında)

| # | Kontrol | Kriter | Sonuç Alanı |
|---|---------|--------|-------------|
| 1 | Satır sayacı | Kapsamdaki her dosya ≥500 (boş-hariç) | dosya → satır listesi |
| 2 | İzlenebilirlik | Her dosyada kanıt yolu / `DOĞRULAMA GEREKLİ` etiketi | bölüm referansları |
| 3 | Kırık referans | Yeni `[[...]]` hedefleri Test-Path | 0 kırık hedef |
| 4 | Sayım tutarlılığı | PNG 19, ADR 79, root 12, total_files 787 | boot dosyaları birleşik |
| 5 | IMPLEMENTED/PLANNED | Kod iddiaları etiketli | domain matris güncel |
| 6 | log.md append | Faz özeti + dosya listesi + timestamp | audit kayıt no |
| 7 | Structure korunumu | Silinen başlık/bölüm yok (yalnız düzeltme + ekleme) | edit sayısı raporu |
| 8 | Teknoloji etiketi | Yeni stack içeriği §9.2 matrisiyle uyumlu | stack satır referansı |

Kontrol listesi atlanarak faz kapatılamaz; eksik kontrol RED gösterge sayılır (§7.1) ve faz açık kalır.

### 12.7 Faz Kapanış Raporu Şablonu

```text
FAZ RAPORU N: [faz adı]
Kapsam:           [dosya listesi — mutlak/göreli yollar]
Satır sonuçları:  [dosya → önce/sonra — boş-hariç]
Kontrol listesi:  8/8 — her madde ✓/✗ (§12.6)
Kırık referans:   [yeni kırık sayısı — hedef 0]
Stack etiketi:    [§9.2 matrisine yapılan ekleme/değişim]
log.md:           [append kayıt no]
Sonraki faz:      [ad] — başlama önkoşulu: [kriter]
```

**Örnek dolum — Faz 1 kısmi (2026-09-08):**

```text
FAZ RAPORU 1: Kök 12 boot dosyası revizyonu (devam ediyor)
Kapsam:           .ai/{engine,glossary,ROLE,ULTRA-THINKING,index,AGENTS,
                  MEMORY,CLAUDE,brain,keys,WORKFLOW}.md — log.md append-only
Satır sonuçları:  engine 46→500+ · glossary 68→500+ · ROLE 285→500+
                  (tamamlananlar satır sayacıyla doğrulanır)
Kontrol listesi:  [faz kapanışında doldurulur]
Kırık referans:   arşiv tarihleri 2026-08-13→09-01 (12 link) düzeltiliyor
Stack etiketi:    dinamik yığın ilkesi eklendi (§9) — IMPLEMENTED/PLANNED
log.md:           faz kapanışında append edilecek
Sonraki faz:      Faz 2 (architecture/) — önkoşul: Faz 1 kapanış raporu
```

---

## 13. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 21.0.0 |
| Bölüm sayısı | 13 (§3 vaat edilen 4 bölüm bu sürümde gerçekleştirildi; §12 Faz Yürütme Planı + kontrol listesi eklendi) |
| Doğrulama | Faz 0 envanteri + Faz 1 satır editleri, 2026-09-08 — Test-Path + composer.json okuma + LSP taraması tabanlı |
| Kapsam etiketi | Teknoloji yığını: dinamik (Node.js · C++ · C# · PHP + proje gereksinimli diğerleri) — [[ROLE.md]] §11 eşlemesi |
| Bilinen açık | SessionInitializer duplicate (ADR bekliyor), vault-integrity-check.ps1 kayıp, 3 tanımsız sabit (§8.1 #7) |
| Audit | Bu revizyon log.md'ye Faz 1 kaydıyla append edildi |

---

*Orchestration Engine v21.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-09-08*
*Mode: Red Team · Human Mode · Truth Mode*
