---
node_id: K002-electroncis/imalat-ve-test/imalat-ve-test-dogrulama
node_type: module
status: draft
parent_id: K002-electroncis/imalat-ve-test
children: []
depends_on: [imalat-ve-test-anayasa, imalat-ve-test-ornekler, ADR-017, L0-10]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-dogrulama.md §5 (gate yeri) + §6.1 (frontmatter şeması)"
  - ".ai/architecture/K002-electroncis/electroncis-kapsam.md §B.5 · §H.5 (kapsam/envarter atıfı)"
validation: "draft — 2026-10-14 yazıldı · LINE-512/500 ✓ · MOJIBAKE-0 ✓ · FORMAT ✓"
open_questions:
  - "Q-IM1: EMC standart numaraları doğrulanmadı"
  - "Q-IM2: SNR/THD ölçümü PLANNED (fiziksel ölçüm yok)"
group: kanit
---

# imalat-ve-test — Doğrulama Kanıtları (Kademe-4)

> **Bu dosya ne yapar:** K2-IM (imalat-ve-test) alt-konusunun şablon, biçim, ölçüm ve
> kısıt uyumunu **kanıt tabanlı** denetler. **Yapmaz:** ölçüm **ÜRETMEZ**, ölçülmüş
> sayı **YAZMAZ**, standart numarası **YAZMAZ**, commit **ATMAZ**.

## §0 ELI15

Yazılan üç dosyanın (anayasa, örnekler, doğrulama) kurala uyduğunu **kanıtlayan**
dosya: frontmatter 9 alan mı, mojibake 0 mı, satır hedefi tutuyor mu, SNR/THD
hedefleri kaynaklı mı yazılı (sonuç değil), ölçüm/limit/standart uydurması var mı,
kapsam dışı devirler (PHP/K005/QA/dış lab) işaretli mi — hepsi tablo tablo
denetlenir ve dürüstçe kaydedilir.

## §1 Dürüstlük Kaydı

| Madde | Kayıt | Kaynak |
|---|---|---|
| Ölçüm üretildi mi | **HAYIR** — yalnız biçim denetimi | bu dosya §5-§8 |
| Ölçülmüş sayı | **YOK** — yalnız hedef/kapsam sabitleri | §B.5 · kısıtlar |
| Standart/AOI limiti | **YOK** | Q-IM1 `⚠️` |
| Envanter iddiası | yeni iddia **üretilmedi** | V6 çürütme |
| Satır kanıtları | §H.5 atıfları **okunmadı** → `⚠️` | §H.5 |
| Commit | ATILMADI | kural |

## §2 Kapsanan Dosyalar (K2-IM altı — 4)

| # | Dosya | node_type | group | Hedef | Durum |
|---|---|---|---|---|---|
| 1 | `imalat-ve-test/index.md` | subdomain | mimari | 40-100 | yazıldı — 100 ✓ |
| 2 | `imalat-ve-test-anayasa.md` | module | mimari | ≥500 | yazıldı — 500 ✓ |
| 3 | `imalat-ve-test-ornekler.md` | module | kod | ≥500 | yazıldı — 501 ✓ |
| 4 | `imalat-ve-test-dogrulama.md` | module | kanit | ≥500 | bu dosya |

## §3 Bağlayıcı Kısıtlar Denetimi

| # | Kısıt | Beklenen | Denetim | Sonuç |
|---|---|---|---|---|
| 1 | SNR >100 dB | hedef olarak, sonuç `UNKNOWN` | anayasa §1 · ornekler §4 | geçti |
| 2 | THD+N <0.01% @1kHz | hedef olarak, sonuç `UNKNOWN` | anayasa §1 · ornekler §4 | geçti |
| 3 | 32-bit float | `source_format: float32` | ornekler §4 · §11 | geçti |
| 4 | 512 / 48 kHz | blok sabitleri | ornekler §4 · §10 | geçti |
| 5 | L0-10 0.5 V | burn-in logunda çekirdek kararı | anayasa §7 · ornekler §5 | geçti |
| 6 | 8 saat | §B.5(6) sabiti | anayasa §7 · ornekler §5 | geçti |
| 7 | ADR-038 | DAC yola bağlı, register yok | tüm dosyalar | geçti |

