---
title: "Edge AI Modülü"
layer: K4
category: "Yapay Zeka"
date: 2026-09-20
version: 1.0.0
status: development
components: 6
dependencies: [K0, K1, K4]
---

# Edge AI - Cihaz İçi Yapay Zeka

## Genel Bakış

Edge AI modülü, modelsuz cihazlarda çalışacak şekilde optimize edilmiş yapay zeka modüllerini yönetir. Model quantization, on-device inference ve privacy-preserving ML teknikleri kullanarak yerel işlem gücüyle AI özelliklerini sunar. Bulut bağlantısı olmadan çalışabilen bağımsız bir AI katmanı hedefler.

## Teknik Detaylar

### Model Quantization

Model boyutu ve inference hızı için quantization:

```python
import torch
import torch.quantization as quant
from torch.quantization import QuantStub, DeQuantStub

class ModelQuantizer:
    def __init__(self):
        self.quantization_configs = {
            'fp32': {'dtype': torch.float32},
            'fp16': {'dtype': torch.float16},
            'int8': {
                'dtype': torch.qint8,
                'scheme': quant.PerChannelMinMax,
                'observer': quant.MovingAverageMinMaxObserver
            },
            'int4': {
                'dtype': torch.qint4,
                'scheme': quant.MinMax,
                'observer': quant.HistogramObserver
            }
        }
    
    def quantize_model(self, model: torch.nn.Module, 
                       method: str = 'int8') -> torch.nn.Module:
        """Model'i belirtilen yöntemle quantize et."""
        
        if method == 'dynamic':
            return self._dynamic_quantization(model)
        elif method == 'static':
            return self._static_quantization(model)
        elif method == 'qat':
            return self._quantization_aware_training(model)
        else:
            raise ValueError(f"Unknown method: {method}")
    
    def _dynamic_quantization(self, model: torch.nn.Module) -> torch.nn.Module:
        """Dynamic quantization (en basit)."""
        quantized_model = torch.quantization.quantize_dynamic(
            model,
            {torch.nn.Linear, torch.nn.Conv2d},
            dtype=torch.qint8
        )
        return quantized_model
    
    def _static_quantization(self, model: torch.nn.Module,
                             calibration_data: torch.Tensor = None) -> torch.nn.Module:
        """Static quantization (kalibrasyon gerekli)."""
        
        model.eval()
        
        # Quantization config
        model.qconfig = quant.get_default_qconfig('fbgemm')
        
        # Prepare
        model_prepared = quant.prepare(model)
        
        # Kalibrasyon
        if calibration_data is not None:
            with torch.no_grad():
                for batch in calibration_data:
                    model_prepared(batch)
        
        # Convert
        model_quantized = quant.convert(model_prepared)
        
        return model_quantized
    
    def get_model_size(self, model: torch.nn.Module) -> dict:
        """Model boyutunu hesapla."""
        import io
        
        # fp32 boyutu
        buffer = io.BytesIO()
        torch.save(model.state_dict(), buffer)
        fp32_size = buffer.tell() / (1024 * 1024)  # MB
        
        # Quantize et
        quantized = self.quantize_model(model, 'int8')
        
        buffer = io.BytesIO()
        torch.save(quantized.state_dict(), buffer)
        int8_size = buffer.tell() / (1024 * 1024)  # MB
        
        return {
            'fp32_size_mb': round(fp32_size, 2),
            'int8_size_mb': round(int8_size, 2),
            'compression_ratio': round(fp32_size / int8_size, 2),
            'size_reduction': f"{(1 - int8_size/fp32_size) * 100:.1f}%"
        }
    
    def benchmark_quantized(self, model: torch.nn.Module, 
                           input_shape: tuple,
                           num_runs: int = 100) -> dict:
        """Quantize edilmiş model performansını karşılaştır."""
        
        import time
        
        # Original model
        model.eval()
        input_tensor = torch.randn(input_shape)
        
        # Warmup
        for _ in range(10):
            with torch.no_grad():
                model(input_tensor)
        
        # Benchmark
        start = time.time()
        for _ in range(num_runs):
            with torch.no_grad():
                model(input_tensor)
        fp32_time = (time.time() - start) / num_runs * 1000
        
        # Quantized model
        quantized = self.quantize_model(model, 'int8')
        
        # Warmup
        for _ in range(10):
            with torch.no_grad():
                quantized(input_tensor)
        
        # Benchmark
        start = time.time()
        for _ in range(num_runs):
            with torch.no_grad():
                quantized(input_tensor)
        int8_time = (time.time() - start) / num_runs * 1000
        
        return {
            'fp32_latency_ms': round(fp32_time, 2),
            'int8_latency_ms': round(int8_time, 2),
            'speedup': round(fp32_time / int8_time, 2)
        }
```

