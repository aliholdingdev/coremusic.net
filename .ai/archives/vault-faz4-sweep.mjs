#!/usr/bin/env node
/**
 * vault-faz4-sweep.mjs — FAZ 4 tam sweep (.ai altindaki tum .md dosyalari)
 *
 * Modlar:
 *   node .ai/scripts/vault-faz4-sweep.mjs scan
 *       -> tum .md dosyalarini olcer, C:/temp/opencode/faz4-scan-before.json yazar,
 *          konsola ozet + TUM CJK baglamlarini basar.
 *   node .ai/scripts/vault-faz4-sweep.mjs sweep [--dry-run] [--map <json>]
 *       -> mojibake/CJK onarimi + frontmatter updated/version + footer Last Updated
 *          + koken AGENTS.md alinti duzeltmeleri. log.md'ye ASLA yazmaz.
 *   node .ai/scripts/vault-faz4-sweep.mjs verify
 *       -> sweep sonrasi ayni olcumleri alir, oncesiyle karsilastirir, kapilari basar.
 *
 * Kurallar:
 *  - UTF-8 (BOM'suz) yazar; mevcut EOL (CRLF/LF) ve satir sonlari birebir korunur.
 *  - .ai/log.md DOCUNULMAZ (append-only; append ayri olarak vault-utf8-writer ile).
 *  - Frozen .decisions: yalniz frontmatter (updated/version) + footer + mojibake/CJK onarimi.
 *  - PowerShell yazma cmdletleri YASAK; bu script tek yazma arayuzudur.
 */
import fs from "node:fs";
import path from "node:path";

const AI = path.resolve(process.cwd(), ".ai");
const TODAY = "2026-09-24";
const LOG_MD = path.join(AI, "log.md");
const ROOT_AGENTS = path.join(AI, "AGENTS.md");
const SUB_AGENTS = path.join(AI, ".agents", "AGENTS.md");
const OUT_DIR = "C:/temp/opencode";
const SCAN_BEFORE = OUT_DIR + "/faz4-scan-before.json";
const SCAN_AFTER = OUT_DIR + "/faz4-scan-after.json";
const SWEEP_REPORT = OUT_DIR + "/faz4-sweep-report.json";

/* ============================ ortak ============================ */

function walkMd(dir, out = []) {
  for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) walkMd(p, out);
    else if (e.name.toLowerCase().endsWith(".md")) out.push(p);
  }
  return out;
}

function rel(p) {
  return path.relative(process.cwd(), p).replace(/\\/g, "/");
}

function readText(file) {
  const buf = fs.readFileSync(file);
  const hasBom = buf.length >= 3 && buf[0] === 0xef && buf[1] === 0xbb && buf[2] === 0xbf;
  const body = hasBom ? buf.subarray(3) : buf;
  const hasNul = buf.includes(0);
  let utf8ok = true;
  try { new TextDecoder("utf-8", { fatal: true }).decode(body); } catch { utf8ok = false; }
  const text = body.toString("utf8");
  return { text, hasBom, hasNul, utf8ok, bytes: buf.length };
}

function eolOf(text) {
  const crlf = (text.match(/\r\n/g) || []).length;
  const lf = (text.match(/\n/g) || []).length;
  if (lf === 0) return "NONE";
  if (crlf === lf) return "CRLF";
  if (crlf === 0) return "LF";
  return "MIXED";
}

function lineCount(text) {
  return text.split(/\r?\n/).length;
}

/* ==================== CJK tespiti + onarimi ==================== */

const CJK_RE = /\p{Script=Han}|\p{Script=Hiragana}|\p{Script=Katakana}|\p{Script=Hangul}/gu;

function cjkChunks(text) {
  const out = [];
  const lines = text.split(/\r?\n/);
  for (let i = 0; i < lines.length; i++) {
    const m = lines[i].match(/(?:\p{Script=Han}|\p{Script=Hiragana}|\p{Script=Katakana}|\p{Script=Hangul})+/gu);
    if (m) {
      for (const chunk of m) out.push({ line: i + 1, chunk, text: lines[i].slice(0, 240) });
    }
  }
  return out;
}

