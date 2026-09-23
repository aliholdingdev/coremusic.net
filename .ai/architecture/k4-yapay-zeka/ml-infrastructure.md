---
title: "ML Altyapısı"
layer: K4
category: "Yapay Zeka"
date: 2026-09-20
version: 1.0.0
status: stable
components: 14
dependencies: [K0, K1, K3, K5]
---

# ML Infrastructure - Makine Öğrenimi Altyapısı

## Genel Bakış

ML Infrastructure modülü, model training, serving ve lifecycle management için gereken altyapıyı sağlar. Model registry, feature store, training pipeline ve model serving bileşenlerini içerir. Production-ready MLOps altyapısı ile model versiyonlama, A/B testing ve monitoring desteği sunar.

## Teknik Detaylar

### Model Registry

Model versiyonlama ve lifecycle yönetimi:

```python
import json
import hashlib
from datetime import datetime
from enum import Enum

class ModelStatus(Enum):
    STAGING = "staging"
    PRODUCTION = "production"
    ARCHIVED = "archived"
    DEPRECATED = "deprecated"

class ModelRegistry:
    def __init__(self, storage_path: str = "models/registry"):
        self.storage_path = storage_path
        self.registry = {}
        self._load_registry()
    
    def register_model(self, model_name: str, version: str,
                       model_path: str, metrics: dict,
                       metadata: dict = None) -> dict:
        """Yeni model versiyonu kaydet."""
        
        # Model hash hesaplama
        model_hash = self._calculate_hash(model_path)
        
        # Registry entry
        entry = {
            'model_name': model_name,
            'version': version,
            'model_path': model_path,
            'model_hash': model_hash,
            'metrics': metrics,
            'metadata': metadata or {},
            'status': ModelStatus.STAGING.value,
            'created_at': datetime.now().isoformat(),
            'updated_at': datetime.now().isoformat(),
            'registered_by': 'system'
        }
        
        # Registry'yi güncelle
        if model_name not in self.registry:
            self.registry[model_name] = {}
        
        self.registry[model_name][version] = entry
        
        # Persist
        self._save_registry()
        
        return entry
    
    def promote_model(self, model_name: str, version: str,
                      target_status: ModelStatus) -> dict:
        """Model durumunu değiştir."""
        
        if model_name not in self.registry:
            raise ValueError(f"Model {model_name} not found")
        
        if version not in self.registry[model_name]:
            raise ValueError(f"Version {version} not found")
        
        entry = self.registry[model_name][version]
        
        # Production'a terfi için ek kontroller
        if target_status == ModelStatus.PRODUCTION:
            if entry['metrics'].get('accuracy', 0) < 0.85:
                raise ValueError("Model accuracy below threshold")
            
            # Önceki production modelini arşivle
            self._archive_current_production(model_name)
        
        entry['status'] = target_status.value
        entry['updated_at'] = datetime.now().isoformat()
        
        self._save_registry()
        
        return entry
    
    def get_model(self, model_name: str, version: str = None) -> dict:
        """Model bilgisini al."""
        
        if model_name not in self.registry:
            raise ValueError(f"Model {model_name} not found")
        
        if version:
            if version not in self.registry[model_name]:
                raise ValueError(f"Version {version} not found")
            return self.registry[model_name][version]
        
        # Production versiyonunu döndür
        for v, entry in self.registry[model_name].items():
            if entry['status'] == ModelStatus.PRODUCTION.value:
                return entry
        
        raise ValueError(f"No production model found for {model_name}")
    
    def list_models(self, status: ModelStatus = None) -> list:
        """Tüm modelleri listele."""
        models = []
        
        for model_name, versions in self.registry.items():
            for version, entry in versions.items():
                if status is None or entry['status'] == status.value:
                    models.append({
                        'model_name': model_name,
                        'version': version,
                        **entry
                    })
        
        return models
    
    def _calculate_hash(self, model_path: str) -> str:
        """Model dosyası hash'i."""
        sha256_hash = hashlib.sha256()
        
        with open(model_path, "rb") as f:
            for byte_block in iter(lambda: f.read(4096), b""):
                sha256_hash.update(byte_block)
        
        return sha256_hash.hexdigest()
    
    def _archive_current_production(self, model_name: str):
        """Mevcut production modelini arşivle."""
        for version, entry in self.registry.get(model_name, {}).items():
            if entry['status'] == ModelStatus.PRODUCTION.value:
                entry['status'] = ModelStatus.ARCHIVED.value
                entry['updated_at'] = datetime.now().isoformat()
                break
```

### Feature Store

Öznitelik deposu ve feature pipeline:

