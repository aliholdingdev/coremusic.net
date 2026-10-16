---
node_id: K011-application
node_type: file
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - usecase-akislari
  - command-query
  - event-akislari
  - dto-ve-mapper
  - odem-servisleri
  - zamanlamali-gorevler
  - web
  - desktop
  - electron
  - local-runtime
  - server-runtime
  - application-services
  - commands
  - queries
  - workflows
  - application-testing
depends_on: [K008-domain, K009-security, K010-identitiy, K012-services, K013-integraion, K014-network, K015-media]
evidence:
  - "v2 §2.11 K011-application/ — çocuk listesi kaynağı (16 çocuk birebir korundu)"
  - ".ai/architecture/index.md §5/11 · §8.1 — ad kaydı + iki-ekseni gruplama kuralı"
  - "shared/src/PageRouter/ 14 dosya / 1575 satır · Events/ 14 dosya + 2 Contracts · Cache/ 6 dosya / 176 satır — kademe-3 kanıtı (2026-10-12)"
validation:
  - "Çocuk adları v2 §2.11 ile birebir karşılaştırıldı (10/10) — 2026-10-12"
  - "Web kanıtları application-dogrulama.md §3'e işlendi — 2026-10-12"
  - "Kök 12 dosya satır aralıkları `Get-Content | .Count` ile ölçüldü — 2026-10-12"
  - "Kademe-2 6 çocuk v1+v2 şemasıyla üretildi (index + 5 içerik dosyası)"
  - "Index 40-100 kapısı: 138 → ≤100 (2026-10-13, §2 tablosu §GRUPLAR'a de-duplike edildi — bilgi kaybı yok)"
  - "Kapanış 2026-10-16: üretim kapısı 20/20 PASS · curl 108/108 HTTP 200 · mojibake onarımı (cp1254 kayıpsız) · envanter regresyonu 9 URL noktalı [.] ile çözüldü — application-master.md §E"
open_questions:
  - "desktop ile electron sınırı (aynı kabuk mu, ayrı paket mi?) → ⚠️ VERIFICATION REQUIRED"
  - "server-runtime'ın barındırma yeri (nerede çalıştığı) → ⚠️ VERIFICATION REQUIRED"
  - "commands/queries ayrımı CQRS olarak mı okunacak? → TASARIM İDDİASI"
  - "application-testing ile K019-testing kapsam sınırı → ⚠️ VERIFICATION REQUIRED"
  - "odem-servisleri çocuğu: repo'da ödeme kodu YOK (0 dosya) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K011-application — Uygulama Katmanı

## §1 — ELI15: bu oda ne işe yarar?

v2 §2.11: *"Uygulamanın kendisi — web sayfası, masaüstü penceresi, Electron çatısı."* Kullanıcının gördüğü ve tıkladığı **her şey** bu odadır: arayüzü çizmek ve "çal, ara, ekle" talebini alıp doğru servise iletmek. İş kuralı burada **yaşamaz** → K008-domain · arka plan → K012-services · dış dünya → K013-integraion → bağlantı → K014-network · medya → K015-media. K011 yalnız A2 bandında kodlar; politika K009'dan gelir.

## §2 — GRUPLAR kuralı

İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1): her dosyanın frontmatter'ında **9. alan `group`** (`mimari` · `kod` · `kanit`) + klasör içi dağılım aşağıdaki **§GRUPLAR** tablosunda. Bu dosyanın ayrı grup tablosu kaldırıldı — bilgi **§GRUPLAR**'da tektir (dedup, 2026-10-13).

## §3 — Çocuk kataloğu

**Kademe-2 (yeni, v1+v2 şeması):** usecase-akislari · command-query · event-akislari · dto-ve-mapper · odem-servisleri · zamanlamali-gorevler — her biri 6 MD (index 40–100 · kapsam · dogrulama · web-arastirma · ornekler · bilesenler). **Kademe-3:** `pagerrouter/` · `event-bus/` · `cache-layer/` — her dosya `{teknoloji}-{rol}.md`, ≥500 satır, yalnız kendi klasöründe. v2 eski 10 çocuk `verify-required`, adları değiştirilmedi/silinmedi.

## §4 — Bağımlılık özeti (tam metin: application-bagimliliklar.md)

| Yön | Düğüm | Ne için |
|---|---|---|
| depends_on → | K008-domain | Entity/VO sözleşmesi (link, kod taşımaz) |
| depends_on → | K009-security | Güvenlik politikası, CSRF/CSP kuralları |
| depends_on → | K010-identitiy | Oturum, rol, cihaz kimliği doğrulaması |
| depends_on → | K012-services | Arka plan/servis çağrıları (adaptör) |
| depends_on → | K013-integraion | Dış API kontratları ve adaptörler |
| depends_on → | K014-network | Taşıma katmanı (HTTP/WebSocket) |
| depends_on → | K015-media | Oynatma ve medya verisi görünümü |
| ← used_by | K017-observability · K019-testing | Uygulama telemetrisi ve test hedefi |

## §5 — Durum & açık sorular

- **Durum:** draft — 20 klasör × 6 MD = 120 MD üretildi (2026-10-16 kapanış). Üretim kapısı 20/20 PASS · curl 108/108 HTTP 200 · mojibake onarımı tamam · envanter regresyonu noktalı [.] ile çözüldü. Ayrıntı: application-master.md §E (kapanış kaydı) · §11–§14.
- **Açık sorular:** 5 madde (desktop↔electron · server-runtime · CQRS · K019 sınırı · ödeme kodu yok) — hiçbiri bu dosyada kapatılmaz, tamamı FM `open_questions` alanında.

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `application-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| mimari | `application-bilesenler.md` | Bileşen planı ve kademe ipuçları | Kademe-1 |
| kanit | `application-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `application-evrensel-dil.md` | domain terim sözlüğü (TR + EN) | Kademe-1 |
| mimari | `application-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-1 |
| mimari | `application-kararlar.md` | Mimari kararlar, alternatifler, ADR bağlantıları | Kademe-1 |
| kod | `application-kod-ornekleri.md` | Gerçek repo kodu, dosya ve satır | Kademe-1 |
| mimari | `application-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| kanit | `application-riskler.md` | Risk, etki, olasılık ve azaltım planı | Kademe-1 |
| kod | `application-test-stratejisi.md` | domain testleri: unit, property, contract | Kademe-1 |
| kanit | `application-web-arastirma.md` | Sistemli resmi kaynak tarama kaydı | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-16