function applyCjkMap(text, map) {
  const applied = [];
  let out = text;
  // en uzun "from" once: alt-string catismasini onler (orn. 管管理ını / 管理ını)
  const ordered = [...map].sort((a, b) => b.from.length - a.from.length);
  for (const e of ordered) {
    if (!e.from) continue;
    const parts = out.split(e.from);
    if (parts.length > 1) {
      out = parts.join(e.to);
      applied.push({ from: e.from, to: e.to, count: parts.length - 1, note: e.note || "" });
    }
  }
  return { text: out, applied };
}

/* ==================== mojibake tespiti + onarimi ================
 * Strateji: CP1252/latin1 olarak yanlis decode edilmis UTF-8 baytlarini
 * geri ceviriyoruz. Her "yuksek kodlu" (>= U+0080) karakter kostusu icinde:
 *   karakter -> orijinal bayt (latin1 varsayilan + CP1252 ozel haritalar)
 *   bayt dizisi strict UTF-8 olarak decode edilebiliyorsa VE U+FFFD uretmiyorsa
 *   => sonuc kabul (gercek mojibake onarimi). Diger durumlarda ham korunur.
 * Boylece mevcut Turkce metin (ö, ş, ğ, İ, ı ...) ASLA bozulmaz.
 * ================================================================ */

const CP1252_SPECIALS = {
  0x80: "\u20ac", 0x82: "\u201a", 0x83: "\u0192", 0x84: "\u201e", 0x85: "\u2026",
  0x86: "\u2020", 0x87: "\u2021", 0x88: "\u02c6", 0x89: "\u2030", 0x8a: "\u0160",
  0x8b: "\u2039", 0x8c: "\u0152", 0x8e: "\u017d", 0x91: "\u2018", 0x92: "\u2019",
  0x93: "\u201c", 0x94: "\u201d", 0x95: "\u2022", 0x96: "\u2013", 0x97: "\u2014",
  0x98: "\u02dc", 0x99: "\u2122", 0x9a: "\u0161", 0x9b: "\u203a", 0x9c: "\u0153",
  0x9e: "\u017e", 0x9f: "\u0178",
};
const REV = new Map();
for (let b = 0; b < 256; b++) REV.set(b, b);            // latin1 (U+0000..U+00FF)
for (const [b, ch] of Object.entries(CP1252_SPECIALS)) { // CP1252 ozel baytlar
  REV.delete(ch.codePointAt(0));
  REV.set(ch.codePointAt(0), Number(b));
}
for (let b = 0x80; b <= 0x9f; b++) REV.delete(b);        // C1 kontrol karakterleri dosyada beklenmez

const FATAL_DECODER = new TextDecoder("utf-8", { fatal: true });
const HIGH_RUN = /[\u0080-\uFFFF]+/g;
const RESIDUAL_RE = /\uFEFF|ï¿½|\uFFFD/g;

function repairMojibake(text) {
  let fixed = 0;
  const out = text.replace(HIGH_RUN, (run) => {
    let bytes = [];
    let raw = "";
    let emit = "";
    const flush = () => { emit += raw; raw = ""; };
    for (const ch of run) {
      const b = REV.get(ch.codePointAt(0));
      if (b === undefined) { flush(); emit += ch; continue; } // haritada yok (ş, ğ, İ, ı, emoji...) -> oldugu gibi
      bytes.push(b);
      raw += ch;
      if (bytes.length >= 2) {
        try {
          const dec = FATAL_DECODER.decode(Uint8Array.from(bytes));
          if (dec.includes("\uFFFD")) {
            // orijinal zaten bozuk (ï¿½ gibi): ham bas, sonraki karakterler icin sifirla
            flush(); bytes = []; fixed += 0;
          } else {
            emit += dec; raw = ""; bytes = []; fixed += 1;
          }
        } catch { /* eksik/gecersiz UTF-8: birikime devam */ }
      }
    }
    flush();
    return emit;
  });
  return { text: out, fixed };
}

