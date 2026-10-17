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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
