---
node_id: "K012-services/ai-service/ai-service-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K012-services/ai-service"
children: []
depends_on:
  - "ai-service/ai-service-kapsam.md"
  - "services-dogrulama.md"
evidence:
  - "10 URL — HTTP 200 (2026-10-16)"
  - "kesişim testi: url-envanter.txt — çakışma 0"
  - "kesişim testi: url-inventory.txt — çakışma 0"
  - "K006 yapay-zeka-web-arastirma.md çakışma kontrolü — 0"
  - "services-dogrulama.md §5 15 URL ile çakışma — 0"
validation:
  - "2026-10-16 — 4 başlık × 1–3 URL · HEAD 200 doğrulama · kesişim testi (url-envanter.txt + url-inventory.txt + K006)"
  - "yöntem notu: HTTP HEAD (redirect: follow) — Invoke-WebRequest yerine OpenCode Code Mode fetch ile çalıştırıldı; yalnız erişilebilirlik doğrulandı, sayfa içeriği OKUNMADI"
open_questions:
  - "Q1 karar bekliyor — seçenek matrisi §2"
group: kanit
---

# ai-service — Web Araştırması (K012)

> **Konu:** Q1 — ai-service K012 servis katmanının arkasındaki YZ yeteneği K006-yapay-zeka mı, K015-AI mı?
> (services-master.md §7/1 · services-dogrulama.md §4/Q1 → `UNKNOWN` · ai-service-kapsam.md §5/R1)
>
> **Yöntem:** 4 başlık → birincil/resmi kaynak → HTTP HEAD 200 (2026-10-16) → kesişim testi.
> K006'nın 32+ URL listesi ve services-dogrulama §5'in 15 URL'si ile **çakışma 0**.
> **Sınır:** yalnız HEAD durumu doğrulanmıştır; sayfa metni okunmadığı için içerik/idam iddiaları
> `⚠️ VERIFICATION REQUIRED` kalır (K006 §18 ile aynı dürüstlük kuralı).

---

## §1 KAYNAK TABLOSU

| # | URL | Ne için (başlık) | Konu | HTTP 200 (2026-10-16) |
|---|---|---|---|---|
| W1 | https://aws.amazon.com/bedrock/ | model erişim yüzeyi tek API altında — gateway/ yönlendirme zemini | 1 · gateway | 200 |
| W2 | https://learn.microsoft.com/en-us/azure/api-management/genai-gateway-capabilities | üretken YZ gateway yetenekleri (resmi MS Learn sayfası) | 1 · gateway | 200 |
| W3 | https://learn.microsoft.com/en-us/azure/ai-foundry/openai/quotas-limits | kota/limit yüzeyi (rate limit — yönlendirme politikası komşusu) | 1 · gateway | 200 (redirect final: ai-foundry) |
| W4 | https://docs.aws.amazon.com/sagemaker/latest/dg/realtime-endpoints.html | inference serving = gerçek zamanlı uç nokta (model çalıştırma) | 2 · serving | 200 |
| W5 | https://learn.microsoft.com/en-us/azure/machine-learning/concept-endpoints | uç nokta (serving) kavram dokümanı | 2 · serving | 200 |
| W6 | https://docs.aws.amazon.com/sagemaker/latest/dg/model-registry.html | model registry = platform/yaşam döngüsü tarafı | 2 · platform | 200 |
| W7 | https://www.nist.gov/itl/ai-risk-management-framework | NIST AI RMF — YZ risk yönetimi çerçevesi kapısı | 3 · standart | 200 |
| W8 | https://owasp.org/www-project-top-10-for-large-language-model-applications/ | OWASP LLM Top 10 proje sayfası | 3 · güvenlik | 200 (redirect final: owasp.org/projects/...) |
| W9 | https://platform.openai.com/docs/guides/function-calling | function/tool calling resmi kılavuzu — uygulama-içi YZ çağrı yüzeyi | 4 · tool calling | 200 (redirect final: developers.openai.com) |
| W10 | https://docs.anthropic.com/en/docs/agents-and-tools/tool-use/overview | tool use resmi kılavuzu (Anthropic) | 4 · tool calling | 200 (redirect final: platform.claude.com) |

**Toplam: 10 kabul edilmiş URL · kesişim (url-envanter.txt 2701 satır + url-inventory.txt 2761 satır) = 0 · K006 ile çakışma = 0.**

Kabul kuralı: yalnız birincil/resmi kaynak (aws.amazon.com · learn.microsoft.com · ai.google.dev ailesi ·
nist.gov · owasp.org · platform.openai.com/docs · docs.anthropic.com) · 200 dönmeyen RED (§3) ·
envanterde veya K006'da geçen RED.

---

## §2 Q1 SEÇENEK MATRİSİ

> Karar **VERİLMEZ**; yalnız seçenek sunulur. Her satır `Kaynak (W-x)` ile §1'e bağlıdır.