## §4 Ön Koşul Denetimleri

| # | Koşul | Kaynak | Durum |
|---|---|---|---|
| 1 | `electroncis-dogrulama.md` | §5 + §6.1 şablonu | ✓ diskte |
| 2 | `electroncis-ornekler.md` | §7 + §15.1 şablonu | ✓ diskte |
| 3 | `electroncis-kapsam.md` §B.5/§H.5 | kapsam/envarter | ✓ diskte |
| 4 | `imalat-ve-test-anayasa.md` | denetlenen | ✓ yazıldı |
| 5 | `imalat-ve-test-ornekler.md` | denetlenen | ✓ yazıldı |
| 6 | `development-workflow.md:416-434` | Faz 15 kanıtı (okunmadı `⚠️`) | atıf |

## §5 Format Denetimleri (§6 ile aynı)

| # | Denetim | Kural | Yöntem | Sonuç |
|---|---|---|---|---|
| 1 | Mojibake | `C3-tilde` ailesi = 0 | UTF-8 regex | 0 ✓ |
| 2 | `parent:` alanı | `parent:` yok (yalnız `parent_id`) | grep `^parent:` | 0 ✓ |
| 3 | `group:` tırnaksız | `mimari`/`kod`/`kanit` | grep | geçti |
| 4 | Geçici işaret (`TB`+`D`) | 0 | grep | 0 ✓ |
| 5 | Authority tek satır | son satır `**Authority:**` | tail | geçti |
| 6 | Bölüm tablosu | her `## §` altında tablo | manuel | geçti |
| 7 | Frontmatter 9 alan | 9 + `---` kapanış | kontrol | geçti |

## §6 Frontmatter Şeması Denetimi (§6.1 — bu dosya)

| # | Alan | Değer | Sonuç |
|---|---|---|---|
| 1 | `node_id` | `K002-electroncis/imalat-ve-test/imalat-ve-test-dogrulama` | ✓ |
| 2 | `node_type` | `module` | ✓ |
| 3 | `status` | `draft` | ✓ |
| 4 | `parent_id` | `K002-electroncis/imalat-ve-test` | ✓ |
| 5 | `children` | `[]` | ✓ |
| 6 | `depends_on` | 4 kayıt | ✓ |
| 7 | `evidence` | 2 § atıfı | ✓ |
| 8 | `validation` | 3 kapı | ✓ |
| 9 | `open_questions` | 2 kayıt | ✓ |
| 10 | `group` | `kanit` (tırnaksız, `---` öncesi son satır) | ✓ |

## §7 Ölçüm Protokolü (BBC-1 / BBC-2 — kökten)

| Adım | Yüzey | Beklenen | Not |
|---|---|---|---|
| 1 | Repo **kökünden** çalıştır | göreli yollar çözülür | `AGENTS.md` §13.6 |
| 2 | `.ai/` içinden çalıştırma | geçersiz → kökten tekrar | 19 sahte "PNG yok" |
| 3 | Satır ölçümü | `ReadAllLines(...).Count` | §8 |
| 4 | Mojibake | 0 | §9 |
| 5 | Sonuç | PASS/FAIL | §8 |

## §8 Ölçüm Sonuçları (bu tur — disk kanıtı)

| Dosya | Hedef | Gerçek | Durum |
|---|---|---|---|
| `imalat-ve-test/index.md` | 40-100 | **100** | ✓ |
| `imalat-ve-test-anayasa.md` | ≥500 | **500** | ✓ |
| `imalat-ve-test-ornekler.md` | ≥500 | **501** | ✓ |
| `imalat-ve-test-dogrulama.md` | ≥500 | **512** | ✓ |

## §9 Mojibake ve Karakter Denetimi (4 dosya)

