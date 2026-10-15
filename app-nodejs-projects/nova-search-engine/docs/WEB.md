# NovaSearch — Web GUI (3110)

## Başlatma

```bash
npm run serve
# → NovaSearch API: http://127.0.0.1:3100/api/v1/health
# → NovaSearch Web: http://127.0.0.1:3110/
```

Tarayıcıda <http://127.0.0.1:3110/> aç. `serve` **iki sunucuyu birden** başlatır — API olmadan arayüz çalışmaz (arayüz `fetch` ile 3100'e gider).

- Portlar `.env`'den gelir: `WEB_PORT=3110`, `API_PORT=3100`, `API_HOST=127.0.0.1`.
- Arayüz tek dosyadır: `public/index.html` (vanilla JS, bağımlılık yok, build adımı yok).

## Ekran bölümleri

```
┌ NovaSearch — YouTube metadata arama ────────────────────────────┐
│ [sorgu..................] [24▾] [ilgi▾] [tümü▾] [Ara]          │  ← form
│ ☑AI skor ☐deep ☑açıklama ☐offline  min izlenme [0] [AI prompt…] │  ← seçenekler
│ ▓▓▓░░░░░░ animasyonlu ilerleme çubuğu                          │  ← yalnız istek sırasında
│ 24 sonuç · kaynak: db · 52766 ms · AI açık · AI prompt ✓ · DB: 128 │ meta
│ #  BAŞLIK  KANAL  SÜRE  İZLENME  SKOR  VİDEO ID  URL           │  ← sonuç tablosu (8 kolon)
│ 1  video..  Tarkan 4:02  168.3 M  [95]  NAHRpEqg  https://watch? │
└─────────────────────────────────────────────────────────────────┘
```

## Kontroller

| Kontrol | Değerler | Eşdeğer API parametresi |
|---|---|---|
| Sorgu kutusu | serbest metin · **Enter** ile ara | `q` |
| Sonuç limiti | 12 / 24 / 50 / 100 / 500 / 1000 / 5000 | `limit` |
| Sıralama | ilgi / izlenme / tarih / puan | `sort` |
| Tür | tümü / video / şarkı / short | `filter` |
| **AI skor** (varsayılan açık) | ✓/☐ | `ai=1` |
| **deep** | ✓/☐ | `deep=1` |
| **açıklama** (varsayılan açık) | ✓/☐ | `explain=1` |
| **offline** | ✓/☐ | `offline=1` |
| min izlenme | sayı | `minViews` |
| **AI prompt** | serbest metin (opsiyonel) | `prompt` |

## Skor rozetleri

| Rozet | Anlam |
|---|---|
| **Yeşil `nn`** | AI skoru (`aiScore`) — üst sıralarda `> Türkçe gerekçe` satırı görünür |
| **Gri `nn`** | Deterministik skor (AI kapalı, bütçe doldu ya da model o videoyu puanlamadı) |

Meta satırı: `kaynak` (`ram/disk/db/youtube`) · `tookMs` · `AI prompt ✓` (prompt gönderildiyse) · `DB: N` · `deep:` genişletilmiş sorgular.

**Tablo kolonları (8):** `#` · Başlık (video linki) · Kanal · Süre · İzlenme · Skor · **Video ID** · **URL** (tam `watch?v=` bağlantısı, tıklanabilir).

## Davranış notları

- İstek sürerken buton kilitlenir + animasyonlu bar görünür; bitince geri gelir.
- Çift Enter/yarış kilidi var (`window.__busy`) — aynı sorgu iki kez gönderilmez.
- Sonuçlar sunucu tarafında önbelleklenir (RAM + 30 gün disk): aynı sorgu ikinci kez ~10 ms döner.
- `deep` açıkken yanıt 15-40 sn sürebilir (2 AI çağrısı + gerekirse YouTube toplama).

## Sorun giderme

| Belirti | Neden | Çözüm |
|---|---|---|
| "Hata: ... API çalışıyorsa `nova serve` açık olmalı (3100)" | API kapalı | `npm run serve` (ikisini de başlatır) veya portu kontrol et |
| Tablo boş + "Sonuç yok" | DB boş/ offline | `offline` işaretini kaldır veya CLI: `npm run nova -- collect "<sorgu>" --target 100` |
| AI rozetleri hep gri | Key yok/bozuk/bütçe | `.env` → `OPENROUTER_API_KEY`; arama yine çalışır (fail-safe) |
| 429 görünür | 60 istek/dk sınırı | kısa süre bekle |

API detayı → [API.md](API.md) · CLI → [CLI.md](CLI.md) · Kurulum → [INSTALL.md](INSTALL.md)
