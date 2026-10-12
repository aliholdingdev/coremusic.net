---
title: "K000-isletim-sistemi — Doğrulama Protokolü"
type: architecture-verification
node_id: K000-isletim-sistemi
status: draft
date: 2026-10-12
updated: 2026-10-12
---

# os-dogrulama — K000 Doğrulama Protokolü

## §1 Web-kanıt kayıtları (tur-1)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | WASAPI, uygulama ile ses uç noktası arasındaki veri akışını yönetir; arayüzler Audioclient.h / Audiopolicy.h | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ doğrulandı |
| 2 | Exclusive-mode akış ses uç noktasını ele geçirir; shared mode'a fallback seçeneği kullanıcıya gösterilmeli | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ doğrulandı |
| 3 | Hedef Windows yığını: WASAPI · COM · WinRT · WDK | .ai/AGENTS.md §4 | 2026-10-12 | ✅ dosya okundu |
| 4 | Per-OS Neva player ADR'si diskte mevcut | .ai/.decisions/accepted/ADR-019-per-os-neva-player.md | 2026-10-12 | ℹ️ varlık — içerik okunmadı |
| 5 | Linux / macOS / Android ses alt sistemleri | — | 2026-10-12 | **UNKNOWN** (kanıt toplanmadı) |
| 6 | İşletim sistemi sürüm/sürüm hedefleri | — | 2026-10-12 | **UNKNOWN** |

> **Yasak hatırlatma:** yukarıdaki 5 ve 6. satırlar için isim/ürün **uydurulmaz**;
> kanıt gelene kadar UNKNOWN kalır.

## §2 Doğrulama protokolü — ölçütler → verified

| # | Ölçüt | Kanıt şekli | Şu an |
|---|---|---|---|
| D1 | v2 §2.1 mini-ağacı ile çocuk listesi birebir (12/12) | karşılaştırma kaydı | ✅ 2026-10-12 |
| D2 | 12 çocuk klasöründe index.md var ve 8 zorunlu alan taşıyor | disk sayımı | ✅ 2026-10-12 |
| D3 | Master + bagimliliklar + dogrulama üçlüsü mevcut | disk sayımı | ✅ 2026-10-12 |
| D4 | §1 tablosunda UNKNOWN/⚠️ satırı kalmamış | kanıt turu | ❌ 2 açık (5, 6) |
| D5 | 4 açık soru kapanmış (§3) | üst karar / kanıt | ❌ 4 açık |
| D6 | depends_on/used_by iddialarının en az biri [KANIT] etiketli | dosya yolu / URL | ❌ 0 kanıt |
| D7 | Korunması gereken yazım ihlali yok | yazım denetimi | ✅ bu domainde ihlal yok |

**Geçiş kuralı:** D1–D7'nin **tamamı** ✅ olunca index.md içindeki status: draft → verified.

## §3 Açık sorular (open_questions)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | electroncis neyi temsil ediyor? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q2 | isletim-sistemi-core ↔ isletim-sistemi-kernel sınırı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q3 | isletim-sistemi-plan kapsamı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q4 | Hedef OS platform seti ve sürümleri (UNKNOWN) | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q5 | K002 ID GAP bu domainle ilişkili mi? | ⚠️ VERIFICATION REQUIRED | registry §6 |

## §4 Kanıt toplama planı (tur-2)

1. Linux/macOS/Android ses alt sistemleri için resmi doküman taraması → §1 5 ve 6 satırları.
2. .ai/AGENTS.md §24.3 içindeki Embedded satırı ile K000 sınırının teyidi
   (NeVaEngine dizini 0 dosya uyarısı).
3. Açık sorular Q1–Q3 için üst karar → kapsam netleşince çocuk index'leri güncellenir.

## §5 Adım adım doğrulama sırası (protokol)

| Adım | İş | Sorumlu | Bağımlılık |
|---|---|---|---|
| V1 | Disk sayımı: 4 domain MD + 12 çocuk index.md | bu ajan | — |
| V2 | v2 §2.1 mini-ağaçı ile birebir diff (12 çocuk) | bu ajan | V1 |
| V3 | §1'deki URL'lerin erişilebilirliğinin tekrar kontrolü | researcher | — |
| V4 | UNKNOWN satırları için kanıt turu-2 (§4) | researcher | V3 |
| V5 | Q1–Q3 kapsam kararlarının alınması | Vault Steward | V4 |
| V6 | os-bagimliliklar.md [TASARIM] → [KANIT] geçişi | bu ajan | V4 |
| V7 | status: draft → verified güncellemesi | Vault Steward | V1–V6 tamamı ✅ |

## §6 Kanıt bütünlüğü kuralları (bu dosya için bağlayıcı)

1. URL'siz veya dosya yolsuz iddia yazılmaz → **UNKNOWN**.
2. Dosya varlığı ≠ içerik doğruluğu → ℹ️ işareti ile ayrılır (bkz. §1/4).
3. Yıldız/sürüm gibi sayısal iddialar **erişim tarihiyle** birlikte yazılır.
4. v2 yazım hataları (electroncis · donanaım · identitiy) **düzeltilmez** — v2 §3 KURAL 1.
5. Bu dosyadaki her ✅ işareti bir URL veya gerçek dosya yolu ile eşleşmek zorundadır.
6. Tahmin edilen kapsam satırı değil, yalnız soru satırı yazılır (Q listesi).


---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