function mojibakeStats(text) {
  const { text: repaired, fixed } = repairMojibake(text);
  const residual = (text.match(RESIDUAL_RE) || []).filter((x) => x !== "\uFEFF").length;
  // idempotans: onarim sonrasi hala degisiyorsa kirli say
  const again = repairMojibake(repaired).fixed;
  return {
    wouldFix: fixed + again,
    residual,
    dirty: (fixed + again) > 0 || residual > 0,
    sample: fixed > 0 ? sampleFixed(text, repaired) : "",
  };
}

function sampleFixed(before, after) {
  const b = before.split(/\r?\n/), a = after.split(/\r?\n/);
  for (let i = 0; i < b.length && i < a.length; i++) {
    if (b[i] !== a[i]) return (i + 1) + ": " + b[i].trim().slice(0, 160);
  }
  return "";
}

/* ==================== frontmatter + footer ==================== */

const FM_RE = /^---\r?\n([\s\S]*?)\r?\n---(?=\r?\n|$)/;

function parseFm(text) {
  const m = text.match(FM_RE);
  if (!m) return null;
  return { body: m[1], full: m[0] };
}

function bumpVersion(v) {
  const s = String(v).trim();
  let m = s.match(/^(v?)(\d+)\.(\d+)\.(\d+)$/);
  if (m) return m[1] + m[2] + "." + m[3] + "." + (Number(m[4]) + 1);
  m = s.match(/^(v?)(\d+)\.(\d+)$/);
  if (m) return m[1] + m[2] + "." + m[3] + ".1";
  return null;
}

function sweepMetadataSafe(text, meta) {
  const m = text.match(FM_RE);
  if (!m) { meta.hasFm = false; return text; }
  meta.hasFm = true;
  const fmEol = m[0].includes("\r\n") ? "\r\n" : "\n";
  const rawBody = m[1];
  const bodyEol = rawBody.includes("\r\n") ? "\r\n" : "\n";
  const lines = rawBody.split(/\r?\n/);
  let changed = false;

  for (let i = 0; i < lines.length; i++) {
    let lm = lines[i].match(/^([ \t]*updated:[ \t]*)(['"]?)([^'"#]*?)(['"]?)([ \t]*)$/);
    if (lm && !meta.updatedSeen) {
      meta.updatedSeen = true;
      meta.oldUpdated = lm[3].trim();
      meta.newUpdated = TODAY;
      const q = lm[2] || "";
      if (meta.oldUpdated !== TODAY) { lines[i] = lm[1] + q + TODAY + q + lm[5]; changed = true; }
      continue;
    }
    lm = lines[i].match(/^([ \t]*version:[ \t]*)(['"]?)([^'"#]*?)(['"]?)([ \t]*)$/);
    if (lm && !meta.versionSeen) {
      meta.versionSeen = true;
      const oldV = lm[3].trim();
      const newV = bumpVersion(oldV);
      meta.oldVersion = oldV;
      if (newV) {
        meta.newVersion = newV;
        const q = lm[2] || "";
        if (oldV !== newV) { lines[i] = lm[1] + q + newV + q + lm[5]; changed = true; }
      } else meta.versionUnparsed = oldV;
      continue;
    }
  }
  if (!meta.updatedSeen) meta.noUpdated = true;
  if (!meta.versionSeen) meta.noVersion = true;
  if (!changed) return text;

  const newFm = "---" + fmEol + lines.join(bodyEol) + fmEol + "---";
  // closing --- sonrasi kalan metni koru
  const rest = text.slice(m[0].length);
  return newFm + rest;
}

