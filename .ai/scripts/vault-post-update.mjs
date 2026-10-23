#!/usr/bin/env node
// vault-post-update.mjs — Asama 8, adim 2: boot dosyalarinin frontmatter `updated:` alani tazeliyi.
// Mekanizma: dosya basindaki frontmatter icindeki `updated: YYYY-MM-DD` satiri bugunun tarihiyle degistirilir;
// BASKA HICBIR SATIRA DOKUNULMAZ (surgical). `updated:` alani olmayan dosya atlanir ve raporlanir.
// HARIC (asla dokunulmaz): log.md (append-only) · SESSIONS/ · .templates/ · .decisions/ (frozen).
// Yazim vault-utf8-writer.mjs (write modu, @tmpfile) uzerinden — tek yazma kanali.
// Karar: 2026-10-23 (PLAN Adim 12 — kullanici onayli uretim).
import { spawnSync } from "node:child_process";
import fs from "node:fs";
import os from "node:os";
import path from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(__dirname, "..", "..");
const WRITER = path.join(ROOT, ".ai", "scripts", "vault-utf8-writer.mjs");

// root scope: kose boot md dosyalari (AGENTS.md §25.4 hafif boot).
const ROOT_BOOT = ["AGENTS.md", "CLAUDE.md", "WORKFLOW.md", "README.md"];
// full scope ekler: .ai/ koku SSOT md dosyalari (depth-1; log.md HARIC).
const AI_ROOT_EXCLUDE = new Set(["log.md"]);

function usage() {
  console.log(
    [
      "Kullanim:",
      "  node .ai/scripts/vault-post-update.mjs --scope <root|full> [--dry-run]",
      "",
      "  root  → kose boot md'leri: " + ROOT_BOOT.join(", "),
      "  full  → root + .ai/ koku SSOT md'leri (log.md haric)",
      "",
      "  --dry-run   Yazma, yalnizca ne degisecegini raporla",
      "",
      "Kural: yalnizca frontmatter `updated:` satiri guncellenir; baska satir degismez.",
      "Haric: log.md (append-only) · SESSIONS/ · .templates/ · .decisions/ (frozen).",
    ].join("\n")
  );
}

function today() {
  const d = new Date();
  const p = (n) => String(n).padStart(2, "0");
  return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`;
}

function targets(scope) {
  const list = ROOT_BOOT.map((f) => path.join(ROOT, f));
  if (scope === "full") {
    const ai = path.join(ROOT, ".ai");
    for (const e of fs.readdirSync(ai, { withFileTypes: true })) {
      if (e.isFile() && e.name.endsWith(".md") && !AI_ROOT_EXCLUDE.has(e.name)) list.push(path.join(ai, e.name));
    }
  }
  return list.filter((f) => fs.existsSync(f));
}

// frontmatter `updated:` satirini bul: dosyanin ilk 30 satiri icinde `^updated:\s*...$`.
function findUpdated(text) {
  const lines = text.split(/\r?\n/);
  for (let i = 0; i < Math.min(lines.length, 30); i++) {
    const m = /^updated:\s*(\S+)\s*$/.exec(lines[i]);
    if (m) return { index: i, value: m[1] };
  }
  return null;
}

function writeViaWriter(relPath, text) {
  const tmp = path.join(os.tmpdir(), `vault-post-update-${process.pid}-${Date.now()}.md`);
  fs.writeFileSync(tmp, text, "utf8");
  try {
    const res = spawnSync(process.execPath, [WRITER, "write", "--file", relPath, "--text", `@${tmp}`], {
      cwd: ROOT, encoding: "utf8", shell: false,
    });
    if (res.status !== 0) { console.error(`writer basarisiz (${relPath}): ${res.stderr || res.stdout}`); return false; }
    return true;
  } finally {
    fs.rmSync(tmp, { force: true });
  }
}

function main() {
  const argv = process.argv.slice(2);
  const scopeIdx = argv.indexOf("--scope");
  const scope = scopeIdx >= 0 ? argv[scopeIdx + 1] : undefined;
  const dryRun = argv.includes("--dry-run");
  if (argv.includes("--help") || argv.includes("-h")) { usage(); process.exit(0); }
  if (scope !== "root" && scope !== "full") { console.error("Eksik/gecersiz --scope (root|full)"); usage(); process.exit(2); }
  if (!fs.existsSync(WRITER)) { console.error(`Writer yok: ${WRITER}`); process.exit(1); }

  const date = today();
  const report = { scope, date, dryRun, updated: [], skippedCurrent: [], skippedNoField: [], failed: [] };

  for (const file of targets(scope)) {
    const rel = path.relative(ROOT, file).replace(/\\/g, "/");
    const text = fs.readFileSync(file, "utf8");
    const found = findUpdated(text);
    if (!found) { report.skippedNoField.push(rel); continue; }
    if (found.value === date) { report.skippedCurrent.push(rel); continue; }

    const lines = text.split(/\r?\n/);
    const eol = text.includes("\r\n") ? "\r\n" : "\n";
    lines[found.index] = `updated: ${date}`;
    if (dryRun) { report.updated.push({ file: rel, from: found.value, to: date, note: "dry-run — yazilmadi" }); continue; }
    const ok = writeViaWriter(rel, lines.join(eol));
    if (ok) report.updated.push({ file: rel, from: found.value, to: date });
    else report.failed.push(rel);
  }

  console.log(JSON.stringify(report, null, 2));
  console.log(
    `SONUC: ${report.updated.length} guncellendi · ${report.skippedCurrent.length} zaten guncel · ` +
    `${report.skippedNoField.length} alani yok (elle ekle) · ${report.failed.length} basarisiz` +
    (dryRun ? " · DRY-RUN (yazma yapilmadi)" : "")
  );
  if (report.failed.length) process.exit(1);
}

main();
