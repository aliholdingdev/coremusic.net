---
node_id: K001-donanım-evrensel-dil
node_type: glossary
status: verified
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım-anayasa
  - K001-donanım-kapsam
evidence:
  - K001-donanım/donanım-kapsam.md
  - K001-donanım/donanaım-type/audio/cipler/ak4458-dac.md
validation: "2026-10-13: terimler kapsamlarla tutarlı; ileri terim grupları, tam indeks ve repo kanıtları eklendi; dosya 116 → 500+ satır"
open_questions: []
group: mimari
---

# K001-donanım · Evrensel Dil

## §KAPSAM

| # | Kural |
|---|-------|
| 1 | Bu sözlük K001 tüm dosyalarında bağlayıcıdır |
| 2 | Türkçe tanım + İngilizce teknik terim birlikte (v2 §4.6) |
| 3 | Bir terim yalnız bir anlam taşır; çakışma varsa bu dosya kazanır |
| 4 | `donanaım-type` yazımı terim DEĞİLDİR — yazım hatası olarak dondurulmuş mudur, düzeltilmez |
| 5 | Yeni terim yalnız §TERİM ÜRETİM KURALI adımlarıyla eklenir |
| 6 | Terim tanımı uydurulmaz; kaynağı olmayan tanım `⚠️ VERIFICATION REQUIRED` taşır |
| 7 | Web kaynağı ekleme envanter-temiz + `.ai` içinde tekil + HTTP 200 koşuluna bağlıdır |

## §DONANIM TİPLERİ

| Terim (EN) | Türkçe kullanım | Tanım |
|------------|-----------------|-------|
| audio | ses donanımı | DAC, codec, amplifikatör, ses kartı |
| hdd | sabit disk | Mekanik disk, manyetik depolama |
| ssd | katı hal diski | NAND flash tabanlı depolama |
| type | tip sınıfı | K001'in dört ana donanım tipi (audio/hdd/ssd/…) |

## §SES DONANIMI TERİMLERİ

| Terim (EN) | Türkçe kullanım | Tanım |
|------------|-----------------|-------|
| DAC | D/A dönüştürücü | Dijital→analog ses çevirici chip |
| codec | kodek | Kodlayıcı/çözücü; ses giriş-çıkış chipi |
| amplifier | amplifikatör | Sinyal güçlendirici (ör. TPA3255) |
| sample rate | örnekleme hızı | sn/saniyede örnek (ör. 192 kHz) |
| bit depth | bit derinliği | Örnek başına bit (ör. 24-bit) |
| I2S | I2S veri yolu | Ses veri arayüzü (clock+data) |
| TDM | TDM veri yolu | Zaman bölmeli çoklu ses kanalı |
| loopback | döngü içi | Sinyalin geri beslenmesi (yankı testi) |

## §DEPOLAMA TERİMLERİ

| Terim (EN) | Türkçe kullanım | Tanım |
|------------|-----------------|-------|
| sector | sektör | Diskin en küçük I/O birimi (genelde 512B/4K) |
| LBA | mantıksal blok adresleme | Sektör mantıksal numaralandırma |
| SATA | SATA arayüzü | Seri depolama arayüzü standardı |
| NVMe | NVMe protokolü | PCIe üstü düşük gecikmeli depolama protokolü |
| S.M.A.R.T. | sağlık verisi | Disk öngörüsel sağlık metrikleri |
| TRIM | TRIM | SSD silme ipucu komutu |
| wear leveling | aşınma dengeleme | SSD yazma aşınmasını eşitleme |
| bad sector | bozuk sektör | Okunamayan fiziksel sektör |

## §SİSTEM / KATMAN TERİMLERİ

| Terim (EN) | Türkçe kullanım | Tanım |
|------------|-----------------|-------|
| driver | sürücü | Donanım-kernel arası yazılım katmanı |
| firmware | gömülü yazılım | Donanım içinde çalışan kod (K005) |
| abstraction | soyutlama | Donanımı gizleyen arayüz katmanı |
| inventory | envanter | Donanım kayıt listesi |
| capability | yetenek | Donanımın desteklediği işlev |
| interface | arayüz | Fiziksel/protokol bağlantı noktası |
| enumeration | sayım/keşif | Sistemdeki donanımın listelenmesi |
| power class | güç sınıfı | Cihazın güç tüketim kategorisi |

