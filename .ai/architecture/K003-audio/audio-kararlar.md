---
node_id: K003-audio/kararlar
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on:
  - K005-firmware
  - K006-codebase
  - K007-sandbox
  - K008-superapp
  - K009-api
  - K010-network
  - K011-ui-kit
evidence:
  - ".ai/.decisions/index.md §3 (accepted 80) — okundu 2026-10-18: ADR-017/019/025/037/038/097"
  - ".ai/architecture/K003-audio/audio-master.md §29.2 (ASIO lisans kararı) — okundu 2026-10-18"
  - ".ai/architecture/K003-audio/audio-master.md §3.4 (Q2/Q3/Q5/Q7 kararları) — okundu 2026-10-18"
validation: "Tüm ADR numaraları .ai/.decisions/index.md ile çapraz doğrulandı — 2026-10-18"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — audio-kararlar.md mevcut (beyazliste dışı, dokunulmadı):
  yeni eklenen KA- önekli yerel kararlar onunla birleştirilecek (sahip: K003-audio).
group: mimari
---

# K003-audio — Kararlar (ses motoru)

## §1 — ELI15: Bu nedir?

Bir karar = "şu soruya şu cevabı verdik, çünkü şu sebep var" kaydı. Bu dosya
K003 ses motorunun tüm kararlarını toplar. İki tür var: **ADR** = vault'un
genel kayıtları (`.ai/.decisions/index.md` numaralandırır, uydurulmaz);
**KA** = K003'e özgü yerel kararlar (burada KA- önekiyle başlar, ADR değildir).

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | K003-audio köküne ait ADR atıfları + KA- yerel kararlar |
| Kapsam dışı | Yeni ADR numarası tahsisi (sadece `.ai/.decisions/index.md`) · K004-dsp kararları (KD- öneki) |

## §3 — Doğrulanmış ADR seti (K003'e bağlananlar)

| ADR | Başlık | K003'e etkisi | Kanıt |
|---|---|---|---|
| ADR-017 | DSP Hardware Mode Decision — **Q5 kapattı** | Backend hardware mode kararı | `.ai/.decisions/index.md` §3 |
| ADR-019 | Audio Engine Lock-Free Ring Buffer | Ring buffer = mimari zorunluluk | index.md §3 |
| ADR-025 | WASAPI Exclusive Mode Audio Architecture | Windows exclusive backend | index.md §3 |
| ADR-037 | macOS CoreAudio AudioUnit Integration | macOS backend + AU | index.md §3 |
| ADR-038 | macOS AudioUnit HAL Plugin Architecture | AU → HAL köprüsü | index.md §3 |
| ADR-097 | (draft) | ⚠️ başlık draft-only — üretim öncesi `index.md`'den teyit | index.md §3 (draft) |

> **Kural:** ADR numarası bu dosyadan değiştirilemez; yeniden numaralandırma yok.
> ADR-097 `draft` durumunda — kabul edilmeden karar olarak sayılmaz.

## §4 — KA- yerel kararlar (K003'e özgü, ADR değil)

