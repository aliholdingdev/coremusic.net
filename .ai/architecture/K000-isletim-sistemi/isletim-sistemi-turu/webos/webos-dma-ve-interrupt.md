---
title: "webOS DMA ve Kesme - LS2 Media/Audio Servis Katmanı, g-media-pipeline, DMA Belge Boşluğu"
type: architecture-deep-dive
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-dma-ve-interrupt.md"
node_type: architecture
status: draft
date: 2026-10-14
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/webos
siblings: [webos-anayasa, webos-kernel, webos-ses-stacki, webos-dma-ve-interrupt]
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-ses-stacki.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-dma-ve-interrupt.md"
evidence:
  - "www.webosose.org/docs/reference/ls2-api/com-webos-media/ — com.webos.media LS2 servisi (medya kontrol) (fetch: 2026-10-14)"
  - "www.webosose.org/docs/reference/ls2-api/com-webos-service-audio/ — com.webos.service.audio LS2 servisi (ses kontrol) (fetch: 2026-10-14)"
  - "github.com/webosose/g-media-pipeline — GStreamer tabanlı medya pipeline (webOS OSE) (fetch: 2026-10-14)"
  - "www.webosose.org/docs/ — webOS OSE mimari referansları (fetch: 2026-10-14)"
validation: "TRUTH MODE: webOS OSE ses erişimi yalnızca LS2 servisleri üzerinden belgelenmiştir; DMA/kesme TOPOLOJİSİ resmi dokümanda YOK → ⚠️/UNKNOWN; Linux çekirdek DMA katmanına bağımlılık K000/linux dosyasındadır (dahili kanıt); 'webOS Linux üzerindedir' iddiası bu turda kaynak ile TAM doğrulanmadı → ⚠️"
open_questions:
  - "webOS OSE çekirdeğinin ALSA/DMAEngine katmanı — resmi webOS dokümanında DMA belgesi YOK (⚠️ VERIFICATION REQUIRED)"
  - "g-media-pipeline → donanım arası tampon sahipliği ve kopya sayısı DOĞRULANMADI (⚠️ UNKNOWN)"
  - "Kesme (IRQ) → servis uyanışı zinciri webOS özelinde DOĞRULANMADI (⚠️ UNKNOWN)"
  - "webOS OSE'in Linux çekirdeği kullanıp kullanmadığı — mimari sayfadan TAM doğrulanmadı (⚠️)"
group: mimari
---

# webos-dma-ve-interrupt — webOS DMA ve Kesme Mimarisi (Teknoloji: webOS)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / webos` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** webOS'ta ses/medya erişiminin **yalnızca LS2 servis katmanından** geçtiğini resmi
> kaynaklarla belgelemek; DMA/kesme topolojisinin resmi dokümanda bulunmamasını uydurmadan
> kaydetmek; alt çekirdek katmana bağımlılığı işaretleme.
> **Konu sırası:** (1) sınır → (2) servis katmanı → (3) pipeline → (4) DMA belge boşluğu →
> (5) katman modeli → (6) açık konular → (7) FAQ → (8) bağımlılık → (9) RED TEAM → (10) fetch günlüğü.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** webOS OSE'in belgelenmiş ses/medya servis yüzeyi (LS2 API, g-media-pipeline);
  DMA/kesme belge boşluğunun dürüst kaydı; katman modeli.
- ❌ **Yapılmaz:** Uydurma DMA/IRQ akışı; "webOS'ta ALSA şudur" kanıtsız iddia; servis kodu
  (repo 0 `.cpp/.h`); donanım register haritası.

---

## 1. Belgelenmiş Ses/Medya Yüzeyi (LS2 Servisleri)

| # | Servis | Rol | Kaynak |
|---|---|---|---|
| 1 | `com.webos.media` | Medya oynatma/kontrol servisi (LS2 üzerinden) | webosose.org ✅ 2026-10-14 |
| 2 | `com.webos.service.audio` | Ses kontrol servisi (LS2 üzerinden) | webosose.org ✅ 2026-10-14 |
| 3 | LS2 (Linux Service Bus) | Servisler arası mesajlaşma altyapısı — uygulama bu bus'a konuşur | webosose.org ✅ 2026-10-14 (API referansı) |
| 4 | `g-media-pipeline` | GStreamer tabanlı medya pipeline (webOS OSE) | github.com/webosose ✅ 2026-10-14 |

**İş akışı (belgelenmiş):** Uygulama → **LS2 mesajı** → `com.webos.service.audio` / `com.webos.media`
→ `g-media-pipeline` (GStreamer) → … → donanım. Uygulamanın **doğrudan ses tamponuna/donanıma
yolu yoktur** — her şey servis bus'ı üzerinden gider.

---

## 2. DMA Belge Boşluğu Kaydı

| # | Öğe | Durum | Davranış |
|---|---|---|---|
| 1 | DMA topolojisi (kanal, common buffer) | **YOK** — resmi webOS dokümanında karşılık bulunamadı | `⚠️ VERIFICATION REQUIRED` |
| 2 | Kesme (IRQ) → servis uyanışı zinciri | **YOK** | `⚠️ UNKNOWN` |
| 3 | g-media-pipeline → donanım arası tampon sahipliği (kopya sayısı) | **YOK** | `⚠️ UNKNOWN` |
| 4 | webOS OSE çekirdeğinin DMA/ALSA katmanı | **YOK** (tam doğrulanamadı) | `⚠️` |
| 5 | Ses DSP / TV donanımı DMA ilişkisi | **YOK** | `⚠️ UNKNOWN` |