function sweepFooter(text, meta) {
  const re = /(\*\*Last Updated:\*\*[ \t]*)(\d{4}-\d{2}-\d{2})/gi;
  let hit = false;
  const out = text.replace(re, (full, p1, date) => {
    hit = true;
    meta.oldFooter = meta.oldFooter || date;
    meta.newFooter = TODAY;
    return p1 + TODAY;
  });
  if (!hit) {
    meta.noFooter = true;
    // varyantlari raporla (degistirme)
    const v = text.match(/\*\*[^\n*]{0,40}(G.ncelleme|Updated)[^\n*]{0,40}\*\*[^\n]{0,30}/gi);
    if (v) meta.footerVariants = v.slice(0, 3);
  }
  return out;
}

/* ==================== koken AGENTS.md alintilari (IS 3) ======== */

function fixRootQuotes(text, rootVerNew, subVerQuote) {
  const applied = [];
  const a = text.split("(v22.0.0)").join("(v" + rootVerNew + ")");
  if (a !== text) applied.push({ from: "(v22.0.0)", to: "(v" + rootVerNew + ")", count: text.split("(v22.0.0)").length - 1 });
  const b = a.split("v1.2.1").join(subVerQuote);
  if (b !== a) applied.push({ from: "v1.2.1", to: subVerQuote, count: a.split("v1.2.1").length - 1 });
  return { text: b, applied };
}

/* ============================ scan ============================ */