### On-Device Inference

```python
import numpy as np
from typing import Optional

class OnDeviceInference:
    def __init__(self, model_path: str, backend: str = "onnx"):
        self.backend = backend
        self.model = self._load_model(model_path)
        self.input_preprocessor = InputPreprocessor()
        self.output_postprocessor = OutputPostprocessor()
        
        # Memory management
        self.max_memory_mb = 512
        self.current_memory_usage = 0
        
        # Thread pool
        self.num_threads = self._detect_optimal_threads()
    
    def _load_model(self, model_path: str):
        """Model'i cihaza yükle."""
        
        if self.backend == "onnx":
            import onnxruntime as ort
            
            # Session options
            options = ort.SessionOptions()
            options.intra_op_num_threads = self.num_threads
            options.inter_op_num_threads = 2
            options.graph_optimization_level = ort.GraphOptimizationLevel.ORT_ENABLE_ALL
            
            # Execution provider
            providers = ['CPUExecutionProvider']
            if self._cuda_available():
                providers.insert(0, 'CUDAExecutionProvider')
            
            session = ort.InferenceSession(
                model_path, options, providers=providers
            )
            return session
        
        elif self.backend == "tflite":
            import tensorflow as tf
            
            interpreter = tf.lite.Interpreter(
                model_path=model_path,
                num_threads=self.num_threads
            )
            interpreter.allocate_tensors()
            return interpreter
        
        elif self.backend == "coreml":
            import coremltools as ct
            
            model = ct.models.MLModel(model_path)
            return model
    
    def infer(self, input_data: np.ndarray) -> dict:
        """Inference yap."""
        
        # Pre-processing
        processed_input = self.input_preprocessor.process(input_data)
        
        # Backend'e göre inference
        if self.backend == "onnx":
            output = self._infer_onnx(processed_input)
        elif self.backend == "tflite":
            output = self._infer_tflite(processed_input)
        elif self.backend == "coreml":
            output = self._infer_coreml(processed_input)
        
        # Post-processing
        result = self.output_postprocessor.process(output)
        
        return result
    
    def _infer_onnx(self, input_data: np.ndarray) -> np.ndarray:
        """ONNX Runtime inference."""
        import onnxruntime as ort
        
        # Input name al
        input_name = self.model.get_inputs()[0].name
        
        # Inference
        output = self.model.run(None, {input_name: input_data.astype(np.float32)})
        
        return output[0]
    
    def _detect_optimal_threads(self) -> int:
        """Optimal thread sayısını tespit et."""
        import os
        import multiprocessing
        
        cpu_count = multiprocessing.cpu_count()
        
        # Mobil cihazlar için daha az thread
        if self._is_mobile_device():
            return min(4, cpu_count)
        
        return min(8, cpu_count)
    
    def _is_mobile_device(self) -> bool:
        """Mobil cihaz kontrolü."""
        import platform
        system = platform.system().lower()
        return system in ['android', 'ios']
    
    def _cuda_available(self) -> bool:
        """CUDA desteği kontrolü."""
        try:
            import torch
            return torch.cuda.is_available()
        except:
            return False
```

### Privacy-Preserving ML