| Kod | Karar | Durum | Kanıt |
|---|---|---|---|
| KA-A1 | Backend matrisi: Windows = ASIO (birincil) + WASAPI (fallback); macOS = CoreAudio; Linux = ALSA + JACK/PipeWire | Aktif | `audio-master.md` §6 |
| KA-A2 | ASIO lisansı: GPLv3 (Steinberg `asiosdk-open`, 2025-10-15) — 2026-10-14 §29.2 kararıyla lisans riski **kapandı**; sürüm teyidi üretim öncesi | Aktif ⚠️ | `audio-master.md` §29.2 |
| KA-A3 | Q2: 44.1/48/88.2/96/176.4/192 kHz + 16/24/32f bit derinliği; 384 kHz yalnızca donanım bildirirse | Aktif | `audio-master.md` §3.4 Q2 |
| KA-A4 | Q3: Örnekleme dönüşümü yalnızca zincirde zorunluysa (ör. 44.1→48 resample); bit-exact yollar dönüşümsüz | Aktif | `audio-master.md` §3.4 Q3 |
| KA-A5 | Q5: Exclusive mode birincil, shared fallback; shared'te bit-exact garanti edilmez | Aktif | `audio-master.md` §3.4 Q5 · ADR-025 |
| KA-A6 | Q7: Callback bütçesi 5-20 ms; allocate/lock/log yasak; overrun → xrun sayacı (K016'ya event) | Aktif | `audio-master.md` §3.4 Q7 · ADR-019 |
| KA-A7 | MIDI 2.0: 32-bit sayısal değerler, MIDI-CI zorunlu; MIDI 1.0 backward-compat köprü | Aktif | `audio-master.md` §2 (K00302-midi) |
| KA-A8 | Plugin host köprüleri (VST3/AAX/AU/SoundWire): ayrı process sandbox (K007-sandbox); plugin crash'i ses yolunu öldürmez | Aktif | `audio-master.md` §2 · `audio-bagimliliklar.md` §1 |

## §5 — Karar-öncelik sırası (vault-içi otorite)

```
.ai/CLAUDE.md §2  >  ADR (index.md)  >  KA- yerel karar (bu dosya)  >  master §3.4
```

Çelişki halinde üstteki kazanır. ADR ile KA- çakışırsa ADR kazanır ve KA-
yeniden yazılır (numara değil içerik).

## §6 — Kanıt ve doğrulama durumu

| İddia | Kanıt | Durum |
|---|---|---|
| 5 accepted + 1 draft ADR K003'e bağlı | `.ai/.decisions/index.md` §3 | ✓ Doğrulandı 2026-10-18 |
| ASIO lisans kararı kapandı | `audio-master.md` §29.2 | ✓ Okundu 2026-10-18 |
| Q2/Q3/Q5/Q7 kararları | `audio-master.md` §3.4 | ✓ Okundu 2026-10-18 |
| ADR-097 başlığı | `index.md` yalnız draft durumunu veriyor | ⚠️ VERIFICATION REQUIRED — üretim öncesi teyit |
| audio-kararlar.md senkronu | beyazliste dışı, dokunulmadı | ⚠️ Manuel birleştirme gerekebilir |

## §7 — Açık sorular

| # | Soru | Sahip |
|---|---|---|
| 1 | ADR-097 başlığı ne? Kabul tarihi var mı? | Vault Steward |
| 2 | KA-A2 ASIO sürümü (Steinberg repo hangi tag?) üretimde sabitlendi mi? | K003-audio |
| 3 | KA-A1 backend matrisi: Linux'ta JACK mi PipeWire mi birincil? | K003-audio |
| 4 | KA-A8 sandbox (K007) process sınırı: plugin başına mı, oturum başına mı? | K007-sandbox |

## §8 — Karar kayıt şablonu (sonraki KA- kayıtları için)

Yeni yerel karar eklerken bu şablon zorunlu (uydurma numara yasak):

```yaml
kod: KA-A9            # KA- önek + K003 sıra; ADR numarası ASLA tahsis edilmez
karar: "tek cümlelik karar"
durum: aktif | tartışılıyor | kapandı
tarih: YYYY-MM-DD
kanit: "<dosya> §<bölüm>"   # disk kanıtı yoksa: UNKNOWN
bagli_adr: ADR-0xx | null   # varsa index.md'den doğrulanmış numara
```

### Karar yaşam döngüsü

| Durum | Anlamı | Geçiş kuralı |
|---|---|---|
| tartışılıyor | Henüz uygulanmadı | kanıt + onay → aktif |
| aktif | Uygulanır, bağlayıcı | yeni kanıt → revize veya kapandı |
| kapandı | Yerine yenisi geldi | neden + successor kodu yazılır |

### Bu dosyadaki karar dağılımı

| Tür | Adet | Liste |
|---|---|---|
| Doğrulanmış ADR (accepted) | 5 | ADR-017 · 019 · 025 · 037 · 038 |
| ADR (draft — sayılmaz) | 1 | ADR-097 ⚠️ |
| KA- yerel (aktif) | 7 | KA-A1..KA-A7 |
| KA- yerel (aktif ⚠️) | 1 | KA-A2 (sürüm teyidi) |
| KA- yerel (köprü) | 1 | KA-A8 (K007 bağımlı) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
