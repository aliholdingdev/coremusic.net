---
type: architecture
category: l2
title: "Service Discovery"
date: 2026-08-08
updated: 2026-09-08
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Service Discovery

**Zorunlu Bağlantılar:** [[index]] · [[ADR-032-ipc-contract-versioning]] · [[../ecosystem/index]]

---

## 1. Amaç

Servis keşfi ve health check mekanizmasını tanımlar. [[ADR-032-ipc-contract-versioning]] ile uyumludur.

**Faz 2c dürüstlük notu (2026-09-08):** `ServiceDiscovery` sınıfı üretim kodunda **YOKTUR** (Faz 0: `shared/src/` 19 modül listesinde IPC/Discovery yok). Bu dosya **hedef tasarım dokümanıdır** — tüm IMPLEMENTED iddiaları §8 durum tablosuna tabidir.

---

## 2. Servis Haritası (7 Servis — ADR-039)

| Servis | Port | Protokol | Health Check | Durum |
|--------|------|----------|--------------|-------|
| Control Service | 81 | HTTP | `/health` | PLANNED (music domain kodu yok) |
| Media Service | 5000/6000 | HTTP | `/health` | PLANNED |
| Audio Service | 9741 | REST | `/health` | PLANNED |
| Audio Service | 9742 | WebSocket | — (WS health farklı) | PLANNED |
| Download Service | 3001 | HTTP/WS | `/health` | PLANNED (Node.js) |
| Device Service | — | BLE/WiFi/USB | cihaz düzeyi | PLANNED |
| Network Audio | — | WebRTC/P2P | P2P ping | PLANNED |
| AI Service | — | Internal | `/health` | PLANNED |

*Ortak kaynak: [[ADR-039-7-service-platform-architecture]] + index.md §6. Fiziksel gerçeklik: yalnız auth+home+assets kodda (Faz 0).*

---

## 3. Hedef Implementation

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class ServiceDiscovery
{
    private array $services = [
        'control' => ['host' => 'localhost', 'port' => 81],
        'media' => ['host' => 'localhost', 'port' => 5000],
        'audio' => ['host' => 'localhost', 'port' => 9741],
        'download' => ['host' => 'localhost', 'port' => 3001],
    ];

    public function getUrl(string $service, string $path): string
    {
        $config = $this->services[$service] ?? null;

        if (!$config) {
            throw new \RuntimeException("Unknown service: {$service}");
        }

        return "http://{$config['host']}:{$config['port']}{$path}";
    }

    public function healthCheck(string $service): bool
    {
        $url = $this->getUrl($service, '/health');
        $response = @file_get_contents($url);

        return $response !== false;
    }

    public function healthCheckAll(): array
    {
        $results = [];

        foreach ($this->services as $name => $config) {
            $results[$name] = $this->healthCheck($name);
        }

        return $results;
    }
}
```

**Tasarım uyarıları (hedef deseni iyileştirme adayları):**
1. `@file_get_contents` — timeout'suz; üretime curl wrapper + 5s timeout şart.
2. Sabit `localhost` — çoklu sunucu dağıtımında config dışarıdan gelmeli.
3. `healthCheck` bool döner — durum detayı (degraded vs down) kaybolur; §10 sözleşmesi bool yerine durum döndürmeli.

---

## 4. Health Check Response Sözleşmesi

```json
{
    "status": "healthy",
    "service": "control",
    "version": "4.0.0",
    "uptime": 12345,
    "timestamp": "2026-08-08T12:00:00Z"
}
```

**Alan sözleşmesi:**

| Alan | Tip | Değerler | Not |
|------|-----|----------|-----|
| `status` | string | `healthy` \| `degraded` \| `down` | 3 durum modeli |
| `service` | string | registry adı | eşleşme zorunlu |
| `version` | string | semver | servis kendi sürümü |
| `uptime` | int | saniye | proses başlangıcından |
| `timestamp` | string | ISO 8601 UTC | — |

Hata kodları [[architecture/03-contracts/api-error-codes]] standardına bağlanır; servis-başına ayrı format icat edilmez.

---

## 5. Durum Modeli

| Durum | Anlam | Response HTTP | Aksiyon |
|-------|-------|---------------|---------|
| `healthy` | Normal servis | 200 | Trafik devam |
| `degraded` | Kısmi yetenek (bağımlılı yavaş vb.) | 200 + detail | Trafik + alarm |
| `down` | Yanıt yok / hata | timeout/5xx | Fallback chain (§13) |

---

## 6. Registry Tasarımı (Hedef)

```
ServiceRegistry
  → konfig kaynağı: DomainConfig / env (hardcoded dizi üretimde yasak)
  → cache: APCu, anahtar "svc:registry", TTL 30sn
  → çözümleme: service adı → host:port
  → invalidation: deployment sonrası TTL doğal yenileme
```

| Öğe | Değer | Not |
|-----|-------|-----|
| Cache anahtar | `svc:registry` | ADR-007 namespace |
| TTL | 30 sn | Health frekansıyla uyumlu |
| Kaynak | config dosyası + env override | Secrets YASAK (Guardrail #15) |

---

## 7. Health Check Akışı

```
healthCheckAll() [cron veya on-demand]
  → her servis için (paralel PLANNED)
     → getUrl(service, '/health')
        → curl, timeout 5s
           ├─ 200 healthy   → sonuç: healthy
           ├─ 200 + degraded detay → degraded
           ├─ 5xx / timeout → down + fallback chain (§13)
           └─ sonuç cache'e: "svc:health:{ad}" TTL 15sn
```

---

## 8. Gerçek Kod Durumu (Faz 0 Doğrulaması)

| Öğe | Durum | Kanıt |
|-----|-------|-------|
| `ServiceDiscovery` sınıfı | **PLANNED** — `shared/src/` içinde yok | 19 modül listesi (Faz 0) |
| `/health` endpoint'leri | **PLANNED** — hiçbir domain'de route yok | routes.php incelemesi beklemede |
| Registry cache | **PLANNED** | CacheManager tüketici listesi (l0 §15) |
| IPC katmanı | **PLANNED** | ADR-032 kapsam onayı bekliyor |
| `ecosystem/service-health-check.md` | DOKÜMAN mevcut | `.ai/ecosystem/` (7 dosya) |

**Sonuç:** Bu dosyanın §2-§7 içeriği tamamı hedef tasarım. "Servis keşfi aktif" iddiası yapılamaz — kod gelene dek PLANNED.

---

## 9. Timeout Matrisi

| İşlem | Timeout | Not |
|-------|---------|-----|
| Health check tek servis | 5 sn | ADR-032 |
| Servisler arası API çağrısı | 10 sn | İş yüküne göre endpoint bazlı aşılabilir |
| WebSocket handshake | 5 sn | 9742 |
| Registry cache TTL | 30 sn | §6 |
| Health cache TTL | 15 sn | §7 |

---

## 10. Load Balancing Stratejisi (Hedef)

| Strateji | Kullanım Alanı | Not |
|----------|----------------|-----|
| Round-robin | Çoklu stateless instance | media/download gibi |
| Sticky | Session-bağlı işler | auth servisi (session affinity) |
| Tek instance | Audio service (donanım bağlı) | 9741/9742 tek cihaz |

Fiziksel dağıtım tek makinadan çok makinaya geçtiğinde LB katmanı (nginx) devreye girer — [[../02-deployment/index]] kararıdır; uygulama içi LB yalnız registry fallback'inde.

---

## 11. Fallback Chain (Servis Down)

```
Audio Service down tespiti (§7)
  → 1. Retry (1 sn aralık, max 2)
     → 2. Degraded mode: DSP/EQ özellikleri kapat → oynatma devam (local playback)
        → 3. Kullanıcı bildirimi: "Ses servisi kullanılamıyor" (UI katmanı)
           → 4. log CRITICAL + escalasyon (L1→L2)
