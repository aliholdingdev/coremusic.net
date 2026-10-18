---
title: "media — Kapsam"
type: architecture-scope
node_id: K008-domain/media
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# media — Kademe-2 Kapsam Kaydı

> **DDD notu:** bu kayıt bu dalın **Bounded Context tanımı adayıdır** (ADR-097 K4'te
> kilitlenir). Kapsam *aggregate · entity · ubiquitous language* ile kurulur;
> v2 bu dala **yalnızca ad verir** — tahminle dolgu **yapılmaz**.

## §1 Rol (ELI15)

`domain-master.md` §1 satır 40 bu dali **"Katalog kavramları (şarkı · albüm · medya)"**
sorumluluğuna yazar (`music · media · library` üçlüsü). Basitçe: bir içeriğe **nasıl
erişildiğinin** anlamı — `stream` mi, `download` mı, hangi kullanıcı, ne zaman —
"erişim" kelimesinin tek anlamı burada doğar.

`media/index.md` satır 13: v2'de rol tanımı **yok** → §1 bir tasarım yorumudur.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Erişim türü kavramı (`accessType`: stream · download · preview) | Dosyanın kodlanması, bitrate, codec | K014-media · K004-dsp (master §2.1) |
| 2 | Erişimin kiminle ilgili olduğu (`mediaId` + `userId`) | Kimlik/rol tanımı | K0100-identitiy (master §2.1) |
| 3 | "erişildi" olayının kurala dönüşü | Olmanın fiziksel üretilmesi/ses yolu | K003-audio (master §2.1 — ⚠️ SINIR AÇIK) |
| 4 | Medya kavramının katalogdan **ayrı** tanımı | Şarkı/albüm kimliği | music (master §1/1) |
| 5 | Yerel dosya ↔ katalog içeriği ayrımı kavramı | Yerel dosyanın taranması/indexlenmesi | K007-data (bagimliliklar §4/1) |
| — | *(dışında)* medya kitaplığı/saklama şeması | — | K007-data (master §2.1) |

