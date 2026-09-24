---
title: "CoreMusic — Müzik Streaming Sunucuları Ekosistem Dersleri"
type: guide
category: ecosystem
version: 1.0.0
status: active
authority: reference
durum: active
tarih: 2026-09-24
kaynak: exa web doğrulaması (2026-09-24)
updated: 2026-09-24
---

# CoreMusic — Müzik Streaming Sunucuları Ekosistem Dersleri

**Zorunlu Bağlantılar:** [[index]] · [[../CLAUDE.md]] · [[../AGENTS.md]] · [[../architecture/github-referanslari]] · [[../architecture/index]] · [[ekosistem-mimarileri]] · [[../.templates/documentation/docs-md-template]]

**Konu:** Koel · Ampache · Mopidy → **K8 · K9 · K15** (ikincil: K4, K5, K11)

---

## §1 Amaç

Bu doküman, açık kaynak **müzik streaming sunucularından** (Koel 17.175★, Ampache 3.809★, Mopidy) CoreMusic'in **K8 Servis**, **K9 API & Routing** ve **K15 Medya & Streaming** katmanları için çıkarılabilir mimari dersleri, **ne kopyalanır / ne kopyalanmaz** kararını ve **lisans tablosunu** tek yerde toplar.

CoreMusic'in server + client çift rolü olan bir platform olduğunu [[../CLAUDE.md]] §4.2 tanımlar: kütüphane yönetimi, metadata arşivi, streaming uçları ve otomatik indirme yeteneği. Bu yetenek kümesi, 20 yıldır üretimde çalışan açık kaynak sunucularla aynı problem alanıdır — dolayısıyla **mimari ders boldur, kod kopyası yasaktır** (özellikle AGPL kapsamında).

| Alan | Değer |
|------|-------|
| Hedef kitle | Backend Architect (K8/K9), Data Engineer (K5), Embedded (K15 stream uçları), Vault Steward |
| Kapsadığı proje | Koel (Laravel+Vue), Ampache (PHP), Mopidy (Python) |
| Ana çıktı | Ders → K8/K9/K15 entegrasyon + lisans tablosu (AGPL kırmızı çizgi) |
| Doğrulama | exa web doğrulaması, 2026-09-24 — yeni web araştırması yapılmamıştır |
| Vault bağlantısı | Referans havuzu [[../architecture/github-referanslari]] §2 · katman SSOT [[../architecture/index]] §2 |
| Kural | Yalnız fikir alınır; AGPL kodu CoreMusic'e (kapalı kaynak) asla kopyalanmaz — §4.4 |

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Koel, Ampache, Mopidy mimari dersleri | Kod kopyası / fork (AGPL ve tümü için beyaz oda dışında) |
| Ders → K8/K9/K15 entegrasyon somut girdileri | K8 servis kodunun kendisi (→ architecture/k8-servis/) |
| Lisans tablosu + AGPL kırmızı çizgisi | Lisans hukuki danışmanlığı (→ ADR + yetkili) |
| Subsonic API dersi (kendi implementasyonumuz) | Subsonic API spesifikasyonunun tam metni (dış kaynak) |
| Koel arayüz/AI asistan dersi (fikir) | UI kodu — Guardrail #11 mockup gate (→ .ai/ui-design/) |

*Alt konular:* §3.1 Koel · §3.2 Ampache · §3.3 Mopidy · §3.4 karşılaştırma · §3.5 ders→K matrisi · §4.4 lisans · §5 workflow.

### §2.2 Hedef Kitle

| Kitle | Ne Alır |
|-------|---------|
| Backend Architect | Servis sınırı, playlist sorgu motoru, API yüzeyi dersleri (K8/K9) |
| Data Engineer | Smart playlist kural modeli → BCNF şema uyarlaması (K5) |
| Embedded / Windows SW | Stream uçları ve transcode zinciri (K15) |
| AI (K4) | Koel AI asistan + Last.fm/Spotify/MusicBrainz sinyal fikirleri |
| UI Designer | Koel'in arayüz liderliği dersi — Guardrail #11 kapsamında mockup'tan sonra |

---

### §2.3 Bağımlılıklar (Bu Dosyadan Önce Okunanlar)

