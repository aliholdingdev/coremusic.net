---
title: "Neva Engine — MATLAB/Octave Kod Örnekleri (6 Örnek)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-19
---

# Neva Engine — MATLAB/Octave Kod Örnekleri

> **Derlenmedi/test edilmedi — üretim kodu doc 01/03 planından geçer.** · v1.0.0 · K1-K6 uyumlu · **Rol:** MATLAB = **offline filtre tasarımı / analiz** — gerçek zamanlı YOKTUR.
> **Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6) · [[06-gereksinimler-ve-farkliliklar]] (R16 · R18) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10)

## §1 Bağlam
MATLAB, Neva Engine'de **offline filtre tasarımı ve ölçüm analizi** dilidir: katsayılar C++ çekirdeğe aktarılır, yanıt grafiği/ölçüm doğrulama yapılır. Örnekler **taban MATLAB** (toolbox'suz) yazılmıştır; toolbox/paket referansları `⚠️` ile işaretlidir. Gerçek zamanlı callback, lock-free kuyruk, cihaz erişimi MATLAB'da **yoktur** (§4 · §7 dürüst sapmalar).
## §2 Örnek 1 — Biquad EQ · (R16 · K2)
```matlab
% neva_biquad.m — [b,a]=peaking() YOK → elle RBJ peaking · normalize a(1) = 1 (K2)
function c = make_peaking(f0, q, gain_db, fs)
q = min(max(q, 0.05), 32); f0 = min(max(f0, 10), 0.49*fs);       % Nyquist koruması
A = 10^(gain_db/40); w0 = 2*pi*(f0/fs); al = sin(w0)/(2*q); a0 = 1 + al/A;
c = struct('b', [(1+al*A), -2*cos(w0), (1-al*A)]/a0, ...
           'a', [1, -2*cos(w0), (1-al/A)]/a0);
% Uygulama: y = filter(c.b, c.a, x);   (taban fonksiyon · R16 çift form: DF1/DF2T aynı koef)
end
```
**Neva bağlamı:** offline katsayı üretimi — `c.b/c.a` C++20 çekirdeğe (doc 02/03) kopyalanır; MATLAB'ın kendisi Neva'nın RT motoru değildir. (Toolbox'taki `iirpeak` HARİCİ araç kutusudur — bu örnek toolbox'suz elle hesap.)

## §3 Örnek 2 — 32f buffer döngüsü · (K2 · K4 · K5)
```matlab
function sr = pick_sample_rate(preferred, device_rates)     % 48k varsayılan (K4)
k4 = [44100 48000 88200 96000 176400 192000];
if ~ismember(preferred, k4), preferred = 48000; end                 % K4
if ismember(preferred, device_rates), sr = preferred; return; end   % K6: SR eşit
k4d = k4(ismember(k4, device_rates));
if isempty(k4d), sr = 48000; else, sr = max(k4d); end               % K5: en yüksek yerel
end
% Vektörel alternatif (madde-başına sapma): L = buf(1:2:end); R = buf(2:2:end);
%   yL = filter(c.b, c.a, L);  → sabit katsayı vektörleşir; envelope/durum blokları VETÖRLEŞEMEZ
```
**Sapma (madde-başına):** interleaved frame döngüsü yerine kanal split + `filter()` gösterilir; IIR durum-geçişi ve doğrusal-olmayan bloklar (§6) for-loop kalır. **Neva bağlamı:** offline blok doğrulama — 48k referans veri C++ çekirdeğine girer.

## §4 Örnek 3 — SPSC ring buffer · (R10)
```matlab
% SAPMA: MATLAB'da gerçek lock-free SPSC YOK (tek-process, offline) → halka simülasyonu
function r = ring_init(cap)                                  % cap = 2^n
r = struct('buf', zeros(1,cap), 'mask', cap-1, 'head', 0, 'tail', 0);
end
function [r, ok] = ring_push(r, v)
nxt = r.head + 1;
if nxt - r.tail > r.mask, ok = false; return; end             % dolu
r.buf(mod(r.head, r.mask+1)+1) = v; r.head = nxt; ok = true;  % 1-bazlı indeks (+1)
end
% ring_pop aynı desen: tail == head → boş; değilse buf(mod(tail, mask+1)+1) oku, tail+1
```
**Dürüst sapma:** atomik/kilitli kuyruk yok — mod-indekli çevrim-dışı simülasyon; gerçek lock-free SPSC C++/Rust çekirdektedir (R10 · doc 03).

