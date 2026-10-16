---
title: "K000 — OS Donanım Tespiti (Alt-Dal Künyesi)"
type: architecture-node
node_id: K000-isletim-sistemi/os-donanim-tespiti
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children:
  - os-donanim-tespiti-kapsam
  - os-donanim-tespiti-dogrulama
  - os-donanim-tespiti-ornekler
  - os-donanim-tespiti-kod-seti
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
evidence:
  - "shared/src/Device/DeviceDetector.php (316 satır / 272 dolu — class satır 19, detect() satır 57 — IMPLEMENTED, uygulama-seviyesi tespit)"
  - "shared/src/Device/DeviceManager.php (662 satır / 588 dolu, 7 cihaz sabiti satır 29–40 — IMPLEMENTED)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/setupapi/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://docs.kerneSl.org/driver-api/driver-model/index.html (HTTP 200, 2026-10-13, HEAD)"
  - "https://devicetree.org/ (HTTP 200, 2026-10-13, HEAD)"
validation: "draft — 2026-10-13 yazıldı · 3 kardeş içerik dosyası ≥500 satır hedefi yazım sonrası ölçülür · fiziksel ölçüm YOK · K000 kök index/§GRUPLAR satırı eklenmedi (orkestratöre devredildi) | 2026-10-14 — kod-seti eklendi: 4. kardeş içerik dosyası (os-donanim-tespiti-kod-seti.md · 1069 satır · 22 kod bloğu · 39 resmi URL HTTP 200 · spot-check: orkestratör)"
open_questions:
  - "Q-DT1: Hedef platform seti K000 Q4 ile aynı — Win32 mi, POSIX mı, ikisi mi? → ⚠️ VERIFICATION REQUIRED"
  - "Q-DT2: Repo'da OS-seviyesi donanım tespiti kodu YOK (SetupDi/udev/devnode grep 0) — uygulama-seviyesi DeviceDetector bunun yerine mi geçiyor, örtüşüyor mu? → ⚠️ VERIFICATION REQUIRED"
  - "Q-DT3: PnP hotplug senaryosu CoreMusic'te gerçek mi (USB ses kartı tak-çıkar) — senaryo sahibi K003 audio mü, K000 mi? → ⚠️ VERIFICATION REQUIRED"
date: "2026-10-13"
updated: "2026-10-14"
group: mimari
---

# os-donanim-tespiti — OS Donanım Tespiti (Kademe-2 Alt-Dal Künyesi)

**ELI15:** İşletim sistemi açıldığında bilgisayarda **ne takılı olduğunu** nasıl
öğrenir? Bu dal, donanımı **keşfetme** (enumeration), **tak-çıkar olaylarını
yakalama** (hotplug/PnP), **sürücü ile eşleştirme** (driver binding) ve **cihaz
kimliğini okuma** (VID/PID/compatible) süreçlerini belgeler. Amaç: OS'nin
donanımı **keşif anındaki** sınırını sabitlemek — keşif sırası, ekleme-çıkarma
olayı, sürücü bağlama, ID eşleme, başarısız-keşif hata modeli, OS↔donanım API
sınırı (anayasa: "Çekirdek sürücü kodu ring-0 → K000", `.ai/CLAUDE.md` §2).
"Cihaza nasıl erişilir" (`cihaz-erisimi`) ayrı konudur — bu dal **yaşam
döngüsü**nün sahibidir. depends_on: K000 kök (children'a **eklenmedi** —
orkestratör) · K001-donanım (çapraz sahiplik) · SetupAPI/PnP · driver-model/
sysfs/devicetree (HEAD 200 §6).

## §1 Kademe ve rol

| Katman | Kademe | Bu dalın rolü |
|---|---|---|
| enumeration / devnode | 3 | OS keşif sırası ve cihaz ağacı |
| hotplug / PnP olayları | 3 | ekleme-çıkarma olay modeli |
| sürücü bağlama | 3 | INF/alias/modalias ile modül eşleme |
| cihaz-ID eşleme | 3 | VID/PID/compatible eşleşmesi |
| OS↔donanım API sınırı | 3 | K001 `donanım-api` ile sözleşmenin **OS tarafı** |

## §2 Alt-dal envanteri (4)

| # | Dosya | `group` | Odak |
|---|---|---|---|
| 1 | `os-donanim-tespiti-kapsam.md` | mimari | Kapsam, sınır, çelişki kaydı, sınır tablosu |
| 2 | `os-donanim-tespiti-dogrulama.md` | kanit | İddia → kanıt matrisi, URL HEAD, kapılar |
| 3 | `os-donanim-tespiti-ornekler.md` | kod | Repo kodu (4 PHP dosyası) + gözlem şablonları |
| 4 | `os-donanim-tespiti-kod-seti.md` | kod | Cross-platform CPU/bellek/ses/arayüz enumerasyon kod seti — 1069 satır · 22 kod bloğu · 39 resmi URL (2026-10-14) |

## §3 Komşu sınırı (özet — tam tablo `-kapsam.md` §10.2/§3)

| Dışarda olan | Nereye gider | Dayanak |
|---|---|---|
| Cihaz erişim API'si (fd/ioctl/mmap) | `isletim-sistemi-api/cihaz-erisimi` | **iki dal da yaşar**: keşif burada, erişim orada |
| Ses yığını / WASAPI sürücü modeli | `isletim-sistemi-audio/wasapi/` (13 md) | glob 2026-10-13 |
| Cihaz tür sınıflandırma | K001 `donanaım-type/` | bu dal ≠ sınıflandırma |
| Cihaz envanteri (ne var) | K001 `donanım-devices/` | bu dal ≠ envanter |
| Firmware yükleme | K005-firmware | firmware ≠ OS keşfi |

## §4 Web kaynakları (HEAD 2026-10-13)

| # | URL | HTTP |
|---|---|---|
| 1 | https://learn.microsoft.com/en-us/windows/win32/api/setupapi/ | ✅ 200 |
| 2 | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | ✅ 200 |
| 3 | https://docs.kernel.org/driver-api/driver-model/index.html | ✅ 200 |
| 4 | https://docs.kernel.org/driver-api/infrastructure.html | ✅ 200 |
| 5 | https://docs.kernel.org/admin-guide/sysfs-rules.html | ✅ 200 |
| 6 | https://devicetree.org/ | ✅ 200 |
| 7 | https://uefi.org/ | ❌ 403 → kullanılmadı |

## §5 Açık sorular ve çelişki notu

Açık sorular: Q-DT1–Q-DT3 FM `open_questions` alanında (tekrarlanmadı) —
tamamı ⚠️ VERIFICATION REQUIRED. **Çelişki (özet — tam kayıt `-kapsam.md`
§10.1):** Bu klasör v2 mini-ağaçta K000 kademe-2 listesinde **YOKTU**; klasör
açımı Vault Steward 2026-10-13 onayı ile yapıldı; **K000 kök `index.md`
children/§GRUPLAR metin revizyonu hâlâ APPROVAL REQUIRED** ve orkestratöre
devredilmiştir. Durum: **PROPOSED — APPROVAL REQUIRED**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
