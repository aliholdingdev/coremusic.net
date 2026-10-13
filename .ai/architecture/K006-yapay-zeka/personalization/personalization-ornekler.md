---
title: "personalization — Örnekler"
type: architecture-examples
node_id: K006-yapay-zeka/personalization
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# personalization — Örnekler

## §1 Gerçek repo kodu (ÖNCELİK 1 — bu oturumda okunarak doğrulandı)

`shared/src/AI/AIWorkflow.php` satır 52–56 · 75–78 — oturum bağlamının öneriye enjeksiyonu:

```php
$sessionId = $request['session_id'] ?? 'anonymous';

// 1. Context collect — session'dan tercihleri çek
$context = $this->memory->getSession($sessionId, 'preferences') ?? [];
$context = array_merge($context, $request);
...
$recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10);

// 6. Feedback — sonucu kaydet
$this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);
```

`shared/src/AI/AIEngine.php` satır 52–56 — kişisel bağlam alanları:

```php
$userId = $context['user_id'] ?? null;
$sessionId = $context['session_id'] ?? null;
$mood = $context['mood'] ?? null;
$genre = $context['genre'] ?? null;
$history = $context['history'] ?? [];
```

`shared/src/AI/AIEngine.php` satır 211–213 — anonim kullanıcıda collaborative kolu devre dışı:

```php
if ($userId === null) {
    return [];
}
```

## §2 Resmi doküman örneği (URL + snippet)

**Amazon Personalize — Recording real-time events** ·
https://docs.aws.amazon.com/personalize/latest/dg/recording-events.html (doğrulandı 2026-10-12):

> "If your domain use case or custom recipe supports real-time personalization, Amazon Personalize uses
> events in real time to update and adapt recommendations according to a user's evolving interest."

> "For anonymous users (users who haven't logged in), for each event specify only a `sessionId`."

## §3 Uygulama notu

- Repo örneği ile resmi örnek aynı **anonim → kayıtlı geçiş** fikrini paylaşır; CoreMusic'in hangi servisi
  kullanacağı **UNKNOWN** (kapsam §5/R4).
- `getCollaborativeScores` anonimde `[]` döndüğü için kişiselleştirmenin gücü içerik koluna bağlı.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `personalization-kapsam.md` | bu dalın rolü, içinde/dışında, açık sorular |
| `personalization-dogrulama.md` | iddia → URL/dosya kanıt tablosu |
| `../index.md` · `../ai-master.md` §1/§3 | üst düğüm künyesi + çocuk tablosu (v2 satır 262) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
