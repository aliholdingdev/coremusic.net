---
title: "Log Aggregation"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# Log Aggregation

## Genel Bakış

Log toplama, COREMUSIC platformunda tüm servislerden gelen logların merkezi olarak toplanması, işlenmesi ve saklanmasını sağlar. ELK Stack (Elasticsearch, Logstash, Kibana) ile güçlendirilen bu sistem, log analizi, arama ve raporlama imkanı sunar.

## ELK Stack Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                    LOG TOPLAMA MİMARİSİ                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Uygulama Servisleri ──→ Logstash ──→ Elasticsearch ──→ Kibana │
│       │                    │               │              │     │
│    Filebeat            Filter &         Index &        Query & │
│    (Collector)         Transform        Store          Visual  │
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │  Filebeat    │  │  Logstash    │  │  Kibana      │         │
│  │  - Container │  │  - Grok      │  │  - Dashboard │         │
│  │  - System    │  │  - GeoIP     │  │  - Alert     │         │
│  │  - App       │  │  - Enrich    │  │  - Discover  │         │
│  └──────────────┘  └──────────────┘  └──────────────┘         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Bileşen Konfigürasyonları

### Filebeat Config

```yaml
# filebeat.yml
filebeat.inputs:
  # Uygulama logları
  - type: container
    paths:
      - '/var/lib/docker/containers/*/*.log'
    processors:
      - add_docker_metadata:
          host: "unix:///var/run/docker.sock"
      - add_kubernetes_metadata:
          host: ${NODE_NAME}
          matchers:
            - logs_path:
                logs_path: "/var/log/containers/"
  
  # System logları
  - type: log
    enabled: true
    paths:
      - /var/log/syslog
      - /var/log/auth.log
    fields:
      type: syslog
    fields_under_root: true
  
  # Nginx access logları
  - type: log
    enabled: true
    paths:
      - /var/log/nginx/access.log
    fields:
      type: nginx-access
    fields_under_root: true
  
  # Uygulama JSON logları
  - type: log
    enabled: true
    paths:
      - /var/log/coremusic/*.json
    json.keys_under_root: true
    json.add_error_key: true
    json.overwrite_keys: true

output.logstash:
  hosts: ["logstash:5044"]
  bulk_max_size: 2048
  worker: 4

processors:
  - add_host_metadata:
      when.not.contains.tags: forwarded
  - add_cloud_metadata: ~
  - add_fields:
      target: ''
      fields:
        environment: production
        service: coremusic

logging.level: info
logging.to_files: true
logging.files:
  path: /var/log/filebeat
  name: filebeat
  keepfiles: 7
  permissions: 0644
```

### Logstash Config

```ruby
# logstash.conf
input {
  beats {
    port => 5044
  }
  
  kafka {
    bootstrap_servers => "kafka:9092"
    topics => ["coremusic-logs"]
    group_id => "logstash-consumer"
    codec => json
  }
}

filter {
  # Docker metadata
  if [container][name] =~ /coremusic/ {
    mutate {
      add_field => { "service" => "%{[container][name]}" }
    }
  }
  
  # Uygulama log filtreleme
  if [type] == "app" {
    grok {
      match => { "message" => "%{TIMESTAMP_ISO8601:timestamp} %{LOGLEVEL:level} %{GREEDYDATA:message}" }
    }
    
    date {
      match => [ "timestamp", "yyyy-MM-dd HH:mm:ss.SSS" ]
      target => "@timestamp"
    }
    
    mutate {
      remove_field => [ "timestamp" ]
    }
  }
  
  # Nginx log filtreleme
  if [type] == "nginx-access" {
    grok {
      match => { "message" => "%{IPORHOST:remote_ip} - %{DATA:user} \[%{HTTPDATE:access_time}\] \"%{WORD:method} %{DATA:request} HTTP/%{NUMBER:http_version}\" %{NUMBER:status} %{NUMBER:bytes_sent} \"%{DATA:referrer}\" \"%{DATA:user_agent}\"" }
    }
    
    date {
      match => [ "access_time", "dd/MMM/yyyy:HH:mm:ss Z" ]
      target => "@timestamp"
    }
    
    mutate {
      convert => {
        "status" => "integer"
        "bytes_sent" => "integer"
      }
    }
    
    # GeoIP enrichment
    geoip {
      source => "remote_ip"
      target => "geoip"
    }
    
    useragent {
      source => "user_agent"
      target => "user_agent_info"
    }
  }
  
  # Hata seviyesi normalizasyonu
  if [level] {
    mutate {
      uppercase => [ "level" ]
    }
    
    if [level] == "ERR" or [level] == "FATAL" {
      mutate {
        add_field => { "severity" => "critical" }
      }
    } else if [level] == "WARN" or [level] == "WARNING" {
      mutate {
        add_field => { "severity" => "high" }
      }
    }
  }
  
  # Hassas veri maskelenme
  mutate {
    gsub => [
      "password", "****",
      "credit_card", "****",
      "ssn", "***-**-****"
    ]
  }
}

output {
  elasticsearch {
    hosts => ["elasticsearch:9200"]
    index => "coremusic-logs-%{+YYYY.MM.dd}"
    user => "elastic"
    password => "${ELASTIC_PASSWORD}"
  }
  
  # Hata logları için ayrı output
  if [severity] == "critical" {
    kafka {
      bootstrap_servers => "kafka:9092"
      topic_id => "coremusic-critical-logs"
    }
  }
  
  # Debug için stdout
  if [level] == "DEBUG" {
    stdout {
      codec => rubydebug
    }
  }
}
```

