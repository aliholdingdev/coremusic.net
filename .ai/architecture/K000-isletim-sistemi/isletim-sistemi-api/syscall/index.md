---
node_id: K000-isletim-sistemi/isletim-sistemi-api/syscall
node_type: subdomain-scope
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-api
children:
  - K000-isletim-sistemi/isletim-sistemi-api/syscall/syscall-mekanizmasi
  - K000-isletim-sistemi/isletim-sistemi-api/syscall/syscall-hata-kodlari-ve-errno
  - K000-isletim-sistemi/isletim-sistemi-api/syscall/syscall-guvenlik-ve-sandbox
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-api/isletim-sistemi-api-kapsam
evidence:
  - shared/src/AI/AIEngine.php:288-310
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:185-215
validation: 2026-10-13 · dosya uzunlugu 40-100 satir araliginda ve URL'ler HTTP 200 ile dogrulandi
open_questions:
  - Q-P3 · hedef platform seti netlestirilmeli · ⚠️ VERIFICATION REQUIRED
group: mimari
---
# syscall alt dali — indeks

> `syscall` alt dali, isletim sistemi API'sinin cekirdek cagri yuzeyini
> kapsar: mekanizma (cagri nasil calisir), hata kodlari (hata nasil
> ayristirilir) ve guvenlik (cagri nasil sinirlandirilir). Bu uc dosya,
> `isletim-sistemi-api-kapsam.md` §A.3 · K8 kapsamindadir.

---

## §DOSYA — uc dosya · kapsam · grup

| Dosya | Kapsam | Group |
|---|---|---|
| syscall-mekanizmasi | cagri adimlari, kayitlar, arguman siniri | mimari |
| syscall-hata-kodlari-ve-errno | donus degeri araligi, errno okuma, hata siniflandirma | mimari |
| syscall-guvenlik-ve-sandbox | cagri sinirlandirma, yetki kontrolü, guvenlik acigi onleme | mimari |

## §KANIT — gercek repo baglantilari

| Dosya | Satır | Baglanti |
|---|---|---|
| shared/src/AI/AIEngine.php | 288-310 | surec cagrisi deseni (proc_open) |
| shared/tests/Repository/ApiKeyCreateCommandTest.php | 248-269 | fork ile surec bekleme (pcntl_fork) |
| home.coremusic.net/include/Stream/MusicStreamHandler.php | 185-215 | surec cagrisi ile dosya akisi |

## §GRUPLAR — Kademe-3 indeks

| Grup | Dosya | Durum |
|---|---|---|
| mimari | syscall-mekanizmasi.md | yazildi |
| mimari | syscall-hata-kodlari-ve-errno.md | yazildi |
| mimari | syscall-guvenlik-ve-sandbox.md | yazildi |

## §KAPSAM — sinirlar

| Sinir | Kural | Kaynak |
|---|---|---|
| Web katmani | kapsam disi | kapsam §A.3 · K1 |
| DSP | kapsam disi | kapsam §A.3 · K2 |
| Donanim | kapsam disi | kapsam §A.3 · K3 |
| Hedef platform | Q-P3 acik | kapsam §A.3 · K10 |
| URL siniri | yalnizca resmi belgeler | kapsam §A.3 · K4 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