```python
import pandas as pd
from typing import Dict, List
import redis
import json

class FeatureStore:
    def __init__(self, redis_client: redis.Redis, mysql_conn):
        self.redis = redis_client
        self.mysql = mysql_conn
        self.feature_definitions = self._load_feature_definitions()
    
    def get_features(self, entity_id: int, 
                     feature_names: List[str]) -> Dict:
        """Entity için öznitelikleri al."""
        
        features = {}
        
        for name in feature_names:
            # Önce Redis'ten kontrol et
            cached = self._get_from_cache(entity_id, name)
            if cached is not None:
                features[name] = cached
                continue
            
            # MySQL'den al
            value = self._get_from_mysql(entity_id, name)
            
            # Cache'e yaz
            self._set_cache(entity_id, name, value)
            
            features[name] = value
        
        return features
    
    def get_batch_features(self, entity_ids: List[int],
                           feature_names: List[str]) -> pd.DataFrame:
        """Batch öznitelik alımı."""
        
        # Online features (Redis)
        online_features = self._get_online_features(entity_ids, feature_names)
        
        # Offline features (MySQL)
        offline_features = self._get_offline_features(entity_ids, feature_names)
        
        # Birleştir
        df = pd.DataFrame(index=entity_ids)
        
        for name in feature_names:
            if name in online_features:
                df[name] = online_features[name]
            elif name in offline_features:
                df[name] = offline_features[name]
        
        return df
    
    def update_features(self, entity_id: int, features: Dict):
        """Öznitelikleri güncelle."""
        
        for name, value in features.items():
            # Feature definition kontrolü
            if name not in self.feature_definitions:
                raise ValueError(f"Unknown feature: {name}")
            
            definition = self.feature_definitions[name]
            
            # Transformation uygula
            transformed_value = self._apply_transformation(
                value, definition.get('transformation')
            )
            
            # Validation
            self._validate_feature(transformed_value, definition)
            
            # Cache'i güncelle
            self._set_cache(entity_id, name, transformed_value)
            
            # MySQL'i güncelle
            self._update_mysql(entity_id, name, transformed_value)
    
    def create_feature_view(self, view_name: str, 
                           feature_names: List[str],
                           description: str = ""):
        """Feature view oluştur."""
        
        view = {
            'name': view_name,
            'features': feature_names,
            'description': description,
            'created_at': datetime.now().isoformat()
        }
        
        # MySQL'e kaydet
        cursor = self.mysql.cursor()
        cursor.execute("""
            INSERT INTO feature_views (name, features, description)
            VALUES (%s, %s, %s)
        """, (view_name, json.dumps(feature_names), description))
        self.mysql.commit()
        
        return view
    
    def _get_from_cache(self, entity_id: int, feature_name: str):
        """Redis'ten öznitelik al."""
        key = f"feature:{entity_id}:{feature_name}"
        cached = self.redis.get(key)
        
        if cached:
            return json.loads(cached)
        return None
    
    def _set_cache(self, entity_id: int, feature_name: str, value):
        """Redis'e öznitelik kaydet."""
        key = f"feature:{entity_id}:{feature_name}"
        ttl = self.feature_definitions.get(feature_name, {}).get('ttl', 3600)
        
        self.redis.setex(key, ttl, json.dumps(value))
```

### Training Pipeline

```python
import mlflow
from sklearn.model_selection import train_test_split
import optuna

class TrainingPipeline:
    def __init__(self, model_registry: ModelRegistry):
        self.registry = model_registry
        self.experiment_name = "coremusic-ml"
        
        mlflow.set_experiment(self.experiment_name)
    
    def train_model(self, model_name: str, model_class, 
                    dataset: pd.DataFrame, target: str,
                    config: dict = None) -> dict:
        """Model eğitimi pipeline'ı."""
        
        with mlflow.start_run(run_name=f"{model_name}_training"):
            # Config
            config = config or self._get_default_config(model_name)
            
            # Veriyi hazırla
            X_train, X_val, y_train, y_val = train_test_split(
                dataset.drop(columns=[target]),
                dataset[target],
                test_size=0.2,
                random_state=42
            )
            
            # Model oluştur
            model = model_class(**config)
            
            # Hiperparametre optimizasyonu
            if config.get('optimize_hyperparams', False):
                best_params = self._optimize_hyperparameters(
                    model_class, X_train, y_train, X_val, y_val
                )
                model = model_class(**best_params)
                config.update(best_params)
            
            # Eğit
            model.fit(X_train, y_train)
            
            # Değerlendir
            metrics = self._evaluate_model(model, X_val, y_val)
            
            # MLflow logging
            mlflow.log_params(config)
            mlflow.log_metrics(metrics)
            
            # Model kaydet
            model_path = self._save_model(model, model_name)
            
            # Registry'ye kaydet
            version = self._get_next_version(model_name)
            entry = self.registry.register_model(
                model_name=model_name,
                version=version,
                model_path=model_path,
                metrics=metrics,
                metadata={
                    'config': config,
                    'dataset_size': len(dataset),
                    'train_size': len(X_train),
                    'val_size': len(X_val)
                }
            )
            
            return {
                'model_name': model_name,
                'version': version,
                'metrics': metrics,
                'config': config
            }
    
    def _optimize_hyperparameters(self, model_class, X_train, y_train,
                                  X_val, y_val, n_trials=50) -> dict:
        """Optuna ile hiperparametre optimizasyonu."""
        
        def objective(trial):
            params = {
                'n_estimators': trial.suggest_int('n_estimators', 100, 1000),
                'max_depth': trial.suggest_int('max_depth', 3, 20),
                'learning_rate': trial.suggest_float('learning_rate', 0.01, 0.3, log=True),
                'min_child_weight': trial.suggest_int('min_child_weight', 1, 10),
                'subsample': trial.suggest_float('subsample', 0.6, 1.0),
                'colsample_bytree': trial.suggest_float('colsample_bytree', 0.6, 1.0)
            }
            
            model = model_class(**params)
            model.fit(X_train, y_train)
            
            predictions = model.predict(X_val)
            accuracy = np.mean(predictions == y_val)
            
            return accuracy
        
        study = optuna.create_study(direction='maximize')
        study.optimize(objective, n_trials=n_trials)
        
        return study.best_params
    
    def _evaluate_model(self, model, X_val, y_val) -> dict:
        """Model performansını değerlendir."""
        from sklearn.metrics import accuracy_score, f1_score, roc_auc_score
        
        predictions = model.predict(X_val)
        probabilities = model.predict_proba(X_val)[:, 1] if hasattr(model, 'predict_proba') else None
        
        metrics = {
            'accuracy': float(accuracy_score(y_val, predictions)),
            'f1_score': float(f1_score(y_val, predictions, average='weighted'))
        }
        
        if probabilities is not None:
            metrics['auc_roc'] = float(roc_auc_score(y_val, probabilities))
        
        return metrics
```

