---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/surucu-ve-modul-modeli
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel
children:
  - K000-isletim-sistemi/isletim-sistemi-kernel/surucu-ve-modul-modeli/surucu-ve-modul-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-kernel/surucu-ve-modul-modeli/surucu-ve-modul-modeli-surucu
  - K000-isletim-sistemi/isletim-sistemi-kernel/surucu-ve-modul-modeli/surucu-ve-modul-modeli-ornekler
depends_on: []
evidence:
  - https://docs.kernel.org/driver-api/driver-model/index.html
  - https://docs.kernel.org/driver-api/index.html
  - https://docs.kernel.org/filesystems/vfs.html
  - https://learn.microsoft.com/en-us/windows/hardware/drivers/kernel/
  - https://docs.kernel.org/security/index.html
validation:
  - "2026-10-13 · 5 resmi URL HEAD 200 · envanter kesişimi 0 · dosya envanteri disk glob ile sayıldı"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
  - "`electroncis` ↔ kernel sürücü sınırı (Q1) → UNKNOWN"
group: mimari
---

# surucu-ve-modul-modeli — Alt-Dal İndeksi (Kademe-4)

**ELI15:** Sürücü, çekirdeğin donanımı konuşan dilidir; modül ise bu sürücünün
**çalışırken takılıp çıkarılabilen** paketidir — dosya sistemi (VFS) bunları
kullanıcıya "dosya kapısı" olarak sunar, yetki denetimi (capability/ioctl)
kapıyı korur.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-kernel` |
| Kademe | 4 · alt-dal |
| Durum | `draft` (Q2 · Q4 · Q1 açık) |
| Ana işlev | 4 (dosya sağlar — VFS↔sürücü) + 5 (güvenliği uygular — yetki/ioctl) |

## §1 Kanıtlanmış çerçeve

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Sürücü/donanım bağlama modeli (driver-model) | https://docs.kernel.org/driver-api/driver-model/index.html | 2026-10-13 | ✅ HEAD 200 |
| Sürücü geliştirme arayüzü ana kaynağı (driver-api) | https://docs.kernel.org/driver-api/index.html | 2026-10-13 | ✅ HEAD 200 |
| VFS (sanal dosya sistemi) çekirdek arayüzü | https://docs.kernel.org/filesystems/vfs.html | 2026-10-13 | ✅ HEAD 200 |
| Windows kernel-mode sürücü yüzeyi | https://learn.microsoft.com/en-us/windows/hardware/drivers/kernel/ | 2026-10-13 | ✅ HEAD 200 |
| Çekirdek güvenlik alt sistemi (capability · LSM) | https://docs.kernel.org/security/index.html | 2026-10-13 | ✅ HEAD 200 |

## §2 Bu alt-dalın dosya envanteri (disk · 2026-10-13)

| # | Dosya | İçerik yüzeyi (bölüm omurgası) | group | Satır |
|---|---|---|---|---|
| 1 | `index.md` (bu dosya) | alt-dal indeksi · komşu sınır matrisi | mimari | 40-100 |
| 2 | `surucu-ve-modul-modeli-anayasa.md` | §0-§3: ELI15 · yükçekirdek/out-of-tree modül · yaşam döngüsü · sürücü sınıfları | mimari | ≥500 |
| 3 | `surucu-ve-modul-modeli-surucu.md` | §4-§6: kullanıcı-kernel sınırı (syscall/ioctl) · sürücü testi (kunit/QEMU) · referans | mimari | ≥500 |
| 4 | `surucu-ve-modul-modeli-ornekler.md` | §1-§6: örnek kataloğu · senaryolar · repo örnekleri | kod | ≥500 |

> Kural: bu 4 dosyanın yüzeyi bu alt-dalın **kendi beyazlistesidir**; kök
> `isletim-sistemi-kernel/*.md` ve kardeş alt-dallara bu turda **dokunulmadı**.

## §3 Komşu sınır matrisi (bu alt-dal ↔ diğerleri)

| Dışarıda olan | Nereye gider | Neden |
|---|---|---|
| Donanım topolojisi · PCB · DAC/ADC | `K001-donanım` / `electroncis` | donanım sınırı (Q1 — UNKNOWN) |
| OS türü (yükleme modeli sınıflandırması) | `isletim-sistemi-turu` | tür `-turu` |
| Ses sürücüsü (ASIO/ALSA/CoreAudio) | `isletim-sistemi-audio` | ses `-audio` |
| Sürücü güvenlik politikası · imzalama | `isletim-sistemi-security` | güvenlik `-security` |
| Sürücü gözlemi · modül logu | `isletim-sistemi-observability` | gözlem `-observability` |
| Sürücü testi · kunit/QEMU testi | `isletim-sistemi-testing` | test `-testing` |
| Uygulama↔sürücü ioctl yüzeyi | `isletim-sistemi-api` | API `-api` |

> Bu alt-dal **yalnız** modül yükleme + sürücü arayüzü + yetki denetimini işler;
> donanım topolojisi, güvenlik politikası, gözlem ve test **komşu daldadır** —
> burada tekrar edilmez (çakışma = 0).

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| M1 | `core` ↔ `kernel` sınırı (sürücü/VFS hangi dalda)? | ⚠️ VERIFICATION REQUIRED (Q2) |
| M2 | Hedef çekirdek (Linux/Windows/macOS)? | UNKNOWN (Q4) |
| M3 | `electroncis` ↔ kernel sürücü sınırı (Q1)? | UNKNOWN |
| M4 | Çekirdek sürümü / spin-off (Linux fork) bilgisi? | UNKNOWN |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Alt-dal künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `surucu-ve-modul-modeli-anayasa.md` | Anasözleşme ve bağlayıcı mimari kurallar | Kademe-4 |
| mimari | `surucu-ve-modul-modeli-surucu.md` | Kullanıcı-kernel sınırı ve test omurgası | Kademe-4 |
| kod | `surucu-ve-modul-modeli-ornekler.md` | Örnek senaryolar ve repo örnekleri | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
