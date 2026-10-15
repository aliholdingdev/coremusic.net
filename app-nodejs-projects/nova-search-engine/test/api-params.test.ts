import { test } from 'node:test';
import assert from 'node:assert/strict';
import { boolParam } from '../src/apps/api.ts';

/* ---------- boolParam: tüm boolean parametreler 1/0 VE true/false ile çalışır ---------- */

function url(params: string): URL {
  return new URL(`http://x/api/v1/search?q=t${params ? '&' + params : ''}`);
}

test('boolParam: 1/true/yes/on → true', () => {
  for (const v of ['1', 'true', 'True', 'TRUE', 'yes', 'on', 'YES']) {
    assert.equal(boolParam(url(`detail=${v}`), 'detail'), true, `detail=${v}`);
  }
});

test('boolParam: 0/false/no/off → false', () => {
  for (const v of ['0', 'false', 'False', 'no', 'off', 'NO']) {
    assert.equal(boolParam(url(`ai=${v}`), 'ai'), false, `ai=${v}`);
  }
});

test('boolParam: param yok/geçersiz → default (ai=false, detail=true)', () => {
  assert.equal(boolParam(url(''), 'ai'), false);
  assert.equal(boolParam(url('ai='), 'ai'), false);
  assert.equal(boolParam(url('ai=bilinmeyen'), 'ai'), false);
  // detail default açık: param verilmezse bile enrichment çalışır (boş/null alanlar webden kazınır)
  assert.equal(boolParam(url(''), 'detail', true), true);
  assert.equal(boolParam(url('detail=0'), 'detail', true), false);
  assert.equal(boolParam(url('detail=false'), 'detail', true), false);
});
