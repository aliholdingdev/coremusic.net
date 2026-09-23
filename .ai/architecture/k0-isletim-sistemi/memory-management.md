---
title: "Memory Management - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.0
---

# Memory Management

## Genel Bakış

Memory Management modülü, COREMUSIC'ın bellek yönetim stratejilerini ve optimizasyonlarını tanımlar. Sanal bellek, sayfa tabloları, bellek havuzları, slab allocator ve buffer yönetimi ile gerçek zamanlı ses işleme için yüksek performanslı bellek yönetimi sağlar. Düşük gecikme ve minimum bellek parçalanması hedefler.

## Teknik Detaylar

### 1. Virtual Memory

Sanal bellek, process'ler için soyut bellek adresleme sağlar:

```c
#include <sys/mman.h>
#include <unistd.h>

// Large pages ile bellek ayırma
void* allocate_large_pages(size_t size) {
    // Huge page desteği kontrolü
    size_t huge_page_size = 2 * 1024 * 1024;  // 2MB

    // Huge page sayısını hesapla
    size_t num_pages = (size + huge_page_size - 1) / huge_page_size;
    size_t aligned_size = num_pages * huge_page_size;

    // mmap ile huge page talep et
    void *ptr = mmap(NULL, aligned_size,
        PROT_READ | PROT_WRITE,
        MAP_PRIVATE | MAP_ANONYMOUS | MAP_HUGETLB,
        -1, 0);

    if (ptr == MAP_FAILED) {
        // Fallback: normal sayfalar
        ptr = mmap(NULL, size,
            PROT_READ | PROT_WRITE,
            MAP_PRIVATE | MAP_ANONYMOUS,
            -1, 0);
    }

    return ptr;
}

// Sanal bellek koruma
void protect_memory_region(void *addr, size_t size, int protection) {
    mprotect(addr, size, protection);
}

// Bellek haritalama
void* memory_map_file(const char *path, size_t *map_size) {
    int fd = open(path, O_RDONLY);
    struct stat st;
    fstat(fd, &st);

    *map_size = st.st_size;
    void *ptr = mmap(NULL, *map_size, PROT_READ, MAP_PRIVATE, fd, 0);
    close(fd);

    return ptr;
}

// Bellek senkronizasyonu
void sync_memory(void *addr, size_t size) {
    msync(addr, size, MS_SYNC | MS_INVALIDATE);
}
```

### 2. Page Tables

Sayfa tabloları, sanal-bedeni bellek eşleme:

```c
// Sayfa tablosu yapısı
typedef struct {
    uint64_t present : 1;
    uint64_t writable : 1;
    uint64_t user : 1;
    uint64_t accessed : 1;
    uint64_t dirty : 1;
    uint64_t nx : 1;
    uint64_t frame : 40;
} PageTableEntry;

typedef struct {
    PageTableEntry entries[512];
} PageTable;

// Sayfa tablosu oluşturma
PageTable* create_page_table() {
    PageTable *table = aligned_alloc(4096, sizeof(PageTable));
    memset(table, 0, sizeof(PageTable));
    return table;
}

// Sayfa ayırma
void map_page(PageTable *pml4, uint64_t virtual_addr, uint64_t physical_addr, uint64_t flags) {
    // PML4 indeksini hesapla
    int pml4_index = (virtual_addr >> 39) & 0x1FF;
    int pdpt_index = (virtual_addr >> 30) & 0x1FF;
    int pd_index = (virtual_addr >> 21) & 0x1FF;
    int pt_index = (virtual_addr >> 12) & 0x1FF;

    // Seviye 3 tablosunu bul veya oluştur
    if (!pml4->entries[pml4_index].present) {
        PageTable *pdpt = create_page_table();
        pml4->entries[pml4_index].present = 1;
        pml4->entries[pml4_index].writable = 1;
        pml4->entries[pml4_index].frame = (uint64_t)pdpt >> 12;
    }
    PageTable *pdpt = (PageTable *)(pml4->entries[pml4_index].frame << 12);

    // Seviye 2 tablosunu bul veya oluştur
    if (!pdpt->entries[pdpt_index].present) {
        PageTable *pd = create_page_table();
        pdpt->entries[pdpt_index].present = 1;
        pdpt->entries[pdpt_index].writable = 1;
        pdpt->entries[pdpt_index].frame = (uint64_t)pd >> 12;
    }
    PageTable *pd = (PageTable *)(pdpt->entries[pdpt_index].frame << 12);

    // Seviye 1 tablosunu bul veya oluştur
    if (!pd->entries[pd_index].present) {
        PageTable *pt = create_page_table();
        pd->entries[pd_index].present = 1;
        pd->entries[pd_index].writable = 1;
        pd->entries[pd_index].frame = (uint64_t)pt >> 12;
    }
    PageTable *pt = (PageTable *)(pd->entries[pd_index].frame << 12);

    // Sayfayı haritala
    pt->entries[pt_index].present = 1;
    pt->entries[pt_index].writable = (flags & 0x2) ? 1 : 0;
    pt->entries[pt_index].user = (flags & 0x4) ? 1 : 0;
    pt->entries[pt_index].frame = physical_addr >> 12;
}

// TLB flush
void flush_tlb() {
    #ifdef __x86_64__
    asm volatile("mov %%cr3, %%rax\n\t"
                 "mov %%rax, %%cr3\n\t" ::: "rax");
    #endif
}
```