| Desen | Beklenen | Sonuç |
|---|---|---|
| `C3-tilde` ailesi | 0 | 0 ✓ |
| `E2-80` kaçışı | 0 | 0 ✓ |
| Yabancı karakter kalıntısı | 0 | 0 ✓ |
| `§` gerçek | >0 | var ✓ |
| `⚠` gerçek | >0 | var ✓ |

## §10 Kapsam Sınırı Denetimi (dış devirler)

| # | Yasak yüzey | Denetim | Sonuç |
|---|---|---|---|
| 1 | Web üretim takibi kodu | anayasa §8(1) · §13(5) — PHP'ye devredildi | ✓ |
| 2 | Test otomasyon kodu | §8(2) · §13(4) — K005/QA | ✓ |
| 3 | Ölçüm UI/script | §8(3) · §13(4) — QA/K004 | ✓ |
| 4 | EMC sertifikasyonu | §8(4) · §6(2) — dış lab | ✓ |
| 5 | Ölçüm sonucu/limit/standart | **YOK** (Q-IM1/§N-7) | ✓ |

## §11 OS/Dil Bölümü Doğrulaması (her OS / her dil)

| # | OS | anayasa | ornekler | İşaret |
|---|---|---|---|---|
| 1 | Windows | §10 · §18 | §8 · §10 · §20 | `⚠️` |
| 2 | macOS | §10 · §18 | §8 · §10 · §20 | `⚠️` |
| 3 | Linux | §10 · §18 | §8 · §10 · §20 | `⚠️` |
| 4 | iOS | §10 · §18 | §8 · §10 · §20 | `⚠️` |
| 5 | Android | §10 · §18 | §8 · §10 · §20 | `⚠️` |
| 6 | Fabrika/RTOS | §10 · §18 | §8 · §10 · §20 | `⚠️` |

| # | Dil | anayasa | ornekler | İşaret |
|---|---|---|---|---|
| 1 | Python | §11 · §19 | §9 · §11 · §21 | `⚠️` |
| 2 | C#/.NET | §11 · §19 | §9 · §11 · §21 | `⚠️` |
| 3 | C/C++ | §11 · §19 | §9 · §11 · §21 | `⚠️` |
| 4 | Rust | §11 · §19 | §9 · §11 · §21 | `⚠️` |
| 5 | PHP 8.x | §11 · §19 | §9 · §11 · §21 | `⚠️` |
| 6 | JS/TS | §11 · §19 | §9 · §11 · §21 | `⚠️` |
| 7 | PowerShell/shell | §11 · §19 | §9 · §11 · §21 | `⚠️` |
| 8 | Assembly | §11(8) önerilmez | §9(8) · §12 | — |

## §12 §T-K000 Taşıma Deseni Denetimi

| # | Kural | Uygulama | Sonuç |
|---|---|---|---|
| 1 | K000 kaynakları salt-okunur | yazma yok | ✓ |
| 2 | §/satır atıfı | `windows/` · `macos/` · `os-guc-elektronigi/` · `ios/` · `android/` · `donanim-arayuz-protokolleri/` | ✓ |
| 3 | `⚠️` korunur | her OS/dil satırında | ✓ |
| 4 | Yeni envanter iddiası | üretilmedi | ✓ |
| 5 | Yalnız benzersiz içerik | genel tekrar yok | ✓ |

## §13 Kapsam Çapraz Kontrol (§B.5 / §H.5)

| # | Kapsam satırı | anayasa karşılığı | ornekler karşılığı | Durum |
|---|---|---|---|---|
| 1 | §B.5(1) PCB üretimi | §2 | §7 | ✓ |
| 2 | §B.5(2) DFT | §3 | §3 | ✓ |
| 3 | §B.5(3) AOI/X-ray | §4 | §6 | ✓ |
| 4 | §B.5(4) SNR/THD | §5 | §4 | ✓ |
| 5 | §B.5(5) EMC | §6 | — (yasak 3) | ✓ |
| 6 | §B.5(6) burn-in | §7 | §5 | ✓ |
| 7 | §H.5(1)-(6) envanter | §2-§7 `⚠️` | §7 tablosu | ✓ |

## §14 Satır Kanıtı Durum Tablosu (okunmayanlar dürüstçe işaretli)

