---
node_id: K012-services/playback-service/playback-service-surucu
node_type: module
status: draft
parent_id: K012-services/playback-service
role: surucu
children: []
depends_on:
  - "K012-services/playback-service/playback-service-anayasa.md"
evidence:
  - "home.coremusic.net/include/Class/ComponentLoader.php — make/display satır 85-106, 2026-10-13 okundu"
  - "home.coremusic.net/include/Class/AbstractComponent.php — render satır 34-44, 2026-10-13 okundu"
  - "home.coremusic.net/pages/components/player-info.php — 1-65 TAMAMEN okundu, 2026-10-13"
  - "home.coremusic.net/footer.php — 1-65 okundu, 65+ okunmadı (§10 boşluk)"
validation:
  - "Sürüş adımları anayasa §4/§14 ile çapraz doğrulandı (2026-10-13)"
open_questions:
  - "footer.php 65+ (HTML gövde, JS çağrıları) okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "PlayerInfoComponent.js akışı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# playback-service — Surucu (kademe-3 · module · rol: surucu)

**ELI15:** Bu dosya playback görselleştirmesinin **sürüş sırasını** verir: sayfa render anında hangi adım hangi satırdan geçer, varyant seçimi nerede olur, JS köprüsü nasıl kurulur. Playback'ın durum-üretim akışı repo'da bulunamadı (anayasa §6/§19) — bu sürüş yalnızca **görüntüleme** içindir.

**Sınır (ne DEĞİL):** Session sözlüğü anayasa §2'dedir · verbatim bloklar anayasa §12/§20'dedir · yazan taraf taraması anayasa §19'dadır.

**Sürüş kuralı:** Her adım `Dosya: … / Satır: …` ile bağlanır; adımlar gerçek kod sırasıdır.

**Durum:** draft · PHP runtime yok — canlı doğrulama ⚠️ VERIFICATION REQUIRED.

## §1 Ana Akış — Bileşen Render

| # | Adım | Satır | Devam | Ayrılma |
|---|---|---|---|---|
| 1 | `ComponentLoader::display('player-info', $variant)` | Loader 103-106 | — | — |
| 2 | `make()` → registry arama (`player-info` → sınıf) | Loader 87-88 | kayıt varsa | `\InvalidArgumentException` "Bilinmeyen bileşen" (88) |
| 3 | `RecentTracksComponent` özel dalı kontrolü | Loader 90-92 | player-info → `new $class($variant)` (94) | — |
| 4 | ctor: session okuma (6 string + 1 int + 1 URL) | PlayerInfo 40-47 | — | — |
| 5 | ctor: 11 asset yolu | PlayerInfo 48-58 | — | — |
| 6 | `render()` → `ob_start` | AbstractComponent 36 | — | — |
| 7 | `require $this->partial()` | AbstractComponent 38 | — | `\Throwable` → `ob_end_clean` + rethrow (40-43) |
| 8 | partial: varyant seçimi `$isWide = $this->variant->isWide()` | partial 14 | — | — |
| 9 | partial: `cmConfig` JSON üretimi (strip_tags → json_encode → htmlspecialchars) | partial 16-27 | — | — |
| 10 | Wide dalı (`player-info--wide`) veya embedded dalı (`now-playing--embedded`) | partial 29/49 | — | — |
| 11 | `ob_get_clean()` → string | AbstractComponent 39 | — | — |
| 12 | `echo` | Loader 105 | — | — |

**Yol hesabı:** `partial()` = `dirname(__DIR__, 2) . '/pages/components/' . key() . '.php'` (AbstractComponent 31) — `include/Class/` iki seviye yukarı → proje kökü → `pages/components/player-info.php`.

## §2 Varyant Karar Ağacı

| # | Karar | Satır | Sonuç |
|---|---|---|---|
| 1 | `HomeLayoutVariant` ctor argümanı gelir | PlayerInfo 36 / Loader 85 (default `Embedded`) | — |
| 2 | `variant->isWide()` (partial 14) | partial 29 | true → wide iskelet |
| 3 | — | partial 49 | false → embedded iskelet |
| 4 | Loader default'u | Loader 85 | parametre yoksa `Embedded` |

**Çağrı zinciri notu:** `isWide()` ayrıca `AbstractComponent::isWide()` (satır 81-84) ile de mevcuttur; partial tercihen `$this->variant->isWide()` kullanır (partial 14). İki yol aynı `readonly` property'ye bakar.

## §3 Alt Akış — cmConfig JSON Üretimi

