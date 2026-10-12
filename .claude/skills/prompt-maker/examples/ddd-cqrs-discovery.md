---
title: "prompt-maker Example — DDD/CQRS Dallı Keşif (Adaptif Soru Ağacı)"
type: example
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Örnek: DDD/CQRS Dallı Keşif — Adaptif Soru Ağacı

Bu örnek, prompt-maker'ın **adaptif** (statik questionnaire değil) soru motorunu ve
**[UNKNOWN]/[VERIFY REQUIRED]** yönetimini gösterir: her cevap → analiz → eksik bilgi →
sonraki soru dallanması.

---

## 1. Ham İstek

```text
"kullanıcı: sipariş/indirme akışına event sourcing mi kursak"
```

---

## 2. Dallı Keşif (CEVAP A → ANALİZ → SORU B)

### Dal 1 — Mimari temel

```text
S: Bu akış nerede? Mevcut sistem mi, yeni mi?
C: Mevcut download-service; kuyruk ekleniyor (önceki görev).

S: Event sourcing NEDEN gündeme geldi? (WHY? — § Derinlik kuralı)
C: "Kayıt izleme/audit için" dedi.

ANALİZ: Audit ≠ Event Sourcing. CoreMusic'te zaten `log.md` append-only +
coremusic_download history alanı var. → CQRS/ES zorunlu DEĞİL sinyali.
```

### Dal 2 — CQRS dallanması (kullanıcı ısrar ederse)

```text
S: Neden CQRS? Command/Query ayrımı hangi bounded context'te?
C: "Yazma çok, okuma az"

S: Read model ayrı mı? Ayrı read DB mi?
C: [UNKNOWN] → Cevap yok — UYDURULMAZ.

S: Eventual Consistency kabul ediliyor mu?
C: Hayır — kullanıcı "aynı anda görmeliyim" dedi.

ANALİZ: Eventual Consistency red + tek DB → CQRS'nin temel faydası (read ölçekleme)
burada zayıf. → Alternatif: basit kuyruk + transactional outbox (kapsam küçük).
```

### Dal 3 — Event Sourcing AYRI requirement (§ kritik kural)

```text
KURAL: "CQRS seçildi diye Event Sourcing varsayma."
S: ES ayrıca mı isteniyorsa: "Event store kalıcılığı (silinmeyen event geçmişi)
   gerçekten gerekiyor mu? Recovery/audit mi, sadece log mu?"
C: "Log yeterli"
→ ES: REDDEDİLDİ — gerekçe: log zaten var; event store operasyon maliyeti
  (schema, replay, snapshot) kapsamı aşar. (Red gerekçesi zorunlu.)
```

---

## 3. [UNKNOWN] Yönetimi (asla tahmin yok)

| Bilgi | Durum | Aksiyon |
|-------|-------|---------|
| Mevcut kuyruk şeması | `[UNKNOWN]` | Disk okuma yapılacak (download-service + §18) |
| Read DB var mı | `[UNKNOWN]` | P0 sorusu — cevap yoksa ES/CQRS finali YOK |
| ADR-026 kapsamı | `[VERIFY REQUIRED]` | ADR metni okunur, tahmin edilmez |
| Günlük event hacmi | `[UNKNOWN]` | Performans iddiası yazılmaz |

**Çelişki yakalanırsa:**

```text
CONFLICT DETECTED
  Kullanıcı: "audit için ES"  ↔  Vault: "log.md append-only + history alanı (§18)"
  Aksiyon: yalnız bu noktayı netleştiren 1 soru sor:
  "Mevcut history/log kanalı yetersiz mi — spesifik hangi bilgi eksik?"
```

---

## 4. Tamamlanma Kapısı (Completion Gate)

```text
CHECK: Intent net? (evet — audit) · Technology net? (mevcut Node/TS service)
       Requirements yeterli mi? (HAYIR — read model/geçmiş sorgusu ihtiyacı bilinmiyor)
→ Kritik eksik var → soru sormaya devam. "DISCOVERY COMPLETE" DEĞİL.
```

Kullanıcı **"devam et / yeterli"** derse final prompt üretilir; aksi halde
mevcut durumda üretim YASAK (bilgi eksikse final = uydurma üretmek demektir).

---

## 5. Örnek Final (kullanıcı yeterli derse — kısaltılmış)

```markdown
# GENERATED PROMPT (kapsam: basit kuyruk + audit)
## Mimari: DDD YOK (kapsam tek bounded context'ten küçük) · CQRS YOK (eventual
  consistency red edildi) · Event Sourcing REDDEDİLDİ (gerekçe §2 Dal 3)
## Karar: transactional kuyruk + coremusic_download history + log.md append
## Audit: mevcut history alanı — ek event store YOK
## [VERIFY REQUIRED]: ADR-026/028 tam metinleri okunarak netleşecek maddeler
```

---

## Öğrenilecek dersler

1. **WHY sorgusu** — "ES kursak" isteğinin gerçek nedeni sorgulanır; çoğunlukla audit
   = mevcut log/history ile çözülür.
2. **CQRS ≠ ES** — ayrı gereksinim; ayrılmadan varsayılmaz.
3. **[UNKNOWN] finali bloke eder** — kritik cevap yoksa Completion Gate açılmaz.
4. **Red gerekçesi zorunlu** — "REDDEDİLDİ — gerekçe" satırı yoksa karar geçersiz.
5. **CONFLICT DETECTED** — yalnız çelişen noktayı netleştiren tek soru sorulur.
