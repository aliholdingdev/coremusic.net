---
node_id: K019-comilance/controls
node_type: subdomain
status: draft
parent_id: K019-comilance
children:
  - kod-ornekleri.md
depends_on:
  - K019-comilance
  - policies
evidence:
  - "OWASP Top 10:2025 resmi liste — https://top10.owasp.org/2025/en (okundu: 2026-10-12; yeniden doğrulandı: 2026-10-17)"
  - "ISO/IEC 27001:2022 Annex A yapısı — 93 kontrol / 4 tema (A.5 organizasyonel 37 · A.6 kişiler 8 · A.7 fiziksel 14 · A.8 teknolojik 34); 2013→2022 geçişinde 114/14 alandan 93/4 temeye indirildi, 11 yeni kontrol eklendi (tehdit istihbaratı, bulut, veri maskeleme dahil) — secureframe.com/hub/iso-27001/controls · drata.com/learn/iso-27001/controls-annex-a · glocertinternational.com (okundu: 2026-10-17)"
  - "Vault kontrolleri: kök AGENTS.md §5 Security · .ai/AGENTS.md §16 · accepted/ADR-010, ADR-012, ADR-013, ADR-020, ADR-022, ADR-059, ADR-095, ADR-101"
  - "Domain listesi disk ölçümü: .ai/architecture/ K000–K013 arası 14 klasör mevcut (2026-10-17, Test-Path doğrulaması)"
validation:
  - "2026-10-12 — OWASP A01–A10 başlıkları resmi OWASP kaynağından doğrulandı"
  - "2026-10-17 — ISO 27001:2022 Annex A yapısı (93/4 tema) web kaynaklarından doğrulandı; K000–K013 domain haritası eklendi"
  - "2026-10-17 — kırık referans düzeltmesi: '../K020-licence/' klasörü diskte YOK (ölçüm) → dürüst not ile işaretlendi"
  - "2026-10-17 — node_id diske hizalandı: K019-comilance (kullanıcı kararı: disk kanıtı esastır)"
open_questions:
  - "⚠️ ID CONFLICT (kısmen kapandı 2026-10-17): kullanıcı kararı disk kanıtını esas aldı — klasör K019-comilance adıyla kalır; registry index.md §5 madde 21 hâlâ 'K020-comilance' diyor → registry reconciliationsı üst karar"
  - "ISO 27001 SoA (Statement of Applicability) henüz yazılmadı — hangi A.5–A.8 kontrolü CoreMusic'e uygulanır tek tek tanımsız → VERIFY REQUIRED"
  - "'K020-licence/' klasörü diskte YOK — bağımlılık lisans denetimi hangi dosyada tutulacak? (registry §5 madde 23'te 'dependency-licenses.md' bekleniyor)"
  - "Her kontrolün son denetim tarihi ve sonucu YOK → tüm satırlar denetlenmedi"
group: mimari
---


# K019-comilance/controls — Kontrol Haritası

**ELI15:** "Kural nasıl sağlanıyor?" cevabı — her satır bir kontrol ve **denetim durumu**; henüz denetlenmemiş satır `denetlenmedi` yazılır, `geçti` yazılmaz. Kod gövdesi: [[kod-ornekleri]] (11 dil).

## OWASP Top 10:2025 haritası (kaynak: `top10.owasp.org/2025/en`)

