---
node_id: os-test-stratejisi
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: [K000-isletim-sistemi, K009-security, K016-observability, K18-testing]
evidence:
  - ".ai/architecture/K000-isletim-sistemi/os-dogrulama.md §1-§3 (PoC P1-P4, ölçek, gate) · 2026-10-18"
  - "shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269 (test yazım örneği) · 2026-10-18"
  - "shared/phpunit.xml (dual-FD audit + bootstrap) + .github/workflows/ci.yml:1 (PHASE-0 gate) · 2026-10-18"
validation: "Piramit 3 katmanlı; ≥8 K000-özel senaryo; her senaryonun KABUL KRİTERİ ölçülebilir olmalı."
open_questions:
  - "⚠️ Gerçek kernel/runtime üzerinde otomatik test altyapısı (harness) tanımlı değil — PoP P1-P4 elle mi otomatik mi? UNKNOWN."
  - "⚠️ os-dogrulama §2.2'deki ölçek hedefleri 'sıfır sonuç' eşiği olarak mı yoksa hedef metrik olarak mı raporlanacak? VERIFICATION REQUIRED."
group: mimari
---

# K000 — Test Stratejisi (Piramit · Senaryolar · Kabul Kriterleri)

## §1 ELI15

Test stratejisi = "neyi, hangi sırayla, ne kadar ucuza doğrularız".
Piramidin dibinde **çok sayıda ucuz** test (lint, birim), ortada
**entegrasyon**, tepesinde **az ve pahalı** uçtan uca PoC'ler var.
Bu dosya K000'e özgü senaryoları ve **ölçülebilir** kabul kriterlerini
sabitler.

## §2 Test Piramidi

```text
        /\
       /  \      E2E / PoC (P1-P4, boot gate)  — os-dogrulama §2
      /----\     Entegrasyon (CI PHASE-0, kuyruk boot etkileşimi)
     /------\    Birim (fonksiyon/breakpoint c1-c4)
    /--------\   Statik (php -l, ön-surum doğrulama, sözlük lint)
```

| Katman | K000 kapsamı | Araç / Kanıt | Sıklık |
|--------|--------------|--------------|--------|
| Statik | `php -l` her dosya; frontmatter/piramit sözlük lint'i; ADR numarası `.ai/.decisions/index.md` kayıtlı mı | `ci.md` PHP-08 · `.ai/06-systems/php-dogrulama.md` | her commit |
| Birim | Breakpoint c1-c4 gate bitişinde **her** `php -l` dosyasında; kuyruk (rl/cn/pr/sr) davranışları | os-dogrulama §3 | her commit |
| Entegrasyon | PHASE-0 dual-FD audit (`/tmp/c0d4_f1`); structured log şeması (newline-delimited JSON) | `shared/phpunit.xml` · `.github/workflows/ci.yml:1` · `StructuredLogger.php:72` | PR |
| E2E | PoC P1-P4 (boot → panic → kırıcı → redaksiyon); boot gate `boot--acd--`; sr springback; crt.watch görsel kanıt | os-dogrulama §2.1-§2.2 | gece / sürüm |

## §3 K000-Özel Senaryolar

| # | Senaryo | Yöntem | KABUL KRİTERİ (ölçülebilir) |
|---|---------|--------|------------------------------|
| TS-O1 | Boot zinciri uçtan uca | PoC P1 (mock kernel→gerçek runtime→3 kademe→crt.watch) | P1 **GEÇTİ** yazımı + boot penceresi ≈1.1 sn (`boot--acd--`); gözlemlenen pencere: `< 400 sn` (06:50) — hedef ±? **⚠️** |
| TS-O2 | Kare-probe doğruluk | PoP paralel probe + 3 kademe içerik hash | ≤100 probes (kademeli ölçek); "SIFIR SONUÇ" isabeti ±0 bayt fark |
| TS-O3 | Beklenmeyen paniği tetikleme | PoC P2 (fault injection) | PoC **GEÇTİ**; okuryazar olay ("oka takılan piksel") CRASH hesaba katılır |
| TS-O4 | Kısmi dosya CRASH topolojisi | PoC P3 (kısmi sızıntı) | Ghost #1-Ghost #7 tespitli; "arıza okuryazar" (kısmi dosya) piksel seviyesinde |
| TS-O5 | Kernel panic → kırıcı dönüşümü | PoC P4 → redaksiyon | Ring 0 → kullanıcı uzayı hatası zinciri **kırıcı PoC'ye** dönüşür; `runtime-error-truth.md` suistimali başkasının crash'ine satın alma |
| TS-O6 | Boot penceresi ölçümü | `boot--acd--` zaman damgalı gate | Her adımda GEÇTİ/BİTTİ ayrı raporlanır (tek büyük GEÇTİ yok) |
| TS-O7 | Yarış durumu (eşzamanlı probe yazımı) | paralel probe + hash karşılaştırma | Çakışan içerik yok; lock-free gözlemi tutarlı |
| TS-O8 | Deadlock / kuyruk tıkanması | `.ai/08-logic` kuyruk (rl/cn/pr/sr) + boot etkileşimi testi | boot etkileşimi kuyruğu **kilitlemez**; K016 batch window içinde çözülür |
| TS-O9 | PIPE canlılık/kapanış tespiti | `stream_select` 5 sn timeout + kapanış (silent-cleanup) | kapanan pipe'ta CPU% stabil; `TypeError`/`SIGPIPE` **yok** (PHP seviyesinde yok sayılır) |
| TS-O10 | Yetki fail-safe | `stream-permission.md` + `security-context.md` §3 | izin düşerse erişim **kapalı** (fail-open yok); yanlış erişim: sessiz escalation yok |
| TS-O11 | CI kapak bütünlüğü | dual-FD (`/tmp/c0d4_f1`) PHASE-0 | tüm kapaklar yeşil = canlı ağ yok, suistimal yok (`runtime-error-truth.md`) |
| TS-O12 | Kod örneği derlenebilirliği | her dil bloğu: derle/syntax | PHP `php -l` geçer; `rustc`/`gcc`/`javac` blokları 0 hata (**⚠️ otomasyon UNKNOWN**) |

## §4 Kabul Kriteri (Genel Çapraz)

1. os-dogrulama §2.2 **beş** ölçek doğrulaması (mock kernel, gerçek
   runtime, N probe, sr springback, crt.watch görsel kanıt) tamamı
   "SIFIR SONUÇ".
2. Her PoC sonucu `GEÇTİ`/`KALDI` (tek büyük `GEÇTİ` ile gruplanmaz).
3. `php -l` gate bitişinde her dosyada c1-c4 breakpoint sonuçları yazılır.
4. Kırıcı sızıntı testi "kısmi dosya okuryazarı" gibi değil, **başkasının
   crash'ine satın alma** olarak tanımlanır (runtime-error-truth.md).
5. Testlerde ADR/KD numaraları `UNKNOWN` değil, `.decisions/index.md`
   veya `os-kararlar.md` üzerinden referanslanır.

## §5 Kanıt

- os-dogrulama.md §1-§3 (PoC, ölçek, gate) · os-dogrulama.md §3 (breakpoint c1-c4)
- `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` — assertion deseni
- `shared/phpunit.xml` (dual-FD + bootstrap) · `.github/workflows/ci.yml:1` (PHASE-0)
- `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-208` (TS-O9 kaynağı)

## §6 ⚠️ Bilinen Bozuk Referanslar (salt rapor — DÜZELTME YASAK)

- `os-master.md` FM `depends_on: [K002-altyapi]` — diskte YOK (önceki rapor).
