---
title: "K000 — Kanıt Kaynakları"
description: "K000-isletim-sistemi katmanının 3'lü kanıt zinciri, denge kuralı, açık maddeler/⚠️ defteri ve R14 araştırma kapısı. index.md'nin deep dosyasıdır."
owner: win-sw
tier: 3
risk: medium
status: draft
updated: 2026-10-08
type: wiki
category: kspace-katman
version: 1.0.0
authority: "SSOT: .ai/architecture/K000-isletim-sistemi/index.md"
domain: K000-isletim-sistemi
depends-on:
  - "index.md"
  - "00-kspace-anayasa.md"
ssot: false
---

# K000 — Kanıt Kaynakları (deep)

> **Deep dosya — SSOT DEĞİL.** Bu dosya K000 katman kanıtlarının envanteridir;
> katmanın kendisine dair tek otorite `index.md`'dir (bkz. R9).
> Üretilen kanıt bu dosyaya işlenir, kanıtsız madde burada `⚠️` ile durur.

---

## 1. Kapsam

Bu dosya şu dört soruyu yanıtlar:

| # | Soru | Yanıtın başlığı |
|---|---|---|
| 1 | Bir K000 iddiası nasıl kanıtlanır? | §2 — 3'lü kanıt zinciri |
| 2 | Kaynaklar nasıl ağırlıklandırılır? | §3 — denge kuralı |
| 3 | Şu an neyin kanıtı YOK? | §4 — ⚠️ defteri |
| 4 | Eksik kanıt nasıl tamamlanır? | §5 — R14 araştırma kapısı |

Referans otorite: `@.ai/CLAUDE.md` §8 ZERO-HALLUCINATION · `@.ai/architecture/rules.md` R4/R14.

---

## 2. 3'lü kanıt zinciri (index.md §7)

Her K000 iddiası üç ayaklıdır; **ayaklardan biri eksikse iddia kanıtsızdır.**

### 2.1 Ayak 1 — Kaynak (source)

İddianın dayandığı somut nesne:

| Kaynak türü | Örnek | Kabul edilir mi? |
|---|---|---|
| Vault dosyası | `00-kspace-anayasa.md` L45 | ✅ |
| Repo dosyası (kanıtlanmış commit) | `Dockerfile` @ commit `abc123` | ✅ |
| Harici doküman | Microsoft WASAPI dokümanı (URL + erişim tarihi) | ✅ (URL zorunlu) |
| Model hafızası | "ASIO düşük gecikmelidir" (kaynaksız) | ❌ |
| Web (doğrulanmamış) | forum gönderisi | ❌ (alt: vault) |

Kural: **kaynak yoksa madde yazılmaz.** `@.ai/CLAUDE.md` §8.1.

### 2.2 Ayak 2 — Alıntı / kanıt (evidence)

Kaynağın o iddiayı destekleyen **tam somut kısmı**:

- Dosya ise: gerçek dosya yolu + satır no (örn. `index.md:142`).
- Commit ise: kısa hash + dosya yolu.
- URL ise: bağlantı + tırnak içinde alıntı + erişim tarihi.
- Kod ise: sınıf/metot adı tam yazılır; "bir yerde böyle bir şey vardı" YASAK.

Alıntısız iddia = uydurma iddia sayılır (`@.ai/CLAUDE.md` §8.3).

### 2.3 Ayak 3 — Durum etiketi (status tag)

İddianın zaman/zaruriyet durumu:

| Etiket | Anlamı | K000'de beklenen kullanım |
|---|---|---|
| `[CURRENT]` | Diskte/MVP'de şu an var | Kanıt dosyası varsa |
| `[TARGET]` | Karar verilmiş hedef | Anayasa L45 hedefi |
| `[PROPOSED]` | Öneri, onay bekliyor | Dengeleme ayarları |
| `[PLANNED]` | Planlı, henüz başlamadı | Repo kanıtı olmayan her şey |
| `[DESIGN]` | Tasarım aşaması | Port/adapter sözleşmeleri |
| `⚠️ VERIFICATION REQUIRED` | Doğrulanamayan iddia | §4 defterindeki her madde |

Çakışma kuralı: etiketle kaynak çelişirse **kaynak kazanır** (`rules.md` R14 — "Kaynak" 4. madde).

### 2.4 Tam örnek (şablon)

```markdown
K000, WASAPI Exclusive modunda çalışır. [TARGET]
- Kaynak: .ai/architecture/00-kspace-anayasa.md (L45)
- Kanıt: "Platform: Windows 10+ (Exclusive/CoreMusic-Payload)"
- Dayanak: ADR-096 · tier: 3
- Repo karşılığı: YOK (kanıtlanmış commit bulunamadı) ⚠️ VERIFICATION REQUIRED
```

