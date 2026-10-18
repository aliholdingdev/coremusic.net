---
node_id: K004-dsp/dsp-web-arastirma
node_type: standard
status: draft
parent_id: K004-dsp
children: []
depends_on: [K003-audio, K000-isletim-sistemi, K001-donanım, K005-firmware]
evidence:
  - "K004-dsp/dsp-master.md §B.4 (15 URL, HTTP 200 · 2026-10-13) · 2026-10-13"
  - "K004-dsp/dsp-master.md §B.3 (Signalsmith MIT lisans doğrulaması) · 2026-10-13"
  - "Bu dosya: 8 URL webfetch taze doğrulama · 2026-10-18 (6 ✅ / 2 ⚠️ gövde alınamadı)"
validation: draft — 15 ana kaynak master §B.4 (2026-10-13) + 6 kaynak bu oturum (2026-10-18) doğrulandı; 2 URL bu oturumda gövde vermedi ⚠️; red-team review bekliyor
open_questions:
  - "⚠️ docs.juce.com ve itu.int/BS.1770 bu oturumda boş gövde döndü (2'şer deneme) — 2026-10-13 HTTP 200 kaydı esas, teyit tekrarı gerekebilir"
  - "⚠️ Signalsmith sürümü README'de kontrol örneği (1,7,0) görünür — mevcut sürüm mü yoksa örnek mi belirsiz (UNKNOWN, T9)"
group: kanit
---

# DSP — Web Araştırması (K004-dsp)

> **Bu dosya nedir?** DSP kararlarının (kod-örnekleri/dil kuralları/ölçüm kapıları) dayandığı
> dış web kaynaklarının doğrulama kaydı. Kural: **her URL erişim tarihiyle** kaydedilir; erişilemeyen
> `⚠️ DEAD?` işaretlenir; doğrulanmayan iddia `⚠️ VERIFICATION REQUIRED` kalır. Uydurma URL yok.

## 1. ELI15 — Web Araştırması Neden Doğrulanır?

LLM'ler internette olmayan sayfaları, yanlış sürümleri ve eskimiş bilgileri **gerçek gibi**
yazabilir. Bu dosya bu tuzağa karşı sigortadır: her satır "şu adresi şu tarihte açtım, şu çıktı"
der. İki tur vardır — **tur 1** (2026-10-13, `dsp-master.md` §B.4, 15 URL ✅) ve **tur 2**
(bu dosya, 2026-10-18, 8 URL taze webfetch). Bir URL bu oturumda açılmazsa "ölüdür" demek yerine
`⚠️` koyarız: belki geçici ağ hatasıdır, belki sayfa taşınmıştır — ikisini de **kanıt ayırır**.

## 2. Doğrulama Özeti

