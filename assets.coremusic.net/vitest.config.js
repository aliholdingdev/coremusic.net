/**
 * vitest.config.js — Unit test configuration for PlayerInfoComponent
 * 
 * Runs with @vitest-environment jsdom
 * Coverage target: ≥80%
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
    include: ['tests/**/*.spec.js'],
    testTimeout: 5000,
  },
});
