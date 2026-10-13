---
title: "Testing — Doğrulama"
type: architecture-scope
node_id: K006-yapay-zeka/testing
node_type: subdomain-verification
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# Testing — Doğrulama

Amaç: `testing-kapsam.md` içindeki iddiaların kaynaklarını listelemek. Her satır bir iddia + kaynak + tarih + durum. Durum: ✅ doğrulandı · ⚠️ kısmi · UNKNOWN doğrulanamadı.

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | scikit-learn cross-validation, test/validasyon bölme ve veri sızıntısı (leakage) önleme resmi belgelerde anlatılır | https://scikit-learn.org/stable/modules/cross_validation.html | 2026-10-12 | ✅ |
| 2 | scikit-learn `cross_validate` API'si, çoklu metrikli çapraz doğrulama çağrısını resmi dokümante eder | https://scikit-learn.org/stable/modules/generated/sklearn.model_selection.cross_validate.html | 2026-10-12 | ✅ |
| 3 | Evidently AI "Tests" kütüphanesi, üretim modeli için otomatik kalite/drift test tanımlar (test mantığı için referans) | https://docs.evidentlyai.com/docs/library/tests | 2026-10-12 | ✅ |
| 4 | Evidently quickstart (monitoring/test kurulum adımları) resmi dokümanda anlatılır | https://docs.evidentlyai.com/quickstart_ml | 2026-10-12 | ✅ |
| 5 | K006 testing v2 kapsamı: "unit · integration · regression · fuzz" | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\v2.md` §2.4-10 (satır 252) | 2026-10-12 | ✅ |
| 6 | ai-master.md §3'te testing çıktısı "AI unit · integration test" olarak kayıtlıdır | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §3 (satır 72) | 2026-10-12 | ✅ |
| 7 | `shared/tests/` PHPUnit yapısı mevcuttur (test altyapısı dizini diskte) | `C:\www\coremusic.net\shared\tests\` | 2026-10-12 | ✅ |
| 8 | `shared/tests/` içinde AI'a dair test dosyası YOKTUR (grep `AI` → 0 isabet) | `C:\www\coremusic.net\shared\tests` — grep `AIEngine\|AIWorkflow\|AIOrchestrator` (0 isabet) | 2026-10-12 | ✅ |
| 9 | `yapay-zeka-bagimliliklar.md` testing bağımlılık satırında "AI test kütüphanesi: UNKNOWN" kayıtlıdır | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\yapay-zeka-bagimliliklar.md` (satır 24) | 2026-10-12 | ✅ |
| 10 | CoreMusic'te AI koduna dair test dosyası bulunamadı (AIEngine/AIWorkflow testi yok) | `C:\www\coremusic.net\shared\tests` — grep (0 isabet) | 2026-10-12 | ⚠️ |
| 11 | AI testleri CI'a bağlı değildir (CI step'inde AI testi referansı yok) | `C:\www\coremusic.net\ci` + `shared/ci` — grep `ai` ile AI test adımı yok (kapsam maddesi 5, CI eksiği) | 2026-10-12 | ⚠️ |
| 12 | AI test kütüphanesi seçimi (PHPUnit · özel fixture) belirsizdir | UNKNOWN | 2026-10-12 | UNKNOWN |
| 13 | Determinizm/seed politikası ve hedef coverage oranı sabitlenmemiştir | UNKNOWN | 2026-10-12 | UNKNOWN |

| 14 | IEEE, fuzz testing'i malformed/semi-random girdilerle crash/bellek bozulması arama yöntemi olarak tanımlar | https://technav.ieee.org/topic/fuzzing/ | 2026-10-12 | ✅ |
| 15 | Fuzz test orak (oracle): crash/hang = failure; gerekirse geçici dosyalama (temp filing) kullanılır | https://research.cs.wisc.edu/mist/SoftwareSecurityCourse/Chapters/38-Intro-Fuzz-Testing.pdf | 2026-10-12 | ✅ |
| 16 | Tarihsel kanıt: UW fuzz testing (Bart Miller, 1988) UNIX programlarında %25–33 crash oranına ulaştı | https://research.cs.wisc.edu/mist/SoftwareSecurityCourse/Chapters/38-Intro-Fuzz-Testing.pdf | 2026-10-12 | ✅ |
| 17 | Go, standart toolchain'de Go 1.18'den beri fuzzing destekler; coverage guidance ile çalışır | https://go.dev/doc/security/fuzz/ | 2026-10-12 | ✅ |
| 18 | Go fuzz test seed corpus (`F.Add` + `testdata/fuzz/`) ile çalışır; başarısız girdi seed corpus dosyasına yazılır | https://go.dev/doc/tutorial/fuzz | 2026-10-12 | ✅ |
| 19 | Go `go test -fuzz` + `-fuzztime` ile süre/iterasyon bütçesi sınırlanır | https://go.dev/doc/security/fuzz/ | 2026-10-12 | ✅ |
| 20 | Go fuzzing, birim testinin ulaşamadığı edge case'leri otomatik girdi üretimiyle keşfeder | https://go.dev/doc/tutorial/fuzz | 2026-10-12 | ✅ |
| 21 | Go coverage instrumentation fuzzing'i yalnız AMD64 ve ARM64 mimarilerinde destekler | https://go.dev/doc/tutorial/fuzz | 2026-10-12 | ✅ |
| 22 | AFL++ sanitizers (ASAN · MSAN · UBSAN · CFISAN · TSAN · LSAN) ile crash dışı hataları da yakalar | https://github.com/AFLplusplus/AFLplusplus/blob/stable/docs/features.md | 2026-10-12 | ✅ |
| 23 | AFL++ fuzzing üç adımlı süreçtir: instrument etme · corpus hazırlama · mutasyonla fuzzing | https://aflplus.plus/docs/fuzzing_in_depth/ | 2026-10-12 | ✅ |
| 24 | AFL++ libfuzzer `LLVMFuzzerTestOneInput()` harness'larıyla uyumludur (de facto standart) | https://aflplus.plus/docs/fuzzing_in_depth/ | 2026-10-12 | ✅ |
| 25 | AFL++ persistent mode, hedef fonksiyonları bellekte tekrar çağırarak fuzzing hızını artırır | https://aflplus.plus/docs/fuzzing_in_depth/ | 2026-10-12 | ✅ |
| 26 | AFL++ CI fuzzing'de secondary (`-S`) modları önerir; `-M` modu kısa CI koşuları için uygun değildir | https://aflplus.plus/docs/fuzzing_in_depth/ | 2026-10-12 | ✅ |
| 27 | AFL++ `AFL_CUSTOM_MUTATOR_LIBRARY` ile özel mutatör yüklenir (structure-aware fuzzing) | https://github.com/AFLplusplus/AFLplusplus/blob/stable/docs/custom_mutators.md | 2026-10-12 | ✅ |
| 28 | AI testlerinin CI'a bağlanması ve coverage eşiği tanımlanmadı | — | 2026-10-12 | UNKNOWN |
| 29 | Fuzz test girdi sınırları ve süre bütçesi sabitlenmedi | — | 2026-10-12 | UNKNOWN |
| 30 | Fuzz kapsamındaki girdi yüzeyleri (model dosyası · context · prompt) netleşmedi | — | 2026-10-12 | UNKNOWN |
| 31 | testing kademe-2/3 kapsamı resmileşmedi | ADR-097 K4 (referans) | 2026-10-12 | ⚠️ BEKLİYOR |

**Sayım:** ✅23 · ⚠️3 · UNKNOWN5 · Toplam31