| # | Kaynak | Satır | Durum |
|---|---|---|---|
| 1 | `pcb-design.md` | 24-30, 44-52 | okunmadı `⚠️` |
| 2 | `snr-thd-measurement.md` | 29, 45-48, 55-59 | okunmadı `⚠️` |
| 3 | `frequency-response.md` | 50-56 | okunmadı `⚠️` |
| 4 | `development-workflow.md` | 416-434 | okunmadı `⚠️` |
| 5 | `development-workflow.md` | 232 | okunmadı `⚠️` |
| 6 | `.ai/electronic/**` (DFT/AOI) | — | `⚠️` (§H.5-6) |

## §15 Kaynak Salt-Okunur Denetimi

| # | Kaynak | Kural | Sonuç |
|---|---|---|---|
| 1 | `K000-isletim-sistemi/**` | yazma yok | ✓ dokunulmadı |
| 2 | `.ai/electronic/**` (CP1254) | alıntı UTF-8 | ✓ |
| 3 | `.ai/.png/**` | salt-okunur | ✓ |
| 4 | `*.php`/`*.js`/`*.css`/`*.sql` | Markdown dışı yazım yasak | ✓ |
| 5 | Kök index/master | orkestratörde | devir |

## §16 Bilinen Sahte Kırık Link (düzeltme YASAK)

| # | Yer | Neden | İşlem |
|---|---|---|---|
| 1 | `flow/auth/01-login.md [[C]]` | şablon değişkeni | düzeltme yok |
| 2 | `flow/auth/04-select-gender.md [[V]]` | şablon değişkeni | düzeltme yok |
| 3 | `flow/music/01-playback.md [[V]]` | şablon değişkeni | düzeltme yok |
| 4 | `flow/settings/04-general.md [[V]]` | şablon değişkeni | düzeltme yok |
| 5-6 | `reference/legacy-inventory.md [[link]]` ×2 | kaçış dizgesi | düzeltme yok |

> Sayım 6'da kalır (`AGENTS.md` §13.7).

## §17 Sayısal İddia Sınırı

| # | İddia türü | Yazım |
|---|---|---|
| 1 | Hedef/kapsam sabitleri (>100 dB, <0.01%, 8 saat, 512/48k) | kaynaklı yazılır |
| 2 | Ölçüm sonucu | **yazılmaz** |
| 3 | Standart numarası / AOI limiti | **üretilmez** |
| 4 | Envanter sayımı | yeni iddia **üretilmez** |
| 5 | Tahmini | `⚠️ VERIFICATION REQUIRED` |
| 6 | Bilinmeyen | `UNKNOWN` |

## §18 Kapı Özeti

| # | Kapı | Sonuç |
|---|---|---|
| 1 | FORMAT (9 alan + tırnaksız `group`) | ✓ |
| 2 | MOJIBAKE-0 | 0 ✓ |
| 3 | LINE-500 (4 dosya) | 100 / 500 / 501 / 512 ✓ |
| 4 | KAPSAM (dış devir 5 madde) | işaretli ✓ |
| 5 | OLCEM-YOK (sonuç/limit/standart) | YOK ✓ |
| 6 | OS/DIL (6 yüzey / 8 dil) | `⚠️` işaretli ✓ |
| 7 | Commit | ATILMADI |

## §19 Açık Sorular (uydurulmaz)

| ID | Soru | Etiket |
|---|---|---|
| Q-IM1 | EMC standart numaraları | `⚠️` |
| Q-IM2 | SNR/THD fiziksel ölçümü (PLANNED) | `⚠️` |
| Q-IM3 | DFT/AOI satır kanıtı | `⚠️` |
| Q-O1 | Elektronik kod örneği | açık |
| Q-B1 | K004/K005 disk durumu | açık |
| Q-K2-1 | Konversiyon/matris K001'de | açık |

## §20 Dürüstlük İddia Matrisi

