---
title: "K000 OS — Bağımlılık & Sınır (izinli/yasak matrisleri · boundary)"
type: deep
category: architecture
version: "1.0.0"
status: draft
authority: "SSOT: .ai/architecture/K000-isletim-sistemi/index.md — derin dosya (ADR-096 karar 13-16 · rules R3.1)"
updated: 2026-10-08
tier: 3
domain: K000-isletim-sistemi
ssot: false
risk: medium
owner: win-sw
depends-on: [".ai/architecture/K000-isletim-sistemi/index.md", ".ai/architecture/00-kspace-anayasa.md"]
---

# K000 OS — Bağımlılık & Sınır

**Künye**

| Alan | Değer |
|---|---|
| Dosya | `bagimlilik-sinir.md` — katman derin dosyası 3/4 (rules R3.1) |
| Konu | İzinli/yasak matrisleri · DATA/SECURITY/FAILURE boundary detayı · port/adapter · ihlal akışı · YARGI uygulaması |
| Özet sahibi | `index.md` §5 / §6 — bu dosya derinlik katmanıdır |
| Durum | draft · bağlayıcı hükümler [CURRENT], tasarımlar [DESIGN] |
| Sahip | `win-sw` |

> **Bağlayıcılık:** bu dosyadaki yasaklar `rules.md` R6 + anayasa §5.1 + F1
> H19/H20'den gelir; burada **yorumlanır**, yeni kural üretilmez. Yeni kural
> için ADR zorunlu (H08 · R10).

---

## §1 Sınır Haritası (hangi madde hangi dosyada)

