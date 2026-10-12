#!/usr/bin/env node
/**
 * CoreMusic — Skill Usage Mandate Hook (UserPromptSubmit)
 *
 * SSOT: kök CLAUDE.md §Skill Usage Mandate. Bu hook yalnızca o metnin kısa
 * salt-sürümünü + diskten taranan proje skill isimlerini enjekte eder.
 *
 * Modlar:
 *   (default)      stdin hook JSON oku → stdout {"hookSpecificOutput": {...}}
 *   --check        v3.0 format doğrulayıcı (exit 0 = temiz, 1 = ihlal)
 *
 * Emniyet: her koşulda exit 0 (prompt asla bloklanmaz); --check hariç.
 */
'use strict';

const fs = require('fs');
const path = require('path');

const PROJECT_DIR = process.env.CLAUDE_PROJECT_DIR || process.cwd();
const SKILLS_DIR = path.join(PROJECT_DIR, '.claude', 'skills');
const GLOBAL_SKILLS_DIR = process.env.CLAUDE_CONFIG_DIR
  ? path.join(process.env.CLAUDE_CONFIG_DIR, 'skills')
  : path.join(require('os').homedir(), '.claude', 'skills');

// CoreMusic-yazarlı GENEL skill'ler (tek ev: global). Proje-ait skill'ler DISK'ten taranır.
const COREMUSIC_GLOBAL = ['human-mode', 'truth-engine', 'verify-loop', 'agent-debate', 'context-report'];

const BANNED_ROOT_KEYS = [
  'title', 'type', 'version', 'authority', 'mode', 'purpose',
  'reference', 'triggers', 'changelog', 'dependencies',
];

// ---------------------------------------------------------------- utilities

function readFrontmatter(skillFile) {
  const raw = fs.readFileSync(skillFile, 'utf8');
  const m = raw.match(/^---\r?\n([\s\S]*?)\r?\n---/);
  if (!m) return { raw, fm: null };
  return { raw, fm: m[1] };
}

