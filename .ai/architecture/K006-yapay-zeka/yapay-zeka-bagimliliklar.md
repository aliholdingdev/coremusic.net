---
title: "K006-yapay-zeka — Bağımlılıklar (K-Space V3)"
type: architecture-dependencies
category: architecture
date: 2026-10-12
status: draft
version: 1.0.0
node_id: K006-yapay-zeka
---

# yapay-zeka-bagimliliklar — K006-yapay-zeka

## §1 Etiket kuralı (önce oku)

Bu dosyadaki **her satır TASARIM İDDİASIdır**; hiçbiri `verified» değildir.
Kanıt = gerçek dosya yolu / URL / test kaydı. Kanıt gelince `yapay-zeka-dogrulama.md» §6'da
ölçüt kapanır ve satır `verified» etiketine döner. Uydurma bağımlılık yazılmaz.

## §2 depends_on (bu domain neye muhtaç)

| # | Hedef düğüm | Ne alınır | Gerekçe | Kanıt durumu |
|---|---|---|---|---|
| 1 | K007-data | öneri girdisi: etkileşim geçmişi, katalog kayıtları | model verisiz çalışmaz | TASARIM İDDİASI — kanıt yok |
| 2 | K008-domain | "öneri", "beğeni", "çalma listesi" kuralının tanımı | kural domain'de, model onu uygular | TASARIM İDDİASI — kanıt yok |
| 3 | K012-services | inference'ın çalışacağı süreç/servis | model tek başına çalışmaz | TASARIM İDDİASI — kanıt yok |

> v2 §2.6 bu bağımlılıkları **vermez**; listedeki üç satır yapısal mantıktan türetildi (ad + rol cümlesi).

## §3 used_by (bu domain kimi besliyor)

| # | Kaynak düğüm | Ne alır | Kanıt durumu |
|---|---|---|---|
| 1 | K011-application | öneri kartı/sıralama sonucu (UI) | TASARIM İDDİASI |
| 2 | K016-AI | ajanların ML yeteneği (tek çağrı noktası) | TASARIM İDDİASI |
| 3 | K019-testing | model kalitesi için regresyon ölçütü | TASARIM İDDİASI |
| 4 | K020-recomenataion | değerlendirme/ölçüm çıktıları | ⚠️ SINIR AÇIK — VERIFY REQUIRED |

## §4 Entegrasyon sınırları

| Sınır | Paylaşılır | Paylaşılmaz |
|---|---|---|
| K006 ↔ K016-AI | tek ML çağrı noktası (interfaceler) | ajan çalışma zamanı, prompt yönetimi (K016'nın) |
| K006 ↔ K007-data | veriye **erişim sözleşmesi** | şema sahipliği, migration, yedek (K007'nin) |
| K006 ↔ K008-domain | kural tanımına **okuma** | kuralı değiştirme yetkisi (domain'in) |
| K006 ↔ K012-services | inference süreci (servis) | model ağırlığı/metriği saklama biçimi → UNKNOWN |
| K006 ↔ K020-recomenataion | ne olduğu henüz belli değil | tahmin yasak → ⚠️ VERIFY REQUIRED |

### §4.1 Entegrasyon kuralı (özet)

1. K006 **okur** (veri, kural, süreç) — **sahip olmaz**; sahiplik ilgili düğümde kalır.
2. Sınırı aşan tek istisna: ML çağrı noktası (K006 ↔ K016) — bu da ADR ile kilitlenecek.
3. Sınır aşıldığında: kayıt → `.ai/architecture/index.md» §6'ya işlenir, tahmin edilmez.

## §5 Riskli bağımlılıklar

| # | Risk | Neden | Etki | Öneri | Durum |
|---|---|---|---|---|---|
| 1 | `ai-master.md» adı K016-AI ile aynı | registry §5 satır 6 ve 16 | karışan SSOT, yanlış dosyaya yazma | ADR ile ayrıştırma onayı | ⚠️ VERIFY REQUIRED |
| 2 | recommendation üçlü örtüşme | K006 · K008 · K020-recomenataion | sahiplik kavgası, çift kod | sahiplik ADR'si | ⚠️ VERIFY REQUIRED |
| 3 | model girdi şeması K007 ile değişir | şema evrimi | öneri sessiz bozulur | şema sözleşmesi + test (K019) | TASARIM ÖNERİSİ |
| 4 | local-inference donanım bağımlılığı | K001/K004 ile bilinmeyen sınır | bazı cihazlarda çalışamama | gereksinim şeridi ADR'de | ⚠️ VERIFY REQUIRED |

## §6 Doğrulama durumu (ne zaman `verified»)

| Bağımlılık | Ölçüt | Şu an |
|---|---|---|
| K007-data → | veri erişim sözleşmesi dosyada + test kaydı | draft (kanıt yok) |
| K008-domain → | kural tanımı referansı + review kaydı | draft (kanıt yok) |
| K012-services → | süreç sözleşmesi (service-contract) kaydı | draft (kanıt yok) |
| used_by 4 satırı | ilgili domain index'lerinde karşılıklı kayıt | draft (karşılıklı kayıt yok) |

## §7 Kaynaklar

| Kaynak | Ne verdi |
|---|---|
| v2 §2.6 (satır 251–268) | yalnız rol + çocuk listesi — bağımlılık VERMEZ |
| .ai/architecture/index.md §5–§6 | master adı · K006↔K016 ayrımı |
| ADR-097 §5 | etiket ve isimlendirme kuralı |
| v2 §4.2 | `depends_on» alanının tanımı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
