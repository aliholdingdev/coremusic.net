---
node_id: K012-services
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - music-service
  - media-service
  - playback-service
  - audio-service
  - user-service
  - device-service
  - ai-service
  - integration-service
  - background-jobs
  - service-contracts
depends_on: [K006-yapay-zeka, K007-data, K008-domain, K013-integraion, K010-network, K014-media]
evidence:
  - "v2 §2.12 K012-services/ — çocuk listesi kaynağı (10 çocuk, birebir)"
  - ".ai/architecture/index.md §5/12 — services-master.md adı kaydı"
  - "Protobuf canonical model (portify benzeri) — 2026-10-12 beyanı (URL yok ⚠️)"
validation:
  - "Çocuk adları v2 §2.12 ile birebir karşılaştırıldı (10/10) — 2026-10-12"
  - "Web kanıtları services-dogrulama.md §3'e işlendi — 2026-10-12"
open_questions:
  - "ai-service K006-yapay-zeka'ya mı K015-AI'ye mi bağlanır? → ⚠️ VERIFICATION REQUIRED"
  - "playback-service (K012) ile K015/playback sahipliği → ⚠️ VERIFICATION REQUIRED"
  - "service-contracts evrensel Playlist/Track spec'i mi? → TASARIM İDDİASI"
  - "background-jobs ile K018-devops/dağıtım sınırı → ⚠️ VERIFICATION REQUIRED"
group: mimari
updated: 2026-10-18
---