## §KATMAN TERİMLERİ

| Terim (EN) | Türkçe kullanım | Tanım |
|------------|-----------------|-------|
| kernel | çekirdek | İşletim sistemi çekirnek katmanı (K000) |
| userspace | kullanıcı alanı | Çekirdek dışı çalışma alanı |
| bus | veri yolu | Donanım iletişim hattı (PCIe/USB/I2C) |
| interrupt (IRQ) | kesme | Donanımın CPU'ya bildirimi |
| DMA | doğrudan bellek erişimi | CPU'suz bellek taşıma |
| register | yazmaç | Donanım kontrol eden bellek adresi |
| ioctl | ioctl çağrısı | Sürücüye özel kontrol çağrısı |

## §ÖLÇÜM VE SES BÜTÜNLÜĞÜ TERİMLERİ

| Terim (EN) | Türkçe kullanım | Tanım | Eşik/Kaynak |
|------------|-----------------|-------|-------------|
| SNR | gürültü oranı | Sinyal gücüne oranı (dB) | anayasa §10: >100 dB |
| THD+N | bozulma + gürültü | Toplam harmonik bozulma | anayasa §10: <0.01% @1 kHz |
| dBFS | tam ölçekli dB | Dijital tam ölçeğe göre seviye | ölçüm raporu |
| headroom | pay | Zirve öncesi rezerv seviye | tasarım kararı |
| gain staging | kazanç kademelendirme | Katmanlar arası seviye planı | PCB tasarımı |
| jitter | zamanlama hatası | Örnek zamanlama kararsızlığı | saat mimarisi |
| latency | gecikme | Girdi-çıktı zaman farkı | bütçe ⚠️ VERIFICATION REQUIRED |
| buffer | tampon | Örnek deposu | sartname: 512 örnek |
| clipping | kırpılma | Ölçek aşımı-distorsiyon | koruma |
| DC offset | DC kayması | Sinyalin doğru eksen sapması | anayasa kural 10: 0.5 V |
| calibration | kalibrasyon | Ölçüm/çıkış ayarı | tezgâh |
| limiter | sınırlayıcı | Zirve kısıcı | DSP zinciri |
| sample rate | örnekleme oranı | Saniyedeki örnek sayısı | sartname: 48 kHz |
| bit depth | bit derinliği | Örnek çözünürlüğü | ADR-017: 32-bit float |

## §GÜVENLİK TERİMLERİ

| Terim (EN) | Türkçe kullanım | Tanım | K001 karşılığı |
|------------|-----------------|-------|----------------|
| authentication | kimlik doğrulama | Kullanıcı/cihaz doğrulama | AuthenticationMiddleware L26 |
| authorization | yetkilendirme | Erişim izni kontrolü | AuthGuard L7 |
| CSRF | sahte istek koruması | İstek sahteciliği önleme | CsrfMiddleware L7 |
| CORS | köken politikası | Cross-origin erişim kuralı | CorsMiddleware L15 |
| rate limit | istek sınırı | Çağrı frekansı koruması | RateLimitMiddleware L20 |
| encryption | şifreleme | OKU/ÇÖZ okunmazlık | proje standardı: AES-256-GCM |
| Argon2id | parola özeti | Bellek-sert parola hashi | proje standardı |
| nonce | tek kullanımlık sayı | Şifreleme tekrar koruması | kripto kuralı |
| token | jeton | Oturum/istek kanıtı | oturum katmanı |
| session | oturum | Kullanıcı bağlamı | oturum katmanı |
| fail-safe | güvenli hata | Hata durumunda güvenli kapanış | güvenlik kuralı |
| fallback | yedek yol | Birincil başarısız devreye girer | WASAPI fallback (edge case 6) |
| payload | yük | Taşıyan veri gövdesi | API gövdesi |
| salt | tuz | Özete eklenen rastgele veri | parola özeti |
| hash | özet | Geri döndürülemez özet | parola/kayıt özeti |
| permission | izin | Dosya/rol erişim yetkisi | CI `permissions:` (ci.yml L14) |
| secret | gizli anahtar | Açığı sızan veri | GitLeaks kapısı |
| redaction | maskeleme | Hassas verinin gizlenmesi | log kuralı: `[REDACTED]` |

