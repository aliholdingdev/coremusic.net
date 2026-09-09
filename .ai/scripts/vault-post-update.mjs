#!/usr/bin/env node
// vault-post-update.mjs — CoreMusic vault post-operation güncelleme aracı
//
// İşlem sonrası 12 root .ai dosyasını ve .claude/.opencode senkronizasyonunu günceller.
//
// Kullanım:
//   node .ai/scripts/vault-post-update.mjs [--scope root|full] [--dry-run]
//
// scope:
//   root (varsayılan) — 12 root .ai dosyası + .claude/.opencode sync
//   full              — Tüm 244 AGENTS.md + 244 CLAUDE.md (büyük vault yeniden yapılandırma için)
//
// Tüm write işlemleri vault-utf8-writer.mjs üzerinden yapılır.

import fs from 'node:fs';
import path from 'node:path';
import { execSync } from 'node:child_process';

const CWD = process.cwd();
const AI_DIR = path.join(CWD, '.ai');
const SCRIPTS_DIR = path.join(AI_DIR, 'scripts');
const WRITER = path.join(SCRIPTS_DIR, 'vault-utf8-writer.mjs');

// --- Argüman analizi ---
const args = process.argv.slice(2);
const scope = args.includes('--full') ? 'full' : 'root';
const dryRun = args.includes('--dry-run');

// --- Tarih ---
function pad(n) { return String(n).padStart(2, '0'); }
const now = new Date();
const dateStr = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
const timeStr = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
const timestamp = `${dateStr} ${timeStr}`;

// --- vault-utf8-writer ile yazma ---
function writerAppend(filePath, entryContent) {
  if (dryRun) {
    console.log(`[DRY] append → ${filePath}`);
    return;
  }
  const tmpFile = path.join(SCRIPTS_DIR, '.tmp-vault-entry.md');
  fs.writeFileSync(tmpFile, entryContent, 'utf8');
  try {
    execSync(`node "${WRITER}" append --file "${filePath}" --entry-file "${tmpFile}"`, { stdio: 'inherit' });
  } finally {
    try { fs.unlinkSync(tmpFile); } catch {}
  }
}

function writerCopy(src, dest) {
  if (dryRun) {
    console.log(`[DRY] copy ${src} → ${dest}`);
    return;
  }
  execSync(`node "${WRITER}" copy --from "${src}" --file "${dest}"`, { stdio: 'inherit' });
}

// --- Dosya okuma ---
function readFile(filePath) {
  if (!fs.existsSync(filePath)) return '';
  return fs.readFileSync(filePath, 'utf8').replace(/^﻿/, '');
}

// --- 12 Root dosya listesi ---
const ROOT_FILES = [
  'MEMORY.md',
  'log.md',
  'brain.md',
  'index.md',
  'keys.md',
  'engine.md',
  'AGENTS.md',
  'WORKFLOW.md',
  'CLAUDE.md',
  'ROLE.md',
  'ULTRA-THINKING.md',
  'glossary.md',
];

// --- Root dosya时间戳 güncelleme ---
function updateRootTimestamps() {
  console.log('\n--- Root .ai dosyaları güncelleniyor ---');
  let updated = 0;

  for (const file of ROOT_FILES) {
    const filePath = path.join(AI_DIR, file);
    if (!fs.existsSync(filePath)) {
      console.log(`  ⏭ ${file} — mevcut değil, atlandı`);
      continue;
    }

    let content = readFile(filePath);

    // Frontmatter'daki updated alanını güncelle
    const updatedPattern = /^(updated:\s*)(.+)$/m;
    if (updatedPattern.test(content)) {
      content = content.replace(updatedPattern, `$1${dateStr}`);
      if (!dryRun) {
        fs.writeFileSync(filePath, content, 'utf8');
      }
      console.log(`  ✅ ${file} — updated: ${dateStr}`);
      updated++;
    } else {
      console.log(`  ⏭ ${file} — updated alanı bulunamadı`);
    }
  }

  console.log(`  Toplam: ${updated} dosya güncellendi`);
  return updated;
}

