---
title: "personalization — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/personalization
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# personalization — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`ai-master.md` §1, v2 §2.6 rol cümlesini birebir taşır: **"Şarkı öneren, sesi analiz eden yapay zeka parçaları."**
`ai-master.md` §1 sorumluluk tablosu (satır 37–42) bu adı **"Öneri üretmek"** sorumluluğuna bağlar
(`recommendation · personalization` birlikte) — tablo *tasarım yorumudur*.

Basitçe: aynı öneriyi herkese değil, **bu kullanıcıya** göre yapmak — oturum tercihleri, mood, geçmiş.
Repo'da karşılık **kısmen** var: workflow oturum tercihini okuyup öneriye enjekte eder
(`AIWorkflow` satır 55), ama bu çocuğun **resmi kapsam tanımı v2'de yoktur**.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Kullanıcı bağlamının (user_id · mood · geçmiş) öneri modeline verilmesi | Bağlam/etkileşim verisinin saklanması | K007-data (bagimliliklar §2/1) |
| 2 | Kişiselleştirme sinyallerinin sözleşmesi (alan adları) | "beğeni/tercih" iş kuralının tanımı | K008-domain (bagimliliklar §2/2) |
| 3 | Oturum tercihinin okunması (öneri girdisi olarak) | Oturum güvenliği, CSRF, kimlik | K009-security · K010-identitiy (ai-master §2.1) |
| 4 | Kişisel sıralamanın K006'da üretilmesi | Öneri kartının UI'da gösterimi | K011-application (ai-master §2.1) |
| 5 | Kişiselleştirme sinyali ↔ K016 tek ML çağrı noktası | Ajan çalışma zamanı | K016-AI (ai-master §2.1) |
| 6 | Kişiselleştirmenin öneri akışına entegrasyonu | Öneri motorunun skorlama çekirdeği | recommendation (ai-master §3) |

**Sınır kuralı (v2 §2.6 · registry §6):** bu dal yalnız **K006 = ML ürün tarafını** kapsar; K016-AI ajan
altyapısına **taşınmaz**.

**Yasak:** tablo **kademe-2 taslak sınır önerisidir** — v2 §2.6 bu çocuğa rol vermez; kilit ADR-097 K4'tedir.

### Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | personalization ↔ recommendation (kişisel sıralama kimin?) | **UNKNOWN** — v2 ayrım vermez | ai-master §3 satır 261–262 |
| A2 | Tercih verisi nerede saklanır (session vs K007 tablosu) | ⚠️ VERIFY REQUIRED | bagimliliklar §4/2 |
| A3 | Anonim oturum (session_id) kişiselleştirmesi kapsamda mı? | ⚠️ taslak — repo'da `anonymous` dalı var | `AIWorkflow.php` satır 52 |
| A4 | Kişisel veri/gizlilik sınırı (KVKK benzeri) hangi düğümde? | **UNKNOWN** | ai-master §2.1 (K009/K010) |

## §3 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk adı v2 §2.6 satır 262'de birebir geçer | `.ai/architecture/K006-yapay-zeka/personalization/index.md` (evidence) | ✅ dosya okundu |
| 2 | Rol tanımı yok → `⚠️ VERIFICATION REQUIRED` | `personalization/index.md` (open_questions satır 13) | ✅ dosya okundu |
| 3 | Workflow oturum tercihini okur ve bağlama birleştirir | `shared/src/AI/AIWorkflow.php` satır 55–56 | ✅ dosya okundu |
| 4 | Anonim oturum dalı (`'anonymous'`) mevcut | `shared/src/AI/AIWorkflow.php` satır 52 | ✅ dosya okundu |
| 5 | Engine bağlamı `user_id · session_id · mood · genre · history` olarak okur | `shared/src/AI/AIEngine.php` satır 52–56 | ✅ dosya okundu |
| 6 | Sonuç oturuma 1 saat (3600 sn) yazılır | `shared/src/AI/AIWorkflow.php` satır 78 | ✅ dosya okundu |
| 7 | Kişiselleştirilmiş önerinin gerçek zamanlı etkileşimle güncellendiği resmi servis kanıtı | `personalization-dogrulama.md` satır 7 (URL) | ✅ kayıt |
| 8 | K006'nın kişiselleştirme teknoloji seçimi | `ai-master.md` §4 | **UNKNOWN** |

### Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | personalization ↔ recommendation sahipliği kimde? | ⚠️ VERIFICATION REQUIRED |
| R2 | Tercih verisinin saklandığı yer (session vs K007)? | ⚠️ bagimliliklar §4 — **UNKNOWN** |
| R3 | Anonim oturum kişiselleştirmesi K006'da mı, K010'da mı? | ⚠️ **UNKNOWN** |
| R4 | Kişisel veri saklama/gizlilik sınırı? | ⚠️ ai-master §2.1 — K009/K010 |

**Kapanış:** status: draft kalır. R1–R3 kapanmadan bu dosyada kişiselleştirme **sahipliği veya veri yeri**
iddiası yazılmaz (yapay-zeka-dogrulama §5).

## §4 İlişkili dosyalar

- [[personalization-dogrulama]]
- [[personalization-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
