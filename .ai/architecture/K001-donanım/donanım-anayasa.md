---
title: "K001-donanım — Donanım Anayasası"
type: architecture-master
node_id: K001-donanım
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
date: 2026-10-12
updated: 2026-10-12
authority: "SSOT: v2 coremusic-katmanli-mimari-agaci-v2.md §2.2"
related:
  - "index.md"
  - "donanım-bagimliliklar.md"
  - "donanım-dogrulama.md"
---

# donanım-anayasa — K001 Donanım Master Dosyası

> **Dosya adı notu:** v2 §2.2'de bu domain için master dosyası tanımlı değildir.
> ADR-097 K2'nin tek istisnası olarak **donanım-anayasa.md** (yeni, Türkçe rol kelimesi)
> üretilmiştir — bkz. ADR-097 §2 K2 dipnotu.

## §1 Rol (ELI15)

**v2 §2.2 — birebir:** Kasadaki fiziksel parçalar — disk, ses kartı, ekran, kablolar.

Tek cümleyle: CoreMusic'in **fiziksel tarafını** tarif eder. Yazılımın dokunabildiği en alt
gerçeklik burasıdır: ne kadar watt, ne kadar bit, hangi DAC/ADC, hangi disk.

## §2 Kapsam / Sınır (ne DEĞİL)

| Bu domain DEĞİL | Nereye gider |
|---|---|
| Cihazın içindeki küçük yazılım (boot-time iç OS) | K005-firmware |
| Sesin üretilmesi/yönlendirilmesi (yazılım motor) | K003-audio |
| Sinyal şekillendirme (EQ, filtre, reverb) | K004-dsp |
| Devre/PCB tasarım süreçleri ve denetimi | K020-comilance (yönetişim) — ⚠️ sınır onayı yok |
| İşletim sisteminin donanımı sürüşü | K000-isletim-sistemi |

**Kapsam içi:** disk (hdd/ssd), ses donanımı (audio), ekran, kablolar, donanım türleri,
donanım API'si, donanım çekirdek/alt katmanları.

## §3 Kademe-2 çocuk tablosu

| # | Çocuk (v2 birebir) | ELI15 | Durum |
|---|---|---|---|
| 1 | donanım-kernel | Donanıma en yakın donanım katmanı | draft · ⚠️ core ile sınır |
| 2 | donanım-core | Donanım tarafının merkez bileşenleri | draft · ⚠️ kernel ile sınır |
| 3 | donanım-api | Donanıma erişim arayüzü | draft · ⚠️ |
| 4 | donanaım-type | Donanım türü ayrımı (3 alt bağlam) | draft · yazım korunur |

### §3.1 Kademe-3 — donanaım-type altı (bounded-context)

| # | Alt bağlam | ELI15 | Durum |
|---|---|---|---|
| 1 | hdd | Mekanik/katı disk depolama | draft · ⚠️ |
| 2 | audio | Ses kartı, DAC/ADC, amplifikatör girişi | draft · §4 kanıtı var |
| 3 | ssd | Katı hal depolama | draft · ⚠️ |

> **Yazım notu:** donanaım-type (kaynaktaki yazım) v2'den birebir korunmuştur;
> düzeltme YASAK — v2 §3 KURAL 1.

## §4 Teknoloji / Ekosistem (yalnız kanıtlı bulgular)

> Kural: kanıt yoksa **UNKNOWN**, isim uydurulmaz.

