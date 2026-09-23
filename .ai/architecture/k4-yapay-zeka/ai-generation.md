---
title: "AI Üretim Modülü"
layer: K4
category: "Yapay Zeka"
date: 2026-09-20
version: 1.0.0
status: experimental
components: 9
dependencies: [K0, K3, K4, K5]
---

# AI Generation - Yapay Zeka Üretim Modülü

## Genel Bakış

AI Generation modülü, müzik üretimi, kapak resmi oluşturma ve playlist oluşturma için generative AI modüllerini içerir. Diffusion modelleri, transformer-based müzik üretimi ve content generation pipeline'larını yönetir. Experimental aşamadadır ve kontrollü üretim için guardrails ile donatılmıştır.

## Teknik Detaylar

### Müzik Üretimi (Music Generation)

Transformer tabanlı müzik üretimi:

```python
import torch
import torch.nn as nn
from transformers import GPT2LMHeadModel, GPT2Tokenizer

class MusicGenerator:
    def __init__(self, model_path: str = "models/music-gen-v1"):
        self.model_path = model_path
        self.device = "cuda" if torch.cuda.is_available() else "cpu"
        
        # MIDI tokenizer
        self.tokenizer = MIDITokenizer(vocab_size=512)
        
        # Generation model
        self.model = MusicTransformer(
            vocab_size=512,
            d_model=512,
            nhead=8,
            num_layers=12
        ).to(self.device)
        
        # Checkpoint yükleme
        self._load_checkpoint(model_path)
        
        # Temperature ve sampling parametreleri
        self.generation_config = {
            'temperature': 0.8,
            'top_k': 50,
            'top_p': 0.95,
            'repetition_penalty': 1.2,
            'max_length': 2048
        }
    
    def generate(self, prompt: dict = None, duration_seconds: int = 30,
                 style: str = None) -> dict:
        """Müzik üret."""
        
        # Prompt işleme
        if prompt is None:
            prompt = self._create_random_prompt()
        
        # Token dizisine çevir
        input_tokens = self.tokenizer.encode_prompt(prompt)
        
        # Üretim
        generated_tokens = self._generate_tokens(
            input_tokens, 
            max_new_tokens=duration_seconds * 50  # ~50 tokens/second
        )
        
        # MIDI'ye çevir
        midi_data = self.tokenizer.decode_to_midi(generated_tokens)
        
        # Post-processing
        midi_data = self._post_process(midi_data)
        
        # Metadata
        metadata = self._extract_metadata(midi_data)
        
        return {
            'midi_data': midi_data,
            'metadata': metadata,
            'duration_seconds': len(midi_data.notes) * 0.02,
            'tokens_generated': len(generated_tokens),
            'generation_config': self.generation_config
        }
    
    def _generate_tokens(self, input_tokens: list, 
                         max_new_tokens: int) -> list:
        """Token-based generation."""
        self.model.eval()
        
        input_tensor = torch.tensor(
            [input_tokens], dtype=torch.long
        ).to(self.device)
        
        generated = input_tokens.copy()
        
        with torch.no_grad():
            for _ in range(max_new_tokens):
                # Forward pass
                logits = self.model(input_tensor)
                
                # Son token'ın logits'ları
                next_token_logits = logits[:, -1, :] / self.generation_config['temperature']
                
                # Top-k sampling
                top_k_logits, top_k_indices = torch.topk(
                    next_token_logits, self.generation_config['top_k']
                )
                
                # Top-p sampling
                probs = torch.softmax(top_k_logits, dim=-1)
                sorted_probs, sorted_indices = torch.sort(probs, descending=True)
                cumulative_probs = torch.cumsum(sorted_probs, dim=-1)
                
                # Nucleus sampling
                sorted_indices_to_remove = cumulative_probs > self.generation_config['top_p']
                sorted_indices_to_remove[..., 1:] = sorted_indices_to_remove[..., :-1].clone()
                sorted_indices_to_remove[..., 0] = 0
                
                indices_to_remove = sorted_indices_to_remove.scatter(1, sorted_indices, sorted_indices_to_remove)
                top_k_logits[indices_to_remove] = float('-inf')
                
                # Sampling
                probs = torch.softmax(top_k_logits, dim=-1)
                next_token = torch.multinomial(probs, num_samples=1)
                
                # Repetition penalty
                if next_token.item() in generated[-50:]:
                    logits[:, -1, next_token] /= self.generation_config['repetition_penalty']
                    probs = torch.softmax(logits[:, -1, :], dim=-1)
                    next_token = torch.multinomial(probs, num_samples=1)
                
                generated.append(next_token.item())
                
                # EOS kontrolü
                if next_token.item() == self.tokenizer.eos_token:
                    break
                
                # Input tensor'u güncelle
                input_tensor = torch.cat([
                    input_tensor[:, 1:], 
                    next_token.unsqueeze(0)
                ], dim=1)
        
        return generated
```

