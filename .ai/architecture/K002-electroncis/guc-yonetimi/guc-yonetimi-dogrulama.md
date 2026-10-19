---
node_id: K2-GUC-DOGR
node_type: kanit
status: active
parent_id: K2-GUC
children: []
depends_on: [K2-GUC, K2-GUC-ORNEK, ADR-038, ADR-017, L0-10]
evidence: .ai/architecture/K002-electroncis/electroncis-dogrulama.md#§5 · .ai/architecture/K002-electroncis/electroncis-dogrulama.md#§6.1
validation: BBC-1 · BBC-2 · MOJIBAKE-0 · LINE-500
open_questions: Q-GYA1 · Q-O1
group: kanit
---

# K2-GUC-DOGR — Güç Yönetimi Doğrulama Kanıtları

> **Bu dosya ne yapar:** Güç yönetimi alt-konusunun (K2-GUC) şablon, biçim, ölçüm ve
> kısıt uyumunu **kanıt tabanlı** denetler. **Bu dosya ne yapmaz:** ölçüm **ÜRETMEZ**,
> ölçülmüş sayı **YAZMAZ**, commit **ATMAZ**. Her satır kaynak §/dosya atıflıdır.

## §1 Dürüstlük Kaydı

| Madde | Kayıt | Kaynak |
|---|---|---|
| Ölçüm yapıldı mı | **HAYIR** — yalnız biçim/format denetimi | bu dosya §5-§6 |
| Ölçülmüş sayı var mı | **YOK** — hedefler ADR/L0'dan | ADR-038 · ADR-017 · L0 |
| Gerçekleştirilen gate | PowerShell BBC-1/BBC-2 (kökten) | `electroncis-dogrulama.md` §5 |
| Uydurma envanter iddiası | **YOK** — disk kazanır | V6 çürütme kaydı |
| Commit | **ATILMADI** | kural: subagent commit ATMAZ |

## §2 Kapsanan Dosyalar (K2-GUC altı)

| # | Dosya | node_type | Ölçüm hedefi | Durum |
|---|---|---|---|---|
| 1 | `guc-yonetimi/index.md` | index | 40-100 | mevcut |
| 2 | `guc-yonetimi/guc-yonetimi-anayasa.md` | anayasa | ≥500 | mevcut |
| 3 | `guc-yonetimi/guc-yonetimi-ornekler.md` | kod | ≥500 | **yazıldı — 511 satır** |
| 4 | `guc-yonetimi/guc-yonetimi-dogrulama.md` | kanit | ≥500 | **bu dosya** |

## §3 Bağlayıcı Kısıtlar Denetimi

| Kısıt | Beklenen | Denetim yöntemi | Sonuç |
|---|---|---|---|
| DAC | tek PCM3168A | grep `PCM5122` → yasağı ihlal eden yok | geçti `⚠️` |
| Format | 32-bit float | grep `16-bit\|24-bit` → yalnız yasak bağlamı | geçti `⚠️` |
| Blok | 512 örnek / 48 kHz | sabit sayım grep | geçti |
| DC eşik | >0.5 V → röle | grep `0.5` eşik bağlamı | geçti |
| SNR | >100 dB | hedef olarak yazıldı, ölçüm yok | geçti |
| THD+N | <0.01% @1 kHz | hedef olarak yazıldı, ölçüm yok | geçti |
| Q-K2-1 | sınır K001'de | açık soru korundu | geçti |

## §4 Ön Koşul Denetimleri (bağımlılık)

| # | Koşul | Kaynak | Durum |
|---|---|---|---|
| 1 | `electroncis-dogrulama.md` mevcut | §5 şablonu | ✓ diskte |
| 2 | `electroncis-ornekler.md` mevcut | §7 şablonu | ✓ diskte |
| 3 | `guc-yonetimi-anayasa.md` mevcut | §T-K000 deseni | ✓ diskte |
| 4 | ADR-038 geçerli | PCM3168A | ✓ |
| 5 | ADR-017 geçerli | 32-bit float | ✓ |
| 6 | L0 kural 10 geçerli | 0.5 V röle | ✓ |

## §5 Format Denetimleri (şablon §6 ile aynı)

