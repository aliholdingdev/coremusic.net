---
title: "Local Inference — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/local-inference
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# Local Inference — Kapsam

**ELI15:** Local Inference, yapay zekanın **internet olmadan, kendi bilgisayarında** çalışmasıdır. Bulutta (uzak sunucuda) model çağırmak yerine modeli indirip kendi makinenin işlemcisinde (CPU · GPU · NPU) çalıştırırsın — bu hem gizlilik (veri dışarı çıkmaz) hem gecikme (sunucu yolculuğu yok) hem de maliyet avantajı sağlar. Bu alt alan, hangi modelin yerelde çalıştırılacağını, hangi donanım hızlandırıcısının (execution provider) kullanılacağını ve yerel çalışma modunun sınırlarını (bellek · model boyutu) belirler. K006'nın çocuk tablosunda (ai-master.md §3) 7. sırada tanımlıdır; `local-inference/` klasörünün `index.md` (satır 1–47) kademe-2 kapsam taslağının olmadığı, üst düzey kapsamın v2 §2.4-7'de verildiğini belirtir.

## 1. Kapsam içi (İçinde)

1. **Yerel model çalıştırma modu** — modelin kullanıcı makinesinde çalıştırılması (yerel ↔ bulut ikiliği); mod seçimi ve fallback (yerel yetmezse buluta geçiş).
2. **Execution provider (hızlandırıcı) seçimi** — CPU · GPU (CUDA) · DirectML · NPU gibi donanım hızlandırıcıların seçimi ve sıralaması; makine donanımına göre otomatik tercih.
3. **Yerel çalışma sınırları** — model boyutu ve bellek bütçesi; hangi modellerin yerelde sığdığı, sığmayanların küçültülmesi (quantization) veya buluta devri.
4. **Yerel çıkarım hattı (pipeline)** — örneğin ses analizi için yerel özellik çıkarımı + yerel model adımı; `local-inference/` + `audio-intelligence/` birlikte.
5. **Çevrimdışı davranış** — internet yokken hangi işlevlerin çalıştığı, kuyruğa alınanların ne zaman senkronize edildiği.

## 2. Kapsam dışı (Dışında)

1. **Bulut inference (QPS · stream · pipeline)** — canlı tahmin yürütme `inference/` alt alanındadır (v2 §2.4-4).
2. **Model eğitimi (hype loop)** — eğitim `evaluation/` + `local-inference/` sınırındaki hype döngüsüne aittir; yerelde yalnız **çalıştırma**.
3. **Donanım topolojisi / DSP · ASIO donanımı (K004)** — ses donanımı seçimi K004-KALEM-SES'e aittir; burada yalnız donanımın hızlandırıcı rolü.
4. **Ajan / zeka altyapısı (K016-AI)** — ajan orkestrasyonu K016'ya aittir (K006 sınırı, `v2.md §2.6`).
5. **Depolama · DB (K001-KAYNAK-HAVUZU)** — veri katmanı K001'e aittir (`v2.md §2.4-2`); yerel model dosyalarının saklama yeri ayrı karar.

## 3. Disk kanıtları

| Kanıt | Dosya / konum | Not |
|---|---|---|
| Alt alan envanteri | `.ai/architecture/K006-yapay-zeka/ai-master.md §3` (satır 69) | 7. satır · "Yerel/edge çıkarım · offline AI"; çıktı "Yerel model çalıştırma · offline mod" |
| Kapsam sınırı (v2) | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-7` (satır 241) | "Minimal kapsam: offline mod · yerel model cache · hafif SDK · VSIX" |
| Çocuk index · taslak yok | `.ai/architecture/K006-yapay-zeka/local-inference/index.md` (satır 45) | "Kademe-2 kapsamları ayrı dosyalarda; henüz oluşturulmadı" (disk kanıtı: bu dosya) |
| Fallback sinyali | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-4` (satır 234) | "Cloud → SaaS fallback: QPS · API key · fallback model" — yerel mod ikiliğine komşu |
| Bağımlılık · yerel | `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` (satır 18) | M09 · yerel model — envanter satırı |
| Donanım bilinmiyor | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §7 (satır 116) | "Hangi hardware üzerinde AI çalışacak?" açık sorusu |

## 4. Açık sorular

1. **Donanım kesin değil** — hedef makine (CPU · NVIDIA GPU · DirectML/NPU) belirsiz; execution provider seçimi buna bağlı (ai-master.md §7 satır 116'daki açık soruyla bağlantılı).
2. Yerel ↔ bulut geçiş (fallback) kuralı: hangi durumda yerelden buluta, hangi durumda tersine — netleşmedi.
3. Yerel model bellek/boyut bütçesi (MB/GB) sabitlenmedi; quantization eşiği bilinmiyor.
4. **Bu alt alanın kademe-2 kapsam taslağı v2'de yok** — üstteki tanımlar taslak önerisidir; v2 §2.4-7 ile resmileşmedi (aynı belirsizlik `music-analysis/` kapsamının 4. maddesinde de işaretli).
5. Çevrimdışı senkronizasyon (kuyruk · çakışma) davranışı tasarımsız.

## İlişkili dosyalar

- [[local-inference-dogrulama]]
- [[local-inference-ornekler]]
- [[../index|K006-yapay-zeka index]]
