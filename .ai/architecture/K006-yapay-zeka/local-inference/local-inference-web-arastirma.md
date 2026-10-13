---
node_id: K006.7-local-inference-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/** grep ollama|llama|gguf|onnx → 0 eşleşme (2026-10-12)"
  - "shared/src/AI/AIEngine.php L288–313 (dış araç çağırma deseni: shell_exec ffprobe)"
validation: "2026-10-12 · 9 benzersiz URL havuzdan, iddialar sayfa metniyle · status=verified"
open_questions:
  - "Yerel çıkarım sunucuda mı, kullanıcı cihazında mı çalışacak → UNKNOWN"
  - "Ollama gibi bir sunucu kullanılırsa sürüm/elleme politikası → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K006.7-local-inference — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **yerel çıkarım**
> ayağı. Tüm URL'ler 2026-10-12 doğrulanmıştır; yeni URL üretilmedi — yalnız havuz §2.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Yerel çıkarım motorları hangileri? | K006.7'nin çekirdek sorusu — repo'da **0 satır** | §2.7 (26–28) |
| Model çalışma zamanı ailesi? | sunucu-yerel çalıştırma | §2.1 (1) |
| LLM yerel sunum mümkün mü? | hafif LLM senaryosu | §2.2 (4) |
| Kaynak model/tersine mühendislik? | yerel model tedariki | §2.1 (6) · §2.2 (8) |
| Yerel test stratejisi | K006.9 ile ortak | §2.8 (32) |
| Veri yerellliği/uyumluluk | KVKK/GDPR bağlamı (yerel = veri çıkmaz) | §2.9 (29) |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.7 madde |
|---|---|---|---|
| 1 | https://github.com/ggml-org/llama.cpp | **130.787★**, **MIT** — yerel LLM çıkarım motoru | yerel çıkarım adayı |
| 2 | https://github.com/ggml-org/whisper.cpp | **54.204★**, **MIT** — yerel Whisper çıkarımı | yerel ses modeli adayı |
| 3 | https://ollama.com/ | Ollama — yerel model çalıştırma; sayfa: **9M+/ay kullanıcı**, **1B+ indirme**, **200T+ token** | hazır sunucu/CLI adayı |
| 4 | https://onnxruntime.ai/docs/ | ONNX Runtime — model çalışma zamanı ailesi | taşınabilir yerel runtime |
| 5 | https://vllm.ai/ | vLLM — yüksek verimli LLM tahmin/servis motoru | GPU-yerel sunum adayı |
| 6 | https://github.com/openai/whisper | Whisper — **110.308★**, **MIT** — kaynak model | whisper.cpp'nin kaynağı |
| 7 | https://huggingface.co/docs/hub/ | Hub — model paketleme/dağıtma/sürümleme | yerel model tedariki |
| 8 | https://docs.pytest.org/en/stable/ | pytest dokümanları | aday test altyapısı |
| 9 | https://gdpr.eu/ | GDPR metin/özet portalı | veri yerellliği gerekçesi |

**Toplam K006.7 için: 9 benzersiz URL** (havuz §2.7 · §2.1 · §2.2 · §2.8 · §2.9).

---

## §3 İddia Ayrıntıları (sayfa metni dayanaklı)

| İddia | Dayanak URL | Ne söylüyor | K006.7'e etkisi |
|---|---|---|---|
| Yerel çıkarım MIT lisanslıdır | 1–2 · 6 | üç projenin de lisansı MIT | lisans riski düşük — aday |
| Hazır CLI/sunucu mevcuttur | 3 | Ollama sayfası kullanım/indirme rakamları verir | en düşük entegrasyon maliyeti |
| Yerel runtime taşınabilir olur | 4 | ONNX Runtime aynı modeli farklı cihazlarda çalıştırır | donanım bağımsızlık |
| GPU yerel sunum mümkün | 5 | vLLM yüksek verim vurgusu | donanım gerektirir — ⚠️ |
| Model dosyası yerel diske iner | 7 | Hub paketleme/dağıtma dokümanları | disk/ölçü yönetimi |
| Yerel işleme veri dışarı çıkmaz | 9 | GDPR uyumluluk metni | KVKK/GDPR gerekçesi (§2.9 kök) |

---

## §4 K006.7'ye Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.7 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| llama.cpp | yerel LLM çıkarımı | 1 | **kurulmadı** (grep → 0) |
| whisper.cpp | yerel transkripsiyon | 2 | **kurulmadı** (grep `whisper` → 0) |
| Ollama | yerel model sunucusu/CLI | 3 | **kurulmadı** (grep `ollama` → 0) |
| ONNX Runtime | yerel taşınabilir runtime | 4 | **kurulmadı** (grep → 0) |
| vLLM | GPU yerel sunum | 5 | **kurulmadı** (grep → 0) |
| Hub + pytest | model tedariki + test | 7–8 | **kurulmadı** |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` · `status: draft`).

---

## §5 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| llama.cpp/whisper.cpp/Ollama yerel çalışır | `shared/src/**` grep `ollama|llama|gguf|onnx|whisper` → **0 eşleşme** (2026-10-12) | ⚠️ boşluk |
| Dış araç çağırma deseni | `extractMetadata` L290–295 → `shell_exec('ffprobe …')` + `escapeshellarg` L292 — **VAR** (yerel binary çağırmanın repo'daki tek örneği) | ✅ desen hazır |
| Yapılandırma yüzeyi | `AIEngine::__construct(ConfigManager)` L34 · `getConfig` L41 — yerel runtime ayarları buraya binebilir | ✅ desen hazır |
| Yerel model dosyası yönetimi | **0 satır** (indirme/versiyonlama/checksum yok) | ⚠️ boşluk |
| Donanım tespiti (GPU/CPU) | `analyzeHardware` L138 **kural tabanlı** — çıkarım donanımı tespiti değil | 🔶 kısmi |
| Veri yerelliği | sunucuda yerel çıkarım = veri dışarı çıkmaz — **tasarım avantajı**, kod kanıtı yok | 🔶 tasarım |

**Sonuç:** K006.7'nin repo'da **hiçbir gerçek bileşeni yoktur**; ancak iki **desen** hazır:
ffprobe ile dış binary çağırma (L290–295) ve ConfigManager tabanlı yapılandırma (L34/L41) —
yerel çıkarım entegrasyonunun oturacağı iki mevcut seam'dir. Bu, K006.7'nin ana bulgusudur.

---

## §6 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | Domain "local-inference" der; repo'da tek bir yerel çıkarım satırı **0** | ⚠️ **açık boşluk** (riskler R-02) |
| C2 | "Yerel" kelimesi iki anlamda: sunucu-yerel mi, kullanıcı-cihaz-yerel mi | ⚠️ kapsam belirsizliği → VERIFICATION REQUIRED |
| C3 | vLLM GPU ister — sunucu donanımı bilinmiyor | ⚠️ VERIFICATION REQUIRED |
| C4 | Ollama sayfa rakamları (9M+/ay vb.) sayfa metnidir, bağımsız denetim **değil** | ⚠️ tek-kaynak iddia |
| C5 | ffprobe deseni `shell_exec` — güvenli kaçış var (L292) ama süreç yönetimi/timeout **yok** | ⚠️ mühendislik boşluğu |

---

## §7 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **9** |
| ✅ sayfa metniyle doğrulandı | 9 |
| Repo ile çapraz eşleşme | 2 (dış binary deseni · config seam'i — ikisi de "hazır desen") |
| ⚠️ boşluk/çelişki | 5 (C1–C5) |
| UNKNOWN | 3 (yerellik tanımı · donanım · sunucu politikası) |
| Kurulu aday | 0 |

---

## §8 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.1 · §2.2 · §2.7 · §2.8 · §2.9 — 2026-10-12.
2. `shared/src/**` grep (`ollama|llama|gguf|onnx|whisper`) → 0 eşleşme, 2026-10-12.
3. `shared/src/AI/AIEngine.php` L34 · L41 · L288–313 — 2026-10-12 disk okuma.
4. `shared/composer.json` — runtime/ML paketi yok, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 9/9 URL havuzdan doğrulandı, 2 hazır-desen kaydı, 5 işaretli boşluk, 0 kurulu aday`
