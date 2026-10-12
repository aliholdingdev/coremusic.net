---
title: "K000-isletim-sistemi — İşletim Sistemi Master"
type: architecture-master
node_id: K000-isletim-sistemi
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
date: 2026-10-12
updated: 2026-10-12
authority: "SSOT: v2 coremusic-katmanli-mimari-agaci-v2.md §2.1"
related:
  - "index.md"
  - "os-bagimliliklar.md"
  - "os-dogrulama.md"
---

# os-master — K000 İşletim Sistemi Master Dosyası

## §1 Rol (ELI15)

**v2 §2.1 — birebir:** Bilgisayarın/konsolun kendisi. Programların üstünde çalıştığı zemin
(Windows, Linux, macOS, Android…).

Tek cümleyle: CoreMusic'in (web uygulaması, ses motoru, DSP zinciri, firmware) üstünde
**çalıştığı zemini** tarif eder. Zemin değişince üstteki her şey değişir — bu yüzden K000,
Band-1'in ilk sırasındadır.

## §2 Kapsam / Sınır (ne DEĞİL)

| Bu domain DEĞİL | Nereye gider |
|---|---|
| Fiziksel parçalar (disk, ses kartı, ekran, kablo) | K001-donanım |
| Cihazın kendi küçük yazılımı (boot-time iç OS) | K005-firmware |
| Sesin üretilip hoparlöre gitmesi (motor) | K003-audio |
| Ekolayzır/filtre/reverb gibi sinyal şekillendirme | K004-dsp |
| Ağ, DNS, yük dengeleme | K014-network |
| CI/CD, konteyner, deploy | K018-devops |

**Kapsam içi:** işletim sistemi türü, çekirdek, çalışma zamanı, OS API yüzeyi, OS ses alt
sistemi, çapraz-platform uyumluluk, OS gözetleme (observability), OS güvenliği, OS testleri.

## §3 Kademe-2 çocuk tablosu

| # | Çocuk (v2 birebir) | ELI15 | Durum |
|---|---|---|---|
| 1 | electroncis | Elektronik bağlantılı OS alt dalı | draft · ⚠️ kapsam tanımsız |
| 2 | isletim-sistemi-api | Uygulamalara açılan OS çağrı yüzeyi | draft · ⚠️ |
| 3 | isletim-sistemi-audio | OS ses alt sistemi (WASAPI ailesi) | draft · kanıt var (§4) |
| 4 | isletim-sistemi-core | Merkez OS servisleri | draft · ⚠️ kernel ile sınır |
| 5 | isletim-sistemi-cross | OS'ler arası çapraz uyumluluk | draft · ⚠️ |
| 6 | isletim-sistemi-kernel | Çekirdek katmanı | draft · ⚠️ |
| 7 | isletim-sistemi-observability | OS log/metric/trace | draft · ⚠️ |
| 8 | isletim-sistemi-plan | Kapsamı belirsiz alt dal | draft · ⚠️ |
| 9 | isletim-sistemi-runtime | Çalışma zamanı ve süreç yürütme | draft · ⚠️ |
| 10 | isletim-sistemi-security | Yetki/izolasyon/oturum güvenliği | draft · ⚠️ |
| 11 | isletim-sistemi-testing | OS katmanı testleri | draft · ⚠️ |
| 12 | isletim-sistemi-turu | OS tip/tür sınıflandırması | draft · ⚠️ |

> **Sınır kuralı:** core ile kernel ve cross ile turu ayrımı v2'de yazılmamıştır;
> iki çift için de **tahmin yapılmadı** — kapsam üst kademede ayrılmadan bu dosyalara
> içerik yazılmaz.

## §4 Teknoloji / Ekosistem (yalnız kanıtlı bulgular)

> Kural: kanıt yoksa **UNKNOWN** yazılır, isim uydurulmaz.

| Öğe | Bulgu | Kanıt (URL / dosya) | Tarih | Durum |
|---|---|---|---|---|
| Windows Core Audio / WASAPI | WASAPI, uygulama ile ses uç noktası (endpoint) arasındaki veri akışını yönetir; arayüzler Audioclient.h ve Audiopolicy.h başlıklarında tanımlıdır | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ doğrulandı |
| WASAPI shared / exclusive | Exclusive-mode akış ses uç noktasını ele geçirir (diğer tüm sistem sesleri susar); Microsoft kullanıcıya shared mode'a dönüş seçeneği sunulmasını ve cihazın odak kaybında derhal bırakılmasını şart koşar | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ doğrulandı |
| Hedef Windows yığını | WASAPI · COM · WinRT · WDK | .ai/AGENTS.md §4 (Windows Software Engineer satırı) | 2026-10-12 | ✅ dosya okundu |
| Per-OS Neva player kararı | v2 öncesi ADR, dosya adı OS-bazlı oynatıcı kararını taşıyor | .ai/.decisions/accepted/ADR-019-per-os-neva-player.md (dosya varlığı) | 2026-10-12 | ℹ️ yalnız varlık — içerik okunmadı |
| Linux ses alt sistemi (ALSA/PipeWire) | — | — | — | **UNKNOWN** |
| macOS ses alt sistemi (Core Audio) | — | — | — | **UNKNOWN** |
| Android ses alt sistemi | — | — | — | **UNKNOWN** |
| Çekirdek/OS sürüm hedefi | — | — | — | **UNKNOWN** |

