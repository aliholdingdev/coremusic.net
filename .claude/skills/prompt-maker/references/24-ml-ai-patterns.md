# ML/AI PATTERNS — TAM REHBER
# Prompt Maker v7.2.0 | 2026-06-11 | Kiro IDE Native
# Model lifecycle, RAG, vector search, fine-tuning, MLOps, inference

---

## ML SİSTEM MİMARİSİ

### ML Pipeline Katmanları

```
Data Layer
  ├── Raw data ingestion (S3, GCS, HDFS)
  ├── Data validation (Great Expectations, Pandera)
  ├── Feature engineering
  └── Feature store (Feast, Tecton)

Training Layer
  ├── Experiment tracking (MLflow, W&B)
  ├── Hyperparameter tuning (Optuna, Ray Tune)
  ├── Distributed training (PyTorch DDP, Horovod)
  └── Model registry (MLflow, DVC)

Serving Layer
  ├── Model serving (TorchServe, TF Serving, Triton)
  ├── A/B testing
  ├── Shadow deployment
  └── Canary rollout

Monitoring Layer
  ├── Data drift detection
  ├── Model performance monitoring
  ├── Prediction logging
  └── Alerting
```

### MLOps Zorunlu Bileşenler

```
Reproducibility:
  ✅ Seed her yerde sabit (random, numpy, torch)
  ✅ Dependency pinning (requirements.txt exact versions)
  ✅ Docker image versioning
  ✅ Data versioning (DVC)
  ✅ Code versioning (Git + tags)

Experiment Tracking:
  ✅ Her run için: params, metrics, artifacts
  ✅ Model checkpointing
  ✅ Hyperparameter logging
  ✅ Dataset hash logging

Model Registry:
  ✅ Model versioning (semantic)
  ✅ Stage transitions (staging → production)
  ✅ Model lineage (hangi data, hangi code)
  ✅ Rollback capability
```

---

## PYTHON ML STANDARTLARI

### Reproducibility Pattern

```python
import random
import numpy as np
import torch
import os

def set_seed(seed: int = 42) -> None:
    """Tüm random seed'leri sabit yap — reproducibility için zorunlu."""
    random.seed(seed)
    np.random.seed(seed)
    torch.manual_seed(seed)
    torch.cuda.manual_seed_all(seed)
    os.environ['PYTHONHASHSEED'] = str(seed)

    # Deterministic operations (performans maliyeti var)
    torch.backends.cudnn.deterministic = True
    torch.backends.cudnn.benchmark = False

# Her training script'in başında çağır
set_seed(42)
```

### PyTorch Training Loop — Production Grade

