import { test } from 'node:test';
import assert from 'node:assert/strict';
import { fmtDuration, fmtViews } from '../src/cli/format.ts';

test('süre biçimi', () => {
  assert.equal(fmtDuration(0), '--:--');
  assert.equal(fmtDuration(59), '0:59');
  assert.equal(fmtDuration(125), '2:05');
  assert.equal(fmtDuration(3661), '1:01:01');
});

test('görüntülenme biçimi', () => {
  assert.equal(fmtViews(999), '999');
  assert.equal(fmtViews(1500), '1.5 B');
  assert.equal(fmtViews(2_400_000), '2.4 M');
  assert.equal(fmtViews(1_200_000_000), '1.2 Mr');
});
