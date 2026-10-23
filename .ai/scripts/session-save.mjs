#!/usr/bin/env node
// session-save.mjs — Oturum kaydi uretici (vault-sync Asama 8, adim 1)
// Sablon SSOT: .ai/.templates/session-log-template.md (Guardrail #16)
// Cikti: .ai/SESSIONS/{YYYY-MM-DD}-{slug}.md — yazim vault-utf8-writer.mjs (write modu, @tmpfile) uzerinden.
// Karar: 2026-10-23 (PLAN Adim 12 — kullanici onayli uretim).
import { spawnSync } from "node:child_process";
import fs from "node:fs";
import os from "node:os";
import path from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(__dirname, "..", "..");
const TEMPLATE = path.join(ROOT, ".ai", ".templates", "session-log-template.md");
const SESSIONS_DIR = path.join(ROOT, ".ai", "SESSIONS");
const WRITER = path.join(ROOT, ".ai", "scripts", "vault-utf8-writer.mjs");

function usage() {
  console.log(
    [
      "Kullanim:",
      '  node .ai/scripts/session-save.mjs --task "<gorev>" --status <completed|partial|blocked> --agent <agent> [secenekler]',
      "",
      "Secenekler:",
      '  --summary "<metin>"          §3.2 Summary (yoksa VERIFICATION REQUIRED birakilir)',
      '  --decision "<karar>"         §3.4 Decisions (coklu verilebilir)',
      '  --rationale "<gerekce>"      §3.4 karar gerekcesi (karar sirasiyla eslesir)',
      '  --issue "<sorun>"            §3.5 Issues (coklu verilebilir)',
      '  --resolution "<cozum>"       §3.5 sorun cozumu (sorun sirasiyla eslesir)',
      '  --files "yol:islem,..."      §3.6 Files Changed (islem: added|modified|deleted)',
      '  --next "<adim>"              §3.7 Next Steps (coklu verilebilir)',
      "  --dry-run                    Dosyaya yazma, ciktiyi konsola bas",
      "",
      "Kural: dolmayan {PLACEHOLDER} alani raporlanir; kayit elle tamamlanir (sablon §4).",
    ].join("\n")
  );
}

function parseArgs(argv) {
  const opts = { decision: [], rationale: [], issue: [], resolution: [], files: [], next: [], dryRun: false };
  for (let i = 0; i < argv.length; i++) {
    const a = argv[i];
    if (a === "--task") opts.task = argv[++i];
    else if (a === "--status") opts.status = argv[++i];
    else if (a === "--agent") opts.agent = argv[++i];
    else if (a === "--summary") opts.summary = argv[++i];
    else if (a === "--decision") opts.decision.push(argv[++i]);
    else if (a === "--rationale") opts.rationale.push(argv[++i]);
    else if (a === "--issue") opts.issue.push(argv[++i]);
    else if (a === "--resolution") opts.resolution.push(argv[++i]);
    else if (a === "--files") opts.files.push(...argv[++i].split(","));
    else if (a === "--next") opts.next.push(argv[++i]);
    else if (a === "--dry-run") opts.dryRun = true;
    else if (a === "--help" || a === "-h") { usage(); process.exit(0); }
    else { console.error(`Bilinmeyen arguman: ${a}`); usage(); process.exit(2); }
  }
  return opts;
}

function slugify(text) {
  return text
    .toLowerCase()
    .replace(/[\u00e7\u011f\u0131\u00f6\u015f\u00fc]/g, (c) => ({ "\u00e7": "c", "\u011f": "g", "\u0131": "i", "\u00f6": "o", "\u015f": "s", "\u00fc": "u" }[c]))
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "")
    .slice(0, 60);
}

