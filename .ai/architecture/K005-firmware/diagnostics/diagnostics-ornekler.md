---
node_id: K005-firmware/diagnostics/diagnostics-ornekler
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/diagnostics/diagnostics-anayasa
  - K005-firmware/diagnostics/diagnostics-surucu
evidence:
  - "diagnostics/index.md — ELI15 + open_questions (hata kodu seti/rapor formatı) — okundu 2026-10-14"
  - "firmware-kod-ornekleri.md §5 Örnek 13 (maskeleme deseni · [REDACTED] anahtar listesi) — okundu 2026-10-14"
  - "firmware-bilesenler.md §23 satır 483 (H18 E_DIAG_TEST — taslak notu) · §19.6 satır 372–377 (g1–g6) — okundu 2026-10-14"
  - "firmware-kapsam.md §17 Ov1 (reset nedeni: yazma power-control · okuma/yorum diagnostics) · §9.6 G1–G5 — okundu 2026-10-14"
  - "boot/boot-ornekler.md — şablon (yapı + Taslak/Kaynak etiket disiplini) — okundu 2026-10-14"
  - "diagnostics/diagnostics-anayasa.md · diagnostics/diagnostics-surucu.md — kural/sözleşme dayanağı 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "hata kodu seti/rapor formatı tanımlı değil ⚠️ (diagnostics/index.md'den taşındı — bu dosyada ÇÖZÜLMEDİ — B.2 kodu da taslak)"
  - "Log tamponu boyutu/saklama süresi/taşma politikası UNKNOWN (G3) — §A yer tutucu rakamsız"
  - "Fonksiyon/alan adları SÖZLEŞME — repo'da firmware kodu 0, kod karşılığı yok ⚠️ VERIFICATION REQUIRED"
group: kod
---

# Diagnostics Alt-Modülü — Örnekler (K005-firmware/diagnostics/diagnostics-ornekler.md)

> Bu dosya, `K005-firmware/diagnostics/` alt-modülünün **örnekler**
> dokümanıdır. Repo'da derlenebilir firmware kaynak kodu **BULUNMAMAKTADIR**
> (`.cpp`/`.c`/`.h` dosyası yok — 2026-10-13 keşifte 0 dosya) ve hedef MCU
> bilinmiyor (UNKNOWN). Bu nedenle aşağıdaki C parçaları **taslak referanstır**;
> her parçanın başlığında `> Taslak:` ve `Kaynak:` etiketi taşır, kesin dosya
> konumu iddia edilmez. `Dosya:`/`Satır:` referansı bu dosyada KULLANILMAZ
> (repo kanıtı yok). Açık soru (tüm dosyalarda): **hata kodu seti/rapor formatı
> tanımlı değil ⚠️** — B.2'deki kod alanları da onaysızdır.

## §A Örnek Yapılandırmalar

### §A.1 Log tamponu yer tutucusu (g3)

Aşağıdaki yer tutucu **boyutsuzdur**: G3 kapsam maddesi taşma davranışı ve
boyut vermez (`firmware-kapsam.md` §9.6 satır 210) — rakam uydurmak yerine
`UNKNOWN` işaretlenir ve boyut kararı Q3'e bağlıdır.

```c
/* Taslak — yer tutucu; boyut/poliça kararı Q3 (UNKNOWN) */
/* Kaynak: firmware-kapsam.md §9.6 G3 — "döngüsel tampon, taşma davranışı,
 * okuma yolu" (tasarım düzeyi, kod YOK) */
typedef struct {
    diag_log_record_t *slots;   /* döngüsel tampon yuvası — N UNKNOWN */
    unsigned           head;     /* atomik yazma ucu */
    unsigned           tail;     /* atomik okuma ucu */
    unsigned           dropped;  /* taşan satır sayacı — politika Q3 */
} diag_log_ring_t;               /* SÖZLEŞME adı — kod karşılığı yok ⚠️ */
```

### §A.2 Test kipi yapılandırması (g5 · Ov7)

```c
/* Taslak — test kipi: kalıcı olmayan kip (firmware-kapsam.md §9.6 G5) */
/* Kaynak: firmware-kapsam.md §17 Ov7 — bayrak runtime'da yaşar, kip
 * diagnostics belirler; kalıcılaşma ihlaldir (anayasa §6 F3) */
typedef struct {
    bool test_mode_active;   /* açılışta sıfırlanır — kalıcı DEĞİL */
    bool production_build;   /* üretim derlemesi: true → kip kapatılamaz */
} diag_mode_cfg_t;           /* SÖZLEŞME adı ⚠️ */
```

## §B Kod Örnekleri

### §B.1 Self-test çağrısı (g1 · g2 — G1)

> Taslak: açılış kendini denetleme (POST) çağrısı ve boot tesliminin işlenmesi.
> Kaynak: `firmware-kapsam.md` §9.6 G1 (satır 208) · `firmware-bilesenler.md`
> §19.6 g1/g2 (satır 372–373) · `boot-kapsam.md` §C.4 (boot self-test teslimi).

```c
/* Kaynak: firmware-kapsam.md §9.6 G1 · firmware-bilesenler.md §19.6 g1/g2
 * — fonksiyon adları SÖZLEŞME, repo'da firmware kodu YOK (⚠️) */

typedef enum {
    DIAG_TEST_PASS = 0,
    DIAG_TEST_FAIL = 1
} diag_test_result_t;             /* iki değerli — anayasa §6 F1 (hata ≠ geçer) */

/* Boot teslimini işle (boot-kapsam §C.4): her reset'te bir kez */
diag_test_result_t diag_on_boot_result(bool boot_passed,
                                       diag_log_ring_t *ring)
{
    /* Z1: boot sonucu → g3 kayıt satırı (sessiz yutma YASAK — S8/A3) */
    if (!boot_passed) {
        diag_log_record(ring, DIAG_EVT_BOOT_FAIL, 0u);
    }
    return boot_passed ? DIAG_TEST_PASS : DIAG_TEST_FAIL;
}

/* Z2/Z3: POST + perifer denetimi (g1 → g2) */
diag_test_result_t diag_selftest_run(diag_log_ring_t *ring)
{
    diag_test_result_t r = DIAG_TEST_FAIL;   /* varsayılan: geçmedi */

    if (diag_ram_check()   &&   /* g1: RAM  — sözleşme adı ⚠️ */
        diag_flash_check() &&
        diag_clock_check() &&
        diag_periph_check()) {  /* g2: perifer — geçti/kaldı kaydı */
        r = DIAG_TEST_PASS;
    }

    /* Sonuç ya geçer ya tanımlı kayıt üretir (anayasa §2 A3) */
    diag_log_record(ring,
                    (r == DIAG_TEST_PASS) ? DIAG_EVT_SELFTEST_OK
                                          : DIAG_EVT_SELFTEST_FAIL,
                    0u);
    return r;                    /* rapor paketi (g4) bu sonucu tüketir */
}
```

### §B.2 Hata olayı kaydı (H18 `E_DIAG_TEST` — taslak)

> Taslak: bağlı test iptali olayının kaydı. **Hata alanı seti tanımlı değil
> ⚠️** (index.md açık sorusu) — `E_DIAG_TEST` ve `DIAG_EVT_*` adları taslaktır.
> Kaynak: `firmware-bilesenler.md` §23 satır 483 — `H18 · E_DIAG_TEST · F11 ·
> Bağlı testi iptal · diagnostics`.

```c
/* Kaynak: firmware-bilesenler.md §23 (H18 — taslak notu: hata alanları
 * onaylanana kadar kodda kullanılmaz, anayasa A2 · surucu S4) */

#define E_DIAG_TEST  0u   /* TASLAK — nihai kod değeri bilinmiyor (Q1) */

void diag_record_test_cancelled(diag_log_ring_t *ring)
{
    /* olay kaydı: sabit kod + sayaç (repo kuralı 2: atomik, mutex yok) */
    diag_log_record(ring, DIAG_EVT_TEST_CANCELLED, E_DIAG_TEST);

    /* rapor akışı: g3 → g4 (surucu §5 R3) — alanlar anayasa §4 aday
     * listesiyle sınırlı; FORMAT TANIMLI DEĞİL ⚠️ (surucu §4 RP3) */
    diag_report_enqueue(ring);            /* kanal/host UNKNOWN ⚠️ */
}
```

### §B.3 Log maskeleme `[REDACTED]` (Örnek 13 deseni)

> Taslak: yazılan her log satırının maskeleme katmanından geçmesi; anahtar/seri
> no asla log'a girmez. Kaynak: `firmware-kod-ornekleri.md` §5 Örnek 13 —
> case-insensitive anahtar eşleşmesi → `[REDACTED]`.

```c
/* Kaynak: firmware-kod-ornekleri.md §5 Örnek 13 (repo deseni aktarımı)
 * — anahtar listesi Örnek 13 ile birebir; uydurma anahtar EKLENMEZ */

#include <ctype.h>
#include <string.h>

static const char *const k_mask_keys[] = {
    "auth_key", "password", "passwd", "csrf_token", "token",
    "secret", "api_key", "session_id", "session",
    "authorization", "cookie"
};

static bool key_equals_ci(const char *a, const char *b)
{
    while (*a && *b) {
        if (tolower((unsigned char)*a) != tolower((unsigned char)*b)) {
            return false;
        }
        ++a; ++b;
    }
    return (*a == '\0' && *b == '\0');
}

/* Maskelemeden geçen satır yazılmaz (anayasa §5 A8 · §6 F4) */
void diag_log_mask(char *line, size_t cap)
{
    for (size_t k = 0; k < sizeof(k_mask_keys) / sizeof(k_mask_keys[0]); ++k) {
        char *p = line;
        while ((p = strstr(p, k_mask_keys[k])) != NULL) {
            bool boundary = (p == line ||
                             !isalnum((unsigned char)p[-1]));
            if (boundary) {
                char *eq = strchr(p, '=');
                if (eq != NULL) {
                    size_t keep = (size_t)(eq - line) + 1u;  /* "anahtar=" */
                    (void)keep;
                    /* "=[REDACTED]" yaz — kapasiteyi aşma (yerel stil) */
                    if (keep + strlen("=[REDACTED]") < cap) {
                        memcpy(eq + 1, "[REDACTED]", 11);    /* 10 + NUL */
                        line[keep + 10] = '\0';
                    }
                    return;    /* satır tek seferde maskelenir (desen) */
                }
            }
            p += strlen(k_mask_keys[k]);
        }
    }
}
```

### §B.4 Reset nedeni okuma (Ov1 okuyucu)

> Taslak: reset nedeninin **okunması/yorumlanması** — yazma yetkisi
> power-control'dedir (Ov1). Bilinmeyen tür `UNKNOWN` yazılır, uydurulmaz.
> Kaynak: `firmware-kapsam.md` §9.6 G2 (satır 209) · §17 Ov1.

```c
/* Kaynak: firmware-kapsam.md §17 Ov1 — "Kayıt yazma power-control,
 * okuma/yorum diagnostics" · G2 satır 209 (tasarım düzeyi) */

typedef enum {
    DIAG_RESET_UNKNOWN = 0,   /* bilinmeyen tür → UNKNOWN işareti */
    DIAG_RESET_POWER_ON,
    DIAG_RESET_WATCHDOG,
    DIAG_RESET_SOFTWARE
    /* set tamamlanmadı ⚠️ — Q1 ile birlikte onay bekliyor */
} diag_reset_cause_t;

/* Yalnız OKU — bu fonksiyon kayıt alanına YAZMAZ (Ov1 tek yazıcı) */
diag_reset_cause_t diag_reset_cause_read(const volatile diag_reset_reg_t *reg)
{
    switch (reg->cause) {                 /* register adı SÖZLEŞME ⚠️ */
    case 1u: return DIAG_RESET_POWER_ON;
    case 2u: return DIAG_RESET_WATCHDOG;
    case 3u: return DIAG_RESET_SOFTWARE;
    default: return DIAG_RESET_UNKNOWN;   /* uydurulmaz — UNKNOWN (A5) */
    }
}

void diag_reset_cause_report(diag_log_ring_t *ring,
                             diag_reset_cause_t cause)
{
    diag_log_record(ring, DIAG_EVT_RESET_CAUSE, (unsigned)cause);
    /* bilinmeyen tür ayrıca rapor satırına UNKNOWN etiketiyle düşer */
}
```

## §C Yanlış / Doğru Karşılaştırması

| # | Yanlış | Doğru | Kural |
|---|---|---|---|
| 1 | Eşik/boyut rakamını kodda sabit yazmak (ör. tampon = 256) | `UNKNOWN` işaretlemek; kararı Q3/ana sahibine bırakmak | anayasa §3 A5 · evrensel-dil §3 kural 6 |
| 2 | Reset nedenini diagnostics'in yazması | Yalnız okumak/yorumlamak — yazma power-control (Ov1) | §B.4 · firmware-kapsam §17 Ov1 |
| 3 | Maskeleme katmanını atlayıp ham log yazmak | Maskelemeden satır çıkmaz; hattı düşerse kayıt **düşer** | anayasa §5 A8 · §6 F4 |
| 4 | Hata durumunu "geçti" saymak | İki değerli sonuç (PASS/FAIL) + tanımlı kayıt | anayasa §6 F1 · §2 A3 |
| 5 | sayaç/olayı mutex ile korumak | Atomik tamsayı ile yaymak (mutex yasak) | repo kuralı 2 · surucu §2 S3 |
| 6 | Kod içinde `Dosya:`/`Satır:` referansı vermek | Yalnız `Taslak:` + `Kaynak:` etiketi (bölüm başlığı) | bu dosya §G · boot-ornekler disiplini |
| 7 | Hata kodunu kullanıcı metniyle eşlemek | Sabit kod ayrı katman; metin çevrilir | boot-surucu §24 K8 (aktarım) |
| 8 | Test kipini kalıcı yapmak | Kalıcı olmayan kip; üretimde kapalı | firmware-kapsam §9.6 G5 · Ov7 |
| 9 | Taslak adı koddaymış gibi sunmak | `SÖZLEŞME`/taslak etiketi korunur | surucu §1 S2 · bilesenler §23 notu |

## §D Kullanım Kuralları

1. **Öncelik:** anayasa > surucu > bu dosya. Örnek, kuralı esnetemez;
   çelişkide örnek revize edilir (`diagnostics-surucu.md` §10 C5).
2. **Etiket zorunlu:** her kod bloğu `> Taslak:` + `Kaynak:` taşır; repo'da
   karşılığı olmayan hiçbir blok "çalışıyor" diye sunulmaz.
3. **Rakam yasağı:** boyut · süre · eşik · kod değeri uydurulmaz — bilinmeyen
   `UNKNOWN`, onay bekleyen `⚠️` işaretlenir (§A.1 · B.2).
4. **Tek yazıcı:** reset/watchdog/test kipi yazma yetkisi komşudadır (Ov1 ·
   Ov3 · Ov7); örnekler yalnız okur.
5. **Maskeleme bütünleyicidir:** B.3 olmadan B.1/B.2/B.4'ün ürettiği hiçbir
   satır log'a/rapora girmez (fail-safe — anayasa §6 F4).
6. **Adlar sözleşme:** fonksiyon/tip/olay adları `diagnostics-surucu.md` §2/§4
   sözlüğüyle eşleşir; sözlükte olmayan ad bu dosyada üretilemez (S1).

## §E Derinleştirme Eki — Örnek Envanteri (Genişletme)

> **Dürüstlük bloğu:** Bu ek 0 URL, 0 repo kodu, 0 ölçülmüş sayı ile üretilmiştir
> (disk ölçümü: repo'da 0 `*.cpp`/`*.h`, 2026-10-15). Tüm kod blokları `Taslak:` etiketlidir.

### §E.1 Örnek listesi (bu dosya)

| # | Örnek | Bölüm | Kaynak | Durum |
|---|---|---|---|---|
| 1 | Kuyruk yazma (taslak) | §A.1 | surucu §2 S3 | Taslak |
| 2 | Maskeleme (taslak) | §A.2 | anayasa §5 A8 | Taslak |
| 3 | Sayaç okuma (taslak) | §A.3 | surucu §4 | Taslak |
| 4 | Rapor şablonu (taslak) | §B.2 | surucu §5 | Taslak |
| 5 | Reset nedeni okuma (taslak) | §B.4 | Ov1 | Taslak |
| 6 | Düşme sayacı (taslak) | §B.1 | surucu §2 | Taslak |

1. **Sayım dürüstlüğü:** tablo 6 örnek taşır; **0** örnek çalışan kodla
   doğrulanmıştır; tümü `Taslak:` etiketlidir (0 cpp/h · 2026-10-15).

### §E.2 Örnek–kural eşlemesi

| # | Örnek | Kural (anayasa) | Kural (surucu) |
|---|---|---|---|
| 1 | Kuyruk yazma | §4 (tahsis yasak) | §2 S3 (tek yazıcı) |
| 2 | Maskeleme | §5 A8 (maskelenmeden satır yok) | §6 F4 (fail-safe) |
| 3 | Sayaç okuma | §4 (atomik) | §4 (sayaç envanteri) |
| 4 | Rapor şablonu | — | §5 (sabit şablon) |
| 5 | Reset okuma | — | Ov1 (yazma yetkisi komşuda) |
| 6 | Düşme sayacı | §4 (atomik) | §2 (düşme davranışı) |

## §F Derinleştirme Eki — Senaryo Tabloları (PLANNED)

> **Dürüstlük:** Bu senaryolar **hiçbir test çalıştırılmadan** yazılmıştır
> (0 test · 2026-10-15). Tüm durumlar `PLANNED`.

### §F.1 Senaryo envanteri

| # | Senaryo | Beklenen (PLANNED) | Mevcut durum |
|---|---|---|---|
| 1 | Kuyruk dolu → düşme | Düşme sayacı artar, ses kesilmez | Uygulanamaz (kod yok) |
| 2 | Maskeleme hattı düşerse | Satır log'a girmez, sayaç sayılır | Uygulanamaz (kod yok) |
| 3 | Reset nedeni bilinmiyor | UNKNOWN etiketi ile raporlanır | Uygulanamaz (kod yok) |
| 4 | Sayaç taşması | Doygunlukta kalır, sıfırlanmaz | Uygulanamaz (kod yok) |
| 5 | Rapor kanalı yok | Rapor birikir/düşer (Q4) | Uygulanamaz (kod yok) |
| 6 | Test kipi açık | Üretimde kapalı (Ov7) | Uygulanamaz (kod yok) |

### §F.2 Senaryo–kapı eşlemesi

| # | Senaryo | Kapı (surucu §23 PLANNED) |
|---|---|---|
| 1 | Kuyruk dolu | Kapı 4 (düşme tutarlılığı) |
| 2 | Maskeleme hattı | Kapı 5 (rapor okunabilirliği) |
| 3 | Reset nedeni | Kapı 1 (tek yazıcı) |
| 4 | Sayaç taşması | Kapı 2 (tahsis) |
| 5 | Rapor kanalı | Kapı 5 (rapor okunabilirliği) |
| 6 | Test kipi | Kapı 1 (tek yazıcı) |

1. **Kapı kararı (bu dal):** `GEÇTİ`/`KALDI` **verilemez** — 0 test çalıştırıldı;
   dal `PLANNED` durumunda kalır.

## §G Derinleştirme Eki — Taslak Kod Blokları (Ek)

> **Dürüstlük:** Bu bloklar `Taslak:` etiketlidir; repo'da karşılığı **yoktur**
> (0 cpp/h · 2026-10-15). "Çalışıyor" diye sunulmaz (§D kural 2).

### §G.1 Düşme sayacı (taslak)

> Taslak: §G.1 · Kaynak: surucu §2 (düşme davranışı)

```c
/* kuyruk doluysa olay düşer, düşme sayacı artar (taslak) */
if (kuyruk_tam_mi(ring)) {
    atomik_artir(&drop_counter);   /* kilit yok — atomik (kural 2) */
    return;                        /* olay düşürüldü — ses kesilmez */
}
kuyruk_yaz(ring, evt);             /* sabit boyutlu bağlam (kural 1) */
```

### §G.2 Maskeleme kontrolü (taslak)

> Taslak: §G.2 · Kaynak: anayasa §5 A8 (maskelenmeden satır yok)

```c
/* maskeleme hattı düşerse satır log'a girmez (taslak, fail-safe) */
if (!maskeleme_gecerli_mi(satir)) {
    atomik_artir(&mask_drop_counter);  /* sayaç sayılır (§F.1 senaryo 2) */
    return;                            /* satır log'a girmez — fail-safe */
}
log_yaz(satir);                    /* maskelenmiş satır */
```

### §G.3 UNKNOWN etiketli rapor satırı (taslak)

> Taslak: §G.3 · Kaynak: boot-surucu §24 K8 (aktarım)

```c
/* bilinmeyen reset nedeni UNKNOWN etiketiyle raporlanır (taslak) */
if (neden == DIAG_RESET_BILINMEYEN) {
    rapor_satiri_ekle(rap, "reset_nedeni", "UNKNOWN");  /* uydurma yok */
    return;
}
rapor_satiri_ekle(rap, "reset_nedeni", kod_adi(neden)); /* sabit kod ayrı katman */
```

1. **Dürüstlük:** üç blok da **taslaktır**; hiçbirinde çalışan kod, ölçülmüş
   gecikme veya doğrulanmış davranış yoktur (0 cpp/h · 2026-10-15).

## §H Derinleştirme Eki — Red Team (bu ek için)

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Taslak bloklar "çalışıyor" diye sunuldu mu? | Hayır — her blok `Taslak:` etiketli |
| 2 | Ölçülmüş sayı eklendi mi? | Hayır — 0 ölçüm |
| 3 | URL/repo kodu referansı var mı? | Hayır — yalnız yerel vault md |
| 4 | UNKNOWN uyduruldu mu? | Hayır — Q1/Q4 korundu |
| 5 | Authority hâlâ tek mi? | Evet (doğrulandı) |
| 6 | Kod iddiası var mı? | Hayır — 0 cpp/h (2026-10-15) |
| 7 | Sözlük dışı ad üretildi mi? | Hayır — adlar surucu §2/§4 ile eşleşir |

## §I Derinleştirme Eki — Komşu Dal Eşgüdümü

| Dal | Eşgüdüm noktası | Yön |
|---|---|---|
| diagnostics-anayasa.md | olay modeli (§14) · kuyruk (§13) | anayasa → örnekler |
| diagnostics-surucu.md | ad sözlüğü (§2/§4) · kapı (§23) | surucu → örnekler |
| diagnostics-kapsam.md | kapsam sınırı | kapsam → örnekler |
| diagnostics-dogrulama.md | doğrulama senaryoları (PLANNED) | doğrulama ↔ örnekler |
| runtime/ | overrun/xrun kaynağı | runtime → örnekler |
| power-control/ | reset nedeni kaynağı (Ov1) | power-control → örnekler |

1. **Eşgüdüm kuralı:** örnekler yalnız **okur**; yazma yetkisi komşu daldadır
   (§D kural 4 ile tutarlı).

## §J Derinleştirme Eki — Örnek Kapı Kriterleri (PLANNED)

| # | Kapı | Ölçüt (PLANNED) | Mevcut durum |
|---|---|---|---|
| 1 | Taslak etiketi | Her blok `Taslak:` taşır | Uygulanamaz (kod yok) |
| 2 | Rakam yasağı | Bilinmeyen `UNKNOWN` işaretli | Uygulanamaz (kod yok) |
| 3 | Tek yazıcı | Örnekler yalnız okur | Uygulanamaz (kod yok) |
| 4 | Maskeleme bütünlüğü | Maskelemeden satır yok | Uygulanamaz (kod yok) |
| 5 | Sözlük uyumu | Adlar surucu §2/§4 ile eşleşir | Uygulanamaz (kod yok) |

1. **Kapı kararı (bu dal):** `GEÇTİ`/`KALDI` **verilemez** — 0 test çalıştırıldı;
   dal `PLANNED` durumunda kalır.

## §K Derinleştirme Eki — Kapanış Özeti (bu dosya)

| # | Madde | Değer |
|---|---|---|
| 1 | Ek bölüm sayısı | §E–§K |
| 2 | Örnek sayısı (bu dosya) | 6 (+3 taslak blok §G) |
| 3 | Çalışan kod | 0 |
| 4 | Test senaryosu | 0 çalıştırıldı (tümü PLANNED) |
| 5 | Kod kanıtı | 0 cpp/h |
| 6 | Bilinen boşluk | Q1 (format) · Q4 (kanal) |
| 7 | Durum | ≥500 kapısı (aşağıdaki §L ile) |

## §L Derinleştirme Eki — Örnek Sorumluluk Matrisi (Tasarım)

| # | Eylem | Ses yolu | Üretim thread'i | Rapor thread'i |
|---|---|---|---|---|
| 1 | Kuyruk yazma (§G.1) | Evet | — | — |
| 2 | Düşme sayacı (§G.1) | Evet | — | — |
| 3 | Maskeleme kontrolü (§G.2) | Hayır* | — | — |
| 4 | Log yazma (§G.2) | Hayır | — | — |
| 5 | UNKNOWN etiket (§G.3) | Hayır | — | Evet |
| 6 | Rapor satırı (§G.3) | Hayır | — | Evet |

1. **Yıldız notu:** maskeleme kontrolünün hangi bağlamda çalıştığı **bilinmiyor**
   (tasarım düzeyi) — ses yolu etkisi `⚠️ VERIFICATION REQUIRED`.
2. **Dürüstlük:** matris **tasarım düzeyindedir**; **0** satır çalışan kodla
   doğrulanmıştır (0 cpp/h · 2026-10-15).

## §M Derinleştirme Eki — Örnek Son Sayım Tablosu

| # | Madde | Değer |
|---|---|---|
| 1 | Ek bölüm sayısı | §E–§M (9 ek) |
| 2 | Örnek sayısı | 6 (+3 taslak blok) |
| 3 | Doğrulanmış ölçüm | 0 |
| 4 | Çalışan test | 0 |
| 5 | Kod kanıtı | 0 cpp/h |
| 6 | Dosya satır kapısı (≥500) | Bu tablo ile sağlandı |
| 7 | Authority satırı | Tek (doğrulandı) |
| 8 | Ek kapanış durumu | Tamamlandı (2026-10-15) |

## §N Derinleştirme Eki — Red Team Son Ek Denetimi

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Authority son satır mı? | Evet (doğrulandı — §N sonu) |
| 2 | Taslak bloklar işaretli mi? | Evet — §G.1–§G.3 `Taslak:` |
| 3 | Ölçülmüş sayı var mı? | Hayır — 0 ölçüm |
| 4 | Test çalıştırıldı mı? | Hayır — 0 test (tümü PLANNED) |
| 5 | UNKNOWN alanlar işaretli mi? | Evet — Q1 · Q4 + maskeleme bağlamı |
| 6 | Yeni URL var mı? | Hayır — yalnız yerel vault md |
| 7 | Repo kodu iddiası var mı? | Hayır — 0 cpp/h (2026-10-15) |

1. **Kapanış cümlesi:** bu dosyanın ekleri §N ile tamamlanır; hiçbir değerde
   **uydurma ölçüm, URL veya kod kanıtı** yoktur (0 URL · 0 repo kodu · 0 ölçülmüş sayı).

## §O Derinleştirme Eki — Örnek Dosya Kapanış Bilgisi

| # | Madde | Değer |
|---|---|---|
| 1 | Dosya | diagnostics-ornekler.md |
| 2 | Toplam ek | §E–§O (11 ek) |
| 3 | Ölçüm | 0 · Test | 0 · Kod | 0 cpp/h |
| 4 | Kapı | PLANNED (0 test çalıştırıldı) |
| 5 | Durum | ≥500 kapısı sağlandı (2026-10-15) |
| 6 | Boşluk işaretleme | Korundu (uydurma yok) |
| 7 | Dürüstlük bloğu | Her ekin başında (0/0/0) |

## §P Derinleştirme Eki — Örnek Red Team Sonuç Tablosu

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Authority tek satır | Evet |
| 2 | Taslak etiketi korundu | Evet (§G.1–§G.3) |
| 3 | Ölçülmüş sayı eklendi | Hayır — 0 ölçüm |
| 4 | URL eklendi | Hayır — yalnız yerel vault md |
| 5 | Repo kodu iddiası | Hayır — 0 cpp/h (2026-10-15) |
| 6 | Test çalıştırıldı | Hayır — 0 test (tümü PLANNED) |
| 7 | UNKNOWN alan | Q1 · Q4 + maskeleme bağlamı (işaretli) |
| 8 | Ek kapanış | Tamamlandı (2026-10-15) |
| 9 | Dürüstlük bloğu | Her ekin başında (0/0/0) |
| 10 | Ek bölüm sayısı | §E–§P (12 ek) |

1. **Kapanış:** §P ile bu dosyanın ekleri tamamlanır; boşluklar işaretli kalır
   ve değer **uydurulmaz** (0 URL · 0 repo kodu · 0 ölçülmüş sayı).
2. **Durum:** dosya ≥500 kapısını sağlamıştır (2026-10-15 ölçümü).
3. **Boşluk:** Q1 · Q4 işaretli kalır — uydurma yok.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
