---
node_id: K020-discovery-test-stratejisi
node_type: subdomain
status: draft
parent_id: K020-discovery
children: []
depends_on: [K007-data, K011-application, K18-testing]
evidence:
  - ".ai/architecture/K020-discovery/discovery-dogrulama.md §4 (8 yazım-sonu kontrol kapısı — 2026-10-18)"
  - ".ai/architecture/K020-discovery/discovery-web-arastirma.md §6 (12 servis / 18 URL / 0 hata turu — 2026-10-17)"
  - ".ai/architecture/K18-testing/testing-master.md (mevcut test altyapısı — 2026-10-18)"
validation: "Piramit katmanları K019 değil K020'ye ait (kanıt/kapı testleri); her senaryo ölçülebilir kabul kriteri taşır."
open_questions:
  - "⚠️ Otomatik kapı-betiği (frontmatter/ölçüm/0-isabet) diskte YOK — kapılar manuel"
  - "⚠️ K020 testleri K18-testing kapsamına girer mi? (kapsam beyanı doğrulanmadı)"
group: mimari
---

# Discovery — Test Stratejisi

> K020-discovery bir **doküman/kanıt disiplinidir** — buradaki "test" = iddiaların
> disk/web kanıtıyla çürütülmesi (falsifikasyon). Piramit: statik kapılar →
> kanıt turları → saha (web) doğrulaması → üst onay.

## §1. Test Piramidi (K020 uyarlaması)

```
        [ ÜST ONAY / KAPANIŞ ]        ← registry §9.x (yalnız orkestratör)
       [ SAHA DOĞRULAMASI (web) ]     ← URL turları: 18 URL / 0 hata
      [ KANIT TURLARI (falsifikasyon) ]← grep/Test-Path: iddia ↔ disk
   [ STATİK KAPILAR (yazım-sonu) ]    ← 8 kapı (dogrulama §4)
```

> Not: K18'in birim/entegrasyon piramidi burada **geçerli değildir**; K020'de
> çalıştırılacak yazılım yok (kod sınırı — master §1.2/5).

## §2. Statik kapılar (dosya başına — dogrulama §4)

| # | Kapı | Kontrol (otomatikleştirilebilir) | Kırılırsa |
|---|---|---|---|
| 1 | Frontmatter | 9 alan + `group` (mimari/kod/kanit) + Authority footer | dosya geçersiz |
| 2 | Ölçüm | `ReadAllLines().Length` — `Measure-Object -Line` yasak | sayı uydurma riski |
| 3 | Tahmin yasağı | tarih/URL/API uydurulmadı; doğrulanamayan `⚠️` | ZERO-HALLUCINATION ihlali |
| 4 | Secret yasağı | token/anahtar `.md`'de yok | ADR-095 §1.2 ihlali |
| 5 | Sıra yasağı | `APPROVAL → IMPLEMENTATION` ters değil | AGENTS.md §1 ihlali |
| 6 | Kod sınırı | entegrasyon kodu yok; YTM kapalı | ADR-097 §1/6 ihlali |
| 7 | Sahiplik | S-1/K008 C2/ID CONFLICT `⚠️` taşındı, çözülmedi gibi sunulmadı | kanıt sahteciliği |
| 8 | Dil | Türkçe prose, mojibake yok, `// ...` dolgu yok | kalite ihlali |

## §3. Test senaryoları (kanıt turları)

| ID | Senaryo | Tip | Beklenen |
|---|---|---|---|
| DSC-T01 | İddia-disk çaprazı: her `evidence` satırındaki dosya gerçekten var mı? | statik | 100% isabet; olmayan = `⚠️` |
| DSC-T02 | 0-isabet kanıtı: `signal.js`/`manifest.json`/`seed_kit.json` glob'u | statik | 0 isabet → iddia SİLİNİR (geri gelmez) |
| DSC-T03 | `log.md` satır atıfları: atıf edilen satır log.md uzunluğunu aşıyor mu? | statik | log.md 118 satır; >118 atıf = hata |
| DSC-T04 | URL turu: 18 URL HEAD/fetch | saha (web) | 0 hata; erişilemeyen = `⚠️ DEAD?` |
| DSC-T05 | Gate denetimi: YTM/kapalı kapı örneği var mı? | statik | YTM kodu/örneği = 0 |
| DSC-T06 | ADR atıf doğruluğu: ADR-097 §2.2/2.3/2.4 gibi var olmayan bölüm atfı | statik | 0 — yoksa `⚠️` |
| DSC-T07 | Bağımlılık yönü: `recomenataion → discovery` oku ters mü? | statik | yön korunur (D-1) |
| DSC-T08 | Ölü K adı sızıntısı: `K0100-identitiy` vb. `depends_on`'a sızmış mı? | statik | 0 sızıntı (yalnız gerçek K adları) |

## §4. Kabuk (acceptance) kriterleri

1. Her dosyanın **§2 8 kapısından** geçişi (kayıt: bu dosyanın `validation` alanı).
2. **DSC-T01**: `evidence` satırlarının ≥%90'ı diskte doğrulanmış; kalanı `⚠️`.
3. **DSC-T04**: URL turu **0 hata**; erişilemeyen URL `⚠️ DEAD?` ile raporlanmış
   (tur-2 referansı: 12 servis / 18 URL / 0 hata — 2026-10-17).
4. **DSC-T06**: var olmayan ADR numarası/bölümü **0** adet (uydurma ADR yasağı).
5. **DSC-T08**: `depends_on` listesinde ölü K adı **0** (⚠️ işaretli madde hariç —
   onlar metin gövdesindedir, bağımlılık değildir).
6. Kanıtsız iddia yayına girmez: `partial` node `verified` gibi sunulmaz.
7. Kapanış: registry §9.x kaydı **yalnız orkestratör** tarafından yazılır.

## §5. Otomasyon durumu (dürüst envanter)

| Araç | Durum |
|---|---|
| Frontmatter/ölçüm/0-isabet otomasyon betiği (`.ai/scripts/*.ps1` — kökten çalışır) | ⚠️ K020'ye özel betik **YOK** (mevcut gate scriptleri ui-design'a ait) |
| URL turu (fetch) | manuel / bu oturumda yapıldı (2026-10-17, 2026-10-18) |
| grep/Test-Path iddia-disk | manuel (Truth Mode 2026-10-17 turu) |
| K18 CI'ına bağlılık | ⚠️ kapsam beyanı doğrulanmadı — bağımsız sayılır |

## §6. Doğrulanmayan iddialar

| İddia | Durum |
|---|---|
| K020 kapı betikleri CI'da otomatik koşuyor | YOK (betik kanıtı yok) |
| K020 testleri K18 kapsamındadır | ⚠️ VERIFICATION REQUIRED |
| Deezer kota değeri | UNKNOWN (2026-10-17: resmi sayı yayımlanmadı) |

---

**Authority:** Bayram Ali / Vault Steward
