---
node_id: K009-security
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - authentication
  - authorization
  - policies
  - session-security
  - secrets
  - encryption
  - input-validation
  - rate-limiting
  - audit
  - threat-model
  - security-testing
depends_on:
  - K000-isletim-sistemi
  - K007-data
  - K010-network
  - K011-application
  - K013-integraion
evidence:
  - "coremusic-katmanli-mimari-agaci-v2.md §2.9 satır 310–329 (11 çocuk listesi)"
  - "shared/src/Middleware/ — 11 middleware dosyası (envanter 2026-10-12)"
  - "shared/src/Security/ — 6 PHP sınıfı (envanter 2026-10-12)"
  - "security-bagimliliklar.md satır 9–16 — giden bağımlılıklar (K000-isletim-sistemi, K004-dsp, K005-firmware, K007-data, K008-domain, K010-network, K011-application, K013-integraion) — 2026-10-18"
validation:
  - "2026-10-13 — frontmatter v2 §4.2 9 alan + group, `parent:` anahtarı yok, tüm listeler [] dahil dolu"
  - "2026-10-13 — URL tabloları HTTP 200 ile doğrulandı; envanter kesişimi 0"
  - "2026-10-15 — §2 child tablosu 8-şemaya güncellendi (11/11 çocuk 8 dosya) + §4/§5 os-security-kapsam bridge kaydı (kullanıcı kararı: ikisi de)"
  - "2026-10-18 — depends_on phantom kayıtlar (K0100-identitiy, K017-observability, K018-devops) disk-gerçek adlarla düzeltildi (kaynak: security-bagimliliklar.md satır 9–16)"
open_questions:
  - "K009 ↔ K010 sınırı (session-security vs K0100-identitiy/session/tokens) ⚠️ VERIFICATION REQUIRED"
  - "v2 §2.9 çocuklara yalnızca ad verir; rol tanımları üretimde verildi ⚠️ VERIFICATION REQUIRED"
  - "K009 ↔ K009-api ID CONFLICT kaydı (v2 §6 satır 1002) korunuyor — onaysız yeniden adlandırma YASAK"
group: mimari
updated: 2026-10-18
---