| Öğe | Bulgu | Kanıt (URL / dosya) | Tarih | Durum |
|---|---|---|---|---|
| TPA3255 | Class-D amfi; TI sayfası 18 V–53.5 V besleme aralığı ve PurePath Ultra başlığını taşıyor | https://www.ti.com/lit/gpn/TPA3255 | 2026-10-12 | ✅ doğrulandı (aralık ⚠️ — §7 Q2) |
| TPA3255 PBTL KiCad | PBTL kipinde TPA3255 için KiCad projesi (şematik, PCB, dokümantasyon) | https://github.com/EliMattingly22/TPA3255_ClassD_PBTL | 2026-10-12 | ✅ doğrulandı |
| Hedef ses donanımı | PCM3168A · AK4458 · Class AB | .ai/AGENTS.md §4 (Audio Hardware Engineer satırı) | 2026-10-12 | ✅ dosya okundu |
| Çip yerine önerme kuralı | PCM5122 kullanılmışsa PCM3168A veya AK4458 öner | .ai/AGENTS.md §17 (Edge Cases #8) | 2026-10-12 | ✅ dosya okundu |
| Ölçüm standartları | SNR > 100 dB · THD+N < 0.01 % @ 1 kHz · Class AB akım hesabı · worst-case tolerans + ESD | ~/.config/opencode/AGENTS.md §5 (Audio & Electronics) | 2026-10-12 | ✅ dosya okundu |
| 8-1 ses kartı çip seçimi | ADR dosyası diskte mevcut | .ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md | 2026-10-12 | ℹ️ varlık — içerik okunmadı |
| Donanım tasarım standartları | ADR dosyası diskte mevcut | .ai/.decisions/accepted/ADR-063-hardware-design-standards.md | 2026-10-12 | ℹ️ varlık — içerik okunmadı |
| Elektronik platform mimarisi | ADR dosyası diskte mevcut | .ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md | 2026-10-12 | ℹ️ varlık — içerik okunmadı |
| Class AB 24 V kararı | ADR dosyası diskte mevcut | .ai/.decisions/accepted/ADR-089-classab-24v.md | 2026-10-12 | ℹ️ varlık — içerik okunmadı |
| Disk türü envanteri (hdd/ssd) | — | — | — | **UNKNOWN** |
| Ekran / kablo envanteri | — | — | — | **UNKNOWN** |

### §4.1 Lisans notu

Bu turda K001 için üçüncü-parti **lisans iddiası yoktur**. ASIO SDK GPLv3 notu
K003/K005 kapsamındadır (audio-master.md §4 · firmware-master.md §4).

### §4.2 PVDD aralığı uyuşmazlığı (açık kayıt)

Görev manifesti TPA3255 için **24–52 V PVDD** iddiasını taşır; TI datasheet sayfası ise
**18 V – 53.5 V** besleme aralığını göstermektedir. İkisi aynı aralık değildir —
hangisinin kastedildiği ( referans devre aralığı mı, çipin izin verdiği aralık mı )
**⚠️ VERIFICATION REQUIRED** olarak kayıtlıdır ve tek taraflı düzeltme yapılmamıştır.

## §5 Bağımlılık özeti

| Yön | Hedef | Tür |
|---|---|---|
| depends_on | K000-isletim-sistemi | tasarım iddiası [TASARIM] |
| used_by (iddia) | K000 (sürücü sınırı) · K003-audio (ses donanımı) · K005-firmware (cihaz kontrolü) | [TASARIM] → donanım-bagimliliklar.md |
| Kanıtlanmış bağımlılık | 0 satır | ❌ |

## §6 Doğrulama özeti

| Ölçüt | Durum |
|---|---|
| v2 §2.2 mini-ağaç birebirliği (4 çocuk + 3 alt bağlam) | ✅ 2026-10-12 |
| Yeni master adı ADR-097 K2 istisnasına uygun (donanım-anayasa.md) | ✅ 2026-10-12 |
| §4'te kanıtsız teknoloji iddiası olmaması | ✅ 2 UNKNOWN işaretli |
| PVDD uyuşmazlığı kayıt altında | ✅ açık (§4.2) |
| Açık sorular kapanışı | ❌ 3 açık ⚠️ |

**Sonuç:** status: draft kalır — verified için donanım-dogrulama.md §2 gerekir.

## §7 Açık sorular

1. K002 ID GAP bu domaini etkiliyor mu? (v2 §3.1)
2. TPA3255 PVDD: 24–52 V (manifest) ↔ 18–53.5 V (TI datasheet) — hangisi?
3. donanım-kernel ↔ donanım-core sınırı nedir?
4. donanaım-type altındaki hdd/audio/ssd dışı türler (ekran, kablo…) nereye gidiyor?
5. Hedef disk (hdd/ssd) ve ekran envanteri — **UNKNOWN**.

Hepsi → ⚠️ VERIFICATION REQUIRED · takip: donanım-dogrulama.md §3.

## §8 Kaynaklar

| # | Kaynak | Erişim |
|---|---|---|
| 1 | v2 · coremusic-katmanli-mimari-agaci-v2.md §2.2 (K001-donanım) | yerel dosya, 2026-10-12 |
| 2 | v2 §3 KURAL 1 (yazım dokunulmazlığı — donanaım-type) | yerel dosya, 2026-10-12 |
| 3 | TPA3255 datasheet (TI) | https://www.ti.com/lit/gpn/TPA3255 · 2026-10-12 |
| 4 | TPA3255 Class-D PBTL (KiCad) | https://github.com/EliMattingly22/TPA3255_ClassD_PBTL · 2026-10-12 |
| 5 | ADR-097 §2 K2 (master dosyası istisnası) | .ai/.decisions/draft/ADR-097-...md · 2026-10-12 |
| 6 | ADR-097 §5 isimlendirme spesifikasyonu | .ai/.decisions/draft/ADR-097-...md · 2026-10-12 |

## §9 Kademe-2/3 genişletme planı (bu turda YAPILMADI)

| # | Düğüm | Sıradaki kademe | Ön koşul |
|---|---|---|---|
| 1 | donanım-kernel | bounded-context | Q3 sınır kararı |
| 2 | donanım-core | bounded-context | Q3 sınır kararı |
| 3 | donanım-api | bounded-context | Q3 sınır kararı |
| 4 | donanaım-type/hdd | module | Q4 envanter |
| 5 | donanaım-type/audio | module | §4 kanıtları genişletilir |
| 6 | donanaım-type/ssd | module | Q4 envanter |

## §10 Sınır kararları (K001 ↔ komşular)

| # | Karar | Tür | Durum |
|---|---|---|---|
| 1 | Donanım, firmware'in üstünde değil **altında**dır (firmware donanımı sürer) | tasarım | ⚠️ onay |
| 2 | K001 yalnız fiziksel varlığı tarif eder; sürücü kodu K000'dedir | tasarım | ⚠️ onay |
| 3 | Ölçüm eşikleri (SNR/THD+N) bu domainin kabul kapısıdır | kanıt | §4 ölçüm satırı |
| 4 | donanaım-type altına yalnız 3 bağlam açılır (hdd · audio · ssd) | kaynak-sadakat | ✅ v2 birebir |
| 5 | Ekran ve kablo için ayrı bağlam AÇILMAZ — envanter gelene kadar UNKNOWN | yasak | ⚠️ envanter |


---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