function fmValue(fm, key) {
  const m = fm.match(new RegExp(`^${key}:[ \\t]*(.+)$`, 'm'));
  return m ? m[1].trim().replace(/^["']|["']$/g, '') : null;
}

function fmRootKeys(fm) {
  const keys = [];
  for (const line of fm.split(/\r?\n/)) {
    const m = line.match(/^([A-Za-z_][\w-]*):/);
    if (m) keys.push(m[1]);
  }
  return keys;
}

function listProjectSkills() {
  if (!fs.existsSync(SKILLS_DIR)) return [];
  return fs.readdirSync(SKILLS_DIR, { withFileTypes: true })
    .filter(d => d.isDirectory())
    .map(d => d.name)
    .filter(name => fs.existsSync(path.join(SKILLS_DIR, name, 'SKILL.md')))
    .sort();
}

// ---------------------------------------------------------------- hook mode

function hookMain() {
  try { process.stdin.resume(); } catch (_) { /* ignore */ }

  let names;
  try {
    names = listProjectSkills();
  } catch (_) {
    names = [];
  }

  const globalPresent = COREMUSIC_GLOBAL.filter(n =>
    fs.existsSync(path.join(GLOBAL_SKILLS_DIR, n, 'SKILL.md')));

  const projectList = names.length ? names.join(', ') : '(proje skill taraması boş)';
  const globalList = globalPresent.length
    ? `Global CoreMusic skill'leri (${GLOBAL_SKILLS_DIR}): ${globalPresent.join(', ')}`
    : '';
  const thirdParty = 'Üçüncü taraf global gruplar (dotnet, dx-blazor, superpowers, claude-mem, chrome-devtools vb.) available-skills listesindedir.';

  const additionalContext = [
    'SKILL ZORUNLULUĞU (kök CLAUDE.md §Skill Usage Mandate): Görev bir skill ile eşleşiyorsa',
    'İLK işlem Skill tool ile yüklemektir; eşleşen skill\'siz işlem başlatmak YASAKTIR.',
    `Proje skill'leri (.claude/skills): ${projectList}.`,
    globalList,
    thirdParty,
    "İstisna: kullanıcı açıkça 'skill kullanma' derse veya görev skill'lerle ilgisizse.",
  ].filter(Boolean).join(' ');

  process.stdout.write(JSON.stringify({
    hookSpecificOutput: {
      hookEventName: 'UserPromptSubmit',
      additionalContext,
    },
  }));
  process.exit(0);
}

// -------------------------------------------------------------- check mode

function checkMain() {
  const violations = [];
  const projectNames = listProjectSkills();

  for (const name of projectNames) {
    const dir = path.join(SKILLS_DIR, name);
    const file = path.join(dir, 'SKILL.md');
    let { fm } = readFrontmatter(file);

    if (fm === null) {
      violations.push(`${name}: SKILL.md frontmatter YOK`);
      continue;
    }

    const declared = fmValue(fm, 'name');
    if (declared !== name) violations.push(`${name}: name "${declared}" klasörle 1:1 değil`);

    const desc = fmValue(fm, 'description');
    if (!desc) violations.push(`${name}: description YOK`);
    else {
      if (desc.length > 1024) violations.push(`${name}: description ${desc.length} char (>1024)`);
      if (!/^Use when /i.test(desc)) violations.push(`${name}: description "Use when" ile başlamıyor`);
    }

    for (const key of fmRootKeys(fm)) {
      if (BANNED_ROOT_KEYS.includes(key)) violations.push(`${name}: yasak root key "${key}"`);
    }

    if (!fm.match(/^metadata:/m)) violations.push(`${name}: metadata bloğu YOK`);

    // linklenen references/examples dosyaları var mı (link kontrolü: satır bazlı)
    const raw = fs.readFileSync(file, 'utf8');
    const linkRe = /\((references\/[^)#\s]+|(examples\/[^)#\s]+))\)/g;
    let lm;
    while ((lm = linkRe.exec(raw)) !== null) {
      const target = path.join(dir, lm[1]);
      if (!fs.existsSync(target)) violations.push(`${name}: kırık link ${lm[1]}`);
    }

    // orphan: references/ ve examples/ altındaki .md dosyaları linklenmemiş mi
    for (const sub of ['references', 'examples']) {
      const subDir = path.join(dir, sub);
      if (!fs.existsSync(subDir)) continue;
      for (const f of fs.readdirSync(subDir)) {
        if (!f.endsWith('.md')) continue;
        if (!raw.includes(`${sub}/${f}`)) violations.push(`${name}: ORPHAN ${sub}/${f} (SKILL.md'de link yok)`);
      }
    }

    // sidecar yasağı
    const sidecars = [];
    (function walk(d) {
      for (const e of fs.readdirSync(d, { withFileTypes: true })) {
        if (e.isDirectory() && e.name !== '.archive') walk(path.join(d, e.name));
        else if (e.isFile() && e.name === 'CLAUDE.md') sidecars.push(path.relative(dir, path.join(d, e.name)));
      }
    })(dir);
    for (const s of sidecars) violations.push(`${name}: yasak sidecar CLAUDE.md → ${s}`);
  }

  // proje↔global çift kopya (gölgeleme) — yalnız CoreMusic global kümesine bak
  for (const g of COREMUSIC_GLOBAL) {
    if (projectNames.includes(g)) violations.push(`çift kopya: "${g}" hem projede hem global sette`);
  }

  if (violations.length) {
    console.error(`skill-mandate --check: ${violations.length} ihlal`);
    for (const v of violations) console.error('  ✗ ' + v);
    process.exit(1);
  }
  console.log(`skill-mandate --check: OK (${projectNames.length} proje skill'i temiz)`);
  process.exit(0);
}

// ---------------------------------------------------------------- dispatch

try {
  if (process.argv.includes('--check')) {
    checkMain();
  } else {
    hookMain();
  }
} catch (err) {
  // Hook modu asla prompt'u bloklamasın; check modunda hatayı yüzeye çıkar.
  if (process.argv.includes('--check')) {
    console.error('skill-mandate --check fatal: ' + err.message);
    process.exit(1);
  }
  process.exit(0);
}