---
title: "K000 Win32 Olay Döngüsü ve Mesaj Kuyruğu — GetMessage/DispatchMessage, WindowProc ve İstemci Olay Akışı"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 · K000-K071"
updated: 2026-10-06
---

# K000 — Win32 Olay Döngüsü ve Mesaj Kuyruğu

> **K numarası:** K000 · **Klasör:** `k000-windows-core` · **Dosya:** `win32-olay-dongusu-ve-mesaj-kuyrugu`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `windows-software-engineer` (Win32 yüzeyi)
> **İlgili ADR'ler:** `⚠️ VERIFICATION REQUIRED` (ADR-017 / ADR-019 bu klasörde dosya olarak yok)

## 1. Kapsam ve Bağlam

Bu belge, Windows tarafında kullanıcı arayüzünün çalıştığı **olay döngüsünü** (message loop) ve
**mesaj kuyruğunu** tanımlar: `GetMessage` / `PeekMessage` / `DispatchMessage` çağrısı akışı,
`WindowProc` pencere prosedürü, mesajların thread kuyruklarına yazılması ve CoreMusic masaüstü
isteminin bu döngü üzerindeki olay akışı.

Belge, çekirdek mimariyi anlatan `[[windows-core-mimari]]` ile API yüzeyini anlatan
`[[windows-api-yuzeyi]]` dosyalarının kardeşidir. **Sınır kuralı:** bu belge K0 (İşletim Sistemi)
kapsamındadır; ses sinyalinin işlendiği gerçek zamanlı yol ASIO/WASAPI tarafındadır
(`[[asio-cekirdek-entegrasyonu]]` · `[[wasapi-ses-yolu-cekirdek]]`) ve olay döngüsü **o yolun
parçası değildir** — yalnızca kontrol/komuta akışını taşır.

### 1.1 Dosya İlişkileri

| İlişki | Dosya |
|---|---|
| Klasör dizini | `[[index]]` |
| Kardeş (çekirdek) | `[[windows-core-mimari]]` |
| Kardeş (API yüzeyi) | `[[windows-api-yuzeyi]]` |
| Kardeş (ASIO gerçek zamanlı yol) | `[[asio-cekirdek-entegrasyonu]]` |
| Kardeş (WASAPI ses yolu) | `[[wasapi-ses-yolu-cekirdek]]` |
| Aşağı sürücü katmanı | `[[../k014-surucu-yigin/index]]` |
| Platform sürücüleri | `[[../k015-platform-suruculeri/index]]` |
| Kardeş OS'ler | `[[../k001-linux-rpi5/index]]` · `[[../k002-macos-tasinabilirlik/index]]` |

### 1.2 Kapsam Sınırı (k000)

| Konu | Durum |
|---|---|
| Win32 mesaj döngüsü, `WindowProc`, thread mesaj kuyruğu | ✅ Bu belgede |
| Uygulama içi komuta kuyruğu (lock-free) — ses motoruna geçiş | ✅ Bu belgede (sınır tanımı) |
| ASIO `bufferSwitch` gerçek zamanlı callback'i | → `[[asio-cekirdek-entegrasyonu]]` |
| WASAPI `IAudioClient` olay handle'ı | → `[[wasapi-ses-yolu-cekirdek]]` |
| Windows token/ACL/CFG güvenlik detayı | → `[[windows-guvenlik-ve-olcullu-kisitlar]]` |
| ETW ve performans ölçüm detayı | → `[[windows-performans-ve-gozlemlenebilirlik]]` |
| K1 donanım (DAC/ADC, amplifikatör) | `[KAPSAM DIŞI]` |
| K5 veri (MySQL, önbellek, kalıcılık) | `[KAPSAM DIŞI]` |

## 2. Gömülü Kaynaklar

| # | Kaynak (salt-okunur) | Satır | Not |
|---|----------------------|------:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | 423 | COM/WASAPI örnek kodu, IPC named pipe |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | 659 | Named Pipes + Message Queues (POSIX) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | 693 | §2 Windows NT API (ntdll) |
| 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 732 | §9 Zaman Servisi, §6 IPC Manager |
| 5 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` | 97 | katman bağımlılıkları (eski) |
| 6 | `_backup/arch-2026-10-06_1057/architecture/katman-baglilik-matrisi.md` | 523 | kanonik K0–K20 matrisi |
| 7 | `.ai/architecture/k000-windows-core/windows-api-yuzeyi.md` | 677 | mevcut durum (salt-okunur, değiştirilmedi) |

> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` §9 (IPC — Named Pipes) ve §8 (COM Interface).

## 3. Win32 Olay Döngüsü — Temeller

