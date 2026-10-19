---
node_id: K005-firmware/security/security-ornekler
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/security/index
  - K005-firmware/security/security-anayasa
  - K005-firmware/security/security-surucu
evidence:
  - "K005-firmware/boot/boot-ornekler.md — lettered iskelet + Taslak/Kaynak disiplini + Dosya/Satır yasağı — okundu 2026-10-14"
  - "K005-firmware/boot/boot-ornekler.md §B.5 — anti-rollback read-only taslak (rollback_check · FW_VERSION_MIN 1U) — okundu 2026-10-14"
  - "K005-firmware/boot/boot-surucu.md §2 F4/§6 E4/§3 ST5-ST16 — bayrak ve hata adları — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md satır 395-400 (s1-s6) · 479/481 (H14/H16) · 442 (G8) — okundu 2026-10-14"
  - "K005-firmware/firmware-kod-ornekleri.md satır 303 — ADR referans gerçek konumu — okundu 2026-10-14"
  - "K005-firmware/firmware-kapsam.md §17 Ov2 (tek yazıcı=update) — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "boot ↔ security sınırı ve imza akışı tanımsız ⚠️"
  - "T5 imza algoritması SEÇİLMEDİ — §B/§C imza çağrıları primitive'siz taslaktır (UNKNOWN)"
  - "Hedef MCU/derleme zinciri UNKNOWN — kod parçaları derlenebilir iddia edilmez"
group: kod
---

# Security Alt-Modülü — Örnekler (K005-firmware/security/security-ornekler.md)

> Bu dosya, `K005-firmware/security/` alt-modülünün **örnekler**
> dokümanıdır. Repo'da derlenebilir firmware kaynak kodu
> **BULUNMAMAKTADIR** (`.cpp`/`.c`/`.h` dosyası yok — 2026-10-13 keşifte
> 0 dosya) ve hedef MCU bilinmiyor (UNKNOWN). Bu nedenle aşağıdaki C
> parçaları **taslak referanstır**; her parçanın başlığında `Taslak:`
> ve `Kaynak:` etiketi taşır ve kesin dosya konumu iddia edilmez.
> Kesin dosya konumu / satır numarası referansı bu dosyada KULLANILMAZ
> (repo kanıtı yok).

## §A İmza Denetimi Çağrısı (taslak)

### §A.1 security tarafı — doğrulayıcı akışı (V1-V5 sırası)

```c
/* Taslak: imza doğrulama akışı — anayasa §3 (V1-V5 sırası bağlayıcı).
   Kaynak: security-anayasa.md §3 + firmware-bilesenler.md s1/s2/s4 birimleri
   (dalga-3) — repo'da firmware kodu yok, adlar sözleşme düzeyinde. */
#include <stdbool.h>
#include <stdint.h>

typedef enum { IMZA_GECTI = 0, IMZA_KALDI = 1, IMZA_BELIRSIZ = 2 } imza_sonuc;

/* Fail-closed: belirsizlik asla GECTI sayılmaz (anayasa SA5/SA6). */
static imza_sonuc imza_dogrula(const uint8_t *gorsel, uint32_t uzunluk,
                               uint8_t anahtar_no)
{
    if (gorsel == 0 || uzunluk == 0u) return IMZA_BELIRSIZ;   /* SI1 girdi denetimi */
    if (anahtar_erişim_red(anahtar_no)) return IMZA_BELIRSIZ;  /* s2 — SI3 */
    if (zincir_dogrula_kok_guven() != 0) return IMZA_KALDI;    /* s1 — SI2 (H16) */
    bool imza = imza_yonetimi_dogrula(gorsel, uzunluk, anahtar_no);
    if (!imza) return IMZA_KALDI;                              /* geçersiz → reddet */
    if (surum_denetle(gorsel_surum(gorsel)) != 0) return IMZA_KALDI; /* s4 — AR3 */
    return IMZA_GECTI;                                         /* V5'e hazır */
}

/* F4 yalnız tam sonuçta yayılır — kısmi sonuç yasak (anayasa SA4 · SS5). */
void security_boot_cagri_yaniti(imza_sonuc sonuc)
{
    if (sonuc == IMZA_GECTI) {
        bayrak_set(F4_FW_SIG_OK);            /* tek üretici: security (SS3) */
    } else {
        hata_yay(E4_IMZA_HATASI);            /* boot: ST5 → ST16 HALT */
        hata_yay(H16_E_SEC_CHAIN);           /* security taslağı — §D notu ⚠️ */
    }
}
```