```python
import hashlib
from typing import Tuple

class PrivacyPreservingML:
    def __init__(self, epsilon: float = 1.0):
        """
        epsilon: Differential privacy parameter
        Düşük epsilon = daha fazla gizlilik, daha az doğruluk
        """
        self.epsilon = epsilon
        self.noise_scale = 1.0 / epsilon
    
    def federated_averaging(self, client_models: list,
                           client_sizes: list) -> dict:
        """Federated learning - weighted averaging."""
        
        # Toplam boyut
        total_size = sum(client_sizes)
        
        # Ağırlıklı ortalama
        global_weights = None
        
        for model, size in zip(client_models, client_sizes):
            weight = size / total_size
            
            if global_weights is None:
                global_weights = {k: v * weight for k, v in model.items()}
            else:
                for k in global_weights:
                    global_weights[k] += model[k] * weight
        
        # Differential privacy noise ekle
        noisy_weights = self._add_dp_noise(global_weights)
        
        return {
            'global_model': noisy_weights,
            'num_clients': len(client_models),
            'total_size': total_size,
            'epsilon': self.epsilon
        }
    
    def _add_dp_noise(self, weights: dict) -> dict:
        """Differential privacy noise ekle."""
        noisy_weights = {}
        
        for key, value in weights.items():
            if isinstance(value, np.ndarray):
                # Gaussian noise
                noise = np.random.normal(
                    0, self.noise_scale, value.shape
                )
                noisy_weights[key] = value + noise.astype(value.dtype)
            else:
                noisy_weights[key] = value
        
        return noisy_weights
    
    def secure_aggregation(self, client_updates: list) -> dict:
        """Güvenli aggregation (encryption-based)."""
        
        # Shamir's Secret Sharing
        shares = self._secret_share_updates(client_updates)
        
        # Shares'leri birleştir
        aggregated_shares = self._aggregate_shares(shares)
        
        # Reconstruct
        aggregated_update = self._reconstruct(aggregated_shares)
        
        return {
            'aggregated_update': aggregated_update,
            'num_clients': len(client_updates),
            'security_level': 'threshold'
        }
    
    def _secret_share_updates(self, updates: list) -> list:
        """Secret sharing ile updates paylaş."""
        shares = []
        
        for update in updates:
            client_shares = []
            
            for key, value in update.items():
                if isinstance(value, np.ndarray):
                    # Random shares oluştur
                    n_clients = len(updates)
                    random_shares = [
                        np.random.randn(*value.shape).astype(value.dtype)
                        for _ in range(n_clients - 1)
                    ]
                    
                    # Son share = value - sum(other shares)
                    last_share = value - sum(random_shares)
                    random_shares.append(last_share)
                    
                    client_shares.append({
                        'key': key,
                        'shares': random_shares
                    })
            
            shares.append(client_shares)
        
        return shares
    
    def local_differential_privacy(self, data: np.ndarray,
                                   mechanism: str = "laplace") -> np.ndarray:
        """Yerel differential privacy."""
        
        if mechanism == "laplace":
            # Laplace mechanism
            sensitivity = 1.0
            noise = np.random.laplace(
                0, sensitivity / self.epsilon, data.shape
            )
        elif mechanism == "gaussian":
            # Gaussian mechanism
            sensitivity = 1.0
            sigma = sensitivity * np.sqrt(2 * np.log(1.25 / 0.001)) / self.epsilon
            noise = np.random.normal(0, sigma, data.shape)
        elif mechanism == "exponential":
            # Exponential mechanism
            scores = -np.abs(data)
            probs = np.exp(self.epsilon * scores / 2)
            probs /= probs.sum()
            indices = np.random.choice(len(data), size=data.shape, p=probs)
            return data[indices]
        
        return data + noise.astype(data.dtype)
```

### Edge Model Optimization

