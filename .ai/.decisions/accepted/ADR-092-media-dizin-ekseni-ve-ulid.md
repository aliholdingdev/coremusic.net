---
title: "CoreMusic — ADR-092: Medya Arşivi Dizin Ekseni ve ULID Kimliği"
type: "architecture-decision"
category: "infrastructure"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.1.2"
status: "accepted"
authority: "SSOT — media.coremusic.net medya ağacı yerleşimi: tek disk ekseni (sanatçı → albüm → parça), iki kök (audio/video), ULID kalıcı kimlik, yan-JSON meta, kontrolü taksonomi + serbest etiket"
kaynak: "Kullanıcı karar metni (2026-09-29) + disk kanıtı: ADR-039 L120, VISION.md L136, CLAUDE.md §2.1, C: sürücü ölçümü (2026-09-29), şablon .ai/.templates/adr/adr-template.md"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic — ADR-092: Medya Arşivi Dizin Ekseni ve ULID Kimliği (Media Archive Directory Axis & ULID Identity)

> **Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-09-29 · **Ağırlık:** 1 (varsayılan) · **İlgili ADR:** 039
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `media-dizin-ekseni-ve-ulid` · **Dosya:** `ADR-092-media-dizin-ekseni-ve-ulid.md`
> **İlgili kararlar:** [[ADR-039-7-service-platform-architecture]] (media.coremusic.net servis yerleşimi) · [[../../architecture/k15-medya-streaming/index.md]] (FFmpeg + metadata hattı) · [[../index.md]] · [[../../VISION.md]] · [[../../CLAUDE.md]]
> **Şablon:** `.ai/.templates/adr/adr-template.md` (Guardrail #16 — 10 alanlı frontmatter + §1-§7 iskelet, ADR-090/ADR-091 biçimiyle hizalı)
> **Numara gerekçesi:** `.ai/.decisions/accepted/` serisinde en yüksek numara **ADR-090** (`ADR-090-channel-variant-product-family.md`); ancak **ADR-091 zaten dolu** (`.ai/.decisions/ADR-091-template-engine-no-eval.md` + `log.md:629` kaydı + şablon `coremusic-vault-template.md:407` "yeni: ≥ ADR-091") → **ilk boş numara 092**. Frozen **001-037'ye dokunulmamıştır**; ayrı seri `architecture/adr/` (023-026) **karıştırılmamıştır**.

---

## §1 Bağlam (Context)

### §1.1 Bağlam Tetikleyicileri (disk kanıtı)

| # | Kaynak | Disk Kanıtı | Etiket |
|---|--------|-------------|--------|
| 1 | [[ADR-039-7-service-platform-architecture]] (satır 120) | `media` servisi: **media.coremusic.net:5000/6000**, PHP (+ FFmpeg → K15, K8 dışı), **dizin YOK**, **PLANNED** | IMPLEMENTED (karar) — yerleşim **bu ADR'dedir** |
| 2 | [[../../VISION.md]] §6 (madde 6, satır 136) | "**Merkezi Medya Yönetimi ve Streaming (`media.coremusic.net`)**: Çok kaynaklı otonom indirme (YouTube, YouTube Music, Deezer FLAC stüdyo kalitesi), kayıpsız format dönüştürme ve **merkezi ev/ofis medya arşivi**" | IMPLEMENTED (doküman) |
| 3 | [[../../CLAUDE.md]] §2.1 *SSOT Priority Order* (satır 45) | Vault hiyerarşisi: CLAUDE > AGENTS > WORKFLOW > brain > index | IMPLEMENTED (doküman) |
| 4 | [[../../architecture/k15-medya-streaming/index.md]] (K15 katmanı) | **✅ DISKTE VAR — 16 dosya** (git tracked; son commit `523497c`): `index.md` = "K15 Medya & Streaming Katmanı" (FFmpeg pipeline · codec · HLS/DASH · CDN), `media-metadata.md` = "Media Metadata Engine" (**ID3 / Vorbis / APE / iTunes oku-yaz**) | IMPLEMENTED (doküman) — FFmpeg + metadata hattının sahibi; **yerleşim/eksen bu ADR'nin** |

> **Dürüstlük notu (görev metni ↔ disk):** Görev metninde VISION §6 için *"Merkezi medya arşivi: Metadata + Kapak"* ifadesi kullanılıyor; **diskte birebir bu cümle YOKTUR** (VISION.md `:465`'te geçen "Metadata + telif izleme" başlıktır, alakasız). Diskte doğrulanan ifade satır 136'daki **"merkezi ev/ofis medya arşivi"**dir. Metadata + kapak şartı, bu ADR'nin §2.4/§2.6'daki **kendi kavramsallaştırmasıdır** — VISION'a atfedilmez.

### §1.2 Kaynak Koleksiyon (kullanıcı beyanı — tarama YAPILMADI)

| Alan | Değer | Etiket |
|------|-------|--------|
| Konum | `C:\Users\Bayram Ali\Music` | kullanıcı beyanı |
| Dosya sayısı / boyut | **7.551 dosya · 38,33 GB** | kullanıcı beyanı — **⚠️ VERIFICATION REQUIRED** (bu görevde dizin **taranmadı**: "MUSIC'E DOKUNMA" kısıtı; §7.2 kabul kriteriyle doğrulanır) |
| Format dağılımı | **%98,5 MP3** + flac / wav / m4a / ogg / wma | kullanıcı beyanı (aynı kısıt) |
| Kök klasörler (4) | MÜZİKLER **3.918** · Necati USB **1.924** · Ali Alınan Şarkılar 1: **1.029** · Ali Alınan Şarkılar 2: **677** (toplam 7.548 — 7.551 ile 3 fark kök-dışı dosyalar) | kullanıcı beyanı (aynı kısıt) |
| Sorunlar | **Bozuk Türkçe adlar** (mojibake/yanlış kodlama), **iç içe klasörler** | kullanıcı beyanı (aynı kısıt) |
| C: sürücü boşluğu | **74,7 GB boş** (Get-PSDrive ölçümü, 2026-09-29); görev metninde "69 GB" yazıyordu → **güncel ölçüm esastır** | ✅ DOĞRULANDI (disk) |

### §1.3 Kaynak Kanıtları (disk taraması — web araştırması YOK)

> **⚠️ Şablon sapması, bilinçli:** `.templates/adr/adr-template.md` §1.3'ü "Web'den Araştırma Raporu" olarak zorunlu kılar. Bu görevin kapsamı **yalnız 1 dosya yazmak** olup web araştırması **yapılmamıştır**; uydurmak yerine aynı konum **disk kanıtı tablosuyla** doldurulmuş, web araştırması sonraki revizyona **AÇIK KALEM** olarak bırakılmıştır (`updated` alanı revize edilir).

| İddia | Durum | Kanıt |
|-------|-------|-------|
| media.coremusic.net henüz kodlanmış değil | ✅ DOĞRULANDI | ADR-039 L120 = **PLANNED**, "dizin YOK" |
| Medya arşivi şartı vizyonda var | ✅ DOĞRULANDI | VISION.md `:136` |
| `k15-medya-streaming` katmanı mevcut | ✅ DOĞRULANDI — **16 dosya** | `git ls-files .ai/architecture/k15-medya-streaming/` → `index.md`, `media-metadata.md`, `ffmpeg-pipeline.md`, `hls-streaming.md`, `dash-streaming.md` vb. **(İlk glob `k15*` yalnız dosya adı eşlediği için BOŞ döndü — hatalı sonuç düzeltildi: `Test-Path` = True)** |
| 1M+ varlık hedefi | ⚠️ VERIFICATION REQUIRED | görev metni (kullanıcı beyanı) — vault'ta bağımsız kaynak aranmadı |
| Music koleksiyonu istatistikleri | ⚠️ VERIFICATION REQUIRED | §1.2 — tarama kısıt nedeniyle yapılmadı |

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| **TAŞIMA YOK** | Orijinal dosyalar `C:\Users\Bayram Ali\Music` içinde **yerinde kalır**; bu faz yalnız **iskelet + config + docs** üretir |
| **ADR-039 çekirdeği değişmez** | Port 5000/6000, PHP 8.4 + FFmpeg, "dizin YOK" kaydı ADR-039'un; bu ADR yalnız **yerleşim/dizin eksenini** verir |
| **Frozen 001-037 immutabel** | Yalnız atıf; düzeltme yapılmaz |
| **Kod 0 → PLANNED disiplini** | `media.coremusic.net` kodu/iskeleniz repoda yok (ADR-039 PLANNED) → uygulama adımları PLANNED etiketlidir; karar seviyesi IMPLEMENTED |
| **REDACTED** | Yol/credential/kişisel veri salt gerekli olduğu kadar yazılır; sır vault'a girmez |
| **Tek yazma kanalı** | Tüm vault yazımı `.ai/scripts/vault-utf8-writer.mjs`; `log.md` yalnız append |

---

## §2 Karar (Decision)

**media.coremusic.net medya arşivi, TEK disk ekseninde (sanatçı → albüm → parça), iki kökte (audio + video), kalıcı ULID kimliği ve yan-JSON meta ile kurulur; çok-eksen disk reddedilir, MySQL yalnız türetilmiş indekstir.**

### §2.1 Karar Maddeleri (8)

| # | Karar |
|---|-------|
| **1** | **Disk ekseni tek: sanatçı → albüm → parça.** Tür / kullanım / dönem / ruh hâli = **tag** (çok-eksen disk = **duplike + kaçak** üretir; bkz. §3 alternatif 1-2) |
| **2** | **İki kök:** `audio\` + `video\` (ses + video klip) |
| **3** | **Kalıcı kimlik = ULID**; **slug = takma ad** (rename indeksi kırmaz — kimlik slug'da değil ULID'dedir) |
| **4** | **Hiyerarşik meta:** `artist.json` → `album.json` → `meta.json`; **tekli/çeşitli** derlemeler **self-contained** (dış bağımlılık olmadan okunur) |
| **5** | **Format korunur, düz klasör,** `derived\` **YOK**; FFmpeg çıktısı = uygulama `cache\` (**medyanın DIŞI**) |
| **6** | **SSOT = yan JSON**; **MySQL = türetilmiş indeks** (`utf8mb4_tr_0900_ai_ci`), **yeniden üretilebilir** |
| **7** | **Kontrollü taksonomi** (`config\taxonomy.json`, **kapalı** set) + **serbest `etiket[]`** (**açık** set) |
| **8** | **Yaşam döngüsü:** `inbox` → `aktif` → `saklı` → `tekrar` → `arşiv` |

### §2.2 Yerleşim (fiziksel)

```
C:\www\coremusic.net\media.coremusic.net\     ← proje kökü (repo içinde, ADR-039 servisi)
├─ .gitignore          ← media/ hariç (medya baytı git'e girmez)
├─ media\              ← SADECE MEDYA
├─ config\  docs\      ← medya dışı
└─ [faz 2: public\ src\ bin\ cache\ reports\ catalog\]
```

**Kural:** `media\` içine **yalnız medya ağacı** girer; kod / config / docs **media dışında** kalır (§7.1 kabul kriteri 1).

**git kuralı:** `media/` .gitignore'da; repo yalnız kod/config/docs track eder (ADR-092 §6.1 kural 6 + §4.3 R2 ile uyumlu — C: izleme).

> **Doğrulama (2026-09-29):** `media.coremusic.net\.gitignore` diskte mevcut → `git check-ignore media.coremusic.net/media` → `media.coremusic.net/.gitignore:2:media/` ✓ (38 GB+ medya baytı repo'ya girmez). Kast edilen bağ: **§6.1 kural 6 + §4.3 R2** (C: <31 GB izleme).

### §2.3 Neden Bu Seçenek? (Rationale)

1. **Tek eksen = tek doğruluk:** Aynı şarkı birden çok türe/döneme ait olabilir; disk bunu **fiziksel kopya**yla çözer → 1M+ ölçekte depolama ve tutarsızlık patlar. Eksen tek, çok-eksenli sorgu **tag + indeks** ile çözülür (kayıp yok).
2. **ULID = yeniden adlandırmaya dayanıklı kimlik:** Slug değişirse dosya taşınmaz/bozulmaz; indeks ULID'ye bağlıdır. 1M+ varlıkta `rename` operasyonel bir gerçektir, istisna değil.
3. **Yan JSON SSOT = MySQL bağımsızlığı:** İndeks çökse/yeniden kurulsa bile arşiv yaşar; DB yalnız **türetilmiş** olduğundan `utf8mb4_tr_0900_ai_ci` ile yeniden üretilebilir (SSOT Priority: [[../../CLAUDE.md]] §2.1).
4. **derived\ YOK + cache medyanın dışı:** Arşiv = **orijinal + kapak + meta**; transcode üretilmiş çıktı olduğundan silinip yeniden üretilebilir — arşivde **üretim sonucu tutmak** hem alan hem de "orijinal mi?" belirsizliği getirir.

### §2.4 Teknik Detaylar (şema örnekleri — PLANNED)

```jsonc
// media\audio\<sanatçı>\artist.json
{ "ulid": "01J...", "slug": "sanatci-adi", "ad": "Sanatçı Adı",
  "etiket": [], "olusturma": "2026-09-29" }
```

```jsonc
// media\audio\<sanatçı>\<albüm>\album.json
{ "ulid": "01J...", "slug": "album-adi", "yil": 2001, "sanatci_ulid": "01J..." }
```

```jsonc
// media\audio\<sanatçı>\<albüm>\<parça>.mp3 + meta.json
{ "ulid": "01J...", "slug": "parca-adi", "format": "mp3", "suresi_sn": 213,
  "album_ulid": "01J...", "tur": ["pop"], "etiket": ["dugun"],
  "durum": "aktif", "sha256": "..." }
```

> **⚠️ VERIFICATION REQUIRED:** Yukarıdaki alan adları **taslaktır**; üretim öncesi `config/schema/*.json` ile sabitlenir ve §7.1 kabul kriteri 4'te audit edilir.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| **1** | **Genre-first (tür → sanatçı → şarkı)** | Tür gezinmesi yüzeyde kolay | Aynı şarkı **3 türe** ait olabilir; disktek sınıflandırma **tek** olabilir → duplike dosya + kural kaçakları (tag'ler silinirse eşleşme kaybolur) | **RET** — disk tek sınıflandırma yapar, çok-tür gerçeği **kopya/kaçak** üretir; tür araması tag + GUI ile kayıpsız karşılanır (§4.1) |
| **2** | **Etkinlik-first (Necati USB düzeni: düğün/halay/kına)** | Çalma-listesi/bölüm mantığı hazır | Kümeler **kesişir** (aynı parça 3 etkinlikte); sanatçı/albüm erişimi ikinci planda kalır | **RET** — etkinlik kesişen kümelerdir; disk eksenine girerse her kesişim yeni kopya demektir → **tag** (`etiket[]`) olarak taşınır |
| **3** | **Veri kökü `E:\` sürücüsü** | C: üzerinde alan baskısı yok | Kullanıcı **her şeyin `media.coremusic.net` içinde** olmasını istedi; ikinci disk = yedekleme/taşınma/izin karmaşası | **RET** — kullanıcı kararı; C: **74,7 GB boş** (§1.2), 38,33 GB sonrası ~**36,4 GB** kalır → **<31 GB eşiği** (§6 kuralı) aşılmadan önce disk genişletme incelemesi yapılır |
| **4** | **`derived\` alt klasörü (transcode çıktısı arşivde)** | Dönüştürülmüş dosya "el altında" durur | Alan iki katına çıkar; **orijinal mi üretim mi** belirsizleşir; yeniden transcode = çift bakım | **RET** — kullanıcı: *arşiv = orijinal + kapak + meta*; transcode = **cache** (uygulama katmanı, `media\` dışı) → silinir/yeniden üretilir |
| **5** | **Embedded ID3 yazımı (metadayı dosyanın içine koymak)** | Tek dosya taşınır | **Orijinal dosya değişir** (hash/byte düzeyinde) — "taşınmaz/dokunulmaz" ilkesini kırar; bozuk ad/farklı kaynak geri alınamaz | **RET** — orijinal **dokunulmaz**; meta **yan JSON**'da yaşar (§2.6, madde 4) |

---

## §4 Sonuçlar (Consequences)

### §4.1 Olumlu Sonuçlar

- **Tek eksenle tür araması kayıpsız:** Tür/dönem/ruh sorguları **tag + GUI/MySQL indeksi** ile karşılanır; diskte kopya üretilmez → "kayıp yok, indeks çözer".
- **Rename güvenli:** Kalıcı kimlik **ULID** olduğundan slug/yol değişikliği indeksi kırmaz; 1M+ ölçekte `rename` rutindir.
- **MySQL çökse arşiv yaşar:** SSOT yan JSON'da olduğu için DB **yeniden üretilebilir**; veri kaybı riski indekse bağlı değildir.
- **Alan/ütü şeffaf:** `derived\` olmadığından arşiv boyutu = gerçek varlık boyutu; cache temizliği arşivi **etkilemez**.
- **Kapsam disiplini:** TAŞIMA YOK → bu fazda veri bütünlüğü riski sıfıra yakın (yalnız iskelet/config/docs).

### §4.2 Olumsuz Sonuçlar

- **C: diski büyüme baskısı:** 38,33 GB + gelecek 1M+ varlık C: üzerinde **disk genişletme incelemesi** tetikler (§6 eşiği — bakım kuralı olarak sürekli izlenir).
- **Tür gezinmesi dolaylı:** Genre-first'e alışkın kullanıcı için tek eksen **daha derin tıklama** gerektirir → GUI/tag katmanının iyi kurulması gerekir.
- **Çift meta kaynağı riski:** Yan JSON (SSOT) ile MySQL (türetilmiş) **senkron** tutulmazsa indeks eskir → yeniden indeksleme zorunlu hâle gelir.
- **Bozuk Türkçe adlar:** Kaynak kırık isimlendirme (§1.2) normalize edilmeden **slug üretimi** hatalı üretebilir → girişte (inbox) temizlik şart.

### §4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **R1 Kaynak istatistikleri doğrulanmadı (7.551 / 38,33 GB)** | Orta | Orta | §7.2 kabul kriteri: kaynak tarama ile **sağ** (taşınmadan sayım); sonuç bu ADR `updated` satırına işlenir |
| **R2 C: doluyor (<31 GB)** | Orta | Yüksek | §6 eşiği: sürekli izleme → eşik altı ise disk genişletme incelemesi (yeni ADR gerekmez) |
| **R3 Slug/UTF-8 bozukluğu** | Orta | Orta | Slug **ASCII ≤80**; meta **UTF-8** (§7.1 kriter 5); `vault-utf8-writer` verify + mojibake scan |
| **R4 Taksonomi kayması (açık/kapalı sınırının aşılması)** | Düşük-Orta | Orta | `taxonomy.json` **kapalı**; yeni tür = config değişikliği + onay; `etiket[]` açık kalır |
| **R5 ULID üretimi/çakışma** | Düşük | Yüksek | Şema `^[0-9A-HJKMNP-TV-Z]{26}$` (§6) — audit'te regex zorunlu (§7.1 kriter 4) |

---

## §5 Uygulama (Implementation)

### §5.1 Uyum Matrisi

| Kaynak | Şart / İlişki | Bu ADR'nin Karşılığı |
|--------|----------------|----------------------|
| [[ADR-039-7-service-platform-architecture]] (L120) | media.coremusic.net **PLANNED** (PHP 8.4 + FFmpeg, port 5000/6000, "dizin YOK") → **yerleşim kararı bu ADR'de** | §2.1-§2.2 dizin eksen + yerleşim; ADR-039'un "dizin YOK" kaydı **kapatılmaz**, tamamlanır |
| [[../../VISION.md]] §6 (madde 6, L136) | "Merkezi Medya Yönetimi ve Streaming … **merkezi ev/ofis medya arşivi**" | §2.4 yan-JSON meta + kapak kalıcı arşiv içinde (§1.1 notu: "Metadata + Kapak" ifadesi VISION'da birebir YOK) |
| [[../../CLAUDE.md]] §2.1 | SSOT Priority Order (CLAUDE > AGENTS > WORKFLOW > brain > index) | §2.6 madde 6: **SSOT = yan JSON**; MySQL türetilmiş |
| [[../../architecture/k15-medya-streaming/index.md]] + `media-metadata.md` | K15 = **FFmpeg pipeline + Media Metadata Engine** (ID3/Vorbis/APE/iTunes **oku-yaz**); ADR-039'un "FFmpeg → K15" atfının dokümanı | §2.1 madde 5: FFmpeg çıktısı **uygulama `cache\`**'indedir (`media\`'ye girmez) · §3 alternatif 5: **embedded ID3 yazımı RET** — orijinal dokunulmaz; K15 motoru **okuma** tarafında, yazma **yan JSON SSOT**'a bağlıdır |

### §5.2 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Bu ADR'yi şablondan üret + künye/§1-§7 dolu (Guardrail #16) | Vault Steward | 2 dk | ✅ UYGULANDI (2026-09-29) |
| 2 | İskeleniz: `media\` + `src\` + `config\` + `docs\` ayrımı (yalnız dizin/config/docs — **taşınma yok**) — **Faz 2 alt maddeleri (2026-09-29):**<br>• **PHP CLI (Faz 2):** `bin/{scan,audit,ingest}.php` + `src/Media/{Slugger,Ulid,Taxonomy,Validator,CatalogWriter}.php` + `composer.json` → commit `50f8734` (2.060 satır PHP); `audit.php` ADR §6.1/§7.1 kurallarını denetler; `ingest.php` dry-run (CSV), `--commit` olmadan tek bayt kopyalamaz (tek kopya noktası `ingest.php:328`) — **⚠️ PHP bu makinede yok → `php -l`/çalışma testi YAPILMADI**<br>• **MySQL türetilmiş indeks:** `.ai/.sql/mysql/media_catalog.sql` — DB `media_catalog`, 9 tablo + `v_asset_search` (FULLTEXT), `utf8mb4_tr_0900_ai_ci`, BCNF, taxonomy 98 seed (17 anahtar), `asset_tag`→`taxonomy` FK — **⚠️ MySQL yok → DDL çalıştırılamadı**; `coremusic_media` BAŞKA DB (ana uygulamanın cihaz-senkron şeması, v8.0.0) — ezilmedi, geri alındı<br>• **Git kuralı:** `catalog/ reports/ vendor/ *.log` ignore + `!src/Media/` negasyonu (`media/` çapasızdı, `src/Media/` PHP kodunu yutuyordu)<br>• **Kararlar:** boş slug yedeği = `isimsiz` (2026-09-29) · audit/scan `--deep` bayrağı, varsayılan hafif mod = yeniden hash YOK (§6.1 k.5 ile uyumlu)<br>• **Test:** statik denetim 13 PASS / 1 FAIL (scan koşulsuz hash → düzeltildi) / 4 VR<br>• **Ertelenen:** GUI (faz 3) — başka oturum `.ai/ui-design/` dosyasını sıfırdan yeniden yazıyor, çakışma riski; ingest `--commit` ile dosya kopyalama ⏳ | Backend + Data | 1 gün | ✅ UYGULANDI (2026-09-29 — 62 dizin + config 3 + docs 2) · `Test-Path 'C:\www\coremusic.net\media.coremusic.net\media'` = **True** |
| 3 | `config\taxonomy.json` (kapalı taksonomi) + şema örnekleri `config/schema/*.json` + audit (regex/UTF-8) | Data Engineer | 2 gün | ✅ UYGULANDI (2026-09-29 — `config/taxonomy.json` 17 anahtar + şema `config/media.schema.json` (`config/schema/*.json` yolu YOK — yol sapması, disk kanıtı) + `audit.php` statik denetim 13 PASS / 1 FAIL (scan koşulsuz hash → düzeltildi) / 4 VR; ayrıntı adım 2 alt maddesi) · **⚠️ PHP yok → `php -l`/çalışma testi YAPILMADI** |
| 4 | Kaynak sayım (7.551 dosya / 38,33 GB) doğrulaması — **salt-okunur**, taşınmadan | Data Engineer | 2 saat | ⏳ PLANNED (R1) |
| 5 | `inbox → aktif → saklı → tekrar → arşiv` yaşam döngüsü akışı + C: <31 GB izleme | Backend + DevOps | 3 gün | ⏳ PLANNED |
| 6 | MySQL türetilmiş indeks (`utf8mb4_tr_0900_ai_ci`) + yeniden indeksleme çalıştırması | Data Engineer | 2 gün | ✅ UYGULANDI (2026-09-29 — `media_catalog.sql` YAZILDI: 9 tablo + `v_asset_search` FULLTEXT, `utf8mb4_tr_0900_ai_ci`, BCNF; ayrıntı adım 2 alt maddesi) · ⏳ **DDL + yeniden indeksleme ÇALIŞTIRILAMADI — ⚠️ VERIFICATION REQUIRED (MySQL bu makinede yok)** · NOT: `coremusic_media` ezilmedi, geri alındı |
| 7 | `.ai/.decisions/index.md` kayıt satırı (ADR-092) — **ayrı işlem, bu görevde YAZILMADI** | MO (vault-updater) | 1 dk | ⏳ PLANNED |

### §5.3 Geri Dönüş Planı

1. **Karar seviyesi:** `status: accepted` ama **frozen değil** → vazgeçiş = yeni ADR (NNN+1) ve bu dosya `superseded-by` ile bağlanır; metin **silinmez/değiştirilmez**.
2. **İskelet/config seviyesi:** Bu faz yalnız dizin + config + docs üretir → iptal maliyeti klasör/ayar silmektir; **orijinal Music verisine hiç dokunulmadığı için geri alınacak veri yoktur** (TAŞIMA YOK).
3. **Taksonomi seviyesi:** `taxonomy.json` değişecekse eski sürüm config içinde saklanır; `etiket[]` alanları etkilenmez.
4. **Log/dizin seviyesi:** `log.md` append-only → geri dönüş de **yeni satır**; `index.md` kaydı geri alınırsa satır `—` işaretlenir (silinmez).
5. **Bozulma durumunda:** `node .ai/scripts/vault-utf8-writer.mjs repair --file <dosya>` (yedek alır) → gerekirse `git checkout`.

---

## §6 İlgili Dokümanlar ve Bağlı Kurallar

### §6.1 Bağlılıklar / Kurallar (bağlayıcı)

| # | Kural | Değer / Eşik | Aksiyon |
|---|-------|--------------|---------|
| 1 | **Slug** | **≤ 80 karakter, ASCII** | Aşım → slug kırpılır; kimlik ULID'dedir (slug değişimi indeksi kırmaz) |
| 2 | **ULID** | regex **`^[0-9A-HJKMNP-TV-Z]{26}$`** | Audit'te zorunlu doğrulama (§7.1 kriter 4) — I/L/O/U **yok** |
| 3 | **`_cesitli` klasör adı** | **a-z** zorunlu (Türkçe karakter YOK) | Üretimde lowercase normalizasyonu |
| 4 | **Sanatçı klasör sayısı > 50.000** | **baş-harf katmanı** (`a/`, `b/` …) | Eşik aşılırsa eklenir; mevcut ağaca toplu taşıma YOK |
| 5 | **sha256** | **yalnız girişte** (inbox) hesaplanır | Sonraki aşamalarda yeniden hash'leme YOK (dokunulmazlık) |
| 6 | **C: boş alan < 31 GB** | **disk genişletme incelemesi** | Sürekli izleme (DevOps) — §4.3 R2 |

### §6.2 İlişki Tablosu (wiki-link hedefleri: **8/8 diskte doğrulandı** — Test-Path, 2026-09-29)

| Dosya (wiki-link) | İlişki |
|-------------------|--------|
| [[ADR-039-7-service-platform-architecture]] | media.coremusic.net servis yerleşimi (port 5000/6000, PHP 8.4 + FFmpeg, PLANNED, "dizin YOK") — **bu ADR onu tamamlar** |
| [[../index.md]] | Karar dizini — **ADR-092 kayıt satırı BEKLİYOR** (§5.2 adım 7; bu görevde yalnız 1 dosya yazıldığı için güncellenmedi) |
| [[../../VISION.md]] | §6 madde 6 (satır 136) merkezi medya arşivi şartı |
| [[../../CLAUDE.md]] | §2.1 SSOT Priority Order + Guardrail #16 (şablon zorunluluğu) |
| [[../../brain.md]] | **✅ diskte** (1.089 satır) — `:205` K15 katmanı satırı: "Medya & Streaming — FFmpeg, FLAC, HLS, DASH, ID3" · `:252` K15→K0 bağımlılığı; **`media.coremusic.net` girişi YOK** → yerleşim/eksen ilk kez bu ADR'de yazılıyor |
| `.ai/.templates/adr/adr-template.md` | §1-§7 iskelet kaynağı (Guardrail #16) |
| [[../../architecture/k15-medya-streaming/index.md]] · `media-metadata.md` | **K15 Medya & Streaming katmanı** — 16 dosya (git tracked, commit `523497c`): `index.md` (FFmpeg pipeline · codec · HLS/DASH · CDN), `media-metadata.md` (**Media Metadata Engine** — ID3/Vorbis/APE/iTunes **oku-yaz**), `ffmpeg-pipeline.md` | ADR-039'un "FFmpeg → K15" atfının dokümanı · §2.1 madde 5: FFmpeg çıktısı **uygulama `cache\`**'indedir, `media\`'ye girmez · §3 alternatif 5: **embedded ID3 yazımı RET** — orijinal dokunulmaz; K15 motoru **okuma**da kullanılır, yazma **yan JSON SSOT**'a bağlıdır |
| `.ai/.decisions/ADR-091-template-engine-no-eval.md` | Aynı seride **dolu olan 091** → bu ADR **092** aldı (Numara gerekçesi) |

---

## §7 Onay

### §7.1 Kabul Kriterleri (Doğrulama)

| # | Kriter | Doğrulama Yöntemi | Durum |
|---|--------|--------------------|-------|
| **1** | `media\` içinde **sadece medya ağacı** (`audio\` + `video\` + yan meta); kod/config/docs **media dışında** | Dizin listesi denetimi | ⏳ PLANNED (iskelet henüz kurulmadı — ADR-039 PLANNED) |
| **2** | Kaynak **7.551 dosya / 38,33 GB sağ** (**taşınmadı**) | Salt-okunur sayım (§5.2 adım 4) | ⏳ PLANNED — **⚠️ VERIFICATION REQUIRED** (§1.2, R1) |
| **3** | `config\` + `docs\` + **bu ADR** diskte mevcut | `Test-Path` / glob | ✅ Bu ADR yazıldı; config/docs ⏳ PLANNED |
| **4** | Şema örnekleri **audit 0 hata** (ULID regex, `_cesitli` a-z, slug ≤80 ASCII) | `config/schema/*.json` audit çalıştırması | ⏳ PLANNED (§4.3 R5) |
| **5** | Slug **ASCII** + meta **UTF-8 Türkçe** doğru (mojibake YOK) | `vault-utf8-writer verify` + `scan` | ⏳ PLANNED (bu dosya yazımında verify çalıştırılacaktır) |

### §7.2 Karar Künyesi

| Alan | Değer |
|------|-------|
| **Durum** | **accepted** |
| **Tarih** | **2026-09-29** |
| **Ağırlık** | **1 (varsayılan)** |
| **İlgili ADR** | **039** |
| **Seriler karıştırılmadı** | `.ai/architecture/adr/` (023-026) ayrı seridir — bu ADR **092** numarasını `.decisions/` kümesinden aldı |

### §7.3 Onay Tablosu

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali / Vault Steward | 2026-09-29 | ✅ (kullanıcı karar metni onayı) |
| Tech Lead | — | — | ⏳ |
| Arch Lead | — | — | ⏳ |

---

*ADR-092 v1.1.2 — CoreMusic Architecture Decision Record*
*Authority: ADR-092 Karar Metni (SSOT)*
*Last Updated: 2026-09-29*
*Mode: Red Team · Human Mode · Truth Mode*
