#!/usr/bin/env node
// vault-utf8-writer.mjs — CoreMusic vault UTF-8 güvenli yazma aracı
//
// Kök neden: PowerShell 5.1 Add-Content/Out-File/echo> cmdlet'leri encoding
// belirtilmediğinde Windows-1254 yazar; -Encoding UTF8 ise BOM ekler.
// Kural: Tüm vault dosya yazımları bu script ile yapılır (Node.js fs, BOM'suz UTF-8).
//
// Kullanım:
//   node vault-utf8-writer.mjs append --file <hedef> --entry-file <girdi-dosyasi>
//   node vault-utf8-writer.mjs insert-before-marker --file <hedef> --marker "<metin>" --entry-file <girdi>
//   node vault-utf8-writer.mjs write --file <hedef> --content-file <icerik>
//   node vault-utf8-writer.mjs copy --from <kaynak> --file <hedef>
//   node vault-utf8-writer.mjs verify --file <hedef>
//   node vault-utf8-writer.mjs repair --file <hedef>
//   node vault-utf8-writer.mjs scan --dir <klasor> [--idle-min 10]
//   node vault-utf8-writer.mjs strip-bom --dir <klasor> --backup-dir <yedek> [--dry-run] [--idle-min 10]
//
// Not: append modu bayt seviyesinde çalışır; mevcut içerik decode edilmez,
// karışık/bozuk encoding'li dosyalarda (ör. log.md) veri kaybına neden olmaz.

import fs from 'node:fs';
import path from 'node:path';

function die(msg) {
  console.error('HATA: ' + msg);
  process.exit(1);
}

const [, , mode, ...rest] = process.argv;

function argOf(name) {
  const i = rest.indexOf(name);
  return i >= 0 ? rest[i + 1] : undefined;
}

function requireTarget() {
  const file = argOf('--file');
  if (!file) die('--file zorunlu');
  return path.resolve(file);
}

function hasBom(buf) {
  return buf.length >= 3 && buf[0] === 0xef && buf[1] === 0xbb && buf[2] === 0xbf;
}

function isValidUtf8(buf) {
  try {
    new TextDecoder('utf-8', { fatal: true }).decode(buf);
    return true;
  } catch {
    return false;
  }
}

function readUtf8NoBom(p) {
  return fs.readFileSync(path.resolve(p), 'utf8').replace(/^\uFEFF/, '');
}

function writeUtf8NoBom(p, text) {
  fs.writeFileSync(p, text, { encoding: 'utf8' });
  const back = fs.readFileSync(p);
  if (hasBom(back)) die('BOM sızdı: ' + p);
  if (!isValidUtf8(back)) die('Yazım sonrası geçersiz UTF-8: ' + p);
  return back.length;
}

