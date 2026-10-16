---
node_id: K005-firmware/update/update-anayasa
node_type: module
status: draft
parent_id: K005-firmware/update
children: []
depends_on:
  - K005-firmware/boot
  - K005-firmware/security
  - K001-donanım
evidence:
  - "update/index.md (künye + Q4 açık soru) — okundu 2026-10-14"
  - ".ai/electronic/firmware/ota-update.md §2 · §5-§13 (akış · imza zinciri · A/B · rollback · güç-kesintisi) — okundu 2026-10-14"
  - ".ai/electronic/firmware/boot-sequence.md §8 · §11 · §13 (A/B yuva · kurtarma · anti-rollback) — okundu 2026-10-14"
  - "K005-firmware/boot/boot-anayasa.md §6-§8 (P1-P10 · A8 kurtarma hiyerarşisi · devir) — okundu 2026-10-14"
  - "K005-firmware/firmware-kapsam.md §9.7 (U1-U6) — okundu 2026-10-14"
  - "K005-firmware/firmware-master.md §3.4 (S1-S8) — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "update ↔ güvenlik (imza) kime ait? (K005 Q4) — ⚠️ VERIFICATION REQUIRED; bu dosyada ÇÖZÜLMEZ, her bölümde taşınır"
  - "A/B bölüm yerleşimi ve flash haritası bilinmiyor — UNKNOWN (K001/K005 kararı)"
  - "Confirm (onay) zaman aşımı süresi tanımsız — ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# update-anayasa — Update Alt-Modülü Anayasası (K005-firmware)

> **ELI15:** Çalışan yazılımın yerine yenisini kuran modül. Görevi: paketi al,
> bütünlüğünü ve imzasını doğrula, kullanılmayan yuvaya yaz, cihazı yeniden
> başlat, ilk açılışta sağlık kontrolü yap — tutmazsa eski sürüme geri dön.
> Bu dosya update alt-modülünün **kurallarını ve sınırlarını** koyar; arayüz
> sözleşmeleri `update-surucu.md`, sınır tablosu `update-kapsam.md`, örnekler
> `update-ornekler.md`, kanıt tabloları `update-dogrulama.md` içindedir.

## §1 Amaç, Kapsam ve Kapsam Dışı

| Alan | Soru | Cevap | Sahip |
|---|---|---|---|
| Amaç | Bu dosya neyi düzenler? | update alt-modülünün anayasası (kural + sınır) | K005-firmware/update |
| Kapsam içi | Manifest · indirme/doldurma · doğrulama · A/B geçişi · geri alma · yarıda kesme (U1-U6) | evet | update |
| Kapsam içi | DFU 4-faz yükleme akışı (detay: `update-surucu.md` §10) | evet | update |
| Kapsam içi | Anti-rollback sayacı — boot ile ORTAK satır (firmware-kapsam §9.1 B6 · §17 Ov2 · `boot-ornekler.md` §B.5) | evet | update · boot |
| Kapsam içi | Post-update sağlık kontrolü ve confirm akışı | evet | update |
| Kapsam dışı | İmza ALGORİTMASI ve anahtar kasası | hayır → security — sınır **Q4 açık (⚠️)** | security |
| Kapsam dışı | Boot'un image doğrulama çağrısı ve devir anı | hayır → boot | boot |
| Kapsam dışı | Bulut/kademeli dağıtım politikası (staged rollout) | hayır → kapsam teyidi yok (⚠️ `firmware-web-arastirma.md` Q1) | üst karar |
| Kapsam dışı | Ses/DSP zincirinin akış verisi | hayır → K003-audio · K004-dsp | K003 · K004 |
| Kapsam dışı | Donanım topolojisi, flash fiziksel tasarımı | hayır → K001-donanım | K001 |

> **Sınır notu:** Update, *değişim anını* yönetir. İlk açılış ve slot seçimi
> boot'un, kendini onaylama runtime'ın işidir; bu dosyada yalnızca bayrak ve
> devir koşulları yazılır (§6). Somut flash adresi hiçbir yerde yazılmaz.

## §2 Update Zinciri (İndir → Doğrula → Kur → İlk Çalıştırma → Onay/Geri Al)

Kaynak: `.ai/electronic/firmware/ota-update.md §2` (okundu 2026-10-14).