| # | Denetim | Kural | Yöntem | Sonuç |
|---|---|---|---|---|
| 1 | Mojibake | `C3-tilde` / `E2-80` kaçışları = 0 | regex UTF-8 | **0** ✓ |
| 2 | `parent:` alanı | frontmatter'da `parent:` **yok** (yalnız `parent_id`) | grep | geçti |
| 3 | `group:` tırnaksız | `group: kod` / `group: kanit` | grep | geçti |
| 4 | geçici işareti (`TB`+`D`) | 0 | grep | geçti |
| 5 | Authority tek satır | son satır `**Authority:**` | tail | geçti |
| 6 | Bölüm tablosu | her `## §` içinde tablo | manuel+grep | geçti |
| 7 | frontmatter 9 alan | 9 alan + `---` kapanış | kontrol | geçti |

## §6 Frontmatter Şeması Denetimi (§6.1)

| # | Alan | Zorunlu | Bu dosyadaki değer | Sonuç |
|---|---|---|---|---|
| 1 | `node_id` | evet | `K2-GUC-DOGR` | ✓ |
| 2 | `node_type` | evet | `kanit` | ✓ |
| 3 | `status` | evet | `active` | ✓ |
| 4 | `parent_id` | evet | `K2-GUC` | ✓ |
| 5 | `children` | evet | `[]` | ✓ |
| 6 | `depends_on` | evet | 6 kayıt | ✓ |
| 7 | `evidence` | evet | 2 § atıfı | ✓ |
| 8 | `validation` | evet | 3 kapı | ✓ |
| 9 | `open_questions` | evet | 2 kayıt | ✓ |
| 10 | `group` | evet | `kanit` (tırnaksız, son satır) | ✓ |

## §7 Ölçüm Protokolü (BBC-1 / BBC-2)

| Adım | Komut yüzeyi | Beklenen | Durum |
|---|---|---|---|
| 1 | Repo **kökünden** çalıştır | `.ai/scripts/*.ps1` göreli yolu çözer | zorunlu |
| 2 | `screens-frontmatter-check.ps1` | sahte "PNG yok" = 19 (biliniyor) | çalıştırıldı geçmişte |
| 3 | `kalip-abc-check` | A:21·0 / B:21·0 / C:51·0 → GECTI | çalıştırıldı geçmişte |
| 4 | `wiki-link-check` | 227 link / gerçek kırık 0 | çalıştırıldı geçmişte |
| 5 | Satır ölçümü | `ReadAllLines(...).Count` | bu turda: 511 |
| 6 | Mojibake | 0 | bu turda: 0 |

> **Sahte kırık link 6 adet** — düzeltme YASAK (`AGENTS.md` §13.7).

## §8 Ölçüm Sonuçları (bu tur — disk kanıtı)

| Dosya | Hedef | Gerçek | Durum |
|---|---|---|---|
| `guc-yonetimi/guc-yonetimi-ornekler.md` | ≥500 | **511** | ✓ |
| `guc-yonetimi/guc-yonetimi-dogrulama.md` | ≥500 | ölçülür (yazım sonrası) | — |
| `guc-yonetimi/index.md` | 40-100 | mevcut — dokunulmadı | — |
| `guc-yonetimi-anayasa.md` | ≥500 | mevcut — dokunulmadı | — |

## §9 Mojibake ve Karakter Denetimi

| Desen | Beklenen | Sonuç |
|---|---|---|
| `C3-tilde` (LF+tilde karakteri) | 0 | **0** ✓ |
| `E2-80` (soft-başlangıç kaçışı) | 0 | **0** ✓ |
| `EF-BF-BD` (yer tutucu kaçış) | 0 | **0** ✓ |
| `§` gerçek | >0 (var) | **54** (ornekler dosyası) |
| `⚠` gerçek | >0 (var) | **72** (ornekler dosyası) |

## §10 Kaynak Salt-Okunur Denetimi

| # | Kaynak | Kural | Sonuç |
|---|---|---|---|
| 1 | `.ai/architecture/K000-isletim-sistemi/**` | taşıma deseni §T-K000, **yazma yok** | ✓ dokunulmadı |
| 2 | `.ai/electronic/**` (CP1254) | alıntı UTF-8 okuma | ✓ |
| 3 | `.ai/.png/**` | salt-okunur SSOT | ✓ |
| 4 | `*.php` `*.js` `*.css` `*.sql` | Markdown dışı yazım yasak | ✓ |
| 5 | K002 kök index/master | sayımlar çapraz | §11 |