| # | OWASP kategorisi | CoreMusic kontrolü (vault kanıtı) | Son denetim |
|---|---|---|---|
| A01 | Broken Access Control | auth middleware + rol doğrulama · `ADR-010` · `ADR-020` · CSRF (`kod-ornekleri` §2.4) · en az yetki GRANT (§10.2) | denetlenmedi |
| A02 | Security Misconfiguration | CSP nonce (`ADR-012`) · `.env`-only secret · `ADR-022` · CSP middleware (`kod-ornekleri` §3.1) · `.env` izin kapısı (§11.2) | denetlenmedi |
| A03 | Software Supply Chain Failures | GitLeaks `secret-scan.yml` · bağımlılık lisansı → lisans denetimi **klasör diskte yok** (açık madde) · gitleaks kapısı (`kod-ornekleri` §11.1) · CI kapıları (§12.1) | denetlenmedi |
| A04 | Cryptographic Failures | AES-256-GCM/ChaCha20 · Argon2id · MD5/SHA-1/DES yasak · 5 dilde GCM şablonu (`kod-ornekleri` §2.3 · §4.2 · §5.1 · §6.1 · §7.1 · §8.1 · §9.1) | denetlenmedi |
| A05 | Injection | parametreli sorgu zorunlu · `SELECT *` yasak · no ORM (`ADR-002`) · 6 dilde şablon (`kod-ornekleri` §2.1 · §3.2 · §4.1 · §5.2 · §6.2 · §7.2 · §9.3) | denetlenmedi |
| A06 | Insecure Design | `ADR-098` · `ADR-099` · güvenlik kanonik sırası (`ADR-101`) · policy-as-code (`kod-ornekleri` §12.2) | denetlenmedi |
| A07 | Authentication Failures | `ADR-052/058` · JWT RS256 + MFA (`ADR-059/095`) · Argon2id şablonları (§2.2 · §4.3 · §9.2) · rate limit (§3.3) | denetlenmedi |
| A08 | Software or Data Integrity Failures | outbox (`ADR-100`) · origin+CSRF (`ADR-094`) · migration checksum (`kod-ornekleri` §10.4) · imzalı firmware (§8.2) · dependency-review (§12.1) | denetlenmedi |
| A09 | Security Logging and Alerting Failures | `log.md` append-only audit trail · audit_log tetikleyici kilidi (`kod-ornekleri` §10.1) · KVKK 72s sayacı (§2.5) | denetlenmedi |
| A10 | Mishandling of Exceptional Conditions | fail-safe (fail-open değil) · `§17 Edge Cases` · Go/Rust hata disiplini (`kod-ornekleri` §5.2 · §9.2) | denetlenmedi |

> 2025 farkı: A03 ve A10 **yeni** kategoriler; SSRF artık A01 kapsamındadır.
> 2026-10-17 yeniden doğrulama: liste `top10.owasp.org/2025/en` ile birebir.

## ISO/IEC 27001:2022 Annex A haritası (kısmi — SoA öncesi başlangıç)

> **Kaynak:** Annex A = **93 kontrol / 4 tema** (A.5 organizasyonel 37 · A.6
> kişiler 8 · A.7 fiziksel 14 · A.8 teknolojik 34). 2022 geçişinde 11 yeni
> kontrol eklendi (tehdit istihbaratı, bulut hizmetleri, veri maskeleme, güvenli
> kodlama dahil). Kaynak: secureframe · drata · glocertinternational (2026-10-17).
>
> **Dürüstlük sınırı:** Aşağıdaki seçki **başlangıç**tır; ISO standardının kendisi
> ücretlidir → kontrol numaralarının birebir metin eşleşmesi `⚠️ VERIFY REQUIRED`.
> Uygulama kararı = SoA (Statement of Applicability) — henüz yazılmadı.

| Tema | Çekirdek(CoreMusic) | Vault karşılığı | Durum |
|---|---|---|---|
| A.5 Organizational (37) | politikalar · roller · tedarik zinciri · gizlilik/PKK (KVKK) · olay öğrenme | `policies/index.md` · kök AGENTS §5 · KVKK m.12 | iskelet |
| A.6 Persons (8) | farkındalık · ekip sorumlulukları | `.ai/AGENTS.md` §16 agent sorumlulukları | iskelet |
| A.7 Physical (14) | sunucu/cihaz fiziksel güvenliği | **kapsam tanımsız** (CoreMusic bulut/self-host ayrımı yok) | `⚠️ VERIFY REQUIRED` |
| A.8 Technological (34) · seçki | erişim kontrolü · imtiyazlı erişim · kripto kullanımı (A.8.24 benzeri) · loglama · izleme · veri maskeleme/PPD · güvenli geliştirme/kodlama | OWASP A01–A10 ile örtüşür · `kod-ornekleri.md` | iskelet |

## Vault içi kalite kontrolleri (§16 — hedef, ölçüm DEĞİL)

| Alan | Hedef | Gerçek ölçüm |
|---|---|---|
| Backend · UI · Security · Data | %100 | `⚠️ VERIFY REQUIRED` |
| QA | ≥%80 coverage · %0 flaky | `⚠️ VERIFY REQUIRED` |
| DevOps | CI/CD ≥%95 · GitLeaks temiz | `⚠️ VERIFY REQUIRED` |

