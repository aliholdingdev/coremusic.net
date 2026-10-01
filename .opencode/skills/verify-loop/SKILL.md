---
name: verify-loop
description: "Use when writing, reviewing, or changing any PHP, CSS, or JS web code — forces web search verification, browser MCP page testing (elements/layout), image-to-code matching, Figma data comparison, fix, and re-verify. Triggers: PHP, CSS, JS, kodlama, doğrula, verify, test, browser mcp, figma, layout, image eşleşme."
title: "CoreMusic — Verify Loop (Kod Doğrulama + Browser MCP Test)"
type: skill-instruction
version: 1.0
updated: 2026-09-30
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Web Search ile kod doğrulama
  - Browser MCP canlı sayfa testi (element + layout)
  - Image ↔ sistem kodu eşleşmesi
  - Figma data karşılaştırması
  - Düzelt → tekrar test → teyit + öğret
triggers:
  - "PHP kodu yaz"
  - "CSS düzelt"
  - "JS ekle"
  - "kodlamada doğrula"
  - "verify"
  - "browser mcp test"
  - "sayfaya git test et"
  - "figma ile karşılaştır"
  - "layout doğru mu"
  - "image eşleşme"
  - "elementleri kontrol et"
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/ui-design/reference/04-verification.md"
changelog:
  - version: 1.0
    date: 2026-09-30
    changes:
      - İlk sürüm — 5 aşamalı zorunlu doğrulama döngüsü
---

# VERIFY LOOP — CoreMusic

## MAX THINKING — Anti-Overthink (Kısa — 2026-10-01)

1. Reasoning = LOW; uzun analiz paragrafı, plan kompozisyonu, promptu geri anlatma YASAK.
2. Her dosya 1 kez okunur; ilk okumadan sonra KARAR VER — aynı veriyi ikinci kez analiz etme.
3. Arka arkaya 3 başarısız düzeltme → DUR, şüpheli varsayımı söyle, 1 kısa soru sor.
4. Bilinmeyen = UNKNOWN; gereksiz dosya/skill/agent/context/plan üretme.
5. Görev → aksiyon → sonuç (nokta atışı); aynı hatayı tekrarlama. Tam metin (7 madde): `@.ai/AGENTS.md` § MAX THINKING.



**Bu skill web kodu içeren her görevde zorunludur.** PHP, CSS veya JS içeren
her değişiklikte döngü çalıştırılmadan iş "tamamlandı" sayılmaz.

Temel prensip: **Kod iddia etmez, kanıtlanır.** Kanıtlanamayan sonuç
`⚠️ VERIFICATION REQUIRED` etiketi taşır.

---

## §0 Yetki Sınırı

- Bu skill **kod düzeltme yetkisi vermez**. Döngü hata bulursa:
  - `*.css` / `*.js` / frontend layout → **ui-designer**
  - `*.php` / routing / middleware → **backend-architect**
  - test/coverage → **qa-engineer**
  - `*.sql` / schema → **data-engineer**
- Skill sahibi ajan düzeltmeyi yapar, sonra döngü **Aşama 2'ye döner**.
- `git commit` bu skill'de ATILMAZ — orkestratöre aittir (AGENTS.md §13.9/5).

---

## AŞAMA 1 — Hazırlık (Mockup Gate)

Sıra değişmez (AGENTS.md §13): **PNG → ASCII → Inventory → Tokens → Reference**

1. İlgili görseli oku: `.ai/ui-design/screens/**` · `.ai/.png/**`
   (`.ai/.png/**` salt-okunur SSOT — değiştirilmez).
2. CSS/HTML/JS/layout/bileşen işlerinde mockup **okunmadan kod yazılamaz**.
   Görsel okunamıyorsa → **DUR ve bildir.**
3. Figma token doğrulaması için gate script **yalnız repo kökünden**
   (`C:\www\coremusic.net`) çalıştırılır — `.ai/` içinden çalıştırılan sonuç
   geçersizdir, sahte "PNG yok" üretir (AGENTS.md §13.6).

**Çıktı:** hangi mockup okundu, hangi ekran/katman hedef — 1 satır.

---

## AŞAMA 2 — Web Search Doğrulama

Yazılan veya incelenen her PHP/CSS/JS kodunda kullanılan API, fonksiyon,
özelliik veya pratik **web search ile doğrulanır**:

1. İlgi alanı: MDN (CSS/JS), php.net + PHP docs (PHP), OWASP (güvenlik).
2. Minimum **2 kaynak** çapraz kontrol; her iddiaya **kaynak URL + tarih**.
3. Deprecated / desteklenmeyen API → kodda `// ⚠️ VERIFICATION REQUIRED`
   etiketi ve düzeltme önerisi (H001 reddi).
4. Kaynak tarihi eskiyse veya çelişkiliyse → tahmin yok, **DUR + kullanıcıya sor**.

**Çıktı:** doğrulanan her iddia için `id → kaynak → tarih` listesi.

---

## AŞAMA 3 — Browser MCP Canlı Test (ZORUNLU)

Bu aşama atlanamaz. Sayfa **gerçek tarayıcıda** test edilir:

1. Dev sunucusu başlat (gerekirse): `php -S localhost:81 -t public/`
   — auth gerekiyorsa bypass **yalnız dev sunucusunda** ve
   `shared/test_bypass.php` / fail-closed middleware testleri biliniyor olarak.
