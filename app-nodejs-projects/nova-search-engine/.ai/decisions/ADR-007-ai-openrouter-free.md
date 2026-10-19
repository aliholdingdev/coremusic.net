# ADR-007 — AI: OpenRouter Free + Fallback Zinciri

- **Durum:** Kabul
- **Tarih:** 2026-10-16
- **Alan:** Yapay zekâ

## Bağlam

AI destekli arama (sorgu genişletme, filtreleme) için LLM gerekli. Bütçe: ücretsiz
modeller. Kullanıcı anahtarı `.env` üzerinden sağladı.

## Karar

- Sağlayıcı: **OpenRouter** (`/api/v1/chat/completions`, fetch — SDK yok)
- Modeller (`packages/ai/src/models.ts`):
  1. `openrouter/free` (otomatik yönlendirici)
  2. `google/gemma-4-26b-a4b-it:free`
  3. `nvidia/nemotron-3-super-120b-a12b:free`
  4. `thinkingmachines/inkling:free`
- **Doğrulama:** 2026-10-16'da OpenRouter `/api/v1/models` çıktısından teyit
  edildi (465 model tarandı). `deepseek/deepseek-chat` ve `llama-3.1-8b-instruct`
  artık ÜCRETLİDİR — free listesine alınmaz.

## Kritik kurallar

1. **AI aramayı ASLA bloklamaz:** anahtar yoksa / tüm modeller düşerse heuristic
   analiz devreye girer (`analyzeQuery` her zaman çalışır).
2. **Rate limit:** dakikada 20 istek (token bucket); retry'lar backoff + jitter.
3. **Gizlilik:** anahtar yalnız `.env`'de; log'a/çıktıya yazılmaz.
4. Free listesi **canlı hedeftir** — `models.ts` en geç 3 ayda bir yeniden
   doğrulanır (MEMORY.md uyarısı).

## Sonuçlar

- `OpenRouterClient.chatJSON` kirli yanıttan JSON çıkarır, hatada fallback döner
- AI'ın görevi sorgu genişletme (deep search varyantları) + ipucu yakalama;
  skorlama deterministik kalır (test edilebilirlik)
