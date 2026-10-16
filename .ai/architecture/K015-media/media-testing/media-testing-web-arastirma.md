---
node_id: "K015-media/media-testing/media-testing-web-arastirma"
node_type: "module"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "8 kaynak URL'si 2026-10-16'da webfetch ile canlı doğrulandı — 8/8 HTTP 200"
  - "Ana iddialar 2+ kaynak cross-check (§2) — çapraz doğrulama 4/4 (C-4 kısmi: ses-özel iddia tek kaynak)"
validation: "8/8 URL canlı (2026-10-16); her iddia §3'te CoreMusic yüzeyine bağlandı; bulamayan/tek-kaynaklı iddia ⚠️ VERIFICATION REQUIRED (§4)"
open_questions:
  - "CoreMusic'in medya test araç seti seçimi → Q-1 (kapsam §6) — bu dosya yalnız seçenek kanıtı taşır"
  - "P.863 erişim/ücret koşulları → ⚠️ VERIFICATION REQUIRED (§4-W4)"
group: kanit
---

# K015 · media-testing · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Konu: medya decode doğrulama araçları,
> ITU algısal kalite standartları, toleranslı çıktı karşılaştırması, tarayıcı/E2E ses testi

---

## §1 DOĞRULAMA TABLOSU (canlı kaynaklar — 2026-10-16)

> Kural: her satır **iddia × URL × tarih × durum** taşır; iddialar kaynak metninden çıkarılmıştır (uydurma yok).

| # | İddia (kaynak ne diyor) | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | ffprobe: multimedya akışlarından bilgi toplar, insan/makine-okunur çıktı verir; container ve her stream'in format/tipini kontrol eder; açılamayan/ tanınmayan girdi için **pozitif çıkış kodu** döner; `-show_format` / `-show_streams` / `-show_error` + JSON writer | https://ffmpeg.org/ffprobe.html | Tarih: 2026-10-16 | HTTP 200 |
| 2 | ITU-T P serisi listesi: P.862 (PESQ) ve P.862.1/.2/.3 "5 January 2024'te silindi — bkz P.863, P.863.1, P.863.2"; P.863 "Perceptual objective listening quality prediction" yürürlükte; P.861 de P.862 ile değiştirilip silinmiş; P.800 öznel MOS yöntemi | https://www.itu.int/rec/T-REC-P/en | Tarih: 2026-10-16 | HTTP 200 |
| 3 | Recommendation ITU-T P.863 (03/2018) yayını — "Perceptual objective listening quality prediction" (e-publication kaydı mevcut; sayfa başlık düzeyinde döndü) | https://www.itu.int/epublications/publication/itu-t-p-863-2018-03-perceptual-objective-listening-quality-prediction | Tarih: 2026-10-16 | HTTP 200 (başlık) |
| 4 | THD (Wikipedia): toplam harmonik bozulma tanımı (harmonik güçler toplamı / temel frekans gücü); **"THD standartsız bir spesifikasyondur, üreticiler arası sonuçlar kolayca karşılaştırılamaz"**; THD+N daha yaygındır ve cihazlar arası daha karşılaştırılabilir; anlamlı ölçüm ölçüm bandını içermelidir | https://en.wikipedia.org/wiki/Total_harmonic_distortion | Tarih: 2026-10-16 | HTTP 200 |
| 5 | Media pipeline CI/CD: medya çıktısı "nadiren bit-exact'tir"; "byte değil özellik assert et"; FFmpeg sürümü hash ile sabitlenir; golden kapıları PSNR ≥ 42 dB, SSIM ≥ 0.98, LUFS toleransı ±0.5; golden güncellemesi yalnız ayrı işte (GOLDEN_UPDATE=1) ve kod-review'dan geçer; flaky test **karantinaya** alınır, skip edilmez | https://mediapipeline.org/pipeline-automation-batch-processing/ci-cd-and-testing-for-media-pipelines/ | Tarih: 2026-10-16 | HTTP 200 |
| 6 | BeatsCheck (GitHub, MIT): müzik dosyalarının bütünlüğünü **ffmpeg ile tam decode testi** yaparak denetler; başarısız veya "yarıda kesilen" dosyaları işaretler; sonraki taramalar yalnız yeni dosyaları kontrol eder (`rescan --fresh` her şeyi yeniden tarar) | https://github.com/chodeus/BeatsCheck | Tarih: 2026-10-16 | HTTP 200 |
| 7 | Playwright (resmi): Chromium/Firefox/WebKit için tek API ile uçtan uca test; test izolasyonu (her test taze browser context), paralellik + shard (CI için), auto-wait + web-first assertion'lar (yapay timeout'suz) | https://playwright.dev/ | Tarih: 2026-10-16 | HTTP 200 |
| 8 | "Testing Browser Audio with Playwright" (kazis.dev): HTML5/Web Audio ses testi tarayıcıda **monkey-patch** ile yapılır (`HTMLAudioElement.prototype.play`, `AudioBufferSourceNode.prototype.start` → `window.soundsPlayed`); fixture ile izolasyon; headless CI'da ses kartı gerekmez; **sınırlama: "oynatma niyetini" yakalar, kullanıcının gerçekten duyduğunu garanti etmez** | https://kazis.dev/blogs/playwright-sounds-test | Tarih: 2026-10-16 | HTTP 200 |