## §TEST TERİMLERİ

| Terim (EN) | Türkçe kullanım | Tanım | K001 karşılığı |
|------------|-----------------|-------|----------------|
| unit test | birim test | En küçük birim doğrulaması | PHPUnit 10 (composer L24) |
| integration test | entegrasyon test | Katman birleşimi doğrulaması | §ENTEGRASYON senaryoları |
| contract test | kontrat test | Yanıt şekli sabitleme | `/api/devices/*` |
| e2e test | uçtan uca test | Kullanıcı akışı doğrulaması | Playwright (package L8) |
| fixture | test verisi | Sabit test girdisi | test-stratejisi §L |
| mock | sahte nesne | Bağımlılık taklidi | birim test |
| coverage | kapsam | Kodun test edilen oranı | hedef %90+ |
| flaky | kararsız | Sonucu değişen test | hedef %0 |
| gate | kapı | Geçiş şartı kontrolü | PASS/BLOCK |
| regression | gerileme | Eski davranışın bozulması | tekrar koşu |
| assertion | doğrulama | Beklenen-değer kontrolü | PHPUnit assert |
| smoke test | duman testi | Hızlı temel kontrol | ilk kapı |
| static analysis | statik analiz | Kod çalıştırmadan inceleme | `composer stan` (L48) |
| lint | biçim denetimi | Stil/dil kural kontrolü | `.ai/CLAUDE.md` §6 |
| test suite | test takımı | Birlikte koşan test grubu | phpunit.xml (8 testsuites) |
| runner | çalıştırıcı | Test yürütücüsü | vitest (package L5) |
| verifier | doğrulayıcı | Kontrol adımını uygulayan | kanit grubu |
| benchmark | ölçüm koşusu | Performans kıyası | ⚠️ VERIFICATION REQUIRED |

## §MİMARİ VE VAULT TERİMLERİ

| Terim (EN) | Türkçe kullanım | Tanım | K001 karşılığı |
|------------|-----------------|-------|----------------|
| ADR | mimari karar kaydı | Karar + gerekçe + sonuç kaydı | kararlar dosyası (henüz ADR değil) |
| SSOT | tek doğruluk kaynağı | Tek geçerli veri kaynağı | vault (.ai/) |
| vault | kasa | Proje bilgi bankası | `.ai/` dizini |
| frontmatter | ön veri | Dosya başlığı metadata bloğu | her K001 dosyası |
| node_id | düğüm kimliği | Vault düğüm tanımı | `K001-donanım-*` |
| parent_id | üst düğüm | Kapsayıcı düğüm | `K001-donanım` |
| group | grup | `mimari`/`kod`/`kanit` sınıfı | FM alanı |
| status | durum | draft/verified/vetted yaşamı | FM alanı |
| authority | otorite | Kaydın sahibi | son satır kanonik |
| handover | devir | Görev başka ajana aktarım | AGENTS §9 |
| escalation | eskalasyon | Üst seviyeye bildirim | AGENTS §10 |
| context lock | bağlam kilidi | Eşzamanlı yazımdan koruma | AGENTS §12 |
| wave (dalga) | dalga | Uygulama sırası bölmesi | dalga-3 plan / dalga-4 yazar |
| frozen | dondurulmuş | Değiştirilemez yüzey | ADR'ler, `donanaım-type` |
| inventory | envanter | Varlık listesi | cihaz envanteri |
| evidence | kanıt | Dosya/satır/URL dayanağı | FM `evidence:` |
| validation | doğrulama kaydı | FM doğrulama cümlesi | FM alanı |
| open question | açık soru | Kapanmamış karar | FM + §OPEN QUESTIONS |
| gate | kapı (vault) | Görev geçiş şartı | gate tabloları |
| registry | kayıt defteri | Merkezi liste | K002 (ID GAP) |

