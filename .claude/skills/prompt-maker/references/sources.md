---
title: "PROMPT-MAKER — WEB ARAŞTIRMA KAYNAKLARI (Truth Mode)"
version: "4.0.0"
updated: "2026-10-21"
type: reference
purpose: "Bu skill'in prompt mühendisliği iddialarının dayandığı resmi kaynaklar. Her iddia = kaynak + erişim tarihi."
---

# WEB ARAŞTIRMA KAYNAKLARI — Truth Mode Kanıt Defteri

> **Kural (Zero-Hallucination):** Bu dosyadaki her iddia fetch edilip doğrulanmış
> kaynaklara dayanır. Kaynaksız iddia bu dosyaya yazılmaz. Doğrulanamayan iddia
> `⚠️ VERIFICATION REQUIRED` etiketi taşır ve üretilen prompt'a kesin kural olarak
> girmez.

**Erişim tarihi (tümü):** 2026-10-21
**Doğrulama turu:** Dalga 3 reset'i sırasında 5 kaynak yeniden fetch edilerek
doğrulandı (K1–K5). Bu turda eski `⚠️ VERIFICATION REQUIRED` olan iki kalem
(K2-6 prefill, K3-6 reusable prompts) resmi metinle teyit edilerek seviye
yükseltildi — ayrıntı ilgili maddelerde.

---

## K1 — Anthropic Prompt Engineering Overview

| Alan | Değer |
|------|-------|
| URL | `https://platform.claude.com/docs/en/build-with-claude/prompt-engineering/overview` |
| Erişim | 2026-10-21 |
| Not | `docs.anthropic.com/.../prompt-engineering/overview` bu adrese redirect eder; iki URL aynı içeriktir |
| Sahip | Anthropic (resmi Claude docs) |

**Doğrulanan iddialar:**

1. Prompt mühendisliği, modelin istenen görevi güvenilir biçimde yerine getirmesini
   sağlayacak girdiyi tasarlamaktır.
2. **Başarı kriterlerini önce tanımla:** istenen çıktıyı ve nasıl değerlendirileceğini
   netleştir; doküman bu kriterleri ön koşul sayar (success criteria + empirical tests
   + first draft prompt yoksa önce o kurulur).
