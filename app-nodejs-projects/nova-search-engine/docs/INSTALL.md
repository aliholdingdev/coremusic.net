# NovaSearch — Kurulum

## Gereksinimler

| | |
|---|---|
| Node.js | **≥ 22.5.0** (yerleşik `node:sqlite` + FTS5 — native modül yok) |
| Paket yöneticisi | npm |
| Bağımlılık | tek paket: `youtubei.js@^18.1.0` |
| İnternet | kurulum + YouTube toplama/AI için; `-offline` modda gerekmez |

## Kurulum (4 adım)

```bash
cd app-nodejs-projects/nova-search-engine
npm install
cp .env.example .env      # sonra .env'i düzenle (AI key isteğe bağlı)
npm run nova -- --help    # doğrulama: yardım metni basılmalı
```

## `.env` değişkenleri

| Değişken | Varsayılan | Açıklama |
|---|---|---|
| `OPENROUTER_API_KEY` | *(boş)* | AI anahtarı — **yalnız** `.env`'de tutulur (`.md/.json/.log`'a yazma yasak). Boşsa AI katmanı devre dışı, arama bozulmaz. |
| `OPENROUTER_MODEL` | `nvidia/nemotron-3-super-120b-a12b:free` | Ana model |
| `OPENROUTER_FALLBACK_MODELS` | `google/gemma-4-31b-it:free,nvidia/nemotron-3-ultra-550b-a55b:free` | Virgülle ayrılmış fallback zinciri |
| `AI_ENABLED` | `true` | AI anahtarına rağmen katmanı kapatır (`false`) |
| `AI_TIMEOUT_MS` | `30000` | AI bütçesi (tüm model zinciri **paylaşır**) |
| `DB_SQLITE_PATH` | `./data/novasearch.db` | Kalıcı DB (ilk açılışta otomatik oluşur) |
| `API_PORT` | `3100` | REST API portu |
| `API_HOST` | `127.0.0.1` | API/Web dinleme adresi |
| `WEB_PORT` | `3110` | Web GUI portu |
| `LOG_LEVEL` | `info` | `debug` → AI çağrı notları stderr'e |
| `NOVA_OFFLINE` | `0` | `1` → YouTube'a asla gitmez (global offline) |

> `.env` gitignored'dır (`.gitignore:4`) — commit'e girmez, yalnız `.env.example` girdi.

## Doğrulama

```bash
npm run typecheck   # tsc --noEmit → 0 hata
npm test            # node --test → 13/13
npm run stats       # DB sayaçları (DB otomatik oluşur)
```

Sunucu testi:

```bash
npm run serve
# → NovaSearch API: http://127.0.0.1:3100/api/v1/health
# → NovaSearch Web: http://127.0.0.1:3110/
```

Tarayıcıda <http://127.0.0.1:3110/> aç, `Ctrl+C` ile durdur.

## Sorun giderme

| Belirti | Neden | Çözüm |
|---|---|---|
| `EADDRINUSE` 3100/3110 | Eski süreç açık | `Get-NetTCPConnection -LocalPort 3100,3110 -State Listen` → PID'yi durdur |
| AI skor/gerekçe yok | Key yok/bozuk ya da bütçe doldu | `.env`'de key'i kontrol et; `LOG_LEVEL=debug` ile `[ai] scoreResults` notunu izle — arama yine çalışır |
| `yt-dlp benzeri` ağ hatası | YouTube erişim/ban | `-offline` kullan ya da biraz bekle; jitter 1.5–6 sn zaten koruyucu |
| Node sürüm hatası | `<22.5` | `node -v` → güncel Node kur |

## Sonraki adım

- CLI → [docs/CLI.md](CLI.md)
- REST API → [docs/API.md](API.md)
- Web arayüzü → [docs/WEB.md](WEB.md)
