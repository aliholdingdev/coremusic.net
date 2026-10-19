---
title: "K001-donanım — Çelişkiler ve Açık Kararlar Kaydı"
type: analysis-report
node_id: K001-donanım/raporlar/celiskiler-ve-acik-kararlar
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
  - K002-electroncis
evidence:
  - "Görev girdisindeki 8 çelişki (C-01…C-08) — bu raporda tamamı kayıtlıdır, hiçbiri gizlenmemiştir (§3–§5)"
  - "Oturum-tespiti çelişkileri C-09…C-12 bu oturumun disk ölçümü ile tespit edildi (§6)"
  - "Tur-2b yeni çelişkiler C-13…C-15 paralel Vault Steward kapanışları sonrası disk ölçümü ile eklendi (§7)"
  - "Her çelişki için: dosya yolu + satır numarası (veya 'dosya diskte YOK' kanıtı) — §3–§7 tabloları"
  - "Disk ölçümü 2026-10-13 (tur-2/2b): anayasa 518 · K000 index 13 children + '12 düğüm' validation · ADR-064 328 satır accepted/ · ADR-061 diskte YOK"
validation:
  - "15 çelişkinin 15'i gerçek disk kanıtı ile eşleştirildi; hiçbiri 'duydum' düzeyinde değil"
  - "Tur-2b kapanışları (C-06 · C-10) canlı ağaçta yeniden ölçülerek doğrulandı — bu oturumun işi olarak DEĞİL, paralel Vault Steward kapanışı olarak kaydedildi"
  - "Görev girdisi çelişkileri (C-01…C-08) ile tur-2 disk durumu arasındaki farklar §8'de ayrıca tablolandı (8 satır)"
  - "Hiçbir çelişki bu raporda Vault Steward onayı olmadan 'çözülmüş' işaretlenmedi"
open_questions:
  - "C-01…C-15'in hangileri kapanabilir (dosya değişikliği = D) vs karar gerektirir (K)? — §9 karar matrisi"
  - "C-08 Q2b: ADR-064 başlığı hâlâ '11 vs 13' sorusunu canlı tutuyor — başlık düzeltme kararı Vault Steward'ındır"
  - "C-13: K000/electroncis mi, K002-electroncis mi bağlayıcıdır? — sınır kararı (ADR düzeyinde)"
date: 2026-10-13
updated: 2026-10-13
group: kanit
---

# K001-donanım — Çelişkiler ve Açık Kararlar Kaydı

## §1 Bu raporun rolü (ELI15)

Vault'ta "iki yer iki farklı şey söylüyor" durumlarının **tamamını** tek yerde
toplar. Kural basit: bir çelişki varsa sessizce düzeltilmez — önce **iki tarafın
da gerçek kanıtı** yazılır (dosya + satır), sonra kararı Vault Steward verir.
Bu rapor 8 görev-girdisi çelişkisini (C-01…C-08), 4 oturum-tespiti çelişkisini
(C-09…C-12) ve 3 tur-2b çelişkisini (C-13…C-15) kaydeder. Çözüm uygulamaz;
gizleme yapmaz.

## §2 Çelişki kayıt formatı

Her kayıt: **ID** · konu · taraf-A (iddia + kanıt) · taraf-B (iddia + kanıt) ·
etki · çözüm tipi (dosya düzeltmesi = D / karar = K) · durum.

**Durum sözlüğü:**

| Durum | Anlam |
|---|---|
| **AÇIK** | Çelişki canlıdır; henüz hiçbir taraf düzeltilmedi |
| **KISMEN** | Bazı satırlar işaretlendi/not düşüldü; esas çelişki hâlâ duruyor |
| **KAPANDI** | Canlı ağaçta çelişki artık yok — yeniden ölçülerek doğrulandı (tur-2b) |
| **KAYITLI** | Bilgi düzeyinde kayıt; aksiyon gerekmez (görev girdisi hatası vb.) |

**Tur-2b notu (2026-10-13):** Paralel bir Vault Steward oturumu canlı ağaçta
kapanışlar yaptı (C-06 · C-10). Bu kapanışlar **bu raporun işi değildir** — bu
rapor yalnızca disk gerçeğini yeniden ölçerek kaydeder. Gizleme yasağı gereği
kapanan çelişkiler silinmez, durumları güncellenir.

## §3 Görev girdisi çelişkileri C-01…C-04 (K001 içi)

### C-01 — anayasa "çocuk sayısı 4" vs 5 kademe-2 çocuk

