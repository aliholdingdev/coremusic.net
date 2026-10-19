---
node_id: os-evrensel-dil
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: [K000-isletim-sistemi]
evidence:
  - ".ai/architecture/K000-isletim-sistemi/os-master.md §1-§6 (kavram envanteri) · 2026-10-18"
  - ".ai/architecture/K000-isletim-sistemi/os-kararlar.md §2 (yerel karar sözlüğü KD-001..KD-010) · 2026-10-18"
validation: "Tüm terimler EN↔TR çift yönlü eşleşmeli; ≥15 madde; K000 master içeriği ile birebir örtüşmeli."
open_questions:
  - "⚠️ 'process' için Türkçe resmi karşılık ('süreç' mi, 'iş' mi?) Akademik Türkçe'de 'iş' (job) ayrı kavramdır — kullanım tutarlılığı VERIFICATION REQUIRED."
  - "⚠️ 'session' ve 'daemon' Türkçe kaynaklarda sıklıkla İngilizce bırakılır; zorunlu çeviri önerilmez — tercih ekibi kararı (UNKNOWN)."
group: mimari
---

# K000 — Evrensel Dil (Sözlük · EN ↔ TR)

> **Kapsam:** K000-isletim-sistemi kademesinin resmi çift dilli sözlüğü.
> Amaç: `.ai/architecture/` içinde ve koda yazılan yorumlarda kavramların
> tek karşılıkla kullanılması; ADR/KD metinlerinde anlam kaymasının önlenmesi.

## §1 ELI15

Bir kavramı iki farklı kelimeyle anlarsan ekip birbirini yanlış anlar.
Bu dosya OS kavramlarının **İngilizce (kaynak)** ve **Türkçe (kullanım)**
karşılıklarını sabitler. Yeni bir OS terimi eklerken buraya da ekle;
ADR, kod yorumu ve test adlarında bu sözlük geçerlidir.

## §2 Çift Yönlü Sözlük

| # | İngilizce (EN) | Türkçe (TR) | K000 bağlamı | Not |
|---|----------------|-------------|--------------|-----|
| 1 | Operating System (OS) | İşletim Sistemi | K kökü · `os-*` | Üst şemadan: `os-master.md` |
| 2 | Kernel | Çekirdek | Monolitik çekirdek kararı KD-001 | RTL/C/Go örneklerinde "çekirdek" |
| 3 | System call | Sistem çağrısı | `man 2` bağlantıları | Kapatma görevi §7.1 os-kapsam |
| 4 | Process | Süreç *(bkz. ⚠️ open_question)* | `fork/exec/waitpid` | RTE adım 3 (os-kapsam §3.3) |
| 5 | Thread | İş parçacığı | paylaşılan adres uzayı | `pthread_create` |
| 6 | Context switch | Bağlam değişimi | mikro-saniye maliyet | RTL süreç 4 |
| 7 | Scheduler | Zamanlayıcı | CFS (KD-003) | okuma: `docs.kernel.org/scheduler` |
| 8 | Preemption | Öncelikle kesme (önleme) | ROUND_ROBIN/QR öncelik | `.ai/08-logic/proje/oncelik-sistemi.php` |
| 9 | Interrupt | Kesme (donanım) | K001 ↔ K000 sınırı | bootloader-K000 PROT-K5 |
| 10 | System memory (RAM) | Sistem belleği | PROT-K7 fork+exec | `mm-api.html` (2026-10-18 doğrulandı) |
| 11 | Virtual memory | Sanal bellek | PROT-K6 | sayfalama, TLB |
| 12 | File descriptor (FD) | Dosya tanımlayıcısı | `Stream/MusicStreamHandler.php:185-208` | 0/1/2 stdin/stdout/stderr |
| 13 | Path resolution | Yol çözümleme | `man 7 path_resolution` | symlink uçları |
| 14 | Permission (DAC) | Yetki (izin) | PROT-K1 / ADR-091 | rwx bitleri |
| 15 | Service (daemon) | Arka plan hizmeti | `.github/workflows/ci.yml:1` | ⚠️ TR'de "daemon" sık kullanılır |
| 16 | Shell | Kabuk | `sh`/`bash` | ayrı: CLI yüzeyi |
| 17 | Boot (bootloader) | Önyükleme | PROT-K4 (~1.1 sn) | `boot--acd--` gate |
| 18 | Logging | Günlükleme | `StructuredLogger.php:72` | newline-delimited JSON |
| 19 | Scheduling policy | Zamanlama ilkesi | `.ai/08-logic` | QR/RL/CN/PR/SR |
| 20 | Crash dump / core | Çökme dökümü | os-dogrulama §2.2 | `-core` dosyası |
| 21 | Concurrency | Eşzamanlılık | parallel probe | RT güvenliği os-dogrulama §1 |
| 22 | Race condition | Yarış durumu | TS-O7 | deadlock TS-O8 |
| 23 | Kernel panic | Çekirdek paniği | PoC P1-P4 | dönüştürücü (os-dogrulama §1) |
| 24 | Sysfs / procfs | procfs/sysfs sanal dosya sistemleri | PROT-K3 | kernel arayüz kanıtı |
| 25 | Device node | Aygıt düğümü | K001 bağımlılığı | `/dev/*` |
| 26 | Job (batch) | İş (toplu) | ⚠️ süreçten FARKLI | kuyruk örnekleri: os-kod-ornekleri |
| 27 | Inter-process communication (IPC) | Süreçler arası iletişim | 5 K002↔K000 kuralı | pipe, socket, shared memory |
| 28 | State machine | Durum makinesi | `shared/src/Device/DeviceManager.php:24` | onStart/onEnd/onDeviceAdded |
| 29 | Batch window (K016) | Toplu pencere | `.ai/06-systems/observability-rules.md §7` | K016 bağımlılık kanıtı |
| 30 | Userland (vs kernel) | kullanıcı alanı | monolitik çekirdek | çekirdek/kullanıcı ayrımı |

## §3 Kullanım Kuralları

1. **Köprü dosya → bağlam:** Bir kelime iki K'da geçiyorsa (ör. *boot*
   K000↔K001, *firmware* K000↔K005, *stream* K000↔K004) ADR/KD metninde
   `[K000↔K001]` gibi köprü etiketi kullanılır (tabelalar: os-kapsam §5).
2. **Yerel karar adı = `KD-xxx`** (ADR DEĞİL): karar sözlüğü
   `os-kararlar.md` §2'de; ADR numaraları yalnız `.ai/.decisions/index.md`.
3. **Yasaklı adlandırmalar:** `page` (→ *route*, KD-009), yetkisiz
   `multitenant` (→ `tenant+plan`, KD-010), `PCM5122` (ADR-038: DAC).
4. **Bilinmeyen = `UNKNOWN`**, doğrulanamayan = `⚠️ VERIFICATION REQUIRED`
   (zero-hallucination, AGENTS.md §3).

## §4 Kanıt

- os-master.md §1-§6 — kavram envanteri (2026-10-18)
- os-kararlar.md §2 — KD-001..KD-010 sözlüğü (2026-10-18)
- `.ai/08-logic/proje/oncelik-sistemi.php` — qr/rl/cn/pr/sr kuyruk kısaltmaları
- `shared/src/PageRouter/StructuredLogger.php:72` — *logging* örneği

## §5 ⚠️ Bilinen Bozuk Referanslar (salt rapor — DÜZELTME YASAK)

- `#os-evrensel-dil` etiketi kullanımda olabilir; bu dosya onu karşılar
  (ekleme yapıldı — silme/yeniden adlandırma YOK).
