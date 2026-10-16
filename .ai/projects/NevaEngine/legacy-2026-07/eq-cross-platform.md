---
type: technical-spec
project: Neva Engine
component: eq-cross-platform
updated: 2026-06-03
---

# Neva Engine — Cross-Platform EQ Rendering

> Her platform için optimize edilmiş EQ UI render + DSP pipeline.

---

## 1. Platform Matrix

| Platform      | EQ UI Render         | DSP Library       | SIMD     | Plugin Host   |
| ------------- | -------------------- | ----------------- | -------- | ------------- |
| Windows XP    | GDI+                 | JUCE DSP + IIR    | MMX/SSE  | VST3 (32-bit) |
| Windows 7/8.1 | Direct2D 1.0/1.1     | JUCE DSP          | SSE2/AVX | VST3          |
| Windows 10/11 | Direct2D 1.3 + DX12  | JUCE DSP          | AVX2     | VST3/CLAP     |
| macOS         | Metal + CoreGraphics | JUCE + Accelerate | NEON     | AU/VST3/CLAP  |
| Linux         | OpenGL / Cairo       | JUCE DSP          | SSE/AVX  | LV2/VST3/CLAP |
| RPi5 (ARM64)  | OpenGL ES 3.1        | JUCE DSP + NEON   | NEON     | LV2/CLAP      |

---

## 2. EQ Slider Render (Per-Platform)

### Windows (Direct2D)

```cpp
// EQ slider — 31 vertical sliders with gradient fill
void renderEQSlider(ID2D1RenderTarget* rt, float x, float y, float w, float h, float value, ID2D1Brush* fillBrush) {
    // Track
    rt->FillRectangle(D2D1::RectF(x, y, x + w, y + h), trackBrush);

    // Fill (from bottom)
    float fillH = h * (value + 12.0f) / 24.0f; // ±12dB range
    rt->FillRectangle(D2D1::RectF(x, y + h - fillH, x + w, y + h), fillBrush);

    // Thumb
    D2D1_ELLIPSE thumb = {{x + w/2, y + h - fillH}, 6, 6};
    rt->FillEllipse(thumb, thumbBrush);

    // Frequency label
    DrawText(rt, frequencyText, x, y + h + 4, w, 10);
}
```

### Linux (OpenGL/Cairo)

```cpp
void renderEQSliderCairo(cairo_t* cr, float x, float y, float w, float h, float value) {
    // Track
    cairo_set_source_rgba(cr, 1, 1, 1, 0.15);
    cairo_rectangle(cr, x, y, w, h);
    cairo_fill(cr);

    // Fill gradient
    cairo_pattern_t* grad = cairo_pattern_create_linear(x, y + h, x, y);
    cairo_pattern_add_color_stop_rgba(grad, 0.0, 1.0, 0.31, 0.85, 0.8); // #ff4fd8
    cairo_pattern_add_color_stop_rgba(grad, 1.0, 0.91, 0.47, 0.63, 0.5); // #e879a0
    cairo_set_source(cr, grad);

    float fillH = h * (value + 12.0f) / 24.0f;
    cairo_rectangle(cr, x, y + h - fillH, w, fillH);
    cairo_fill(cr);
    cairo_pattern_destroy(grad);
}
```

### RPi5 (OpenGL ES 3.1)

```glsl
// EQ slider vertex shader
// GLES 3.1, optimized for VideoCore VII
#version 310 es
in vec2 position;
in float gain_dB; // -12..12
out float vGain;
uniform mat4 projection;

void main() {
    gl_Position = projection * vec4(position, 0.0, 1.0);
    vGain = gain_dB;
}
```

---

## 3. Spectrum Analyzer Render

```cpp
// Cross-platform spectrum visualization
// 31 bars matching EQ bands

void renderSpectrum(const float* spectrum, int bins) {
    for (int band = 0; band < 31; ++band) {
        // Map FFT bin to EQ band
        int fftBin = freqToBin(EQ_FREQS[band], sampleRate, bins);
        float magnitude = spectrum[fftBin];
        float barHeight = magnitude * maxBarHeight;

        // Color gradient (green→yellow→red)
        Color barColor = magnitudeToColor(magnitude);

        drawBar(band * barWidth, maxBarHeight - barHeight, barWidth - 2, barHeight, barColor);
    }
}

Color magnitudeToColor(float mag) {
    // -60dB = green, -30dB = yellow, 0dB = red
    if (mag < -45) return Color(0.0f, 1.0f, 0.0f);
    if (mag < -15) return Color(1.0f, 1.0f, 0.0f);
    return Color(1.0f, 0.0f, 0.0f);
}
```

---

## 4. Performance Optimization

```cpp
// SIMD optimizasyonu — EQ DSP pipeline
#if defined(__AVX2__)
    // 8 samples per iteration (256-bit)
    __m256 b0_vec = _mm256_set1_ps(b0);
    __m256 b1_vec = _mm256_set1_ps(b1);
    // ... AVX2 vectorized IIR filter
#elif defined(__SSE2__)
    // 4 samples per iteration (128-bit)
    __m128 b0_vec = _mm_set1_ps(b0);
    // ... SSE2 vectorized IIR filter
#elif defined(__ARM_NEON)
    // 4 samples per iteration (128-bit NEON)
    float32x4_t b0_vec = vdupq_n_f32(b0);
    // ... NEON vectorized IIR filter
#else
    // Scalar fallback
#endif
```

---

*Neva Engine Cross-Platform EQ Rendering v1 · 2026-06-03*
