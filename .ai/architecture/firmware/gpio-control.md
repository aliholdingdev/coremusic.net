---
title: "GPIO Control Firmware"
layer: Firmware
category: "Firmware"
date: 2026-09-20
---

# GPIO Control Firmware

## Genel Bakış

GPIO control firmware, COREMUSIC'ın fiziksel arayüz bileşenlerini yönetir. Buton handling (debounce, long-press), LED animasyonları, encoder desteği ve display iletişimi bu katmanın sorumlulukları arasındadır. XMOS XU316 ve STM32 üzerindeki GPIO'lar için optimize edilmiş interrupt-driven ve polling-based kontrol mekanizmaları sunar.

## Firmware Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                  GPIO CONTROL FIRMWARE                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              Button Handler                          │      │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐  │      │
│  │  │  Debounce    │  │  Long Press  │  │  Combo   │  │      │
│  │  │  Filter      │  │  Detection   │  │  Keys    │  │      │
│  │  └──────────────┘  └──────────────┘  └──────────┘  │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              LED Controller                          │      │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐  │      │
│  │  │  PWM         │  │  Animation   │  │  Color   │  │      │
│  │  │  Driver      │  │  Engine      │  │  Mixing  │  │      │
│  │  └──────────────┘  └──────────────┘  └──────────┘  │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              Rotary Encoder                          │      │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐  │      │
│  │  │  Quadrature  │  │  Velocity    │  │  Push    │  │      │
│  │  │  Decoder     │  │  Detection   │  │  Button  │  │      │
│  │  └──────────────┘  └──────────────┘  └──────────┘  │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              Display Driver                          │      │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐  │      │
│  │  │  OLED SSD    │  │  Font        │  │  Frame   │  │      │
│  │  │  1306 Driver │  │  Renderer    │  │  Buffer  │  │      │
│  │  └──────────────┘  └──────────────┘  └──────────┘  │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              Input Event Dispatcher                  │      │
│  │  Button → Event Queue → State Machine → Actions     │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Kaynak Kod Yapısı

```
gpio_control/
├── src/
│   ├── gpio_core.xc               # Ana GPIO yöneticisi
│   ├── button_handler.xc          # Buton debounce & handling
│   ├── led_controller.xc          # LED PWM & animasyon
│   ├── rotary_encoder.xc          # Rotary encoder decoder
│   ├── display_driver.xc          # OLED display driver
│   ├── i2c_display.xc            # I2C display iletişimi
│   ├── gpio_interrupt.xc          # Interrupt handler
│   ├── event_queue.xc             # Event queue yönetimi
│   └── gpio_main.xc               # Ana program
│
├── include/
│   ├── gpio_config.h              # Pin konfigürasyonu
│   ├── gpio_types.h               # Veri tipleri
│   ├── button_config.h            # Buton parametreleri
│   ├── led_config.h               # LED renk/harita
│   └── display_config.h           # Display ayarları
│
├── fonts/
│   ├── font_8x16.h                # 8x16 ASCII font
│   └── font_6x8.h                 # 6x8 ASCII font
│
└── Makefile
```

## Teknik Detaylar

### GPIO Pin Haritası (XMOS XU316)