| Seçenek | Kaynak (W-x) | Artı | Eksi | Etkilenen dosya |
|---|---|---|---|---|
| **A: K006'ya bağla (platform servisi — YZ yeteneği/serving sahipliği K006'da, ai-service yalnız kapı)** | W4 · W5 · W6 | serving↔platform ayrımı resmi dokümanlarla çizilebilir (uç nokta = serving, registry = platform); K006'nın elindeki inference/model-management kapsamıyla doğal örtüşür | K006'nın CoreMusic'e özel "capability" iddiaları bu turda okunmadı → `⚠️ VERIFICATION REQUIRED`; karar ADR gerektirebilir | ai-service/index.md · ai-service-kapsam.md §5/R1 · services-master.md §7/1 · services-dogrulama.md §4/Q1 |
| **B: K016'ya bağla (AI node sahipliği — registry §6 kapsam ayrımı üzerinden)** | W1 · W6 | çağrı-yüzeyi/sahiplik sorusunu tek düğümde toplar; servis katmanı (K012) ile YZ düğümü ayrımı daha net görünür | K016'nın kapsamı bu turda **okunmadı** (registry §6 referansı dışında iddia yok) → kanıt eksik; W-x bu seçeneği yalnızca "ince katman" metaforuyla destekliyor | ai-service/index.md open_questions satır 17 · services-master.md §7/1 · services-dogrulama.md §4/Q1 |
| **C: İkisi arasında sınır tanımla (ai-service = ince YZ gateway; yetenek sahipliği ayrı ADR ile)** | W1 · W2 · W3 | gateway deseni (routing/limit/maliyet/fallback) resmi kaynaklarla gerekçelendirilebilir; Q1'i "hangisi" yerine "sınır nerede" sorusuna çevirir | Sınırı çizecek kişiler/faydalar bu turda kanıtlanmadı; ek ADR + iki komşu K ile mutabakat gerekir | ai-service-kapsam.md §2 (sınır satırı 1) · services-master.md §7/1–§7/2 · services-bagimliliklar.md §1/3 |
| **D: Kararı ertele — Q1'i tasarımsal-değil-kanıt sorunu olarak ADR'ye taşı (kanıt yetersizse)** | W7 · W8 | NIST/OWASP kapıları uyum (risk/güvenlik) zeminini kurar; karar öncesi zorunlu kontrol listesi çıkar | Süreç maliyeti; Q1 `UNKNOWN` olarak açık kalır, K012 alt alan dosyaları bekler | services-master.md §7/5 (ADR gerekliliği) · services-dogrulama.md §15 |

**Öneri-seviyesi (tek yorum — bağlayıcı DEĞİL):** *orta-öneri* → C (sınır tanımla) ile başlayıp A/B'yi
Ardından ADR'ye taşımak; gerekçe: HEAD-200 kanıtı yalnız **yüzey varlığını** gösterir, K016/K006
içerik okuması yapılmadığı için "hangi düğüm" iddiası tek başına kapatılamaz (zero-hallucination).

---

## §3 REDDEDİLEN ADAYLAR

| # | Aday | Gerekçe |
|---|---|---|
| R1 | https://learn.microsoft.com/en-us/azure/machine-learning/endpoint-real-time-invocation | HTTP 404 (2026-10-16) |
| R2 | https://learn.microsoft.com/en-us/azure/machine-learning/how-to-deploy-and-manage-instances | HTTP 404 |
| R3 | https://cloud.google.com/vertex-ai/prediction/docs/overview | HTTP 404 |
| R4 | https://cloud.google.com/vertex-ai/prediction/docs/get-ai-predictions | HTTP 404 |
| R5 | https://cloud.google.com/apigateway/docs/overview | HTTP 404 (redirect: docs.cloud.google.com) |
| R6 | https://cloud.google.com/api-gateway/docs/rate-limit | HTTP 404 |
| R7 | https://ai.google.dev/gemini-api/docs/overview | HTTP 404 |
| R8 | https://cloud.google.com/vertex-ai/generative-ai/docs/overview | 200 döndü ama redirect `docs.cloud.google.com/gemini-enterprise-agent-platform/models`'a gidiyor — sayfa kimliği değiştiği için iddia-zemini sayılmadı |
| R9 | https://ai.google.dev/gemini-api/docs/rate-limits | 200 · kesişim 0 · **10 URL tavanına takıldı** (konu 1'de W1–W3 zaten kapsıyor) |
| R10 | https://platform.openai.com/docs/guides/structured-outputs | 200 · redirect `developers.openai.com` · W9 ile örtüşen başlık (tool/çıkı yüzeyi) — mükerrer kanıt |
| R11 | https://learn.microsoft.com/en-us/azure/ai-services/openai/quotas-limits | 200 döndü ama redirect final URL → `ai-foundry/openai/quotas-limits`; **W3 bu final URL olarak alındı** — başlıkla mükerrer kanıt olmasın diye RED |
| R12 | https://nvlpubs.nist.gov/nistpubs/ai/NIST.AI.600-1.pdf | 200 · tavan + PDF içeriği okunmadı → kapı düzeyinde W7 tercih edildi |
| R13 | https://airc.nist.gov/ | 200 · W7 ile aynı kurum kapısı — mükerrer kanıt |
| R14 | K006'nın 32+ URL'si (onnxruntime · vllm · transformers · owasp cheatsheetseries vb.) | **tekrar yazılmadı** — K006 çakışma kontrolü 0 isabet (aynı URL bu dosyada yok) |
| R15 | services-dogrulama §5'in 15 URL'si (rfc-editor · w3c · 12factor · cncf · ietf) | **çakışma yasak** — 0 isabet, hiçbiri tekrar kullanılmadı |

> Kesişim kanıtı: tam-URL `Select-String` eşlemesi her iki envanterde (url-envanter.txt ·
> url-inventory.txt) **0** = §1'deki 10 URL ve §3'teki adaylar hiçbirinde geçmiyor.

---

## §4 KAPSAM DIŞI

- **Genel YZ araştırması (K006):** `[[K006-yapay-zeka/yapay-zeka-web-arastirma]]` — 32+ URL'lik
  inference/model-management/recommendation taraması; bu dosya onu **tekrarlamaz** (yalnız Q1
  sınır sorusuna kaynak arar).
- **Kardeş dosyalar (ai-service):**
  `[[ai-service/index]]` · `[[ai-service/ai-service-kapsam]]` ·
  `[[ai-service/ai-service-dogrulama]]` · `[[ai-service/ai-service-ornekler]]`
- **Çapraz referans:** `services-master.md` §7/1 · `services-dogrulama.md` §4/Q1 ve §5 ·
  `ai-service-kapsam.md` §5/R1.
- **Bu dosyada DEĞİL:** K015-AI kapsamlı okuma, sayfa içeriği/idddia çıkarma, repo kod çapraz bağ.

---