# K012-services — Arka Plan Servisleri

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K012-services |
| **K Adı** | Servisler (Services — arka plan) |
| **Kapsam** | Arka planda iş yapan küçük uygulamalar — "çalan parça servisi", "kullanıcı servisi". Arayüz değil, iş yapan taraftır: çalmayı yönetir, kullanıcıyı tutar, cihazı kaydeder, zamanlanmış işleri koşar. Uygulama (K011) bunları çağırır; veri K007'de, iş kuralı K008'de, dış dünya K013'tedir. 10 kademe-2 çocuk. |
| **Kritik Bağımlılıklar** | K006-yapay-zeka · K007-data · K008-domain · K013-integraion · K014-media (+K010-network) (kanıt: FM depends_on — K0100-identitiy ölü referansı 2026-10-18'de silindi ⚠️) |
| **Durum** | draft (10 kademe-2 çocuk) |
| **Son Güncelleme** | 2026-10-12 (çocuk adları v2 §2.12 ile birebir 10/10) |

## 1. ELI15 — bu oda ne işe yarar?

v2 §2.12: *"Arka planda iş yapan küçük uygulamalar — 'çalan parça servisi',
'kullanıcı servisi'."*
Arayüz değil, **iş yapan** taraftır: çalmayı yönetir, kullanıcıyı tutar, cihazı
kaydeder, zamanlanmış işleri koşar. Uygulama (K011) bunları çağırır; veri K007'de,
iş kuralı K008'de, dış dünya K013'te durur.

## 2. Çocuk kataloğu (kademe-2 — v2 §2.12 ile birebir)

| # | Çocuk | ELI15 rol (tasarım iddiası) | Durum |
|---|---|---|---|
| 1 | music-service | Müzik katalog/arama işlerini yürüten servis | draft |
| 2 | media-service | Medya varlığı işlerini yürüten servis | draft |
| 3 | playback-service | "Ne çalıyor / ne çalacak" durumunu yöneten servis | draft |
| 4 | audio-service | Ses katmanına ait servis işleri (ayrıntı ⚠️) | draft |
| 5 | user-service | Kullanıcı profili ve tercihleri servisi | draft |
| 6 | device-service | Cihaz kaydı, durumu ve yetkisi servisi | draft |
| 7 | ai-service | Yapay zeka işlerini sunan servis (bağlı olduğu düğüm ⚠️) | draft |
| 8 | integration-service | Dış sistem çağrılarını barındıran servis (K013 kontratları) | draft |
| 9 | background-jobs | Zamanlanmış ve arka plan işleri | draft |
| 10 | service-contracts | Servisler arası sözleşmeler (mesaj/DTO biçimi ⚠️) | draft |

## 3. Bağımlılık özeti (tam metin: services-bagimliliklar.md)

| Yön | Düğüm | Ne için (TASARIM İDDİASI) |
|---|---|---|
| depends_on → | K006-yapay-zeka / K015-AI | ai-service'in arkası (hangisi ⚠️ §4) |
| depends_on → | K007-data | Kalıcı veri erişimi |
| depends_on → | K008-domain | İş kuralı ve domain modeli |
| depends_on → | K013-integraion | Dış sistem kontratları (integration-service) |
| depends_on → | K010-network | Servisler arası taşıma |
| depends_on → | K014-media | Kitaplık/metadata/akış verisi |
| ← used_by | K011-application | Uygulamanın servis çağrıları |
| ← used_by | K019-testing | Servis testleri (sahiplik ⚠️) |

## 4. Durum & açık sorular

- **Durum:** draft — kademe-3/4 genişletmesi ADR-097 K4 fazı (C) bekliyor. Ayrıntı: services-bagimliliklar.md (§1–§5) · services-dogrulama.md (§1–§27) · services-riskler.md (§3) · services-ornekler.md (§3).
- Kaynak: v2 §2.12 · .ai/architecture/index.md §5/12 · ADR-097 §5
- Kısa liste:
  1. ai-service → K006 mı, K016 mı? → ⚠️ VERIFICATION REQUIRED (registry §6)
  2. playback-service ↔ K015/playback sahipliği → ⚠️ VERIFICATION REQUIRED
  3. service-contracts evrensel Playlist/Track spec'i mi? → TASARIM İDDİASI
  4. audio-service kapsamı (K003-audio ile sınır) → ⚠️ VERIFICATION REQUIRED

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `services-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `services-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `services-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| mimari | `services-riskler.md` | Risk kaydı (R1–R8) ve azaltım planı | Kademe-1 |
| kod | `services-ornekler.md` | Kanıt kartları (S1) ve pseudocode örnekler | Kademe-1 |
| kanit | `services-web-arastirma.md` | Q1–Q9 web kanıtlı seçenek matrisleri (11 URL + 4 kardeş pointer; karar ADR bekler) | Kademe-1 |

## §BAĞLANTILAR

> **Kaynak & yöntem (2026-10-18 genişletme):** 22 K'nın tamamının `index.md` frontmatter
> `depends_on` alanları + tüm `.md` dosyalarında ad-geçen K taraması (UTF-8 ölçümü).
> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kendi belgeleri hedefe referans
> veriyor · `←` karşı tarafın belgeleri bu K'ya referans veriyor · `↔` ikisi de.
> Kanıtsız eşleşmede `⚠️` yazılır; **uydurma bağımlılık satırı yazılmaz** (zero-hallucination).

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ← | HTTP/REST'in kapsam dışı bırakıldığı OS zemini | `K000 isletim-sistemi-observability-kapsam.md` L60 → K012-services (tam ad) · `K000 index.md` L151 stok satır (sayılmaz) · K012'de `K000-isletim-sistemi` 0 isabet (yalnız `parent_id` registry başlığı + `K000–K009` kapsam aralığı, sayılmaz) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | ⚠️ | donanım–cihaz servisi sınırı — kanıt yok | `K001 index.md` L126 §BAĞLANTILAR stok satırı (sayılmaz) · `K001 raporlar/K001-donanım-mevcut-durum-raporu.md` L187 envanter sayımı (zayıf, sayılmaz) · K012'de `K001-donanım` 0 isabet (`⚠️ VERIFY REQUIRED`) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ⚠️ | donanım metrik/analiz sınırı — kanıt yok | K012 `ai-service-kapsam.md` L176/L185/L235/L419 `K002-hardware` varyantı (registry §6.2-A'da kayıtlı değil · dosya "adı da ⚠️" diyor — sayılmaz) · `K002` belgelerinde K012 0 isabet (`⚠️ VERIFY REQUIRED`) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | → | audio-service ↔ sinyal motoru sınırı | `K012 services-master.md` L76/L107 · `services-bagimliliklar.md` L98 K003 referansı · `K003 index.md` L131 stok satır (ters kanıt sayılmaz) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | → | DSP işi servis üzerinden (tasarım) | `K012 services-master.md` L76 (`K003-audio/K004-dsp`) · `K004` belgelerinde K012 0 isabet (ters kanıt yok) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ⚠️ | doğrudan kanıt yok — firmware etkisi olası | `K005` belgelerinde K012 0 isabet · `K012` belgelerinde K005 0 isabet (`⚠️ VERIFY REQUIRED`) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ↔ | ai-service'in YZ arkası (hangisi ⚠️ §4) | `K012 index.md` L74 + `services-master.md` L22 FM depends_on (K006-yapay-zeka) · `K006 yapay-zeka-kapsam.md` L258 (K012-services → K006) · `yapay-zeka-bilesenler.md` L257 → K012 |
| K007-data | [[K007-data]] | ../K007-data/index.md | ↔ | servis kalıcılığı (repo üzerinden) | `K012 index.md` L75 + `services-bagimliliklar.md` L58 · `K007 data-bagimliliklar.md` L28/L76 · `data-master.md` L194 → K012 |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | ↔ | iş kuralı ve domain modeli | `K012 index.md` L76 + `services-master.md` L94 · `K008 music-kapsam.md` L35 · `media-kapsam.md` L397 · `user-kapsam.md` L37 → K012 |
| K009-security | [[K009-security]] | ../K009-security/index.md | ⚠️ | güvenlik sınırı — iki yönde de sayılır kanıt yok | K012 `user-service-anayasa.md` L449 çıplak `K009` (zayıf, sayılmaz) · `K009 index.md` L153 "iki yönde kanıt yok" stok satırı (sayılmaz) (`⚠️ VERIFY REQUIRED`) |
| K010-network | [[K010-network]] | ../K010-network/index.md | ↔ | servisler arası taşıma | `K012 index.md` L78 + `services-master.md` L92 · `K010 network-bagimliliklar.md` L42/L55 · `K010 index.md` L82 → K012 |
| K011-application | [[K011-application]] | ../K011-application/index.md | ↔ | uygulamanın servis çağrıları | `K012 index.md` L80 (← used_by) · `K011 web-kapsam.md` L178 · `usecase-akislari-kapsam.md` L71/L108 · `zamanlamali-gorevler-kapsam.md` L69 → K012 |
| K012-services | — (kendi) | index.md | ○ | bu düğümün kendisi | — |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ↔ | integration-service kontrat tüketimi | `K012 index.md` L77 + `services-master.md` L91 · `K013 integraion-bagimliliklar.md` L45 · `integration-master.md` L55/L119 → K012 |
| K014-media | [[K014-media]] | ../K014-media/index.md | ↔ | medya verisi okuma / playback sahipliği | `K012 index.md` L79 + `services-master.md` L93 · `K014 media-master.md` L38/L113 · `media-bagimliliklar.md` L43 → K012 |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ↔ | ajan tool-call → servis yüzeyi | `K012 index.md` L26/L74 (K015-AI) · `K015 ai-master.md` L11/L90 FM depends_on · `ai-bagimliliklar.md` L32/L54 → K012 |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ← | servis log/metric/health üretimi (K016 toplar) | `K016 observability-master.md` L21/L108 + `K016 index.md` L18 FM depends_on → K012-services · K012'de K016'ya tam ad yok (çıplak `K016` zayıf, sayılmaz) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ↔ | dağıtım hattı / gateway sınırı | `K012 services-master.md` L97/L317/L350 eski ad `K018-devops` (⚠️ ad çelişkisi, registry §6.2-A) · `K17 devops-kapsam.md` L194 → K012-services |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | → | servis testleri (sahiplik ⚠️) | `K012 services-master.md` L98/L351 · `services-bagimliliklar.md` L70 eski ad `K019-testing` (⚠️ ad çelişkisi, registry §6.2-A) · `K18` belgelerinde K012 0 isabet |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ← | API güvenlik kontrol eşlemesi | `K019 controls/index.md` L100 → K012-services (tam ad) · K012'de `K019-comilance` 0 isabet |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ← | servis katmanının keşif/imza bağlantısı | `K020 agent-kapsam.md` L401 · `agent-bilesenler.md` L52/L173/L288/L385 → K012-services · K012'de yalnız çıplak `K020` (kapsam listesi, sayılmaz) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ⚠️ | doğrudan kanıt yok — öneri entegrasyonu olası | `K021` belgelerinde K012 0 isabet · `K012` belgelerinde K021 0 isabet (`⚠️ VERIFY REQUIRED`) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