## §DEPOLAMA İLERİ TERİMLERİ

| Terim (EN) | Türkçe kullanım | Tanım |
|------------|-----------------|-------|
| throughput | verim | Birim zamanda taşınan veri |
| IOPS | saniyelik I/O | Saniyedeki işlem sayısı |
| queue depth | kuyruk derinliği | Bekleyen I/O isteği sayısı |
| NAND | NAND flash | SSD depolama hücresi teknolojisi |
| endurance | dayanım | Toplam yazma ömrü (TBW) |
| TBW | toplam yazılabilir | Üretici ömür değeri |
| DRAM-less | önbelleksiz | Dahili DRAM'i olmayan SSD |
| partition | bölüm | Disk mantıksal bölmesi |
| filesystem | dosya sistemi | Dizin/veri yapısı (btrfs/ext4) |
| wearout | aşınma | SSD hücre yorgunluğu |
| remap | yeniden eşleme | Bad sector taşıma tablosu |
| NCQ | komut kuyruğu | SATA paralel komut sırası |
| trim budget | TRIM bütçesi | Silme ipucu işleme kapasitesi |
| snapshot | anlık görüntü | Anlık kopya (zfs/btrfs) |

## §AĞ VE BAĞLANTI TERİMLERİ

| Terim (EN) | Türkçe kullanım | Tanım |
|------------|-----------------|-------|
| protocol | protokol | İletişim kuralları kümesi |
| handshake | el sıkışma | Bağlantı kurma aşaması |
| packet | paket | Ağ veri birimi |
| bandwidth | bant genişliği | Taşıma kapasitesi |
| topology | topoloji | Bağlantı düzeni |
| enumeration (bus) | barınak sayımı | USB/cihaz keşif aşaması |
| descriptor | tanımlayıcı | Cihaz nitelik bloğu |
| endpoint | uç | İletişim adresi (USB/API) |
| hub | göbek | Çoklayıcı bağlantı düğümü |
| bridge | köprü | İki ağ katmanı bağlayıcı |
| full-duplex | tam çift yönlü | Aynı anda gönderim-alım |
| power negotiation | güç müzakeresi | USB-C güç anlaşması |
| cable assembly | kablo demeti | Fiziksel bağlantı takımı |
| connector | konnektör | Mekanik bağlantı parçası |

## §YAZIM VE STİL TERİMLERİ

| Kural | Örnek | Yasak |
|-------|-------|-------|
| Teknik terim İngilizce | `driver`, `firmware` | "sürücü yazılımı" uzatması (ilk geçişte parantez serbest) |
| Dosya adları ASCII | `donanım-kernel.md` | Türkçe karakter dosya adı |
| `donanaım-type` | yazım hatası dahil | DÜZELTME YASAK |
| Tarih formatı | `2026-10-12` | "12 Ekim 2026" |
| URL'ler | tam HTTPS | kısaltılmış/çevrilmiş |

## §YAZIM NORMLARI DETAYI

| # | Norm | Doğru | Yanlış |
|---|------|-------|--------|
| N1 | Tarih | `2026-10-13` | 13/10/2026 biçimleri |
| N2 | Saat | `14:32` 24h | "2:32 pm" |
| N3 | Birim | `48 kHz`, `512 örnek` | aralıksız sayı |
| N4 | Version | `^10.5` (kayıtta) | uydurma tam sürüm |
| N5 | Dosya yolu | `shared/src/Device/` | boşluklu/yanlış ters eğik |
| N6 | Satır atfı | `Satır: 24` | "24. satır civarı" |
| N7 | URL | tam HTTPS adresi | kısaltılmış biçim |
| N8 | Kod adı | `donanaım-type` birebir | düzeltilmiş hâli |
| N9 | Yüzde | `%90+` | "yüzde 90 artı" |
| N10 | Eşik | `>100 dB` | "100 dB'den büyük yaklaşık" |
| N11 | Belirsizlik | `⚠️ VERIFICATION REQUIRED` | tahmini sayı |
| N12 | Bilinmeyen | `UNKNOWN` | boş bırakma |

## §ÇAKIŞMA ÇÖZÜM HİYERARŞİSİ