### 2.5 Kanıt zinciri kontrol listesi (yayın öncesi)

Bir K000 maddesi `[CURRENT]` etiketlenecekse hepsi evet olmalı:

| # | Soru | Hayır ise |
|---|---|---|
| 1 | Kaynak gerçek dosya yolu/commit/URL mi? | madde yazılmaz |
| 2 | Alıntı kaynaktan birebir mi (uydurma yok)? | alıntı düzeltilir |
| 3 | Durum etiketi var mı? | etiketsiz iddia = ihlal |
| 4 | Etiket `[CURRENT]` ise repo kanıtı var mı? | `[PLANNED]`'e çekilir |
| 5 | Daha ağır kaynakla çelişiyor mu? | çelişki → §3.1 kuralı |
| 6 | Kardeş K-ID'ye varsayımsal atıf var mı? | atıf silinir (BÖLÜM 4) |
| 7 | İddia kod bloğu içinde mi? | kod içi link/metin link-check'ten muaftır; madde sayımına girmez |

---

## 3. Denge kuralı (index.md §7.1)

### 3.1 Ağırlık sırası

```text
repo kanıtı (commit) > vault (ADR/anayasa) > harici doküman > web > model hafızası
```

Ağırlık, iddia lehine **değil**, doğrulama gücüne göre çalışır:

1. İki kaynak çelişirse **daha ağır olan** kazanır.
2. Eşit ağırlıkta (örn. iki vault dosyası) → `@.ai/CLAUDE.md` §2.1 otorite sırası uygulanır
   (anayasa > ADR-096 > rules.md > index.md).
3. Çözülemeyen çelişki → `🔴 VERIFICATION REQUIRED` + üst karara sevk.

### 3.2 Ağırlık × durum matrisi (K000 uygulaması)

| İddia türü | En ağır kaynak | Şu anki durum |
|---|---|---|
| Platform/OS hedefi | anayasa L45 | `[TARGET]` |
| Servis envanteri | repo (Windows servisleri) | `⚠️` — repo kanıtı yok |
| Port/adapter sözleşmesi | bu katman `index.md` §6.6 | `[DESIGN]` |
| Web ayağı | EK B / `.ai/api/` | `⚠️` — doğrudan K000 kaynağı yok |
| Ölçüm/latency | harici ölçüm raporu + tekrar | `⚠️` — ölçüm YOK |

### 3.3 Denge ihlali belirtileri

Bir iddiada şu varsa denge bozuktur:

- Kaynak yalnız "hafıza/vakıa" ise,
- Alıntı yoksa,
- Durum etiketi yoksa,
- Etiket `[CURRENT]` ama repo kanıtı yoksa (en sık görülen hata),
- Kaynak `⚠️` ile işaretli ama madde kesin dille yazılmışsa.

---

## 4. ⚠️ Defteri — kanıtsız / açık maddeler (index.md §6.7 + §9 uyarıları)

> **Kural**: Bu defterdeki maddeler kesin dille yazılamaz; her biri
> `⚠️ VERIFICATION REQUIRED` taşır ve tamamlanana kadar `[PLANNED]`/`[DESIGN]` kalır.

### 4.1 Repo kanıtı olmayan maddeler

| # | Maddede atıf | Beklenen kanıt | Durum |
|---|---|---|---|
| 1 | 8 servis (portproxy/kdig/capture/proxy/raw/pac/cleaner/capstone) | Windows servis kaydı / `sc.exe` çıktısı / servis kodu | `⚠️` [PLANNED] |
| 2 | `coremusic-capstone.exe` | derlenmiş binary + commit | `⚠️` [PLANNED] |
| 3 | `CoreMusic-Payload-ISO/` | ISO ağacı / build betiği | `⚠️` [PLANNED] |
| 4 | NetworkPolicy `EgressDefaultDeny` | Windows karşılığı (WFP filter) | `⚠️` [DESIGN] |
| 5 | Ekran kilidi / dokunmatik kalibrasyon / disk şifreleme | GPO/BitLocker kanıtı | `⚠️` [PLANNED] |
| 6 | `capstone.json` per-key sigorta zinciri | schema + imzalama kodu | `⚠️` [DESIGN] |
| 7 | Container bütçesi (mem %60 / disk %70) | Windows karşılığı + ölçüm | `⚠️` [DESIGN] |
| 8 | "SLOW LOG / 10 sn" | anahtar-kelime listesi | `⚠️` [PLANNED] |
| 9 | `coremusic-ev0.json` / ring.jsonl rol ayrımı | dosya şeması | `⚠️` [PLANNED] |
| 10 | boot-metrics (4 sn / 80%) | ölçüm metodolojisi | `⚠️` [PLANNED] |
| 11 | APFS hattı | macOS sürüm/materyali | `⚠️` [PLANNED] |
| 12 | Katman A sembolleri (Windows CRMPacket / CoreAudio) | simge envanteri | `⚠️` [PLANNED] |
| 13 | K000 → K001-K010 bağımlılık ağacı | komşu katman kartları | `⚠️` [PLANNED] |
| 14 | 5 klasör (device/…/watchdog) | disk ağacı | `⚠️` [PLANNED] |

