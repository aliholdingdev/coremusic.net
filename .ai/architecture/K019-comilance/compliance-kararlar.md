---
node_id: K019-comilance/compliance-kararlar.md
node_type: subdomain
status: draft
parent_id: K019-comilance
children: []
depends_on:
  - K009-security
  - K007-data
  - K017-devops
evidence:
  - ".ai/.decisions/index.md §3–§5 — 94 ADR (80 accepted · 10 rejected · 3 draft · 1 kök) — okundu 2026-10-18"
  - "compliance-master.md §4 (Denetim döngüsü — ADR kapısı) + §6 (Kayıt kuralları — ADR-042) — okundu 2026-10-18"
  - "controls/index.md §OWASP haritası — her A01–A10 satırındaki ADR atıfları — okundu 2026-10-18"
  - "K019-comilance/index.md — K020-comilance ID CONFLICT kaydı — okundu 2026-10-18"
validation:
  - "2026-10-18 — her ADR numarası .ai/.decisions/index.md ile birebir doğrulandı (uydurma numara yok)"
  - "2026-10-18 — red edilen ADR'ler (R-006 · R-007) yalnız 'reddedildi' bağlamında anıldı"
  - "2026-10-18 — draft ADR'ler (ADR-097 · ADR-103) durum etiketiyle (draft/onay bekliyor) anıldı"
open_questions:
  - "ADR-103 (medya-sağlayıcı auth gate) onaylanana kadar K020-discovery servis entegrasyonu kapalı kalacak mı? (⚠️ VERIFICATION REQUIRED)"
  - "OWASP kontrol haritasındaki 'denetlenmedi' satırları için ADR gerekir mi, düzeltme yeter mi? (compliance-master §7 madde 4 — ⚠️)"
  - "K019-comilance ID CONFLICT (registry 'K020-comilance' diyor) için ADR açılacak mı? (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# compliance-kararlar — Uyum Karar Defteri

> Dayanak: `.ai/.decisions/index.md` (94 ADR — disk ölçümü 2026-10-12). Bu dosya
> K019-comilance **bağlamındaki** kararları ADR formatında (Durum/Bağlam/Karar/
> Alternatifler/Sonuçlar) özetler. **Yeni ADR numarası vermez** — numaralar mevcut
> `.ai/.decisions/` defterinden alınır; uydurma numara yasaktır.

## §1 ELI15

> **Bu dosya, uyum rafını etkileyen mimari kararların "neden böyle" hikâyesini
> tek yerde toplar — hangi ADR hangi OWASP/KVKK kapısını kapattı, hangisini
> açık bıraktı.**

Kararlar üç sınıftır: (1) **kontrol kararları** (güvenlik kapısı — A01–A10
gövdesi), (2) **kayıt kararları** (append-only · ID · ölçüm kanonu), (3) **kapı
kararları** (onay öncesi entegrasyon yasağı). Her karar mevcut bir ADR'ye
bağlıdır; yeni numara uydurulmaz.

## §2 Uyum etkili kontrol kararları (accepted — frozen dahil)