**Sınır notu:** satır 3 `domain-bagimliliklar.md` §4 satır 49'da `"ne çalınır" listesi
paylaşılır · "nasıl çalınır" motoru → ⚠️ SINIR AÇIK` diye yazılıdır; bu tablo o açık
sınırı **tekrarlar**, kapatmaz.

## §3 DDD model notu (bu dal için)

| # | Kavram | Bu dalda değeri | Kanıt durumu |
|---|---|---|---|
| 1 | Bounded Context (aday) | Erişim/medya modeli: media · accessType · userId | ⚠️ ADR-097 K4 bekliyor |
| 2 | Aggregate (aday) | `MediaAccess` (bir erişim kaydı) — **sınır çizilmedi** | ⚠️ TASARIM İDDİASI |
| 3 | Entity | `mediaId` + olayın kimliği; erişim kaydı tektir | ✅ repo: `MediaAccessedEvent.php` satır 27 |
| 4 | Value Object (aday) | `accessType` — **sabit küme**: `stream` · `download` (dokümante) | ✅ repo: satır 29, 80 (yorum: "stream, download, etc.") |
| 5 | Ubiquitous language | erişim = `access` · akış = `stream` · indirme = `download` · yerel = `is_local` | ✅ repo satır 39 · Spotify `is_local` |
| 6 | Domain event | `media.accessed` (`mediaId · userId · accessType`) | ✅ repo: satır 27–29, 39 |
| 7 | Invariant | **tanımsız** (ör. "download kuralı kimin izni?") | ⚠️ VERIFICATION REQUIRED |

**Yasak:** `accessType` kümesinin tamamı ve izin invariant'ı bu dosyada **uydurulmaz** —
`etc.` ifadesi (satır 80) kapanmamış bir açıklık işaretidir.

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: `media` = v2 §2.8 satır 299 | `domain-master.md` §3 satır 75 | ✅ dosya okundu |
| 2 | Sorumluluk #1 = katalog kavramları → music · media · library | `domain-master.md` §1 satır 40 | ✅ dosya okundu |
| 3 | "Rol tanımı v2'de yok → ⚠️" | `media/index.md` satır 13 | ✅ dosya okundu |
| 4 | Domain event `media.accessed` gerçek (`mediaId · userId · accessType`) | `shared/src/Events/Domain/MediaAccessedEvent.php` satır 27–29, 39 | ✅ shell ile okundu |
| 5 | `accessType` dokümanı "stream, download, etc." der — küme açık | aynı dosya satır 80 (getter yorumu) | ✅ shell ile okundu |
| 6 | `payload()` `accessType` anahtarını yayınlar | aynı dosya satır 58 | ✅ shell ile okundu |
| 7 | K008 ↔ K003/K015 sınırı "ne çalınır" / "nasıl çalınır" ayrımı — **AÇIK** | `domain-bagimliliklar.md` §4 satır 49 | ⚠️ SINIR AÇIK |
| 8 | Medya kavramının resmi kapsam tanımı | — | **UNKNOWN** (ADR-097 K4) |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | `accessType` kümesinin tamamı nedir? ("etc." kapanmamış) | ⚠️ VERIFICATION REQUIRED — bu kayıtta açıldı |
| R2 | media ↔ music sınırı: erişim, şarkının özelliği mi ayrı varlık mı? | ⚠️ VERIFICATION REQUIRED |
| R3 | Download izni kuralı (lisans/DRM) kime ait? K013 mü, K020-licence mı? | ⚠️ VERIFICATION REQUIRED |
| R4 | media erişim kaydının saklanması (tablo/olay deposu) | **UNKNOWN** — K007/K011 kanıtı yok |
| R5 | Kodlama/codec kuralının sahibi (K015 mi K004 mü?) | **UNKNOWN** (master §2.1 ⚠️ SINIR AÇIK) |

**Kapanış:** status: draft kalır. §3/7 ve §4/8 kapanmadan bu dosyada kapsam iddiası
**kesin** sayılmaz (`domain-dogrulama.md` §5 satır 67).

---

## §6 ŞEMA KANITLARI: ERİŞİM VE MEDYA ÇEKİRDEĞİ

> **Kaynak:** `.ai/.sql/mysql/coremusic_media.sql` (172 satır · 2026-10-12
> envanterinde okundu). Bu bölüm **kilitli kapsam değildir** — §3 yasağı gereği
> yalnız kanıt sunar; invariant/kural iddiası §7'de açıkça öneridir.

### §6.1 Çekirdek tablo envanteri (coremusic_media.sql)

| Tablo | Satır | Rol | Kanıt notu |
|---|---|---|---|
| `media_access` | s.115–130 | Dosya erişim izni (paylaşım/işbirliği) | `access_type` enum 4 değer (s.119) + `expires_at` (s.122) |
| `media_audit` | s.132–148 | Dosya işlem denetim izi | `action` enum 7 değer (s.135) + `file_key` (s.136) |
| `media_metadata` | s.150–170 | Dosya meta verisi | 2 UNIQUE (s.166 file_key · s.167 music_id) + `checksum_sha256` (s.159) |
| `device_*` (5 tablo) | s.18–113 | Cihaz/senkron | device-management dalının (§11) — bu dalda değil |

### §6.2 `accessType` ÜÇ KAYNAKLI ÇELİŞKİ (bu turun en önemli bulgusu)

| Kaynak | Satır | Değer kümesi |
|---|---|---|
| `MediaAccessedEvent.php` getter yorumu | s.80 | "stream, download, etc." (**açık küme — §3/5 yasağı**) |
| `media_access.access_type` (şema) | s.119 | `read` · `write` · `delete` · `admin` (kapalı 4'lü) |
| `media_audit.action` (şema) | s.135 | `upload` · `download` · `delete` · `play` · `share` · `move` · `copy` (kapalı 7'li) |

**Okuma:** üç kaynak üç farklı dil konuşur — olay "stream/download", erişim tablosu
"read/write/delete/admin", denetim tablosu "download/play/share". `download`
her üçünde de geçer; `stream` **yalnız olayda**, `play` **yalnız denetimde**
(s.135). Bu üçlemenin eşlemesi **dosyalanmamıştır** → §12 R-M6'ya işlenir.
§3 yasağı gereği bu eşleme burada **uydurulmaz**.

### §6.3 `media_metadata` tekillik ve bütünlük kanıtları

| İddia | Kanıt satırı | Durum |
|---|---|---|
| Bir `file_key` tek kayıt | s.166 `uk_media_metadata_file_key` | ✅ |
| Bir `music_id` tek dosya | s.167 `uk_media_metadata_music_id` | ✅ |
| Bütünlük özeti var | s.159 `checksum_sha256` (varchar 64, nullable) | ✅ kolon var; içerik doğrulama kuralı yazılmadı |
| MIME biçimi kayıtlı | s.158 `mime_type` (nullable) | ✅ |
| Yumuşak silme ikilisi | s.163–164 `is_deleted` + `deleted_at` | ✅ music M-P9 ile aynı desen |
| `file_size` zorunlu | s.157 `NOT NULL` | ✅ |

### §6.4 `media_access` süre / yetki kanıtları

| İddia | Kanıt satırı | Durum |
|---|---|---|
| İzin süreli olabilir | s.122 `expires_at` (nullable) | ✅ NULL = süresiz — **yorum yasağı: süresizlik kuralı yazılmadı** |
| İznin sahibi kayıtlı | s.120 `granted_by` (nullable) + s.121 `granted_at` | ✅ |
| Süre indeksi | s.129 `idx_media_access_expires_at` | ✅ |
| FK **YOK** (user_id / file_key) | s.126–127 yalnız KEY, CONSTRAINT yok (dosya s.115–130'da FK satırı bulunmaz) | ✅ parçalanmışlık kanıtlı — bütünlük uygulamada |

### §6.5 Komşu şema notu: `media_catalog.sql` (medya arşivi — ayrı veritabanı)

| İddia | Kanıt satırı | Durum |
|---|---|---|
| Ayrı bir arşiv şeması vardır | `media_catalog.sql` s.1–27 başlık yorumu ("MEDYA ARŞİVİ — MySQL türetilmiş indeks") | ✅ okundu |
| SSOT JSON'tur, DB türetilmiştir | s.10–14 (`media.schema.json` · `taxonomy.json`; "DB çıksa arşiv yaşar") | ✅ |
| ULID kimliği kullanır (binary(16) değil) | s.50 `CHAR(26)` "ULID (Crockford base32)" | ✅ |
| Türkçe kolon dili | s.52 `sanatci` · s.53 `tip` enum · s.55 `durum` enum | ✅ |
| DDL doğrulanmamış | s.25–26 `⚠️ VERIFICATION REQUIRED: MySQL bu makinede yok` | ⚠️ kendi beyanı |

> **Sınır notu:** `media_catalog` bu dalın **komşusudur**, kanıtı değildir —
> kimlik dili (ULID) ile `coremusic_media` (binary(16) UUID) **farklıdır**;
> eşleme §12 R-M8'de açık sorudur.

## §7 INVARIANT ADAYLARI (SQL KANITLI — KİLİTLENMEMİŞ ÖNERİ)

> **§3 yasağı gereği:** bu tablo **kilitli invariant listesi değildir**; her satır
> kanıtı ile **öneri**yi ayrı sütunda tutar. §2/§3'teki ⚠️ satırları ve
> `status: draft` bu eklemle **kapanmaz** — kapanış ADR-097 K4'e bağlıdır.

| # | SQL kanıtı (satır) | Ne garanti eder | Invariant adayı (kanıt DEĞİL) |
|---|---|---|---|
| A-M1 | s.166 UNIQUE (file_key) | Aynı dosya anahtarı tek meta satırı | Tek dosya iki kez kayıt edilemez |
| A-M2 | s.167 UNIQUE (music_id) | Bir parça tek dosyaya bağlı | Parça↔dosya birebir; ikinci dosya ret |
| A-M3 | s.157 `file_size NOT NULL` | Boyutsuz meta satırı yaratılamaz | Boyu bilinmeyen dosya kaydı geçersiz |
| A-M4 | s.119 `access_type` enum 4 değer | Kapalı küme | Geçersiz izin türü yazılamaz (VO §8) |
| A-M5 | s.135 `action` enum 7 değer | Kapalı küme | Denetimde geçersiz işlem yazılamaz |
| A-M6 | s.122 `expires_at` NULL | Süresiz izin mümkün | Süresizlik yalnız NULL ile ifade edilir — "sonsuz timestamp" yasak (öneri) |
| A-M7 | s.117 `user_id NOT NULL` + s.152 (audit) `user_id NOT NULL` | Sahipsiz izin/satır yok | Sahipsiz erişim kaydı yaratılamaz |
| A-M8 | s.126–127 FK **YOK** (media_access) | DB erişim bütünlüğü zorlamaz | Bütünlük uygulama katmanında doğrulanmalı — DB güvencesi **yok** |
| A-M9 | s.163–164 + s.48 yumuşak silme ikilisi | İki tabloda aynı desen | Silme yalnız ikisi birlikte set edilmeli (music M-P9 ile aynı kural) |
| A-M10 | s.159 `checksum_sha256` (nullable) | Bütünlük özeti opsiyonel | Checksum varsa 64 karakter onaltılık; doğrulama akışı tanımsız ⚠️ |

### §7.1 Uyumsuzluk / açık noktalar (kanıt + öneri ayrımı)

| İddia | Kanıt | Durum |
|---|---|---|
| `accessType` üç dilli (§6.2) | olay s.80 ↔ s.119 ↔ s.135 | ⚠️ EŞLEME YAZILMADI — §12 R-M6 |
| `media_access` FK'sız | s.115–130 CONSTRAINT satırı yok | ⚠️ DB güvencesi YOK — A-M8 |
| `stream` erişim tipi şemada yok | s.119'da `stream` geçmiyor; olay s.80'de var | ⚠️ olay↔şema dil farkı — R-M6 |
| `play` izni ayrı mı? | s.135'te `play` var; s.119'da yok | ⚠️ izin↔denetim ayrımı — R-M6 |

## §8 VALUE OBJECT ADAYLARI (ÖNERİ — DEPODA VO YOK)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda medya VO sınıfı yoktur; adaylar
> yalnız şema kolonlarından türetilmiştir (`domain-master.md` §25 kuralı).

| # | VO | Dayanak kolon | Değer kümesi / biçim | Önerilen davranış |
|---|---|---|---|---|
| V-A1 | `AccessType` | s.119 enum(4) | read/write/delete/admin | `from(string)` fabrika — geçersizde ret |
| V-A2 | `AuditAction` | s.135 enum(7) | upload/download/delete/play/share/move/copy | `from(string)` + `isReadLike()` (download/play) |
| V-A3 | `FileKey` | s.118/s.136/s.153 varchar(255) | boştur olamaz, 255 üst sınır | Boş/whitespace ret; 255 üstü ret |
| V-A4 | `ChecksumSha256` | s.159 varchar(64) | 64 onaltılık karakter | Biçim doğrulama — `null` = bilinmiyor (**≠** geçersiz) |
| V-A5 | `FileSizeBytes` | s.157 bigint NOT NULL | ≥0 tam sayı (bayt) | Negatif ret; 0 bayt=? kuralı tanımsız ⚠️ |
| V-A6 | `MimeType` | s.158 varchar(100) | `type/subtype` biçimi | Biçim doğrulama — `null` opsiyonel |
| V-A7 | `GrantExpiry` | s.122 timestamp NULL | NULL = süresiz · zaman = an | Süresizlik yalnız NULL (A-M6) |
| V-A8 | `FileExtension` | s.156 varchar(10) | nokta-sız uzantı | Nokta başlangıçlı girdi ret (öneri) |

### §8.1 VO test edilebilirlik notları

| VO | Karakteristik test fikri (öneri) |
|---|---|
| V-A1 | 5. değer `from()` çağrısında ret |
| V-A3 | 256 karakterli key ret; boş string ret |
| V-A4 | 63 karakterli hash ret; `null` ayrı yol |
| V-A7 | geçmiş zamanlı `expires_at` → süresi dolmuş izin (akış §20.2) |

## §9 DOMAIN OLAY ENVANTERİ

### §9.1 Var olan olaylar (dosya + satır kanıtlı)

| Olay | Dosya kanıtı | `eventName` | Payload (satır kanıtlı) |
|---|---|---|---|
| `MediaAccessedEvent` | `shared/src/Events/Domain/MediaAccessedEvent.php` s.27–29 (property) · s.39 · s.58 | `media.accessed` (s.39 ⚠️ ad doğrulanmadı — §12 R-M9) | mediaId · userId · accessType (+ timestamp, s.58 payload) |

> **Sahiplik notu:** olay bu dalda tanımlıdır; **yayıncısı** (dispatch eden kod)
> bu turda taranmadı → playback D-PL7'dekiyle aynı ⚠️ sınıfındadır.

### §9.2 Eksik olay adayları (öneri — uydurma YASAK)

| # | Önerilen olay | Tetikleyici kanıtı | Mevcut durum |
|---|---|---|---|
| E-A1 | `media.access_granted` | s.120–121 `granted_by`/`granted_at` | ❌ DOSYA YOK (`Events/Domain/` 10 dosyada yok) |
| E-A2 | `media.access_expired` | s.122 `expires_at` + s.129 indeks | ❌ DOSYA YOK |
| E-A3 | `media.audit_recorded` | s.132–148 `media_audit` tablosu | ❌ DOSYA YOK |
| E-A4 | `media.metadata_registered` | s.150–170 tekil kayıt | ❌ DOSYA YOK |
| E-A5 | `media.checksum_mismatch` | s.159 `checksum_sha256` doğrulama | ❌ DOSYA YOK |

> **Kural:** E-A1…E-A5, `domain-bilesenler.md` §12 protokolüne tabidir —
> olay ancak dosya + `class` + `eventName` satır kanıtıyla envantere girer.

## §10 REPOSITORY SÖZLEŞMESİ ÖNERİSİ

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda medya repository arayüzü
> kanıtlanmamıştır. Dönüş dili `IUserRepository` ile hizalıdır (`?array`/`array`/`void`/`bool`).

| # | Yöntem (öneri) | Dönüş | Bağlı invariant / kanıt |
|---|---|---|---|
| R-A1 | `findMetadataByFileKey(string $key): ?array` | kayıt veya null | A-M1 (s.166) |
| R-A2 | `findMetadataByMusicId(string $musicId): ?array` | kayıt veya null | A-M2 (s.167) |
| R-A3 | `grant(string $userId, string $fileKey, string $type, ?string $expires): array` | izin | A-M4, A-M6, A-M8 (FK yok → uygulama doğrular) |
| R-A4 | `revoke(string $userId, string $fileKey): void` | — | A-M7 |
| R-A5 | `findActiveGrant(string $userId, string $fileKey): ?array` | izin veya null | A-M6 (süre kontrolü) |
| R-A6 | `recordAudit(string $userId, string $action, string $fileKey): void` | — | A-M5 (s.135) |
| R-A7 | `softDeleteMetadata(string $id): void` | — | A-M9 |
| R-A8 | `verifyChecksum(string $fileKey, string $sha256): bool` | evet/hayır | A-M10, V-A4 |

### §10.1 Sözleşme tipi notu

`file_key` (s.118) bu dalın ana erişim anahtarıdır — `music_id` ise music'e
bağlayıcıdır (A-M2). R-A3'ün `grant`'ı `MediaAccessedEvent`'i **tetiklemez**
(olay *erişim anını* taşır, izni değil) — bu ayrım §20.3 akışında açılır.

## §11 KOMŞU SINIR DETAYI (§2 GENİŞLETMESİ)

| Komşu | Yön | Alan/kanıt | Ayrım kuralı |
|---|---|---|---|
| music | media ← music | `music_id` FK (s.152) + A-M2 birebir | Dosya tanımı burada, parça tanımı music'te |
| device-management | media ← device | `device_tracks.file_format` (coremusic_media.sql s.57) | Cihaz-senkron dosyası orada; dosya meta verisi burada (§6.1 notu) |
| K003/K008 siniri (bagimliliklar §4/49) | media ↔ playback | "ne çalınır / nasıl çalınır" — **AÇIK** | Bu tablo sınırı **tekrarlar**, kapatmaz |
| K020-licence | media → dış | Download izni (lisans/DRM) — §5 R3 | Lisans dili bu dalda **tanımsız** |
| media_catalog (arşiv) | media ↔ arşiv | ULID (s.50) ↔ binary(16) (s.116) kimlik dili farkı | Eşleme §12 R-M8 — kapatılmadı |
| K007-data | media → data | Şemanın normalizasyonu | Bu dal şema **üretmez**, yalnız kanıtlar |
| library | media ← library | `user_downloads.file_format` (coremusic_user.sql s.42) | Koleksiyon kaydı orada, dosya meta verisi burada |

## §12 AÇIK SORULAR (§5 GENİŞLETME — R-M6…R-M10)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-M6 | `accessType` üç dili (olay s.80 ↔ s.119 ↔ s.135) nasıl eşlenir? | §6.2 çatışma tablosu | Tek eşleme tablosu — ADR |
| R-M7 | `stream` izni şemada neden yok (olayda var)? | s.119 vs s.80 | `media_access` enum'unun genişletilmesi mi, eşleme mi? |
| R-M8 | `media_catalog` (ULID) ↔ `coremusic_media` (UUID) kimlik eşlemesi | §6.5 s.50 vs s.116 | Taşıma/ köprü tasarımı |
| R-M9 | `MediaAccessedEvent`'in `eventName()` tam değeri | §9.1 ⚠️ (payload s.58 okundu, ad satırı doğrulanmadı) | Tek dosya okuması ile kapanır |
| R-M10 | `media_access` FK'sız — bütünlük kimi sorumluluğunda? | A-M8 (s.115–130 FK yok) | Uygulama doğrulama katmanı kararı |

> **§5'teki R1–R5 aynen korunur** (önceki tur kaydı); bu tur yalnız R-M6…R-M10
> ekler. `status: draft` ve §3 yasağı **değişmez**.

## §13 KARAKTERİSTİK TEST SENARYOLARI (ÖNERİ)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Senaryo taslağıdır; yazılmamıştır
> (`qa-engineer`/`test-writer` kapsamı).

| # | Senaryo | Beklenen | Bağlı kural |
|---|---|---|---|
| T-A1 | Aynı `file_key` ile ikinci meta kaydı | DB ret | A-M1 (s.166) |
| T-A2 | Aynı `music_id` ile ikinci dosya | DB ret | A-M2 (s.167) |
| T-A3 | `file_size` olmadan meta kaydı | DB ret | A-M3 (s.157) |
| T-A4 | Geçersiz `access_type` değeri | VO ret / DB ret | A-M4, V-A1 |
| T-A5 | Geçersiz `action` değeri (audit) | VO ret / DB ret | A-M5, V-A2 |
| T-A6 | Süresi dolmuş izinle erişim denemesi | ret (akış §20.2) | A-M6, V-A7 |
| T-A7 | Sahipsiz izin (`user_id` boş) | ret | A-M7 |
| T-A8 | Checksum uyuşmazlığı | `media.checksum_mismatch` adayı tetiklenir (dosya YOK → ⚠️) | A-M10 |
| T-A9 | Soft delete iki alan birlikte | `is_deleted=1` + `deleted_at` dolu | A-M9 |
| T-A10 | `media.accessed` yayınlayınca payload | 3 anahtar + timestamp | §9.1 |

## §14 BC ETKİLEŞİM MATRİSİ (YÖN + NİYET)

> Yön ve niyet §2/§11'den türetilmiştir; **çağrı mekanizması** iddiası taşımaz.

| # | Çağıran → Çağrılan | Niyet | Bağlı kural |
|---|---|---|---|
| A-BC1 | media → music | `music_id` geçerliliği | A-M2 |
| A-BC2 | library → media | İndirilen dosya meta verisi | §11 |
| A-BC3 | playback → media | "nasıl çalınır" (codec/bitrate) — **AÇIK sınır** | bagimliliklar §4/49 |
| A-BC4 | device-management → media | Cihaz-senkron dosya biçimi | §6.1 notu |
| A-BC5 | media → K020-licence | Download izni (lisans) | §5 R3 |
| A-BC6 | media → media_catalog | Arşiv eşlemesi (ULID↔UUID) | R-M8 |
| A-BC7 | K0100-identitiy → media | `userId` kimliği (erişim sahibi) | §2/2 |

## §15 ROL VE SORUMLULUK MATRİSİ (ÖNERİ)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda rollerin dağılımına dair kanıt yoktur.

| Karar alanı | Önerilen sahip | Neden | Ataç |
|---|---|---|---|
| `accessType` eşleme tablosu (3 dil) | media domain sahibi + `architect` | §6.2 çelişkisi | R-M6 |
| İzin invariant'ı (süre/sahip) | media domain sahibi | A-M6/A-M7 | §7 |
| Denetim (`media_audit`) yazımı | security katmanı | s.132–148 denetim izi | §6.1 |
| FK'sızlık (A-M8) çözümü | `data-engineer` + media sahibi | Bütünlük kimi sorumlu? | R-M10 |
| media_catalog eşlemesi | `legacy-modernizer` | ULID↔UUID dili farkı | R-M8 |
| Olay envanteri genişlemesi | `domain-bilesenler.md` protokolü | Uydurma olay yasağı | §9.2 |

## §16 ÖRNEK ANLATIM (TUTARLILIK OKUMASI)

Bir dosya kataloğa girdiğinde zincir şöyledir: `media_metadata` kaydı `music_id`
ile birebir bağlanır (A-M2, s.167) ve `file_size` olmadan yaratılamaz (A-M3,
s.157); dosya anahtarı tektir (A-M1, s.166). Paylaşım izni ayrı bir
`media_access` satırıdır: iznin sahibi ve vereni kayıtlıdır (A-M7, s.117/s.120),
izin türü kapalı dörtlüdedir (A-M4, s.119) ve süreli olabilir (A-M6, s.122 —
`NULL` = süresiz). Dikkat: `media_access`'te FK **yoktur** (A-M8), yani bozuk
bir `user_id`/`file_key` DB'de **düşmez** — doğrulama uygulamaya düşer. Her
dosya işlemi `media_audit`'a 7'li kapalı kümede düşer (A-M5, s.135). Olay
katmanında ise tek kanıtlı olay `media.accessed` tir (§9.1) ve onun `accessType`
dili (s.80 "stream, download, etc.") şema diliyle (s.119) **birebir örtüşmez** —
zincirin en zayıf halkası burasıdır (R-M6). `media_catalog` arşivi ayrı bir
dil konuşur (ULID, Türkçe kolon — §6.5) ve bu dalın kanıtı değil, komşusudur.

## §17 OKUMA YOLU VE KOMŞU DOSYA DAVET LİSTESİ

### §17.1 Bu dosyadan okuma sırası

| Amac | Sıra |
|---|---|
| Erişim dilini anlamak | §1 → §2 → §6.2 (çelişki) |
| Şema kanıtlarını görmek | §6.1 → §6.3 → §6.4 |
| Invariant/VO ayrımı | §7 → §7.1 → §8 (öneri etiketi) |
| Olay envanteri | §9.1 (kanıt) → §9.2 (`❌`) → `domain-bilesenler.md` §12 |
| Kapanış | §12 (R1–R10) → §22 (ölçüm) |

### §17.2 Çağrılan komşu dosyalar

| Dosya | Neden |
|---|---|
| `domain-master.md` §24/§25 | Invariant/VO üretim kuralı |
| `domain-bilesenler.md` §7–§12 | Olay protokolü |
| `domain-bagimliliklar.md` §4/49 | K003/K015 sınır — **AÇIK** |
| `domain-web-arastirma.md` §18.2 | Tek URL kaynağı (C4) |
| `playback-kapsam.md` §11 | "nasıl çalınır" sınırı |
| `media/index.md` satır 13 | v2'de rol tanımı yok kanıtı |

## §18 OKUYUCU UYARILARI VE ÖLÇÜM YÖNTEMİ

### §18.1 Sınırlar

1. **Bu dosya kapsam + kanıt dosyasıdır, kod değildir.** §8 VO'ları ve §10
   repository sözleşmesi depoda yoktur — okuyucu "var" sanıp çağıramaz.
2. **URL aramayın.** C4 gereği web kanıtı `domain-web-arastirma.md` §18.2'dedir.
3. **§13 testleri yazılmamıştır** — taslaktır (`qa-engineer` kapsamı).
4. **`❌ DOSYA YOK` satırları kasıtlı kayıttır** — uydurma olay eklemek §9.2
   protokolünü ihlal eder.
5. **`status: draft` bu turda korunmuştur** (§3 yasağı + ADR-097 K4 beklentisi).

### §18.2 Ölçüm yöntemi

| Soru | Cevap |
|---|---|
| Satır nasıl sayılır? | `[System.IO.File]::ReadAllLines(yol).Length` |
| Tahmin var mı? | Hayır — §22 tablosu araç ölçümüdür; tahmin doldurma yasak |
| Mojibake nasıl taranır? | U+4E00–U+9FFF aralığı tüm dosyada taranır; isabet 0 beklenir |
| URL ihlali nasıl taranır? | `https?://` takibi; tek başına `http` desen açıklamasıdır, sayılmaz |
| Authority nasıl korunur? | 2026-10-12 satırı aynen kalır, 2026-10-17 satırı eklenir |