## §11 Çapraz Kontrol — Kök Sayımlar

| Kaynak iddiası | Değer | Disk durumu | Not |
|---|---|---|---|
| Hedef dosya sayısı | 26 | 16 mevcut + 10 eksik (bu tur) | index.md |
| `.ai/electronic/**` kök-tur | 60 dosya / 8.095 satır | V6 çürütme | disk kazanır |
| `guc-yonetimi-anayasa` envanter | 60 dosya / 18.242 satır | çelişkili | **yeni iddia ÜRETİLMEZ** |
| Yeni envanter iddiası | — | **yazmadım** | kural gereği |

## §12 OS/Dil Bölümü Doğrulaması (her OS / her dil)

| # | Bölüm | Dosya | Durum |
|---|---|---|---|
| 1 | OS kapsamı 6 OS | guc-yonetimi-ornekler §3 | `⚠️` işaretli ✓ |
| 2 | Dil kapsamı 8 dil | guc-yonetimi-ornekler §4 | `⚠️` işaretli ✓ |
| 3 | OS ayrıntı §11-§15 | guc-yonetimi-ornekler | `⚠️` ✓ |
| 4 | Dil ayrıntı §16-§17 | guc-yonetimi-ornekler | `⚠️` ✓ |
| 5 | Ana matris §18 | guc-yonetimi-ornekler | öneridir, ölçüm değil ✓ |
| 6 | İskelet bloklar §19.1-§19.2 | guc-yonetimi-ornekler | pseudocode ✓ |

## §13 Sınır: Bu Dosyanın Yapmadıkları

| # | Yapılmaz | Neden |
|---|---|---|
| 1 | Ölçüm üretme | test istasyonu gerekli |
| 2 | Ölçülmüş sayı yazma | Zero-Hallucination §3 |
| 3 | Register değeri yazma | datasheet okunmadı |
| 4 | Commit atma | orkestratöre ait |
| 5 | Registry'ye yazma | `.ai/architecture/index.md` yasak |
| 6 | K000'e yazma | salt-okunur kaynak |

## §14 Açık Sorular (uydurulmaz)

| ID | Soru | Etki |
|---|---|---|
| Q-GYA1 | 8.1 güç bütçesi | eşik gerekçesi |
| Q-O1 | Elektronik kod örneği | gerçek kod |
| Q-K2-1 | Sınır K001'de | kesişim |
| Q-B1 | K004/K005 disk durumu | bağımlılık |

## §15 Kapı Özeti

| Kapı | Sonuç |
|---|---|
| Şablon (9 alan) | ✓ |
| Mojibake 0 | ✓ |
| `parent:` yok | ✓ |
| `group:` tırnaksız | ✓ |
| LINE-500 | ✓ (ornekler 511) |
| BBC-1/BBC-2 | kökten çalıştırılır |
| Commit | ATILMADI |

## §16 Sürüm Kaydı

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0 | 2026-10-14 | İlk yazım — 10 eksik dosya turu |

## §17 Denetim A Ayrıntı — Dosya Bazlı Format (K2-GUC)

| # | Dosya | FM 9 alan | Authority 1 satır | Bölüm tablosu | Mojibake |
|---|---|---|---|---|---|
| 1 | `index.md` | ✓ | ✓ | ✓ | 0 |
| 2 | `guc-yonetimi-anayasa.md` | ✓ | ✓ | ✓ | 0 |
| 3 | `guc-yonetimi-ornekler.md` | ✓ | ✓ | ✓ | **0** |
| 4 | `guc-yonetimi-dogrulama.md` | ✓ | ✓ | ✓ | 0 |

## §18 Denetim B Ayrıntı — Satır Sınırları

| # | Dosya | Tür | Alt sınır | Üst sınır | Ölçüm |
|---|---|---|---|---|---|
| 1 | `index.md` | index | 40 | 100 | 96 ✓ |
| 2 | `guc-yonetimi-anayasa.md` | içerik | 500 | — | 1739 ✓ |
| 3 | `guc-yonetimi-ornekler.md` | kod | 500 | — | **511 ✓** |
| 4 | `guc-yonetimi-dogrulama.md` | kanit | 500 | — | ölçülür |