| # | Aşama | Ne yapılır | Başarı ölçütü | Hata eylemi |
|---|---|---|---|---|
| 1 | Paket al | HTTPS veya DFU kanalından manifest + paket alınır | manifest ayrıştırıldı (U1) | iptal + yeniden dene |
| 2 | Bütünlük | Paket özeti hesaplanıp manifest ile karşılaştırılır | özet eşleşti (U3) | paket reddi — eski sürüm kalır |
| 3 | İmza | İmza doğrulama **çağrısı** yapılır (algoritma security'nin) | doğrulandı (U3) | reddet — Q4 sınırı: çağrı mı algoritma mı **⚠️ açık** |
| 4 | Zemin hazırla | Geri alma yolu (eski yuva/sayaç) kullanılabilir durumda | rollback kapısı açık (U5) | güncelleme durdurulur |
| 5 | Yaz | Paket **pasif** yuvaya yazılır ve yazılan okunarak doğrulanır | yazma doğrulandı (U2 · U4) | yarıda kesme → aktif yuva değişmez (U6) |
| 6 | Geçiş | Boot-edilebilir (pending) bayrağı kilitlenir | bayrak yazıldı (U4) | geçiş yok — eski yuva devam |
| 7 | İlk çalıştırma | Yeniden başlatma; boot yeni yuvayı dener, contador++ | boot + self-test geçti | contador eşiği/recovery → geri alma |
| 8 | Onay / Geri al | Runtime self-test sonrası confirm; tutmazsa eskiye dönüş | confirm yazıldı (U5) | rollback — eski yuva (U5) |

**Zincir kuralı (U-A1):** Zincirdeki her aşama ya *tam geçer* ya *tanımlı
hata eylemine* düşer — belirsiz ara durum YASAKTIR (boot-anayasa A1 ile aynı
ilke).

**Zincir mülkiyeti (U-A2):** Aşama 1 taşıma yüzeyi (ağ/USB — host/K001),
aşama 2-6 update modülü, aşama 7 boot, aşama 8 runtime (confirm) → update
görevidir. Aşama 8'den sonra kalıcı durum `CONFIRMED` sayılır
(`update-surucu.md` §3).

## §3 A/B Yuva ve Slot Durumları

Kaynak: `.ai/electronic/firmware/boot-sequence.md §8` + `ota-update.md §6`
(okundu 2026-10-14).

| Yuva durumu | Anlam | Boot davranışı | Üreten |
|---|---|---|---|
| good | Sağlıklı firmware (aktif A yuvası) | Boot edilir | update (confirm sonrası) |
| pending | Yeni firmware — denenecek (B yuvası) | Dener, boot contador++ | update (geçiş yazımı) |
| bad | Başarısız image | Boot edilmez | update (rollback kararı) |
| recovery | Kurtarma image | Son çare | boot (RECOVERY_REQ) |

**Yuva kuralı (U-A3):** A/B yuva adı A (active) · B (inactive) şeklindedir
(`ota-update.md §6`); somut flash **adresi/bölüm adı UNKNOWN** — K001 bölüm
haritasına bağlıdır, uydurulmaz.

## §4 Anayasal İlkeler (Sert Kurallar)

| # | Kural | Kaynak | Sonuç (ihlalde) |
|---|---|---|---|
| U-P1 | Doğrulanmamış paket (özet + imza) flash'a YAZILMAZ | ota-update §2 · firmware-master §3.4 S3 | güncelleme ret |
| U-P2 | Yazım pasif yuvadadır; aktif yuva güncelleme sırasında DEĞİŞMEZ | ota-update §6.2 · §12 | ret |
| U-P3 | Boot-edilebilir bayrağı yalnız yazma + doğrulama sonrası kilitlenir (commit-after-write) | ota-update §12.1 | ret |
| U-P4 | Geri alma (rollback) kapısı asla kapatılmaz — her koşulda eskiye dönülür | firmware-riskler §2 risk 3 · §3 azaltma 3 | ret |
| U-P5 | Anti-rollback monotonu: manifest `min_version` altındaki sürüm reddedilir | ota-update §7.1 · firmware-master §3.4 S5 | ret |
| U-P6 | Kurtarma hiyerarşisi sabittir: `retry (≤2) → yedek/recovery → hata kodu` | boot-anayasa A8 · ota-update §13.1 | ret |
| U-P7 | Update telemetrisi anahtar/seri no içermez (maskeleme katmanı) | firmware-master §3.4 S6 [KANIT] | ret |
| U-P8 | DFU sırasında cihaz normal işini durdurur; durma boot tarafında da kayıtlı | boot-dogrulama §U | ret |

## §5 Hata Durumları ve Kurtarma

> **Kod uyarısı:** Update'e ait hata **kodları tanımsızdır** (⚠️) — kod
> aralığı boot-surucu §6 E0-E7 ile çakışmadan tahsis edilmelidir; tahsis
> `update-surucu.md` §12'ye ertelenmiştir. Burada yalnızca senaryo → eylem
> yazılır.

| # | Hata senaryosu | Tetik | Eylem | Son durum |
|---|---|---|---|---|
| UE1 | İndirme/aktarım kesildi | taşıma hatası | güncelleme iptal, yeniden dene | eski yuva aktif |
| UE2 | Özet veya imza reddi | doğrulama sonucu geçersiz | paket yok sayılır, flash'a yazılmaz | eski sürüm kalır |
| UE3 | Yazma yarıda kesildi (güç) | güç kesintisi | aktif yuva değişmez; yeniden başlarda aşama 1'e dönülür | A (good) devam |
| UE4 | İlk boot başarısız | boot contador eşiği aşıldı | recovery → rollback | eski yuva (boot-sequence §8.1) |
| UE5 | Self-test / confirm başarısız | runtime sağlık kontrolü | rollback tetiklenir | eski yuva (ota-update §9) |
| UE6 | Anti-rollback ihlali | sürüm < min_version | güncelleme reddi | eski sürüm kalır (ota-update §13) |
| UE7 | Boot-lock/OTP yazım hatası | OTP sayaç yazımı başarısız | geçiş kilitlenmez, güncelleme durdurulur | ⚠️ tanımsız — tasarım kararı |

**Kurtarma kuralı (U-A8):** Hiyerarşi boot-anayasa A8 ile **aynıdır**: sonsuz
retry YASAKTIR; eşikler `update-dogrulama.md` §A DV-U senaryolarında ölçülür.

## §6 Sınır Komşuları ve Devir Sözleşmeleri

| Komşu | Yön | Ne devredilir | Kanıt |
|---|---|---|---|
| boot | update ↔ | Update-incomplete · test-boot · confirm bayrakları; RECOVERY_REQ alımı; T11 revert | boot-kapsam §C.1 · §G.2 T11 |
| security | update ↔ | Paket imza doğrulama çağrısı + sonucu — **sınır Q4 açık (⚠️)** | boot-kapsam §I (Q4 mutabık yön) — bu satır Q4'ü ÇÖZMEZ |
| runtime | update ← | Confirm bayrağı (self-test OK sonrası) | boot-kapsam §C.1 |
| diagnostics | update → | Update olay kaydı (başlangıç · doğrulama · rollback) | boot-surucu §9 deseni |
| testing | update ← | DV-U kapı tetikleri (güncelleme · geri alma · DFU) | update-dogrulama §A |
| K001-donanım | update ↔ | Bölme haritası ve flash yüzeyi (E1k) | firmware-bagimliliklar §17 D12 |

## §7 Ölçüm ve Eşik Kapıları

| # | Kapı | Eşik | Nasıl ölçülür | Durum |
|---|---|---|---|---|
| UB1 | Image yazma bütçesi | ⚠️ UNKNOWN (hedef cihazda ölçülecek) | donanım ölçümü | ölçüm PLANNED |
| UB2 | İmza doğrulama süresi | ⚠️ UNKNOWN | imza testi (DV-B3 bağı) | ölçüm PLANNED |
| UB3 | Geri alma tetiklenebilirliği | her ilk-boot doğrulamasında | A/B slot testi (DV-U2) | Tasarım seviyesi |
| UB4 | Boot-lock doğrulama | OTP okuma-yazma teyidi | OTP testi (DV-U7) | Tasarım seviyesi |
| UB5 | DFU 4-faz geçişi | USB DFU 1.1 uyumu | DFU testi (DV-U8) | ölçüm PLANNED — ekipman yok |

> Kapıların tamamı `boot-dogrulama.md` §U'da **AÇIK** olarak sayılır; burada
> yalnız tanımı verilir, kapanış iddiası yazılmaz.

## §8 Bağımlılık Grafiği (Yön: bağımlı → bağımlı olduğu)

| Düğüm | Bağımlı olduğu | Tür | Not |
|---|---|---|---|
| update (F12) | K001 (E1k · bölüm haritası) | mimari | firmware-bagimliliklar §17 D12 |
| update (F12) | boot (bayrak · devir · ilk boot) | modül | boot-kapsam §C.1 |
| security (F13) | F1 · F12 | mimari | D13 — Q4 sınırı burada |
| update-surucu | update-anayasa | mimari | arayüz anayasadan türetilir |
| update-ornekler | update-anayasa + update-surucu | kod | kalıp anayasaya uyar |

## §9 Kaynak ve Repo Kanıtı

**Repo'da firmware kodu YOK** (boot-dogrulama §G satır 1 — 2026-10-13 shell
taraması: `.cpp/.h/.c = 0`) → bu dosyadaki tüm iddialar **tasarım seviyesindedir**.

