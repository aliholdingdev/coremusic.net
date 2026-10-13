---
title: "personalization — Doğrulama"
type: architecture-verification
node_id: K006-yapay-zeka/personalization
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# personalization — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa ile bulunan **resmi URL'ler** · (b) bu oturumda okunan **gerçek disk yolları**.
> Yasak: URL, sürüm, API uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk adı "personalization" v2 §2.6 satır 262'de birebir geçer | `.ai/architecture/K006-yapay-zeka/personalization/index.md` (evidence) | 2026-10-12 | ✅ dosya okundu |
| 2 | Rol tanımı v2'de yok → `⚠️ VERIFICATION REQUIRED` | `personalization/index.md` (open_questions) | 2026-10-12 | ✅ dosya okundu |
| 3 | Workflow oturum tercihini okuyup bağlama birleştirir | `shared/src/AI/AIWorkflow.php` satır 55–56 | 2026-10-12 | ✅ dosya okundu |
| 4 | Anonim oturum (`'anonymous'`) dalı repo'da mevcut | `shared/src/AI/AIWorkflow.php` satır 52 | 2026-10-12 | ✅ dosya okundu |
| 5 | Öneri bağlamı alanları: user_id · session_id · mood · genre · history | `shared/src/AI/AIEngine.php` satır 52–56 | 2026-10-12 | ✅ dosya okundu |
| 6 | Amazon Personalize gerçek zamanlı etkileşim olayları (PutEvents) ile öneriyi canlı günceller | https://docs.aws.amazon.com/personalize/latest/dg/recording-events.html | 2026-10-12 | ✅ doğrulandı |
| 7 | Amazon Personalize, gerçek zamanlı öneride skorları 0–1 aralığında **göreli** üretir | https://docs.aws.amazon.com/personalize/latest/dg/recommendations.html | 2026-10-12 | ✅ doğrulandı |
| 8 | "Real-time personalization" belirli use case/recipe'lerle sınırlıdır ve otomatik güncelleme 2 saatte birdir | https://docs.aws.amazon.com/personalize/latest/dg/how-new-data-influences-recommendations.html | 2026-10-12 | ✅ doğrulandı |
| 9 | Kişiselleştirme ↔ recommendation ayrımı v2'de tanımlı değil | `.ai/architecture/K006-yapay-zeka/ai-master.md` §3 satır 261–262 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 10 | Tercih verisinin saklandığı yer (session vs K007) | `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` §4/2 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | Anonim oturum kişiselleştirmesinin sahibi (K006 mı K010 mu) | — | 2026-10-12 | **UNKNOWN** |
| 12 | CoreMusic'in Amazon Personalize kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 13 | K006 kişiselleştirme teknoloji/model seçimi | `ai-master.md` §4 | 2026-10-12 | **UNKNOWN** |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 13 |
| İşlenen benzersiz URL | 3 (6 · 7 · 8) |
| Gerçek dosya yolu satırı | 5 (1 · 2 · 3 · 4 · 5) + 2 dosya referansı (9 · 10) |
| ✅ doğrulandı | 8 (1–8) |
| ⚠️ VERIFICATION REQUIRED | 2 (9 · 10) |
| UNKNOWN | 3 (11 · 12 · 13) |

**Geçiş kuralı:** satır 9–10 ADR/sahiplik kararıyla; satır 11–13 teknoloji ve sahiplik kaydıyla
(`yapay-zeka-dogrulama.md` §4) kapanır — kanıt gelene kadar UNKNOWN kalır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