> **Etiket:** `ARAŞTIRMA REFERANSI` — aşağıdaki API adları ve akış Win32 platform sözleşmesidir;
> bu depoda buna karşılık gelen bir belge **yoktur** (grep: `GetMessage|DispatchMessage|WindowProc`
> → `_backup/architecture/` içinde 0 eşleşme). Sürüm, zaman aşımı ve gecikme rakamı **üretilmemiştir**.

### 3.1 Kayıt → Pencere → Döngü Zinciri

| Adım | Çağrı | Sorumluluk |
|---|---|---|
| 1 | `RegisterClass` / `RegisterClassEx` | `WNDPROC` adresini sınıf ile ilişkilendirir |
| 2 | `CreateWindow` / `CreateWindowEx` | Pencereyi (thread'in istemcisiyle) oluşturur |
| 3 | `GetMessage` | Kuyruktan mesaj **çekmesini bekler** (bloklayıcı) |
| 4 | `TranslateMessage` | Tuş basımını `WM_CHAR` karakter mesajına çevirir |
| 5 | `DispatchMessage` | Mesajı pencerenin `WindowProc`'una iletir |
| 6 | `PostQuitMessage` → `WM_QUIT` | `GetMessage` 0 döndürür, döngü biter |

### 3.2 Klasik Mesaj Döngüsü (kod örneği)

```cpp
// K0 — Win32 olay döngüsü (ARAŞTIRMA REFERANSI)
MSG msg;
while (GetMessage(&msg, nullptr, 0, 0) > 0) {
    TranslateMessage(&msg);
    DispatchMessage(&msg);
}
// GetMessage: WM_QUIT -> 0 (döngü sonu), hata -> -1, mesaj -> 0 olmayan
return static_cast<int>(msg.wParam);
```

### 3.3 Bloklayıcı Olmayan Varyant (oyun/döngü bütçesi olan istemciler)

```cpp
// PeekMessage: kuyruk boşsa döngü hemen geri döner (polling)
while (running) {
    while (PeekMessage(&msg, nullptr, 0, 0, PM_REMOVE)) {
        if (msg.message == WM_QUIT) { running = false; break; }
        TranslateMessage(&msg);
        DispatchMessage(&msg);
    }
    // Döngünün geri kalanı: çizim / komuta işleme (RT ses YOLU BURADA DEĞİL)
    renderFrame();
}
```

| Seçenek | Davranış | Kullanım |
|---|---|---|
| `GetMessage` | Kuyruk boşsa bekler | Standart pencere uygulaması |
| `PeekMessage` + `PM_REMOVE` | Kuyruk boşsa dönmedi devam eder | Çizim döngüsü olan istemci |
| `MsgWaitForMultipleObjects` | Mesaj + handle bekleme birleşik | WASAPI/ASIO event handle'ı ile kuyruk beklemesi |
| `GetMessage` + filtre | Belirli pencere/ID aralığını çeker | Yardımcı pencere iş parçacığı |

> `MsgWaitForMultipleObjects` kombinasyonu bu belgede **kavramsal** olarak tanımlanmıştır;
> uygulama kodu depoda yok → `⚠️ VERIFICATION REQUIRED` (uygulanacak istemci mimarisi henüz yok).

### 3.4 WindowProc (Pencere Prosedürü)

```cpp
// K0 — WindowProc: mesajların uygulamaya tek giriş kapısı
LRESULT CALLBACK CoreMusicWindowProc(HWND hwnd, UINT msg,
                                     WPARAM wParam, LPARAM lParam) {
    switch (msg) {
    case WM_CREATE:      return 0;                 // kurulum
    case WM_SIZE:        onResize(LOWORD(lParam),  // yeniden boyut
                                 HIWORD(lParam));
                         return 0;
    case WM_APP + 1:     onCommandFromWorker(wParam); // uygulama özel mesaj
                         return 0;
    case WM_DESTROY:     PostQuitMessage(0);       // döngüyü bitir
                         return 0;
    default:             return DefWindowProc(hwnd, msg, wParam, lParam);
    }
}
```

**Kurallar:**

| # | Kural | Gerekçe |
|---|---|---|
| 1 | İşlenmeyen mesajlar `DefWindowProc`'a gider | Varsayılan pencere davranışı korunur |
| 2 | `WM_APP + n` aralığı uygulamaya özeldir | Sistem mesajlarıyla çakışma önlenir |
| 3 | `WindowProc` içinde **bloklayıcı çağrı yok** | UI thread'i beslenmezse çizim durur |
| 4 | `WindowProc` içinde gerçek zamanlı ses işi **yapılmaz** | RT yol ASIO callback'inde çalışır |
| 5 | Uzun iş worker thread'e devredilir | Döngü gecikmesi sınırlı kalır |

## 4. Mesaj Kuyruğu ve Eşzamanlılık

> **Etiket:** `ARAŞTIRMA REFERANSI` — genel Win32 semantiği; depoda karşılık belgesi yok.

### 4.1 Kuyruk Sahipliği

| Kuyruk | Sahibi | Not |
|---|---|---|
| Thread mesaj kuyruğu | Oluşturan thread | `PostThreadMessage` / `PostMessage` hedefi |
| Pencere kuyruğu | `CreateWindow`'u çağıran thread | Mesajlar `WindowProc`'a `DispatchMessage` ile gider |
| Sistem/girdi kuyruğu | `⚠️ VERIFICATION REQUIRED` | Yerel/girdi kuyruğu ayrımı ayrıntısı bu depoda doğrulanmadı |

### 4.2 Post vs Send

| Çağrı | Bloklayıcı mı? | Kim yürütür? | Kuyruğa yazar mı? |
|---|---|---|---|
| `PostMessage` | Hayır | Hedef thread kendi döngüsünde | ✅ Evet |
| `PostThreadMessage` | Hayır | Hedef thread | ✅ Evet |
| `SendMessage` | **Evet** (hedef işleyene kadar) | Mesaj gönderen de işleyebilir (yerel/uzak kuralı) | Hayır (doğrudan çağırır) |
| `SendNotifyMessage` | Yerel hedefte evet, uzakta hayır | Hedef thread | Koşullu |
| `PostQuitMessage` | Hayır | Çağıran thread'in kuyruğuna `WM_QUIT` | ✅ Evet |

### 4.3 Eşzamanlılık Riskleri

| # | Risk | Belirti | Önlem |
|---|---|---|---|
| E1 | Çapraz thread `SendMessage` iki taraf da birbirini beklerse | Karşılıklı bekleme (deadlock) | Uzun iş için `PostMessage` |
| E2 | `WM_QUIT`'ten sonra kalan mesajlar | Çökme / yarım temizlik | `WM_DESTROY` → `PostQuitMessage`, kuyruk boşaltılır |
| E3 | Worker thread'in `WindowProc`'a sahip olmaması | Mesajlar teslim edilmez | Pencereler yalnızca UI thread'inde oluşturulur |
| E4 | Kuyruk taşması (yoğun `PostMessage`) | Gecikme / kayıp komuta | Kuyruk yerine lock-free komuta kuyruğu |
| E5 | Olay döngüsünün RT ses süresiyle yarışması | UI gecikmesi | RT yol ile UI tamamen ayrı thread/havuz |

## 5. CoreMusic Masaüstü İstemcisi — Olay Akışı

> **Etiket:** `⚠️ VERIFICATION REQUIRED` — depoda `Electron` için **kanıt yok** (grep `.md`
> geneli: eşleşmeler yalnızca `.ai/.agents/*` içindeki başka bağlamlar; `electron` bağımlılığı
> içeren bir `package.json` okunmadı). Bu nedenle istemci teknolojisi **`[UNKNOWN]`** olarak
> işaretlenmiştir; aşağıdaki akış **teknolojiden bağımsız** K0 sözleşmesidir.

### 5.1 Katmanlar

```
┌──────────────────────────────────────────────────────────┐
│  İSTEMCİ (teknoloji: [UNKNOWN] — Electron / yerel C++)    │
│  ┌────────────────────────────────────────────────────┐  │
│  │ Olay döngüsü: GetMessage / DispatchMessage          │  │
│  │ WindowProc → komuta üretir                          │  │
│  └───────────────────────┬────────────────────────────┘  │
│                          │ komuta (lock-free kuyruk)      │
│  ┌───────────────────────▼────────────────────────────┐  │
│  │ K0 çekirdek yüzeyi: thread / bellek / zaman         │  │
│  └───────────────────────┬────────────────────────────┘  │
└──────────────────────────┼───────────────────────────────┘
                           │
              K2 sürücü yığını → ASIO/WASAPI callback (RT)
```

### 5.2 Komuta Akışı Tablosu

| Olay | Kaynak | Hedef | Taşıyıcı | RT yol |
|---|---|---|---|---|
| Çal/duraklat butonu | `WindowProc` (`WM_COMMAND`) | Ses motoru | Lock-free komuta kuyruğu | ❌ |
| Örnekleme oranı değişikliği | `WindowProc` | Sürücü yeniden başlatma | Kuyruk + tampon takası | ❌ (yeniden başlatma anında durur) |
| Cihaz tak/çıkar | Sistem mesajı | Sürücü yeniden seçim | Kuyruk | ❌ |
| Buffer underrun bildirimi | ASIO/WASAPI callback | Gözlem sayacı | Atomik sayaç (kuyruk değil) | ✅ (yazan taraf RT) |
| Kapanış | `WM_CLOSE` → `WM_DESTROY` | Sıralı kapanış | Sıra: akış → tampon → sürücü | ❌ |

> **Sınır kuralı (bu belge asli kararı):** UI → ses yönünde **komuta** kuyruğu, ses → UI yönünde
> **atomik sayaç/olay** akışı kullanılır. `SendMessage` ile RT thread'e asla ulaşılmaz.

### 5.3 Kapsam İçi Kod İskeleti (kavramsal)

```cpp
// UI thread — komuta üretir, RT yoluna dokunmaz
struct Cmd { uint32_t type; uint32_t payload; };   // POD, tahsis yok

// RT tarafı önceden allocate eder; UI yalnızca slot yazar (lock-free)
void postCommand(const Cmd& c) noexcept {
    cmdQueue.push(c);        // std::atomic tabanlı, yasaklı örüntü değil
}
```

Yasaklı örüntüler (kaynak: gömülü `CLAUDE.md` §3) bu yolda da geçerlidir:
`malloc()`, `free()`, `new`, `delete`, `std::vector::push_back` kullanılmaz.

## 6. Ses Yolu ile Etkileşim (K0 Sınırı)

| Durum | Olay döngüsü rolü | RT yol |
|---|---|---|
| Normal çalma | Yalnızca durum/UI günceller | ASIO/WASAPI callback'i ayrı thread |
| Cihaz değişimi | Yeniden başlatma komutu yollar | Akış durdurulur → tampon → sürücü (sıra korunur) |
| UI thread'i askıda | Çizim durur; **ses devam eder** | Kuyruk RT'yi bloke etmez |
| RT callback'te `SendMessage` | **YASAK** | UI thread'i bloke ederse underrun |
| Olay döngüsü kapanışı | `PostQuitMessage` ile biter | Önce akış durdurulur, sonra döngü biter |

**Eşzamanlılık çizelgesi:**

1. `WM_CLOSE` gelir → kullanıcıya onay (varsa) → kapanış komutu.
2. Ses akışı durdurulur (`IAudioClient::Stop` / ASIO `stop`) — `[[wasapi-ses-yolu-cekirdek]]`.
3. Tamponlar serbest bırakılır, COM/`CoUninitialize` çağrılır.
4. `DestroyWindow` → `WM_DESTROY` → `PostQuitMessage(0)`.
5. `GetMessage` 0 döner, döngü ve thread biter.

## 7. Kenar Durumları

| # | Kenar durum | Koşul | Sonuç | Yaklaşım |
|---|---|---|---|---|
| E1 | Çapraz thread `SendMessage` | İki taraf birbirini beklerse | Karşılıklı bekleme | `PostMessage` + token |
| E2 | Kuyruk taşması | Çok hızlı komuta üretimi | Gecikme | Toplu komuta / atomik sayaç |
| E3 | `WM_QUIT` sonrası artakalan mesajlar | Döngü erken çıkar | Yarım temizlik | Kapanış sırası (§6) |
| E4 | Worker thread'de pencere | Oluşturan thread kapanır | Yetim pencere | Pencereler yalnız UI thread'inde |
| E5 | Modal döngü (`DialogBox`) | İç içe ikinci döngü | Sıra dışı akış | Tek döngü ilkesi; modal yerine durum |
| E6 | Görselleştirici/devre dışı pencere | `EnableWindow(false)` sırasında mesaj | Birikim | Kuyruğu kapanışta boşalt |

> **Kanıt:** E1–E6 bu belgenin analizidir; depoda karşılık gelen hata kaydı **yok** → uygulama
> sonrası gözlem `[[windows-performans-ve-gozlemlenebilirlik]]` §7'ye bağlanır.

## 8. Hata Modları

| # | Hata modu | Belirti | Sinyal | Sonuç |
|---|---|---|---|---|
| F1 | Döngünün hiç başlamaması | Pencere görünmez | `GetMessage` hata (-1) | Uygulama açılmaz |
| F2 | `DefWindowProc` çağrısının atlanması | Sistem davranışı bozulur | Boyut/minimiz eksik | Görüntü bozukluğu |
| F3 | RT → UI `SendMessage` | UI donması + ses takılması | underrun sayacı | Kesinti |
| F4 | `PostQuitMessage`'ın erken çağrılması | Kapanışta çökme | Ardışık `WM_DESTROY` | Veri kaybı riski |
| F5 | Uygulama özel mesaj aralığının çakışması | Yanlış işleyici tetiklenir | `WM_APP+n` çakışması | Beklenmedik davranış |

## 9. Bağımlılıklar

### 9.1 Kullandığı (yukarı)

- `[[windows-core-mimari]]` — NT çekirdek yüzeyi, thread/bellek primitifleri.
- Gömülü `README.md` §6 (IPC Manager) ve §9 (Zaman Servisi).

### 9.2 Kullanan (aşağı)

- `[[asio-cekirdek-entegrasyonu]]` — komuta/olay sınırı (RT yol bu döngüden **bağımsız**).
- `[[wasapi-ses-yolu-cekirdek]]` — olay handle'ı ile kuyruk bekleme kombinasyonu.
- `[[../k014-surucu-yigin/index]]` — kapanış/tampon sırası.

## 10. Performans ve Gözlem Notları

| Nokta | Ölçüm | Belge |
|---|---|---|
| Olay döngüsü gecikmesi | `⚠️ VERIFICATION REQUIRED` (ölçüm yok) | — |
| QPC çözünürlüğü | Eski belgede "~100ns" iddiası → doğrulanmadı | `README.md` §9.1 |
| Underrun sayacı | Atomik sayaç (kavramsal) | `[[windows-performans-ve-gozlemlenebilirlik]]` |
| Named Pipe gecikmesi | Eski belgede iki farklı hedef (§ÇELİŞKİ #1) | `README.md` §7 vs `ipc-mekanizmalari.md` §10 |

### 10.1 Bu Katman İçin Önerilen Gözlem Noktaları (kavramsal)

| # | Gözlem | Tip | Kaynak | K12 (İzleme) ile ilişki |
|---|---|---|---|---|
| O1 | Komuta kuyruğu taşma sayısı | sayaç (atomik) | UI yazımı | gösterim verisi ↓ |
| O2 | `WM_DESTROY` sonrası artakalan mesaj | sayaç | döngü temizliği | gösterim verisi ↓ |
| O3 | UI döngüsü bekleme süresi | histogram | `QPC` (§9.1) | gösterim verisi ↓ |
| O4 | Cihaz değişimi başlangıç/bitiş | olay kaydı | senaryo B (§16.2) | gösterim verisi ↓ |

> **K12 sınırı (kanonik):** `katman-baglilik-matrisi.md` §5.1 madde 10 → **K12 → K0 yasaktır
> (ORTA)**; §2.1'de K12'nin tek oku K8 sütunundadır. Bu yüzden Windows gözlem verisi bu katmandan
> **doğrudan** K12'ye akıtılmaz; akış K8 üzerinden yürür. Aksi halde Layer Violation → revert
> (`.ai/AGENTS.md` §5 / §18 madde 6).

### 10.2 Ölçüm Protokolü (bu katman)

1. Ölçüm `QueryPerformanceCounter` ile alınır (kaynak: `README.md` §9.1 — Zaman Servisi K0-09).
2. Ölçüm **RT thread'de değil**, UI thread'inde ve yalnız gözlem amaçlı yapılır.
3. Ölçüm kodu tahsis yapmaz (öneden ayrılmış dizi) — gömülü `CLAUDE.md` §3.
4. Sonuçlar `[[windows-performans-ve-gozlemlenebilirlik]]` §4'teki sayaca yazılır.
5. Eşik değeri **uydurulmaz**; eşik `⚠️ VERIFICATION REQUIRED` olarak bırakılır.

| Ölçüm hedefi | Eşik değeri | Durum |
|---|---|---|
| UI döngüsü gecikmesi | `[NOT PROVIDED]` | ölçülmedi |
| Komuta kuyruğu taşması | `[NOT PROVIDED]` | ölçülmedi |
| Kapanış süresi | `[NOT PROVIDED]` | ölçülmedi |

## 11. İddia → Kanıt Tablosu

| # | İddia | Kanıt türü | Kanıt |
|---|---|---|---|
| 1 | Win32 döngüsü `GetMessage`/`DispatchMessage` üzerine kurulur | `ARAŞTIRMA REFERANSI` | depoda karşılık belgesi yok |
| 2 | Pencereler yalnız UI thread'inde oluşturulur | `ARAŞTIRMA REFERANSI` | genel Win32 kuralı |
| 3 | UI → ses yalnız lock-free komuta kuyruğu ile geçer | `MEVCUT PROJE GERÇEĞİ` (tasarım) | gömülü `CLAUDE.md` §1-§3 |
| 4 | `alignas(64)` zorunlu | `MEVCUT PROJE GERÇEĞİ` | `README.md` §4.2 (K0-16) |
| 5 | RT thread'de `malloc`/`mutex` yasak | `MEVCUT PROJE GERÇEĞİ` | `CLAUDE.md` §1 madde 1-2 |
| 6 | Kapanış sırası: akış → tampon → sürücü | `MEVCUT PROJE GERÇEĞİ` | `windows-api-yuzeyi.md` §4 E5 |
| 7 | İstemci Electron mı? | `[UNKNOWN]` | depoda kanıt yok |
| 8 | Olay döngüsü gecikme değeri | `[NOT PROVIDED]` | ölçüm yok |

## 12. Bağlantı Haritası (diğer k000 dosyaları)

| Hedef | Ne için |
|---|---|
| `[[windows-core-mimari]]` | NT çekirdek, thread/bellek primitifleri |
| `[[windows-api-yuzeyi]]` | COM başlatma, ASIO/WASAPI yüzeyi |
| `[[wasapi-ses-yolu-cekirdek]]` | `IAudioClient` olay handle'ı, oturum |
| `[[asio-cekirdek-entegrasyonu]]` | RT callback, öncelik, tahsis yasağı |
| `[[windows-guvenlik-ve-olcullu-kisitlar]]` | pencere/pipe güvenlik tanımı |
| `[[windows-performans-ve-gozlemlenebilirlik]]` | gözlem sayaçları, ETW |
| `[[index]]` | klasör dizini |

## 11. Mesaj Türleri Referans Tablosu

> **Etiket:** `ARAŞTIRMA REFERANSI` — Win32 mesaj adları genel platform sözleşmesidir; bu depoda
> karşılık gelen uygulama kodu yoktur. Hiçbir mesaj kimliğine (sayısal değer) bu belgede yer
> verilmemiştir — `⚠️ VERIFICATION REQUIRED` (mesaj ID'leri SDK başlıklarından okunmalıdır).

| Mesaj | Yön | K000'de işlevi | RT yol |
|---|---|---|---|
| `WM_CREATE` | sistem → pencere | Kurulum, kaynak ayırma (UI thread) | ❌ |
| `WM_SIZE` | sistem → pencere | Yeniden boyutlandırma, çizim alanı | ❌ |
| `WM_PAINT` | sistem → pencere | Yeniden çizim | ❌ |
| `WM_CLOSE` | sistem → pencere | Kapanış niyeti (onay burada) | ❌ |
| `WM_DESTROY` | sistem → pencere | Kaynak serbest bırakma | ❌ |
| `WM_QUIT` | `PostQuitMessage` | Döngü sonu ( GetMessage → 0 ) | ❌ |
| `WM_COMMAND` | menü/düğme → pencere | Komuta (çal/duraklat/vb.) | ❌ |
| `WM_APP + n` | uygulama özel | İstemci ↔ istemci içi haberleşme | ❌ |
| `WM_DEVICECHANGE` | sistem → pencere | Cihaz tak/çıkar | ❌ |
| `WM_TIMER` | sistem → pencere | Zamanlanmış UI işi | ❌ |

**Kural:** tablodaki hiçbiri gerçek zamanlı ses tamponu taşımaz. Ses verisi yalnızca
`[[asio-cekirdek-entegrasyonu]]` §4 ve `[[wasapi-ses-yolu-cekirdek]]` §5'te tanımlı tamponlarda
bulunur.

## 12. Döngü Varyantları ve Seçim Matrisi

| Varyant | Bekleme | Ek maliyet | Uygun CoreMusic senaryosu | Öncelik |
|---|---|---|---|---|
| Klasik `GetMessage` döngüsü | Sonsuz (mesaj gelene kadar) | Düşük | Standart pencere/ayar ekranı | 1 |
| `PeekMessage` polling döngüsü | Yok (döner) | CPU tüketimi | Çizim döngüsü olan istemci | 2 |
| `MsgWaitForMultipleObjects` | Mesaj veya handle | Orta | WASAPI/ASIO event handle'ı bekleme | 3 |
| `GetMessage` + filtre | Daraltılmış | Düşük | Yardımcı pencere thread'i | 4 |
| Worker thread'de döngü yok | — | — | Worker'lar yalnız kuyruk dinler | varsayılan |

**Seçim kuralı:** CoreMusic istemcisi tek UI thread'inde **tek** mesaj döngüsü çalıştırır;
yardımcı thread'ler mesaj döngüsü kurmaz (yalnız işlevsel kuyruk). Birden fazla döngü yalnız
bağımsız pencere thread'i gereken durumda ve mimari onayı ile açılır.

## 13. Komuta Kuyruğu Tasarım Sözleşmesi (UI → Ses)

UI döngüsü ile RT yol arasındaki tek meşru köprü bu kuyruktur. Sözleşme:

| # | Kural | Kaynak |
|---|---|---|
| 1 | Elemanlar POD ve sabit boyutlu | gömülü `CLAUDE.md` §3 (yasaklı örüntüler) |
| 2 | Tahsis **önceden** yapılır; çalışma anında `new` yok | gömülü `README.md` §4.2 |
| 3 | `std::atomic` / `Interlocked*` kullanılır | `windows-api-yuzeyi.md` §3.2 tablosu |
| 4 | Critical Section / SRW Lock **yasak** | aynı kaynak |
| 5 | Taşma durumunda sessiz kayıp yok; sayac artar ve gözleme yazılır | `[[windows-performans-ve-gozlemlenebilirlik]]` §4 |
| 6 | Kuyruk boyutu sabittir (kapasite = tasarım değeri) | `⚠️ VERIFICATION REQUIRED` (kapasite seçilmedi) |

```cpp
// Kavramsal iskelet — tahsis yok, kilit yok
template <typename T, size_t N>
class SPSCQueue {                    // tek yazan (UI) + tek okuyan (RT)
public:
    bool push(const T& v) noexcept;  // doluysa false + taşma sayacı
    bool pop(T& out) noexcept;
private:
    alignas(64) std::atomic<size_t> head_{0};   // false-sharing önlenir
    alignas(64) std::atomic<size_t> tail_{0};
    T slots_[N];                                 // sabit dizi
};
```

> `alignas(64)` zorunluluğu: gömülü `README.md` §4.2 (Cache Line / false sharing) ve
> `K0-16 Cache Line` bileşeni (README §2).

## 14. Yaşam Döngüsü ile Mesaj Sırası

| Evre | Mesajlar | Beklenen sıra | Bozulursa |
|---|---|---|---|
| Başlangıç | `WM_CREATE` → `WM_SIZE` → `WM_PAINT` | Sıralı | Kurulum eksik |
| Çalışma | `WM_COMMAND` / `WM_DEVICECHANGE` / `WM_TIMER` | Sıra önemsiz (komuta kuyruğu) | Komuta kaybı |
| Kapanış isteği | `WM_CLOSE` → (onay) → `WM_DESTROY` | Sıralı | Yarım temizlik |
| Döngü sonu | `WM_QUIT` | `WM_DESTROY`'dan **sonra** | Erken çıkış |
| Kapanış sonrası | Artakalan `WM_*` | İşlenmez (döngü bitti) | Yetim kaynak |

**Zorunlu sıra:** ses akışı durdurma → tampon serbest bırakma → `DestroyWindow` → döngü bitişi.
Sıra `[[windows-api-yuzeyi]]` §4 kenar durum E5 ile uyumludur (kapanış sırası: akış → tampon → sürücü).

## 15. Denetim Listesi (kod öncesi)

- [ ] Tek UI thread'inde tek mesaj döngüsü var mı?
- [ ] `WindowProc` içinde bloklayıcı çağrı (dosya/ağ/kilit) var mı? → olmamalı
- [ ] RT thread'e `SendMessage` ile ulaşılmıyor mu? → olmamalı
- [ ] Komuta kuyruğu tahsis yapıyor mu? → `new`/`malloc` yasak
- [ ] `WM_DESTROY` → `PostQuitMessage` bağlanmış mı?
- [ ] Kapanış sırası (akış → tampon → sürücü) korunuyor mu?
- [ ] Uygulama özel mesaj aralığı `WM_APP + n` mi (sistem mesajı değil)?
- [ ] Gözlem: taşma/underrun sayaçları `[[windows-performans-ve-gozlemlenebilirlik]]` §4'e bağlı mı?

## 16. Senaryo Akışları (Adım Adım)

### 16.1 Senaryo A — Uygulama açılışı

| # | Adım | Thread | Çağrı/olay | Hata modu |
|---|---|---|---|---|
| 1 | Pencere sınıfı kaydı | UI | `RegisterClass` | F5 (§8) |
| 2 | Pencere oluşturma | UI | `CreateWindow` → `WM_CREATE` | F2 |
| 3 | Komuta kuyruğu hazırlığı | UI | önceden tahsis (kapasite sabit) | taşma sayacı |
| 4 | Ses yolu başlatma | worker | `IAudioClient::Start` / ASIO `start` | → F5 `[[wasapi-ses-yolu-cekirdek]]` §7 |
| 5 | Döngüye giriş | UI | `GetMessage` döngüsü | F1 |
| 6 | İlk çizim | UI | `WM_PAINT` | F2 |

### 16.2 Senaryo B — Cihaz değiştirme (sıcak tak-çıkar)

| # | Adım | Thread | Taşıyıcı | RT etkisi |
|---|---|---|---|---|
| 1 | Sistem bildirimi | sistem | `WM_DEVICECHANGE` | yok |
| 2 | Yeniden başlatma kararı | UI | komuta kuyruğu | akış durur |
| 3 | Akış durdurma | worker | `IAudioClient::Stop` / ASIO `stop` | tampon boşaltılır |
| 4 | Tampon/sürücü temizliği | worker | sıralı kapanış (E5) | tahsis yok |
| 5 | Yeni cihaz açma | worker | `Initialize` + `Start` | yeniden akış |
| 6 | Durum bildirimi | worker → UI | atomik sayaç + `WM_APP+n` | yazan RT, bloke değil |

### 16.3 Senaryo C — Kapanış

| # | Adım | Çağrı/olay | Zorunlu sıra |
|---|---|---|---|
| 1 | Kapanış isteği | `WM_CLOSE` | — |
| 2 | Onay (opsiyonel) | UI diyalog | döngü içinde |
| 3 | Ses yolunu durdur | akış → tampon → sürücü | **1. sırada** |
| 4 | Kaynakları bırak | `DestroyWindow` → `WM_DESTROY` | **2. sırada** |
| 5 | Döngüyü bitir | `PostQuitMessage(0)` → `WM_QUIT` | **3. sırada** |
| 6 | COM temizliği | `CoUninitialize` | **4. sırada** (§5.3) |

## 17. Risk Kayıt Defteri

| # | Risk | Olgasılık | Etki | Azaltma | Sahip |
|---|---|---|---|---|---|
| R1 | UI döngüsünün bloke edilmesi | Orta | Çizim durması (ses sürer) | `WindowProc`'ta bloklayıcı yok | `embedded-engineer` |
| R2 | RT → UI `SendMessage` | Düşük | Karşılıklı bekleme | Yalnız atomik sayaç + `PostMessage` | `embedded-engineer` |
| R3 | Komuta kuyruğu taşması | Orta | Komuta kaybı | Taşma sayacı + gözlem eşiği | `performance-engineer` |
| R4 | Kapanış sırasının ihlali | Orta | Askıda iş/çökme | §16.3 sırası zorunlu | `embedded-engineer` |
| R5 | İstemci teknolojisinin belirsizliği | Yüksek | Tasarımın yanlış oturması | `[UNKNOWN]` — Electron kanıtı yok | mimari onay |

> R5: depoda `Electron` bağımlılığı kanıtlanamadı; istemci kararı **onay kapısında** verilmelidir.

## 18. Terminoloji

| Terim | Bu belgedeki anlamı | Karıştırılmaması gereken |
|---|---|---|
| Mesaj kuyruğu | Win32 thread/pencere mesaj kuyruğu | `ipc-mekanizmalari` §4 POSIX Message Queues |
| Komuta kuyruğu | UI → ses lock-free kuyruk | Named Pipe (süreçler arası) |
| Olay döngüsü | `GetMessage` döngüsü | ASIO `bufferSwitch` callback döngüsü |
| `WindowProc` | Pencere prosedürü | Ses motoru callback'i |
| Event handle | `IAudioClient`/`SetEventHandle` olayı | Win32 `CreateEvent` ile oluşturulan genel olay |

## 19. Kapsam Dışı

| Konu | İşaret |
|---|---|
| DAC/ADC, amplifikatör, termal donanım (K1) | `[KAPSAM DIŞI]` |
| MySQL/önbellek/veri kalıcılığı (K5) | `[KAPSAM DIŞI]` |
| Uygulama katmanı API'si (K8–K11) | `[KAPSAM DIŞI]` |
| CI/CD ve izleme altyapısı kurulumu (K13) | `[KAPSAM DIŞI]` |

## ÇELİŞKİ / DOĞRULAMA

1. **Katman adlandırma çelişkisi.** Gömülü `k0-isletim-sistemi/index.md` (§ Bağımlılıklar)
   üst katmanları "K1 Ses Motoru · K2 Ağ Katmanı · K3 Uygulama Katmanı" olarak sayar; kanonik
   `_backup/.../katman-baglilik-matrisi.md` §1.1 ve §2.2 ise K1 = Donanım, K2 = Sürücü,
   K3 = Ses Motoru der. **Çözüm:** kanonik matris kazanır; eski liste kullanılmaz.
2. **"Message Queue" terminoloji çelişkisi.** Gömülü `k10-uygulama/notification-panel.md:235`
   "Message Queue" derken uygulama içi kuyruğu kasteder; bu belge "mesaj kuyruğu" ile
   **Win32 thread mesaj kuyruğunu** kasteder. İkisi aynı şey değildir — ayrım §4.1'de zorunlu.

> Toplam çelişki: **2** (dosya sınırı: en fazla 5).

## Kaynaklar

Bu belge yazılırken **gerçekten okunan** dosya yolları:

1. `C:\www\coremusic.net\.ai\architecture\k000-windows-core\index.md`
2. `C:\www\coremusic.net\.ai\architecture\k000-windows-core\windows-api-yuzeyi.md`
3. `C:\www\coremusic.net\.ai\architecture\k000-windows-core\windows-core-mimari.md`
4. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-core.md`
5. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-api.md`
6. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\ipc-mekanizmalari.md`
7. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\system-calls.md`
8. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\README.md`
9. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\index.md`
10. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\CLAUDE.md`
11. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\katman-baglilik-matrisi.md`

**Doğrulama durumu:** Yeni sürüm/sürüm-sürümüne/gecikme rakamı üretilmemiştir. Uygulama kodu
(C++/JS) bu klasörde yoktur (`*.cpp`/`*.h` sayısı 0 → `⚠️ VERIFICATION REQUIRED`); tüm kod
blokları **kavramsal iskelettir** ve üzerinde etiket taşır.
