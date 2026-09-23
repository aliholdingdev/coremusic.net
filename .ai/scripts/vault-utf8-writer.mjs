#!/usr/bin/env node
/**
 * vault-utf8-writer.mjs — CoreMusic .ai/ vault tek yazma arayuzu (UTF-8, BOM'suz)
 *
 * Kullanim:
 *   node .ai/scripts/vault-utf8-writer.mjs append   --file <path> --text <string|@file>
 *   node .ai/scripts/vault-utf8-writer.mjs insert-before-marker --file <path> --marker <string> --text <string|@file>
 *   node .ai/scripts/vault-utf8-writer.mjs write    --file <path> --text <string|@file>
 *   node .ai/scripts/vault-utf8-writer.mjs copy     --src <path> --dest <path>
 *   node .ai/scripts/vault-utf8-writer.mjs verify   --file <path>
 *   node .ai/scripts/vault-utf8-writer.mjs repair   --file <path>
 *   node .ai/scripts/vault-utf8-writer.mjs scan     [--dir <path>]
 *
 * Kurallar:
 *  - Her yazma UTF-8 (BOM yok), LF/CRLF korunur.
 *  - append: bayt-seviyesi mevcut icerige dokunmadan sona ekler (log.md icin tek izinli mod).
 *  - write/insert-before-marker: oncesinde <file>.bak yedegi alir (ilk yedek).
 *  - verify: BOM, UTF-8 gecerliligi, CJK/mojibake artefaktlarini raporlar.
 *  - repair: UTF-8 bozuklugunu yedek alip temiz UTF-8 ile yazar.
 *  - scan: verilen dizindeki .md dosyalarinda mojibake desenlerini arar.
 */
import fs from "node:fs";
import path from "node:path";

function parseArgs(argv) {
  const args = { _: [] };
  for (let i = 0; i < argv.length; i++) {
    const a = argv[i];
    if (a.startsWith("--")) {
      const key = a.slice(2);
      const next = argv[i + 1];
      if (next === undefined || next.startsWith("--")) args[key] = true;
      else { args[key] = next; i++; }
    } else args._.push(a);
  }
  return args;
}

function resolveText(val) {
  if (val === undefined || val === null) return "";
  if (typeof val === "string" && val.startsWith("@")) {
    return fs.readFileSync(val.slice(1), "utf8");
  }
  return String(val);
}

function readUtf8(file) {
  const buf = fs.readFileSync(file);
  const hasBom = buf.length >= 3 && buf[0] === 0xef && buf[1] === 0xbb && buf[2] === 0xbf;
  const body = hasBom ? buf.subarray(3) : buf;
  const text = new TextDecoder("utf-8", { fatal: false }).decode(body);
  return { buf, hasBom, text, body };
}

function backup(file) {
  const dir = "C:/temp/opencode/vault-backups";
  fs.mkdirSync(dir, { recursive: true });
  const bak = path.join(dir, path.basename(file) + ".bak");
  if (!fs.existsSync(bak)) fs.copyFileSync(file, bak);
  return bak;
}

function writeUtf8(file, text) {
  // EOL degistirilmez: verilen metin birebir UTF-8 (BOM'suz) yazilir.
  backup(file);
  const clean = text.replace(/^\uFEFF/, "");
  fs.writeFileSync(file, Buffer.from(clean, "utf8"), { encoding: "utf8" });
}

const MOJIBAKE_RE = /Ã[\x80-\xbf]|â€|Â[\x80-\xbf]|â„¢|ÅŸ|ÅŸ|Ã§|Ã¼|Ã¶|Ã¡|Ã©|ï¿½|�/g;
const CJK_RE = /[\u3040-\u30ff\u3400-\u4dbf\u4e00-\u9fff\uf900-\ufaff\uac00-\ud7af]/g;

function verifyFile(file) {
  const { hasBom, text } = readUtf8(file);
  const moji = text.match(MOJIBAKE_RE) || [];
  const cjk = text.match(CJK_RE) || [];
  const hasNul = text.includes("\u0000");
  return { file, hasBom, mojibake: moji.length, mojiSamples: [...new Set(moji)].slice(0, 8), cjk: cjk.length, cjkSamples: [...new Set(cjk)].slice(0, 12), hasNul, lines: text.split(/\r?\n/).length };
}

const mode = process.argv[2];
const args = parseArgs(process.argv.slice(3));

try {
  if (mode === "append") {
    const file = path.resolve(args.file);
    const text = resolveText(args.text);
    const cur = fs.readFileSync(file);
    fs.appendFileSync(file, Buffer.from(text, "utf8"), { encoding: "utf8" });
    console.log(JSON.stringify({ ok: true, mode, file, appendedBytes: Buffer.byteLength(text, "utf8"), totalBytes: cur.length + Buffer.byteLength(text, "utf8") }, null, 2));
  } else if (mode === "insert-before-marker") {
    const file = path.resolve(args.file);
    const marker = args.marker;
    const text = resolveText(args.text);
    const { text: cur } = readUtf8(file);
    const idx = cur.indexOf(marker);
    if (idx < 0) { console.error(JSON.stringify({ ok: false, error: "marker_not_found", marker })); process.exit(2); }
    const next = cur.slice(0, idx) + text + cur.slice(idx);
    writeUtf8(file, next);
    console.log(JSON.stringify({ ok: true, mode, file, marker, insertedBytes: Buffer.byteLength(text, "utf8") }, null, 2));
  } else if (mode === "write") {
    const file = path.resolve(args.file);
    const text = resolveText(args.text);
    writeUtf8(file, text);
    console.log(JSON.stringify({ ok: true, mode, file, bytes: Buffer.byteLength(text, "utf8") }, null, 2));
  } else if (mode === "copy") {
    const src = path.resolve(args.src);
    const dest = path.resolve(args.dest);
    backup(dest);
    fs.copyFileSync(src, dest);
    console.log(JSON.stringify({ ok: true, mode, src, dest }, null, 2));
  } else if (mode === "verify") {
    const file = path.resolve(args.file);
    console.log(JSON.stringify(verifyFile(file), null, 2));
  } else if (mode === "repair") {
    const file = path.resolve(args.file);
    const { buf, hasBom, text } = readUtf8(file);
    backup(file);
    const clean = text.replace(/�/g, "");
    fs.writeFileSync(file, Buffer.from(clean, "utf8"), { encoding: "utf8" });
    console.log(JSON.stringify({ ok: true, mode, file, removedBom: hasBom, bytesBefore: buf.length, bytesAfter: Buffer.byteLength(clean, "utf8") }, null, 2));
  } else if (mode === "scan") {
    const dir = path.resolve(args.dir || ".ai");
    const out = [];
    const walk = (d) => {
      for (const e of fs.readdirSync(d, { withFileTypes: true })) {
        const p = path.join(d, e.name);
        if (e.isDirectory()) walk(p);
        else if (e.name.endsWith(".md")) {
          const v = verifyFile(p);
          if (v.mojibake > 0 || v.hasBom || v.hasNul) out.push(v);
        }
      }
    };
    walk(dir);
    console.log(JSON.stringify({ ok: true, mode, dir, dirty: out.length, files: out }, null, 2));
  } else {
    console.error("Bilinmeyen mod: " + mode);
    process.exit(1);
  }
} catch (err) {
  console.error(JSON.stringify({ ok: false, error: String(err && err.message || err) }));
  process.exit(1);
}
