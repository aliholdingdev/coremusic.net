---
title: "Chunking (Parçalama)"
type: kavram
created: 2026-10-06
updated: 2026-10-06
sources: [test-kaynak]
tags: [chunk, parçalama, pipeline]
---

# Chunking

Kaynağı anlamlı metin parçacıklarına ayırma adımı; [[embedding]] öncesi zorunludur.

## Dikkat Edilecekler

- Parça çok büyükse [[vector-database]] sorgusu gürültülü döner; çok küçükse anlam kopar.
- Parça başına kaynak künyesi tutulmazsa hangi bilginin nereden geldiği kaybolur → kaynaksız iddia riski.
- Ayrılmış yazma/okuma (ingest/query) yoksa eski parça güncellenmediğinde çelişki kalır (bkz. [[ai-agent-memory]]).

Kaynak: `.ai/raw/test-kaynak.md`

## İlgili Sayfalar
- [[embedding]] — sonraki adım
- [[vector-database]] — kullanım ortamı
- [[ai-agent-memory]] — üst kavram
- `.ai/raw/test-kaynak.md` — birincil kaynak
