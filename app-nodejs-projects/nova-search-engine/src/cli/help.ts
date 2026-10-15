/** Yardım metni — parametre tablosu prompt'taki kısayollarla birebir. */
export const HELP = `
nova-search-engine — bağımsız YouTube metadata arama motoru (parametrik CLI)

KULLANIM
  nova search "<sorgu>" [parametreler]
  nova collect "<sorgu>" [--target N]   derin toplama (hedef kayıt, ilerleme çubuğu)
  nova collect "<sorgu>" -s             tüm sayfaları otomatik kaydırır (continuation bitene kadar, max 60)
  nova collect "<sorgu>" [--once]       kuyruktaki işleri işler
  nova video <id|url> [parametreler]    video detayı: kayıt + yorumlar + benzer videolar
  nova enrich [--limit N]               video detay + kanal zenginleştirme (il. çubuğu)
  nova stats                            DB / cache sayaçları
  nova cache --prune                    süresi dolmuş disk cache'i temizler
  nova serve                            API + Web başlatır (3100 API · 3110 Web)
  nova --help / --version

VIDEO PARAMETRELERİ (-kısa | --uzun)
  -comments  N       Yorum çeker (0-50; üst düzey yorumlar)
  -related   N       Benzer/önerilen videoları çeker (0-20)
  -ai                Benzer videolara AI benzerlik skoru + gerekçe (üst sınır8)
  -json              Ham JSON çıktısı (VideoDetailPayload)
  -offline           YouTube'a gitmez; yalnız DB + disk cache

  (id yerine tam URL de olabilir: watch?v= · youtu.be · /shorts/)

ARAMA PARAMETRELERİ (-kısa | --uzun)
  -ai                 AI katmanını açar (analiz + skor + gerekçe)
  -deep               Deep Search: AI 3 ek sorgu üretir, birleştirir
  -explain            İlk sonuçlara "Neden bu video?" açıklaması
  -offline            YouTube'a asla gitmez (yalnız yerel DB/cache)
  -q    --query  METIN  Ek AI sorgusu (positional sorgu varken -q, AI prompt'u olur)
  -aip  --aiPrompt METIN  AI'ya gidecek özel prompt (analiz + skor + genişletmeye eklenir)
  -eq   --extraQuery   Ek arama terimi
  -s    --scroll   [N]  N sayfa (max 5) · değersiz (-s) = tüm sayfaları topla (auto, max 60)
  -r    --result   N   Sonuç limiti (= -l, max 5000)
  -l    --limit    N   Sayfa başına sonuç (varsayılan 24, max 5000)
  -f    --filter   v|s|sh   video / song / short
  -sb   --sortBy   relevance|views|date|rating
  -minv --minViews N   Alt görüntülenme sınırı
  -maxv --maxViews N   Üst görüntülenme sınırı
  -mins --minScore N   Alt AI skor sınırı (0-100)
  -qa   --quality  N   Kalite eşiği (0-100)
  -d    --duration ">600" | "60-300"   süre aralığı (sn)
  -p    --page     N   Sayfa numarası
  -t    --type     METIN  kategori türü
  -ud   --uploadDate "2015" | "<=1y"   yükleme tarihi

ÖRNEKLER
  nova search "Akasya Durağı" -ai -explain
  nova search "adele" -result 100 -scroll 3 -f v -sb views
  nova search "tarkan" -s -result 200          # tüm sayfaları otomatik kaydırıp topla
  nova search "lofi" -q "yavaş sakin piyano" -ai -deep
  nova search "tarkan" -ai -explain -aip "sadece resmi klipleri tercih et"
  nova search "tarkan" -f s -minv 100000 -offline
  nova search "jazz" -d ">600" -mins 70 -json
  nova collect "kayıp şarkılar" --target 5000
  nova enrich -limit 30

ÇIKIŞ KODLARI: 0 başarılı · 2 parametre hatası · 1 beklenmeyen hata
`;

export const VERSION = 'nova-search-engine 0.1.0';
