---
node_id: K019-comilance/compliance-riskler.md
node_type: subdomain
status: draft
parent_id: K019-comilance
children: []
depends_on:
  - K009-security
  - K007-data
  - K017-devops
evidence:
  - "compliance-master.md §7 (Açık sorular — 9 madde) + §10 (Sık yapılan hatalar) — okundu 2026-10-18"
  - "controls/index.md open_questions (SoA · lisans klasörü · denetim tarihi yok) — okundu 2026-10-18"
  - "policies/index.md #6/#7/#8 (KVKK · ISO · GDPR koşullu) — okundu 2026-10-18"
  - "KVKK m.18 idari yaptırım aralığı (m.12 ihlali: 15.000–1.000.000 TL) — kvkk.gov.tr 2026-10-17 doğrulandı"
validation:
  - "2026-10-18 — her risk satırında Olasılık × Etki × Azaltma × Sahip sütunları tam"
  - "2026-10-18 — olasılık/etki değerleri NITEL (düşük/orta/yüksek) — sayısal skor uydurulmadı"
  - "2026-10-18 — her risk gerçek disk/açık-soru kanıtına bağlandı"
open_questions:
  - "KVKK VERBİS kaydı yapılmadıysa yaptırım zaman aşımı ne? — hukuki yorum bu vaultun kapsamı değil (⚠️ VERIFICATION REQUIRED)"
  - "ISO SoA yazılmadan risk tedavi planı onaylanabilir mi? — süreç kararı üst karar (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# compliance-riskler — Uyum Risk Defteri

> Dayanak: `compliance-master.md` §7/§10 · `controls/index.md` open_questions.
> Her satır **Olasılık × Etki × Azaltma × Sahip** taşır. Olasılık/etki **nitel**
> (düşük/orta/yüksek); sayısal risk skoru (OL×ET×O) uydurulmaz — ölçüm yok.

## §1 ELI15

> **Uyum riski şudur: bir kurala uymadığımızı denetim öncesinde fark etmezsek
> ceza, itibar kaybı veya veri sızıntısı yaşarız. Riskler burada önceden yazılır
> ki azaltma planı olsun.**

## §2 Risk defteri

| # | Risk | Olasılık | Etki | Azaltma | Sahip | Kanıt |
|---|---|---|---|---|---|---|
| R1 | Denetim **yapılmadan** "uyumlu" yazılması → kanıtsız iddia (en ağır ihlal) | orta | yüksek | Kayıt kuralları (master §6) — sonuç değeri yalnız `geçti/kaldı/VERIFY REQUIRED`; `verification/` append-only | Vault Steward | master §10 madde 1 |
| R2 | KVKK m.12 veri güvenliği ihlali → 72 saat bildirim yükümlülüğü + m.18 yaptırım (15.000–1.000.000 TL) | orta | yüksek | m.12 teknik+idari tedbir haritası (controls/index) · secret-scan.yml · K009-security denetimi | security-engineer | policies #6 · kvkk.gov.tr 2026-10-17 |
| R3 | ISO SoA yazılmadan "ISO uyumlu" iddiası → denetim bulgusu | yüksek | orta | SoA'ya kadar kontrol satırları `denetlenmedi` · sertifika iddiası yasağı | Vault Steward | controls open_questions |
| R4 | GDPR koşulu (AB veri öznesi) sonradan tespit edilirse geç kapsam genişlemesi | düşük | yüksek | Politika #8 şartlı etkin · AB veri öznesi taraması yapılacak | Vault Steward | policies #8 |
| R5 | Secret sızıntısı (`.env` git'e girerse) | düşük | yüksek | secret-scan.yml (gitleaks) · `.gitignore` · `.env` izin kapısı · ADR-095 §1.2 | devops-engineer | controls/index A03 |
| R6 | Lisans denetimi boşluğu (`K020-licence/` diskte YOK) → uyumsuz lisans tespit edilemez | orta | orta | dependency-manager görevi tanımlı · composer-audit job'ı · açık soru olarak tescilli | dependency-manager | controls open_questions |
| R7 | OWASP kontrolü `fail` kalır ama ADR açılmaz → açık kusur sessizce taşınır | orta | orta | Denetim döngüsü adımı ⑥–⑦ (master §4) — fail → `verify-required` → üst karar | security-engineer | master §4 |
| R8 | Append-only ihlali (eski satır düzeltilip silinir) → denetim geçmişi kaybolur | düşük | yüksek | ADR-042 satır-satır edit kuralı · yeni satır + `⚠️` notu | MO (vault-updater) | master §6 madde 2 |
| R9 | `comilance` yazımının "düzeltilmesi" → 489+ wiki-link ve disk kanıtı bozulur | düşük | orta | v2 §3 KURAL 1 — yazım korunur · immutable dosya adları (brief §Kapsam dışı) | Vault Steward | K019 index.md open_questions |
| R10 | registry §5 madde 21 ile disk (`K020-comilance` vs `K019-comilance`) çelişmesi → yanlış yönlendirme | yüksek | düşük | Disk kanıtı esas kararı (2026-10-17) · ID CONFLICT açık soru · registry güncellemesi üst karar | Vault Steward | master §7 madde 5 |
| R11 | Coverage ≥%80 hedefi ölçülmeden "QA uygun" iddiası | yüksek | orta | `.ai/AGENTS.md` §16 hedefi = vault beyanı · K18-testing'te ölçüm bekleniyor · VERIFY REQUIRED etiketi | qa-engineer | controls/index §Vault içi |
| R12 | OWASP Top 10:2025 sürüm kayması (A03/A10 yeni — SSRF A01'e taşındı) → harita eskir | düşük | orta | 2026-10-17 yeniden doğrulama · her uyum turunda OWASP sayfası yeniden okunur | security-engineer | controls/index notu |

## §3 Risk sınıflandırması (özet)

| Etki | Olasılık | Sayı | Riskler |
|---|---|---|---|
| yüksek | orta/yüksek | 3 | R1 · R2 · R11 |
| yüksek | düşük | 3 | R4 · R5 · R8 |
| orta | yüksek | 1 | R3 |
| orta | orta | 3 | R6 · R7 · R9 |
| orta | düşük | 1 | R12 |
| düşük | yüksek | 1 | R10 |

## §4 Escalasyon eşikleri (ne zaman üst karar)

| Tetikleyici | Seviye | Hedef |
|---|---|---|
| KVKK m.12 ihlali tespiti | L2 → L3 | security-engineer → Vault Steward (72 saat sayacı) |
| OWASP kontrol `fail` + ADR kararı | L2 | security-engineer → mimari ADR |
| ID CONFLICT (registry ↔ disk) | L2 | Vault Steward → kullanıcı onayı |
| GDPR koşulunun etkinleşmesi | L3 | kapsam genişlemesi — kullanıcı kararı |

## §5 Doğrulama

- [x] 12 risk · her satırda Olasılık/Etki/Azaltma/Sahip tam
- [x] Olasılık/etki nitel — sayısal skor uydurulmadı
- [x] Her risk gerçek kanıta (dosya/açık soru/URL) bağlandı
- [x] KVKK yaptırım aralığı doğrulanmış kaynaktan (kvkk.gov.tr 2026-10-17)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
