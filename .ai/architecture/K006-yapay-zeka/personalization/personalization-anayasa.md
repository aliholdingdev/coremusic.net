---
node_id: K006-yapay-zeka/personalization/personalization-anayasa
node_type: module
status: active
parent_id: K006-yapay-zeka/personalization
children:
  - K006-yapay-zeka/personalization/personalization-kapsam
  - K006-yapay-zeka/personalization/personalization-bilesenler
  - K006-yapay-zeka/personalization/personalization-dogrulama
  - K006-yapay-zeka/personalization/personalization-ornekler
  - K006-yapay-zeka/personalization/personalization-web-arastirma
depends_on:
  - K006-yapay-zeka/music-analysis/music-analysis-anayasa
  - K006-yapay-zeka/recommendation/recommendation-anayasa
  - K006-yapay-zeka/evaluation/evaluation-anayasa
evidence:
  - "Dosya: .ai/architecture/K006-yapay-zeka/index.md [Satır: 262]"
  - "Dosya: .ai/architecture/K006-yapay-zeka/recommendation/recommendation-anayasa.md"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/ai-models.md [Satır: 265]"
validation: "İçerik doğrulama: 2026-10-15 — anayasa, kök index.md §2.6 (satır 262) ve kardeş recommendation-anayasa.md ile eşleştirildi"
open_questions:
  - "Profil veri alanlarının tam listesi (hangi öznitelikler) UNKNOWN — kapsam dosyasında karar bekliyor"
  - "Profil saklama süresi ve silme (right to be forgotten) politikası ADR bekliyor"
  - "Soğuma (decay) fonksiyonu ve ağırlık parametreleri ölçülmeden seçilemez — UNKNOWN"
group: mimari
---
# Personalization — Alt Dal Anayasası (kademe-2)