```

İlke: Core müzik deneyimi Audio Service'e bağımlıdır; kademeli bozulma tam kesintiyi tercih eder (Offline-First ilkesiyle uyum — brain Edge Cases).

---

## 12. Discovery Kullanım Noktaları (Hedef)

| Tüketici | Kullanım | Servis |
|----------|----------|--------|
| home domain | validate-key POST | control/auth (gerçekte HomeAuthBridge sabit URL — PLANNED discovery'ye geçiş) |
| media panel | kütüphane sorguları | control |
| download tetikleme | indirme kuyruğu | download (Node.js) |
| AI panel | öneri istekleri | AI service |

**Not:** Mevcut tek gerçek cross-domain çağrı `HomeAuthBridge → validate-key`'dir ve **sabit URL** kullanır (185 satır kod okuması) — discovery'ye geçiş hedef mimari kararıdır, mevcut davranış çalışmaktadır.

---

## 13. Test Senaryoları (PLANNED)

| # | Senaryo | Beklenen |
|---|---------|----------|
| 1 | Healthy servis health check | 200 + status:healthy |
| 2 | Durdurulmuş servis | down + fallback adım 1 |
| 3 | 2 retry sonrası down | degraded mode tetiklenir |
| 4 | Timeout (>5s) | down sayılır |
| 5 | Registry'de olmayan ad | RuntimeException |
| 6 | Cache TTL dolması | registry yenilenir |
| 7 | healthCheckAll paralel | tüm servisler sorgulanır |
| 8 | Degraded detay alanı | 200 + detail |
| 9 | Yanlış port | down (bağlantı hatası) |
| 10 | HomeAuthBridge sabit URL | discovery'siz çalışmaya devam (geriye uyum) |

---

## 14. Diagnostics (tekrarlanabilir)

```powershell
# 1. ServiceDiscovery sınıfı var mı (beklenen: yok → PLANNED doğru)
Get-ChildItem -LiteralPath "shared\src" -Recurse -Filter "ServiceDiscovery.php" -ErrorAction SilentlyContinue

# 2. IPC/Discovery klasörü (beklenen: yok)
Test-Path -LiteralPath "shared\src\Ipc"

# 3. Mevcut cross-domain çağrı kanıtı
Select-String -LiteralPath "home.coremusic.net\include\Auth\HomeAuthBridge.php" -Pattern "validate-key"

# 4. ecosystem doküman karşılığı
Get-ChildItem -LiteralPath ".ai\ecosystem" -Filter "*.md" | Select-Object Name

