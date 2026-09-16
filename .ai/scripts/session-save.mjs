#!/usr/bin/env node
// session-save.mjs — CoreMusic session durumu kaydetme aracı
//
// İşlem sonrası session durumunu kaydeder:
//   1. .ai/sessions/YYYY-MM-DD-HH-MM-SS.md dosyası oluşturur
//   2. .ai/MEMORY.md §18 (Session History) tabloya satır ekler
//   3. .ai/MEMORY.md §20 (Current Session State) günceller
//   4. .ai/log.md'ye audit trail ekler
//   5. .ai/memory/context/project-state.md günceller
//
// Tüm write işlemleri vault-utf8-writer.mjs üzerinden yapılır.
//
// Kullanım:
//   node .ai/scripts/session-save.mjs --task "Görev açıklaması" --status completed|partial|failed --agent mo
//   node .ai/scripts/session-save.mjs --task "Görev" --status completed --agent mo --files "dosya1.php,dosya2.js"

import fs from 'node:fs';
import path from 'node:path';
import { execSync } from 'node:child_process';

const CWD = process.cwd();
const AI_DIR = path.join(CWD, '.ai');
const SCRIPTS_DIR = path.join(AI_DIR, 'scripts');
const SESSIONS_DIR = path.join(AI_DIR, 'sessions');
const CONTEXT_DIR = path.join(AI_DIR, 'sessions', 'context');
const WRITER = path.join(SCRIPTS_DIR, 'vault-utf8-writer.mjs');

// --- Argüman analizi ---
function argOf(name) {
  const args = process.argv.slice(2);
  const i = args.indexOf(name);
  return i >= 0 ? args[i + 1] : undefined;
}

const task = argOf('--task');
const status = argOf('--status');
const agent = argOf('--agent') || 'mo';
const filesArg = argOf('--files') || '';

if (!task || !status) {
  console.error('Kullanım: node session-save.mjs --task "Görev" --status completed|partial|failed [--agent mo] [--files "dosya1,dosya2"]');
  process.exit(1);
}

if (!['completed', 'partial', 'failed'].includes(status)) {
  console.error('HATA: --status completed, partial veya failed olmalı');
  process.exit(1);
}

// --- Tarih Formatı ---
function pad(n) { return String(n).padStart(2, '0'); }
const now = new Date();
const ts = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}-${pad(now.getHours())}-${pad(now.getMinutes())}-${pad(now.getSeconds())}`;
const dateStr = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
const timeStr = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

// --- vault-utf8-writer ile yazma ---
function writerAppend(filePath, entryContent) {
  const tmpFile = path.join(SCRIPTS_DIR, '.tmp-session-entry.md');
  fs.writeFileSync(tmpFile, entryContent, 'utf8');
  try {
    execSync(`node "${WRITER}" append --file "${filePath}" --entry-file "${tmpFile}"`, { stdio: 'inherit' });
  } finally {
    try { fs.unlinkSync(tmpFile); } catch {}
  }
}

function writerWrite(filePath, content) {
  const tmpFile = path.join(SCRIPTS_DIR, '.tmp-session-write.md');
  fs.writeFileSync(tmpFile, content, 'utf8');
  try {
    execSync(`node "${WRITER}" write --file "${filePath}" --content-file "${tmpFile}"`, { stdio: 'inherit' });
  } finally {
    try { fs.unlinkSync(tmpFile); } catch {}
  }
}

// --- Dosya okuma ---
function readFile(relPath) {
  const fullPath = path.join(CWD, relPath);
  if (!fs.existsSync(fullPath)) return '';
  return fs.readFileSync(fullPath, 'utf8').replace(/^﻿/, '');
}

// --- 1. Session dosyası oluştur ---
function createSessionFile() {
  const sessionFile = path.join(SESSIONS_DIR, `${ts}.md`);
  const filesList = filesArg ? filesArg.split(',').map(f => f.trim()).filter(Boolean) : [];
  const statusEmoji = status === 'completed' ? '✅' : status === 'partial' ? '⚠️' : '❌';

  const content = `# Session: ${task} — ${dateStr}

## Context
${task}

## Status: ${statusEmoji} ${status.toUpperCase()}

## Agent
${agent}

---

## Changes Made

${filesList.length > 0
    ? filesList.map((f, i) => `### ${i + 1}. \`${f}\`\n**Change:** Updated\n**Why:** Part of task: ${task}`).join('\n\n')
    : '_(No files specified)_'}
${filesList.length > 0 ? '' : ''}
---

## Key Files
${filesList.length > 0 ? filesList.map(f => `- \`${f}\``).join('\n') : '- _No files tracked_'}

## Known Issues
- _None reported_

---

