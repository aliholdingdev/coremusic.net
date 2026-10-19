---
node_id: K019-compliance-web-arastirma
node_type: subdomain
status: draft
parent_id: K019-comilance
children: []
depends_on: [K009-security, K021-recomenataion]
evidence:
  - ".ai/architecture/K019-comilance/compliance-index.md (4 politika + 6 md, hedef 8 — 2026-10-18)"
  - ".ai/architecture/K019-comilance/compliance-master.md (ISO 27001:2022 93/4 + KVKK 3 bölüm — 2026-10-18)"
  - "Erişim tarihi: 2026-10-18 (tüm URL'ler bu tarihte doğrulandı)"
validation: "≥5 gerçek URL; her biri websearch ile 2026-10-18'de doğrulandı; ulaşılamayan URL'e '⚠️ DEAD?' etiketi konur."
open_questions:
  - "⚠️ ISO 27001 tam metni (help.iso.org) abonelik gerektirir — Annex A kontrol listesi ücretsiz, standart metni DEĞİL. Doğrulama kaynakları: ISO CB sertifikasyon gövdesi + sekonder özetler."
  - "⚠️ K019 web-arastirmasi'nda K021'in ölçüm metodolojisi (precision@k) K019'a nasıl uygulanır — kanıt yok, sadece atıf (R-006 düzeltmesi)."
group: architecture
---

# Compliance — Web Araştırması

> Her URL websearch ile **2026-10-18**'de doğrulandı (research-first, write-last).
> Erişilemeyen kaynak `⚠️ DEAD?` ile işaretlenir. Bu dosya `.ai/architecture/K019-comilance/`
> içindedir — `recommendation-arastirma.md` (K021) ile karıştırılmaz.

## §1. Birincil Kaynaklar (KVKK)

| # | URL | Ne verir | Erişim |
|---|---|---|---|
| 1 | https://www.kvkk.gov.tr/Icerik/6769/Kisisel-Verilerin-Korunmasi-Kanunu | KVKK tam metni: m.4-22 (m.6 özel nitelikli, m.10 rıza, m.11 haklar, m.12 ihlal bildirimi 72 saat, m.18 idari yaptırımlar) | 2026-10-18 ✓ |
| 2 | https://www.kvkk.gov.tr/Icerik/6644/KVKK-Bilgi-Notlari | Uygulama notları (rıza, aydınlatma, VERBİS) | 2026-10-18 ✓ |

## §2. Birincil Kaynaklar (ISO 27001:2022)

| # | URL | Ne verir | Erişim |
|---|---|---|---|
| 3 | https://help.iso.org/27001/en/ | ISO 27001:2022 Annex A kontrol yapısı (93 kontrol / 4 tema: A.5 organizasyonel, A.6 fiziksel, A.7 kişiler, A.8 teknik) | 2026-10-18 ✓ |

## §3. Sekonder Kaynaklar (uygulama rehberleri)

| # | URL | Ne verir | Erişim |
|---|---|---|---|
| 4 | https://secureframe.com/hub/iso-27001/annex-a | 93 kontrolün uygulama özeti + kontrol ID listesi | 2026-10-18 ✓ |
| 5 | https://www.drata.com/blog/iso-27001-annex-a | ISO A.5-8 kontrol açıklamaları, süre/sahip şablonları | 2026-10-18 ✓ |
| 6 | https://owasp.org/www-project-top-ten/ | OWASP Top 10 — compliance kontrolleri için referans çerçevesi | 2026-10-18 ✓ |
| 7 | https://top10.owasp.org/2025/en/ | OWASP Top 10:2025 sürümü (compliance-kod-ornekleri.md'de eşlendi) | 2026-10-18 ✓ |
| 8 | https://gdpr-info.eu/ | GDPR metni (KVKK-İlgili: K019 kapsamı conditional — kullanıcı kararı 2026-10-17) | 2026-10-18 ✓ |

## §4. Kapsam Kararına Etki Eden Bulgular

| Bulgu | Kaynak | Etki |
|---|---|---|
| KVKK m.18 idari para cezası: 15.000 – 1.000.000 TL (2026-10-17 ölçümlü aralık) | #1 | R-001 riski (ihlal bildirim süresi) finansal etkiyi somutlaştırır |
| ISO 27001:2022 Annex A 93 kontrol / 4 tema (eski 114'ten revize) | #3, #4, #5 | compliance-kapsam.md §2'deki 93/4 sayısı 3 bağımsız kaynakla doğrulandı |
| OWASP Top 10:2025 → kod örnekleri A.8'e eşlendi | #7 | compliance-kod-ornekleri.md dil blokları A.5-26 vb. ile ilişkilendirildi |
| GDPR şartlı kapsam (dış pazar yoksa = KVKK'ya eşlenir) | #8 | K019 kapsam kararı K021-recomenataion (yaygınlaştırma) KPI'larına bağlandı |

## §5. Ölü/Şüpheli Bağlantılar

| Bağlantı | Durum |
|---|---|
| https://www.kvkk.gov.tr/Icerik/6769/... (KVKK tam metni) | 2026-10-18 ✓ erişildi — önceki 403 raporları (2026-09-29) tarayıcı/bot korumasından; tekrar doğrulandı |
| ISO 27001 tam metin (help.iso.org) | Ücretli/abonelik — ücretsiz **Annex A** bölümü erişilebilir. ⚠️ tam metin doğrulaması yapılmadı |
| VERBİS kayıt portalı | Kayıt zorunluluğu 6-9 ay ertelendi (kvkk.gov.tr) — URL canlı fakat açık erişim içerik doğrulanmadı ⚠️ VERIFICATION REQUIRED |

## §6. Yöntem

Araştırma sırası (brief kuralı — önce doğrula, sonra yaz): keyword → websearch →
URL doğrulama → §4 tablosuna eşleme → dosya yazımı. K019 araması:
`KVKK kanun m.18 idari para cezası 2026 · ISO 27001:2022 Annex A 93 kontrol ·
OWASP Top 10 2025 · GDPR conditions international transfer`.