3. Belirsiz girdi belirsiz çıktı üretir — netlik model kalitesinden bağımsız bir
   tasarım sorumluluğudur (K2'deki "clear and direct" maddesiyle desteklenir).
4. Tüm tekniklerin (clarity, examples, XML structuring, role prompting, thinking,
   prompt chaining) tek "living reference" adresi **Prompting best practices**
   sayfasıdır (K2) — sürüm takibi oradan yapılır.

## K2 — Anthropic Claude Prompting Best Practices

| Alan | Değer |
|------|-------|
| URL | `https://platform.claude.com/docs/en/build-with-claude/prompt-engineering/claude-prompting-best-practices` |
| Erişim | 2026-10-21 |
| Sahip | Anthropic (resmi Claude docs) |
| Sayfa durumu | "living reference" — model listesi ve teknikler sürekli güncellenir |

**Doğrulanan iddialar:**

1. **Be clear and direct:** spesifik ol; "above and beyond" istiyorsan açıkça iste.
   *Altın kural (dokümandan):* prompt'u görevi az bilen bir meslektaşa göster;
   o şaşırırsa model de şaşırır. Adım sırası/eksiksizlik önemliyse numaralı liste kullan.
2. **Context / motivation ekle:** kuralın *neden* olduğunu söylemek modelin
   genellemesini iyileştirir (ör. "asla ünlem kullanma" yerine "çıkış TTS tarafından
   okunacak" gerekçesi).
3. **Examples (few-shot):** 3–5 örnek en iyi sonucu verir; örnekler **relevant**,
   **diverse** (edge case kapsayan) ve **structured** olmalı — her örnek `<example>``,
   topluluk `<examples>` etiketiyle sarmalanır ki talimattan ayrışsın.
4. **XML ile bölümleme:** `<instructions>`, `<context>`, `<input>` gibi tutarlı,
   açıklayıcı etiketler; hiyerarşi varsa iç içe (`<documents>` → `<document index="n">`).
5. **Role:** system prompt'ta tek cümlelik rol bile davranışı/tonu odaklar.
6. **Uzun bağlam kuralı:** 20k+ token girdide uzun veri prompt'un **üstünde**,
   sorgu/talimat/örnekler **altta**; doküman testlerinde sorgunun sonda olması
   kaliteyi "up to 30 percent" iyileştirebilir (dokümanın kendi ifadesi).
7. **Uzun doküman işlerinde quote-first:** önce ilgili alıntılar (`<quotes>`),
   sonra görev — model gürültüyü eleyip kanıta bağlanır.
8. **Format steerance:** "ne yapma" yerine "ne yap"; XML format göstergesi
   (`... etiketleri içinde yaz`); prompt'un stilini hedef çıktı stiliyle eşle.
9. **Prefill ÖLÜ (seviye yükseltildi 2026-10-21):** "Starting with Claude 4.6
   models and Claude Mythos Preview, prefilled responses … are no longer supported.
   Requests with prefilled assistant messages to these models return a 400 error."
   Eski `⚠️ VERIFICATION REQUIRED` bu metinle **VERIFIED** oldu. JSON/sınıflandırma
   prefill'leri yerine **Structured Outputs** / enum'lu tool alanı önerilir.
10. **Tool kullanımda açık ol:** "öner" dersen önerir; "değiştir" dersen değiştirir.
    Eylem eşiği prompt ile ayarlanır (`default_to_action` / `do_not_act_before_instructions`).
11. **Thinking:** güncel modellerde **adaptive thinking** (`effort` + karmaşıklık);
    extended thinking `budget_tokens` Claude 4.7+ modellerde 400 döner. Manuel CoT
    yalnız thinking kapalıyken fallback. "Before you finish, verify your answer against
    [test criteria]" kalıbı self-check için etkilidir (bazı modellerde aşırı doğrulama
    riskine karşı dikkatli olunacağı dokümanda ayrıca yazılıdır).
12. **Model-köprüleme:** teknik adı geçen modele göre ölçülür; başka modele
    taşırken kendi eval'ınla teyit et.

## K3 — OpenAI Prompt Engineering Guide

| Alan | Değer |
|------|-------|
| URL | `https://platform.openai.com/docs/guides/prompt-engineering` |
| Erişim | 2026-10-21 |
| Sahip | OpenAI (resmi docs) |

**Doğrulanan iddialar:**

1. **Rol otoritesi:** `instructions` parametresi / **developer** mesajı, `input` /
   **user** mesajından önceliklidir (OpenAI model spec "chain of command").
   Doküman açıkça der: developer = fonksiyon tanımı (kurallar/iş mantığı),
   user = fonksiyon argümanları (girdi). **Talimat veri ayrımı** injection
   savunmasının temelidir.
2. **Developer message sırası (önerilen):** Identity → Instructions → Examples →
   Context; doküman "usually in this order (though the exact optimal content and
   order may vary by model)" der — kesin mutlak değil, **öneri** olarak kaydedilir.
   Context en sonda, çünkü istekler arası değişir.
3. **Markdown + XML bölümleme:** başlık/liste = hiyerarşi; XML = içerik sınırı;
   XML attribute = talimatta referanslanabilecek metadata.
4. **Few-shot çeşitliliği:** "show a diverse range of possible inputs with the
   desired outputs" — tek tip örnek tek tipe aşırı genelleme yapar.
5. **Prompt caching:** sık kullanılacak sabit blok prompt'un **başına** ve JSON
   istek gövdesinde ilk parametrelere konur (prefix-match); değişken içerik sona.
6. **Reusable prompt objects (seviye yükseltildi 2026-10-21):** dokümanda birebir
   şu geçiyor: "Prompt creation will be de-emphasized beginning **June 3, 2026**,
   and `v1/prompts` is scheduled to shut down on **November 30, 2026**."
   Tarihler resmi sayfadaki deprecation notundan **VERIFIED** edildi; buna karşılık
   OpenAI üretimi prompt'ların **kod içinde** (typed args + test + deployment)
   sürülmesini önerir. ⚠️ Not: kademeli kapanış takvimi resmi deprecation page'den
   son bir kez daha teyit edilmeden yeni entegrasyon planına girmez.
7. **Reasoning modelleri farklıdır:** "reasoning model = senior co-worker" —
   hedef ver, detayı ona bırak; GPT-tarifi modeller "junior" gibi açık talimat
   ister. "Adım adım düşün" zinciri reasoning modellerde genelde gereksizdir.
8. **Model pinleme + eval:** üretimde belirli model snapshot'ına pinle; prompt
   davranışını test/eval ile izle.

## K4 — MDN: JSON (veri interchange — çıktı şema doğrulama)

| Alan | Değer |
|------|-------|
| URL | `https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON` |
| Erişim | 2026-10-21 |
| Sahip | MDN Web Docs (Mozilla) |

**Doğrulanan iddialar:**

1. Geçerli JSON sözdizimi ABNF olarak **RFC 8259**'da tanımlıdır (MDN'de tam
   grammar bloğu mevcut).
