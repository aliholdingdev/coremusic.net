---
reference_doc: .ai/.templates/adr/adr-nygard-template.md (v1.0.1 — Guardrail #16)
title: "CoreMusic — K001 donanaım-type Tür Taksonomisi Genişlemesi: 3 → 9 Bağlam (display · power · cables · network · memory · cpu)"
type: adr
category: decisions
date: 2026-10-13
updated: 2026-10-13
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: "donanım-anayasa.md §10/3 · §12/C10 · §12.1 · donanaım-type-kapsam.md §2/A6 · §7/3-T1 · §12/M6"
---

# CoreMusic — K001 donanaım-type Tür Taksonomisi Genişlemesi (display+power önceki oturum · cables+network+memory+cpu bu oturum)

**Durum:** draft — kullanıcı onayı bekliyor (onay → `accepted/`, redd → `rejected/`)
**Tarih:** 2026-10-13
**Karar Veren:** Bayram Ali / Vault Steward
**İlgili ADR'ler:** ADR-017 (DSP hardware mode) · ADR-038 (XMOS XU316 + PCM3168A) · ADR-062 (donanım tipi sınıflandırma) · ADR-063 (donanım-envanter) · ADR-064 (electronics platform) · ADR-089 (Class AB) · ADR-097 (K-Space V3 — draft) · ADR-101 (güvenlik kanonik sırası — max accepted)
**İlgili Silinmiş:** ADR-061 (dosya diskte YOK — `rejected/` veya `accepted/` altında yok; bu ADR onun yerine geçmez, bağımsızdır)

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (kanıtlanmış)

`donanaım-type/` K001-donanım'ın kademe-2 tür sınıflandırma dalıdır. 2026-10-13
disk ölçümünde (bu oturum öncesi) **5 bağlı bağlam** taşır — 3 orijinal + 2
aynı günün önceki oturumunda açılan:

| # | Bağlam | Dosya sayısı | Durum |
|---|---|---|---|
| 1 | `audio/` | 7 (index + kapsam + dogrulama + ornekler + 3 cip) | draft/active |
| 2 | `hdd/` | 4 (index + kapsam + dogrulama + ornekler) | draft |
| 3 | `ssd/` | 4 (index + kapsam + dogrulama + ornekler) | draft |
| 4 | `display/` | 4 (index + kapsam önceki oturum · dogrulama + ornekler bu oturumda tamamlandı) | proposed |
| 5 | `power/` | 4 (index + kapsam + dogrulama + ornekler — Vault Steward onayıyla 2026-10-13 20:35 açıldı) | proposed |

> **Düzeltme kaydı (2026-10-13, bu oturum):** İlk taslak "3 bağlı bağlam" ve
> "power reddedildi" derdi — disk gerçeğiyle (power/ 4 dosya mevcut, anayasa §26
> sayımına girmemiş) hizalandı; zero-hallucination: disk > taslak varsayımı.
>
> **Keşif 2 (2026-10-13 22:07):** Doğrulama sırasında `gpu/` dalı diskte bulundu
> (3 dosya: index · kapsam · dogrulama) — 21:52–22:07
> arası, `power/`'ı açan paralel Vault Steward oturumu tarafından açılmış. **10. dal**
> = toplam 10 bağlam. `gpu/` bu ADR'nin KAPSAMI DIŞINDADIR (ayrı onay/revizyon
> gerekir — `gpu/index.md` Q-GI1 zaten "ADR-102 revizyonu APPROVAL REQUIRED" der).
> `donanaım-type/index.md`'ye children + §GRUPLAR olarak eklendi (disk gerçeği).
> **Ek (2026-10-13 22:55 sayımı):** `gpu-ornekler.md` 22:49'da paralel oturumca
> yazıldı (513 satır, §1–§12) → gpu 4/4 tamam; `donanaım-type/` toplamı 45 → **46 md**.
> gpu'nun ADR-102 kapsamına GİRMEMESİ karar değişikliği değildir (Q-GI1 açık).

### §1.2 Tetikleyici Belirsizlik

`donanım-anayasa.md` §10/3 açık soru olarak kayıtlıdır:

> `| 3 | donanaım-type altı 3 bağlam mı, fazlası mı? | genişleme | ⚠️ VERIFICATION REQUIRED |`

§12/C10 ise yapısal sınıf olarak "3 kademe-3 alt bağlam (audio/hdd/ssd)" der ve
**envanterle değişebilir** olduğunu, değişim için **ADR onayı** gerektiğini söyler
(§12.1). ELI15 (§0) ise fiziksel parçaları "disk, ses kartı, **ekran, kablolar**"
olarak anar — yani anayasa'nın kendi özeti 3 bağlamdan fazlasını ima eder.

