#!/usr/bin/env node
/**
 * CoreMusic .ai Control Plane Validator — tek giriş (Q6 kararı: dağıtık check + tek entry)
 *
 * Check modülleri:
 *   fm-check     : authority-critical dosyalarda 13 alan FM + enum doğrulaması
 *   link-check   : wiki-link [[hedef]] çözülebilir mi (0 gerçek kırık)
 *   ssot-check   : aynı domain'de 2+ ssot:true = duplicate authority uyarısı
 *   dep-check    : depends-on hedefi var mı + circular dependency tespiti (§32)
 *   tier-check   : tier enum (1-5) + T1/T2 dosya listesi (bilgi)
 *   orphan-check : sahipsiz dosya (FM var ama owner yok / hiç FM yok) (§33)
 *
 * Kullanım:
 *   node .ai/scripts/validate.mjs           → tam rapor, ihlal varsa exit 1
 *   node .ai/scripts/validate.mjs --check   → tek satır sonuç (hook/skill modu)
 *
 * Emniyet: salt-okunur (dosyaya YAZMAZ). Her koşulda rapor üretir; --check'te sessizlik yok.
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const AI = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const CHECK = process.argv.includes('--check');

const TIER_ENUM = [1, 2, 3, 4, 5];
const RISK_ENUM = ['low', 'medium', 'high'];
// disk gerçeği (Zero-Hallucination): vault FM'leri yaygın olarak `active` kullanır — enum bunu kapsar
const STATUS_ENUM = ['active', 'draft', 'proposed', 'approved', 'rejected', 'deprecated'];
const REQUIRED = ['title', 'type', 'category', 'version', 'status', 'authority', 'updated',
                  'tier', 'domain', 'ssot', 'risk', 'owner', 'depends-on'];

// Bilinen sahte linkler (AGENTS §13.7 — düzeltme YASAK) + kod içi attribute'lar
const PLACEHOLDER_LINKS = new Set(['C', 'V', 'link', 'wiki-link', 'nodiscard', 'TODO', 'FIXME', 'VERIFY']);
// Sadeleştirmede/stale catalog'da kaldırılan dizinler → bilinen ölü hedef (tracked debt, gate DEĞİL)
const DEAD_DIRS = new Set([
  'architecture', 'archives', 'prompts', 'reports', 'checklists', 'subdomains', 'projects',
  // index.md stale katalog hedefleri (eski layout)
  'electronic', 'registry', 'knowledge', 'research', 'workflows', 'testing', 'scaffold',
  'sessions', 'confidence', 'reference',
]);
// .ai'de MEVCUT üst dizinler — içlerindeki kırık link = gerçek ihlal (yol hatası)
const LIVE_DIRS = new Set(['.agents', '.decisions', '.diagram', '.obsidian', '.personas', '.png',
  '.rules', '.sql', '.templates', 'ecosystem', 'scripts', 'servers', 'ui-design']);

// Authority-critical kapsam (uplift set): 13-alan ZORUNLU
const rootFiles = fs.readdirSync(AI).filter(f => f.endsWith('.md') && f !== 'log.md');
const scopeSub = [
  ...fs.readdirSync(path.join(AI, '.agents')).filter(f => f.endsWith('.md')).map(f => `.agents/${f}`),
  ...fs.readdirSync(path.join(AI, '.rules')).filter(f => f.endsWith('.md')).map(f => `.rules/${f}`),
  '.decisions/index.md',
].filter(f => fs.existsSync(path.join(AI, f)));

const violations = [];   // {file, check, msg}
const warnings = [];
const info = [];

function rel(p) { return path.relative(AI, p).replace(/\\/g, '/'); }

function readFm(raw) {
  const m = raw.match(/^---\r?\n([\s\S]*?)\r?\n---/);
  return m ? { fm: m[1], body: raw.slice(m[0].length) } : { fm: null, body: raw };
}

function fmVal(fm, key) {
  const m = fm.match(new RegExp('^' + key + ':[ \\t]*(.+)$', 'm'));
  return m ? m[1].trim().replace(/^["']|["']$/g, '') : null;
}

// ─── fm-check + tier-check + orphan-check ───────────────────────────────────
function checkFm(files) {
  for (const f of files) {
    const p = path.join(AI, f);
    const raw = fs.readFileSync(p, 'utf8');
    const { fm } = readFm(raw);
    if (!fm) { violations.push({ file: f, check: 'orphan-check', msg: 'frontmatter YOK' }); continue; }

    // 13 alan zorunluluk
    const missing = REQUIRED.filter(k => !new RegExp('^' + k.replace(/-/g, '-') + ':', 'm').test(fm));
    if (missing.length) violations.push({ file: f, check: 'fm-check', msg: 'eksik alan: ' + missing.join(', ') });

    // enum'lar
    const tier = fmVal(fm, 'tier');
    if (tier !== null) {
      const n = Number(tier);
      if (!TIER_ENUM.includes(n)) violations.push({ file: f, check: 'tier-check', msg: 'geçersiz tier: ' + tier });
      else if (n <= 2) info.push(`T${n} · ${f}`);
    }
    const risk = fmVal(fm, 'risk');
    if (risk !== null && !RISK_ENUM.includes(risk)) violations.push({ file: f, check: 'fm-check', msg: 'geçersiz risk: ' + risk });
    const status = fmVal(fm, 'status');
    if (status !== null && !STATUS_ENUM.includes(status)) violations.push({ file: f, check: 'fm-check', msg: 'geçersiz status: ' + status });
    const ssot = fmVal(fm, 'ssot');
    if (ssot !== null && !['true', 'false'].includes(ssot)) violations.push({ file: f, check: 'fm-check', msg: 'geçersiz ssot: ' + ssot });
    if (fmVal(fm, 'owner') === null) violations.push({ file: f, check: 'orphan-check', msg: 'owner yok (sahipsiz)' });
  }
}

// ─── ssot-check: domain = konu-ALANI, konu DEĞİL → duplicate mekanik tespit edilemez.
//    Aynı domain/category'de çoklu ssot:true = insan gözden geçirme UYARISI (gate değil).
function checkSsot(files) {
  const byKey = new Map();
  for (const f of files) {
    const { fm } = readFm(fs.readFileSync(path.join(AI, f), 'utf8'));
    if (!fm || fmVal(fm, 'ssot') !== 'true') continue;
    const key = (fmVal(fm, 'domain') || '?') + '/' + (fmVal(fm, 'category') || '?');
    if (!byKey.has(key)) byKey.set(key, []);
    byKey.get(key).push(f);
  }
  for (const [k, list] of byKey) {
    if (list.length > 1) warnings.push(`ssot gözden geçirme: "${k}" içinde ${list.length} ssot:true → ${list.join(', ')} (farklı konular olabilir; duplicate authority var mı insan kontrolü)`);
  }
}

// ─── dep-check: hedef var mı + circular ────────────────────────────────────
function checkDeps(files) {
  const graph = new Map();
  for (const f of files) {
    const { fm } = readFm(fs.readFileSync(path.join(AI, f), 'utf8'));
    if (!fm) continue;
    const dep = fmVal(fm, 'depends-on');
    if (!dep) continue;
    const items = dep.replace(/^\[|\]$/g, '').split(',').map(s => s.trim().replace(/^["']|["']$/g, '')).filter(Boolean);
    const norm = items.map(d => d.replace(/^\.ai\//, ''));
    graph.set(f, norm);
    for (const d of norm) {
      const cand = [path.join(AI, d), path.join(AI, d + '.md'), path.join(AI, d, 'index.md')];
      if (!cand.some(c => fs.existsSync(c))) violations.push({ file: f, check: 'dep-check', msg: 'depends-on hedefi YOK: ' + d });
    }
  }
  // cycle: DFS
  const state = new Map();
  const stack = [];
  function dfs(n) {
    if (state.get(n) === 2) return;
    if (state.get(n) === 1) {
      const i = stack.indexOf(n);
      violations.push({ file: n, check: 'dep-check', msg: 'CIRCULAR: ' + stack.slice(i).concat(n).join(' → ') });
      return;
    }
    state.set(n, 1); stack.push(n);
    for (const m of (graph.get(n) || [])) if (graph.has(m)) dfs(m);
    stack.pop(); state.set(n, 2);
  }
  for (const n of graph.keys()) dfs(n);
}

// ─── link-check: kod içi / placeholder / dosyaya göreli çözüm / dead-dir ayrımı ──
function stripCode(text) {
  return text.replace(/```[\s\S]*?```/g, '').replace(/`[^`\n]*`/g, '');
}

function checkLinks(files) {
  const linkRe = /\[\[([^\]|#]+)(?:[|#][^\]]*)?\]\]/g;
  const deadCount = new Map();
  for (const f of files) {
    const raw = fs.readFileSync(path.join(AI, f), 'utf8');
    const { body } = readFm(raw);
    const text = stripCode(body);
    const fileDir = path.dirname(path.join(AI, f));
    let m;
    while ((m = linkRe.exec(text)) !== null) {
      let t = m[1].trim();
      if (!t || t.startsWith('http')) continue;
      if (PLACEHOLDER_LINKS.has(t)) continue;                    // §13.7 sahte linkler
      if (/^ADR-\d+\/\d/.test(t)) continue;                      // ADR-053/054 tarzı metin-cross-ref
      const rel = t.replace(/^\.\//, '').replace(/^\.ai\//, '');
      const cand = [
        path.join(fileDir, rel), path.join(fileDir, rel + '.md'), path.join(fileDir, rel, 'index.md'),   // dosyaya göreli (ANA çözüm)
        path.join(AI, rel), path.join(AI, rel + '.md'), path.join(AI, rel, 'index.md'),                  // .ai köküne göreli
        path.join(path.dirname(AI), rel), path.join(path.dirname(AI), rel + '.md'),                      // repo kökü
      ];
      if (cand.some(c => fs.existsSync(c))) continue;
      const firstSeg = rel.split('/')[0].replace(/\.md$/, '');
      if (DEAD_DIRS.has(firstSeg) || !LIVE_DIRS.has(firstSeg) && firstSeg.includes('/') === false && !['.ai'].includes(firstSeg) && !fs.existsSync(path.join(AI, firstSeg)) && !fs.existsSync(path.join(AI, firstSeg + '.md'))) {
        // bilinen ölü hedef ya da var olmayan üst-segment (stale katalog) → tracked debt
        const k = DEAD_DIRS.has(firstSeg) ? firstSeg : '(stale-katalog)';
        deadCount.set(k, (deadCount.get(k) || 0) + 1);
      } else {
        // MEVCUT dizin içinde çözülemeyen = gerçek yol hatası → ihlal
        violations.push({ file: f, check: 'link-check', msg: 'kırık wiki-link (mevcut ağaca içinde): [[' + t + ']]' });
      }
    }
  }
  for (const [d, n] of deadCount) warnings.push(`dead-link borç: "${d}/" → ${n} referans (hedef diskte YOK — ⚠️ VERIFICATION REQUIRED, stale katalog/sadeleştirme)`);
}

// ─── run ───────────────────────────────────────────────────────────────────
const scope = [...rootFiles.map(f => f), ...scopeSub];
checkFm(scope);
checkSsot(scope);
checkDeps(scope);
checkLinks(scope);

const fmV = violations.filter(v => v.check === 'fm-check').length;
const tierV = violations.filter(v => v.check === 'tier-check').length;
const linkV = violations.filter(v => v.check === 'link-check').length;
const depV = violations.filter(v => v.check === 'dep-check').length;
const orphanV = violations.filter(v => v.check === 'orphan-check').length;

if (CHECK) {
  if (violations.length === 0) {
    console.log(`validate --check: OK (${scope.length} dosya · 6 check temiz)`);
    process.exit(0);
  } else {
    console.log(`validate --check: ${violations.length} İHLAL (${scope.length} dosya)`);
    for (const v of violations) console.log(`  [${v.check}] ${v.file}: ${v.msg}`);
    process.exit(1);
  }
} else {
  console.log(`=== .ai Control Plane Validator — ${scope.length} dosya ===`);
  console.log(`fm=${fmV} tier=${tierV} link=${linkV} dep=${depV} orphan=${orphanV} uyarı=${warnings.length}`);
  for (const v of violations) console.log(`  ✗ [${v.check}] ${v.file}: ${v.msg}`);
  for (const w of warnings) console.log(`  ⚠ ${w}`);
  if (info.length) { console.log('-- T1/T2 dosyaları (yüksek koruma) --'); for (const i of info) console.log('  ' + i); }
  console.log(violations.length === 0 ? 'SONUÇ: TEMİZ' : `SONUÇ: ${violations.length} İHLAL`);
  process.exit(violations.length === 0 ? 0 : 1);
}