```
┌─────────────────────────────────────────────────────────────┐
│ Pin Assignment                                              │
├──────────┬──────────┬──────────┬────────────────────────────┤
│ Port     │ Pin      │ Direction│ Function                   │
├──────────┼──────────┼──────────┼────────────────────────────┤
│ XS1_1A   │ PA0      │ Input    │ Button: Play/Pause         │
│ XS1_1B   │ PA1      │ Input    │ Button: Volume Up          │
│ XS1_1C   │ PA2      │ Input    │ Button: Volume Down        │
│ XS1_1D   │ PA3      │ Input    │ Button: Next Track         │
│ XS1_1E   │ PA4      │ Input    │ Button: Previous Track     │
│ XS1_1F   │ PA5      │ Input    │ Rotary Encoder A           │
│ XS1_1G   │ PA6      │ Input    │ Rotary Encoder B           │
│ XS1_1H   │ PA7      │ Input    │ Rotary Encoder Push        │
│ XS1_2A   │ PB0      │ Output   │ LED: Status (Green)        │
│ XS1_2B   │ PB1      │ Output   │ LED: Status (Red)          │
│ XS1_2C   │ PB2      │ Output   │ LED: Status (Blue)         │
│ XS1_2D   │ PB3      │ Output   │ LED: Volume Indicator      │
│ XS1_2E   │ PB4      │ Output   │ I2C: SDA (Display)         │
│ XS1_2F   │ PB5      │ Output   │ I2C: SCL (Display)         │
│ XS1_2G   │ PB6      │ Output   │ SPI: CS (Optional)         │
│ XS1_2H   │ PB7      │ Output   │ SPI: MOSI (Optional)       │
└──────────┴──────────┴──────────┴────────────────────────────┘
```

### Button Handler

```xc
// Button debounce ve handling
// 5ms debounce süresi, long-press detection

#define DEBOUNCE_MS         5
#define LONG_PRESS_MS       1000
#define DOUBLE_PRESS_MS     300
#define DEBOUNCE_TICKS      (DEBOUNCE_MS * 100)  // 500μs ticks

// Button state yapısı
typedef struct {
    port p_button;              // Button port
    int current_state;          // Current debounced state
    int last_raw_state;         // Last raw reading
    int debounce_counter;       // Debounce counter
    int press_counter;          // Press duration counter
    int release_counter;        // Release duration counter
    int is_pressed;             // Button is pressed flag
    int long_press_detected;    // Long press flag
} button_state_t;

// Button event tipleri
typedef enum {
    BTN_EVENT_NONE,
    BTN_EVENT_PRESS,
    BTN_EVENT_RELEASE,
    BTN_EVENT_LONG_PRESS,
    BTN_EVENT_DOUBLE_PRESS,
    BTN_EVENT_TRIPLE_PRESS
} button_event_t;

// Button handler fonksiyonu
button_event_t button_handler(button_state_t *btn) {
    int raw_state;
    btn->p_button :> raw_state;

    // Debounce processing
    if (raw_state != btn->last_raw_state) {
        btn->debounce_counter = DEBOUNCE_TICKS;
    } else if (btn->debounce_counter > 0) {
        btn->debounce_counter--;
    }

    // Debounce completed
    if (btn->debounce_counter == 0 && raw_state != btn->current_state) {
        btn->current_state = raw_state;

        if (btn->current_state == 0) {  // Button pressed (active low)
            btn->is_pressed = 1;
            btn->press_counter = 0;
            return BTN_EVENT_PRESS;
        } else {  // Button released
            btn->is_pressed = 0;

            // Long press detection
            if (btn->press_counter >= LONG_PRESS_MS * 2) {
                btn->long_press_detected = 1;
                return BTN_EVENT_LONG_PRESS;
            }

            // Normal press
            return BTN_EVENT_RELEASE;
        }
    }

    // Press duration tracking
    if (btn->is_pressed) {
        btn->press_counter++;
        if (btn->press_counter >= LONG_PRESS_MS * 2) {
            if (!btn->long_press_detected) {
                btn->long_press_detected = 1;
                return BTN_EVENT_LONG_PRESS;
            }
        }
    }

    return BTN_EVENT_NONE;
}

// Multi-button combo detection
typedef struct {
    button_state_t buttons[5];  // 5 buttons
    int combo_mask;              // Active buttons bitmask
    int combo_hold_time;         // Hold time counter
} combo_detector_t;

button_event_t combo_detect(combo_detector_t *cd) {
    int current_mask = 0;

    for (int i = 0; i < 5; i++) {
        if (cd->buttons[i].is_pressed) {
            current_mask |= (1 << i);
        }
    }

    if (current_mask != cd->combo_mask) {
        cd->combo_mask = current_mask;
        cd->combo_hold_time = 0;
    } else if (cd->combo_mask != 0) {
        cd->combo_hold_time++;
    }

    // Combo hold detection (500ms)
    if (cd->combo_hold_time >= 1000) {
        cd->combo_hold_time = 0;
        return BTN_EVENT_LONG_PRESS;
    }

    return BTN_EVENT_NONE;
}
```

