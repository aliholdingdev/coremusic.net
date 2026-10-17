---
node_id: K014-media/media-cache
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
  - "Boyut/süre (TTL) politikası → ⚠️ UNKNOWN"
group: mimari
---


# media-cache — K014-media (kademe-2 · subdomain)

**ELI15:** Medya önbelleği: tekrar indirilmemesi için tutulan geçici veri.

**Sınır (ne DEĞİL):** İndirme K015/downloads · önbellek geneli (medya dışı) başka domain → sınır ⚠️.

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
| 2 | Boyut/süre (TTL) politikası → ⚠️ UNKNOWN | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

## §ROL (ELI15)

Medya önbelleği, tekrar indirilmemesi için tutulan geçici veridir: artwork, metadata ve parça önbelleği (master §9.9 — içerik TASARIM İDDİASI). Bu çocuğun işi hangi verinin ne kadar önbellekte kalacağına ve hangi stratejiyle silineceğine karar vermektir; strateji tablosu doğrulanmıştır (master §22.5): LRU genel önbellek için, LFU sık dinlenenler için, ARC ise taramaya dayanıklı olduğu (scan-resistant) için katalog/artwork'e tercih edilir. Dikkat: ses akışında (tekil dinleme) saf LRU'nun isabet oranı ≈ 0'dır (master §22.5) — parça önbelleği LRU ile ölçülmez. Sunum tarafında içerik `Cache-Control: public, max-age=31536000, immutable` ve ETag başlıklarıyla önbelleklenir (master §22.5); `If-None-Match` → 304 revalidasyon akışının dayanağı RFC 9110 semantics'idir (TASARIM ÖNERİSİ). Boyut ve TTL eşikleri ise hâlâ ⚠️ UNKNOWN'dur (master §12/3 → Vault Steward §16/3 kararı bekliyor) — bu dosya eşik uydurmaz.

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Kaynak |
|---|---|---|
| 1 | Medya önbelleği: tekrar indirilmemesi için geçici veri tutma | master §9.9 · v2 §2.15 (S2) |
| 2 | İçerik: artwork · metadata · parça önbelleği | master §9.9 (TASARIM İDDİASI) |
| 3 | Önbellek stratejisi: LRU · LFU · ARC · TTL | master §22.5 (doğrulanmış strateji tablosu) |
| 4 | `Cache-Control: public, max-age=31536000, immutable` + ETag başlıkları | master §22.5 (doğrulanmış) |
| 5 | Scan-resistant (ARC) katalog/artwork önbelleği | master §22.5 (ARC — katalog için tercih) |
| 6 | ETag revalidasyonu: `If-None-Match` → 304 (gövde yok) | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) · dayanak RFC 9110 (master §22.7) |
| 7 | TTL süpürücü (zamanlayıcı temizlik) | master §22.5 (TTL) · süre ⚠️ UNKNOWN (§16/3) |
| 8 | Önbellek isabet oranı (hit/miss) ölçüm verisi | master §15/4 (eşik ⚠️ UNKNOWN) |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Kaynak |
|---|---|---|---|
| 1 | İndirme (neyin dosya olarak saklanacağı) | K014-media/downloads | master §9.7 · §2.2 |
| 2 | Çevrimdışı kullanılabilirlik (ne hazır / ne silinmeli) | K014-media/offline | master §9.8 |
| 3 | Kalıcı veri şeması/ORM (önbellek kayıtları) | K007-data | master §2.2 |
| 4 | Ağ taşımasının ayrıntısı (revalidasyon isteği taşır) | K010-network | master §2.2 |
| 5 | Ses sinyali işleme / decode | K003-audio · K004-dsp | master §2.2 |
| 6 | Dış katalog/kaynak (neyin önbellekleneceği) | K013-integraion | master §2.2 |
| 7 | Arayüz gösterimi (önbellek durumu/gösterimi) | K011-application | master §2.2 |
| 8 | Boyut/TTL eşik kararı (sayısal politika) | Vault Steward (§16/3) | master §9.9 · §12/3 → ⚠️ UNKNOWN |

## §BİLEŞENLER

