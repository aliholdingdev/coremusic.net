---
node_id: K000-isletim-sistemi/os-kararlar.md
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on:
  - K001-donanım
  - K003-audio
  - K005-firmware
evidence:
  - ".ai/.decisions/index.md — ADR listesi (accepted 80 + ADR-091 + draft 3) — okundu 2026-10-18"
  - "os-master.md §A–§C, FM open_questions Q1–Q5 — okundu 2026-10-18"
  - "index.md §2 sınırlar · §3 kanıt durumu — okundu 2026-10-18"
  - "index.md §BAĞLANTILAR (10 hedef K, 2026-10-17 taraması) — okundu 2026-10-18"
validation:
  - "2026-10-18 — her kararın ADR numarası .decisions/index.md ile çapraz okundu (uydurma numara YOK)"
  - "2026-10-18 — format: Durum/Bağlam/Karar/Alternatifler/Sonuçlar (brief §10)"
  - "2026-10-18 — K000 içi (yerel) kararlar ADR'siz, numara üretilmedi"
open_questions:
  - "K000 için henüz ADR'lenmemiş yerel kararlar (KD-06, KD-07) üst karar bekliyor (⚠️)"
  - "os-master FM `depends_on: [K002-altyapi]` phatom kaydı — hangi K ile değiştirileceği bilinmiyor (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# os-kararlar — K000 Karar Defteri

> Format (brief §10): **Durum / Bağlam / Karar / Alternatifler / Sonuçlar**.
> ADR numaraları **yalnız** `.ai/.decisions/index.md`'de diskte var olan
> kayıtlardan alınmıştır; uydurma numara yasaktır. ADR'siz yerel kararlar
> `KD-` önekiyle numaralanır (bu dosyanın kendi serisi, ADR değildir).

## §1 ELI15

> **Bu dosya "neden böyle yapıyoruz?" sorusunun kayıtlı cevaplarıdır.**

Her karar dört soruyu yanıtlar: ne durumdaydık (bağlam), neyi seçtik (karar),
neyi reddettik (alternatifler), kazancımız/kaybımız ne (sonuçlar). Yeni karar
eklenir, eskisi **silinmez** — yanlış çıkan karar `Durum: Reddedildi` olur.

## §2 ADR bağlantılı kararlar (diskte doğrulandı — 2026-10-18)

### KD-A1 · ADR-017 — DSP Hardware Mode (XMOS, JUCE, ASIO)

| Alan | Değer |
|---|---|
| **Durum** | accepted (index §3 satır: `ADR-017-dsp-hardware-mode`) |
| **Bağlam** | Ses işleme zinciri host mu, donanım içinde mi? ASIO/JUCE sürüm kanıtı gerekiyor |
| **Karar** | Zincir hesabı host'ta (NevaEngine/JUCE); XMOS yalnız hard-RT zamanlama + I2S/TDM |
| **Alternatifler** | (a) her şey donanım içinde — reddedildi: K005 kapsam şişmesi · (b) her şey host — reddedildi: RT garantisi yok |
| **Sonuçlar** | K000↔K005 sınırı netleşti; ADR-017 KAPANMA kaydı dsp-master FM Q5 (2026-10-15) |

### KD-A2 · ADR-019 — Per-OS Neva Player

| Alan | Değer |
|---|---|
| **Durum** | accepted (`ADR-019-per-os-neva-player`) |
| **Bağlam** | Oynatıcı her OS'te aynı mı davranmalı? |
| **Karar** | OS başına ayrı Neva Player profili |
| **Alternatifler** | tek ortak oynatıcı — reddedildi: OS ses yığını davranış farkları |
| **Sonuçlar** | `isletim-sistemi-turu` 8 platform dalı bu kararı besler; K000↔K003 sınırı etkilenir |

### KD-A3 · ADR-082 — Dev/Staging Environment Architecture

