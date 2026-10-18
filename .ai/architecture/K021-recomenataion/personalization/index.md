---
node_id: K021-recomenataion/personalization
node_type: subdomain
status: draft
parent_id: K021-recomenataion
children: []
depends_on:
  - K021-recomenataion
  - ranking
evidence:
  - "v2 §2.20 — personalization/ (alt dosya listesi yok → children: [])"
  - "shared/src/AI/AIEngine.php:49-57 — context/user_id/session_id/history bağlam okuması (2026-10-17)"
validation:
  - "2026-10-12 — şekil v2 §2.20 ile birebir"
  - "2026-10-17 — içerik sinyal/gizlilik/cold-start derinliği eklendi"
open_questions:
  - "Kişisel profil hangi veriden tutulacak, KVKK kapsamı ne? → VERIFY REQUIRED (K020-comilance açık sorusu)"
  - "Profil nerede saklanır (MySQL tablo / APCu / dosya)? → VERIFY REQUIRED (K007-data şema kapısı)"
  - "Kullanıcıya profil denetimi (görüntüleme/silme) sunulacak mı? → VERIFY REQUIRED (KVKK madde 15/17 benzeri haklar — kapsam kararı comilance'da)"
group: mimari
---


# K021-recomenataion/personalization — Kişiselleştirme Alt Bölgesi

**ELI15:** "Sıralamayı **bu kullanıcıya özel** nasıl hâle getiririz?" sorusunun alanı.

| Alan | Değer |
|---|---|
| `node_type` | `subdomain` · `children: []` |
| Sorumluluk | Kullanıcıya göre **uyarlama** (profil → sıralama girdisi F5) |
| Pipeline konumu | S1/S2'yi besler — profil, `ranking/`'in girdisidir (master §4) |
| Bağımlılık | `ranking` (uyarlama, sıralamaya girer) |
| Profil verisi | **tanımlı değil** → `⚠️ VERIFY REQUIRED` |
| Gizlilik sınırı | KVKK/GDPR kapsamı tanımsız → `⚠️ VERIFY REQUIRED` |
| Varsayılan davranış | profil yoksa **kişiselleştirme yok** (fail-safe) — süreç kuralı |

**Kapı:** veri kaynağı ve gizlilik kapsamı onaylanmadan profil şeması yazılmaz.

## §1 Kişiselleştirme girdileri — sinyal haritası