| Taraf | İddia | Kanıt |
|---|---|---|
| A | "Çocuk sayısı 4" | `donanım-anayasa.md:46` (tur-2b'de hâlâ aynı metin) |
| A2 | "C9: 4 kademe-2 çocuk" (kendi içinde tekrar) | `donanım-anayasa.md:249` |
| B | K001 kökünde 5 kademe-2 klasör var | `donanım-api/` · `donanım-core/` · `donanım-devices/` · `donanım-kernel/` · `donanım-type/` (Get-ChildItem 2026-10-13) |
| B2 | K001 kök index.md children = 5 | `K001-donanım/index.md:6-11` |

Etki: anayasa sayısal olarak yanlış (iki yerde: :46 ve :249); yeni child
(devices) anayasaya işlenmemiş. devices dalı ADR-064 ile açılmıştır
(`donanım-devices/index.md` ADR-064 evidence taşır).
Çözüm tipi: **D** (anayasa revizyonu :46 + :249 — APPROVAL REQUIRED).
Durum: **AÇIK** (tur-2b'de her iki satır da eski metnini koruyor — yeniden ölçüm).

### C-02 — anayasa "9 bağlam PROPOSED" vs "3 alt bağlam"

| Taraf | İddia | Kanıt |
|---|---|---|
| A | 9 bağlam genişleme (display·power·cables·network·memory·cpu + mevcut 3) — PROPOSED, APPROVAL REQUIRED | `donanım-anayasa.md:195` · `:250` |
| A2 (tur-2b) | :195 artık "✓ CEVAPLANDI" notu taşır — tur-2b'de kısmi çözüm | `donanım-anayasa.md:195` |
| A3 (tur-2b) | :250 PROPOSED notu hâlâ duruyor (kapanış yok) | `donanım-anayasa.md:250` |
| B | "donanım-anayasa.md §3 — 3 alt bağlam" | `donanaım-type/index.md:21` (evidence satırı) |

Etki: anayasa kendi içinde 3 ve 9 sayılarını farklı yerlerde taşır; type indeksi
eski sayıya atıf yapar.
Çözüm tipi: **D** (anayasa §3 + indeks evidence satırı güncellemesi).
Durum: **KISMEN** (tur-2b: :195'e "✓ CEVAPLANDI" notu düşüldü — bu bir
işaretlemedir, çelişkinin tamamı kapanmamıştır; :250 hâlâ PROPOSED taşır).

### C-03 — type-kapsam "yalnız 3 bağlam" (STALE)

| Taraf | İddia | Kanıt |
|---|---|---|
| A | 10 alt bağlam açıldı | `donanaım-type/index.md:6-16` (children: 10 satır) · diskte 10 klasör |
| B | "yalnız 3 bağlam" | `donanaım-type-kapsam.md:77-78` · `:95` · `:135` |
| B2 (tur-2b) | :135 artık "STALE" olarak işaretli | `donanaım-type-kapsam.md:135` |
| B3 (tur-2b) | :77-78 ve :95 hâlâ işaretsiz eski metin | `donanaım-type-kapsam.md:77-78` · `:95` |

Etki: kapsam dosyası 7 yeni bağı tanımıyor; okur çelişir. Tur-2b bir satırı
işaretledi (:135) ama iki satır (:77-78 · :95) hâlâ açık.
Çözüm tipi: **D** (kapsam revizyonu — APPROVAL REQUIRED; :135 işaretli, diğer
iki satır işaretsiz).
Durum: **KISMEN** (1/3 satır işaretli, 2/3 hâlâ AÇIK).

### C-04 — M6 "bağlam AÇILMAZ" kuralı vs 7 yeni bağlam

| Taraf | İddia | Kanıt |
|---|---|---|
| A | M6: "kapsam dışı — bağlam AÇILMAZ" | `donanaım-type-kapsam.md:284` |
| B | 7 yeni bağlam açıldı (display·power·cables·network·memory·cpu·gpu) | disk: `donanaım-type/` altında 10 klasör |

Etki: kural metni yeni gerçeğe aykırı; "açılmaz" kuralı ihlal edilmiş görünüyor
(oysa ADR-102 ile PROPOSED olarak açıldı — kural ile eylem arasında bağ eksik).
Çözüm tipi: **K** (M6 kuralının ADR-102 ile istisnasının yazılıp yazılmayacağı).
Durum: **AÇIK** (tur-2b'de değişiklik yok — yeniden ölçüm).

## §4 Görev girdisi çelişkileri C-05…C-06 (registry/atıf)

### C-05 — architecture/index.md "K002 hiç yok" vs K002-electroncis 16 md

| Taraf | İddia | Kanıt |
|---|---|---|
| A | "K002 hiç yok" · "K002* dizini 0" | `.ai/architecture/index.md:94` · `:119` |
| B | K002-electroncis: 16 md, 3 dizin | Get-ChildItem 2026-10-13 · aynı dosyanın :583/:682 satırlarında K002 açılış kayıtları (8→15 dosya ölçümleri) var |

Etki: registry dosyası kendi içinde de çelişir (:94/:119 vs :583/:682).
Çözüm tipi: **D** (registry düzeltmesi — beyazliste dışı bu oturumda).
Durum: **AÇIK** (tur-2b'de registry paralel ajan tarafından düzenlendi ama bu
satırların kapanışı doğrulanmadı — ⚠️ VERIFICATION REQUIRED: :94/:119 yeniden
okunmadı; kapanış iddiası yazılmaz).

### C-06 — K000 kademe-2 listesinde os-donanim-tespiti YOK

| Taraf | İddia | Kanıt |
|---|---|---|
| A | K000 kademe-2 listesi 12 child, `os-donanim-tespiti` YOK | `K000-isletim-sistemi/index.md:6` (eski okuma, tur-1) |
| B | Dal diskte var ve 4/4 dosya tam | `K000-isletim-sistemi/os-donanim-tespiti/` — index 96 · kapsam 503 · dogrulama 535 · ornekler 515 |
| C (tur-2b kapanış kanıtı) | K000 index:6 artık 13 child taşır ve `os-donanim-tespiti` dâhildir | `K000-isletim-sistemi/index.md:6` (13-child inline array — yeniden ölçüm 2026-10-13) |

Ek kanıt: dalın kendi metni durumu eski kabul eder — `os-donanim-tespiti-kapsam.md:331`
"§10.1 v2 mini-ağaç eksikliği — PROPOSED — APPROVAL REQUIRED" (bu metin hâlâ
duruyor olabilir — ⚠️ VERIFICATION REQUIRED).
Çözüm tipi: **D** (K000 index children'ına satır ekleme — **uygulandı**, tur-2b).
Durum: **KAPANDI** (tur-2b — paralel Vault Steward kapanışı; bu rapor yalnızca
yeniden ölçümle doğruladı).

### C-06b (ilişkili yeni bulgu — bkz. C-15) — K000 validation sayımı geride

C-06 kapanırken `K000-isletim-sistemi/index.md:8` validation satırı "12 düğüm
sayıldı" metnini hâlâ taşıyor (13 child'a rağmen). Bu yeni çelişkidir → C-15.

## §5 Görev girdisi çelişkileri C-07…C-08 (K002/ADR)

### C-07 — K002 "5 alt-konu" iddiası vs 3 dizin

| Taraf | İddia | Kanıt |
|---|---|---|
| A | 5 alt-konu: devre-ve-analog · dijital-ve-mikroislemci · guc-yonetimi · **sensor-ve-akteyici** · **imalat-ve-test** | `K002-electroncis/index.md:56-64` |
| B | Diskte yalnız 3 dizin | `K002-electroncis/` altında: `devre-ve-analog` · `dijital-ve-mikroislemci` · `guc-yonetimi` (Get-ChildItem 2026-10-13) |

Etki: 2 alt-konu ya üretimde ya da iddia fazlası. İlgili bulgu: K002'nin
`guvenli-rezervasyon` ve `test-onceliklileri` alt-dizinleri **dizin olarak var
ama md yok** (tur-envanteri §13.3) — bu, "5 alt-konu"nun kısmen iskelet
olduğunu gösterir.
Çözüm tipi: **K** (üretim devam ediyor mu, satır silinecek mi kararı).
Durum: **AÇIK** (tur-2b'de değişiklik yok).

### C-08 — ADR-064 "13/11/9/7" servis sayısı + ADR-061 atıfı

| Taraf | İddia | Kanıt |
|---|---|---|
| A | 4 farklı servis sayısı (13 · 11 · 9 · 7) başlıkta bir arada | `accepted/ADR-064-electronics-platform-architecture.md` title |
| B (tur-2b çözücü kanıt) | ADR-064 authority (b): "13 servis DOĞRULANAMADI (hayalet iz keys.md:163) → **bağlayıcı = 11 alan servisi** (ADR-039 §2.1)" | `ADR-064-electronics-platform-architecture.md` authority bloğu |
| C (kalan açık) | Başlık hâlâ "13 vs 11" sorusunu canlı tutuyor | ADR-064 title (başlık düzeltilmedi) |

Ek: ADR-064:19 → ADR-061'e atıf; ADR-061 **diskte YOK**
(`.ai/.decisions/index.md:44` silinme kaydı; yedek
`C:\temp\opencode\vault-backups\ADR-061-electronics-architecture.md.bak`).
Görev girdisi ayrıca ADR-062'yi "electronics platform" sanıyordu; tur-2 ölçümü
**ADR-062 = DSP Pipeline Architecture (389 sat)**, electronics platform =
**ADR-064 (328 sat)** olduğunu kanıtladı.
Çözüm tipi: **K** (gövde çözüldü — sayı bağlayıcısı 11; kalan: başlık düzeltme
kararı + ADR-061 atıf kararı).
Durum: **AÇIK** (gövde bağlamında KISMEN aydınlatıldı; başlık stale — Q2b;
karar Vault Steward'ındır).

## §6 Oturum-tespiti çelişkileri C-09…C-12 (bu turun yeni bulguları)

### C-09 — anayasa "482" vs 517/518 (görev girdisi ölçüm farkı)

Görev girdisi anayasa'yı 482 satır olarak verdi; tur-1 ölçümü 517, tur-2 ölçümü
**518** buldu. Fark gizlenmez: görev girdisi bir önceki oturumun ölçümüdür,
dosya o tarihten beri büyümüştür (+36 satır). Kanıt: Get-Content sayımı
(2026-10-13, iki tur). Tip: **bilgi** (düzeltme gerekmez — sayım güncellemesi).
Durum: **KAYITLI**.

### C-10 — gpu-ornekler "BEKLENIYOR" vs dosya var (525)

| Taraf | İddia | Kanıt |
|---|---|---|
| A | "gpu-ornekler.md BEKLENIYOR — 22:30 itibarıyla diskte YOK" | `donanaım-type/index.md:93` (eski okuma, tur-1) |
| B | Dosya diskte var, 525 satır | Get-Content sayımı 2026-10-13 (tur-2) |
| C (tur-2b kapanış kanıtı) | `donanaım-type/index.md:94` artık "gpu-ornekler … 4/4 tamam" metnini taşır | `donanaım-type/index.md:94` (yeniden ölçüm 2026-10-13) |

Etki: indeks satırı stale idi; tur-2b'de indeks güncellendi.
Tip: **D** (indeks düzeltmesi — **uygulandı**, tur-2b).
Durum: **KAPANDI** (tur-2b — paralel Vault Steward kapanışı; yeniden ölçümle
doğrulandı).

### C-11 — ADR-061 yedek yolu iddiası vs gerçek yedek

| Taraf | İddia | Kanıt |
|---|---|---|
| A | Yedek `C:\temp\opencode\vault-backup-2026-10-12\decisions-adr061\` altında | `.ai/architecture/index.md:44` (atıf metni) |
| B | Bu yol diskte YOK; gerçek yedek `C:\temp\opencode\vault-backups\ADR-061-electronics-architecture.md.bak` + `adr-061.md.bak` | Get-ChildItem 2026-10-13 |

Tip: **D** (registry atıf yolu düzeltmesi). Durum: **AÇIK** (tur-2b'de
doğrulanmadı — ⚠️ VERIFICATION REQUIRED: registry bu oturumda paralel ajan
tarafından düzenlendi, :44 yeniden okunmadı).

### C-12 — kök `donanım-kapsam.md` iddiası vs diskte YOK

Görev girdisi K001 kökünde `donanım-kapsam.md` dosyasını andı; tur-2 ölçümünde
kökte 10 dosya vardır ve `donanım-kapsam.md` **bunlar arasında yok**
(anayasa · kararlar · bagimliliklar · dogrulama · riskler · test-stratejisi ·
kod-ornekleri · evrensel-dil · web-arastirma · index). Tip: **bilgi**
(görev girdisi hatası — dosya hiç var olmamış olabilir). Durum: **KAYITLI**.

## §7 Tur-2b yeni çelişkileri C-13…C-15 (paralel kapanışların artığı)

> Bu üç çelişki, tur-2b paralel Vault Steward kapanışlarından sonra yapılan
> yeniden ölçümden çıkmıştır. Hiçbiri gizlenmemiştir; tam kanıt kartları aşağıda.

### C-13 — iki-köklü electroncis: K000/electroncis (12 md) vs K002-electroncis (kök 6 md)

| Taraf | İddia | Kanıt |
|---|---|---|
| A | `K000-isletim-sistemi/electroncis/` altında 12 md — 2 alt-dalin kendi anayasa dosyaları dâhil | Get-ChildItem 2026-10-13 (tur-2b) — dizin: index · anayasa · kapsam · dogrulama · ornekler · bagimliliklar + alt-dal md'leri |
| B | `K002-electroncis/` ayrı bir kademe-2 kökü olarak 6 md taşır | `K002-electroncis/index.md` + 5 kök md (index 100 · master 505 · kapsam 507 · dogrulama 507 · ornekler 505 · bagimliliklar 501) |
| Çelişki | "Electronics" domaini iki ayrı kökte iki kez tanımlanıyor — hangisi bağlayıcı? | K000 index:6 13-child listesinde electroncis dâhil · ADR-102 §6 C2'de K002-altyapi kayıtlı |

Etki: iki anayasa dosyası iki farklı kökte aynı domaini tanımlar; gelecekteki
güncelleme birinde unutulursa yeni çelişki doğar.
Çözüm tipi: **K** (sınır kararı — hangi kök SSOT? ADR düzeyinde).
Durum: **AÇIK**.

### C-14 — `K002-altyapi` kök adı: ADR-102 §6 C2 kaydı vs disk gerçeği

| Taraf | İddia | Kanıt |
|---|---|---|
| A | K000 kök `index.md:7` `depends_on: [K002-altyapi]` taşır | `K000-isletim-sistemi/index.md:7` |
| B | ADR-102 §6 C2 de bu bağı kaydeder (karar açık bırakılmış) | `draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md` §6 C2 |
| C | Diskte `K002-altyapi` adında dizin YOK (K002 = electroncis) | Get-ChildItem 2026-10-13 |

Etki: `K002-altyapi` bir "placeholder kök adı" olabilir (altyapı alt sistemleri
için ayrılmış) — ama diskte karşılığı yok; bağımlılık satırı havada.
Çözüm tipi: **K** (isim kararı — K002-altyapi gerçek bir kök mü, yoksa
gelecek için ayrılmış isim mi?).
Durum: **AÇIK** (ADR-102 §6 C2 ile çift kayıtlı — tek yerde çözülmeli).

### C-15 — K000 index validation "12 düğüm" vs 13 child (+ anayasa C9 stale)

| Taraf | İddia | Kanıt |
|---|---|---|
| A | K000 index validation: "12 düğüm sayıldı (124 md / 39.099 satır)" | `K000-isletim-sistemi/index.md:8` |
| B | K000 index children = 13 (tur-2b ekleme sonrası) | `K000-isletim-sistemi/index.md:6` |
| İlişkili | anayasa:249 C9 "4 kademe-2 çocuk" — C-01 ile aynı stale metin | `donanım-anayasa.md:249` |

Etki: C-06 kapanırken children güncellendi ama validation sayımı güncellenmedi —
**kapanışın artığı**. "124 md / 39.099 satır" sayısı da artık 13 düğümle
uyuşmuyor olabilir (⚠️ VERIFICATION REQUIRED: 124 md sayısı yeniden sayılmadı).
Çözüm tipi: **D** (validation satırı yeniden sayım + güncelleme — APPROVAL REQUIRED).
Durum: **AÇIK**.

## §8 Durum özeti — 15 çelişki (tur-2b sonrası)

| ID | Konu | Tip | Durum |
|---|---|---|---|
| C-01 | anayasa çocuk 4 vs 5 (:46 · :249) | D | AÇIK |
| C-02 | anayasa 3 vs 9 bağlam (:195 CEVAPLANDI · :250 PROPOSED) | D | KISMEN |
| C-03 | type-kapsam "yalnız 3" STALE (:135 işaretli · :77-78·:95 açık) | D | KISMEN |
| C-04 | M6 "bağlam AÇILMAZ" vs 7 açılma (:284) | K | AÇIK |
| C-05 | registry "K002 hiç yok" STALE (:94·:119) | D | AÇIK ⚠️ |
| C-06 | K000 index os-donanim-tespiti YOK | D | **KAPANDI** (tur-2b) |
| C-07 | K002 5 alt-konu vs 3 dizin | K | AÇIK |
| C-08 | ADR-064 13/11/9/7 + ADR-061 atıfı — gövde 11 bağladı, başlık stale (Q2b) | K | AÇIK |
| C-09 | anayasa 482 vs 518 (ölçüm farkı) | bilgi | KAYITLI |
| C-10 | gpu-ornekler "BEKLENIYOR" vs dosya var | D | **KAPANDI** (tur-2b) |
| C-11 | ADR-061 yedek yolu iddiası vs gerçek yedek | D | AÇIK ⚠️ |
| C-12 | kök `donanım-kapsam.md` diskte YOK | bilgi | KAYITLI |
| C-13 | iki-köklü electroncis (K000 12 md vs K002 6 md) | K | AÇIK (NEW) |
| C-14 | K002-altyapi placeholder kök (index:7 + ADR-102 §6 C2) | K | AÇIK (NEW) |
| C-15 | K000 validation "12 düğüm" vs 13 child (+ anayasa C9 stale) | D | AÇIK (NEW) |

Durum dağılımı: **2 KAPANDI** (C-06 · C-10) · **2 KISMEN** (C-02 · C-03) ·
**2 KAYITLI** (C-09 · C-12) · **9 AÇIK** (C-01 · C-04 · C-05 · C-07 · C-08 ·
C-11 · C-13 · C-14 · C-15). ⚠️ işaretli 2 kayıt (C-05 · C-11) paralel ajan
düzenlemesi sonrası yeniden okunmadı — kapanış iddiası yazılmadı.

## §9 Görev girdisi ↔ tur-2 fark tablosu (8 satır)

| # | Görev girdisi | Tur-2 gerçeği |
|---|---|---|
| 1 | anayasa 482 | **518** (C-09) |
| 2 | kök `donanım-kapsam.md` var | **YOK** (C-12) |
| 3 | gpu "0 dizin, üretimde" | **4 dosya** (index 99 · kapsam 585 · dogrulama 503 · ornekler 525) |
| 4 | network-ornekler 156 | **511** ✓ eşik aşıldı |
| 5 | memory 3×290'lar (295/286/296) | **504/515/503** ✓ |
| 6 | cpu-ornekler "500 altı" | **504** ✓ (görev girdisi kendi içinde de çelişkiliydi: "504 (500'ü geçti)") |
| 7 | K004-dsp "22 dosya" | **69 md** (12 alt klasör + 5 kök) |
| 8 | kernel-dogrulama 78 | **511** ✓ TAMAM |

## §10 Karar matrisi — hangisi karar, hangisi düzeltme?

| Çözüm tipi | Çelişkiler | Karar mercii |
|---|---|---|
| **D** — dosya düzeltmesi (onaylı, AÇIK/KISMEN) | C-01 · C-02 · C-03 · C-05 · C-11 · C-15 | Vault Steward onayı ile uygulanır; bu oturumda uygulanmadı |
| **D** — uygulanmış (tur-2b, paralel ajan) | C-06 · C-10 | Kapanış doğrulandı; ek aksiyon yok |
| **K** — karar gerekir | C-04 · C-07 · C-08 · C-13 · C-14 | Vault Steward (ADR düzeyinde) |
| **bilgi** — kayıt yeterli | C-09 · C-12 | Aksiyon yok |

**Karar kümeleri (öncelik sırası):**

1. **Sınır kararları (K, yüksek etki):** C-13 (electroncis SSOT kökü) + C-14
   (K002-altyapi ismi) — ikisi ADR-102 §6 C2 ile bağlantılıdır; tek ADR'de
   çözülmeleri önerilir.
2. **Başlık/istisna kararları (K, orta etki):** C-04 (M6 istisnası) · C-07
   (K002 alt-konu iskeleti) · C-08 Q2b (ADR-064 başlık düzeltme).
3. **Toplu düzeltme (D, düşük risk):** C-01 · C-02 · C-03 · C-15 — dördü de
   "sayı/eski metin güncelleme" sınıfındadır; tek oturumda uygulanabilir.
4. **Registry düzeltmeleri (D, beyazliste dışı):** C-05 · C-11 — architecture
   index dosyası bu oturumun beyazlistesi dışındadır; Vault Steward uygular.

## §11 Kanıt dökümü — kritik çelişkilerin tam transkriptleri (tur-2b)

> Bu bölüm, §3–§7'deki kartların arkasındaki ham okumaları taşır: "şu satırda
> şu yazıyor" kanıtının birebir kopyası. Okur, kartın iddiasını transkriptle
> karşılaştırabilir.

### §11.1 C-01 transkript — anayasa :46 ve :249

| Satır | Ham metin (okuma, 2026-10-13) | Yorum |
|---|---|---|
| `donanım-anayasa.md:46` | "Çocuk sayısı 4" (kademe-2 sayımı) | Sayısal iddia — 5 çocuk diskte |
| `donanım-anayasa.md:249` | "C9 — 4 kademe-2 çocuk" (kontrol listesi) | Aynı yanlış sayının tekrarı |

İki satır da tur-2b sonrası eski metnini koruyor (yeniden ölçüm). Düzeltme
iki satırı birden kapsamalıdır.

### §11.2 C-02 transkript — anayasa :195 ve :250

| Satır | Ham metin (okuma, 2026-10-13) | Yorum |
|---|---|---|
| `donanım-anayasa.md:195` | 9 bağlam maddesi + "✓ CEVAPLANDI" notu | Tur-2b'de not düşülmüş — kısmi kapanış |
| `donanım-anayasa.md:250` | PROPOSED notu (APPROVAL REQUIRED taşır) | Hâlâ açık — tam kapanış yok |

Kısmi kapanışın kapsamı: :195 yalnızca "soru cevaplandı" der; ":250'deki
PROPOSED madde hâlâ bağlayıcı değildir" anlamına gelir. Tam kapanış, ADR-102
kabulü ile olacaktır.

### §11.3 C-03 transkript — type-kapsam :77-78 · :95 · :135

| Satır | Ham metin (okuma, 2026-10-13) | İşaret |
|---|---|---|
| `donanaım-type-kapsam.md:77-78` | "yalnız 3 bağlam" (eski metin) | İşaretsiz — AÇIK |
| `donanaım-type-kapsam.md:95` | "yalnız 3 bağlam" (tekrar) | İşaretsiz — AÇIK |
| `donanaım-type-kapsam.md:135` | "yalnız 3 bağlam" + STALE işareti | İşaretli — tur-2b |

1/3 işaretli. Düzeltme kararı verilirken üç satırın birlikte ele alınması gerekir.

### §11.4 C-06 transkript — K000 index :6 (kapanış kanıtı)

| Okuma | Değer | Tarih |
|---|---|---|
| Eski okuma (tur-1) | children = 12; `os-donanim-tespiti` YOK | görev girdisi |
| Yeni okuma (tur-2b) | children = 13 (inline array); `os-donanim-tespiti` DÂHİL | 2026-10-13 |

Kapanış tipi: satır ekleme (D tipi uygulanmış). Doğrulama: yeniden ölçüm.

### §11.5 C-10 transkript — type index :93/:94 (kapanış kanıtı)

| Okuma | Değer | Tarih |
|---|---|---|
| Eski okuma (tur-1) | :93 "gpu-ornekler BEKLENIYOR — diskte YOK" | görev girdisi |
| Yeni okuma (tur-2b) | :94 "gpu-ornekler … 4/4 tamam" | 2026-10-13 |

Kapanış tipi: indeks satırı güncellemesi (D tipi uygulanmış). Doğrulama:
yeniden ölçüm.

### §11.6 C-08 transkript — ADR-064 authority (b)

| Kaynak | Ham metin (okuma, 2026-10-13) |
|---|---|
| ADR-064 title | "13 vs 11" sorusunu taşıyan başlık (stale — Q2b) |
| ADR-064 authority (b) | "13 servis DOĞRULANAMADI (hayalet iz keys.md:163) → bağlayıcı = 11 alan servisi (ADR-039 §2.1)" |
| ADR-064 §1.1 | Mevcut durum kanıt tablosu (elektronik platform envanteri) |

Gövde çözümü bağlayıcıdır; başlık düzeltmesi Vault Steward kararıdır (Q2b).

### §11.7 C-15 transkript — K000 index :8

| Satır | Ham metin (okuma, 2026-10-13) | Yorum |
|---|---|---|
| `K000-isletim-sistemi/index.md:6` | children = 13 (tur-2b sonrası) | Güncel |
| `K000-isletim-sistemi/index.md:8` | "12 düğüm sayıldı (124 md / 39.099 satır)" | STALE — C-06 kapanışının artığı |

124 md sayımı 13 düğümle yeniden yapılmalıdır (⚠️ VERIFICATION REQUIRED:
yeniden sayım yapılmadı, yalnız metin okundu).

## §12 Çelişki doğum kronolojisi (hangi tur hangisini buldu)

| Tur | Tarih | Bulunan çelişkiler | Yöntem |
|---|---|---|---|
| Görev girdisi (tur-0) | öncesi | C-01…C-08 | Önceki oturumun kayıtları |
| Tur-1 | 2026-10-13 | (okuma — yeni çelişki yok, ölçüm notları) | İlk envanter turu |
| Tur-2 | 2026-10-13 | C-09 · C-10 · C-11 · C-12 | Yeniden ölçüm + görev-girdisi fark tablosu |
| Tur-2b | 2026-10-13 | C-13 · C-14 · C-15 (+ C-06·C-10 kapanış doğrulaması) | Paralel Vault Steward kapanışları sonrası yeniden ölçüm |

**Kronoloji dersi (kayıt):** C-06 ve C-10, tur-1'de AÇIK iken tur-2b'de kapandı —
kapanışlar bu raporun değil, paralel Vault Steward oturumunun işidir. C-15 ise
**kapanışın artığıdır**: C-06 children'ı 13'e çıkarırken validation satırı
(:8) unutulmuş. Bu, "kapanış yaparken artığı temizle" kuralının neden
gerektiğini gösteren somut örnektir.

## §13 Çapraz referanslar (çelişki ↔ diğer raporlar)

| Çelişki | İlgili rapor | Bölüm |
|---|---|---|
| C-01 · C-02 | mevcut-durum-raporu | §7 (anayasa durumu) |
| C-03 · C-04 | tur-envanteri | §4 (10 alt bağlam envanteri) |
| C-06 · C-15 | tur-envanteri | §13.2 (K000 kök index okuması) |
| C-07 · C-13 · C-14 | domain-esleme-matrisi | §2.2 · §2.3 (K002 kök katalog + komşu alt-dizinler) |
| C-08 | domain-esleme-matrisi | §4.1 (ADR-064 authority 5 madde) |
| C-13 · C-14 | onerilen-dosya-agaci | §6 (sınır önerileri E6-E7) |
| C-15 | onerilen-dosya-agaci · kabul-kriterleri | §6 (E8) · §3 (K-C4 ölçüm kriteri) |
| C-01…C-15 | uygulama-plani | §3 (karar kartları K-1…K-8) |

## §14 Okuyucu notları (sık sorulanlar — bu raporu okuyan Vault ajanı için)

| # | Soru | Cevap |
|---|---|---|
| 1 | "Neden bazı çelişkiler KAPANDI ama silinmiyor?" | Gizleme yasağı (AGENTS.md §3): kapanan çelişki tarihsel kanıttır; silinirse "bu ağaçta hiç çelişki olmadı" izlenimi doğar. Durum alanı güncellenir, satır kalır. |
| 2 | "C-02 KISMEN — yani çözüldü mü?" | Hayır. :195'e not düştü (cevaplandı); :250 hâlâ PROPOSED. Tam kapanış ADR-102 kabulü ile olur. KISMEN = "işaret var, kapanış yok". |
| 3 | "⚠️ işaretli C-05/C-11 için ne yapmalıyım?" | Paralel ajan registry'yi düzenlemiş olabilir ama bu oturum o satırları yeniden okumadı. Kapanış iddiası yazılmaz; Vault Steward yeniden okutmalıdır. |
| 4 | "C-13 mü, C-14 mü önce çözülmeli?" | C-14 (K002-altyapi) — çünkü C-13'ün sınır kararı K002'nin kök adını ve kapsamını belirler; isim kararı olmadan sınır kararı yarım kalır. |
| 5 | "Bu rapor ne zaman güncellenmeli?" | Her Vault Steward kapanış turundan sonra: yeni çelişki eklenir, kapananın durumu yeniden ölçülerek güncellenir. Bu rapor canlı bir günlüktür, tek seferlik rapor değil. |
| 6 | "Neden 15 çelişki, görev girdisi 8 diyordu?" | Görev girdisi 8 çelişkiyi bir önceki oturumdan devraldı; bu oturum 4'ünü (C-09…C-12) kendi ölçümüyle, 3'ünü (C-13…C-15) tur-2b kapanışlarının artığı olarak buldu. Toplam 15 = 8 + 4 + 3. Sayı uydurulmadı, her birinin kartı var. |
| 7 | "KAPANDI ile KISMEN arasındaki fark tam olarak ne?" | KAPANDI = canlı ağaçta çelişki artık yok (iki taraf da hizalandı — yeniden ölçümle doğrulandı). KISMEN = bazı satırlar işaretlendi/not düşüldü ama esas çelişki hâlâ duruyor (örn. C-03'te 3 satırın 1'i işaretli). KAPANDI = iş bitti; KISMEN = iş başladı, bitmedi. |

## §15 Bu raporun kendi sürüm günlüğü (rapor-özel change log)

| Sürüm | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| v1.0 | 2026-10-13 | İlk sürüm: C-01…C-08 (görev girdisi) + C-09…C-12 (tur-2 tespiti) = 12 kayıt | Bu oturum (tur-2 subagent) |
| v1.1 | 2026-10-13 | Tur-2b genişletme: C-13 · C-14 · C-15 eklendi (3 yeni kart); C-06 · C-10 → KAPANDI; C-02 · C-03 → KISMEN; C-08 authority (b) transkripti eklendi; §11 kanıt dökümü · §12 kronoloji · §13 çapraz referans · §14 SSS · §15 bu tablo | Bu oturum (tur-2b subagent) |

**Sürüm kuralı:** Bu dosya Vault Steward'ın tek çelişki günlüğüdür (§SONUÇ).
Yeni sürüm yalnızca (a) yeni çelişki kartı ekleyerek veya (b) durum alanını
yeniden ölçümle güncelleyerek oluşur; eski satırlar silinmez (gizleme yasağı).

**Sürüm sayacı:** v1.1 = bu dosyanın 2. sürümü; toplam 15 çelişki kartı + 7
SSS satırı + 8 transkript tablosu taşır (2026-10-13).

## §KAPSAM-DIŞI (bu rapor neyi çözmez)

1. **Hiçbir çelişki çözülmez** — kayıt + kanıt + öneri düzeyinde kalır.
2. **Dosya revizyonu yapılmaz** — anayasa · kapsam · registry · indeks hiçbiri
   bu oturumda değiştirilmedi (tur-2b kapanışları paralel Vault Steward'ın işidir).
3. **ADR üretilmez/taşınmaz** — ADR-102 `draft/`'ta kalır; ADR-064/ADR-061
   kararları Vault Steward'ındır.
4. **Görev girdisi hataları düzeltilmez** — C-09/C-12 yalnız kayıt altına alınır.
5. **⚠️ işaretli 2 kayıt (C-05 · C-11) yeniden okunmaz** — paralel ajan
   düzenlemesi sonrası doğrulama ayrı bir işlemdir; bu rapor kapanış iddiası
   üretmez.

## §SONUÇ-VE-SONRAKİ-ADIM

**Doğrulandı (kanıt eşleştirmesi): 15 çelişki kaydı** — C-01…C-08 görev
girdisi + C-09…C-12 oturum tespiti + C-13…C-15 tur-2b yeni bulguları;
15'inin de iki tarafı gerçek dosya/satır veya "dosya YOK" kanıtı ile
eşleştirildi. 2 çelişki tur-2b'de kapanmış (C-06 · C-10 — yeniden ölçümle
doğrulandı), 2'si kısmen işaretlenmiş (C-02 · C-03), 9'u açıktır.

**PROPOSED: 0 kalem** — bu rapor karar üretmez (C-13/C-14/C-15 kartları
"yeni tespit"tir, öneri değildir).

**Onay bekliyor (APPROVAL REQUIRED): 11 kalem** — 6 dosya düzeltmesi (D tipi:
C-01·C-02·C-03·C-05·C-11·C-15) + 5 karar (K tipi: C-04·C-07·C-08·C-13·C-14).

**Sonraki adım (tek):** Vault Steward §10 matrisini onaylayıp önce **sınır
kararlarını** (C-13 · C-14 — ADR-102 §6 C2 ile tek ADR'de) verir, sonra D
tipi toplu düzeltmeyi (C-01·C-02·C-03·C-15) uygulatır; o güne kadar bu kayıt
dosyası K001'in **tek çelişki günlüğüdür** — yeni çelişki buraya eklenir,
başka yere dağıtılmaz.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