| Alan | Değer |
|---|---|
| **Durum** | accepted (`ADR-082-dev-environment`) |
| **Bağlam** | Geliştirme/CI ortamı ile production aynı mı olmalı? |
| **Karar** | Ayrı dev/staging mimarisi |
| **Alternatifler** | tek ortam — reddedildi: veri/secret riski |
| **Sonuçlar** | CI platformu `ubuntu-latest` (`.github/workflows/ci.yml:1`) bu kararın K000 ayağı |

### KD-A4 · ADR-091 — TemplateEngine eval() Kaldırımı (Guardrail #21)

| Alan | Değer |
|---|---|
| **Durum** | accepted (kök dosya — `ADR-091-template-engine-no-eval.md`) |
| **Bağlam** | Şablonda `eval()` güvenlik riski |
| **Karar** | `eval()` kaldırıldı |
| **Alternatifler** | sanitize ile sürdürme — reddedildi |
| **Sonuçlar** | K000'de anlamı: kullanıcı-kernel sınırı öncesi OS'e giden yol kapatıldı; şablon motoru OS syscall'ına `realpath` denetimiyle girer |

### KD-A5 · ADR-101 — Güvenlik Kanonik Sırası + Uzak Çağrı Sınırları

| Alan | Değer |
|---|---|
| **Durum** | accepted (`ADR-101-guvenlik-kanonik-sirasi-uzak-cagri`) |
| **Bağlam** | Timeout/retry/fallback sırası her katmanda aynı mı? |
| **Karar** | Güvenlik kontrolleri kanonik sırada; uzak çağrıda timeout/retry/fallback sınırları |
| **Alternatifler** | her modül kendi sırası — reddedildi: tutarsız fail-safe |
| **Sonuçlar** | K000'de `shell_exec`/`proc_open` yolları bu sınırlara bağlanır (os-master §C.1) |

### KD-A6 · ADR-098 / ADR-099 — Modül Sınırı = Gizlenecek Karar · Tek Deploy Modular Monolith

| Alan | Değer |
|---|---|
| **Durum** | accepted (her ikisi de) |
| **Bağlam** | K000 bir **domain**tir, deploy birimi değil |
| **Karar** | OS katmanı mantıksal modül sınırıdır; deploy tekittir |
| **Alternatifler** | OS katmanını ayrı deploy — reddedildi |
| **Sonuçlar** | K000 kök dosyaları kod **üretmez** (index §1); sınır yalnız dokümanda |

### KD-A7 · ADR-097 §5.5 — K-Space V3 (draft) · iki-ekseni gruplama

| Alan | Değer |
|---|---|
| **Durum** | **draft** (`draft/ADR-097-…`, onay bekliyor) |
| **Bağlam** | Kök MD şeması ve grup alanı nereden geliyor? |
| **Karar** | Her dosyanın frontmatter'ında 9. alan `group` (mimari/kod/kanit) |
| **Alternatifler** | grup alanı olmadan — reddedildi: §GRUPLAR matrisi okunamaz |
| **Sonuçlar** | Bu dosya dahil Stage-2 üretimlerinin tamamı `group: mimari` taşır |

## §3 K000 yerel kararları (ADR'siz — bu dosyanın serisi)

### KD-06 — K000 kod üretmez

| Alan | Değer |
|---|---|
| **Durum** | active (bu üretim) |
| **Bağlam** | Domain dosyaları kod mu yazmalı, sözleşme mı? |
| **Karar** | K000 yalnız OS sözleşmesini sabitler; kod örnekleri `-kod-ornekleri` dosyasında **örnek**tir, üretim kodu değildir |
| **Alternatifler** | domain'de kod barındırmak — reddedildi: katman kirliliği |
| **Sonuçlar** | index §1 "kod üretmez"; `*.php`/`*.js`/`*.c` yüzeyine dokunulmaz |

### KD-07 — Hedef platform seti belirsizliği korunur (Q4)

| Alan | Değer |
|---|---|
| **Durum** | active — **açık** |
| **Bağlam** | Windows/Linux/macOS üçü de hedef mi? |
| **Karar** | `UNKNOWN` yazılır, tahmin edilmez |
| **Alternatifler** | "üçü de" demek — reddedildi: kanıt yok |
| **Sonuçlar** | os-kapsam §8 Q4 · os-riskler R-satırı |

