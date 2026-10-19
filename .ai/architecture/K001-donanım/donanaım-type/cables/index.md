---
title: "donanaım-type/cables — Kablo, Konnektör ve Arayüz Sınıfı Künyesi"
type: architecture
node_id: K001-donanım/donanaım-type/cables
node_type: bounded-context
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - cables-kapsam.md
  - cables-dogrulama.md
  - cables-ornekler.md
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md §4 (cables dalı: HDMI/DP/USB/ethernet/analogue ses kabloları + konnektör tipleri) · §4.1 (K001 = konnektör + kablo sınıfı; K002 = PCB yolu + sinyal bütünlüğü; K000 = protokol/sürücü) · §4.2 (ADR-038/089/064 DEĞİŞMEZ) — 2026-10-13 eklendi (orkestratör patch, Q-CK4)"
  - "Vault Steward 2026-10-13 dağıtık onayı — klasör açımı bu oturumda YAPILDI"
  - "donanım-anayasa.md §10/3 (satır 195) · §12/C10 (satır 250) · §2 (satır 58) — yalnız 3 bağlam, yapısal sınıf"
  - "donanaım-type-kapsam.md §2/A6 (satır 46) · §7/3-T1 (135) · §7/3-T2 (136) · §12/M6 (284) · §7.3/X3 (428) — kablo bağlamı AÇILMAZ (revizyon: APPROVAL REQUIRED)"
  - "Repo ölçümü 2026-10-13 (.ai/architecture, 911 md, kelime-sınırlı, büyük-küçük harf duyarlı): USB=258 · I2S=183 · I2C=147 · GPIO=109 · SPI=99 · HDMI=67 · cache alakasız · SATA=38 · PCIe=23 · DisplayPort=27 · UART=30 · USB Type-C=0"
  - "HEAD 2026-10-13: usb.org 200 · sata-io.org 200 · displayport.org 200 · hdmi.org 200 · docs.kernel.org/i2c 200 · spi-summary 200 · tty 200 · gpio 200 · PCI 200"
  - "HEAD 2026-10-13 reddedilenler: jedec.org 403 · pcisig.com 403 (görev notu: SSL hatası) · nxp.com 404 · aes.org 403 → KULLANILMADI"
validation: "proposed — 2026-10-13 yazıldı; anayasa ve üst kapsam metni revize EDİLMEDİ (PROPOSED — APPROVAL REQUIRED); D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Q-C1: Kablo/konnektör fiziksel envanteri (marka · kesit · uzunluk · pin sayısı) bilinmiyor — UNKNOWN (K001 Q4)"
  - "Q-C2: anayasa §10/3 + §12/C10 ve kapsam §2/A6 · §7/3-T1-T2 metin revizyonu onayı alınmadı — status: proposed kalır"
  - "Q-C3: PCIe spec (pcisig.com) ve JEDEC/I2C resmi sürümü bu oturumda doğrulanamadı — ⚠️ VERIFICATION REQUIRED (403)"
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

# K001-donanım/donanaım-type/cables — ELI15

Kablo, konektör ve arayüz sınıfı: bilgisayar ile çevre birimi, kart ile kart
veya çip ile çip arasındaki **fiziksel taşıma yüzeyi**. Dört katmanı ayrı
modellenir — kablo (ne taşır) · konektör (nereden geçer) · elektriksel arabirim
(nasıl ölçülür) · protokol (paket kimi ilgilendirir). K001 tür envanterinin
6. alt bağlamıdır (display · power sonrası 3. yeni bağlam — bu tur).

