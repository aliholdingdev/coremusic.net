---
node_id: K006.7-local-inference-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/src/** grep ollama|llama|gguf|onnx|whisper → 0 eşleşme (2026-10-12)"
  - "shared/src/AI/AIEngine.php L34 · L41 · L288–313 (2026-10-12 tam okuma)"
validation: "2026-10-12 · yerel çıkarım bileşeni = 0 satır; yalnız 2 hazır seam kaydedildi · status=verified"
open_questions:
  - "Yerel çıkarım binary'si sunucuya nasıl kurulacak (paket/versiyon) → UNKNOWN"
  - "shell_exec deseni süreç yönetimi (timeout/kill) olmadan büyütülürse risk → tasarımda ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K006.7-local-inference — Bileşenler

> K006.7'in **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. **Yerel çıkarım yapan tek bir bileşen yoktur**; yalnızca entegrasyonun
> oturabileceği iki mevcut desen (seam) kaydedilmiştir.

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| K006.7'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — 7 PHP, "local-inference" adında dosya yok |
| Yerel çıkarım kodu | **0 satır** | grep `ollama|llama|gguf|onnx|torch|whisper` → 0 (2026-10-12) |
| Model dosyası indirme/versiyonlama | **0 satır** | grep `download|checksum|model_version` → 0 |
| Hazır seam · dış binary çağırma | 1 (`extractMetadata` L288–313 → `shell_exec('ffprobe …')`) | §2.1 |
| Hazır seam · yapılandırma | 1 (`AIEngine::__construct(ConfigManager)` L34 · `getConfig` L41) | §2.2 |
| Yerel çıkarım sözleşmesi | **YOK** | `shared/src/Contracts/AI/` — 6 interface, hiçbiri runtime çağırmaz |

---

## §2 Gerçek Kod Bileşenleri (seam'ler)

### §2.1 `AIEngine::extractMetadata(string $filePath): array` — L288–313 (private)

| Satır | Davranış | K006.7 açısından |
|---|---|---|
| 290–293 | `ffprobe -v quiet -print_format json -show_format -show_streams "%s"` | **dış binary çağırma deseni** — yerel çıkarım binary'si (llama/whisper/Ollama CLI) için birebir aynı kalıp |
| 292 | `escapeshellarg($filePath)` | kaçış doğru — korunması gereken standart |
| 295 | `shell_exec($cmd)` | senkron, timeout **yok** (⚠️ mühendislik boşluğu, C5) |
| 296–298 | `$output === null` → varsayılanlar | başarısızlıkta sessiz düşme |

**Değer:** K006.7 **sıfırdan süreç çağırma yazmak zorunda değildir** — mevcut desen
genişletilebilir. Ama `shell_exec` tek-shot'tır; uzun süren model çıkarımı için süreç
yönetimi gerekir (⚠️).

### §2.2 `AIEngine::__construct(ConfigManager $config)` — L34 · `getConfig` — L41

| Satır | Davranış | K006.7 açısından |
|---|---|---|
| 34 | bağımlılık `ConfigManager` | yerel runtime ayarları (model yolu, binary yolu, timeout) buraya binebilir |
| 41 | `getConfig()` yapılandırmayı döner | tek yapılandırma kapısı — paralel config okuma yok |

---

## §3 Sözleşme Yüzeyi (runtime bağlanacak yer)

| Sözleşme | Durum | Kayıt |
|---|---|---|
| `AIEngineInterface` (5 imza) | mevcut — motoru çağırır, **runtime çağırmaz** | Contracts L28–60 |
| `AIOrchestratorInterface` (6 imza) | mevcut — görev dağıtımı | Contracts L13 |
| `ToolCallingInterface` (5 imza) | mevcut — araç çağırma (K016) | Contracts L13 |
| (yerel çıkarım runtime'ı interface'i) | **YOK** | `shared/src/Contracts/AI/` glob — 6 dosya, hiçbiri |

**Tasarım notu (iddia değil):** `ToolCallingInterface::callTool` (K016, L73) kavramsal olarak
dış araç çağırır; ancak K006.7'nin runtime'ı bu arayüzün **hangi implementasyonu** olacağı
**UNKNOWN** — bugünden bağlanmamıştır.

---

## §4 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.7 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| Yerel çıkarım motoru (llama.cpp/whisper.cpp/Ollama) | **0 satır** | grep → 0 (2026-10-12) |
| Model dosyası indirme + checksum | **0 satır** | grep → 0 |
| Model dizini/ölçü yönetimi | **0 satır** | grep `model_dir|disk_space` → 0 |
| Donanım keşfi (GPU/CPU/RAM) | **0 satır** | `analyzeHardware` L138 donanım *puanlamasıdır*, çıkarım donanımı keşfi değil |
| Uzun süreç yönetimi (timeout/kill/log) | **0 satır** | L295 tek-shot `shell_exec` |
| Yerel çıkarım önbelleği (sonuç caching) | **0 satır** | composer.json'da cache paketi yok (grep → 0) |
| Dış binary çağırma deseni | ✅ **var** | L290–295 |
| Yapılandırma seam'i | ✅ **var** | L34 · L41 |

---

## §5 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.2-inference | genel "çıkarım" sorusu; K006.7 onun **yerel** alt seçeneği — runtime kararı verilmeden ikisi de boş |
| K006.6-audio-intelligence | whisper.cpp adayı transkripsiyonu K006.6 kapsamına girer; entegrasyon sırası belirsiz |
| K006.1-model-management | yerel model dosyası inince sürüm/kayıt katmanı gerekir — bugün 0 satır |
| K009/K016 (dış) | `shell_exec` güvenlik denetimi security katmanının konusudur (escapeshellarg zaten var, L292) |

---

## §6 Kaynaklar

1. `shared/src/AI/AIEngine.php` — L34 · L41 · L288–313 tam okuma, 2026-10-12.
2. `shared/src/**` grep (`ollama|llama|gguf|onnx|torch|whisper`) → 0 eşleşme, 2026-10-12.
3. `shared/src/Contracts/AI/` glob → 6 interface, runtime çağıran yok, 2026-10-12.
4. `shared/composer.json` — cache/ML/runtime paketi yok, 2026-10-12.
5. `local-inference-web-arastirma.md` §5 — çapraz bağ.

---

`status: verified` · `validation: 2026-10-12 · 0 yerel çıkarım bileşeni, 2 hazır seam (L288–313 · L34/L41), 6 beklenen bileşenden 6'sı 0 satır`