### Elasticsearch Config

```yaml
# elasticsearch.yml
cluster.name: coremusic-logs
node.name: ${HOSTNAME}
path.data: /usr/share/elasticsearch/data
path.logs: /usr/share/elasticsearch/logs

network.host: 0.0.0.0
http.port: 9200

discovery.type: single-node

xpack.security.enabled: true
xpack.security.audit.enabled: true

# Index template
action.auto_create_index: true

# ILM (Index Lifecycle Management)
ilm.enabled: true
```

### Kibana Config

```yaml
# kibana.yml
server.port: 5601
server.host: "0.0.0.0"
server.name: "coremusic-kibana"

elasticsearch.hosts: ["http://elasticsearch:9200"]
elasticsearch.username: "kibana_system"
elasticsearch.password: "${KIBANA_PASSWORD}"

xpack.security.enabled: true
xpack.encryptedSavedObjects.encryptionKey: "${ENCRYPTION_KEY}"
xpack.security.encryptionKey: "${SECURITY_KEY}"
xpack.reporting.encryptionKey: "${REPORTING_KEY}"

# Default index pattern
kibana.defaultAppId: "discover"
```

## Index Lifecycle Management (ILM)

### ILM Policy

```json
{
  "policy": {
    "phases": {
      "hot": {
        "min_age": "0ms",
        "actions": {
          "rollover": {
            "max_primary_shard_size": "50gb",
            "max_age": "1d"
          },
          "set_priority": {
            "priority": 100
          }
        }
      },
      "warm": {
        "min_age": "7d",
        "actions": {
          "shrink": {
            "number_of_shards": 1
          },
          "forcemerge": {
            "max_num_segments": 1
          },
          "set_priority": {
            "priority": 50
          }
        }
      },
      "cold": {
        "min_age": "30d",
        "actions": {
          "set_priority": {
            "priority": 0
          }
        }
      },
      "delete": {
        "min_age": "90d",
        "actions": {
          "delete": {}
        }
      }
    }
  }
}
```

### Index Template

```json
{
  "index_patterns": ["coremusic-logs-*"],
  "template": {
    "settings": {
      "number_of_shards": 3,
      "number_of_replicas": 1,
      "index.lifecycle.name": "coremusic-logs-policy",
      "index.lifecycle.rollover_alias": "coremusic-logs"
    },
    "mappings": {
      "properties": {
        "@timestamp": { "type": "date" },
        "message": { "type": "text" },
        "level": { "type": "keyword" },
        "service": { "type": "keyword" },
        "host": { "type": "keyword" },
        "container": {
          "properties": {
            "name": { "type": "keyword" },
            "id": { "type": "keyword" }
          }
        },
        "request_id": { "type": "keyword" },
        "user_id": { "type": "keyword" },
        "exception": {
          "properties": {
            "type": { "type": "keyword" },
            "message": { "type": "text" },
            "stacktrace": { "type": "text" }
          }
        }
      }
    }
  }
}
```

## Log Shipping

### Fluentd Config

```xml
<!-- fluentd.conf -->
<source>
  @type forward
  port 24224
  bind 0.0.0.0
</source>

<filter docker.**>
  @type parser
  key_name log
  reserve_data true
  remove_key_name_field true
  <parse>
    @type json
  </parse>
</filter>

<filter docker.coremusic-**>
  @type record_transformer
  <record>
    service_name ${record["container"]["name"].split("_").last}
    environment production
  </record>
</filter>

<match docker.coremusic-**>
  @type elasticsearch
  host elasticsearch
  port 9200
  index_name coremusic-logs
  type_name _doc
  
  <buffer>
    @type file
    path /var/log/fluentd/buffers
    flush_mode interval
    flush_interval 10s
    chunk_limit_size 2M
    queue_limit_length 32
    retry_max_interval 30s
    retry_forever true
  </buffer>
</match>

<match **>
  @type elasticsearch
  host elasticsearch
  port 9200
  index_name general-logs
  type_name _doc
  
  <buffer>
    @type file
    path /var/log/fluentd/buffers/general
    flush_mode interval
    flush_interval 30s
  </buffer>
</match>
```

