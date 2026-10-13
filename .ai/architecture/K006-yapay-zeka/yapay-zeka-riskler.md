---
node_id: K006-yapay-zeka-riskler
node_type: domain
status: verified
parent_id: "K000–K5000 Architecture Registry"
children:
  - K006.1-audio-intelligence
  - K006.2-evaluation
  - K006.3-inference
  - K006.4-local-inference
  - K006.5-model-management
  - K006.6-music-analysis
  - K006.7-personalization
  - K006.8-recommendation
  - K006.9-testing
depends_on:
  - K004-shared
  - K016-AI
evidence:
  - "shared/src/AI/AIEngine.php L49–77, L84–86, L209–218, L315–330 (2026-10-12)"
  - "shared/src/AI/AIWorkflow.php L49–91 (JSON_THROW_ON_ERROR L70, prompt L66–72)"
  - "shared/composer.json — log/monitoring paket yokluğu (2026-10-12)"
validation: "2026-10-12 · 14 risk, her biri dosya+satır kanıtı + etki + azaltma ile · status=verified"
open_questions:
  - "KVKK Üretken YZ rehberinin K006'ya uygulanacak maddeleri ayrı turda okunacak (⚠️ VERIFICATION REQUIRED)"
  - "KIŞI/oturum verisinin nereye yazıldığı (memory->setSession) — alt depo konumu UNKNOWN"
group: kanit
---

# K006 — Yapay Zeka · Riskler

> Her risk: ID · kanıt (dosya+satır) · etki · olasılık · azaltma. Kanıtsız risk yazılmaz;
> kod gözlemi olmayanlar `⚠️`/`UNKNOWN` ile işaretlidir. Sıralama: etki × olasılık.

---

## 1. Ürün Riskleri (kod kanıtlı)

### R-01 · Öneri hattı fiilen boş — **KRİTİK**
- **Kanıt:** `AIEngine.php` L209–218 (`getCollaborativeScores` → `[]`) · L223–235
  (`getContentBasedScores` → `[]`) · L65 (hibrit `0.6/0.4`) · L71 (`MIN_SCORE=0.7`).
- **Etki:** iki girdi de boşsa `combineScores([], [], …)` → filtre → sonuç **boş liste**.
  Öneri özelliği kullanıcıya "sonuç yok" döndürebilir.
- **Olasılık:** yüksek (mevcut kodda daima).
- **Azaltma:** CF/content kolunun doldurulması (K006.8 öncelikli açık) veya `MIN_SCORE`
  altındaki geçici fallback — **her ikisi de tasarım onayı ister** (kod gömülü).
- **Çocuk:** K006.8-recommendation.

### R-02 · Ses analizi tüm dosyalarda aynı — **YÜKSEK**
- **Kanıt:** `AIEngine.php` L315–330 — `extractFeatures` sabit döner; `$filePath` kullanılmaz.
- **Etki:** `analyzeAudio` her dosya için `bpm=120.0, key=C, energy=0.7` üretir (L94–105).
  K006.1/K006.6 çıktıları gerçekçi görünür ama **yanlıştır** (sessiz hata).
- **Olasılık:** yüksek (daima).
- **Azaltma:** C++ DSP engine bağlantısı (yorum L317) veya çıktıyı açıkça `placeholder` olarak
  işaretleyen API katmanı.
- **Çocuk:** K006.1 · K006.6.

### R-03 · Uydurma enstrümantasyon beklentisi — **YÜKSEK**
- **Kanıt:** repo grep 2026-10-12: `AIPerformanceMonitor` · `AIQualityManager` · `AICache` ·
  `AIRetryManager` · `AIRequestValidator` → **0 eşleşme**; `monolog` composer.json'da yok.
- **Etki:** "metrik/retry/önbellek/doğrulama katmanı var" varsayımıyla plan yapılırsa
  **kanıtsız mimari** oluşur. Aynı hata bu revizyonun önceki turunda gerçekleşti
  (bkz. `yapay-zeka-bilesenler.md` §9).
- **Azaltma:** tüm envanter iddiaları disk grep'i ile bağlanmış durumda (doğrulandı).
- **Çocuk:** K006.2 · K006.3.

### R-04 · Hata yönetimi fırlatma tabanlı — **ORTA**
- **Kanıt:** `AIEngine.php` L84–86 (`throw new \RuntimeException("Audio file not found: …")`).
- **Etki:** proje standardı Result/Either iken AI yüzeyi exception fırlatır; çağıran taraf
  yakalamazsa 500 üretir (AIWorkflow içinde try/catch **görülmedi** — L49–91 tam okundu).