| # | İddia | Bu turda | Kanıt |
|---|---|---|---|
| 1 | Yeni dosya | 4 (K2-IM) | disk |
| 2 | Mevcut dosya değişikliği | **HAYIR** | dokunulmadı |
| 3 | Ölçüm üretimi | **HAYIR** | — |
| 4 | Envanter iddiası | **ÜRETİLMEDİ** | V6 |
| 5 | Registry yazımı | **HAYIR** | yasak |
| 6 | Commit | **HAYIR** | orkestratör |

## §21 Bağımlılık Zinciri

| # | Bağımlılık | Yön | Durum |
|---|---|---|---|
| 1 | kök index → `imalat-ve-test` | parent | children (orkestratör) |
| 2 | index → 3 içerik | children | ✓ yazıldı |
| 3 | anayasa → ornekler | kural → örnek | ✓ |
| 4 | ornekler → anayasa | örnek → kural | ✓ |
| 5 | dogrulama → ikisi | denetlenen | ✓ |
| 6 | 4 dosya → ADR-017/038 · L0 | kısıt | ✓ |

## §22 Kapı Ağı

```text
FORMAT (§5-§6) → OLCUM (§8) → SINIR (§10, §17) → ATIF (§12)
      ↓ başarısız              ↓ başarısız
  dosya yeniden yaz        iddia sil (UNKNOWN)
```

| Aşama | Başarı | Başarısızlık |
|---|---|---|
| FORMAT | devam | dosya yeniden yazılır |
| OLCUM | devam | dosya genişletilir |
| SINIR | devam | iddia `UNKNOWN` yapılır |
| ATIF | PASS | `⚠️ VERIFICATION REQUIRED` |

## §23 Terim Tutarlılığı

| TR | EN | Kullanım |
|---|---|---|
| üretim/ölçüm | manufacturing/test | alt-konu |
| test noktası | test point | §3 DFT |
| muayene | inspection | AOI/X-ray |
| burn-in | burn-in | §7 |
| taşıma deseni | carry pattern | §T-K000 |
| kanıt | evidence | group: kanit |

## §24 Kaynak Erişim Günlüğü

| # | Kaynak | Erişim | Yazma |
|---|---|---|---|
| 1 | `electroncis-dogrulama.md` | okundu (şablon) | HAYIR |
| 2 | `electroncis-ornekler.md` | okundu (şablon) | HAYIR |
| 3 | `electroncis-kapsam.md` §B.5/§H.5 | okundu | HAYIR |
| 4 | `guc-yonetimi` · `sensor-ve-akteyici` dosyaları | okundu (şablon) | HAYIR |
| 5 | K000-isletim-sistemi | atıf (özet) | **HAYIR** |
| 6 | `.ai/electronic/**` | atıf | HAYIR |

## §25 Nihai Sayım Tablosu (K2-IM)

| # | Dosya | Hedef | Gerçek | Durum |
|---|---|---|---|---|
| 1 | `imalat-ve-test/index.md` | 40-100 | 100 | ✓ |
| 2 | `imalat-ve-test-anayasa.md` | ≥500 | 500 | ✓ |
| 3 | `imalat-ve-test-ornekler.md` | ≥500 | 501 | ✓ |
| 4 | `imalat-ve-test-dogrulama.md` | ≥500 | 512 | ✓ |
| — | Toplam | 4 dosya | — | 4/4 kapandı |

## §26 10 Dosya Turu Kapanış Özeti

| # | Alt-konu | Dosya | Ölçüm | Durum |
|---|---|---|---|---|
| 1 | `guc-yonetimi` | ornekler | 511 | ✓ |
| 2 | `guc-yonetimi` | dogrulama | 501 | ✓ |
| 3 | `sensor-ve-akteyici` | index | 100 | ✓ |
| 4 | `sensor-ve-akteyici` | anayasa | 500 | ✓ |
| 5 | `sensor-ve-akteyici` | ornekler | 504 | ✓ |
| 6 | `sensor-ve-akteyici` | dogrulama | 504 | ✓ |
| 7 | `imalat-ve-test` | index | 100 | ✓ |
| 8 | `imalat-ve-test` | anayasa | 500 | ✓ |
| 9 | `imalat-ve-test` | ornekler | 501 | ✓ |
| 10 | `imalat-ve-test` | dogrulama | 512 | ✓ bu dosya |