| # | Adım | Satır | Katman |
|---|---|---|---|
| 1 | `song`/`artist` → `strip_tags` | partial 19-20 | etiket temizliği |
| 2 | `progress` → `seekPct` (int, escape gerektirmez) | partial 21 | — |
| 3 | `json_encode` + `JSON_HEX_TAG \| JSON_HEX_APOS` | partial 17, 23 | HTML tehlikeli karakter kaçışı |
| 4 | `htmlspecialchars(…, ENT_QUOTES, 'UTF-8')` | partial 16, 25 | tırnak kaçışı (tek tırnaklı `data-cm-config='…'`) |
| 5 | `data-cm-config='<?= $cmConfig ?>'` | partial 30, 50 | DOM'a yazım |

**Sıra kuralı:** `strip_tags` → `json_encode` (hex) → `htmlspecialchars` — üç katman asla atlanmaz; tek başına `htmlspecialchars` yetersizdi (JSON `{`/`"`), tek başına `json_encode` yetersizdi (tek tırnaklı atribüt).

## §4 Alt Akış — Footer Bar (okunan kısım)

| # | Adım | Satır |
|---|---|---|
| 1 | `DeviceManager::instance` (kurulu değilse) + viewport | footer 22-27 |
| 2 | `$h` closure (escape) | footer 29-31 |
| 3 | `ASSETS_URL` + `csp_nonce` | footer 33-34 |
| 4 | Session okuma (anayasa §2 sözlüğü) | footer 36-46 |
| 5 | 11 ikon yolu | footer 48-59 |
| 6 | Tier class: `--4k` / `--wide` / `--embedded` | footer 62-64 |
| 7 | `<footer>` + seekbar + meta (HTML) | footer 66-80 |
| 8 | — (blok dışı: gövde devamı) | ⚠️ footer 80+ okunmadı |

## §5 Hata Yolları

| Yol | Tetikleyen | Sonuç | Satır |
|---|---|---|---|
| Bilinmeyen anahtar | registry'de yok | `\InvalidArgumentException` — sayfa 500 üretir | Loader 88 |
| `ComponentInterface` olmayan sınıf | `register` çağrısı | `\InvalidArgumentException` (constructor) | Loader 68-70 |
| Partial bulunamazsa | `require` başarısız | `\Throwable` → buffer temiz + rethrow | AbstractComponent 38-43 |
| Session anahtarı yok | `?? $default` | Figma varsayılanı | PlayerInfo 40-46 |
| `current_art` yok | `??` | sabit asset yolu | PlayerInfo 47 |

**Gözlem:** `render` try/catch'i yakalar AMA yeniden-fırlatır (AbstractComponent 40-43) — sessiz yutma yok; tek kazancı output buffer temizliğidir.

## §6 Buffer ve Çıktı Sürüşü

| Olay | Satır | Davranış |
|---|---|---|
| `ob_start` | AbstractComponent 36 | partial çıktısı tamponlanır |
| `ob_get_clean` | 39 | tampon string olarak döner |
| `ob_end_clean` (hata) | 41 | tampon atılır, rethrow |
| `display` → `echo` | 46-49 / Loader 105 | output yazılır |

**Bileşen-vs-footer farkı:** Bileşen tamponsuz `echo` (Loader 105) — footer doğrudan `include` akışıdır; buffer stratejisi footer'da footer dosyasının kendi kontrolündedir (okunan kısımda buffer çağrısı YOK).

## §7 Zamanlama Kısıtları

| Kısıt | Değer | Satır |
|---|---|---|
| Ctor'da ağ/DB yok | PlayerInfo DB enjeksiyonu almaz | Loader 94 (genel dal) |
| Ctor'da dosya IO yok | yalnız session + asset string | PlayerInfo 40-58 |
| `render` tek `require` | partial dışında dosya yok | AbstractComponent 38 |
| Session okuma salt okunur | `$_SESSION[$key] ??` — yazım yok | AbstractComponent 64-73 |

**Sonuç:** Render kısa-özgülüdür (side-effect: yalnız echo); ağır iş (durum üretimi, ses) tamamen dışarıdadır — bulunamadı (anayasa §6).

## §8 Uç Nokta Eşlemesi (okunan kısımla sınırlı)

| Çağrı | Yer | Sürüş |
|---|---|---|
| `$loader->display('player-info', $variant)` | pages/home.php (belge örneği, Loader satır 18-22 yorumu) — home.php okunmadı → ⚠️ | §1 |
| `<footer>` render | footer.php include akışı (header/footer kabuğu) — include noktası okunmadı → ⚠️ | §4 |
| JS köprüsü | `data-cm-component="cm-player-info"` → `PlayerInfoComponent.js` (partial satır 10) | ⚠️ JS okunmadı |

## §9 Bilinen Boşluklar

