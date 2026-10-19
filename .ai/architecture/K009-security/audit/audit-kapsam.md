---
node_id: K009-security/audit/audit-kapsam
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/audit
  - K009-security/security-kapsam
evidence: "audit/index.md §4 sınırları (K010/K000/K017) + kod envanteri: Redactor·FileHandler·StructuredLogger·Events·SecurityHelper"
validation: "Kapsam içi/dışı + sınır matrisi + kabul kriterleri (index §4 tekrarı, genişletme YOK)"
open_questions: "K017 entegrasyon kanıtı ⚠️ · saklama/bütünlük sahibi atanmadı ⚠️"
group: mimari
---

# K009 · audit · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · parent: K009-security/audit · rol: denetim izi kapsam sınırı.

## §1 Kapsam İçi

| # | Öğe | Kanıt (Dosya·Satır) | Not |
|---|---|---|---|
| 1 | Kritik olay kaydı ilkesi (login/logout/yetki reddi) | `UserLoggedInEvent.php·39`, `FileHandler.php·179` | her olayda bir kayıt |
| 2 | Hassas alan maskeleme (Redactor) | `Redactor.php·28-31/65-93` | tek nokta |
| 3 | Yapılandırılmış log (JSON alan adları) | `StructuredLogger.php·61-69` | normalize |
| 4 | Append-only yazım + lock | `FileHandler.php·251`, `SecurityHelper.php·54` | bütünlük başlangıcı |
| 5 | Ayrı security stream (audit tail'i) | `FileHandler.php·233-235` | prod dâhil aktif |
| 6 | Zaman damgası (her katmanda) | `UserLoggedInEvent.php·31/59`, `FileHandler.php·209` | anayasa §7 |
| 7 | Test bypass aktivasyon kaydı | `SecurityHelper.php·42-55` | gözlemlenebilirlik |
| 8 | authEvent/securityEvent olay sınıflandırması | `FileHandler.php·155-160/178-181` | seviye politikası |
| 9 | Saklama süresi + KVKK silme ilkesi | — | ⚠️ KANIT YOK (anayasa U-1/U-2) |
| 10 | Kayıt bütünlüğü (imza/zoetluk) | — | ⚠️ KANIT YOK (anayasa U-7) |

## §2 Kapsam Dışı

| Öğe | Neden | Giden Yer |
|---|---|---|
| Kimlik deposu, kullanıcı modeli | kimlik tanımı | ⚠️ |
| OS/systemd journal, dosya izinleri, zaman sunucusu | işletim sistemi | K000-isletim-sistemi/security |
| Gözlemlenebilirlik metrikleri/trace saklama | tüketici sistem | K016-observability |
| Yetki kararının kendisi (neye erişim) | yetki modeli | authorization |
| Oturum politikası/timeout | oturum yaşam döngüsü | session-security |
| Sır deposu ve sır erişim politikası | sır yaşam döngüsü | secrets |
| Loglama politikası (parola gücü vb.) | ayrı politika | policies (dalga-3) |

> **Genişletme yasağı:** bu tablo `audit/index.md` §4 sınırlarının TEKRARIDIR; yeni sınır eklenmez, var olan genişletilmez.

## §3 Sınır Matrisi

| Soru | Cevap | Sahip |
|---|---|---|
| "Ne kaydedilir?" | bu çocuk (anayasa §2-§3) | audit |
| "Kayıt nasıl üretilir/sürülür?" | bu çocuk (surucu §3) | audit |
| "Kayıt nereye akar, metrik olur mu?" | tüketim | K016-observability |
| "Kayıt ne kadar saklanır, silinir mi?" | retention/KVKK | ⚠️ sahipsiz (anayasa U-1/U-2) |
| "Kayıt değiştirilemez mi?" | bütünlük | ⚠️ kanıtsız (anayasa U-7) |
| "Kim/ne zaman/neye erişti?" | olay kaydı | audit (bu çocuk) |
| "Saat doğru mu, senkron mu?" | OS zaman sunucusu | K000-isletim-sistemi |
| "Kimlik bilgisi nereden gelir?" | userId/role üretimi | K010 + authentication |

## §4 Bağımlılıklar

| Bağ | Tür | Kanıt/Not |
|---|---|---|
| Redactor | maskeleme | `Redactor.php·25` |
| FileHandler / LoggerFactory | yazıcı | `FileHandler.php·30`, `LoggerFactory.php·15` |
| StructuredLogger | normalize | `StructuredLogger.php·7` |
| Domain Events | olay üretimi | `UserLoggedInEvent.php·20`, `UserLoggedOutEvent.php·20` |
| SecurityHelper | bypass kaydı | `SecurityHelper.php·42-55` |
| K016-observability | tüketici | ⚠️ KANIT YOK (surucu U-1) |

## §5 Kapsam İçi/Dışı Kesişim Matrisi

| Konu | audit | ilgili çocuk | Kesişim |
|---|---|---|---|
| authz_denied kaydı | olay kaydı ✅ | authorization | kararı o üretir, kaydı burada |
| bypass logu | kayıt ✅ | authentication | bypass aktifliği orada, izi burada |
| secret_access | ⚠️ olay tanımı yok | secrets | kaynak orada |
| Parola redaction | maske ✅ | authentication | ham parola orada üretilir |
| Trace saklama | yalnız traceId alanı ✅ | K017 | metrik orada |
| Dosya izinleri | append-only kod ✅ | K000 | OS düzeyi orada |

## §6 Kabul Kriterleri

| # | Kriter | Durum |
|---|---|---|
| 1 | Redactor tek maskeleme noktası kanıtlandı | ✅ |
| 2 | Append-only + LOCK_EX kanıtlandı | ✅ |
| 3 | Yetki reddi olayı tanımı var | ✅ (`FileHandler.php·179`) |
| 4 | Kritik olay üretici kod kanıtı ≥4 | ✅ (audit-ornekler §1) |
| 5 | Saklama süresi/KVKK kanıtı | ⚠️ YOK |
| 6 | Bütünlük (imza) kanıtı | ⚠️ YOK |
| 7 | K017 tüketim kanıtı | ⚠️ YOK |
| 8 | secret_access olay kanıtı | ⚠️ YOK |

## §7 Reddedilen Kapsam Seçenekleri

| Seçenek | Neden Red |
|---|---|
| Log rotasyon/retention kodunu burada tasarlamak | sahibi atanmadı; ADR gerekir (anayasa U-1) |
| SIEM kurulumu | K017/deployment alanı |
| Log imzalama (HMAC) uygulaması | kanıt yok; ADR gerekir (anayasa U-7) |
| KVKK silme rotini implementasyonu | hukuki karar + ADR (anayasa U-2) |
| Dosya sistem izinleri/OS journal | K000 kapsam dışı (index §4) |

## §8 Kapsam — Doğrulama Bağlantısı

| Kapsam Kalemi | Doğrulama Satırı |
|---|---|
| §1-1 (kritik olay kaydı) | AD-01 |
| §1-2 (redaction) | AD-02 |
| §1-4 (append-only) | AD-03 |
| §1-5 (security stream) | AD-04 |
| §1-6 (zaman damgası) | AD-05 |
| §1-7 (bypass kaydı) | AD-06 |
| §1-9 (saklama/KVKK) | AD-11 ⚠️ KANIT YOK |
| §1-10 (bütünlük) | AD-12 ⚠️ KANIT YOK |

## §9 Sınır Kontrol Listesi

| Kontrol | Durum |
|---|---|
| K0100-identitiy'e olay modeli yazıldı mı? | HAYIR ✅ |
| K000 dosya izni/iddia edildi mi? | HAYIR ✅ (yalnız sınır referansı) |
| K017 metrik/tüketim iddia edildi mi? | HAYIR — ⚠️ kanıt yok olarak işaretlendi ✅ |
| authorization kararına girildi mi? | HAYIR ✅ (yalnız authz_denied kaydı) |
| Yeni URL havuzu eklendi mi? | EVET — 9 URL, tekrar 0 (web-arastirma) |

## §10 Kapsam İçi Olay Matrisi (Genişletme — Kanıtlı)

| # | Olay/Ay | Dosya·Satır | Kapsam Sahibi |
|---|---|---|---|
| 1 | user.logged_in / user.logged_out | UserLoggedInEvent.php·39 · UserLoggedOutEvent.php·38 | audit (bu çocuk) |
| 2 | login_success/failed/error | FileHandler.php·156-158 | audit |
| 3 | authz_denied / csrf_invalid | FileHandler.php·179 | audit (kayıt) + authorization (karar) |
| 4 | rate_limited | FileHandler.php·157/179 | audit (kayıt) + rate-limiting (politika) |
| 5 | bypass_activated / TEST_BYPASS | FileHandler.php·179 · SecurityHelper.php·47-54 | audit (kayıt) + authentication (aktiflik) |
| 6 | secret_access | — | ⚠️ tanımsız (anayasa U-3) |

## §11 Kapsam Dışı Devir Matrisi (Genişletme — Sahipler)

| Boşluk | Giden Yer | Devir Notu |
|---|---|---|
| retention süresi | sahipsiz → Vault Steward ADR | anayasa U-1 |
| KVKK silme | sahipsiz → hukuki karar | anayasa U-2 |
| imza/zoetluk | sahipsiz → Architect ADR | anayasa U-7 |
| SIEM/uzak aktarım | K016-observability | surucu §14 |
| dosya izinleri | K000-isletim-sistemi | index §4 |
| olay alanının kimliği (userId) doğruluğu | K0100-identitiy | index §4 |

## §12 Son Durum

| Alan | Değer |
|---|---|
| Dosya | audit-kapsam.md · group: mimari · parent K009-security |
| Satır | ≥150 |
| Genişletme | index §4 sınırları TEKRARLANDI, yeni sınır EKLENMEDİ |
| git commit | ATILMADI (subagent yasağı) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