### §A.2 Boot tarafı — çağrıyı bekleyen akış (referans)

```c
/* Taslak: boot'un security çağrısı bekleme kolu — ST5 SIGN_CHECK.
   Kaynak: boot-surucu.md §3 (ST5 → E4 → ST16) + §4 TR4/TR5 sözleşmeleri. */
if (f4_fw_sig_ok_oku()) {
    durum_gec(ST6_HW_INIT);        /* imza geçti — TR5 garantisi */
} else {
    hata_kolu(E4);                 /* TR17: → ST16 HALT (yalnız güvenlik olayı) */
}
/* Dikkat: çağrının GİRDİ paketi (görsel nasıl iletilir) TANIMSIZ ⚠️ —
   boot ↔ security sınırı açık sorudur (index.md), bu taslak onu çözmez. */
```

## §B İmza/Anahtar Kasası Arayüzü (taslak — primitive'siz)

```c
/* Taslak: anahtar erişim denetimi (s2) — ham anahtar dışarı ÇIKMAZ.
   Kaynak: security-anayasa.md SA3 + firmware-bilesenler.md s2 (dalga-3).
   NOT: imza algoritması T5 SEÇİLMEDİ (UNKNOWN) — aşağıdaki çağrıların
   altı DOLDURULMAMIŞTIR; "firmware şunu kullanır" cümlesi yazılmaz. */
typedef struct { uint8_t anahtar_no; uint8_t seviye; } anahtar_istek;

bool anahtar_erişim_red(const anahtar_istek *istek)
{
    if (istek == 0) return true;                       /* fail-closed */
    if (uretim_derlemesi_degil()) return true;         /* A7: üretimde kapı kapalı */
    return yetki_denetle(istek->seviye) != 0;          /* s3 — SI4 */
    /* ⚠️ T5: imza primitive + anahtar uzunluğu SEÇİLMEDİ — UNKNOWN */
}
```

## §C Flaş Yazma Kilidi ve Anti-rollback (salt okuma)

### §C.1 Yazma kilidi — security flash'a YAZMAZ

```c
/* Taslak: yazma yetkisi tekelleşir — tek yazıcı UPDATE.
   Kaynak: security-anayasa.md SA2 + firmware-kapsam.md §17 Ov2. */
int security_flash_yaz(const void *veri, uint32_t boyut)
{
    (void)veri; (void)boyut;
    return -1;   /* her koşulda RED — güvenlikçi flaşa yazmaz (kasıtlı) */
}
/* update modülü tek yazıcıdır; boot ve security yalnız okur. */
```

### §C.2 Anti-rollback — salt okunur denetim (read-only)

```c
/* Taslak: anti-rollback OKUMA tarafı — sayacı bu fonksiyon YAZMAZ.
   Kaynak: boot-ornekler.md §B.5 (rollback_check mantığı — Kaynak: boot-dogrulama
   B-04) + security-anayasa.md §4 (AR1-AR4). Sayaç YAZMA sahibi belirsiz ⚠️
   (security-surucu §4 M2 — update vs security, ÇÖZÜLMEDİ). */
#define FW_VERSION_MIN 1U   /* sayacın okunan minimum eşiği (taslak) */

int rollback_check(uint32_t gelen_surum)
{
    uint32_t sayaç = anti_rollback_sayac_oku();   /* salt okuma */
    if (gelen_surum < FW_VERSION_MIN) return -1;  /* eşik altı: red */
    if (gelen_surum < sayaç)          return -1;  /* AR1: sürüm düşürmeye red */
    return 0;
    /* Geçerli imza + eski sürüm = RED (AR3) — imza tek başına yetmez. */
    /* Boot tarafı okuma/uygulama: boot-ornekler §B.5 · BOOT_E003 köprüsü. */
}
```

## §D Kırmızı Çizgiler — Kötü Uygulama Örnekleri