### LED Controller

```xc
// LED PWM ve animasyon controller
// RGB LED support, smooth fading, animations

#define PWM_RESOLUTION    256    // 8-bit PWM
#define PWM_FREQUENCY     1000   // 1kHz PWM
#define PWM_PERIOD        (500000 / PWM_FREQUENCY)  // 500 cycles @ 1MHz

// LED state yapısı
typedef struct {
    port p_red;             // Red PWM output
    port p_green;           // Green PWM output
    port p_blue;            // Blue PWM output
    uint8_t current_r;      // Current red value
    uint8_t current_g;      // Current green value
    uint8_t current_b;      // Current blue value
    uint8_t target_r;       // Target red value
    uint8_t target_g;       // Target green value
    uint8_t target_b;       // Target blue value
    int fade_speed;         // Fade speed (0 = instant)
    int pwm_counter;        // PWM counter
} led_state_t;

// PWM generation fonksiyonu
void led_pwm_generate(led_state_t *led) {
    led->pwm_counter++;
    if (led->pwm_counter >= PWM_PERIOD) {
        led->pwm_counter = 0;
    }

    // Red LED
    if (led->pwm_counter < led->current_r) {
        led->p_red <: 1;
    } else {
        led->p_red <: 0;
    }

    // Green LED
    if (led->pwm_counter < led->current_g) {
        led->p_green <: 1;
    } else {
        led->p_green <: 0;
    }

    // Blue LED
    if (led->pwm_counter < led->current_b) {
        led->p_blue <: 1;
    } else {
        led->p_blue <: 0;
    }
}

// Smooth fade fonksiyonu
void led_fade_update(led_state_t *led) {
    if (led->fade_speed == 0) {
        // Instant change
        led->current_r = led->target_r;
        led->current_g = led->target_g;
        led->current_b = led->target_b;
        return;
    }

    // Red fade
    if (led->current_r < led->target_r) {
        led->current_r += led->fade_speed;
        if (led->current_r > led->target_r) {
            led->current_r = led->target_r;
        }
    } else if (led->current_r > led->target_r) {
        led->current_r -= led->fade_speed;
        if (led->current_r < led->target_r) {
            led->current_r = led->target_r;
        }
    }

    // Green fade (aynı mantık)
    // Blue fade (aynı mantık)
}

// LED animasyon patterns
typedef enum {
    LED_ANIM_OFF,
    LED_ANIM_SOLID,
    LED_ANIM_BLINK,
    LED_ANIM_PULSE,
    LED_ANIM_RAINBOW,
    LED_ANIM_BREATHING
} led_animation_t;

// Animasyon state
typedef struct {
    led_animation_t current_anim;
    int anim_counter;
    int anim_phase;
    uint8_t anim_color_r;
    uint8_t anim_color_g;
    uint8_t anim_color_b;
} led_anim_state_t;

// Animasyon update
void led_animation_update(led_state_t *led, led_anim_state_t *anim) {
    anim->anim_counter++;

    switch (anim->current_anim) {
        case LED_ANIM_OFF:
            led->target_r = 0;
            led->target_g = 0;
            led->target_b = 0;
            break;

        case LED_ANIM_SOLID:
            led->target_r = anim->anim_color_r;
            led->target_g = anim->anim_color_g;
            led->target_b = anim->anim_color_b;
            break;

        case LED_ANIM_BLINK:
            if (anim->anim_counter % 500 < 250) {
                led->target_r = anim->anim_color_r;
                led->target_g = anim->anim_color_g;
                led->target_b = anim->anim_color_b;
            } else {
                led->target_r = 0;
                led->target_g = 0;
                led->target_b = 0;
            }
            break;

        case LED_ANIM_PULSE:
            // Sine wave pulse
            anim->anim_phase += 1;
            if (anim->anim_phase >= 360) {
                anim->anim_phase = 0;
            }
            float intensity = (sinf(anim->anim_phase * M_PI / 180.0f) + 1.0f) / 2.0f;
            led->target_r = (uint8_t)(anim->anim_color_r * intensity);
            led->target_g = (uint8_t)(anim->anim_color_g * intensity);
            led->target_b = (uint8_t)(anim->anim_color_b * intensity);
            break;

        case LED_ANIM_RAINBOW:
            anim->anim_phase += 3;
            if (anim->anim_phase >= 360) {
                anim->anim_phase = 0;
            }
            // HSV to RGB conversion
            hsv_to_rgb(anim->anim_phase, 1.0f, 1.0f,
                       &led->target_r, &led->target_g, &led->target_b);
            break;

        case LED_ANIM_BREATHING:
            anim->anim_phase += 1;
            if (anim->anim_phase >= 360) {
                anim->anim_phase = 0;
            }
            float breath = (sinf(anim->anim_phase * M_PI / 180.0f) + 1.0f) / 2.0f;
            led->target_r = (uint8_t)(anim->anim_color_r * breath);
            led->target_g = (uint8_t)(anim->anim_color_g * breath);
            led->target_b = (uint8_t)(anim->anim_color_b * breath);
            break;
    }
}
```

