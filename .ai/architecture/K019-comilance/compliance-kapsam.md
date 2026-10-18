---
node_id: K019-comilance/compliance-kapsam.md
node_type: subdomain
status: draft
parent_id: K019-comilance
children: []
depends_on:
  - K007-data
  - K009-security
  - K011-application
  - K012-services
  - K013-integraion
evidence:
  - "compliance-master.md §2 (kapsam/kapsam dışı tablosu) — okundu 2026-10-18"
  - "policies/index.md §Politika kaydı (8 satır) + §Kapsam kararı kaydı — okundu 2026-10-18"
  - "controls/index.md §OWASP Top 10:2025 haritası + §ISO Annex A + §K000–K013 domain uyum haritası — okundu 2026-10-18"
  - "K019-comilance/ disk ölçümü 2026-10-17: 6 md (index · master · policies/index · controls/index · controls/kod-ornekleri · verification/index)"
  - "KVKK 6698 — kvkk.gov.tr (2026-10-17 doğrulandı) · ISO/IEC 27001:2022 Annex A 93 kontrol/4 tema — secureframe/drata (2026-10-17)"
validation:
  - "2026-10-18 — §2/§3 satırları compliance-master.md §2 + policies/index.md §Politika kaydı ile çapraz okundu (çelişki yok)"
  - "2026-10-18 — kapsam dışı 5 komşu (K007/K009/K011/K012/K013) her satırda etiketli"
  - "2026-10-18 — uyumluluk iddiası içeren tek bir cümle yok (zero-hallucination)"
open_questions:
  - "GDPR koşulu: AB veri öznesi tespiti yapılmadı → politika #8 şartlı etkin (⚠️ VERIFICATION REQUIRED)"
  - "ISO SoA (Statement of Applicability) yazılmadı — hangi A.5–A.8 kontrolü CoreMusic'e uygulanır birebir tanımsız (⚠️ VERIFICATION REQUIRED)"
  - "Lisans denetimi `K020-licence/` klasöründe tutulacaktı; klasör diskte YOK → nerede tutulacak üst karar (⚠️ VERIFICATION REQUIRED)"
  - "registry index.md §5 madde 21 hâlâ 'K020-comilance' diyor; disk kanıtı 'K019-comilance' → registry güncellemesi üst karar (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# compliance-kapsam — Uyum Kapsamı Sınırı

> Dayanak: `compliance-master.md` §2 · `policies/index.md` · `controls/index.md`.
> Bu dosya K019-comilance'ın **içinde / dışında / komşu** ayrımının kök dökümüdür.
> Çelişkide `compliance-master.md` kazanır; bu dosya kapsam sınırını ayrıntılandırır.

## §1 ELI15

> **K019-comilance, projenin "kurallara uyuyor muyuz?" sorusunun kanıt rafıdır —
> kuralların kendisi değil, uydugunu kanıtlayan tarihli kayıtlar burada durur.**

Uyum üç ayaklıdır: (1) **kural** (politika — nerede yazılı?), (2) **kontrol**
(nasıl sağlanıyor — kodda/mimari/organizasyonda karşılığı var mı?), (3) **kanıt**
(ne zaman denetlendi, sonuç neydi — tarihli satır). Üç ayaktan biri eksikse kayıt
`denetlenmedi` veya `VERIFY REQUIRED` kalır; "uyumlu" cümlesi **asla yazılmaz**.

## §2 Kapsam İÇİNDE (K019-comilance'a ait)