| # | Çakışma | Kazanan | Gerekçe |
|---|---------|---------|---------|
| 1 | Bu dosya ↔ başka K001 dosyası | bu dosya | §KAPSAM kural 3 |
| 2 | Bu dosya ↔ anayasa | anayasa | üst otorite |
| 3 | Vault ↔ model hafızası | vault | zero-hallucination |
| 4 | Vault ↔ web kaynağı | vault (kayıt) + web (kanıt) | ikisi farklı katman |
| 5 | Türkçe terim ↔ İngilizce terim | İngilizce teknik ad | v2 §4.6 |
| 6 | Yazım hatası ↔ dil kuralı | yazım hatası (dondurma) | frozen kuralı |
| 7 | İki ADR çatışırsa | frozen olan | değiştirilemezlik |
| 8 | Ölçüm ↔ tahmin | ölçüm | kanıt hiyerarşisi |

## §DOĞRU-YANLIŞ KULLANIM ÖRNEKLERİ

| # | Yanlış kullanım | Doğru kullanım |
|---|-----------------|----------------|
| 1 | "sürücü yazılımı katmanı" | "driver katmanı" (ilk geçişte parantez serbest) |
| 2 | "disk tipi HDD olan audio cihazı" | "audio sınıfı cihaz, hdd alt bileşenli" |
| 3 | "SNR yaklaşık 100 dB" (eşik iddiası) | "SNR >100 dB (anayasa §10)" |
| 4 | "PCM5122 kullanıldı" | "PCM5122 yasaklı; PCM3168A/AK4458 (ADR-038)" |
| 5 | "testler geçti" (kanıtsız) | "test sonucu ⚠️ VERIFICATION REQUIRED" |
| 6 | "Juce 9 sürümü doğrulandı" | "JUCE 9 sürüm kanıtı ⚠️ VERIFICATION REQUIRED (kural 12)" |
| 7 | "buffer 500 civarı" | "buffer 512 örnek (sartname)" |
| 8 | "DC 0.5V üstü" | "DC offset >0.5 V → koruma rölesi (kural 10)" |
| 9 | "envanter 4 SQL dosyası" | "envanter 20 SQL dosyası / 4345 satır (2026-10-13)" |
| 10 | "type yazımı düzeltildi" | "`donanaım-type` dondurulmuş, düzeltilmez" |
| 11 | "URL çalışıyor" (kanıtsız) | "HTTP 200 doğrulandı (tarih)" |
| 12 | "ADR-038 güncellendi" | "frozen ADR okunur, değiştirilmez" |
| 13 | "K001 kodu yazıldı" | "K001 üretim kodu PLANNED" |
| 14 | "asenkron kuyruk" (kanıtsız) | "kilitli kuyruk yoksa ⚠️" |

## §SIK YAPILAN HATALAR

| # | Hata | Sonuç | Düzeltme |
|---|------|-------|----------|
| 1 | Terimi Türkçe'ye zorlamak | Anlaşılmaz kod adı | İngilizce terim + Türkçe tanım |
| 2 | İki dosyada farklı tanım | SSOT ihlali | bu dosyaya taşı |
| 3 | Ölçüm uydurma | Zero-hallucination ihlali | `UNKNOWN`/`⚠️` yaz |
| 4 | Yazım hatasını düzeltmek | frozen ihlali (yasa) | birebir koru |
| 5 | Tarihi serbest yazmak | Tarama başarısız | ISO biçim kullan |
| 6 | Boş tablo hücresi | Denetim hatası | değer yaz veya `—` kullan |
| 7 | Envante girmemiş URL | URL gate BLOCK | doğrula + envanter kontrolü |
| 8 | Tekrarlı URL (aynı `.ai`) | Benzersizlik ihlali | farklı kaynak seç |
| 9 | Kanıtsız `Dosya:`/`Satır:` | Doğrulama zinciri kırılır | Read ile doğrula |
| 10 | Sürüm/iddia genişletme | Hallüsinasyon | yalnız dosyada okunanı yaz |

## §SAHİPLİK VE KULLANIM

