---
title: "Format Decoder"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Format Decoder (FLAC, MP3, AAC, WAV, DSD)

## Genel Bakış

COREMUSIC, çoklu ses formatlarını destekleyen bir decoder modülü içerir. FLAC, MP3, AAC, WAV ve DSD formatlarını decode ederek K3 DSP pipeline'ına ham PCM verisi sağlar.

## Teknik Detaylar

### Format Karşılaştırması

```
┌─────────────────────────────────────────────────────────┐
│              Ses Format Karşılaştırması                 │
├──────────┬──────────┬──────────┬──────────┬────────────┤
│ Format   │ Kayıpsız │ Bit Hızı │ SR       │ Bit Depth  │
├──────────┼──────────┼──────────┼──────────┼────────────┤
│ FLAC     │ Evet     │ 800-3000 │ 8-384k   │ 4-32       │
│ MP3      │ Hayır    │ 32-320k  │ 8-48k    │ 16         │
│ AAC      │ Hayır    │ 32-512k  │ 8-96k    │ 16/24      │
│ WAV      │ Evet     │ Sabit    │ 1-192k   │ 8/16/24/32 │
│ DSD      │ Evet     │ 2.8M+    │ 2.8M+    │ 1-bit      │
└──────────┴──────────┴──────────┴──────────┴────────────┘
```

### FLAC Decoder

```cpp
class FLACDecoder {
public:
    FLACDecoder() {
        decoder = FLAC__stream_decoder_new();
    }
    
    ~FLACDecoder() {
        FLAC__stream_decoder_delete(decoder);
    }
    
    bool open(const std::string& filename) {
        FLAC__StreamDecoderInitStatus status = 
            FLAC__stream_decoder_init_file(
                decoder, 
                filename.c_str(),
                writeCallback,
                metadataCallback,
                errorCallback,
                this
            );
        
        if (status != FLAC__STREAM_DECODER_INIT_STATUS_OK) {
            return false;
        }
        
        FLAC__stream_decoder_process_until_end_of_metadata(decoder);
        return true;
    }
    
    bool readAudio(float* buffer, uint32_t frames) {
        uint32_t bytesRead = 0;
        
        while (bytesRead < frames && 
               FLAC__stream_decoder_get_state(decoder) != 
               FLAC__STREAM_DECODER_END_OF_STREAM) {
            
            FLAC__stream_decoder_process_single(decoder);
            
            // 24-bit PCM'i float'a dönüştür
            for (uint32_t i = 0; i < currentBuffer.size(); i++) {
                buffer[bytesRead + i] = 
                    currentBuffer[i] / 8388608.0f;  // 2^23
            }
            
            bytesRead += currentBuffer.size();
        }
        
        return bytesRead > 0;
    }
    
    uint32_t getSampleRate() const { return sampleRate; }
    uint32_t getChannels() const { return channels; }
    uint32_t getBitsPerSample() const { return bitsPerSample; }
    uint64_t getTotalSamples() const { return totalSamples; }
    
private:
    FLAC__StreamDecoder* decoder;
    std::vector<FLAC__int32> currentBuffer;
    
    uint32_t sampleRate;
    uint32_t channels;
    uint32_t bitsPerSample;
    uint64_t totalSamples;
    
    static FLAC__StreamDecoderWriteStatus writeCallback(
        const FLAC__StreamDecoder* decoder,
        const FLAC__Frame* frame,
        const FLAC__int32* const buffer[],
        void* clientData) {
        
        auto* self = static_cast<FLACDecoder*>(clientData);
        
        self->sampleRate = frame->header.sample_rate;
        self->channels = frame->header.channels;
        self->bitsPerSample = frame->header.bits_per_sample;
        
        uint32_t blocksize = frame->header.blocksize;
        self->currentBuffer.resize(blocksize * self->channels);
        
        // Interleave
        for (uint32_t i = 0; i < blocksize; i++) {
            for (uint32_t ch = 0; ch < self->channels; ch++) {
                self->currentBuffer[i * self->channels + ch] = 
                    buffer[ch][i];
            }
        }
        
        return FLAC__STREAM_DECODER_WRITE_STATUS_CONTINUE;
    }
    
    static void metadataCallback(
        const FLAC__StreamDecoder* decoder,
        const FLAC__StreamMetadata* metadata,
        void* clientData) {
        
        if (metadata->type == FLAC__METADATA_TYPE_STREAMINFO) {
            auto* self = static_cast<FLACDecoder*>(clientData);
            self->totalSamples = 
                metadata->data.stream_info.total_samples;
        }
    }
    
    static void errorCallback(
        const FLAC__StreamDecoder* decoder,
        FLAC__StreamDecoderErrorStatus status,
        void* clientData) {
        // Hata yönetimi
    }
};
```

### MP3 Decoder

```cpp
class MP3Decoder {
public:
    MP3Decoder() {
        mpg123_init();
        handle = mpg123_new(nullptr, &err);
    }
    
    ~MP3Decoder() {
        mpg123_delete(handle);
        mpg123_exit();
    }
    
    bool open(const std::string& filename) {
        if (mpg123_open(handle, filename.c_str()) != MPG123_OK) {
            return false;
        }
        
        mpg123_getformat(handle, &rate, &channels, &encoding);
        return true;
    }
    
    size_t readAudio(float* buffer, size_t frames) {
        size_t done;
        size_t bytesNeeded = frames * channels * sizeof(float);
        
        // MP3 oku ve float'a dönüştür
        uint8_t tempBuffer[4096];
        mpg123_read(handle, tempBuffer, bytesNeeded, &done);
        
        // int16 → float dönüşümü
        int16_t* samples = reinterpret_cast<int16_t*>(tempBuffer);
        for (size_t i = 0; i < done / sizeof(int16_t); i++) {
            buffer[i] = samples[i] / 32768.0f;
        }
        
        return done / sizeof(int16_t) / channels;
    }
    
    long getSampleRate() const { return rate; }
    int getChannels() const { return channels; }
    
private:
    mpg123_handle* handle;
    int err;
    long rate;
    int channels;
    int encoding;
};
```

