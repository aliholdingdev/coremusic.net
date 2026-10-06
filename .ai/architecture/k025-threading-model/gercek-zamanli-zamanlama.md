---
title: "Gerçek Zamanlı Zamanlama - k025-threading-model"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Gerçek Zamanlı Zamanlama

> Klasör: `k025-threading-model` · Dilim: D01 (k018–k035) · Dosya: `gercek-zamanli-zamanlama.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Gerçek zamanlı zamanlama politikaları, öncelik devri ve deadline yönetimi.

Bu belge; D01 diliminin (İş Parçacığı Modeli) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Gerçek zamanlı zamanlama politikaları, öncelik devri ve deadline yönetimi.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (İş Parçacığı Modeli)
- **Çapraz referanslar:** [[../k024-ipc-mekanizmalari/ipc-mekanizmalari.md]] · [[../k032-latency-optimization/latency-optimization.md]] · [[../k026-memory-management/memory-management.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### threading-model.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` (736 satır)

#### threading-model.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § (giriş) — L1–L10

---
title: "Threading Model - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.1
---

### Threading Model


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § `Genel Bakış` — L11–L13


Threading Model modülü, COREMUSIC'ın çoklu iş parçacığı yönetim stratejilerini tanımlar. Thread pool'lar, lock-free veri yapıları, atomik operations, condition variables ve mutex hiyerarşisi ile yüksek performanslı ve thread-safe bir ortam sağlar. Gerçek zamanlı ses işleme için deterministik davranış ve minimum gecikme hedefler.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § `Teknik Detaylar` — L15–L621


##### 1. Thread Pools

Thread pool'lar, iş parçacığı oluşturma/m yok etme overhead'ini azaltır:

```c
#include <stdatomic.h>
#include <pthread.h>

// Task yapısı
typedef struct Task {
    void (*function)(void *);
    void *arg;
    struct Task *next;
} Task;

// Thread pool yapısı
typedef struct {
    pthread_t *threads;
    int thread_count;

    Task *task_queue;
    pthread_mutex_t queue_mutex;
    pthread_cond_t queue_cond;

    atomic_int active_tasks;
    atomic_int running;

    // Worker thread ID'leri
    int *thread_ids;
    cpu_set_t *cpu_affinities;
} ThreadPool;

// Thread pool oluşturma
ThreadPool* thread_pool_create(int thread_count) {
    ThreadPool *pool = malloc(sizeof(ThreadPool));
    pool->thread_count = thread_count;
    pool->threads = malloc(sizeof(pthread_t) * thread_count);
    pool->thread_ids = malloc(sizeof(int) * thread_count);
    pool->cpu_affinities = malloc(sizeof(cpu_set_t) * thread_count);

    pthread_mutex_init(&pool->queue_mutex, NULL);
    pthread_cond_init(&pool->queue_cond, NULL);

    pool->task_queue = NULL;
    pool->active_tasks = 0;
    pool->running = 1;

    // Worker thread'leri başlat
    for (int i = 0; i < thread_count; i++) {
        pool->thread_ids[i] = i;

        // CPU affinity ayarla
        CPU_ZERO(&pool->cpu_affinities[i]);
        CPU_SET(i, &pool->cpu_affinities[i]);

        pthread_create(&pool->threads[i], NULL, worker_thread, pool);
    }

    return pool;
}

// Worker thread fonksiyonu
void* worker_thread(void *arg) {
    ThreadPool *pool = (ThreadPool *)arg;

    while (pool->running) {
        pthread_mutex_lock(&pool->queue_mutex);

        while (!pool->task_queue && pool->running) {
            pthread_cond_wait(&pool->queue_cond, &pool->queue_mutex);
        }

        if (!pool->running && !pool->task_queue) {
            pthread_mutex_unlock(&pool->queue_mutex);
            break;
        }

        // Task'ı al
        Task *task = pool->task_queue;
        if (task) {
            pool->task_queue = task->next;
        }

        pthread_mutex_unlock(&pool->queue_mutex);

        if (task) {
            atomic_fetch_add(&pool->active_tasks, 1);
            task->function(task->arg);
            atomic_fetch_sub(&pool->active_tasks, 1);
            free(task);
        }
    }

    return NULL;
}

// Task ekleme
void thread_pool_submit(ThreadPool *pool, void (*function)(void *), void *arg) {
    Task *task = malloc(sizeof(Task));
    task->function = function;
    task->arg = arg;
    task->next = NULL;

    pthread_mutex_lock(&pool->queue_mutex);

    // Queue sonuna ekle
    if (!pool->task_queue) {
        pool->task_queue = task;
    } else {
        Task *current = pool->task_queue;
        while (current->next) {
            current = current->next;
        }
        current->next = task;
    }

    pthread_cond_signal(&pool->queue_cond);
    pthread_mutex_unlock(&pool->queue_mutex);
}

// Thread pool durdurma
void thread_pool_destroy(ThreadPool *pool) {
    pool->running = 0;
    pthread_cond_broadcast(&pool->queue_cond);

    for (int i = 0; i < pool->thread_count; i++) {
        pthread_join(pool->threads[i], NULL);
    }

    pthread_mutex_destroy(&pool->queue_mutex);
    pthread_cond_destroy(&pool->queue_cond);

    free(pool->threads);
    free(pool->thread_ids);
    free(pool->cpu_affinities);
    free(pool);
}

// Priority thread pool
typedef struct {
    ThreadPool *high_priority;
    ThreadPool *normal_priority;
    ThreadPool *low_priority;
    atomic_int current_priority;
} PriorityThreadPool;

PriorityThreadPool* priority_thread_pool_create(int threads_per_priority) {
    PriorityThreadPool *pool = malloc(sizeof(PriorityThreadPool));
    pool->high_priority = thread_pool_create(threads_per_priority);
    pool->normal_priority = thread_pool_create(threads_per_priority);
    pool->low_priority = thread_pool_create(threads_per_priority);
    pool->current_priority = 0;
    return pool;
}

void priority_thread_pool_submit(PriorityThreadPool *pool, int priority,
    void (*function)(void *), void *arg) {
    switch (priority) {
        case 0: thread_pool_submit(pool->high_priority, function, arg); break;
        case 1: thread_pool_submit(pool->normal_priority, function, arg); break;
        case 2: thread_pool_submit(pool->low_priority, function, arg); break;
    }
}
```