## K000–K013 domain uyum haritası (kullanıcı kararı 2026-10-17 — "İkisi de")

> **Yöntem:** her satır diskte doğrulanmış bir domain klasörüne bağlanır
> (`Test-Path` ölçümü 2026-10-17: 14/14 mevcut). "Uyum bağlantısı" = o domain'in
> hangi kontrol ailesine girdiği; denetim sonucu **henüz alınmadı** → hepsi
> `denetlenmedi`.

| Domain (disk) | Uyum bağlantısı | OWASP / kanonik | KVKK teması | Sorumlu agent | Durum |
|---|---|---|---|---|---|
| `K000-isletim-sistemi` | OS yapılandırma · yetki · patch | A02 · A10 | — | embedded-engineer | denetlenmedi |
| `K001-donanım` | donanım güvenliği · ESD/fiziksel | A02 · ISO A.7 | — | audio-hw | denetlenmedi |
| `K002-electroncis` | devre/BOM güvenliği · tedarik zinciri | A03 | — | audio-hw | denetlenmedi |
| `K003-audio` | sürücü/cihaz erişimi · zaman-kritik yüzey | A01 · A10 | — | embedded-engineer | denetlenmedi |
| `K004-dsp` | zaman-kritik gerçek-zamanlı · fail-safe | A10 | — | embedded-engineer | denetlenmedi |
| `K005-firmware` | imzalı firmware · boot zinciri · OTA | A08 · A02 | — | dsp-fw | denetlenmedi |
| `K006-yapay-zeka` | model verisi · eğitim verisi gizliliği | A02 · A04 | **Evet — özel nitelikli veri olabilir (m.6)** | ai-engineer | denetlenmedi |
| `K007-data` | BCNF · parametreli sorgu · PII saklama/silme | A05 · A04 | **Evet — m.4 ölçülülük · m.10 imha · m.12 muhafaza** | data-engineer | denetlenmedi |
| `K008-domain` | domain kuralları · olay bütünlüğü | A06 · A08 | kısmi | backend-developer | denetlenmedi |
| `K009-security` | crypto · auth · CSRF/CSP · rate limit | A01 · A02 · A04 · A07 | **Evet — m.12 teknik/idari tedbir** | security-engineer | denetlenmedi |
| `K010-network` | TLS · ağ yapılandırması · SSRF | A01 · A02 | — | network-engineer | denetlenmedi |
| `K0100-identitiy` | kimlik · MFA · parola (Argon2id) · oturum | A07 · A01 | **Evet — kimlik verisi (m.2)** | backend-developer | denetlenmedi |
| `K011-application` | uygulama güvenli tasarım · yetki denetimi | A06 · A01 | kısmi | backend-developer | denetlenmedi |
| `K012-services` | API güvenliği · gateway · rate limit | A01 · A07 | kısmi | backend-developer | denetlenmedi |
| `K013-integraion` | sözleşmeler · tedarikçi güvenliği · OpenAPI doğrulama | A03 · A08 | **Aktarım (m.8/9)** | backend-developer | denetlenmedi |

> **Not (disk gerçekliği):** üstteki harita **disk adlarıyla** yazıldı. Registry
> (`index.md` §5) ile disk arasında K016/K17/K18/K019 bandında numara sapması
> var (registry: K017-observability · K018-devops · K019-testing; disk:
> K016-observability · K17-devops · K18-testing · K019-comilance) — bu sapma
> `⚠️ VERIFY REQUIRED` (üst karar; bu dosya düzeltemez).

**Durum:** harita iskeleti · sonuç satırları `verification/index.md`'ye yazılır.

## Kod örnekleri (dosya)

| Dosya | Kapsam | Durum |
|---|---|---|
| `kod-ornekleri.md` | 11 dil (PHP · JS · Python · Go · C# · Java · C/C++ · Rust · SQL · Bash · YAML+Rego) — A01–A10 + KVKK şablonları | draft şablon |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi · OWASP+ISO+K000–K013 kontrol haritası | Kademe-2 |
| kod | `kod-ornekleri.md` | 11 dilde uyum kontrolü kod şablonları | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