### KD-08 — Ölü K referansları silinmez, `⚠️` ile taşınır

| Alan | Değer |
|---|---|
| **Durum** | active (brief yasağı) |
| **Bağlam** | FM `depends_on: [K002-altyapi]` diskte karşılığı olmayan K adıdır |
| **Karar** | Referans olduğu yerde `⚠️` işaretlenir; yeni yazım kara listeden (K002-altyapi · K0100-identitiy · K020-licence · K020-comilance · K020-comere · K001-infrastructure · K002-data · K019-testing · K018-devops) |
| **Alternatifler** | sessizce düzeltmek — reddedildi: izinsiz silme |
| **Sonuçlar** | Bu üretimde ölü referans **silinmedi**; yeni dosyalarda yalnız diskte var olan K adları yazıldı |

### KD-09 — Ses erişimi içeride, ses verisi dışarıda

| Alan | Değer |
|---|---|
| **Durum** | active |
| **Bağlam** | WASAPI/ALSA çağrısı K000 mi K003 mü? |
| **Karar** | Cihaza **erişim** K000 (isletim-sistemi-audio); akış/routing K003 |
| **Alternatifler** | ikisi de tek düğümde — reddedildi: sınır belirsizliği |
| **Sonuçlar** | os-kapsam §2 I5 ↔ §3 O5; `dsp`/`firmware` komşularıyla çakışma yok |

## §4 Karar ↔ komşu K matrisi

| # | Karar | Etkilediği komşu | Geçiş |
|---|---|---|---|
| 1 | KD-A1 (ADR-017) | K004-dsp · K005-firmware | host=hesap · cihaz=zamanlama |
| 2 | KD-A2 (ADR-019) | K003-audio | OS başına oynatıcı profili |
| 3 | KD-A3 (ADR-082) | K17-devops | CI platformu seçimi |
| 4 | KD-A5 (ADR-101) | K009-security | timeout/retry/fallback sırası |
| 5 | KD-09 | K003-audio | erişim ↔ akış ayrımı |
| 6 | KD-08 | tüm komşular | ölü K adı yazılmaz |

## §5 Karar ekleme protokolü

| Adım | Aksiyon | Zorunlu kayıt |
|---|---|---|
| 1 | Karar diskteki mevcut bir dosyayla çelişiyor mu? | çapraz satır |
| 2 | ADR var mı? → `.ai/.decisions/index.md` kontrol | ADR no **veya** `KD-` |
| 3 | ADR yoksa `KD-` serisi kullanılır (uydurma ADR yasak) | satır no |
| 4 | Alternatifler + Sonuçlar doldurulur | 5 alan zorunlu |
| 5 | Komşu etkisi varsa §4'e satır | komşu K |

## §6 Karar durumu sözlüğü

| Durum | Anlamı |
|---|---|
| `accepted` | diskte ADR dosyası var, onaylı |
| `draft` | diskte taslak var, onay bekliyor |
| `active` | bu dosyanın yerel kararı, ADR yok |
| `Reddedildi` | alternatif seçildi — satır silinmez |
| `⚠️` | üst karar/kanıt bekliyor |

## §7 Doğrulama

| # | Kriter | Durum |
|---|---|---|
| C1 | Her ADR no `.decisions/index.md`'de mevcut | ✅ 2026-10-18 (ADR-017/019/082/091/098/099/101/097) |
| C2 | Her karar 5 alanı içeriyor (Durum/Bağlam/Karar/Alt/Sonuç) | ✅ |
| C3 | Uydurma ADR no yok | ✅ |
| C4 | Ölü K referansı silinmedi (⚠️ taşındı) | ✅ |

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | KD-06/KD-07 ADR'leşmeli mi? | `⚠️` üst karar |
| Q2 | `K002-altyapi` phatom kaydı ne zaman temizlenir? | `⚠️ VERIFICATION REQUIRED` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18
