---
node_id: K020-discovery-kararlar
node_type: subdomain
status: draft
parent_id: K020-discovery
children: []
depends_on: [K007-data, K009-security, K011-application]
evidence:
  - ".ai/architecture/K020-discovery/discovery-master.md §6 (gate haritası — ADR-010/095/097/101 gerçek dosya atıfları — 2026-10-17 disk doğrulandı)"
  - ".ai/.decisions/index.md (ADR-010 accepted · ADR-095 accepted · ADR-097 draft · ADR-101 accepted — 2026-10-18 okundu)"
  - ".ai/projects/NovaSearchEngine/01-kurulum-mimari-ve-acik-konular.md:4 (proje kökü) + .ai/decisions/ADR-001-youtube-api-v3-yasak.md (gerçek dosya yolu)"
validation: "Yalnız diskte var olan ADR numaraları; uydurma ADR adı yok; her karar durum (accepted/draft) ile."
open_questions:
  - "⚠️ 'media-provider auth gate' ADR'si YOK — üretim entegrasyonu bloke; gate onayı kullanıcı yetkisi (Bayram Ali)"
  - "⚠️ S-1 sahiplik ADR'si + D-3 sahiplik sınırı ADR'si HENÜZ YAZILMADI (bekleyenler)"
group: mimari
---

# Discovery — Kararlar

> K020-discovery'yi etkileyen **gerçek** ADR'ler ve kapı kararları. Yalnız diskte
> doğrulanmış numaralar kullanılır; var olmayan ADR uydurulmaz (ZERO-HALLUCINATION).

## §1. ELI15 — Bu dosya ne işe yarar?

Keşif ekibi "şu servisi entegre edelim" derken biri dur demek zorunda. Bu "dur"
demeleri ya bir **ADR** (arşivlenmiş mimari karar) ya da bir **kapı (gate)**'tır.
Bu dosya hangi kararın nerede olduğunu ve neye "dur" dediğini tek yerde gösterir.
Karar **üretmez** — sadece gerçek kaynakları haritalar.

## §2. Keşfi etkileyen gerçek ADR'ler

