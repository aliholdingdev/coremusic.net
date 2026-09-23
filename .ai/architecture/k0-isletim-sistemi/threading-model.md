---
title: "Threading Model - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.0
---

# Threading Model

## Genel Bakış

Threading Model modülü, COREMUSIC'ın çoklu iş parçacığı yönetim stratejilerini tanımlar. Thread pool'lar, lock-free veri yapıları, atomik operations, condition variables ve mutex hiyerarşisi ile yüksek performanslı ve thread-safe bir ortam sağlar. Gerçek zamanlı ses işleme için deterministik davranış ve minimum gecikme hedefler.

## Teknik Detaylar

### 1. Thread Pools

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

### 2. Lock-free Structures

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

### 3. Atomic Operations

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

### 4. Condition Variables

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

### 5. Mutex Hierarchy

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

## API / Arayüz

### COREMUSIC Threading API Başlık Dosyası

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

## Bağımlılıklar

### Gereksinimler
- pthreads (Linux/macOS)
- Windows Threads API
- C11 atomics veya GCC atomics

### Alt Katmanlar
- K0 Memory Management
- K0 İşletim Sistemi katmanı

### Üst Katmanlar
- K1 Ses Motoru
- K3 Uygulama Katmanı

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Thread creation | < 50μs | Belirlenecek |
| Mutex lock/unlock | < 100ns | Belirlenecek |
| Spinlock lock/unlock | < 50ns | Belirlenecek |
| LF stack push/pop | < 100ns | Belirlenecek |
| LF queue enqueue/dequeue | < 200ns | Belirlenecek |
| Condition wait/signal | < 1μs | Belirlenecek |

## Durum: Implementasyon

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
