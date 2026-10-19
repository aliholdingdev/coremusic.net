---
title: "music — Doğrulama"
type: architecture-verification
node_id: K008-domain/music
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# music — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda açılan URL'ler · (b) bu oturumda okunan gerçek disk
> yolları. Yasak: URL, sınıf, satır uydurmak; uydurulan satır sayılmaz.
> Durumlar: ✅ doğrulandı · ⚠️ VERIFICATION REQUIRED · **UNKNOWN** (kanıt yok).

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Spotify Track nesnesi `album · artists · duration_ms · name · uri · is_local` alanlarını tanımlar | https://developer.spotify.com/documentation/web-api/reference/get-track | 2026-10-15 | ✅ HTTP GET — yanıt örneğinde `is_local: false` görünür |
| 2 | Spotify "Get Album Tracks" uç noktası albüm içindeki track listesini döner | https://developer.spotify.com/documentation/web-api/reference/get-an-albums-tracks | 2026-10-15 | ✅ HTTP GET |
| 3 | Katalog dili referansı: DDD Reference (Evans 2004) — DDD kalıp/tanım özeti | https://www.domainlanguage.com/ddd/reference/ | 2026-10-15 | ✅ HTTP GET — "Make the model explicit" ifadesi HTML'de görünmedi → ⚠️ VERIFICATION REQUIRED (PDF içeriği) |
| 4 | Bounded Context, büyük modeli birleştirici değil ayrıştırıcı bir kalıptır (Fowler bliki) | [[domain-web-arastirma]] §1 #5 (kök URL tekil — C4) | 2026-10-12 | ✅ kökte doğrulandı |
| 5 | CoreMusic katalog DTO'su 7 alanlı (`id·title·artist·album·duration·coverUrl·streamUrl`) | `shared/src/Api/Dto/Response/MusicResponse.php` satır 20–26 | 2026-10-12 | ✅ shell ile okundu |
| 6 | `music.added` domain event gerçek (`musicId·addedBy·title`) | `shared/src/Events/Domain/MusicAddedEvent.php` satır 27–29, 39 | 2026-10-12 | ✅ shell ile okundu |
| 7 | `music.played` domain event gerçek (`musicId·userId·duration`) | `shared/src/Events/Domain/MusicPlayedEvent.php` satır 27–29, 39 | 2026-10-12 | ✅ shell ile okundu |
| 8 | music rolü (tasarım yorumu) = katalog kavramları | `domain-master.md` §1 satır 40 | 2026-10-12 | ✅ dosya okundu |
| 9 | music, v2 §2.8'de ad olarak geçer (satır 298) | `domain-master.md` §3 satır 74 · `music/index.md` satır 9 | 2026-10-12 | ✅ dosya okundu |
| 10 | music'in resmi rol tanımı v2'de YOK | `music/index.md` satır 13 (open_questions) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | Aggregate sınırı (Track aggregate) ve invariant listesi | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (ADR-097 K4) |
| 12 | music ↔ media sınırı (aynı varlık mı?) | `domain-master.md` §7 (bu soru kayıtlı değil) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 13 | CoreMusic'in katalog modelini Koel/Ampache'den türettiği | `domain-master.md` §4 satır 99 (referans ≠ seçim) | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 14 | CoreMusic'in Spotify/Apple kataloğunu kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 15 | music ↔ K013-integraion eşleme alanı (hangi id?) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 15 |
| Benzersiz URL | 3 (get-track · get-an-albums-tracks · DDD Reference — #4 kök pontere taşındı, C4) |
| Gerçek dosya yolu satırı | 8 (5–12) |
| ✅ doğrulandı | 9 (1–9) |
| ⚠️ VERIFICATION REQUIRED | 3 (10 · 11 · 12) |
| UNKNOWN | 3 (13 · 14 · 15) |

**Geçiş kuralı:** satır 10–12 ADR-097 K4 (kademe-3 kapsam kilitlenmesi) ile kapanır ·
satır 13–15 kanıt gelene kadar UNKNOWN kalır; kanıt gelmezse `status: draft` sürer
(`domain-dogrulama.md` §5 satır 67).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
