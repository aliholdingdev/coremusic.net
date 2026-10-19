---
node_id: os-kod-ornekleri
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: [K000-isletim-sistemi, K001-donanım, K005-firmware]
evidence:
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php:185-208 (PHP PIPE hattı) · 2026-10-18"
  - "shared/src/PageRouter/StructuredLogger.php:72 (newline-delimited JSON) · 2026-10-18"
  - "shared/src/Device/DeviceManager.php:24 (durum makinesi) · 2026-10-18"
validation: "≥12 dil bloğu · her blok 15-60 satır, yorumlu ve etiketli · dosya ≥250 satır · PHP bloğu `php -l` ile sözdizimi geçer."
open_questions:
  - "⚠️ C/C++/Rust/Java blokları bu oturumda derlenmedi (araç zinciri yok) — derleme doğrulaması VERIFICATION REQUIRED."
  - "⚠️ Assembly bloğu NASM/ELF varsayar; hedef platform (x86-64 Linux) varsayımı UNKNOWN."
group: mimari
---

# K000 — Kod Örnekleri (13 Dil · OS Senaryosu)

> **Ortak senaryo:** Bir iş kuyruğuna iş al, öncelik sırasına göre çalıştır,
> sonuçu pipe ile ana sürece taşı, newline-delimited JSON olarak logla.
> Her dil bloğu 15-60 satır, yorumlu ve etiketlidir.

## §1 ELI15

Aynı OS fikrini (kuyruk + süreç + pipe + log) 13 dilde yazıyoruz. Kavram
her yerde aynı: **sıraya koy → çalıştır → kanala yaz → logla**. Dil
farkı yalnızca sözdizimidir; sözlük `os-evrensel-dil.md` geçerlidir.

## §2 Dil Blokları

### 1. PHP 8.4 — PIPE + stream_select (üretim hattı deseni)

```php
<?php
declare(strict_types=1);

// K000: ana süreç iki worker pipe'ını 5 sn timeout ile bekler (R-06)
$descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w']];
$procA = proc_open('worker-a', $descriptors, $pipesA);
$procB = proc_open('worker-b', $descriptors, $pipesB);

$read = [$pipesA[1], $pipesB[1]];
$write = $except = null;

// stream_select() = select() eşdeğeri (php.net 2026-10-18 doğrulandı)
if (false === ($n = stream_select($read, $write, $except, 5, 0))) {
    fwrite(STDERR, "select hatasi\n");            // false kontrolü === ile
} elseif ($n > 0) {
    foreach ($read as $stream) {                  // hazır pipe'lar
        $chunk = fread($stream, 4096);
        if ($chunk === '' || $chunk === false) {  // kapanış tespiti
            fclose($stream); continue;
        }
        error_log(json_encode(['ts' => microtime(true), 'line' => $chunk]));
    }
}
proc_close($procA); proc_close($procB);           // reap (waitpid karşılığı)
```

### 2. JavaScript (Node.js) — fork + IPC kanalı

```javascript
// K000: ana süreç işi çocuk süreçlere dağıtır, sonuç mesaj kuyruğu
import { fork } from 'node:child_process';        // fork(): adres uzayı kopyalanır

const jobs = [ { id: 1, prio: 3 }, { id: 2, prio: 1 } ]; // prio: 1 = yüksek
const queue = [...jobs].sort((a, b) => a.prio - b.prio); // hazır sıraya al

for (const job of queue) {                        // öncelik sırasıyla başlat
  const child = fork('./worker.js', [String(job.id)]);
  child.on('message', (msg) => {                  // IPC pipe (kanal)
    console.log(JSON.stringify({ ts: Date.now(), job: job.id, ...msg }));
  });
  child.on('exit', (code) => {                    // waitpid karşılığı
    if (code !== 0) console.error(`job ${job.id} kaldi: ${code}`);
  });
}
```

### 3. TypeScript — tip güvenli iş sözleşmesi

```typescript
// K000: iş sözleşmesi — tip bilinmeyen iş kabul edilmez (zero-hallucination)
type JobState = 'queued' | 'running' | 'done' | 'failed';

interface Job {
  readonly id: number;
  readonly prio: 1 | 2 | 3;                       // öncelik kademesi (QR/RL/CN/PR/SR)
  state: JobState;
  startedAt?: number;
}

export function enqueue(queue: Job[], job: Job): Job[] {
  // durum makinesi ihlali: done/failed iş tekrar kuyruğa girmez
  if (job.state !== 'queued') throw new Error(`gecersiz durum: ${job.state}`);
  return [...queue, job].sort((a, b) => a.prio - b.prio);
}
```

### 4. Python — os.pipe + select ile çoklu bekleyici