| # | Boşluk | Durum |
|---|---|---|
| 1 | footer.php 80+ (gövde, JS çağrıları, buton sarmalayıcıları) | ⚠️ VERIFICATION REQUIRED |
| 2 | pages/home.php (display çağrı sırası) | ⚠️ VERIFICATION REQUIRED |
| 3 | `PlayerInfoComponent.js` (config okuma, komut gönderimi) | ⚠️ VERIFICATION REQUIRED |
| 4 | `DeviceManager::instance` implementasyonu | ⚠️ VERIFICATION REQUIRED |
| 5 | `php -l` kanıtı | ⚠️ VERIFICATION REQUIRED |
| 6 | Footer butonlarının (<img>) JS click bağlantısı | UNKNOWN — okunan kısımda handler yok |

## §10 İddia Kayıt Defteri (yeni URL yok)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| player-info.php 1-65 tam okundu (negatif/olumlu iddia) | URL yok — 2026-10-13 read kanıtı | 2026-10-13 | geçerli |
| Footer gövdesi okunmadı (eksiklik iddiası) | URL yok — kapsam sınırı | 2026-10-13 | geçerli |

**Yeni URL eklenmedi** — envanter kesişimi 0.

## §11 Satır Referans Sözlüğü

> Bu sözlük dosyada geçen her `Dosya: … / Satır: …` iddiasını tek yerde toplar; sürüş adımlarının kanıt bağı buradadır.

| Dosya | Satır | Ne | Kullanım |
|---|---|---|---|
| ComponentLoader.php | 68-70 | `ComponentInterface` olmayan sınıf reddi | §5 |
| ComponentLoader.php | 85 | `display` giriş + `Embedded` default'u | §2 |
| ComponentLoader.php | 87-88 | registry arama + "Bilinmeyen bileşen" | §1 · §5 |
| ComponentLoader.php | 90-92 | `RecentTracksComponent` özel dalı | §1 |
| ComponentLoader.php | 94 | `new $class($variant)` genel dal | §1 · §7 |
| ComponentLoader.php | 103-106 | `display('player-info', $variant)` çağrısı | §1 |
| AbstractComponent.php | 31 | `partial()` yol hesabı (`dirname(__DIR__, 2)`) | §1 |
| AbstractComponent.php | 34-44 | `render()` gövdesi | §1 |
| AbstractComponent.php | 36 | `ob_start` | §6 |
| AbstractComponent.php | 38 | `require $this->partial()` | §1 · §5 |
| AbstractComponent.php | 39 | `ob_get_clean()` | §6 |
| AbstractComponent.php | 40-43 | catch → `ob_end_clean` + rethrow | §5 · §6 |
| AbstractComponent.php | 46-49 | `display` → `echo` | §6 |
| AbstractComponent.php | 64-73 | session salt-okunur okuma | §7 |
| AbstractComponent.php | 81-84 | `isWide()` erişilebilir sarmalayıcı | §2 |
| PlayerInfoComponent.php | 36 | ctor variant argümanı | §2 |
| PlayerInfoComponent.php | 40-46 | `$_SESSION[$key] ?? $default` | §5 · §12 |
| PlayerInfoComponent.php | 46 | `progress_pct` default = **30** | §12 · §21 |
| PlayerInfoComponent.php | 47 | `current_art` default (asset yolu) | §5 |
| PlayerInfoComponent.php | 48-58 | 11 asset yolu | §1 · §15 |
| player-info.php | 10 | `data-cm-component="cm-player-info"` | §8 |
| player-info.php | 14 | `$this->variant->isWide()` | §2 |
| player-info.php | 16 | `htmlspecialchars` (cmConfig sarmalayıcı) | §3 · §14 |
| player-info.php | 17 | `JSON_HEX_TAG \| JSON_HEX_APOS` bayrakları | §3 |
| player-info.php | 19-20 | `strip_tags` (song/artist) | §3 |
| player-info.php | 21 | `progress` → `seekPct` | §3 |
| player-info.php | 23 | `json_encode` | §3 |
| player-info.php | 25 | ikinci `htmlspecialchars` | §3 · §14 |
| player-info.php | 29-30 | wide dal (`player-info--wide`) | §1 |
| player-info.php | 49-50 | embedded dal (`now-playing--embedded`) | §1 |
| footer.php | 22-27 | `DeviceManager::instance` + viewport | §4 |
| footer.php | 29-31 | `$h` escape closure | §4 · §14 |
| footer.php | 33-34 | `ASSETS_URL` + `csp_nonce` | §4 |
| footer.php | 36-46 | session okuma (anayasa §2 sözlüğü) | §4 · §12 |
| footer.php | 37 | `progress_pct` default = **65** | §12 · §21 |
| footer.php | 48-59 | 11 ikon yolu | §4 · §15 |
| footer.php | 62-64 | tier sınıfı (`--4k`/`--wide`/`--embedded`) | §2 · §4 |
| footer.php | 66-80 | `<footer>` + seekbar + meta HTML | §4 |
| footer.php | 80+ | okunmadı | §9 |

## §12 Session Anahtarı Sürüş Matrisi

