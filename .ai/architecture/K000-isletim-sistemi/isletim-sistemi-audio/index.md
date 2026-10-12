---
node_id: K000-isletim-sistemi/isletim-sistemi-audio
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: []
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (2026-10-12) · https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (2026-10-12)"
validation:
  - "Çocuk adı v2 §2.1 mini-ağacından birebir alındı (2026-10-12)"
open_questions:
  - "Linux/macOS/Android ses alt sistemleri kanıtlanmadı → ⚠️ VERIFICATION REQUIRED"
---

# isletim-sistemi-audio — Kademe-2 Kayıt

**ELI15 (ad-türevi):** İşletim sisteminin ses alt sistemi — uygulama ile ses uç noktası arasındaki katman.

**Kapsam / sınır:** Windows tarafında Core Audio / WASAPI ailesi kanıtlanmıştır; diğer platformlar UNKNOWN.

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