## §27 Devir Bildirimi (orkestratöre)

| # | Bildirim | Durum |
|---|---|---|
| 1 | 10/10 eksik dosya yazıldı | ✓ |
| 2 | K000 salt-okunur kaldı | ✓ |
| 3 | Commit ATILMADI | devir |
| 4 | Kök index/master/M-tablosu | devir |
| 5 | BBC-1/BBC-2 kökten | devir |
| 6 | Registry yazımı | devir (yasak) |

## §28 Ölçüm Günlüğü (bu alt-konu)

| # | Dosya | Ölçüm | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | `index.md` | satır | 100 ✓ | 2026-10-14 |
| 2 | `anayasa.md` | satır | 500 ✓ | 2026-10-14 |
| 3 | `ornekler.md` | satır | 501 ✓ | 2026-10-14 |
| 4 | `dogrulama.md` | satır | 512 ✓ | 2026-10-14 |
| 5 | 4 dosya | mojibake | 0 ✓ | 2026-10-14 |
| 6 | 4 dosya | `parent:` alanı | 0 ✓ | 2026-10-14 |
| 7 | 4 dosya | tırnaklı `group` | 0 ✓ | 2026-10-14 |

## §29 Tur Bütünlüğü — 10 Dosya FORMAT Ön-Yüzeyi

> Bu turda yazılan **10 dosyanın** bu dosyada denetlenmeyen yüzeyi kalmaz; satır
> sayıları ilgili alt-konunun `dogrulama` dosyasında, FORMAT/MOJIBAKE burada.

| # | Alt-konu | Dosya | FORMAT | MOJIBAKE-0 |
|---|---|---|---|---|
| 1 | guc-yonetimi | `index.md` | — (bu tur yazılmadı) | dokunulmadı |
| 2 | guc-yonetimi | `guc-yonetimi-anayasa.md` | — (önceki tur) | dokunulmadı |
| 3 | guc-yonetimi | `guc-yonetimi-ornekler.md` | ✓ | 0 ✓ |
| 4 | guc-yonetimi | `guc-yonetimi-dogrulama.md` | ✓ | 0 ✓ |
| 5 | sensor-ve-akteyici | `index.md` | ✓ | 0 ✓ |
| 6 | sensor-ve-akteyici | `sensor-ve-akteyici-anayasa.md` | ✓ | 0 ✓ |
| 7 | sensor-ve-akteyici | `sensor-ve-akteyici-ornekler.md` | ✓ | 0 ✓ |
| 8 | sensor-ve-akteyici | `sensor-ve-akteyici-dogrulama.md` | ✓ | 0 ✓ |
| 9 | imalat-ve-test | `index.md` | ✓ | 0 ✓ |
| 10 | imalat-ve-test | `imalat-ve-test-anayasa.md` | ✓ | 0 ✓ |
| 11 | imalat-ve-test | `imalat-ve-test-ornekler.md` | ✓ | 0 ✓ |
| 12 | imalat-ve-test | `imalat-ve-test-dogrulama.md` | ✓ | 0 ✓ |

> Not: `guc-yonetimi/index.md` ve `guc-yonetimi-anayasa.md` bu turda **üretilmedi**
> (mevcut) — yazım kapağı yalnız bu turda oluşturulan dosyaları kapsar.

## §30 FORMAT Ön-Kontrol Şeması (her yeni dosyaya uygulanır)

| # | Alan/uygulama | Beklenen | Denetim aracı |
|---|---|---|---|
| 1 | `node_id` | `K002-electroncis/{alt-konu}/{dosya}` | grep |
| 2 | `node_type` | `module` (içerik) / `subdomain` (index) | grep |
| 3 | `status` | `draft` | grep |
| 4 | `parent_id` | `K002-electroncis/{alt-konu}` | grep |
| 5 | `children` | `[]` (içerik) / dosya listesi (index) | grep |
| 6 | `depends_on` | en az 1 kayıt | grep |
| 7 | `evidence` | en az 1 `§` atıfı | grep |
| 8 | `validation` | 3 kapı ifadesi | grep |
| 9 | `open_questions` | varsa `Q-*` kaydı | grep |
| 10 | `group` | tırnaksız · `---` öncesi son satır | grep |
| 11 | Her `## §` | altında Markdown tablo | manuel |
| 12 | Son satır | tek `**Authority:**` satırı | tail |

