# Neva Engine — C++20 / MSVC (ADR-0105 §7.1 ilk kod iskeleti)
# Hedefler:
#   neva_engine  (STATIC)  : çekirdek + adapter (decode/EQ/backend)
#   neva_session (SHARED)  : C ABI DLL export (extern "C", §2.6)
#   neva_tests   (exe, optional) : ctest birim testleri (§5)
# 3. parti bağımlılık YOK (libdsd/ffmpeg/soxr/libplacebo sonraki faz — TODO).

cmake_minimum_required(VERSION 3.20)
project(neva_engine LANGUAGES CXX)

set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)
set(CMAKE_CXX_EXTENSIONS OFF)

# --- Çekirdek STATIC lib (tüm kaynak) ---
add_library(neva_engine STATIC
    src/neva_session.cpp
    src/wasapi_backend.cpp
    src/null_backend.cpp
    src/decode_stub.cpp
)
target_include_directories(neva_engine PUBLIC ${CMAKE_CURRENT_SOURCE_DIR}/include)
target_include_directories(neva_engine PRIVATE ${CMAKE_CURRENT_SOURCE_DIR}/src)
# Header'daki export zinciri: NEVA_SESSION_EXPORTS > NEVA_ENGINE_STATIC > dllimport.
# STATIC lib'de tanımlar C2491'e düşmesin diye NEVA_ENGINE_STATIC tanımlanmalı (PUBLIC:
# tüketici hedefler de import görmesin; DLL hedefinde NEVA_SESSION_EXPORTS önceliklidir).
target_compile_definitions(neva_engine PUBLIC NEVA_ENGINE_STATIC)

if(MSVC)
    target_compile_options(neva_engine PRIVATE /W4 /permissive- /Zc:__cplusplus /utf-8)
    # Windows adapter COM/AVRT link (mmdevapi gerekmez: COM vtable + __uuidof ile çözülür)
    target_link_libraries(neva_engine PRIVATE ole32 avrt uuid)
endif()

# --- C ABI SHARED DLL (extern "C" export) ---
add_library(neva_session SHARED src/neva_session.cpp)
target_include_directories(neva_session PUBLIC ${CMAKE_CURRENT_SOURCE_DIR}/include)
target_include_directories(neva_session PRIVATE ${CMAKE_CURRENT_SOURCE_DIR}/src)
target_compile_definitions(neva_session PRIVATE NEVA_SESSION_EXPORTS)
target_link_libraries(neva_session PRIVATE neva_engine)
if(MSVC)
    target_compile_options(neva_session PRIVATE /W4 /permissive- /Zc:__cplusplus /utf-8)
endif()

# --- Test (optional; §5 kapıları) ---
option(NEVA_BUILD_TESTS "Birim testlerini kur" ON)
if(NEVA_BUILD_TESTS)
    enable_testing()
    add_executable(neva_tests tests/test_biquad.cpp)
    target_include_directories(neva_tests PRIVATE
        ${CMAKE_CURRENT_SOURCE_DIR}/include
        ${CMAKE_CURRENT_SOURCE_DIR}/src)
    if(MSVC)
        target_compile_options(neva_tests PRIVATE /W4 /permissive- /Zc:__cplusplus /utf-8)
    endif()
    add_test(NAME biquad_eq_ring COMMAND neva_tests)
endif()
