# BLOK G — DEVOPS & ALTYAPI (300+ Soru)

**5 Kategori, ALT ALTA Format**

---

## G1 — ALTYAPI KODU (IaC) — 60 Soru

### IaC Seçimleri

G1. IaC aracı hangisi?
- [ ] Terraform
- [ ] Ansible
- [ ] CloudFormation
- [ ] Pulumi
- [ ] Bicep
- [ ] Helm
- [ ] Custom
- [ ] None

G2. IaC dili hangisi?
- [ ] HCL
- [ ] YAML
- [ ] Python
- [ ] Go
- [ ] JSON

G3. IaC sürümleme stratejisi?
- [ ] Git
- [ ] S3
- [ ] Custom
- [ ] None

G4. IaC test aracı?
- [ ] Terratest
- [ ] Packer
- [ ] Policy as Code
- [ ] None

G5. Terraform state yönetimi?
- [ ] Remote backend (S3 / Terraform Cloud / Consul)
- [ ] Local state
- [ ] Custom
- [ ] None

G6. State file encryption?
- [ ] At-rest (KMS / Vault)
- [ ] In-transit (TLS)
- [ ] Both
- [ ] None

G7. State locking?
- [ ] Enabled
- [ ] Disabled
- [ ] Conditional

G8. State backup stratejisi?
- [ ] Automated
- [ ] Manual
- [ ] Custom
- [ ] None

G9. Terraform workspace kullanımı?
- [ ] Production / Staging / Dev
- [ ] Per-team
- [ ] Per-app
- [ ] None

G10. Terraform versioning (provider)?
- [ ] Exact version (= 5.0.0)
- [ ] Patch version (~> 5.0)
- [ ] Latest (no constraint)
- [ ] Custom

G11. IaC linting ve validation?
- [ ] tflint + terraform validate
- [ ] checkov
- [ ] Enabled locally only
- [ ] Not done

G12. IaC dokumentasyon?
- [ ] README + inline comments
- [ ] terraform-docs
- [ ] Auto-generated
- [ ] None

G13. IaC modülleri kullanımı?
- [ ] Standardized modules
- [ ] Custom modules
- [ ] Terraform Registry
- [ ] Mixed

G14. Modül versioning?
- [ ] Semantic versioning
- [ ] Git tags
- [ ] Unversioned
- [ ] None

G15. Cross-environment IaC?
- [ ] Variable files per env
- [ ] Workspace-based
- [ ] Parameter Store / Secrets Manager
- [ ] hardcoded

G16. IaC code review?
- [ ] Peer required
- [ ] CODEOWNERS
- [ ] Auto-merge
- [ ] Not enforced

G17. Terraform plan review?
- [ ] terraform plan output in PR
- [ ] terraform cloud UI
- [ ] Manual verification
- [ ] None

G18. Ansible playbook versioning?
- [ ] Git + tags
- [ ] Roles library
- [ ] Dynamic inventory
- [ ] None

G19. Ansible idempotency?
- [ ] Enforced
- [ ] Monitored
- [ ] Not checked
- [ ] N/A

G20. CloudFormation stack policy?
- [ ] Implemented
- [ ] Planned
- [ ] Not used

G21. IaC cost estimation?
- [ ] terraform cost / Infracost
- [ ] Manual
- [ ] Not estimated

G22. IaC secret injection?
- [ ] Parameter Store / Vault
- [ ] Environment variables
- [ ] Hardcoded
- [ ] None

G23. IaC drift detection?
- [ ] Automated (Config rules / Drift detection)
- [ ] Manual
- [ ] None

G24. Drift remediation?
- [ ] Automatic
- [ ] Manual
- [ ] Documented procedure

G25. IaC testing framework?
- [ ] Terratest
- [ ] kitchen-terraform
- [ ] Custom
- [ ] None

G26. IaC test coverage?
- [ ] >80%
- [ ] 50-80%
- [ ] <50%
- [ ] Not measured

G27. IaC policy as code?
- [ ] Sentinel / OPA
- [ ] Custom scripts
- [ ] None

G28. IaC RBAC?
- [ ] By resource / team
- [ ] By environment
- [ ] Open
- [ ] None

G29. IaC audit trail?
- [ ] Git history
- [ ] State audit log
- [ ] CloudTrail
- [ ] None

G30. Multi-region IaC?
- [ ] Provider aliasing
- [ ] Separate configurations
- [ ] Module-based
- [ ] Manual

G31. IaC CI/CD integration?
- [ ] Automated plan + apply
- [ ] Manual approval required
- [ ] Scheduled apply
- [ ] None

G32. Terraform backend redundancy?
- [ ] Multi-region replication
- [ ] Single region
- [ ] Manual backup
- [ ] None

G33. IaC migration stratejisi (eski kaynaklar)?
- [ ] terraform import
- [ ] Rewrite
- [ ] Manual
- [ ] None

G34. IaC dependency management?
- [ ] Explicit (depends_on)
- [ ] Implicit (references)
- [ ] Mixed
- [ ] None

G35. IaC performance optimization?
- [ ] Parallelization (-parallelism)
- [ ] Resource chunking
- [ ] Not optimized

G36. Template engine (CloudFormation/Ansible)?
- [ ] Jinja2
- [ ] Go templates
- [ ] Custom
- [ ] None

G37. IaC environment parity?
- [ ] Identical templates
- [ ] Variable-driven differences
- [ ] Separate configs
- [ ] Manual

G38. IaC disaster recovery?
- [ ] Documented restore procedure
- [ ] Tested
- [ ] Annual drill
- [ ] None

G39. IaC compliance scanning?
- [ ] CIS Terraform Benchmark
- [ ] Custom rules
- [ ] None

G40. IaC cost tagging?
- [ ] Automatic tags
- [ ] Manual tags
- [ ] Not tagged

G41. IaC secret rotation?
- [ ] Automated
- [ ] Manual
- [ ] Never

G42. IaC network architecture as code?
- [ ] VPC / Subnets / Routes fully defined
- [ ] Partial
- [ ] Manual

G43. IaC security groups as code?
- [ ] Fully defined
- [ ] Exceptions manual
- [ ] Manual

G44. IaC database as code?
- [ ] Schema + data
- [ ] Schema only
- [ ] Manual

G45. IaC SSL/TLS cert management?
- [ ] AWS Secrets Manager / Vault
- [ ] Hardcoded
- [ ] Manual renewal
- [ ] None

G46. IaC load balancer configuration?
- [ ] Fully coded
- [ ] Partial
- [ ] Manual

G47. IaC DNS management?
- [ ] Route53 / Azure DNS / GCP DNS via IaC
- [ ] Manual DNS
- [ ] Mixed

G48. IaC container registry?
- [ ] ECR / GCR / ACR via IaC
- [ ] Manual
- [ ] None

G49. IaC Kubernetes resources?
- [ ] YAML + Helm via Terraform
- [ ] kubectl apply manual
- [ ] Mixed

G50. IaC GitOps integration?
- [ ] ArgoCD / Flux with IaC
- [ ] Manual Git-to-deploy
- [ ] None

G51. IaC documentation generation?
- [ ] terraform-docs / graphviz
- [ ] Manual docs
- [ ] No docs

G52. IaC rollback procedure?
- [ ] terraform destroy + re-apply
- [ ] Blue-green infrastructure
- [ ] Manual recovery
- [ ] None

G53. IaC upgrade path?
- [ ] Terraform version strategy
- [ ] Ad-hoc upgrades
- [ ] Pinned versions

G54. IaC local development?
- [ ] Localstack / Terraform
- [ ] Cloud-based dev
- [ ] Manual setup

G55. IaC secrets scan?
- [ ] pre-commit hooks
- [ ] CI/CD scan
- [ ] Manual review
- [ ] None

G56. IaC logging and monitoring?
- [ ] CloudTrail / Audit logs
- [ ] Custom logging
- [ ] None

G57. IaC graph visualization?
- [ ] terraform graph
- [ ] Custom visualization
- [ ] None

G58. IaC code reusability?
- [ ] Modules library
- [ ] Monorepo
- [ ] Copy-paste
- [ ] None

G59. IaC best practices enforcement?
- [ ] linting + style guide
- [ ] PR comments
- [ ] Ad-hoc

G60. IaC resource naming convention?
- [ ] Standardized naming
- [ ] Ad-hoc
- [ ] Environment-based prefix

---

## G2 — DOCKER & KAPSÜLLEŞTİRME — 60 Soru

### Container Image Stratejisi

