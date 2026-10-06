---
title: "AI Ajanlarında Hafıza (Test Kaynağı)"
type: kaynak
raw_path: raw/test-kaynak.md
created: 2026-10-06
updated: 2026-10-06
sources: [test-kaynak]
ingested: 2026-10-06 17:51
tags: [hafıza, ajan, vektör, bağlam]
---

# Test Kaynağı Özeti — AI Ajanlarında Hafıza

## Genel Özet

AI ajanlarında kalıcı hafızanın neden gerekli olduğu, hafızanın üç katmanı (episodik, semantik, prosedürel) ve iki uygulama yaklaşımı (vektör veritabanı, dosya tabanlı) anlatılıyor. Ana tez: sohbet geçmişi tek başına yeterli değil; yazma (ingest) ile okuma (query) ayrımı yapılmazsa sistem çelişkilerle dolar.

## Ana Fikirler

1. Bağlam penceresi sınırlıdır; eski mesajlar düşünce tutarsızlığa yol açar.
2. Üretim ajanları sohbet geçmişine değil kalıcı bir hafıza katmanına ihtiyaç duyar.
3. Hafıza üç katmanı: episodik (ne zaman/ne), semantik (olgu/kavram), prosedürel (nasıl).
4. Vektör tabanlı hafıza ölçeklenir ama belirsizlik yaratır; dosya tabanlı hafıza şeffaftır, küçük-orta ölçekte tercih edilir.
5. Kritik kural: ingest/query ayrımı ve eski kaydın güncellenmesi yoksa wiki çelişkilerle dolar.

## Önemli Alıntılar/Veriler

- "Model, bir konuşma içinde yalnızca son birkaç bin token'ı aynı anda görür."
- "Episodik kayıt eskidiğinde silinebilir, semantik olgu ise kaynağıyla birlikte yıllarca geçerli kalabilir."
- "Yazma (ingest) ile okuma (query) ayrılmadığında wiki hızla çelişkilerle dolar."

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

- [[ai-agent-memory]]
- [[context-window]]
- [[episodic-memory]]
- [[semantic-memory]]
- [[procedural-memory]]
- [[vector-database]]
- [[file-based-memory]]
- [[embedding]]
- [[chunking]]