> Anayasa §2 sözlüğünün sürüş karşılığı: hangi anahtar hangi dosyadan hangi satırda okunuyor.

| Anahtar (anayasa §2) | Tür | PlayerInfoComponent | footer.php | Sürüş notu |
|---|---|---|---|---|
| `current_song` | string | 40-47 (`??` default) | 36-46 | yazım yeri repo'da 0 isabet (anayasa §6) |
| `progress_pct` | int | 46 → default **30** | 37 → default **65** | **çelişki §21** — gerekçe yok → UNKNOWN |
| `volume` | string | 40-47 | 36-46 | yazım yeri bulunamadı (anayasa §6) |
| `current_art` | string/URL | 47 (`??` sabit asset) | 36-46 | kaçış uygulanmadığı iddiası §14 |
| 3 anahtar daha | string | 40-47 kapsamı | 36-46 kapsamı | anahtar adları anayasa §2'ye havale — bu dosyada uydurulmaz |
| `cmConfig` (DOM) | string | partial 16-27 üreteci | — | session değil, üretim verisi §3 |

**Ortak sürüş kuralı:** Her iki okuyucu da `?? $default` kalıbındadır — anahtar yoksa sessiz varsayılana düşülür, hata üretilmez (PlayerInfo 40-46 · footer 36-46).

**Yazım kanıtı yok:** `current_song =` / `progress_pct =` / `volume =` yazım-grebi `home.coremusic.net` içinde 0 isabet → bu anahtarları kimin doldurduğu **UNKNOWN** (anayasa §6/§19).

## §13 Varyant ve Tier Noktaları Kesişimi

| # | Nokta | Dosya/Satır | Karar |
|---|---|---|---|
| 1 | Loader default | ComponentLoader 85 | parametre yoksa `Embedded` |
| 2 | ctor geçirimi | PlayerInfoComponent 36 | gelen argüman `readonly` property olur |
| 3 | sarmalayıcı erişim | AbstractComponent 81-84 | `isWide()` ikinci yol |
| 4 | partial kararı | player-info.php 14 | `$this->variant->isWide()` |
| 5 | iskelet seçimi | player-info.php 29 / 49 | wide / embedded |
| 6 | footer tier sınıfı | footer.php 62-64 | `--4k` / `--wide` / `--embedded` |

**Tekilleştirme gözlemi:** `isWide()` iki yerde mevcut (AbstractComponent 81-84 · partial 14); ikisi de aynı `readonly` property'ye bakar — paralel sayaç yoktur.

**Footer ayrımı:** Footer tier sınıfı 3'lüdür (`--4k` dahil), partial kararı 2'li (wide/embedded) — `--4k` dalı partial'da karşılığı olmayan bir sınıftır; `--4k`'yı tetikleyen koşul footer 62-64 dışına taşar → ⚠️ VERIFICATION REQUIRED.

## §14 Kaçış (Escape) Katmanı Sürüşü

| Katman | Nerede | Ne korur | Uygulanan |
|---|---|---|---|
| `strip_tags` | player-info.php 19-20 | HTML etiketi kalıntısı | song/artist |
| `json_encode` + hex bayraklar | player-info.php 17, 23 | JSON içinde `<`, `'` | cmConfig verisi |
| `htmlspecialchars(ENT_QUOTES)` | player-info.php 16, 25 | tırnak kaçışı | JSON string'i (tek tırnaklı atribüt) |
| `$h` closure | footer.php 29-31 | genel HTML kaçışı | footer metinleri |
| `csp_nonce` | footer.php 34 | script kaynağı doğrulaması | script etiketleri |