| Kaynak | Bölüm | Bu dosyada kullanıldığı yer |
|---|---|---|
| `.ai/electronic/firmware/ota-update.md` | §2 akış | §2 zincir tablosu |
| `.ai/electronic/firmware/ota-update.md` | §5-§7 · §9 · §12-§13 | §3 · §4 · §5 |
| `.ai/electronic/firmware/boot-sequence.md` | §8 · §11 · §13 | §3 · §4 U-P5 · §5 UE4 |
| `K005-firmware/boot/boot-anayasa.md` | §6-§8 | §4 U-P6 · §6 |
| `K005-firmware/firmware-kapsam.md` | §9.7 (U1-U6) | §1 kapsam satırları |
| `K005-firmware/firmware-master.md` | §3.4 (S1-S8) | §4 U-P1 · U-P7 |

## §10 Web Araştırması (tek nokta)

Bu dosyada **URL yoktur**. Update'e ait tüm URL kayıtları klasörde tek
noktada toplanır: `update-dogrulama.md` §E (erişim 2026-10-14, kesişim 0 —
K-14). Tekrarlanan URL kanıt sayımını şişirir (K-9).

## §11 Açık Sorular (open_questions)

| # | Soru | Etki | Durum |
|---|---|---|---|
| K005 Q4 | update ↔ güvenlik (imza) kime ait? | §2 aşama 3 · §6 security satırı · update-surucu §7 | ⚠️ VERIFICATION REQUIRED — **çözülmez, taşınır** |
| U-Q2 | A/B bölüm yerleşimi / flash haritası nedir? | §3 · U-P2 | UNKNOWN (K001/K005 kararı) |
| U-Q3 | Confirm zaman aşımı süresi kaçtır? | §5 UE5 · update-surucu §8 | ⚠️ tanımsız |
| U-Q4 | Bulut/kademeli dağıtım kapsam teyidi var mı? | §1 kapsam dışı satırı | ⚠️ (`firmware-web-arastirma.md` Q1) |