switch (mode) {
  case 'append': {
    const target = requireTarget();
    const entryFile = argOf('--entry-file');
    if (!entryFile) die('--entry-file zorunlu');
    let entry = fs.readFileSync(path.resolve(entryFile));
    if (entry.length >= 3 && entry[0] === 0xef && entry[1] === 0xbb && entry[2] === 0xbf) {
      entry = entry.subarray(3);
      console.log('Not: giris dosyasindaki BOM temizlendi.');
    }
    const fd = fs.openSync(target, 'a');
    fs.writeSync(fd, entry);
    fs.closeSync(fd);
    const size = fs.statSync(target).size;
    console.log(`OK append ${target} +${entry.length} bayt → toplam ${size} (bayt-seviyesi, mevcut içerik korunur)`);
    break;
  }

  case 'insert-before-marker': {
    const target = requireTarget();
    const marker = argOf('--marker');
    const entryFile = argOf('--entry-file');
    if (!marker || !entryFile) die('--marker ve --entry-file zorunlu');
    const buf = fs.readFileSync(target);
    if (!isValidUtf8(buf)) {
      die('Hedef dosya geçersiz UTF-8 içeriyor; insert-before-marker kullanılamaz. Önce dosyayı onarın (verify modu ile kontrol edin).');
    }
    const text = buf.toString('utf8');
    const idx = text.indexOf(marker);
    if (idx < 0) die('marker bulunamadı: ' + marker);
    const entry = readUtf8NoBom(entryFile);
    const out = text.slice(0, idx) + entry + text.slice(idx);
    const size = writeUtf8NoBom(target, out);
    console.log(`OK insert-before-marker ${target} → ${size} bayt`);
    break;
  }

  case 'write': {
    const target = requireTarget();
    const src = argOf('--content-file');
    if (!src) die('--content-file zorunlu');
    const content = readUtf8NoBom(src);
    const size = writeUtf8NoBom(target, content);
    console.log(`OK write ${target} → ${size} bayt (UTF-8, BOM'suz)`);
    break;
  }

  case 'copy': {
    const src = argOf('--from');
    if (!src) die('--from zorunlu');
    const target = requireTarget();
    fs.copyFileSync(path.resolve(src), target);
    console.log(`OK copy ${src} → ${target}`);
    break;
  }

  case 'scan': {
    const dir = argOf('--dir');
    if (!dir) die('--dir zorunlu');
    const root = path.resolve(dir);
    const idleMin = Number(argOf('--idle-min') || 0);
    const exclArg = argOf('--exclude');
    const exclude = new Set((exclArg ? exclArg.split(',') : []).map((s) => s.trim()).filter(Boolean));
    const exts = new Set(['.md', '.mjs', '.json', '.ps1', '.css', '.sql', '.txt', '.yml', '.yaml']);
    const bad = [];
    let total = 0;
    let bomCount = 0;
    let skippedActive = 0;
    const now = Date.now();
    (function walk(d) {
      for (const e of fs.readdirSync(d, { withFileTypes: true })) {
        const p = path.join(d, e.name);
        if (e.isDirectory()) { if (!exclude.has(e.name)) walk(p); continue; }
        if (!exts.has(path.extname(e.name).toLowerCase())) continue;
        total++;
        const buf = fs.readFileSync(p);
        const bom = hasBom(buf);
        if (bom) bomCount++;
        if (!isValidUtf8(buf)) {
          const ageMin = (now - fs.statSync(p).mtimeMs) / 60000;
          if (idleMin > 0 && ageMin < idleMin) { skippedActive++; continue; }
          bad.push({ rel: path.relative(root, p), size: buf.length, bom, ageMin: Math.round(ageMin) });
        }
      }
    })(root);
    console.log(`Tarama: ${root}${exclude.size ? ` | Haric: ${[...exclude].join(', ')}` : ''}`);
    console.log(`Incelenen: ${total} dosya | BOM'lu: ${bomCount} | Gecersiz UTF-8: ${bad.length}${idleMin ? ` | aktif (son ${idleMin} dk) atlandi: ${skippedActive}` : ''}`);
    bad.forEach(f => console.log(`  GECERSIZ: ${f.rel} (${f.size} bayt, BOM: ${f.bom ? 'var' : 'yok'}, son yazim: ${f.ageMin} dk once)`));
    process.exit(bad.length ? 2 : 0);
    break;
  }

  case 'strip-bom': {
    const dirArg = argOf('--dir');
    const fileArg = argOf('--file');
    const dry = rest.includes('--dry-run');
    const idleMin = Number(argOf('--idle-min') || 0);
    const backupDir = argOf('--backup-dir');
    if (!dirArg && !fileArg) die('--dir veya --file zorunlu');
    if (!dry && !backupDir) die('Gercek calistirmada --backup-dir zorunlu (geri alinabilirlik icin).');
    const exts = new Set(['.md', '.mjs', '.json', '.ps1', '.css', '.sql', '.txt', '.yml', '.yaml']);
    const exclArg = argOf('--exclude');
    const exclude = new Set((exclArg ? exclArg.split(',') : []).map((s) => s.trim()).filter(Boolean));
    const now = Date.now();
    const kok = fileArg ? path.dirname(path.resolve(fileArg)) : path.resolve(dirArg);
    const hedefler = fileArg ? [path.resolve(fileArg)] : [];
    if (!fileArg) {
      (function walk(d) {
        for (const e of fs.readdirSync(d, { withFileTypes: true })) {
          const p = path.join(d, e.name);
          if (e.isDirectory()) { if (!exclude.has(e.name)) walk(p); continue; }
          if (!exts.has(path.extname(e.name).toLowerCase())) continue;
          hedefler.push(p);
        }
      })(kok);
    }
    let stripped = 0;
    let skipped = 0;
    for (const p of hedefler) {
      const buf = fs.readFileSync(p);
      if (!hasBom(buf)) continue;
      const ageMin = (now - fs.statSync(p).mtimeMs) / 60000;
      if (idleMin > 0 && ageMin < idleMin) { skipped++; continue; }
      if (dry) { console.log(`[DRY] BOM kaldirilacak: ${path.relative(kok, p)}`); stripped++; continue; }
      const dest = path.join(path.resolve(backupDir), path.relative(kok, p));
      fs.mkdirSync(path.dirname(dest), { recursive: true });
      fs.copyFileSync(p, dest);
      fs.writeFileSync(p, buf.subarray(3));
      stripped++;
    }
    console.log(`Ozet: ${stripped} dosya${dry ? ' (DRY-RUN, yazilmadi)' : ' temizlendi'}${skipped ? `, ${skipped} aktif dosya atlandi (son ${idleMin} dk)` : ''}. Yedek: ${backupDir || '(dry-run)'}`);
    break;
  }

  case 'verify': {
    const target = requireTarget();
    const buf = fs.readFileSync(target);
    const valid = isValidUtf8(buf);
    console.log(`${target}: ${buf.length} bayt | BOM: ${hasBom(buf) ? 'VAR (kaldirilmali)' : 'yok'} | UTF-8: ${valid ? 'GECERLI' : 'GECERSIZ'}`);
    process.exit(valid ? 0 : 2);
    break;
  }

  case 'repair': {
    const target = requireTarget();
    const buf = fs.readFileSync(target);
    if (isValidUtf8(buf)) {
      console.log(`${target}: zaten gecerli UTF-8, onarim gerekmiyor.`);
      break;
    }
    // UTF-8 durum makinesi ile gecersiz bayt konumlarini bul
    const invalidPos = [];
    let i = 0;
    while (i < buf.length) {
      const c = buf[i];
      if (c < 0x80) { i++; continue; }
      let len = 0;
      if (c >= 0xc2 && c <= 0xdf) len = 1;
      else if (c >= 0xe0 && c <= 0xef) len = 2;
      else if (c >= 0xf0 && c <= 0xf4) len = 3;
      let ok = len > 0;
      for (let j = 1; ok && j <= len; j++) {
        if (i + j >= buf.length || buf[i + j] < 0x80 || buf[i + j] > 0xbf) ok = false;
      }
      if (ok) { i += len + 1; } else { invalidPos.push(i); i++; }
    }
    if (invalidPos.length === 0) die('Tarayici gecersiz bayt bulamadi ama fatal decoder hata verdi — elle inceleme gerekli.');
    // ardisik konumlari bozuk koşulara grupla
    const runs = [];
    let start = invalidPos[0];
    let prev = invalidPos[0];
    for (let k = 1; k < invalidPos.length; k++) {
      if (invalidPos[k] === prev + 1) { prev = invalidPos[k]; }
      else { runs.push([start, prev]); start = invalidPos[k]; prev = invalidPos[k]; }
    }
    runs.push([start, prev]);
    const cp1254 = new TextDecoder('windows-1254');
    const chunks = [];
    let cursor = 0;
    for (const [s, e] of runs) {
      if (s > cursor) chunks.push(buf.subarray(cursor, s));
      chunks.push(Buffer.from(cp1254.decode(buf.subarray(s, e + 1)), 'utf8'));
      cursor = e + 1;
    }
    if (cursor < buf.length) chunks.push(buf.subarray(cursor));
    // yedek
    const backup = target + '.bak-repair';
    fs.copyFileSync(target, backup);
    const out = Buffer.concat(chunks);
    fs.writeFileSync(target, out);
    const back = fs.readFileSync(target);
    const stillInvalid = !isValidUtf8(back);
    console.log(`OK repair ${target}: ${invalidPos.length} gecersiz bayt (${runs.length} kos) CP1254 -> UTF-8 cevrildi.`);
    console.log(`Yedek: ${backup} | Yeni boyut: ${back.length} bayt | Sonuc: ${stillInvalid ? 'HALA GECERSIZ — elle incele' : 'UTF-8 GECERLI'}`);
    process.exit(stillInvalid ? 2 : 0);
    break;
  }

  default:
    die('Bilinmeyen mod: ' + mode + ' | modlar: append | insert-before-marker | write | copy | verify');
}