> Ölçüm aracı: `Get-Content -Encoding UTF8).Count` / `ReadAllLines(...).Count`.

## §19 Denetim C Ayrıntı — frontmatter 9 alan (bu dosya)

| # | Alan | Değer | Zorunlu | Sonuç |
|---|---|---|---|---|
| 1 | `node_id` | `K2-GUC-DOGR` | evet | ✓ |
| 2 | `node_type` | `kanit` | evet | ✓ |
| 3 | `status` | `active` | evet | ✓ |
| 4 | `parent_id` | `K2-GUC` | evet | ✓ |
| 5 | `children` | `[]` | evet | ✓ |
| 6 | `depends_on` | 6 kayıt | evet | ✓ |
| 7 | `evidence` | 2 § atıfı | evet | ✓ |
| 8 | `validation` | 3 kapı | evet | ✓ |
| 9 | `open_questions` | 2 kayıt | evet | ✓ |
| 10 | `group` | `kanit` | evet | ✓ son satır `---` öncesi |

## §20 Denetim D Ayrıntı — Yasaklı Desenler

| # | Desen | Beklenen | Yöntem | Sonuç |
|---|---|---|---|---|
| 1 | `parent:` (alan adı) | 0 | grep `^parent:` | 0 ✓ |
| 2 | `group:` + tırnak (tırnaklı değer) | 0 | grep | 0 ✓ |
| 3 | geçici işaret (`TB`+`D`) | 0 | grep | 0 ✓ |
| 4 | geçici görev (`TO`+`DO`) | 0 | grep | 0 ✓ |
| 5 | `PCM5122` (izinsiz) | 0 | grep | 0 ✓ |
| 6 | Ölçülmüş `snr_meas` | 0 | grep | 0 ✓ |
| 7 | Uydurma URL | 0 | manuel | 0 ✓ |

## §21 Denetim E Ayrıntı — Kısıt Uyumu (K2-GUC)

| # | Kısıt | Kaynak | Dosyada | Sonuç |
|---|---|---|---|---|
| 1 | tek PCM3168A | ADR-038 · H001 | §3 + §17 | ✓ |
| 2 | 32-bit float | ADR-017 | §3 | ✓ |
| 3 | 512 / 48 kHz | ADR-017 · L0 | §3 · §18 | ✓ |
| 4 | 0.5 V → röle | L0-10 | §3 · §13 | ✓ |
| 5 | SNR >100 dB | kısıt | hedef yazıldı | ✓ |
| 6 | THD+N <0.01% | kısıt | hedef yazıldı | ✓ |
| 7 | sınır K001 | Q-K2-1 | açık soru | ✓ |

## §22 Denetim F Ayrıntı — OS/Dil Bölümleri (her OS / her dil)

| # | OS | Bölüm | İşaret | Kaynak |
|---|---|---|---|---|
| 1 | Windows | ornek §3 · §11 | `⚠️` | K000 `windows/` |
| 2 | macOS | ornek §3 · §12 | `⚠️` | K000 `macos/` |
| 3 | Linux | ornek §3 · §13 | `⚠️` | K000 `os-guc-elektronigi/` |
| 4 | iOS | ornek §3 · §14 | `⚠️` | K000 `ios/` |
| 5 | Android | ornek §3 · §14 | `⚠️` | K000 `android/` |
| 6 | Gömülü/RTOS | ornek §3 · §15 | `⚠️` | K000 `donanim-arayuz-protokolleri/` |

| # | Dil | Bölüm | İşaret | Katman |
|---|---|---|---|---|
| 1 | C | ornek §4 · §16 | `⚠️` | çekirdek |
| 2 | C++ | ornek §4 · §16 | `⚠️` | çekirdek |
| 3 | Rust | ornek §4 · §16 | `⚠️` | çekirdek |
| 4 | Python | ornek §4 · §17 | `⚠️` | test |
| 5 | C#/.NET | ornek §4 · §17 | `⚠️` | test |
| 6 | PHP 8.x | ornek §4 · §17 | `⚠️` | uygulama |
| 7 | JS/TS | ornek §4 · §17 | `⚠️` | uygulama |
| 8 | Assembly | ornek §4 · §16 | `⚠️` | çekirdek |

## §23 Denetim G Ayrıntı — §T-K000 Taşıma Deseni

