---
type: architecture
category: layer
title: "K017 — Güç Kaynağı"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K017 — Güç Kaynağı

## §1 Kimlik
- Katman: K017 · Alan: **A5** (K16-K20).
- Kapsam: 6S LiPo batarya + ±35V boost konvertör beslemesi.

## §2 Sorumluluk
1. 6S LiPo batarya paketi ve şarj/koruma akışı (ADR-089).
2. ±35V boost konvertör tasarımı ve filtreleme.
3. Amplifikatör (K016) besleme şartnamesi.

## §3 Bağlantılar (Dependency Rule)
- **Alt:** A0 zemini (K000–K005) — sayısal kural (plan §2-1).
- **Üst (çağıran):** K016 (amplifikatörü besler) · K020 (üretim).
- A5 içi kenarlar plan §3'te yok → **UNKNOWN**.

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost |

## §5 Durum
**PLANNED** — ADR-089 şartname düzeyinde; güç hesabı/ölçüm dosyası diskte yok.

## §6 Risk / Not
- Koruma devresi (aşırı akım/ısıl) şartnamesi kanıtlanmadı → **UNKNOWN**.

---

## §7 Makro Katman Karşılığı (K0–K4)

| Bu K | §6 durumu | Makro eşleme | Makro kimliği |
|---|---|---|---|
| K017 | PLANNED | **Yok** — A5 (K016–K020) makro K0–K4'e girmez | A5 (AGENTS §5) · `[[../katmanli-mimari-k0-k4/index]]` §6.1 |

- Eşleme tablosu: `[[../00-master-index]]` §8 · kenarlar: `[[../katman-baglilik-matrisi]]` §4 · makro tanımı: `[[../katmanli-mimari-k0-k4/index]]` §1.1.
- K017, K000–K005 zeminine **aralık bağımlılığıyla** bağlıdır (matris §2); A5 içi kenarlar plan §3'te yok → **UNKNOWN** (§3).

## §8 Arayüz (Girdi/Çıktı & API)

| Kenar | Yön | Girdi → Çıktı | Kanıt |
|---|---|---|---|
| Pil/adaptör → K017 | girdi | 6S LiPo 22.2V **veya** 19–24V DC adaptör | ADR-089 §1.1 (README L171 alıntısı) |
| K017 → K016 | çıkış | 12–24V DC → boost → **±35V** (LM5122 interleaved dual, %96) | ADR-089 §2 kalem 5 |
| K017 → K018 | ısıl | konvertör kaybı → termal yük | K018 §3 |
| K017 → K020 | üretim | güç modülü tasarım/BOM girdisi | K020 §2-3 |
| OR-ing | giriş | çift kaynak (batarya ↔ DC) birleştirme | ADR-089 §1.1 (CLAUDE L117 alıntısı) |

- Çıkış topolojisi boost + inverting olarak özetlenir; **polarite/şema/ölçüm dosyası yok** → **UNKNOWN**.

## §9 Hata Modları ve Güvenlik Sınırı

| Hata modu | Sonuç | Belirlenmiş kontrol | Kanıt durumu |
|---|---|---|---|
| Düşük giriş voltajı | sistemin kapanması | **UVP etkinliği** | ADR-089 §3.3.3-4 (kabul kriteri) — eşik **yok** |
| Aşırı giriş/çıkış voltajı | amplifikatör hasarı | **OVP etkinliği** | aynı — devre **yok** |
| Aşırı akım / kısa devre | konvertör hasarı | **OCP etkinliği** | aynı — devre **yok** |
| Aşırı ısıl | termal kaçış | **OTP** + KSD301 72°C kesme | ADR-089 §3.3.3-4/-5 — ölçüm **yok** |
| Şebeke 50Hz köprü gürültüsü | SNR düşüşü | DC-ONLY mimari (AC besleme 🔴 REDDEDİLDİ) | ADR-089 §3.1 A7 · §3.3.4 |

**Güvenlik sınırı:** bu katman yüksek enerjili (12–24V / ±35V) taraftır; yazılım güvenlik katmanı (K006) burada geçerli değildir. Sınır **donanım korumaları** + fiziksel izolasyondur; batarya şarj/koruma akışının kod karşılığı **yok** (PLANNED).

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

1. **UNKNOWN** — koruma devresinin **eşikleri, gecikmeleri ve şeması** yok. ADR-089 bunları yalnız **kabul kriteri** olarak ister (*"UVP/OVP/OCP/OTP korumaları etkin"*, §3.3.3-4); bu dosya §6 tespitiyle uyumludur — çelişki yoktur.
2. **UNKNOWN** — güç hesabı/ölçüm dosyası diskte yok (§5); **%96 verim** ve Ripple Eater referansı (ADR-089 §1.4) ölçülmemiştir.
3. **UNKNOWN** — A5 içi kenarlar plan §3'te yok (§3). Yukarıdaki §8 tablosundaki kenarlar K017 §2-§3 ve ADR-089 okumasından **türetilmiştir**; planda kayıtlı değildir.
4. **⚠️ VERIFICATION REQUIRED** — ADR-089 §1.1 L33 alıntısı `[[../../CLAUDE.md]]` L117'dir; o dosya bu görevde **okunmadı** → aktarılan "LM5122 ×2 · OR-ing · %96 verim · UVP/OVP/OCP/OTP" değerleri **ikincil kaynak, doğrulanmadı**.
5. **⚠️ VERIFICATION REQUIRED** — ADR-090 §1.1'in atıf ettiği `architecture/k17-guc-kaynagi/CLAUDE.md` bu çalışma ağacında **bulunamadı**: kök `architecture/` **yok**; eşleşen dizin `--\architecture\k17-guc-kaynagi\ = **1 dosya** (`index.md`) — `CLAUDE.md` **YOK**.
