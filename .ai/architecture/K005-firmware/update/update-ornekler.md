---
node_id: K005-firmware/update/update-ornekler
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware/update/update-anayasa
  - K005-firmware/update/update-surucu
  - K005-firmware/update/update-kapsam
  - K005-firmware/boot/boot-ornekler
  - K005-firmware/firmware-bilesenler
evidence:
  - .ai/architecture/K005-firmware/boot/boot-ornekler.md §B.4 · §B.5 · §C (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/device-control/device-control-ornekler.md §A (desen — okundu 2026-10-14)
  - .ai/architecture/K005-firmware/update/update-anayasa.md §2–§5 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/update/update-surucu.md §2–§10 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/update/update-kapsam.md §A–§D (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/firmware-bilesenler.md §19.7 (okundu 2026-10-14)
validation:
  - 2026-10-14 — FM 10 alan + group + son satır Authority denetimi
open_questions:
  - "update ↔ güvenlik (imza) kime ait? (K005 Q4) — bu dosyada ÇÖZÜLMEZ; imza örneklerinde algoritma adı yazılmaz"
  - "Hedef MCU ve repo firmware kodu yok (2026-10-13: 0 dosya) — örnekler taslaktır, derlenemez"
group: kod
---

# Update Alt-Modülü — Örnekler (K005-firmware/update/update-ornekler.md)

> Bu dosya, `K005-firmware/update/` alt-modülünün **örnekler** dokümanıdır. Repo'da
> derlenebilir firmware kaynak kodu BULUNMAMAKTADIR (2026-10-13 keşif: 0 dosya) ve hedef
> MCU bilinmiyor (UNKNOWN). Aşağıdaki C parçaları **`Taslak:`** etiketli **taslak
> referanstır**; her parçanın başında `Kaynak:` etiketi taşır ve kesin dosya konumu iddia
> edilmez. `Dosya:`/`Satır:` referansı bu dosyada KULLANILMAZ.

## §A Örnek Yapılandırmalar

### §A.1 Manifest doğrulama akışı (taslak)

```c
/* Taslak: manifest doğrulama — firmware-kapsam §9.7 U1 (sürüm · hedef donanım · imza · boyut).
 * Kaynak: update-kapsam.md §A.2 U1 · update-surucu.md §2 US2-VERIFY.
 * İmza ALGORİTMASI burada yazılmaz (Q4 — sahiplik security'de, açık soru). */

typedef struct {
    uint32_t version;        /* paket sürümü */
    uint32_t hw_target;      /* hedef donanım kimliği */
    uint32_t image_size;     /* boyut alanı */
    const uint8_t *sig_blob; /* imza blob'u — içeriği security'nin (Q4) */
} update_manifest_t;

int update_verify_manifest(const update_manifest_t *m)
{
    if (m == NULL || m->image_size == 0U) {
        return -1;                       /* U1 ayrıştırma red */
    }
    if (m->hw_target != HW_TARGET_ID) {
        return -2;                       /* hedef donanım uyuşmazlığı */
    }
    if (m->version <= current_fw_version()) {
        return -3;                       /* anti-rollback: geriye sürüm kurulmaz */
    }
    /* İmza denetimi burada çağrılır (u4 · F13 bağlantılı) — sonuç fail-safe:
     * hata ≠ geçerli (boot-kapsam §C.2 kuralı). Algoritma: Q4 → security. */
    if (update_call_signature_check(m->sig_blob) != 0) {
        return -4;
    }
    return 0;
}
```

### §A.2 A/B bölüm geçişi — switch yazımı (taslak)

```c
/* Taslak: geçiş (switch) — firmware-kapsam §9.7 U4 "yeni bölümün açılabilir ilan edilmesi".
 * Kaynak: update-kapsam.md §A.2 U4 · §D.2 §20.3 adım 5 (F12, sayaç++) ·
 *         update-surucu.md §3 UF5 boot_counter / §4 UT2. */

int update_commit_switch(void)
{
    /* 1) Yeni görüntü yazıldı ve doğrulandı (US3-WRITE kapısı — update-surucu §6). */
    if (update_image_is_verified() == 0) {
        return -1;
    }

    /* 2) Geçiş bayrağı: yalnız doğrulanmış image'dan sonra yazılır (U6 kuralı:
     *    yarıda kesme → işaret KONULMAZ, eski bölüm kalır). */
    if (update_set_pending_flag() != 0) {
        return -2;
    }

    /* 3) Deneme sayacı artırılır (boot-sequence §8.1 contador boot_counter aynası). */
    update_increment_boot_counter();

    /* 4) Sayaç++ ile yeniden başlatma (§20.3 adım 6 → F1). */
    update_request_reboot();
    return 0;
}
```

### §A.3 Güç-kesinti-güvenli yazma (taslak)

```c
/* Taslak: yarıda kesme kurtarma (u7) — firmware-kapsam §9.7 U6 "en kritik satır".
 * Kaynak: update-kapsam.md §A.2 U6 · update-surucu.md §5 UR5 · §6 KAPI-1/3.
 * Kalıp: boot-ornekler.md §B.4 flash_write_image (erase → program → CRC verify). */

int update_write_image_safe(const uint8_t *src, uint32_t len)
{
    /* Atılabilir (A/B) yuvaya yaz — eski yuvaya dokunma. */
    if (update_erase_inactive_slot() != 0) {
        return -1;
    }
    if (update_program_slot(src, len) != 0) {
        return -2;              /* yarıda kesildi → pending KONULMAZ (§6 KAPI-3) */
    }
    if (update_slot_crc_verify() != 0) {
        return -3;              /* doğrulanmadı → pending KONULMAZ */
    }
    /* Yalnız buraya kadar geldiyse pending işareti yazılır (US4-PENDING). */
    if (update_set_pending_flag() != 0) {
        return -4;
    }
    return 0;
}
```

### §A.4 Onay (confirm) — self-test sonrası (taslak)

```c
/* Taslak: onay bayrağı — update-surucu §4 UT4 (US7-CONFIRM_WAIT → US8-CONFIRMED).
 * Kaynak: update-surucu.md §2 US8 · boot-kapsam.md §C.1 "Confirm bayrağı (runtime → update)".
 * UYARI: onay akışı vault'ta ⚠️ tanımsız — bu taslak desen değil, kurgu iskeletidir. */

int update_confirm_application(void)
{
    if (update_selftest_passed() == 0) {
        return -1;                       /* self-test red → UR3 rollback */
    }
    if (update_write_confirmed_flag() != 0) {
        return -2;
    }
    update_clear_pending_flag();         /* pending=0 · confirmed=1 (US8) */
    update_reset_boot_counter();
    return 0;
}
```

### §A.5 Rollback — geri alma (taslak)

```c
/* Taslak: geri alma — update-surucu §8 KAPI · firmware-kapsam §9.7 U5.
 * Kaynak: update-surucu.md §5 UR3/UR4/UR5 · §2 US9-ROLLBACK ·
 *         boot-anayasa.md §8 sırası (retry ≤ 2 → yedek bank → kurtarma → hata kodu).
 * Geri alınan sürüm SADECE doğrulanmış eski sürümdür (anti-rollback ihlali reddedilir). */

int update_rollback(void)
{
    /* 1) Doğrulanmış eski image mevcut mu? (ota-update.md §7.2 — vault atfı:
     *    .ai/electronic/firmware/ota-update.md §7.2 Rollback Durum Tablosu) */
    if (update_old_image_available() == 0) {
        return -1;                       /* yok → kurtarma (boot tarafı) */
    }
    /* 2) Anti-rollback denetimi: eski image'ın sürümü güvenli eşiğin altındaysa
     *    KURULMAZ (boot-ornekler §B.5 aynası — FW_VERSION_MIN). */
    if (rollback_check() != 0) {
        return -2;
    }
    /* 3) Boot A8 retry ≤ 2 aşıldıysa yedek banka devir (boot-anayasa §8). */
    if (update_switch_to_old_slot() != 0) {
        return -3;
    }
    update_request_reboot();
    return 0;
}
```

### §A.6 Anti-rollback sayacı — update yüzeyi (boot-ornekler §B.5 aynası, taslak)

```c
/* Taslak: anti-rollback — Kaynak: boot-ornekler.md §B.5 (birebir aynı kalıp,
 * firmware-kapsam.md §17 Ov2: secure_version TEK YAZICI update).
 * Fark: burada güncelleme ÖNCESİ denetim (US2-VERIFY · UR6). */

#define FW_VERSION_MIN 1U

int update_rollback_check(void)
{
    uint32_t stored = flash_read_fw_version();

    if (stored < FW_VERSION_MIN)
    {
        return -1;
    }

    return 0;
}
```

## §B Uzatılmış Örnek — Güncelleme Akış İşleticisi (taslak kontrol akışı)

```c
/* Taslak: 7-adım güncelleme senaryosunun işleticisi —
 * Kaynak: firmware-bagimliliklar.md §20.3 (adım 1–7) · update-anayasa.md §2 zinciri ·
 *         update-surucu.md §2 durum makinesi (US1→US8).
 * Tasarım seviyesi: repo'da firmware kodu yok; fonksiyon adları TASLAKTIR. */

int update_run_cycle(void)
{
    /* Adım 1 — Manifest al (US1-FETCH). */
    if (update_fetch_manifest() != 0) { return -1; }

    /* Adım 2 — Bölme seç (K001 bölme haritası · Z7 zinciri). */
    if (update_select_partition() != 0) { return -2; }

    /* Adım 3 — İndir/doldur (güç kesilirse U6 → safe write §A.3). */
    if (update_download_image() != 0) { return -3; }

    /* Adım 4 — Doğrula (F13 + K001 E1k) — RED → adım 1'e dön (US10-ABORT). */
    if (update_verify_image() != 0) { return -4; }

    /* Adım 5 — Geçiş yaz (F12 · sayaç++ — §A.2). */
    if (update_commit_switch() != 0) { return -5; }

    /* Adım 6 — Yeniden başlat (F1 · §20.1) — bu çağrıdan sonra akış kesilir;
     *    kalan adım bir sonraki boot'ta çalışır. */
    update_request_reboot();
    return 0;

    /* Adım 7 — Açılışta doğrula (F1 + F13) — başarızsa rollback (US9-ROLLBACK · §A.5):
     *    run-after-boot tarafında: if (update_boot_verify() != 0) update_rollback(); */
}
```

## §C Kötü Uygulama Örnekleri

| # | Kötü | Neden kötü | İyi |
|---|---|---|---|
| 1 | İmza doğrulamayı atlayıp manifest'teki sürümü olduğu gibi kabul etmek | S14 ihlali — imzasız görüntü kurulur (firmware-kapsam §9.8 S14 · W10 REDDEDİLİR) | İmza + bütünlük olmadan yazma yok (US2-VERIFY) |
| 2 | `pending` işaretini yazmadan image yazmaya başlamak | Güç kesilirse yarım image + işaretsiz durum → U6 ihlali | Erase → program → CRC → SONRA pending (§A.3 sırası) |
| 3 | Rollback'te eski image'ı doğrulamadan kurmak | Bozuk/eski-altı image cihazı brick eder — risk 3 (firmware-riskler §2) | Doğrulanmış eski image + `rollback_check()` (§A.5) |
| 4 | `secure_version` alanını düşürerek "eski sürüme dönme" | Ov2 ihlali — anti-rollback sayacı yalnız güncellenir/artırılır | Yalnız artırma; düşürme yalnız boot'un geçiş kararında (Ov2: tek yazıcı update) |
| 5 | Onay (confirm) bayrağını self-test ÖNCESİNDE yazmak | Bozuk firmware kalıcı onay alır — UT3/UT4 sırası tersine döner | self-test → health → confirm (US6→US7→US8) |
| 6 | Update hata durumunu "geçti" olarak raporlamak | Ölçüm/kanıt sahteliği — hata ≠ geçerli (fail-safe) | Kod döndürür; durum Tasarım seviyesi/ölçüm PLANNED olarak kalır |

## §D Karşılaştırmalı Örnekler

| Kriter | A/B (dual-bank) yolu | Tek-bank / dosya yolu | Update etkisi |
|---|---|---|---|
| Yazma hedefi | Atılabilir yuvaya yaz (§A.3) | Geçici dosya + atomik rename | Hangisi geçerli: firmware-kapsam §9.7 A/B (dual-bank) der; tek-bank varyantı UNKNOWN (update-surucu §6-1) |
| Geri alma | Yedek yuvaya boot devri (§A.5) | İndirilmiş eski paketi yeniden kur | İkisinde de doğrulanmış eski sürüm şart (UR5) |
| Sayaç | `boot_counter` ++ · `rollback_counter` (UF4/UF5) | Dosya bayrağı | boot-sequence §8.1 · firmware-kapsam §17 Ov2 aynası |
| Yarım yazma | Bayrak konulmaz → eski yuva kalır | rename olmadan eski dosya kalır | U6 "en kritik satır" her iki yolda da korunur |
| İmza | u4 · F13 bağlantılı (Q4) | Aynı | Yol ne olursa olsun imza reddi → eski bölüm (firmware-bagimliliklar §20.4) |

## §E CI Entegrasyon Notu

- Repo'da firmware derleme hattı YOKTUR (2026-10-13: 0 kod kanıtı) — bu örneklerin
  derlenmesi/çalıştırılması **ölçüm PLANNED**.
- İlgili köprü: `update-dogrulama.md` DV-U senaryoları (güncelleme/geri alma · yarım
  yazma · güç kesintisi) tasarım seviyesinde tanımlıdır; kod-kanıtlı test yoktur.

## §F Sık Sorulanlar

| # | Soru | Cevap |
|---|---|---|
| Q1 | İmza algoritması bu örnekte neden yazılmadı? | Q4 açık — update ↔ güvenlik (imza) kime ait? (update-kapsam §A.3 · §C.2); algoritma adı `UNKNOWN`, uydurulmaz |
| Q2 | Örnekler derlenir mi? | Hayır — repo'da firmware kodu yok; `Taslak:` + `Kaynak:` etiketlidir |
| Q3 | Hangi MCU/register kullanılıyor? | UNKNOWN — register adı/addr iddia edilmez (firmware-evrensel-dil §5) |
| Q4 | Tek-bank yolu da mı geçerli? | firmware-kapsam §9.7 A/B (dual-bank) der; tek-bank yalnız §D karşılaştırmada, durum UNKNOWN |


---

## §G Durum Makinesi Örnekleri (US-etrafında — taslak)

### §G.1 Update işleticisi durumları (özet kartı)

| Durum | Tetik | Çıkış | Güç-kaybı (şartname) |
|---|---|---|---|
| IDLE | istek | INDIRME | etkisiz |
| INDIRME | bayt akışı | DOGRULA | yarım dosya → sıfırla |
| DOGRULA | yerel hazır | KUR/RED | karar yok → sıfırla |
| KUR | onay | BEKLE-ONAY | slot geçersiz işaretle |
| BEKLE-ONAY | boot olayı | ONAY/GERI-ALMA | etiket beklemede |
| ONAY | etiket | KAPANDI | — |
| GERI-ALMA | tetik/hata | KAPANDI (eski slot) | eski slot korunur |

### §G.2 Geçiş tablosu (durum × olay)

| ↓ \ olay | istek | bayt bitti | doğrula_ok | doğrula_err | boot_ok | boot_err |
|---|---|---|---|---|---|---|
| IDLE | INDIRME | — | — | — | — | — |
| INDIRME | — | DOGRULA | — | — | — | — |
| DOGRULA | — | — | KUR | KAPANDI(RED) | — | — |
| KUR | — | — | — | — | BEKLE-ONAY | — |
| BEKLE-ONAY | — | — | — | — | ONAY | GERI-ALMA |
| ONAY/GERI-ALMA | IDLE | — | — | — | — | — |

> Geçiş tablosu **taslaktır**; gerçek enum/değerler kod yokken sabitlenmez.

---

## §H Kötü Uygulama Örnekleri — Genişletme (YAPILMAYACAK)

```c
/* YAPILMAYACAK — imza kararı olmadan yazım (U-F2 ihlali). */
download(url, buf);
write_slot(buf);          /* doğrulama YOK — kapı-2 ihlali */
```

```c
/* YAPILMAYACAK — kendi kendine onay (U-P6 ihlali). */
write_slot(new_img);
set_label(SLOT, INSTALLED);   /* boot sonucu beklemeden onay */
```

```c
/* YAPILMAYACAK — yazdıktan sonra işaretleme (U-P7 ihlali). */
write_slot_partial(buf);      /* güç burada kesilirse: NE slotta NE eski */
set_label(SLOT, INVALID);     /* işaretleme SONRA — geç */
```

```c
/* YAPILMAYACAK — rollback'te sayaç düşürme (U-P8 ihlali). */
counter_write(CNT, ver - 1);  /* anti-rollback baypas */
activate_slot(OLD);
```

```c
/* YAPILMAYACAK — kurtarma başlatma (U-P9 ihlali). */
if (update_failed()) enter_recovery_mode();  /* recovery sahibi BOOT */
```

---

## §I Sağlam Kalıp Örnekleri — Genişletme (TASLAK)

```c
/* TASLAK — kod yok (cpp/h = 0 · 2026-10-15). */
if (!manifest_verify(m)) return RED;         /* u2 — kapı-1 */
if (!signature_check(m, sig)) return RED;    /* u6/V1-V5 — kapı-2 (Q4/T5 beklemede) */
mark_invalid(TARGET);                        /* U-P7 — önce işaretle */
write_slot(TARGET, img);                     /* u3 — hedef slot */
set_label(TARGET, PENDING);                  /* U-F4 — beklemede */
/* ... ilk boot ... */
if (boot_ok()) set_label(TARGET, INSTALLED); /* U-F5 — yalnız boot sonucuyla */
else           request_rollback();           /* H-U5 */
```

```c
/* TASLAK — güç-kaybı zinciri (DV-U12/U13 şablonu). */
/* indirme kesilirse: slot etiketleri DEĞİŞMEZ */
/* yazma kesilirse:  hedef slot geçersiz · eski slot kurtarıcı */
```

---

## §J Betik İskeletleri (taslak — depoda YOKTUR)

```powershell
# Taslak: DV-U12 indirme kesintisi iskeleti — depoda bu betik YOKTUR.
# Kanit: 2026-10-15 envanter — firmware test altyapisi yok (G9 tanimsiz).
# Kaynak: update-dogrulama.md §H.3 · ota-update.md §12 [Satır: 237].
# Adimlar: fixture hazirla -> indirmeyi baslat -> kes -> etiket oku -> defter
```

```powershell
# Taslak: DV-U13 yazma kesintisi iskeleti — UX3 (HIL) gerekir; BLOKE damgali.
# beklenen: hedef slot invalid · eski slot aktif · boot sonucu defterde
```

```powershell
# Taslak: kapı raporu satiri (kapi-1..6) — durum: PASS/FAIL/BLOKE/BELIRSIZ
# kapi-2 icin zorunlu not: T5/Q4 acikken BELIRSIZ/BLOKE (gizleme yasak)
```

---

## §K Fixture Listesi (şartname — uygulama yok)

| Kod | Fixture | Kart | Üretim |
|---|---|---|---|
| FX-U1 | imzasız image | DV-U15 | mutasyon |
| FX-U2 | bozuk imzalı image | DV-U11 | mutasyon + test imzalama |
| FX-U3 | yarım indirme | DV-U12/U16 | kesme (taslak betik) |
| FX-U4 | iki kurulu slot | DV-U18 | etikel elleme (UX3) |
| FX-U5 | eski sürüm | DV-U14 | sürüm sabitleme |
| FX-U6 | dolu NVS | DV-U19 | ayar üretici (taslak) |
| FX-U7 | boot-lock etkin | DV-U20 | boot arayüzü (mock) |

1. Envanter her çalıştırmadan önce sayılır; eksik = kart BLOKE.
2. Değerler okunarak yazılır (uydurma imza/sayaç yasak).

---

## §L Örnek ↔ Kapı ↔ Senaryo Köprüsü (özet)

| Örnek | Kapı | Senaryo |
|---|---|---|
| §H (imzasız yazım) | kapı-2 | DV-U15 |
| §H (kendi-kendine onay) | kapı-4 | DV-U10 |
| §H (işaretleme sonra) | kapı-3 | DV-U13 |
| §H (sayaç düşürme) | kapı-5 | DV-U14 |
| §H (kurtarma başlatma) | kapı-4 | DV-U20 |
| §I (tam zincir) | kapı-1…5 | DV-U10/U11/U14 |
| §J (kesinti iskeletleri) | kapı-3 | DV-U12/U13 |

---

## §M Sık Sorulanlar (bu dosya)

| Soru | Yanıt |
|---|---|
| Betikler çalışır mı? | Hayır — taslak damgalı; depoda firmware altyapısı yok |
| Enum/değerler kesin mi? | Hayır — kod yok; sabitleme ADR ister |
| İmza örneği hangi kütüphane? | T5 SEÇİLMEDİ — ad uydurmak yasak (⚠️ VERIFICATION REQUIRED) |
| NVS örneği hangi donanım? | Bilinmiyor — donanım adı vault kaynaklı olmalı |
| UX3 neden BLOKE? | G9 workflow tanımsız (firmware-test-stratejisi §3) |
| Sonraki üretim ne? | T5/Q4/G9 sonrası gerçek sonuç satırları (append) |

---

## §N Kapanış Kontrolü (bu ek)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölümler | §G–§N |
| Silinen mevcut içerik | 0 (append-only) |
| Frontmatter | korundu (10 alan + group: kod) |
| Taslak damgasız blok | 0 |
| Çalıştırılmış iddiası | 0 |
| Son satır Authority | birebir korunur |

## §O Örnek Kütüphanesi — Vault OTA Dosyası Derinletme (append · 2026-10-16)

> Kaynak: `Dosya: .ai/electronic/firmware/ota-update.md` (524 satır, okundu 2026-10-16). Bu ekteki örnekler TASLAK/şartnamedir; **çalıştırılmış kod iddiası 0**, depoda `*.cpp/*.h/*.c` YOK (2026-10-15 ölçümü).

### §O.1 Örnek — Rollback Test Senaryosu Seti (US-etkisi)

| # | Örnek senaryo | Tetikleyici | Beklenen sonuç | Dayanak |
|---|---|---|---|---|
| R1 | Yeni firmware boot olmaz (3 deneme) | boot contador eşiği | eski yuva boot eder | ota-update.md:476 §25.1 |
| R2 | Canary penceresinde watchdog | reset beklenmedik | rollback + log | ota-update.md:478 §25.1 |
| R3 | Sağlık kontrolü timeout | alive ping yok | rollback | ota-update.md:479 §25.1 |
| R4 | NVS migrate hatası | şema uyuşmazlığı | rollback — NVS KORUNUR | ota-update.md:480 §25.1 |
| R5 | Rollback sonrası tekrar güncelleme | ikinci deneme | temiz başarı | ota-update.md:481 §25.1 |

### §O.2 Örnek — Kapı (KAPI) Kabul Kriteri Şablonu

| Kapı | Örnek kabul kriteri | Red koşulu | Dayanak |
|---|---|---|---|
| KAPI-İmza | RSA-2048 doğrulama başarılı | herhangi bir bayt uyuşmazsa reddet | ota-update.md:47 §3 |
| KAPI-Yazma | inactive yuvaya TAM yazım + verify | yarım yazım → bayrak ATILMAZ | ota-update.md:237 §12 |
| KAPI-Commit | flush+verify sonrası bootable bayrağı | verify başarısız → bayrak yok | ota-update.md:249 §12.1 |
| KAPI-Boot | boot contador ≤ eşik | eşiği aş → eski yuva | ota-update.md:360 §19.1 |
| KAPI-Sağlık | canary penceresi içinde temiz boot | pencere içi reset → rollback | ota-update.md:190 §9.1 |

### §O.3 Örnek — Doğrulama Test Matrisi (9 satır, vault birebir)

| Test | Senaryo | Kabul kriteri | Dayanak |
|---|---|---|---|
| Normal güncelleme | İmzalı paket, sağlıklı ağ | yeni sürüm aktif | ota-update.md:462 §25 |
| İmza reddi | Bozuk/sahte imza | reddi + eski sürüm | ota-update.md:463 §25 |
| Güç-kesintisi (yazma) | yazma sırasında reset | aktif yuva değişmez | ota-update.md:464 §25 |
| Güç-kesintisi (commit) | bayrak öncesi reset | eski yuva boot | ota-update.md:465 §25 |
| Boot döngüsü | yeni firmware boot olmaz | watchdog → rollback | ota-update.md:466 §25 |
| Sağlık kontrolü | canary penceresinde reset | otomatik rollback | ota-update.md:467 §25 |
| Anti-rollback | eski sürüm paketi | reddi | ota-update.md:468 §25 |
| Ağ kesintisi (resume) | indirme yarıda | kaldığı yerden devam | ota-update.md:469 §25 |
| Uyumsuz donanım | yanlış hardware_id | reddi | ota-update.md:470 §25 |

### §O.4 Örnek — Flash Bölümleme Yerleşimi (tipik)

| Bölüm | Boyut (tipik) | İçerik | Yazma sıklığı | Dayanak |
|---|---|---|---|---|
| Boot ROM / loader | sabit | donanıma bağlı | salt-okunur | ota-update.md:318 §18 |
| Recovery image | 1× firmware | kurtarma | nadiren | ota-update.md:319 §18 |
| Slot A | 1× firmware | aktif/pasif | güncelleme | ota-update.md:320 §18 |
| Slot B | 1× firmware | aktif/pasif | güncelleme | ota-update.md:321 §18 |
| NVS / config | küçük | kalıcı ayar, sayaç | sık | ota-update.md:322 §18 |
| Scratch | değişken | geçici tampon | sık | ota-update.md:323 §18 |

İlkeler (ota-update.md:327 §18.1): A ve B aynı boyutta ve bağımsız · NVS firmware yuvalarından ayrı · recovery nadiren yazılır ve imzalıdır · anti-rollback counter donanımda/OTP.

### §O.5 Örnek — Kademeli Dağıtım (canary) Oranları

| Aşama | Oran | Amaç | Kapı | Dayanak |
|---|---|---|---|---|
| Canary | %1–5 | erken sinyal toplama | hata oranı eşiği | ota-update.md:201 §10 |
| Early | %10–25 | genişletilmiş doğrulama | sağlık metrikleri | ota-update.md:202 §10 |
| Broad | %50–90 | ana yayılım | telemetri | ota-update.md:203 §10 |
| Full | %100 | tamamlanma | — | ota-update.md:204 §10 |

Kohort ayrımı (donanım revizyonu / bölge / beta) ota-update.md:212 §10.2 — sorun kohortta izole kalır.

### §O.6 Örnek — Kısmi İndirme (resume / delta) Kalıbı

| Teknik | Açıklama | Avantaj | Dayanak |
|---|---|---|---|
| Chunked download | paket parçalara bölünür | kesintide kaldığı yerden devam | ota-update.md:374 §20 |
| Resume token | ilerleme saklanır | baştan başlamaz | ota-update.md:375 §20 |
| Delta (binary diff) | yalnız değişen baytlar | bant genişliği tasarrufu | ota-update.md:376 §20 |
| Local staging | indirilen paket yerelde | yeniden doğrulama | ota-update.md:377 §20 |
| Retry + backoff | üstel geri çekilme | dayanıklılık | ota-update.md:378 §20 |

⚠️ VERIFICATION REQUIRED (ota-update.md:384 §20.1): delta'da imza TAM paket üzerinde doğrulanır; taban sürüm uyuşmazsa delta reddedilir → tam paket iner.

### §O.7 Örnek — Filo Rolleri (update server tarafı)

| Rol | Sorumluluk | Dayanak |
|---|---|---|
| Update server | firmware + manifest barındırır, imzalar | ota-update.md:392 §21 |
| Enrollment / device registry | cihaz envanteri + mevcut sürüm | ota-update.md:393 §21 |
| Rollout orchestrator | kademeli dağıtım kararı | ota-update.md:394 §21 |
| Telemetry ingest | cihaz sağlık metrikleri | ota-update.md:395 §21 |
| Rollback coordinator | hatalı sürüm geri çekme | ota-update.md:396 §21 |

hardware_id kontrolü (ota-update.md:402 §21.1): uyumsuz donanım revizyonuna yanlış firmware gönderilmez.

### §O.8 Kötü Uygulama — Yapılmayacaklar (§C/§H ile aynı disiplin)

| # | Yapılmayacak | Sonuç | Dayanak |
|---|---|---|---|
| K1 | Tek yuvaya doğrudan yazım | güç kesintisinde brick | ota-update.md:110 §6.2 |
| K2 | TLS'yi firmware imzası yerine koymak | MITM'de sahte image kabul | ota-update.md:194 §8.2 |
| K3 | bootable bayrağını yazmadan önce atmak | yarım image boot edilir | ota-update.md:249 §12.1 |
| K4 | Donanımsal sayaç olmadan anti-rollback iddiası | eski/zayıf sürüm geri yüklenir | ota-update.md:143 §7.1 |
| K5 | Delta'yı imzalamak (tam paket yerine) | taban uyuşmazlığında doğrulama kırılır | ota-update.md:384 §20.1 |

### §O.9 Kapanış (bu ek)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölüm | §O (append-only; §A–§N silinmedi) |
| Taslak damgasız "çalıştırıldı" iddiası | 0 |
| Silinen mevcut içerik | 0 |
| Frontmatter | korundu (10 alan + group: kod) |
| Dayanak formatı | her satır `Dosya` satır referanslı |


**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
