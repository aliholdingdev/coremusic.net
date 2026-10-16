---
node_id: "K015-media/synchronization/synchronization-kapsam"
node_type: "module"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "media-master.md §9.10 (rol: cihaz/dış sistemle medya verisi eşitleme — v2 §2.15; yön ve çakışma satırları ⚠️ UNKNOWN)"
  - "media-master.md §12/9 (synchronization yönü çift/tek → ⚠️ UNKNOWN)"
  - "media-master.md §13/8 (eşitleme tutarlılığı test senaryosu sahipliği: K015/synchronization)"
  - "index.md §A kapı 2 (aynı ad iki domain'de → §7/3 — açık)"
validation: "Draft — tüm tasarım satırları TASARIM İDDİASI'dır; §7/3 ad çakışması ve §12/9 yön kararı açık bırakıldı (2026-10-16)"
open_questions:
  - "Yön: çift yönlü mü tek yönlü mü → ⚠️ UNKNOWN (media-master §12/9)"
  - "Çakışma stratejisi (son-yazan-kazanır mı) → ⚠️ UNKNOWN (media-master §9.10)"
  - "Ad çakışması K013-integraion/synchronization → ⚠️ VERIFICATION REQUIRED (§7/3 — çözülmedi)"
group: mimari
---

# K015 · synchronization · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam Sınırı: Ne Eşitleniyor, Ne Hariç
>
> **Genel etiket:** bu dosyadaki her tasarım satırı **TASARIM İDDİASI**'dır — kanıt değildir; kanıt
> `synchronization-web-arastirma.md` §1'de, iskelet kod `synchronization-ornekler.md`'dedir.

---

## §1 KAPSAM İÇİ (modellenen)

**TASARIM İDDİASI —** aşağıdaki 6 kalem bu çocuğun eşitleme yüzeyini oluşturur:

1. **Medya verisi eşitlemesi** — cihaz/dış sistemle medya verisinin (kitaplık, bilgi, ilerleme) aynı tutulması — rol media-master §9.10 (v2 §2.15).
2. **Eşitlenecek alan kümeleri** — (a) katalog/kimlik değişiklikleri, (b) playback ilerleme (positionMs), (c) çalma kuyruğu durumu — alan kümesi ve öncelik **TASARIM İDDİASI**.
3. **Fark tespiti** — yerel ve uzak sürüm karşılaştırması (versiyon/ETag benzeri işaretçi) — işaretçinin biçimi **UNKNOWN** (§6-Q4).
4. **Birleştirme (fusion)** — farklar tek yazımda birleştirilir; çakışma bulunursa ayrı duruma düşer (§3).
5. **Kuyruk ve tekrar deneme** — başarısız eşitleme kuyruğa girer, backoff ile yeniden denenir (§5).
6. **Tutarlılık testi veri yüzeyi** — media-master §13/8 "eşitleme tutarlılığı" senaryosunun ölçeceği veri bu çocuktan gelir; testin kendisi bu kapsamda **değildir** (§2-3).

**Sınır kuralı (TASARIM İDDİASI):** bu dosyanın satırları media-master §9.10'u genişletir; §9.10'daki ⚠️ UNKNOWN satırları bu dosyada **karar sayılmaz**, yalnız açık soru olarak taşınır (§6).

---

## §2 KAPSAM DIŞI (bilinçli hariç — gerekçeli)

1. **Dış sistem/katalog eşitlemesi (dış API konuşması)** — K013-integraion üzerinden yürür; orada da "synchronization" adı vardır → **ad çakışması §7/3, bu dosyada çözülmez** (açık soru: §6-Q3). Gerekçe: dış API sahipliği media-master §5 tablosunda K013'tedir.
2. **Ağ katmanı** (taşıma, TLS, yeniden deneme sayısının ağ politikası) — K014-network. Gerekçe: eşitleme mantığı ağdan bağımsız modellenir.
3. **Test altyapısı ve test çalıştırma** — K019-testing vs K015/media-testing sahipliği **§7/4**; bu kapsam test senaryosu yazmaz, yalnız veri yüzeyini tanımlar (§1-6).
4. **Akış protokolü (içerik biçimi)** — media-master §12/4 ⚠️ UNKNOWN; eşitleme bu protokolü taşımaz.
5. **İndirme ve önbellek eşitlemesi** — downloads / media-cache çocukları (§12/3 TTL UNKNOWN); bu çocuk yalnız "veri aynı mı?" sorusunu taşır.
6. **Sunucu/istemci mimarisi kararı** — nerede eşitlendiği (client-side queue mu, sunucu authoritative mı) **TASARIM İDDİASI gerektirir, kanıt yok** → §6-Q5.

---

## §3 DURUM MAKİNESİ (TASARIM İDDİASI)

> Aşağıdaki tablonun tüm satırları **TASARIM İDDİASI**'dır — media-master §9.10'da durum makinesi **yoktur**;
> 6 durum bu dosyada önerilir.

| Durum | Anlam | Çıkış geçişleri |
|---|---|---|
| `idle` | bekliyor; kuyruk boş | → `detecting` (zamanlayıcı veya kullanıcı tetiği) |
| `detecting` | fark tespiti sürüyor (yerel × uzak işaretçi karşılaştırması) | → `merging` (fark var) · → `idle` (fark yok) · → `error` (ağ/izin hatası) |
| `merging` | farklar birleştiriliyor (fusion) | → `resolved` (yazma başarılı) · → `conflict` (sürüm uyuşmazlığı) · → `error` |
| `conflict` | eşzamanlı yazım tespit edildi | → `resolved` (çözüm uygulandı — strateji §6-Q2 UNKNOWN) · → `error` (çözülemedi) |
| `resolved` | birleştirme yazıldı, doğrulama bekleniyor | → `idle` (doğrulama tamam) · → `error` (doğrulama başarısız) |
| `error` | hata; backoff kuyruğunda | → `idle` (yeniden deneme başarılı veya pes — pes eşiği UNKNOWN §6-Q6) · → `detecting` (retry) |

