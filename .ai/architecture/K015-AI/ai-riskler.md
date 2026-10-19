---
title: "K015-AI — Riskler"
node_id: K015-AI/ai-riskler.md
node_type: subdomain
status: draft
parent_id: K015-AI/index.md
children: []
depends_on:
  - K007-data
  - K009-security
  - K011-application
  - K012-services
evidence:
  - "shared/src/AI/PromptEngine.php:27-35 (INJECTION_PATTERNS) · 2026-10-18"
  - "shared/src/AI/AuthenticationMiddleware.php:22-24 (401 fail-closed) · 2026-10-18"
  - "shared/src/AI/ToolCalling.php:28-40 (AGENT_PERMISSIONS) · 2026-10-18"
  - ".ai/architecture/K015-AI/ai-master.md §G (açık sorular) · 2026-10-18"
validation: "2026-10-18 — her satır Olasılık×Etki×Azaltma×Sahip taşır; kanıtsız iddia UNKNOWN"
open_questions: "⚠️ MCP sürüm/yıldız kanıtı yok · ⚠️ evaluation (K006) sınırı kademe-3 açık · ⚠️ runtime etiketi (PLANNED/IMPLEMENTED) yok"
group: mimari
---

# K015-AI — Riskler

## §1 ELI15

Bu dosya yapay zeka katmanında **neyin ters gidebileceğini** önceden yazar. Her
satır dört şey taşır: **olması ne kadar muhtemel** (olasılık), **başka neyi bozar**
(etki), **nasıl küçültürüz** (azaltma) ve **kimin sorumluluğu** (sahip). Kural:
kanıt yoksa risk "yok" sayılmaz — **UNKNOWN** yazılır ve doğrulanmak üzere
işaretlenir.

## §2 Kapsam

- Yalnız K015-AI (bileşenler + kararlar D-01…D-08).
- Güvenlik duvarının geneli K009-security'nin; burada AI katmanına özgü kesimler var.

## §3 Ölçek

| Ölçek | Olasılık | Etki |
|-------|----------|------|
| 1 | Nadir (<%10) | 1 — fark edilmez |
| 2 | Seyrek (%10–30) | 2 — iş aksamaz |
| 3 | Olası (%30–60) | 3 — telafi gerekir |
| 4 | Sık (> %60) | 4 — servis/kapsam bozulur |
| 5 | Kesin | 5 — güvenlik/veri kaybı |

**Öncelik = Olasılık × Etki** (≥12 kırmızı, 6–11 sarı, ≤5 yeşil).

## §4 Risk Kayıtları

### R-01 — Prompt injection ile araç çağrısının kandırılması
- **Olasılık:** 4 · **Etki:** 5 · **Öncelik:** 20 (kırmızı)
- **Tehdit:** Kullanıcı girişi modeli "öncekileri yok say" diyerek yönlendirir,
  agent araç çağrısını (§C-02) kötüye kullanır.
- **Azaltma:** `INJECTION_PATTERNS` taraması (D-03) + `AGENT_PERMISSIONS`
  beyazlistesi (D-02) — ikisi katmanlıdır; tarama tek başına yeterli sayılmaz.
- **Sahip:** security-engineer (Expert, persona 2) + backend-architect.
- **Kanıt:** `PromptEngine.php:27-35` · `ToolCalling.php:28-40`.
- **Kalan boşluk:** Kalıp listesi statiktir — yeni saldırı kalıbı bilinmiyor
  (⚠️ cadence ile yenilenmesi gerekir, §6 U-04).

### R-02 — Yetkisiz AI ucuna erişim (auth'sız uç)
- **Olasılık:** 2 · **Etki:** 5 · **Öncelik:** 10 (sarı)
- **Tehdit:** AI ucu auth kapısız yayınlanır; veri/model sızıntısı.
- **Azaltma:** `AuthenticationMiddleware` fail-closed 401 (D-04) + K009-security
  auth zinciri; hata durumunda 500 yerine 401 (bilinçli: sızdırmamak).
- **Sahip:** security-engineer.
- **Kanıt:** `AuthenticationMiddleware.php:22-24`.
- **Kalan boşluk:** Uç envanterinin tam listesi bu dosyada değil (UNKNOWN → §6).

### R-03 — Aşırı geniş araç izni (least-privilege ihlali)
- **Olasılık:** 3 · **Etki:** 4 · **Öncelik:** 12 (kırmızı)
- **Tehdit:** Yeni agent'a izinsiz araç eklenir; yetki genişler, denetim kaybolur.
- **Azaltma:** Whitelist explicit — default reddir (D-02); izin değişikliği kod
  review'dan geçer.
- **Sahip:** backend-architect + code-reviewer.
- **Kanıt:** `ToolCalling.php:28-40`.

### R-04 — Bellek sızıntısı / yanlış katmanda veri kalıcılığı
- **Olasılık:** 3 · **Etki:** 4 · **Öncelik:** 12 (kırmızı)
- **Tehdit:** Hassas bağlam L1'de kalması gerekirken L3 (arşiv, K007-data) yazılır;
  silme/retention ihlali (KVKK bağlamı → K019-comilance ile kesişim).
