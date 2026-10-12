---
title: "k1 Donanım — Agent Kapsamı ve Domain Kuralları"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "Derived: .ai/AGENTS.md (registry SSOT) + .ai/architecture/k1-donanim/index.md (katman) — bu dosya k1 kapsamıdır"
updated: 2026-10-07
tier: 3
domain: k1-donanim
ssot: false
risk: medium
owner: "audio-hw"
depends-on: [".ai/architecture/k1-donanim/index.md", ".ai/AGENTS.md"]
---

# k1 Donanım — Agent Kapsamı ve Domain Kuralları

> **Authority zinciri:** `.ai/AGENTS.md` (11 agent registry, routing/handover SSOT) > bu dosya (k1'e özel kapsam) > görev notları.
> Bu dosya **kural koymaz**; k1'e özel sorumluluk/sınır özetler. Yetki çelişkisinde registry kazanır (R8).

---

## §1 Sahiplik Matrisi (k1 kimin?)

| Agent | Rol (k1 kapsamında) | Yapabilir | Yapamaz (sınır ihlali) |
|-------|---------------------|-----------|-------------------------|
| **audio-hw** (birincil) | K1 sahibi: ses zinciri + güç + PCB/termal tasarımı | Tasarım dokümanı, bileşen seçimi, envanter kaydı, ADR önerisi | Middleware/auth kodu (k6/k7) · DSP algoritması (k3) · firmware kodu (firmware/) |
| **dsp-fw** (yardımcı) | XMOS/I2S/TDM firmware ↔ donanım sınırı | I2S/TDM saat/kadran gereksinimleri, firmware-donanım kontratı | Analog topoloji kararı (audio-hw) |
| **embedded** (yardımcı) | C++20/JUCE ↔ donanım sınırı | Real-time kısıtları (buffer/latency) — anayasa §5 K3 | Parça seçimi/PCB (audio-hw) |
| **data / security / backend / ui / devops** | — | **k1'de yetkisi yok** | Bu dizine yazma (registry §5 domain boundary) |
| **MO (vault-updater)** | koordinasyon/kayıt | validator, log append, katalog satırı | Tasarım kararı üretme |

**Yetki seviyesi (R10 bağlamı):** audio-hw k1 içeriğini **PROPOSE** eder → 👤 insan onayı (katman kapı) → MODIFY. APPROVE/GOVERN yetkisi agent'larda **yoktur** (Q5/Q32).

## §2 Handover Senaryoları (k1 → diğer)

| Tetik | Hedef agent | Öncelik | Not (registry §9.3 ile hizalı) |
|-------|-------------|---------|-------------------------------|
| Bileşen seçimi güvenlik verisi içeriyorsa (credential vault donanımı, NFC/RFID) | security | HIGH | tasarım + threat notu teslim |
| I2S/TDM saat/çerçeve uyuşmazlığı | dsp-fw | HIGH | handover formatı registry §9.1 |
| Latency bütçesi ihlali (ASIO <10ms hedefi — ADR-006) | embedded | HIGH | ölçüm kanıtı ekle |
| PCB/termal üretim dosyası gerekirse → deploy/CI | devops | MEDIUM | gerber/BOM yok — PLANNED |
| Tasarım → kod doğrulama (AIEngine referansı) | qa | MEDIUM | kod kanıtı grep |

**Handover kuralı:** hedef onayı olmadan iş tamamlanmaz (registry §9.2) · tüm handover `log.md`'ye append.

## §3 k1'e Özel Domain Kuralları (özetsel — SSOT anayasada)

> Bu maddeler `.ai/CLAUDE.md` §5 (K1/K16-K20 satırları) ve ADR'lerden **türetilmiş özdür**; tekrar değil link. Çelişkide anayasa/ADR kazanır (R8).

| # | Kural | Kaynak | İhlal sonucu |
|---|-------|--------|--------------|
| D1 | **DC-Only güç** — sinyal zincirine parazit YASAK | anayasa §5 K1 | tasarım reddi |
| D2 | **Class AB topolojisi zorunlu; Class D YASAK** | ADR-089 · kök CLAUDE §Mimari | tasarım reddi |
| D3 | **PCM5122 YASAK** → PCM3168A + AK4458 + XMOS XU316 | ADR-038 (H001) | bileşen reddi |
| D4 | 50W/kanal · 8 kanal modüler · tek kanal bağımsız enable | anayasa §5 K16 | mimari ihlal |
| D5 | DC offset >0.5V → koruma rölesi **zorunlu** | anayasa §23 (safety) | güvenlik ihlali (High risk) |
| D6 | Güç: UVP/OVP/OCP/OTP korumaları zorunlu | anayasa §5 K17 | güvenlik ihlali |
| D7 | PCM3168A = codec (6-in/8-out, ADC+DAC) — "yalnız ADC" yazımı düzeltildi (web doğrulama 2026-10-07, ti.com) | kök CLAUDE §Web Verification | yanlış spec |
| D8 | Kapalı kaynak tasarım; üçüncü-parça datasheet atfı zorunlu | anayasa §4.1 | lisans riski |
| D9 | Kapsam sınırı: sürücü(ASIO/WASAPI/ALSA) → **k2** · DSP → **k3** · firmware → **firmware/** · üretim → kapsam dışı | k1 index §2 | duplicate authority (R7) |
| D10 | Her tasarım iddiası ADR/web/grep kanıtlı; kanıtsız `UNKNOWN` | R9 · ADR-005 | Zero-Hallucination ihlali |

## §4 A-alanı ve Eşleme (registry §5 A0-A5 ile)

- **A0** = K0-K5 (altyapı/donanım) → k1 **A0 etiketli** envanter kayıtları üretir.
- K1 fiziksel katmanı ≠ A0 alan etiketi karmaşası: envanterde `A-alanı: A0` yazılır (anayasa §5 A-etiket tablosu).
- K-matrix eşlemesi: `../../00-master-index.md` (d03 → K100-K149 aralığı, Q11 hibrit kaynak).

## §5 Yetki Sınırı Şeması (k1 yaşam döngüsü)

```text
[Keşif/analiz] → READ/ANALYZE/REPORT     (onaysız — envanter taraması, kanıt toplama)
      ↓
[Öneri]        → PROPOSE                  (onaysız — tasarım alternatifi, ADR taslağı)
      ↓                                    ama: dosya DEĞİŞTİRMEYİ kapsamaz
[Onay kapısı]  → 👤 insan (R10 katman kapısı)
      ↓
[Uygulama]     → MODIFY (yalnız k1 dizini + katalog satırı)
      ↓
[Validator]    → 11 check exit 0 → log append → status geçişi
```

**Otomatik karar sınırı (Q45):** READ/ANALYZE/REPORT/PROPOSE otomatik · CHANGE (k1 içeriği) = onaylı · ARCHITECTURE/SECURITY/SSOT = her seferinde 👤.

## §6 Görev Başlangıcı Checklist (k1 için)

1. `k1-donanim/index.md` okundu mu? (P2 — sınır §2, durum §6)
2. [[architecture/rules]] R1–R20 + bu dosya §3 kural özeti okundu mu?
3. Kanıt: web (URL+tarih) + ADR + grep — 3'lüsü (R14, alt-katman zorunlu; k1 için en az 2 → 2/3 = ⚠️)
4. Kapsam sınırı: k2/k3/firmware konusu bu dizine giriyor mu? (giriyorsa DUR, doğru katman)
5. Yazım sonrası: `validate.mjs --check` exit 0 + min-500 + ID tekilliği
6. 👤 katman onayı → `status: draft → active`

---

## §7 İlişkili Dosyalar

| Dosya | İlişki |
|-------|--------|
| `k1-donanim/index.md` | katman SSOT girişi (bu dosyanın bağlamı) |
| [[architecture/rules]] | R1–R20 (bu dosya kuralları ÖZETLER, sahibi değildir) |
| [[architecture/00-master-index]] | authority (500 katman + d03 eşlemesi) |
| `.ai/AGENTS.md` | registry SSOT (routing/handover/§6.1 persona) |
| `.ai/.agents/audio-hardware-engineer.md` | sahip agent profili |
| [[.decisions/accepted/ADR-038-8-1-sound-card-chip-selection]] | çip seçimi (D3) |
| [[.decisions/accepted/ADR-089-classab-24v]] | topoloji + güç (D2/D6) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-07
**Mode:** Red Team · Human Mode · Truth Mode