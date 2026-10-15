/** Yardım metni — parametre tablosu prompt'taki kısayollarla birebir. */
export const HELP = `
nova-search-engine — bağımsız YouTube metadata arama motoru (parametrik CLI)

KULLANIM
  nova search "<sorgu>" [parametreler]
  nova collect "<sorgu>" [--target N]   derin toplama (hedef kayıt, ilerleme çubuğu)
  nova collect "<sorgu>" [--once]       kuyruktaki işleri işler
  nova enrich [--limit N]               video detay + kanal zenginleştirme (il. çubuğu)
  nova stats                            DB / cache sayaçları
  nova cache --prune                    süresi dolmuş disk cache'i temizler
  nova serve                            API + Web başlatır (3100 API · 3110 Web)
  nova --help / --version

ARAMA PARAMETRELERİ (-kısa | --uzun)
  -ai                 AI katmanını açar (analiz + skor + gerekçe)
  -deep               Deep Search: AI 3 ek sorgu üretir, birleştirir
  -explain            İlk sonuçlara "Neden bu video?" açıklaması
  -offline            YouTube'a asla gitmez (yalnız yerel DB/cache)
  -q    --query  METIN  Ek AI sorgusu (positional sorgu varken -q, AI prompt'u olur)
  -aip  --aiPrompt METIN  AI'ya gidecek özel prompt (analiz + skor + genişletmeye eklenir)
  -eq   --extraQuery   Ek arama terimi
  -s    --scroll   N   Toplanacak sayfa (max 5)
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
  nova search "lofi" -q "yavaş sakin piyano" -ai -deep
  nova search "tarkan" -ai -explain -aip "sadece resmi klipleri tercih et"
  nova search "tarkan" -f s -minv 100000 -offline
  nova search "jazz" -d ">600" -mins 70 -json
  nova collect "kayıp şarkılar" --target 5000
  nova enrich -limit 30

ÇIKIŞ KODLARI: 0 başarılı · 2 parametre hatası · 1 beklenmeyen hata
`;

export const VERSION = 'nova-search-engine 0.1.0';