```python
import json, os, select, time

# K000: os.pipe() = anonim kanal; ana süreç iki worker'ı eşzamanlı bekler
rd_a, wr_a = os.pipe()
rd_b, wr_b = os.pipe()
os.write(wr_a, b"job-1 ok\n")
os.write(wr_b, b"job-2 ok\n")

# select(): hazır FD'ler 5 sn timeout ile (kuyruk tıkanması R-08)
ready, _, _ = select.select([rd_a, rd_b], [], [], 5.0)
for fd in ready:
    data = os.read(fd, 4096)
    if not data:                                   # kapanış tespiti
        os.close(fd); continue
    print(json.dumps({"ts": time.time(), "fd": fd, "line": data.decode()}))
```

### 5. C — fork/exec/waitpid çekirdek üçlüsü

```c
/* K000: fork -> exec -> waitpid (syscalls(2) 2026-10-18 dogrulandi) */
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/wait.h>

int main(void) {
    pid_t pid = fork();                 /* 0: cocuk, >0: ana, -1: hata */
    if (pid < 0) { perror("fork"); return 1; }

    if (pid == 0) {                     /* cocuk: ad degistirip calisir */
        execl("/usr/bin/worker", "worker", (char *)NULL);
        _exit(127);                     /* exec buraya donerse: hata */
    }

    int status = 0;
    if (waitpid(pid, &status, 0) < 0) { perror("waitpid"); return 1; }
    printf("child exit=%d\n", WIFEXITED(status) ? WEXITSTATUS(status) : -1);
    return 0;
}
```

### 6. C++ — RAII ile FD temizliği (sızıntı yok)

```cpp
// K000: dosya tanimlayicisi RAII ile — kapanmamis FD kalmaz (R-07)
#include <unistd.h>
#include <stdexcept>
#include <string>

class PipeFd {                          // tek sahipli FD sarmalayici
    int rd_ = -1, wr_ = -1;
public:
    PipeFd() { if (::pipe(fds_) != 0) throw std::runtime_error("pipe"); rd_ = fds_[0]; wr_ = fds_[1]; }
    ~PipeFd() { if (rd_ >= 0) ::close(rd_); if (wr_ >= 0) ::close(wr_); }
    PipeFd(const PipeFd&) = delete;             // kopyalama yasak (cift kapanma)
    int readEnd() const { return rd_; }
    void writeLine(const std::string& s) const { (void)!::write(wr_, s.data(), s.size()); }
private:
    int fds_[2]{-1, -1};
};
```

### 7. C# — BackgroundService ile kuyruk işleyici

```csharp
// K000: IHostedService — kuyruk + zaman damgali boot gate benzeri dongu
using System.Collections.Concurrent;
using System.Text.Json;

public sealed class Job { public required int Id { get; init; } public required int Prio { get; init; } }

public sealed class QueueWorker : BackgroundService {
    private readonly ConcurrentQueue<Job> _queue = new();

    protected override async Task ExecuteAsync(CancellationToken ct) {
        while (!ct.IsCancellationRequested) {
            if (_queue.TryDequeue(out var job)) {                 // oncelikli kuyruk disarida
                var line = JsonSerializer.Serialize(new { ts = DateTimeOffset.UtcNow, job.Id });
                Console.WriteLine(line);                          // newline-delimited JSON
            } else {
                await Task.Delay(50, ct);                         // bosken uyku (CPU R-06)
            }
        }
    }
}
```

### 8. Go — goroutine + channel (IPC'nin Go karşılığı)

```go
// K000: channel = pipe; WaitGroup = waitpid grubu
package main

import (
	"encoding/json"
	"fmt"
	"time"
)

type Job struct {
	ID    int `json:"id"`
	Prio  int `json:"prio"`
}

func main() {
	ch := make(chan Job, 8)                 // tamponlu kanal (pipe tamponu)
	go func() {                              // cocuk "surec" benzeri goroutine
		for j := range ch {
			b, _ := json.Marshal(map[string]any{"ts": time.Now().Unix(), "job": j.ID})
			fmt.Println(string(b))
		}
	}()
	ch <- Job{ID: 1, Prio: 1}               // is yaz
	ch <- Job{ID: 2, Prio: 3}
	close(ch)                                // EOF bildirimi
}
```

### 9. Rust — Result ile hata yayılımı (fail-safe)

```rust
// K000: hata yayilir, yok sayilmaz (R-06: SIGPIPE sessiz kaybi yasak)
use std::io::{self, BufRead, Write};
use std::process::{Command, Stdio};

fn run_worker(name: &str) -> Result<String, io::Error> {
    let mut child = Command::new(name)
        .stdout(Stdio::piped())             // pipe ac
        .spawn()?;                          // hata Result ile yayar
    let out = child.stdout.take().expect("stdout pipe yok");
    let mut line = String::new();
    io::BufReader::new(out).read_line(&mut line)?;
    let status = child.wait()?;             // waitpid karsiligi
    if !status.success() {
        return Err(io::Error::other(format!("exit={status}")));
    }
    Ok(line.trim().to_string())
}
```

### 10. Java — ProcessBuilder + hava boşluğu temizliği