2. Browser MCP ile sayfayı aç: `browser.tabs.open` (veya `playwright.browser_navigate`).
3. **Konsol hatası** topla (console/network error) — hata varsa bu bir bulgudur.
4. **Element kontrolü:** beklenen elementler DOM'da mı, görünür mü,
   doğru sınıf/id ile mi render oluyor (snapshot al).
5. **Layout kontrolü:** responsive kırılımlarda taşma/örtüşme/kırık grid yok mu;
   45-tier cihaz matrisi (`ui-design/reference/10-device-specific-guidelines`).
6. Etkileşim testi: buton/form/toggle tıklanır, sonuç gözlemlenir.

**Çıktı:** pass/fail per element + konsol hataları + ekran görüntüsü/snapshot.

---

## AŞAMA 4 — Image ↔ Kod ve Figma Eşleştirme

1. **Image ↔ sistem kodu:** sayfadaki görsel/icon/src yolları kod içindeki
   karşılığıyla eşleşiyor mu? Katalog: `ui-design/reference/03-icon-asset-catalog.md`.
   Eksik/kırık `src`/`url()` → bulgu.
2. **Figma data kontrolü:**
   - Token'lar: `.ai/scripts/figma-tokens.ps1` **repo kökünden** çalıştırılır;
     sonuç `fail > 0` ise geçersiz.
   - Token SSOT: `FIGMA_TOKEN` / `FIGMA_FILE_KEY` **yalnız `.ai/.env.figma`'dan**
     okunur — anahtar asla `.md`/`.json`/`.log`'a yazılmaz (§13.9/3).
   - Ham veri: `.ai/ui-design/reference/figma/raw/*.json` ile sayfa ölçümü
     (renk/ölçü/spacing) karşılaştırılır.
3. **Kanıt sınırı (uydurma YASAK):**
   - 4/15 Figma sayfası boş (`326:3386`, `16:106`, `1801:12472`, `1801:12473`)
     → bu sayfalardan veri iddiası üretilmez.
   - 15 gizli node (`visible: false`) → PNG indirilemez, eşleşme **kanıtlanamaz**.
   - `tokens-3840.json` / `tokens-tv.json` boş → 4K/TV karşılaştırması YAPILMAZ.
   - Eşleşmeyen/kanıtlanamayan alan → `⚠️ VERIFICATION REQUIRED`.

**Çıktı:** eşleşme tablosu (beklenen → bulununan → pass/fail).

---

## AŞAMA 5 — Düzelt, Teyit, Öğret

1. Bulguları önceliklendir: CRITICAL (güvenlik/çökme) > HIGH (bozuk layout) >
   MEDIUM (görsel hata) > LOW (iyileştirme).
2. §0'daki sorumlu ajana yönlendir; düzeltme yapılır.
3. **Teyit = Aşama 3'ün 2. temiz run'ı.** İkinci test de temizse döngü kapanır;
   kirliyse Aşama 3'e dön (max 3 retry, sonra escalation — AGENTS.md §17/4).
4. **Öğret (kalıcı hale getir):**
   - Bulguyu ve kuralı `.ai/log.md`'ye **append-only** yaz (mevcut satıra dokunma).
   - Tekrarlayan hata sınıfı varsa kuralı kullanıcıya **1 cümleyle öğret**
     ("bundan sonra X yaparken Y kontrol edilecek").
   - Vault `.md` değişikliği gerekiyorsa şablon zorunlu (Guardrail 16) ve
     commit orkestratöre aittir.
5. Rapora ekle: ne doğrulandı, ne test edildi, ne düzeltildi, kanıt nerede.

---

## Yasaklar (özet)

| Yasak | Kaynak |
|---|---|
| Gate script'i `.ai/` içinden çalıştırma | §13.6 |
| Token'ı `.md/.json/.log`'a yazma | §13.9/3 |
| `.ai/.png/**` değiştirme | §13.9/4 |
| Uydurma token/PNG/Figma ölçümü üretme | §24.3 |
| 6 sahte kırık linki "onarma" | §13.7 |
| Gate'siz mockupsiz frontend kodu | §13 |
| Bu skill ile `git commit` atma | §13.9/5 |

## Hata durumları

| Durum | Aksiyon |
|---|---|
| Mockup okunamıyor | DUR + bildir |
| Browser MCP sayfayı açamıyor | Sunucu/port kontrolü → 3 retry → escalation |
| Figma verisi boş/gizli node | `⚠️ VERIFICATION REQUIRED` — eşleşme iddiası YOK |
| 3 düzeltmeden sonra hatalı | Dur, şüpheli varsayımı adıyla söyle |

## MAX THINKING — Anti-Overthink (2026-10-01)
- Reasoning = LOW. Bu skill yüklendiğinde uzun analiz, promptu geri anlatma, plan kompozisyonu YASAK.
- Nokta atışı: gorev -> aksiyon -> sonuc. Ayni dosya/veri 2. kez okunmaz; ilk okumadan sonra KARAR VER.
- Skill yalniz ihtiyac aninda yuklenir; boot'ta toplu skill yukleme YASAK (kural: koku AGENTS.md, on-demand vault).
- 3 basarisiz duzeltme -> DUR, supheli varsayimi soyle, 1 kisa soru sor.
- Bilinmeyen = UNKNOWN. Gereksiz dosya/klasor/skill/agent/context/plan uretme.
- Cikti: ne degisti -> hangi dosya -> sonraki adim. Maks 5 madde.
