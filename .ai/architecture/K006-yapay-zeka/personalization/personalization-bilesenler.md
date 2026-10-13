---
node_id: K006.4-personalization-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/src/AI/AIEngine.php L49–77 · L209–264 (2026-10-12 tam okuma)"
  - "shared/src/Knowledge/MemorySystem.php L45 (2026-10-12; K016 sahipliğinde)"
  - "shared/src/Contracts/AI/MemorySystemInterface.php L13 (7 imza)"
validation: "2026-10-12 · kalıcı profil bileşeni = 0 satır; 2 kısmi bileşen (çeşitlilik + oturum hafızası) kaydedildi · status=verified"
open_questions:
  - "Kullanıcı profili K006.4'ün mü, K016 MemorySystem'in mi sahipliğinde → ⚠️ VERIFICATION REQUIRED"
  - "Profil silme/riza (KVKK) akışı nasıl işlenecek → UNKNOWN"
group: mimari
---

# K006.4-personalization — Bileşenler

> K006.4'ün **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. **Kalıcı kullanıcı profili yoktur**; iki kısmi bileşen (çeşitlilik filtresi +
> K016 oturum hafızası) kaydedilmiştir.

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| K006.4'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — 7 PHP, "personalization" adında dosya yok |
| Kalıcı kullanıcı profili | **0 satır** | grep `profile|consent` src → 0 (2026-10-12) |
| İşbirlikçi skorlayıcı | **boş gövde** (`getCollaborativeScores` L209–218 → `[]`) | §2.1 |
| İçerik-tabanlı skorlayıcı | **boş gövde** (`getContentBasedScores` L223–235 → `[]`) | §2.2 |
| Çeşitlilik filtresi | ✅ **var** (`applyDiversityFilter` L264) | §2.3 |
| Oturum hafızası (K016) | ✅ **var** (`MemorySystem::setSession` L45 · interface 7 imza) | §3 |
| Sözleşme karşılığı | 1 (`MemorySystemInterface` L13 — **K016 sahipliğinde**) | §3 |

---

## §2 Gerçek Kod Bileşenleri

### §2.1 `AIEngine::getCollaborativeScores(array $userPreferences): array` — L209–218 (private) — **boş**

| Satır | Davranış |
|---|---|
| 211–218 | iki dalda da `return [];` — kullanıcı-öğe matrisi / geçmiş veri kaynağı **yok** |

### §2.2 `AIEngine::getContentBasedScores(array $userPreferences): array` — L223–235 (private) — **boş**

| Satır | Davranış |
|---|---|
| 225–235 | `return [];` — parça özellik/etiket veri kaynağı **yok** |

**Bulgu:** kişiselleştirmenin iki girdisi de **her koşulda boştur**; `getRecommendations`
(L49–77) bu yüzden yalnız `combineScores` + eşik + sıralama yapar — kullanıcıya özel
**hiçbir davranış yoktur**.

### §2.3 `AIEngine::applyDiversityFilter(array $recommendations): array` — L264 (private)

| Satır | Davranış |
|---|---|
| 266–285 (yaklaşık bölge) | sonuç listesine çeşitlilik uygular |

**Değer:** "aynı tür üst üste gelmesin" kuralının tek somut karşılığı — kişiselleştirme
yan etkisi, profil değil.

### §2.4 Tercih girdisi — `getRecommendations(array $userPreferences, array $context = [])` L49–55

| Satır | Davranış |
|---|---|
| 49 | tercihler **çağırıcıdan** gelir — depolanmaz, profilleştirilmez |
| 53–55 | boş tercih → `[]` |

---

## §3 K016 Yüzeyi (oturum hafızası — dış sahiplik)

| Bileşen | Konum | İmza | K006.4 ilişkisi |
|---|---|---|---|
| `MemorySystem::setSession` | L45 | 1/7 | oturum bazlı veri saklama — profilin **en yakın** karşılığı |
| `MemorySystemInterface` | Contracts L13 | 7 imza | arayüz K016'da — K006.4 **sahip değildir** |

**Sınır kaydı:** oturum (K016) ≠ kalıcı profil (K006.4). Kalıcı profil için bugün **hiçbir
bileşen yoktur**.

---

## §4 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.4 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| Kalıcı kullanıcı profili | **0 satır** | grep → 0 |
| Tercih toplama/depolama | **0 satır** | tercihler istek gövdesinden gelir (L49) |
| İşbirlikçi filtreleme (matris çıkarımı) | **0 satır** (boş gövde) | L209–218 |
| İçerik profillemesi | **0 satır** (boş gövde) | L223–235 |
| Oturumlar arası profil birleştirme | **0 satır** | grep → 0 |
| KVKK rıza/silme akışı | **0 satır** | grep `consent|gdpr|kvkk` → 0 |
| Çeşitlilik kuralı | ✅ **var** | L264 |
| Oturum hafızası | ✅ **var (K016)** | MemorySystem L45 |

---

## §5 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.3-recommendation | kişiselleştirme, önerinin **girdi katmanıdır** (profil→skor); bugün ikisi de boş skorlayıcıya dayanır |
| K016-AI (dış) | MemorySystem oturum verisi → profil kaynağı olabilir; **sahiplik 3-way açık soru** |
| K006.8-evaluation | profil kalitesi/drift ölçümü K006.8'e düşer — bugün metrik yok |
| K007-data (dış) | profil tablosu BCNF şema ile Data Engineer sahipliğinde olmalı (tasarım iddiası) |

---

## §6 Kaynaklar

1. `shared/src/AI/AIEngine.php` — L49–77 · L209–264 tam okuma, 2026-10-12.
2. `shared/src/Knowledge/MemorySystem.php` — L45, 2026-10-12 (K016).
3. `shared/src/Contracts/AI/MemorySystemInterface.php` — L13 (7 imza), 2026-10-12.
4. `shared/src/**` grep (`profile|consent|gdpr|kvkk`) → 0 eşleşme, 2026-10-12.
5. `personalization-web-arastirma.md` §5 — çapraz bağ.

---

`status: verified` · `validation: 2026-10-12 · 2 boş skorlayıcı + 1 filtre + 1 K016 hafıza kaydedildi, 8 beklenen bileşenden 6'sı 0 satır`