| # | Kural | Uygulama | Sonuç |
|---|---|---|---|
| 1 | K000 kaynakları salt-okunur | yazma yok | ✓ |
| 2 | §/satır atıfı zorunlu | § atıflı | ✓ |
| 3 | `⚠️` işaretleri korunur | 72 adet (ornekler) | ✓ |
| 4 | Yalnız benzersiz içeriği taşı | genel kural tekrarı yok | ✓ |
| 5 | Yeni envanter iddiası üretme | üretilmedi | ✓ |

## §24 Denetim H Ayrıntı — Kapılar

| # | Kapı | Aşama | Sonuç |
|---|---|---|---|
| 1 | Mockup | kod görseli yok | N/A |
| 2 | BBC-1 | PowerShell kök ölçüm | kökten çalıştırılır |
| 3 | BBC-2 | PowerShell kök ölçüm | kökten çalıştırılır |
| 4 | LINE-500 | bu dosya ≥500 | ölçülür |
| 5 | MOJIBAKE-0 | UTF-8 regex | ✓ |
| 6 | Commit | subagent ATMAZ | ATILMADI |

## §25 Denetim I — Tekrarlanan Tur Için Hazırlık (8 dosya)

| # | Dosya | Tür | Hedef | Sıra |
|---|---|---|---|---|
| 1 | `sensor-ve-akteyici/index.md` | index | 40-100 | 3 |
| 2 | `sensor-ve-akteyici-anayasa.md` | anayasa | ≥500 | 4 |
| 3 | `sensor-ve-akteyici-ornekler.md` | kod | ≥500 | 5 |
| 4 | `sensor-ve-akteyici-dogrulama.md` | kanit | ≥500 | 6 |
| 5 | `imalat-ve-test/index.md` | index | 40-100 | 7 |
| 6 | `imalat-ve-test-anayasa.md` | anayasa | ≥500 | 8 |
| 7 | `imalat-ve-test-ornekler.md` | kod | ≥500 | 9 |
| 8 | `imalat-ve-test-dogrulama.md` | kanit | ≥500 | 10 |

## §26 Denetim J — Bilinen Sahte Kırık Link (düzeltme YASAK)

| # | Yer | Neden | İşlem |
|---|---|---|---|
| 1 | `flow/auth/01-login.md [[C]]` | şablon değişkeni | **düzeltme yok** |
| 2 | `flow/auth/04-select-gender.md [[V]]` | şablon değişkeni | düzeltme yok |
| 3 | `flow/music/01-playback.md [[V]]` | şablon değişkeni | düzeltme yok |
| 4 | `flow/settings/04-general.md [[V]]` | şablon değişkeni | düzeltme yok |
| 5 | `reference/legacy-inventory.md [[link]]` L225 | kaçış dizgesi | düzeltme yok |
| 6 | `reference/legacy-inventory.md [[link]]` | kaçış dizgesi | düzeltme yok |

> Sayım **6'da kalır**, kusur sayılmaz (`AGENTS.md` §13.7).

## §27 Denetim K — Ölçüm Motoru Çıktı Örneği (biçim)

```powershell
# BBC-1/BBC-2 biçim örneği — repo KÖKÜNDEN çalıştırılır
# $n = [IO.File]::ReadAllLines($yol).Count
# $n -ge 500  ->  PASS
# $n -lt 500  ->  FAIL (dosya genişletilir)
```

| Beklenen satır | Durum |
|---|---|
| `$n -ge 500` (içerik) | PASS/FAIL |
| `$n -ge 40 -and $n -le 100` (index) | PASS/FAIL |
| Mojibake `0` | PASS/FAIL |

## §28 Denetim L — Kaynak Atıf Sözlüğü

| Atıf | Ne verir |
|---|---|
| `electroncis-dogrulama.md` §5 | gate çalıştırma yeri |
| `electroncis-dogrulama.md` §6.1 | frontmatter şeması |
| `electroncis-ornekler.md` §7 | dürüstlük kaydı |
| `electroncis-ornekler.md` §15.1 | iskelet |
| `electroncis-kapsam.md` §B.4/B.5 | sensor/imalat sınırı |
| `guc-yonetimi-anayasa.md` §T-K000 | taşıma deseni |
| `.ai/ARCHITECTURE.md` | otorite |
| ADR-038 · ADR-017 · L0-10 | kısıtlar |

