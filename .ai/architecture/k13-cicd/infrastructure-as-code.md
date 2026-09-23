---
title: "Infrastructure as Code"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# Infrastructure as Code

## Genel Bakış

COREMUSIC Infrastructure as Code (IaC) katmanı, tüm altyapı kaynaklarının versiyon kontrolünde ve otomatize edilebilir şekilde yönetimini sağlar. Terraform ile cloud resources, Ansible ile server configuration, ve Kustomize/Helm ile Kubernetes configuration management uygulanır. Immutable infrastructure prensibi ile drift prevention sağlanır.

## Pipeline Akışı

```
Code Change → Terraform Plan → Review → Terraform Apply → Ansible Provisioning → Kubernetes Config → Validation → State Lock
```

## Teknik Detaylar

### Terraform Module Yapısı

```hcl
# infrastructure/terraform/main.tf
terraform {
  required_version = ">= 1.6.0"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
    kubernetes = {
      source  = "hashicorp/kubernetes"
      version = "~> 2.25"
    }
  }

  backend "s3" {
    bucket         = "coremusic-terraform-state"
    key            = "production/terraform.tfstate"
    region         = "eu-west-1"
    dynamodb_table = "terraform-locks"
    encrypt        = true
  }
}

# EKS Cluster Module
module "eks" {
  source = "./modules/eks"

  cluster_name    = "coremusic-production"
  cluster_version = "1.29"
  region          = "eu-west-1"

  vpc_id     = module.vpc.vpc_id
  subnet_ids = module.vpc.private_subnet_ids

  node_groups = {
    general = {
      instance_types = ["t3.large"]
      min_size       = 3
      max_size       = 10
      desired_size   = 3
    }

    compute = {
      instance_types = ["c5.xlarge"]
      min_size       = 0
      max_size       = 5
      desired_size   = 0
      labels = {
        workload = "compute"
      }
    }
  }
}

# RDS Module
module "rds" {
  source = "./modules/rds"

  identifier = "coremusic-db"
  engine     = "mysql"
  engine_version = "8.0"

  instance_class = "db.r6g.large"
  allocated_storage = 100

  db_name  = "coremusic"
  username = "coremusic"
  password = random_password.db_password.result

  vpc_id     = module.vpc.vpc_id
  subnet_ids = module.vpc.database_subnet_ids

  backup_retention_period = 7
  multi_az               = true
  storage_encrypted      = true
}

# ElastiCache Module
module "redis" {
  source = "./modules/elasticache"

  cluster_id      = "coremusic-redis"
  engine          = "redis"
  engine_version  = "7.0"
  node_type       = "cache.r6g.large"
  num_cache_nodes = 3

  vpc_id     = module.vpc.vpc_id
  subnet_ids = module.vpc.private_subnet_ids
}
```

### VPC Configuration

```hcl
# infrastructure/terraform/modules/vpc/main.tf
module "vpc" {
  source = "terraform-aws-modules/vpc/aws"

  name = "coremusic-vpc"
  cidr = "10.0.0.0/16"

  azs             = ["eu-west-1a", "eu-west-1b", "eu-west-1c"]
  private_subnets = ["10.0.1.0/24", "10.0.2.0/24", "10.0.3.0/24"]
  public_subnets  = ["10.0.101.0/24", "10.0.102.0/24", "10.0.103.0/24"]
  database_subnets = ["10.0.201.0/24", "10.0.202.0/24", "10.0.203.0/24"]

  enable_nat_gateway     = true
  single_nat_gateway     = false
  one_nat_gateway_per_az = true

  enable_dns_hostnames = true
  enable_dns_support   = true

  tags = {
    Environment = "production"
    Project     = "coremusic"
    ManagedBy   = "terraform"
  }
}
```

### Ansible Configuration

```yaml
# infrastructure/ansible/playbooks/site.yml
---
- name: Configure COREMUSIC servers
  hosts: all
  become: true

  vars:
    app_name: coremusic
    app_version: "{{ lookup('env', 'APP_VERSION') }}"
    php_version: "8.3"
    nginx_worker_processes: "auto"
    nginx_worker_connections: 1024

  roles:
    - common
    - docker
    - nginx
    - monitoring
    - security

# infrastructure/ansible/roles/docker/tasks/main.yml
---
- name: Install Docker
  apt:
    name:
      - docker.io
      - docker-compose-plugin
    state: present
    update_cache: yes

- name: Add user to docker group
  user:
    name: deploy
    groups: docker
    append: yes

- name: Configure Docker daemon
  template:
    src: daemon.json.j2
    dest: /etc/docker/daemon.json
  notify: restart docker

- name: Login to container registry
  community.docker.docker_login:
    registry: ghcr.io
    username: "{{ github_actor }}"
    password: "{{ github_token }}"
```

