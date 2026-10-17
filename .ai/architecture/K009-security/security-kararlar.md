---
node_id: K009-security/security-kararlar
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-master
  - K009-security/security-web-arastirma
evidence: "K-01..K-12 kararları + ADR referansları + kod kanıtları"
validation: "Her karar: gerekçe, alternatif, kanıt, durum"
open_questions: "K-04 anahtar rotasyonu; K-09 bypass whitelist gözden geçirmesi"
group: mimari
---

# K009 · Güvenlik · Kararlar (Kök)

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Rol: K009 mimari güvenlik kararları (ADR niteliğinde özet).
> Karar kuralı: gerekçe + alternatif + kanıt zorunlu; kanıtsız karar VERIFICATION REQUIRED.

## §1 KARAR LİSTESİ

| # | Karar | Gerekçe | Kanıt | Durum |
|---|---|---|---|---|
| K-01 | Parola hash = Argon2id (pepper HMAC-SHA256 ön işleme) | OWASP Password Storage CS; GPU direnci | Password.php·38-49 | ✅ UYGULANMIŞ |
| K-02 | CSRF: double-submit token + hash_equals, bypass listesi boş | OWASP CSRF CS; A01 riski | CsrfMiddleware.php·12-50 | ✅ UYGULANMIŞ |
| K-03 | Oturum: idle 1800 sn + lifetime + session kimliği ID rotation | OWASP Session Management CS | SessionLifecycle.php·17/60 · SessionInitializer.php·40-46 | ✅ UYGULANMIŞ |
| K-04 | JWT: RS256 asimetrik; private.pem yalnız imzalama; jti revocation | 800-57pt1r5; imza gizliliği | JwtService.php·15-30 | ✅ UYGULANMIŞ (key-rotation ⚠️) |
| K-05 | Rate limit: auth uçları fail-closed, genel uçlar fail-open | ADR-013 §3/§5.4 | RateLimiterMiddleware.php·16-30 | ✅ UYGULANMIŞ |
| K-06 | Güvenlik başlıkları merkezi middleware | A05 misconfig tekelleşmesi | SecurityHeadersMiddleware.php·16-40 | ✅ UYGULANMIŞ |
| K-07 | Return URL allowlist (aynı authority + kontrol karakteri guard) | Open redirect (A01 yüzeyi) | ReturnUrlPolicy.php·5-45 | ✅ UYGULANMIŞ |
| K-08 | API anahtarı: SHA-256 hash + prefix; ham anahtar asla saklanmaz | A08 (secret sızıntısı) | ApiKeyRepository.php·12-30/171/304 | ✅ UYGULANMIŞ |
| K-09 | CSRF bypass whitelist kaldırıldı | A01 riski taşıyordu (kod yorumu) | CsrfMiddleware.php·12-17 | ✅ UYGULANMIŞ |
| K-10 | Kimlik modeli K0100-identitiy'e devredildi | Katman sorumluluğu | K009 kapsam §KAPSAM-DIŞI | ✅ SINIR |
| K-11 | ORM yasak; parametreli PDO (ADR-002) | Injection (A03) | ApiKeyRepository başlık kanıtı | ✅ BAĞLI ADR |
| K-12 | Sıfır-uçurma: doğrulanmayan iddia UNKNOWN/⚠️ | Vault politikası | bu dosya §3 | ✅ SÜREKLİ |

## §2 ALTERNATİF ANALİZİ

| # | Reddedilen Alternatif | Neden Red |
|---|---|---|
| K-01 | bcrypt (1024 round) | OWASP: Argon2id birincil; maliyet yükseltme esnekliği yok |
| K-01 | MD5/SHA-1 + salt | Yasak (AGENTS.md §5 Security); GPU kırılabilir |
| K-02 | Yalnız SameSite=Lax | GET üstünden state-changing uçlar ve alt-domain senaryoları; savunma derinliği |
| K-02 | Her istekte session doğrulama (custom header) | SPA + API karışık; double-submit yeterli + origin check |
| K-03 | Yalnız absolute timeout | Idle oturum hırsızlığı (fixation sonrası) için idle şart |
| K-04 | HS256 (simetrik secret) | Secret iki tarafta; sızıntısında imza sahteciliği — RS256 |
| K-04 | JWKS uzaktan çekme (öneri) | Altyapı kanıtı yok → ⚠️ VERIFICATION REQUIRED (D-28) |
| K-05 | Her istekte fail-closed | Hizmet sürekliliği (ADR-013 §3) — genel uçlar fail-open |
| K-07 | Referer header'a güvenmek | Referrer-Policy kısıtlı/gizli olabilir; allowlist sağlam |
| K-08 | Ham anahtar DB'de | Sızıntısında tüm anahtarlar kompromize |

## §3 ADR BAĞLANTILARI

| ADR | Konu | K009 Etkisi | Durum |
|---|---|---|---|
| ADR-002 | PDO/ORM yasak | Injection savunması (K-11) | accepted |
| ADR-010 | CSRF stratejisi | K-02/K-09 | accepted (.ai/.decisions/accepted) |
| ADR-013 | Rate limit politikası | K-05 | accepted (kodda referanslı) |
| ADR-020 | API key şema | K-08 | accepted (kodda referanslı) |
| ADR-043 | Middleware sırası | Permission sırası (kodda referanslı) | accepted (kod yorumu) |
| ADR-097 | K-space v3 + gruplama | Bu dosyanın şeması | draft (.decisions/draft) |
| ADR-097 §5.5 | mimari/kod/kanıt grupları | group alanı eşlemesi | uygulandı |
| FOSSA-005/006/011 | Kripto envanter boşlukları | encryption dosyası (dalga-3 bu oturum) | açık |

## §4 KARAR — DOĞRULAMA BAĞLANTISI

| Karar | Doğrulama Satırı | Durum |
|---|---|---|
| K-01 | D-01, D-02 | ✅ |
| K-02 | D-03, D-04 | ✅ |
| K-03 | D-06, D-09 | ✅ |
| K-04 | D-16 | ✅ (key rotation → D-28 ⚠️) |
| K-05 | D-12, D-13 | ✅ |
| K-06 | D-05 | ⚠️ HSTS blok doğrulaması açık |
| K-07 | D-21 | ✅ |
| K-08 | D-10, D-11 | ✅ |
| K-09 | — (kod yorumu kanıtı) | ✅ |
| K-10 | kapsam §KAPSAM-DIŞI | ✅ |
| K-11 | ApiKeyRepository başlık | ✅ |
| K-12 | web-arastirma §11 | ✅ |

## §5 AÇIK KARARLAR (ÜST KARAR İSTEYECEK)

| # | Açık Soru | Seçenekler | Beklenen |
|---|---|---|---|
| O-01 | HSTS preload kaydı | preload başvurusu vs yalnız header | üst karar (deployment) |
| O-02 | localhost allowlist (K-08/ReturnUrlPolicy) | üretimde kaldır vs koru | üst karar |
| O-03 | AES-256-GCM nerede kullanılıyor? | kanıt oku vs envantere ekleme | kod okuma |
| O-04 | MFA kapsamı | K010 mu K009 mu | K0100-identitiy ile ortak karar |
| O-05 | ASVS seviye hedefi | L1 vs L2 | üst karar |

| Son Durum | |
|---|---|
| Dosya | security-kararlar.md · group: mimari |
| Karar | 12 uygulanmış/sınırlı · 5 açık (§5) |
| Bağ | master + web-arastirma + ADR dizini |

## §6 KARAR KAYIT ŞABLONU (ZORUNLU)

| Alan | Zorunluluk | Açıklama |
|---|---|---|
| Karar (ne) | Zorunlu | Tek cümle, emir kipi |
| Gerekçe (neden) | Zorunlu | Risk/standart bağlantısı |
| Alternatif (neyi redd) | Zorunlu | En az 1 reddedilen seçenek (§2) |
| Kanıt (nerede) | Zorunlu | Dosya·Satır veya URL |
| Durum | Zorunlu | UYGULANMIŞ / AÇIK / VERIFICATION REQUIRED |
| İptal koşulu | Öneri | Hangi şartta karar yeniden açılır |

## §7 KARAR YAŞAM DÖNGÜSÜ

| Aşım | Tetikleyici | İşlem |
|---|---|---|
| Öneri | Yeni bileşen/tehdit/ADR | §1 tablosuna K-xx satırı |
| İnceleme | Kanıt toplama | §4 doğrulama eşlemesi |
| Kabul | Uygulama + test | Durum → UYGULANMIŞ |
| Askıya alma | Kanıt eksik | Durum → VERIFICATION REQUIRED (kod YAZMAZ) |
| Kapatma | İptal koşulu gerçekleşti | §8 çakışma kaydı ile |

## §8 OTORİTE VE ÇAKIŞMA

| Kaynak | Otorite | K009 Karar Üstü mü? |
|---|---|---|
| .ai/CLAUDE.md §2.1 | Vault otorite sırası | EVET (üst) |
| ADR accepted (ADR-010/013/020 vb.) | Karar kaydı | EVET (frozen) |
| ADR-097 draft | Şema kaynağı | Kısmi (draft) |
| v2 spec §2.9 | Çocuk listesi | EVET (ID/kapsam) |
| ADR-097 §5.5 | Gruplama | EVET (group alanı) |
| Bu dosya (security-kararlar) | K009 iç karar özeti | Hayır (alt) |

| Çakışma Kuralı | |
|---|---|
| Üst belge ile çelişki | Üst belge kazanır; bu dosyada düzeltme satırı açılır |
| Kod ile çelişki | Kod gerçektir; karar durumu revize edilir (ör. K-04 HS256 iddiası → RS256 düzeltmesi 2026-10-12) |
| Çocuk dosya ile | Kök karar önce; çocuk yalnız derinleştirir |

## §9 KARAR — ÇOCUK DOSYA DAĞILIMI

| Karar | Giden Çocuk Dosyası |
|---|---|
| K-01 | encryption + authentication (karar/kapsam) |
| K-02, K-09 | session-security |
| K-03 | session-security |
| K-04 | encryption |
| K-05 | rate-limiting |
| K-06 | encryption (HSTS/CSP) |
| K-07 | session-security |
| K-08 | secrets |
| K-10 | index + kapsam (sınır) |
| K-11 | (dalga-3 input-validation) |
| K-12 | tüm web-arastirma dosyaları |

## §10 DEĞİŞİKLİK GÜNLÜĞÜ

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-12 | K-04 HS256 → RS256 düzeltmesi | JwtService.php diskten okundu (ALGORITHM=RS256) |
| 2026-10-12 | D-05 Permissions-Policy "okunmadı" → "mevcut" | SecurityHeadersMiddleware dizisi okundu |
| 2026-10-12 | K-09 eklendi | CsrfMiddleware kod yorumu (A01 riski) |
| 2026-10-12 | Şema ADR-097 §5.5'e göre yazıldı | 12 kök + 6 çocuk × 6 |

| Son Durum (karar) | |
|---|---|
| Uygulanmış | 10 (K-01..K-03, K-05..K-11 arası) |
| Açık | 5 (§5) |
| Düzeltmeli | 1 (K-04, günlüklendi) |