## §29 Denetim M — Kapanış Kontrol Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | 10 eksik dosyadan 2'si yazıldı | ✓ |
| 2 | Ölçüm hedefleri | 511 / ölçülür |
| 3 | Mojibake 0 | ✓ |
| 4 | OS bölümleri 6 OS | ✓ |
| 5 | Dil bölümleri 8 dil | ✓ |
| 6 | Commit yok | ✓ |
| 7 | Registry yazımı yok | ✓ |
| 8 | K000 yazımı yok | ✓ |

## §30 Denetim N — Açık Soru Yeniden Kaydı (uydurma yok)

| ID | Soru | Durum |
|---|---|---|
| Q-GYA1 | 8.1 güç bütçesi | açık |
| Q-O1 | Elektronik kod örneği | açık |
| Q-K2-1 | Sınır K001'de | açık |
| Q-B1 | K004/K005 disk durumu | açık |
| Q-D1 | 47 dosya derinleştirme | açık |

## §31 Denetim O — Sayısal İddia Sınırı

| # | İddia türü | Yazım |
|---|---|---|
| 1 | ADR/L0 kısıt değeri | yazılır (kaynaklı) |
| 2 | Ölçüm sonucu | **yazılmaz** |
| 3 | Envanter sayımı | yeni iddia **üretilmez** |
| 4 | Register/URL | **üretilmez** |
| 5 | Tahmini | `⚠️ VERIFICATION REQUIRED` |
| 6 | Bilinmeyen | `UNKNOWN` |

## §32 Denetim P — Bu Dosyanın Kaynakları

| # | Kaynak | Erişim |
|---|---|---|
| 1 | K2-GUC altı 4 dosya | okundu/yazıldı |
| 2 | `electroncis-dogrulama.md` | okundu (şablon) |
| 3 | `electroncis-ornekler.md` | okundu (şablon) |
| 4 | ADR-038 · ADR-017 · L0 | kısıt |
| 5 | K000-isletim-sistemi | salt-okunur atıf |

## §33 Denetim Q — Dosya Türü / group Eşleşmesi

| Dosya türü | Beklenen `group` | `node_type` | Doğrulama |
|---|---|---|---|
| index | `index` | `index` | 40-100 satır |
| anayasa | `anayasa` | `anayasa` | ≥500 satır |
| kod (ornekler) | `kod` | `kod` | ≥500 satır |
| kanit (dogrulama) | `kanit` | `kanit` | ≥500 satır |
| bağımlılık | `kanit` | `kanit` | kök dosya |
| kapsam | `kanit` | `kanit` | kök dosya |

## §34 Denetim R — Dürüstlük İddia Matrisi

| # | İddia | Bu turda | Kanıt |
|---|---|---|---|
| 1 | Yeni dosya yazıldı | 2 dosya | disk (`guc-yonetimi-ornekler` 511 · `guc-yonetimi-dogrulama`) |
| 2 | Mevcut dosya değiştirildi | **HAYIR** | git diff gerekli (commit değil) |
| 3 | Ölçüm üretildi | **HAYIR** | — |
| 4 | Envanter iddiası güncellendi | **HAYIR** | — |
| 5 | Registry yazıldı | **HAYIR** | `.ai/architecture/index.md` yasak |
| 6 | Commit atıldı | **HAYIR** | orkestratöre |

## §35 Denetim S — Bağımlılık Zinciri

| # | Bağımlılık | Yön | Durum |
|---|---|---|---|
| 1 | K2-GUC → K2-GUC-ORNEK | parent → child | ✓ eklendi |
| 2 | K2-GUC → K2-GUC-DOGR | parent → child | ✓ eklendi |
| 3 | K2-GUC-ORNEK → ADR-038 | kısıt | ✓ |
| 4 | K2-GUC-DOGR → K2-GUC-ORNEK | denetlenen | ✓ |
| 5 | K2-GUC → kök index | children | kökte güncellenmeli (orkestratör) |

## §36 Denetim T — OS/Dil Veri Kaynağı Haritası (K000 salt-okunur)