##### 2. Lock-free Structures

Lock-free veri yapıları, kilitleme olmadan eşzamanlı erişim:

```c
#include <stdatomic.h>

// Lock-free stack
typedef struct LFNode {
    void *data;
    _Atomic(struct LFNode *) next;
} LFNode;

typedef struct {
    _Atomic(LFNode *) top;
    atomic_int count;
} LFStack;

LFStack* lf_stack_create() {
    LFStack *stack = malloc(sizeof(LFStack));
    stack->top = NULL;
    stack->count = 0;
    return stack;
}

void lf_stack_push(LFStack *stack, void *data) {
    LFNode *node = malloc(sizeof(LFNode));
    node->data = data;

    LFNode *old_top;
    do {
        old_top = atomic_load(&stack->top);
        node->next = old_top;
    } while (!atomic_compare_exchange_weak(&stack->top, &old_top, node));

    atomic_fetch_add(&stack->count, 1);
}

void* lf_stack_pop(LFStack *stack) {
    LFNode *old_top;
    LFNode *new_top;

    do {
        old_top = atomic_load(&stack->top);
        if (!old_top) return NULL;
        new_top = old_top->next;
    } while (!atomic_compare_exchange_weak(&stack->top, &old_top, new_top));

    atomic_fetch_sub(&stack->count, 1);
    void *data = old_top->data;
    free(old_top);
    return data;
}

// Lock-free queue (Michael-Scott queue)
typedef struct LFQueueNode {
    void *data;
    _Atomic(struct LFQueueNode *) next;
} LFQueueNode;

typedef struct {
    _Atomic(LFQueueNode *) head;
    _Atomic(LFQueueNode *) tail;
    atomic_int count;
} LFQueue;

LFQueue* lf_queue_create() {
    LFQueue *queue = malloc(sizeof(LFQueue));
    LFQueueNode *sentinel = malloc(sizeof(LFQueueNode));
    sentinel->next = NULL;

    queue->head = sentinel;
    queue->tail = sentinel;
    queue->count = 0;

    return queue;
}

void lf_queue_enqueue(LFQueue *queue, void *data) {
    LFQueueNode *node = malloc(sizeof(LFQueueNode));
    node->data = data;
    node->next = NULL;

    LFQueueNode *old_tail;
    LFQueueNode *next;

    do {
        old_tail = atomic_load(&queue->tail);
        next = atomic_load(&old_tail->next);

        if (old_tail == atomic_load(&queue->tail)) {
            if (next == NULL) {
                if (atomic_compare_exchange_weak(&old_tail->next, &next, node)) {
                    break;
                }
            } else {
                atomic_compare_exchange_weak(&queue->tail, &old_tail, next);
            }
        }
    } while (1);

    atomic_compare_exchange_weak(&queue->tail, &old_tail, node);
    atomic_fetch_add(&queue->count, 1);
}

void* lf_queue_dequeue(LFQueue *queue) {
    LFQueueNode *old_head;
    LFQueueNode *old_tail;
    LFQueueNode *next;

    do {
        old_head = atomic_load(&queue->head);
        old_tail = atomic_load(&queue->tail);
        next = atomic_load(&old_head->next);

        if (old_head == atomic_load(&queue->head)) {
            if (old_head == old_tail) {
                if (next == NULL) {
                    return NULL;  // Queue boş
                }
                atomic_compare_exchange_weak(&queue->tail, &old_tail, next);
            } else {
                void *data = next->data;
                if (atomic_compare_exchange_weak(&queue->head, &old_head, next)) {
                    free(old_head);
                    atomic_fetch_sub(&queue->count, 1);
                    return data;
                }
            }
        }
    } while (1);
}
```

##### 3. Atomic Operations

Atomik işlemler, kilitleme gerektirmeyen güvenli operasyonlar:

```c
#include <stdatomic.h>

// Atomic counter
typedef struct {
    _Atomic int64_t value;
} AtomicCounter;

void atomic_counter_init(AtomicCounter *counter, int64_t initial) {
    atomic_store(&counter->value, initial);
}

int64_t atomic_counter_increment(AtomicCounter *counter) {
    return atomic_fetch_add(&counter->value, 1) + 1;
}

int64_t atomic_counter_decrement(AtomicCounter *counter) {
    return atomic_fetch_sub(&counter->value, 1) - 1;
}

int64_t atomic_counter_get(AtomicCounter *counter) {
    return atomic_load(&counter->value);
}

// Atomic compare and swap
typedef struct {
    _Atomic int64_t value;
} AtomicCAS;

int64_t atomic_cas_compare(AtomicCAS *cas, int64_t expected, int64_t desired) {
    atomic_compare_exchange_strong(&cas->value, &expected, desired);
    return expected;
}

// Atomic min/max
int64_t atomic_min(_Atomic int64_t *value, int64_t new_value) {
    int64_t old_value;
    do {
        old_value = atomic_load(value);
        if (new_value >= old_value) break;
    } while (!atomic_compare_exchange_weak(value, &old_value, new_value));
    return old_value;
}

int64_t atomic_max(_Atomic int64_t *value, int64_t new_value) {
    int64_t old_value;
    do {
        old_value = atomic_load(value);
        if (new_value <= old_value) break;
    } while (!atomic_compare_exchange_weak(value, &old_value, new_value));
    return old_value;
}

// Spinlock
typedef struct {
    _Atomic int lock;
} Spinlock;

void spinlock_init(Spinlock *spin) {
    atomic_store(&spin->lock, 0);
}

void spinlock_lock(Spinlock *spin) {
    while (atomic_exchange(&spin->lock, 1)) {
        // Busy wait - pause instruction for CPU
        #ifdef __x86_64__
        asm volatile("pause");
        #endif
    }
}

int spinlock_trylock(Spinlock *spin) {
    return atomic_exchange(&spin->lock, 0) == 0;
}

void spinlock_unlock(Spinlock *spin) {
    atomic_store(&spin->lock, 0);
}

// Read-Write Lock
typedef struct {
    _Atomic int readers;
    _Atomic int writer;
    Spinlock spin;
} RWLock;

void rwlock_init(RWLock *rw) {
    atomic_store(&rw->readers, 0);
    atomic_store(&rw->writer, 0);
    spinlock_init(&rw->spin);
}

void rwlock_read_lock(RWLock *rw) {
    while (atomic_load(&rw->writer)) {
        #ifdef __x86_64__
        asm volatile("pause");
        #endif
    }
    atomic_fetch_add(&rw->readers, 1);
}

void rwlock_read_unlock(RWLock *rw) {
    atomic_fetch_sub(&rw->readers, 1);
}

void rwlock_write_lock(RWLock *rw) {
    spinlock_lock(&rw->spin);
    while (atomic_load(&rw->readers) > 0) {
        spinlock_unlock(&rw->spin);
        #ifdef __x86_64__
        asm volatile("pause");
        #endif
        spinlock_lock(&rw->spin);
    }
    atomic_store(&rw->writer, 1);
}

void rwlock_write_unlock(RWLock *rw) {
    atomic_store(&rw->writer, 0);
    spinlock_unlock(&rw->spin);
}
```

