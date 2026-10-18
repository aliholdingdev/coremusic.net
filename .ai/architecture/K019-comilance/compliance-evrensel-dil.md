---
node_id: K019-compliance-evrensel-dil
node_type: subdomain
status: draft
parent_id: K019-comilance
children: []
depends_on: [K009-security, K021-recomenataion]
evidence:
  - ".ai/architecture/K019-comilance/compliance-master.md (KVKK + ISO + GDPR + KVKK-AI şartnamesi terminolojisi — 2026-10-18)"
  - ".ai/architecture/K019-comilance/compliance-controls.md (21+ kontrol: m.6-18, A.5-8 kontrol ID'leri — 2026-10-18)"
  - "kvkk.gov.tr (erişim 2026-10-18) — KVKK'nın resmi İngilizce karşılıkları"
validation: "≥15 EN/TR terim; her terim K019 kanıt dosyasında geçer; çelişen kullanımlar tek sözlükte normalize edilir."
open_questions:
  - "⚠️ ISO 27001 kontrol başlıklarının resmi Türkçe çevirisi ISO TS 13428 içindedir — satın alma gerektirir; buradaki TR karşılıkları pratik çeviridir (resmi DEĞİL)."
  - "⚠️ 'Veri sorumlusu / Controller' eşlemesi KVKK İngilizce özetinde 'data controller' olarak geçer; 'controller' tek başına ISO'da farklı bağlamda (A.5.23 cloud) kullanılır."
group: architecture
---

# Compliance — Evrensel Dil (EN ↔ TR)

> K019 sözlüğü: KVKK, ISO 27001:2022 ve KVKK-AI terminolojisi. Kural: TR metinde
> Kanun'un resmi terimi kullanılır (ör. *ilgili kişi*, *veri sorumlusu*); EN
> terminolojisi (GDPR/ISO bağlamı) kaynağında aynen kalır.

## §1. Temel Terimler (15+)

| # | English | Türkçe | Bağlam / Not |
|---|---|---|---|
| 1 | Data controller | Veri sorumlusu | KVKK m.3 — kararı veren tüzel kişi (CoreMusic) |
| 2 | Data processor | Veri işleyen | KVKK m.3 — talimatla işleyen (ör. bulut sağlayıcı) |
| 3 | Data subject | İlgili kişi | KVKK m.3 — kimliği belli/elli kişi; GDPR: *data subject* aynı |
| 4 | Personal data | Kişisel veri | KVKK m.3 — tanımlı veya tanımlanabilir gerçek kişiye ait bilgi |
| 5 | Special categories of data | Özel nitelikli kişisel veri | KVKK m.6 (19 bent: sağlık, din, biyometrik…); GDPR: *special category* |
| 6 | Explicit consent | Açık rıza | KVKK m.3/2/A — aydınlatmaya dayanan, belirli, bilgilendirmeye özgü irade |
| 7 | Processing | Kişisel verilerin işlenmesi | KVKK m2 — elde etme, kaydetme, düzenleme, silme… tam faaliyet listesi |
| 8 | Data breach | Kişisel veri ihlali | KVKK m.12 — 72 saat içinde Kurul'a bildirim |
| 9 | Privacy notice | Aydınlatma metni | KVKK m.10 — veri sorumlusunun kimliği + amaç + aktarım bilgisi |
| 10 | Record of processing activities | İşlenen kişisel veri kayıt sistemi | KVKK m.4/1-a — 'VERBİS' tekniği (🔴 bknz §4) |
| 11 | Information security management system (ISMS) | Bilgi güvenliği yönetim sistemi (BGYS) | ISO 27001 çıktı hedefi |
| 12 | Statement of applicability (SoA) | Uygulanabilirlik beyanı | ISO 27001 §6.1.3 d — 93 kontrolün gerekçelendirilmesi |
| 13 | Risk assessment / treatment | Risk değerlendirmesi / riski azaltma | ISO 27001 §6.1.2-6.1.3 |
| 14 | Control objective | Kontrol amacı | ISO Annex A temas amacı (ör. A.5) |
| 15 | Corrective action | Düzeltici faaliyet | ISO 27001 §10.1 — uygunsuzluk → kök neden → aksiyon |
| 16 | Audit trail / logging | Denetim izi / kayıt tutma | ISO A.8.15; KVKK m.6/3 (işleme kaydı) |
| 17 | Legitimate interest | Meşru menfaat | GDPR (KVKK'da **sınırlı** — m.5/2 f yalnız kısmi) ⚠️ KVKK tam emsal DEĞİL |
| 18 | Pseudonymization | Pseudonimleştirme | ISO A.8.10, GDPR m.4/5 — KVKK'da açık terim yok (yan eşdeğer) |
| 19 | Right to erasure | Silme hakkı | KVKK m.11/1-e; GDPR: *right to erasure / right to be forgotten* |
| 20 | Cross-border transfer | Yurt dışına veri aktarımı | KVKK m.9 — şartlar: yeterlilik kararı / uygun güvence / istisna |

## §2. KVKK-AI Şartnamesi Terimleri (K006-ai ile ortak)

| English | Türkçe | Not |
|---|---|---|
| Sensitive personal data (AI model) | Hassas / ilgi alanı verisi | `subjectSystemCodes` + `isSensitive` flag (entity-model.md) |
| Profiling | Profil oluşturma | KVKK m.2/1-b — otomatik değerlendirme; öneri motoru S1-S5 (K021) |
| Automated decision-making | Otomatik karar verme | KVKK m.2/2 — bireysel etki doğuran otomatik karar |
| Explainability (AI) | Açıklanabilirlik | KVKK-AI şartnamesi — model gerekçesi sunulabilir olmalı |

## §3. ISO 27001:2022 Kontrol Temaları (Resmi EN + pratik TR)

| Kod | EN | TR (pratik) |
|---|---|---|
| A.5 | Organizational controls | Organizasyonel kontroller |
| A.6 | Physical controls | Fiziksel kontroller |
| A.7 | People controls | İnsan kaynaklı kontroller |
| A.8 | Technological controls | Teknolojik kontroller |

> ⚠️ Resmi Türkçe çeviriler ISO TS 13428'dedir (satın alınmadı — §1 dipnotu).

## §4. Sözlük Uyarıları (çelişki noktaları)

1. **VERBİS vs kayıt sistemi:** 'VERBİS' sekonder kaynakta *processing activities
   record* ile eşlenir; resmi İngilizce karşılık 'record of processing activities'dir.
2. **Legitimate interest:** KVKK'da GDPR kadar geniş DEĞİL — metinde 'meşru menfaat'
   geçmez (m.5/2-f kısmi istisna). Eşleme yapılırken kapsam belirtilmeli.
3. **Rıza (consent) ≠ aydınlatma (notice):** m.10 aydınlatma zorunluluğu ayrıdır,
   rıza m.3/1-a'dır. İkisi karıştırılmamalı.
4. **Incident (olay) vs breach (ihlal):** ISO A.9.2 olay (incident), KVKK m.12 ihlal
   (breach) — Türkçe: 'olay' vs 'ihlal'.

## §5. Kaynaklar

- KVKK tam metni: https://www.kvkk.gov.tr/Icerik/6769/Kisisel-Verilerin-Korunmasi-Kanunu (erişim 2026-10-18)
- ISO Annex A: https://help.iso.org/27001/en/ (erişim 2026-10-18)
- GDPR: https://gdpr-info.eu/ (erişim 2026-10-18)
- Vault: `compliance-master.md` §1 · `compliance-controls.md` §1-§5