## §19 KARAR MATRİSİ (BU DOSYA ÖLÇÜSÜNDE)

| # | Karar | Seçilen | Reddedilen | Neden |
|---|---|---|---|---|
| D-A1 | Bu turda kapsam iddiası | Kanıt sunma + öneri etiketleme | Invariant/kural seti kilitleme | §3 yasağı + ADR-097 K4 |
| D-A2 | `accessType` çelişkisi | Üç kaynaklı çelişki kaydı (§6.2) + açık soru (R-M6) | Tek dili "gerçek" saymak | Üç farklı satır kanıtı var, eşleme yok |
| D-A3 | `media_catalog` | Komşu olarak dosyalama (§6.5) | Bu dala dahil saymak | Ayrı şema + farklı kimlik dili |
| D-A4 | FK'sızlık | Uygulama doğrulama gerekliliği kaydı (A-M8) | DB'de FK var varsaymak | s.115–130'da CONSTRAINT yok |
| D-A5 | `stream` vs `play` | Açık soru (R-M7) | Eşleme uydurmak | Zero-hallucination |
| D-A6 | Olay envanteri | 1 kanıtlı + 5 `❌ DOSYA YOK` | Uydurma olay yazmak | §9.2 protokolü |

## §20 AKIŞ SENARYOLARI (TASARIM ÖNERİSİ — kanıt DEĞİL)