##### 4. Condition Variables

Condition variables, thread senkronizasyonu için:

```c
#include <pthread.h>

// Condition variable wrapper
typedef struct {
    pthread_cond_t cond;
    pthread_mutex_t mutex;
} CMCondition;

CMCondition* cm_condition_create() {
    CMCondition *cond = malloc(sizeof(CMCondition));
    pthread_cond_init(&cond->cond, NULL);
    pthread_mutex_init(&cond->mutex, NULL);
    return cond;
}

void cm_condition_wait(CMCondition *cond) {
    pthread_mutex_lock(&cond->mutex);
    pthread_cond_wait(&cond->cond, &cond->mutex);
    pthread_mutex_unlock(&cond->mutex);
}

void cm_condition_wait_timeout(CMCondition *cond, uint64_t timeout_ms) {
    struct timespec ts;
    clock_gettime(CLOCK_REALTIME, &ts);
    ts.tv_sec += timeout_ms / 1000;
    ts.tv_nsec += (timeout_ms % 1000) * 1000000;

    if (ts.tv_nsec >= 1000000000) {
        ts.tv_sec++;
        ts.tv_nsec -= 1000000000;
    }

    pthread_mutex_lock(&cond->mutex);
    pthread_cond_timedwait(&cond->cond, &cond->mutex, &ts);
    pthread_mutex_unlock(&cond->mutex);
}

void cm_condition_signal(CMCondition *cond) {
    pthread_mutex_lock(&cond->mutex);
    pthread_cond_signal(&cond->cond);
    pthread_mutex_unlock(&cond->mutex);
}

void cm_condition_broadcast(CMCondition *cond) {
    pthread_mutex_lock(&cond->mutex);
    pthread_cond_broadcast(&cond->cond);
    pthread_mutex_unlock(&cond->mutex);
}

// Barrier (tüm thread'lerin tamamlanmasını bekleme)
typedef struct {
    pthread_barrier_t barrier;
    int thread_count;
} CMBarrier;

CMBarrier* cm_barrier_create(int thread_count) {
    CMBarrier *barrier = malloc(sizeof(CMBarrier));
    barrier->thread_count = thread_count;
    pthread_barrier_init(&barrier->barrier, NULL, thread_count);
    return barrier;
}

void cm_barrier_wait(CMBarrier *barrier) {
    pthread_barrier_wait(&barrier->barrier);
}

void cm_barrier_destroy(CMBarrier *barrier) {
    pthread_barrier_destroy(&barrier->barrier);
    free(barrier);
}
```

##### 5. Mutex Hierarchy

Mutex hiyerarşisi, deadlock önleme için:

```c
#include <pthread.h>

// Mutex seviyeleri
typedef enum {
    MUTEX_LEVEL_CRITICAL = 0,   // En yüksek öncelik
    MUTEX_LEVEL_HIGH = 1,
    MUTEX_LEVEL_MEDIUM = 2,
    MUTEX_LEVEL_LOW = 3,        // En düşük öncelik
    MUTEX_LEVEL_COUNT = 4
} MutexLevel;

// Mutex hierarchy yapısı
typedef struct {
    pthread_mutex_t mutexes[MUTEX_LEVEL_COUNT];
    _Atomic int current_level;
    _Atomic int lock_count[MUTEX_LEVEL_COUNT];
    pthread_t owner_thread;
} MutexHierarchy;

MutexHierarchy* mutex_hierarchy_create() {
    MutexHierarchy *hierarchy = malloc(sizeof(MutexHierarchy));

    for (int i = 0; i < MUTEX_LEVEL_COUNT; i++) {
        pthread_mutexattr_t attr;
        pthread_mutexattr_init(&attr);
        pthread_mutexattr_settype(&attr, PTHREAD_MUTEX_RECURSIVE);
        pthread_mutex_init(&hierarchy->mutexes[i], &attr);
        atomic_store(&hierarchy->lock_count[i], 0);
    }

    atomic_store(&hierarchy->current_level, MUTEX_LEVEL_COUNT);
    return hierarchy;
}

// Hiyerarşik mutex kilitleme
void mutex_hierarchy_lock(MutexHierarchy *hierarchy, MutexLevel level) {
    // Seviye kontrolü (deadlock önleme)
    int current = atomic_load(&hierarchy->current_level);
    if (level < current) {
        fprintf(stderr, "Mutex hierarchy violation: level %d < current %d\n",
                level, current);
        abort();
    }

    pthread_mutex_lock(&hierarchy->mutexes[level]);
    atomic_store(&hierarchy->current_level, level);
    atomic_fetch_add(&hierarchy->lock_count[level], 1);

    // Thread sahipliğini kaydet
    hierarchy->owner_thread = pthread_self();
}

// Hiyerarşik mutex kilidi açma
void mutex_hierarchy_unlock(MutexHierarchy *hierarchy, MutexLevel level) {
    if (hierarchy->owner_thread != pthread_self()) {
        fprintf(stderr, "Mutex not owned by current thread\n");
        abort();
    }

    atomic_fetch_sub(&hierarchy->lock_count[level], 1);
    pthread_mutex_unlock(&hierarchy->mutexes[level]);

    // Bir sonraki seviyeye geç
    for (int i = 0; i < MUTEX_LEVEL_COUNT; i++) {
        if (atomic_load(&hierarchy->lock_count[i]) > 0) {
            atomic_store(&hierarchy->current_level, i);
            return;
        }
    }
    atomic_store(&hierarchy->current_level, MUTEX_LEVEL_COUNT);
}

// Timeout ile mutex kilitleme
int mutex_hierarchy_trylock(MutexHierarchy *hierarchy, MutexLevel level,
                           uint64_t timeout_ms) {
    struct timespec ts;
    clock_gettime(CLOCK_REALTIME, &ts);
    ts.tv_sec += timeout_ms / 1000;
    ts.tv_nsec += (timeout_ms % 1000) * 1000000;

    int result = pthread_mutex_timedlock(&hierarchy->mutexes[level], &ts);
    if (result == 0) {
        atomic_store(&hierarchy->current_level, level);
        atomic_fetch_add(&hierarchy->lock_count[level], 1);
        hierarchy->owner_thread = pthread_self();
    }

    return result;
}

void mutex_hierarchy_destroy(MutexHierarchy *hierarchy) {
    for (int i = 0; i < MUTEX_LEVEL_COUNT; i++) {
        pthread_mutex_destroy(&hierarchy->mutexes[i]);
    }
    free(hierarchy);
}
```

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § `API / Arayüz` — L623–L694


##### COREMUSIC Threading API Başlık Dosyası

```c
#ifndef COREMUSIC_THREADING_H
#define COREMUSIC_THREADING_H

#include <stdint.h>
#include <stddef.h>

// Thread Pool
CM_Status CM_ThreadPool_Create(int thread_count, void **pool);
CM_Status CM_ThreadPool_Submit(void *pool, void (*func)(void*), void *arg);
CM_Status CM_ThreadPool_WaitComplete(void *pool);
CM_Status CM_ThreadPool_Destroy(void *pool);

// Priority Thread Pool
CM_Status CM_PriorityThreadPool_Create(int threads_per_priority, void **pool);
CM_Status CM_PriorityThreadPool_Submit(void *pool, int priority,
    void (*func)(void*), void *arg);
CM_Status CM_PriorityThreadPool_Destroy(void *pool);

// Lock-free Stack
CM_Status CM_LFStack_Create(void **stack);
CM_Status CM_LFStack_Push(void *stack, void *data);
CM_Status CM_LFStack_Pop(void *stack, void **data);
CM_Status CM_LFStack_Destroy(void *stack);

// Lock-free Queue
CM_Status CM_LFQueue_Create(void **queue);
CM_Status CM_LFQueue_Enqueue(void *queue, void *data);
CM_Status CM_LFQueue_Dequeue(void *queue, void **data);
CM_Status CM_LFQueue_Destroy(void *queue);

// Atomic Operations
CM_Status CM_AtomicCounter_Create(int64_t initial, void **counter);
CM_Status CM_AtomicCounter_Increment(void *counter, int64_t *result);
CM_Status CM_AtomicCounter_Decrement(void *counter, int64_t *result);
CM_Status CM_AtomicCounter_Get(void *counter, int64_t *result);

// Spinlock
CM_Status CM_Spinlock_Create(void **spin);
CM_Status CM_Spinlock_Lock(void *spin);
CM_Status CM_Spinlock_TryLock(void *spin, int *success);
CM_Status CM_Spinlock_Unlock(void *spin);

// RWLock
CM_Status CM_RWLock_Create(void **rw);
CM_Status CM_RWLock_ReadLock(void *rw);
CM_Status CM_RWLock_ReadUnlock(void *rw);
CM_Status CM_RWLock_WriteLock(void *rw);
CM_Status CM_RWLock_WriteUnlock(void *rw);

// Condition Variable
CM_Status CM_Condition_Create(void **cond);
CM_Status CM_Condition_Wait(void *cond);
CM_Status CM_Condition_WaitTimeout(void *cond, uint64_t timeout_ms);
CM_Status CM_Condition_Signal(void *cond);
CM_Status CM_Condition_Broadcast(void *cond);

// Barrier
CM_Status CM_Barrier_Create(int thread_count, void **barrier);
CM_Status CM_Barrier_Wait(void *barrier);

// Mutex Hierarchy
CM_Status CM_MutexHierarchy_Create(void **hierarchy);
CM_Status CM_MutexHierarchy_Lock(void *hierarchy, int level);
CM_Status CM_MutexHierarchy_Unlock(void *hierarchy, int level);

#endif // COREMUSIC_THREADING_H
```

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § `Bağımlılıklar` — L696–L709


##### Gereksinimler
- pthreads (Linux/macOS)
- Windows Threads API
- C11 atomics veya GCC atomics

##### Alt Katmanlar
- K0 Memory Management
- K0 İşletim Sistemi katmanı

##### Üst Katmanlar
- K1 Ses Motoru
- K3 Uygulama Katmanı

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § `Performans Metrikleri` — L711–L720


| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Thread creation | < 50μs | Belirlenecek |
| Mutex lock/unlock | < 100ns | Belirlenecek |
| Spinlock lock/unlock | < 50ns | Belirlenecek |
| LF stack push/pop | < 100ns | Belirlenecek |
| LF queue enqueue/dequeue | < 200ns | Belirlenecek |
| Condition wait/signal | < 1μs | Belirlenecek |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § `Durum: Implementasyon` — L722–L736


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Thread pool implementasyonu
2. Lock-free queue implementasyonu
3. Spinlock implementasyonu
4. Mutex hierarchy
5. RWLock implementasyonu

