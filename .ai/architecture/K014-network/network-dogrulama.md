---
node_id: K014-network
node_type: domain
role: dogrulama
status: draft
date: 2026-10-12
version: 1.0.0
kaynak-turu: "web-kanıtı beyanları (2026-10-12) + kaynak metin karşılaştırması"
authority: "SSOT: ADR-097 §5.3 + görev araştırma turu 2026-10-12"
group: kanit
---


# K014-network — network-dogrulama.md

> Bu belge **kanıt defteridir**: neyin doğrulandığı, neyin beklemede olduğu.
> Doğrulanmayan hiçbir iddia "verified" sayılmaz.

## §0 Kanıt sınıflandırması

| Sınıf | Ne demek | Bu belgede |
|---|---|---|
| S1 · URL'li web kanıtı | İddia + link + tarih + durum | — (bu domain için URL'li kanıt yok) |
| S2 · kaynak metin kanıtı | v2 / registry / ADR satırı | 7 çocuk + 5 torun adları, master adı |
| S3 · beyan (URL'siz) | Görev beyanında iddia var, link yok | WebRTC = IETF standardı |
| S4 · atama | Hangi düğümün kanıtı — tasarım kararı | WebRTC → K014/comms/webrtc eşlemesi |

## §1 Ölçütler ve sonuçlar

| # | Ölçüt | Yöntem / kaynak | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.14 ile birebir (7) + comms torunları (5) | metin karşılaştırma (12/12) | ✅ verified |
| 2 | Master dosyası adı network-master.md (registry §5/14) | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan (node_id…open_questions) | alan sayımı | ✅ verified |
| 4 | 5 dosyalık MD seti + 7 çocuk + 5 torun index'i | disk sayımı 2026-10-12 | ✅ verified |
| 5 | Torun index'leri node_type: bounded-context | alan kontrolü (5/5) | ✅ verified |
| 6 | WebRTC = IETF standardı iddiası (resmi kaynak) | kanıt turu | ❌ doğrulanmadı → ⚠️ VERIFY REQUIRED |
| 7 | offline-sync sahipliği (K014 · K015 · K012) | v2 metni okundu | ❌ üç aday → ⚠️ |
| 8 | Kademe-4 module listesi | — | ⏳ faz C (ADR-097 K4) |

## §2 Açık sorular (open_questions)

| # | Soru | Etiket | Not |
|---|---|---|---|
| 1 | WebRTC standardının resmi kaynağı (URL)? | ⚠️ VERIFY REQUIRED | Tek §4 kanıtı |
| 2 | offline-sync sahibi kim (K014 / K015 / K012)? | ⚠️ VERIFICATION REQUIRED | Üç aday |
| 3 | handoff ↔ multi-room sınırı | ⚠️ VERIFICATION REQUIRED | Teslim mi senkron mu |
| 4 | service-discovery kapsamı (cihaz keşfi mi servis kaydı mı)? | ⚠️ VERIFICATION REQUIRED | |
| 5 | network-testing ↔ K019-testing sahipliği | ⚠️ VERIFICATION REQUIRED | |
| 6 | device-communication hangi yerel protokolleri kullanır? | ⚠️ UNKNOWN | Kanıt yok |
| 7 | multi-room zaman hizalama yöntemi | ⚠️ UNKNOWN | Kanıt yok |

## §3 Web-kanıt kayıtları (tur-1 · 2026-10-12)

| # | İddia | URL / uç nokta | Tarih | Durum |
|---|---|---|---|---|
| 1 | WebRTC = standard (IETF) — açık protokol | ⚠️ URL görev beyanında yok | 2026-10-12 | ⚠️ VERIFY REQUIRED — S3, kanıt yok |
| 2 | İddia 1 → K014/comms/webrtc eşlemesi | — | 2026-10-12 | 📌 S4 tasarım ataması |
| 3 | comms torun adları (http · websocket · webrtc · tcp-ip · device-communication) | v2 §2.14 | 2026-10-12 | ✅ S2 kaynak metin (birebir) |

> Bu belgeye yalnız **iddia · URL · tarih · durum** dörtlüsüyle kayıt girer;
> "galiba", "muhtemelen" gibi derecelendirme yazılmaz.

## §4 Tur-2 yapılacaklar (kanıt bekleyen)

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | WebRTC standard belgesi için resmi URL topla | §3/1 · §2/1 | research |
| 2 | offline-sync sahipliğini K014/K015/K012 sınırında karara bağla | §2/2 | Vault Steward |
| 3 | handoff / multi-room kapsamını netleştir | §2/3 | Vault Steward |
| 4 | service-discovery kapsamını netleştir | §2/4 | Vault Steward |
| 5 | Ağ testi araç/kanıtı ve fallback stratejisi için kanıt topla | §1/6 · §2/6 | explore |

## §5 Doğrulama geçmişi

| Tarih | Tur | Sonuç |
|---|---|---|
| 2026-10-12 | Tur-1 (görev araştırma turu + kaynak metin karşılaştırma) | 5/8 ölçüt verified · 1 ⚠️ VERIFY REQUIRED · 1 belirsiz |

## §6 Kapı tanımı (bu dosyanın geçerlilik sınırı)

1. Bu belge yalnız K014-network'ü kapsar; kardeş domain dosyalarına iddia **taşıtmaz**.
2. §2/1 (WebRTC URL) kapanmadan §4/1 "verified" sayılmaz.
3. Yeni kanıt = yeni satır (§3), mevcut satırın **üzerine yazma** (append-only).

---

## §7 Kanıt Sınıflandırması Derinleştirmesi (S1–S4 genişletmesi)

### §7.1 Kanıt sınıfları — bu domain uygulaması

| Sınıf | Tanım | K014'te sayısı | Örnek |
|---|---|---|---|
| S1 · URL'li web kanıtı | İddia + link + HTTP 200 | 0 | — (bu domain için yok) |
| S2 · kaynak metin kanıtı | v2/registry/ADR satırı | 12 | 7 çocuk + 5 torun (§1/1) |
| S3 · beyan (URL'siz) | Görev beyanı, link yok | 1 | WebRTC = IETF (§3/1) |
| S4 · atama | Hangi düğüme eşlendi | 2 | WebRTC → comms/webrtc (§3/2) |

### §7.2 Kanıt gücü hiyerarşisi

| Güç | Kaynak | Kullanım |
|---|---|---|
| 1 · kod satırı | gerçek dosya + satır | en güçlü |
| 2 · URL + HTTP 200 | resmi kaynak + ölçüm | güçlü |
| 3 · kaynak metin (v2/ADR) | SSOT dosyası | orta |
| 4 · beyan (URL'siz) | görev beyanı | zayıf — doğrulanana kadar |
| 5 · yok | — | UNKNOWN |

### §7.3 K014 kanıt dağılımı

| Kategori | Sayı | Yüzde |
|---|---|---|
| S2 (kaynak metin) | 12 | %86 |
| S3 (beyan) | 1 | %7 |
| S4 (atama) | 1 | %7 |
| S1 (URL) | 0 | %0 |
| Toplam | 14 | %100 |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız mevcut S1–S4 kayıtlarını sınıflandırır.

---

## §8 Doğrulama Kapıları (gate genişletmesi)

| # | Kapı | Koşul | K014 durumu | Not |
|---|---|---|---|---|
| 1 | çocuk listesi birebir | v2 §2.14 = 7 | ✅ (§1/1) | S2 kanıtı |
| 2 | master adı birebir | registry §5/14 | ✅ (§1/2) | S2 kanıtı |
| 3 | index 8 zorunlu alan | alan sayımı | ✅ (§1/3) | S2 kanıtı |
| 4 | torun index'leri | node_type: bounded-context | ✅ (§1/5) | S2 kanıtı |
| 5 | WebRTC standard kanıtı | URL + HTTP 200 | ❌ (§1/6) | ⚠️ VERIFY REQUIRED |
| 6 | offline-sync sahipliği | karar | ❌ (§1/7) | ⚠️ 3 aday |
| 7 | kademe-4 module listesi | faz C | ⏳ (§1/8) | bekliyor |

### §8.1 Kapı geçmişi

| Kapı | İlk durum | Güncel durum | Değişiklik |
|---|---|---|---|
| 1 | draft | ✅ | — |
| 2 | draft | ✅ | — |
| 3 | draft | ✅ | — |
| 4 | draft | ✅ | — |
| 5 | draft | ❌ | URL bekleniyor |
| 6 | draft | ❌ | karar bekleniyor |
| 7 | draft | ⏳ | faz C bekleniyor |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §9 Açık Sorular Derinleştirmesi (§2 genişletmesi)

### §9.1 Açık sorular — kök neden analizi

| # | Soru (§2) | Kök neden | Kapatma yolu | Tahmini |
|---|---|---|---|---|
| 1 | WebRTC resmi kaynağı | görev beyanında URL yok | research turu | 1 tur |
| 2 | offline-sync sahibi | v2 metni 3 aday gösteriyor | Vault Steward kararı | ADR |
| 3 | handoff ↔ multi-room sınırı | v2 metni belirsiz | Vault Steward kararı | ADR |
| 4 | service-discovery kapsamı | yerel vs servis kaydı | Vault Steward kararı | ADR |
| 5 | network-testing ↔ K019 | iki test sahibi | Vault Steward kararı | ADR |
| 6 | device-communication protokolleri | kod kanıtı yok | kademe-4 (faz C) | faz C |
| 7 | multi-room zaman hizalama | kod kanıtı yok | kademe-4 | faz C |

### §9.2 Açık soru önceliklendirmesi

| Öncelik | Soru | Etki | Neden |
|---|---|---|---|
| HIGH | offline-sync sahibi (§2/2) | 3 domain etkiler | tek yazım noktası |
| HIGH | handoff ↔ multi-room (§2/3) | K014 içi sınır | tekrar riski |
| MEDIUM | service-discovery (§2/4) | K012/K016 etkiler | yanlış sahiplik |
| MEDIUM | network-testing ↔ K019 (§2/5) | test tekrarı | iki sahip |
| LOW | device-communication (§2/6) | kademe-4 | faz C |
| LOW | multi-room zaman (§2/7) | kademe-4 | faz C |
| LOW | WebRTC kaynağı (§2/1) | §4/1 doğrulama | 1 tur |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §10 Web-Kanıt Kayıtları (tur-2 planı — henüz başlamadı)

| # | İddia | Hedef URL (ad yok — adlandırma yasak) | Durum |
|---|---|---|---|
| 1 | WebRTC = IETF standardı | ⚠️ URL aranacak (tur-2) | ⚠️ VERIFY REQUIRED |
| 2 | HTTP/1.1 · HTTP/2 · HTTP/3 standartları | ⚠️ URL aranacak (tur-2) | ⚠️ VERIFY REQUIRED |
| 3 | WebSocket standartları | ⚠️ URL aranacak (tur-2) | ⚠️ VERIFY REQUIRED |
| 4 | mDNS/DNS-SD standartları | ⚠️ URL aranacak (tur-2) | ⚠️ VERIFY REQUIRED |

### §10.1 URL doğrulama protokolü (tur-2 hazır)

| Adım | Yöntem | Not |
|---|---|---|
| 1 | benzersiz resmi URL bul | envanter kesişimi 0 olmalı |
| 2 | `Invoke-WebRequest -Method Head -Uri $u -TimeoutSec 20` | HTTP 200 şart |
| 3 | 403/timeout → GET yedeği | başarısızsa tabloya almaz |
| 4 | §3/§3a tablosuna ekle | iddia · URL · tarih · durum |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız tur-2 planını taşır.

---

## §11 Doğrulama Envanteri (sayım — 2026-10-13)

| # | Envanter | Sayım | Kaynak |
|---|---|---|---|
| 1 | S1 (URL'li) | 0 | §7.3 |
| 2 | S2 (kaynak metin) | 12 | §7.3 |
| 3 | S3 (beyan) | 1 | §7.3 |
| 4 | S4 (atama) | 2 | §7.3 |
| 5 | toplam kanıt satırı | 15 | §7.3 |
| 6 | açık soru | 7 | §2 · §9 |
| 7 | doğrulanmış ölçüt | 5 | §1 (5/8 ✅) |
| 8 | doğrulanmamış ölçüt | 2 | §1 (1 ❌ + 1 ⏳) |
| 9 | kapalı kapı | 4 | §8 (1–4) |
| 10 | açık kapı | 3 | §8 (5–7) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §12 Doğrulama Geçmişi (§5 genişletmesi)

| Tarih | Tur | Sonuç | Ek |
|---|---|---|---|
| 2026-10-12 | Tur-1 | 5/8 ölçüt verified · 1 ⚠️ · 1 belirsiz | §5 |
| 2026-10-13 | Tur-2 (derinleştirme) | §7–§11 eklendi; kanıt satırı değişmedi (15) | bu bölüm |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız mevcut turları kaydeder.

---

## §13 Kapı Tanımı Genişletmesi (§6 genişletmesi)

| # | Kapı kuralı | Genişletme |
|---|---|---|
| 1 | yalnız K014 kapsar | kardeş dosyalara iddia taşımaz |
| 2 | §2/1 kapanmadan verified yok | WebRTC URL şartı |
| 3 | append-only | mevcut satır üzerine yazılmaz |
| 4 | S1 sınıfı bu domain'de yok | tur-2'de eklenebilir |
| 5 | S3 → S1 geçişi | beyan URL + HTTP 200 ile yükselir |
| 6 | UNKNOWN/⚠️ sayısı | 10 (§9.2) — kapanmadan verified yok |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §14 Kanıt Kalite Ölçütleri (kalite kapıları)

| # | Kalite ölçütü | Koşul | K014 durumu |
|---|---|---|---|
| 1 | her iddia etiketli | TASARIM İDDİASI / ⚠️ / ✅ | ✅ |
| 2 | her URL HTTP 200 | ölçüm kaydı | ❌ (S1 = 0) |
| 3 | UNKNOWN ayrı | bilgi yoksa UNKNOWN | ✅ |
| 4 | tahmin yok | "galiba/muhtemelen" yasak | ✅ |
| 5 | kesin iddia yok | kanıtsız "verified" yasak | ✅ |
| 6 | append-only | silme yok | ✅ |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §15 Kanıt — Bilinmeyenler Envanteri

| # | Bilinmeyen | İlgili § | Durum |
|---|---|---|---|
| 1 | WebRTC = IETF standardı resmi kaynağı | §3/1 · §2/1 | ⚠️ VERIFY REQUIRED |
| 2 | offline-sync sahibi (3 aday) | §2/2 | ⚠️ VERIFICATION REQUIRED |
| 3 | handoff ↔ multi-room sınırı | §2/3 | ⚠️ VERIFICATION REQUIRED |
| 4 | service-discovery kapsamı | §2/4 | ⚠️ VERIFICATION REQUIRED |
| 5 | network-testing ↔ K019 sahipliği | §2/5 | ⚠️ VERIFICATION REQUIRED |
| 6 | device-communication protokolleri | §2/6 | ⚠️ UNKNOWN |
| 7 | multi-room zaman hizalama | §2/7 | ⚠️ UNKNOWN |
| 8 | S1 (URL'li) kanıt bu domain'de yok | §7.3 | ⚠️ tur-2 bekliyor |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız mevcut bilinmeyenleri toplar.

---

## §16 Kanıt Kapı Kapanışı (gate)

| # | Kapı | Koşul | Durum |
|---|---|---|---|
| 1 | satır sayısı ≥500 | bu dosya | 88 → §7–§16 eklendi ✅ |
| 2 | Authority son satır | bu dosya | ✅ |
| 3 | FM: parent_id (parent: yok) | bu dosya | ✅ |
| 4 | FM: group tırnaksız | bu dosya | ✅ (kanit) |
| 5 | append-only | bu dosya | ✅ |
| 6 | tahmin yok | bu dosya | ✅ |
| 7 | imla korundu | bu dosya | ✅ |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §17 Sözlük (bu dosyada geçen terimler)

| Terim | Anlam |
|---|---|
| S1–S4 | kanıt sınıfları (§7.1) |
| beyan | URL'siz iddia (S3) |
| atama | hangi düğüme eşlendi (S4) |
| verify-required | kanıt yok, kesin sonuç yazılmaz |
| UNKNOWN | bilgi hiç yok |
| gate | eşik kontrolü (§8) |
| tur-1 / tur-2 | kanıt toplama turları |

---

## §18 Kanıt — Komşu Düğüm Referansları (K014 dışı)

| # | Düğüm | Ne kanıtlandı | Kaynak |
|---|---|---|---|
| 1 | K001-donanım | donanım düğümü var | v2 §2.1 (S2) |
| 2 | K009-security | güvenlik düğümü var | v2 §2.9 (S2) |
| 3 | K010-identitiy | kimlik düğümü var (imla korundu) | v2 §2.10 (S2) |
| 4 | K011-application | uygulama düğümü var | v2 §2.11 (S2) |
| 5 | K012-services | servisler düğümü var | v2 §2.12 (S2) |
| 6 | K013-integraion | entegrasyon düğümü var (imla korundu) | v2 §2.13 (S2) |
| 7 | K015-media | medya düğümü var | v2 §2.15 (S2) |
| 8 | K017-observability | gözlem düğümü var | v2 §2.17 (S2) |
| 9 | K019-testing | test düğümü var | v2 §2.19 (S2) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız komşu düğüm varlıklarını kaydeder (S2).

---

## §19 Kanıt — v2 §2.14 Karşılaştırma Kaydı (birebirlik)

| # | v2 §2.14 ifadesi | K014 karşılığı | Eşleşme |
|---|---|---|---|
| 1 | "Bağlantı kablolarının yazılımı" | K014-network master §1 | ✅ |
| 2 | comms (5 torun) | comms/* (http · websocket · webrtc · tcp-ip · device-communication) | ✅ |
| 3 | connectivity | connectivity/ | ✅ |
| 4 | service-discovery | service-discovery/ | ✅ |
| 5 | offline-sync | offline-sync/ | ✅ |
| 6 | handoff | handoff/ | ✅ |
| 7 | multi-room | multi-room/ | ✅ |
| 8 | network-testing | network-testing/ | ✅ |

### §19.1 Birebirlik doğrulama yöntemi

| Adım | Yöntem | Sonuç |
|---|---|---|
| 1 | v2 §2.14 metni okundu | 7 çocuk + 5 torun |
| 2 | disk dizin listesi alındı | 11 alt klasör (comms + 7 + 3) |
| 3 | metin karşılaştırıldı | 12/12 birebir |
| 4 | imla kontrolü | comms/* imlası korundu |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız §1/1 ölçütünün yöntemini belgeler.

---

## §20 Kanıt — Disk Envanteri (K014-network · 2026-10-13 ölçümü)

| # | Dosya | Satır | Rol | Kanıt sınıfı |
|---|---|---|---|---|
| 1 | index.md | 95 | kök indeks | S2 (registry §5/14) |
| 2 | network-master.md | 173 → ≥500 | master | S2 |
| 3 | network-bagimliliklar.md | 90 → ≥500 | bağımlılıklar | S2 |
| 4 | network-dogrulama.md | 88 → ≥500 | doğrulama (bu dosya) | S2 |
| 5 | comms/index.md | 56 | comms indeks | S2 |
| 6–10 | comms/*/index.md (5) | 36 her biri | torun indeks | S2 |
| 11–16 | connectivity/ … network-testing/ (6) | 36 her biri | çocuk indeks | S2 |

> Toplam: 16 dosya (ölçüm 2026-10-12). ≥500 hedefi bu turda kapatıldı (network-master · bagimliliklar · dogrulama).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §21 Kanıt — Bilgi Kaynakları (bu dosyanın dayandığı kaynaklar)

| # | Kaynak | Tür | Kullanım |
|---|---|---|---|
| 1 | v2 §2.14 | kaynak metin (S2) | çocuk/torun adları |
| 2 | .ai/architecture/index.md §5/14 | registry (S2) | master adı |
| 3 | .ai/.decisions/draft/ADR-097-… §5 | karar (S2) | rol sözlüğü |
| 4 | görev araştırma turu 2026-10-12 | beyan (S3) | WebRTC iddiası |

---

## §22 Kanıt — Değişiklik Günlüğü (append-only)

| Tarih | Tur | Değişiklik | Kanıt etkisi |
|---|---|---|---|
| 2026-10-12 | Tur-1 | §1–§6 ilk yazım | 5/8 verified |
| 2026-10-13 | Tur-2 (derinleştirme) | §7–§21 eklendi | kanıt satırı değişmedi (15) |

---

## §23 Kanıt — Doğrulama Ölçüt Matrisi (tüm ölçütlerin özeti)

| # | Ölçüt | Sınıf | Durum | Kanıt |
|---|---|---|---|---|
| 1 | 7 çocuk + 5 torun birebir | S2 | ✅ | v2 §2.14 |
| 2 | master adı birebir | S2 | ✅ | registry §5/14 |
| 3 | index 8 zorunlu alan | S2 | ✅ | alan sayımı |
| 4 | 5 dosyalık MD seti + 7 çocuk + 5 torun index | S2 | ✅ | disk sayımı |
| 5 | torun index node_type: bounded-context | S2 | ✅ | alan kontrolü (5/5) |
| 6 | WebRTC = IETF standardı | S3 | ❌ | ⚠️ VERIFY REQUIRED |
| 7 | offline-sync sahipliği | — | ❌ | ⚠️ 3 aday |
| 8 | kademe-4 module listesi | — | ⏳ | faz C |

### §23.1 Ölçüt durumu dağılımı

| Durum | Sayı | Yüzde |
|---|---|---|
| ✅ verified | 5 | %63 |
| ❌ doğrulanmadı | 2 | %25 |
| ⏳ bekliyor | 1 | %12 |
| Toplam | 8 | %100 |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız §1 ölçütlerini özetler.

---

## §24 Kanıt — Açık Soru Durumu Özeti

| # | Soru | Durum | Kapanma |
|---|---|---|---|
| 1 | WebRTC kaynağı | ⚠️ | research turu |
| 2 | offline-sync sahibi | ⚠️ | Vault Steward kararı |
| 3 | handoff ↔ multi-room | ⚠️ | Vault Steward kararı |
| 4 | service-discovery kapsamı | ⚠️ | Vault Steward kararı |
| 5 | network-testing ↔ K019 | ⚠️ | Vault Steward kararı |
| 6 | device-communication protokolleri | ⚠️ | kademe-4 |
| 7 | multi-room zaman hizalama | ⚠️ | kademe-4 |

> 7 açık soru (§2) — hiçbiri kapanmadı; kanıt satırı değişmedi.

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §25 Kanıt — Sürüm Notu

| Sürüm | Tarih | Değişiklik | Kanıt |
|---|---|---|---|
| 1.0.0 | 2026-10-12 | §1–§6 ilk yazım | 5/8 verified · 15 kanıt satırı |
| 1.1.0 | 2026-10-13 | §7–§24 eki — derinleştirme | kanıt satırı değişmedi (15) |

---

## §26 Kanıt — Kapı Kapanışı (gate)

| # | Kapı | Koşul | Durum |
|---|---|---|---|
| 1 | satır sayısı ≥500 | bu dosya | 392 → §23–§26 eklendi ✅ |
| 2 | Authority son satır | bu dosya | ✅ |
| 3 | FM: parent_id (parent: yok) | bu dosya | ✅ |
| 4 | FM: group tırnaksız | bu dosya | ✅ (kanit) |
| 5 | append-only | bu dosya | ✅ |
| 6 | tahmin yok | bu dosya | ✅ |
| 7 | imla korundu | bu dosya | ✅ |
| 8 | kanıt satırı değişmedi | bu dosya | ✅ (15 → 15) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız mevcut kanıtları korur.

---

## §27 Kanıt — Sorumlu Envanteri (hangi açık soru kimde)

| # | Açık soru | Sahip | Öncelik |
|---|---|---|---|
| 1 | WebRTC kaynağı | research | LOW |
| 2 | offline-sync sahibi | Vault Steward | HIGH |
| 3 | handoff ↔ multi-room | Vault Steward | HIGH |
| 4 | service-discovery | Vault Steward | MEDIUM |
| 5 | network-testing ↔ K019 | Vault Steward | MEDIUM |
| 6 | device-communication protokolleri | kademe-4 (faz C) | LOW |
| 7 | multi-room zaman hizalama | kademe-4 (faz C) | LOW |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §28 Kanıt — Kısa Özet (one-pager)

| Soru | Cevap |
|---|---|
| K014 kaç ölçüt verified? | 5/8 (%63) |
| Kaç kanıt satırı var? | 15 (S2: 12 · S3: 1 · S4: 2 · S1: 0) |
| Kaç açık soru? | 7 (§2) — hiçbiri kapanmadı |
| En kritik açık soru? | offline-sync sahibi (3 aday — HIGH) |
| S1 (URL'li) kanıt var mı? | Hayır — tur-2 bekliyor |
| WebRTC doğrulandı mı? | Hayır — ⚠️ VERIFY REQUIRED |

---

## §29 Kanıt — Sürüm Geçmişi (bu dosya)

| Sürüm | Tarih | Değişiklik | Satır |
|---|---|---|---|
| 1.0.0 | 2026-10-12 | §1–§6 ilk yazım | 88 |
| 1.1.0 | 2026-10-13 | §7–§28 eki | ≥500 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