| # | Bileşen | Sorumluluk | Etiket |
|---|---|---|---|
| 1 | ARC politika motoru | T1 (recency) / T2 (frequency) + B1/B2 hayalet listeleriyle admit/evict kararı — tarama kirletmez | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) · strateji adı doğrulanmış (master §22.5) |
| 2 | LRU kontrast politikası | basit genel önbellek; audio stream'de isabet ≈ 0 — parça için ölçüt değil | doğrulanmış (master §22.5 — hit ≈ 0 uyarısı) |
| 3 | TTL süpürücü | zamanlayıcı temizlik: expire olan girdileri siler | TASARIM ÖNERİSİ · süre ⚠️ UNKNOWN (master §16/3) |
| 4 | ETag revalidatörü | `If-None-Match` → 304: gövde yok, önbellek geçerli/süre tazelenir | TASARIM ÖNERİSİ · ETag doğrulanmış (master §22.5) · RFC 9110 (§22.7) |
| 5 | Cache başlığı üreteci | katalog/artwork için `Cache-Control: immutable` + ETag sunumu | doğrulanmış (master §22.5) |
| 6 | İçerik sınıfı ayrımı | katalog/artwork → ARC; parça önbelleği → TTL (LRU ölçüt değil) | TASARIM ÖNERİSİ (master §22.5 önerisi) |
| 7 | Ölçüm veri üretici | hit/miss oranı verisi (K017-observability'a taşınır) | TASARIM İDDİASI (ölçüm master §15/4 — eşik ⚠️ UNKNOWN) |
| 8 | Politika kapısı | boyut/TTL eşiği okuması | ⚠️ UNKNOWN (master §9.9 · §12/3 → Vault Steward §16/3) |

## §STANDARTLAR

| # | Standart/fark | Ne | Kaynak | Etiket |
|---|---|---|---|---|
| 1 | `Cache-Control: public, max-age=31536000, immutable` | önbelleklenir içeriğin sunum başlığı | master §22.5 | ✅ doğrulanmış |
| 2 | ETag (içerik hash) | revalidasyon anahtarı (`If-None-Match` çifti) | master §22.5 | ✅ doğrulanmış |
| 3 | ARC | scan-resistant — tarama ile kirletilmez; katalog/artwork için tercih | master §22.5 | ✅ doğrulanmış (strateji tablosu) |
| 4 | LRU | genel önbellek — audio stream'de isabet ≈ 0 (tekil dinleme), scan'e dayanıksız | master §22.5 | ✅ doğrulanmış |
| 5 | LFU | sık dinlenenler için frekans temelli temizlik | master §22.5 | ✅ doğrulanmış |
| 6 | TTL | zamanlayıcı temizlik (süre eşiği ayrı karar) | master §22.5 | ✅ doğrulanmış (eşik ⚠️ UNKNOWN) |
| 7 | HTTP semantics (RFC 9110, STD 97) | `If-None-Match` → `304 Not Modified` revalidasyon akışının temeli | master §22.2 · §22.7 | ✅ doğrulanmış (RFC) · akış TASARIM ÖNERİSİ |

### §STANDARTLAR — TASARIM ÖNERİLERİ (Vault Steward onayı ister)

| # | Öneri | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | Katalog/artwork → ARC + `immutable`/ETag | tarama dayanıklılığı + yeniden doğrulama maliyeti düşüklüğü | master §22.5 TASARIM ÖNERİSİ · `ornekler.md` §2-§16 |
| 2 | Parça (audio stream) önbelleği → TTL; LRU ile ölçülmez | tekil dinlemede LRU isabet ≈ 0 | master §22.5 · süre ⚠️ UNKNOWN (§16/3) |
| 3 | Revalidasyon = `If-None-Match` → 304; 304'te gövde yok, süre tazelenir | bandwidht tasarrufu, içerik bütünlüğü | master §22.5 (ETag) + RFC 9110 (§22.7) · `ornekler.md` |
| 4 | Üç kademe artwork (thumbnail/medium/large) `immutable` cache ile sunulur | CAA kademesi (250/500/1200 px) ile uyum | master §22.4 TASARIM ÖNERİSİ → §22.5 |
| 5 | Hit/miss oranı K017-observability'a ölçüm olarak akar; eşik konmaz | eşik Vault Steward §16/3 kararı | master §15/4 (eşik ⚠️ UNKNOWN) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