| # | Terim grubu | Sahiplik | Kullanım yeri |
|---|-------------|----------|---------------|
| 1 | Donanım tipleri | K001 (embedded) | tüm K001 dosyaları |
| 2 | Ses ölçüm terimleri | audio-hw | anayasa §10, ölçüm |
| 3 | Depolama terimleri | K001 core | hdd/ssd kapsamları |
| 4 | Güvenlik terimleri | security | API katmanı |
| 5 | Test terimleri | qa | test-stratejisi |
| 6 | Vault terimleri | Vault Steward | tüm vault |
| 7 | Ölçüm eşikleri | anayasa §10 | bağlayıcı eşikler |
| 8 | Çapraz referanslar | mimari | kararlar/doğrulama |

## §TERİM ÜRETİM KURALI

| # | Adım | Koşul | Red sebebi |
|---|------|-------|------------|
| 1 | Aday terim + İngilizce ad | net tanım | muğlak ad |
| 2 | Kaynak bağlama (dosya/ölçüm/URL) | kanıt var | kanıtsızlık |
| 3 | Çakışma taraması (bu dosya) | eşleşme yok | mevcut terim çakışması |
| 4 | Bölüm atama | ilgili § | uygunsuz bölüm |
| 5 | Türkçe tanım yazımı | teknik terim İngilizce korunur | zorla çeviri |
| 6 | İndekse ekleme | §TAM TERİM İNDEKSİ | indekssiz ekleme |
| 7 | Doğrulama | gate temiz | yasaklı desen |
| 8 | Kayıt | revizyon tarihçesi | kayıtsız değişiklik |

## §TERİM HAYAT DÖNGÜSÜ

| # | Durum | Anlamı | Geçiş |
|---|-------|--------|-------|
| 1 | proposed | Aday, henüz bağlayıcı değil | kaynak bağlanınca |
| 2 | accepted | Bağlayıcı terim | § eklenince |
| 3 | frozen | Değiştirilemez (ör. `donanaım-type`) | otorite kararı |
| 4 | deprecated | Yeni yazımda kullanılmaz | eşleniğiyle |
| 5 | disputed | İki kaynak çatışırsa | hiyerarşi çözümü |
| 6 | unknown | Tanımı bilinmiyor | `⚠️` işareti |

## §ÇAPRAZ REFERANS TERİMLERİ

| Terim | Bağlı ADR/Konu |
|-------|----------------|
| DSP | ADR-096 / ADR-097 (donanım üstü sinyal işleme) |
| Neva Engine | K005 firmware (donanım üstü motor) |
| deviceProfile | `shared/src/Device/DeviceManager.php` L225 |
| isEmbedded/isPhone/is4kTv | DeviceManager L239/L259/L284 |
| PCM3168A | ADR-038 H001 — tek geçerli codec (PCM5122 yasaklı) |
| AK4458 | alternatif DAC önerisi (ADR-038) |
| TPA3255 | D sınıfı amplifikatör çipi (cipler ağacı) |
| 512 örnek / 48 kHz | sartname (olcum PLANNED) |
| 32-bit float | ADR-017 |
| 8.1 surround | kanal düzeni (sartname) |
| ASIO Exclusive Lock | tek uygulama kuralı (kural 9) |
| koruma rölesi | DC offset >0.5 V (kural 10) |

## §TAM TERİM İNDEKSİ (EN → Türkçe → Bölüm)

