---
title: "K037.7 — WASAPI Hata Kodları: AUDCLNT_E Sözlüğü, Fallback, Hata Yayılımı"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# WASAPI Hata Kodları

> **Kapsam:** Backup'ta kanıtlı WASAPI `HRESULT` hata kodları, anlamları,
> fallback zinciri ve hata yayılımı. Uydurma hex YASAKTIR.
>
> **Kanıt kuralı:** §1'deki 4 kod birincil backup'tır (satır no ile).
> Backup'ta geçmeyen kod **hex ile yazılmaz** → yalnız kategori/semptom
> olarak `⚠️ VERIFICATION REQUIRED` ile anılır. **Repo'da WASAPI implementasyon
> kodu YOKTUR** (`index.md` §1.3).

---

## §1 Backup'taki 4 Hata Kodu (tek kaynak)

Birincil backup kaynağı hata kodlarını tek tabloda verir:

| Hex | Symbol (backup) | Anlam | Backup Çözüm |
|---|---|---|---|
| `0x8889000A` | `AUDCLNT_E_DEVICE_IN_USE` | Cihaz başka süreççe kullanımda (exclusive lock çatışması) | Shared mode'a geç |
| `0x88890008` | `AUDCLNT_E_UNSUPPORTED_FORMAT` | Desteklenmeyen format | Formatı değiştir |
| `0x8889000E` | `AUDCLNT_E_EXCLUSIVE_MODE_NOT_ALLOWED` | Exclusive moda izin verilmiyor | Yetki kontrolü |
| `0x88890018` | `AUDCLNT_E_BUFFER_SIZE_ERROR` | Buffer boyutu hatalı/uymuyor | Buffer boyutunu ayarla |

**Backup kanıtı:** `k2-surucu/wasapi-exclusive.md L124–L130`.
**Tekrar doğrulama:** Aynı 4 kod `k2-surucu/README.md` içinde geçmez;
`windows-api.md`/`windows-core.md` içinde de hex kod **geçmez** → tek kaynak.

> ⚠️ **VERIFICATION REQUIRED:** Bu 4 kod **backup içi tek kanıttır**; official
> Microsoft hata kodu listesiyle çapraz doğrulama yapılmamıştır. Kullanım
> öncesi MSDN `AUDCLNT_E_*` tablosuyla eşleştirilmelidir. Backup dışı hiçbir
> hex bu dosyaya yazılmamıştır.

---

## §2 Kod Bazlı Eylem Matrisi

| Kod | Tetikleyen senaryo | Kategori | Aksiyon (backup çözümü) |
|---|---|---|---|
| `0x88890008` | Format `Initialize`'da reddedildi | **Format** | Formatı değiştir / fallback zinciri → [[wasapi-format-negotiation]] §5 |
| `0x8889000A` | Cihaz başka süreççe kullanımda (lock çatışması) | **Lock / Erişim** | Shared mode'a geç → [[wasapi-exclusive-mode]] §7 |
| `0x8889000E` | Exclusive moda izin verilmiyor | **Yetki / Politika** | Yetki kontrolü → [[wasapi-exclusive-mode]] §7 |
| `0x88890018` | Buffer boyutu cihazla uyuşmuyor | **Buffer** | Buffer boyutunu ayarla → [[wasapi-buffer-latency]] §6 |

**Backup kanıtı (fallback):** `wasapi-exclusive.md L124–L130`
("fallback zinciri" ifadesi + kod tablosu).

---

## §3 Fallback Zinciri (backup'a sadık sürüm)

Backup L124–L130: "Cihaz/fallback zinciri … `0x8889000A` · `0x88890008` …
fallback zinciri" (not: backup satır bütünlüğü L124–L130 aralığındadır).

```
IAudioClient::Initialize(hedef format)
        │
        ├─ 0x88890008 (UNSUPPORTED_FORMAT) — "Formatı değiştir"
        │       → format fallback zinciri
        │         32-bit float → 24-bit → 16-bit   ⚠️ sıra çıkarımı
        │         (backup format tablosu L99–L111 · derinlik sırası README L119)
        │
        ├─ 0x8889000A (DEVICE_IN_USE) — "Shared mode'a geç"
        │       → başka süreç exclusive lock'u tutuyor
        │         fallback: Shared mod ([[wasapi-device-hotplug]] §4)
        │
        ├─ 0x8889000E (EXCLUSIVE_MODE_NOT_ALLOWED) — "Yetki kontrolü"
        │         → yetki/privilej kontrolü yapıp tekrar dene;
        │           başarısızsa Shared (index §3.2 kural 3)
        │
        └─ 0x88890018 (BUFFER_SIZE_ERROR) — "Buffer boyutunu ayarla"
                → period/buffer süresini cihaz yeteneği içine al
                  ([[wasapi-buffer-latency]] §6)
```

> ⚠️ **VERIFICATION REQUIRED:** Fallback **sırası** backup'ta açık yazılmamış;
> backup yalnız kodların ve "fallback zinciri" ifadesinin varlığını belgeler.
> Sıra, README format derinlik tablosundan (`L119`: Shared 16/24-bit,
> Exclusive 16/24/32-bit) türetilmiştir.

**Backup kanıtı:** `wasapi-exclusive.md L124–L130` · `README.md L116–L122` ·
`wasapi-exclusive.md L99–L111`.

---

## §4 Kod Dışı Hata Sınıfları (kanıtsız alan)

Backup'ta hex'i geçmeyen, yalnızca kavram olarak geçen hata/uyarı alanları:

| Sınıfname | Durum | Kaynak |
|---|---|---|
| Cihaz kaybı/geçersizleşme (cihazın sökülmesi) — ayrı hata kodu | `⚠️ VERIFICATION REQUIRED` (hex yok) | Backup'ta yalnız lock çatışması kodu geçer: `0x8889000A` = `DEVICE_IN_USE` (L126); cihaz-kaldırma kodu **geçmez** |
| `Initialize` hataları geneli (E_INVALIDARG vb.) | `⚠️ VERIFICATION REQUIRED` | Backup yalnız 4 kod verir (L124–L130) |
| Event handle hatası | `⚠️ VERIFICATION REQUIRED` | Backup `SetEventHandle` kullanır, hata kodu vermez (`windows-core.md L225`) |

> **Kural:** Bu alana **asla hex uydurulmaz.** Official dokümantasyondan
> doğrulanana kadar semptom adıyla anılır.

---

## §5 Hata Yayılımı (Katman Zinciri)

Backup, hata yayılımının katmanlarını akış diyagramıyla verir:

```
Application → Audio Client → Audio Session → Audio Engine → Hardware
```

**Backup kanıtı:** `k2-surucu/README.md L124–L132`.

| Katman | Hata kaynağı | Yayılım |
|---|---|---|
| Application | Yetki reddi (`0x8889000E` EXCLUSIVE_MODE_NOT_ALLOWED) | Exclusive açılışı yapılamaz |
| Audio Client | Format (`0x88890008`) | Stream hiç açılmaz |
| Audio Session | Oturum olayı (backup arayüz listesi L88–L97) | Metering/volume etkilenir |
| Audio Engine | Shared modda engine kaynaklı dönüşüm | Bit-perfect kaybı (`README L116–L122`) |
| Hardware | Lock çatışması (`0x8889000A` DEVICE_IN_USE) · buffer uyuşmazlığı (`0x88890018`) | Exclusive yol kapalı → Shared fallback |

**Çıkarım notu:** Tablo, akış diyagramı (README L124–L132) + 4 kod
(wasapi-exclusive L124–L130) birleştirilerek üretilmiştir — *çıkarım*,
hex uydurma yoktur.

---

## §6 Hata Sonrası Kurtarma Prosedürü

1. **Teşhis:** Hangi katman? (`GetCurrentPadding` döngüsü — `windows-core.md L228–L233`)
2. **Sınıflandır:** 4 koddan biri mi? (§1) → evet: §2 eylemi; hayır: `⚠️ VERIFICATION REQUIRED`
3. **Lock çatışması (`0x8889000A`) ise:** fallback önceliği — ASIO #1 →
   WASAPI Exclusive #2 → Shared #6 (`k2-surucu/CLAUDE.md L27–L34`) → [[wasapi-device-hotplug]] §4;
   envanter WMI `Win32_SoundDevice` (`windows-core.md L187–L203`)
4. **Format hatası (`0x88890008`) ise:** fallback zinciri → [[wasapi-format-negotiation]]
5. **Yetki hatası (`0x8889000E`) ise:** yetki/privilej kontrolü → [[wasapi-exclusive-mode]] §7
6. **Buffer hatası (`0x88890018`) ise:** period/buffer boyutu → [[wasapi-buffer-latency]] §6

> ⚠️ **VERIFICATION REQUIRED:** Repo'da kod olmadığından bu prosedür
> **iş akışı taslağıdır**; implementasyon sonrası test edilmelidir.

---

## §7 Çapraz Bağlantılar

| Konu | Dosya |
|---|---|
| Format fallback | [[wasapi-format-negotiation]] |
| Lock çatışması / yetki reddi | [[wasapi-exclusive-mode]] |
| Buffer boyutu hatası | [[wasapi-buffer-latency]] |
| Cihaz bulma / fallback zinciri | [[wasapi-device-hotplug]] |
| Oturum katmanı | [[wasapi-audio-session]] |
| Mod karşılaştırması (hub) | [[wasapi-exclusive-shared]] |
| Üst hub | [[index]] |

> **Düzeltme notu (2026-10-06):** İlk üretimde `0x8889000A/000E/0018`
> sembolleri backup'a aykırı yazılmıştı (`DEVICE_INVALIDATED`/`OUT_OF_ORDER`/
> `BUFFER_OPERATION_PENDING`); backup kazandı → semboller `k2-surucu/
> wasapi-exclusive.md L126–L129` ile birebir hizalandı (bkz. index §20 çelişki kaydı).

---

## §8 Kanıt Kaynakları (backup satır haritası)

| # | İddia | Backup dosyası | Satır |
|---|---|---|---|
| 1 | 4 hata kodu + fallback zinciri | `k2-surucu/wasapi-exclusive.md` | L124–L130 |
| 2 | Format tablosu (fallback hedefleri) | `k2-surucu/wasapi-exclusive.md` | L99–L111 |
| 3 | Mod tablosu (bit-perfect · derinlik) | `k2-surucu/README.md` | L112–L122 |
| 4 | Katman akış diyagramı | `k2-surucu/README.md` | L124–L132 |
| 5 | Event loop + padding | `k0-isletim-sistemi/windows-core.md` | L223–L234 |
| 6 | WMI `Win32_SoundDevice` | `k0-isletim-sistemi/windows-core.md` | L182–L203 |
| 7 | IAudioClient arayüz kümesi | `k2-surucu/wasapi-exclusive.md` | L37–L49 |

---

## §9 Değişiklik Günlüğü

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 4.0.0 | 2026-10-06 | İlk üretim — multi-md yapılandırması (hata kodları) |
| 4.0.0 | 2026-10-06 | Çelişki düzeltmesi — 3 sembol backup'a hizalandı (`DEVICE_IN_USE`/`EXCLUSIVE_MODE_NOT_ALLOWED`/`BUFFER_SIZE_ERROR`, `wasapi-exclusive.md L126–L129`; backup kazandı) |