### Kapak Resmi Üretimi (Cover Art Generation)

Diffusion tabanlı albüm kapağı üretimi:

```python
from diffusers import StableDiffusionPipeline, DDIMScheduler
import PIL.Image

class CoverArtGenerator:
    def __init__(self, model_id="stabilityai/stable-diffusion-xl-base-1.0"):
        self.pipe = StableDiffusionPipeline.from_pretrained(
            model_id,
            torch_dtype=torch.float16,
            variant="fp16"
        ).to("cuda")
        
        # Scheduler
        self.pipe.scheduler = DDIMScheduler.from_config(
            self.pipe.scheduler.config
        )
        
        # Style presets
        self.style_presets = {
            'minimalist': 'minimalist album cover, clean design, modern',
            'abstract': 'abstract art album cover, colorful, geometric',
            'vintage': 'vintage album cover, retro style, textured',
            'photography': 'photography album cover, high quality, artistic',
            'illustration': 'illustration album cover, hand drawn, artistic',
            'neon': 'neon album cover, cyberpunk, glowing'
        }
    
    def generate(self, prompt: str, style: str = 'minimalist',
                 width: int = 1024, height: int = 1024,
                 num_images: int = 1) -> dict:
        """Kapak resmi üret."""
        
        # Style preset'ı ekle
        style_text = self.style_presets.get(style, '')
        full_prompt = f"{prompt}, {style_text}, album cover, high quality"
        
        # Negative prompt
        negative_prompt = (
            "low quality, blurry, distorted, deformed, "
            "ugly, bad anatomy, watermark, text, logo"
        )
        
        # Generation
        images = self.pipe(
            prompt=full_prompt,
            negative_prompt=negative_prompt,
            width=width,
            height=height,
            num_inference_steps=50,
            guidance_scale=7.5,
            num_images_per_prompt=num_images
        ).images
        
        # Metadata
        metadata = {
            'prompt': full_prompt,
            'negative_prompt': negative_prompt,
            'style': style,
            'dimensions': f"{width}x{height}",
            'num_inference_steps': 50,
            'guidance_scale': 7.5
        }
        
        return {
            'images': images,
            'metadata': metadata
        }
    
    def generate_from_music(self, audio_features: dict, 
                            style: str = 'minimalist') -> dict:
        """Müzik özelliklerinden kapak resmi üret."""
        
        # Audio features'dan prompt oluştur
        prompt = self._features_to_prompt(audio_features, style)
        
        return self.generate(prompt, style)
    
    def _features_to_prompt(self, features: dict, style: str) -> str:
        """Müzik özelliklerinden prompt üret."""
        mood = features.get('mood_label', 'neutral')
        energy = features.get('energy_level', 'medium')
        genre = features.get('genre', 'general')
        
        mood_descriptions = {
            'happy': 'bright, cheerful, uplifting',
            'sad': 'melancholic, blue, emotional',
            'excited': 'energetic, vibrant, dynamic',
            'calm': 'peaceful, serene, tranquil',
            'romantic': 'romantic, soft, intimate',
            'tense': 'dark, mysterious, suspenseful'
        }
        
        energy_descriptions = {
            'high': 'high energy, powerful, intense',
            'medium': 'balanced, moderate',
            'low': 'soft, gentle, subtle'
        }
        
        prompt = (
            f"Album cover art, {mood_descriptions.get(mood, '')}, "
            f"{energy_descriptions.get(energy, '')}, {genre} music, "
            f"{style} style, professional design"
        )
        
        return prompt
```

### Playlist Üretimi