- **Azaltma:** AI çağrılarını saran Result sarmalayıcısı (K006.3 tasarımı).
- **Çocuk:** K006.3-inference · K006.9-testing.

### R-05 · `analyzeAudio` sözleşme üstü genişlik — **ORTA**
- **Kanıt:** Contracts `AIEngineInterface.php` L34 (`@return` 6 alan) vs AIEngine L94–105 (10 alan).
- **Etki:** daraltan çağıran yok; ileride şema değişimi kırıcı olur.
- **Azaltma:** docblock'ın implementasyonla hizalanması (düşük efor, sözleşme değişikliği).
- **Çocuk:** K006.1 · K006.9.

---

## 2. Mimari / Sınır Riskleri

### R-06 · K016 bağımlılığı K006 akışının içinde — **ORTA**
- **Kanıt:** `AIWorkflow.php` L63 `$this->knowledge->search(...)` · L66 `promptEngine->
  generatePrompt(...)` · sınıf L20–41 (6 somut bağımlılık).
- **Etki:** K006.8 akışı K016'daki değişikliklerden doğrudan etkilenir; K016 refaktörü K006'yu
  kırabilir. Registry §6/94'e göre iki domain ayrı — bu çağrı **kayıtlı** ama sözleşmesiz.
- **Olasılık:** orta (K016 aktif geliştirilirse).
- **Azaltma:** K006↔K016 veri sözleşmesi ADR'si (kararlar Q5).
- **Çocuk:** K006.8 · kök.

### R-07 · `shared/src/AI/CLAUDE.md` kapsam ihlali — **DÜŞÜK**
- **Kanıt:** dizinde 1 CLAUDE.md, K016 sınıflarını listeler (envanter `yapay-zeka-bilesenler.md` §3).
- **Etki:** K006 dizini içinde K016 dokümanı — iki domain'in dokümanı karışır.
- **Azaltma:** K016 vault'una taşınması (dizin/yazı yetkisi bu görevin dışında — raporlandı).

### R-08 · Öneri sahipliği belirsizliği — **ORTA (yönetim)**
- **Kanıt:** registry §6 (K006 ↔ K008 ↔ K020 üçlüsü) · `ai-master.md` §7/2.
- **Etki:** iki domain aynı özelliği ayrı ayrı tasarlayabilir → duplicate implementasyon.
- **Azaltma:** ADR ile sahiplik ataması (kararlar Q1); tekrar: **kesin sahiplik iddiası yasak**.

### R-09 · Ölü/değişken kod yüzeyi — **DÜŞÜK**
- **Kanıt:** `AIEngine.php` L53 (`$sessionId`) gövdede kullanılmaz · L315 `$filePath` kullanılmaz ·
  L41 `getConfig()` interface dışı genel erişim.
- **Etki:** bakım kirliliği; L315 ayrıca R-02'nin kaynağıdır.
- **Azaltma:** dead-code temizliği (yetkili: Backend Architect; bu görev yalnız raporlar).

---

## 3. Veri / Uyum Riskleri

### R-10 · KVKK Üretken YZ rehberi okunmadı — **YÜKSEK (uyum)**
- **Kanıt:** `https://www.kvkk.gov.tr/Icerik/8547/Uretken-Yapay-Zeka-Rehberi` doğrulandı
  (`yapay-zeka-dogrulama.md` §2-14) ama **maddeleri özetlenmedi** (web-arastirma open_questions).
- **Etki:** kişiselleştirme/öneri (profil verisi) için uyumluluk gereksinimleri çıkarılmadı;
  `⚠️ VERIFICATION REQUIRED`.
- **Azaltma:** ayrı araştırma turu; K006.7/K006.8 tasarım gate'i olarak okunmalı.
- **Çocuk:** K006.7 · K006.8 · kök.

### R-11 · Oturum verisi yazım yeri bilinmiyor — **ORTA**
- **Kanıt:** `AIWorkflow.php` L78 `memory->setSession($sessionId, 'last_recommendations', …, 3600)`.
- **Etki:** öneri geçmişi nereye yazılıyor (APCu/dosya/DB)? Alt depo `UNKNOWN` — veri dayanıklılığı,
  silinme hakkı (KVKK md. Verfügung) ve TTL davranışı belirsiz.
- **Azaltma:** `MemorySystem` alt depo incelemesi (K016 yüzeyi, sınır kaydı).