// --- .claude/ ve .opencode/ senkronizasyonu ---
function syncConfigDirs() {
  console.log('\n--- .claude/ ve .opencode/ senkronizasyonu ---');
  let synced = 0;

  // CLAUDE.md senkronizasyonu
  const aiClaude = path.join(AI_DIR, 'CLAUDE.md');
  const claudeDir = path.join(CWD, '.claude');
  const opencodeDir = path.join(CWD, '.opencode');

  if (fs.existsSync(aiClaude)) {
    // .claude/CLAUDE.md
    const claudeClaude = path.join(claudeDir, 'CLAUDE.md');
    if (fs.existsSync(claudeDir)) {
      writerCopy(aiClaude, claudeClaude);
      console.log('  ✅ .claude/CLAUDE.md senkronize edildi');
      synced++;
    }

    // .opencode/CLAUDE.md
    const opencodeClaude = path.join(opencodeDir, 'CLAUDE.md');
    if (fs.existsSync(opencodeDir)) {
      writerCopy(aiClaude, opencodeClaude);
      console.log('  ✅ .opencode/CLAUDE.md senkronize edildi');
      synced++;
    }
  }

  // AGENTS.md senkronizasyonu
  const aiAgents = path.join(AI_DIR, 'AGENTS.md');
  if (fs.existsSync(aiAgents)) {
    const claudeAgents = path.join(claudeDir, 'AGENTS.md');
    if (fs.existsSync(claudeDir)) {
      writerCopy(aiAgents, claudeAgents);
      console.log('  ✅ .claude/AGENTS.md senkronize edildi');
      synced++;
    }

    const opencodeAgents = path.join(opencodeDir, 'AGENTS.md');
    if (fs.existsSync(opencodeDir)) {
      writerCopy(aiAgents, opencodeAgents);
      console.log('  ✅ .opencode/AGENTS.md senkronize edildi');
      synced++;
    }
  }

  console.log(`  Toplam: ${synced} dosya senkronize edildi`);
  return synced;
}

// --- Wiki-link cross-reference doğrulama ---
function validateWikiLinks() {
  console.log('\n--- Wiki-link doğrulaması ---');
  const wikiLinkRegex = /\[\[([^\]]+)\]\]/g;
  let totalLinks = 0;
  let brokenLinks = 0;

  for (const file of ROOT_FILES) {
    const filePath = path.join(AI_DIR, file);
    if (!fs.existsSync(filePath)) continue;

    const content = readFile(filePath);
    let match;
    while ((match = wikiLinkRegex.exec(content)) !== null) {
      totalLinks++;
      const linkTarget = match[1];
      // Basit kontrol: dosya var mı?
      const targetPath = path.join(AI_DIR, linkTarget + '.md');
      const altPath = path.join(AI_DIR, linkTarget);
      if (!fs.existsSync(targetPath) && !fs.existsSync(altPath)) {
        console.log(`  ⚠️ Kırık link: [[${linkTarget}]] → ${file}`);
        brokenLinks++;
      }
    }
  }

  console.log(`  Toplam: ${totalLinks} link, ${brokenLinks} kırık`);
  return { total: totalLinks, broken: brokenLinks };
}

// --- Full scope: recursive AGENTS.md ve CLAUDE.md tarama ---
function scanAllAgentFiles() {
  console.log('\n--- Full scope: Tüm AGENTS.md ve CLAUDE.md taraması ---');
  let agentCount = 0;
  let claudeCount = 0;

  function walk(dir, depth = 0) {
    if (depth > 10) return;
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    for (const entry of entries) {
      if (entry.name === 'node_modules' || entry.name === '.git' || entry.name === 'vendor' || entry.name === 'archives') continue;
      const fullPath = path.join(dir, entry.name);
      if (entry.isDirectory()) {
        walk(fullPath, depth + 1);
      } else if (entry.name === 'AGENTS.md') {
        agentCount++;
      } else if (entry.name === 'CLAUDE.md') {
        claudeCount++;
      }
    }
  }

  walk(AI_DIR);
  console.log(`  AGENTS.md: ${agentCount} dosya`);
  console.log(`  CLAUDE.md: ${claudeCount} dosya`);
  return { agents: agentCount, claude: claudeCount };
}

// --- log.md'ye audit trail ---
function appendToLog(synced, linkResult) {
  const logPath = path.join(AI_DIR, 'log.md');
  const entry = `\n[${timestamp}] [INFO] [vault-updater] [SYNC] Vault post-update (${scope}): ${synced} dosya senkronize, ${linkResult.total} wiki-link (${linkResult.broken} kırık)\n`;
  writerAppend(logPath, entry);
  console.log('\nlog.md güncellendi');
}

// --- Ana akış ---
console.log(`\n=== Vault Post-Update ===`);
console.log(`Kapsam: ${scope}`);
console.log(`Mod: ${dryRun ? 'DRY-RUN' : 'CANLI'}`);
console.log(`Tarih: ${timestamp}`);

try {
  const updated = updateRootTimestamps();
  const synced = syncConfigDirs();
  const linkResult = validateWikiLinks();

  if (scope === 'full') {
    scanAllAgentFiles();
  }

  if (!dryRun) {
    appendToLog(synced, linkResult);
  }

  console.log(`\n✅ Vault post-update tamamlandı.`);
  console.log(`   Root dosya: ${updated} güncellendi`);
  console.log(`   Config sync: ${synced} dosya`);
  console.log(`   Wiki-links: ${linkResult.total} toplam, ${linkResult.broken} kırık`);
} catch (err) {
  console.error('\n❌ Vault post-update hatası:', err.message);
  process.exit(1);
}