### Rotary Encoder

```xc
// Quadrature rotary encoder decoder
// 24 pulses per revolution, push button

#define ENCODER_PPR    24    // Pulses per revolution

typedef struct {
    port p_a;               // Channel A
    port p_b;               // Channel B
    port p_push;            // Push button
    int position;           // Current position
    int velocity;           // Rotation velocity
    int last_a;             // Last A state
    int last_b;             // Last B state
    int push_state;         // Push button state
    int push_debounce;      // Push debounce
} encoder_state_t;

// Encoder state machine
typedef enum {
    ENCODER_EVENT_NONE,
    ENCODER_EVENT_CW,       // Clockwise rotation
    ENCODER_EVENT_CCW,      // Counter-clockwise rotation
    ENCODER_EVENT_PUSH,
    ENCODER_EVENT_RELEASE
} encoder_event_t;

encoder_event_t encoder_handler(encoder_state_t *enc) {
    int a, b;
    enc->p_a :> a;
    enc->p_b :> b;

    // Quadrature decoding
    if (a != enc->last_a) {
        if (a == b) {
            enc->position++;
            enc->velocity++;
            return ENCODER_EVENT_CW;
        } else {
            enc->position--;
            enc->velocity--;
            return ENCODER_EVENT_CCW;
        }
    }

    enc->last_a = a;
    enc->last_b = b;

    // Push button handling
    int push;
    enc->p_push :> push;
    if (push != enc->push_state) {
        enc->push_debounce--;
        if (enc->push_debounce == 0) {
            enc->push_state = push;
            enc->push_debounce = 50;  // 5ms debounce

            if (push == 0) {  // Pushed (active low)
                return ENCODER_EVENT_PUSH;
            } else {
                return ENCODER_EVENT_RELEASE;
            }
        }
    } else {
        enc->push_debounce = 50;
    }

    return ENCODER_EVENT_NONE;
}

// Velocity detection (acceleration)
int encoder_get_velocity(encoder_state_t *enc) {
    // Velocity decay
    if (enc->velocity > 0) {
        enc->velocity--;
    } else if (enc->velocity < 0) {
        enc->velocity++;
    }

    // Velocity thresholds
    if (enc->velocity > 10) {
        return 3;  // Fast rotation
    } else if (enc->velocity > 5) {
        return 2;  // Medium rotation
    } else if (enc->velocity > 0) {
        return 1;  // Slow rotation
    }

    return 0;  // Stopped
}
```