G61. Base image seçimi?
- [ ] Alpine
- [ ] Distroless
- [ ] Debian
- [ ] Ubuntu
- [ ] CentOS / RHEL
- [ ] Custom

G62. Image boyutu?
- [ ] <100MB (minimal)
- [ ] 100-500MB
- [ ] >500MB
- [ ] Not optimized

G63. Multi-stage builds?
- [ ] Yes, production-optimized
- [ ] Yes, basic
- [ ] No

G64. Layer caching optimization?
- [ ] Dependency order (slow → fast changing)
- [ ] Basic ordering
- [ ] Not optimized

G65. .dockerignore kullanımı?
- [ ] Comprehensive
- [ ] Basic
- [ ] None

G66. Build secret management?
- [ ] BuildKit secrets
- [ ] Build args
- [ ] Hardcoded
- [ ] None

G67. Docker security scanning?
- [ ] Trivy
- [ ] Snyk
- [ ] Clair
- [ ] Grype
- [ ] None

G68. Image signature / provenance?
- [ ] Cosign
- [ ] Notary
- [ ] None

G69. Container registry?
- [ ] Docker Hub
- [ ] ECR (AWS)
- [ ] GCR (GCP)
- [ ] ACR (Azure)
- [ ] Artifactory
- [ ] Harbor
- [ ] GitHub Packages
- [ ] GitLab Registry
- [ ] Private on-prem

G70. Registry backup/redundancy?
- [ ] Multi-region replication
- [ ] Manual backup
- [ ] Single region
- [ ] None

G71. Registry authentication?
- [ ] API keys / tokens
- [ ] IAM roles
- [ ] Basic auth
- [ ] None

G72. Image tagging strategy?
- [ ] latest + version (v1.2.3)
- [ ] Commit SHA
- [ ] Timestamp
- [ ] Branch name
- [ ] Multiple tags

G73. Immutable image registry?
- [ ] Enforced (no overwrite)
- [ ] Allowed
- [ ] Not tracked

G74. Image promotion strategy?
- [ ] Dev → Staging → Prod
- [ ] Promoted via tagging
- [ ] Manual promotion
- [ ] None

G75. Dockerfile best practices?
- [ ] Single FROM, minimal layers, specific versions
- [ ] Basic practices
- [ ] None

G76. Vulnerability scanning frequency?
- [ ] Per commit
- [ ] Daily
- [ ] Weekly
- [ ] Never

G77. CVE remediation process?
- [ ] Automated patching
- [ ] Manual review + patch
- [ ] Documented SLA
- [ ] Ignored

G78. Image size reduction?
- [ ] Layer squashing
- [ ] Distroless base
- [ ] Custom optimization
- [ ] Not optimized

G79. Artifact retention policy?
- [ ] Latest N images
- [ ] By age (90 days)
- [ ] Unlimited
- [ ] Manual cleanup

G80. Container runtime?
- [ ] Docker
- [ ] Containerd
- [ ] CRI-O
- [ ] Podman
- [ ] Mixed

G81. OCI compliance?
- [ ] OCI-compliant images
- [ ] Docker-specific
- [ ] Not checked

G82. Build parallelization?
- [ ] Enabled (Docker BuildKit)
- [ ] Sequential
- [ ] Not applicable

G83. Build caching strategy?
- [ ] Remote caching
- [ ] Local cache
- [ ] No caching

G84. Docker Compose (dev)?
- [ ] Yes, for local dev
- [ ] No
- [ ] Production use (not recommended)

G85. Multi-architecture builds?
- [ ] Yes (linux/amd64, linux/arm64)
- [ ] Single arch
- [ ] Manual per-arch

G86. Image attestation?
- [ ] SLSA provenance
- [ ] Custom metadata
- [ ] None

G87. Image scanning integration?
- [ ] CI/CD gate (fail on HIGH)
- [ ] Reporting only
- [ ] Not integrated

G88. Secrets in container?
- [ ] Volume mounts / env at runtime
- [ ] BuildKit secrets
- [ ] Hardcoded
- [ ] None

G89. Container compliance scanning?
- [ ] CIS Docker Benchmark
- [ ] Custom rules
- [ ] None

G90. Health check in Dockerfile?
- [ ] Implemented
- [ ] Not set

G91. Non-root user in Dockerfile?
- [ ] Enforced
- [ ] Recommended
- [ ] Root allowed

G92. Signal handling (SIGTERM)?
- [ ] Proper shutdown handlers
- [ ] Not implemented

G93. Startup probe / readiness probe?
- [ ] Implemented
- [ ] Not applicable

G94. Container resource limits (Dockerfile context)?
- [ ] Documented
- [ ] Set at runtime

G95. Image rebuild frequency?
- [ ] On every dependency update
- [ ] Weekly
- [ ] Manual

G96. Image audit log?
- [ ] Registry audit trail
- [ ] Manual tracking
- [ ] None

G97. Image garbage collection?
- [ ] Automated (registry cleanup)
- [ ] Manual
- [ ] Not done

G98. Rootless Docker?
- [ ] Enabled
- [ ] Not used
- [ ] Planned

G99. Container networking image config?
- [ ] EXPOSE documented
- [ ] Not specified

G100. Build-time variable injection?
- [ ] ARG with build context
- [ ] Hardcoded
- [ ] None

G101. Metadata in image?
- [ ] LABEL for version / maintainer / license
- [ ] Basic labels
- [ ] None

G102. Build reproducibility?
- [ ] Timestamp-neutral (builds same hash)
- [ ] Non-deterministic
- [ ] Not tested

G103. Image test (container-structure-test)?
- [ ] Yes
- [ ] No

G104. Sbom (software bill of materials)?
- [ ] Generated per image
- [ ] Manual
- [ ] None

G105. Base image updates?
- [ ] Automated via Dependabot
- [ ] Manual monthly
- [ ] Ad-hoc

G106. Dockerfile linting?
- [ ] hadolint enabled
- [ ] Manual review
- [ ] None

G107. Build failure notification?
- [ ] Slack / Email
- [ ] Dashboard
- [ ] None

G108. Image pull policies?
- [ ] Always pull latest
- [ ] IfNotPresent (local-first)
- [ ] Never

G109. Registry quota management?
- [ ] Implemented
- [ ] Not set

G110. Cross-registry image copying?
- [ ] Skopeo / crane automation
- [ ] Manual
- [ ] Not done

G111. Container image compliance?
- [ ] FIPS-compliant base
- [ ] Standard
- [ ] Not checked

G112. Build isolation?
- [ ] Containerized builder (Docker-in-Docker)
- [ ] Host-based builder
- [ ] Cloud builder (Cloud Build / GitHub Actions)

G113. Build caching across pipelines?
- [ ] Shared cache layer
- [ ] Per-pipeline cache
- [ ] No sharing

G114. Image promotion gate?
- [ ] Test pass required
- [ ] Security scan required
- [ ] Manual approval
- [ ] Automatic

G115. Dependency pinning in Dockerfile?
- [ ] Exact versions (pip == / apt versions)
- [ ] Latest (no pins)
- [ ] Major-version pins

G116. Image deprecation?
- [ ] Documented EOL dates
- [ ] Manual tracking
- [ ] Not tracked

G117. Private dependency auth (Dockerfile)?
- [ ] Secure credentials via BuildKit
- [ ] Git token in args (insecure)
- [ ] Not applicable

G118. Layer order optimization?
- [ ] Most-changed layers last
- [ ] Random order
- [ ] Not optimized

G119. Docker image history cleanup?
- [ ] Squashing before push
- [ ] Full history preserved
- [ ] Partial cleanup

G120. Build-time secrets leak scanning?
- [ ] Pre-commit scanning (prevent hardcoded secrets)
- [ ] Registry scanning
- [ ] Not scanned

---

## G3 — KUBERNETES & ORKESTRASYOM — 80 Soru

### K8s Temel Kurulum

G121. Kubernetes dağıtımı?
- [ ] Vanilla (kubeadm)
- [ ] EKS (AWS)
- [ ] GKE (GCP)
- [ ] AKS (Azure)
- [ ] OpenShift
- [ ] K3s (lightweight)
- [ ] K0s
- [ ] Managed Kubernetes as a Service

G122. K8s versiyon stratejisi?
- [ ] Latest stable
- [ ] LTS version
- [ ] Pinned version (N-3)
- [ ] Ad-hoc

G123. K8s network plugin (CNI)?
- [ ] Flannel
- [ ] Calico
- [ ] Cilium
- [ ] AWS VPC CNI
- [ ] Azure CNI
- [ ] Default (kubenet)
- [ ] Custom