```python
from __future__ import annotations

import logging
from dataclasses import dataclass, field
from pathlib import Path
from typing import Optional

import torch
import torch.nn as nn
from torch.utils.data import DataLoader
from torch.optim import AdamW
from torch.optim.lr_scheduler import CosineAnnealingLR

logger = logging.getLogger(__name__)


@dataclass
class TrainingConfig:
    """Training konfigürasyonu — tüm hyperparameter'lar burada."""
    model_name:    str   = "bert-base-uncased"
    num_epochs:    int   = 10
    batch_size:    int   = 32
    learning_rate: float = 2e-5
    weight_decay:  float = 0.01
    warmup_steps:  int   = 500
    max_grad_norm: float = 1.0
    seed:          int   = 42
    device:        str   = field(default_factory=lambda: "cuda" if torch.cuda.is_available() else "cpu")
    output_dir:    Path  = Path("./outputs")
    checkpoint_dir: Path = Path("./checkpoints")


class Trainer:
    def __init__(
        self,
        model:      nn.Module,
        config:     TrainingConfig,
        train_loader: DataLoader,
        val_loader:   DataLoader,
    ) -> None:
        self.model        = model.to(config.device)
        self.config       = config
        self.train_loader = train_loader
        self.val_loader   = val_loader

        self.optimizer = AdamW(
            model.parameters(),
            lr=config.learning_rate,
            weight_decay=config.weight_decay,
        )
        self.scheduler = CosineAnnealingLR(
            self.optimizer,
            T_max=config.num_epochs,
        )
        self.best_val_loss = float('inf')
        self.scaler = torch.cuda.amp.GradScaler()  # Mixed precision

    def train(self) -> dict[str, list[float]]:
        history: dict[str, list[float]] = {"train_loss": [], "val_loss": []}

        for epoch in range(self.config.num_epochs):
            train_loss = self._train_epoch(epoch)
            val_loss   = self._validate_epoch(epoch)

            self.scheduler.step()

            history["train_loss"].append(train_loss)
            history["val_loss"].append(val_loss)

            logger.info(
                f"Epoch {epoch+1}/{self.config.num_epochs} | "
                f"Train: {train_loss:.4f} | Val: {val_loss:.4f}"
            )

            # Best model kaydet
            if val_loss < self.best_val_loss:
                self.best_val_loss = val_loss
                self._save_checkpoint(epoch, val_loss)

        return history

    def _train_epoch(self, epoch: int) -> float:
        self.model.train()
        total_loss = 0.0

        for batch_idx, batch in enumerate(self.train_loader):
            inputs = {k: v.to(self.config.device) for k, v in batch.items()}

            self.optimizer.zero_grad()

            # Mixed precision training
            with torch.cuda.amp.autocast():
                outputs = self.model(**inputs)
                loss    = outputs.loss

            self.scaler.scale(loss).backward()

            # Gradient clipping
            self.scaler.unscale_(self.optimizer)
            torch.nn.utils.clip_grad_norm_(
                self.model.parameters(),
                self.config.max_grad_norm,
            )

            self.scaler.step(self.optimizer)
            self.scaler.update()

            total_loss += loss.item()

        return total_loss / len(self.train_loader)

    def _validate_epoch(self, epoch: int) -> float:
        self.model.eval()
        total_loss = 0.0

        with torch.no_grad():
            for batch in self.val_loader:
                inputs  = {k: v.to(self.config.device) for k, v in batch.items()}
                outputs = self.model(**inputs)
                total_loss += outputs.loss.item()

        return total_loss / len(self.val_loader)

    def _save_checkpoint(self, epoch: int, val_loss: float) -> None:
        checkpoint_path = self.config.checkpoint_dir / f"best_model_epoch{epoch}.pt"
        checkpoint_path.parent.mkdir(parents=True, exist_ok=True)

        torch.save({
            "epoch":      epoch,
            "model_state_dict":     self.model.state_dict(),
            "optimizer_state_dict": self.optimizer.state_dict(),
            "val_loss":   val_loss,
            "config":     self.config,
        }, checkpoint_path)

        logger.info(f"Checkpoint saved: {checkpoint_path}")
```

---

## RAG (RETRIEVAL-AUGMENTED GENERATION)

### RAG Pipeline Mimarisi

```
Indexing Pipeline (offline):
  Documents → Chunking → Embedding → Vector Store

Query Pipeline (online):
  Query → Embedding → Vector Search → Context → LLM → Response
```

### RAG Implementasyonu (Python)

```python
from __future__ import annotations

from dataclasses import dataclass
from typing import Any

import numpy as np
from sentence_transformers import SentenceTransformer


@dataclass
class Document:
    id:       str
    content:  str
    metadata: dict[str, Any]
    embedding: np.ndarray | None = None


class RAGPipeline:
    """
    Basit RAG pipeline.
    Production'da: Pinecone, Weaviate, Qdrant, pgvector kullan.
    """

    def __init__(
        self,
        embedding_model: str = "sentence-transformers/all-MiniLM-L6-v2",
        top_k: int = 5,
    ) -> None:
        self.encoder  = SentenceTransformer(embedding_model)
        self.top_k    = top_k
        self.documents: list[Document] = []
        self.embeddings: np.ndarray | None = None

    def index(self, documents: list[Document]) -> None:
        """Dokümanları embed et ve index'e ekle."""
        texts = [doc.content for doc in documents]
        embeddings = self.encoder.encode(
            texts,
            batch_size=32,
            show_progress_bar=True,
            normalize_embeddings=True,  # Cosine similarity için
        )

        for doc, emb in zip(documents, embeddings):
            doc.embedding = emb

        self.documents = documents
        self.embeddings = embeddings

        print(f"Indexed {len(documents)} documents")

    def retrieve(self, query: str) -> list[Document]:
        """Query'ye en yakın dokümanları getir."""
        if self.embeddings is None:
            raise RuntimeError("Index is empty. Call index() first.")

        query_embedding = self.encoder.encode(
            [query],
            normalize_embeddings=True,
        )[0]

        # Cosine similarity (normalize edilmiş → dot product yeterli)
        scores = np.dot(self.embeddings, query_embedding)

        # Top-K seç
        top_indices = np.argsort(scores)[::-1][:self.top_k]

        return [self.documents[i] for i in top_indices]

    def generate(self, query: str, llm_client: Any) -> str:
        """Retrieve + Generate."""
        relevant_docs = self.retrieve(query)

        # Context oluştur
        context = "\n\n".join([
            f"[{i+1}] {doc.content}"
            for i, doc in enumerate(relevant_docs)
        ])

        # LLM'e gönder
        prompt = f"""Aşağıdaki bağlamı kullanarak soruyu yanıtla.
Bağlamda olmayan bilgileri uydurma.

Bağlam:
{context}

Soru: {query}

Yanıt:"""

        return llm_client.complete(prompt)
```

