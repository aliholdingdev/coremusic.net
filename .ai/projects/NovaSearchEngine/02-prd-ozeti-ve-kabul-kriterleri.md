# NovaSearchEngine — 02 PRD Özeti ve Kabul Kriterleri

> Sürüm: 1.0.0 · Tarih: 2026-10-16 · Durum: Aktif
> **SSOT:** Tam PRD proje repoda `docs/PRD.md` (391 satır, 17 bölüm) — bu dosya ana vault **özeti**dir; çelişkide repodaki PRD kazanır.
> İlgili: [[01-kurulum-mimari-ve-acik-konular]]

---

## 1. Ürün Özeti (1 paragraf)

NovaSearch AI, YouTube'u yalnızca veri sağlayıcı olarak kullanan; her aramayı/keşfi kendi veritabanına yazan; offline'da daha önce keşfedilmiş içeriği arayabilen; AI (OpenRouter free) destekli kişisel metadata arama motorudur. YouTube klonu değildir — arama + metadata yönetimi. Tek kullanıcı, yerel/kişisel kullanım, localhost.

## 2. Kapsam Kilidi (V1)

| Yüzey | Tanım |
|-------|-------|
| Parametrik CLI | `nova-search` — flag'li (query, extraQuery, result, scroll, filter, profile, lang, sort, udate, searchBy, ai, offline, model, minViews, maxViews) |
| Menü Sihirbazı | `nova-search-wizard` — interaktif akış, aynı sözleşmeyi kullanır |
| Web | Vanilla JS + ITCSS/BEM — arama/sonuç/filtre/detay ekranları, API'den sunulur |
| REST API | Fastify, port 3001 — health/search/videos/channels/stats |
| AI | OpenRouter free — sorgu analizi + sonuç zenginleştirme + "Neden önerildi?" |
| DB | SQLite (dev) + MySQL (prod) — DatabaseAdapter ile |

**Kapsam dışı (V2/V3 ertelendi):** embedding/vektör, OCR, speech-to-text, knowledge-graph, öneri motoru, likeCount/publishedAt (V2), çoklu kullanıcı.

## 3. PRD §15 Kabul Kriterleri (bağlayıcı — 8 madde)

| # | Kriter | Ölçüm | 2026-10-16 Durumu |
|---|--------|-------|-------------------|
| 1 | `tsc --noEmit` 0 hata | komut çıktısı | ✅ DOĞRULANDI (0 hata) |
| 2 | `bun test` tümü yeşil | komut çıktısı | ✅ DOĞRULANDI (40/40 pass, 169 expect) |
| 3 | Canlı arama sonuçları benzersiz (dedup) | smoke test | ✅ DOĞRULANDI (5/5 unique, 2026-10-16 proje log) |
| 4 | `--offline` internet olmadan çalışır | offline test | ✅ DOĞRULANDI (local-db 11 sonuç) |
| 5 | AI anahtarı yoksa arama bozulmaz (fallback) | anahtarsız test | ✅ DOĞRULANDI (2026-10-16 commit'leri: fallback when OPENROUTER_API_KEY missing) |
| 6 | Her sonuç `score` + `reasons` taşır | API/web E2E | ✅ DOĞRULANDI (uyum %90 badge, 60 neden, detay paneli) |
| 7 | 2 CLI + Web + API aynı sözleşmeyi kullanır | mimari | ✅ (paylaşılan paketler — D1 kararı) |
| 8 | Anahtar hiçbir commit'te görünmez | git taraması | ✅ (.gitignore `.env` kapsıyor — 2026-10-16 doğrulandı) |

**Kabul kriteri 8/8 kapandı** (2026-10-16 — orkestratör doğrulaması; kanıtlar proje log + canlı komut çıktıları).

## 4. Test Kapısı (PRD §13 ↔ debate D5)

- Araç: **Bun test** (vitest değil) · hedef coverage **%75**.
- **ÖLÇÜM 2026-10-16:** funcs %52.21 / lines %60.30 → **KAPI AÇIK**.
- Öncelikli kapatma sırası: `database/mysql.ts` (canlı test) → `search/engine` (+bootstrap) → `youtube/*` (mock) → `workers/*` (anti-ban).

## 5. Kabul Edilen Riskler (PRD §16)

| Risk | Azaltma | Durum |
|------|---------|-------|
| Free model listesi değişir | fallback zinciri + aylık doğrulama | 2026-10-16: 15 free model doğrulandı; deepseek v3-0324:free ölü (404) |
| YouTube iç API değişir | izole provider katmanı + retry | yapısalcı (packages/youtube izole) |
| MySQL drift | DbAdapter + migration testleri | adapter yazıldı; canlı test BEKLEMEDE (MySQL 127.0.0.1:3306 CANLI erişilebilir) |
| Kapsam kayması | PRD kapsam kilidi | bu dosya §2 |
| Bayat süreç (eski kod) | değişiklik sonrası restart | deneyle öğrenildi (proje log 14:32 dersi) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-16
**Mode:** Red Team · Human Mode · Truth Mode