| ADR | Başlık | Durum | Keşif etkisi |
|---|---|---|---|
| **ADR-010** | CSRF protection strategy (`state`/PKCE) | accepted | Auth/CSRF gate: servis keşfi sonuçları onaydan önce sunulur; PKCE doğrulaması atlanmaz. **⚠️ "media-provider auth gate" diye ayrı ADR YOKTUR** (eski atıf uydurma dosya adıydı) |
| **ADR-095** | Hybrid JWT RS256 access token | accepted | §1.2 secret/log yasağı: `private.pem` asla git'e girmez, token `.md`'ye yazılmaz · §2.1 session cookie ≠ API JWT |
| **ADR-097** | K-space v3 sıfırdan kurulum — çoklu MD ve bant rezervasyonu | **draft** | §1 madde 6 (satır 33): YouTube Music resmi public API yok (TOS riski) → KAPALI · §2 K2 çoklu-MD · §2 K6 ID CONFLICT · §5.5 gruplama. **⚠️ §2.2/2.3/2.4 (pipeline/enum) iddiaları YOK — uydurma bölümler silindi** |
| **ADR-101** | Güvenlik kanonik sırası + uzak çağrı | accepted | §2.2 fallback zorunluluğu: API yanıt modeli asla "her zaman 200" kabul edilmez. **⚠️ "sentinel/seq.1-30" iddiası bu ADR'de YOK** |
| **ADR-001** (`./decisions/`, proje vault'u) | YouTube API v3 yasak | gerçek dosya | `packages/youtube/*` keşfiyle çapraz: v3 arama yüzeyi yasak kapsamındadır (yol: `.ai/decisions/ADR-001-youtube-api-v3-yasak.md`) |

## §3. ADR dışı bağlayıcı kararlar

| Karar | Kaynak (gerçek) | Etki |
|---|---|---|
| KAPALI kapı: YouTube Music | `draft/ADR-097` §1 madde 6 + `K020-recomenataion/index.md:55` çapraz | kod yazımı yasak — kapı **değişmez** |
| Ölçüm kanonu | `.ai/architecture/index.md` §9.12.1 (satır 761-764) | `ReadAllLines().Length`; `Measure-Object -Line` ile ölçülen sayı geçersiz |
| Sıra yasağı | root `AGENTS.md` §1 | `APPROVAL → IMPLEMENTATION` ters çevrilmez |
| Geçici arama kapsamı K-SCOPE-4 | `K007-data/data-kapsam.md:148/126` | S-1 çözülene dek kurallar K020'ye, indeks K007'ye geçici |
| SSOT tek-doğruluk | registry §3 + ADR-097 K1 | karar yalnız bir katmanda yazılır; alt katman `⚠️` ile referanslar |

## §4. Nygard-format karar örnekleri (şablon — bu K'ya ait taslak, ADR DEĞİL)

> Aşağıdakiler **sablon/gösterimdir**; gerçek ADR numarası **taşıtmazlar** (uydurma
> numara yasağı). Onaylanırsa `.ai/.decisions/draft/` altında gerçek numarayla açılır.

### Örnek-1 — S-1 arama sahipliği sınırı (taslak)

- **Bağlam:** `K007-data/search` ve `K020-discovery/search` ikisi de "arama" diyor
  (data-master.md:219 — 40+ referans). Çift indeks riski: tutarsız sonuç.
- **Karar:** Kurallar/sorgu-dilbilgisi K020-discovery'de; indeksleme/veri K007-data'da.
- **Alternatifler:** (a) tek çatı altında birleştirme — K007 40+ atfını kırar;
  (b) ikisini de bırak — çift indeks devam; (c) sahiplik ADR'si yaz — **seçilen**.
- **Sonuçlar:** Geçici K-SCOPE-4 ile yaşam; ADR yazılına dek `⚠️` taşınır.

### Örnek-2 — Medya-sağlayıcı auth gate (taslak — BEKLEYEN)

- **Bağlam:** Spotify PKCE · Tidal zorunlu PKCE · Apple Music JWT · Genius ticari
  lisans — her servisin kapısı farklı (tur-2). `ADR-010` yalnız CSRF'dir.
- **Karar:** Servis-bazlı auth gate ayrı ADR olarak açılır; onay kullanıcı yetkisindedir.
- **Alternatifler:** (a) ADR-010'u genişlet — CSRF kapsamı bulanıklaşır; (b) gate'i
  kod içine göm — denetlenemez; (c) ayrı ADR — **seçilen**.
- **Sonuçlar:** Üretim entegrasyonu bloke (durum: açık).

### Örnek-3 — Keşif skorlama eşiği (taslak)

- **Bağlam:** Aşama 3 "skorla" eşik tanımı yok (master §3 — `⚠️ eşik tanımsız`).
- **Karar:** Eşik, keşif bulguları biriktikten sonra ölçüme dayalı verilir; bugünden
  sayı **uydurulmaz**.
- **Alternatifler:** (a) literatür eşiği al — CoreMusic kataloğu için kanıtsız;
  (b) sabitle — ölçüm yok; (c) ertele + `UNKNOWN` işaretle — **seçilen**.
- **Sonuçlar:** Aşama 3 çıktısı `⚠️` ile taşınır; kapanış registry §9.x'te.

### Örnek-4 — Sinyal sözleşmesi (taslak)

- **Bağlam:** Eski sürüm `SignalType`/`ListenType` enum'ları taşıyordu — diskte
  **0 isabet** (Truth Mode 2026-10-17). `signals.ts` yalnız arama kalite skoru.
- **Karar:** Sinyal yüzeyi (enum/ucu/kayıt) ayrı tasarım + ADR gerektirir; keşif
  çıktıları var olmayan enum'a atıf yapmaz.
- **Alternatifler:** (a) enum'u vault'tan üret — uydurma; (b) mevcut `signals.ts`'i
  sinyal say — domain skoru ≠ sinyal; (c) ayrı ADR — **seçilen**.
- **Sonuçlar:** Açık soru olarak taşındı.

## §5. Kapalı/Ret kararları (bu K'da)

| Konu | Karar | Kaynak |
|---|---|---|
| YouTube Music entegrasyonu | **RET/KAPALI** — resmi API yok, TOS riski | ADR-097 §1/6 |
| `client/js/core/signal.js` vb. repo iddiaları | **SİLİNDİ** — 0 isabet | Truth Mode §9.3/2 |
| `log.md:774-801` atıfları | **SİLİNDİ** — log.md 118 satır | dogrulama §2 |
| `S-2…S-7` etiketleri | **SİLİNDİ** — yalnız S-1 gerçek | bagimliliklar §2 |
| "68 dosya · §9.13 K020 dahil" iddiası | **SİLİNDİ** — §9.13 K000-K013'ü kapsar | dogrulama §2/8 |

## §6. Açık sorular (karar bekleyen)

1. ⚠️ S-1 sahiplik ADR'si (bekliyor — Örnek-1 taslağı hazır).
2. ⚠️ D-3 K008 discovery sahiplik sınırı (domain + architect).
3. ⚠️ Medya-sağlayıcı auth gate ADR'si (Örnek-2 — kullanıcı onayı).
4. ⚠️ ADR-097 draft → accepted yol haritası (draft'ta kalan kapılar).

---

**Authority:** Bayram Ali / Vault Steward