### Display Driver (OLED SSD1306)

```xc
// SSD1306 OLED display driver
// 128x64 pixels, I2C interface

#define DISPLAY_WIDTH   128
#define DISPLAY_HEIGHT  64
#define DISPLAY_PAGES   8    // 64/8 = 8 pages

#define SSD1306_ADDR    0x3C  // I2C address (0x3D if SA0 high)

// Display buffer
uint8_t display_buffer[DISPLAY_WIDTH * DISPLAY_PAGES];

// I2C communication
void ssd1306_write_command(uint8_t command) {
    uint8_t data[2] = {0x00, command};  // Co=0, D/C#=0
    i2c_write(SSD1306_ADDR, data, 2);
}

void ssd1306_write_data(uint8_t data) {
    uint8_t buf[2] = {0x40, data};  // Co=0, D/C#=1
    i2c_write(SSD1306_ADDR, buf, 2);
}

// Display initialization
void ssd1306_init() {
    // Display off
    ssd1306_write_command(0xAE);

    // Set memory addressing mode (horizontal)
    ssd1306_write_command(0x20);
    ssd1306_write_command(0x00);

    // Set column address range
    ssd1306_write_command(0x21);
    ssd1306_write_command(0x00);
    ssd1306_write_command(0x7F);

    // Set page address range
    ssd1306_write_command(0x22);
    ssd1306_write_command(0x00);
    ssd1306_write_command(0x07);

    // Set COM scan direction (remapped)
    ssd1306_write_command(0xC8);

    // Set segment remap
    ssd1306_write_command(0xA1);

    // Set contrast
    ssd1306_write_command(0x81);
    ssd1306_write_command(0xCF);

    // Display on
    ssd1306_write_command(0xAF);
}

// Pixel drawing
void display_set_pixel(int x, int y, int color) {
    if (x < 0 || x >= DISPLAY_WIDTH || y < 0 || y >= DISPLAY_HEIGHT) {
        return;
    }

    int page = y / 8;
    int bit = y % 8;

    if (color) {
        display_buffer[page * DISPLAY_WIDTH + x] |= (1 << bit);
    } else {
        display_buffer[page * DISPLAY_WIDTH + x] &= ~(1 << bit);
    }
}

// Character drawing (8x16 font)
void display_draw_char(int x, int y, char c, int color) {
    int char_index = c - 32;  // ASCII offset
    if (char_index < 0 || char_index > 95) return;

    for (int row = 0; row < 16; row++) {
        uint8_t font_data = font_8x16[char_index * 16 + row];
        for (int col = 0; col < 8; col++) {
            if (font_data & (0x80 >> col)) {
                display_set_pixel(x + col, y + row, color);
            }
        }
    }
}

// String drawing
void display_draw_string(int x, int y, const char *str, int color) {
    while (*str) {
        display_draw_char(x, y, *str, color);
        x += 8;
        str++;
    }
}

// Screen update
void display_update() {
    // Send buffer to display
    for (int page = 0; page < DISPLAY_PAGES; page++) {
        ssd1306_write_command(0xB0 + page);  // Set page address
        ssd1306_write_command(0x00);         // Set lower column
        ssd1306_write_command(0x10);         // Set higher column

        for (int col = 0; col < DISPLAY_WIDTH; col++) {
            ssd1306_write_data(display_buffer[page * DISPLAY_WIDTH + col]);
        }
    }
}
```

### Event Queue