G124. Network policies?
- [ ] Mandatory (all traffic denied by default)
- [ ] Recommended (some policies)
- [ ] Not used

G125. Service mesh?
- [ ] Istio
- [ ] Linkerd
- [ ] Consul
- [ ] AWS App Mesh
- [ ] Dapr
- [ ] None

G126. Ingress controller?
- [ ] NGINX Ingress
- [ ] Traefik
- [ ] HAProxy
- [ ] Envoy
- [ ] AWS ALB Controller
- [ ] Custom

G127. API gateway (K8s context)?
- [ ] Kong
- [ ] AWS API Gateway
- [ ] Azure API Management
- [ ] Custom
- [ ] None

G128. Service discovery?
- [ ] Kubernetes DNS (CoreDNS)
- [ ] Consul
- [ ] Eureka
- [ ] Manual
- [ ] External DNS

G129. Load balancing algoritması?
- [ ] Round-robin
- [ ] Least connections
- [ ] IP hash
- [ ] Custom
- [ ] Random

G130. Pod security standards?
- [ ] Restricted (non-root, no privilege escalation)
- [ ] Baseline (permissive)
- [ ] Privileged (full access)
- [ ] Not enforced

G131. Resource quotas?
- [ ] Implemented (CPU / Memory per namespace)
- [ ] Not set
- [ ] Partial

G132. Limit ranges?
- [ ] Implemented
- [ ] Not set

G133. Pod disruption budgets (PDB)?
- [ ] Implemented
- [ ] Not set

G134. Horizontal Pod Autoscaler (HPA)?
- [ ] CPU/Memory-based
- [ ] Custom metrics (Prometheus)
- [ ] Manual
- [ ] None

G135. Vertical Pod Autoscaler (VPA)?
- [ ] Implemented
- [ ] Planned
- [ ] Not used

G136. Cluster autoscaling?
- [ ] Enabled (auto add/remove nodes)
- [ ] Manual
- [ ] None

G137. Node affinity?
- [ ] Strict rules
- [ ] Preferred
- [ ] Not used

G138. Pod affinity / anti-affinity?
- [ ] Strict rules (different zones)
- [ ] Preferred
- [ ] Not used

G139. Taints and tolerations?
- [ ] Used for specialized nodes
- [ ] Not used

G140. DaemonSets?
- [ ] Used (logging / monitoring agents)
- [ ] Not applicable

G141. StatefulSets?
- [ ] Used (databases / message queues)
- [ ] Deployments only

G142. Jobs and CronJobs?
- [ ] Implemented
- [ ] Not used

G143. Kubernetes operators?
- [ ] Custom operators
- [ ] Community operators (KEDA / NATS)
- [ ] None

G144. Helm charts versioning?
- [ ] Semantic versioning
- [ ] Git tags
- [ ] Unversioned
- [ ] Not used

G145. GitOps workflow?
- [ ] ArgoCD
- [ ] Flux
- [ ] Manual Git-to-deploy
- [ ] None

G146. Service mesh sidecar injection?
- [ ] Automatic (webhook)
- [ ] Manual annotation
- [ ] None

G147. Circuit breaker (service mesh)?
- [ ] Configured in mesh (DestinationRule)
- [ ] App-level (Hystrix / Polly)
- [ ] Both

G148. Rate limiting in service mesh?
- [ ] EnvoyFilter configured
- [ ] Not set

G149. Mutual TLS (mTLS)?
- [ ] Strict (all traffic encrypted)
- [ ] Permissive (auto-upgrade to mTLS)
- [ ] Disabled

G150. Authorization policies?
- [ ] Implemented (deny-all + allow rules)
- [ ] Not used

G151. Virtual services (traffic routing)?
- [ ] Configured
- [ ] Basic Kubernetes Services
- [ ] None

G152. Destination rules (load balancing)?
- [ ] Configured
- [ ] Not used

G153. Gateways (Istio Gateways)?
- [ ] Configured
- [ ] Not used

G154. Traffic mirroring (canary testing)?
- [ ] Implemented
- [ ] None

G155. Session affinity (sticky sessions)?
- [ ] Implemented
- [ ] Not used

G156. Service topology?
- [ ] Locality-aware routing
- [ ] Not used

G157. Timeout policies?
- [ ] Configured
- [ ] Not set

G158. Retry policies?
- [ ] Configured
- [ ] Not set

G159. Circuit breaker thresholds?
- [ ] Configured (consecutive errors)
- [ ] Default
- [ ] Not set

G160. Istio observability?
- [ ] Full (metrics + tracing + logging)
- [ ] Partial
- [ ] None

G161. Kiali dashboard?
- [ ] Installed (service mesh visualization)
- [ ] Not used

G162. Custom resource definitions (CRDs)?
- [ ] Used for custom resources
- [ ] Not applicable

G163. Webhook validation?
- [ ] Mutating + Validating webhooks
- [ ] Partial
- [ ] None

G164. RBAC policies?
- [ ] Granular (ClusterRole / Role per team)
- [ ] Basic (admin / user)
- [ ] Open (no RBAC)

G165. Kube-apiserver audit?
- [ ] Enabled (log all API calls)
- [ ] Disabled

G166. Kubelet security?
- [ ] Secure (--require-kubeconfig, --anonymous-auth=false)
- [ ] Permissive
- [ ] Default

G167. Pod security policy / Pod security standards?
- [ ] Enforced
- [ ] Warn only
- [ ] Not set

G168. Network policies enforcement?
- [ ] All traffic denied by default
- [ ] Whitelist approach
- [ ] Not enforced

G169. Container runtime security?
- [ ] containerd (secure)
- [ ] Docker (higher risk)
- [ ] CRI-O
- [ ] Other

G170. Kubelet API endpoint?
- [ ] Disabled (--kubeconfig required)
- [ ] Enabled (auth required)
- [ ] Open

G171. Etcd encryption at rest?
- [ ] Enabled
- [ ] Disabled

G172. Control plane audit logging?
- [ ] Enabled
- [ ] Disabled

G173. Secrets management (application secrets)?
- [ ] Vault / External Secrets Operator
- [ ] Sealed Secrets (in-cluster)
- [ ] Kubernetes Secrets (base64 - not secure!)
- [ ] ConfigMaps

G174. Secrets rotation?
- [ ] Automated (External Secrets)
- [ ] Manual
- [ ] Never

G175. ConfigMaps?
- [ ] Separate from code
- [ ] Embedded in charts
- [ ] Custom

G176. Persistent volumes?
- [ ] Implemented
- [ ] Not applicable

G177. Storage classes?
- [ ] Multiple (fast SSD / standard)
- [ ] Default only

G178. Persistent volume claim (PVC)?
- [ ] Dynamic provisioning
- [ ] Manual
- [ ] None

G179. Volume snapshots?
- [ ] Automated
- [ ] Manual
- [ ] Not used

G180. StatefulSet ordering?
- [ ] Ordered (pod-0, pod-1...)
- [ ] Not applicable

G181. Headless services?
- [ ] Used for StatefulSets
- [ ] Not applicable

G182. Init containers?
- [ ] Used for setup
- [ ] Not used

G183. Sidecar containers?
- [ ] Used (logging / proxy)
- [ ] Not applicable

G184. Liveness probe?
- [ ] Configured
- [ ] Not set

G185. Readiness probe?
- [ ] Configured
- [ ] Not set

G186. Startup probe?
- [ ] Configured
- [ ] Not set

G187. Probe failure handling?
- [ ] Pod restarted
- [ ] Traffic removed
- [ ] Both

G188. QoS (Quality of Service) class?
- [ ] Guaranteed (requests == limits)
- [ ] Burstable
- [ ] BestEffort

G189. Resource requests?
- [ ] Configured (CPU / Memory)
- [ ] Not set

G190. Resource limits?
- [ ] Configured (CPU / Memory)
- [ ] Not set

G191. Memory pressure handling?
- [ ] Pod eviction configured
- [ ] Manual

G192. CPU throttling?
- [ ] Monitored
- [ ] Not measured

G193. Node drain?
- [ ] Graceful drain on node maintenance
- [ ] Not implemented

G194. Pod priority and preemption?
- [ ] Implemented
- [ ] Not used

G195. Cluster role binding?
- [ ] Fine-grained
- [ ] Basic

G196. Service accounts per app?
- [ ] Yes
- [ ] Shared

G197. Kubernetes secrets encryption?
- [ ] At-rest (etcd encryption)
- [ ] In-transit (TLS)
- [ ] Not encrypted