> Senaryolar §7 invariant adaylarından türetilmiştir; implementasyon yoktur.

### §20.1 Dosya kaydı

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | K012-services | Dosya kaydı dili media'dan gelir | §2/3 |
| 2 | media | `file_size` yoksa ret | A-M3 |
| 3 | media | Aynı `file_key` tekrarı ret | A-M1 |
| 4 | media | `music_id` birebir — ikinci dosya ret | A-M2 |

### §20.2 İzin verme ve kullanma

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | media | İzin türü kapalı dörtlüyü aşarsa ret | A-M4, V-A1 |
| 2 | media | `expires_at` geçmişse izin kullanılamaz | A-M6, V-A7 |
| 3 | media | `media.accessed` yayılır — payload 3 anahtar | §9.1 |
| 4 | audit | İşlem `media_audit`'a 7'li kümede düşer | A-M5 |

### §20.3 İzin ↔ olay ayrımı

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | media | İzin **verme** olayı tetiklemez (dosya YOK — E-A1) | §9.2 |
| 2 | media | Olay yalnız **erişim anında** yayılır | §10.1 |

## §21 SONRAKİ TUR İŞ KALEMLERİ

| # | İş | Bağlı soru/borç | Çıktı |
|---|---|---|---|
| J-A1 | `MediaAccessedEvent` eventName satırı okuması | R-M9 | Ad doğrulanır |
| J-A2 | `accessType` eşleme tasarımı | R-M6, R-M7 | Tek eşleme tablosu — ADR |
| J-A3 | `media_access` FK kararı | R-M10, A-M8 | Uygulama doğrulama katmanı |
| J-A4 | media_catalog eşleme taraması | R-M8 | ULID↔UUID köprüsü |
| J-A5 | `domain-dogrulama.md` tur sonu raporu | §22 | Ölçüm (a–d) |