> **Yasak (Zero-Hallucination):** Bu 5 satır için akış/IRQ/register **uydurulmaz**. "webOS OSE Linux
> üzerindedir ve dolayısıyla DMA Linux DMAEngine'idir" cümlesi **mantıksal çıkarımdır** — kaynakla
> tam doğrulanmadan **iddiaya dönüştürülmez**; `⚠️` olarak taşınır.

---

## 3. Katman Modeli (Doğrulanmış + Varsayımsal Ayrımı)

| Katman | Durum | Not |
|---|---|---|
| Uygulama (web app / native) | ✅ Belgelenmiş | Yalnızca LS2 mesajları |
| LS2 servis katmanı (`com.webos.media`, `com.webos.service.audio`) | ✅ Belgelenmiş | webosose.org API referansı |
| `g-media-pipeline` (GStreamer) | ✅ Repo kanıtlı | github.com/webosose ✅ 2026-10-14 |
| Ses framework / HAL | ⚠️ YOK | DMA belgesi yok |
| Çekirdek DMA/IRQ | ⚠️ YOK (linux dosyasına bağımlılık) | K000/linux ✅ dahili · webOS'a özel doğrulama yok |
| Donanım | ⚠️ YOK | Cihaza özgü |

**Sonuç:** webOS'ta uygulama katmanı için DMA **tamamen görünmezdir** — hatta macOS/iOS'tan daha
fazla, çünkü arada **servis bus'ı** vardır. Uygulamanın ölçebileceği tek şey LS2 mesaj gecikmesi ve
pipeline kuyruğudur; DMA/kesme ölçümü bu platformda **yapılamaz** (belge de yoktur).

---

## 4. Açık Konular (Q listesi)

| # | Soru | Durum |
|---|---|---|
| Q1 | DMA topolojisi (webOS OSE) | ⚠️ VERIFICATION REQUIRED — dokümanda YOK |
| Q2 | IRQ → servis uyanışı zinciri | ⚠️ UNKNOWN |
| Q3 | Pipeline tampon sahipliği / kopya sayısı | ⚠️ UNKNOWN |
| Q4 | webOS OSE çekirdeğinin DMA/ALSA katmanı | ⚠️ (Linux bağımlılığı tam doğrulanmadı) |
| Q5 | TV donanımı ses DSP DMA ilişkisi | ⚠️ UNKNOWN |

---

## 5. Sıkça Sorulanlar (FAQ)

**S: Bu dosyada DMA akışı neden yok?**
H: webOS OSE resmi dokümanı DMA/kesme topolojisini **belgelemiyor**; ses erişimi LS2 servisleri
üzerinden anlatılıyor. Uydurmak yerine boşluk `⚠️` ile kaydedildi (§2).

**S: Uygulama doğrudan ses tamponuna yazabilir mi?**
H: Hayır. Belgelenmiş yol uygulama → LS2 → servis → pipeline'dır; doğrudan tampon/donanım yolu
**yoktur**.

**S: CoreMusic webOS'u hedefleyebilir mi?**
H: Q1–Q3 kapanmadan hedef sayılmaz; DMA topolojisi bilinmeden gecikme alt sınırı hesaplanamaz →
UNKNOWN.

---

## 6. Bağımlılıklar

| Bağımlılık | Dosya |
|---|---|
| webOS ses stacki | `webos-ses-stacki.md` |
| Alt çekirdek DMA/IRQ (mantıksal katman) | `../linux/linux-dma-ve-interrupt.md` |
| Boşluk kıyas (belgelenmiş platform) | `../android/android-dma-ve-interrupt.md` |

---

## 7. RED TEAM

| # | İddia | Sorgu | Sonuç |
|---|---|---|---|
| 1 | "webOS DMA'sı Linux DMAEngine'idir" | webOS'a özel kanıt var mı? | **RED** — çıkarım; `⚠️` kalmalı |
| 2 | "Uygulama LS2 üzerinden DMA'ya dokunur" | LS2 mesajı DMA çağrısı mı? | **RED** — hayır, kontrol/komut yoludur |
| 3 | "g-media-pipeline DMA'yı yönetir" | Repo'da DMA kanıtı? | **RED/UNKNOWN** — GStreamer pipeline'dır; DMA kanıtı yok |
| 4 | "DMA belgesi yok = DMA yok" | Yokluk kanıtlanmış mı? | **RED** — yalnızca **belge yokluğu** kanıtlanmıştır |

---

## 8. Fetch Günlüğü

| Tarih | Kaynak | Durum |
|---|---|---|
| 2026-10-14 | webosose.org/docs/reference/ls2-api/com-webos-media/ | ✅ fetch |
| 2026-10-14 | webosose.org/docs/reference/ls2-api/com-webos-service-audio/ | ✅ fetch |
| 2026-10-14 | github.com/webosose/g-media-pipeline | ✅ fetch |
| 2026-10-14 | webOS OSE DMA/kesme topolojisi | ❌ karşılık bulunamadı → ⚠️ |
| 2026-10-12 | docs.kernel.org DMAEngine/IRQ (alt katman) | ✅ (K000/linux, dahili) |

---

**Authority:** Bayram Ali / Vault Steward · Red Team · Human Mode · Truth Mode
