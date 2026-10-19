// ===========================================
// Job Queue — basit, kuyruklu, yeniden denemeli
// İş mantığı: kullanıcı araması asla scraping'i
// beklemez; iş kuyruğa alınır, worker arkada işler.
// ===========================================

import { backoffMs, sleep } from '@novasearch/common';

export interface Job<T = unknown> {
  id: number;
  payload: T;
  attempts: number;
}

export type JobHandler<T> = (payload: T) => Promise<void>;

export interface QueueStats {
  queued: number;
  processing: number;
  completed: number;
  failed: number;
}

export class JobQueue<T> {
  private items: Job<T>[] = [];
  private processing = 0;
  private completed = 0;
  private failed = 0;
  private nextId = 1;
  private running = false;

  constructor(
    private handler: JobHandler<T>,
    private concurrency = 2,
    private maxAttempts = 3,
  ) {}

  push(payload: T): number {
    const id = this.nextId++;
    this.items.push({ id, payload, attempts: 0 });
    return id;
  }

  start(): void {
    if (this.running) return;
    this.running = true;
    void this.loop();
  }

  stop(): void {
    this.running = false;
  }

  stats(): QueueStats {
    return {
      queued: this.items.length,
      processing: this.processing,
      completed: this.completed,
      failed: this.failed,
    };
  }

  private async loop(): Promise<void> {
    while (this.running) {
      if (this.items.length === 0 || this.processing >= this.concurrency) {
        await sleep(100);
        continue;
      }
      const job = this.items.shift();
      if (!job) continue;
      this.processing++;
      void this.process(job);
    }
  }

  private async process(job: Job<T>): Promise<void> {
    try {
      await this.handler(job.payload);
      this.completed++;
    } catch {
      job.attempts++;
      if (job.attempts < this.maxAttempts) {
        await sleep(backoffMs(job.attempts));
        this.items.push(job);
      } else {
        this.failed++;
      }
    } finally {
      this.processing--;
    }
  }
}