### §4.1 Lisans notu

Bu turda K000 için lisans iddiası **yoktur**. ASIO SDK'nın GPLv3 olduğu bilgisi K003/K005
kapsamındadır (bkz. audio-master.md §4 · firmware-master.md §4).

## §5 Bağımlılık özeti

| Yön | Hedef | Tür |
|---|---|---|
| depends_on (bu tur) | — | **boş** — K000 zemindir, üstüne basmaz |
| used_by (iddia) | K003-audio · K004-dsp · K005-firmware · K018-devops | tasarım iddiası → os-bagimliliklar.md |

## §6 Doğrulama özeti

| Ölçüt | Durum |
|---|---|
| v2 §2.1 mini-ağaç birebirliği (12 çocuk) | ✅ karşılandı (2026-10-12) |
| §4'te kanıtsız teknoloji iddiası olmaması | ✅ 3 UNKNOWN açıkça işaretli |
| Çocuk klasör index.md'leri (12/12) | ✅ yazıldı |
| Açık sorular kapanışı | ❌ 4 açık ⚠️ |

**Sonuç:** status: draft kalır — verified için os-dogrulama.md §2'nin tamamı gerekir.

## §7 Açık sorular

1. electroncis neyi temsil ediyor? (imla hatalı yazım korunmuştur — v2 §3 KURAL 1)
2. isletim-sistemi-core ile isletim-sistemi-kernel farkı nedir?
3. isletim-sistemi-plan hangi planı kastediyor?
4. K002 ID GAP bu domaini (veya K001'i) etkiliyor mu?
5. Hedef OS platform seti ve sürümleri (Windows/Linux/macOS/Android?) — **UNKNOWN**.

Hepsi → ⚠️ VERIFICATION REQUIRED · takip: os-dogrulama.md §3.

## §8 Kaynaklar

| # | Kaynak | Erişim |
|---|---|---|
| 1 | v2 · coremusic-katmanli-mimari-agaci-v2.md §2.1 (K000-isletim-sistemi) | yerel dosya, 2026-10-12 |
| 2 | v2 §3 adlandırma kuralları (KURAL 1-3) | yerel dosya, 2026-10-12 |
| 3 | v2 §4.1-4.3 kademe yapısı + 8 zorunlu alan + index şablonu | yerel dosya, 2026-10-12 |
| 4 | About WASAPI — Microsoft Learn | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi · 2026-10-12 |
| 5 | Exclusive-Mode Streams — Microsoft Learn | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams · 2026-10-12 |
| 6 | ADR-097 §5 isimlendirme spesifikasyonu | .ai/.decisions/draft/ADR-097-...md · 2026-10-12 |

## §9 Kademe-2 genişletme planı (bu turda YAPILMADI)

> Kademe-3 (bounded-context) açılışı, ADR-097 K2'ye göre ayrı üretim turudur.
> Bu tablo **niyet kaydıdır** [TASARIM].

| # | Çocuk | Sıradaki kademe | Ön koşul |
|---|---|---|---|
| 1 | electroncis | bounded-context | Q1 kapsam onayı |
| 2 | isletim-sistemi-api | bounded-context | Q2 sınır kararı |
| 3 | isletim-sistemi-audio | bounded-context | Q4 platform kararı |
| 4 | isletim-sistemi-core | bounded-context | Q2 sınır kararı |
| 5 | isletim-sistemi-cross | bounded-context | Q2 sınır kararı |
| 6 | isletim-sistemi-kernel | bounded-context | Q2 sınır kararı |
| 7 | isletim-sistemi-observability | bounded-context | Q4 platform kararı |
| 8 | isletim-sistemi-plan | bounded-context | Q3 kapsam onayı |
| 9 | isletim-sistemi-runtime | bounded-context | Q2 sınır kararı |
| 10 | isletim-sistemi-security | bounded-context | Q4 platform kararı |
| 11 | isletim-sistemi-testing | bounded-context | Q4 platform kararı |
| 12 | isletim-sistemi-turu | bounded-context | Q2 sınır kararı |

## §10 Sınır kararları (K000 ↔ komşular)

| # | Karar | Tür | Durum |
|---|---|---|---|
| 1 | K000, K001-donanıma yalnız sürücü arayüzünden bakar | tasarım | ⚠️ onay bekliyor |
| 2 | K000, K005-firmware'e müdahale etmez (firmware bağımsız çalışır) | tasarım | ⚠️ onay bekliyor |
| 3 | OS ses alt sistemi, K003'ün taşıyıcısıdır — rakibi değil | tasarım | §4 kanıtı var |
| 4 | K000, K014-network ve K018-devops'a yalnız hizmet sağlar | tasarım | ⚠️ onay bekliyor |
| 5 | Çekirdek müdahale yasağı: gerçek zamanlı ses yolu OS çekirdeğine yazmaz | tasarım | ⚠️ onay bekliyor |


---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