### §1.3 Kullanıcı Onayı (2026-10-13)

Vault Steward bu oturumda **dağıtık onay** vermiştir:

1. `display/` klasörü açılmıştır (index + kapsam yazıldı — 2026-10-13, `status: proposed`).
2. `power/` klasörü aynı günün önceki oturumunda açılmıştır (4 dosya, 20:35,
   `status: proposed`, Vault Steward onayı — `power/index.md` validation satırı kanıttır).
3. Kalan 4 dalın (**cables · network · memory · cpu**) da açılması onaylanmıştır.
4. `donanım-anayasa.md` ve `donanaım-type-kapsam.md` revizyonu için **4 onaylı edit**
   tanımlanmıştır (bu ADR'nin §7'sine bakınız).

### §1.4 Repo Ölçümü (kanıt — uydurma yok)

`display/index.md` evidence satırı: 700 kaynak dosyada `EDID=0 · DDC=0 · HDMI=0 ·
DisplayPort=0 · VESA=0 · touchscreen/dokunmatik=19`. Bu, **repo'da ekran
teknolojisine dair kod/şema referansı OLMADIĞINI** kanıtlar — yani bu genişleme
**kod entegrasyonu değil, dokümantasyon/taksonomi genişlemesidir** (documentation
≠ physical production).

---

## §2 Karar Sürücüleri (Decision Drivers)

| # | Sürücü | Kaynak |
|---|---|---|
| D1 | Anayasa §10/3 açık sorusuna cevap vermek | donanım-anayasa.md §10/3 |
| D2 | ELI15'teki "ekran, kablolar" ifadesini yapıya bağlamak | donanım-anayasa.md §0 |
| D3 | K001 tür envanterinin gerçek donanım sınıflarını yansıtması | ADR-062 · ADR-063 |
| D4 | Güç kaynağı/PCB/BOM'un K001'de tutulmaması (sınır koruması) | ADR-064 · anayasa §2 |
| D5 | Zero-hallucination: envanter verisi olmayan türler UNKNOWN kalmalı | anayasa §10/4 · §23 |
| D6 | `donanaım-type` yazımının v2 kaynağına sadık kalması (düzeltme yasak) | v2 §3 KURAL 1 |
| D7 | Yapısal sınıf (C10) değişiminin ADR ile belgelenmesi | anayasa §12.1 |

---

## §3 Alternatifler (Alternatives Considered)

| # | Alternatif | Artıları | Eksileri | Sonuç |
|---|---|---|---|---|
| A1 | **Mevcut durumda kal** (audio · hdd · ssd · display · power) | Değişiklik yok | ELI15'le kısmen çelişir (kablolar); §10/3 açık soru açık kalır; ağ/bellek/işlemci sınıflandırmasız | **REDDEDİLDİ** |
| A2 | **4 yeni dal aç** (cables · network · memory · cpu) + display tamamlanır → toplam 9 | ELI15 uyumlu; §10/3 kapanır; gerçek sınıflandırma; K002 sınırı korunur | anayasa §12/C10 + kapsam §7.3 revizyonu gerekir (PROPOSED) | **SEÇİLDİ** |
| A3 | Ekranı `donanım-devices/` altına koy (cihaz envanteri) | Yeni dal açılmaz | `donanım-devices` = *birim* envanteri, *sınıf* değil; sınıf ayrımı bozulur (ADR-063) | **REDDEDİLDİ** |
| A4 | Yeni K-dominı aç (ör. K006-display) | Temiz sınır | K002 ID GAP zaten var; yeni GAP yaratır; anayasa §9 bağımlılık zinciri bozulur; kullanıcı yeni K-domaini onaylamadı | **REDDEDİLDİ** |
| A5 | Power/PCB **devre** sahipliği de K001'e ekle | Eksiksiz donanım | ADR-064 + anayasa §2: fiziksel PCB/BOM = HAYIR → K002-electroncis'e ait | **REDDEDİLDİ** (sınır koruması) — not: power **tip/enventar dalı** zaten açılmıştır (#5); reddedilen, devre/PCB/BOM'un K001'e taşınmasıdır |

---

## §4 Karar (Decision)

**`donanaım-type/` altına 4 yeni bağlı bağlam açılır ve `display/` tamamlanır.**
Toplam bağlam sayısı **5 → 9** olur (orijinal 3 + aynı günün önceki oturumunda
açılan display · power + bu oturumdaki 4 yeni dal):

| # | Bağlam | Kapsam (özet) | Grup |
|---|---|---|---|
| 4 | `display/` | Panel teknolojileri (LCD/OLED), GPU görüntü çıkışı, DP/HDMI arayüzleri, dokunmatik katman, ekran kontrolcüsü | ekran |
| 5 | `power/` | Güç kaynağı **sınıf/tip** envanteri (P-T01…P-T13) — önceki oturumda açıldı; devre/PCB/BOM K002'de kalır | güç |
| 6 | `cables/` | Fiziksel bağlantı kabloları: HDMI/DP/USB/ethernet/analogue ses kabloları, konnektör tipleri | bağlantı |
| 7 | `network/` | Ağ arayüz donanımı: Ethernet kontrolcüsü, Wi-Fi/BT kartları, ağ PHY'ları | ağ |
| 8 | `memory/` | Bellek donanımı: RAM (DDR/SO-DIMM), EEPROM/flash yongaları, önbellek depolama | bellek |
| 9 | `cpu/` | İşlemci: CPU/SoC, XMOS XU316 (ADR-038), gömülü işlemci çekirdekleri | işlemci |

### §4.1 Bağlam Bazlı Sınır Kuralları

| Bağlam | K001'e AİT | K001'e AİT DEĞİL |
|---|---|---|
| display | Panel + arayüz + dokunmatik **sınıfı** | GPU'nun hesap çekirdeği (K004-dsp) · güç kaynağı (K002) |
| power | Güç kaynağı **sınıf/tip** envanteri | Devre/PCB/BOM/güç hesabı (K002-electroncis) |
| cables | Konnektör + kablo **sınıfı** | Fiziksel PCB yolu (K002) · sinyal bütünlüğü ölçümü (K002) |
| network | Ağ PHY/kart **sınıfı** | Ağ protokol yığını (K000) · driver (K000) |
| memory | Bellek yongası **sınıfı** | Bellek yönetim algoritması (K000) |
| cpu | İşlemci **sınıfı** | DSP algoritması (K004) · firmware (K005) |

### §4.2 Değişmez Kısıtlar (Constraint Preservation)

| Kısıt | Durum |
|---|---|
| ADR-038: XMOS XU316 + PCM3168A | **DEĞİŞMEZ** — cpu dalı yalnız envanter düzeyinde referans verir |
| PCM5122 kullanımı yasak (anayasa §17/E8) | **DEĞİŞMEZ** — audio dalı kazanır |
| ADR-089: Class AB | **DEĞİŞMEZ** — güç kaynağı K001'de değil |
| ADR-064: Elektronik platform | **DEĞİŞMEZ** — power/PCB/BOM → `K002-electroncis` cross-ref |
| K002 ID GAP (K002 elektroncis mevcut, `architecture/index.md` §6 hâlâ "K002 yok" der) | **KENDİNE ÇÖZÜLMEZ** — çelişki raporlanır (bkz. §6) |

---

## §5 Sonuçlar (Consequences)

### §5.1 Olumlu

- §10/3 açık sorusu **cevaplanır** (3 değil, 9 bağlam).
- ELI15'teki "ekran, kablolar" ifadesi **yapıya bağlanır**.
- 4 yeni dal, gerçek donanım sınıflandırmasını yansıtır (ADR-062/063 uyumlu).
- Güç kaynağı/PCB sınırı **korunur** (ADR-064).

### §5.2 Olumsuz / Riskli

- `donanım-anayasa.md` §12/C10 ve `donanaım-type-kapsam.md` §7/3-T1, §12/M6
  revizyonu **gerekir** — bu, Vault Steward onaylı **4 edit** ile yapılır.
- Tüm yeni dosyalar `status: proposed` taşır; `active`/`draft`a geçiş ayrı onay ister.
- Eski "yalnız 3 bağlam" ifadeleri (kapsam §7.3 T1/T2, dogrulama §34) **stale
  citation** olarak işaretlenir — doğrudan silinmez, `PROPOSED` etiketiyle
  revize edilir (ADR-042: satır-satır edit, silme yok).

### §5.3 Nötr

- `donanaım-type/index.md` §GRUPLAR tablosuna 4 satır eklenir (orchestrator yapar;
  display · power satırları önceki oturumda eklenmişti).
- `display/` klasöründe eksik `display-dogrulama.md` ve `display-ornekler.md`
  tamamlanır.
- Tüm yeni dosyalar `group:` alanını taşır (mimari/kanit/kod).

---

## §6 Bilinen Çelişkiler (gizleme yok)

| # | Çelişki | Durum |
|---|---|---|
| C1 | `architecture/index.md` §6: "K002 hiç yok" — ama `K002-electroncis/` diskte (16 dosya) | **ÇÖZÜLMEZ** (bu ADR kapsamı dışı — Vault Steward kararı gerekir) |
| C2 | `K000-isletim-sistemi/index.md` `depends_on: [K002-altyapi]` — diskte `K002-electroncis` var (isim uyuşmazlığı) | **ÇÖZÜLMEZ** (raporlanır) |
| C3 | `K002-electroncis/index.md` 5 çocuk bildirir, diskte 3 dizin var | **ÇÖZÜLMEZ** (raporlanır) |
| C4 | `donanım-devices/index.md` §44: "donanaım-type = sınıf (audio · hdd · ssd · display · power)" — bu oturumdan sonra 9 bağlam oldu (cables/network/memory/cpu eklendi) → devices sınır listesi artık stale | **Bu ADR ile düzeltilmez** (devices dosyası ayrı edit ister) |
| C5 | `display/index.md` "5. alt bağlam" der — bağlam sayısı 2026-10-13'te 10 oldu (display yine 4. sırada) | **Yazım hatası** — raporlanır, düzeltilmez |
| C6 | Eski "yalnız 3 bağlam" citation'ları (kapsam §7.3, dogrulama §34) | **4 edit ile PROPOSED işaretlenir** |

---

## §7 Uygulama (Implementation)

| # | Adım | Sorumlu | Durum |
|---|---|---|---|
| 1 | `display/index.md` + `display-kapsam.md` yazımı | önceki oturum | ✓ TAMAM (2026-10-13, 20:22) |
| 1b | `power/` (4 dosya) yazımı — ADR-102 kapsamı öncesi, Vault Steward onayı | önceki oturum | ✓ TAMAM (2026-10-13, 20:35) |
| 2 | `display-dogrulama.md` + `display-ornekler.md` tamamlama | docs-writer subagent | ✓ TAMAM (26.669 + 20.369 bayt) |
| 3 | `cables/` (4 dosya) | docs-writer subagent | ✓ TAMAM (4 dosya, toplam ~100 KB) |
| 4 | `network/` (4 dosya, 1.970 satır) | docs-writer subagent | ✓ TAMAM |
| 5 | `memory/` (4 dosya, 1.290 satır) | docs-writer subagent | ✓ TAMAM |
| 6 | `cpu/` (4 dosya, ~85 KB) | docs-writer subagent | ✓ TAMAM (rate-limit dosyalama SONRASI — dosyalar tam) |
| 7 | `donanaım-type/index.md` children + §GRUPLAR güncelleme | orchestrator | BU ADR ile |
| 8 | `donanım-anayasa.md` §10/3 · §12/C10 · §26 revizyonu | orchestrator (PROPOSED) | BU ADR ile |
| 9 | `donanaım-type-kapsam.md` §7.3 stale-citation düzeltmesi | orchestrator (PROPOSED) | BU ADR ile |
| 10 | `.ai/.decisions/index.md` §4A'ya ADR-102 satırı | orchestrator | BU ADR ile |

**Toplam yeni dosya (bu oturum):** 16 (cables 4 + network 4 + memory 4 + cpu 4) +
display 2 tamamlama = 18 · power 4 = önceki oturum (toplam genişleme 22)
**Toplam rapor:** 10 (`.ai/reports/k001-taksonomi/` altında)

---

## §8 İlişkili Dosyalar

| Dosya | İlişki |
|---|---|
| `.ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md` | Bu ADR |
| `donanım-anayasa.md` | §10/3 · §12/C10 · §12.1 · §26 |
| `donanaım-type/index.md` | children + §GRUPLAR |
| `donanaım-type-kapsam.md` | §2/A6 · §7/3-T1 · §12/M6 (stale citation) |
| `donanaım-type/display/index.md` | 4. dal künyesi (status: proposed) |
| `donanaım-type/power/index.md` | 5. dal künyesi (status: proposed — önceki oturum) |
| `.ai/reports/k001-taksonomi/` | 10 rapor dosyası |
| `donanım-devices/index.md` | Sınır: sınıf ≠ birim (ADR-063) |

---

**Authority:** Bayram Ali / Vault Steward
**Mode:** Red Team · Human Mode · Truth Mode