**Saved by:** session-save.mjs
**Timestamp:** ${dateStr} ${timeStr}
`;

  fs.mkdirSync(SESSIONS_DIR, { recursive: true });
  fs.writeFileSync(sessionFile, content, 'utf8');
  console.log(`Session dosyası oluşturuldu: ${sessionFile}`);
  return sessionFile;
}

// --- 2. MEMORY.md güncelle ---
function updateMemoryMd() {
  const memoryPath = path.join(AI_DIR, 'MEMORY.md');
  let content = readFile('.ai/MEMORY.md');

  if (!content) {
    console.error('HATA: .ai/MEMORY.md okunamadı');
    return;
  }

  // §18 Session History tablosuna satır ekle
  const statusEmoji = status === 'completed' ? '✅' : status === 'partial' ? '⚠️' : '❌';
  const newHistoryRow = `| ${dateStr} | ${task} | ${statusEmoji} ${status} | — | ${agent} |`;

  // Tablonun son satırını bul ve ekle
  const historyMarker = '## 18. Session History';
  const historyIdx = content.indexOf(historyMarker);
  if (historyIdx >= 0) {
    // Tablo başlığını bul
    const tableHeader = '| Tarih | Konu | Durum | ADR | Agent |';
    const tableSep = '|-------|------|-------|-----|-------|';
    const headerIdx = content.indexOf(tableHeader, historyIdx);
    const sepIdx = content.indexOf(tableSep, headerIdx);

    if (headerIdx >= 0 && sepIdx >= 0) {
      // Tablonun son satırını bul (bir sonraki ## bölümüne kadar)
      const afterSep = sepIdx + tableSep.length;
      let lastRowIdx = afterSep;
      let lineStart = afterSep;

      while (lineStart < content.length) {
        const nextNewline = content.indexOf('\n', lineStart);
        if (nextNewline < 0) break;
        const line = content.substring(lineStart, nextNewline).trim();
        if (line.startsWith('##') || line === '') {
          lastRowIdx = lineStart;
          break;
        }
        lastRowIdx = nextNewline;
        lineStart = nextNewline + 1;
      }

      // Yeni satırı ekle
      content = content.slice(0, lastRowIdx) + '\n' + newHistoryRow + '\n' + content.slice(lastRowIdx);
    }
  }

  // §20 Current Session State güncelle
  const stateMarker = '## 20. Current Session State';
  const stateIdx = content.indexOf(stateMarker);
  if (stateIdx >= 0) {
    const stateTable = `| Ozellik | Deger |
|---------|-------|
| Session Date | ${dateStr} |
| Active Task | ${task} |
| Domain | Active Development |
| Last Action | Session saved: ${task} (${status}) |
| Changed Files | ${filesArg || 'N/A'} |
| Known Issue | _None_ |`;

    // Mevcut tabloyu bul ve değiştir
    const tableStart = content.indexOf('| Ozellik | Deger |', stateIdx);
    if (tableStart >= 0) {
      // Tablonun sonunu bul
      let tableEnd = tableStart;
      let lineStart = tableStart;
      while (lineStart < content.length) {
        const nextNewline = content.indexOf('\n', lineStart);
        if (nextNewline < 0) break;
        const line = content.substring(lineStart, nextNewline).trim();
        if (line.startsWith('##') || line === '') {
          tableEnd = lineStart;
          break;
        }
        tableEnd = nextNewline;
        lineStart = nextNewline + 1;
      }
      content = content.slice(0, tableStart) + stateTable + '\n\n' + content.slice(tableEnd);
    }
  }

  writerWrite(memoryPath, content);
  console.log('MEMORY.md güncellendi');
}

// --- 3. log.md'ye append ---
function appendToLog() {
  const logPath = path.join(AI_DIR, 'log.md');
  const statusEmoji = status === 'completed' ? '✅' : status === 'partial' ? '⚠️' : '❌';
  const entry = `\n[${dateStr} ${timeStr}] [INFO] [session-manager] [SAVE] Session ${statusEmoji} ${status}: "${task}" (agent: ${agent}) | session-file: ${ts}.md\n`;

  writerAppend(logPath, entry);
  console.log('log.md güncellendi');
}

// --- 4. project-state.md güncelle ---
function updateProjectState() {
  const statePath = path.join(CONTEXT_DIR, 'project-state.md');
  fs.mkdirSync(CONTEXT_DIR, { recursive: true });

  const content = `# Project State — ${dateStr}

## Last Session
| Ozellik | Deger |
|---------|-------|
| Date | ${dateStr} |
| Task | ${task} |
| Status | ${status} |
| Agent | ${agent} |
| Session File | ${ts}.md |

## Changed Files
${filesArg ? filesArg.split(',').map(f => `- ${f.trim()}`).join('\n') : '- _No files tracked_'}

## Next Session Instructions
1. Read \`.ai/MEMORY.md\` §20 (Current Session State)
2. Check if task "${task}" needs continuation
3. Read session file: \`.ai/sessions/${ts}.md\`
4. Read context: \`.ai/sessions/context/project-state.md\`
`;

  writerWrite(statePath, content);
  console.log('project-state.md güncellendi');
}

// --- Ana akış ---
console.log(`\n=== Session Save ===`);
console.log(`Görev: ${task}`);
console.log(`Durum: ${status}`);
console.log(`Agent: ${agent}`);
console.log(`Tarih: ${dateStr} ${timeStr}`);
console.log('');

try {
  createSessionFile();
  updateMemoryMd();
  appendToLog();
  updateProjectState();
  console.log('\n✅ Session kaydı tamamlandı.');
} catch (err) {
  console.error('\n❌ Session kaydı hatası:', err.message);
  process.exit(1);
}