| # | Sinyal | Örnek | Kanıt/durum |
|---|---|---|---|
| P1 | Kullanıcı kimliği | `user_id` | **okunuyor** — `AIEngine.php:52` |
| P2 | Oturum | `session_id` | **okunuyor** — `AIEngine.php:53` |
| P3 | Çalma geçmişi | dinlenen parçalar | **bağlam olarak okunuyor** — `AIEngine.php:56` (`history`) · **kaynağı/log şeması yok** → `⚠️` |
| P4 | Tercih bağlamı | mood, genre | **okunuyor** — `AIEngine.php:54-55` |
| P5 | Açık tercihler | favori, beğeni, oy | **yok** → `⚠️` |
| P6 | Türetilmiş profil | tür vektörü, sanatçı affinity, dinleme süresi dağılımı | **yok** → `⚠️` (P3'ten türetilecek — tasarım ADR) |

> Repo kodu **kimliği ve geçmişi okuyor** ama **kalıcı profil üretmiyor** (382 satır
> okundu; profil tablosu/sınıfı yok). Bu bir **eksikliktir**, iddia değil.

## §2 Profil modeli seçenekleri (ADR-2 gündemi)

| Seçenek | Ne | Saklama | KVKK yükü |
|---|---|---|---|
| A. Oturum-içi | yalnız aktif oturumdaki tercih | bellek/APCu, oturum sonunda silinir | **en düşük** |
| B. Kalıcı özet | dönemsel özet vektör (ör. tür dağılımı) | MySQL (K007 şeması) | orta — silme hakkı gerekir |
| C. Ham geçmiş | tüm dinleme kaydı | MySQL | **en yüksek** — tam geçmişi silme/paketleme yükü |

> Seçenek seçimi **KVKK kapsam kararına (K1) bağlıdır** — kapsam tanımsızken A/B/C
> kararı verilemez; yalnızca A **varsayılan fail-safe** olarak süreç kuralıdır
> (profil yoksa kişiselleştirme yok).

## §3 Cold-start (yeni kullanıcı) stratejisi

| Durum | Davranış | Durum |
|---|---|---|
| Yeni kullanıcı (veri yok) | kişiselleştirme **kapalı** → genel/popüler/katalog turu | süreç kuralı (fail-safe) |
| Az verili kullanıcı | ağırlık kişiselleştirmeye doğru **yavaş** kayar (kademeli) | tasarım → `⚠️` (ADR-2) |
| Yeni içerik (az etkileşim) | içerik-bazlı skor (F1) öne çıkar | F1 mevcut (`AIEngine.php:61-62`) |
| Kullanıcı verisini sildi | profili ve özetlerini sil → cold-start'a döner | **kapı:** K1 kapsamında tanımlanacak |

## §4 Gizlilik ve uyumluluk kapıları (bağlayıcı)

| Kapı | Soru | Sahip | Durum |
|---|---|---|---|
| K1 — veri kapsamı | Bu profil kişisel veri midir, KVKK/GDPR kapsamı nedir? | `K020-comilance` | `⚠️ VERIFY REQUIRED` |
| K1a — hukuki dayanak | Veri işleme dayanağı nedir (açık rıza / sözleşme / meşru menfaat)? | `K020-comilance` | `⚠️ VERIFY REQUIRED` — **tahmin yazılmaz** |
| K1b — saklama süresi | Profil ne kadar süre tutulur, ne zaman imha edilir? | `K020-comilance` | `⚠️ VERIFY REQUIRED` |
| K1c — kullanıcı hakları | Görüntüleme/silme/düzeltme arayüzü var mı? | `K020-comilance` | `⚠️ VERIFY REQUIRED` |
| K2 — veri kaynağı | Profil hangi tablodan/olaydan beslenecek? | Vault Steward + `K007-data` | `⚠️ VERIFY REQUIRED` |

> **Süreç kuralı:** K1–K1c kapalı olmadan profil şeması **yazılmaz**, veri **toplanmaz**,
> kişiselleştirme **canlıya alınamaz** (master §9 ①).

## §5 Fail-safe davranış (bağlayıcı — süreç kuralı)

| Tetikleyici | Davranış |
|---|---|
| Profil yok / boş | kişiselleştirme **devre dışı** — sıralama genel girdiyle çalışır |
| Profil okuma hatası | kişiselleştirme **atlanır** — öneri hatasız döner (fail-open değil, **atlatma**) |
| Kullanıcı sildi | tüm özetler + örtük vektörler silinir → cold-start |
| KVKK kapsamı daraltıldı | kişiselleştirme **kapatılır**, sıralama korunur |

## §6 Sınır (bu alt bölgenin sahip olmadığı)

| Alan | Sahip | Not |
|---|---|---|
| Skor/çünkü üretimi | `[[../ranking/index]]` | personalization profil **verir**, sıralamaz |
| Metrik (kişiselleştirme kazancı) | `[[../evaluation/index]]` | kişiselleştirme-kişiselleştirmesiz karşılaştırması S6'da |
| Hukuki kapsam | `../K020-comilance/` | K1 kapıları |
| Şema/migration | `K007-data` | profil tablosu burada tanımlanmaz |
| Şifreleme/anahtar | `K009-security` | hassas alan şifrelemesi sınırı |

## §7 Kod referansları

| Konu | Dil | Dosya |
|---|---|---|
| Bağlam okuma + profil yoksa genel öneri (fail-safe) | PHP | `[[../recommendation-kod-ornekleri]]` §PHP · §TypeScript |
| Profil vektörü güncelleme (özet) | Python · Kotlin · Swift | aynı dosya |
| Silme/paket hakkı (KVKK akışı) | SQL · PowerShell | aynı dosya |

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | KVKK/GDPR kapsamı ve hukuki dayanak (K1a)? | `⚠️ VERIFY REQUIRED` |
| 2 | Profil modeli A/B/C seçeneklerinden hangisi? | `⚠️ VERIFY REQUIRED` — ADR-2 + K1 |
| 3 | Saklama süresi ve imha otomasyonu (K1b)? | `⚠️ VERIFY REQUIRED` |
| 4 | Kullanıcı hakları arayüzü (K1c)? | `⚠️ VERIFY REQUIRED` |
| 5 | Profil şeması hangi tabloda (K007 şeması)? | `⚠️ VERIFY REQUIRED` |

## §BAĞLANTILAR

| Hedef | Wiki-link | Göreli dosya yolu | İlişki |
|---|---|---|---|
| Program anayasası | `[[../recommendation-master]]` | `../recommendation-master.md` | S1–S6 + §7 sinyal haritası |
| Ranking | `[[../ranking/index]]` | `../ranking/index.md` | profil → F5 girdisi |
| Ölçüm | `[[../evaluation/index]]` | `../evaluation/index.md` | kişiselleştirme kazancı ölçümü |
| Kod örnekleri | `[[../recommendation-kod-ornekleri]]` | `../recommendation-kod-ornekleri.md` | profil/fail-safe uygulama referansı |
| Araştırma | `[[../recommendation-arastirma]]` | `../recommendation-arastirma.md` | KVKK/GDPR kaynakları |
| Klasör kapısı | `[[../index]]` | `../index.md` | çocuk kataloğu |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