| # | Alan | Ne kapsar | Sahip çocuk | Etiket |
|---|---|---|---|---|
| I1 | Politika kayıt defteri | "Ne kural?" — 8 satır (güvenlik · kalite · vault LINT · domain sınırı · WCAG · KVKK · ISO · GDPR koşullu) | policies/ | [TASARIM] |
| I2 | OWASP kontrol haritası | A01–A10 ↔ vault kontrolü ↔ denetim durumu | controls/ | [TASARIM] |
| I3 | ISO 27001 çerçevesi | ISMS + Annex A 93/4 tema — SoA öncesi iskelet | controls/ | [TASARIM] |
| I4 | K000–K013 domain uyum haritası | 14 domain ↔ OWASP/KVKK teması ↔ sorumlu agent | controls/ | [TASARIM] |
| I5 | Kontrol kod şablonları | 11 dilde (PHP·JS·Python·Go·C#·Java·C/C++·Rust·SQL·Bash·YAML+Rego) | controls/kod-ornekleri.md | [TASARIM] |
| I6 | Denetim turu kayıtları | "Ne zaman denetlendi, sonuç ne" — tur-1 (2026-10-12) · tur-2 (2026-10-17) | verification/ | [TASARIM] |
| I7 | KVKK uyum programı | m.4 işleme ilkeleri · m.6 özel nitelikli · m.12 tedbir+ihlal · m.16 VERBİS · m.18 yaptırım | policies/ | [KARAR] |
| I8 | ISO ISMS çerçevesi | Annex A teması + risk tedavisi + SoA zorunluluğu | policies/ | [KARAR] |
| I9 | GDPR koşullu kapsam | Yalnız AB veri öznesi tespit edilirse tam kapsam | policies/ | dalga-3 |

## §3 Kapsam DIŞI (K019-comilance'a ait DEĞİL)

| # | Dışarıda | Nerede işlenir | Neden |
|---|---|---|---|
| D1 | Bağımlılık **lisans** uyumu | `K020-licence/` **beklenirdi — diskte YOK** (registry §5 madde 23) | Ayrı denetim ailesi; klasör boşluğu açık soru |
| D2 | Kod incelemesi (code review) | `code-reviewer` akışı · `.ai/WORKFLOW.md` | Süreç denetimi, uyum kaydı değil |
| D3 | Runtime güvenlik testi (pentest) | `penetration-tester` agent'ı · K009-security | Saha denetimi; sonuçları buraya **girer**, üretimi burada değil |
| D4 | AB GDPR tam uygulaması | Koşul tespiti yapılmadı → politika #8 **şartlı etkin** | Veri öznesi yoksa kapsam genişletilmez |
| D5 | Sertifika iddiası (ISO 27001 / KVKK) | **Hiçbiri yoktur, yazılmaz** | Denetim/sertifikasyon yapılmadı |
| D6 | KVKK hukuki danışmanlığı | Hukuk uzmanı | Bu vault hukuki mütalaa üretmez; dayanak maddeleri kaydeder |
| D7 | KVKK VERBİS kayıt işlemi | Vault Steward + güvenlik sorumlusu | Kayıt yapıldı mı kanıt yok → açık soru #9 |

## §4 Komşu sınırı (kim neyin sahibi — bu dosyanın sahip olmadığı)

| Komşu K | İlişki | Sınır |
|---|---|---|
| K009-security | güvenlik **politikasının kendisi** (crypto · auth · CSRF/CSP · rate limit) | K019 yalnız o politikanın **uyum kaydını** tutar; kontrol tasarımı K009'da |
| K007-data | veri şeması, PII saklama/silme **mekanizması** | K019 KVKK m.10/m.12'ye uyum **kanıtını** ister; şema K007'de |
| K011-application | uygulama güvenli tasarımı | K019 denetim sonucunu kaydeder, kodu değiştirmez |
| K012-services | API güvenliği / gateway | OWASP A01/A07 eşlemesi K019'da, uygulaması K012'de |
| K013-integraion | dış servis sözleşmeleri / tedarikçi | OWASP A03/A08 eşlemesi K019'da; entegrasyon K013'te |
| K17-devops | CI/CD kapıları (secret-scan · composer-audit · lint) | Kapı **çalıştırılması** K17'de; uyum **kaydı** burada |
| K18-testing | test stratejisi / coverage | QA hedefi (.ai/AGENTS.md §16 ≥%80) K019 politika #2'de kayıtlı; ölçüm K18'de |

## §5 Kapsam kararları (kullanıcı onayı 2026-10-17 — bağlayıcı)

| # | Karar | Seçenek reddi | Kaynak |
|---|---|---|---|
| K1 | Uyum kapsamı = **KVKK + ISO/IEC 27001:2022** | GDPR yalnız AB veri öznesi tespit edilirse koşullu etkin | `policies/index.md` §Kapsam kararı kaydı |
| K2 | Disk kanıtı esas — klasör `K019-comilance` adıyla kalır | registry'deki `K020-comilance` yazımı | `compliance-master.md` §6 madde 4 |
| K3 | `comilance` yazımı korunur (v2 §3 KURAL 1) | `compliance` olarak düzeltilmesi | K019 index.md open_questions |
| K4 | Kod örnekleri 11 dil → `controls/kod-ornekleri.md` | kapsam dışı bırakılması | `policies/index.md` §Kapsam kararı kaydı |
| K5 | K000–K013 domain haritası + 5 md arası wiki-link tamamlandı | — | K019 index.md validation (2026-10-17) |

## §6 Ölü referanslar (⚠️ işaretli — silinmez, yazılmaz)

Bu K kapsamında geçen ve **diskte karşılığı olmayan** referanslar (ADR-097 K5 ·
registry §6.2-B — kök brief §Kapsam dışı):

| Ölü referans | Gerçek karşılık (disk) | Durum |
|---|---|---|
| `K020-comilance` | `K019-comilance` | ⚠️ registry §5 madde 21 hâlâ eski adı taşıyor — üst karar |
| `K020-licence` | **YOK** — lisans denetimi nerede? | ⚠️ açık soru (registry §5 madde 23) |
| `K020-comere` | **YOK** | ⚠️ hiçbir vault dosyasında geçmiyor |
| `K020-adr` | `.ai/.decisions/` (karar serisi) + `../adr/` (mimari ADR serisi) | ⚠️ kırık atıf 2026-10-17'de düzeltildi |
| `K019-testing` | `K18-testing` (disk) — frontmatter'da K019-testing yazar | ⚠️ ID CONFLICT — onaysız yeniden adlandırma YASAK |
| `K018-devops` | `K17-devops` (disk) | ⚠️ registry'de eski numara; disk kanıtı esas |

## §7 Uyum alanları kataloğu (master §3 ile aynı — özet)

| # | Alan | Dayanak | Durum |
|---|---|---|---|
| 1 | Güvenlik (OWASP Top 10:2025) | kök AGENTS.md §5 · `top10.owasp.org/2025/en` | kontrol iskeleti — denetlenmedi |
| 2 | Kalite standartları | `.ai/AGENTS.md` §16 | ölçülmüş değer yok → VERIFY REQUIRED |
| 3 | Vault kuralları / LINT | `.ai/CLAUDE.md` §6 + 16 Hard Guardrail | iskelet |
| 4 | Politikalar | `policies/index.md` — 8 kayıt satırı | kayıt genişledi · denetim yok |
| 5 | Bağımlılık lisansı | klasör diskte YOK | ⚠️ VERIFY REQUIRED |
| 6 | Gizli veri / secret yönetimi | kök AGENTS §5 · `.github/workflows/secret-scan.yml` | pipeline dosyası var, çalıştığı doğrulanmadı |
| 7 | Veri koruma (KVKK) | 6698 — m.4/m.6/m.12/m.16/m.18 (kvkk.gov.tr 2026-10-17) | kapsam kararlı · saha denetimi yok |
| 8 | Bilgi güvenliği (ISO 27001:2022) | Annex A 93/4 tema | çerçeve benimsendi · SoA yazılmadı |

## §8 Doğrulama

- [x] Frontmatter 9 alan (K005 kanonu — node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group)
- [x] `depends_on` yalnız diskte var olan K adları (K007/K009/K011/K012/K013 — 22 K listesi registry §5)
- [x] §2/§3 satırları master §2 + policies + controls ile çapraz okundu
- [x] Uyumluluk iddiası ("uyumlu", "sertifikalı") içeren cümle yok
- [x] Ölü referanslar §6'da ⚠️ işaretli — silinmedi, yazılmadı
- [ ] GDPR koşulu · ISO SoA · lisans klasörü → açık soru (master §7)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