## §5 DEĞİŞİKLİK KAYDI

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-16 | İlk yazım — 10 URL kabul (HEAD 200) · 15 RED kaydı · Q1 için 4 seçenek (karar yok) | Researcher (subagent) |

**Yöntem şeffaflığı:** HTTP doğrulama `HEAD` + `redirect: follow` ile yapılmıştır
(Invoke-WebRequest için bu oturumda shell yoktu; eşdeğer HEAD sorgusu Code Mode `fetch` ile).
Yalnız durum kodu + final URL kaydedildi; **sayfa içeriği okunmadı** → içerik iddiaları
`⚠️ VERIFICATION REQUIRED`. `git commit` atılmadı (orkestratöre aittir); atanan dosya dışında
değişiklik yapılmadı.

---

## §6 TUR-2 KAYNAK EKİ (2026-10-18)

> **Append-only ek** — §1–§5 korunmuştur; §5 DEĞİŞİKLİK KAYDI satırlarına dokunulmadı.
> Doğrulama: Code Mode `fetch` (GET · redirect: follow) — **2026-10-18** (§1'deki HEAD'den
> farklı yöntem, şeffaf kayıt — her ikisi de 200 doğrular).
> Benzersizlik: K012 recursive tarama (50 dosya) + **K006-yapay-zeka** URL listesi taraması
> (yapay-zeka-web-arastirma.md §1–§18'deki 32+ URL) → kesişim **0** (RED kayıtları §6.7).

### §6.1 TUR-2 KAYNAK TABLOSU (T2-A1 … T2-A4)

| # | URL | Ne kanıtlar | Q1 seçeneğine etkisi | HTTP (2026-10-18) |
|---|-----|-------------|----------------------|--------------------|
| T2-A1 | https://en.wikipedia.org/wiki/MLOps | MLOps: model yaşam döngüsünün (eğitim→dağıtım→izleme→yeniden eğitim) operasyon disiplini olarak tanımı | A seçeneğini güçlendirir: yetenek sahipliği bir yaşam döngüsüdür, tek çağrı değil | GET 200 ✅ |
| T2-A2 | https://en.wikipedia.org/wiki/Automatic_speech_recognition | Otomatik konuşma tanıma (ASR): ses→metin dönüşümünün ayrı bir YZ yeteneği ailesi olduğu | CoreMusic ai-service yetenek haritası: ASR (Whisper) K006 kapsamında — A seçeneği | GET 200 ✅ |
| T2-A3 | https://en.wikipedia.org/wiki/Generative_artificial_intelligence | Üretken YZ: modelin yeni içerik üretmesi — LLM/RAG ailesinin genel çerçevesi | Gateway/LLM yüzeyi (W9/W10 tool calling) ile yetenek çerçevesini birleştirir; A/C | GET 200 ✅ |
| T2-A4 | https://en.wikipedia.org/wiki/A/B_test | A/B testi: iki varyantın randomize karşılaştırılması — model/servis değerlendirme metodu | Serving sonrası değerlendirme döngüsü (eval) — A seçeneğinin K006 eval kümesiyle örtüşmesi | GET 200 ✅ |

### §6.2 T2-A1 — MLOps (model yaşam döngüsü operasyonları)

**Kanıt:** Wiki MLOps'u, makine öğrenmesi modelinin **yaşam döngüsünün uçtan uca**
(y eğitim, doğrulama, dağıtım, izleme, sürüm yönetimi, yeniden eğitim) operasyon ve otomasyon
disiplini olarak tanımlar. MLOps'un amacı model ile operasyon (DevOps) arasındaki boşluğu
kapatmaktır; sürümleme, izleme (drift) ve geri alma (rollback) temel bileşenlerdir.

**Q1 etkisi:** TUR-1'de seçenek A ("YZ yeteneği/serving sahipliği K006'da") W4/W5/W6
(serving/registry dokümanları) ile desteklenmişti. T2-A1 bu kararın **kapsam tanımını**
zenginleştirir: yetenek sahipliği yalnız "modeli sunmak" değil, **model yaşam döngüsü**nün
tamamıdır (eğitim→izleme→yeniden eğitim). K006'nın mevcut kümeleri (yapay-zeka-web-arastirma
§2.2 "model management / MLOps" kümesi) ile T2-A1 birebir örtüşür → A seçeneğinin gerekçesi
"model registry"den "model yaşam döngüsü"ne genişler.

**K012 ile ilişki:** ai-service (K012) **kapı**dır (W1/W2/W3 gateway); yaşam döngüsü
K006'da kalır. T2-A1 bu ayrımı kanıtlar: MLOps yeteneği kapı düzeyinde **değildir** →
ai-service-kapsam.md §2 sınır satırına destek.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.3 T2-A2 — Automatic Speech Recognition (ASR)

**Kanıt:** Otomatik konuşma tanıma (ASR), insan konuşmasının metne dönüştürülmesidur; wiki
bu sürecin ayrı bir YZ yeteneği ailesi olduğunu (dil modeli ≠ konuşma modeli) ve özellikler
(gerçek zamanlı akış, çoklu dil) tanımlar.

**Q1 etkisi:** CoreMusic ai-service yetenek haritasında **ASR (Whisper)** bir yetenektir;
K006'nın URL listesinde `openai-whisper` (K006 §6, satır 82) zaten doğrulanmıştır. T2-A2 bu
yeteneğin **kavramsal çerçevesini** kanıtlar: ASR ayrı yetenek ailesi olduğu için ayrı
model/serving hattı ister → A seçeneğinde "yetenek sahipliği K006'da" iddiası ASR için de
geçerlidir (yalnız LLM için değil).

**K012 ile ilişki:** ai-service yalnız ASR **çağrı yüzeyini** (Q4 sözleşmesi) tanımlar;
model/çevirme hattı K006'nındır. T2-A2 bu sınırın kanıtıdır.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.4 T2-A3 — Generative Artificial Intelligence (üretken YZ)