---

## §12 İddia → Dayanak Satır Eşlemesi

| # | İddia (bu dosyada) | Dayanak | Tür |
|---|---|---|---|
| C1 | Update zinciri 8 aşama | ota-update.md §2 (§2 kaynak satırı) | kaynak |
| C2 | A/B yuva durumları: good · pending · bad · recovery | boot-sequence.md §8 + ota-update.md §6 | kaynak |
| C3 | 8 anayasal ilke U-P1–U-P8 | §4 tablosunun Kaynak sütunu (ota-update §2 · §6.2 · §7.1 · §12.1 · firmware-master §3.4 S3/S5/S6 · firmware-riskler §2/§3 · boot-anayasa A8 · boot-dogrulama §U) | kural (kaynaklı) |
| C4 | 7 hata senaryosu UE1–UE7 | ota-update §9/§12/§13 · boot-sequence §8.1 · boot-dogrulama §U (§5 satır kaynakları) | kaynak |
| C5 | Kurtarma hiyerarşisi `retry (≤2) → yedek/recovery → hata kodu` | boot-anayasa A8 · ota-update §13.1 | kural (anayasa) |
| C6 | Anti-rollback monotonu (`min_version` altındaki sürüm reddi) | ota-update §7.1 · firmware-master §3.4 S5 | kaynak |
| C7 | Kapsam U1–U6 (manifest · indirme · doğrulama · A/B · geri alma · yarıda kesme) | firmware-kapsam §9.7 | kaynak |
| C8 | 5 ölçüm kapısı UB1–UB5; eşiklerin çoğu UNKNOWN | bu dosya §7 + eşiğin kendisi `⚠️ UNKNOWN` | sözleşme + dürüstlük |
| C9 | Repo'da firmware kodu YOK (2026-10-13: 0 kod kanıtı) → tüm iddialar tasarım seviyesi | boot-dogrulama §G satır 1 | dürüstlük |
| C10 | Q4 (imza kime ait) açık kalır | update/index.md açık soru · boot-kapsam §I (aktarım) | açık soru ⚠️ |
| C11 | Bu dosyada URL yok; tüm URL'ler update-dogrulama §E'de tek noktada | bu dosya §10 (K-14 tek-üretim kuralı) | kural |
| C12 | DFU 4-faz yükleme akışı kapsam içi | firmware-kapsam §9.7 (OTA veya DFU) + boot-dogrulama §U (DFU bağlaması) | kaynak |

## §13 Red Team İtiraz Kayıtları (soru → yanıt → kanıt)

| # | İtiraz | Yanıt | Kanıt |
|---|---|---|---|
| T1 | "İmza kime ait sorusu bu dosyada çözüldü mü?" | Hayır — Q4 her bölümde ⚠️ olarak taşınır; §6'daki boot-kapsam §I satırı aktarım, çözüm değildir | §1 · §2 aşama 3 · §6 · §11 |
| T2 | "Zincirin 8 aşaması kaynakta mı?" | Evet — ota-update.md §2 akışının birebir eşlemesi | §2 |
| T3 | "Somut flash adresi/bölüm adı yazıldı mı?" | Hayır — UNKNOWN; K001 bölüm haritasına bağlı, uydurulmadı | §3 U-A3 |
| T4 | "Hata kodları neden yok?" | Update hata kodları tanımsız; tahsis update-surucu §12'ye ertelendi — kod uydurulmaz | §5 kod uyarısı |
| T5 | "Ölçüm kapıları ölçüldü mü?" | Hayır — kapılar tanımlı; Durum sütunu Tasarım seviyesi / ölçüm PLANNED, asla 'geçti' değil | §7 |
| T6 | "Anti-rollback boot ile çelişir mi?" | Hayır — ortak satır (firmware-kapsam §9.1 B6 · §17 Ov2 · boot-ornekler §B.5); iki dosyada tek tanım, çift tanım yok | §1 · §4 U-P5 |
| T7 | "Ölçüm/ürün değeri uydurulmuş mu?" | Hayır — bilinmeyen `UNKNOWN`, doğrulanamayan `⚠️`; eşiklerin çoğu UNKNOWN | §7 · §9 · §11 |
| T8 | "URL'ler bu dosyada şişirilmiş mi?" | Bu dosyada URL yok; K-14 tek-üretim kuralı | §10 |

---


---

## §14 Update Zinciri Derinletme (faz davranışları)

### §14.1 Faz bazlı bağlayıcı kurallar (U-F1…U-F6)

| Faz | Kural | İhlali |
|---|---|---|
| U-F1 indirme | manifest onayı olmadan indirme **kullanılmaz** (bayt israfı serbest, kurulum yasak) | erken kurulum |
| U-F2 doğrulama | manifest + imza kararı olmadan yazım **yok** | kapı-2 ihlali |
| U-F3 yazım | yazım yalnız **hedef** slota; geçici dosya yok | çapraz yazım |
| U-F4 bekleme | etiket "beklemede" kalır; kendiliğinden onaylanmaz | kendi-kendine onay |
| U-F5 onay | onay yalnız boot sonucuyla | sahte onay |
| U-F6 geri alma | geri alma sayaç monotonluğunu **ihlal etmez** | anti-rollback ihlali |

