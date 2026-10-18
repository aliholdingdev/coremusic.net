---
title: "K016-observability — Kararlar"
node_id: K016-observability/observability-kararlar.md
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
  - ".ai/architecture/K016-observability/observability-master.md §B (28 karar envanteri) · 2026-10-18"
  - ".ai/architecture/K016-observability/observability-master.md §E (repo kanıtları) · 2026-10-18"
  - ".ai/.decisions/index.md (ADR numaraları — uydurma yasak) · 2026-10-18"
  - "shared/src/Log/Redactor.php:31/65 · FileHandler.php:188 · 2026-10-18"
validation: "2026-10-18 — numaralar yalnız envanterden; 'adr' sütunu gerçek numara ya da 'ADR numarası atanacak'"
open_questions: "⚠️ metrik/tracing/health/alert teknolojisi kararı ADR bekliyor · ⚠️ alerting/ dizin kararı açık · ⚠️ .ai/log.md sınırı kademe-3"
group: mimari
---

# K016-observability — Kararlar

## §1 ELI15

Gözlemleme katmanının **kararları** burada: "logu nereye yazarız, hassas bilgiyi
nasıl maskeleriz, metriği neyle ölçeriz". Kural: ADR numarası **yalnızca**
`.ai/.decisions/index.md`'den gelir; henüz numara almamış karara **"ADR numarası
atanacak"** yazılır. Uydurma numara, gelecekte gerçek bir kararla çakışır — bu
yüzden yasaktır.

## §2 Kapsam

- K016-observability kararları; master `observability-master.md` §B'deki 28
  maddenin bu paketteki kaydı.
- Başka K'ların kararlarına dokunulmaz (beyazliste).

## §3 Karar Kaydı (disk kanıtlı)

### D-01 — Logger merkezi factory'den kurulur
- **Karar:** Logger dağıtık `new` ile değil `LoggerFactory` ile alınır.
- **Durum:** Implemented · **Kanıt:** `LoggerFactory.php:11`.
- **Sonuç:** Tek konfigürasyon noktası; davranış değişikliği tek yerde toplanır.
- **ADR:** ADR numarası atanacak.

### D-02 — Uygulama logu PSR-3 `LoggerInterface`'ine uyar
- **Karar:** Tüm yazıcılar PSR-3 sözleşmesini uygular.
- **Durum:** Implemented · **Kanıt:** `FileHandler.php:30` +
  `Psr3LoggerTest.php:44/49` (arayüz + tüm methodlar).
- **Sonuç:** Sağlayıcı değiştirilebilir; test sözleşmeyi kilitler.
- **ADR:** ADR numarası atanacak.

### D-03 — Seviye kapısı: minLevel + debugMode
- **Karar:** `info` altındaki seviyeler minLevel'a göre elenir; debug modu ayrı.
- **Durum:** Implemented · **Kanıt:** `FileHandler.php:40/52/54`,
  `Psr3LoggerTest.php:81/95/103`.
- **Sonuç:** Üretimde gürültü azalır; kapı testle korunur.
- **ADR:** ADR numarası atanacak.

### D-04 — Hassas veri redaction (mask) zorunlu
- **Karar:** password/token/secret/authorization/cookie/e-posta/hash
  `[REDACTED]` ile maskelenir; yazım yolu maskeden geçer.
- **Durum:** Implemented · **Kanıt:** `Redactor.php:25/28/31/65`,
  `FileHandler.php:188` (securityStream),
  `LoggerRedactionTest.php:55/70/140/165`.
- **Sonuç:** PII/sır diske sızmaz; K009-security ile kesişir.
- **ADR:** ADR numarası atanacak.

### D-05 — Güvenlik olayları ayrı akışa (securityStream)
- **Karar:** Uyarı-ailesi/seviye olayları `securityStream=true` ile ayrılır.
- **Durum:** Implemented · **Kanıt:** `FileHandler.php:52/188`,
  `Psr3LoggerTest.php:127`.
