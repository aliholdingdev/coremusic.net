---
node_id: "K009-security/threat-model/threat-model-kapsam"
node_type: "module"
status: "draft"
parent_id: "K009-security"
children: []
depends_on: []
evidence:
  - ".ai/architecture/K009-security/security-riskler.md (R-01..R-14 kayıt defteri — tehlikelerin sahibi, tehdit modeli onu genişletmez)"
  - ".ai/.decisions/accepted/ADR-014-multi-db-migration-strategy.md (migration kararı)"
  - "shared/AGENTS.md §2 (envanter: database/migrations/ + src/Middleware envanteri)"
validation: "Draft — anayasa'daki sınır matrisi (U-5 açık sorusu) ile birlikte onaya sunulur; R atıfları security-riskler.md:36-49 ile hizalı (2026-10-15)"
open_questions: []
group: "mimari"
---

# K009 · threat-model · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Kapsam Sınırı: Ne Modelleme Dışı, Ne Hariç

---

## §1 KAPSAM İÇİ (modellenen)

### §1a Varlıklar (anayasa §4 ile hizalı)

1. **Kullanıcı oturumu + kimlik** (session, refresh, OTP, CSRF token'ı) — R-04 (CSRF), R-09 (cookie hırsızlığı).
2. **Yönetici yüzeyi** (`/admin/*`, `/api/admin/*`) — R-01 (BypassAuth whitelist, **AÇIK — test yok**), R-14 (rol karşılaştırma zaafı).
3. **1p API** (`/api/v1/*` dahil `public/`, `admin/`, `dev/*`) — R-02 (JWT sahtecelik), R-06 (enjeksiyon), R-12 (brute-force).
4. **Paylaşılan middleware katmanı** (`shared/src/Middleware/*`, `shared/src/Security/*`) — ortak kapı: OriginCheck, CSRF, CORS, RateLimiter, BypassAuth, ReturnUrlPolicy, SecurityHeaders.
5. **DB migration dosyaları** (`database/migrations/` — `shared/AGENTS.md` §2 envanteri) — şema bütünlüğü: risk defterinde ayrı R kaydı **YOK** (⚠️ kayıt boşluğu); migration kararı ADR-014.
6. **Konfigürasyon env'i** (`.env.*` şablonları; gerçeği gizli) — R-08 (log/yanıt sızıntısı), R-11 (anahtar saklama).
7. **CI/CD pipeline** (`.github/workflows/*.yml`) — deploy gating; ayrı R kaydı yok (⚠️).
8. **Deploy betikleri** (`scripts/*.ps1`) — doğrudan R kaydı yok (⚠️); en yakın yüzey R-06 (command injection).

### §1b Saldırı yüzeyleri (tehdit modelinin girdisi)

| Yüzey | Nerede | Ana tehdit sınıfı |
|---|---|---|
| Tarayıcı → 1p origin | `coremusic.net`, `www`, `cdn` | sınıf S (sahtecilik), sınıf 8 (açık redirect) |
| Origin ↔ origin | `*.workers.dev` preview, 3p script | sınıf 10 (misconfig), sınıf I |
| Admin oturumu | `/admin/*` | S + E (auth atlatma), sınıf D (brute-force) |
| CLI/terminal | `scripts/*.ps1`, deploy | E (yetki), I (secret sızıntısı) |
| Dosya sistemi | `database/migrations/`, `scripts/*.ps1` | Sınıf T (şema/veri bozulması), sınıf R (log yoksayma) |
| Paket ağacı | `shared/vendor`, npm lockfile'ları | §3.2-12 bağımlılık CVE |

### §1c Zaman penceresi

- **Birincil:** 2026-10 anlık görüntü (repo HEAD).
- **İkincil:** 2026-09 lansman sonrası düzeltmeler (ADR-010 CSRF, ADR-011 session, ADR-013 rate-limiting, ADR-020/ADR-022 origin-CORS) — bu ADR'lerin kararları **kabul edilmiş kontrol** sayılır.

---

## §2 KAPSAM DIŞI (bilinçli hariç — gerekçeli)

1. **Üçüncü taraf OAuth sağlayıcılarının iç güvenlikleri** (Google/GitHub/Microsoft) — kendi tehdit modelleri var; sadece **entegrasyon sınırı** modellenir (redirect_uri allowlist, token saklama).
2. **Fiziksel/altyapı katmanı** (sunucu, ağ, DNS sağlayıcısı) — altyapı tehdidi bu modellemenin parçası değil; ayrı gate/runbook kanıtı bu vault'ta **bulunamadı** (⚠️ VERIFICATION REQUIRED).
3. **Kullanıcı cihazı/güvenilirliği** — phishing, zararlı eklenti; sadece sunucu tarafı savunmalar modellenir.
4. **`.ai/` vault içeriği ve `.opencode/` kurallar** — dahili dokümantasyon; sızıntı riski var ama **bilgi-gizliliği** etkisi düşük (secret yasağı: kök `AGENTS.md` §16.3 — anahtar asla `.md`/`.json`/`.log`'a yazılmaz).
5. **Non-kritik statik içerik** (CSS/JS asset'leri) — XSS hariç; XSS zaten T-01 kapsamında.
6. **Mobile/CLI native uygulamalar** — henüz yok (product-scope: web-first).

---

## §3 SINIR MATRİSİ (İçeri/Dışarı Sınırı — U-5 açık sorusu)

| # | Konu | İçeri mi? | Gerekçe |
|---|---|---|---|
| S-1 | `shared/` middleware'leri | ✅ İçeri | Her 1p origin'i kullanır; ortak kapı |
| S-2 | 3p script'leri (JS) | ⚠️ Yarı | XSS yüzeyi modellenir; kendi tehditleri hariç |
| S-3 | `workers.dev` preview origin | ✅ İçeri | "auth olmadan yayınlanmaz" kuralının hangi ADR'de kararı **UNKNOWN** (⚠️ VERIFICATION REQUIRED) — tehdit kontrolü sayılması Vault Steward onayı ister |
| S-4 | OAuth redirect_uri'leri | ✅ İçeri (sadece sınır) | open-redirect + token exchange |
| S-5 | DB migration'lar | ✅ İçeri | R-06 (enjeksiyon yüzeyi, KISMİ) — şema-bozma için ayrı R kaydı YOK (⚠️) |
| S-6 | Altyapı/DNS | ❌ Hariç | bkz §2.2 |
| S-7 | `.ai/` vault | ❌ Hariç | kök `AGENTS.md` §16.3: secret yasak |
| S-8 | Deploy betikleri | ✅ İçeri | ayrı R kaydı yok (⚠️) |
| S-9 | `npm audit` / dependency | ⚠️ Yarı | §3.2-12 bağımlılık CVE — R-19 **UNKNOWN** (defterde) |
| S-10 | CI secrets | ✅ İçeri | sınıf I (secret ifşası) — repo'nun en yüksek etkisi: "GitHub'da açık text secret var" ifşası |

---

## §4 TEHDİT KAYDI ↔ R-DEFTERİ SINIRI

- **Tehdit modeli ≠ risk defteri.** `security-riskler.md` R-01..R-14 **risk** (etki+gerçekleşme) taşır; bu model **tehdit** (potansiyel + yüzey) taşır.
- Bağlantı: her tehdit → en az 1 risk kaydı (varsa) → kontrol (varsa) → kanıt (varsa).
- **Sınır kararı (anayasa T-08 kuralı):** K009 dışında bir domene (K010/K000/K014) tehdit-modeli kararı yazılmaz; sınırların devri Vault Steward onayı ister.

---

## §5 KAPSAM DEĞİŞİKLİK PROTOKOLÜ

1. Kapsama yeni bir **yüzey** (servis, endpoint sınıfı, deploy betiği) eklenirse **tekrar modelle** tetiklenir (anayasa §7).
2. Kapsam dışına bir şey taşınırsa (örn. altyapı) **gerekçe + sahip** yazılır — sessiz silme yasak.
3. Her revizyon: anayasa §7'deki "ne zaman tekrar" tetikleyicilerine bağlanır.

---

## §2a KAPSAM DIŞI — SAHİP VE GERİ DÖNÜŞ KOŞULU (§2 maddelerinin eşleniği)

§2 listesi hariçliği **gerekçeli** yazar; bu tablo her maddeye sahip ve "nasıl geri alınır" koşulu bağlar (sessiz silme yasak — §5/2).

| # (§2) | Hariç konu | Sahibi | Geri dönüş koşulu (kapsama alınma tetikleyicisi) |
|---|---|---|---|
| 1 | 3p OAuth sağlayıcı iç güvenlikleri | K010-identitiy (yalnız entegrasyon sınırı) | redirect_uri/token saklama yüzeyi değişirse → yalnız sınır yeniden modellenir |
| 2 | Fiziksel/altyapı (sunucu, ağ, DNS) | K014-network · devops-engineer | altyapı gate/runbook kanıtı vault'a girerse (⚠️ şu an YOK) → S-6 yeniden değerlendirilir |
| 3 | Kullanıcı cihazı / phishing | kullanıcı + istemci tarafı | sunucu tarafı yeni doğrulama yüzeyi eklerse → S-1 kapsamına girer |
| 4 | `.ai/` vault + `.opencode/` kuralları | Vault Steward | secret yasağı (kök `AGENTS.md` §16.3) ihlal edilirse → anayasa §7 tetikleyicisi 8 (olay) |
| 5 | Statik içerik (CSS/JS) | frontend | XSS yüzeyi zaten T-01 kapsamında; yeni script enjeksiyon yüzeyi çıkarsa → modellenir |
| 6 | Mobile/CLI native uygulama | product scope (yok) | uygulama üretilirse → kapsam revizyonu zorunlu (§5) |

---

## §6 KAPSAM KARARI ↔ ADR BAĞI (hangi ADR kapsamı nasıl etkiledi)

> Bu dosya ADR **okumaz/üretmez**; kararlar sibling dosyalarda kayıtlıdır (bilesenler §3 D-01..D-06 · surucu §2d).
> Tablo yalnızca "kapsam hangi kararla değişti" izini taşır — ADR metni değiştirilemez (anayasa Y-10).

| ADR | Kapsam etkisi | K009 içi mi? | Kaynak (sibling kayıt) |
|---|---|---|---|
| ADR-008 (bypass auth) | bypass kapsamı yalnız ADR ile değişir → §1a-2 / §3 S-1 içinde | ✅ içeri | surucu §4 (`shared/AGENTS.md` §4 Yasak-2) · bilesenler §3 |
| ADR-010 (CSRF) | state-changing yüzeyi + origin/CORS yorumları → §1a-1 / §1b | ✅ içeri | bilesenler §3 D-01 · ornekler Ör-02/Ör-03 |
| ADR-011 (session) | çerez/oturum yüzeyi → §1a-1 (V-1 · R-09) | ✅ içeri | bilesenler §3 D-02 · ornekler Ör-10 |
| ADR-012 (CSP nonce) | başlık set'i yüzeyi → §1b (tarayıcı ↔ origin) | ✅ içeri | ornekler Ör-09 · dogrulama AD-14 |
| ADR-013 (rate-limit APCu) | fail-open/fail-closed sınırı → §1a-4 · §3 S-5 | ✅ içeri | bilesenler §3 D-04 · surucu §5 |
| ADR-014 (multi-DB migration) | migration yüzeyi → §1a-5 · §3 S-5 | ✅ içeri | dogrulama AD-10 |
| ADR-020 / ADR-022 (origin-CORS) | origin allowlist yüzeyi → §1b · §3 S-2/S-3 | ✅ içeri | ornekler Ör-01/Ör-02 · bilesenler §3 D-03 |

| Kural | |
|---|---|
| Yeni ADR | kapsam etkisi bu tabloya **ek satır** olarak yazılır (mevcut satır silinmez) |
| K010/K000/K014 kararı | ❌ yazılmaz → devir (§7 — anayasa T-08) |
| Çelişki | kapsam ↔ ADR çelişirse ADR metni kazanır (frozen), kapsam revize edilir (§5) |

---

## §7 SINIR DEVİR PROTOKOLÜ (T-08 — K009 dışı yüzey)

| Adım | İşlem | Çıktı | Sorumlu |
|---|---|---|---|
| D-1 | Yüzeyin sahibi domen belirlenir (K010-identitiy / K000-isletim-sistemi / K014-network) | domen adı | security-engineer |
| D-2 | Kanıt (Dosya·Satır) ile sınır gerekçesi yazılır | devir satırı | security-engineer |
| D-3 | Vault Steward onayı | ONAY / RED | Vault Steward |
| D-4 | Onay sonrası bu modelde yalnız **sınır referansı** kalır | §2 / §3 satırı | bu çocuk |

Devredilen yüzey tehdit-modeline **geri yazılamaz** (anayasa T-08 · surucu H-8); onaysız devir Layer violation → revert sayılır.

---

## §8 AÇIK KAPSAM KALEMLERİ (⚠️ — kapanışı gerekenler)

| # | Açık kalem | Etiket | Kapanış koşulu |
|---|---|---|---|
| O-1 | Altyapı gate/runbook kanıtı vault'ta yok (§2-2) | ⚠️ VERIFICATION REQUIRED | doküman diskte görününce S-6 yeniden değerlendirilir |
| O-2 | `workers.dev` preview "authsız yayınlanmaz" kararının ADR'si | UNKNOWN (§3 S-3) | ADR adı bulunana kadar tehdit kontrolü sayılmaz |
| O-3 | DB migration için ayrı R kaydı yok (§1a-5) | ⚠️ kayıt boşluğu | `security-riskler.md`'ye R satırı açılır (append) |
| O-4 | CI/CD için ayrı R kaydı yok (§1a-7) | ⚠️ kayıt boşluğu | aynı — R satırı + sahip (devops-engineer) |
| O-5 | Deploy betikleri için ayrı R kaydı yok (§1a-8 · §3 S-8) | ⚠️ kayıt boşluğu | aynı — en yakın yüzey R-06 (command injection) |
| O-6 | DFD diskte yok (anayasa U-1 · web-arastirma W-2) | ⚠️ VERIFICATION REQUIRED | diyagram üretilmeden modelleme "tamamlandı" denmez |

---

## §9 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — §1-§5 (kapsam içi/dışı, sınır matrisi, değişim protokolü) | Vault Steward (subagent) |
| 2026-10-15 | Genişletme (tur-2) — §2a sahip/geri dönüş · §6 ADR izi · §7 devir · §8 açık kalemler | Vault Steward (subagent) |

## § BAĞLANTILAR

- [[threat-model-anayasa]] · [[threat-model-surucu]] · [[threat-model-ornekler]] · [[threat-model-dogrulama]] · [[threat-model-bilesenler]] · [[threat-model-web-arastirma]] · [[index]]
- [[security-riskler]] · [[adr-index]] · [[../index]]

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