| Sınır | EK A değeri | Derinlik |
|---|---|---|
| data | çekirdek veri sınırı | §4 |
| security | ORTA | §5 |
| failure | fail-over | §6 |
| izinli | `—` (kök) | §2 |
| yasak | üst katmana doğrudan erişim (yalnız port/adapter) | §3 |
| observability / test | (EK A'da alan yok → tasarım) | §7 |
| port/adapter | F1 §8.2 | §8 |
| ihlal akışı | anayasa §5.1 · R6.5/R6.6 | §9 |
| YARGI 1-6 | F1 §8.4 | §10 |

---

## §2 İZİNLİ Bağımlılıklar (EK A: izinli = `—` · R6.1)

| Hedef | Tür | Aralık / not |
|---|---|---|
| — | alt katman | **YOK** — K000 kök katmandır (anayasa satır 71: "izinli=— (kök)") |
| Donanım/firmware (K001 üstü) | port/adapter | Yalnız port/adapter ile **aşağı inilir**; K000 doğrudan donanıma erişmez, erişim K002'de |
| Üst katmanlar K001-K020 | gelen çağrı | Üst katmanlar K000'i port/adapter üzerinden çağırır (yön: yukarı → aşağı) |
| K005 (veri) | hizmet çağrısı | kalıcılık K005'te; K000 kendi durumunu kalıcı DB'ye **yazmaz** |
| K006 (güvenlik) | politika sorgusu | K000 yetki **kararı üretmez**; sorgular |
| K012 (gözlem) | olay akışı | K000 yalnız taşıyıcı; kural K012'de |

**Yön kuralı (R6.1):** izinli = **alt katmanlar** (daha düşük K-ID) + port/adapter.
K000 için alt katman kümesi **boştur** → somut izin, yalnız *gelen* çağrılardır.

**Kesişim testi:** IZINLI (K000 altı = ∅) ∩ YASAK (K001-K020) = **∅** → R4.4(b) GEÇTİ.

---

## §3 YASAK Bağımlılıklar (R6.1 · F1 H19/H20)

| # | Yasak | Kaynak | Sonuç / Yaptırım |
|---|---|---|---|
| 1 | K001-K020 katmanlarına **doğrudan erişim** | anayasa satır 71 · R6.1 | Layer Violation → **derhal revert + log CRITICAL** (`.ai/CLAUDE.md` satır 186) |
| 2 | Aşağı katmandan yukarı **geri çağrı** (H20) | F1 §5.1 H20 · R6.1 | Yalnız port/adapter — bağımlılık inversiyonu ihlali |
| 3 | Komşu katmanla **doğrudan DB/veri paylaşımı** (H19) | F1 §5.1 H19 · YARGI 2 | data boundary ihlali |
| 4 | L0 → L2/L3 | `.ai/CLAUDE.md` satır 182 | katman ihlali — L0 için açıkça yasaklanmış tek satır |
| 5 | Web/UI katmanının doğrudan donanım katmanına erişimi | F1 §8.5 | K000 üzerinden değil, **K002 üzerinden** |
| 6 | Servisler arası senkron doğrudan çağrı | `.ai/CLAUDE.md` §5 K8 | olay (event) sınırı ile — K000'e de uygulanır |
| 7 | K006 bypass mekanizması tanımlamak | `.ai/CLAUDE.md` §5 K6 ("Asla bypass edilemez") | K000'de bypass **tanımlanamaz** |

### 3.1 Olay (event) yukarı serbest — senkron çağrı yukarı yasak

R6.2 gereği K000'den üst katmanlara **olay yayını serbesttir**; senkron çağrı
yukarı yasaktır. Örnekler (TASARIM · [DESIGN]):

| Olay | Üretici | Tüketici | Not |
|---|---|---|---|
| cihaz bağlanma/çıkarma | K000 (enumerasyon) | K002 · K001 | USB/BT yüzeyi K002'de |
| süreç çöküşü | K000 (process table) | K012 · K008 | fail-over tetikleyicisi |
| boot/sağlık durumu | K000 (health) | K012 | alert eşlemesi K012'de |
| konteyner health değişimi | K000 (container runtime) | K013 | PLANNED (repo 0 dosya) |

---

## §4 DATA_BOUNDARY — Çekirdek Veri Sınırı (YARGI 2)

| Konu | Kural |
|---|---|
| Sınır adı | çekirdek veri sınırı (anayasa satır 71) |
| K000'in verisi | süreç tablosu, dosya tanıtıcıları, soket durumu, cihaz envanteri — **üst katman tablolarıyla paylaşılmaz** |
| Üst katmanın verisi | medya/kişi/çalma listesi tabloları **K005'e aittir**; K000 o tabloları okumaz/yazmaz |
| Paylaşılan tablo | **YOK** — aynı veri sınırı iki katmandaysa YARGI ihlali (R4.4d) |
| Kalıcılık | K000 kendi durumunu kalıcı DB'ye **yazmaz**; kalıcılık K005'tedir |
| Geçiş biçimi | yalnız taşıma/alan (payload) — **tablo şeması değil** |
| Denetim | `dep-check` (R6.6) + kod gözden geçirmede tablo paylaşımı taraması |

**Örnek ihlal (ne yapılmaz):** K000 kodunun `media_library` tablosunu SELECT
etmesi → H19 + YARGI 2 çift ihlali → revert + CRITICAL log.

---

## §5 SECURITY_BOUNDARY (EK A: security=ORTA)

| Konu | Değer |
|---|---|
| Seviye | **ORTA** (anayasa satır 71) — K006 YÜKSEK'tir; K000 onun altında kalmaz, **karar üretmez** |
| OWASP eşlemesi | **A02:2025** Security Misconfiguration (boot/konteyner yapılandırması) · **A08:2025** Software/Data Integrity (imzalı asset/firmware) — F1 §9.1 |
| İlgili kural | **F1 SEC15**: firmware/asset güncellemesi imzalı; doğrulanmadan çalıştırılmaz |
| Devre dışı bırakılamaz | K006 bypass'ı (`Asla bypass edilemez`) — K000'de bypass mekanizması tanımlanamaz |
| Yetki | K000 yetki kararı **üretmez**; yetki K006'nın alanıdır |
| Giriş doğrulama | syscall/girdi doğrulama **politikası** K006/K007'de; K000 yalnız sistem çağrısı geçişi |
| Denetim izi | yetki değişim kaydı K012/K006'da (F1 SEC11 · OWASP A09) |
| Sırlar | K000 yapılandırmasında hardcoded credential **YOK** (F1 H04 · SEC06) |

**İlişki:** security=ORTA, "düşük güvenlik" demek değildir; **güvenlik
kararı**nın K000'de olmadığını, K006'ya **arayüz** verdiğini söyler (F1 §9.1
eşlemesi: A02 → boot/konteyner yapılandırması, A08 → imzalı asset).

---

## §6 FAILURE_MODE (EK A: fail-over)

| Senaryo | Davranış | Durum |
|---|---|---|
| Süreç çöküşü | yeniden başlatma / devralma | DESIGN |
| Kernel/servis hatası | sistem yeniden başlatma (**fail-over**) | DESIGN |
| Disk/bellek yetersizliği | üst katmanlara **durum kodu** (exception değil) | DESIGN |
| fail-open senaryosu | **YOK** — fail-open yalnız belgelenmiş ve ADR'li senaryoda (F1 SEC14) | — |
| Veri bütünlüğü kaybı | K000'in sorumluluğu **değil**; kurtarma K005 (backup) | sınır |
| Konteyner sağlık kaybı | konteyner restart → health kontrol (Docker) | PLANNED (repo 0) |
| Cihaz kaybı (ASIO vb.) | fallback zinciri K002'de (ör. WASAPI fallback) | K002'ye devir |

**Zorunlu ayrım:** `fail-secure` (varsayılan) ≠ `fail-open` (yalnız ADR) ≠
`fail-over` (yeniden başlatma) — K000'in EK A değeri **fail-over**'dır;
başka değer yazmak ADR gerektirir (F1 EK C · H08).

---

## §7 Observability / Test Sınırı

K000 yalnız **taşıyıcıdır**:

| Alan | K000'in rolü | Sahip (canonical owner) |
|---|---|---|
| Log | çekirdek günlüğü üretir/taşır | **K012** (R7.1 tek owner) |
| Metric | süreç/cpu/bellek sayaçlarını okur | **K012** — metrik adları birlikte tanımlanacak [PLANNED] |
| Trace | syscall zinciri olayı | **K012** |
| Alert | — (eşik kararını vermez) | **K012** |
| Test stratejisi | kendi test kapısını tanımlar | **K013 CI/CD** (çapraz-katman) |
| Test kapsamı | backend/frontend/audio/download testleri bu katmanın **üstünde** koşar | `.ai/CLAUDE.md` §17 (≥80% hedef) |

---

## §8 Port/Adapter Deseni (F1 §8.2 — K000'e uygulaması)

| Port tipi | Yön | Örnek (tasarım) | Adapter | Durum |
|---|---|---|---|---|
| Inbound (gelen) | üst katman → K000 | süreç başlatma / dosya açma portu | üst katmanın kendi adapter'ı | DESIGN |
| Outbound (giden) | K000 → donanım/çekirdek | cihaz erişim portu (asıl erişim K002'de) | K002 sürücü adapter'ı | DESIGN |
| Kural | — | adapter değiştirilirse **K000'in sözleşmesi değişmez** | F1 §8.2 | bağlayıcı |
| Kural | — | **iç halka dış halkanın varlığını bilemez** (F1 §8.1 temiz mimari) | F1 §8.1 | bağlayıcı |
| Sınır | — | K000'in port'u başka bir katmanın DB'sine bağlanamaz (H19) | F1 H19 | bağlayıcı |

**Bağımlılık yönü (F1 §8.1):** `Web/UI ──▶ Application ──▶ Domain ◀── Infrastructure`;
Domain hiçbir katmana bağımlı değildir. K000 bu şemada **en dış/zemin**
alkanıdır: iç halka (Domain/K010) K000'i **bilmez**, portu üzerinden konuşur.

---

## §9 Sınır İhlali Denetim ve Yaptırım Akışı (R6.5/R6.6 · anayasa §5.1)

| Adım | Aksiyon | Kaynak |
|---|---|---|
| 1 | İhlal tespiti: K000 → K003 gibi doğrudan üst erişim (port/adapter'sız) | R6.1 · §3 |
| 2 | **Derhal revert** | `.ai/CLAUDE.md` satır 186 ("Layer Violation İhlali: derhal revert + log CRITICAL") |
| 3 | `log.md`'ye **CRITICAL** girişi (append-only) | anayasa §5.1 · J5 |
| 4 | 👤 bilgi — insan onayı olmadan düzeltme yapılmaz | R6.5 |
| 5 | Döngü (circular) varsa: `dep-check` **exit 1**; en zayıf kenar ADR'a taşınır | R6.6 |
| 6 | Kardeş ilişki `refers-to` olarak düzeltilir (K-ID düz metin) | R6.4 |

**Sıfır tolerans:** döngü toleransı sıfırdır; tek elle çözüm yok — kenar ADR'a
taşınır (R6.6).

---

## §10 F1 §8.4 YARGI'larının K000'e Uygulaması

| Yargı | Kural (F1 §8.4) | K000 uygulaması | Durum |
|---|---|---|---|
| YARGI 1 | Her katman yalnız ALTINDAKİ katmanı tanır | K000'in altı **yok** → hiçbir K katmanını çağıramaz; yalnız port/adapter ile aşağı inilir | GEÇERLİ (izinli=—) |
| YARGI 2 | Her katmanın DATA BOUNDARY'si vardır; tablo paylaşımı yasak | çekirdek veri sınırı (§4) | GEÇERLİ |
| YARGI 3 | Her katmanın SECURITY BOUNDARY'si vardır | security=ORTA + A02/A08 (§5) | GEÇERLİ |
| YARGI 4 | Her katmanın OBSERVABILITY alanı vardır | süreç/cpu/bellek · syscall hata · boot health [PLANNED] | TASARIM |
| YARGI 5 | Her katmanın FAILURE_MODE'u tanımlıdır | fail-over (§6) | GEÇERLİ |
| YARGI 6 | İletişim servis çağrısı yerine EVENT BOUNDARY ile tercih edilir | K000 → üst katmanlara olay yayını serbest (R6.2 · §3.1) | GEÇERLİ (kural) |

---

## §11 Komşu Katman İlişki Matrisi (düz metin K-ID — wiki-link YASAK)

| Yön | K-ID | İlişki | Not |
|---|---|---|---|
| Bu katman | K000 | kök (izinli = —) | alt katman yok |
| Üst (band-1 kardeş) | K001…K020 | `refers-to` (doküman) — **`depends-on` DEĞİL** | R6.4 |
| Fiziksel uçak | K016-K020 | `refers-to` | K000 SOFTWARE'da kalır (F1 §8.5) |
| Doğrudan üst **veri sahibi** | K005 | hizmet çağrısı + olay | H19: paylaşılan tablo yok |
| **Güvenlik** üst otoritesi | K006 | politika sorgusu | bypass yok (§5) |
| **Gözlem** üst otoritesi | K012 | olay akışı | K000 yalnız taşıyıcı (§7) |
| **Sürücü** kesişimi | K002 | port/adapter | donanım-yazılım kesişimi yalnız K002 (F1 §8.5) |
| **Çağrı** üst katmanları | K007-K015 | gelen port | senkron yukarı yok |

---

## §12 Açık Sınır Maddeleri

| # | Açık madde | Etki | Aksiyon |
|---|---|---|---|
| 1 | Port/adapter arayüzlerinin hiçbiri yazılmadı (repo'da K000 dosyası yok) | §8 tamamı [DESIGN] | R14 research + üretim kapısında kod kanıtı |
| 2 | Cross-Platform API'nin sınır tanımı yok | §2/§8 arası boşluk | K000 derinleşme dosyası gerekebilir (R3.1 koşulu) |
| 3 | Olay katalogları (event şeması) tanımsız | §3.1 [DESIGN] | event sözleşmesi K012/K014 ile birlikte |
| 4 | fail-over senaryoları ADR'li değil | §6 tamamı [DESIGN] | fail-open/use-case için ADR zorunlu (F1 SEC14) |

### 12.1 Sınır karar ağacı (yeni bir bağımlılık eklerken)

```text
Yeni bağımlılık önerisi
  → Yön sorusu: aşağı mı iniyor? (K000 → üst katman)
      EVET → R6.1 ihlali — RED (olay yukarı serbest, bkz. §3.1)
  → Veri sorusu: K000 çekirdek verisi (queue/capstone/device-state) yazılıyor mu?
      EVET → DATA_BOUNDARY — port/adapter üzerinden DEĞİLSE RED (§4 · R4.4(d))
  → Güvenlik sorusu: security köprüsü / bypass içeriyor mu?
      EVET → SECURITY_BOUNDARY — K006 onayı olmadan RED (§5 · anayasa K6)
  → Başarısızlık sorusu: bu bağımlılık düşerse fail-open mı fail-closed mı?
      BİLİNMEYOR → [DESIGN] + ⚠️ VERIFICATION REQUIRED (§6 · F1 SEC14)
  → Hepsi temiz → §8 port/adapter sözleşmesine yaz · [DESIGN] etiketi
```

---

## §13 Referanslar

| # | Kaynak | Kullanım |
|---|---|---|
| 1 | `00-kspace-anayasa.md` satır 71 · §5.1 | sınır üçlüsü · ihlal kuralı |
| 2 | `.ai/CLAUDE.md` satır 182-186 (§5.1) · §5 K6/K8 | katman yönü · bypass yok · event sınırı |
| 3 | F1 §5.1 (H19/H20) · §8.1-8.5 · §9.1-9.3 · SEC14/SEC15 | yasaklar · temiz mimari · OWASP · fail-open · imza |
| 4 | `rules.md` R6 · R7.1 · R4.4(d) | bağımlılık yönü · tek owner · veri sınırı ihlali |

---

**Authority:** `index.md` (SSOT) — bu dosya derinlik katmanı, `ssot: false`
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode
