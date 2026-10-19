# log.md — Audit Trail (append-only)

## 2026-10-16

- [13:16] Sıfırdan kurulum başladı; `C:\www\coremusic.net\app-nodejs-projects\nova-search-engine` oluşturuldu (migrate kopya onayla silindi).
- [13:20] pnpm workspace kuruldu: 8 paket + 2 app. youtubei.js 18.1.0, fastify 5, mysql2, commander 12.
- [13:22] `.env` oluşturuldu; `OPENROUTER_API_KEY` yazıldı (yalnız yerel; `.gitignore` içinde).
- [13:30] Çekirdek yazıldı: common, database (SQLite+MySQL adapter), cache, ai, youtube, search, workers.
- [13:35] 2 CLI yazıldı: `nova-search` (parametrik) + `nova-search-wizard` (menü).
- [13:38] Web (Vanilla JS + ITCSS/BEM) + Fastify API yazıldı.
- [13:40] typecheck temiz (exit 0). 27/27 test yeşil.
- [13:42] Smoke: `search "Akasya Durağı"` → 10 sonuç (1.6sn, canlı YouTube).
- [13:43] Smoke: `search "Adele hello live" --ai` → AI aktif, sonuçlar doğrulandı.
- [13:43] **Bug düzeltildi:** sorgu varyantları birleşirken iç dedup eksikti (aynı video 3 kez).
- [13:44] Smoke: `--offline` → 10 sonuç / 9ms / local-db. DB: 21 video.
- [13:45] API smoke: `/api/health` ok · `/api/search` ok · web statik HTTP 200.
- [13:46] OpenRouter free model listesi API'den doğrulandı; `models.ts` güncellendi (ADR-007).
- [14:05] **Toolchain değişimi:** esbuild/tsx/vitest TAMAMEN çıkarıldı → **Bun 1.4.2**
  (çalıştırıcı + `bun test`). Gerekçe: pnpm build-script izin sorunları + kullanıcı kararı.
  Doğrulama: `node:sqlite` ve `process.loadEnvFile` Bun'da uyumlu (test edildi).
- [14:08] `bun test`: 27/27 · 150 expect. `tsc`: 0 hata (`@types/bun` eklendi).
- [14:09] Canlı smoke (Bun): YouTube arama 5/5 unique · AI arama 5/5 unique (AI:True).
- [14:10] API+Web (Bun): `/api/health` ok · `/api/search` 11 sonuç (local-db) · web HTTP 200.
- [14:30] **Derin çıktı turu:** CLI Unicode box tablolar + progressbar; "NEDEN ÖNERİLDİ?"
  gerekçe kutusu (buildReasons); API/web zenginleştirilmiş sonuç (score, matchedTerms,
  reasons, analysis); web kartlarında açıklama+etiket+neden listesi; DB'ye `channel_name`
  kolonu (ALTER ile geriye dönük); favicon 404 kapatıldı.
- [14:32] Ders: **bayat süreç tuzağı** — eski API süreci eski kodla çalışırken cache'ten
  sızan `reasons` alanı yanıttaydı ama `score` yoktu (eski toVideos atıyordu). Kod değil,
  süreç bayattı → restart ile düzeldi. Değişiklik sonrası süreç yenileme kuralı.
- [14:35] Doğrulama: tsc 0 · bun test 33/33 (160 expect) · CLI box çıktı + kanal sütunu
  dolu (Tarkan) · web tarayıcı testi (playwright): arama→kart→neden listeleri ✅.
- [14:50] Port duzeltmesi: .ai/PROJECTS.md talimatina gore PORT=3001 + http://novasearch.coremusic.net:3001 (hosts'ta zaten tanimli). Tarayici dogrulamasi: uyum %90 badge, nedenler, kanal dolu.
- [15:20] Tamamlama turu: docs/PRD.md (kapsam kilidi + kabul kriterleri), web autocomplete (datalist) + acilir detay paneli (tam metadata) + sistem durum cubugu; wizard pipe-dostu (test edilebilirlik). Dogrulama: tsc 0 � 33/33 test � web E2E (8 oneri, uyum %90, 60 neden, detay acik) � wizard pipe gecti � API 5 endpoint. NOT: kullanici 'npm run api' ile calistiracak - port 3001 birakildi.
- [16:10] Prompt-denetim turu: 7 eksik kapandi - SearchIndex (MSA: once indexle sonra aday), scroll 5000/continuation 50 (~1000+ video), refresh komutu (metadata tazeleme), trending, backup (VACUUM INTO), stream (GoogleVideo, IOS istemcisi trick), docs/MIMARI.md + knowledge-base + WORKFLOW. Bug bulundu: parseNumber yerine '|| 7' kullanimi gecerli 0'i yutuyordu (refresh). Testler: 40/40.
- [17:20] Zengin metadata turu: Video sozlesmesi genisletildi (publishedText, commentCount, category, thumbnails[], badges[], isFamilySafe/Unlisted/Crawlable, channelSubscriberCount/Verified). Mapper: parseRelativeDate (tr+en), tam kazima (getVideoDetails: like_count, primary_info, microformat, secondary_info). DB: 11 yeni kolon + ALTER migration. Oto-collect: arka plan derin toplama + detay zenginlestirme isleri (kuyruk 5 bekleyen/3 tamamlandi - canli dogrulandi). Web: AI yonlendirme promptu alani (aiQuery) + 17 alanli detay paneli. Bug: JobQueue sonsuz dongu CLI cikisini engelliyordu (close() collector.stop() ile cozuldu).

## 2026-10-16 [16:10] - Ana vault devir + MySQL canli test baslangici

