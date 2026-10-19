---
title: "K016-observability — Evrensel Dil"
node_id: K016-observability/observability-evrensel-dil.md
node_type: subdomain
status: draft
parent_id: K016-observability/index.md
children: []
depends_on:
  - K012-services
  - K010-network
  - K014-media
  - K17-devops
evidence:
  - "shared/src/Log/*.php terim karşılıkları (LEVELS/WARNING_FAMILY/Redactor/traceId) · 2026-10-18"
  - "https://php-fig.org/psr/psr-3/ — seviye adları (erişim 2026-10-18)"
  - "https://opentelemetry.io/docs/concepts/signals/ — sinyal adları (erişim 2026-10-13)"
validation: "2026-10-18 — ≥15 terim · EN/TR + kod karşılığı; resmi karşılık yoksa işaretli"
open_questions: "⚠️ 'log/olay/kayıt' TR tekliği · ⚠️ health/alert TR karşılıkları vault'ta tekleştirilmedi"
group: mimari
---

# K016-observability — Evrensel Dil

## §1 ELI15

Gözlemleme konusunda kullandığımız kelimelerin **tek anlamı**: "log" dediğimizde,
"trace" dediğimizde herkes aynı şeyi anlasın. Tablo EN ↔ TR ↔ kod karşılığını
verir. Karşılık resmi değilse (kaynakta standart yoksa) işaretlenir — uydurma
karşılık yazılmaz.

## §2 Kapsam

- Yalnız K016 terimleri.
- PSR-3/OTel kaynaklı terimlerde kaynak (erişim tarihi) §5'te.

## §3 EN ↔ TR Terim Tablosu

| # | EN Terim | TR Karşılık | Tanım | Kod karşılığı |
|---|----------|-------------|-------|----------------|
| 1 | Log / Logging | Kayıt / Günlükleme* | Zaman damgalı olay satırı | `FileHandler::log()` |
| 2 | Logger | Kaydedici** | Yazan bileşen | `LoggerFactory` |
| 3 | Level | Seviye | debug/info/warning/error… | `LEVELS` |
| 4 | Minimum level | Alt seviye eşiği | Bu seviyenin altı yazılmaz | `minLevel` (`FileHandler.php:54`) |
| 5 | Debug mode | Hata ayıklama kipi | Debug satırlarının anahtarı | `debugMode` |
| 6 | Context | Bağlam | Mesajla giden anahtar-değer dizisi | `log(..., array $context)` |
| 7 | Trace ID | İz kimliği | İstek boyu korelasyon anahtarı | traceId (`Psr3LoggerTest:116`) |
| 8 | Redaction / Redactor | Maskeleme / Maskeleyici | Hassas veriyi `[REDACTED]` yapma | `Redactor::redact()` |
| 9 | Mask | Maske | Değiştirilmiş değer | `[REDACTED]` (`Redactor.php:28`) |
| 10 | Sensitive data | Hassas veri | Şifre/token/e-posta… | `SENSITIVE_KEYS` (`:31`) |
| 11 | PII | Kişisel veri (KVKK: kişisel veri) | Kimliği belirlenebilir veri | e-posta maskesi (`:56`) |
| 12 | Security stream | Güvenlik akışı | Ayrı güvenlik olayı dosyası | `securityStream` (`FileHandler.php:188`) |
| 13 | Warning family | Uyarı ailesi | warning…emergency grubu | `WARNING_FAMILY` (`:52`) |
| 14 | Metric | Metrik | Sayısal ölçüm (ör. gecikme) | ⚠️ yok (D-07 UNKNOWN) |
| 15 | Trace (tracing) | İz (izleme) | İstek dağınık yolculuğu | ⚠️ yok (D-08 UNKNOWN) |
| 16 | Span | Aralık** | Tek adımın süresi | ⚠️ yok (OTel, S-2) |
| 17 | Health check | Sağlık denetimi | Ucun canlılığı | ⚠️ yok (D-09 UNKNOWN) |
| 18 | Alert / Alerting | Uyarı / Uyarılama | Eşiği aşınca tetik | ⚠️ yok (D-10 UNKNOWN) |
| 19 | Burn rate | Yanma hızı** | SLO bütçesinin harcanma hızı | ⚠️ yok (S-4, Google SRE) |
| 20 | Observability | Gözlemlenebilirlik | Sistemi dışarıdan anlama | bu K'nin adı |
| 21 | Structured log | Yapılandırılmış kayıt | JSON alanlı satır | structured logger (`:165` test) |
| 22 | Log rotation | Günlük döndürme** | Dosya boyutu/tarih sınırı | ⚠️ UNKNOWN (`FileHandler:207` yolu) |