| Tur | Kaynak | Tarih | Sonuç |
|-----|--------|-------|-------|
| 1 | `dsp-master.md` §B.4 — 15 URL listesi | 2026-10-13 | 15/15 ✅ HTTP 200 (master kaydı) |
| 2 | Bu dosya — 8 URL webfetch (§4) | 2026-10-18 | **6 ✅ / 2 ⚠️** (boş gövde, 2'şer deneme) |
| 2+ | `dsp-master.md` §B.3 — Signalsmith lisans | 2026-10-13 + 2026-10-18 ✅ | MIT doğrulandı (bu oturum GitHub sayfası teyitli) |

## 3. Tur 1 — Master Kaynak Tablosu (§B.4 · 15 URL · erişim 2026-10-13)

| # | Kaynak | URL | K004 kullanımı | Erişim | Durum |
|---|--------|-----|----------------|--------|-------|
| 1 | C++ draft standardı | https://eel.is/c++draft/ | Dil bağlayıcısı (C++20 kuralları) | 2026-10-13 | ✅ HTTP 200 |
| 2 | cppreference | https://en.cppreference.com/w/ | `std::atomic`, `alignas` referansı | 2026-10-13 | ✅ HTTP 200 |
| 3 | ISO C++ | https://isocpp.org/ | Standart kapısı | 2026-10-13 | ✅ HTTP 200 |
| 4 | GCC belgeleri | https://gcc.gnu.org/onlinedocs/ | `-ffast-math`/FP yarı-kesinlik uyarıları | 2026-10-13 | ✅ HTTP 200 |
| 5 | MSVC `/fp` | https://learn.microsoft.com/en-us/cpp/build/reference/fp-specify-floating-point-behavior | FP davranışı referansı | 2026-10-13 | ✅ HTTP 200 |
| 6 | UBSan (Clang) | https://clang.llvm.org/docs/UndefinedBehaviorSanitizer.html | Undefined davranış denetimi | 2026-10-13 | ✅ HTTP 200 |
| 7 | Valgrind | https://valgrind.org/ | Bellek denetimi (Linux) | 2026-10-13 | ✅ HTTP 200 |
| 8 | Cppcheck | https://cppcheck.sourceforge.io/ | Statik analiz | 2026-10-13 | ✅ HTTP 200 |
| 9 | CMake | https://cmake.org/cmake/help/latest/ | Build tanımı | 2026-10-13 | ✅ HTTP 200 |
| 10 | RFC 2119 | https://www.rfc-editor.org/rfc/rfc2119 | MUST/SHOULD şartname dili | 2026-10-13 | ✅ HTTP 200 |
| 11 | NIST SP 800-82r3 | https://csrc.nist.gov/pubs/sp/800/82/r3/final | Ölçüm güvence çerçevesi | 2026-10-13 | ✅ HTTP 200 |
| 12 | ITU-R BS.1770-5 | https://www.itu.int/rec/R-REC-BS.1770-5/en | Loudness algoritması standardı | 2026-10-13 | ✅ HTTP 200 |
| 13 | EBU Tech 3344 | https://tech.ebu.ch/docs/tech/tech3344.pdf | Loudness ölçüm notu | 2026-10-13 | ✅ HTTP 200 |
| 14 | JUCE doxygen kökü | https://docs.juce.com/ | DSP envanteri kapısı | 2026-10-13 | ✅ HTTP 200 |
| 15 | `juce::dsp` namespace | https://docs.juce.com/master/namespacejuce_1_1dsp.html | İşleyici listesi (Limiter/Reverb/Convolution…) | 2026-10-13 | ✅ HTTP 200 |

Ek (§B.4 devamı): MDN Web Audio API (`https://developer.mozilla.org/en-US/docs/Web/API/Web_Audio_API`) ve W3C Web Audio önergesi (`https://www.w3.org/TR/webaudio/`) — 2026-10-13 ✅ HTTP 200 (tarayıcı referans modeli, toplam 17 satır).

## 4. Tur 2 — Bu Oturum Taze Doğrulama (webfetch · 2026-10-18)

| # | URL | Erişim | Durum | Gözlem (çıktıdan) |
|---|-----|--------|-------|-------------------|
| 1 | https://eel.is/c++draft/ | 2026-10-18 | ✅ HTTP 200 | Draft içeriği: "Generated on 2026-08-23" — güncel |
| 2 | https://en.cppreference.com/w/cpp/atomic/memory_order | 2026-10-18 | ✅ HTTP 200 | `memory_order` tam metni; `consume` C++26'da deprecated notu dahil |
| 3 | https://www.rfc-editor.org/rfc/rfc2119 | 2026-10-18 | ✅ HTTP 200 | MUST/SHOULD/MAY tanımları; "updated by RFC 8174" notu görünür |
| 4 | https://clang.llvm.org/docs/UndefinedBehaviorSanitizer.html | 2026-10-18 | ✅ HTTP 200 | UBSan tam dokümanı: `-fsanitize=undefined` kontrol listesi, minimal runtime, Windows dahil platform listesi |
| 5 | https://cmake.org/cmake/help/latest/ | 2026-10-18 | ✅ HTTP 200 | Sayfa başlığı: **CMake 4.5.0-rc1 Documentation** (docs latest bu sürümde) |
| 6 | https://github.com/Signalsmith-Audio/dsp | 2026-10-18 | ✅ HTTP 200 | **279 ★ · 86 commit · MIT · header-only C++11**; dosyalar: `fft.h filters.h delay.h envelopes.h spectral.h …`; README'de `SIGNALSMITH_DSP_VERSION_CHECK(1, 7, 0)` örneği |
| 7 | https://docs.juce.com/ | 2026-10-18 | ⚠️ gövde alınamadı (2 deneme) | Boş yanıt — **DEAD? değil, teyit bekliyor**; master 2026-10-13 HTTP 200 kaydı esas alınır |
| 8 | https://www.itu.int/rec/R-REC-BS.1770-5/en | 2026-10-18 | ⚠️ gövde alınamadı (2 deneme) | Boş yanıt — master 2026-10-13 + K002 oturumu doğrulaması esas; tekrar teyit önerilir |

**Elenen deneme (kayıt için):** `clang.llvm.org/Manual/Ubsan.html` → 404 — bu adres **yok**;
doğru adres master'daki `/docs/UndefinedBehaviorSanitizer.html` (satır 260). URL yazarken
kaynaktan kopyala, tahmin etme dersi.

## 5. Doğrulama Yöntemi (5 adım)

1. **URL'i kaynaktan al** — `dsp-master.md` §B.4 satırı gibi; hafızadan/yazım-tahmininden URL üretme (§4 elenmesi).
2. **webfetch ile aç** — HTTP gövdesi + durum kodu; 2xx ise içerik kontrolü (sayfa gerçekten konuyla mı ilgili?).
3. **Erişim tarihini yaz** — `YYYY-MM-DD`; tarihsiz doğrulama yok sayılır.
4. **Boş gövde ≠ 404** — boş yanıt `⚠️ gövde alınamadı`, 404/410 `⚠️ DEAD?` olarak ayrı işaretlenir.
5. **Sonucu satıra işle** — kanıt (çıktıdan bir olgusal detay: sürüm/yıldız/tanım) ile birlikte; kanıtsız "✅" yazılmaz.

## 6. Eşlemesi Olmayan / Doğrulanmayan İddialar

| İddia | Durum | Not |
|-------|-------|-----|
| JUCE 9 sürüm numarası | ⚠️ VERIFICATION REQUIRED | docs.juce.com bu oturumda gövde vermedi; sürüm sayfası teyidi bekliyor |
| ASIO SDK 2.3.4 sürüm numarası | ⚠️ VERIFICATION REQUIRED | §B.4'te Microsoft ASIO sürüm sayfası **yok** — URL eklenmeli |
| Signalsmith mevcut sürüm (1,7,0?) | UNKNOWN | README kontrol örneği (1,7,0) görüldü; mevcut sürüm mü örnek mi belirsiz → T9 pin |
| CMake "latest" = 4.5.0-rc1 | ✅ 2026-10-18 | Docs başlığından okundu — **rc** sürüm; üretim pin'i bu olmayabilir |
| DSPark benchmark rakamları (90+ yıldız ↔ 2★) | ✅ master §B.2 | Q1 kapsamında; resmi repo URL'i Q1 kararında eklenir |
| EMC/ölçüm standartı sayıları (IEC 61000-4-x vb.) | ⚠️ VERIFICATION REQUIRED | K004 kapsamında değil (K002 alanı); bu dosyada doğrulanmadı |

## 7. Kullanım Kuralları

1. `dsp-kod-ornekleri.md` ve `dsp-test-stratejisi.md` bu dosyadaki URL'leri **erişim tarihiyle** atıf yaparak kullanır.
2. Yeni kaynak eklenecekse §4 tablosuna aynı gün webfetch sonucuyla yazılır — tarihsiz satır silinir.
3. `⚠️ DEAD?` işaretli URL kararı değiştirmez; taşınsa bile **içerik kaynağı** (ör. BS.1770) korunur, adres aranır.
4. Sürüm iddiaları (`dsp-kararlar.md` §6) yalnız bu dosyadaki ✅ kanıtla güncellenir; `⚠️` kalır → üretim sürümü asla yazılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