> **Kapanış bu tur:** §1–§5 aynen korundu · §3 yasağı işlemede ·
> `status: draft` · hiçbir ⚠️/UNKNOWN kapatılmadı · Authority 2 satır.

## §22 ERİŞİM MODELİ OLGUNLUK BANDI (bu dosya ölçeğinde)

| Katman | Durum | Kanıt | Not |
|---|---|---|---|
| Şema (erişim + denetim + meta) | ✅ VAR | coremusic_media.sql s.115–170 | 3 tablo · 2 UNIQUE · 1 enum 4'lü · 1 enum 7'li |
| Domain olay | ✅ VAR (1) | MediaAccessedEvent.php s.27–29, 39 | Yayıncısı ⚠️ taranmadı |
| VO sınıfı | ❌ YOK | depo taraması (§8 başlığı) | Adaylar yalnız şemadan türetildi |
| Repository arayüzü | ❌ YOK | `domain-bilesenler.md` §10 taraması | §10 yalnız öneri |
| Invariant listesi (kilitli) | ❌ YOK | ADR-097 K4 beklemede | §7 kilitlenmemiş |
| İzin↔denetim eşlemesi | ❌ YOK | §6.2 üç dilli çelişki | R-M6 |
| Test | ❌ YOK | §13 taslak | `qa-engineer` kapsamı |