### 3. Memory Pool

Bellek havuzları, sabit boyutlu nesneler için hızlı bellek ayırma:

```c
#include <stdatomic.h>

// Memory pool yapısı
typedef struct MemoryBlock {
    struct MemoryBlock *next;
} MemoryBlock;

typedef struct {
    void *memory;
    size_t block_size;
    size_t block_count;
    MemoryBlock *free_list;
    atomic_int lock;
} MemoryPool;

// Pool oluşturma
MemoryPool* memory_pool_create(size_t block_size, size_t block_count) {
    MemoryPool *pool = malloc(sizeof(MemoryPool));
    pool->block_size = block_size;
    pool->block_count = block_count;
    pool->lock = 0;

    // Pool belleğini ayır
    size_t total_size = block_size * block_count;
    pool->memory = mmap(NULL, total_size,
        PROT_READ | PROT_WRITE,
        MAP_PRIVATE | MAP_ANONYMOUS, -1, 0);

    // Free list'i oluştur
    pool->free_list = NULL;
    for (size_t i = 0; i < block_count; i++) {
        MemoryBlock *block = (MemoryBlock *)((char *)pool->memory + i * block_size);
        block->next = pool->free_list;
        pool->free_list = block;
    }

    return pool;
}

// Block ayırma (lock-free)
void* memory_pool_alloc(MemoryPool *pool) {
    MemoryBlock *old_head;
    MemoryBlock *new_head;

    do {
        old_head = pool->free_list;
        if (!old_head) return NULL;
        new_head = old_head->next;
    } while (!atomic_compare_exchange_weak(&pool->free_list, &old_head, new_head));

    return old_head;
}

// Block serbest bırakma (lock-free)
void memory_pool_free(MemoryPool *pool, void *ptr) {
    MemoryBlock *block = (MemoryBlock *)ptr;
    MemoryBlock *old_head;

    do {
        old_head = pool->free_list;
        block->next = old_head;
    } while (!atomic_compare_exchange_weak(&pool->free_list, &old_head, block));
}

// Pool temizleme
void memory_pool_destroy(MemoryPool *pool) {
    munmap(pool->memory, pool->block_size * pool->block_count);
    free(pool);
}
```

### 4. Slab Allocator

Slab allocator, sık kullanılan nesneler için optimize edilmiş bellek yöneticisi:

```c
// Slab yapısı
typedef struct Slab {
    struct Slab *next;
    void *objects;
    uint64_t *bitmap;
    size_t object_count;
    size_t in_use;
} Slab;

typedef struct {
    size_t object_size;
    size_t objects_per_slab;
    size_t slab_size;
    Slab *slabs;
    Slab *partial;
    atomic_int lock;
} SlabAllocator;

// Slab allocator oluşturma
SlabAllocator* slab_allocator_create(size_t object_size, size_t objects_per_slab) {
    SlabAllocator *allocator = malloc(sizeof(SlabAllocator));
    allocator->object_size = object_size;
    allocator->objects_per_slab = objects_per_slab;

    // Slab boyutunu hesapla (16-byte alignment)
    size_t bitmap_size = (objects_per_slab + 63) / 64 * sizeof(uint64_t);
    allocator->slab_size = sizeof(Slab) + bitmap_size +
                          object_size * objects_per_slab;
    allocator->slab_size = (allocator->slab_size + 15) & ~15;

    allocator->slabs = NULL;
    allocator->partial = NULL;
    allocator->lock = 0;

    return allocator;
}

// Yeni slab oluştur
Slab* slab_create(SlabAllocator *allocator) {
    Slab *slab = mmap(NULL, allocator->slab_size,
        PROT_READ | PROT_WRITE,
        MAP_PRIVATE | MAP_ANONYMOUS, -1, 0);

    slab->objects = (char *)slab + sizeof(Slab) +
                   ((allocator->objects_per_slab + 63) / 64 * sizeof(uint64_t));
    slab->bitmap = (uint64_t *)((char *)slab + sizeof(Slab));
    slab->object_count = allocator->objects_per_slab;
    slab->in_use = 0;

    // Bitmap'i sıfırla
    memset(slab->bitmap, 0, (allocator->objects_per_slab + 63) / 64 * sizeof(uint64_t));

    return slab;
}

// Nesne ayırma
void* slab_alloc(SlabAllocator *allocator) {
    // Partial slab'lardan ara
    Slab *slab = allocator->partial;
    while (slab) {
        for (size_t i = 0; i < slab->object_count; i++) {
            size_t word = i / 64;
            size_t bit = i % 64;

            if (!(slab->bitmap[word] & (1ULL << bit))) {
                // Boş slot buldu
                slab->bitmap[word] |= (1ULL << bit);
                slab->in_use++;

                // Partial list'ten çıkar eğer tamamlandıysa
                if (slab->in_use == slab->object_count) {
                    // Partial list'ten çıkar
                }

                return (char *)slab->objects + i * allocator->object_size;
            }
        }
        slab = slab->next;
    }

    // Yeni slab oluştur
    slab = slab_create(allocator);
    slab->next = allocator->slabs;
    allocator->slabs = slab;

    // İlk nesneyi ata
    slab->bitmap[0] = 1;
    slab->in_use = 1;

    return slab->objects;
}

// Nesne serbest bırakma
void slab_free(SlabAllocator *allocator, void *ptr) {
    Slab *slab = allocator->slabs;
    while (slab) {
        char *start = slab->objects;
        char *end = start + allocator->object_size * slab->object_count;

        if (ptr >= start && ptr < end) {
            size_t index = ((char *)ptr - start) / allocator->object_size;
            size_t word = index / 64;
            size_t bit = index % 64;

            slab->bitmap[word] &= ~(1ULL << bit);
            slab->in_use--;

            // Partial list'e ekle
            if (slab->in_use < slab->object_count && slab->in_use > 0) {
                // Partial list'e ekle
            }

            return;
        }
        slab = slab->next;
    }
}
```

### 5. Buffer Management

Ses verisi için optimize edilmiş buffer yönetimi:

```c
// Ring buffer yapısı
typedef struct {
    void *buffer;
    size_t size;
    size_t head;
    size_t tail;
    atomic_int lock;
    size_t overruns;
} RingBuffer;

// Ring buffer oluşturma
RingBuffer* ring_buffer_create(size_t size) {
    RingBuffer *rb = malloc(sizeof(RingBuffer));
    rb->buffer = mmap(NULL, size * 2,  // Mirrored ring buffer
        PROT_READ | PROT_WRITE,
        MAP_PRIVATE | MAP_ANONYMOUS, -1, 0);
    rb->size = size;
    rb->head = 0;
    rb->tail = 0;
    rb->lock = 0;
    rb->overruns = 0;

    // İkinci yarıyı ilk yarıya kopyala (mirroring)
    memcpy((char *)rb->buffer + size, rb->buffer, size);

    return rb;
}

// Ring buffer'a yazma
size_t ring_buffer_write(RingBuffer *rb, const void *data, size_t len) {
    size_t available = rb->size - (rb->head - rb->tail);
    if (len > available) {
        rb->overruns++;
        len = available;
    }

    memcpy((char *)rb->buffer + rb->head, data, len);

    // Mirrored bölgeye de yaz
    memcpy((char *)rb->buffer + rb->head + rb->size, data, len);

    rb->head = (rb->head + len) % rb->size;
    return len;
}

// Ring buffer'dan okuma
size_t ring_buffer_read(RingBuffer *rb, void *data, size_t len) {
    size_t available = rb->head - rb->tail;
    if (len > available) {
        len = available;
    }

    memcpy(data, (char *)rb->buffer + rb->tail, len);
    rb->tail = (rb->tail + len) % rb->size;
    return len;
}

// Double buffer (zaman uyumsuz işleme için)
typedef struct {
    void *buffers[2];
    size_t size;
    atomic_int active_buffer;
    atomic_int processing;
} DoubleBuffer;

DoubleBuffer* double_buffer_create(size_t size) {
    DoubleBuffer *db = malloc(sizeof(DoubleBuffer));
    db->buffers[0] = mmap(NULL, size, PROT_READ | PROT_WRITE,
        MAP_PRIVATE | MAP_ANONYMOUS, -1, 0);
    db->buffers[1] = mmap(NULL, size, PROT_READ | PROT_WRITE,
        MAP_PRIVATE | MAP_ANONYMOUS, -1, 0);
    db->size = size;
    db->active_buffer = 0;
    db->processing = 0;
    return db;
}

// Double buffer swap
void double_buffer_swap(DoubleBuffer *db) {
    db->active_buffer = 1 - db->active_buffer;
}

// Triple buffer (üçlü buffer - senza DispatchQueue için)
typedef struct {
    void *buffers[3];
    size_t size;
    atomic_int write_buffer;
    atomic_int read_buffer;
    atomic_int display_buffer;
} TripleBuffer;

TripleBuffer* triple_buffer_create(size_t size) {
    TripleBuffer *tb = malloc(sizeof(TripleBuffer));
    for (int i = 0; i < 3; i++) {
        tb->buffers[i] = mmap(NULL, size, PROT_READ | PROT_WRITE,
            MAP_PRIVATE | MAP_ANONYMOUS, -1, 0);
    }
    tb->size = size;
    tb->write_buffer = 0;
    tb->read_buffer = 1;
    tb->display_buffer = 2;
    return tb;
}
```

