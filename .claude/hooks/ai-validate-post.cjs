#!/usr/bin/env node
/**
 * ai-validate-post.cjs — PostToolUse (Edit|Write)
 *
 * Q6 validation enforce: .ai/** altındaki .md dosyası değiştirildikten SONRA
 * validate.mjs --check çalışır; ihlal varsa {"decision":"block"} ile Claude'a
 * geri besleme yapılır (düzeltmesi zorunlu). Temizse sessiz (exit 0).
 *
 * Emniyet: .ai DIŞI her şeyde ve doğrulama hatasında sessiz exit 0
 * (validator kendi kendine crash ederse gate yanlışlıkla kilitlemez — sadece
 * validator exit 1 İHLAL bildirirken block verilir).
 */
'use strict';
const fs = require('fs');
const path = require('path');
const { spawnSync } = require('child_process');

let input = '';
try { input = fs.readFileSync(0, 'utf8'); } catch { process.exit(0); }

let data;
try { data = JSON.parse(input); } catch { process.exit(0); }

const fp = (data.tool_input && (data.tool_input.file_path || data.tool_input.filePath)) || '';
if (!fp) process.exit(0);

const project = process.env.CLAUDE_PROJECT_DIR || process.cwd();
let rel = path.isAbsolute(fp) ? path.relative(project, fp) : fp;
rel = rel.replace(/\\/g, '/');

if (!/^\.ai\/.+\.md$/i.test(rel)) process.exit(0);          // yalnız .ai .md

const validator = path.join(project, '.ai', 'scripts', 'validate.mjs');
if (!fs.existsSync(validator)) process.exit(0);

const r = spawnSync(process.execPath, [validator, '--check'], { encoding: 'utf8', cwd: project });
if (r.status === 1) {
  const lines = (r.stdout || '').trim().split('\n').slice(0, 12).join(' | ');
  process.stdout.write(JSON.stringify({
    decision: 'block',
    reason: 'Control Plane validator İHLAL — .ai/scripts/validate.mjs --check başarısız. Düzelt + tekrar dene. ' + lines,
  }));
}
process.exit(0);
