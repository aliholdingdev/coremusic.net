---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
children: []
depends_on: []
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis
  - https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams
validation: "2026-10-12 · 2 resmi URL HTTP 200 · dosya envanteri disk glob ile sayıldı · diğer wasapi-*.md dosyalarına dokunulmadı"
open_questions:
  - "wasapi-asio-karsilastirma.md neden deep-dal içinde, ayrı child mı olmalı? → ⚠️ VERIFICATION REQUIRED"
  - "WASAPI'nin CoreMusic ile gerçek teması YOK (repo'da Windows ses kodu yok) → UNKNOWN"
group: mimari
---

# wasapi — Deep-Dal İndeksi (Kademe-4)

**ELI15:** WASAPI, Windows'un **ses kartına erişim kapısıdır** — uygulama sesi buradan
ister, Windows ses aygıtına dağıtır.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio` |
| Kademe | 4 · deep-dal |
| Durum | `draft` |
| Platform | Windows (Windows Core Audio ailesi) |

## §1 Kanıtlanmış çerçeve

| # | İddia | Kaynak | Tarih | Durum |
|---|---|---|---|---|
| 1 | Windows Core Audio API ailesi (WASAPI dahil) resmi tanım | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis | 2026-10-12 | ✅ HTTP 200 |
| 2 | Exclusive mode streams — tek uygulamanın cihazı dışlama modunda alması | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ HTTP 200 |

## §2 Bu deep-dalın dosya envanteri (disk, 2026-10-12)

| # | Dosya | İçerik yüzeyi | Bu turda |
|---|---|---|---|
| 1 | `wasapi-anayasa.md` | Windows ses anayasası | **DOKUNULMADI** (başka agent) |
| 2 | `wasapi-arayuzler.md` | arayüz listesi | **DOKUNULMADI** |
| 3 | `wasapi-kernel.md` | kernel ilişkisi | **DOKUNULMADI** |
| 4 | `wasapi-surucu-ve-yigin.md` | sürücü + yığın | **DOKUNULMADI** |
| 5 | `wasapi-asio-karsilastirma.md` | ASIO karşılaştırması | **DOKUNULMADI** |
| 6 | `index.md` (bu dosya) | deep-dal indeksi | **oluşturuldu** |

> Kural: `wasapi-anayasa/arayuzler/kernel/surucu-ve-yigin/asio-karsilastirma.md`
> **başka bir agent'ın yüzeyidir** — bu revizyonda silinmedi, düzenlenmedi, taşınmadı.

## §3 Bu deep-dalın üst sınırları

| Dışında olan | Nereye gider |
|---|---|
| Diğer platform ses API'leri (ALSA, CoreAudio, AAudio) | `../index.md` §1 |
| DSP algoritmaları | K004-dsp |
| Ses motoru C++ kodu | K003-audio |
| ASIO'nun genel karşılaştırması | bu dosya §2/5 — konum kararı açık (Q-A3) |

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| W1 | `wasapi-asio-karsilastirma.md` doğru konumda mı? | ⚠️ VERIFICATION REQUIRED |
| W2 | CoreMusic'in WASAPI ile gerçek teması ne zaman? (repo'da Windows ses kodu yok) | UNKNOWN |
| W3 | Hedef platform (Q4) — Windows ses öncelikli mi? | UNKNOWN |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `wasapi-anayasa.md` | Anasözleşme ve bağlayıcı mimari kurallar | Kademe-3 |
| mimari | `wasapi-arayuz-hiyerarsisi.md` | Arayüz hiyerarşisi ve kalıtım ilişkileri | Kademe-3 |
| mimari | `wasapi-arayuzler.md` | API arayüzleri ve çağrı sözleşmeleri | Kademe-3 |
| mimari | `wasapi-asio-karsilastirma.md` | Teknoloji karşılaştırma matrisi ve seçim ölçütü | Kademe-3 |
| mimari | `wasapi-latency-ve-buffer.md` | Gecikme ölçümü ve buffer boyutlandırma | Kademe-3 |
| mimari | `wasapi-session-management.md` | Oturum yönetimi ve durum senkronu | Kademe-3 |
| mimari | `wasapi-shared-ve-exclusive-mode.md` | Paylaşımlı ve münhasır çalışma modları | Kademe-3 |
| mimari | `wasapi-surucu-modeli.md` | Sürücü katmanı ve yüklenme modeli | Kademe-3 |
| mimari | `wasapi-surucu-ve-yigin.md` | Sürücü katmanı ve yığın takibi | Kademe-3 |
| kod | `wasapi-kod-ornekleri.md` | Gerçek repo kodu, dosya ve satır | Kademe-3 |
| kanit | `wasapi-web-arastirma.md` | Sistemli resmi kaynak tarama kaydı | Kademe-3 |
| kanit | `wasapi-winsdk-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