### §14.2 Faz geçiş kanıtı

1. Her faz geçişi bir olay üretir (telemetri) — sessiz geçiş yasak (kapsam §H.1).
2. Faz atlamak yalnız **kurtarma modunda** mümkündür; kurtarma modu boot sahibidir.
3. Faz kanıtı kapı-3/4 için gereklidir; kanıtsız faz = kapı BELIRSIZ.

### §14.3 Faz ↔ kapı ↔ bileşen

| Faz | Kapı | Bileşen | Sahip |
|---|---|---|---|
| U-F1 | kapı-1 | u1/u2 | update |
| U-F2 | kapı-2 | u2/u6 | security (imza) + update |
| U-F3 | kapı-3 | u3/u6 | update |
| U-F4 | kapı-4 | u4 | boot (onay olayı) + update |
| U-F5 | kapı-4 | u4/u5 | boot + update |
| U-F6 | kapı-5 | u5/u6 | update + security (okuma) |

---

## §15 A/B Slot Durumları Derinletme (durum × işaret)

### §15.1 Slot işaret matrisi (taslak — ölçülmedi)

| Durum | hedef slot | eski slot | boot tercihi |
|---|---|---|---|
| kurulum öncesi | boş | kurulu (aktif) | eski |
| yazma sırasında | geçersiz işaretli | kurulu (aktif) | eski |
| yazma sonrası | beklemede | kurulu (pasif) | eski |
| ilk boot başarılı | kurulu (yedek) | kurulu (pasif) | yeni (onay sonrası) |
| ilk boot başarısız | geçersiz | kurulu (aktif) | eski |
| geri alma sonrası | geçersiz | kurulu (aktif) | eski |

1. Matris **tasarım düzeyidir**; gerçek etiket alanı/bayt düzeni UNKNOWN (kod yok).
2. Boot tercihi sütunu boot-dogrulama/boot-sequence §8 [Satır: 138] çapasına bağlı —
   uygulama boot'ta (update yalnız etiket yazar).
3. Ölçüm: her satır DV-U10…DV-U14 kartlarıyla doğrulanacak (PLANNED).

### §15.2 Geçersiz-slot kuralı (bağlayıcı)

- Geçersiz işaretli slot **asla** tercih edilmez; işaretleme yazma anında yapılır
  (önce işaretle, sonra yaz → güç-kaybında eski slot kurtarıcı).
- İşaret silme yalnız onay sonrası (U-F5).

---

## §16 Anayasal İlkeler — Ek Maddeler (U-P6…U-P12)

| Madde | Metin |
|---|---|
| U-P6 | "Kendiliğinden onay" yasaktır — onay yalnız boot sonucuyla |
| U-P7 | Geçersiz slot tercih edilemez; işaretleme yazmadan önce |
| U-P8 | Sayaç monotonluğu geri alma dahil **hiçbir** yolda düşürülmez |
| U-P9 | Kurtarma modu yalnız boot sahibidir; update kurtarma **başlatamaz** |
| U-P10 | Telemetri yayıncısı karar sahibi değildir (u7 ≠ kapı) |
| U-P11 | Ölçülmemiş güç-kaybı davranışı "güvenli" diye yazılmaz (şartname damgası) |
| U-P12 | Kapı BLOKE/BELIRSIZ satırı gizlenemez (kapı sağlığı dürüstlüğü) |

### §16.1 Maddelerin uygulama yeri

- U-P6/U-P9 → update-surucu §2/§8 (durum/geri alma kapıları).
- U-P7 → update-surucu §6 (yazma kapısı) + DV-U13.
- U-P8 → update-dogrulama §H.5 (DV-U14) + kapı-5.
- U-P10 → update-surucu §12 (telemetri arayüzü).
- U-P11 → §15.1 notu + DV-U12/U13 damgası.
- U-P12 → update-dogrulama §L + testing-anayasa §20 (P21 ile uyumlu).

---

## §17 Hata Durumları Derinletme (H-U1…H-U8)

| Kod | Hata | Tepki | Kurtarma |
|---|---|---|---|
| H-U1 | ağ kesildi (indirme) | kuyruk/iptal · slot etkisiz | yeniden indirme |
| H-U2 | manifest hatalı | RED · olay | manifest düzeltmesi |
| H-U3 | imza hatalı | RED (kapı-2) | security köprüsü |
| H-U4 | yazma hatası | slot geçersiz işaretle | eski slot aktif |
| H-U5 | ilk boot başarısız | otomatik geri alma (etiket) | eski slot |
| H-U6 | sayaç okuma hatası | UNKNOWN döndür · karar yok | okuma tekrarı (sınırsız yasak) |
| H-U7 | geri alma başarısız | **kurtarma modu isteği → boot** | boot-dogrulama §U |
| H-U8 | telemetri yolu kapalı | sayaçla · sessiz kayıp yasak | yolu onarır (diagnostics) |