## Kibana Dashboard'ları

### Discover Query Examples

```json
{
  "query": {
    "bool": {
      "must": [
        { "match": { "level": "ERROR" } },
        { "range": { "@timestamp": { "gte": "now-1h" } } }
      ],
      "filter": [
        { "term": { "service": "coremusic-api" } }
      ]
    }
  },
  "sort": [
    { "@timestamp": { "order": "desc" } }
  ]
}
```

### Dashboard Panel

```json
{
  "title": "Error Logs Over Time",
  "type": "line",
  "params": {
    "type": "line",
    "grid": { "categoryLines": false }
  },
  "data": {
    "aggs": [
      {
        "id": "date_histogram",
        "enabled": true,
        "schema": "metric",
        "params": {
          "field": "@timestamp",
          "interval": "auto",
          "min_doc_count": 1,
          "extended_bounds": {}
        }
      }
    ],
    "searchSource": {
      "query": {
        "query": "level: ERROR",
        "language": "kuery"
      }
    }
  }
}
```

## Log Arama API

### Python Elasticsearch Client

```python
from elasticsearch import Elasticsearch
from datetime import datetime, timedelta

class LogSearch:
    def __init__(self, hosts: list):
        self.es = Elasticsearch(hosts)
    
    def search_logs(
        self,
        query: str,
        service: str = None,
        level: str = None,
        start_time: datetime = None,
        end_time: datetime = None,
        size: int = 100
    ):
        if start_time is None:
            start_time = datetime.now() - timedelta(hours=1)
        if end_time is None:
            end_time = datetime.now()
        
        must = [
            {"query_string": {"query": query}}
        ]
        
        if service:
            must.append({"term": {"service": service}})
        if level:
            must.append({"term": {"level": level}})
        
        body = {
            "query": {
                "bool": {
                    "must": must,
                    "filter": [
                        {"range": {"@timestamp": {
                            "gte": start_time.isoformat(),
                            "lte": end_time.isoformat()
                        }}}
                    ]
                }
            },
            "sort": [{"@timestamp": {"order": "desc"}}],
            "size": size
        }
        
        return self.es.search(index="coremusic-logs-*", body=body)
    
    def get_error_stats(self, service: str, hours: int = 24):
        body = {
            "size": 0,
            "query": {
                "bool": {
                    "must": [
                        {"term": {"level": "ERROR"}},
                        {"range": {"@timestamp": {"gte": f"now-{hours}h"}}}
                    ],
                    "filter": [
                        {"term": {"service": service}}
                    ]
                }
            },
            "aggs": {
                "errors_over_time": {
                    "date_histogram": {
                        "field": "@timestamp",
                        "fixed_interval": "1h"
                    }
                },
                "top_errors": {
                    "terms": {
                        "field": "message.keyword",
                        "size": 10
                    }
                }
            }
        }
        
        return self.es.search(index="coremusic-logs-*", body=body)

# Kullanım
log_search = LogSearch(["http://elasticsearch:9200"])
results = log_search.search_logs(
    query="timeout OR connection refused",
    service="coremusic-api",
    level="ERROR"
)
```

## Saklama Politikası

| Veri Türü | Hot | Warm | Cold | Delete |
|-----------|-----|------|------|--------|
| Uygulama logları | 7 gün | 30 gün | 60 gün | 90 gün |
| Erişim logları | 7 gün | 14 gün | 30 gün | 60 gün |
| Hata logları | 30 gün | 90 gün | 180 gün | 365 gün |
| Audit logları | 30 gün | 365 gün | - | - |

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| Elasticsearch | 8.10+ | Log saklama ve arama |
| Kibana | 8.10+ | Log görselleştirme |
| Logstash | 8.10+ | Log işleme |
| Filebeat | 8.10+ | Log toplama |
| Fluentd | 1.16+ | Alternatif log toplama |

## Durum: Implementasyon

**Aşama**: Hazırlık
**Öncelik**: Yüksek
**Tahmini Süre**: 1 hafta
**Notlar**: Tüm servislerin JSON formatında log üretmesi gerekmektedir.