# 5. Port taraması (çalışan servisler — ortam bağımlı)
Test-NetConnection -ComputerName localhost -Port 81 -WarningAction SilentlyContinue | Select-Object TcpTestSucceeded
```

---

## 15. SSS

**S: Servis keşfi şu an çalışıyor mu?**
C: Hayır — `ServiceDiscovery` kodu yok (§8). Tek gerçek cross-domain hattı HomeAuthBridge'in sabit URL'idir. Bu doküman hedef tasarım ve geçiş planıdır.

**S: Neden `@file_get_contents` önerilmiyor?**
C: Timeout'suzdur — down servis 30-60sn PHP sürecini bloklar. Curl + 5s timeout §9 sözleşmesinin gereğidir.

**S: Health check kim çağırır?**
C: Hedef: cron/ondemand monitor (02-deployment observability). Şu an çağıran yok — endpoint'ler de yok.

**S: `svc:registry` neden APCu?**
C: Okuma-yoğun, değişim seyrek (deployment anı) — APCu idealdir; Redis PLANNED'e taşınırsa anahtar aynı kalır (l0 cache soyutlaması sayesinde).

**S: HomeAuthBridge discovery'ye geçmeli mi?**
C: PLANNED karar — mevcut sabit URL çalışıyor (tek hedef, tek auth domain). Çoklu auth instance/dağıtım geldiğinde geçer. Şimdilik değişiklik gereksiz (YAGNI).

**S: 7 servis yerine 8 satır var — AI Service?**
C: ADR-039 7 servis + AI ayrı listelenir (index.md §6: PHP+Python PLANNED). Numaralandırma kaynak farklılığını yansıtır; ikisi de PLANNED.

**S: Bu doküman riskli mi — hep PLANNED?**
C: Hayır, tam tersi: Faz 0'da doküman-kod sapması "IMPLEMENTED yazılmış ama kod yok" satırlardan doğdu. PLANNED etiketi dürüst ilerleme raporudur.

---

## 16. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Örnek kodun implement edilmiş sanılması | Yüksek | Orta | §8 durum tablosu + §1 not |
| 2 | Timeout'suz health check | Orta (kod yazılırken) | Yüksek | §9 matris + curl zorunluluğu |
| 3 | Hardcoded service listesi | Orta | Orta | §6 registry config dışarıdan |
| 4 | healthCheck bool bilgi kaybı | Orta | Düşük | §10 3-durum modeli |
| 5 | IPC ADR-032 kapsam öncesi kod yazımı | Düşük | Yüksek | ADR kapısı |

---

## 17. İzlenebilirlik Tablosu

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| ServiceDiscovery YOK | shared/src glob | Faz 0 (2026-09-08) |
| IPC klasörü YOK | Test-Path shared/src/Ipc | Faz 0 |
| 7 servis port haritası | ADR-039 + index.md §6 | Çapraz okuma |
| HomeAuthBridge sabit URL | HomeAuthBridge.php (185s) | Kod okuma |
| ecosystem dokümanı var | .ai/ecosystem/ (7 md) | Dosya sayımı |
| health sözleşmesi | Bu dosya §4 | Hedef tasarım etiketi |

---

## 18. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 4.0.0 | 2026-08-08 | İlk doküman |
| 5.0.0 | 2026-09-08 | Faz 2c: §1/§8 PLANNED dürüstlük notları; §5 durum modeli; §6 registry tasarımı; §9 timeout; §11 fallback chain; §12 kullanım noktaları; §13-17 test/diagnostics/SSS/risk/izlenebilirlik |

---

## 18. Health Check Curl Örneği (timeout'suz sorununun çözümü)

§3'teki `@file_get_contents` yerine hedef implementasyon:

```php
<?php
declare(strict_types=1);

final class HealthProbe
{
    public function probe(string $url, int $timeoutSec = 5): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeoutSec,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => false,
        ]);

        $body   = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($body === false || $status !== 200) {
            return ['status' => 'down', 'detail' => $err ?: "HTTP {$status}"];
        }

        $data = json_decode((string) $body, true);
        return [
            'status'  => $data['status'] ?? 'degraded',
            'detail'  => $data ?? [],
        ];
    }
}
```

**Farklar (§3 örneğine göre):** timeout var, HTTP status sınıyor, JSON decode ediyor, 3-durum modeli döndürüyor. Bu sınıf da PLANNED'tir — §8 kapsamında.

---

## 19. Port Çakışma Matrisi

| Port | Atanan | Çakışma Riski | Not |
|------|--------|---------------|-----|
| 80 | admin (hedef) / IIS varsayılan | Yüksek (Windows) | IIS paralel kuralı — Q&A kararı (MEMORY) |
| 81 | music/control | Orta | Apache alternatif port |
| 3001 | download (Node.js) | Düşük | — |
| 3306 | MySQL | — | DB |
| 5000/6000 | media | Orta (macOS AirPlay 5000!) | macOS dev ortamında çakışma tespiti |
| 6379 | Redis (PLANNED) | Düşük | — |
| 9741/9742 | audio REST/WS | Düşük (özel seçim) | — |

Not: Port çakışması deployment ortam kararıdır; registry config'i ortam bazlı override desteklemelidir (§6).

---

## 20. Degraded Mode — Servis Başına Detay

| Servis | Degraded Anlamı | Kullanıcı Etkisi |
|--------|-----------------|------------------|
| Control | Session/auth çalışıyor, bazı özellikler kapalı | Login devam eder |
| Media | Metadata servisi yavaş — oynatma local | Listeler gecikmeli |
| Audio | DSP/EQ kapalı, temel oynatma local playback | Kalite düşer, kesinti yok |
| Download | Kuyruk duraklar | Yeni indirme başlamaz |
| AI | Öneriler boş döner | Panel boş bölüm |

İlke: §11 fallback zinciri servis-bazlı bu matrisle birlikte çalışır — "her şey ya da hiçbir şey" değil.

---

## 21. HomeAuthBridge → Discovery Geçiş Planı

```
ŞİMDİ (IMPLEMENTED):
  HomeAuthBridge → sabit "auth.coremusic.net/validate-key" (kod içi)

