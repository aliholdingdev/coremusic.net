---
node_id: K008-domain/evrensel-dil
node_type: root
status: verified
parent_id: K008-domain
children: []
depends_on:
  - K008-domain/domain-master.md
evidence:
  - musicbrainz.org/doc/Recording
  - musicbrainz.org/doc/Work
  - musicbrainz.org/doc/Release
  - en.wikipedia.org/wiki/Domain-driven_design
validation: 2026-10-12 — terimler web kaynağı + repo tablo/olay adlarıyla hizalandı
open_questions:
  - "Türkçe karşılıkların UI'da kullanımı ui-design katmanınınca onaylanmalı (K008 yetkisi dışı)"
group: mimari
---

# K008-domain — Evrensel (Ubiquitous) Dil

> **Authority: Bayram Ali / Vault Steward.** CoreMusic domain'i **tek sözlük**
> kullanır: Türkçe domain niyeti + İngilizce teknik terim (kod/şema/event adı).
> Bu dosya SSOT'tur; diğer K008 dosyaları buradaki terimi değiştirmez.

## §1 DİL KURALI

| Kural | Tanım |
|---|---|
| L1 | Kod, event, tablo, dosya adı **her zaman İngilizce** teknik terimdir |
| L2 | Domain belgeleri Türkçe yazılır; terim ilk geçişte `Türkçe (English)` biçiminde |
| L3 | Eşanlamlı yasaktır: bir kavram = bir ad (kod içinde takma ad yok) |
| L4 | UI metni (ekran string'i) bu sözlüğün dışındadır; sahibi UI katmanı |
| L5 | Terim çelişkisinde bu sözlük kazanır (kapsam §4 sahiplik matrisi) |

## §2 ÇEKİRDEK DDD TERİMLERİ

| Türkçe domain | İngilizce | Tanım (tek cümle) | K008 kullanımı |
|---|---|---|---|
| Sınırlı Bağlam | Bounded Context | Dilin tutarlı olduğu alan sınırı | 10 çocuk |
| Bağlam Haritası | Context Map | BC'ler arası ilişki haritası | `context-mapping` |
| Yaygın Dil | Ubiquitous Language | Ekip + kodun paylaştığı tek dil | Bu dosya |
| Alan Olayı | Domain Event | Alanda gerçekleşen, geçmiş zamanlı olgu | master §3.3 |
| Tutarlılık Sınırı | Aggregate | Transaction'ın kapsadığı sınır | master §3.2 |
| Koleksiyon Sahibi | Aggregate Root | Aggregate'e giriş kapısı | her çocuk `-kapsam.md` |
| Varlık | Entity | Kimliği ile tanınan nesne | bilesenler §3 |
| Değer Nesnesi | Value Object | Değeri ile tanınan, değişmez nesne | bilesenler §3 |
| Depo | Repository | Aggregate kalıcılık sözleşmesi | master §3.2-A5 |
| Alan Servisi | Domain Service | Tek aggregate'e ait olmayan kural | bilesenler §1 |
| Fabrika | Factory | Karmaşık aggregate kurulumu | gerekirse çocukta |

## §3 MÜZİK ALANI TERİMLERİ (MusicBrainz diliyle hizalı)

| Türkçe | İngilizce | Kaynak / Not |
|---|---|---|
| Eser | Work | MusicBrainz "Work" — soyut beste (söz/müzik) |
| Kayıt | Recording | MusicBrainz "Recording" — stüdyo kaydı |
| Sürüm | Release | MusicBrainz "Release" — yayımlanmış fiziksel/dijital baskı |
| Sürüm Grubu | Release Group | MusicBrainz — aynı eserin sürümleri kümesi |
| Sanatçı | Artist | MusicBrainz Artist |
| Etiket | Label | Sürümü yayımlayan şirket |
| Parça | Track | Sürüm içindeki kayda bağlı numaralı giriş |
| Derleme | Compilation | Çok sanatçılı/derleme albüm (Compilation makalesi) |
| Radyo İstasyonu | Radio Station | `radio_stations` (coremusic_musics.sql s.460) |
| Podcast Programı | Podcast Show | `podcast_shows` (s.370) |

## §4 KİMLİK VE METİ VERİ TERİMLERİ

| Türkçe | İngilizce | Kaynak / Not |
|---|---|---|
| Uluslararası Kayıt Kodu | ISRC | Kayıt (recording) düzeyi kimlik |
| Uluslararası Eser Kodu | ISWC | Eser (work) düzeyi kimlik |
| Uluslararası Standart Ad Kodu | ISNI | Sanatçı/şahıs kimliği |
| MusicBrainz Kimliği | MBID | MusicBrainz UUID kimliği |
| Parmak İzi | Acoustic Fingerprint | Ses içeriği kimliği (AcoustID) |
| Ses Biçimi | Audio Format | FLAC/OGG/MP3/aac (RFC/kaynak belgeleri web-arastirma'da) |
| Etiket Bloğu | Tag Block (ID3) | ID3v2 yapısı |
| Örnekleme Oranı | Sample Rate | Dijital ses çözünürlüğü (yatay) |
| Bit Oranı | Bit Rate | Saniye başına bit |
| Bit Derinliği | Audio Bit Depth | Dijital ses çözünürlüğü (dikey) |
| Düzeltme | Dithering | Bit derinliği düşürürken gürültü enjeksiyonu |
| Kazanç Düzeltmesi | ReplayGain | Parça bazlı ses seviyesi eşitleme |

## §5 OYNATMA VE KÜTÜPHANE TERİMLERİ

| Türkçe | İngilizce | Kaynak / Not |
|---|---|---|
| Çalma Listesi | Playlist | `playlists` (coremusic_playlist.sql s.84) |
| Çalma Sırası / Kuyruk | Playback Queue | `playback_queue` (coremusic_user.sql s.18) |
| Favori | Favorite | `user_favorites` (s.59) |
| Dinleme Geçmişi | Listening History | `user_listening_history` (s.83) |
| Tercih | User Preference | `user_preferences` (s.104) |
| Profil | User Profile | `user_profiles` (s.126) |
| Boşluksuz Oynatma | Gapless Playback | Parçalar arası boşluksuz geçiş |
| Çapraz Solma | Crossfading | Parçalar arası üst üste binen geçiş |
| Karıştırma | Shuffle | Sıra dışına çıkan rastgele oynatma |
| Cihaz | Device | `devices` (coremusic_media.sql s.89) |
| Anahtar | API Key | `ApiKeyRepository` (shared/src) |

## §6 ALAN OLAYI ADLANDIRMASI

| Türkçe | Event adı (İngilizce, geçmiş zaman) |
|---|---|
| Müzik çalındı | `MusicPlayedEvent` |
| Çalma listesi oluşturuldu | `PlaylistCreatedEvent` |
| Kullanıcı kaydı tamamlandı | `UserRegisteredEvent` |
| Medya dönüştürüldü | `MediaTranscodedEvent` |
| Kuyruk güncellendi | `PlaybackQueueUpdatedEvent` |

**Kural:** Event adı `{Nesne}{Olay}Event` = özne + fiil (ingilizce, past tense).
Türkçe karşılık yalnız belgede; kodda İngilizce (L1).

## §7 YASAK EŞANLAMLILAR (L3 — tek ad kuralı)

| Yasak kullanım | Doğru ad |
|---|---|
| `track` ↔ `recording` karıştırma | Track = sürüm girişi; Recording = ses kaydı |
| `song` (kodda) | `Recording` veya `Work` — bağlama göre |
| `item` / `thing` | Somut ad (Playlist, MediaFile…) |
| `song_id` / `track_id` (karma) | Bağlam adı: `recordingId`, `trackId` (sürüm girişi) |
| `user_id` ile `member_id` eşanlamlılığı | `userId` (tek ad) |

## §8 TERİM EKLEME PROTOKOLÜ

| Adım | Kural |
|---|---|
| 1 | Yeni terim önce çocuk `-kapsam.md`'de önerilir |
| 2 | Onay sonrası bu dosyaya §'ye eklenir (satır: Türkçe · İngilizce · kaynak) |
| 3 | Kaynak web ise URL `domain-web-arastirma.md`'ye (tekil) yazılır, buraya değil |

---

*Authority: Bayram Ali / Vault Steward · K008-domain evrensel dil · 2026-10-12 · MusicBrainz + DDD kaynağı hizalı*