## API / Arayüz

### COREMUSIC Memory API Başlık Dosyası

```c
#ifndef COREMUSIC_MEMORY_H
#define COREMUSIC_MEMORY_H

#include <stdint.h>
#include <stddef.h>

// Virtual Memory
CM_Status CM_VirtualAlloc(size_t size, int flags, void **ptr);
CM_Status CM_VirtualFree(void *ptr, size_t size);
CM_Status CM_VirtualProtect(void *ptr, size_t size, int protection);
CM_Status CM_VirtualLock(void *ptr, size_t size);
CM_Status CM_VirtualUnlock(void *ptr, size_t size);

// Large Pages
CM_Status CM_LargePageAlloc(size_t size, void **ptr);
CM_Status CM_LargePageFree(void *ptr, size_t size);

// Memory Pool
CM_Status CM_MemoryPool_Create(size_t block_size, size_t block_count, void **pool);
CM_Status CM_MemoryPool_Alloc(void *pool, void **ptr);
CM_Status CM_MemoryPool_Free(void *pool, void *ptr);
CM_Status CM_MemoryPool_Destroy(void *pool);

// Slab Allocator
CM_Status CM_SlabAllocator_Create(size_t object_size, size_t objects_per_slab, void **alloc);
CM_Status CM_SlabAlloc(void *alloc, void **ptr);
CM_Status CM_SlabFree(void *alloc, void *ptr);
CM_Status CM_SlabAllocator_Destroy(void *alloc);

// Ring Buffer
CM_Status CM_RingBuffer_Create(size_t size, void **rb);
CM_Status CM_RingBuffer_Write(void *rb, const void *data, size_t len, size_t *written);
CM_Status CM_RingBuffer_Read(void *rb, void *data, size_t len, size_t *read);
CM_Status CM_RingBuffer_Destroy(void *rb);

// Double Buffer
CM_Status CM_DoubleBuffer_Create(size_t size, void **db);
CM_Status CM_DoubleBuffer_Swap(void *db);
CM_Status CM_DoubleBuffer_GetWritePtr(void *db, void **ptr);
CM_Status CM_DoubleBuffer_GetReadPtr(void *db, void **ptr);
CM_Status CM_DoubleBuffer_Destroy(void *db);

#endif // COREMUSIC_MEMORY_H
```

## Bağımlılıklar

### Gereksinimler
- Linux Kernel 2.6+
- Windows 10+ (large page support)
- POSIX (mmap, mprotect)

### Alt Katmanlar
- CPU (MMU, TLB)
- Donanım bellek

### Üst Katmanlar
- K0 Threading Model
- K1 Ses Motoru
- K3 Uygulama Katmanı

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Pool alloc/free | < 50ns | Belirlenecek |
| Slab alloc/free | < 100ns | Belirlenecek |
| Ring buffer write | < 200ns | Belirlenecek |
| Large page alloc | < 1ms | Belirlenecek |
| Page fault latency | < 5μs | Belirlenecek |

## Durum: Implementasyon

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Ring buffer implementasyonu (ses buffer yönetimi için kritik)
2. Memory pool implementasyonu
3. Slab allocator implementasyonu
4. Large page desteği
5. Double/Triple buffer

**Sonraki Adımlar**:
- Ring buffer performans testlerinin yapılması
- Memory pool'un ses processing pipeline'ına entegrasyonu
- Slab allocator'ın常用 nesne boyutlarının belirlenmesi