# K009-security — Domain Künyesi

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K009-security |
| **K Adı** | Güvenlik (Security) |
| **Kapsam** | Kimlik doğrulama, yetkilendirme, politikalar, oturum güvenliği, sırlar, şifreleme, girdi doğrulama, rate-limit, denetim, tehdit modeli ve güvenlik testi. Kanıt yüzeyi: `shared/src/Middleware/` 11 middleware + `shared/src/Security/` 6 PHP sınıfı. 11 kademe-2 çocuk. |
| **Kritik Bağımlılıklar** | K010-network · K011-application · K013-integraion (kanıt: FM depends_on — FM'deki K0100-identitiy · K017-observability · K018-devops kayıtlarının diske karşılığı YOK ⚠️) |
| **Durum** | draft (11 kademe-2 çocuk · 8 dosya şeması) |
| **Son Güncelleme** | 2026-10-15 (§2 child tablosu 8-şemaya güncellendi · §4/§5 os-security-kapsam bridge kaydı) |

> **Kapıdaki bekçi — şifre, yetki, şifreleme, saldırı koruması.** (v2 §2.9, imla korunur.)

## §1 Rol (ELI15)

| Alan | Değer |
|---|---|
| Ne yapar | Gelen istekte kimlik kanıtını, yetkiyi, girdiyi ve başlıkları doğrular; doğrulamazsa reddeder. |
| Ne yapmaz | Kimlik modeli üretmez (K0100-identitiy), işletim sistemi güvenliği kurmaz (K000-isletim-sistemi/security). |
| Kademe | Kademe-2 (domain) + kademe-3 (alt-modül) |
| Otorite | Bayram Ali / Vault Steward |
| Üretim | K009-v2 derinleştirme, 2026-10-13, Mode: Red Team · Human Mode · Truth Mode |

## §2 Çocuk kataloğu (11/11, v2 satır 318–328 — ad/İçerik sütunu 8-şemaya güncellendi 2026-10-15)

| # | Çocuk | v2 satır | İçerik (8 dosya şeması — 2026-10-15) | Durum |
|---|---|---|---|---|
| 1 | authentication | 318 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma | draft (8/8) |
| 2 | authorization | 319 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma | draft (8/8) |
| 3 | policies | 320 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma | draft (8/8) |
| 4 | session-security | 321 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma | draft (8/8) |
| 5 | secrets | 322 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma | draft (8/8) |
| 6 | encryption | 323 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma | draft (8/8) |
| 7 | input-validation | 324 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma | draft (8/8) |
| 8 | rate-limiting | 325 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma (+ `rate-limiting-index.md` yardımcı envanter) | draft (9/8) |
| 9 | audit | 326 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma | draft (8/8) |
| 10 | threat-model | 327 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma | draft (8/8 — genişletme sürüyor) |
| 11 | security-testing | 328 | index · anayasa · surucu · kapsam · dogrulama · bilesenler · ornekler · web-arastirma | draft (8/8) |

## §3 GRUPLAR (ADR-097 §5.5 — zorunlu tablo)

| Grup | Dosya önekleri | Amaç | Kademe |
|---|---|---|---|
| mimari | `index` · `-master` · `-kapsam` · `-bagimliliklar` · `-anayasa` · `-surucu` · `-bilesenler` · `-kararlar` | künye, kapsam, ilke, sürücü, bileşen | 2 / 3 |
| kod | `-ornekler` · `-kod-ornekleri` · `-test-stratejisi` | gerçek repo koduna bağlı örnek | 3 |
| kanit | `-dogrulama` · `-web-arastirma` · `-riskler` | iddia → URL → tarih → durum | 3 |

## §4 Sınır — kapsam dışı

| Konu | Sahibi | K009 tutumu |
|---|---|---|
| Kimlik deposu, kimlik modeli, credentials | `K0100-identitiy` | Kapsam dışı — yalnız sınır referansı |
| İşletim sistemi düzeyi güvenlik (kernel, disk, servis) | `K000-isletim-sistemi/security` | Kapsam dışı — yalnız sınır referansı · ayrıntı: os-security-kapsam.md |

> Ayrıntı: `security-kapsam.md` §KAPSAM DIŞI. Bu domainde iki konuya karar yazılmaz.

## §5 Dosya haritası (kök)

| Dosya | group | Hedef satır |
|---|---|---|
| `index.md` | mimari | 40–100 |
| `security-master.md` | mimari | ≥500 |
| `security-kapsam.md` | mimari | ≥500 |
| `os-security-kapsam.md` | mimari | 300–500 |
| `security-bagimliliklar.md` | mimari | ≥500 |
| `security-dogrulama.md` | kanit | ≥500 |
| `security-kod-ornekleri.md` | kod | ≥500 |
| `security-bilesenler.md` · `security-kararlar.md` · `security-evrensel-dil.md` · `security-riskler.md` · `security-test-stratejisi.md` · `security-web-arastirma.md` | mimari / kanit / kod | ≥500 |

> Web iddia matrisi (OWASP proactive-controls · NIST 800-30 r1 — HTTP 200, 2026-10-13) `security-dogrulama.md` §3'tedir; bu dizin dosyasında tekrarlanmaz.

## §GRUPLAR

> İki-ekseni gruplama (registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**. `Kademe` ataması şeffaf rol kuralıdır (index/master/kapsam → Kademe-1; destek → Kademe-2) — diskte `kademe` alanı yok, uydurma yok.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Domain künyesi | Kademe-1 |
| mimari | `security-master.md` | K009 anayasası | Kademe-1 |
| mimari | `security-kapsam.md` | Kapsam matrisi | Kademe-1 |
| mimari | `os-security-kapsam.md` | İşletim sistemi güvenliği sınırı | Kademe-1 |
| mimari | `security-bilesenler.md` | Bileşenler (kök) | Kademe-2 |
| mimari | `security-bagimliliklar.md` | Bağımlılık ağacı | Kademe-2 |
| mimari | `security-kararlar.md` | Kararlar (kök) | Kademe-2 |
| kod | `security-kod-ornekleri.md` | Kod örnekleri | Kademe-2 |
| kanit | `security-riskler.md` | Riskler | Kademe-2 |
| kod | `security-test-stratejisi.md` | Test stratejisi | Kademe-2 |
| kanit | `security-web-arastirma.md` | Web araştırması | Kademe-2 |
| mimari | `security-evrensel-dil.md` | Evrensel dil | Kademe-2 |
| kanit | `security-dogrulama.md` | Doğrulama | Kademe-2 |


## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K009-security | [[K009-security]] | ./K009-security/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ↔ | bağımlı (depends_on) | M→21 · M←6 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ↔ | iki yönlü atıf | M→1 · M←3 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ← | karşı taraf referanslıyor | M→0 · M←4 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | ↔ | bağımlı (depends_on) | M→5 · M←20 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | ↔ | bağımlı (depends_on) | M→12 · M←20 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | ↔ | bağımlı (depends_on) | M→7 · M←24 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ↔ | bağımlı (depends_on) | M→8 · M←9 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ← | karşı taraf güçlü referanslıyor | M→0 · M←10 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ← | karşı taraf güçlü referanslıyor | M→0 · M←16 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ← | karşı taraf referanslıyor | M→0 · M←2 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ← | karşı taraf güçlü referanslıyor | M→0 · M←11 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | ← | karşı taraf referanslıyor | M→0 · M←3 (kök .md, 2026-10-18) |


## §BAĞLANTILAR

> **Okuma kuralı:** `←` işareti **hedef K'nın kendi dizinini kasteder** (`K00X-*/index.md` veya o dizindeki bir belge) — yani "K00X burada K009'dan bahsediyor" anlamına gelir; K009'in kendi alt klasör belgeleri değil.
> **Yön semantiği:** `↔` = iki yönlü referans kanıtı var · `→` = yalnız bu K'nın belgeleri hedefi anıyor · `←` = yalnız hedef K'nın dizini bu K'yı anıyor · `⚠️` = arama yapıldı, iki yönde de kanıt yok.
> **Kanıt standardı:** Gerçek dosya adı + satır numarası; uydurma referans yok.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K000-isletim-sistemi | `[[K000-isletim-sistemi/index]]` | `../K000-isletim-sistemi/index.md` | ↔ | OS güvenlik yetenekleri — K009 K000'i anıyor + K000 K009'u anıyor | `os-security-kapsam.md` 9,88 · `K000 os-bagimliliklar.md` 417 |
| K001-donanım | `[[K001-donanım/index]]` | `../K001-donanım/index.md` | ← | Yalnız K001 K009'u anıyor | `K001 index.md` 112 |
| K002-electroncis | `[[K002-electroncis/index]]` | `../K002-electroncis/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K003-audio | `[[K003-audio/index]]` | `../K003-audio/index.md` | ⚠️ | — | iki yönde kanıt yok (K003 kendi tablosunda L117 zaten ⚠️) |
| K004-dsp | `[[K004-dsp/index]]` | `../K004-dsp/index.md` | → | Gizli veri ve DRM sınırları — K009 K004'ü anıyor | `security-kapsam.md` 58 |
| K005-firmware | `[[K005-firmware/index]]` | `../K005-firmware/index.md` | ↔ | Firmware imzalama — K009 K005'i anıyor + K005 K009'u anıyor | `security-kapsam.md` 58 · `K005 index.md` 47 · `firmware-evrensel-dil.md` 251 |
| K006-yapay-zeka | `[[K006-yapay-zeka/index]]` | `../K006-yapay-zeka/index.md` | ← | Yalnız K006 K009'u anıyor (AI güvenlik sınırı) | `K006 ai-master.md` 106,109 |
| K007-data | `[[K007-data/index]]` | `../K007-data/index.md` | ↔ | Veri şifreleme — K009 K007'yi anıyor + K007 K009'u anıyor | `security-bagimliliklar.md` 11 · `K007 data-bagimliliklar.md` 24,62 |
| K008-domain | `[[K008-domain/index]]` | `../K008-domain/index.md` | → | Identity domain / input-validation domain referansı — K009 K008'i anıyor | `input-validation/input-validation-surucu.md` 60,129 |
| K009-security (kendi) | `[[K009-security/index]]` | `index.md` | ○ | Kendi kendine bağımlılık yok. | Bu belge (bu satır) |
| K010-network | `[[K010-network/index]]` | `../K010-network/index.md` | ↔ | TLS ve ağ güvenliği — K009 K010'u anıyor + K010 K009'u anıyor | `security-bagimliliklar.md` 14 · `K010 index.md` 16,68 |
| K011-application | `[[K011-application/index]]` | `../K011-application/index.md` | ↔ | Auth middleware — K009 K011'i anıyor + K011 K009'u anıyor | `security-bagimliliklar.md` 15 · `K011 application-bagimliliklar.md` 10,43 |
| K012-services | `[[K012-services/index]]` | `../K012-services/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K013-integraion | `[[K013-integraion/index]]` | `../K013-integraion/index.md` | ↔ | API güvenliği — K009 K013'ü anıyor + K013 K009'u anıyor | `security-bagimliliklar.md` 16 · `K013 index.md` 18,68 |
| K014-media | `[[K014-media/index]]` | `../K014-media/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K015-AI | `[[K015-AI/index]]` | `../K015-AI/index.md` | ← | Yalnız K015 K009'u anıyor | `K015 ai-bagimliliklar.md` 30,52,192 |
| K016-observability | `[[K016-observability/index]]` | `../K016-observability/index.md` | ← | Yalnız K016 K009'u anıyor | `K016 observability-bagimliliklar.md` 13,63,71 |
| K17-devops | `[[K17-devops/index]]` | `../K17-devops/index.md` | ← | Yalnız K17 K009'u anıyor | `K17 index.md` 19 · `devops-master.md` 23,64,105 |
| K18-testing | `[[K18-testing/index]]` | `../K18-testing/index.md` | ← | Yalnız K18 K009'u anıyor | `K18 index.md` 21 · `testing-master.md` 120 |
| K019-comilance | `[[K019-comilance/index]]` | `../K019-comilance/index.md` | ← | Yalnız K019 K009'u anıyor | `K019 controls/index.md` 96 |
| K020-discovery | `[[K020-discovery/index]]` | `../K020-discovery/index.md` | ← | Yalnız K020 K009'u anıyor | `K020 agent/agent-bilesenler.md` 47,93 |
| K021-recomenataion | `[[K021-recomenataion/index]]` | `../K021-recomenataion/index.md` | ← | Yalnız K021 K009'u anıyor | `K021 recommendation-master.md` 112 |

**Authority:** `security-master.md` (Master authority belgesi — bu index authority'si tamamlar).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
