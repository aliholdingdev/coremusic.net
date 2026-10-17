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
  - K015-AI
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

---

## 7. NevaEngine / DSP / Firmware Kaynaklı Riskler (R-15 … R-26)

> Bu bölümün tamamı K006↔NevaEngine kesişim alanından beslenir (kapsam sınırı: `yapay-zeka-kapsam.md` §15). Kaynaklar salt okunur; satır numaraları 2026-10-15 okumasından.

**Sayım:** 12 ek risk → toplam 26 (14 + 12).

| # | Risk | Şiddet | Olasılık | Kaynak | Durum |
|---|---|---|---|---|---|
| R-15 | Real-time callback ihlali (DSP zincirinde tahsis/kilit) | KRİTİK | orta | `02-dsp-zincir-mimari.md` [Satır: 71–87] | açık |
| R-16 | Gain staging hatası → klip/saturasyon | YÜKSEK | orta | `02-dsp-zincir-mimari.md` [Satır: 16–90] | açık |
| R-17 | Guardrails (sınır) atlanır → zincir öngörüsüz amplifikasyon | YÜKSEK | düşük | `02-dsp-zincir-mimari.md` [Satır: 71–87] | açık |
| R-18 | Lisans uyumsuzluğu (SDK/lib envanteri) | YÜKSEK | düşük | `00-sdk-lib-envanter.md` [Satır: 19–76 · 65–73] | açık |
| R-19 | IO format uyuşmazlığı (32-bit/192 kHz ↔ web PCM) | YÜKSEK | orta | `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] | açık |
| R-20 | Web-PCM planı ile mixer planı arasında sapma | ORTA | orta | `05-neva-mixer-web-pcm-plani.md` [Satır: 132–143] | açık |
| R-21 | Gereksinim/farklılık matrix güncelliğini yitirir | ORTA | orta | `06-gereksinimler-ve-farkliliklar.md` [Satır: 15–75] | açık |
| R-22 | Firmware/RTOS güncelleme (OTA) başarısızlığı | KRİTİK | düşük | `.ai/electronic/firmware/` (ota-update başlıkları) | açık |
| R-23 | Driver uyumsuzluğu (platform/io katmanı) | YÜKSEK | düşük | `.ai/electronic/drivers/` (audio-drivers başlıkları) | açık |
| R-24 | AI-auto-EQ model kararı (eq-ai-auto) ile K006 vaadi senkronsuzluğu | ORTA | orta | `legacy-2026-07/eq-ai-auto.md` [Satır: 20–199 · 119 · 140] | açık |
| R-25 | Model envanteri (ai-models) sürüklenmesi — başlık değişimi | ORTA | orta | `legacy-2026-07/ai-models.md` başlıklar [Satır: 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726] | açık |
| R-26 | EQ kullanıcı modu davranışı (eq-user-modes) ile test zinciri boşluğu | DÜŞÜK | düşük | `legacy-2026-07/eq-user-modes.md` [Satır: 105–115 · 133–162 · 168–183] | açık |

### 7.1 R-15 — Real-time callback ihlali

| Alan | Değer |
|---|---|
| Tetikleyici | DSP callback içinde `new`/`malloc`, mutex, lock alma, log yazma |
| Kaynak | `02-dsp-zincir-mimari.md` [Satır: 71–87] (guardrails) |
| Etki | Drop-out, click/pop, senkron kaybı |
| Azaltma | Buffer'lar baştan allocate; lock-free kuyruk; noexcept callback |
| Doğrulama | benchmark/test bloğu (`legacy-2026-07/rules/` [Satır: 423–480]) |

### 7.2 R-16 — Gain staging hatası

| Alan | Değer |
|---|---|
| Tetikleyici | Zincirde her aşamanın kazancı toplamı > headroom |
| Kaynak | `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| Etki | Sessiz klip, THD artışı, kullanıcı algısı bozulması |
| Azaltma | Aşama başına kazanç tablosu + tepe ölçümü |
| Doğrulama | 32-bit/192 kHz format kuralı (`10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99]) |

### 7.3 R-17 — Guardrails atlanır

| Alan | Değer |
|---|---|
| Tetikleyici | Yeni efekt eklenirken sınır (limit/clipper/safety) bloğu atlanır |
| Kaynak | `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| Etki | Donanım/a-hop zararı riski, KRİTİK seviyeye geçiş |
| Azaltma | Guardrail checklist; zincir sonu zorunlu sınır |
| Doğrulama | Kod incelemesi + test bloğu (`rules/` [Satır: 423–480]) |

### 7.4 R-18 — Lisans uyumsuzluğu

| Alan | Değer |
|---|---|
| Tetikleyici | SDK/lib envanterinde lisans alanı eksik veya belirsiz |
| Kaynak | `00-sdk-lib-envanter.md` [Satır: 19–76 · 65–73] |
| Etki | Dağıtım/CI engeli, hukuki risk |
| Azaltma | Her bağımlılıkta lisans sütunu zorunlu |
| Doğrulama | Envanter tablosu denetimi |

### 7.5 R-19 — IO format uyuşmazlığı

| Alan | Değer |
|---|---|
| Tetikleyici | 32-bit/192 kHz zincir ↔ web PCM planı arasında bit/örnekleme farkı |
| Kaynak | `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] |
| Etki | Yeniden örnekleme kalitesi bozulması, senkron kaybı |
| Azaltma | Format matrisi tek noktada; dönüşüm tek yerde |
| Doğrulama | IO format kuralları denetimi |

### 7.6 R-20 — Web-PCM ↔ mixer planı sapması

| Alan | Değer |
|---|---|
| Tetikleyici | `05-neva-mixer-web-pcm-plani.md` §7 riskler güncellenmezse |
| Kaynak | `05-neva-mixer-web-pcm-plani.md` [Satır: 132–143] |
| Etki | Web çalma zinciri ile mixer beklentisi çelişir |
| Azaltma | Plan revizyonunda risk satırları yeniden sayılır |
| Doğrulama | §7 satır sayımı çapraz okuma |

### 7.7 R-21 — Gereksinim/farklılık matrix güncelliği

| Alan | Değer |
|---|---|
| Tetikleyici | Yeni gereksinim eklendiğinde farklılık satırı unutulur |
| Kaynak | `06-gereksinimler-ve-farkliliklar.md` [Satır: 15–75] |
| Etki | Beklenti–gerçeklik farkı, kapsam kayması |
| Azaltma | Her gereksinim için farklılık satırı zorunlu |
| Doğrulama | §5 sayım kontrolü |

### 7.8 R-22 — Firmware/OTA güncelleme başarısızlığı

| Alan | Değer |
|---|---|
| Tetikleyici | OTA sırasında kesinti; firmware geri dönüşü olmayan hata |
| Kaynak | `.ai/electronic/firmware/` (ota-update başlıkları, 7 dosya) |
| Etki | Cihaz brick riski (KRİTİK) |
| Azaltma | A/B partition, doğrulama + rollback |
| Doğrulama | Firmware vault denetimi (salt okunur) |

### 7.9 R-23 — Driver uyumsuzluğu

| Alan | Değer |
|---|---|
| Tetikleyici | Platform/io katmanında sürücü sürüm/uyumluluk farkı |
| Kaynak | `.ai/electronic/drivers/` (audio-drivers başlıkları, 10 dosya) |
| Etki | Cihaz kaybı, fallback (WASAPI) tetiklenmesi |
| Azaltma | Sürüm matrisi + fallback zinciri |
| Doğrulama | Drivers vault denetimi (salt okunur) |

### 7.10 R-24 — eq-ai-auto ↔ K006 senkronsuzluğu

| Alan | Değer |
|---|---|
| Tetikleyici | eq-ai-auto davranış kararı değişir, K006 çocuk dokümanı güncellenmez |
| Kaynak | `legacy-2026-07/eq-ai-auto.md` [Satır: 20–199 · 119 · 140] |
| Etki | Kullanıcı vaadi (§6 14 vaat) ile gerçek davranış ayrışır |
| Azaltma | Kesişim kaydı (`yapay-zeka-kapsam.md` §15.1) her revizyonda yeniden sayılır |
| Doğrulama | Kesişim tablosu çapraz okuma |

### 7.11 R-25 — ai-models envanter sürüklenmesi

| Alan | Değer |
|---|---|
| Tetikleyici | Model başlığı eklenir/kaldırılır, indeks satırı unutulur |
| Kaynak | `legacy-2026-07/ai-models.md` başlıklar [Satır: 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726] |
| Etki | Model envanteri okunabilirliğini kaybeder |
| Azaltma | Başlık sayımı periyodik |
| Doğrulama | Başlık satırı sayımı |

### 7.12 R-26 — eq-user-modes test zinciri boşluğu

| Alan | Değer |
|---|---|
| Tetikleyici | Kullanıcı modu davranışı için test bloğu eksik |
| Kaynak | `legacy-2026-07/eq-user-modes.md` [Satır: 105–115 · 133–162 · 168–183] |
| Etki | Davranış regresyonu fark edilmez |
| Azaltma | Mod başına test senaryosu |
| Doğrulama | `rules/` test blokları [Satır: 423–480] |

---

## 8. Azaltma Planları (özet)

| Risk | Azaltma sahibi | Adım | Durum |
|---|---|---|---|
| R-15 | Embedded/DSP | Lock-free callback + baştan allocate | planlandı |
| R-16 | Embedded/DSP | Kazanç tablosu + tepe ölçümü | planlandı |
| R-17 | Embedded/DSP | Guardrail checklist | planlandı |
| R-18 | Dependency | Lisans sütunu zorunluluğu | planlandı |
| R-19 | Embedded/DSP | Format matrisi tek nokta | planlandı |
| R-20 | Embedded/DSP | Plan risk satırı sayımı | planlandı |
| R-21 | Business | Gereksinim↔farklılık satırı zorunlu | planlandı |
| R-22 | Firmware | A/B partition + rollback | planlandı |
| R-23 | Drivers | Sürüm matrisi + fallback | planlandı |
| R-24 | K006 vault | Kesişim tablosu sayımı | planlandı |
| R-25 | K006 vault | Başlık sayımı periyodik | planlandı |
| R-26 | QA | Mod test senaryosu | planlandı |

---

## 9. İzleme (metrik ve periyot)

| Metrik | Periyot | Eşik | Kaynak |
|---|---|---|---|
| Guardrail satırı değişimi | her revizyon | 0 kayıp | `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| Envanter lisans boşluğu | envanter revizyonu | 0 boş | `00-sdk-lib-envanter.md` [Satır: 65–73] |
| IO format satır değişimi | IO revizyonu | matris güncel | `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] |
| Mixer planı risk satırı | plan revizyonu | sayım eşit | `05-neva-mixer-web-pcm-plani.md` [Satır: 132–143] |
| Gereksinim↔farklılık satırı | gereksinim revizyonu | 0 eksik | `06-gereksinimler-ve-farkliliklar.md` [Satır: 15–75] |
| ai-models başlık sayısı | periyodik | başlık sayısı sabit | `ai-models.md` başlıklar [Satır: 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726] |
| OTA/A-B doğrulama | firmware revizyonu | rollback mevcut | `.ai/electronic/firmware/` |
| Driver sürüm matrisi | driver revizyonu | sürüm güncel | `.ai/electronic/drivers/` |

---

## 10. Sayım (bu tur)

| Sayım | Değer |
|---|---|
| Ek risk (§7) | 12 (R-15 … R-26) |
| Toplam risk | 26 (14 + 12) |
| Azaltma satırı (§8) | 12 |
| İzleme metriği (§9) | 8 |
| Silinen satır | 0 (append-only) |
| Beyazliste dışı yazım | 0 |
| git commit | yok (orkestratöre ait) |

---

## 11. Kaynak Eki (§7–§9 eklenenler)

1. `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` — Satır 16–90 (guardrails 71–87), 2026-10-15.
2. `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` — Satır 19–76 (lisans 65–73), 2026-10-15.
3. `.ai/projects/NevaEngine/10-io-format-32bit-192khz-kurallari.md` — Satır 15–99, 2026-10-15.
4. `.ai/projects/NevaEngine/05-neva-mixer-web-pcm-plani.md` — Satır 132–143 (§7), 2026-10-15.
5. `.ai/projects/NevaEngine/06-gereksinimler-ve-farkliliklar.md` — Satır 15–75 (§5), 2026-10-15.
6. `.ai/electronic/firmware/` — ota-update başlıkları (7 dosya envanteri), 2026-10-15.
7. `.ai/electronic/drivers/` — audio-drivers başlıkları (10 dosya envanteri), 2026-10-15.
8. `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` — Satır 20–199 · 119 · 140, 2026-10-15.
9. `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` — başlıklar Satır 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726, 2026-10-15.
10. `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` — Satır 105–115 · 133–162 · 168–183, 2026-10-15.
11. `.ai/projects/NevaEngine/legacy-2026-07/rules/` — test blokları Satır 423–480, 2026-10-15.

---

`status: verified` · `validation: ... ek (2026-10-15 · kapanış turu) · §7–§11 eklendi: 12 ek risk (R-15…R-26, toplam 26) · 12 azaltma satırı · 8 izleme metriği · 11 kaynak eki — append-only, silinen satır 0`

---

## 12. Tetikleyici Senaryolar (§7–§26 riskleri gerçek olayla eşleştirilir)

| # | Senaryo | Tetiklenen risk | Erken sinyal | İlk aksiyon |
|---|---|---|---|---|
| S-1 | DSP zincirine yeni efekt bloğu eklenir | R-15 · R-17 | callback içinde tahsis log'u | ek zinciri inceleme + guardrail checklist |
| S-2 | Kazanç değerleri el ile değiştirilir | R-16 | tepe ölçüm eşiği aşımı | kazanç tablosuna geri dön |
| S-3 | Yeni SDK/lib envantere eklenir | R-18 | lisans sütunu boş | lisans doğrulama |
| S-4 | IO format (bit/örnekleme) değişir | R-19 | format matrisi uyuşmazlığı | dönüşüm tek noktada toplanır |
| S-5 | Web-PCM planı revize edilir | R-20 | §7 risk satırı değişmez | plan risk satırı yeniden sayılır |
| S-6 | Yeni gereksinim eklenir | R-21 | farklılık satırı eksik | §5 matrix güncellenir |
| S-7 | OTA paketi yayınlanır | R-22 | doğrulama/rollback yok | A/B partition kontrolü |
| S-8 | Driver sürümü değişir | R-23 | sürüm matrisi güncellenmemiş | fallback zinciri doğrulanır |
| S-9 | eq-ai-auto davranışı değişir | R-24 | kesişim tablosu değişmez | kesişim sayımı |
| S-10 | ai-models başlık ekleme/kaldırma | R-25 | başlık sayısı değişir | indeks satırı güncellenir |
| S-11 | eq-user-modes davranışı regresyonu | R-26 | test bloğu düşer | mod test senaryosu eklenir |

---

## 13. Erken Uyarı Sinyalleri (izleme → risk eşlemesi)

| Sinyal | Ölçüm kaynağı | Bağlı risk | Eşik |
|---|---|---|---|
| Callback içinde tahsis | kod incelemesi | R-15 | 0 kabul |
| Kazanç tepe değeri | tepe ölçümü | R-16 | headroom altında |
| Guardrail bloğu varlığı | zincir sonu kontrolü | R-17 | zorunlu |
| Lisans sütunu doluluğu | envanter tablosu | R-18 | %100 |
| Format matrisi eşleşmesi | IO kuralları | R-19 | matris güncel |
| Plan risk satırı sayısı | plan §7 | R-20 | eşit |
| Gereksinim↔farklılık satırı | §5 matrix | R-21 | 0 eksik |
| OTA rollback mevcudiyeti | firmware vault | R-22 | zorunlu |
| Driver sürüm matrisi | drivers vault | R-23 | güncel |
| Kesişim tablosu değişimi | `yapay-zeka-kapsam.md` §15.1 | R-24 | her revizyonda say |
| ai-models başlık sayısı | başlık satırı sayımı | R-25 | sabit |
| Mod test bloğu | `rules/` test blokları | R-26 | 0 boşluk |

---

## 14. Eskalasyon Eşlemesi (risk → seviye)

| Seviye | Koşul | Hedef | Aksiyon |
|---|---|---|---|
| L1 | DÜŞÜK/ORTA risk, tespit edildi | ilgili vault sahibi | kayda geçir + azaltma planla |
| L2 | YÜKSEK risk, tekrarlayan | vault sahibi + QA | azaltma uygula + doğrula |
| L3 | KRİTİK risk (R-15 · R-22) | vault sahibi + mimari | durdur + geri al + inceleme |

Kural: KRİTİK risk açıkken ilgili yüzeyde **yeni değişiklik başlatılmaz** (duraklatma); yalnız azaltma çalışması yapılır.

---

## 15. Bağımlılık Riskleri (risk → risk zinciri)

| Zincir | Açıklama |
|---|---|
| R-19 → R-16 | IO format değişirse kazanç tablosu yeniden hesaplanır (headroom etkilenir) |
| R-18 → R-22 | Lisans/SDK sorun firmware paketlemeyi engeller |
| R-21 → R-24 | Gereksinim matrix sürüklenirse kesişim (R-24) yanlış sayılır |
| R-25 → R-26 | Model envanteri sürüklenirse mod test senaryoları bayatlar |
| R-15 → R-17 | Guardrail atlanırsa real-time ihlali (tahsis) daha olasıdır |

Zincir kuralı: bir risk kapatıldığında zincirdeki alt riskler yeniden gözden geçirilir (kapanış ≠ zincir sonu).

---

## 16. Sayım (bu tur ek — §12–§15)

| Sayım | Değer |
|---|---|
| Tetikleyici senaryo (§12) | 11 (S-1 … S-11) |
| Erken uyarı sinyali (§13) | 12 |
| Eskalasyon seviyesi (§14) | 3 (L1/L2/L3) |
| Bağımlılık zinciri (§15) | 5 |
| Silinen satır | 0 (append-only) |
| Beyazliste dışı yazım | 0 |
| git commit | yok (orkestratöre ait) |

---

`status: verified` · `validation: ... ek 2 (2026-10-15 · kapanış turu) · §12–§16 eklendi: 11 tetikleyici senaryo · 12 erken uyarı sinyali · 3 eskalasyon seviyesi · 5 bağımlılık zinciri — append-only, silinen satır 0`

---

## 17. Risk Sahipliği Atama Matrisi (R-15 … R-26)

| Risk | Birincil sahip | İkincil sahip | Doğrulama sahibi |
|---|---|---|---|
| R-15 | Embedded/DSP | — | QA |
| R-16 | Embedded/DSP | — | QA |
| R-17 | Embedded/DSP | — | QA |
| R-18 | Dependency | Backend | Compliance |
| R-19 | Embedded/DSP | Windows SW | QA |
| R-20 | Embedded/DSP | Backend | QA |
| R-21 | Business | Backend | QA |
| R-22 | Firmware | DevOps | QA |
| R-23 | Drivers | Windows SW | QA |
| R-24 | K006 vault (MO) | Embedded | QA |
| R-25 | K006 vault (MO) | Embedded | QA |
| R-26 | QA | Embedded | — |

Kural: her riskin **tek bir birincil sahibi** vardır; ikincil sahip yalnız katkı sağlar, kapanış kararını birincil verir (sorumluluk dağılmaz).
