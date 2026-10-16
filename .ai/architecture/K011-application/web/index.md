---
node_id: K011-application/web
node_type: subdomain
status: active
parent_id: K011-application
children:
  - "web-kapsam.md"
  - "web-dogrulama.md"
  - "web-web-arastirma.md"
  - "web-ornekler.md"
  - "web-bilesenler.md"
depends_on: []
evidence:
  - "v2 §2.11 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.11 ile birebir karşılaştırıldı — 2026-10-12"
  - "Ünite-2 kapısı: 5 içerik dosyası ≥500 satır · §GRUPLAR · footer — 2026-10-15"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Kademe-3 kapsamı: hangi BC'ler web'e özel? → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# web — K011-application (kademe-2 · subdomain)

**ELI15:** Tarayıcıda çalışan arayüz katmanı: sayfa, bileşen, stil ve form akışları.

**Sınır (ne DEĞİL):** İş kuralı K008-domain'de · servis sahibi K012 · ağ taşıması K014.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler application-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** active (Ünite-2 içerik üretildi 2026-10-15) · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **`group` son alan**.

| Grup | Dosya | Amaç | Kademe | Eksen |
|---|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 | kayıt × indeks |
| mimari | `web-kapsam.md` | Kapsam/sınır/katman tanımı + bileşen haritası | Ünite-2 | mimari × kapsam |
| kanit | `web-dogrulama.md` | Kapı kontrolleri, envanter, betik devri | Ünite-2 | kanıt × kapı |
| kanit | `web-web-arastirma.md` | Havuz 9 URL (tek kullanım yeri) + claim matrisi | Ünite-2 | araştırma × kanıt |
| kod | `web-ornekler.md` | PHP/JS/CSS/SQL gerçek kod alıntıları + YAPMA↔YAP | Ünite-2 | kod × anti-pattern |
| mimari | `web-bilesenler.md` | Envanter sayımı, bağımlılık grafiği, boşluklar | Ünite-2 | bileşen × sayım |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