**Okuma:** erişim modeli **şema-öncelikli** doğmuştur — dil (olay) ve kural
(invariant) katmanları şemanın gerisindedir. Bu, music dalının olgunluk
profilinden (§22 music) **farklı değildir**; K008 çocuklarının ortak tipidir.

## §23 RİSK VE TEKNİK BORÇ KAYDI

| # | Risk / borç | Kanıt | Ağırlık | Ataç |
|---|---|---|---|---|
| B-A1 | `accessType` üç dilli çelişki — eşleme yok | §6.2 | YÜKSEK — olay↔şema uyumsuzluğu veri kaybı doğurabilir | R-M6 |
| B-A2 | `media_access` FK'sız — bozuk referans DB'de düşmez | A-M8 (s.115–130) | YÜKSEK — sessiz bozulma | R-M10 |
| B-A3 | Checksum doğrulama akışı tanımsız | A-M10 (s.159 nullable) | ORTA — bütünlük güvencesi yok | §9.2 E-A5 |
| B-A4 | `media_catalog` kimlik dili farkı (ULID↔UUID) | §6.5 s.50 vs s.116 | ORTA — eşleme köprüsü yok | R-M8 |
| B-A5 | `stream` izni şemada yok | R-M7 (s.119 vs s.80) | ORTA — dil kaybı | R-M7 |
| B-A6 | İzin süresi dolumu için olay yok | E-A2 `❌ DOSYA YOK` | DÜŞÜK — zamanlanmış kontrol tanımsız | §9.2 |

