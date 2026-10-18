---
node_id: K008-domain
node_type: domain
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children:
  - music
  - media
  - user
  - library
  - playlist
  - playback
  - discovery
  - recommendation
  - device-management
  - context-mapping
depends_on: []
evidence:
  - "v2 §2.8 — coremusic-katmanli-mimari-agaci-v2.md satır 290–308 (10 çocuk, master = domain-master.md)"
  - "v2 §6 satır 1001 — K008 ID CONFLICT (öneri: K008-domain · eski disk: K008-servisler)"
  - "shared/config/domain.php satır 7–15 — 7 subdomain (auth, home, assets, music, admin, media, api)"
  - "shared/src/Events/Domain/ — 9 domain event (MusicPlayedEvent … GenderSetEvent)"
## §GRUPLAR

> İki-ekseni gruplama (registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**. `Kademe` ataması şeffaf rol kuralıdır (index/master/kapsam → Kademe-1; destek → Kademe-2) — diskte `kademe` alanı yok, uydurma yok.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Domain künyesi | Kademe-1 |
| mimari | `domain-master.md` | Domain anayasa | Kademe-1 |
| mimari | `domain-kapsam.md` | Kapsam sınırı | Kademe-1 |
| mimari | `domain-bilesenler.md` | Bileşen kataloğu | Kademe-2 |
| mimari | `domain-bagimliliklar.md` | Bağımlılık ekseni | Kademe-2 |
| mimari | `domain-kararlar.md` | Domain ADR'leri | Kademe-2 |
| kod | `domain-kod-ornekleri.md` | Domain kod kanıtı | Kademe-2 |
| kanit | `domain-riskler.md` | Risk defteri | Kademe-2 |
| kod | `domain-test-stratejisi.md` | Test stratejisi | Kademe-2 |
| kanit | `domain-web-arastirma.md` | Web araştırması | Kademe-2 |
| mimari | `domain-evrensel-dil.md` | Ubiquitous dil | Kademe-2 |
| kanit | `domain-dogrulama.md` | Doğrulama defteri | Kademe-2 |


## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K008-domain |
| **K Adı** | Domain (İş Kuralları) |
| **Kapsam** | İşin kuralları — "çal", "listeye ekle", "beğen" gibi kelimelerin anlamı. Müzik, medya, kullanıcı, kitaplık, playlist, playback, discovery, recommendation, device-management ve context-mapping olmak üzere 10 kademe-2 çocuk. 7 subdomain runtime kaydı `shared/config/domain.php:7-15`, 9 domain event `shared/src/Events/Domain/`. |
| **Kritik Bağımlılıklar** | yok (FM `depends_on: []` — en alt domain katmanı; gelen bağımlılıklar K006/K007/K011/K012 tarafında tescilli) |
| **Durum** | verify-required (10 kademe-2 çocuk · K008-domain ↔ eski disk K008-servisler ID CONFLICT açık) |
| **Son Güncelleme** | 2026-10-15 (10 çocuk × 6 MD = 72/72 tamamlandı · 89 tekil URL havuzu) |

> **ELI15 (v2 §2.8, birebir):** İşin kuralları — "çal", "listeye ekle", "beğen" gibi kelimelerin anlamı.

## §1 Çocuk Kataloğu (kademe-2 · v2 §2.8)

v2 bu çocuklara **yalnızca ad verir**; rol tanımları v2'de yoktur → her çocuk `-kapsam.md` içinde ad + repo kanıtı ile tanımlanır, tahmin uydurulmaz.

| # | Çocuk | v2 satır | Bu vault'taki MD'ler | status |
|---|-------|----------|----------------------|--------|
| 1 | music | 298 | index · kapsam · dogrulama · web-arastirma · ornekler · bilesenler | draft |
| 2 | media | 299 | index · kapsam · dogrulama · web-arastirma · ornekler · bilesenler | draft |
| 3 | user | 300 | index · kapsam · dogrulama · web-arastirma · ornekler · bilesenler | draft |
| 4 | library | 301 | index · kapsam · dogrulama · web-arastirma · ornekler · bilesenler | draft |
| 5 | playlist | 302 | index · kapsam · dogrulama · web-arastirma · ornekler · bilesenler | draft |
| 6 | playback | 303 | index · kapsam · dogrulama · web-arastirma · ornekler · bilesenler | draft |
| 7 | discovery | 304 | index · kapsam · dogrulama · web-arastirma · ornekler · bilesenler | draft |
| 8 | recommendation | 305 | index · kapsam · dogrulama · web-arastirma · ornekler · bilesenler | draft |
| 9 | device-management | 306 | index · kapsam · dogrulama · web-arastirma · ornekler · bilesenler | draft |
| 10 | context-mapping | 307 | index · kapsam · dogrulama · web-arastirma · ornekler · bilesenler | draft |

## §GRUPLAR (ADR-097 §5.5 · Eksen-1)

| Grup | Dosya | Amaç | Kademe |
|------|-------|------|--------|
| mimari | `index.md` | kayıt künyesi · 10 alan · çocuk kataloğu | 2 |
| mimari | `domain-master.md` | anayasa — rol, sınır, model iskeleti (v2'deki ad, dokunulmaz) | 2 |
| mimari | `domain-kapsam.md` | ne DEĞİL — K006/K016 sınırı, kapsam dışı listesi | 2 |
| mimari | `domain-bagimliliklar.md` | depends_on / used_by / entegrasyon sınırı | 2 |
| mimari | `domain-bilesenler.md` | kademe 3–8 bileşen planı (module → implementation) | 2 |
| mimari | `domain-kararlar.md` | domain karar defteri (ADR bağları) | 2 |
| mimari | `domain-evrensel-dil.md` | Ubiquitous Language — domain terimleri ↔ kod adları | 2 |
| kod | `domain-kod-ornekleri.md` | gerçek repo dosya-satır örnekleri (entity/repository/event) | 2 |
| kod | `domain-test-stratejisi.md` | domain testleri: unit, property, contract | 2 |
| kanit | `domain-dogrulama.md` | ölçütler · açık sorular · web-kanıt kayıtları | 2 |
| kanit | `domain-web-arastirma.md` | DDD/domain araştırması — tekil URL kanıt tablosu | 2 |
| kanit | `domain-riskler.md` | domain riskleri · CONFLICT kayıtları · azaltım planı | 2 |

## §2 Çelişki Kayıtları (devralınan — v2 §3 KURAL 1)

| Kayıt | Durum |
|-------|-------|
| `K008-domain` (öneri) ↔ `K008-servisler` (eski disk) → ID CONFLICT | `⚠️ VERIFY REQUIRED` — onaysız yeniden adlandırma yok |
| `recommendation` / `discovery` çocukları ↔ K006-yapay-zeka · K020-recomenataion · K020-discovery | `⚠️ VERIFY REQUIRED` — sahiplik sınırı ADR gerektirir |
| `shared/AGENTS.md` "K-space kaldırıldı (2026-10-12)" ↔ ADR-097 sıfırdan kurulum | `⚠️ VERIFY REQUIRED` — orkestratör kararı |

## §3 Durum Özeti

| Alan | Değer |
|------|-------|
| status | verify-required (ID CONFLICT açık) |
| Group dağılımı | mimari:7 · kod:2 · kanit:3 (kök) — çocuklar: mimari:3 · kod:1 · kanit:2 her çocukta |
| depends_on | BİLİNÇLİ BOŞ — domain katmanı dışa bağımlı olmaz ilkesi (tasarım iddiası, kanıtlanmış bağımsızlık DEĞİL) |
| Web kanıtı | ✅ var — `-web-arastirma.md` + çocuk arastırma dosyaları (tekil URL) |
| Repo kanıtı | ✅ var — `shared/src/`, `.ai/.sql/mysql/` (bkz. `-kod-ornekleri.md`) |

## §BAĞLANTILAR

> **Okuma kuralı:** `←` işareti **hedef K'nın kendi dizinini kasteder** (`K00X-*/index.md` veya o dizindeki bir belge) — yani "K00X burada K008'den bahsediyor" anlamına gelir; K008'in kendi alt klasör belgeleri değil.
> **Yön semantiği:** `↔` = iki yönlü referans kanıtı var · `→` = yalnız bu K'nın belgeleri hedefi anıyor · `←` = yalnız hedef K'nın dizini bu K'yı anıyor · `⚠️` = arama yapıldı, iki yönde de kanıt yok.
> **Kanıt standardı:** Gerçek dosya adı + satır numarası; uydurma referans yok.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K000-isletim-sistemi | `[[K000-isletim-sistemi/index]]` | `../K000-isletim-sistemi/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K001-donanım | `[[K001-donanım/index]]` | `../K001-donanım/index.md` | ← | Yalnız K001 K008'i anıyor | `K001 index.md` 111 |
| K002-electroncis | `[[K002-electroncis/index]]` | `../K002-electroncis/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K003-audio | `[[K003-audio/index]]` | `../K003-audio/index.md` | ↔ | Audio domain modelleri — K008 K003'e referans verir + K003 K008'i anıyor | `media-kapsam.md` 34 · `K003 index.md` 116 |
| K004-dsp | `[[K004-dsp/index]]` | `../K004-dsp/index.md` | → | DSP domain sınırları — K008 K004'ü anıyor | `media-kapsam.md` 32 |
| K005-firmware | `[[K005-firmware/index]]` | `../K005-firmware/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K006-yapay-zeka | `[[K006-yapay-zeka/index]]` | `../K006-yapay-zeka/index.md` | ↔ | AI domain bağlamı — K008 K006'yı anıyor + K006 K008'i anıyor | `index.md` 31,92 · `domain-master.md` 188 · `ai-master.md` 103,214 |
| K007-data | `[[K007-data/index]]` | `../K007-data/index.md` | ↔ | Entity persistence / şema sahibi — K008 K007'yi anıyor + K007 K008'i anıyor | `music-kapsam.md` 33,241 · `media-kapsam.md` 36,257 · `library-kapsam.md` 253 · `K007 data-bagimliliklar.md` 23,61 |
| K008-domain (kendi) | `[[K008-domain/index]]` | `index.md` | ○ | Kendi kendine bağımlılık yok. `depends_on: []` — en alt domain katmanı. | Bu belge (bu satır) |
| K009-security | `[[K009-security/index]]` | `../K009-security/index.md` | ← | Yalnız K009 K008'i anıyor (input-validation domain referansı) | `K009 input-validation/input-validation-surucu.md` 60,129 |
| K010-network | `[[K010-network/index]]` | `../K010-network/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K011-application | `[[K011-application/index]]` | `../K011-application/index.md` | ← | Yalnız K011 K008'i anıyor | `K011 application-bagimliliklar.md` 8,41 |
| K012-services | `[[K012-services/index]]` | `../K012-services/index.md` | ↔ | Servis sözleşmeleri — K008 K012'yi anıyor + K012 K008'i anıyor | `music-kapsam.md` 35 · `media-kapsam.md` 397 · `user-kapsam.md` 37 · `K012 ai-service-kapsam.md` 54 |
| K013-integraion | `[[K013-integraion/index]]` | `../K013-integraion/index.md` | ↔ | Event/DTO paylaşımı · dış katalog eşlemesi — K008 K013'ü anıyor + K013 K008'i anıyor | `music-kapsam.md` 39,247,336 · `music-dogrulama.md` 37 · `K013 index.md` 17,67 |
| K014-media | `[[K014-media/index]]` | `../K014-media/index.md` | → | Medya domain sınırı — K008 K014'ü anıyor | `media-kapsam.md` 32 · `music-kapsam.md` 34 |
| K015-AI | `[[K015-AI/index]]` | `../K015-AI/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K016-observability | `[[K016-observability/index]]` | `../K016-observability/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K17-devops | `[[K17-devops/index]]` | `../K17-devops/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K18-testing | `[[K18-testing/index]]` | `../K18-testing/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K019-comilance | `[[K019-comilance/index]]` | `../K019-comilance/index.md` | ← | Yalnız K019 K008'i anıyor | `K019 controls/index.md` 95 |
| K020-discovery | `[[K020-discovery/index]]` | `../K020-discovery/index.md` | ↔ | Discovery/recommendation sahiplik sınırı (⚠️ CONFLICT) — K008 K020'yi anıyor + K020 K008'i anıyor | `index.md` 31,92 · `domain-master.md` 188 · `K020 discovery-bagimliliklar.md` 11,52 |
| K021-recomenataion | `[[K021-recomenataion/index]]` | `../K021-recomenataion/index.md` | ← | Yalnız K021 K008'i anıyor | `K021 recommendation-master.md` 111 |

**Authority:** `domain-master.md` (Master authority belgesi — bu index authority'si tamamlar).

> **Authority:** Bayram Ali / Vault Steward · **Last Updated:** 2026-10-18 · **Mode:** Red Team · Human Mode · Truth Mode
