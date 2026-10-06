---
title: "Bağlam Penceresi (Context Window)"
type: kavram
created: 2026-10-06
updated: 2026-10-06
sources: [test-kaynak]
tags: [llm, bağlam, token]
---

# Bağlam Penceresi

Modelin aynı anda "görebildiği" token sınırı. Bir konuşma içinde yalnızca son birkaç bin token görünürdür; pencere dışına çıkan mesajlar unutulur ve ajan tutarsız cevap verir.

## Sonuçları

- Sohbet geçmişi tek başına yeterli değildir → [[ai-agent-memory]] gerekir.
- Eski talimatlar pencere dışına çıkınca kullanıcı tercihleri kaybolur; kalıcı katmanda tutulur.

Kaynak: `.ai/raw/test-kaynak.md`

## İlgili Sayfalar
- [[ai-agent-memory]] — bu sınırın çözümü
- [[context-window]] ile ilişkili depolama: [[vector-database]] · [[file-based-memory]]
- `.ai/raw/test-kaynak.md` — birincil kaynak