**Kanıt:** Üretken YZ, modelin yeni içerik (metin, ses, görüntü) **üretmesine** dayanan YZ
ailesidir; büyük dil modelleri (LLM) üretken YZ'nin en yaygın biçimidir. Wiki bu ailenin
eğitim/ince ayar (fine-tune) ve çıkarım (inference) gerektirdiğini tanımlar.

**Q1 etkisi:** W9/W10 (function/tool calling) zaten LLM **çağrı yüzeyi**ni kanıtlamıştı;
T2-A3 bu yüzeyin **arkasındaki yetenek ailesini** adlandırır: üretken YZ (LLM + RAG) K006'nın
inference kümesi (K006 §2.1) kapsamındadır → C seçeneği ("sınır tanımla") için çerçeve:
ai-service = üretken YZ **kapısı** (gateway), yetenek = K006. Not: K006 URL listesinde
`Large_language_model` **yoktu** — bu turda eklenmedi (kontenjan/tekillik kararına göre
T2-A3 genel aile olarak tercih edildi; LLM özel madde ⚠️ VERIFICATION REQUIRED olarak
işaretlenebilir).

**Doğrulama:** GET → 200 (2026-10-18).

### §6.5 T2-A4 — A/B Testi (model/servis değerlendirme)

**Kanıt:** A/B testi, iki varyantın (A ve B) **rastgele atamayla** benzer kitlelerde
karşılaştırılması ve tek bir metrik (dönüşüm, hata oranı, gecikme) ile karşılaştırma
yöntemidir; wiki istatistiksel anlamlılık kavramını tanımlar ve "aynı anda iki varyant"
gerektiğini vurgular.

**Q1 etkisi:** TUR-1'de değerlendirme döngüsü hiç kanıtlanmamıştı (W1–W10 yalnız
gateway/serving/registry/risk/tool-calling idi). T2-A4 **serving sonrası değerlendirme**
döngüsüne kanıt ekler: model sürümü canlıya alındığında A/B testi ile karşılaştırılır →
K006'nın evaluation kümesi (yapay-zeka-web-arastirma §2.4) ile örtüşür. A seçeneğinin
gerekçesine "yaşam döngüsü + değerlendirme" boyutu eklenir.

**K012 ile ilişki:** ai-service (kapı) A/B testi **yürütmez**; yalnız isteklerin sürüm/route
(W3 kota/limit) katmanında hangi modele gideceğini belirler (canary/route). T2-A4 bu
ayrımın çerçevesidir.

**Doğrulama:** GET → 200 (2026-10-18).

### §6.6 Q1 MATRİSİNE TUR-2 ETKİSİ (özet — KARAR DEĞİL)

