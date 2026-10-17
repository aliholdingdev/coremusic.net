---
node_id: K009-security
node_type: domain
status: draft
parent_id: null
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
  - K0100-identitiy
  - K011-application
  - K013-integraion
  - K010-network
  - K017-observability
  - K018-devops
evidence:
  - "coremusic-katmanli-mimari-agaci-v2.md §2.9 satır 310–329 (11 çocuk listesi)"
  - "shared/src/Middleware/ — 11 middleware dosyası (envanter 2026-10-12)"
  - "shared/src/Security/ — 6 PHP sınıfı (envanter 2026-10-12)"
validation:
  - "2026-10-13 — frontmatter v2 §4.2 9 alan + group, `parent:` anahtarı yok, tüm listeler [] dahil dolu"
  - "2026-10-13 — URL tabloları HTTP 200 ile doğrulandı; envanter kesişimi 0"
  - "2026-10-15 — §2 child tablosu 8-şemaya güncellendi (11/11 çocuk 8 dosya) + §4/§5 os-security-kapsam bridge kaydı (kullanıcı kararı: ikisi de)"
open_questions:
  - "K009 ↔ K010 sınırı (session-security vs K0100-identitiy/session/tokens) ⚠️ VERIFICATION REQUIRED"
  - "v2 §2.9 çocuklara yalnızca ad verir; rol tanımları üretimde verildi ⚠️ VERIFICATION REQUIRED"
  - "K009 ↔ K009-api ID CONFLICT kaydı (v2 §6 satır 1002) korunuyor — onaysız yeniden adlandırma YASAK"
group: mimari
## §BAĞLANTILAR

> 2026-10-17 · orkestratör · kaynak: K000–K013 arasi gercek dosya taramasi (ad gecen K eslesmesi) + v2 S4.2 depends_on · iliskiler dogrulanmadir, kesin sozlesme degildir.

| Hedef K | Wiki-link | Göreli dosya yolu | İlişki |
|---|---|---|---|
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | OS guvenlik yetenekleri |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | gizli veri ve DRM sinirlari |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | firmware imzalama |
| K007-data | [[K007-data]] | ../K007-data/index.md | veri seifreleme |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | identity domain |
| K010-network | [[K010-network]] | ../K010-network/index.md | TLS ve ag guvenligi |
| K011-application | [[K011-application]] | ../K011-application/index.md | auth middleware |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | API guvenligi |

---

# K009-security — Domain Künyesi

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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
