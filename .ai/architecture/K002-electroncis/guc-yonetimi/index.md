---
node_id: K002-electroncis/guc-yonetimi
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: [guc-yonetimi-anayasa, guc-yonetimi-ornekler, guc-yonetimi-dogrulama]
depends_on: []
evidence:
  - ".ai/electronic/** (60 dosya · 18.242 satır, 2026-10-13 disk ölçümü) — güç konu başlıkları okuma"
  - "Web doğrulama: HEAD HTTP 200 · envanter kesişimi 0 (alt-konu içerik dosyaları §5.1)"
  - "Repo'da elektronik kodu YOK dürüstlük kaydı (K-node kök index)"
validation: "draft — 2026-10-13 yazıldı · 4 dosya · ≥500 ölçümü yazım sonrası"
open_questions:
  - "Q-GY1: Koruma rölesi DC offset (>0.5V) birinciliği bu alt-konuda mı, `devre-ve-analog` mı? → UNKNOWN"
  - "Q-GY2: 8.1 kanal toplam güç bütçesi (rail sayısı × akım) hesaplandı mı? → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# guc-yonetimi — Alt-Konu İndeksi (Kademe-3)

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1 — `electronics` DEĞİL, düzeltilmez).
> K002-electroncis'in güç alt-konusu: dönüştürücü topolojisi, regülatör, gate
> sürücü, açma sırası ve koruma — yani sesin çalıştığı enerji.

## §0 ELI15

Bu alt-konu, elektriğin **sisteme girip doğru voltaja inene kadar yaşadığı her şeyi** kapsar: hangi topolojinin seçildiğini, regülatörün nasıl karar verdiğini, MOSFET'in nasıl sürüldüğünü ve koruma rölesinin ne zaman tetiklendiğini. Basitçe: sinyal yolu `devre-ve-analog`'dadır, dijital saat `dijital-ve-mikroislemci`'dedir; burada **enerjinin kendisi** yaşar — L0 kuralı 10 (DC offset >0.5V → koruma rölesi) bu alt-konunun kapısıdır.

## §1 Amaç

| # | Amaç | Çıktı dosya |
|---|---|---|
| 1 | Tasarım anayasası: topoloji + regülatör + sürücü + koruma eşiği | `guc-yonetimi-anayasa.md` |
| 2 | Örnekler: güç hattı hesabı + açma sırası + kural eşlemesi | `guc-yonetimi-ornekler.md` |
| 3 | Doğrulama: URL HTTP 200 + envanter kesişimi 0 + format denetimi | `guc-yonetimi-dogrulama.md` |

## §2 Kademe / rol

| # | Öğe | Değer |
|---|---|---|
| 1 | Kademe | 3 — subdomain (içerik dosyaları Kademe-4) |
| 2 | parent_id | `K002-electroncis` |
| 3 | node_type | `subdomain` (bu indeks) · `module` (3 içerik) |
| 4 | Durum | `draft` (A1 tur-1) |
| 5 | Grup ağırlığı | `mimari` (anayasa) · `kod` (örnekler) · `kanit` (doğrulama) |
| 6 | Yazım | `electroncis` + `guc-yonetimi` — dokunulmaz |

## §3 Dosya envanteri (3)

| # | Dosya | node_id sonu | group | Hedef satır |
|---|---|---|---|---|
| 1 | `guc-yonetimi-anayasa.md` | `/guc-yonetimi-anayasa` | mimari | ≥500 |
| 2 | `guc-yonetimi-ornekler.md` | `/guc-yonetimi-ornekler` | kod | ≥500 |
| 3 | `guc-yonetimi-dogrulama.md` | `/guc-yonetimi-dogrulama` | kanit | ≥500 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — 9. alan `group` (tirnaksız).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `guc-yonetimi-anayasa.md` | Topoloji + regülatör + sürücü + koruma eşiği | 4 |
| kod | `guc-yonetimi-ornekler.md` | Güç hattı hesap örnekleri + kural eşlemesi | 4 |
| kanit | `guc-yonetimi-dogrulama.md` | URL HTTP 200 + kesişim 0 + format denetimi | 4 |

## §5 Komşu sınırı

| # | Komşu | Sınır konusu | Ayrım kuralı | Etiket |
|---|---|---|---|---|
| 1 | `devre-ve-analog` | Sinyal yolu bileşenleri | Sinyal ≠ güç; enerji yolu burada | taslak |
| 2 | `dijital-ve-mikroislemci` | 3.3V/1.8V mantık raili | Rail üretimi burada, register orada | taslak |
| 3 | `sensor-ve-akteyici` | Aktüatör sürücü gücü | Sürücü entegrasyonu orada, güç topolojisi burada | taslak |
| 4 | `imalat-ve-test` | Güç testi (verim,ripple) | Eşik burada, üretim testi orada | taslak |
| 5 | `K001-donanım` | Güç bileşenvanteri | "VAR" K001, "NASIL" K002 | taslak |
| 6 | L0 kuralı 10 | DC offset >0.5V → koruma rölesi | Bağlayıcı kısıt | bağlayıcı |

## §6 depends_on

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | K001-donanım | sınır (bileşen envanteri) | Q-K2-1 kök açık |
| 2 | L0 kuralı 10 | kısıt (koruma rölesi) | bağlayıcı |
| 3 | `devre-ve-analog/` | kardeş (sinyal yolu) | bu K-node içi |
| 4 | ADR-017 | kısıt (32-bit float veri) | frozen |

## §7 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-GY1 | Koruma rölesi DC offset birinciliği bu alt-konuda mı? | UNKNOWN | Vault Steward |
| Q-GY2 | 8.1 kanal toplam güç bütçesi hesaplandı mı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q-GY3 | Güç topolojisi vault satırları okunmadı mı? | ⚠️ VERIFICATION REQUIRED | 3 ajan |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