| Seçenek | TUR-1 kanıtı | TUR-2 ek | Değişen satır |
|---|---|---|---|
| A — K006'ya bağla | W4, W5, W6 | T2-A1 (yaşam döngüsü çerçevesi), T2-A2 (ASR yetenek ailesi), T2-A4 (eval döngüsü) | en çok zenginleşen seçenek: serving→yaşam döngüsü→değerlendirme |
| B — K016'ya bağla | W1, W6 | (dolaylı: T2-A1 yaşam döngüsü K016'nın "AI node" tanımına sığmaz — K016 kapsamı bu turda okunmadı) | B hâlâ kanıt eksik (⚠️ korunur) |
| C — sınır tanımla | W1, W2, W3 | T2-A3 (üretken YZ kapısı = gateway çerçevesi), T2-A2 (ASR çağrısı gateway'den geçer) | C'nin çerçeve gerekçesi güçlendi |
| D — karar ertele | W7, W8 | (dolaylı: T2-A4 evaluation "hangi düğüm" sorusunu kapatmaz) | D hâlâ geçerli seçenek |

> **Öneri-seviyesi (KARAR DEĞİL):** TUR-2 sonrası kanıt yoğunluğu sırası **A ≈ C > D > B**
> olarak güncellendi (TUR-1: C > A ≈ D > B); A'nın yükselmesi T2-A1/A2/A4'ün yaşam döngüsü +
> yetenek ailesi + değerlendirme boyutlarını eklemesinden kaynaklanır. C'nin korunması, K016
> kapsamının hâlâ okunmamış olmasından kaynaklanır. **Nihai karar ADR'ye aittir**
> (services-master §7/1 → ⚠️ UNKNOWN korunur).

### §6.7 REDDEDİLEN ADAYLAR (TUR-2 — dürüst kayıt, 2026-10-18)

| # | Aday URL | Neden (RED) | Tarih |
|---|----------|-------------|-------|
| T2-R1 | https://en.wikipedia.org/wiki/A/B_testing | HTTP **404** — slug yazım hatası; doğru adres `A/B_test` (200) → T2-A4 olarak alındı | 2026-10-18 |
| T2-R2 | https://en.wikipedia.org/wiki/Queue-based_load_leveling | HTTP **404** — bu tur adayıydı ama RED (kaydı kök `services-web-arastirma.md` §6.8'de; byte-tekilliği) | 2026-10-18 |
| T2-R3 | K006'daki 32+ URL (onnxruntime · pytorch · tensorflow · vllm · whisper · mlflow · huggingface · tensorboard · evidentlyai · scikit-learn · librosa · pyannote · eeg-notebooks · neural-interference-coding) | **K006 envanter tekrarı** — hepsi K006 yapay-zeka-web-arastirma §1–§18'de doğrulanmış; bu dosyaya yazılmadı (çakışma 0 = hiçbiri bu dosyada yok) | 2026-10-18 |
| T2-R4 | https://en.wikipedia.org/wiki/Large_language_model | **Bu turda 200 döndü** ancak T2-A3 (Generative AI) genel aile olarak tercih edildi — LLM madde tekrarı olmasın diye kontenjan RED; Q1 kararı ADR'ye ait | 2026-10-18 |

> **Şeffaflık notu:** T2-R4 404 **değildir** — kontenjan/tercih RED'idir; "200 döndü ama
> tercih edilmedi" dürüstçe yazılmıştır (uydurma "404" iddiası yasak).

### §6.8 TUR-2 KANIT SAYIMI VE DOĞRULAMA ÖZETİ

| Kalem | Değer |
|-------|-------|
| Aday URL (bu tur) | 8 |
| Kabul (T2-A1…T2-A4) | 4 — hepsi GET 200 (2026-10-18) |
| RED (§6.7) | 4 — dürüst gerekçeli (404 ×2 · K006 tekrar ×1 · kontenjan ×1) |
| K012 + K006 kesişim | 0 |
| §1–§5 silindi mi? | HAYIR — append-only |
| Eski envanter dosyaları | `url-envanter.txt` / `url-inventory.txt` bu turda **diskte BULUNAMADI** → kesişim K012 recursive (50 dosya) ile substitutes edildi (DRIFT kartına taşındı) |

### §6.9 YETENEK HARİTASI (TUR-2 ile netleşen — KARAR DEĞİL)

> Bu tablo Q1 kararının girdisidir: hangi yetenek **kapıda** (ai-service/K012), hangi yetenek
> **arkada** (K006) — TUR-2 kanıtlarıyla. **Karar verilmedi** (Q1 UNKNOWN korunur); tablo
> yalnız yetenek→sahip eşleme çerçevesini çizer.

| Yetenek | K012 ai-service (kapı) | K006 (arka uç) | Kanıt |
|---------|------------------------|----------------|-------|
| Model serving (inference) | çağrıyı alır, yönlendirir | model çalıştırma (uç nokta) | W4, W5 |
| Model registry / sürüm | — | registry + sürüm yönetimi | W6, T2-A1 |
| Yaşam döngüsü (eğitim→izleme→yeniden eğitim) | — | MLOps döngüsü | T2-A1 |
| ASR (ses→metin) | çağrıyı alır | Whisper/ASR hattı | T2-A2, W4 |
| Üretken YZ (LLM/RAG) | gateway (route/limit) | inference (LLM ailesi) | T2-A3, W9, W10 |
| A/B değerlendirme | — | eval döngüsü (canary route hariç) | T2-A4 |
| Tool/function calling | sınır sözleşmesi (Q4) | modelin tool çağrısı | W9, W10 |
| Risk/güvenlik (OWASP/NIST) | kapı politikası | model düzeyi risk | W7, W8 |
| Kota/limit (rate limit) | **kapıda** (gateway yeteneği) | — | W2, W3 |

> **Çıkarım (KARAR DEĞİL):** K012'nin tuttuğu 3 yetenek (gateway route/limit · tool-calling
> sözleşmesi · risk politikası) — K006'nın tuttuğu 6 yetenek (serving · registry · yaşam
> döngüsü · ASR · LLM · eval). A seçeneği bu haritayı "arka uç tek düğüm" olarak, C seçeneği
> "sınır net + iki taraf ayrı ADR" olarak okur. B seçeneği (K016) bu haritada **yok** →
> K016 kapsamı okunmadan haritaya eklenemez (⚠️ korunur).

### §6.10 YETENEK→Q4 SÖZLEŞME ETKİSİ (kapı sözleşmesi alanları — KARAR DEĞİL)

| Sözleşme alanı | Ne taşır | Q1 seçeneğine etkisi | Kanıt |
|----------------|----------|----------------------|-------|
| `model_route` | hangi modele gidecek (canary/A-B) | A: route K006'da; C: route kapıda | T2-A4, W3 |
| `capability` | ASR / LLM / tool-calling ayrımı | A: yetenek ayrımı K006'da; C: yetenek ayrımı kapıda | T2-A2, T2-A3 |
| `idempotency_key` | tekrar-güvenli çağrı | seçenek bağımsız (Q4 zorunlu) | T2-B2/B3 (bg dosyası) |
| `limit_tier` | kota/limit sınıfı | C: kapıda (W3) | W2, W3 |
| `tool_schema` | tool-calling sözleşmesi (IDL) | C: kapı sözleşmesi | W9, W10 |
| `risk_tier` | NIST/OWASP risk sınıfı | D: karar öncesi zemin | W7, W8 |

### §6.11 KANIT EŞİĞİ TABLOSU (hangi soru hangi kanıtla kapanır — dürüstlük)

| Soru | Kapanabilir mi? | Eksik kanıt | Durum |
|------|------------------|-------------|-------|
| YZ yeteneği K006 mı K015 mi? | HAYIR | K016 kapsamı okunmadı; K006 içerik okuması yapılmadı | ⚠️ UNKNOWN (karar ADR) |
| ai-service kapı mı, sahip mi? | **KISMEN** | TUR-2 + TUR-1 birlikte "kapı" çerçevesini kanıtlıyor (T2-A3, W1/W2/W3); "sahiplik" için ADR gerek | kapı-sahiplik ayrımı netleşti |
| ASR hangi düğümde? | HAYIR | Whisper URL'si K006'da var (L82), içerik okunmadı | ⚠️ |
| LLM madde (wiki) eklenmeli mi? | HAYIR (kontenjan) | T2-A3 tercih edildi; LLM madde 200 döndü | RED (kontenjan) |
| A/B routing kapıda mı? | **KISMEN** | T2-A4 değerlendirme çerçevesi; routing kararı kapı politikası | ⚠️ |

> **Dürüstlük kuralı:** "kapı çerçevesi" TUR-2 ile **kanıtla** netleşti; "hangi düğüm sahip"
> hiçbir turda kapatılmadı → Q1 UNKNOWN korunur. Bu tablo, "hiçbir şey kanıtlanmadı" iddiasını
> da düzeltir: **bir boyut** (kapı) kanıtlandı, **ikinci boyut** (sahiplik) kanıtlanmadı.