### 4.2 Kaynağı olmayan hedefler (index.md §9 kuyruk listesi)

| # | Hedef | Atıf | Durum |
|---|---|---|---|
| 15 | Web ayağı — endpoint'ler, WebSocket kanalları, yetki ağacı | EK B / `.ai/api/` | `⚠️` — K000'a doğrudan kaynak yok |
| 16 | Health probe interval'ları (backend: 30 sn) | servis envanteri | `⚠️` |
| 17 | `ci/` dizini + `adrs-ek-c` reference'ı | `.ai/ci/` | `⚠️` |
| 18 | TCP 8765 YETKİ kapsül kontrolü | ADR-096 | `⚠️` |
| 19 | `.env` → `%LOCALAPPDATA%` geçişi | port-adapter §3.12 | `⚠️` |

### 4.3 Defter kapanış kuralı

Bir madde `⚠️`'den çıkarsa:

1. Kanıt diskten çıkar (gerçek dosya yolu + satır/commit),
2. İlgili deep dosyadaki madde `[CURRENT]`'e çekilir,
3. Bu tablo bir satır güncellenir (satır SİLİNMEZ — append-only düzeltme),
4. `validate.mjs --check` gate'i geçilir.

Kanıt üretilemezse madde defterde kalır; **defterde kalmak ihlal değildir,
kanıtsız kesin cümle ihlaldir.**

---

## 5. R14 araştırma kapısı — eksik kanıt nasıl tamamlanır

### 5.1 Kapı akışı

```text
Madde kanıtsız (§4)
  → araştırmacı ajan (research-analyst) — kaynak toplama
  → çapraz doğrulama (en az 2 bağımsız kaynak | repo commit tek başına yeter)
  → kanıt satırı üretilir (§2.4 şablonu)
  → bu dosyaya işlenir (§4 tablosu güncellenir)
  → validate.mjs gate
```

Kapı kuralı: `rules.md` R14 — doğrulanmayan iddia tam kanıt zinciri olmadan
`[CURRENT]`'e çekilemez.

### 5.2 Öncelik sırası (araştırma başlarken)

1. **Repo**: `git log` / `git grep` — en ağır kanıt.
2. **Vault**: ADR-096, anayasa, `.ai/.decisions/`.
3. **Harici doküman**: Microsoft/Apple resmi dokümanı (URL + tarih).
4. **Web**: yalnız 1-3'te bulunamazsa; iki bağımsız kaynak şart.

### 5.3 Red kriterleri

Aşağıdakiler kanıt sayılmaz:

- Model hafızası ("genelde böyle yapılır"),
- İmzasız/erefsiz repo dosyası (hangi commit?),
- Kırık/erişilemeyen URL,
- Kardeş K-ID'ye varsayımsal atıf (BÖLÜM 4 ihlali),
- Bu defterdeki maddenin kendisi (referans döngüsü).

---

## 6. İlişki özeti

| Kaynak | Bu dosyadaki rolü |
|---|---|
| `index.md` | Kanıtlanacak iddiaların SSOT kaynağı (§6.7/§9) |
| `00-kspace-anayasa.md` | K000 kimlik kanıtı (L45, L69-72) |
| `ADR-096-kspace-5000-boundary-model.md` | Derinlik k13-16 (4 deep dosya zorunluluğu) + EK C katman şablonu |
| F1 master prompt §5.4 · §10.2 · H10 | Durum etiketi kümesi · 16 alan kontratı · hedef ≠ kanıt ayrımı |
| `rules.md` R4 | Sürüm + kanıt zorunluluğu |
| `rules.md` R14 | Araştırma kapısı |
| `@.ai/CLAUDE.md` §8 | Zero-hallucination |
| `kimlik-karti.md` | 20 alanın kanıt durumu (bu dosyadaki defteri besler) |
| `bagimlilik-sinir.md` | İhlal akışı — kanıt zinciri burada başlar |
| `sorumluluk.md` | Sahiplik — kanıtın üreticisi rol/matrisi |

---

## 7. Değişiklik günlüğü

- 2026-10-08 — v1.0.0 — ilk sürüm (`index.md` §6.7 + §7 + §9 + `rules.md` R14'ten derlendi).
