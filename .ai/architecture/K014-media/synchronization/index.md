---
node_id: K014-media/synchronization
node_type: subdomain
status: draft
parent_id: K014-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Aynı ad iki domain'de: ne ayırıyor → ⚠️ VERIFICATION REQUIRED (§7/3)"
group: mimari
---


# synchronization — K014-media (kademe-2 · subdomain)

**ELI15:** Medya verisinin (kitaplık/bilgi/ilerleme) eşitlenmesi.

**Sınır (ne DEĞİL):** Dış sistem eşitlemesi K013/synchronization (aynı ad) · ağ K014.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Aynı ad iki domain'de: ne ayırıyor → ⚠️ VERIFICATION REQUIRED (§7/3) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

## §ROL (ELI15)

Medya verisi eşitlemesi, kitaplık/bilgi/ilerleme gibi medya verisinin cihazlar ve dış sistemle aynı tutulmasıdır (master §9.10 — v2 §2.15 S2). İki cihaz aynı veriyi değiştirdiğinde çakışma çıkar; çakışma çözümünün ne olduğu (ör. son-yazan-kazanır) henüz ⚠️ UNKNOWN'dur (master §9.10). Eşitlemenin Yönü — tek yönlü mü çift yönlü mü — de ⚠️ UNKNOWN'dur ve master §22.6'da "⏳ AÇIK" kalmaya devam eder; Vault Steward kararı beklenir (master §12/9) — bu dosya yön SEÇMEZ. Dikkat: "synchronization" adı K013-integraion domain'inde de geçer; ne ayırdığı ⚠️ VERIFICATION REQUIRED'dır (master §7/3). Eşitleme gecikmesi (cihazlar arası fark) bir ölçüm verisidir; eşiği K017-observability belirler (master §15/5 — eşik ⚠️ UNKNOWN).

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Kaynak |
|---|---|---|
| 1 | Medya verisi eşitleme: cihaz/dış sistemle kitaplık/bilgi/ilerleme aynı tutma | master §9.10 · v2 §2.15 (S2) |
| 2 | Vektör saat (vector clock): değişiklik sırası + çakışma (CONCURRENT) tespiti | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) |
| 3 | Çakışma çözümü: son-yazan-kazanır (LWW) dahil seçenekler | TASARIM ÖNERİSİ · strateji ⚠️ UNKNOWN (master §9.10) |
| 4 | Senkron kuyruğu: pending/failed durumları + yeniden deneme | TASARIM ÖNERİSİ (`ornekler.md`) |
| 5 | Delta alışverişi: yalnız bilinen saatin altındaki değişikliklerin gönderimi/alımı | TASARIM ÖNERİSİ (`ornekler.md`) |
| 6 | Eşitleme tutarlılığı test senaryosu (cihazlar arası eşitlik) | master §13/8 |
| 7 | Eşitleme gecikmesi ölçüm verisi (cihazlar arası fark) | master §15/5 (eşik ⚠️ UNKNOWN) |
| 8 | Yön seçimi (tek/çift) — karar kapısı (bu dosya seçmez) | ⚠️ UNKNOWN → Vault Steward (master §12/9 · §22.6 AÇIK) |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Kaynak |
|---|---|---|---|
| 1 | Dış sistemle eşitleme (aynı ad — ne ayırıyor?) | K013-integraion | master §7/3 · §9.10 |
| 2 | Ağ taşımasının ayrıntısı (senkron paketini taşır) | K010-network | master §2.2 |
| 3 | Kalıcı veri şeması/ORM (eşitleme kayıtları, saat kalıcılığı) | K007-data | master §2.2 |
| 4 | Çevrimdışı kullanılabilirlik (ne hazır / ne eşitlenmeli) | K014-media/offline | master §9.8 · §7/2 |
| 5 | İndirme (dosyanın yerel kopyasının alınması) | K014-media/downloads | master §9.7 |
| 6 | Uygulama akışı/arayüz (eşitleme butonu/durumu) | K011-application | master §2.2 |
| 7 | Kuyruk/servis yürütme (arka plan senkron işi) | K012-services | master §2.2 |
| 8 | Yön (tek/çift) + çakışma stratejisi kararı | Vault Steward (§12/9 · §16/9) | master §22.6 → ⚠️ AÇIK |

## §BİLEŞENLER

