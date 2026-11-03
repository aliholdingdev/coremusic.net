---
title: "home.coremusic.net — TODO Listesi"
type: todo
category: project-tracking
version: 1.3.0
status: active
authority: SSOT
updated: 2026-10-30
---

# home.coremusic.net — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §7 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** Ana panelin kalan işleri: oynatma akışı sözleşmesi, router sözleşmesi, GUI spec kapısı.
**eli15:** Maddeler `PRD.md §7`'den türetildi. Faz 3 GUI paneli ise media'daki spec'in genişlemesidir — kapıya ekleme medya kararına bağlıdır.

## Açık Maddeler

- [ ] **#20 (coreplayer) Offline-First çapraz kontrolü** — oynatma akışı (PRD-PLT-001) doğrulanır · Kaynak: `[[PRD.md]]` §7.1
- [ ] **#10/#9 (Middleware/PageRouter) router/kernel sözleşmesi** — doğrulama · Kaynak: `[[PRD.md]]` §7.2
- [ ] **Faz 3 GUI spec kapısı** — `media.coremusic.net/docs/faz3-gui-spec.md` bu panelin genişlemesi ise kapıya eklenir · Kaynak: `[[PRD.md]]` §7.3
- [ ] **ORTA: `MusicStreamHandler` ↔ ADR-112 stream mimarisi hizası** — `include/Stream/MusicStreamHandler.php` (byte-range + auth guard + path traversal koruması) yerel/offline akış sözleşmesi (opak ticket · üye oturumu zorunlu · API asla `file_path` vermez) ile karşılaştırılır; sapma raporlanır, pipeline sırası değiştirilmez · Kaynak: ADR-112 (Faz 6) + `include/Stream/MusicStreamHandler.php`

## TAM Madde Listesi — Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Bu bölüm tek doğruluk listesidir; `## Geçmiş & Mikro Ölçek` kanıt satırları bu listenin detayıdır. Sıra: **MICRO** (tek eylem) → **ORTA** (modül) → **EPIC** (faz).

### A. GEÇMİŞ — TAMAMLANAN
- [x] MICRO: DOM container sessiz-init dersi + PHP/JS ham oran eşik kuralı (SESSIONS 2026-10-19)
- [x] ORTA: CSRF token (Backend Adım 08) — `1e806d1`
- [x] ORTA: history from-to + gün gruplu + hibrit HistoryComponent — `1f60449` `27c568b`
- [x] ORTA: header CSP + idempotent replaceChildren + 6 spec — `afe8634`
- [x] ORTA: dropdown rol-bazlı sunucu filtresi — `f3a6cc9`
- [x] ORTA: footer volume ikon + mute toggle — `59ea405` `339104d`
- [x] ORTA: home song button Figma kapak overlay — `b0921d8`
- [x] ORTA: sidebar/history scrollbar onarımı (Figma ölçümü) — `2ce12f1`
- [x] EPIC: Backend Rebuild Adım 01-11 kod tarafı — 2026-10-22 (canlı KAPI-E ⏳ ayrı)

### B. AÇIK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: H-1a — `coreplayer` offline akışını yalnız oku → 3 satır bulgu notu
- [ ] MICRO: H-4a — volume slider `change` + history gruplama → 2 test dosyası
- [ ] MICRO: H-5a — `res-blue`/`res-default` 404 isteklerini log'da tara
- [ ] MICRO: ölü `.volume-set-slider` seçici temizliği (düzeltme planı)
- [ ] MICRO: `home-1024` 12 PNG ↔ header 60px / içerik 450px / footer 90px ölçüm kontrolü
- [ ] ORTA: H-1 #20 coreplayer Offline-First çapraz kontrol (PRD-PLT-001 · 30sn)
- [ ] ORTA: H-2 #10/#9 router/kernel sözleşmesi (ADR-021/083)
- [ ] ORTA: `health.php` sağlık kontrolü davranışı korunur denetimi (AGENTS §3)
- [ ] EPIC: H-3 Faz 3 GUI spec kapısı (media `faz3-gui-spec.md` kararı)
- [ ] EPIC: Backend 09-11 KAPI-E browser canlı doğrulaması (server PC)

