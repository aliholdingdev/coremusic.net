---
title: "media — Doğrulama"
type: architecture-verification
node_id: K008-domain/media
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# media — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda açılan URL'ler · (b) bu oturumda okunan gerçek disk
> yolları. Yasak: URL, sınıf, satır uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Spotify Track nesnesinde `is_local` alanı yerel dosya ayrımını taşır | https://developer.spotify.com/documentation/web-api/reference/get-track | 2026-10-12 | ✅ sayfa açıldı |
| 2 | Spotify, "Spotify content may not be downloaded" politikasını API dokümanında yazılı tutar (download ≠ izinli) | https://developer.spotify.com/documentation/web-api/reference/get-track (politika notu) | 2026-10-12 | ✅ sayfada metin var |
| 3 | Spotify, içeriğin ML/AI modeline sokulmasını yasaklar (erişim türü ≠ eğitim izni) | https://developer.spotify.com/documentation/web-api/reference/get-recommendations (politika notu) | 2026-10-12 | ✅ sayfa açıldı |
| 4 | Yerel dosya içeriğinin playlist'te `is_local` ile temsil edildiği dokümante | https://developer.spotify.com/documentation/web-api/concepts/playlists | 2026-10-12 | ✅ doğrulandı |
| 5 | `media.accessed` domain event gerçek (`mediaId · userId · accessType`) | `shared/src/Events/Domain/MediaAccessedEvent.php` satır 27–29, 39 | 2026-10-12 | ✅ shell ile okundu |
| 6 | `accessType` doc-blok "stream, download, etc." der → küme tamamlanmamış | aynı dosya satır 80 | 2026-10-12 | ✅ shell ile okundu |
| 7 | `payload()` `accessType` + `timestamp` yayınlar | aynı dosya satır 55–60 | 2026-10-12 | ✅ shell ile okundu |
| 8 | media rolü (tasarım yorumu) = katalog kavramları üçlüsü | `domain-master.md` §1 satır 40 | 2026-10-12 | ✅ dosya okundu |
| 9 | media, v2 §2.8'de ad olarak geçer (satır 299) | `domain-master.md` §3 satır 75 · `media/index.md` satır 9 | 2026-10-12 | ✅ dosya okundu |
| 10 | media rol tanımı v2'de YOK | `media/index.md` satır 13 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | `accessType` sabit kümesi (kapalı enum) tanımlı değil | `MediaAccessedEvent.php` satır 80 ("etc.") | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 12 | K008 ↔ K003/K015 "nasıl çalınır" sınırı açık | `domain-bagimliliklar.md` §4 satır 49 · `domain-master.md` §2.1 satır 51 | 2026-10-12 | ⚠️ SINIR AÇIK |
| 13 | Media aggregate'sinin CoreMusic'te tanımlı olduğu | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 14 | CoreMusic'in download/DRM politikası (lisans kısıtı) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 15 | media erişim kaydının saklandığı tablo/depo | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 15 |
| Benzersiz URL | 3 (get-track · get-recommendations · concepts/playlists) |
| Gerçek dosya yolu satırı | 8 (5–12) |
| ✅ doğrulandı | 9 (1–9) |
| ⚠️ VERIFICATION REQUIRED / SINIR AÇIK | 3 (10 · 11 · 12) |
| UNKNOWN | 3 (13 · 14 · 15) |

**Geçiş kuralı:** satır 10–11 ADR-097 K4 ile, 12 sınır ADR'si ile kapanır · 13–15 kanıt
gelene kadar UNKNOWN kalır; kapanmazsa `status: draft` sürer.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
