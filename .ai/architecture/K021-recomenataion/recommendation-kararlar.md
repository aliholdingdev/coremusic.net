---
node_id: K021-recomenataion-kararlar
node_type: subdomain
status: draft
parent_id: K021-recomenataion
children: []
depends_on: [K006-yapay-zeka, K009-security, K019-comilance]
evidence:
  - ".ai/architecture/K021-recomenataion/recommendation-master.md §25 (karar defteri D1-D6) ve §31 (ADR referansları — 2026-10-18)"
  - ".ai/.decisions/index.md (ADR-030 accepted · ADR-029/ADR-025 accepted · ADR-097 draft — 2026-10-18 okundu)"
  - "recommendation-arastirma.md §0 (RFC 9110 + MDN: 403 semantiği HTTP 200 doğrulandı — 2026-10-17)"
validation: "Yalnız diskte var olan ADR numaraları; ADR-098 'henüz yazılmadı' olarak; uydurma numara yok."
open_questions:
  - "⚠️ ADR-2 (sıralama yöntemi) · ADR-3 (metrik eşikleri) HENÜZ AÇILMADI — numaralar master'da kapı adıdır, dosya yok"
  - "⚠️ ADR-098 (Band-2 + K021 ID kararı) henüz yazılmadı — üst karar"
group: mimari
---

# Recommendation — Kararlar

> K021'i etkileyen **gerçek** ADR'ler + program karar defteri (D1-D6). Uydurma ADR
> numarası yok; kapı adı olarak kullanılan "ADR-2/ADR-3" henüz açılmamış kapılardır.

## §1. ELI15

Öneri sisteminde asıl soru "hangi sıralama?" ve "kaç puan iyi?". Bu iki soru
**henüz cevaplanmadı** — kapı olarak duruyorlar. Bu dosya: hangi karar verilmiş
(gerçek ADR), hangisi bekliyor (kapı) ve kimin yetkisinde olduğunu gösterir.

## §2. İlgili gerçek ADR'ler

| ADR | Başlık | Durum | K021 etkisi |
|---|---|---|---|
| **ADR-030** | YZ stratejisi çerçevesi | accepted | `recommendation-master.md` authority zinciri: bu programın YZ-sınırı bu ADR'ye bağlı |
| **ADR-097** | K-space v3 kurulumu — çoklu MD + bant rezervasyonu | **draft** | §5 isimlendirme + iki-ekseni şema (`group` + §GRUPLAR) — bu K'nın frontmatter kanonu |
| **ADR-029** | Dinleme odaları | accepted | sosyal karar sınırı (master §31 — yalnız sınır notu) |
| **ADR-025** | EQ ayarları | accepted | sosyal karar sınırı (master §31 — yalnız sınır notu) |
| **ADR-010** | CSRF stratejisi (state/PKCE) | accepted | K009 auth kapısı → 401/403 semantiği (çapraz) |
| **ADR-095** | Hybrid JWT RS256 | accepted | oturum↔API ayrımı — S4 API sözleşmesi (çapraz) |
| **ADR-098** | Band-2 açılışı + K021 ID kararları | **HENÜZ YAZILMADI** | ID CONFLICT kapısı (master §12/5) — numara gelecek, bugünden içerik uydurulmaz |

## §3. Program karar defteri (D1-D6 — master §25)

