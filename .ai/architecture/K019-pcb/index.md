---
type: architecture
category: layer
title: "K019 — PCB"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K019 — PCB

## §1 Kimlik
- Katman: K019 · Alan: **A5** (K16-K20).
- Kapsam: PCB tasarımı ve donanım tasarım standartları.

## §2 Sorumluluk
1. Donanım tasarım standartlarına uygun PCB yerleşimi (ADR-063).
2. Sinyal/güç/termal katman planı (şartname düzeyi).
3. BOM ve üretim dosyalarının hazırlanması (üretim = K020).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** A0 zemini (K000–K005) — sayısal kural (plan §2-1).
- **Üst (çağıran):** K016 · K017 · K018 (fiziksel taşıyıcı) · K020.
- A5 içi kenarlar plan §3'te yok → **UNKNOWN**.

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-063 | Hardware Design Standards |
| ADR-061 | Electronics Architecture (L6) — üst şema |

## §5 Durum
**PLANNED** — ADR-063 dosyası diskte; PCB tasarım dosyası yok: `**/*.{kicad_pcb,gbr,gerber,brd}` glob = 0 (2026-10-09).

## §6 Risk / Not
- DRC/gerber kanıtı yok → üretim-öncesi doğrulama yapılmadı (**UNKNOWN**).

---

## §7 Makro Katman Karşılığı (K0–K4)

| Bu K | §6 durumu | Makro eşleme | Makro kimliği |
|---|---|---|---|
| K019 | PLANNED | **Yok** — A5 (K016–K020) makro K0–K4'e girmez | A5 (AGENTS §5) · `[[../katmanli-mimari-k0-k4/index]]` §6.1 |

- Eşleme: `[[../00-master-index]]` §8 · kenarlar: `[[../katman-baglilik-matrisi]]` §4 · makro tanımı: `[[../katmanli-mimari-k0-k4/index]]` §1.1 · §6.1-§6.2.
- Not (matris §1): A5 kapsamı "Bileşenler (donanım)"tır; ADR-063'ün authority satırı bu standardı **A0/A5** sınırında birlikte kapsar.

## §8 Arayüz (Girdi/Çıktı & API)

| Kenar | Yön | Girdi → Çıktı | Kanıt |
|---|---|---|---|
| K016/K017/K018 → K019 | taşıyıcı | modül/güç/termal → PCB yerleşimi | K019 §3 |
| K019 → K020 | üretim | Gerber **veya IPC-2581** + BOM + netlist | ADR-063 §2(c)/(d) |
| K019 → A0 zemin | katman | sinyal/güç/termal katman planı | K019 §2-2 |
| DRC kapısı | kalite | tasarım → **fabrikasyon öncesi** onay | ADR-063 §2(a) — ☐ işaretsiz |
| EMC kapısı | kalite | tasarım → **lansman öncesi** onay | ADR-063 authority (b) — ☐ işaretsiz |

## §9 Hata Modları ve Güvenlik Sınırı

| Hata modu | Sonuç | Belirlenmiş kontrol | Kanıt durumu |
|---|---|---|---|
| DRC ihlali (min iz/boşluk) | üretim reddi | fabrikasyon öncesi **zorunlu DRC kapısı** | ADR-063 §2(a) — ☐ (işaretsiz) |
| EMI/EMC uyumsuzluğu | sertifika reddi, lansman durur | IEC 61000-4-2/4-3/4-4/4-5 immünite matrisi | ADR-063 authority (b) — ☐ |
| Topraklama kaynaklı parazit | ses zinciri bozulması | **star ground şartı** | ADR-089 §3.3.4 (yasaklar) |
| Sınıfsız tedarik | belirsiz kart kalitesi | IPC-6012 **Class 2** kart + IPC-A-610 Class 2 işçilik | ADR-063 §2 — kabul edildi |
| Revizyon dağınıklığı | uyumsuz üretim seti | şema+BOM+layout **tek revizyon seti** (IPC-2581) | ADR-063 §2(c) — PLANNED |

**Güvenlik sınırı:** K019 donanım/üretim kalitesi katmanıdır; yazılım güvenlik primitifleri (K006) bu katmana uygulanmaz. **EMC kapısı lansman öncesi işaretsiz kapatılamaz** (ADR-063 authority (b)).

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

1. **UNKNOWN** — PCB CAD dosyası yok: `**/*.{kicad_pcb,gbr,gerber,brd}` glob = **0** (§5, 2026-10-09); DRC/gerber kanıtı yok → üretim-öncesi doğrulama yapılmadı (§6).
2. **⚠️ VERIFICATION REQUIRED** — ADR-063 künyesi/kaynak satırı `architecture/k19-pcb/` = **12 .md** iddia eder. Bu çalışma ağacında: kök `C:\www\coremusic.net\architecture` = **ENOENT (yok)**; `.ai\architecture\k19-pcb` = **0**; eşleşen tek dizin `--\architecture\k19-pcb\ = **1 dosya**. "12 .md" **doğrulanamadı**.
3. **⚠️ VERIFICATION REQUIRED** — ADR-063'ün bağlayıcı kural seti `k19-pcb/index.md:44-55`, DRC `k19:178`, EMC `k19:180` diye satır numarasıyla atıf yapar. Yukarıdaki tek dosya (`--\architecture\k19-pcb\index.md`) **47 satırlık STUB**'tur (status: draft, *"Envanter: henüz yok"*); `k19-pcb/index.md` (SSOT) `.ai\architecture` altında **bulunmaz** → atıf edilen satır aralıkları **teyit edilemedi**.
4. **UNKNOWN** — üç kalite kapısı **işaretsiz (☐)**: DRC (`k19:178`) · EMC sertifikasyon testi (`k19:180`) · EMC sertifikası (`emc-compliance:369`). Bu üç satır **ADR-063'ün kendi alıntısıdır**; hedef dosyalar bu görevde açılmadı.
5. **UNKNOWN** — ADR-063'ün AES17 fabrika testi bağlaması (§2(e)) üretim test dokümanına **bağlanmadı**; ADR-063 bunu PLANNED olarak kurar ve `k19|k1|k20` grep'ini 0 isabet bildirir.