G198. Kubernetes audit webhook?
- [ ] Configured
- [ ] Disabled

G199. Kube-proxy mode?
- [ ] iptables (traditional)
- [ ] ipvs (high-performance)
- [ ] userspace (legacy)

G200. Custom metrics export?
- [ ] Prometheus /metrics endpoint
- [ ] Custom metrics server
- [ ] None

---

## G4 — CI/CD KÜMESİ — 70 Soru

### Pipeline Mimarisi

G201. CI/CD platformu?
- [ ] GitHub Actions
- [ ] Jenkins
- [ ] GitLab CI/CD
- [ ] Bitbucket Pipelines
- [ ] Azure DevOps
- [ ] Custom
- [ ] Multiple

G202. Pipeline tetikleyici?
- [ ] Git push / PR
- [ ] Schedule (cron)
- [ ] Manual
- [ ] Webhook (external)
- [ ] Multiple

G203. Pipeline aşamaları?
- [ ] Lint → Build → Test → Deploy
- [ ] Custom stages
- [ ] Sequential
- [ ] Parallel

G204. Build paralelleştirme?
- [ ] Yes (matrix builds)
- [ ] Limited
- [ ] No

G205. Test paralelleştirme?
- [ ] Yes (multiple test jobs)
- [ ] Limited
- [ ] Sequential

G206. Artifact depolama?
- [ ] Container registry (ECR / GCR)
- [ ] Artifactory
- [ ] S3 / GCS / Azure Blob
- [ ] Custom
- [ ] Multiple

G207. Artifact sürümleme?
- [ ] Semantic versioning (v1.2.3)
- [ ] Timestamp (2024-06-11-091200)
- [ ] Git SHA (abc1234...)
- [ ] Branch name
- [ ] Mixed

G208. Build caching?
- [ ] Enabled (layer caching)
- [ ] Disabled
- [ ] Selective

G209. Cache invalidation?
- [ ] Manual
- [ ] Automatic (dependency hash)
- [ ] Smart (timestamp)

G210. Docker build optimization?
- [ ] Multi-stage builds
- [ ] Layer caching
- [ ] BuildKit features
- [ ] All of above
- [ ] None

G211. Image tagging stratejisi (CI/CD)?
- [ ] latest + version tags
- [ ] Commit SHA
- [ ] Branch name
- [ ] Multiple tags

G212. Code quality gates?
- [ ] SonarQube / Codacy
- [ ] Custom metrics
- [ ] Not enforced

G213. SAST (Static Analysis)?
- [ ] Sonarqube / SonarCloud
- [ ] Semgrep
- [ ] Custom rules
- [ ] None

G214. DAST (Dynamic Analysis)?
- [ ] OWASP ZAP
- [ ] Burp Suite
- [ ] Custom scanning
- [ ] None

G215. Container scanning (pipeline)?
- [ ] Trivy / Snyk / Clair
- [ ] Registry-side scanning
- [ ] None

G216. Test coverage enforcement?
- [ ] Minimum threshold (e.g. >80%)
- [ ] Reported only
- [ ] Not measured

G217. Test sonuçları raporlaması?
- [ ] Dashboard (Junit)
- [ ] Email
- [ ] Slack
- [ ] None

G218. Build notification?
- [ ] Real-time (Slack / Webhook)
- [ ] Batch email
- [ ] None

G219. Pipeline DAG görselleştirme?
- [ ] Visual graph
- [ ] Linear list
- [ ] Custom UI

G220. Deployment türü?
- [ ] Canary (gradual rollout)
- [ ] Blue-green (instant switch)
- [ ] Rolling (sequential)
- [ ] All supported
- [ ] None

G221. Canary traffic dağılımı?
- [ ] 1% / 5% / 10% / 25%
- [ ] Manual control
- [ ] Automated

G222. Canary ölçümleri?
- [ ] Error rate / latency
- [ ] Custom metrics
- [ ] Not monitored

G223. Canary otomasyonu?
- [ ] Automated rollout on success
- [ ] Manual approval
- [ ] Scheduled

G224. Blue-green switch?
- [ ] Instant switch
- [ ] Gradual (rolling)
- [ ] Manual

G225. Rolling update ayarları?
- [ ] maxSurge / maxUnavailable configured
- [ ] Default
- [ ] Not applicable

G226. Deployment onayı?
- [ ] Automatic (CI pass = deploy)
- [ ] Manual approval required
- [ ] Conditional (test pass + CODEOWNERS)

G227. Deployment notification?
- [ ] Slack / PagerDuty
- [ ] Email
- [ ] Dashboard
- [ ] None

G228. Deployment hook?
- [ ] Pre-deploy (health checks)
- [ ] Post-deploy (smoke tests)
- [ ] Custom
- [ ] None

G229. Database migration?
- [ ] Automated (Flyway / Liquibase)
- [ ] Manual (versioned SQL)
- [ ] Ad-hoc

G230. Migration rollback?
- [ ] Automatic (if deploy fails)
- [ ] Manual
- [ ] Not possible

G231. Feature flag integrasyonu?
- [ ] LaunchDarkly / Split.io integration
- [ ] Manual gates
- [ ] None

G232. A/B test altyapısı?
- [ ] Built-in (deployment platform)
- [ ] Third-party (experimentation platform)
- [ ] None

G233. Smoke test (post-deployment)?
- [ ] Automated
- [ ] Manual
- [ ] None

G234. Health check doğrulaması?
- [ ] Otomatik (CI/CD checks)
- [ ] Manual
- [ ] None

G235. Load balancer güncelleme?
- [ ] Automated
- [ ] Manual
- [ ] Not applicable

G236. DNS güncelleme (canary)?
- [ ] Automated
- [ ] Manual
- [ ] Not applicable