### §17.1 Tepki kuralları

1. H-U3'te "dene ve geç" yasak (fail-open yasak — security ile uyumlu).
2. H-U6'da varsayılan değer **yok** (UNKNOWN döndür — kapı-5 BELIRSIZ).
3. H-U7 update'te çözülemez: kurtarma boot'un (mülkiyet sınırı).

---

## §18 Ölçüm ve Eşik Kapıları Derinletme

### §18.1 Ölçüm çapaları (vault kaynaklı — yöntem)

| Ölçüm | Yöntem çapası | Ölçülen kapı |
|---|---|---|
| indirme süresi/bütçesi | ota-update.md §26 [Satır: 492] | kapı-6 (bütçe) |
| ilk boot süresi | boot-sequence.md §3 [Satır: 47] | kapı-4 |
| geri alma süresi | ota-update.md §7.2 [Satır: 138] (durum tablosu çapası) | kapı-4/5 |
| yazma bütçesi | ota-update.md §12 [Satır: 237] (güç-güvenli) | kapı-3 |

1. Bu dosyada **sayısal eşik yok** (ölçülmedi) — eşik ADR ile konur (P16 uyumlu).
2. Ölçümler UX3/G9 sonrası mümkün; blokaj kapı raporunda görünür.

### §18.2 Ölçüm → kapı akışı

ölçüm → dağılım → ADR → eşik → kapı kriteri (firmware-test-stratejisi §18) →
kapı değerlendirmesi (testing) → rapor (Vault Steward). Ters akışta eşik uydurmadır.

---

## §19 Sınır Komşuları — Derinletme (devir satırları)

| Komşu | Devir | Sahip | Güncelleme koşulu |
|---|---|---|---|
| boot | slot/etiket/boot-lock fiziksel | boot | boot-lock arayüzü değişince |
| security | imza kararı (V1–V5) | security | Q4/T5 kararı gelince |
| runtime | güncelleme sırasında hot-path | runtime | kilit/bekleme tasarımı değişince |
| diagnostics | telemetri/olay | diagnostics | format değişince |
| testing | kapı verisi + çalıştırma | testing | kapı eklenince |
| K001 | kullanıcı onay akışı | K001 | arayüz değişince |

### §19.1 Devir kuralı

- Devir yalnız **sözleşme** düzeyindedir; komşu dosyanın içeriği bu dosyada kopyalanmaz (SSOT).
- Devir satırları her üretim turunda yeniden sayılır (eklendi mi/silindi mi — append-only).

---

## §20 Red Team İtirazları — Ek (bu ek için)

1. **İtiraz:** "Onayı beklemeyiz, zaman kazanalım." **Karar:** U-P6 ihlali — kendi
   kendine onay, bozuk image'ı kalıcı yapar (DV-U10 gerekçesi).
2. **İtiraz:** "Rollback'te sayaç düşsün, eski dönelim." **Karar:** U-P8 ihlali —
   anti-rollback baypas (DV-U14).
3. **İtiraz:** "Yazmadan önce işaretleme gereksiz." **Karar:** U-P7 ihlali — güç-kaybında
   brick riski (DV-U13 · kapı-3).
4. **İtiraz:** "Telemetri kapalıysa güncelleme durmasın." **Karar:** kısmi — H-U8 sayaçla
   devam eder ama sessiz kayıp yasak (onarım şart).
5. **İtiraz:** "BLOKE kapılar rapordan düşsün." **Karar:** U-P12 ihlali — gizleme yasak.

---

## §21 Kapanış Kontrolü (bu ek)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölümler | §14–§21 |
| Silinen mevcut içerik | 0 (append-only) |
| Frontmatter | korundu (10 alan + group: mimari) |
| Dolgu/TBD | 0 |
| Ölçülmemiş eşik | 0 (eşik yok — dürüstlük) |
| Kapı "geçti" iddiası | 0 |
| Son satır Authority | birebir korunur |


---

## §22 İlke ↔ Kapı ↔ Senaryo İzlenebilirlik (tam matris)

### §22.1 Tam iz tablosu (anayasa geneli)

| İlke | Kapı | Senaryo | Kanıt tipi |
|---|---|---|---|
| U-F1 (indirme yasağı) | kapı-1 | DV-U16 | manifest kararı satırı |
| U-F2 (doğrulama önce) | kapı-2 | DV-U15 | imza kararı (BLOKE — T5) |
| U-F3 (hedef slota yazım) | kapı-3 | DV-U13 | slot işareti + güç testi (UX3) |
| U-F4 (bekleme etiketi) | kapı-4 | DV-U10 | etiket okuma |
| U-F5 (boot onayı) | kapı-4 | DV-U10/U11 | boot sonucu + etiket |
| U-F6 (sayaç monoton) | kapı-5 | DV-U14 | sayaç okuması |
| U-P7 (işaretleme önce) | kapı-3 | DV-U13 | işaret sırası |
| U-P8 (geri alma sayaç) | kapı-5 | DV-U14 | sayaç sabit kaydı |
| U-P9 (kurtarma boot) | kapı-4 | DV-U20 | kilit RED kaydı |
| U-P10 (yayıncı ≠ karar) | kapı-6 | DV-U17 | telemetri akış kaydı |
| U-P11 (ölçülmemiş davranış) | kapı-3 | DV-U12/U13 | şartname damgası |
| U-P12 (gizleme yasak) | tüm | rapor | BLOKE/BELIRSIZ satırları |