**Sonraki Adımlar**:
- Thread pool performans testlerinin yapılması
- Lock-free yapıların stres testleri
- Deadlock test senaryolarının yazılması

### system-calls.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` (692 satır)

#### system-calls.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § (giriş) — L1–L10

---
title: "System Calls - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.1
---

### System Calls


#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § `Teknik Detaylar` — L15–L590


##### 1. POSIX Syscall Interface

POSIX, Unix tabanlı sistemler için standart arayüz:

```c
#include <sys/syscall.h>
#include <unistd.h>

// Temel POSIX syscall'lar
ssize_t sys_read(int fd, void *buf, size_t count) {
    return syscall(SYS_read, fd, buf, count);
}

ssize_t sys_write(int fd, const void *buf, size_t count) {
    return syscall(SYS_write, fd, buf, count);
}

int sys_open(const char *pathname, int flags, mode_t mode) {
    return syscall(SYS_open, pathname, flags, mode);
}

int sys_close(int fd) {
    return syscall(SYS_close, fd);
}

// Memory management syscall'lar
void *sys_mmap(void *addr, size_t length, int prot, int flags, int fd, off_t offset) {
    return (void *)syscall(SYS_mmap, addr, length, prot, flags, fd, offset);
}

int sys_munmap(void *addr, size_t length) {
    return syscall(SYS_munmap, addr, length);
}

int sys_mprotect(void *addr, size_t length, int prot) {
    return syscall(SYS_mprotect, addr, length, prot);
}

int sys_mlock(const void *addr, size_t length) {
    return syscall(SYS_mlock, addr, length);
}

int sys_munlock(const void *addr, size_t length) {
    return syscall(SYS_munlock, addr, length);
}

// Process management syscall'lar
pid_t sys_fork(void) {
    return syscall(SYS_fork);
}

int sys_execve(const char *filename, char *const argv[], char *const envp[]) {
    return syscall(SYS_execve, filename, argv, envp);
}

pid_t sys_wait4(pid_t pid, int *wstatus, int options, struct rusage *rusage) {
    return syscall(SYS_wait4, pid, wstatus, options, rusage);
}

int sys_kill(pid_t pid, int sig) {
    return syscall(SYS_kill, pid, sig);
}

// Signal handling
typedef void (*sighandler_t)(int);

sighandler_t sys_signal(int signum, sighandler_t handler) {
    // signal syscall yerine sigaction kullan
    struct sigaction sa;
    sa.sa_handler = handler;
    sigemptyset(&sa.sa_mask);
    sa.sa_flags = 0;

    if (sigaction(signum, &sa, NULL) == -1) {
        return SIG_ERR;
    }

    return handler;
}

// Thread management
pid_t sys_clone(unsigned long flags, void *child_stack, int *parent_tid, int *child_tid, unsigned long tls) {
    return syscall(SYS_clone, flags, child_stack, parent_tid, child_tid, tls);
}

// Semaphore
int sys_semget(key_t key, int nsems, int semflg) {
    return syscall(SYS_semget, key, nsems, semflg);
}

int sys_semop(int semid, struct sembuf *sops, unsigned long nsops) {
    return syscall(SYS_semop, semid, sops, nsops);
}

// Shared memory
int sys_shmget(key_t key, size_t size, int shmflg) {
    return syscall(SYS_shmget, key, size, shmflg);
}

void *sys_shmat(int shmid, const void *shmaddr, int shmflg) {
    return (void *)syscall(SYS_shmat, shmid, shmaddr, shmflg);
}
```

##### 2. Windows NT API

Windows NT API, Windows platformu için düşük seviyeli arayüz:

```c
#include <windows.h>
#include <winternl.h>

// NT API fonksiyon tanımı
typedef NTSTATUS (NTAPI *NtCreateFile_t)(
    PHANDLE FileHandle,
    ACCESS_MASK DesiredAccess,
    POBJECT_ATTRIBUTES ObjectAttributes,
    PIO_STATUS_BLOCK IoStatusBlock,
    PLARGE_INTEGER AllocationSize,
    ULONG FileAttributes,
    ULONG ShareAccess,
    ULONG CreateDisposition,
    ULONG CreateOptions,
    PVOID EaBuffer,
    ULONG EaLength
);

typedef NTSTATUS (NTAPI *NtReadFile_t)(
    HANDLE FileHandle,
    HANDLE Event,
    PIO_APC_ROUTINE ApcRoutine,
    PVOID ApcContext,
    PIO_STATUS_BLOCK IoStatusBlock,
    PVOID Buffer,
    ULONG Length,
    PLARGE_INTEGER ByteOffset,
    PULONG Key
);

typedef NTSTATUS (NTAPI *NtWriteFile_t)(
    HANDLE FileHandle,
    HANDLE Event,
    PIO_APC_ROUTINE ApcRoutine,
    PVOID ApcContext,
    PIO_STATUS_BLOCK IoStatusBlock,
    PVOID Buffer,
    ULONG Length,
    PLARGE_INTEGER ByteOffset,
    PULONG Key
);

// NT API fonksiyonlarını yükle
HMODULE ntdll = LoadLibrary("ntdll.dll");

NtCreateFile_t NtCreateFile = (NtCreateFile_t)GetProcAddress(ntdll, "NtCreateFile");
NtReadFile_t NtReadFile = (NtReadFile_t)GetProcAddress(ntdll, "NtReadFile");
NtWriteFile_t NtWriteFile = (NtWriteFile_t)GetProcAddress(ntdll, "NtWriteFile");

// NT dosya açma
NTSTATUS nt_open_file(const char *filename, HANDLE *handle) {
    UNICODE_STRING uniName;
    RtlInitUnicodeString(&uniName, L"\\??\\C:\\path\\to\\file");

    OBJECT_ATTRIBUTES objAttr;
    InitializeObjectAttributes(&objAttr, &uniName,
        OBJ_CASE_INSENSITIVE | OBJ_KERNEL_HANDLE,
        NULL, NULL);

    IO_STATUS_BLOCK ioStatus;
    LARGE_INTEGER allocationSize;

    NTSTATUS status = NtCreateFile(
        handle,
        GENERIC_READ | SYNCHRONIZE,
        &objAttr,
        &ioStatus,
        &allocationSize,
        FILE_ATTRIBUTE_NORMAL,
        FILE_SHARE_READ | FILE_SHARE_WRITE,
        FILE_OPEN_IF,
        FILE_SYNCHRONOUS_IO_NONALERT,
        NULL, 0);

    return status;
}

// NT dosya okuma
NTSTATUS nt_read_file(HANDLE handle, void *buffer, ULONG length) {
    IO_STATUS_BLOCK ioStatus;
    LARGE_INTEGER byteOffset = {0};

    NTSTATUS status = NtReadFile(
        handle,
        NULL, NULL, NULL,
        &ioStatus,
        buffer, length,
        &byteOffset, NULL);

    return status;
}

// NT dosya yazma
NTSTATUS nt_write_file(HANDLE handle, void *buffer, ULONG length) {
    IO_STATUS_BLOCK ioStatus;
    LARGE_INTEGER byteOffset = {0};

    NTSTATUS status = NtWriteFile(
        handle,
        NULL, NULL, NULL,
        &ioStatus,
        buffer, length,
        &byteOffset, NULL);

    return status;
}

// NT process oluşturma
NTSTATUS nt_create_process(const char *filename, HANDLE *process, HANDLE *thread) {
    UNICODE_STRING uniName;
    RtlInitUnicodeString(&uniName, L"\\??\\C:\\Windows\\System32\\notepad.exe");

    OBJECT_ATTRIBUTES objAttr;
    InitializeObjectAttributes(&objAttr, &uniName,
        OBJ_CASE_INSENSITIVE | OBJ_KERNEL_HANDLE,
        NULL, NULL);

    CLIENT_ID clientId;
    NTSTATUS status = RtlCreateProcessParameters(
        &processParameters, &uniName,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

    if (!NT_SUCCESS(status)) {
        return status;
    }

    status = NtCreateProcess(
        process,
        PROCESS_CREATE_THREAD | PROCESS_VM_OPERATION | PROCESS_VM_READ | PROCESS_VM_WRITE,
        &objAttr,
        NtCurrentProcess(),
        &clientId,
        processParameters,
        NULL, NULL, 0);

    return status;
}
```

##### 3. Linux io_uring

io_uring, yüksek performanslı asenkron I/O için:

```c
#include <liburing.h>
#include <sys/eventfd.h>

// io_uring yapısı
struct io_uring ring;
int event_fd;

// io_uring başlatma
int setup_io_uring() {
    int ret = io_uring_queue_init(256, &ring);
    if (ret < 0) {
        perror("io_uring_queue_init");
        return -1;
    }

    // Event fd oluştur
    event_fd = eventfd(0, EFD_NONBLOCK | EFD_SEMAPHORE);

    return 0;
}

// Async read operation
int async_read(int fd, void *buffer, size_t size, off_t offset) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_read(sqe, fd, buffer, size, offset);
    io_uring_sqe_set_data(sqe, buffer);

    io_uring_submit(&ring);
    return 0;
}

// Async write operation
int async_write(int fd, const void *buffer, size_t size, off_t offset) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_write(sqe, fd, buffer, size, offset);
    io_uring_sqe_set_data(sqe, (void *)buffer);

    io_uring_submit(&ring);
    return 0;
}

// Async accept
int async_accept(int listen_fd, struct sockaddr *addr, socklen_t *addrlen) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_accept(sqe, listen_fd, addr, addrlen, 0);

    int *client_fd = malloc(sizeof(int));
    io_uring_sqe_set_data(sqe, client_fd);

    io_uring_submit(&ring);
    return 0;
}

// Async connect
int async_connect(int fd, const struct sockaddr *addr, socklen_t addrlen) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_connect(sqe, fd, addr, addrlen);

    io_uring_sqe_set_data(sqe, NULL);
    io_uring_submit(&ring);
    return 0;
}

// Async send
int async_send(int fd, const void *buf, size_t len, int flags) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_send(sqe, fd, buf, len, flags);
    io_uring_sqe_set_data(sqe, (void *)buf);

    io_uring_submit(&ring);
    return 0;
}

// Async recv
int async_recv(int fd, void *buf, size_t len, int flags) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_recv(sqe, fd, buf, len, flags);
    io_uring_sqe_set_data(sqe, buf);

    io_uring_submit(&ring);
    return 0;
}

// Completion handling
int handle_completions(void (*callback)(void *data, int res)) {
    struct io_uring_cqe *cqe;
    int completed = 0;

    io_uring_for_each_cqe(&ring, &cqe) {
        void *data = io_uring_cqe_get_data(cqe);
        int result = cqe->res;

        callback(data, result);
        completed++;
    }

    io_uring_cq_advance(&ring, completed);
    return completed;
}

// Batch operations
int batch_read(int *fds, void **buffers, size_t *sizes, off_t *offsets, int count) {
    for (int i = 0; i < count; i++) {
        struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
        if (!sqe) return -1;

        io_uring_prep_read(sqe, fds[i], buffers[i], sizes[i], offsets[i]);
        io_uring_sqe_set_data(sqe, buffers[i]);
    }

    io_uring_submit(&ring);
    return count;
}

// io_uring temizleme
void cleanup_io_uring() {
    io_uring_queue_exit(&ring);
    close(event_fd);
}
```

##### 4. epoll (Linux)

epoll, yüksek performanslı I/O çoklama:

```c
#include <sys/epoll.h>
#include <unistd.h>

#define MAX_EVENTS 100

// epoll yapısı
typedef struct {
    int epoll_fd;
    struct epoll_event events[MAX_EVENTS];
    int event_count;
} EpollContext;

// epoll başlatma
EpollContext* epoll_create_context() {
    EpollContext *ctx = malloc(sizeof(EpollContext));
    ctx->epoll_fd = epoll_create1(EPOLL_CLOEXEC);
    ctx->event_count = 0;
    return ctx;
}

// fd ekleme
int epoll_add_fd(EpollContext *ctx, int fd, uint32_t events, void *data) {
    struct epoll_event ev;
    ev.events = events;
    ev.data.ptr = data;

    return epoll_ctl(ctx->epoll_fd, EPOLL_CTL_ADD, fd, &ev);
}

// fd güncelleme
int epoll_modify_fd(EpollContext *ctx, int fd, uint32_t events, void *data) {
    struct epoll_event ev;
    ev.events = events;
    ev.data.ptr = data;

    return epoll_ctl(ctx->epoll_fd, EPOLL_CTL_MOD, fd, &ev);
}

// fd kaldırma
int epoll_remove_fd(EpollContext *ctx, int fd) {
    return epoll_ctl(ctx->epoll_fd, EPOLL_CTL_DEL, fd, NULL);
}

// Eventleri bekleme
int epoll_wait_events(EpollContext *ctx, int timeout_ms) {
    return epoll_wait(ctx->epoll_fd, ctx->events, MAX_EVENTS, timeout_ms);
}

// Event handling
void epoll_handle_events(EpollContext *ctx,
    void (*handle_readable)(void *data),
    void (*handle_writable)(void *data),
    void (*handle_error)(void *data)) {

    for (int i = 0; i < ctx->event_count; i++) {
        struct epoll_event *ev = &ctx->events[i];
        void *data = ev->data.ptr;

        if (ev->events & EPOLLERR) {
            if (handle_error) handle_error(data);
        } else {
            if (ev->events & EPOLLIN) {
                if (handle_readable) handle_readable(data);
            }
            if (ev->events & EPOLLOUT) {
                if (handle_writable) handle_writable(data);
            }
        }
    }
}

// epoll temizleme
void epoll_destroy_context(EpollContext *ctx) {
    close(ctx->epoll_fd);
    free(ctx);
}
```

##### 5. kqueue (macOS/BSD)

kqueue, macOS ve BSD'ler için yüksek performanslı event notification:

```c
#include <sys/event.h>
#include <sys/time.h>

// kqueue yapısı
typedef struct {
    int kqueue_fd;
    struct kevent events[MAX_EVENTS];
    int event_count;
} KqueueContext;

// kqueue başlatma
KqueueContext* kqueue_create_context() {
    KqueueContext *ctx = malloc(sizeof(KqueueContext));
    ctx->kqueue_fd = kqueue();
    ctx->event_count = 0;
    return ctx;
}

// Event ekleme
int kqueue_add_event(KqueueContext *ctx, int fd, int filter, uint32_t flags, void *data) {
    struct kevent change;
    EV_SET(&change, fd, filter, flags, 0, 0, data);

    return kevent(ctx->kqueue_fd, &change, 1, NULL, 0, NULL);
}

// OKUYUN event'i ekleme (epoll equivalent: EPOLLIN)
int kqueue_add_read(KqueueContext *ctx, int fd, void *data) {
    return kqueue_add_event(ctx, fd, EVFILT_READ, EV_ADD | EV_ENABLE, data);
}

// YAZMA event'i ekleme (epoll equivalent: EPOLLOUT)
int kqueue_add_write(KqueueContext *ctx, int fd, void *data) {
    return kqueue_add_event(ctx, fd, EVFILT_WRITE, EV_ADD | EV_ENABLE, data);
}

// Timer event ekleme
int kqueue_add_timer(KqueueContext *ctx, int ident, int seconds, int milliseconds, void *data) {
    struct kevent change;
    struct timespec ts = {seconds, milliseconds * 1000000};

    EV_SET(&change, ident, EVFILT_TIMER, EV_ADD | EV_ENABLE, 0, 0, data);

    return kevent(ctx->kqueue_fd, &change, 1, NULL, 0, &ts);
}

// Signal event ekleme
int kqueue_add_signal(KqueueContext *ctx, int signum, void *data) {
    struct kevent change;
    EV_SET(&change, signum, EVFILT_SIGNAL, EV_ADD | EV_ENABLE, 0, 0, data);

    return kevent(ctx->kqueue_fd, &change, 1, NULL, 0, NULL);
}

// Eventleri bekleme
int kqueue_wait_events(KqueueContext *ctx, int timeout_ms) {
    struct timespec ts;
    if (timeout_ms >= 0) {
        ts.tv_sec = timeout_ms / 1000;
        ts.tv_nsec = (timeout_ms % 1000) * 1000000;
    }

    return kevent(ctx->kqueue_fd, NULL, 0, ctx->events, MAX_EVENTS,
        timeout_ms >= 0 ? &ts : NULL);
}

// Event handling
void kqueue_handle_events(KqueueContext *ctx,
    void (*handle_readable)(void *data),
    void (*handle_writable)(void *data),
    void (*handle_timer)(void *data),
    void (*handle_signal)(void *data)) {

    for (int i = 0; i < ctx->event_count; i++) {
        struct kevent *ev = &ctx->events[i];
        void *data = ev->udata;

        switch (ev->filter) {
            case EVFILT_READ:
                if (handle_readable) handle_readable(data);
                break;
            case EVFILT_WRITE:
                if (handle_writable) handle_writable(data);
                break;
            case EVFILT_TIMER:
                if (handle_timer) handle_timer(data);
                break;
            case EVFILT_SIGNAL:
                if (handle_signal) handle_signal(data);
                break;
        }
    }
}

// kqueue temizleme
void kqueue_destroy_context(KqueueContext *ctx) {
    close(ctx->kqueue_fd);
    free(ctx);
}
```

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § `Performans Metrikleri` — L667–L676


| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| POSIX syscall latency | < 100ns | Belirlenecek |
| NT API latency | < 200ns | Belirlenecek |
| io_uring submit | < 1μs | Belirlenecek |
| io_uring completion | < 1μs | Belirlenecek |
| epoll wait | < 10μs | Belirlenecek |
| kqueue wait | < 10μs | Belirlenecek |

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| - pthreads (Linux/macOS) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L699 |
| - Windows Threads API | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L700 |
| - C11 atomics veya GCC atomics | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L701 |
| - K0 Memory Management | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L704 |
| - K0 İşletim Sistemi katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L705 |
| - K1 Ses Motoru | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L708 |
| - K3 Uygulama Katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L709 |
| - Linux Kernel 5.1+ (io_uring için 5.6+) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L653 |
| - liburing 2.0+ (io_uring için) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L654 |
| - Windows SDK (NT API için) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L655 |
| - macOS SDK (kqueue için) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L656 |
| - CPU instruction set | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L659 |
| - Donanım I/O | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L660 |
| - K0 Cross-Platform API | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L663 |
| - K0 IPC Mekanizmaları | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L664 |
| - K1 Ses Motoru | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L665 |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| void cm_condition_wait_timeout(CMCondition *cond, uint64_t timeout_ms) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L469 |
| ts.tv_sec += timeout_ms / 1000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L472 |
| ts.tv_nsec += (timeout_ms % 1000) * 1000000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L473 |
| // Timeout ile mutex kilitleme | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L597 |
| uint64_t timeout_ms) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L599 |
| ts.tv_sec += timeout_ms / 1000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L602 |
| ts.tv_nsec += (timeout_ms % 1000) * 1000000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L603 |
| CM_Status CM_Condition_WaitTimeout(void *cond, uint64_t timeout_ms); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L680 |
| int epoll_wait_events(EpollContext *ctx, int timeout_ms) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L452 |
| return epoll_wait(ctx->epoll_fd, ctx->events, MAX_EVENTS, timeout_ms); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L453 |
| int kqueue_wait_events(KqueueContext *ctx, int timeout_ms) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L546 |
| if (timeout_ms >= 0) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L548 |
| ts.tv_sec = timeout_ms / 1000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L549 |
| ts.tv_nsec = (timeout_ms % 1000) * 1000000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L550 |
| timeout_ms >= 0 ? &ts : NULL); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L554 |
| CM_Status CM_EpollWait(void *ctx, int timeout_ms, int *count); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L635 |
| CM_Status CM_KqueueWait(void *ctx, int timeout_ms, int *count); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L644 |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| perror("io_uring_queue_init"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L281 |
| void (*handle_error)(void *data)) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L460 |
| if (handle_error) handle_error(data); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L467 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| Threading Model modülü, COREMUSIC'ın çoklu iş parçacığı yönetim stratejilerini tanımlar. Thread pool'lar, lock-free veri yapıları, atomik o… | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L13 |
| cpu_set_t *cpu_affinities; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L46 |
| pool->cpu_affinities = malloc(sizeof(cpu_set_t) * thread_count); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L55 |
| // CPU affinity ayarla | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L68 |
| CPU_ZERO(&pool->cpu_affinities[i]); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L69 |
| CPU_SET(i, &pool->cpu_affinities[i]); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L70 |
| free(pool->cpu_affinities); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L151 |
| // Busy wait - pause instruction for CPU | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L384 |
| PVOID EaBuffer, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L140 |
| PVOID Buffer, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L150 |
| PVOID Buffer, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L162 |
| NTSTATUS nt_read_file(HANDLE handle, void *buffer, ULONG length) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L204 |
| buffer, length, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L212 |
| NTSTATUS nt_write_file(HANDLE handle, void *buffer, ULONG length) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L219 |
| buffer, length, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L227 |
| int async_read(int fd, void *buffer, size_t size, off_t offset) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L292 |
| io_uring_prep_read(sqe, fd, buffer, size, offset); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L296 |
| io_uring_sqe_set_data(sqe, buffer); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L297 |
| int async_write(int fd, const void *buffer, size_t size, off_t offset) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L304 |
| io_uring_prep_write(sqe, fd, buffer, size, offset); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L308 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| - Thread pool performans testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L734 |
| - Lock-free yapıların stres testleri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L735 |
| - Deadlock test senaryolarının yazılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L736 |
| - io_uring benchmark testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L690 |
| - epoll ile high-performance networking testleri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L691 |
| - kqueue macOS implementasyon testleri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L692 |

## Riskler ve Belirsizlikler

Kaynaklarda `Belirlenecek` / `UNKNOWN` / `TODO` içeren toplam **12** satır tespit edildi (tüm kaynak dosyalar üzerinde tam tarama).

| Belirsizlik / risk (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |

## Identifier Envanteri

> Identifier'lar kaynak metinden sayım ile üretilmiştir; ilk geçtiği satır kanıt olarak verilmiştir.

| Identifier | Geçiş sayısı | İlk kanıt |
|---|---|---|
| `SCHED_FIFO` | 0 | [kaynakta eşleşme yok] |
| `priority` | 21 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L155 |
| `deadline` | 0 | [kaynakta eşleşme yok] |
| `latency` | 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L671 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | (başlık + giriş) | L1–L10 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Genel Bakış | L11–L13 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Teknik Detaylar | L15–L621 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## API / Arayüz | L623–L694 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Bağımlılıklar | L696–L709 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Performans Metrikleri | L711–L720 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Durum: Implementasyon | L722–L736 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | (başlık + giriş) | L1–L10 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Genel Bakış | L11–L13 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Teknik Detaylar | L15–L590 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## API / Arayüz | L592–L648 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Bağımlılıklar | L650–L665 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Performans Metrikleri | L667–L676 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Durum: Implementasyon | L678–L692 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § `Durum: Implementasyon` — L722–L736


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Thread pool implementasyonu
2. Lock-free queue implementasyonu
3. Spinlock implementasyonu
4. Mutex hierarchy
5. RWLock implementasyonu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § `Durum: Implementasyon` — L678–L692


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. io_uring implementasyonu (Linux için en yüksek performans)
2. epoll implementasyonu
3. kqueue implementasyonu
4. POSIX syscall wrapper'ları
5. Windows NT API entegrasyonu

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