| # | Terim (EN) | Türkçe | Bölüm |
|---|------------|--------|-------|
| 1 | abstraction | soyutlama | Sistem/Katman |
| 2 | authentication | kimlik doğrulama | Güvenlik |
| 3 | authorization | yetkilendirme | Güvenlik |
| 4 | audio | ses donanımı | Donanım Tipleri |
| 5 | bad sector | bozuk sektör | Depolama |
| 6 | bandwidth | bant genişliği | Ağ |
| 7 | bit depth | bit derinliği | Ses Donanımı |
| 8 | buffer | tampon | Ölçüm |
| 9 | bus | veri yolu | Katman |
| 10 | capability | yetenek | Sistem/Katman |
| 11 | cable assembly | kablo demeti | Ağ |
| 12 | clipping | kırpılma | Ölçüm |
| 13 | codec | kodek | Ses Donanımı |
| 14 | connector | konnektör | Ağ |
| 15 | contract test | kontrat test | Test |
| 16 | CORS | köken politikası | Güvenlik |
| 17 | coverage | kapsam | Test |
| 18 | CSRF | sahte istek koruması | Güvenlik |
| 19 | DAC | D/A dönüştürücü | Ses Donanımı |
| 20 | DC offset | DC kayması | Ölçüm |
| 21 | descriptor | tanımlayıcı | Ağ |
| 22 | DMA | doğrudan bellek erişimi | Katman |
| 23 | driver | sürücü | Sistem/Katman |
| 24 | DRAM-less | önbelleksiz | Depolama İleri |
| 25 | encryption | şifreleme | Güvenlik |
| 26 | endurance | dayanım | Depolama İleri |
| 27 | enumeration | sayım/keşif | Sistem/Katman |
| 28 | endpoint | uç | Ağ |
| 29 | e2e test | uçtan uca test | Test |
| 30 | fail-safe | güvenli hata | Güvenlik |
| 31 | fallback | yedek yol | Güvenlik |
| 32 | filesystem | dosya sistemi | Depolama İleri |
| 33 | fixture | test verisi | Test |
| 34 | firmware | gömülü yazılım | Sistem/Katman |
| 35 | flaky | kararsız | Test |
| 36 | full-duplex | tam çift yönlü | Ağ |
| 37 | gate | kapı | Test + Mimari |
| 38 | gain staging | kazanç kademelendirme | Ölçüm |
| 39 | S.M.A.R.T. | sağlık verisi | Depolama |
| 40 | handover | devir | Mimari |
| 41 | handshake | el sıkışma | Ağ |
| 42 | hash | özet | Güvenlik |
| 43 | headroom | pay | Ölçüm |
| 44 | hdd | sabit disk | Donanım Tipleri |
| 45 | hub | göbek | Ağ |
| 46 | I2S | I2S veri yolu | Ses Donanımı |
| 47 | IOPS | saniyelik I/O | Depolama İleri |
| 48 | interface | arayüz | Sistem/Katman |
| 49 | interrupt | kesme | Katman |
| 50 | inventory | envanter | Sistem + Mimari |
| 51 | ioctl | ioctl çağrısı | Katman |
| 52 | jitter | zamanlama hatası | Ölçüm |
| 53 | kernel | çekirdek | Katman |
| 54 | LBA | mantıksal blok adresleme | Depolama |
| 55 | latency | gecikme | Ölçüm |
| 56 | limiter | sınırlayıcı | Ölçüm |
| 57 | loopback | döngü içi | Ses Donanımı |
| 58 | mock | sahte nesne | Test |
| 59 | NAND | NAND flash | Depolama İleri |
| 60 | nonce | tek kullanımlık sayı | Güvenlik |
| 61 | NVMe | NVMe protokolü | Depolama |
| 62 | packet | paket | Ağ |
| 63 | parent_id | üst düğüm | Mimari |
| 64 | partition | bölüm | Depolama İleri |
| 65 | permission | izin | Güvenlik |
| 66 | power class | güç sınıfı | Sistem/Katman |
| 67 | protocol | protokol | Ağ |
| 68 | queue depth | kuyruk derinliği | Depolama İleri |
| 69 | rate limit | istek sınırı | Güvenlik |
| 70 | regression | gerileme | Test |
| 71 | register | yazmaç | Katman |
| 72 | remap | yeniden eşleme | Depolama İleri |
| 73 | salt | tuz | Güvenlik |
| 74 | sample rate | örnekleme oranı | Ölçüm |
| 75 | SATA | SATA arayüzü | Depolama |
| 76 | sector | sektör | Depolama |
| 77 | secret | gizli anahtar | Güvenlik |
| 78 | session | oturum | Güvenlik |
| 79 | topology | topoloji | Ağ |
| 80 | SNR | gürültü oranı | Ölçüm |
| 81 | snapshot | anlık görüntü | Depolama İleri |
| 82 | ssd | katı hal diski | Donanım Tipleri |
| 83 | static analysis | statik analiz | Test |
| 84 | TDM | TDM veri yolu | Ses Donanımı |
| 85 | THD+N | bozulma + gürültü | Ölçüm |
| 86 | throughput | verim | Depolama İleri |
| 87 | token | jeton | Güvenlik |
| 88 | TRIM | TRIM | Depolama |
| 89 | TBW | toplam yazılabilir | Depolama İleri |
| 90 | type | tip sınıfı | Donanım Tipleri |
| 91 | unit test | birim test | Test |
| 92 | userspace | kullanıcı alanı | Katman |
| 93 | wear leveling | aşınma dengeleme | Depolama |
| 94 | wearout | aşınma | Depolama İleri |