| # | Karar | Durum | Sahip |
|---|---|---|---|
| D1 | Boru hattı kanonik tanımı S1-S6 | ✅ alındı (2026-10-17) | Vault Steward |
| D2 | Sıralama yöntemi (A kural / B skor / C öğrenen) | ⏸ bekliyor — ADR-2 | ADR mercii (master'da `K020-adr` — ⚠️ klasör adı doğrulanmadı) |
| D3 | Metrik seçimi + eşikler | ⏸ bekliyor — ADR-3 | `evaluation/` + ADR |
| D4 | KVKK kapsamı + hukuki dayanak | ⏸ bekliyor — K1 | `K019-comilance` (⚠️ master 'K020-comilance' yazar) |
| D5 | `0.6/0.4` ağırlık doğrulaması | ⏸ bekliyor — Faz-4 ölçümü | Vault Steward + K006 |
| D6 | Klasör ID'si (K021 vs 40+ K020 referansı) | ⏸ üst karar | Vault Steward + kullanıcı |

## §4. Kapı kararları (kural olarak bağlayıcı — ADR değil)

| Karar | Kaynak | Etki |
|---|---|---|
| S6 tanımlanmadan S4 yayına alınamaz | master §9 (K4/K5) | ölçüm yoksa yayım yasak |
| Eşik uydurulmaz | master §8 (zero-hallucination) | ölçüm yapılmadan sayı yazılmaz |
| K1 açıkken K2/K3 kısıtlı | master §27 | veri kapsamı belirsizken yöntem seçmek spekülatif |
| Profil K1 öncesi toplanmaz | master §7 | kişiselleştirme kapalı başlar (fail-safe) |
| Boş liste doldurulmaz | master §18/§19 | uydurma öneri yasak |
| Fail-safe zorunlu, fail-open yasak | master §26 sözlük | hata = kişiselleştirmeyi atla, genel sıralamayı koru |

## §5. Nygard-format karar örnekleri (şablon — ADR numarası TAŞIMAZ)

> Onaylanırsa `.ai/.decisions/draft/` altında gerçek numarayla açılır; bugünden
> numara uydurulmaz.

### Örnek-1 — Sıralama yöntemi (ADR-2 kapısı — taslak)

- **Bağlam:** Faz-1 `usort` + sabit 0.6/0.4 ağırlık kullanıyor (AIEngine.php:65,74);
  ağırlık veriyle doğrulanmadı (master §11/6). Üç seçenek masada.
- **Karar (öneri):** Önce **B (ölçülebilir skor)** — mevcut davranışı korur, ölçüm
  (Faz-4) ADR'sinden sonra **C (öğrenen)**.
- **Alternatifler:** (a) A kural — esnek ama ölçeklenmez; (b) B skor — mevcut kodla
  uyumlu; (c) C öğrenen — S6 verisi yokken **imkânsız**.
- **Sonuçlar:** D2 ⏸; Faz-4 ölçümü K4 kapısına bağlanır.

### Örnek-2 — Metrik eşikleri (ADR-3 kapısı — taslak)

- **Bağlam:** Offline metrik isimleri var (NDCG@K · Precision@K · coverage —
  scikit-learn kaynağı HTTP 200), **eşik yok**.
- **Karar (öneri):** Eşikler veri seti + ilk Faz-4 ölçümünden sonra atanır; şimdi
  sayı yazmak yasak.
- **Alternatifler:** (a) literatür eşiği — CoreMusic verisi için kanıtsız;
  (b) şimdi sabitle — ölçüm yok; (c) ertele → ADR-3 — **seçilen**.
- **Sonuçlar:** D3 ⏸; K5 yayım kapısı K4'e bağlı.

### Örnek-3 — KVKK kapsamı (K1 kapısı — taslak)

- **Bağlam:** Personalization profili kişisel veri işleyebilir; KVKK/GDPR kapsamı
  tanımsız (`K019-comilance` açık sorusu — master §12/1).
- **Karar (öneri):** Kapsam kararı verilmeden profil şeması **yazılmaz**, veri
  **toplanmaz**; `personalized:false` fail-safe ile başlanır.
- **Alternatifler:** (a) hemen topla — geri alınamaz maliyet (master §11/2);
  (b) anonimleştir — kapsam yine de tanımsız; (c) kararı beklemek — **seçilen**.
- **Sonuçlar:** D4 ⏸; API'de `personalized:false` şeffaflığı (master §18).

### Örnek-4 — K021 ID CONFLICT (D6 — taslak, üst karar)

- **Bağlam:** Klasör diskte `K021-recomenataion`; vault'ta 40+ `K020-recomenataion`
  referansı; Band-2 kapısı ADR-098 öncesi (registry §4).
- **Karar (öneri):** Disk adı **immutable**; dönüşüm yalnız üst karar + ADR-098 ile.
- **Alternatifler:** (a) klasörü adlandır — ~40 referans kırılır, yasak;
  (b) referansları toplu değiştir — onaysız toplu değişim yasak; (c) bekle —
  **seçilen** (⚠️ olarak taşınır).
- **Sonuçlar:** D6 ⏸; açık soru olarak master §12/5'te durur.

## §6. Ret/kapanan kararlar

| Konu | Karar | Kaynak |
|---|---|---|
| "4 katman / 6 adım" iki ayrı liste | **RET** → tek kanonik S1-S6 (2026-10-17) | master §4 düzeltme notu |
| "17 URL / 6×404" ilk sayım | **DÜZELTİLDİ** → 28 URL = 15+9+4 | master §14 (Truth Mode) |
| Eşikli yayım (ölçümsüz "iyi") | **RET** — yasak | master §11/1 + §9 |

## §7. Açık sorular

1. ⚠️ ADR-2/ADR-3 numaraları henüz gerçek dosya değil — kapı adı.
2. ⚠️ ADR-098 yazılmadan K021 ID kararı kapanmaz.
3. ⚠️ D4 KVKK kararı `K019-comilance` tarafında — kapsam güneşi çıkmadan profil yok.

---

**Authority:** Bayram Ali / Vault Steward