---

## MODEL SERVING

### FastAPI ile Model Serving

```python
from __future__ import annotations

import asyncio
from contextlib import asynccontextmanager
from typing import Any

import torch
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field


class PredictRequest(BaseModel):
    text: str = Field(..., min_length=1, max_length=10000)
    top_k: int = Field(default=5, ge=1, le=100)


class PredictResponse(BaseModel):
    predictions: list[dict[str, Any]]
    model_version: str
    latency_ms: float


# Global model (singleton)
_model: Any = None
_model_version: str = ""


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Model'i startup'ta yükle, shutdown'da temizle."""
    global _model, _model_version

    print("Loading model...")
    _model = load_model("./models/best_model.pt")
    _model_version = "1.0.0"
    print(f"Model loaded: v{_model_version}")

    yield  # App çalışıyor

    print("Unloading model...")
    del _model
    torch.cuda.empty_cache()


app = FastAPI(lifespan=lifespan)


@app.post("/predict", response_model=PredictResponse)
async def predict(request: PredictRequest) -> PredictResponse:
    if _model is None:
        raise HTTPException(status_code=503, detail="Model not loaded")

    import time
    start = time.perf_counter()

    # Inference (CPU-bound → thread pool'da çalıştır)
    loop = asyncio.get_event_loop()
    predictions = await loop.run_in_executor(
        None,  # Default thread pool
        lambda: _model.predict(request.text, top_k=request.top_k)
    )

    latency_ms = (time.perf_counter() - start) * 1000

    return PredictResponse(
        predictions=predictions,
        model_version=_model_version,
        latency_ms=round(latency_ms, 2),
    )


@app.get("/health")
async def health() -> dict[str, str]:
    return {
        "status": "healthy" if _model is not None else "loading",
        "model_version": _model_version,
    }
```

---

## ML GÜVENLİK

```
Model Güvenliği:
  ✅ Model dosyaları imzalanmalı (checksum)
  ✅ Adversarial input detection
  ✅ Input validation (type, range, length)
  ✅ Output sanitization (PII masking)
  ✅ Rate limiting (inference endpoint)
  ✅ Authentication (API key veya JWT)

Veri Güvenliği:
  ✅ Training data anonymization
  ✅ PII removal before training
  ✅ Data access logging
  ✅ Model inversion attack önleme
  ✅ Membership inference attack önleme

Privacy:
  ✅ Differential privacy (gerektiğinde)
  ✅ Federated learning (hassas veri için)
  ✅ Model output filtering (sensitive info)
```

---

## ML CHECKLIST

```
Data:
  [ ] Data validation var mı?
  [ ] Data versioning var mı? (DVC)
  [ ] PII temizlendi mi?
  [ ] Train/val/test split doğru mu?
  [ ] Data leakage yok mu?

Training:
  [ ] Seed sabit mi?
  [ ] Experiment tracking var mı?
  [ ] Checkpoint'ler kaydediliyor mu?
  [ ] Gradient clipping var mı?
  [ ] Mixed precision kullanılıyor mu?

Serving:
  [ ] Model versioning var mı?
  [ ] Health check endpoint var mı?
  [ ] Rate limiting var mı?
  [ ] Input validation var mı?
  [ ] Latency monitoring var mı?

Monitoring:
  [ ] Data drift detection var mı?
  [ ] Model performance monitoring var mı?
  [ ] Prediction logging var mı?
  [ ] Alerting var mı?
```

---

*ML/AI Patterns v7.0.0 — 2026-05-29 | Kiro IDE Native*
*PyTorch, RAG, FastAPI serving, MLOps, reproducibility*
*Kaynak: pytorch.org/docs, huggingface.co/docs, mlflow.org/docs*