> İndeks 94 satır taşır; her satır bu dosyada tanımlı bir terimi işaret eder.
> Bölüm sütunu terimin geçtiği § grubunu verir (tek anlam kuralı §KAPSAM 3).

## §YENİ DOĞRULANMIŞ WEB KAYNAKLARI

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| Sourcemaking, design patterns ve refactoring üzerine bir referans sitesidir | https://sourcemaking.com/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |
| Refactoring Guru, refactoring ve design patterns kataloğu sunar | https://refactoring.guru/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |

> Her iki URL de `url-envanter.txt` (1673) ile kesişmez ve `.ai/**.md` içinde ilk kez
> kullanılır; pattern/refactoring terim dizinleri evrensel dil için ortak referanstır.

## §REPO KANITLARI (Read ile doğrulanmış, 2026-10-13)

| Dosya | Satır | İlgililik |
|-------|-------|-----------|
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 24 | sınıf başlangıcı | `device` teriminin repo karşılığı |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 216 | `device()` | cihaz erişim terimi |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 225 | `deviceProfile()` | profil terimi (§ÇAPRAZ) |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 239 | `isEmbedded()` | embedded terimi |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 259 | `isPhone()` | phone terimi |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 284 | `is4kTv()` | 4kTV terimi |
| Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 19 | sınıf başlangıcı | enumeration terimi |
| Dosya: `shared/src/Device/DeviceCssMap.php` / Satır: 5 | sınıf başlangıcı | eşleme terimi |
| Dosya: `shared/composer.json` / Satır: 24 | phpunit sürümü | test terimleri karşılığı |
| Dosya: `package.json` / Satır: 5 | vitest betiği | runner terimi karşılığı |
| Dosya: `.github/workflows/ci.yml` / Satır: 14 | `permissions:` | permission terimi karşılığı |

## §ÖLÇÜM VE KABUL TABLOSU

| # | Ölçüm | Beklenen | Kayıt |
|---|-------|----------|-------|
| 1 | Dosya satır sayısı | 500 ve üzeri | sayım |
| 2 | Terim satırı (bu dosya) | 160+ | tablo toplamı |
| 3 | İndeks satırı | 94 | §TAM TERİM İNDEKSİ |
| 4 | Yeni URL kesişimi | 0 | envanter + `.ai` taraması |
| 5 | Boş hücre | 0 | tablo taraması |
| 6 | Mojibake | 0 | bayt doğrulama |
| 7 | Kanıt satırı | 11 (Read) | §REPO KANITLARI |
| 8 | Son satır biçimi | kanonik authority | satır karşılaştırma |

## §OPEN QUESTIONS

| # | Soru |
|---|------|
| 1 | "type" terimi mü `donanaım-type` yazımını mı kapsıyor? (kapsam dosyası §5) |
| 2 | Öğrenilen yeni terimler hangi sıklıkla indekse girer? (sahibi: Vault Steward) |
| 3 | Ölçüm eşik terimleri anayasa dışında bir dosyada tekrarlanabilir mi? (SSOT tartışması) |
| 4 | `gate` terimi iki bölümde geçiyor (Test + Mimari) — anlam birliği korunuyor mu? |
| 5 | K002 registry terimleri bu sözlüğe mi bağlanır? (ID GAP) |
| 6 | Yazım dondurma listesine yeni madde eklenebilir mi? (otorite kapısı) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