2. Nesne anahtarları **çift tırnak** içinde yazılır; tek tırnak geçersiz.
3. **Trailing comma yasak**; `undefined` ve yorum satırı JSON'da yoktur.
4. "JSON döndür" kalıbı prompt'a yazılacaksa bu kurallar çıktı doğrulamasının
   asgari şartıdır; şema alanları (isim / tip / zorunluluk) prompt içinde açıkça
   tarif edilmezse model tutarsız JSON üretir.

## K5 — OpenAI Structured Outputs (JSON Schema)

| Alan | Değer |
|------|-------|
| URL | `https://developers.openai.com/api/docs/guides/structured-outputs` |
| Erişim | 2026-10-21 |
| Sahip | OpenAI (resmi docs) |

**Doğrulanan iddialar:**

1. **Structured Outputs**, model çıktısının verilen **JSON Schema**'ya sadık
   olmasını garanti eder; zorunlu anahtar eksiltme / uydurma enum engellenir.
2. **Structured Outputs vs JSON mode:** ikisi de geçerli JSON üretir; yalnız
   Structured Outputs **şemaya uyar**. JSON mode yerine mümkünse Structured
   Outputs önerilir.
3. İki kullanım biçimi: (a) function calling, (b) `text.format` içinde
   `json_schema` + `strict: true`. Araç/veri köprüsü ise function calling;
   kullanıcının gördüğü yanıt şeması ise `text.format`.
4. Strict şemada `required` + `additionalProperties: false` örnekleri dokümanda
   standarttır (tip güvenliği "no need to validate or retry").
5. Prompt-maker karşılığı: prompt'a "JSON döndür" yerine **şema + strict
   niyeti** tarif et; üretim tarafı şemayı kodda tutsun (K3-6 ile uyumlu:
   prompt kodda sürülür).

---

## Kaynak Kullanım Matrisi (hangi iddia hangi kaynağa dayanır)

| Prompt-maker kuralı | Kaynak |
|---------------------|--------|
| Başarı kriteri önce tanımlanır | K1 |
| Net rol/kişilik (Persona) | K2 |
| 3–5 `<example>` few-shot, çeşitli örnekler | K2, K3 |
| XML/Markdown bölümleme | K2, K3 |
| Talimat > veri ayrımı (injection savunması) | K3 |
| Developer message sırası (Identity→Instructions→Examples→Context) | K3 |
| Uzun bağlam üste, görev altta | K2 |
| Prompt caching başa, değişken sona | K3 |
| Reasoning modelde zincir zorlama yok | K3 |
| JSON çıktı doğrulama (çift tırnak, virgül, RFC 8259) | K4 |
| JSON şema + strict (structured outputs) | K5 |
| Prefill kullanma (Claude 4.6+ → 400) | K2 |
| Prompt kodda sürülür, v1/prompts kapanıyor | K3 |
| Self-check / verify talimatı | K2 |
| "Ne yap" format talimatı, "ne yapma" yerine | K2 |

## Doğrulama Protokolü (bu dosyanın kuralı)

1. **Hızlı tur:** yeni prompt kuralı yazmadan önce 2–5 resmi kaynak (MDN / WHATWG /
   W3C / ECMA / Anthropic / OpenAI resmi docs) taraması — varsayılan seviye.
2. **Derin tur:** mimari karar, güvenlik advisory, sürüm upgrade → 10+ kaynak,
   çapraz doğrulama zorunlu.
3. **Çelişki:** iki resmi kaynak çelişirse ikisi de yazılır; tek tane "doğru"
   sanılmaz — kullanıcıya sunulur.
4. **Tarih:** her kaynak erişim tarihiyle kaydedilir (bu dosya: 2026-10-21).
5. **Eşik:** 90–100 güven = VERIFIED (kesin kural yazılabilir) · 60–89 =
   `⚠️ VERIFICATION REQUIRED` (prompt'a koşullu girer) · <60 = REJECTED
   (prompt'a girmez).
6. **Yaşayan doküman:** K2 ve K3 sürekli güncellenir; sürüm/kaldırma iddiaları
   (prefill, budget_tokens, v1/prompts) her üretim döngüsünde yeniden taranır.

---

*Truth Mode Kaynak Defteri v4.0.0 — CoreMusic Prompt Maker — Erişim: 2026-10-21*