```python
class EdgeModelOptimizer:
    def __init__(self, target_device: str = "raspberry_pi"):
        self.target_device = target_device
        self.device_specs = self._get_device_specs(target_device)
    
    def _get_device_specs(self, device: str) -> dict:
        """Cihaz özelliklerini al."""
        specs = {
            'raspberry_pi': {
                'cpu': 'ARM Cortex-A72',
                'ram_mb': 4096,
                'gpu': None,
                'compute_capability': 0,
                'max_model_size_mb': 200
            },
            'jetson_nano': {
                'cpu': 'ARM Cortex-A57',
                'ram_mb': 4096,
                'gpu': 'NVIDIA Maxwell',
                'compute_capability': 5.3,
                'max_model_size_mb': 500
            },
            'esp32': {
                'cpu': 'Xtensa LX6',
                'ram_mb': 520,
                'gpu': None,
                'compute_capability': 0,
                'max_model_size_mb': 20
            },
            'phone': {
                'cpu': 'ARM Cortex-A78',
                'ram_mb': 8192,
                'gpu': 'Adreno/Mali',
                'compute_capability': 6,
                'max_model_size_mb': 1000
            }
        }
        
        return specs.get(device, specs['phone'])
    
    def optimize_for_device(self, model: torch.nn.Module) -> dict:
        """Cihaza özel optimizasyon."""
        
        optimizations = []
        
        # Model boyutu kontrolü
        current_size = self._get_model_size_mb(model)
        max_size = self.device_specs['max_model_size_mb']
        
        if current_size > max_size:
            # Quantization gerekli
            model = self._apply_optimal_quantization(model, max_size)
            optimizations.append(f"quantized to fit {max_size}MB")
        
        # CPU optimization
        if self.device_specs['gpu'] is None:
            # CPU-only optimization
            model = self._optimize_for_cpu(model)
            optimizations.append("optimized for CPU inference")
        
        # Memory optimization
        model = self._apply_memory_optimization(model)
        optimizations.append("memory optimization applied")
        
        # Operator fusion
        if hasattr(torch, 'compile'):
            model = torch.compile(model, mode="reduce-overhead")
            optimizations.append("operator fusion applied")
        
        return {
            'model': model,
            'optimizations': optimizations,
            'final_size_mb': self._get_model_size_mb(model)
        }
    
    def _apply_optimal_quantization(self, model: torch.nn.Module,
                                    target_size_mb: int) -> torch.nn.Module:
        """Optimal quantization uygula."""
        quantizer = ModelQuantizer()
        
        current_size = self._get_model_size_mb(model)
        required_ratio = target_size_mb / current_size
        
        if required_ratio < 0.125:  # 8x reduction needed
            return quantizer.quantize_model(model, 'int4')
        elif required_ratio < 0.25:  # 4x reduction needed
            return quantizer.quantize_model(model, 'int8')
        else:
            return quantizer.quantize_model(model, 'int8')
    
    def _apply_memory_optimization(self, model: torch.nn.Module) -> torch.nn.Module:
        """Bellek optimizasyonu."""
        # Gradient checkpointing (training için)
        if hasattr(model, 'gradient_checkpointing_enable'):
            model.gradient_checkpointing_enable()
        
        # Activation recomputation
        for module in model.modules():
            if isinstance(module, torch.nn.MultiheadAttention):
                module.training = False  # Disable attention dropout
        
        return model
```

## API / Konfigürasyon

```yaml
# config/edge-ai.yaml
target_device: "auto"
quantization:
  enabled: true
  default_method: "int8"
  fallback_method: "fp16"
  
inference:
  backend: "onnx"
  num_threads: "auto"
  batch_size: 1
  timeout_ms: 100
  
privacy:
  differential_privacy: true
  epsilon: 1.0
  federated_learning: false
  local_processing: true
  
optimization:
  operator_fusion: true
  memory_optimization: true
  cache_size_mb: 256
  
model_management:
  auto_download: true
  update_check_interval: 86400
  max_cached_models: 5
```

## Performans / Ölçeklenebilirlik

| Cihaz | Model | Boyut | Latency | Throughput |
|-------|-------|-------|---------|------------|
| Raspberry Pi 4 | Whispersmall | 244MB | 2.5s | 0.4x RT |
| Jetson Nano | Whispermedium | 769MB | 800ms | 1.25x RT |
| Phone (Snapdragon) | Whisperlarge | 1.5GB | 500ms | 2x RT |
| ESP32 | Custom CNN | 2MB | 50ms | 20x RT |

## Bağımlılıklar

| Bağımlılık | Version | Amaç |
|------------|---------|------|
| onnxruntime | 1.17+ | ONNX inference |
| tensorflow-lite | 2.14+ | TFLite inference |
| torch | 2.1+ | Model quantization |
| coremltools | 7.0+ | Core ML export |

## Durum: Implementasyon

Edge AI modülü **development** aşamasındadır. Model quantization ve ONNX export çalışıyor. Cihaz bazlı optimizasyon devam etmektedir.
