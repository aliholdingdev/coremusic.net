---
node_id: K006.9-testing-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/composer.json — phpunit ^10.5 · phpstan ^1.10 (require-dev, 2026-10-12)"
  - "shared/tests/**/*.php glob → 44 dosya; AI testi 0 (2026-10-12)"
  - "shared/src/AI/AIEngine.php + AIWorkflow.php — test edilmemiş kusur yüzeyi (2026-10-12)"
validation: "2026-10-12 · test altyapısı = kurulu; K006 test bileşeni = 0 satır; 7 karakteristik hedef kaydedildi · status=verified"
open_questions:
  - "AI testleri nerede yaşayacak: shared/tests/Unit/AI mı, ayrı paket mi → UNKNOWN"
  - "Ses dosyası fixture'ı nasıl üretilecek (minik WAV mı, ffprobe mock mu) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K006.9-testing — Bileşenler

> K006.9'un **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. Test **altyapısı** olgundur; **K006 katmanına ait tek test dosyası yoktur**.

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| Test altyapısı | **kurulu** — phpunit ^10.5 · phpstan ^1.10 | composer.json require-dev |
| Mevcut test dosyası | **44** (`shared/tests/**/*.php`) | glob 2026-10-12 |
| K006'a ait test dosyası | **0** | 44 dosyanın 0'ı AI/AIEngine/AIWorkflow/AIOrchestrator |
| K006.9'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — "testing" adında dosya yok |
| Test edilmemiş kusur yüzeyi | **7 karakteristik hedef** | §3 |
| Kök test stratejisi | ✅ **var** (`yapay-zeka-test-stratejisi.md`, kapılar G1–G5) | vault |

---

## §2 Mevcut Test Takımı (AI dışı — altyapı kanıtı)

| Takım | Dosyalar (örnek) | Gösterdiği kalıp |
|---|---|---|
| Middleware | `CsrfMiddlewareTest` · `AuthMiddlewareTest` · `RateLimiterMiddlewareTest` · `OriginCheckMiddlewareTest` · `MiddlewarePipelineTest` · `SessionManagerMiddlewareTest` · `ValidationMiddlewareTest` · `BypassAuthMiddlewareTest` · `BypassAuthMiddlewareFailClosedTest` | fail-closed/fail-open davranış testi — **AI katmanı için örneklenmemiş** |
| Api | `VersioningTest` · `RouteTableTest` · `ApiResponseTest` · `DtoTest` · `ApiPipelineSecurityTest` · `GatewayMethodRoutingTest` … | pipeline doğrulama |
| Unit | `ConfigManagerTest` · `JwtServiceTest` · `Psr3LoggerTest` · `LoggerRedactionTest` · `DeviceDetectorTest` … | birim test kalıbı |
| Repository/Events/Component/OAuth | `ApiKeyRepositoryTest` · `EventDispatcherTest` · `TemplateEngineTest` · `OAuthManagerTest` … | entegrasyon kalıbı |

**Kıyas kaydı (C3):** `RateLimiterMiddlewareTest` L127 — cache başarısızlığında fail-open
davranışını test eder; AIWorkflow'daki **kontrolsüz `callTool`** (L122–126) için benzer bir
davranış testi **yoktur**.

---

## §3 Test Edilmemiş Kusur Yüzeyi (karakteristik hedef listesi)

| # | Hedef | Konum | Testlenmesi gereken davranış |
|---|---|---|---|
| 1 | `JSON_THROW_ON_ERROR` | AIWorkflow L70 · AIEngine L300 | bozuk JSON → istisna yayılımı |
| 2 | `escapeshellarg` kaçışı | AIEngine L292 | özel karakterli dosya yolu |
| 3 | null→varsayılan fallback | AIEngine L296–298 | ffprobe yoksa `sample_rate=44100` döner (sessiz) |
| 4 | Boş skorlayıcılar | AIEngine L209–218 · L223–235 | her koşulda `[]` — öneri boş |
| 5 | Sabit ağırlık + eşik | AIEngine L65 · L71 · L74 | 0.6/0.4 + MIN_SCORE + sıralama determinizmi |
| 6 | Placeholder öznitelikler | AIEngine L319–326 | her dosya için aynı bpm/key — **yanlışlıkla "doğru" test edilmemeli** |
| 7 | Kontrolsüz `callTool` + beklenmeyen dispatch | AIWorkflow L122–126 · L59 | sessiz başarısızlık / görev kaybı |

**Kayıt:** bu 7 hedef, K006 testlerinin **ilk sprintinin** tamamını karşılar (kök
`yapay-zeka-test-stratejisi.md` G1–G5 kapılarıyla uyumlu).

---

## §4 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.9 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| AIEngine birim testi | **0 satır** | glob 44 dosya → 0 AI |
| AIWorkflow birim testi | **0 satır** | aynı |
| AIOrchestrator birim testi | **0 satır** | aynı |
| Ses dosyası fixture'ı | **0 dosya** | `shared/tests/Fixtures/` yalnız chrome header/footer PHP |
| Mock ffprobe/DB araçları | **0 satır** | grep `ffprobe|db-query` tests → 0 |
| Metrik doğrulama testi | **0 satır** | repo'da metrik kodu da 0 |
| Test altyapısı (PHPUnit/PHPStan) | ✅ **kurulu** | composer.json |
| Test takımı kalıbı (44 dosya) | ✅ **var** | glob 2026-10-12 |
| Kök test stratejisi dokümanı | ✅ **var** | `yapay-zeka-test-stratejisi.md` |

---

## §5 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.6-audio-intelligence | §3-6 (placeholder) + §3-3 (fallback) onun birincil karakteristik testleri |
| K006.3-recommendation | §3-4 · §3-5 (boş skorlayıcı + eşik/sıralama determinizmi) |
| K006.8-evaluation | metrik doğrulama testi — metrik kodu 0 olduğu için bugün **önce metrik yazımı** gerekir |
| QA katmanı (dış) | PHPUnit kalıbı `shared/tests/**` — AI testleri aynı kalıba uymalı (tasarım iddiası) |

---

## §6 Kaynaklar

1. `shared/composer.json` — require-dev (phpunit ^10.5 · phpstan ^1.10), 2026-10-12.
2. `shared/tests/**/*.php` glob → 44 dosya listesi + AI testi 0, 2026-10-12.
3. `shared/tests/Middleware/RateLimiterMiddlewareTest.php` L127 — fail-open kıyası, 2026-10-12.
4. `shared/src/AI/AIEngine.php` + `AIWorkflow.php` — §3 kusur yüzeyi, 2026-10-12.
5. `.ai/architecture/K006-yapay-zeka/yapay-zeka-test-stratejisi.md` — kapılar G1–G5.

---

`status: verified` · `validation: 2026-10-12 · altyapı kurulu (44 dosya), K006 testi 0, 7 karakteristik hedef + 9 beklenen bileşenden 6'sı 0 satır kaydedildi`
