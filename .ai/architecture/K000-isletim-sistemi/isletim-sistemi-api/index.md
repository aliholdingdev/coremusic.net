---
node_id: K000-isletim-sistemi/isletim-sistemi-api
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: []
evidence: []
validation:
  - "Çocuk adı v2 §2.1 mini-ağacından birebir alındı (2026-10-12)"
open_questions:
  - "Hangi API yüzeyi kastediliyor (kernel API mi, kullanıcı uzay hizmetleri mi)? → ⚠️ VERIFICATION REQUIRED"
---

# isletim-sistemi-api — Kademe-2 Kayıt

**ELI15 (ad-türevi):** İşletim sisteminin uygulamalara açtığı çağrı/arayüz yüzeyi.

**Kapsam / sınır:** Syscall, sistem servisleri ve uygulama sözleşmesi bu alt dalın konusudur (ad-türevi).

| Öğe | Değer |
|---|---|
| parent_id | K000-isletim-sistemi |
| Kademe | 3 · subdomain |
| Master referansı | os-master.md §3 (satır listesi) |
| Alt kademe | bu turda AÇILMADI (children: []) |
| Durum | draft |

**⚠️ VERIFICATION REQUIRED:** v2 §2.1 bu klasörü yalnız isim olarak listeler; ne işe
yaradığını tek tek yazmaz. Yukarıdaki özet **ad kökeninden** türetilmiştir, kanıt değildir.
Kapsam üst kademede (os-master.md §7 açık soruları) onaylanana kadar bu dosyaya
tahmini içerik eklenmez — v2 §3 KURAL 3.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12