**Geçiş kuralı (TASARIM İDDİASI):** durumlar tek yönlüdür; `conflict` asla `detecting`'e atlanmaz — önce çözüm, sonra yeniden tespit. `error` dışındaki hiçbir durum ağa çıkmadan birleştirmez (üçlü yazımda son yazan mı kazanır: §6-Q2).

---

## §4 VERİ AKIŞI (TASARIM İDDİASI)

```text
[Kaynak cihaz/sistem]
   │ (1) yerel değişiklik + kuyruğa yaz
   ▼
[detecting] ── (2) işaretçi karşılaştır (yerel × uzak) ── fark yok ──▶ idle
   │ (3) fark var
   ▼
[merging] ── (4) alan bazında birleştir (fusion)
   │
   ├─ sürüm uyuşmazlığı ──▶ [conflict] ── (5) çözüm stratejisi (UNKNOWN) ──▶ resolved
   ▼
(6) yazma + doğrulama (hata ──▶ [error] ── backoff retry)
   ▼
[resolved] ── (7) doğrulama tamam ──▶ idle
```

| Adım | Veri | Sorumluluk |
|---|---|---|
| 1 | yerel değişiklik + meta (alan adı, zaman damgası) | eşitleme istemcisi (bu çocuk) |
| 2 | işaretçi listesi (ETag/sync-token benzeri — biçimi §6-Q4) | tespit katmanı |
| 3 | fark kümesi (eklendi/değişti/silindi) | tespit katmanı |
| 4 | birleştirilmiş kayıt | fusion |
| 5 | çözüm girdisi (çakışma kaydı: iki sürüm + alanlar) | çözüm stratejisi (§6-Q2 UNKNOWN) |
| 6 | yazma yanıtı + doğrulama işareti | yazma katmanı |
| 7 | kapanış kaydı (audit) | gözlem (sınır: log redaction kuralı geçerli) |

---

## §5 HATA DURUMLARI (TASARIM İDDİASI)

| # | Hata | Tetik | Davranış | İşaret |
|---|---|---|---|---|
| E-1 | ağ kesintisi | istek zaman aşımı | kuyruğa al + backoff retry | normal yeniden deneme |
| E-2 | izin/otorizasyon reddi | 401/403 benzeri yanıt | kuyruğu durdur, kullanıcı/onay bekle | fail-safe durdurma |
| E-3 | sürüm uyuşmazlığı | işaretçi eşleşmedi | `conflict` durumuna geç | **çakışma stratejisi → UNKNOWN işaretle** (§6-Q2) |
| E-4 | bilinmeyen alan | birleştirme sırasında tanınmayan alan | alanı **UNKNOWN işaretle**, ezme; sonraki tarihe bırak | `⚠️ VERIFICATION REQUIRED` |
| E-5 | kısmi yazma | yarım güncelleme | atomik yazma zorunlu; başarısızsa tüm kaydı geri al | veri bütünlüğü |
| E-6 | doğrulama başarısız | yazma sonrası okuma uyuşmadı | `error` + tekrar `detecting` | döngü koruması (sınır: sonsuz döngü yok — pes eşiği §6-Q6) |

**Kural (TASARIM İDDİASI):** E-3 ve E-4'te sistem **sessizce veri ezmez** — çakışma ve bilinmeyen alan açıkça işaretlenir; çözüm stratejisi seçilene kadar `conflict` durumu korunur.

---

## §6 AÇIK SORULAR (kapanışı gerekenler)

| # | Açık soru | Etiket | Kapanış koşulu |
|---|---|---|---|
| Q-1 | **Yön:** çift yönlü mü tek yönlü mü? | ⚠️ UNKNOWN (media-master §12/9) | Vault Steward kararı — §3 geçişleri yön kararıyla revize edilir |
| Q-2 | **Çakışma stratejisi:** son-yazan-kazanır mı, alan-bazlı fusion mı, sunucu-önceliği mi? | ⚠️ UNKNOWN (media-master §9.10) | strateji adı + gerekçe yazılana kadar E-3'te sistem kararı vermez |
| Q-3 | **Ad çakışması:** bu çocuk ile K013-integraion/synchronization ne ayırıyor? | ⚠️ VERIFICATION REQUIRED (§7/3) | Vault Steward kararı — **bu dosyada çözülmez** |
| Q-4 | İşaretçi biçimi (ETag / sync-token / zaman damgası) hangisi? | ⚠️ VERIFICATION REQUIRED | kanıt: `synchronization-web-arastirma.md` §2 (RFC 6578/4791 üçlüsü) sonrası seçim |
| Q-5 | Eşitleme mimarisi: sunucu-authoritative mı, istemci kuyruğu mu? | ⚠️ VERIFICATION REQUIRED | mimari karar + ADR |
| Q-6 | Retry/pes eşiği (maksimum deneme, backoff üst sınırı) | ⚠️ UNKNOWN | sayısal eşik onayı |

---

## § REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-16 | İlk yazım — §1-§6 (kapsam içi/dışı, durum makinesi, veri akışı, hata, açık sorular) | Vault Steward (subagent) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