```xc
// Event queue - GPIO event'lerini yönetme
#define EVENT_QUEUE_SIZE 32

typedef enum {
    EVT_BUTTON_PRESS,
    EVT_BUTTON_RELEASE,
    EVT_BUTTON_LONG_PRESS,
    EVT_ENCODER_CW,
    EVT_ENCODER_CCW,
    EVT_ENCODER_PUSH,
    EVT_VOLUME_CHANGE,
    EVT_TRACK_CHANGE,
    EVT_DISPLAY_UPDATE
} event_type_t;

typedef struct {
    event_type_t type;
    int data;           // Event-specific data
    unsigned timestamp; // Event timestamp
} event_t;

typedef struct {
    event_t events[EVENT_QUEUE_SIZE];
    int head;
    int tail;
    int count;
} event_queue_t;

// Event ekleme
int event_queue_push(event_queue_t *queue, event_type_t type, int data) {
    if (queue->count >= EVENT_QUEUE_SIZE) {
        return -1;  // Queue dolu
    }

    timer t;
    unsigned time;
    t :> time;

    queue->events[queue->tail].type = type;
    queue->events[queue->tail].data = data;
    queue->events[queue->tail].timestamp = time;
    queue->tail = (queue->tail + 1) % EVENT_QUEUE_SIZE;
    queue->count++;

    return 0;
}

// Event okuma
int event_queue_pop(event_queue_t *queue, event_t *event) {
    if (queue->count == 0) {
        return -1;  // Queue boş
    }

    *event = queue->events[queue->head];
    queue->head = (queue->head + 1) % EVENT_QUEUE_SIZE;
    queue->count--;

    return 0;
}
```

### GPIO Interrupt Handler

```xc
// Interrupt-driven GPIO handling
// Buton ve encoder için interrupt

// Port interrupt konfigürasyonu
void gpio_interrupt_setup() {
    // Enable port interrupts
    set_port_interrupt(p_button_play, INT_RISING | INT_FALLING);
    set_port_interrupt(p_button_vol_up, INT_RISING | INT_FALLING);
    set_port_interrupt(p_button_vol_down, INT_RISING | INT_FALLING);

    // Enable encoder interrupts
    set_port_interrupt(p_encoder_a, INT_CHANGING);
    set_port_interrupt(p_encoder_b, INT_CHANGING);

    // Enable global interrupts
    enable_interrupts();
}

// Interrupt handler
void gpio_isr() {
    // Button interrupt
    if (is_port_interrupt_pending(p_button_play)) {
        clear_port_interrupt(p_button_play);
        event_queue_push(&event_queue, EVT_BUTTON_PRESS, 0);
    }

    // Encoder interrupt
    if (is_port_interrupt_pending(p_encoder_a)) {
        clear_port_interrupt(p_encoder_a);
        int a, b;
        p_encoder_a :> a;
        p_encoder_b :> b;

        if (a == b) {
            event_queue_push(&event_queue, EVT_ENCODER_CW, 0);
        } else {
            event_queue_push(&event_queue, EVT_ENCODER_CCW, 0);
        }
    }
}
```

## Derleme & Yükleme

### GPIO Firmware Derleme

```bash
cd firmware/xmos/app_usb_audio_skc

# GPIO control ile derleme
xmake clean
xmake all CONFIG=gpio_enabled

# Display desteği
xmake all CONFIG=gpio_enabled DISPLAY=1

# Interrupt mode
xmake all CONFIG=gpio_enabled INTERRUPT_MODE=1
```

### GPIO Test

```bash
# Buton testi
# Her butona basıldığında LED yanıp sönme
# Long-press ile mod değişikliği

# Encoder testi
# Rotasyon ile volume ayarlama
# Push ile mute toggle

# Display testi
# OLED'de "COREMUSIC" yazısı gösterme
# Volume bar animation
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|-----------|----------|------|
| lib_i2c_master | >= 2.x | Display I2C communication |
| lib_locks | >= 1.x | Event queue synchronization |
| lib_logging | >= 1.x | Debug logging |

## Durum: Implementasyon

| Modül | Durum | Açıklama |
|-------|-------|----------|
| Button Handler | Planlandı | Debounce & long-press |
| LED Controller | Planlandı | PWM & animations |
| Rotary Encoder | Planlandı | Quadrature decoder |
| Display Driver | Planlandı | SSD1306 OLED |
| Event Queue | Planlandı | Event management |
| GPIO Interrupt | Planlandı | Interrupt handling |