**Kural:** bu tablodaki satırlar **silinmez**; kapanış yalnız kanıt eklenerek
yapılır (`domain-dogrulama.md` §9 defterine işlenir).

## §24 TUR SONU ÖLÇÜM (2026-10-17)

| Metrik | Değer | Yöntem |
|---|---|---|
| Ekleme öncesi satır | 86 | `[System.IO.File]::ReadAllLines().Length` |
| Eklenen bölüm | §6–§24 (19 bölüm) | append-only; §1–§5 + Authority 2026-10-12 korundu |
| Invariant adayı | 10 (A-M1…A-M10) | hepsi şema satır kanıtlı; kilitlenmemiş |
| Uyumsuzluk kaydı | 4 (§7.1) | hepsi ⚠️ — 3 dilli dil / FK / stream / play |
| VO adayı | 8 (V-A1…V-A8) | hepsi `TASARIM ÖNERİSİ — kanıt DEĞİL` etiketli |
| Olay kaydı | 1 kanıtlı + 5 `❌ DOSYA YOK` | dosya + satır kanıtı (§9) |
| Açık soru | 5 (§5) + 5 yeni (R-M6…R-M10) = 10 | §5 kayıtları aynen korundu |
| Test senaryosu | 10 (T-A1…T-A10) | taslak — yazılmadı |
| Kapanan ⚠️ | **0** | §3 yasağı gereği hiçbir ⚠️/UNKNOWN kapatılmadı |
| Yeni URL | 0 | C4 — tek URL kaynağı `domain-web-arastirma.md` §18.2 |
| Authority | eski (2026-10-12) korundu + yeni eklendi | master §22 M-6 |