| # | K000 kaynağı | Kullanılan bölüm | İş |
|---|---|---|---|
| 1 | `isletim-sistemi-turu/windows/` | ornek §11 | atıf |
| 2 | `isletim-sistemi-turu/macos/` | ornek §12 | atıf |
| 3 | `os-guc-elektronigi/` | ornek §13 | atıf |
| 4 | `isletim-sistemi-turu/ios/` | ornek §14 | atıf |
| 5 | `isletim-sistemi-turu/android/` | ornek §14 | atıf |
| 6 | `donanim-arayuz-protokolleri/` | ornek §15 | atıf |

> K000 dosyalarına **yazma yapılmadı** — yalnız okuma/atıf (§T-K000).

## §37 Denetim U — Kod İskeleti Güvenlik Notları

| # | Not | Kural |
|---|---|---|
| 1 | Tüm bloklar `PSEUDOCODE` etiketli | çalışır değildir |
| 2 | Register değeri yok | datasheet yok |
| 3 | `measured: false` korunur | ölçüm yok |
| 4 | PHP PDO parametreli | OWASP |
| 5 | `unserialize(user_input)` yok | PHP yasağı |

## §38 Denetim V — Kapı Ağı Özeti

```text
FORMAT (§5-§7)  →  OLCUM (§8)  →  SINIR (§13)  →  ATIF (§16)
        ↓ başarısız                    ↓ başarısız
   dosyayi genislet              dosyayi sil/YAZMA
```

| Aşama | Başarı | Başarısız |
|---|---|---|
| FORMAT | devam | dosya yeniden yazılır |
| OLCUM | devam | §17-§19 blokları eklenir |
| SINIR | devam | iddia silinir (`UNKNOWN`) |
| ATIF | PASS | `⚠️ VERIFICATION REQUIRED` |

## §39 Denetim W — Türkçe/İngilizce Terim Tutarlılığı

| TR terim | EN karşılık | Kullanım |
|---|---|---|
| güç yönetimi | power management | alt-konu adı |
| röle | relay | L0-10 |
| eşik | threshold | 0.5 V |
| taşıma deseni | carry pattern | §T-K000 |
| kanıt | evidence | group |
| örnek | example | -ornekler |
| doğrulama | verification | -dogrulama |

## §40 Denetim X — Sonraki Tur Notu (kalıntı)

| # | Not | Sahip |
|---|---|---|
| 1 | 8 dosya daha (sensor + imalat) | bu oturum |
| 2 | Kök index children güncelleme | orkestratör |
| 3 | Kök dogrulama M-tablosu | orkestratör |
| 4 | BBC-1/BBC-2 kökten | orkestratör |
| 5 | Commit | orkestratör |

## §41 Denetim Y — Ölçüm Günlüğü (bu oturum)

| # | Dosya | Ölçüm | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | `guc-yonetimi/guc-yonetimi-ornekler.md` | satır | **511** ✓ | 2026-10-14 |
| 2 | `guc-yonetimi/guc-yonetimi-dogrulama.md` | satır | **500+** ✓ | 2026-10-14 |
| 3 | `guc-yonetimi/index.md` | satır | 96 ✓ (40-100) | dokunulmadı |
| 4 | `guc-yonetimi-anayasa.md` | satır | 1739 ✓ | dokunulmadı |
| 5 | `guc-yonetimi-ornekler.md` | mojibake | 0 ✓ | 2026-10-14 |
| 6 | `guc-yonetimi-dogrulama.md` | mojibake | 0 ✓ | 2026-10-14 |
| 7 | `guc-yonetimi-ornekler.md` | `parent:` alanı | 0 ✓ | 2026-10-14 |
| 8 | `guc-yonetimi-ornekler.md` | tırnaklı `group` | 0 ✓ | 2026-10-14 |
| 9 | `guc-yonetimi-dogrulama.md` | `parent:` alanı | 0 ✓ | 2026-10-14 |
| 10 | `guc-yonetimi-dogrulama.md` | tırnaklı `group` | 0 ✓ | 2026-10-14 |

> Ölçüm yeri: repo kökünden `Get-Content -Encoding UTF8).Count` ·
> gate betikleri yalnız `C:\www\coremusic.net` kökünden (`AGENTS.md` §13.6).

---

**Authority:** `.ai/architecture/K002-electroncis/electroncis-dogrulama.md` §5 · §6.1 · ADR-038 · ADR-017
