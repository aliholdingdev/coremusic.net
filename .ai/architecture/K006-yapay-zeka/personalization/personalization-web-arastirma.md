---
node_id: K006.4-personalization-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/AI/AIEngine.php L209–264 (skorlayıcılar boş gövde)"
  - "shared/src/Knowledge/MemorySystem.php L45 (K016 oturum hafızası)"
validation: "2026-10-12 · 8 benzersiz URL havuzdan, iddialar sayfa metniyle · status=verified"
open_questions:
  - "Kişiselleştirme verisi (tercih/oturum) hangi depoda tutulacak → UNKNOWN"
  - "KVKK rıza/ Silme hakkı kişiselleştirme profilinde nasıl uygulanacak → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K006.4-personalization — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **kişiselleştirme**
> ayağı. Tüm URL'ler 2026-10-12 doğrulanmıştır; yeni URL üretilmedi — yalnız havuz §2.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Kişiselleştirme bir öneri görevi midir, ayrı katman mı? | kapsam sınırı (K006.3 ile) | §2.6 (20) |
| Çevrimiçi/çevrimdışı kişiselleştirme nasıl kurulur? | repo'da 0 satır — aday çerçeve | §2.6 (21) |
| Kullanıcı-gizli geri bildirim (implicit feedback) nasıl işlenir? | tıklama/çalma verisi tipi | §2.6 (22–23) |
| Kullanıcı profili + oturum hafızası? | K016 MemorySystem sınırı | §2.6 (19 · 24–25) |
| Kişisel veri uyumluluğu (KVKK/GDPR)? | profil = kişisel veri | §2.9 (29 · 31) |
| İzleme/drift? | profil kalitesi ölçümü | §2.5 (10) |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.4 madde |
|---|---|---|---|
| 1 | https://recommenders-team.github.io/recommenders/intro.html | 5 ana görev: popülerlik · içerik tabanlı · hibrit · değerlendirme · **kişiselleştirme** | kapsam tanımı |
| 2 | https://librecommender.readthedocs.io/en/stable/ | Libre recommender — **Apache 2.0**, çevrimiçi/**çevrimdışı** kişiselleştirme, sürüm **1.5.2** | çerçeve adayı |
| 3 | https://github.com/recommenders-team/recommenders | recommenders — **21.928★**, **MIT** | referans çerçeve |
| 4 | https://github.com/NicolasHug/Surprise | Surprise — **6.805★**, **BSD-3** | algoritma + profil değerlendirme |
| 5 | https://surprise.readthedocs.io/en/stable/ | SVD, KNN, ölçüm metrikleri | gizli-geri-bildirim algoritması |
| 6 | https://www.evidentlyai.com/ | Evidently — ML izleme/değerlendirme/drift; **Apache 2.0**, **7.500+★** | profil drift'i |
| 7 | https://gdpr.eu/ | GDPR metin/özet portalı | kişisel veri gerekçesi |
| 8 | https://www.kvkk.gov.tr/Icerik/8547/Uretken-Yapay-Zeka-Rehberi | KVKK **Üretken Yapay Zekâ Rehberi** | TR uyum çerçevesi |

**Toplam K006.4 için: 8 benzersiz URL** (havuz §2.5 · §2.6 · §2.9).

---

## §3 İddia Ayrıntıları (sayfa metni dayanaklı)

| İddia | Dayanak URL | Ne söylüyor | K006.4'e etkisi |
|---|---|---|---|
| Kişiselleştirme bir ana görevdir | 1 | 5 görevden biri olarak sayılmış | kapsam K006.4 = kişiselleştirme |
| Çevrimdışı kişiselleştirme mümkündür | 2 | Apache 2.0 · sürüm 1.5.2 · online/offline | gizli geri bildirim adayı |
| Tüketim verisi "implicit feedback"tir | 3–5 | framework'ler kullanıcı-öğe etkileşiminden öğrenir | repo'daki çalma verisi tipi |
| Profil zamanla kayabilir | 6 | drift araç seti | profil sağlığı ölçümü |
| Kişiselleştirme profili = kişisel veri | 7–8 | GDPR/KVKK metinleri | rıza/silme zorunluluğu |