function scanFile(file) {
  const r = readText(file);
  const isLog = path.resolve(file) === LOG_MD;
  const cjk = cjkChunks(r.text);
  const moji = mojibakeStats(r.text);
  const fm = parseFm(r.text);
  const meta = { hasFm: !!fm };
  let updated = null, version = null;
  if (fm) {
    const um = fm.body.match(/^([ \t]*updated:[ \t]*)(.*)$/m);
    if (um) updated = um[2].trim().replace(/^['"]|['"]$/g, "");
    const vm = fm.body.match(/^([ \t]*version:[ \t]*)(.*)$/m);
    if (vm) version = vm[2].trim().replace(/^['"]|['"]$/g, "");
  }
  const fmDate = fm ? (fm.body.match(/^([ \t]*date:[ \t]*)(.*)$/m) || [])[2] : null;
  const foot = r.text.match(/\*\*Last Updated:\*\*[ \t]*(\d{4}-\d{2}-\d{2})/i);
  const fv = r.text.match(/\*\*[^\n*]{0,40}(G.ncelleme|Updated)[^\n*]{0,40}\*\*[^\n]{0,30}/gi);
  return {
    file: rel(file),
    isLog,
    bytes: r.bytes,
    lines: lineCount(r.text),
    eol: eolOf(r.text),
    bom: r.hasBom,
    nul: r.hasNul,
    utf8ok: r.utf8ok,
    hasFm: !!fm,
    updated,
    date: fmDate ? fmDate.trim().replace(/^['"]|['"]$/g, "") : null,
    version,
    footer: foot ? foot[1] : null,
    footerVariants: fv ? fv.slice(0, 3) : [],
    cjkCount: cjk.length,
    cjk,
    mojiWouldFix: moji.wouldFix,
    mojiResidual: moji.residual,
    mojiDirty: moji.dirty,
    mojiSample: moji.sample,
    dirty: (moji.dirty && !isLog) || cjk.length > 0 || r.hasBom || r.hasNul || !r.utf8ok,
  };
}

function doScan(outFile) {
  const files = walkMd(AI).sort();
  const rows = files.map(scanFile);
  const summary = {
    when: new Date().toISOString(),
    total: rows.length,
    logMdExcluded: rows.filter((r) => r.isLog).length,
    noFrontmatter: rows.filter((r) => !r.hasFm).map((r) => r.file),
    withUpdated: rows.filter((r) => r.updated).length,
    withVersion: rows.filter((r) => r.version).length,
    withFooter: rows.filter((r) => r.footer).length,
    noFooterButVariant: rows.filter((r) => !r.footer && r.footerVariants.length).map((r) => ({ f: r.file, v: r.footerVariants })),
    versions: [...new Set(rows.filter((r) => r.version).map((r) => r.version))].sort(),
    updatedValues: [...new Set(rows.filter((r) => r.updated).map((r) => r.updated))].sort(),
    bom: rows.filter((r) => r.bom).map((r) => r.file),
    nul: rows.filter((r) => r.nul).map((r) => r.file),
    utf8bad: rows.filter((r) => !r.utf8ok).map((r) => r.file),
    mojiDirtyFiles: rows.filter((r) => r.mojiDirty).map((r) => ({ f: r.file, log: r.isLog, fix: r.mojiWouldFix, res: r.mojiResidual, s: r.mojiSample })),
    cjkFiles: rows.filter((r) => r.cjkCount > 0).map((r) => ({ f: r.file, log: r.isLog, n: r.cjkCount, hits: r.cjk })),
    totalCjk: rows.reduce((a, r) => a + r.cjkCount, 0),
    totalMojiWouldFix: rows.reduce((a, r) => a + r.mojiWouldFix, 0),
    totalLines: rows.reduce((a, r) => a + r.lines, 0),
    totalBytes: rows.reduce((a, r) => a + r.bytes, 0),
    dirtyFiles: rows.filter((r) => r.dirty).map((r) => r.file),
  };
  fs.mkdirSync(OUT_DIR, { recursive: true });
  fs.writeFileSync(SCAN_BEFORE, JSON.stringify({ summary, rows }, null, 1), "utf8");
  console.log(JSON.stringify({ ...summary, cjkFiles: summary.cjkFiles.map((c) => ({ f: c.f, log: c.log, n: c.n })), mojiDirtyFiles: summary.mojiDirtyFiles.map((m) => ({ f: m.f, log: m.log, fix: m.fix, res: m.res })) }, null, 1));
  console.log("\n===== CJK BAGLAMLARI (dosya | satir | chunk | satir metni) =====");
  for (const cf of summary.cjkFiles) {
    for (const h of cf.hits) console.log(`${cf.f} | L${h.line} | ${h.chunk} | ${h.text}`);
  }
}

/* ============================ sweep ============================ */

function doSweep(opts) {
  const dry = !!opts["dry-run"];
  let map = [];
  if (opts.map) map = JSON.parse(fs.readFileSync(opts.map, "utf8"));
  const files = walkMd(AI).sort();
  const report = { dry, TODAY, files: files.length, changed: [], details: [], skippedLog: rel(LOG_MD), quotes: [] };

  // IS-3 icin hedef surumler (swap oncesi oku)
  const rootMeta = {};
  const rootTextBefore = readText(ROOT_AGENTS).text;
  sweepMetadataSafe(rootTextBefore, rootMeta); // sadece hesap: old->new
  const rootVerNew = rootMeta.newVersion || bumpVersion(rootMeta.oldVersion) || "22.0.2";
  const subMeta = {};
  sweepMetadataSafe(readText(SUB_AGENTS).text, subMeta);
  const subVerNew = subMeta.newVersion || bumpVersion(subMeta.oldVersion) || "1.2.3";
  // TALIMAT: koken alinti hedefi v1.2.2 (senkron ajanin cektigi deger)
  const subVerQuote = "v1.2.2";
  report.rootVer = { old: rootMeta.oldVersion, next: rootVerNew };
  report.subVer = { old: subMeta.oldVersion, next: subVerNew, quoteTarget: subVerQuote };

  for (const file of files) {
    const isLog = path.resolve(file) === LOG_MD;
    if (isLog) continue; // APPEND-ONLY: hic dokunulmaz
    const before = readText(file);
    let text = before.text;
    const d = { file: rel(file), writes: [] };

    // 1) CJK
    const cjkBefore = cjkChunks(text).length;
    if (map.length) {
      const r = applyCjkMap(text, map);
      if (r.applied.length) {
        text = r.text;
        for (const a of r.applied) d.writes.push(`cjk:${JSON.stringify(a.from)}->${JSON.stringify(a.to)} x${a.count}`);
      }
    }
    const cjkAfter = cjkChunks(text).length;
    d.cjk = cjkBefore + "->" + cjkAfter;
    if (cjkAfter > 0) d.leftover = cjkChunks(text).map((c) => "L" + c.line + ":" + c.chunk);

    // 2) mojibake
    const mo = mojibakeStats(text);
    if (mo.wouldFix > 0) {
      const r = repairMojibake(text);
      text = r.text;
      d.moji = "fixed:" + r.fixed + "(+residual:" + mo.residual + ")";
    } else d.moji = "0";

    // 3) frontmatter updated/version
    const meta = {};
    text = sweepMetadataSafe(text, meta);
    d.meta = meta.hasFm
      ? `ver:${meta.oldVersion || "YOK"}->${meta.newVersion || "-"} upd:${meta.oldUpdated || "YOK"}->${meta.updatedSeen ? TODAY : "-"}`
      : "NO-FRONTMATTER";

    // 4) footer
    const fmeta = {};
    text = sweepFooter(text, fmeta);
    if (fmeta.oldFooter) d.footer = fmeta.oldFooter + "->" + fmeta.newFooter;
    else d.footer = fmeta.footerVariants && fmeta.footerVariants.length ? "VARIANT:" + fmeta.footerVariants.join(" ; ") : "yok";

    // 5) IS-3 koken alintilari
    if (path.resolve(file) === ROOT_AGENTS) {
      const q = fixRootQuotes(text, rootVerNew, subVerQuote);
      if (q.applied.length) {
        text = q.text;
        report.quotes = q.applied;
        for (const a of q.applied) d.writes.push(`quote:${a.from}->${a.to} x${a.count}`);
      }
    }

    const changed = text !== before.text;
    d.changed = changed;
    d.lines = lineCount(before.text) + "->" + lineCount(text);
    d.eol = eolOf(before.text) + "->" + eolOf(text);
    if (changed) {
      report.changed.push(d.file);
      if (!dry) {
        if (path.resolve(file) === LOG_MD) throw new Error("log.md'ye yazma engellendi!");
        fs.writeFileSync(file, Buffer.from(text, "utf8")); // BOM'suz UTF-8, EOL korunur
        d.wrote = true;
      }
    }
    if (changed || d.cjk !== "0->0" || d.moji !== "0") report.details.push(d);
  }

  fs.mkdirSync(OUT_DIR, { recursive: true });
  fs.writeFileSync(SWEEP_REPORT, JSON.stringify(report, null, 1), "utf8");
  console.log(JSON.stringify({ dry, total: report.files, changedCount: report.changed.length, rootVer: report.rootVer, subVer: report.subVer, quotes: report.quotes }, null, 1));
  for (const d of report.details) console.log([d.file, d.cjk, d.moji, d.meta || "", "footer:" + d.footer, d.lines, ...(d.writes || [])].join(" | "));
}

/* ============================ verify ============================ */

function doVerify() {
  const files = walkMd(AI).sort();
  const rows = files.map(scanFile);
  const before = JSON.parse(fs.readFileSync(SCAN_BEFORE, "utf8"));
  const bRows = before.rows;
  const g = {};

  // Kapı 1: dosya sayısı
  g.count = { before: bRows.length, after: rows.length, ok: bRows.length === rows.length };

  // Kapı 2: mojibake/CJK (log.md haric)
  const mojiLeft = rows.filter((r) => !r.isLog && r.mojiDirty).map((r) => r.file);
  const cjkLeft = rows.filter((r) => !r.isLog && r.cjkCount > 0).map((r) => ({ f: r.file, n: r.cjkCount, hits: r.cjk }));
  const logRow = rows.find((r) => r.isLog);
  const bLog = bRows.find((r) => r.isLog);
  g.clean = {
    mojiFilesLeft: mojiLeft, cjkLeft,
    logBefore: { moji: bLog ? bLog.mojiWouldFix + "/" + bLog.mojiResidual : null, cjk: bLog ? bLog.cjkCount : null },
    logAfter: { moji: logRow ? logRow.mojiWouldFix + "/" + logRow.mojiResidual : null, cjk: logRow ? logRow.cjkCount : null },
    ok: mojiLeft.length === 0 && cjkLeft.length === 0 &&
        !!logRow && !!bLog &&
        logRow.mojiWouldFix === bLog.mojiWouldFix && logRow.mojiResidual === bLog.mojiResidual && logRow.cjkCount === bLog.cjkCount,
  };

  // Kapı 5: ornek 5 dosya
  const samples = [];
  const prefer = [".ai/CLAUDE.md", ".ai/AGENTS.md", ".ai/index.md", ".ai/.agents/AGENTS.md", ".ai/.templates/adr/adr-template.md"];
  for (const p of prefer) {
    const r = rows.find((x) => x.file === p);
    if (r) samples.push({ file: p, updated: r.updated, version: r.version, footer: r.footer, ok: r.updated === TODAY && r.footer === TODAY });
  }
  for (const r of rows) {
    if (samples.length >= 5) break;
    if (!samples.find((s) => s.file === r.file) && r.updated) samples.push({ file: r.file, updated: r.updated, version: r.version, footer: r.footer, ok: r.updated === TODAY && (r.footer === TODAY || !r.footer) });
  }
  g.samples = samples;

  // Kapı 6: satir sayisi
  const bTotal = bRows.reduce((a, r) => a + r.lines, 0);
  const aTotal = rows.reduce((a, r) => a + r.lines, 0);
  const lineDiffs = [];
  for (const r of rows) {
    const b = bRows.find((x) => x.file === r.file);
    if (b && b.lines !== r.lines) lineDiffs.push({ f: r.file, before: b.lines, after: r.lines });
  }
  g.lines = { before: bTotal, after: aTotal, diff: aTotal - bTotal, perFileChanged: lineDiffs, ok: lineDiffs.length === 0 };

  // Kapı 7: BOM / NUL / UTF-8
  g.bytes = {
    bom: rows.filter((r) => r.bom).map((r) => r.file),
    nul: rows.filter((r) => r.nul).map((r) => r.file),
    utf8bad: rows.filter((r) => !r.utf8ok).map((r) => r.file),
  };
  g.bytes.ok = g.bytes.bom.length === 0 && g.bytes.nul.length === 0 && g.bytes.utf8bad.length === 0;

  // metadata odevi kontrolu: updated bugun olan dosya sayisi / version kalan eski degerler
  const notUpdated = rows.filter((r) => r.hasFm && r.updated && r.updated !== TODAY && !r.isLog).map((r) => r.file + "=" + r.updated);
  const noFm = rows.filter((r) => !r.hasFm).map((r) => r.file);
  const noVer = rows.filter((r) => r.hasFm && !r.version).map((r) => r.file);
  const staleFooter = rows.filter((r) => r.footer && r.footer !== TODAY && !r.isLog).map((r) => r.file + "=" + r.footer);
  g.meta = { updatedNotToday: notUpdated, footerNotToday: staleFooter, noFrontmatter: noFm, noVersionCount: noVer.length, noVersionSample: noVer.slice(0, 12) };

  fs.writeFileSync(SCAN_AFTER, JSON.stringify({ rows, g }, null, 1), "utf8");
  console.log(JSON.stringify(g, null, 1));
}

/* ============================ main ============================ */

const mode = process.argv[2];
const opts = {};
for (let i = 3; i < process.argv.length; i++) {
  const a = process.argv[i];
  if (a.startsWith("--")) { const n = process.argv[i + 1]; if (n && !n.startsWith("--")) { opts[a.slice(2)] = n; i++; } else opts[a.slice(2)] = true; }
}
try {
  if (mode === "scan") doScan();
  else if (mode === "sweep") doSweep(opts);
  else if (mode === "verify") doVerify();
  else { console.error("Mod: scan | sweep [--dry-run] [--map f] | verify"); process.exit(1); }
} catch (e) {
  console.error("HATA:", e && e.stack || e);
  process.exit(1);
}