### R-12 · `JSON_THROW_ON_ERROR` ile bağlam kırılması — **DÜŞÜK**
- **Kanıt:** `AIWorkflow.php` L70 — `json_encode(..., JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)`.
- **Etki:** `$context['history']` içinde UTF-8 bozukluğu/kaynak veri olursa **istisna fırlatılır**;
  akışın 4. adımında yakalama yok → öneri workflow'u çöker.
- **Olasılık:** düşük (geçmiş dizisi controller'dan geliyorsa).
- **Azaltma:** try/catch veya bayraksız encode (karar: tasarım onayı).
- **Çocuk:** K006.8 · K006.9.

---

## 4. Bağımlılık / Operasyon Riskleri

### R-13 · AI bağımlılığı yok, versiyonlar UNKNOWN — **ORTA**
- **Kanıt:** `shared/composer.json` — require'da ML/öneri paketi yok; Python lockfile yok.
- **Etki:** aday teknoloji (`yapay-zeka-bagimliliklar.md` §3, 23 aday) "kuruldu" sanılabilir;
  sürüm/lisans CVE taraması yapılamaz.
- **Azaltma:** bağımlılık ekleme öncesi ADR + lisans/CVE taraması (kararlar Q4).

### R-14 · Test kapsamı AI için sıfır — **YÜKSEK**
- **Kanıt:** `composer.json` dev'de `phpunit ^10.5` + `phpstan ^1.10` (level 5, script `stan`) ·
  K006 AI sınıflarına ait test dosyası **0 satır** (gerekiyorsa doğrulanır; bulgu: yok).
- **Etki:** R-01/R-02 gibi kritik boşluklar test ile yakalanamaz; regresyon kapıları yok.
- **Azaltma:** `yapay-zeka-test-stratejisi.md` §5 yol haritası.
- **Çocuk:** K006.9-testing.

---

## 5. Risk Matrisi

| ID | Risk | Etki | Olasılık | Çocuk | Durum |
|---|---|---|---|---|---|
| R-01 | Öneri hattı boş | KRİTİK | yüksek | K006.8 | açık |
| R-02 | Ses analizi placeholder | YÜKSEK | yüksek | K006.1/K006.6 | açık |
| R-03 | Uydurma enstrümantasyon beklentisi | YÜKSEK | orta | K006.2/K006.3 | 🛡 düzeltildi (§9) |
| R-04 | Exception tabanlı hata | ORTA | orta | K006.3/K006.9 | açık |
| R-05 | Şema genişliği | ORTA | düşük | K006.1/K006.9 | açık |
| R-06 | K016 bağımlılığı sözleşmesiz | ORTA | orta | K006.8 | açık (Q5) |
| R-07 | CLAUDE.md kapsam ihlali | DÜŞÜK | — | kök | raporlandı |
| R-08 | Saha sahipliği belirsiz | ORTA | orta | K006.8 | açık (Q1) |
| R-09 | Ölü kod | DÜŞÜK | — | kök | raporlandı |
| R-10 | KVKK YZ rehberi okunmadı | YÜKSEK | — | K006.7/K006.8 | ⚠️ açık |
| R-11 | Oturum verisi deposu | ORTA | orta | K006.7 | UNKNOWN |
| R-12 | JSON istisnası | DÜŞÜK | düşük | K006.8/K006.9 | açık |
| R-13 | Bağımlılık/sürüm yok | ORTA | — | kök | UNKNOWN |
| R-14 | AI testi 0 | YÜKSEK | yüksek | K006.9 | açık |

**Sayım:** 14 risk · KRİTİK 1 · YÜKSEK 4 · ORTA 6 · DÜŞÜK 3 · 🛡 1 kapatıldı.

---

## 6. Kaynaklar

1. `shared/src/AI/AIEngine.php` — L49–77 · L84–86 · L209–235 · L315–330, 2026-10-12.
2. `shared/src/AI/AIWorkflow.php` — L49–91 (L63 · L66 · L70 · L78), 2026-10-12.
3. `shared/src/Contracts/AI/AIEngineInterface.php` — L34, 2026-10-12.
4. `shared/composer.json` — require/require-dev/scripts, 2026-10-12.
5. Repo grep (uydurma semboller) — 0 eşleşme, 2026-10-12.
6. `.ai/architecture/index.md` §6 satır 94 — K006↔K016.

---

`status: verified` · `validation: 2026-10-12 · 14 risk, 12'si dosya+satır kanıtlı, 2'si belirsizlik (R-10 ⚠️ · R-11/R-13 UNKNOWN)`