```c
/* YASAK 1 — fail-open: belirsizliği "geçti" saymak. */
if (imza_dogrula(...) != IMZA_GECTI) { /* ... */ }
else { ac(); }                 /* ✗ IMZA_BELIRSIZ de "else" düşer → asla aç */

/* YASAK 2 — security'nin flaşa yazması (SA2 ihlali). */
security_flash_yaz(paket, n);  /* ✗ her koşulda -1 döner (§C.1) */

/* YASAK 3 — ham anahtarın log/olaya çıkması (SA3). */
olay_yay("anahtar: %s", anahtar);  /* ✗ yalnız metadata (anahtar_no) */

/* YASAK 4 — OWASP web kuralının firmware'e otomatik uygulanması. */
/* ✗ "firmware Argon2id/AES-256-GCM kullanır" — T5 SEÇİLMEDİ iken bu bir
   iddia değil uydurmadır; kaynaklı ATIF serbest, uygulama iddiası yasak
   (anayasa §7 ilke 6). */

/* YASAK 5 — boot sınırını uydurarak kapatmak. */
imza_cagrisi_protokolu_biliniyor;  /* ✗ index.md açık sorusu AÇIK kalır */
```

## §E Web Araştırma Tablosu

| # | Kaynak | Amaç | Tarih | Durum |
|---|---|---|---|---|
| 1 | https://csrc.nist.gov/pubs/sp/800/193/final | Protection/Detection/Recovery prensipleri (SA5/SA6 karşılığı) | 2026-10-14 | ✅ erişildi |
| 2 | https://docs.mcuboot.com/design.html | A/B slot · anti-rollback · imza TLV karşılığı | 2026-10-14 | ✅ erişildi |
| 3 | https://theupdateframework.github.io/specification/latest/ | güncelleme imzası / metadata bağlama | 2026-10-14 | ✅ erişildi |
| 4 | https://www.rfc-editor.org/rfc/rfc8032 | EdDSA spesifikasyonu (T5 seçenek adayı referans) | 2026-10-14 | ✅ erişildi |
| 5 | https://cheatsheetseries.owasp.org/ | atıf kaynağı — firmware'e otomatik uygulanmaz | 2026-10-14 | ✅ erişildi |
| 6 | https://www.psacertified.org/security-certification | PSA güvenlik sınıflandırması referansı | 2026-10-14 | ✅ erişildi |

> Not (K-9/K-14 çelişki notu): bu 6 URL boot-dogrulama §E /
> firmware-web-arastira §3.2 ile **paylaşımlıdır**; boot dosyaları da
> aynı havuzu tekrar eder (mevcut emsal). URL tek-yazır kuralına uyum:
> her satır kendi tarihi ve atıfıyla taşınır — kopya sayılmaz, kaynak
> belirtilir.

## §F C++ Örneği — Güvenli Dur Sınıfı Taslağı

```cpp
/* Taslak: fail-safe kilit kapsayıcısı (RAII benzeri) — güvenli dur.
   Kaynak: security-anayasa.md §6 (SA5/SA6) + boot-dogrulama.md §C.3
   (çağrı hatası → fail-safe, S9 asla S10) — repo'da derlenebilir kod yok. */
#include <cstdint>

class GuvenliDurKilit {
public:
    explicit GuvenliDurKilit(bool imza_gecti) noexcept
        : acilabilir_(imza_gecti) {}            /* belirsiz=false → fail-closed */

    bool acabilir_mi() const noexcept { return acilabilir_; }

    void kilitli_mi_bildir(std::uint32_t olay_kodu) noexcept {
        /* E4/H16 olayını diagnostics'e yollar; metin log yetmez (SS8). */
        olay_yay_guvenlik(olay_kodu);
    }

private:
    bool acilabilir_;   /* yalnız V5 tam sonuçta true olur (SA4) */
};
```

## §G Güvenlik Olay Kodları — Doğrulama Köprüsü

| Kod | Anlam | Kötü davranış | Köprü | Durum |
|---|---|---|---|---|
| E4 | imza doğrulama hatası | ST16 HALT | boot-dogrulama DV-B3 | SÖZLEŞME |
| H16 `E_SEC_CHAIN` | zincir açamama (güvenli dur) | açamama | security-dogrulama DV-S2 | TASLAK ⚠️ |
| H14 `E_UPD_SIG` | imza reddi (update tarafı) | red + eski bölüm | update dalı | TASLAK ⚠️ |
| BOOT_E003 | boot anti-rollback okuma (§C.2 köprüsü) | red | boot-ornekler §B.5 | TASLAK |
| SG5 | tamper olayı | zincir durmaz, olay yayılır | boot-surucu §20 | SÖZLEŞME |

## §H Sık Sorulanlar

**S: Kodlar derlenir mi?**
Hayır. Repo'da derlenebilir firmware kaynağı yok (2026-10-13 keşif: 0
`.c`/`.cpp`/`.h` dosyası) ve hedef MCU UNKNOWN. Parçalar referans
tasaktır; `Taslak:`/`Kaynak:` etiketi taşırlar.

