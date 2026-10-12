---
title: "K001-donanım — Doğrulama Protokolü"
type: architecture-verification
node_id: K001-donanım
status: draft
date: 2026-10-12
updated: 2026-10-12
---

# donanım-dogrulama — K001 Doğrulama Protokolü

## §1 Web-kanıt kayıtları (tur-1)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | TPA3255 Class-D amfi; 18 V–53.5 V besleme aralığı | https://www.ti.com/lit/gpn/TPA3255 | 2026-10-12 | ✅ doğrulandı |
| 2 | TPA3255 için PBTL kipinde KiCad referans projesi (şematik + PCB) | https://github.com/EliMattingly22/TPA3255_ClassD_PBTL | 2026-10-12 | ✅ doğrulandı |
| 3 | Görev iddiası: TPA3255 24–52 V PVDD | görev manifesti | 2026-10-12 | ⚠️ 1 ile uyuşmuyor |
| 4 | Hedef ses donanımı PCM3168A · AK4458 · Class AB | .ai/AGENTS.md §4 | 2026-10-12 | ✅ dosya okundu |
| 5 | PCM5122 yerine PCM3168A/AK4458 öner kuralı | .ai/AGENTS.md §17 #8 | 2026-10-12 | ✅ dosya okundu |
| 6 | SNR > 100 dB · THD+N < 0.01 % @ 1 kHz · worst-case tolerans + ESD | ~/.config/opencode/AGENTS.md §5 | 2026-10-12 | ✅ dosya okundu |
| 7 | ADR-038 · ADR-063 · ADR-064 · ADR-089 dosyaları diskte mevcut | .ai/.decisions/accepted/*.md | 2026-10-12 | ℹ️ varlık — içerik okunmadı |
| 8 | hdd/ssd disk envanteri | — | 2026-10-12 | **UNKNOWN** |
| 9 | Ekran / kablo envanteri | — | 2026-10-12 | **UNKNOWN** |

> **Yasak:** 8 ve 9 için ürün/marka **uydurulmaz**; kanıt gelene kadar UNKNOWN.

## §2 Doğrulama protokolü — ölçütler → verified

| # | Ölçüt | Kanıt şekli | Şu an |
|---|---|---|---|
| D1 | v2 §2.2 mini-ağaç birebirliği (4 çocuk + 3 alt bağlam) | karşılaştırma kaydı | ✅ 2026-10-12 |
| D2 | 7 çocuk klasöründe index.md (8 zorunlu alan) | disk sayımı | ✅ 2026-10-12 |
| D3 | anayasa + bagimliliklar + dogrulama üçlüsü | disk sayımı | ✅ 2026-10-12 |
| D4 | §1'de açık ⚠️ / UNKNOWN satırı kalmamış | kanıt turu | ❌ 3 açık (3, 8, 9) |
| D5 | Açık sorular kapanmış (§3) | üst karar | ❌ 3 açık |
| D6 | [TASARIM] satırlarından en az biri [KANIT] olmuş | dosya/URL | ❌ 0 |
| D7 | donanaım-type yazımı bozulmamış | yazım denetimi | ✅ 2026-10-12 |

**Geçiş kuralı:** D1–D7 tamamı ✅ → index.md status: draft → verified.

## §3 Açık sorular (open_questions)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | K002 ID GAP bu domainle ilişkili mi? | ⚠️ VERIFICATION REQUIRED | registry §6 |
| Q2 | TPA3255 PVDD: 24–52 V (manifest) ↔ 18–53.5 V (TI) — hangisi? | ⚠️ VERIFICATION REQUIRED | audio-hw |
| Q3 | donanım-kernel ↔ donanım-core sınırı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q4 | hdd/ssd/ekran/kablo envanteri (UNKNOWN) | ⚠️ VERIFICATION REQUIRED | Vault Steward |

## §4 Adım adım doğrulama sırası

| Adım | İş | Sorumlu | Bağımlılık |
|---|---|---|---|
| V1 | Disk sayımı: 4 MD + 7 çocuk index | bu ajan | — |
| V2 | v2 §2.2 ile birebir diff | bu ajan | V1 |
| V3 | URL erişilebilirlik tekrarı (§1: 1, 2) | researcher | — |
| V4 | UNKNOWN satırları için kanıt turu-2 | researcher | V3 |
| V5 | Q2–Q4 kararları | Vault Steward / audio-hw | V4 |
| V6 | [TASARIM] → [KANIT] geçişi | bu ajan | V4 |
| V7 | status: verified güncellemesi | Vault Steward | V1–V6 ✅ |

## §5 Kanıt bütünlüğü kuralları

1. URL'siz veya dosya yolsuz iddia yazılmaz → **UNKNOWN**.
2. Dosya varlığı ≠ içerik doğruluğu → ℹ️ işareti ile ayrılır (§1/7).
3. Sayısal iddialar (V, dB, yıldız) erişim tarihiyle yazılır.
4. v2 yazım hataları (donanaım-type) düzeltilmez — v2 §3 KURAL 1.
5. Uyuşmazlık tek taraflı düzeltmez, **iki taraflı kaydedilir** (§1/1 ve §1/3).

## §6 Kanıt turu planı (tur-2 ne zaman açılır)

| Tur | Kapsam | Tetikleyici | Beklenen çıktı |
|---|---|---|---|
| tur-1 | §1 satır 1–7 | bu dosyanın yazımı (2026-10-12) | tamamlandı — 2 URL ✅, 1 ⚠️, 1 ℹ️, 2 UNKNOWN |
| tur-2 | §1 satır 8–9 (envanter) | K001 kapsam envanteri istendiğinde | hdd/ssd/ekran/kablo listesi veya kalıcı UNKNOWN kararı |
| tur-3 | §1 satır 3 (PVDD) | TI datasheet vs manifest kararlaştırıldığında | Q2 kapanış kaydı — iki değer de silinmez |
| tur-4 | D6 ([TASARIM]→[KANIT]) | ilk fiziksel/arayüz kanıtı geldiğinde | bagimliliklar §2/§6 satırlarının etiket güncellemesi |

**Tazelik kuralı:** URL kanıtları (§1/1, §1/2) 90 günden eskiyse erişim tarihiyle
yeniden denenir; yeniden erişilemezse durum ✅ → ⚠️ VERIFICATION REQUIRED olarak
geri alınır. Geri alma sessiz yapılmaz — §6 tablosuna satır eklenir.

**Kapanış kontrolü:** D4 ❌ → tur-2/tur-3 kapandığında ❌ → ✅ olur; D5 yalnız
üst kararla kapanır; D6 için en az bir [KANIT] satırı zorunludur.
status: verified yazımı bu dosyadan değil, index.md'den yapılır ve yalnız
D1–D7 tamamı ✅ olduğunda geçerlidir.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12