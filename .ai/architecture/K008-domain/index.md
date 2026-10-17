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
  - "ADR-097 §5.5 — İki-Ekseni Gruplama Şeması (group alanı + §GRUPLAR)"
validation:
  - "2026-10-12 — children listesi v2 §2.8 mini-ağaçı ile birebir karşılaştırıldı (10/10)"
  - "2026-10-12 — 12 kök MD + 6 çocuk × 6 MD üretilmiştir (v1+v2 + ADR-097 K2/K3 şeması)"
  - "2026-10-12 — web kanıtları HTTP HEAD/GET ile doğrulandı (tekil URL, bkz. -web-arastirma.md)"
  - "2026-10-15 — 10 çocuk × 6 MD = 60 çocuk dosyası + 12 kök MD = 72/72 tamamlandı; §GRUPLAR her çocukta 6 satır (mimari:3 · kod:1 · kanit:2); 89 tekil URL havuzu çakışmasız (bkz. -web-arastirma.md §16)"
open_questions:
  - "v2 §6: K008-domain ↔ eski disk K008-servisler → ID CONFLICT — VERIFY REQUIRED (onaysız yeniden adlandırma yok)"
  - "recommendation/discovery sahipliği: K006-yapay-zeka · K020-recomenataion · K020-discovery ile örtüşme → ⚠️ VERIFICATION REQUIRED"
  - "shared/AGENTS.md (2026-10-12) 'K-space kaldırıldı' notu ile ADR-097 sıfırdan kurulum kararı arasındaki ilişki → ⚠️ VERIFICATION REQUIRED"
  - "Kademe-3 (bounded-context) yapısı v2'de tanımlı değil → her çocuk için -bilesenler.md taslağı ADR onayı bekliyor"
group: mimari
## §BAĞLANTILAR

> 2026-10-17 · orkestratör · kaynak: K000–K013 arasi gercek dosya taramasi (ad gecen K eslesmesi) + v2 S4.2 depends_on · iliskiler dogrulanmadir, kesin sozlesme degildir.

| Hedef K | Wiki-link | Göreli dosya yolu | İlişki |
|---|---|---|---|
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | audio domain modelleri |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | DSP domain sinirlari |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | AI domain baglami |
| K007-data | [[K007-data]] | ../K007-data/index.md | entity persistence |
| K012-services | [[K012-services]] | ../K012-services/index.md | servis sozlesmeleri |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | event ve DTO paylasimi |

---

# K008-domain — Domain Künyesi

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

> **Authority:** Bayram Ali / Vault Steward · **Last Updated:** 2026-10-12 · **Mode:** Red Team · Human Mode · Truth Mode
