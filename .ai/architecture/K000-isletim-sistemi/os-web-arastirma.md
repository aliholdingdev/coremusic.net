---
node_id: os-web-arastirma
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: [K000-isletim-sistemi, K001-donanım, K004-dsp]
evidence:
  - "webfetch ile 2026-10-18 doğrulanan 6 kaynak (§2 tablosu) · erişim tarihi her satırda"
  - ".ai/architecture/K000-isletim-sistemi/os-bilesenler.md §3-§5 (RAM/sistem belleği, DAC, sürücü köprüsü) · 2026-10-18"
validation: "≥5 gerçek URL · her biri webfetch ile erişilmiş ve erişim tarihi yazılmış · erişilemeyen kaynak '⚠️ DEAD?' ile işaretli."
open_questions:
  - "⚠️ https://learn.microsoft.com/en-us/windows/win32/coreaudio/apis → 2026-10-18'de HTTP 404 (önceki oturumda erişilmişti) — güncel MS Core Audio URL'si UNKNOWN, ⚠️ DEAD? yeniden aranmalı."
  - "⚠️ KD-003 (CFS)上游 durumu: scheduler doc EEVDF Scheduler sayfası da listeliyor (kernel 7.3.0-rc7) — CFS→EEVDF geçişi vault etkisi VERIFICATION REQUIRED."
group: mimari
---

# K000 — Web Araştırması (Doğrulanmış Kaynaklar)

> **Doğrulama yöntemi:** Her URL üretim öncesi `webfetch` ile çekilip
> içerik doğrulandı. Erişilemeyenler `⚠️ DEAD?` ile işaretlenir.
> Erişim tarihi: **2026-10-18** (tüm satırlar).

## §1 ELI15

Bu dosya K000'idaki iddiaların dış kaynak kanıtlarını tutar. Kural basit:
idDia önce, kaynak sonra — her satırda "hangi iddiayı destekliyor" yazar,
kaynak üretmez (zero-hallucination).

## §2 Doğrulanmış Kaynaklar

| # | URL | Erişim | Ne doğruladı (çıkarım) | Desteklediği iddia |
|---|-----|--------|------------------------|--------------------|
| 1 | `https://man7.org/linux/man-pages/man2/syscalls.2.html` | 2026-10-18 ✅ | "The system call is the fundamental interface between an application and the Linux kernel." Liste: `fork(2)` 1.0 · `execve(2)` 1.0 · `waitpid(2)` 1.0 · `pipe(2)` 1.0 · `select(2)` 1.0 (man-pages 6.19, 2026-02-08) | os-kapsam §3.3 süreç yaşam döngüsü (fork/exec/waitpid); sistem çağrısı tanımı |
| 2 | `https://man7.org/linux/man-pages/man7/path_resolution.7.html` | 2026-10-18 ✅ | Yol çözümleme 3 adım; maks. 40 symlink (`ELOOP`); `EACCES`/`ENOENT`/`ENOTDIR`/`ENAMETOOLONG`; izin bitleri üçlü grup; `fsuid`/`fsgid`; `CAP_DAC_OVERRIDE` + `CAP_DAC_READ_SEARCH` | os-bilesenler §5 dosya/yetki katmanı; os-riskler R-03 (fail-safe) |
| 3 | `https://docs.kernel.org/scheduler/index.html` | 2026-10-18 ✅ | Kernel dokümantasyonu 7.3.0-rc7; Scheduler bölümü: **CFS Scheduler** ve **EEVDF Scheduler** alt sayfaları birlikte listeli; CFS Bandwidth Control, Schedutil | os-bilesenler §4 zamanlayıcı katmanı; KD-003 (CFS) ⚠️ upstream notu |
| 4 | `https://docs.kernel.org/core-api/mm-api.html` | 2026-10-18 ✅ | `kmalloc`/`kzalloc`/`kfree`/`kvfree`; `GFP_KERNEL` (uykuya dalabilir) vs `GFP_ATOMIC` (uyumaz, acil rezerv); `get_user`/`put_user` (kullanıcı↔çekirdek kopya); `SLAB_TYPESAFE_BY_RCU` | os-bilesenler §3 RAM/bellek API'si; "System memory (RAM)" (os-evrensel-dil #10) |
| 5 | `https://www.php.net/manual/en/function.stream-select.php` | 2026-10-18 ✅ | `stream_select()` = `select()` eşdeğeri (timeout sn+µs); 0-timeout döngüsü CPU uyarısı; dönüşte diziler değişir (`===` ile false kontrolü); **Windows'ta `proc_open()` FD'leri ile başarısız**; pipe'ta EOF'ta bloklama notu | os-kod-ornekleri PHP bloğu; os-riskler R-06 (PIPE/SIGPIPE); MusicStreamHandler hattı |
| 6 | `https://www.alsa-project.org/wiki/Main_Page` | 2026-10-18 ✅ (önceki turda TIMEOUT idi) | ALSA: "Fully modularized sound drivers", "SMP and thread-safe design", alsa-lib 1.2.16.1 (2026-06-12) | os-bilesenler §6 ses/donanım sürücü köprüsü (K000↔K001↔K004) |

## §3 ⚠️ Ulaşılamayan / Değişen Kaynaklar

| URL | Tarih | Durum |
|-----|-------|-------|
| `https://learn.microsoft.com/en-us/windows/win32/coreaudio/apis` | 2026-10-18 | **⚠️ DEAD?** — HTTP 404 (önceki oturumda erişilmişti; güncel MS Core Audio path'i UNKNOWN, yeniden aranmalı) |

## §4 Kaynak Kullanım Kuralları

1. **Doğrulanmayan URL yazılmaz** — §2'ye yalnız `webfetch` ile çekilmiş
   kaynak girer; erişilemeyen §3'e düşer (`⚠️ DEAD?`).
2. **Vault > model hafızası** — §2'deki çıkarım satırı kaynaktaki
   gerçek metne dayanır; aksi `UNKNOWN`/`⚠️ VERIFICATION REQUIRED` yazılır.
3. **Tarih zorunlu** — her satırda erişim tarihi (2026-10-18).

## §5 Kanıt

- webfetch turları: 2026-10-18 (6 ✅, 1 ⚠️ 404)
- Yerel bağ: os-bilesenler §3-§6 · os-kapsam §3.3 · os-riskler R-03/R-06 · os-kod-ornekleri §2

## §6 ⚠️ Bilinen Bozuk Referanslar (salt rapor — DÜZELTME YASAK)

- `#os-web-arastirma` kullanımda olabilir (bu dosya karşılar).
- `os-master.md` FM `depends_on: [K002-altyapi]` — diskte YOK (önceki rapor).
