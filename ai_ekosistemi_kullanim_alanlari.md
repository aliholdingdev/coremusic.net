# CoreMusic Yapay Zekâ (AI) Ekosistemi ve Ajanlarının Kullanım Alanları

CoreMusic projesi; salt bir son kullanıcı ürününün ötesinde, kendi geliştirme, denetleme ve sürdürme süreçlerini otonom olarak yöneten 11 farklı uzmanlık alanına sahip **"CoreMusic AI Engineering"** yapay zekâ ajanları (agents) ekosistemiyle inşa edilmiştir.

Geleneksel yazılım geliştirme süreçlerindeki dağınık kodlama ve mimari tutarsızlıkları kökten reddeden bu AI platformu; Single Source of Truth (Tek Gerçeklik Kaynağı - SSOT) prensibiyle çalışır. Her bir ajan, belirli bir domain (alan) üzerinde uzmanlaşarak sistemin kod kalitesini, güvenlik duvarlarını ve kusursuz akustik mimarisini koruma altına alır.

**Mimari Yönetim, Karar Alma ve Orkestrasyon (Master Orchestrator):**
Sistemin beyni olan Master Orchestrator, karmaşık ve çok katmanlı yapıdaki (10 bağımsız subdomain) tüm mimari değişiklikleri koordine eder. Projenin "Vanilla JS zorunluluğu" veya "Sıfır ORM (Sadece PDO)" gibi katı mimari kurallarını (Hard Guardrails) denetleyerek, sistemde teknik borç oluşturacak yanlış teknoloji seçimlerini anında engeller ve görevleri doğru alt ajanlara yönlendirir.

**Backend, API ve BCNF Veritabanı Mimarisi:**
CoreMusic'in arka planındaki devasa veri yükünü hafifletmek için **Backend Architect** ve **Data Engineer** ajanları devreye girer. API uç noktaları Clean Architecture (Temiz Mimari) standartlarında kodlanırken; 18 bağımsız veritabanı Boyce-Codd Normal Formunda (BCNF) tasarlanır. Bu ajanlar, hantal ORM yapılarını reddederek doğrudan optimize edilmiş ham PDO sorguları ve zaman indeksli UUID v7 yapıları üretir.

**Kusursuz Arayüz ve UI/UX Tasarımı:**
Kullanıcı arayüzleri, ağır framework'ler (React, Vue vb.) kullanılmadan **UI Designer** ajanı tarafından doğrudan Vanilla JS ve ITCSS (9 Katmanlı CSS) mimarisiyle inşa edilir. Ajan, üretilen tüm ekranların 19 adet kanonik (SSOT) mockup tasarımına birebir uymasını, 60 FPS akıcılığında çalışmasını ve WCAG 2.2 AA erişilebilirlik standartlarına tam uyum sağlamasını garanti eder.

**Proaktif Güvenlik (Red Team Modu) ve Kalite Güvencesi:**
Sistemin dışarıdan gelebilecek her türlü saldırıya karşı korunması **Security Engineer** ajanının sorumluluğundadır. OWASP 2025 standartlarını baz alarak XSS ve CSRF zafiyetlerini arayan ajan, koda asla hassas verilerin yazılmamasını sağlar. Ayrıca, kodlar canlı ortama geçmeden önce **QA Engineer** ajanı tarafından "Truth Mode" çerçevesinde Playwright, Vitest ve PHPUnit testlerinden geçirilerek kanıta dayalı kalite onayı alır.

**Donanım Entegrasyonu, DSP ve Ses Motoru (Neva Engine):**
CoreMusic'i fiziksel donanımla (Raspberry Pi 5, XMOS DAC) buluşturan köprü, gömülü sistem ajanlarıdır. **Embedded Engineer**, **Audio HW Engineer** ve **DSP Firmware Engineer**; ASIO ve WASAPI standartlarında 32-Bit Float ses motorunun (<10ms gecikmeli) geliştirilmesinden ve 8.1 surround ses dağıtım matrisinin stüdyo kalitesinde (THD+N < %0.01) işlenmesinden sorumludur.

**Kesintisiz Dağıtım ve DevOps (CI/CD):**
Üretilen mükemmel kodun sunuculara veya cihazlara sorunsuz bir şekilde taşınması **DevOps Engineer** ajanının görevidir. Docker 24+ container altyapısını yöneten ajan, ev medya merkezleri (home.coremusic.net) veya araç içi sistemler için cihazlara özel, hafifletilmiş Linux imajlarının derlenme ve dağıtılma süreçlerini otonom olarak gerçekleştirir.