- **Sonuç:** Güvenlik incelemesi ayrı akıştan okunur.
- **ADR:** ADR numarası atanacak.

### D-06 — İzlenebilirlik: traceId satıra damgalanır
- **Karar:** Her satıra traceId eklenir.
- **Durum:** Implemented · **Kanıt:** `Psr3LoggerTest.php:116`.
- **Sonuç:** İstek boyu korelasyonu mümkün; ağ katmanıyla (K010-network)
  ilişkili.
- **ADR:** ADR numarası atanacak.

## §4 Kararlar — ADR/PLANNED Bekleyenler

### D-07 — Metrik teknolojisi seçilmedi (öneri: Prometheus)
- **Durum:** ADR bekliyor — **metrik/tracing/health/alert teknolojisi UNKNOWN**.
- **Dayanak:** `observability-master.md` §B/§G; diskte metrik kodu **yok**
  (zero-hallucination — "var" denmedi).
- **ADR:** ADR numarası atanacak (öneri PLANNED, uygulanmadı).

### D-08 — Tracing teknolojisi seçilmedi (öneri: OpenTelemetry)
- **Durum:** ADR bekliyor · Aday: OpenTelemetry (kaynak:
  `observability-web-arastirma.md` §3 — doğrulandı 2026-10-18).
- **ADR:** ADR numarası atanacak.

### D-09 — Health check biçimi (öneri: RFC-healthcheck)
- **Durum:** ADR bekliyor · diskte kanıt yok.
- **ADR:** ADR numarası atanacak.

### D-10 — Alert/uyarı bileşeni + `alerting/` fazladan dizin kararı
- **Durum:** Açık — dizin kararı açık (master §G); fazladan dizin açılıp
  açılmayacağı belli değil.
- **ADR:** ADR numarası atanacak.

### D-11 — vault `.ai/log.md` ↔ uygulama logu sınırı
- **Durum:** Açık (kademe-3) — bu paket uygulama logunu belgeler; vault session
  logu ayrı katmandır ve bu dosyaya taşınmaz.
- **ADR:** ADR numarası atanacak.

## §5 ADR Durum Özeti

| Karar | ADR | Durum |
|-------|-----|-------|
| D-01…D-06 | ADR numarası atanacak | Implemented (kod kanıtlı), dosya yok |
| D-07…D-10 | ADR numarası atanacak | ADR bekliyor (teknoloji UNKNOWN) |
| D-11 | ADR numarası atanacak | kademe-3 sınır kararı |

> `.ai/.decisions/index.md` bu oturumda **ADR-083/087/088'i** "NO FILE on disk"
> olarak döndürdü; K016 için envanterde geçen gerçek numara **kaydedilmedi** →
> tüm satırlar "ADR numarası atanacak". Numara ataması Vault Steward yetkisindedir.

## §6 Karar ↔ Bileşen Eşlemesi

| Karar | Bileşen |
|-------|---------|
| D-01 | C-01 LoggerFactory |
| D-02 | C-02 FileHandler |
| D-03 | C-02 FileHandler (LEVELS/minLevel) |
| D-04 | C-03 Redactor |
| D-05 | C-02 FileHandler (securityStream) |
| D-06 | C-02 (traceId damgası, test T-01) |

## §7 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

- ⚠️ D-07…D-10: teknoloji seçimi **UNKNOWN** — diskte metrik/tracing/health/alert
  kodu yok; öneri adaydır, karar değildir.
- ⚠️ D-11: kademe-3 sınır kararı **açık**.
- ⚠️ Implemented kararlarda (D-01…D-06) kod kanıtı var ama **ADR dosyası yok**
  → numara ataması bekliyor.

## §8 İlgili Dosyalar

- `observability-master.md` §B — kaynak karar envanteri (28 madde)
- `observability-bilesenler.md` — kararların bağlandığı bileşenler
- `observability-riskler.md` — karar riskleri
- `../../.decisions/index.md` — gerçek ADR numaralarının tek kaynağı

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