- **Azaltma:** Katman doğrulaması (`remember()` bilinmeyen katmanı reddeder, D-05);
  retention K007-data politikasına tabidir.
- **Sahip:** data-engineer (Expert, persona 3).
- **Kanıt:** `MemorySystem.php:18-31`.

### R-05 — Model/sağlayıcı sürümü doğrulanmamış bağımlılık (MCP vb.)
- **Olasılık:** 3 · **Etki:** 3 · **Öncelik:** 9 (sarı)
- **Tehdit:** Sürüm/yıldız kanıtı olmadan MCP kararı uygulanır; güvenlik/olgunluk
  bilinmez (zero-hallucination ihlali riski).
- **Azaltma:** Karar ADR'ye bağlanmadan uygulanmaz (D-08); doğrulama
  `ai-web-arastirma.md` §5'te işaretlidir.
- **Sahip:** dependency-manager + Vault Steward.
- **Kanıt:** `ai-master.md` §G açık sorusu.

### R-06 — Kapsam kayması: evaluation (K006) sınırının belirsizliği
- **Olasılık:** 3 · **Etki:** 3 · **Öncelik:** 9 (sarı)
- **Tehdit:** Değerlendirme kodu iki K'ya birden yazılır; tekrar ve sorumsuzluk
  doğar.
- **Azaltma:** Kademe-3 sınır kararı kapatılmadan evaluation dosyaları üretilmez
  (D-07 bu pakette açık bırakıldı).
- **Sahip:** architect (Expert, persona 1).
- **Kanıt:** `ai-master.md` §G.

### R-07 — Hallucination: kanıtsız beyanın vault'a girmesi
- **Olasılık:** 3 · **Etki:** 4 · **Öncelik:** 12 (kırmızı)
- **Tehdit:** Var olmayan satır/URL/ADR numarası yazılır; vault güvenilirliği düşer.
- **Azaltma:** zero-hallucination (UNKNOWN/VERIFICATION REQUIRED işaretleme) + bu
  paketteki ADR uydurma yasağı; web kaynakları üretim öncesi doğrulandı.
- **Sahip:** Vault Steward + research-analyst.
- **Kanıt:** `.stage2-brief.md` yasaklar; `ai-kararlar.md` §5.

### R-08 — Runtime etiketi belirsizliği (PLANNED vs IMPLEMENTED)
- **Olasılık:** 4 · **Etki:** 2 · **Öncelik:** 8 (sarı)
- **Tehdit:** Planlı bileşen varmış gibi raporlanır; beklenti/ölçüm yanılgısı.
- **Azaltma:** Bileşen kartlarına etiket yazılmadı (`ai-bilesenler.md` §4 notu);
  master §G etiketi kapatılana kadar "implemented" beyanı yok.
- **Sahip:** Vault Steward.
- **Kanıt:** `ai-master.md` §G.

## §5 Öncelik Matrisi (özet)

| Risk | O | E | P | Renk | Sahip |
|------|---|---|---|------|-------|
| R-01 | 4 | 5 | 20 | kırmızı | security-engineer |
| R-03 | 3 | 4 | 12 | kırmızı | backend-architect |
| R-04 | 3 | 4 | 12 | kırmızı | data-engineer |
| R-07 | 3 | 4 | 12 | kırmızı | Vault Steward |
| R-02 | 2 | 5 | 10 | sarı | security-engineer |
| R-05 | 3 | 3 | 9 | sarı | dependency-manager |
| R-06 | 3 | 3 | 9 | sarı | architect |
| R-08 | 4 | 2 | 8 | sarı | Vault Steward |

## §6 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | İlgili risk | Not |
|---|-----------|-------------|-----|
| U-01 | AI uç envanterinin tam listesi | R-02 | UNKNOWN — keşif gerekli |
| U-02 | MCP sürüm/yıldız kanıtı | R-05 | `ai-web-arastirma.md` §5 |
| U-03 | evaluation sınırı (kademe-3) | R-06 | karar bekliyor |
| U-04 | INJECTION_PATTERNS güncelleme cadence'ı | R-01 | politika UNKNOWN |
| U-05 | runtime etiketi (PLANNED/IMPLEMENTED) | R-08 | master §G açık |

## §7 İzleme ve Kapanış

- Kırmızı riskler (R-01/03/04/07) `ai-test-stratejisi.md` §5'teki senaryolarla
  izlenir.
- U-01…U-05 yalnız kanıtla kapanır; kapanışta bu dosya güncellenir ve UNKNOWN
  satırı silinmez, "kapatıldı (kanıt: …)" olarak işaretlenir.

## §8 İlgili Dosyalar

- `ai-kararlar.md` — risklerin bağlandığı D-01…D-08
- `ai-test-stratejisi.md` — risk doğrulama senaryoları
- `ai-web-arastirma.md` — dış kaynak doygunluğu (R-01/R-05)
- `../../K009-security/` — güvenlik duvarının geneli

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
