---
title: "donanaım-type/network — Ağ Donanımı Sınıfı Künyesi"
type: architecture
node_id: K001-donanım/donanaım-type/network
node_type: bounded-context
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - network-kapsam.md
  - network-dogrulama.md
  - network-ornekler.md
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Vault Steward 2026-10-13 dağıtık onayı — klasör açımı bu oturumda YAPILDI"
  - "donanım-anayasa.md §10/3 (satır 195) · §12/C10 (satır 250) · §2 (satır 58) — yalnız 3 bağlam, yapısal sınıf"
  - "donanaım-type-kapsam.md §2/A6 (satır 46) · §7/3-T1 (135) · §12/M6 (284) — ağ donanımı açıkça sayılmadı (revizyon: APPROVAL REQUIRED)"
  - "Repo ölçümü 2026-10-13 (.ai/architecture, 911 md, kelime-sınırlı, büyük-küçük harf duyarlı): Bluetooth=29 · NIC=5 · Wi-Fi=4 · Ethernet=2 · CAN=0 · SFP=0 · modem=0 · transceiver=0"
  - "HEAD 2026-10-13: ieee.org 202 · wi-fi.org 200 · ieee802.org 200 · ethernetalliance.org 200 · bluetooth.com 200 · can-cia.org 200 · bosch.com 200 · 3gpp.org 200 · zephyrproject.org 200 · linuxfoundation.org 200 · docs.kernel.org/networking 200"
  - "HEAD 2026-10-13 reddedilenler: gsma.com 403 · microchip.com 403 · nxp.com 404 · st.com timeout → KULLANILMADI"
validation: "proposed — 2026-10-13 yazıldı; anayasa ve üst kapsam metni revize EDİLMEDİ (PROPOSED — APPROVAL REQUIRED); D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Q-N1: ağ donanımı envanteri (marka · model · MAC · port) bilinmiyor — UNKNOWN (K001 Q4)"
  - "Q-N2: anayasa §10/3 + §12/C10 ve kapsam §2/A6 · §7/3-T1 metin revizyonu onayı alınmadı — status: proposed kalır"
  - "Q-N3: gsma.com 403 — hüceler/3GPP detayları doğrulanamadı ⚠️ VERIFICATION REQUIRED"
  - "Q-N4: L-sınıfı (endüstriyel/embedded — CAN, seri kontrolcü) sahipliği UNKNOWN — K001 mi K003 mü?"
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

# K001-donanım/donanaım-type/network — ELI15

Ağ donanımı sınıfı: bilgisayarın **fiziksel ağ erişim yüzeyi** — Ethernet
denetleyicisi · Wi-Fi · Bluetooth · hücresel modem · NIC · transceiver · SFP ·
CAN/indüstriyel · seri kontrolcüler. K001 tür envanterinin 7. alt bağlamıdır
(display · power · cables sonrası 4. yeni bağlam — bu tur).

> ⚠️ **PROPOSED — APPROVAL REQUIRED:** Bu klasör Vault Steward 2026-10-13 tarihli
> açık onayı ile açılmıştır; ancak `donanım-anayasa.md` (§10/3 · §12/C10 · §2) ve
> `donanaım-type-kapsam.md` (§2/A6 · §7/3-T1 · §12/M6) metinleri hâlâ "yalnız 3
> bağlam" der. **Metin revizyonu yapılmadı** → tüm dosya seti `status: proposed`
> taşır; kapanış anayasa + üst kapsam revizyonuyla olur. Ayrıntı:
> `network-kapsam.md` §0.

> yazım notu: `donanaım-type` yazımı v2 kaynağına sadıktır — **düzeltme yasak**
> (v2 §3 KURAL 1). Klasör adı bu yazımı taşır.

## Durum

| Alan | Değer |
|---|---|
| node_type | bounded-context |
| parent | K001-donanım/donanaım-type |
| children | `network-kapsam.md` · `network-dogrulama.md` · `network-ornekler.md` |
| status | proposed (anayasa revizyonu bekleniyor) |
| Kapsam | Tip/envanter sınıflandırması — fiziksel ağ donanımı (akış servisi AYRI) |
| Envanter | UNKNOWN (Q-N1) — ağ donanımı listesi yok |
| Ölçüm | 0 (bu oturumda fiziksel ölçüm YAPILMADI — kapılar `network-dogrulama.md` §3) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `network-kapsam.md` | Çelişki kaydı · akış-vs-fiziksel ayrımı · 10 tip kaydı (26 alan) | Kademe-3 |
| kanit | `network-dogrulama.md` | Ağ kapıları K-N1…K-N8 · HEAD-200 kanıt matrisi · envanter | Kademe-3 |
| kod | `network-ornekler.md` | Ayrım örnekleri · pin/port çalışma sayfaları · şablonlar | Kademe-3 |

> **Index children/§GRUPLAR bağlantısı:** `donanaım-type/index.md` ve
> `K001-donanım/index.md` satırları **bu subagent tarafından DOKUNULMADI** —
> children/§GRUPLAR ekleme işi ORKESTRATÖR'e aittir (toplu yazım).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode