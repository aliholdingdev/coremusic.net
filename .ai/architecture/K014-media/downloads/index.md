---
node_id: K014-media/downloads
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
  - "Saklama politikası/sınırı → ⚠️ UNKNOWN"
group: mimari
---


# downloads — K014-media (kademe-2 · subdomain)

**ELI15:** İndirme: dosyanın yerel kopyasının alınması ve saklanması.

**Sınır (ne DEĞİL):** Taşıma K014 · çevrimdışı K015/offline · önbellek K015/media-cache.

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
| 2 | Saklama politikası/sınırı → ⚠️ UNKNOWN | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

## §ROL (ELI15)

Downloads, bir dosyanın yerel kopyasının alınması ve saklanması işidir (master §9.7 — v2 §2.15 S2). Kullanıcı bir parçayı "indir" dediğinde istek K010-network üzerinden gider, dosya yerel depoya yazılır ve indirme durumu takip edilir (master §14.3 indirme zinciri). Yarıda kesilen indirme kaldığı yerden devam eder (resume — master §9.7 TASARIM İDDİASI); bunun dayanağı HTTP `Range` isteğidir (RFC 9110 — master §22.2, doğrulanmış). İndirilen dosyanın doğru olduğu ETag/SHA-256 içerik hash doğrulamasıyla teyit edilir (master §22.5). Çevrimdışı kullanılabilirlik bu çocuğun DEĞİL, kardeş düğüm `offline`'ın işidir (master §9.8); saklama süresi/boyut politikası ise henüz ⚠️ UNKNOWN'dur (master §12/3 → Vault Steward §16/3 kararı bekliyor).

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Kaynak |
|---|---|---|
| 1 | İndirme: dosyanın yerel kopyasının alınması | master §9.7 · v2 §2.15 (S2) |
| 2 | Resume: yarıda kesilirse kaldığı yerden devam | master §9.7 (TASARIM İDDİASI) |
| 3 | HTTP `Range: bytes=N-` ile parça/devam isteği | master §22.2 (RFC 9110 — doğrulanmış) |
| 4 | İçerik hash doğrulama: ETag (+ SHA-256 karşılığı) | master §22.5 (doğrulanmış) |
| 5 | İndirme durumu + ilerleme takibi (byte/byte) | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) · ölçüm master §15/3 |
| 6 | `.part` geçici dosya + tamamlanınca atomik rename | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) |
| 7 | İndirilebilir format seti: MP3 · FLAC · WAV | master §22.1 (TASARIM ÖNERİSİ set kararı) |
| 8 | Yeniden deneme (retry) + exponential backoff | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Kaynak |
|---|---|---|---|
| 1 | Ağ taşımasının ayrıntısı (bağlantı, yeniden gönderim) | K010-network | master §2.2 · §9.7 |
| 2 | Çevrimdışı kullanılabilirlik (ne hazır / ne silinmeli) | K014-media/offline | master §9.8 · §14.3 |
| 3 | Medya önbelleği (boyut/TTL politikası sahibi) | K014-media/media-cache | master §9.9 · §12/3 |
| 4 | Kalıcı veri şeması/ORM (indirme kayıtları) | K007-data | master §2.2 |
| 5 | Dış kaynak/katalog (neyin indirileceği) | K013-integraion | master §2.2 |
| 6 | Uygulama akışı/arayüz ("indir" butonu, ilerleme çubuğu) | K011-application | master §2.2 · §14.3 |
| 7 | Kuyruk/servis yürütme (arka plan indirme) | K012-services | master §2.2 |
| 8 | Saklama süresi/boyut sınırı kararı | Vault Steward (§16/3) | master §9.7 · §12/3 → ⚠️ UNKNOWN |

## §BİLEŞENLER

| # | Bileşen | Sorumluluk | Etiket |
|---|---|---|---|
| 1 | Range istemcisi | `Range: bytes=N-` ile devam isteği (206 Partial Content) | doğrulanmış (master §22.2 — RFC 9110) |
| 2 | `.part` yazıcı + atomik rename | geçici dosyaya append; bitince hedefe taşıma — yarım dosya asla görünmez | TASARIM ÖNERİSİ (`ornekler.md`) |
| 3 | Checksum doğrulayıcı | ETag (içerik hash) / SHA-256 karşılaştırması | doğrulanmış (master §22.5) · eşitlik varsayımı TASARIM ÖNERİSİ |
| 4 | Retry/backoff denetleyici | hata sonrası exponential backoff ile yeniden deneme | TASARIM ÖNERİSİ (`ornekler.md`) — üst sınır ⚠️ UNKNOWN |
| 5 | İlerleme izleyici | indirilen/Toplam byte raporu | TASARIM İDDİASI (ölçüm master §15/3 — eşik ⚠️ UNKNOWN) |
| 6 | Taşıma kapısı | K010-network üzerinden HTTP çağrısı | TASARIM İDDİASI (master §2.2 · §14.3) |
| 7 | Kalıcılık seam'i | indirme işi kaydı (state/offset/etag) → şema K007'de | TASARIM İDDİASI (master §2.2) |
| 8 | Politika kapısı | saklama süresi/boyut sınırı okuması | ⚠️ UNKNOWN (master §9.7 · §12/3) |

## §STANDARTLAR

| # | Standart/fark | Ne | Kaynak | Etiket |
|---|---|---|---|---|
| 1 | HTTP Range (RFC 9110, STD 97) | `Range: bytes=N-` + `206 Partial Content` — resume temeli | master §22.2 | ✅ doğrulanmış |
| 2 | `Cache-Control: public, max-age=31536000, immutable` | indirilen içeriğin önbellek başlığı | master §22.5 | ✅ doğrulanmış |
| 3 | ETag | içerik hash — indirme sonrası checksum doğrulaması | master §22.5 | ✅ doğrulanmış |
| 4 | MP3 · FLAC · WAV | indirilebilir temel format seti (patent durumu doğrulandı) | master §22.1 | ✅ doğrulanmış |
| 5 | AAC (Via LA) · WMA (proprietary) | indirme listesine ALINMAZ | master §22.1 | ✅ doğrulanmış |

### §STANDARTLAR — TASARIM ÖNERİLERİ (Vault Steward onayı ister)

| # | Öneri | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | Resume deseni = `Range: bytes=N-` + `.part` geçici dosya + atomik rename | RFC 9110 resume dayanağı; yarım dosya hiçbir zaman görünmez | master §22.2 · §9.7 · `ornekler.md` §2-§16 |
| 2 | Doğrulama = ETag/SHA-256 eşitliği; uyuşmazsa yeniden indir | bütünlük kanıtı | master §22.5 · `ornekler.md` |
| 3 | Retry = exponential backoff (üst sınır Vault Steward belirler) | ağ kararlılığı | TASARIM ÖNERİSİ — eşik ⚠️ UNKNOWN (master §16/3) |
| 4 | İlerleme ölçümü byte/byte + MB/s | kullanıcı geri bildirimi | master §15/3 (eşik ⚠️ UNKNOWN) |
| 5 | Saklama politikası (boyut/TTL) belirlenmeden kalıcı indirme kuyruğu açılmaz | §12/3 açık kalmaya devam ediyor | master §9.7 · §16/3 → Vault Steward kararı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