## §25 UBİQUİTOUS LANGUAGE GENİŞLETME (kanıt satırlı sözlük)

> Bu tablo §3/5'teki 4 terimin (access · stream · download · is_local)
> **şema karşılıklarını** ekler; terim uydurulmaz, her satır kanıt satırı taşır.

| Terim (dal dili) | Şema/olay karşılığı | Kanıt satırı | Not |
|---|---|---|---|
| erişim | `media_access` | s.115 | tablo adı kendisi |
| erişim türü | `access_type` | s.119 | 4 değer — §6.2 çelişkisi |
| izin veren | `granted_by` | s.120 | nullable — sistem verdiyse NULL |
| izin anı | `granted_at` | s.121 | |
| izin sonu | `expires_at` | s.122 | NULL = süresiz (A-M6) |
| denetim | `media_audit` | s.132 | denetim izi |
| işlem | `action` | s.135 | 7 değer |
| dosya anahtarı | `file_key` | s.118 · s.136 · s.153 | üç tabloda aynı dil |
| dosya özeti | `checksum_sha256` | s.159 | nullable |
| medya meta verisi | `media_metadata` | s.150 | |
| parça bağlantısı | `music_id` | s.152 | birebir (A-M2) |
| arşiv | `media_catalog` | §6.5 | ayrı şema — komşu |
| akış (olay dili) | `stream` | s.80 (olay yorumu) | şemada **karşılığı yok** — R-M7 |
| indirme (olay dili) | `download` | s.80 | şemada `access_type` içinde yok, `action` içinde var (s.135) |

**Sınır:** bu sözlük **bu dalın** dilidir; `play` (s.135) ile `music.played`
olayının `play`i **aynı sözcük, farklı bağlam** — birleştirme kararı §12 R-M6'ya
bağlıdır ve bu dosyada yapılmaz.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Authority:** Bayram Ali / Vault Steward · K008-media-kapsam · 2026-10-17 · §6–§21 eklendi: şema kanıtları (coremusic_media.sql 3 tablo) · accessType üç dilli çelişki kaydı · A-M1…A-M10 invariant adayı (kilitlenmemiş) · V-A1…V-A8 VO · E-A1…E-A5 eksik olay · R-A1…R-A8 repository · R-M6…R-M10 açık soru · 10 test senaryosu · status: draft korundu
