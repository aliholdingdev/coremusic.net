---
node_id: K005-firmware
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - boot
  - runtime
  - device-control
  - audio-control
  - power-control
  - diagnostics
  - update
  - security
  - testing
depends_on:
  - K001-donanım
evidence:
  - "v2 §2.5 mini-ağaç — 9 çocuk adı (2026-10-12 okundu)"
  - "firmware-master.md §4 — web kanıtları (ASIO SDK GPLv3 · TPA3255 datasheet · KiCad PBTL)"
validation: "draft — A1 tur-1; D-kriterleri firmware-dogrulama.md §2"
open_questions:
  - "ASIO SDK GPLv3 lisans etkisi firmware/SDK entegrasyonunda ölçülemedi (⚠️ VERIFY REQUIRED)"
  - "TPA3255 PVDD: manifest 24–52 V ↔ TI datasheet 18–53.5 V — iki değer de kayıtlı (⚠️)"
---

# K005-firmware — Firmware Alanı

## §1 ELI15

**Donanımın kendi küçük yazılımı — cihaz açılırken çalışan 'iç işletim sistemi'.**

Bilgisayar açıldığında ekran gelmeden önce donanımın içinde küçük bir program
uyanır: kendi kendini test eder, kartların ayarlarını yükler, hazır olunca
üst sisteme (K000/K003) haber verir. ELI15 ile: K005, buzdolabının içindeki
termostat yazılımıdır — buzdolabını (K001) anlar, soğutmayı ayarlar, ama
mutfağın (OS) nasıl çalıştığını bilmez.

Ne DEĞİL: kasadaki **fiziksel parçanın kendisi** (→ K001-donanım) · bilgisayarın
**işletim sistemi** (→ K000-isletim-sistemi) · sesin **taşınması/şekillendirilmesi**
(→ K003-audio / K004-dsp).

## §2 Kademe-2 çocuk katalogu (v2 §2.5 — 9 çocuk)

| # | Çocuk | Rol (1 satır) | Kanıt durumu |
|---|---|---|---|
| 1 | boot | Açılışta çalışan ilk adım — kendini tanıtma ve yükleme | ⚠️ adından türetildi |
| 2 | runtime | Çalışma anında sürekli dönen kontrol döngüsü | ⚠️ adından türetildi |
| 3 | device-control | Parçalara komut gönderme (aç/kapat/ayarla) | ⚠️ adından türetildi |
| 4 | audio-control | Ses donanımının ayar ve kip kontrolü | ⚠️ adından türetildi |
| 5 | power-control | Güç durumları — uyku, kapanma, akım kontrolü | ⚠️ adından türetildi |
| 6 | diagnostics | Kendi kendini test etme ve hata raporu | ⚠️ adından türetildi |
| 7 | update | Yazılım güncelleme ve geri alma | ⚠️ adından türetildi |
| 8 | security | Güvenlik kilidi — imza, erişim, koruma | ⚠️ adından türetildi |
| 9 | testing | Firmware testleri ve kabul kriterleri | ⚠️ adından türetildi |

> ⚠️ VERIFICATION REQUIRED: v2 §2.5 çocukları yalnızca ad olarak tanımlar;
> roller adından türetilmiştir (v2 §3 KURAL 3) — uydurma register/sürüm yazılmaz.

## §3 Bağımlılık özeti (tasarım iddiası)

| Yön | Bağımlılık | Etiket |
|---|---|---|
| K005 → K001-donanım | Yazılımın çalıştığı fiziksel parça ve register'lar | [TASARIM] |
| K001-donanım | → (çağıran) K005: donanım ancak firmware ile uyanır | [TASARIM] |
| K003-audio | → (çağıran) K005: ses yolu hazır durumu sorgulanır | [TASARIM] |
| K004-dsp | Donanım içi DSP hedefi varsa firmware'e bağlanır | [TASARIM] ⚠️ |

## §4 Doğrulama durumu

| Kriter | Durum |
|---|---|
| D1 v2 §2.5 birebirliği (9 çocuk) | ✅ 2026-10-12 |
| D2 9 çocuk klasöründe index.md | ✅ 2026-10-12 |
| D3 master + bagimliliklar + dogrulama üçlüsü | ✅ 2026-10-12 |
| D4 açık ⚠️ / UNKNOWN kalmadı | ❌ ASIO lisans etkisi + PVDD çelişkisi + UNKNOWN'lar |
| D5 açık sorular kapandı | ❌ 5 açık |
| D6 en az bir [TASARIM] → [KANIT] geçişi | ❌ 0 |
| D7 status: verified | ❌ D1–D7 tamamlanınca |

**Status: draft** — verified yalnız D1–D7 tamamı ✅ olduğunda yazılır.

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
