---
node_id: K000-isletim-sistemi
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - electroncis
  - isletim-sistemi-api
  - isletim-sistemi-audio
  - isletim-sistemi-core
  - isletim-sistemi-cross
  - isletim-sistemi-kernel
  - isletim-sistemi-observability
  - isletim-sistemi-plan
  - isletim-sistemi-runtime
  - isletim-sistemi-security
  - isletim-sistemi-testing
  - isletim-sistemi-turu
depends_on: []
evidence:
  - "v2 §2.1 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 148-168"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (erişim 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (erişim 2026-10-12)"
validation:
  - "Çocuk listesi v2 §2.1 ile birebir karşılaştırıldı — 12/12 eşleşme (2026-10-12)"
open_questions:
  - "electroncis kapsamı v2'de tanımsız → ⚠️ VERIFICATION REQUIRED"
  - "isletim-sistemi-core ile isletim-sistemi-kernel sınırı belirsiz → ⚠️ VERIFICATION REQUIRED"
  - "isletim-sistemi-plan neyi kastediyor (yol haritası mı, süreç planı mı) → ⚠️ VERIFICATION REQUIRED"
  - "K002 ID GAP bu domainle ilişki kuruyor mu → ⚠️ VERIFICATION REQUIRED"
---

# K000-isletim-sistemi — Kayıt

**ELI15 (v2 §2.1, birebir):** Bilgisayarın/konsolun kendisi. Programların üstünde çalıştığı zemin
(Windows, Linux, macOS, Android…).

> **Alan notu:** children listesi v2 mini-ağacından birebir alınmıştır; depends_on bu turda
> **boş** bırakılmıştır (tasarım iddiası henüz yazılmadı — iddialar
> os-bagimliliklar.md içinde ETİKETLİ olarak taşınır).

## Çocuk kataloğu (kademe-2)

> 1 satırlık özetler **ad kökeninden** türetilmiştir — v2 §2.1 çocukları tek tek tanımlaz.
> Bu yüzden kanıt sütunu ⚠️ yok olanlar içerik bakımından VERIFICATION REQUIRED'dır.

| # | Çocuk | Ne işe yarar (1 satır) | Kanıt |
|---|---|---|---|
| 1 | electroncis | Elektronik ile ilgili OS tarafı alt dalı — v2 kapsam tanımı yok | ⚠️ yok |
| 2 | isletim-sistemi-api | İşletim sisteminin uygulamalara açtığı çağrı/arayüz yüzeyi | ⚠️ yok |
| 3 | isletim-sistemi-audio | İşletim sisteminin ses alt sistemi (Windows Core Audio / WASAPI) | ✔ MS Learn |
| 4 | isletim-sistemi-core | Sistemin merkez servisleri — kernel ile sınırı belirsiz | ⚠️ yok |
| 5 | isletim-sistemi-cross | İşletim sistemleri arası çapraz uyumluluk/taşınabilirlik | ⚠️ yok |
| 6 | isletim-sistemi-kernel | Donanım ile yazılım arasındaki en alt katman (çekirdek) | ⚠️ yok |
| 7 | isletim-sistemi-observability | OS düzeyinde log/metric/trace (gözlemlenebilirlik) | ⚠️ yok |
| 8 | isletim-sistemi-plan | Kapsamı belirsiz alt dal (plan neyin planı?) | ⚠️ yok |
| 9 | isletim-sistemi-runtime | Çalışma zamanı ortamı ve süreç yürütme | ⚠️ yok |
| 10 | isletim-sistemi-security | OS güvenliği: yetki, izolasyon, kripto, oturum | ⚠️ yok |
| 11 | isletim-sistemi-testing | İşletim sistemi katmanı testleri | ⚠️ yok |
| 12 | isletim-sistemi-turu | İşletim sistemi tip/tür sınıflandırması | ⚠️ yok |

## Durum

| Öğe | Değer |
|---|---|
| status | draft |
| Kademe | 2 · domain |
| Alt klasör | 12 (kademe-2) — bounded-context'e bu turda inilmedi |
| Ek dosyalar | os-master.md · os-bagimliliklar.md · os-dogrulama.md |
| Bloklayan açık soru | electroncis · core/kernel sınırı · plan kapsamı (hepsi ⚠️) |

**Doğrulama kapısı:** bu kayıt ancak os-dogrulama.md §2 ölçütlerinin tamamı karşılanınca
status: verified alır.