---

## §4 K006.4'e Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.4 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| Libre recommender | çevrimiçi/çevrimdışı kişiselleştirme | 2 | **kurulmadı** |
| recommenders çerçevesi | referans implementasyon | 3 | **kurulmadı** |
| Surprise (SVD/KNN) | kullanıcı-profil matrisi çıkarımı | 4–5 | **kurulmadı** |
| Evidently | profil drift izleme | 6 | **kurulmadı** |
| KVKK/GDPR uyum katmanı | rıza + silme + amaç sınırı | 7–8 | **yok** (kod 0 satır) |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` · `status: draft`).

---

## §5 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| Kişiselleştirme = kullanıcı matrisi | `getCollaborativeScores` L209–218 **her koşulda `[]`** — kullanıcı matrisi verisi yok | ⚠️ boşluk |
| İçerik profillemesi | `getContentBasedScores` L223–235 **`[]`** — özellik/etiket kaynağı yok | ⚠️ boşluk |
| Tercih girdisi | `getRecommendations($userPreferences, …)` L49–77 — tercihler **çağırıcıdan gelir**, depolanmaz | 🔶 istek-anında |
| Çeşitlilik (profil yorgunluğu karşıtı) | `applyDiversityFilter` L264 — **VAR** (tek gerçek kişiselleştirme-yan etki kuralı) | ✅ kısmi |
| Oturum hafızası | K016 `MemorySystem::setSession` L45 + 6 metot daha (MemorySystemInterface 7 imza) | ✅ var (K016 sahipliğinde) |
| Profil verisi/riza yönetimi | grep `consent|profile|gdpr|kvkk` src → **0** | ⚠️ boşluk |
| Kişisel veri uyumu | KVKK Üretken YZ Rehberi'ne karşılık kod **0 satır** | ⚠️ boşluk |

**Sonuç:** K006.4'ün tek gerçek karşılıkları: istek-anında gelen tercihler (L49) + çeşitlilik
filtresi (L264) + K016 oturum hafızası. **Kalıcı kullanıcı profili yoktur** — kişiselleştirme
bugün imkânsızdır (skorlayıcılar boş). Bu, K006.4'ün ana bulgusudur.

---

## §6 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | `recommenders.microsoft.com` — CRAWL_UNKNOWN_ERROR → `recommenders-team.github.io` ile değiştirildi | ✅ düzeltildi (havuz §6.2) |
| C2 | K006.4 "kişiselleştirme" der; repo'da kalıcı profil **0 satır** | ⚠️ **açık boşluk** (R-02) |
| C3 | Profil = kişisel veri; KVKK rıza/silme mekanizması **yok** | ⚠️ uyum riski (R-07) |
| C4 | MemorySystem K016'da — K006.4'ün profil sahipliği belirsiz | ⚠️ VERIFICATION REQUIRED |
| C5 | Libre/Surprise Python; K006 PHP | 🔶 dil sınırı — UNKNOWN |

---

## §7 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **8** |
| ✅ sayfa metniyle doğrulandı | 8 |
| Repo ile çapraz eşleşme | 2 (çeşitlilik filtresi · K016 oturum hafızası) |
| ⚠️ boşluk/çelişki | 5 (C2–C5 + boş skorlayıcılar) |
| UNKNOWN | 3 (profil deposu · sahiplik · dil sınırı) |
| Kurulu aday | 0 |

---

## §8 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.5 · §2.6 · §2.9 — 2026-10-12.
2. `shared/src/AI/AIEngine.php` L49–77 · L209–264 — 2026-10-12 disk okuma.
3. `shared/src/Knowledge/MemorySystem.php` L45 — 2026-10-12 disk okuma (K016).
4. `shared/src/**` grep (`consent|profile|gdpr|kvkk`) → 0 eşleşme, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 8/8 URL havuzdan doğrulandı, 2 çapraz eşleşme, 5 işaretli boşluk, 0 kurulu aday`
