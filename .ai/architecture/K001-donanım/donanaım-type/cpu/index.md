---
title: "donanaım-type/cpu — Genel Amaçlı İşlemci Sınıfı Künyesi"
type: architecture
node_id: K001-donanım/donanaım-type/cpu
node_type: bounded-context
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - cpu-kapsam.md
  - cpu-dogrulama.md
  - cpu-ornekler.md
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Vault Steward 2026-10-13 dağıtık onayı — klasör açımı bu oturumda YAPILDI"
  - "donanım-anayasa.md §10 Açık Sorular/3 (satır 195) · §12/C10 (satır 250) — mevcut metinde 3 bağlam"
  - "donanaım-type-kapsam.md §2/A6 (satır 46) · §7/3-T1 (satır 135) — kapsam DIŞINDA (revizyon: APPROVAL REQUIRED)"
  - "Repo ölçümü 2026-10-13: 319 .php · 0 .c/.cpp/.h/.cs dosyasında cpuid·__rdtsc·AVX·NEON·SSE2 = 0"
  - "https://riscv.org/ (HTTP 200, erişim 2026-10-13, HEAD) · https://www.arm.com/architecture (HTTP 200, erişim 2026-10-13, HEAD)"
validation: "proposed — 2026-10-13 yazıldı; anayasa ve üst kapsam metni revize EDİLMEDİ (PROPOSED — APPROVAL REQUIRED); D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Q-C1: Çalışma makinesinin CPU envanteri (model · çekirdek · kuşak · önbellek) bilinmiyor — UNKNOWN (K001 Q4)"
  - "Q-C2: anayasa §10/3 + §12/C10 ve kapsam §2/A6 · §7/3-T1 revizyonu onayı alınmadı — status: proposed kalır"
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

# K001-donanım/donanaım-type/cpu — ELI15

Genel amaçlı işlemci (CPU) sınıfı: x86/x64 · ARM (AArch64) · RISC-V gibi
komut takımları, çekirdek sayısı ve mimari kuşaklar, önbellek (cache)
hiyerarşisi, bellek denetleyicisi, komut seti genişletmeleri ve sanallaştırma
genişletmeleri. PHP 8.4 uygulama katmanının ve ses motoru iş yükünün fiziksel
olarak oturduğu yer burasıdır.

> ⚠️ **PROPOSED — APPROVAL REQUIRED:** Bu klasör Vault Steward 2026-10-13 tarihli
> açık onayı ile açılmıştır; ancak `donanım-anayasa.md` (§10 Açık Sorular/3 ·
> §12/C10) ve `donanaım-type-kapsam.md` (§2/A6 · §7/3-T1) metinleri hâlâ "yalnız
> 3 bağlam (hdd · audio · ssd)" der. **Metin revizyonu yapılmadı** → tüm bu dosya
> seti `status: proposed` taşır; kapanış anayasa + üst kapsam revizyonuyla olur.
> Çelişki tablosu: `cpu-kapsam.md` §0.

> yazım notu: `donanaım-type` yazımı v2 kaynağına sadıktır — **düzeltme yasak**
> (v2 §3 KURAL 1; `donanaım-type/index.md` yazım notu). Klasör adı bu yazımı taşır.

## Durum

| Alan | Değer |
|---|---|
| node_type | bounded-context |
| parent_id | K001-donanım/donanaım-type |
| children | `cpu-kapsam.md` · `cpu-dogrulama.md` · `cpu-ornekler.md` |
| status | proposed (anayasa revizyonu bekleniyor) |
| Kapsam | Genel amaçlı işlem — ISA · çekirdek · önbellek · denetleyici · sanallaştırma |
| Envanter | UNKNOWN (Q-C1) — fiziksel CPU listesi yok |
| Ölçüm | 0 — bu sınıfa ait hiçbir fiziksel ölçüm YAPILMADI |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `cpu-kapsam.md` | Çelişki kaydı · ISA/çekirdek/önbellek kapsamı · 26 alan tür kayıtları | Kademe-3 |
| kanıt | `cpu-dogrulama.md` | CPU kapıları C1–C9 · HEAD-200 kanıt matrisi · envanter · PHP bağlam kapısı | Kademe-3 |
| kod | `cpu-ornekler.md` | Derleme bayrağı · envanter toplama · PHP/JIT kontrol · ölçüm formları | Kademe-3 |

## Sınır özeti (bu klasörün tutmadığı yüzeyler)

| Komşu | Bu klasörün tutmadığı |
|---|---|
| K004-dsp | Donanım DSP çipleri ve sinyal işleme zinciri — `cpu/` yalnız genel amaçlı işlem |
| K002-electroncis | Devre/PCB tasarımı, BOM hesabı |
| K000-isletim-sistemi | Zamanlayıcı (scheduler), önbellek/TLB yönetimi, sürücü katmanı |
| `../display/` · `../gpu/` | GPU = işlem+render; display = panel+arayüz |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
