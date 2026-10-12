#!/usr/bin/env node
/**
 * ai-risk-gate.cjs — PreToolUse (Edit|Write)
 *
 * Q3 onay matrisi HIGH sınıfı uygulaması: T1/T2 otorite dosyalarına yazma,
 * İNSAN onayı (permissionDecision: "ask") gerektirir.
 *
 * Emniyet: her koşulda exit 0; ask yalnız HIGH risk dosyada.
 */
'use strict';
const fs = require('fs');
const path = require('path');

const HIGH_RISK_FILES = new Set([
  'CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md', 'ULTRA-THINKING.md',
]);
const HIGH_RISK_DIRS = ['.rules']; // .ai/.rules/** → HIGH

let input = '';
try { input = fs.readFileSync(0, 'utf8'); } catch { process.exit(0); }

let data;
try { data = JSON.parse(input); } catch { process.exit(0); }

const fp = (data.tool_input && (data.tool_input.file_path || data.tool_input.filePath)) || '';
if (!fp) process.exit(0);

const project = process.env.CLAUDE_PROJECT_DIR || process.cwd();
let rel = fp;
if (path.isAbsolute(fp)) {
  rel = path.relative(project, fp);
}
rel = rel.replace(/\\/g, '/');

// Kök T1/T2 boot dosyaları (Q32/R10 — .ai/ DIŞINDA da HIGH risk)
const ROOT_HIGH = new Set(['CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md']);
if (ROOT_HIGH.has(rel)) {
  process.stdout.write(JSON.stringify({
    hookSpecificOutput: {
      hookEventName: 'PreToolUse',
      permissionDecision: 'ask',
      permissionDecisionReason:
        'Control Plane HIGH-RISK kapısı (Q32): kök boot dosyası ' + rel +
        ' T1/T2 otorite yüzeyidir — İNSAN (Bayram Ali) onayı gerekir. ' +
        'Değişiklik risk: high · Doğrulama: node .ai/scripts/validate.mjs --check',
    },
  }));
  process.exit(0);
}

// yalnız .ai altındaki dosyalar
const m = rel.match(/^\.ai\/(.+)$/);
if (!m) process.exit(0);
const inner = m[1];

const isHigh =
  HIGH_RISK_FILES.has(inner) ||
  HIGH_RISK_DIRS.some(d => inner === d || inner.startsWith(d + '/'));

if (isHigh) {
  const out = {
    hookSpecificOutput: {
      hookEventName: 'PreToolUse',
      permissionDecision: 'ask',
      permissionDecisionReason:
        'Control Plane HIGH-RISK kapısı (Q3 onay matrisi): .ai/' + inner +
        ' T1/T2 otorite dosyasıdır — İNSAN (Bayram Ali) onayı gerekir. ' +
        'Değişiklik risk: high · Doğrulama: node .ai/scripts/validate.mjs --check',
    },
  };
  process.stdout.write(JSON.stringify(out));
}
process.exit(0);