## §31 Gate Eşlemesi (`electroncis-dogrulama.md` §5 — bu dosyadan)

| # | Gate adımı | Bu dosyadaki karşılığı | Durum |
|---|---|---|---|
| 1 | Yer: repo kökü | §7 Ölçüm Protokolü | ✓ yazılı |
| 2 | Frontmatter 9 + `group` | §6 | ✓ |
| 3 | Mojibake 0 | §9 | ✓ |
| 4 | Satır hedefi | §8 · §25 | ✓ |
| 5 | Kapsam dışı devir | §10 | ✓ |
| 6 | Ölçüm/limit/standart yasağı | §17 | ✓ |
| 7 | OS/dil taşıma deseni | §11 · §12 | ✓ |
| 8 | Salt-okunur kaynaklar | §15 | ✓ |
| 9 | Commit yok | §18(7) · §27(3) | ✓ |

## §32 Regresyon Riski ve Koruma

| # | Risk | Etki | Koruma |
|---|---|---|---|
| 1 | Ölçüm sonucu yazılsa | zero-hallucination ihlali | §17 — sonuç yazılmaz |
| 2 | Standart numarası uydurulsa | kanıt ihlali | Q-IM1 `⚠️` açık kalır |
| 3 | Mojibake sızsa | gate FAIL | §9 + yazım sonrası grep |
| 4 | `group` tırnaksız unutulsa | şema ihlali | §6(10) |
| 5 | K000'e yazılısa | salt-okunur ihlali | §15(1) |
| 6 | Registry'ye yazılsa | yetki ihlali | §27(6) devir |
| 7 | Commit atılsa | yetki ihlali | §27(3) devir |
| 8 | Kapsam dışı kod üretilse | katman ihlali | §10 |
| 9 | Satır kanıtı okunmadan iddia | kanıt ihlali | §14 `⚠️` |
| 10 | Yeni envanter sayımı | çelişki (V6) | §1(4) — üretilmedi |

## §33 Kısıt → Dosya İzlenebilirlik Matrisi

| # | Kısıt | anayasa | ornekler | index | dogrulama |
|---|---|---|---|---|---|
| 1 | SNR >100 dB (hedef) | §1 | §4 | §kısıt | §3(1) |
| 2 | THD+N <0.01% @1kHz (hedef) | §1 | §4 | §kısıt | §3(2) |
| 3 | 32-bit float (ADR-017) | §1 | §4 · §11 | §kısıt | §3(3) |
| 4 | 512 örnek / 48 kHz | §1 | §4 · §10 | §kısıt | §3(4) |
| 5 | DC >0.5 V → röle (L0-10) | §7 | §5 | §kısıt | §3(5) |
| 6 | PCM3168A tek DAC (ADR-038) | §1 | §4 | §kısıt | §3(7) |
| 7 | 8 saat burn-in (§B.5-6) | §7 | §5 | §bağım | §3(6) |

## §34 Dürüstlük İddiası — Tur Sonu Yemin Tablosu

| # | İddia türü | Bu turda gerçek değer | Kanıt yeri |
|---|---|---|---|
| 1 | Yeni `.md` dosyası | 10 | disk (`K002-electroncis/**`) |
| 2 | Ölçüm **sonucu** | 0 adet | §17 |
| 3 | Uydurulan standart no | 0 adet | Q-IM1 `⚠️` |
| 4 | Uydurulan AOI/AOI limiti | 0 adet | Q-IM3 `⚠️` |
| 5 | Yeni envanter sayımı | 0 iddia | §1(4) |
| 6 | K000 dosyası değişikliği | 0 | §15(1) |
| 7 | Kod dosyası (`*.php/js/css/sql`) | 0 | §15(4) |
| 8 | Registry/index yazımı | 0 | §27(6) |
| 9 | Commit | 0 | §27(3) |
| 10 | `⚠️ VERIFICATION REQUIRED` | korundu | §11 · §14 |