### Kubernetes Configuration

```yaml
# infrastructure/k8s/base/kustomization.yaml
apiVersion: kustomize.config.k8s.io/v1beta1
kind: Kustomization

resources:
  - namespace.yaml
  - deployment.yaml
  - service.yaml
  - ingress.yaml
  - hpa.yaml
  - configmap.yaml
  - networkpolicy.yaml

commonLabels:
  app: coremusic
  managed-by: kustomize

# infrastructure/k8s/overlays/production/kustomization.yaml
apiVersion: kustomize.config.k8s.io/v1beta1
kind: Kustomization

resources:
  - ../../base

patchesStrategicMerge:
  - deployment-patch.yaml
  - hpa-patch.yaml

replicas:
  - name: coremusic-app
    count: 3
```

### State Management

```hcl
# State locking with DynamoDB
resource "aws_dynamodb_table" "terraform_locks" {
  name         = "terraform-locks"
  billing_mode = "PAY_PER_REQUEST"
  hash_key     = "LockID"

  attribute {
    name = "LockID"
    type = "S"
  }
}

# State backup
resource "aws_s3_bucket" "terraform_state" {
  bucket = "coremusic-terraform-state"

  versioning {
    enabled = true
  }

  server_side_encryption_configuration {
    rule {
      apply_server_side_encryption_by_default {
        sse_algorithm = "aws:kms"
      }
    }
  }

  lifecycle {
    prevent_destroy = true
  }
}
```

### Drift Detection

```yaml
# .github/workflows/terraform-drift.yml
name: Terraform Drift Detection

on:
  schedule:
    - cron: '0 6 * * *'  # Daily at 6am

jobs:
  drift-detection:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Setup Terraform
        uses: hashicorp/setup-terraform@v3
        with:
          terraform_version: "1.6.0"

      - name: Terraform Init
        run: terraform init
        working-directory: infrastructure/terraform

      - name: Terraform Plan
        id: plan
        run: terraform plan -detailed-exitcode -out=tfplan
        working-directory: infrastructure/terraform
        continue-on-error: true

      - name: Notify drift detected
        if: steps.plan.outputs.exitcode == 2
        uses: slackapi/slack-github-action@v1
        with:
          payload: |
            {
              "text": "⚠️ Infrastructure drift detected!",
              "blocks": [
                {
                  "type": "section",
                  "text": {
                    "type": "mrkdwn",
                    "text": "*Terraform Drift Detected*\nCheck the workflow for details: ${{ github.server_url }}/${{ github.repository }}/actions/runs/${{ github.run_id }}"
                  }
                }
              ]
            }
        env:
          SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK }}
```

### Environment Variables

```hcl
# infrastructure/terraform/variables.tf
variable "environment" {
  description = "Environment name"
  type        = string
  default     = "production"

  validation {
    condition     = contains(["development", "staging", "production"], var.environment)
    error_message = "Environment must be development, staging, or production."
  }
}

variable "region" {
  description = "AWS region"
  type        = string
  default     = "eu-west-1"
}

variable "cluster_version" {
  description = "Kubernetes cluster version"
  type        = string
  default     = "1.29"
}
```

## Konfigürasyon

### CI/CD for IaC

```yaml
# .github/workflows/terraform.yml
name: Terraform

on:
  push:
    branches: [main]
    paths:
      - 'infrastructure/**'
  pull_request:
    branches: [main]
    paths:
      - 'infrastructure/**'

jobs:
  terraform:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Setup Terraform
        uses: hashicorp/setup-terraform@v3

      - name: Terraform Init
        run: terraform init

      - name: Terraform Format
        run: terraform fmt -check

      - name: Terraform Validate
        run: terraform validate

      - name: Terraform Plan
        run: terraform plan -out=tfplan
        if: github.event_name == 'pull_request'

      - name: Terraform Apply
        run: terraform apply -auto-approve
        if: github.ref == 'refs/heads/main' && github.event_name == 'push'
```

## Bağımlılıklar

- `terraform`: Infrastructure provisioning
- `ansible`: Configuration management
- `kustomize`: Kubernetes configuration
- `helm`: Package management
- `aws`: Cloud provider

## Durum: Implementasyon

| Bileşen              | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| Terraform Modules    | Hazır       | 2026-09-20      |
| Ansible Roles        | Hazır       | 2026-09-20      |
| Kustomize Bases      | Hazır       | 2026-09-20      |
| State Management     | Hazır       | 2026-09-20      |
| Drift Detection      | Hazır       | 2026-09-20      |
