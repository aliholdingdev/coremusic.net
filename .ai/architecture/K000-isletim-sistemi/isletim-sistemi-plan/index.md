---
node_id: K000-isletim-sistemi/isletim-sistemi-plan
node_type: subdomain
status: verify-required
parent_id: K000-isletim-sistemi
children:
  - index.md
  - isletim-sistemi-plan-kapsam.md
  - isletim-sistemi-plan-dogrulama.md
  - isletim-sistemi-plan-ornekler.md
  - zamanlama-scheduler
  - is-parcacigi-planlamasi
  - kaynak-dagilimi
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-runtime
evidence:
  - "https://docs.kernel.org/scheduler/index.html"
  - "https://man7.org/linux/man-pages/man7/pthreads.7.html"
  - "https://man7.org/linux/man-pages/man7/capabilities.7.html"
  - "home.coremusic.net/AGENTS.md §1 (Raspberry Pi 5 hedefi)"
validation:
  - "2026-10-13: kök 4 dosya ≥500/40-100 hizalandı; 3 alt-dal × 4 dosya = 12 yeni dosya"
  - "2026-10-13: 19 benzersiz URL HTTP 200 · envanter kesişimi 0 · 25 repo satırı Select-Object -Index ile teyit"
open_questions:
  - "Q3: kapsam = OS planlama mı, uygulama yol haritası mı? [UNKNOWN]"
  - "Plan1: scheduling içeriği kernel/runtime'da mı, burada mı? [UNKNOWN]"
group: mimari
---

# isletim-sistemi-plan

> **Kapsam (tek cümle):** `isletim-sistemi-plan`, OS planlama (scheduling) ekseninde, `kernel` ve `runtime` komşularına bağımlı, **uygulama yol haritası kapsam dışı** bir alt-dır; üç alt-dalı çerçeveler, mekanizmayı değil.

## Neden `verify-required`?

v2 ağacında düğümün adı vardır, tanımı yoktur. Kapsamı uydurmak zero-hallucination'a aykırıdır; bu yüzden kapsam dosyası (1) bilineni yazar, (2) bilinmeyeni `[UNKNOWN]` işaretler, (3) olası yorumu **aday kaynak** olarak sunar, (4) kararı üst otoriteye bırakır. Dallanma Q3'ün cevabı değildir — yalnız "kanıt neyi taşıyorsa onun etrafında yap" kararıdır (`isletim-sistemi-plan-kapsam.md §C`).

## Dosya indeksi (kök — Kademe-3)

| Dosya | Ne içerir |
|---|---|
| [`isletim-sistemi-plan-kapsam.md`](isletim-sistemi-plan-kapsam.md) | Kapsam sınırı (§A), 3 alt-dal envanteri (§B), Q3 karar kaydı (§C), bağımlılık grafiği (§D), sınır komşuları (§E), açık sorular (§F), dürüstlük kaydı (§G), üretim kaydı (§H) |
| [`isletim-sistemi-plan-dogrulama.md`](isletim-sistemi-plan-dogrulama.md) | 19 benzersiz URL HTTP 200 (§2), envanter kesişim denetimi (§1), 25 repo satırı `Select-Object -Index` teyitli (§3), ölçümler (§5), frontmatter denetimi (§6) |
| [`isletim-sistemi-plan-ornekler.md`](isletim-sistemi-plan-ornekler.md) | 24 gerçek repo kodu örneği (§1), kötü uygulama kataloğu (§2), politika matrisi (§3), repo'da olmayan dürüstlük kaydı (§4), alt-dal köprüsü (§5) |

## Alt-dal envanteri (Kademe-3 klasör · dosyalar Kademe-4)

| Alt-dal | İçerik dosyaları (3'er) | Eksen sorusu | Durum |
|---|---|---|---|
| [`zamanlama-scheduler/`](zamanlama-scheduler/index.md) | anayasa · surucu · ornekler | hangi iş, hangi sırayla CPU alır? | draft |
| [`is-parcacigi-planlamasi/`](is-parcacigi-planlamasi/index.md) | anayasa · surucu · ornekler | iş birimi nasıl kurulur, çekirdeğe nasıl oturtulur? | draft |
| [`kaynak-dagilimi/`](kaynak-dagilimi/index.md) | anayasa · surucu · ornekler | kaynak kime, ne kadar, ne zaman verilir? | draft |

Bağımlılık yönü tek yönlüdür (`plan` → `kernel` · `plan` → `runtime`); tam grafik ve ihlal kataloğu: `isletim-sistemi-plan-kapsam.md §D`.

## Sınır özeti (komşular)

| Komşu | Bu subdomainin ona bıraktığı | Almadığı |
|---|---|---|
| `isletim-sistemi-core` | süreç/kavram çerçevesi | süreç mekanizması tanımı |
| `isletim-sistemi-kernel` | politika çerçevesi ve ölçütler | kesme/lock, kuyruk/wakeup iç mekanizması |
| `isletim-sistemi-api` | uygulama katmanı kuyruk/öncelik örneğinin OS olmadığının notu | kullanıcı parçacığı API tasarımı |
| `isletim-sistemi-security` | zamanlama politikası güvenliği **ihtiyacının** tanımı | capability/rol/ACL modeli |
| `isletim-sistemi-observability` | ölçüm adımlarının listesi (latency · jitter · utilization) | metrik boru hattı, log şeması |
| `isletim-sistemi-testing` | doğrulama senaryosu ve eşikler | test kodu, CI kapısı |
| `isletim-sistemi-audio` | RT zamanlama sınıfının ses için anlamı (WASAPI/ASIO deadline) | zincir/ASIO/ring buffer tasarımı |
| `isletim-sistemi-turu` | OS zamanlayıcı farklarının varlığının işaret edilmesi | kurulum yolu, dağıtım yapılandırması |

## Açık sorular

| # | Soru | Durum | Kapanış koşulu |
|---|---|---|---|
| Q3 | Kapsam nedir (OS planlama / uygulama roadmap)? | **[UNKNOWN]** | Üst otorite kararı — `isletim-sistemi-plan-kapsam.md §C.4` |
| Plan1 | Scheduling içeriği ayrı yerde mi (kernel/runtime), burada mı? | **[UNKNOWN]** | `kernel`/`runtime` okunup sınırın teyidi — `zamanlama-scheduler-anayasa.md §8` |

Kapatılmaz: iki soru da disk kanıtıyla cevaplanamaz; cevap vermek uydurma olur.

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `isletim-sistemi-plan-kapsam.md` | Kapsam sınırı, alt-dal envanteri ve kapsam dışı listesi | Kademe-3 |
| kod | `isletim-sistemi-plan-ornekler.md` | Uygulama örnekleri, gerçek repo kodu ve politika karşılaştırması | Kademe-3 |
| kanit | `isletim-sistemi-plan-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-3 |

Alt-dal `index.md` dosyalarının üçü de `group: mimari` (Kademe-3); alt-dal içerik dosyaları: `-anayasa` → `mimari` · `-surucu` → `mimari` · `-ornekler` → `kod` (Kademe-4).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode