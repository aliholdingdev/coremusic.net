---
node_id: K015-MEDIA-PLAYBACK-ORNEKLER
node_type: kod
status: active
parent_id: K015-MEDIA
children: []
depends_on: [K015-MEDIA-PLAYBACK-KAPSAM, K003-AUDIO]
evidence: [K015-media/playback/playback-kapsam.md, projects/NevaEngine/03-isletim-sistemi-kod-ornekleri.md]
validation: partial
open_questions: 3
group: kod
---

# Playback — Kod Örnekleri (K015-media/children/playback)

> **Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
>
> Bu dosya `playback` düğümü için **tasarım iskeletleri** içerir (C++20,
> Neva üslubu). Format referansı:
> `.ai/architecture/K009-security/threat-model/threat-model-ornekler.md`.
>
> **Her örnek TASARIM İDDİASI'dır — üretim kodu değildir.**
> Üretim koduna geçiş §6/1 (sahiplik) ve §6/2 (codec) açık soruları
> **çözüldükten** sonra yapılır.

## §1 — Kayıt Formatı

Her kayıt şu tabloyu taşır:

| Alan | İçerik |
|---|---|
| Amaç | Örneğin neyi gösterdiği |
| İlgili kapsar madde | `playback-kapsam.md` §x/x |
| Dil / üslup | C++20 · Neva (RAII, `std::unique_ptr`, `noexcept`, sabit kapasite) |
| Üretim mü? | Hayır — TASARIM İDDİASI |

## §2 — Örnekler

### Örnek 1 — Durum Makinesi (kapsam §3 — S-1..S-7)

```cpp
// Amaç: kapsam §3'teki 7 durumu tip güvenli, tek yönlü geçişlerle temsil etmek.
// Üretim mü? HAYIR — TASARIM İDDİASI (tasarım iskeleti).
#pragma once
#include <cstdint>
#include <string_view>

namespace k015::playback {

enum class State : std::uint8_t {
    Idle = 0,     // S-1
    Loading,      // S-2
    Playing,      // S-3
    Paused,       // S-4
    Buffering,    // S-5 — hata DEĞİL geçici durum
    Error,        // S-6
    Ended,        // S-7
};

// Geçiş tablosu: kapsam §3 ile birebir. Olmayan geçiş = geçersiz.
struct TransitionResult {
    bool ok{false};
    std::string_view reason{};   // "geçersiz geçiş: Playing -> Loading" gibi
};

[[nodiscard]] constexpr bool is_valid_transition(State from, State to) noexcept
{
    switch (from) {
    case State::Idle:     return to == State::Loading;
    case State::Loading:  return to == State::Playing || to == State::Error;
    case State::Playing:  return to == State::Paused || to == State::Buffering
                                  || to == State::Ended || to == State::Error;
    case State::Paused:   return to == State::Playing || to == State::Ended
                                  || to == State::Error;
    case State::Buffering:return to == State::Playing || to == State::Error;
    case State::Ended:    return to == State::Idle;       // yeniden başlatma
    case State::Error:    return to == State::Idle;       // kurtarma
    }
    return false;
}

class PlaybackStateMachine {
public:
    [[nodiscard]] State state() const noexcept { return state_; }

    TransitionResult transition(State to) noexcept
    {
        if (!is_valid_transition(state_, to))
            return {false, "geçersiz geçiş"};
        state_ = to;
        return {true, {}};
    }

private:
    State state_{State::Idle};
};

} // namespace k015::playback
```

**Not:** `Buffering` → `Playing` geri dönüşü ve `Buffering`'in hata
olmaması, kapsam §5/H-4 ile aynıdır; `Error` yalnız geri döndürülemez
hatalarda kullanılır.

---

### Örnek 2 — Ring-Buffer Arayüz İskeleti (kapsam §5 — tampon)

```cpp
// Amaç: sabit kapasiteli, kilit gerektirmeyen tek-yazar/tek-okuyucu
//       ses tamponunun ARAYÜZ iskeletini göstermek.
// Üretim mü? HAYIR — TASARIM İDDİASI.
// DİKKAT: sıfır tahsis / lock-free İDDİASI BURADA YAPILMAZ (kural: abartı yok);
//         yalnız arayüz ve kapasite disiplini tanımlanır.
#pragma once
#include <array>
#include <cstddef>
#include <span>

namespace k015::playback {

template <std::size_t Capacity>   // Capacity: power-of-two varsayılır
class RingBuffer {
public:
    static_assert(Capacity > 0 && (Capacity & (Capacity - 1)) == 0,
                  "kapasite 2'nin kuvveti olmalı");

    // Yazan taraf (ses motoru / dekoder): taşma olursa ne kadar yazılabildiğini döndürür.
    [[nodiscard]] std::size_t write(std::span<const float> data) noexcept;

    // Okuyan taraf (çıkış/kuyruk): istenen kadar okur, gerçekten okunanı döndürür.
    [[nodiscard]] std::size_t read(std::span<float> out) noexcept;

    [[nodiscard]] std::size_t size() const noexcept;
    [[nodiscard]] bool empty() const noexcept { return size() == 0; }
    [[nodiscard]] bool full() const noexcept { return size() == Capacity; }

    void clear() noexcept;

private:
    std::array<float, Capacity> buf_{};
    std::size_t head_{0};
    std::size_t tail_{0};
    std::size_t count_{0};
};

} // namespace k015::playback
```

---

### Örnek 3 — K003 Ses Motoru Çağrı İskeleti (kapsam §4 zinciri)

```cpp
// Amaç: media-master §14.1 zincirinin
//   K015-media/playback -> K003-audio (ses motoru) -> hoparlör/çıkış
//   adımını çağıran tip katmanını göstermek.
// Üretim mü? HAYIR — TASARIM İDDİASI.
#pragma once
#include <memory>
#include <string_view>
#include "playback_state_machine.hpp"   // Örnek 1

namespace k015::playback {

// K003-audio'nun SAHİPLİĞİ bu düğümde DEĞİLDİR (kapsam §2): arayüz orada,
// burada yalnızca çağrılan sözleşmenin tanımı vardır.
struct IAudioEngine {                    // K003 sözleşmesi (çeşitleme — doğrulanacak)
    virtual ~IAudioEngine() = default;
    virtual void start() noexcept = 0;   // akışı çalmaya başlat
    virtual void stop() noexcept = 0;    // durdur
    virtual void pause() noexcept = 0;   // duraklat
};

class PlaybackController {
public:
    PlaybackController(std::unique_ptr<IAudioEngine> engine,
                       PlaybackStateMachine& sm) noexcept
        : engine_(std::move(engine)), sm_(sm) {}

    // K011-application'dan gelen komut: dosya konumu çözümü + motor çağrısı.
    void on_play_requested()
    {
        if (auto r = sm_.transition(State::Loading); !r.ok) return;   // S-1 -> S-2
        // TODO (açık soru §6/1): dosya konumu çözümü K015'te mi, K012'de mi?
        if (engine_) engine_->start();
        (void)sm_.transition(State::Playing);                         // S-2 -> S-3
    }

    void on_pause_requested()
    {
        if (auto r = sm_.transition(State::Paused); !r.ok) return;    // S-3 -> S-4
        if (engine_) engine_->pause();
    }

private:
    std::unique_ptr<IAudioEngine> engine_;   // K003 — sahiplik K003'te
    PlaybackStateMachine& sm_;
};

} // namespace k015::playback
```

## §3 — Özet

| # | Örnek | Kapsam karşılığı | Üretim mü? |
|---|---|---|---|
| 1 | Durum makinesi (`State`, geçiş tablosu) | §3 (S-1..S-7) | Hayır — TASARIM İDDİASI |
| 2 | Ring-buffer arayüz iskeleti | §5 (tampon disiplini) | Hayır — TASARIM İDDİASI |
| 3 | K003 ses motoru çağrı iskeleti | §4 zinciri (K015 → K003) | Hayır — TASARIM İDDİASI |

**Üretim öncesi çözülecekler:** §6/1 (sahiplik: K015 vs K012/playback-service) ·
§6/2 (codec seti) · §6/5 (dosya biçimi).

## §4 — Revizyon

| Tarih | Revizyon | Authority |
|---|---|---|
| 2026-10-16 | İlk örnek seti — C++20 Neva üslubu; abartı/sıfır-tahsis iddiası eklenmedi | Bayram Ali / Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