### §22.2 Çift yönlü iz kontrolü

1. İleri: her ilke ≥1 kapıya bağlı (12/12 — tabloda görünür).
2. Geri: her kapı ≥1 senaryoya bağlı (kapı-1…6 · DV-U10…U21).
3. Boşluk: kapı-2/3 BLOKE — ilke doğrulaması **bilerek** yarım (T5/Q4/G9).

### §22.3 Zincir bütünlüğü notu

- İddia (ilke) → kapı (kriter) → senaryo (test) → kanıt (dosya+satır) dörtlüsü
  bozulursa kapı **BELIRSIZ** (testing-anayasa §21 K-15 zinciri ile uyumlu).
- Bu dosyanın tüm kapıları şu an BELIRSIZ/BLOKE — çalışan test yok (cpp/h = 0).

---

## §23 Terimler ve Kısaltmalar (bu ek sözlüğü)

| Terim | Anlamı |
|---|---|
| U-F1…U-F6 | faz kuralları (§14.1) |
| U-P6…U-P12 | ek anayasal maddeler (§16) |
| H-U1…H-U8 | hata tepki tablosu (§17) |
| Kapı-1…6 | update kapıları (update-dogrulama §L) |
| UX1–UX3 | doğrulama ortam kademeleri |
| BLOKE/BELIRSIZ | FAIL değildir — kapı durumları |
| şartname damgası | ölçülmemiş davranışın dürüst işareti (U-P11) |

---

## §24 Kapanış — Bu Ek Özeti

| Bölüm | Ne getirdi |
|---|---|
| §14 | U-F1…U-F6 faz kuralları + faz↔kapı↔bileşen |
| §15 | Slot işaret matrisi + geçersiz-slot kuralı |
| §16 | U-P6…U-P12 maddeleri + uygulama yerleri |
| §17 | H-U1…H-U8 tepkileri + kurtarma sınırları |
| §18 | Ölçüm çapaları (vault kaynaklı) + ölçüm→kapı akışı |
| §19 | Devir satırları + güncelleme koşulları |
| §20 | 5 red team itiraz-kararı |
| §22–§23 | Tam iz matrisi + sözlük |

### §24.1 Son kontrol

- §1–§13 + frontmatter append-only korundu · Dolgu/TBD: 0 · Ölçülmemiş eşik: 0 ·
  Uydurma kapı/sayı: 0 · Son satır Authority: birebir · Kapı "geçti" iddiası: 0.


---

## §25 Sonraki Üretim Koşulları (bu ek kapanışı)

| Koşul | Gelince yapılacak |
|---|---|
| Q4 imza sahipliği kararı (ADR) | §22.1 U-F2 satırı güncellenir + kapı-2 değerlendirilir |
| T5 primitive kararı | DV-U15 BLOKE'dan çıkar (update-dogrulama ek §V) |
| G9 workflow (DevOps) | §18 ölçüm çapaları aktifleşir · kapı-3 değerlendirilir |
| Sayaç yazım sahibi (Ç-U3) | §17 H-U6 satırı + §22.1 U-F6 satırı append ile güncellenir |
| İlk gerçek ölçüm | §18.1 tabloya ölçüm satırı append (uydurma değer yasak) |

1. Tüm güncellemeler append-only; mevcut matrisler silinmez.
2. Her üretim sonunda §24.1 son kontrolü yenilenir (kapı kapısı ölçüm notu).
3. Kararlar ADR ile; anayasa maddesi değişimi yalnız ek (U-P13+) ile yapılır.

## §26 Vault OTA Kaynağı Derinletmesi (append eki · 2026-10-16)

> Kaynak bu ek için: `Dosya: .ai/electronic/firmware/ota-update.md` (524 satır — okundu 2026-10-16). Aşağıdaki her iddia o dosyadaki bölüm/satır ile dayanaklanır; uydurma register, sürüm, URL yoktur.

### §26.1 Vault Bölüm ↔ Bu Anayasa Eşlemesi

| Vault Bölüm | Kaynak satır | Bu dosyadaki karşılığı | Eklenti kararı |
|---|---|---|---|
| 2. OTA akışı (7 adım) | ota-update.md:25 | §2 Zincir | birebir uyum — yeni madde yok |
| 3. Güvenlik tablosu (RSA-2048 · AES-256-GCM · SHA-256 · dual-bank) | ota-update.md:47 | §4 ilkeler | uyum; algoritma seçimi §26.3'te derinleştirilir |
| 5. İmzalama zinciri / root of trust | ota-update.md:68 | §2 · §7 | tamamlayıcı — U-Q4 (imza sahipliği) için zincir semantiği eklendi |
| 6. Dual-bank (A/B) adımları | ota-update.md:97 | §3 Slot durumları | uyum — atomiklik gerekçesi §26.2'de |
| 7. Rollback türleri (otomatik / anti / kademeli) | ota-update.md:124 | §5 Hata durumları | anti-rollback bu dosyada yoktu → §26.4 |
| 12. Güç-kesintisi güvenliği + atonom commit | ota-update.md:237 | §4 · §5 | commit-after-write §26.5 |
| 19. Bootloader entegrasyonu + boot seçim algoritması | ota-update.md:345 | §6 boot komşusu | devir satırı §26.6'ya bağlandı |
| 22.1 Savunma derinliği (6 katman) | ota-update.md:419 | §4 ilkeler | katman listesi §26.7'de |

