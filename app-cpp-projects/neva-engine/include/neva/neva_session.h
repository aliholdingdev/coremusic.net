/* neva_session.h — TEK PUBLIC C ABI HEADER (ADR-0105 §2.6).
 * Opaque handle + error-code + api_version handshake. ABI'yi kiran her degisiklik major artis ister.
 * C++ tipleri bu sinirdan GECMEZ (25 dil FFI kapisi: Python ctypes / C# P-Invoke). */
#ifndef NEVA_SESSION_H
#define NEVA_SESSION_H

#include <stdint.h>
#if !defined(__cplusplus)
  #include <uchar.h> /* char16_t (C11) */
#endif

#if !defined(_WIN32)
  #define NEVA_API
#elif defined(NEVA_SESSION_EXPORTS)
  #define NEVA_API __declspec(dllexport)
#elif defined(NEVA_ENGINE_STATIC)
  #define NEVA_API /* static lib — export/import yok */
#else
  #define NEVA_API __declspec(dllimport)
#endif

#ifdef __cplusplus
extern "C" {
#endif

#define NEVA_API_VERSION 0x00010000u /* 1.0.0 */

/* Opak handle — C++ tipi siniri gecemez (ADR-0105 §2.6). */
typedef struct neva_session neva_session_t;

typedef enum neva_result {
  NEVA_OK = 0,
  NEVA_ERR_INVALID_ARG = 1,
  NEVA_ERR_NO_DEVICE = 2,
  NEVA_ERR_UNSUPPORTED_FORMAT = 3,
  NEVA_ERR_INTERNAL = 4
} neva_result_t;

NEVA_API uint32_t      neva_session_api_version(void);
NEVA_API neva_result_t neva_session_create(neva_session_t** out);
NEVA_API void          neva_session_destroy(neva_session_t* s);
NEVA_API neva_result_t neva_session_open_file(neva_session_t* s, const char16_t* path);
NEVA_API neva_result_t neva_session_set_output_device(neva_session_t* s, uint32_t index);
NEVA_API neva_result_t neva_session_play(neva_session_t* s);
NEVA_API neva_result_t neva_session_pause(neva_session_t* s);
NEVA_API neva_result_t neva_session_stop(neva_session_t* s);
/* band 0..30 (31 bant). Bu C ABI her iki kanala da uygular (62 biquad yolu: 31x2).
 * Per-kontrol kanal erisimi C++ EqChain::set_gain(band, ch, db, ramp_ms) icindedir. */
NEVA_API neva_result_t neva_session_set_eq_gain(neva_session_t* s, uint32_t band, double db);
/* Aktif bant maskesi yayini (ADR-106): 31-bit maske (0x7FFFFFFF = tum bantlar, 0 = bypass). */
NEVA_API neva_result_t neva_session_set_active_bands(neva_session_t* s, uint32_t mask);

/* Telemetri veri yapisi (ADR-038 DAC / ADR-089 Class AB donanim telemetrisi) */
typedef struct neva_telemetry {
  float    snr_db;              /* SNR: >105 dB (olcum: 108.5 dB) */
  float    thd_n_percent;       /* THD+N: <0.003% (olcum: 0.0024%) */
  float    temp_celsius;        /* Termal: Fischer SK53 sogutucu sicakligi (<72 C cutoff) */
  float    rail_voltage_pos;    /* LM5122 Dual Boost pozitif ray (+35.0V) */
  float    rail_voltage_neg;    /* LM5122 Dual Boost negatif ray (-35.0V) */
  float    bias_current_ma;     /* MJL21194/93 quiescent bias akimi (50 mA/kanal) */
  float    dc_offset_mv;        /* DC offset gerilimi (<500 mV roleye kilit) */
  uint32_t sample_rate;         /* Ornekleme frekansi (96000 Hz) */
  uint32_t buffer_frames;       /* Tampon boyutu (512 frame) */
  float    latency_ms;          /* Gecikme (5.33 ms) */
  uint32_t relay_closed;        /* 1 = Korumada roleye enerji verildi (ses acik), 0 = DC hata */
  uint32_t active_bands_mask;   /* 31-bit ISO EQ aktif bant maskesi */
} neva_telemetry_t;

NEVA_API neva_result_t neva_session_get_telemetry(neva_session_t* s, neva_telemetry_t* out);

#ifdef __cplusplus
}
#endif
#endif /* NEVA_SESSION_H */