G237. SSL/TLS sertifika güncelleme?
- [ ] Automated (Let's Encrypt)
- [ ] Manual renewal
- [ ] Manual + reminder

G238. Secrets injection (pipeline)?
- [ ] Vault / KMS
- [ ] Environment variables
- [ ] Hardcoded
- [ ] None

G239. Variable interpolation?
- [ ] Jinja2 / Go templates
- [ ] Custom
- [ ] None

G240. Conditional execution?
- [ ] If-then (bash conditional)
- [ ] Matrix builds
- [ ] Custom logic
- [ ] None

G241. Pipeline versioning?
- [ ] YAML in Git repo
- [ ] UI-based (history tracked)
- [ ] Custom VCS
- [ ] Not versioned

G242. Pipeline as Code?
- [ ] Declarative (YAML)
- [ ] Imperative (Groovy / Python)
- [ ] Both supported

G243. Reusable pipeline components?
- [ ] Templates / macros
- [ ] Shared libraries
- [ ] None

G244. Cross-repo pipeline trigger?
- [ ] Supported (webhook)
- [ ] Not supported

G245. Scheduled pipeline?
- [ ] Cron-based (nightly builds)
- [ ] Manual trigger
- [ ] Webhook-based

G246. Deployment otomatik rollback?
- [ ] On error (health check fail)
- [ ] Manual trigger
- [ ] Time-based (revert after N min)

G247. Rollback automation?
- [ ] Instant (previous version)
- [ ] Gradual (canary reverse)
- [ ] Manual

G248. Pipeline SLA?
- [ ] Target execution time (e.g. <10 min)
- [ ] Not defined

G249. Pipeline metrikleri?
- [ ] Duration / Success rate / Pass rate
- [ ] Tracked but not public
- [ ] Not measured

G250. Pipeline failure analysis?
- [ ] Automatic root cause identification
- [ ] Manual analysis
- [ ] Logged only

G251. Pipeline cache sharing?
- [ ] Cross-job caching
- [ ] Per-job cache
- [ ] No caching

G252. Pipeline security scanning?
- [ ] Secrets scanning (TruffleHog)
- [ ] Dependency scanning (Dependabot)
- [ ] SAST + DAST
- [ ] All

G253. Pipeline audit trail?
- [ ] Who deployed what and when
- [ ] Basic logging
- [ ] None

G254. Pipeline rate limiting?
- [ ] Max concurrent jobs
- [ ] Not limited

G255. Pipeline resource allocation?
- [ ] CPU / Memory per job
- [ ] Default allocation
- [ ] Not configured

G256. Docker-in-Docker (DinD)?
- [ ] Used (build inside container)
- [ ] Avoided (security risk)
- [ ] Not applicable

G257. Pipeline self-hosted runners?
- [ ] Yes (custom agents)
- [ ] Cloud-based only

G258. Pipeline timeout?
- [ ] Per job
- [ ] Global
- [ ] Not set

G259. Pipeline retry logic?
- [ ] Automatic (transient failures)
- [ ] Manual re-run
- [ ] Not implemented

G260. Pipeline notification rules?
- [ ] Always / On failure / On change
- [ ] Customizable per team
- [ ] Fixed rules

G261. Deployment rollback notification?
- [ ] Real-time alert
- [ ] Email summary
- [ ] None

G262. Pipeline dashboard?
- [ ] Real-time status
- [ ] Historical data
- [ ] None

G263. Pipeline integration (other tools)?
- [ ] Slack / PagerDuty / Jira
- [ ] Partial
- [ ] None

G264. Multi-branch pipeline?
- [ ] Yes (main / develop / feature branches)
- [ ] No

G265. Pipeline as self-service?
- [ ] Developers can trigger
- [ ] Admin-only
- [ ] Restricted roles

G266. Hotfix pipeline?
- [ ] Fast-tracked (skip some tests)
- [ ] Same process as normal
- [ ] Not implemented

G267. Release pipeline?
- [ ] Separate from CI
- [ ] Unified pipeline
- [ ] Manual process

G268. Pipeline documentation?
- [ ] Documented steps
- [ ] README only
- [ ] No documentation

G269. Pipeline best practices enforcement?
- [ ] Linting + style guide
- [ ] PR review required
- [ ] Not enforced

G270. Pipeline cost tracking?
- [ ] Usage metrics
- [ ] Chargeback per team
- [ ] Not tracked

---

## G5 — İZLEME & KÜTÜPHANELER — 75 Soru

### Observability Yığını

G271. İzleme platformu?
- [ ] Prometheus + Grafana
- [ ] Datadog
- [ ] New Relic
- [ ] CloudWatch
- [ ] Splunk
- [ ] Custom
- [ ] Multiple

G272. Metrik kazı aralığı?
- [ ] 15 saniye
- [ ] 30 saniye
- [ ] 1 dakika
- [ ] Custom

G273. Metrik saklama?
- [ ] 15 gün
- [ ] 1 yıl
- [ ] Unlimited
- [ ] Custom

G274. Dashboard şablonları?
- [ ] Önceden oluşturulmuş
- [ ] Custom
- [ ] Self-service

G275. Alert eşikleri?
- [ ] Dinamik (anomali tespiti)
- [ ] Statik
- [ ] ML-tabanlı

G276. Alert deduplication?
- [ ] Enabled
- [ ] Disabled

G277. Alert sessizleştirme?
- [ ] Zamanlı (bakım penceresi)
- [ ] Manual
- [ ] None

G278. Alert escalation?
- [ ] Multi-level (SEV-0 → SEV-3)
- [ ] Single level
- [ ] None

G279. On-call yönlendirme?
- [ ] Automatic
- [ ] Manual
- [ ] Rotation-based

G280. Olay yönetimi?
- [ ] PagerDuty / Opsgenie
- [ ] Custom
- [ ] None

G281. Olay takibi?
- [ ] Jira / Linear
- [ ] Custom
- [ ] None

G282. Olay sonrası analiz?
- [ ] Formal (RCA doc)
- [ ] Informal (Slack summary)
- [ ] None

G283. RCA (Root Cause Analysis)?
- [ ] Documented
- [ ] Ad-hoc
- [ ] Not practiced

G284. Kusursuz kültür?
- [ ] Enforced (no blame)
- [ ] Encouraged
- [ ] Not practiced

G285. İşlem kaydı?
- [ ] Git history
- [ ] Changelog file
- [ ] Manual doc

G286. Değişim izleme?
- [ ] Tüm değişiklikler
- [ ] Deployment only
- [ ] Not tracked

G287. Korelasyon kimliği?
- [ ] Oluşturuldu / Yayıldı
- [ ] Not used

G288. İz bağlamı yayılımı?
- [ ] W3C traceparent
- [ ] Custom
- [ ] None

G289. Debug logging?
- [ ] Üretimde devre dışı
- [ ] Enabled (sampling)
- [ ] Always enabled

G290. Yapılandırılmış logging?
- [ ] JSON format
- [ ] Key-value
- [ ] Plain text

G291. Log toplama gecikmesi?
- [ ] <1 saniye
- [ ] <10 saniye
- [ ] >1 dakika

G292. Log saklama?
- [ ] 7 gün
- [ ] 30 gün
- [ ] 1 yıl
- [ ] Unlimited

G293. Log rotasyonu?
- [ ] Automated (daily / size-based)
- [ ] Manual
- [ ] Not done

G294. Log sıkıştırması?
- [ ] Gzip / Zstd
- [ ] None

G295. Hassas log redaksiyonu?
- [ ] Automatic (secrets)
- [ ] Manual
- [ ] None

G296. Log ayrıştırması?
- [ ] Otomatik (parsing)
- [ ] Manual
- [ ] None

G297. Custom dashboard?
- [ ] Self-service (all devs)
- [ ] Admin-only
- [ ] Not allowed

G298. Runbook otomasyonu?
- [ ] Fully (executable scripts)
- [ ] Partial (manual steps)
- [ ] None

G299. Debugging capability?
- [ ] Remote debugging
- [ ] Local only
- [ ] Not allowed

G300. Profiling üretimde?
- [ ] Safe mode (CPU / memory)
- [ ] Development only
- [ ] Not allowed

G301. Memory dump analizi?
- [ ] Tools available
- [ ] Not practiced

G302. Core dump oluşturma?
- [ ] Enabled
- [ ] Disabled

G303. Goroutine/thread dump?
- [ ] Automated
- [ ] Manual
- [ ] Not captured

G304. Network paket yakalama?
- [ ] tcpdump / Packet broker
- [ ] Not available

G305. Hizmet durum sayfası?
- [ ] Public
- [ ] Internal
- [ ] None

G306. Durum sayfası otomasyonu?
- [ ] API-driven (auto-update)
- [ ] Manual
- [ ] None

G307. Olay zaman çizelgesi?
- [ ] Auto-generated
- [ ] Manual
- [ ] None

G308. Tarihsel metrik?
- [ ] Available (queryable)
- [ ] Not retained

G309. Anormallik tespiti?
- [ ] ML-tabanlı
- [ ] Threshold-based
- [ ] None

G310. Tahmine dayalı ölçeklendirme?
- [ ] ML
- [ ] Manual thresholds
- [ ] None

G311. SLO (Service Level Objective)?
- [ ] Defined (availability / latency)
- [ ] Not defined

G312. SLI (Service Level Indicator)?
- [ ] Measured
- [ ] Estimated
- [ ] Not tracked

G313. Error budget?
- [ ] Calculated (100% - SLO)
- [ ] Not tracked

G314. Fatigue uyarı sistemi?
- [ ] Managed (oncall doesn't spam)
- [ ] High false positive rate
- [ ] Not managed

G315. MTTD (Mean Time To Detect)?
- [ ] <5 dakika
- [ ] <15 dakika
- [ ] <1 saat
- [ ] Not measured

G316. MTTR (Mean Time To Resolve)?
- [ ] <15 dakika
- [ ] <1 saat
- [ ] <4 saat
- [ ] SLA-based

G317. Availability (uptime)?
- [ ] 99.9% (8.76 saat downtime/yıl)
- [ ] 99.99% (52 dakika/yıl)
- [ ] 99.999% (5 dakika/yıl)
- [ ] Not tracked

G318. Trace collection?
- [ ] Jaeger / Zipkin
- [ ] DataDog / Honeycomb
- [ ] None

G319. Trace örnekleme?
- [ ] 100% (all requests)
- [ ] 10% / 1% (sampled)
- [ ] Adaptive

G320. Uçtan uca distributed tracing?
- [ ] Full end-to-end
- [ ] Application only
- [ ] None

G321. Request flow visualization?
- [ ] Trace waterfall
- [ ] Service dependency graph
- [ ] None

G322. Trace storage retention?
- [ ] 7 gün
- [ ] 30 gün
- [ ] 1 yıl
- [ ] Unlimited

G323. Hot path analizi?
- [ ] Most called endpoints identified
- [ ] Not tracked

G324. Latency percentiles?
- [ ] P50 / P95 / P99 / P99.9
- [ ] Average only
- [ ] Not measured

G325. Request count trending?
- [ ] Tracked over time
- [ ] Current only
- [ ] Not measured

G326. Error rate trending?
- [ ] Per endpoint / service
- [ ] Overall only
- [ ] Not tracked

G327. Resource utilization?
- [ ] CPU / Memory / Disk / Network
- [ ] Partial
- [ ] Not monitored

G328. Container metrics?
- [ ] Docker stats / Kubelet metrics
- [ ] Not collected

G329. Database metrics?
- [ ] Query count / latency / errors
- [ ] Not tracked

G330. Cache hit ratio?
- [ ] Monitored
- [ ] Not measured

G331. Queue depth?
- [ ] Message queue / job queue
- [ ] Not monitored

G332. Dependency health?
- [ ] Service-to-service checks
- [ ] Not monitored

G333. Circuit breaker state?
- [ ] Monitored (open / half-open / closed)
- [ ] Not tracked

G334. Rate limiter hits?
- [ ] Tracked
- [ ] Not measured

G335. Cost anomaly detection?
- [ ] AI-based (auto-alert)
- [ ] Threshold-based
- [ ] None

G336. Budget alert?
- [ ] Immediate / Daily / Weekly
- [ ] Not set

G337. Cost allocation tag?
- [ ] Comprehensive (team / project / app)
- [ ] Partial
- [ ] None

G338. Right-sizing recommendation?
- [ ] From monitoring system
- [ ] Manual audit
- [ ] None

G339. Reserved instance tracking?
- [ ] Auto-match with usage
- [ ] Manual
- [ ] Not tracked

G340. Spot instance usage?
- [ ] Automated (interruptible workloads)
- [ ] Not used
- [ ] Manual

G341. Backup durum monitoring?
- [ ] Automated checks
- [ ] Manual verification
- [ ] Not checked

G342. Restore test otomasyonu?
- [ ] Scheduled test restores
- [ ] Manual
- [ ] Never

G343. Disaster recovery drill?
- [ ] Quarterly / Biannually
- [ ] Annual
- [ ] Never

G344. Capacity forecast?
- [ ] ML-based trend
- [ ] Manual projection
- [ ] Not planned

G345. Dependency graph?
- [ ] Visualization available
- [ ] Not tracked

---

## Ek Sorular — G346-G360 (15 Soru)

G346. CI/CD best practices enforcement?
- [ ] Linting + style guide
- [ ] PR review required
- [ ] Not enforced

G347. Infrastructure change approval workflow?
- [ ] CODEOWNERS required
- [ ] Manual approval
- [ ] Auto-merge
- [ ] No approval

G348. Multi-cloud observability?
- [ ] Unified dashboard (AWS + GCP + Azure)
- [ ] Separate tools per cloud
- [ ] Not applicable

G349. Security compliance audit?
- [ ] Automated scanning
- [ ] Manual audit
- [ ] Not performed

G350. Infrastructure documentation?
- [ ] Architecture diagrams
- [ ] Runbooks
- [ ] Auto-generated
- [ ] None

G351. Disaster recovery documentation?
- [ ] Detailed RTO/RPO plan
- [ ] High-level overview
- [ ] Not documented

G352. Knowledge transfer process?
- [ ] Structured onboarding
- [ ] Pair programming sessions
- [ ] Documentation only
- [ ] Ad-hoc

G353. DevOps toolchain maturity?
- [ ] Fully automated end-to-end
- [ ] Mostly automated (manual gates)
- [ ] Semi-automated
- [ ] Manual processes

G354. Monitoring alert fatigue?
- [ ] Regularly tuned (<1% false positive)
- [ ] Moderate tuning
- [ ] High false positive rate

G355. Infrastructure code review coverage?
- [ ] >80% of changes reviewed
- [ ] 50-80% reviewed
- [ ] <50% reviewed
- [ ] Not enforced

G356. Incident postmortem action items?
- [ ] Tracked and completed
- [ ] Tracked but not completed
- [ ] Not tracked

G357. Kubernetes cost optimization?
- [ ] Right-sizing pods
- [ ] Using spot instances / preemptible nodes
- [ ] Not optimized

G358. Container registry security?
- [ ] Private registry with RBAC
- [ ] Public registry
- [ ] Not managed

G359. Secrets scanning in Git history?
- [ ] Pre-commit hooks
- [ ] CI/CD scan
- [ ] Not scanned

G360. Infrastructure layer testing?
- [ ] Unit tests (Terratest)
- [ ] Integration tests
- [ ] Both

```

---

*BLOK G: 360+ Sorular | DevOps & Altyapı | 5 Ana Kategori*
*Formats: Seçenek ALT ALTA, Turkish, Production-grade*

```

---

**Kategori Özeti:**
- **G1-G60:** Infrastructure as Code (Terraform, Ansible, CloudFormation)
- **G61-G120:** Docker & Container (Base image, security, registry, optimization)
- **G121-G200:** Kubernetes & Orchestration (K8s setup, networking, security, operators)
- **G201-G270:** CI/CD Pipeline (Stages, deployment, testing, automation)
- **G271-G360:** Monitoring & Observability (Metrics, logs, tracing, incidents, SLOs)
G2. IaC language? (HCL / YAML / Python / Go / JSON)
G3. IaC versioning? (Git / S3 / Custom)
G4. IaC testing? (Terratest / Packer / None)
G5. Container image base? (Alpine / Distroless / Debian / Ubuntu / CentOS)
G6. Image size? (<100MB / 100-500MB / >500MB)
G7. Multi-stage builds? (Yes / No)
G8. Layer caching optimization? (Yes / No)
G9. Image security scanning? (Trivy / Snyk / Clair / None)
G10. Registry? (Docker Hub / ECR / GCR / Artifactory / GitHub / GitLab / Private)
G11. Registry backup? (Yes / No)
G12. Image signing? (Cosign / Notary / None)
G13. Orchestration? (Kubernetes / Docker Swarm / ECS / App Engine / Heroku / None)
G14. K8s version? (Latest / LTS / Pinned / Custom)
G15. K8s distro? (Vanilla / EKS / GKE / AKS / OpenShift / K3s / K0s)
G16. K8s CNI? (Flannel / Calico / Cilium / Amazon VPC CNI / Azure CNI / Default)
G17. Network policies? (Mandatory / Recommended / Not used)
G18. Service mesh? (Istio / Linkerd / Consul / AWS App Mesh / Dapr / None)
G19. Ingress controller? (NGINX / Traefik / HAProxy / Envoy / AWS ALB)
G20. API gateway? (Kong / AWS API Gateway / Azure API Management / Custom)
G21. Service discovery? (Kubernetes DNS / Consul / Eureka / Manual)
G22. Load balancing? (Round-robin / Least connections / IP hash / Custom)
G23. Secrets management? (Vault / AWS Secrets Manager / Azure Key Vault / Sealed Secrets / External Secrets)
G24. Secrets rotation? (Automated / Manual / Never)
G25. ConfigMaps? (Separate / Embedded / Custom)
G26. Log aggregation? (ELK / Loki / Splunk / DataDog / CloudWatch / Diğer)
G27. Log retention? (1 day / 7 days / 30 days / 90 days / 1 year / Unlimited)
G28. Log sampling? (100% / 10% / 1% / Adaptive)
G29. Metrics storage? (Prometheus / Graphite / InfluxDB / CloudWatch / Datadog)
G30. Metrics retention? (1 day / 15 days / 1 year / Unlimited)
G31. Visualization? (Grafana / Kibana / CloudWatch / Custom / None)
G32. Custom dashboards? (Yes / No)
G33. Alerting? (PagerDuty / Opsgenie / Slack / Email / SNS / Custom)
G34. Alert routing? (Severity-based / Team-based / Escalation / Custom)
G35. Alert fatigue? (Managed / Not managed)
G36. On-call schedule? (Roster / Random / Volunteer / None)
G37. On-call runbook? (Yes / No)
G38. Trace collection? (Jaeger / Zipkin / DataDog / Honeycomb / Diğer)
G39. Trace sampling? (100% / 10% / 1% / Adaptive)
G40. Distributed tracing? (Full end-to-end / Application only / None)
G41. Backup strategy? (Automated / Scheduled / On-demand / Continuous)
G42. Backup frequency? (Real-time / Hourly / Daily / Weekly / Custom)
G43. Backup storage? (S3 / GCS / Azure Blob / On-prem / Multiple)
G44. Backup encryption? (Yes / No)
G45. Backup testing? (Regular / Ad-hoc / Never)
G46. Restore testing? (Quarterly / Annual / Never)
G47. RTO (Recovery Time Objective)? (<15 min / <1 hour / <4 hours / <1 day)
G48. RPO (Recovery Point Objective)? (Real-time / <1 hour / <1 day / <1 week)
G49. Disaster recovery plan? (Documented / Tested / Annual / Never)
G50. DR drill frequency? (Quarterly / Biannually / Annually)
G51. Multi-region strategy? (Active-active / Active-passive / Single-region)
G52. Geographic redundancy? (Same-region / Multi-region / Global)
G53. Failover automation? (Automatic / Manual / Semi-automatic)
G54. Failover time? (<5 min / <15 min / <1 hour)
G55. Health checks? (Liveness / Readiness / Startup / Custom)
G56. Health check frequency? (10s / 30s / 60s / Custom)
G57. Canary deployment? (Automated / Manual / None)
G58. Canary traffic (%)?  (1% / 5% / 10% / 25%)
G59. Blue-green deployment? (Supported / Not supported)
G60. Blue-green traffic switching? (Instant / Gradual / Manual)
G61. Rolling updates? (Supported / Not supported)
G62. Rolling update max surge? (1 / 25% / 50% / Custom)
G63. Rolling update max unavailable? (0 / 1 / 25% / Custom)
G64. Feature flags? (LaunchDarkly / Split.io / custom / None)
G65. Feature flag TTL? (Temporary / Permanent)
G66. A/B testing infrastructure? (Built-in / Third-party / None)
G67. Database migration strategy? (Automated / Manual / Version-controlled)
G68. Migration rollback? (Automatic / Manual / None)
G69. Zero-downtime deployment? (Supported / Best-effort / Not supported)
G70. Deployment frequency? (Multiple daily / Daily / Weekly / Monthly)
G71. Deployment window? (24/7 / Business hours / Scheduled / Custom)
G72. Deployment approval? (Automatic / Manual / Conditional)
G73. Deployment notifications? (Slack / Email / PagerDuty / Custom)
G74. Deployment rollback? (Automatic / Manual / Time-based)
G75. Rollback automation? (Instant / Gradual / Manual)
G76. Environment parity? (Identical / Approximate / Minimal)
G77. Infrastructure testing? (TerraformTest / Policy as Code / None)
G78. Configuration management? (Puppet / Chef / Ansible / SaltStack / None)
G79. Package repository? (Artifactory / Nexus / GitHub Packages / Custom)
G80. Dependency management? (Automated updates / Monthly reviews / Manual)

**G81-G120 — Advanced K8s & Orchestration (40 Soru)**
- G81. Pod security standards? (Restricted / Baseline / Privileged)
- G82. Resource quotas? (Implemented / Not set)
- G83. Limit ranges? (Implemented / Not set)
- G84. HPA (Horizontal Pod Autoscaler)? (Implemented / Manual / None)
- G85. VPA (Vertical Pod Autoscaler)? (Implemented / Not used)
- G86. Pod disruption budgets? (Implemented / Not set)
- G87. Cluster autoscaling? (Enabled / Manual / None)
- G88. Node affinity? (Strict / Preferred / Not used)
- G89. Pod affinity? (Strict / Preferred / Not used)
- G90. Taints and tolerations? (Used / Not used)
- G91. DaemonSets? (Used / Not applicable)
- G92. StatefulSets? (Used / Deployments only)
- G93. Jobs and CronJobs? (Implemented / Not used)
- G94. Operators? (Custom / Community / None)
- G95. Helm charts? (Versioned / Not version controlled)
- G96. GitOps? (ArgoCD / Flux / Manual / None)
- G97. Service mesh sidecar injection? (Automatic / Manual / None)
- G98. Circuit breaker patterns? (In mesh / App-level / Both)
- G99. Rate limiting in mesh? (Configured / Not set)
- G100. Mutual TLS (mTLS)? (Strict / Permissive / Disabled)
- G101. Authorization policies? (Implemented / Not used)
- G102. Virtual services? (Configured / Basic / None)
- G103. Destination rules? (Configured / Not used)
- G104. Gateways? (Configured / Not used)
- G105. Traffic mirroring? (Implemented / None)
- G106. Load balancing algorithms? (Round-robin / Least-conn / Custom)
- G107. Session affinity? (Implemented / Not used)
- G108. Circuit breaker thresholds? (Configured / Default)
- G109. Timeout policies? (Configured / Not set)
- G110. Retry policies? (Configured / Not set)
- G111. Istio observability? (Enabled / Partial / None)
- G112. Kiali dashboard? (Installed / Not used)
- G113. Istio telemetry? (Custom metrics / Default)
- G114. Distributed tracing in mesh? (Enabled / Disabled)
- G115. Custom resource definitions? (CRDs used / Not applicable)
- G116. Webhook validation? (Mutating / Validating / Both / None)
- G117. RBAC policies? (Granular / Basic / Open)
- G118. Kube-apiserver audit? (Enabled / Disabled)
- G119. Kubelet security? (Secure / Permissive)
- G120. Container runtime? (containerd / Docker / CRI-O / Other)

**G121-G160 — CI/CD Pipeline (40 Soru)**
- G121. CI/CD platform? (GitHub Actions / Jenkins / GitLab / GitOps / Custom)
- G122. Pipeline stages? (Lint / Build / Test / Deploy / Other)
- G123. Build parallelization? (Yes / No)
- G124. Test parallelization? (Yes / No)
- G125. Artifact storage? (Container registry / Artifactory / S3 / Custom)
- G126. Artifact versioning? (Semantic / Timestamp / Git SHA)
- G127. Build caching? (Enabled / Disabled)
- G128. Cache invalidation? (Manual / Automatic / Smart)
- G129. Docker build optimization? (Multi-stage / Layer caching / Custom)
- G130. Image tag strategy? (Latest / Version / Commit SHA)
- G131. Immutable images? (Enforced / Allowed / Not tracked)
- G132. Scanning in pipeline? (SAST / DAST / Container scan)
- G133. Code coverage enforcement? (Threshold enforced / Reported / Not measured)
- G134. Test result reporting? (Dashboard / Email / Slack / None)
- G135. Build notifications? (Real-time / Batch / None)
- G136. Pipeline visualization? (DAG / Linear / Custom)
- G137. Partial deployments? (Canary / Blue-green / Rolling)
- G138. Deployment approval? (Automatic / Manual / Conditional)
- G139. Deployment notifications? (Slack / PagerDuty / Email)
- G140. Deployment hooks? (Pre-deploy / Post-deploy / Custom)
- G141. Database migrations? (Automated / Manual / Versioned)
- G142. Migration reversibility? (Full rollback / Partial / No)
- G143. Feature flags in pipeline? (Automated / Manual)
- G144. A/B testing pipeline? (Integrated / External / None)
- G145. Smoke tests? (Post-deployment / On-demand / None)
- G146. Health check verification? (Automated / Manual)
- G147. Load balancer updates? (Automated / Manual)
- G148. DNS updates? (Automated / Manual)
- G149. SSL certificate updates? (Automated / Manual)
- G150. Secrets injection? (Vault / KMS / Environment / Sealed)
- G151. Variable interpolation? (Jinja2 / Go templates / Custom)
- G152. Conditional execution? (If-then / Matrix builds / Custom)
- G153. Pipeline versioning? (YAML in repo / UI-based / Custom)
- G154. Pipeline as Code? (Declarative / Imperative / Both)
- G155. Reusable pipeline components? (Templates / Macros / Custom)
- G156. Cross-repository pipelines? (Triggered / Not supported)
- G157. Scheduled pipelines? (Cron / Manual / Triggered)
- G158. Rollback automation? (Instant / Gradual / Manual)
- G159. Pipeline SLA? (Execution time target / None)
- G160. Pipeline metrics? (Duration / Success rate / Custom)

**G161-G200 — Infrastructure & Cloud (40 Soru)**
- G161. Cloud provider? (AWS / GCP / Azure / Multi-cloud / On-prem)
- G162. Infrastructure as Code? (Terraform / CloudFormation / Pulumi / Custom)
- G163. IaC versioning? (Git / S3 / Custom)
- G164. IaC testing? (Terratest / PolicyCode / None)
- G165. IaC linting? (Enabled / Disabled)
- G166. State management? (Remote backend / Local / Manual)
- G167. State locking? (Enabled / Disabled)
- G168. State encryption? (At-rest / In-transit / Both)
- G169. Backup strategy? (Automated / Manual / Custom)
- G170. Multi-region infrastructure? (Active-active / Active-passive / Single)
- G171. Disaster recovery? (RTO: ___ / RPO: ___)
- G172. Cost optimization? (Reserved instances / Spot / Auto-scaling)
- G173. Network design? (VPC / Custom / Default)
- G174. VPC peering? (Implemented / Not needed)
- G175. VPN configuration? (Site-to-site / Client-to-site / None)
- G176. NAT gateway? (Managed / Custom / Not needed)
- G177. Bastion hosts? (Deployed / Not needed)
- G178. Load balancer type? (Application / Network / Classic)
- G179. Load balancer sticky sessions? (Enabled / Disabled)
- G180. Auto-scaling groups? (Configured / Manual / None)
- G181. Launch templates? (Versioned / Static / None)
- G182. AMI management? (Golden image / Packer / Manual)
- G183. Snapshot strategy? (Automated / Manual / None)
- G184. Volume encryption? (At-rest / Not encrypted)
- G185. Security groups? (Restrictive / Permissive / Custom)
- G186. NACLs? (Configured / Not used)
- G187. Route tables? (Customized / Default)
- G188. Internet gateway? (Configured / Not needed)
- G189. NAT instances? (Deployed / Not needed)
- G190. VPC flow logs? (Enabled / Disabled)
- G191. CloudTrail? (Enabled / Disabled)
- G192. CloudWatch? (Custom metrics / Default / None)
- G193. CloudWatch alarms? (Configured / Needed / None)
- G194. Lambda functions? (Serverless / Not used)
- G195. Lambda cold start? (Optimized / Not measured)
- G196. Lambda concurrency? (Reserved / Provisioned / Default)
- G197. API Gateway? (REST / HTTP / WebSocket / Multiple)
- G198. API Gateway caching? (Enabled / Disabled)
- G199. API Gateway throttling? (Configured / Default)
- G200. Database instance class? (Optimized / General / Under-provisioned)

**G201-G240 — Monitoring & Troubleshooting (40 Soru)**
- G201. Monitoring stack? (Prometheus + Grafana / Datadog / CloudWatch)
- G202. Metric scrape interval? (15s / 30s / 1m / Custom)
- G203. Metric retention? (15d / 1y / Unlimited / Custom)
- G204. Dashboard templates? (Pre-built / Custom / None)
- G205. Alert thresholds? (Dynamic / Static / ML-based)
- G206. Alert deduplication? (Enabled / Disabled)
- G207. Alert silencing? (Scheduled / Manual / None)
- G208. Alert escalation? (Multi-level / Single / None)
- G209. On-call routing? (Automatic / Manual)
- G210. Incident management? (PagerDuty / Opsgenie / Custom / None)
- G211. Incident tracking? (Jira / Custom / None)
- G212. Post-incident analysis? (Formal / Informal / None)
- G213. RCA (Root Cause Analysis)? (Documented / Ad-hoc / None)
- G214. Blameless culture? (Implemented / Not practiced)
- G215. Change log? (Git / Changelog file / Manual)
- G216. Change tracking? (All changes / Deployments only)
- G217. Correlation ID? (Generated / Propagated / Not used)
- G218. Trace context propagation? (W3C traceparent / Custom / None)
- G219. Debug logging? (Disabled in prod / Enabled / Sampling)
- G220. Structured logging? (JSON / Key-value / Plain text)
- G221. Log aggregation latency? (<1s / <10s / >1m)
- G222. Log retention? (7d / 30d / 1y / Unlimited)
- G223. Log rotation? (Automated / Manual / Daily)
- G224. Log compression? (Gzip / Zstd / None)
- G225. Sensitive log redaction? (Automatic / Manual / None)
- G226. Log parsing? (Automated / Manual / None)
- G227. Custom dashboards? (Self-service / Admin-only / Not allowed)
- G228. Runbook automation? (Full / Partial / None)
- G229. Debugging capability? (Remote debugging / Local only)
- G230. Profiling in production? (Safe mode / Development only / Not allowed)
- G231. Memory dump analysis? (Tools available / Not practiced)
- G232. Core dump generation? (Enabled / Disabled)
- G233. Goroutine/thread dumps? (Automated / Manual / Not captured)
- G234. Network packet capture? (Tcpdump / Packet broker / None)
- G235. Service status page? (Public / Internal / None)
- G236. Status page automation? (API-driven / Manual / None)
- G237. Incident timeline? (Auto-generated / Manual / None)
- G238. Historical metrics? (Available / Not retained)
- G239. Anomaly detection? (ML-based / Threshold / None)
- G240. Predictive scaling? (ML / Configured thresholds / None)

**G241-G270 — Testing & Quality Assurance (30 Soru)**
- G241. Test environment parity? (Identical / Approximate / Different)
- G242. Test data seeding? (Automated / Manual / Fixtures)
- G243. Test data cleanup? (Automated / Manual / None)
- G244. Database sanitization? (Production-like / Generic data)
- G245. PII in test environments? (Anonymized / Real / Masked)
- G246. Test isolation? (Complete / Shared state / Ad-hoc)
- G247. Flaky test detection? (Automated / Manual / Not tracked)
- G248. Flaky test remediation? (Immediate / Backlog / Ignored)
- G249. Test retry logic? (Implemented / Not used)
- G250. Test retry limit? (1 / 3 / 5 / Custom)
- G251. Parallel test execution? (Supported / Sequential / Not tested)
- G252. Test execution order? (Random / Deterministic / Custom)
- G253. Test timeout? (Per test / Global / None)
- G254. Test report generation? (HTML / JSON / Custom)
- G255. Test result archival? (Long-term / Deleted / Custom)
- G256. Code coverage history? (Tracked / Not tracked)
- G257. Mutation testing? (Implemented / Planned / None)
- G258. Property-based testing? (Implemented / Not used)
- G259. Fuzzing? (Continuous / Periodic / None)
- G260. Accessibility testing? (Automated / Manual / Both)
- G261. Visual regression testing? (Implemented / Not used)
- G262. Performance regression? (Tracked / Not measured)
- G263. Security regression? (Tracked / Ad-hoc)
- G264. Cross-browser testing? (Automated / Manual / None)
- G265. Mobile device testing? (Physical / Emulated / None)
- G266. Accessibility compliance? (WCAG AA / AAA / None)
- G267. Performance benchmarking? (Baseline / Regression / None)
- G268. Load test baselines? (Established / Not measured)
- G269. Security scanning frequency? (Per commit / Daily / Weekly)
- G270. Compliance testing? (Automated / Manual / None)

**G271-G300 — Reliability & Observability (30 Soru)**
- G271. Mean time to detection (MTTD)? (<5min / <15min / <1hour / Unmeasured)
- G272. Mean time to resolution (MTTR)? (<15min / <1hour / <4hours / SLA-based)
- G273. Incident severity SLA? (SEV-0 / SEV-1 / SEV-2+ / Custom)
- G274. On-call escalation matrix? (Documented / Implicit / None)
- G275. War room automation? (Auto-created / Manual / None)
- G276. Incident state machine? (Defined / Ad-hoc / None)
- G277. Post-mortem template? (Standard / Custom / None)
- G278. Blameless postmortem? (Enforced / Encouraged / Not practiced)
- G279. Action item tracking? (Jira / Linear / Manual)
- G280. Action item follow-up? (Automated / Scheduled / None)
- G281. Incident trend analysis? (Monthly / Quarterly / None)
- G282. Infrastructure as Code testing? (Terratest / PolicyCode / None)
- G283. IaC drift detection? (Automated / Manual / None)
- G284. IaC approval process? (Peer review / CODEOWNERS / Auto-merge)
- G285. State file backup? (Automated / Manual / Cloud-managed)
- G286. State file encryption? (At-rest / In-transit / Both)
- G287. Provider pinning? (Exact version / Patch version / Latest)
- G288. Dependency scanning? (Automated / Manual / Weekly)
- G289. Outdated resource audit? (Quarterly / Annual / Never)
- G290. Cost anomaly detection? (AI-based / Threshold / None)
- G291. Budget alerts? (Immediate / Daily / Weekly)
- G292. Cost allocation tags? (Comprehensive / Partial / None)
- G293. Utilization reports? (Automated / Manual / None)
- G294. Recommendation engine? (Right-sizing / Reserved instances / None)
- G295. Capacity forecast? (ML-based / Manual / None)
- G296. Dependency graph visualization? (Available / Not tracked)
- G297. Critical path analysis? (Implemented / None)
- G298. Blast radius estimation? (Automated / Manual / None)
- G299. Service dependency monitoring? (Real-time / Periodic / None)
- G300. Cross-service latency tracking? (Tracing / Logs / None)
```

---

*BLOK G: 300+ Sorular | DevOps & Deployment | 17 Kategori*
