---
node_id: model-management
node_type: subdomain
status: draft
parent_id: K006-yapay-zeka
children: []
depends_on: []
evidence:
  - "v2 §2.6 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 259"
validation:
  - "2026-10-12 — v2 mini-ağaç satır 259 ile karşılaştırıldı: ad birebir"
open_questions:
  - "Rol tanımı v2'de yok → ⚠️ VERIFICATION REQUIRED"
  - "Kademe-3 (bounded-context) yapısı tanımlı değil → ADR-097 K4"
  - "depends_on boş: bu kademe için kanıtlanmış bağımlılık yok"
group: mimari
---

# model-management — K006-yapay-zeka (kademe-2)

| Alan | Değer |
|---|---|
| node_id | model-management |
| node_type | subdomain |
| parent_id | K006-yapay-zeka |
| Kaynak | v2 §2.6 (satır 259) |
| Durum | draft |

## Ne işe yarar?

v2 bu alt konuya **yalnızca ad verir** — tanım yok → `⚠️ VERIFICATION REQUIRED».
Ad, kademe-3 tasarımında başlangıç noktasıdır; resmi kapsam ADR-097 K4'te kilitlenir.
Tahminle dolgu **yapılmaz**.

## Bağımlılık

- Üst düğüm künyesi: `../index.md»
- Üst düğüm bağımlılıkları: `../yapay-zeka-bagimliliklar.md»
- Bu düğüm: `children: []» (v2 daha derin göstermiyor).

## Durum

| Ölçüt | Sonuç |
|---|---|
| v2'de adı geçiyor mu? | ✅ evet (satır 259) |
| Rol tanımı var mı? | ❌ yok → VERIFY REQUIRED |
| Kademe-3 planı | ⏳ ADR-097 K4 |
| status | draft |

## İlişkili dosyalar

- [[model-management-kapsam]]
- [[model-management-dogrulama]]
- [[model-management-ornekler]]

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan group**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Kademe-2 kayıt ve durum künyesi | Kademe-2 |
| mimari | `model-management-bilesenler.md` | Bileşen envanteri ve boşluk sayımı | Kademe-2 |
| mimari | `model-management-kapsam.md` | Alt alan kapsam sınırı ve hariçler | Kademe-2 |
| kod | `model-management-ornekler.md` | Gerçek repo kod örnekleri ve satırlar | Kademe-2 |
| kanit | `model-management-dogrulama.md` | İddia·kaynak·tarih·durum kanıt satırları | Kademe-2 |
| kanit | `model-management-web-arastirma.md` | Resmi kaynak taraması ve çelişki kaydı | Kademe-2 |