### AAC Decoder

```cpp
class AACDecoder {
public:
    AACDecoder() {
        NeAACDecInit(&handle);
    }
    
    ~AACDecoder() {
        NeAACDecClose(handle);
    }
    
    bool open(const uint8_t* aacData, size_t dataSize) {
        unsigned long sampleRate;
        unsigned char channels;
        
        NeAACDecInit(handle, aacData, dataSize, 
                     &sampleRate, &channels);
        
        this->sampleRate = sampleRate;
        this->channels = channels;
        return true;
    }
    
    float* decode(const uint8_t* aacData, size_t dataSize, 
                  size_t* decodedSamples) {
        NeAACDecFrameInfo frameInfo;
        void* output = NeAACDecDecode(handle, &frameInfo, 
                                       aacData, dataSize);
        
        *decodedSamples = frameInfo.samples;
        return static_cast<float*>(output);
    }
    
private:
    NeAACDecHandle handle;
    unsigned long sampleRate;
    unsigned char channels;
};
```

### DSD Decoder

```cpp
class DSDDecoder {
public:
    DSDDecoder() {}
    
    bool open(const std::string& filename) {
        // DSD dosyasını oku
        // DSD64 (2.8MHz), DSD128 (5.6MHz), DSD256 (11.2MHz)
        return true;
    }
    
    // DSD → PCM dönüşümü (simple decimation)
    void decodeToPCM(float* output, uint32_t frames) {
        // DSD bitlerini PCM'e dönüştür
        // 8x decimation (DSD64 → PCM 352.8kHz)
        for (uint32_t i = 0; i < frames; i++) {
            float sample = 0.0f;
            
            // 8 DSD bit → 1 PCM sample
            for (int bit = 0; bit < 8; bit++) {
                uint8_t dsdBit = (dsdBuffer[i] >> (7 - bit)) & 1;
                sample += dsdBit ? 1.0f : -1.0f;
                sample *= 0.5f;
            }
            
            output[i] = sample;
        }
    }
    
    uint32_t getDSDSampleRate() const { return dsdSampleRate; }
    uint32_t getPCMOutputRate() const { return pcmSampleRate; }
    
private:
    uint32_t dsdSampleRate = 2822400;  // DSD64
    uint32_t pcmSampleRate = 352800;   // 352.8kHz
    std::vector<uint8_t> dsdBuffer;
};
```

### Format Otomatik Algılama

```cpp
class FormatDetector {
public:
    static AudioFormat detect(const std::string& filename) {
        // Dosya uzantısına bak
        std::string ext = getFileExtension(filename);
        
        if (ext == "flac") return AudioFormat::FLAC;
        if (ext == "mp3") return AudioFormat::MP3;
        if (ext == "aac" || ext == "m4a") return AudioFormat::AAC;
        if (ext == "wav") return AudioFormat::WAV;
        if (ext == "dsf" || ext == "dff") return AudioFormat::DSD;
        
        // Magic number ile algılama
        std::ifstream file(filename, std::ios::binary);
        uint8_t header[12];
        file.read(reinterpret_cast<char*>(header), 12);
        
        if (memcmp(header, "fLaC", 4) == 0) 
            return AudioFormat::FLAC;
        if (memcmp(header, "ID3", 3) == 0 || 
            header[0] == 0xFF && (header[1] & 0xE0) == 0xE0)
            return AudioFormat::MP3;
        if (memcmp(header, "RIFF", 4) == 0) 
            return AudioFormat::WAV;
        
        return AudioFormat::Unknown;
    }
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class FormatDecoderModule {
public:
    FormatDecoderModule();
    
    // Dosya açma
    bool openFile(const std::string& filename);
    void closeFile();
    
    // Okuma
    size_t read(float* buffer, size_t frames);
    
    // Bilgi
    AudioFormat getFormat() const;
    uint32_t getSampleRate() const;
    uint32_t getChannels() const;
    uint32_t getBitsPerSample() const;
    uint64_t getTotalFrames() const;
    uint64_t getCurrentPosition() const;
    
    // Seek
    bool seek(uint64_t frame);
    bool seekToTime(double seconds);
    
    // Durum
    bool isEOF() const;
    bool isOpen() const;
    
private:
    std::unique_ptr<FLACDecoder> flacDecoder;
    std::unique_ptr<MP3Decoder> mp3Decoder;
    std::unique_ptr<AACDecoder> aacDecoder;
    std::unique_ptr<DSDDecoder> dsdDecoder;
    
    AudioFormat currentFormat;
    std::ifstream fileStream;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | FLAC | MP3 | AAC | WAV | DSD |
|--------|------|-----|-----|-----|-----|
| Decode CPU | 2% | 3% | 2.5% | 0.1% | 5% |
| Latency | < 1ms | < 5ms | < 5ms | < 0.1ms | < 2ms |
| Bellek | 2MB | 1MB | 1MB | 0 | 4MB |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| libFLAC | Dış |
| libmpg123 | Dış |
| libfaad | Dış |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: FLAC decoder
- **Faz 2**: MP3, AAC decoders
- **Faz 3**: WAV, DSD decoders
- **Faz 4**: Otomatik format algılama
- **Tahmini Süre**: 2 hafta (80 adam-saat)