| ADR | Başlık | Durum | Uyum etkisi (OWASP/KVKK) |
|---|---|---|---|
| ADR-002 | PDO Mandatory, ORM Yasak | accepted (frozen) | A05 injection — parametreli sorgu tek yol |
| ADR-003 | Multi-DB 9 BCNF Veritabanı | accepted (frozen) | A05 · KVKK m.4 ölçülülük — şema kalitesi |
| ADR-005 | Ultrathink Protocol (Zero Hallucination) | accepted (frozen) | A06 insecure design — kanıtsız iddia yasağı (uyum kaydının temeli) |
| ADR-008 | Bypass Auth Middleware | accepted (frozen) | A01 — hangi uçların auth dışı olduğu kararlı |
| ADR-010 | CSRF Protection Strategy | accepted (frozen) | A01 · A08 — `state`/PKCE doğrulaması |
| ADR-011 | Session Management | accepted (frozen) | A07 — oturum ömrü/rotation |
| ADR-012 | CSP Nonce Strict-Dynamic | accepted (frozen) | A02 security misconfiguration |
| ADR-013 | Rate Limiting APCu | accepted (frozen) | A01/A07 — brute-force kapısı |
| ADR-020 | API Public Security | accepted (frozen) | A01 — public API yetki sınırı |
| ADR-022 | Database Hardened Security | accepted (frozen) | A05 · KVKK m.12 — DB sertleştirme |
| ADR-034 | Credential Vault Normalization | accepted (frozen) | A02/A04 — secret yalnız `.env` |
| ADR-052 | Hybrid Auth Session + JWT | accepted | A07 — session cookie ≠ API JWT ayrımı |
| ADR-056 | Auth Modülü (RBAC + Permission middleware) | accepted | A01 — rol doğrulama gövdesi |
| ADR-058 | Merkezi Auth Servisi | accepted | A01/A07 — kimlik tek noktada |
| ADR-059 | JWT Kütüphanesi ve MFA TOTP | accepted | A07 — MFA zorunluluğu |
| ADR-091 | TemplateEngine eval() Kaldırımı (Guardrail #21) | accepted (kök) | A03/A08 — kod enjeksiyonu yüzeyi kapatıldı |
| ADR-094 | API pipeline'ına OriginCheck + koşullu CSRF | accepted | A01/A08 — ADR-020 sıra genişletmesi |
| ADR-095 | Hybrid JWT (RS256): issue/validate/revocation | accepted | A04/A07 — imzalama + iptal · secret/log yasağı §1.2 |
| ADR-100 | Cross-DB Yazmada Outbox Zorunlu | accepted | A08 integrity — tutarsız yazma engeli |
| ADR-101 | Güvenlik Kanonik Sırası + Uzak Çağrı (Timeout/Retry/Fallback) | accepted | A10 exceptional conditions — fallback zorunlu §2.2 |

## §3 Uyum etkili kapı kararları (draft — onay bekliyor)

| ADR | Başlık | Durum | Uyum etkisi |
|---|---|---|---|
| ADR-097 | K-Space V3: sıfırdan kurulum · çoklu-MD · isimlendirme · Band-2..7 | **draft** (onay bekliyor) | ID CONFLICT kapıları (K021-recomenataion · K18-testing) — düzenlenebilir |
| ADR-103 | Medya-Sağlayıcı Auth Gate: harici servis kimlik şeması kapısı | **draft** (2026-10-17 eklendi) | A01/A07 — K020-discovery tur-2 devri; onay öncesi üretim entegrasyonu kapalı |

## §4 Reddedilen kararlar (uyum bağlamında — referans, geri dönülmez)

| R-ADR | Başlık | Red nedeni | Uyum etkisi |
|---|---|---|---|
| R-006 | Laravel Eloquent ORM | ORM yasak | A05 — ORM injection yüzeyi reddedildi; ADR-002 ile çift kapı |
| R-007 | Firebase Authentication | harici bağımlılık | A01/A07 — kimlik verisi üçüncü tarafa devredilmedi |

## §5 Örnek ADR özetleri (Nygard formatı — 4 karar)

### K1 — Uyum kapsamı kararı (KVKK + ISO 27001)

- **Durum:** accepted (kullanıcı onayı 2026-10-17) — kayıt: `policies/index.md` §Kapsam kararı
- **Bağlam:** Uyum çerçevesi tanımsızdı; KVKK · GDPR · ISO 27001 üçü de adaydı.
- **Karar:** Aktif uyum çerçevesi = **KVKK (6698) + ISO/IEC 27001:2022**; GDPR yalnız AB veri öznesi tespit edilirse koşullu etkinleşir.
- **Alternatifler:** (a) üçünü birden zorunlu kılmak — hedef kitleyle orantısız; (b) yalnız KVKK — ISO çerçevesi eksik kalır; (c) hiçbiri — denetim kapısı olmaz.
- **Sonuçlar:** + net kapsam · KVKK m.16 VERBİS açık sorusu · ISO SoA yazımı bekliyor.

### K2 — Append-only kayıt kuralı

- **Durum:** accepted — dayanak ADR-042 (Vault Restructuring) + `compliance-master.md` §6
- **Bağlam:** Denetim kayıtları düzeltilirse denetim geçmişi kaybolur; kanıt zinciri kopar.
- **Karar:** Mevcut satır **düzeltilmez**; yanlışsa yeni satır + `⚠️` notu eklenir (append-only).
- **Alternatifler:** (a) satır-satır edit — geçmişi siler; (b) sürümleme — karmaşık, md yeter.
- **Sonuçlar:** + denetim geçmişi korunur · − yanlış satır tarihsel olarak kalır (işaretli).

### K3 — Ölçüm kanonu

- **Durum:** accepted — dayanak registry §9.12.1
- **Bağlam:** İki farklı satır sayma yöntemi (ReadAllLines vs Measure-Object) farklı değer üretiyordu.
- **Karar:** Dosya ölçümü kanonik yöntemi = `[System.IO.File]::ReadAllLines().Length`.
- **Alternatifler:** `Get-Content | Measure-Object -Line` — **geçersiz** sayılır.
- **Sonuçlar:** + tutarlı sayı · − PowerShell sürüm farkları bu yöntemle gizlenir (raporlanır).

### K4 — Disk kanıtı esas (ID CONFLICT çözümü)

- **Durum:** accepted (kullanıcı onayı 2026-10-17)
- **Bağlam:** Disk `K019-comilance`, registry `K020-comilance` diyordu.
- **Karar:** Disk kanıtı esastır; klasör `K019-comilance` adıyla kalır; registry güncellemesi üst karara bağlı.
- **Alternatifler:** (a) disk'i registry'ye uydurmak — yeniden adlandırma yasağı; (b) sessiz düzeltme — iz kaybı.
- **Sonuçlar:** + disk/registry farkı açık soru olarak tescilli · registry §5 madde 21 güncellenmedi.

## §6 Karar kapıları (uyum döngüsündeki ADR tetikleyicileri)

```text
① Politika yaz (policies/)  →  ② Kontrol ata (controls/)
        →  ③ Kod gövdesi (kod-ornekleri)
        →  ④ Denetle (verification turu)
        →  ⑤ fail ise  → ADR aç (karar serisi) veya düzelt
        →  ⑥ open_questions → üst karar
```

| Kapı | Ne tetikler | ADR tipi |
|---|---|---|
| OWASP kontrolü `fail` | güvenlik düzeltmesi veya istisna | `.ai/.decisions/accepted/` |
| KVKK m.12 ihlali | 72 saat bildirim + kök neden ADR | accepted + Kurul kaydı |
| ISO SoA boşluğu | hangi A.5–A.8 uygulanır | draft → accepted |
| ID CONFLICT | registry vs disk | ADR-097 (draft) kapsamında |

## §7 Doğrulama

- [x] Her ADR numarası `.ai/.decisions/index.md` §3–§5 ile birebir doğrulandı (94 dosya · 2026-10-12 ölçümü)
- [x] Uydurma ADR numarası yok — yalnız mevcut defter numaraları kullanıldı
- [x] Draft/red durumları etiketlendi
- [x] Yeni ADR **açılmadı** (bu dosya karar üretmez, kaydeder)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