## §5 Örnek 4 — WAV 32f I/O · (K3 · K1)
```matlab
function write_wav_f32(path, fs, x, ch)   % elle RIFF · format 3 (IEEE_FLOAT) · K4 SR
assert(ismember(fs, [44100 48000 88200 96000 176400 192000]), 'K4 SR');
n = numel(x); dataBytes = n*4;
fid = fopen(path, 'wb', 'ieee-le');
fwrite(fid, 'RIFF'); fwrite(fid, 36+dataBytes, 'uint32'); fwrite(fid, 'WAVE');
fwrite(fid, 'fmt '); fwrite(fid, 16, 'uint32');
fwrite(fid, 3, 'uint16'); fwrite(fid, ch, 'uint16');          % format 3 (K1/K3)
fwrite(fid, fs, 'uint32'); fwrite(fid, fs*ch*4, 'uint32');
fwrite(fid, ch*4, 'uint16'); fwrite(fid, 32, 'uint16');
fwrite(fid, 'data'); fwrite(fid, dataBytes, 'uint32');
fwrite(fid, single(x), 'float32'); fclose(fid);               % doğrudan 32f (K1→K3)
end
```
**Dürüst sapma:** okuma için taban `audioread` örnekleri **double**'a açar → K2 zinciri `single()` dönüşümü ister (`⚠️ VERIFICATION REQUIRED`: Octave uyumluluğu · `audiowrite` format-3 çıktısı doğrulanmadı → elle yazım gösterildi). **Neva bağlamı:** offline referans WAV (doc 05 PCM doğrulaması · K1/K3).

## §6 Örnek 5 — Compressor+limiter · (R10 · K3)
```matlab
function y = compress(x, fs)   % envelope (5/50 ms) → −12 dB → 4:1 → clamp 0 dBFS (K3)
atk = exp(-1/(0.005*fs)); rel = exp(-1/(0.050*fs));
thr = 10^(-12/20); ratio = 4; makeup = 10^((1-1/ratio)*12*0.5/20);
env = 0; y = zeros(size(x));
for i = 1:numel(x)                                % durum+eşik → vektörLEŞEMEZ
    if abs(x(i)) > env, a = atk; else, a = rel; end
    env = a*env + (1-a)*abs(x(i));
    g = makeup;
    if env > thr, g = g * 10^(-(20*log10(env+1e-12)+12)*(1-1/ratio)/20); end
    y(i) = min(max(x(i)*g, -1), 1);               % brickwall 0 dBFS
end
end
```
**TPDF dither notu (K3):** 24-bit int düşüşünde `d = (rand(size(y))-rand(size(y)))/2^23` (1 LSB @24-bit) + round + clamp; float32 çıktıda (K6) dither yok. **Neva bağlamı:** offline dinamik zarf doğrulama; üretim compressor C++ çekirdekte (R10).

## §7 Örnek 6 — OS/cihaz enum · (R18)
```matlab
% SAPMA: taban MATLAB'da ses cihazı enum/erişimi YOK (audioDeviceReader HARİCİ toolbox)
function sel = pick_default(profilePath)        % R18: yalnız dosyadan okuma
sel = struct('backend','NONE','rate',48000,'from_saved',false);
if ~isfile(profilePath), return; end            % 1) önceki seçim kaydı yok
s = jsondecode(fileread(profilePath));          % taban fonksiyon
if isfield(s,'backend'), sel.backend = s.backend; end
if isfield(s,'rate'), sel.rate = s.rate; sel.from_saved = true; end
end
```
**Dürüst sapma:** 2. adım (K5 en yüksek yerel SR) + OS tespiti cihaz erişimi olmadan yapılamaz → çekirdek katmanında (C++/Rust, R18 · doc 03) kalır. **Neva bağlamı:** offline profil/ayar katmanı (R5 profil sistemiyle entegre).

## §8 Dile özel notlar (MATLAB/Octave)
| Başlık | Not |
|---|---|
| **Vektörleşme** | Kural: her mümkün blok vektörel (`filter`, `buf(1:2:end)`); for-loop yalnız doğrusal-olmayan/envelope adımlarında (madde-başına sapma raporlanır) |
| **RT yok** | MATLAB JIT + tek-process modeli RT callback/lock-free sunmaz — Neva rolü offline tasarım; canlı ses yolu C++ çekirdektir |
| **Toolbox bağımlılığı** | Örnekler **taban**: `filter`, `fwrite`, `jsondecode`, `audioread` toolbox'suz gelir; `iirpeak`/`audioDeviceReader` HARİCİ araç kutusudur (`⚠️ VERIFICATION REQUIRED`) |
| **double varsayılan** | Her sayı `double` → K2 32f için `single()` dönüşümü gerekir; hesap yolu çoğunlukla double kalır (dürüst sapma) |
| **Bayt düzeni · Octave** | `fopen(...,'ieee-le')` açık little-endian (K1/K3); `audioread`/`jsondecode` Octave sürüm farkları `⚠️ VERIFICATION REQUIRED` |

**Kural: clean-room (R1) — standart desenler, tescilli kod kopyalanmaz.** · **Authority:** Master Orchestrator → Embedded Engineer · v1.0.0 (2026-10-19) · derlenmedi/test edilmedi · commit'siz (orkestratöre ait)