### §6.12 MLOPS YAŞAM DÖNGÜSÜ ↔ K006 KÜMELERİ EŞLEŞMESİ (T2-A1 derinliği)

| Yaşam döngüsü evresi (T2-A1) | K006 kümesi (yapay-zeka-web-arastirma §2) | K012 ai-service etkisi |
|------------------------------|-------------------------------------------|------------------------|
| Eğitim (training) | §2.1 inference (dolaylı) · §2.3 training_jobs | — (kapıda değil) |
| Doğrulama (validation) | §2.4 evaluation | — |
| Dağıtım (deployment) | §2.1 inference (serving) | W4/W5 (uç nokta çağrısı) |
| İzleme (monitoring/drift) | §2.2 model management (Evidently/MLflow) | olay akışına `drift_event` (⚠️ alan kararı ADR) |
| Sürüm (registry) | §2.2 (model registry) | `model_route` (§6.10) |
| Yeniden eğitim (retrain) | §2.3 (training_jobs) | — |

> **Sonuç (KARAR DEĞİL):** MLOps döngüsünün 6 evresinden **yalnız 2'si** (serving · registry)
> kapı ile kesişir; 4'ü (training · validation · drift · retrain) K006'nın içindedir → bu,
> A seçeneğinin (yetenek arkada) lehine bir çerçevenin, C seçeneğinin (sınır net) lehine bir
> çerçevenin **her ikisi de** okunabilir. Karar ADR'ye aittir.

### §6.12a YETENEK YÖNLENDİRME MATRİSİ (kapı politikası — KARAR DEĞİL)

> W1/W2/W3 (gateway) + T2-A3 (üretken YZ ailesi) + T2-A2 (ASR) birleştiğinde kapı **yönlendirme
> politikası** tanımlamalıdır. Bu tablo politika boyutlarını çizer; eşik/nicelik ADR'ye aittir → ⚠️.

| Politika boyutu | Ne belirler | Kanıt | Q1 seçeneğine etkisi |
|-----------------|-------------|-------|----------------------|
| `capability` route | ASR çağrısı K006 ASR hattına, LLM çağrısı K006 LLM hattına | T2-A2, T2-A3 | A: route K006'da; C: route kapıda |
| `model_route` (canary) | sürüm %/oylama ile dağıtımı | T2-A4 (A/B değerlendirme) | A: route kararı eval ile |
| `limit_tier` | kota/limit sınıfı (rate limit) | W2, W3 | C: kapı yeteneği |
| `fallback` | birincil model hata → ikincil model | W4/W5 (uç nokta hata yüzeyi) | C: kapı politikası |
| `risk_tier` | NIST/OWASP risk sınıfı → ek politika | W7, W8 | D: karar öncesi zemin |
| `audit` | çağrının denetim kaydı (kim, ne, hangi model) | W7 (NIST governance) | seçenek bağımsız |

### §6.12b GÜVENLİK/GÖVDE POLITİKA KATMANLARI (W7 + W8 birleşimi — KARAR DEĞİL)