**Sayım:** 8 URL · 8/8 canlı (2026-10-16) · ölü URL: 0 · `⚠️ VERIFICATION REQUIRED`: §4'te 5 kalem (W-1, W-3, W-4, W-6, W-7).

---

## §2 ÇAPRAZ DOĞRULAMA (ana iddialar ≥2 kaynak)

| # | Ana iddia | Kaynak 1 | Kaynak 2 (+) | Sonuç |
|---|---|---|---|---|
| C-1 | Medya bütünlüğü **decode ile** doğrulanır (dosya sonuna kadar çözülebiliyor mu) | ffprobe çıkış kodu + stream denetimi (kaynak 1) | BeatsCheck tam decode testi (kaynak 6) | ✅ 2 kaynak — resmi araç dokümanı + gerçek uygulama |
| C-2 | Medya çıktısı **byte-exact değildir**; karşılaştırma özellik + toleransla yapılır | "nadiren bit-exact" · "özellik assert et" · PSNR/LUFS eşikleri (kaynak 5) | THD: üreticiler arası karşılaştırma ölçüm koşulları açıklanmadan yapılamaz → ölçüm **eşik/koşul** ister (kaynak 4) | ✅ 2 kaynak — farklı boyut (CI pratiği + ölçüm teorisi) |
| C-3 | PESQ (P.862) güncel değildir; yerini **P.863** almıştır | ITU P serisi: "deleted on 5 January 2024 → refer to P.863" (kaynak 2) | P.863 (03/2018) e-publication kaydı (kaynak 3) | ✅ 2 URL — **aynı yayıncı (ITU)**; bağımsız ikinci yayıncı aranmadı (§4-W5) |
| C-4 | Tarayıcı/E2E seviyesinde ses testi **yapısal (API-seviyesi)** yaklaşım ister | Playwright resmi: test runner, fixture/context izolasyonu, CI shard (kaynak 7) | Monkey-patch + fixture ile ses yakalama (kaynak 8) | ✅ 2 kaynak — **kısıt:** "ses-özel" iddianın tek dayanağı kaynak 8 (§4-W1) |

---

## §3 KOD XREF (kaynak → K015 yüzeyi)

| # | Kaynak iddiası | K015 karşılığı | Dosya·Bölüm / durum |
|---|---|---|---|
| 1 | ffprobe JSON + pozitif çıkış kodu (tanınmayan girdi) | decode doğrulama adımı (kapsam §4 adım 2 · §5 T-2) | `media-testing-ornekler.md` Ör-01/Ör-02 — araç **iskelet**, sürüm Q-1 (seçim yapılmadı) |
| 2+3 | P.862 silindi → P.863 | algısal kalite ölçümü adayı | kapsam §6-Q6 — **kullanım kararı verilmedi**; erişim koşulu §4-W4 |
| 4 | THD/THD+N üreticiler arası karşılaştırılabilir değil; ölçüm bandı zorunlu | toleranslı karşılaştırma prensibi | kapsam §1-3 · §5 T-3 — eşik **Q-3 UNKNOWN**, bu dosyada sayısal değer yok |
| 5 | özellik-üzerinden karşılaştırma (PSNR/SSIM/LUFS), sabitlenmiş ffmpeg, fail-closed golden, karantina | kapsam §5 T-7 (byte-eşitlik reddi) · §4 adım 4 | eşikler **referans değerdir, CoreMusic eşiği değildir** (Q-3) |
| 6 | ffmpeg tam decode + "yarıda kesilen" dosya tespiti | T-2 decode hatası + "stop short" sınıfı | ornekler Ör-02 bash kapısı — iskelet, üretim betiği değil |
| 7 | Playwright izolasyon/paralel/shard + auto-wait | E2E seviyesinde medya senaryosu çalıştırma zemini | kapsam §1-5 (durum takibi) — **E2E altyapısı K019 sınırı, §7/4 açık** |
| 8 | Web Audio/HTMLAudio monkey-patch fixture | tarayıcı sesi tetik doğrululaması (oynatma niyeti) | kapsam dışı-6 notu: ses *duyulabilirliği* ölçülmez — niyet testi kapsamı Q-2'ye bağlı |