**Kaçış boşluğu (iddefteri §21'e bağlı):** `$imgCover` / `$currentArt` için kaçış uygulandığına dair satır kanıtı **okunan kısımlarda YOK** — `src` niteliğine ham değer yazımı XSS yüzeyi olur; yazan taraf ve satır aralığı bilinmediğinden **UNKNOWN**, doğrulama `⚠️ VERIFICATION REQUIRED`.

**Sıra savunması:** cmConfig üç katmanlıdır (§3); katmanlardan biri tek başına kaldırılsaydı diğer ikisi tek başına yetersiz kalırdı — bu nedenle sıra değiştirilemez.

## §15 Asset ve İkon Sürüşü

| Kaynak | Sayı | Satır | Dosya listesi |
|---|---|---|---|
| PlayerInfoComponent asset yolları | 11 | 48-58 | anayasa §12 verbatim bloğunda — burada tekrar yazılmaz |
| footer ikon yolları | 11 | 48-59 | okunan kısımda tek tek okunmadı → isim listesi UNKNOWN |
| `current_art` fallback | 1 | PlayerInfo 47 | sabit asset yolu (anayasa §12) |

**Eşleşme sorusu:** İki 11'lik küme aynı görselleri mi işaret ediyor — kıyas için her iki dosyanın string listesi yan yana okunmalıdır; şu an **UNKNOWN** (kısmi okuma).

**Kök:** `ASSETS_URL` footer 33 — mutlak URL üretim noktası; asset yollarının göreli mi mutlak mı olduğu anayasa §12'ye havale.

## §16 Hata Yansıması — Kullanıcı Yüzeyi

| Hata | İç yüzey | Kullanıcı yüzeyi | Satır |
|---|---|---|---|
| Bilinmeyen bileşen | `\InvalidArgumentException` | sayfa 500 (yakalayıcı yok) | Loader 88 |
| Arayüz ihlali | `\InvalidArgumentException` | sayfa 500 | Loader 68-70 |
| Partial yok/bozuk | rethrow | 500 + temiz buffer | AbstractComponent 40-43 |
| Session anahtarı eksik | sessiz default | normal render, Figma varsayılanı | PlayerInfo 40-46 |
| JSON üretimi başarısız | `json_encode` `false` | kısmi cmConfig — davranış okunmadı | player-info.php 23 ⚠️ |

**Gözlem:** Yakalayıp yeniden-fırlatma deseni (AbstractComponent 40-43) tek kazancı output buffer temizliğidir; hata gizleme yoktur — hata sayfa düzeyine taşınır.

**Session hattı hata üretmez:** `??` kalıbı nedeniyle eksik anahtar hata değil normal akıştır (§12) — playback için "durum yok" ile "hata" ayrımı bu dosyada yapılmaz.

## §17 Zaman Çizelgesi (istek-içinde render)

| # | Olay | Sıra | Kilit |
|---|---|---|---|
| 1 | istek gelir, session açılır | render öncesi | session extensionu — okunmadı ⚠️ |
| 2 | sayfa `display` çağırır | Loader 103-106 | — |
| 3 | registry + ctor (session okuma) | Loader 87-94 | — |
| 4 | `ob_start` → `require` → partial | AbstractComponent 36-38 | — |
| 5 | cmConfig JSON + iskelet | partial 16-50 | — |
| 6 | `ob_get_clean` → `echo` | 39 → Loader 105 | — |
| 7 | footer include akışı | footer 22-80 | okunan kısım |
| 8 | JS köprüsü (`data-cm-component`) | partial 10 | tarayıcıda — ⚠️ JS okunmadı |

**Çıktı biçimi:** Tek HTML parçası + gömülü `data-cm-config` atribütü; ağ trafiği render sırasında yok (durum sonraki JS katmanında güncellenir — o katman UNKNOWN).

## §18 Çağrı Grafiği

```text
sayfa (pages/*.php)
  └─ ComponentLoader::display('player-info', $variant)      [Loader 103-106]
       ├─ make(): registry arama → sınıf                      [Loader 87-88]
       ├─ RecentTracksComponent özel dalı                     [Loader 90-92]
       ├─ new $class($variant)                                [Loader 94]
       │    └─ PlayerInfoComponent::__construct
       │         ├─ session okuma (?? default)                [PlayerInfo 40-47]
       │         └─ 11 asset yolu                             [PlayerInfo 48-58]
       ├─ $comp->render()                                     [AbstractComponent 34-44]
       │    ├─ ob_start                                       [36]
       │    ├─ require pages/components/player-info.php       [38 → partial 1-65]
       │    │    ├─ $this->variant->isWide()                  [partial 14]
       │    │    ├─ cmConfig (strip→json→escape)              [partial 16-27]
       │    │    └─ wide | embedded iskelet                   [partial 29 | 49]
       │    └─ ob_get_clean()                                 [39]
       └─ echo                                                [Loader 105]

footer.php include akışı
  ├─ DeviceManager::instance + viewport                      [footer 22-27]
  ├─ session okuma (?? default)                              [footer 36-46]
  ├─ 11 ikon + tier sınıfı                                   [footer 48-64]
  └─ <footer> HTML (66-80) · gövde 80+ OKUNMADI              [⚠️]
```

## §19 Sürüş Beklentileri (test yüzeyi)

| # | Beklenen davranış | Kanıt | Test edilebilirlik |
|---|---|---|---|
| 1 | bilinmeyen anahtar → exception | Loader 88 | PHP yok → ⚠️ |
| 2 | parametresiz çağrı → `Embedded` | Loader 85 | PHP yok → ⚠️ |
| 3 | wide çağrıda `player-info--wide` | partial 29 | PHP yok → ⚠️ |
| 4 | eksik session → Figma default | PlayerInfo 40-46 | PHP yok → ⚠️ |
| 5 | render exception → temiz buffer | AbstractComponent 40-43 | PHP yok → ⚠️ |
| 6 | cmConfig tek tırnakla açılıp kapanır | partial 25, 30/50 | PHP yok → ⚠️ |
| 7 | `ComponentInterface` olmayan sınıf reddi | Loader 68-70 | PHP yok → ⚠️ |
| 8 | `RecentTracksComponent` özel dalı | Loader 90-92 | PHP yok → ⚠️ |
| 9 | cmConfig hex kaçışı (`<` → `\u003C` sınıfı) | partial 17 | PHP yok → ⚠️ |
| 10 | session anahtarları yalnız okunur | PlayerInfo 40-47 · footer 36-46 | PHP yok → ⚠️ |
| 11 | footer tier 3'lü sınıf | footer 62-64 | PHP yok → ⚠️ |

**Kanıt durumu:** Tüm beklentiler statik okumadan türetilmiştir; `php -l` ve canlı render **çalıştırılmadı** (runtime yok) → her satır `⚠️ VERIFICATION REQUIRED`.

## §20 İddia → Kanıt Matrisi

| İddia | Kanıt tipi | Dosya/Satır | Durum |
|---|---|---|---|
| Render 12 adımda tamamlanır | okuma | Loader 85-106 · AbstractComponent 34-44 | geçerli |
| cmConfig üç katmanlı | okuma | player-info.php 16-27 | geçerli |
| İki `isWide()` yolu aynı property | okuma | AbstractComponent 81-84 · partial 14 | geçerli |
| Footer 11 ikon kullanır | okuma | footer 48-59 | geçerli (isimler UNKNOWN) |
| `progress_pct` default 30 vs 65 | okuma | PlayerInfo 46 · footer 37 | **çelişki — §21** |
| `currentArt` kaçışsız | negatif okuma | okunan kısımda kaçış satırı yok | yazar bilinmiyor → UNKNOWN |
| Playback durumunu kim yazar | greb | `home.coremusic.net` yazım-grebi 0 isabet | UNKNOWN (anayasa §6) |
| Footer gövdesi/JS çağrısı | kapsam dışı | footer 80+ okunmadı | ⚠️ VERIFICATION REQUIRED |
| `isWide()` iki yolu aynı property | okuma | AbstractComponent 81-84 · partial 14 · anayasa §13 | geçerli |
| Senaryolar statik türetim | okuma | §25 — çalışma anı doğrulama yok | ⚠️ VERIFICATION REQUIRED |

## §21 Bilinen Çelişkiler (çözülmedi)

| # | Çelişki | Kanıt | Davranış |
|---|---|---|---|
| 1 | `progress_pct` default **30** (PlayerInfo 46) vs **65** (footer 37) | iki okuma | çelişki KORUNUR — gerekçe UNKNOWN, uydurma açıklama yazılmaz |
| 2 | `$imgCover` / `$currentArt` kaçış uygulanmadığı iddiası | okunan kısımda kaçış satırı yok | yüzey kaydedilir; yazan taraf UNKNOWN |
| 3 | `isWide()` iki implementasyon | AbstractComponent 81-84 · partial 14 | teilleşme yok, ikisi de geçerli |
| 4 | tier 3'lü (`--4k`) vs varyant 2'li | footer 62-64 · partial 29/49 | `--4k` kaynağı ⚠️ |

**Çözüm kuralı:** Bu çelişkiler bu dosyada çözülmez; sahip ataması yapısalcı karardır (K012 kök §3).

## §22 Devir ve Sınır Matrisi

| Konu | Bu dosyada | Devralan |
|---|---|---|
| Session sözlüğü (anahtar adları, anlamları) | yalnız sürüş satırları | anayasa §2 |
| Verbatim bloklar (66+28+85 satır) | yok | anayasa §12 |
| Yazan taraf taraması | yok | anayasa §19 |
| Footer 80+ gövde | ⚠️ boşluk | sonraki okuma turu |
| `PlayerInfoComponent.js` akışı | ⚠️ boşluk | sonraki okuma turu |
| Playback durum-üretimi | kapsam dışı (bulunamadı) | anayasa §6 — Q2 açık |

## §23 Footer HTML İskeleti (footer 66-80)

> Okunan kısım: `<footer>` başlangıcından 80. satıra kadar. 80+ gövde okunmadı (§9).

| # | Öğe | Satır | Not |
|---|---|---|---|
| 1 | `<footer>` açılışı + tier sınıfı | 62-64/66 | sınıf adı `--4k`/`--wide`/`--embedded` |
| 2 | seekbar kabuğu | 66-80 aralığı | konum tam satır → ⚠️ okunan kısımda kaba ayrışım |
| 3 | meta alanı (şarkı/sanatçı kapak) | 66-80 aralığı | kaçış durumu §14 |
| 4 | kontrol butonları (`<img>` ikonları) | 48-59/66-80 | tıklama bağlantısı §9 boşluğu 6 |
| 5 | JS betik etiketleri | 65 (`ASSETS_URL`/nonce) + 80+ | nonce footer 34 |
| 6 | `</footer>` kapanışı | 80+ ⚠️ | gövde devamı okunmadı |

**Ayrışım dürüstlüğü:** Satır 66-80 aralığındaki öğeler `Dosya: footer.php / Satır: 66-80` granularityindedir; tekil satır numarası iddia edilmez — uydurma hassasiyet yazılmaz.

**Veri akışı:** footer session'ı partial ile **aynı** sözlüğü okur (§12); iki okuma arasında köprü JS katmanındadır → `⚠️ VERIFICATION REQUIRED`.

## §24 Sürüş Güvenlik Denetimi

| # | Yüzey | Kontrol | Satır | Sonuç |
|---|---|---|---|---|
| 1 | cmConfig atribütü | hex bayrak + `ENT_QUOTES` | partial 17, 25 | korumalı |
| 2 | song/artist metni | `strip_tags` | partial 19-20 | etiket temizliği — tam kaçış mı? §14 hatları |
| 3 | footer metinleri | `$h` closure | footer 29-31 | korumalı |
| 4 | script yüklemesi | `csp_nonce` | footer 34 | nonce'lu |
| 5 | kapak `src` | kaçış kanıtı yok | PlayerInfo 47 / footer meta | **UNKNOWN — §21 no 2** |
| 6 | session okuma | salt okunur `??` | PlayerInfo 40-47 · footer 36-46 | yazım yok |
| 7 | partial yolu | sabit `dirname` + key | AbstractComponent 31 | key registry'de — kaçak yok (Loader 87-88) |
| 8 | hata yolu | rethrow + temiz buffer | AbstractComponent 40-43 | bilgi sızıntısı: exception metni 500'e taşınır → ⚠️ |

**Sınır:** Denetim yalnızca okunan satırları kapsar; JS tarafı (gönderilen komutlar, URL doğrulaması) kapsam dışı → `⚠️ VERIFICATION REQUIRED`.

## §25 Örnek Sürüş Senaryoları

### §25.1 Senaryo A — Varyantsız Çağrı

| # | Adım | Sonuç |
|---|---|---|
| 1 | `display('player-info')` | Loader 85 default `Embedded` |
| 2 | ctor session eksik | `??` Figma default (PlayerInfo 40-46) |
| 3 | partial `isWide()` false | embedded dal (partial 49) |
| 4 | cmConfig | eksik anahtarların defaultları JSON'a yazılır |
| 5 | çıktı | `now-playing--embedded` + `data-cm-config` |

### §25.2 Senaryo B — Wide Çağrı

| # | Adım | Sonuç |
|---|---|---|
| 1 | `display('player-info', Wide)` | Loader 85 argüman korunur |
| 2 | partial 14 | `isWide()` true |
| 3 | partial 29-30 | `player-info--wide` + `data-cm-component` |
| 4 | footer tier | `--wide` (footer 62-64) — `--4k` koşulu ayrı ⚠️ |

### §25.3 Senaryo C — Bilinmeyen Anahtar

| # | Adım | Sonuç |
|---|---|---|
| 1 | `display('player-info-x')` | Loader 87-88 arama başarısız |
| 2 | exception | `\InvalidArgumentException` "Bilinmeyen bileşen" |
| 3 | sayfa | 500 — buffer yok henüz (render başlamadı) |
| 4 | log | yakalayıcı/günlük satırı okunmadı → UNKNOWN |

### §25.4 Senaryo D — Partial Hatası

| # | Adım | Sonuç |
|---|---|---|
| 1 | `require` kısmi başarısız | `\Throwable` |
| 2 | catch (40-43) | `ob_end_clean` — tampon atılır |
| 3 | rethrow | 500 — yarım HTML YOK (tampon temizlendi) |

**Senaryo kanıtı:** Dördü de statik okumadan türetildi; çalışma anı doğrulama yok → `⚠️ VERIFICATION REQUIRED`.

## §26 Kısıtlar ve Kararlar

| # | Karar | Gerekçe | Satır |
|---|---|---|---|
| 1 | Render'da ağ/DB yok | ctor DB almaz, partial tek `require` | Loader 94 · AbstractComponent 38 |
| 2 | Session salt okunur | `??` kalıbı, yazım yok | PlayerInfo 40-47 · footer 36-46 |
| 3 | Çift `isWide()` tolere edildi | ikisi aynı property | AbstractComponent 81-84 · partial 14 |
| 4 | Hata sessizce yutulmuyor | rethrow | AbstractComponent 40-43 |
| 5 | cmConfig üç katman | atribüt tek tırnaklı | partial 16-27 |
| 6 | JS köprüsü data- niteliği | deklassman | partial 10, 30/50 |

**Karar-ötesi boşluk:** Playback durumunun **üretimi** bu sürüşün dışında ve repo'da bulunamadı (anayasa §6) — Q2 açık soru olarak kalır.

## §27 Komponent Kıyaslama Özeti

> Bu tablo playback bileşeninin K012 içindeki komşularıyla kıyaslama sonucunu özetler; ayrıntı anayasa §18'dedir.

| Ölçüt | PlayerInfo | RecentTracksComponent | Sonuç |
|---|---|---|---|
| Kayıt yolu | genel dal (Loader 94) | özel dal (Loader 90-92) | iki farklı iskelet |
| Session okuma | 6 string + 1 int + 1 URL (40-47) | anayasa §18 | PlayerInfo daha geniş |
| Asset yolu | 11 (48-58) | anayasa §18 | PlayerInfo asset-ağırlıklı |
| Varyant | `isWide()` 2'li (partial 29/49) | anayasa §18 | — |
| cmConfig | var (partial 16-27) | anayasa §18 | PlayerInfo'ya özgü |
| JS köprüsü | `cm-player-info` (partial 10) | anayasa §18 | farklı bileşen adı |
| Loader özel dalı | hayır | evet (Loader 90-92) | yalnız RecentTracks |
| `new` argümanı | `$variant` (94) | `__construct` (91-92) | çağrı biçimleri farklı |
| Önbellek/süreç | yok (render-boyunca sabit) | anayasa §18 | — |
| Hata davranışı | exception + temiz buffer | anayasa §18 | ortak `AbstractComponent` |
| Test dosyası | var (repo taraması, anayasa §18) | anayasa §18 | — |
| Kapsam sınırı | yalnız görüntüleme | anayasa §18 | durum-üretimi ikisinde de yok |

**Kıyaslama sınırı:** Sütun 2 dışındaki değerler anayasa §18'e dayanır; bu dosyada tekrar hesaplanmaz (SSOT).

**Ortak payda:** Her iki bileşen de yalnızca görselleştirme katmanıdır — playback/son dinleme durumunu **üreten** kod bu iki dosyanın dışında ve repo taramasında bulunamadı (anayasa §6 · §28 boşluk 10).

## §28 Boşluk Takip Matrisi

> Bu tablo §9 boşluklarının takip kimliğidir; her boşluk bir sonraki okuma turunda kapatılır veya sahibine devredilir.

| # | Boşluk | İlgili bölüm | Öncelik | Kapanış ölçütü |
|---|---|---|---|---|
| 1 | footer.php 80+ (gövde, JS çağrıları, buton sarmalayıcıları) | §4 · §9 · §23 | yüksek | dosyanın tamamının okunması |
| 2 | pages/home.php (display çağrı sırası) | §8 · §9 · §17 | yüksek | dosyanın okunması + adım sırasının §17'ye işlenmesi |
| 3 | `PlayerInfoComponent.js` (config okuma, komut gönderimi) | §8 · §9 · §25 | yüksek | JS dosyasının okunması + senaryolara eklenmesi |
| 4 | `DeviceManager::instance` implementasyonu | §4 · §9 | orta | sınıf dosyasının okunması |
| 5 | `php -l` kanıtı | §9 · §19 | orta | PHP runtime kurulumu + çıktı kaydı |
| 6 | Footer butonlarının JS click bağlantısı | §9 · §24 | yüksek | footer 80+ okuması + JS eşlemesi |
| 7 | `--4k` tier kaynağını tetikleyen koşul | §13 · §21 | orta | footer 62-64 dışına taşan okuma |
| 8 | `$imgCover`/`$currentArt` yazan tarafı | §14 · §20 · §21 | yüksek | yazım-grebi genişletmesi |
| 9 | 3 adımsız session anahtarının adları | §12 | düşük | anayasa §2 sözlüğünün bu dosyaya taşınması |
| 10 | Playback durum-üretimi (kim, nerede) | §26 · anayasa §6 | yapısal | K012 kök sahibi kararı (Q2) |

**Takip kuralı:** Boşluk kapatıldığında ilgili satır bu tabloda işaretlenir, silinmez — append-only yazım korunur.

## §29 Değişiklik Kaydı

| Tarih | Değişiklik | Kanal |
|---|---|---|
| 2026-10-13 | İlk yazım — 11 sürüş bölümü: ana akış, varyant, cmConfig, footer, hata, buffer, kısıtlar | Vault Steward oturumu |
| 2026-10-13 | Genişletme — §11-§22: satır sözlüğü, session matrisi, varyant kesişimi, kaçış, asset, hata yüzeyi, zaman çizelgesi, çağrı grafiği, test beklentileri, iddia matrisi, çelişkiler, devir | Vault Steward oturumu |
| 2026-10-13 | Genişletme — §23-§29: footer iskeleti, güvenlik denetimi, 4 senaryo, kısıtlar/kararlar, kıyaslama, boşluk takibi | Vault Steward oturumu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