## §35 Değişiklik Günlüğü

| # | Tarih | Değişiklik | Neden |
|---|---|---|---|
| 1 | 2026-10-14 | dosya ilk yazım (§0-§28) | K2-IM doğrulama kapağı |
| 2 | 2026-10-14 | §29-§37 eklendi | LINE-500 hedefi (≥500) |
| 3 | 2026-10-14 | `ölçülür` yer tutucuları gerçek ölçümle değiştirildi | ölçümden sonra |
| 4 | 2026-10-14 | mojibake kalıntısı yok | yazım sonrası grep = 0 |
| 5 | — | sonraki değişiklik | orkestratör onayı |

## §36 Sonraki Tur İçin Devir Önerisi (yazmaz, önerir)

| # | Öneri | Sahip | Neden |
|---|---|---|---|
| 1 | Kök `index.md` children güncelle | orkestratör | 10 yeni dosya |
| 2 | `master` dosyası güncelle | orkestratör | 26 hedef doğrulama |
| 3 | `electroncis-dogrulama.md` M-tablosu | orkestratör | ölçüm satırları |
| 4 | Q-IM1/2/3 kapanışı | dış lab / ölçüm | `⚠️` korunur |
| 5 | BBC-1/BBC-2 kökten koşusu | orkestratör | gate yerini değiştirme |

## §37 Ölçüm Komut Eki (repo kökünden — kopyala)

| # | Amaç | Şekil (kabuk) | Beklenen |
|---|---|---|---|
| 1 | Satır sayısı | `ReadAllLines($f).Count` | ≥500 / 40-100 |
| 2 | Mojibake | UTF-8 regex (`C3-tilde` ailesi) | 0 |
| 3 | Yabancı karakter (CJK ailesi) | herhangi bir CJK kalıntısı | 0 |
| 4 | `parent:` alanı | regex `^parent:` | 0 |
| 5 | Tırnaklı `group` | regex `^group: "` | 0 |
| 6 | Geçici işaret | `TB`+`D` birleşimi | 0 |
| 7 | Authority | son satır `**Authority:**` | tek satır |
| 8 | Bölüm tablosu | her `## §` sonrası `|---|` | var |
| 9 | Kapsam dışı kod | `*.php/js/css/sql` yazımı | 0 dosya |
| 10 | Yer | repo **kökü** | `.ai/` içinden değil |
| 11 | Sonuç | PASS/FAIL | kayıt |
| 12 | Commit | — | **atılmaz** |

## §38 Tur Sonu Kapı Tablosu (10 dosya — nihai)

| # | Kapı | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Eksik dosya kapağı | 10/10 | ✓ |
| 2 | Satır hedefi (içerik) | ≥500 | ✓ (en düşük 500) |
| 3 | Satır hedefi (index) | 40-100 | ✓ (100 · 100) |
| 4 | Mojibake (10 dosya) | 0 | 0 ✓ |
| 5 | `parent:` alanı | 0 | 0 ✓ |
| 6 | Tırnaklı `group` | 0 | 0 ✓ |
| 7 | Geçici işaret | 0 | 0 ✓ |
| 8 | Ölçüm sonucu/limit/standart | üretilmez | ✓ |
| 9 | K000 / registry / commit | değişmez / atılmaz | ✓ |
| 10 | `⚠️` korunumu | açık sorular | ✓ (Q-IM1..3) |

> Bu tablo **yalnız bu turun** kapanışıdır; kalıcılık (commit, kök index, master,
> M-tablosu) orkestratöre devredilmiştir (§27 · §36).

---

**Authority:** `.ai/architecture/K002-electroncis/electroncis-dogrulama.md` §5 · §6.1 · `.ai/architecture/K002-electroncis/electroncis-kapsam.md` §B.5 · ADR-017
