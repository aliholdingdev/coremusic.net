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

#ifdef __cplusplus
}
#endif
#endif /* NEVA_SESSION_H */