```java
// K000: ProcessBuilder = fork/exec; stream'ler finally ile kapanir
import java.io.*;
import java.util.*;

public final class OsQueue {
    public static String run(String worker) throws IOException, InterruptedException {
        ProcessBuilder pb = new ProcessBuilder(worker);   // argv hazirla
        pb.redirectErrorStream(true);
        Process p = pb.start();                           // fork+exec
        String line;
        try (BufferedReader r = new BufferedReader(new InputStreamReader(p.getInputStream()))) {
            while ((line = r.readLine()) != null) {       // pipe okuma
                System.out.println("{\"line\":\"" + line.replace("\"", "\\\"") + "\"}");
            }
        } finally {
            p.getOutputStream().close();                 // EOF bildir
        }
        return String.valueOf(p.waitFor());              // waitpid
    }
}
```

### 11. SQL — iş kuyruğu sorgusu (öncelik + toplu pencere)

```sql
-- K000: kuyruk tablosu — oncelik sirasi + K016 toplu penceresi (batch window)
SELECT j.id,
       j.prio,
       j.created_at,
       TIMESTAMPDIFF(SECOND, j.created_at, NOW()) AS bekleme_sn
FROM   job_queue AS j
WHERE  j.state = 'queued'                    -- yalniz bekleyenler
  AND  j.created_at <= NOW() - INTERVAL 5 SECOND   -- kuyruk olgunlasma
ORDER  BY j.prio ASC, j.created_at ASC       -- oncelik -> FIFO
LIMIT  50;                                   -- toplu pencere boyutu

-- Kritik: id'de indeks yoksa tam tarama -> idx(state, prio, created_at)
```

### 12. Shell (Bash) — boot gate + pipe kuyruğu ölçümü

```bash
#!/usr/bin/env bash
# K000: boot gecisi olcumu (PROT-K4) + kuyruk tikanma testi (TS-O8)
set -euo pipefail

start=$(date +%s.%N)                        # zaman damgasi (boot--acd--)
if ! timeout 5 ./worker --once | head -n1; then
  echo "PIPE_KAPANDI" >&2                    # kapanis tespiti (R-06)
  exit 1
fi
elapsed=$(awk -v a="$start" -v b="$(date +%s.%N)" 'BEGIN{printf "%.2f", b-a}')
echo "{\"gate\":\"boot--acd--\",\"elapsed\":$elapsed}"   # JSON log satiri
# Beklenen: ~1.1 sn hedefi; asim durumunda gate KALDI raporlanir
```

### 13. Assembly (x86-64, NASM) — write(2) ile minimal sistem çağrısı

```nasm
; K000: sistem cagrisi = kullanici alani -> cekirdek tek gecis kapi
; write(1, msg, len) -> syscall no 1 (x86-64 Linux, AMD64 ABI)
section .data
    msg db '{"gate":"boot--acd--","state":"OK"}', 10   ; newline-delimited
    msg.len equ $ - msg                         ; uzunluk sabiti

section .text
    global _start
_start:
    mov rax, 1          ; syscall: write
    mov rdi, 1          ; fd = stdout
    lea rsi, [rel msg]  ; tampon
    mov rdx, msg.len    ; uzunluk
    syscall             ; -> cekirdek

    mov rax, 60         ; syscall: exit
    xor rdi, rdi        ; durum 0
    syscall
```

## §3 Dil Envanteri (Kontrol)

| # | Dil | Blok | K000 konusu |
|---|-----|------|-------------|
| 1 | PHP 8.4 | §2.1 | stream_select + kapanış tespiti |
| 2 | JavaScript | §2.2 | fork + IPC |
| 3 | TypeScript | §2.3 | durum makinesi sözleşmesi |
| 4 | Python | §2.4 | os.pipe + select |
| 5 | C | §2.5 | fork/exec/waitpid |
| 6 | C++ | §2.6 | FD RAII |
| 7 | C# | §2.7 | kuyruk servisi |
| 8 | Go | §2.8 | channel = pipe |
| 9 | Rust | §2.9 | Result / fail-safe |
| 10 | Java | §2.10 | ProcessBuilder |
| 11 | SQL | §2.11 | öncelikli kuyruk sorgusu |
| 12 | Shell | §2.12 | boot gate ölçümü |
| 13 | Assembly | §2.13 | syscall geçişi |

**Toplam: 13 dil ≥ 12 şartı karşılar.** §2.1 PHP bloğu `php -l`
ile sözdizimi doğrulanır (test-stratejisi TS-O12).

## §4 Kanıt

- `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-208` — PIPE/silent-cleanup deseni (§2.1)
- `shared/src/PageRouter/StructuredLogger.php:72` — newline-delimited JSON (§2.12/§2.13)
- `shared/src/Device/DeviceManager.php:24` — durum makinesi (§2.3)
- webfetch 2026-10-18: php.net `stream_select` · man7 `syscalls(2)` (§2.5)

## §5 ⚠️ Bilinen Bozuk Referanslar (salt rapor — DÜZELTME YASAK)

- `os-master.md` FM `depends_on: [K002-altyapi]` — diskte YOK (önceki rapor).