**S: `Kaynak:` neden kesin dosya/satır referansı değil?**
Repo kanıtı olmadan dosya/satır iddiası uydurmadır. Bu dosyada yalnız
vault bölüm referansı (`security-anayasa.md §3` gibi) ve okunmuş kaynak
adı kullanılır.

**S: İmza algoritması neden boş (§B)?**
T5 `SEÇİLMEDİ · ⚠️ UNKNOWN` (firmware-bilesenler satır 99). Seçim
yapılmadan "firmware şunu kullanır" yazmak hallüsinasyondur.

## §I CI Entegrasyon Notu

CI yalnız host (PHP) tarafını derler/test eder; bu dosyanın taslak C
parçaları CI'ya **k alınmaz** (kod yok, derleme yok). Kapılar
`firmware-test-stratejisi.md §18` (G2a-G3 aktif · G9 SBOM eksik) ve
`security-dogrulama.md` DV-S serisi ile tanımlıdır.

## §J Security Test Matrisi (özet köprü)

| Senaryo | Taslak § | Beklenen | Köprü |
|---|---|---|---|
| Geçerli imza → aç | §A.1 | F4 set · ST6 | DV-S1 |
| Bozuk/imsız → reddet | §A.1 | E4 · ST16 | DV-S2 (boot DV-B3) |
| Çağrı hatası → fail-safe | §A.1/§F | açamama | DV-S3 (boot DV-B4) |
| Eski sürüm → red | §C.2 | rollback_check -1 | DV-S5 (boot §B.5) |
| security flash yazma dene | §C.1 | -1 (her koşul) | DV-S4 |
| Ham anahtar sızdırma | §B/§D | olayda yalnız no | DV-S6 |


---

## §K — Akış Zaman Diyagramları (taslak — okunabilirlik)

### §K.1 Başarılı doğrulama (boot → security → boot)

```text
boot                security               flash/kasa
  |  request(V1-V5)   |                       |
  |------------------>|  read key ref (G2)    |
  |                   |---------------------->|
  |                   |<----- ref (veya ERR)  |
  |                   |  read image+sig       |
  |                   |  verify (V1..V5)      |
  |<---- PASS/FAIL ---|                       |
  |  continue|halt    |                       |
```

Kural: diyagramda **ara geçiş yoktur** — doğrulayıcı, karar vermeden önce hiçbir
duruma sıçramaz (V1–V5 sırası bozulursa üretici tarafı ihlal eder).

### §K.2 Başarısız doğrulama (M1) — yeniden deneme yok

```text
boot                security
  |  request          |
  |------------------>|  V2'de imza uyuşmadı
  |                   |  emit event M1 (G5)
  |<---- FAIL --------|
  |  halt (kal)       |
  |  (tekrar çağrısı YASAK - bkz. dogrulama M.5)
```

### §K.3 Kasa erişim hatası (M2) — fail-secure

```text
boot                security            kasa(G2)
  |  request          |                   |
  |------------------>|  ref iste          |
  |                   |------------------>|  ERR
  |                   |<---- ERR ---------|
  |                   |  emit event M2
  |<---- FAIL (kal) --|
```

Not: M2 durumunda doğrulayıcı **asla** "kasa yoksa geç" üretmez; bu, §L.1 fail-open
örneğinin birebir zıddıdır.

---

## §L — Kötü Uygulama Örnekleri (genişletme — YAPILMAZ)

### §L.1 fail-open kalıbı (en tehlikeli)

```c
/* YAPILMAYACAK — fail-open: hata durumunda geç karar üretir. */
int rc = verify_image(img, sig);
if (rc != 0) {
    /* "dene devam et" davranışı — T1/T5 vektörü açar */
    log("verify problem, continuing");
    return ALLOW;      /* YASAK: belirsizlikte izin */
}
return ALLOW;
```

Düzeltme yönü (tasarım dili): hata → kal · logla (G5) · tekrar deneme yok.

### §L.2 kasa → doğrulayıcı bağımlılığının tersi

```c
/* YAPILMAYACAK — doğrulayıcı kasayı yönetir (tek-yönlülük ihlali). */
key_store_write(kid, key_bytes);  /* doğrulayıcı yazarsa G2 mülkiyeti kırılır */
```

Düzeltme yönü: kasa referans üretir; yazım G2'nin mülkiyetindedir (kapsam §H.3).

### §L.3 sessiz olay kaybı