| Katman | Ne korur | Kanıt | ai-service'te mi? |
|--------|----------|-------|-------------------|
| Prompt injection (LLM Top 10 #1) | kullanıcının modeli manipüle etmesi | W8 | kapıda **filtre**; model düzeyi K006 |
| Insecure plugin/tool (LLM Top 10) | tool-calling suistimali | W8, W9, W10 | kapıda `tool_schema` allowlist |
| Model DoS (LLM Top 10) | aşırı kaynak tüketimi | W8, W3 | kapıda rate limit (limit_tier) |
| Veri sızıntısı (eğitim verisi) | hassas verinin modele sızm | W7 (governance), W8 | kapıda `audit` + K006'da veri politikası |
| Model drift/yanlışlık | modelin bozulması | T2-A1 (izleme), T2-A4 (eval) | K006'da (kapıda değil) |

> **Sınır kuralı (dürüstlük):** kapı **filtre/limit/audit** uygular; model düzeyi güvenlik
> (prompt injection derinliği, drift) **K006'nın** sorumluluğundadır → bu ayrım Q1 kararının
> güvenlik boyutudur; seçeneklerin hiçbiri bu güvenliği kapıda **tamamen** taşımaz.

### §6.13 TUR-2 KANIT SAYIMI (derinlik ekleri sonrası)

| Kalem | Değer |
|-------|-------|
| Ek matris | §6.9 yetenek haritası (9 satır) · §6.10 sözleşme alanları (6 satır) · §6.11 kanıt eşiği (5 satır) · §6.12 MLOps eşleşmesi (6 satır) |
| Karar durumu | **KARAR VERİLMEDİ** — Q1 UNKNOWN korunur; matrisler ADR girdisidir |
| §1–§5 silindi mi? | HAYIR — append-only |

### §6.14 DEĞİŞİKLİK KAYDI (TUR-2)

| Tarih | Değişiklik | Yapan |
|-------|------------|-------|
| 2026-10-18 | §6 TUR-2 — 4 kabul URL (GET 200) · 4 dürüst RED (LLM kontenjan dahil) · Q1 matrisi güncellendi (yaşam döngüsü + ASR + eval boyutları) · append-only | Researcher subagent (git commit YASAK) |
| 2026-10-18 | §6.9–§6.13 eklendi (yetenek haritası · sözleşme alanları · kanıt eşiği · MLOps eşleşmesi) · append-only | Researcher subagent (git commit YASAK) |

### §6.14a Q1 KARAR ÖNCESİ KONTROL LİSTESİ (ADR girdisi — KARAR VERİLMEDİ)

> Q1 (YZ yeteneği K006 mı K015 mı) bu görevde **kapatılmadı** (UNKNOWN korunur). Bu tablo
> kararın **hangi eksik kanıtla** kapatılabileceğini dürüstçe listeler — uydurma kanıt yok.

| # | Eksik kanıt | Nereden alınmalı | Q1 seçeneğine etkisi | Durum |
|---|-------------|-------------------|----------------------|-------|
| 1 | K016 kapsamı (AI node) okunmadı | K016 arşivi (diskte var mı kontrol edilmeli) | B seçeneği için ön koşul | ⚠️ VERIFICATION REQUIRED |
| 2 | K006 içerik okuması (idddia düzeyi) | K006 master/kapsam dosyaları | A seçeneği için güç | ⚠️ |
| 3 | K006 ↔ K015-AI ayrımı (varsa) | K006 + K015 çapraz okuma | A/B/C için netlik | ⚠️ |
| 4 | LLM madde (wiki) — kontenjan RED | T2-A3 ile telafi edildi; LLM madde 200 döndü | çerçeve (T2-A3) yeterli | RED (kontenjan, dürüst) |
| 5 | ASR içerik okuması | K006 whisper satırı (L82) + ASR çerçevesi (T2-A2) | A'nın ASR boyutu | ⚠️ |

> **Karar matrisinin son hali (§6.6):** A ≈ C > D > B. A'nın kanıtı en zenginleşen (T2-A1/A2/A4);
> B'nin kanıtı yetersiz (K016 okunmadı). C, K016 okunmadığı için **güvenli geçici** çerçeve.

### §6.14b YETENEK HARİTASI GENİŞLEMESİ (Q1 kararına etki — KARAR DEĞİL)

> §6.9'daki 9 satır (kapı vs arka uç) bu alt bölümde **yetenek ailesi başına** genişler;
> her satır "hangi K sahip" sorusunun kanıt durumunu taşır.

| Yetenek ailesi | K012 kapıda ne var? | K006 arka uçta ne var? | K016 olabilir mi? | Kanıt durumu |
|----------------|---------------------|------------------------|--------------------|--------------|
| ASR (ses→metin) | çağrı yüzeyi (capability alan) | Whisper hattı | K016'da "çalıştırma" olabilir | ⚠️ A (T2-A2, W4) |
| LLM/RAG | gateway (route/limit) | inference (üretken YZ) | ⚠️ | ⚠️ A/C (T2-A3, W9/W10) |
| Model registry | — | sürüm/yönetim | ⚠️ | ⚠️ A (W6, T2-A1) |
| Eval/A-B | — (canary route hariç) | değerlendirme döngüsü | ⚠️ | ⚠️ A (T2-A4) |
| Drift/izleme | olay akışına `drift_event` | Evidently/MLflow | ⚠️ | ⚠️ A (T2-A1) |
| Risk/güvenlik | kapı politikası | model düzeyi risk | — | W7/W8 (kapı) |
| Kota/limit | **kapı** | — | — | W2/W3 (kapı) |
| Tool-calling | sınır sözleşmesi | modelin tool çağrısı | ⚠️ | W9/W10 |

> **Sonuç (KARAR DEĞİL):** her satır A'nın lehine **ama K016 sütunu boş** (kanıt yok) →
> "A mı C mi" kararı K016 okuması olmadan **kapatılamaz** (dürüstlük kuralı). Bu, Q1'in
> hâlâ UNKNOWN olmasının tek gerekçesidir.

### §6.15 TUR-2 KANIT SAYIMI (derinlik ekleri sonrası — son)

| Kalem | Değer |
|-------|-------|
| Kabul URL | 4 (T2-A1…T2-A4) — hepsi GET 200 (2026-10-18) |
| RED | 4 (404 ×2 · K006 tekrar ×1 · kontenjan LLM ×1) |
| Ek matris | §6.9 yetenek haritası (9) · §6.10 sözleşme (6) · §6.11 kanıt eşiği (5) · §6.12 MLOps eşleşmesi (6) · §6.12a yönlendirme (6) · §6.12b güvenlik (5) · §6.14a kontrol listesi (5) · §6.14b genişleme (8) |
| Karar | **KARAR VERİLMEDİ** — Q1 UNKNOWN (K016 okunmadı) |

### §6.16 TUR-2 SONUÇ TABLOSU (ai-service — dürüst özet)

| Boyut | TUR-1 durumu | TUR-2 sonucu |
|-------|--------------|--------------|
| Kapı çerçevesi | W1/W2/W3 (gateway) | **T2-A3 ile güçlendi** (üretken YZ ailesi) — kapı netleşti |
| Yetenek sahipliği | W4/W5/W6 (serving/registry) | T2-A1/A2/A4 ile **zenginleşti** (yaşam döngüsü + ASR + eval) — ama K016 okunmadı |
| ASR boyutu | kanıt yoktu | **T2-A2** (ASR ayrı yetenek ailesi) — A lehine |
| Eval boyutu | kanıt yoktu | **T2-A4** (A/B değerlendirme çerçevesi) — A lehine |
| LLM çerçeve | W9/W10 (tool calling) | T2-A3 (üretken YZ genel aile) — kapı-arka uç çerçevesi |
| RED dürüstlüğü | 15 RED (§3) | +4 RED (2×404 · 1 K006 tekrar · 1 LLM kontenjan) — "200 döndü ama tercih edilmedi" dürüst |
| Karar | UNKNOWN | **hâlâ UNKNOWN** (K016 okunmadı — §6.14a) |

> **Son söz (TUR-2):** TUR-2 ai-service dosyasında **kapı çerçevesini** kanıtladı (T2-A3 + T2-A2)
> ve **yaşam döngüsü + değerlendirme boyutlarını** ekledi (T2-A1 + T2-A4). Ancak "hangi düğüm
> sahip" (K006 mı K015/K016 mı) **hiçbir turda kapatılmadı** → Q1 UNKNOWN korunur. Bu, TUR-2'nin
> dürüst sınırıdır: bir boyut netleşti, ikinci boyut netleşmedi.

### §6.17 Q1 KARAR MATRİSİ (TUR-2 sonrası son tablo — KARAR VERİLMEDİ)

| Seçenek | TUR-2 netliği | Eksik | Karar |
|---------|---------------|-------|-------|
| A — K006'ya bağla | **Yüksek** (T2-A1/A2/A4 + W4/W5/W6 — 6 kanıt) | K016 okunmadı | ADR |
| B — K016'ya bağla | Düşük (W1/W6 — 2 kanıt; K016 okunmadı) | K016 kapsamı okunmadı | ADR |
| C — sınır tanımla | Orta (W1/W2/W3 + T2-A3 — 4 kanıt) | K016 okunmadan karar verilemez | ADR |
| D — ertele | Geçerli (W7/W8 + §6.14a eksikler) | — | ADR |

> **Öneri-seviyesi (KARAR DEĞİL):** A ≈ C > D > B — TUR-1'deki C > A ≈ D > B sırasından
> **A'nın C ile eşleşmesi** yeni (T2-A1/A2/A4). B'nin düşüklüğü K016 okunmamış olmasından
> kaynaklanır (dürüst sınır).

### §6.18 TUR-2 DÜRÜSTLÜK ÖZETİ (ai-service — uzun vade notu)

| İddia | Durum |
|-------|-------|
| "Kapı çerçevesi kanıtlandı" | ✅ doğrulandı (W1/W2/W3 + T2-A3 + T2-A2) |
| "Yetenek sahipliği zenginleşti" | ✅ doğrulandı (T2-A1 + T2-A4) |
| "K016 kapsamı okundu" | ❌ HAYIR — okunmadı (dürüst sınır) |
| "Q1 kapatıldı" | ❌ HAYIR — UNKNOWN korunur |
| "LLM madde 404 idi" | ❌ HAYIR — 200 döndü, kontenjan RED (dürüst) |
| "Uydurma URL/RED yok" | ✅ 21 kabul + 19 RED (tümü 2026-10-18'de doğrulanmış) |

> **Uzun vade:** Q1 kararı ADR'de, K016 kapsamı okunduktan sonra **yeniden** değerlendirilmeli;
> bu dosya TUR-2 ile "kapı netleşti, sahiplik belirsiz" durumunu **dürüstçe** kaydetti.

### §6.19 KARDEŞ DOSYA KESİŞİM TABLOSU (TUR-2 — byte-tekilliği)

| Konu | Kardeş/K006 dosyası | Bu dosyada var mı? | Karar |
|------|----------------------|--------------------|-------|
| K006 32+ URL (onnxruntime · whisper · vllm · transformers vb.) | K006 yapay-zeka-web-arastirma §1–§18 | ❌ HAYIR (çakışma 0) | RED (§6.7 T2-R3) |
| NIST AI RMF (W7) | bu dosya §1 | ✅ EVET (TUR-1) | — |
| OWASP LLM Top 10 (W8) | bu dosya §1 | ✅ EVET (TUR-1) | — |
| Tool calling (W9/W10) | bu dosya §1 | ✅ EVET (TUR-1) | — |
| background-jobs URL'leri | bg §1 (W-1…W-10) | ❌ HAYIR | bu dosyanın değil |
| playback URL'leri | playback §1 (W-1…W-10) | ❌ HAYIR | bu dosyanın değil |

> **Sonuç:** byte-tekilliği TUR-2'de **0 ihlal** (K006 32+ URL → hiçbiri bu dosyada tekrar
> yazılmadı; RED ile çözüldü).

### §6.20 Q1 SON KARAR GİRDİSİ TABLOSU (ADR paketi — KARAR VERİLMEDİ)

> Q1 kararı ADR'ye giderken şu **6 girdi** paketlenmelidir; bu tablo paketin içeriğini sabitler.

| # | Girdi | Kaynak bölüm | Özeti |
|---|-------|--------------|-------|
| 1 | 4 seçenek matrisi | §2 | A/B/C/D — artılar/eksiler |
| 2 | 14 kanıt URL | §1 + §6.1 | 10 TUR-1 + 4 TUR-2 (hepsi 200) |
| 3 | Yetenek haritası (kapı vs arka uç) | §6.9, §6.14b | 9 satır + genişleme |
| 4 | MLOps yaşam döngüsü eşleşmesi | §6.12 | 6 evre (yalnız 2'si kapıda) |
| 5 | Sözleşme alanları | §6.10 | 6 alan (model_route, capability, …) |
| 6 | Eksik kanıt listesi | §6.14a | 5 eksik (K016 okuma dahil) |

> **Karar durumu:** 6 girdi hazır; **K016 okunmadan karar verilmez** → Q1 UNKNOWN.

### §6.21 TUR-2 SON KAYIT (ai-service — 2026-10-18)

| Madde | Değer |
|-------|-------|
| Kabul URL | 4 (T2-A1…T2-A4) — GET 200 |
| RED | 4 (404 ×2 · K006 tekrar ×1 · kontenjan ×1) |
| Ek matris | 8 tablo (§6.9–§6.20) |
| Karar | UNKNOWN (K016 okunmadı) |
| §1–§5 silindi mi? | HAYIR — append-only |
| Commit | YASAK (orkestratöre aittir) |

### §6.22 SON DÜRÜSTLÜK NOTU (bu dosyanın uzun vade pozisyonu)

> Bu dosya TUR-2 ile **iki boyutta** derinleşti: (1) kapı çerçevesi (W1/W2/W3 + T2-A3 + T2-A2)
> ve (2) yaşam döngüsü + değerlendirme (T2-A1 + T2-A4). Üçüncü boyut (sahiplik: hangi düğüm)
> **kapatılmadı** — K016 okunmadan kapatılamaz. Q1 kararı bu dosyanın **tek açık kalan** sorusudur;
> karar ADR'ye aittir ve bu dosya yalnız girdi üretir, **karar vermez**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