function today() {
  const d = new Date();
  const p = (n) => String(n).padStart(2, "0");
  return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`;
}

function tableRows(pairs, fallback) {
  if (!pairs.length) return fallback;
  return pairs.map(({ key, val }) => `| ${key} | ${val} |`).join("\n");
}

function zip(a, b) {
  return a.map((key, i) => ({ key, val: b[i] || "\u26a0\ufe0f VERIFICATION REQUIRED" }));
}

function main() {
  const opts = parseArgs(process.argv.slice(2));
  for (const req of ["task", "status", "agent"]) {
    if (!opts[req]) { console.error(`Eksik zorunlu arguman: --${req}`); usage(); process.exit(2); }
  }
  if (!fs.existsSync(TEMPLATE)) { console.error(`Sablon yok: ${TEMPLATE}`); process.exit(1); }
  if (!fs.existsSync(WRITER)) { console.error(`Writer yok: ${WRITER}`); process.exit(1); }

  const date = today();
  const sessionId = `${date}-${slugify(opts.task)}`;
  let text = fs.readFileSync(TEMPLATE, "utf8");

  // §3.2 Summary — sablonun serbest metni (placeholder degil).
  text = text.replace(
    "Brief description of what was accomplished.",
    opts.summary || "\u26a0\ufe0f VERIFICATION REQUIRED \u2014 `--summary` ile eklenmedi, elle doldurulacak."
  );

  // Tum {...} placeholder'lari deger map'i ile global degistir (sablon satir formatindan bagimsiz).
  const map = {
    SESSION_ID: sessionId,
    DATE: date,
    START_TIME: "\u26a0\ufe0f VERIFICATION REQUIRED (elle girilecek)",
    END_TIME: new Date().toTimeString().slice(0, 5),
    DURATION: "\u26a0\ufe0f VERIFICATION REQUIRED (elle girilecek)",
    STATUS: opts.status,
    TASK_1: opts.task,
    TASK_2: "\u2014",
    DECISION_1: zip(opts.decision, opts.rationale).map((x) => x.key).join(" ") || "\u2014",
    RATIONALE: zip(opts.decision, opts.rationale).map((x) => x.val).join(" ") || "(karar verilmedi \u2014 gerekiyorsa elle ekle)",
    ISSUE_1: zip(opts.issue, opts.resolution).map((x) => x.key).join(" ") || "\u2014",
    RESOLUTION: zip(opts.issue, opts.resolution).map((x) => x.val).join(" ") || "(sorun yok \u2014 gerekiyorsa elle ekle)",
    FILE_PATH: opts.files.length ? opts.files.map((f) => f.slice(0, f.lastIndexOf(":"))).join("<br>") : "\u2014",
    ACTION: opts.files.length ? opts.files.map((f) => f.slice(f.lastIndexOf(":") + 1)).join("<br>") : "(dosya degisikligi verilmedi \u2014 `--files` ile eklenmeli)",
    NEXT_STEP_1: opts.next[0] || "(sonraki adim verilmedi \u2014 `--next` ile eklenmeli)",
    NEXT_STEP_2: opts.next[1] || "\u2014",
  };
  // Satir-bazli doldurma: {STATUS}/{NOTES} numarasiz generik placeholder'lardir; {TASK_2} satirinda farkli deger alir.
  const eol = text.includes("\r\n") ? "\r\n" : "\n";
  text = text
    .split(/\r?\n/)
    .map((line) => {
      const inTask2 = /\{TASK_2\}/.test(line);
      const local = {
        ...map,
        STATUS: inTask2 ? "\u2014" : opts.status,
        NOTES: inTask2 ? "(ikinci gorev verilmedi)" : `agent: ${opts.agent}`,
      };
      return line.replace(/\{([A-Z0-9_]+)\}/g, (m, k) => (k in local ? local[k] : m));
    })
    .join(eol);

  // Sablon §4 kurali: kalan {...} placeholder sayisi raporlanir (doldurulmadan kapanis yasak).
  // Beyaz liste: yalnizca sablonun gercek placeholder isimleri sayilir; metin icindeki literal "{PLACEHOLDER}" istisna.
  const KNOWN = new Set(["SESSION_ID", "DATE", "START_TIME", "END_TIME", "DURATION", "STATUS", "TASK_1", "TASK_2", "NOTES", "DECISION_1", "RATIONALE", "ISSUE_1", "RESOLUTION", "FILE_PATH", "ACTION", "NEXT_STEP_1", "NEXT_STEP_2"]);
  const leftover = [...new Set([...text.matchAll(/\{([A-Z0-9_]+)\}/g)].map((m) => m[1]).filter((k) => KNOWN.has(k)))];
  const outFile = path.join(SESSIONS_DIR, `${sessionId}.md`);

  if (opts.dryRun) {
    console.log(text);
  } else {
    fs.mkdirSync(SESSIONS_DIR, { recursive: true });
    const tmp = path.join(os.tmpdir(), `session-save-${process.pid}-${Date.now()}.md`);
    fs.writeFileSync(tmp, text, "utf8");
    try {
      const res = spawnSync(process.execPath, [WRITER, "write", "--file", `.ai/SESSIONS/${sessionId}.md`, "--text", `@${tmp}`], {
        cwd: ROOT, encoding: "utf8", shell: false,
      });
      if (res.status !== 0) { console.error(`writer basarisiz: ${res.stderr || res.stdout}`); process.exit(1); }
    } finally {
      fs.rmSync(tmp, { force: true });
    }
    console.log(`Oturum kaydi yazildi: .ai/SESSIONS/${sessionId}.md`);
  }
  if (leftover.length) console.log(`\u26a0\ufe0f Elle doldurulacak placeholder: ${leftover.length} (${[...new Set(leftover)].join(", ")})`);
  else console.log("Tum placeholder'lar dolduruldu/temizlendi (sablon §4 uyumlu).");
  console.log(`Sonraki: vault-post-update.mjs --scope root (Asama 8, adim 2) \u2014 hedef: ${path.relative(ROOT, outFile)}`);
}

main();
