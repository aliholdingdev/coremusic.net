---
node_id: K000-isletim-sistemi/isletim-sistemi-api/ag-api
node_type: subdomain-scope
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api
children:
  - K000-isletim-sistemi/isletim-sistemi-api/ag-api/socket-api-temelleri
  - K000-isletim-sistemi/isletim-sistemi-api/ag-api/udp-tcp-ve-tls-api
  - K000-isletim-sistemi/isletim-sistemi-api/ag-api/posix-socket-ve-winsock-karsilastirmasi
depends_on: []
evidence:
  - https://man7.org/linux/man-pages/man2/socket.2.html
  - https://www.rfc-editor.org/rfc/rfc9293
  - https://learn.microsoft.com/en-us/windows/win32/winsock/windows-sockets-start-page-2
validation: []
open_questions: []
group: mimari
---

# ag-api — Alt-dal Künyesi (Kademe-3)

**ELI15:** Ağ üzerinden konuşan her uygulamanın (tarayıcı, API istemcisi, streaming
istemcisi) kullandığı **soket arayüzü** bu alt-dalın konusudur. İşletim sistemi
`isletim-sistemi-api` dalının 2. katmanında (kütüphane wrapper) ve 3. katmanında
(platform API'si) görünen **ağ sırtı** burada derinleştirilir.

| Künye | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/ag-api |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api |
| Kademe | 3 — alt-dal kapsamı (subdomain-scope) |
| Çocuk sayısı | 3 (disk kanıtlı — bu turda yazıldı) |
| Durum | draft |
| Grup | mimari (tüm dosyalarda 9. alan `group`) |

## §1 Kapsam — bu alt-dal neyi taşır

| # | Kapsam başlığı | Karşıladığı dosya | Odak |
|---|---|---|---|
| 1 | Soket temelleri | `socket-api-temelleri.md` | Adresleme, oluşturma, bağlama, aktarım, descriptor yaşam döngüsü, hata modları |
| 2 | Transport semantiği + TLS | `udp-tcp-ve-tls-api.md` | TCP akış, UDP dagram, non-blocking çoğullama, TLS sarmalama, ölçüm |
| 3 | POSIX ↔ Winsock farkı | `posix-socket-ve-winsock-karsilastirmasi.md` | Startup/teardown, tip ve hata kodu farkları, taşınabilir sarmalama stratejisi |

> Bu alt-dal **çekirdek ağ yığınını değil**, uygulamanın gördüğü **soket API
> yüzeyini** belgeler. Paket yönlendirme, TCP durum makinesi iç yapısı ve NIC
> sürücüsü `isletim-sistemi-kernel` / `isletim-sistemi-core` sınırlarındadır.

## §2 Üst dal ile ilişki

| Üst dal katmanı | Bu alt-daldaki karşılığı |
|---|---|
| Katman 2 — Kütüphane wrapper | POSIX `libc` soket çağrıları (`socket` · `bind` · `connect` · `send` · `recv`) |
| Katman 3 — Platform API'si | Winsock2 (`WSAStartup` · `SOCKET` · `closesocket`) — `platform-api` alt-dalı ile ortak sınır |

## §3 Repo kanıtı (disk)

| # | Repo dosyası | Ne gösterir |
|---|---|---|
| R1 | `shared/src/AI/ToolCalling.php:256` — `$ch = curl_init($url);` | PHP `curl` sarmalayıcısı nihayetinde TCP soketi (POSIX'te `libc`, Windows'ta Winsock) kullanır |
| R2 | `shared/src/Api/Registry/ServiceRegistry.php:102` — `$context = stream_context_create([` | PHP stream katmanı soket üstünde TLS ayarlarını taşır |

> Çıkarım: CoreMusic PHP kodu soketi **doğrudan çağırmaz**; `curl` / stream
> sarmalayıcıları üzerinden örtük kullanır. Doğrudan soket kodu L0 (Neva Engine)
> katmanında yazılacaktır — bu alt-dal o tasarımın referansıdır.

## §4 Sınır komutları

| Dışarıda olan | Nereye gider |
|---|---|
| Ses akışı soket semantiği (RTP, audio streaming davranış) | `isletim-sistemi-audio` |
| Kernel ağ yığını iç yapısı (TCP state machine, congestion control algoritması) | `isletim-sistemi-kernel` |
| Win32 genel API yüzeyi (COM, DLL, WinRT) | `platform-api` alt-dalı |
| Uygulamanın HTTP müşteri kütüphanesi (PHP tarafı) | K011-application |

## §5 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-A1 | L0 motorunun ağ özelliği (uzaktan kontrol / streaming) gerçek mi, yoksa kapsam dışı mı? | ⚠️ VERIFICATION REQUIRED |
| Q-A2 | hedefte IPv6 zorunlu mu, IPv4 yeterli mi? (sartname 512/48 kHz ölçüm maddesi ağ katmanını kapsamıyor) | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Alt-dal künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `socket-api-temelleri.md` | Soket adresleme, yaşam döngüsü ve hata modları | Kademe-3 |
| mimari | `udp-tcp-ve-tls-api.md` | Transport semantiği, çoğullama desenleri, TLS, ölçüm | Kademe-3 |
| mimari | `posix-socket-ve-winsock-karsilastirmasi.md` | POSIX ↔ Winsock fark matrisi ve taşınabilir sarmalama | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