\* "Logging" için TR'de yaygın "günlükleme" olsa da bu vault'ta **"kayıt (log)"**
ikilisi kullanılır — arama tutarlılığı için (⚠️ §6 G-01).
\** Resmi/standart TR karşılığı **yok** (bu vault'un önerisi) → işaretli.

## §4 Kullanım Kuralları

1. İlk geçişte çift yazımla ("iz kimliği (traceId)"), sonrası tek.
2. Kod adları çevrilmez: `Redactor`, `minLevel`, `securityStream` aynen kalır.
3. TR metinlerde "uyarı (alert)" ≠ "sağlık denetimi (health)" asla birbirine
   karıştırılmaz (S-3: health ≠ alert).
4. Karşılığı olmayan terim (§3 ⚠️ satırları) **eksik bileşendir** — dil sorunu
   değil, D-07…D-10 karar boşluğudur; "var" denmez.

## §5 Karar ↔ Terim Eşlemesi + Kaynak

| Karar | Terim(ler) | Kaynak |
|-------|-----------|--------|
| D-02 | Logger · Level · Context | PSR-3 (S-1, 2026-10-18) |
| D-03 | Minimum level · Debug mode | PSR-3 (S-1) |
| D-04 | Redaction · Mask · Sensitive · PII | kod (Redactor) · KVKK bağlamı |
| D-05 | Security stream · Warning family | kod (FileHandler) |
| D-06 | Trace ID | test (Psr3LoggerTest) |
| D-07 | Metric · Alert | Prometheus/SRE (S-3/S-4) |
| D-08 | Trace · Span | OpenTelemetry (S-2) |
| D-09 | Health check | ⚠️ aday yok, ADR bekliyor |
| D-10 | Alert / Alerting | Prometheus/SRE (S-3/S-4) |

## §6 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Not |
|---|-----------|-----|
| G-01 | "Log" için tek TR karşılık kararı | "kayıt (log)" kullanımı kararı yok |
| G-02 | Span/burn-rate/rotation TR tekilliği | standart TR yok (öneri işaretli) |
| G-03 | §3'teki ⚠️ satırlarının bileşen karşılığı | D-07…D-10 ADR bekliyor |

## §6.1 Örnek Cümle Kullanımları

| Bağlam | Yanlış | Doğru |
|--------|--------|-------|
| İlk geçiş | "log'a yazıldı" | "kayıda (log) yazıldı" |
| Maske | "şifre silindi" | "şifre maskelendi (`[REDACTED]`)" |
| Kapı | "info kapalıydı" | "alt seviye eşiği (minLevel) info'ya ayarlıydı" |
| Uyarı | "health alert'i tetiklendi" | "uyarı (alert) tetiklendi; sağlık denetimi ayrı" |
| Eksik bileşen | "metrik yok" → belirsiz | "metrik bileşeni UNKNOWN (ADR bekliyor)" |
| Kod | "Redaktör sınıfı" | `Redactor` (kod adı çevrilmez) |

## §6.2 Sözlük Bakım Kuralları

1. **Ekleme:** Yeni terim yalnız disk kanıtıyla (yeni sembol/test) eklenir.
2. **Eksik bileşen terimleri** (§3 ⚠️ satırları: metric/trace/health/alert/span/
   burn-rate): bileşen D-07…D-10 ile ADR'den geçince tablo "gerçek karşılık +
   kod" ile güncellenir; o güne kadar ⚠️ işareti kalır.
3. **Kaldırma:** Terim silinmez; kavram kalktıysa "kullanım dışı" işaretlenir.
4. **Çapraz K:** Redaction/mask terimleri K009-security ile; PII terimi
   K019-comilance ile **aynı anlamda** tutulur (çelişkide ortak K kazanır).

## §7 İlgili Dosyalar

- `observability-bilesenler.md` · `observability-kararlar.md` — terimlerin
  kullanıldığı dosyalar
- `observability-web-arastirma.md` — S-1…S-6 kaynakları
- `../../K009-security/` — PII/hassas veri kesişimi · `../../K019-comilance/` — KVKK

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