> ⚠️ **PROPOSED — APPROVAL REQUIRED:** Bu klasör Vault Steward 2026-10-13 tarihli
> açık onayı ile açılmıştır; ancak `donanım-anayasa.md` (§10/3 · §12/C10 · §2) ve
> `donanaım-type-kapsam.md` (§2/A6 · §7/3-T1 · §7/3-T2 · §12/M6 · §7.3/X3)
> metinleri hâlâ "yalnız 3 bağlam" ve **"ekran · kablo · güç kaynağı bağlamı
> AÇILMAZ"** der — yasağın adında doğrudan **kablo** geçer. **Metin revizyonu
> yapılmadı** → tüm bu dosya seti `status: proposed` taşır; kapanış anayasa +
> üst kapsam revizyonuyla olur. Ayrıntı: `cables-kapsam.md` §0.

> yazım notu: `donanaım-type` yazımı v2 kaynağına sadıktır — **düzeltme yasak**
> (v2 §3 KURAL 1; `donanaım-type/index.md` yazım notu). Klasör adı bu yazımı taşır.

## Durum

| Alan | Değer |
|---|---|
| node_type | bounded-context |
| parent | K001-donanım/donanaım-type |
| children | `cables-kapsam.md` · `cables-dogrulama.md` · `cables-ornekler.md` |
| status | proposed (anayasa revizyonu bekleniyor) |
| Kapsam | Tip/envanter sınıflandırması — kablo ↔ konektör ↔ arayüz ↔ protokol AYRI |
| Envanter | UNKNOWN (Q-C1) — fiziksel kablo/konnektör listesi yok |
| Ölçüm | 0 (bu oturumda fiziksel ölçüm YAPILMADI — kapılar `cables-dogrulama.md` §3) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `cables-kapsam.md` | Çelişki kaydı · 4 katman kuralı · **17 tip kaydı** (26 alan — C-T01…C-T17; ADR-102 §4 farkı için Q-CK7 notu) | Kademe-3 |
| kanit | `cables-dogrulama.md` | Kablo kapıları K-C1…K-C8 · HEAD-200 kanıt matrisi · envanter | Kademe-3 |
| kod | `cables-ornekler.md` | Katman ayrım · pin/katman çalışma sayfaları · hata akışı şablonları | Kademe-3 |

> **Index children/§GRUPLAR bağlantısı:** `donanaım-type/index.md` ve
> `K001-donanım/index.md` satırları **bu subagent tarafından DOKUNULMADI** —
> children/§GRUPLAR ekleme işi ORKESTRATÖR'e aittir (toplu yazım).

> **Q-CK4/Q-CK7 kapsam-notu (2026-10-13 — orkestratör patch; SON DURUM):**
> Bu klasörde **iki ayrı okuma** yazıldı, ikisi de `status: proposed` taşıyor:
> (1) `cables-kapsam.md` (sibling yazımı, disk'teki güncel sürüm) **17 tip**
> kaydı uygular — C-T01 USB-A · C-T02 USB-C · C-T03 PCIe · C-T04 SATA ·
> C-T05 I2S/TDM · C-T06 SPI · C-T07 I2C · C-T08 UART · C-T09 GPIO · C-T10
> DisplayPort · C-T11 HDMI · C-T12 koaksiyel · C-T13 XLR · C-T14 RCA ·
> C-T15 güç konnektörleri · C-T16 konnektör sınıfları · C-T17 adaptör
> (kapsam §2 · §5). (2) ADR-102 §4 literal okuması yalnız "HDMI/DP/USB/
> ethernet/analogue ses kabloları + konnektör tipleri" sayar. **Fark:**
> ethernet/RJ45 kablo+konnektör kaydı sibling sürümünde YOK · I2C/SPI/UART/
> GPIO = kart-arası kablo olarak kayıtlı (K002 sınır sorusu — sibling Q-C7).
> Bu iki okuma **çözülmedi**; arbitration ORKESTRATÖR'e aittir (Q-CK7 ·
> `cables-dogrulama.md` §3.2 · §13.9/9). Not: bu subagent'in ilk patch'i
> §GRUPLAR'ı "8 tip"e çekmişti — sibling sürümü disk'e önce/sonra yazıldı,
> index disk gerçeğine (17 tip) hizalandı; silme yapılmadı.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
