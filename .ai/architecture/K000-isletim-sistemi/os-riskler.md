---
node_id: os-riskler
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: [K000-isletim-sistemi, K001-donanım, K009-security, K016-observability, K17-devops, K18-testing]
evidence:
  - ".ai/architecture/K000-isletim-sistemi/os-dogrulama.md §1-§2 (PoC P1-P4, ölçek hedefleri) · 2026-10-18"
  - ".ai/architecture/K000-isletim-sistemi/os-kararlar.md §2 (KD-001..KD-010 risk bağları) · 2026-10-18"
  - "shared/phpunit.xml (dual-FD audit gate) + .github/workflows/ci.yml:1 (CI kanıtı) · 2026-10-18"
validation: "Her satır Olasılık×Etki×Azaltma×Sahip içerir; ≥12 risk; os-dogrulama PoC'leri ile eşleşmeli."
open_questions:
  - "⚠️ Risk sahipleri (Sahip sütunu) ROLE etiketi ile yazıldı — gerçek ekip/agent ataması UNKNOWN (sahip matrisi vault'ta yok)."
  - "⚠️ R-011 monolitik çekirdek kriz metriği (panic süresi eşiği) için hedef değer verilmedi — UNKNOWN."
group: mimari
---

# K000 — Riskler (Olasılık × Etki × Azaltma × Sahip)

## §1 ELI15

Risk = "bir şey ters giderse ne kaybederiz". Her satırda dört şey vardır:
**Olasılık** (ne kadar sık?), **Etki** (ne bozar?), **Azaltma** (ne
yapıyoruz?), **Sahip** (kim bakıyor?). Risk listesi boş azaltmayla
yazılmaz; azaltma yoksa o satır `UNKNOWN` işaretlenir.

## §2 Risk Kayıt Defteri

| # | Risk | Olasılık | Etki | Azaltma | Sahip |
|---|------|----------|------|---------|-------|
| R-01 | RTL uçtan uca sızdırırsa boot/ölçek geri dönüşü (PoC P1-P4 geçmez) | Orta | Çok yüksek | os-dogrulama §2.2 zinciri: mock kernel → gerçek runtime → N probe → sr springback → crt.watch görsel kanıt | ROLE:mimari |
| R-02 | PROT-K5 boundary ihlali (bootloader ↔ OS veri/ikon dosyası paylaşırsa) | Orta | Yüksek | `bootloader-kernel-protokolu.md` tek kapı: boot_meta + `write_kernel_fail` + `os_error_boundary`; ikon BOOT_SURFACE olarak zaten yasak | ROLE:mimari |
| R-03 | Yetkisiz dosya erişimi (fail-open: izin düşerse erişim açık kalır) | Orta | Çok yüksek | `security-context.md` §3: sessiz privilege escalation yok, fail-safe (kapatmak); `stream-permission.md` açık kapı yok; ADR-091 CAP_NET_RAW/ADMIN kapıları | ROLE:security |
| R-04 | Boot penceresi aşımı (~1.1 sn gate `boot--acd--`) | Orta | Yüksek | Zaman damgalı boot gate (`boot--acd--`), PROT-K4 ölçümü; `.ai/01-core/*.php` giriş protokolü | ROLE:devops |
| R-05 | Beklenmeyen çekirdek paniği → OCAK TETİKLENİR (os-dogrulama §1) | Düşük | Çok yüksek | Panic dönüştürücü → kırıcı PoC üretir → redaksiyon (perception-redaction); sr springback kanıtı | ROLE:mimari |
| R-06 | PIPE'de `SIGPIPE` sessiz veri kaybı / `TypeError` (PHP stream kapanınca write false döner) | Orta | Orta | PHP seviyesi yok say → `stream_select` 5 sn timeout + canlılık **+ kapanış tespiti** (silent-cleanup sarmalayıcı) | ROLE:backend |
| R-07 | VMFS `Label too long` (maks. 255 bayt) üstü edge-case | Düşük | Orta | `checkLiveness` dosya yokluğunu temizler; maks-label guard | ROLE:backend |
| R-08 | Yarış durumu / deadlock (eşzamanlılık hataları TS-O7/O8) | Orta | Yüksek | Parallel probe + içerik hash'i (os-dogrulama §2.1); `.ai/08-logic` kuyruk ilkesi | ROLE:mimari |
| R-09 | `php -l` gate kandırır (syntax geçer, runtime patlar) | Orta | Orta | Gate bitişi **her** `php -l` dosyasında c1-c4 breakpoint (os-dogrulama §3); ci.md PHP-08 doğrulaması | ROLE:qa |
| R-10 | CI'da tüm kapaklar yeşil (`phpunit.xml` dual-FD audit) ama prod'da kırık | Düşük | Yüksek | `.github/workflows/ci.yml` PHASE-0; dual-FD (`/tmp/c0d4_f1`); `runtime-error-truth.md` suistimal denetimi | ROLE:devops |
| R-11 | Monolitik çekirdek kütüphanesi (KD-001): kriz anında tek dosya değişimi | Orta | Yüksek | PROT-K0 tek doğruluk kaynağı; PoC tetikleyicisi + kurtarma (session-recovery) | ROLE:mimari |
| R-12 | K001 bağımlılık kopması (device node / kesme beklenen yerde gelmez) | Orta | Yüksek | `bootloader-kernel-protokolu.md` PROT-K1..K7; K002↔K000 5 kural; `DeviceManager.php:24` durum makinesi | ROLE:embed |
| R-13 | ADR-038 ihlali (PCM5122/DAC H001 dışı parça kod'a sızmak) | Düşük | Yüksek | ADR-038 fatura/kılıf kapıları; K000 yalnız "sürücü/hayatta kalma" yüzeyine dokunur | ROLE:embed |
| R-14 | Gözlemlenebilirlik boşluğu: os log'u newline-delimited JSON olmazsa korelasyon kırılır | Düşük | Orta | `StructuredLogger.php:72` newline-delimited JSON; `.ai/06-systems/observability-rules.md §7` batch window | ROLE:observability |
| R-15 | Sözlük tutarsızlığı (page↔route, process↔iş) → yanlış bağ | Orta | Düşük | `os-evrensel-dil.md` 30 terim; yasaklı adlar KD-009/KD-010 | ROLE:docs |

## §3 Azaltma Yoksa

`Azaltma` hücresi `UNKNOWN` olan satır üretilmez; üretilirse o risk
**kabul edilmemiş** sayılır ve §2'ye `UNKNOWN` notuyla eklenir.
Olasılık/Etki sözcükleri: Düşük / Orta / Yüksek / Çok yüksek (sayısal
yüzde verilmez — ölçüm os-dogrulama §2'dedir).

## §4 Kanıt

- `os-dogrulama.md` §1 (PoC P1-P4) · §2.1-§2.2 (ölçek) · §3 (gate)
- `os-kararlar.md` §2 (KD bağları) · `firmware-kapsam.md` türev doğrulama
- `shared/phpunit.xml` · `.github/workflows/ci.yml:1` · `shared/src/Device/DeviceManager.php:24`

## §5 ⚠️ Bilinen Bozuk Referanslar (salt rapor — DÜZELTME YASAK)

- `os-master.md` FM `depends_on: [K002-altyapi]` — bu K diskte YOK (önceki rapor).
