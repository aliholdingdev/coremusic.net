---
title: "CoreMusic — Mood Taksonomisi (25 Küme · 6 Grup Eşlemesi)"
type: reference
category: personas
version: 1.0.0
status: active
authority: reference
updated: 2026-09-26
---

# CoreMusic — Mood Taksonomisi (25 Küme · 6 Grup Eşlemesi)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[.templates/index]]

---

## §1 Amaç

Bu dosya, CoreMusic persona sisteminde kullanılan **mood kümelerinin tam sınıflandırmasıdır**: her mood için tanım, Big Five (OCEAN) eğilimi, müzik türü eşlemesi ve test etkisi tek yerde durur. Persona dosyalarındaki "Birincil küme / İkincil küme" alanları **yalnız buradaki adlardan** alınır (persona-template §3.5.4 — uydurma küme adı yazılmaz). Sınıflandırma üç katmandır: **10 temel mood** (eski taksonomi çekirdeği) + **8 rol/demografik mood** (grup kimliğinden doğan) + **7 arabesk/dans türevi** (2026-06-03 genişlemesi) → **25 benzersiz küme** (kullanılan 24 + kullanılmayan Nostaljik).

| Alan | Değer |
|------|-------|
| Doküman tipi | `reference` — mood taksonomisi (persona-template §3.5.4'ün tek kaynağı) |
| Küme sayısı | **25 benzersiz** (10 temel + 8 rol + 7 arabesk/dans türevi) |
| Grup kapsamı | 6 grup / 68 persona (→ [[index]] §2.1) |
| Hedef kitle | QA Engineer (test odağı), UI Designer (renk/tema/animasyon), UX Researcher |
| Karar bağı | [[../.decisions/accepted/ADR-023-persona-driven-testing]] §2.2a (20 persona matrisi) — mood adları ADR ile uyumlu |
| Eski kaynak | `.ai/personas/mood-taxonomy.md` (salt-okunur, 2026-06-02, 10 temel mood + eşleştirme matrisi) |
| Genişleme kaynağı | Eski `personas/index.md` §Yeni Persona'lar + §Mood Taksonomisi (8 arabesk/dans mood, 2026-06-03) |
| Zorunlu kardeş | [[index]] (katalog) · [[test-scenarios-mapping]] (senaryo eşlemesi) · [[research-bank]] 🔄 (kaynak bankası) |

### §1.1 Bu Dosya Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Dosya | Bu Dosya Kullanılmaz |
|---------|-------------|----------------------|
| Mood küme adı + tanım + test etkisi | ✅ Bu dosya | — |
| Mood–müzik türü eşleştirme matrisi | ✅ Bu dosya (§3.4) | — |
| 68 persona'nın hangi mood'da olduğu | [[index]] §6.3 | ❌ |
| Persona × senaryo × cihaz eşlemesi | [[test-scenarios-mapping]] | ❌ |
| BPM/renk/sanatçı iddialarının kaynağı | [[research-bank]] 🔄 | ❌ |
| Persona üretme iskeleti | [[../.templates/personas/persona-template]] | ❌ |

**Ayırıcı test:** "Bir mood adı mı arıyorsun?" → bu dosya. "Bu mood hangi senaryoda test ediliyor?" → [[test-scenarios-mapping]]. "Bu iddia doğru mu?" → [[research-bank]] + `⚠️ VERIFICATION REQUIRED`.

### §1.2 Genel Kaynak Uyarısı (TÜM BU DOSYA İÇİN GEÇERLİ)

> ⚠️ **VERIFICATION REQUIRED — toplu etiket:** Bu dosyadaki **BPM aralıkları, müzik türleri tercih sıralaması ve mood↔Big Five eşlemeleri** gerçek-dünya iddiasıdır ve **iki bağımsız kaynakla doğrulanmamıştır**. Eski vault taksonomisinden alınmış, yeniden sınıflandırılmıştır. Doğrulama ⇒ `[[research-bank]]` (başka ajan yazıyor — bu oturumda dokunulmaz). Persona dosyasına mood küme adı **ad** olarak taşınabilir (kurgusal tasarım kararı); **BPM/tür/kişilik iddiası** taşınırken §4.5 etiketi zorunludur.

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 25 mood kümesinin tanımı, Big Five eğilimi, test etkisi (§3.2-§3.4) | Persona dosyalarının içeriği (→ persona-template ile üretilir) |
| 6 grup × mood dağılımı (§2.2) | 68 persona'nın mood ataması (→ [[index]] §6.3) |
| Mood–müzik türü eşleştirme matrisi (§3.5) | Test senaryosu adımları (→ [[test-scenarios-mapping]]) |
| ADR-023 20 persona matrisiyle mood uyumu (§3.6) | Karar metni/şartlar (→ ADR-023) |
| Küme adı kullanım kuralları (§4) | Gerçek-dünya doğrulama kaynakları (→ [[research-bank]] 🔄) |

*Alt konular:* küme katalogları (temel/rol/türev) → grup eşlemesi → müzik matrisi → ADR uyumu → kurallar → workflow → doğrulama → referanslar.

### §2.2 Grup × Mood Dağılımı (kategori · mood · kişi sayısı)

| Grup | Kategori | Mood | Kişi Sayısı |
|------|----------|------|------------|
| Kız Çocuk (4-11) | Temel | Enerjik | 5 (Zeynep, Duru, İlayda, Peri, Rüya) |
| Kız Çocuk (4-11) | Temel | Kaşif | 3 (Elif, Gökçe, Pınar) |
| Kız Çocuk (4-11) | Temel | Yaratıcı | 3 (Defne, İpek, Selin) |
| Kız Çocuk (4-11) | Rol | Utangaç | 2 (Asya, Masal) |
| Kız Çocuk (4-11) | Rol | Lider | 2 (Ada, Nehir) |
| Kız Çocuk (4-11) | Türev | Arabesk Kaşif | 1 (Elifsu) |
| Kız Çocuk (4-11) | Türev | Dans Enerjik | 1 (Mina) |
| **Kız Çocuk toplam** | | | **17** |
| Erkek Çocuk (4-11) | Temel | Kaşif | 2 (Yusuf, Emir) |
| Erkek Çocuk (4-11) | Temel | Enerjik | 3 (Göktuğ, Mert, Atlas) |
| Erkek Çocuk (4-11) | Temel | Sporcu | 2 (Efe, Arda) |
| Erkek Çocuk (4-11) | Rol | Meraklı | 2 (Kerem, Kuzey) |
| Erkek Çocuk (4-11) | Rol | Sessiz | 1 (Deniz) |
| Erkek Çocuk (4-11) | Türev | Arabesk Meraklı | 1 (Yiğit Can) |
| Erkek Çocuk (4-11) | Türev | Dans Sporcu | 1 (Egehan) |
| **Erkek Çocuk toplam** | | | **12** |
| Genç Kız (12-17) | Temel | Enerjik | 4 (Zeynep, Nehir, Sude, Dilara) |
| Genç Kız (12-17) | Temel | Romantik | 3 (Alya, Azra, İrem) |
| Genç Kız (12-17) | Temel | Melankolik | 3 (Elif, Asya, Ceren) |
| Genç Kız (12-17) | Temel | Sosyal | 3 (Defne, Yağmur, Begüm) |
| Genç Kız (12-17) | Temel | Moody | 2 (Asel, Ece) |
| Genç Kız (12-17) | Türev | Arabesksever | 1 (Zeliha) |
| Genç Kız (12-17) | Türev | Danssever | 1 (Buse) |
| **Genç Kız toplam** | | | **17** |
| Genç Erkek (12-17) | Temel | Sporcu | 2 (Metehan, Ege) |
| Genç Erkek (12-17) | Temel | Romantik | 2 (Alparslan, Rüzgar) |
| Genç Erkek (12-17) | Temel | Sosyal | 2 (Berkay, Toprak) |
| Genç Erkek (12-17) | Rol | Hip-Hop | 2 (Emirhan, Doruk Ö.) |
| Genç Erkek (12-17) | Rol | Gamer | 2 (Kaan, Çınar) |
| Genç Erkek (12-17) | Türev | Arabesk Melankolik | 1 (Furkan) |
| Genç Erkek (12-17) | Türev | Dans Enerjik | 1 (Atakan) |
| **Genç Erkek toplam** | | | **12** |
| Yetişkin Kadın (25-45) | Temel | Romantik | 1 (Ayşe) |
| Yetişkin Kadın (25-45) | Temel | Enerjik | 1 (Buket) |
| Yetişkin Kadın (25-45) | Temel | Melankolik | 1 (Ceyda) |
| Yetişkin Kadın (25-45) | Temel | Profesyonel | 1 (Deniz Ö.) |
| Yetişkin Kadın (25-45) | Rol | Anne | 1 (Ebru) |
| **Yetişkin Kadın toplam** | | | **5** |
| Yetişkin Erkek (25-45) | Temel | Romantik | 1 (Baran) |
| Yetişkin Erkek (25-45) | Temel | Enerjik | 1 (Can) |
| Yetişkin Erkek (25-45) | Temel | Melankolik | 1 (Doruk E.) |
| Yetişkin Erkek (25-45) | Temel | Profesyonel | 1 (Ahmet — **Hip-Hop**; §3.4 notu) |
| Yetişkin Erkek (25-45) | Rol | Baba | 1 (Emre) |
| **Yetişkin Erkek toplam** | | | **5** |

**Sayım kontrolü:** 17 + 12 + 17 + 12 + 5 + 5 = **68** ✅ (dosya bazlı çapraz kontrol: [[index]] §6.3).

> **Not (yetişkin erkek #66):** Ahmet Çelik'in mood'u eski eşlemede **Hip-Hop**, dosya adı `ahmet-celik-hiphop.md` — yukarıda kategori sütunu "Temel/Profesyonel" satırıyla karışmaması için §3.3 Rol kataloğunda **Hip-Hop** olarak işlenmiştir; eski grup tablosunda yetişkin erkek "Profesyonel" mood'u yalnız **hiçbir dosyada yoktur** (eski index'te tanımlı ama atamasız) → ⚠️ VERIFICATION REQUIRED ( Vault Steward onayı: Profesyonel mood'u yetişkin erkekte kullanılmıyor).

---

## §3 Mimari — Mood Kümelerinin Tam Sınıflandırması

### §3.1 Kategori Tanımları

| Kategori | Tanım | Küme sayısı | Kaynak |
|----------|-------|-------------|--------|
| **A · Temel mood** | Müzik zevki + dinleme davranışından doğan mood | 10 (kullanılan 9; **Nostaljik** atamasız) | Eski `mood-taxonomy.md` (2026-06-02) |
| **B · Rol/demografik mood** | Yaş·rol kimliğinden doğan mood (Utangaç, Lider, Anne, Baba …) | 8 | Eski `index.md` grup tabloları |
| **C · Arabesk/Dans türevi** | 2026-06-03 genişlemesi — iki ana türün türevleri | 7 benzersiz ad (eski tabloda 8 satır) | Eski `index.md` §Yeni Persona'lar |
| **Toplam** | Benzersiz küme | **25** (kullanılan 24 + Nostaljik) | Bu dosya §3.2-§3.4 |

### §3.2 Kategori A — 10 Temel Mood

> Her küme için: **Tanım** · **Big Five eğilimi (kurgusal önerme — ⚠️ §1.2)** · **Müzik/BPM (⚠️ §1.2)** · **UI tercihi** · **Test etkisi** (ADR-023 senaryolarına bağlantı). BPM değerleri eski taksonomidendir.

#### §3.2.1 Romantik

| Alan | Değer |
|------|-------|
| Tanım | Aşk/duygu odaklı dinleyici; şarkıya duygusal bağ kurar, az skip eder, tekrar dinler |
| Big Five eğilimi (kurgusal) | Yüksek Uyumluluk (C), orta-düşük Dışadönüklük (O), orta Duygusal Denge (N) |
| Müzik tercihi / BPM | Aşk şarkıları, slow, ballad, Türk sanat müziği · **60-100 BPM** ⚠️ |
| Dinleme zamanı | Akşam, gece, yalnız |
| UI tercihi | Yumuşak renkler, yavaş animasyonlar, sıcak tonlar |
| Test etkisi | Duygusal UI, renk paleti, **slow geçiş animasyonları**; ADR-023 satır 16 (mood-geçiş) BPM eşikleri |
| Grup ataması | Genç Kız ×3 · Genç Erkek ×2 (Romantik/Rock türevi) · Yetişkin Kadın ×1 · Yetişkin Erkek ×1 |

#### §3.2.2 Enerjik

| Alan | Değer |
|------|-------|
| Tanım | Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici |
| Big Five eğilimi (kurgusal) | Yüksek Dışadönüklük (O), yüksek Deneyime Açıklık (A), düşük N |
| Müzik tercihi / BPM | Pop, dance, elektronik, hareketli · **120-160 BPM** ⚠️ |
| Dinleme zamanı | Sabah, spor, araba, arkadaşlarla |
| UI tercihi | Canlı renkler, hızlı animasyonlar, yüksek kontrast |
| Test etkisi | **Hızlı navigasyon, anlık geri bildirim, skip performansı**; ADR-023 satır 12 (hızlı navigasyon + geri tuşu) |
| Grup ataması | Kız Çocuk ×5 · Erkek Çocuk ×3 · Genç Kız ×4 · Yetişkin Kadın ×1 · Yetişkin Erkek ×1 |

#### §3.2.3 Melankolik

| Alan | Değer |
|------|-------|
| Tanım | Derin dinleyici; şarkı sözü okur, az etkileşim kurar, gece ve yalnız dinler |
| Big Five eğilimi (kurgusal) | Yüksek Duygusal Denge/N (ters kodlama: yüksek puan = düşük denge), orta A |
| Müzik tercihi / BPM | Alternative, indie, slow rock, hüzünlü şarkılar · **60-90 BPM** ⚠️ |
| Dinleme zamanı | Gece, yağmurlu hava, yalnız |
| UI tercihi | Karanlık tema, minimalist, az animasyon |
| Test etkisi | **Karanlık tema, keşif algoritması, düşük etkileşim**; dark/light kontrast (WCAG 1.4.3 — ⚠️ research-bank) |
| Grup ataması | Genç Kız ×3 · Yetişkin Kadın ×1 · Yetişkin Erkek ×1 (+ Arabesk Melankolik türevi ×1) |

#### §3.2.4 Moody (Değişken)

| Alan | Değer |
|------|-------|
| Tanım | Ruh hâline göre tür değiştiren; geniş BPM aralığında, yüksek skip oranlı dinleyici |
| Big Five eğilimi (kurgusal) | Yüksek N (duygusal dalgalanma), orta O |
| Müzik tercihi / BPM | Rap, hip-hop, trap; ruh haline göre değişir · **80-140 (geniş)** ⚠️ |
| Dinleme zamanı | Gün içinde değişken |
| UI tercihi | Değişken, kişiselleştirilebilir tema/öneri ayarı |
| Test etkisi | **Anlık karar, tür değiştirme (genre switching), öneri doğruluğu**; ADR-023 satır 16 (geçersiz mood değeri = error) |
| Grup ataması | Genç Kız ×2 (Asel, Ece) |

#### §3.2.5 Profesyonel

| Alan | Değer |
|------|-------|
| Tanım | Odaklanma amaçlı, arka planda dinleyen; verimlilik odaklı, az etkileşimli kullanıcı |
| Big Five eğilimi (kurgusal) | Yüksek Sorumluluk (E), düşük Dışadönüklük (O) |
| Müzik tercihi / BPM | Caz, klasik, lo-fi, ambient, enstrümantal · **60-100 BPM** ⚠️ |
| Dinleme zamanı | Çalışma saatleri, ofis |
| UI tercihi | Minimal, hızlı, verimli, az dikkat dağıtıcı |
| Test etkisi | **Productivity modu, arka plan performansı, oturum (session) süresi**; ADR-023 satır 7 (uzun oturum + cache TTL boundary) |
| Grup ataması | Yetişkin Kadın ×1 (Deniz Ö.) · Yetişkin Erkek — tanımlı, atamasız (§2.2 notu) |

#### §3.2.6 Kaşif

| Alan | Değer |
|------|-------|
| Tanım | Tüm türleri deneyen, yeni çıkanları ve deneysel müzikleri kovalayan keşif kullanıcısı |
| Big Five eğilimi (kurgusal) | Çok yüksek Deneyime Açıklık (A), orta O |
| Müzik tercihi / BPM | Tüm türler, yeni çıkanlar, deneysel · **tüm aralıklar** ⚠️ |
| Dinleme zamanı | Boş zaman, hafta sonu |
| UI tercihi | Keşif odaklı widget'lar, öneri blokları |
| Test etkisi | **Keşif algoritması, öneri kalitesi, tür çeşitliliği**; ADR-023 satır 18 (müzik keşif — boş/uzun/karakter-sınırı sorgular) |
| Grup ataması | Kız Çocuk ×3 · Erkek Çocuk ×2 |

#### §3.2.7 Sosyal

| Alan | Değer |
|------|-------|
| Tanım | Paylaşan, takip eden, akışta dolaşan; müzik sosyalleşme aracıdır |
| Big Five eğilimi (kurgusal) | Yüksek Dışadönüklük (O), yüksek Uyumluluk (C) |
| Müzik tercihi / BPM | Popüler, trend, viral, sosyal medyada paylaşılan · **100-140 BPM** ⚠️ |
| Dinleme zamanı | Arkadaşlarla, partide, sosyal ortam |
| UI tercihi | Paylaşım butonları, sosyal özellikler öne çıkar |
| Test etkisi | **Paylaşım akışı, işbirlikli playlist, sosyal feed**; ADR-023 satır 20 (paylaşım → yayılım / gizli hesap / 280 karakter sınırı) |
| Grup ataması | Genç Kız ×3 · Genç Erkek ×2 |

#### §3.2.8 Nostaljik

| Alan | Değer |
|------|-------|
| Tanım | Geçmişe dönük dinleyici; yıla göre arama, throwback ve favori listeleri |
| Big Five eğilimi (kurgusal) | Orta-düşük A, orta C |
| Müzik tercihi / BPM | 90'lar, 2000'ler, retro, eski Türkçe pop · **70-120 BPM** ⚠️ |
| Dinleme zamanı | Akşam, anı canlanınca |
| UI tercihi | Arama odaklı, geçmiş/favoriler öne çıkar |
| Test etkisi | **Arama kalitesi, geçmiş, yıl/on yıl filtreleri** |
| Grup ataması | **YOK — kullanılmayan küme** (68 persona içinde atama yok; korunur, silinmez) ⚠️ Vault Steward onayına açık |

#### §3.2.9 Sporcu

| Alan | Değer |
|------|-------|
| Tanım | Antrenman odaklı; yüksek tempolu, motivasyon şarkısı ve workout playlist kullanır |
| Big Five eğilimi (kurgusal) | Yüksek Sorumluluk (E), yüksek O |
| Müzik tercihi / BPM | Yüksek enerji, EDM, rock, workout mix · **130-180 BPM** ⚠️ |
| Dinleme zamanı | Spor, koşu, antrenman |
| UI tercihi | Büyük butonlar, hızlı erişim, ses kontrolü |
| Test etkisi | **Tempo bazlı öneri, workout modu, hızlı kontrol (dokunma hedefi ≥44/48px — ⚠️ WCAG research-bank)** |
| Grup ataması | Erkek Çocuk ×2 · Genç Erkek ×2 (+ Dans Sporcu türevi ×1) |

#### §3.2.10 Yaratıcı

| Alan | Değer |
|------|-------|
| Tanım | Görsel-müzik eşleşmesi seven; resim/yazı gibi yaratıcı çalışırken uzun oturum kurar |
| Big Five eğilimi (kurgusal) | Çok yüksek Deneyime Açıklık (A), orta-düşük E |
| Müzik tercihi / BPM | Enstrümantal, deneysel, world music, soundtrack · **60-120 BPM** ⚠️ |
| Dinleme zamanı | Yaratıcı çalışma, resim, yazı |
| UI tercihi | Görsel odaklı, albüm kapağı büyük, renk uyumu |
| Test etkisi | **Görsel tasarım, mood eşleşmesi, AudioVisualizer kalitesi** |
| Grup ataması | Kız Çocuk ×3 |

### §3.3 Kategori B — 8 Rol/Demografik Mood

| # | Küme | Tanım | Big Five eğilimi (kurgusal) | Müzik tercihi ⚠️ | Test etkisi | Grup ataması |
|---|------|-------|------------------------------|------------------|------------|-------------|
| 11 | **Utangaç** | Çekingen, az risk alan; arayüzde güven veren ipuçları ister | Yüksek N, düşük O | Yumuşak, tanıdık pop/çocuk şarkıları | **Güven veren boş durum metni, büyük geri tuşu, hata mesajı tonu** | Kız Çocuk ×2 |
| 12 | **Lider** | Öncü, yönlendiren; listeleri ve sıralamayı kontrol eder | Yüksek O, yüksek E | Dans/pop, kararlı ritim | **Sıralama/önceliklendirme kontrolleri, rol/izin akışı (ebeveyn ≠ çocuk)** | Kız Çocuk ×2 |
| 13 | **Meraklı** | Soru soran, keşfeden çocuk; sık arama yapar | Yüksek A, orta N | Çocuk dostu keşif, oyun müziği | **Basit arama (kısa sorgu), filtre geri bildirimi** — ADR-023 satır 11 | Erkek Çocuk ×2 |
| 14 | **Sessiz** | Az etkileşim, uzun pasif dinleme | Düşük O, yüksek N | Sakin, enstrümantal | **Düşük etkileşim akışı, klavye/fare dışı erişim, otomatik ilerleme** | Erkek Çocuk ×1 |
| 15 | **Hip-Hop** | Rap/trap odaklı, lirik ve ritim duyarlı | Yüksek O, orta A | Rap, hip-hop, trap · **80-140 BPM** ⚠️ | **Söz senkronu, hızlı skip, tür filtresi** | Genç Erkek ×2 · Yetişkin Erkek ×1 |
| 16 | **Gamer** | Oyun içi/akış müzikleri; çoklu görev ve düşük gecikme beklentisi | Yüksek A, yüksek O | Elektronik, chiptune, gaming soundtrack | **Düşük gecikme (INavigation), arka plan çalma dayanıklılığı, kısayollar** | Genç Erkek ×2 |
| 17 | **Anne** | Aile hesabı yöneticisi; ebeveyn kontrolü ve içerik filtresi kullanır | Yüksek C, yüksek N | Türkçe pop, aile uyumlu içerik | **Ebeveyn kontrolü akışı (ADR-023 satır 9: H / E yetkisiz / B yaş sınırı), profil paylaşımı** | Yetişkin Kadın ×1 |
| 18 | **Baba** | Aile hesabı; çocuk hesabıyla ortak dinleme | Yüksek C, orta O | Karışık, arabesk/pop köprüsü | **Ortak oturum, ebeveyn PIN'i, çoklu cihaz (ADR-023 satır 14)** | Yetişkin Erkek ×1 |

### §3.4 Kategori C — 7 Arabesk/Dans Türevi Mood (2026-06-03)

| # | Küme | Tanım | Big Five eğilimi (kurgusal) | Müzik/BPM ⚠️ | Test etkisi | Grup ataması |
|---|------|-------|------------------------------|--------------|------------|-------------|
| 19 | **Arabesksever** | Sürekli arabesk; hüzün/duygu odaklı dinleme | Yüksek N, orta C | Arabesk, duygusal vokal · **60-100 BPM** ⚠️ | **Duygusal UI, slow tempo, vokal öncelikli öneri** | Genç Kız ×1 (Zeliha) |
| 20 | **Danssever** | Sürekli dans/elektronik; yüksek enerji | Yüksek O, düşük N | EDM, dance · **120-160 BPM** ⚠️ | **Hızlı navigasyon, canlı renkler, yüksek BPM filtresi** | Genç Kız ×1 (Buse) |
| 21 | **Arabesk Kaşif** | Aileden öğrenilmiş arabesk + keşif | Yüksek A, orta N | Arabesk + çocuk keşif | **Çocuk dostu UI, aile paylaşımı, içerik filtresi** | Kız Çocuk ×1 (Elifsu) |
| 22 | **Arabesk Meraklı** | Babayla ortak arabesk zevki | Orta C, orta N | Arabesk, ortak dinleme | **Aile hesabı, ebeveyn kontrolü (yetkinlik sınırı)** | Erkek Çocuk ×1 (Yiğit Can) |
| 23 | **Dans Sporcu** | Break dance + yüksek tempo | Yüksek E, yüksek O | EDM/rock workout · **130-180 BPM** ⚠️ | **Egzersiz odaklı, BPM bazlı öneri, büyük dokunma hedefi** | Erkek Çocuk ×1 (Egehan) |
| 24 | **Arabesk Melankolik** | Gece arabesk, duygusal derinlik | Yüksek N, düşük O | Arabesk, slow · **60-90 BPM** ⚠️ | **Karanlık tema, slow geçişler, gece modu** | Genç Erkek ×1 (Furkan) |
| 25 | **Dans Enerjik** | Dans kursı/workout + hareketli müzik (çocuk ve genç erkek ortak adı) | Yüksek O, düşük N | Dans, club, workout · **120-160 BPM** ⚠️ | **Koreografi videoları, workout playlist, yüksek BPM** | Kız Çocuk ×1 (Mina) · Genç Erkek ×1 (Atakan) |

> **Eski tablo notu:** Eski `index.md` §Mood Taksonomisi 8 satır yazar; son satır "Dans Enerjik (Erkek)" ayrı ad taşısa da dosya adı/atanması **`atakan-demir-dans`** ve küme adı **Dans Enerjik**'tir → bu katalogda 25. küme tek satırdır (bilgi korunumu: eski satır burada anıldı, silinmedi).

### §3.5 Mood–Müzik Türü Eşleştirme Matrisi (10 temel mood — eski matris korunarak yeniden yazıldı)

Sembol: `●●●` = Birincil · `●●` = Güçlü · `●` = Orta · `○` = Zayıf · `-` = Yok

| Mood | Pop | Rock | Rap | Elektronik | Klasik | Türkçe Pop | Arabesk | Caz |
|------|-----|------|-----|-----------|--------|-----------|---------|-----|
| Romantik | ● | ○ | - | - | ● | ●● | ● | ●● |
| Enerjik | ●● | ● | ●● | ●● | - | ●● | - | - |
| Melankolik | ○ | ●● | ○ | - | ● | ● | ●● | ● |
| Moody | ● | ● | ●●● | ● | - | ● | - | - |
| Profesyonel | - | - | - | ○ | ●● | - | - | ●●● |
| Kaşif | ● | ● | ● | ● | ● | ● | ● | ● |
| Sosyal | ●● | ● | ●● | ●● | - | ●● | - | - |
| Nostaljik | ●● | ● | - | - | - | ●●● | ● | - |
| Sporcu | ● | ●● | ●● | ●●● | - | ● | - | - |
| Yaratıcı | ○ | ● | - | ● | ●● | ○ | - | ●● |

**Kategori B/C eklemeleri (matrisin devamı — bu katalogda yeni):**

| Mood | Arabesk | Rap/Hip-Hop | EDM/Dans | Çocuk dostu | Enstrümantal |
|------|---------|-------------|----------|-------------|--------------|
| Hip-Hop | - | ●●● | ●● | - | - |
| Gamer | - | ● | ●●● | ○ | ● |
| Anne | - | - | ○ | ●●● | ○ |
| Baba | ●● | ● | ● | ●● | ○ |
| Utangaç | ○ | - | - | ●●● | ● |
| Lider | - | ● | ●● | ●● | - |
| Meraklı | ○ | ○ | ● | ●●● | ○ |
| Sessiz | - | - | ○ | ● | ●●● |
| Arabesksever | ●●● | - | - | - | ○ |
| Danssever | - | ○ | ●●● | - | - |
| Arabesk Kaşif | ●●● | - | - | ●●● | - |
| Arabesk Meraklı | ●●● | - | - | ●● | - |
| Dans Sporcu | - | - | ●●● | ●● | - |
| Arabesk Melankolik | ●●● | - | - | ○ | ● |
| Dans Enerjik | - | - | ●●● | ●● | - |

*(Bu ek matris türetilmiştir — kurgusal tasarım kararı; doğrulama ⇒ `[[research-bank]]` ⚠️ §1.2.)*

### §3.6 ADR-023 20 Persona Matrisiyle Mood Uyumu

ADR-023 §2.2a satır bazlı eşleşme (mood adları bu taksonomidendir):

| ADR satırı | Persona | Bağlı mood (bu dosya) | Kategori | Test etkisi (özet) |
|-----------|---------|------------------------|----------|--------------------|
| 1 | Senior geliştirici | Profesyonel (rol: yazılımcı/senior) | A | Uzun oturum, verimlilik |
| 2 | Junior geliştirici | Kaşif (öğrenme eğrisi) | A | Anlamlı hata mesajları |
| 3 | Uzman (domain) | Profesyonel | A | Karmaşık filtre/sorgu sınırı |
| 4 | Normal son kullanıcı | Romantik/Normal (nötr) | A | Login → oturum → sayfa geçişi |
| 5 | Profesyonel kullanıcı | Profesyonel | A | Rate limit boundary (ADR-013) |
| 6 | Ev kullanıcısı | Anne/Baba (aile) | B | Cihaz değişimi, session yenileme |
| 7 | Studio kullanıcısı | Profesyonel (studio) | A | Uzun oturum, cache TTL boundary |
| 8 | Yazılımcı | Profesyonel (rol) | A | Router sözleşmesi + API key (ADR-021/020) |
| 9 | Kız çocuk (4-11) | Kaşif/Enerjik/Yaratıcı/Utangaç/Lider + türevler | A+B+C | İçerik filtresi + ebeveyn kontrolü |
| 10 | Genç kız (12-17) | Romantik/Enerjik/Melankolik/Moody/Sosyal + Arabesksever/Danssever | A+C | Paylaşım izni boundary |
| 11 | Erkek çocuk (4-11) | Kaşif/Enerjik/Sporcu/Meraklı/Sessiz + türevler | A+B+C | Basit arama + boş sonuç |
| 12 | Genç erkek (12-17) | Hip-Hop/Gamer/Sporcu/Romantik/Sosyal + türevler | A+B+C | Hızlı navigasyon + geri tuşu |
| 13 | Yetişkin kadın (25-45) | Anne/Romantik/Enerjik/Melankolik/Profesyonel | A+B | Profil + ödeme formu (**ödeme PLANNED ⚠️**) |
| 14 | Yetişkin erkek (25-45) | Romantik/Sporcu/Melankolik/Baba/(Hip-Hop) | A+B | Çoklu cihaz session çakışması |
| 15 | Erişilebilirlik kişisi | (mood bağımsız — WCAG odaklı) | — | Klavye + ekran okuyucu (WCAG 2.2 AA) |
| 16 | Mood-geçiş kişisi | Arabesksever ↔ Danssever | C | Mood yazma + BPM filtresi (H/E/B) |
| 17 | Tarayıcı navigasyon kişisi | (mood bağımsız) | — | History API + 404 fallback |
| 18 | Müzik keşif kişisi | Kaşif | A | Arama/öneri sorgusu boundary |
| 19 | Playlist kişisi | Sosyal / Profesyonel | A | Playlist CRUD + yarış durumu |
| 20 | Sosyal paylaşım kişisi | Sosyal | A | Paylaşım → yayılım / 280 karakter |

> **Kural (ADR-023 §2.2a):** satır 9-14 grup temsilcisidir — 68 dosyanın teker teker testlenmesi DEĞİL; yeni satır = yeni ADR (ADR-088+).

---

## §4 Kurallar

### §4.0 ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

*Bu dosya `[[.templates/documentation/docs-md-template]]` iskeletiyle üretilmiştir; persona-domaini kuralları `[[../.templates/personas/persona-template]]` §3.5.4/§4.5 ile uyumludur.*

### §4.1 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Guardrail #16 — Template Mandatory | Dosya şablondan üretilir | Dosya geçersiz, revert |
| 2 | **Küme adı tek kaynaktan** | Persona "Birincil/İkincil küme" alanı yalnız bu §3'ten alınır | Uydurma ad → dosya revert, log ERROR |
| 3 | Kaynak etiketleme (ADR-005) | BPM/tür/kişilik iddiası → `⚠️ VERIFICATION REQUIRED` + `[[research-bank]]` | Etiketsiz iddia silinir |
| 4 | ADR-023 uyumu | Mood adları 20 satırlık matristeki adlarla birebir (§3.6) | Çelişki → DUR, Vault Steward |
| 5 | In-Place | Küme adları yeniden adlandırılmaz; eski ad korunur | Dosya geri yüklenir |
| 6 | Derinlik 500+ | Bu dosya ≥ 500 satır (`verify` → `lines`) | Tamamlanmış sayılmaz |
| 7 | Dil / mojibake | Türkçe; `Ã-`/U+FFFD yasak; tek yazma arayüzü `vault-utf8-writer` | `repair` ile onarılır |
| 8 | Kullanılmayan küme silinmez | Nostaljik atamasız ama korunur | Silme yasak (bilgi korunumu) |
| 9 | Append-only log | Değişiklikler `[[../log.md]]`'ye eklenir | Geçmiş satır değişmez |
| 10 | `research-bank.md` mülkiyeti | Başka ajan — bu oturumda dokunulmaz | Eşzamanlı yazım → handover |

### §4.2 Yasak / Doğru Tablosu

| ✅ Yasak | ✅ Doğru |
|---------|---------|
| Persona dosyasında uydurma mood adı ("enerjik-sevimli") | `[[index]]`/bu dosya §3'teki ad ("Enerjik") |
| BPM iddiasını kaynaksız yazmak | `⚠️ VERIFICATION REQUIRED` + `[[research-bank]]` pointer'ı |
| Mood–Big Five eşleşmesini "bilimsel kanıt" gibi sunmak | "kurgusal persona önermesi" etiketi (§3.2 başlıkları) |
| Nostaljik kümesini "kullanılmıyor" diye silmek | Korunur; atama tablosunda `- (kullanılmayan)` yazılır |
| ADR-023 matrisine 21. satır eklemek | Yeni persona/satır = yeni ADR (ADR-088+) |
| Eski taksonomi dosyasına yazmak | Salt-okunur okuma; tüm üretim bu dizinde |

### §4.3 Wiki-Link Formatı

| ✅ Doğru | ❌ Yanlış |
|----------|-----------|
| `[[index]]` | `[katalog](index.md)` |
| `[[test-scenarios-mapping]]` | `[eşleme](C:\...\test-scenarios-mapping.md)` |
| `[[../.decisions/accepted/ADR-023-persona-driven-testing]]` | `https://iç-sistem/adr-023` |
| `[[research-bank]]` | `[[personas/research-bank.md]]` (uzantılı) |

### §4.4 Derinlik Standardı (500+)

| Kural | Değer |
|-------|-------|
| Bu dosya | ≥ 500 satır (ham ölçüm — `vault-utf8-writer verify` → `lines`) |
| Persona dosyaları | ≥ 500 satır (persona-template §4.6) |
| Kapsam dışı | `session-log-template.md` (144 satır) |
| İhlal | 500 altındaysa §5.2 ile derinleştirilir (küme satırları zaten §3.5/§3.6 genişletilebilir) |

### §4.5 REDACTED, KVKK ve Kaynak Etiketi

| Durum | Aksiyon |
|-------|---------|
| BPM / müzik türü / sanatçı iddiası | `⚠️ VERIFICATION REQUIRED` + `[[research-bank]]` (≥2 kaynak beklenir) |
| Mood↔Big Five eşlemesi | `kurgusal persona önermesi` etiketi; "kanıt" gibi sunulmaz |
| WCAG kriter numarası (1.4.3, 2.5.8 …) | Gerçek-dünya → 2 kaynak veya `⚠️` + research-bank |
| 18 yaş altı persona (29 çocuk + 29 teen) | Kurgusal; gerçek çocuk verisi asla (KVKK — ⚠️ mevzuat iddiası research-bank'te) |
| Secret / gerçek kişi verisi | `[REDACTED]` — yazılmaz |

---

## §5 Workflow

### §5.1 Mood Kullanım / Üretim Adımları

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | Bu dosyanın §3'ünü oku (küme adları + kategoriler) | Geçerli ad listesi | 3 dk |
| 2 | Persona için birincil + ikincil kümeyi seç (grup uyumu §2.2) | 2 küme adı | 2 dk |
| 3 | Test etkisi satırını `[[test-scenarios-mapping]]` ile çaprazla | Eşleşen senaryo | 5 dk |
| 4 | BPM/tür/kişilik satırını persona dosyasına taşırken §4.5 etiketini yaz | Etiketli veri | 5 dk |
| 5 | ADR-023 §3.6 satırıyla uyumu doğrula (yeni satır yok!) | Uyum onayı | 2 dk |
| 6 | `vault-utf8-writer verify --file` → ≥500, mojibake 0 | UTF-8 raporu | <1 dk |
| 7 | `log.md` append (küme ekleme/değişikliği) | Audit trail | 1 dk |

```text
§3 OKU → KÜME SEÇ → GRUP UYUMU → SENARYO ÇAPRAZLA → KAYNAK ETİKETLE → ADR-023 UYUMU → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Uydurma küme adı | persona-template §3.5.4 doğrulaması düşer | Adı bu §3'ten al; yoksa `⚠️ VERIFICATION REQUIRED` |
| Sayım uyuşmazlığı | §2.2 grup toplamı ≠ 68 | [[index]] §6.3 ile karşılaştır → `log.md` |
| Mood–matris çelişkisi | ADR-023 satırı farklı ad taşıyor | DUR → Vault Steward (frozen değil ama aktif karar) |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Kırık wiki-link | Hedef yok | §7.1'de `📋` işaretle + `log.md` |
| research-bank'e dokunma | Eşzamanlı yazım | DUR → handover (AGENTS.md §9) |

### §5.3 Bitiş Koşulları

- [ ] Dosya `.ai/.personas/mood-taxonomy.md` yolunda, `.md` uzantılı
- [ ] `verify` temiz (BOM yok, mojibake 0, `lines ≥ 500`)
- [ ] `research-bank.md` bu oturumda değiştirilmedi
- [ ] `log.md` append edildi
- [ ] Wiki-link'ler geçerli ya da §7.1'de durum notu var

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type: reference`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] H1 + Zorunlu Bağlantılar satırı var
- [ ] §1-§7 başlıkları eksiksiz (docs-md iskeleti)
- [ ] §4.0 Şablon-Önce bloğu birebir mevcut ve §4'ün ilk maddesi
- [ ] **25 küme** tanımlı (10 temel + 8 rol + 7 türev) — §3.2-§3.4 toplamı
- [ ] Her kümede: Tanım · Big Five eğilimi · Müzik/BPM · Test etkisi · Grup ataması (5 alan)
- [ ] §2.2 grup toplamları **17/12/17/12/5/5 = 68**
- [ ] §3.6 ADR-023 20 satır ile mood eşleşmesi tam (1-20)
- [ ] §3.5 müzik matrisi 10 temel mood + 15 kategori B/C ek satırı
- [ ] Her gerçek-dünya iddiası `⚠️ VERIFICATION REQUIRED` + `[[research-bank]]` (§1.2 toplu etiket + satır içi)
- [ ] Dosya ≥ 500 satır (`verify` → `lines`)
- [ ] Mojibake yok, BOM yok
- [ ] `research-bank.md`'ye dokunulmadı
- [ ] §7.2 Değişiklik Geçmişi append-only + Authority footer

### §6.2 Metrikler

| Metrik | Beklenen |
|--------|----------|
| Benzersiz küme | **25** (kullanılan 24 + Nostaljik atamasız) |
| Kategori | 3 (A temel 10 · B rol 8 · C türev 7) |
| Grup eşlemesi | 6 grup / 68 persona |
| ADR-023 uyum satırı | 20/20 |
| Müzik matrisi | 10 + 15 = 25 satır |
| Derinlik | ≥ 500 satır |
| `⚠️ VERIFICATION REQUIRED` | §1.2 (toplu) + §2.2 (1) + §3.x BPM satırları + §4.5 (4) |

### §6.3 Bilinen Boşluklar

| # | Boşluk | Durum |
|---|--------|-------|
| 1 | Mood↔Big Five eşlemeleri kanıtsız (kurgusal önerme) | ⚠️ research-bank |
| 2 | Nostaljik kullanılmıyor | Korunuyor; onay bekliyor |
| 3 | Yetişkin erkek "Profesyonel" mood'u atamasız | §2.2 notu — ⚠️ Vault Steward |
| 4 | Eski taksonomide 10 mood; bu dosyada 25 (genişleme) | Eski dosya salt-okunur kaldı — bilgi korunumu |
| 5 | `methodology.md` (Level 1/2/3) diskte yok | 📋 planlanan — [[index]] §3.2 |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[CLAUDE.md]]` | Vault anayasası | ✅ `.ai/CLAUDE.md` |
| `[[AGENTS.md]]` | Agent routing (test/persona → QA) | ✅ `.ai/AGENTS.md` |
| `[[WORKFLOW.md]]` | Süreçler | ✅ `.ai/WORKFLOW.md` |
| `[[.templates/index]]` | Şablon registry'si | ✅ `.ai/.templates/index.md` |
| `[[.templates/documentation/docs-md-template]]` | Yapısal anahtar (8-bölüm) | ✅ `.ai/.templates/documentation/docs-md-template.md` |
| `[[../.templates/personas/persona-template]]` | Persona şablonu — §3.5.4 küme kuralı | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[index]]` | Persona kataloğu (68/6 grup) | ✅ `.ai/.personas/index.md` |
| `[[test-scenarios-mapping]]` | Persona × senaryo eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[research-bank]]` | Gerçek-dünya kaynak bankası | 🔄 **BAŞKA AJAN YAZIYOR** — `.ai/.personas/research-bank.md` (dokunulmaz) |
| `[[../.decisions/accepted/ADR-023-persona-driven-testing]]` | 20 persona test matrisi + şart 1c | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |
| `[[../index]]` | Master katalog §12 | ✅ `.ai/index.md` |
| `[[../log.md]]` | Audit trail | ✅ `.ai/log.md` |
| `[[personas/methodology]]` | Test seviyeleri | 📋 planlanan — `.ai/.personas/methodology.md` |

### §7.2 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — 25 kümelik tam taksonomi (10 temel + 8 rol + 7 türev), Big Five/müzik/test etkisi sütunları, 6 grup eşlemesi, müzik matrisi, ADR-023 20 satır uyumu (eski 128 satırlık taksonomi yeniden yazıldı ve genişletildi) | vault-updater (subagent) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
