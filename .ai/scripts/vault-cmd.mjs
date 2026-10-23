#!/usr/bin/env node
// vault-cmd.mjs — Turkce komut arayuzu: vault-utf8-writer.mjs sarmalayicisi (tek yazma kanali korunur).
// Karar: 2026-10-23 (PLAN Adim 12 — kullanici onayli uretim).
//
// Komut eslemesi (Turkce -> writer modu):
//   ekle        -> append                 node vault-cmd.mjs ekle --file <yol> --text <metin|@dosya>
//   yaz         -> write                  node vault-cmd.mjs yaz  --file <yol> --text <metin|@dosya>
//   araya-ekle  -> insert-before-marker   node vault-cmd.mjs araya-ekle --file <yol> --marker <deger> --text <metin|@dosya>
//   kopyala     -> copy                   node vault-cmd.mjs kopyala --src <yol> --dest <yol>
//   dogrula     -> verify                 node vault-cmd.mjs dogrula --file <yol>
//   onar        -> repair                 node vault-cmd.mjs onar   --file <yol>
//   tara        -> scan                   node vault-cmd.mjs tara   [--dir <dizin>]
//
// Ek koruma (writer'dan once): yazma hedefi repo koku altinda olmak ZORUNLUDUR (dizin disi yazma engeli).
import { spawnSync } from "node:child_process";
import path from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(__dirname, "..", "..");
const WRITER = path.join(ROOT, ".ai", "scripts", "vault-utf8-writer.mjs");

const MODES = {
  ekle: "append",
  yaz: "write",
  "araya-ekle": "insert-before-marker",
  kopyala: "copy",
  dogrula: "verify",
  onar: "repair",
  tara: "scan",
};
const WRITE_CMDS = new Set(["ekle", "yaz", "araya-ekle", "kopyala", "onar"]);

function usage() {
  console.log(
    [
      "Kullanim: node .ai/scripts/vault-cmd.mjs <komut> [writer argumanlari]",
      "",
      "Komutlar:",
      "  ekle        --file <yol> --text <metin|@dosya>        Sona ekler (log.md icin tek izinli mod)",
      "  yaz         --file <yol> --text <metin|@dosya>        Dosyayi tamamen yeniden yazar",
      "  araya-ekle  --file <yol> --marker <d> --text <t|@d>   Marker onune insert eder",
      "  kopyala     --src <yol> --dest <yol>                  Kopyalar",
      "  dogrula     --file <yol>                              BOM/UTF-8/mojibake raporu",
      "  onar        --file <yol>                              UTF-8 onarimi (yedek alir)",
      "  tara        [--dir <dizin>]                           .md dosyalarinda mojibake taramasi",
      "",
      "Koruma: yazma hedefi repo koku altinda olmalidir (ROOT disina yazma engelli).",
    ].join("\n")
  );
}

function parseArgv(argv) {
  const passthrough = [];
  for (let i = 0; i < argv.length; i++) {
    const a = argv[i];
    passthrough.push(a);
    if (a.startsWith("--") && argv[i + 1] !== undefined && !argv[i + 1].startsWith("--")) passthrough.push(argv[++i]);
  }
  return passthrough;
}

function main() {
  const [cmd, ...rest] = process.argv.slice(2);
  if (!cmd || cmd === "--help" || cmd === "-h" || !MODES[cmd]) { usage(); process.exit(cmd ? 0 : 2); }

  // Yazma komutlarinda hedefler ROOT altinda mi? (kopyala: src+dest; digerleri: --file)
  if (WRITE_CMDS.has(cmd)) {
    const checks = [];
    const fi = rest.indexOf("--file");
    const di = rest.indexOf("--dest");
    if (fi >= 0 && rest[fi + 1]) checks.push(rest[fi + 1]);
    if (di >= 0 && rest[di + 1]) checks.push(rest[di + 1]);
    for (const target of checks) {
      const abs = path.resolve(ROOT, target);
      if (abs !== ROOT && !abs.startsWith(ROOT + path.sep)) {
        console.error(`ENGELLENDI: hedef repo koku disinda: ${target}`);
        process.exit(2);
      }
    }
  }

  const res = spawnSync(process.execPath, [WRITER, MODES[cmd], ...parseArgv(rest)], {
    cwd: ROOT, encoding: "utf8", shell: false,
  });
  if (res.stdout) process.stdout.write(res.stdout);
  if (res.stderr) process.stderr.write(res.stderr);
  process.exit(res.status === null ? 1 : res.status);
}

main();
