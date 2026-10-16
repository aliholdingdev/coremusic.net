---
title: "K011 · zamanlamali-gorevler — Giriş"
type: architecture
kodeme: kademe-2
module: K011-application
parent: zamanlamali-gorevler
status: active
updated: 2026-10-15
group: mimari
---

# K011-application/zamanlamali-gorevler — Zamanlamalı Görevler

**Amaç:** Bu çocuk, CoreMusic uygulamasındaki "zaman bağlı tetikleme, gecikme ve periyodik
güncelleme" yüzeyini tek yerde toplar: istemcide `setTimeout`/`setInterval`, sunucuda
`time()` tabanlı **tembel (lazy) zamanlama**, şemada ise CRON/QUEUE/WORKER için
**boşluk bırakılmış** enum'lar. Kanıt 2026-10-15 disk ölçümüdür.

**Kritik bulgu:** Repo'da **çalışan cron altyapısı YOK** (PHP `cron|crontab` 0 isabet;
JS `queue|worker` 0 gerçek kuyruk; SQL enum'larında CRON/QUEUE/WORKER değerleri var ama
**üreten kod yok**). Zamanlama fiilen üç katmanda yaşar: JS istemci zamanlayıcıları,
PHP istek-zamanı tembel kontrolü ve SQL şema yer tutucuları.

## §GRUPLAR

| Bölüm | Konu |
|---|---|
| §1 | Çocuk dosyalar (6 dosya) |
| §2 | Kapı durumu (özet) |
| §3 | Ölçüm özeti (2026-10-15) |
| §4 | Açık sorular (özet) |

---

## §1 — Çocuk dosyalar

| # | Dosya | Grup | Amaç |
|---|---|---|---|
| 1 | `index.md` | mimari | Giriş · katalog · kapı durumu (bu dosya) |
| 2 | `zamanlamali-gorevler-kapsam.md` | mimari | Kapsam sınırı, 3 katman envanteri, akış tabloları |
| 3 | `zamanlamali-gorevler-dogrulama.md` | kanit | İddia↔dosya↔satır, test envanteri, kapı PASS/BLOCK, KPI |
| 4 | `zamanlamali-gorevler-web-arastirma.md` | kanit | 5 URL havuz kaydı, kesişim, claim↔repo matrisi |
| 5 | `zamanlamali-gorevler-ornekler.md` | kod | PHP/JS/SQL/CSS gerçek kod blokları + YAPMA↔YAP |
| 6 | `zamanlamali-gorevler-bilesenler.md` | mimari | Bileşen envanteri, akış iz düşümü, bağımlılık grafiği |

## §2 — Kapı durumu (özet)

| # | Kapı | Durum | Kanıt |
|---|---|---|---|
| 1 | Zamanlayıcı kodu bulundu mu? | ✅ PASS | JS 20 isabet / 14 dosya (grep, 2026-10-15) |
| 2 | Cron altyapısı var mı? | ❌ BLOCK (boşluk) | PHP `cron\|crontab` 0 isabet → PLANNED |
| 3 | PHP blocking sleep var mı? | ✅ PASS (yok) | `sleep()\|usleep()` 0 isabet — doğru kaçınma |
| 4 | URL havuzu erişilebilir mi? | ✅ PASS | 5/5 HTTP 200 (curl.exe -L, 2026-10-15) |
| 5 | Envante kesişimi | ✅ PASS (0) | 4286 envanter URL × 5 havuz → HIT=0 |
| 6 | Test kapsamı zamanlayıcıda | ❌ BLOCK | JS spec 0 timer testi (9 dosya, fakeTimers yok) |
| 7 | CSS süre tokenları | ⚠️ ÇELIŞKİ | `--duration-fast` 100ms vs 0ms (iki dosya) |

## §3 — Ölçüm özeti (2026-10-15)

| Yüzey | Sayı | Yöntem |
|---|---|---|
| JS `.js` dosyası | 95 (7 spec) | `Get-ChildItem -Filter *.js` |
| JS `setTimeout\|setInterval` | 20 isabet / 14 dosya | `Select-String` |
| PHP `shared/src` | 203 dosya / 20 dizin | `Get-ChildItem -Recurse` |
| PHP `sleep\|usleep` | 0 | `Select-String` |
| SQL `.ai/.sql/mysql` | 20 dosya | `Get-ChildItem -Filter *.sql` |
| CSS `01_Abstracts` | 19 dosya | `Get-ChildItem -Filter *.css` |
| PHP test | 45 dosya / 35 zaman isabeti | `Select-String` |

## §4 — Açık sorular (özet)

1. Cron/queue/worker altyapısı ne zaman ve hangi düğümde (K011 mi K012 mi)? → ⚠️ VERIFICATION REQUIRED
2. `--duration-fast` çakışması hangi dosyanın lehine çözülecek? → ⚠️ VERIFICATION REQUIRED
3. SQL enum'larındaki CRON/QUEUE/WORKER değerlerini kim/neye göre dolduracak? → PLANNED

> Ayrıntı ve spec §8 satırı: `zamanlamali-gorevler-kapsam.md` §6 ·
> `zamanlamali-gorevler-dogrulama.md` §7 · `zamanlamali-gorevler-web-arastirma.md` §6.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
