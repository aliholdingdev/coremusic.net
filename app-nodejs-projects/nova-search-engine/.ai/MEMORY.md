# MEMORY.md — NovaSearch AI Oturum Hafızası

> Sonraki oturumlar buradan devam eder. Append-only; eski kayıtlar silinmez.

## 2026-10-16 — Sıfırdan Kurulum Oturumu

### Öğrenilenler (kritik)

1. **OpenRouter free liste canlı bir hedef.** 2026-10-16'da `deepseek/deepseek-chat`
   ve `meta-llama/llama-3.1-8b-instruct` artık **ücretli**; `google/gemini-2.0-flash-free`
   ve `deepseek/deepseek-r1` listeden düşmüş. Güncel free'ler: `openrouter/free`,
   `google/gemma-4-*:free`, `nvidia/nemotron-3*:free`, `thinkingmachines/inkling:free`.
   → `packages/ai/src/models.ts` periyodik doğrulanmalı (bkz. ADR-007).
2. **youtubei.js 18.x API:** `Innertube.create()` · `yt.search(q, SearchFilters)` ·
   `search.getContinuation()` (scroll) · `Video.video_id / length_text / view_count: Text`.
   `SearchFilters = { upload_date?, type?, duration?, prioritize?, features? }` —
   `sort_by` yoktur, sıralama yerelde yapılır.
3. **pnpm 11:** `onlyBuiltDependencies` artık `pnpm-workspace.yaml` içindedir;
   package.json'daki `pnpm` alanı okunmaz. `pnpm exec` bozuk bağımlılık kontrolünde
   takılırsa araçlar `node_modules/.bin/*` üzerinden doğrudan çalıştırılabilir.
4. **Node 22+:** SQLite için `node:sqlite` (DatabaseSync) gömülüdür — ek bağımlılık
   gerekmez; `.env` için `process.loadEnvFile()` yeterlidir (dotenv gerekmez).
5. **Dedup tuzağı:** farklı sorgu varyantlarından (AI genişletme) gelen aynı video
   birleşirken iç dedup yapılmalı — `filter` sırasında `seen` setine ekleyerek ilerle.
6. **`|| varsayılan` tuzağı:** `parseInt(x) || 7` ifadesinde geçerli `0` yutulur
   (`refresh --days 0` → 7 oldu, bug bulundu). Sayı ayrıştırmada `parseNumber` kullan.
7. **Stream URL'leri (2026-10):** web istemcisinde format URL'leri boş/şifreli gelir
   ("No valid URL to decipher"); **`getBasicInfo(id, { client: 'IOS' })** doğrudan
   googlevideo URL'i döndürür (17 format doğrulandı). ANDROID'te URL yok.

### Kullanıcı Kararları (bağlayıcı)

- Proje kökü: `C:\www\coremusic.net\app-nodejs-projects\nova-search-engine` (eski
  `C:\www\nova-search-engine` yalnız referanstır).
- Sıfırdan yazım; taşınmış kopya silindi (onaylı).
- CLI: **2 adet** (parametrik + menü sihirbazı).
- Web: **Vanilla JS** (ADR-004 korundu; React/Vue istenmedi).
- DB: **SQLite dev + MySQL prod** (PostgreSQL ADR'si revize edildi).
- AI: OpenRouter free + anahtar `.env`'e yazıldı (yerel/kişisel kullanım).

### Açık Konular

- MySQL adapter canlı veritabanıyla test edilmedi (docker-compose servisi hazır değil).
- `packages/youtube` arama düğümlerinde `likeCount`/`publishedAt` gelmiyor (V2).
- Test kapsamı %75 eşiğinin altında olabilir — `pnpm test:coverage` ile raporlanmalı.

### Toolchain (2026-10-16 güncel)

- **Bun 1.4.2** çalıştırıcı ve test koşucusudur; **esbuild/tsx/vitest KULLANILMAZ**.
- `bun test tests/` · `bun packages/cli/src/index.ts` · `bun apps/api/src/index.ts`
- `node:sqlite` + `process.loadEnvFile` Bun ile uyumludur (ölçüldü).
- TS tarafı `tsc --noEmit` ile denetlenir; `@types/bun` gerekli (`types: ["node","bun"]`).