```c
/* YAPILMAYACAK — M5 ihlali: kuyruk dolunca olayı at. */
if (queue_full()) drop_event(e);   /* repudiation açığı (T7) */
```

Düzeltme yönü: düşürme yok — sayac + en eski kaydı işaretle (kapsam §M M5).

### §L.4 tekrar deneme döngüsü (zamanlama kanalı)

```c
/* YAPILMAYACAK — M1'de otomatik tekrar (brute-force kanalı). */
while (verify_image(img, sig) != 0) { retry(); }
```

Düzeltme yönü: tek deneme · kal · olay · boot-dogrulama üzerinden kurtarma kararı.

### §L.5 test alanını üretimde açık bırakma

```c
/* YAPILMAYACAK — L5 test alanı release'de açık. */
if (debug_area_enabled()) allow_service_mode();  /* T6/T2 vektörü */
```

Düzeltme yönü: release derlemesinde alan devre dışı; başarısızlıkta fail-secure.

---

## §M — Sağlam Örnek Kalıpları (M1–M4 · tasarım dili)

### §M.1 erken reddetme (girdi denetimi)

```c
/* TASLAK — repo'da kod yok (cpp/h = 0 · 2026-10-15). */
if (sig_len == 0 || sig_len > SIG_MAX) return REJECT; /* uzunluk denetimi önce */
if (img_len < IMG_MIN)               return REJECT;
/* ... V1-V5 sırası ... */
```

Not: "uzunluk denetimi önce" bir **varsayımdır** (DV-S2 ile doğrulanacak) — kesin
davranış bu vault'ta sabitlenmemiştir.

### §M.2 karar + olay atomikliği

```c
/* TASLAK — karar verilmeden olay üretimi tamamlanır (senkron kritik olay). */
verdict = verify_chain(V1_TO_V5);
emit_event(verdict == PASS ? EV_OK : EV_M1);   /* G5 — karardan önce geri dönme */
return verdict;
```

### §M.3 salt-okuma sayaç denetimi

```c
/* TASLAK — sayaç yalnız okunur; yazım update mülkiyetindedir. */
uint32_t sc = counter_read(CNT_ANTIROLL);      /* G4 — yazım yok */
return (img_ver >= sc) ? OK : REJECT;          /* M3 hatası döndürülür (okuma hatası) */
```

### §M.4 politika varsayılanı

```c
/* TASLAK — bağlam eksikse reddet (M4). */
decision_t d = policy_evaluate(ctx);
if (!ctx.complete) d = DENY;                   /* varsayılan güven duyma */
return d;
```

---

## §N — Durum Makinesi Örnekleri (taslak — G1 etrafında)

### §N.1 Doğrulayıcı durumları

| Durum | Tetik | Çıkış | Hata çıkışı |
|---|---|---|---|
| IDLE | boot isteği | VERIFYING | — |
| VERIFYING | V1 tamam | VERIFYING (V2) | M1 → HALT |
| VERIFYING | kasa ERR | HALT | M2 |
| PASS | V5 tamam | boot devam | — |
| HALT | (kalıcı) | — | — (yeniden giriş YOK) |

### §N.2 Geçiş tablosu (durum × olay)

| ↓ Durum \ Olay | istek | v_ok | v_err | kasa_err | reset |
|---|---|---|---|---|---|
| IDLE | VERIFYING | — | — | — | IDLE |
| VERIFYING | — | bir sonraki V | HALT (M1) | HALT (M2) | IDLE* |
| PASS | — | — | — | — | IDLE |
| HALT | — | — | — | — | IDLE* |

* reset/boot dalı sahibidir; bu tablo yalnız güvenlik tarafı gözlemidir (boot-dogrulama devri).

---

## §O — Test Betiği İskeletleri (taslak — betik depoda YOKTUR)

```powershell
# Taslak: DV-S1 bozuk imza denemesi iskeleti — depoda bu betik YOKTUR.
# Kanıt: 2026-10-15 envanter — bu depoda betik/CI firmware testi yok (G9 tanımsız).
# Kaynak: security-dogrulama.md §H.1 · §K.1 sonuç şeması.
# Adimlar: fixture hazirla -> boot tetikle -> olay kaydini oku -> sonuc satiri yaz
# verdict degerleri: PASS / FAIL / BLOCKED / INVALID (uydurma deger yok)
```

```powershell
# Taslak: sonuc satiri uretimi (K.1 semasi) — alanlar disinda deger UYDURULMAZ.
# ts, scen, env, verdict, evidence, actor
```