---

## §4 NOTLAR VE ⚠️ KAYITLAR

| # | Not / eksik kanıt | Etiket |
|---|---|---|
| W-1 | "Playwright ses testi oynatma niyetini ölçer, duyulabilirliği garanti etmez" **tek kaynaklıdır** (kaynak 8 — blog); ikinci kaynak bulunamadı | ⚠️ VERIFICATION REQUIRED |
| W-2 | PSNR ≥ 42 dB / SSIM ≥ 0.98 / LUFS ±0.5 eşikleri **tek site** (kaynak 5) tarafından verilir — standart değildir; CoreMusic eşikleri Q-3 (UNKNOWN) | tek kaynak — eşikler bağlayıcı değil |
| W-3 | THD/THD+N maddesi **ansiklopedik ikincil** kaynaktır (kaynak 4); birincil standart metin (IEC/IEEE) okunmadı | ⚠️ VERIFICATION REQUIRED |
| W-4 | P.863 e-publication sayfası **yalnız başlık düzeyinde** döndü — erişim/ücret/ek-koşulları doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| W-5 | C-3'teki iki URL de **ITU**'ya aittir — bağımsız ikinci yayıncı cross-check'ü yapılmadı | tek yayıncı — cross-check kısmi |
| W-6 | K019-testing'in kapsamı/altyapısı bu araştırmada **incelenmedi** — §7/4 sahiplik sorusu açık kalır | ⚠️ VERIFICATION REQUIRED |
| W-7 | CoreMusic'in kendi test araç seti vault'ta tanımlı değil (media-master §12/10) — bu dosya yalnız harici seçenek kanıtı taşır; seçim Q-1 | ⚠️ VERIFICATION REQUIRED |

---

## §5 AÇIK KONULAR

| # | Konu | Etiket |
|---|---|---|
| TW-1 | Araç seti seçimi (ffprobe/ffmpeg sürümleri, PHPUnit/Playwright rolleri) | Q-1 — bu dosya karar **vermez**, yalnız kanıt taşır |
| TW-2 | Tolerans eşikleri (süre/PSNR/LUFS sayısal değerleri) | kapsam Q-3 (UNKNOWN) — Vault Steward |
| TW-3 | §7/4 sahiplik (K019-testing vs K015/media-testing) | ⚠️ VERIFICATION REQUIRED — bu dosyada **kapatılmaz** |
| TW-4 | P.863 kullanımı (erişim + hangi senaryoda) | Q-6 + §4-W4 |

---

## §6 YENİDEN ERİŞİM / YENİLEME KURALI

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Yenileme tetiği | §1'deki bir durum/tarih eskiyorsa (kanıt tazeleme turu, Q-1/Q-3 kararı) URL yeniden fetch edilir |
| 2 | Tarih güncelleme | Fetch edilen satırın tarihi gerçek erişim gününe çekilir; fetch edilmeyen eskisinde kalır |
| 3 | Yeni URL ekleme | Yalnız webfetch ile o gün doğrulanırsa §1 tablosuna satır eklenir; uydurma URL yasak |
| 4 | Ölü kaynak | 404/410 dönerse satır silinmez — durum `DEAD (erişim: YYYY-MM-DD)` olur ve §5'e kayıt açılır |
| 5 | Kapsam sınırı | Yenileme yalnız §1'deki 8 URL'yi kapsar; synchronization dosyasının URL'leri burada yenilenmez |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
