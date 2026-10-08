/**
 * vitest.config.js — CoreMusic JS unit test configuration (WP2/J1)
 *
 * Runs with @vitest-environment jsdom
 * Coverage target: ≥80% (Soft Constraint §8.1 — CI'da continue-on-error job'ı)
 * include: tests/ (e2e-dışı component specs) + js/ altındaki in-tree primitive
 * spec'leri (2026-10-07: önce include 6/7 spec'i atlıyordu — fixed).
 */
import { defineConfig } from 'vitest/config';

export default defineConfig({
  test: {
    globals: true,
    environment: 'jsdom',
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html'],
      include: ['js/**/*.js'],
      exclude: [
        'node_modules/',
        'tests/',
        '**/*.spec.js',
        '**/*.test.js',
      ],
      lines: 80,
      functions: 80,
      branches: 80,
      statements: 80,
    },
    include: ['tests/**/*.spec.js', 'js/**/*.spec.js'],
    testTimeout: 5000,
  },
});