> **Bu dosya kademe-2 anayasadır.** Ebeveyn: `K006-yapay-zeka` (bkz. `index.md` §2.6 çocuk haritası — v2 satır 262, `G:\` arşivinde).
> Kardeş anayasalar: `../recommendation/recommendation-anayasa.md`, `../music-analysis/music-analysis-anayasa.md`, `../evaluation/evaluation-anayasa.md`.
> Bu dosya **şablon içermektedir** — tüm alanlar somut değerle doldurulmuştur.

---

## §1. Kapsam Sınırı

**personalization**, K006'da **kullanıcıya özgü profilin üretilmesi, güncellenmesi ve saklanması** disiplinini kapsar. Kapsayıcılar:

1. **Profil inşası** — etkileşim sinyallerinden (dinleme, beğenme, atlama) kullanıcı öznitelik üretimi.
2. **Profil güncelleme** — yeni etkileşimle birlikte ağırlık güncelleme, soğuma (decay), oturum özeti.
3. **Profil saklama & mahremiyet** — veri alanı, saklama süresi, silme/anonymousleştirme.
4. **Profilin teslimi** — recommendation'a profil **sinyali** olarak aktarım (sıralama **bu dalda değildir**).

**Kapsam dışı:**

- Öneri **sıralama/aday üretimi** → `recommendation` (personalization profil verir, **karar vermez**)
- İçerik **etiketleme** (parça hakkındaki bilgi) → `music-analysis`
- Profil **modelinin canlı çağrısı/önbelleği** → `inference` (burada yalnız profil *sözleşmesi* tanımlanır)
- Kimlik/auth → K006 dışı (auth domain)

**Kapsam sınırı testi:** "Girdi **kullanıcı**, çıktı **kullanıcı-özellikleri** mi?" → Evet = personalization. "Çıktı **önerilen parça listesi** mi?" → recommendation.

---

## §2. İlke ve Değerler

### §2.1 Öncelik sırası

| # | İlke | Gerekçe |
|---|------|---------|
| 1 | **Profil = tahmindir, gerçek değildir** | Profil alanları olasılıksal/ağırlıklı; kesin "ilgi" iddiası yasak |
| 2 | **Mahremiyet önce, personalization sonra** | Yanlış kişiselleştirme düzeltilebilir; sızan veri geri alınamaz |
| 3 | **Soğuma (decay) zorunlu** | Eski davranış bugünkü tercihi temsil etmez — taze sinyal ağır |
| 4 | **Yeni kullanıcı bozukluğu** | Yeni/az-etkileşimli kullanıcıda profil **soğuk başlangıç** olarak işaretlenir — uydurma profil üretilmez |
| 5 | **Şeffaflık & itiraz hakkı** | Kullanıcı profilinin **ne içerdiği** bilinmeli, düzeltme/silme mümkün olmalı |

### §2.2 Zero-hallucination bağlayıcılığı

1. Profil-öznitelik listesi (hangi alanlar) netleşmemişse `UNKNOWN` — **alan uydurulmaz** (§12.3).
2. Decay/ağırlık sayıları **ölçülmeden** kesin yazılmaz; formül verilir, parametre `UNKNOWN` işaretli.
3. "X kullanıcısının profili şöyle" tipi örnekler **sentetik** olarak işaretlenir — gerçek kullanıcı verisi aktarılmaz.
4. Kişiselleştirme kazanç iddiaları (ör. CTR artışı) **ölçüm/rapor bağlantısıyla** yazılır — kaynağı yoksa `⚠️ VERIFICATION REQUIRED`.

---

## §3. Mimari Roller ve Kararlar

### §3.1 Profil boru hattı

```
[1] SİNYAL      → etkileşim olayları (dinleme/süre/atlama/beğeni)
[2] ÖZNİTELİK   → davranış özniteliklerine dönüşüm (§4)
[3] GÜNCELLEME  → ağırlık güncelleme + decay (§5)
[4] KALİTE      → soğuk-başlangıç tespiti + doluluk ölçümü (§6)
[5] MAHREMİYET  → alan minimizasyonu + saklama/silme (§7)
[6] TESLİM      → recommendation'a profil sinyali (§10)
```

**Karar (K-PS-01):** Personalization **sıralama yapmaz** — profil sinyali üretir; sıralama ağırlıkları `recommendation`'ındır (ayrım, sahiplik karmaşasını önler).
**Kanıt:** Kök `index.md` [Satır: 262] kapsam satırı; kardeş `recommendation-anayasa.md` sıralama sahipliği.

### §3.2 Sahiplik matrisi

| Alan | Sorumluluk | Not |
|------|-----------|-----|
| Sinyal toplama sözleşmesi | personalization | §4 (hangi olay hangi öznitelik) |
| Profil şeması + saklama | personalization | §4, §7 |
| Decay/ağırlık parametresi | personalization + evaluation ölçümü | §5 |
| Sıralama ağırlıkları | `recommendation` | bu dal **girdi** verir |
| Kazanç ölçümü (A/B) | `evaluation` | §9 |
| Profil canlı okuma/önbellek | `inference` | §10 |

**Karar (K-PS-02):** Profil **verisi** bu dalın, profil **okuma hızı/önbelleği** inference'ın, profil **kullanımı** recommendation'ın — üçü tek yerde birleşmez.

---

## §4. Profil Şeması (zorunlu alanlar)

### §4.1 Çekirdek profil kaydı

| Alan | Zorunlu | Açıklama |
|------|---------|----------|
| `user_id` | Evet | kimlik (anonimleştirme düzeyi §7.3) |
| `updated_at` | Evet | son güncelleme damgası |
| `signal_count` | Evet | toplam etkileşim — doluluk göstergesi |
| `cold_start` | Evet | `true` ise profil **yetersiz** (§6.1) |
| `attributes` | Evet | öznitelikler: `{name, weight, updated_at}` listesi |
| `schema_version` | Evet | profil şeması sürümü |
| `decay_params` | Evet | kullanılan decay parametresi (§5) |
| `consent` | Evet | izin düzeyi (hangi alan işlenebilir — §7.2) |
| `retention_until` | Evet | silme tarihi (§7.4) |

### §4.2 Öznitelik kuralları

1. Öznitelik adları **kayıtlı listeden** (kontrol listesi) — serbest ad yasak (music-analysis §5 ile aynı disiplin).
2. Her öznitelik **`weight` + `updated_at`** taşır — ağırlıksız öznitelik yok.
3. Kaynak sinyal **ölçülebilir** (kaç olay) — `signal_count` ayrıca öznitelik düzeyinde de tutulur.
4. `UNKNOWN` alanlar **liste dışıdır** — kapsam kararı alınana kadar şemaya **eklenmez** (uydurma yok, §2.2.1).

**Karar (K-PS-03):** Profil şeması **ADR ile sabitlenir**; alan ekleme yeni `schema_version` + migrate ile yapılır (eski alanlar silinmez, `deprecated` işaretlenir).

---

## §5. Güncelleme ve Soğuma (Decay)

### §5.1 Güncelleme kuralı

```
w_new = f(w_old, sinyal_türü, taze_sinyal, decay(t))
```

- **Yön:** destekleyen sinyal ↑, ters sinyal (atlama) ↓ — **simetrik** kural, uç durumlar test edilir (§9).
- **Türlü ağırlık:** dinleme-süresi ≠ beğeni ≠ atlama — her sinyal türünün katsayısı ayrı (değerler `UNKNOWN`, ölçümle belirlenir).

### §5.2 Decay

| Parametre | Kural | Durum |
|-----------|-------|-------|
| Fonksiyon | üstel/ yarı-ömür biçiminde | formül bu anayasa düzeyinde; **yarı-ömrü `UNKNOWN`** (ölçülmeden seçilemez) |
| Uygulama | okumada **pasaif** decay (fiziksel yazım yok — performans) + periyodik fiziksel yazım | mimari karar (K-PS-04) |
| Sıfırlama | `retention_until` dolduğunda alan **silinir** (§7.4) | mahremiyetle uyumlu |

**Karar (K-PS-04):** Decay **okuma anında** hesaplanır (lazy) — saniyede milyonlarca profilin fiziksel olarak güncellenmesi yerine; periyodik fiziksel yazım yalnız **rota/rapor** içindir.

### §5.3 Oturum (session) davranışı

1. Kısa oturum içi tekrarlar **patlama (burst)** sayılır — spam/art arda aynı parça **aşırı ağırlık üretmez** (katsayı sınırlı).
2. Oturum sonunda **özet** yazılır (session summary) — ham olay akışı profilde **tutulmaz** (minimizasyon, §7.1).

---

## §6. Soğuk Başlangıç ve Doluluk

### §6.1 Soğuk başlangıç tanımı

| Durum | Koşul | Davranış |
|-------|-------|----------|
| Yeni kullanıcı | `signal_count` eşiği altı | `cold_start: true` — popüler/keşif karışımı (öneri tarafı; bu dal **işaretleme** yapar) |
| Az-etkileşimli | öznitelik doluluğu düşük | güven aralıklı profil — ağırlıklar **küçük** kalır |
| Uzun pasif | son etkileşim eski | decay tümüyle düşürmüş → yeniden `cold_start` |

**Kural (K-PS-05):** Eşikler **ölçümle** belirlenir (evaluation) — eşik `UNKNOWN` ise varsayılan **muhafazakâr** (daha erken `cold_start`), uydurulmuş kesin eşik yazılmaz.

### §6.2 Doluluk ölçümü (profil sağlığı metrikleri)

| Metrik | Tanım | Kapı |
|--------|-------|------|
| Doluluk oranı | dolu alan / toplam alan | rapor |
| Soğuk oranı | `cold_start` kullanıcı oranı | rapor + alarm |
| Tazelik | sinyal yaşı dağılımı (decay etkisi) | rapor |
| Sinyal/tüketim oranı | gelen olay vs. okunan profil | sistem metriği |

---

## §7. Mahremiyet ve Saklama

### §7.1 Minimizasyon

1. Profil **yalnız** kişiselleştirme için gereken alanları içerir (§4 şeması dışı alan yok).
2. Ham olay akışı profilde **tutulmaz** — özet/öznitelik düzeyinde (§5.3.2).
3. Serbest metin (arama sorgusu vb.) profilde **tutulmaz** — ya tokenize/etiket dönüşümü ya hariç (`UNKNOWN` kapsam kararı → §12.3).

### §7.2 İzin (consent) bağlaması

| İzin düzeyi | İzinli işlev |
|-------------|--------------|
| Yok | personalization **kapalı** — öneri genel/popüler |
| Kısmi | izinli alanlar işlenir; izinsiz alan **hiç üretilmez** |
| Tam | şema dahili tümü |

**Kural:** İzin, alan-özelik düzeyinde çalışır — "hepsi veya hiç" dayatması yok; **izin geri çekimi** anında etkili olur (alanlar silinir, kuyruk işleri iptal).

### §7.3 Anonimleştirme & kimlik

1. Profil anahtarı **pseudonymous** kimliktir — kimlik sistemiyle **doğrudan birleşik** tutulmaz (harici sistemle eşleştirme ancak gerekliyse, sınırlandırılmış).
2. `user_id` değerleri raporlara **gömülmez** — istatistiksel özet yayımlanır.

### §7.4 Saklama süresi & silme

| Olay | Aksiyon |
|------|---------|
| `retention_until` doldu | profil alanları **silinir** (kayıt: silme olayı) |
| Kullanıcı silme talebi | tümü + türev özetler silinir; silme **kanıtı** tutulur (ne-silindi, ne-zaman) |
| İzin geri çekimi | §7.2 |

**Karar (K-PS-06):** Silme işlemi **taraflı** değildir: profil + cache (inference) + downstream kopyalar (recommendation) **hepsi** kapsamdadır — kısmi silme = ihlal.

---

## §8. Rehberler (Guidelines)

1. **Şema önce model** — profil alanları kararlaştırılmadan güncelleme kodu yazılmaz (K-PS-03).
2. **Sinyal türü ayrı** — her sinyalin katsayısı ayrı, test edilebilir (§5.1).
3. **Lazy decay** — fiziksel yazım periyodik (K-PS-04).
4. **Görünür soğukluk** — `cold_start` bayrağı tüketiciye **açıkça** iletilir; sessiz-az-profil yok.
5. **Profil = girdi, karar değil** — sıralama ağırlıkları recommendation'ın (K-PS-01).
6. **Muhafazakâr eşik** — `UNKNOWN` eşikte varsayılan savunmacı (K-PS-05).
7. **Minimizasyon** — her alan "buna gerçekten gerek var mı?" sorusundan geçer (§7.1).
8. **Geri izlenebilir şema** — `schema_version` + migrate (K-PS-03).

---

## §9. Zorunlu Kurallar (MUST / MUST NOT)

| # | Kural | Seviye |
|---|-------|--------|
| P1 | Profil kaydı **zorunlu alanları** (§4.1) tam taşımalıdır | MUST |
| P2 | `cold_start` durumu **işaretli** olmalı — yetersiz profil sessiz sunulmamalı (§6.1) | MUST |
| P3 | Decay uygulanmadan **eski sinyal** taze sinyalle eşit ağırlıkta **kalamaz** (§5.2) | MUST |
| P4 | Ham olay akışı / serbest metin profilde **tutulmaz** (§7.1) | MUST NOT |
| P5 | İzin geri çekimi **anında** etkili olmalı; izinsiz alan **üretilmez** (§7.2) | MUST |
| P6 | Silme **tüm katmanlarda** (profil + cache + downstream) tamamlanır (K-PS-06) | MUST |
| P7 | Sıralama ağırlıkları bu dalda **tanımlanamaz** (K-PS-01) | MUST NOT |
| P8 | Parametreler (decay yarı-ömrü, eşikler) ölçülmeden kesin sayı yazılmaz (§2.2.2) | MUST |
| P9 | Raporlarda `user_id` **gömülmez** — istatistiksel özet (§7.3.2) | MUST NOT (gömme) |
| P10 | `retention_until` alanı **her profilde** bulunur (§4.1) | MUST |
| P11 | Soğuk kullanıcıya **uydurma profil** üretilmez — `cold_start` + genel keşif (§6.1) | MUST NOT |

---

## §10. Bağımlılıklar ve Uç Noktalar

### §10.1 Bağımlı olduğu alanlar

| Alan | Ne alır | Kaynak |
|------|---------|--------|
| `music-analysis` | içerik öznitelikleri (profil eşleştirme girdisi) | `../music-analysis/` |
| `recommendation` | sıralama sözleşmesi (ne teslim edilir) | `../recommendation/` |
| `evaluation` | eşik/kazanç ölçümü (A/B, gate) | `../evaluation/` |
| `inference` | canlı okuma/önbellek altyapısı | `../inference/` |
| `testing` | şema + gizlilik testleri | `../testing/` |

### §10.2 Çıkış (uç nokta)

| Çıktı | Alıcı | Biçim |
|-------|-------|-------|
| Profil sinyali (`attributes` + `cold_start` + güven) | `recommendation` | §4 şeması |
| Sağlık metrikleri (doluluk/soğuk/tazelik) | `evaluation`/dashboard | §6.2 |
| Silme/izin olayları | denetim kaydı | §7 |
| `schema_version` göç planı | `testing`/ADR | K-PS-03 |

---

## §11. Karar Kaydı

| Karar | İçerik | Kanıt |
|-------|--------|-------|
| K-PS-01 | Bu dal profil üretir, **sıralamaz** | kök index [Satır: 262] + recommendation-anayasa |
| K-PS-02 | Veri/okuma/kullanım sahipliği ayrık | bu dosya §3.2 |
| K-PS-03 | Şema ADR ile; alan ekleme versioned migrate | bu dosya §4.2 |
| K-PS-04 | Decay lazy (okuma anında); fiziksel yazım periyodik | bu dosya §5.2 |
| K-PS-05 | `cold_start` eşikleri ölçümle; `UNKNOWN`-ken muhafazakâr | bu dosya §6.1 |
| K-PS-06 | Silme tüm katmanlar (profil+cache+downstream) | bu dosya §7.4 |

**Reddedilen alternatifler:**

- *Profil + sıralama tek modülde* → sahiplik/ölçüm karmaşası (K-PS-01).
- *Fiziksel decay (her olayda yaz)* → milyonlarca yazım, gecikme (K-PS-04).
- *Yeni kullanıcıya "tahmini" profil* → uydurma (P11, İlke 4).
- *Eşiği göze göre seçmek* → keyfi kapı (P8, evaluation E10).

---

## §12. İlişkili Dosyalar ve Açık Sorular

### §12.1 Kardeş dosyalar (bu dal)

`index.md` · `personalization-kapsam.md` · `-bilesenler.md` · `-dogrulama.md` · `-ornekler.md` · `-web-arastirma.md` · kök `../yapay-zeka-kararlar.md` + `../yapay-zeka-riskler.md`

### §12.2 Vault referansları

| Dosya | Ne verir | Not |
|-------|----------|-----|
| `K006-yapay-zeka/index.md` [Satır: 262] | ebeveyn bağlantısı | v2 §2.6 |
| `recommendation/recommendation-anayasa.md` | sıralama sahipliği sınırı | kardeş |
| `ai-models.md` [Satır: 265] | içerik öznitelik girdisi (genre) | music-analysis kökenli |
| `../evaluation/evaluation-anayasa.md` | A/B + kapı usulü | §5.2 |

### §12.3 Açık sorular

1. **Profil alan listesi** (hangi öznitelikler) → kapsam dosyasında karar bekliyor → `UNKNOWN` (K-PS-03 ile korumalı: şemaya alınmadı).
2. **Saklama süresi / GDPR benzeri talep prosedürü** → ADR bekliyor → `UNKNOWN`.
3. **Decay yarı-ömrü / sinyal katsayıları** → ölçüm (evaluation) gerektirir → `UNKNOWN` (P8).
4. **Cold-start eşik değeri** → ölçümle belirlenecek → `UNKNOWN` (K-PS-05).
5. **Serbest metin (arama) sinyali** kapsam içinde mi? → `UNKNOWN` (§7.1.3).

---

## §13. İlgili Lint Maddeleri

| LINT (`.ai/CLAUDE.md` §6) | Bu anayasadaki karşılığı |
|---------------------------|--------------------------|
| Zero-hallucination | §2.2 — parametre/eşik uydurma yasak |
| `UNKNOWN` / `VERIFICATION REQUIRED` | §12.3 |
| Kanıt zorunluluğu | §11 karar tablosu |
| Keyfi eşik yasağı | K-PS-05, P8 |
| Gizlilik (PII/log) | §7 — ham sinyal/yüksek kimlik tutmama |

---

## §14. Bağlam ve Versiyon

| Alan | Değer |
|------|-------|
| Sahip | K006-yapay-zeka / personalization |
| Ebeveyn | `K006-yapay-zeka` (kök `index.md` §2.6 — v2 satır 262) |
| Kademe | 2 |
| Durum | active |
| Oluşturulma | 2026-10-15 |
| Kapsanan sibling'lar | `personalization-kapsam` · `-bilesenler` · `-dogrulama` · `-ornekler` · `-web-arastirma` · `index.md` |
| Grup | mimari (`group:` frontmatter) |
| Belge tipi | anayasa (kademe-2, bu dalın usul belgesi — şablon değil) |

---

## §15. Bileşen Sözlüğü (TR↔EN, bu dalın terimleri)

| TR | EN | Bu anayasadaki anlamı |
|----|----|------------------------|
| Profil sinyali | profile signal | recommendation'a teslim edilen girdi (§10.2) |
| Soğuma | decay | zamanla ağırlık azalması (§5.2) |
| Soğuk başlangıç | cold start | yetersiz sinyalli kullanıcı (§6.1) |
| Doluluk | profile fill rate | dolu alan oranı (§6.2) |
| Patlama | burst | oturum içi tekrar (§5.3.1) |
| Minimizasyon | data minimisation | gereksiz alan tutmama (§7.1) |
| Rastgele kimlik | pseudonymous id | kimlikle birleşik olmayan anahtar (§7.3) |
| Saklama | retention | `retention_until`'a kadar tutma (§7.4) |
| İzin | consent | alan-özelik işlev izni (§7.2) |
| Kaynak sinyal | source signal | etkileşim olayı (§4.2.3) |
| Şema göçü | schema migration | `schema_version` değişimi (K-PS-03) |

**Terim kuralı:** Olmayan terim bu dalda icat edilmez — önce `inference-anayasa.md` §9 sözlüğüne bakılır.

---

## §16. Durum Matrisi (profil durumları)

| Durum | Tanım | Tüketim |
|-------|-------|---------|
| `EMPTY` | hiç sinyal yok (yeni kullanıcı) | `cold_start` akışı (P11) |
| `COLD` | sinyal var ama eşik altı | `cold_start: true` + sınırlı öznitelik |
| `ACTIVE` | sağlıklı doluluk + tazelik | tam profil sinyali |
| `STALE` | tazelik düşmüş (decay ağır bastı) | profil + bayrak — tüketici ağırlığı bilir |
| `CONSENT_PAUSED` | izin geri çekildi | **üretim durdu**, veriler §7.2/§7.4'e göre |
| `DELETING` | silme işlemde (K-PS-06) | okuma **kapalı** |
| `DELETED` | silindi (kanıt kaydı var) | yok — yeniden açılabilir yeni kayıt |
| `SCHEMA_PENDING` | yeni şemaya göç bekliyor | eski şema okunur, migrate kuyrukta |

**Zorunlu kural:** Geçişler **olay kaydı** üretir; `DELETING → ACTIVE` **yoktur** (silme geri alınamaz — yeniden sinyal yeni kayıttır).

---

## §17. Hata Sınıfları ve Profil Davranışı

| Sınıf | Örnek | Davranış | Kayıt |
|-------|-------|----------|-------|
| Sinyal | olay şeması bozuk | olay **düşer** (profil bozulmaz) | düşen olay sayacı |
| Şema | `schema_version` uyuşmaz | `SCHEMA_PENDING` + migrate | olay + plan |
| İzin | izin yokken alan okundu | alan **üretilmez/okunmaz** | ihlal olayı (güvenlik) |
| Silme | silme kısmi başarısız | **retry + alarm** (P6) | silme denetim kaydı |
| Performans | okuma gecikmesi | cache fallback (inference) | sistem metriği |
| Bütünlük | `retention_until` yok | kayıt **reddedilir** (P10) | şema hatası |
| Eşik | `UNKNOWN` parametre | muhafazakâr varsayılan (K-PS-05) | karar notu |

**Kural (K-PS-07):** Hata profili **sessizce yanlış üretmez** — hatanın adresi ya "işlenmedi/üretilmedi" (izlenebilir), ya "güvenli tarafta" (muhafazakâr) taraftır.

---

## §18. Güvenlik ve Etik (bu dalın yüzeyi)

1. **Profil = hassas varlık** — en az PII düzeyinde korunur (erişim kontrolü, şifreleme yüzeyi altyapıda; **bu anayasa erişim kuralını bağlar**: yalnız `recommendation`/`evaluation` rolleri okur).
2. **Profil sızıntısı = özel hayat ihlali** — profil dump'ı log'a/rapor'a **gömülmez** (P9).
3. **Manipülasyon** — kullanıcı-üretilen sinyallerde abartılı ağırlık patlaması sınırlı (§5.3.1 burst kuralı) — profil-savunması.
4. **Ayrımcılık riski** — profil öznitelikleri **korunan özellikler** (ör. etnik/din benzeri alanlar) içeremez → kontrol listesi §4.2.1'de **yasak-baz** olarak tutulur (`UNKNOWN` listesi kapsam kararında) → evaluation bias denetimi (K006 §2.1) ile ilişkili.
5. **Şeffaflık** — kullanıcının "profilimde ne var" görme yüzeyi **zorunlu** (İlke 5) — yüzey tasarımı K006 dışı UI, **işlev sözleşmesi** burada kayıtlıdır.

---

## §19. Anti-Patternler (yasak kısayollar)

| Anti-pattern | Neden zararlı | Doğrusu |
|--------------|---------------|---------|
| "Profilde her şeyi tutalım" | mahremiyet ihlali | minimizasyon (P4, §7.1) |
| "Yeni kullanıcıya tahmin uydur" | sahte profil | `cold_start` (P11) |
| "Decay'i sonra ekleriz" | bayat ağırlık | P3 |
| "Silme sadece profilden" | downstream sızıntı | K-PS-06 (tüm katmanlar) |
| "İzin tek seferde alınır" | geri çekim yok | alan-özelik izin (§7.2) |
| "Eşikleri göz kararı" | keyfi | ölçüm + muhafazakâr varsayılan (K-PS-05) |
| "Sıralamayı da burada yapalım" | sahiplik bulanık | K-PS-01 |
| "Rapora kullanıcı ID'leri" | kimlik sızıntısı | P9 |

---

## §20. Ölçüm ve Test Kapıları (özet — detay §12.1 kardeş dosyalar + evaluation)

1. **Şema kapısı** — §4 zorunlu alanlar + `retention_until` (P1, P10).
2. **Soğuk başlangıç kapısı** — eşik senaryoları (P2, P11).
3. **Decay kapısı** — zaman atlatma testi: eski sinyal gerçekten düşüyor mu (P3).
4. **İzin kapısı** — geri çekim → alanlar anında yok (P5) + izinli alanlar (§7.2).
5. **Silme kapısı** — üç katmanlı silme doğrulaması (P6) + kanıt kaydı.
6. **Burst kapısı** — oturum içi spam sınırlama (§5.3.1).
7. **Kalite kapısı** — profil → öneri kazanç A/B (evaluation §5.2; kazanç `UNKNOWN` ise ölçüm başlar).

---

## §21. Kaynaklar ve Red-Team Soruları

### §21.1 Kaynaklar

| # | Kaynak | Kullanım |
|---|--------|----------|
| R1 | `K006-yapay-zeka/index.md` [Satır: 262] | ebeveyn bağlantısı |
| R2 | `recommendation/recommendation-anayasa.md` | sıralama sahipliği sınırı |
| R3 | `ai-models.md` [Satır: 265] | içerik öznitelik girdisi |
| R4 | `evaluation-anayasa.md` §5.2, §16 | A/B + kapı usulü |
| R5 | Web kaynağı | AÇILMADI — bu turda URL eklenmedi (`personalization-web-arastirma.md`'ye devredildi) |

### §21.2 Red-Team soruları

| # | Soru | Beklenen cevap |
|---|---|---|
| T1 | İzin geri çekildi, profil hâlâ okunuyor mu? | hayır — P5, `CONSENT_PAUSED` |
| T2 | Silme sonrası cache'te profil duruyor mu? | durmaz — K-PS-06 |
| T3 | 1000 aynı parça art arda dinlendi? | burst sınırlı — §5.3.1 |
| T4 | Eski sinyal (1 yıl önce) hâlâ güçlü mü? | hayır — decay, P3 |
| T5 | Soğuk kullanıcıya "ilgi" mi sunuldu? | `cold_start` + genel keşif — P11 |
| T6 | Raporda `user_id` var mı? | yok, istatistik — P9 |
| T7 | `retention_until` eksik kayıt? | reddedilir — P10 |
| T8 | Sıralama ağırlığı bu dalda mı? | hayır — K-PS-01 |
| T9 | Korunan özellik profilde mi? | kontrol listesi yasak-baz — §18.4 |
| T10 | Decay parametresi kaynağı ne? | ölçüm/`UNKNOWN` — K-PS-05, P8 |

### §21.3 Revizyon Kaydı

| Tarih | Değişiklik | Yapan | Not |
|---|---|---|---|
| 2026-10-15 | İlk yazım (dosya oluşturma) | K006 vault içerik ajanı | Append-only; §1–§21 |
| — | Sonraki revizyon | — | Yalnız ek bölüm olarak |

**Authority:** Bayram Ali / Vault Steward · **status:** active — §12.3 açık soruları ADR/ölçüm ile kapatılır.

---

## §22. Kapı Özeti — anayasa son kararı

Bir personalization değişikliği aşağıdaki kapıların **tamamından** geçmeden profil sinyali olarak teslim edilmez:

1. **Şema kapısı** — §4 zorunlu alanlar + `schema_version` + `retention_until` (P1, P10).
2. **Mahremiyet kapısı** — minimizasyon + izin + üç-katmanlı silme (P4, P5, P6).
3. **Soğukluk kapısı** — `cold_start` işaretleme ve eşik muhafazakârlığı (P2, P11, K-PS-05).
4. **Tazelik kapısı** — decay uygulanmış eski sinyal (P3, K-PS-04).
5. **Sahiplik kapısı** — sıralama ağırlığı bu dalda **üretilmemiş** (P7, K-PS-01).
6. **Parametre kapısı** — `UNKNOWN` sayılar kesin yazılmamış; ölçüm planı var (P8).
7. **Kazanç kapısı** — profil → öneri A/B ölçümü (evaluation §5.2) olmadan kalıcı yayma yok.

**Tek cümle özet:** *"İzinsiz, bayat, ölçülmemiş ve silinemeyen profil, kişiselleştirme malzemesi değil, yükümlülüktür."*
**Revizyonla ilişkisi:** Bu bölüm §21.3 revizyon kaydından sonra eklenmiştir (append-only); sonraki ek bölüm §23, §24… olarak numaralanır.

---

## §23. Ek Usul — Değişiklik Eşiği Tablosu (ne değişince ne tekrarlanır)

| Değişen yüzey | Tekrarlanacak kapı | Neden |
|---|---|---|
| Profil şeması (`schema_version`) | kapı 1 (şema) + göç testi (K-PS-03) | alan/retention ilişkileri |
| Decay parametresi (yarı-ömür) | kapı 4 (tazelik) + ölçüm (P8) | bayatlık davranışı değişir |
| `cold_start` eşiği | kapı 2 + doluluk metrikleri (§6.2) | soğuk oranı değişir |
| İzin modeli (alan düzeyi) | kapı 2 (mahremiyet) + §7.2 senaryoları | izinli alan kümesi değişir |
| Sinyal türleri/katsayıları | kapı 4 + burst testi (§5.3.1) | ağırlık davranışı değişir |
| Silme mekanizması | kapı 5 (üç katman) + denetim kanıtı (P6) | sızıntı riski |

**Son kural:** Tabloda olmayan değişiklik bile, **ölçüm (§20.7 kazanç kapısı) + mahremiyet kapıları (P4–P6) olmadan** profil sinyali olarak teslim edilmez — bu ikisi her değişikliğin alt kümesidir.

**İlişki notu:** Bu tablo §11.1 bağımlılıkları ile birlikte okunur — `music-analysis` girdisi değişirse (içerik öznitelikleri) §20 giriş + §20.2 çıkış **yeniden** bağlanır; downstream tüketici (`recommendation`) sözleşme değişikliğinde §3.2 sahiplik satırları **dokunulmaz** kalır (K-PS-01: sıralama recommendation'ın).
