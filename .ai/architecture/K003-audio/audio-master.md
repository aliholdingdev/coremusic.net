---
title: "K003-audio — Master"
type: architecture-master
node_id: K003-audio
status: draft
date: 2026-10-12
updated: 2026-10-12
source: "v2 §2.3 (coremusic-katmanli-mimari-agaci-v2.md)"
---

# audio-master — K003 Ses Motoru Anayasası

## §1 Rol (ELI15)

**Sesin üretilip hoparlöre/kulaklığı gittiği motor.**

K003, ses verisinin kaynaktan çıkış noktasına kadarki **aktarma ve zamanlama**
sorumluluğunu taşır. Bir medya oynatıcı düşünelim: dosya okunur, belleğe
alınır, kanallara ayrılır, doğru cihaza gönderilir ve tüm bunlar zamanında
yapılır. Bu adımların hangisinin nerede durduğu bu alanın 12 çocuğunda tanımlıdır.

Basit benzetme: K003 bir **su boru hattı**dır. Suyu üreten K001 (kaynak/fiziksel
parça), suyun tadını ayarlayan K004 (DSP), hattın döşenmesine izin veren zemin
K000 (OS), çeşmenin içindeki küçük mekanizma K005'tir (firmware). Boru hattı
kendi başına iyi çalışmalı: sızdırmamalı (veri kaybı), basınç dalgalanmamalı
(zamanlama jitter'ı), yanlış çeşmeye çıkmamalı (yanlış routing).

## §2 Kapsam / Sınır

### §2.1 Ne K003'ün işidir

| Alan | Kapsam içi |
|---|---|
| akış | Ses verisinin sırayla işlenmesi ve taşınması |
| cihaz | Hangi çıkış/giriş cihazının seçileceği ve değiştirilebileceği |
| sürücü | Sürücü katmanıyla tek noktadan konuşma |
| backend | Buffer, kuyruk ve çalışma zamanı altyapısı |
| yön | Kanal yönlendirme (routing) ve karıştırma (mixing) |
| zaman | Realtime çalışma kuralları ve clock senkronizasyonu |
| sağlık | diagnostics + testing ile ölçüm ve doğrulama |

### §2.2 Ne DEĞİL (sınır)

| Konu | Sahibi | Gerekçe |
|---|---|---|
| EQ / bass / tiz / reverb / crossover DSP zinciri | K004-dsp | Sinyal şekillendirme ayrı alan |
| DAC/ADC, amplifikatör, PCB tasarımı | K001-donanım | Fiziksel tasarım ayrı alan |
| Boot, register yazımı, cihaz içi güncelleme | K005-firmware | Donanım içi yazılım ayrı alan |
| Ses API'si, sürücü yükleme, OS kaynak yönetimi | K000-isletim-sistemi | Zemin katmanı |
| Uygulama arayüzü (oynat/duraklat butonu) | uygulama katmanı | K003 motoru, UI'ı bilmez |

**Sınır kuralı:** K003 içinden K004'e sinyal **gönderilir**, K004'ün içine
**sokulmaz**; DSP parametresi veri olarak taşınır, DSP hesabı K003'ün realtime
döngüsüne **kopyalanmaz** (aksi halde aynı matematik iki yerde yaşar).

## §3 Kademe-2 çocuk tablosu (v2 §2.3 — birebir, 12 çocuk)

| # | Çocuk | Kapsam (1 satır) | Üst düğüm | Durum |
|---|---|---|---|---|
| 1 | engine | Zinciri çalıştıran ana döngü ve yaşam döngüsü | K003-audio | draft |
| 2 | device | Cihaz envanteri, seçimi ve değişimi | K003-audio | draft |
| 3 | driver | Sürücü yüzeyiyle tek noktadan etkileşim | K003-audio | draft |
| 4 | backend | Buffer/kuyruk/çalışma zamanı altyapısı | K003-audio | draft |
| 5 | input | Kaynak girişi (dosya, mikrofon, akış) | K003-audio | draft |
| 6 | output | Çıkış tarafı (hoparlör, kulaklık, akış) | K003-audio | draft |
| 7 | routing | Kanal/akış yönlendirme kuralları | K003-audio | draft |
| 8 | mixing | Çoklu kaynağı tek akışta birleştirme | K003-audio | draft |
| 9 | realtime | Zaman baskısı ve lock-free çalışma kuralları | K003-audio | draft |
| 10 | synchronization | Clock/epoch uyumu, drift telafisi | K003-audio | draft |
| 11 | diagnostics | Log, ölçüm, hata sınıflandırma | K003-audio | draft |
| 12 | testing | Test senaryoları ve kabul kriterleri | K003-audio | draft |

> Çocuk tanımları yalnız addan türetilmiştir (v2 §3 KURAL 3) → her satır
> ⚠️ VERIFICATION REQUIRED taşır; içerik iddiası sayılmaz.

## §4 Teknoloji (yalnızca doğrulanmış web bulguları)

| # | Bulgu | Kanıt (URL) | Erişim | Durum |
|---|---|---|---|---|
| T1 | Steinberg ASIO SDK açık kaynak ilan edilmiş (GPLv3) | https://www.steinberg.net/developers/asiosdk-open | 2026-10-12 | ✅ doğrulandı |
| T2 | JUCE deposu erişilebilir | https://github.com/juce-framework/JUCE | 2026-10-12 | ✅ repo doğrulandı |
| T3 | JUCE 8811★ (görev iddiası) | bağımsız sayım gerekir | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| T4 | NAudio deposu + ASIO tartışma kategorisi mevcut | https://github.com/naudio/NAudio/discussions/categories/general | 2026-10-12 | ✅ kategori doğrulandı |
| T5 | NAudio + ASIO tartışması (2026-09-28) kesin başlığı | aday: discussions/1091 · discussions/1250 · issues/1249 · Docs/AsioPlayback.md | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| T6 | WASAPI ana sayfası (Windows Core Audio) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ doğrulandı |
| T7 | WASAPI exclusive mode streams belgesi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ doğrulandı |
| T8 | ASIO cihaz kaybında WASAPI fallback kuralı | .ai/AGENTS.md §17 #6 (dosya okundu) | 2026-10-12 | ✅ iç kanıt |
| T9 | Realtime: lock-free callback, buffer'lar baştan allocate | ~/.config/opencode/AGENTS.md §5 (dosya okundu) | 2026-10-12 | ✅ iç kanıt |
| T10 | JUCE 9 / ASIO SDK 2.3.4 hedef sürümleri | .ai/AGENTS.md §4 (dosya okundu) | 2026-10-12 | ✅ iç kanıt (hedef — kurulu değil) |
| T11 | ASIO SDK sürüm numarası (2.3.4) bağımsız web teyidi | — | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| T12 | NAudio sürüm/paket bilgisi | — | 2026-10-12 | **UNKNOWN** — kanıt toplanmadı |

**T1 lisans notu (⚠️):** ASIO SDK'nın GPLv3 açık kaynak oluşu, CoreMusic'in
kendi lisans rejimiyle çatışabilir veya uyumlu olabilir; etki (derived work
sayılma / yayım yükümlülüğü) hukuki değerlendirme gerektirir → açık soru Q1.

**Yasak:** T11/T12 gibi doğrulanmamış satırlara sürüm/paket adı **uydurulmaz**;
kanıt gelene kadar UNKNOWN veya ⚠️ kalır.

## §5 Bağımlılık özeti

| Yön | Bağımlılık | Tür | Etiket |
|---|---|---|---|
| K003 → K000-isletim-sistemi | Ses API'si, sürücü yükleme, kaynak yönetimi | çalışma zamanı | [TASARIM] |
| K003 → K001-donanım | DAC/ADC, ses kartı, I2S/TDM hattı | fiziksel | [TASARIM] |
| K003 → K005-firmware | Donanımın hazır hale gelmesi (boot sonrası) | zamanlama | [TASARIM] |
| K004-dsp → K003 | DSP, akışın üstünde çalışır | çalışma zamanı | [TASARIM] |
| K003 → K002 | **ID GAP** — registry'de K002 kimliği belirsiz | — | ⚠️ registry §6 |

Bağımlılık yönü: zemin → donanım → motor → DSP → firmware akışına göre K003
**üçüncü katmandır**. Geriye (K000/K001'e) yalnızca arayüz yüzeyinden bağımlıdır;
üst katmanlar K003'ü çağıran taraftır (used_by). Ayrıntı: audio-bagimliliklar.md.

## §6 Doğrulama özeti

| Kriter | Tanım | Şu an |
|---|---|---|
| D1 | v2 §2.3 mini-ağaç birebirliği (12 çocuk) | ✅ 2026-10-12 |
| D2 | 12 çocuk klasöründe index.md (8 zorunlu alan) | ✅ 2026-10-12 |
| D3 | master + bagimliliklar + dogrulama dosyaları mevcut | ✅ 2026-10-12 |
| D4 | açık ⚠️ / UNKNOWN satırı kalmadı | ❌ 3 açık (T3, T5, T11) + 1 UNKNOWN (T12) |
| D5 | açık sorular kapandı | ❌ 3 açık |
| D6 | en az bir [TASARIM] → [KANIT] geçişi | ❌ 0 |
| D7 | status: verified yazımı | ❌ D1–D7 ✅ olunca |

**Geçiş kuralı:** D1–D7 tamamı ✅ → index.md status: draft → verified.
Kısmi geçiş yoktur; D4'te tek bir açık ⚠️ kalırsa verified yazılmaz.

## §7 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | ASIO SDK GPLv3 lisansı CoreMusic'e nasıl etki eder? | ⚠️ VERIFY REQUIRED | legal-advisor + embedded |
| Q2 | NAudio + ASIO tartışması (2026-09-28) tam hangi thread? | ⚠️ VERIFY REQUIRED | researcher |
| Q3 | JUCE 8811★ sayımı nasıl bağımsız teyit edilir? | ⚠️ VERIFY REQUIRED | researcher |
| Q4 | ASIO SDK 2.3.4 hedefi güncel sürümlerle uyumlu mu? | ⚠️ VERIFY REQUIRED | embedded-engineer |
| Q5 | engine ↔ backend çocuk sınırı nedir? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q6 | input/output ↔ device/driver sorumluluk ayrımı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |

## §8 Kaynaklar

| Kaynak | İlgili bölüm | Bağlantı |
|---|---|---|
| v2 §2.3 — K003-audio/ mini-ağaç | §1, §2, §3 | coremusic-katmanli-mimari-agaci-v2.md (G: Drive · okundu 2026-10-12) |
| v2 §3 — adlandırma kuralları (KURAL 1–3) | §2, §3 | aynı dosya |
| v2 §4.1–4.3 — kayıt şeması | §6 | aynı dosya |
| ADR-097 K2 — multi-MD şeması ve rol adları | §1, §3 | .ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md |
| Steinberg ASIO SDK (açık kaynak) | §4 T1 · §7 Q1 | https://www.steinberg.net/developers/asiosdk-open |
| JUCE deposu | §4 T2 · §7 Q3 | https://github.com/juce-framework/JUCE |
| NAudio discussions (ASIO başlıkları) | §4 T4–T5 · §7 Q2 | https://github.com/naudio/NAudio/discussions/categories/general |
| NAudio ASIO docs (aday kanıt) | §4 T5 | https://github.com/naudio/NAudio/blob/main/Docs/AsioPlayback.md |
| WASAPI (Windows Core Audio) | §4 T6 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi |
| WASAPI exclusive mode | §4 T7 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams |
| Agent registry (JUCE 9, ASIO SDK 2.3.4; ASIO→WASAPI fallback) | §4 T8, T10 | .ai/AGENTS.md §4 · §17 (okundu 2026-10-12) |
| Global mühendislik standartları (lock-free RT, buffer ön-allocate) | §4 T9 | ~/.config/opencode/AGENTS.md §5 (okundu 2026-10-12) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