HEDEF (PLANNED):
  → ServiceDiscovery::getUrl('auth', '/validate-key')
     → registry config'ten host çözümü

GEÇİŞ ADIMLARI:
  1. ServiceDiscovery + HealthProbe sınıfları üretilir (ADR-032 onayı)
  2. Registry config'e 'auth' kaydı
  3. HomeAuthBridge ctor'a discovery enjeksiyonu (DIP — sabit kaldırılır)
  4. Geriye uyum: discovery yoksa eski sabit kullan (feature flag)
  5. Test: §13 senaryo 10 (geriye uyum) + yeni discovery hattı
```

Karar kaydı: geçiş PLANNED — YAGNI gereği tek auth instance döneminde acele edilmez (§15 SSS).

---

## 22. Registry Config Şeması (Hedef Örnek)

```php
// shared/config/services.php (PLANNED)
return [
    'auth'     => ['host' => 'auth.coremusic.net',  'port' => 80,  'health' => '/health'],
    'home'     => ['host' => 'home.coremusic.net',  'port' => 80,  'health' => '/health'],
    'control'  => ['host' => 'localhost',            'port' => 81,  'health' => '/health'],
    'media'    => ['host' => 'localhost',            'port' => 5000,'health' => '/health'],
    'audio'    => ['host' => 'localhost',            'port' => 9741,'health' => '/health'],
    'download' => ['host' => 'localhost',            'port' => 3001,'health' => '/health'],
];
```

Kurallar: secret YOK (Guardrail #15) · host env override edilebilir · health path sabit `/health` · dosya konumu `shared/config/` (routes.php komşusu).

---

## 23. Ek Test Senaryoları

| # | Senaryo | Beklenen |
|---|---------|----------|
| 11 | Curl connect timeout (2sn) | down + "connection" detail |
| 12 | HTTP 500 yanıtı | down (200 değil) |
| 13 | HTTP 200 ama JSON bozuk | degraded (parse hatası toleransı) veya down — kod kararı |
| 14 | 302 yönlendiren health | down (FOLLOWLOCATION false — health redirect takip etmez) |
| 15 | Registry cache dolu + config değişimi | TTL 30sn içinde yeni değer |
| 16 | Paralel healthCheckAll | toplam süre = max(servis süreleri) (seri değil) |

---

## 24. Ek SSS

**S: Health endpoint neden `/health` sabit?**
C: Standartlaşma — monitoring araçları tek pattern taraması yapar. Servis-başına farklı path registry config'ten aşılabilir (§22 `health` alanı).

**S: FOLLOWLOCATION neden false?**
C: Health probe'un bir redirect'i takip etmesi yanlış servise sorgu gönderebilir (open redirect türevi). 302/301 görmek zaten anomali → down.

**S: Degraded nasıl tespit edilir — servis kendisi mi söyler?**
C: Servis kendi `status: degraded` döner (§4 sözleşme) — bağımlılık yavaşlığı gibi iç bilgiyi yalnız o bilir. Probe sadece okur.

**S: WebSocket (9742) health'i neden yok?**
C: WS handshake farklı protokoldür; REST kardeşi (9741) üzerinden proxy health yapılır veya WS ping/pong kullanılır — Faz 2h (06-audio) kararı.

**S: Paralel health check nasıl?**
C: PHP'de curl_multi veya async ekosistemi (Node tarafında trivial). Seri tarama 5 servis × 5sn timeout = 25sn blok riski — paralel zorunlu hedeftir (§7 PLANNED).

## 28. Uygulama Fazları (Discovery Devreye Alma Yol Haritası)

| Faz | İçerik | Önkoşul | Durum |
|-----|--------|---------|-------|
| D1 | `HealthProbe` sınıfı (curl + timeout) | ADR-032 onayı | PLANNED |
| D2 | `shared/config/services.php` + Registry cache (APCu `svc:registry`) | D1 | PLANNED |
| D3 | Her servis `/health` endpoint'i (domain başına route) | D2 | PLANNED |
| D4 | `healthCheckAll()` paralel + observability bağlantısı | D3 + 02-deployment | PLANNED |
| D5 | HomeAuthBridge discovery geçişi (feature flag'li) | D2 + test | PLANNED |

Kural: D1-D5 sıralıdır; D3'süz health taraması anlamsızdır (uyarlanacak endpoint yok). Bu fazlar [[engine.md]] §12.1 Faz 2c altına işlenir.

---

## 29. Sistemler Arası Sözleşme Tablosu

| Sözleşme | Taraf 1 | Taraf 2 | Taşıyıcı |
|----------|---------|---------|----------|
| Health JSON (§4) | Her servis | Probe/monitor | HTTP GET `/health` |
| Registry config (§22) | Deployment | ServiceDiscovery | `shared/config/services.php` |
| `svc:registry` cache | Registry | CacheManager | ADR-007 namespace |
| `svc:health:{ad}` cache | Probe | Okuyan herkes | TTL 15sn |
| Fallback zinciri (§11) | Caller | Servis | Retry→degraded→bildirim |
| ADR-032 sürüm | Servis | Servis | IPC sözleşme sürümü |

Bu tablo, discovery'nin L2 içindeki kontrat yüzeyidir — L0 cache, L2 routing ve ecosystem (health-check dokümanı) üçgenini bağlar.

---

## 30. Tek Başına Auth Hattının Özel Durumu

Gerçeklik (Faz 0): ekosistemde çalışan tek servisler arası çağrı HomeAuthBridge'dir ve **discovery'siz** çalışır:

| Konu | Şimdi | Hedef (D5) |
|------|-------|------------|
| URL kaynağı | Kod içi sabit | Registry config |
| Health | Yok | `/health` + probe |
| Hata davranışı | Retry 2 + TTL 300 | Fallback chain |
| Test | Senaryo 10 (geriye uyum) | D5 test seti |

**Geçiş ilkesi:** Çalışan hatyı kırma — feature flag (`discovery_enabled`) ile şalterli geçiş; flag dev'de açık, prod'da D5 doğrulamasından sonra açılır. Bu desen l0 §29 prosedürünün uygulama örneğidir.

---

## 31. Ek Test Senaryoları

| # | Senaryo | Beklenen |
|---|---------|----------|
| 17 | D5 flag kapalı + discovery hazır | HomeAuthBridge eski sabit URL'i kullanır |
| 18 | D5 flag açık + auth down | fallback zinciri + kullanıcıya "oturum açılamadı" |
| 19 | Registry config eksik servis | RuntimeException + log CRITICAL |
| 20 | İki servis aynı port (config hatası) | ilk çağrıda bağlantı tuhaflığı — config validation zorunlu (D2) |
| 21 | healthCheckAll tek servis döngüde down | diğerlerinden etkilenmez (izolasyon) |

---

## 32. Ek SSS

**S: Neden `services.php` PHP dosyası — JSON/YAML değil?**
C: Vault konvansiyonu: mevcut config'ler PHP (`routes.php`, `oauth-platforms.php`). include-dizi deseni, opcache ile hızlı ve tip uyumludur. YAML ayrıştırıcı bağımlılığı gereksiz (YAGNI).

**S: Discovery devreye alınca HomeAuthBridge'in 2-retry davranışı değişir mi?**
C: Hayır — §21 yalnız URL kaynağını değiştirir; retry/TTL davranışı HomeAuthBridge'in kendi sözleşmesidir. Karışım önlenir: discovery adres çözer, davranış sınıfta kalır.

**S: Health cache 15sn içinde servis ölürse?**
C: En kötü 15sn + probe timeout (5sn) = 20sn gecikme ile down fark edilir. Kritik servisler (control/auth) için probe frekansı artırılabilir — deployment kararı.

**S: config validation ne yapar (test 20)?**
C: D2'de registry yükleme anında: duplicate port, duplicate ad, eksik health path kontrolü. Erkek hatayı başlangıçta yakalamak, runtime tuhaflığından iyidir.

**S: Bu dokümandaki örnek kod (HealthProbe) nihai mi?**
C: Hedef desen — D1 ADR'sinde final hali tartışılır (interface sözleşmesi, DI enjeksiyonu vb.). Referans çalışma kuralı geçerli.

---

## 33. Faz 2c Kapanış Katkısı

Bu dosyanın revizyonu Faz kontrol listesine şu katkıları verdi:

1. **PLANNED dürüstlüğü:** ServiceDiscovery kod yokluğu resmileşti (§8) — doküman-kod sapması kapanıyor.
2. **Sözleşme öncülüğü:** Health JSON şeması (§4) — D3 endpoint'lerinin ortak dili.
3. **Geçiş planı:** HomeAuthBridge şalterli geçiş (§21) — çalışan hatyı koruyarak.
4. **Port/çakışma bilgisi:** §19 — deployment öncesi risk taraması.
5. **Test öncülleri:** §13+§31 — D1-D5 test setinin iskeleti.

---

## 25. Risk Kaydı Ek

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 7 | Health redirect takibi | Düşük | Orta | §24 SSS — FOLLOWLOCATION false |
| 8 | Port çakışması (macOS 5000) | Orta | Orta | §19 matris + env override |
| 9 | Seri health tarama bloğu | Orta | Orta | §23-16 paralel zorunluluk |
| 10 | Registry'de secret sızıntısı | Düşük | Kritik | §22 Guardrail #15 kuralı |
| 11 | Discovery'siz döneme "aktif" yazımı | Yüksek | Orta | §8 tablo + §1 not |

---

## 26. İzlenebilirlik Tablosu Ek

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| HealthProbe PLANNED | Bu dosya §18 | Hedef desen |
| Port matrisi | §19 | ADR-039 + MEMORY Q&A (IIS/Apache) |
| Degraded matris | §20 | Hedef tasarım |
| Geçiş planı 5 adım | §21 | PLANNED — YAGNI gerekçeli |
| Config şeması | §22 | Hedef örnek |
| macOS 5000 çakışması | §19 | Genel platform bilgisi — ortam testi önerilir |

---

## 27. Revizyon Geçmişi (Güncel)

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 5.0.0 | 2026-09-08 | Faz 2c tam revizyon (§1-17) |
| 5.1.0 | 2026-09-08 | §18 HealthProbe curl örneği; §19 port matrisi; §20 degraded detay; §21 HomeAuthBridge geçiş planı; §22 config şeması; §23-26 ekler |
| 5.2.0 | 2026-09-08 | §28 uygulama fazları (D1-D5); §29 sözleşme tablosu; §30 auth hattı özel durumu; §31-33 ekler; sözlük |

---

## 34. Sözlük

| Terim | Tanım |
|-------|-------|
| **Service Discovery** | Servis adreslerinin dinamik çözümlenmesi |
| **Health Probe** | `/health` sorgulayıcı (curl + timeout) |
| **Registry** | Servis ad → host:port kayıt defteri |
| **Degraded** | Kısmi yetenek durumu |
| **Fallback Chain** | Retry → degraded → bildirim sırası |
| **Circuit Breaker** | Sürekli hatalı servise istek kesme deseni (PLANNED) |
| **Feature Flag** | Şalterli geçiş anahtarı (discovery_enabled) |
| **`svc:` Namespace** | Registry/health cache anahtar öneki (ADR-007) |
| **Paralel Probe** | Servislerin eşzamanlı sorgulanması |
| **Sticky** | Session-affinity yönlendirme |

---

## 35. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.2.0 |
| **Bölüm Sayısı** | 35 |
| **Durum Etiketi** | TAMAMI PLANNED (§8) — dürüst kapsam |
| **ADR Uyumlu** | ✅ 032 (+039/084/086 çapraz) |
| **Test Senaryosu** | 21 (§13 + §23 + §31) |
| **Uygulama Fazları** | D1-D5 yol haritası (§28) |
| **Risk Kaydı** | 11 kalem |
| **Zero Hallucination** | ✅ — "aktif" iddiası hiç yok |
| **Şablonlar** | §18 HealthProbe + §36 `/health` üretim şablonu |
| **Geçiş Deseni** | §21 feature flag'li HomeAuthBridge geçişi (çalışan hat koruması) |
| **Çapraz Doküman** | ecosystem 7-md seti, l0 §15 cache tüketici listesi, 02-deployment monitoring |
| **Ölçüm Disiplin** | Paralel probe zorunlu (§24 SSS) — seri tarama 25sn blok riski |
| **Varlık Durumu** | ServiceDiscovery/HealthProbe/IPC sınıfları YOK (§8) — doküman hedef tasarım |
| **Geçiş Eşiği** | D5 flag prod'a yalnız D1-D4 test geçişinden sonra |
| **Faz Bağlantısı** | §28 D1-D5 → engine §12 faz planına işlenir |
| **Health Sözleşme** | §4 JSON (status/service/version/uptime/timestamp) — D3 endpoint'lerinin ortak dili |
| **Probe Disiplin** | Curl + timeout + FOLLOWLOCATION=false (§18) — @file_get_contents yasak |

---

## 38. Kısa SSS

**S: Probe sırasında servisin kendisi yoğunsa?**
C: health endpoint'i hafif olmalıdır (§36 kural — iş mantığı içermez); yoğunluk probe'u yavaşlatır ama down saydırmaz (200 + status alanı yeterli).

**S: `svc:` anahtarları servis restart'ta yaşar mı?**
C: APCu process-local — restart'ta silinir; ilk probe yeniden doldurur (TTL doğal yenileme). Kalıcılık gerekmez.

**S: Bu doküman PLANNED ama ecosystem'de health-check dokümanı var — çelişki mi?**
C: Hayır — `ecosystem/service-health-check.md` hedef davranışı tanımlar (aynı PLANNED hatta); bu dosya L2 discovery perspektifini ve geçiş planını (§21) ekler. İkisi tamamlar, çelişmez.

**S: Faz 2e contracts ile bu dokümanın ilişkisi?**
C: contracts/api-observability (203) health endpoint sözleşmelerini detaylandırır — D3 üretiminde bu dosya + contracts birlikte okunur.

---

## 36. `/health` Endpoint Üretim Şablonu (D3)

Her domain'e health route eklemenin standart adımları (auth domain örneği — IMPLEMENTED altyapıyla):

1. **Handler:** `auth.coremusic.net/handler/HealthHandler.php` — `IMiddleware`/handler sözleşmesine uyumlu; bağımlılık durumu özetler (DB `SELECT 1`, cache erişimi).
2. **Route:** `auth-routes.php`'e `health` key — `requiresAuth: false`, `cacheable: false` (her istek taze).
3. **Yanıt:** §4 JSON sözleşmesi (`status/service/version/uptime/timestamp`) — `version` composer.json'dan.
4. **Güvenlik:** Rate limit dışı tutulur mu? HAYIR — probe istekleri de sayılır; ayrı `rl:health:` limit düşük tutulur.
5. **Test:** §13 senaryo 1 + cache TTL davranışı.
6. **Kayıt:** routes.php diff → faz raporu; IMPLEMENTED etiketi kod kanıtıyla.

Şablon kuralı: health handler'ı iş mantığı İÇERMEZ — yalnız bağımlılık ping'i; aksi halde health kendisi down olur (döngüsel risk).

---

## 37. İlgili Dosyalar (Genişletilmiş)

| Dosya | İlişki |
|-------|--------|
| [[index]] | L2 ana dizin |
| [[../ecosystem/7-service-integration]] | 7-servis entegrasyon hedefi |
| [[../ecosystem/service-health-check]] | Health dokümanı (kardeş) |
| [[../02-deployment/index]] | Monitoring/observability sahibi |
| [[../l0-infrastructure/index]] | CacheManager (registry cache) |
| [[../l1-security/index]] | `rl:` rate limit (health istekleri dahil) |
| [[ADR-032-ipc-contract-versioning]] | Sürümleme sözleşmesi |
| [[ADR-039-7-service-platform-architecture]] | Servis kümesi |

---