| # | Bileşen | Sorumluluk | Etiket |
|---|---|---|---|
| 1 | Vektör saat motoru | cihaz sayaçları: increment · merge (per-key max) · karşılaştırma (BEFORE/AFTER/EQUAL/CONCURRENT) | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) |
| 2 | Çakışma çözümleyici | son-yazan-kazanır (LWW) / vektör saat karşılaştırmalı birleştirme seçenekleri | TASARIM ÖNERİSİ · strateji ⚠️ UNKNOWN (master §9.10) |
| 3 | Senkron kuyruğu | değişiklikleri pending/failed durumlarıyla tutar; failed kaydı silinmez, yeniden denenir | TASARIM ÖNERİSİ (`ornekler.md`) |
| 4 | Delta alışverişi | karşı tarafa yalnız bilinen saatin altındaki değişiklikleri gönderir/alır (tam senkron değil) | TASARIM ÖNERİSİ (`ornekler.md`) |
| 5 | Yön kapısı | tek yönlü/çift yönlü mod okuması — seçimi YAPMAZ | ⚠️ UNKNOWN (master §12/9 → Vault Steward §22.6) |
| 6 | Taşıma kapısı | senkron paketini K010-network üzerinden taşır | TASARIM İDDİASI (master §2.2) |
| 7 | Kalıcılık seam'i | eşitleme kaydı + vektör saat → şema K007'de | TASARIM İDDİASI (master §2.2) |
| 8 | Tutarlılık ölçüm veri | cihazlar arası fark → K017-observability | TASARIM İDDİASI (ölçüm master §15/5 — eşik ⚠️ UNKNOWN) |

## §STANDARTLAR

| # | Standart/fark | Ne | Kaynak | Etiket |
|---|---|---|---|---|
| 1 | Eşitleme yönü (tek/çift) | karar KAPANMADI — Vault Steward beklenir | master §12/9 · §22.6 (⏳ AÇIK) | ⚠️ UNKNOWN |
| 2 | Çakışma stratejisi (son-yazan-kazanır mı?) | karar KAPANMADI | master §9.10 · §12/9 | ⚠️ UNKNOWN |
| 3 | "synchronization" adı iki domain'de | K013-integraion ile sınır ne? | master §7/3 | ⚠️ VERIFICATION REQUIRED |
| 4 | Eşitleme tutarlılığı testi (cihazlar arası eşitlik) | test senaryosu #8 | master §13/8 | ✅ doğrulanmış senaryo |
| 5 | Eşitleme gecikmesi ölçümü | cihazlar arası fark — eşik ⚠️ UNKNOWN | master §15/5 | ✅ doğrulanmış ölçüm kalemi |

> **Not:** master §22 web araştırması sync yönünü KAPSAM DIŞI bırakmıştır (§22 başlığı · §22.6 "⏳ AÇIK") — bu çocuk için doğrulanmış **dış standart (RFC/ISO) YOKTUR**; satırlar yalnız vault içi kanıtları taşır. Vektör saat/LWW/delta mekaniği TASARIM ÖNERİSİ illustrative örneklerdir (`ornekler.md` §2-§16), doğrulanmış standart olarak sunulmaz.

### §STANDARTLAR — TASARIM ÖNERİLERİ (Vault Steward onayı ister)

| # | Öneri | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | Değişiklik sırası vektör saat ile izlenir; CONCURRENT çakışma son-yazan-kazanır (LWW) ile çözülür | sıralama/çakışma tespiti için dilsel bağımsız mekanik | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) — strateji kararı ⚠️ UNKNOWN (master §9.10) |
| 2 | Yön kararı (tek/çift) belirlenmeden kalıcı senkron kuyruğu açılmaz | §12/9 açık kalmaya devam ediyor | master §12/9 · §22.6 → Vault Steward kararı |
| 3 | Senkron kuyruğu pending/failed durumları + yeniden deneme taşır | hata dayanıklılığı; failed kaydı silinmez | TASARIM ÖNERİSİ (`ornekler.md`) |
| 4 | Delta alışverişi (tam senkron değil, yalnız değişiklik listesi) | bant genişliği ve gerilim tasarrufu | TASARIM ÖNERİSİ (`ornekler.md`) |
| 5 | Tutarlılık ölçümü K017-observability'a akar; eşik konmaz | eşik Vault Steward belirler | master §15/5 (eşik ⚠️ UNKNOWN) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