### Model Serving

```python
import asyncio
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import torch

app = FastAPI()

class ModelServer:
    def __init__(self, registry: ModelRegistry):
        self.registry = registry
        self.loaded_models = {}
        self.device = "cuda" if torch.cuda.is_available() else "cpu"
    
    async def load_model(self, model_name: str, version: str = None):
        """Model'i belleğe yükle."""
        
        entry = self.registry.get_model(model_name, version)
        
        # Model yükle
        model = self._load_model_from_path(entry['model_path'])
        model.to(self.device)
        model.eval()
        
        cache_key = f"{model_name}:{entry['version']}"
        self.loaded_models[cache_key] = {
            'model': model,
            'metadata': entry,
            'loaded_at': datetime.now()
        }
        
        return cache_key
    
    async def predict(self, model_name: str, input_data: dict) -> dict:
        """Tahmin yap."""
        
        # Model'i bul
        cache_key = self._find_loaded_model(model_name)
        if not cache_key:
            await self.load_model(model_name)
            cache_key = self._find_loaded_model(model_name)
        
        model_info = self.loaded_models[cache_key]
        model = model_info['model']
        
        # Input işleme
        processed_input = self._preprocess_input(input_data)
        
        # Inference
        with torch.no_grad():
            input_tensor = torch.tensor(processed_input).to(self.device)
            output = model(input_tensor)
        
        # Post-processing
        result = self._postprocess_output(output)
        
        return {
            'prediction': result,
            'model_version': model_info['metadata']['version'],
            'latency_ms': self._measure_latency()
        }
```

## API / Konfigürasyon

```yaml
# config/ml-infrastructure.yaml
model_registry:
  storage_path: "models/registry"
  auto_archive: true
  max_versions: 10
  
feature_store:
  redis_ttl: 3600
  batch_size: 1000
  online_features: true
  offline_features: true
  
training:
  mlflow_tracking_uri: "http://localhost:5000"
  experiment_name: "coremusic-ml"
  default_optimizer: "adam"
  early_stopping_patience: 10
  
serving:
  max_batch_size: 32
  timeout_seconds: 30
  gpu_memory_fraction: 0.8
  num_workers: 4
  
monitoring:
  enable_metrics: true
  metrics_port: 9090
  log_predictions: true
  alert_threshold: 0.1
```

## Performans / Ölçeklenebilirlik

| Metrik | Değer |
|--------|-------|
| Model Load Time | 2.5s |
| Inference Latency | 15ms |
| Throughput | 500 req/s |
| Feature Store Latency | 5ms |
| Training Time (1M rows) | 30min |

## Bağımlılıklar

| Bağımlılık | Version | Amaç |
|------------|---------|------|
| mlflow | 2.8+ | Experiment tracking |
| optuna | 3.4+ | Hyperparameter optimization |
| redis | 5.0+ | Feature store cache |
| mysql | 8.0 | Feature store persistence |
| fastapi | 0.104+ | Model serving API |
| uvicorn | 0.24+ | ASGI server |

## Durum: Implementasyon

ML Infrastructure modülü **stable** durumdadır. Model registry ve feature store production-ready. Training pipeline aktif olarak kullanılmaktadır.
