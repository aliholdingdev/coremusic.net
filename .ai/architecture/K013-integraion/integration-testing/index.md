---
node_id: K013-integraion/integration-testing
node_type: subdomain
status: draft
parent_id: K013-integraion
children:
  - ittest-anayasa.md
  - ittest-sozlesme.md
  - ittest-dogrulama.md
  - ittest-kod-ornekleri.md
  - ittest-web-arastirma.md
depends_on: []
evidence:
  - "v2 §2.13 — çocuk adı kaynağı (birebir)"
  - "ittest-web-arastirma.md — 17 benzersiz URL fetch ile 2026-10-16 doğrulandı; 12 reddedilen kaynak gerekçeli"
  - "auth.coremusic.net/phpunit.xml satır 10-12 — Integration suite kaldırıldı, tests/Integration/ diskte YOK"
  - ".github/workflows/ci.yml satır 77, 80 — CI yalnız --testsuite Unit koşar"
  - ".github/workflows/secret-scan.yml satır 4, 29 — gitleaks kapı; .gitleaks.toml diskte YOK"
  - "assets.coremusic.net/tests/mocks/player-api.mock.js satır 1-184 — tek mock kaynağı (createMockFetch)"
  - "K019-testing/integration/integration-test-ortami.md (525 satır) — sınır sahibi doküman"
validation:
  - "İsim v2 §2.13 ile birebir karşılaştırıldı — 2026-10-12"
  - "5 içerik dosyasının satır sayısı ölçüldü (2026-10-16): 355 / 351 / 350 / 599 / 363 — hepsi ≥350"
  - "Her dosyada §Kaynak Tablosu + §A/Soru + zorunlu footer doğrulandı — 2026-10-16"
  - "Cross-reference: 28 kullanım satırı (kaynak → dosya + bölüm/satır) `ittest-web-arastirma.md` §2.4'te"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Sahte çevre kütüphanesi/sahipliği → ⚠️ VERIFICATION REQUIRED"
  - "Q1 K013 ↔ K019 sınır sahibi — iki doküman da canlı entegrasyon testinden söz ediyor (ittest-anayasa §6)"
  - "Q2 Live-test onay gate'i hangi adrese istek atacak? Test edilen canlı ortam adresi tanımlı değil (ittest-sozlesme §4.4)"
  - "Q3 Pact broker kurulacak mı; kurulmazsa consumer sözleşmesi nerede saklanacak? (ittest-sozlesme §3.4)"
  - "Q4 Swift [X1] / Elixir [X2] kaynakları erişilemedi — iki dil örneği taslak (ittest-kod-ornekleri §6)"
  - "Q5 ci.yml js-coverage/js-e2e continue-on-error: true (satır 167, 183) — entegrasyon kapısı sert mi? (ittest-dogrulama §5.5)"
group: mimari
---


# integration-testing — K013-integraion (kademe-2 · subdomain)

**ELI15:** Entegrasyon testleri: dış sistem yerine sahte (mock) çevre ve canlı doğrulama.

**Sınır (ne DEĞİL):** Ortak test altyapısı ve kapsam sahibi K019-testing.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler integraion-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.

**Seri (5 dosya, 2026-10-16):** `ittest-` öneki — mimari 2 · kod 2 · kanıt 1. Sıra: anayasa → sozlesme → dogrulama → kod-ornekleri → web-arastirma.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `ittest-anayasa.md` | Kavramlar, ELI15 birim≠entegrasyon≠sözleşme, piramit, canlı vs mock, K019 sınırı | Kademe-3 (taslak) |
| mimari | `ittest-sozlesme.md` | Ortam sözleşmesi, stub sözleşmesi, canlı-test onay gate'i | Kademe-3 (taslak) |
| kod | `ittest-dogrulama.md` | Sözleşme doğrulama, mock tutarlılığı, eski kayıt, secret tarama, CI kapısı + rapor biçimi | Kademe-3 (taslak) |
| kod | `ittest-kod-ornekleri.md` | 12 dilde entegrasyon testi örneği (`# Dil: X — Rol:` başlıkları) | Kademe-3 (taslak) |
| kanit | `ittest-web-arastirma.md` | 17 doğrulanmış URL + 12 reddedilen kaynak + iddia/dosya cross-reference | Kademe-3 (taslak) |

**Dağılım:** mimari 3 · kod 2 · kanıt 1 = 6 satır (index + 5 çocuk).

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Sahte çevre kütüphanesi/sahipliği → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Q1 K013 ↔ K019 sınır sahibi | AÇIK — `ittest-anayasa.md` §6 |
| 5 | Q2 Live-test gate hedef adresi (canlı ortam tanımsız) | AÇIK — `ittest-sozlesme.md` §4.4 |
| 6 | Q3 Pact broker kararı | AÇIK — `ittest-sozlesme.md` §3.4 |
| 7 | Q4 Swift [X1] / Elixir [X2] dil kaynakları doğrulanamadı | AÇIK — `ittest-kod-ornekleri.md` §6 |
| 8 | Q5 CI kapısı sertliği (continue-on-error: true, satır 167/183) | AÇIK — `ittest-dogrulama.md` §5.5 |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
