---
node_id: donanım-kapsam
node_type: subdomain
status: draft
parent_id: K001-donanım
children: []
depends_on: [K000-isletim-sistemi]
evidence:
  - ".ai/architecture/K001-donanım/donanım-anayasa.md §2 Kapsam Yetki Sınırı (10 satır) · 2026-10-18"
  - ".ai/architecture/K001-donanım/index.md §GENEL BAKIŞ + Çocuk kataloğu (5 kademe-2, 10 kademe-3) · 2026-10-18"
  - ".ai/architecture/K001-donanım/donanım-bagimliliklar.md §2 (Giden: K000 · K005) · 2026-10-18"
validation: "Her yüzey tek satırda EVET/HAYIR + karar mercii ile belirlenmeli; anayasa §2 ile birebir örtüşmeli; yeni satır yalnız anayasa revizyonuyla eklenebilir."
open_questions:
  - "⚠️ `architecture/index.md` §6 hâlâ 'K001 ile K003 arasında K002 yok (ID GAP)' der — `K002-electroncis/` diskte VAR (16 md) → çelişki C1, Vault Steward kararı bekliyor (ADR-102 §6)."
  - "⚠️ 7 yeni tür (display · power · cables · network · memory · cpu · gpu) anayasa §2'ye eklenmedi — anayasa §2/§10 metin revizyonu PROPOSED (onay bekliyor)."
  - "⚠️ TPA3255 PVDD aralığı: görev iddiası 24–52 V, TI datasheet 18–53.5 V → ⚠️ VERIFICATION REQUIRED."
  - "⚠️ Ses donanımı envanteri K001 mi K003 mü? (anayasa §10 #5) → VERIFICATION REQUIRED."
group: mimari
---

# K001 — Kapsam (Sınır Tanımı)

## §1 ELI15

K001 = kasadaki fiziksel parçaların vault karşılığı. Bu dosya **neyin
K001'de olduğunu** ve **neyin kesinlikle başka K'da olduğunu** tek tek
satırda söyler. Sınır, "acaba" ile değil, karar mercii ile çizilir:
kararsız kaldığında bu dosya + `donanım-anayasa.md` §2 kazanır.

## §2 Kapsam İçi (K001'de — EVET)

| Yüzey | Karar mercii | Not |
|---|---|---|
| Sürücü seviyesi donanım soyutlama | K001 | ana iş (anayasa §2) |
| Cihaz tipi sınıflandırma | K001/donanaım-type | yazım **dokunulmaz** (v2 §3 KURAL 1) |
| Donanım erişim API'si | K001/donanım-api | keşif · komut · durum · olay |
| Kernel-üstü arayüz | K001/donanım-kernel | çekirdeğe en yakın rol |
| Depolama cihazı envanteri | K001/donanaım-type/hdd · ssd | kademe-3 |
| Cihaz tespiti ve kayıt | K001/donanım-devices | IMPLEMENTED: `DeviceDetector.php` · `DeviceManager.php` |
| DAC/codec/amplifier envanteri | K001/donanaım-type/audio/cipler | PCM3168A · AK4458 · TPA3255 |

## §3 Kapsam Dışı (K001'de DEĞİL — HAYIR)

| Yüzey | Karar mercii | Gerekçe |
|---|---|---|
| Çekirdek sürücü kodu (ring-0) | K000-isletim-sistemi | yalnız sınır (anayasa §2) |
| Cihaz firmware'i / MCU yüklemesi | K005-firmware | XMOS · DSP zinciri |
| Ses işleme zinciri | K003-audio | K001 yalnız cihazı sunar |
| DSP algoritması | K004-dsp | komşu |
| Fiziksel PCB / BOM / kart tasarımı | K002-electroncis | sınır KAPANDI 2026-10-15 (index §GENEL BAKIŞ) |
| Güç kaynağı tasarımı | K002-electroncis | power-kapsam §0 kaydı (2026-10-13) |
| Kalıcı depolama şeması / dosya sistemi | K007-data · K000 | ssd/hdd yalnız envanter |

## §4 Düğüm Kapsamı (Ne Kadar Yer Kaplıyor)

| Kademe | Sayı | Öğe |
|---|---|---|
| Kademe-2 çocuk | 5 | donanım-kernel · donanım-core · donanım-api · donanaım-type · donanım-devices |
| Kademe-3 alt bağlam | 10 | hdd · audio · ssd · display · power · cables · network · memory · cpu · gpu |
| Root dosyalar (kapsam bu) | 11+2 | anayasa · bagimliliklar · dogrulama · kararlar · evrensel-dil · kod-ornekleri · platformlararasi-kod-ornekleri · test-stratejisi · riskler · web-arastirma · index (+ **donanım-kapsam** · **donanım-bilesenler**) |
| Ek raporlar | 8 | `raporlar/` (uygulama-plani, tur-envanteri, os-dil-matrisi, …) |

## §5 Sınır Karar Prosedürü

1. **Eşleşen satır var mı?** → §2/§3 tablosuna bak; varsa karar kesin.
2. **Yeni yüzey mi?** → `donanım-anayasa.md` §2'ye satır eklenmeden
   K001'e alınmaz (bu dosya anayasayı tekrarlamaz, uygular).
3. **İki K arasında mı?** → karar mercii sütunu (§2/§3) belirler;
   çelişkide anayasa kazanır, sonra bu dosya.
4. **K000/K005 yüzeyine kayma** → dur + escalation (anayasa §6 kapısı,
   LAYER_VIOLATION).
5. **Kanıtsız iddia** → `⚠️ VERIFICATION REQUIRED`; tahmin yok
   (anayasa §3 İlke 2 fail-safe).

## §6 Bilinen Kapsam Çelişkileri (salt rapor — DÜZELTME YASAK)

| # | Çelişki | Durum |
|---|---|---|
| C1 | `architecture/index.md` §6 "K002 yok (ID GAP)" ↔ `K002-electroncis/` diskte VAR | ⚠️ Vault Steward kararı (ADR-102 §6) |
| C2 | anayasa §2 (4 kademe-2 + 3 kademe-3) ↔ index (5 çocuk + 10 bağlam) | ⚠️ anayasa §2/§10 revizyonu PROPOSED |
| C3 | power-kapsam §0 "tasarım K002" ↔ anayasa §2 "depolama envanteri EVET" satır dışı power | ⚠️ çelişki kaydı anayasa §10'da |
| C4 | PVDD 24–52 V (görev) ↔ 18–53.5 V (TI datasheet, erişim 2026-10-12) | ⚠️ VERIFICATION REQUIRED |
| C5 | `index.md` §GRUPLAR tablosu bu dosyayı listelemiyor | ⚠️ index.md yazımı S2-B/majorite yetkisinde — bu oturumda dokunulmadı |

## §7 Kanıt

- `donanım-anayasa.md` §2 (10 satır sınır) · §5 (matris) · §6 (sözleşmeler)
- `index.md` §GENEL BAKIŞ (5+10) · Çocuk kataloğu · §1.5 Neva Engine eşlemesi
- `donanım-bagimliliklar.md` §2 — giden bağımlılık: K000 · K005
- `donanaım-type/audio/cipler/{pcm3168a-codec,ak4458-dac,tpa3255-amplifier}.md`