---

## §P — Örnek ↔ Kapı Eşlemesi (özet köprü — §J'yi genişletir)

| Örnek | Kapı | Doğrulama senaryosu |
|---|---|---|
| §K.2 (M1 kal) | K-S1 | DV-S1 · DV-S9 |
| §L.1 (fail-open) | K-S1/K-S3 | DV-S1 (negatif karşıt) |
| §M.1 (erken reddetme) | K-S1 | DV-S2 |
| §L.2 (kasa yönü) | K-S2 | DV-S7 |
| §M.3 (salt okuma) | K-S4 | DV-S4 |
| §L.3 (sessiz kayıp) | K-S6 | DV-S8 |
| §L.5 (test alanı) | K-S3 | DV-S5 · DV-S11 |
| §M.2 (olay atomikliği) | K-S6 | DV-S6 |

---

## §Q — Kullanım Notları ve Kapanış (bu ek)

1. Her taslak blok **"repo'da YOK"** damgasını taşır; çalışan/başarılı iddiası yoktur.
2. Bloklar C dili stilinde verilmiştir (mimari anlatım); derleme/çalıştırma iddiası yok.
3. Mevcut §A–§J + frontmatter + son Authority satırı append-only korunmuştur.
4. T5 primitive seçilirse §M bloklarına gerçek kütüphane/altyapı adı eklenebilir —
   o ana kadar ad uydurmak yasaktır (⚠️ VERIFICATION REQUIRED).


---

## §R — Sık Sorulanlar (bu ek için — §H'yi tamamlar)

| Soru | Yanıt |
|---|---|
| Taslak kod derlenir mi? | Hayır — depoda C/C++ yok (cpp/h = 0 · 2026-10-15); bloklar anlatım aracıdır |
| Hangi imza kütüphanesi? | T5 SEÇİLMEDİ — ad yazmak uydurma olur (⚠️ VERIFICATION REQUIRED) |
| fail-open neden yasak? | Belirsizlikte izin, T1/T5 vektörünü doğrudan açar (§L.1 · kapsam §M.1) |
| Doğrulayıcı kasa yazabilir mi? | Hayır — G2 mülkiyeti kasa arayüzünde (§L.2 · kapsam §H.3) |
| Olay kaybı affedilir mi? | Hayır — M5 sayarak işaretleme ister (§L.3) |
| DV-S2'de uzunluk denetimi kesin mi? | Hayır — varsayım; DV-S2 ile doğrulanacak (§M.1 notu) |
| Reset sonrası durum kimde? | Boot dalı; bu dosya yalnız gözlem üretir (§N.2 notu) |
| Betikler nerede? | Yok — iskeletler taslak damgalıdır (§O · G9 tanımsız) |
| Sonraki üretim ne? | T5 kararı sonrası §M'ye gerçek altyapı adı (append-only) |

### §R.1 Ek için son kontrol

1. §K–§R eklenmiştir; §A–§J silinmemiştir (append-only).
2. Çalıştırılmış iddiası: 0 · uydurma dosya/URL/marka: 0 · dolgu satırı: 0.
3. Son satır Authority birebir korunur.


---

## §S — Örnek Kullanım Kılavuzu (kim ne okur)

1. **Geliştirici** (tasarım yaparken): §M sağlam kalıplar → §L yasak kalıplar sırasıyla
   okunur; §N durum makinesi arayüz yazımına şablon olur.
2. **İnceleyen (reviewer)**: §P kapı eşlemesi ile her örneğin karşılık geldiği kapıyı
   denetler; taslak damgası olmayan blok varsa **uydurma** kabul edilir ve reddedilir.
3. **QA**: §O iskeletleri §R.1 kontrol listesiyle birleştirilir; DV-S kartlarına
   (security-dogrulama §H) referans verilir.
4. **Architect**: §L.2/§L.4 gibi mülkiyet ihlallerini ADR girdisi olarak izler.
5. **Vault Steward**: her üretim turunda §R.1 sayımını yeniler (append-only kaydı).

### §S.1 Örnek dosyasının güncellenme koşulu

- T5 kararı gelirse: §M bloklarına gerçek altyapı adı append edilir (silme yok).
- Q4 kararı gelirse: §K diyagramlarındaki sahiplik notu yeni satırla güncellenir.
- Yeni kapı açılırsa: §P tablosuna satır eklenir (§J köprüsü korunur).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