| Sıra | Dosya | Amaç |
|------|-------|------|
| 1 | [[../CLAUDE.md]] | §5 K katmanları, §6A API-First (ADR-084), §6A.4 Event Driven (ADR-086) |
| 2 | [[../architecture/index]] §2 | K8/K9/K15 tanım ve bileşen sayıları |
| 3 | [[../architecture/github-referanslari]] §2 | Koel/Ampache/Mopidy havuz kaydı |
| 4 | [[service-integration]] | Mevcut event types (track.play, download.complete) |
| 5 | [[index]] §3.4-§3.5 | Ekosistem geneli K çapraz matrisi |
| 6 | [[../.templates/index]] §7.1 | Şablon seçimi (Guardrail #16) |

---

## §3 Mimari

### §3.1 Koel — Arayüz Lideri (MIT)

| Alan | Değer (exa 2026-09-24) |
|------|------------------------|
| Stars | 17.175★ |
| Stack | Laravel (PHP) + Vue |
| Lisans | MIT |
| Öne çıkanlar | Akıllı playlist (smart playlist), Last.fm/Spotify/MusicBrainz entegrasyonları, AI asistan |
| Karşılaştırma | Arayüz lideri (Ampache = özellik lideri) |
| ⚠️ Not | [[../architecture/github-referanslari]] §2'de 17.195★ kaydı vardır (2026-09-20 havuz kaydı); yıldız farkı doğal drift — iki tablo da kendi tarihini taşır |

**Çıkarılan mimari dersler:**

| # | Ders | Mekanizma | CoreMusic Karşılığı |
|---|------|-----------|---------------------|
| K-1 | Akıllı playlist = kural sorgusu, elle listelemek değil | Kural tabanlı sorgu (artist/genre/mood/ play-count koşulları) | coremusic_playlist + coremusic_catalog kırılımında saved-query modeli |
| K-2 | Entegrasyonlar servis arkasında tutulur | Last.fm/Spotify/MusicBrainz ayrı adaptör | K8 AI/Media Service adaptör arayüzü; dış sağlayıcı değişirse tek yer |
| K-3 | AI asistan ayrı bir yüzey | Sohbet/öneri UI ≠ çalma motoru | K4 Recommendation + K10 panel yüzeyi ayrımı |
| K-4 | Arayüz = ürün farkı | Vue SPA, hızlı gezinme | CoreMusic: Vanilla JS SPA (ADR-001) — framework YASAK; ders: hız/akış, framework değil |
| K-5 | Metadata zenginliği (MusicBrainz) kütüphanenin değeri | Sanatçı/album eser kimliği eşleştirme | coremusic_musics + coremusic_catalog'ta external_id sütun ailesi |

### §3.2 Ampache — Özellik Lideri (AGPL-3.0)

| Alan | Değer (exa 2026-09-24) |
|------|------------------------|
| Stars | 3.809★ |
| Stack | PHP |
| Lisans | AGPL-3.0 |
| Öne çıkanlar | Subsonic API uyumluluğu, 2001'den beri sürüm, geniş özellik seti |
| Karşılaştırma | Özellik lideri (Koel = arayüz lideri) |
| ⚠️ Not | github-referanslari §2'de 3.793★ kaydı vardır (2026-09-20) — drift, iki kayıt da tarihli |

**Çıkarılan mimari dersler:**

| # | Ders | Mekanizma | CoreMusic Karşılığı |
|---|------|-----------|---------------------|
| A-1 | Harici istemciler için endüstri standardı API yüzeyi | Subsonic API (/rest/*.view) | Kendi implementasyonumuzla K9 Gateway'a versiyonlu Subsonic-uyumlu uç ailesi |
| A-2 | Uzun ömürlü şema = migration disiplini | 20+ yıllık geriye uyumluluk | K5 coremusic_patch migration disiplini; BCNF + geriye dönük okuma katmanı |
| A-3 | Transcode zinciri zorunlu | Orijinal dosya → istemciye uygun bit-rate | K15 audio-transcoding: on-demand transcode kuyruğu |
| A-4 | Katalog tarama = ayrı iş | Catalog scan arka planda | K8 Media Service batch işi + K12 metrik |
| A-5 | Kullanıcı/ACL katmanı ayrı | Yetki → katalog erişimi | K6 RBAC (regular/premium/studio/car/admin/system) ile eşlenir |

**🔴 Kopyalanmaz:** Ampache kodu AGPL-3.0'dır — CoreMusic kapalı kaynaktır. Kod, dosya, şablon, SQL, sınıf **asla** kopyalanmaz; yalnızca yukarıdaki mimari dersler beyaz oda dokümanına aktarılır (§4.4).

### §3.3 Mopidy — Uzantı Mimarisi (Python)

| Alan | Değer |
|------|-------|
| Stack | Python; MPD protokolü sunar; eklenti (backend/frontend) mimarisi |
| Lisans | ⚠️ VERIFICATION REQUIRED — exa 2026-09-24 doğrulamasında lisans değeri belirtilmemiştir; kullanımdan önce doğrulanmalıdır |
| Kaynak | Mopidy (github.com/mopidy) — [[../architecture/github-referanslari]] §2 havuzunda yer alır (dil: Python) |

**Çıkarılan mimari dersler:**

| # | Ders | Mekanizma | CoreMusic Karşılığı |
|---|------|-----------|---------------------|
| M-1 | Çekirdek + uzantı ayrımı | Backend (kaynak) / Frontend (protokol) eklentileri | K8 servis çekirdeği + K9 protokol adaptörleri (HTTP/WS/Subsonic yüzeyi) |
| M-2 | Protokol adaptörü = erişilebilirlik | MPD istemcileri çekirdeği değiştirmeden bağlanır | Subsonic-uyumlu uç = aynı çekirdeğe ikinci protokol yüzeyi |
| M-3 | Yerel katalog + harici kaynak birlikte | Katalog backends'i | Offline-First: yerel katalog (SQLite/MySQL) + Download Service kaynakları birleşik görünüm |
| M-4 | Oynatma durumu tek gerçek | Tek state kaynağı | K3 Audio Service playback state + K9 WS broadcast (event: track.play) |

### §3.4 Karşılaştırma Matrisi

| Ölçüt | Koel | Ampache | Mopidy | CoreMusic Çıkarımı |
|-------|------|---------|--------|--------------------|
| Dil/Stack | PHP (Laravel) + Vue | PHP | Python | PHP 8.4 + Vanilla JS + C++20 (farklı — ders alınır) |
| Lisans | MIT ✅ | AGPL-3.0 ❌ kod | ⚠️ doğrulanacak | §4.4 lisans kapısı |
| Öncelikli güç | Arayüz | Özellik | Uzantı mimarisi | Üçü de: arayüz (K10), özellik (K8), uzantı (K9 adaptör) |
| API yüzeyi | Kendi REST API | Subsonic API (standart) | MPD + HTTP | Subsonic-uyumlu + kendi REST/OpenAPI (ADR-084) |
| Playlist | Akıllı playlist | Playlist + SQL kuralları | Uzantılar | smart_query sözleşmesi (K8/K5) |
| Transcode | Var | Var (çok format) | Uzantı | K15 kuyruklu transcode |
| Harici entegrasyon | Last.fm, Spotify, MusicBrainz | Harici kaynaklar | Harici backend'ler | K8 adaptör arayüzü + K4 |
| AI | AI asistan | Yok (gözlem) | Yok | K4 Recommendation + Voice |
| Uygunluk | K8, K10, K11 | K8, K9, K15 | K8, K9, K0 | — |

### §3.5 Ders → K Katmanı Matrisi (K8 / K9 / K15)

| Ders | Kaynak | Hedef K | SOMUT Giriş |
|------|--------|---------|-------------|
| Akıllı playlist kural motoru | Koel | **K8**, K5 | Media/Control Service içine smart_query parametresi; OpenAPI'ye ekle (ADR-084) |
| MusicBrainz/Last.fm adaptör sınırı | Koel | **K8**, K4 | AI Service adaptör arayüzü: IExternalMetadataProvider; dış API anahtarları credential vault (K6) |
| AI asistanın ayrı yüzey olması | Koel | **K8**, K10, K4 | Asistan uci K9'dan geçer; çalma motoruna doğrudan çağrı YASAK (Event Bus) |
| Subsonic-uyumlu uç ailesi | Ampache | **K9** | Gateway altında /rest/* versiyonlu uç; OpenAPI'ye x-subsonic alanı |
| Transcode zinciri (istenciyi uygun format) | Ampache | **K15** | audio-transcoding.md: on-demand kuyruk + cache; FFmpeg pipeline |
| Katalog tarama batch işi | Ampache | **K8**, K12 | Media Service scan job + metrik (dosya/dk, hata oranı) |
| 20 yıl şema geçmişi | Ampache | **K5** | coremusic_patch migration disiplini; şema versiyon okuması |
| Çekirdek/uzantı ayrımı | Mopidy | **K8**, **K9** | Servis çekirdeği sabit; protokol yüzeyleri eklenti gibi adaptör |
| Oynatma durumu tek kaynak | Mopidy | **K8**, **K15** | track.play event'i tek publisher: Audio Service; diğerleri dinler (ADR-086) |
| Yerel katalog + harici kaynak birleşimi | Mopidy | **K15**, K5 | Offline-First katalog birleşim görünümü (MySQL + SQLite queue) |
| Arayüz hızı/akış dersi | Koel | **K11** | Mockup gate sonrası (Guardrail #11): gezinme akışı referansı — framework DEĞİL |

### §3.6 Ne Kopyalanır / Ne Kopyalanmaz

| ✅ Kopyalanır (fikir/standart) | ❌ Kopyalanmaz (kod/varlık) |
|-------------------------------|----------------------------|
| Mimari şema dersi (servis ayrımı, adaptör, kuyruk) | Ampache AGPL-3.0 kod dosyaları |
| API yüzeyi tasarımı (Subsonic standart istemcileri) | Koel/Ampache/Mopidy sınıf, fonksiyon, SQL kopyası |
| Akıllı playlist kural modeli (kendi sorgu motorumuz) | Harici entegrasyon anahtarları, kullanıcı verisi |
| Transcode zinciri akışı (FFmpeg — kendi pipeline'ımız) | Ekran görüntüsü/asset/marka (telif) |
| Karşılaştırma sonuçları (bu dosya §3.4) | Lisans metinleri (değiştirilemez, §4.4) |

### §3.7 Entegrasyon — CoreMusic'e SOMUT Giriş

| # | Somut Giriş | Hedef K | Giriş Noktası | Sahip Agent |
|---|-------------|---------|---------------|-------------|
| 1 | smart_query sözleşmesi: playlist kural modeli OpenAPI'ye yazılır | K8, K9, K5 | k9-api-routing/openapi-spec.md + k8-servis/media-service.md | backend |
| 2 | Subsonic-uyumlu /rest/* uç ailesi (kendi implementasyonumuz) | K9 | k9-api-routing/api-gateway.md + versioning-strategy.md | backend |
| 3 | On-demand transcode kuyruğu: istemci→format→cache→stream | K15 | k15-medya-streaming/audio-transcoding.md + streaming-protocol.md | backend |
| 4 | Katalog tarama batch işi + K12 metrikleri | K8, K12 | k8-servis/media-service.md + k12-izleme/prometheus-metrics.md | backend + devops |
| 5 | IExternalMetadataProvider adaptör arayüzü (Last.fm/MusicBrainz fikri) | K8, K4 | k8-servis/ai-service.md + k4-yapay-zeka/recommendation-engine.md | backend |
| 6 | AI asistanı Event Bus üzerinden yalnız event dinleyicisi | K8, K9 | k9-api-routing/event-bus.md (ADR-086) | backend |
| 7 | Offline-First katalog birleşim görünümü (yerel + indirilen) | K15, K5 | k15-medya-streaming/flac-support.md + k5-veri-yonetimi/sqlite-local.md | data + backend |
| 8 | AGPL beyaz oda raporu: Ampache dersleri yalnız bu dosyadan okunur | K6 | .ai/ecosystem/muzik-streaming-sunuculari.md (bu dosya) + log kaydı | security + Vault Steward |
| 9 | Koel arayüz akış dersi → mockup gate sonrası K11 girdisi | K11 | ui-design/01-mockup-index okunduktan sonra k11-ux/* | ui |

### §3.8 Derin Dersler (Uygulama Detayları)

#### §3.8.1 Koel — Akıllı Playlist Kural Modeli (alan bazlı)

> CoreMusic karşılığı: `smart_query` sözleşmesi (K8/K9) + `coremusic_playlist` şeması (K5). Aşağıdaki alanlar **taslak**tır; şema değişikliği Data Engineer + ADR gerektirir.

| Alan | Tür | Koel Dersi | CoreMusic Uyarlaması |
|------|-----|------------|----------------------|
| rule_group | enum (all/any) | koşul gruplaması | saved_query.rule_group (AND/OR) |
| rule_type | enum | artist / album / genre / year / play_count / rating / mood | coremusic_catalog kırılımına göre genişletilir |
| operator | enum | is / contains / gt / lt / between | prepared statement ile üretilir (string birleştirme YASAK) |
| value | text[] | tekil veya liste | JSON kolonu değil — BCNF uyumlu ayrı kural tablosu |
| ordering | enum | recently_added / play_count / alpha / random | playlist_order sütunu |
| limit | int | son N | query_limit (varsayılan 100) |
| dynamic | bool | kayıt anında statik mi, dinamik mi yeniden hesap mı | dynamic=1 → sorgu her çalınışta çalışır (cache: APCu, 60s) |
| owner_scope | enum | private / public / collaborative | K6 RBAC ile eşleşir (regular/premium/studio/car/admin/system) |

#### §3.8.2 Ampache — Transcode Zinciri Adımları (ders akışı)

| # | Adım | Ampache Dersi | CoreMusic K15 Karşılığı |
|---|------|---------------|--------------------------|
| 1 | Kaynak dosya tespiti | Orijinal dosya (FLAC vb.) | coremusic_musics.file_path + format metadata |
| 2 | İstemci yeteneği okuma | Codec/bitrate istek | BFF üzerinden istemci profili (ADR-084) |
| 3 | Karar: doğrudan mı, transcode mı | FLAC→MP3/AAC dönüşümü | audio-transcoding.md karar ağacı |
| 4 | Transcode işi | Arka plan işi | K8 Media Service kuyruk + K12 metrik |
| 5 | Cache | Dönüşüm çıktısının saklanması | transcode cache (Redis/APCu + dosya) |
| 6 | Stream | HTTP ile teslim | K15 streaming-protocol → K14 üzerinden |
| 7 | İzleme | Başarı/oran | K12: transcode başarı oranı, kuyruk derinliği |

#### §3.8.3 Subsonic API Yüzeyi — Uyum Hedefi (kendi implementasyonumuz)

| Uç (standart) | Amaç | CoreMusic Karşılığı |
|---------------|------|---------------------|
| ping.view | sağlık/oturum | K9 Gateway health + K7 middleware |
| getAlbumList2.view | liste sayfalama | K9 BFF query + K5 BCNF sayfalama |
| search3.view | arama | K8 Search Service (search-service.md) |
| stream.view | oynatma | K15 stream ucu (transcode kararı §3.8.2) |
| getPlaylist / createPlaylist | playlist CRUD | K8 Media/Control + smart_query genişlemesi |
| scrobble.view | dinleme bildirimi | K12/K4 event: track.play (service-integration.md) |
| getCoverArt.view | kapak | K15 media-metadata + K14 cache |

**Kural:** Bu tablo yalnız **uyum hedefi**dir; Subsonic spesifikasyonu harici kaynaktır, kodu kopyalanmaz (§3.6).

#### §3.8.4 Mopidy — Çekirdek/Uzantı Sözleşmesi (ders)

| Sözleşme | Mopidy Dersi | CoreMusic Uyarlaması |
|----------|---------------|----------------------|
| Backend (kaynak uzantısı) | Her müzik kaynağı bir backend | K8 servis çekirdeği + kaynak adaptörleri (yerel katalog, Download Service) |
| Frontend (protokol uzantısı) | MPD/HTTP ayrı katman | K9 protokol yüzeyleri: REST (OpenAPI), WS, Subsonic-uyumlu |
| Tek event kaynak | Durum değişimi tek yerden yayılır | Audio Service tek publisher; Event Bus (ADR-086) |
| Eklenti kayıt | Uzantı kendini kaydeder | Adaptör registrasyonu (config; hardcode yok) |
| Hata izolasyonu | Uzantı hatası çekirdeği düşürmez | Servis hatası → K12 alert, Event Bus devam (Dusk dersiyle kesişir: [[ses-dsp-acik-kaynak]]) |

### §3.9 Öncelik Matrisi — CoreMusic'e Aktarım Sırası

| Öncelik | Ders | Hedef K | Gerekçe | Bağımlılık |
|---------|------|---------|---------|------------|
| P0 | Transcode zinciri + stream ucu | K15 | Offline-First + her cihaza uygun teslim olmadan platform çalışmaz | FFmpeg pipeline |
| P0 | Servis sınırı + Event Bus | K8 | Diğer her dersin taşıyıcısı | ADR-086 |
| P1 | smart_query sözleşmesi | K8, K9, K5 | Playlist CoreMusic'in 10 yeteneği arasında | K5 şema onayı |
| P1 | Subsonic-uyumlu uç | K9 | Mevcut 3. taraf istemci ekosistemine erişim | P0 servis sınırı |
| P2 | Katalog tarama batch + metrik | K8, K12 | Arşivleme yeteneği | K12 Pipeline |
| P2 | Harici metadata adaptörü | K8, K4 | Öneri kalitesi | K6 credential vault |
| P3 | Arayüz/akış dersi | K11 | Guardrail #11 mockup gate'ten sonra | ui-design okuması |

### §3.10 Riskler ve Açık Sorular

| # | Risk / Açık Soru | Etki | Önlem | Durum |
|---|------------------|------|-------|-------|
| 1 | Ampache AGPL kodunun "referans" diye girmesi | Yüksek — kapalı kaynak ihlali | §4.4 kırmızı çizgi + kod incelemesinde lisans etiketi | Aktif kontrol |
| 2 | Mopidy lisansı doğrulanmadan karar vermek | Orta | VERIFICATION REQUIRED; §4.4 tablosu boş bırakıldı | Açık — doğrulama bekliyor |
| 3 | Yıldız sayısındaki drift'in (17.175/17.195) çelişki sanılması | Düşük | İki kayıt da tarihli; [[../architecture/github-referanslari]] SSOT değildir, havuz kaydıdır | Kapandı (§3.1 notu) |
| 4 | Subsonic uyumunun fazla geniş kapsamı | Orta — süre | Uç ailesi kademeli: ping → search → stream → playlist | Planlandı (§3.9 P1) |
| 5 | Smart query'nin BCNF'ye uymaması | Orta | Kural satırları ayrı tablo; JSON kolon kullanılmaz | Tasarım kuralı (§3.8.1) |
| 6 | Koel'den framework kopyası (Vue) isteği | Yüksek — ADR-001 ihlali | Yasak/Doğru tablosu §4.3 | Aktif |
| 7 | Transcode kuyruğunun K14'e doğrudan yazması | Orta — layer violation | K15 → K14 tek hedef kuralı (matris §2.1) | Kural hazır |

---

---

## §4 Kurallar

### §4.1 ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

*(docs-md-template §3.3'ten birebir — silinemez.)*

### §4.2 Bu Dosyaya Özgü Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | AGPL kodu kopyalanmaz | Ampache kodundan satır, dosya, SQL alınmaz | Kod revert + log ERROR (K6) |
| 2 | Ders ≠ kod | Her ders §3.5'te K katmanı ve somut girişle yazılır | K'sız ders işlenemez |
| 3 | Yıldız sayıları tarihlidir | Kullanılan değer: exa 2026-09-24; havuz değeri farklıysa ikisi de taşınır | Tarihsiz sayı → VERIFICATION REQUIRED |
| 4 | Subsonic uyumu kendi kodumuzla | Uç tasarımı kopyalanmaz, standart istemciye uyum hedeflenir | Uyum hedefi kaybolursa ADR |
| 5 | K8→K9 yönü | Servis API'ye çıkar; servisler arası doğrudan çağrı yasak (Event Bus) | Layer violation → revert |
| 6 | Frontmatter + ≥500 satır | docs-md-template §4.2, §4.6 | Dosya eksik sayılır |

### §4.3 Yasak / Doğru

| ✅ Yasak | ✅ Doğru |
|----------|----------|
| Ampache'den dosya kopyalamak | Beyaz oda: dersi bu dosyadan oku, kodu sıfırdan yaz |
| "Koel'i fork'layıp panel yapalım" | ADR-001: Vanilla JS — framework yasak; sadece akış dersi |
| Lisanssız Mopidy bilgisi | VERIFICATION REQUIRED etiketi |
| Servisler arası doğrudan HTTP | Event Bus (ADR-086) |
| Playlist kuralında SELECT * | Açık sütun listesi + prepared statement |

### §4.4 Lisans Tablosu (Bağlayıcı)

| Proje | Lisans | Kod Kopyalanır mı? | Fikir/Mimari Ders | Karar |
|-------|--------|--------------------|-------------------|-------|
| Koel | MIT | ⚠️ Teknik olarak mümkün (MIT); **ama CoreMusic kapalı kaynak — kopyalamaya gerek yok** | ✅ Serbest | Yalnız ders; kod kopyası tercih edilmez |
| Ampache | AGPL-3.0 | ❌ **HAYIR — asla** | ✅ Serbest | Kırmızı çizgi: kod, dosya, SQL, sınıf yok |
| Mopidy | ⚠️ doğrulanacak | ❌ Doğrulanana kadar yok | ✅ Ders (mimari) | Lisans doğrulanınca §4.4 tablosu güncellenir |
| Subsonic API (standart) | Harici yayın | Uyum hedefi; spesifikasyon harici kaynak | ✅ İstemci uyumu | Kendi implementasyonumuz |
| FFmpeg (transcode dersi) | LGPL/GPL varyantı | ⚠️ Dış süreç olarak kullanımı uygundur; linkleme modu ADR | ✅ | K15 pipeline ADR'si |

**AGPL Uyarısı (bağlayıcı cümle):** Ampache kaynak kodundan **hiçbir** satır CoreMusic kod tabanına, şablonuna, SQL'ine veya dokümantasyonuna kopyalanmaz; yalnız mimari fikir aktarılır. İhlal: kod revert + K6 log CRITICAL.

---

## §5 Workflow

### §5.1 Ders İşleme Adımları

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | Bu dosyanın §3.x bölümünü oku | Ders + kaynak + tarih | 5 dk |
| 2 | §3.5'ten hedef K + somut giriş satırını seç | K etiketi | 2 dk |
| 3 | §4.4 lisans kapısını uygula | ✅/⚠️/❌ | 1 dk |
| 4 | Katman dosyasını aç (k8-servis / k9-api / k15-medya) | Hedef dosya | 2 dk |
| 5 | Somut girişi işle; ADR gerekiyorsa Guardrail #14 onayı | Vault güncellemesi | 10 dk |
| 6 | log.md append | Audit satırı | 1 dk |

~~~text
OKU → K EŞLE (§3.5) → LİSANS KAPISI (§4.4) → KATMAN DOSYASI → İŞLE → ONAY (gerekirse) → LOG
~~~

### §5.2 Tipik Senaryolar

| Soru | Oku | Çıktı |
|------|-----|-------|
| Akıllı playlist nasıl tasarlanır? | §3.1 K-1 + §3.5 satır 1 | smart_query sözleşmesi |
| Subsonic istemcileri desteklensin mi? | §3.2 A-1 + §3.5 satır 4 | K9 /rest/* uç ailesi |
| Transcode nerede başlar? | §3.2 A-3 | K15 audio-transcoding kuyruğu |
| Ampache kodundan şema alabilir miyiz? | §4.4 | ❌ — beyaz oda dersi |

### §5.3 Güncelleme Protokolü

| # | Kural |
|---|-------|
| 1 | Yıldız/sürüm değişikliği → satır güncellenir, tarih korunur (eski değer üstü çizilmez; change history'ye not) |
| 2 | Lisans değişikliği tespiti → §4.4 + [[index]] §4.4 birlikte güncellenir |
| 3 | Yeni sunucu projesi → [[../architecture/github-referanslari]] §2 havuzuna + bu dosyaya §3 satırı |
| 4 | Ekleme append-only; silme yok |

---

### §5.4 K8 / K9 / K15 Uç ve Event Taslakları

| # | Uç / Event | Katman | Kaynak Ders | Not |
|---|-----------|--------|-------------|-----|
| 1 | GET /v1/playlists/{id}/smart | K9 | Koel smart playlist | rule set ile yeniden hesap |
| 2 | POST /v1/playlists/smart | K9 | Koel | Oluşturma; K5 kural tablosuna yazar |
| 3 | GET /rest/{action}.view | K9 | Ampache/Subsonic | Uyum yüzeyi — kendi implementasyonumuz |
| 4 | GET /v1/stream/{trackId} | K15 | Ampache transcode | Format kararı §3.8.2 |
| 5 | WS track.play | K8 → K9 | Mopidy tek-event dersi | Tek publisher: Audio Service |
| 6 | Event download.complete | K8 | Kendi hizmet | service-integration.md ile eşleşir |
| 7 | Event library.scan.done | K8 → K12 | Ampache katalog dersi | Metrik: dosya/dk, hata oranı |
| 8 | GET /v1/search?q= | K8/K9 | Subsonic search3 dersi | Search Service ayrı servis |

**Event eşlemesi:** Bu taslaktaki event adları [[service-integration]] tablosuyla uyumlu tutulur; çelişirse service-integration.md güncellenir (append-only).

---

### §5.5 Örnek Senaryo — "Yeni bir cihaz istemcisi Subsonic ile bağlanıyor"

| Adım | Aksiyon | Çıktı | Katman |
|------|---------|-------|--------|
| 1 | İstemci ping.view gönderir | Uyum kontrolü (§3.8.3) | K9 Gateway |
| 2 | Middleware zinciri (Origin→…→Validation) | Kimlik + yetki | K7 + K6 |
| 3 | search3.view → Search Service |uç eşlemesi (§5.4 #8) | K8 |
| 4 | stream.view → format kararı | transcode mı doğrudan mı (§3.8.2) | K15 |
| 5 | Transcode işi kuyruğa | kuyruk metriği | K8 → K12 |
| 6 | Teslim K14 üzerinden | cache/CDN | K14 |
| 7 | scrobble → track.play event | AI + log | K8 → K4, K12 |

**Rollere göre 5 dakikalık okuma:** Backend → §3.5 + §3.8.2 + §5.4 · Data → §3.8.1 · Windows/Embedded → §3.8.3 + [[asio-wasapi-rehber]] · MO → §3.9 + §3.10.

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter + durum/tarih/kaynak alanları
- [ ] Tek H1; §1-§7 eksiksiz
- [ ] §4.1 Şablon-Önce bloğu §4'ün ilk maddesi
- [ ] §2.1 Kapsam/Kapsam Dışı ≥ 3 satır
- [ ] §3'te Koel, Ampache, Mopidy için ayrı alt bölüm var
- [ ] §3.4 karşılaştırma matrisi ≥ 8 satır
- [ ] §3.5 ders→K matrisi her satırda hedef K + somut giriş içeriyor (≥ 10 satır)
- [ ] §3.6 ne kopyalanır/ne kopyalanmaz tablosu var
- [ ] §4.4 lisans tablosu ≥ 5 satır + AGPL bağlayıcı uyarısı mevcut
- [ ] AGPL'den kopyalanan kod izi yok (aranan: ampache kaynağı + kod bloğu — 0)
- [ ] VERIFICATION REQUIRED etiketleri yerinde (Mopidy lisansı)
- [ ] §5.1 ≥ 4 adım
- [ ] §6.1 - [ ] ≥ 8 madde
- [ ] §7.2 append-only değişiklik geçmişi
- [ ] Authority footer
- [ ] ≥ 500 satır (verify lines)
- [ ] Mojibake 0 (vault-utf8-writer verify)
- [ ] 0 dosya silme

### §6.2 Metrikler

| Metrik | Değer |
|--------|-------|
| Kapsanan proje | 3 (+1 standart: Subsonic API) |
| Hedef katman | K8, K9, K15 (ikincil K4, K5, K11, K12) |
| Lisans sınıfı | MIT / AGPL-3.0 / doğrulanacak |
| Somut giriş satırı | 9 |
| Doğrulama | exa 2026-09-24 |
| Versiyon | 1.0.0 |

### §6.3 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Yıldız uyuşmazlığı | 17.175 vs 17.195 | İkisi de tarihli kayıt olarak kalır; tazeleme 6 ayda bir |
| AGPL sızıntısı | Ampache dosya adı kodda | Revert + K6 CRITICAL |
| K'sız ders | §3.5 satırı boş | İşleme alınmaz; §3.5'e K ekle |
| Eksik lisans | §4.4 satırı boş | DUR, VERIFICATION REQUIRED |

### §6.4 Kanıt Komutları (salt-okunur)

~~~text
# Satır sayısı (≥500 kontrolü)
#   vault-utf8-writer verify --file .ai/ecosystem/muzik-streaming-sunuculari.md  →  lines
# Mojibake taraması
#   vault-utf8-writer scan --dir .ai/ecosystem                                   →  mojibake 0
# 0 dosya silme kanıtı
#   git status --porcelain                                                       →  delete = 0
~~~

| Beklenen Çıktı | Değer |
|----------------|-------|
| lines | ≥ 500 |
| mojibake | 0 |
| BOM | false |
| git delete | 0 |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Hedef | İlişki |
|-------|--------|
| [[index]] | Ekosistem indeksi — K çapraz matrisi |
| [[ekosistem-mimarileri]] | Kardeş: Spotify/Apple/YT Music pipeline dersleri (K8/K9/K14/K15) |
| [[../architecture/github-referanslari]] | Koel/Ampache/Mopidy havuz kaydı (§2) |
| [[../architecture/index]] | K0-K20 katman SSOT'u |
| [[../CLAUDE.md]] | §5 katmanlar, §21 yasaklı kalıplar, Guardrail #1/#3/#16 |
| [[../AGENTS.md]] | Routing §6 (PHP/API keywords → Backend Architect) |
| [[../brain]] | ADR defteri — Subsonic uç kararı için ADR buraya |
| [[../.templates/documentation/docs-md-template]] | Şablon (Guardrail #16) |
| [[../.templates/index]] | Şablon registry'si |
| [[service-integration]] | Event types (track.play vb.) — K8 event adlarıyla eşleşir |
| [[../log.md]] | Audit trail |

### §7.2 Kaynak Linkleri (Web — exa 2026-09-24)

- https://github.com/koel/koel — Koel (Laravel+Vue, MIT)
- https://github.com/ampache/ampache — Ampache (PHP, AGPL-3.0, Subsonic API)
- https://github.com/mopidy/mopidy — Mopidy (Python)
- https://www.subsonic.org/ — Subsonic API standardı (uyum hedefi)
- https://musicbrainz.org/ — MusicBrainz metadata (Koel dersi K-5)

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-24 | 1.0.0 | İlk üretim — Koel/Ampache/Mopidy dersleri, K8/K9/K15 entegrasyon, lisans tablosu (exa 2026-09-24) | ekosistem-writer (subagent) |

### §7.4 İlgili Vault Satırları

| Vault Satırı | Bu Dosyayla İlişki |
|--------------|--------------------|
| [[../CLAUDE.md]] §10 Service Map | 7 backend servisi — §3.5 girişleri buraya işler |
| [[../CLAUDE.md]] §6A.4 Event Driven | Event Bus kuralı — §5.4 event taslakları |
| [[../CLAUDE.md]] §21 Forbidden Patterns | ORM yasak — §3.8.1 sorgu üretimi prepared statement |
| [[../AGENTS.md]] §6 routing | PHP/API keyword → Backend Architect |
| [[../architecture/index]] §3 | Bağımlılık matrisi bağlantısı — K15→K14 tek hedef |


*(Append-only: yeni satır EKLENİR; mevcut satır değişmez.)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