- Ana vault kaydi: coremusic.net/.ai/projects/NovaSearchEngine/01 (kurulum+mimari) + 02 (PRD ozeti+kabul kriterleri 8/8).
- index.md projects 62 md + keys.md keyword + log.md append tamam.
- MySQL 127.0.0.1:3306 CANLI erisilebilir (docker yok, yerel MySQL) -> adapter canli testi BASLIYOR.
- coverage kapisı: %60.30 lines / %52.21 funcs (hedef %75) - kapatma sirlasi: mysql -> search -> youtube(mock) -> workers.- [18:00] Maksimum veri turu: url/channelUrl/viewCountText/captionTracks/topComments/relatedIds eklendi; getVideoDetails tam kazima (getComments, watch_next_feed taramasi, secondary_info.owner abone/dogrulama). Probe ile ogrenildi: getInfo'da 'microformat' YOK, alanlar secondary_info.owner + watch_next_feed uzerinde; like_count cogu videoda undefined. parseViews TR/EN kisaltma destegi (B=bin TR, B=milyar EN; 'yorum/abone/izlenme' imleyicileri) - 41 B yorum = 41.000 dogrulandi. Kanal detayi collector'da otomatik kaziniyor.
- [18:40] Temizlik + test URL listesi: olu kod temizlendi (server.ts void searchYouTube artigi, RATE_LIMITS, CollectJob, step()); scripts/mysql-smoke.ts bilerek tutuldu (MySQL testi icin). docs/TEST-URLS.md yazildi (API/CLI/Web + parametre sozlugu + sorun giderme). SearchIndex TTL (60sn) ile eski-kopya servis hatasi cozuldu; /api/v1/* alias eklendi. Dogrulama: tsc 0, 49/49, 4 URL canli.
- [18:55] KRITIK BUG: refreshStale merge'i '...v' + birkac alan kopyaliyordu; tags/publishedAt/topComments/relatedIds/captionTracks gibi zengin alanlar DUSUYORDU (275 tazelendi dedi ama DB'de 15 etiketli vardi). Duzeltme: ...fresh yayiliyor, fresh'te bos donen kritik alanlar eski degerle korunuyor. Tek satir dogrulama: tags 24, comments 31000, related 20, published 2012-05-01. Tam kazima yeniden baslatildi.
- [19:10] Web kalici yapildi: start-web.bat + Startup kisayolu (NovaSearch-Web.lnk) - kabuk olumlerinden bagimsiz, oturum acilisinda otomatik. Arka plan kabuklari Shell.NotFoundError ile oluordu; cozum: Start-Process (bagimsiz surec). Kazima (275 satir) devam ediyor.
- [19:35] AI her sisteme tam entegre: aiRelevanceFilter (AI ilgi puani - 0.2 alti elenir, 0.6/0.4 harman, neden uretir), CLI tablosu zenginlesti (Begeni/Tarih sutunlari), web'e Ekstra AI sorgusu alani, wizard'a ek sorgu. -ai (tek tire) ve --ai ikisi dogrulandi. Canli: Kuzu Kuzu %92 vs derleme %60 (AI filtresi ayrismasi), web 107 sonuc / 5 varyant.

## 2026-10-16 [16:45] - MySQL adapter canli testi GECTI (6/6)

- scripts/mysql-smoke.ts olusturuldu (ayri novasearch_test DB - legacy DB'ye dokunulmadi).
- Sonuc: CREATE DB OK · VERSION 9.7.0-er2 · migrate OK · round-trip OK (42) · transaction rollback OK (0) · videos dedup ENGELLENDI (UNIQUE youtube_id) · cleanup OK.
- BUG FIX: packages/database/src/schema.ts migrate() coklu-DDL tek exec'te gonderiyordu -> MySQL multipleStatements:false (injection guvenligi) reddediyordu. DDL ; ile bolunup tek tek calistirildi. SQLite davranisi degismedi.
- TESPIT: mevcut novasearch DB legacy salema (42 kolon, id varchar UUID, channel_id, duration) - kod kanonik salamayi (channel_youtube_id, duration_seconds) bekliyor. DB hic kullanilmamis (3 satir). DROP+recreate onay BEKLEMEDE.- [20:05] 7 is + SOLID turu tamam: API parite (trending/refresh/backup/metrics), oneri motoru (indeks terim benzerligi), dil tespiti + spam skoru (common/signals - katman geregi ai'den tasindi), monitoring sayaclari, admin sayfasi (/admin.html), PRD 544 / MIMARI 510 satir. Duzeltmeler: API refresh '|| 7' tuzagi tekrar, suggest handler DRY, sinyaller scoreVideo'da tamamlaniyor (cache yolu eski objeleri de kapsar). Testler 61/61, tsc 0. Ol� kod taramasi temiz.
- [21:00] Hiz + tablo + begeni turu: begeni sayisi bulundu (SegmentedLikeDislikeButtonView.like_count - primary_info.menu icinde, probe: 558.534); collectLikeCount defansif tarama. Tablo: Video ID + URL (youtu.be) sutunlari, Tarih mutlak tarih. API hiz: AI analizi ile indeks yuklemesi paralel (YouTube ritmi/ban koruma degismedi). ai param: true/1/yes/on. Bozuk goruntulenme sayilari (eski parse bug'i: TR 'B'/'goruntulenme' imleyicisi eksikti) tespit edildi - 612 satir - onarim kazimasi arka planda suruyor.
- [21:20] CLI AI sorgusu duzeltmesi: (1) AI sorgusu (q/extraQuery) artik AI/SORGU ANALIZI kutusunda 'AI sorgunuz:' satiriyla gorunuyor (analysis.directive); (2) --ai verilip -q girilmemisse TTY'de CLI 'AI yonlendirme sorgusu' diye soruyor (stdin TTY degilse sessiz - betikler bozulmaz).
