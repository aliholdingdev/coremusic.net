---
node_id: user
node_type: subdomain
status: draft
parent_id: K008-domain
children: []
depends_on: []
evidence:
  - "v2 §2.8 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 300"
validation:
  - "2026-10-12 — v2 mini-ağaç satır 300 ile karşılaştırıldı: ad birebir"
open_questions:
  - "Rol tanımı v2'de yok → ⚠️ VERIFICATION REQUIRED"
  - "Kademe-3 (bounded-context) yapısı tanımlı değil → ADR-097 K4"
  - "depends_on boş: bu kademe için kanıtlanmış bağımlılık yok"
group: mimari
---

# user — K008-domain (kademe-2)

| Alan | Değer |
|---|---|
| node_id | user |
| node_type | subdomain |
| parent_id | K008-domain |
| Kaynak | v2 §2.8 (satır 300) |
| Durum | draft |

## Ne işe yarar?

v2 bu alt konuya **yalnızca ad verir** — tanım yok → `⚠️ VERIFICATION REQUIRED`.
Ad, kademe-3 tasarımında başlangıç noktasıdır; resmi kapsam ADR-097 K4'te kilitlenir.
Tahminle dolgu **yapılmaz**.

## Bağımlılık

- Üst düğüm künyesi: `../index.md`
- Üst düğüm bağımlılıkları: `../domain-bagimliliklar.md`
- Bu düğüm: `children: []` (v2 daha derin göstermiyor).

## Durum

| Ölçüt | Sonuç |
|---|---|
| v2'de adı geçiyor mu? | ✅ evet (satır 300) |
| Rol tanımı var mı? | ❌ yok → VERIFY REQUIRED |
| Kademe-3 planı | ⏳ ADR-097 K4 |
| status | draft |

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `user-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `user-bilesenler.md` | Kademe-3 bileşen planı — taslak, ADR onayı bekliyor | Kademe-2 |
| kod | `user-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `user-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
| kanit | `user-web-arastirma.md` | Web kanıtı — tekil URL doğrulama tablosu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward

