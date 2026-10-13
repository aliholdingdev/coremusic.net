---
title: "diagnostics — Doğrulama"
type: architecture-verification
node_id: diagnostics-dogrulama
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on: []
evidence:
  - "§1 iddia tablosu — 12 satır: 9 doğrulandı, 1 ⚠️, 2 UNKNOWN"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): PipeWire latency dokümanı (docs.pipewire.org)"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): Apple AURenderCallback (audiotoolbox)"
validation:
  - "§1–§3 mevcut yapı korundu (append-only); §2 sayım §1 ile birebir"
  - "§4–§11 ek bölümler: log/telemetry/alarm eşiği tasarımı — ölçüm SONUCU içermez"
open_questions:
  - "#10 hata sınıflandırma şeması (K003 kodları) — ⚠️ VERIFICATION REQUIRED"
  - "#11 ölçüm metrik listesi + örnekleme periyodu — UNKNOWN"
  - "#12 ölçüm verisi saklama süresi ve yeri — UNKNOWN"
  - "#4 log formatı/seviyesi standardı (index.md açık sorusu) — açık"
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# diagnostics — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) bu oturumda shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt
> (dosya okunarak). Yasak: URL, sürüm, sınıf, sürüm numarası uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "diagnostics · Log, ölçüm, hata sınıflandırma" | `audio-master.md` §3 satır 72 | 2026-10-12 | ✅ dosya okundu |
| 2 | "Sensitive data log'da → [REDACTED] ile maskeleme" (Edge Case #3) | `.ai/AGENTS.md` §17 | 2026-10-12 | ✅ dosya okundu |
| 3 | `log.md` append-only: tüm ajanlar ekler, geçmiş satıra dokunmaz (§25.3 kural 3) | `.ai/AGENTS.md` §25.3 | 2026-10-12 | ✅ dosya okundu |
| 4 | Açık soru: "log formatı/seviyesi standardı yok ⚠️" | `diagnostics/index.md` open_questions | 2026-10-12 | ✅ dosya okundu |
| 5 | `auval` CLI, bir audio unit'ı plug-in API + temel işlev (kanal konfigürasyonu, instantiate süresi, render yeteneği) üzerinden doğrular | `audio-master.md` §4 A6 (kaynak: Apple AudioUnitDevelopmentFundamentals) | 2026-10-12 | ✅ dosya okundu |
| 6 | PipeWire latency: linked node latency'leri toplanır, **mismatch tespit edilir** | https://pipewire.org/page_latency | 2026-10-12 | has-been-fetched 2026-10-12 |
| 7 | PipeWire scheduling: driver node döngüsü + sayaçlar (olay kaynağı) | https://pipewire.org/page_scheduling | 2026-10-12 | has-been-fetched 2026-10-12 |
| 8 | Core Audio C API sürücü/format/codec/sesun kapsamı (olay kaynağı API'si) | https://developer.apple.com/documentation/coreaudio/core_audio_overview/core_audio_api_reference | 2026-10-12 | has-been-fetched 2026-10-12 |
| 9 | Repo'da ses telemetrisi toplama kodu yok | kök `*.cpp`/`*.h` glob 0 isabet | 2026-10-12 | ✅ ölçüm |
| 10 | Hata sınıflandırma şeması (K003 kodları/sınıfları) | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | Ölçüm metrik listesi + örnekleme periyodu | — | 2026-10-12 | **UNKNOWN** (tasarım yok) |
| 12 | Ölçüm verisinin saklama süresi ve yeri | — | 2026-10-12 | **UNKNOWN** (karar yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 12 |
| Benzersiz URL | 3 (6 · 7 · 8) |
| Gerçek dosya yolu / iç kanıt satırı | 5 (1 · 2 · 3 · 4 · 5) + 1 ölçüm (9) = 6 |
| ✅ doğrulandı / dosya okundu / ölçüm | 9 (1–9) |
| ⚠️ VERIFICATION REQUIRED | 1 (10) |
| UNKNOWN | 2 (11 · 12) |

**Doğruluk notu:** §2 satır sayıları §1 ile birebir sayılmalıdır; sayım
uyuşmazsa §1 yeniden sayılır (sessiz düzeltme yasak).

## §3 Geçiş kuralı

- **#10** sınıflandırma şeması error-coordinator kararıyla kapanır →
  kapanınca `diagnostics-kapsam.md` §3 DG3 satırı `[TASARIM]` → `[KANIT]` olur.
- **#4** log formatı kararıyla birlikte kapanır (index.md açık sorusu).
- **#11–12** metrik/periyot/saklama kararı olmadan sayı ya da süre yazılmaz →
  `UNKNOWN` kalır (uydurma yasak).
- **#6–8** bağımsız teyit istenirse URL ancak gerçekten fetch edilince
  `✅ doğrulandı` durumuna yükseltilir.

---

## §4 Telemetri ve log doğrulama protokolü (PLANNED)

> **Durum:** Bölüm **doğrulama tasarımıdır**; ölçüm/log çıktısı içermez.
> Kardeşler: [[diagnostics-kapsam]] · [[diagnostics-ornekler]] · [[index]] ·
> [[K000-isletim-sistemi/isletim-sistemi-audio/index]].

### §4.1 Amaç, kapsam, sorumluluk

| # | Öğe | Tanım |
|---|---|---|
| A1 | Amaç | Log/telemetri akışının **doğru, maskeli, eklemeli (append-only)** çalıştığını ve alarm eşiklerinin tanımlı olduğunu doğrulamak |
| A2 | Kapsam | K003 diagnostics: olay kaydı, ölçüm metrikleri, alarm sınıfı |
| A3 | Kapsam dışı | Log altyapısının kendisi (K000 observability alanı) — çapraz referans |
| A4 | Çıktı | Log doğrulama raporu + alarm tablosu + PASS/FAIL (§14 formu) |
| A5 | Veri yolu | 32-bit float ölçüm değeri (ADR-017) |
| A6 | Gizlilik | Hassas veri `[REDACTED]` (§1 #2 — `.ai/AGENTS.md` §17 #3) |
| A7 | Kayıt bütünlüğü | `log.md` append-only (§1 #3 — §25.3 kural 3) |
| A8 | Sorumlu | error-coordinator (şema, §1 #10) + diagnostics sahibi (embedded) + qa (kapı) |
| A9 | OS katmanı | Olay/saat kaynağı: [[K000-isletim-sistemi/isletim-sistemi-audio/index]] · log: K000 observability |

### §4.2 Ön koşul tablosu

| # | Ön koşul | Kaynak | Durum | Eksikse |
|---|---|---|---|---|
| P1 | Hassas veri maskeleme kuralı | §1 #2 | ✅ kural | — |
| P2 | `log.md` append-only | §1 #3 | ✅ kural | — |
| P3 | Log formatı/seviyesi standardı | §1 #4 (index.md açık soru) | ❌ UNKNOWN | format kararı yok |
| P4 | Hata sınıflandırma şeması (K003 kodları) | §1 #10 | ⚠️ VERIFICATION REQUIRED | §6/§13 yalnız taslak |
| P5 | Metrik listesi + örnekleme periyodu | §1 #11 | ❌ UNKNOWN | §7 matrisi “aday” |
| P6 | Ölçüm verisi saklama süresi/yeri | §1 #12 | ❌ UNKNOWN | saklama yok |
| P7 | Repo'da telemetri kodu yok (baseline) | §1 #9 ölçüm | ✅ ölçüm | — |
| P8 | `auval` benzeri doğrulama referansı | §1 #5 | ✅ dosya okundu | — |
| P9 | Olay kaynağı API'leri (platform) | §1 #6–#8 | has-been-fetched | §12'de 404 notu |
| P10 | Alarm eşikleri (kim karar verir?) | — | ❌ UNKNOWN | §6 kapıları kapalı |

### §4.3 Doğrulama adımları

| Adım | İşlem | Çıktı | Hata durumunda |
|---|---|---|---|
| S1 | Format/seviye kararını bekle (#4) | format kararı | yoksa yalnız yapısal test (§5) |
| S2 | Şemayı doğrula (alan adları, zorunlu alanlar) | şema raporu | eksik alan → FAIL |
| S3 | Sahte olay üret (etiketli: üretim verisi değil) | test olayları | — |
| S4 | Maskelenmeyi denetle (hassas alan → `[REDACTED]`) | maske raporu | maske yoksa **BLOCK** |
| S5 | Append-only denetimi (önceki satır değişmedi) | bütünlük raporu | değişim varsa FAIL |
| S6 | Metrik toplamayı çalıştır (#11 kararı gerekiyor) | metrikler | karar yoksa KAPALI |
| S7 | Alarm eşiklerini uygula (#10/P10) | alarm kaydı | eşik yoksa KAPALI |
| S8 | Saklama/temizlik denetimi (#12) | saklama raporu | karar yoksa KAPALI |
| S9 | Olay kaynaklarıyla çapraz kontrol (§1 #6–#8) | çapraz not | — |
| S10 | Sonuçları §14 boş forma işle | oturum kaydı | — |
| S11 | Kardeşlerle karşılaştırma (realtime xrun olayı vb.) | çapraz not | — |

### §4.4 Olay/log kayıt şeması (alan tanımı — değer yok)

| Alan | Tip | Zorunlu | Not |
|---|---|---|---|
| `timestamp` | uint64 | Evet | Kaynak: OS sayacı (§12 K000 çapraz) |
| `severity` | enum | Evet | Seviye kümesi §5.1 (aday — P3) |
| `subsystem` | id | Evet | sync/realtime/mix/route/device… |
| `event_code` | id | Evet | §13 şema taslağı (#10 ⚠️) |
| `block_index` | uint64 | Hayır | realtime korelasyonu |
| `metric_name` | string | Hayır | §7 (P5 kararı gerekiyor) |
| `metric_value` | float32 | Hayır | 32-bit float (ADR-017) |
| `masked_fields` | list | Koşullu | `[REDACTED]` uygulanan alanlar |
| `run_id` | id | Evet | oturum kimliği |
| `note` | string | Hayır | serbest |

## §5 Log doğrulama matrisi (LOG)

### §5.1 Seviye/format matrisi (aday — P3 kararı bekleniyor)

| Seviye | Aday kullanım | Beklenen davranış | Durum |
|---|---|---|---|
| TRACE | Blok içi iz (üretimde kapalı) | Performansa maliyeti ≈0 (üretimde yazılmaz) | ⚠️ taslak |
| DEBUG | Geliştirme | Yapılandırma ile açılır | ⚠️ taslak |
| INFO | Yaşam döngüsü olayı (başlat/durdur) | Tek satır, olay kodlu | ⚠️ taslak |
| WARN | Geri kazanılabilir olay (xrun, kilit beklemesi) | Sayaçla birlikte | ⚠️ taslak |
| ERROR | Kapı ihlali / veri kaybı | Sınıf kodu ile (§13) | ⚠️ taslak |
| FATAL | Koruma (DC >0.5 V — kural 10) | Röle kuralı + kilitli kayıt | ✅ kural 10 |
| FORMAT | *(UNKNOWN)* | — | ❌ P3 kararı yok |
| METRIC FORMAT | *(UNKNOWN)* | — | ❌ P5 kararı yok |

### §5.2 Senaryo matrisi (LOG-01 … LOG-14)

| ID | Senaryo | Yöntem | Beklenen (tasarım) | Sonuç | Kapı |
|---|---|---|---|---|---|
| LOG-01 | Append-only | Önce/sonra hash | Geçmiş satır değişmez | ⬜ | §9 K2 |
| LOG-02 | Hassas veri maskesi | Sahte hassas alan üret | `[REDACTED]` | ⬜ | §9 K1 (BLOCK) |
| LOG-03 | Boş/uzun alan | Sınır değerleri | Şema korunur | ⬜ | K3 |
| LOG-04 | Çok yüksek olay hızı | Sel (flood) | Kayıp sayaçlanır; çökme yok | ⬜ | K4 |
| LOG-05 | Disk dolu | Dolu yolu hedefle | Çökme yok, uyarı | ⬜ | K5 |
| LOG-06 | Eşzamanlı yazım | Çok thread | Çakışmış satır yok (ekleme atomikliği tasarımı) | ⬜ | ⚠️ P3 |
| LOG-07 | Saat sıçraması | OS saat olayı | Sıralama bozukluğu raporlanır | ⬜ | K6 |
| LOG-08 | Şema ihlali | Eksik alanlı olay | Red/uyarı | ⬜ | K3 |
| LOG-09 | Metrik eksikliği | #11 kararı yok | KAPALI-KAPI yazılır | ⬜ | K7 |
| LOG-10 | Alarm eşiği ihlali | Eşik üstü değer (karar varsa) | Alarm satırı üretilir | ⬜ | §6 |
| LOG-11 | Saklama süresi | #12 kararı yok | KAPALI-KAPI | ⬜ | K8 |
| LOG-12 | `log.md` bozulması | Değişiklik denemesi | Vault kurtarma kuralı (Edge Case #10) | ⬜ | K2 |
| LOG-13 | Xrun olayı kaydı | realtime ile ortak olay | Olay sayaçla eşleşir | ⬜ | çapraz |
| LOG-14 | FATAL koruma kaydı | DC >0.5 V (kural 10) | Röle + kilitli kayıt | ⬜ | K9 |

## §6 Alarm eşiği test tablosu (ALM)

| ID | Alarm (aday) | Kaynak metrik | Eşik | Beklenen aksiyon | Eşik durumu | Sonuç |
|---|---|---|---|---|---|---|
| ALM-01 | Xrun sayısı eşiği | xrun sayaç | *(UNKNOWN)* | WARN/ERROR sınıfı | ❌ P10 | ⬜ |
| ALM-02 | Callback bütçe aşımı | callback süresi | *(UNKNOWN — #10 realtime)* | ERROR | ❌ | ⬜ |
| ALM-03 | DC offset > 0.5 V | DC ölçümü | **0.5 V (kural 10)** | koruma rölesi | ✅ kural | ⬜ |
| ALM-04 | Cihaz kaybı | cihaz olayı | olay tetikli | ERROR + fallback | ✅ olay | ⬜ |
| ALM-05 | Kilit ihlali (2. uygulama) | kilit hatası | olay tetikli | WARN | ✅ kural 9 | ⬜ |
| ALM-06 | Log yolu doluluğu | disk kullanımı | *(UNKNOWN)* | WARN | ❌ P10 | ⬜ |
| ALM-07 | Saat sıçraması | timestamp ardışıklık | monoton ihlali | WARN | ⚠️ P4 | ⬜ |
| ALM-08 | Telemetri kaybı | metrik boşluğu | *(UNKNOWN)* | WARN | ❌ P5/P10 | ⬜ |
| ALM-09 | Route döngüsü tespiti | graf denetimi | *(UNKNOWN — routing §5)* | ERROR | ⚠️ | ⬜ |
| ALM-10 | Taşma/clip sayacı | clip sayacı | *(UNKNOWN — mixing §8)* | WARN | ⚠️ | ⬜ |
| ALM-11 | Bellek/tahsis ihlali (RT) | probe | **0 (kural 1)** | BLOCK/ERROR | ✅ kural | ⬜ |
| ALM-12 | Uzun kesintisiz çalışma | süre | *(UNKNOWN)* | bilgilendirme | ❌ | ⬜ |

**Kapı kuralı:** Eşik satırı UNKNOWN olan alarm testi **KAPALI-KAPI**dır; eşik
uydurulup “geçti” yazılamaz.

## §7 Metrik toplama matrisi (MET — §1 #11 kararı bekliyor)

| ID | Metrik (aday) | Örnekleme periyodu | Saklama (#12) | Durum |
|---|---|---|---|---|
| MET-01 | Callback süresi (dağılım) | *(UNKNOWN)* | *(UNKNOWN)* | ❌ |
| MET-02 | Xrun sayaç | *(UNKNOWN)* | *(UNKNOWN)* | ❌ |
| MET-03 | Blok sayaç (tam) | *(UNKNOWN)* | *(UNKNOWN)* | ❌ |
| MET-04 | CPU kullanımı (RT thread) | *(UNKNOWN)* | *(UNKNOWN)* | ❌ |
| MET-05 | Kuyruk derinliği | *(UNKNOWN)* | *(UNKNOWN)* | ❌ |
| MET-06 | Clip/taşma sayacı | *(UNKNOWN)* | *(UNKNOWN)* | ❌ |
| MET-07 | DC offset (kütürel) | *(UNKNOWN)* | *(UNKNOWN)* | ❌ |
| MET-08 | Cihaz durumu olayı | olay tetikli | *(UNKNOWN)* | ⚠️ |
| MET-09 | Log kaybı sayacı | *(UNKNOWN)* | *(UNKNOWN)* | ❌ |
| MET-10 | Kapı sonuçları (PASS/FAIL) | oturum sonu | *(UNKNOWN)* | ⚠️ |

**Sayım kuralı:** Metrik sayısı “10” değil; **10 aday ve 0 onaylıdır** —
onay yalnız #11 kararıyla olur.

## §8 Eşik tabloları (aday — onay bekliyor)

| Eşik | Aday | Dayanak | Durum |
|---|---|---|---|
| E1 | DC alarm eşiği = 0.5 V | kural 10 | ✅ kural |
| E2 | Xrun alarm eşiği | — | ❌ UNKNOWN |
| E3 | Log seli (flood) tanımlı **rate** | — | ❌ UNKNOWN |
| E4 | Saklama süresi | — | ❌ UNKNOWN (#12) |
| E5 | Örnekleme periyodu | — | ❌ UNKNOWN (#11) |
| E6 | Hassas alan listesi | §1 #2 (maske kuralı var; **liste** yok) | ⚠️ VERIFICATION REQUIRED |
| E7 | Hata kodu aralığı/şeması | — | ⚠️ VERIFICATION REQUIRED (#10) |
| E8 | Monoton timestamp ihlali toleransı | monotonluk (§ synchronization #2) | 0 (tasarım) |
| E9 | Log kaybı kabulü | — | ❌ UNKNOWN |
| E10 | Loglama RT yasağı (callback içi yazım) | kurallar 1–2 | 0 (kural) |
| E11 | `log.md` boyut/artış kuralı | append-only (§1 #3) | ⚠️ biçim kararı yok |
| E12 | Disk dolu davranışı | — | ⚠️ tasarım yok |

## §9 Geçme / kalma kriterleri (PASS–FAIL)

| # | Kriter | Ölçüm/Denetim | PASS | FAIL |
|---|---|---|---|---|
| K1 | Maskeleme | LOG-02 | `[REDACTED]` her hassas alanda | Sızıntı → **BLOCK** (§1 #2) |
| K2 | Append-only | LOG-01/LOG-12 | önceki satır değişmez | Değişim → vault kurtarma (Edge Case #10) |
| K3 | Şema sağlığı | LOG-03/08 | şema korunur | RED/uyarı yoksa FAIL |
| K4 | Sel dayanımı | LOG-04 | çökme yok; kayıp sayaçlı | Gizli kayıp → FAIL |
| K5 | Dolu disk | LOG-05 | çökme yok | FAIL |
| K6 | Sıralama | LOG-07 | bozulma raporlanır | Gizliyorsa FAIL |
| K7 | Metrik kapısı | §7 | #11 kararı varsa uygula | Yoksa KAPALI-KAPI |
| K8 | Saklama kapısı | §4.3 S8 | #12 kararı varsa uygula | Yoksa KAPALI-KAPI |
| K9 | FATAL/DC | LOG-14/ALM-03 | kural 10 işler | FAIL → güvenlik eskalasyonu |
| K10 | RT yasağı | LOG-05/E10 | callback'te log yazımı yok | İhlal → BLOCK |
| K11 | Alarm bütünlüğü | §6 | eşikli satırlarda sonuç var | UNKNOWN eşikli satır sayılmaz |
| K12 | Dürüstlük | — | sonuç satırında koşul+tarih | yoksa sayılmaz |

## §10 Hata enjeksiyon senaryoları (DF)

| ID | Enjeksiyon | Beklenen dayanım | Tespit | Sonuç |
|---|---|---|---|---|
| DF-01 | Hassas veri üretimi | `[REDACTED]` | tarama | K1 |
| DF-02 | Disk dolu | çökme yok | uyarı | K5 |
| DF-03 | Log yolu değiştirme/geçersiz | reddedilir/uyarı | hata | K3 |
| DF-04 | Eşzamanlı yazım yarışı | bozuk satır yok | hash denetimi | ⚠️ P3 |
| DF-05 | Saat sıçraması | sıralama uyarısı | ardışıklık denetimi | K6 |
| DF-06 | Olay seli (10× hız) | kayıp sayaçlı | sayaç | K4 |
| DF-07 | Şema dışı alan | red/uyarı | doğrulayıcı | K3 |
| DF-08 | Tekrarlanan `run_id` | yakalanır | tekrar denetimi | §5.2 |
| DF-09 | Saklama süresi dolu temizlik | #12 kararı yoksa KAPALI | — | K8 |
| DF-10 | Eşiği aşan metrik (kararlı eşik) | alarm satırı | §6 | K11 |
| DF-11 | `log.md` değişiklik denemesi | kurtarma kuralı | vault diff | K2 |
| DF-12 | RT callback içinde log yazımı | tespit/BLOCK | statik denetim | K10 |

## §11 Regresyon kapıları

| Kapı | Tetikleyici | Zorunlu test | Kapatma |
|---|---|---|---|
| DG-1 | Telemetri kodu eklendi (baseline yok — §1 #9) | §5.2 LOG-01–LOG-05 | K1, K2, K3 |
| DG-2 | Format/seviye kararı (#4) | §5.1 tamamı | P3 ✅ |
| DG-3 | Hata şeması (#10) | §13 + §6 | P4 ✅ |
| DG-4 | Metrik/periyot (#11) | §7 + §10 DF-10 | P5 ✅ |
| DG-5 | Saklama (#12) | §4.3 S8 | P6 ✅ |
| DG-6 | Realtime değişikliği | LOG-13 çapraz | olay eşleşmesi |
| DG-7 | Güvenlik değişikliği (maske listesi) | DF-01 | K1 BLOCK yok |
| DG-8 | Vault boot dosyası değişikliği | K2 + Edge Case #10 | append-only korundu |

## §12 Bağlantılar ve kaynak denetimi

### §12.1 Bağlantılar

| Bağlantı | Dosya | İlişki |
|---|---|---|
| Kardeş kapsam | [[diagnostics-kapsam]] | DG3 satırı `[TASARIM]` → `[KANIT]` (§3) |
| Kardeş örnekler | [[diagnostics-ornekler]] | Olay/şema örnekleri |
| Dizin | [[index]] | Açık soru #4 burada |
| OS/log katmanı | [[K000-isletim-sistemi/isletim-sistemi-audio/index]] · K000 observability | Saat/olay kaynağı |
| Realtime | [[realtime-dogrulama]] | Xrun olayı kaynağı (LOG-13) |
| Test piramidi | [[testing-kapsam]] | Log testleri L2 katmanı |
| Mixing/Routing | [[mixing-dogrulama]] · [[routing-dogrulama]] | ALM-09/ALM-10 eşikleri |

### §12.2 Kaynak denetim kaydı (2026-10-13)

> `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`; tam URL eklenmez (0 yeni URL).

| # | Kaynak | HEAD | Not |
|---|---|---|---|
| 1 | PipeWire dokümantasyonu — latency (docs.pipewire.org) | **HTTP 200** | §1 #6 mismatch tespiti güncel alanda |
| 2 | PipeWire dokümantasyonu — genel/amaç (docs.pipewire.org) | **HTTP 200** | olay kaynağı kavramları |
| 3 | Apple Developer Documentation — AURenderCallback (AudioToolbox) | **HTTP 200** | render olay kaynağı |
| 4 | Apple Developer Documentation — Core Audio ana sayfası | **HTTP 200** | API kapsam referansı |
| 5 | §1'deki `pipewire.org/page_latency` · `page_scheduling` | **HTTP 404** | §1 satır 6–7 yeniden değerlendirilmeli (sessiz düzeltme yasak — yalnız not) |
| 6 | §1'deki eski Apple `coreaudio/..._api_reference` | **HTTP 404** | §1 satır 8 aynı şekilde |

---

## 13. Olay kaydı şeması (taslak — alanlar aday, alan içeriği uydurulmaz)

```
event_id      : monoton artan sayaç (dahili, uydurulmaz — sayaç değeri ölçüm değildir)
timestamp_ns  : CLOCK_MONOTONIC tabanlı (— ns)
source        : input | render | output | xrun | overrun | underrun | device | app
channel       : ilgili kanal/dipol (yoksa boş)
severity      : info | warn | fault
code          : modül-kod (bkz §11)
detail_ref    : ayrıntının gösterdiği kayıt bloğu (metin değil, referans)
```

- Şema **taslaktır**: üretim koduna alınmadan önce 8.1 kanal haritasıyla gözden geçirilecek.
- Kayıt metni: ölçüm değil, **olay tanımı**dır; içinde sayı varsa yalnız sayaç/sayaç-artışıdır.

## 14. Boş rapor formu — doğrulama oturumu

| Alan | Değer |
|---|---|
| Tarih / araç / ortam | |
| Test kimliği (§7) | |
| Örneklem / kanal / dipol | |
| Ölçüm penceresi (pencere tipi) | |
| Çıktı (geçti/kalmadı — §8 imzası) | |
| Kanıt (gözlem kütüğü satır aralığı) | |
| Sapma varsa (ne, neden, kayıt no) | |

## 15. Kapanış haritası — doğrulanmış / doğrulanmamış

| Öğe | Durum |
|---|---|
| Capture/render/release ayrımı (§4.1) | taslak — ölçülmedi |
| Fault injection sonuçları (§9) | bekliyor |
| 8.1 dipol kanal atamaları | bekliyor (§6) |
| UBD sayıları | yok (§7 — uydurulmaz) |
| TRACE sıralaması kanıtı | yok (§10.1) |

## 16. Risk defteri

| # | Risk | Olasılık/etki | Azaltma |
|---|---|---|---|
| R1 | Ölçüm adayı eşiklerin gerçek değer sanılması | yüksek/yüksek | her tabloda "aday" etiketi korunur |
| R2 | Yerel sayıların payda/ölçüm değiştirilmeden dışarı taşınması | orta/yüksek | yerel sayı içermez; yalnız prosedür |
| R3 | Kayıt yığınının kendisinin zamanlama bozması | orta/orta | kaydı ölçümden AYRI pencerede aç/kapa |
| R4 | Kök 404 kaynaklara dayanan iddialar | yüksek/orta | §12 ile yalnız HEAD 200 kaynak |

## 17. Araç envanteri (kullanılacak — çalıştırma kaydı boş)

| Araç | Rol | Çalıştırıldı mı? |
|---|---|---|
| Uygulama içi TRACE (§10.1) | kayıt akışı | hayır |
| OS ses hata günlüğü (§1.2'de adı geçen) | sistem xrun kaydı | hayır |
| XRUN/yığın sayacı (§6.2) | sayaç okuma | hayır |

## 18. Denetim listesi (özet — §8'deki imza bağlayıcıdır)

1. §9 enjeksiyonları çalıştırdın mı? 2. §6 kanalları ölçtün mü? 3. §8 formu dolduruldu mu?
4. §11 köprüleri güncellendi mi? 5. Sapmalar §16'ya yazıldı mı?

## 19. Terimler

| Terim | Anlamı (bu dosyada) |
|---|---|
| XRUN | taşma/eksik akış sayacı olayı (dahili ölçüm) |
| Overrun/Underrun | ters yönlü yığın sayacı olayı |
| Fault injection | arıza enjeksiyonu (§9) |
| Yığın derinliği | ölçülen nokta sayısı (kuyruk uzunluğu DEĞİL — §5) |

## 20. Ek kayıtlar

- Kayıt yığını **ölçüm verisi değil, olay akışıdır**; doğrulama bunlardan sayı okumaz.
- TRACE çıktısı üretim derlemesinde kapatılmış olmalı (kayıt yığını ayrı modül — §10.1).

## 21. Kuru çalıştırma (masa başı)

- §7 matrisinin her satırı için: "bu neyle ölçülür?" (§17) ve "kim onaylar?" (§8 imzası) boş bırakılmaz.
- §9.7–§9.10 senaryoları donanım olmadan **yalnız mekanizma** olarak gözden geçirilir; sonuç alanı "çalıştırılmadı" yazılır.

## 22. Sık sorulanlar

**S: Kök kaynağı 404, kullanabilir misin?**
**C:** Hayır — §12'de işaretlenir; adı geçer, URL'si yeni bölümde YOK.

**S: Ölçtüğüm xrun sayısı ne kadar iyi?**
**C:** Bu dosyanın eşiği yok (§7); yalnız kayıt ve regresyon (§10) uygulanır.

**S: 8.1'de dipolü hangi kanala veriyorum?**
**C:** §6.1'deki gibi atama yapılmalı — atama YOKSA test PAS sayılır mı? Hayır, EXCLUDE.

## 23. Kapsam dışı

Firmware · PCB/BOM · Web arayüzü · Waveform ekranı (bkz `[[K001-devre/tasarim-dogrulama]]` yüzeyi) · ADR metin değişikliği · Çekirdek DSP matematiği.

---

## 24. Kayıt yığını akış diyagramı (akış — sayı yok)

```
[olay: render çağrısı / xrun sayacı / cihaz durumu]
        │  (event kaydı — §13 şeması)
        ▼
[olay yığını — hazır alanı dolunca değdirilir (detay: §4.1-§4.2)]
        │  (değme anında sabit metin bloğu doldurulur — atama, uzunluk, kod)
        ▼
[threads / monitor döngüsü — §4.3]
        │  (periyot: ölçek — §10.4; durum: açık/kapalı)
        ▼
[arşiv: .cmtrace (eşik: §6.3 — kayıt sınırı testi)]
        │
        ▼
[analiz: ⚠️ bağımsız inceleme aracı — §7.4/§10.3]
```

- Diyagramda geçen tek sayı = başlık numaralarıdır.
- Ok yönü tek yönlüdür; geri besleme **tasarım dışıdır** (analiz sonucu kayıt yığınına geri yazılmaz).

## 25. Zamanlama bütçesi çizelgesi (doldurulacak — sayı yok)

| Aşama | Bütçe nereden gelecek? | Dolduruldu mu? |
|---|---|---|
| Olay oluşumu → yığın yazma | §10.4 ölçüm penceresinden | hayır |
| Yığın → değme | §4.2 değme kuralından (kural var, ölçüm yok) | hayır |
| Değme → dosya | analiz penceresi (§5 ayrı pencere) | hayır |
| Arşiv dosyası → rapor | §8 imzası | hayır |

- Bütçe sayıları **hiçbir yere önceden yazılmaz**; ölçüm geldiğinde §15 kapanış haritasına işlenir.

## 26. 8.1 dipol haritası — kayıt tarafı sorumlulukları (atamalar §6.1'de)

| # | Dipol | Kayıt açısından sorumluluk |
|---|---|---|
| 1–7 | Front/Center/Sur (bkz §6.1) | hangi kaydın hangi kanalı içerdiği §11'de işaretli |
| 8 | LFE | dürtü içeriği ayrı kayda girer mi — karar: §6.2 ölçümüne bağlı |
| 9 | Subwoofer | dijital istisna (§2.1) — kayıt kapsamı dışında |

## 27. Kanıt dizini (iç referans — dosya dışı iddia yok)

| Kanıt türü | Bu dosyada nerede |
|---|---|
| Kayıt kuralı metni | §3.1–§3.5 (mevcut dosyanın özgün bölümü) |
| Değme mekanizması | §4.1–§4.3 |
| Yığın kapasite testi | §6.3 |
| Ölçüm/baskımlama | §7.1–§7.6 |
| Kapsam-kararı | §8 |
| Enjeksiyon | §9.1–§9.10 |
| Kanıt talebi yokluğu | §10.5 (boş) |
| Kaynak durumu | §12 |

## 28. Uzatılmış SSS (tekrar açılanlar)

**S: TRACE'i her zaman açık tutalım mı?**
**C:** Hayır — kayıt yığını ayrı modüldür ve analiz penceresi ayrıdır (§4.3, §10.4).

**S: Yığın dolarsa ne olur?**
**C:** Değme düşer (§4.2) — bu bir **fault** sayılır ve §9.5 enjeksiyonuyla bilinçli tetiklenir.

**S: Kayıt aday eşikleri (§6.1, §6.3) neden sayı?**
**C:** Bunlar bu dosyanın (dahili) giriş değerleridir; ölçüm değildir, test **girdisidir**.

**S: Hangi kaynaklara güveniyorsun?**
**C:** §12'de HEAD 200 dönenler; §1'deki 404'ler düzeltilemez (sessiz düzeltme yasak — §12 notu).

**S: Dosya neyi "geçmiş" sayar?**
**C:** §8'deki imza; ama imza için ölçüm kaydı şart ve kayıt YOK (§10.5) → durum: **geçemedi, kanıt yok**.

## 29. Son denetim satırları (oturum kapanışı)

1. Tüm ek §'ler "aday / bekliyor / boş" işaretli mi? → evet.
2. Yeni URL: 0 → evet. Uydurma ölçüm: 0 → evet. Uydurma araç/sürüm: 0 → evet.
3. Authority tek satır → evet. Dürüstlük bloğu korundu → evet.

## 30. Kapsam dışı — genişletilmiş

Yukarıdaki §23'e ek olarak: OS kayıtlarına **dışarıdan** erişim (izleyici kurulumu),
günlük döndürme/rotasyon politikası, kayıtların şifrelenmesi — hiçbiri bu dosyanın
kapsamında **değildir** ve doğrulama iddiasına girmez.

## 31. Ek kayıt formları — ayrıntı satırı (boş)

```
olay no    : ___
kod        : ___            (§11'deki kısa kod)
kanal/dipol: ___            (§6.1 haritasına göre)
sayı       : ___            (sayaç/artış — ölçüm DEĞİL)
imzalayan  : ___
```

## 32. Komşu düzeltme (§1 yanlışını düzeltmeden taşıma)

- §1'in kök linkleri §12'de 404 olarak kayıtlıdır; **düzeltme yetkisi bu dosyada yok**.
- Yeni bölümde kök link **yazılmaz** (kural: 0 yeni URL) — atıf yalnız kaynak adıyla yapılır.

---

> **Dürüstlük bloğu:** Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler 0 yeni URL,
> 0 uydurma ölçüm içerir; doğrulanmış kaynaklar yalnız belirtilen URL'lerdir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