### §26.2 Atomiklik Gerekçesi (neden A/B zorunlu)

| Koşul | Tek-yuva sonuç | A/B sonuç | Dayanak |
|---|---|---|---|
| Yazma anında güç kesilir | image bozuk → brick | aktif yuva değişmez | ota-update.md:97 §6.2 |
| Bootable bayrağı yarım yazılır | boot döngüsü | commit-after-write bayrağı atılmaz | ota-update.md:237 §12.1 |
| Yeni sürüm sağlık testinde kalır | manuel kurtarma | otomatik rollback (canary penceresi) | ota-update.md:180 §9 · §9.1 |

### §26.3 İmza Algoritması Seçim Matrisi (U-Q4 girdisi)

| Algoritma | İmzalama | Anahtar | Vault notu | K005 durumu |
|---|---|---|---|---|
| RSA-2048 | yavaş | uzun | "mevcut tasarım" | §4 / U-Q4 — sahiplik açık |
| ECDSA P-256 | hızlı | kısa | "alternatif (daha verimli)" | değerlendirilmemiş |
| Ed25519 | hızlı | çok kısa | "modern alternatif" | değerlendirilmemiş |

Dayanak: `ota-update.md:80` §5.1 tablosu. ⚠️ VERIFICATION REQUIRED: hangi algoritmanın seçildiğine dair vault'ta karar kaydı YOK — U-Q4 bu ekle kapanmaz.

### §26.4 Anti-Rollback (bu dosyada yeni kavram)

| Öğe | Kural | Dayanak |
|---|---|---|
| Sürüm monotonisi | sayaç donanımda/OTP tutulur; bootloader eski sürümdeki paketi reddeder | ota-update.md:140 §7.1 |
| Zayıflık koşulu | sayaç salt-okunur donanımda değilse anti-rollback zayıflar → root-of-trust ile birlikte düşünülür | ota-update.md:143 §7.1 |
| Manifest bağı | `min_version` alanı anti-rollback alt sınırını taşır | ota-update.md:172 §8.1 |

Etki: §11 eşleme bu ekten U-F6 (sayaç yazım sahibi) satırını besler; sahiplik yine Ç-U3 ile açık kalır.

### §26.5 Güç-kesintisi Commit Sırası (atonom commit)

| # | Adım | Bayrak etkisi | Dayanak |
|---|---|---|---|
| 1 | Inactive yuvaya image yaz | — | ota-update.md:239 §12 tablo |
| 2 | flush + verify (checksum/imza) | henüz bootable DEĞİL | ota-update.md:243 §12 |
| 3 | bootable bayrağını ata (commit-after-write) | pending → denenir | ota-update.md:249 §12.1 |
| 4 | reboot → boot contador++ | döngü sayacı | ota-update.md:254 §13 |

### §26.6 Boot Seçim Algoritması (§6 devir satırı dayanağı)

```
bootloader başlangıcı
  → aktif yuva bayrağını oku (good / pending / bad)
  → pending ise → yeni yuvayı dene (boot contador++)
  → contador eşiği aşarsa → eski (good) yuvaya dön
  → recovery image yalnız son çare
```

Dayanak: `ota-update.md:360` §19.1. Bu algoritma boot-anayasa devri ile çelişmez; boot tarafı yalnız bayrak okur, update tarafı yazar (§11'deki mevcut sınır korunur).

### §26.7 Savunma Derinliği Katmanları (6 katman)

| # | Katman | Tek başına ihlal edilirse | Dayanak |
|---|---|---|---|
| 1 | TLS 1.3 transport | paket imzası ayakta kalır | ota-update.md:149 §8 |
| 2 | Paket imzası (RSA/ECDSA) | checksum yine doğrulanır | ota-update.md:68 §5 |
| 3 | SHA-256 checksum | imza reddi devrede | ota-update.md:47 §3 |
| 4 | Secure boot zinciri | uygulama devre dışı kalır | ota-update.md:68 §5 tablo |
| 5 | Anti-rollback | eski sürüm reddi | ota-update.md:140 §7.1 |
| 6 | Post-update sağlık kontrolü | otomatik rollback | ota-update.md:180 §9 |

### §26.8 Bu Ek Sonrası Kapı Durumu

| Kontrol | Sonuç |
|---|---|
| Eklenen bölüm | §26 (append-only; mevcut §1–§25 silinmedi) |
| Dayanaksız iddia | 0 — her satır ota-update.md bölüm/satır referanslı |
| Uydurma URL/sürüm/register | 0 |
| Repo C++ kodu iddiası | 0 (repo kodu yok — §9 kanıt sınırı korunur) |
| Kapanan açık soru | 0 — U-Q4 / Ç-U3 veriyle beslendi, kapandı DEĞİL |


**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