```python
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler

class PlaylistGenerator:
    def __init__(self, track_database):
        self.db = track_database
        self.kmeans = None
        self.scaler = StandardScaler()
    
    def generate_playlist(self, seed_tracks: list, length: int = 20,
                          mood: str = None, energy: str = None) -> list:
        """Seed track'lardan playlist oluştur."""
        
        # Seed track özelliklerini al
        seed_features = self._get_track_features(seed_tracks)
        
        # Clustering
        clusters = self._cluster_tracks(seed_features)
        
        # Benzer track'ları bul
        similar_tracks = self._find_similar_tracks(
            seed_features, clusters, n=length * 3
        )
        
        # Filtreleme
        filtered = self._apply_filters(
            similar_tracks, mood=mood, energy=energy
        )
        
        # Sıralama (flow consideration)
        sorted_playlist = self._optimize_order(filtered[:length])
        
        # Metadata
        metadata = self._generate_metadata(sorted_playlist, seed_tracks)
        
        return {
            'tracks': sorted_playlist,
            'metadata': metadata,
            'seed_tracks': seed_tracks,
            'total_duration': sum(t['duration'] for t in sorted_playlist)
        }
    
    def _cluster_tracks(self, features: list) -> dict:
        """Track'ları feature clustering ile grupla."""
        feature_matrix = np.array([
            [f['energy'], f['valence'], f['danceability'], 
             f['tempo'] / 200, f['acousticness']]
            for f in features
        ])
        
        # Normalize
        feature_matrix = self.scaler.fit_transform(feature_matrix)
        
        # K-means clustering
        n_clusters = min(5, len(features))
        self.kmeans = KMeans(n_clusters=n_clusters, random_state=42)
        clusters = self.kmeans.fit_predict(feature_matrix)
        
        return {
            'labels': clusters.tolist(),
            'centers': self.kmeans.cluster_centers_.tolist()
        }
    
    def _optimize_order(self, tracks: list) -> list:
        """Enerji akışına göre sırala."""
        if not tracks:
            return tracks
        
        # Energy-based ordering
        # Düşük enerji ile başla, orta enerji, yüksek enerji, düşüş
        energy_sorted = sorted(tracks, key=lambda t: t['energy'])
        
        n = len(energy_sorted)
        if n < 4:
            return energy_sorted
        
        # Arc pattern: low -> medium -> high -> medium -> low
        first_quarter = energy_sorted[:n//4]
        second_quarter = energy_sorted[n//4:n//2]
        third_quarter = energy_sorted[n//2:3*n//4]
        fourth_quarter = energy_sorted[3*n//4:]
        
        ordered = (
            sorted(first_quarter, key=lambda t: t['energy']) +
            sorted(second_quarter, key=lambda t: t['energy']) +
            sorted(third_quarter, key=lambda t: t['energy'], reverse=True) +
            sorted(fourth_quarter, key=lambda t: t['energy'], reverse=True)
        )
        
        return ordered
```

## API / Konfigürasyon

```yaml
# config/ai-generation.yaml
music_generation:
  model_path: "models/music-gen-v1"
  device: "auto"
  max_duration_seconds: 300
  sample_rate: 44100
  
cover_art:
  model_id: "stabilityai/stable-diffusion-xl-base-1.0"
  default_width: 1024
  default_height: 1024
  default_style: "minimalist"
  num_inference_steps: 50
  
playlist:
  max_length: 50
  min_length: 5
  default_mood: null
  energy_flow: "arc"
  
safety:
  content_filter: true
  nsfw_check: true
  copyright_check: true
  human_review_threshold: 0.8
```

## Performans / Ölçeklenebilirlik

| Metrik | Değer |
|--------|-------|
| Music Gen (30s) | 45s |
| Cover Art (1024x1024) | 12s |
| Playlist Gen (20 tracks) | 200ms |
| GPU Memory (SDXL) | 8GB |
| CPU Fallback | 5x slower |

## Bağımlılıklar

| Bağımlılık | Version | Amaç |
|------------|---------|------|
| torch | 2.1+ | Model inference |
| diffusers | 0.21+ | Diffusion models |
| transformers | 4.35+ | Text processing |
| Pillow | 10.0+ | Image processing |
| pretty_midi | 0.2.10+ | MIDI processing |

## Durum: Implementasyon

AI Generation modülü **experimental** aşamadadır. Music generation ve cover art temel fonksiyonları çalışıyor. Safety guardrails ve copyright kontrolü geliştirme aşamasındadır.
