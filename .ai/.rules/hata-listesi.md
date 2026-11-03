---
title: "CoreMusic — Hata Listesi & Önleme Kataloğu (Error Prevention Catalog)"
type: rules
category: error-prevention
version: 1.1.0
status: active
authority: "SSOT: .ai/.rules/hata-listesi.md"
updated: 2026-11-01
---

# HATA LİSTESİ — Hata Önleme & Doğrulama Kataloğu

> **Kural (PDF Standardı):** Bir hata veya kritik tuzak keşfedildiğinde buraya tek satır olarak işlenir; her teslim veya kod yazımı öncesinde taranarak doğrulanır.

---

| Kod | Kural / Tuzak | Önleme Standardı |
|---|---|---|
| **HL-01** | **Tema Zorunluluğu (YALNIZCA LIGHT MODE)** | CoreMusic'te ŞU ANDA YALNIZCA Light Mode mevcuttur. Dark Mode henüz tasarlanmamıştır/yoktur. Dark mode varsayımı, dark class'ları veya `@media (prefers-color-scheme: dark)` yazmak KESİNLİKLE YASAKTIR. |
| **HL-02** | **Amplifikatör Mimarisi (CLASS AB)** | Donanım ve DSP amfi topolojisi kesinlikle discrete Class AB'dir. Class D mimarisi veya PWM varsayımı yasaktır. |
| **HL-03** | **Sürüm Kontrolü (Subagent Commit Yasağı)** | Subagent `git commit` ATAMAZ; commit yetkisi yalnızca kullanıcıya ve ana orkestratöre aittir. |
| **HL-04** | **CSS Container Query Disiplini** | `container-type: inline-size` kuralı ölçülecek kapsayıcının kendisine konmalıdır; flex sütunlar `minmax(0, ...)` yapılmalıdır (aksi halde başlık ~0px çöker). |
| **HL-05** | **JavaScript DOM Yaşam Döngüsü (Güvenli Init)** | DOM'da henüz mevcut olmayan container ile init eden JS modülleri sessiz kalmamalıdır; başarısızlıkta `dbg.warn` verip `_initialized = false` bırakmalıdır. |
| **HL-06** | **Sayısal Oran Hassasiyeti (Ham Oran Eşikleri)** | PHP ile JS arasındaki eşik karşılaştırmaları yuvarlanmış yüzde üzerinden değil, ham oran (`$volRatio <= 0.02 / 0.33 / 0.66`) üzerinden yapılmalıdır. |
| **HL-07** | **Kimlik Bilgisi Güvenliği (Figma Token İzolasyonu)** | `FIGMA_TOKEN` asla `.md`, `.json` veya log dosyalarına yazılmaz; yalnızca `.ai/.env.figma` dosyasından okunur (`.gitignore:69`). |
| **HL-08** | **Veritabanı Bütünlüğü (Migration Zorunluluğu)** | Canlı DB şeması doğrudan `CREATE DATABASE` veya manuel DDL ile değiştirilemez; migration dosyası hazırlanır ve kullanıcı onayı alınır. |
| **HL-09** | **Yetkilendirme Sözlüğü (Standart RBAC Rolleri)** | Kod veya route seviyesinde sözlük dışı rol (`requiredRole`) uydurulamaz; uydurulan roller 403 Access Denied hatasına yol açar. |
| **HL-10** | **Ağ & Güvenlik Başlıkları (Antivirüs/Kaspersky Ayrıştırması)** | Antivirüs yazılımlarının enjekte ettiği script/style hataları sunucu kaynaklı sanılmamalıdır; sunucu CSP başlığı `curl -I` ile teyit edilmelidir. |
| **HL-11** | **Token & Oturum Optimizasyonu (Tek Sayfalık Hafıza)** | Tüm geçmişi tek sohbette biriktirme. Konu değiştiğinde, büyük analiz bittiğinde veya her teslimden sonra `durum.md` dosyasını güncelle ve yeni sohbete geç. |
| **HL-12** | **Stil Mimarisi Bütünlüğü (ITCSS İzolasyonu)** | Mevcut CSS sınıfları ve katman import zinciri onay ve ADR olmadan taşınamaz/silinemez; yanlış yerleşim yalnızca raporlanır. |