### C. GELECEK — SIRADAKİ BÜYÜK İŞLER (öncelik sırası)
| Sıra | EPIC iş | İlk MICRO adım |
|---|---|---|
| 1 | H-1 çapraz kontrol kapanışı → PRD §7'ye yaz | coreplayer okuma notu |
| 2 | H-4 birim test yeşilliği | 2 test dosyası |
| 3 | Offline-First canlı 30sn + reconnect (PLT-001) | server PC curl |
| 4 | Faz 3 GUI kapısı → home panel genişlemesi | media kararını bekle |## Adım Adım Yürütme (2026-10-25 güçlendirme)

**Kural:** Her adım tek oturum · `[ ]` → çalış · `[x]` + kanıt (commit/dosya/ölçüm) → kapanır · **YAPMA** sütunu bağlayıcıdır (ihlal → revert) · komut satırını gönder → agent o adımı yapar.

| # | Adım | Ne yapılacak (kaynak) | YAPMA | Hazır komut |
|---|---|---|---|---|
| H-1 | [ ] | #20 coreplayer Offline-First çapaz kontrol — oynatma akışı PRD-PLT-001 ile doğrulanır, bulgu PRD §7'ye yazılır | oynatma kodunu doğrulamadan değiştirme | `H-1: coreplayer offline-first çapraz kontrolü yap — PRD-PLT-001 (30sn kesintisiz) ile test et, bulguyu home/PRD §7'ye yaz, kod YAZMA` |
| H-2 | [ ] | #10/#9 router/kernel sözleşmesi doğrulama (PageRouter ADR-021/083) | middleware/router sırasını değiştirme | `H-2: home router/kernel sözleşmesini ADR-021/083 ile çapraz doğrula` |
| H-3 | [ ] | Faz 3 GUI spec kapısı — `media/docs/faz3-gui-spec.md` kararı beklenir | kapı açılmadan GUI kodu/UI ekleme | `H-3: Faz3 GUI kapısını değerlendir — spec bu panelin genişlemesi mi, kararı PRD §7.3'e yaz` |
| H-4 | [ ] | Footer volume + history birim testi (log 2026-10-19: test YOK) | mevcut component testlerini bozma | `H-4: footer volume slider + history gruplama için birim test yaz (mevcut suite'e ekle)` |
| H-5 | [ ] | `res-blue`/`res-default` 404 +ölü `.volume-set-slider` seçici temizliği (log 2026-10-19 kapsam dışı) | mockup PNG'siz CSS/renk değişikliği | `H-5: res-blue/res-default 404 kaynaklarını bul, ölü seçicileri temizleme planını yaz` |

**Yüzey yasakları:** mockup PNG'siz UI kodu (Guardrail #11) · framework (ADR-001) · `config/.env` içeriği yazımı · PHP sayfasına gömülü DOM JS'i · commit (MO).
## Geçmiş & Mikro Ölçek — Tam Liste (2026-10-25)

**Geçmiş — tamamlanan (kanıtlı):**
- [x] `[EPİK]` Backend Adım 08-11 arayüz/tarih/işlev işleri — `1e806d1` `1f60449` `27c568b` `afe8634` `f3a6cc9`
- [x] `[ORTA]` Footer volume ikon + mute toggle — `59ea405` `339104d` (2026-10-19)
- [x] `[ORTA]` Home song button Figma kapak overlay — `b0921d8` (2026-10-16)
- [x] `[ORTA]` Sidebar/history scrollbar onarımı (Figma ölçümü) — `2ce12f1`
- [x] `[MİKRO]` DOM container sessiz-init dersi + PHP/JS eşik ham oran kuralı — SESSIONS 2026-10-19

**Mikro işler (en küçük → büyük):**
- [ ] `[MİKRO]` H-4 altı: volume slider `change` + history gruplama için 2 test dosyası aç
- [ ] `[MİKRO]` H-5 altı: `res-blue`/`res-default` 404 isteklerini log'da bul (tek tarama)
- [ ] `[MİKRO]` H-1 altı: `coreplayer` offline akışını yalnız oku → 3 satır bulgu notu
- [ ] `[ORTA]` H-1/H-2 çapraz kontrolleri PRD §7'ye yaz → sonra `[EPİK]` Faz 3 GUI kararı (H-3)## Referanslar

- `[[PRD.md]]` (§7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
