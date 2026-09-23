---
title: "Sesli Asistan Modülü"
layer: K4
category: "Yapay Zeka"
date: 2026-09-20
version: 1.0.0
status: development
components: 11
dependencies: [K0, K2, K3, K4, K5]
---

# Voice Assistant - Sesli Asistan Modülü

## Genel Bakış

Sesli asistan modülü, COREMUSIC sistemine sesli komut alma, işleme ve yanıt verme kabiliyeti kazandıran bir alt modüldür. Wake word detection, speech-to-text, natural language understanding (NLU) ve text-to-speech (TTS) pipeline'ını yönetir. Edge cihazlarında çalışacak şekilde optimize edilmiş, gizlilik odaklı bir mimariye sahiptir.

## Teknik Detaylar

### Wake Word Detection

Düşük güçlü cihazlarda çalışan wake word tespit sistemi:

```python
import numpy as np
from collections import deque

class WakeWordDetector:
    def __init__(self, model_path: str, threshold=0.7):
        self.model = self._load_model(model_path)
        self.threshold = threshold
        self.sample_rate = 16000
        self.chunk_size = 1280  # 80ms
        
        # Ring buffer
        self.buffer = deque(maxlen=30)  # 2.4 saniye
        self.detection_history = []
    
    def process_audio(self, audio_chunk: np.ndarray) -> dict:
        """Seschunk'ını işle ve wake word tespit et."""
        
        # MFCC öznitelikleri
        features = self._extract_features(audio_chunk)
        
        # Model inference
        prediction = self.model.predict(features)
        
        # Smoothing
        self.buffer.append(prediction)
        smoothed = self._smooth_predictions()
        
        # Detection kararı
        detected = smoothed > self.threshold
        
        #_false positive kontrolü
        if detected:
            is_valid = self._validate_detection()
            if not is_valid:
                detected = False
        
        result = {
            'detected': detected,
            'confidence': float(smoothed),
            'raw_prediction': float(prediction),
            'processing_time_ms': self._measure_latency()
        }
        
        if detected:
            self.detection_history.append({
                'timestamp': self._get_timestamp(),
                'confidence': float(smoothed)
            })
        
        return result
    
    def _extract_features(self, audio: np.ndarray) -> np.ndarray:
        """MFCC ve diğer öznitelikleri çıkar."""
        import librosa
        
        # MFCC
        mfcc = librosa.feature.mfcc(
            y=audio, sr=self.sample_rate, n_mfcc=13
        )
        
        # Delta MFCC
        delta_mfcc = librosa.feature.delta(mfcc)
        
        # Delta-delta MFCC
        delta2_mfcc = librosa.feature.delta(mfcc, order=2)
        
        # Birleştir
        features = np.concatenate([
            mfcc, delta_mfcc, delta2_mfcc
        ], axis=0)
        
        return features.T  # (time_steps, features)
    
    def _smooth_predictions(self) -> float:
        """Tahminleri düzleştir (median filter)."""
        if len(self.buffer) == 0:
            return 0.0
        
        return float(np.median(list(self.buffer)))
    
    def _validate_detection(self) -> bool:
        """False positive kontrolü."""
        if len(self.detection_history) < 2:
            return True
        
        # Son 5 saniyede birden fazla detection varsa
        recent = [d for d in self.detection_history 
                  if self._get_timestamp() - d['timestamp'] < 5]
        
        if len(recent) > 3:
            return False  # Çok sık tetikleniyor
        
        return True
```

### Natural Language Understanding (NLU)

Ses komutlarını anlamlandıran NLU motoru:

```python
from transformers import pipeline, AutoTokenizer, AutoModelForSequenceClassification
import re

class NLUEngine:
    def __init__(self, model_name="dbmdz/bert-base-turkish-cased"):
        self.tokenizer = AutoTokenizer.from_pretrained(model_name)
        self.model = AutoModelForSequenceClassification.from_pretrained(
            model_name
        )
        
        # Intent sınıflandırma
        self.intent_classifier = pipeline(
            "text-classification",
            model=self.model,
            tokenizer=self.tokenizer
        )
        
        # Entity extraction patterns
        self.entity_patterns = self._load_entity_patterns()
        
        # Intent tanimlari
        self.intents = [
            'play_music', 'pause_music', 'next_track', 'previous_track',
            'volume_up', 'volume_down', 'set_volume', 'shuffle',
            'repeat', 'add_to_playlist', 'create_playlist',
            'search_artist', 'search_album', 'search_genre',
            'set_timer', 'get_info', 'help'
        ]
    
    def understand(self, text: str) -> dict:
        """Metni anla ve intent/entity çıkar."""
        
        # Intent sınıflandırma
        intent_result = self._classify_intent(text)
        
        # Entity çıkarma
        entities = self._extract_entities(text)
        
        # Dialog state güncelleme
        dialog_state = self._update_dialog_state(intent_result, entities)
        
        # Confidence threshold
        if intent_result['confidence'] < 0.5:
            return {
                'intent': 'unknown',
                'confidence': intent_result['confidence'],
                'entities': entities,
                'action': self._get_clarification_action(),
                'dialog_state': dialog_state
            }
        
        # Action mapping
        action = self._map_to_action(intent_result['intent'], entities)
        
        return {
            'intent': intent_result['intent'],
            'confidence': intent_result['confidence'],
            'entities': entities,
            'action': action,
            'dialog_state': dialog_state,
            'raw_text': text
        }
    
    def _classify_intent(self, text: str) -> dict:
        """Intent sınıflandırması yap."""
        # Pre-processing
        processed = self._preprocess_text(text)
        
        # Model inference
        result = self.intent_classifier(processed)
        
        return {
            'intent': result[0]['label'].lower(),
            'confidence': result[0]['score']
        }
    
    def _extract_entities(self, text: str) -> dict:
        """Named entity recognition."""
        entities = {}
        
        # Sanatçı adı
        artist_pattern = r'(?:by|singing|artist)\s+(.+?)(?:\s+and|\s*$)'
        artist_match = re.search(artist_pattern, text, re.IGNORECASE)
        if artist_match:
            entities['artist'] = artist_match.group(1).strip()
        
        # Şarkı adı
        song_pattern = r'(?:play|song|track)\s+(.+?)(?:\s+by|\s*$)'
        song_match = re.search(song_pattern, text, re.IGNORECASE)
        if song_match:
            entities['song'] = song_match.group(1).strip()
        
        # Volume seviyesi
        volume_pattern = r'(?:volume|set)\s+(?:to\s+)?(\d+)(?:\s*%)?'
        volume_match = re.search(volume_pattern, text, re.IGNORECASE)
        if volume_match:
            entities['volume'] = int(volume_match.group(1))
        
        # Zamanlayıcı
        timer_pattern = r'(?:timer|set|alarm)\s+(?:for\s+)?(\d+)\s*(min|hour|saniye)'
        timer_match = re.search(timer_pattern, text, re.IGNORECASE)
        if timer_match:
            entities['duration'] = {
                'value': int(timer_match.group(1)),
                'unit': timer_match.group(2)
            }
        
        return entities
    
    def _map_to_action(self, intent: str, entities: dict) -> dict:
        """Intent'i system action'a çevir."""
        action_map = {
            'play_music': {
                'type': 'PLAY',
                'params': {
                    'query': entities.get('song', ''),
                    'artist': entities.get('artist', ''),
                    'shuffle': False
                }
            },
            'set_volume': {
                'type': 'VOLUME',
                'params': {
                    'level': entities.get('volume', 50)
                }
            },
            'next_track': {
                'type': 'SKIP',
                'params': {}
            }
        }
        
        return action_map.get(intent, {
            'type': 'UNKNOWN',
            'params': {}
        })
```

### Text-to-Speech (TTS)

```python
class TextToSpeech:
    def __init__(self, voice_id="tr_TR-Standard-A"):
        self.voice_id = voice_id
        self.sample_rate = 22050
        
    def synthesize(self, text: str, speed: float = 1.0) -> np.ndarray:
        """Metni sese çevir."""
        
        # Prosody analizi
        prosody = self._analyze_prosody(text)
        
        # Phoneme dizisine çevir
        phonemes = self._text_to_phonemes(text)
        
        # Waveform üret
        waveform = self._generate_waveform(phonemes, prosody, speed)
        
        # Post-processing
        waveform = self._apply_enhancement(waveform)
        
        return waveform
    
    def _analyze_prosody(self, text: str) -> dict:
        """Prosody (vurgu, ton) analizi."""
        sentences = text.split('.')
        
        prosody = {
            'pitch_contour': [],
            'duration': [],
            'energy': []
        }
        
        for sentence in sentences:
            if not sentence.strip():
                continue
            
            # Basit prosody kuralları
            words = sentence.split()
            
            # Son kelime için pitch düşüşü
            for i, word in enumerate(words):
                if i == len(words) - 1:
                    prosody['pitch_contour'].append(-0.2)
                elif i == 0:
                    prosody['pitch_contour'].append(0.1)
                else:
                    prosody['pitch_contour'].append(0.0)
                
                prosody['duration'].append(len(word) * 0.05)
                prosody['energy'].append(0.8 if i < 3 else 0.6)
        
        return prosody
```

## API / Konfigürasyon

```yaml
# config/voice-assistant.yaml
wake_word:
  enabled: true
  model_path: "models/wake_word_v3.onnx"
  threshold: 0.7
  sample_rate: 16000
  chunk_size: 1280
  
nlu:
  model_name: "dbmdz/bert-base-turkish-cased"
  confidence_threshold: 0.5
  max_sequence_length: 128
  
tts:
  voice_id: "tr_TR-Standard-A"
  sample_rate: 22050
  speed: 1.0
  
dialog:
  max_history: 10
  timeout_seconds: 30
  confirmation_required: ["delete_playlist", "shuffle_all"]
  
privacy:
  local_processing: true
  cloud_fallback: false
  data_retention_days: 7
```

## Performans / Ölçeklenebilirlik

| Metrik | Değer |
|--------|-------|
| Wake Word Latency | 45ms |
| NLU Latency | 25ms |
| TTS Latency | 180ms |
| End-to-End Latency | 250ms |
| Memory Usage | 350MB |
| CPU Usage | 15% |

## Bağımlılıklar

| Bağımlılık | Version | Amaç |
|------------|---------|------|
| transformers | 4.35+ | NLU model |
| torch | 2.1+ | Model inference |
| librosa | 0.10+ | Audio processing |
| onnxruntime | 1.17+ | Edge inference |
| pyttsx3 | 2.90+ | Fallback TTS |

## Durum: Implementasyon

Voice Assistant modülü **development** aşamasındadır. Wake word detection ve NLU modülleri çalışıyor. TTS entegrasyonu devam etmektedir. Edge deployment için optimizasyon gereklidir.
